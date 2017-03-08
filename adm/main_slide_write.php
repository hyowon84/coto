<?php
$sub_menu = "800010";
$sub_sub_menu = "1";

include_once('./_common.php');

//auth_check($auth[$sub_menu], 'w');

$g5['title'] = '메인 슬라이드 관리';
include_once('./admin.head.php');

if($no){
	$mode = "u";
	$main_slide_row = sql_fetch("select * from g5_main_slide where no='$no' ");
}else{
	$mode = "w";
}

?>

<form name="fmain_silde" action="main_slide_update.php" method="POST" enctype="multipart/form-data">
<input type="hidden" name="HTTP_CHK" value="CHK_OK">
<input type="hidden" name="mode" value="<?=$mode?>">
<input type="hidden" name="no" value="<?=$no?>">

<div class="tbl_frm01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?></caption>
    <colgroup>
        <col class="grid_4">
        <col>
    </colgroup>
    <tbody>
    <tr>
        <th scope="row">이미지 첨부</th>
        <td>
            <input type="file" name="img_file"> <?=$main_slide_row[img_file]?>
        </td>
    </tr>
	<tr>
		<th scope="row">URL</th>
		<td>
			<input type="text" style="width:300px;height:20px;" name="URL" value="<?=$main_slide_row[URL]?>">
		</td>
	</tr>
	<tr>
		<th scope="row">공개 여부</th>
		<td>
			<?if($no){?>
				<input type="checkbox" name="status" value="2" <?if($main_slide_row[status] == "2"){ echo "checked";}?>>
			<?}else{?>
				<input type="checkbox" name="status" value="2" checked>
			<?}?>
			<font color="red">* 체크시 공개 됩니다.</font>
		</td>
	</tr>

	<!--
	<tr>
		<th scope="row">백그라운드 여부(패턴, 색깔)</th>
		<td>
			<input type="radio" name="back_status" value="1" <?//if($main_slide_row[back_status] == "1"){echo "checked";}?>>패턴
			<input type="radio" name="back_status" value="2" <?//if($main_slide_row[back_status] == "2"){echo "checked";}?>>색깔
			<input type="radio" name="back_status" value="" <?//if($main_slide_row[back_status] == ""){echo "checked";}?>>없음
		</td>
	</tr>
	<tr class="back_dis">
		<th scope="row">백그라운드</th>
		<td class="back_dis_input">
			
		</td>
	</tr>
	-->

    </tbody>
    </table>
</div>

<div style="text-align:center;">
<input type="button" value="등록" onclick="fmain_silde_submit()">
<input type="button" value="목록" class="list">
</div>
</form>


<script type="text/javascript">

$(document).ready(function(){

<?if($main_slide_row[back_status] != "1" && $main_slide_row[back_status] != "2"){?>
	$(".back_dis").hide();
<?}?>

	$("input:radio[name='back_status']").click(function(){
		var val = $(this).val();
		if(val == "1"){
			$(".back_dis").show();
			$(".back_dis_input").html("<input type='file' name='background_file'>");
		}else if(val == "2"){
			$(".back_dis").show();
			$(".back_dis_input").html("<input type='text' name='background' value=''>");
		}else{
			$(".back_dis").hide();
		}
	});

	$(".list").click(function(){
		goto_url('./main_slide.php');
	});
});

function fmain_silde_submit(){
	if(confirm("등록 하시겠습니까?")){
		document.fmain_silde.submit();
	}
}
</script>

<?php
include_once ('./admin.tail.php');
?>
