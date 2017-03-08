<?php
include_once('./_common.php');

if(!$member['mb_id'])alert("회원만 가능합니다.");

$row = sql_fetch("select count(*) as cnt from {$g5['g5_shop_group_purchase_addr_table']} where mb_id = '$member[mb_id]'");

$sql_common = "set		gp_tax					=			'".$_POST['gp_tax']."',
								gp_tax_number		=			'".$_POST['gp_tax_number']."',
								gp_sel_addr	 			=			'".$_POST['gp_sel_addr']."',
								gp_name					=			'".$_POST['gp_name']."',
								gp_hp					=			'".$_POST['gp_hp']."',
								gp_zip1					=			'".$_POST['gp_zip1']."',
								gp_zip2					=			'".$_POST['gp_zip2']."',
								gp_addr1				=			'".$_POST['gp_addr1']."',
								gp_addr2				=			'".$_POST['gp_addr2']."',
								gp_addr3				=			'".$_POST['gp_addr3']."',
								gp_addr_jibeon			=			'".$_POST['gp_addr_jibeon']."'";
if($row['cnt']){
	$sql = "update {$g5['g5_shop_group_purchase_addr_table']} $sql_common where mb_id = '$member[mb_id]'";
	sql_query($sql);
}else{
	$sql = "insert into {$g5['g5_shop_group_purchase_addr_table']} $sql_common, mb_id = '$member[mb_id]'";
	sql_query($sql);
}

goto_url("cart_gp.php");
?>