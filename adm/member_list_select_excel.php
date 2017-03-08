<?php
$sub_menu = '200100';

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
    $mb_id = $_POST['mb_id'][$k];

    $mb = sql_fetch(" select * from {$g5['member_table']} where mb_id = '$mb_id'");

    if (!$mb) continue;


	$mb = array_map('iconv_euckr', $mb);
	
	$mb_nick = $mb['mb_nick'];

	$it_name = iconv("utf-8","euc-kr","기념품");
	$it_state = iconv("utf-8","euc-kr","즉납");

	$worksheet->write($iCount, 0, $mb['mb_name']);
	$worksheet->write($iCount, 1, $mb_nick);
	$worksheet->write($iCount, 2, $mb['mb_zip1'].'-'.$mb['mb_zip2']);
	$worksheet->write($iCount, 3, $mb['mb_addr1'].' '.$mb['mb_addr2']);
	$worksheet->write($iCount, 4, ' '.$mb['mb_hp']);
	$worksheet->write($iCount, 5, ' '.$mb['mb_hp']);
	$worksheet->write($iCount, 6, $it_name);
	$worksheet->write($iCount, 7, 3);
	$worksheet->write($iCount, 8, $it_state);

	$iCount++;

  
}
$workbook->close();
if($iCount==1)alert("자료가 존재하지 않습니다.");
else{
	
	header("Content-Type: application/x-msexcel; name=\"membertaxlist-".date("ymd", time()).".xls\"");
	header("Content-Disposition: inline; filename=\"ordertaxlist-".date("ymd", time()).".xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	unlink($fname);

	exit;
}
?>