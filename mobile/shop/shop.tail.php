<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>
    </div> <!--head.php  <div id="container">-->
</div>

<hr>

<div id="ft" style="clear:both;width:100%">
	 <div id="ft_catch2">
		<li>회사소개</li>

		<li>이용약관</li>

		<li>개인정보취급방침</li>

		<li>CONTACT US</li>
	</div>

    <!--<?php echo popular('basic'); // 인기검색어 ?>
<?php echo visit('basic'); // 방문자수 ?>-->
    <div id="ft_copy">
        <li style="list-style:none;padding-top:5px;padding:5px;color:#707070;font-size:0.62em;letter-spacing:-0.1em;text-align:center">
        회사명: 코인즈투데이 | 대표: 박민우<br/>
        서울특별시 강남구 밤고개로14길 13-34(자곡동 274)<br/>
        통신판매업신고: 제2014-경기양주- 0130호 | 사업자등록번호:127-46-73320<br/>
        Tel: 070-4323-6999,6998 | Fax: 070-8230-0777 | 이메일: minwoo@coinstoday.co.kr<br/>개인정보관리책임자: 박민우<br/>
        전자상거래 소비자보호 법률에 따른 구매 안전 서비스 안내 : 본 판매자는 KB국민은행과 계약을 통해 구매 안전 서비스를 자동으로 제공중입니다.<br/> <a href="#" onclick="onPopKBAuthMark();return false;" style="color:#fff;background:#707070;border-radius:5px;padding:1px 5px 1px 5px;">서비스 가입사실 확인</a><br/>
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
    <a href="<?php echo $href; ?>" id="device_change">PC 버전으로 보기</a>
<?php
}

if ($config['cf_analytics']) {
    echo $config['cf_analytics'];
}

include_once(G5_PATH."/tail.script.php");
include_once(G5_PATH."/tail.sub.php");
?>