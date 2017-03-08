<?
include_once('./_common.php');

$chk = sql_fetch("select count(*) as cnt from {$g5['g5_idpw_auth_table']} where code='".$_POST[code]."' ");

if(!$chk[cnt]){
	alert("잘못된 접근 경로입니다.");
	exit;
}

sql_query("
update {$g5['member_table']} set
mb_password='".sql_password($_POST[mb_pw])."'
where mb_name='".$_POST[name]."'
and mb_birth='".$_POST[birth]."'
and replace(mb_hp, '-', '')='".$_POST[hp]."'
");

alert("정상적으로 변경 되었습니다.", "/");
?>