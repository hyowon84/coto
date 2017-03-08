<?php
$sub_menu = "200800";
include_once('./_common.php');
include_once('./admin.head.php');

auth_check($auth[$sub_menu], 'r');

$g5['title'] = '제휴문의';

$colspan = 6;

$sql_common = " from {$g5['g5_alliance_table']} ";
$sql_search = " where 1 ";
if (isset($domain))
    $sql_search .= " and vi_referer like '%{$domain}%' ";

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
            order by no desc
            limit {$from_record}, {$rows} ";
$result = sql_query($sql);
?>

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col">기업정보</th>
        <th scope="col">담당자명</th>
        <th scope="col">연락처</th>
        <th scope="col">이메일</th>
        <th scope="col">날짜</th>
		<th scope="col">답변여부</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
		if($row[reply_status] == "n"){
			$status = "<font color='red'>미답변</font>";
		}else{
			$status = "<font color='blue'>답변완료</font>";
		}
    ?>
    <tr class="<?php echo $bg; ?>">
        <td class="td_category"><?php echo $row[com_info]; ?></td>
        <td><a href="./alliance_view.php?no=<?=$row[no]?>"><?php echo $row[name]; ?></a></td>
        <td class="td_category"><?php echo $row[tel]; ?></td>
        <td class="td_category"><?php echo $row[email]; ?></td>
        <td class="td_datetime"><?php echo date("Y.m.d", $row[date]); ?></td>
		<td class="td_datetime"><?php echo $status; ?></td>
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
