<?php
$sub_menu = "800010";
$sub_sub_menu = "1";

include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$token = get_token();

$g5['title'] = '상세스토어슬라이드관리';
include_once (G5_ADMIN_PATH.'/admin.head.php');

$store_slide_res = sql_query("select * from g5_store_view_slide order by no desc ");
$store_slide_num = mysql_num_rows($store_slide_res);
?>

<section id="scp_list">
    <h2>상세스토어슬라이드관리</h2>

    <div class="tbl_head01 tbl_wrap">
        <table>
        <caption>상세스토어슬라이드관리</caption>
        <tbody>

		<?
		for($i = 0; $i < $store_slide_row = mysql_fetch_array($store_slide_res); $i++){
		?>
        <tr class="<?php echo $bg; ?> del<?=$store_slide_row[no]?>">
            <td width="7%"><?=$store_slide_row[no]?></td>
            <td width="20%"><?=$store_slide_row[img_file]?></td>
			<td><?=$store_slide_row[url]?></td>
            <td width="7%"><input type="button" value="삭제" class="del_btn" idx="<?=$store_slide_row[no]?>"></td>
        </tr>
		<?
		}
		?>
        </tbody>
        </table>
    </div>


	<form name="fstoreslide" id="fstoreslide" method="post" enctype="multipart/form-data">
	<input type="hidden" name="w" value="d">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <input type="hidden" name="token" value="<?php echo $token; ?>">
	<input type="hidden" name="HTTP_CHK" value="CHK_OK">
	<input type="hidden" name="mode" value="w">

    <div class="btn_list01 btn_list">
		<!--
		<select name="store_status" style="padding:5px;height:32px;">
			<option value="1010">Gold</option>
			<option value="1020">Silver</option>
			<option value="1030">OtherMetal</option>
		</select>
		-->

		<input type="file" name="store_img" style="padding:5px;height:20px;">
		<!--URL : <input type="text" name="store_url" style="padding:5px;width:250px;height:20px;">-->
        <input type="button" value="이미지 등록" style="padding:5px;height:32px;" class="store_slide_submit">
    </div>
	</form>

</section>

<div class="test"></div>

<script type="text/javascript">

$(document).ready(function(){

	$(".store_slide_submit").click(function(){
		
		$("form[name='fstoreslide']").attr("action", "./store_view_slide_update.php").submit();
		
	});

	$(".del_btn").click(function(){
		var idx = $(this).attr("idx");
		
		if(confirm("정말 삭제하시겠습니까?")){

			$.ajax({
				type : "POST",
				dataType : "HTML",
				url : "./_Ajax.store_view_slide.php",
				data : "idx=" + idx,
				success : function(data){
					$(".del" + idx).remove();
				}
			});
		}
	});

});

</script>

<?php
include_once(G5_ADMIN_PATH.'/admin.tail.php');
?>