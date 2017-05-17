<?php
include_once('./_common.php');


auth_check($auth[$sub_menu], "r");
$mb_id = $member[mb_id];

/*
 * JSON DECODE 관련 이슈
 * 1. 상품명에 " OR ' 가 포함 되있는경우 디코딩 실패 str_replace로 변환필요
 * 2. 넘겨받은 JSON텍스트 ICONV로 변환필요
 * 3. STRIPSLASH
 * */
$arr = jsonDecode($_POST['data']);


if( strlen($arr[number]) > 3 ) {

	$number = $arr[number];
	$gpcode = $arr[gpcode];
	$od_id = $arr[od_id];
	$it_memo = $arr[it_memo];	/* 품목별 메모 */
	

	/* 상품정보 수정 */
	$common_sql = "	UPDATE	clay_order	SET
														it_memo = '$it_memo'	/*품목별 메모*/
									WHERE		gpcode	= '$gpcode'		/*연결된 공구코드*/
									AND			od_id		= '$od_id'		/*주문ID*/
									AND			number = '$number'
	";
	sql_query($common_sql);
	db_log($common_sql,'clay_order','품목별 메모');
}
else {	/* 복수레코드일때 */

	for($i = 0; $i < count($arr); $i++) {

		$grid = $arr[$i];
		
		$number = $grid[number];
		$gpcode = $grid[gpcode];
		$od_id = $grid[od_id];
		$it_memo = $grid[it_memo];	/* 품목별 메모 */

		
		/* 상품정보 수정 */
		$common_sql = "	UPDATE	clay_order	SET
															it_memo = '$it_memo'	/*품목별 메모*/
										WHERE		gpcode	= '$gpcode'		/*연결된 공구코드*/
										AND			od_id		= '$od_id'		/*주문ID*/
										AND			number = '$number'
		";
		sql_query($common_sql);
		db_log($common_sql,'clay_order','주문신청관리');
	}
}

//echo $common_sql;


if($result) {
	$json[success] = "true";
	$json[message] = '품목별 메모가 수정되었습니다';
} else {
	$json[success] = "false";
	$json[message] = '품목별 메모가 수정되지 않았습니다. 관리자에게 문의바랍니다.';
}

$json_data = json_encode_unicode($json);
echo $json_data;
?>