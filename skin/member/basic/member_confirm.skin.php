<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>

<!-- 회원 비밀번호 확인 시작 { -->
<link rel="stylesheet" href="<?php echo $member_skin_url ?>/style.css">

<div id="mb_confirm" class="mbskin">
    <h1><img src="<?=G5_URL?>/img/mem_conf_logo.gif" /></h1>

	<div class="box">
		<p style="height:40px;">
			<span><img src="<?=G5_URL?>/img/mem_conf_ico.gif" /></span>
			<span class="txt">고객님의 개인정보 보호를 위해 비밀번호를 다시 한번 입력해주시기 바랍니다.</span>
			<!--<?php// if ($url == 'member_leave.php') { ?>
			비밀번호를 입력하시면 회원탈퇴가 완료됩니다.
			<?php// }else{ ?>
			회원님의 정보를 안전하게 보호하기 위해 비밀번호를 한번 더 확인합니다.
			<?php// }  ?>
			-->
		</p>

		<form name="fmemberconfirm" action="<?php echo $url ?>" onsubmit="return fmemberconfirm_submit(this);" method="post">
		<input type="hidden" name="mb_id" value="<?php echo $member['mb_id'] ?>">
		<input type="hidden" name="w" value="u">

		<fieldset>
			<div class="id_info">
				<span><strong>회원아이디</strong></span>
				<span id="mb_confirm_id"><?php echo $member['mb_id'] ?></span>
			</div>

			<div class="cl pw_info">
				<input type="password" name="mb_password" id="login_pw" required class="frm_input" value="비밀번호">
			</div>

			<div class="cl">
				<input type="image" src="/img/mem_conf_btn.gif" id="btn_submit">
			</div>
		</fieldset>

		</form>
	</div>

    <div class="btn_confirm">
        <a href="<?php echo G5_URL ?>">메인으로 돌아가기</a>
    </div>

</div>

<script type="text/javascript">

$(document).ready(function(){
	$("input[name='mb_password']").click(function(){
		$("input[name='mb_password']").val("");
	});
});

function fmemberconfirm_submit(f)
{
    document.getElementById("btn_submit").disabled = true;

    return true;
}
</script>
<!-- } 회원 비밀번호 확인 끝 -->