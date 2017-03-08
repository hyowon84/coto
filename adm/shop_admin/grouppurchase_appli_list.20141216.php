<?php
$sub_menu = '500300';
$sub_sub_menu = '3';

include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$g5['title'] = '공동구매신청관리';
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


$sql_search = "";
if ($search != "") {
    if ($sel_field != "") {
		switch($sel_field){
			case "mb_nick":
				$where[] = " mb_id in ( select mb_id from {$g5['member_table']} where mb_nick like '%".$search."%' ) ";
				break;
			case "ct_wearing_cnt":
				$where[] = " ct_wearing_cnt > 0";
				break;
			default :
		        $where[] = " $sel_field like '%$search%' ";
				break;
		}
    }

    if ($save_search != $search) {
        $page = 1;
    }
}


$where[] = " total_amount_code <> '' ";

if($od_status){
   $where[] = " ct_status = '$od_status' ";
}

if ($fr_date && $to_date) {
    $where[] = " ct_time between '$fr_date 00:00:00' and '$to_date 23:59:59' ";
}


if($sfl_code2 != ""){
	$where[] = " total_amount_code ='".$sfl_code2."' ";
}


if ($where) {
    $sql_search = ' where '.implode(' and ', $where);
}


// 테이블의 전체 레코드수만 얻음
$sql = " select count(*) as cnt
		   from {$g5['g5_shop_cart_table']} a left join {$g5['g5_shop_group_purchase_table']} b on ( a.it_id = b.gp_id )
		  $sql_search order by a.ct_id desc";

$row = sql_fetch($sql);
$total_count = $row['cnt'];

$sql = " select SUM(ct_price * ct_qty) as total_price
		   from {$g5['g5_shop_cart_table']} a left join {$g5['g5_shop_group_purchase_table']} b on ( a.it_id = b.gp_id )
		  $sql_search order by a.ct_id desc";

$total_price = sql_fetch($sql);

$rows = 100;
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
		  $sql_search order by a.ct_id desc limit $from_record, $rows ";

$result = sql_query($sql);

//$qstr  = $qstr.'&amp;sca='.$sca.'&amp;page='.$page;
$qstr1 = "sel_field=$sel_field&amp;sfl_code=$sfl_code&amp;fr_date=$fr_date&amp;to_date=$to_date&amp;sfl_code2=$sfl_code2&amp;od_status=$od_status&amp;search=$search&amp;save_search=$search";
$qstr = "$qstr1&amp;sort1=$sort1&amp;sort2=$sort2&amp;page=$page";

$listall = '<a href="'.$_SERVER['PHP_SELF'].'" class="ov_listall">전체목록</a>';
?>

<div class="local_ov01 local_ov">
    <?php echo $listall; ?>
    등록된 상품 <?php echo $total_count; ?>건
</div>
<form class="local_sch02 local_sch">
<input type="hidden" name="doc" value="<?php echo $doc; ?>">
<input type="hidden" name="sort1" value="<?php echo $sort1; ?>">
<input type="hidden" name="sort2" value="<?php echo $sort2; ?>">
<input type="hidden" name="page" value="<?php echo $page; ?>">
<input type="hidden" name="save_search" value="<?php echo $search; ?>">

<div class="sch_last">
	<strong>주문상태</strong>
	<select name="od_status" id="od_status">
		<option value="">전체</option>
		<option value="입금대기" <?php echo get_selected($od_status, '입금대기'); ?>>입금대기</option>
		<option value="결제완료" <?php echo get_selected($od_status, '결제완료'); ?>>결제완료</option>
		<option value="상품준비중" <?php echo get_selected($od_status, '상품준비중'); ?>>상품준비중</option>
		<option value="배송중" <?php echo get_selected($od_status, '배송중'); ?>>배송중</option>
		<option value="배송완료" <?php echo get_selected($od_status, '배송완료'); ?>>배송완료</option>
	</select>
	&nbsp;&nbsp;&nbsp;
	<strong>공동구매코드</strong>
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
	&nbsp;&nbsp;&nbsp;
    <strong>주문일자</strong>
    <input type="text" id="fr_date"  name="fr_date" value="<?php echo $fr_date; ?>" class="frm_input" size="10" maxlength="10"> ~
    <input type="text" id="to_date"  name="to_date" value="<?php echo $to_date; ?>" class="frm_input" size="10" maxlength="10">
    <button type="button" onclick="javascript:set_date('오늘');">오늘</button>
    <button type="button" onclick="javascript:set_date('어제');">어제</button>
    <button type="button" onclick="javascript:set_date('이번주');">이번주</button>
    <button type="button" onclick="javascript:set_date('이번달');">이번달</button>
    <button type="button" onclick="javascript:set_date('지난주');">지난주</button>
    <button type="button" onclick="javascript:set_date('지난달');">지난달</button>
    <button type="button" onclick="javascript:set_date('전체');">전체</button>
	&nbsp;&nbsp;&nbsp;
	<select name="sel_field" id="sel_field">
		<option value="it_name" <?php echo get_selected($sel_field, 'it_name'); ?>>상품명</option>
		<option value="mb_nick" <?php echo get_selected($sel_field, 'mb_nick'); ?>>닉네임</option>
		<option value="ct_wearing_cnt" <?php echo get_selected($sel_field, 'ct_wearing_cnt'); ?>>미입고(유)</option>
		<option value="total_amount_code" <?php echo get_selected($sel_field, 'total_amount_code'); ?>>공동구매코드</option>
	</select>

	<label for="search" class="sound_only">검색어</label>
	<input type="text" name="search" value="<?php echo $search; ?>" id="search" class="frm_input" autocomplete="off">

    <input type="submit" value="검색" class="btn_submit">
</div>
</form>


<form name="fitemlistupdate" method="post" action="./grouppurchase_appli_listupdate.php" autocomplete="off">

<div class="btn_add01 btn_add">
	<div style="float:left;padding:0 0 0 20px;">
	공동구매 총 합계 : <?=number_format($total_price[total_price])?> 원
	</div>

	<div style="clear:both;">
		<div class="btn_list01 btn_list" style="float:left;">
			<?php if ($is_admin == 'super') { ?>
			<input type="button" name="act_button" value="선택삭제">
			<?php } ?>
			<a href="#" class="go_sise_update">현재시세 금액 업데이트</a>
			<a href="#" style="background:#ff3061;color:#fff;" class="act_button" value="전체삭제">전체삭제</a>
			<a href="#" class="go_excel">엑셀다운로드</a>
		</div>
	</div>
</div>



<input type="hidden" name="sel_field" value="<?php echo $sel_field; ?>">
<input type="hidden" name="sst" value="<?php echo $sst; ?>">
<input type="hidden" name="sod" value="<?php echo $sod; ?>">
<input type="hidden" name="mode">
<input type="hidden" name="page" value="<?=$page?>">
<input type="hidden" name="save_search" value="<?=$save_search?>">
<input type="hidden" name="od_status" value="<?=$od_status?>">
<input type="hidden" name="search" value="<?=$search?>">
<input type="hidden" name="fr_date" value="<?=$fr_date?>">
<input type="hidden" name="to_date" value="<?=$to_date?>">
<input type="hidden" name="sfl_code" value="<?=$sfl_code?>">
<input type="hidden" name="sfl_code2" value="<?=$sfl_code2?>">
<input type="hidden" name="od_status" value="<?php echo $od_status; ?>">

<div class="test"></div>

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
		<th scope="col" rowspan="3">
            <label for="chkall" class="sound_only">상품 전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col">no</a></th>
        <th scope="col" width="100px">날짜</th>
		<th scope="col" width="100px">공동구매 코드</th>
		<th scope="col" id="th_img" width="100px">이미지</th>
        <th scope="col" id="th_pc_title">상품명</th>
		<th scope="col" width="120px">주문자</th>
        <th scope="col" width="120px">수량</th>
		<th scope="col" width="100px">단가(개)</th>
		<th scope="col" width="100px">판매금액</th>
        <th scope="col" width="70px">진행상태</th>
		<th scope="col" width="70px">판매가능여부</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=mysql_fetch_array($result); $i++)
    {
        $href = G5_SHOP_URL.'/grouppurchase.php?gp_id='.$row['it_id'];
        $bg = 'bg'.($i%2);

		$row[ct_type] = substr($row[ct_type], 0, 4);

		$image = get_gp_image($row['it_id'], 70, 70);

		$gp_row = sql_fetch("select * from {$g5['g5_shop_group_purchase_table']} where gp_id='".$row[it_id]."' ");

		$mb = get_member($row[mb_id]);

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

		$totalPrice = ($row['ct_price'] * $row[ct_qty]) + $op_price;

    ?>
    <tr class="<?php echo $bg; ?>">
		<td class="td_chk">
            <label for="chk_<?php echo $i; ?>" class="sound_only"><?php echo get_text($row['bo_subject']) ?> 게시판</label>
            <input type="checkbox" name="chk[]" value="<?php echo $i; ?>" id="chk_<?php echo $i; ?>">
			<input type="hidden" name="gp_id[<?php echo $i; ?>]" value="<?php echo $row['ct_id']; ?>">
        </td>
        <td class="td_num">
            <?php echo $row['ct_id']; ?>
        </td>
        <td class="td_datetime"><?=date("Y-m-d H_i_s", strtotime($row[ct_time]))?></td>
		<td class="td_num"><?php echo printGrouppurCode($row['total_amount_code'])?></td>
        <td class="td_img">
            <?php echo $image?>
        </td>
		<td>
			<div><a href="<?php echo $href; ?>"><?=$row[it_name]?></a></div>
			<div style="padding:5px 0 0;">(URL : <a href="<?=$gp_row[gp_site]?>" target="_blank"><?=cut_str($gp_row[gp_site], 50)?></a>)</div>
		</td>
		<td class="td_mbnick"><?php echo $mb['mb_nick']?></td>
		<td class="td_num"><?php echo $row[ct_qty]?>개</td>
		<td class="td_mngsmall">
			개당</br>
			<input type="text" name="ct_price<?=$row[ct_id]?>" value="<?=$row[ct_price]?>" class="frm_input" style="text-align:center;" size="10"></br>
			<input type="button" value="가격변경" style="border:1px #000 solid;background:#fff;" class="price_chg" idx="<?=$row[ct_id]?>" op_price="<?=$op_price?>"></td>
		<td class="td_numbig"><?php echo number_format($totalPrice)?> 원</td>
        <td class="td_mngsmall go_status"><?php echo getPurchaseStateText($row['total_amount_code']);?></td>
		<td class="td_mngsmall buy_status" idx="<?=$row[ct_id]?>">
			<?if($row[buy_status] == "y"){?>
				<font color="blue">판매가능</font>
			<?}else{?>
				<font color="red">품절</font>
			<?}?>
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
$(function(){
    $("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
});

$(document).ready(function(){

	$(".price_chg").click(function(){
		var idx = $(this).attr("idx");
		var op_price = $(this).attr("op_price");
		var ct_price = $("input[name='ct_price"+idx+"']").val();

		$.post("./_Ajax.applipricechg.php", {idx:idx, op_price:op_price, ct_price:ct_price}, function(data){
			alert("정상적으로 변경 되었습니다.");
		});
	});

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

	$(".go_sise_update").click(function(){
		location.href = "./grouppurchase_appli_priceupdate.php";
	});


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

function set_date(today)
{
    <?php
    $date_term = date('w', G5_SERVER_TIME);
    $week_term = $date_term + 7;
    $last_term = strtotime(date('Y-m-01', G5_SERVER_TIME));
    ?>
    if (today == "오늘") {
        document.getElementById("fr_date").value = "<?php echo G5_TIME_YMD; ?>";
        document.getElementById("to_date").value = "<?php echo G5_TIME_YMD; ?>";
    } else if (today == "어제") {
        document.getElementById("fr_date").value = "<?php echo date('Y-m-d', G5_SERVER_TIME - 86400); ?>";
        document.getElementById("to_date").value = "<?php echo date('Y-m-d', G5_SERVER_TIME - 86400); ?>";
    } else if (today == "이번주") {
        document.getElementById("fr_date").value = "<?php echo date('Y-m-d', strtotime('-'.$date_term.' days', G5_SERVER_TIME)); ?>";
        document.getElementById("to_date").value = "<?php echo date('Y-m-d', G5_SERVER_TIME); ?>";
    } else if (today == "이번달") {
        document.getElementById("fr_date").value = "<?php echo date('Y-m-01', G5_SERVER_TIME); ?>";
        document.getElementById("to_date").value = "<?php echo date('Y-m-d', G5_SERVER_TIME); ?>";
    } else if (today == "지난주") {
        document.getElementById("fr_date").value = "<?php echo date('Y-m-d', strtotime('-'.$week_term.' days', G5_SERVER_TIME)); ?>";
        document.getElementById("to_date").value = "<?php echo date('Y-m-d', strtotime('-'.($week_term - 6).' days', G5_SERVER_TIME)); ?>";
    } else if (today == "지난달") {
        document.getElementById("fr_date").value = "<?php echo date('Y-m-01', strtotime('-1 Month', $last_term)); ?>";
        document.getElementById("to_date").value = "<?php echo date('Y-m-t', strtotime('-1 Month', $last_term)); ?>";
    } else if (today == "전체") {
        document.getElementById("fr_date").value = "";
        document.getElementById("to_date").value = "";
    }
}

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
