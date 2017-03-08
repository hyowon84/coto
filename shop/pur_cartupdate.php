<?php
include_once('./_common.php');

// print_r2($_POST); exit;

if($buy_kind == "공동구매"){
	$orderform_url = "orderform_gp.php";
}else if($buy_kind == "구매대행"){
	$orderform_url = "orderform_pur.php";
}else{
	$orderform_url = "orderform.php";
}

// cart id 설정
set_cart_id($sw_direct);

if($sw_direct)
    $tmp_cart_id = get_session('ss_cart_direct');
else
    $tmp_cart_id = get_session('ss_cart_id');

// 브라우저에서 쿠키를 허용하지 않은 경우라고 볼 수 있음.
if (!$tmp_cart_id)
{
    alert('더 이상 작업을 진행할 수 없습니다.\\n\\n브라우저의 쿠키 허용을 사용하지 않음으로 설정한것 같습니다.\\n\\n브라우저의 인터넷 옵션에서 쿠키 허용을 사용으로 설정해 주십시오.\\n\\n그래도 진행이 되지 않는다면 쇼핑몰 운영자에게 문의 바랍니다.');
}


// 레벨(권한)이 상품구입 권한보다 작다면 상품을 구입할 수 없음.
if ($member['mb_level'] < $default['de_level_sell'])
{
    alert('상품을 구입할 수 있는 권한이 없습니다.');
}

if($act == "buy")
{
    if(!count($_POST['ct_chk']))
        alert("주문하실 상품을 하나이상 선택해 주십시오.");

    $fldcnt = count($_POST['it_id']);
    for($i=0; $i<$fldcnt; $i++) {
        $ct_chk = $_POST['ct_chk'][$i];
        if($ct_chk) {
            $it_id = $_POST['it_id'][$i];
            $sql = " update {$g5['g5_shop_cart_table']}
                        set ct_select = '1'
                        where it_id = '$it_id' and od_id = '$tmp_cart_id' ";
            sql_query($sql);
        }
    }

    if ($is_member) // 회원인 경우
        goto_url(G5_SHOP_URL.'/'.$orderform_url);
    else
        goto_url(G5_BBS_URL.'/login.php?url='.urlencode(G5_SHOP_URL.'/'.$orderform_url));
}
else if ($act == "alldelete") // 모두 삭제이면
{
    $sql = " delete from {$g5['g5_shop_cart_table']}
              where od_id = '$tmp_cart_id' ";
    sql_query($sql);
}
else if ($act == "seldelete") // 선택삭제
{
    if(!count($_POST['ct_chk']))
        alert("삭제하실 상품을 하나이상 선택해 주십시오.");

    $fldcnt = count($_POST['it_id']);
    for($i=0; $i<$fldcnt; $i++) {
        $ct_chk = $_POST['ct_chk'][$i];
        if($ct_chk) {
            $it_id = $_POST['it_id'][$i];
            $sql = " delete from {$g5['g5_shop_cart_table']} where it_id = '$it_id' and od_id = '$tmp_cart_id' ";
            sql_query($sql);
        }
    }
}
else // 장바구니에 담기
{
    if (!$_POST['gp_id'])
        alert('구매 하실 상품 정보가 넘어오지 않았습니다.');

    $ct_count = 0;


	$it_id = $_POST['gp_id'];

	if ($_POST['ct_qty'] < 1)
		alert('수량은 1 이상 입력해 주십시오.');

	// 상품정보
	$sql = " select * from {$g5['g5_shop_group_purchase_table']} where gp_id = '$it_id' ";
	$it = sql_fetch($sql);
	if(!$it['gp_id'])
		alert('상품정보가 존재하지 않습니다.');

	$ct_select = 1;


	// 장바구니에 Insert
	$comma = '';
	$sql = " INSERT INTO {$g5['g5_shop_cart_table']}
					( od_id, ct_gubun, mb_id, it_id, it_name, ct_status, ct_price, ct_point, ct_point_use, ct_stock_use, ct_option, ct_qty, ct_time, ct_ip, ct_send_cost, ct_direct, ct_select, ct_payment, ct_type, ct_time_code )
				VALUES ";

	$sql .= "( '$tmp_cart_id', 'P', '{$member['mb_id']}', '{$it['gp_id']}', '".addslashes($it['gp_name'])."', '쇼핑', '{$_POST['it_price']}', '0', '0', '0', '$io_value', '".$_POST['ct_qty']."', '".G5_TIME_YMDHIS."', '$REMOTE_ADDR', '0', '$sw_direct', '$ct_select', '{$_POST['ct_payment']}', '$ca_id', ".strtotime(G5_TIME_YMDHIS)." )";

	sql_query($sql);
}

// 바로 구매일 경우
if ($sw_direct)
{
    if ($is_member)
    {
    	goto_url(G5_SHOP_URL."/".$orderform_url."?sw_direct=$sw_direct&buy_kind=$buy_kind");
    }
    else
    {
    	goto_url(G5_BBS_URL."/login.php?url=".urlencode(G5_SHOP_URL."/".$orderform_url."?sw_direct=$sw_direct&buy_kind=$buy_kind"));
    }
}
else
{
    goto_url(G5_SHOP_URL.'/cart_pur.php');
}
?>
