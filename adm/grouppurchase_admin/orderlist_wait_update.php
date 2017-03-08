<?php
$sub_menu = '500300';
$sub_sub_menu = '3';

include_once('./_common.php');
include_once(G5_PATH.'/adm/shop_admin/admin.shop.lib.php');
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

    $current_status = $od['od_status'];
    $change_status  = $_POST['od_status'];

    switch ($current_status)
    {
        case '입금대기' :
            if ($change_status != '결제완료') continue;
            if ($od['od_settle_case'] != '무통장') continue;
            change_status($od_id, '입금대기', '결제완료');
            order_update_receipt($od_id);

            // SMS
            if($config['cf_sms_use'] == 'icode' && $_POST['send_sms'] && $default['de_sms_use4']) {
                $sms_contents = conv_sms_contents($od_id, $default['de_sms_cont4']);
                if($sms_contents) {
                    $receive_number = preg_replace("/[^0-9]/", "", $od['od_hp']);	// 수신자번호
                    $send_number = preg_replace("/[^0-9]/", "", $default['de_admin_company_tel']); // 발신자번호

                    if($receive_number && $send_number) {
                        $SMS->Add($receive_number, $send_number, $config['cf_icode_id'], $sms_contents, "");
                        $sms_count++;
                    }
                }
            }

            // 메일
            if($config['cf_email_use'] && $_POST['od_send_mail'])
                include './ordermail.inc.php';

            break;

        case '결제완료' :
            if ($change_status != '상품준비중') continue;
            change_status($od_id, '결제완료', '상품준비중');
            break;

        case '상품준비중' :
            if ($change_status != '배송중') continue;

            $delivery['invoice'] = $invoice;
            $delivery['invoice_time'] = $invoice_time;
            $delivery['delivery_company'] = $delivery_company;

            order_update_delivery($od_id, $od['mb_id'], $change_status, $delivery);
            change_status($od_id, '상품준비중', '배송중');

            // SMS
            if($config['cf_sms_use'] == 'icode' && $_POST['send_sms'] && $default['de_sms_use5']) {
                $sms_contents = conv_sms_contents($od_id, $default['de_sms_cont5']);
                if($sms_contents) {
                    $receive_number = preg_replace("/[^0-9]/", "", $od['od_hp']);	// 수신자번호
                    $send_number = preg_replace("/[^0-9]/", "", $default['de_admin_company_tel']); // 발신자번호

                    if($receive_number && $send_number) {
                        $SMS->Add($receive_number, $send_number, $config['cf_icode_id'], $sms_contents, "");
                        $sms_count++;
                    }
                }
            }

            // 메일
            if($config['cf_email_use'] && $_POST['od_send_mail'])
                include './ordermail.inc.php';

            // 에스크로 배송
            if($_POST['send_escrow'] && $od['od_tno'] && $od['od_escrow']) {
                $escrow_tno[$escrow_idx]  = $od['od_tno'];
                $escrow_numb[$escrow_idx] = $invoice;
                $escrow_corp[$escrow_idx] = $delivery_company;
                $escrow_idx++;
                $escrow_count++;
            }

            break;

    } // switch end


    // 주문정보
    $info = get_order_info($od_id);
    if(!$info) continue;

	$sql = " update {$g5['g5_shop_order_table']}
                set od_misu         = '0',
                    od_tax_mny      = '{$info['od_tax_mny']}',
                    od_vat_mny      = '{$info['od_vat_mny']}',
                    od_free_mny     = '{$info['od_free_mny']}'
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

goto_url($url."?$qstr");
?>