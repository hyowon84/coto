<?
include_once('./_common.php');

header("Content-Type: application/x-msexcel; name=\"today-".date("ymd", time()).".xls\"");
header("Content-Disposition: inline; filename=\"today-".date("ymd", time()).".xls\"");

$where = array();

$sql_search = "";
if ($search != "") {
    if ($sel_field != "") {
        $where[] = " $sel_field like '%$search%' ";
    }

    if ($save_search != $search) {
        $page = 1;
    }
}

$where[] = " od_id in ( select distinct od_id from $g5[g5_shop_cart_table] where ct_gubun <> 'P' ) ";

if ($od_status) {
    switch($od_status) {
        case '전체취소':
            $where[] = " od_status = '취소' ";
            break;
        case '부분취소':
            $where[] = " od_status IN('입금대기', '결제완료', '상품준비중', '배송대기', '배송중', '배송완료') and od_cancel_price > 0 ";
            break;
        default:
            $where[] = " od_status = '$od_status' ";
            break;
    }

    switch ($od_status) {
        case '입금대기' :
            $sort1 = "od_id";
            $sort2 = "desc";
            break;
        case '결제완료' :   // 결제완료
            $sort1 = "od_receipt_time";
            $sort2 = "desc";
            break;
        case '배송중' :   // 배송중
            $sort1 = "od_invoice_time";
            $sort2 = "desc";
            break;
    }
}

if ($od_settle_case) {
    $where[] = " od_settle_case = '$od_settle_case' ";
}

if ($od_misu) {
    $where[] = " od_misu != 0 ";
}

if ($od_cancel_price) {
    $where[] = " od_cancel_price != 0 ";
}

if ($od_refund_price) {
    $where[] = " od_refund_price != 0 ";
}

if ($od_receipt_point) {
    $where[] = " od_receipt_point != 0 ";
}

if ($od_coupon) {
    $where[] = " od_coupon != 0 ";
}

if ($od_escrow) {
    $where[] = " od_escrow = 1 ";
}

if ($fr_date && $to_date) {
    $where[] = " od_time between '$fr_date 00:00:00' and '$to_date 23:59:59' ";
}

if ($where) {
    $sql_search = ' where '.implode(' and ', $where);
}



if ($sel_field == "")  $sel_field = "od_id";
if ($sort1 == "") $sort1 = "od_id";
if ($sort2 == "") $sort2 = "desc";

$sql_common = " from {$g5['g5_shop_order_table']} $sql_search and combine_deli_status='y' and od_id in (select distinct od_id from g5_shop_cart where ct_gubun <> 'P') ";

$sql = " select count(od_id) as cnt " . $sql_common;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql  = " select *,
            (od_cart_coupon + od_coupon + od_send_coupon) as couponprice
           $sql_common
           order by $sort1 $sort2
		   , combine_deli_code desc
           ";

$result = sql_query($sql);

$EXCEL_STR = "
<table border='1'>
<tr>
   <td>주문번호</td>
   <td>주문상태</td>
   <td>결제수단</td>
   <td>운송장번호</td>
   <td>배송회사</td>
   <td>배송일시</td>
   <td>주문합계</td>
   <td>입금합계</td>
   <td>주문취소</td>
   <td>쿠폰</td>
   <td>미수금</td>
</tr>";

while($row = mysql_fetch_array($result)) {

	// 결제 수단
	$s_receipt_way = $s_br = "";
	if ($row['od_settle_case'])
	{
		$s_receipt_way = $row['od_settle_case'];
		$s_br = '<br />';
	}
	else
	{
		$s_receipt_way = '결제수단없음';
		$s_br = '<br />';
	}

	if ($row['od_receipt_point'] > 0)
		$s_receipt_way .= $s_br."포인트";

	if ($od_status == '배송준비중') {
		$od_invoice = "<input type='text' name='od_invoice[".$i."]' value='".$row['od_invoice']."' class='frm_input' size='10'>";
	} else if ($od_status == '배송중' || $od_status ==  '배송완료') {
		$od_invoice = $row['od_invoice'];
	} else {
		$od_invoice = "-";
	}

	if ($od_status == '배송준비중') {
		$od_delivery_company = "<select name='od_delivery_company[".$i."]'>";
		$od_delivery_company .= get_delivery_company($delivery_company);
		$od_delivery_company .= "</select>";
	} else if ($od_status == '배송중' || $od_status ==  '배송완료') {
		$od_delivery_company = $delivery_company;
	} else {
		$od_delivery_company = "-";
	}

	if ($od_status == '배송준비중') {
		$od_invoice_time = "<input type='text' name='od_invoice_time[".$i."]' value='".$invoice_time."' class='frm_input' size='10' maxlength='19'>";
	} else if ($od_status == '배송중' || $od_status ==  '배송완료') {
		$od_invoice_time = substr($row['od_invoice_time'],2,14);
	} else {
		$od_invoice_time = "-";
	}

	$od_price_sum = $row['od_cart_price'] + $row['od_send_cost'] + $row['od_send_cost2'];

   $EXCEL_STR .= "
   <tr>
	   <td>".$row['od_id']."</td>
	   <td>".$row['od_status']."</td>
	   <td>".$s_receipt_way."</td>
	   <td>".$od_invoice."</td>
	   <td>".$od_delivery_company."</td>
	   <td>".$od_invoice_time."</td>
	   <td>".$od_price_sum."</td>
	   <td>".$row['od_receipt_price']."</td>
	   <td>".$row['od_cancel_price']."</td>
	   <td>".$row['couponprice']."</td>
	   <td>".$row['od_misu']."</td>
   </tr>
   ";
}
  
$EXCEL_STR .= "</table>";
  
echo "<meta content=\"application/vnd.ms-excel; charset=UTF-8\" name=\"Content-type\"> ";
echo $EXCEL_STR;
?>