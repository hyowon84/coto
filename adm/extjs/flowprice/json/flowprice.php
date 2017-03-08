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


/* 귀금속시세항목 설정항목들 */
if($mode == 'fpmetalsetting') {

	/* 주문금액 큰 순서대로 회원목록 추출 */
	$SELECT_SQL = "	SELECT	FC.number,
													FC.ex_id,
													FC.sortNo,					/*정렬*/
													FC.metal_type,			/*귀금속 유형*/
													FC.weight,					/*중량*/
													IFNULL(FC.add_price,0) AS add_price,				/*추가금액or할인금액*/
													FC.title,						/*타이틀*/
													FC.reg_date,
													FD.buy_price,
													FD.sell_price
									FROM		flowprice_cfg FC
													LEFT JOIN (	SELECT	number,
																							fp_id,
																							gr_id,
																							ex_id,
																							metal_type,
																							sell_price,
																							buy_price,
																							start_date,
																							end_date
																			FROM		flowprice_data
																			WHERE		gr_id = (SELECT	MAX(gr_id) FROM flowprice_data )
													) FD ON (FD.fp_id = FC.number)
									WHERE		1=1
									$AND_SQL
	";
}

/* 귀금속시세 데이터 */
else if($mode == 'flowprice') {
	$start = 0;
	list($limit) = mysql_fetch_array(sql_query("SELECT	COUNT(*) FROM flowprice_cfg"));

	/* 주문금액 큰 순서대로 회원목록 추출 */
	$SELECT_SQL = "SELECT	FD.number,
												FD.fp_id,
												FD.ex_id,
												FD.metal_type,
												FD.buy_price,		#살때가격
												FD.sell_price,	#팔때가격

												FD.start_date,
												FD.end_date,
												FC.sortNo,
												FC.title,
												FC.weight,

												CASE
													WHEN	FD.metal_type = 'GL' THEN
														CONCAT('1:',FD.sell_price / SL.buy_price)
													WHEN	FD.metal_type = 'SL' THEN
														CONCAT(GL.buy_price / FD.sell_price,':1')
												END AS metalExchrate

								FROM		flowprice_data FD
												LEFT JOIN flowprice_cfg FC ON (FC.number = FD.fp_id)

												/*금일때 은비율*/
												LEFT JOIN (	SELECT	FD.number,
																						FD.fp_id,
																						FD.gr_id,
																						FD.ex_id,
																						FD.metal_type,
																						FD.sell_price,
																						FD.buy_price,
																						FD.start_date,
																						FD.end_date,
																						FC.sortNo,
																						FC.title,
																						FC.weight
																		FROM		flowprice_data FD
																						LEFT JOIN flowprice_cfg FC ON (FC.number = FD.fp_id)
												) SL ON (SL.gr_id = FD.gr_id AND SL.metal_type = 'SL' AND SL.weight = 1)

												/*은일때 금비율*/
												LEFT JOIN (	SELECT	FD.number,
																						FD.fp_id,
																						FD.gr_id,
																						FD.ex_id,
																						FD.metal_type,
																						FD.sell_price,
																						FD.buy_price,
																						FD.start_date,
																						FD.end_date,
																						FC.sortNo,
																						FC.title,
																						FC.weight
																		FROM		flowprice_data FD
																						LEFT JOIN flowprice_cfg FC ON (FC.number = FD.fp_id)
												) GL ON (GL.gr_id = FD.gr_id AND GL.metal_type = 'GL' AND GL.weight = 1)
								WHERE		FD.gr_id = (SELECT	MAX(gr_id) FROM flowprice_data )
	";

}

/* 환율 항목들 */
else if($mode == 'exchsetting') {

	/* 주문금액 큰 순서대로 회원목록 추출 */
	$SELECT_SQL = "	SELECT	E.number,
													E.ex_id,
													E.sortNo,	/*정렬*/
													E.money_type,	/*귀금속 유형*/
													E.qty,	/*중량*/
													E.sellfee,
													E.buyfee,
													E.title,	/*타이틀*/
													E.reg_date
									FROM		exchrate_cfg E

									WHERE		1=1
									$AND_SQL
	";
}
/* 환율 시세 */
else if($mode == 'exchrate') {

	$SELECT_SQL = "	SELECT	T.sortNo,
													T.money_type,
													T.qty,
													T.title,
													T.exchrate,		/*기준환율*/
													T.sellfee,
													T.exchrate + T.sellfee AS exchrate_sell,
													(T.exchrate + T.sellfee) * T.qty AS SELL_PRICE,
													T.buyfee,
													T.exchrate + T.buyfee AS exchrate_buy,
													(T.exchrate + T.buyfee) * T.qty AS BUY_PRICE
									FROM	(
													SELECT	E.sortNo,	/*정렬*/
																	E.money_type,	/*귀금속 유형*/
																	E.qty,	/*중량*/
																	E.title,	/*타이틀*/
																	CASE
																		WHEN	E.money_type = 'USD'	THEN
																			FP.USD_T
																		WHEN	E.money_type = 'CNY'	THEN
																			FP.CNY_T
																	END exchrate,
																	E.sellfee,
																	E.buyfee
													FROM		exchrate_cfg E
																	,(SELECT	*
																		FROM		flow_price
																		ORDER BY reg_date	DESC
																		LIMIT 1
																		) FP
													WHERE		1=1
												) T
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