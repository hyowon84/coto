<?php
include_once('./_common.php');
auth_check($auth[$sub_menu], "r");


//스트립슬래시를 안하면 json_decode가 안됨
$arr = json_decode(str_replace('\"','"',stripslashes( iconv('utf-8', 'cp949', $_POST['data'] ) )),true);
$mb_id = $member[mb_id];


/* 단일레코드일때 */
if( strlen($arr[id]) > 3 || strlen($arr[number]) > 3 ) {

	list($prev_id) = mysql_fetch_array(sql_query("SELECT	MAX(gr_id) FROM flowprice_data"));
	$gr_id = $prev_id + 1;

	$ex_id = $arr[id];
	$fp_id = $arr[fp_id];
	$metal_type = $arr[metal_type];
	$sell_price = $arr[sell_price];
	$buy_price = $arr[buy_price];
	$weight = $arr[weight];


	//이전 시세 종료일 업데이트
	$prev_sql = "UPDATE		flowprice_data	SET
													end_date = now()
									WHERE		gr_id = '$prev_id'
	";
	sql_query($prev_sql);


	/* 시세항목 설정값 입력 */
	$common_sql = "	INSERT	INTO	flowprice_data		SET
															gr_id = '$gr_id',
															fp_id = '$fp_id',
															ex_id = '$ex_id',
															weight = '$weight',
															metal_type = '$metal_type',	/*귀금속 유형*/
															sell_price = '$sell_price',	/*중량*/
															buy_price = '$buy_price',	/*타이틀*/
															start_date = now(),	/*시작일(생성일)*/
															end_date = now()	/*종료일(마감일)*/
	";
	sql_query($common_sql);

}
else {	/* 복수레코드일때 */

	list($prev_id) = mysql_fetch_array(sql_query("SELECT	MAX(gr_id) FROM flowprice_data"));
	$gr_id = $prev_id + 1;

	for($i = 0; $i < count($arr); $i++) {
		$grid = $arr[$i];

		$ex_id = $grid[id];
		$fp_id = $grid[fp_id];
		$metal_type = $grid[metal_type];
		$sell_price = $grid[sell_price];
		$buy_price = $grid[buy_price];
		$weight = $grid[weight];

//	$중복데이터 = sql_fetch("	SELECT	*	FROM	flowprice_data	WHERE		ex_id = '$ex_id' ");
//	if($중복데이터[ex_id]) continue;


		//이전 시세 종료일 업데이트
		$prev_sql = "UPDATE		flowprice_data	SET
													end_date = now()
									WHERE		gr_id = '$prev_id'
		";
		sql_query($prev_sql);



		/* 시세항목 설정값 입력 */
		$common_sql = "	INSERT	INTO	flowprice_data		SET
															gr_id = '$gr_id',
															fp_id = '$fp_id',
															ex_id = '$ex_id',
															weight = '$weight',
															metal_type = '$metal_type',	/*귀금속 유형*/
															sell_price = '$sell_price',	/*중량*/
															buy_price = '$buy_price',	/*타이틀*/
															start_date = now(),	/*시작일(생성일)*/
															end_date = now()	/*종료일(마감일)*/
		";
		sql_query($common_sql);

	}
}

if($result) {
	$json[success] = "true";
	$json[message] = '입력되었습니다';
} else {
	$json[success] = "false";
	$json[message] = '입력되지 않았습니다. 관리자에게 문의바랍니다.';
}

$json_data = json_encode_unicode($json);
echo $json_data;
?>