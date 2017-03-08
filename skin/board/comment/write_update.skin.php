<?
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가 

if($w == '' || $w == 'r') { sql_query(" delete from $g5[board_new_table] where wr_id = '$wr_id' "); } 
goto_url("./board.php?bo_table=$bo_table&page=$page" . $qstr);
?>
