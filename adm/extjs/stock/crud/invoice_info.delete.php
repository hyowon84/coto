<?php
include_once('./_common.php');
auth_check($auth[$sub_menu], "r");
$mb_id = $member[mb_id];

/*
 * JSON DECODE 관련 이슈
 * 1. 넘겨받은 JSON텍스트 ICONV로 변환필요 
 * 2. 상품명에 " OR ' 가 포함 되있는경우 디코딩 실패 str_replace로 변환필요
 * 3. STRIPSLASH 안하면 디코딩이 안됨
 * */
$arr = jsonDecode($_POST['data']);


/* 단일레코드일때 */
if( strlen($arr[iv_id]) > 3 ) {
	$gpcode = $arr[gpcode];
	$iv_id = $arr[iv_id];

	/* 상품정보 수정 */
	$common_sql = "	DELETE FROM	invoice_item
									WHERE		iv_id = '$iv_id'
	";
	sql_query($common_sql);
	db_log($common_sql,'invoice_info','발주서 품목들 삭제');
	
	$common_sql = "	DELETE FROM	invoice_info
									WHERE		gpcode = '$gpcode'
									AND			iv_id = '$iv_id'
	";
	sql_query($common_sql);
	db_log($common_sql,'invoice_info','발주서 정보 삭제');
}
else {	/* 복수레코드일때 */

	for($i = 0; $i < count($arr); $i++) {
		$grid = $arr[$i];

		$gpcode = $grid[gpcode];
		$iv_id = $grid[iv_id];


		/* 상품정보 수정 */
		$common_sql = "	DELETE FROM	invoice_item
										WHERE		iv_id = '$iv_id'
		";
		sql_query($common_sql);
		db_log($common_sql,'invoice_info','발주서 품목들 삭제');
		
		/* 상품정보 수정 */
		$common_sql = "	DELETE FROM	invoice_info
										WHERE		gpcode = '$gpcode'
										AND			iv_id = '$iv_id'
		";
		sql_query($common_sql);
		db_log($common_sql,'invoice_info','발주서 정보 삭제');
	}
}


if($result) {
	$json[success] = "true";
	$json[message] = '삭제 되었습니다';
} else {
	$json[success] = "false";
	$json[message] = '삭제되지 않았습니다. 관리자에게 문의바랍니다.';
}

$json_data = json_encode_unicode($json);
echo $json_data;
?>