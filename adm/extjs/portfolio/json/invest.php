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

//예상투자규모
if($mode == 'expectInvest') {
	if($pf_id) {
		$AND_SQL = "	AND	PFM.pf_id = '$pf_id'	";
	}

	$SELECT_SQL = "/* 예상 투자 규모*/
									SELECT	PFM.pf_id,
													'초기 자금' AS fund_type,
													'금/은' AS metal_type,
													PFD.EXPECT_PRICE,
													PFD.EXPECT_PRICE / (PFD.EXPECT_PRICE + IFNULL(PFM.surplus_fund,0)) * 100 AS EXPECT_PER
									FROM		portfolio_mb PFM
													LEFT JOIN (	SELECT	pf_id,
																							SUM(gram * gram_per_price) AS EXPECT_PRICE
																			FROM		portfolio_data
																			GROUP BY pf_id
													) PFD ON (PFD.pf_id = PFM.pf_id)
									WHERE		1=1
									$AND_SQL
									UNION ALL
									SELECT	PFM.pf_id,
													'예정 자금' AS fund_type,
													'금/은' AS metal_type,
													 PFM.surplus_fund AS EXPECT_PRICE,
													 IFNULL(PFM.surplus_fund,0) / (PFD.EXPECT_PRICE + IFNULL(PFM.surplus_fund,0)) * 100 AS EXPECT_PER
									FROM		portfolio_mb PFM
													LEFT JOIN (	SELECT	pf_id,
																							SUM(gram * gram_per_price) AS EXPECT_PRICE
																			FROM		portfolio_data
																			GROUP BY pf_id
													) PFD ON (PFD.pf_id = PFM.pf_id)
									WHERE		1=1
									$AND_SQL
	";
}
//초기구성자금
else if($mode == 'beginFundSet') {

	$SELECT_SQL = "/* 초기 구성자금 */
								SELECT	PFD.pf_id,
												'보유중인 초기구성자금' AS fund_type,
												PFD.metal_type,
												SUM(PFD.gram * PFD.gram_per_price) AS EXPECT_PRICE,
												SUM(PFD.gram * PFD.gram_per_price) / PFT.TOTAL_PRICE * 100 AS EXPECT_PER
								FROM		portfolio_data PFD
												LEFT JOIN (	SELECT	pf_id,
																						SUM(gram * gram_per_price) AS TOTAL_PRICE
																		FROM		portfolio_data
																		GROUP BY pf_id
												) PFT ON (PFT.pf_id = PFD.pf_id)
												LEFT JOIN portfolio_mb PFM ON (PFM.pf_id = PFD.pf_id)
								WHERE		1=1
								$AND_SQL
								GROUP BY PFD.pf_id, PFD.metal_type
	";

}
//추가매수자금
else if($mode == 'beginFundBuy') {
	if($pf_id) {
		$AND_SQL = "	AND	PFM.pf_id = '$pf_id'	";
	}

	$SELECT_SQL = "/* 추가예정자금 */
								SELECT	PFM.pf_id,
												'추가매수예정자금' AS fund_type,
												PFM.surplus_fund,
												IFNULL(PFM.surplus_year,0) AS surplus_year
								FROM		portfolio_mb PFM
								WHERE		1=1
								$AND_SQL
	";
}
/* 투자성향 목표예상수치%, 달성수치% */
else if($mode == 'invest' || $mode == 'achinvest') {
	if($pf_id) {
		$AND_SQL = "	AND	PFI.pf_id = '$pf_id'	";
	}

	/* 투자성향 목표설정 예상치, 성향별 목표 달성수치 */
	$SELECT_SQL = "	/* 성향별 목표 예상 수치 통계 */
									SELECT	PFI.pf_id,
													PFI.invest_type,
													IFNULL(PFI.target_per,0) AS target_per,
													IFNULL(PFT.TOTAL_PRICE * IFNULL(PFI.target_per / 100,1),0) + (IFNULL(PFM.surplus_fund,0) * IFNULL(PFI.target_per / 100,1)) AS TARGET_PRICE,
													SUM(PFD.gram * PFD.gram_per_price) / (PFT.TOTAL_PRICE * IFNULL(PFI.target_per / 100,1) + (IFNULL(PFM.surplus_fund,0) * IFNULL(PFI.target_per / 100,1))) * 100 AS ACH_PER,
													SUM(PFD.gram * PFD.gram_per_price) AS ACH_PRICE
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
													LEFT JOIN comcode CC ON (CC.ctype = 'portfolio' AND CC.col = 'invest_type' AND CC.code = PFD.invest_type)
									WHERE		1=1
									$AND_SQL
									GROUP BY PFI.pf_id,PFI.invest_type
									ORDER BY PFI.pf_id, PFI.sortNo ASC
	";
}

/* 금/은 포지션 목표 예상금액  */
else if($mode == 'investdtl') {

	if($pf_id) {
		$AND_SQL = "	AND	P.pf_id = '$pf_id'	";
	}

	/* 포트폴리오 회원 목록 추출 */
	$SELECT_SQL = "	SELECT	P.pf_id,
													P.invest_type,
													P.metal_type,
													P.target_per,
													P.sortNo,
													T.TARGET_PRICE,
													IFNULL(T.TARGET_PRICE,0) * (P.target_per / 100) AS TARGET_PRICE
									FROM		portfolio_investdtl P
													LEFT JOIN (	SELECT	PFI.pf_id,
																							PFI.invest_type,
																							PFI.target_per,
																							IFNULL(PFT.TOTAL_PRICE,0) * IFNULL(PFI.target_per / 100,1) + (IFNULL(PFM.surplus_fund,0) * IFNULL(PFI.target_per / 100,1)) AS TARGET_PRICE,

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
																			GROUP BY PFI.pf_id,PFI.invest_type

													) T ON (T.pf_id = P.pf_id AND T.invest_type = P.invest_type)
									WHERE		1=1
									$AND_SQL
									ORDER BY P.pf_id, P.sortNo ASC
	";

}

/* 금/은 현재 목표 달성금액  */
else if($mode == 'achinvestdtl') {

	if($pf_id) {
		$AND_SQL = "	AND	P1.pf_id = '$pf_id'	";
	}

	/* 포트폴리오 회원 목록 추출 */
	$SELECT_SQL = "	SELECT	P1.pf_id,
													P1.invest_type,
													P1.metal_type,
													(P1.ACH_PRICE / P2.TARGET_PRICE * 100) AS ACH_PER,
													P1.ACH_PRICE
									FROM		(	SELECT	PFI.pf_id,
																		PFI.invest_type,
																		PFI.metal_type,
																		IFNULL(PFD.ACH_PRICE,0) AS ACH_PRICE
														FROM		portfolio_investdtl PFI
																		LEFT JOIN (	SELECT	pf_id,
																												invest_type,
																												metal_type,
																												SUM(gram * gram_per_price) AS ACH_PRICE
																								FROM		portfolio_data
																								GROUP BY pf_id, invest_type, metal_type
																		) PFD ON (PFI.pf_id = PFD.pf_id AND PFI.invest_type = PFD.invest_type AND PFI.metal_type = PFD.metal_type)
														WHERE		1=1
													) P1

													LEFT JOIN (	SELECT	P.pf_id,
																							P.invest_type,
																							P.metal_type,
																							P.target_per,
																							P.sortNo,
																							T.TARGET_PRICE * (P.target_per / 100) AS TARGET_PRICE
																			FROM		portfolio_investdtl P
																							LEFT JOIN (	SELECT	PFI.pf_id,
																																	PFI.invest_type,
																																	PFI.target_per,
																																	PFT.TOTAL_PRICE * IFNULL(PFI.target_per / 100,1) + (IFNULL(PFM.surplus_fund,0) * IFNULL(PFI.target_per / 100,1)) AS TARGET_PRICE,
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
																													GROUP BY PFI.pf_id,PFI.invest_type

																							) T ON (T.pf_id = P.pf_id AND T.invest_type = P.invest_type)
																			WHERE		1=1

													) P2 ON (P2.pf_id = P1.pf_id AND P2.invest_type = P1.invest_type AND P2.metal_type = P1.metal_type)
									WHERE	1=1
									$AND_SQL
									ORDER BY P1.pf_id, P2.sortNo ASC
	";
}

/* 하단 - 추가 매수 자금 테이블표 */
else if($mode == 'estimate') {
	$SELECT_SQL = "/* 하단 - 금속 자금 평가 테이블표  */
									SELECT		T.pf_id,
														T.metal_type,
														T.TOTAL_GRAM,
														T.ESTIMATE_NOW,
														T.ESTIMATE_BUYED,
														T.ESTIMATE_NOW - T.ESTIMATE_BUYED AS ESTIMATE_PROFIT,
														(T.ESTIMATE_NOW - T.ESTIMATE_BUYED) / T.ESTIMATE_BUYED * 100 AS ESTIMATE_PROFIT_PER,

														T.flowprice_now,
														T.flowprice_buyed,
														T.flowprice_now - T.flowprice_buyed AS flowprice_profit,
														(T.flowprice_now - T.flowprice_buyed) / T.flowprice_now * 100 AS flowprice_profit_per
									FROM		(
														SELECT	PFD.pf_id,
																		PFD.metal_type,
																		SUM(PFD.gram) AS TOTAL_GRAM,

																		CASE
																			WHEN	PFD.metal_type = 'Gold' THEN
																				SUM(PFD.gram * FP.GL / 3.75)
																			WHEN	PFD.metal_type = 'Silver' THEN
																				SUM(PFD.gram * FP.SL / 3.75)
																			ELSE
																				0
																		END ESTIMATE_NOW,	#현재평가금액

																		SUM(PFD.gram * PFD.gram_per_price) AS ESTIMATE_BUYED,	#구매평가금액

																		CASE
																			WHEN	PFD.metal_type = 'Gold' THEN
																				FP.GL
																			WHEN	PFD.metal_type = 'Silver' THEN
																				FP.SL
																			ELSE
																				0
																		END flowprice_now,	#현재평가금액
																		SUM(PFD.gram * PFD.gram_per_price) / SUM(PFD.gram) * 3.75 AS flowprice_buyed 	#구매당시 시세

														FROM		portfolio_data PFD
																		LEFT JOIN (	SELECT	pf_id,
																												metal_type,
																												SUM(gram * gram_per_price) AS TOTAL_PRICE
																								FROM		portfolio_data
																								GROUP BY pf_id, metal_type
																		) PFT ON (PFT.pf_id = PFD.pf_id AND PFT.metal_type = PFD.metal_type)
																		LEFT JOIN portfolio_mb PFM ON (PFM.pf_id = PFD.pf_id)
																		,(	SELECT	*
																				FROM		flow_price_exg
																				ORDER BY reg_date DESC
																				LIMIT 1
																		) FP
														WHERE		1=1
														$AND_SQL
														GROUP BY PFD.pf_id, PFD.metal_type
													) T
";

}

if($pw == 'jhw') {
	echo $SELECT_SQL;
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