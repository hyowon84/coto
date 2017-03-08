<?php
/* 
 * AJAX 위시리스트 JSON 데이터 추출 
 * */
include_once('./_common.php');
include_once('../lib/coinstoday.lib.php');


/*
$s_cart_id = get_session('ss_cart_id');

// 코드값 검색
if($ct_type)$ct_type_que = " and ct_type='".$ct_type."' ";
else $ct_type_que = " and ct_type != '' ";
*/

/*
상세정보
세션정보 mb_id
패러미터 od_id 
*/
$sql = "		SELECT	CT.ct_id,			/* 액션에 필요한 PK가 되는 카트ID */
							CT.mb_id,			/* 회원ID */
							CT.od_id,			/* 주문번호 */
							CT.total_amount_code AS gp_code,		/* 공동구매 진행코드 */
							GP.gp_name,		/* 상품명 */
							GP.gp_img,		/* 이미지 URL */
							CASE
								WHEN	CT.ct_gubun = 'N' THEN
									IF(IT.it_img1,CONCAT('".G5_DATA_URL."','/item/',IT.it_img1),NULL)	
								WHEN	CT.ct_gubun = 'P' THEN
									GP.gp_img
								ELSE
									NULL
							END AS img_url,
							CT.ct_qty,		/* 수량 */
							CT.ct_price,	/* 가격*/
							CT.ct_point		/* 포인트 */
				FROM		g5_shop_cart CT
								LEFT OUTER JOIN g5_shop_order OD ON (OD.od_id = CT.od_id AND OD.mb_id = CT.mb_id)
								LEFT OUTER JOIN g5_shop_group_purchase GP ON (GP.gp_id = CT.it_id)
								LEFT OUTER JOIN g5_shop_item IT ON (IT.it_id = CT.it_id)
				WHERE		OD.mb_id = '$member[mb_id]'
				AND			CT.od_id = '$od_id'
				ORDER BY CT.ct_id DESC
";
$result = sql_query($sql);


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
	
	if(!$i) {
		$json['od_id'] = $row[od_id];
		$json['gp_code'] = $row[gp_code];
	}
	$i++;
}
$json['total_count'] = $cart_count;



/* 하단 결제금액 */
$sql = "	SELECT	CT.od_id,
								SUM(CT.ct_price) AS total_product_price,															/* 상품금액 합계*/
								SUM(CT.ct_point) AS total_point,																		/* 포인트사용 합계*/
								SUM(OD.od_send_cost) + SUM(OD.od_send_cost2) AS total_baesongbi,	/* 배송비 합계*/
								SUM(CT.ct_price) + SUM(OD.od_send_cost) + SUM(OD.od_send_cost2) AS total_price	/* 상품금액합계 + 배송비합계 = 총 결제금액*/
				FROM		g5_shop_cart CT
								LEFT OUTER JOIN g5_shop_order OD ON (OD.od_id = CT.od_id AND OD.mb_id = CT.mb_id)
								LEFT OUTER JOIN g5_shop_group_purchase GP ON (GP.gp_id = CT.it_id)
								LEFT OUTER JOIN g5_shop_item IT ON (IT.it_id = CT.it_id)
				WHERE		OD.mb_id = '$member[mb_id]'
				AND			CT.od_id = '$od_id'
				GROUP BY CT.od_id
";
$result = sql_query($sql);

$row = mysql_fetch_assoc($result);
$json['total_product_price'] = $row[total_product_price];
$json['total_baesongbi'] = $row[total_baesongbi];
$json['total_price'] = $row[total_price];
$json_data = json_encode_unicode($json);

echo $json_data;
?>