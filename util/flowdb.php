<?php
include_once('./_common.php');
include_once(G5_MSHOP_PATH.'/head.php');


//외환은행
$bankUrl = "http://fx.keb.co.kr/FER1101C.web?schID=fex&mID=FER1101C";
$bankSource = iconv("euc-kr","utf-8",curl($bankUrl));

$temp_unit = explode("<td class='grid_money' title='송금보내실때'>",$bankSource);
$temp_unit2 = explode("<td class='grid_money' title='매매기준율'>",$bankSource);

/* 미국 달러 */
$usd_unit = explode("</td>",$temp_unit[2]);
$usd_unit2 = explode("</td>",$temp_unit2[2]);
$korUnit = $usd_unit[0];
$korUnit2 = $usd_unit2[0];

/* 중국 위안 */
$cny_unit = explode("</td>",$temp_unit[35]);
$cny_unit2 = explode("</td>",$temp_unit2[35]);
$chaUnit = $cny_unit[0];
$chaUnit2 = $cny_unit2[0];

set_session('unit_usa', 1);
set_session('unit_kor', $korUnit);
set_session('unit_cha', $chaUnit);


$USD =  (( $korUnit - $korUnit2 ) * 0.2 + $korUnit2);
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