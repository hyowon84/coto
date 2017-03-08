<?php
include_once('./_common.php');
auth_check($auth[$sub_menu], "r");


//스트립슬래시를 안하면 json_decode가 안됨
$arr = json_decode(str_replace('\"','"',stripslashes( iconv('utf-8', 'cp949', $_POST['data'] ) )),true);
$mb_id = $member[mb_id];


/* 단일레코드일때 */
if( strlen($arr[id]) > 3 || strlen($arr[d_id]) > 3 ) {
	$ex_id = $arr[id];
	$d_id = $arr[d_id];
	$name = $arr[name];
	$hphone = $arr[hphone];
	$nick = $arr[nick];
	$mb_id = $arr[mb_id];

	/* 상품정보 수정 */
	$common_sql = "	DELETE FROM	portfolio_data
									WHERE		(d_id = '$d_id'	OR ex_id = '$ex_id')
	";
	sql_query($common_sql);

}
else {	/* 복수레코드일때 */

	for($i = 0; $i < count($arr); $i++) {
		$grid = $arr[$i];

		$ex_id = $grid[id];
		$d_id = $grid[d_id];
		$name = $grid[name];
		$hphone = $grid[hphone];
		$nick = $grid[nick];
		$mb_id = $grid[mb_id];

		/* 상품정보 수정 */
		$common_sql = "	DELETE FROM	portfolio_data
										WHERE		(d_id = '$d_id'	OR	ex_id = '$ex_id')
		";
		sql_query($common_sql);

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