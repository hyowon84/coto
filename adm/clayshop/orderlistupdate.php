<?php
include_once('./_common.php');
include_once(G5_PATH.'/adm/shop_admin/admin.shop.lib.php');

// $change_status  = $_POST['od_chg_status'];
$mb_id = $member[mb_id];
$컬럼_주문상태 = ($stats) ? ",stats = '$stats' " : '';

/**************************** 주문내역 개별항목 수정 ********************************/
foreach ($_POST['chk'] as $number) {
	$sel_sql = "	SELECT	CL.*,
												CI.*
								FROM		clay_order CL
												LEFT JOIN clay_order_info CI ON (CI.od_id = CL.od_id)
								WHERE		CL.number	=	$number
	";
	$co = mysql_fetch_array(sql_query($sel_sql));

	$it_id = $co[it_id];
	$it_qty = $_POST['it_qty'][$number];
	$주문번호 = $co['od_id'];


	/* 수량 */
	if($co[it_qty] != $it_qty) {
		$ins_sql = "INSERT INTO 		log_table	SET
															logtype = 'clayorder',		/*로그유형 ( ex: clayorder )*/
															gr_id	= '$co[od_id]',				/*pk_id*/
															pk_id	= '$number',			/*pk_id*/
															it_id		= '$co[it_id]',
															memo = '주문수량',			/*메모태그*/
															admin_id = '$mb_id',		/*로그를 남긴 관리자ID*/
															col = 'it_qty',				/*변경 항목*/
															value = '$it_qty',					/*변경된 값*/
															reg_date = now()				/*변경된 날짜*/
		";
		sql_query($ins_sql);
	}



## 결제완료로 변경시 현재재고에서 신청수량만큼 차감하면 안됨
## 코투재고는 누적주문수량, 재고관리프로그램 입고량의 합산으로 계산해야함.
// 	if($stats == '20') {
// 		$log_sql = "SELECT	COUNT(*) AS CNT
// 								FROM		log_table
// 								WHERE		gr_id = '$주문번호'
// 								AND			pk_id = '$number'
// 								AND			value LIKE '%-> 20'
// 		";
// 		$log = mysql_fetch_array(sql_query($log_sql));
// 		if($log[CNT] == 0) {
// 			$신청수량 = ($co[it_qty]) ? $co[it_qty] : '0';
// 			$upd_sql = "	UPDATE	g5_shop_group_purchase	SET
// 															jaego = (jaego - $신청수량)
// 										WHERE		gp_id = '$it_id'
// 			";
// 			sql_query($upd_sql);
// 		}
// 	}



	/* 상태값 */
	if($co[stats] != $stats && $stats != '') {
		$ins_sql = "INSERT INTO 		log_table	SET
														logtype = 'clayorder',		/*로그유형 ( ex: clayorder )*/
														gr_id	= '$co[od_id]',				/*pk_id*/
														pk_id	= '$number',			/*pk_id*/
														it_id		= '$co[it_id]',
														memo = '주문상태',			/*메모태그*/
														admin_id = '$mb_id',		/*로그를 남긴 관리자ID*/
														col = 'stats',				/*변경 항목*/
														value = '".$co[stats]." -> ".$stats."',					/*변경된 값*/
														reg_date = now()				/*변경된 날짜*/
		";
		sql_query($ins_sql);
	}


	/* 공구>주문내역>카트 데이터 일괄 상태변경 */

	$sql = "	UPDATE	clay_order	SET
										it_qty = '$it_qty'
										$컬럼_주문상태
						WHERE		number = '$number'
	";
	sql_query($sql);





	/* 결제완료, 상품준비중, 배송완료 일때만 SMS문자 전송됨,  입금요청 상태는 제외 */
	if( ($이전OD_ID != $co[od_id]) && ( $stats == '20' || $stats == '25' || $stats == '30' || $stats == '35' || $stats == '40') && !$not_send_sms)
	{

		/* 결제완료로 변경시 중복입금공지 방지 */
		if($stats == '20') {

			/* 주문정보 */
			$sms_sql = "	SELECT	CI.*,
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
			$smschk_result = sql_query($sms_sql);
			$smschk = mysql_fetch_array($smschk_result);

			/* SMS문자 보낸이력이 있을경우 패스 */
			if($smschk[CNT] > 0) continue;

		}

		/* SMS */
		$SMS = new SMS5;
		$SMS->SMS_con($config['cf_icode_server_ip'], $config['cf_icode_id'], $config['cf_icode_pw'], $config['cf_icode_server_port']);

		unset($mh_hp);
		$co['hphone'] = get_hp($co['hphone'], 0);

		$mh_hp[]['bk_hp'] = $co['hphone'];	//수신자번호
		$mh_reply = preg_replace('/-/','',$member['mb_hp']);	//발신자번호

		//문자 셋팅
		$mh_send_message = $v_sms[$stats];
		$mh_send_message = preg_replace("/{주문금액}/", number_format($주문금액), $mh_send_message);
		$mh_send_message = preg_replace("/{완판금액}/", number_format($완판금액), $mh_send_message);
		$mh_send_message = preg_replace("/{회사명}/", $default['de_admin_company_name'], $mh_send_message);
		$mh_send_message = preg_replace("/{주문ID}/", $co['od_id'], $mh_send_message);
		$mh_send_message = preg_replace("/{택배회사}/", $co['delivery_company'], $mh_send_message);
		$mh_send_message = preg_replace("/{운송장번호}/", $co['delivery_invoice'], $mh_send_message);


		$result = $SMS->Add($mh_hp, $mh_reply, '', '', $mh_send_message, $booking, 1);
		$result = $SMS->Send();
		$SMS->Init(); // 보관하고 있던 결과값을 지웁니다.

		/* 로그기록 */
		$ins_sql = "INSERT	INTO 	sms5_write		SET
															wr_renum = '$wr_renum',
															od_id			=	'$co[od_id]',			/* 관련 주문번호 */
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

	$이전OD_ID = $co[od_id];
}







/******************************** 주문내역의 그룹정보 수정 ***********************************/
$co = '';

foreach ($_POST['gr_chk'] as $od_id) {
	$sel_sql = "	SELECT	CL.*,
												CI.*
								FROM		clay_order_info CI
												LEFT JOIN clay_order CL ON (CL.od_id = CI.od_id)
								WHERE	CI.od_id	=	'$od_id'
	";
	$co = mysql_fetch_array(sql_query($sel_sql));

	$clay_id = $_POST['clay_id'][$od_id];
	$name = $_POST['name'][$od_id];
	$paytype = $_POST['paytype'][$od_id];
	$receipt_name = $_POST['receipt_name'][$od_id];
	$hphone = $_POST['hphone'][$od_id];
	$delivery_type = $_POST['delivery_type'][$od_id];
	$delivery_invoice = $_POST['delivery_invoice'][$od_id];
	$admin_memo = $_POST['admin_memo'][$od_id];
	$addr1 = $_POST['addr1'][$od_id];
	$addr1_2 = $_POST['addr1_2'][$od_id];
	$addr2 = $_POST['addr2'][$od_id];
	$zip = $_POST['zip'][$od_id];
	$cash_receipt_yn = $_POST['cash_receipt_yn'][$od_id];
	$cash_receipt_type = $_POST['cash_receipt_type'][$od_id];
	$cash_receipt_info = $_POST['cash_receipt_info'][$od_id];

	switch($cash_receipt_type) {
		case 'C01':
			$cash_receipt_type_nm = '개인소득공제';
			break;
		case 'C02':
			$cash_receipt_type_nm = '사업자지출증빙';
			break;
		default:
			$cash_receipt_type_nm = '';
			break;
	}

	/* 배송정보입력 */
	if($co[delivery_invoice] != $delivery_invoice) {
		$ins_sql = "INSERT INTO 		log_table	SET
															logtype = 'clayorder',		/*로그유형 ( ex: clayorder )*/
															gr_id = '$co[od_id]',				/*pk_id*/
															memo = '배송정보',			/*메모태그*/
															admin_id = '$mb_id',		/*로그를 남긴 관리자ID*/
															col = 'delivery_invoice',				/*변경 항목*/
															value = '$delivery_invoice',					/*변경된 값*/
															reg_date = now()				/*변경된 날짜*/
		";
		sql_query($ins_sql);

		//배송준비중(25)일때 운송장번호 입력시 배송완료(40)로 변경후 sms전송
		if( $co[stats] == '25' ) {
			/* 상태값 배송완료로 변경 */
			$ins_sql = "INSERT INTO 		log_table	SET
																logtype = 'clayorder',		/*로그유형 ( ex: clayorder )*/
																gr_id	= '$co[od_id]',				/*pk_id*/
																pk_id	= '',									/*pk_id*/
																it_id		= '',
																memo = '전체주문상태',			/*메모태그*/
																admin_id = '$mb_id',		/*로그를 남긴 관리자ID*/
																col = 'stats',				/*변경 항목*/
																value = '40',					/*변경된 값*/
																reg_date = now()				/*변경된 날짜*/
			";
			sql_query($ins_sql);

			$sql = "	UPDATE	clay_order	SET
												stats = '40'
							WHERE		od_id = '$od_id'
							AND			stats IN (25)	/* 상품준비중인 주문내역만 배송완료로 - 박민우 요청 2015.09.01 */
			";
			sql_query($sql);



			/* SMS */
			$SMS = new SMS5;
			$SMS->SMS_con($config['cf_icode_server_ip'], $config['cf_icode_id'], $config['cf_icode_pw'], $config['cf_icode_server_port']);

			unset($mh_hp);
			$co['hphone'] = get_hp($co['hphone'], 0);

			$mh_hp[]['bk_hp'] = $co['hphone'];	//수신자번호
			$mh_reply = preg_replace('/-/','',$member['mb_hp']);	//발신자번호

			//문자 셋팅
			$mh_send_message = $v_sms['40'];
			$mh_send_message = preg_replace("/{주문ID}/", $co['od_id'], $mh_send_message);
			$mh_send_message = preg_replace("/{운송장번호}/", $delivery_invoice, $mh_send_message);


			$result = $SMS->Add($mh_hp, $mh_reply, '', '', $mh_send_message, $booking, 1);
			$result = $SMS->Send();
			$SMS->Init(); // 보관하고 있던 결과값을 지웁니다.

			/* 로그기록 */
			$ins_sql = "INSERT	INTO 	sms5_write		SET
																wr_renum = '$wr_renum',
																od_id			=	'$co[od_id]',			/* 관련 주문번호 */
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

	/* 관리자메모 */
	if($co[admin_memo] != $admin_memo) {
			$ins_sql = "INSERT INTO 		log_table	SET
															logtype = 'clayorder',		/*로그유형 ( ex: clayorder )*/
															gr_id = '$co[od_id]',				/*pk_id*/
															memo = '관리자메모',		/*메모태그*/
															admin_id = '$mb_id',		/*로그를 남긴 관리자ID*/
															col = 'delivery_invoice',			/*변경 항목*/
															value = '$admin_memo',					/*변경된 값*/
															reg_date = now()				/*변경된 날짜*/
		";
		sql_query($ins_sql);
	}

	/* 배송지변경 */
	if($co[addr2] != $addr2) {
		$ins_sql = "INSERT INTO 	log_table	SET
												logtype = 'clayorder',		/*로그유형 ( ex: clayorder )*/
												gr_id = '$co[od_id]',				/*pk_id*/
												memo = '배송지변경',		/*메모태그*/
												admin_id = '$mb_id',		/*로그를 남긴 관리자ID*/
												col = 'delivery_invoice',			/*변경 항목*/
												value = '$addr2',					/*변경된 값*/
												reg_date = now()				/*변경된 날짜*/
		";
		sql_query($ins_sql);

	}


	/* 배송방법 */
	if($co[delivery_type] != $delivery_type) {
		$ins_sql = "INSERT INTO 		log_table	SET
													logtype = 'clayorder',		/*로그유형 ( ex: clayorder )*/
													gr_id	= '$co[od_id]',				/*pk_id*/
													pk_id	= '',									/*pk_id*/
													it_id		= '',
													memo = '배송방법',			/*메모태그*/
													admin_id = '$mb_id',		/*로그를 남긴 관리자ID*/
													col = 'delivery_type',				/*변경 항목*/
													value = '".$v_delivery_type[$delivery_type]."',
													reg_date = now()				/*변경된 날짜*/
		";
		sql_query($ins_sql);
	}

	/* 현금영수증입력정보 */
	if($co[cash_receipt_info] != $cash_receipt_info) {
		$ins_sql = "INSERT INTO 		log_table	SET
														logtype = 'clayorder',		/*로그유형 ( ex: clayorder )*/
														gr_id	= '$co[od_id]',				/*pk_id*/
														pk_id	= '',									/*pk_id*/
														it_id		= '',
														memo = '현금영수증입력정보',			/*메모태그*/
														admin_id = '$mb_id',		/*로그를 남긴 관리자ID*/
														col = 'cash_receipt_type_info',				/*변경 항목*/
														value = '".$cash_receipt_type_nm." ".$cash_receipt_info."',
														reg_date = now()				/*변경된 날짜*/
		";
		sql_query($ins_sql);
	}

	/* 클레이아이디 */
	if($co[clay_id] != $clay_id) {
		$ins_sql = "INSERT INTO 		log_table	SET
													logtype = 'clayorder',		/*로그유형 ( ex: clayorder )*/
													gr_id	= '$co[od_id]',				/*pk_id*/
													pk_id	= '',									/*pk_id*/
													it_id		= '',
													memo = '클레이닉네임',			/*메모태그*/
													admin_id = '$mb_id',		/*로그를 남긴 관리자ID*/
													col 	= 'clay_id',				/*변경 항목*/
													value = '".$co[clay_id]." -> ".$clay_id."',
													reg_date = now()				/*변경된 날짜*/
		";
		sql_query($ins_sql);

		$sql = "	UPDATE	clay_order	SET
												clay_id = '$clay_id'
							WHERE		od_id = '$od_id'	";
		sql_query($sql);
	}

	/* 주문자명 */
	if($co[name] != name) {
		$ins_sql = "INSERT INTO 		log_table	SET
													logtype = 'clayorder',		/*로그유형 ( ex: clayorder )*/
													gr_id	= '$co[od_id]',				/*pk_id*/
													pk_id	= '',									/*pk_id*/
													it_id		= '',
													memo = '주문자명',			/*메모태그*/
													admin_id = '$mb_id',		/*로그를 남긴 관리자ID*/
													col 	= 'name',				/*변경 항목*/
													value = '".$co[name]." -> ".$name."',
													reg_date = now()				/*변경된 날짜*/
		";
		sql_query($ins_sql);

		$sql = "	UPDATE	clay_order	SET
												name = '$name'
							WHERE		od_id = '$od_id'	";
		sql_query($sql);
	}

	/* 연락처 */
	if($co[hphone] != $hphone) {
		$ins_sql = "INSERT INTO 		log_table	SET
													logtype = 'clayorder',		/*로그유형 ( ex: clayorder )*/
													gr_id	= '$co[od_id]',				/*pk_id*/
													pk_id	= '',									/*pk_id*/
													it_id		= '',
													memo = '연락처',				/*메모태그*/
													admin_id = '$mb_id',		/*로그를 남긴 관리자ID*/
													col 	= 'clay_id',				/*변경 항목*/
													value = '".$co[hphone]." -> ".$hphone."',
													reg_date = now()				/*변경된 날짜*/
		";
		sql_query($ins_sql);

		$sql = "	UPDATE	clay_order	SET
										hphone = '$hphone'
							WHERE		od_id = '$od_id'			";
		sql_query($sql);

	}


	/* 배송비 선결제로 수정할 경우 */
	if($delivery_type == 'D01') {
		$dp_sql = "	SELECT	GI.baesongbi
								FROM	clay_order_info CI
											LEFT JOIN gp_info GI ON (GI.gpcode = CI.gpcode)
								WHERE	CI.od_id = '$od_id'
		";
		$dp_result = sql_query($dp_sql);
		$dp = mysql_fetch_array($dp_result);
		$배송비가격 = $dp[baesongbi];
	}
	else {
		$배송비가격 = '0';
	}
	$배송비가격조건 = ",delivery_price = $배송비가격";


	$sql = "	UPDATE	clay_order_info	SET
										clay_id = '$clay_id'
										,name				= '$name'
										,paytype		= '$paytype'
										,receipt_name = '$receipt_name'
										,hphone = '$hphone'
										,admin_memo = '$admin_memo'
										,addr1 			= '$addr1'
										,addr1_2 		= '$addr1_2'
										,addr2 			= '$addr2'
										,zip 				= '$zip'
										,cash_receipt_yn = '$cash_receipt_yn'
										,cash_receipt_type = '$cash_receipt_type'
										,cash_receipt_info = '$cash_receipt_info'
										,delivery_invoice = '$delivery_invoice'
										,delivery_type = '$delivery_type'
										$배송비가격조건
					WHERE		od_id = '$od_id'
	";
	sql_query($sql);
}

goto_url("./orderlist.php?$qrystr");
//go_url("($list_ct_id) 해당 주문내역들이 $change_status로 변경되었습니다","orderlist.php?$qstr");
?>