<?php
include_once('./_common.php');

$idx = $_POST[idx];
$op_price = $_POST[op_price];
$ct_price = $_POST[ct_price];

if($idx){
	/*
	$sql = "select * from {$g5['g5_shop_cart_table']} where ct_id='".$idx."' limit 0, 1 ";
	$row = sql_fetch($sql);
	$ct_qty = $row[ct_qty] - $row[ct_buy_qty];
	$num = $ct_price - $op_price;
	$price = $num / $ct_qty;
	*/

	$ct = sql_fetch("select ct_id, od_id from {$g5['g5_shop_cart_table']} where ct_id = '".$idx."'");
	if($ct['ct_id']){
		$sql = "
			update {$g5['g5_shop_cart_table']} set
						ct_price='".$ct_price."'
			where ct_id='".$idx."'
		";
		$result = sql_query($sql);

		if($ct['od_id']){
			// �ֹ��� ����
			$sum_sql = " select SUM(IF(io_type = 1, (io_price * (ct_qty-ct_gp_soldout)), ((ct_price + io_price) * (ct_qty-ct_gp_soldout)))) as price,
					COUNT(it_id) as cnt,
					SUM( IF( ct_notax = 0, ( IF(io_type = 1, (io_price * (ct_qty-ct_gp_soldout)), ( (ct_price + io_price) * (ct_qty-ct_gp_soldout)) ) - cp_price ), 0 ) ) as tax_mny,
					SUM( IF( ct_notax = 1, ( IF(io_type = 1, (io_price * (ct_qty-ct_gp_soldout)), ( (ct_price + io_price) * (ct_qty-ct_gp_soldout)) ) - cp_price ), 0 ) ) as free_mny
				from {$g5['g5_shop_cart_table']}
				where od_id = '".$ct['od_id']."' ";
			$sum = sql_fetch($sum_sql);

			$cart_count1 = $sum[cnt];

			$tot_ct_price1 = $sum['price'];
			$sql2 = "update {$g5['g5_shop_order_table']}	set od_cart_price     = '$tot_ct_price1' where od_id = '".$ct['od_id']."'";

			$result = sql_query($sql2);
		}
		
		
		if($result) {
			echo "ok";
		} else {
			echo "fail $sql \r\n $sql2";
		}
	}
}
?>