<?php
include_once('./_common.php');


//5분에 한번씩 최신등록상품 HTML파일 생성
$filelist = array(
	"mainCateItemList",
	"mainGpItemList"
);

//PC
for($i = 0; $i < sizeof($filelist); $i++) {
	$Response = get_httpRequest("http://coinstoday.co.kr/ajax/".$filelist[$i].".php");

	$f = fopen("../data/html/".$filelist[$i].".html","w+");
	fwrite($f, $Response);
	fclose($f);
	echo $filelist[$i].".html 파일 생성 완료<br>\r\n";
}


//MOBILE
for($i = 0; $i < sizeof($filelist); $i++) {
	$Response = get_httpRequest("http://coinstoday.co.kr/ajax/".$filelist[$i].".php?device=mobile");

	$f = fopen("../data/html/".$filelist[$i]."_m.html","w+");
	fwrite($f, $Response);
	fclose($f);
	echo $filelist[$i]."_m.html 파일 생성 완료<br>\r\n";
}


//$ch = curl_init();
//"https://www.moderncoinmart.com/images/D.cache.dpthmbn/62023.jpg";


/* 진행중인 공동구매 상품목록 */
include_once "../data/html/mainGpItemList.html";

/* 카테고리 상품목록 */
include_once "../data/html/mainCateItemList.html";



/* 진행중인 공동구매 상품목록 */
include_once "../data/html/mainGpItemList_m.html";

/* 카테고리 상품목록 */
include_once "../data/html/mainCateItemList_m.html";

?>