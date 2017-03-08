<?
include_once('./_common.php');

if($_POST[mode] == "id_chk"){
	$id_chk = sql_fetch("select count(*) as cnt from {$g5['member_table']} where mb_id='".$_POST[id]."' ");
	echo $id_chk[cnt];
}
if($_POST[mode] == "nick_chk"){
	$nick_chk = sql_fetch("select count(*) as cnt from {$g5['member_table']} where mb_nick='".$_POST[nick]."' ");
	echo $nick_chk[cnt];
}
?>