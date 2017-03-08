<?php
include_once('./_common.php');
auth_check($auth[$sub_menu], "r");


//스트립슬래시를 안하면 json_decode가 안됨
$arr = json_decode(str_replace('\"','"',stripslashes( iconv('utf-8', 'cp949', $_POST['data'] ) )),true);
$mb_id = $member[mb_id];


/* 단일레코드일때 */
if( strlen($arr[id]) > 3 ) {

	/* 고유ID 생성 SQL    by. JHW */
	$seq_sql = "	SELECT	CONCAT(	'PF',
																DATE_FORMAT(now(),'%Y%m%d'),
																LPAD(COALESCE(	(	SELECT	MAX(SUBSTR(pf_id,11,4))
																									FROM		portfolio_mb
																									WHERE		pf_id LIKE CONCAT('%',DATE_FORMAT(now(),'%Y%m%d'),'%')
																									ORDER BY pf_id DESC
																								)
																,'0000') +1,4,'0')
												)	AS oid
								FROM		DUAL
	";
	list($pf_id) = mysql_fetch_array(sql_query($seq_sql));
	$ex_id = $arr[id];
	$name = $arr[name];
	$hphone = $arr[hphone];
	$nick = $arr[nick];
	$mb_id = $arr[mb_id];

	$common_sql = "INSERT		portfolio_mb	SET
															ex_id = '$ex_id',
															pf_id = '$pf_id',		/*포트폴리오 관리ID*/
															name = '$name',			/*이름*/
															hphone = '$hphone',	/*연락처*/
															nick = '$nick',			/*카페활동닉네임이 있을경우 입력*/
															mb_id = '$mb_id',		/*코투 계정이 있을경우 입력*/
															reg_date = now()		/*등록일*/
	";
	sql_query($common_sql);

	/*투자성향 입력폼을 위한 기본값 셋팅*/
	$ivcode_sql = "	SELECT	*
									FROM		comcode CC
									WHERE		CC.ctype = 'portfolio'
									AND			CC.col = 'invest_type'
									ORDER BY CC.order ASC
	";
	$ivcode_result = sql_query($ivcode_sql);


	$metal = Array('Gold','Silver');
	$sortNo = 0;
	$dtl_cnt = 0;

	while( $code = mysql_fetch_array($ivcode_result)) {

		$sortNo++;
		$invest_type = $code[code];

		$ins_sql = "INSERT		portfolio_invest	SET
															pf_id = '$pf_id',			/*회원관리번호*/
															sortNo = '$sortNo',
															invest_type = '$invest_type',	/*투자유형 코드 I01:투자, I02:대비,생존*/
															target_per = '0'			/*목표%*/
		";
		sql_query($ins_sql);


		for($m = 0; $m < count($metal); $m++) {
			$dtl_cnt++;
			$ins_sql = "INSERT		portfolio_investdtl	SET
															pf_id = '$pf_id',				/*회원관리번호*/
															sortNo = '$dtl_cnt',
															invest_type = '$invest_type',		/*투자유형 코드 I01:투자, I02:대비,생존*/
															metal_type = '".$metal[$m]."',	/*금속유형 Gold:금, Silver:실버*/
															target_per = '0'	/*목표%*/
			";
			sql_query($ins_sql);
		}

	}

}
else {	/* 복수레코드일때 */

	for($i = 0; $i < count($arr); $i++) {
		/* 고유ID 생성 SQL    by. JHW */
		$seq_sql = "	SELECT	CONCAT(	'PF',
																	DATE_FORMAT(now(),'%Y%m%d'),
																	LPAD(COALESCE(	(	SELECT	MAX(SUBSTR(pf_id,11,4))
																										FROM		portfolio_mb
																										WHERE		pf_id LIKE CONCAT('%',DATE_FORMAT(now(),'%Y%m%d'),'%')
																										ORDER BY pf_id DESC
																									)
																	,'0000') +1,4,'0')
													)	AS oid
									FROM		DUAL
		";
		list($pf_id) = mysql_fetch_array(sql_query($seq_sql));

		$grid = $arr[$i];
		$name = $grid[name];
		$hphone = $grid[hphone];
		$nick = $grid[nick];
		$mb_id = $grid[mb_id];

		$common_sql = "INSERT		portfolio_mb	SET
																ex_id = '$ex_id',
																pf_id = '$pf_id',		/*포트폴리오 관리ID*/
																name = '$name',			/*이름*/
																hphone = '$hphone',	/*연락처*/
																nick = '$nick',			/*카페활동닉네임이 있을경우 입력*/
																mb_id = '$mb_id',		/*코투 계정이 있을경우 입력*/
																reg_date = now()		/*등록일*/
		";
		sql_query($common_sql);

	}
}

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