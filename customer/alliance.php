<?php
include_once('./_common.php');

if (G5_IS_MOBILE) {
    include_once(G5_MSHOP_PATH.'/head.php');
} else {
    include_once(G5_PATH.'/head.php');
}

?>

<style type="text/css">
.title{font-size: 1.5em;padding: 40px 4.166666666666667% 15px;font-weight: bold;background-color: #F3F7F8}
</style>

<div class="title">
	<span>제휴문의</span>
	<span style="font-size:1.108695652173913em;font-weight:normal;"><?=$tit_sub?></span>
</div>

<form name="falliance" id="falliance" method="POST" enctype="multipart/form-data">

<input type="hidden" name="HTTP_CHK" value="CHK_OK">

<div class="tbl_head02 tbl_wrap alliance_tb">
    <table>
    <thead>
    <tr>
        <th scope="col" width="80px">기업 정보</th>
		<td>
			<input type="text" name="com_info" size="20" class="frm_input">
		</td>
    </tr>
	<tr>
        <th scope="col" width="80px">담당자명</th>
		<td>
			<input type="text" name="name" size="20" class="frm_input">
		</td>
    </tr>
	<tr>
        <th scope="col" width="80px" valign="top">문의내용</th>
		<td>
			<span>내용은 최소 10자이상, 최대 1000자미만 입력해주세요.</span>
			<textarea name="content" id="wr_content" style="width:98%;height:100px;"></textarea>
			<div id="char_count_wrap" style="text-align:right;"><span id="char_count"></span> / 1000자</div>
		</td>
    </tr>
	<tr>
        <th scope="col" width="80px">연락처</th>
		<td>
			<select name="tel1" class="frm_input">
				<option value="">선택</option>
				<option value="02">02</option>
				<option value="051">051</option>
				<option value="053">053</option>
				<option value="032">032</option>
				<option value="062">062</option>
				<option value="042">042</option>
				<option value="052">052</option>
				<option value="044">044</option>
				<option value="031">031</option>
				<option value="033">033</option>
				<option value="043">043</option>
				<option value="041">041</option>
				<option value="063">063</option>
				<option value="061">061</option>
				<option value="054">054</option>
				<option value="055">055</option>
				<option value="064">064</option>
			</select>&nbsp;&nbsp;-&nbsp;&nbsp;
			<input type="text" name="tel2" class="frm_input" size="5" maxlength="4">&nbsp;&nbsp;-&nbsp;&nbsp;
			<input type="text" name="tel3" class="frm_input" size="5" maxlength="4">
		</td>
    </tr>
	<tr>
        <th scope="col" width="80px">휴대폰</th>
		<td>
			<select name="hp1" class="frm_input">
				<option value="">선택</option>
				<option value="010">010</option>
				<option value="011">011</option>
				<option value="016">016</option>
				<option value="017">017</option>
				<option value="018">018</option>
				<option value="019">019</option>
				<option value="0505">0505</option>
				<option value="0502">0502</option>
			</select>&nbsp;&nbsp;-&nbsp;&nbsp;
			<input type="text" name="hp2" class="frm_input" size="5" maxlength="4">&nbsp;&nbsp;-&nbsp;&nbsp;
			<input type="text" name="hp3" class="frm_input" size="5" maxlength="4">
		</td>
    </tr>
	<tr>
        <th scope="col" width="80px" class="frm_email">아이디(이메일)</th>
		<td>
			<input type="text" name="email1" size="15" class="frm_input">
		</td>
    </tr>
	<tr>
        <th scope="col" width="80px">파일첨부</th>
		<td>
			<input type="file" name="al_file" size="20" class="frm_input">
		</td>
    </tr>
	</thead>

	<tbody>

	</tbody>
	</table>
    <div>
        <input type="button" value="등록" class="al_submit al_button">
        <input type="button" value="취소" class="al_cancel al_button">
    </div>
</div>
</form>

<script type="text/javascript">

// 글자수 제한
var char_min = parseInt(10); // 최소
var char_max = parseInt(1000); // 최대
check_byte("wr_content", "char_count");

$(function() {
	$("#wr_content").on("keyup", function() {
		check_byte("wr_content", "char_count");
	});
});

$(document).ready(function(){
	$("select[name='email3']").change(function(){
		$("input[name='email2']").val($(this).val());
	});

	$(".al_submit").click(function(){

		if (document.getElementById("char_count")) {
            if (char_min > 0 || char_max > 0) {
                var cnt = parseInt(check_byte("wr_content", "char_count"));
                if (char_min > 0 && char_min > cnt) {
                    alert("내용은 "+char_min+"글자 이상 쓰셔야 합니다.");
                    return false;
                }
                else if (char_max > 0 && char_max < cnt) {
                    alert("내용은 "+char_max+"글자 이하로 쓰셔야 합니다.");
                    return false;
                }
            }
        }

		if(confirm("제휴문의를 등록 하시겠습니까?")){
			$("form[name='falliance']").attr("action", "./alliance_update.php").submit();
		}
	});

	$(".al_cancel").click(function(){
		history.go(-1);
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