<?php
include_once('./_common.php');
include_once(G5_MSHOP_PATH.'/head.php');


//네이버 환율정보 매매기준과 송금보낼때 차이는 11.20 정도 차이  송금환율 = 매매기준환율 + 11.20
//$bankUrl = "http://m.stock.naver.com/marketindex/index.nhn?menu=exchange#exchange";
$USD_URL = "http://m.stock.naver.com/api/json/marketindex/marketIndexDay.nhn?marketIndexCd=FX_USDKRW&pageSize=20&page=1";	//미국환율 JSON 데이터
$CNY_URL = "http://m.stock.naver.com/api/json/marketindex/marketIndexDay.nhn?marketIndexCd=FX_CNYKRW&pageSize=20&page=1"; //중국

$USD_JSON = json_decode(get_httpRequest($USD_URL));
$CNY_JSON = json_decode(get_httpRequest($CNY_URL));


$USD송금기준 = $USD_JSON->result->marketIndexDay[0]->sv;
$USD매매기준 = $USD_JSON->result->marketIndexDay[0]->nv;

$CNY송금기준 = $CNY_JSON->result->marketIndexDay[0]->sv;
$CNY매매기준 = $CNY_JSON->result->marketIndexDay[0]->nv;


/* 미국 달러 */
$korUnit = $USD송금기준;	//송금
$korUnit2 = $USD매매기준;	//매매

/* 중국 위안 */
$chaUnit = $CNY송금기준;
$chaUnit2 = $CNY매매기준;


set_session('unit_usa', 1);
set_session('unit_kor', $korUnit);
set_session('unit_cha', $chaUnit);

/*쿼리 계산시 사용하는 미국 환율 1145.12 */
$USD =  (( $korUnit - $korUnit2 ) * 0.2 + $korUnit2);

/*쿼리 계산시 사용하는 중국 환율 165.93 */
$CNY =  (( $chaUnit - $chaUnit2 ) * 0.2 + $chaUnit2);


// 금,은,백금,팔라듐 시세 내역
$flowdataSource = get_httpRequest("http://www.kitco.com/market/");
$tmpFlowData = explodeCut($flowdataSource,'<table class="world_spot_price">','</table>');


//업데이트 시간 긁기 =>  04/22/2016 16:59
preg_match_all("/<td id\=\"wsp-[A-Z]+-date\" class=\"date\">([0-9a-z\/\s\:\.]+)<\/td>/i",$tmpFlowData,$match_date);
preg_match_all("/<td id\=\"wsp-[A-Z]+-time\" class=\"time\">([0-9a-z\/\s\:\.]+)<\/td>/i",$tmpFlowData,$match_time);

$SpotTime = $match_date[1][0]." ".$match_time[1][0].":00";
$timestamp = date("Y-m-d",strtotime($SpotTime));
$reg_date = date("Y-m-d H:i:s",strtotime($SpotTime));


/* ASK금액 입력 */
preg_match_all("/<td id\=\"wsp-[A-Z]+-ask\">([0-9a-z\/\s\:\.]+)<\/td>/i",$tmpFlowData,$match_ask);
preg_match_all("/<td id\=\"wsp-[A-Z]+-bid\">([0-9a-z\/\s\:\.\-]+)<\/td>/i",$tmpFlowData,$match_bid);


//금,은,백금,팔라듐 변동폭
preg_match_all("/<td id\=\"wsp-[A-Z]+-change\" class\=\"change\"><p class\=[\"]*[a-zA-Z0-9]*[\"]*>[\+\-]*([0-9a-z\/\s\:\.\-]+)<\/p><\/td>/i",$tmpFlowData,$match_ch);



$GL = str_replace(',','',$match_ask[1][0]);
$GL_change = $match_ch[1][0];
$GL_arrow = (GL_change > 0) ? 'up' : 'down';

$SL = str_replace(',','',$match_ask[1][1]);
$SL_change = $match_ch[1][1];
$SL_arrow = ($SL_change > 0) ? 'up' : 'down';

$PT = str_replace(',','',$match_ask[1][2]);
$PT_change = $match_ch[1][2];
$PT_arrow = ($PT_change > 0) ? 'up' : 'down';

$PD = str_replace(',','',$match_ask[1][3]);
$PD_change = $match_ch[1][3];
$PD_arrow = ($PD_change > 0) ? 'up' : 'down';

$chk_sql = "	SELECT	COUNT(*) AS CNT
							FROM		flow_price
							WHERE		ymd				=	'$timestamp'
							AND			reg_date	= '$reg_date'
";
$row = mysql_fetch_array(sql_query($chk_sql));

if($row[CNT] == 0) {
	$ins_sql = "	INSERT INTO		flow_price		SET
																ymd				=	'$timestamp',
																reg_date	= '$reg_date',	/*기준 날짜*/
																USD = '$USD',						/*미국달러환율*/
																USD2 = '$korUnit',
																USD_T = '$korUnit2',
																CNY = '$CNY',						/*중국위안환율*/
																CNY2 = '$chaUnit',
																CNY_T = '$chaUnit2',
																GL = '$GL',							/*금 시세*/
																SL = '$SL',							/*은 시세*/
																PT = '$PT',							/*백금 시세*/
																PD = '$PD',							/*팔라듐 시세*/
																GL_change = '$GL_change',
																SL_change = '$SL_change',
																PT_change = '$PT_change',
																PD_change = '$PD_change',
																GL_arrow = '$GL_arrow',
																SL_arrow = '$SL_arrow',
																PT_arrow = '$PT_arrow',
																PD_arrow = '$PD_arrow'
	";
	sql_query($ins_sql);
	echo $ins_sql;
}

?>
달러상품 계산기<br>

<font style='font-size:14pt; font-weight:bold;'>
달러 $
<input type='text' id='dollar' name='dollar' onchange='calculate(this.value)'> * $환율(<?=$_SESSION[unit_kor_duty]?>원) * 수수료(1.06) * 부가세(1.1)
<br><br><br>
결과 <span id='result' style=''></span>원
</font>

<script>
var kor_duty = '<?=$_SESSION[unit_kor_duty]?>';

function calculate(dollar) {
	var result;

	result = dollar * kor_duty * 1.06 * 1.1;
	result = Math.floor(result);
	$('#result').html(number_fomrat(result));
}

function number_fomrat(str){
    str = str + "";
    if(str == "" || /[^0-9,]/.test(str)) return str;
    str = str.replace(/,/g, "");
    for(var i=0; i<parseInt(str.length/3, 10); i++){
        str = str.replace(/([0-9])([0-9]{3})(,|$)/, "$1,$2$3");
    }
    return str;
}
</script>