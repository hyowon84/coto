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



<!--<div id="sub_title2" style="width:100%;"><span>ID/PW찾기</span></div>-->

<div id="aside2"></div>


<div>

	<form name="form1" action="../okname/hs_fnfrm_popup2.php" method="post">

	<input type="hidden" name="idcf_mbr_com_cd" id="idcf_mbr_com_cd">

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
	<input type="hidden" name="in_tp_bit" value="15">
	<input type="hidden" name="birthday" value="">	<!-- 생년월일 -->
	<input type="hidden" name="gender" value="">		<!-- 성별 : 남(1), 여(0) -->

	<div class="tbl_frm01 tbl_wrap" style="background:#fff;padding:3px">
		<table>
		<caption>비밀번호 찾기</caption>
		<tbody>
		<tr>
			<th scope="row"><label for="reg_mb_id">이름</label></th>
			<td scope="row" colspan="2">
				<input class="text_style" type="text" name="name" maxlength="20" size="20" value="">
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="reg_mb_id">내외국인구분</label></th>
			<td scope="row" colspan="2">
				<input type="radio" name="nation" value="1" checked>내국인
				<input type="radio" name="nation" value="2">외국인
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="reg_mb_id">휴대폰</label></th>
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
<div class="test"></div>


       

<script type="text/javascript">

function jsSubmit(){
	var form1 = document.form1;
	var isChecked = false;
	var inTpBit = "";

	var name = $("input[name='name']").val();
	var tel_no1 = $("select[name='tel_no1']").val();
	var tel_no2 = $("input[name='tel_no2']").val();
	var tel_no3 = $("input[name='tel_no3']").val();

	if (form1.name.value == "") {
		if (form1.name.value == "") {
			alert("성명을 입력해주세요");
			return;
		}
	}
	if (form1.tel_no1.value == "" || form1.tel_no2.value == "" || form1.tel_no3.value == "") {
		alert("휴대폰번호를 입력해주세요");
		return;
	}
	form1.tel_no.value = form1.tel_no1.value + form1.tel_no2.value + form1.tel_no3.value;

	$.ajax({
		type : "POST",
		dataType : "JSON",
		url : "./_Ajax.password_lost.php",
		data : "name=" + name + "&tel_no1=" + tel_no1 + "&tel_no2=" + tel_no2 + "&tel_no3=" + tel_no3,
		success : function(data){
			$("input[name='birthday']").val(data.birthday);

			if(data.gender == "m"){
				$("input[name='gender']").val(1);
			}else{
				$("input[name='gender']").val(0);
			}

			window.open("", "auth_popup", "width=430,height=590,scrollbar=yes");

			$("form[name='form1']").attr({"target":"auth_popup"}).submit();
		}
	});
	
	/*var form1 = document.form1;
	form1.target = "auth_popup";
	form1.submit();*/
}

</script>



<?php
include_once('./_tail.php');
?>
