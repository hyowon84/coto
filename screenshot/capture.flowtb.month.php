<?php
include_once('./_common.php');

$modelist = Array('SL_UP_B','GL_UP_B','SL_DOWN_B','GL_DOWN_B');

if(!$date) {
	$date = date("Y-m-d");
}
/* 보고싶은 날짜 */
$ymd = date("Ymd",strtotime($date));

/*비교할 날짜*/
if(!$cmpdate) $cmpdate = date("Y-m-d",strtotime("-1 month"));

for($i = 0; $i < count($modelist); $i++) {
	$mode = $modelist[$i];
	
	$CAPTURE_URL = "http://coinstoday.co.kr/chart/flow_tb.php?mode=".$mode."&date=".$date."&cmpdate=".$cmpdate;
	$javascript_filename = "./js/".$ymd."_flowtb_month_".$mode.".js";
	$screenshot_filename = "./img/".$ymd."_flowtb_month_".$mode.".jpg";
	
	// DB 설정 파일 생성
	$file = $javascript_filename;
	$f = fopen($file, 'a');
	
	$javascript_contents = "
	// Web Page를 Control 하기 위한 Web Page Module 객체 생성
	var page = require('webpage').create();
	
	
	
	// PhantomJS에서 화면을 어떤 사이즈로 출력할 것인지에 대한 값
	// 미디어 쿼리도 동작
	page.viewportSize = {
	width: 1000 };
	
	
	// 페이지 오픈
	page.open('$CAPTURE_URL', function(status) {
	
		// status 인자를 통해 성공, 실패여부 확인
		if (status !== 'success') {
			console.log('Cannot open site');
			phantom.exit();
	
		} else {
			// 페이지가 렌더링 되는 시간(2초) 기다린 후
			window.setTimeout(function () {
				console.log('$screenshot_filename');
				page.render('$screenshot_filename');    // 스크린 캡쳐파일 생성
				phantom.exit();
			}, 30000);
		}
	});
	";
	
	fwrite($f,$javascript_contents);
	fclose($f);
	
	$response = shell_exec('phantomjs '.$javascript_filename);
	echo "<img src='http://coinstoday.co.kr/screenshot/".$response."' />";
	echo "<br><br><br><br>";
}
?>