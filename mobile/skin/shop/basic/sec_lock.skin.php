<form name="fsec" id="fsec" method="POST" enctype="multipart/form-data">
<div class="seclock">
	<div class='seclock_top'>
		<div class='seclock_top_title'>실물투자 컨설팅 & 보안금고</div>	
		<!-- div class='seclock_top_nav'>홈 > 실물투자 컨설팅 & 보안금고</div -->
	</div>
		
	<div class='seclock_box'>
		<div class='seclock_box_title'>실물투자컨설팅 설명서</div>
		<div class="seclock_box_main"><a href="<?php echo G5_BBS_URL?>/consulting_download.php?csf_idx=1"><img src="<?=G5_URL?>/img/sec_down_bn.gif" border="0" align="absmiddle"></a></div>
	</div>

	<div class='seclock_box'>
		<div class='seclock_box_title'>컨설팅 가이드</div>
		<div class='seclock_box_line'></div>
		<div class="seclock_box_main">
			<div class='seclock_box_rect'><span class='step_no'>1</span><span class='sTitle'>설문지 작성과 컨설팅 예약 접수</span></div>
			<div class='seclock_box_rect'><span class='step_no'>2</span><span class='sTitle'>담당자 상담</span></div>
			<div class='seclock_box_rect'><span class='step_no'>3</span><span class='sTitle'>컨설팅 & 보안금고 계약완료</span></div>
			<div class='seclock_box_rect'><span class='step_no'>4</span><span class='sTitle'>계약완료 후 서비스 이용</span></div>
		</div>
	</div>
	
	<div class='seclock_box'>
		<div class='seclock_box_title'>실물 투자 컨설팅 & 보안금고 신청</div>
		<div class="seclock_box_main">
			<table class='seclock_box_main_tb' border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td class='tit'>신청자명</td>
					<td class='val'><input type="text" name="sec_name" class="seclock_inp" style='width:90%;' /></td>
				</tr>
				<tr>
					<td class='tit'>이메일</td>
					<td class='val'>
						<input type="text" name="sec_email1" class="seclock_inp" style='width:20%;'> @
						<input type="text" name="sec_email2" class="seclock_inp" style="width:30%;">
						<select name="sec_email3" style='width:70px;'>
							<option value="">직접입력</option>
							<option value="naver.com">naver.com</option>
							<option value="hanmail.net">hanmail.net</option>
							<option value="nate.com">nate.com</option>
							<option value="gmail.com">gmail.com</option>
							<option value="hotmail.com">hotmail.com</option>
							<option value="lycos.co.kr">lycos.co.kr</option>
							<option value="empal.com">empal.com</option>
							<option value="dreamwiz.com">dreamwiz.com</option>
							<option value="korea.com">korea.com</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class='tit'>휴대폰</td>
					<td class='val'>
						<select name="sec_hp1" style='width:25%'>
							<option value="">선택</option>
							<option value="010">010</option>
							<option value="011">011</option>
							<option value="016">016</option>
							<option value="017">017</option>
							<option value="018">018</option>
							<option value="019">019</option>
							<option value="0505">0505</option>
							<option value="0502">0502</option>
						</select> - 
						<input type="text" name="sec_hp2" style="width:30%;" class="seclock_inp" maxlength="4"> - 
						<input type="text" name="sec_hp3" style="width:30%;" class="seclock_inp" maxlength="4">
					</td>
				</tr>
			</table>
		</div>
		<div class="seclock_box_main">
			<div class='seclock_box_rect2'><span class='step_no'>1.</span><span class='sTitle'>당신의 귀금속 구매 목적은 무엇입니까?</span></div>
			<div class='seclock_box_choice'>
				<input type="radio" name="sec_radio1" value="투자"> 투자
				<input type="radio" name="sec_radio1" value="수집"> 수집
				<input type="radio" name="sec_radio1" value="둘다"> 둘다
				<input type="radio" name="sec_radio1" value="기타"> 기타
			</div>
			
			<div class='seclock_box_rect2'><span class='step_no'>2.</span><span class='sTitle'>보유하신 금속 중 가장 많은 비율의 금속은?</span></div>
			<div class='seclock_box_choice'>
				<input type="radio" name="sec_radio2" value="금"> 금
				<input type="radio" name="sec_radio2" value="은"> 은
				<input type="radio" name="sec_radio2" value="백금"> 백금
				<input type="radio" name="sec_radio2" value="기타"> 기타
			</div>
				
			<div class='seclock_box_rect2'><span class='step_no'>3.</span><span class='sTitle'>보유 하고 있는 중량은 대략 얼마나 되십니까?</span></div>
			<div class='seclock_box_choice'>
				<input type="radio" name="sec_radio3" value="1kg 이하"> 1kg 이하
				<input type="radio" name="sec_radio3" value="1kg~10kg"> 1kg~10kg
				<input type="radio" name="sec_radio3" value="10kg~30kg"> 10kg~30kg
				<input type="radio" name="sec_radio3" value="30kg 이상"> 30kg 이상
			</div>
			
			<div class='seclock_box_rect2'><span class='step_no'>4.</span><span class='sTitle'>구매하신 귀금속의 구매가를 기록하고 있습니까?</span></div>
			<div class='seclock_box_choice'>
				<input type="radio" name="sec_radio4" value="yes"> yes
				<input type="radio" name="sec_radio4" value="no"> no
			</div>
		</div>
	</div>
	
	<div class='seclock_box'>
		<div class='seclock_box_main'>
			<span style='float:left;'>문의사항</span>
		</div>
		<div class="seclock_box_main">
			<textarea name="sec_radio5" class="seclock_ta"></textarea>
		</div>
	</div>		
	
	<div class='seclock_top'>
		<a href="javascript:void(0);" class="submit_bn cancel">취소</a>
		<a href="javascript:void(0);" class="submit_bn submit">등록</a>		
	</div>
</div>
</form>
