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

<div id="aside2"></div>

<!--<div id="sub_title2" ><span>서비스가이드</span>
	<ul>
		<li><img src="img/home_bn.png"></li>
			<li><img src="img/arrow_bn.png"></li>
			<li>About</li>
			<li><img src="img/arrow_bn.png"></li>
			<li>서비스가이드</li>
	</ul>
</div>-->

<div id="sub_title2" ><span>서비스가이드</span>
	<ul>
		<li>홈</li>	
		<li>></li>
		<li>About</li>
		<li>></li>
		<li>서비스가이드</li>
	</ul>
</div>


<div id="company">

		


		<div id="sub_cate2">
			<ul>
				
				<li><a href="#guide2_1">Coin's store</a></li>
				<li><a href="#guide2_1">공동구매</a></li>
				<li><a href="#guide2_3">구매대행</a></li>
				<li><a href="#guide2_3">배송대행</a></li>
				<li><a href="#guide2_4">그레이딩</a></li>
				<li><a href="#guide2_5">위탁판매</a></li>
				<li><a href="#guide2_6">보관서비스</a></li>
				<li><a href="#guide2_6">포트폴리오</a></li>
			

				
			</ul>
		</div>
	

		
		<li id="sub_content">	
			<img src="img/guide2_1.png" id="guide2_1" width="100%">

			
		</li>

</div>


       



		




<?php
include_once('./_tail.php');
?>
