<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$gp_code = getPurchaseBuyCode($it['ca_id']);
$cnt = sql_fetch("select SUM(ct_qty) as ct_qty from {$g5['g5_shop_cart_table']} where it_id='".$it[gp_id]."' and  total_amount_code = '".$gp_code."' and ct_gubun='P' and ct_status='쇼핑' ");

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
<link rel="stylesheet" href="<?php echo G5_SHOP_SKIN_URL; ?>/style.css">

<style type="text/css">

/* 상품 상세보기 */
#sit1 {margin:0;border:0px}
.sit_admin {text-align:right}
.sit_empty {padding:20px 0;text-align:center}

/* 상품 상세보기 - 개요 */
#sit_ov_wrap1 {margin:0px;border-bottom:0px;background:#fff;zoom:1}
#sit_ov_wrap1:after {visibility:hidden;clear:both;content:""}
#sit_ov_wrap1 .seller_box{border:5px #cfcfcf solid;height:50px;background:#f9f9fa;}
#sit_ov_wrap1 .seller_box .seller_title{float:left;text-align:center;font-weight:bold;font-size:16px;padding:15px 10px 15px 10px;}
#sit_ov_wrap1 .seller_box .seller_info{float:left;padding:5px 7px 7px 15px;width:271px;}
#sit_ov_wrap1 .frm_line{border-top:1px #cfcfcf solid;}
#sit_ov_wrap1 .auc_info_box{margin:10px 0 15px 0;}
#sit_ov_wrap1 .auc_info_box td{font-size:14px;}
#sit_ov_wrap1 .price_box{margin:10px 0 10px 0;}

#sit_toptitle{text-align:left;font-size:18px;padding-bottom:10px;font-weight:bold;color:#000;width:690px;border-bottom:1px solid #eaeaea}

/* 상품 상세보기 - 이미지 미리보기 */
#sit_pvi1 {float:left;padding:15px;width:210px;background:#f9f9fa}
#sit_pvi_big1 {margin:0 0 0px;text-align:center}
#sit_pvi_big1 a {display:none}
#sit_pvi_big1 a.visible {display:block}
#sit_pvi_big1 img {border:1px solid #cfcfcf}

#sit_pvi1 > ul{margin:0 auto;position:relative;width:auto;background:#f9f9fa;padding-top:100px}

/* 상품 상세보기 - 간략정보 및 구매기능 */
#sit_ov1 {position:relative;float:right;padding:20px 15px 15px;width:430px;height:auto !important;background:#f9f9fa;min-height:207px;}
#sit_ov1 h3 {margin:0 0 10px}
#sit_ov1 .ca_name{font-weight:bold;font-size:15px;}

#sit_title1 {font-family:'NanumGothic',dotum;margin:0 10px 0 0;font-size:1.3em;color:#000;}

.sit_ov_tbl1 {margin:7px 0 20px 0;width:100%;border:0;border-collapse:collapse;border:0px solid #cfcfcf;background:#f9f9fa}
.sit_ov_tbl1 tr{height:20px;}
.sit_ov_tbl1 th {border-top:0px solid #e9e9e9;border-bottom:0px solid #e9e9e9;font-weight:normal;text-align:left}
.sit_ov_tbl1 td {padding:7px 0;border-top:0px solid #e9e9e9;border-bottom:0px solid #e9e9e9}
.sit_ov_tbl1 .tb_surtax td{font-size:12px;padding:0 3px 0 3px;}
.sit_ov_tbl1 .sit_qty_minus{background:#fff;color:#828282;padding:0 9px;border-top:1px #cfcfcf solid;border-left:1px #cfcfcf solid;border-bottom:1px #cfcfcf solid;}
.sit_ov_tbl1 .sit_qty_plus{background:#fff;color:#828282;padding:0 9px;border-top:1px #cfcfcf solid;border-right:1px #cfcfcf solid;border-bottom:1px #cfcfcf solid;}
.sit_ov_ro {padding:2px 2px 3px;border:0;background:transparent;text-align:right;vertical-align:middle}
.sit_ov_opt {padding:2px 2px 3px;border:0;background:transparent;vertical-align:middle}
.sit_ov_input {margin:0 1px 0 0;padding:2px 2px 3px;border:0px solid #b8c9c2;background:transparent;vertical-align:middle}

#sit_sel_option1 h3 {position:absolute;font-size:0;line-height:0;overflow:hidden}

#sit_tot_price1 {margin:0px 0;font-size:2.3em;font-weight:bold;width:300px;color:#ff5100}

/*#sit_ov_btn1 {margin:15px 0 0 34px;margin:0 0 0 33px;padding:0;text-align:center;list-style:none;letter-spacing:-3px}*/
#sit_ov_btn1 {position:absolute;left:500px;top:-20px;margin:0 0 0 0px;margin:0 0 0 0px;padding:0;text-align:center;list-style:none;letter-spacing:-3px}
#sit_ov_btn1 a {display:inline-block;width:80px;height:30px;border:0;font-size:0.95em;vertical-align:middle;text-align:center;text-decoration:none;letter-spacing:-0.1em;line-height:2.8em;cursor:pointer}
#sit_ov_btn1 input {display:inline-block;border:0;font-size:0.95em;text-align:center;text-decoration:none;letter-spacing:-0.1em;cursor:pointer}

#sit_btn_buy3 {background:#f45100;color:#fff;font-weight:bold}
#sit_btn_cart3{background:#f45100;color:#fff;margin-left:5px;font-weight:bold}

#it_view_card_price1{color:#545454;font-weight:bold;font-size:2.0em;padding:0px;}
#it_view_price1{color:#545454;font-weight:bold;font-size:2.0em;padding:0px;}

#sit_opt_added1 {position:relative;margin:0;padding:0;background:#fff;border:2px dashed #cfcfcf;list-style:none;padding:5px 15px 5px 15px;height:60px;margin-top:34px}
#sit_opt_added1 li {padding:10px 20px;}
#sit_opt_added1 li div {margin:5px 0 0;text-align:right}
#sit_opt_added1 .last_price {}
.gp_des{clear:both;padding:0px 100px 20px 10px;text-align:right;font-size:19px;color:#f00101;font-weight:bold;background:#f9f9fa}

.gp_line{clear:both;height:1px;border-bottom:1px #cfcfcf solid;}
.gp_line1{clear:both;height:1px;border-bottom:2px #545454 solid;}
.gp_rule{padding:3px 0 0 15px;color:#000}
.gp_rule .number{color:#ff8955;}
.gp_rule .underline{text-decoration:underline;font-weight:bold;}

.gp_agree{margin:10px 0 0 0;padding:3px;font-size:18px;font-weight:bold;color:#000}
.gp_agree textarea{font-family:'NanumBarunGothic';color:#545454;font-size:10px;font-weight:bold;padding:15px 30px 15px 30px;width:91%;height:150px;border:1px #d2d2d2 solid;}

</style>

<script type="text/javascript" src="<?=G5_URL?>/js/jquery.loupe.min.js"></script>

<form name="fitem1" method="post" action="<?php echo $action_url; ?>" onsubmit="return fitem_submit(this);">
<input type="hidden" name="gp_id" value="<?php echo $gp_id; ?>">
<input type="hidden" name="sw_direct">
<input type="hidden" name="url">
<input type="hidden" name="ca_id" value="<?=$it['ca_id']?>">
<input type="hidden" name="buy_kind" value="공동구매">
<input type="hidden" name="ct_send_cost" id="ct_send_cost" value="<?=$it[gp_sc_price]?>">
<input type="hidden" name="op_name" id="op_name" value="<?=$_GET[op_name]?>">

<div id="sit_ov_wrap1">
	<div id="sit_toptitle">
		<?php echo stripslashes($it['gp_name']); ?> 
	</div>
    <!-- 상품이미지 미리보기 시작 { -->
    <div id="sit_pvi1">
        <div id="sit_pvi_big1" style="margin:0;">
			<img src='<?php echo $it[gp_img]?>' style="width:210px;height:210px;" class="demo">
		</div>
    </div>
    <!-- } 상품이미지 미리보기 끝 -->

    <!-- 상품 요약정보 및 구매 시작 { -->
    <section id="sit_ov1">
		<!--<div class="ca_name"><?=$it[ca_name]?></div>-->
		<div class="ca_name">상품금액</div>
		<div style="margin:3px 0 0 0;height:1px;border-bottom:1px #cfcfcf solid;"></div>
        <!--<h2 id="sit_title1"><?php echo stripslashes($it['gp_name']); ?> <span class="sound_only">요약정보 및 구매</span></h2>-->
		
		


		<table class="sit_ov_tbl1" style="margin:10px 0 0 0;">
        <colgroup>
            <col class="grid_3">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row" style="width:45%;">카드가</th>
			<th scope="row" style="width:10%;"></th>
			<th scope="row" style="width:45%;">송금가</th>
        </tr>
		<tr>
			<!-- 2014 11 20 backup 
			<td style="padding:0 0 10px 0;">
                <span id="it_view_card_price1"><?php echo display_price(ceil($it[it_card_price] / 100) * 100 * $_GET['cnt'] + $_GET[op_price]); ?></span>
                <input type="hidden" id="it_card_price" name="it_card_price" value="<?php echo ceil($it[it_card_price] / 100) * 100 * $_GET['cnt'] + $_GET[op_price]; ?>">
            </td>
			<td style="padding:0 0 10px 0;">
                <span id="it_view_price1"><?php echo display_price(ceil($it[it_price] / 100) * 100 * $_GET['cnt'] + $_GET[op_price]); ?></span>
                <input type="hidden" id="it_price" name="it_price" value="<?php echo ceil($it[it_price] / 100) * 100 * $_GET['cnt'] + $_GET[op_price]; ?>">
            </td>
			-->
			<td style="padding:20px 0 10px 0;text-align:right">
                <span id="it_view_card_price1"><?php echo display_price(CeilGe($it[it_card_price])  + $_GET[op_price]); ?></span>
                <input type="hidden" id="it_card_price" name="it_card_price" value="<?php echo CeilGe($it[it_card_price])  + $_GET[op_price]; ?>">
            </td>
			<td></td>
			<td style="padding:20px 0 10px 0;text-align:right">
                <span id="it_view_price1"><?php echo display_price(CeilGe($it[it_price])  + $_GET[op_price]); ?></span>
                <input type="hidden" id="it_price" name="it_price" value="<?php echo CeilGe($it[it_card_price])  + $_GET[op_price]; ?>">
            </td>

		</tr>
		</tbody>
		</table>

        <?php if ($is_orderable) { ?>
        <!-- 선택된 옵션 시작 { -->
        <section style="clear:both;margin:0px;position:relative;">
			<div class="last_price" style="position:absolute;left:250px;top:12px;font-size:25px;color:#ff5100;z-index:100;font-weight:bold;">
					<span><?php echo display_price(CeilGe($it[it_price]) * $_GET['cnt'] + $_GET[op_price]); ?></span>
					 <input type="hidden" id="it_price" name="it_price" value="<?php echo CeilGe($it[it_price]) * $_GET['cnt'] + $_GET[op_price]; ?>">
				</div>

            <?php
                if(!$it['it_buy_min_qty'])
                    $it['it_buy_min_qty'] = 1;
            ?>

			<table id="sit_opt_added1" class="sit_ov_tbl" >
				
			<tbody>
				
				<tr>
					<th width="50px" style="font-weight:bold;text-align:center;color:#000">수량</th>
					<td id="sit_sel_option1" width="160px" style="font-weight:bold;text-align:center;color:#000">

						<?php echo $_GET['cnt']; ?>&nbsp&nbsp&nbsp&nbsp개&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<b>신청금액</b>
						<input type="hidden" name="ct_qty" value="<?php echo $_GET['cnt']; ?>">
						<!--<button type="button" class="sit_qty_minus btn_frmline">-</button><input type="text" name="ct_qty" value="<?php// echo $it['it_buy_min_qty']; ?>" class="frm_input" size="5" style="border:1px #cfcfcf solid;text-align:center;"><button type="button" class="sit_qty_plus btn_frmline">+</button>-->
						
					</td>
					<th><!--재고수량--></th>
					<td><?//=$it['it_stock_qty']?></td>
				</tr>

				<input type="hidden" name="ct_payment" value="B">
				<input type="hidden" name="ct_payment1" value="C">

				<!--<tr>
					<th width="50px" style="font-weight:bold;text-align:center;color:#000">옵션</th>
					<td id="sit_sel_option1" style="font-weight:bold;text-align:center;color:#000">
						 
						<?=$op_name1?>
						
					</td>-->
					<!--<th><!--재고수량--><!--</th>
					<td><?//=$it['it_stock_qty']?></td>
				</tr>-->

				<!--<tr>
					<th scope="row">결제방식</th>
					<td>
						<input type="radio" name="ct_payment" value="B" onclick="price_calculate();" checked> 현금결제
						&nbsp;<input type="radio" name="ct_payment" value="C" onclick="price_calculate();" > 카드결제
					</td>
				</tr>-->
			</tbody>
			</table>

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
    
    </section>
    <!-- } 상품 요약정보 및 구매 끝 -->
	
	

</div>



<div class="gp_des">
	<!-- 공동구매 신청 하셨습니다. -->
</div>


<div class="gp_line"></div>

<div class="gp_agree">
	<div style="margin-left:5px;">코인즈투데이특약동의</div>
	<div style="margin:10px 0 0 0;">
		<textarea name="agree"><?=$config[cf_dc]?></textarea>
	</div>
</div>

<div style="margin:25px 0 25px 5px;text-align:left;font-weight:bold;color:#000;padding-bottom:25px;">
	<div style="float:left;width:500px;margin-top:5px;">코인즈투데이특약에 동의 하십니까?&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="radio" name="gp_agr[]" id="gp_agr" value="y">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;동의함&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="radio" name="gp_agr[]" id="gp_agr" value="n" checked>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;동의안함
	</div>
	<div style="float:left;width:190px;text-align:right;"><a href="<?=G5_URL?>/company1.php?cate=2" style="text-align:right;border:2px solid #a1a1a3;display:inline-block;padding:5px 8px;text-decoration:none">자세히보기</a></div>
</div>

<div class="gp_line"></div>

<div style="position:relative;margin:30px 0 0 0;padding-bottom:20px;">
	<div class="gp_rule">
		<span class="number">1.</span><span>실시간 시세에 의해 가격변동이 있을 수 있습니다.</span>
	</div>
	<div class="gp_rule">
		<span class="number">2.</span><span>공동구매신청 후 수량 수정이 불가하니 신중히 신청 하시기 바랍니다.</span>
	</div>
	<div class="gp_rule">
		<span class="number">3.</span><span>공동구매 구매진행 중 품절로 인해 구매취소가 될 수 있으니 양해 바랍니다.</span>
	</div>

	<div id="sit_ov_btn1" style="margin:15px 0 0 0 !important;">
		<input type="image" src="<?=G5_URL?>/img/gp_sumbit_btn.gif" border="0" align="absmiddle" onclick="document.pressed=this.value;" value="장바구니" id="sit_btn_buy3" style="width:190px;height:60px;">
		<!--<img src="<?=G5_URL?>/img/gp_jjim_btn.gif" border="0" align="absmiddle" onclick="item_wish(document.fitem1, '<?php echo $it['gp_id']; ?>');" id="sit_btn_cart3" style="cursor:pointer;">-->
	</div>

</div>

<!--<div>
	<div id="sit_ov_btn1" style="margin:15px 0 0 0 !important;">
		<input type="image" src="<?=G5_URL?>/img/gp_sumbit_btn.gif" border="0" align="absmiddle" onclick="document.pressed=this.value;" value="장바구니" id="sit_btn_buy3" style="width:190px;height:60px;">
		<img src="<?=G5_URL?>/img/gp_jjim_btn.gif" border="0" align="absmiddle" onclick="item_wish(document.fitem1, '<?php echo $it['gp_id']; ?>');" id="sit_btn_cart3" style="cursor:pointer;">
</div>
	</div>-->

<div style="width:700px;background:#ff0000;padding:5px 10px 10px 10px;margin:10px 0 0 -10px;text-align:center;color:#fff;font-size:11px;font-weight:bold;">
	
	신청하신 물품은 실시간 주문현황(공동구매)에서 확인 가능하며, 주문은 공동구매종료후에 가능합니다.
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
								<th scope="row" style="background:#f6fbff;border-bottom:1px solid #d2d2d2;text-align:center;color:#003ca5;font-weight:bold">현금가</th>
								<th scope="row" style="background:#f6fbff;border-bottom:1px solid #d2d2d2;border-right:1px solid #d2d2d2;text-align:center;color:#003ca5;font-weight:bold">카드가</th>
								<th scope="row" style="background:#fffbf9;border-bottom:1px solid #d2d2d2;text-align:center;color:#f45100;font-weight:bold">현금가</th>
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


<script type="text/javascript" src="<?=G5_URL?>/js/common_product.js"></script>