<?php
include_once('./_common.php');
$g5['title'] = '장바구니';
include_once('./_head.php');


//PC웹&모바일 장바구니 정보 SQL
$cart_sql = "	SELECT	T.gp_id,
						          T.ca_id,
                      T.ca_id2,
                      T.ca_id3,
                      T.event_yn,
                      T.gp_name,
                      T.gp_site,
                      T.gp_img,
                      T.gp_explan,
                      T.gp_objective_price,
                      T.gp_have_qty,
                      T.gp_buy_min_qty,
                      T.gp_buy_max_qty,
                      T.gp_charge,
                      T.gp_duty,
                      T.gp_use,
                      T.gp_order,
                      T.gp_stock,
                      T.gp_time,
                      T.gp_update_time,
                      T.gp_price,
                      T.gp_price_org,
                      T.gp_card_price,
                      T.gp_price_type,
                      T.gp_metal_type,
                      T.gp_metal_don,
                      T.gp_metal_etc_price,
                      T.gp_sc_method,
                      T.gp_sc_price,
                      T.it_type,
                      T.gp_type1,
                      T.gp_type2,
                      T.gp_type3,
                      T.gp_type4,
                      T.gp_type5,
                      T.gp_type6,
                      IF(T.real_jaego > 0,T.real_jaego,0) AS real_jaego,
                      CASE
                        WHEN	T.ca_id LIKE 'CT%' || T.ca_id = 'GP'	THEN
                          CASE
                            WHEN	T.gp_price_type = 'Y'	THEN	/*실시간스팟시세*/
                              CEIL(T.gp_realprice / 100) * 100
                            WHEN	T.gp_price_type = 'N'	THEN	/*고정가달러환율*/
                              CEIL(T.gp_fixprice / 100) * 100
                            WHEN	T.gp_price_type = 'W'	THEN	/*원화적용*/
                              T.gp_price
                            ELSE
                              0
                          END
                        ELSE
                          CEIL(IFNULL(T.po_cash_price,T.gp_price) / 100) * 100
                      END po_cash_price,
                      T.number,
                      T.it_id,				/*상품코드*/
                      T.mb_id,				/*계정 또는 세션아이디*/
                      T.gpcode,				/*연결된 공구코드*/
                      T.it_qty,				/*상품수량*/
                      T.it_name,			/*상품명*/
                      T.stats,				/*상태값*/
                      T.reg_date,			/*등록일시*/
                      CA.ca_name,
                      GI.gpcode_name
              FROM		$sql_cartproduct
                    LEFT JOIN g5_shop_category CA ON (CA.ca_id = T.ca_id)
                    LEFT JOIN gp_info GI ON (GI.gpcode = T.gpcode)
";
$result = sql_query($cart_sql);

if($mode == 'jhw') {
	echo $cart_sql;
}

//장바구니 페이지
if (G5_IS_MOBILE) {
    include_once('./m/cart.php');
    exit;
}
?>

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
<div class="cart_title"><?=$g5['title']?></div>

<!-- 탭메뉴 -->
<form name="coto_cart" id="coto_cart" method="post" action="cart.update.php">
<input type="hidden" id='mode' name="mode" value="UPDATE">


<div class="list_box">

	<div id="sod_bsk">

		<div class="tbl_head01 tbl_wrap product1">
			<table>
				<thead>
				<tr>
					<th scope="col"><input type="checkbox" class='ct_all' name="ct_all" value="1" id="ct_all" checked="checked" idx="1" status="1"></th>
					<th scope="col" class="right" colspan="2">상품정보</th>
					<th scope="col" class="right" width="90px">수량</th>
					<th scope="col" class="right">주문금액</th>
				</tr>
				</thead>
				<tbody>

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
	?>

			<tr>
				<td class="td_chk">
					<input type="hidden" id="gpcode_<?=$i?>" name="gpcode[<?=$i?>]"   value="<?=$row['gpcode']?>">
					<input type="hidden" id="itid_<?=$i?>" name="it_id[<?=$i?>]"    value="<?=$row['it_id']?>">
					<input type="hidden" id="itname_<?=$i?>" name="it_name[<?=$i?>]"  value="<?=get_text($row['it_name'])?>">
					
					<input type="checkbox" id="ct_chk_<?=$i?>" class='ct_chk' name="ct_chk[<?=$i?>]" value="1" ct_send_cost="<?=$item[it_sc_price]?>" status="1" checked="checked">
				</td>

				<td title='이미지' class="sod_img">
					<div class='imgLiquidNoFill imgLiquid' style='float:left;width:70px;height:70px;padding:0 15px 0 15px;'>
						<a <?="href=\"".G5_SHOP_URL."/grouppurchase.php?gpcode=".$row[gpcode]."&gp_id=".$row[it_id]."&ca_id=".$row[ca_id]."\" class=\"sct_a sct_img\""?>>
							<?=$이미지?>
						</a>
			 		</div>
				</td>

				<td title='상품명' class="right">
					<a <?="href=\"".G5_SHOP_URL."/grouppurchase.php?gpcode=".$row[gpcode]."&gp_id=".$row[it_id]."&ca_id=".$row[ca_id]."\" class=\"sct_a sct_img\""?>>
						<?=$상품명?>
						<div style="margin:7px 0 0 0;">
							<div style="float:left;width:50%; text-align:left;">예상재고수량 <?=number_format($예상재고수량)?>ea</div>
							<div style="float:left;width:50%; text-align:right;">판매가 <?=number_format($상품판매가)?>원</div>
						</div>
					</a>
				</td>

				<td title='수량' class="td_num right" align="center">
					<input type="text" name="it_qty[<?=$i?>]" value="<?=$row[it_qty]?>" style='width:30px; text-align:right;'>
					<div class="pro_choi_bn" style="width:70px;">
						<ul>
							<li class="pro_all_btn_upd" onclick="submitCartItem('CHK_UPDATE','<?=$i?>')" style="padding:3px;">수정</li>
							<li class="pro_all_btn_del" onclick="submitCartItem('CHK_DELETE','<?=$i?>')" style="padding:3px;">삭제</li>
						</ul>
					</div>
				</td>

				<td class="td_numbig right">
					<span id="sell_price_<?=$i?>" class="sell_price sell_price<?=$row['it_id']?>">
						<?=number_format($주문금액)?> 원
					</span>
				</td>
			</tr>

			<?
			} // for 끝

			if ($i == 0) {
				echo '<tr><td colspan="8" class="empty_table">장바구니에 담긴 상품이 없습니다.</td></tr>';
			} else {
				$총무게kg = $총무게 / 1000;
				$누적배송비 = getDeliveryPricePerKg($총무게kg);
				$총결제예상금액 = $누적배송비 + $총상품금액;
			}

			?>
			</tbody>
			</table>
		</div>
		

		<!-- 상품 선택 버튼 -->
		<div class="pro_choi_bn">
			<ul>
				<li class="pro_all_chk" idx="1">전체선택</li>
				<li class="pro_all_rel" idx="1">선택해제</li>
				<li class="pro_all_btn_del" onclick="return submitCart('CHK_DELETE')">삭제</li>
			</ul>
		</div>

	</div>
</div>


<!-- 총가격 정보 -->
<div class='totalpriceinfo'>

	<table width='100%'>
		<tr>
			<td rowspan='2' width='96' style='text-align:center; padding:0px;'><b1>총합계</b1></td>
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
	<input type='button' onclick="orderCart()" value='주문하기' id='sit_btn_buy2' style='width:120px; height:50px; font-size:1.2em; font-weight:bold;'>
	<a href="<?=G5_SHOP_URL?>/list.php?ca_id=<?=$continue_ca_id?>"><img src="<?G5_URL?>/shop/img/cart_shop_bn.gif" align="absmiddle" style="border:0;" onclick="return form_check('buy');"></a>
</div>

</form>

<script>
	//체크박스전체선택
	// 모두선택
	$(".ct_all").click(function() {

		//체크되있으면
		if($(this).is(":checked") == true) {

			$(".ct_chk").each(function(){
				this.checked = true;
			});

		}
		else {

			$(".ct_chk").each(function(){
				this.checked = false;
			});

		}

	});

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

<?
include_once('./_tail.php');
?>