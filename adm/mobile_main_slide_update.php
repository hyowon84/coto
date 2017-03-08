<?
$sub_menu = "800010";
$sub_sub_menu = "1";

include_once('./_common.php');

if($member[admin_yn] != "Y") alert("관리자만 이용 가능합니다.");

$path = G5_DATA_PATH."/mobile_main_img";


if($_POST[mode] == "w"){
	

	$file_name_arr = explode(".", $_FILES[img_file][name]);
	$file_name = $file_name_arr[0]."_".strtotime("now");
	$file_ext = $file_name_arr[1];

	move_uploaded_file($_FILES[img_file][tmp_name], $path."/".$file_name.".".$file_ext);


	sql_query("
	insert into g5_mobile_main_slide set
	img_file='".$file_name.".".$file_ext."',
	status='".$_POST[status]."',
	date='".strtotime("now")."',
	URL='".$_POST[URL]."'
	");

	goto_url("mobile_main_slide.php", "등록이 완료 되었습니다.");

}else if($_POST[mode] == "u"){

	
	if($_FILES[img_file][name]){
		$del_img = sql_fetch("select * from g5_mobile_main_slide where no='$no' ");

		if($del_img[img_file]){
			unlink($path."/".$del_img[img_file]);
		}

		$file_name_arr = explode(".", $_FILES[img_file][name]);
		$file_name = $file_name_arr[0]."_".strtotime("now");
		$file_ext = $file_name_arr[1];

		move_uploaded_file($_FILES[img_file][tmp_name], $path."/".$file_name.".".$file_ext);

		sql_query("
		update g5_mobile_main_slide set
		img_file='$file_name.".".$file_ext',
		status='".$_POST[status]."',
		date='".strtotime("now")."',
		URL='".$_POST[URL]."'
		where no='$no'
		");
	}else{

		sql_query("
		update g5_mobile_main_slide set
		status='".$_POST[status]."',
		date='".strtotime("now")."',
		URL='".$_POST[URL]."'
		where no='$no'
		");

	}

	

	goto_url("mobile_main_slide.php", "수정이 완료 되었습니다.");
	

}else if($_POST[mode] == "d"){

	$no = $_POST[no];

	$del_img = sql_fetch("select * from g5_mobile_main_slide where no='$no' ");

	if($del_img[img_file]){
		unlink($path."/".$del_img[img_file]);
	}
	sql_query("delete from g5_mobile_main_slide where no='$no' ");

	alert("삭제가 완료 되었습니다.","mobile_main_slide.php");

}
?>