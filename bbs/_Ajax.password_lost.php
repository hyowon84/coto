<?
include_once('./_common.php');

$row = sql_fetch("select * from {$g5['member_table']} where mb_name='".$_POST[name]."' and REPLACE(mb_hp, '-', '')='".$_POST[tel_no]."' ");

$gender = ($row[mb_sex] == 'w' || $row[mb_sex] == 'W') ? 0 : 1;
$arr = array("birthday" => $row[mb_birth], "gender" => $gender);
echo json_encode($arr);
?>