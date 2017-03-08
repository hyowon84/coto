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

if($pf_id) {
	$AND_SQL .= "	AND	PFD.pf_id = '$pf_id'	";
}

//파이형 차트 : 성향별 예상 자금분배
if($mode == 'invest') {
	if($pf_id) {
		$AND_SQL = "	AND	PFM.pf_id = '$pf_id'	";
	}
	$SELECT_SQL = "/* 예상 투자 규모*/
									SELECT	PFD.pf_id,
													'초기구성자금' AS colName,
													SUM(PFD.gram * PFD.gram_per_price) AS data1,
													SUM(PFD.gram * PFD.gram_per_price) / (IFNULL(SUM(PFD.gram * PFD.gram_per_price),0) + IFNULL(PFM.surplus_fund,0)) * 100 AS data2
									FROM		portfolio_mb PFM
													LEFT JOIN portfolio_data PFD ON (PFM.pf_id = PFD.pf_id)
									WHERE		1=1
									$AND_SQL
									GROUP BY PFD.pf_id
									UNION ALL
									SELECT	PFD.pf_id,
													'추가예정자금' AS colName,
													 PFM.surplus_fund AS data1,
													 IFNULL(PFM.surplus_fund,0) / (IFNULL(SUM(PFD.gram * PFD.gram_per_price),0) + IFNULL(PFM.surplus_fund,0)) * 100 AS data2
									FROM		portfolio_mb PFM
													LEFT JOIN portfolio_data PFD ON (PFM.pf_id = PFD.pf_id)

									WHERE		1=1
									$AND_SQL
									GROUP BY PFD.pf_id
	";
}
//파이형 차트 : 금/은 비중
else if($mode == 'MetalPer') {
	$SELECT_SQL = "/* 금/은 비중 */
								SELECT	PFD.pf_id,
												PFD.metal_type AS colName,
												SUM(PFD.gram * PFD.gram_per_price) / PFT.TOTAL_PRICE * 100 AS data1	#금속 전체 비중(%)
								FROM		portfolio_data PFD
												/* TOTAL SUM */
												LEFT JOIN (	SELECT	pf_id,
																						SUM(gram) AS TOTAL_GRAM,
																						SUM(gram * gram_per_price) AS TOTAL_PRICE
																		FROM		portfolio_data
																		GROUP BY pf_id
												) PFT ON (PFT.pf_id = PFD.pf_id)
								WHERE		1=1
								$AND_SQL
								GROUP BY PFD.pf_id, PFD.metal_type
	";
}
//파이형 차트 : 금의 자금분배액 or 은 자금분배액
else if($mode == 'MetalFundPer') {
	$SELECT_SQL = "/* 금 또는 은 비중 */
								SELECT	PFD.pf_id,
												PFD.metal_type,
												PFD.invest_type AS colName,
												SUM(PFD.gram * PFD.gram_per_price) / PFT.TOTAL_PRICE * 100 AS data1	#금속 전체 비중(%)
								FROM		portfolio_data PFD
												/* TOTAL SUM */
												LEFT JOIN (	SELECT	pf_id,
																						metal_type,
																						SUM(gram) AS TOTAL_GRAM,
																						SUM(gram * gram_per_price) AS TOTAL_PRICE
																		FROM		portfolio_data
																		WHERE		1=1
																		GROUP BY pf_id,metal_type
												) PFT ON (PFT.pf_id = PFD.pf_id AND PFT.metal_type = PFD.metal_type)
												LEFT JOIN portfolio_invest PFI ON (PFI.pf_id = PFD.pf_id AND PFI.invest_type = PFD.invest_type)
								WHERE		PFD.metal_type = '$metal_type'
								$AND_SQL
								GROUP BY PFD.pf_id, PFD.metal_type, PFD.invest_type
								ORDER BY PFI.pf_id, PFI.sortNo ASC
	";
}

//막대형 차트 : 금 or 은 투자금액
else if($mode == 'MetalInvestPrice') {
	$SELECT_SQL = "/* 금속 투자성향 */
								SELECT	PFD.pf_id,
												PFD.invest_type AS colName,
												IFNULL(GT.TOTAL_PRICE,0) AS data1,
												IFNULL(ST.TOTAL_PRICE,0) AS data2
								FROM		portfolio_data PFD
												LEFT JOIN portfolio_invest PFI ON (PFI.pf_id = PFD.pf_id AND PFI.invest_type = PFD.invest_type)
												LEFT JOIN (	SELECT	pf_id,
																						invest_type,
																						metal_type,
																						SUM(gram * gram_per_price) AS TOTAL_PRICE
																		FROM		portfolio_data
																		WHERE		metal_type = 'Gold'
																		GROUP BY pf_id, invest_type
												) GT ON (GT.pf_id = PFD.pf_id AND GT.invest_type = PFD.invest_type)
												LEFT JOIN (	SELECT	pf_id,
																						metal_type,
																						invest_type,
																						SUM(gram * gram_per_price) AS TOTAL_PRICE
																		FROM		portfolio_data
																		WHERE		metal_type = 'Silver'
																		GROUP BY pf_id, invest_type
												) ST ON (ST.pf_id = PFD.pf_id AND ST.invest_type = PFD.invest_type)
								WHERE		1=1
								$AND_SQL
								GROUP BY PFD.pf_id, PFD.invest_type
								ORDER BY PFD.pf_id, PFI.sortNo ASC
	";
}
//막대형 차트 : 금 or 은 투자금액
else if($mode == 'MetalInvestPer') {
	$SELECT_SQL = "/* 금속 투자성향 예상자금분배 백분율 */
									SELECT	PFD.pf_id,
													PFD.invest_type AS colName,
													GT.target_per AS data1,
													ST.target_per AS data2
									FROM	portfolio_invest PFD
												LEFT JOIN portfolio_investdtl GT ON (GT.pf_id = PFD.pf_id AND GT.invest_type = PFD.invest_type AND GT.metal_type = 'Gold')
												LEFT JOIN portfolio_investdtl ST ON (ST.pf_id = PFD.pf_id AND ST.invest_type = PFD.invest_type AND ST.metal_type = 'Silver')
									WHERE	1=1
									$AND_SQL
									ORDER BY PFD.pf_id, PFD.sortNo ASC
	";
}
//가로 막대형 차트 : 금 or 은 목표금액, 달성금액
else if($mode == 'TargetAchieve') {
	$SELECT_SQL = "/* 목표금액, 달성금액 */
									SELECT	PFD.pf_id,
													PFD.sortNo,
													CONCAT(PFD.invest_type,'(',PFD.metal_type,')') AS colName,
													IFNULL(T.TARGET_PRICE,0) * (PFD.target_per / 100) AS data1,	#목표금액
													T.ACH_PRICE AS data2 #달성금액
									FROM		portfolio_investdtl PFD
													LEFT JOIN (	SELECT	PFI.pf_id,
																							PFI.invest_type,
																							PFI.target_per,
																							IFNULL(PFT.TOTAL_PRICE * IFNULL(PFI.target_per / 100,1),0) + (IFNULL(PFM.surplus_fund,0) * IFNULL(PFI.target_per / 100,1)) AS TARGET_PRICE,
																							SUM(PFD.gram * PFD.gram_per_price) / (PFT.TOTAL_PRICE * IFNULL(PFI.target_per / 100,1) + (IFNULL(PFM.surplus_fund,0) * IFNULL(PFI.target_per / 100,1))) * 100 AS ACH_PER,
																							SUM(PFD.gram * PFD.gram_per_price) AS ACH_PRICE,
																							CC.`order` AS sortNo
																			FROM		portfolio_invest PFI
																							LEFT JOIN portfolio_data PFD ON (PFD.pf_id = PFI.pf_id AND PFD.invest_type = PFI.invest_type)

																							/*초기자본 토탈금액 구하기*/
																							LEFT JOIN (	SELECT	pf_id,
																																	SUM(gram * gram_per_price) AS TOTAL_PRICE
																													FROM		portfolio_data
																													GROUP BY pf_id
																							) PFT ON (PFT.pf_id = PFI.pf_id)

																							/*여유자금 값 추출*/
																							LEFT JOIN portfolio_mb PFM ON (PFM.pf_id = PFI.pf_id)

																							LEFT JOIN comcode CC ON (CC.ctype = 'portfolio' AND CC.col = 'invest_type' AND CC.code = PFI.invest_type)
																			GROUP BY PFI.pf_id, PFI.invest_type

													) T ON (T.pf_id = PFD.pf_id AND T.invest_type = PFD.invest_type)
									WHERE		1=1
									$AND_SQL
									ORDER BY PFD.pf_id, PFD.sortNo ASC
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