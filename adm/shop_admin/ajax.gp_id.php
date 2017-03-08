<?php
include_once('./_common.php');

$gp_id = trim($_POST['gp_id']);

if (preg_match("/[^\w\-]/", $gp_id)) { // \w : 0-9 A-Z a-z _
    //die("{\"error\":\"상품코드는 영문자 숫자 _ - 만 입력 가능합니다.\"}");
}

$sql = " select gp_name from {$g5['g5_shop_group_purchase_table']} where gp_id = '{$gp_id}' ";
$row = sql_fetch($sql);
if ($row['gp_name']) {
    $gp_name = addslashes($row['gp_name']);
    die("{\"error\":\"이미 등록된 상품코드 입니다.\\n\\n상품명 : {$gp_name}\"}");
}

die("{\"error\":\"\"}"); // 정상
?>