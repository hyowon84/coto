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
	$SEQ_SQL = 	"	SELECT	CONCAT(	'WR',
																DATE_FORMAT(now(),'%Y%m%d'),
																LPAD(COALESCE(	(	SELECT	MAX(SUBSTR(wr_id,11,4))
																									FROM		wire_info
																									WHERE		wr_id LIKE CONCAT('%',DATE_FORMAT(now(),'%Y%m%d'),'%')
																									ORDER BY wr_id DESC
																								)
																,'0000') +1,4,'0')
												)	AS wr_id
								FROM		DUAL
	";
	list($wr_id) = mysql_fetch_array(sql_query($SEQ_SQL));
}


/* 인보이스 기본폼 입력 */
$common_sql = "	wire_info	SET
										wr_type			=	'$wr_type',
										wr_name			= '$wr_name',					/*송금내역 별칭*/
										wr_exchrate	=	'$wr_exchrate',			/*환율*/
										wr_currency = '$wr_currency',			/*통화유형*/
										wr_totalprice	=	'$wr_totalprice',	/*송금총액*/
										wr_out_fee	= '$wr_out_fee',			/*송금수수료(해외)*/
										wr_in_fee		= '$wr_in_fee',				/*송금수수료(국내)*/
										wr_memo			= '$wr_memo',					/*메모*/
										wr_date			= '$wr_date'					/*송금일*/
";


switch($mode) {
	case 'new':
		$sql = " INSERT INTO ".$common_sql."
														,wr_id		= '$wr_id'					/*인보이스ID*/
														,iv_id		= '$iv_id'					/*연결된 인보이스ID*/
														,admin_id	= '$member[mb_id]'	/*담당자*/
														,reg_date = now()
		";
		break;
	case 'update':
		$sql = "UPDATE ".$common_sql."
							WHERE		wr_id		= '$wr_id'
		";
		break;
	default:
		break;
}

$result = sql_query($sql);
db_log($sql,'wire_info',"송금정보 작성 $mode");

$list = explode(',',$iv_id);

for($i = 0; $i < count($list); $i++) {
	$target = $list[$i];

	$ii_sql = "	UPDATE invoice_info	SET
												wr_id = '$wr_id'
							WHERE		iv_id = '$target'
	";
	sql_query($ii_sql);

	$iv_sql = "	UPDATE invoice_item	SET
												iv_stats = '10'
							WHERE		iv_id = '$target'
							AND			iv_stats NOT IN ('99')
	";
	sql_query($iv_sql);

}




if($result) {
	$json[success] = "true";
	$json[message] = '송금내역이 작성되었습니다';
} else {
	$json[success] = "false";
	$json[message] = '입력이 실패하였습니다. 관리자에게 문의바랍니다.';
}

$json_data = json_encode_unicode($json);
echo $json_data;
?>