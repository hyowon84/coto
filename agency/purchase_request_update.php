<?php
include_once('./_common.php');

if(get_magic_quotes_gpc())
{
    $_GET  = array_add_callback("stripslashes", $_GET);
    $_POST = array_add_callback("stripslashes", $_POST);
}

$_GET  = array_add_callback("mysql_real_escape_string", $_GET);
$_POST = array_add_callback("mysql_real_escape_string", $_POST);


if(get_session("mem_order_se"))$member = get_member(get_session("mem_order_se"));


// 새로운 주문번호 생성
if($_POSE[w]==""){
	
	if(!get_session('ss_order_id'))set_session('ss_order_id', get_uniqid());

	// 주문번호를 얻는다.
	$od_id = get_session('ss_order_id');

}elseif($_POSE[w]=="u"){

	$od_id = $_POST[od_id];

	$row = sql_fetch(" select * from {$g5['g5_purchase_order_table']} where  od_id = '$od_id' and mb_id= '{$member['mb_id']}'");
	if(!$row[od_id])alert("존재 하지 않는 주문정보입니다.");

	if($row[od_status]!="주문")alert("진행상태가 주문일경우만 수정이 가능합니다.");

}

if(!$od_id)alert("주문번호 값이 제대로 넘어오지 않았습니다.");


// 담겨진 상품 삭제
$sql = "delete from $g5[g5_purchase_cart_table] where od_id = '$od_id'";
sql_query($sql);


$total_price = 0;


for($i=0,$k=0;$i<count($_POST[pc_item]);$i++){
	if(trim($_POST[pc_item][$i])){

		$sql = "insert into $g5[g5_purchase_cart_table] set od_id = '$od_id', 
																			pc_num = '$i', 
																			mb_id = '$member[mb_id]', 
																			pc_item = '".$_POST[pc_item][$i]."',
																			pc_item_url = '".$_POST[pc_item_url][$i]."',
																			pc_item_option = '".$_POST[pc_item_option][$i]."',
																			pc_type = '".$_POST[pc_type][$i]."',
																			pc_price = '".$_POST[pc_price][$i]."',
																			pc_price1 = '".$_POST[pc_price1][$i]."',
																			pc_qty = '".$_POST[pc_qty][$i]."',
																			pc_datetime = 	'".G5_TIME_YMDHIS."',
																			pc_ip	='$REMOTE_ADDR',
																			pc_status = '대기'";
		sql_query($sql);

		$total_price += $_POST[pc_price][$i];
		$total_price1 += $_POST[pc_price1][$i];
		$k++;
	}
}


$sql_common = " set od_site           = '$od_site',
							od_exception	= '$od_exception',
							od_name           = '$od_name',
							od_email          = '$od_email',
							od_tel            = '$od_tel',
							od_hp             = '$od_hp',
							od_zip1           = '$od_zip1',
							od_zip2           = '$od_zip2',
							od_addr1          = '$od_addr1',
							od_addr2          = '$od_addr2',
							od_addr3          = '$od_addr3',
							od_addr_jibeon    = '$od_addr_jibeon',
							od_b_name         = '$od_b_name',
							od_b_tel          = '$od_b_tel',
							od_b_hp           = '$od_b_hp',
							od_b_zip1         = '$od_b_zip1',
							od_b_zip2         = '$od_b_zip2',
							od_b_addr1        = '$od_b_addr1',
							od_b_addr2        = '$od_b_addr2',
							od_b_addr3        = '$od_b_addr3',
							od_b_addr_jibeon  = '$od_b_addr_jibeon',
							od_memo           = '$od_memo',
							od_cart_count     = '$k',
							od_cart_item_price= '$total_price',
							od_cart_price	  = '$total_price1'
							";

if($_POSE[w]==""){
	// 주문서에 입력
	$sql = " insert {$g5['g5_purchase_order_table']} 
				{$sql_common}, od_id = '$od_id', mb_id= '{$member['mb_id']}',  od_status = '주문', od_shop_memo = '', od_time = '".G5_TIME_YMDHIS."', od_ip = '$REMOTE_ADDR'";
	$result = sql_query($sql, false);
}elseif($_POSE[w]=="u"){

	$sql = " update {$g5['g5_purchase_order_table']} {$sql_common} where  od_id = '$od_id' and mb_id= '{$member['mb_id']}'";
	$result = sql_query($sql, false);
}
//주문번호제거
set_session('ss_order_id', '');

goto_url(G5_URL.'/agency/purchase_request_list.php');
?>