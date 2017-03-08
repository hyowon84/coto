<!-- div id="sub_title2" style="width:100%;border:0px;"><span>실물투자 컨설팅 & 보안금고</span>
	<ul>
		<li>홈</li>
		<li>></li>
		<li>실물투자 컨설팅 & 보안금고</li>
	</ul>
</div-->

<div id="sec_lock">

<form name="fsec" id="fsec" method="POST" enctype="multipart/form-data">

	<div class="sec_top">
		<div><img src="<?=G5_URL?>/img/sec_img1.gif" border="0" align="absmiddle"></div>
		<div class="cl sec_bn">
			<span>실물투자컨설팅 설명서</span></br>
			<span class="bn"><a href="<?php echo G5_BBS_URL?>/consulting_download.php?csf_idx=1"><img src="<?=G5_URL?>/img/sec_down_bn.gif" border="0" align="absmiddle"></a></span>
		</div>
	</div>

	<div id="sub_title2" style="clear:both;height:25px;margin:10px 10px 40px 50px;width:700px;border-bottom:2px solid #000;"><span>컨설팅 가이드</span></div>
	<div class="cl sec_middle">
		<img src="<?=G5_URL?>/img/sec_img2.gif" border="0" align="absmiddle">
	</div>

	<div id="sub_title2" style="clear:both;height:25px;margin:10px 10px 30px 50px;width:700px;border-bottom:2px solid #000;"><span>실물투자 컨설팅 & 보안금고 신청</span></div>
	<div class="cl sec_bottom">
		
		<div>
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr style="height:40px;">
					<td style="width:140px;">신청자명</td>
					<td><input type="text" name="sec_name" class="seclock_inp" style="width:220px;"></td>
				</tr>
				<tr style="height:40px;">
					<td>이메일</td>
					<td>
						<input type="text" name="sec_email1" class="seclock_inp" style="width:120px;"> @ <input type="text" name="sec_email2" class="seclock_inp" style="width:120px;">
						<select name="sec_email3" class="seclock_inp">
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
				<tr style="height:40px;">
					<td>휴대폰</td>
					<td>
						<select name="sec_hp1" class="seclock_inp">
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
						<input type="text" name="sec_hp2" style="width:100px;" class="seclock_inp" maxlength="4"> - 
						<input type="text" name="sec_hp3" style="width:100px;" class="seclock_inp" maxlength="4">
					</td>
				</tr>
			</table>
		</div>
		<div style="margin:30px 0 0 0;border:1px #777777 solid;padding:35px 10px 10px 10px;font-weight:normal;font-size:13px;">
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td>1. 당신의 귀금속 구매 목적은 무엇입니까?</td>
				</tr>
				<tr>
					<td>
						<input type="radio" name="sec_radio1" value="투자"> 투자
						<input type="radio" name="sec_radio1" value="수집"> 수집
						<input type="radio" name="sec_radio1" value="둘다"> 둘다
						<input type="radio" name="sec_radio1" value="기타"> 기타
					</td>
				</tr>
				<tr style="height:15px;"><td></td></tr>
				<tr>
					<td>2. 보유하신 금속 중 가장 많은 비율의 금속은?</td>
				</tr>
				<tr>
					<td>
						<input type="radio" name="sec_radio2" value="금"> 금
						<input type="radio" name="sec_radio2" value="은"> 은
						<input type="radio" name="sec_radio2" value="백금"> 백금
						<input type="radio" name="sec_radio2" value="기타"> 기타
					</td>
				</tr>
				<tr style="height:15px;"><td></td></tr>
				<tr>
					<td>3. 보유 하고 있는 중량은 대략 얼마나 되십니까?</td>
				</tr>
				<tr>
					<td>
						<input type="radio" name="sec_radio3" value="1kg 이하"> 1kg 이하
						<input type="radio" name="sec_radio3" value="1kg~10kg"> 1kg~10kg
						<input type="radio" name="sec_radio3" value="10kg~30kg"> 10kg~30kg
						<input type="radio" name="sec_radio3" value="30kg 이상"> 30kg 이상
					</td>
				</tr>
				<tr style="height:15px;"><td></td></tr>
				<tr>
					<td>4. 구매하신 귀금속의 구매가를 기록 하고 있습니까?</td>
				</tr>
				<tr>
					<td>
						<input type="radio" name="sec_radio4" value="yes"> yes
						<input type="radio" name="sec_radio4" value="no"> no
					</td>
				</tr>
				<tr style="height:15px;"><td></td></tr>
				<tr>
					<td>궁금한 사항 있으시면 말씀해 주세요.</td>
				</tr>
				<tr>
					<td>
						<textarea name="sec_radio5" class="seclock_inp" style="width:98%;height:75px;"></textarea>
					</td>
				</tr>
			</table>
		</div>

		<div style="font-style:italic;font-size:12px;">
			컨설팅을 원하신다면, 등록해 주십시오. 코인즈투데이의 담당자가 유선 혹은 메일로 연락 드리겠습니다.
		</div>

		<div style="text-align:right;">
			<a href="javascript:void(0);" class="submit_bn submit">등록</a>
			<a href="javascript:void(0);" class="submit_bn cancel">취소</a>
		</div>
	</div>
</form>
</div>