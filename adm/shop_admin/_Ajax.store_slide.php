<?
include_once('./_common.php');

$row = sql_fetch("select * from g5_store_slide where no='".$_POST[idx]."' ");

unlink("../../data/store_slide/".$row[img_file]);

sql_query("
delete from g5_store_slide where no='".$_POST[idx]."'
");
?>