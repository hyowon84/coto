<?
$sub_menu = '500300';
$sub_sub_menu = '3';

include_once('./_common.php');

auth_check($auth[$sub_menu], "r");


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

$where[] = " gp_code <> '' ";
$where[] = " od_tax <> '' ";

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


$sql_common = " from {$g5['g5_shop_order_table']} $sql_search $sql_group ";

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
           $sql_order  ";

$result = sql_query($sql);



/*================================================================================
php_writeexcel http://www.bettina-attack.de/jonny/view.php/projects/php_writeexcel/
=================================================================================*/

include_once(G5_LIB_PATH.'/Excel/php_writeexcel/class.writeexcel_workbook.inc.php');
include_once(G5_LIB_PATH.'/Excel/php_writeexcel/class.writeexcel_worksheet.inc.php');

$fname = tempnam(G5_DATA_PATH, "tmp-order_tax_list.xls");
$workbook = new writeexcel_workbook($fname);
$worksheet = $workbook->addworksheet();

// Put Excel data
$data = array('H', '거래일자', '상품명', '공급가액', '부가세', '봉사료', '거래총액', '거래자구분', '주민번호/핸드폰/사업자번호', '상점연락처');
$data = array_map('iconv_euckr', $data);

$col = 0;
foreach($data as $cell) {
	$worksheet->write(0, $col++, $cell);
}


for($i=1; $row=sql_fetch_array($result); $i++) {
	$ct_send_cost = iconv_euckr(($row['ct_send_cost'] ? '착불' : '선불'));
	$row = array_map('iconv_euckr', $row);

	$amount['order'] = $row['od_cart_price'] + $row['od_send_cost'] + $row['od_send_cost2'];

	$od_tax_price = CeilGe($amount['order']*0.06);

	$od_it_price = $amount['order'] - $od_tax_price;

	$od_tax1 = $od_it_price * 0.9;
	$od_tax2 = $od_it_price * 0.1;

	$worksheet->write($i, 0, 'D');
	$worksheet->write($i, 1, ' '.date("Ymd",strtotime($row['od_time'])));
	$worksheet->write($i, 2, $row['gp_code']);
	$worksheet->write($i, 3, $od_tax1);
	$worksheet->write($i, 4, $od_tax2);
	$worksheet->write($i, 5, $od_tax_price);
	$worksheet->write($i, 6, $amount['order']);
	$worksheet->write($i, 7, $row['od_tax']);
	$worksheet->write($i, 8, ' '.$row['od_tax_hp']);
	$worksheet->write($i, 9, '02-1544-0001');
}

$workbook->close();

header("Content-Type: application/x-msexcel; name=\"orderlist-".date("ymd", time()).".xls\"");
header("Content-Disposition: inline; filename=\"orderlist-".date("ymd", time()).".xls\"");
$fh=fopen($fname, "rb");
fpassthru($fh);
unlink($fname);

exit;
?>