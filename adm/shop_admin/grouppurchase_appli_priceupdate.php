<?
include_once('./_common.php');

$cart_res = sql_query("select * from {$g5['g5_shop_cart_table']} ");

for($i = 0; $row = mysql_fetch_array($cart_res); $i++){
	if($row['ct_status']== "쇼핑"){
		// 상품가격 업데이트
		UpdateGroupPurchasePrice($row[it_id]);

		$sql = " select SUM(ct_qty) as qty,
					SUM(ct_buy_qty) as buy_qty
				from {$g5['g5_shop_cart_table']}
				where it_id = '{$row['it_id']}'
				and ct_id = '".$row[ct_id]."'
				";

		$sum = sql_fetch($sql);

		$basicPrice = getGroupPurchaseQtyBasicPrice($row[it_id],$sum['qty']-$sum['buy_qty']);
		$ct_usd_price = getGroupPurchaseQtyBasicUSD($row[it_id],$sum['qty']-$sum['buy_qty']);

		$sql = "update $g5[g5_shop_cart_table] set ct_price = '".$basicPrice."',ct_usd_price = '".$ct_usd_price."' where it_id = '{$row['it_id']}' and ct_id='".$row[ct_id]."' ";
		//echo $sql."</br>";
		sql_query($sql);
	}
}

alert("현재 시세로 업데이트 되었습니다.", "./grouppurchase_appli_list.php?page=".$page."&save_stx=".$save_stx."&ct_status=".$ct_status."&sca=".$sca."&gp_dt_f=".$gp_dt_f."&gp_dt_l=".$gp_dt_l."&sfl=".$sfl."&stx=".$stx."&sfl_code=".$sfl_code."&sfl_code2=".$sfl_code2);
?>