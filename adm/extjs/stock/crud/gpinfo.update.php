<?php
include_once('./_common.php');
auth_check($auth[$sub_menu], "r");


//스트립슬래시를 안하면 json_decode가 안됨
//$arr = json_decode(str_replace('\"','"',stripslashes( iconv('utf-8', 'cp949', $_POST[] ) )),true);
$mb_id = $member[mb_id];


if($mode == 'memo') {
	/* 단일레코드일때 */
	if (strlen($gpcode) > 1) {

		$gpcode = $_POST[gpcode];
		$invoice_memo = $_POST[invoice_memo];
		$memo = $_POST[memo];
		$reg_date = $_POST[reg_date];

		/* 공동구매 수정 */
		$common_sql = "	UPDATE	gp_info	SET
															invoice_memo = '$invoice_memo',															
															memo = '$memo'
										WHERE		gpcode = '$gpcode'
		";
		$result = sql_query($common_sql);

	}
}
else if($mode == 'grid') {
	/*
 * JSON DECODE 관련 이슈
 * 1. 넘겨받은 JSON텍스트 ICONV로 변환필요 
 * 2. 상품명에 " OR ' 가 포함 되있는경우 디코딩 실패 str_replace로 변환필요
 * 3. STRIPSLASH 안하면 디코딩이 안됨
 * */
	$arr = jsonDecode($_POST['data']);

	$gpcode = $arr[gpcode];
	$stats = $arr[stats];

	/* 상품정보 수정 */
	$common_sql = "	UPDATE	gp_info		SET
														stats = '$stats'
									WHERE		gpcode = '$gpcode'
	";
	sql_query($common_sql);

	
	/*주문마감시 공구코드와 관련 공구상품들 공구재고값 초기화 */
	if($stats == '05') {

		$sql = "SELECT	GI.links
						FROM		gp_info GI
						WHERE		GI.gpcode = '$gpcode' ";
		$row = sql_fetch($sql);

		if(strstr($row[links],"'")){
			$links = $row[links];
		}
		else {
			$links = explodeMakeCode(",",$row[links]);	
		}
		
			
		$upd_sql = "UPDATE	g5_shop_group_purchase GP	SET
													GP.gp_jaego = 0
								WHERE		GP.gp_id IN ($links)
		";
		sql_query($upd_sql);

	}
	
}

if($result) {
	$json[success] = "true";
	$json[message] = '수정되었습니다';
} else {
	$json[success] = "false";
	$json[message] = '수정되지 않았습니다. 관리자에게 문의바랍니다.';
}

$json_data = json_encode_unicode($json);
echo $json_data;
?>