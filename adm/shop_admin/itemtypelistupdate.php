<?php
$sub_menu = '400200';
$sub_sub_menu = '2';

include_once('./_common.php');

check_demo();

auth_check($auth[$sub_menu], "w");

for ($i=0; $i<count($_POST['it_id']); $i++)
{
    $sql = "update {$g5['g5_shop_item_table']}
               set it_type = '".$_POST['it_type'.$i]."'
             where it_id = '{$_POST['it_id'][$i]}' ";

    sql_query($sql);
}

//goto_url("./itemtypelist.php?sort1=$sort&amp;sort2=$sort2&amp;sel_ca_id=$sel_ca_id&amp;sel_field=$sel_field&amp;search=$search&amp;page=$page");
goto_url("itemtypelist.php?sca=$sca&amp;sst=$sst&amp;sod=$sod&amp;sfl=$sfl&amp;stx=$stx&amp;page=$page");
?>
