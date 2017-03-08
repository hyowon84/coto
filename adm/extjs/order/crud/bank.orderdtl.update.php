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
	$it_qty = $arr[it_qty];	/*주문수량*/
	$it_org_price = $arr[it_org_price];	/*주문당시 개당 상품가격*/
	$stats = $arr[stats];		/*상태 ( 취소:99, 신청:00, )*/
	

	include G5_INC_PATH."/logWriteOrder.php";

	/* 상품정보 수정 */
	$common_sql = "	UPDATE	clay_order	SET
														stats = '$stats'		/*상태 ( 취소:99, 신청:00, )*/
									WHERE		gpcode	= '$gpcode'		/*연결된 공구코드*/
									AND			od_id		= '$od_id'		/*주문ID*/
									AND			number = '$number'
	";
	sql_query($common_sql);
	db_log($common_sql,'clay_order','주문신청관리');
}
else {	/* 복수레코드일때 */

	for($i = 0; $i < count($arr); $i++) {

		$grid = $arr[$i];
		$number = $grid[number];
		$gpcode = $grid[gpcode];
		$od_id = $grid[od_id];
		$it_qty = $grid[it_qty];	/*주문수량*/
		$it_org_price = $grid[it_org_price];	/*주문당시 개당 상품가격*/
		$stats = $grid[stats];		/*상태 ( 취소:99, 신청:00, )*/


		include G5_INC_PATH."/logWriteOrder.php";
		
		/* 상품정보 수정 */
		$common_sql = "	UPDATE	clay_order	SET
															stats = '$stats'		/*상태 ( 취소:99, 신청:00, )*/
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
	$json[message] = '개별주문정보가 수정되었습니다';
} else {
	$json[success] = "false";
	$json[message] = '개별주문정보가 수정되지 않았습니다. 관리자에게 문의바랍니다.';
}

$json_data = json_encode_unicode($json);
echo $json_data;
?>