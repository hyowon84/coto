<?php
$sub_menu = '700300';
include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

//print_r2($_GET); exit;

/*
function multibyte_digit($source)
{
    $search  = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
    $replace = array("０","１","２","３","４","５","６","７","８","９");
    return str_replace($search, $replace, (string)$source);
}
*/

function conv_telno($t)
{
    // 숫자만 있고 0으로 시작하는 전화번호
    if (!preg_match("/[^0-9]/", $t) && preg_match("/^0/", $t))  {
        if (preg_match("/^01/", $t)) {
            $t = preg_replace("/([0-9]{3})(.*)([0-9]{4})/", "\\1-\\2-\\3", $t);
        } else if (preg_match("/^02/", $t)) {
            $t = preg_replace("/([0-9]{2})(.*)([0-9]{4})/", "\\1-\\2-\\3", $t);
        } else {
            $t = preg_replace("/([0-9]{3})(.*)([0-9]{4})/", "\\1-\\2-\\3", $t);
        }
    }

    return $t;
}







$where = " and ";
$sql_search = "";
if ($stx != "") {
    if ($sfl != "") {
        $sql_search .= " $where $sfl like '%$stx%' ";
        $where = " and ";
    }
    if ($save_stx != $stx)
        $page = 1;
}

if ($sca != "") {
    //$sql_search .= " $where (a.ca_id like '$sca%' or a.ca_id2 like '$sca%' or a.ca_id3 like '$sca%') ";
	$sql_search .= " $where (a.ca_id like '$sca%') ";
}

if ($sfl == "")  $sfl = "it_name";


$sql_common = "
	FROM		g5_shop_group_purchase a
					LEFT OUTER JOIN g5_shop_category b ON (a.ca_id = b.ca_id)
	WHERE	1=1
";

/* 필요없어 보이는데.. */
if ($is_admin != 'super') $sql_common .= " AND b.ca_mb_id = '{$member['mb_id']}'";

$sql_common .= $sql_search;

if($event_yn == 'Y') {
	$sql_common .= " AND	a.event_yn = 'Y' ";
}



if (!$sst) {
    $sst  = "gp_id";
    $sod = "desc";
}
$sql_order = "order by $sst $sod";


$rows = $excel_row;
$page = $excel_page;

$from_record = ($page - 1) * $rows; // 시작 열을 구함



$sql  = " SELECT *
			$sql_common
			
			$sql_order 
			limit $from_record, $rows ";

$result = sql_query($sql);

$cnt = @mysql_num_rows($result);
if (!$cnt)
	alert("출력할 내역이 없습니다.");

/*================================================================================
php_writeexcel http://www.bettina-attack.de/jonny/view.php/projects/php_writeexcel/
=================================================================================*/

include_once(G5_LIB_PATH.'/Excel/php_writeexcel/class.writeexcel_workbook.inc.php');
include_once(G5_LIB_PATH.'/Excel/php_writeexcel/class.writeexcel_worksheet.inc.php');

$fname = tempnam(G5_DATA_PATH, "tmp-orderlist.xls");
$workbook = new writeexcel_workbook($fname);
$worksheet = $workbook->addworksheet();

// Put Excel data
$data = array('상품코드(추가건 외 수정불가능)','상품명','수량','상품주소', '수수료', '관세','출력순서','판매가능(1:판매가능/빈란:판매불가능)','금액(원화)', '상품유형', '분류', '2차 분류', '3차 분류','코인투데이체크');
$data = array_map('iconv_euckr', $data);

$col = 0;
foreach($data as $cell) {
	$worksheet->write(0, $col++, $cell);
}

for($i=1; $row=sql_fetch_array($result); $i++) {

	$row = array_map('iconv_euckr', $row);

	$gp_type = "";
	$gp_price = getGroupPurchaseBasicPrice($row[gp_id]);

	if($row[gp_type1]=="1")$gp_type = "1";
	elseif($row[gp_type1]=="2")$gp_type = "2";
	elseif($row[gp_type1]=="3")$gp_type = "3";
	elseif($row[gp_type1]=="4")$gp_type = "4";
	elseif($row[gp_type1]=="5")$gp_type = "5";

	$j=0;
	$worksheet->write($i, $j++, $row['gp_id']);
	$worksheet->write($i, $j++, $row['gp_name']);
	$worksheet->write($i, $j++, $row['jaego']);
	$worksheet->write($i, $j++, $row['gp_site']);
	$worksheet->write($i, $j++, $row['gp_charge']);
	$worksheet->write($i, $j++, $row['gp_duty']);
	$worksheet->write($i, $j++, $row['gp_order']);
	$worksheet->write($i, $j++, $row['gp_use']);
	//$worksheet->write($i, $j++, $gp_type);
	$worksheet->write($i, $j++, number_format($gp_price));
	$worksheet->write($i, $j++, $row['it_type']);
	$worksheet->write($i, $j++, $row['ca_id']);
	$worksheet->write($i, $j++, $row['ca_id2']);
	$worksheet->write($i, $j++, $row['ca_id3']);
	$worksheet->write($i, $j++, $row['event_yn']);	
}

$workbook->close();

header("Content-Type: application/x-msexcel; name=\"purchaselist-".date("ymd", time())."_".$page.".xls\"");
header("Content-Disposition: inline; filename=\"purchaselist-".date("ymd", time())."_".$page.".xls\"");
$fh=fopen($fname, "rb");
fpassthru($fh);
unlink($fname);
?>

</body>
</html>