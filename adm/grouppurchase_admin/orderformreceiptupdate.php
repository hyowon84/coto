<?php
$sub_menu = '500300';
$sub_sub_menu = '3';

include_once('./_common.php');
include_once(G5_LIB_PATH.'/mailer.lib.php');
include_once(G5_LIB_PATH.'/icode.sms.lib.php');

auth_check($auth[$sub_menu], "w");

$sql = " select * from {$g5['g5_shop_order_table']} where od_id = '$od_id' ";
$od  = sql_fetch($sql);
if(!$od['od_id'])
    alert('주문자료가 존재하지 않습니다.');

// 결제정보 반영
$sql = " update {$g5['g5_shop_order_table']}
            set od_send_cost       = '{$_POST['od_send_cost']}' 
            where od_id = '$od_id' ";
sql_query($sql);

// 주문정보
$info = get_grouppurchase_info($od_id);
if(!$info)
    alert('주문자료가 존재하지 않습니다.');

$od_status = $od['od_status'];
$cart_status = false;

// 미수가 0이고 상태가 입금대기이었다면 결제완료으로 변경
if($info['od_misu'] == 0 && $od['od_status'] == '입금대기')
{
    $od_status = '결제완료';
    $cart_status = true;
}

// 배송정보가 있으면 주문상태 배송중으로 변경
$order_status = array('결제완료', '상품준비중');
if($_POST['od_delivery_company'] && $_POST['od_invoice'] && in_array($od['od_status'], $order_status))
{
    $od_status = '배송중';
    $cart_status = true;
}

// 미수금 정보 등 반영
$sql = " update {$g5['g5_shop_order_table']}
            set od_misu         = '{$info['od_misu']}',
                od_tax_mny      = '{$info['od_tax_mny']}',
                od_vat_mny      = '{$info['od_vat_mny']}',
                od_free_mny     = '{$info['od_free_mny']}',
                od_send_cost    = '{$info['od_send_cost']}',
                od_status       = '$od_status'
            where od_id = '$od_id' ";
sql_query($sql);

// 장바구니 상태 변경
if($cart_status) {
    $sql = " update {$g5['g5_shop_cart_table']}
                set ct_status = '$od_status'
                where od_id = '$od_id' ";

    switch($od_status) {
        case '결제완료':
            $sql .= " and ct_status = '입금대기' ";
            break;
        case '배송중':
            $sql .= " and ct_status IN ('".implode("', '", $order_status)."') ";
            breask;
        default:
            ;
    }

    sql_query($sql);
}


$qstr = "sort1=$sort1&amp;sort2=$sort2&amp;sel_field=$sel_field&amp;search=$search&amp;page=$page";

goto_url("./orderform.php?od_id=$od_id&amp;$qstr");
?>
