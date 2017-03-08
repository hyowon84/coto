<?php
$sub_menu = '500300';
$sub_sub_menu = '3';

include_once('./_common.php');

auth_check($auth[$sub_menu], "w");

$ct_chk_count = count($_POST['ct_chk']);
if(!$ct_chk_count)
    alert('처리할 자료를 하나 이상 선택해 주십시오.');

$status_normal = array('입금대기','해외배송대기','해외배송중','결제완료','상품준비중','배송대기','배송중','배송완료','미입고');
$status_cancel = array('취소','교환','반품','품절');

if (in_array($_POST['ct_status'], $status_normal) || in_array($_POST['ct_status'], $status_cancel)) {
    ; // 통과
} else {
    alert('변경할 상태가 올바르지 않습니다.');
}

$od_id1 = get_uniqid();
$cart_count = 0;
$mod_history = '';
$cnt = count($_POST['ct_id']);

$chk_res = sql_query("select * from {$g5['g5_shop_cart_table']} where ct_source_id='$od_id' limit 0, 1 ");
$chk = mysql_num_rows($chk_res);

for ($i=0; $i<$cnt; $i++)
{
    $k = $_POST['ct_chk'][$i];
    $ct_id = $_POST['ct_id'][$k];

    if(!$ct_id)
        continue;

    $sql = " select * from {$g5['g5_shop_cart_table']} where od_id = '$od_id' and ct_id  = '$ct_id' ";
    $ct = sql_fetch($sql);

	$del_od_id_row = sql_fetch("select * from {$g5['g5_shop_cart_table']} where ct_source_id='".$ct[od_id]."' limit 0, 1 ");
	$del_od_id = $del_od_id_row[od_id];

    if(!$ct['ct_id'])
        continue;
	
	/*
    // 수량이 변경됐다면
    $ct_qty = $_POST['ct_qty'][$k];

	$ct_notstocked_cnt = ceil(($ct[ct_qty] + $ct[ct_notstocked_cnt])-$ct_qty);
	$ct_notstocked_cnt_que = ",ct_notstocked_cnt='$ct_notstocked_cnt'";

    if($ct['ct_qty'] != $ct_qty) {
        $diff_qty = $ct['ct_qty'] - $ct_qty;

        // 재고에 차이 반영.
        if($ct['ct_stock_use']) {
            if($ct['io_id']) {
                $sql = " update {$g5['g5_shop_item_option_table']}
                            set io_stock_qty = io_stock_qty + '$diff_qty'
                            where it_id = '{$ct['it_id']}'
                              and io_id = '{$ct['io_id']}'
                              and io_type = '{$ct['io_type']}' ";
            } else {
                $sql = " update {$g5['g5_shop_item_table']}
                            set it_stock_qty = it_stock_qty + '$diff_qty'
                            where it_id = '{$ct['it_id']}' ";
            }

            sql_query($sql);
        }

        // 수량변경
        $sql = " update {$g5['g5_shop_cart_table']}
                    set ct_qty = '$ct_qty'
					$ct_notstocked_cnt_que
                    where ct_id = '$ct_id'
                      and od_id = '$od_id' ";

        sql_query($sql);
        $mod_history .= G5_TIME_YMDHIS.' '.$ct['ct_option'].' 수량변경 '.$ct['ct_qty'].' -> '.$ct_qty."\n";
    }
	

    // 재고를 이미 사용했다면 (재고에서 이미 뺐다면)
    $stock_use = $ct['ct_stock_use'];
    if ($ct['ct_stock_use'])
    {
        if ($ct_status == '입금대기' || $ct_status == '취소' || $ct_status == '교환' || $ct_status == '반품' || $ct_status == '품절')
        {
            $stock_use = 0;
            // 재고에 다시 더한다.
            if($ct['io_id']) {
                $sql = " update {$g5['g5_shop_item_option_table']}
                            set io_stock_qty = io_stock_qty + '{$ct['ct_qty']}'
                            where it_id = '{$ct['it_id']}'
                              and io_id = '{$ct['io_id']}'
                              and io_type = '{$ct['io_type']}' ";
            } else {
                $sql = " update {$g5['g5_shop_item_table']}
                            set it_stock_qty = it_stock_qty + '{$ct['ct_qty']}'
                            where it_id = '{$ct['it_id']}' ";
            }

            sql_query($sql);
        }
    }
    else
    {
        // 재고 오류로 인한 수정
        if ($ct_status == '배송중' || $ct_status == '배송완료')
        {
            $stock_use = 1;
            // 재고에서 뺀다.
            if($ct['io_id']) {
                $sql = " update {$g5['g5_shop_item_option_table']}
                            set io_stock_qty = io_stock_qty - '{$ct['ct_qty']}'
                            where it_id = '{$ct['it_id']}'
                              and io_id = '{$ct['io_id']}'
                              and io_type = '{$ct['io_type']}' ";
            } else {
                $sql = " update {$g5['g5_shop_item_table']}
                            set it_stock_qty = it_stock_qty - '{$ct['ct_qty']}'
                            where it_id = '{$ct['it_id']}' ";
            }

            sql_query($sql);
        }
		*/
        /* 주문 수정에서 "품절" 선택시 해당 상품 자동 품절 처리하기
        else if ($ct_status == '품절') {
            $stock_use = 1;
            // 재고에서 뺀다.
            $sql =" update {$g5['g5_shop_item_table']} set it_stock_qty = 0 where it_id = '{$ct['it_id']}' ";
            sql_query($sql);
        } */
  //  }

    $point_use = $ct['ct_point_use'];
    // 회원이면서 포인트가 0보다 크면
    // 이미 포인트를 부여했다면 뺀다.
    if ($mb_id && $ct['ct_point'] && $ct['ct_point_use'])
    {
        $point_use = 0;
        //insert_point($mb_id, (-1) * ($ct[ct_point] * $ct[ct_qty]), "주문번호 $od_id ($ct_id) 취소");
        delete_point($mb_id, "@delivery", $mb_id, "$od_id,$ct_id");
    }

	if($ct_status != "미입고"){
		$ct_status_que = " ct_status     = '$ct_status', ";
	}

	// 히스토리에 남김
	// 히스토리에 남길때는 작업|시간|IP|그리고 나머지 자료
	$ct_history="\n$ct_status|$now|$REMOTE_ADDR";

	$sql = " update {$g5['g5_shop_cart_table']}
				set ct_point_use  = '$point_use',
					ct_stock_use  = '$stock_use',
					$ct_status_que
					ct_history    = CONCAT(ct_history,'$ct_history')
				where od_id = '$od_id'
				and ct_id  = '$ct_id' ";
	sql_query($sql);
/*
	//미입고 카트 복사
	$sql_common = "
		,it_id='".$ct[it_id]."'
		,it_name='".$ct[it_name]."'
		,ct_status='".$ct_status."'
		,ct_history='".$ct[ct_history]."'
		,ct_payment='".$ct[ct_payment]."'
		,ct_price='".$ct[ct_price]."'
		,ct_usd_price='".$ct[ct_usd_price]."'
		,ct_point='".$ct[ct_point]."'
		,cp_price='".$ct[cp_price]."'
		,ct_point_use='".$ct[ct_point_use]."'
		,ct_stock_use='".$ct[ct_point_use]."'
		,ct_option='".$ct[ct_option]."'
		,ct_qty='".$ct_notstocked_cnt."'
		,ct_notax='".$ct[ct_notax]."'
		,io_id='".$ct[io_id]."'
		,io_type='".$ct[io_type]."'
		,io_price='".$ct[io_price]."'
		,ct_time='".$ct[ct_time]."'
		,ct_ip='".$ct[ct_ip]."'
		,ct_send_cost='0'
		,ct_direct='".$ct[ct_direct]."'
		,ct_select='".$ct[ct_select]."'
		,ct_type='".$ct[ct_type]."'
		,ct_time_code='".$ct[ct_time_code]."'
		,auc_status='".$ct[auc_status]."'
		,pur_status='".$ct[pur_status]."'
		,ct_card_status='".$ct[ct_card_status]."'
		,ct_card_price='".$ct[ct_card_price]."'
		,ct_gp_status='".$ct[ct_gp_status]."'
		,buy_status='".$ct[buy_status]."'
		,ct_buy_qty='".$ct[ct_buy_qty]."'
		,total_amount_code='".$ct[total_amount_code]."'
		,ex_status='".$ct[ex_status]."'
		,ex_reason='".$ct[ex_reason]."'
		,ex_content='".$ct[ex_content]."'
		,ex_addr='".$ct[ex_addr]."'
		,ex_name='".$ct[ex_name]."'
		,ex_tel='".$ct[ex_name]."'
		,ex_hp='".$ct[ex_name]."'
		,re_status='".$ct[re_status]."'
		,ex_content1='".$ct[ex_content1]."'
	";

	if($ct_status == "미입고"){

		if(!$chk){
			$sql = "
				insert into {$g5['g5_shop_cart_table']} set
				od_id='".$od_id1."'
				,mb_id='".$ct[mb_id]."'
				,ct_gubun='".$ct[ct_gubun]."'
				$sql_common
				,ct_source_id='$od_id'
				,ct_ct_id='".$ct[ct_id]."'
			";
			//echo $sql."</br>";
			//exit;
			sql_query($sql);
		}else{
			$sql = "
				update {$g5['g5_shop_cart_table']} set
				ct_gubun='".$ct[ct_gubun]."'
				$sql_common
				,ct_ct_id='".$ct[ct_id]."'
				where ct_source_id='$od_id'
				and it_name='".$ct[it_name]."'
			";
			//echo $sql."</br>";
			//exit;
			sql_query($sql);
		}

		$od_cart_price += $ct[ct_price] * $ct_notstocked_cnt;
	}
*/
	//if($ct_notstocked_cnt == 0){
	//	sql_query("delete from {$g5['g5_shop_cart_table']} where ct_source_id='$od_id' and it_name='".$ct[it_name]."' ");
	//}


    $cart_count++;
}

// 장바구니 상품 모두 취소일 경우 주문상태 변경
$cancel_change = false;
if (in_array($_POST['ct_status'], $status_cancel)) {
    $sql = " select count(*) as cnt from {$g5['g5_shop_cart_table']} where od_id = '$od_id' ";
    $row = sql_fetch($sql);

    if($cart_count == $row['cnt'])
        $cancel_change = true;
}


// 미수금 등의 정보
$info = get_grouppurchase_info($od_id);


if(!$info)
    alert('주문자료가 존재하지 않습니다.');

$sql = " update {$g5['g5_shop_order_table']}
            set od_cart_price   = '{$info['od_cart_price']}',
                od_cart_coupon  = '{$info['od_cart_coupon']}',
                od_coupon       = '{$info['od_coupon']}',
                od_send_coupon  = '{$info['od_send_coupon']}',
                od_cancel_price = '{$info['od_cancel_price']}',
                od_send_cost    = '{$info['od_send_cost']}',
                od_misu         = '{$info['od_misu']}',
                od_tax_mny      = '{$info['od_tax_mny']}',
                od_vat_mny      = '{$info['od_vat_mny']}',
                od_free_mny     = '{$info['od_free_mny']}' ";

				
if ($mod_history) { // 수량변경 히스토리 기록
    $sql .= " , od_mod_history = CONCAT(od_mod_history,'$mod_history') ";
}

if($cancel_change) {
    $sql .= " , od_status = '$ct_status' "; // 주문상품 모두 취소, 반품, 품절이면 주문 취소
} else {
    if (in_array($_POST['ct_status'], $status_normal)) { // 정상인 주문상태만 기록
        $sql .= " , od_status = '{$_POST['ct_status']}' ";
    }
}
if (in_array($_POST['ct_status'], $status_normal) && !$cancel_change) { // 정상인 주문상태만 기록
    $sql .= " , od_status = '{$_POST['ct_status']}' ";
}

$sql .= " where od_id = '$od_id' ";

sql_query($sql);



$qstr = "sort1=$sort1&amp;sort2=$sort2&amp;sel_field=$sel_field&amp;search=$search&amp;page=$page";

$url = "./orderform.php?od_id=$od_id&amp;$qstr";

// 1.06.06
$od = sql_fetch(" select od_receipt_point from {$g5['g5_shop_order_table']} where od_id = '$od_id' ");
if ($od['od_receipt_point'])
    alert("포인트로 결제한 주문은,\\n\\n주문상태 변경으로 인해 포인트의 가감이 발생하는 경우\\n\\n회원관리 > 포인트관리에서 수작업으로 포인트를 맞추어 주셔야 합니다.", $url);
else
    goto_url($url);
?>
