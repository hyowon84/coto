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
$cr_id = $arr['cr_id'];
$cr_name = $arr['cr_name'];
$cr_refno = $arr['cr_refno'];
$cr_dutyfee = $arr['cr_dutyfee'];
$cr_taxfee = $arr['cr_taxfee'];
$cr_shipfee = $arr['cr_shipfee'];
$cr_file = $arr['cr_file'];
$cr_memo = $arr['cr_memo'];
$cr_date = $arr['cr_date'];


/* 통관 기본폼 입력 */
$common_sql = "	UPDATE		clearance_info	SET
														cr_name = '$cr_name',	/*통관내역 별칭*/
														cr_refno = '$cr_refno',	/*통관번호*/
														cr_dutyfee = '$cr_dutyfee',		/* 관세 */
														cr_taxfee = '$cr_taxfee',			/* 부가세 */
														cr_shipfee = '$cr_shipfee',		/* 배송비 */
														cr_file = '$cr_file',	/*통관내역서 파일첨부*/
														cr_memo = '$cr_memo',	/*메모*/
														cr_date = '$cr_date'	/*통관일*/
								WHERE		cr_id		= '$cr_id'
";
$result = sql_query($common_sql);
db_log($common_sql,'clearance_info','통관탭 통관정보 수정');

if($result) {
	$json[success] = "true";
	$json[message] = '통관내역이 수정되었습니다';
} else {
	$json[success] = "false";
	$json[message] = '수정이 실패하였습니다. 관리자에게 문의바랍니다.';
}

$json_data = json_encode_unicode($json);
echo $json_data;
?>