<?php
$sub_menu = '600100';
include_once('./_common.php');

auth_check($auth[$sub_menu], "w");


// 미수금 등의 정보
$info = get_purchase_info($od_id);

if(!$info)
    alert('주문자료가 존재하지 않습니다.');

for($i=0;$i<count($_POST[pc_num]);$i++){
	
	if($_POST['od_status']=="취소") $pc_status = "취소";
	else $pc_status = $_POST['pc_status'][$_POST[pc_num][$i]];

	$sql = " update {$g5['g5_purchase_cart_table']}
            set pc_status = '".$pc_status."'  where od_id = '$od_id' and pc_num = '".$_POST[pc_num][$i]."'";
	sql_query($sql);
}

$sql = " update {$g5['g5_purchase_order_table']}
            set od_status = '{$_POST['od_status']}'  where od_id = '$od_id' ";
sql_query($sql);

$qstr = "sort1=$sort1&amp;sort2=$sort2&amp;sel_field=$sel_field&amp;search=$search&amp;page=$page";

$url = "./purchase_form.php?od_id=$od_id&amp;$qstr";

// 1.06.06
goto_url($url);
?>
