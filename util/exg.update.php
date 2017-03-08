<?php
include_once('./_common.php');


//외환은행
$exgoldUrl = "http://exgold.co.kr/";

$exgoldSource = iconv('euc-kr','utf-8',curl($exgoldUrl));


$금_소스 = explodeCut($exgoldSource,'<!-- 국내 도매시세 (금) -->','<!-- 국내 도매시세 (금) -->');
$은_소스 = explodeCut($exgoldSource,'<!-- 국내 도매시세 (은) -->','<!-- 국내 도매시세 (은) -->');
$백금_소스 = explodeCut($exgoldSource,'<!-- 국내 도매시세 (백금) -->','<!-- 국내 도매시세 (백금) -->');
$팔라듐_소스 = explodeCut($exgoldSource,'<!-- 국내 도매시세 (파라듐) -->','<!-- 국내 도매시세 (파라듐) -->');



preg_match_all('/<strong>([0-9\,]+)<\/strong>/',$금_소스,$match_gold);
$GL = str_replace(',','',$match_gold[1][1]);

preg_match_all('/<strong>([0-9\,]+)<\/strong>/',$은_소스,$match_gold);
$SL = str_replace(',','',$match_gold[1][1]);

preg_match_all('/<strong>([0-9\,]+)<\/strong>/',$백금_소스,$match_gold);
$PT = str_replace(',','',$match_gold[1][1]);

preg_match_all('/<strong>([0-9\,]+)<\/strong>/',$팔라듐_소스,$match_gold);
$PD = str_replace(',','',$match_gold[1][1]);

$ymd = date("Y-m-d");
$reg_date = date("Y-m-d H:i:s");

$ins_sql = "	INSERT INTO		flow_price_exg	SET
											ymd				=	'$ymd',
											reg_date	= '$reg_date',	/*기준 날짜*/
											GL = '$GL',							/*금 시세*/
											SL = '$SL',							/*은 시세*/
											PT = '$PT',							/*백금 시세*/
											PD = '$PD'							/*팔라듐 시세*/
";
sql_query($ins_sql);
?>