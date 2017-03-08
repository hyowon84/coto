<?php
$sub_menu = "200800";
include_once('./_common.php');
include_once('./admin.head.php');

auth_check($auth[$sub_menu], 'r');

$g5['title'] = '제휴문의';

$sql = "select * from {$g5['g5_alliance_table']} where no='".$no."' ";

$result = sql_query($sql);
$row = mysql_fetch_array($result);

$tel_arr = explode("-", $row["tel"]);
$hp_arr = explode("-", $row["hp"]);
$email_arr = explode("@", $row["email"]);
?>

<form name="falliance" id="falliance" method="POST" enctype="multipart/form-data">
<input type="hidden" name="no" value="<?=$no?>">
<input type="hidden" name="HTTP_CHK" value="CHK_OK">

<div class="tbl_head02 tbl_wrap alliance_tb">
    <table>
    <thead>
    <tr>
        <th scope="col" width="200px">기업 정보</th>
		<td>
			<input type="text" name="com_info" size="20" class="frm_input" value="<?=$row[com_info]?>">
		</td>
    </tr>
	<tr>
        <th scope="col" width="200px">담당자명</th>
		<td>
			<input type="text" name="name" size="20" class="frm_input" value="<?=$row[name]?>">
		</td>
    </tr>
	<tr>
        <th scope="col" width="200px" valign="top">문의내용</th>
		<td>
			<textarea name="content" style="width:98%;height:100px;"><?=$row[content]?></textarea>
		</td>
    </tr>
	<tr>
        <th scope="col" width="200px">연락처</th>
		<td>
			<select name="tel1" class="frm_input">
				<option value="">선택</option>
				<option value="02" <?if($tel_arr[0] == "02"){echo "selected";}?>>02</option>
				<option value="051" <?if($tel_arr[0] == "051"){echo "selected";}?>>051</option>
				<option value="053" <?if($tel_arr[0] == "053"){echo "selected";}?>>053</option>
				<option value="032" <?if($tel_arr[0] == "032"){echo "selected";}?>>032</option>
				<option value="062" <?if($tel_arr[0] == "062"){echo "selected";}?>>062</option>
				<option value="042" <?if($tel_arr[0] == "042"){echo "selected";}?>>042</option>
				<option value="052" <?if($tel_arr[0] == "052"){echo "selected";}?>>052</option>
				<option value="044" <?if($tel_arr[0] == "044"){echo "selected";}?>>044</option>
				<option value="031" <?if($tel_arr[0] == "031"){echo "selected";}?>>031</option>
				<option value="033" <?if($tel_arr[0] == "033"){echo "selected";}?>>033</option>
				<option value="043" <?if($tel_arr[0] == "043"){echo "selected";}?>>043</option>
				<option value="041" <?if($tel_arr[0] == "041"){echo "selected";}?>>041</option>
				<option value="063" <?if($tel_arr[0] == "063"){echo "selected";}?>>063</option>
				<option value="061" <?if($tel_arr[0] == "061"){echo "selected";}?>>061</option>
				<option value="054" <?if($tel_arr[0] == "054"){echo "selected";}?>>054</option>
				<option value="055" <?if($tel_arr[0] == "055"){echo "selected";}?>>055</option>
				<option value="064" <?if($tel_arr[0] == "064"){echo "selected";}?>>064</option>
			</select> -
			<input type="text" name="tel2" class="frm_input" size="5" maxlength="4" value="<?=$tel_arr[1]?>"> -
			<input type="text" name="tel3" class="frm_input" size="5" maxlength="4" value="<?=$tel_arr[2]?>">
		</td>
    </tr>
	<tr>
        <th scope="col" width="200px">휴대폰</th>
		<td>
			<select name="hp1" class="frm_input">
				<option value="">선택</option>
				<option value="010" <?if($hp_arr[0] == "010"){echo "selected";}?>>010</option>
				<option value="011" <?if($hp_arr[0] == "011"){echo "selected";}?>>011</option>
				<option value="016" <?if($hp_arr[0] == "016"){echo "selected";}?>>016</option>
				<option value="017" <?if($hp_arr[0] == "017"){echo "selected";}?>>017</option>
				<option value="018" <?if($hp_arr[0] == "018"){echo "selected";}?>>018</option>
				<option value="019" <?if($hp_arr[0] == "019"){echo "selected";}?>>019</option>
				<option value="0505" <?if($hp_arr[0] == "0505"){echo "selected";}?>>0505</option>
				<option value="0502" <?if($hp_arr[0] == "0502"){echo "selected";}?>>0502</option>
			</select> -
			<input type="text" name="hp2" class="frm_input" size="5" maxlength="4" value="<?=$hp_arr[1]?>"> -
			<input type="text" name="hp3" class="frm_input" size="5" maxlength="4" value="<?=$hp_arr[2]?>">
		</td>
    </tr>
	<tr>
        <th scope="col" width="200px">이메일</th>
		<td>
			<input type="text" name="email1" size="15" class="frm_input" value="<?=$email_arr[0]?>"> @
			<input type="text" name="email2" size="15" class="frm_input" value="<?=$email_arr[1]?>">
			<select name="email3" class="frm_input">
				<option value="">직접입력</option>
				<option value="naver.com">naver.com</option>
				<option value="nate.com">nate.com</option>
				<option value="yahoo.co.kr">yahoo.co.kr</option>
				<option value="hanmail.net">hanmail.net</option>
			</select>
		</td>
    </tr>
	<tr>
        <th scope="col" width="200px">파일첨부</th>
		<td>
			<a href="./alliance_download.php?name=<?=$row[file]?>"><?=$row[file]?></a>
		</td>
    </tr>

	<tr>
        <th scope="col" width="200px">답변여부</th>
		<td>
			<input type="radio" name="reply_status" value="y" <?if($row[reply_status] == "y"){echo "checked";}?>> 답변완료
			<input type="radio" name="reply_status" value="n" <?if($row[reply_status] == "n"){echo "checked";}?>> 미답변
		</td>
    </tr>

	<tr>
		<td colspan="2" align="center" style="border:0px;">
			<input type="button" value="등록" class="al_submit al_button">
			<input type="button" value="취소" class="al_cancel al_button">
		</td>
	</tr>
	</thead>

	<tbody>

	</tbody>
	</table>
</div>
</form>

<script type="text/javascript">

$(document).ready(function(){
	$("select[name='email3']").change(function(){
		$("input[name='email2']").val($(this).val());
	});

	$(".al_submit").click(function(){
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
include_once('./admin.tail.php');
?>
