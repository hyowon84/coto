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

$SELECT_SQL = "	SELECT	LT.no,
												LT.logtype,		/*로그유형 ( ex: clayorder )*/
												IFNULL(LT.it_id,LT.gr_id) AS key_id,
												LT.value,			/*변경된 값*/
												LT.reg_date,	/*변경된 날짜*/
												MB.mb_name,

												LT.gr_id,			/*GROUP_ID*/
												LT.pk_id,			/*PK_ID*/
												LT.it_id,			/*IT_ID*/
												LT.memo,			/*메모태그*/
												LT.admin_id,	/*로그를 남긴 관리자ID*/
												LT.col,				/*변경 항목*/
												CD.value AS code_name
								FROM		log_table LT
												LEFT JOIN g5_member MB ON (MB.mb_id = LT.admin_id)
												LEFT JOIN comcode CD ON (CD.ctype = 'clayorder' AND CD.col = 'stats' AND CD.code = LT.value)
								WHERE		LT.logtype = 'clayorder'
								AND			LT.gr_id = '$od_id'
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