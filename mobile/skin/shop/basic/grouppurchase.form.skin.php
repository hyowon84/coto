<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가


/* 신청수량 합계 */
$cnt_sql = "	SELECT	SUM(ct_qty) as ct_qty
							FROM		g5_shop_cart
							WHERE		it_id='$gp_id'
							AND			ct_gubun='P'
							AND			total_amount_code = '$gp_code'
							AND			ct_status IN ('쇼핑','입금완료') ";
$cnt = sql_fetch($cnt_sql);

if($cnt[ct_qty]){
	$ct_qty = "현재 주문 수량 : ".$cnt[ct_qty];
}else{
	$cnt[ct_qty] = 0;
}


$it[it_price] = ceil($it[po_cash_price] / 100) * 100;
$it[it_card_price] = ceil($it[po_cash_price] * 1.03 / 100) * 100;

$현금가 = $it[it_price];
$카드가 = $it[it_card_price];
?>

<link rel="stylesheet" href="<?=G5_MSHOP_SKIN_URL?>/style.css">
<script type="text/javascript" src="<?=G5_URL?>/js/jquery.loupe.min.js"></script>

<form name="fitem" method="post" action="<?=$action_url?>" onsubmit="return fitem_submit(this);">
<input type="hidden" name="gp_id" value="<?=$gp_id?>">
<input type="hidden" name="it_id" value="<?=$gp_id?>">
<input type="hidden" name="sw_direct">
<input type="hidden" name="url">
<input type="hidden" name="ca_id" value="<?=$ca_id?>">
<input type="hidden" name="buy_kind" value="공동구매">
<h2 id="sit_title">
	<?
	if($it[gp_site]) {
	?>
	<div style="float:left; background:white; border:1px #545454 solid; padding:1px; margin-right:5px; font-size:12px; width:auto; height:18px; ">
		<a href="<?=$it[gp_site]?>" target="_blank" style='padding:0px;'>원문보기</a>
	</div>
	<?
	}
	?>
	
	<?=stripslashes($it['gp_name'])?>
</h2>
<div id="sit_ov_wrap">


	<!-- 상품이미지 미리보기 시작 { grouppurchase.form.skin (M) -->
	<div id="sit_pvi">
		<div id="sit_pvi_big" style="margin:0;">
			<img src='<?=$it[gp_img]?>' style="width:100%;max-width:500px" class="demo">
		</div>
		<script type="text/javascript">
		$(".demo").loupe();
		</script>
		<div style="padding:0 9px 0 9px;"><div style="background:#f9f9fb;height:0px;"></div></div>
	</div>
	<div style="height:10px;background:#f5f6f7"></div>
	<!-- } 상품이미지 미리보기 끝 -->

	<!-- 상품 요약정보 및 구매 시작 { -->
	<section id="sit_ov" >
		<!-- <div class="ca_name"><?=$it[ca_name]?></div>
		<div style="margin:3px 0 0 0;height:1px;border-bottom:1px #cfcfcf solid;"></div> -->


		<div id="sit_price_area" style="padding:18px 15px 15px;">
			<div>
				<span style="color:#515151;font-weight:bold;font-size:1.250em">가격정보</span>
				<span id="it_view_card_price">카드가 <?=display_price($카드가)?></span>
				<input type="hidden" id="it_card_price" name="it_card_price" value="<?=$카드가?>">
			</div>
			<div style="padding:10px 0">
				<span id="it_view_price">현금가 <?=display_price($현금가)?></span>
				<input type="hidden" id="it_price" name="it_price" value="<?=$현금가?>">
			</div>
		</div>
		<div style="margin:5px 15px 0;height:1px;background:#dcdcdc"></div>
		<!-- <div style="float:right;height:45px;"><?=$sns_share_links?></div> -->

		<?php if ($is_orderable) { ?>
		<!-- 선택된 옵션 시작 { -->
		<div id="sit_qty_area" style="padding:20px 15px 9px">
			<?php
				if(!$it['it_buy_min_qty']) {
					$it['it_buy_min_qty'] = 1;
				}
			?>

			<script type="text/javascript" src="<?=G5_URL?>/js/common_product.js"></script>

			<div id="sit_sel_option">
				<span style="color:#515151;font-weight:bold;font-size:1.250em">수량</span>
				<div style="display:inline;">
					<img type="button" class="plus_qty" name="plus_<?=$gp_id?>" src='<?=G5_URL?>/img/m/groupPlusQty.png'  />
					<input type="text" id="<?=$gp_id?>_qty" name="ct_qty<?=$gp_id?>" value="<?=$it['it_buy_min_qty']?>" class="sit_frm_input" size="5" style="text-align:center;" readonly>
					<img type="button" class="minus_qty" name="minus_<?=$gp_id?>" src='<?=G5_URL?>/img/m/groupMinusQty.png'/>
				</div>
			</div>
			<div style="height:20px;background:#fff"></div>

			<?
			/*
			<div style="text-align:right;color:#1c11ff;font-size:1.000em">
				<span style="margin:0 17px 0 0">신청중인 수량</span>
				<span><?=number_format($cnt[ct_qty])?>개</span>
			</div>
			*/
			?>
			<input type="hidden" name="ct_payment" value="B">
			<input type="hidden" name="ct_payment1" value="C">
		</div>
		<!-- } 선택된 옵션 끝 -->

		<!-- 총 구매액 -->
		<!--<div id="sit_tot_price"></div>-->
		<?php } ?>

		<div style="height:7.5px;background:#f5f6f7"></div>
		<div id="sit_ov_btn">
			<?
			$장바구니버튼 = "<input type='button' id='sit_btn_buy2' onclick=\"cart_add('add','$it[gp_id]','$gpcode')\" value='장바구니 담기' style='width:130px;height:30px; font-size:1.25em; font-weight:bold;'>";
			$주문하기버튼 = "<input type='button' id='sit_btn_buy2' onclick=\"quickOrder('$it[gp_id]','$gpcode')\" value='주문하기' style='margin-left:5px; width:130px;height:30px; font-size:1.25em; font-weight:bold;'>";
			
			if($it[po_cash_price] > 0 && $it[real_jaego]) {
				echo "<div style='margin:0px auto; width:285px;'>".$장바구니버튼.$주문하기버튼."</div>";
			}
			?>
		</div>

	</section>
	<!-- } 상품 요약정보 및 구매 끝 -->
	<div style="height:35px;background:#f5f6f7"></div>

<?
/*
if(substr($it[ca_id],0,2) != 'CT') {
?>
	<div id="volume" >
		<div id="volume_subject" style="width:100%;border:0;margin-left:10px">
			VOLUME PRICING
		</div>
		<table border="0" cellspacing="0" cellpadding="0" style="width:100%;font-size: 0.833em;" align="center">
			<tr>
				<td>
					<!-- 볼륨프라이싱 테이블 가격표 -->
					<div id="volume_body">
						<table class="sit_ov_tbl" style="border-bottom:1px #d2d2d2 solid;">
							<thead>
								<th height="30px" scope="row" style="text-align:center;font-weight:bold;border:1px #d2d2d2 solid;">단위</th>
								<th height="30px" scope="row" style="background:#f6fbff;border:1px #d2d2d2 solid;text-align:center;color:#003ca5;font-weight:bold" colspan="2">달러($)</th>
								<th height="30px" scope="row" style="background:#fffbf9;border:1px #d2d2d2 solid;text-align:center;color:#f45100;font-weight:bold" colspan="2">원화(\)</th>
							</thead>
							<tbody>

							<tr height="30px">
								<th scope="row" style="text-align:center;font-weight:bold;border:1px #d2d2d2 solid;">수량</th>
								<th scope="row" style="background:#f6fbff;border-bottom:1px solid #d2d2d2;border-right:1px solid #d2d2d2;text-align:center;color:#003ca5;font-weight:bold">현금가</th>
								<th scope="row" style="background:#f6fbff;border-bottom:1px solid #d2d2d2;border-right:1px solid #d2d2d2;text-align:center;color:#003ca5;font-weight:bold">카드가</th>
								<th scope="row" style="background:#fffbf9;border-bottom:1px solid #d2d2d2;border-right:1px solid #d2d2d2;text-align:center;color:#f45100;font-weight:bold">현금가</th>
								<th scope="row" style="background:#fffbf9;border-bottom:1px solid #d2d2d2;border-right:1px solid #d2d2d2;text-align:center;color:#f45100;font-weight:bold">카드가</th>
							</tr>

							<?php
							$sql = " select * from {$g5['g5_shop_group_purchase_option_table']}  where gp_id = '$it[gp_id]' order by po_num";
							$result = sql_query($sql);


							for($i=0;$poR=sql_fetch_array($result);$i++){?>
							<tr height="30px">
								<td sqty="<?=$poR[po_sqty]?>" eqty="<?=$poR[po_eqty]?>" style="border:1px solid #d2d2d2;">
									<?php
									if($i==0 && $poR[po_eqty]==99999) echo "모든 수량";
									else{
										echo $poR[po_sqty]?> <?php if($poR[po_eqty]==99999) echo "이상 구매시"; else echo " ~ ".$poR[po_eqty];
									}?>
								</td>
								<td volume_price="<?=getExchangeRate($poR[po_cash_price],$it[gp_id])?>" style="background:#f6fbff;border-right:1px solid #d2d2d2;color:#003ca5">
									$ <?=getExchangeUSDRate($poR[po_cash_price],$gp_id);?>
								</td>
								<td volume_price="<?=getExchangeRate($poR[po_card_price],$it[gp_id])?>" style="background:#f6fbff;border-right:1px solid #d2d2d2;color:#003ca5">
									$ <?=getExchangeUSDRate($poR[po_card_price],$gp_id);?>
								</td>
								<td volume_price="<?=getExchangeRate($poR[po_cash_price],$it[gp_id])?>" style="background:#fffbf9;border-right:1px solid #d2d2d2;color:#f45100">
									<?=display_price(getExchangeRate($poR[po_cash_price],$it[gp_id]))?>
								</td>
								<td volume_price="<?=getExchangeRate($poR[po_card_price],$it[gp_id])?>" style="background:#fffbf9;border-right:1px solid #d2d2d2;color:#f45100">
									<?=display_price(getExchangeRate($poR[po_card_price],$it[gp_id]))?>
								</td>
							</tr>
							<?php }?>
							</tbody>
						</table>
					</div>
				</td>
			</tr>
		</table>
	</div>
</div>
<?
}
*/
?>
</form>
<script>

// 바로구매, 장바구니 폼 전송
function fitem_submit(f)
{
	if (document.pressed == "장바구니") {
		f.sw_direct.value = 0;
	} else { // 바로구매
		f.sw_direct.value = 1;
	}

	// 판매가격이 0 보다 작다면
	if (document.getElementById("it_price").value < 0) {
		alert("전화로 문의해 주시면 감사하겠습니다.");
		return false;
	}


	var val, io_type, result = true;
	var sum_qty = 0;
	var min_qty = parseInt(<?=$it['it_buy_min_qty']?>);
	var max_qty = parseInt(<?=$it['it_buy_max_qty']?>);
	var $el_type = $("input[name^=io_type]");

	$("input[name^=ct_qty]").each(function(index) {
		val = $(this).val();

		if(val.length < 1) {
			alert("수량을 입력해 주십시오.");
			result = false;
			return false;
		}

		if(val.replace(/[0-9]/g, "").length > 0) {
			alert("수량은 숫자로 입력해 주십시오.");
			result = false;
			return false;
		}

		if(parseInt(val.replace(/[^0-9]/g, "")) < 1) {
			alert("수량은 1이상 입력해 주십시오.");
			result = false;
			return false;
		}

		io_type = $el_type.eq(index).val();
		if(io_type == "0")
			sum_qty += parseInt(val);
	});

	if(!result) {
		return false;
	}
	return true;
}
</script>