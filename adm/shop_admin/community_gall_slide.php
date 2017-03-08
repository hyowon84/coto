<?php
$sub_menu = '400760';
include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$token = get_token();

$g5['title'] = '커뮤니티슬라이드관리';
include_once (G5_ADMIN_PATH.'/admin.head.php');

$community_slide_res = sql_query("select * from {$g5['g5_community_gall_slide_table']} order by no desc ");
$community_slide_num = mysql_num_rows($community_slide_res);
?>

<section id="scp_list">
    <h2>커뮤니티슬라이드(갤러리)관리</h2>

    <div class="tbl_head01 tbl_wrap">
        <table>
        <caption>커뮤니티슬라이드(갤러리)관리</caption>
        <tbody>

		<?
		for($i = 0; $i < $community_slide_row = mysql_fetch_array($community_slide_res); $i++){
		?>
        <tr class="<?php echo $bg; ?> del<?=$community_slide_row[no]?>">
            <td width="7%"><?=$community_slide_row[no]?></td>
            <td width="20%"><?=$community_slide_row[img_file]?></td>
			<td><?=$community_slide_row[url]?></td>
            <td width="7%"><input type="button" value="삭제" class="del_btn" idx="<?=$community_slide_row[no]?>"></td>
        </tr>
		<?
		}
		?>
        </tbody>
        </table>
    </div>


	<form name="fcommunityslide" id="fcommunityslide" method="post" enctype="multipart/form-data">
	<input type="hidden" name="w" value="d">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <input type="hidden" name="token" value="<?php echo $token; ?>">
	<input type="hidden" name="HTTP_CHK" value="CHK_OK">
	<input type="hidden" name="mode" value="w">

    <div class="btn_list01 btn_list">
		<select name="community_status" style="padding:5px;height:32px;">
			<option value="community_gall">커뮤니티(갤러리)</option>
		</select>

		<input type="file" name="community_img" style="padding:5px;height:20px;">
		URL : <input type="text" name="community_url" style="padding:5px;width:250px;height:20px;">
        <input type="button" value="이미지 등록" style="padding:5px;height:32px;" class="community_slide_submit">
    </div>
	</form>

</section>

<div class="test"></div>

<script type="text/javascript">

$(document).ready(function(){

	$(".community_slide_submit").click(function(){
		
		$("form[name='fcommunityslide']").attr("action", "./community_slide_update.php").submit();
		
	});

	$(".del_btn").click(function(){
		var idx = $(this).attr("idx");
		
		if(confirm("정말 삭제하시겠습니까?")){

			$.ajax({
				type : "POST",
				dataType : "HTML",
				url : "./_Ajax.community_gall_slide.php",
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