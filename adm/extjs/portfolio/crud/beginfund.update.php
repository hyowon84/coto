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

/* 단일레코드일때 */
if( strlen($arr[id]) > 3 || strlen($arr[pf_id]) > 3 ) {
	$ex_id = $arr[id];

	if(strlen($ex_id) > 3) $추가키값 = " OR	ex_id = ex_id = '$ex_id' ";

	$pf_id = $arr[pf_id];
	$surplus_fund = $arr[surplus_fund];
	$surplus_year = $arr[surplus_year];

	/* 상품정보 수정 */
	$common_sql = "	UPDATE	portfolio_mb	SET
															surplus_fund = '$surplus_fund',
															surplus_year = '$surplus_year'
									WHERE		pf_id = '$pf_id'
									$추가키값
	";
	sql_query($common_sql);

}
else {	/* 복수레코드일때 */

	for($i = 0; $i < count($arr); $i++) {
		$grid = $arr[$i];

		$ex_id = $grid[id];
		$pf_id = $grid[pf_id];
		$surplus_fund = $grid[surplus_fund];
		$surplus_year = $grid[surplus_year];

		/* 상품정보 수정 */
		$common_sql = "	UPDATE	portfolio_mb	SET
															surplus_fund = '$surplus_fund',
															surplus_year = '$surplus_year'
									WHERE		pf_id = '$pf_id'
									$추가키값
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