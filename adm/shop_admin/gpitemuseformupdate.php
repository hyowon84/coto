<?php
$sub_menu = '500200';
$sub_sub_menu = '2';

include_once('./_common.php');

check_demo();

if ($w == 'd')
    auth_check($auth[$sub_menu], "d");
else
    auth_check($auth[$sub_menu], "w");

$qstr = "page=$page&amp;sort1=$sort1&amp;sort2=$sort2";

if ($w == "u")
{
    $sql = "update g5_shop_gpitem_use
               set is_subject = '$is_subject',
                   is_content = '$is_content',
                   is_confirm = '$is_confirm'
             where is_id = '$is_id' ";
    sql_query($sql);

    update_use_cnt($_POST['gp_id']);

    goto_url("./gpitemuseform.php?w=$w&amp;is_id=$is_id&amp;$qstr");
}
else
{
    alert();
}
?>
