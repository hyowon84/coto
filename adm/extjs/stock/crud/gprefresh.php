<?php
include_once('./_common.php');
auth_check($auth[$sub_menu], "r");


//스트립슬래시를 안하면 json_decode가 안됨
//$arr = json_decode(str_replace('\"','"',stripslashes( iconv('utf-8', 'cp949', $_POST[] ) )),true);
$mb_id = $member[mb_id];


//상품목록 새로 갱신할 공구코드 목록
$temp = '';

if (strlen($gpcode_list) > 1) {

	$tmp_gpcode_list = explode(",",str_replace("\'","",$gpcode_list));
	
	for($i = 0; $i < count($tmp_gpcode_list); $i++) {
		$gpcode = $tmp_gpcode_list[$i];
	
		if(strlen($gpcode) < 3) break;
		
		//step1. 기존 공구의 상품목록 정보 정리
		$gpinfo_sql = "SELECT	gpcode_name, links	FROM gp_info WHERE gpcode = '$gpcode' ";
		$arr = $sqli->query($gpinfo_sql)->fetch_array();
		$gpinfo = explode(",",str_replace("'","",$arr[links]));
		
		foreach($gpinfo AS $id => $v) {
			$temp[$id] = $v;
		}
		
		
		//step2. 실제 주문들어간 데이터에서 공구의 상품목록 정리*/
		$find_sql = "	SELECT	DISTINCT
													CL.gpcode,
													CL.it_id
									FROM		clay_order CL
									WHERE		CL.gpcode = '$gpcode'
									GROUP BY CL.gpcode, CL.it_id
		";
		$ob = $sqli->query($find_sql);
		
		//상품코드 중복 해결
		while($row = $ob->fetch_array()) {
			$temp[$row[it_id]] = $row[it_id]; 
		}

		
		foreach($temp AS $id => $v) {
			$신규코드_list .= "'".$v."',";
		}

		$신규코드_list = substr($신규코드_list,0,strlen($신규코드_list)-1);
		$upd_sql = "	UPDATE	gp_info	SET
														links = \"$신규코드_list\"
									WHERE		gpcode = '$gpcode'
		";
		$log_upd_sql.= "$upd_sql \r\n\r\n";
		$log_msg .= "$arr[gpcode_name] 공동구매 상품목록은 $신규코드_list 으로 목록설정완료<br><br> ";
	}

//	echo $log_upd_sql;
	
//	db_log($log_upd_sql,'gp_info',"공동구매 상품목록 새로설정");

}

$json[success] = "true";
$json[message] = $log_msg;


$json_data = json_encode_unicode($json);
echo $json_data;
?>