<?php
$sub_menu = '300700';
include_once('./_common.php');

for($i=0;$i<count($_POST[dp_gubun]);$i++){

	$dp_gubun = $_POST[dp_gubun][$i];

	

	$sql = "select * from $g5[g5_domestic_price_table] where dp_gubun = '".$dp_gubun."' and dp_date = '".G5_TIME_YMD."'";
    $row = sql_fetch($sql);

	$sql_search = "set	dp_buy_price		=		'".$_POST[dp_buy_price][$dp_gubun]."',
								dp_sell_price		=		'".$_POST[dp_sell_price][$dp_gubun]."',
								dp_arrow				=		'".$_POST[dp_arrow][$dp_gubun]."',
								dp_rate				=		'".$_POST[dp_rate][$dp_gubun]."'";


	if($row[dp_gubun]){
		$sql = "update $g5[g5_domestic_price_table] $sql_search where dp_gubun = '".$dp_gubun."' and dp_date = '".G5_TIME_YMD."'";
	}else{
		$sql = "insert into $g5[g5_domestic_price_table] $sql_search, dp_gubun = '".$dp_gubun."', dp_date = '".G5_TIME_YMD."'";
	}

	sql_query($sql);
}

goto_url("./domesticprice_form.php");
?>
