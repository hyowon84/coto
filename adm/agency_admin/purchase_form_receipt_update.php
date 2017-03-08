<?php
$sub_menu = '400400';
include_once('./_common.php');
include_once(G5_LIB_PATH.'/mailer.lib.php');
include_once(G5_LIB_PATH.'/icode.sms.lib.php');

auth_check($auth[$sub_menu], "w");

$sql = " select * from {$g5['g5_purchase_order_table']} where od_id = '$od_id' ";
$od  = sql_fetch($sql);
if(!$od['od_id'])
    alert('주문자료가 존재하지 않습니다.');

if ($od_receipt_time) {
    if (check_datetime($od_receipt_time) == false)
        alert('결제일시 오류입니다.');
}

// 결제정보 반영
$sql = " update {$g5['g5_purchase_order_table']}
            set od_deposit_name    = '{$_POST['od_deposit_name']}',
                od_bank_account    = '{$_POST['od_bank_account']}',
                od_receipt_time    = '{$_POST['od_receipt_time']}',
                od_receipt_price   = '{$_POST['od_receipt_price']}',
                od_receipt_point   = '{$_POST['od_receipt_point']}',
                od_refund_price    = '{$_POST['od_refund_price']}',
				od_cancel_price    = '{$_POST['od_cancel_price']}',
                od_delivery_company= '{$_POST['od_delivery_company']}',
                od_invoice         = '{$_POST['od_invoice']}',
                od_invoice_time    = '{$_POST['od_invoice_time']}',
				 od_cart_price    = '{$_POST['od_cart_price']}',
                od_send_cost       = '{$_POST['od_send_cost']}',
                od_send_cost2      = '{$_POST['od_send_cost2']}'
            where od_id = '$od_id' ";
sql_query($sql);

// 주문정보
$info = get_purchase_info($od_id);
if(!$info)
    alert('주문자료가 존재하지 않습니다.');

$od_status = $od['od_status'];
$cart_status = false;

// 미수가 0이고 상태가 주문이었다면 입금으로 변경

if($_POST['od_cart_price'] > 0 && $od['od_cart_price'] == 0 && $od['od_status'] == '준비')
{
    $od_status = '입금요청';
}

if($info['od_misu'] == 0 && $od['od_status'] == '입금요청')
{
    $od_status = '입금확인';
}

// 배송정보가 있으면 주문상태 배송으로 변경
$order_status = array('입금확인', '입금요청', '준비');
if($_POST['od_delivery_company'] && $_POST['od_invoice'] && in_array($od['od_status'], $order_status))
{
    $od_status = '배송';
}

// 미수금 정보 등 반영
$sql = " update {$g5['g5_purchase_order_table']}
            set od_misu         = '{$info['od_misu']}',
                od_tax_mny      = '{$info['od_tax_mny']}',
                od_vat_mny      = '{$info['od_vat_mny']}',
                od_free_mny     = '{$info['od_free_mny']}',
                od_send_cost    = '{$info['od_send_cost']}',
                od_status       = '$od_status'
            where od_id = '$od_id' ";
sql_query($sql);

$qstr = "sort1=$sort1&amp;sort2=$sort2&amp;sel_field=$sel_field&amp;search=$search&amp;page=$page";

goto_url("./purchase_form.php?od_id=$od_id&amp;$qstr");
?>
