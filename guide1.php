<?php
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
    require_once(G5_SHOP_PATH.'./index.php');
    return;
}

if (G5_IS_MOBILE) {
	include_once(G5_MSHOP_PATH.'/head.php');
} else {
    include_once('./_head.php');
}

?>

<div id="sub_title2" style="width:100%;"><h1>회사소개</h1>
<!--	<ul>-->
<!--		<li>홈</li>-->
<!--		<li>></li>-->
<!--		<li>About</li>-->
<!--		<li>></li>-->
<!--		<li>회사소개</li>-->
<!--	</ul>-->
</div>

<div id="aside2"></div>


<div id="company">
		<!--<ul>
			<li id="sub_content_title">
				회사소개
			</li>
			<li id="sub_content">
	Coin's Today는 귀금속을 태생으로 한 각종 주화(Coin)와 바(Bar)를 다루는 회사로서 투자와 수집을 원하는 소비자들에게 특화된 서비스를 제공하고 있는 회사 입니다.
	일반적인 판매는 물론 국내에서 발행되지 않는 다양한 Coin들을 회원이 쉽고 합리적인 가격에 접할 수 있도록 공동구매, 구매 대행을 진행하고 있으며 모든 회원들의 원활한 수집 겸 재테크 활동을 위하여 배송지 대행, 판매 대행, 그레이딩 대행 그리고 금고 보관과 개인 포트폴리오 서비스를 제공하고 있습니다. Coin's Today는 귀금속 실물 투자와 수집을 하는 모든 소비자에 최적화된 서비스를 제공해 드리고 있는 회사입니다. 금화 은화 수집을 비롯하여 실물투자에 관심이 있었던 분들이라면 저희 Coin's Today에서 새로운 세상으로 접해 보시길 바랍니다.
			</li>

		</ul>

		<li id="sub_content_right">
			aaa
		</li>


		<li id="sub_content_title">
			● 질문과 답변
		</li>

		<li id="sub_content2">
			<?
				echo "<iframe id=iframe onLoad=\"calcHeight();\"   src=\"../../~cointoday/bbs/board.php?bo_table=qa\" scrolling=no frameborder=0 width=100% height=0></iframe>";
			?>
		</li>-->


		<li id="sub_content" style="width:100%;">
			<?php if (G5_IS_MOBILE) {?>
				<img src="img/m/aboutUs.png" style="width:100%;">
			<?php } else { ?>
				<img src="img/guide1.png" style="width:100%;">
			<?php
			}
			?>
		</li>
</div>











<?php
    if (G5_IS_MOBILE) {
        include_once(G5_MSHOP_PATH.'/tail.php');
    } else {
        include_once('./_tail.php');
    }
?>
