<?
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>

<link rel="stylesheet" href="<?=G5_MSHOP_SKIN_URL?>/style.css">


<!-- div style="background:#fff;height:250px;padding:10px;">
	<h3 style="padding:15px"><img src="<?=G5_URL?>/img/regard_title.png" border="0" align="absmiddle"></h3>
	<?
	
// 	if ($default['de_rel_list_use']) { ?>
	<!-- 관련상품 시작 { -->
<!-- 	<section id="sit_rel"> -->
		<!--<h2>관련상품</h2>-->
<!-- 		<div class="sct_wrap"> -->
			<?
// 			$sql = " select b.* from {$g5['g5_shop_item_relation_table']} a left join {$g5['g5_shop_group_purchase_table']} b on (a.it_id2=b.gp_id) where a.it_id = '{$it['gp_id']}' and b.gp_use='1' ";
// 			$list = new item_list($default['de_rel_list_skin'], $default['de_rel_list_mod'], 0, $default['de_rel_img_width'], $default['de_rel_img_height']);
// 			$list->set_mobile(true);
// 			$list->set_query($sql);
// 			echo $list->run();
// 			?>
<!-- 		</div> -->
<!-- 	</section> -->
	<!-- } 관련상품 끝 -->
	<?
// 	 }
// 	 ?>
</div-->

<div style="background:#fff;">
<!-- 상품 정보 시작 { -->
<section id="sit_inf">
    <!--<h2>상품 정보</h2>-->

    <?=pg_anchor('inf')?>


		<? if ($it['gp_360img']) { // 상품 상세설명 ?>
			<div id="sit_inf_explan">
				<center>
					<?=conv_content($it['gp_360img'], 1)?>
				</center>
			</div>
		<? } ?>

    <? if ($it['gp_explan']) { // 상품 상세설명 ?>
    <div id="sit_inf_explan">
        <?=conv_content($it['gp_explan'], 1)?>
    </div>
    <? } ?>

</section>
<!-- } 상품 정보 끝 -->

<!-- 사용후기 시작 { -->
<section id="sit_use" >

    <!--<h2>사용후기</h2>-->
    <?=pg_anchor('use')?>

    <div id="itemuse"><? include_once('./gpitemuse.php')?></div>
</section>
<!-- } 사용후기 끝 -->

<!-- 상품문의 시작 { -->
<section id="sit_qa">
    <!--<h2>상품문의</h2>-->
    <?=pg_anchor('qa')?>

    <div id="itemqa"><? include_once('./gpitemqa.php')?></div>
</section>
<!-- } 상품문의 끝 -->

<? if ($default['de_baesong_content']) { // 배송정보 내용이 있다면 ?>
<!-- 배송정보 시작 { -->
<section id="sit_dvr" style="width:100%">
    <!--<h2>배송정보</h2>-->
    <?=pg_anchor('dvr')?>

	<div style="padding:30px 20px 10px 20px;">
    <?=conv_content($default['de_gpbaesong_content'], 1)?>
	</div>
</section>
<!-- } 배송정보 끝 -->
<? } ?>

</div>