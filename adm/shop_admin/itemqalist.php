<?php
$sub_menu = '400200';
$sub_sub_menu = '2';

include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$g5['title'] = '상품문의';
include_once (G5_ADMIN_PATH.'/admin.head.php');

$where = " where ";
$sql_search = "";
if ($stx != "") {
    if ($sfl != "") {
        $sql_search .= " $where $sfl like '%$stx%' ";
        $where = " and ";
    }
    if ($save_stx != $stx)
        $page = 1;
}

if ($sca != "") {
    $sql_search .= " and ca_id like '$sca%' ";
}

if ($sfl == "")  $sfl = "it_name";
if (!$sst) {
    $sst = "iq_id";
    $sod = "desc";
}

$sql_common = "  from		g5_shop_gpitem_qa a
												LEFT JOIN g5_shop_group_purchase b on (a.gp_id = b.gp_id)
												LEFT JOIN g5_member c on (a.mb_id = c.mb_id)
";
$sql_common .= $sql_search;

// 테이블의 전체 레코드수만 얻음
$sql = " select count(*) as cnt " . $sql_common;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql  = " select *
          $sql_common
          order by $sst $sod, iq_id desc
          limit $from_record, $rows ";
$result = sql_query($sql);

echo "<textarea width='1200' height='600'>".$sql."</textarea>";


//$qstr = 'page='.$page.'&amp;sst='.$sst.'&amp;sod='.$sod.'&amp;stx='.$stx;
$qstr  = $qstr.'&amp;sca='.$sca.'&amp;save_stx='.$stx;

$listall = '<a href="'.$_SERVER['PHP_SELF'].'" class="ov_listall">전체목록</a>';
?>

<div class="local_ov01 local_ov">
    <?php echo $listall; ?>
    전체 문의내역 <?php echo $total_count; ?>건
</div>

<form name="flist" class="local_sch01 local_sch">
<input type="hidden" name="page" value="<?php echo $page; ?>">
<input type="hidden" name="save_stx" value="<?php echo $stx; ?>">

<label for="sca" class="sound_only">분류선택</label>
<select name="sca" id="sca">
    <option value="">전체분류</option>
    <?php
    $sql1 = " select ca_id, ca_name from {$g5['g5_shop_category_table']} order by ca_id ";
    $result1 = sql_query($sql1);
    for ($i=0; $row1=mysql_fetch_array($result1); $i++) {
        $len = strlen($row1['ca_id']) / 2 - 1;
        $nbsp = "";
        for ($i=0; $i<$len; $i++) $nbsp .= "&nbsp;&nbsp;&nbsp;";
        echo '<option value="'.$row1['ca_id'].'" '.get_selected($sca, $row1['ca_id']).'>'.$nbsp.$row1['ca_name'].'</option>'.PHP_EOL;
    }
    ?>
</select>

<label for="sfl" class="sound_only">검색대상</label>
<select name="sfl" id="sfl">
    <option value="gp_name" <?php echo get_selected($sfl, 'gp_name'); ?>>상품명</option>
    <option value="a.gp_id" <?php echo get_selected($sfl, 'a.gp_id'); ?>>상품코드</option>
</select>

<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="stx" value="<?php echo $stx; ?>" id="stx" required class="frm_input required">
<input type="submit" value="검색" class="btn_submit">

</form>

<form name="fitemqalist" method="post" action="./itemqalistupdate.php" onsubmit="return fitemqalist_submit(this);" autocomplete="off">
<input type="hidden" name="sca" value="<?php echo $sca; ?>">
<input type="hidden" name="sst" value="<?php echo $sst; ?>">
<input type="hidden" name="sod" value="<?php echo $sod; ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl; ?>">
<input type="hidden" name="stx" value="<?php echo $stx; ?>">
<input type="hidden" name="page" value="<?php echo $page; ?>">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col">
            <label for="chkall" class="sound_only">상품문의 전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col"><?php echo subject_sort_link('gp_name'); ?>상품명</a></th>
        <th scope="col"><?php echo subject_sort_link('iq_subject'); ?>질문</a></th>
        <th scope="col"><?php echo subject_sort_link('mb_name'); ?>이름</a></th>
        <th scope="col"><?php echo subject_sort_link('iq_answer'); ?>답변</a></th>
        <th scope="col">관리</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=mysql_fetch_array($result); $i++) {
        $row['iq_subject'] = cut_str($row['iq_subject'], 30, "...");

        $href = G5_SHOP_URL.'/grouppurchase.php?gp_id='.$row['gp_id'];
        $name = get_sideview($row['mb_id'], $row['iq_name'], $row['mb_email'], $row['mb_homepage']);
        $answer = $row['iq_answer'] ? 'Y' : '&nbsp;';
        $iq_question = get_view_thumbnail($row['iq_question'], 300);
        $iq_answer = $row['iq_answer'] ? get_view_thumbnail($row['iq_answer'], 300) : "답변이 등록되지 않았습니다.";

        $bg = 'bg'.($i%2);
     ?>
    <tr class="<?php echo $bg; ?>">
        <td class="td_chk">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo get_text($row['iq_subject']) ?> 상품문의</label>
            <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i; ?>">
            <input type="hidden" name="iq_id[<?php echo $i; ?>]" value="<?php echo $row['iq_id']; ?>">
        </td>
        <td><a href="<?php echo $href; ?>"><?php echo get_it_image($row['gp_id'], 50, 50); ?> <?php echo cut_str($row['gp_name'],30); ?></a></td>
        <td>
            <a href="#" class="qa_href" onclick="return false;" target="<?php echo $i; ?>"><?php echo $row['iq_subject']; ?></a>
            <div id="qa_div<?php echo $i; ?>" class="qa_div" style="display:none;">
                <strong>문의내용</strong><br>
                <?php echo $iq_question; ?>
                <strong>답변</strong><br>
                <?php echo $iq_answer; ?>
            </div>
        </td>
        <td class="td_name"><?php echo $name; ?></td>
        <td class="td_boolean"><?php echo $answer; ?></td>
        <td class="td_mngsmall">
            <a href="./itemqaform.php?w=u&amp;iq_id=<?php echo $row['iq_id']; ?>&amp;<?php echo $qstr; ?>"><span class="sound_only"><?php echo $row['iq_subject']; ?> </span>수정</a>
        </td>
    </tr>
    <?php
    }
    if ($i == 0) {
        echo '<tr><td colspan="6" class="empty_table"><span>자료가 없습니다.</span></td></tr>';
    }
    ?>
    </tbody>
    </table>
</div>

<div class="btn_list01 btn_list">
    <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value">
</div>
</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['PHP_SELF']}?$qstr&amp;page="); ?>

<script>
function fitemqalist_submit(f)
{
    if (!is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
    }

    if(document.pressed  == "선택삭제") {
        if(!confirm("선택한 자료를 정말 삭제하시겠습니까?")) {
            return false;
        }
    }

    return true;
}

$(function(){
    $(".qa_href").click(function(){
        var $content = $("#qa_div"+$(this).attr("target"));
        $(".qa_div").each(function(index, value){
            if ($(this).get(0) == $content.get(0)) { // 객체의 비교시 .get(0) 를 사용한다.
                $(this).is(":hidden") ? $(this).show() : $(this).hide();
            } else {
                $(this).hide();
            }
        });
    });
});
</script>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
