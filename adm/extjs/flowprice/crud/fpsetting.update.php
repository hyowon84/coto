<?php
include_once('./_common.php');
auth_check($auth[$sub_menu], "r");


//스트립슬래시를 안하면 json_decode가 안됨
$arr = json_decode(str_replace('\"','"',stripslashes( iconv('utf-8', 'cp949', $_POST['data'] ) )),true);
$mb_id = $member[mb_id];


/* 단일레코드일때 */
if( strlen($arr[id]) > 3 || strlen($arr[number]) > 0 ) {
	$ex_id = $arr[id];
	$number = $arr[number];
	$sortNo = $arr[sortNo];
	$metal_type = $arr[metal_type];
	$weight = $arr[weight];
	$add_price = $arr[add_price];
	$title = $arr[title];

	/* 상품정보 수정 */
	$common_sql = "	UPDATE	flowprice_cfg	SET
															ex_id = '$ex_id',
															sortNo = '$sortNo',					/*정렬*/
															metal_type = '$metal_type',	/*귀금속 유형*/
															weight = '$weight',					/*중량*/
															add_price = '$add_price',		/*추가금액*/
															title = '$title'						/*타이틀*/
									WHERE		ex_id = '$ex_id'
									OR			number = '$number'
	";
	sql_query($common_sql);

}
else {	/* 복수레코드일때 */

	for($i = 0; $i < count($arr); $i++) {
		$grid = $arr[$i];

		$ex_id = $grid[id];
		$number = $grid[number];
		$sortNo = $grid[sortNo];
		$metal_type = $grid[metal_type];
		$weight = $grid[weight];
		$add_price = $grid[add_price];
		$title = $grid[title];

		/* 상품정보 수정 */
		$common_sql = "	UPDATE	flowprice_cfg	SET
															ex_id = '$ex_id',
															sortNo = '$sortNo',					/*정렬*/
															metal_type = '$metal_type',	/*귀금속 유형*/
															weight = '$weight',					/*중량*/
															add_price = '$add_price',		/*추가금액*/
															title = '$title'						/*타이틀*/
									WHERE		ex_id = '$ex_id'
									OR			number = '$number'
	";
		sql_query($common_sql);

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