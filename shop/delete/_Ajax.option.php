<?
include_once('./_common.php');

$op_name = explode("|", $_POST[op_name]);
for($i = 0; $i < count($op_name); $i++){
	if($op_name[$i]){
		$row = sql_fetch("select * from {$g5['g5_shop_option2_table']} where it_id='".$_POST[it_id]."' and con='".$op_name[$i]."' limit 0, 1 ");
		$price = $price + $row[price];
	}
}

if($price){
	echo $price;
}else{
	echo 0;
}
?>