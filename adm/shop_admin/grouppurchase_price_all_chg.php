<?
include_once('./_common.php');

$sql = "
	select * from {$g5['g5_shop_group_purchase_table']}
";
$res = sql_query($sql);

for($i = 0; $row = mysql_fetch_array($res); $i++){
	$gp_price = ceil(getGroupPurchaseBasicPrice($row[gp_id]) / 100) * 100;
	$gp_card_price = ceil(getGroupPurchaseBasicPrice1($row[gp_id]) / 100) * 100;
	$sql = "
		update {$g5['g5_shop_group_purchase_table']} set
		gp_price='".$gp_price."',
		gp_card_price='".$gp_card_price."'
		where gp_id='".$row[gp_id]."'
	";
	//echo $sql."</br>";
	sql_query($sql);
}

alert("모든 가격이 현재가로 변경 되었습니다.", "./grouppurchaselist.php");
?>