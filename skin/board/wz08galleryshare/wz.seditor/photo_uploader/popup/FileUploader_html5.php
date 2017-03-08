<?php
	include_once("./_common.php");
	include_once("../../../thumb.lib.php");
	include_once("../../../wetoz.config.php");

	$doc_width = preg_match("/^[0-9]+$/", $doc_width) ? $doc_width : THUMB_CONTENT_WIDTH;

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
	

 	$sFileInfo = '';
	$headers = array(); 
	foreach ($_SERVER as $k => $v){   
		if(substr($k, 0, 9) == "HTTP_FILE"){ 
			$k = substr(strtolower($k), 5); 
			$headers[$k] = $v; 
		} 
	}
	
	$orgname	= $headers['file_name'];
	$filesize	= $headers['file_size'];
	$filename	= $orgname;
	$fileExt	= strrchr($headers['file_name'],".");	//확장자 추출
	$fileExt	= strtolower($fileExt);
	$filename	= "wz_".abs(ip2long($_SERVER[REMOTE_ADDR])).'_'.md5($filename).$fileExt;
	

	if (!preg_match("/\.(jp[e]?g|gif|png|bmp)$/i", $fileExt)) {
		$sFileInfo .= "&errstr=error";
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
		
		$file = new stdClass; 
		$file->name = rawurldecode($filename);	
		$file->orgname = rawurldecode($orgname);	
		$file->size = $filesize;
		$file->content = file_get_contents("php://input"); 
		
		$newPath = $g4['smart_path'].DIRECTORY_SEPARATOR.iconv("utf-8", "cp949", $file->name);
		
		if(file_put_contents($newPath, $file->content)) {

			// 에디터 파일 썸네일처리. (글쓰기 페이지에서 가져온 doc_width 값으로 사이즈 처리)
			$imginfo = @getimagesize($newPath);
			if ($imginfo[0] > $doc_width) { 
				$img_width = $doc_width; $img_height = 0;
				$img = $newPath;
				$thumbimg = thumbnail($img, $img_width, $img_height, false, 0, THUMB_QUALITY, 1);
			}
			
			$sFileInfo .= "&bNewLine=true";
			$sFileInfo .= "&sFileName=".$file->name;
			$sFileInfo .= "&sFileSize=".$file->size;
			$sFileInfo .= "&sFileOrgName=".$file->orgname;
			$sFileInfo .= "&sFileURL=". $g4['url'] ."/data/file".$smart_path_sub."/".$file->name;
		}
	}
	echo $sFileInfo;
 ?>