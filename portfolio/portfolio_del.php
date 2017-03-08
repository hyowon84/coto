<?
include_once("./_common.php");

if($_POST['HTTP_CHK'] != "CHK_OK") alert("잘못된 접근방식입니다.");

$wr_id = $_POST['wr_id'];

$row = sql_fetch("select * from g5_write_portfolio where wr_id=$wr_id ");

$path = "../data/file/portfolio/";
chmod($path, 0707);

if(is_file($path.$row[wr_1])){
	unlink($path.$row[wr_1]);
}

sql_query("delete from g5_write_portfolio where wr_id=$wr_id ");

alert("정상적으로 삭제 되었습니다.", G5_URL."/bbs/board.php?bo_table=portfolio");
?>