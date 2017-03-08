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


/* 새로 작성시에만 인보이스 시퀀스 생성 */
if($mode == 'new') {
	$SEQ_SQL = 	"	SELECT	CONCAT(	'IV',
																DATE_FORMAT(now(),'%Y%m%d'),
																LPAD(COALESCE(	(	SELECT	MAX(SUBSTR(iv_id,11,4))
																									FROM		invoice_info
																									WHERE		iv_id LIKE CONCAT('%',DATE_FORMAT(now(),'%Y%m%d'),'%')
																									ORDER BY iv_id DESC
																								)
																,'0000') +1,4,'0')
												)	AS iv_id
								FROM		DUAL
	";
	list($iv_id) = mysql_fetch_array(sql_query($SEQ_SQL));
}


/* 인보이스 기본폼 입력 */
$common_sql = "	invoice_info	SET
								iv_name					=	'$iv_name',
								gpcode					= '$gpcode',					/*같이 묶어서 보길 원할경우 입력*/
								iv_dealer				= '$iv_dealer',				/*인보이스 딜러업체*/
								iv_order_no			= '$iv_order_no',			/*인보이스 주문번호*/
								money_type			=	'$money_type',			/*통화유형*/
								iv_receipt_link	= '$iv_receipt_link',	/*해외송금 입출금내역 링크*/
								iv_date					= '$iv_date',					/*인보이스 날짜*/
								iv_shippingfee	=	'$iv_shippingfee',	/*배송비*/
								iv_tax					=	'$iv_tax',					/*관세*/
								iv_discountfee	= '$iv_discountfee',	/*디스카운트금액*/
								iv_memo					= '$iv_memo'					/*내용*/
";


switch($mode) {
	case 'new':
		$sql = " INSERT INTO ".$common_sql."
														,iv_id		= '$iv_id'			/*인보이스ID*/
														,admin_id	= '$member[mb_id]'		/*담당자*/
														,reg_date = now()
		";
		break;
	case 'update':
		$sql = "UPDATE ".$common_sql."
							WHERE		gpcode	= '$gpcode'		/*같이 묶어서 보길 원할경우 입력*/
							AND			iv_id		= '$iv_id'
		";
		break;
	default:
		break;
}

$result = sql_query($sql);
db_log($sql,'invoice_info',"발주서 $mode");


/* 인보이스 물품 항목들 입력 */
for($i = 0; $i < count($grid); $i++) {
	$it = $grid[$i];

	$it[iv_it_name] = str_replace("'", "\'", $it[iv_it_name]);
	$it[iv_it_name] = str_replace('"', "\'", $it[iv_it_name]);


	/* 인보이스 기본폼 입력 */
	$common_sql = "	invoice_item	SET
											iv_it_name 			= \"$it[iv_it_name]\",		/*주문상품명*/
											iv_dealer_worldprice		= '$it[iv_dealer_worldprice]',		/*주문단가*/
											iv_dealer_price = '$it[iv_dealer_price]',	/*주문단가*/
											ip_qty					= '$it[ip_qty]',					/*실제출고수량*/
											iv_qty					= '$it[iv_qty]',					/*주문수량*/
											iv_dealer				= '$iv_dealer',						/*딜러*/
											iv_order_no			= '$it[iv_order_no]',			/*인보이스 번호*/
											iv_receipt_link = '$it[iv_receipt_link]'	/*입출금링크번호*/
	";

	switch($mode) {
		case 'new':
			$sql = " INSERT INTO ".$common_sql."
															,gpcode		= '$it[gpcode]'				/*같이 묶어서 보길 원할경우 입력*/
															,iv_id		= '$iv_id'				/*인보이스ID*/
															,iv_it_id	= '$it[iv_it_id]'	/*주문상품코드*/
															,reg_date = now()
			";
			break;
		case 'update':
			$sql = "UPDATE ".$common_sql."
								WHERE		gpcode		= '$it[gpcode]'				/*같이 묶어서 보길 원할경우 입력*/
								AND			iv_id			= '$iv_id'				/*인보이스ID*/
								AND			iv_it_id	= '$it[iv_it_id]'	/*주문상품코드*/
			";
			break;
		default:
			break;
	}
	sql_query($sql);
	db_log($sql,'invoice_item',"발주품목 $mode");
}




if($result) {
	$json[success] = "true";
	$json[message] = '발주 정보가 입력되었습니다';
} else {
	$json[success] = "false";
	$json[message] = '입력이 실패하였습니다. 관리자에게 문의바랍니다.';
}

$json_data = json_encode_unicode($json);
echo $json_data;
?>