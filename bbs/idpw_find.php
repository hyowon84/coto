<?php
define('_INDEX_', true);
include_once('./_common.php');

// 초기화면 파일 경로 지정 : 이 코드는 가능한 삭제하지 마십시오.
if ($config['cf_include_index']) {
    if (!@include_once($config['cf_include_index'])) {
        die('기본환경 설정에서 초기화면 파일 경로가 잘못 설정되어 있습니다.');
    }
    return; // 이 코드의 아래는 실행을 하지 않습니다.
}

// 루트 index를 쇼핑몰 index 설정했을 때
if(isset($default['de_root_index_use']) && $default['de_root_index_use']) {
    require_once(G5_SHOP_PATH.'/index.php');
    return;
}

if (G5_IS_MOBILE) {
    include_once(G5_MOBILE_PATH.'/index.php');
    return;
}

include_once('./_head.php');
?>

<link rel="stylesheet" type="text/css" href="<?=G5_URL?>/css/slide.css">
<script type="text/javascript" src="<?=G5_URL?>/js/jquery.simpleSlider.js"></script>

<style>
#wrapper {
width: 100%;
margin: 0 auto;
position:absolute;
z-index:50002;
}

/*#mainBackground .rolling<?=$i+1?>{ width:100%; height:100%; background:url(data/main_img/<?=$main_slide_row[img_file]?>) left; background-repeat:no-repeat; z-index:<?=$i+1?>;cursor:pointer; }*/
</style>

<div id="mainCover">
<div id="mainBackground">
  <!-- Main container -->

	<div style="margin:160px 0 0 0;">
		<!-- 좌우 allow 시작 -->
		<!--<div id="simpleSliderPrevious"><img src="/new_img/main/arrow_left.gif" /></div>
		<div id="simpleSliderNext"><img src="/new_img/main/arrow_right.gif" /></div>-->
		<!-- 좌우 allow 끝 -->
		<div id="sliderContainer">
			<ul id="simpleSlider">

				<?
				$main_slide_res = sql_query("select * from g5_main_slide order by no desc ");
				$main_slide_num = mysql_num_rows($main_slide_res);
				for($i = 0; $i < $main_slide_num; $i++){
					$main_slide_row = mysql_fetch_array($main_slide_res);
				?>
					<li style="cursor:pointer;width:100%;height:100%;background:url(data/main_img/<?=$main_slide_row[img_file]?>) left top;background-repeat:repeat-y;" onclick="goto_url('<?=$main_slide_row1[URL]?>');"></li>
				<?
				}
				?>

			</ul>
			<div style="position:absolute;z-index:50000;margin:-150px 0 0 220px;"><img src="<?=G5_URL?>/img/view_more.png" border="0" align="absmiddle"></div>
		</div>
	</div>

		
      
</div>
</div>

<script type="text/javascript">

$(document).ready(function(){
	$('#simpleSlider').simpleSlider({
		interval: 3000,
		wantNav: true,
		navContainer: "#sliderContainer",
		pauseOnHover: true
	});
});

</script>
           
       



		




<?php
include_once('./_tail.php');
?>
