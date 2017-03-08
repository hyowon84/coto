<?php
include_once('./_common.php');

$od_now_date = date("Y-m-d H:i:s", strtotime("+ ".$default[de_deposit_keep_term]."day"));

if($default[de_guest_cart_use] == 0){
	if($member[mb_id] == ""){
		alert("로그인 후 이용 가능합니다.");
	}
}

if (G5_IS_MOBILE) {
    include_once(G5_MSHOP_PATH.'/orderform.php');
    return;
}

set_session("ss_direct", $sw_direct);
// 장바구니가 비어있는가?
if ($sw_direct) {
    $tmp_cart_id = get_session('ss_cart_direct');
}
else {
    $tmp_cart_id = get_session('ss_cart_id');
}

if (get_cart_count($tmp_cart_id) == 0)
    alert('장바구니가 비어 있습니다.', G5_SHOP_URL.'/cart.php');

$g5['title'] = '주문서 작성';

// 전자결제를 사용할 때만 실행
if($default['de_iche_use'] || $default['de_vbank_use'] || $default['de_hp_use'] || $default['de_card_use']) {
    switch($default['de_pg_service']) {
        case 'lg':
            $g5['body_script'] = 'onload="isActiveXOK();"';
            break;
        default:
            $g5['body_script'] = 'onload="CheckPayplusInstall();"';
            break;
    }
}

include_once('./_head.php');
if ($default['de_hope_date_use']) {
    include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');
}

// 새로운 주문번호 생성
$od_id = get_uniqid();
set_session('ss_order_id', $od_id);
$s_cart_id = $tmp_cart_id;
$order_action_url = G5_HTTPS_SHOP_URL.'/orderformupdate.php';

require_once('./settle_'.$default['de_pg_service'].'.inc.php');

// 결제대행사별 코드 include (스크립트 등)
require_once('./'.$default['de_pg_service'].'/orderform.1.php');

$k = 0;
$j = 0;
?>

<!-- 구매 시작 { -->
<script src="<?php echo G5_JS_URL; ?>/shop.js"></script>

<!-- 타이틀 -->
<div class="cart_title"><?=$g5['title']?></div>

<!-- navi -->
<div class="cart_nav">
	<ul>
		<li><img src="img/cart_nav1.gif" border="0" align="absmiddle"></li>
		<li><img src="img/cart_nav2_on.gif" border="0" align="absmiddle"></li>
		<li><img src="img/cart_nav3.gif" border="0" align="absmiddle"></li>
	</ul>
</div>

<!-- 탭메뉴 -->
<!--
<div class="cl cart_tab" style="margin:10px 0 0 0;">
	<ul>
		<li class="on">투데이 스토어 상품</li>
		<li>공동구매 상품</li>
		<li class="right">구매대행 상품</li>
	</ul>
</div>
-->


<form name="forderform" id="forderform" method="post" action="<?php echo $order_action_url; ?>" onsubmit="return forderform_check(this);" autocomplete="off">
<input type="hidden" name="buy_kind" id="buy_kind" value="<?=$buy_kind?>">

<div class="list_box">

	<div class="sub_title">일반상품</div>

	<div id="sod_frm">
		<!-- 주문상품 확인 시작 { -->

		<div class="tbl_head01 tbl_wrap product1">
			<table id="sod_list">
			<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="ct_all" value="1" id="ct_all" checked="checked" status="1" idx="1"></th>
				<th scope="col" class="right" colspan="2">상품정보</th>
				<th scope="col" class="right" width="70px">수량</th>
				<th scope="col" class="right">상품금액</th>
				<th scope="col" class="right">옵션</th>
				<th scope="col" class="right">할인</th>
				<th scope="col" width="100px">배송비</th>
			</tr>
			</thead>
			<tbody>
			<?php
			$tot_point = 0;
			$tot_sell_price = 0;
			$send_cost1 = 0;

			$is_bank_payment = $is_cash_payment =  true;

			$goods = $goods_it_id = "";
			$goods_count = -1;

			// $s_cart_id 로 현재 장바구니 자료 쿼리
			$sql = " select a.ct_id,
							a.it_id,
							a.it_name,
							a.ct_price,
							a.ct_point,
							a.ct_qty,
							a.ct_status,
							a.ct_send_cost,
							a.ct_gubun,
							a.ct_payment,
							a.ct_op_option,
							b.ca_id,
							b.ca_id2,
							b.ca_id3,
							b.it_notax
					   from {$g5['g5_shop_cart_table']} a left join {$g5['g5_shop_item_table']} b on ( a.it_id = b.it_id )
					  where a.od_id = '$s_cart_id' and (a.auc_status='1' or a.auc_status is NULL or a.auc_status='n')
						and a.ct_select = '1'
						and a.mb_id='".$member[mb_id]."'
						";

			if($default['de_cart_keep_term']) {
				$ctime = date('Y-m-d', G5_SERVER_TIME - ($default['de_cart_keep_term'] * 86400));
				$sql .= " and substring(a.ct_time, 1, 10) >= '$ctime' ";
			}
			$sql .= " group by a.it_id ";
			$sql .= " order by a.ct_id ";
			$result = sql_query($sql);

			$good_info = '';
			$it_send_cost = 0;
			$it_cp_count = 0;

			$comm_tax_mny = 0; // 과세금액
			$comm_vat_mny = 0; // 부가세
			$comm_free_mny = 0; // 면세금액
			$tot_tax_mny = 0;

			for ($i=0; $row=mysql_fetch_array($result); $i++)
			{
				// 합계금액 계산
				$sql = " select SUM(IF(io_type = 1, (io_price * ct_qty), ((ct_price + io_price) * ct_qty))) as price,
								SUM(ct_point * ct_qty) as point,
								SUM(ct_qty) as qty
							from {$g5['g5_shop_cart_table']}
							where it_id = '{$row['it_id']}'
							  and od_id = '$s_cart_id' ";
				$sum = sql_fetch($sql);

				if (!$goods)
				{
					//$goods = addslashes($row[it_name]);
					//$goods = get_text($row[it_name]);
					$goods = preg_replace("/\'|\"|\||\,|\&|\;/", "", $row['it_name']);
					$goods_it_id = $row['it_id'];
				}
				$goods_count++;

				// 에스크로 상품정보
				if($default['de_escrow_use']) {
					if ($i>0)
						$good_info .= chr(30);
					$good_info .= "seq=".($i+1).chr(31);
					$good_info .= "ordr_numb={$od_id}_".sprintf("%04d", $i).chr(31);
					$good_info .= "good_name=".addslashes($row['it_name']).chr(31);
					$good_info .= "good_cntx=".$row['ct_qty'].chr(31);
					$good_info .= "good_amtx=".$row['ct_price'].chr(31);
				}

				if($row[ct_gubun]=="P"){
					$image = get_gp_image($row['it_id'], 50, 50);
					$name = get_gp_image1($row['it_id']);
				}else{
					$image = get_it_image($row['it_id'], 50, 50);
					$name = $row['it_name'];
				}

				$it_name = '<b>' . stripslashes($row['it_name']) . '</b>';
				$it_options = print_item_options($row['it_id'], $s_cart_id);
				if($it_options) {
					$it_name .= '<div class="sod_opt">'.$it_options.'</div>';
				}

				// 복합과세금액
				if($default['de_tax_flag_use']) {
					if($row['it_notax']) {
						$comm_free_mny += $sum['price'];
					} else {
						$tot_tax_mny += $sum['price'];
					}
				}

				//옵션 상품
				$op_arr = explode("|", $row[ct_op_option]);
				$op_price = 0;
				$op_name = "";
				for($b = 0; $b < count($op_arr); $b++){
					if($op_arr[$b]){
						$op_row = sql_fetch("select * from {$g5['g5_shop_option2_table']} where it_id='".$row[it_id]."' and con='".$op_arr[$b]."' ");
						$op_price = $op_price + $op_row[price];
						$op_name .= $op_row[con].",";
					}
				}
				$op_name = substr($op_name, 0, strlen($op_name)-1);

				$point      = $sum['point'];
				$sell_price = $sum['price'] + $op_price;

				// 쿠폰
				if($is_member) {
					$cp_button = '';
					$cp_count = 0;

					$sql = " select cp_id
								from {$g5['g5_shop_coupon_table']}
								where mb_id IN ( '{$member['mb_id']}', '전체회원' )
								  and cp_start <= '".G5_TIME_YMD."'
								  and cp_end >= '".G5_TIME_YMD."'
								  and cp_minimum <= '$sell_price'
								  and (
										( cp_method = '0' and cp_target = '{$row['it_id']}' )
										OR
										( cp_method = '1' and ( cp_target IN ( '{$row['ca_id']}', '{$row['ca_id2']}', '{$row['ca_id3']}' ) ) )
									  ) ";
					$res = sql_query($sql);

					for($k=0; $cp=sql_fetch_array($res); $k++) {
						if(is_used_coupon($member['mb_id'], $cp['cp_id']))
							continue;

						$cp_count++;
					}

					if($cp_count) {
						$cp_button = '<button type="button" class="cp_btn btn_frmline">적용</button>';
						$it_cp_count++;
					}
				}

				// 배송비
				switch($row['ct_send_cost'])
				{
					case 1:
						$ct_send_cost = '착불';
						break;
					case 2:
						$ct_send_cost = '무료';
						break;
					default:
						$ct_send_cost = '선불';
						break;
				}

				$item = sql_fetch("select * from {$g5['g5_shop_item_table']} where it_id='".$row[it_id]."' ");

				if($row[ct_gubun]=="P"){
					if($row[ct_payment]=="B")$is_cash_payment = false;
					elseif($row[ct_payment]=="C") $is_bank_payment =  false;
				}
			?>

			<tr>
				<td>
					<input type="checkbox" name="ct_chk[<?php echo $k; ?>]" value="1" id="ct_chk_<?php echo $k; ?>" ct_send_cost="<?=$item[it_sc_price]?>" status="1" checked="checked">
				</td>
				<td class="sod_img"><?php echo $image; ?></td>
				<td class="right">
					<input type="hidden" name="it_id[<?php echo $i; ?>]"    value="<?php echo $row['it_id']; ?>">
					<input type="hidden" name="it_name[<?php echo $i; ?>]"  value="<?php echo get_text($row['it_name']); ?>">
					<input type="hidden" name="it_price[<?php echo $i; ?>]" value="<?php echo $sell_price; ?>">
					<input type="hidden" name="cp_id[<?php echo $i; ?>]" value="">
					<input type="hidden" name="cp_price[<?php echo $i; ?>]" value="0">
					<input type="hidden" name="ca_id[<?php echo $i; ?>]"    value="<?php echo $row['ct_type']; ?>">
					<div>
						<?php if($default['de_tax_flag_use']) { ?>
						<input type="hidden" name="it_notax[<?php echo $i; ?>]" value="<?php echo $row['it_notax']; ?>">
						<?php } ?>
						<?php echo stripslashes($it_name); ?>
					</div>
					<div style="margin:7px 0 0 0;">
						<div style="float:left;width:50%;text-align:right;">판매가</div>
						<div class="pro_price"><?php echo number_format($row['ct_price']); ?></div>
					</div>
				</td>
				<td class="td_num right"><?php echo number_format($sum['qty']); ?></td>
				<td class="td_numbig right"><span class="total_price sell_price"><?php echo number_format($sell_price); ?></span></td>
				<td class="td_num right right"><?=$op_name?></td>
				<td class="td_num right right">-</td>
				<td class="td_num"><?php echo $ct_send_cost; ?></td>
			</tr>

			<?php
				$tot_point      += $point;
				$tot_sell_price += $sell_price;
				$send_cost1 += $item[it_sc_price];

				$k++;
				$j++;
			} // for 끝

			if ($j != 0) {
				// 배송비 계산
				//$send_cost = get_sendcost($s_cart_id);
				//$send_cost1 = get_sendcost1($s_cart_id, 1, "n");

				if($j > 1){
					$send_cost = $send_cost + 3500;
					$send_cost1 = 3500;
				}else{
					$send_cost = $send_cost + $send_cost1;
					$send_cost1 = $send_cost1;
				}
			}

			// 복합과세처리
			if($default['de_tax_flag_use']) {
				$comm_tax_mny = round(($tot_tax_mny + $send_cost) / 1.1);
				$comm_vat_mny = ($tot_tax_mny + $send_cost) - $comm_tax_mny;
			}
			?>
			</tbody>
			</table>
		</div>

		<!-- 상품 선택 버튼 -->
		
		<div class="pro_choi_bn">
		<!--
			<ul>
				<li class="pro_all_chk" idx="1">상품 전체선택</li>
				<li class="pro_all_rel" idx="1">상품 선택해제</li>
				<li onclick="return form_check('seldelete1', '1');">선택상품 삭제</li>
			</ul>
		-->
		</div>
		

		<!-- 일반상품 가격 정보 -->
		<div class="price_info price_info1">
			<table border="0" cellspacing="0" cellpadding="0" width="100%" height="100px" class="price_info_tb">
				<tr height="40px">
					<td rowspan="2" width="87px" class="gubun">
						일반상품 
					</td>
					<td width="188px">
						상품금액
					</td>
					<td width="135px">
						할인금액
					</td>
					<td width="150px">
						배송비
					</td>
					<td>
						결제예상금액
					</td>
				</tr>
				<tr>
					<td><div class="price_box"><span class="price">0</span><span style="color:#818181;font-weight:normal;font-size:14px;">원</span></div></td>
					<td><div class="price_box"><span class="price">0</span><span style="color:#818181;font-weight:normal;font-size:14px;">원</span></div></td>
					<td><div class="price_box"><span class="price"><?=number_format($send_cost1)?></span><span style="color:#818181;font-weight:normal;font-size:14px;">원</span></div></td>
					<td><div class="price_box all"><span class="price">0</span><span style="font-weight:normal;font-size:14px;">원</span></div></td>
				</tr>
			</table>
		</div>

	</div>

	<div class="sub_title">경매상품</div>

	<div id="sod_frm">
		<!-- 주문상품 확인 시작 { -->

		<div class="tbl_head01 tbl_wrap product2">
			<table id="sod_list">
			<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="ct_all" value="1" id="ct_all" checked="checked" status="2" idx="2"></th>
				<th scope="col" class="right" colspan="2">상품정보</th>
				<th scope="col" class="right" width="70px">수량</th>
				<th scope="col" class="right">상품금액</th>
				<th scope="col" class="right">할인</th>
				<th scope="col" width="100px">배송비</th>
			</tr>
			</thead>
			<tbody>
			<?php

			$j = 0;
			$cost = 0;
			//$tot_point = 0;
			//$tot_sell_price = 0;
			$send_cost1 = 0;

			$is_bank_payment = $is_cash_payment =  true;

			$goods_count = -1;

			// $s_cart_id 로 현재 장바구니 자료 쿼리
			$sql = " select a.ct_id,
							a.it_id,
							a.it_name,
							a.ct_price,
							a.ct_point,
							a.ct_qty,
							a.ct_status,
							a.ct_send_cost,
							a.ct_gubun,
							a.ct_payment,
							b.ca_id,
							b.ca_id2,
							b.ca_id3,
							b.it_notax
					   from {$g5['g5_shop_cart_table']} a left join {$g5['g5_shop_item_table']} b on ( a.it_id = b.it_id )
					  where a.od_id = '$s_cart_id' and a.auc_status='y'
						and a.ct_select = '1' ";

			if($default['de_cart_keep_term']) {
				$ctime = date('Y-m-d', G5_SERVER_TIME - ($default['de_cart_keep_term'] * 86400));
				$sql .= " and substring(a.ct_time, 1, 10) >= '$ctime' ";
			}
			$sql .= " group by a.it_id ";
			$sql .= " order by a.ct_id ";
			$result = sql_query($sql);

			$good_info = '';
			$it_send_cost = 0;
			$it_cp_count = 0;

			$comm_tax_mny = 0; // 과세금액
			$comm_vat_mny = 0; // 부가세
			$comm_free_mny = 0; // 면세금액
			$tot_tax_mny = 0;

			$num = mysql_num_rows($result);

			for ($i=0; $row=mysql_fetch_array($result); $i++)
			{
				// 합계금액 계산
				$sql = " select SUM(IF(io_type = 1, (io_price * ct_qty), ((ct_price + io_price) * ct_qty))) as price,
								SUM(ct_point * ct_qty) as point,
								SUM(ct_qty) as qty
							from {$g5['g5_shop_cart_table']}
							where it_id = '{$row['it_id']}'
							  and od_id = '$s_cart_id' ";
				$sum = sql_fetch($sql);

				if (!$goods)
				{
					//$goods = addslashes($row[it_name]);
					//$goods = get_text($row[it_name]);
					$goods = preg_replace("/\'|\"|\||\,|\&|\;/", "", $row['it_name']);
					$goods_it_id = $row['it_id'];
				}
				$goods_count++;

				// 에스크로 상품정보
				if($default['de_escrow_use']) {
					if ($i>0)
						$good_info .= chr(30);
					$good_info .= "seq=".($i+1).chr(31);
					$good_info .= "ordr_numb={$od_id}_".sprintf("%04d", $i).chr(31);
					$good_info .= "good_name=".addslashes($row['it_name']).chr(31);
					$good_info .= "good_cntx=".$row['ct_qty'].chr(31);
					$good_info .= "good_amtx=".$row['ct_price'].chr(31);
				}

				if($row[ct_gubun]=="P")$image = get_gp_image($row['it_id'], 50, 50);
				else $image = get_it_image($row['it_id'], 50, 50);

				$it_name = '<b>' . stripslashes($row['it_name']) . '</b>';
				$it_options = print_item_options($row['it_id'], $s_cart_id);
				if($it_options) {
					$it_name .= '<div class="sod_opt">'.$it_options.'</div>';
				}

				// 복합과세금액
				if($default['de_tax_flag_use']) {
					if($row['it_notax']) {
						$comm_free_mny += $sum['price'];
					} else {
						$tot_tax_mny += $sum['price'];
					}
				}

				// 쿠폰
				if($is_member) {
					$cp_button = '';
					$cp_count = 0;

					$sql = " select cp_id
								from {$g5['g5_shop_coupon_table']}
								where mb_id IN ( '{$member['mb_id']}', '전체회원' )
								  and cp_start <= '".G5_TIME_YMD."'
								  and cp_end >= '".G5_TIME_YMD."'
								  and cp_minimum <= '$sell_price'
								  and (
										( cp_method = '0' and cp_target = '{$row['it_id']}' )
										OR
										( cp_method = '1' and ( cp_target IN ( '{$row['ca_id']}', '{$row['ca_id2']}', '{$row['ca_id3']}' ) ) )
									  ) ";
					$res = sql_query($sql);

					for($k=0; $cp=sql_fetch_array($res); $k++) {
						if(is_used_coupon($member['mb_id'], $cp['cp_id']))
							continue;

						$cp_count++;
					}

					if($cp_count) {
						$cp_button = '<button type="button" class="cp_btn btn_frmline">적용</button>';
						$it_cp_count++;
					}
				}

				// 배송비
				switch($row['ct_send_cost'])
				{
					case 1:
						$ct_send_cost = '착불';
						break;
					case 2:
						$ct_send_cost = '무료';
						break;
					default:
						$ct_send_cost = '선불';
						break;
				}

				$item = sql_fetch("select * from {$g5['g5_shop_item_table']} where it_id='".$row[it_id]."' ");

				$auc_row1 = sql_fetch("select * from g5_shop_auction where it_id='".$row['it_id']."' order by no desc limit 0, 1 ");
				$my_auc_row = sql_fetch("select * from g5_shop_auction where it_id='".$row['it_id']."' and loginid='".$member[mb_id]."' order by no desc limit 0, 1 ");
				$my_max_auc_row = sql_fetch("select * from {$g5['g5_shop_auction_max_table']} where it_id='".$row['it_id']."' and loginid='".$member[mb_id]."' order by no desc limit 0, 1 ");

				if($row[ct_gubun]=="P"){
					if($row[ct_payment]=="B")$is_cash_payment = false;
					elseif($row[ct_payment]=="C") $is_bank_payment =  false;
				}


				$point      = $sum['point'];
				//$sell_price = $sum['price'];
				$sell_price = $auc_row1[it_last_bid];
			?>

			<tr>
				<td>
					<input type="checkbox" name="ct_chk[<?php echo $k; ?>]" value="1" id="ct_chk_<?php echo $k; ?>" status="2" ct_send_cost="<?=$item[it_sc_price]?>" checked="checked">
				</td>
				<td class="sod_img"><?php echo $image; ?></td>
				<td class="right">
					<input type="hidden" name="it_id[<?php echo $i; ?>]"    value="<?php echo $row['it_id']; ?>">
					<input type="hidden" name="it_name[<?php echo $i; ?>]"  value="<?php echo get_text($row['it_name']); ?>">
					<input type="hidden" name="it_price[<?php echo $i; ?>]" value="<?php echo $sell_price; ?>">
					<input type="hidden" name="cp_id[<?php echo $i; ?>]" value="">
					<input type="hidden" name="cp_price[<?php echo $i; ?>]" value="0">
					<input type="hidden" name="ca_id[<?php echo $i; ?>]"    value="<?php echo $row['ct_type']; ?>">
					<div>
						<?php if($default['de_tax_flag_use']) { ?>
						<input type="hidden" name="it_notax[<?php echo $i; ?>]" value="<?php echo $row['it_notax']; ?>">
						<?php } ?>
						<?php echo stripslashes($row['it_name']); ?>
					</div>
					<div style="margin:7px 0 0 0;">
						<div style="float:left;width:50%;text-align:right;">판매가</div>
						<div class="pro_price">
							<?php echo number_format($sell_price); ?>
							<?php// echo number_format($row['ct_price']); ?>
						</div>
					</div>
				</td>
				<td class="td_num right"><?php echo number_format($sum['qty']); ?></td>
				<td class="td_numbig right"><span class="total_price sell_price"><?php echo number_format($sell_price); ?></span></td>
				<td class="td_num right right">-</td>
				<td class="td_num"><?php echo $ct_send_cost; ?></td>
			</tr>

			<?php
				$tot_point      += $point;
				$tot_sell_price += $sell_price;
				$send_cost1 += $item[it_sc_price];

				$k++;
				$j++;
			} // for 끝

			if ($j != 0) {
				// 배송비 계산
				//$send_cost = get_sendcost($s_cart_id);
				//$send_cost1 = get_sendcost1($s_cart_id, 1, "n");

				if ($j != 0) {
					// 배송비 계산
					//$send_cost = get_sendcost($s_cart_id);
					//$send_cost1 = get_sendcost1($s_cart_id, 1, "n");

					if($j > 1){
						$send_cost = $send_cost + 3500;
						$send_cost1 = 3500;
					}else{
						
						$send_cost = $send_cost + $send_cost1;
						$send_cost1 = $send_cost1;
					}
				}
			}

			$cost1 = $cost1;

			// 복합과세처리
			if($default['de_tax_flag_use']) {
				$comm_tax_mny = round(($tot_tax_mny + $send_cost) / 1.1);
				$comm_vat_mny = ($tot_tax_mny + $send_cost) - $comm_tax_mny;
			}

			if ($k == 0) {
				//echo '<tr><td colspan="7" class="empty_table">장바구니에 담긴 상품이 없습니다.</td></tr>';
				alert('장바구니가 비어 있습니다.', G5_SHOP_URL.'/cart.php');
			}
			?>
			</tbody>
			</table>
		</div>

		<!-- 상품 선택 버튼 -->
		
		<div class="pro_choi_bn">
		<!--
			<ul>
				<li class="pro_all_chk" idx="2">상품 전체선택</li>
				<li class="pro_all_rel" idx="2">상품 선택해제</li>
				<li onclick="return form_check('seldelete2', '2');">선택상품 삭제</li>
			</ul>
		-->
		</div>
		

		<!-- 경매상품 가격 정보 -->
		<div class="price_info price_info2">
			<table border="0" cellspacing="0" cellpadding="0" width="100%" height="100px" class="price_info_tb">
				<tr height="40px">
					<td rowspan="2" width="87px" class="gubun">
						경매상품 
					</td>
					<td width="188px">
						상품금액
					</td>
					<td width="135px">
						할인금액
					</td>
					<td width="150px">
						배송비
					</td>
					<td>
						결제예상금액
					</td>
				</tr>
				<tr>
					<td><div class="price_box"><span class="price">0</span><span style="color:#818181;font-weight:normal;font-size:14px;">원</span></div></td>
					<td><div class="price_box"><span class="price">0</span><span style="color:#818181;font-weight:normal;font-size:14px;">원</span></div></td>
					<td><div class="price_box"><span class="price"><?=number_format($send_cost1)?></span><span style="color:#818181;font-weight:normal;font-size:14px;">원</span></div></td>
					<td><div class="price_box all"><span class="price">0</span><span style="font-weight:normal;font-size:14px;">원</span></div></td>
				</tr>
			</table>
		</div>


	</div>

	<input type="hidden" name="od_price"    value="<?php echo $tot_sell_price; ?>">
	<input type="hidden" name="org_od_price"    value="<?php echo $tot_sell_price; ?>">
	<input type="hidden" name="od_send_cost" value="<?php echo $send_cost; ?>">
	<input type="hidden" name="od_send_cost2" value="0">
	<input type="hidden" name="item_coupon" value="0">
	<input type="hidden" name="od_coupon" value="0">
	<input type="hidden" name="od_send_coupon" value="0">

	<?php
    // 결제대행사별 코드 include (결제대행사 정보 필드)
    require_once('./'.$default['de_pg_service'].'/orderform.2.php');
    ?>
	
	<?php
		/* 상품코드 설정 파라미터 입니다.(상품권을 따로 구분하여 처리할 수 있는 옵션기능입니다.)
		<input type="hidden" name="good_cd"      value=""> */

		/* = -------------------------------------------------------------------------- = */
		/* =   4. 옵션 정보 END                                                         = */
		/* ============================================================================== */
	?>

	<!-- 구매자 정보 입력 시작 { -->
	<section id="sod_frm_orderer">
		<h2>구매자 정보</h2>

		<div class="tbl_frm01 tbl_wrap">
			<table>
			<tbody>
			<tr>
				<th scope="row"><label for="od_name">이름</label></th>
				<td><input type="text" name="od_name" value="<?php echo $member['mb_name']; ?>" id="od_name" required class="frm_input required" maxlength="20"></td>
			</tr>

			<?php if (!$is_member) { // 비회원이면 ?>
			<tr>
				<th scope="row"><label for="od_pwd">비밀번호</label></th>
				<td>
					<span class="frm_info">영,숫자 3~20자 (주문서 조회시 필요)</span>
					<input type="password" name="od_pwd" id="od_pwd" required class="frm_input required" maxlength="20">
				</td>
			</tr>
			<?php } ?>

			<tr>
				<th scope="row"><label for="od_tel">연락처</label></th>
				<td><input type="text" name="od_tel" value="<?php echo $member['mb_hp']; ?>" id="od_tel" required class="frm_input required" maxlength="20"></td>
			</tr>
			
			<!--
			<tr>
				<th scope="row"><label for="od_hp">핸드폰</label></th>
				<td><input type="text" name="od_hp" value="<?php// echo $member['mb_hp']; ?>" id="od_hp" class="frm_input" maxlength="20"></td>
			</tr>
			-->

			<input type="hidden" name="od_hp" value="<?php echo $member['mb_hp']; ?>" id="od_hp" class="frm_input" maxlength="20">
			
			<!--
			<?php// $zip_href = G5_BBS_URL.'/zip.php?frm_name=forderform&amp;frm_zip1=od_zip1&amp;frm_zip2=od_zip2&amp;frm_addr1=od_addr1&amp;frm_addr2=od_addr2&amp;frm_addr3=od_addr3&amp;frm_jibeon=od_addr_jibeon'; ?>
			<tr>
				<th scope="row">주소</th>
				<td>
					<label for="od_zip1" class="sound_only">우편번호 앞자리<strong class="sound_only"> 필수</strong></label>
					<input type="text" name="od_zip1" value="<?php// echo $member['mb_zip1'] ?>" id="od_zip1" required class="frm_input required" size="3" maxlength="3">
					-
					<label for="od_zip2" class="sound_only">우편번호 뒷자리<strong class="sound_only"> 필수</strong></label>
					<input type="text" name="od_zip2" value="<?php// echo $member['mb_zip2'] ?>" id="od_zip2" required class="frm_input required" size="3" maxlength="3">
					<a href="<?php// echo $zip_href; ?>" class="btn_frmline win_zip_find" target="_blank">주소 검색</a><br>
					<input type="text" name="od_addr1" value="<?php// echo $member['mb_addr1'] ?>" id="od_addr1" required class="frm_input frm_address required" size="60">
					<label for="od_addr1">기본주소<strong class="sound_only"> 필수</strong></label><br>
					<input type="text" name="od_addr2" value="<?php// echo $member['mb_addr2'] ?>" id="od_addr2" class="frm_input frm_address" size="60">
					<label for="od_addr2">상세주소</label><br>
					<input type="text" name="od_addr3" value="<?php// echo $member['mb_addr3'] ?>" id="od_addr3" readonly="readonly" class="frm_input frm_address" size="60">
					<label for="od_addr3">참고항목</label>
					<input type="hidden" name="od_addr_jibeon" value="<?php// echo $member['mb_addr_jibeon']; ?>"><br>
					<span id="od_addr_jibeon"><?php// echo ($member['mb_addr_jibeon'] ? '지번주소 : '.$member['mb_addr_jibeon'] : ''); ?></span>
				</td>
			</tr>
			-->

			<tr>
				<th scope="row"><label for="od_email">이메일</label></th>
				<td>
					<input type="hidden" name="od_email" value="<?php echo $member['mb_id']; ?>" id="od_email" required class="frm_input required">
					<?php echo $member['mb_id']; ?>
				</td>
			</tr>

			<?php if ($default['de_hope_date_use']) { // 배송희망일 사용 ?>
			<tr>
				<th scope="row"><label for="od_hope_date">희망배송일</label></th>
				<td>
					<!-- <select name="od_hope_date" id="od_hope_date">
					<option value="">선택하십시오.</option>
					<?php
					for ($i=0; $i<7; $i++) {
						$sdate = date("Y-m-d", time()+86400*($default['de_hope_date_after']+$i));
						echo '<option value="'.$sdate.'">'.$sdate.' ('.get_yoil($sdate).')</option>'.PHP_EOL;
					}
					?>
					</select> -->
					<input type="text" name="od_hope_date" value="" id="od_hope_date" required class="frm_input required" size="11" maxlength="10" readonly="readonly"> 이후로 배송 바랍니다.
				</td>
			</tr>
			<?php } ?>
			</tbody>
			</table>
		</div>
	</section>
	<!-- } 구매자 정보 입력 끝 -->

	<!-- 배송지 정보 입력 시작 { -->
	<section id="sod_frm_taker">
		<h2>배송지 정보</h2>

		<div class="tbl_frm01 tbl_wrap">
			<table>
			<tbody>
			<?php
			if($is_member) {
				// 배송지 이력
				$addr_list = '';
				$sep = chr(30);

				// 주문자와 동일
				$addr_list .= '<input type="radio" name="ad_sel_addr" value="same" id="ad_sel_addr_same" checked>'.PHP_EOL;
				$addr_list .= '<label for="ad_sel_addr_same">주문자와 동일</label>'.PHP_EOL;

				// 기본배송지
				$sql = " select *
							from {$g5['g5_shop_order_address_table']}
							where mb_id = '{$member['mb_id']}'
							  and ad_default = '1' ";
				$row = sql_fetch($sql);
				if($row['ad_id']) {
					$val1 = $row['ad_name'].$sep.$row['ad_tel'].$sep.$row['ad_hp'].$sep.$row['ad_zip1'].$sep.$row['ad_zip2'].$sep.$row['ad_addr1'].$sep.$row['ad_addr2'].$sep.$row['ad_addr3'].$sep.$row['ad_jibeon'].$sep.$row['ad_subject'];
					$addr_list .= '<input type="radio" name="ad_sel_addr" value="'.$val1.'" id="ad_sel_addr_def">'.PHP_EOL;
					$addr_list .= '<label for="ad_sel_addr_def">기본배송지</label>'.PHP_EOL;
				}

				// 최근배송지
				$sql = " select *
							from {$g5['g5_shop_order_address_table']}
							where mb_id = '{$member['mb_id']}'
							  and ad_default = '0'
							order by ad_id desc
							limit 1 ";
				$result = sql_query($sql);
				for($i=0; $row=sql_fetch_array($result); $i++) {
					$val1 = $row['ad_name'].$sep.$row['ad_tel'].$sep.$row['ad_hp'].$sep.$row['ad_zip1'].$sep.$row['ad_zip2'].$sep.$row['ad_addr1'].$sep.$row['ad_addr2'].$sep.$row['ad_addr3'].$sep.$row['ad_jibeon'].$sep.$row['ad_subject'];
					$val2 = '<label for="ad_sel_addr_'.($i+1).'">최근 배송지('.($row['ad_subject'] ? $row['ad_subject'] : $row['ad_name']).')</label>';
					$addr_list .= '<input type="radio" name="ad_sel_addr" value="'.$val1.'" id="ad_sel_addr_'.($i+1).'"> '.PHP_EOL.$val2.PHP_EOL;
				}

				$addr_list .= '<input type="radio" name="ad_sel_addr" value="new" id="od_sel_addr_new">'.PHP_EOL;
				$addr_list .= '<label for="od_sel_addr_new">새로운 배송지</label>'.PHP_EOL;

				$addr_list .='<a href="'.G5_SHOP_URL.'/orderaddress.php" id="order_address" class="btn_frmline">배송지목록</a>';
			} else {
				// 주문자와 동일
				$addr_list .= '<input type="checkbox" name="ad_sel_addr" value="same" id="ad_sel_addr_same">'.PHP_EOL;
				$addr_list .= '<label for="ad_sel_addr_same">구매자 정보와 동일</label>'.PHP_EOL;
			}
			?>
			<tr>
				<th scope="row">배송지 선택</th>
				<td>
					<?php echo $addr_list; ?>
				</td>
			</tr>

			<!--
			<?php// if($is_member) { ?>
			<tr>
				<th scope="row"><label for="ad_subject">배송지명</label></th>
				<td>
					<input type="text" name="ad_subject" id="ad_subject" class="frm_input" maxlength="20">
					<input type="checkbox" name="ad_default" id="ad_default" value="1">
					<label for="ad_default">기본배송지로 설정</label>
				</td>
			</tr>
			<?php// } ?>
			-->
			<input type="hidden" name="ad_subject" id="ad_subject">

			<tr>
				<th scope="row"><label for="od_b_name">이름</label></th>
				<td><input type="text" name="od_b_name" id="od_b_name" required class="frm_input required" maxlength="20" value="<?=$member[mb_name]?>"></td>
			</tr>
			<tr>
				<th scope="row"><label for="od_b_tel">연락처</label></th>
				<td><input type="text" name="od_b_tel" id="od_b_tel" required class="frm_input required" maxlength="20" value="<?php echo $member['mb_hp']; ?>"></td>
			</tr>

			<!--
			<tr>
				<th scope="row"><label for="od_b_hp">핸드폰</label></th>
				<td><input type="text" name="od_b_hp" id="od_b_hp" class="frm_input" maxlength="20"></td>
			</tr>
			-->
			<input type="hidden" name="od_b_hp" id="od_b_hp">

			<?php $zip_href = G5_BBS_URL.'/zip.php?frm_name=forderform&amp;frm_zip1=od_b_zip1&amp;frm_zip2=od_b_zip2&amp;frm_addr1=od_b_addr1&amp;frm_addr2=od_b_addr2&amp;frm_addr3=od_b_addr3&amp;frm_jibeon=od_b_addr_jibeon'; ?>
			<tr>
				<th scope="row">주소</th>
				<td id="sod_frm_addr">
					<label for="od_b_zip1" class="sound_only">우편번호 앞자리<strong class="sound_only"> 필수</strong></label>
					<input type="text" name="od_b_zip1" id="od_b_zip1" required class="frm_input required" size="3" maxlength="3" value="<?=$member[mb_zip1]?>">
					-
					<label for="od_b_zip2" class="sound_only">우편번호 뒷자리<strong class="sound_only"> 필수</strong></label>
					<input type="text" name="od_b_zip2" id="od_b_zip2" required class="frm_input required" size="3" maxlength="3" value="<?=$member[mb_zip2]?>">
					<a href="<?php echo $zip_href; ?>" class="btn_frmline win_zip_find" target="_blank">주소 검색</a><br>
					<input type="text" name="od_b_addr1" id="od_b_addr1" required class="frm_input frm_address required" size="40" value="<?=$member[mb_addr1]?>">
					
					<!--<input type="text" name="od_b_addr2" id="od_b_addr2" class="frm_input frm_address" size="60">
					<label for="od_b_addr2">상세주소</label>-->
					<input type="hidden" name="od_b_addr2" id="od_b_addr2" value="<?=$member[mb_addr2]?>">
					<input type="text" name="od_b_addr3" id="od_b_addr3" readonly="readonly" class="frm_input frm_address" size="40" value="<?=$member[mb_addr3]?>">
					
					<input type="hidden" name="od_b_addr_jibeon" value="<?=$member[mb_addr_jibeon]?>">
					<span id="od_b_addr_jibeon"></span>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="od_memo">배송 메모</label></th>
				<td>
					<div style="float:left;width:90%;"><textarea name="od_memo" id="od_memo"></textarea></div>
					<div style="float:left;text-align:left;margin:30px 0 0 0;font-size:13px;">(0/50자)</div>
				</td>
			</tr>
			</tbody>
			</table>
		</div>
	</section>
	<!-- } 배송지 정보 입력 끝 -->



	<!-- 결제정보 입력 시작 { -->
	<?php
	$oc_cnt = $sc_cnt = 0;
	if($is_member) {
		// 주문쿠폰
		$sql = " select cp_id
					from {$g5['g5_shop_coupon_table']}
					where mb_id IN ( '{$member['mb_id']}', '전체회원' )
					  and cp_method = '2'
					  and cp_start <= '".G5_TIM_YMD."'
					  and cp_end >= '".G5_TIME_YMD."' ";
		$res = sql_query($sql);

		for($k=0; $cp=sql_fetch_array($res); $k++) {
			if(is_used_coupon($member['mb_id'], $cp['cp_id']))
				continue;

			$oc_cnt++;
		}

		if($send_cost > 0) {
			// 배송비쿠폰
			$sql = " select cp_id
						from {$g5['g5_shop_coupon_table']}
						where mb_id IN ( '{$member['mb_id']}', '전체회원' )
						  and cp_method = '3'
						  and cp_start <= '".G5_TIM_YMD."'
						  and cp_end >= '".G5_TIME_YMD."' ";
			$res = sql_query($sql);

			for($k=0; $cp=sql_fetch_array($res); $k++) {
				if(is_used_coupon($member['mb_id'], $cp['cp_id']))
					continue;

				$sc_cnt++;
			}
		}
	}
	?>

	<section id="sod_frm_pay">
		<h2>결제정보입력(입금 기간은 <?=$default[de_deposit_keep_term]?>일입니다.)</h2>

		<div class="tbl_frm01 tbl_wrap">
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td style="padding:0;border:0;width:65%;">
						<table>
						<tbody>
						<tr>
							<th scope="row">적립금 사용</th>
							<td style="line-height:20px;">

								<?
								$temp_point = 0;
								// 회원이면서 포인트사용이면
								if ($is_member && $config['cf_use_point'])
								{
									// 포인트 결제 사용 포인트보다 회원의 포인트가 크다면
									if ($member['mb_point'] >= $default['de_settle_min_point'])
									{
										$temp_point = (int)$default['de_settle_max_point'];

										if($temp_point > (int)$tot_sell_price)
											$temp_point = (int)$tot_sell_price;

										if($temp_point > (int)$member['mb_point'])
											$temp_point = (int)$member['mb_point'];

										$point_unit = (int)$default['de_settle_point_unit'];
										$temp_point = (int)((int)($temp_point / $point_unit) * $point_unit);
								?>
								<input type="text" name="od_temp_point" value="0" id="od_temp_point" class="frm_input" size="10"> 원 <span style="color:#f1f1f3;">|</span> <input type="checkbox" name="point_all_use" id="point_all_use" value="y"> 모두 사용</br>
								· 사용가능 적립금 : <span style="color:#fb5626;"><?php echo display_point($temp_point); ?></span>원 사용가능,30일 이내 소멸예정 : <span style="color:#fb5626;"><?php echo display_point($temp_point); ?></span>원

								<?
									$multi_settle++;
									}
								}
								?>
							</td>
						</tr>
						<tr>
							<th scope="row">결제수단 선택</th>
							<td >
								<?

								if (!$default['de_card_point'])
									//echo '<p id="sod_frm_pt_alert"><strong>무통장입금</strong> 이외의 결제 수단으로 결제하시는 경우 포인트를 적립해드리지 않습니다.</p>';

								$multi_settle == 0;
								$checked = '';

								$escrow_title = "";
								if ($default['de_escrow_use']) {
									$escrow_title = "에스크로 ";
								}else{
									$escrow_title = "실시간 ";
								}

								// 신용카드 사용
								$is_cash_payment = true;	//2014-06-05오후 4:11 우선 무통장만 가능하게
								if ($default['de_card_use'] && $is_cash_payment) {
									$multi_settle++;
									echo '<input type="radio" id="od_settle_card" name="od_settle_case" value="신용카드" '.$checked.'> <label for="od_settle_card">신용카드</label>'.PHP_EOL;
									$checked = '';
								}

								// 휴대폰 사용
								if ($default['de_hp_use'] && $is_cash_payment) {
									$multi_settle++;
									echo '<input type="radio" id="od_settle_hp" name="od_settle_case" value="휴대폰" '.$checked.'> <label for="od_settle_hp">휴대폰 결제</label>'.PHP_EOL;
									$checked = '';
								}

								// 계좌이체 사용
								if ($default['de_iche_use'] && $is_cash_payment) {
									$multi_settle++;
									echo '<input type="radio" id="od_settle_iche" name="od_settle_case" value="계좌이체" '.$checked.'> <label for="od_settle_iche">'.$escrow_title.'계좌이체</label>'.PHP_EOL;
									$checked = '';
								}
								

								// 가상계좌 사용
								if ($default['de_vbank_use'] && $is_cash_payment) {
									$multi_settle++;
									echo '<input type="radio" id="od_settle_vbank" name="od_settle_case" value="가상계좌" '.$checked.'> <label for="od_settle_vbank">'.$escrow_title.'가상계좌</label>'.PHP_EOL;
									$checked = '';
								}


								// 무통장입금 사용
								if ($default['de_bank_use'] && $is_bank_payment) {
									$multi_settle++;
									//echo '<input type="radio" id="od_settle_bank" name="od_settle_case" value="무통장" '.$checked.'> <label for="od_settle_bank">무통장입금</label>'.PHP_EOL;
									echo '<input type="radio" id="od_settle_bank" name="od_settle_case" value="무통장" checked> <label for="od_settle_bank">무통장입금</label>'.PHP_EOL;
									$checked = '';
								}
								//if ($default['de_bank_use']) {
									// 은행계좌를 배열로 만든후
									$str = explode("\n", trim($default['de_bank_account']));

									/*
									if (count($str) <= 1)
									{
										//$bank_account = '<input type="hidden" name="od_bank_account" value="'.$str[0].'">'.$str[0].PHP_EOL;
									}
									else
									{
										$bank_account = '<select name="od_bank_account" id="od_bank_account">'.PHP_EOL;
										$bank_account .= '<option value="">선택하십시오.</option>';
										for ($i=0; $i<count($str); $i++)
										{
											//$str[$i] = str_replace("\r", "", $str[$i]);
											$str[$i] = trim($str[$i]);
											$bank_account .= '<option value="'.$str[$i].'">'.$str[$i].'</option>'.PHP_EOL;
										}
										$bank_account .= '</select>'.PHP_EOL;
									}
									*/
									//echo '<div id="settle_bank" style="display:none">';
									//echo '<label for="od_bank_account" class="sound_only">입금할 계좌</label>';
									//echo $bank_account;
									//echo '<br><label for="od_deposit_name">입금자명</label>';
									//echo '<input type="text" name="od_deposit_name" id="od_deposit_name" class="frm_input" size="10" maxlength="20">';
									//echo '</div>';
									
								//}

								if ($default['de_bank_use'] || $default['de_vbank_use'] || $default['de_bank_use'] || $default['de_bank_use'] || $default['de_bank_use']) {
								echo '</fieldset>';

								}

								if ($multi_settle == 0)
									echo '<p>결제할 방법이 없습니다.<br>운영자에게 알려주시면 감사하겠습니다.</p>';
								?>
							</td>
						</tr>
						

						<input type="hidden" name="RTR_InBank" value="미정">

						<tr class="mu_dis">
							<th>입금은행</th>
							<td>
								<select name="od_bank_account">
									<option value="">선택하십시오.</option>
									<?
									for ($i=0; $i<count($str); $i++)
									{
										//$str[$i] = str_replace("\r", "", $str[$i]);
										$str[$i] = trim($str[$i]);
										echo '<option value="'.$str[$i].'">'.$str[$i].'</option>'.PHP_EOL;
									}
									?>
								</select>
								<span style="color:#ef4e18;">입금계좌 정보</span>는 주문완료 페이지에서 확인 가능합니다.
							</td>
						</tr>
						
						<tr class="mu_dis">
							<th>입금기한</th>
							<td>
								<?=$od_now_date?>까지
								<input type="hidden" name="od_last_date" id="od_last_date" value="<?=$od_now_date?>">
							</td>
						</tr>
						<tr class="mu_dis">
							<th>현금영수증</th>
							<td>
								<p>
								<input type="radio" name="od_tax[]" value="0">소득공제용&nbsp;&nbsp;
								<input type="radio" name="od_tax[]" value="1">지출증빙용&nbsp;&nbsp;
								<input type="radio" name="od_tax[]" value="" checked>미발행&nbsp;&nbsp;
								</p>
								<p class="tax_st" style="margin:5px 0 0 0;display:none;">
								
								</p>
							</td>
						</tr>

						<tr>
							<th>환불은행</th>
							<td><input type="text" name="od_bank[0]" size="20"></td>
						</tr>

						<tr>
							<th>환불계좌</th>
							<td><input type="text" name="od_bank[1]" style="width:100%;"></td>
						</tr>

						<tr>
							<th>환불예금주</th>
							<td><input type="text" name="od_remit" size="20"></td>
						</tr>

						</tbody>
						</table>
					</td>
					<td style="padding:17px;border:0;background:#eeeff3;" valign="top">
						<table border="0" cellspacing="0" cellpadding="0" width="100%" class="buy_all_price_tb">
							<tr>
								<td colspan="2" style="font-size:17px;">결제 총 금액</td>
							</tr>
							<tr style="font-size:12px;">
								<td>총 주문금액</td>
								<td align="right"><span><?php echo number_format($tot_sell_price); ?></span><span> 원</span></td>
							</tr>
							<tr style="font-size:12px;">
								<td>적립금 사용</td>
								<td align="right"><span><?=number_format($tot_point)?></span><span> 원</span></td>
							</tr>
							<tr><td colspan="2"><div style="height:1px;background:#e1e1e2;"></div></td></tr>
							<tr style="font-size:15px;">
								<td>총 결제 금액</td>
								<td align="right" style="font-size:17px;color:#fc3f00;">
									<span class="total_all_price">
										<?php $tot_price = $tot_sell_price + $send_cost; // 총계 = 주문상품금액합계 + 배송비 ?>
										<?php echo number_format($tot_price); ?>
									</span>
									<span> 원</span>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>


		<?php
		/*if (!$default['de_card_point'])
			echo '<p id="sod_frm_pt_alert"><strong>무통장입금</strong> 이외의 결제 수단으로 결제하시는 경우 포인트를 적립해드리지 않습니다.</p>';

		$multi_settle == 0;
		$checked = '';

		$escrow_title = "";
		if ($default['de_escrow_use']) {
			$escrow_title = "에스크로 ";
		}else{
			$escrow_title = "실시간 ";
		}

		if ($default['de_bank_use'] || $default['de_vbank_use'] || $default['de_bank_use'] || $default['de_bank_use'] || $default['de_bank_use']) {
		echo '<fieldset id="sod_frm_paysel">';
		echo '<legend>결제방법 선택</legend>';
		}

		// 무통장입금 사용
		if ($default['de_bank_use'] && $is_bank_payment) {
			$multi_settle++;
			echo '<input type="radio" id="od_settle_bank" name="od_settle_case" value="무통장" '.$checked.'> <label for="od_settle_bank">무통장입금</label>'.PHP_EOL;
			$checked = '';
		}

		// 가상계좌 사용
		if ($default['de_vbank_use'] && $is_cash_payment) {
			$multi_settle++;
			echo '<input type="radio" id="od_settle_vbank" name="od_settle_case" value="가상계좌" '.$checked.'> <label for="od_settle_vbank">'.$escrow_title.'가상계좌</label>'.PHP_EOL;
			$checked = '';
		}

		// 계좌이체 사용
		if ($default['de_iche_use'] && $is_cash_payment) {
			$multi_settle++;
			echo '<input type="radio" id="od_settle_iche" name="od_settle_case" value="계좌이체" '.$checked.'> <label for="od_settle_iche">'.$escrow_title.'계좌이체</label>'.PHP_EOL;
			$checked = '';
		}

		// 휴대폰 사용
		if ($default['de_hp_use'] && $is_cash_payment) {
			$multi_settle++;
			echo '<input type="radio" id="od_settle_hp" name="od_settle_case" value="휴대폰" '.$checked.'> <label for="od_settle_hp">휴대폰</label>'.PHP_EOL;
			$checked = '';
		}

		// 신용카드 사용
		if ($default['de_card_use'] && $is_cash_payment) {
			$multi_settle++;
			echo '<input type="radio" id="od_settle_card" name="od_settle_case" value="신용카드" '.$checked.'> <label for="od_settle_card">신용카드</label>'.PHP_EOL;
			$checked = '';
		}

		$temp_point = 0;
		// 회원이면서 포인트사용이면
		if ($is_member && $config['cf_use_point'])
		{
			// 포인트 결제 사용 포인트보다 회원의 포인트가 크다면
			if ($member['mb_point'] >= $default['de_settle_min_point'])
			{
				$temp_point = (int)$default['de_settle_max_point'];

				if($temp_point > (int)$tot_sell_price)
					$temp_point = (int)$tot_sell_price;

				if($temp_point > (int)$member['mb_point'])
					$temp_point = (int)$member['mb_point'];

				$point_unit = (int)$default['de_settle_point_unit'];
				$temp_point = (int)((int)($temp_point / $point_unit) * $point_unit);
		?>
			<p id="sod_frm_pt">보유포인트(<?php echo display_point($member['mb_point']); ?>)중 <strong id="use_max_point">최대 <?php echo display_point($temp_point); ?></strong>까지 사용 가능</p>
			<input type="hidden" name="max_temp_point" value="<?php echo $temp_point; ?>">
			<label for="od_temp_point">사용 포인트</label>
			<input type="text" name="od_temp_point" value="0" id="od_temp_point" class="frm_input" size="10">점 (<?php echo $point_unit; ?>점 단위로 입력하세요.)
		<?php
			$multi_settle++;
			}
		}

		if ($default['de_bank_use']) {
			// 은행계좌를 배열로 만든후
			$str = explode("\n", trim($default['de_bank_account']));
			if (count($str) <= 1)
			{
				$bank_account = '<input type="hidden" name="od_bank_account" value="'.$str[0].'">'.$str[0].PHP_EOL;
			}
			else
			{
				$bank_account = '<select name="od_bank_account" id="od_bank_account">'.PHP_EOL;
				$bank_account .= '<option value="">선택하십시오.</option>';
				for ($i=0; $i<count($str); $i++)
				{
					//$str[$i] = str_replace("\r", "", $str[$i]);
					$str[$i] = trim($str[$i]);
					$bank_account .= '<option value="'.$str[$i].'">'.$str[$i].'</option>'.PHP_EOL;
				}
				$bank_account .= '</select>'.PHP_EOL;
			}
			echo '<div id="settle_bank" style="display:none">';
			echo '<label for="od_bank_account" class="sound_only">입금할 계좌</label>';
			echo $bank_account;
			echo '<br><label for="od_deposit_name">입금자명</label>';
			echo '<input type="text" name="od_deposit_name" id="od_deposit_name" class="frm_input" size="10" maxlength="20">';
			echo '</div>';
		}

		if ($default['de_bank_use'] || $default['de_vbank_use'] || $default['de_bank_use'] || $default['de_bank_use'] || $default['de_bank_use']) {
		echo '</fieldset>';

		}

		if ($multi_settle == 0)
			echo '<p>결제할 방법이 없습니다.<br>운영자에게 알려주시면 감사하겠습니다.</p>';*/
		?>
	</section>
	<!-- } 결제 정보 입력 끝 -->


	<!-- 동의 시작 -->
	<!--
	<div class="agree">

		<h2>개인정보 제3자 제공 및 주의사항 동의</h2>
		<table border="0" cellspacing="0" cellpadding="0" width="800px" class="agree1_tb">
			<tr>
				<td class="tab_on agree1_bn" idx="0">개인정보 제3자 제공</td>
				<td class="agree1_bn" idx="1">주의사항</td>
				<td>&nbsp;</td>
				<td class="right">&nbsp;</td>
			</tr>
			<tr class="agree1_dis" style="display:table-row;">
				<td style="padding:0;border:0;" colspan="4">
					<textarea readOnly><?=$config[cf_pay_aree1];?></textarea>
				</td>
			</tr>
			<tr class="agree1_dis">
				<td style="padding:0;border:0;" colspan="4">
					<textarea readOnly><?=$config[cf_pay_aree2];?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="4" style="font-weight:bold;text-align:left;border:0;">
					<input type="checkbox" name="agr_1[]" id="agr_1" value="y"> 개인정보 제3자 제공에 동의&nbsp;&nbsp;&nbsp;
					<input type="checkbox" name="agr_1[]" id="agr_1" value="y"> 주의사항에 동의
				</td>
			</tr>
		</table>

	</div>
	-->

	<div class="agree">

		<h2>결제대행서비스 표준이용약관</h2>
		<table border="0" cellspacing="0" cellpadding="0" width="800px" class="agree1_tb">
			<tr>
				<td class="tab_on agree2_bn" idx="0">기본약관</td>
				<td class="agree2_bn" idx="1">개인정보 수집, 이용</td>
				<td class="agree2_bn" idx="2">개인정보 제공, 위탁</td>
				<td class="right">&nbsp;</td>
			</tr>
			<tr class="agree2_dis" style="display:table-row;">
				<td style="padding:0;border:0;" colspan="4">
					<textarea readOnly><?=$config[cf_pay_aree3];?></textarea>
				</td>
			</tr>
			<tr class="agree2_dis">
				<td style="padding:0;border:0;" colspan="4">
					<textarea readOnly><?=$config[cf_pay_aree4];?></textarea>
				</td>
			</tr>
			<tr class="agree2_dis">
				<td style="padding:0;border:0;" colspan="4">
					<textarea readOnly><?=$config[cf_pay_aree5];?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="4" style="font-weight:bold;text-align:left;border:0;">
					<input type="checkbox" name="agr_2[]" id="agr_2" value="y"> 본인은 위의 내용을 모두 읽어보았으며 이에 전체 동의합니다.
				</td>
			</tr>
		</table>

	</div>

	<!-- 동의 끝 -->



	<?php// if ($goods_count) $goods .= ' 외 '.$goods_count.'건'; ?>
	<!-- } 주문상품 확인 끝 -->

	<!-- 주문상품 합계 시작 { -->
	<!--<dl id="sod_bsk_tot">
		<dt class="sod_bsk_sell">주문</dt>
		<dd class="sod_bsk_sell"><strong><?php// echo number_format($tot_sell_price); ?> 원</strong></dd>
		<?php// if($it_cp_count > 0) { ?>
		<dt class="sod_bsk_coupon">쿠폰할인</dt>
		<dd class="sod_bsk_coupon"><strong id="ct_tot_coupon">0 원</strong></dd>
		<?php// } ?>
		<dt class="sod_bsk_dvr">배송비</dt>
		<dd class="sod_bsk_dvr"><strong><?php// echo number_format($send_cost); ?> 원</strong></dd>
		<dt class="sod_bsk_cnt">총계</dt>
		<dd class="sod_bsk_cnt">
			<?php// $tot_price = $tot_sell_price + $send_cost; // 총계 = 주문상품금액합계 + 배송비 ?>
			<strong id="ct_tot_price"><?php// echo number_format($tot_price); ?> 원</strong>
		</dd>
		<dt class="sod_bsk_point">포인트</dt>
		<dd class="sod_bsk_point"><strong><?php// echo number_format($tot_point); ?> 점</strong></dd>
	</dl>-->
	<!-- } 주문상품 합계 끝 -->
		
	<?php
    // 결제대행사별 코드 include (주문버튼)
    require_once('./'.$default['de_pg_service'].'/orderform.3.php');
    ?>
	</form>

	<?php
    if ($default['de_escrow_use']) {
        // 결제대행사별 코드 include (에스크로 안내)
        require_once('./'.$default['de_pg_service'].'/orderform.4.php');
    }
    ?>

</div>

<script>
$(function() {
    var $cp_btn_el;
    var $cp_row_el;
    var zipcode = "";

	$(".agree1_bn").click(function(){
		var idx = $(this).attr("idx");

		$(".agree1_dis").each(function(i){
			if(idx == i){
				$(".agree1_bn").eq(i).addClass("tab_on");
				$(".agree1_dis").eq(i).css("display", "table-row");
			}else{
				$(".agree1_bn").eq(i).removeClass("tab_on");
				$(".agree1_dis").eq(i).css("display", "none");
			}
		});
	});

	$(".agree2_bn").click(function(){
		var idx = $(this).attr("idx");

		$(".agree2_dis").each(function(i){
			if(idx == i){
				$(".agree2_bn").eq(i).addClass("tab_on");
				$(".agree2_dis").eq(i).css("display", "table-row");
			}else{
				$(".agree2_bn").eq(i).removeClass("tab_on");
				$(".agree2_dis").eq(i).css("display", "none");
			}
		});
	});

	$("input[name='od_settle_case']").click(function(){
		if($(this).val() == "신용카드"){
			$(".card_dis").css("display", "table-row");
		}else{
			$(".card_dis").css("display", "none");
		}
	});

    $(".cp_btn").click(function() {
        $cp_btn_el = $(this);
        $cp_row_el = $(this).closest("tr");
        $("#cp_frm").remove();
        var it_id = $cp_btn_el.closest("tr").find("input[name^=it_id]").val();

        $.post(
            "./orderitemcoupon.php",
            { it_id: it_id,  sw_direct: "<?php echo $sw_direct; ?>" },
            function(data) {
                $cp_btn_el.after(data);
            }
        );
    });

    $(".cp_apply").live("click", function() {
        var $el = $(this).closest("tr");
        var cp_id = $el.find("input[name='f_cp_id[]']").val();
        var price = $el.find("input[name='f_cp_prc[]']").val();
        var subj = $el.find("input[name='f_cp_subj[]']").val();
        var sell_price;

        if(parseInt(price) == 0) {
            if(!confirm(subj+"쿠폰의 할인 금액은 "+price+"원입니다.\n쿠폰을 적용하시겠습니까?")) {
                return false;
            }
        }

        // 이미 사용한 쿠폰이 있는지
        var cp_dup = false;
        var cp_dup_idx;
        var $cp_dup_el;
        $("input[name^=cp_id]").each(function(index) {
            var id = $(this).val();

            if(id == cp_id) {
                cp_dup_idx = index;
                cp_dup = true;
                $cp_dup_el = $(this).closest("tr");;

                return false;
            }
        });

        if(cp_dup) {
            var it_name = $("input[name='it_name["+cp_dup_idx+"]']").val();
            if(!confirm(subj+ "쿠폰은 "+it_name+"에 사용되었습니다.\n"+it_name+"의 쿠폰을 취소한 후 적용하시겠습니까?")) {
                return false;
            } else {
                coupon_cancel($cp_dup_el);
                $("#cp_frm").remove();
                $cp_dup_el.find(".cp_btn").text("적용").focus();
                $cp_dup_el.find(".cp_cancel").remove();
            }
        }

        var $s_el = $cp_row_el.find(".total_price");;
        sell_price = parseInt($cp_row_el.find("input[name^=it_price]").val());
        sell_price = sell_price - parseInt(price);
        if(sell_price < 0) {
            alert("쿠폰할인금액이 상품 주문금액보다 크므로 쿠폰을 적용할 수 없습니다.");
            return false;
        }
        $s_el.text(number_format(String(sell_price)));
        $cp_row_el.find("input[name^=cp_id]").val(cp_id);
        $cp_row_el.find("input[name^=cp_price]").val(price);

        calculate_total_price();
        $("#cp_frm").remove();
        $cp_btn_el.text("변경").focus();
        if(!$cp_row_el.find(".cp_cancel").size())
            $cp_btn_el.after("<button type=\"button\" class=\"cp_cancel btn_frmline\">취소</button>");
    });

    $("#cp_close").live("click", function() {
        $("#cp_frm").remove();
        $cp_btn_el.focus();
    });

    $(".cp_cancel").live("click", function() {
        coupon_cancel($(this).closest("tr"));
        calculate_total_price();
        $("#cp_frm").remove();
        $(this).closest("tr").find(".cp_btn").text("적용").focus();
        $(this).remove();
    });

    $("#od_coupon_btn").click(function() {
        $("#od_coupon_frm").remove();
        var $this = $(this);
        var price = parseInt($("input[name=org_od_price]").val()) - parseInt($("input[name=item_coupon]").val());
        if(price <= 0) {
            alert('상품금액이 0원이므로 쿠폰을 사용할 수 없습니다.');
            return false;
        }
        $.post(
            "./ordercoupon.php",
            { price: price },
            function(data) {
                $this.after(data);
            }
        );
    });

    $(".od_cp_apply").live("click", function() {
        var $el = $(this).closest("tr");
        var cp_id = $el.find("input[name='o_cp_id[]']").val();
        var price = parseInt($el.find("input[name='o_cp_prc[]']").val());
        var subj = $el.find("input[name='o_cp_subj[]']").val();
        var send_cost = $("input[name=od_send_cost]").val();
        var item_coupon = parseInt($("input[name=item_coupon]").val());
        var od_price = parseInt($("input[name=org_od_price]").val()) - item_coupon;

        if(price == 0) {
            if(!confirm(subj+"쿠폰의 할인 금액은 "+price+"원입니다.\n쿠폰을 적용하시겠습니까?")) {
                return false;
            }
        }

        if(od_price - price <= 0) {
            alert("쿠폰할인금액이 주문금액보다 크므로 쿠폰을 적용할 수 없습니다.");
            return false;
        }

        $("input[name=sc_cp_id]").val("");
        $("#sc_coupon_btn").text("쿠폰적용");
        $("#sc_coupon_cancel").remove();

        $("input[name=od_price]").val(od_price - price);
        $("input[name=od_cp_id]").val(cp_id);
        $("input[name=od_coupon]").val(price);
        $("input[name=od_send_coupon]").val(0);
        $("#od_cp_price").text(number_format(String(price)));
        $("#sc_cp_price").text(0);
        calculate_order_price();
        $("#od_coupon_frm").remove();
        $("#od_coupon_btn").text("쿠폰변경").focus();
        if(!$("#od_coupon_cancel").size())
            $("#od_coupon_btn").after("<button type=\"button\" id=\"od_coupon_cancel\" class=\"btn_frmline\">쿠폰취소</button>");
    });

    $("#od_coupon_close").live("click", function() {
        $("#od_coupon_frm").remove();
        $("#od_coupon_btn").focus();
    });

    $("#od_coupon_cancel").live("click", function() {
        var org_price = $("input[name=org_od_price]").val();
        var item_coupon = parseInt($("input[name=item_coupon]").val());
        $("input[name=od_price]").val(org_price - item_coupon);
        $("input[name=sc_cp_id]").val("");
        $("input[name=od_coupon]").val(0);
        $("input[name=od_send_coupon]").val(0);
        $("#od_cp_price").text(0);
        $("#sc_cp_price").text(0);
        calculate_order_price();
        $("#od_coupon_frm").remove();
        $("#od_coupon_btn").text("쿠폰적용").focus();
        $(this).remove();
        $("#sc_coupon_btn").text("쿠폰적용");
        $("#sc_coupon_cancel").remove();
    });

    $("#sc_coupon_btn").click(function() {
        $("#sc_coupon_frm").remove();
        var $this = $(this);
        var price = parseInt($("input[name=od_price]").val());
        var send_cost = parseInt($("input[name=od_send_cost]").val());
        $.post(
            "./ordersendcostcoupon.php",
            { price: price, send_cost: send_cost },
            function(data) {
                $this.after(data);
            }
        );
    });

    $(".sc_cp_apply").live("click", function() {
        var $el = $(this).closest("tr");
        var cp_id = $el.find("input[name='s_cp_id[]']").val();
        var price = parseInt($el.find("input[name='s_cp_prc[]']").val());
        var subj = $el.find("input[name='s_cp_subj[]']").val();
        var send_cost = parseInt($("input[name=od_send_cost]").val());

        if(parseInt(price) == 0) {
            if(!confirm(subj+"쿠폰의 할인 금액은 "+price+"원입니다.\n쿠폰을 적용하시겠습니까?")) {
                return false;
            }
        }

        $("input[name=sc_cp_id]").val(cp_id);
        $("input[name=od_send_coupon]").val(price);
        $("#sc_cp_price").text(number_format(String(price)));
        calculate_order_price();
        $("#sc_coupon_frm").remove();
        $("#sc_coupon_btn").text("쿠폰변경").focus();
        if(!$("#sc_coupon_cancel").size())
            $("#sc_coupon_btn").after("<button type=\"button\" id=\"sc_coupon_cancel\" class=\"btn_frmline\">쿠폰취소</button>");
    });

    $("#sc_coupon_close").live("click", function() {
        $("#sc_coupon_frm").remove();
        $("#sc_coupon_btn").focus();
    });

    $("#sc_coupon_cancel").live("click", function() {
        $("input[name=od_send_coupon]").val(0);
        $("#sc_cp_price").text(0);
        calculate_order_price();
        $("#sc_coupon_frm").remove();
        $("#sc_coupon_btn").text("쿠폰적용").focus();
        $(this).remove();
    });

    $("#od_b_addr2").focus(function() {
        var zip1 = $("#od_b_zip1").val().replace(/[^0-9]/g, "");
        var zip2 = $("#od_b_zip2").val().replace(/[^0-9]/g, "");
        if(zip1 == "" || zip2 == "")
            return false;

        var code = String(zip1) + String(zip2);

        if(zipcode == code)
            return false;

        zipcode = code;
        calculate_sendcost(code);
    });

    $("#od_settle_bank").on("click", function() {
        $("[name=od_deposit_name]").val( $("[name=od_name]").val() );
        $(".mu_dis").show();
    });

    $("#od_settle_iche,#od_settle_card,#od_settle_vbank,#od_settle_hp").bind("click", function() {
        $(".mu_dis").hide();
    });

    // 배송지선택
    $("input[name=ad_sel_addr]").on("click", function() {
        var addr = $(this).val().split(String.fromCharCode(30));

        if (addr[0] == "same") {
            if($(this).is(":checked"))
                gumae2baesong(true);
            else
                gumae2baesong(false);
        } else {
            if(addr[0] == "new") {
                for(i=0; i<10; i++) {
                    addr[i] = "";
                }
            }

            var f = document.forderform;
            f.od_b_name.value        = addr[0];
            f.od_b_tel.value         = addr[1];
            f.od_b_hp.value          = addr[2];
            f.od_b_zip1.value        = addr[3];
            f.od_b_zip2.value        = addr[4];
            f.od_b_addr1.value       = addr[5];
            f.od_b_addr2.value       = addr[6];
            f.od_b_addr3.value       = addr[7];
            f.od_b_addr_jibeon.value = addr[8];
            f.ad_subject.value       = addr[9];

            document.getElementById("od_b_addr_jibeon").innerText = "지번주소 : "+addr[8];

            var zip1 = addr[3].replace(/[^0-9]/g, "");
            var zip2 = addr[4].replace(/[^0-9]/g, "");

            if(zip1 != "" && zip2 != "") {
                var code = String(zip1) + String(zip2);

                if(zipcode != code) {
                    zipcode = code;
                    calculate_sendcost(code);
                }
            }
        }
    });

    // 배송지목록
    $("#order_address").on("click", function() {
        var url = this.href;
        window.open(url, "win_address", "left=100,top=100,width=800,height=600,scrollbars=1");
        return false;
    });


	var close_btn_idx;
	var idx, status, old_cnt, cnt, pro_price, price1_1, price1, send_cost, cart_id, price3, cost, cost1, cost2, all_price;

	<?if($send_cost){?>
		send_cost = <?=$send_cost?>;
	<?}else{?>
		send_cost = 0;
	<?}?>

	$(".combine_deli_bn").click(function(){
		var deli_price = $("input[name='od_send_cost']").val();
		var chk = false;

		$("input[name^='com_chk']").each(function(i){
			if($("input[name^='com_chk']").eq(i).is(":checked") == true){
				chk = true;
			}
		});

		if(chk == false){
			alert("통합배송 주문건을 1건 이상 선택하세요.");
			return false;
		}

		if(confirm("통합배송을 신청하시겠습니까?")){
			$(".price_info1").find(".price").eq(2).html('0');
			price3 = removeComma($(".price_info1").find(".price").eq(0).html()) - $(".price_info1").find(".price").eq(2).html();
			$(".price_info1").find(".price").eq(3).html(commaNum(price3));

			$(".price_info2").find(".price").eq(2).html('0');
			price3 = removeComma($(".price_info2").find(".price").eq(0).html()) - $(".price_info2").find(".price").eq(2).html();
			$(".price_info2").find(".price").eq(3).html(commaNum(price3));

			$("input[name='od_send_cost']").val("");
			$(".total_all_price").html(commaNum(removeComma($(".total_all_price").html()) - deli_price));

			$("input[name='combine_deli_status']").val("y");
		}
	});

    // 선택사항수정
    $(".mod_options").click(function() {
        var it_id = $(this).closest("tr").find("input[name^=it_id]").val();
        var $this = $(this);
        close_btn_idx = $(".mod_options").index($(this));

        $.post(
            "./cartoption.php",
            { it_id: it_id },
            function(data) {
                $("#mod_option_frm").remove();
                $this.after("<div id=\"mod_option_frm\"></div>");
                $("#mod_option_frm").html(data);
                price_calculate();
            }
        );
    });

    // 모두선택
    $("input[name=ct_all]").click(function() {
		var idx = $(this).attr("idx");
		var cost = 0;
		var k = 0;
		var status = $(this).attr("status");
		var price1_1 = 0;
		var price1 = 0;

        if($(this).is(":checked")){

			$(".product" + idx).find("input[name^=ct_chk]").each(function(i){
				if($(".product" + idx).find("input[name^=ct_chk]").eq(i).attr("disabled") != "disabled"){
					$(".product" + idx).find("input[name^=ct_chk]").eq(i).attr("checked", true);
					cost = $(".product" + idx).find("input[name^=ct_chk]").eq(i).attr("ct_send_cost");
					k++;
				}
			});

            //$(".product" + idx).find("input[name^=ct_chk]").attr("checked", true);

			if(status == 1){
				$(".product1").find(".sell_price").each(function(i){
					price1_1 = removeComma($(".product1").find(".sell_price").eq(i).html());
					price1 = parseInt(price1_1) + parseInt(price1);
				});

				if(k > 1){
					$(".price_info" + status).find(".price").eq(2).html(commaNum(3500));
				}else{
					$(".price_info" + status).find(".price").eq(2).html(commaNum(cost));
				}

				$(".price_info1").find(".price").eq(0).html(commaNum(price1));
				$(".all_price_info").find(".price_all").eq(0).html(commaNum(price1));
				price3 = removeComma($(".price_info1").find(".price").eq(2).html());
				price3 = parseInt(price3) + price1;

				if(price3){
					price3 = price3;
				}else{
					price3 = 0;
				}
				
				$(".price_info1").find(".price").eq(2).html($(".price_info" + status).find(".price").eq(2).html());

				$(".price_info1").find(".price").eq(3).html(commaNum(price3));
			}else{
				$(".product2").find(".sell_price").each(function(i){
					price1_1 = removeComma($(".product2").find(".sell_price").eq(i).html());
					price1 = parseInt(price1_1) + parseInt(price1);
				});

				if(k > 1){
					$(".price_info" + status).find(".price").eq(2).html(commaNum(3500));
				}else{
					$(".price_info" + status).find(".price").eq(2).html(commaNum(cost));
				}

				$(".price_info2").find(".price").eq(0).html(commaNum(price1));
				$(".all_price_info").find(".price_all").eq(0).html(commaNum(price1));
				price3 = removeComma($(".price_info2").find(".price").eq(2).html());
				price3 = parseInt(price3) + price1;

				if(price3){
					price3 = price3;
				}else{
					price3 = 0;
				}
				
				$(".price_info2").find(".price").eq(2).html($(".price_info" + status).find(".price").eq(2).html());

				$(".price_info2").find(".price").eq(3).html(commaNum(price3));
			}

        }else{
            $(".product" + idx).find("input[name^=ct_chk]").attr("checked", false);

			if(status == 1){
				$(".price_info1").find(".price").eq(0).html(0);
				$(".price_info1").find(".price").eq(1).html(0);
				$(".price_info1").find(".price").eq(2).html(0);
				$(".price_info1").find(".price").eq(3).html(0);
			}else{
				$(".price_info2").find(".price").eq(0).html(0);
				$(".price_info2").find(".price").eq(1).html(0);
				$(".price_info2").find(".price").eq(2).html(0);
				$(".price_info2").find(".price").eq(3).html(0);
			}
		}
    });

	// 체크박스 클릭시
	$("input:checkbox[name^='ct_chk']").click(function(){
		var sell_price = removeComma($(this).parent().parent().find(".sell_price").html());
		var price = 0;
		var status = $(this).attr("status");
		var cost = removeComma($(".price_info" + status).find(".price").eq(2).html());
		var num = 0;
		var num1 = 0;
		var k = 0;

		
		if(status == 1){
			price = removeComma($(".price_info1").find(".price").eq(0).html());
			//cost = removeComma($(".price_info1").find(".price").eq(2).html());
		}else{
			price = removeComma($(".price_info2").find(".price").eq(0).html());
			//cost = removeComma($(".price_info2").find(".price").eq(2).html());
		}
		

		$(".product" + status).find("input[name^=ct_chk]").each(function(i){
			if($(".product" + status).find("input[name^=ct_chk]").eq(i).is(":checked") == true){
				cost = $(".product" + status).find("input[name^=ct_chk]").eq(i).attr("ct_send_cost");
				k++;
			}
		});
		

		if($(this).is(":checked") == true){

			if(k > 1){
				$(".price_info1").find(".price").eq(2).html(commaNum(3500));
			}else if(k == 1){
				$(".price_info1").find(".price").eq(2).html(commaNum(cost));
			}else{
				$(".price_info1").find(".price").eq(2).html(commaNum(0));
			}

			num = parseInt(price) + parseInt(sell_price);
			num1 = parseInt(price) + parseInt(sell_price) + parseInt(cost);

			if(status == 1){
				$(".price_info1").find(".price").eq(0).html(commaNum(num));
				$(".price_info1").find(".price").eq(3).html(commaNum(num1));
			}else{
				$(".price_info2").find(".price").eq(0).html(commaNum(num));
				$(".price_info2").find(".price").eq(3).html(commaNum(num1));
			}
		}else{

			if(status == 1){

				if(k > 1){
					$(".price_info" + status).find(".price").eq(2).html(commaNum(3500));
				}else if(k == 1){
					$(".price_info" + status).find(".price").eq(2).html(commaNum(cost));
				}else{
					$(".price_info" + status).find(".price").eq(2).html(commaNum(0));
				}
				
				num = parseInt(price) - parseInt(sell_price);
				num1 = parseInt(price) - parseInt(sell_price) + parseInt($(".price_info" + status).find(".price").eq(2).html());

				$(".price_info1").find(".price").eq(0).html(commaNum(num));
				$(".price_info1").find(".price").eq(3).html(commaNum(num1));
			}else{

				if(k > 1){
					$(".price_info" + status).find(".price").eq(2).html(commaNum(3500));
				}else if(k == 1){
					$(".price_info" + status).find(".price").eq(2).html(commaNum(cost));
				}else{
					$(".price_info" + status).find(".price").eq(2).html(commaNum(0));
				}

				num = parseInt(price) - parseInt(sell_price);
				num1 = parseInt(price) - parseInt(sell_price) + parseInt($(".price_info" + status).find(".price").eq(2).html());

				$(".price_info2").find(".price").eq(0).html(commaNum(num));
				$(".price_info2").find(".price").eq(3).html(commaNum(num1));
			}
		}
	});

    // 옵션수정 닫기
    $("#mod_option_close").live("click", function() {
        $("#mod_option_frm").remove();
        $(".mod_options").eq(close_btn_idx).focus();
    });
    $("#win_mask").click(function () {
        $("#mod_option_frm").remove();
        $(".mod_options").eq(close_btn_idx).focus();
    });

	//상품 전체선택
	$(".pro_all_chk").click(function(){
		var idx = $(this).attr("idx");
		var n = idx - 1;
		$(".product" + idx).find("input[name^=ct_chk]").attr("checked", true);
		$("input[name=ct_all]").eq(n).attr("checked", true);
	});

	//상품 선택해제
	$(".pro_all_rel").click(function(){
		var idx = $(this).attr("idx");
		var n = idx - 1;
		$(".product" + idx).find("input[name^=ct_chk]").attr("checked", false);
		$("input[name=ct_all]").eq(n).attr("checked", false);
	});

	//수량
	price1_1 = 0;
	price1 = 0;
	price3 = 0;
	
	$(".product1").find(".sell_price").each(function(i){
		price1_1 = removeComma($(".product1").find(".sell_price").eq(i).html());
		price1 = parseInt(price1_1) + parseInt(price1);
	});

	$(".price_info1").find(".price").eq(0).html(commaNum(price1));
	$(".all_price_info").find(".price_all").eq(0).html(commaNum(price1));

	price3 = removeComma($(".price_info1").find(".price").eq(2).html());
	price3 = parseInt(price3) + price1;

	if(price3){
		price3 = price3;
	}else{
		price3 = 0;
	}
	
	$(".price_info1").find(".price").eq(3).html(commaNum(price3));
	
	price1_1 = 0;
	price1 = 0;
	price3 = 0;

	$(".product2").find(".sell_price").each(function(i){
		price1_1 = removeComma($(".product2").find(".sell_price").eq(i).html());
		price1 = parseInt(price1_1) + parseInt(price1);
	});

	$(".price_info2").find(".price").eq(0).html(commaNum(price1));
	$(".all_price_info").find(".price_all_auc").eq(0).html(commaNum(price1));

	price3 = removeComma($(".price_info2").find(".price").eq(2).html());
	price3 = parseInt(price3) + price1;

	if(price3){
		price3 = price3;
	}else{
		price3 = 0;
	}
	
	$(".price_info2").find(".price").eq(3).html(commaNum(price3));

});

function cart_cnt(th, idx, old_cnt, cnt, pro_price, price1_1, price1, cart_id, status){

	all_price = 0;

	$(th).parent().parent().parent().find(".sell_price" + idx).html(commaNum(pro_price));
	$(th).parent().parent().find(".ct_cnt").find("input:text[name='ct_qty["+idx+"]']").val(cnt);

	if(status == 1){
		$(".product1").find(".sell_price").each(function(i){
			price1_1 = removeComma($(".product1").find(".sell_price").eq(i).html());
			price1 = parseInt(price1_1) + parseInt(price1);
		});

		$(".price_info1").find(".price").eq(0).html(commaNum(price1));
		$(".all_price_info").find(".price_all").eq(0).html(commaNum(price1));

		price3 = removeComma($(".price_info1").find(".price").eq(2).html());
		price3 = parseInt(price3) + price1;
		
		$(".price_info1").find(".price").eq(3).html(commaNum(price3));
	}else{
		$(".product2").find(".sell_price").each(function(i){
			price1_1 = removeComma($(".product2").find(".sell_price").eq(i).html());
			price1 = parseInt(price1_1) + parseInt(price1);
		});

		$(".price_info2").find(".price").eq(0).html(commaNum(price1));
		$(".all_price_info").find(".price_all_auc").eq(0).html(commaNum(price1));

		price3 = removeComma($(".price_info1").find(".price").eq(2).html());
		price3 = parseInt(price3) + price1;
		
		$(".price_info2").find(".price").eq(3).html(commaNum(price3));
	}

	$.ajax({
		type : "POST",
		dataType : "HTML",
		url : "./_Ajax.cart.php",
		data : "idx=" + idx + "&cnt=" + cnt + "&cart_id=" + cart_id + "&status=" + status,
		success : function(data){
			$(".price_info" + status).find(".price").eq(2).html(commaNum(data));

			cost1 = removeComma($(".price_info1").find(".price").eq(2).html());
			cost2 = removeComma($(".price_info2").find(".price").eq(2).html());
			cost = parseInt(cost1) + parseInt(cost2);
			
			$(".all_price_info").find(".price_all_cost").html(commaNum(cost));

			all_price = parseInt(removeComma($(".price_info1").find(".price").eq(3).html())) + parseInt(removeComma($(".price_info2").find(".price").eq(3).html()));
			$(".all_price_info").find(".price_all_price").html(commaNum(all_price));
		}
	});
}

function coupon_cancel($el)
{
    var $dup_sell_el = $el.find(".total_price");
    var $dup_price_el = $el.find("input[name^=cp_price]");
    var org_sell_price = $el.find("input[name^=it_price]").val();

    $dup_sell_el.text(number_format(String(org_sell_price)));
    $dup_price_el.val(0);
    $el.find("input[name^=cp_id]").val("");
}

function calculate_total_price()
{
    var $it_prc = $("input[name^=it_price]");
    var $cp_prc = $("input[name^=cp_price]");
    var tot_sell_price = sell_price = tot_cp_price = 0;
    var it_price, cp_price, it_notax;
    var tot_mny = comm_tax_mny = comm_vat_mny = comm_free_mny = tax_mny = vat_mny = 0;
    var send_cost = parseInt($("input[name=od_send_cost]").val());

    $it_prc.each(function(index) {
        it_price = parseInt($(this).val());
        cp_price = parseInt($cp_prc.eq(index).val());
        sell_price += it_price;
        tot_cp_price += cp_price;
    });

    tot_sell_price = sell_price - tot_cp_price + send_cost;

    $("#ct_tot_coupon").text(number_format(String(tot_cp_price))+" 원");
    $("#ct_tot_price").text(number_format(String(tot_sell_price))+" 원");

    $("input[name=good_mny]").val(tot_sell_price);
    $("input[name=od_price]").val(sell_price - tot_cp_price);
    $("input[name=item_coupon]").val(tot_cp_price);
    $("input[name=od_coupon]").val(0);
    $("input[name=od_send_coupon]").val(0);
    <?php if($oc_cnt > 0) { ?>
    $("input[name=od_cp_id]").val("");
    $("#od_cp_price").text(0);
    if($("#od_coupon_cancel").size()) {
        $("#od_coupon_btn").text("쿠폰적용");
        $("#od_coupon_cancel").remove();
    }
    <?php } ?>
    <?php if($sc_cnt > 0) { ?>
    $("input[name=sc_cp_id]").val("");
    $("#sc_cp_price").text(0);
    if($("#sc_coupon_cancel").size()) {
        $("#sc_coupon_btn").text("쿠폰적용");
        $("#sc_coupon_cancel").remove();
    }
    <?php } ?>
    $("input[name=od_temp_point]").val(0);
    <?php if($temp_point > 0 && $is_member) { ?>
    calculate_temp_point();
    <?php } ?>
    calculate_order_price();
}

function calculate_order_price()
{
    var sell_price = parseInt($("input[name=od_price]").val());
    var send_cost = parseInt($("input[name=od_send_cost]").val());
    var send_cost2 = parseInt($("input[name=od_send_cost2]").val());
    var send_coupon = parseInt($("input[name=od_send_coupon]").val());
    var tot_price = sell_price + send_cost + send_cost2 - send_coupon;

    $("input[name=good_mny]").val(tot_price);
    $("#od_tot_price").text(number_format(String(tot_price)));
    <?php if($temp_point > 0 && $is_member) { ?>
    calculate_temp_point();
    <?php } ?>
}

function calculate_temp_point()
{
    var sell_price = parseInt($("input[name=od_price]").val());
    var mb_point = parseInt(<?php echo $member['mb_point']; ?>);
    var max_point = parseInt(<?php echo $default['de_settle_max_point']; ?>);
    var point_unit = parseInt(<?php echo $default['de_settle_point_unit']; ?>);
    var temp_point = max_point;

    if(temp_point > sell_price)
        temp_point = sell_price;

    if(temp_point > mb_point)
        temp_point = mb_point;

    temp_point = parseInt(temp_point / point_unit) * point_unit;

    $("#use_max_point").text("최대 "+number_format(String(temp_point))+"점");
    $("input[name=max_temp_point]").val(temp_point);
}

function calculate_sendcost(code)
{
    $.post(
        "./ordersendcost.php",
        { zipcode: code },
        function(data) {
            $("input[name=od_send_cost2]").val(data);
            $("#od_send_cost2").text(number_format(String(data)));

            calculate_order_price();
        }
    );
}

function calculate_tax()
{
    var $it_prc = $("input[name^=it_price]");
    var $cp_prc = $("input[name^=cp_price]");
    var sell_price = tot_cp_price = 0;
    var it_price, cp_price, it_notax;
    var tot_mny = comm_free_mny = tax_mny = vat_mny = 0;
    var send_cost = parseInt($("input[name=od_send_cost]").val());
    var send_cost2 = parseInt($("input[name=od_send_cost2]").val());
    var od_coupon = parseInt($("input[name=od_coupon]").val());
    var send_coupon = parseInt($("input[name=od_send_coupon]").val());
    var temp_point = 0;

    $it_prc.each(function(index) {
        it_price = parseInt($(this).val());
        cp_price = parseInt($cp_prc.eq(index).val());
        sell_price += it_price;
        tot_cp_price += cp_price;
        it_notax = $("input[name^=it_notax]").eq(index).val();
        if(it_notax == "1") {
            comm_free_mny += (it_price - cp_price);
        } else {
            tot_mny += (it_price - cp_price);
        }
    });

    if($("input[name=od_temp_point]").size())
        temp_point = parseInt($("input[name=od_temp_point]").val());

    tot_mny += (send_cost + send_cost2 - od_coupon - send_coupon - temp_point);
    if(tot_mny < 0) {
        comm_free_mny = comm_free_mny + tot_mny;
        tot_mny = 0;
    }

    tax_mny = Math.round(tot_mny / 1.1);
    vat_mny = tot_mny - tax_mny;
    $("input[name=comm_tax_mny]").val(tax_mny);
    $("input[name=comm_vat_mny]").val(vat_mny);
    $("input[name=comm_free_mny]").val(comm_free_mny);
}

function forderform_check(f)
{
    errmsg = "";
    errfld = "";
    var deffld = "";

    /*
	check_field(f.od_name, "주문하시는 분 이름을 입력하십시오.");
    if (typeof(f.od_pwd) != 'undefined')
    {
        clear_field(f.od_pwd);
        if( (f.od_pwd.value.length<3) || (f.od_pwd.value.search(/([^A-Za-z0-9]+)/)!=-1) )
            error_field(f.od_pwd, "회원이 아니신 경우 주문서 조회시 필요한 비밀번호를 3자리 이상 입력해 주십시오.");
    }
    check_field(f.od_tel, "주문하시는 분 전화번호를 입력하십시오.");
    //check_field(f.od_addr1, "우편번호 찾기를 이용하여 주문하시는 분 주소를 입력하십시오.");
    //check_field(f.od_addr2, " 주문하시는 분의 상세주소를 입력하십시오.");
    //check_field(f.od_zip1, "");
    //check_field(f.od_zip2, "");

    clear_field(f.od_email);
    if(f.od_email.value=='' || f.od_email.value.search(/(\S+)@(\S+)\.(\S+)/) == -1)
        error_field(f.od_email, "E-mail을 바르게 입력해 주십시오.");

    if (typeof(f.od_hope_date) != "undefined")
    {
        clear_field(f.od_hope_date);
        if (!f.od_hope_date.value)
            error_field(f.od_hope_date, "희망배송일을 선택하여 주십시오.");
    }
	

    check_field(f.od_b_name, "받으시는 분 이름을 입력하십시오.");
    check_field(f.od_b_tel, "받으시는 분 전화번호를 입력하십시오.");
    //check_field(f.od_b_addr1, "우편번호 찾기를 이용하여 받으시는 분 주소를 입력하십시오.");
    //check_field(f.od_b_addr2, "받으시는 분의 상세주소를 입력하십시오.");
    //check_field(f.od_b_zip1, "");
    //check_field(f.od_b_zip2, "");

    var od_settle_bank = document.getElementById("od_settle_bank");
    if (od_settle_bank) {
        if (od_settle_bank.checked) {
            check_field(f.od_bank_account, "계좌번호를 선택하세요.");
            check_field(f.od_deposit_name, "입금자명을 입력하세요.");
        }
    }

    // 배송비를 받지 않거나 더 받는 경우 아래식에 + 또는 - 로 대입
    f.od_send_cost.value = parseInt(f.od_send_cost.value);

    if (errmsg)
    {
        alert(errmsg);
        errfld.focus();
        return false;
    }
	*/

    var settle_case = document.getElementsByName("od_settle_case");
    var settle_check = false;
    var settle_method = "";

	
    for (i=0; i<settle_case.length; i++)
    {
        if (settle_case[i].checked)
        {
            settle_check = true;
            settle_method = settle_case[i].value;
            break;
        }
    }
    if (!settle_check)
    {
        alert("결제방식을 선택하십시오.");
        return false;
    }
	

	if($("input[name='od_tel']").val() == ""){
		alert("구매자 연락처를 입력하세요.");
		return false;
	}

	if($("input[name='od_b_tel']").val() == ""){
		alert("배송지 연락처를 입력하세요.");
		return false;
	}

	if($("input[name='od_b_zip1']").val() == ""){
		alert("배송지 주소를 선택하세요.");
		return false;
	}

	if($("input[name='od_b_zip2']").val() == ""){
		alert("배송지 주소를 선택하세요.");
		return false;
	}

	if($("input[name='od_b_addr1']").val() == ""){
		alert("배송지 주소를 선택하세요.");
		return false;
	}


	if(settle_method=="무통장"){
		if($("select[name='od_bank_account']").val() == ""){
			alert("입금은행을 선택하세요.");
			return false;
		}
	}

	/*var chk = true;

	$("input[name^='od_bank']").each(function(i){
		if($("input[name^='od_bank']").eq(i).val() == ""){
			chk = false;
		}
	});

	if(chk == false){
		alert("환불정보를 입력하세요");
		return false;
	}

	if($("input[name='od_remit']").val() == ""){
		alert("환불정보를 입력하세요.");
		return false;
	}*/

	/*
	if($("input[name^='agr_1']").eq(0).is(":checked") == false || $("input[name^='agr_1']").eq(1).is(":checked") == false){
		alert("개인정보 제3자 제공 및 주의사항 동의에 체크 하시기 바랍니다.");
		return false;
	}
	*/

	if($("input[name^='agr_2']").eq(0).is(":checked") == false){
		alert("결제대행서비스 표준이용약관에 체크 하시기 바랍니다.");
		return false;
	}

	/*
    var od_price = parseInt(f.od_price.value);
    var send_cost = parseInt(f.od_send_cost.value);
    var send_cost2 = parseInt(f.od_send_cost2.value);
    var send_coupon = parseInt(f.od_send_coupon.value);

    var max_point = 0;
    if (typeof(f.max_temp_point) != "undefined")
        max_point  = parseInt(f.max_temp_point.value);

    var temp_point = 0;
    if (typeof(f.od_temp_point) != "undefined") {
        if (f.od_temp_point.value)
        {
            var point_unit = parseInt(<?php echo $default['de_settle_point_unit']; ?>);
            temp_point = parseInt(f.od_temp_point.value);

            if (temp_point < 0) {
                alert("포인트를 0 이상 입력하세요.");
                f.od_temp_point.select();
                return false;
            }

            if (temp_point > od_price) {
                alert("상품 주문금액(배송비 제외) 보다 많이 포인트결제할 수 없습니다.");
                f.od_temp_point.select();
                return false;
            }

            if (temp_point > <?php echo (int)$member['mb_point']; ?>) {
                alert("회원님의 포인트보다 많이 결제할 수 없습니다.");
                f.od_temp_point.select();
                return false;
            }

            if (temp_point > max_point) {
                alert(max_point + "점 이상 결제할 수 없습니다.");
                f.od_temp_point.select();
                return false;
            }

            if (parseInt(parseInt(temp_point / point_unit) * point_unit) != temp_point) {
                alert("포인트를 "+String(point_unit)+"점 단위로 입력하세요.");
                f.od_temp_point.select();
                return false;
            }

            // pg 결제 금액에서 포인트 금액 차감
            if(settle_method != "무통장") {
                f.good_mny.value = od_price + send_cost + send_cost2 - send_coupon - temp_point;
            }
        }
    }
	*/
    /*var tot_price = od_price + send_cost + send_cost2 - send_coupon - temp_point;

	
    if (document.getElementById("od_settle_iche")) {
        if (document.getElementById("od_settle_iche").checked) {
            if (tot_price - temp_point < 150) {
                alert("계좌이체는 150원 이상 결제가 가능합니다.");
                return false;
            }
        }
    }

    if (document.getElementById("od_settle_card")) {
        if (document.getElementById("od_settle_card").checked) {
            if (tot_price - temp_point < 1000) {
                alert("신용카드는 1000원 이상 결제가 가능합니다.");
                return false;
            }
        }
    }

    if (document.getElementById("od_settle_hp")) {
        if (document.getElementById("od_settle_hp").checked) {
            if (tot_price - temp_point < 350) {
                alert("휴대폰은 350원 이상 결제가 가능합니다.");
                return false;
            }
        }
    }
	*/

    <?php if($default['de_tax_flag_use']) { ?>
    calculate_tax();
    <?php } ?>

    // pay_method 설정
    <?php if($default['de_pg_service'] == 'kcp') { ?>
    switch(settle_method)
    {
        case "계좌이체":
            f.pay_method.value = "010000000000";
            break;
        case "가상계좌":
            f.pay_method.value = "001000000000";
            break;
        case "휴대폰":
            f.pay_method.value = "000010000000";
            break;
        case "신용카드":
            f.pay_method.value = "100000000000";
            break;
        default:
            f.pay_method.value = "무통장";
            break;
    }
    <?php } else if($default['de_pg_service'] == 'lg') { ?>
		
    switch(settle_method)
    {
        case "계좌이체":
            f.LGD_CUSTOM_FIRSTPAY.value = "SC0030";
            f.LGD_CUSTOM_USABLEPAY.value = "SC0030";
            break;
        case "가상계좌":
            f.LGD_CUSTOM_FIRSTPAY.value = "SC0040";
            f.LGD_CUSTOM_USABLEPAY.value = "SC0040";
            break;
        case "휴대폰":
            f.LGD_CUSTOM_FIRSTPAY.value = "SC0060";
            f.LGD_CUSTOM_USABLEPAY.value = "SC0060";
            break;
        case "신용카드":
            f.LGD_CUSTOM_FIRSTPAY.value = "SC0010";
            f.LGD_CUSTOM_USABLEPAY.value = "SC0010";
            break;
        default:
            f.LGD_CUSTOM_FIRSTPAY.value = "무통장";
            break;
    }
    <?php } ?>

    // 결제정보설정
    <?php if($default['de_pg_service'] == 'kcp') { ?>
    f.buyr_name.value = f.od_name.value;
    f.buyr_mail.value = f.od_email.value;
    f.buyr_tel1.value = f.od_tel.value;
    f.buyr_tel2.value = f.od_hp.value;
    f.rcvr_name.value = f.od_b_name.value;
    f.rcvr_tel1.value = f.od_b_tel.value;
    f.rcvr_tel2.value = f.od_b_hp.value;
    f.rcvr_mail.value = f.od_email.value;
    f.rcvr_zipx.value = f.od_b_zip1.value + f.od_b_zip2.value;
    f.rcvr_add1.value = f.od_b_addr1.value;
    f.rcvr_add2.value = f.od_b_addr2.value;

    if(f.pay_method.value != "무통장") {
        if(jsf__pay( f )) {
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }
    <?php } if($default['de_pg_service'] == 'lg') { ?>
    f.LGD_BUYER.value = f.od_name.value;
    f.LGD_BUYEREMAIL.value = f.od_email.value;
    f.LGD_BUYERPHONE.value = f.od_hp.value;
    f.LGD_AMOUNT.value = f.good_mny.value;
    f.LGD_RECEIVER.value = f.od_b_name.value;
    f.LGD_RECEIVERPHONE.value = f.od_b_hp.value;
    <?php if($default['de_escrow_use']) { ?>
    f.LGD_ESCROW_ZIPCODE.value = f.od_b_zip1.value + f.od_b_zip2.value;
    f.LGD_ESCROW_ADDRESS1.value = f.od_b_addr1.value;
    f.LGD_ESCROW_ADDRESS2.value = f.od_b_addr2.value;
    f.LGD_ESCROW_BUYERPHONE.value = f.od_hp.value;
    <?php } ?>
    <?php if($default['de_tax_flag_use']) { ?>
    f.LGD_TAXFREEAMOUNT.value = f.comm_free_mny.value;
    <?php } ?>

    if(f.LGD_CUSTOM_FIRSTPAY.value != "무통장") {
          Pay_Request("<?php echo $od_id; ?>", f.LGD_AMOUNT.value, f.LGD_TIMESTAMP.value);
    } else {
        f.submit();
    }
    <?php } ?>
}

// 구매자 정보와 동일합니다.
function gumae2baesong(checked) {
    var f = document.forderform;

    if(checked == true) {
        f.od_b_name.value = f.od_name.value;
        f.od_b_tel.value  = f.od_tel.value;
        //f.od_b_hp.value   = f.od_hp.value;
        //f.od_b_zip1.value = f.od_zip1.value;
        //f.od_b_zip2.value = f.od_zip2.value;
        //f.od_b_addr1.value = f.od_addr1.value;
        //f.od_b_addr2.value = f.od_addr2.value;
        //f.od_b_addr3.value = f.od_addr3.value;
        //f.od_b_addr_jibeon.value = f.od_addr_jibeon.value;
        //document.getElementById("od_b_addr_jibeon").innerText = document.getElementById("od_addr_jibeon").innerText;

        calculate_sendcost(String(f.od_b_zip1.value) + String(f.od_b_zip2.value));
    } else {
        f.od_b_name.value = "";
        f.od_b_tel.value  = "";
        //f.od_b_hp.value   = "";
        //f.od_b_zip1.value = "";
        //f.od_b_zip2.value = "";
        //f.od_b_addr1.value = "";
        //f.od_b_addr2.value = "";
        //f.od_b_addr3.value = "";
        //f.od_b_addr_jibeon.value = "";
        //document.getElementById("od_b_addr_jibeon").innerText = "";
    }
}

<?php if ($default['de_hope_date_use']) { ?>
$(function(){
    $("#od_hope_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", minDate: "+<?php echo (int)$default['de_hope_date_after']; ?>d;", maxDate: "+<?php echo (int)$default['de_hope_date_after'] + 6; ?>d;" });
});
<?php } ?>



$(document).ready(function(){
	$("input[name^='od_tax']").click(function(){
		var od_tax = $(this).val();

		if(od_tax == '0'){
			$(".tax_st").css("display", "block");
			$(".tax_st").html('휴대폰번호 <input type="text" name="od_tax_hp" style="width:80%;"><input type="hidden" name="tax_status" value="휴대폰번호">');
		}else if(od_tax == '1'){
			$(".tax_st").css("display", "block");
			$(".tax_st").html('사업자번호 <input type="text" name="od_tax_hp" style="width:80%;"><input type="hidden" name="tax_status" value="사업자번호">');
		}else{
			$(".tax_st").css("display", "none");
		}
		
	});

	calculate_order_price();
});

</script>

<?php
include_once('./_tail.php');

// 결제대행사별 코드 include (스크립트 실행)
require_once('./'.$default['de_pg_service'].'/orderform.5.php');

?>