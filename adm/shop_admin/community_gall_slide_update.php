<?
include_once('./_common.php');

if($_POST[HTTP_CHK] != "CHK_OK") alert("잘못된 접근방식입니다.");

//chmod("../../data/store_slide", 0707);

if($mode == "w"){

	if(move_uploaded_file($_FILES[community_img][tmp_name], "../../data/community_slide/".strtotime("now")."_".$_POST[community_status]."_".$_FILES[community_img][name])){
		//echo "성공";

		sql_query("
		insert into {$g5['g5_community_slide_table']} set
		img_file='".strtotime("now")."_".$_POST[community_status]."_".$_FILES[community_img][name]."',
		url='".$community_url."',
		status='".$_POST[community_status]."',
		date='".strtotime("now")."'
		");
	}else{
		//echo "실패";
	}

}

alert("성공적으로 등록 되었습니다.", "./community_slide.php");
?>