<?php
include_once('./_common.php');
auth_check($auth[$sub_menu], "r");
$mb_id = $member[mb_id];

/*
 * JSON DECODE 관련 이슈
 * 1. 넘겨받은 JSON텍스트 ICONV로 변환필요 
 * 2. 상품명에 " OR ' 가 포함 되있는경우 디코딩 실패 str_replace로 변환필요
 * 3. STRIPSLASH 안하면 디코딩이 안됨
 * */
$arr = jsonDecode($_POST['data']);

/* 단일레코드일때 */
if( strlen($arr[number]) > 3 ) {
	$vo = $arr;
}
else {

	/* 주문정보 폼 기록 */
	if($mode == 'form') {
		$vo = $_POST;
	}
}

/*주문상태 업데이트 및 결제확인 SMS전송 */
function updateOrderStats($vo) {
	global $config, $v_sms, $member;
	
	$order = explode(',',$vo[admin_link]);
	for($i=0; $i < count($order); $i++) {
		$주문번호 = trim($order[$i]);

		//문자 셋팅
		switch($vo[bank_type]) {
			case 'B01':	//상품주문
				$조회유형 = "입금이 확인되었습니다";
				$stats = 20;
				$AND_SQL = "	AND			stats IN ('00','10')	";
				break;
			case 'B07':	//환불
				$조회유형 = "환불이 처리되었습니다";
				$stats = 90;
				$AND_SQL = "	AND			number IN ()	";	//입출금내역 우측 주문관리에서 수동으로 선택 환불처리 UI가 있음 여기서 구현안함
				break;
			default:
				break;
		}

		
		
		/* 주문정보 */
		$co_sql = "	SELECT	CI.*,
												IFNULL(SW.CNT,0) AS CNT
								FROM		clay_order_info CI
												LEFT JOIN (	SELECT	od_id,
																						COUNT(*) AS CNT
																		FROM		sms5_write
																		WHERE		wr_message LIKE '%$조회유형%'
																		GROUP BY od_id
												) SW ON (SW.od_id = CI.od_id)
								WHERE		CI.od_id = '$주문번호'
		";
		
		$co_result = sql_query($co_sql);
		$co = mysql_fetch_array($co_result);
		
		if($i == (count($order)-1)) {
			$admin_memo = "$co[clay_id] $co[name]";
		}



		/* 상품주문 유형이면서 입금확인 체크, 결제완료건에 대해서만 SMS 전송 */
		/* SMS문자보낸 이력이 있거나, 상품주문건 아니면 패스 */
		if ($co[CNT] > 0 || $vo[bank_type] != 'B01') continue;

		$upd_sql = "UPDATE	clay_order_info	SET
													receipt_link = '$vo[number]'
								WHERE		od_id = '$주문번호'
		";
		sql_query($upd_sql);


		$upd_sql = "UPDATE	clay_order	SET
													stats = '$stats'
								WHERE		od_id = '$주문번호'
								$AND_SQL
											
		";
		sql_query($upd_sql);
		db_log($upd_sql,'clay_order','입출금내역 변경');
		
		
		if( ($이전OD_ID != $주문번호) && $vo[bank_type] == 'B01' ) {
			$receive_number = get_hp($co['hphone'], 0);
//			$send_number = preg_replace('/-/','',$member['mb_hp']);	//발신자번호
			$send_number = '0220886657';

			
			$mh_send_message = preg_replace("/{주문ID}/", $co['od_id'], $v_sms[$stats]);
			
			$SMS = new SMS;
			$SMS->SMS_con($config['cf_icode_server_ip'], $config['cf_icode_id'], $config['cf_icode_pw'], $config['cf_icode_server_port']);
			$SMS->Add($receive_number, $send_number, $config['cf_icode_id'], iconv("utf-8", "euc-kr", stripslashes($mh_send_message)), "");
			$SMS->Send();
			$SMS->Init(); // 보관하고 있던 결과값을 지웁니다.
			
			/* 로그기록 */
			$ins_sql = "INSERT	INTO 	sms5_write		SET
																wr_renum		= '$wr_renum',
																od_id				=	'$주문번호',			/* 관련 주문번호 */
																wr_reply		= '$send_number',				/*보내는사람번호*/
																wr_target		= '$co[hphone]',			/*받는사람번호*/
																wr_message	= '$mh_send_message',	/*메시지내용*/
																wr_memo			= '$wr_memo',
																wr_datetime	= now(),					/*보낸날짜*/
																wr_booking	= '$wr_booking',	/*예약날짜*/
																wr_total		= '1',
																wr_re_total	= '$wr_re_total',
																wr_success	= '1',
																wr_failure	= '$wr_failure'																
			";
			sql_query($ins_sql);
		}// IF(이전OD_ID != 주문번호) END
		
		$이전OD_ID = $주문번호;
	}//for end
	
	return $admin_memo;
}




//2개 이상일경우
if( !strlen($arr[number]) && count($arr) > 1) {

	for($i = 0; $i < count($arr); $i++) {
		$vo = $arr[$i];
		$관리자메모 = updateOrderStats($vo);
		$관리자메모 = ($vo[admin_memo]) ? $vo[admin_memo] : $관리자메모;
		
		$common_sql = "	UPDATE	bank_db		SET
															trader_name = '$vo[trader_name]',	/*메모*/
															bank_type		= '$vo[bank_type]',		/*입/출금 유형*/
															tax_type		= '$vo[tax_type]',		/*세금관련 처리유형*/
															tax_no 			= '$vo[tax_no]',			/*세금관련 처리번호*/
															tax_refno 	= '$vo[tax_refno]',		/*세금관련 처리번호*/
															cash_memo		= '$vo[cash_memo]',		/*현금영수증 관련*/
															admin_link	= '$vo[admin_link]',	/*연결된 주문번호들*/
															admin_memo	= '$관리자메모'				/*관리자 메모*/
										WHERE		number		= '$vo[number]'
		";
		sql_query($common_sql);
		db_log($common_sql,'bank_db','입출금내역수정');
	}

} else {	//단일 레코드일 경우
	$관리자메모 = updateOrderStats($vo);
	$관리자메모 = ($vo[admin_memo]) ? $vo[admin_memo] : $관리자메모;
	
	$common_sql = "	UPDATE	bank_db		SET
														trader_name = '$vo[trader_name]',		/*메모*/
															bank_type		= '$vo[bank_type]',		/*입/출금 유형*/
															tax_type		= '$vo[tax_type]',		/*세금관련 처리유형*/
															tax_no 			= '$vo[tax_no]',			/*세금관련 처리번호*/
															tax_refno 	= '$vo[tax_refno]',		/*세금관련 처리번호*/
															cash_memo		= '$vo[cash_memo]',		/*현금영수증 관련*/
															admin_link	= '$vo[admin_link]',	/*연결된 주문번호들*/
															admin_memo	= '$관리자메모'				/*관리자 메모*/
									WHERE		number		= '$vo[number]'
	";
	sql_query($common_sql);

	db_log($common_sql,'bank_db','입출금내역수정');
}


if($result) {
	$json[success] = "true";
	$json[message] = '입출금정보가 수정되었습니다';
} else {
	$json[success] = "false";
	$json[message] = '입출금정보가 수정되지 않았습니다. 관리자에게 문의바랍니다.';
}

$json_data = json_encode_unicode($json);
echo $json_data;
?>