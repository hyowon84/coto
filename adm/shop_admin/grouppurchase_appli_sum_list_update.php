<?php
$sub_menu = "500300";
include_once('./_common.php');

check_demo();


if($_POST['w']=="u"){
	if(!$_POST['gp_code'])alert("값이 넘어오지 않았습니다.");
	if(!$_POST['gp_id'])alert("값이 넘어오지 않았습니다.");

	$gp_code = $_POST['gp_code'];
	$gp_id = $_POST['gp_id'];

	$row = sql_fetch("select * from {$g5['g5_shop_group_purchase_group_table']} where gp_code = '".$gp_code."' and gp_id = '".$gp_id."'");
	if($row['gp_id']){

		$gp_soldout = $_POST['gp_soldout'];
		$gp_wearing = $_POST['gp_wearing'];

		$gp_orderqty = $row['gp_cart_qty'] - $gp_soldout;

		if($gp_orderqty < $gp_wearing)$gp_wearing = $gp_orderqty;
	
		$gp_notstocked = $gp_orderqty - $gp_wearing;
	

		if($row['gp_cart_qty'] < $gp_soldout)alert("신청수보다 품절수가 높을 수 없습니다.");
		if($gp_notstocked<0)alert("미입고 수량이 주문 수량보다 많습니다.");


		sql_query("update {$g5['g5_shop_group_purchase_group_table']} set gp_soldout = '".$gp_soldout."', gp_wearing = '".$gp_wearing."', gp_notstocked = '".$gp_notstocked."' where gp_code = '".$gp_code."' and gp_id = '".$gp_id."'");

		// 카트 품절 및 입고 미입고 정산
		updateGroupPurhchaseCartQtyCal($gp_code,$gp_id,$gp_soldout, $gp_wearing);

	}else alert("값이 존재하지 않습니다.");
}
?>
