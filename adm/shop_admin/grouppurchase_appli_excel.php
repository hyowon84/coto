<?
include_once('./_common.php');

header("Content-Type: application/x-msexcel; name=\"gongdong-".date("ymd", time()).".xls\"");
header("Content-Disposition: inline; filename=\"gongdong-".date("ymd", time()).".xls\"");


$sql_search = "";
if ($search != "") {
    if ($sel_field != "") {
		switch($sel_field){
			case "mb_nick":
				$where[] = " mb_id in ( select mb_id from {$g5['member_table']} where mb_nick like '%".$search."%' ) ";
				break;
			case "ct_wearing_cnt":
				$where[] = " ct_wearing_cnt > 0";
				break;
			default :
		        $where[] = " $sel_field like '%$search%' ";
				break;
		}
    }

    if ($save_search != $search) {
        $page = 1;
    }
}


$where[] = " total_amount_code <> '' ";

if($od_status){
   $where[] = " ct_status = '$od_status' ";
}

if ($fr_date && $to_date) {
    $where[] = " ct_time between '$fr_date 00:00:00' and '$to_date 23:59:59' ";
}


if($sfl_code2 != ""){
	$where[] = " total_amount_code ='".$sfl_code2."' ";
}


if ($where) {
    $sql_search = ' where '.implode(' and ', $where);
}

if (!$sst) {
    $sst  = "ct_id";
    $sod = "desc";
}
$sql_order = "order by $sst $sod";

$sql = " select a.*
		   from {$g5['g5_shop_cart_table']} a left join {$g5['g5_shop_group_purchase_table']} b on ( a.it_id = b.gp_id )
		  $sql_search order by a.ct_id desc  ";

$result = sql_query($sql);

$EXCEL_STR = "
<table border='1'>
<tr>
   <td>번호</td>
   <td>공동구매코드</td>
   <td>주문자</td>
   <td>상품명</td>
   <td>단가</td>
   <td>수량</td>
   <td>판매금액</td>
   <td>진행상태</td>
   <td>판매가능여부</td>
</tr>";

while($row = mysql_fetch_array($result)) {

	$href = G5_SHOP_URL.'/grouppurchase.php?gp_id='.$row['it_id'];
	$bg = 'bg'.($i%2);

	$row[ct_type] = substr($row[ct_type], 0, 4);

	$image = get_gp_image($row['it_id'], 70, 70);

	$gp_row = sql_fetch("select * from {$g5['g5_shop_group_purchase_table']} where gp_id='".$row[it_id]."' ");

	$mb = get_member($row[mb_id]);

	if($row[ct_buy_qty]){
		$ct_buy_qty = $row[ct_buy_qty];
	}else{
		$ct_buy_qty = 0;
	}

	//옵션 상품
	$op_arr = explode("|", $row[ct_op_option]);
	$op_price = 0;
	for($b = 0; $b < count($op_arr); $b++){
		if($op_arr[$b]){
			$op_row = sql_fetch("select * from {$g5['g5_shop_option2_table']} where it_id='".$row[it_id]."' and con='".$op_arr[$b]."' ");
			$op_price = $op_price + $op_row[price];
		}
	}

	$totalPrice = ($row['ct_price'] * $row[ct_qty]) + $op_price;

	$buy_status = "";
	if($row[buy_status] == "y")$buy_status="판매가능";
	else $buy_status = "품절";

   $EXCEL_STR .= "
   <tr>
       <td>".$row[ct_id]."</td>
	   <td>".$row['total_amount_code']."</td>
	   <td>".$mb[mb_nick]."</td>
	   <td>".$row[it_name]."</td>
	   <td>".number_format($row[ct_price])."</td>
	   <td>".$row[ct_qty]."</td>
	   <td>".number_format($totalPrice)."</td>
	   <td>".getPurchaseStateText($row['total_amount_code'])."</td>
	   <td>".$buy_status."</td>
   </tr>
   ";
}
  
$EXCEL_STR .= "</table>";
  
echo "<meta content=\"application/vnd.ms-excel; charset=UTF-8\" name=\"Content-type\"> ";
echo $EXCEL_STR;
?>