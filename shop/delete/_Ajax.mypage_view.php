<?
include_once("./_common.php");

//2014072318130916
$s_cart_id = $_POST[T_od_id];
$status = $_POST[status];
?>

<? if(G5_IS_MOBILE) {?>
	<div class="detailViewContainer">
		<div class="orderNumber">
			<span class="label">주문번호 </span><span class="data"><?=$s_cart_id?></span>
		</div>
		<?php
		$od_sql_res = sql_query("
				select * from {$g5['g5_shop_order_table']}
				where od_id='".$s_cart_id."'
			");

		$in_que = " and od_id in( ";
		for($j = 0; $od_sql_row = mysql_fetch_array($od_sql_res); $j++){
			$in_que .= "'".$od_sql_row[od_id]."', ";
		}
		$in_que = substr($in_que, 0, strlen($in_que)-2);
		$in_que .= ") ";


		// $s_cart_id 로 현재 장바구니 자료 쿼리
		$sql = "
				select * from {$g5['g5_shop_cart_table']}
				where 1 $in_que
				group by total_amount_code order by total_amount_code desc
				";

		$result = sql_query($sql);

		for ($i=0; $row1=mysql_fetch_array($result); $i++)
		{
		$send_cost_all = 0;
		?>
		<table id="sod_list" cellspacing="0" cellpadding="0" class="tbl_head01">
			<thead>
			<tr>
				<td colspan="6" style="border-top:0px;padding-bottom:8px;">
					<span style="font-weight:bold;">브랜드건별주문번호</span> <?=$row1[total_amount_code]?></td>
			</tr>
			<tr>
				<th scope="col" colspan="2">상품정보</th>
				<th scope="col">수량</th>
				<th scope="col">옵션</th>
				<th scope="col">상품금액</th>
				<!--<th scope="col">미입고</th>-->
				<th scope="col">할인</th>
				<!--<th scope="col">배송비</th>-->
			</tr>
			</thead>
			<tbody>

			<?
			$sql = "
					select * from {$g5['g5_shop_cart_table']}
					where 1 $in_que
					and total_amount_code='".$row1[total_amount_code]."'
					group by it_id
					order by total_amount_code desc
				";

			$res = sql_query($sql);
			for($a = 0; $row = mysql_fetch_array($res); $a++){

				// 합계금액 계산
				$sql = " select SUM(IF(io_type = 1, (io_price * ct_qty), ((ct_price + io_price) * ct_qty))) as price,
									SUM(ct_point * (ct_qty-ct_gp_soldout)) as point, sum(ct_gp_soldout) as ct_total_gp_soldout, sum(ct_wearing_cnt) as ct_total_gp_wearing,
									SUM(ct_qty) as qty,
									SUM(ct_buy_qty) as buy_qty
								from {$g5['g5_shop_cart_table']}
								where it_id = '{$row['it_id']}'
								and od_id = '".$row[od_id]."'
								";

				$sum = sql_fetch($sql);

				$gp_row = sql_fetch("select * from {$g5['g5_shop_group_purchase_table']} where gp_id='".$row['it_id']."' ");
				$row[it_price] = getGroupPurchaseQtyBasicPrice($gp_row[gp_id],$sum['qty']-$sum['buy_qty']);

				$a1 = '<a href="'.G5_URL.'/shop/grouppurchase.php?gp_id='.$gp_row[gp_id].'">';
				$a2 = '</a>';

				$od = sql_fetch("select * from {$g5['g5_shop_order_table']} where od_id='".$row[od_id]."' ");

				if($row['buy_status'] == "y"){
					$cnt = $row['ct_price'] * ($sum[qty]-$sum[ct_total_gp_soldout]);
				}else{
					$cnt = "0";
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
				//$sell_price = $sum['price'];
				$sell_price = ceil($cnt / 100) * 100 + $op_price;

				$not_stocked_price = ceil($row[ct_price] * $row[ct_notstocked_cnt] / 100) * 100;

				$image = get_gp_image($row['it_id'], 35, 35);
				?>
				<tr>
					<td class="sod_img"><?php echo $image; ?></td>
					<td width="30%" class="borderRight">
						<input type="hidden" name="it_id[<?php echo $i; ?>]" value="<?php echo $row['it_id']; ?>">
						<input type="hidden" name="it_name[<?php echo $i; ?>]" value="<?php echo get_text($row['it_name']); ?>">
						<input type="hidden" name="it_price[<?php echo $i; ?>]" value="<?php echo $sell_price; ?>">
						<input type="hidden" name="cp_id[<?php echo $i; ?>]" value="">
						<input type="hidden" name="cp_price[<?php echo $i; ?>]" value="0">
						<input type="hidden" name="ca_id[<?php echo $i; ?>]" value="<?php echo $row['ct_type']; ?>">
						<input type="hidden" name="gp_sc_price[<?php echo $i; ?>]" value="<?php echo $row['gp_sc_price']; ?>">
						<div>
							<?php if($default['de_tax_flag_use']) { ?>
								<input type="hidden" name="it_notax[<?php echo $i; ?>]" value="<?php echo $row['it_notax']; ?>">
							<?php } ?>
						</div>
						<div><?php echo $a1.get_text($row['it_name']).$a2; ?></div>
						<div style="margin:7px 0 0 0;">
							<div style="float:left;width:50%;text-align:right;">판매가</div>
							<div class="pro_price"><?php echo number_format($row['ct_price']); ?></div>
						</div>
					</td>
					<td class="td_num right ct_qty borderRight"><?php echo number_format($sum['qty']); ?><?php if($sum['ct_total_gp_soldout']>0)echo '<br><font color="red">('.$sum['ct_total_gp_soldout'].'개 품절)</font>';?></td>
					<td class="td_numbig right borderRight"><span class="total_price sell_price"><?php echo $op_name; ?></span></td>
					<td class="td_numbig right borderRight"><span class="total_price sell_price"><?php echo number_format($sell_price); ?></span></td>
					<!--
					<td class="td_num right right">
					<span class="total_price sell_price">
						<?php
						if($sum['ct_total_gp_wearing']>0)echo "<font color='blue'>".number_format($sum['ct_total_gp_wearing'])."개</font>";
						else echo "-";
						?>

					</span>
					</td>
					-->
					<td class="td_num right right">-</td>
					<!--<td class="td_num">-</td>-->
				</tr>

				<?php
				$tot_point      += $point;
				$tot_sell_price += $sell_price;
				$send_cost1 += 3500;

				if($row['buy_status'] == "y"){
					if($buy_st[cnt]){
						$sell_price2 = $sell_price2 + ($row[it_price] * $sum['qty']);
					}
				}

				$k++;

			}
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

			$od = sql_fetch("select SUM(od_send_cost) as od_send_cost, od_cart_price from {$g5['g5_shop_order_table']} where od_id='".$s_cart_id."' ");
			if($od['od_cart_price']>0)$send_cost = $od[od_send_cost];
			else $send_cost = 0;

			$tot_sell_price = $od['od_cart_price'];


			// 복합과세처리
			if($default['de_tax_flag_use']) {
				$comm_tax_mny = round(($tot_tax_mny + $send_cost) / 1.1);
				$comm_vat_mny = ($tot_tax_mny + $send_cost) - $comm_tax_mny;
			}
			?>
			</tbody>
		</table>
		<div class="cl combine_deli_price_info">
		<table border="0" cellspacing="0" cellpadding="0" width="100%" class="all_price_info_tb">
			<tr>
				<td class="textAlignCenter">총합계</td>
				<td>
					<table width="100%">
						<tr style="background-color: #eeeeee;">
							<td class="textAlignCenter">상품금액</td>
						</tr>
						<tr>
							<td class="textAlignRight">
								<span class="price_all"><?=number_format($tot_sell_price)?></span><span style="color:#bcbcbc">원</span>
							</td>
						</tr>
					</table>
				</td>
				<td>
					<table width="100%" >
						<tr style="background-color: #eeeeee;">
							<td class="textAlignCenter">할인금액</td>
						</tr>
						<tr>
							<td class="textAlignRight"><span class="price_all">0</span><span style="color:#bcbcbc">원</span></td>
						</tr>
					</table>
				</td>
				<td>
					<table width="100%">
						<tr style="background-color: #eeeeee;">
							<td class="textAlignCenter">배송비</td>
						</tr>
						<tr>
							<td class="textAlignRight" style="padding-right:30px;"><span class="price_all_cost"><?=number_format($send_cost)?></span><span style="color:#bcbcbc">원</span></td>
						</tr>
					</table>
				</td>
				<td>
					<table width="100%">
						<tr style="background-color: #eeeeee;">
							<td class="textAlignCenter">결제금액</td>
						</tr>
						<tr>
							<td class="textAlignRight" style="padding:-5px 0 0 0;">
								<span class="price_all_price"><?=number_format($tot_sell_price + $send_cost)?></span>
								<span style="color:#ff4e00;">원</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
<?} else {?>
	<link rel="stylesheet" type="text/css" href="<?=G5_URL?>/css/default_shop_group.css" />

	<div style="maegin:0 0 10px 0;"><span style="font-weight:bold;">주문번호</span><span style="color:#74d3d0;"><?=$s_cart_id?></span></div>

	<?if($status == "T"){?>
		<div style="margin:10px 0 0 0;height:700px;">
			<div style="width:100%;height:600px;overflow-y:auto;">

			<?php
			$od_sql_res = sql_query("
				select * from {$g5['g5_shop_order_table']}
				where combine_deli_code='".$s_cart_id."'
			");

			$in_que = " and od_id in( ";
			for($j = 0; $od_sql_row = mysql_fetch_array($od_sql_res); $j++){
				$in_que .= "'".$od_sql_row[od_id]."', ";
			}
			$in_que = substr($in_que, 0, strlen($in_que)-2);
			$in_que .= ") ";


			// $s_cart_id 로 현재 장바구니 자료 쿼리
			$sql = "
				select * from {$g5['g5_shop_cart_table']}
				where 1 $in_que
				group by total_amount_code order by total_amount_code desc
				";

			$result = sql_query($sql);

			for ($i=0; $row1=mysql_fetch_array($result); $i++)
			{
				$tot_sell_price1 = 0;
				$send_cost_all = 0;
			?>

			<table id="sod_list" cellspacing="0" cellpadding="0" class="tbl_head01" style="width:100%;">
			<thead>
			<tr>
				<td colspan="6" style="border-top:0px;"><span style="font-weight:bold;">브랜드건별주문번호</span> <?=$row1[total_amount_code]?></td>
			</tr>
			<tr>
				<th scope="col" class="right" colspan="2">상품정보</th>
				<th scope="col" class="right" width="70px">수량</th>
				<th scope="col" class="right">상품금액</th>
				<th scope="col" class="right">옵션</th>
				<th scope="col" class="right">미입고</th>
				<th scope="col" class="right">할인</th>
				<th scope="col" width="100px">배송비</th>
			</tr>
			</thead>
			<tbody>

			<?
				$sql = "
					select * from {$g5['g5_shop_cart_table']}
					where 1 $in_que
					and total_amount_code='".$row1[total_amount_code]."'
					group by it_id
					order by total_amount_code desc
				";

				$res = sql_query($sql);
				for($a = 0; $row = mysql_fetch_array($res); $a++){

					// 합계금액 계산
					$sql = " select SUM(IF(io_type = 1, (io_price * ct_qty), ((ct_price + io_price) * ct_qty))) as price,
									SUM(ct_point * ct_qty) as point, sum(ct_gp_soldout) as ct_total_gp_soldout, sum(ct_wearing_cnt) as ct_total_gp_wearing,
									SUM(ct_qty) as qty,
									SUM(ct_buy_qty) as buy_qty
								from {$g5['g5_shop_cart_table']}
								where it_id = '{$row['it_id']}'
								and od_id = '".$row[od_id]."'
								";

					$sum = sql_fetch($sql);

					$gp_row = sql_fetch("select * from {$g5['g5_shop_group_purchase_table']} where gp_id='".$row['it_id']."' ");
					$row[it_price] = getGroupPurchaseQtyBasicPrice($gp_row[gp_id],$sum['qty']-$sum['buy_qty']);

					$a1 = '<a href="'.G5_URL.'/shop/grouppurchase.php?gp_id='.$gp_row[gp_id].'">';
					$a2 = '</a>';

					if($row['buy_status'] == "y"){
						$cnt = $row['ct_price'] * ($sum[qty]-$sum[ct_total_gp_soldout]);
					}else{
						$cnt = "0";
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
					//$sell_price = $sum['price'];
					$sell_price = ceil($cnt / 100) * 100 + $op_price;


					$not_stocked_price = ceil($row[ct_price] * $row[ct_notstocked_cnt] / 100) * 100;

					$image = get_gp_image($row['it_id'], 70, 70);
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
							</div>
							<div><?php echo $a1.get_text($row['it_name']).$a2; ?></div>
							<div style="margin:7px 0 0 0;">
								<div style="float:left;width:50%;text-align:right;">판매가</div>
								<div class="pro_price"><?php echo number_format($row['ct_price']); ?></div>
							</div>
						</td>
						<td class="td_num right ct_qty"><?php echo number_format($sum['qty']); ?>

								<?php if($sum['ct_total_gp_soldout']>0)echo '<br><font color="red">('.$sum['ct_total_gp_soldout'].'개 품절)</font>';?></td>
						<td class="td_numbig right">
							<span class="total_price sell_price">
								<?php
								if($sum[ct_total_gp_wearing] != "미입고"){
									echo number_format($sell_price);
								}else{
									echo "-";
								}
								?>
							</span>
						</td>
						<td class="td_num right ct_qty"><?php echo $op_name; ?></td>
						<td class="td_numbig right">
							<span class="total_price sell_price">
								<?php
								if($row[ct_notstocked_cnt]){
									echo number_format($not_stocked_price)."</br>(".$row[ct_notstocked_cnt]."건)";
								}else{
									if($row[ct_status] != "미입고"){
										echo "-";
									}else{
										echo number_format($sell_price)."</br>(".$row[ct_qty]."건)";
									}
								}
								?>
							</span>
						</td>
						<td class="td_num right right">-</td>
						<td class="td_num">-</td>
					</tr>

			<?php
					$tot_point      += $point;

					if($row[ct_status] != "미입고"){
						$tot_sell_price += $sell_price;
						$tot_sell_price1 += $sell_price;
					}

					if($row['buy_status'] == "y"){
						if($buy_st[cnt]){
							$sell_price2 = $sell_price2 + ($row[it_price] * $sum['qty']);
						}
					}

					$k++;


				}


			?>

				<tr>
					<td colspan="4" style="border:0px;"></td>
					<td colspan="2" style="border:0px;text-align:right;">
						<!--배송료&nbsp;&nbsp;<?//=number_format($send_cost_all)?>&nbsp;&nbsp;&nbsp;&nbsp;-->
						상품합계금액&nbsp;&nbsp;<span style="color:#000;"><?=number_format($tot_sell_price1)?>&nbsp;&nbsp;</span>
					</td>
				</tr>

			<?
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

			$od = sql_fetch("select SUM(od_send_cost) as od_send_cost, od_cart_price from {$g5['g5_shop_order_table']} where combine_deli_code='".$s_cart_id."' ");
			if($od['od_cart_price']>0)$send_cost_all1 = $od[od_send_cost];
			else $send_cost_all1 = 0;

			$tot_sell_price = $od['od_cart_price'];


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
									<td class="right_aln" style="padding-right:30px;"><span class="price_all_cost"><?=number_format($send_cost_all1)?></span><span style="color:#bcbcbc">원</span></td>
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
										<span class="price_all_price"><?=number_format($tot_sell_price + $send_cost_all1)?></span>
										<span style="font-weight:normal;font-size:14px;color:#ff4e00;">원</span>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>

		</div>

	<?}else if($status == "N"){?>
		<div style="margin:10px 0 0 0;height:700px;">
			<div style="width:100%;height:600px;overflow-y:auto;">

			<?php
			$od_sql_res = sql_query("
				select * from {$g5['g5_shop_order_table']}
				where od_id='".$s_cart_id."'
			");

			$in_que = " and od_id in( ";
			for($j = 0; $od_sql_row = mysql_fetch_array($od_sql_res); $j++){
				$in_que .= "'".$od_sql_row[od_id]."', ";
			}
			$in_que = substr($in_que, 0, strlen($in_que)-2);
			$in_que .= ") ";


			// $s_cart_id 로 현재 장바구니 자료 쿼리
			$sql = "
				select * from {$g5['g5_shop_cart_table']}
				where 1 $in_que
				group by total_amount_code order by total_amount_code desc
				";

			$result = sql_query($sql);

			for ($i=0; $row1=mysql_fetch_array($result); $i++)
			{
				$send_cost_all = 0;
			?>
			<table id="sod_list" cellspacing="0" cellpadding="0" class="tbl_head01" style="width:100%;">
			<thead>
			<tr>
				<td colspan="6" style="border-top:0px;"><span style="font-weight:bold;">브랜드건별주문번호</span> <?=$row1[total_amount_code]?></td>
			</tr>
			<tr>
				<th scope="col" class="right" colspan="2">상품정보</th>
				<th scope="col" class="right" width="70px">수량</th>
				<th scope="col" class="right">상품금액</th>
				<th scope="col" class="right">옵션</th>
				<th scope="col" class="right">미입고</th>
				<th scope="col" class="right">할인</th>
				<th scope="col" width="100px">배송비</th>
			</tr>
			</thead>
			<tbody>

			<?
				$sql = "
					select * from {$g5['g5_shop_cart_table']}
					where 1 $in_que
					and total_amount_code='".$row1[total_amount_code]."'
					group by it_id
					order by total_amount_code desc
				";

				$res = sql_query($sql);
				for($a = 0; $row = mysql_fetch_array($res); $a++) {
					// 합계금액 계산
					$sql = " select SUM(IF(io_type = 1, (io_price * ct_qty), ((ct_price + io_price) * ct_qty))) as price,
									SUM(ct_point * (ct_qty-ct_gp_soldout)) as point, sum(ct_gp_soldout) as ct_total_gp_soldout, sum(ct_wearing_cnt) as ct_total_gp_wearing,
									SUM(ct_qty) as qty,
									SUM(ct_buy_qty) as buy_qty
								from {$g5['g5_shop_cart_table']}
								where it_id = '{$row['it_id']}'
								and od_id = '".$row[od_id]."'
								";

					$sum = sql_fetch($sql);

					$gp_row = sql_fetch("select * from {$g5['g5_shop_group_purchase_table']} where gp_id='".$row['it_id']."' ");
					$row[it_price] = getGroupPurchaseQtyBasicPrice($gp_row[gp_id],$sum['qty']-$sum['buy_qty']);

					$a1 = '<a href="'.G5_URL.'/shop/grouppurchase.php?gp_id='.$gp_row[gp_id].'">';
					$a2 = '</a>';

					$od = sql_fetch("select * from {$g5['g5_shop_order_table']} where od_id='".$row[od_id]."' ");

					if($row['buy_status'] == "y"){
						$cnt = $row['ct_price'] * ($sum[qty]-$sum[ct_total_gp_soldout]);
					}else{
						$cnt = "0";
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
					//$sell_price = $sum['price'];
					$sell_price = ceil($cnt / 100) * 100 + $op_price;
					$not_stocked_price = ceil($row[ct_price] * $row[ct_notstocked_cnt] / 100) * 100;
					$image = get_gp_image($row['it_id'], 35, 0);
			?>
			<tr>
				<td class="sod_img"><?php echo $image; ?></td>
				<td class="right" style="width:200px;">
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
					</div>
					<div><?php echo $a1.get_text($row['it_name']).$a2; ?></div>
					<div style="margin:7px 0 0 0;">
						<div style="float:left;width:50%;text-align:right;">판매가</div>
						<div class="pro_price"><?php echo number_format($row['ct_price']); ?></div>
					</div>
				</td>
				<td class="td_num right ct_qty"><?php echo number_format($sum['qty']); ?><?php if($sum['ct_total_gp_soldout']>0)echo '<br><font color="red">('.$sum['ct_total_gp_soldout'].'개 품절)</font>';?></td>
				<td class="td_numbig right"><span class="total_price sell_price"><?php echo number_format($sell_price); ?></span></td>
				<td class="td_numbig right"><span class="total_price sell_price"><?php echo $op_name; ?></span></td>
				<td class="td_num right right">
					<span class="total_price sell_price">
						<?php
						if($sum['ct_total_gp_wearing']>0)echo "<font color='blue'>".number_format($sum['ct_total_gp_wearing'])."개</font>";
						else echo "-";
						?>

					</span>
				</td>
				<td class="td_num right right">-</td>
				<td class="td_num">-</td>
			</tr>

			<?php
					$tot_point      += $point;
					$tot_sell_price += $sell_price;
					$send_cost1 += 3500;

					if($row['buy_status'] == "y"){
						if($buy_st[cnt]){
							$sell_price2 = $sell_price2 + ($row[it_price] * $sum['qty']);
						}
					}

					$k++;

				}
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

			$od = sql_fetch("select SUM(od_send_cost) as od_send_cost, od_cart_price from {$g5['g5_shop_order_table']} where od_id='".$s_cart_id."' ");
			if($od['od_cart_price']>0)$send_cost = $od[od_send_cost];
			else $send_cost = 0;

			$tot_sell_price = $od['od_cart_price'];


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
	<?}?>
<?}?>