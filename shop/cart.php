<?php
include_once('./_common.php');

// cart id 설정
set_cart_id($sw_direct);

$s_cart_id = get_session('ss_cart_id');
// 선택필드 초기화
$sql = " update {$g5['g5_shop_cart_table']} set ct_select = '0' where od_id = '$s_cart_id' ";
sql_query($sql);

$cart_action_url = G5_SHOP_URL.'/cartupdate.php';

if (G5_IS_MOBILE) {
    include_once(G5_MSHOP_PATH.'/cart.php');
    return;
}

$k = 0;

$g5['title'] = '쇼핑카트';
include_once('./_head.php');
?>

<!-- 장바구니 시작 { -->
<script src="<?php echo G5_JS_URL; ?>/shop.js"></script>

<!-- 타이틀 -->
<div class="cart_title"><?=$g5['title']?></div>

<!-- navi -->
<div class="cart_nav">
	<ul>
		<li><img src="img/cart_nav1_on.gif" border="0" align="absmiddle"></li>
		<li><img src="img/cart_nav2.gif" border="0" align="absmiddle"></li>
		<li><img src="img/cart_nav3.gif" border="0" align="absmiddle"></li>
	</ul>
</div>

<!-- 탭메뉴 -->


<form name="frmcartlist" id="sod_bsk_list" method="post" action="<?php echo $cart_action_url; ?>">
<input type="hidden" name="buy_kind" value="투데이스토어">

<div class="list_box">

	<div class="sub_title">일반상품</div>

	<div id="sod_bsk">

		<div class="tbl_head01 tbl_wrap product1">
			<table>
			<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="ct_all" value="1" id="ct_all" checked="checked" idx="1" status="1"></th>
				<th scope="col" class="right" colspan="2">상품정보</th>
				<th scope="col" class="right" width="70px">수량</th>
				<th scope="col" class="right">상품금액</th>
				<th scope="col" class="right">옵션</th>
				<th scope="col" class="right">할인</th>
				<th scope="col" class="right">배송</th>
				<th scope="col" width="100px">선택</th>
			</tr>
			</thead>
			<tbody>
			<?php
			$tot_point = 0;
			$tot_sell_price = 0;
			$send_cost1 = 0;

			// $s_cart_id 로 현재 장바구니 자료 쿼리
			$cart_sql = "	SELECT	CT.	number,
														CT.	mb_id,	/*계정 또는 세션아이디*/
														CT.	gpcode,	/*연결된 공구코드*/
														CT.	it_id,	/*상품코드*/
														CT.	it_qty,	/*상품수량*/
														CT.	it_name,	/*상품명*/
														CT.	stats,	/*상태값*/
														CT.	reg_date	/*등록일시*/
										FROM		coto_cart CT
														LEFT JOIN g5_shop_group_purchase GP ON (GP.gp_id = CT.it_id)
										WHERE		CT.ss_id = '$_SESSION[ss_id]'
										OR			CT.mb_id = '$member[mb_id]'
										ORDER BY 	CT.reg_date DESC
			";
			$result = sql_query($cart_sql);

			$it_send_cost = 0;

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

				if ($i==0) { // 계속쇼핑
					$continue_ca_id = $row['ca_id'];
				}

				$a1 = '<a href="./item.php?it_id='.$row['it_id'].'"><b>';
				$a2 = '</b></a>';
				$image = get_it_image($row['it_id'], 70, 70);

				$it_name = $a1 . stripslashes($row['it_name']) . $a2;
				$it_options = print_item_options($row['it_id'], $s_cart_id);
				if($it_options) {
					$mod_options = '<div class="sod_option_btn"><button type="button" class="mod_options">선택사항수정</button></div>';
					$it_name .= '<div class="sod_opt">'.$it_options.'</div>';
				}

				$item = sql_fetch("select * from {$g5['g5_shop_item_table']} where it_id='".$row[it_id]."' ");

				//옵션 상품
				$op_arr = explode("|", $row[ct_op_option]);
				$op_price = 0;
				for($b = 0; $b < count($op_arr); $b++){
					if($op_arr[$b]){
						$op_row = sql_fetch("select * from {$g5['g5_shop_option2_table']} where it_id='".$row[it_id]."' and con='".$op_arr[$b]."' ");
						$op_price = $op_price + $op_row[price];
						$op_name .= $op_row[con].",";
					}
				}
				$op_name = substr($op_name, 0, strlen($op_name)-1);

				// 배송비
				switch($row['it_sc_method'])
				{
					case 1:
						$ct_send_cost = "착불</br>(".number_format($item[it_sc_price]).")";
						//$ct_send_cost = get_sendcost1($s_cart_id, 0, "n");
						break;
					case 2:
						$ct_send_cost = '무료';
						break;
					default:
						$ct_send_cost = '선불</br>('.number_format($item[it_sc_price]).")";
						//$ct_send_cost = get_sendcost1($s_cart_id, 0, "n");
						break;
				}

				$point      = $sum['point'];
				$sell_price = $sum['price'];
			?>

			<tr>
				<td class="td_chk">
					<?=$row[auc_status]?>
					<input type="checkbox" name="ct_chk[<?php echo $k; ?>]" value="1" id="ct_chk_<?php echo $k; ?>" ct_send_cost="<?=$item[it_sc_price]?>" status="1" checked="checked">
				</td>
				<td class="sod_img"><?php echo $image; ?></td>
				<td class="right">
					<input type="hidden" name="it_id[<?php echo $k; ?>]"    value="<?php echo $row['it_id']; ?>">
					<input type="hidden" name="it_name[<?php echo $k; ?>]"  value="<?php echo get_text($row['it_name']); ?>">
					<?php echo $it_name.$mod_options; ?>
					<div><?php echo get_text($row['it_name']); ?></div>
					<div style="margin:7px 0 0 0;">
						<div style="float:left;width:50%;text-align:right;">판매가</div>
						<div class="pro_price"><?php echo number_format($row['ct_price']); ?></div>
					</div>
				</td>
				<td class="td_num right" align="center"><?php echo number_format($sum['qty']); ?></td>
				<td class="td_numbig right"><span id="sell_price_<?php echo $k; ?>" class="sell_price sell_price<?php echo $row['it_id']; ?>"><?php echo number_format($sell_price); ?></span></td>
				<!--<td class="td_numbig"><?php// echo number_format($point); ?></td>-->
				<td class="td_num right"><?php echo $op_name;?></td>
				<td class="td_num right">-</td>
				<td class="td_num right"><?php echo $ct_send_cost; ?></td>
				<td class="td_num">
					<img src="img/cart_buy_bn.gif" align="absmiddle" class="cart_buy" style="cursor:pointer;"></br>
					<a href="javascript:item_wish(document.fwish, '<?php echo $row['it_id']; ?>');" id="sit_btn_wish"><img src="img/cart_wishlist_bn.gif" align="absmiddle" style="border:0px;cursor:pointer;"></a></br>
					<img src="img/cart_del_bn.gif" align="absmiddle" style="border:0px;cursor:pointer;" onclick="return form_check('delete', '', '<?php echo $row['it_id']; ?>');">
				</td>
			</tr>

			<?php
				$tot_point      += $point;
				$tot_sell_price += $sell_price;
				$send_cost1 += $item[it_sc_price];

				$k++;
			} // for 끝

			if ($i == 0) {
				echo '<tr><td colspan="8" class="empty_table">장바구니에 담긴 상품이 없습니다.</td></tr>';
			} else {
				// 배송비 계산
				//$send_cost1 = get_sendcost1($s_cart_id, 0, "n");
				$send_cost = get_sendcost($s_cart_id, 0);

				if($k > 1){
					$cost1 = 3500;
				}else{
					$cost1 = $send_cost1;
				}
			}

			?>
			</tbody>
			</table>
		</div>

		<!-- 상품 선택 버튼 -->
		<div class="pro_choi_bn">
			<ul>
				<li class="pro_all_chk" idx="1">상품 전체선택</li>
				<li class="pro_all_rel" idx="1">상품 선택해제</li>
				<li onclick="return form_check('seldelete1', '1');">선택상품 삭제</li>
			</ul>
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
					<td><div class="price_box"><span class="price"><?=number_format($cost)?></span><span style="color:#818181;font-weight:normal;font-size:14px;">원</span></div></td>
					<td><div class="price_box all"><span class="price">0</span><span style="font-weight:normal;font-size:14px;">원</span></div></td>
				</tr>
			</table>
		</div>

	</div>

	<div class="sub_title">경매상품</div>


	<div id="sod_bsk1">

		<div class="tbl_head01 tbl_wrap product2">
			<table>
			<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="ct_all" value="1" id="ct_all" checked="checked" idx="2" status="2"></th>
				<th scope="col" colspan="3">상품정보</th>
				<th scope="col">현재입찰가</th>
				<th scope="col">내 입찰가</th>
				<th scope="col">진행상태</th>
				<th scope="col">낙찰가</th>
				<th scope="col" width="100px">선택</th>
			</tr>
			</thead>
			<tbody>
			<?php
			//$tot_point = 0;
			//$tot_sell_price = 0;
			$send_cost1 = 0;

			// $s_cart_id 로 현재 장바구니 자료 쿼리
			$sql = " select a.ct_id,
							a.it_id,
							a.it_name,
							a.ct_price,
							a.ct_point,
							a.ct_qty,
							a.ct_status,
							it_sc_method,
							a.ct_op_option,
							b.ca_id,
							b.ca_id2,
							b.ca_id3
					   from {$g5['g5_shop_cart_table']} a left join {$g5['g5_shop_item_table']} b on ( a.it_id = b.it_id )
					  where a.od_id = '$s_cart_id'
					  and ct_status IN ( '쇼핑', '입금대기', '결제완료', '상품준비중', '배송대기', '배송중', '배송완료' )
					  and a.auc_status='y' and ct_type='' and a.mb_id='".$member[mb_id]."' ";

			if($default['de_cart_keep_term']) {
				$ctime = date('Y-m-d', G5_SERVER_TIME - ($default['de_cart_keep_term'] * 86400));
				$sql .= " and substring(a.ct_time, 1, 10) >= '$ctime' ";
			}
			$sql .= " group by a.it_id ";
			$sql .= " order by a.ct_id ";
			$result = sql_query($sql);

			$it_send_cost = 0;

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

				if ($i==0) { // 계속쇼핑
					$continue_ca_id = $row['ca_id'];
				}

				$a1 = '<a href="./item.php?it_id='.$row['it_id'].'"><b>';
				$a2 = '</b></a>';
				$image = get_it_image($row['it_id'], 70, 70);

				$it_name = $a1 . stripslashes($row['it_name']) . $a2;
				$it_options = print_item_options($row['it_id'], $s_cart_id);
				if($it_options) {
					$mod_options = '<div class="sod_option_btn"><button type="button" class="mod_options">선택사항수정</button></div>';
					$it_name .= '<div class="sod_opt">'.$it_options.'</div>';
				}

				$item = sql_fetch("select * from {$g5['g5_shop_item_table']} where it_id='".$row[it_id]."' ");

				$auc_row1 = sql_fetch("select * from g5_shop_auction where it_id='".$row['it_id']."' order by no desc limit 0, 1 ");
				$my_auc_row = sql_fetch("select * from g5_shop_auction where it_id='".$row['it_id']."' and loginid='".$member[mb_id]."' order by no desc limit 0, 1 ");
				$my_max_auc_row = sql_fetch("select * from {$g5['g5_shop_auction_max_table']} where it_id='".$row['it_id']."' and loginid='".$member[mb_id]."' order by no desc limit 0, 1 ");

				if(strtotime($item[it_last_date]) < strtotime("now")){
					if($auc_row1[loginid] != $member[mb_id]){
						$sql = "delete from {$g5['g5_shop_cart_table']} where it_id='".$row[it_id]."' and mb_id='".$member[mb_id]."' ";
						sql_query($sql);
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

				// 배송비
				switch($row['it_sc_method'])
				{
					case 1:
						$ct_send_cost = "착불</br>(".number_format($item[it_sc_price]).")";
						//$ct_send_cost = get_sendcost1($s_cart_id, 0, "n");
						break;
					case 2:
						$ct_send_cost = '무료';
						break;
					default:
						$ct_send_cost = '선불</br>('.number_format($item[it_sc_price]).")";
						//$ct_send_cost = get_sendcost1($s_cart_id, 0, "n");
						break;
				}

				$point      = $sum['point'];
				//$sell_price = $sum['price'];
				$sell_price = $auc_row1[it_last_bid];


			?>

			<input type="hidden" name="ct_qty[<?php echo $row['it_id']; ?>]" id="ct_qty" value="<?php echo $sum['qty']; ?>" idx="<?php echo $row['it_id']; ?>" status="2" cart_id="<?=$s_cart_id?>">

			<tr>
				<td class="td_chk">
				<?
				if(strtotime($item[it_last_date]) < strtotime("now")){
					if($auc_row1[loginid] == $member[mb_id]){
				?>
					<input type="checkbox" name="ct_chk[<?php echo $k; ?>]" value="1" id="ct_chk_<?php echo $k; ?>" status="2" ct_send_cost="<?=$item[it_sc_price]?>" checked="checked">
				<?
					}
				}
				?>
				</td>
				<td style="color:#585858;text-align:center;font-size:12px;">
					<div>
						<?
						$auc_row = sql_fetch("select * from {$g5['g5_shop_item_table']} where it_id='".$row['it_id']."' ");
						if(strtotime($item[it_last_date]) < strtotime("now")){
						?>
							경매종료
						<?
						}else{
						?>
							경매중
						<?}?>
					</div>
					<div style="margin:10px 0 0 0;">
						<span style="color:#66d0cc;">종료일시</span></br>
						<?=date("Y/m/d", strtotime($item[it_last_date]))?></br>
						<?=date("H:i:s", strtotime($item[it_last_date]))?>
					</div>
				</td>
				<td class="sod_img"><?php echo $image; ?></td>
				<td class="right">
					<input type="hidden" name="it_id[<?php echo $k; ?>]"    value="<?php echo $row['it_id']; ?>">
					<input type="hidden" name="it_name[<?php echo $k; ?>]"  value="<?php echo get_text($row['it_name']); ?>">
					<?php// echo $it_name.$mod_options; ?>
					<div><?php echo get_text($row['it_name']); ?></div>

					<div class="pro_price" style="display:none;"><?php echo number_format($row['ct_price']); ?></div>
				</td>

				<td class="td_numbig right">
					<span>
						<?php
						if(strtotime($item[it_last_date]) < strtotime("now")){
							echo "-";
						}else{
							echo number_format($sell_price)."원";
						}
						?>
					</span>
					<span id="sell_price_<?php echo $k; ?>" class="sell_price sell_price<?php echo $row['it_id']; ?>" style="display:none;"><?php echo number_format($sell_price); ?>원</span>
				</td>
				<!--<td class="td_numbig"><?php// echo number_format($point); ?></td>-->
				<td class="right" style="text-align:center;font-size:12px;">
					<span style="color:#ff4c03;">최대입찰희망가</span></br>
					<span>
					<?
					if(strtotime($item[it_last_date]) < strtotime("now")){
						echo "-";
					}else{
						echo number_format($my_max_auc_row[it_max_bid])."원";
					}
					?>
					</span></br></br>
					<span style="color:#51b8ff;">내 입찰가</span></br>
					<span>

					<?
					if(strtotime($item[it_last_date]) < strtotime("now")){
						echo "-";
					}else{?>

						<?php echo number_format($my_auc_row[it_last_bid])."원";?>
						</span></br></br>
						<span style="border:1px #cfcfcf solid;text-align:center;padding:5px;cursor:pointer;" onclick="goto_url('<?=G5_SHOP_URL?>/auction.php?it_id=<?=$row[it_id]?>');">추가입찰하기</span>
					<?
					}
					?>
				</td>
				<td class="td_num right">
					<?
					if(strtotime($item[it_last_date]) < strtotime("now")){
						if($auc_row1[loginid] == $member[mb_id]){
					?>
						낙찰
					<?
						}
					}else{
					?>
						입찰중
					<?}?>
				</td>
				<td class="right" style="text-align:center;">
					<?
					if(strtotime($item[it_last_date]) < strtotime("now")){
						if($auc_row1[loginid] == $member[mb_id]){
							echo number_format($auc_row1[it_last_bid])."원";
						}
					}else{
						echo "0원";
					}
					?>
				</td>
				<td class="td_num" style="line-height:24px;">
					<?
					if(strtotime($item[it_last_date]) < strtotime("now")){
						if($auc_row1[loginid] == $member[mb_id]){
					?>
						<img src="img/cart_buy_bn.gif" align="absmiddle" class="cart_buy" style="cursor:pointer;"></br>
					<?
						}
					}
					?>
					<a href="javascript:item_wish(document.fwish, '<?php echo $row['it_id']; ?>');" id="sit_btn_wish"><img src="img/cart_wishlist_bn.gif" align="absmiddle" style="border:0px;cursor:pointer;"></a></br>
				</td>
			</tr>

			<?php
				$tot_point      += $point;
				$tot_sell_price += $sell_price;
				$send_cost1 += $item[it_sc_price];

				$k++;
			} // for 끝

			if ($i == 0) {
				echo '<tr><td colspan="8" class="empty_table">장바구니에 담긴 상품이 없습니다.</td></tr>';
				$send_cost1_1 = 0;
				$send_cost_1 = 0;
			} else {
				// 배송비 계산
				$send_cost1 = get_sendcost1($s_cart_id, 0, "y");
				$send_cost = get_sendcost($s_cart_id, 0);

				if($k > 1){
					$cost = 3500;
				}else{
					$cost = $send_cost1;
				}
			}
			?>
			<tr>
				<td colspan="9" style="border:0px;">
					1. <b>"최대희망입찰가"</b> 자율적으로 금액을 입력하여 경매에 참여하는 것입니다.</br>
					2. <b>"추가입찰하기"</b> 버튼을 누르면 정해진 금액이 자동으로 추가되어 입찰을 하게 됩니다. (추가입찰의 경우, 경매상품의 마지막 입찰자가 됩니다.)</br>
					3. 경매중인 상태에서만 <b>"추가입찰"</b>이 가능합니다.(경매중에만 추가입찰하기 버튼이 생성됩니다.)</br>
					4. 낙찰이 되시면 <b>"구매가능"</b> 버튼이 생성됩니다.</br>
					5. 경매가 종료 되고 <b>낙찰이 되지 않았을 경우</b> 쇼핑카트에서 자동 삭제 되어집니다.
				</td>
			</tr>
			</tbody>
			</table>
		</div>

		<!-- 상품 선택 버튼 -->
		<div class="pro_choi_bn">
			<ul>
				<li class="pro_all_chk" idx="2">상품 전체선택</li>
				<li class="pro_all_rel" idx="2">상품 선택해제</li>
				<li onclick="return form_check('seldelete2', '2');">선택상품 삭제</li>
			</ul>
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
					<td><div class="price_box"><span class="price"><?=number_format($cost)?></span><span style="color:#818181;font-weight:normal;font-size:14px;">원</span></div></td>
					<td><div class="price_box all"><span class="price">0</span><span style="font-weight:normal;font-size:14px;">원</span></div></td>
				</tr>
			</table>
		</div>

	</div>
</div>

<!-- 총가격 정보 -->
<div class="all_price_info">
	<table border="0" cellspacing="0" cellpadding="0" width="100%" class="all_price_info_tb">
		<tr>
			<td width="96px" style="font-size:17px;color:#545454;">총합계</td>
			<td width="186px">
				<table border="0" cellspacing="0" cellpadding="0" width="100%" class="tb">
					<tr height="32px">
						<td>상품금액</td>
					</tr>
					<tr height="53px">
						<td>
							<div style="margin:-12px 0 0 5px;text-align:left;">일반상품</div>
							<div style="margin:0 20px 0 0;text-align:right;"><span class="price_all">0</span><span>원</span></div>
						</td>
					</tr>
					<tr height="60px">
						<td>
							<div style="margin:-12px 0 0 5px;text-align:left;">경매상품</div>
							<div style="margin:0 20px 0 0;text-align:right;"><span class="price_all_auc">0</span><span>원</span></div>
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
						<td class="right_aln"><span class="price_all">0</span><span>원</span></td>
					</tr>
					<tr height="60px">
						<td class="right_aln"><span class="price_all_auc">0</span><span>원</span></td>
					</tr>
				</table>
			</td>
			<td width="147px">
				<table border="0" cellspacing="0" cellpadding="0" width="100%" class="tb">
					<tr height="32px">
						<td>배송비</td>
					</tr>
					<tr height="113px">
						<td class="right_aln" style="padding-bottom:10px;"><span class="price_all_cost"><?=number_format($cost1 + $cost)?></span><span>원</span></td>
					</tr>
				</table>
			</td>
			<td>
				<table border="0" cellspacing="0" cellpadding="0" width="100%" class="tb">
					<tr height="32px">
						<td>총결제예상금액</td>
					</tr>
					<tr height="113px">
						<td class="right_aln">
							<span class="price_all_price"><?=number_format($tot_sell_price + $send_cost)?></span>
							<span style="font-weight:normal;font-size:14px;color:#ff4e00;">원</span>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>

<!--
<?php
//$tot_price = $tot_sell_price + $send_cost; // 총계 = 주문상품금액합계 + 배송비
//if ($tot_price > 0 || $send_cost > 0) {
?>
<dl id="sod_bsk_tot">
	<?php// if ($send_cost > 0) { // 배송비가 0 보다 크다면 (있다면) ?>
	<dt class="sod_bsk_dvr">배송비</dt>
	<dd class="sod_bsk_dvr"><strong><?php// echo number_format($send_cost); ?> 원</strong></dd>
	<?php// } ?>

	<?php
	//if ($tot_price > 0) {
	?>

	<dt class="sod_bsk_cnt">총계 가격/포인트</dt>
	<dd class="sod_bsk_cnt"><strong><?php// echo number_format($tot_price); ?> 원 / <?php// echo number_format($tot_point); ?> 점</strong></dd>
	<?php// } ?>

</dl>
<?php// } ?>
-->


<div id="sod_bsk_act">
	<?php if ($k == 0) { ?>
	<a href="<?php echo G5_SHOP_URL; ?>/list.php?ca_id=1010"><img src="<?G5_URL?>/shop/img/cart_shop_bn.gif" align="absmiddle" style="border:0;" onclick="return form_check('buy');"></a>
	<?php } else { ?>
	<input type="hidden" name="url" value="./orderform.php">
	<input type="hidden" name="records" value="<?php echo $k; ?>">
	<input type="hidden" name="act" value="">
	<input type="hidden" name="no" value="">
	<img src="<?G5_URL?>/shop/img/cart_buy_all_bn.gif" align="absmiddle" style="border:0;cursor:pointer;" onclick="return form_check('buy');">
	<a href="<?php echo G5_SHOP_URL; ?>/list.php?ca_id=<?php echo $continue_ca_id; ?>"><img src="<?G5_URL?>/shop/img/cart_shop_bn.gif" align="absmiddle" style="border:0;" onclick="return form_check('buy');"></a>
	<!--<a href="<?php// echo G5_SHOP_URL; ?>/list.php?ca_id=<?php echo $continue_ca_id; ?>" class="btn01">쇼핑 계속하기</a>
	<button type="button" onclick="return form_check('buy');" class="btn_submit">주문하기</button>
	<button type="button" onclick="return form_check('seldelete');" class="btn01">선택삭제</button>
	<button type="button" onclick="return form_check('alldelete');" class="btn01">비우기</button>-->
	<?php } ?>
</div>

</form>

<form name="fwish" id="fwish" method="POST">
<input type="hidden" name="url" id="url" value="">
<input type="hidden" name="it_id" id="it_id" value="">
<input type="hidden" name="ct_qty" id="ct_qty" value="">
</form>

<form name="fdel" id="fdel" method="POST">
<input type="hidden" name="url" id="url" value="">
<input type="hidden" name="it_id" id="it_id" value="">
<input type="hidden" name="act" id="act" value="">
</form>

<script>
$(function() {
    var close_btn_idx;
	var idx, status, old_cnt, cnt, pro_price, price1_1, price1, send_cost, cart_id, price3, cost, cost1, cost2, all_price;

	<?if($send_cost){?>
		send_cost = <?=$send_cost?>;
	<?}else{?>
		send_cost = 0;
	<?}?>

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
				$(".product2").find("input[name^=ct_chk]").each(function(i){
					price1_1 = removeComma($(".product2").find(".sell_price").eq(i).html());
					price1 = parseInt(price1_1) + parseInt(price1);
				});

				if(k > 1){
					$(".price_info" + status).find(".price").eq(2).html(commaNum(3500));
				}else{
					$(".price_info" + status).find(".price").eq(2).html(commaNum(cost));
				}

				$(".price_info2").find(".price").eq(0).html(commaNum(price1));
				$(".all_price_info").find(".price_all_auc").eq(0).html(commaNum(price1));
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

			$(".price_all_price").html(commaNum(parseInt(removeComma($(".price_all").html())) + parseInt(removeComma($(".price_all_auc").html())) + parseInt(removeComma($(".price_all_cost").html()))));

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

	$(".product2").find("input[name^='ct_chk']").each(function(i){
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

	$(".price_all_price").html(commaNum(parseInt(removeComma($(".price_all").html())) + parseInt(removeComma($(".price_all_auc").html())) + parseInt(removeComma($(".price_all_cost").html()))));

	$(".cnt_p").click(function(){
		idx = $(this).attr("idx");
		status = $(this).attr("status");
		old_cnt = $(this).parent().parent().find(".ct_cnt").find("input:text[name='ct_qty["+idx+"]']").val();
		cnt = parseInt(old_cnt) + 1;
		pro_price = removeComma($(this).parent().parent().parent().find(".pro_price").html());
		price1_1 = 0;
		price1 = 0;
		price3 = 0;
		cart_id = $(this).attr("cart_id");

		pro_price = pro_price * cnt;

		cart_cnt(this, idx, old_cnt, cnt, pro_price, price1_1, price1, cart_id, status);

	});

	$(".cnt_m").click(function(){
		idx = $(this).attr("idx");
		status = $(this).attr("status");
		old_cnt = $(this).parent().parent().find(".ct_cnt").find("input:text[name='ct_qty["+idx+"]']").val();
		cnt = parseInt(old_cnt) - 1;
		pro_price = removeComma($(this).parent().parent().parent().find(".pro_price").html());
		price1_1 = 0;
		price1 = 0;
		cart_id = $(this).attr("cart_id");

		pro_price = pro_price * cnt;

		if(cnt < 1){
			alert("최소 구매 수량은 1 입니다.");
			return false;
		}

		cart_cnt(this, idx, old_cnt, cnt, pro_price, price1_1, price1, cart_id, status);

	});

	$("input[name^=ct_qty]").keyup(function(){
		idx = $(this).attr("idx");
		status = $(this).attr("status");
		old_cnt = $(this).val();
		cnt = parseInt(old_cnt);
		pro_price = removeComma($(this).parent().parent().parent().find(".pro_price").html());
		price1_1 = 0;
		price1 = 0;
		cart_id = $(this).attr("cart_id");

		pro_price = pro_price * cnt;

		if(old_cnt < 1){
			alert("최소 구매 수량은 1 입니다.");
			$(this).val(1);
			return false;
		}

		$(this).parent().parent().parent().find(".sell_price" + idx).html(commaNum(pro_price));

		if(status == 1){
			$(".product1").find(".sell_price").each(function(i){
				price1_1 = removeComma($(".product1").find(".sell_price").eq(i).html());
				price1 = parseInt(price1_1) + parseInt(price1);
			});

			$(".price_info1").find(".price").eq(0).html(price1);
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
	});

	$(".cart_buy").click(function(){
		var f = document.frmcartlist;
		var idx = $(".cart_buy").index(this);

		$("input:checkbox[name^='ct_chk']").each(function(i){
			if(idx == i){
				$("input:checkbox[name^='ct_chk']").eq(i).attr("checked", true);
			}else{
				$("input:checkbox[name^='ct_chk']").eq(i).attr("checked", false);
			}
		});

		f.act.value = "buy";
        f.submit();
	});

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

// 상품보관
function item_wish(f, it_id)
{
	f.url.value = "<?php echo G5_SHOP_URL; ?>/wishupdate.php?it_id="+it_id;
	f.it_id.value = it_id;
	f.ct_qty.value = $("input[name='ct_qty["+it_id+"]']").val();
	f.action = "<?php echo G5_SHOP_URL; ?>/wishupdate.php";
	f.submit();
}

function form_check(act, num, idx) {

    var f = document.frmcartlist;
    var cnt = f.records.value;
	var data = "";

    if (act == "buy")
    {
        if($("input[name^=ct_chk]:checked").size() < 1) {
            alert("주문하실 상품을 하나이상 선택해 주십시오.");
            return false;
        }

        f.act.value = act;
        f.submit();
    }
    else if (act == "alldelete")
    {
        f.act.value = act;
        f.submit();
    }
    else if (act == "seldelete1")
    {

        if($("input[name^=ct_chk]:checked").size() < 1) {
            alert("삭제하실 상품을 하나이상 선택해 주십시오.");
            return false;
        }

		$(".product1").find("input[name^=ct_chk]").each(function(i){

			if($(".product1").find("input[name^=ct_chk]").eq(i).is(":checked") == true){
				data += $(".product1").find("input[name^=it_id]").eq(i).val() + "|";
			}
		});

		data = data.substr(0, data.length - 1);


		f.no.value = data;
        f.act.value = act;
        f.submit();
    }
	else if (act == "seldelete2")
    {
        if($("input[name^=ct_chk]:checked").size() < 1) {
            alert("삭제하실 상품을 하나이상 선택해 주십시오.");
            return false;
        }

		$(".product2").find("input[name^=ct_chk]").each(function(i){

			if($(".product2").find("input[name^=ct_chk]").eq(i).is(":checked") == true){
				data += $(".product2").find("input[name^=it_id]").eq(i).val() + "|";
			}
		});

		data = data.substr(0, data.length - 1);

		f.no.value = data;
        f.act.value = act;
        f.submit();
    }
	else if(act == "delete")
	{
		if(confirm("정말 삭제 하시겠습니까?")){
			document.fdel.url.value = "<?=G5_URL?>/shop/cart.php";
			document.fdel.act.value = act;
			document.fdel.it_id.value = idx;
			document.fdel.action = "./cartupdate.php";
			document.fdel.submit();
		}
	}

    return true;
}

</script>
<!-- } 장바구니 끝 -->

<?php
include_once('./_tail.php');
?>