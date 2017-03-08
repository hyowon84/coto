<?php
include_once('./_common.php');
include_once(G5_PATH.'/adm/shop_admin/admin.shop.lib.php');

global $v_delivery_type;



$sel_sql = "	SELECT	DISTINCT
											CO.hphone
							FROM		clay_order CO
							WHERE		CO.it_name LIKE '%탈%'
							AND			CO.clay_id NOT LIKE '%은공주%'
							AND			CO.hphone NOT IN ('','010-9042-3438','010-4664-0454')
";
$result = sql_query($sel_sql);


while($row = mysql_fetch_array($result)) {

	$hphone = $row[hphone];

	unset($mh_hp);
	$row['hphone'] = get_hp($row['hphone'], 0);

	$mh_hp[]['bk_hp'] = $row['hphone'];	//수신자번호
	$mh_reply = preg_replace('/-/','','0220886657');	//발신자번호
	//$mh_reply = preg_replace('/-/','',$member['mb_hp']);	//발신자번호

	//문자 셋팅
	$mh_send_message = '한국의 탈 2차(봉산탈) 메달이 출시되었습니다. 예약구매는 http://goo.gl/yl1aPl 에서 신청가능합니다. -코인즈투데이-';

	/* SMS */
	$SMS = new SMS5;
	$SMS->SMS_con($config['cf_icode_server_ip'], $config['cf_icode_id'], $config['cf_icode_pw'], $config['cf_icode_server_port']);

	$sms_result = $SMS->Add($mh_hp, $mh_reply, '', '', $mh_send_message, $booking, 1);
	$sms_result = $SMS->Send();

	$SMS->Init(); // 보관하고 있던 결과값을 지웁니다.

	/* 로그기록 */
	$ins_sql = "INSERT	INTO 	sms5_write		SET
														wr_renum = '$wr_renum',
														od_id			=	'2차 탈메달 홍보',			/* 관련 주문번호 */
														wr_reply = '$mh_reply',				/*보내는사람번호*/
														wr_target = '$row[hphone]',			/*받는사람번호*/
														wr_message = '$mh_send_message',	/*메시지내용*/
														wr_datetime = now(),					/*보낸날짜*/
														wr_booking = '$wr_booking',	/*예약날짜*/
														wr_total = '1',
														wr_re_total = '$wr_re_total',
														wr_success = '1',
														wr_failure = '$wr_failure',
														wr_memo = '$wr_memo'
	";
	sql_query($ins_sql);

}

?>