<?php
include_once('./_common.php');
auth_check($auth[$sub_menu], "r");


//스트립슬래시를 안하면 json_decode가 안됨

$arr = json_decode(str_replace('\"','"',stripslashes( iconv('utf-8', 'cp949', $_POST['data'] ) )),true);
$mb_id = $member[mb_id];

/* 단일레코드일때 */
if( strlen($arr[number]) > 3 ) {

	$number = $arr[number];
	$od_id = $arr[od_id];
	$admin_link = $arr[admin_link];
	$admin_memo = $arr[admin_memo];


	/* 상품정보 수정 */
	$common_sql = "	UPDATE	bank_db	SET
														admin_link = '$admin_link',			/*연결된 주문번호들*/
														admin_memo = '$admin_memo'			/*관리자 메모*/
									WHERE		number = '$number'
	";
	sql_query($common_sql);

}
else {	/* 복수레코드일때 */

	for($i = 0; $i < count($arr); $i++) {

		$grid = $arr[$i];
		$od_id = $grid[od_id];
		$number = $grid[number];
		$admin_link = $grid[admin_link];
		$admin_memo = $grid[admin_memo];


		/* 상품정보 수정 */
		$common_sql = "	UPDATE	bank_db	SET
															admin_link = '$admin_link',			/*연결된 주문번호들*/
															admin_memo = '$admin_memo'			/*관리자 메모*/
										WHERE		number = '$number'
		";
		sql_query($common_sql);

	}
}


db_log($common_sql,'bank_db','입출금내역 수정');


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