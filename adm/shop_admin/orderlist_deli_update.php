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

    $od_res = sql_query(" select * from {$g5['g5_shop_order_table']} where combine_deli_code = '$od_id' ");
	
	for($j = 0; $od = mysql_fetch_array($od_res); $j++){

		$invoice      = $_POST['od_invoice'][$od_id][$od[od_id]];
		$invoice_time = $_POST['od_invoice_time'][$od_id][$od[od_id]];
		$delivery_company = $_POST['od_delivery_company'][$od_id][$od[od_id]];

		$invoice_com      = $_POST['od_invoice_com'][$k];
		$invoice_time_com = $_POST['od_invoice_time_com'][$k];
		$delivery_company_com = $_POST['od_delivery_company_com'][$k];

		// 주문정보
		$info = get_order_info($od[od_id]);
		if(!$info) continue;

		
		
		// 카트정보
		if($_POST[modify_status] == "1"){
			$current_status = $od['od_status'];
			$change_status  = $_POST['od_status'];
			$od_status_que  = ",od_status = '".$change_status."'";

			$sql = " update {$g5['g5_shop_cart_table']}
					set ct_status = '".$change_status."'
					where od_id = '".$od[od_id]."' ";

			$od_id1 = $od[od_id];

			sql_query($sql, true);
		}else if($_POST[modify_status] == "2"){

			if($invoice_com){
				$od_invoice_que = "
					,od_invoice = '".$invoice_com."'
					,od_delivery_company = '".$delivery_company_com."'
					,od_invoice_time = '".$invoice_time_com."'
				";
				$od_status_que  = ",od_status = '배송중'";

				$sql = " update {$g5['g5_shop_cart_table']}
						set ct_status = '배송중'
						where od_id = '".$od[od_id]."' ";

				$od_id1 = $od[od_id];
				
			}
			sql_query($sql, true);
		}

		$sql = " update {$g5['g5_shop_order_table']}
					set od_misu         = '0'
						,od_tax_mny      = '{$info['od_tax_mny']}'
						,od_vat_mny      = '{$info['od_vat_mny']}'
						,od_free_mny     = '{$info['od_free_mny']}'
						$od_invoice_que
						$od_status_que
					where od_id = '".$od_id1."' ";

					//echo $sql."</br></br>";

		sql_query($sql, true);
	}
	//exit;
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