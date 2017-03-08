<?php
$sub_menu = '500200';
$sub_sub_menu = '2';

include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$g5['title'] = '상품유형관리';
include_once (G5_ADMIN_PATH.'/admin.head.php');

/*
$sql_search = " where 1 ";
if ($search != "") {
	if ($sel_field != "") {
    	$sql_search .= " and $sel_field like '%$search%' ";
    }
}

if ($sel_ca_id != "") {
    $sql_search .= " and (ca_id like '$sel_ca_id%' or ca_id2 like '$sel_ca_id%' or ca_id3 like '$sel_ca_id%') ";
}

if ($sel_field == "")  $sel_field = "it_name";
*/

$where = " where ";
$sql_search = "";
if ($stx != "") {
    if ($sfl != "") {
        $sql_search .= " and $sfl like '%$stx%' ";
    }
    if ($save_stx != $stx)
        $page = 1;
}

if ($sca != "") {
    //$sql_search .= " $where (ca_id like '$sca%' or ca_id2 like '$sca%' or ca_id3 like '$sca%') ";
	$sql_search .= " and (ca_id like '$sca%') ";
}

if ($sfl == "")  $sfl = "gp_name";

if($stype || $stype == "0"){
	$sql_search .= " and it_type='$stype' ";
}

if (!$sst)  {
    $sst  = "gp_id";
    $sod = "desc";
}
$sql_order = "order by $sst $sod";

$sql_common = "  from {$g5['g5_shop_group_purchase_table']} where 1 ";
$sql_common .= $sql_search;

// 테이블의 전체 레코드수만 얻음
$sql = " select count(*) as cnt " . $sql_common;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql  = " select gp_id,
                 gp_name,
                 it_type
          $sql_common
          $sql_order
          limit $from_record, $rows ";
		
$result = sql_query($sql);

//$qstr1 = 'sel_ca_id='.$sel_ca_id.'&amp;sel_field='.$sel_field.'&amp;search='.$search;
//$qstr  = $qstr1.'&amp;sort1='.$sort1.'&amp;sort2='.$sort2.'&amp;page='.$page;
$qstr  = $qstr.'&amp;sca='.$sca.'&amp;page='.$page.'&amp;save_stx='.$stx;

$listall = '<a href="'.$_SERVER['PHP_SELF'].'" class="ov_listall">전체목록</a>';
?>

<style>
.type_bn{cursor:pointer;}
</style>

<div class="local_ov01 local_ov">
    <?php echo $listall; ?>
    전체 상품 <?php echo $total_count; ?>개
</div>

<form name="flist" class="local_sch01 local_sch">
<input type="hidden" name="doc" value="<?php echo $doc; ?>">
<input type="hidden" name="sort1" value="<?php echo $sort1; ?>">
<input type="hidden" name="sort2" value="<?php echo $sort2; ?>">
<input type="hidden" name="page" value="<?php echo $page; ?>">

<label for="sca" class="sound_only">분류선택</label>
<select name="sca" id="sca">
    <option value="">전체분류</option>
    <?php
    $sql1 = " SELECT	ca_id,
 											ca_name
							FROM		g5_shop_category
  						WHERE		ca_id LIKE 'CT%'
  						ORDER BY ca_id ";
    $result1 = sql_query($sql1);
    for ($i=0; $row1=sql_fetch_array($result1); $i++) {
        $len = strlen($row1['ca_id']) / 2 - 1;
        $nbsp = "";
        for ($i=0; $i<$len; $i++) $nbsp .= "&nbsp;&nbsp;&nbsp;";
        echo '<option value="'.$row1['ca_id'].'" '.get_selected($sca, $row1['ca_id']).'>'.$nbsp.$row1['ca_name'].PHP_EOL;
    }
    ?>
</select>

<label for="sfl" class="sound_only">검색대상</label>
<select name="sfl" id="sfl">
    <option value="gp_name" <?php echo get_selected($sfl, 'gp_name'); ?>>상품명</option>
    <option value="gp_id" <?php echo get_selected($sfl, 'gp_id'); ?>>상품코드</option>
</select>

<select name="stype" id="stype">
	<option value="">상품유형</option>
	<option value="0" <?if($stype == "0"){echo "selected";}?>>선택없음</option>
	<?
	$icon_tit_res = sql_query("
		select * from {$g5['g5_gp_item_type_icon_table']}
		order by no asc
	");

	for($i = 0; $icon_tit_row = mysql_fetch_array($icon_tit_res); $i++){
	?>
		<option value="<?=$icon_tit_row[no]?>" <?if($stype == $icon_tit_row[no]){echo "selected";}?>><?=$icon_tit_row[tp_name]?></option>
	<?
	}
	?>
</select>

<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="stx" value="<?php echo $stx; ?>" id="stx" class="frm_input">
<input type="submit" value="검색" class="btn_submit">

</form>

<form name="fitemtypelist" method="post" action="./gpitemtypelistupdate.php">
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
        <th scope="col"><?php echo subject_sort_link("gp_id", $qstr, 1); ?>상품코드</a></th>
        <th scope="col"><?php echo subject_sort_link("gp_name"); ?>상품명</a></th>

		<th scope="col" class="type_bn" val="0">선택없음</th>
		<?
		$icon_tit_res = sql_query("
			select * from {$g5['g5_gp_item_type_icon_table']}
			order by no asc
		");

		for($i = 0; $icon_tit_row = mysql_fetch_array($icon_tit_res); $i++){
		?>
		<th scope="col" class="type_bn" val="<?=$icon_tit_row[no]?>"><?=$icon_tit_row[tp_name]?></th>
		<?
		}
		?>
        
		<!--<th scope="col"><?php echo subject_sort_link("it_type1", $qstr, 1); ?>히트<br>상품</a></th>
        <th scope="col"><?php echo subject_sort_link("it_type2", $qstr, 1); ?>추천<br>상품</a></th>
        <th scope="col"><?php echo subject_sort_link("it_type3", $qstr, 1); ?>신규<br>상품</a></th>
        <th scope="col"><?php echo subject_sort_link("it_type4", $qstr, 1); ?>인기<br>상품</a></th>
        <th scope="col"><?php echo subject_sort_link("it_type5", $qstr, 1); ?>할인<br>상품</a></th>-->
        <th scope="col">관리</th>
    </tr>
    </thead>
    <tbody>
    <?php for ($i=0; $row=sql_fetch_array($result); $i++) {
        $href = G5_SHOP_URL.'/grouppurchase.php?gp_id='.$row['gp_id'];

        $bg = 'bg'.($i%2);
    ?>
    <tr class="<?php echo $bg; ?>">
        <td class="td_code">
            <input type="hidden" name="gp_id[<?php echo $i; ?>]" value="<?php echo $row['gp_id']; ?>">
            <?php echo $row['gp_id']; ?>
        </td>
        <td><a href="<?php echo $href; ?>"><?php echo get_it_image($row['gp_id'], 50, 50); ?><?php echo cut_str(stripslashes($row['gp_name']), 60, "&#133"); ?></a></td>

		<td class="td_chk"><input type="radio" name="it_type<?php echo $i; ?>" class="type_0" value="0" <?php echo ($row['it_type'] == "0" ? 'checked' : ''); ?>></td>
		<?
		$icon_res = sql_query("
			select * from {$g5['g5_gp_item_type_icon_table']}
			order by no asc
		");

		for($k = 0; $icon_row = mysql_fetch_array($icon_res); $k++){
		?>
        <td class="td_chk"><input type="radio" name="it_type<?php echo $i; ?>" class="type_<?=$icon_row[no]?>" value="<?=$icon_row[no]?>" <?php echo ($row['it_type'] == $icon_row[no] ? 'checked' : ''); ?>></td>
		<?
		}
		?>

        <!--<td class="td_chk"><input type="checkbox" name="it_type2[<?php echo $i; ?>]" value="1" <?php echo ($row['it_type2'] ? 'checked' : ''); ?>></td>
        <td class="td_chk"><input type="checkbox" name="it_type3[<?php echo $i; ?>]" value="1" <?php echo ($row['it_type3'] ? 'checked' : ''); ?>></td>
        <td class="td_chk"><input type="checkbox" name="it_type4[<?php echo $i; ?>]" value="1" <?php echo ($row['it_type4'] ? 'checked' : ''); ?>></td>
        <td class="td_chk"><input type="checkbox" name="it_type5[<?php echo $i; ?>]" value="1" <?php echo ($row['it_type5'] ? 'checked' : ''); ?>></td>-->
        <td class="td_mngsmall">
            <a href="./grouppurchaseform.php?w=u&amp;gp_id=<?php echo $row['gp_id']; ?>&amp;ca_id=<?php echo $row['ca_id']; ?>&amp;<?php echo $qstr; ?>"><span class="sound_only"><?php echo cut_str(stripslashes($row['gp_name']), 60, "&#133"); ?> </span>수정</a>
         </td>
    </tr>
    <?php
    }

    if (!$i)
        echo '<tr><td colspan="8" class="empty_table"><span>자료가 없습니다.</span></td></tr>';
    ?>
    </tbody>
    </table>
</div>

<div class="btn_confirm03 btn_confirm">
    <input type="submit" value="일괄수정" class="btn_submit">
</div>
</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['PHP_SELF']}?$qstr&amp;page="); ?>


<script type="text/javascript">

$(document).ready(function(){
	$(".type_bn").click(function(){
		var val = $(this).attr("val");
		$(".type_"+val).attr("checked", true);
	});
});

</script>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
