<?php
$sub_menu = "800010";
$sub_sub_menu = "1";

include_once('./_common.php');

auth_check($auth[$sub_menu], 'r');

$sql_common = " from {$g5['g5_cus_slide_table']} ";

$sql_search = " where (1) ";
if ($stx) {
    $sql_search .= " and ( ";
    switch ($sfl) {
        case 'mb_point' :
            $sql_search .= " ({$sfl} >= '{$stx}') ";
            break;
        case 'mb_level' :
            $sql_search .= " ({$sfl} = '{$stx}') ";
            break;
        case 'mb_tel' :
        case 'mb_hp' :
            $sql_search .= " ({$sfl} like '%{$stx}') ";
            break;
        default :
            $sql_search .= " ({$sfl} like '{$stx}%') ";
            break;
    }
    $sql_search .= " ) ";
}

$sql_order = " order by no desc ";

$sql = " select count(*) as cnt {$sql_common} {$sql_search} {$sql_order} ";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if (!$page) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함


$g5['title'] = 'Customer 슬라이드 관리';
include_once('./admin.head.php');

$sql = " select * {$sql_common} {$sql_search} {$sql_order} limit {$from_record}, {$rows} ";
$result = sql_query($sql);

?>

<?php if ($is_admin == 'super') { ?>
<div class="btn_add01 btn_add">
    <a href="./customer_slide_write.php" id="customer_slide_add">등록</a>
</div>
<?php } ?>

<form name="fcustomer_slide" id="fcustomer_slide" action="./customer_slide_update.php" onsubmit="return fcustomer_slide_submit(this);" method="post">
<input type="hidden" name="sst" value="<?php echo $sst ?>">
<input type="hidden" name="sod" value="<?php echo $sod ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
<input type="hidden" name="stx" value="<?php echo $stx ?>">
<input type="hidden" name="page" value="<?php echo $page ?>">

<div class="tbl_head02 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        
        <th scope="col" width="10%">No</th>
		<th scope="col">파일명</th>
		<th scope="col" width="10%">날짜</th>
		<th scope="col" width="10%">관리</th>
        
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        
    ?>

    <tr class="<?php echo $bg; ?>">
        
        <td align="center"><?php echo $row[no] ?></td>
		<td class="td_name sv_use"><?php echo $row[img_file] ?></td>
		<td align="center"><?php echo date("Y-m-d", $row[date]) ?></td>
		<td align="center">
			<input type="button" value="수정" class="modify" no="<?=$row[no]?>">
			<input type="button" value="삭제" class="del" no="<?=$row[no]?>">
		</td>
        
    </tr>

    <?php
    }
    if ($i == 0)
        echo "<tr><td colspan='4' class=\"empty_table\">자료가 없습니다.</td></tr>";
    ?>
    </tbody>
    </table>
</div>

</form>

<form name="del_form" method="POST">
<input type="hidden" name="HTTP_CHK" value="CHK_OK">
<input type="hidden" name="mode" value="d">
<input type="hidden" name="no">
</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page='); ?>

<script type="text/javascript">

$(document).ready(function(){
	$(".modify").click(function(){
		var no = $(this).attr("no");
		location.href = "./customer_slide_write.php?no=" + no;
	});

	$(".del").click(function(){

		if(confirm("정말 삭제하시겠습니까?")){
			var no = $(this).attr("no");
			$("form[name='del_form']").find("input[name='no']").val(no);
			$("form[name='del_form']").attr("action", "./customer_slide_update.php").submit();
		}
	});
});

</script>

<?php
include_once ('./admin.tail.php');
?>
