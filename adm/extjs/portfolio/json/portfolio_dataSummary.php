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


/* 금속 합계 */
if($mode == 'summary') {
	if($pf_id) {
		$AND_SQL .= "	AND	PD.pf_id = '$pf_id'	";
	}

	/* 포트폴리오 회원 목록 추출 */
	$SELECT_SQL = "	SELECT	CASE
														WHEN	PD.metal_type = 'Gold' THEN
															'Gold'
														WHEN	PD.metal_type = 'Silver' THEN
															'Silver'
														ELSE
															''
													END metal_type_nm,
													IV.value AS invest_type_nm,
													SUM(PD.gram) AS TOTAL_GRAM,
													SUM(PD.gram / 31.1) AS TOTAL_OZ,
													SUM(PD.gram / 3.75) AS TOTAL_DON,
													SUM(PD.gram_per_price * PD.gram) AS TOTAL_PRICE
									FROM		portfolio_data PD
													LEFT JOIN comcode IV ON (IV.ctype = 'portfolio' AND IV.col = 'invest_type' AND IV.code = PD.invest_type)
									WHERE		1=1
									$AND_SQL
									GROUP BY PD.metal_type, PD.invest_type

	";
}

/* 연결된 상품목록 */
else if($mode == 'itemlist') {

	if($keyword) {
		$키워드 = " AND ";

// 		$arrkey = explode(' ',$keyword);
// 		for($i = 0; $i < count($arrkey); $i++) {
// 			$복수키워드 .= " (T.gp_id LIKE '%$arrkey[$i]%' OR T.gp_name LIKE '%$arrkey[$i]%' ) AND ";
// 		}
// 		$복수키워드 = "AND (".substr($복수키워드, 0, strlen($복수키워드)-4).")";
	}
	$AND_SQL .= $복수키워드;


	$SELECT_SQL = "	SELECT	PD.d_id,					/*수정에 필요한 key값*/
													PD.pf_id,
													PD.metal_type,		/*금속유형 GL, SL, PT, PD ...*/
													PD.invest_type,	/*투자성향 투자,수집,대비*/
													PD.item_name,		/*품목명*/
													PD.oz						/*무게 기본단위 oz*/
									FROM		portfolio_data PD
									WHERE		1=1
									$AND_SQL
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