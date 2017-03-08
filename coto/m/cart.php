<style>
	ff4e00 { color:#ff4e00; }
	.totalpriceinfo b1 { font-size:1.4em; font-weight:bold; }
	.totalpriceinfo table { border:6px solid #545454; border-collapse: collapse; border-spacing: 0; background-color: white; }
	.totalpriceinfo table th, .totalpriceinfo table td { border:1px solid #cfcfcf; }

	.totalpriceinfo table th { height:32px; background-color: #f9f9f9; }
	.totalpriceinfo table td { height:53px; font-size:1.2em; font-weight:bold; text-align:right; padding-right:10px; }

	.totalpriceinfo .totalpricefont { color: #ff4e00; }
</style>


<!-- 장바구니 시작 { -->
<script src="<?=G5_JS_URL?>/common_order.js"></script>
<script src="<?=G5_JS_URL?>/shop.js"></script>
<script src="<?=G5_JS_URL?>/imgLiquid.js"></script>
<script>
$(document).ready(function() {
	$(".imgLiquidNoFill").imgLiquid({fill:false});
});
</script>

<!-- 타이틀 -->
<div class="cart_title" style='font-size:1.3em; font-weight:bold; margin-bottom:20px;'><?=$g5['title']?></div>

<!-- 탭메뉴 -->
<form name="coto_cart" id="coto_cart" method="post" action="cart.update.php">
<input type="hidden" id='mode' name="mode" value="UPDATE">
<input type="hidden" id='del_it_id' name="del_it_id" value="">

<ul class="product1">
<?
$tot_point = 0;
$tot_sell_price = 0;
$send_cost1 = 0;

for ($i=0; $row=mysql_fetch_array($result); $i++)
{
	$총무게 += ($row[gp_metal_don] * $row[it_qty] * 31.1035);
	$상품명 = "<b>[$row[gpcode_name]][$row[it_id]]</b><br>$row[gp_name]";
	$이미지 = "<img src='$row[gp_img]' />";
	$상품판매가 = $row[po_cash_price];
	$주문금액 = $row[it_qty] * $row[po_cash_price];
	$예상재고수량 = $row[real_jaego];
	$총상품금액 += $주문금액;

	/* http 가 들어간건 다이렉트로, 아닌건 get_it_thumb함수로 */
	if( strstr($row[gp_img],'http')) {
		$image = "<img src='$row[gp_img]' width=$default[de_mimg_width] />";
	}
	else {
		$image = get_it_thumbnail1($row[gp_img],$default['de_mimg_width'],$default['de_mimg_height'], '', 1);
	}
	?>
	<li class="rtLi">
		<div class="productName line1row taCenter">
			<input type="hidden" name="gpcode[<?=$i?>]"    value="<?=$row['gpcode']?>">
			<input type="hidden" name="it_id[<?=$i?>]"    value="<?=$row['it_id']?>">
			<input type="hidden" name="it_name[<?=$i?>]"  value="<?=get_text($row['it_name'])?>">
			<input type="checkbox" id="ct_chk_<?=$i?>" class='ct_chk' name="ct_chk[<?=$i?>]" value="1" ct_send_cost="<?=$item[it_sc_price]?>" status="1" checked="checked">
			<b><a <?="href=\"".G5_SHOP_URL."/grouppurchase.php?gpcode=".$row[gpcode]."&gp_id=".$row[it_id]."&ca_id=".$row[ca_id]."\" class=\"sct_a sct_img\""?>><?=stripslashes($상품명)?></a></b>
		</div>
		<div class="productImg"><?=$image?></div>
		<div class="productInfo">
			<div class="price"><label>판매가</label><p><?=number_format($상품판매가)?>원</p></div>
			<div class="qty"><label>예상재고수량</label><p><?=number_format($예상재고수량)?>ea</p></div>
			<div class="qty"><label>담은수량</label><p>
				<input type="text" name="it_qty[<?=$i?>]" value="<?=$row[it_qty]?>" style='border:1px solid gray; width:30px; text-align:right;'> ea
			</div>
			<div style='width:90px; margin:0px auto; margin-top:5px;'>
				<input type='button' onclick="submitCartItem('CHK_UPDATE','<?=$i?>')" value='변경' id='sit_btn_buy2' style='width:40px; height:25px; font-size:1.2em; font-weight:bold;'>
				<input type='button' onclick="submitCartItem('CHK_DELETE','<?=$i?>')" value='삭제' id='sit_btn_buy2' style='width:40px; height:25px; font-size:1.2em; font-weight:bold; margin-left:5px;'>
			</div>
		</div>
	</li>
<?
} // for 끝

if ($i == 0) {

	echo '<li style="text-align:center; font-size:1.2em; font-weight:bold; padding:30px;">장바구니에 담긴 상품이 없습니다.</li>';

} else {

	$총무게kg = $총무게 / 1000;
	$누적배송비 = getDeliveryPricePerKg($총무게kg);
	$총결제예상금액 = $누적배송비 + $총상품금액;

?>

<!-- 상품 선택 버튼 -->
<div class="pro_choi_bn" style="width:291px; margin:0px auto;">
	<ul>
		<li class="pro_all_chk">전체선택</li>
		<li class="pro_all_rel" onclick="">선택해제</li>
		<li onclick="return submitCart('CHK_DELETE')">삭제</li>
	</ul>
</div>

<?
}
?>
</ul>

<script>
	//상품 전체선택
	$(".pro_all_chk").click(function(){
		$(".product1").find("input[name^=ct_chk]").attr("checked", true);
		$("input[name=ct_all]").eq(n).attr("checked", true);
	});

	//상품 선택해제
	$(".pro_all_rel").click(function(){
		$(".product1").find("input[name^=ct_chk]").attr("checked", false);
		$("input[name=ct_all]").eq(n).attr("checked", false);
	});
</script>

<!-- 총가격 정보 -->
<div class='totalpriceinfo' style='margin-bottom:20px;'>
	<table width='100%'>
		<tr>
			<td rowspan='2' width='96' style='text-align:center; padding:0px;'><b1>총<br>합<br>계</b1></td>
			<th width='186'>상품금액</td>
			<th width='147'>배송비</td>
			<th width='234'>총결제예상금액</th>
		</tr>
		<tr>
			<td><?=number_format($총상품금액)?>원</td>
			<td><?=number_format($누적배송비)?>원 (<?=number_format($총무게kg,2)?>kg)</td>
			<td><ff4e00><?=number_format($총결제예상금액)?>원</ff4e00></td>
		</tr>
	</table>
</div>

<div id="sod_bsk_act">


<?
	if ($i != 0) {
?>
	<input type='button' onclick="orderCart()" value='주문하기' id='sit_btn_buy2' style='width:120px; height:50px; font-size:1.2em; font-weight:bold;'>
<?
	}
?>
	<a href="/"><img src="<?G5_URL?>/shop/img/cart_shop_bn.gif" align="absmiddle" style="border:0;" onclick="return form_check('buy');"></a>
</div>
</form>

<?php
include_once('./_tail.php');
?>