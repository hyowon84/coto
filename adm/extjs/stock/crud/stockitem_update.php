<?php
include_once('./_common.php');
auth_check($auth[$sub_menu], "r");
$mb_id = $member[mb_id];

/*
 * JSON DECODE 관련 이슈
 * 1. 넘겨받은 JSON텍스트 ICONV로 변환필요 
 * 2. 상품명에 " OR ' 가 포함 되있는경우 디코딩 실패 str_replace로 변환필요
 * 3. STRIPSLASH 안하면 디코딩이 안됨
 * */
$arr = jsonDecode($_POST['data']);

$it_id = array();

/* 단일레코드일때 */
if( ($arr[number]*1) > 0 ) {

	$number = $arr[number];
	$gpcode = $arr[gpcode];
	$iv_id = $arr[iv_id];
	$iv_it_id = $arr[iv_it_id];
	$iv_dealer_worldprice = $arr[iv_dealer_worldprice];
	$iv_dealer_price = $arr[iv_dealer_price];
	$iv_qty = $arr[iv_qty];
	$iv_stats = $arr[iv_stats];
	$real_jaego = $arr[real_jaego];
	
	/* 상품정보 수정 */
	$common_sql = "	UPDATE	invoice_item	SET
														iv_dealer_worldprice = '$iv_dealer_worldprice',
														iv_dealer_price = '$iv_dealer_price',
														iv_qty	= '$iv_qty',
														iv_stats = '$iv_stats'
									WHERE		1=1
									AND			number = '$number'
									AND			iv_id = '$iv_id'
									AND			iv_it_id = '$iv_it_id'
									
	";
	sql_query($common_sql);
	db_log($common_sql,'invoice_item','발주품목 수정');
	
	$it_id[] = "'$iv_it_id'";
	
}
else {	/* 복수레코드일때 */

	for($i = 0; $i < count($arr); $i++) {
		$grid = $arr[$i];
		$number = $grid[number];
		$gpcode = $grid[gpcode];
		$iv_id = $grid[iv_id];
		$iv_it_id = $grid[iv_it_id];
		$iv_dealer_worldprice = $grid[iv_dealer_worldprice];
		$iv_dealer_price = $grid[iv_dealer_price];
		$iv_qty = $grid[iv_qty];
		$iv_stats = $grid[iv_stats];
		$real_jaego = $grid[real_jaego];

		/* 상품정보 수정 */
		$common_sql = "	UPDATE	invoice_item	SET
															iv_dealer_worldprice = '$iv_dealer_worldprice',
															iv_dealer_price = '$iv_dealer_price',
															iv_qty	= '$iv_qty',
															iv_stats = '$iv_stats'
										WHERE		1=1
										AND			number = '$number'
										AND			iv_id = '$iv_id'
										AND			iv_it_id = '$iv_it_id'
		";
		sql_query($common_sql);
		db_log($common_sql,'invoice_item','발주품목 수정');
		
		$it_id[] = "'$iv_it_id'";
		
	}
}


if(count($it_id) > 0) {
	$gpid_list = implode(",", $it_id);

	/*입고처리된 품목들은 카테고리 변경.  공동구매 -> 입고예정 카테고리로 변경*/
	$cate_sql = "	UPDATE	g5_shop_group_purchase 	SET
													ca_id = 'CTIP'
								WHERE		gp_id IN ($gpid_list)								
	";
	$result = sql_query($cate_sql);
	db_log($cate_sql, 'g5_shop_group_purchase', '입고처리');

	
	sql_query(" DELETE FROM  product_ipinfo WHERE	it_id IN ($gpid_list) ");
	$ip_sql = "	INSERT	INTO	product_ipinfo
											(	it_id,
												ip_qty,
												od_qty,
												ip_yn
											)
												SELECT	T.it_id,
																T.ip_qty,
																T.od_qty,
																IF(T.ip_qty >= T.od_qty,'Y','N') AS ip_yn 
												FROM		(
																	SELECT	IV.iv_it_id AS it_id,
																					SUM(IV.iv_qty) AS ip_qty,						/*전체발주수량 중 총 입고수량 */
																					IFNULL(OD.OD_QTY,0) AS od_qty				/*총 주문수량*/	
																	FROM		invoice_item IV
																					LEFT JOIN (	SELECT	it_id,
																															SUM(it_qty) AS OD_QTY
																											FROM		clay_order
																											WHERE		1=1
																											AND			stats >= '00'
																											AND			stats <= '60'
																											AND			it_id IN ($gpid_list)
																											GROUP BY it_id
																					) OD ON (OD.it_id = IV.iv_it_id)
																	WHERE		1=1
																	AND			IV.iv_stats = '40'
																	AND			IV.iv_it_id IN ($gpid_list)
																	GROUP BY IV.iv_it_id
																) T
	";
	sql_query($ip_sql);
	db_log($ip_sql, 'product_ipinfo', '입고정보생성');
	
}

if($result) {
	$json[success] = "true";
	$json[message] = '상품이 수정되었습니다';
} else {
	$json[success] = "false";
	$json[message] = '상품이 수정되지 않았습니다. 관리자에게 문의바랍니다.';
}

$json_data = json_encode_unicode($json);
echo $json_data;
?>