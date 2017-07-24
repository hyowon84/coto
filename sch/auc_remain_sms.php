<?php
include_once('./_common.php');


function process($data) {
	global $config;

	$log = sql_fetch("SELECT	*	FROM	sms5_write WHERE od_id = '$data[ac_code]' AND wr_target = '$data[mb_hp]' ");
	
	if($log[wr_target])
		return false;
	
	
	/* SMS */
	$mh_reply = '0220886657';
	$SMS = new SMS5;
	$SMS->SMS_con($config['cf_icode_server_ip'], $config['cf_icode_id'], $config['cf_icode_pw'], $config['cf_icode_server_port']);
	$mh_hp[]['bk_hp'] = get_hp($data[mb_hp],0);

	$경매명 = mb_substr($data[gp_name], 0, 18, 'UTF-8')."..";
	$mh_send_message = "{$경매명}의 경매마감시간이 30분 남았습니다 - 코인즈투데이";
	$result = $SMS->Add($mh_hp, $mh_reply, '', '', $mh_send_message, '', 1);

	$result = $SMS->Send();
	$SMS->Init(); // 보관하고 있던 결과값을 지웁니다.

	/* 로그기록 */
	$ins_sql = "INSERT	INTO 	sms5_write		SET
													wr_renum = '$wr_renum',
													od_id			=	'$data[ac_code]',			/* 관련 주문번호 */
													wr_reply = '$mh_reply',						/*보내는사람번호*/
													wr_target = '$data[mb_hp]',						/*받는사람번호*/
													wr_message = '$mh_send_message',	/*메시지내용*/
													wr_datetime = now(),							/*보낸날짜*/
													wr_booking = '$wr_booking',				/* 예약전송날짜*/
													wr_total = '1',
													wr_re_total = '$wr_re_total',
													wr_success = '1',
													wr_failure = '$wr_failure',
													wr_memo = '$wr_memo'
	";
	sql_query($ins_sql);

	echo "$경매명 - ".$data[mb_name]." ".$data[mb_hp]." 문자메시지 전송완료<br>";

}// 프로세스 end


/* 경매종료 마감 30분전 */
$ac_sql = "	SELECT	GP.gp_name,
										AC.*,
										MB.mb_name,
										MB.mb_hp
						FROM		g5_shop_group_purchase GP
										LEFT JOIN	auction_log AC ON (AC.ac_code = GP.ac_code AND AC.it_id = GP.gp_id )
										LEFT JOIN g5_member MB ON (MB.mb_id = AC.mb_id)
						WHERE		1=1
						AND			DATE_ADD(GP.ac_enddate,INTERVAL -30 MINUTE) <= NOW()
						AND			GP.ac_enddate >= NOW()
						AND			AC.bid_stats <= '05'
						GROUP BY AC.it_id, AC.mb_id
";
$result = sql_query($ac_sql);

while($arr = mysql_fetch_array($result)) {
	echo process($arr);
}

?>