<?php
include_once("./_common.php");

	header("Content-Type: text/html; charset=$g4[charset]");
	
	// wetoz.kr : 위토즈개발 : 2013-07-03
	$mode = strtolower($mode);
	$bfno = strtolower($bfno);
	$fname = strip_tags(addslashes($fname));

	$bfno		= preg_match("/^[0-9]+$/", $bfno) ? $bfno : "";
	$result		= "00";
	$resultMsg	= "";
	$filepath	= "$g4[path]/data/file/$bo_table/";

	
	
	if ($mode == "server") { // 수정일 경우에만 적용 됨.

		$wr = get_write($write_table, $wr_id);
		if (!$wr[wr_id]){
			$result		= "99";
			$resultMsg	= "글이 존재하지 않습니다.\\n\\n글이 삭제되었거나 이동하였을 수 있습니다.";
		}

		if (get_session('ss_bo_table') != $bo_table || get_session('ss_wr_id') != $wr_id) {
			$result		= "99";
			$resultMsg	= "올바른 방법으로 수정하여 주십시오.";
		}

		if ($is_admin == "super") // 최고관리자 통과
			;
		else if ($is_admin == "group") { // 그룹관리자
			$mb = get_member($write[mb_id]);
			if ($member[mb_id] != $group[gr_admin]) { // 자신이 관리하는 그룹인가?
				$result		= "99";
				$resultMsg	= "자신이 관리하는 그룹의 게시판이 아니므로 수정할 수 없습니다.";
			}
			else if ($member[mb_level] < $mb[mb_level]) { // 자신의 레벨이 크거나 같다면 통과
				$result		= "99";
				$resultMsg	= "자신의 권한보다 높은 권한의 회원이 작성한 글은 수정할 수 없습니다.";
			}
		} else if ($is_admin == "board") { // 게시판관리자이면
			$mb = get_member($write[mb_id]);
			if ($member[mb_id] != $board[bo_admin]) { // 자신이 관리하는 게시판인가?
				$result		= "99";
				$resultMsg	= "자신이 관리하는 게시판이 아니므로 수정할 수 없습니다.";
			}
			else if ($member[mb_level] < $mb[mb_level]) { // 자신의 레벨이 크거나 같다면 통과
				$result		= "99";
				$resultMsg	= "자신의 권한보다 높은 권한의 회원이 작성한 글은 수정할 수 없습니다.";
			}
		} else if ($member[mb_id]) {
			if ($member[mb_id] != $write[mb_id]) {
				$result		= "99";
				$resultMsg	= "자신의 글이 아니므로 수정할 수 없습니다.";	
			}
		} else {
			if ($write[mb_id]) {
				$result		= "99";
				$resultMsg	= "로그인 후 수정하세요.";
			}
		}
		
		
		if ($result == "00") 
		{ 
			$sql = " select bf_source, bf_file from $g4[board_file_table] where bo_table = '$bo_table' and wr_id = '$wr_id' and bf_no = '$bfno' ";
			$file = sql_fetch($sql);
			if (!$file[bf_file]) {
				$result		= "99";
				$resultMsg	= "파일 정보가 존재하지 않습니다.";
			}
			else {
				$filepath = $filepath . $file["bf_file"];
				$filepath = addslashes($filepath);
				/*
				if (!is_file($filepath) || !file_exists($filepath)) {
					$result		= "99";
					$resultMsg	= "파일이 존재하지 않습니다.";
				}
				*/
				sql_query(" delete from $g4[board_file_table] where bo_table = '$bo_table' and wr_id = '$wr_id' and bf_no = '$bfno' ");
				@unlink($filepath);

				$result		= "00";
				$resultMsg	= "정상적으로 삭제되었습니다.";
	
			}
		} 
	} 
	else { // 글 저장전 처리

		$filepath = $filepath . $fname;
		$filepath = addslashes($filepath);
		@unlink($filepath);

	}

	die("{\"result\":\"$result\",\"resultMsg\":\"$resultMsg\"}");
?>