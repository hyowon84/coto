<?php
include_once('./_common.php');
global $is_admin;

$ss_id = $_SESSION[ss_id];
$mb_id = $member[mb_id];



//개별공동구매가 진행중인것만 주문

if(!$개인구매코드) alert("개별구매 주문접수 기간이 아닙니다. 관리자에게 문의해주세요.","/coto/orderpay.php");

$배송비 = $개별공구[baesongbi];
$다이렉트주문 = ($_POST[gpcode] && $_POST[it_id]) ? true : false;

/* 1개 상품 바로 주문하기로 넘어왔을경우 */
if($다이렉트주문) {

	if ( !($_POST[it_qty] > 0) ) {
		alert('수량이 입력되지 않았습니다', '/');
		exit;
	}

	//다이렉트는 공구일수도 빠른배송상품일수도 있다.
	if($gpcode == 'QUICK') {
		$sql_product = makeProductSql($gpcode);
		$sql_product_rp = str_replace('#상품기본조건#', " AND		gp_id = '$_POST[it_id]' ", $sql_product);
	} else {
		$sql_product = makeProductSql($gpcode);
		$sql_product_rp = str_replace('#공동구매조건#', " AND		IT.gp_id = '$_POST[it_id]' ", $sql_product);
	}
	
	
	/* 상품정보 */
	$cart_sql = " SELECT		T.gp_id AS it_id,
 													'$_POST[it_qty]' AS it_qty,
													T.ca_id,
													T.ca_id2,
													T.ca_id3,
													T.event_yn,
													T.gp_name,
													T.gp_site,
													T.gp_img,
													T.gp_explan,
													T.gp_objective_price,
													T.gp_have_qty,
													T.gp_buy_min_qty,
													T.gp_buy_max_qty,
													T.gp_charge,
													T.gp_duty,
													T.gp_use,
													T.gp_order,
													T.gp_stock,
													T.gp_time,
													T.gp_update_time,
													T.gp_price,
													T.gp_price_org,
													T.gp_card_price,
													T.gp_price_type,
													T.gp_metal_type,
													T.gp_metal_don,
													T.gp_metal_etc_price,
													T.gp_sc_method,
													T.gp_sc_price,
													T.it_type,
													T.gp_type1,
													T.gp_type2,
													T.gp_type3,
													T.gp_type4,
													T.gp_type5,
													T.gp_type6,
													IF(T.real_jaego > 0,T.real_jaego,0) AS real_jaego,
													CASE
														WHEN	T.ca_id LIKE 'CT%' || T.ca_id = 'GP'	THEN
															CASE
																WHEN	T.gp_price_type = 'Y'	THEN	/*실시간스팟시세*/
																	CEIL(T.gp_realprice / 100) * 100
																WHEN	T.gp_price_type = 'N'	THEN	/*고정가달러환율*/
																	CEIL(T.gp_fixprice / 100) * 100
																WHEN	T.gp_price_type = 'W'	THEN	/*원화적용*/
																	T.gp_price
																ELSE
																	0
															END
														ELSE
															/* 딜러업체 상품 */
															CASE
																WHEN	T.gp_price_type = 'Y'	THEN	/*실시간스팟시세*/
																	CEIL(T.gp_realprice / 100) * 100
																WHEN	T.gp_price_type = 'N'	THEN	/*고정가달러환율*/
																	CEIL(IFNULL(T.po_cash_price,T.gp_price) / 100) * 100
																WHEN	T.gp_price_type = 'W'	THEN	/*원화적용*/
																	T.gp_price
																ELSE
																	0
															END
													END po_cash_price,
													T.USD,
													CT.ca_name,
													CT.ca_use,
													CT.ca_include_head,
													CT.ca_include_tail,
													CT.ca_cert_use,
													CT.ca_adult_use
									FROM		$sql_product_rp
													LEFT JOIN g5_shop_category CT ON (CT.ca_id = T.ca_id)
	";
}
else {

	// $s_cart_id 로 현재 장바구니 자료 쿼리
	$cart_sql = "	SELECT	T.gp_id,
												T.ca_id,
												T.ca_id2,
												T.ca_id3,
												T.event_yn,
												T.gp_name,
												T.gp_site,
												T.gp_img,
												T.gp_explan,
												T.gp_objective_price,
												T.gp_have_qty,
												T.gp_buy_min_qty,
												T.gp_buy_max_qty,
												T.gp_charge,
												T.gp_duty,
												T.gp_use,
												T.gp_order,
												T.gp_stock,
												T.gp_time,
												T.gp_update_time,
												T.gp_price,
												T.gp_price_org,
												T.gp_card_price,
												T.gp_price_type,
												T.gp_metal_type,
												T.gp_metal_don,
												T.gp_metal_etc_price,
												T.gp_sc_method,
												T.gp_sc_price,
												T.it_type,
												T.gp_type1,
												T.gp_type2,
												T.gp_type3,
												T.gp_type4,
												T.gp_type5,
												T.gp_type6,
												IF(T.real_jaego > 0,T.real_jaego,0) AS real_jaego,
												CASE
													WHEN	T.ca_id LIKE 'CT%' || T.ca_id = 'GP'	THEN
														CASE
															WHEN	T.gp_price_type = 'Y'	THEN	/*실시간스팟시세*/
																CEIL(T.gp_realprice / 100) * 100
															WHEN	T.gp_price_type = 'N'	THEN	/*고정가달러환율*/
																CEIL(T.gp_fixprice / 100) * 100
															WHEN	T.gp_price_type = 'W'	THEN	/*원화적용*/
																T.gp_price
															ELSE
																0
														END
													ELSE
														CEIL(IFNULL(T.po_cash_price,T.gp_price) / 100) * 100
												END po_cash_price,
												T.number,
												T.it_id,				/*상품코드*/
												T.mb_id,				/*계정 또는 세션아이디*/
												T.gpcode,				/*연결된 공구코드*/
												T.gpcode_name,
												T.it_qty,				/*상품수량*/
												T.it_name,			/*상품명*/
												T.stats,				/*상태값*/
												T.reg_date,			/*등록일시*/
												CA.ca_name,
												T.GP_ORDER_QTY,
												T.VIV_QTY,
												T.RIV_QTY,
												T.ORDER_QTY
								FROM		$sql_cartproduct
												LEFT JOIN g5_shop_category CA ON (CA.ca_id = T.ca_id)
								WHERE		T.real_jaego >= T.it_qty
	";
}
$cart_result = sql_query($cart_sql);
$cart_cnt = mysql_num_rows($cart_result);


if(!$cart_cnt) {
	alert("장바구니에 담긴 상품이 없습니다.","/coto/orderpay.php");
}

else if($cart_cnt > 0) {
	/* 옵션 고유ID 생성 SQL    by. JHW */
	$seq_sql = "	SELECT	CONCAT(	'PB',
																		DATE_FORMAT(now(),'%Y%m%d'),
																		LPAD(COALESCE(	(	SELECT	MAX(SUBSTR(od_id,11,4))
																											FROM		clay_order_info
																											WHERE		od_id LIKE CONCAT('%',DATE_FORMAT(now(),'%Y%m%d'),'%')
																											ORDER BY od_id DESC
																										)
																		,'0000') +1,4,'0')
														)	AS oid
										FROM		DUAL
	";
	list($od_id) = mysql_fetch_array(sql_query($seq_sql));
	$succ_cnt = $fail_cnt = 0;

	/* 전체신청상품 주문서 작성 */
	//for ($i=0; $row = mysql_fetch_array($result); $i++) {
	while($row = mysql_fetch_array($cart_result)) {


		$od_sql = "	SELECT	CL.mb_id,
												CL.it_id,
												SUM(it_qty) AS SUM_QTY
								FROM		clay_order CL
								WHERE		1=1
								AND			CL.it_id = '$row[it_id]'
								AND			(CL.mb_id = '$mb_id' OR CL.mb_id = '$ss_id')
								GROUP BY CL.mb_id, CL.it_id
		";
		$sumdata = mysql_fetch_array(sql_query($od_sql));
		
		
		/* 1개 상품 바로주문일경우 재고가 부족하면 back */
		if($다이렉트주문 && $row[real_jaego] < $_POST[it_qty]) {
			echo "<script>
							alert(\"신청수량[$_POST[it_qty]]이 재고[$row[real_jaego]]를 초과했습니다. 다시 신청해주세요\");
							history.back();
						</script>";
			
//			echo $cart_sql;
//			exit;
		}
		
		$gpcode = ($row[gpcode]) ? $row[gpcode] : $_POST[gpcode];
		
		$남은수량 = $row[real_jaego];
		$신청수량 = $row[it_qty];
		
		$총무게 += ($row[gp_metal_don] * $row[it_qty] * 31.1035);
		$상품명 = $row[gp_name];

		$상품판매가 = $row[po_cash_price];
		$it_id = $row[it_id];
		$it_name = $row[gp_name];
		$최대구매수량 = $row[gp_buy_max_qty];
		$주문내역수량 = $sumdata[SUM_QTY];
		$누적주문수량 = $신청수량 + $주문내역수량;
		$현재공구총신청수량 = $row[GP_ORDER_QTY];
		
		if($신청수량 == 0) continue;

		/* 실시간형 or 고정형 */
		/* 신청수량에 따른 볼륨프라이싱 가격 */
		if( $공구정보[volprice_yn] == 'Y' && $row[gp_price] == 0 ){
			$price_sql = "	SELECT	PO.gp_id,
															PO.po_num,
															PO.po_sqty,	/*최소신청수량*/
															PO.po_eqty,	/*최대신청수량*/
															PO.po_cash_price,
															PO.po_card_price,
															PO.po_add_price,
															PO.po_jaego	/*단품상품을 위한 재고정보 기입*/
											FROM		g5_shop_group_purchase_option PO
											WHERE		PO.gp_id = '$it_id'
											AND			PO.po_sqty <= '$현재공구총신청수량'
											AND			PO.po_eqty >= '$현재공구총신청수량'
			";
			$price = mysql_fetch_array(sql_query($price_sql));

			$신청당시상품가격 = getExchangeRate($price[po_cash_price],$it_id);
		}	/* 고정가격, 미리 원화설정된 금액 */
		else {
			$신청당시상품가격 = $row[po_cash_price];
		}


		if( $누적주문수량 > $최대구매수량 ) {
			$경고메시지 .= "{$row[gp_name]} 상품의 경우 고객님의 누적주문수량({$누적주문수량}개)이 최대구매수량({$최대구매수량}개)을 초과하였습니다. 수량조절후 다시 주문해주세요\\n";
			$result = false;
		}
		else if($신청수량 > $남은수량 && $is_admin != 'super') {
			$경고메시지 .= "{$row[gp_name]} 상품의 경우 고객님의 신청수량({$신청수량}개)이 남은수량(현재 {$남은수량}개)을 초과하였습니다. 수량조절후 다시 주문해주세요\n";
			$result = false;
		}
		else {
			$주문금액 += ($신청당시상품가격 * $신청수량);

			$mb_id = ($mb_id) ? $mb_id : $_SESSION[ss_id];
			
			$it_name = str_replace('\"', "", $it_name);
			$hphone = "$hp1-$hp2-$hp3";
			/* 볼륨가적용일땐 주문신청(00), 볼륨가적용안함일땐 주문금액문자 전송되니 입금요청 상태로 변경*/
			$기본주문상태 = ($공구정보[volprice_yn] == 'N') ? '10' : '00';
			$ins_sql = "	INSERT	INTO 	clay_order		SET
																gpcode = '$gpcode',
																od_id = '$od_id',
																it_id = '$it_id',
																it_name = \"$it_name\",
																it_qty	=	'$신청수량',
																it_org_price = '$신청당시상품가격',
																stats = '$기본주문상태',
																clay_id = '$clay_id',
																mb_id	= '$mb_id',
																name = '$name',
																hphone = '$hphone',
																od_date = now()
			";
			$result = sql_query($ins_sql);
		}

		$row[gp_name] = 개행문자삭제($row[gp_name]);

		if($result) {
			$succ_cnt++;
			$성공메시지 .= "[{$row[gpcode_name]} - {$row[gp_name]}] 상품(수량 {$신청수량}개)의 주문신청이 정상처리되었습니다\\n";
		} else {
			$fail_cnt++;
			$경고메시지 .= "{$row[gp_name]} 상품(수량 {$신청수량}개)의 주문신청이 실패하였습니다\\n";
		}
	} //while end

	if(!$succ_cnt) {
		$comment = "$성공메시지 \\n $경고메시지";
		echo "<script>
						alert(\"$comment\");
						location.href = '/';
					</script>";
		exit;
	}
	
	/* 현금영수증신청 */
	if($cash_receipt_yn == 'Y') {
		if($cash_receipt_type == 'C01') {
			$현금영수증신청정보 = "$cash_hp1-$cash_hp2-$cash_hp3";
		}
		if($cash_receipt_type == 'C02') {
			$현금영수증신청정보 = "$cash_bno1-$cash_bno2-$cash_bno3";
		}
	}
	else {
		$cash_receipt_yn = 'N';
		$cash_receipt_type = '';
	}

	/* 연말 코투세일 10만원 이상 구매자 배송비 무료 */
	if($주문금액 > 100000 && $개인구매코드 == 'E201512_09') $배송비 = 0;


	/* 배송비 관련 계산 */
	if($delivery_type == 'D01') {
		$총무게kg = $총무게 / 1000;
		$무게별배송비 = getDeliveryPricePerKg($총무게kg);
		$주문금액 += $무게별배송비;
	}
	else {
		$무게별배송비 = 0;
	}

	$ins_sql = "	INSERT	INTO 	clay_order_info		SET
														gpcode = '$개인구매코드',
														od_id = '$od_id',
														clay_id = '$clay_id',
														mb_id = '$mb_id',
														name = '$name',
														receipt_name = '$receipt_name',
														hphone = '$hphone',
														zip = '$zip',
														addr1 = '$addr1',
														addr1_2 = '$addr1_2',
														addr2 = '$addr2',
														memo = '$memo',
														cash_receipt_yn = '$cash_receipt_yn',
														cash_receipt_type = '$cash_receipt_type',
														cash_receipt_info = '$현금영수증신청정보',
														delivery_type = '$delivery_type',
														delivery_price= '$무게별배송비',
														delivery_direct= 'N',
														delivery_invoice = '$invoice',
														od_ip = '$방문자IP',
														od_browser = '$접속기기',
														od_date = now()
	";
	$result = sql_query($ins_sql);
	
}//if end


/* SMS */
$mh_reply = '0220886657';
//$mh_reply = '01023661233';
$SMS = new SMS5;
$SMS->SMS_con($config['cf_icode_server_ip'], $config['cf_icode_id'], $config['cf_icode_pw'], $config['cf_icode_server_port']);
$mh_hp[]['bk_hp'] = get_hp($hphone,0);
$주문금액 = number_format($주문금액);

/* 볼륨가적용 여부 */
if($볼륨가적용 == 'Y') {
	$mh_send_message = "[{$od_id}] 공구 마감후 입금안내SMS 발송시 그때 결제바랍니다-코인즈투데이";

	$comment = "\\n{$성공메시지}{$경고메시지}주문번호는 {$od_id}입니다.\\n상담시 필요하니 기록해두시기 바랍니다\\n※ 해당 공구는 볼륨프라이싱 표에 나온대로 총 공구신청수량에 따라 결제금액이 할인적용될수 있습니다.\\n마감후 볼륨가가 적용된 금액으로 입금안내SMS문자가 발송되오니 그때 결제하시면 정상적으로 할인혜택을 받으실수 있습니다";
}
else	/* 볼륨가미적용 */
{
	$mh_send_message = "[{$od_id}] 금액:{$주문금액}원, 신한은행 110408552944 코인즈투데이";

	$comment = "{$성공메시지}{$경고메시지}주문번호는 {$od_id}입니다.\\n상담시 필요하니 기록해두시기 바랍니다";
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

/*다이렉트주문이 아닐경우*/
if(!$다이렉트주문) {
	
	$cart_sql = "	UPDATE	coto_cart		SET
													stats = 60
								WHERE		(ss_id = '$ssid'	OR	mb_id = '$mb_id')
	";
	sql_query($cart_sql);
}

echo "
<script>
	alert(\"$comment\");
	location.href = '/';
</script>";

?>