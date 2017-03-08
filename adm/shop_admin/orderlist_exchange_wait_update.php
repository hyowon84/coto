<?php
$sub_menu = '400400';
include_once('./_common.php');
include_once('./admin.shop.lib.php');
include_once(G5_LIB_PATH.'/mailer.lib.php');
include_once(G5_LIB_PATH.'/icode.sms.lib.php');

define("_ORDERMAIL_", true);

//print_r2($_POST); exit;

$sms_count = 0;
if($config['cf_sms_use'] == 'icode' && $_POST['send_sms'])
{
    $SMS = new SMS;
	$SMS->SMS_con($config['cf_icode_server_ip'], $config['cf_icode_id'], $config['cf_icode_pw'], $config['cf_icode_server_port']);
}

$escrow_count = 0;
if($_POST['send_escrow']) {
    $escrow_tno  = array();
    $escrow_corp = array();
    $escrow_numb = array();
    $escrow_idx  = 0;
}

for ($i=0; $i<count($_POST['chk']); $i++)
{
    // 실제 번호를 넘김
    $k     = $_POST['chk'][$i];
    $od_id = $_POST['od_id'][$k];

    $invoice      = $_POST['od_invoice'][$k];
    $invoice_time = $_POST['od_invoice_time'][$k];
    $delivery_company = $_POST['od_delivery_company'][$k];

    $od = sql_fetch(" select * from {$g5['g5_shop_order_table']} where od_id = '$od_id' ");
    if (!$od) continue;

    //change_order_status($od['od_status'], $_POST['od_status'], $od);
    //echo $od_id . "<br>";

    // 주문정보
    $info = get_order_info($od_id);
    if(!$info) continue;

	// 카트정보
	if($_POST[modify_status] == "1"){
		$current_status = $od['od_status'];
		$change_status  = $_POST['od_status'];
		$od_status_que  = ",od_status = '".$change_status."'";

		$sql = " update {$g5['g5_shop_cart_table']}
                set ct_status = '".$change_status."'
                where od_id = '$od_id' ";

		sql_query($sql, true);
	}else if($_POST[modify_status] == "2"){

		if($invoice){
			$od_invoice_que = "
				,od_invoice = '".$invoice."'
				,od_delivery_company = '".$delivery_company."'
				,od_invoice_time = '".$invoice_time."'
			";
			$od_status_que  = ",od_status = '교환'";

			$sql = " update {$g5['g5_shop_cart_table']}
					set ct_status = '교환'
					where od_id = '$od_id' ";

			sql_query($sql, true);
		}
	}

    $sql = " update {$g5['g5_shop_order_table']}
                set od_misu         = '{$info['od_misu']}'
                    ,od_tax_mny      = '{$info['od_tax_mny']}'
                    ,od_vat_mny      = '{$info['od_vat_mny']}'
                    ,od_free_mny     = '{$info['od_free_mny']}'
					$od_invoice_que
					$od_status_que
                where od_id = '$od_id' ";

    sql_query($sql, true);

}

// SMS
if($config['cf_sms_use'] == 'icode' && $_POST['send_sms'] && $sms_count)
{
    $SMS->Send();
}

// 에스크로 배송
if($_POST['send_escrow'] && $escrow_count)
{
    include_once('./orderescrow.inc.php');
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

//goto_url("./orderlist.php?$qstr");
goto_url($_POST[url]);
?>