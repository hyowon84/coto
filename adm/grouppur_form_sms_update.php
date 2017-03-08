<?php
$sub_menu = '500300';
$sub_sub_menu = '3';

include_once('./_common.php');

check_demo();

auth_check($auth[$sub_menu], "w");


//
// 영카트 default
//
$sql = " update {$g5['g5_shop_group_purchase_default_table']}
            set gp_sms_cont1                = '$gp_sms_cont1',
                gp_sms_cont2                = '$gp_sms_cont2',
                gp_sms_cont3                = '$gp_sms_cont3' 
                ";
sql_query($sql);

goto_url("./grouppur_form.php");
?>
