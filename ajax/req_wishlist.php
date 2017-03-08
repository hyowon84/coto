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


$원화 = $_SESSION[unit_kor_duty];

$sql = "	SELECT	a.wi_id,		/* wish id*/
						a.mb_id,
						a.it_id,		
						b.gp_id,		/*상품코드*/
						b.gp_img,
						a.wi_cnt,	/* 수량 */	
						a.wi_time,	/* 찜한날짜 */
						b.gp_price,	/* 일반가 */
						b.gp_name,
						IF(b.gp_price = 0,c.po_cash_price*$원화,b.gp_price) AS gp_price	/* 일반가 */
			FROM		g5_shop_wish a
						LEFT OUTER JOIN g5_shop_group_purchase b on ( a.it_id = b.gp_id )
						LEFT OUTER JOIN g5_shop_group_purchase_option c on (c.gp_id = a.it_id AND c.po_sqty <= 1)
			WHERE		1=1
			AND		a.mb_id = '".$member[mb_id]."'
			ORDER	BY	a.wi_id DESC
";
$result = sql_query($sql);

//



$cart_count = mysql_num_rows($result);

$json = array();
$json['data'] = array();


$i = 0;
while($row = mysql_fetch_assoc($result)) {
	$row[gp_img] = urlencode($row[gp_img]); 
	$row[gp_name] = str_replace('"',"\"",$row[gp_name]);
	$row[gp_name] = str_replace("\t","",$row[gp_name]);
	$row[gp_name] = str_replace("\r\n","",$row[gp_name]);
	array_push($json['data'], $row);
}

$json['total_count'] = $cart_count;
$json_data = json_encode_unicode($json);

echo $json_data;
?>