<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>

<script src="<?php echo G5_JS_URL; ?>/jquery.nicescroll.min.js"></script>
<script src="<?php echo G5_JS_URL; ?>/jquery.fancyalert.js"></script>
<script src="<?php echo G5_JS_URL; ?>/jquery.cfSlider.js"></script><!-- https://github.com/codefactory/cfSlider -->
<script src="<?php echo G5_JS_URL; ?>/jquery.cfSlidePanel.js"></script>



<form name="fitem" action="<?php echo $action_url; ?>" method="post" onsubmit="return fitem_submit(this);">
<input type="hidden" name="it_id[]" value="<?php echo $it['it_id']; ?>">
<input type="hidden" name="sw_direct">
<input type="hidden" name="url">

<!-- 경매상품 상세페이지 스킨 -->

<div>
	
	<!-- 상품이미지 -->
	<section id='slider'>
		<!-- 1상품이미지 미리보기 시작 { item.form.auction.skin -->
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
		
	<!-- 경매정보 -->
	<section class="itemdtl_box">
		<table width='100%' align='center'>
			<tr>
				<td class='itemdtlauc_tit' valign="middle">
					<div class='itemdtlauc_statusbox'>경매중</div>
				</td>
				<td class='itemdtlauc_val' valign="middle" rowspan=2>
					<div>종료일시 2015년 06월 01일 01시 00분 00초</div>
					<div class='itemdtlauc_limit_time'>남은시간: <span>85일 2시간 17분 22초</span></div>
				</td>
			</tr>
			<tr>
				<td class='itemdtlauc_tit' valign="middle">&nbsp;</td>
			</tr>
			<tr><td colspan='2' class='itemdtl_tr_1pxline'></td></tr>
			
			<tr>
				<td class='itemdtl_tit' valign="middle">수량   1개</td>
				<td class='itemdtl_val' valign="middle">현재입찰가 입찰 : 3 bid </td>
			</tr>
			
		</table>
	</section>
	
	
	<!-- 최대입찰희망가 -->
	<section class="itemdtl_box">
		<table width='100%' style='padding-bottom:13px;'>
			<tr>
				<td height='40' valign="top" class='aucinfo_row' colspan='2' style='font-size:1.000em'>최대입찰희망가</td>
			</tr>
			<tr>
				<td class='itemdtlauc_inputnum_qty'><input type='text' class='itemdtlauc_inputnum' />원</td>
				<td class=''><button id='maxipchal'>최대경매가입찰</button></td>
			</tr>
			<tr>
				<td class='bbline1px_dc' colspan='2' height='50' style='font-size:0.833em'>
				님의 최대경매입찰가 : <span style='color:#345ee7'>500원</span><br>
				현재 님이 최대경매가 입찰자입니다
				</td>
			</tr>
			<tr>
				<td colspan='2' height='48' align='right' valign="bottom"><span class='fontst_bwp_txt'>예상구매금액</span> <span class='fontst_bwp_val'>400원</span></td>
			</tr>
		</table>
	</section>
	
	
	
	<section class="itemdtl_box3">
		<aside id="sit_siblings">
			<h2>다른 상품 보기</h2>
			<?php
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