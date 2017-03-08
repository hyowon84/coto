<?php
include_once('./_common.php');
auth_check($auth[$sub_menu], "r");


//스트립슬래시를 안하면 json_decode가 안됨
$arr = json_decode(str_replace('\"','"',stripslashes( iconv('utf-8', 'cp949', $_POST['data'] ) )),true);
$mb_id = $member[mb_id];


/* 단일레코드일때 */
if( strlen($arr[id]) > 3 || strlen($arr[number]) > 0 ) {
	$number = $arr[number];
	$ex_id = $arr[ex_id];
	$sortNo = $arr[sortNo];
	$money_type = $arr[money_type];
	$qty = $arr[qty];
	$sellfee = $arr[sellfee];
	$buyfee = $arr[buyfee];
	$title = $arr[title];
	$reg_date = $arr[reg_date];

	/* 시세항목 설정값 입력 */
	$common_sql = "	UPDATE	exchrate_cfg		SET
															ex_id = '$ex_id',
															sortNo = '$sortNo',	/*정렬*/
															money_type = '$money_type',	/*화폐 유형*/
															qty = '$qty',	/*수량*/
															sellfee = '$sellfee',	/*판매수수료*/
															buyfee = '$buyfee',	/*매입수수료*/
															title = '$title',	/*타이틀*/
															reg_date = now()
									WHERE		number = '$number'
	";
	sql_query($common_sql);

}
else {	/* 복수레코드일때 */

	for($i = 0; $i < count($arr); $i++) {
		$grid = $arr[$i];

		$number = $grid[number];
		$ex_id = $grid[ex_id];
		$sortNo = $grid[sortNo];
		$money_type = $grid[money_type];
		$qty = $grid[qty];
		$sellfee = $grid[sellfee];
		$buyfee = $grid[buyfee];
		$title = $grid[title];
		$reg_date = $grid[reg_date];

		/* 시세항목 설정값 입력 */
		$common_sql = "	UPDATE	exchrate_cfg		SET
															ex_id = '$ex_id',
															sortNo = '$sortNo',	/*정렬*/
															money_type = '$money_type',	/*화폐 유형*/
															qty = '$qty',	/*수량*/
															sellfee = '$sellfee',	/*판매수수료*/
															buyfee = '$buyfee',	/*매입수수료*/
															title = '$title',	/*타이틀*/
															reg_date = now()
									WHERE		number = '$number'
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