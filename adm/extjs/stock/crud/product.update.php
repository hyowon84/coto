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
if( strlen($arr[it_id]) > 3 ) {

	$gp_id = $arr[it_id];
	$gp_jaego = $arr[gp_jaego];
	$admin_memo = $arr[admin_memo];

	/* 상품정보 수정 */
	$common_sql = "	UPDATE	g5_shop_group_purchase	SET
														gp_jaego = '$gp_jaego',
														admin_memo = '$admin_memo'
									WHERE		gp_id = '$gp_id'
									
	";
	$result = sql_query($common_sql);
}
else {	/* 복수레코드일때 */

	for($i = 0; $i < count($arr); $i++) {
		$it = $arr[$i];
		$gp_id = $it[it_id];
		$gp_jaego = $it[gp_jaego];
		$admin_memo = $it[admin_memo];

		/* 상품정보 수정 */
		$common_sql = "	UPDATE	g5_shop_group_purchase	SET
															gp_jaego = '$gp_jaego',
															admin_memo = '$admin_memo'
										WHERE		gp_id = '$gp_id'
		";
		$result = sql_query($common_sql);
	}
}

//echo $common_sql;
db_log($common_sql,'g5_shop_group_purchase','발주탭 재고or메모 수정');


if($result) {
	$json[success] = "true";
	$json[message] = '상품이 수정되었습니다';
} else {
	$json[success] = "false";
	$json[message] = '상품이 수정되지 않았습니다. 관리자에게 문의바랍니다.';
}

$json_data = json_encode_unicode($json);
echo $json_data;
?>