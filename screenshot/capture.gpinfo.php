<?php
include_once('./_common.php');

if($gpcode) {
	$조건 = "AND		gpcode = '$gpcode'";
}

$gp_sql = "	SELECT	*
						FROM		gp_info
						WHERE	1=1
						$조건
						AND			start_date <= DATE_FORMAT(now(),'%Y-%m-%d')
						AND			end_date	>= DATE_FORMAT(now(),'%Y-%m-%d')
						AND			stats	= 'START'
						AND			list_view = 'Y'
						ORDER BY reg_date DESC
						LIMIT 3
";
$result = sql_query($gp_sql);

if($mode == 'jhw') echo $gp_sql;


echo "<center>";

while($GP = mysql_fetch_array($result)) {
	
	/* 해당 공구회차의 주문서페이지 캡쳐 */
	$CAPTURE_URL = "http://coinstoday.co.kr/screenshot/orderdetail.php?gpcode=".$GP[gpcode]."&device=mobile";
	$javascript_filename = "./js/order_".$GP[gpcode].".js";
	$screenshot_filename = "./img/order_".$GP[gpcode].".jpg";
	
	// DB 설정 파일 생성
	$file = './'.$javascript_filename;
	
	$f = fopen($file, 'a');
	
	$javascript_contents = "
	// Web Page를 Control 하기 위한 Web Page Module 객체 생성
	var page = require('webpage').create();
	
	// PhantomJS에서 화면을 어떤 사이즈로 출력할 것인지에 대한 값
	// 미디어 쿼리도 동작
	page.viewportSize = { width: 360, height: 800};
	
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
			}, 2000);
		}
	});
	";
		
	fwrite($f,$javascript_contents);
	fclose($f);

	$response = shell_exec('phantomjs '.$javascript_filename);
	echo "<img src='http://coinstoday.co.kr/screenshot/".$response."' />";
	echo "<br><br><br><br>";

	#############################################################################################################
	
	
	/* 해당 공구회차의 공구참여자 캡쳐 */
	$CAPTURE_URL = "http://coinstoday.co.kr/screenshot/buyer.php?gpcode=".$GP[gpcode];
	$javascript_filename = "./js/buyer_".$GP[gpcode].".js";
	$screenshot_filename = "./img/buyer_".$GP[gpcode].".jpg";
	
	
	// DB 설정 파일 생성
	$file = './'.$javascript_filename;
	
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
			}, 2000);
		}
	});
	";
	
	fwrite($f,$javascript_contents);
	fclose($f);

	$response = shell_exec('phantomjs '.$javascript_filename);
	echo "<img src='http://coinstoday.co.kr/screenshot/".$response."' />";
	echo "<br><br><br><br>";
}

echo "</center>";
?>