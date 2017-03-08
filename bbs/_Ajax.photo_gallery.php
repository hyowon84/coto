<?
include_once("./_common.php");


if($_POST[mode] == "recomm"){

	$recomm_res = sql_query("select * from {$g5['g5_photo_gal_recomm_table']} where mb_id='".$member[mb_id]."' and wr_id='".$_POST[wr_id]."' ");
	$recomm_num = mysql_num_rows($recomm_res);

	if($recomm_num){
		echo "n";
		exit;
	}else{
		sql_query("
			insert into {$g5['g5_photo_gal_recomm_table']} set
			mb_id='".$member[mb_id]."',
			date='".strtotime("now")."',
			wr_id='".$_POST[wr_id]."'
		");

		sql_query("
		update {$g5['g5_write_bestshot_table']} set
		wr_1='".$_POST[num]."'
		where wr_parent='".$_POST[wr_id]."'
		and wr_reply=''
		");

		sql_query("
		update {$g5['g5_write_photo_gallery_table']} set
		wr_1='".$_POST[num]."'
		where wr_parent='".$_POST[wr_id]."'
		and wr_reply=''
		");

		echo "y";
		exit;
	}
}else if($_POST[mode] == "bestshot"){
	
	chmod("../data/file/photo_gallery/", 0707);
	chmod("../data/file/bestshot/", 0707);

	$bestS_res = sql_query("select * from {$g5['g5_write_bestshot_table']} where wr_parent='".$_POST[wr_id]."' ");
	$bestS_num = mysql_num_rows($bestS_res);

	if($bestS_num){
		echo "n";
		exit;
	}else{
		
		$gall_row = sql_fetch("select * from {$g5['g5_write_photo_gallery_table']} where wr_id='".$_POST[wr_id]."' ");
		
		//게시글 복사
		$sql = " insert into {$g5['g5_write_bestshot_table']}
					set  wr_id = '".$gall_row[wr_id]."',
						 wr_num = '".$gall_row[wr_num]."',
						 wr_reply = '".$gall_row[wr_reply]."',
						 wr_parent = '".$gall_row[wr_parent]."',
						 wr_comment = ".$gall_row[wr_comment].",
						 ca_name = '".$gall_row[ca_name]."',
						 wr_option = '".$gall_row[wr_option]."',
						 wr_subject = '".$gall_row[wr_subject]."',
						 wr_content = '".$gall_row[wr_content]."',
						 wr_link1 = '".$gall_row[wr_link1]."',
						 wr_link2 = '".$gall_row[wr_link2]."',
						 wr_link1_hit = ".$gall_row[wr_link1_hit].",
						 wr_link2_hit = ".$gall_row[wr_link2_hit].",
						 wr_hit = ".$gall_row[wr_hit].",
						 wr_good = ".$gall_row[wr_good].",
						 wr_nogood = ".$gall_row[wr_nogood].",
						 mb_id = '".$gall_row[mb_id]."',
						 wr_password = '".$gall_row[wr_password]."',
						 wr_name = '".$gall_row[wr_name]."',
						 wr_email = '".$gall_row[wr_email]."',
						 wr_homepage = '".$gall_row[wr_homepage]."',
						 wr_datetime = '".date("Y-m-d H:i:s", strtotime("now"))."',
						 wr_last = '".date("Y-m-d H:i:s", strtotime("now"))."',
						 wr_ip = '".$gall_row[wr_ip]."',
						 wr_1 = '".$gall_row[wr_1]."',
						 wr_2 = '".$gall_row[wr_2]."',
						 wr_3 = '".$gall_row[wr_3]."',
						 wr_4 = '".$gall_row[wr_4]."',
						 wr_5 = '".$gall_row[wr_5]."',
						 wr_6 = '".$gall_row[wr_6]."',
						 wr_7 = '".$gall_row[wr_7]."',
						 wr_8 = '".$gall_row[wr_8]."',
						 wr_9 = '".$gall_row[wr_9]."',
						 wr_10 = '".$gall_row[wr_10]."' ";

		sql_query($sql);

		//파일 복사
		$file_res = sql_query("select * from {$g5['board_file_table']} where bo_table='photo_gallery' and wr_id='".$_POST[wr_id]."' ");
		$file_num = mysql_num_rows($file_res);

		if($file_num){
			for($i = 0; $file_row = mysql_fetch_array($file_res); $i++){
				$sql = "
					insert into {$g5['board_file_table']} set
					bo_table = 'bestshot',
					wr_id = '".$file_row[wr_id]."',
					bf_no = '".$file_row[bf_no]."',
					bf_source = '".$file_row[bf_source]."',
					bf_file = '".$file_row[bf_file]."',
					bf_download = '".$file_row[bf_download]."',
					bf_content = '".$file_row[bf_content]."',
					bf_filesize = '".$file_row[bf_filesize]."',
					bf_width = '".$file_row[bf_width]."',
					bf_height = '".$file_row[bf_height]."',
					bf_type = '".$file_row[bf_type]."',
					bf_datetime = '".$file_row[bf_datetime]."'
				";

				sql_query($sql);

				copy("../data/file/photo_gallery/".$file_row[bf_file], "../data/file/bestshot/".$file_row[bf_file]);
			}
		}

		//댓글 복사
		$gall_com_res = sql_query("select * from {$g5['g5_write_photo_gallery_table']} where wr_parent='".$_POST[wr_id]."' and wr_is_comment != '0' ");
		$gall_com_num = mysql_num_rows($gall_com_res);

		if($gall_com_num){
			for($i = 0; $i < $gall_com_row = mysql_fetch_array($gall_com_res); $i++){
				$sql = " insert into {$g5['g5_write_bestshot_table']}
					set wr_num = '".$gall_com_row[wr_num]."',
						 wr_reply = '".$gall_com_row[wr_reply]."',
						 wr_parent = '".$gall_com_row[wr_parent]."',
						 wr_is_comment = '".$gall_com_row[wr_is_comment]."',
						 wr_comment = '".$gall_com_row[wr_comment]."',
						 wr_comment_reply = '".$gall_com_row[wr_comment_reply]."',
						 ca_name = '".$gall_com_row[ca_name]."',
						 wr_option = '".$gall_com_row[wr_option]."',
						 wr_subject = '".$gall_com_row[wr_subject]."',
						 wr_content = '".$gall_com_row[wr_content]."',
						 wr_link1 = '".$gall_com_row[wr_link1]."',
						 wr_link2 = '".$gall_com_row[wr_link2]."',
						 wr_link1_hit = '".$gall_com_row[wr_link1_hit]."',
						 wr_link2_hit = '".$gall_com_row[wr_link2_hit]."',
						 wr_hit = '".$gall_com_row[wr_hit]."',
						 wr_good = '".$gall_com_row[wr_good]."',
						 wr_nogood = '".$gall_com_row[wr_nogood]."',
						 mb_id = '".$gall_com_row[mb_id]."',
						 wr_password = '".$gall_com_row[wr_password]."',
						 wr_name = '".$gall_com_row[wr_name]."',
						 wr_email = '".$gall_com_row[wr_email]."',
						 wr_homepage = '".$gall_com_row[wr_homepage]."',
						 wr_datetime = '".date("Y-m-d H:i:s", strtotime("now"))."',
						 wr_last = '".date("Y-m-d H:i:s", strtotime("now"))."',
						 wr_ip = '".$gall_com_row[wr_ip]."',
						 wr_1 = '".$gall_com_row[wr_1]."',
						 wr_2 = '".$gall_com_row[wr_2]."',
						 wr_3 = '".$gall_com_row[wr_3]."',
						 wr_4 = '".$gall_com_row[wr_4]."',
						 wr_5 = '".$gall_com_row[wr_5]."',
						 wr_6 = '".$gall_com_row[wr_6]."',
						 wr_7 = '".$gall_com_row[wr_7]."',
						 wr_8 = '".$gall_com_row[wr_8]."',
						 wr_9 = '".$gall_com_row[wr_9]."',
						 wr_10 = '".$gall_com_row[wr_10]."' ";
				//echo $sql."</br>";
				
				sql_query($sql);
			}
			//exit;
		}

		echo "y";
		exit;
	}
}
?>