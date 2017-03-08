<?php
$sub_menu = '500300';
$sub_sub_menu = '3';

include_once('./_common.php');
include_once(G5_PATH.'/adm/shop_admin/admin.shop.lib.php');

$change_status  = $_POST['od_chg_status'];

foreach ($_POST['ct_id'] as $ct_id) {
	$list_ct_id .= $ct_id.",";
	
// 	$od = sql_fetch(" select * from {$g5['g5_shop_order_table']} where od_id = '$od_id' ");
// 	if (!$od) continue;
	/* 선택된 공구 관련 주문내역 일괄 상태변경 */
	/*
	$sql = " UPDATE	g5_shop_order	SET
								od_status = '{$change_status}'
				WHERE		mb_id = '$mb_id'	
				AND			
	";//AND		REPLACE(SUBSTRING(SUBSTRING_INDEX(gp_code, '_', 2),LENGTH(SUBSTRING_INDEX(gp_code,  '_',1-1)) + 1), '_', '') LIKE '%$gp_no%'
	sql_query($sql);
	*/
	
	/* 마지막행일때 선택된 CART 데이터 일괄 업데이트 */
	
// 	$sql = " update {$g5['g5_shop_order_table']} set od_status = '{$change_status}' where od_id = '{$od_id}' ";
// 	sql_query($sql, true);
	
// 	$sql = " update {$g5['g5_shop_cart_table']} set ct_status = '{$change_status}' where od_id = '{$od_id}'  ";
// 	sql_query($sql, true);
	
	/* 화면에서 관련값 노출도 안되있고, 자칫하면 잘못된 값을 덮어씌울가능성 있어 주석처리
	// 주문정보
	change_admin_status($od_id,$change_status);
	$info = get_grouppurchase_info($od_id);
	
	
	// 미수금 정보 등 반영
	$sql = " update {$g5['g5_shop_order_table']}	set
								od_cart_price   = '{$info['od_cart_price']}',
								od_cart_coupon  = '{$info['od_cart_coupon']}',
								od_coupon	  = '{$info['od_coupon']}',
								od_send_coupon  = '{$info['od_send_coupon']}',
								od_cancel_price = '{$info['od_cancel_price']}',
								od_send_cost	= '{$info['od_send_cost']}',
								od_misu		= '{$info['od_misu']}',
								od_tax_mny	 = '{$info['od_tax_mny']}',
								od_vat_mny	 = '{$info['od_vat_mny']}',
								od_free_mny	= '{$info['od_free_mny']}' 
				WHERE		mb_id = (SELECT	mb_id	FROM	g5_shop_order	WHERE	od_id = '$od_id')	
				AND	 	gp_code	LIKE '%$gp_no%'
			";
	sql_query($sql);
 	*/
}

if( count($_POST['ct_id']) > 0 ) {
	$list_ct_id = substr($list_ct_id,0,strlen($list_ct_id)-1);
	
	/* 공구>주문내역>카트 데이터 일괄 상태변경 */
	$sql2 = "	UPDATE	g5_shop_cart	SET
								ct_status = '{$change_status}',
								ct_history = CONCAT('{$change_status} (',now(),') | ',ct_history)
					WHERE		ct_id IN ($list_ct_id)
					AND		ct_status NOT LIKE '%취소%'
	";//
	sql_query($sql2);
}



$qstr  = "sort1=$sort1&amp;sort2=$sort2&amp;sel_field=$sel_field&amp;search=$search";
$qstr .= "&amp;od_status=$od_status";
$qstr .= "&amp;od_settle_case=$od_settle_case";
$qstr .= "&amp;od_misu=$od_misu";
$qstr .= "&amp;od_cancel_price=$od_cancel_price";
$qstr .= "&amp;od_receipt_price=$od_receipt_price";
$qstr .= "&amp;od_receipt_point=$od_receipt_point";
$qstr .= "&amp;od_receipt_coupon=$od_receipt_coupon";
//$qstr .= "&amp;page=$page";

//exit;
go_url("($list_ct_id) 해당 주문내역들의 상태가 $change_status로 변경되었습니다","orderlist_renew.php?$qstr");
?>