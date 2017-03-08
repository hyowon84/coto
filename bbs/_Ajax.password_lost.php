<?
include_once('./_common.php');

$row = sql_fetch("select * from {$g5['member_table']} where mb_name='".$_POST[name]."' and REPLACE(mb_hp, '-', '')='".$_POST[tel_no]."' ");

$arr = array("birthday" => $row[mb_birth], "gender" => $row[mb_sex]);
echo json_encode($arr);
?>