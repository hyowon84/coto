<?php
define('_INDEX_', true);
include_once('./_common.php');

if (G5_IS_MOBILE) {
	include_once(G5_MSHOP_PATH.'/head.php');
} else {
	include_once('./_head.sub.php');
}


if(!$_POST[return_id]){
	$chk = sql_fetch("select count(*) as cnt from {$g5['g5_idpw_auth_table']} where code='".$_POST[hs_cert_svc_tx_seqno]."' and mb_id='".$_POST[name]."' and mb_hp='".$_POST[tel_no]."' and mb_birth='".$_POST[birthday]."' ");

	if(!$chk[cnt]){
		alert("잘못된 접근 경로입니다.");
	}
}
?>


<!-- 회원 비밀번호 확인 시작 { -->
<link rel="stylesheet" href="<?php echo $member_skin_url ?>/style.css">

<?
if(!$_POST[return_id]){
?>
<div id="pw_chk" class="mbskin">
    <h1><img src="<?=G5_URL?>/img/mem_conf_logo.gif" /></h1>

	<form name="fidpw" id="fidpw" method="POST">
	<input type="hidden" name="code" value="<?=$_POST[hs_cert_svc_tx_seqno]?>">
	<input type="hidden" name="name" value="<?=$_POST[name]?>">
	<input type="hidden" name="hp" value="<?=$_POST[tel_no]?>">
	<input type="hidden" name="birth" value="<?=$_POST[birthday]?>">

	<div class="box">
		<div class="title">비밀번호 재설정</div>
		<div class="des">
			비밀번호 찾기 인증에 성공하셨습니다.</br>
			비밀번호를 재설정해 주세요
		</div>
		<div class="line"></div>
		<div class="input_box">
			<table border="0" cellspacing="0" cellpadding="0" align="center">
				<tr>
					<td align="left" style="color:#545454;font-size:17px;font-weight:bold;padding:0 15px 0 0;">비밀번호</td>
					<td><input type="password" name="mb_pw" class="input"></td>
				</tr>
				<tr>
					<td align="left" style="color:#545454;font-size:17px;font-weight:bold;padding:0 15px 0 0;">비밀번호확인</td>
					<td><input type="password" name="mb_pw_re" class="input"></td>
				</tr>
			</table>
		</div>
		<div class="submit_bn">
			<img src="<?=G5_URL?>/img/idpw_bn.gif" border="0" align="absmiddle" class="idpw_bn" />
		</div>
	</div>
	</form>

	<div style="margin:15px 0 0 0;text-align:center;">
		코인즈투데이에서 제공해 드리는 방법으로 아이디/비밀번호를 찾으실 수 없는 고객님께서는 고객센터(070-4323-6998)로 연락 주시기 바랍니다.
	</div>

</div>

<?}else{?>

<div id="pw_chk" class="mbskin">
    <h1><img src="<?=G5_URL?>/img/mem_conf_logo.gif" /></h1>

	<form name="fidpw" id="fidpw" method="POST">
	<input type="hidden" name="code" value="<?=$_POST[hs_cert_svc_tx_seqno]?>">
	<input type="hidden" name="name" value="<?=$_POST[name]?>">
	<input type="hidden" name="hp" value="<?=$_POST[tel_no]?>">
	<input type="hidden" name="birth" value="<?=$_POST[birthday]?>">

	<div class="box" style="height:400px;">
		<div class="title">아이디 찾기 완료</div>
		<div class="des">
			아이디 찾기 본인인증에 성공하셨습니다.</br>
			입력하신 정보와 일치 하는 아이디 목록입니다. 비밀번호 분실시에는 비밀번호 찾기를 진행해 주세요.
		</div>
		<div class="line"></div>
		<div class="input_box" style="margin:70px 0;">
			<table border="0" cellspacing="0" cellpadding="0" align="center">
				<tr>
					<td align="left" style="color:#6b6b6b;font-size:20px;font-weight:bold;"><?=$_POST[return_id]?></td>
				</tr>
			</table>
		</div>
		<div class="submit_bn">
			<img src="<?=G5_URL?>/img/idpw_login_bn.gif" border="0" align="absmiddle" style="cursor:pointer;" onclick="goto_url('<?=G5_URL?>/bbs/login.php');" />&nbsp;&nbsp;
			<img src="<?=G5_URL?>/img/idpw_pwfind_bn.gif" border="0" align="absmiddle" style="cursor:pointer;" onclick="goto_url('<?=G5_URL?>/bbs/password_lost.php');" />
		</div>
	</div>
	</form>

	<div style="margin:15px 0 0 0;text-align:center;">
		코인즈투데이에서 제공해 드리는 방법으로 아이디/비밀번호를 찾으실 수 없는 고객님께서는 고객센터(070-4323-6998)로 연락 주시기 바랍니다.
	</div>

</div>

<?}?>

<script type="text/javascript">

$(document).ready(function(){
	$(".idpw_bn").click(function(){
		if($("input[name='mb_pw']").val() == ""){
			alert("비밀번호를 입력해주세요.");
			$("input[name='mb_pw']").focus();
			return false;
		}

		if($("input[name='mb_pw_re']").val() == ""){
			alert("비밀번호를 입력해주세요.");
			$("input[name='mb_pw_re']").focus();
			return false;
		}

		if($("input[name='mb_pw']").val() != $("input[name='mb_pw_re']").val()){
			alert("비밀번호가 틀립니다..");
			$("input[name='mb_pw']").focus();
			return false;
		}

		$("form[name='fidpw']").attr("action", "./password_mresult_update.php").submit();

	});
});

</script>
<!-- } 회원 비밀번호 확인 끝 -->

<?
include_once('./_tail.sub.php');
?>