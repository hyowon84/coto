<?
include_once('./_common.php');

if($_POST[status] == "취소"){

	$ct = sql_fetch("select ct_id, it_id from $g5[g5_shop_cart_table] where ct_id='".$_POST[ct_id]."'");

	if($ct[it_id]){
		// 상품가격 업데이트
		UpdateGroupPurchasePrice($ct[it_id]);
		
		$sql = " select SUM(ct_qty) as qty,
					SUM(ct_buy_qty) as buy_qty
				from {$g5['g5_shop_cart_table']}
				where ct_id='".$_POST[ct_id]."'";

		$sum = sql_fetch($sql);

		$basicPrice = getGroupPurchaseQtyBasicPrice($ct[it_id],$sum['qty']-$sum['buy_qty']);
		$ct_usd_price = getGroupPurchaseQtyBasicUSD($ct[it_id],$sum['qty']-$sum['buy_qty']);


		$sql = "update $g5[g5_shop_cart_table] set ct_price = '".$basicPrice."',ct_usd_price = '".$ct_usd_price."', ct_gp_status='y' where ct_id='".$_POST[ct_id]."'";
		sql_query($sql);
	}
}else if($_POST[status] == "buy_status"){
	$row = sql_fetch("select * from {$g5['g5_shop_cart_table']} where ct_id='".$_POST[idx]."' ");
	if($row[buy_status] == "n"){
		sql_query("update {$g5['g5_shop_cart_table']} set buy_status='y' where ct_id='".$_POST[idx]."' ");
		echo "y";
	}else{
		sql_query("update {$g5['g5_shop_cart_table']} set buy_status='n'where ct_id='".$_POST[idx]."' ");
		echo "n";
	}
}else if($_POST[status] == "gpcode_status"){
	$gpcode_row = sql_fetch("select * from g5_group_cnt_pay where gubun_code='".$_POST[gp_code]."' order by no desc ");
	echo '<option value="'.$gpcode_row[group_code].'"';
	if($sfl_code2 == $gpcode_row[group_code]){
		echo " selected ";
	}
	echo '>'.$gpcode_row[group_code].'</option>';

	$gpcode_res = sql_query("select * from {$g5['g5_total_amount_table']} where dealer='".$_POST[gp_code]."' and  type_code!='".$gpcode_row[group_code]."' order by no desc ");
	for($i = 0;$gpcode_row = mysql_fetch_array($gpcode_res); $i++){
		echo '<option value="'.$gpcode_row[type_code].'"';
		if($sfl_code2 == $gpcode_row[type_code]){
			echo " selected ";
		}
		echo '>'.$gpcode_row[type_code].'</option>';
	}

}else{
	sql_query("
	update {$g5['g5_shop_cart_table']} set
	ct_gp_status='n'
	where ct_id='".$_POST[ct_id]."'
	");
}
?>