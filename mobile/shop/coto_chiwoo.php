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

<div style="width:100%">
	<a href="http://www.coinstoday.co.kr/shop/grouppurchase.php?gp_id=KOR17_SM1z_CHIWOO">
		<img class="slick-slide-img" src="/image.php?image=/data/main_img/image 11(1)_1497330369.jpg" width="100%" draggable="false">
	</a>
</div>

<div>
<? include_once(G5_MSHOP_PATH . '/tail.php'); ?>
</div>
