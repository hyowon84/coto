<?php
/* 
 * AJAX 위시리스트 JSON 데이터 추출 
 * */
include_once('./_common.php');
include_once('../lib/coinstoday.lib.php');

$s_cart_id = get_session('ss_cart_id');

// 코드값 검색
if($ct_type)$ct_type_que = " and ct_type='".$ct_type."' ";
else $ct_type_que = " and ct_type != '' ";



$sql = "	SELECT	a.ct_id,
						a.it_id,
						a.it_name,
						a.od_id,
						a.ct_price,
						a.ct_point,
						a.ct_qty,
						a.ct_status,
						a.ct_send_cost,
						a.ct_gp_status,
						a.ct_type,
						a.buy_status,
						a.ct_op_option,
						a.total_amount_code, 
						b.ca_id,
						b.ca_id2,
						b.ca_id3,
						b.gp_img
			FROM		{$g5['g5_shop_cart_table']} a
						LEFT OUTER JOIN {$g5['g5_shop_group_purchase_table']} b on ( a.it_id = b.gp_id )
			WHERE		a.od_id = '$s_cart_id'
			AND		(`ct_status`='쇼핑')
			AND		a.mb_id='".$member[mb_id]."'
			AND		a.ct_gubun = 'P'	/* 출처구분 ( 투데이스토어, 공동구매 ...) */
			$ct_type_que		/* 공동구매 카테고리 구분 */
			GROUP	BY	a.it_id
			ORDER	BY	a.ct_id
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