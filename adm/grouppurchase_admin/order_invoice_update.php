<?php
$sub_menu = '400400';
include_once('./_common.php');
include_once(G5_PATH.'/adm/shop_admin/admin.shop.lib.php');


if(!$_POST['od_id'])alert("값이 전달되지 않았습니다.");
if(!$_POST['od_invoice'])alert("값이 전달되지 않았습니다..");

$od=sql_fetch("select * from {$g5['g5_shop_order_table']} where od_id = '{$_POST['od_id']}' ");
if(!$od['od_id'])alert("없는 주문입니다.");

if($od['od_invoice']!=$_POST['od_invoice']){

	// 미수금 정보 등 반영
	$sql = "	UPDATE	 {$g5['g5_shop_order_table']}	SET	
											od_invoice   = '{$_POST['od_invoice']}',
											od_delivery_company  = '우체국' 
						WHERE		od_id = '{$_POST['od_id']}'
	";
	sql_query($sql);

	change_admin_status($_POST['od_id'],"배송중");
	
	
	/* SMS 문자 전송 */
	$msg = "[코인즈투데이] 상품이 배송되었습니다. 우체국택배 ".$_POST['od_invoice'];
	
	send_sms($od, $msg);	

	echo "<script>alert('변경 및 SMS전송 완료');</script>";
	
	parent_reload();
}
?>