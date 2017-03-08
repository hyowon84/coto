<?php
include_once('./_common.php');
auth_check($auth[$sub_menu], "r");


//스트립슬래시를 안하면 json_decode가 안됨
$arr = json_decode(str_replace('\"','"',stripslashes( iconv('utf-8', 'cp949', $_POST['data'] ) )),true);
$mb_id = $member[mb_id];


/* 단일레코드일때 */
if( strlen($arr[id]) > 3 || strlen($arr[pf_id]) > 3 ) {

	$d_id = $arr[d_id];
	$ex_id = $arr[id];
	$pf_id = $arr[pf_id];
	$metal_type = $arr[metal_type];
	$invest_type = $arr[invest_type];
	$item_name = $arr[item_name];
	$gram = $grid[gram];
	$gram_per_price = $arr[gram_per_price];
	$memo = $arr[memo];
	$mb_id = $arr[mb_id];

	/* 상품정보 수정 */
	$common_sql = "	INSERT	portfolio_data	SET
															ex_id = '$ex_id',
															pf_id = '$pf_id',
															metal_type = '$metal_type',		/*금속유형 GL, SL, PT, PD ...*/
															invest_type = '$invest_type',	/*투자성향 투자,수집,대비*/
															item_name = '$item_name',			/*품목명*/
															don = '$don',
															gram = '$gram',
															oz = '$oz',										/*무게 기본단위 oz*/
															gram_per_price = '$gram_per_price',
															memo = '$memo',
															reg_date = now()
	";
	sql_query($common_sql);

}
else {	/* 복수레코드일때 */

	for($i = 0; $i < count($arr); $i++) {
		$grid = $arr[$i];

		$d_id = $grid[d_id];
		$ex_id = $grid[id];
		$pf_id = $grid[pf_id];
		$metal_type = $grid[metal_type];
		$invest_type = $grid[invest_type];
		$item_name = $grid[item_name];
		$gram = $grid[gram];
		$gram_per_price = $grid[gram_per_price];
		$memo = $grid[memo];
		$mb_id = $grid[mb_id];


		$중복데이터 = mysql_fetch_array(sql_query("	SELECT	*	FROM	portfolio_data	WHERE		ex_id = '$ex_id' "));

		if($중복데이터[ex_id]) continue;

		/* 상품정보 수정 */
		$common_sql = "	INSERT	portfolio_data	SET
																ex_id = '$ex_id',
																pf_id = '$pf_id',
																metal_type = '$metal_type',		/*금속유형 GL, SL, PT, PD ...*/
																invest_type = '$invest_type',	/*투자성향 투자,수집,대비*/
																item_name = '$item_name',			/*품목명*/
																gram = '$gram',
																gram_per_price = '$gram_per_price',
																memo = '$memo',
																reg_date = now()
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