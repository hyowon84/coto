<?php
include_once('./_common.php');

if(get_magic_quotes_gpc())
{
    $_GET  = array_add_callback("stripslashes", $_GET);
    $_POST = array_add_callback("stripslashes", $_POST);
}

$_GET  = array_add_callback("mysql_real_escape_string", $_GET);
$_POST = array_add_callback("mysql_real_escape_string", $_POST);


if(get_session("mem_order_se")){
	$member[mb_id] = get_session("mem_order_se");
	$member = sql_fetch("select * from {$g5['member_table']} where mb_id='".$member[mb_id]."'");
}else{
	$member[mb_id] = $member[mb_id];
}

// 장바구니가 비어있는가?
if (get_session("ss_direct"))
    $tmp_cart_id = get_session('ss_cart_direct');
else
    $tmp_cart_id = get_session('ss_cart_id');

if (get_cart_count($tmp_cart_id) == 0)// 장바구니에 담기
    alert('장바구니가 비어 있습니다.\\n\\n이미 주문하셨거나 장바구니에 담긴 상품이 없는 경우입니다.', G5_SHOP_URL.'/cart.php');

$error = "";

$is_price_check = true;
$all_it_name = "";
// 장바구니 상품 재고 검사
$sql = " select it_id,
				ct_gubun,
                ct_qty,
                it_name,
                io_id,
                io_type,
                ct_option
           from {$g5['g5_shop_cart_table']}
          where od_id = '$tmp_cart_id'
            and ct_select = '1' and mb_id='".$member[mb_id]."' ";
$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++)
{
	if($row[ct_gubun]=="N"){
		// 상품에 대한 현재고수량
		if($row['io_id']) {
			$it_stock_qty = (int)get_option_stock_qty($row['it_id'], $row['io_id'], $row['io_type']);
		} else {
			$it_stock_qty = (int)get_it_stock_qty($row['it_id']);
		}
		// 장바구니 수량이 재고수량보다 많다면 오류
		if ($row['ct_qty'] > $it_stock_qty)
			$error .= "{$row['ct_option']} 의 재고수량이 부족합니다. 현재고수량 : $it_stock_qty 개\\n\\n";
	}elseif($row[ct_gubun]=="P")$is_price_check = false;

	if($i==0)$all_it_name.=$row['it_name'];
}
if($i>1)$all_it_name.=" 외 ".($i-1)."건";

if($i == 0)
    alert('장바구니가 비어 있습니다.\\n\\n이미 주문하셨거나 장바구니에 담긴 상품이 없는 경우입니다.', G5_SHOP_URL.'/cart.php');

if ($error != "")
{
    $error .= "다른 고객님께서 {$od_name}님 보다 먼저 주문하신 경우입니다. 불편을 끼쳐 죄송합니다.";
    alert($error);
}

$i_price     = (int)$_POST['od_price'];
$i_send_cost  = (int)$_POST['od_send_cost'];
$i_send_cost2  = (int)$_POST['od_send_cost2'];
$i_send_coupon  = (int)$_POST['od_send_coupon'];
$i_temp_point = (int)$_POST['od_temp_point'];


// 주문금액이 상이함

if($ct_mode == "gp"){
	$sql = " select SUM(IF(io_type = 1, (io_price * ct_qty), ((ct_price + io_price) * (ct_qty - ct_buy_qty)))) as od_price,
				  COUNT(distinct it_id) as cart_count, ct_op_option, it_id
				from {$g5['g5_shop_cart_table']} where od_id = '$tmp_cart_id' and ct_status='쇼핑' and mb_id='".$member[mb_id]."'
				";
}else{
	$sql = " select SUM(IF(io_type = 1, (io_price * ct_qty), ((ct_price + io_price) * (ct_qty - ct_buy_qty)))) as od_price,
				  COUNT(distinct it_id) as cart_count, ct_op_option, it_id
				from {$g5['g5_shop_cart_table']} where od_id = '$tmp_cart_id' and ct_select = '1' and mb_id='".$member[mb_id]."' ";
}

$row = sql_fetch($sql);

//옵션 상품
$op_arr = explode("|", $row[ct_op_option]);
$op_price = 0;
for($b = 0; $b < count($op_arr); $b++){
	if($op_arr[$b]){
		$op_row = sql_fetch("select * from {$g5['g5_shop_option2_table']} where it_id='".$row[it_id]."' and con='".$op_arr[$b]."' ");
		$op_price = $op_price + $op_row[price];
	}
}

$tot_ct_price = ceil($row['od_price'] / 100) * 100 + $op_price;
$cart_count = $row['cart_count'];

// 쿠폰금액계산
$tot_cp_price = 0;

if($is_member) {
    // 상품쿠폰
    $tot_it_cp_price = $tot_od_cp_price = 0;
    $it_cp_cnt = count($_POST['cp_id']);
    $arr_it_cp_prc = array();

    for($i=0; $i<$it_cp_cnt; $i++) {
        $cid = $_POST['cp_id'][$i];
        $it_id = $_POST['it_id'][$i];
        $sql = " select cp_id, cp_method, cp_target, cp_type, cp_price, cp_trunc, cp_minimum, cp_maximum
                    from {$g5['g5_shop_coupon_table']}
                    where cp_id = '$cid'
                      and mb_id IN ( '{$member['mb_id']}', '전체회원' )
                      and cp_start <= '".G5_TIME_YMD."'
                      and cp_end >= '".G5_TIME_YMD."'
                      and cp_method IN ( 0, 1 ) ";

        $cp = sql_fetch($sql);

        if(!$cp['cp_id'])
            continue;

        // 사용한 쿠폰인지
        if(is_used_coupon($member['mb_id'], $cp['cp_id']))
            continue;

        // 분류할인인지
        if($cp['cp_method']) {
            $sql2 = " select it_id, ca_id, ca_id2, ca_id3
                        from {$g5['g5_shop_item_table']}
                        where it_id = '$it_id' ";
            $row2 = sql_fetch($sql2);

            if(!$row2['it_id'])
                continue;

            if($row2['ca_id'] != $cp['cp_target'] && $row2['ca_id2'] != $cp['cp_target'] && $row2['ca_id3'] != $cp['cp_target'])
                continue;
        } else {
            if($cp['cp_target'] != $it_id)
                continue;
        }

        // 상품금액
        $sql = " select SUM( IF(io_type = '1', io_price * ct_qty, (ct_price + io_price) * (ct_qty - ct_buy_qty))) as sum_price
                    from {$g5['g5_shop_cart_table']}
                    where od_id = '$tmp_cart_id'
                      and it_id = '$it_id'
                      and ct_select = '1' and mb_id='".$member[mb_id]."'";
        $ct = sql_fetch($sql);
        $item_price = $ct['sum_price'];

        if($cp['cp_minimum'] > $item_price)
            continue;

        $dc = 0;
        if($cp['cp_type']) {
            $dc = floor(($item_price * ($cp['cp_price'] / 100)) / $cp['cp_trunc']) * $cp['cp_trunc'];
        } else {
            $dc = $cp['cp_price'];
        }

        if($cp['cp_maximum'] && $dc > $cp['cp_maximum'])
            $dc = $cp['cp_maximum'];

        if($item_price < $dc)
            continue;

        $tot_it_cp_price += $dc;
        $arr_it_cp_prc[$it_id] = $dc;
    }

    $tot_od_price = $tot_ct_price - $tot_it_cp_price;

    // 주문쿠폰
    if($_POST['od_cp_id']) {
        $sql = " select cp_id, cp_type, cp_price, cp_trunc, cp_minimum, cp_maximum
                    from {$g5['g5_shop_coupon_table']}
                    where cp_id = '{$_POST['od_cp_id']}'
                      and mb_id IN ( '{$member['mb_id']}', '전체회원' )
                      and cp_start <= '".G5_TIME_YMD."'
                      and cp_end >= '".G5_TIME_YMD."'
                      and cp_method = '2' ";
        $cp = sql_fetch($sql);

        // 사용한 쿠폰인지
        $cp_used = is_used_coupon($member['mb_id'], $cp['cp_id']);

        $dc = 0;
        if(!$cp_used && $cp['cp_id'] && ($cp['cp_minimum'] <= $tot_od_price)) {
            if($cp['cp_type']) {
                $dc = floor(($tot_od_price * ($cp['cp_price'] / 100)) / $cp['cp_trunc']) * $cp['cp_trunc'];
            } else {
                $dc = $cp['cp_price'];
            }

            if($cp['cp_maximum'] && $dc > $cp['cp_maximum'])
                $dc = $cp['cp_maximum'];

            if($tot_od_price < $dc)
                die('Order coupon error.');

            $tot_od_cp_price = $dc;
            $tot_od_price -= $tot_od_cp_price;
        }
    }

    $tot_cp_price = $tot_it_cp_price + $tot_od_cp_price;
}

//if ((int)(ceil(($tot_ct_price - $tot_cp_price) / 100) * 100) != ceil($i_price / 100) * 100) {
//	die("Error.");
//}

// 배송비가 상이함
if($ct_mode == "gp"){
	$send_cost = $od_send_cost;
}else{
	$send_cost = get_sendcost($tmp_cart_id);
}

$tot_sc_cp_price = 0;
if($is_member && $send_cost > 0) {
	// 배송쿠폰
	if($_POST['sc_cp_id']) {
		$sql = " select cp_id, cp_type, cp_price, cp_trunc, cp_minimum, cp_maximum
					from {$g5['g5_shop_coupon_table']}
					where cp_id = '{$_POST['sc_cp_id']}'
					  and mb_id IN ( '{$member['mb_id']}', '전체회원' )
					  and cp_start <= '".G5_TIME_YMD."'
					  and cp_end >= '".G5_TIME_YMD."'
					  and cp_method = '3' ";
		$cp = sql_fetch($sql);

		// 사용한 쿠폰인지
		$cp_used = is_used_coupon($member['mb_id'], $cp['cp_id']);

		$dc = 0;
		if(!$cp_used && $cp['cp_id'] && ($cp['cp_minimum'] <= $tot_od_price)) {
			if($cp['cp_type']) {
				$dc = floor(($send_cost * ($cp['cp_price'] / 100)) / $cp['cp_trunc']) * $cp['cp_trunc'];
			} else {
				$dc = $cp['cp_price'];
			}

			if($cp['cp_maximum'] && $dc > $cp['cp_maximum'])
				$dc = $cp['cp_maximum'];

			if($dc > $send_cost)
				$dc = $send_cost;

			$tot_sc_cp_price = $dc;
		}
	}
}

//if ((int)($send_cost - $tot_sc_cp_price) !== (int)($i_send_cost - $i_send_coupon)) {
//	die("Error..");
//}

// 추가배송비가 상이함
$zipcode = $od_b_zip1 . $od_b_zip2;
$sql = " select sc_id, sc_price from {$g5['g5_shop_sendcost_table']} where sc_zip1 <= '$zipcode' and sc_zip2 >= '$zipcode' ";
$tmp = sql_fetch($sql);
if(!$tmp['sc_id'])
	$send_cost2 = 0;
else
	$send_cost2 = (int)$tmp['sc_price'];
if($send_cost2 !== $i_send_cost2)
	die("Error...");

// 결제포인트가 상이함
// 회원이면서 포인트사용이면
$temp_point = 0;
if ($is_member && $config['cf_use_point'])
{
	if($member['mb_point'] >= $default['de_settle_min_point']) {
		$temp_point = (int)$default['de_settle_max_point'];

		if($temp_point > (int)$tot_od_price)
			$temp_point = (int)$tot_od_price;

		if($temp_point > (int)$member['mb_point'])
			$temp_point = (int)$member['mb_point'];

		$point_unit = (int)$default['de_settle_point_unit'];
		$temp_point = (int)((int)($temp_point / $point_unit) * $point_unit);
	}
}

if (($i_temp_point > (int)$temp_point || $i_temp_point < 0) && $config['cf_use_point'])
	die("Error....");


if ($od_temp_point)
{
    if ($member['mb_point'] < $od_temp_point)
        alert('회원님의 포인트가 부족하여 포인트로 결제 할 수 없습니다.');
}

$i_price = $i_price + $i_send_cost + $i_send_cost2 - $i_temp_point - $i_send_coupon;
$order_price = $tot_od_price + $send_cost + $send_cost2 - $tot_sc_cp_price - $od_temp_point;

$od_status = '입금대기';
if ($od_settle_case == "무통장")
{
    $od_receipt_point   = $i_temp_point;
    $od_receipt_price   = 0;
    $od_misu            = $i_price - $od_receipt_price;
    if($od_misu == 0) {
        $od_status      = '결제완료';
        $od_receipt_time = G5_TIME_YMDHIS;
    }
}
else if ($od_settle_case == "계좌이체")
{
    switch($default['de_pg_service']) {
        case 'lg':
            include G5_SHOP_PATH.'/lg/xpay_result.php';
            break;
        default:
            include G5_SHOP_PATH.'/kcp/pp_ax_hub.php';
            $bank_name  = iconv("cp949", "utf-8", $bank_name);
            break;
    }

    $od_tno             = $tno;
    $od_receipt_price   = $amount;
    $od_receipt_point   = $i_temp_point;
    $od_receipt_time    = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3 \\4:\\5:\\6", $app_time);
    $od_bank_account    = $od_settle_case;
    $od_deposit_name    = $od_name;
    $od_bank_account    = $bank_name;
    $pg_price           = $amount;
    $od_misu            = $i_price - $od_receipt_price;
    if($od_misu == 0)
        $od_status      = '결제완료';
}
else if ($od_settle_case == "가상계좌")
{
    switch($default['de_pg_service']) {
        case 'lg':
            include G5_SHOP_PATH.'/lg/xpay_result.php';
            break;
        default:
            include G5_SHOP_PATH.'/kcp/pp_ax_hub.php';
            $bankname   = iconv("cp949", "utf-8", $bankname);
            $depositor  = iconv("cp949", "utf-8", $depositor);
            break;
    }

    $od_receipt_point   = $i_temp_point;
    $od_tno             = $tno;
    $od_receipt_price   = 0;
    $od_bank_account    = $bankname.' '.$account;
    $od_deposit_name    = $depositor;
    $pg_price           = $amount;
    $od_misu            = $i_price - $od_receipt_price;
}
else if ($od_settle_case == "휴대폰")
{
    switch($default['de_pg_service']) {
        case 'lg':
            include G5_SHOP_PATH.'/lg/xpay_result.php';
            break;
        default:
            include G5_SHOP_PATH.'/kcp/pp_ax_hub.php';
            break;
    }

    $od_tno             = $tno;
    $od_receipt_price   = $amount;
    $od_receipt_point   = $i_temp_point;
    $od_receipt_time    = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3 \\4:\\5:\\6", $app_time);
    $od_bank_account    = $commid.' '.$mobile_no;
    $pg_price           = $amount;
    $od_misu            = $i_price - $od_receipt_price;
    if($od_misu == 0)
        $od_status      = '결제완료';
}
else if ($od_settle_case == "신용카드")
{
    switch($default['de_pg_service']) {
        case 'lg':
            include G5_SHOP_PATH.'/lg/xpay_result.php';
            break;
        default:
            include G5_SHOP_PATH.'/kcp/pp_ax_hub.php';
            $card_name  = iconv("cp949", "utf-8", $card_name);
            break;
    }

    $od_tno             = $tno;
    $od_app_no          = $app_no;
    $od_receipt_price   = $amount;
    $od_receipt_point   = $i_temp_point;
    $od_receipt_time    = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3 \\4:\\5:\\6", $app_time);
    $od_bank_account    = $card_name;
    $pg_price           = $amount;
    $od_misu            = $i_price - $od_receipt_price;
    if($od_misu == 0)
        $od_status      = '결제완료';
}
else
{
    die("od_settle_case Error!!!");
}

// 주문금액과 결제금액이 일치하는지 체크
if($tno) {
    if((int)$order_price !== (int)$pg_price) {
        $cancel_msg = '결제금액 불일치';
        switch($default['de_pg_service']) {
            case 'lg':
                include G5_SHOP_PATH.'/lg/xpay_cancel.php';
                break;
            default:
                include G5_SHOP_PATH.'/kcp/pp_ax_hub_cancel.php';
                break;
        }

        die("Receipt Amount Error");
    }
}


if ($is_member)
    $od_pwd = $member['mb_password'];
else
    $od_pwd = sql_password($_POST['od_pwd']);

// 주문번호를 얻는다.
$od_id = get_session('ss_order_id');

$od_escrow = 0;
if($escw_yn == 'Y')
    $od_escrow = 1;

// 복합과세 금액
$od_tax_mny = round($i_price / 1.1);
$od_vat_mny = $i_price - $od_tax_mny;
$od_free_mny = 0;
if($default['de_tax_flag_use']) {
    $od_tax_mny = (int)$_POST['comm_tax_mny'];
    $od_vat_mny = (int)$_POST['comm_vat_mny'];
    $od_free_mny = (int)$_POST['comm_free_mny'];
}





if($ct_mode != "gp"){

	$today_cart_res = sql_query("select * from {$g5['g5_shop_cart_table']} where od_id='$tmp_cart_id' and mb_id='".$member[mb_id]."'");
	for($i = 0; $today_cart_row = mysql_fetch_array($today_cart_res); $i++){

		$it = sql_fetch("select * from {$g5['g5_shop_item_table']} where it_name='".$today_cart_row[it_name]."'");
		$filename = explode("/", $it[it_img1]);
		$filename = explode(".", $filename[1]);
		$ext = $filename[count($filename)-1];
		$fileNm = "";

		for($j = 0; $j < count($filename) - 1; $j++){
			$fileNm .= $filename[$j].".";
		}
		$fileNm = substr($fileNm, 0, strlen($fileNm)-1);

		copy("../data/item/".$it[it_id]."/thumb-".$fileNm."_170x170.jpg", "../data/file/portfolio/".$fileNm."_".strtotime("now").".jpg");

		$wr_5 = $it[it_real_usd_price] * ($today_cart_row[ct_qty] - $today_cart_row[ct_buy_qty]);
		$wr_6 = get_price($it) * ($today_cart_row[ct_qty] - $today_cart_row[ct_buy_qty]);

		$sql = "
			insert into g5_write_portfolio set
			wr_subject='".$today_cart_row[it_name]."',
			wr_datetime='".date("Y-m-d H:i:s")."',
			mb_id='".$member[mb_id]."',
			wr_last='".date("Y-m-d H:i:s")."',
			wr_ip='".$REMOTE_ADDR."',
			wr_1='".$fileNm."_".strtotime("now").".jpg',
			wr_2='".$it[it_metal_type]."',
			wr_3='".$today_cart_row[ct_qty]."',
			wr_4='".$it[it_metal_don]."',
			wr_5='".$wr_5."',
			wr_6='".$wr_6."',
			wr_7='N',
			img_width='170',
			img_height='170'
		";
		
		sql_query($sql);

		$wr_id = mysql_insert_id();

		sql_query("
		update g5_write_portfolio set
		wr_num='-".$wr_id."',
		wr_parent='".$wr_id."'
		where wr_id=".$wr_id."
		");
	}

	// 주문서에 입력
	$sql = " insert {$g5['g5_shop_order_table']}
				set od_id             = '$od_id',
					mb_id             = '{$member['mb_id']}',
					od_pwd            = '$od_pwd',
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
					od_deposit_name   = '$od_deposit_name',
					od_memo           = '$od_memo',
					od_cart_count     = '$cart_count',
					od_cart_price     = '$tot_ct_price',
					od_cart_coupon    = '$tot_it_cp_price',
					od_cart_usd_price 	=	'$od_cart_usd_price',
					od_exchange_rate	=	'$od_exchange_rate',
					od_send_cost      = '$od_send_cost',
					od_send_coupon    = '$tot_sc_cp_price',
					od_send_cost2     = '$od_send_cost2',
					od_coupon         = '$tot_od_cp_price',
					od_receipt_price  = '$od_receipt_price',
					od_receipt_point  = '$od_receipt_point',
					od_bank_account   = '$od_bank_account',
					od_receipt_time   = '$od_receipt_time',
					od_misu           = '$od_misu',
					od_tno            = '$od_tno',
					od_app_no         = '$od_app_no',
					od_escrow         = '$od_escrow',
					od_tax_flag       = '{$default['de_tax_flag_use']}',
					od_tax_mny        = '$od_tax_mny',
					od_vat_mny        = '$od_vat_mny',
					od_free_mny       = '$od_free_mny',
					od_status         = '$od_status',
					od_shop_memo      = '',
					od_hope_date      = '$od_hope_date',
					od_time           = '".G5_TIME_YMDHIS."',
					od_ip             = '$REMOTE_ADDR',
					od_settle_case    = '$od_settle_case',
					buy_status		  = '$buy_kind',
					od_bank			  = '".$_POST['od_bank'][0]."|".$_POST['od_bank'][1]."',
					od_remit		  = '".$_POST['od_remit']."',
					od_tax			  = '".$_POST['od_tax']."',
					tax_status		  = '".$_POST['tax_status']."',
					od_tax_hp		  = '".$_POST['od_tax_hp']."',
					od_last_date	  = '".$_POST['od_last_date']."',
					combine_deli_code = 'T_".$od_id."',
					combine_deli_date = '".strtotime("now")."'
					";

	$result = sql_query($sql, false);

}





// 장바구니 상태변경
// 신용카드로 주문하면서 신용카드 포인트 사용하지 않는다면 포인트 부여하지 않음
$cart_status = $od_status;
$sql_card_point = "";
if ($od_receipt_price > 0 && !$default['de_card_point']) {
    $sql_card_point = " , ct_point = '0' ";
}

// 공동구매 상품 od_id 분류
if($ct_mode == "gp"){

	$gp_cart_res = sql_query("select * from {$g5['g5_shop_cart_table']} where od_id='$tmp_cart_id' and mb_id='".$member[mb_id]."' and ct_gubun = 'P'");

	for($i = 0; $gp_cart_row = sql_fetch_array($gp_cart_res); $i++){

		$gp = sql_fetch("select * from {$g5['g5_shop_group_purchase_table']} where gp_name='".str_replace("'","''",$gp_cart_row[it_name])."'");
		
		$wr_5 = $gp_cart_row[ct_usd_price] * $gp_cart_row[ct_qty];
		$wr_6 = $gp_cart_row[ct_price] * $gp_cart_row[ct_qty];

		if(stristr($gp[gp_name], "gold")){
			$gp_metal_type = "GL";
		}else if(stristr($gp[gp_name], "silver")){
			$gp_metal_type = "SL";
		}else if(stristr($gp[gp_name], "platinum")){
			$gp_metal_type = "PT";
		}else if(stristr($gp[gp_name], "palladium")){
			$gp_metal_type = "PD";
		}else{
			$gp_metal_type = "ETC";
		}

		$Response = curl("http://www.apmex.com/product/32457");
		$gp_metal_don = getExplodeValue($Response,"<table class=\"table table-product-specs\">","</div>");
		$gp_metal_don = explodeTag("tr", $gp_metal_don);
		$gp_metal_don = explodeTag("td", $gp_metal_don[6]);
		$gp_metal_don = str_replace(" oz", "", str_replace(" troy", "", $gp_metal_don[0]));

		$gp_cart_row[ct_qty] = $gp_cart_row[ct_qty] - $gp_cart_row[ct_buy_qty];

		$sql = "
			insert into g5_write_portfolio set
			wr_subject='".str_replace("'","''",$gp_cart_row[it_name])."',
			wr_datetime='".date("Y-m-d H:i:s")."',
			mb_id='".$member[mb_id]."',
			wr_last='".date("Y-m-d H:i:s")."',
			wr_ip='".$REMOTE_ADDR."',
			wr_1='".$gp[gp_img]."',
			wr_2='".$gp_metal_type."',
			wr_3='".$gp_cart_row[ct_qty]."',
			wr_4='".$gp_metal_don."',
			wr_5='".$wr_5."',
			wr_6='".$wr_6."',
			wr_7='P',
			img_width='170',
			img_height='170'
		";
		
		sql_query($sql);

		$wr_id = mysql_insert_id();

		sql_query("
		update g5_write_portfolio set
		wr_num='-".$wr_id."',
		wr_parent='".$wr_id."'
		where wr_id=".$wr_id."
		");
	}


	
	$sql1 = "update {$g5['g5_shop_cart_table']} set ct_status = '$cart_status' $sql_card_point where od_id='".$tmp_cart_id."' and mb_id='".$member[mb_id]."'";
	sql_query($sql1);

	$sql = " select SUM(IF(io_type = 1, (io_price * ct_qty), ((ct_price + io_price) * (ct_qty - ct_buy_qty)))) as od_price,
			  COUNT(distinct it_id) as cart_count
			from {$g5['g5_shop_cart_table']} where od_id = '".$od_id."' and ct_status='입금대기' and mb_id='".$member[mb_id]."'
			";
	$cart_count_row = sql_fetch($sql);
	$cart_count1 = $cart_count_row[cart_count];

	$tot_ct_price1 = $cart_count_row['od_price'] + $op_price;

	$od_send_cost1 = $send_cost; // 배송비

	$od_misu1 = $od_send_cost1 + $tot_ct_price1;

	$sql = " insert {$g5['g5_shop_order_table']}
			set od_id             = '".$od_id."',
				mb_id             = '{$member['mb_id']}',
				od_pwd            = '$od_pwd',
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
				od_deposit_name   = '$od_deposit_name',
				od_memo           = '$od_memo',
				od_cart_count     = '$cart_count1',
				od_cart_price     = '$tot_ct_price1',
				od_cart_coupon    = '$tot_it_cp_price',
				od_cart_usd_price = '$od_cart_usd_price',
				od_exchange_rate  = '$od_exchange_rate',
				od_send_cost      = '$od_send_cost1',
				od_send_coupon    = '$tot_sc_cp_price',
				od_send_cost2     = '$od_send_cost2',
				od_coupon         = '$tot_od_cp_price',
				od_receipt_price  = '$od_receipt_price',
				od_receipt_point  = '$od_receipt_point',
				od_bank_account   = '$od_bank_account',
				od_receipt_time   = '$od_receipt_time',
				od_misu           = '$od_misu1',
				od_tno            = '$od_tno',
				od_app_no         = '$od_app_no',
				od_escrow         = '$od_escrow',
				od_tax_flag       = '{$default['de_tax_flag_use']}',
				od_tax_mny        = '$od_tax_mny1',
				od_vat_mny        = '$od_vat_mny1',
				od_free_mny       = '$od_free_mny',
				od_status         = '$od_status',
				od_shop_memo      = '',
				od_hope_date      = '$od_hope_date',
				od_time           = '".G5_TIME_YMDHIS."',
				od_ip             = '$REMOTE_ADDR',
				od_settle_case    = '$od_settle_case',
				buy_status		  = '$buy_kind',
				od_bank			  = '".$_POST['od_bank'][0]."|".$_POST['od_bank'][1]."',
				od_remit		  = '".$_POST['od_remit']."',
				od_tax			  = '".$_POST['od_tax']."',
				tax_status		  = '".$_POST['tax_status']."',
				od_tax_hp		  = '".$_POST['od_tax_hp']."',
				od_last_date	  = '".$_POST['od_last_date']."',
				combine_deli_code = 'T_".$od_id."',
				combine_deli_date = '".strtotime("now")."'
				";

		$result = sql_query($sql);

}else{
	$sql = "update {$g5['g5_shop_cart_table']}
			   set od_id = '$od_id',
				   ct_status = '$cart_status'
				   $sql_card_point
			 where od_id = '$tmp_cart_id'
			   and ct_select = '1' and mb_id='".$member[mb_id]."'";
	$result = sql_query($sql, false);

	$od_id1 = $od_id;
}



if($result && $od_settle_case == "무통장" && $_POST['RTR_InBank']){


	$od = sql_fetch("select sum(od_misu) as total_misu from {$g5['g5_shop_order_table']} where od_id like '".$od_id."%' and mb_id = '".$member['mb_id']."'");
	$sql = "INSERT INTO RTAM_REQUEST VALUES ('' ,'".$od_id."', '".$member['mb_id']."', '".G5_TIME_YMDHIS."',  '".$all_it_name."', '".$od['total_misu']."', '".$_POST['RTR_InBank']."', '".$od_name."', '".$od['total_misu']."', '0')";
	sql_query($sql);
}

// 주문정보 입력 오류시 결제 취소
if(!$result) {
    if($tno) {
        $cancel_msg = '주문정보 입력 오류';
        switch($default['de_pg_service']) {
            case 'lg':
                include G5_SHOP_PATH.'/lg/xpay_cancel.php';
                break;
            default:
                include G5_SHOP_PATH.'/kcp/pp_ax_hub_cancel.php';
                break;
        }
    }

    // 관리자에게 오류 알림 메일발송
    $error = 'order';
    include G5_SHOP_PATH.'/ordererrormail.php';

	 // 주문삭제
    sql_query(" delete from {$g5['g5_shop_order_table']} where od_id = '$od_id' ");

    die('<p>고객님의 주문 정보를 처리하는 중 오류가 발생해서 주문이 완료되지 않았습니다.</p><p>'.strtoupper($default['de_pg_service']).'를 이용한 전자결제(신용카드, 계좌이체, 가상계좌 등)은 자동 취소되었습니다.');
}

// 회원이면서 포인트를 사용했다면 테이블에 사용을 추가
if ($is_member && $od_receipt_point)
    insert_point($member['mb_id'], (-1) * $od_receipt_point, "주문번호 $od_id 결제");

$od_memo = nl2br(htmlspecialchars2(stripslashes($od_memo))) . "&nbsp;";


// 쿠폰사용내역기록
if($is_member) {
    $it_cp_cnt = count($_POST['cp_id']);
    for($i=0; $i<$it_cp_cnt; $i++) {
        $cid = $_POST['cp_id'][$i];
        $cp_it_id = $_POST['it_id'][$i];
        $cp_prc = (int)$arr_it_cp_prc[$cp_it_id];

        if(trim($cid)) {
            $sql = " insert into {$g5['g5_shop_coupon_log_table']}
                        set cp_id       = '$cid',
                            mb_id       = '{$member['mb_id']}',
                            od_id       = '$od_id',
                            cp_price    = '$cp_prc',
                            cl_datetime = '".G5_TIME_YMDHIS."' ";
            sql_query($sql);
        }

        // 쿠폰사용금액 cart에 기록
        $cp_prc = (int)$arr_it_cp_prc[$cp_it_id];
        $sql = " update {$g5['g5_shop_cart_table']}
                    set cp_price = '$cp_prc'
                    where od_id = '$od_id'
                      and it_id = '$cp_it_id'
                      and ct_select = '1' and mb_id='".$member[mb_id]."' 
                    order by ct_id asc
                    limit 1 ";
        sql_query($sql);
    }

    if($_POST['od_cp_id']) {
        $sql = " insert into {$g5['g5_shop_coupon_log_table']}
                    set cp_id       = '{$_POST['od_cp_id']}',
                        mb_id       = '{$member['mb_id']}',
                        od_id       = '$od_id',
                        cp_price    = '$tot_od_cp_price',
                        cl_datetime = '".G5_TIME_YMDHIS."' ";
        sql_query($sql);
    }

    if($_POST['sc_cp_id']) {
        $sql = " insert into {$g5['g5_shop_coupon_log_table']}
                    set cp_id       = '{$_POST['sc_cp_id']}',
                        mb_id       = '{$member['mb_id']}',
                        od_id       = '$od_id',
                        cp_price    = '$tot_sc_cp_price',
                        cl_datetime = '".G5_TIME_YMDHIS."' ";
        sql_query($sql);
    }
}


include_once(G5_SHOP_PATH.'/ordermail1.inc.php');
include_once(G5_SHOP_PATH.'/ordermail2.inc.php');

// SMS BEGIN --------------------------------------------------------
// 주문고객과 쇼핑몰관리자에게 SMS 전송
if($config['cf_sms_use'] && ($default['de_sms_use2'] || $default['de_sms_use3'])) {
    $sms_contents = array($default['de_sms_cont2'], $default['de_sms_cont3']);
    $recv_numbers = array($od_hp, $default['de_sms_hp']);
    $send_numbers = array($default['de_admin_company_tel'], $od_hp);

    include_once(G5_LIB_PATH.'/icode.sms.lib.php');

    $SMS = new SMS; // SMS 연결
    $SMS->SMS_con($config['cf_icode_server_ip'], $config['cf_icode_id'], $config['cf_icode_pw'], $config['cf_icode_server_port']);
    $sms_count = 0;

    for($s=0; $s<count($sms_contents); $s++) {
        $sms_content = $sms_contents[$s];
        $recv_number = preg_replace("/[^0-9]/", "", $recv_numbers[$s]);
        $send_number = preg_replace("/[^0-9]/", "", $send_numbers[$s]);

        $sms_content = preg_replace("/{이름}/", $od_name, $sms_content);
        $sms_content = preg_replace("/{보낸분}/", $od_name, $sms_content);
        $sms_content = preg_replace("/{받는분}/", $od_b_name, $sms_content);
        $sms_content = preg_replace("/{주문번호}/", $od_id, $sms_content);
        $sms_content = preg_replace("/{주문금액}/", number_format($tot_ct_price), $sms_content);
        $sms_content = preg_replace("/{회원아이디}/", $member['mb_id'], $sms_content);
        $sms_content = preg_replace("/{회사명}/", $default['de_admin_company_name'], $sms_content);

        $idx = 'de_sms_use'.($s + 2);

        if($default[$idx] && $recv_number) {
            $SMS->Add($recv_number, $send_number, $config['cf_icode_id'], iconv("utf-8", "euc-kr", stripslashes($sms_content)), "");
            $sms_count++;
        }
    }

    if($sms_count > 0)
        $SMS->Send();
}
// SMS END   --------------------------------------------------------


// orderview 에서 사용하기 위해 session에 넣고
$uid = md5($od_id.G5_TIME_YMDHIS.$REMOTE_ADDR);
set_session('ss_orderview_uid', $uid);

// 주문번호제거
set_session('ss_order_id', '');

// 기존자료 세션에서 제거
if (get_session('ss_direct'))
    set_session('ss_cart_direct', '');

// 배송지처리
if($is_member) {
    $sql = " select * from {$g5['g5_shop_order_address_table']}
                where mb_id = '{$member['mb_id']}'
                  and ad_name = '$od_b_name'
                  and ad_tel = '$od_b_tel'
                  and ad_hp = '$od_b_hp'
                  and ad_zip1 = '$od_b_zip1'
                  and ad_zip2 = '$od_b_zip2'
                  and ad_addr1 = '$od_b_addr1'
                  and ad_addr2 = '$od_b_addr2'
                  and ad_addr3 = '$od_b_addr3' ";
    $row = sql_fetch($sql);

    // 기본배송지 체크
    if($ad_default) {
        $sql = " update {$g5['g5_shop_order_address_table']}
                    set ad_default = '0'
                    where mb_id = '{$member['mb_id']}' ";
        sql_query($sql);
    }

    if($row['ad_id']){
        $sql = " update {$g5['g5_shop_order_address_table']}
                      set ad_default = '$ad_default',
                          ad_subject = '$ad_subject'
                    where mb_id = '{$member['mb_id']}'
                      and ad_id = '{$row['ad_id']}' ";
    } else {
        $sql = " insert into {$g5['g5_shop_order_address_table']}
                    set mb_id       = '{$member['mb_id']}',
                        ad_subject  = '$ad_subject',
                        ad_default  = '$ad_default',
                        ad_name     = '$od_b_name',
                        ad_tel      = '$od_b_tel',
                        ad_hp       = '$od_b_hp',
                        ad_zip1     = '$od_b_zip1',
                        ad_zip2     = '$od_b_zip2',
                        ad_addr1    = '$od_b_addr1',
                        ad_addr2    = '$od_b_addr2',
                        ad_addr3    = '$od_b_addr3',
                        ad_jibeon   = '$od_b_addr_jibeon' ";
    }

    sql_query($sql);
}

if($ct_mode == "gp"){
	goto_url(G5_SHOP_URL.'/orderinquiry_gp.php?cate1=&ct_type_status=1');
}else{
	goto_url(G5_SHOP_URL.'/orderinquiryview.php?od_id='.$od_id.'&amp;uid='.$uid);
}
?>

<html>
    <head>
        <title>주문정보 기록</title>
        <script>
            // 결제 중 새로고침 방지 샘플 스크립트 (중복결제 방지)
            function noRefresh()
            {
                /* CTRL + N키 막음. */
                if ((event.keyCode == 78) && (event.ctrlKey == true))
                {
                    event.keyCode = 0;
                    return false;
                }
                /* F5 번키 막음. */
                if(event.keyCode == 116)
                {
                    event.keyCode = 0;
                    return false;
                }
            }

            document.onkeydown = noRefresh ;
        </script>
    </head>
</html>