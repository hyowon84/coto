<?
include_once("./_common.php");

if($_POST['HTTP_CHK'] != "CHK_OK") alert("잘못된 접근방식입니다.");

$path = "../data/file/portfolio";

$wr_id = $_POST[wr_id];

chmod($path, 0707);

if($_POST[mode] == "w"){

	for($i = 0; $i < count($_POST[port_title]); $i++){
		if($_POST[port_title][$i]){

			$buy1_arr = get_rates_ret(2);
			$buy1 = $buy1_arr[3];
			$buy2 = $_POST[port_buy][$i];
			$buy = $buy1 * $buy2;
			
			$port_file_arr = explode(".", $_FILES[port_file][name][$i]);
			$port_file_name = $port_file_arr[0]."_".$i."_".strtotime("now");
			$port_file_ext = $port_file_arr[1];
			$file_name = $port_file_name.".".$port_file_ext;

			$upload_arr = getimagesize($_FILES[port_file][tmp_name][$i]);
			//exit;

			move_uploaded_file($_FILES[port_file][tmp_name][$i], $path."/".$file_name);
			
			sql_query("
				insert into g5_write_portfolio set
				wr_subject='".$_POST[port_title][$i]."',
				wr_datetime='".date("Y-m-d H:i:s")."',
				mb_id='".$member[mb_id]."',
				wr_last='".date("Y-m-d H:i:s")."',
				wr_ip='".$REMOTE_ADDR."',
				wr_1='".$file_name."',
				wr_2='".$_POST[port_metal][$i]."',
				wr_3='".$_POST[port_cnt][$i]."',
				wr_4='".$_POST[port_oz][$i]."',
				wr_5='".$_POST[port_buy][$i]."',
				wr_6='".$buy."',
				img_width='".$upload_arr[0]."',
				img_height='".$upload_arr[1]."'
			");

			$wr_id = mysql_insert_id();

			sql_query("
			update g5_write_portfolio set
			wr_num='-".$wr_id."',
			wr_parent='".$wr_id."'
			where wr_id=".$wr_id."
			");
		}
	}
}else if($_POST[mode] == "u"){

	if($_POST[port_title]){

		$buy1_arr = get_rates_ret(2);
		$buy1 = $buy1_arr[3];
		$buy2 = $_POST[port_buy];
		$buy = $buy1 * $buy2;
		
		if($_FILES[port_file][name]){

			$img_row = sql_fetch("select * from g5_write_portfolio where wr_id=$wr_id ");

			unlink($path."/".$img_row[wr_1]);

			$port_file_arr = explode(".", $_FILES[port_file][name]);
			$port_file_name = $port_file_arr[0]."_".strtotime("now");
			$port_file_ext = $port_file_arr[1];
			$file_name = $port_file_name.".".$port_file_ext;

			$upload_arr = getimagesize($_FILES[port_file][tmp_name]);
			//exit;

			move_uploaded_file($_FILES[port_file][tmp_name], $path."/".$file_name);

			$img_que = "
			wr_1='".$file_name."',
			img_width='".$upload_arr[0]."',
			img_height='".$upload_arr[1]."',
			";
		}

		$sql = "update g5_write_portfolio set
			wr_subject='".$_POST[port_title]."',
			wr_datetime='".date("Y-m-d H:i:s")."',
			mb_id='".$member[mb_id]."',
			wr_last='".date("Y-m-d H:i:s")."',
			wr_ip='".$REMOTE_ADDR."',
			$img_que
			wr_2='".$_POST[port_metal]."',
			wr_3='".$_POST[port_cnt]."',
			wr_4='".$_POST[port_oz]."',
			wr_5='".$_POST[port_buy]."',
			wr_6='".$buy."'
			where wr_id=$wr_id
			";
		
		sql_query($sql);
	}

}

chmod($path, 0644);

alert("등록이 완료 되었습니다.", G5_URL."/bbs/board.php?bo_table=portfolio");
?>