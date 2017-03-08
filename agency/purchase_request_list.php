<?php
include_once('./_common.php');

define("_ORDERINQUIRY_", true);

// 회원인 경우
$sql_common = " from {$g5['g5_purchase_order_table']} where mb_id = '{$member['mb_id']}' ";

// 테이블의 전체 레코드수만 얻음
$sql = " select count(*) as cnt " . $sql_common;
$row = sql_fetch($sql);
$total_count = $row['cnt'];


$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page == '') { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함


$g5['title'] = '주문내역조회';
include_once(G5_PATH.'/head.php');
?>

<!-- 주문 내역 시작 { -->
<div id="sod_v">
    <p>주문서번호 링크를 누르시면 주문상세내역을 조회하실 수 있습니다.</p>

    <?php
    $limit = " limit $from_record, $rows ";
    include "./purchase.sub.php";
    ?>

    <?php echo get_paging($config['cf_write_pages'], $page, $total_page, "{$_SERVER['PHP_SELF']}?$qstr&amp;page="); ?>
</div>
<!-- } 주문 내역 끝 -->

<?php
include_once(G5_PATH.'/tail.php');
?>
