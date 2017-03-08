<?
include_once('./_common.php');

header("Content-Type: application/x-msexcel; name=\"gongdong_all-".date("ymd", time()).".xls\"");
header("Content-Disposition: inline; filename=\"gongdong_all-".date("ymd", time()).".xls\"");

$sql_common = " from {$g5['g5_shop_group_purchase_group_table']} as pg left join {$g5['g5_shop_group_purchase_table']} as gp on pg.gp_id = gp.gp_id";


$sql_search = "";
if ($search != "") {
    if ($sel_field != ""){
		switch($sel_field){
			case "pg.gp_wearing":
				$where[] = " pg.gp_wearing > 0";
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

$where[] = " pg.gp_code <> '' ";


if($od_status){
   $where[] = " pg.gp_code in ( select type_code from {$g5['g5_total_amount_table']} where gc_state='$od_status') ";
}


if($sfl_code2 != ""){
	$where[] = " pg.gp_code ='".$sfl_code2."' ";
}


if ($where) {
    $sql_search = ' where '.implode(' and ', $where);
}


if (!$sst) {
    $sst  = "pg.gp_datetime";
    $sod = "desc";
}
$sql_order = "ORDER	BY	$sst $sod";


$sql = " select count(*) as cnt " . $sql_common;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

// $rows = $config['cf_page_rows'];
$rows = ($rec_qty) ? $rec_qty : '30';
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

if($_GET['page'] >= '0') {
	$LIMIT = "LIMIT		$from_record, $rows";
}



$sql = "SELECT	pg.gp_code,	
							pg.gp_id,	
							pg.gp_cart_cnt,			/* 카트주문건수 */
							pg.gp_cart_price,		/* 총주문금액 */
							pg.gp_datetime,	
							pg.gp_cart_qty,			/* 총신청수량 */
							pg.gp_soldout,			/* 총품절수량 */
							pg.gp_wearing,			/* 총미입고수량 */
							pg.gp_notstocked,	/* 총입고수량 */
							(pg.gp_cart_qty - pg.gp_soldout - pg.gp_wearing) AS real_cart_qty,		/* 실제주문수량 = 총신청수량 - 품절수량 - 미입고수량 */
							gp.ca_id,
							gp.gp_id,
							gp.gp_name,
							gp.gp_site
			FROM		g5_shop_group_purchase_group pg
							LEFT OUTER JOIN g5_shop_group_purchase gp on (pg.gp_id = gp.gp_id)
			$sql_search
			$sql_order
			$LIMIT
";//pg.gp_cart_qty ,	/*총주문수량*/

// echo $sql;
// exit;

// $result = sql_query($sql);

$result = sql_query($sql);

$EXCEL_STR = "
<table border='1'>
<tr>
   <td>공동구매코드</td>
   <td>상품코드</td>
   <td>상품명</td>
   <td>상품URL</td>
   <td>신청수량</td>
   <td>품절수량</td>
   <td>미입고수량</td>
   <td>입고수량</td>
   <td>주문할수량</td>
   <td>단가(USD)</td>
   <td>총합계(USD)</td>
</tr>";

while($row = mysql_fetch_array($result)) {

	$ct = sql_fetch("select * from {$g5['g5_shop_cart_table']} where total_amount_code = '$row[gp_code]' and it_id = '$row[gp_id]'");

   $EXCEL_STR .= "
   <tr>
       <td>".$row['gp_code']."</td>
	   <td>".$row[gp_id]."</td>
	   <td>".$row[gp_name]."</td>
	   <td>".$row[gp_site]."</td>
	   <td>".$row[gp_cart_qty]."</td>
	   <td>".$row[gp_soldout]."</td>
	   <td>".$row[gp_wearing]."</td>
	   <td>".$row[gp_notstocked]."</td>
	   <td>".$row[real_cart_qty]."</td>
	   <td>".$ct[ct_usd_price]."</td>
	   <td>".$ct[ct_usd_price]*$row[gp_cart_qty]."</td>	   
   </tr>
   ";
}
  
$EXCEL_STR .= "</table>";
  
echo "<meta content=\"application/vnd.ms-excel; charset=UTF-8\" name=\"Content-type\"> ";
echo $EXCEL_STR;
?>