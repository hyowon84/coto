<?
include_once('./_common.php');

$sql = "
	update g5_main_slide set
	status=''
";
sql_query($sql);

for($i = 0; $i < count($_POST[dis_status]); $i++){
	$sql = "
		update g5_main_slide set
		status='2'
		where no='".$_POST[dis_status][$i]."'
	";
	sql_query($sql);
}

alert("정상적으로 수정 되었습니다.", "./main_slide.php");
?>