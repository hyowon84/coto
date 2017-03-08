<?php 
include_once("_common.php");

if(count($_POST['ct_chk'])<1)alert("최소 한개이상의 값이 넘어와야합니다.");

$total_qty = $total_soldout = $total_wearing = $total_notstocked = 0;
for($i=0;$i<count($_POST['ct_chk']);$i++){
	$k= $_POST['ct_chk'][$i];

	
	if($_POST['ct_id'][$k]){
		$ct = sql_fetch("select * from {$g5['g5_shop_cart_table']} where ct_id = '".$_POST['ct_id'][$k]."'");
		$total_qty += $ct['ct_qty'];
		$total_soldout += $_POST['ct_gp_soldout'][$k];
		$total_wearing += $_POST['ct_wearing_cnt'][$k];

		$ct_notstocked_cnt = $ct['ct_qty'] - $_POST['ct_gp_soldout'][$k] - $_POST['ct_wearing_cnt'][$k];

		sql_query("update {$g5['g5_shop_cart_table']} set ct_gp_soldout = '".$_POST['ct_gp_soldout'][$k]."', ct_wearing_cnt = '".$_POST['ct_wearing_cnt'][$k]."', ct_notstocked_cnt = '".$ct_notstocked_cnt."'  where ct_id = '".$_POST['ct_id'][$k]."'");

		//주문금액 정산
		if($ct['od_id'])ReCalOrderPriceUpdate($ct['od_id']);
	}
}

$total_notstocked =  $total_qty - $total_soldout - $total_wearing;

sql_query("update {$g5['g5_shop_group_purchase_group_table']} set gp_soldout = '".$total_soldout."', gp_wearing = '".$total_wearing."', gp_notstocked = '".$total_notstocked."' where gp_code = '".$_POST['gp_code']."' and gp_id = '".$_POST['gp_id']."'");
?>