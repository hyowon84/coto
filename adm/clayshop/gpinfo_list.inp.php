<?php
include_once('./_common.php');

auth_check($auth[$sub_menu], "r");
auth_check($auth[$sub_menu], "w");

//if($is_admin != 'super') exit;




if($mode == 'new') {
	$links = explodeMakeCode(',',$links);
	$sql = "		 INSERT INTO 	gp_info		SET
															gpcode			= '$gpcode',			/*공구코드*/
															gpcode_name	= '$gpcode_name',	/*공구이름*/
															menu_name		= '$menu_name',		/*카테고리메뉴명*/
															gp_type 		= '$gp_type',			/*정기 or 긴급*/
															links			 	= \"$links\",						/*공구진행할 상품코드들*/
															locks			 	= '$locks',				/*가격락킹할 상품코드들*/
															solds			 	= '$solds',				/*딜러업체 품절상품코드들*/
															start_date	= '$start_date',	/*시작일*/
															end_date 		= '$end_date',		/*종료일*/
															choice_dealer = '$choice_dealer',	/*전체 또는 딜러(브랜드) 선택*/
															baesongbi		= '$baesongbi',
															sell_title	=	'S01',	/*가격타이틀*/
															volprice_yn = '$volprice_yn',	/* 볼륨프라이스설정여부 */
															list_view 	= '$list_view',		/*공구진행내역 보기/안보기*/
															memo 				= '$memo',				/*메모*/
															cafe_url		= '$cafe_url',				/*메모*/
															menu_view 	= 'N',				/*홈페이지 메뉴에 공개여부*/
															stats 			= '00',				/*공구진행상태*/
															reg_date		= now()
	";
	$result = sql_query($sql);
	db_log($sql,'gp_info',"공동구매 신규입력");

}
else if ($mode == 'PRICE_UPDATE_ALL') {
	/* 볼륨가적용에 해당하는 공구에만 적용 */
	if($volprice_yn == 'Y') {
		$볼륨가적용SQL = "	UPDATE	clay_order CO		SET
																	CO.it_org_price = (
																											SELECT	CEIL((( GO.po_cash_price * FD.USD * (1+ (GP.gp_charge + GP.gp_duty)/100) ) * 1.1) /100)*100 AS volprice
																											FROM		(	SELECT	gpcode,
																																				it_id,
																																				SUM(it_qty) AS QTY
																																FROM		clay_order
																																WHERE		stats NOT IN ('99')
																																GROUP BY gpcode, it_id
																															) CLS
																															LEFT JOIN g5_shop_group_purchase GP ON (GP.gp_id = CLS.it_id)
																															LEFT JOIN g5_shop_group_purchase_option GO ON (GO.gp_id = CLS.it_id AND GO.po_sqty <= CLS.QTY AND GO.po_eqty >= CLS.QTY)
																															LEFT JOIN gp_info GI ON (GI.gpcode = CLS.gpcode)
																															,(SELECT	*
																																FROM		flow_price
																																ORDER BY reg_date DESC
																																LIMIT 1
																															) FD
																											WHERE		1=1
																											AND			GI.volprice_yn = 'Y'
																											AND			CLS.it_id = CO.it_id
																										)
												WHERE		CO.gpcode = '$gpcode'
												AND			CO.stats IN ('00','10')
												AND			CO.it_id NOT IN (	SELECT locks_itid	FROM	v_gpinfo_locks	WHERE gpcode = '$gpcode' )
		";
// 		sql_query($볼륨가적용SQL);

	}
}
/* 개별상품가격 수동 업데이트 */
else if ($mode == 'updateProductPrice') {
	$볼륨가적용SQL = "	UPDATE	clay_order CO		SET
																CO.it_org_price = '$price'
											WHERE		CO.gpcode = '$gpcode'
											AND			CO.it_id = '$it_id'
											AND			CO.stats IN ('00','10')

	";
	sql_query($볼륨가적용SQL);

	$ins_sql = "INSERT INTO 		log_table	SET
																logtype = 'clayorder',		/*로그유형 ( ex: clayorder )*/
																gr_id	= '$co[od_id]',				/*pk_id*/
																pk_id	= '$number',			/*pk_id*/
																it_id		= '$co[it_id]',
																memo = '가격갱신',			/*메모태그*/
																admin_id = '$mb_id',		/*로그를 남긴 관리자ID*/
																col = 'stats',				/*변경 항목*/
																value = '".$co[stats]." -> ".$stats."',					/*변경된 값*/
																reg_date = now()				/*변경된 날짜*/
	";
	sql_query($ins_sql);
}
//공구정보 건별 수정
else if($mode == 'mod') {

	$invoice_memo = str_replace("'",'',$invoice_memo);
	$stock_memo = str_replace("'",'',$stock_memo);

	//공구접수에서 발주신청으로 변경시 인보이스 관련 내용 메모 자동입력
	if($stats == '10') {
		$invo_sql = "	SELECT	it_name,
													SUM(it_qty) AS it_qty
									FROM		clay_order
									WHERE		gpcode	= '$gpcode'
									AND			stats		IN ('00','10','20')
									GROUP BY it_id, it_name
		";
		$invo_result = sql_query($invo_sql);

		while($inv = mysql_fetch_array($invo_result)) {
			$invoice .= $inv[it_name]." :: ".$inv[it_qty]."(ea)\r\n";
		}

		$invoice_memo = $invoice."\r\n".$invoice_memo;
	}


	$links = explodeMakeCode(',',$links);
	$sql = "		 UPDATE		gp_info		SET
													gpcode_name	= '$gpcode_name',			/*공구이름*/
													menu_name		= '$menu_name',				/*카테고리메뉴명*/
													links			 	= \"$links\",						/*공구진행할 상품코드들*/
													locks			 	= '$locks',						/*가격락킹할 상품코드들*/
													solds			 	= '$solds',						/*딜러업체 품절상품코드들*/
													start_date	= '$start_date',			/*시작일*/
													end_date 		= '$end_date',				/*종료일*/
													choice_dealer = '$choice_dealer',	/*전체 또는 딜러(브랜드) 선택*/
													baesongbi		= '$baesongbi',
													sell_title	=	'$sell_title',
													volprice_yn = '$volprice_yn',			/*볼륨프라이스설정여부*/
													menu_view 	= '$menu_view',				/*홈페이지 메뉴에 공개여부*/
													list_view 	= '$list_view',				/*공구진행내역 보기/안보기*/
													invoice_memo= '$invoice_memo',
													stock_memo	= '$stock_memo',
													memo 				= '$memo',						/*메모*/
													cafe_url		= '$cafe_url',				/*메모*/
													stats 			= '$stats'						/*공구진행상태*/
								WHERE		gpcode			= '$gpcode'
	";
	sql_query($sql);
	db_log($sql,'gp_info',"공동구매 내용 수정");

	/*주문마감시 공구코드와 관련 공구상품들 공구재고값 초기화 */
	if($stats == '05') {

		$sql = "SELECT	GI.links
						FROM		gp_info GI
						WHERE		GI.gpcode = '$gpcode' ";
		$row = sql_fetch($sql);
		$links = $row[links];

		$upd_sql = "UPDATE	g5_shop_group_purchase GP	SET
													GP.gp_jaego = 0
								WHERE		GP.gp_id IN ($links)
		";
		sql_query($upd_sql);
		
	}

}

/* 볼륨가 적용후 SMS발송 */
else if($mode == 'sms') {
	/* SMS */
	// $mh_reply = '0220886657';
	$mh_reply = '01023661233';
	$SMS = new SMS5;
	$SMS->SMS_con($config['cf_icode_server_ip'], $config['cf_icode_id'], $config['cf_icode_pw'], $config['cf_icode_server_port']);
	$mh_hp[]['bk_hp'] = get_hp($hphone,0);
	$주문금액 = number_format($주문금액);

	/* 볼륨가적용 여부 */
	if($볼륨가적용 == 'Y') {
		$mh_send_message = "[{$od_id}] 공구 마감후 입금안내SMS 발송시 그때 결제바랍니다-코인즈투데이";

		$comment = "'\\n{$성공메시지}{$경고메시지}주문번호는 {$od_id}입니다.\\n상담시 필요하니 기록해두시기 바랍니다\\n※ 해당 공구는 볼륨프라이싱 표에 나온대로 총 공구신청수량에 따라 결제금액이 할인적용될수 있습니다.\\n마감후 볼륨가가 적용된 금액으로 입금안내SMS문자가 발송되오니 그때 결제하시면 정상적으로 할인혜택을 받으실수 있습니다'";
	}
	else	/* 볼륨가미적용 */
	{
		$mh_send_message = "[{$od_id}] 금액:{$주문금액}원, 신한은행 110408552944 코인즈투데이";

		$comment = "'{$성공메시지}{$경고메시지}주문번호는 {$od_id}입니다.\\n상담시 필요하니 기록해두시기 바랍니다'";
	}

	$result = $SMS->Add($mh_hp, $mh_reply, '', '', $mh_send_message, '', 1);
	$result = $SMS->Send();
	$SMS->Init(); // 보관하고 있던 결과값을 지웁니다.

	/* 로그기록 */
	$ins_sql = "INSERT	INTO 	sms5_write		SET
														wr_renum = '$wr_renum',
														od_id			=	'$od_id',							/* 관련 주문번호 */
														wr_reply = '$mh_reply',						/*보내는사람번호*/
														wr_target = '$hphone',						/*받는사람번호*/
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
}


if($result) {
	echo "1";
}
else
{
	echo "0";
}
?>