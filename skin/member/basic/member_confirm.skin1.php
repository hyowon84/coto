<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

?>

<link rel="stylesheet" href="../css/default_shop.css">

<!-- 주문 내역 목록 시작 { -->
<?php if (!$limit) { ?>총 <?php echo $cnt; ?> 건<?php } ?>

<div id="aside2"></div>

<?include_once("../inc/mypage_menu.php");?>

<div style="background:#fff;">
	
	<form name="fmemberconfirm" action="<?php echo $url ?>" onsubmit="return fmemberconfirm_submit(this);" method="post" style="margin:0px 0 0 0;">
	<input type="hidden" name="mb_id" value="<?php echo $member['mb_id'] ?>">
	<input type="hidden" name="w" value="u">

	<div id="mem_title">
		<div style="color:#767676;font-size:15px;"><strong>비밀번호 확인</strong></div>
<!--		<div style="float:right;color:#babdc1;">비밀번호 찾기</div>-->
	</div>

	<div class="cl mem_box">
		<div>
			<span style="color:#000;font-weight:bold;">회원아이디</span><span style="margin:0 0 0 20px;"><?php echo $member['mb_id'] ?></span>
		</div>
		<div style="margin:7px 0 0 0;">
			<input type="password" name="mb_password" id="login_pw" required class="frm_input" value="비밀번호">

			<input type="image" src="/img/mem_conf_btn.gif" id="btn_submit" style="background:none;padding:0px;">
		</div>
		<div style="margin:5px 0 0 0;font-size:13px;color:#8c9197;">
			<img src="<?=G5_URL?>/img/mem_conf_ico.gif">
			<b><?=$member[mb_name]?></b>님의 개인정보 보호를 위해 비밀번호를 다시 한번 확인합니다
		</div>
	</div>


	</form>
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