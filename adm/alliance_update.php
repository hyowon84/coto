<?
include_once("./_common.php");

if($_POST[HTTP_CHK] != "CHK_OK"){ alert("잘못된 접근 방식입니다.");}

$sql = "
update {$g5['g5_alliance_table']} set
com_info='".$_POST[com_info]."',
name='".$_POST[name]."',
content='".$_POST[content]."',
reply_status='".$_POST[reply_status]."'
where no='".$_POST[no]."'
";

sql_query($sql);

alert("수정이 완료 되었습니다.", "./alliance.php");
?>