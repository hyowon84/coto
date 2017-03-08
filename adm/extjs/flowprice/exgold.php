<?php
$sub_menu = '510200';
// $sub_sub_menu = '2';

include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$g5['title'] = '국내 금속 시세';
include_once (G5_ADMIN_PATH.'/admin.head.php');

$sql_search = "";
if ($search != "") {
	
	/* 거래자명 */
	$where[] = " BD.trader_name LIKE '%$search%' ";
	
	/* 주문번호 */
	$where[] = " BD.admin_link LIKE '%$search%' ";
	
	/* 메모 */
	$where[] = " BD.admin_memo LIKE '%$search%' ";
	
	/* 금액비교 (한글의 경우 1로 인식하기에 100원보다 큰경우에만 조건검색) */
	if($search > 100) { 
		$where[] = " BD.input_price = '$search' ";
		$where[] = " BD.output_price = '$search' ";
	}
	
	if ($where) {
		$sql_search .= ' AND  '.implode(' OR ', $where);
	}
	
	$qrystr = "search=$search&amp;test=1";
}
?>
<script src="http://malsup.github.com/jquery.form.js"></script> 
<link rel="stylesheet" href="http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	
	
<!-- form class='local_sch'>
	<div style='float:left; margin-left:10px;'>
		<input type='button' id="opener" value="입출금목록입력" />
	</div>
	<div style='float:left; margin-left:50px;'>
		<label for="search" class="sound_only">검색어</label>
		검색어 : <input type="text" name="search" value="<?=$search;?>" id="search" class="frm_input" autocomplete="off">
	</div>&nbsp;
	<input type="submit" value="검색" class="btn_submit">
</form -->



<style>
/* 다이얼로그 스타일 */
.bank_dialog table tr th { padding:0px; text-align:left; height:25px; }
.bank_dialog table tr td { padding-left:10px; border:1px #d1dee2; }
.bank_dialog table tr td input { border:1px solid #EAEAEA; }

#divBankTableArea table tr th, #divBankTableArea table tr td{
	text-align:center; border:1px solid #d1dee2; 
}

#divBankTableArea table tr th { height:25px; background-color:#EEEEEE; }
#divBankTableArea table tr td { height:25px; }


#divBankDtlTableArea { width:30%; float:left; margin:10px; }
#divBankDtlTableArea table tr th { height:25px; background-color:#fbffd7; }
/* #divBankDtlTableArea table { width:50%; } */

.bank_tr:hover { background-color:#f1fbff; cursor:pointer; }
.bank_dtl_tr:hover { background-color:#fcffe4; cursor:pointer; }

.DetailOn { display:''; }
.DetailOff { display:none; }

.banklist_inp_text { border:1px solid #d1dee2; width:90%; }

.yellow { background-color:#fffcda; }
</style>

<br>

<!-- AJAX DB업데이트 -->
<div id="dialog" class='bank_dialog' title="입금내역 엑셀 입력">
	<form id='bank_form' name='bank_form' method="post" action="bank_list.inp.php" enctype="MULTIPART/FORM-DATA" autocomplete="off">
	
	<table id='bank_excel_tb' border='0'>
		<tr>
			<th>엑셀</th>
			<td><input type='file' name='excelfile' /></td>
		</tr>
		<tr>
			<td colspan='2' height='40' align='center'>
				<input type="button" value="입력" id='dialog_submit' /> 
				<input type="button" value='취소' id='dialog_reset'>
			</td>
		</tr>
	</table>
	
	<div id='bank_excel_loading' style='text-align:center; display:none;'>
		<img src='/img/ajax-loader.gif' />
		<br><br>엑셀데이터 입력중... <br>완료될때까지 기다려주세요
	</div>
	
	<div id='bank_excel_complete' style='text-align:center; display:none;'>
		입력완료
	</div>
	
	</form>
</div>

<? 
$rows = 20;	//default : 20개
if ($page == "") {
	$page = 1;
} // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함


//그룹태그 or 상품코드 기준으로 SELECT SQL
$common_sql = "	SELECT	*
								FROM		flow_price_exg FPE
								WHERE		1=1
								$sql_search
								ORDER BY FPE.reg_date DESC
";
$sql = $common_sql."	LIMIT $from_record, $rows";
$result = sql_query($sql);

$total_count = mysql_num_rows(sql_query($common_sql));
// $total_price = $row['total_price'];
// $total_cancelprice = $row['total_cancelprice'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
?>


<div id='divBankTableArea' style='width:700px; margin:0px auto;'>
	<table>
	<tr>
		<th width='120'>거래일시</th>
		<th width='120'>금(3.75g)</th>
		<th width='120'>은(3.75g)</th>
		<th width='120'>백금(3.75g)</th>
		<th width='120'>팔라듐(3.75g)</th>
	</tr>
	<?
	while($row = mysql_fetch_array($result)) {
		$거래일 = $row[reg_date];
		$금시세 = $row[GL];
		$은시세 = $row[SL];
		$백금시세 = $row[PT];
		$팔라듐시세 = $row[PD];
	?>
	<tr class='bank_tr <?=$link_color?>' id='<?=''?>' <?=$bgcolor?>>
		<td onclick="showDetail('b<?=$row[number]?>')"><?=$거래일?></td>
		<td style='text-align:right; padding-right:5px;'><?=number_format($금시세)?>원</td>
		<td style='text-align:right; padding-right:5px;'><?=number_format($은시세)?>원</td>
		<td style='text-align:right; padding-right:5px;'><?=number_format($백금시세)?>원</td>
		<td style='text-align:right; padding-right:5px;'><?=number_format($팔라듐시세)?>원</td>		
	</tr>
	<tr><td colspan='5' style='background-color:black; height:1px; padding:0px;'></td></tr>
	<?
	}
	?>
	</table>
	
	<?=get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['PHP_SELF']}?$qrystr&amp;page=");?>
	
</div>


<script>
$(document).ready(function() { 
    // bind 'myForm' and provide a simple callback function 
    $('#bank_form').ajaxForm(function(data) {
    	$("#bank_excel_tb").show();
    	$("#bank_excel_loading").hide();
    	$("#bank_excel_complete").show();
			//disalbe 해제
    });
});

$( "#dialog" ).dialog({ 
	autoOpen: false,
	width:'auto'		 
});
$( "#opener" ).click(function() {
	$( "#dialog" ).dialog( "open" );
});



//DIALOG FORM SUBMIT
$( "#dialog_submit" ).click(function() {
	$("#bank_excel_tb").hide();
	$("#bank_excel_loading").show();
	$("#bank_form").submit();
});


//DIALOG FORM RESET
$( "#dialog_reset" ).click(function() {
	$("#bank_form").each(function(){
		this.reset();
	});
});

/* FORM VALIDATION  */
function chkForm() {
	var v_chk = 0;
	var v_qty = 0;
		
	$('input').each(function(e){
		if( ($(this).attr('type') == 'text' || $(this).attr('type') == 'tel') && (!$(this).val() && $(this).attr('title') != undefined) ) {
			// 방문수령일때 ZIP또는 상세주소 입력통과
			if( !($('#delivery_type').val() == 'D03' && $(this).attr('name') == 'zip' || $(this).attr('name') == 'addr2') ) {
				alert($(this).attr('title')+'을(를) 입력해주세요'+$(this).val() );
				v_chk++;
			}
		}
	});

	$('.ea').each(function(e){
		v_qty += $(this).val(); 
	});

	if(v_qty == 0) {
		alert('입고 or 출고 수량을 입력해주세요');
		v_chk++;
	}
	
	if(v_chk > 0)
		return false;
	else
		return true;	
}

function showDetail(id) {
	if($('#'+id).attr('class') == 'banklist DetailOn') {
		$('#'+id).attr('class','banklist DetailOff');
	}
	else {
		$('#'+id).attr('class','banklist DetailOn');
	}
}

function updateBankData(number) {
	$('#bt_'+number).hide();
	
	$.post('bank_update.php',
					{ 'number' : number,
						'bank_type'	:	$("select[name=bank_type_"+number+"]").val(),
						'admin_link': $('#admin_link_'+number).val(),
						'admin_memo': $('#admin_memo_'+number).val()
					},
					function( data ) {
						if(data == 1) {
							alert('수정 완료');
						} else {
							alert('수정 실패');
						}
						//var content = $( data ).find( '#content' );
						//$( "#result" ).empty().append( content );
					}
	);
}
</script>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>