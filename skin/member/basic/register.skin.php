<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

sql_query("
delete from g5_mail_auth where ip='".$REMOTE_ADDR."'
");
?>

<!-- 회원가입약관 동의 시작 { -->
<link rel="stylesheet" href="<?php echo $member_skin_url ?>/style.css">
<link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/themes/base/jquery-ui.css" rel="stylesheet" />
<style type="text/css">
.regi_dis{display:none;}
td {font-size:11px;height:25px;letter-spacing:0.1em;line-height:25px}
input.text_style {border:1px solid #bfbfbf;height:18px;background:#f7f7f7}

<!--
.ui-datepicker { font:12px dotum; }
.ui-datepicker select.ui-datepicker-month,
.ui-datepicker select.ui-datepicker-year { width: 70px;}
.ui-datepicker-trigger { margin:0 0 -5px 2px; }
-->
</style>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/jquery-ui.min.js"></script>
<script type="text/javascript">
jQuery(function($){
	$.datepicker.regional['ko'] = {
		closeText: '닫기',
		prevText: '이전달',
		nextText: '다음달',
		currentText: '오늘',
		monthNames: ['1월(JAN)','2월(FEB)','3월(MAR)','4월(APR)','5월(MAY)','6월(JUN)',
		'7월(JUL)','8월(AUG)','9월(SEP)','10월(OCT)','11월(NOV)','12월(DEC)'],
		monthNamesShort: ['1월','2월','3월','4월','5월','6월',
		'7월','8월','9월','10월','11월','12월'],
		dayNames: ['일','월','화','수','목','금','토'],
		dayNamesShort: ['일','월','화','수','목','금','토'],
		dayNamesMin: ['일','월','화','수','목','금','토'],
		weekHeader: 'Wk',
		dateFormat: 'yymmdd',
		firstDay: 0,
		isRTL: false,
		showMonthAfterYear: true,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['ko']);

    $('#reg_mb_birth').datepicker({
        showOn: 'button',
		buttonImage: '../img/calendar.gif',
		buttonImageOnly: true,
        buttonText: "달력",
        changeMonth: true,
		changeYear: true,
        showButtonPanel: true,
        yearRange: 'c-99:c+99',
        maxDate: '+0d'
    });
});
</script>

<div id="sub_title3" ><span style="font-size:23px;font-weight:bold;font-family:'NanumGothic'" >회원가입</span></div>

<div id="member_top">
	<ul>
		<li><img src="<?php echo G5_IMG_URL ?>/mem1_o.png"></li>
		<li><img src="<?php echo G5_IMG_URL ?>/mem2.png"></li>
		<li><img src="<?php echo G5_IMG_URL ?>/mem3.png"></li>
	</ul>
</div>

<h1 style="margin:20px 0 0 0;font-size:15px;font-weight:bold;font-family:'NanumGothic';margin-bottom:10px">본인인증</h1>
<div id="member_center">
	<ul>
		<li class="hp_auth <?php if(strtolower(basename($PHP_SELF))=="register.php") echo " member_center_on";?>" idx="1">휴대폰 인증</li>
		<li class="hp_auth" idx="2">아이핀(I-PIN) 인증</li>
		<li class="hp_auth" idx="3">외국인 회원(Foreigners)</li>
	</ul>
</div>

<div class="mbskin" style="background:#fff;border:1px solid #eaeaea;border-top:0px;padding-bottom:20px">


	<!-- 휴대폰 인증 -->
	<div class="regi_dis hp_auth1" style="display:block;">

	<form name="form1" action="../plugin/okname/hs_cnfrm_popup2.php" method="post">
		<input type="hidden" name="idcf_mbr_com_cd" id="idcf_mbr_com_cd">
		<input type="hidden" name="in_tp_bit" value="15">

		<?php
		// 입력유형은 다음의 조합을 따릅니다.
		//  1 : 0001 - 성명
		//  2 : 0010 - 생년월일
		//  3 : 0011 - 생년월일 + 성명
		//  4 : 0100 - 성별,내외국인구분
		//  5 : 0101 - 성별,내외국인구분 + 성명
		//  6 : 0110 - 성별,내외국인구분 + 생년월일
		//  7 : 0111 - 성별,내외국인구분 + 생년월일 + 성명
		//  8 : 1000 - 통신사,휴대폰번호
		//  9 : 1001 - 통신사,휴대폰번호 + 성명
		// 10 : 1010 - 통신사,휴대폰번호 + 생년월일
		// 11 : 1011 - 통신사,휴대폰번호 + 생년월일 + 성명
		// 12 : 1100 - 통신사,휴대폰번호 + 성별,내외국인구분
		// 13 : 1101 - 통신사,휴대폰번호 + 성별,내외국인구분 + 성명
		// 14 : 1110 - 통신사,휴대폰번호 + 성별,내외국인구분 + 생년월일
		// 15 : 1111 - 통신사,휴대폰번호 + 성별,내외국인구분 + 생년월일 + 성명
		?>

		<!--<input type="radio" name="in_tp_bit" value="0" checked>없음 (팝업에서 모든 정보를 입력)<br/>
		<input type="radio" name="in_tp_bit" value="7">성명+생년월일+성별,내외국인구분<br/>
		<input type="radio" name="in_tp_bit" value="8">통신사,휴대폰번호<br/>
		<input type="radio" name="in_tp_bit" value="15">성명+생년월일+성별,내외국인구분+통신사,휴대폰번호<br/>-->


		<div class="tbl_frm01 tbl_wrap" style="background:#fff;padding:3px">
			<table>
			<caption>기본정보</caption>
			<tbody>
			<tr>
				<th scope="row"><label for="reg_mb_id"><span style="color:#fd6500;font-size:7px">▶</span> 이름</label></th>
				<td scope="row" colspan="2">
					<input class="text_style" type="text" name="name" maxlength="20" size="20" value="">
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="reg_mb_id"><span style="color:#fd6500;font-size:7px">▶</span> 생년월일</label></th>
				<td scope="row" colspan="2">
					ex) 19910102
					<input class="text_style" type="text" name="birthday" id="reg_mb_birth" maxlength="8" size="10" value="" readonly="readonly" /> ☜ 클릭
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="reg_mb_id"><span style="color:#fd6500;font-size:7px">▶</span> 성별</label></th>
				<td scope="row" colspan="2">
					<input type="radio" name="gender" value="1" checked>남
					<input type="radio" name="gender" value="0">여
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="reg_mb_id"><span style="color:#fd6500;font-size:7px">▶</span> 내외국인구분</label></th>
				<td scope="row" colspan="2">
					<input type="radio" name="nation" value="1" checked>내국인
					<input type="radio" name="nation" value="2">외국인
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="reg_mb_id"><span style="color:#fd6500;font-size:7px">▶</span> 휴대폰</label></th>
				<td scope="row" colspan="2">
					<input type="radio" name="tel_com_cd" value="01" checked>SKT
					<input type="radio" name="tel_com_cd" value="02">KT
					<input type="radio" name="tel_com_cd" value="03">LGU+
					<input type="radio" name="tel_com_cd" value="04">알뜰폰SKT<br/>
					<select name="tel_no1">
						<option value="010">010</option>
						<option value="011">011</option>
						<option value="016">016</option>
						<option value="017">017</option>
						<option value="018">018</option>
						<option value="019">019</option>
						<option value="0505">0505</option>
						<option value="0502">0502</option>
					</select> -
					<input class="text_style" type="text" name="tel_no2" maxlength="4" size="5" value=""> -
					<input class="text_style" type="text" name="tel_no3" maxlength="4" size="5" value="">
					<input class="text_style" type="hidden" name="tel_no" maxlength="11" size="15" value="">
				</td>
			</tr>
			</tbody>
			</table>
		</div>

		 <div class="btn_confirm" style="margin:20px 0 0 0;padding:0">
			<img src="<?php echo G5_IMG_URL ?>/ok_bn.jpg" value="본인확인" id="btn_submit" class="btn_submit" accesskey="s" onClick="jsSubmit();" style="padding:0;margin:0;cursor:pointer;">
		</div>

		</form>


		<form name="kcbResultForm" method="post" >
			<input type="hidden" name="idcf_mbr_com_cd" 		value="" 	/>
			<input type="hidden" name="hs_cert_svc_tx_seqno" 	value=""	/>
			<input type="hidden" name="hs_cert_rqst_caus_cd" 	value="" 	/>
			<input type="hidden" name="result_cd" 				value="" 	/>
			<input type="hidden" name="result_msg" 				value="" 	/>
			<input type="hidden" name="cert_dt_tm" 				value="" 	/>
			<input type="hidden" name="di" 						value="" 	/>
			<input type="hidden" name="ci" 						value="" 	/>
			<input type="hidden" name="name" 					value="" 	/>
			<input type="hidden" name="birthday" 				value="" 	/>
			<input type="hidden" name="gender" 					value="" 	/>
			<input type="hidden" name="nation" 					value="" 	/>
			<input type="hidden" name="tel_com_cd" 				value="" 	/>
			<input type="hidden" name="tel_no" 					value="" 	/>
			<input type="hidden" name="return_msg" 				value="" 	/>
		</form>
	</div>

	<!-- 아이핀 인증 -->
	<div class="regi_dis hp_auth2">

		<form name="fipin" id="fipin" action="../okname/ipin2.php" method="post">
			<table width=795 height=100%>
				<tr>
					<td colspan="2" width=795 align=center valign=bottom style="height:100px"><strong style="font-size:13px;text-align:center;color:#646569"> 안전한 회원 가입을 위한 본인 확인 단계입니다.</strong></td>
				</tr>
				<tr>
					<td colspan="2" align="center" valign=top>	<input type="image" src="<?php echo G5_IMG_URL ?>/ipin_bn.jpg" value="아이핀" onClick="jsSubmit1();"></td>
				</tr>
				<tr>
					<td style="height:200px;margin-top:70px">
						<img src="<?php echo G5_IMG_URL ?>/ipin_notice.jpg" style="margin-left:-4px">
					</td>
				</tr>
			</table>
		</form>
		<form name="kcbOutForm" method="post">
			<input type="hidden" name="encPsnlInfo" />
			<input type="hidden" name="virtualno" />
			<input type="hidden" name="dupinfo" />
			<input type="hidden" name="realname" />
			<input type="hidden" name="cprequestnumber" />
			<input type="hidden" name="age" />
			<input type="hidden" name="sex" />
			<input type="hidden" name="nationalinfo" />
			<input type="hidden" name="birthdate" />
			<input type="hidden" name="coinfo1" />
			<input type="hidden" name="coinfo2" />
			<input type="hidden" name="ciupdate" />
			<input type="hidden" name="cpcode" />
			<input type="hidden" name="authinfo" />
			<input type="hidden" name="hs_cert_svc_tx_seqno" />
		</form>

	</div>

	<!-- 외국인 회원 -->
	<div class="regi_dis hp_auth3">

		<form name="femail" id="femail" action="<?=G5_URL?>/bbs/register_form.php" method="post">
			<input type="hidden" name="mail_auth" value="y">
			<table width=400px height=300px align="center">
				<tr height="20px"><td></td></tr>
				<tr>
					<td colspan="2" width=795 align=left valign=bottom height=50>
						<strong style="font-size:13px;text-align:center;color:#646569;padding-right:13px">이메일 입력</strong>
						<input class="text_style" type="text" name="email" id="email" size="30" value="" >
						<input type="button" value="▶ 인증 메일 발송" onclick="jsSubmit2();" style="background:#fff;border:0;color:#fd6500;font-weight:bold;margin-left:10px;cursor:pointer">
					</td>
				</tr>
				<tr>
					<td colspan="2" width=795 align=left valign=top height=150>
						<strong style="font-size:13px;text-align:center;color:#646569">인증번호 입력</strong>
						<input class="text_style" type="text" name="mail_key" id="mail_key" size="30" value="">
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center" valign=top height=100>
						<input type="image" src="<?php echo G5_IMG_URL ?>/ok_bn.jpg" value="확인" class="mail_submit">

					</td>
				</tr>
			</table>
		</form>

	</div>

	<div class="test"></div>

<script type="text/javascript">

$(document).ready(function(){

	$(".hp_auth").click(function(){
		var idx = $(this).attr("idx");
		var num;
		var num1;

		$("#member_center").find("li").each(function(i){

			num = idx - 1;
			num1 = i + 1;
			if(i == num){
				$("#member_center").find("li").eq(i).addClass("member_center_on");
				$(".hp_auth" + num1).css({"display":"block"});
			}else{
				$("#member_center").find("li").eq(i).removeClass("member_center_on");
				$(".hp_auth" + num1).css({"display":"none"});
			}

		});
	});

	$(".mail_submit").click(function(){

		if($("input:text[name='mail_key']").val() == ""){
			alert("인증번호를 입력하세요.");
			return false;
		}
		$("form[name='femail']").submit();
	});

});

	function jsSubmit2(){
		var frm = document.femail;
		var email;

		if(frm.email.value == ""){
			alert("이메일을 입력하시기 바랍니다.");
			frm.email.focus();
			return false;
		}

		email = frm.email.value;

		$.post("./_Ajax.mail.php", {email : email}, function(data){
			$(".test").html(data);
			alert("인증메일이 발송 되었습니다.");
			return false;
		});
	}

	function jsSubmit1(){
		var popupWindow = window.open("", "kcbPop", "left=200, top=100, status=0, width=450, height=550");
		var form1 = document.fipin;
		form1.target = "kcbPop";
		form1.submit();

		popupWindow.focus();
	}

	function jsSubmit(){
		var form1 = document.form1;
		var isChecked = false;
		var inTpBit = "";

		if (form1.name.value == "") {
			if (form1.name.value == "") {
				alert("성명을 입력해주세요");
				return;
			}
		}
		if (form1.birthday.value == "") {
			if (form1.birthday.value == "") {
				alert("생년월일을 입력해주세요");
				return;
			}
		}
		if (form1.tel_no1.value == "" || form1.tel_no2.value == "" || form1.tel_no3.value == "") {
			alert("휴대폰번호를 입력해주세요");
			return;
		}

		form1.tel_no.value = form1.tel_no1.value + form1.tel_no2.value + form1.tel_no3.value;

		window.open("", "auth_popup", "width=430,height=590,scrollbar=yes");

		var form1 = document.form1;
		form1.target = "auth_popup";
		form1.submit();
	}
//-->

function fregister_submit(f)
{
	if (!f.agree.checked) {
		alert("회원가입약관의 내용에 동의하셔야 회원가입 하실 수 있습니다.");
		f.agree.focus();
		return false;
	}

	if (!f.agree2.checked) {
		alert("개인정보수집이용안내의 내용에 동의하셔야 회원가입 하실 수 있습니다.");
		f.agree2.focus();
		return false;
	}

	return true;
}
</script>
</div>
</div>
<!-- } 회원가입 약관 동의 끝 -->