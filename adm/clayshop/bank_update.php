<?php
include_once('./_common.php');
auth_check($auth[$sub_menu], "r");

/* SMS */
global $config;


$sql = "	UPDATE	bank_db	SET
										bank_type = '$bank_type',				/* 입/출금 유형 */
										tax_type = '$tax_type',					/* 세금 처리유형 */
										tax_no = '$tax_no',							/* 세금 처리번호 */
										admin_link = '$admin_link',			/*연결된 주문번호들*/
										admin_memo = '$admin_memo'			/*관리자 메모*/
					WHERE		number = '$number'
";
$결과 = sql_query($sql);


$order = explode(',',$admin_link);
for($i=0; $i < count($order); $i++) {
	$주문번호 = trim($order[$i]);

	/* 주문정보 */
	$co_sql = "	SELECT	CI.*,
											IFNULL(SW.CNT,0) AS CNT
							FROM		clay_order_info CI
											LEFT JOIN (	SELECT	od_id,
																					COUNT(*) AS CNT
																	FROM		sms5_write
																	WHERE		wr_message LIKE '%입금이 확인되었습니다%'
																	GROUP BY od_id
											) SW ON (SW.od_id = CI.od_id)
							WHERE		CI.od_id = '$주문번호'
	";
	$co_result = sql_query($co_sql);
	$co = mysql_fetch_array($co_result);

	/* SMS문자보낸 이력이 있을경우 패스 */
	if($co[CNT] > 0) continue;


	$upd_sql = "UPDATE	clay_order_info	SET
												receipt_link = '$number'
							WHERE		od_id = '$주문번호'
	";
	sql_query($upd_sql);


	/* 입금내역 대비 주문내역 관련 연관데이터 확인 */
	$bank_sql = "	SELECT	*
								FROM		bank_db
								WHERE		number = '$number'
	";
	$row = sql_fetch($bank_sql);

	$거래일 = $row[tr_date];
	$거래시간 = $row[tr_time];
	$입금액 = number_format($row[input_price]);
	$출금액 = number_format($row[output_price]);
	$거래자명 = $row[trader_name];
	$연결주문번호 = $row[admin_link];
	$관리자메모 = $row[admin_memo];


	/* 입금내역 정보를 기준으로 연관된 주문내역 검색 */
	$검색어 = substr(trim($거래자명),0,9);


	$연관주문번호목록 = '';
	$tmp = explode(',',$row[admin_link]);

	for($i = 0; $i < count($tmp); $i++) {
		$연관주문번호목록 .= "'".$tmp[$i]."',";
	}
	$연관주문번호목록 = substr($연관주문번호목록,0,strlen($연관주문번호목록)-1);

	$find_sql = "	SELECT	IFNULL(CC.cnt,0) AS cancel,		/* 취소 */
												IFNULL(CR.cnt,0) AS request,	/* 주문신청, 입금요청 */
												IFNULL(CP.cnt,0) AS payok,		/* 결제완료 */
												CLS.*,
												CI.*
							  FROM		(	SELECT	CL.od_id,
																	COUNT(CL.od_id) AS order_cnt,
																	SUM(IT.it_price * CL.it_qty) AS total_price,
																	SUM(CL.it_org_price * CL.it_qty) AS total_orgprice
													FROM	clay_order CL
																LEFT JOIN clay_order_info CI ON (CI.od_id = CL.od_id)
																LEFT JOIN g5_shop_item IT ON (IT.it_id = CL.it_id)
													GROUP BY CL.od_id
												) AS CLS
												LEFT JOIN clay_order_info CI ON (CI.od_id = CLS.od_id)
												LEFT JOIN (	SELECT	od_id,
																						COUNT(*) as cnt
																		FROM		clay_order
																		WHERE		stats = 99
																		GROUP BY od_id
												) CC ON (CC.od_id = CLS.od_id)
												LEFT JOIN (	SELECT	od_id,
																						COUNT(*) as cnt
																		FROM		clay_order
																		WHERE		stats IN ('00','10')
																		GROUP BY od_id
												) CR ON (CR.od_id = CLS.od_id)
												LEFT JOIN (	SELECT	od_id,
																						COUNT(*) as cnt
																		FROM		clay_order
																		WHERE		stats >= 20
																		AND			stats <= 60
																		GROUP BY od_id
												) CP ON (CP.od_id = CLS.od_id)
								WHERE	1=1
								AND		((
												(CLS.total_orgprice+3500) = '$row[input_price]'
												OR	(CLS.total_orgprice+5000) = '$row[input_price]'
												OR	(CLS.total_orgprice) = '$row[input_price]'
											)
								AND		(
												CI.clay_id	LIKE	'%$검색어%'
												OR		CI.name		LIKE	'%$검색어%'
												OR		CI.receipt_name	LIKE	'%$검색어%'
								))
								OR		CI.od_id IN ($연관주문번호목록)
								ORDER	BY	CLS.od_id DESC
	";
	$find_result = sql_query($find_sql);
	$연관건수 = mysql_num_rows($find_result);

	/* 연관 건수가 1개 이상이여야하고 주문신청 상태인 건들만 결제완료로 변경 */
	if($연관건수 > 0) {
		$upd_sql = "UPDATE	clay_order	SET
													stats = 20
								WHERE		stats IN ('00','10')
								AND			od_id = '$주문번호'
		";
		sql_query($upd_sql);


		/* 상품주문 유형이면서 입금확인 체크 */
		if( ($이전OD_ID != $주문번호) && ($bank_type == 'B01' || $bank_type == 'B07') ) {
			

			$receive_number = get_hp($co['hphone'], 0);
			$send_number = preg_replace('/-/','',$member['mb_hp']);	//발신자번호

			//문자 셋팅
			switch($bank_type) {
				case 'B01':	//상품주문
					$stats = 20;
					break;
				case 'B07':	//환불
					$stats = 90;
					break;
				default:
					break;
			}
			
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
	}// IF($co[stats] == '00' OR $co[stats] == '10') END

	$이전OD_ID = $주문번호;
}

#송장번호 입력

echo 1;
?>