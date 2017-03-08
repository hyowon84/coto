<?php
$sub_menu = '600200';

include_once('./_common.php');
include_once(G5_PATH.'/adm/shop_admin/admin.shop.lib.php');

global $v_delivery_type;

if($mode == 'update') {

	for($i = 0; $i < 30; $i++) {

		//4글자 이하면 진행안함
		if(strlen($_POST[od_id][$i]) < 3) continue;

		$od_id = $_POST[tag].$_POST[od_id][$i];

		$sel_sql = "	SELECT	*
									FROM		clay_order_info CI
									WHERE		CI.od_id = '$od_id'
									ORDER BY CI.od_id DESC
		";
		$co = mysql_fetch_array(sql_query($sel_sql));


		$delivery_invoice = $_POST[baesong_no][$i];
		$mb_id = $member[mb_id];

		$upd_sql = "	UPDATE	clay_order_info	SET
														delivery_invoice = '$delivery_invoice'
									WHERE		od_id = '$od_id'
		";
		sql_query($upd_sql);

		$upd_sql = "	UPDATE	clay_order	SET
														stats = '40'
									WHERE		od_id = '$od_id'
									AND			stats = '25'	/* 배송대기중 주문데이터만 */
		";
		sql_query($upd_sql);

		$ins_sql = "INSERT INTO 		log_table	SET
															logtype = 'clayorder',		/*로그유형 ( ex: clayorder )*/
															gr_id = '$od_id',				/*pk_id*/
															memo = '배송정보',			/*메모태그*/
															admin_id = '$mb_id',		/*로그를 남긴 관리자ID*/
															col = 'delivery_invoice',				/*변경 항목*/
															value = '$delivery_invoice',					/*변경된 값*/
															reg_date = now()				/*변경된 날짜*/
		";
		sql_query($ins_sql);


		/* SMS */
		$SMS = new SMS5;
		$SMS->SMS_con($config['cf_icode_server_ip'], $config['cf_icode_id'], $config['cf_icode_pw'], $config['cf_icode_server_port']);

		unset($mh_hp);
		$co['hphone'] = get_hp($co['hphone'], 0);

		$mh_hp[]['bk_hp'] = $co['hphone'];	//수신자번호
		$mh_reply = preg_replace('/-/','','0220886657');	//발신자번호
// 		$mh_reply = preg_replace('/-/','',$member['mb_hp']);	//발신자번호

		//문자 셋팅
		$mh_send_message = $v_sms['40'];
		$mh_send_message = preg_replace("/{주문ID}/", $od_id, $mh_send_message);
		$mh_send_message = preg_replace("/{운송장번호}/", $delivery_invoice, $mh_send_message);


		$result = $SMS->Add($mh_hp, $mh_reply, '', '', $mh_send_message, $booking, 1);
		$result = $SMS->Send();
		$SMS->Init(); // 보관하고 있던 결과값을 지웁니다.

		/* 로그기록 */
		$ins_sql = "INSERT	INTO 	sms5_write		SET
															wr_renum = '$wr_renum',
															od_id			=	'$od_id',			/* 관련 주문번호 */
															wr_reply = '$mh_reply',				/*보내는사람번호*/
															wr_target = '$co[hphone]',			/*받는사람번호*/
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
}

include_once (G5_ADMIN_PATH.'/admin.head.php');
?>

<form action='<?=$PHP_SELF?>' method='post'>
<input type='hidden' name='mode' value='update'>

<table align='center'>
	<tr>
		<td colspan='2' align='center'>
			배송완료정보 기록
			<input type='text' name='tag' value='CL<?=date('Y')?>' />
			<br>
			예제)	주문번호 | 등기번호 =>
			<input type='text' name='' value='02160028' readonly /> |
			<input type='text' name='' value='6025022007845' readonly />
		</td>
	</tr>

	<?
	for($i = 0; $i < 30; $i++) {
	?>
	<tr>
		<td class='deputy_th' height='20' width='130'>주문번호 | 등기번호</th>
		<td class='deputy_td' width='320'>
			<input type='text' name='od_id[<?=$i?>]' value='' /> |
			<input type='text' name='baesong_no[<?=$i?>]' value='' />
		</td>
	</tr>
	<?
	}
	?>
	<tr>
		<td colspan='2' align='center' style='padding:10px;'><input type='submit' value='수정'></td>
	</tr>
</table>
</form>

<?
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>