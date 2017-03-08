<?php
$sub_menu = '500300';
$sub_sub_menu = '3';

include_once('./_common.php');

auth_check($auth[$sub_menu], "w");

$od = sql_fetch("select od_id from {$g5['g5_shop_order_table']} where od_id = '".$_POST['od_idx']."'");

if($od['od_id']){
	sql_query("update {$g5['g5_shop_order_table']} set od_tax_state = '".$_POST['od_tax_state']."' where od_id = '".$od['od_id']."'");
}
?>