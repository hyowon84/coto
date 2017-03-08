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


/* 필요한 항목이 있을경우 아래에서 갖다쓰기바람 */

// 			O.od_cancel_price,
// 			O.od_receipt_point,
// 			O.od_refund_price,
	
// 			C.ct_history,		/*상태변경시 기록하는 로그*/
// 			C.ct_payment,		/* 결제방식?  B, ...  */
// 			C.ct_usd_price,	/*상품 가격(달러)*/
// 			C.ct_point,			/**/
// 			C.cp_price,			/* 주문당시 환율금액 */
// 			C.ct_point_use,	/* 포인트 사용가능여부? */
// 			C.ct_stock_use,	/* ? */
// 			C.ct_option,		/* 선택옵션정보? */
	
	
// 			C.ct_notax,
// 			C.io_id,
// 			C.io_type,
// 			C.io_price,
	
// 			C.ct_ip,
// 			C.ct_send_cost,
// 			C.ct_direct,
// 			C.ct_select,
// 			C.ct_type,	/*공동구매 카테 구분*/
// 			C.ct_time_code,	/*날짜 암호화*/
// 			C.auc_status,	/*경매구분*/
// 			C.pur_status,	/*구매대행 여부*/
// 			C.ct_card_status,	/*카드여부*/
// 			C.ct_card_price,	/*카드금액*/
// 			C.ct_gp_status,	/*공동구매 확인 여부*/
// 			C.buy_status,	/*판매가능여부*/
// 			C.ct_buy_qty,	/*실판매수량*/
// 			C.total_amount_code,	/*공동구매 건별 코드*/
// 			C.ex_status,	/*교환여부*/
// 			C.ex_reason,	/*사유선택*/
// 			C.ex_content,	/*사유내용*/
// 			C.ex_addr,	/*교환주소*/
	
// 			/* 주문자 정보 */
// 			O.od_name,	/*주문자명*/
// 			O.od_email,	/*주문자 이메일*/
// 			O.od_tel,	/*주문자 연락처*/
// 			O.od_hp,		/*주문자 휴대폰*/
// 			O.od_zip1,
// 			O.od_zip2,
// 			O.od_addr1,
// 			O.od_addr2,
// 			O.od_addr3,
// 			O.od_addr_jibeon,
	
// 			O.od_deposit_name,
// 			O.od_b_name,
// 			O.od_b_tel,
// 			O.od_b_hp,
// 			O.od_b_zip1,
// 			O.od_b_zip2,
// 			O.od_b_addr1,
// 			O.od_b_addr2,
// 			O.od_b_addr3,
// 			O.od_b_addr_jibeon,
// 			O.od_memo,
// 			O.od_cart_count,
// 			O.od_cart_price,
// 			O.od_cart_coupon,
// 			O.od_cart_usd_price,
// 			O.od_exchange_rate,

// 			O.od_bank_account,
// 			O.od_receipt_time,
// 			O.od_coupon,
	
// 			O.od_shop_memo,
// 			O.od_mod_history,
// 			O.od_status,
// 			O.od_hope_date,
// 			O.od_settle_case,
// 			O.od_mobile,
// 			O.od_tno,
// 			O.od_app_no,
// 			O.od_escrow,
// 			O.od_tax_flag,
// 			O.od_tax_mny,
// 			O.od_vat_mny,
// 			O.od_free_mny,
// 			O.od_delivery_company,
// 			O.od_invoice,
// 			O.od_invoice_time,
// 			O.od_cash,
// 			O.od_cash_no,
// 			O.od_cash_info,
// 			O.od_time,
// 			O.od_pwd,
// 			O.od_ip,
// 			O.buy_status,	/*구매 종류*/
// 			O.od_bank,	/*입금은행*/
// 			O.od_remit,	/*예금주*/
// 			O.od_tax,	/*현금영수증여부*/
// 			O.tax_status,	/*영수증 발행 종류*/
// 			O.od_tax_hp,	/*영수증 발행 휴대폰*/
// 			O.od_last_date,	/*입금대기기간*/
// 			O.combine_deli_code,	/*통합배송 코드*/
// 			O.combine_deli_date,	/*통합배송 날짜*/
// 			O.combine_deli_status,	/*통합배송 여부*/
// 			O.gp_code,
// 			O.od_wearing_cnt,
// 			O.od_tax_state

$sql = "	SELECT	O.ROW_CNT,	/* 주문번호에 종속되있는 주문상품 레코드 갯수 ROWSPAN으로 묶을때 필요 */
								C.ct_gubun,	/*N:투데이스토어, P:공동구매, (A:사용안하는중)*/
								C.ct_id,				/*개별 주문ID*/
								C.mb_id,				/*주문한 회원ID*/
								C.it_id,				/*상품ID*/
								C.it_name,			/*상품명 */
								C.ct_time,			/*주문일자*/
								C.od_id,				/*주문번호(그룹ID)*/
								C.ct_price,			/* 상품가격(달러) 원화로 변환 */
								(O.ORDER_PRICE + O.od_send_cost + O.od_send_cost2) AS TOTAL_PRICE,	/* 총 주문금액 */
								C.ct_qty,			/* 신청수량 */
								C.ct_status,		/*주문진행상태*/
								O.od_receipt_price,	/*입금액*/
								O.od_misu,			/*미입금액*/
								
								O.od_send_cost,	/* 배송비 */
								O.od_send_cost2,	/* 배송비(기타) */
								O.od_send_coupon,
								CASE
									WHEN	C.ct_gubun = 'N' THEN
										IF(IT.it_img1,CONCAT('".G5_DATA_URL."','/item/',IT.it_img1),NULL)	
									WHEN	C.ct_gubun = 'P' THEN
										GP.gp_img
									ELSE
										NULL
								END AS img_url	
			FROM		(SELECT	COUNT(B.od_id) AS ROW_CNT,	/* 주문번호당 카트정보 갯수 */
									SUM(B.ct_price*B.ct_qty) AS ORDER_PRICE,
									A.*
						FROM		g5_shop_order A
									LEFT OUTER JOIN g5_shop_cart B ON (B.od_id = A.od_id AND B.ct_status NOT IN ('취소','판매취소') )
						WHERE		A.mb_id = '".$member[mb_id]."'
						GROUP BY A.od_id
						) O
						LEFT OUTER JOIN g5_shop_cart C ON (C.od_id = O.od_id)
						LEFT OUTER JOIN g5_shop_group_purchase GP ON (GP.gp_id = C.it_id)
						LEFT OUTER JOIN g5_shop_item IT ON (IT.it_id = C.it_id)
			ORDER	BY	C.ct_time DESC, C.ct_id DESC
";

$result = sql_query($sql);

//echo $sql;


//g5_shop_wish_table



$cart_count = mysql_num_rows($result);

$json = array();
$json['data'] = array();


$i = 0;
while($row = mysql_fetch_assoc($result)) {
	$row[gp_img] = urlencode($row[img_url]);
	$row[it_name] = str_replace('"',"\"",$row[it_name]);
	$row[it_name] = str_replace("\t","",$row[it_name]);
	$row[it_name] = str_replace("\r\n","",$row[it_name]);
	array_push($json['data'], $row);
}

$json['total_count'] = $cart_count;
$json_data = json_encode_unicode($json);

echo $json_data;
?>