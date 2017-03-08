<?php
$sub_menu = '400320';
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
    $sql_search .= " $where (a.ca_id like '$sca%' or a.ca_id2 like '$sca%' or a.ca_id3 like '$sca%') ";
}

if ($sfl == "")  $sfl = "it_name";

$sql_common = " from {$g5['g5_shop_item_table']} a ,
                     {$g5['g5_shop_category_table']} b
               where (a.ca_id = b.ca_id";
if ($is_admin != 'super')
    $sql_common .= " and b.ca_mb_id = '{$member['mb_id']}'";
$sql_common .= ") ";
$sql_common .= $sql_search;


if (!$sst) {
    $sst  = "it_id";
    $sod = "desc";
}
$sql_order = "order by $sst $sod";


$sql  = " select *
           $sql_common
           $sql_order ";
$result = sql_query($sql);

$cnt = @mysql_num_rows($result);
if (!$cnt)
	alert("출력할 내역이 없습니다.");

/*================================================================================
php_writeexcel http://www.bettina-attack.de/jonny/view.php/projects/php_writeexcel/
=================================================================================*/

include_once(G5_LIB_PATH.'/Excel/php_writeexcel/class.writeexcel_workbook.inc.php');
include_once(G5_LIB_PATH.'/Excel/php_writeexcel/class.writeexcel_worksheet.inc.php');

$fname = tempnam(G5_DATA_PATH, "tmp-itemlist.xls");
$workbook = new writeexcel_workbook($fname);
$worksheet = $workbook->addworksheet();

// Put Excel data
$data = array('상품코드','기본분류','분류2','분류3','간단설명','상품명','모바일상품명','금속종류(GL:금 / SL:은 / PT:백금 / PD:팔라듐 / EC:기타)','금속수량(oz,t)','제조사','원산지','브랜드','모델','발행년도','금속타입','금속함량','액면가','지름,두께','중량','상태','상품유형(1:선택업음 / 3:신상품 / 4:인기 / 5:할인 / 6:Auction)','기본설명','시중가격','판매가격','판매타입(N:고정형 / Y:실시간형 / U:실시간형 URL / A:경매형)','실시간형추가금액','실시간형추가금액(W:원 / P:% / D:$)','실시간형URL','경매 종료일시','전화문의','포인트','포인트타입(0:금액 / 1:비율)','판매자이메일','판매가능','재고수량','재고통보수량','최소구매수량','최대구매수량','과세유형(0:과세 / 1:비과세)','정렬순서','이미지1','이미지2','이미지3','이미지4','이미지5','이미지6','이미지7','이미지8', '이미지9', '이미지10', '상품유형');
$data = array_map('iconv_euckr', $data);

$col = 0;
foreach($data as $cell) {
	$worksheet->write(0, $col++, $cell);
}

for($i=1; $row=sql_fetch_array($result); $i++) {


	$row = array_map('iconv_euckr', $row);

	$j=0;

	$worksheet->write($i, $j++, $row['it_id']);
	$worksheet->write($i, $j++, $row['ca_id']);
	$worksheet->write($i, $j++, $row['ca_id2']);
	$worksheet->write($i, $j++, $row['ca_id3']);
	$worksheet->write($i, $j++, $row['it_year']);
	$worksheet->write($i, $j++, $row['it_name']);
	$worksheet->write($i, $j++, $row['it_mobile_name']);
	$worksheet->write($i, $j++, $row['it_metal_type']);
	$worksheet->write($i, $j++, $row['it_metal_don']);
	$worksheet->write($i, $j++, $row['it_maker']);
	$worksheet->write($i, $j++, $row['it_origin']);
	$worksheet->write($i, $j++, $row['it_brand']);
	$worksheet->write($i, $j++, $row['it_model']);
	$worksheet->write($i, $j++, $row['it_1']);
	$worksheet->write($i, $j++, $row['it_2']);
	$worksheet->write($i, $j++, $row['it_3']);
	$worksheet->write($i, $j++, $row['it_4']);
	$worksheet->write($i, $j++, $row['it_5']);
	$worksheet->write($i, $j++, $row['it_6']);
	$worksheet->write($i, $j++, $row['it_7']);
	$worksheet->write($i, $j++, $row['it_type']);
	$worksheet->write($i, $j++, $row['it_basic']);
	$worksheet->write($i, $j++, $row['it_cust_price']);
	$worksheet->write($i, $j++, $row['it_price']);
	$worksheet->write($i, $j++, $row['it_price_type']);
	$worksheet->write($i, $j++, $row['it_real_add_price']);
	$worksheet->write($i, $j++, $row['it_real_add_unit']);
	$worksheet->write($i, $j++, $row['it_real_url']);
	$worksheet->write($i, $j++, $row['it_last_date']);
	$worksheet->write($i, $j++, $row['it_tel_inq']);
	$worksheet->write($i, $j++, $row['it_point']);
	$worksheet->write($i, $j++, $row['it_point_type']);
	$worksheet->write($i, $j++, $row['it_sell_email']);
	$worksheet->write($i, $j++, $row['it_use']);
	$worksheet->write($i, $j++, $row['it_stock_qty']);
	$worksheet->write($i, $j++, $row['it_noti_qty']);
	$worksheet->write($i, $j++, $row['it_buy_min_qty']);
	$worksheet->write($i, $j++, $row['it_buy_max_qty']);
	$worksheet->write($i, $j++, $row['it_notax']);
	$worksheet->write($i, $j++, $row['it_order']);
	$worksheet->write($i, $j++, $row['it_img1']);
	$worksheet->write($i, $j++, $row['it_img2']);
	$worksheet->write($i, $j++, $row['it_img3']);
	$worksheet->write($i, $j++, $row['it_img4']);
	$worksheet->write($i, $j++, $row['it_img5']);
	$worksheet->write($i, $j++, $row['it_img6']);
	$worksheet->write($i, $j++, $row['it_img7']);
	$worksheet->write($i, $j++, $row['it_img8']);
	$worksheet->write($i, $j++, $row['it_img9']);
	$worksheet->write($i, $j++, $row['it_img10']);
	$worksheet->write($i, $j++, $row['it_type']);
}

$workbook->close();

header("Content-Type: application/x-msexcel; name=\"itemlist-".date("ymd", time()).".xls\"");
header("Content-Disposition: inline; filename=\"itemlist-".date("ymd", time()).".xls\"");
$fh=fopen($fname, "rb");
fpassthru($fh);
unlink($fname);
?>

</body>
</html>