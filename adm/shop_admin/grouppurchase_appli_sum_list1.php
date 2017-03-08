<?php
$sub_menu = '500300';
$sub_sub_menu = '3';

include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$g5['title'] = '공동구매신청합계';
include_once (G5_ADMIN_PATH.'/admin.head.php');

// 분류
$ca_list  = '<option value="">선택</option>'.PHP_EOL;
$sql = " select * from {$g5['g5_shop_category_table']} ";
if ($is_admin != 'super')
    $sql .= " where ca_mb_id = '{$member['mb_id']}' ";
$sql .= " order by ca_id ";
$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++)
{
    $len = strlen($row['ca_id']) / 2 - 1;
    $nbsp = '';
    for ($i=0; $i<$len; $i++) {
        $nbsp .= '&nbsp;&nbsp;&nbsp;';
    }
    $ca_list .= '<option value="'.$row['ca_id'].'">'.$nbsp.$row['ca_name'].'</option>'.PHP_EOL;
}


$where = " and ";
$sql_search = "";
if ($stx != "") {
    if ($sfl != "") {
        $sql_search .= " $where a.$sfl like '%$stx%' ";
        $where = " and ";
    }
    if ($save_stx != $stx)
        $page = 1;
}

if ($sca != "") {
    $sql_search .= " $where (b.ca_id like '$sca%') ";
}


// 상태분류
if($ct_status){
	$sql_search .= " and ct_status='$ct_status' ";
}

// 테이블의 전체 레코드수만 얻음
$sql = "select count(DISTINCT gp_site) as cnt from
			{$g5['g5_shop_cart_table']} a,
			{$g5['g5_shop_group_purchase_table']} b
		where a.it_id=b.gp_id
		and a.ct_type != ''
		and ct_status in('쇼핑', '입금대기')
		$sql_search
		";


$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

if (!$sst) {
    $sst  = "ct_id";
    $sod = "desc";
}
$sql_order = "order by $sst $sod";

$sql = "
		select * from
			{$g5['g5_shop_cart_table']} a
		where a.ct_gubun='P'
		and ct_status in('쇼핑', '입금대기')
		$sql_search
		group by total_amount_code
		order by ct_id desc
		limit $from_record, $rows
		";

$result = sql_query($sql);

//$qstr  = $qstr.'&amp;sca='.$sca.'&amp;page='.$page;
$qstr  = $qstr.'&amp;sca='.$sca.'&amp;page='.$page.'&amp;save_stx='.$stx;

$listall = '<a href="'.$_SERVER['PHP_SELF'].'" class="ov_listall">전체목록</a>';
?>

<div class="local_ov01 local_ov">
    <?php echo $listall; ?>
    등록된 상품 <?php echo $total_count; ?>건
</div>

<form name="flist" class="local_sch01 local_sch" METHOD="POST">
<input type="hidden" name="page" value="<?php echo $page; ?>">
<input type="hidden" name="save_stx" value="<?php echo $stx; ?>">

<!--
<label for="ct_status" class="sound_only">상태선택</label>
<select name="ct_status" id="ct_status">
	<option value="">상태분류</option>
	<option value="n" <?//if($ct_status == "n"){echo "selected";}?>>신청대기</option>
	<option value="c" <?//if($ct_status == "c"){echo "selected";}?>>입금대기</option>
	<option value="y" <?//if($ct_status == "y"){echo "selected";}?>>완료</option>
</select>
-->

<label for="sca" class="sound_only">분류선택</label>
<select name="sca" id="sca">
    <option value="">전체분류</option>
    <?php
    $sql1 = " select ca_id, ca_name from {$g5['g5_shop_category_table']} order by ca_id ";
    $result1 = sql_query($sql1);
    for ($i=0; $row1=sql_fetch_array($result1); $i++) {
        $len = strlen($row1['ca_id']) / 2 - 1;
        $nbsp = '';
        for ($i=0; $i<$len; $i++) $nbsp .= '&nbsp;&nbsp;&nbsp;';
        echo '<option value="'.$row1['ca_id'].'" '.get_selected($sca, $row1['ca_id']).'>'.$nbsp.$row1['ca_name'].'</option>'.PHP_EOL;
    }
    ?>
</select>

<label for="sfl" class="sound_only">검색대상</label>
<select name="sfl" id="sfl">
    <option value="it_name" <?php echo get_selected($sfl, 'it_name'); ?>>상품명</option>
    <option value="total_amount_code" <?php echo get_selected($sfl, 'total_amount_code'); ?>>공동구매코드</option>
</select>

<label for="stx" class="sound_only">검색어</label>
<input type="text" name="stx" value="<?php echo $stx; ?>" id="stx" class="frm_input">
<input type="submit" value="검색" class="btn_submit">

</form>

<form name="fitemlistupdate" method="post" action="./grouppurchase_appli_sum_listupdate.php" autocomplete="off">
<input type="hidden" name="sca" value="<?php echo $sca; ?>">
<input type="hidden" name="sst" value="<?php echo $sst; ?>">
<input type="hidden" name="sod" value="<?php echo $sod; ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl; ?>">
<input type="hidden" name="stx" value="<?php echo $stx; ?>">
<input type="hidden" name="ct_status" value="<?php echo $ct_status; ?>">
<input type="hidden" name="page" value="<?php echo $page; ?>">
<input type="hidden" name="mode">
<input type="hidden" name="URL" value="<?=basename($PHP_SELF)?>">

<div class="btn_add01 btn_add">

	<div style="clear:both;">
		<div class="btn_list01 btn_list" style="float:left;">
			<input type="button" name="act_button" value="선택수정">
			<?php if ($is_admin == 'super') { ?>
			<input type="button" name="act_button" value="선택삭제">
			<?php } ?>
			/
			<select name="all_status_chg" id="all_status_chg" style="padding:10px 5px 10px 5px;">
				<option value="쇼핑">신청대기</option>
				<option value="입금대기">입금대기</option>
			</select>
			<input type="button" name="act_button" value="전체상태변경">
		</div>

		<div style="float:right;">
			<a href="#" style="background:#ff3061;color:#fff;" class="act_button" value="전체삭제">전체삭제</a>
			<a href="#" class="go_excel">상품다운로드</a>
		</div>
	</div>
</div>

<div class="tbl_head02 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
		<th scope="col" rowspan="3">
            <label for="chkall" class="sound_only">상품 전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col">공동구매 코드</th>
		<th scope="col" width="100px">브랜드</th>
		<th scope="col" id="th_img" width="100px">이미지</th>
        <th scope="col" id="th_pc_title">상품명</th>
        <th scope="col" width="70px">신청일자</th>
        <th scope="col" width="70px">진행상태</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=mysql_fetch_array($result); $i++)
    {
        $href = G5_SHOP_URL.'/grouppurchase.php?gp_id='.$row['it_id'];
        $bg = 'bg'.($i%2);

		if($row[ct_type] == "2010"){
			$ct_type = "APMEX";
		}else if($row[ct_type] == "2020"){
			$ct_type = "GAINSVILLE";
		}else if($row[ct_type] == "2030"){
			$ct_type = "MCM";
		}else if($row[ct_type] == "2040"){
			$ct_type = "SCOTTS DALE";
		}else{
			$ct_type = "OTHER DEALER";
		}

		$image = get_gp_image($row['it_id'], 70, 70);

		$gp = sql_fetch("select * from {$g5['g5_shop_group_purchase_table']} where gp_id='".$row[it_id]."' ");

		$cart_cnt = sql_fetch("select count(*) as cnt from {$g5['g5_shop_cart_table']} where total_amount_code='".$row[total_amount_code]."' and ct_status in('쇼핑', '입금대기') ");
		$cart_cnt = $cart_cnt[cnt] - 1;
		if($cart_cnt > 0){
			$cart_cnt = "외 ". $cart_cnt."개";
		}else{
			$cart_cnt = "";
		}

		$ct_dt = $row[ct_time];
    ?>
    <tr class="<?php echo $bg; ?>">
		<td class="td_chk">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo get_text($row['bo_subject']) ?> 게시판</label>
            <input type="checkbox" name="chk[]" value="<?php echo $i; ?>" id="chk_<?php echo $i; ?>">
			<input type="hidden" name="gp_id[<?php echo $i; ?>]" value="<?php echo $row['it_id']; ?>">
			<input type="hidden" name="total_amount_code[<?php echo $i; ?>]" value="<?php echo $row['total_amount_code']; ?>">
        </td>
        <td class="td_num">
			<?php echo $row['total_amount_code']; ?>
        </td>
		<td class="td_num">
            <?php echo $ct_type; ?>
        </td>
        <td class="td_img">
            <?=$image?>
        </td>
		<td>
			<span><a href="<?php echo $href; ?>"><?=cut_str($row[it_name], 80)?></a></span>
			<span style="padding:0 0 0 5px;">(URL : <a href="<?=$gp[gp_site]?>" target="_blank"><?=cut_str($gp[gp_site], 50)?></a>) <?=$cart_cnt?></span>
			<span style="color:blue;cursor:pointer;">
				<a href="./grouppurchase_appli_sum_view.php?gp_code=<?=$row[total_amount_code]?>">상세보기</a>
			</span>
		</td>
		<td class="td_num">
			<?=date("Y-m-d", strtotime($ct_dt))?></br>
			<?=date("H:i:s", strtotime($ct_dt))?>
		</td>
        <td class="td_mngsmall go_status" ct_id="<?=$row[ct_id]?>">
			<select name="gp_status[<?php echo $i; ?>]" id="gp_status">
				<option value="쇼핑" <?if($row[ct_status] == "쇼핑" || $row[ct_gp_status] == ""){echo "selected";}?>>신청대기</option>
				<option value="입금대기" <?if($row[ct_status] == "입금대기"){echo "selected";}?>>입금대기</option>
			</select>
			<?//if($row[ct_status] == "쇼핑" || $row[ct_status] == ""){echo "신청대기";}?>
			<?//if($row[ct_status] == "입금대기"){echo "입금대기";}?>
			<?//if($row[ct_status] == "결제완료"){echo "결제완료";}?>
		</td>
    </tr>
    <?php
    }
    if ($i == 0)
        echo '<tr><td colspan="11" class="empty_table">자료가 한건도 없습니다.</td></tr>';
    ?>
    </tbody>
    </table>
</div>


<!--
<div class="btn_list01 btn_list">
    <input type="button" name="act_button" value="선택수정">
    <?php// if ($is_admin == 'super') { ?>
    <input type="button" name="act_button" value="선택삭제">
    <?php// } ?>
	/
	<select name="all_status_chg" id="all_status_chg" style="padding:10px 5px 10px 5px;">
		<option value="n">신청대기</option>
		<option value="c">입금대기</option>
		<option value="y">확인완료</option>
	</select>
	<input type="button" name="act_button" value="전체상태변경">
</div>
-->

<!-- <div class="btn_confirm01 btn_confirm">
    <input type="submit" value="일괄수정" class="btn_submit" accesskey="s">
</div> -->
</form>

<form name="fitemlistdel" id="fitemlistdel" method="POST">
<input type="hidden" name="HTT_CHK" value="CHK_OK">
</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['PHP_SELF']}?$qstr&amp;page="); ?>

<script type="text/javascript">

$(document).ready(function(){
	$(".all_del").click(function(){
		var f = document.fitemlistdel;
		if(confirm("상품 전체 삭제 시 복구 불가능합니다.\n정말 삭제 하시겠습니까?")){
			f.action = "./grouppurchase_appli_delall.php";
			f.submit();
		}
	});

	/*
	$(".go_status").click(function(){
		var ct_id = $(this).attr("ct_id");
		var status = $(this).children("font").text();

		if($(this).children("font").text() == "취소"){
			$(this).html("<font color='blue'>확인완료</font>");
		}else{
			$(this).html("<font color='red'>취소</font");
		}

		$.ajax({
			type : "POST",
			dataType : "HTML",
			url : "./_Ajax.grouppurchase_appli_list.php",
			data : "ct_id=" + ct_id + "&status=" + status,
			success : function(data){
				alert("정상적으로 처리 되었습니다.");
			}
		});
		
	});
	*/

	$("input[name='act_button']").click(function(){

		if($(this).val() != "전체상태변경"){
			if (!is_checked("chk[]")) {
				alert($(this).val()+" 하실 항목을 하나 이상 선택하세요.");
				return false;
			}
		}

		if($(this).val() == "선택삭제") {
			if(!confirm("선택한 자료를 정말 삭제하시겠습니까?")) {
				return false;
			}
			$("input[name='mode']").val("del");
		}else if($(this).val() == "전체상태변경"){
			if(!confirm("검색한 모든 자료를 변경하시겠습니까?")) {
				return false;
			}
			$("input[name='mode']").val("all_chg");
		}else{
			$("input[name='mode']").val("modify");
		}

		$("form[name='fitemlistupdate']").submit();
	});

	$(".act_button").click(function(){
		if(!confirm("전체 자료를 정말 삭제하시겠습니까?")) {
			return false;
		}
		$("input[name='mode']").val("all_del");
		$("form[name='fitemlistupdate']").submit();
	});

	$(".go_excel").click(function(){
		$("form[name='fitemlistupdate']").attr("action", "./grouppurchase_appli_sum_excel.php").submit();
	});

});

function excelform(url)
{
    var opt = "width=600,height=450,left=10,top=10";
    window.open(url, "win_excel", opt);
    return false;
}
</script>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
