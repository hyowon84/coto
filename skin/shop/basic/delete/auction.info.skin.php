<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

//echo $ca_name;		//카테고리명
?>

<link rel="stylesheet" href="<?php echo G5_SHOP_SKIN_URL; ?>/style.css">
<link rel="stylesheet" type="text/css" href="<?=G5_URL?>/css/slide.css">
<style type="text/css">
.sanchor{border-top:#545454;margin:-20px 0 0 -9px;width:798px;border-top:2px #545454 solid;}
.sanchor li{width:159px;}
.sanchor li a{width:158px;text-align:center;padding:0;background:#fff;}

#simpleSliderNext {
position:absolute;
margin:160px 0 0 768px;
font-weight: bold;
cursor: pointer;
z-index:10;
color:#fff;
}

#simpleSliderPrevious {
position: absolute;
margin:160px 0 0 -1px;
font-weight: bold;
cursor: pointer;
z-index:10;
color:#fff;
}

#simpleSliderNav {
position:absolute;
z-index:2;
padding: 20px;
margin-top: -50px;
margin-left:0;
}
/* Styling for the navigation items, this can also be anything we want, but I am using circles */
.simpleSliderNavItem {
border:1px #fff solid;
height: 8px;
width: 8px;
float: left;
background: url(/new_img/main/slide_dot.gif);
margin-left: 6px;
border-radius: 8px;
cursor: pointer;
}
/* styles for the active nav item */
.active {
border:1px #fff solid;
background-color:#fff;
}

li img {
border-radius: 2px;
}



.search_bar div{float:left;width:65px;}

</style>

<script src="<?php echo G5_JS_URL; ?>/iteminfoimageresize.js"></script>
<script type="text/javascript" src="<?=G5_JS_URL?>/jquery.simpleSlider.js"></script>
<script type="text/javascript">

$(document).ready(function(){
	$('#simpleSlider1').simpleSlider({
		interval: 3000,
		wantNav: true,
		navContainer: "#sliderContainer",
		pauseOnHover: true
	});
});

</script>

<div style="background:#fff;height:250px;padding:10px;">
	<h3 style="padding:15px 15px 15px 20px"><img src="<?=G5_URL?>/img/regard_title.png" border="0" align="absmiddle"></h3>
	<?php
	if ($default['de_rel_list_use']) { ?>
	<!-- 관련상품 시작 { -->
	<section id="sit_rel">
		<!--<h2>관련상품</h2>-->
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
</div>
<div style="height:10px;"></div>

<div style="background:#fff;height:540px;padding:10px;">
	<div style="height:60px;padding:25px 15px 10px 15px;">
		<div style="color:#545454;font-size:17px;font-weight:bold;">베스트 포토 갤러리</div>
		<div style="margin:23px 0 0 0;color:#545454;font-size:12px;">회원님들이 올려주신 사진 중 가장 인기가 많은 사진을 선정하여 베스트 갤러리에 올려집니다.</div>
	</div>
	<!-- 포토갤러리 슬라이드 시작 -->
	<div style="height:390px;margin:0 -10px 0 -10px;">
		
		<!-- 좌우 allow 시작 -->
		<div id="simpleSliderPrevious"><img src="<?=G5_URL?>/img/left_arrow.png" border="0" align="absmiddle"></div>
		<div id="simpleSliderNext"><img src="<?=G5_URL?>/img/right_arrow.png" border="0" align="absmiddle"></div>
		<!-- 좌우 allow 끝 -->
		<div id="sliderContainer" style="height:350px;">
			<ul id="simpleSlider1" style="height:350px;">

				<?
				$store_res = sql_query("select * from g5_store_view_slide order by no desc limit 0, 20");

				for($i = 0; $i < $store_row = mysql_fetch_array($store_res); $i++){
				?>
					<li style="float:left;position:absolute;cursor:pointer;width:100%;height:350px;" onclick="goto_url('<?=G5_URL?>/bbs/board.php?bo_table=photo_gallery');"><img src="../data/store_view_slide/<?=$store_row[img_file]?>" border="0" align="absmiddle" style="width:798px;height:350px;"></li>
				<?
				}
				?>

			</ul>
			
		</div>
		<div style="margin:10px 0 0 20px;color:#545454;font-size:12px;">※ 베스트 갤러리에 뽑히신 회원님에게는 소정의 사은품을 증정합니다.
</div>
	</div>
	<!-- 포토갤러리 슬라이드 끝 -->
	<div style="margin:0px 0 0 0;">
		<div style="float:right;width:130px;height:15px;padding:15px;text-align:center;border:1px #cfcfcf solid;cursor:pointer;font-size:15px" onclick="goto_url('<?=G5_URL?>/bbs/board.php?bo_table=photo_gallery');">포토갤러리 가기 ></div>
	</div>
</div>
<div style="height:10px;"></div>

<div style="background:#fff;">

<!-- 상품 정보 시작 { -->
<div id="sit_inf">
    <!--<h2>상품 상세 정보</h2>-->
    
	<?php echo pg_anchor('inf'); ?>
    <!--<h3>상품 기본설명</h3>-->
    <?php if ($it['it_basic']) { // 상품 기본설명 ?>
    <div id="sit_inf_basic">
         <?php echo $it['it_basic']; ?>
    </div>
    <?php } ?>

    <!--<h3>상품 상세설명</h3>-->
    <?php if ($it['it_explan']) { // 상품 상세설명 ?>
    <div id="sit_inf_explan">
        <?php echo conv_content($it['it_explan'], 1); ?>
    </div>
    <?php } ?>

    <!--<h3>상품 정보 고시</h3>-->
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

</div>
<!-- } 상품 정보 끝 -->

<!-- 상품문의 시작 { -->
<section id="sit_qa">
    <!--<h2>상품문의</h2>-->
    <?php echo pg_anchor('qa'); ?>

    <div id="itemqa"><?php include_once('./itemqa.php'); ?></div>
</section>
<!-- } 상품문의 끝 -->

<!-- 사용후기 시작 { -->
<section id="sit_use">
    <!--<h2>사용후기</h2>-->
    <?php echo pg_anchor('use'); ?>

    <div id="itemuse"><?php include_once('./itemuse.php'); ?></div>
</section>
<!-- } 사용후기 끝 -->

<?php if ($default['de_baesong_content']) { // 배송정보 내용이 있다면 ?>
<!-- 배송정보 시작 { -->
<section id="sit_dvr">
    <!--<h2>배송정보</h2>-->
    <?php echo pg_anchor('dvr'); ?>

	<div style="padding:30px 20px 10px 20px;">
    <?php echo conv_content($default['de_baesong_content'], 1); ?>
	</div>
</section>
<!-- } 배송정보 끝 -->
<?php } ?>


<?php if ($default['de_change_content']) { // 교환/반품 내용이 있다면 ?>
<!-- 오류신고 시작 { -->
<section id="sit_ex">
    <!--<h2>오류신고</h2>-->
    <?php echo pg_anchor('ex'); ?>

    <?php// echo conv_content($default['de_change_content'], 1); ?>
	<div id="itemex"><?php include_once('./itemex.php'); ?></div>
</section>
<!-- } 오류신고 끝 -->
<?php } ?>

</div>

<script>
$(window).on("load", function() {
    $("#sit_inf_explan").iteminfoimageresize();
});
</script>