<?php
include_once('./_common.php');

auth_check($auth[$sub_menu], "r");


$ins_sql = "	INSERT	INTO	log_table	SET
															logtype = 'mustpay',
															gr_id = '$clay_id',
															pk_id = '$hphone',
															memo = '$cs_memo',
															reg_date = NOW()
";

if(sql_query($ins_sql)) {
	echo 1;
} else {
	echo 0;
}

?>