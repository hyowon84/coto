<?php
include_once('./_common.php');

/* 반응형으로 갈 것임
if (G5_IS_MOBILE) {
    include_once(G5_MSHOP_PATH.'/itemuselist.php');
    return;
}
*/

//include_once(G5_LIB_PATH.'/thumb.lib.php');

$sfl = escape_trim($_REQUEST['sfl']);
$stx = escape_trim($_REQUEST['stx']);

$g5['title'] = '사용후기';
if (G5_IS_MOBILE) {
    include_once(G5_MSHOP_PATH.'/head.php');
} else {
    include_once(G5_PATH.'/_head.php');
}

$sql_common = " from `{$g5['g5_shop_item_use_table']}` a join `{$g5['g5_shop_item_table']}` b on (a.it_id=b.it_id) ";
$sql_search = " where a.is_confirm = '1' ";

if(!$sfl)
    $sfl = 'b.it_name';

if ($stx) {
    $sql_search .= " and ( ";
    switch ($sfl) {
        case "a.it_id" :
            $sql_search .= " ($sfl like '$stx%') ";
            break;
        case "a.is_name" :
        case "a.mb_id" :
            $sql_search .= " ($sfl = '$stx') ";
            break;
        default :
            $sql_search .= " ($sfl like '%$stx%') ";
            break;
    }
    $sql_search .= " ) ";
}

if (!$sst) {
    $sst  = "a.is_id";
    $sod = "desc";
}
$sql_order = " order by $sst $sod ";

$sql = " select count(*) as cnt
         $sql_common
         $sql_search
         $sql_order ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " select *
          $sql_common
          $sql_search
          $sql_order
          limit $from_record, $rows ";
$result = sql_query($sql);

include_once("../inc/mypage_menu.php");


$itemuselist_skin = G5_SHOP_SKIN_PATH.'/itemuselist.skin.php';

echo "<div class='contentWrap' style='background:#fff;'>";
include_once("../inc/mypage_submenu.php");
if(!file_exists($itemuselist_skin)) {
    echo str_replace(G5_PATH.'/', '', $itemuselist_skin).' 스킨 파일이 존재하지 않습니다.';
} else {
    include_once($itemuselist_skin);
}
echo "</div>";

if (G5_IS_MOBILE) {
    include_once(G5_MSHOP_PATH.'/tail.php');
} else {
    include_once(G5_PATH.'/tail.php');
}
?>
