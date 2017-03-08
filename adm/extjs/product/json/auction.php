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


/* 경매상품목록 */
if($mode == 'auclist') {

	if($keyword) {
		$키워드 = " AND ";

		$arrkey = explode(' ',$keyword);
		for($i = 0; $i < count($arrkey); $i++) {
			$복수키워드 .= " ( GP.gp_id LIKE '%$arrkey[$i]%' OR GP.gp_name LIKE '%$arrkey[$i]%' OR GP.location LIKE '%$arrkey[$i]%' ) AND ";
		}
		$복수키워드 = "AND (".substr($복수키워드, 0, strlen($복수키워드)-4).")";
	}
	$AND_SQL .= $복수키워드;


	/* 경매상품목록 */
	$SELECT_SQL = "	SELECT	DISTINCT
													AL.it_id,
													GP.gp_id,
													GP.gp_name,
													GP.gp_img,
													AL.ac_code,			/*경매진행코드*/
													GP.ac_code AS gp_ac_code,	/*상품의 마지막 생성된 경매진행코드*/
													GP.ac_yn,													
													GP.ac_enddate,
													GP.ac_qty,
													GP.ac_startprice,
													GP.ac_buyprice,
													BID.MAX_BID_LAST_PRICE,	/*현재입찰가*/
													BID.MAX_BID_PRICE				/*최고입찰가*/
									FROM		auction_log AL
													LEFT JOIN g5_shop_group_purchase GP ON (GP.gp_id = AL.it_id)
													LEFT JOIN (	SELECT	ac_code,
																							it_id,
																							MAX(bid_last_price) AS MAX_BID_LAST_PRICE,
																							MAX(bid_price) AS MAX_BID_PRICE
																			FROM		auction_log
																			GROUP BY ac_code, it_id
													) BID ON (BID.ac_code = AL.ac_code AND BID.it_id = AL.it_id)
													
									WHERE		1=1
									$AND_SQL
	";

}

/* 입찰자 목록 */
else if($mode == 'bidlist') {
	
	if($keyword) {
		$키워드 = " AND ";

		$arrkey = explode(' ',$keyword);
		for($i = 0; $i < count($arrkey); $i++) {
			$복수키워드 .= " (GI.gpcode LIKE '%$arrkey[$i]%' OR GI.gpcode_name LIKE '%$arrkey[$i]%' ) AND ";
		}
		$복수키워드 = "AND (".substr($복수키워드, 0, strlen($복수키워드)-4).")";
	}
	$AND_SQL .= $복수키워드;

	if($ac_code) $AND_SQL .= " AND AL.ac_code = '$ac_code' ";
	if($it_id) $AND_SQL .= " AND AL.it_id = '$it_id' ";
	

	/* 주문금액 큰 순서대로 회원목록 추출 */
	$SELECT_SQL = "	SELECT	AL.no,
													AL.ac_code,					/*경매진행코드*/
													AL.it_id,						/*경매상품코드*/
													AL.it_name,
													AL.mb_id,						/*입찰회원계정*/
													MB.mb_name,
													MB.mb_nick,
													MB.mb_hp,
													AL.bid_qty,					/*입찰수량*/
													AL.bid_price,				/*입찰가격*/
													AL.bid_last_price,	/*현재 입찰가 기록*/
													CC.value AS bid_stats_name,
													AL.bid_stats,				/*낙찰여부*/
													RPAD(AL.bid_date,24,'0') AS bid_date,	/*등록일 밀리초까지*/
													AL.bid_dbdate				/*DB날짜*/
									FROM		auction_log AL
													LEFT JOIN comcode CC ON (CC.ctype = 'auctionlog' AND CC.col = 'bid_stats' AND CC.code = bid_stats)
													LEFT JOIN g5_member MB ON (MB.mb_id = AL.mb_id)
									WHERE		1=1
									$AND_SQL
	";
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

while($row = mysql_fetch_assoc($result)) {
	foreach($row as $key => $val) {
		$row[$key] = 개행문자삭제($val);
		if($key == 'gp_realprice') $row[$key] = CEIL($val / 100) * 100;
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