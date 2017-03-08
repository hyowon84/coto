<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>
    </div> <!--head.php  <div id="container">-->
</div>

<hr>

<div id="ft" style="clear:both;width:100%">
<!--
	 <div id="ft_catch2">
		<li>회사소개</li>

		<li>이용약관</li>

		<li>개인정보취급방침</li>

		<li>CONTACT US</li>
	</div>

<? //echo popular('basic'); // 인기검색어 ?>
<? //echo visit('basic'); // 방문자수 ?> 

-->

    <div id="ft_copy" style="padding:10px 5px;">
        <li style="list-style:none;padding-top:5px;padding:5px;color:#dcdcdc;font-size:0.917em;letter-spacing: -0.05em;text-align:left">
        <b>회사명</b> 코인즈투데이 <b>대표</b> 박민우<br/>
        서울특별시 강남구 밤고개로14길 13-34(자곡동 274)<br/>
        <b>통신판매업신고</b> 제2014-경기양주- 0130호<br/>
        <b>사업자등록번호</b> 127-46-73320</span><br/><br/>
        <b>Tel</b> 070-4323-6999,6998  <b>Fax</b> 070-8230-0777</span><br/>
        <b>이메일</b> minwoo@coinstoday.co.kr</span><br/><br/>
        <b>개인정보관리책임자</b> 박민우<br/>
        <b>전자상거래 소비자보호 법률에 따른 구매 안전 서비스 안내</b><br/>
        <span>본 판매자는 KB국민은행과 계약을 통해 </span><br/>
        <span>구매 안전 서비스를 자동으로 제공중입니다.</span><br/><br/><a href="http://escrow1.kbstar.com/quics?page=B009111&cc=b010807%3Ab008491&mHValue=53c33b910404b57b879243764e930592201405151656495#" target="_blank" style="color:#fff;background:#707070;border-radius:5px;padding:1px 5px 1px 5px;">서비스 가입사실 확인 </a><br/><br/>
        Copyright &copy; <b>coinstoday.co.kr</b> All rights reserved.
		</li>
    </div>
</div>

<?php
if (G5_USE_MOBILE && G5_IS_MOBILE) {
    $seq = 0;
    $href = $_SERVER['PHP_SELF'];
    if ($_SERVER['QUERY_STRING']) {
        $sep = '?';
        foreach ($_GET as $key => $val) {
            if ($key == 'device')
                continue;

            $href .= $sep . $key . '=' . $val;
            $sep = '&amp;';
            $seq++;
        }
    }
    if ($seq)
        $href .= '&amp;device=pc';
    else
        $href .= '?device=pc';
    ?>
<!--    <a href="--><?php //echo $href; ?><!--" id="device_change">PC 버전으로 보기</a>-->
<?php
}

if ($config['cf_analytics']) {
    echo $config['cf_analytics'];
}

include_once(G5_PATH . "/tail.sub.php");
?>