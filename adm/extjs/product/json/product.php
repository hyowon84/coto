<?
include "_common.php";
$json = array();
$data = array();



/* REMOTE SORT */
$sort = json_decode(str_replace('\"','"',$_GET[sort]),true);

for($i = 0; $i < count($sort); $i++) {
	if($i == 0) {
		$ORDER_BY = "ORDER BY ".$sort[$i][property]." ".$sort[$i][direction];
	}
	else {
		$ORDER_BY .= ",".$sort[$i][property]." ".$sort[$i][direction];
	}
}


$AND_SQL = "";


/* 공구목록 */
if($mode == 'gplist') {
	if($keyword) {
		$키워드 = " AND ";

		$arrkey = explode(' ',$keyword);
		for($i = 0; $i < count($arrkey); $i++) {
			$복수키워드 .= " (GI.gpcode LIKE '%$arrkey[$i]%' OR GI.gpcode_name LIKE '%$arrkey[$i]%' ) AND ";
		}
		$복수키워드 = "AND (".substr($복수키워드, 0, strlen($복수키워드)-4).")";
	}
	$AND_SQL .= $복수키워드;


	/* 주문금액 큰 순서대로 회원목록 추출 */
	$SELECT_SQL = "	SELECT	'QUICK' AS gpcode,
													'코투 빠른배송상품' AS gpcode_name,
													'00' AS stats,
													'상시진행' AS stats_name,
													'2016-01-01' AS start_date,
													'2999-12-31' AS end_date,
													'2999-12-31 00:00:00' AS reg_date,
													(	SELECT	COUNT(*)
														FROM		g5_shop_group_purchase GP
														WHERE		GP.ca_id LIKE 'CT%'
													) AS ITEM_CNT
									FROM		DUAL
									UNION ALL
									SELECT	'JAEGO' AS gpcode,
													'코투 재고칸' AS gpcode_name,
													'00' AS stats,
													'상시진행' AS stats_name,
													'2016-01-01' AS start_date,
													'2999-12-31' AS end_date,
													'2999-12-31 00:00:00' AS reg_date,
													(	SELECT	COUNT(*)
														FROM		g5_shop_group_purchase GP
														WHERE		GP.location != ''
													) AS ITEM_CNT
									FROM		DUAL
									UNION ALL
									SELECT	GI.gpcode,
													GI.gpcode_name,
													GI.stats,
													SN.value AS stats_name,
													GI.start_date,
													GI.end_date,
													GI.reg_date,
													GL.ITEM_CNT
									FROM		gp_info GI
													LEFT JOIN comcode SN ON (SN.ctype = 'clayorder' AND SN.col = 'stats' AND SN.code = GI.stats)
													LEFT JOIN (	SELECT	gpcode,
																							COUNT(*) AS ITEM_CNT
																			FROM		v_gpinfo_links
																			GROUP BY gpcode
													) GL ON (GL.gpcode = GI.gpcode)
									WHERE		GI.gpcode != 'QUICK'
									$AND_SQL
	";
}

/* 연결된 상품목록 */
else if($mode == 'itemlist') {

	if($keyword) {
		$키워드 = " AND ";

		$arrkey = explode(' ',$keyword);
		for($i = 0; $i < count($arrkey); $i++) {
			$복수키워드 .= " ( T.gp_id LIKE '%$arrkey[$i]%' OR T.gp_name LIKE '%$arrkey[$i]%' OR T.jaego_memo LIKE '%$arrkey[$i]%' OR T.location LIKE '%$arrkey[$i]%' ) AND ";
		}
		$복수키워드 = "AND (".substr($복수키워드, 0, strlen($복수키워드)-4).")";
	}
	$AND_SQL .= $복수키워드;


	/* 코투재고는 */
	if($gpcode == 'QUICK') {
		/* 카테고리 CT */
		$SELECT_SQL = "	SELECT	
														T.gp_id,
														T.ca_id,
														T.location,
														T.gp_img,
														T.gp_name,
														T.gp_update_time,
														T.gp_price_type,
														T.gp_metal_type,
														T.gp_metal_don,
														T.gp_spotprice_type,
														T.gp_spotprice,
														T.gp_order,
														T.gp_use,
														
														T.gp_price,
														T.gp_usdprice,
														T.gp_realprice,
														T.gp_price_org,
														T.gp_buy_max_qty,
														T.only_member,
														
														/*최초재고값 + 발주수량 - 실주문량*/
														T.real_jaego,												/*실재고*/
														T.jaego,														/*재고보정값*/
														T.jaego_memo,												/*재고메모*/
														IFNULL(T.CO_SUM,0) AS CO_SUM,				/*누적주문량*/
														T.IV_SUM,														/*누적발주량*/
														
														T.ac_yn,								/*경매진행여부*/
														T.ac_code,							/*경매진행코드*/
														T.ac_qty,								/*경매진행수량*/
														T.ac_enddate,						/*경매종료일자*/
														T.ac_startprice,				/*경매 시작가*/
														T.ac_buyprice,						/*경매 즉시구매가*/
														T.ebay_id
										FROM		$sql_admin_product
										WHERE		1=1
										$AND_SQL
		";
//		echo $SELECT_SQL;
	}
	else if($gpcode == 'JAEGO') {
			/* 카테고리 CT */
			$SELECT_SQL = "	SELECT	
														T.gp_id,
														T.ca_id,
														T.location,
														T.gp_img,
														T.gp_name,
														T.gp_update_time,
														T.gp_price_type,
														T.gp_metal_type,
														T.gp_metal_don,
														T.gp_spotprice_type,
														T.gp_spotprice,
														T.gp_order,
														T.gp_use,
														
														T.gp_price,
														T.gp_usdprice,
														T.gp_realprice,
														T.gp_price_org,
														T.gp_buy_max_qty,
														T.only_member,
														
														/*최초재고값 + 발주수량 - 실주문량*/
														T.real_jaego,												/*실재고*/
														T.jaego,														/*재고보정값*/
														T.jaego_memo,												/*재고메모*/
														IFNULL(T.CO_SUM,0) AS CO_SUM,				/*누적주문량*/
														T.IV_SUM,														/*누적발주량*/
														
														T.ac_yn,								/*경매진행여부*/
														T.ac_code,							/*경매진행코드*/
														T.ac_qty,								/*경매진행수량*/
														T.ac_enddate,						/*경매종료일자*/
														T.ac_startprice,				/*경매 시작가*/
														T.ac_buyprice,						/*경매 즉시구매가*/
														T.ebay_id
										FROM		$sql_admin_product
										WHERE		1=1
										AND			T.location != ''
										$AND_SQL
		";
//		echo $SELECT_SQL;
		}
	/* 경매상품은 */
	else if($gpcode == 'AUCTION') {
		
//		$sql_product = makeProductSql($gpcode);
//		$sql_auction_item = str_replace('#상품기본조건#', " AND ac_yn = 'Y' ", $sql_auction_item);
//		
//		/* 카테고리 CT */
//		$SELECT_SQL = "	$sql_auction_item
//										$AND_SQL
//		";
		/* 카테고리 CT */
		$SELECT_SQL = "	SELECT	
														T.gp_id,
														T.ca_id,
														T.location,
														T.gp_img,
														T.gp_name,
														
														T.gp_update_time,
														T.gp_price_type,
														T.gp_metal_type,
														T.gp_metal_don,
														T.gp_spotprice_type,
														T.gp_spotprice,
														T.gp_order,
														T.gp_use,
														
														T.gp_price,
														T.gp_usdprice,
														T.gp_realprice,
														T.gp_price_org,
														T.gp_buy_max_qty,
														
														/*최초재고값 + 발주수량 - 실주문량*/
														T.real_jaego,												/*실재고*/
														T.jaego,														/*재고보정값*/
														IFNULL(T.CO_SUM,0) AS CO_SUM,				/*누적주문량*/
														T.IV_SUM,														/*누적발주량*/
														
														T.ac_yn,								/*경매진행여부*/
														T.ac_code,							/*경매진행코드*/
														T.ac_qty,								/*경매진행수량*/
														T.ac_enddate,						/*경매종료일자*/
														T.ac_startprice,				/*경매 시작가*/
														T.ac_buyprice,						/*경매 즉시구매가*/
														T.ebay_id
										FROM		$sql_admin_product
										WHERE		1=1
										AND			ac_yn = 'Y'
										$AND_SQL
		";
	}
	else if($gpcode) {

		$sql_product = str_replace('#상품기본조건#'," AND GL.gpcode = '$gpcode' ", $sql_product);
		
		/* 선택된 공구의 상품목록 가져오기 */
		$SELECT_SQL = "	SELECT	DISTINCT
														T.ca_id,
														T.location,
														T.gp_id,
														T.gp_img,
														T.gp_name,
														T.gp_price,
														T.gp_usdprice,
														T.gp_order,
														T.gp_use,
														T.gp_price_org,
														T.gp_update_time,
														IFNULL(GO.OPT_CNT,0) AS OPT_CNT,
														T.gp_card,
														T.gp_price,
														T.gp_usdprice,
														T.gp_realprice,
														T.gp_price_org,
														T.gp_buy_max_qty,
														
														/*최초재고값 + 발주수량 - 실주문량*/
														T.real_jaego,												/*실재고*/
														T.jaego,														/*재고보정값*/
														T.jaego_memo,												/*재고메모*/
														IFNULL(T.ORDER_QTY,0) AS CO_SUM,		/*누적주문량*/
														IFNULL(T.RIV_QTY,0) AS IV_SUM,			/*누적발주량*/
														T.jaego,
														T.gp_price_type,
														T.gp_metal_type,
														T.gp_metal_don,
														T.gp_spotprice_type,
														T.gp_spotprice,
														T.ac_yn,								/*경매진행여부*/
														T.ac_code,							/*경매진행코드*/
														T.ac_qty,								/*경매진행수량*/
														T.ac_enddate,						/*경매종료일자*/
														T.ac_startprice,				/*경매 시작가*/
														T.ac_buyprice						/*경매 즉시구매가*/
										FROM		$sql_product
														LEFT JOIN (	SELECT	gp_id,
																								COUNT(*) AS OPT_CNT
																				FROM		g5_shop_group_purchase_option
																				GROUP BY gp_id
														) GO ON (GO.gp_id = T.gp_id)

														,(	SELECT	*
																FROM		flow_price
																ORDER BY	reg_date DESC
																LIMIT 1
														) FP
										WHERE		1=1
										$AND_SQL
		";
	}

//	echo $SELECT_SQL;
}
else if($mode == '') {
	$SELECT_SQL = "";
}

$total_count = mysql_num_rows(sql_query($SELECT_SQL));

/* 코드값 검색 */
$main_sql = "	$SELECT_SQL
							$ORDER_BY
							LIMIT $start, $limit
";
$result = sql_query($main_sql);
//echo $main_sql;


while($row = mysql_fetch_assoc($result)) {
	foreach($row as $key => $val) {
		$row[$key] = 개행문자삭제($val);
		if($key == 'gp_realprice') $row[$key] = CEIL($val / 100) * 100;
		if($key == 'gp_img') {
//			$imgthumb = getThumb($row);
//			$row['gp_img'] = $imgthumb[src];
		}
	}
	array_push($data, $row);
}

if($total_count > 0) {
	$json[total] = "$total_count";
	$json[success] = "true";
	$json[data] = $data;
} else {
	$json[total] = 0;
	$json[success] = "false";
}

$json_data = json_encode_unicode($json);
echo $json_data;
?>