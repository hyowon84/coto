<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if(!$member['mb_id']){

	$url = $_SERVER["REQUEST_URI"];

	$p = parse_url($url);
	if ((isset($p['scheme']) && $p['scheme']) || (isset($p['host']) && $p['host'])) {
		//print_r2($p);
		if ($p['host'].(isset($p['port']) ? ':'.$p['port'] : '') != $_SERVER['HTTP_HOST'])
			alert('url에 타 도메인을 지정할 수 없습니다.');
	}

	$login_url        = login_url($url);
}
$login_action_url = G5_HTTPS_BBS_URL."/login_check.php";
?>

<div class="test"></div>
<!-- 로그인 시작 { -->
<link rel="stylesheet" href="<?php echo $member_skin_url ?>/style.css">

<div id="mb_login" class="mbskin" style="padding:10px 0;">
    <h1 style="margin:10px 0 0 0;text-align:center;"><img src="<?php echo G5_URL ?>/img/login_tit.gif"></h1>

    <form name="flogin" action="<?php echo $login_action_url ?>" onsubmit="" method="post">
    <input type="hidden" name="url" value='<?php echo $login_url ?>'>

    <div id="login_fs">
        <div style="margin:-12px 0 0 0;">이메일은 @이하 주소까지 모두 입력해 주세요</div>
		<div style="margin:10px 0 0 0;">
			
			<div><input type="text" name="mb_id" id="login_id" required class="frm_input" value="" placeholder="아이디@이메일주소"></div>
			<div style="margin:-6px 0 0 0;"><input type="password" name="mb_password" id="login_pw" required class="frm_input" value="" placeholder="비밀번호"></div>
			
		</div>
		<div style="margin:10px 0 0 0;">
			<input type="checkbox" name="auto_login" id="login_auto_login">
			<label for="login_auto_login">로그인 상태 유지</label>
		</div>
    </div>

	<div style="margin:10px 0 0 0;text-align:center;">

		<img src="<?=G5_URL?>/img/login_bn.gif" border="0" align="absmiddle" style="cursor:pointer;" class="login_bn">
		<!--<input type="submit" value="로그인" class="btn_submit">-->
	</div>

	<div style="margin:20px 0 0 0;text-align:center;color:#56ccc8;">
		<a href="<?php echo G5_BBS_URL ?>/password_lost_id.php" style="color:#56ccc8;">아이디 찾기</a> | <a href="<?php echo G5_BBS_URL ?>/password_lost.php" style="color:#56ccc8;">비밀번호 찾기</a> | <a href="<?=G5_URL?>/bbs/register.php" style="color:#56ccc8;">회원가입</a>
	</div>

    </form>

    <?php // 쇼핑몰 사용시 여기부터 ?>
    <?php if ($default['de_level_sell'] == 1) { // 상품구입 권한 ?>

        <!-- 주문하기, 신청하기 -->
        <?php if (preg_match("/orderform.php/", $url)) { ?>

    <section id="mb_login_notmb">
        <h2>비회원 구매</h2>

        <p>
            비회원으로 주문하시는 경우 포인트는 지급하지 않습니다.
        </p>

        <div id="guest_privacy">
            <?php echo $default['de_guest_privacy']; ?>
        </div>

        <label for="agree">개인정보수집에 대한 내용을 읽었으며 이에 동의합니다.</label>
        <input type="checkbox" id="agree" value="1">

        <div class="btn_confirm">
            <a href="javascript:guest_submit(document.flogin);" class="btn02">비회원으로 구매하기</a>
        </div>

        <script>
        function guest_submit(f)
        {
            if (document.getElementById('agree')) {
                if (!document.getElementById('agree').checked) {
                    alert("개인정보수집에 대한 내용을 읽고 이에 동의하셔야 합니다.");
                    return;
                }
            }

            f.url.value = "<?php echo $url; ?>";
            f.action = "<?php echo $url; ?>";
            f.submit();
        }
        </script>
    </section>

        <?php } else if (preg_match("/orderinquiry.php$/", $url)) { ?>

    <fieldset id="mb_login_od">
        <legend>비회원 주문조회</legend>

        <form name="forderinquiry" method="post" action="<?php echo urldecode($url); ?>" autocomplete="off">

        <label for="od_id" class="od_id">주문서번호<strong class="sound_only"> 필수</strong></label>
        <input type="text" name="od_id" value="<?php echo $od_id; ?>" id="od_id" required class="frm_input required" size="20">
        <label for="id_pwd" class="od_pwd">비밀번호<strong class="sound_only"> 필수</strong></label>
        <input type="password" name="od_pwd" size="20" id="od_pwd" required class="frm_input required">
        <input type="submit" value="확인" class="btn_submit">

        </form>
    </fieldset>

    <section id="mb_login_odinfo">
        <h2>비회원 주문조회 안내</h2>
        <p>메일로 발송해드린 주문서의 <strong>주문번호</strong> 및 주문 시 입력하신 <strong>비밀번호</strong>를 정확히 입력해주십시오.</p>
    </section>

        <?php } ?>

    <?php } ?>
    <?php // 쇼핑몰 사용시 여기까지 반드시 복사해 넣으세요 ?>

</div>

<script>
$(function(){
    $("#login_auto_login").click(function(){
        if (this.checked) {
            this.checked = confirm("자동로그인을 사용하시면 다음부터 회원아이디와 비밀번호를 입력하실 필요가 없습니다.\n\n공공장소에서는 개인정보가 유출될 수 있으니 사용을 자제하여 주십시오.\n\n자동로그인을 사용하시겠습니까?");
        }
    });
});

</script>
<!-- } 로그인 끝 -->