<?php
include_once('./_common.php');
global $is_admin;

$list_it_id = explode(',',$it_id);
$memo = strip_tags($memo);


/* 현재 진행중인 공구정보 로딩 */
$gpinfo_sql = "	SELECT	*
								FROM		gp_info
								WHERE		gpcode = '$gpcode'
";
$공구정보 = sql_fetch($gpinfo_sql);


/* 공구정보가 존재할경우 */
if($공구정보[gpcode]) {
	$시작일 = $공구정보[start_date];
	$종료일 = $공구정보[end_date];
	$배송비 = $공구정보[baesongbi];
	$볼륨가적용 = $공구정보[volprice_yn];
	$it_id = $공구정보[links];
}
else {
	$gpcode = $개인구매코드;
	$gpinfo_sql = "	SELECT	*
									FROM		gp_info
									WHERE		gpcode = '$gpcode'
	";
	$공구정보 = sql_fetch($gpinfo_sql);

	$시작일 = $공구정보[start_date];
	$종료일 = $공구정보[end_date];
	$배송비 = $공구정보[baesongbi];
	$볼륨가적용 = $공구정보[volprice_yn];

	/*바로주문일경우*/
	if($it_id) {
		$list_it_id[] = $it_id;
	}
	else {
		$list_it_id = explode(',',$it_id);
	}
}


if($it_id) {

	if($공구정보[gp_type] == 'A') {
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

	} else {
		/* 옵션 고유ID 생성 SQL    by. JHW */
		$seq_sql = "	SELECT	CONCAT(	'CL',
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
	}


	list($od_id) = mysql_fetch_array(sql_query($seq_sql));
	$succ_cnt = $fail_cnt = 0;

	/* 전체신청상품 주문서 작성 */
	for($i = 0; $i < count($list_it_id); $i++) {
		$it_id = $list_it_id[$i];
		$신청수량 = $_POST[it_qty][$i];
		if($신청수량 == 0) continue;

		$sql_product_rp = str_replace('#상품기본조건#', " WHERE		gp_id = '$it_id' ", $sql_product);

		/* 상품정보 */
		$it_sql = " SELECT		T.gp_id,
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
													T.real_jaego AS jaego,
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
													CT.ca_adult_use,
													SD.solds_itid
									FROM		$sql_product_rp
													LEFT JOIN v_gpinfo_solds SD ON (SD.gpcode = '$gpcode' AND SD.solds_itid = T.gp_id)
													LEFT JOIN g5_shop_category CT ON (CT.ca_id = T.ca_id)
		";
		$it = mysql_fetch_array(sql_query($it_sql));
		$it_name = $it[gp_name];


		/* 신청수량 */
		$clay_sql = "	SELECT	IFNULL(SUM(it_qty),0) AS total_qty
									FROM		clay_order
									WHERE		it_id = '$it_id'
									AND		(	gpcode = '$gpcode' OR gpcode = '$개인구매코드' )	/* 해당공구 및 해당공구기간안의 개별주문 */
									AND			od_date BETWEEN '$시작일 00:00:00' AND '$종료일 23:59:59'
									AND			stats NOT IN ('99')		/*취소제외*/
		";
		$t = mysql_fetch_array(sql_query($clay_sql));
		$현재공구총신청수량 = $t[total_qty];

		/* 코투 보유분이 우선순위(설정되어있는게 우선적으로 적용) */
		//재고컬럼은 jaego로 일괄통일
		//$상품재고 = ($it[gp_have_qty] > 0) ? $it[gp_have_qty]*1 : $it[jaego]*1;
		$상품재고 = $it[jaego]*1;
		$남은수량 = $상품재고 - $현재공구총신청수량;
		$남은수량 = ($남은수량 > 0) ? $남은수량 : 0;
		//$신청가능수량 = $it[it_buy_max_qty];


		/* 실시간형 or 고정형 */
		/* 신청수량에 따른 볼륨프라이싱 가격 */
		if($공구정보[volprice_yn] == 'Y'){
			$price_sql = "	SELECT	PO.	gp_id,
															PO.	po_num,
															PO.	po_sqty,	/*최소신청수량*/
															PO.	po_eqty,	/*최대신청수량*/
															PO.	po_cash_price,
															PO.	po_card_price,
															PO.	po_add_price,
															PO.	po_jaego	/*단품상품을 위한 재고정보 기입*/
											FROM		g5_shop_group_purchase_option PO
											WHERE		PO.gp_id = '$it_id'
											AND			PO.po_sqty <= '$현재공구총신청수량'
											AND			PO.po_eqty >= '$현재공구총신청수량'
			";
			$price = mysql_fetch_array(sql_query($price_sql));

			$신청당시상품가격 = getExchangeRate($price[po_cash_price],$it_id);
		}	/* 고정가격, 미리 원화설정된 금액 */
		else {
			$신청당시상품가격 = $it[po_cash_price];
		}

		/* 카드결제일 경우 수수료 3.5% 추가 */
		if($paytype == 'P02') {
			$신청당시상품가격 = $신청당시상품가격 * 1.03;
		}


		if($신청수량 > $남은수량 && $is_admin != 'super') {
			$경고메시지 .= "{$it[gp_name]} 상품의 경우 고객님의 신청수량({$신청수량}개)이 남은수량(현재 {$남은수량}개)을 초과하였습니다. 새로고침후 다시 주문해주세요";
// 			echo "
// 			<script>
// 			alert($경고메시지);
// 			history.go(-1);
// 			</script>";
// 			exit;
		}
		else {
			$주문금액 += ($신청당시상품가격 * $신청수량);

			$hphone = "$hp1-$hp2-$hp3";
			$it_name = str_replace('\"', "", $it_name);
			
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


		$it[gp_name] = 개행문자삭제($it[gp_name]);

		if($result) {
			$succ_cnt++;
			$성공메시지 .= "{$it[gp_name]} 상품(수량 {$신청수량}개)의 주문신청이 정상처리되었습니다\\n";
		} else {
			$fail_cnt++;
			$경고메시지 .= "{$it[gp_name]} 상품(수량 {$신청수량}개)의 주문신청이 실패하였습니다\\n";
		}
	} //for end


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
	if($주문금액 > 100000 && $gpcode == 'E201512_09') $배송비 = 0;

	/* 배송비 관련 계산 */
	if($delivery_type == 'D01') {
		/* 계산공식 추가 필요, 3kg당 5,000원 추가 */
		//$배송비 = ($배송비 > 0) ? $배송비 : $it[gp_sc_price];
		$주문금액 += $배송비;
	}
	else {
		$배송비 = 0;
	}


	$ins_sql = "	INSERT	INTO 	clay_order_info		SET
														gpcode = '$gpcode',
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
														delivery_price= '$배송비',
														delivery_direct= 'N',
														delivery_invoice = '$invoice',
														paytype = '$paytype',
														od_date = now(),
														od_browser = '$접속기기',
														od_ip = '$방문자IP'
	";
	$result = sql_query($ins_sql);

}//if end

/* SMS */
//$mh_reply = '01023661233';
$mh_reply = '0220886657';
$SMS = new SMS5;
$SMS->SMS_con($config['cf_icode_server_ip'], $config['cf_icode_id'], $config['cf_icode_pw'], $config['cf_icode_server_port']);
$mh_hp[]['bk_hp'] = get_hp($hphone,0);
$주문금액 = number_format($주문금액);

$comment = "\\n{$성공메시지}{$경고메시지}주문번호는 {$od_id}입니다.\\n상담시 필요하니 기록해두시기 바랍니다";

/* 볼륨가적용 여부,  코투에서 이제 볼륨가적용으로 공구를 진행안하니 투데이(주)로 변경 */
if($볼륨가적용 == 'Y') {
	$mh_send_message = "[{$od_id}] 공구 마감후 입금안내SMS 발송시 그때 결제바랍니다-투데이(주)";
	$comment .= "\\n※ 해당 공구는 볼륨프라이싱 표에 나온대로 총 공구신청수량에 따라 결제금액이 할인적용될수 있습니다.\\n마감후 볼륨가가 적용된 금액으로 입금안내SMS문자가 발송되오니 그때 결제하시면 정상적으로 할인혜택을 받으실수 있습니다";
}
else	/* 볼륨가미적용 */
{
	$mh_send_message = "[{$od_id}] 금액:{$주문금액}원, 우리은행 1005503165645 투데이(주)";
}

/* 카드결제 */
if($paytype == 'P02') {
	$mh_send_message = "[{$od_id}] 금액:{$주문금액}원, 담당자가 연락드릴예정입니다.";
}
else if($paytype == 'P03') {
	$mh_send_message = "[{$od_id}] 외화결제 신청되었습니다. 담당자가 연락드릴예정입니다.";
}
else if($paytype == 'P04') {
	$mh_send_message = "[{$od_id}] 귀금속결제 신청되었습니다. 담당자가 연락드릴예정입니다.";
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

echo "
<script>
	alert(\"$comment\");
	location.href = '/';
</script>";

?>