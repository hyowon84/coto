<?php
$sub_menu = "900410";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$g5['title'] = '신규가입집계';
include_once('./admin.head.php');

$colspan = 5;

$sql_common = " from {$g5['member_table']} ";
$sql_search = " where mb_datetime between '".date("Y-m-d 00:00:00")."' and '".date("Y-m-d 23:59:59")."' ";
$sql = " select count(*) as cnt
            {$sql_common}
            {$sql_search} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page == '') $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql = " select *
            {$sql_common}
            {$sql_search}
            order by mb_no desc
            limit {$from_record}, {$rows} ";
$result = sql_query($sql);
?>

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col">회원ID</th>
        <th scope="col">회원 닉네임</th>
        <th scope="col">휴대폰번호</th>
        <th scope="col">가입일</th>
        <th scope="col">최근접속일자</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {

        $bg = 'bg'.($i%2);
    ?>
    <tr class="<?php echo $bg; ?>">
        <td class="td_mem"><?php echo $row[mb_id] ?></td>
        <td class="td_mem"><?php echo $link ?><?php echo $row[mb_nick] ?></td>
        <td class="td_mem"><?php echo $row[mb_hp] ?></td>
        <td class="td_datetime"><?php echo $row[mb_datetime] ?></td>
        <td class="td_datetime"><?php echo $row[mb_today_login] ?></td>
    </tr>

    <?php
    }
    if ($i == 0)
        echo '<tr><td colspan="'.$colspan.'" class="empty_table">자료가 없습니다.</td></tr>';
    ?>
    </tbody>
    </table>
</div>

<?php
if (isset($domain))
    $qstr .= "&amp;domain=$domain";
$qstr .= "&amp;page=";

$pagelist = get_paging($config['cf_write_pages'], $page, $total_page, "{$_SERVER['PHP_SELF']}?$qstr");
echo $pagelist;

include_once('./admin.tail.php');
?>
