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



function process($data) {

	$data[addr1] = ($data[addr1] == '구주소') ? '' : $data[addr1];
	$data[addr1_2] = ($data[addr1_2] == '신주소') ? '' : $data[addr1_2];

	/* 상품정보 수정 */
	$common_sql = "	UPDATE	clay_order_info	SET
													clay_id 						= '$data[clay_id]',							/*클레이ID*/
													paytype							= '$data[paytype]',							/*결제방법 무통장P01, 카드결제P02, 외화달러P03, 귀금속결제P04*/
													receipt_link				= '$data[receipt_link]',				/*입출금내역 연결번호*/
													name								= '$data[name]',								/*주문자명*/
													receipt_name				= '$data[receipt_name]',				/*입금자명*/
													hphone							= '$data[hphone]',							/*휴대폰번호*/
													zip									= '$data[zip]',									/*우편번호(5자리)*/
													addr1								= '$data[addr1]',								/*배송지 기본주소(지번주소)*/
													addr1_2							= '$data[addr1_2]',							/*배송지 기본주소(도로명주소)*/
													addr2								= '$data[addr2]',								/*배송지 상세주소*/
													delivery_type				= '$data[delivery_type]',				/*배송비유형 선불/착불/방문수령*/
													delivery_price			= '$data[delivery_price]',			/*주문당시 배송비합계액*/
													delivery_invoice		= '$data[delivery_invoice2]',		/*운송장번호*/
													cash_receipt_yn			=	'$data[cash_receipt_yn]',
													cash_receipt_type		=	'$data[cash_receipt_type]',
													cash_receipt_info		=	'$data[cash_receipt_info]',
													refund_money				=	'$data[refund_money]',
													memo								= '$data[memo]',								/*주문자 요청사항*/
													admin_memo					= '$data[admin_memo]'						/*관리자 메모*/
								WHERE		od_id		= '$data[od_id]'		/*주문ID*/
	";
	sql_query($common_sql);
	db_log($common_sql,'clay_order_info','주문신청관리 주문정보 수정');
	
	
	
	$item_sql = "	UPDATE	clay_order	SET
													clay_id 						= '$data[clay_id]',							/*클레이ID*/
													hphone							= '$data[hphone]',							/*휴대폰번호*/
													name								= '$data[name]'									/*주문자명*/
								WHERE		gpcode	= '$data[gpcode]'		/*연결된 공구코드*/
								AND			od_id		= '$data[od_id]'		/*주문ID*/
	";
	sql_query($item_sql);
	db_log($item_sql,'clay_order','주문신청관리 주문정보 수정후 품목별 기본정보 수정');
	
}



/* 단일레코드일때 */
if( strlen($arr[od_id]) > 3 ) {
	$vo = $arr;
	
}
else {

	/* 주문정보 폼 기록 */
	if($mode == 'form') {
		$vo = $_POST;
	}
}


//2개 이상일경우
if( !strlen($arr[od_id]) && count($arr) > 1) {

	for($i = 0; $i < count($arr); $i++) {
			$vo = $arr[$i];

			process($vo);
		}

} else {	//단일 레코드일 경우

	$vo[addr1] = ($vo[addr1] == '구주소') ? '' : $vo[addr1];
	$vo[addr1_2] = ($vo[addr1_2] == '신주소') ? '' : $vo[addr1_2];

	process($vo);
}


if($result) {
	$json[success] = "true";
	$json[message] = '주문정보가 수정되었습니다';
} else {
	$json[success] = "false";
	$json[message] = '주문정보가 수정되지 않았습니다. 관리자에게 문의바랍니다.';
}

$json_data = json_encode_unicode($json);
echo $json_data;
?>