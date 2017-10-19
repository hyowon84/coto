<?php
define('_INDEX_', true);
include_once('./_common.php');

if (G5_IS_MOBILE) {
	include_once(G5_MSHOP_PATH.'/head.php');
} else {
	include_once('./_head.sub.php');
}
?>


	<!-- 회원 비밀번호 확인 시작 { -->
	<link rel="stylesheet" href="<?php echo $member_skin_url ?>/style.css">

	<div id="idpw_chk" class="mbskin">
		<h1><a href="<?=G5_URL?>"><img src="<?=G5_URL?>/img/mem_conf_logo.gif" /></a></h1>



		<form name="form1" action="../okname/hs_idfnfrm_popup2.php" method="post">

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

			<div class="box">
				<div class="title" style="width:230px; margin:0px auto;">
					<ul>
						<li onclick="goto_url('./password_lost_id.php');" style="float:left; width:auto;">아이디 찾기</li>
						<li class="on" onclick="goto_url('./password_lost.php');" style="float:left; width:auto; margin-left:50px;">비밀번호 찾기</li>
					</ul>
				</div>
				<div class="cl" style="height:1px;border-top:2px #223753 solid;"></div>

				<!-- 아이디 찾기 -->
				<div class="sub_box id_find">
					<ul>
						<li>
							<span class="title" style="padding:0px;">본인 확인 후 찾기</span>
							<div class="des">
								실명이 확인된 아이디는 휴대폰으로 비밀번호를 찾을 수 있습니다.
							</div>
							<div class="line"></div>

							<div class="choice" style="width:130px; margin:0px auto;">
								<table>
									<tr>
										<td><img src="<?=G5_URL?>/img/idpw_ico.gif" align="absmiddle;"></td>
										<td><input type="radio" name="status" value="1" checked></td>
										<td><i>휴대폰 인증</i></td>
									</tr>
								</table>

							</div>

							<div class="des1" style="clear:both;">
								<span><img src="<?=G5_URL?>/img/mem_conf_ico.gif"></span>
						<span style="padding:0 0 0 7px;">
							<i>입력하신 정보는 본인확인을 위해 (주)드림시큐리티에 제공되며,</br>
								본인확인 용도 외에 사용되거나 저장되지 않습니다.</i>
						</span>
							</div>
						</li>
						<li>
							<span class="title"></span>
							<div class="des">
								ㆍ비밀번호는 암호화되어있어, 분실시 확인이 불가능한 정보입니다.</br>
								ㆍ본인 확인을 통해 비밀번호를 재설정 하실 수 있습니다.
							</div>
							<div class="input">
								<div style="margin:10px 0 0 0;">

									<div style="color:##8d8d8d; width:200px; margin:0px auto;">
										내외국인구분&nbsp;&nbsp;&nbsp;
										<input type="radio" name="nation" value="1" checked>내국인
										<input type="radio" name="nation" value="2">외국인</br></br>

										<input type="radio" name="tel_com_cd" value="01" checked>SKT
										<input type="radio" name="tel_com_cd" value="02">KT
										<input type="radio" name="tel_com_cd" value="03">LGU+
										<input type="radio" name="tel_com_cd" value="04">알뜰폰SKT
									</div>

									<div style="width:150px; margin:0px auto; margin-top:10px; margin-bottom:10px;">
										<div style="width:auto;"><input type="text" name="name" id="name" required class="frm_input" value="이름"></div>
										<div style="width:auto;"><input type="text" name="tel_no" id="tel_no" required class="frm_input" value="휴대폰번호"></div>
									</div>

								</div>
							</div>
							<div class="des1">
								<span><img src="<?=G5_URL?>/img/mem_conf_ico.gif"></span>
						<span style="padding:0 0 0 7px;">
							<i>입력하신 정보는 본인확인을 위해 (주)드림시큐리티에 제공되며,</br>
								본인확인 용도 외에 사용되거나 저장되지 않습니다.</i>
						</span>
							</div>
							<div class="submit" style="width:190px; margin:0px auto;">
								<img src="<?=G5_URL?>/img/idpw_find_bn.gif" class="submit_btn" onClick="jsSubmit();">
							</div>
						</li>
					</ul>
				</div>

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
			<input type="hidden" name="return_id" 				value="" 	/>
		</form>

		<div style="margin:15px 0 0 0;text-align:center;">
			코인즈투데이에서 제공해 드리는 방법으로 아이디/비밀번호를 찾으실 수 없는 고객님께서는 고객센터(070-4323-6998)로 연락 주시기 바랍니다.
		</div>

	</div>

	<script type="text/javascript">

		function jsSubmit(){
			var form1 = document.form1;
			var isChecked = false;
			var inTpBit = "";
			var tel_no = $("input[name='tel_no']").val();
			var name = $("input[name='name']").val();

			if (form1.name.value == "") {
				if (form1.name.value == "") {
					alert("성명을 입력해주세요");
					return;
				}
			}
			if (form1.tel_no.value == "") {
				alert("휴대폰번호를 입력해주세요");
				return;
			}

			$.ajax({
				type : "POST",
				dataType : "JSON",
				url : "./_Ajax.password_lost.php",
				data : "name=" + name + "&tel_no=" + tel_no,
				success : function(data){
					$("input[name='birthday']").val(data.birthday);
					$("input[name='gender']").val(data.gender);

					window.open("", "auth_popup", "width=430,height=590,scrollbar=yes");

					$("form[name='form1']").attr({"target":"auth_popup"}).submit();
				}
			});

			/*var form1 = document.form1;
			 form1.target = "auth_popup";
			 form1.submit();*/
		}

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

			$("#name").focus(function(){
				$(this).val("");
			});

			$("#tel_no").focus(function(){
				$(this).val("");
			});
		});

	</script>
	<!-- } 회원 비밀번호 확인 끝 -->

<?
include_once('./_tail.sub.php');
?>