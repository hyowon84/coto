<?php
$sub_menu = '500300';
$sub_sub_menu = '3';

include_once('./_common.php');
include_once(G5_PATH.'/adm/shop_admin/admin.shop.lib.php');


/*================================================================================
php_writeexcel http://www.bettina-attack.de/jonny/view.php/projects/php_writeexcel/
=================================================================================*/

include_once(G5_LIB_PATH.'/Excel/php_writeexcel/class.writeexcel_workbook.inc.php');
include_once(G5_LIB_PATH.'/Excel/php_writeexcel/class.writeexcel_worksheet.inc.php');

$fname = tempnam(G5_DATA_PATH, "tmp-order_tax_list.xls");
$workbook = new writeexcel_workbook($fname);
$worksheet = $workbook->addworksheet();

// Put Excel data
$data = array('수취인명', '배송메세지', '수취인우편번호', '주소.1', '수취인 e-mail 주소(현영)', '수취인이동통신', '상품명', '수량', '요금구분코드');
$data = array_map('iconv_euckr', $data);

$col = 0;
foreach($data as $cell) {
	$worksheet->write(0, $col++, $cell);
}

for ($i=0,$iCount=1; $i<count($_POST['chk']); $i++)
{
    // 실제 번호를 넘김
    $k     = $_POST['chk'][$i];
    $od_id = $_POST['od_id'][$k];

    $od = sql_fetch(" select * from {$g5['g5_shop_order_table']} where od_id = '$od_id' and gp_code <> ''");

    if (!$od) continue;

	$ct = sql_fetch("select sum(ct_qty) as total_qty from {$g5['g5_shop_cart_table']} where od_id = '".$od['od_id']."'");

	$od = array_map('iconv_euckr', $od);
	
	$mb = get_member($od['mb_id']);
	$mb = array_map('iconv_euckr', $mb);
	$mb_nick = $mb['mb_nick'];

	$it_name = iconv("utf-8","euc-kr","기념품");
	$it_state = iconv("utf-8","euc-kr","즉납");

	$worksheet->write($iCount, 0, $od['od_name']);
	$worksheet->write($iCount, 1, $mb_nick);
	$worksheet->write($iCount, 2, $od['od_zip1'].'-'.$od['od_zip2']);
	$worksheet->write($iCount, 3, $od['od_addr1'].' '.$od['od_addr2']);
	$worksheet->write($iCount, 4, ' '.$od['od_hp']);
	$worksheet->write($iCount, 5, ' '.$od['od_hp']);
	$worksheet->write($iCount, 6, $it_name);
	$worksheet->write($iCount, 7, $ct['total_qty']);
	$worksheet->write($iCount, 8, $it_state);

	$iCount++;

  
}
$workbook->close();
if($iCount==1)alert("자료가 존재하지 않습니다.");
else{
	
	header("Content-Type: application/x-msexcel; name=\"ordertaxlist-".date("ymd", time()).".xls\"");
	header("Content-Disposition: inline; filename=\"ordertaxlist-".date("ymd", time()).".xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	unlink($fname);

	exit;
}
?>