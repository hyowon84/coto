<?php
include_once('./_common.php');
//ini_set("display_errors", 1);

/* 반응형으로 갈 것임
if (G5_IS_MOBILE) {
    include_once(G5_MSHOP_PATH.'/orderinquiry.php');
    return;
}
*/

//include_once(G5_LIB_PATH.'/thumb.lib.php');

$sfl = escape_trim($_REQUEST['sfl']);
$stx = escape_trim($_REQUEST['stx']);

$g5['title'] = '상품 Q & A';
if (G5_IS_MOBILE) {
    include_once(G5_MSHOP_PATH.'/head.php');
} else {
    include_once(G5_PATH.'/_head.php');
}

$sql_common = " from g5_shop_gpitem_qa a LEFT join `{$g5['g5_shop_group_purchase_table']}` b on (a.gp_id=b.gp_id) ";
$sql_search = " where (1) ";

if(!$sfl)
    $sfl = 'b.gp_name';

if ($stx) {
    $sql_search .= " and ( ";
    switch ($sfl) {
        case "a.gp_id" :
            $sql_search .= " ($sfl like '$stx%') ";
            break;
        case "a.iq_name" :
        case "a.mb_id" :
            $sql_search .= " ($sfl = '$stx') ";
            break;
        default :
            $sql_search .= " ($sfl like '%$stx%') ";
            break;
    }
    $sql_search .= " ) ";
}

$sql_search .= " and a.mb_id='".$member[mb_id]."' ";

if($answer_month){
	$sql_search .= " and a.iq_time > '".date("Y-m-d H:i:s", strtotime("-".$answer_month." month", time()))."' ";
	$sql_search .= " and a.iq_time < '".date("Y-m-d", strtotime("now"))." 59:59:59' ";
}

if($answer_status == "y"){
	$sql_search .= " and iq_answer!='' ";
}else if($answer_status == "n"){
	$sql_search .= " and iq_answer='' ";
}

if (!$sst) {
    $sst  = "a.iq_id";
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

$sql = " select a.*, b.gp_name
          $sql_common
          $sql_search
          $sql_order
          limit $from_record, $rows ";
$result = sql_query($sql);

$itemqalist_skin = G5_SHOP_SKIN_PATH.'/gpitemqalist.skin.php';

if(!file_exists($itemqalist_skin)) {
    echo str_replace(G5_PATH.'/', '', $itemqalist_skin).' 스킨 파일이 존재하지 않습니다.';
} else {
    include_once($itemqalist_skin);
}

if (G5_IS_MOBILE) {
    include_once(G5_MSHOP_PATH.'/tail.php');
} else {
    include_once(G5_PATH.'/tail.php');
}
?>
