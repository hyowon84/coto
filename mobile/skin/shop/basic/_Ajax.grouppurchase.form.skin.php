<?php
/* 모바일 - 공동구매 신청 레이어팝업 */

if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$gp_code = getPurchaseBuyCode($it['ca_id']);
//진행중인 수량 카운팅
$cnt = sql_fetch("select SUM(ct_qty) as ct_qty from {$g5['g5_shop_cart_table']} where it_id='".$it[gp_id]."' and  total_amount_code = '".$gp_code."' and ct_gubun='P' and ct_status='쇼핑' ");

//진행중인 수량 + 구매자요청수량 = 볼륨프라이싱가격정보 구하기
$it[it_price] = getGroupPurchaseQtyBasicPrice($it[gp_id],$_GET['cnt']+$cnt['ct_qty']);
$it[it_card_price] = getGroupPurchaseQtyBasicPrice1($it[gp_id],$_GET['cnt']+$cnt['ct_qty']);

$op_name_arr = explode("|", $op_name);
for($i = 0; $i < count($op_name_arr); $i++){
	if($op_name_arr[$i]){
		$op_name1 .= $op_name_arr[$i].",";
	}
}

$op_name1 = substr($op_name1, 0, strlen($op_name1)-1);
if($op_name1){
	$op_name1 = $op_name1;
}else{
	$op_name1 = "";
}
?>
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0">

<script type="text/javascript" src="<?=G5_URL?>/js/jquery.loupe.min.js"></script>
<form name="fitem1" method="post" action="/shop/cart_gp.php" onsubmit="return fitem_submit1(this);">
<input type="hidden" name="gp_id" value="<?php echo $gp_id; ?>">
<input type="hidden" name="sw_direct">
<input type="hidden" name="url">
<input type="hidden" name="ca_id" value="<?=$it['ca_id']?>">
<input type="hidden" name="buy_kind" value="공동구매">
<input type="hidden" name="ct_send_cost" id="ct_send_cost" value="<?=$it[gp_sc_price]?>">
<input type="hidden" name="op_name" id="op_name" value="<?=$_GET[op_name]?>">

<div id="sit_ov_wrap1" style='width:100%;'>
	<section id="sit_ov1">
		<div class="gpName" colspan='2' width='100%'>[<?=$it[ca_name]?>]<?=stripslashes($it['gp_name']);?></div>
		<div class="sit_ov_tbl1" style="margin:10px 0 0 0;">
			<div class="prodImg"><img src='<?php echo $it[gp_img]?>' style="width:100%;" class="demo"></div>
			<span class="prodInfo">
				<div class="prodTitle">상품금액</div>
				<span class="card"><p>카드가</p><div><?=display_price(CeilGe($it[it_card_price])  + $_GET[op_price]);?></div></span>
				<span class="cash"><p>구매가</p><div><?=display_price(CeilGe($it[it_price])  + $_GET[op_price]);?></div></span>
				<span class="amount">
					<span class="qty">
						수량 <?=$_GET['cnt']; ?>개
						<input type="hidden" name="ct_qty" value="<?php echo $_GET['cnt']; ?>">
						<input type="hidden" name="ct_payment" value="B">
						<input type="hidden" name="ct_payment1" value="C">
					</span>
					<span class="money">
						<span><?php echo display_price(CeilGe($it[it_price]) * $_GET['cnt'] + $_GET[op_price]); ?></span>
						<input type="hidden" id="it_price" name="it_price" value="<?php echo CeilGe($it[it_price]) * $_GET['cnt'] + $_GET[op_price]; ?>">
					</span>
				</span>
			</span>
			<div class="proposal">공동구매 신청하셨습니다.</div>
		</div>


		<link rel="stylesheet" type="text/css" href="<?=G5_URL?>/css/overflow.css" />
		<script type="text/javascript" src="<?=G5_URL?>/js/overflow.js"></script>
		
		<div class="gp_agree" style='margin-top:20px;'>
			<div style="margin-left:5px;">코인즈투데이특약동의</div>
			<textarea readonly style='width:100%; height:80px;'>
			<?=$config[cf_dc]?>
			</textarea>	
			<!-- div class='faContents overthrow' style="margin:10px 0 0 0; height:200px; border:1px solid;">
			</div-->
		</div>
		<div class="gp_line"></div>
		<div class="txt" style="font-weight:bold;color:#000;padding-bottom:25px;">
			<span style="margin-top:5px;">코인즈투데이특약에 동의 하십니까?</span>
			<span class="radioBtnContainer">
				<input type="radio" name="gp_agr[]" id="gp_agr" value="y"> 동의함 <input type="radio" name="gp_agr[]" id="gp_agr" value="n" checked> 동의안함
			</span>
<!--			<a href="--><?php //echo G5_URL ?><!--/company1.php?cate=2" style="text-align:right;border:2px solid #a1a1a3;display:inline-block;padding:5px 8px;text-decoration:none">자세히보기</a>-->
		</div>
		
		<div class="gp_line"></div>
		
		<div>
			<div class="gp_rule">
				<span class="number">1.</span><span>실시간 시세에 의해 가격변동이 있을 수 있습니다.</span>
			</div>
			<div class="gp_rule">
				<span class="number">2.</span><span>공동구매신청 후 수량 수정이 불가하니 신중히 신청 하시기 바랍니다.</span>
			</div>
 			<div class="gp_rule">
				<span class="number">3.</span><span>공동구매 구매진행 중 품절로 인해 구매취소가 될 수 있으니 양해 바랍니다.</span>
			</div>
		</div>

		<div id="sit_ov_btn1" style="width:190px; margin:auto;">
			<input type="image" src="<?=G5_URL?>/img/gp_sumbit_btn.gif" border="0" align="absmiddle" onclick="document.pressed=this.value;" value="장바구니" id="sit_btn_buy3" style="height:60px;">
		</div>
		<?php if ($is_orderable) { ?>
		<!-- 선택된 옵션 시작 { -->
		<section>

			<?php
				if(!$it['it_buy_min_qty'])
					$it['it_buy_min_qty'] = 1;
			?>

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
		<!--<div id="sit_tot_price1"></div>-->
		<?php } ?>

		<script>
		// 상품보관
		function item_wish(f, it_id)
		{
			f.url.value = "<?php echo G5_SHOP_URL; ?>/wishupdate.php?it_id="+it_id+"&mode=gp";
			f.action = "<?php echo G5_SHOP_URL; ?>/wishupdate.php";
			f.submit();
		}

		// 추천메일
		function popup_item_recommend(it_id)
		{
			if (!g5_is_member)
			{
				if (confirm("회원만 추천하실 수 있습니다."))
					document.location.href = "<?php echo G5_BBS_URL; ?>/login.php?url=<?php echo urlencode(G5_SHOP_URL."/item.php?it_id=$it_id"); ?>";
			}
			else
			{
				url = "./itemrecommend.php?it_id=" + it_id;
				opt = "scrollbars=yes,width=616,height=420,top=10,left=10";
				popup_window(url, "itemrecommend", opt);
			}
		}
		</script>

	</section>
	<div class="warning">신청하신 물품은 실시간 주문현황(공동구매)에서 확인 가능하며,<br/>주문은 공동구매종료후에 가능합니다.</div>
	<!-- } 상품 요약정보 및 구매 끝 -->
</div>
</form>



<div id="volume_body" style="width:100%;border:0;display:none;">
						<table class="sit_ov_tbl" style="border-bottom:1px #d2d2d2 solid;">
							<thead>
								<th height="30px" scope="row" style="text-align:center;font-weight:bold;border:1px #d2d2d2 solid;">단위</th>
								<th height="30px" scope="row" style="background:#f6fbff;border:1px #d2d2d2 solid;text-align:center;color:#003ca5;font-weight:bold" colspan="2">달러($)</th>
								<th height="30px" scope="row" style="background:#fffbf9;border:1px #d2d2d2 solid;text-align:center;color:#f45100;font-weight:bold" colspan="2">원화(\)</th>
							</thead>
							<tbody>

							<tr height="30px">
								<th scope="row" style="text-align:center;font-weight:bold;border:1px #d2d2d2 solid;">수량</th>
								<th scope="row" style="background:#f6fbff;border-bottom:1px solid #d2d2d2;text-align:center;color:#003ca5;font-weight:bold">구매가</th>
								<th scope="row" style="background:#f6fbff;border-bottom:1px solid #d2d2d2;border-right:1px solid #d2d2d2;text-align:center;color:#003ca5;font-weight:bold">카드가</th>
								<th scope="row" style="background:#fffbf9;border-bottom:1px solid #d2d2d2;text-align:center;color:#f45100;font-weight:bold">구매가</th>
								<th scope="row" style="background:#fffbf9;border-bottom:1px solid #d2d2d2;border-right:1px solid #d2d2d2;text-align:center;color:#f45100;font-weight:bold">카드가</th>
							</tr>

							<?php
							$sql = " select * from {$g5['g5_shop_group_purchase_option_table']}  where gp_id = '$it[gp_id]' order by po_num";
							$result = sql_query($sql);
							for($i=0;$poR=sql_fetch_array($result);$i++){?>
							<tr height="30px">
								<td sqty="<?php echo $poR[po_sqty]?>" eqty="<?php echo $poR[po_eqty]?>" style="border:1px solid #d2d2d2;">
									<?php 
									if($i==0 && $poR[po_eqty]==99999) echo "모든 수량";
									else{
										echo $poR[po_sqty]?> <?php if($poR[po_eqty]==99999) echo "이상 구매시"; else echo " ~ ".$poR[po_eqty];
									}?>
								</td>
								<td volume_price="<?php echo getExchangeRate($poR[po_cash_price],$it[gp_id])?>" style="background:#f6fbff;color:#003ca5">
									$ <?php echo getExchangeUSDRate($poR[po_cash_price],$gp_id);?>
								</td>
								<td volume_price="<?php echo getExchangeRate($poR[po_card_price],$it[gp_id])?>" style="background:#f6fbff;border-right:1px solid #d2d2d2;color:#003ca5">
									$ <?php echo getExchangeUSDRate($poR[po_card_price],$gp_id);?>
								</td>
								<td volume_price="<?php echo getExchangeRate($poR[po_cash_price],$it[gp_id])?>" style="background:#fffbf9;color:#f45100">
									<?php echo display_price(getExchangeRate($poR[po_cash_price],$it[gp_id]))?>
								</td>
								<td volume_price="<?php echo getExchangeRate($poR[po_card_price],$it[gp_id])?>" style="background:#fffbf9;border-right:1px solid #d2d2d2;color:#f45100">
									<?php echo display_price(getExchangeRate($poR[po_card_price],$it[gp_id]))?>
								</td>
							</tr>
							<?php }?>
							</tbody>
						</table>
					</div>



<script>


// 바로구매, 장바구니 폼 전송
function fitem_submit1(f)
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
	var min_qty = parseInt(<?php echo $it['it_buy_min_qty']; ?>);
	var max_qty = parseInt(<?php echo $it['it_buy_max_qty']; ?>);
	var $el_type = $("input[name^=io_type]");
	var agr_chk = "";

	$("form[name='fitem1']").find("input[name^=ct_qty]").each(function(index) {
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

	$("form[name='fitem1']").find("input[name='gp_agr[]']").each(function(i){
		if($("form[name='fitem1']").find("input[name='gp_agr[]']").eq(i).is(":checked") == true){

			if($("form[name='fitem1']").find("input[name='gp_agr[]']").eq(i).val() == "n"){
				agr_chk = $("form[name='fitem1']").find("input[name='gp_agr[]']").eq(i).val();
			}
		}
	});

	if(agr_chk == "n"){
		alert("코인즈투데이특약동의에 동의 하셔야 합니다.");
		return false;
	}

	return true;
}
</script>