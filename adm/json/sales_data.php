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


/* 월간 매출/지출 */
if($mode == 'month') {

	$date = date('Y-m', strtotime('-12 month', G5_SERVER_TIME));

	/* 포트폴리오 회원 목록 추출 */
	$SELECT_SQL = "	SELECT	SUBSTRING(CL.od_date, 1, 7) AS colName,
													SUM(CL.it_org_price * CL.it_qty) as data1,
													IFNULL(CC.price,0) AS data2
									FROM		clay_order CL
													LEFT JOIN (	SELECT	T.tr_date,
																							SUM(T.output_price) AS price
																			FROM		(	SELECT	DATE_FORMAT(B.tr_date,'%Y-%m') AS tr_date,
																												B.output_price
																								FROM		bank_db B
																								WHERE		B.account_name = '지출통장'
																								AND			B.output_price > 0
																								AND			B.input_price = 0
																							) T
																			GROUP BY T.tr_date
													) CC ON ( CC.tr_date = SUBSTRING(CL.od_date, 1, 7) )
									WHERE		1=1
									AND			CL.stats NOT IN (99)
									AND			SUBSTRING(CL.od_date, 1, 7) >= '$date'
									GROUP BY SUBSTRING(od_date, 1, 7)
	";
}
/* 주간 매출/지출 */
else if($mode == '12days') {

	$date = date('Y-m-d', strtotime('-15 days', G5_SERVER_TIME));

	/* 포트폴리오 회원 목록 추출 */
	$SELECT_SQL = "	SELECT	SUBSTRING(CL.od_date, 6, 5) AS colName,
													SUM(CL.it_org_price * CL.it_qty) as data1,
													IFNULL(CC.price,0) AS data2
									FROM		clay_order CL
													LEFT JOIN (	SELECT	T.tr_date,
																							SUM(T.output_price) AS price
																			FROM		(	SELECT	B.tr_date,
																												B.output_price
																								FROM		bank_db B
																								WHERE		B.account_name = '지출통장'
																								AND			B.output_price > 0
																								AND			B.input_price = 0
																							) T
																			GROUP BY T.tr_date
													) CC ON ( CC.tr_date = SUBSTRING(CL.od_date, 1, 10) )
									WHERE		1=1
									AND			CL.stats NOT IN (99)
									AND			SUBSTRING(CL.od_date, 1, 10) >= '$date'
									GROUP BY SUBSTRING(od_date, 1, 10)
	";
}
/* 주문상태별 건수, 주문금액 */
else if($mode == 'statscnt') {
	$SELECT_SQL = "	SELECT	CC.value AS stats_nm,
													CL.stats,
													COUNT(*) AS STATS_CNT,
													SUM(CL.it_qty) AS TOTAL_QTY,
													SUM(CL.it_org_price * CL.it_qty) AS TOTAL_PRICE
									FROM		clay_order CL
													LEFT JOIN comcode CC ON (CC.ctype = 'clayorder' AND CC.col = 'stats' AND CC.code = CL.stats)
									WHERE		CL.stats IN (00,10,20,25,30,35,70,75,80)
									GROUP BY CL.stats
	";
}
/* 발주 */
else if($mode == 'oldinvoice') {
	$SELECT_SQL = "	SELECT	II.gpcode,
													GI.gpcode_name,
													II.iv_id,
													II.iv_it_name,
													II.iv_qty,
													II.iv_dealer,
													II.iv_dealer_price,
													II.iv_dealer_worldprice
									FROM		invoice_item II
													LEFT JOIN gp_info GI ON (GI.gpcode = II.gpcode)
									WHERE		II.iv_stats IN ('00')
									ORDER BY II.reg_date ASC
	";
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