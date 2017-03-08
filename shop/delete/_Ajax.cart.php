<?
include_once("./_common.php");

$idx = $_POST["idx"];
$cnt = $_POST["cnt"];
$status = $_POST["status"];


if($mode == "gp"){
	if($_POST["od_id"]){
		$od_id = $_POST["od_id"];
		$od_que = " and od_id='$od_id' ";
	}else{
		$od_id = "";
		$od_que = " ";
	}

	$row = sql_fetch("select * from {$g5['g5_shop_cart_table']}
	where it_id='$idx' $od_que order by ct_id desc limit 0, 1");

	if($row[ct_qty] > 1){
		sql_query("
		update {$g5['g5_shop_cart_table']} set
		ct_qty=ct_qty-1
		where ct_id='".$row[ct_id]."'
		");
	}else{
		sql_query("
		delete from {$g5['g5_shop_cart_table']}
		where ct_id='".$row[ct_id]."'
		");
	}
}else{

	$row = sql_fetch("select * from {$g5['g5_shop_cart_table']}
	where it_id='$idx' order by ct_id desc limit 0, 1");

	sql_query("
	update {$g5['g5_shop_cart_table']} set
	ct_qty='".$cnt."'
	where ct_id='".$row[ct_id]."'
	");

}

if($status == 1){
	echo get_sendcost1($cart_id, 0, "n");
}else{
	echo get_sendcost1($cart_id, 0, "y");
}

?>