<?php
include_once('./_common.php');
auth_check($auth[$sub_menu], "r");


//스트립슬래시를 안하면 json_decode가 안됨
$arr = json_decode(str_replace('\"','"',stripslashes( iconv('utf-8', 'cp949', $_POST['data'] ) )),true);
$mb_id = $member[mb_id];



/* 단일레코드일때 */
if( strlen($arr[iv_id]) > 1 ) {

	$iv_id = $arr[iv_id];
	$gpcode = $arr[gpcode];
	$iv_dealer = $arr[iv_dealer];
	$iv_order_no = $arr[iv_order_no];
	$iv_receipt_link = $arr[iv_receipt_link];
	$iv_date = $arr[iv_date];
	$money_type = $arr[money_type];
	$od_exch_rate = $arr[od_exch_rate];
	$iv_tax = $arr[iv_tax];
	$iv_shippingfee = $arr[iv_shippingfee];
	$iv_discountfee = $arr[iv_discountfee];
	$iv_memo = $arr[iv_memo];
	$arv_exch_rate = $arr[arv_exch_rate];
	$reg_date = $arr[reg_date];
	$admin_id = $arr[admin_id];

	/* 상품정보 수정 */
	$common_sql = "	UPDATE	invoice_info	SET
															money_type = '$money_type',						/*USD, CNY, ...*/
															od_exch_rate = '$od_exch_rate',				/*주문기준 환율*/
															iv_tax = '$iv_tax',
															iv_discountfee = '$iv_discountfee',
															iv_shippingfee = '$iv_shippingfee',
															iv_memo = '$iv_memo'									/*내용*/
									WHERE		gpcode = '$gpcode'
									AND			iv_id = '$iv_id'
	";
	sql_query($common_sql);
	db_log($common_sql,'invoice_info','발주서 수정 ');
}
else {	/* 복수레코드일때 */

	for($i = 0; $i < count($arr); $i++) {
		$grid = $arr[$i];

		$iv_id = $grid[iv_id];
		$gpcode = $grid[gpcode];
		$iv_dealer = $grid[iv_dealer];
		$iv_order_no = $grid[iv_order_no];
		$iv_receipt_link = $grid[iv_receipt_link];
		$iv_date = $grid[iv_date];
		$money_type = $grid[money_type];
		$od_exch_rate = $grid[od_exch_rate];
		$iv_tax = $grid[iv_tax];
		$iv_shippingfee = $grid[iv_shippingfee];
		$iv_discountfee = $grid[iv_discountfee];
		$iv_memo = $grid[iv_memo];
		$arv_exch_rate = $grid[arv_exch_rate];
		$reg_date = $grid[reg_date];
		$admin_id = $grid[admin_id];


		/* 상품정보 수정 */
		$common_sql = "	UPDATE	invoice_info	SET
															money_type = '$money_type',						/*USD, CNY, ...*/
															od_exch_rate = '$od_exch_rate',				/*주문기준 환율*/
															iv_tax = '$iv_tax',
															iv_discountfee = '$iv_discountfee',
															iv_shippingfee = '$iv_shippingfee',
															iv_memo = '$iv_memo'									/*내용*/
										WHERE		gpcode = '$gpcode'
										AND			iv_id = '$iv_id'
		";
		sql_query($common_sql);
		db_log($common_sql,'invoice_info','발주서 수정 ');
	}
}


if($result) {
	$json[success] = "true";
	$json[message] = '수정되었습니다';
} else {
	$json[success] = "false";
	$json[message] = '수정되지 않았습니다. 관리자에게 문의바랍니다.';
}

$json_data = json_encode_unicode($json);
echo $json_data;
?>