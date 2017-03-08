<?
include_once('./_common.php');

if($_POST[mode] == "modify"){
	for($i = 0; $i < count($_POST[chk]); $i++){

		$sql = "
		update {$g5['g5_shop_cart_table']} set ct_status='".$_POST[gp_status][$_POST[chk][$i]]."' where total_amount_code='".$_POST[total_amount_code][$_POST[chk][$i]]."'
		";

		//echo $sql."</br>";
		sql_query($sql);

		/*$sql1 = "select * from {$g5['g5_shop_cart_table']} where ct_id='".$_POST[gp_id][$_POST[chk][$i]]."' ";
		$cart_res = sql_query($sql1);
		for($a = 0; $cart_row = mysql_fetch_array($cart_res); $a++){
			
			$sql = "update {$g5['g5_shop_order_table']} set od_status='".$_POST[gp_status][$_POST[chk][$a]]."' where 1 and od_id='".$cart_row[od_id]."' ";
			echo $sql."</br>";
			//sql_query($sql);
		}*/
	}
	//exit;

}else if($_POST[mode] == "del"){
	for($i = 0; $i < count($_POST[chk]); $i++){
		
		/*echo "
		delete from {$g5['g5_shop_cart_table']} where total_amount_code='".$_POST[total_amount_code][$_POST[chk][$i]]."'
		";*/

		sql_query("
		delete from {$g5['g5_shop_cart_table']} where total_amount_code='".$_POST[total_amount_code][$_POST[chk][$i]]."'
		");
	}
	//exit;
}else if($_POST[mode] == "all_del"){

	$where = " and ";
	$sql_search = "";
	if ($_POST[stx] != "") {
		if ($sfl != "") {
			$sql_search .= " $where ".$_POST[sfl]." like '%".$_POST[stx]."%' ";
			$where = " and ";
		}
		if ($_POST[save_stx] != $_POST[stx])
			$page = 1;
	}

	if ($sca != "") {
		$sql_search .= " $where (ct_type like '".$_POST[sca]."%') ";
	}


	// 상태분류
	if($ct_status){
		$sql_search .= " and ct_status='".$_POST[ct_status]."' ";
	}

	$sql = "delete from {$g5['g5_shop_cart_table']} where 1 and ct_gubun='P' $sql_search ";
	sql_query($sql);

}else if($_POST[mode] == "all_chg"){

	$where = " and ";
	$sql_search = "";
	if ($stx != "") {
		if ($sfl != "") {
			$sql_search .= " $where $sfl like '%$stx%' ";
			$where = " and ";
		}
		if ($save_stx != $stx)
			$page = 1;
	}

	if ($sca != "") {
		$sql_search .= " $where (ca_id like '$sca%') ";
	}


	// 상태분류
	if($ct_status){
		$sql_search .= " and ct_status='$ct_status' ";
	}

	$sql = "update {$g5['g5_shop_cart_table']} set ct_status='".$all_status_chg."' where 1 and ct_gubun='P' $sql_search ";
	sql_query($sql);

	/*$sql1 = "select * from {$g5['g5_shop_cart_table']} where 1 $sql_search ";
	$cart_res = sql_query($sql1);
	for($i = 0; $cart_row = mysql_fetch_array($cart_res); $i++){
		
		$sql = "update {$g5['g5_shop_order_table']} set od_status='".$_POST[all_status_chg]."' where 1 and od_id='".$cart_row[od_id]."' ";
		sql_query($sql);
	}*/
}

if($URL){
	$URL = $URL;
}else{
	$URL = "grouppurchase_appli_list.php";
}
alert("정상적으로 처리 되었습니다.", "./".$URL."?gp_code=".$gp_code."&page=".$page."&save_stx=".$save_stx."&ct_status=".$_POST[ct_status]."&sca=".$sca."&sfl=".$sfl."&stx=".$stx."&page=".$page."&save_stx=".$save_stx."&gp_dt_f=".$gp_dt_f."&gp_dt_l=".$gp_dt_l."&sfl_code=".$sfl_code."&sfl_code2=".$sfl_code2);
?>