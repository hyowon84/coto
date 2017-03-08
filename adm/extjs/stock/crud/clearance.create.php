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
$grid = jsonDecode($_POST['grid']);

/* 새로 작성시에만 송금 시퀀스 생성 */
if($mode == 'new') {
	$SEQ_SQL = 	"	SELECT	CONCAT(	'CR',
																DATE_FORMAT(now(),'%Y%m%d'),
																LPAD(COALESCE(	(	SELECT	MAX(SUBSTR(cr_id,11,4))
																									FROM		clearance_info
																									WHERE		cr_id LIKE CONCAT('%',DATE_FORMAT(now(),'%Y%m%d'),'%')
																									ORDER BY cr_id DESC
																								)
																,'0000') +1,4,'0')
												)	AS cr_id
								FROM		DUAL
	";
	list($cr_id) = mysql_fetch_array(sql_query($SEQ_SQL));
}


/* 통관 기본폼 입력 */
$common_sql = "	clearance_info	SET
										cr_name = '$cr_name',	/*통관내역 별칭*/
										cr_refno = '$cr_refno',	/*통관번호*/
										cr_taxfee = '$cr_taxfee',		/*통관비용 총액*/
										cr_shipfee = '$cr_shipfee',					/*통관수수료*/
										cr_file = '$cr_file',	/*통관내역서 파일첨부*/
										cr_memo = '$cr_memo',	/*메모*/
										cr_date = '$cr_date'	/*통관일*/
";


switch($mode) {
	case 'new':
		$sql = " INSERT INTO ".$common_sql."
														,cr_id		= '$cr_id'					/*인보이스ID*/
														,iv_id		= '$iv_id'					/*연결된 인보이스ID*/
														,admin_id	= '$member[mb_id]'	/*담당자*/
														,reg_date = now()
		";
		break;
	case 'update':
		$sql = "UPDATE ".$common_sql."
							WHERE		cr_id		= '$cr_id'
		";
		break;
	default:
		break;
}

$result = sql_query($sql);
db_log($sql,'clearance_info',"통관탭 통관정보 $mode");

$list = explode(',',$iv_id);

for($i = 0; $i < count($list); $i++) {
	$target = $list[$i];

	$cr_sql = "	UPDATE invoice_info	SET
												cr_id = '$cr_id'
							WHERE		iv_id = '$target'
	";
	sql_query($cr_sql);
}



/* 통관물품 항목들 입력 */
for($i = 0; $i < count($grid); $i++) {
	$it = $grid[$i];

	$it[cr_it_name] = str_replace("'", "\'", $it[cr_it_name]);
	$it[cr_it_name] = str_replace('"', "\'", $it[cr_it_name]);


	/* 인보이스 기본폼 입력 */
	$common_sql = "	 clearance_item	SET
												cr_qty				= '$it[cr_qty]',
												cr_cancel_qty = '$it[cr_cancel_qty]'
	";

	switch($mode) {
		case 'new':
			$sql = " INSERT INTO ".$common_sql."
															,iv_pkno	= '$it[number]'
															,cr_id		= '$cr_id'								/*통관코드*/
															,iv_id		= '$it[iv_id]'
															,ip_id		= '$it[ip_id]'
															,wr_id		= '$it[wr_id]'
															,gpcode		=	'$it[gpcode]'
															,cr_it_name = \"$it[cr_it_name]\"		/*통관품목명*/
															,cr_it_id	=	'$it[cr_it_id]'					/*통관품목코드*/
															,reg_date = now()
			";
			break;
		case 'update':
			$sql = "UPDATE ".$common_sql."
								WHERE		cr_id		= '$cr_id'						/*통관코드*/
								AND			cr_it_id	= '$it[cr_it_id]'		/*통관품목코드*/
			";
			break;
		default:
			break;
	}
	sql_query($sql);
	
	
	if($mode == 'new') {
		$iv_sql = "	UPDATE	invoice_item	SET
													iv_stats = '20'
								WHERE		number = '$it[number]'
								AND			iv_stats NOT IN ('99')
		";
		sql_query($iv_sql);
	}
	
	
}





if($result) {
	$json[success] = "true";
	$json[message] = '통관내역이 작성되었습니다';
} else {
	$json[success] = "false";
	$json[message] = '입력이 실패하였습니다. 관리자에게 문의바랍니다.';
}

$json_data = json_encode_unicode($json);
echo $json_data;
?>