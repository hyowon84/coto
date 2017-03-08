<?php
$sub_menu = "800120";
$sub_sub_menu = "1";

include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$token = get_token();

$g5['title'] = '실물투자컨설팅 파일관리';
include_once (G5_ADMIN_PATH.'/admin.head.php');

$consultingR = sql_fetch("select * from {$g5['g5_consulting_file_table']}  order by csf_idx desc ");
?>

<section id="scp_list">
    <h2><?php echo $g5['title']?></h2>

	<form name="fstoreslide" id="fstoreslide" method="post" enctype="multipart/form-data">
	<input type="hidden" name="w" value="d">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <input type="hidden" name="token" value="<?php echo $token; ?>">
	<input type="hidden" name="HTTP_CHK" value="CHK_OK">
	<input type="hidden" name="csf_idx" value="1">

    <div class="btn_list01 btn_list">
		<input type="file" name="csf_file" style="padding:5px;height:20px;">
        <input type="button" value="파일 등록" style="padding:5px;" class="store_slide_submit">
		<?php if($consultingR['csf_source']){?>
		<br />첨부파일명 : <a href="<?php echo G5_BBS_URL?>/consulting_download.php?csf_idx=<?php echo $consultingR['csf_idx']?>"><?php echo $consultingR['csf_source'];?></a>
		<?php }?>
    </div>
	</form>

</section>

<div class="test"></div>

<script type="text/javascript">

$(document).ready(function(){

	$(".store_slide_submit").click(function(){
		
		$("form[name='fstoreslide']").attr("action", "./consultingfile_form_update.php").submit();
		
	});

});

</script>

<?php
include_once(G5_ADMIN_PATH.'/admin.tail.php');
?>