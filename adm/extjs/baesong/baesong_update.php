<?php
include_once('./_common.php');
auth_check($auth[$sub_menu], "r");


//스트립슬래시를 안하면 json_decode가 안됨
$grid = json_decode(str_replace('\"','"',stripslashes( iconv('utf-8', 'cp949', $_POST['grid'] ) )),true);
$mb_id = $member[mb_id];



/* 주문내역 상태값 변경 */
if($mode == 'statsUpdate') {

	for($i = 0; $i < count($grid); $i++) {
		$order = $grid[$i];
		$number_list .= "'$order[number]',";

		$log_sql = "INSERT INTO 		log_table	SET
																	logtype = 'clayorder',		/*로그유형 ( ex: clayorder )*/
																	gr_id = '$order[od_id]',							/*pk_id*/
																	pk_id = '$order[number]',
																	memo = '주문상태변경',			/*메모태그*/
																	admin_id = '$mb_id',		/*로그를 남긴 관리자ID*/
																	col = 'stats',				/*변경 항목*/
																	value = '$order[stats] -> $stats',					/*변경된 값*/
																	reg_date = now()				/*변경된 날짜*/
		";
		sql_query($log_sql);
	}
	$number_list = substr($number_list, 0, strlen($number_list)-1);

	$common_sql = "	UPDATE	clay_order	SET
														stats = '$stats'
									WHERE		number IN ($number_list)
	";
	$result = sql_query($common_sql);
	db_log($common_sql,'clay_order','통합배송관리 주문상태 변경');
		
	if($result) {
		$json[success] = "true";
		$json[message] = '주문내역들의 상태가 변경되었습니다';
	} else {
		$json[success] = "false";
		$json[message] = '주문내역들의 상태 변경이 실패하였습니다. 관리자에게 문의바랍니다.';
	}

}


/* 주문내역 배송정보 변경 */
else if($mode == 'refundMoneyUpdate') {

	/* 환불금액 업데이트 */
	for ($i = 0; $i < count($grid); $i++) {
		$order = $grid[$i];
		$odid_list .= "'$order[od_id]',";


		if($이전od_id != $order[od_id]) {

			$log_sql = "INSERT INTO 		log_table	SET
																	logtype = 'clayorder',		/*로그유형 ( ex: clayorder )*/
																	gr_id = '$order[od_id]',							/*pk_id*/
																	memo = '환불처리',			/*메모태그*/
																	admin_id = '$mb_id',		/*로그를 남긴 관리자ID*/
																	col = 'refund_money',				/*변경 항목*/
																	value = '$refund_money',					/*변경된 값*/
																	reg_date = now()				/*변경된 날짜*/
			";
			sql_query($log_sql);
		}

		$이전od_id = $order[od_id];
	}


	$odid_list = substr($odid_list, 0, strlen($odid_list) - 1);
	$common_sql1 = "	UPDATE	clay_order_info	SET
															refund_money = '$refund_money'
										WHERE		od_id IN ($odid_list)
	";
	$result = sql_query($common_sql1);
	db_log($common_sql1,'clay_order','통합배송관리 환불처리');

}

/* 주문내역 배송정보 변경 */
else if($mode == 'deliveryUpdate') {
	
	if($refund_money != '환불금액' && $refund_money > 0) {
		$환불변경값 = " ,refund_money = '$refund_money' ";
	}
	
	/* 배송정보 업데이트 */
	for($i = 0; $i < count($grid); $i++) {
		$order = $grid[$i];
		
		$odid_list .= "'$order[od_id]',";
		$number_list .= "'$order[number]',";

		$log_sql = "INSERT INTO 		log_table	SET
																	logtype = 'clayorder',					/*로그유형 ( ex: clayorder )*/
																	gr_id = '$order[od_id]',				/*pk_id*/
																	memo = '운송장번호입력&주문상태($order[stats_name] -> 배송완료)',			/*메모태그*/
																	admin_id = '$mb_id',						/*로그를 남긴 관리자ID*/
																	col = 'delivery_invoice',				/*변경 항목*/
																	value = '$delivery_invoice',		/*변경된 값*/
																	reg_date = now()								/*변경된 날짜*/
		";
		sql_query($log_sql);
	}

	$odid_list = substr($odid_list, 0, strlen($odid_list)-1);
	$number_list = substr($number_list, 0, strlen($number_list)-1);
	
	$common_sql1 = "	UPDATE	clay_order_info	SET
															delivery_invoice = '$delivery_invoice'
															$환불변경값
										WHERE		od_id IN ($odid_list)
	";
	$result = sql_query($common_sql1);
	db_log($common_sql1,'clay_order_info','통합배송관리 주문정보별 배송정보 입력');


	/*품목별로 배송정보 입력, 송장번호 입력된 주문건은 배송완료로 변경 */
	$common_sql2 = "	UPDATE	clay_order	SET
															delivery_invoice = '$delivery_invoice',
															delivery_date = NOW(),
															stats = '40'															
										WHERE		number IN ($number_list)
	";
	$result = sql_query($common_sql2);
	db_log($common_sql2,'clay_order','통합배송관리 품목별 배송정보 입력');
	

	/*배송완료 메시지 SMS전송*/
	$od_id = $grid[0][od_id];
	$od_sql = "	SELECT	*
							FROM		clay_order_info
							WHERE		od_id = '$od_id'
	";
	$co = mysql_fetch_array(sql_query($od_sql));

	$receive_number = get_hp($co['hphone'], 0);
//	$send_number = preg_replace('/-/','',$member['mb_hp']);	//발신자번호
	$send_number = '0220886657';
		
	$mh_send_message = $v_sms['400'];
	$mh_send_message = preg_replace("/{운송장번호}/", $delivery_invoice, $mh_send_message);

	$sms_sql = "	SELECT	COUNT(*) AS CNT
								FROM		sms5_write
								WHERE		wr_message = '$mh_send_message'
								AND			wr_target = '$receive_number'
	";
	$s = mysql_fetch_array(sql_query($sms_sql));
	if( !($s[CNT] > 0) ) {
		$SMS = new SMS;
		$SMS->SMS_con($config['cf_icode_server_ip'], $config['cf_icode_id'], $config['cf_icode_pw'], $config['cf_icode_server_port']);
		$SMS->Add($receive_number, $send_number, $config['cf_icode_id'], iconv("utf-8", "euc-kr", stripslashes($mh_send_message)), "");
		$SMS->Send();
		$SMS->Init(); // 보관하고 있던 결과값을 지웁니다.

		/* 로그기록 */
		$ins_sql = "INSERT	INTO 	sms5_write		SET
															wr_renum = '$wr_renum',
															od_id			=	'$od_id',							/* 관련 주문번호 */
															wr_reply = '$send_number',						/*보내는사람번호*/
															wr_target = '$receive_number',						/*받는사람번호*/
															wr_message = '$mh_send_message',	/*메시지내용*/
															wr_datetime = now(),							/*보낸날짜*/
															wr_booking = '$wr_booking',				/* 예약전송날짜*/
															wr_total = '1',
															wr_re_total = '$wr_re_total',
															wr_success = '1',
															wr_failure = '$wr_failure',
															wr_memo = '$wr_memo'
		";
		$result = sql_query($ins_sql);
	}
}


if($result) {
	$json[success] = "true";
	$json[message] = '주문내역들의 배송정보가 변경되었습니다';
} else {
	$json[success] = "false";
	$json[message] = '주문내역들의 배송정보 수정이 실패하였습니다. 관리자에게 문의바랍니다.';
}


$json_data = json_encode_unicode($json);
echo $json_data;
?>