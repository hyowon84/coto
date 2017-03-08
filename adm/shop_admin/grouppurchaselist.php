<?php
$sub_menu = '500200';
$sub_sub_menu = '2';

include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$g5['title'] = '공동구매관리';
include_once (G5_ADMIN_PATH.'/admin.head.php');

// 분류
$ca_list  = '<option value="">선택</option>'.PHP_EOL;
$sql = "	SELECT	*
 					FROM		g5_shop_category
 					WHERE		1=1
 					AND			ca_id NOT LIKE 'AP%'
 					AND			ca_id NOT LIKE 'GV%'
 					AND			ca_id NOT LIKE 'MC%'
";

if ($is_admin != 'super') {
	$sql .= " where ca_mb_id = '{$member['mb_id']}' ";
}
$sql .= " ORDER	BY	ca_id ";
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
		if($sfl == "gp_name"){
			$stx_arr = explode(" ", $stx);
			for($i = 0; $i < count($stx_arr); $i++){
				$sql_search .= " $where $sfl like '%".$stx_arr[$i]."%' ";
			}
		}else{
			$sql_search .= " $where $sfl like '%$stx%' ";
		}
		$where = " and ";
	}
	if ($save_stx != $stx)
		$page = 1;
}

if ($sca != "") {
	$sql_search .= " $where (CT.ca_id like '$sca%') ";
}

if ($sfl == "")  $sfl = "GP.gp_name";



$sql_common = " FROM	g5_shop_group_purchase GP
											LEFT JOIN g5_shop_category CT ON (CT.ca_id = GP.ca_id)
											,(	SELECT	*
													FROM		flow_price
													ORDER BY ymd DESC
													LIMIT 1
											) FP
							  WHERE		1
";
$sql_common .= $sql_search;


// 테이블의 전체 레코드수만 얻음
$sql = " select count(*) as cnt " . $sql_common;
$row = sql_fetch($sql);
$total_count = $row['cnt'];



// $rows = $config['cf_page_rows'];
$rows = 30;
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

if (!$sst) {
	$sst  = "gp_update_time ";
	$sod = "desc";
}
$sql_order = "order by $sst $sod";


$sql = "		SELECT	T.*,
										CL.SUM_QTY
						FROM		(	SELECT	GP.*,
															CT.ca_name,
															FP.USD
											FROM	g5_shop_group_purchase GP
														LEFT JOIN g5_shop_category CT ON (CT.ca_id = GP.ca_id)
														
														,(	SELECT	*
																FROM		flow_price
																ORDER BY ymd DESC
																LIMIT 1
														) FP
											WHERE		1
											$sql_search
											LIMIT		$from_record, $rows
										) T
										LEFT JOIN (	SELECT	it_id,
																				SUM(it_qty) AS SUM_QTY
																FROM		clay_order
																WHERE		stats NOT IN ('99')
																GROUP BY it_id
										) CL ON (CL.it_id = T.gp_id)
							WHERE	1=1
							$sql_order
";

/*
$sql  = "	SELECT	GP.*,
									CT.ca_name,
									CL.SUM_QTY,
									FP.USD
				  	$sql_common
				  $sql_order
				  LIMIT		$from_record, $rows
";
*/
$result = sql_query($sql);


echo  "<textarea>";
echo $sql;
echo  "</textarea>";




//$qstr  = $qstr.'&amp;sca='.$sca.'&amp;page='.$page;
$qstr  = $qstr.'&amp;sca='.$sca.'&amp;page='.$page.'&amp;save_stx='.$stx;

$listall = '<a href="'.$_SERVER['PHP_SELF'].'" class="ov_listall">전체목록</a>';

//Excel 전체 페이지
$excelRow = 10000;
$excelPage = ceil($total_count / $excelRow);  // 전체 페이지 계산
?>

<div class="local_ov01 local_ov">
	<?=$listall?>
	등록된 상품 <?=$total_count?>건
</div>

<form name="flist" class="local_sch01 local_sch">
<input type="hidden" name="page" value="<?=$page?>">
<input type="hidden" name="save_stx" value="<?=$stx?>">
<label for="sca" class="sound_only">분류선택</label>
<select name="sca" id="sca">
	<option value="">전체분류</option>
	<?php
	$sql1 = " SELECT	ca_id,
										ca_name
						FROM		g5_shop_category
						WHERE		1=1
						AND			ca_id NOT LIKE 'AP%'
						AND			ca_id NOT LIKE 'GV%'
						AND			ca_id NOT LIKE 'MC%'
						AND			ca_id NOT LIKE 'PM%'
						ORDER	BY	ca_id ASC, ca_order DESC 
	";
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
	<option value="gp_name" <?=get_selected($sfl, 'gp_name')?>>상품명</option>
	<option value="gp_id" <?=get_selected($sfl, 'gp_id')?>>상품코드</option>
</select>

<label for="stx" class="sound_only">검색어</label>
<input type="text" name="stx" value="<?=$stx?>" id="stx" class="frm_input">
<input type="submit" value="검색" class="btn_submit">
</form>


<font style='font-size:14pt; font-weight:bold;'>
&nbsp;&nbsp;상품가격1 $
<input type='text' id='dollar1' name='dollar1' onchange="calculate(this.value,'result1')" onkeyup="calculate(this.value,'result1')"> * $환율(<?=$_SESSION[unit_kor_duty]?>원) * 수수료(1.06) * 부가세(1.1) =
결과 <span id='result1' style=''></span>원
<br>&nbsp;&nbsp;상품가격2 $
<input type='text' id='dollar2' name='dollar2' onchange="calculate(this.value,'result2')" onkeyup="calculate(this.value,'result2')"> * $환율(<?=$_SESSION[unit_kor_duty]?>원) * 수수료(1.06) * 부가세(1.1) =
결과 <span id='result2' style=''></span>원
<br>&nbsp;&nbsp;상품가격3 $
<input type='text' id='dollar3' name='dollar3' onchange="calculate(this.value,'result3')" onkeyup="calculate(this.value,'result3')"> * $환율(<?=$_SESSION[unit_kor_duty]?>원) * 수수료(1.06) * 부가세(1.1) =
결과 <span id='result3' style=''></span>원
</font>


<script>
var kor_duty = '<?=$_SESSION[unit_kor_duty]?>';

function calculate(dollar,target) {
	var result;

	result = dollar * kor_duty * 1.06 * 1.1;
	result = Math.floor(result);
	$('#'+target).html(number_fomrat(result));
}

function number_fomrat(str){
    str = str + "";
    if(str == "" || /[^0-9,]/.test(str)) return str;
    str = str.replace(/,/g, "");
    for(var i=0; i<parseInt(str.length/3, 10); i++){
        str = str.replace(/([0-9])([0-9]{3})(,|$)/, "$1,$2$3");
    }
    return str;
}
</script>


<form name="fitemlistupdate" method="post" action="./grouppurchaselistupdate.php" onsubmit="return fitemlist_submit(this);" autocomplete="off">

<div class="btn_list01 btn_list" style="width:25%; float:left;">
	<?if($REMOTE_ADDR == "183.98.59.5"){?>
	<input type="submit" name="act_button" value="투데이스토어로이동" onclick="document.pressed=this.value">
	<?}?>

	<input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value">
</div>

<div class="btn_add01 btn_add" style="width:50%; float:right;">
	<a href="#" class="all_price_chg">가격저장</a>
	<!-- a href="#" style="background:#ff3061;color:#fff;" class="all_del">전체삭제</a-->
	<a href="./grouppurchaseform.php">상품등록</a>
	<a href="./grouppurchaseexcel.php" onclick="return excelform(this.href);" target="_blank">상품일괄등록</a>
	<select name="excel_sel_page" id="excel_sel_page" style="width:50px;height:30px;margin-left:30px;">
		<?php for($i=1;$i<=$excelPage;$i++){?>
		<option value="<?=$i?>"><?=$i?></option>
		<?php }?>
	</select> PAGE
	<a href="#" onclick="exceldownload();return false;">상품다운로드</a>
</div>

<input type="hidden" name="sca" value="<?=$sca?>">
<input type="hidden" name="sst" value="<?=$sst?>">
<input type="hidden" name="sod" value="<?=$sod?>">
<input type="hidden" name="sfl" value="<?=$sfl?>">
<input type="hidden" name="stx" value="<?=$stx?>">
<input type="hidden" name="page" value="<?=$page?>">

<div class="tbl_head02 tbl_wrap">
	<table>
	<caption><?=$g5['title']?> 목록</caption>
	<thead>
	<tr>
		<th scope="col" rowspan="3">
			<label for="chkall" class="sound_only">상품 전체</label>
			<input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
		</th>
		<th scope="col"><?=subject_sort_link('gp_id', 'sca='.$sca)?>상품코드</a></th>
		<th scope="col">
			분류<br>
			<?=subject_sort_link('gp_name', 'sca='.$sca)?>상품명</a>
		</th>
		<th scope="col" id="th_img">이미지</th>
		<th><?=subject_sort_link('b2b_yn', 'sca='.$sca)?>B2B판매</a> <input type='button' onclick="all_chk_eventyn()" value=' * ' /></th>
		<th scope="col">매입가($)</th>
		<th scope="col"><?=subject_sort_link('gp_price', 'sca='.$sca)?>입력가격(원화)</a></th>
		<th>딜러재고</th>
		<th>코투재고</th>
		<th>예상재고<br>(재고-수량)</th>
		<th scope="col"><?=subject_sort_link('gp_charge', 'sca='.$sca)?>수수료</a></th>
		<th scope="col"><?=subject_sort_link('gp_duty', 'sca='.$sca)?>관세</a></th>
		<th scope="col"><?=subject_sort_link('gp_order', 'sca='.$sca)?>순서</a></th>
		<th scope="col" style="width:50px;">
			<?=subject_sort_link('gp_use', 'sca='.$sca, 1)?>판매</a></br>
			<span class="buy_all_chk_bn">전체선택</span>
		</th>
		<th scope="col">관리</th>
	</tr>
	</thead>

	<tbody>

	<?php
	for ($i=0; $row=mysql_fetch_array($result); $i++)
	{
		$href = G5_SHOP_URL.'/grouppurchase.php?gp_id='.$row['gp_id'];
		$bg = 'bg'.($i%2);

		//$gp_price = getGroupPurchaseBasicPrice($row[gp_id]);
		$isEvent = ($row[b2b_yn] == 'Y') ? ' checked ' : '';
		//if(!trim($row['gp_img']) || trim($row['gp_img'])=="http://")UpdateGroupPurchaseEtc($row['gp_id']);
		$예상재고 = $row[gp_have_qty] - $row[SUM_QTY];

		$재고색상 = ($예상재고 != $row[gp_have_qty]) ? 'red' : '';
	?>
	<tr class="<?=$bg?>">
		<td class="td_chk">
			<label for="chk_<?=$i?>" class="sound_only"><?=get_text($row['bo_subject']) ?> 게시판</label>
			<input type="checkbox" name="chk[]" value="<?=$i ?>" id="chk_<?=$i?>">
		</td>
		<td class="td_num">
			<input type="hidden" name="gp_id[<?=$i?>]" value="<?=$row['gp_id']?>">
			<?=$row['gp_id']?>
		</td>
		<td>
			<label for="ca_id_<?=$i?>" class="sound_only">분류</label>

			<!-- select name="ca_id[<?=$i?>]" id="ca_id_<?=$i?>">
				<? //conv_selected_option($ca_list, $row['ca_id']) ?>
			</select>
			 -->
			<?="CATEGORY : ".$row['ca_name']?>
			<input type="text" id="ca_id[<?=$i?>]" name="ca_id[<?=$i?>]" value='<?=$row['ca_id']?>' />

			<?=$tmp_ca_list?><br>
			<input type="text" name="gp_name[<?=$i?>]" value="<?=htmlspecialchars2(cut_str($row['gp_name'],250, ""))?>" class="frm_input" style='width:90%'>
		</td>
		<td class="td_img"><a href="<?=$href?>"><img src="<?=$row['gp_img'];?>" width="50"></a></td>
		<td class="td_numbig"><input type='checkbox' class='b2b_yn' name='b2b_yn[<?=$i?>]' value='Y' <?=$isEvent?> /></td>
		<td class="td_numbig">
			<input type="text" id="gp_price_org[<?=$i?>]" name="gp_price_org[<?=$i?>]" class="frm_input" size='7' value='<?=$row['gp_price_org']?>' /><br>
			<font color=red><b><?=number_format($row['gp_price_org'] * $row[USD])?>원</b></font><br>
			<?=$row[USD]?>원($1)
		</td>
		<td class="td_numbig">
			<span id='price_<?=$row[gp_id]?>'></span>
			<input type="text" id="gp_price[<?=$i?>]" name="gp_price[<?=$i?>]" class="frm_input" size='7' onkeyup="inputPrice('price_<?=$row[gp_id]?>',this.value)" value='<?=$row['gp_price']?>' />
		</td>
		<td align='center'><input type="text" name="jaego[<?=$i?>]" value="<?=$row['jaego']?>" class="frm_input" size="3">개</td></td>
		<td align='center'><input type="text" name="gp_have_qty[<?=$i?>]" value="<?=$row['gp_have_qty']?>" class="frm_input" size="3">개</td></td>
		<td align='center'><font color='<?=$재고색상?>' size=3><b><?=number_format($예상재고)?></b></font></td>
		<td class="td_mngsmall"><input type="text" name="gp_charge[<?=$i?>]" value="<?=$row['gp_charge']?>" class="frm_input" size="3"> %</td>
		<td class="td_mngsmall"><input type="text" name="gp_duty[<?=$i?>]" value="<?=$row['gp_duty']?>" class="frm_input" size="3"> %</td>
		<td class="td_mngsmall"><input type="text" name="gp_order[<?=$i?>]" value="<?=$row['gp_order']?>" class="frm_input" size="10"></td>
		<td class="td_chk"><input type="checkbox" name="gp_use[<?=$i?>]" <?=($row['gp_use'] ? 'checked' : '')?> value="1"></td>
		<td class="td_mng">
			<a href="<?=$href?>"><span class="sound_only"><?=htmlspecialchars2(cut_str($row['it_name'],250, ""))?> </span>보기</a>
			<a href="./grouppurchasecopy.php?gp_id=<?=$row['gp_id']?>&amp;ca_id=<?=$row['ca_id']?>" class="itemcopy" target="_blank"><span class="sound_only"><?=htmlspecialchars2(cut_str($row['it_name'],250, ""))?> </span>복사</a>
			<a href="./grouppurchaseform.php?w=u&amp;gp_id=<?=$row['gp_id']?>&amp;ca_id=<?=$row['ca_id']?>&amp;<?=$qstr?>"><span class="sound_only"><?=htmlspecialchars2(cut_str($row['it_name'],250, ""))?> </span>수정</a>
		</td>
	</tr>
	<?php
	}
	if ($i == 0) echo '<tr><td colspan="11" class="empty_table">자료가 한건도 없습니다.</td></tr>';

	?>
	</tbody>
	</table>
</div>

<div class="btn_list01 btn_list">
	<?if($REMOTE_ADDR == "183.98.59.5"){?>
	<input type="submit" name="act_button" value="투데이스토어로이동" onclick="document.pressed=this.value">
	<?}?>

	<input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value">
	<?php if ($is_admin == 'super') { ?>
	<input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value">
	<?php } ?>
</div>
<!-- <div class="btn_confirm01 btn_confirm">
	<input type="submit" value="일괄수정" class="btn_submit" accesskey="s">
</div> -->
</form>

<form name="fitemlistdel" id="fitemlistdel" method="POST">
<input type="hidden" name="HTT_CHK" value="CHK_OK">
<input type="hidden" name="sca" value="<?=$sca?>">
<input type="hidden" name="sst" value="<?=$sst?>">
<input type="hidden" name="sod" value="<?=$sod?>">
<input type="hidden" name="sfl" value="<?=$sfl?>">
<input type="hidden" name="stx" value="<?=$stx?>">
<input type="hidden" name="page" value="<?=$page?>">
</form>

<?
echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['PHP_SELF']}?$qstr&amp;page=");
?>

<script type="text/javascript">

$(document).ready(function(){
	$(".all_del").click(function(){
		var f = document.fitemlistdel;
		if(confirm("상품 전체 삭제 시 복구 불가능합니다.\n정말 삭제 하시겠습니까?")){
			f.action = "./grouppurchasedelall.php";
			f.submit();
		}
	});

	$(".buy_all_chk_bn").click(function(){
		if($("input[name^='gp_use']").is(":checked") == false){
			$("input[name^='gp_use']").attr("checked", true);
		}else{
			$("input[name^='gp_use']").attr("checked", false);
		}
	});

	$(".all_price_chg").click(function(){
		if(confirm("현재의 가격으로 변경하시겠습니까?")){
			location.href = "./grouppurchase_price_all_chg.php";
		}
	});
});

function fitemlist_submit(f)
{
	if (!is_checked("chk[]")) {
		alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
		return false;
	}

	if(document.pressed == "선택삭제") {
		if(!confirm("선택한 자료를 정말 삭제하시겠습니까?")) {
			return false;
		}
	}

	return true;
}

function excelform(url)
{
	var opt = "width=600,height=450,left=10,top=10";
	window.open(url, "win_excel", opt);
	return false;
}

function exceldownload(){
	var surl = "./grouppurchaseexceldownload.php?<?=$qstr?>&excel_row=<?=$excelRow?>&excel_page=" + $("#excel_sel_page").val() + "&sca=" + $("select[name='sca']").val();
	location.href = surl;
}

function all_chk_eventyn() {
	$('.b2b_yn').each(function(i){
		if($(this).attr('checked') == 'checked') {
			$(this).attr('checked',false);
		} else {
			$(this).attr('checked',true);
		}
	});
}

</script>


<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
