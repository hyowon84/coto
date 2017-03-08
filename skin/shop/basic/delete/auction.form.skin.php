<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if(date("m") < 10){
	$cnt_m = substr(date("m"), 1, 1); 
}else{
	$cnt_m = date("m"); 
}
if(date("d") < 10){
	$cnt_d = substr(date("d"), 1, 1); 
}else{
	$cnt_d = date("d"); 
}
if(date("H") < 10){
	$cnt_h = substr(date("H"), 1, 1); 
}else{
	$cnt_h = date("H"); 
}
if(date("i") < 10){
	$cnt_i = substr(date("i"), 1, 1); 
}else{
	$cnt_i = date("i"); 
}
if(date("s") < 10){
	$cnt_s = substr(date("s"), 1, 1); 
}else{
	$cnt_s = date("s"); 
}

$now_date = date("Y").",".$cnt_m.",".$cnt_d.",".$cnt_h.",".$cnt_i.",".$cnt_s;

/* <div id="end_date">종료일시<br>
<?=$it_last_date1?>년
<?=$it_last_date2?>월
<?=$it_last_date3?>일
<?=$it_last_date4?>시
<?=$it_last_date5?>분
<?=$it_last_date6?>초</div>
*/


echo $it['it_last_date'];


$it_last_date1 = substr($it['it_last_date'], 0, 4);
$it_last_date2 = substr($it['it_last_date'], 4, 2);
$it_last_date3 = substr($it['it_last_date'], 6, 2);
$it_last_date4 = substr($it['it_last_date'], 8, 2);
$it_last_date5 = substr($it['it_last_date'], 10, 2);
$it_last_date6 = substr($it['it_last_date'], 12, 2);

$end_date_m = substr($it['it_last_date'], 4, 2);
$end_date_d = substr($it['it_last_date'], 6, 2);
$end_date_h = substr($it['it_last_date'], 8, 2);
$end_date_i = substr($it['it_last_date'], 10, 2);
$end_date_s = substr($it['it_last_date'], 12, 2);
$end_date = $it_last_date1.",".$end_date_m.",".$end_date_d.",".$end_date_h.",".$end_date_i.",".$end_date_s;

$end_date1 = strtotime($it['it_last_date']) - strtotime("now");

$auc_res1 = sql_query("select * from g5_shop_auction where it_id='$it_id' order by no desc limit 0, 1 ");
$auc_num1 = mysql_num_rows($auc_res1);
$auc_row1 = mysql_fetch_array($auc_res1);

if($it['it_stock_qty']){
	$it_stock_qty = $it['it_stock_qty'];
}else{
	$it_stock_qty = 0;
}

$bid_price = $auc_row1[it_last_bid] * 0.11;
$bid_price = ceil($bid_price/100) * 100;		//다음 입찰가

//최대경매입찰가
$now_max_bid_row1 = sql_fetch("select * from {$g5['g5_shop_auction_max_table']} where it_id='".$it_id."' order by no desc limit 0, 1 ");

$now_max_bid_res = sql_query("select * from {$g5['g5_shop_auction_max_table']} where it_id='".$it_id."' and loginid='".$member[mb_id]."' order by no desc limit 0, 1 ");
$now_max_bid_num = mysql_num_rows($now_max_bid_res);
$now_max_bid_row = mysql_fetch_array($now_max_bid_res);
?>

<link rel="stylesheet" href="<?=G5_SHOP_SKIN_URL;?>/style.css">
<link rel="stylesheet" href="<?=G5_URL?>/css/vanillabox.css">

<script type="text/javascript" src="<?=G5_URL?>/js/jquery.loupe.min.js"></script>
<script type="text/javascript" src="<?=G5_URL?>/js/jquery.vanillabox.js"></script>
<script type="text/javascript" src="<?=G5_URL?>/js/configform.js"></script>
<script type="text/javascript" src="<?=G5_URL?>/js/configpre.js"></script>
<script src='http://coinstoday.co.kr/js/jquery.zoom.js'></script>
<script type="text/javascript">
$(document).ready(function(){
	$(".loupe").find("img").css({"width":"500px", "height":"500px"});
});
</script>

<form name="fitem" method="post" action="<?=$action_url;?>" onsubmit="return fitem_submit(this);">
<input type="hidden" name="auction_mode" value="auction">
<input type="hidden" name="it_id[]" value="<?=$it_id;?>">
<input type="hidden" name="sw_direct">
<input type="hidden" name="url">
<input type="hidden" name="buy_kind" value="투데이스토어">

<div id="sit_ov_wrap">
	<div id="sit_titlebox">
		<div style="margin:0px 0 0 0;"><?=$it['it_year']?></div>
		<h2 id="sit_title"><?=stripslashes($it['it_name']);?> <span class="sound_only">요약정보 및 구매</span></h2>
		<h2 id="sit_title2">경매상품</h2>
	</div>
	<!-- 상품이미지 미리보기 시작 { -->
	<div id="sit_pvi">
		<div id="sit_pvi_big">
		<?php
		$big_img_count = 0;
		$thumbnails = array();
		for($i=1; $i<=10; $i++) {
			if(!$it['it_img'.$i])
				continue;

			$img = get_it_thumbnail1($it['it_img'.$i], $default['de_mimg_width'], $default['de_mimg_height'], '', $i);

			if($img) {
				// 썸네일
				$thumb = get_it_thumbnail1($it['it_img'.$i], 60, 60, '', $i);
				$thumbnails[] = $thumb;
				$big_img_count++;

				//echo '<a href="'.G5_SHOP_URL.'/largeimage.php?it_id='.$it['it_id'].'&amp;no='.$i.'" target="_blank" class="popup_item_image">'.$img.'</a>';
				echo '<a href="javascript:void(0);" class="popup_item_image" onclick="show_button(\''.$it_id.'\')">'.$img.'</a>';
			}
			/*echo "
			<script type='text/javascript'>
			$('.demo".$i."').loupe();
			</script>
			";*/
		}

		if($big_img_count == 0) {
			echo '<img src="'.G5_SHOP_URL.'/img/no_image.gif" alt="">';
		}
	   ?>
		</div>
		<?php
		// 썸네일
		$thumb1 = true;
		$thumb_count = 0;
		$total_count = count($thumbnails);
		if($total_count > 0) {

			echo '<table width=100% bgcolor="#fff" border=0 height=130 cellpadding=0 cellspacing=0 ><tr><td style="border:0px;background:#fff">';


			echo '<table align=center><tr><td>';
			echo '<ul id="sit_pvi_thumb">';

			foreach($thumbnails as $val) {
				$thumb_count++;
				$sit_pvi_last ='';
				if ($thumb_count % 5 == 0) $sit_pvi_last = 'class="li_last"';
					echo '<li '.$sit_pvi_last.'>';
					echo '<a href="'.G5_SHOP_URL.'/largeimage.php?it_id='.$it['it_id'].'&amp;no='.$thumb_count.'" class="popup_item_image img_thumb">'.$val.'<span class="sound_only"> '.$thumb_count.'번째 이미지 새창</span></a>';
					echo '</li>';
			}

			echo '</ul>';
			echo '</td></tr></table>';


			echo '</td></tr></table>';
		}
	   ?>

		<ul id="interactive-image-list<?=$it_id?>" style="display:none;">
		<?
		for($k = 1; $k <= 10; $k++){
			if($it["it_img".$k]){
		?>
			<li class='zoom ex<?=$it_id?>'><a href="<?=G5_DATA_URL?>/item/<?=$it["it_img".$k]?>" title="">&nbsp;</a></li>
		<?
			}
		}
		?>
		</ul>
	</div>
	<!-- } 상품이미지 미리보기 끝 -->


	<script type="text/javascript">
	
	function show_button(wr_id){
		// Options Demo
		var configForm = new ConfigForm($('#config-form' + wr_id));
		var configPre = new ConfigPre($('#config-pre' + wr_id));

		var optionBox = null;
		if (optionBox) {
			optionBox.dispose();
		}

		var config = configForm.buildConfig();
		var targetElems = (config.type === 'iframe') ?
			$('#interactive-page-list' + wr_id + ' a') :
			$('#interactive-image-list' + wr_id + ' a');

		optionBox = targetElems.vanillabox(config);
		optionBox.show();

		$(".vnbx-mask").find("img").addClass("ex1");
	}

	</script>

	<!-- 상품 요약정보 및 구매 시작 { -->
	<section id="sit_ov">
		
		<div id="end_date">종료일시<br><?=$it_last_date1?>년 <?=$it_last_date2?>월 <?=$it_last_date3?>일 <?=$it_last_date4?>시 <?=$it_last_date5?>분 <?=$it_last_date6?>초</div>			
		
		<p id="cl sit_desc"><?php// echo $it['it_basic'];?></p>
		<?php if($is_orderable) {?>
		<p id="sit_opt_info">
			상품 선택옵션 <?=$option_count;?> 개, 추가옵션 <?=$supply_count;?> 개
		</p>
		<?php }?>
		
		<table class="sit_ov_tbl">
		<colgroup>
			<col class="grid_3">
			<col>
		</colgroup>
		<tbody>
		<?php if ($it['it_maker']) {?>
		<tr>
			<th scope="row">제조사</th>
			<td><?=$it['it_maker'];?></td>
		</tr>
		<?php }?>

		<?php if ($it['it_origin']) {?>
		<tr>
			<th scope="row">원산지</th>
			<td><?=$it['it_origin'];?></td>
		</tr>
		<?php }?>

		<?php if ($it['it_brand']) {?>
		<tr>
			<th scope="row">브랜드</th>
			<td><?=$it['it_brand'];?></td>
		</tr>
		<?php }?>

		<?php if ($it['it_model']) {?>
		<tr>
			<th scope="row">모델</th>
			<td><?=$it['it_model'];?></td>
		</tr>
		<?php }?>

		<tr>
			<th colspan="2">
			<?if($it[it_last_date] >= date("YmdHis")){?>
				<div style="float:left;padding:5px;background:#003ca5;color:#fff;">경매중</div>
			<?}else{?>
				<div style="float:left;padding:5px;background:#003ca5;color:#fff;">경매종료</div>
			<?}?>
			</th>
		</tr>

		<?php if (!$it['it_use']) { // 판매가능이 아닐 경우?>

		<tr>
			<th scope="row">현재입찰가</th>
			<td>판매중지</td>
		</tr>
		<?php } else if ($it['it_tel_inq']) { // 전화문의일 경우?>
		<tr>
			<th scope="row">현재입찰가</th>
			<td>전화문의</td>
		</tr>
		<?php } else { // 전화문의가 아닐 경우?>
		<tr>
			<th scope="row" class="c_price">
				현재입찰가
				<span style="color:#000;">
					입찰:
					<?
					$bid_cnt = sql_fetch("select count(*) as cnt from g5_shop_auction where it_id='".$it_id."' and ca_id='1040' ");
					echo $bid_cnt[cnt];
					?>
					bid
				</span>
			</th>
			<td>
				<?if($it[it_last_date] >= date("YmdHis")){?>
				<div class="price_en" style="display:none;">
					<ul>
						<li>입찰가</li>
						<li class="cl"><span class="ct_bid"><?=number_format($bid_price)?></span><span>원</span></li>
					</ul>
				</div>
				<input type="hidden" name="ct_bid" value="<?=$bid_price?>">
				<?}else{?>
				<input type="hidden" name="ct_bid" value="0">
				<?}?>

				<div class="price_en">
					<div>최대입찰희망가</div>
					<div style="height:31px;margin:5px 0 0 0;"><p style="float:left;"><input type="text" name="max_bid" id="max_bid" size="25" style="width:160px;height:27px;"> </p><p style="float:left;margin:12px 0 0 0;">원</p></div>
					<div class="cl" style="height:20px;margin:10px 0 0 0;">
						<a href="javascript:void(0);" class="max_bid_bn" style="color:#fff;text-decoration:none;background:#e8180c;border:1px #e8180c solid;font-size:11px;padding:5px;">최대경매가입찰</a>
					</div>
					<div class="cl" style="margin:7px 0 0 0;">
						<p style="margin:0px 0 0 0;font-size:11px;" class="max_bid_price"></p>
						<?
						if($now_max_bid_num){
						?>
							<?if($now_max_bid_row[it_max_bid] < $now_max_bid_row1[it_max_bid]){?>
								<p style="clear:both;margin:0px 0 0 0;font-size:12px;">현재 <?=$member[mb_id]?>님의 최대경매입찰가보다 높은 가격이 있습니다.</p>
							<?}?>
						<?}?>
					</div>
				</div>
			</td>
		</tr>
			
		<tr>
			<td class="c_price_num" colspan="2">

				<?if($auc_num1){?>

					<?if($it[it_last_date] >= date("YmdHis")){?>
						<?=display_price($auc_row1[it_last_bid]);?>
						<input type="hidden" id="it_price" name="it_price" value="<?=$auc_row1[it_last_bid];?>">
					<?}else{?>
						<?php

						if($auc_row1[it_last_bid] < $now_max_bid_row1[it_max_bid]){
							$bid_price = $auc_row1[it_last_bid] * 0.11;
							$bid_price = ceil($bid_price/100) * 100;
							echo display_price($auc_row1[it_last_bid]);
						?>
							<input type="hidden" id="it_price" name="it_price" value="<?=$auc_row1[it_last_bid];?>">
						<?
						}else{
							echo display_price($auc_row1[it_last_bid]);
						?>
							<input type="hidden" id="it_price" name="it_price" value="<?=$auc_row1[it_last_bid];?>">
						<?}?>
					<?}?>

				<?}else{?>
					<?=display_price(get_price($it));?>
					<input type="hidden" id="it_price" name="it_price" value="<?=get_price($it);?>">
				<?}?>
			</td>
		</tr>

		<?php }?>

		<?php
		/* 재고 표시하는 경우 주석 해제
		<tr>
			<th scope="row">재고수량</th>
			<td><?=number_format(get_it_stock_qty($it_id));?> 개</td>
		</tr>
		*/
	   ?>

		<?php if ($config['cf_use_point']) { // 포인트 사용한다면?>
	   <!-- <tr>
			<th scope="row">포인트</th>
			<td>
				<?php
				$it_point = get_item_point($it);
				echo number_format($it_point);
			   ?> 점
			</td>
		</tr>-->
		<?php }?>
		<?php
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

		<!--
		<div class="cl price_en" style="margin:40px 0 0 0;">
		<?if($it[it_last_date] >= date("YmdHis")){?>		
			<ul>
				<li>입찰가</li>
				<li class="cl"><span class="ct_bid"><?=number_format($bid_price)?></span><span>원</span></li>
			</ul>
			<input type="hidden" name="ct_bid" value="<?=$bid_price?>">
		
		<?}?>
		</div>
		-->


		<tr><td colspan="2">예상구매금액</td></tr>
		<tr><td colspan="2">		
		<!-- 총 구매액 -->
		<div id="sit_tot_price"></div>
		</td><tr>



		

	   
		<?php if($it['it_buy_min_qty']) {?>
		<tr>
			<th>최소구매수량</th>
			<td><?=number_format($it['it_buy_min_qty']);?> 개<td>
		</tr>
		<?php }?>
		<?php if($it['it_buy_max_qty']) {?>
		<tr>
			<th>최대구매수량</th>
			<td><?=number_format($it['it_buy_max_qty']);?> 개<td>
		</tr>
		<?php }?>
		</tbody>
		</table>
		

		<!-- 배송비 선택 { -->
		<table style="border:0;margin:0;padding:5px;border:0px solid #cfcfcf;width:400px;font-size:15px;">
		<!--<tr>
			<th style="text-align:left;width:80px"><?=$ct_send_cost_label;?></th>
			<td><?=$sc_method;?></td>
		</tr>-->
		</table>

		<?if($member[mb_id]){?>

		<div class="cl">
			<div style="float:left;">
				<!--<?
				//if($now_max_bid_num){
				?>
					<?//if($now_max_bid_row[it_max_bid] < $now_max_bid_row1[it_max_bid]){?>
						<p style="margin:-40px 0 0 0;font-size:12px;position:absolute;">현재 <?//=$member[mb_id]?>님의 최대경매입찰가보다 높은 가격이 있습니다.</p>
					<?//}?>
				<?//}?>

				<p style="clear:both;margin:-23px 0 0 0;font-size:11px;position:absolute;" class="max_bid_price"></p>

				<p style="margin:0 0 0 0;"><a href="javascript:void(0);" class="max_biding_bn" style="color:#fff;text-decoration:none;">최대경매가입찰</a></p>
				<div class="max_bid_layer">
					<div class="max_bid_close">x</div>
					<div class="cl" style="margin:5px 0 0 0;text-align:center;">최대경매가입찰</div>
					<div style="text-align:center;margin:5px 0 0 0;">
						<input type="text" name="max_bid" id="max_bid" size="20"></br>
						<p style="margin:10px 0 0 0;">
							<input type="button" value="입찰" class="max_bid_bn">
						</p>
					</div>
				</div>
				-->
			</div>
			<div id="sit_star_sns">
				<?php if ($star_score) {?>
				고객평점 <span>별<?=$star_score?>개</span>
				<img src="<?=G5_SHOP_URL;?>/img/s_star<?=$star_score?>.png" alt="" class="sit_star">
				<?php }?>
				<?=$sns_share_links;?>
			</div>
		</div>

		<?}?>

		<script type="text/javascript">
		$(document).ready(function(){
			<?if($now_max_bid_row[it_max_bid]){?>
			$(".max_bid_price").html("<?=$member[mb_id]?>님의 최대경매입찰가 : <?=number_format($now_max_bid_row[it_max_bid])?>원");
			<?}?>

			$(".max_biding_bn").click(function(){
				if($(".max_bid_layer").css("display") == "none"){
					$(".max_bid_layer").css("display", "block");
				}else{
					$(".max_bid_layer").css("display", "none");
				}
			});

			$(".max_bid_close").click(function(){
				$(".max_bid_layer").css("display", "none");
			});

			$(".max_bid_bn").click(function(){
				var it_id = "<?=$it_id?>";
				var ca_id = "1040";
				var it_max_bid = removeComma($("input[name='max_bid']").val());
				var old_ct_bid = $("input[name='ct_bid']").val()

				$.ajax({
					type: "POST",
					dataType: "JSON",
					url: "./_Ajax.auction.max.bid.php",
					data: "it_id=" + it_id + "&ca_id=" + ca_id + "&it_max_bid=" + it_max_bid,
					success: function(data){
						//$(".test").html(data);
						
						
						if(data.status == "no"){
							alert("종료된 경매입니다.");
							return false;
						}else if(data.status == "no1"){
							alert("현재 입력한 최고경매입칠가보다 더 높은 최대경매입찰가가 있습니다.");
							return false;
						}else if(data.status == "no2"){
							alert("<?=$member[mb_id]?>님께서 현재 입력한 최대경매가보다 높은 최고경매가가 있습니다.");
							return false;
						}else if(data.status == "no3"){
							alert("<?=$member[mb_id]?>님께서 현재 입력한 최대경매가보다 현재경매가가 높습니다.");
							return false;
						}else if(data.status == "no4"){
							alert("<?=$member[mb_id]?>님께서 현재 입력한 최대경매가보다 다음 입찰할 입찰가가 높습니다.");
							return false;
						}else{
							alert("최대경매가 입찰이 완료 되었습니다.");

							$(".max_bid_price").html("<?=$member[mb_id]?>님의 최대경매입찰가 : " + commaNum(data.max_bid_price) + "원</br><font color='blue'>현재 <?=$member[mb_id]?>님이 최대경매가입찰자입니다.</font>");
							$(".ct_bid").html(commaNum(data.bid_price) + " 원");
							$("input[name='ct_bid']").val(data.bid_price);
							//$("#sit_tot_price").empty().html("총 금액 : "+commaNum(data.it_last_bid)+"원");

							if(data.bid_price > old_ct_bid){
								$(".c_price_num").html(commaNum(data.it_last_bid)+' 원<input type="hidden" id="it_price" name="it_price" value="'+data.it_last_bid+'">');
								var sit_tot_price = parseInt(data.it_last_bid) + parseInt($("input[name='ct_bid']").val());
								$("#sit_tot_price").empty().html(commaNum(sit_tot_price)+"<span style='font-size:16px;'>원</span>");
							}

							$(".max_bid_layer").css("display", "none");
						}
						
						
					}
				});
			});

			$("input[name='max_bid']").keyup(function(){
				var val = removeComma($(this).val());
				$(this).val(commaNum(val));
			});

		});
		</script>


		<!-- 남은 시간 { -->
		<table style="border:0;margin:0;padding:15px;background:#f5f6f7;border:1px solid #cfcfcf;width:400px;font-size:15px">
			<tr>
				<td width="70px">남은 시간:</td>
				<td><div id="countDown" style="float:left;color:#ff4e00;font-weight:bold"></div></td>
				<td width="50px">수량</td>
				<td width="50px">1개</td>
			</tr>
			<input type="hidden" id="ct_qty" name="ct_qty[<?=$it_id;?>][]" value="1" class="frm_input" size="5">
		</table>
		
		<?php
		if($option_item) {
	   ?>
		<!-- 선택옵션 시작 { -->
		<section>
			<h3>선택옵션</h3>
			<table class="sit_ov_tbl">
			<colgroup>
				<col class="grid_3">
				<col>
			</colgroup>
			<tbody>
			<?php // 선택옵션
			echo $option_item;
		   ?>
			</tbody>
			</table>
		</section>
		<!-- } 선택옵션 끝 -->
		<?php
		}
	   ?>

		<?php
		if($supply_item) {
	   ?>
		<!-- 추가옵션 시작 { -->
		<section>
			<h3>추가옵션</h3>
			<table class="sit_ov_tbl">
			<colgroup>
				<col class="grid_3">
				<col>
			</colgroup>
			<tbody>
			<?php // 추가옵션
			echo $supply_item;
		   ?>
			</tbody>
			</table>
		</section>
		<!-- } 추가옵션 끝 -->
		<?php
		}
	   ?>

		<?php if ($is_orderable){?>
		<!-- 선택된 옵션 시작 { -->
		<section id="sit_sel_option" style="display:none;">
			<h3>선택된 옵션</h3>
			<?php
			//if(!$option_item) {
			//	if(!$it['it_buy_min_qty'])
			//		$it['it_buy_min_qty'] = 1;
		   ?>
			<ul id="sit_opt_added">
				<li class="sit_opt_list">
					<input type="hidden" name="io_type[<?=$it_id;?>][]" value="0">
					<input type="hidden" name="io_id[<?=$it_id;?>][]" value="">
					<input type="hidden" name="io_value[<?=$it_id;?>][]" value="<?=$it['it_name'];?>">
					<input type="hidden" class="io_price" value="0">
					<input type="hidden" class="io_stock" value="<?=$it['it_stock_qty'];?>">
					<span class="sit_opt_subj"><?=$it['it_name'];?></span>
					<span class="sit_opt_prc"></span>
					<div>
						<!--<input type="text" id="ct_qty" name="ct_qty[<?php// echo $it_id;?>][]" value="<?php// echo $it['it_buy_min_qty'];?>" class="frm_input" size="5">
						<button type="button" class="sit_qty_plus btn_frmline">증가</button>
						<button type="button" class="sit_qty_minus btn_frmline">감소</button>-->
					</div>
				</li>
			</ul>
			<script>
			$(function() {
				price_calculate();
			});
			</script>
			<?php// }?>
		</section>
		<!-- } 선택된 옵션 끝 -->

		
		<?php }?>

		<section style="padding:10px;">
			<table width="100%">
			<colgroup>
				<col class="grid_3">
				<col>
			</colgroup>
			<tbody>
				<tr>
					<td align="left">결제방식</td>
					<td align="right">
						<input type="radio" name="buy_status" value="무통장" checked>
						송금결제
					</td>
				</tr>
			</tbody>
			</table>
		</section>


		<div style="margin:0 0 15px 0;height:1px;border-top:1px #cfcfcf solid;"></div>


		<?php if($is_soldout) {?>
		<p id="sit_ov_soldout">상품의 재고가 부족하여 구매할 수 없습니다.</p>
		<?php }?>

		<div id="sit_ov_btn" >
			<?php// if ($is_orderable) {?>
				<?if($it[it_last_date] >= date("YmdHis")){?>

					<? if($member[mb_id]){?>
						<!--<input type="button" value="입찰하기" id="bid_btn_buy">-->
						<!--<input type="image" src="<?=G5_URL?>/img/bid_cart_bn.jpg" value="장바구니" onclick="document.pressed=this.value;" style="width:147px;height:60px;" id="sit_btn_cart">&nbsp;&nbsp;-->
						<!--img src="<?=G5_URL?>/img/bid_bn.jpg" border="0" align="absmiddle" id="bid_btn_buy" style="cursor:pointer;"-->
						<img src="<?=G5_URL?>/img/bid_bn.jpg" border="0" align="absmiddle">
						<!--<a href="javascript:popup_item_recommend('<?php// echo $it['it_id'];?>');" id="sit_btn_rec">추천하기</a>-->
						<img src="<?=G5_URL?>/img/item_wish_bn.gif" border="0" align="absmiddle" onclick="item_wish(document.fitem, '<?=$it['it_id'];?>')" style="cursor:pointer;margin:0 0 0 5px;">
					<? }?>

				<?}else{?>

					<?
					$auc_res = sql_query("select * from g5_shop_auction where it_id='$it_id' order by it_last_bid desc limit 0, 5 ");
					$auc_num = mysql_num_rows($auc_res);
					if($auc_num){
						for($i = 0; $i < $auc_num; $i++){
							$auc_row = mysql_fetch_array($auc_res);

							if($auc_row[loginid] == $member[mb_id]){
								$chk_id = "ok";
							}
						}

						if($chk_id){
					?>
							<?if($auc_row1[loginid] == $member[mb_id]){?>
								<!--<input type="image" src="<?=G5_URL?>/img/store_cart_btn.gif" onclick="document.pressed=this.value;" value="장바구니" class="input" />-->
								<input type="image" src="<?=G5_URL?>/img/store_buy_btn.gif" onclick="document.pressed=this.value;" value="바로구매" class="input" />
								<!--<input type="submit" onclick="document.pressed=this.value;" value="바로구매" id="sit_btn_buy">
								<input type="submit" onclick="document.pressed=this.value;" value="장바구니" id="sit_btn_cart">-->
								<img src="<?=G5_URL?>/img/item_wish_bn.gif" border="0" align="absmiddle" onclick="item_wish(document.fitem, '<?=$it['it_id'];?>')" style="cursor:pointer;margin:0 0 0 5px;">
							<?}else{?>
								경매가 종료 되었습니다.
							<?}?>
					<?
						}
					}else{
						$auc_res1 = sql_query("select * from g5_shop_item where it_id='$it_id' ");
						$auc_num1 = mysql_num_rows($auc_res1);

						for($i = 0; $i < $auc_num1; $i++){
							$auc_row1 = mysql_fetch_array($auc_res1);

							if($auc_row1[loginid] == $member[mb_id]){
								$chk_id = "ok";
							}
						}
						if($chk_id){
					?>
							<!--<input type="image" src="<?=G5_URL?>/img/bid_cart_bn.jpg" value="장바구니" onclick="document.pressed=this.value;" style="width:147px;height:60px;" id="sit_btn_cart">-->
							<input type="image" src="<?=G5_URL?>/img/bid_bn.jpg" onclick="document.pressed=this.value;" style="width:147px;height:60px;" id="sit_btn_buy">
							<!--<input type="submit" onclick="document.pressed=this.value;" value="바로구매" id="sit_btn_buy">
							<input type="submit" onclick="document.pressed=this.value;" value="장바구니" id="sit_btn_cart">-->
							<!--<a href="javascript:item_wish(document.fitem, '<?php// echo $it['it_id'];?>');" id="sit_btn_wish">위시리스트</a>-->
					<?
						}
					}
					?>
					
				<?}?>
			<?php// }?>

		</div>


	</section>
	<!-- } 상품 요약정보 및 구매 끝 -->

		<!-- 다른 상품 보기 시작 { -->
		<div id="sit_siblings">
			<?php
			if ($prev_href || $next_href) {
				echo $prev_href.$prev_title.$prev_href2;
				echo $next_href.$next_title.$next_href2;
			} else {
				echo '<span class="sound_only">이 분류에 등록된 다른 상품이 없습니다.</span>';
			}
		   ?>
		</div>
		<!-- } 다른 상품 보기 끝 -->
</div>

</form>

<div class="test"></div>


<script type='text/javascript' src='<?=G5_URL?>/js/jquery.timers-1.1.2.js'></script>
<script type="text/javascript">

var it_id = "<?=$it_id?>";
var loginid = "<?=$member[mb_id]?>";




var isFirst = true;
var endTime = new Date(<?=$end_date1?>);
//var nowTime = new Date(<?=$now_date?>);
//var timeLeft = (endTime - nowTime) / 1000;
//var timeSinceLast = 0;

showCountdown(endTime);

$(function(){

	/*
	$(this).everyTime(1000, "countTimer", function(){
		if (isFirst === true || timeLeft == -1 || (timeLeft < 300 && timeSinceLast >= 60)) {
			var nt = new Date().getTime();
			$.get("../timesync.php?nowtime=" + nt, function(data) {
				nowTime = eval('new Date(' + data + ')');
				timeLeft = (endTime - nowTime) / 1000;
			});
			isFirst = false;
			timeSinceLast = 0;
		}
		var outputString = '';
		if (timeLeft <= 0) {
			outputString = '종료';
		}else {
			var dayCount = Math.floor(timeLeft / 86400);
			var hourCount = Math.floor((timeLeft - dayCount * 86400) / 3600);
			var minCount = Math.floor((timeLeft - (dayCount * 86400) - (hourCount * 3600)) / 60);
			var secCount = timeLeft - (dayCount * 86400) - (hourCount * 3600) - (minCount * 60);
			 
			if (dayCount > 0) {
				outputString = dayCount + '일 ';
			}

			outputString += ((hourCount > 0) ? hourCount + ':' : '') + ((minCount < 10) ? '0' + minCount : minCount) + ':' + ((secCount < 10) ? '0' + secCount : secCount);
			timeLeft -= 1;
			timeSinceLast += 1;
		}
		$('#countDown').html(outputString);
	});
	*/




	$("#bid_btn_buy").click(function(){
		var ct_bid = Number($("input[name='ct_bid']").val());
		var it_price = 0;
		var ct_qty = 0;

		$("input[name^='ct_qty']").each(function(i){
			ct_qty += parseInt($("input[name^='ct_qty']").eq(i).val());
		});

		<?
		if($it[it_last_date] >= date("YmdHis") || $now_max_bid_row[it_max_bid] < $auc_row1[it_last_bid]){
		?>

		if(confirm("입찰하시겠습니까?")){
			
			$.ajax({
				type : "POST",
				dataType : "JSON",
				url : "./_Ajax.auction.bid.php",
				data : "it_id="+it_id+"&loginid="+loginid+"&ca_id=1040&loginid=<?=$member[mb_id]?>&ct_qty=" + ct_qty,
				success : function(data){
					//$(".test").html(data);
					
					
					if(data.status == "no"){
						alert("종료된 경매입니다.");
						return false;
					}else if(data.status == "no1"){
						alert("<?=$member[mb_id]?>님께서 최고경매가입니다.");
						return false;
					}else{
						if(data.status == "no2"){
							alert("입찰이 완료 되었고, 현재 최대경매입찰가보다 낮습니다.");
						}else{
							alert("입찰이 완료 되었습니다.");
						}
						$(".c_price_num").html(commaNum(data.it_last_bid)+'<input type="hidden" id="it_price" name="it_price" value="'+data.it_last_bid+'">');
						it_price = parseInt($("input[name='it_price']").val()) * 0.11;
						//$(".auc_td").html(it_bid + '<input type="hidden" id="it_price" value="'+it_bid+'">');
						$(".ct_bid").html(commaNum(data.bid_price) + " 원");
						$("input[name='ct_bid']").val(data.bid_price);
						$("#sit_tot_price").empty().html(commaNum(parseInt(data.it_last_bid) + parseInt(removeComma($(".ct_bid").html())))+"<span style='font-size:16px;'>원</span>");
					}
					
					
				}
			});
		}
		<?}else if($now_max_bid_row[it_max_bid] > $auc_row1[it_last_bid]){?>
		alert("이미 최대경매가입찰을 하셨습니다.");
		return false;
		<?}else if($it[it_last_date] < date("YmdHis")){?>
		alert("종료된 경매입니다.");
		return false;
		<?}?>
		
	});

	// 상품이미지 첫번째 링크
	$("#sit_pvi_big a:first").addClass("visible");

	// 상품이미지 미리보기 (썸네일에 마우스 오버시)
	$("#sit_pvi .img_thumb").bind("mouseover focus", function(){
		var idx = $("#sit_pvi .img_thumb").index($(this));
		$("#sit_pvi_big a.visible").removeClass("visible");
		$("#sit_pvi_big a:eq("+idx+")").addClass("visible");
	});

	/*
	// 상품이미지 크게보기
	$(".popup_item_image").click(function() {
		var url = $(this).attr("href");
		var top = 10;
		var left = 10;
		var opt = 'scrollbars=yes,top='+top+',left='+left;
		popup_window(url, "largeimage", opt);

		return false;
	});
	*/
});

function showCountdown(TimeSecond){

	day = Math.floor(TimeSecond / (3600 * 24));
	mod = TimeSecond % (24 * 3600);
	hour = Math.floor(mod / 3600);
	mod = mod % 3600;
	min = Math.floor(mod / 60);
	sec = mod % 60;
	count =  (day > 0) ? day + "일 " : "";
	count = count + hour + "시간 " + min + "분 " + sec + "초";
	$('#countDown').html(count);

	//# 남은 시간 소모 완료
	if (!TimeSecond){
		$('#countDown').html("종료");
	}else if(TimeSecond < 0){
		$('#countDown').html("종료");
	}else{
		TimeSecond2 = TimeSecond - 1; // -1초씩 표현
		setTimeout("showCountdown(TimeSecond2)", 1000); // 타임아웃 속도
	}
 
}


//공통함수 common_product.js 랑 중복.. 사용안할예정..
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
	var min_qty = parseInt(<?=$it['it_buy_min_qty'];?>);
	var max_qty = parseInt(<?=$it['it_buy_max_qty'];?>);
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
</script>


<script type="text/javascript" src="<?=G5_URL?>/js/common_product.js"></script>