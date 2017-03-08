<?php
include_once('./_common.php');
//auth_check($auth[$sub_menu], "r");


//스트립슬래시를 안하면 json_decode가 안됨
$arr = json_decode(str_replace('\"','"',stripslashes( iconv('utf-8', 'cp949', $_POST['data'] ) )),true);
$mb_id = $member[mb_id];


/* 1g 기준 가격업데이트 */
if($mode == 'allUpdate') {

	$common_sql = "UPDATE	flowprice_data FD		SET
												FD.buy_price = (	SELECT	B.buy_price
																					FROM		(	SELECT	*
																										FROM		flowprice_data
																									) B
																					WHERE		B.gr_id = FD.gr_id
																					AND			B.metal_type = FD.metal_type
																					AND			B.weight = 1.00
																			 ) * FD.weight,
												FD.sell_price = (	SELECT	S.sell_price
																					FROM		(	SELECT	*
																										FROM		flowprice_data
																									) S
																					WHERE		S.gr_id = FD.gr_id
																					AND			S.metal_type = FD.metal_type
																					AND			S.weight = 1.00
																			 ) * FD.weight
							WHERE		FD.gr_id IN (SELECT	MAX(A.gr_id) FROM (SELECT	*	FROM	flowprice_data) A )
	";
	$result = sql_query($common_sql);

}
/* 단일레코드일때 */
else if( strlen($arr[id]) > 3 || strlen($arr[number]) > 0 ) {
	$ex_id = $arr[id];
	$number = $arr[number];
	$fp_id = $arr[fp_id];
	$sell_price = $arr[sell_price];
	$buy_price = $arr[buy_price];

	/* 시세항목 설정값 입력 */
	$common_sql = "	UPDATE		flowprice_data		SET
															ex_id = '',
															sell_price = '$sell_price',	/*중량*/
															buy_price = '$buy_price'	/*타이틀*/
									WHERE			number = '$number'
	";
	$result = sql_query($common_sql);

}
else {	/* 복수레코드일때 */

	for($i = 0; $i < count($arr); $i++) {
		$grid = $arr[$i];

		$ex_id = $grid[id];
		$number = $grid[number];
		$fp_id = $grid[fp_id];
		$sell_price = $grid[sell_price];
		$buy_price = $grid[buy_price];

		/* 시세항목 설정값 입력 */
		$common_sql = "	UPDATE		flowprice_data		SET
															ex_id = '',
															sell_price = '$sell_price',	/*중량*/
															buy_price = '$buy_price'	/*타이틀*/
									WHERE			number = '$number'
	";
		$result = sql_query($common_sql);

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