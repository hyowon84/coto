<?
include_once('./_common.php');

if($_GET['page']) {
	$page_name = "_".$_GET['page']."page";
} else {
	$page_name = '';
	$page = '';
}

header("Content-Type: application/x-msexcel; name=\"gp-".date("ymd", time()).$page_name.".xls\"");
header("Content-Disposition: inline; filename=\"gp-".date("ymd", time()).$page_name.".xls\"");

$where = array();

$sql_search = "";
if ($search != "") {
    if ($sel_field != "") {
			switch($sel_field){
				case "it_name":
					$where[] = " od_id in ( select od_id from {$g5['g5_shop_cart_table']} where it_name like '%".$search."%' ) ";
					break;
				case "mb_nick":
					$where[] = " mb_id in ( select mb_id from {$g5['member_table']} where mb_nick like '%".$search."%' ) ";
					break;

				case "od_wearing_cnt":
					$where[] = " od_wearing_cnt > 0 ";
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

$where[] = " gp_code <> ''  and od_cart_price > 0";

if ($od_settle_case) {
    $where[] = " od_settle_case = '$od_settle_case' ";
}

if($od_status){
   $where[] = " od_status = '$od_status' ";
}

if ($fr_date && $to_date) {
    $where[] = " od_time between '$fr_date 00:00:00' and '$to_date 23:59:59' ";
}


if($sfl_code2 != ""){
	$where[] = " gp_code ='".$sfl_code2."' ";
}


if ($where) {
    $sql_search = ' where '.implode(' and ', $where);
}

if ($sel_field == "")  $sel_field = "od_id";

if (!$sst) {
    $sst = "od_id";
    $sod = "desc";
}
$sql_order = " order by {$sst} {$sod} ";


$sql_common = " from {$g5['g5_shop_order_table']} T $sql_search $sql_group ";

$sql = " select count(od_id) as cnt " . $sql_common;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

if($_GET['page'] >= '0') {
	$LIMIT = "LIMIT		$from_record, $rows";
}

$sql  = "	SELECT	*,
           				(od_cart_coupon + od_coupon + od_send_coupon) as couponprice
				$sql_common
				$sql_order
				$LIMIT
";

$result = sql_query($sql);


$EXCEL_STR = "
<table border='1'>
<tr>
   <td>공동구매코드</td>
	 <td>주문일시</td>
   <td>주문자</td>
   <td>상품명</td>
   <td>단가</td>
   <td>수량</td>
   <td>배송비</td>
   <td>총금액</td>
   <td>진행상태</td>
   <td>현금영수증</td>
</tr>";

for($j=0;$row =sql_fetch_array($result);$j++) {
	$od_tax = "미발행";
	if($row['od_tax']=="1")$od_tax = "지출증빙용<br>[ ".$row['tax_status'].":".$row['od_tax_hp']." ]";
	elseif($row['od_tax']=="0")$od_tax = "현금영수증<br>[ ".$row['tax_status'].":".$row['od_tax_hp']." ]";

	if($row['od_tax'] == '0' || $row['od_tax'] == '1') {
		$현금영수증정보 = $od_tax."<br>".(($row['od_tax_state'] == 'Y') ? '(완료)' : '(처리중)');
	} else {
		$현금영수증정보 = $od_tax;
	}


	$mb = get_member($row['mb_id']);

	// 상품목록
	$sql2 = " select it_id,
                it_name,
				ct_gubun,
                cp_price,
                ct_notax
			   from {$g5['g5_shop_cart_table']}
			  where od_id = '{$row['od_id']}'
			  group by it_id
			  order by ct_id ";
	$result2 = sql_query($sql2);


	$rowspan = mysql_num_rows($result2);

	for($i=0; $row2=sql_fetch_array($result2); $i++) {

            // 상품의 옵션정보
            $sql3 = " select ct_id, it_id, ct_price, ct_point, sum(ct_qty) as total_qty, ct_option, ct_status, cp_price, ct_stock_use, ct_point_use, ct_send_cost, io_type, io_price, sum(ct_gp_soldout) as total_soldout
                        from {$g5['g5_shop_cart_table']}
                        where od_id = '{$row['od_id']}'
                          and it_id = '{$row2['it_id']}' ";
            $opt = sql_fetch($sql3);

			if($opt['io_type'])
				$opt_price = $opt['io_price'];
			else
				$opt_price = $opt['ct_price'] + $opt['io_price'];

			// 소계
			$ct_price['stotal'] = $opt_price * $opt['total_qty'];
			$ct_point['stotal'] = $opt['ct_point'] * $opt['total_qty'];


		$EXCEL_STR .= "<tr>";
		if($i==0){
		  $EXCEL_STR .= "
		   <td rowspan='".$rowspan."'>".$row['gp_code']."</td>
		   <td rowspan='".$rowspan."'>".$row['od_time']."</td>
		   <td rowspan='".$rowspan."'>".$mb['mb_nick']."</td>";	// 공구코드
		}

		 $EXCEL_STR .= "
		   <td>".stripslashes($row2['it_name'])."</td>
		   <td>".number_format($opt_price)."</td>
		   <td>".number_format($opt['total_qty']-$opt['total_soldout'])."</td>"; // 상품명, 단가, 수량

		  if($i==0){
	   $EXCEL_STR .= "
		   <td rowspan='".$rowspan."'>".$row['od_send_cost']."</td>
		   <td rowspan='".$rowspan."'>".number_format($row['od_cart_price'] + $row['od_send_cost'] + $row['od_send_cost2'])."</td>
		   <td rowspan='".$rowspan."'>".$opt['ct_status']."</td>
		   <td rowspan='".$rowspan."'>".$현금영수증정보."</td>"; //TODO: 현금영수증 정보 추가
		  }

		$EXCEL_STR .= "</tr>";
	}
}

$EXCEL_STR .= "</table>";



echo "<meta content=\"application/vnd.ms-excel; charset=UTF-8\" name=\"Content-type\"> ";
echo $EXCEL_STR;
?>