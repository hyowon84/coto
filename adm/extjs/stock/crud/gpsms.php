<?php
include_once('./_common.php');
auth_check($auth[$sub_menu], "r");


//스트립슬래시를 안하면 json_decode가 안됨
//$arr = json_decode(str_replace('\"','"',stripslashes( iconv('utf-8', 'cp949', $_POST[] ) )),true);
$mb_id = $member[mb_id];


if (strlen($gpcode_list) > 1) {

	$gpcode = str_replace("\'","'",$_POST[gpcode_list]);
	$sms_text = $_POST[sms_text];
	$stats = $_POST[stats];
	
	$find_sql = "	SELECT	DISTINCT
												CL.hphone
								FROM		clay_order CL
								WHERE		CL.gpcode IN ($gpcode)
								AND			CL.hphone != ''
								AND			CL.stats < 20
								GROUP BY CL.hphone
	";
	$ob = $sqli->query($find_sql);

	
	
	if($stats > 0) {
		
		$msg = "스타트";
		$연락처 = str_replace("-","",$ob[hphone]).";";
		
		while($row = $ob->fetch_array()) {
			if($row[hphone] && strlen($row[hphone]) > 5) {
				$연락처 = str_replace("-","",$row[hphone]).";";
				$calc_sql = "	SELECT	T.od_id,
															T.PRD_PRICE,
															CI.delivery_price,
															CI.delivery_invoice,
															(T.PRD_PRICE + CI.delivery_price) AS TOTAL_PRICE
											FROM		(
																SELECT	CL.od_id,
																				SUM(CL.it_org_price * CL.it_qty) AS PRD_PRICE 
																FROM		clay_order CL
																WHERE		1=1
																AND			CL.stats < 20
																AND			CL.hphone = '$연락처'
																GROUP BY	CL.od_id
															) T
															LEFT JOIN clay_order_info CI ON (CI.od_id = T.od_id)
															
				";
				$re = $sqli->query($calc_sql);
				
				while ($od = $re->fetch_array()) {
					$mh_send_message = $sms_text;
					$mh_send_message = preg_replace("/{주문ID}/", $od['od_id'], $mh_send_message);
					$mh_send_message = preg_replace("/{주문금액}/", $od['TOTAL_PRICE'], $mh_send_message);
					$mh_send_message = preg_replace("/{회사명}/", '코인스투데이', $mh_send_message);
					$mh_send_message = preg_replace("/{운송장번호}/", $od['delivery_invoice'], $mh_send_message);
					$내용모음.=$mh_send_message."<br>";
				}

				$msg .= sendSms($연락처,$내용모음);
				db_log($find_sql."\r\n$연락처\r\n$sms_text",'ICODE_SMS',"공구 단체SMS/LMS");
				
			} else {
				$msg = "에러";
			}

			
		}
	}
	else {
		while ($row = $ob->fetch_array()) {
			if ($row[hphone] && strlen($row[hphone]) > 5) $연락처 .= str_replace("-", "", $row[hphone]) . ";";
		}

		db_log($find_sql . "\r\n$연락처\r\n$sms_text", 'ICODE_SMS', "공구 단체SMS/LMS");
		$msg = sendSms($연락처, $sms_text);
	}
	

}

$json[success] = "true";
$json[message] = $msg;


$json_data = json_encode_unicode($json);
echo $json_data;
?>