<?
include_once('./_common.php');

if($mode == "del"){
	$row = sql_fetch("select * from {$g5['g5_item_type_icon_table']} where no='".$_POST[idx]."' ");

	unlink("../../data/item_type_icon/".$row[tp_img]);

	sql_query("
	delete from {$g5['g5_item_type_icon_table']} where no='".$_POST[idx]."'
	");
}
?>