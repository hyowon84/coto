<?
/*
 * AJAX 위시리스트 JSON 데이터 추출
 * */
include_once('./_common.php');
include_once(G5_PATH.'/adm/shop_admin/admin.shop.lib.php');
include_once('../lib/coinstoday.lib.php');

$s_cart_id = get_session('ss_cart_id');

// 코드값 검색
$sql = "SELECT	M.it_id,
								MIN(M.reg_date) AS min_date,	/* 최초 입력일 */
								MAX(M.reg_date) AS max_date,	/* 마지막 입력일 */				
								IP.IP_EA_SUM,			/* 누적입고수량 */
								IP.IP_PRICE_AVG,	/* 평균입고가격 */
								IP.IP_PRICE_SUM,	/* 누적입고액 */
								OP.OP_EA_SUM,			/* 누적출고수량 */
								OP.OP_PRICE_AVG,	/* 평균출고가격 */
								OP.OP_PRICE_SUM,		/* 누적출고액 */
								(OP.OP_PRICE_SUM - (OP.OP_EA_SUM * IP.IP_PRICE_AVG)) AS profit_total		/* 출고수량 기준 입고단가 대비 수익금액 */
				FROM		stock_manage M
								LEFT JOIN (	SELECT	it_id,
																		SUM(input_ea) AS 'IP_EA_SUM',
																		SUM(input_price * input_ea) AS 'IP_PRICE_SUM',
																		AVG(input_price) AS 'IP_PRICE_AVG'
														FROM		stock_manage 
														WHERE		input_ea > 0
														GROUP BY it_id
								) IP ON (IP.it_id = M.it_id)
								LEFT JOIN (	SELECT	it_id,
																		SUM(output_ea) AS 'OP_EA_SUM',
																		SUM(output_price * output_ea) AS 'OP_PRICE_SUM',
																		AVG(output_price) AS 'OP_PRICE_AVG'
														FROM		stock_manage 
														WHERE		output_ea > 0
														GROUP BY it_id
								) OP ON (OP.it_id = M.it_id)	
				GROUP BY M.it_id
				ORDER BY M.group_tag DESC, M.it_id DESC
";
$result = sql_query($sql);

//g5_shop_wish_table



$cart_count = mysql_num_rows($result);

$json = array();
$json['data'] = array();


$i = 0;
while($row = mysql_fetch_assoc($result)) {
	$row[gp_img] = urlencode($row[gp_img]);
	$row[it_name] = str_replace('"',"\"",$row[it_name]);
	$row[it_name] = str_replace("\t","",$row[it_name]);
	$row[it_name] = str_replace("\r\n","",$row[it_name]);
	array_push($json['data'], $row);
}

$json['total_count'] = $cart_count;
$json_data = json_encode_unicode($json);

echo $json_data;
?>