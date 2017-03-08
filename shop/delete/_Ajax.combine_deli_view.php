<?
include_once("./_common.php");

if(get_session("mem_order_se")){
	$member[mb_id] = get_session("mem_order_se");
	$member = sql_fetch("select * from {$g5['member_table']} where mb_id='".$member[mb_id]."'");
}else{
	$member[mb_id] = $member[mb_id];
}
?>

<link rel="stylesheet" type="text/css" href="<?=G5_URL?>/css/default_shop_group.css" />

<div style="height:15px;">
	브랜드건별주문번호 <?=$_POST[od_id]?>
</div>

<div style="margin:10px 0 0 0;height:550px;">
	
	<div style="width:100%;height:400px;overflow-y:auto;">

	<table id="sod_list" cellspacing="0" cellpadding="0" class="tbl_head01">
	<thead>
	<tr>
		<th scope="col" class="right" colspan="2">상품정보</th>
		<th scope="col" class="right" width="70px">수량</th>
		<th scope="col" class="right">상품금액</th>
		<th scope="col" class="right">할인</th>
		<th scope="col" width="100px">배송비</th>
	</tr>
	</thead>
	<tbody>
	<?php
	$s_cart_id = $_POST[od_id];
	$ct_type = $_POST[ct_type];
	$tot_point = 0;
	$tot_sell_price = 0;

	$is_bank_payment = $is_cash_payment =  true;

	$goods = $goods_it_id = "";
	$goods_count = -1;

	// $s_cart_id 로 현재 장바구니 자료 쿼리
	$sql = " select a.ct_id,
					a.it_id,
					a.it_name,
					a.ct_price,
					a.ct_card_price,
					a.ct_point,
					a.ct_qty,
					a.ct_status,
					a.ct_send_cost,
					a.ct_gubun,
					a.ct_payment,
					a.ct_type,
					a.ct_op_option,
					b.ca_id,
					b.gp_sc_price
			   from {$g5['g5_shop_cart_table']} a left join {$g5['g5_shop_group_purchase_table']} b on ( a.it_id = b.gp_id )
			  where a.od_id = '$s_cart_id' and `ct_status`='입금대기' and ct_type = '$ct_type'
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

	$k = 0;

	for ($i=0; $row=mysql_fetch_array($result); $i++)
	{
		// 합계금액 계산
		$sql = " select SUM(IF(io_type = 1, (io_price * ct_qty), ((ct_price + io_price) * (ct_qty - ct_buy_qty)))) as price,
						SUM(ct_point * ct_qty) as point,
						SUM(ct_qty) as qty,
						SUM(ct_buy_qty) as buy_qty
					from {$g5['g5_shop_cart_table']}
					where it_id = '{$row['it_id']}'
					  and od_id = '$s_cart_id'
					  and (ct_status='입금대기'
					  or ct_gp_status='결제완료')
					  and ct_type != ''
					  ";
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
		for($b = 0; $b < count($op_arr); $b++){
			if($op_arr[$b]){
				$op_row = sql_fetch("select * from {$g5['g5_shop_option2_table']} where it_id='".$row[it_id]."' and con='".$op_arr[$b]."' ");
				$op_price = $op_price + $op_row[price];
			}
		}

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

	?>
	<tr>
		<td class="sod_img"><?php echo $image; ?></td>
		<td class="right">
			<input type="hidden" name="it_id[<?php echo $i; ?>]"    value="<?php echo $row['it_id']; ?>">
			<input type="hidden" name="it_name[<?php echo $i; ?>]"  value="<?php echo get_text($row['it_name']); ?>">
			<input type="hidden" name="it_price[<?php echo $i; ?>]" value="<?php echo $sell_price; ?>">
			<input type="hidden" name="cp_id[<?php echo $i; ?>]" value="">
			<input type="hidden" name="cp_price[<?php echo $i; ?>]" value="0">
			<input type="hidden" name="ca_id[<?php echo $i; ?>]"    value="<?php echo $row['ct_type']; ?>">
			<input type="hidden" name="gp_sc_price[<?php echo $i; ?>]"    value="<?php echo $row['gp_sc_price']; ?>">
			<div>
				<?php if($default['de_tax_flag_use']) { ?>
				<input type="hidden" name="it_notax[<?php echo $i; ?>]" value="<?php echo $row['it_notax']; ?>">
				<?php } ?>
				<?php echo stripslashes($name).$rem; ?>
			</div>
			<div style="margin:7px 0 0 0;">
				<div style="float:left;width:50%;text-align:right;">판매가</div>
				<div class="pro_price"><?php echo number_format($row['ct_price']); ?></div>
			</div>
		</td>
		<td class="td_num right ct_qty"><?php echo number_format($sum['qty'] - $sum['buy_qty']); ?></td>
		<td class="td_numbig right"><span class="total_price sell_price"><?php echo number_format($sell_price); ?></span></td>
		<td class="td_num right right">-</td>
		<td class="td_num"><?php echo $row[gp_sc_price]; ?></td>
	</tr>

	<?php
		$tot_point      += $point;
		$tot_sell_price += $sell_price;
		$send_cost += $row[gp_sc_price];

		$k++;
	} // for 끝

	if ($i != 0) {
		// 배송비 계산
		//$send_cost = get_sendcost_gp($s_cart_id);
		//$send_cost1 = get_sendcost_gp1($s_cart_id, 0, $ct_type_que);

		if($i > 1){
			$send_cost = 3500;
		}else if($i == 1){
			if($send_cost){
				$send_cost = $send_cost;
			}else{
				$send_cost = 3500;
			}
			
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


	<div class="cl combine_deli_price_info">
		<table border="0" cellspacing="0" cellpadding="0" width="100%" class="all_price_info_tb">
			<tr>
				<td width="96px" style="font-size:17px;color:#545454;text-align:center;">총합계</td>
				<td width="186px">
					<table border="0" cellspacing="0" cellpadding="0" width="100%" class="tb">
						<tr height="32px">
							<td>상품금액</td>
						</tr>
						<tr height="53px">
							<td class="right_aln">
								<span class="price_all"><?=number_format($tot_sell_price)?></span><span style="color:#bcbcbc">원</span>
							</td>
						</tr>
					</table>
				</td>
				<td width="137px">
					<table border="0" cellspacing="0" cellpadding="0" width="100%" class="tb">
						<tr height="32px">
							<td>할인금액</td>
						</tr>
						<tr height="53px">
							<td class="right_aln"><span class="price_all">0</span><span style="color:#bcbcbc">원</span></td>
						</tr>
					</table>
				</td>
				<td width="147px">
					<table border="0" cellspacing="0" cellpadding="0" width="100%" class="tb">
						<tr height="32px">
							<td>배송비</td>
						</tr>
						<tr height="53px">
							<td class="right_aln" style="padding-right:30px;"><span class="price_all_cost"><?=number_format($send_cost)?></span><span style="color:#bcbcbc">원</span></td>
						</tr>
					</table>
				</td>
				<td>
					<table border="0" cellspacing="0" cellpadding="0" width="100%" class="tb">
						<tr height="32px">
							<td>총결제예상금액</td>
						</tr>
						<tr height="53px">
							<td class="right_aln" style="padding:-5px 0 0 0;">
								<span class="price_all_price"><?=number_format($tot_sell_price + $send_cost)?></span>
								<span style="font-weight:normal;font-size:14px;color:#ff4e00;">원</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>

</div>