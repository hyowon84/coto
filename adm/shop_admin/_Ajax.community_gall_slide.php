<?
include_once('./_common.php');

$row = sql_fetch("select * from {$g5['g5_community_gall_slide_table']} where no='".$_POST[idx]."' ");

unlink("../../data/community_gall_slide/".$row[img_file]);

sql_query("
delete from {$g5['g5_community_gall_slide_table']} where no='".$_POST[idx]."'
");
?>