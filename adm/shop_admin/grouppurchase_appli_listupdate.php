<?
include_once('./_common.php');

if($_POST[mode] == "modify"){
	for($i = 0; $i < count($_POST[chk]); $i++){
		$cart_que = ",ct_status='".$_POST[gp_status][$_POST[chk][$i]]."' ";

		$sql = "
		update {$g5['g5_shop_cart_table']} set ct_status='".$_POST[gp_status][$_POST[chk][$i]]."', ct_qty='".$_POST[ct_qty][$_POST[chk][$i]]."', ct_buy_qty='".$_POST[ct_buy_qty][$_POST[chk][$i]]."' $cart_que where ct_id='".$_POST[ct_id][$_POST[chk][$i]]."'
		";

		//echo $sql."</br>";
		sql_query($sql);

		$sql1 = "select * from {$g5['g5_shop_cart_table']} where ct_id='".$_POST[ct_id][$_POST[chk][$i]]."' ";
		$cart_res = sql_query($sql1);
		for($a = 0; $cart_row = mysql_fetch_array($cart_res); $a++){
			
			$sql = "update {$g5['g5_shop_order_table']} set od_status='".$_POST[gp_status][$_POST[chk][$a]]."' where 1 and od_id='".$cart_row[od_id]."' ";
			//echo $sql."</br>";
			sql_query($sql);
		}
	}

	
}
/* MULTI 가격수정 */
else if($_POST[mode] == "modify_price"){
	for($i = 0; $i < count($_POST[chk]); $i++){
		$ct_id = $_POST[ct_id][$_POST[chk][$i]];
		
		$UPD_SQL = "UPDATE		g5_shop_cart		SET
												ct_price	=	'$txt_price'
								WHERE		ct_id='".$ct_id."'
		";
		sql_fetch($UPD_SQL);
		
		$sel_sql = "SELECT	total_amount_code,
											ct_id,
											it_id
							FROM		g5_shop_cart
							WHERE	ct_id='".$_POST[ct_id][$_POST[chk][$i]]."'
		";
		$ct = sql_fetch($sel_sql);
		
		if($ct['ct_id']){
			//삭제하지 않고 판매취소로 변경
// 			sql_query("delete from {$g5['g5_shop_cart_table']} where ct_id='".$_POST[ct_id][$_POST[chk][$i]]."'");

			// 코드 및 상품에 따른 그룹화
			if($ct['total_amount_code']) purchaseItemGorupUpdate($ct['total_amount_code'],$ct['it_id']);
		}
	}
}
/* MULTI 판매취소 */
else if($_POST[mode] == "denysell"){
	for($i = 0; $i < count($_POST[chk]); $i++){
		$ct_id = $_POST[ct_id][$_POST[chk][$i]];
		
		$UPD_SQL = "UPDATE	{$g5['g5_shop_cart_table']}	SET
										ct_status	=	'판매취소',
										ct_history = CONCAT('판매취소 (',now(),') | ',ct_history)
						WHERE		ct_id='".$ct_id."'
		";
		sql_fetch($UPD_SQL);
		
		$sel_sql = "SELECT	total_amount_code,
											ct_id,
											it_id
							FROM		g5_shop_cart
							WHERE	ct_id='".$_POST[ct_id][$_POST[chk][$i]]."'
		";
		$ct = sql_fetch($sel_sql);
		
		if($ct['ct_id']){
			//삭제하지 않고 판매취소로 변경
// 			sql_query("delete from {$g5['g5_shop_cart_table']} where ct_id='".$_POST[ct_id][$_POST[chk][$i]]."'");

			// 코드 및 상품에 따른 그룹화
			if($ct['total_amount_code']) purchaseItemGorupUpdate($ct['total_amount_code'],$ct['it_id']);
		}
	}
}
else if($_POST[mode] == "all_del"){
	/*
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
		$sql_search .= " and ct_gp_status='".$_POST[ct_status]."' ";
	}

	if($gp_code){
		$sql_search .= " and total_amount_code='$gp_code' ";
	}

	$ct_result = sql_query("select total_amount_code, ct_id, it_id  from {$g5['g5_shop_cart_table']} where (1) ".$sql_search);
	for($i=0;$ct_row = sql_fetch_array($ct_result);$i++){

		$sql = "delete from {$g5['g5_shop_cart_table']} where ct_id = '".$ct_row['ct_id']."' ";
		sql_query($sql);

		if($ct_row['total_amount_code'])purchaseItemGorupUpdate($ct_row['total_amount_code'],$ct_row['it_id']);
	}
	*/
}else if($_POST[mode] == "all_chg"){

	$where = " and ";
	$sql_search = "";
	if ($stx != "") {
		if ($sfl != "") {

			if($sfl == "mb_nick"){
				$nick_row = sql_fetch("select * from {$g5['member_table']} where mb_nick='".$stx."' ");
				$sql_search .= " $where mb_id like '".$nick_row[mb_id]."' ";
			}else{
				$sql_search .= " $where $sfl like '%$stx%' ";
			}
			$where = " and ";
		}
		if ($save_stx != $stx)
			$page = 1;
	}

	if($sfl_code2 != ""){
		$sql_search .= " and total_amount_code='".$sfl_code2."' ";
	}

	if ($sca != "") {
		$sql_search .= " $where (b.ca_id like '$sca%') ";
	}

	if ($sca != "") {
		$sql_search .= " $where (ct_type like '".$_POST[sca]."%') ";
	}


	// 상태분류
	if($ct_status){
		$sql_search .= " and ct_status='".$_POST[ct_status]."' ";
	}

	if($gp_dt_f){
		$sql_search .= " and ct_time > '".date("Y-m-d H:i:s", strtotime($gp_dt_f))."' ";
	}

	if($gp_dt_l){
		$sql_search .= " and ct_time < '".date("Y-m-d", strtotime($gp_dt_l))." 59:59:59' ";
	}

	if($gp_code){
		$sql_search .= " and total_amount_code='$gp_code' ";
	}

	$sql = "update {$g5['g5_shop_cart_table']} set ct_status='".$_POST[all_status_chg]."' where 1 and ct_gubun='P' $sql_search ";
	sql_query($sql);

	$sql1 = "select * from {$g5['g5_shop_cart_table']} where 1 $sql_search ";
	$cart_res = sql_query($sql1);
	for($i = 0; $cart_row = mysql_fetch_array($cart_res); $i++){
		
		$sql = "update {$g5['g5_shop_order_table']} set od_status='".$_POST[all_status_chg]."' where 1 and od_id='".$cart_row[od_id]."' ";
		sql_query($sql);
	}
}

if($URL){
	$URL = $URL;
}else{
	$URL = "grouppurchase_appli_list.php";
}
alert("정상적으로 처리 되었습니다.", "./".$URL."?gp_code=".$gp_code."&page=".$page."&save_stx=".$save_stx."&ct_status=".$_POST[ct_status]."&sca=".$sca."&sfl=".$sfl."&stx=".$stx."&page=".$page."&save_stx=".$save_stx."&gp_dt_f=".$gp_dt_f."&gp_dt_l=".$gp_dt_l."&sfl_code=".$sfl_code."&sfl_code2=".$sfl_code2);
?>