<?php
include_once('./_common.php');

if($mode != 'jhw' && $_SERVER['REMOTE_ADDR'] != '221.146.206.90') {
	exit;
}


function process($data) {
	$ac_yn = $data[ac_yn];										/**/
	$ac_qty = $data[ac_qty];									/**/
	$ac_enddate = $data[ac_enddate];					/*경매종료일*/
	$ac_startprice = $data[ac_startprice];		/*시작가*/
	$ac_buyprice = $data[ac_buyprice];				/*즉시구매가*/

	$ca_id = $data[ca_id];
	$gp_id = $data[gp_id];
	$jaego = $data[jaego];
	$location = $data[location];
	$gp_name = $data[gp_name];
	$gp_card = $data[gp_card];
	$gp_price = $data[gp_price];
	$gp_usdprice = $data[gp_usdprice];
	$gp_price_org = $data[gp_price_org];
	$gp_price_type = $data[gp_price_type];
	$gp_spotprice_type = $data[gp_spotprice_type];
	$gp_spotprice = $data[gp_spotprice];
	$gp_metal_type = $data[gp_metal_type];
	$gp_metal_don = $data[gp_metal_don];
	$gp_buy_max_qty = $data[gp_buy_max_qty];
	$only_member = $data[only_member];
	$gp_order = $data[gp_order];
	$gp_use = $data[gp_use];
	$ebay_id = trim($data[ebay_id]);
	$real_jaego = $data[real_jaego];
	$gp_realprice = $data[gp_realprice];


	$시작가 = ceil(($gp_realprice / 5) / 1000) * 1000;
	
	$prev_sql = "	SELECT	*	FROM	g5_shop_group_purchase WHERE	gp_id = '$gp_id'";
	$prev = sql_fetch($prev_sql);

	//경매시작일때
	if($ac_yn == 'Y') {
		/* 경매 고유ID 생성 SQL  by. JHW */
		$seq_sql = "	SELECT	CONCAT(	'AC',
																DATE_FORMAT(now(),'%Y%m%d'),
																LPAD(COALESCE(	(	SELECT	MAX(SUBSTR(ac_code,11,4))
																									FROM		g5_shop_group_purchase
																									WHERE		ac_code LIKE CONCAT('%',DATE_FORMAT(now(),'%Y%m%d'),'%')
																									ORDER BY ac_code DESC
																								)
																,'0000') +1,4,'0')
												)	AS ac_code
									FROM		DUAL
		";
		list($ac_code) = mysql_fetch_array(sql_query($seq_sql));
	}else {
		$ac_code = $prev[ac_code];
	}

	$이전데이터 = getDataGpJaego($gp_id);

	/* 상품정보 수정 */
	$common_sql = "	UPDATE	g5_shop_group_purchase	SET
														ac_yn		= 'Y',										/*경매진행여부*/
														ac_code	= '$ac_code',								/*경매진행코드*/
														ac_qty	= '1',									/*경매진행수량*/
														ac_enddate = '$ac_enddate',					/*경매종료일자*/
														ac_startprice = '$시작가',		/*경매 시작가*/
														ac_buyprice = '$ac_buyprice',				/*경매 즉시구매가*/
														gp_update_time = now()
									WHERE		gp_id = '$gp_id'
	";
	sql_query($common_sql);
	
	echo $common_sql;
	
	$현재데이터 = getDataGpJaego($gp_id);
	db_log($common_sql,'g5_shop_group_purchase','경매자동등록',$이전데이터,$현재데이터);
}


//경매종료되고 재고가 2개 이상인 상품들 추출
$sql = " SELECT		T.gp_id,
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
									T.ac_yn,
									T.ac_code,
									T.ac_qty,
									T.ac_enddate,
									T.ac_startprice,
									T.ac_buyprice,
									
									T.real_jaego,
									T.gp_realprice
					FROM		
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
														END gp_realprice
										FROM			(		SELECT	gp_id,	
																					ca_id,	
																					ca_id2,	/*2차 분류*/
																					ca_id3,	/*3차 분류*/
																					location,	
																					event_yn,	/*이벤트 진행 상품 유(Y)/무(N)*/
																					b2b_yn,	
																					gp_name,	
																					gp_site,	/*상품 원본URL*/
																					gp_img,	
																					gp_explan,	
																					gp_360img,	
																					gp_objective_price,	
																					jaego,	/*상품초기재고*/
																					IFNULL(GP.jaego,0) + IFNULL(RIV.RIV_QTY,0) - IFNULL(CO.ORDER_QTY,0) AS real_jaego,
																					gp_have_qty,	
																					gp_buy_min_qty,			/*최소구매수량*/
																					gp_buy_max_qty,			/*최대구매수량*/
																					only_member,
																					gp_charge,					/*수수료*/
																					gp_duty,						/*관세*/
																					gp_use,							/*판매유무*/
																					gp_order,	
																					gp_stock,	
																					gp_time,	
																					gp_update_time,	
																					gp_price,	/*코투현금가*/
																					gp_usdprice,				/*상품 달러가격*/
																					gp_price_org,				/*코투 매입가($)*/
																					gp_card,						/*카드가 노출 여부*/
																					gp_card_price,			/*코투카드가*/
																					gp_price_type,			/*고정형 / 실시간형*/
																					gp_spotprice_type,	/*스팟시세유형 , %, 원*/
																					gp_spotprice,				/*스팟시세값*/
																					gp_metal_type,			/*GL, SL, PT, PD,ETC*/
																					gp_metal_don,				/*oz*/
																					gp_metal_etc_price,	
																					gp_sc_method,				/*배송유형*/
																					gp_sc_price,				/*배송비*/
																					it_type,						/*상품유형아이콘*/
																					gp_type1,						/*히트*/
																					gp_type2,						/*추천*/
																					gp_type3,						/*신상품*/
																					gp_type4,						/*인기*/
																					gp_type5,						/*할인*/
																					gp_type6,						/*경매*/
																					admin_memo,	
																					IF(NOW() > ac_enddate, 'N',ac_yn) AS ac_yn,		/*경매진행여부*/
																					ac_code,						/*경매진행코드*/
																					ac_enddate,					/*경매마감일*/
																					ac_delay_date,			/*연장최대시간*/
																					ac_delay_cnt,				/*경매마감일 연장횟수*/
																					ac_qty,							/*경매가능수량*/
																					ac_startprice,			/*경매시작가*/
																					ac_buyprice					/*경매즉시구매가*/
																	FROM		g5_shop_group_purchase GP
																					/* 총주문수량 */
																					LEFT JOIN ( SELECT	it_id,
																															SUM(it_qty) AS ORDER_QTY
																											FROM		clay_order
																											WHERE		stats <= 60		/* 취소건 제외, 모든 신청수량 */
																											GROUP BY it_id
																					) CO ON (CO.it_id = GP.gp_id)
																					
																					/*실제발주수량, 가발주포함하는걸로*/
																					LEFT JOIN (	SELECT	iv_it_id,
																															SUM(iv_qty) AS RIV_QTY
																											FROM		invoice_item
																											WHERE		1=1
																											AND			iv_stats >= 00
																											GROUP BY iv_it_id				
																					)	RIV ON (RIV.iv_it_id = GP.gp_id)
																	WHERE		1=1
																	AND			GP.gp_use = '1'
																	AND			GP.ca_id LIKE 'CT%'
																	AND			GP.ac_code LIKE 'AC%'
																	AND			GP.ac_enddate < NOW()
																	AND			IFNULL(GP.jaego,0) + IFNULL(RIV.RIV_QTY,0) - IFNULL(CO.ORDER_QTY,0) >= 0	
																	ORDER BY RAND()
																	LIMIT 20
											) GP
											
											LEFT JOIN	g5_shop_group_purchase_option PO ON (PO.gp_id = GP.gp_id AND po_num = 0)

											,(	SELECT	*
													FROM		flow_price
													ORDER BY	reg_date DESC
													LIMIT 1
											) FP
									) T
					
					WHERE		1=1
					AND			T.gp_realprice > 0
";
$result = sql_query($sql);

$i = 0;
$dd = 6;
while($arr = mysql_fetch_array($result)) {
	$arr[ac_yn] = 'Y';
	list($y,$m,$d) = explode(" ",date("Y m d"));
	$h = 24 - $i;
	$arr[ac_enddate] = date("Y-m-d H:i:s",mktime($h,0,0,$m,$d+$dd,$y));
	process($arr);

	$i++;
	if($h == 17) {
		$i = 0;
		$dd--;
	}
}



if($result) {
	$json[success] = "true";
	$json[message] = '상품이 수정되었습니다';
} else {
	$json[success] = "false";
	$json[message] = '상품이 수정되지 않았습니다. 관리자에게 문의바랍니다.';
}

$json_data = json_encode_unicode($json);
echo $json_data;
?>