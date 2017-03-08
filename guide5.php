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
<div id="company">

		<div id="sub_cate">
			<ul>
				
				<li><a href="<?=G5_URL?>/guide1.php">회사소개</a></li>
				<li><a href="<?=G5_URL?>/guide2.php">블리언코인</a></li>
				<li><a href="<?=G5_URL?>/guide3.php">공동구매</a></li>
				<li><a href="<?=G5_URL?>/guide4.php">구매,배송대행</a></li>
				<li><a href="<?=G5_URL?>/guide5.php"<?php if(strtolower(basename($PHP_SELF))=="guide5.php") echo " class='sub_cate_on'";?>>포트폴리오</a></li>
				<li><a href="<?=G5_URL?>/guide6.php">멤버쉽</a></li>
				
			</ul>
		</div>


		<div id="sub_title">
			<li><img src="img/home_bn.png"></li>
			<li><img src="img/arrow_bn.png"></li>
			<li>About</li>
			<li><img src="img/arrow_bn.png"></li>
			<li>포트폴리오</li>
		</div>



		<li id="sub_content_title">	
			회사소개
		</li>
		<li id="sub_content">
			
Coin's Today는 귀금속을 태생으로 한 각종 주화(Coin)와 바(Bar)를 다루는 회사로서 투자와 수집을 원하는 소비자들에게 특화된 서비스를 제공하고 있는 회사 입니다.
일반적인 판매는 물론 국내에서 발행되지 않는 다양한 Coin들을 회원이 쉽고 합리적인 가격에 접할 수 있도록 공동구매, 구매 대행을 진행하고 있으며 모든 회원들의 원활한 수집 겸 재테크 활동을 위하여 배송지 대행, 판매 대행, 그레이딩 대행 그리고 금고 보관과 개인 포트폴리오 서비스를 제공하고 있습니다. Coin's Today는 귀금속 실물 투자와 수집을 하는 모든 소비자에 최적화된 서비스를 제공해 드리고 있는 회사입니다. 금화 은화 수집을 비롯하여 실물투자에 관심이 있었던 분들이라면 저희 Coin's Today에서 새로운 세상으로 접해 보시길 바랍니다.

		
		</li>

		<li id="sub_content_title">	
			귀금속이란?
		</li>
		<li id="sub_content">
			
'금이야 옥이야', '금을 돌같이 보라' 는 속담이 있듯이 금은 고대부터 가장 귀하게 여기고 좋아하던 금속입니다. B.C 5000년 전부터 발굴 되었던 것으로 추정하고 있으며 B.C 3600년부터 첫 금 제련 작업을 시작하게 되어 이집트, 로마와 바빌론 제국 건설에 큰 역할을 하였습니다. 금과 은은 오랜 기간 동안 그 자연적인 아름다움과 더럽혀지지 않는 불변성, 그리고 금과 은이 갖고 있는 특이 성질(전성, 연성)로 인해 '귀'금속으로(Precious Metal) 불리었으며 부의 상징, 절대 화폐로 세계에서 통용 되어 현대의 경제의 기반을 구축하고 현대 산업에서 없어서는 안될 금속으로 자리매김 하였습니다. 금, 은 , 백금 및 팔라듐과 같은 귀금속은 반지 목걸이와 같은 장식품, 장신구 뿐만 아니라 전자 물품, 자동차 부품, 의료, 심지어 우주 산업에도 사용되는 금속입니다. 귀금속의 특성과 그 가치를 알고 있는 것이 귀금속 수집 및 투자 활동을 하는 여러분께 큰 도움이 될 것입니다.
</li>


		<li id="sub_content_title">	
			금(金, gold) 
		</li>
		<li id="sub_content">
			
원자번호 79번의 원소로, 원소기호는 Au입니다. 구리, 은, 금을 통틀어 구리족 원소라 하기도 하고 또 화폐 금속(coinage metal)이라고도 부르는데, 이들은 고대부터 자연에서 원소상태로 채취한 금속으로, 금화가 사용되기 전부터 화폐로 사용되었습니다. 특히 금은 인류 역사의 시작과 더불어 화폐 가치의 기준이 되어왔는데 우리가 수표 등에 돈의 액수를 적을 때, 금(金) XXX 원 이라 적는 것도 이를 보여주는 예 입니다. 금은 화학 반응이 매우 작은 고체 원소 중 하나 입니다. 공기나 물에 의해 부식되지 않고 원래의 상태를 유지하며 전성(두들겨 펴지기 쉬운 성질)과 연성(잡아 늘이기 쉬운 성질)이 매우 크기 때문에 (금속 원소 중에서 가장 큼) 얇은 박(箔, foil)이나 선(또는 실, wire)으로 용이하게 가공 됩니다. 천연의 노란색과 붉은 색을 잘 반사하여 밝은 노란색을 띄어 사람들이 좋아한다고 합니다. 이런 이유로 석기시대 후반부터 사람들은 금을 가장 고귀한 금속으로 여기고 갖기를 원했습니다. 금이 원소 중의 하나라는 사실이 알려지지 않은 시대에는 값싼 금속이나 다른 물질로부터 금을 만들려는 연금술이 거의 1000년 이상 유행하였고 일부 사람들은 실제로 금을 만들었다고 속임수를 쓰기도 하였습니다.
</li>


</div>


       



		




<?php
include_once('./_tail.php');
?>
