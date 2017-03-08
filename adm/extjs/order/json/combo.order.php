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

/*주문상태*/
if($mode == 'stats') {
	$col = "stats";
}
/*배송유형*/
else if($mode == 'delivery_type') {
	$col = "delivery_type";
}
else if($mode == '') {
	$SELECT_SQL = "";
}

$SELECT_SQL = "	SELECT	'' AS value,
												'-전체-' AS name,
												'00' AS `order`
								FROM		DUAL
								UNION ALL
								SELECT	code AS value,
												value AS name,
												`order`
								FROM		comcode
								WHERE		1=1
								AND			ctype = 'clayorder'
								AND			col		= '$col'
								$AND_SQL
";

$total_count = mysql_num_rows(sql_query($SELECT_SQL));

/* 코드값 검색 */
$main_sql = "	$SELECT_SQL
							$ORDER_BY
							#LIMIT $start, $limit
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