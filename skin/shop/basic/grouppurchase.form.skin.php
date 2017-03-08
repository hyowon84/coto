<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가



// $gp_code = getPurchaseBuyCode($it['ca_id']);

/* 신청수량 합계 */
// $cnt_sql = "	SELECT	SUM(ct_qty) as ct_qty
// 							FROM		g5_shop_cart
// 							WHERE		it_id='$gp_id'
// 							AND		ct_gubun='P'
// 							AND		total_amount_code = '$gp_code'
// 							AND		ct_status IN ('쇼핑','입금완료') ";
// $cnt = sql_fetch($cnt_sql);
//echo $cnt_sql;

if($cnt[ct_qty]){
	$ct_qty = "현재 주문 수량 : ".$cnt[ct_qty];
}else{
	$cnt[ct_qty] = 0;
}

$po_qty_op = sql_fetch("select * from {$g5['g5_shop_group_purchase_option_table']} where gp_id='".$gp_id."' AND po_sqty <= '".$cnt[ct_qty]."' AND po_eqty >= '".$cnt[ct_qty]."' ");

$it[it_price] = ceil($it[po_cash_price] / 100) * 100;
$it[it_card_price] = ceil($it[po_cash_price] * 1.03 / 100) * 100;

$현금가 = $it[it_price];
$카드가 = $it[it_card_price];
?>

<link rel="stylesheet" href="<?=G5_SHOP_SKIN_URL; ?>/style.css">
<script type="text/javascript" src="<?=G5_URL?>/js/jquery.loupe.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){

	$(".loupe").find("img").css({"width":"500px", "height":"500px"});

});
</script>
<script type="text/javascript" src="<?=G5_URL?>/js/common_product.js"></script>

<form name="fitem" method="post" action="<?=$action_url; ?>" onsubmit="return fitem_submit(this);">
<input type="hidden" name="gp_id" value="<?=$gp_id; ?>">
<input type="hidden" name="it_id" value="<?=$gp_id; ?>">
<input type="hidden" name="sw_direct">
<input type="hidden" name="url">
<input type="hidden" name="ca_id" value="<?=$ca_id?>">
<input type="hidden" name="buy_kind" value="공동구매">
<input type="hidden" name="ct_send_cost" id="ct_send_cost" value="<?=$it[gp_sc_price]?>">

<div id="sit_ov_wrap">
	<!-- 상품이미지 미리보기 시작 { grouppurchase.form.skin (P) -->
	<div id="sit_pvi">
		 <h2 id="sit_title"><?=stripslashes($it['gp_name']); ?> <span class="sound_only">요약정보 및 구매</span></h2>
		<div id="sit_pvi_big" style="margin:0;">
			<img src='<?=$it[gp_img]?>' width='400' class="demo">
		</div>
		<script type="text/javascript">
		$(".demo").loupe();
		</script>
		<!--<div style="padding:0 9px 0 9px;"><div style="background:#f9f9fb;height:152px;"></div></div>-->
		<div style="float:right;height:45px;"><?=$sns_share_links; ?></div>
		<?
		if($it[gp_site]) {
		?>
		<div style="float:right;border:1px #545454 solid;font-size:12px;padding:2px;margin:3px 10px 0 0px"><a href="<?=$it[gp_site]?>" target="_blank">원문보기</a></div>
		<?
		}
		?>
	</div>
	<!-- } 상품이미지 미리보기 끝 -->

	<!-- 상품 요약정보 및 구매 시작 { -->
	<section id="sit_ov">

		<div id="sit_title_wrap">
			<ul>
				<li style="width:219px">가격정보</li>
				<li style="width:179px">수량</li>
				<li style="width:292px">구매금액</li>
			</ul>
		</div>


		<div id="sit_title_wrap_content">
			<ul>
				<li style="float:left;width:219px">
					<div class="price_en2">
						<ul>
							<li style="width:200px;">카드가</li>
							<li style="width:219px;text-align:right">
								 <span id="it_view_card_price"><?=display_price($카드가); ?></span>
								   <input type="hidden" id="it_card_price" name="it_card_price" value="<?=ceil($it[it_card_price] / 100) * 100; ?>">
							</li>
							<li style="width:200px;">현금가</li>
							<li style="width:219px;text-align:right">
								 <span id="it_view_price"><?=display_price($현금가); ?></span>
								  <input type="hidden" id="it_price" name="it_price" value="<?=ceil($it[it_price] / 100) * 100; ?>">
							</li>
						</ul>
					</div>
				</li>

				<li style="text-align:center;float:left;width:179px">

					<table class="sit_ov_tbl" style="margin:10px 0 0 0;">
		<colgroup>
			<col class="grid_3">
			<col>
		</colgroup>
		<tbody>
		<!--<tr height="20px">
			<td>
				<div style="float:right;border:1px #545454 solid;font-size:12px;padding:2px;"><a href="<? // echo $it[gp_site]?>" target="_blank">원문보기</a></div><div style="float:right;height:45px;"><? // echo $sns_share_links; ?></div>
			</td>
		</tr>-->


		<!--<tr>
			<td><div style="float:right;height:45px;"><? // echo $sns_share_links; ?></div></td>
		</tr>-->

		<tr>
			<td style="padding:0 0 0px 0;">


				<table border="0" cellspacing="0" cellpadding="0" width="100%">
					<?
					$op_res = sql_query("select * from {$g5['g5_shop_option1_table']} where it_id='".$gp_id."' and gubun='P' order by no asc ");
					for($i = 0; $op_row = mysql_fetch_array($op_res); $i++){
					?>
					<tr>
						<td style="width:20%;padding:0 0 0 20px;"><?=$op_row[con]?></td>
						<td style="width:80%;">
							<select name="gp_option[]">
								<option value="">선택없음</option>
								<?
								$op2_res = sql_query("select * from {$g5['g5_shop_option2_table']} where num='".$op_row[no]."' and it_id='".$gp_id."' order by no asc ");
								for($k = 0; $op2_row = mysql_fetch_array($op2_res); $k++){
								?>
								<option value="<?=$op2_row[con]?>"><?=$op2_row[con]?>(+<?=$op2_row[price]?>)</option>
								<?
								}
								?>
							</select>
						</td>
					</tr>
					<?
					}
					?>
				</table>

				</div>

			</td>
		</tr>

		<input type="hidden" name="op_price" value="">

		</tbody>
		</table>

		<script>
		$(document).ready(function(){
			$("select[name^='gp_option']").change(function(){
				var price = 0;
				var it_price = $("input[name='it_price']").val();
				var con = "";

				$("select[name^='gp_option']").each(function(i){
					con += $("select[name^='gp_option']").eq(i).val() + "|";
				});

				con = con.substring(0, con.length-1);

				$.ajax({
					type: "POST",
					dataType: "HTML",
					url: "./_Ajax.option.php",
					data: "it_id=<?=$gp_id?>&gubun=P&op_name=" + con,
					success: function(data){
						//$(".test").html(data);
						price = parseInt(data);
						$("input[name='op_price']").val(price);

						price_calculate();
					}
				});

			});
		});
		</script>

		<? if ($is_orderable) { ?>
		<!-- 선택된 옵션 시작 { -->
		<section style="clear:both;margin:00px 0 0 0;">
			<?php
				if(!$it['it_buy_min_qty'])
					$it['it_buy_min_qty'] = 1;
			?>

			<div style="border:0px #cfcfcf solid;background:#f5f6f8;">

			<table id="sit_opt_added" class="sit_ov_tbl" style="border:0;">
			<!--<colgroup>
				<col class="grid_3">
				<col>
			</colgroup>-->
			<tbody>
				<tr>
					<!--<th bgcolor="#f5f6f8" style="font-size:12px;">
						<?=$ct_qty;?>
					</th>
					<td bgcolor="#f5f6f8"><?//=$it['it_stock_qty']?></td>-->
					<th style="font-weight:bold;margin" scope="row">수량</th>
					<td id="sit_sel_option" >
						<button type="button" class="sit_qty_minus btn_frmline" onclick="qtyCnt('<?=$gp_id?>_qty',-1)">-</button>
						<input type="text" id="<?=$gp_id?>_qty" name="ct_qty<?=$gp_id?>" value="<?=$it['it_buy_min_qty']; ?>" class="frm_input" size="5" style="border:1px #cfcfcf solid;text-align:center;">
						<button type="button" class="sit_qty_plus btn_frmline" onclick="qtyCnt('<?=$gp_id?>_qty',1)">+</button>
						<input type="hidden" name="ct_qty1" value="<?=$cnt[ct_qty]; ?>">
					</td>
				</tr>
				<tr>
					<th style="font-weight:bold;margin" scope="row" colspan="2">남은수량 <?=($it[real_jaego] > 0)?$it[real_jaego]:0?>개</th>
				</tr>
<?
	/*			<tr>
					<!--<th style="font-size:12px;">
						<?=$ct_qty;?>
					</th>
					<td bgcolor="#f5f6f8"><?//=$it['it_stock_qty']?></td>-->
					<th style="font-size:0.9em; font-weight:bold;margin;color:blue" scope="row">신청중인<br>수량</th>
					<td style="color:blue" id="sit_sel_option" ><?=number_format($cnt[ct_qty])?> 개</td>
				</tr>
	*/
?>
				<input type="hidden" name="ct_payment" value="B">
				<input type="hidden" name="ct_payment1" value="C">

				<!--<tr>
					<th scope="row">결제방식</th>
					<td>
						<input type="radio" name="ct_payment" value="B" onclick="price_calculate();" checked> 현금결제
						&nbsp;<input type="radio" name="ct_payment" value="C" onclick="price_calculate();" > 카드결제
					</td>
				</tr>-->
			</tbody>
			</table>

			</div>



			<script>
			/*
			$(function() {
				price_calculate();
			});
			*/
			</script>
		</section>
		<!-- } 선택된 옵션 끝 -->

		<!-- 총 구매액 -->
		<!--<div id="sit_tot_price"></div>-->
		<? } ?>

				</li>

				<li style="float:left;width:282px;">

						<div id="sit_tot_price_grop">
							<span id="it_view_change_price"><?=display_price(ceil($it[it_price] / 100) * 100); ?></span>
						</div>

						<div id="sit_ov_btn" style="margin:15px 0 0 0 !important;">
								<? if( $it[real_jaego] > 0 ) {?>

								<!--  img src="<?=G5_URL?>/img/new_buy_bn.jpg" class="gp_view_bn" gp_id="<?=$gp_id?>" ca_id="<?=$ca_id?>" style="cursor:pointer;">

								<img src="<?=G5_URL?>/img/new_wish_bn.jpg" border="0" align="absmiddle" onclick="item_wish('<?=$it['gp_id'];?>',$('#ct_qty<?=$it['gp_id']?>').val());" id="sit_btn_cart2" style="cursor:pointer;">
								<input type="button" onclick="item_wish(document.fitem, '<?php// echo $it['gp_id']; ?>');" value="찜하기" id="sit_btn_cart2" style="width:190px;height:60px;">-->

											<?
											$장바구니버튼 = "<input type='button' id='sit_btn_buy2' onclick=\"cart_add('add','$it[gp_id]','$gpcode')\" value='장바구니 담기' style='width:130px;height:45px; font-size:1.25em; font-weight:bold;'>";
											$주문하기버튼 = "<input type='button' id='sit_btn_buy2' onclick=\"quickOrder('$it[gp_id]','$gpcode')\" value='주문하기' style='margin-left:20px; width:130px;height:45px; font-size:1.25em; font-weight:bold;'>";

											if($it[po_cash_price] > 0 && $it[real_jaego]) {
												echo $장바구니버튼.$주문하기버튼;
											}
											?>

								<? }
									else {
								?>
												<p id="sit_ov_soldout">상품이 현재 [품절]이라 구매할 수 없습니다.</p>
								<? }?>
							</div>
				</li>
			</ul>
		</div>

		</section>
	<div class="test"></div>
	<!-- } 상품 요약정보 및 구매 끝 -->


<?
/*
if(substr($it[ca_id],0,2) != 'CT') {
?>
	<div id="volume" style="margin:10px 0 0 0;padding:0 0 0 10px;width:775px;">

		<!--<div style="height:1px;border-bottom:1px solid #cfcfcf;width:100%;"></div>-->
		<table border="0" cellspacing="0" cellpadding="0" style="width:650px;" align="center">
			<tr>
				<td>
					<div id="volume_subject" style="width:100%;border:0;margin-left:0">
						VOLUME PRICING
					</div>
				</td>
			</tr>
			<tr>
				<td>

					<div id="volume_body" style="width:100%;border:0;">
						<table class="sit_ov_tbl" style="border-bottom:1px #d2d2d2 solid;">
							<thead>
								<th height="27px" scope="row" style="text-align:center;font-weight:bold;border:1px #d2d2d2 solid;">단위</th>
								<th height="27px" scope="row" style="background:#f6fbff;border:1px #d2d2d2 solid;text-align:center;color:#003ca5;font-weight:bold" colspan="2">달러($)</th>
								<th height="27px" scope="row" style="background:#fffbf9;border:1px #d2d2d2 solid;text-align:center;color:#f45100;font-weight:bold" colspan="2">원화(\)</th>
							</thead>
							<tbody>

							<tr>
								<th scope="row" style="text-align:center;font-weight:bold;border:1px #d2d2d2 solid;height:27px;">수량</th>
								<th scope="row" style="background:#f6fbff;border-bottom:1px solid #d2d2d2;text-align:center;color:#003ca5;font-weight:bold">현금가</th>
								<th scope="row" style="background:#f6fbff;border-bottom:1px solid #d2d2d2;border-right:1px solid #d2d2d2;text-align:center;color:#003ca5;font-weight:bold">카드가</th>
								<th scope="row" style="background:#fffbf9;border-bottom:1px solid #d2d2d2;text-align:center;color:#f45100;font-weight:bold">현금가</th>
								<th scope="row" style="background:#fffbf9;border-bottom:1px solid #d2d2d2;border-right:1px solid #d2d2d2;text-align:center;color:#f45100;font-weight:bold">카드가</th>
							</tr>

							<?
							$sql = " select * from {$g5['g5_shop_group_purchase_option_table']}  where gp_id = '$it[gp_id]' order by po_num";
							$result = sql_query($sql);

							//카드가, 현금가 가격 볼륨프라이싱 정보
							for($i=0;$poR=sql_fetch_array($result);$i++){?>
							<tr height="30px">
								<td sqty="<?=$poR[po_sqty]?>" eqty="<?=$poR[po_eqty]?>" style="border:1px solid #d2d2d2;">
									<?
									if($i==0 && $poR[po_eqty]==99999) echo "모든 수량";
									else{
										echo $poR[po_sqty]?> <? if($poR[po_eqty]==99999) echo "이상 구매시"; else echo " ~ ".$poR[po_eqty];
									}?>
								</td>
								<? if($it[gp_price_type]=="N"){?>

								<td volume_price="<?=getExchangeRate($poR[po_cash_price],$it[gp_id])?>" style="background:#f6fbff;color:#003ca5">
									$ <?=getExchangeUSDRate($poR[po_cash_price],$gp_id);?>
								</td>
								<td volume_price="<?=getExchangeRate($poR[po_card_price],$it[gp_id])?>" style="background:#f6fbff;border-right:1px solid #d2d2d2;color:#003ca5">
									$ <?=getExchangeUSDRate($poR[po_card_price],$gp_id);?>
								</td>
								<td volume_price="<?=getExchangeRate($poR[po_cash_price],$it[gp_id])?>" style="background:#fffbf9;color:#f45100">
									<?=display_price(getExchangeRate($poR[po_cash_price],$it[gp_id]))?>
								</td>
								<td volume_price="<?=getExchangeRate($poR[po_card_price],$it[gp_id])?>" style="background:#fffbf9;border-right:1px solid #d2d2d2;color:#f45100">
									<?=display_price(getExchangeRate($poR[po_card_price],$it[gp_id]))?>
								</td>

								<? }elseif($it[gp_price_type]=="Y"){
									// 실시간형


									$po_cash_price = getExchangeRealRate($it,$poR);

									$po_cash_usd_price = getExchangeRealUSDRate($it,$poR);
									?>

								<td volume_price="<?=$po_cash_usd_price?>" style="background:#f6fbff;color:#003ca5">
									$ <?=$po_cash_usd_price;?>
								</td>
								<td volume_price="<?=round($po_cash_usd_price*1.03,2)?>" style="background:#f6fbff;border-right:1px solid #d2d2d2;color:#003ca5">
									$ <?=round($po_cash_usd_price*1.03,2);?>
								</td>
								<td volume_price="<?=ceil($po_cash_price / 100) * 100?>" style="background:#fffbf9;color:#f45100">
									<?=display_price(ceil($po_cash_price / 100) * 100)?>
								</td>
								<td volume_price="<?=(ceil($po_cash_price*1.03 / 100) * 100)?>" style="background:#fffbf9;border-right:1px solid #d2d2d2;color:#f45100">
									<?=display_price(ceil($po_cash_price*1.03 / 100) * 100)?>
								</td>

								<? }?>
							</tr>
							<? }?>
							</tbody>
						</table>
					</div>
				</td>
			</tr>
		</table>
	</div>
<?
} //if($it[ca_id] != 'CT') {
*/
?>

</div>

</form>

