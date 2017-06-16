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

//사용안함
if(isset($default['de_root_index_use']) && $default['de_root_index_use']) {
    require_once(G5_SHOP_PATH.'/index.php');
    return;
}


//일반웹 접속
include_once(G5_PATH.'/_head.php');
?>

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
		margin: 0px 0px;
	}
	
	.slick-slide-img {
		width: 1070px;
		height: 540px;
	}

	.slick-prev:before,
	.slick-next:before {
		color: black;
	}
	
	/*.slide_contain { width:1100px; }*/
</style>
<div>
	<a href="http://www.coinstoday.co.kr/shop/grouppurchase.php?gp_id=KOR17_SM1z_CHIWOO" tabindex="0"><img class="slick-slide-img" src="/image.php?image=/data/main_img/image 11(1)_1497330369.jpg" height="550" title=""></a>
</div>
<script src='<?=G5_JS_URL?>/imgLiquid.js'></script>
<script>
$(document).on('ready', function() {
	
	$(".slide").slick({
		autoplay: true,
		autoplaySpeed: 6000,
		dots: true,
		infinite: true,
		variableWidth: true
	});

	/*로딩이 끝나면 보여주기*/
	$(".slide").attr('style','display:block;');
	
	$('.imgLiquidNoFill').imgLiquid({fill:false});
	
});
</script>


<?php
include_once('./_tail.php');
?>