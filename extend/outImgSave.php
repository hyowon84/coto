<?php

//외부이미지 서버에 임시저장 
function remote_image($urlstr){
	$url = parse_url($urlstr);


	$domain = str_replace("www.","",$url[host]);
	$ext = substr($urlstr,strrpos($urlstr,".") + 1);

	$string = "";

	$res = fsockopen("$domain", 80, $strErrorNo, $strErrStr, 2);

	if($res){


		$headerstr = "GET $urlstr HTTP/1.1\r\n";
		$headerstr.= "Host:$domain:80\r\n";
		$headerstr.= "\r\n";

		fputs($res, $headerstr);

		while (!feof($res)){

			$string.= fgets($res, 1024);
		}
		fclose($res);
	}

	if($ext == "gif")
		return substr($string,strpos($string,"GIF89a"));
	elseif($ext == "jpg" || $ext == "jpeg")
		return substr($string,strpos($string,"image/jpeg") + 14);
}

//서버에 임시저장된 파일, 원하는 경로에 저장 
function image_save($file,$string){
	$fp = fopen ($file,'w');
	if(!$fp) echo $file;

	fwrite ($fp,$string);
	fclose ($fp);
}


//이미지 정보 리턴
function get_image_resource_from_file ($path_file){

	if (!is_file($path_file)) {//파일이 아니라면

		$GLOBALS['errormsg'] = $path_file . '은 파일이 아닙니다.';

		return Array();
	}

	$size = @getimagesize($path_file);
	if (empty($size[2])) {//이미지 타입이 없다면

		$GLOBALS['errormsg'] = $path_file . '은 이미지 파일이 아닙니다.';

		return Array();
	}

	if ($size[2] != 1 && $size[2] != 2 && $size[2] != 3) {//지원하는 이미지 타입이 아니라면

		$GLOBALS['errormsg'] = $path_file . '은 gif 나 jpg, png 파일이 아닙니다.';

		return Array();
	}

	switch($size[2]){

		case 1 : //gif

			$im = @imagecreatefromgif($path_file);

			break;

		case 2 : //jpg

			$im = @imagecreatefromjpeg($path_file);

			break;

		case 3 : //png

			$im = @imagecreatefrompng($path_file);

			break;
	}

	if ($im === false) {//이미지 리소스를 가져오기에 실패하였다면

		$GLOBALS['errormsg'] = $path_file . ' 에서 이미지 리소스를 가져오는 것에 실패하였습니다.';

		return Array();
	}
	else {//이미지 리소스를 가져오기에 성공하였다면

		$return = $size;
		$return[0] = $im;
		$return[1] = $size[0];//너비
		$return[2] = $size[1];//높이
		$return[3] = $size[2];//이미지타입
		$return[4] = $size[3];//이미지 attr

		return $return;
	}
}




/*
	Function	: getThumb($param)
	Param		: $param['o_path'] = 원본 파일 경로 (LOCAL PATH or URL)
				  $param['n_path'] = 새 파일 경로 (LOCAL PATH)
				  $param['width'] = 썸네일 이미지 넓이
				  $param['height'] = 썸네일 이미지 높이
				  $param['mode'] = ratio or fixed	(ratio => 비율유지, fixed => 파라메터의 크기로 강제 변경)
				  $param['fill_yn'] = 'Y' or 'N'	(mode가 ratio일 경우 부족한부분 투명 배경처리)
				  $param['preview_yn'] = 'Y' or 'N' (미리보기 방지 여부 => 미리보기방지 대체 이미지 제공)
	Return		: array('bool' => true or false, 'src' => 썸네일 이미지 url, 'msg' => 성공, 실패 메세지)
*/
function getThumb($param,$width = null,$height = null){
	global $접속기기, $DOCUMENT_ROOT;
	preg_match_all("/(jpg|gif|jpeg|png|JPG|PNG|GIF)/",$param[gp_img],$last);
	$lastname = $last[0][0];
	
	//apmex, mcm 예외처리
	if( strstr($param[gp_img], 'apmex') || strstr($param[gp_img], 'moderncoinmart') || strstr($param[gp_img], 'jmbullion') ) {
//		$file = remote_image($param[gp_img]);
//		$out_img = "$DOCUMENT_ROOT/data/outsrc/".$param[gp_id].".".$lastname;
//		image_save($file, $apmex_img);
//		$param['o_path'] = $out_img;
		return array('bool' => true, 'src' => $param[gp_img]);
	}
	else {
		$param['o_path'] = $param[gp_img];
	}
	
	$param['n_path'] = "$DOCUMENT_ROOT/data/outsrc/thumb/".$param[gp_id].(($접속기기 != '데스크탑') ? "_m" : "").".".$lastname;
	$imgsrc = "/data/outsrc/thumb/".$param[gp_id].(($접속기기 != '데스크탑') ? "_m" : "").".".$lastname;
	
	if($width) {
		$param['width'] = $width;
	} else {		
		$param['width'] = ($접속기기 != '데스크탑') ? 120 : 250;
	}
	
	if($height) {
		$param['height'] = $height;
	}
	else {
		$param['height'] = ($접속기기 != '데스크탑') ? 80 : 230;
	}
	
	$param['mode'] = 'ratio';
	$param['fill_yn'] = 'Y';
	$param['preview_yn'] = 'Y';

	if(empty($param['o_path']))		return array('bool' => false, 'msg' => '원본 파일 경로가 없습니다.');
	if(empty($param['n_path']))		return array('bool' => false, 'msg' => '원본 파일 경로가 없습니다.');

	if(!in_array($param['mode'], array('ratio', 'fixed')))	$param['mode'] = 'ratio';
	if(empty($param['width']))		$param['width'] = 300;
	if(empty($param['height']))		$param['height'] = 300;
	if(!in_array($param['fill_yn'], array('Y', 'N')))		$param['fill_yn'] = 'N';
	if(!in_array($param['preview_yn'], array('Y', 'N')))	$param['preview_yn'] = 'Y';

	$tmp = array();
	$src = array();
	$dst = array();

	// 썸네일 이미지 갱신 기간 (1주일)
	if(file_exists($param['n_path'])){
		if(mktime() - filemtime($param['n_path']) < 60 * 60 * 24 * 7)	return array('bool' => true, 'src' => $imgsrc);
	}

	// 미리보기 방지 대체 이미지
	if($param['preview_yn'] == 'N'){
		$param['o_path'] = './hidden.png';	// 미리보기 방지 url
	}
	else{
		// 외부서버 이미지
		if(filter_var($param['o_path'], FILTER_VALIDATE_URL)){
			$tmp['dir'] = $DOCUMENT_ROOT.'/data/outsrc/';	// curl 임시 다운로드 경로

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $param['o_path']);

			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			if($http_code == 200) {
				return array('bool' => false, 'msg' => '이미지 파일이 아닙니다.'); 
			}

			curl_setopt($ch, CURLOPT_NOBODY, true);
//			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 인증서 체크같은데 true 시 안되는 경우가 많다.
//			curl_setopt($ch, CURLOPT_POST, false); // Post Get 접속 여부
//			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//			curl_setopt($ch, CURLOPT_TIMEOUT, 60); // TimeOut 값
//			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); //http 응답코드가 302일때 redirect_url 로 따라감
//			curl_setopt($ch, CURLOPT_MAXREDIRS, 5); //if http server gives redirection responce
//			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 결과값을 받을것인지 
			curl_exec($ch);
			$ch_info = curl_getinfo($ch);
			curl_close($ch);

			if($ch_info['http_code'] != 200)	return array('bool' => false, 'msg' => '원본파일을 가져올수 없습니다.');
			if(array_shift(explode('/', $ch_info['content_type'])) != 'image') return array('bool' => false, 'msg' => '이미지 파일이 아닙니다.');
			$ext = str_replace(array('jpeg'), array('jpg'), array_pop(explode('/', $ch_info['content_type'])));

			$tmp['name'] = 'thumb_tmp_'.date('YmdHisu').'.'.$ext;	// curl 임시 다운로드 파일명
			$tmp['path'] = $tmp['dir'].$tmp['name'];	// curl 임시파일 전체경로

			// curl 임시파일 다운로드
			$fp = fopen($tmp['path'], 'w');
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $param['o_path']);
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_exec($ch);
			curl_close($ch);
			fclose($fp);

			$param['o_path'] = $tmp['path'];
		}
	}

	$src['path'] = $param['o_path'];
	$dst['path'] = $param['n_path'];


	$imginfo = getimagesize($src['path']);
	$src['mime'] = $imginfo['mime'];

	// 원본 이미지 리소스 호출
	switch($src['mime']){
		case 'image/jpeg' :	$src['img'] = imagecreatefromjpeg($src['path']);	break;
		case 'image/gif' :	$src['img'] = imagecreatefromgif($src['path']);		break;
		case 'image/png' :	$src['img'] = imagecreatefrompng($src['path']);		break;
		case 'image/bmp' :	$src['img'] = imagecreatefrombmp($src['path']);		break;
		// mime 타입이 해당되지 않으면 return false
		default :		return array('bool' => false, 'msg' => '이미지 파일이 아닙니다.');						break;
	}

	// 원본 이미지 크기 / 좌표 초기값
	$src['w'] = $imginfo[0];
	$src['h'] = $imginfo[1];
	$src['x'] = 0;
	$src['y'] = 0;

	// 썸네일 이미지 좌표 초기값 설정
	$dst['x'] = 0;
	$dst['y'] = 0;

	// 썸네일 이미지 가로, 세로 비율 계산
	$dst['ratio']['w'] = $src['w'] / $param['width'];
	$dst['ratio']['h'] = $src['h'] / $param['height'];

	switch($param['mode']){
		case 'ratio' :
			// 썸네일 이미지의 비율계산 (가로 == 세로)
			if($dst['ratio']['w'] == $dst['ratio']['h']){
				$dst['w'] = $param['width'];
				$dst['h'] = $param['height'];
			}
			// 썸네일 이미지의 비율계산 (가로 > 세로)
			elseif($dst['ratio']['w'] > $dst['ratio']['h']){
				$dst['w'] = $param['width'];
				$dst['h'] = round(($param['width'] * $src['h']) / $src['w']);
			}
			// 썸네일 이미지의 비율계산 (가로 < 세로)
			elseif($dst['ratio']['w'] < $dst['ratio']['h']){
				$dst['w'] = round(($param['height'] * $src['w']) / $src['h']);
				$dst['h'] = $param['height'];
			}

			if($param['fill_yn'] == 'Y'){
				$dst['canvas']['w'] = $param['width'];
				$dst['canvas']['h'] = $param['height'];
				$dst['x'] = $param['width'] > $dst['w'] ? ($param['width'] - $dst['w']) / 2 : 0;
				$dst['y'] = $param['height'] > $dst['h'] ? ($param['height'] - $dst['h']) / 2 : 0;
			}
			else{
				$dst['canvas']['w'] = $dst['w'];
				$dst['canvas']['h'] = $dst['h'];
			}
			break;
		case 'fixed' :
			// 썸네일 이미지의 비율계산 (가로 == 세로)
			if($dst['ratio']['w'] == $dst['ratio']['h']){
				$dst['w'] = $param['width'];
				$dst['h'] = $param['height'];
			}
			// 썸네일 이미지의 비율계산 (가로 > 세로)
			elseif($dst['ratio']['w'] > $dst['ratio']['h']){
				$dst['w'] = $src['w'] / $dst['ratio']['h'];
				$dst['h'] = $param['height'];

				$src['x'] = ($dst['w'] - $param['width']) / 2;
			}
			// 썸네일 이미지의 비율계산 (가로 < 세로)
			elseif($dst['ratio']['w'] < $dst['ratio']['h']){
				$dst['w'] = $param['width'];
				$dst['h'] = $src['h'] / $dst['ratio']['w'];

				$dst['y'] = 0;
			}
			$dst['canvas']['w'] = $param['width'];
			$dst['canvas']['h'] = $param['height'];
			break;
	}

	// 썸네일 이미지 리소스 생성
	$dst['img'] = imagecreatetruecolor($dst['canvas']['w'], $dst['canvas']['h']);

	// 배경색 처리
	if(in_array($src['mime'], array('image/png', 'image/gif'))){
		// 배경 투명 처리
		imagetruecolortopalette($dst['img'], false, 255);
		$bgcolor = imagecolorallocatealpha($dst['img'], 255, 255, 255, 127);
		imagefilledrectangle($dst['img'], 0, 0, $dst['canvas']['w'],$dst['canvas']['h'], $bgcolor);
	}
	else{
		// 배경 흰색 처리
		$bgclear = imagecolorallocate($dst['img'],255,255,255);
		imagefill($dst['img'],0,0,$bgclear);
	}

	// 원본 이미지 썸네일 이미지 크기에 맞게 복사
	imagecopyresampled($dst['img'] ,$src['img'] ,$dst['x'] ,$dst['y'] ,$src['x'] ,$src['y'] ,$dst['w'] ,$dst['h'] ,$src['w'] ,$src['h']);

	// imagecopyresampled 함수 사용 불가시 사용
	//imagecopyresized($dst['img'] ,$src['img'] ,$dst['x'] ,$dst['y'] ,$src['x'] ,$src['y'] ,$dst['w'] ,$dst['h'] ,$src['w'] ,$src['h']);

	ImageInterlace($dst['img']);

	// 썸네일 이미지 리소스를 기반으로 실제 이미지 생성
	switch($src['mime']){
		case 'image/jpeg' :	imagejpeg($dst['img'], $dst['path']);	break;
		case 'image/gif' :	imagegif($dst['img'], $dst['path']);	break;
		case 'image/png' :	imagepng($dst['img'], $dst['path']);	break;
		case 'image/bmp' :	imagebmp($dst['img'], $dst['path']);	break;
	}

	// 원본 이미지 리소스 종료
	imagedestroy($src['img']);
	// 썸네일 이미지 리소스 종료
	imagedestroy($dst['img']);
	// curl 임시파일 삭제
	if(file_exists($tmp['path'])) unlink($tmp['path']);

	// 썸네일 파일경로 존재 여부 확인후 리턴
	return file_exists($dst['path']) ? array('bool' => true, 'src' => $imgsrc) : array('bool' => false, 'msg' => '파일 생성에 실패하였습니다.');
}
?>