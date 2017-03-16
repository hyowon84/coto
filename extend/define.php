<?

/* 주문상태값 관련 */
$sql = "SELECT		*
				FROM		comcode CC
				WHERE		CC.ctype = 'clayorder'
				AND			CC.col = 'stats'
				ORDER BY CC.order ASC
";
$result = sql_query($sql);

while($arr = mysql_fetch_array($result)) {
	$temp['code_name'][$arr['code']] = $arr['value'];
	$temp['bgcolor'][$arr['code']] = $arr['bgcolor'];
	$temp['color'][$arr['code']] = $arr['color'];
}

$v_stats = $temp['code_name'];
$v_stats_bgcolor = $temp['bgcolor'];

$menu_category = Array('AP'=>'APMEX',
											'GV'=>'GAINESVILLE',
											'MC'=>'MCM',
											'PM'=>'PARADISE MINT',
											'OD'=>'OTHER DEALER');



$v_sms = Array(	'10' => "[{주문ID}]금액:{주문금액}원, 신한은행 :  110408552944 : {회사명}",
								'20' => "주문번호{주문ID}의 입금이 확인되었습니다. -코인즈투데이",
								'25' => "주문번호{주문ID}의 상품은 10~15일후 배송예정입니다. -코인즈투데이",
								'30' => "주문번호{주문ID}의 상품은 방문전달할 예정입니다. -코인즈투데이",
								'35' => "주문번호{주문ID}의 상품은 고객님께서 방문수령할 예정입니다. -코인즈투데이",
								'40' => "{주문ID}의 상품은 익일 배송될 예정입니다(우체국:{운송장번호})-코인즈투데이",
								'90' => "주문번호{주문ID}에 대한 환불이 처리되었습니다 . -코인즈투데이",
								'400' => "주문하신 상품들은 익일 배송될 예정입니다(우체국:{운송장번호})-코인즈투데이",
);


$v_stats_color = Array('00'=>'black',
											'10'=>'green',
											'20'=>'red',
											'25'=>'',
											'40'=>'#dbf4ff',
											'50'=>'#pink',
											'60'=>'#red',
											'99'=>'gray');

/* 배송유형값 관련 */
$v_delivery_type = Array('D01'=>'선불',	'D02'=>'착불','D03'=>'방문수령','D04'=>'통합배송요청');



$개별공구 = getDirectBuyCode();
$개인구매코드 = $개별공구[gpcode];

/* 상품상세페이지는 상품id에 해당하는 카테고리id가 기준이 되야됨*/
if($_GET[gp_id]) {
	$it_sql = "	SELECT	*
							FROM		g5_shop_group_purchase GP
							WHERE		GP.gp_id = '$gp_id'
	";
	$item = mysql_fetch_array(sql_query($it_sql));
	$_GET[ca_id] = $ca_id = $item[ca_id];
}

$개별오더활성화 = ( stristr($개별공구[choice_dealer], "ALL") || stristr($개별공구[choice_dealer], $ca_id) || substr($ca_id,0,2) == 'CT' || substr($ca_id,0,2) == 'GP') ? true : false;


/* 공통으로 사용할 스팟시세관련 SQL
		상품목록, 상세페이지에서 사용	*/
$sql_admin_product = "	(	SELECT	GP.*,
																	GP.gp_usdprice * FP.USD * 1.16 AS gp_fixprice,
																	
																	FP.USD,

																	/*실시간 스팟시세*/
																	CASE
																		WHEN	GP.gp_metal_type = 'GL' THEN
																				CASE
																					WHEN	GP.gp_spotprice_type = '%' THEN
																						( (GP.gp_metal_don *  FP.GL) + (GP.gp_metal_don * FP.GL * (GP.gp_spotprice/100) ) ) * FP.USD * 1.16

																					/* 1온스 이상 */
																					WHEN	GP.gp_spotprice_type = 'U$' THEN
																						(( FP.GL  +  GP.gp_spotprice)  *  GP.gp_metal_don ) * FP.USD * 1.16

																					/* 1온스 이하 */
																					WHEN	GP.gp_spotprice_type = 'D$' THEN
																						(( FP.GL  * GP.gp_metal_don )  + GP.gp_spotprice ) * FP.USD * 1.16

																					WHEN	GP.gp_spotprice_type = '￦' THEN
																						(GP.gp_metal_don * FP.GL * FP.USD * 1.16) + GP.gp_spotprice
																					ELSE
																						0
																				END
																		WHEN	GP.gp_metal_type = 'SL' THEN
																				CASE
																					WHEN	GP.gp_spotprice_type = '%' THEN
																						( ( FP.SL * GP.gp_metal_don ) + ( GP.gp_metal_don * FP.SL * (GP.gp_spotprice/100) ) ) * FP.USD * 1.16

																					/* 1온스 이상 */
																					WHEN	GP.gp_spotprice_type = 'U$' THEN
																						(( FP.SL  + GP.gp_spotprice)  *  GP.gp_metal_don ) * FP.USD * 1.16

																					/* 1온스 이하 */
																					WHEN	GP.gp_spotprice_type = 'D$' THEN
																						(( FP.SL * GP.gp_metal_don )  + GP.gp_spotprice ) * FP.USD * 1.16

																					WHEN	GP.gp_spotprice_type = '￦' THEN
																						( FP.SL * GP.gp_metal_don * FP.USD * 1.16) + GP.gp_spotprice
																					ELSE
																						0
																				END
																		ELSE
																				0
																	END gp_realprice,

																	/* 재고수량 계산 */
																	CO.CO_SUM,
																	IV.IV_SUM,
																	
																	IFNULL(GP.jaego	,0) + IFNULL(IV.IV_SUM,0) - IFNULL(CO.CO_SUM,0) AS real_jaego
													FROM		(	SELECT	*
																		FROM		g5_shop_group_purchase
																		WHERE		ca_id LIKE 'CT%'
																	) GP
																	
																	
																	/* 총주문수량 */
																	LEFT JOIN ( SELECT	it_id,
																											SUM(it_qty) AS CO_SUM
																							FROM		clay_order
																							WHERE		stats <= 60		/* 취소건 제외, 모든 신청수량 */
																							GROUP BY it_id
																	) CO ON (CO.it_id = GP.gp_id)

																	/*실제발주수량, 가발주포함하는걸로*/
																	LEFT JOIN (	SELECT	iv_it_id,
																											SUM(iv_qty) AS IV_SUM
																							FROM		invoice_item
																							WHERE		1=1
																							#AND			gpcode LIKE '%$목록공구코드%'
																							AND			iv_stats >= 00
																							GROUP BY iv_it_id				
																	) IV ON (IV.iv_it_id = GP.gp_id)

																	,(	SELECT	*
																			FROM		flow_price
																			ORDER BY	reg_date DESC
																			LIMIT 1
																	) FP
												) T
";

//상품쿼리 재활용
function makeProductSql($gpcode) {
	global $member;
	
	/* 빠른배송공구코드가 아닌 공구코드 */
	$목록공구코드 = ($gpcode) ? $gpcode : 'QUICK';
	if($gpcode && $목록공구코드 != 'QUICK') {
		//공동구매의 단일품목을 위한 셋팅값
		//장바구니 목록, 장바구니다음의 주문목록에서 사용
		if($it_id) $AND_SQL = "AND			VGL.links_itid = '$it_id'";
		
		if( $member[admin_yn] != 'Y' ) {
			$관리자조건 = "	AND			IT.gp_use = '1'
											AND			GI.stats = '00'
											AND			GI.end_date >= DATE_FORMAT(NOW(),'%Y-%m-%d') 
			";
		}
		$PRODUCT_QUERY = "	SELECT	IT.*
												FROM		v_gpinfo_links VGL
																LEFT JOIN		gp_info GI ON (GI.gpcode = VGL.gpcode)
																LEFT JOIN		g5_shop_group_purchase IT ON (IT.gp_id = VGL.links_itid)
																
												WHERE		1=1
												$관리자조건
												AND			VGL.gpcode = '$gpcode'
												#공동구매조건#
												$AND_SQL
		";	
	} else {
		
		if( $member[admin_yn] != 'Y' ) {
			$관리자조건 = "	AND			gp_use = '1'
											AND			ca_id LIKE 'CT%'
			";
		}
		$PRODUCT_QUERY = "	SELECT	*
												FROM		g5_shop_group_purchase
												WHERE		1=1
												$관리자조건
												#상품기본조건#
												
		";
	}
	
	if($_GET[mode] == 'jhw') {
		//echo "<textarea>".$PRODUCT_QUERY."</textarea>";
	}
	
	/* 상품가격관리에서는 빠른배송상품에 대한 재고관리가 가능. 공구상품은 재고관리 불가능 발주서통해서만 가능 */
	/* 빠른배송상품 목록 쿼리 - 사용중 
			관리자 상품가격관리에서 재고는
			초기재고값 + 실제발주량(가상발주는 제외 공구진행시) - 총주문수량(공구포함) = 실제재고수량
	*/
	$sql_product = "
										(	SELECT	GP.*,
															PO.po_cash_price * FP.USD * 1.16 AS po_cash_price,
															GP.gp_usdprice * FP.USD * 1.16 AS gp_fixprice,
															
															FP.USD,
	
															/*실시간 스팟시세*/
															CASE
																WHEN	GP.gp_metal_type = 'GL' THEN
																		CASE
																			WHEN	GP.gp_spotprice_type = '%' THEN
																				( (GP.gp_metal_don *  FP.GL) + (GP.gp_metal_don * FP.GL * (GP.gp_spotprice/100) ) ) * FP.USD * 1.16
	
																			/* 1온스 이상 */
																			WHEN	GP.gp_spotprice_type = 'U$' THEN
																				(( FP.GL  +  GP.gp_spotprice)  *  GP.gp_metal_don ) * FP.USD * 1.16
	
																			/* 1온스 이하 */
																			WHEN	GP.gp_spotprice_type = 'D$' THEN
																				(( FP.GL  * GP.gp_metal_don )  + GP.gp_spotprice ) * FP.USD * 1.16
	
																			WHEN	GP.gp_spotprice_type = '￦' THEN
																				(GP.gp_metal_don * FP.GL * FP.USD * 1.16) + GP.gp_spotprice
																			ELSE
																				0
																		END
																WHEN	GP.gp_metal_type = 'SL' THEN
																		CASE
																			WHEN	GP.gp_spotprice_type = '%' THEN
																				( ( FP.SL * GP.gp_metal_don ) + ( GP.gp_metal_don * FP.SL * (GP.gp_spotprice/100) ) ) * FP.USD * 1.16
	
																			/* 1온스 이상 */
																			WHEN	GP.gp_spotprice_type = 'U$' THEN
																				(( FP.SL  + GP.gp_spotprice)  *  GP.gp_metal_don ) * FP.USD * 1.16
	
																			/* 1온스 이하 */
																			WHEN	GP.gp_spotprice_type = 'D$' THEN
																				(( FP.SL * GP.gp_metal_don )  + GP.gp_spotprice ) * FP.USD * 1.16
	
																			WHEN	GP.gp_spotprice_type = '￦' THEN
																				( FP.SL * GP.gp_metal_don * FP.USD * 1.16) + GP.gp_spotprice
																			ELSE
																				0
																		END
																ELSE
																		0
															END gp_realprice,
															
															/*
															CASE
																WHEN	'$목록공구코드' != 'QUICK' THEN		
																	#IFNULL(VIV.VIV_QTY,0) - IFNULL(GPO.GP_ORDER_QTY,0)
																	IFNULL(VIV.VIV_QTY,0) + IFNULL(RIV.RIV_QTY,0) - IFNULL(CO.ORDER_QTY,0)
																ELSE															
																	IFNULL(GP.jaego	,0) + IFNULL(RIV.RIV_QTY,0) - IFNULL(CO.ORDER_QTY,0)
															END AS real_jaego,
															*/
															
															IFNULL(GP.jaego	,0) + IFNULL(RIV.RIV_QTY,0) - IFNULL(CO.ORDER_QTY,0) AS real_jaego, 
															
															VIV.VIV_QTY,
															GPO.GP_ORDER_QTY,
															
															RIV.RIV_QTY,														
															CO.ORDER_QTY
															
											FROM		(	$PRODUCT_QUERY
															) GP
															
															/*가상발주수량*/
															LEFT JOIN (	SELECT	iv_it_id,
																									SUM(iv_qty) AS VIV_QTY
																					FROM		invoice_item
																					WHERE		gpcode LIKE '%$목록공구코드%'
																					AND			iv_stats = '00'
																					GROUP BY iv_it_id											
															)	VIV ON (VIV.iv_it_id = GP.gp_id)
											
															/* 해당공구 총주문수량 */
															LEFT JOIN ( SELECT	it_id,
																									SUM(it_qty) AS GP_ORDER_QTY
																					FROM		clay_order
																					WHERE		stats <= 20		/* 취소건 제외, 모든 신청수량 */
																					AND			gpcode = '$목록공구코드'
																					GROUP BY gpcode, it_id
															) GPO ON (GPO.it_id = GP.gp_id)
															
															/*실제발주수량, 가발주포함하는걸로*/
															LEFT JOIN (	SELECT	iv_it_id,
																									SUM(iv_qty) AS RIV_QTY
																					FROM		invoice_item
																					WHERE		1=1
																					#AND			gpcode LIKE '%$목록공구코드%'
																					AND			iv_stats >= 00
																					GROUP BY iv_it_id				
															)	RIV ON (RIV.iv_it_id = GP.gp_id)
															
															/* 총주문수량 */
															LEFT JOIN ( SELECT	it_id,
																									SUM(it_qty) AS ORDER_QTY
																					FROM		clay_order
																					WHERE		stats <= 60		/* 취소건 제외, 모든 신청수량 */
																					GROUP BY it_id
															) CO ON (CO.it_id = GP.gp_id)
															
															LEFT JOIN	g5_shop_group_purchase_option PO ON (PO.gp_id = GP.gp_id AND po_num = 0)
	
															,(	SELECT	*
																	FROM		flow_price
																	ORDER BY	reg_date DESC
																	LIMIT 1
															) FP
										) T
	";
	return $sql_product;
}


$sql_product = makeProductSql($gpcode);
$sql_aucPrd = makeProductSql('QUICK');


$sql_auction_item = " SELECT		
 																T.gp_id,
																T.ca_id,
																T.ca_id2,
																T.ca_id3,
																T.event_yn,
																T.gp_name,
																T.gp_site,
																T.gp_img,
																T.gp_360img,
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
																T.real_jaego,
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
																CA.ca_name,
																CA.ca_use,
																CA.ca_include_head,
																CA.ca_include_tail,
																CA.ca_cert_use,
																CA.ca_adult_use,
																T.ac_yn,
																T.ac_code,
																T.ac_qty,
																T.ac_enddate,
																T.ac_startprice,
																T.ac_buyprice,								
																IFNULL(BID.BID_CNT,0) AS BID_CNT,
																
																IFNULL(BID.MAX_BID_PRICE,0) AS MAX_BID_PRICE,
																IFNULL(BID.MAX_BID_LAST_PRICE,0) AS MAX_BID_LAST_PRICE,
																
																MAX_MB.mb_id AS MB_ID,
																MYBID.MY_BID_PRICE
												FROM		$sql_aucPrd
																LEFT JOIN g5_shop_category CA ON (CA.ca_id = T.ca_id)
																
																LEFT JOIN (	SELECT	ac_code,
																										it_id,
																										COUNT(*) AS BID_CNT,
																										MAX(bid_price) AS MAX_BID_PRICE,
																										MAX(bid_last_price) AS MAX_BID_LAST_PRICE																										
																						FROM		auction_log
																						WHERE		bid_stats <= 90
																						GROUP BY ac_code, it_id
																) BID ON (BID.ac_code = T.ac_code AND BID.it_id = T.gp_id)
																
																LEFT JOIN (	SELECT	DISTINCT
																										ac_code,
																										it_id,
																										mb_id,
																										bid_price																																																				
																						FROM		auction_log
																						WHERE		bid_stats <= 90
																) MAX_MB ON (MAX_MB.ac_code = T.ac_code AND MAX_MB.it_id = T.gp_id AND MAX_MB.bid_price = BID.MAX_BID_PRICE)
																
																LEFT JOIN (	SELECT	AL.mb_id,
																										AL.ac_code,
																										AL.it_id,
																										MAX(AL.bid_price) AS MY_BID_PRICE
																						FROM		auction_log AL
																						WHERE		AL.mb_id = '$member[mb_id]'
																						GROUP BY AL.mb_id, AL.ac_code, AL.it_id																
																) MYBID ON (MYBID.ac_code = T.ac_code AND MYBID.it_id = T.gp_id)
												WHERE		1=1
";


/* 공통으로 사용할 스팟시세관련 SQL
 장바구니 관련 목록부터 주문까지 사용할 쿼리
 */
$ss_id = $_SESSION[ss_id];
$mb_id = $member[mb_id];
$회원조건 = ($mb_id) ? "	OR			CT.mb_id = '$mb_id' " : '';
$sql_cartproduct = "	(	SELECT	CT.number,
																CT.it_id,				/*상품코드*/
																CT.mb_id,				/*계정 또는 세션아이디*/
																CT.gpcode,			/*연결된 공구코드*/
																GI.gpcode_name,
																CT.it_qty,			/*상품수량*/
																CT.it_name,			/*상품명*/
																CT.stats,				/*상태값*/
																CT.reg_date,		/*등록일시*/
																GP.*,
																PO.po_cash_price * FP.USD * 1.16 AS po_cash_price,
																GP.gp_usdprice * FP.USD * 1.16 AS gp_fixprice,

																/*실시간 스팟시세*/
																CASE
																	WHEN	GP.gp_metal_type = 'GL' THEN
																			CASE
																				WHEN	GP.gp_spotprice_type = '%' THEN
																					( (GP.gp_metal_don *  FP.GL) + (GP.gp_metal_don * FP.GL * (GP.gp_spotprice/100) ) ) * FP.USD * 1.16

																				/* 1온스 이상 */
																				WHEN	GP.gp_spotprice_type = 'U$' THEN
																					(( FP.GL  +  GP.gp_spotprice)  *  GP.gp_metal_don ) * FP.USD * 1.16

																				/* 1온스 이하 */
																				WHEN	GP.gp_spotprice_type = 'D$' THEN
																					(( FP.GL  * GP.gp_metal_don )  + GP.gp_spotprice ) * FP.USD * 1.16

																				WHEN	GP.gp_spotprice_type = '￦' THEN
																					(GP.gp_metal_don * FP.GL * FP.USD * 1.16) + GP.gp_spotprice
																				ELSE
																					0
																			END
																	WHEN	GP.gp_metal_type = 'SL' THEN
																			CASE
																				WHEN	GP.gp_spotprice_type = '%' THEN
																					( ( FP.SL * GP.gp_metal_don ) + ( GP.gp_metal_don * FP.SL * (GP.gp_spotprice/100) ) ) * FP.USD * 1.16

																				/* 1온스 이상 */
																				WHEN	GP.gp_spotprice_type = 'U$' THEN
																					(( FP.SL  + GP.gp_spotprice)  *  GP.gp_metal_don ) * FP.USD * 1.16

																				/* 1온스 이하 */
																				WHEN	GP.gp_spotprice_type = 'D$' THEN
																					(( FP.SL * GP.gp_metal_don )  + GP.gp_spotprice ) * FP.USD * 1.16

																				WHEN	GP.gp_spotprice_type = '￦' THEN
																					( FP.SL * GP.gp_metal_don * FP.USD * 1.16) + GP.gp_spotprice
																				ELSE
																					0
																			END
																	ELSE
																			0
																END gp_realprice,

																/* 재고수량 계산 */
																/*
																CASE
																	WHEN	CT.gpcode != 'QUICK' THEN		
																		IFNULL(VIV.VIV_QTY,0) - IFNULL(GPO.GP_ORDER_QTY,0)
																	ELSE						
																		IFNULL(GP.jaego	,0) + IFNULL(RIV.RIV_QTY,0) - IFNULL(CO.ORDER_QTY,0)
																END AS real_jaego,
																*/
																IFNULL(GP.jaego	,0) + IFNULL(RIV.RIV_QTY,0) - IFNULL(CO.ORDER_QTY,0) AS real_jaego, 
																
																VIV.VIV_QTY,
																GPO.GP_ORDER_QTY,
																RIV.RIV_QTY,
																CO.ORDER_QTY
												FROM		coto_cart CT
																LEFT JOIN gp_info GI ON (GI.gpcode = CT.gpcode)
																LEFT JOIN g5_shop_group_purchase GP ON (GP.gp_id = CT.it_id)

																/*가상발주수량*/
																LEFT JOIN (	SELECT	gpcode,
																										iv_it_id,
																										SUM(iv_qty) AS VIV_QTY
																						FROM		invoice_item
																						WHERE		iv_stats = '00'
																						GROUP BY gpcode, iv_it_id											
																) VIV ON (VIV.gpcode = CT.gpcode AND VIV.iv_it_id = GP.gp_id)
												
																/* 해당공구 총주문수량 */
																LEFT JOIN ( SELECT	gpcode,
																										it_id,
																										SUM(it_qty) AS GP_ORDER_QTY
																						FROM		clay_order
																						WHERE		stats <= 20		/* 취소건 제외, 모든 신청수량 */
																						GROUP BY gpcode, it_id
																) GPO ON (GPO.gpcode = CT.gpcode AND GPO.it_id = GP.gp_id)
																
																/*실제발주수량*/
																LEFT JOIN (	SELECT	iv_it_id,
																										SUM(iv_qty) AS RIV_QTY
																						FROM		invoice_item
																						WHERE		1=1
																						AND			iv_stats >= 00
																						GROUP BY iv_it_id				
																) RIV ON (RIV.iv_it_id = GP.gp_id)
																
																/* 총주문수량 */
																LEFT JOIN ( SELECT	it_id,
																										SUM(it_qty) AS ORDER_QTY
																						FROM		clay_order
																						WHERE		stats <= 60		/* 취소건 제외, 모든 신청수량 */
																						GROUP BY it_id
																) CO ON (CO.it_id = GP.gp_id)

																LEFT JOIN g5_shop_group_purchase_option PO ON (PO.gp_id = CT.it_id AND PO.po_sqty <= CT.it_qty AND PO.po_eqty >= CT.it_qty)

																,(	SELECT	FP.*
																		FROM		flow_price FP
																		ORDER BY FP.reg_date DESC
																		LIMIT 1
																) FP

												WHERE		(CT.ss_id = '$ss_id' $회원조건)
												AND			CT.stats NOT IN (99,90,60)	/* 삭제 또는 주문만료 제외 */
												AND			GP.gp_use = '1'		/*노출여부가 Y여야함*/
												AND			GP.ca_id != 'TP'	/*상품대기카테고리*/
												AND			(	CASE
																		WHEN	CT.gpcode = 'QUICK'	THEN
																			1
																		ELSE	/* 접수기간내, 주문접수 상태값일때만 목록 보여지게 */
																			IF( NOW() < GI.end_date && GI.stats = '00',1,0)
																		END
																	)
												ORDER BY 	CT.reg_date DESC
											) T
";


/* 장바구니 담기, 수정시에 사용하는 쿼리 */
$WHERE_CART = "WHERE		(ss_id = '$_SESSION[ss_id]'	OR	mb_id = '$member[mb_id]')
								AND			it_id = '$it_id'
								AND			stats IN ('00')
";
$sql_cart_update = "
										SELECT	GP.gp_id AS it_id,
														GP.ca_id,
														GP.gp_name AS it_name,
														/*						
														CASE
															WHEN	'!공구코드!' != 'QUICK' THEN		
																IFNULL(VIV.VIV_QTY,0) - IFNULL(GPO.GP_ORDER_QTY,0)
															ELSE
																IFNULL(GP.jaego	,0) + IFNULL(RIV.RIV_QTY,0) - IFNULL(CO.ORDER_QTY,0)
														END AS real_jaego,
														*/
														IFNULL(GP.jaego	,0) + IFNULL(RIV.RIV_QTY,0) - IFNULL(CO.ORDER_QTY,0) AS real_jaego,
														IFNULL(VIV.VIV_QTY,0) AS VIV_QTY,								/*가상발주량*/
														IFNULL(GPO.GP_ORDER_QTY,0) AS GP_ORDER_QTY,			/*해당공구주문량*/
														
														IFNULL(GP.jaego,0) AS jaego,										/*상품재고초기값*/
														IFNULL(RIV.RIV_QTY,0) AS RIV_QTY,								/*실발주량*/
														IFNULL(CO.ORDER_QTY,0) AS ORDER_QTY,						/*공구&빠른 총주문량*/
														
														CT.it_id AS CT_ID,
														IFNULL(CT.CT_SUM,0) AS CT_SUM										/* 카트담은수량 */
										
										FROM		g5_shop_group_purchase GP
														
														/*가상발주수량*/
														LEFT JOIN (	SELECT	iv_it_id,
																								SUM(iv_qty) AS VIV_QTY
																				FROM		invoice_item
																				WHERE		gpcode LIKE '%!공구코드!%'
																				AND			iv_stats = '00'
																				GROUP BY iv_it_id											
														)	VIV ON (VIV.iv_it_id = GP.gp_id)
										
														/* 상품의 총 주문수량 */
														LEFT JOIN ( SELECT	it_id,
																								SUM(it_qty) AS GP_ORDER_QTY
																				FROM		clay_order
																				WHERE		stats <= 60		/* 취소건 제외, 모든 신청수량 */
																				AND			gpcode = '!공구코드!'
																				GROUP BY gpcode, it_id
														) GPO ON (GPO.it_id = GP.gp_id)
														
														/* 상품의 총 발주수량*/
														LEFT JOIN (	SELECT	iv_it_id,
																								SUM(iv_qty) AS RIV_QTY
																				FROM		invoice_item
																				WHERE		1=1
																				#AND		gpcode LIKE '%!공구코드!%'
																				AND			iv_stats >= 00
																				GROUP BY iv_it_id				
														)	RIV ON (RIV.iv_it_id = GP.gp_id)
														
														/* 총주문수량 */
														LEFT JOIN ( SELECT	it_id,
																								SUM(it_qty) AS ORDER_QTY
																				FROM		clay_order
																				WHERE		stats <= 60		/* 취소건 제외, 모든 신청수량 */
																				GROUP BY it_id
														) CO ON (CO.it_id = GP.gp_id)
										
														/* 카트 수량 합계 */
														LEFT JOIN ( SELECT	it_id,
																								SUM(it_qty) AS CT_SUM
																				FROM		coto_cart
																				!WHERE_CART!
																				GROUP BY	it_id
														) CT ON (CT.it_id = GP.gp_id)
										WHERE		GP.gp_id = '!상품ID!'
";



?>