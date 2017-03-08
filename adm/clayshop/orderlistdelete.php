<?php
$sub_menu = '500300';
$sub_sub_menu = '3';

include_once('./_common.php');

//print_r2($_POST); exit;

foreach ($_POST['chk'] as $ct_id) {
	$list_ct_id .= $ct_id.",";
}

if( count($_POST['chk']) > 0 ) {
	$list_ct_id = substr($list_ct_id,0,strlen($list_ct_id)-1);
	
	/* 공구>주문내역>카트 데이터 일괄 상태변경 */

	$sql = "	UPDATE	clay_order	SET
									stats = '99'
					WHERE		number IN ($list_ct_id) 
	";
	sql_query($sql);
}

	/*
    // 실제 번호를 넘김
    $k     = $_POST['chk'][$i];
    $od_id = $_POST['od_id'][$k];

    $od = sql_fetch(" select * from {$g5['g5_shop_order_table']} where od_id = '$od_id' ");
    if (!$od) continue;

    $data = serialize($od);

    $sql = " insert {$g5['g5_shop_order_delete_table']} set de_key = '$od_id', de_data = '$data', mb_id = '{$member['mb_id']}', de_ip = '{$_SERVER['REMOTE_ADDR']}', de_datetime = '".G5_TIME_YMDHIS."' ";
    sql_query($sql, true);

    $sql = " delete from {$g5['g5_shop_order_table']} where od_id = '$od_id' ";
    sql_query($sql);
    */
	

$qstr  = "sort1=$sort1&amp;sort2=$sort2&amp;sel_field=$sel_field&amp;search=$search";
$qstr .= "&amp;od_status=$search_od_status";
$qstr .= "&amp;od_settle_case=$od_settle_case";
$qstr .= "&amp;od_misu=$od_misu";
$qstr .= "&amp;od_cancel_price=$od_cancel_price";
$qstr .= "&amp;od_receipt_price=$od_receipt_price";
$qstr .= "&amp;od_receipt_point=$od_receipt_point";
$qstr .= "&amp;od_receipt_coupon=$od_receipt_coupon";

goto_url("./orderlist.php?$qstr");
?>