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


$SELECT_SQL = "	SELECT	'확인' AS BANK_STAT,
												'$od_id' AS od_id,
												BD.tr_date,
												BD.tr_time,
												BD.input_price,
												BD.trader_name,
												BD.admin_link,
												BD.admin_memo
								FROM		bank_db BD
								WHERE		BD.admin_link LIKE '%$od_id%'

								UNION ALL

								SELECT	'미확인' AS BANK_STAT,
												'$od_id' AS od_id,
												BD.tr_date,
												BD.tr_time,
												BD.input_price,
												BD.trader_name,
												BD.admin_link,
												BD.admin_memo
								FROM		bank_db BD
												,(	SELECT	CL.od_id,
																		CI.name,
																		CI.clay_id,
																		CI.receipt_name,
																		SUM(CL.it_org_price * CL.it_qty) AS PRD_PRICE,
																		SUM(CL.it_org_price * CL.it_qty) + CI.delivery_price AS TOTAL_PRICE
														FROM		clay_order CL
																		LEFT JOIN clay_order_info CI ON (CI.od_id = CL.od_id)
														WHERE		CL.od_id LIKE '%$od_id%'
														GROUP BY CL.od_id
												) CL
								WHERE		1=1

								AND			CL.TOTAL_PRICE = BD.input_price		/*배송비까지 금액일치해야됨*/
								AND			(	BD.trader_name LIKE '%CL.name%' OR	BD.trader_name LIKE '%CL.receipt_name%'	OR	BD.trader_name LIKE '%CL.clay_id%')
								AND			BD.admin_link NOT LIKE '%$od_id%'
								AND			(	BD.admin_link IS NULL	OR BD.admin_link = '' )
								/* 주문한 시간이후 입금건만 인정 */
								AND			BD.tr_date >= DATE_FORMAT(DATE_ADD(NOW(),INTERVAL -2 WEEK ),'%Y-%m-%d')
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