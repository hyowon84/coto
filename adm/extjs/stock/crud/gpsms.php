<?php
include_once('./_common.php');
auth_check($auth[$sub_menu], "r");


//스트립슬래시를 안하면 json_decode가 안됨
//$arr = json_decode(str_replace('\"','"',stripslashes( iconv('utf-8', 'cp949', $_POST[] ) )),true);
$mb_id = $member[mb_id];


if (strlen($gpcode_list) > 1) {

	$gpcode = str_replace("\'","'",$_POST[gpcode_list]);
	$sms_text = $_POST[sms_text];
	
	$find_sql = "	SELECT	DISTINCT
												CL.hphone
								FROM		clay_order CL
								WHERE		CL.gpcode IN ($gpcode)
								AND			CL.hphone != ''
								GROUP BY CL.hphone
	";
	$ob = $sqli->query($find_sql);

	while($row = $ob->fetch_array()) {
		if($row[hphone] && strlen($row[hphone]) > 5) $연락처.= str_replace("-","",$row[hphone]).";";
	}

	db_log($find_sql."\r\n$연락처\r\n$sms_text",'ICODE_SMS',"공구 단체SMS/LMS");
	$msg = sendSms($연락처,$sms_text);

}

$json[success] = "true";
$json[message] = $msg;


$json_data = json_encode_unicode($json);
echo $json_data;
?>