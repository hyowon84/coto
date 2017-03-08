<?
include_once('./_common.php');

if($_POST[HTTP_CHK] != "CHK_OK") alert("잘못된 접근방식입니다.");

//chmod("../../data/item_type_icon", 0707);
//chmod("../../data/gpitem_type_icon", 0707);

if($mode == "w"){

	if(move_uploaded_file($_FILES[item_type_img][tmp_name], "../../data/gpitem_type_icon/".strtotime("now")."_".$_FILES[item_type_img][name])){
		//echo "성공";

		sql_query("
		insert into {$g5['g5_gp_item_type_icon_table']} set
		tp_name='".$_POST[item_type_name]."',
		tp_img='".strtotime("now")."_".$_FILES[item_type_img][name]."'
		");
	}else{
		//echo "실패";
	}

}else{

	if($_FILES[img][name][$_POST[idx]]){
		$row = sql_fetch("select * from {$g5['g5_gp_item_type_icon_table']} where no='".$_POST[idx]."' ");
		unlink("../../data/gpitem_type_icon/".$row[tp_img]);

		if(move_uploaded_file($_FILES[img][tmp_name][$_POST[idx]], "../../data/gpitem_type_icon/".strtotime("now")."_".$_FILES[img][name][$_POST[idx]])){
			//echo "성공";
			$modify_que = " ,tp_img='".strtotime("now")."_".$_FILES[img][name][$_POST[idx]]."' ";
		}else{
			//echo "실패";
		}
	}

	sql_query("
	update {$g5['g5_gp_item_type_icon_table']} set
	tp_name='".$_POST[item_type_name][$_POST[idx]]."'
	$modify_que
	where no='".$_POST[idx]."'
	");
}

alert("성공적으로 등록 되었습니다.", "./gpitemtypeicon.php");
?>