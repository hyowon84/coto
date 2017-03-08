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


/* 회원목록 */
if($mode == 'vipMbList') {

	/* 포트폴리오 회원 목록 추출 */
	$SELECT_SQL = "	SELECT	PM.pf_id,		/*포트폴리오 관리ID*/
													PM.name,			/*이름*/
													PM.hphone,		/*연락처*/
													PM.nick,			/*카페활동닉네임이 있을경우 입력*/
													PM.mb_id,		/*코투 계정이 있을경우 입력*/
													PM.reg_date,	/*등록일*/
													(GL.GL_PRICE+SL.SL_PRICE) AS TOTAL_PRICE,
													GL.GL_PRICE,
													GL.GL_GRAM,
													SL.SL_PRICE,
													SL.SL_GRAM
									FROM		portfolio_mb PM
													LEFT JOIN (	SELECT	v.pf_id,
																							v.metal_type,
																							SUM(v.TOTAL_GRAM) AS GL_GRAM,
																							SUM(v.TOTAL_OZ) AS GL_OZ,
																							SUM(v.TOTAL_DON) AS GL_DON,
																							SUM(v.TOTAL_PRICE) AS GL_PRICE
																			FROM		v_pfsummary v
																			GROUP BY v.pf_id, v.metal_type
													) GL ON (GL.pf_id = PM.pf_id AND GL.metal_type = 'GL')
													LEFT JOIN (	SELECT	v.pf_id,
																							v.metal_type,
																							SUM(v.TOTAL_GRAM) AS SL_GRAM,
																							SUM(v.TOTAL_OZ) AS SL_OZ,
																							SUM(v.TOTAL_DON) AS SL_DON,
																							SUM(v.TOTAL_PRICE) AS SL_PRICE
																			FROM		v_pfsummary v
																			GROUP BY v.pf_id, v.metal_type
													) SL ON (SL.pf_id = PM.pf_id AND SL.metal_type = 'SL')
									WHERE		1=1
									$AND_SQL
	";
}

/* 연결된 보유금속 목록 */
else if($mode == 'itemlist') {

	if($pf_id) {
		$AND_SQL .= " AND PD.pf_id = '$pf_id'	";
	}

	if($keyword) {
		$키워드 = " AND ";

		$arrkey = explode(' ',$keyword);
		for($i = 0; $i < count($arrkey); $i++) {
			$복수키워드 .= " (PD.LIKE '%$arrkey[$i]%' OR T.gp_name LIKE '%$arrkey[$i]%' ) AND ";
		}
		/*마지막에 붙은 AND단어 제거*/
		$복수키워드 = "AND (".substr($복수키워드, 0, strlen($복수키워드)-4).")";
	}
	$AND_SQL .= $복수키워드;


	$SELECT_SQL = "	SELECT	PD.d_id,						/*수정에 필요한 key값*/
													PD.pf_id,
													PD.metal_type,			/*금속유형 GL, SL, PT, PD ...*/
													PD.invest_type,			/*투자성향 투자,수집,대비*/
													PD.item_name,				/*품목명*/
													PD.gram,						/*무게 기본단위 gram*/
													(PD.gram / 3.75) AS don,							/*무게 기본단위 don*/
													(PD.gram / 31.1) AS oz,							/*무게 기본단위 oz*/
													PD.gram_per_price,		/*1g당 가격*/
													PD.gram_per_price * PD.gram AS BUYED_PRICE,
													CASE
														WHEN	PD.metal_type = 'Gold'	THEN
															(PD.gram / 31.1) * FP.GL * FP.USD
														WHEN	PD.metal_type = 'Silver'	THEN
															(PD.gram / 31.1) * FP.SL * FP.USD
														ELSE
															0
													END	CALC_PRICE,
													PD.reg_date
									FROM		portfolio_data PD
													,(	SELECT	*
															FROM		flow_price
															ORDER BY reg_date DESC
															LIMIT 1
													) FP
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