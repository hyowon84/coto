<?php
include_once('./_common.php');
?>
<html>
<style>
body { width:740px; font-size:12px; font-family:'굴림',Gulim; padding:0px; background-color: #FFFFFF;  }
table { font-size:12px; font-family:'굴림',Gulim; clear: both;  border-collapse: collapse; border-spacing: 0; }
table tr th { font-size:12px; font-family:'굴림',Gulim; background-color: #EEEEEE; border:1px solid #d1dee2; padding:0px; text-align:center; height:25px; }
table tr td {	font-size:12px; font-family:'굴림',Gulim; background-color: #FFFFFF;  padding:0px; border:1px solid #d1dee2; }
</style>

<body topmargin='0' leftmargin='0'>
<?
if(!$date) {
	$date = date("Y-m-d");;
}


//임의값 조회
/*비교할 날짜*/
if(!$cmpdate) $cmpdate = date("Y-m-d",strtotime("-1 day"));
/*
$date = '2016-01-25';
$cmpdate = '2016-01-17';
*/

// $검색날짜 = date("Y년 n월 j일 G시",strtotime($date));
$검색날짜 = date("Y년 n월 j일 대비,",strtotime($cmpdate))." & ".date("Y년 n월 j일의",strtotime($date));
//$검색날짜 = $검색날짜.date(" G시 기준, ");


/* SL_UP_B, SL_DOWN_B, GL_UP_B, GL_DOWN_B */
echo "<h3>$검색날짜 은(SILVER COIN & ETC) 품목 시세 상승 등락률(전일대비) 높은순 $출처</h3>";
flowProductPrice($금속유형 = 'SL', $정렬기준값 = 'min_gap_per', $등락유형 = 'UP', $정렬유형 = 'BIG', $출력갯수 = 50, $date, $cmpdate);
echo "<br>";
echo "<h3>$검색날짜 금(GOLD COIN & ETC) 품목 시세 상승 등락률(전일대비) 높은순 $출처</h3>";
flowProductPrice($금속유형 = 'GL', $정렬기준값 = 'min_gap_per', $등락유형 = 'UP', $정렬유형 = 'BIG', $출력갯수 = 50, $date, $cmpdate);
echo "<br>";


echo "<h3>$검색날짜 은(SILVER COIN & ETC) 품목 시세 하락 등락률(전일대비) 높은순 $출처</h3>";
flowProductPrice($금속유형 = 'SL', $정렬기준값 = 'min_gap_per', $등락유형 = 'DOWN', $정렬유형 = 'BIG', $출력갯수 = 50, $date, $cmpdate);
echo "<br>";
echo "<h3>$검색날짜 금(GOLD COIN & ETC) 품목 시세 하락 등락률(전일대비) 높은순 $출처</h3>";
flowProductPrice($금속유형 = 'GL', $정렬기준값 = 'min_gap_per', $등락유형 = 'DOWN', $정렬유형 = 'BIG', $출력갯수 = 50, $date, $cmpdate);
echo "<br>";


?>
</body>
</html>