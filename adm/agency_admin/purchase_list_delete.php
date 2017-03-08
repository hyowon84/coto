<?php
$sub_menu = '600100';
include_once('./_common.php');

//print_r2($_POST); exit;

for ($i=0; $i<count($_POST['chk']); $i++)
{
    // 실제 번호를 넘김
    $k     = $_POST['chk'][$i];
    $od_id = $_POST['od_id'][$k];


    $sql = " delete from {$g5['g5_purchase_cart_table']} where od_id = '$od_id' ";
    sql_query($sql);

    $sql = " delete from {$g5['g5_purchase_order_table']} where od_id = '$od_id' ";
    sql_query($sql);
}

$qstr  = "sort1=$sort1&amp;sort2=$sort2&amp;sel_field=$sel_field&amp;search=$search";
$qstr .= "&amp;od_status=$search_od_status";
$qstr .= "&amp;od_settle_case=$od_settle_case";
$qstr .= "&amp;od_misu=$od_misu";
$qstr .= "&amp;od_cancel_price=$od_cancel_price";
$qstr .= "&amp;od_receipt_price=$od_receipt_price";
$qstr .= "&amp;od_receipt_point=$od_receipt_point";
$qstr .= "&amp;od_receipt_coupon=$od_receipt_coupon";

goto_url("./purchase_list.php?$qstr");
?>