<?
include_once('./_common.php');

if($_SESSION[quick_status] == "" || $_SESSION[quick_status] == "1"){
	unset($_SESSION[quick_status]);
	set_session("quick_status", "2");
}else{
	unset($_SESSION[quick_status]);
	set_session("quick_status", "1");
}

?>