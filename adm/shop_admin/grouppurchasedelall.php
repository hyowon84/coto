<?
include_once("./_common.php");

if($_POST[HTTP_CHK] == "CHK_OK") alert("잘못된 접근 방식입니다.");

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
    $sql_search .= " $where (ca_id like '$sca%') ";
}

if ($sfl == "")  $sfl = "it_name";

$sql = "delete from {$g5['g5_shop_group_purchase_table']} where 1 $sql_search ";
//echo $sql;
//exit;
sql_query($sql);

alert("상품 전체가 삭제 되었습니다.", "./grouppurchaselist.php");
?>