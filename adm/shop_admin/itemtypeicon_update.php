<?
$sub_menu = '400200';
$sub_sub_menu = '2';

include_once('./_common.php');

if($_POST[HTTP_CHK] != "CHK_OK") alert("잘못된 접근방식입니다.");

//chmod("../../data/item_type_icon", 0707);

if($mode == "w"){

	if(move_uploaded_file($_FILES[item_type_img][tmp_name], "../../data/item_type_icon/".strtotime("now")."_".$_FILES[item_type_img][name])){
		//echo "성공";

		sql_query("
		insert into {$g5['g5_item_type_icon_table']} set
		tp_name='".$_POST[item_type_name]."',
		tp_img='".strtotime("now")."_".$_FILES[item_type_img][name]."'
		");
	}else{
		//echo "실패";
	}

}else{

	$row = sql_fetch("select * from {$g5['g5_item_type_icon_table']} where no='".$_POST[idx]."' ");
	unlink("../../data/item_type_icon/".$row[tp_img]);
	
	if(move_uploaded_file($_FILES[img][tmp_name][$_POST[idx]], "../../data/item_type_icon/".strtotime("now")."_".$_FILES[img][name][$_POST[idx]])){
		//echo "성공";

		sql_query("
		update {$g5['g5_item_type_icon_table']} set
		tp_name='".$_POST[item_type_name][$_POST[idx]]."',
		tp_img='".strtotime("now")."_".$_FILES[img][name][$_POST[idx]]."'
		where no='".$_POST[idx]."'
		");
	}else{
		//echo "실패";
	}
}

alert("성공적으로 등록 되었습니다.", "./itemtypeicon.php");
?>