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
	$pf_id = $arr[pf_id];
	$invest_type = $arr[invest_type];
	$target_per = $arr[target_per];
	$metal_type = $arr[metal_type];

	/* 상품정보 수정 */
	$common_sql = "	UPDATE	portfolio_investdtl	SET
															target_per = '$target_per'	/*목표%*/
									WHERE		pf_id = '$pf_id'
									AND			invest_type = '$invest_type'
									AND			metal_type = '$metal_type'
	";
	sql_query($common_sql);
}
else {	/* 복수레코드일때 */

	for($i = 0; $i < count($arr); $i++) {
		$grid = $arr[$i];

		$pf_id = $grid[pf_id];
		$invest_type = $grid[invest_type];
		$target_per = $grid[target_per];
		$metal_type = $arr[metal_type];

		/* 상품정보 수정 */
		$common_sql = "	UPDATE	portfolio_investdtl	SET
																target_per = '$target_per'	/*목표%*/
										WHERE		pf_id = '$pf_id'
										AND			invest_type = '$invest_type'
										AND			metal_type = '$metal_type'
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