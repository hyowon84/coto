<?php
include_once('./_common.php');

// print_r2($_POST); exit;

if($default[de_guest_cart_use] == 0){
	if($member[mb_id] == ""){
		alert("로그인 후 이용 가능합니다.");
	}
}

// cart id 설정
set_cart_id($sw_direct);

if($buy_kind == "공동구매"){
	$orderform_url = "orderform_gp.php";
}else if($buy_kind == "구매대행"){
	$orderform_url = "orderform_pur.php";
}else{
	$orderform_url = "orderform.php";
}

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

	if($buy_kind == "공동구매"){

		for($i=0; $i<$fldcnt; $i++) {
			$ct_chk = $_POST['ct_chk'][$i];
			if($ct_chk) {
				$it_id = $_POST['it_id'][$i];
				$sql = " update {$g5['g5_shop_cart_table']} set
							ct_select = '1',
							ct_qty = '".$_POST[ct_qty][$it_id]."'
							where it_id = '$it_id' and od_id = '$tmp_cart_id' and ct_gp_status='y' ";
				sql_query($sql);
			}
		}

	}else{
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
else if ($act == "seldelete1") // 선택삭제1
{
    if(!$_POST['no'])
        alert("삭제하실 상품을 하나이상 선택해 주십시오.");

	$no_arr = explode("|", $_POST['no']);
	//echo count($no_arr);
    //$fldcnt = count($_POST['it_id']);
    for($i=0; $i<count($no_arr); $i++) {
        //$ct_chk = $_POST['ct_chk'][$i];
		$ct_chk = $no_arr[$i];
        if($ct_chk) {
            //$it_id = $_POST['it_id'][$i];
            $sql = " delete from {$g5['g5_shop_cart_table']} where it_id = '$ct_chk' and od_id = '$tmp_cart_id' ";
			//echo $sql."</br>";
            sql_query($sql);
        }
    }
}
else if ($act == "seldelete2") // 선택삭제2
{
    if(!$_POST['no'])
        alert("삭제하실 상품을 하나이상 선택해 주십시오.");

    $no_arr = explode("|", $_POST['no']);
	//echo count($no_arr);
    //$fldcnt = count($_POST['it_id']);
    for($i=0; $i<count($no_arr); $i++) {
        //$ct_chk = $_POST['ct_chk'][$i];
		$ct_chk = $no_arr[$i];
        if($ct_chk) {
            //$it_id = $_POST['it_id'][$i];
            $sql = " delete from {$g5['g5_shop_cart_table']} where it_id = '$ct_chk' and od_id = '$tmp_cart_id' ";
			//echo $sql."</br>";
            sql_query($sql);
        }
    }
}
else if ($act == "delete") // 삭제
{
	$sql = " delete from {$g5['g5_shop_cart_table']} where it_id = '".$_POST[it_id]."' and od_id = '$tmp_cart_id' ";
	sql_query($sql);
}
else // 장바구니에 담기
{
    $count = count($_POST['it_id']);
    if ($count < 1)
        alert('장바구니에 담을 상품을 선택하여 주십시오.');

    $ct_count = 0;
    for($i=0; $i<$count; $i++) {
        // 보관함의 상품을 담을 때 체크되지 않은 상품 건너뜀
        if($act == 'multi' && !$_POST['chk_it_id'][$i])
            continue;

        $it_id = $_POST['it_id'][$i];
        $opt_count = count($_POST['io_id'][$it_id]);

        if($opt_count && $_POST['io_type'][$it_id][0] != 0)
            alert('상품의 선택옵션을 선택해 주십시오.');

        for($k=0; $k<$opt_count; $k++) {
            if ($_POST['ct_qty'][$it_id][$k] < 1)
                alert('수량은 1 이상 입력해 주십시오.');
        }

        // 상품정보
        $sql = " select * from {$g5['g5_shop_item_table']} where it_id = '$it_id' ";
        $it = sql_fetch($sql);
        if(!$it['it_id'])
            alert('상품정보가 존재하지 않습니다.');

		if($it['it_price_type']=="Y" || $it['it_price_type']=="U")$it[it_price] = get_price($it);


        // 최소, 최대 수량 체크
        if($it['it_buy_min_qty'] || $it['it_buy_max_qty']) {
            $sum_qty = 0;
            for($k=0; $k<$opt_count; $k++) {
                if($_POST['io_type'][$it_id][$k] == 0)
                    $sum_qty += $_POST['ct_qty'][$it_id][$k];
            }

            if($it['it_buy_min_qty'] > 0 && $sum_qty < $it['it_buy_min_qty'])
                alert($it['it_name'].'의 선택옵션 개수 총합 '.number_format($it['it_buy_min_qty']).'개 이상 주문해 주십시오.');

            if($it['it_buy_max_qty'] > 0 && $sum_qty > $it['it_buy_max_qty'])
                alert($it['it_name'].'의 선택옵션 개수 총합 '.number_format($it['it_buy_max_qty']).'개 이하로 주문해 주십시오.');

            // 기존에 장바구니에 담긴 상품이 있는 경우에 최대 구매수량 체크
            if($it['it_buy_max_qty'] > 0) {
                $sql4 = " select count(*) as cnt
                            from {$g5['g5_shop_cart_table']}
                            where od_id = '$tmp_cart_id'
                              and it_id = '$it_id'
                              and io_type = '0'
                              and ct_status = '쇼핑' ";
                $row4 = sql_fetch($sql4);

                if(($sum_qty + $row4['cnt']) > $it['it_buy_max_qty'])
                    alert($it['it_name'].'의 선택옵션 개수 총합 '.number_format($it['it_buy_max_qty']).'개 이하로 주문해 주십시오.', './cart.php');
            }
        }

        // 옵션정보를 얻어서 배열에 저장
        $opt_list = array();
        $sql = " select * from {$g5['g5_shop_item_option_table']} where it_id = '$it_id' order by io_no asc ";
        $result = sql_query($sql);
        $lst_count = 0;
        for($k=0; $row=sql_fetch_array($result); $k++) {
            $opt_list[$row['io_type']][$row['io_id']]['id'] = $row['io_id'];
            $opt_list[$row['io_type']][$row['io_id']]['use'] = $row['io_use'];
            $opt_list[$row['io_type']][$row['io_id']]['price'] = $row['io_price'];
            $opt_list[$row['io_type']][$row['io_id']]['stock'] = $row['io_stock_qty'];

            // 선택옵션 개수
            if(!$row['io_type'])
                $lst_count++;
        }

        // 포인트
        $point = 0;
        if($config['cf_use_point'])
            $point = get_item_point($it);

        //--------------------------------------------------------
        //  재고 검사
        //--------------------------------------------------------
        // 이미 장바구니에 있는 같은 상품의 수량합계를 구한다.
        for($k=0; $k<$opt_count; $k++) {
            $io_id = $_POST['io_id'][$it_id][$k];
            $io_type = $_POST['io_type'][$it_id][$k];
            $io_value = $_POST['io_value'][$it_id][$k];

            $sql = " select SUM(ct_qty) as cnt from {$g5['g5_shop_cart_table']}
                      where it_id = '$it_id'
                        and io_id = '$io_id'
                        and io_type = '$io_type'
                        and ct_stock_use = 0
                        and ct_status = '".iconv("EUC-KR", "UTF-8", "쇼핑")."' ";
            $row = sql_fetch($sql);
            $sum_qty = $row['cnt'];

            // 재고 구함
            $ct_qty = $_POST['ct_qty'][$it_id][$k];
            if(!$io_id)
                $it_stock_qty = get_it_stock_qty($it_id);
            else
                $it_stock_qty = get_option_stock_qty($it_id, $io_id, $io_type);


            if ($ct_qty + $sum_qty > $it_stock_qty)
            {
                alert($io_value." 의 재고수량이 부족합니다.\\n\\n현재 재고수량 : " . number_format($it_stock_qty - $sum_qty) . " 개");
            }
        }
        //--------------------------------------------------------

        // 바로구매에 있던 장바구니 자료를 지운다.
        if($i == 0)
            sql_query(" delete from {$g5['g5_shop_cart_table']} where od_id = '$tmp_cart_id' and ct_direct = 1 ", false);

        // 옵션수정일 때 기존 장바구니 자료를 먼저 삭제
        if($act == 'optionmod')
            sql_query(" delete from {$g5['g5_shop_cart_table']} where od_id = '$tmp_cart_id' and it_id = '$it_id' ");

        // 장바구니에 Insert
        // 바로구매일 경우 장바구니가 체크된것으로 강제 설정
        if($sw_direct)
            $ct_select = 1;
        else
            $ct_select = 0;

        // 장바구니에 Insert
        $comma = '';
        $sql = " INSERT INTO {$g5['g5_shop_cart_table']}
                        ( od_id, mb_id, it_id, it_name, ct_status, ct_price, ct_point, ct_point_use, ct_stock_use, ct_option, ct_qty, ct_notax, io_id, io_type, io_price, ct_time, ct_ip, ct_send_cost, ct_direct, ct_select, auc_status, ct_op_option )
                    VALUES ";


		$auction_mode = $_POST[auction_mode];
		if($auction_mode == "auction"){
			$it_price = $_POST[it_price];
			$auction_status = "y";
		}else{
			$it_price = $it[it_price];
			$auction_status = "n";
		}

        for($k=0; $k<$opt_count; $k++) {
            $io_id = $_POST['io_id'][$it_id][$k];
            $io_type = $_POST['io_type'][$it_id][$k];
            $io_value = $_POST['io_value'][$it_id][$k];

            // 선택옵션정보가 존재하는데 선택된 옵션이 없으면 건너뜀
            if($lst_count && $io_id == '')
                continue;

            // 구매할 수 없는 옵션은 건너뜀
            if($io_id && !$opt_list[$io_type][$io_id]['use'])
                continue;

            $io_price = $opt_list[$io_type][$io_id]['price'];
            $ct_qty = $_POST['ct_qty'][$it_id][$k];

            // 구매가격이 음수인지 체크
            if($io_type) {
                if((int)$io_price < 0)
                    alert('구매금액이 음수인 상품은 구매할 수 없습니다.');
            } else {
                if((int)$it['it_price'] + (int)$io_price < 0)
                    alert('구매금액이 음수인 상품은 구매할 수 없습니다.');
            }

            // 동일옵션의 상품이 있으면 수량 더함
            $sql2 = " select ct_id
                        from {$g5['g5_shop_cart_table']}
                        where od_id = '$tmp_cart_id'
                          and it_id = '$it_id'
                          and io_id = '$io_id'
                          and ct_status = '쇼핑' ";
            $row2 = sql_fetch($sql2);

            if($row2['ct_id']) {

                $sql3 = " update {$g5['g5_shop_cart_table']}
                            set ct_qty = ct_qty + '$ct_qty'
                            where ct_id = '{$row2['ct_id']}' ";
                sql_query($sql3);
            }

            // 배송비결제
            if($it['it_sc_type'] == 1)
                $ct_send_cost = 2; // 무료
            else if($it['it_sc_type'] > 1 && $it['it_sc_method'] == 1)
                $ct_send_cost = 1; // 착불

            $sql .= $comma."( '$tmp_cart_id', '{$member['mb_id']}', '{$it['it_id']}', '".addslashes($it['it_name'])."', '쇼핑', '{$it_price}', '$point', '0', '0', '$io_value', '$ct_qty', '{$it['it_notax']}', '$io_id', '$io_type', '$io_price', '".G5_TIME_YMDHIS."', '$REMOTE_ADDR', '$ct_send_cost', '$sw_direct', '$ct_select', '$auction_status', '$op_name' )";
            $comma = ' , ';

            $ct_count++;
        }

        if($ct_count > 0)
            sql_query($sql);
    }
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
    	goto_url(G5_BBS_URL."/login.php?url=".urlencode("/shop/".$orderform_url."?sw_direct=$sw_direct&buy_kind=$buy_kind"));
    }
}
else
{
    goto_url(G5_SHOP_URL.'/cart.php');
}
?>
