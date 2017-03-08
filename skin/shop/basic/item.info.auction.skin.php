<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

//echo $ca_name;		//카테고리명
?>

<link rel="stylesheet" href="<?php echo G5_SHOP_SKIN_URL; ?>/style.css">
<script src="<?php echo G5_JS_URL; ?>/iteminfoimageresize.js"></script>

<!-- 상품 정보 시작 { -->
<section id="sit_inf">
	<h2>상품 정보</h2>
	<?php echo pg_anchor('inf'); ?>

	<h3>상품 기본설명</h3>
	<?php if ($it['it_basic']) { // 상품 기본설명 ?>
	<div id="sit_inf_basic">
		 <?php echo $it['it_basic']; ?>
	</div>
	<?php } ?>

	<h3>상품 상세설명</h3>
	<?php if ($it['it_explan']) { // 상품 상세설명 ?>
	<div id="sit_inf_explan">
		<?php echo conv_content($it['it_explan'], 1); ?>
	</div>
	<?php } ?>

	<h3>상품 정보 고시</h3>
	<?php
	if ($it['it_info_value']) {
		$info_data = unserialize($it['it_info_value']);
		$gubun = $it['it_info_gubun'];
		$info_array = $item_info[$gubun]['article'];
	?>
	<!-- 상품정보고시 -->
	<table id="sit_inf_open">
	<colgroup>
		<col class="grid_4">
		<col>
	</colgroup>
	<tbody>
	<?php
	foreach($info_data as $key=>$val) {
		$ii_title = $info_array[$key][0];
		$ii_value = $val;
	?>
	<tr>
		<th scope="row"><?php echo $ii_title; ?></th>
		<td><?php echo $ii_value; ?></td>
	</tr>
	<?php } //foreach?>
	</tbody>
	</table>
	<!-- 상품정보고시 end -->
	<?php } //if?>

</section>
<!-- } 상품 정보 끝 -->

<!-- 사용후기 시작 { -->
<section id="sit_use">
	<h2>사용후기</h2>
	<?php echo pg_anchor('use'); ?>

	<div id="itemuse"><?php include_once('./itemuse.php'); ?></div>
</section>
<!-- } 사용후기 끝 -->

<!-- 상품문의 시작 { -->
<section id="sit_qa">
	<h2>상품문의</h2>
	<?php echo pg_anchor('qa'); ?>

	<div id="itemqa"><?php include_once('./itemqa.php'); ?></div>
</section>
<!-- } 상품문의 끝 -->

<?php if ($default['de_baesong_content']) { // 배송정보 내용이 있다면 ?>
<!-- 배송정보 시작 { -->
<section id="sit_dvr">
	<h2>배송정보</h2>
	<?php echo pg_anchor('dvr'); ?>

	<?php echo conv_content($default['de_baesong_content'], 1); ?>
</section>
<!-- } 배송정보 끝 -->
<?php } ?>


<?php if ($default['de_change_content']) { // 교환/반품 내용이 있다면 ?>
<!-- 교환/반품 시작 { -->
<section id="sit_ex">
	<h2>교환/반품</h2>
	<?php echo pg_anchor('ex'); ?>

	<?php echo conv_content($default['de_change_content'], 1); ?>
</section>
<!-- } 교환/반품 끝 -->
<?php } ?>

<?php if ($default['de_rel_list_use']) { ?>
<!-- 관련상품 시작 { -->
<section id="sit_rel">
	<h2>관련상품</h2>
	<?php echo pg_anchor('rel'); ?>

	<div class="sct_wrap">
		<?php
		$sql = " select b.* from {$g5['g5_shop_item_relation_table']} a left join {$g5['g5_shop_item_table']} b on (a.it_id2=b.it_id) where a.it_id = '{$it['it_id']}' and b.it_use='1' ";
		$list = new item_list($default['de_rel_list_skin'], $default['de_rel_list_mod'], 0, $default['de_rel_img_width'], $default['de_rel_img_height']);
		$list->set_query($sql);
		echo $list->run();
		?>
	</div>
</section>
<!-- } 관련상품 끝 -->
<?php } ?>

<script>
$(window).on("load", function() {
	$("#sit_inf_explan").iteminfoimageresize();
});
</script>