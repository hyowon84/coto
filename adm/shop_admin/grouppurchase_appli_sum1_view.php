<?php
$sub_menu = '500300';
$sub_sub_menu = '3';

include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$g5['title'] = '공동구매신청합계 상세보기';
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
		$sql_search .= " $where (a.$sfl like REPLACE(REPLACE('%".$stx."%', '£', '&#163;'), '&', '&amp;') or a.$sfl like '%".$stx."%') ";
        $where = " and ";
    }
    if ($save_stx != $stx)
        $page = 1;
}

if($sfl_code2 != ""){
	$sql_search .= " and total_amount_code='".$sfl_code2."' ";
}

if ($sca != "") {
    $sql_search .= " $where (b.ca_id like '$sca%') ";
}


// 상태분류
if($ct_status){
	$sql_search .= " and ct_status='$ct_status' ";
}

if($gp_dt_f){
	$sql_search .= " and ct_time > '".date("Y-m-d H:i:s", strtotime($gp_dt_f))."' ";
}

if($gp_dt_l){
	$sql_search .= " and ct_time < '".date("Y-m-d", strtotime($gp_dt_l))." 59:59:59' ";
}






$sql_search .= " and mb_id='$mb_id' ";

// 테이블의 전체 레코드수만 얻음
$sql = " select count(*) as cnt
		   from {$g5['g5_shop_cart_table']} a left join {$g5['g5_shop_group_purchase_table']} b on ( a.it_id = b.gp_id )
		  where a.ct_type != '' $sql_search order by a.ct_time desc";

$res = sql_query($sql);
$row['cnt'] = mysql_num_rows($res);
$total_count = $row['cnt'];

$sql = " select SUM(ct_price * ct_qty) as total_price
		   from {$g5['g5_shop_cart_table']} a left join {$g5['g5_shop_group_purchase_table']} b on ( a.it_id = b.gp_id )
		  where a.ct_type != '' $sql_search order by a.ct_id desc";

$total_price = sql_fetch($sql);

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

if (!$sst) {
    $sst  = "ct_id";
    $sod = "desc";
}
$sql_order = "order by $sst $sod";

$sql = " select a.*
		   from {$g5['g5_shop_cart_table']} a left join {$g5['g5_shop_group_purchase_table']} b on ( a.it_id = b.gp_id )
		  where a.ct_type != '' $sql_search order by a.ct_time desc limit $from_record, $rows ";
		  

$result = sql_query($sql);

$mem = sql_fetch("select * from {$g5['member_table']} where mb_id='".$mb_id."'");

//$qstr  = $qstr.'&amp;sca='.$sca.'&amp;page='.$page;
$qstr  = $qstr.'&amp;sca='.$sca.'&amp;page='.$page.'&amp;save_stx='.$stx;

$listall = '<a href="'.$_SERVER['PHP_SELF'].'" class="ov_listall">전체목록</a>';
?>

<div class="local_ov01 local_ov">
    <?php echo $listall; ?>
    등록된 상품 <?php echo $total_count; ?>건
</div>
<div class="test"></div>
<form name="flist" class="local_sch01 local_sch" style="line-height:3;">
<input type="hidden" name="page" value="<?php echo $page; ?>">
<input type="hidden" name="save_stx" value="<?php echo $stx; ?>">
<input type="hidden" name="mb_id" value="<?php echo $mb_id; ?>">

<label for="ct_status" class="sound_only">상태선택</label>
<select name="ct_status" id="ct_status">
	<option value="">상태분류</option>
	<option value="쇼핑" <?if($ct_status == "쇼핑"){echo "selected";}?>>신청대기</option>
	<option value="입금대기" <?if($ct_status == "입금대기"){echo "selected";}?>>입금대기</option>
	<!--<option value="결제완료" <?if($ct_status == "결제완료"){echo "selected";}?>>결제완료</option>-->
</select>

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
기간 : 
<input type="text" name="gp_dt_f" id="gp_dt_f" class="frm_input" style="width:70px;" value="<?=$gp_dt_f?>"> ~
<input type="text" name="gp_dt_l" id="gp_dt_l" class="frm_input" style="width:70px;" value="<?=$gp_dt_l?>">

<select name="sfl" id="sfl">
    <option value="it_name" <?php echo get_selected($sfl, 'it_name'); ?>>상품명</option>
</select>

<label for="stx" class="sound_only">검색어</label>
<input type="text" name="stx" value="<?php echo $stx; ?>" id="stx" class="frm_input">
<input type="submit" value="검색" class="btn_submit">

</br>

공동구매 코드 검색 : 
<select name="sfl_code">
	<option value="">1차 분류</option>
	<option value="2010" <?if($sfl_code == "2010"){echo "selected";}?>>APMEX</option>
	<option value="2020" <?if($sfl_code == "2020"){echo "selected";}?>>GAINSVILLE</option>
	<option value="2030" <?if($sfl_code == "2030"){echo "selected";}?>>MCM</option>
	<option value="2040" <?if($sfl_code == "2040"){echo "selected";}?>>SCOTTS DALE</option>
	<option value="2050" <?if($sfl_code == "2050"){echo "selected";}?>>OTHER DEALER</option>
</select>


<select name="sfl_code2">
	<option value="">2차 분류</option>
</select>

</form>

<form name="fitemlistupdate" method="post" action="./grouppurchase_appli_listupdate.php" autocomplete="off">
<input type="hidden" name="URL" value="<?=basename($PHP_SELF)?>">
<input type="hidden" name="gp_code" value="<?=$gp_code?>">

<div class="btn_add01 btn_add">
	

	<div style="clear:both;">

		<div style="float:left;padding:0 0 0 20px;">
			공동구매 총 합계 : <?=number_format($total_price[total_price])?> 원
		</div>

		<!--<div style="float:right;">
			<a href="#" class="go_excel">상품다운로드</a>
		</div>-->
	</div>
</div>


<input type="hidden" name="sca" value="<?php echo $sca; ?>">
<input type="hidden" name="sst" value="<?php echo $sst; ?>">
<input type="hidden" name="sod" value="<?php echo $sod; ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl; ?>">
<input type="hidden" name="stx" value="<?php echo $stx; ?>">
<input type="hidden" name="ct_status" value="<?php echo $ct_status; ?>">
<input type="hidden" name="page" value="<?php echo $page; ?>">
<input type="hidden" name="mode">
<input type="hidden" name="page" value="<?=$page?>">
<input type="hidden" name="save_stx" value="<?=$save_stx?>">
<input type="hidden" name="gp_dt_f" value="<?=$gp_dt_f?>">
<input type="hidden" name="gp_dt_l" value="<?=$gp_dt_l?>">
<input type="hidden" name="sfl_code" value="<?=$sfl_code?>">
<input type="hidden" name="sfl_code2" value="<?=$sfl_code2?>">
<input type="hidden" name="mb_id" value="<?php echo $mb_id; ?>">

<div class="tbl_head02 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
	<tr>
		<th colspan="8" style="text-align:center;"><span></span>"<?=$mem[mb_nick]?>"님 공동구매 신청합계 상세보기</th>
	</tr>
    <tr>
        <th scope="col">닉네임</a></th>
		<th scope="col" width="100px">브랜드</th>
		<th scope="col" id="th_img" width="100px">이미지</th>
        <th scope="col" id="th_pc_title">상품명</th>
		<th scope="col" width="100px">단가($)</th>
		<th scope="col" width="100px">단가(\)</th>
        <th scope="col" width="120px">수량</th>
		<th scope="col" width="100px">총 금액</th>
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

		$gp_row = sql_fetch("select * from {$g5['g5_shop_group_purchase_table']} where gp_id='".$row[it_id]."' ");

		$member_row = sql_fetch("select * from {$g5['member_table']} where mb_id='".$row[mb_id]."' ");

		if($row[ct_buy_qty]){
			$ct_buy_qty = $row[ct_buy_qty];
		}else{
			$ct_buy_qty = 0;
		}

		//옵션 상품
		$op_arr = explode("|", $row[ct_op_option]);
		$op_price = 0;
		for($b = 0; $b < count($op_arr); $b++){
			if($op_arr[$b]){
				$op_row = sql_fetch("select * from {$g5['g5_shop_option2_table']} where it_id='".$row[it_id]."' and con='".$op_arr[$b]."' ");
				$op_price = $op_price + $op_row[price];
			}
		}

    ?>
    <tr class="<?php echo $bg; ?>">
        <td class="td_mngsmall"><?=$member_row[mb_nick]?></td>
		<td class="td_num">
            <?php echo $ct_type; ?>
        </td>
        <td class="td_img">
            <?=$image?>
        </td>
		<td>
			<span><a href="<?php echo $href; ?>"><?=cut_str($row[it_name], 80)?></a></span>
			<span style="padding:0 0 0 5px;">(URL : <a href="<?=$gp_row[gp_site]?>" target="_blank"><?=cut_str($gp_row[gp_site], 50)?></a>)</span>
		</td>
		<td class="td_mngsmall">$ <?=getGroupPurchaseQtyBasicUSD($gp_row[gp_id],1)?></td>
		<td class="td_mngsmall"><?=number_format(ceil($row[ct_price] * 1 / 100) * 100)?> 원</td>
		<td class="td_mngsmall">
			<?=$row[ct_qty]?>개 중</br>
			<?=$ct_buy_qty?>개 판매불가
		</td>
		<td class="td_mngsmall"><?=number_format(ceil($row[ct_price] * $row[ct_qty] / 100) * 100 + $op_price)?> 원</td>
    </tr>
    <?php
    }
    if ($i == 0)
        echo '<tr><td colspan="11" class="empty_table">자료가 한건도 없습니다.</td></tr>';
    ?>
    </tbody>
    </table>
</div>


<!-- <div class="btn_confirm01 btn_confirm">
    <input type="submit" value="일괄수정" class="btn_submit" accesskey="s">
</div> -->
</form>

<form name="fitemlistdel" id="fitemlistdel" method="POST">
<input type="hidden" name="HTT_CHK" value="CHK_OK">
</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['PHP_SELF']}?$qstr&amp;page="); ?>


<link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/themes/base/jquery-ui.css" rel="stylesheet" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/jquery-ui.min.js"></script>
<script type="text/javascript">

jQuery(function($){
	$.datepicker.regional['ko'] = {
		closeText: '닫기',
		prevText: '이전달',
		nextText: '다음달',
		currentText: '오늘',
		monthNames: ['1월(JAN)','2월(FEB)','3월(MAR)','4월(APR)','5월(MAY)','6월(JUN)',
		'7월(JUL)','8월(AUG)','9월(SEP)','10월(OCT)','11월(NOV)','12월(DEC)'],
		monthNamesShort: ['1월','2월','3월','4월','5월','6월',
		'7월','8월','9월','10월','11월','12월'],
		dayNames: ['일','월','화','수','목','금','토'],
		dayNamesShort: ['일','월','화','수','목','금','토'],
		dayNamesMin: ['일','월','화','수','목','금','토'],
		weekHeader: 'Wk',
		dateFormat: 'yymmdd',
		firstDay: 0,
		isRTL: false,
		showMonthAfterYear: true,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['ko']);

    $('#gp_dt_f').datepicker({
        showOn: 'button',
		buttonImage: '../../img/calendar.gif',
		buttonImageOnly: true,
        buttonText: "달력",
        changeMonth: true,
		changeYear: true,
        showButtonPanel: true,
        yearRange: 'c-99:c+99',
        maxDate: '+0d'
    }); 

	$('#gp_dt_l').datepicker({
        showOn: 'button',
		buttonImage: '../../img/calendar.gif',
		buttonImageOnly: true,
        buttonText: "달력",
        changeMonth: true,
		changeYear: true,
        showButtonPanel: true,
        yearRange: 'c-99:c+99',
        maxDate: '+0d'
    }); 
});
</script>

<script type="text/javascript">

$(document).ready(function(){

	<?if($sfl_code){?>
	$.ajax({
		type: "POST",
		dataType: "HTML",
		url: "./_Ajax.grouppurchase_appli_list.php",
		data: "status=gpcode_status&gp_code=<?=$sfl_code?>&sfl_code2=<?=$sfl_code2?>",
		success: function(data){
			//$(".test").html(data);
			$("select[name='sfl_code2']").html(data);
		}
	});
	<?}?>

	$(".all_del").click(function(){
		var f = document.fitemlistdel;
		if(confirm("상품 전체 삭제 시 복구 불가능합니다.\n정말 삭제 하시겠습니까?")){
			f.action = "./grouppurchase_appli_delall.php";
			f.submit();
		}
	});

	$("select[name='sfl_code']").change(function(){
		var gp_code = $(this).val();
		$.ajax({
			type: "POST",
			dataType: "HTML",
			url: "./_Ajax.grouppurchase_appli_list.php",
			data: "status=gpcode_status&gp_code=" + gp_code,
			success: function(data){
				//$(".test").html(data);
				$("select[name='sfl_code2']").html(data);
			}
		});
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

	$(".buy_status").click(function(){
		var idx = $(this).attr("idx");
		var num = $(".buy_status").index($(this));

		$.ajax({
			type : "POST",
			dataType : "HTML",
			url : "./_Ajax.grouppurchase_appli_list.php",
			data : "status=buy_status&idx=" + idx,
			success : function(data){
				if(data == "n"){
					$(".buy_status").eq(num).html('<font color="red">품절</font>');
				}else{
					$(".buy_status").eq(num).html('<font color="blue">판매가능</font>');
				}
			}
		});
	});

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
		$("form[name='fitemlistupdate']").attr("action", "./grouppurchase_appli_excel.php").submit();
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
