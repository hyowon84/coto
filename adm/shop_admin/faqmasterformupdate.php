<?php
$sub_menu = '300910';
include_once('./_common.php');

if ($w == "u" || $w == "d")
    check_demo();

if ($W == 'd')
    auth_check($auth[$sub_menu], "d");
else
    auth_check($auth[$sub_menu], "w");

@mkdir(G5_DATA_PATH."/faq", G5_DIR_PERMISSION);
@chmod(G5_DATA_PATH."/faq", G5_DIR_PERMISSION);

if ($fm_himg_del)  @unlink(G5_DATA_PATH."/faq/{$fm_id}_h");
if ($fm_timg_del)  @unlink(G5_DATA_PATH."/faq/{$fm_id}_t");

$sql_common = " set fm_subject = '$fm_subject',
                    fm_head_html = '$fm_head_html',
                    fm_tail_html = '$fm_tail_html' ";

if ($w == "")
{
    $sql = " alter table {$g5['g5_shop_faq_master_table']} auto_increment=1 ";
    sql_query($sql);

    $sql = " insert {$g5['g5_shop_faq_master_table']} $sql_common ";
    sql_query($sql);

    $fm_id = mysql_insert_id();
}
else if ($w == "u")
{
    $sql = " update {$g5['g5_shop_faq_master_table']} $sql_common where fm_id = '$fm_id' ";
    sql_query($sql);
}
else if ($w == "d")
{
    @unlink(G5_DATA_PATH."/faq/{$fm_id}_h");
    @unlink(G5_DATA_PATH."/faq/{$fm_id}_t");

    // FAQ삭제
	$sql = " delete from {$g5['g5_shop_faq_master_table']} where fm_id = '$fm_id' ";
    sql_query($sql);

    // FAQ상세삭제
	$sql = " delete from {$g5['g5_shop_faq_table']} where fm_id = '$fm_id' ";
    sql_query($sql);
}

if ($w == "" || $w == "u")
{
    if ($_FILES['fm_himg']['name']) upload_file($_FILES['fm_himg']['tmp_name'], $fm_id."_h", G5_DATA_PATH."/faq");
    if ($_FILES['fm_timg']['name']) upload_file($_FILES['fm_timg']['tmp_name'], $fm_id."_t", G5_DATA_PATH."/faq");

    goto_url("./faqmasterform.php?w=u&amp;fm_id=$fm_id");
}
else
    goto_url("./faqmasterlist.php");
?>
