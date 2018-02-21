<?php
include_once('./_common.php');
auth_check($auth[$sub_menu], "r");


//스트립슬래시를 안하면 json_decode가 안됨
$grid = json_decode(str_replace('\"','"',stripslashes( iconv('utf-8', 'cp949', $_POST['grid'] ) )),true);
$mb_id = $member[mb_id];


/* SMS */
$SMS = new SMS5;
$SMS->SMS_con($config['cf_icode_server_ip'], $config['cf_icode_id'], $config['cf_icode_pw'], $config['cf_icode_server_port']);


/* 주문내역 상태값 변경 */
for($i = 0; $i < count($grid); $i++) {
	$row = $grid[$i];
	$메시지 = $row[message];
	$받는사람 = $row[hphone];
	$주문금액 = number_format($row[TOTAL_PRICE]);

	$od_sql = "	SELECT	*
							FROM		clay_order_info CI
							WHERE		CI.od_id = '$row[od_id]'
	";
	$od = mysql_fetch_array(sql_query($od_sql));


	unset($mh_hp);
	$받는사람 = get_hp($받는사람, 0);

	$mh_hp[]['bk_hp'] = $받는사람;	//수신자번호
//	$send_number = preg_replace('/-/','',$member['mb_hp']);	//발신자번호
	$send_number = '0220886657';


	//문자 셋팅
	$mh_send_message = $메시지;
	$mh_send_message = preg_replace("/{주문ID}/", $od['od_id'], $mh_send_message);
	$mh_send_message = preg_replace("/{주문금액}/", $주문금액, $mh_send_message);
	$mh_send_message = preg_replace("/{회사명}/", '투데이(주)', $mh_send_message);
	$mh_send_message = preg_replace("/{운송장번호}/", $od['delivery_invoice'], $mh_send_message);


	$result = $SMS->Add($mh_hp, $send_number, '', '', $mh_send_message, $booking, 1);
	$result = $SMS->Send();
	$SMS->Init(); // 보관하고 있던 결과값을 지웁니다.


	/* 로그기록 */
	$ins_sql = "INSERT	INTO 	sms5_write		SET
																wr_renum = '$wr_renum',
																od_id			=	'$od[od_id]',					/* 관련 주문번호 */
																wr_reply = '$send_number',						/*보내는사람번호*/
																wr_target = '$od[hphone]',				/*받는사람번호*/
																wr_message = '$mh_send_message',	/*메시지내용*/
																wr_memo = '$wr_memo',
																wr_datetime = now(),							/*보낸날짜*/
																wr_booking = now(),								/*무슨날짜?*/
																wr_total = '1',
																wr_re_total = '$wr_re_total',
																wr_success = '1',
																wr_failure = '$wr_failure'
	";
	sql_query($ins_sql);

	if($od[od_id] && stripos($mh_send_message,'은행') > 0) {

		$upd_sql = "UPDATE	clay_order	SET
													stats = '10'
								WHERE		od_id = '$od[od_id]'
								AND			stats = '00'
		";
		sql_query($upd_sql);

	}


}


if($result) {
	$json[success] = "true";
	$json[message] = 'SMS전송 완료';
} else {
	$json[success] = "false";
	$json[message] = 'SMS전송 실패';
}


$json_data = json_encode_unicode($json);
echo $json_data;
?>