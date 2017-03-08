<?php
	include_once("./_common.php");
	include_once("../../../thumb.lib.php");
	include_once("../../../wetoz.config.php");

	$doc_width = preg_match("/^[0-9]+$/", $doc_width) ? $doc_width : THUMB_CONTENT_WIDTH;

	//기본 리다이렉트
	echo $_REQUEST["htImageInfo"];

	// file 디렉토리 생성
	$g4['smart_path'] = $g4['path']."/data/file";
	if (!file_exists($g4['smart_path'])) {
		@mkdir($g4['smart_path'], 0707);
		@chmod($g4['smart_path'], 0707);
	}
	$smart_path_sub = "/".$bo_table;
	$g4['smart_path'] = $g4['smart_path'].$smart_path_sub;
	if (!file_exists($g4['smart_path'])) {
		@mkdir($g4['smart_path'], 0707);
		@chmod($g4['smart_path'], 0707);
	}

	// 디렉토리에 있는 파일의 목록을 보이지 않게 한다.
	$file = $g4['smart_path']."/index.php";
	$f = @fopen($file, "w");
	@fwrite($f, "");
	@fclose($f);
	@chmod($file, 0606);

	$url = $_REQUEST["callback"] .'?callback_func='. $_REQUEST["callback_func"];
	$bSuccessUpload = is_uploaded_file($_FILES['Filedata']['tmp_name']);
	if ($bSuccessUpload) { //성공 시 파일 사이즈와 URL 전송
		
		$orgname	= $_FILES['Filedata']['name'];
		$filesize	= $_FILES['Filedata']['size'];
		$tmp_name	= $_FILES['Filedata']['tmp_name'];
		$filename	= $orgname;
		$fileExt	= strrchr($filename, ".");	//확장자 추출
		$fileExt	= strtolower($fileExt);
		$filename	= "wz_".abs(ip2long($_SERVER[REMOTE_ADDR])).'_'.md5($filename).$fileExt;
		
		// 이미지 파일여부 확인.
		if (!preg_match("/\.(jp[e]?g|gif|png|bmp)$/i", $fileExt)) {
			$url .= '&errstr=error';
		}
		else {

			// 파일 중복 검사
			while(file_exists($g4['smart_path'] .DIRECTORY_SEPARATOR. $filename))
			{
				$a++;
				$ext = explode(".",$filename);
				$ext[0] = $ext[0].$a;
				$filename = join(".",$ext);
			}

			@move_uploaded_file($tmp_name, $g4['smart_path'] .DIRECTORY_SEPARATOR. $filename);
			
			// 에디터 파일 썸네일처리. (글쓰기 페이지에서 가져온 doc_width 값으로 사이즈 처리)
			$imginfo = @getimagesize($g4['smart_path'] .DIRECTORY_SEPARATOR. $filename);
			if ($imginfo[0] > $doc_width) { 
				$img_width = $doc_width; $img_height = 0;
				$img = $g4['smart_path'] .DIRECTORY_SEPARATOR. $filename;
				$thumbimg = thumbnail($img, $img_width, $img_height, false, 0, THUMB_QUALITY, 1);
			}

			$url .= "&bNewLine=true";
			$url .= "&sFileName=".urlencode(urlencode($filename));
			$url .= "&sFileSize=". $filesize;
			$url .= "&sFileOrgName=".$orgname;
			$url .= "&sFileURL=". $g4['url'] ."/data/file".$smart_path_sub."/".urlencode(urlencode($filename));
		}

	} else { //실패시 errstr=error 전송
		$url .= '&errstr=error';
	}
	header('Location: '. $url);
?>