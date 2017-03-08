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
/*
if (G5_IS_MOBILE) {
    include_once(G5_MOBILE_PATH.'/index.php');
    return;
}
*/

if (G5_IS_MOBILE) {
    include_once(G5_MSHOP_PATH.'/head.php');
} else {
    include_once(G5_PATH.'/_head.php');
}

?>

<!--<div id="sub_title2" ><span>회사소개</span>
	<ul>
		<li><img src="img/home_bn.png"></li>
			<li><img src="img/arrow_bn.png"></li>
			<li>About</li>
			<li><img src="img/arrow_bn.png"></li>
			<li>회사소개</li>
	</ul>
</div>-->

<div class="seclock_body" style="width:800px; margin:0px auto;">

<?
$nav_ca_id = $ca_id;

if (G5_IS_MOBILE) {
	?>

	<span id="sct_location">
    	<a href="http://localhost/shop/" class="sct_bg">홈</a>
    	<a href="./sec_lock.php" class="sct_here">실물투자 컨설팅 & 보안금고</a>
	</span>
<?
} else {
	include G5_SHOP_SKIN_PATH . '/navigation.skin.php';
}
?>


<?php 
if(G5_IS_MOBILE) {
	include_once(G5_MSHOP_SKIN_PATH.'/sec_lock.skin.php');
} else {
	include_once(G5_SHOP_SKIN_PATH.'/sec_lock.skin.php');
} //
?>

</div>

<link rel="stylesheet" type="text/css" href="<?=G5_MOBILE_URL?>/css/jquery.maxlength.css">
<style>
	.maxlength-feedback { float:right;}
</style>
<script src="<?=G5_JS_URL?>/jquery.plugin.js"></script>
<script src="<?=G5_JS_URL?>/jquery.maxlength.js"></script>
<script type="text/javascript">

$(function(){
	$('.seclock_ta').maxlength({max: 500, feedbackText: '{c}/{m}'});


	$("select[name='sec_email3']").change(function(){
		$("input[name='sec_email2']").val($(this).val());
		$("input[name='sec_email2']").focus();
	});

	$(".submit").click(function(){
		var chk = false;
		var chk2 = false;
		var chk3 = false;
		var chk4 = false;

		if($("input[name='sec_name']").val() == ""){
			alert("신청자명을 입력해주세요.");
			$("input[name='sec_name']").focus();
			return false;
		}

		if($("input[name='sec_email1']").val() == ""){
			alert("이메일을 입력해주세요.");
			$("input[name='sec_email1']").focus();
			return false;
		}

		if($("input[name='sec_email2']").val() == ""){
			alert("이메일을 입력해주세요.");
			$("input[name='sec_email2']").focus();
			return false;
		}

		if($("input[name='sec_hp2']").val() == ""){
			alert("휴대폰번호를 입력해주세요.");
			$("input[name='sec_hp2']").focus();
			return false;
		}

		if($("input[name='sec_hp3']").val() == ""){
			alert("휴대폰번호를 입력해주세요.");
			$("input[name='sec_hp3']").focus();
			return false;
		}

		$("input[name='sec_radio1']").each(function(i){
			if($("input[name='sec_radio1']").is(":checked") == true){
				chk = true;
			}
		});

		if(chk == false){
			alert("항목에 체크 해주시기 바랍니다.");
			return false;
		}

		$("input[name='sec_radio2']").each(function(i){
			if($("input[name='sec_radio2']").is(":checked") == true){
				chk2 = true;
			}
		});

		if(chk2 == false){
			alert("항목에 체크 해주시기 바랍니다.");
			return false;
		}

		$("input[name='sec_radio3']").each(function(i){
			if($("input[name='sec_radio3']").is(":checked") == true){
				chk3 = true;
			}
		});

		if(chk3 == false){
			alert("항목에 체크 해주시기 바랍니다.");
			return false;
		}

		$("input[name='sec_radio4']").each(function(i){
			if($("input[name='sec_radio4']").is(":checked") == true){
				chk4 = true;
			}
		});

		if(chk4 == false){
			alert("항목에 체크 해주시기 바랍니다.");
			return false;
		}

		$("form[name='fsec']").attr("action", "./sec_lock_update.php").submit();

	});
});
</script>


<?php
if (G5_IS_MOBILE) {
    include_once(G5_MSHOP_PATH.'/tail.php');
} else {
    include_once(G5_PATH.'/tail.php');
}
?>
