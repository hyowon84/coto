<?php
include_once('./_common.php');

if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

include_once('./_head.php');

$od_id = $_GET[od_id];
$it_id = $_GET[it_id];
?>

<div id="aside2"></div>

<!-- 교환요청 시작 { -->

<?include_once("../inc/mypage_menu.php");?>

<div style="background:#fff;">
	

	<?include_once("../inc/mypage_submenu.php");?>

	<div id="my_ex_guide">
		<div style="width:272px;height:18px;font-size:14px;color:#fff;padding:2px 7px 2px 7px;background:#56ccc8;">투데이 스토어 상품만 반품/교환이 가능합니다.</div>
		<div class="cl" style="margin:10px 0 0 0;padding:0 0 0 7px;">
			반품/교환신청은 "배송완료" 후 30일 이내만 가능합니다.</br>
			단, 사전에 반품기간을 달리 정하여 이용자에게 고지한 경우에는 그 기간내에 반품을 신청할 수 있습니다.</br>
			추가로 구매한 상품만을 반품/교환하는 경우 추가 비용이 발생할 수 있습니다.
		</div>
	</div>

	<div id="my_ex_step">
		<div><img src="<?=G5_URL?>/img/my_re_step1.gif" border="0" align="absmiddle"></div>
		<div><img src="<?=G5_URL?>/img/my_re_step2.gif" border="0" align="absmiddle"></div>
		<div><img src="<?=G5_URL?>/img/my_re_step3_o.gif" border="0" align="absmiddle"></div>
	</div>
	
	<div style="margin:30px 0 0 0;padding:0 0 0 15px;font-size:14px;font-weight:bold;color:#545454;">
		택배기사님이 방문할 수거지 정보를 입력하여 주십시오.
	</div>

	<form name="fitemexchange" id="fitemexchange" method="POST">
	<input type="hidden" name="HTTP_CHK" value="CHK_OK">
	<input type="hidden" name="ex_status" value="<?=$_POST[ex_status]?>">
	<input type="hidden" name="ex_con" value="<?=$_POST[ex_con]?>">
	<!-- } 교환요청 끝 -->


	<div id="my_ex_tb" style="margin:5px 0 0 0;border-top:2px #545454 solid;">
		<table border="0" cellspacing="0" cellpadding="0" width="100%">
			<tr height="60px">
				<td style="width:120px;text-align:center;font-size:16px;vertical-align:bottom;"><b>주문하신 상품이 <font color="#ff4e00">반품요청완료</font> 되었습니다.</b></td>
			</tr>
			<tr height="70px">
				<td style="text-align:center;font-size:11px;">반품진행 과정은 교환/반품 현황조회에서 조회가 가능합니다.</td>
			</tr>
		</table>
	</div>

	<div style="font-size:12px;padding:0 0 0 15px;">주문번호 <span style="color:#2977ab;"><?=$od_id?></span></div>
	<div style="margin:10px 0 0 0;font-weight:bold;">
		<div class="tbl_head01 tbl_wrap" style="width:100%;">
			<table>
			<thead>
			<tr>
				<th scope="col" class="right" colspan="2">상품정보</th>
				<th scope="col" class="right" width="70px">수량</th>
				<th scope="col" class="right">상품금액</th>
				<th scope="col" class="right">할인</th>
				<th scope="col" class="right">배송</th>
			</tr>
			</thead>
			<tbody>
			<?php
			$row = sql_fetch("select * from {$g5['g5_shop_cart_table']} where od_id='".$od_id."' and it_id='".$it_id."' ");

			// 합계금액 계산
			$sql = " select SUM(IF(io_type = 1, (io_price * ct_qty), ((ct_price + io_price) * ct_qty))) as price,
							SUM(ct_point * ct_qty) as point,
							SUM(ct_qty) as qty
						from {$g5['g5_shop_cart_table']}
						where it_id = '{$row['it_id']}'
						  and od_id = '$od_id' ";
			$sum = sql_fetch($sql);

			if ($i==0) { // 계속쇼핑
				$continue_ca_id = $row['ca_id'];
			}

			$a1 = '<a href="./grouppurchase.php?it_id='.$row['it_id'].'"><b>';
			$a2 = '</b></a>';
			$image = get_it_image($row['it_id'], 70, 70);

			$it_name = $a1 . stripslashes($row['it_name']) . $a2;
			$it_options = print_item_options($row['it_id'], $s_cart_id);
			if($it_options) {
				$mod_options = '<div class="sod_option_btn"><button type="button" class="mod_options">선택사항수정</button></div>';
				$it_name .= '<div class="sod_opt">'.$it_options.'</div>';
			}

			$item = sql_fetch("select * from {$g5['g5_shop_item_table']} where it_id='".$row[it_id]."' ");

			// 배송비
			switch($row['it_sc_method'])
			{
				case 1:
					$ct_send_cost = number_format($item[it_sc_price]);
					break;
				case 2:
					$ct_send_cost = 0;
					break;
				default:
					$ct_send_cost = number_format($item[it_sc_price]);
					break;
			}

			$point      = $sum['point'];
			$sell_price = $sum['price'];
			?>

			<tr>
				<td class="sod_img"><?php echo $image; ?></td>
				<td class="right">
					<div><?php echo get_text($row['it_name']); ?></div>
					<div style="margin:7px 0 0 0;">
						<div style="float:left;width:50%;text-align:right;">판매가</div>
						<div class="pro_price"><?php echo number_format($row['ct_price']); ?></div>
					</div>
				</td>
				<td class="td_num right" align="center">
					<?php echo $sum['qty']; ?>
				</td>
				<td class="td_numbig right"><?php echo number_format($sell_price); ?></td>
				<td class="td_num right">-</td>
				<td class="td_num right"><?php echo $ct_send_cost; ?></td>
			</tr>

			</tbody>
			</table>
		</div>
	</div>

	<div id="my_ex_bn">
		<img src="<?=G5_SHOP_URL?>/img/cart_shop_bn.gif" border="0" align="absmiddle" onclick="goto_url('<?=G5_SHOP_URL?>/');">
	</div>

	</form>

</div>

