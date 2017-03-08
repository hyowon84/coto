<?php
include_once('./_common.php');
auth_check($auth[$sub_menu], "r");

//스트립슬래시를 안하면 json_decode가 안됨
$arr = json_decode(str_replace('\"','"',stripslashes( iconv('utf-8', 'cp949', $_POST['data'] ) )),true);
$mb_id = $member[mb_id];


function process($data) {
	
	$ac_yn = $data[ac_yn];
	$gp_id = $data[gp_id];

	$prev_sql = "	SELECT	*	FROM	g5_shop_group_purchase WHERE	gp_id = '$gp_id'";
	$prev = sql_fetch($prev_sql);

	//경매시작일때
	if($ac_yn == 'Y' && $prev[ac_yn] == 'N') {
		/* 경매 고유ID 생성 SQL  by. JHW */
		$seq_sql = "	SELECT	CONCAT(	'AC',
																DATE_FORMAT(now(),'%Y%m%d'),
																LPAD(COALESCE(	(	SELECT	MAX(SUBSTR(ac_code,11,4))
																									FROM		g5_shop_group_purchase
																									WHERE		ac_code LIKE CONCAT('%',DATE_FORMAT(now(),'%Y%m%d'),'%')
																									ORDER BY ac_code DESC
																								)
																,'0000') +1,4,'0')
												)	AS ac_code
									FROM		DUAL
		";
		list($ac_code) = mysql_fetch_array(sql_query($seq_sql));
	}else {
		$ac_code = $prev[ac_code];
	}

	/* 상품정보 수정 */
	$common_sql = "	UPDATE	g5_shop_group_purchase	SET
														ac_yn = '$ac_yn',										/*경매진행여부*/
														ac_code = '$ac_code',
														gp_update_time = now()
									WHERE		gp_id = '$gp_id'
	";
	sql_query($common_sql);
	db_log($common_sql,'g5_shop_group_purchase','상품가격관리 > 경매탭');
}

/* 단일레코드일때 */
if( strlen($arr[gp_id]) > 3 ) {
	$data = $arr;
	process($data);
}
else {	/* 복수레코드일때 */
	for($i = 0; $i < count($arr); $i++) {
		$data = $arr[$i];
		process($data);
	}
}


if($result) {
	$json[success] = "true";
	$json[message] = '상품정보가 수정되었습니다';
} else {
	$json[success] = "false";
	$json[message] = '상품정보가 수정되지 않았습니다. 관리자에게 문의바랍니다.';
}

$json_data = json_encode_unicode($json);
echo $json_data;
?>