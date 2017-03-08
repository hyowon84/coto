<?
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>

<script src="<? echo G5_JS_URL; ?>/jquery.nicescroll.min.js"></script>
<script src="<? echo G5_JS_URL; ?>/jquery.fancyalert.js"></script>
<script src="<? echo G5_JS_URL; ?>/jquery.cfSlider.js"></script><!-- https://github.com/codefactory/cfSlider -->
<script src="<? echo G5_JS_URL; ?>/jquery.cfSlidePanel.js"></script>



<form name="fitem" action="<? echo $action_url; ?>" method="post" onsubmit="return fitem_submit(this);">
<input type="hidden" name="it_id[]" value="<? echo $it['it_id']; ?>">
<input type="hidden" name="sw_direct">
<input type="hidden" name="url">
<input type="hidden" name="ct_bid" value="">

<!-- 일반상품 상세페이지 스킨 item.form.skin -->

<div>
	
	<!-- 상품이미지 -->
	<section id='slider'>
		<!-- 상품이미지 미리보기 시작 { item.form.skin -->
			<div class="container">
			<?
			for($k = 1; $k <= 10; $k++){
				if($it["it_img".$k]){
			?>
				<div class='panel'><img src="<?=G5_DATA_URL?>/item/<?=$it["it_img".$k]?>" /></div>
			<?
				}
			}
			?>
			</div>
	</section>
   
	<script>
		$('.container').cfSlidePanel();
	</script>

<?
	/* 가격정보 판매상태 셋팅 */
if (!$it['it_use']) {
	$판매상태 = '판매중지';  
} 
else if ($it['it_tel_inq']) {
	$판매상태 = '전화문의';
} else {	/* 가격정보 로딩 */
	$판매가격 = display_price(get_price($it));
	$판매상태 = "$판매가격 <input type='hidden' id='it_price' value='".get_price($it).";'>";
} 

/* 배송비 관련 설정 */
$ct_send_cost_label = '배송비결제';

if($default['de_send_cost_case'] == '무료')
	$sc_method = '무료배송';
else
	$sc_method = '주문시 결제';

if($it['it_sc_type'] == 1)
	$sc_method = '무료배송';
else if($it['it_sc_type'] > 1) {
	if($it['it_sc_method'] == 1)
		$sc_method = '수령후 지불';
	else if($it['it_sc_method'] == 2) {
		$ct_send_cost_label = '<label for="ct_send_cost">배송비결제</label>';
		$sc_method = '<select name="ct_send_cost" id="ct_send_cost">
		<option value="0">주문시 결제</option>
		<option value="1">수령후 지불</option>
		</select>';
	}
	else
		$sc_method = '주문시 결제';
}


/* 필수옵션, 추가옵션 셋팅 */

/*
if ($it['it_cust_price']) {
/*	<tr>
	<th scope="row">예상구매금액</th>
	<td><div id="sit_tot_price"></div><?// echo display_price($it['it_cust_price']); ?></td>
	</tr>
}
*/
?>
	
	
	<!-- 가격정보 -->
	<section class="itemdtl_box">
		<table width='100%' align='center'>
			<tr>
				<td class='itemdtl_tit' valign="middle">가격정보</td>
				<td class='itemdtl_val' valign="middle">
					<?=$판매상태 ?>
				</td>
			</tr>
			<tr><td colspan='2' class='itemdtl_tr_1pxline'></td></tr>

			
<tr><td colspan='2'>
			<table width='100%' align='center'>
			<tr>
				<td class='itemdtl_tit' valign="middle" title='배송비결제정보'><? echo $ct_send_cost_label; ?></td>
				<td class='itemdtl_val' valign="middle"><span><? echo $sc_method; ?></span></td>
			</tr>
			<tr><td colspan='2' class='itemdtl_tr_1pxline'></td></tr>
	<?
		
		if($option_item) { ?>
			<tr>
				<td class='itemdtl_tit' valign="middle">선택옵션</td>
				<td class='itemdtl_val' valign="middle"><?=$option_item?></td>
			</tr>
			<tr><td colspan='2' class='itemdtl_tr_1pxline'></td></tr>
	<? } 
		if($supply_item) {
		?>
			<tr>
				<td class='itemdtl_tit' valign="middle">추가옵션</td>
				<td class='itemdtl_val' valign="middle"><?=$supply_item?></td>
			</tr>
			<tr><td colspan='2' class='itemdtl_tr_1pxline'></td></tr>
	<?	}	?>		

	
<? if ($it['it_use'] && !$it['it_tel_inq'] && !$is_soldout) { ?>
			<tr>
				<td colspan='2' id='sit_sel_option'>
				
				<?
				if(!$option_item) {
					if(!$it['it_buy_min_qty']) $it['it_buy_min_qty'] = 1;
				?>
				
				<div id="sit_sel_option">
					<ul id="sit_opt_added">
						<li class="sit_opt_list">
							
							<input type="hidden" name="io_type[<? echo $it_id; ?>][]" value="0">
							<input type="hidden" name="io_id[<? echo $it_id; ?>][]" value="">
							<input type="hidden" name="io_value[<? echo $it_id; ?>][]" value="<? echo $it['it_name']; ?>">
							<input type="hidden" class="io_price" value="0">
							<input type="hidden" class="io_stock" value="<? echo $it['it_stock_qty']; ?>">
							<!--<span class="sit_opt_subj"><? echo $it['it_name']; ?></span>
							<span class="sit_opt_prc">(+0원)</span>-->
							<div style="margin:0 auto; width:165px;text-align:center;">재고수량 : <?=$it[it_stock_qty]?></div>
							
							<div style='text-align:center;'>
								<input type="text" name="ct_qty[<? echo $it_id; ?>][]" value="<? echo $it['it_buy_min_qty']; ?>" class="frm_input " size="5" style='text-align:right;'>
								<button type="button" class="sit_qty_plus btn_frmline">증가</button>
								<button type="button" class="sit_qty_minus btn_frmline">감소</button>
							</div>
						</li>
					</ul>
					<script>
					$(function() {
						price_calculate();
					});
					</script>
				<? } ?>
			</div>
				
			</tr>
			<tr><td colspan='2' class='itemdtl_tr_1pxline'></td></tr>
		<? } ?>

		<? if($is_soldout) { ?>
			<p id="sit_ov_soldout">상품의 재고가 부족하여 구매할 수 없습니다.</p>
		<? } ?>
	
</table>
</td></tr>
			
			<tr>
				<td class='itemdtl_tit'>예상 구매 금액</td>
				<td class='itemdtl_val'>
					<div id="sit_tot_price"></div>
					<? //echo display_price($it['it_cust_price']); ?>
				</td>
			</tr>
		</table>
	</section>
	
	<section class="itemdtl_box2">
		<button id='bt_buyitem'>구매하기</button>
		<button id='bt_addcart'>카트담기</button>
	</section>
	
	<section class="itemdtl_box3">
		<aside id="sit_siblings">
			<h2>다른 상품 보기</h2>
			<?
			if ($prev_href || $next_href) {
				echo $prev_href.$prev_title.$prev_href2;
				echo $next_href.$next_title.$next_href2;
			} else {
				echo '<span class="sound_only">이 분류에 등록된 다른 상품이 없습니다.</span>';
			}
			?>
		</aside>
	</section>
	
</form>



<script>

$(window).bind("pageshow", function(event) {
	if (event.originalEvent.persisted) {
		document.location.reload();
	}
});

$(function(){
	// 상품이미지 슬라이드
	var time = 500;
	var idx = idx2 = 0;
	var slide_width = $("#sit_pvi_slide").width();
	var slide_count = $("#sit_pvi_slide li").size();
	$("#sit_pvi_slide li:first").css("display", "block");
	if(slide_count > 1)
		$(".sit_pvi_btn").css("display", "inline");

	$("#sit_pvi_prev").click(function() {
		if(slide_count > 1) {
			idx2 = (idx - 1) % slide_count;
			if(idx2 < 0)
				idx2 = slide_count - 1;
			
			//$("#sit_pvi_slide li:hidden").css("left", "-"+slide_width+"px").css("position","absolute");
			$("#sit_pvi_slide li:hidden").css({'left':"-"+slide_width+"px","position":"absolute"});
			$("#sit_pvi_slide li:eq("+idx+")").filter(":not(:animated)").animate({ left: "+="+slide_width+"px" }, time, function() {
				$(this).css("display", "none").css("left", "-"+slide_width+"px");
			
			});
			
			$("#sit_pvi_slide li:eq("+idx2+")").css("display", "block").filter(":not(:animated)").animate({ left: "+="+slide_width+"px" }, time,
				function() {
					$(this).css("position","relative");
					idx = idx2;
					
				}
			);
		}
	});

	$("#sit_pvi_next").click(function() {
		if(slide_count > 1) {
			idx2 = (idx + 1) % slide_count;
			
			//$("#sit_pvi_slide li:hidden").css("left", slide_width+"px").css("position","absolute");
			$("#sit_pvi_slide li:hidden").css({'left':slide_width+"px","position":"absolute"});
			$("#sit_pvi_slide li:eq("+idx+")").filter(":not(:animated)").animate({ left: "-="+slide_width+"px" }, time, function() {
				$(this).css("display", "none").css("left", slide_width+"px");
			});
			
			$("#sit_pvi_slide li:eq("+idx2+")").css("display", "block").filter(":not(:animated)").animate({ left: "-="+slide_width+"px" }, time,
				function() {
					$(this).css("position","relative");
					idx = idx2;
				}
			);
		}
	});

	// 상품이미지 크게보기
	$(".popup_item_image").click(function() {
		var url = $(this).attr("href");
		var top = 10;
		var left = 10;
		var opt = 'scrollbars=yes,top='+top+',left='+left;
		popup_window(url, "largeimage", opt);

		return false;
	});
});

function content_swipe(direction)
{
	// 로딩 레이어
	load_message();

	var next_href = '<? echo $next_href; ?>';
	var prev_href = '<? echo $prev_href; ?>';
	var str;

	if(direction == "left") {
		str = next_href;
	} else {
		str = prev_href;
	}

	var href = str.match(/https?:\/{2}[^\"]+/gi);

	setTimeout(function() {
		document.location.href = href[0];
	}, 500);
}

function load_message()
{
	var w = $(window).width();
	var h = $(window).height();
	var img_w = 64;
	var img_h = 64;
	var top, left;
	var scr_top = $(window).scrollTop();

	if (/iP(hone|od|ad)/.test(navigator.platform)) {
		if(window.innerHeight - $(window).outerHeight(true) > 0)
			h += (window.innerHeight - $(window).outerHeight(true));
	}

	top = parseInt((h - img_h) / 2);
	left = parseInt((w - img_w) / 2);

	var img = "<div id=\"loading_message\" style=\"top:"+scr_top+"px;width:"+w+"px;height:"+h+"px;\">";
	img += "<img src=\"<? echo G5_MSHOP_SKIN_URL; ?>/img/loading.gif\" style=\"top:"+top+"px;left:"+left+"px;\" />";
	img += "</div>";

	$("body").append(img);
}

// 상품보관
function item_wish(f, it_id)
{
	f.url.value = "<? echo G5_SHOP_URL; ?>/wishupdate.php?it_id="+it_id;
	f.action = "<? echo G5_SHOP_URL; ?>/wishupdate.php";
	f.submit();
}

// 추천메일
function popup_item_recommend(it_id)
{
	if (!g5_is_member)
	{
		if (confirm("회원만 추천하실 수 있습니다."))
			document.location.href = "<? echo G5_BBS_URL; ?>/login.php?url=<? echo urlencode(G5_SHOP_URL."/item.php?it_id=$it_id"); ?>";
	}
	else
	{
		url = "<? echo G5_SHOP_URL; ?>/itemrecommend.php?it_id=" + it_id;
		opt = "scrollbars=yes,width=616,height=420,top=10,left=10";
		popup_window(url, "itemrecommend", opt);
	}
}

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

	if($(".sit_opt_list").size() < 1) {
		alert("상품의 선택옵션을 선택해 주십시오.");
		return false;
	}

	var val, io_type, result = true;
	var sum_qty = 0;
	var min_qty = parseInt(<? echo $it['it_buy_min_qty']; ?>);
	var max_qty = parseInt(<? echo $it['it_buy_max_qty']; ?>);
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

	if(min_qty > 0 && sum_qty < min_qty) {
		alert("선택옵션 개수 총합 "+number_format(String(min_qty))+"개 이상 주문해 주십시오.");
		return false;
	}

	if(max_qty > 0 && sum_qty > max_qty) {
		alert("선택옵션 개수 총합 "+number_format(String(max_qty))+"개 이하로 주문해 주십시오.");
		return false;
	}

	return true;
}



$(document).ready(function(){
	$("select[name^='it_option']").change(function(){
		var price = 0;
		var it_price = $("input[name='it_price']").val();
		var con = "";
		
		$("select[name^='it_option']").each(function(i){
			con += $("select[name^='it_option']").eq(i).val() + "|";
		});

		con = con.substring(0, con.length-1);

		$("input[name='op_name']").val(con);

		$.ajax({
			type: "POST",
			dataType: "HTML",
			url: "./_Ajax.option.php",
			data: "it_id=<?=$it_id?>&gubun=N&op_name=" + con,
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

	
	
<? 
//필요한 소스만 고를 예정 사용안함
if(0) {
?>


	<!-- div id="sit_star_sns">
		<?
		$sns_title = get_text($it['it_name']).' | '.get_text($config['cf_title']);
		$sns_url  = G5_SHOP_URL.'/item.php?it_id='.$it['it_id'];

		if ($score = get_star_image($it['it_id'])) { ?>
		고객선호도 <span>별<? echo $score?>개</span>
		<img src="<? echo G5_SHOP_URL; ?>/img/s_star<? echo $score?>.png" alt="" class="sit_star">
		<? } ?>
		<? echo get_sns_share_link('facebook', $sns_url, $sns_title, G5_MSHOP_SKIN_URL.'/img/sns_fb2.png'); ?>
		<? echo get_sns_share_link('twitter', $sns_url, $sns_title, G5_MSHOP_SKIN_URL.'/img/sns_twt2.png'); ?>
		<? echo get_sns_share_link('googleplus', $sns_url, $sns_title, G5_MSHOP_SKIN_URL.'/img/sns_goo2.png'); ?>
	</div-->
	
	
	<section id="itemdtl_box">
		<strong id="sit_title"><? echo stripslashes($it['it_name']); ?></strong><br>
		<span id="sit_desc"><? echo $it['it_basic']; ?></span>
		<? if($is_orderable) { ?>
		<p id="sit_opt_info">
			상품 선택옵션 <? echo $option_count; ?> 개, 추가옵션 <? echo $supply_count; ?> 개
		</p>
		<? } ?>

		
		<table class="sit_ov_tbl">
		<colgroup>
			<col class="grid_3">
			<col>
		</colgroup>
		<tbody>
		<? if ($it['it_maker']) { ?>
		<tr>
			<th scope="row">제조사</th>
			<td><? echo $it['it_maker']; ?></td>
		</tr>
		<? } ?>

		<? if ($it['it_origin']) { ?>
		<tr>
			<th scope="row">원산지</th>
			<td><? echo $it['it_origin']; ?></td>
		</tr>
		<? } ?>

		<? if ($it['it_brand']) { ?>
		<tr>
			<th scope="row">브랜드</th>
			<td><? echo $it['it_brand']; ?></td>
		</tr>
		<? } ?>
		<? if ($it['it_model']) { ?>
		<tr>
			<th scope="row">모델</th>
			<td><? echo $it['it_model']; ?></td>
		</tr>
		<? }
		
		
			if (!$it['it_use'])
			{ // 판매가능이 아닐 경우 
		?>
		<tr>
			<th scope="row">판매가격</th>
			<td>판매중지</td>
		</tr>
		<? }
			else if ($it['it_tel_inq']) 
			{ // 전화문의일 경우 ?>
		<tr>
			<th scope="row">판매가격</th>
			<td>전화문의</td>
		</tr>
		<? }
			else 
			{ // 전화문의가 아닐 경우?>
			<? if ($it['it_cust_price']) { // 1.00.03?>
			<tr>
				<th scope="row">예상구매금액</th>
				<td><div id="sit_tot_price"></div><?// echo display_price($it['it_cust_price']); ?></td>
			</tr>
			<? } ?>

			<tr>
				<th scope="row">판매가격</th>
				<td>
					<? echo display_price(get_price($it)); ?>
					<input type="hidden" id="it_price" value="<? echo get_price($it); ?>">
				</td>
			</tr>
		<? } //else end ?>

		<?
		/* 재고 표시하는 경우 주석 해제
		<tr>
			<th scope="row">재고수량</th>
			<td><? echo number_format(get_it_stock_qty($it_id)); ?> 개</td>
		</tr>
		*/
		?>

		<? if ($config['cf_use_point']) { // 포인트 사용한다면 ?>
		<!-- <tr>
			<th scope="row"><label for="disp_point">포인트</label></th>
			<td>
				<?
				$it_point = get_item_point($it);
				echo number_format($it_point);
				?> 점
			</td>
		</tr>
		-->
		<? } ?>
		<?
		$ct_send_cost_label = '배송비결제';

		if($default['de_send_cost_case'] == '무료')
			$sc_method = '무료배송';
		else
			$sc_method = '주문시 결제';

		if($it['it_sc_type'] == 1)
			$sc_method = '무료배송';
		else if($it['it_sc_type'] > 1) {
			if($it['it_sc_method'] == 1)
				$sc_method = '수령후 지불';
			else if($it['it_sc_method'] == 2) {
				$ct_send_cost_label = '<label for="ct_send_cost">배송비결제</label>';
				$sc_method = '<select name="ct_send_cost" id="ct_send_cost">
								  <option value="0">주문시 결제</option>
								  <option value="1">수령후 지불</option>
							  </select>';
			}
			else
				$sc_method = '주문시 결제';
		}
		?>
		<tr>
			<th><? echo $ct_send_cost_label; ?></th>
			<td><? echo $sc_method; ?></td>
		</tr>
		<? if($it['it_buy_min_qty']) { ?>
		<tr>
			<th>최소구매수량</th>
			<td><? echo number_format($it['it_buy_min_qty']); ?> 개<td>
		</tr>
		<? } ?>
		<? if($it['it_buy_max_qty']) { ?>
		<tr>
			<th>최대구매수량</th>
			<td><? echo number_format($it['it_buy_max_qty']); ?> 개<td>
		</tr>
		<? } ?>
		</tbody>
		</table>

		<?
		if($option_item) {
		?>
		
		<section>
			<h3>선택옵션</h3>
			<table class="sit_ov_tbl">
			<colgroup>
				<col class="grid_3">
				<col>
			</colgroup>
			<tbody>
			<? // 선택옵션
			echo $option_item;
			?>
			</tbody>
			</table>
		</section>
		<?
		}
		?>

		<?
		if($supply_item) {
		?>
		<section>
			<h3>추가옵션</h3>
			<table class="sit_ov_tbl">
			<colgroup>
				<col class="grid_3">
				<col>
			</colgroup>
			<tbody>
			<? // 추가옵션
			echo $supply_item;
			?>
			</tbody>
			</table>
		</section>
		<?
		}
		?>

		<? if ($it['it_use'] && !$it['it_tel_inq'] && !$is_soldout) { ?>
			<div id="sit_sel_option1">
				<?
				if(!$option_item) {
					if(!$it['it_buy_min_qty'])
						$it['it_buy_min_qty'] = 1;
				?>
					<ul id="sit_opt_added">
						<li class="sit_opt_list">
							
							<input type="hidden" name="io_type[<? echo $it_id; ?>][]" value="0">
							<input type="hidden" name="io_id[<? echo $it_id; ?>][]" value="">
							<input type="hidden" name="io_value[<? echo $it_id; ?>][]" value="<? echo $it['it_name']; ?>">
							<input type="hidden" class="io_price" value="0">
							<input type="hidden" class="io_stock" value="<? echo $it['it_stock_qty']; ?>">
							<!--<span class="sit_opt_subj"><? echo $it['it_name']; ?></span>
							<span class="sit_opt_prc">(+0원)</span>-->
							<div style="float:left;width:165px;text-align:center;">재고수량 : <?=$it[it_stock_qty]?></div>
							
							<div>
								<input type="text" name="ct_qty[<? echo $it_id; ?>][]" value="<? echo $it['it_buy_min_qty']; ?>" class="frm_input" size="5">
								<button type="button" class="sit_qty_plus btn_frmline">증가</button>
								<button type="button" class="sit_qty_minus btn_frmline">감소</button>
							</div>
						</li>
					</ul>
					<script>
					$(function() {
						price_calculate();
					});
					</script>
				<? } ?>
			</div>
		<? } ?>

		<? if($is_soldout) { ?>
			<p id="sit_ov_soldout">상품의 재고가 부족하여 구매할 수 없습니다.</p>
		<? } ?>

		<div id="sit_ov_btn">
			<? if ($is_orderable) { ?>
			<input type="submit" onclick="document.pressed=this.value;" value="바로구매" id="sit_btn_buy">
			<input type="submit" onclick="document.pressed=this.value;" value="장바구니" id="sit_btn_cart">
			<? } ?>
			<a href="javascript:item_wish(document.fitem, '<? echo $it['it_id']; ?>');" id="sit_btn_wish">위시리스트</a>
			<a href="javascript:popup_item_recommend('<? echo $it['it_id']; ?>');" id="sit_btn_rec">추천하기</a>
		</div>
	</section>
	
	<!-- 경매중 정보 --> 
	<section id="itemdtl_box">
	
	</section>	
</div>

<?
$href = G5_SHOP_URL.'/iteminfo.php?it_id='.$it_id;
?>
<div id="sit_more">
	<ul class="sanchor2">
		<li><a href="<? echo $href; ?>" target="_blank">상품정보</a></li>
		<li><a href="<? echo $href; ?>&amp;info=use" target="_blank">사용후기 <span class="item_use_count"><? echo $item_use_count; ?></span></a></li>
		<li><a href="<? echo $href; ?>&amp;info=qa" target="_blank">상품문의 <span class="item_qa_count"><? echo $item_qa_count; ?></span></a></li>
		<? if ($default['de_baesong_content']) { ?><li><a href="<? echo $href; ?>&amp;info=dvr" target="_blank">배송정보</a></li><? } ?>
		<? if ($default['de_change_content']) { ?><li><a href="<? echo $href; ?>&amp;info=ex" target="_blank">교환정보</a></li><? } ?>
		<? if($default['de_rel_list_use']) {?>
		<li><a href="<? echo $href; ?>&amp;info=rel" target="_blank">관련상품 <span class="item_relation_count"><? echo $item_relation_count; ?></span></a></li>
		<? } ?>
	</ul>
</div>

</form>



<?
}
?>