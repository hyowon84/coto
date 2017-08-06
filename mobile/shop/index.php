<?php
    include_once('./_common.php');
    define("_INDEX_", TRUE);
    include_once(G5_MSHOP_PATH.'/head.php');
?>


<link rel="stylesheet" type="text/css" href="<?= G5_URL ?>/mobile/css/flexslider.css">
<script type="text/javascript" src="<?= G5_URL ?>/mobile/js/jquery.flexslider.js"></script>
<script src="<?php echo G5_JS_URL; ?>/shop.mobile.main.js"></script>


<link rel="stylesheet" type="text/css" href="/js/slick/slick.css"/>
<link rel="stylesheet" type="text/css" href="/js/slick/slick-theme.css"/>
<script type="text/javascript" src="/js/slick/slick.min.js"></script>
<style type="text/css">
	html, body {
		margin: 0;
		padding: 0;
	}

	* {
		box-sizing: border-box;
	}

	.slider {
		width: 50%;
		margin: 100px auto;
	}

	.slick-slide {
		margin: 0px 20px;
	}

	.slick-slide img {
		/*width: 1100px*/
	}

	.slick-prev:before,
	.slick-next:before {
		color: black;
	}

	/*.slide_contain { width:1100px; }*/
</style>

<?


/* 메인화면 슬라이드 */
makeMainSlideImage();

/* 경매상품 목록 */
makeHtmlAucPrdList();

/* 진행중인 공동구매 상품목록 */
include_once "./data/html/mainGpItemList_m.html";

/* 카테고리 상품목록 */
include_once "./data/html/mainCateItemList_m.html";
?>

<script src='<?=G5_JS_URL?>/imgLiquid.js'></script>
<script>
$(document).on('ready', function() {
	$(".slide").attr('style','display:block;');
	$('.imgLiquidNoFill').imgLiquid({fill:false});
});
</script>

<div>
<? include_once(G5_MSHOP_PATH . '/tail.php'); ?>
</div>
