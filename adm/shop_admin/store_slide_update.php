<?
include_once('./_common.php');

if($_POST[HTTP_CHK] != "CHK_OK") alert("잘못된 접근방식입니다.");

//chmod("../../data/store_slide", 0707);

if($mode == "w"){

	if(move_uploaded_file($_FILES[store_img][tmp_name], "../../data/store_slide/".strtotime("now")."_".$_POST[store_status]."_".$_FILES[store_img][name])){
		//echo "성공";

		sql_query("
		insert into g5_store_slide set
		img_file='".strtotime("now")."_".$_POST[store_status]."_".$_FILES[store_img][name]."',
		url='".$store_url."',
		status='".$_POST[store_status]."',
		date='".strtotime("now")."'
		");
	}else{
		//echo "실패";
	}

}

alert("성공적으로 등록 되었습니다.", "./store_slide.php");
?>