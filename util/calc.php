<?php

if($_SERVER["REMOTE_ADDR"] != "221.146.206.90") {
	echo "접근불가";	
	exit;
}

include_once('./_common.php');
include_once(G5_MSHOP_PATH.'/head.php');


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