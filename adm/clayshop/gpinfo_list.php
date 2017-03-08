<?php
$sub_menu = '510400';
// $sub_sub_menu = '2';
include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$g5['title'] = '공동구매관리 목록';
include_once (G5_ADMIN_PATH.'/admin.head.php');
include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');


$sql_search = "";
if ($search != "") {

	/* 공구명 */
	$where[] = " GI.gpcode_name LIKE '%$search%' ";

	/* 공구코드 */
	$where[] = " GI.gpcode LIKE '%$search%' ";

	/* 공구품목 상품코드 */
	$where[] = " GI.links LIKE '%$search%' ";

	if ($where) {
		$sql_search .= ' AND  '.implode(' OR ', $where);
	}

	$qrystr = "search=$search&amp;test=1";
}


/* 옵션 고유ID 생성 SQL    by. JHW */

//정기공구코드
$seq_sql = "	SELECT	CONCAT(	'R',
												DATE_FORMAT(now(),'%Y%m'),
												'_',

												LPAD(COALESCE(	(	SELECT	SUBSTR(MAX(gpcode),9,2)
																					FROM		gp_info
																					WHERE		gpcode LIKE CONCAT('R%',DATE_FORMAT(now(),'%Y%m'),'%')
																				)
																		,'00')+1,2,'0')
										)	AS oid
						FROM		DUAL
";
list($gpcode_r) = mysql_fetch_array(sql_query($seq_sql));

//긴급공구코드
$seq_sql = "	SELECT	CONCAT(	'E',
												DATE_FORMAT(now(),'%Y%m'),
												'_',

												LPAD(COALESCE(	(	SELECT	SUBSTR(MAX(gpcode),9,2)
																					FROM		gp_info
																					WHERE		gpcode LIKE CONCAT('E%',DATE_FORMAT(now(),'%Y%m'),'%')
																				)
																		,'00')+1,2,'0')
										)	AS oid
						FROM		DUAL
";
list($gpcode_e) = mysql_fetch_array(sql_query($seq_sql));

//개인오더코드
$seq_sql = "	SELECT	CONCAT(	'D',
												DATE_FORMAT(now(),'%Y%m'),
												'_',

												LPAD(COALESCE(	(	SELECT	SUBSTR(MAX(gpcode),9,2)
																					FROM		gp_info
																					WHERE		gpcode LIKE CONCAT('D%',DATE_FORMAT(now(),'%Y%m'),'%')
																				)
																		,'00')+1,2,'0')
										)	AS oid
						FROM		DUAL
";
list($gpcode_d) = mysql_fetch_array(sql_query($seq_sql));
?>


<form class='local_sch'>

	<div style='float:left; margin-left:10px;'>
		<input type='button' id="opener" value="공동구매 추가" />
	</div>
	<div style='float:left; margin-left:50px;'>
		<label for="search" class="sound_only">검색어</label>

		시작일 : <input type="text" id="start_date" name="start_date" value="<?=$start_date?>" style='width:82px;' >
		종료일 : <input type="text" id="end_date" name="end_date" value="<?=$end_date?>" style='width:82px;' >
		<script>$("#start_date, #end_date").datepicker();</script>

		검색어 : <input type="text" name="search" value="<?=$search;?>" id="search" class="frm_input" autocomplete="off">
	</div>&nbsp;
	<input type="submit" value="검색" class="btn_submit">
</form>


<style>
/* 주문내역 다이얼로그 */
#dialog_orderDetail table tr th { background-color: #EEEEEE; border:1px solid #d1dee2; padding:0px; text-align:center; height:25px; }
#dialog_orderDetail table tr td { padding-left:10px; border:1px solid #d1dee2; }


/* 다이얼로그 스타일 */
.gpinfo_dialog table tr th { padding:0px; text-align:left; height:25px; }
.gpinfo_dialog table tr td { padding-left:10px; border:1px #d1dee2; }
.gpinfo_dialog table tr td input { border:1px solid #EAEAEA; }

#divBankTableArea table tr th, #divBankTableArea table tr td{
	text-align:center; border:1px solid #d1dee2;
}

#divBankTableArea table tr th { height:25px; background-color:#EEEEEE; }
#divBankTableArea table tr td { height:25px; }


#divBankDtlTableArea { width:30%; float:left; margin:10px; }
#divBankDtlTableArea table tr th { height:25px; background-color:#fbffd7; }
/* #divBankDtlTableArea table { width:50%; } */



.gpinfo_tr:hover { background-color:#f1fbff; cursor:pointer; }
.gpinfo_dtl_tr:hover { background-color:#fcffe4; cursor:pointer; }

.divTr_order { height:83px; }

.hover_skyblue:hover { background-color:#f1fbff; cursor:pointer; }

.DetailOn { display:''; }
.DetailOff { display:none; }

.gpinfolist_inp_text { border:1px solid #d1dee2; width:90%; }

.yellow { background-color:#fffcda; }

</style>


<br>

<!-- 공동구매 추가 다이얼로그 -->
<div id="dialog_orderDetail" class='gpinfo_dialog' title="주문내역 목록">
	<div id='dialog_orderDetail_body'></div>
</div>

<!-- 공동구매 추가 다이얼로그 -->
<div id="dialog" class='gpinfo_dialog' title="공동구매 설정" style='display:none;'>
	<form id='gpinfo_form' name='gpinfo_form' method="post" action="gpinfo_list.inp.php?mode=new" enctype="MULTIPART/FORM-DATA" autocomplete="off">

	<table id='gpinfo_tb' border='0'>
		<tr>
			<th>공동구매코드(자동)</th>
			<td>
				<input type='text' id='gpcode' name='gpcode' value='<?=$gpcode_r?>' readonly />
			</td>
		</tr>
		<tr>
			<th>공동구매명칭</th>
			<td>
				<input type='text' id='gpcode_name' name='gpcode_name' value='<?=$gpcode_name?>' />
			</td>
		</tr>
		<tr>
			<th>진행유형</th>
			<td><input type='radio' name='gp_type' onclick="$('#gpcode').val('<?=$gpcode_r?>')" value='R' checked />정기 <input type='radio' name='gp_type' onclick="$('#gpcode').val('<?=$gpcode_e?>')" value='E' />긴급  <input type='radio' name='gp_type' onclick="$('#gpcode').val('<?=$gpcode_d?>')" value='P' />개인</td>
		</tr>
		<tr>
			<th>공구진행할 상품코드</th>
			<td><input type='text' name='links' title='상품코드' value='' />
					eX) AP_12345, SKU12345
			</td>
		</tr>
		<tr>
			<th>딜러 제한(상품코드)</th>
			<td><input type='checkbox' name='choice_dealer' value='ALL' />전체 <input type='checkbox' name='choice_dealer' value='AP' />APMEX <input type='checkbox' name='choice_dealer' value='GV' />GainsVille <input type='checkbox' name='choice_dealer' value='MC' />MCM <input type='checkbox' name='choice_dealer' value='SD' />ScottsDale <input type='checkbox' name='choice_dealer' value='OD' />OTHER </td>
		</tr>
		<tr>
			<th>배송비</th>
			<td><input type='text' name='baesongbi' title='배송비' value='3500' size=4 /></td>
		</tr>
		<tr>
			<th>날짜</th>
			<td>
				<input type='text' id='start_date' name='start_date' style='width:82px;' /> ~ <input type='text' id='end_date' name='end_date' style='width:82px;' />
			</td>
		</tr>
		<tr>
			<th>볼륨프라이스적용</th>
			<td><input type='radio' name='volprice_yn' value='Y' checked />예 <input type='radio' name='volprice_yn' value='N' />아니오</td>
		</tr>
		<tr>
			<th>공구참여내역보기</th>
			<td><input type='radio' name='list_view' value='Y' checked />예 <input type='radio' name='list_view' value='N' />아니오</td>
		</tr>
		<tr>
			<th>간략메모</th>
			<td>
				<input type='text' name='memo' value=''>
			</td>
		</tr>

		<tr>
			<td colspan='2' height='40' align='center'>
				<input type="button" value="입력" id='dialog_submit' />
				<input type="button" value='취소' id='dialog_reset'>
			</td>
		</tr>
	</table>

	<div id='gpinfo_loading' style='text-align:center; display:none;'>
		<img src='/img/ajax-loader.gif' />
		<br><br>데이터 입력중... <br>완료될때까지 기다려주세요
	</div>

	<div id='gpinfo_complete' style='text-align:center; display:none;'>
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
$common_sql = "	SELECT	GI.	gpcode,
												GI.	gpcode_name,
												GI.	menu_name,
												GI.	menu_view,
												GI.	gp_type,
												GI.	links,
												GI.	locks,
												GI.	solds,
												GI.	choice_dealer,
												GI.	start_date,
												GI.	end_date,
												GI.	volprice_yn,
												GI.	sell_title,
												GI.	list_view,
												GI.	baesongbi,
												GI.	cafe_url,
												GI.	memo,
												GI.	invoice_memo,
												GI.	stock_memo,
												GI.	stats,
												GI.	reg_date,
												IFNULL(CL.SUM_QTY,'-') AS SUM_QTY
								FROM		gp_info GI
												LEFT JOIN (	SELECT	gpcode,
																						SUM(it_qty) AS SUM_QTY
																		FROM	clay_order
																		WHERE	stats IN (00,10)
																		GROUP BY gpcode
												) CL ON (CL.gpcode = GI.gpcode)
								WHERE		1=1
								AND			GI.gpcode IN ('QUICK','BULLION','EBAY')	/*빠른배송상품, 불리언공구, 이베이 */

								UNION ALL

								SELECT	GI.	gpcode,
												GI.	gpcode_name,
												GI.	menu_name,
												GI.	menu_view,
												GI.	gp_type,
												GI.	links,
												GI.	locks,
												GI.	solds,
												GI.	choice_dealer,
												GI.	start_date,
												GI.	end_date,
												GI.	volprice_yn,
												GI.	sell_title,
												GI.	list_view,
												GI.	baesongbi,
												GI.	cafe_url,
												GI.	memo,
												GI.	invoice_memo,
												GI.	stock_memo,
												GI.	stats,
												GI.	reg_date,
												IFNULL(CL.SUM_QTY,'-') AS SUM_QTY
								FROM		(	SELECT	*
													FROM		gp_info
													ORDER BY 	reg_date DESC
												) GI
												LEFT JOIN (	SELECT	gpcode,
																						SUM(it_qty) AS SUM_QTY
																		FROM	clay_order
																		WHERE	stats IN (00,10)
																		GROUP BY gpcode
												) CL ON (CL.gpcode = GI.gpcode)
								WHERE		1=1
								AND			GI.gpcode NOT IN ('QUICK','BULLION','EBAY')
								$sql_search
";
$sql = $common_sql."	LIMIT $from_record, $rows";
$result = sql_query($sql);

$total_count = mysql_num_rows(sql_query($common_sql));
// $total_price = $row['total_price'];
// $total_cancelprice = $row['total_cancelprice'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산




$selectbox_sql = "SELECT	*
									FROM		comcode CC
									WHERE		CC.ctype	= 'gpinfo'
									AND			CC.col		= 'stats'
									ORDER BY CC.order ASC
";
$sbox_result = sql_query($selectbox_sql);

while($arr = mysql_fetch_array($sbox_result)) {
	$sbox[] = $arr;
}
?>

<div id='divBankTableArea' style='width:100%;'>
	<table width='100%'>
	<tr>
		<th width='120'>공구코드<br>공구명<br>메뉴명</th>
		<th width='60'>
			진행유형
		</th>
		<th width='60'>미결제수량</th>
		<th width='90'>공구상품<br>가격락킹<br>품절상품</th>
		<th width='120'>딜러선택</th>
		<th width='100'>시작일<br>종료일</th>
		<th width='100'>
			볼륨가적용<br>
			가격타이틀<br>
			공구현황보기
		</th>
		<th width='100'>진행상태<br>메뉴공개<br>배송비</th>
		<th width='350'>메모<br>수정BTN</th>
	</tr>
	<?
	while($row = mysql_fetch_array($result)) {
		$공구코드 = $row[gpcode];
		$공구명 = $row[gpcode_name];
		$메뉴명 = $row[menu_name];

		switch ($row[gp_type]) {
			case 'R':
				$진행유형 = '정기';
				break;
			case 'E':
				$진행유형 = '긴급';
				break;
			case 'P':
				$진행유형 = '개인';
				break;
		}

		$공구상품 = $row[links];
		$락킹상품 = $row[locks];
		$품절상품 = $row[solds];
		$딜러 = $row[choice_dealer];		$CD_ALL = (strstr($딜러,'ALL')) ? 'checked' : '';		$CD_AP = (strstr($딜러,'AP')) ? 'checked' : '';		$CD_GV = (strstr($딜러,'GV')) ? 'checked' : '';		$CD_MC = (strstr($딜러,'MC')) ? 'checked' : '';		$CD_SD = (strstr($딜러,'SD')) ? 'checked' : '';		$CD_OD = (strstr($딜러,'OD')) ? 'checked' : '';
		$배송비 = $row[baesongbi];
		$시작일 = $row[start_date];
		$종료일 = $row[end_date];
		$가격타이틀 = $row[sell_title]; 		$sell_title_y = ($가격타이틀 == 'S01') ? 'checked' : '';		$sell_title_n = ($가격타이틀 == 'S02') ? 'checked' : '';
		$볼륨가설정 = $row[volprice_yn];		$volprice_y = ($볼륨가설정 == 'Y') ? 'checked' : '';				$volprice_n = ($볼륨가설정 == 'N') ? 'checked' : '';
		$메뉴보기 = $row[menu_view];		$menu_y = ($메뉴보기 == 'Y') ? 'checked' : '';		$menu_n = ($메뉴보기 == 'N') ? 'checked' : '';
		$공구보기 = $row[list_view];		$list_y = ($공구보기 == 'Y') ? 'checked' : '';		$list_n = ($공구보기 == 'N') ? 'checked' : '';
		$진행상태 = $row[stats];		$stats_y = ($진행상태 == 'START') ? 'checked' : '';		$stats_n = ($진행상태 == 'FINISH') ? 'checked' : '';
		$메모 = $row[memo];
		$CAFE_URL = $row[cafe_url];
		$발주메모 = $row[invoice_memo];
		$입고메모 = $row[stock_memo];
		$미결제수량 = $row[SUM_QTY];

		$selectbox = makeSelectbox($sbox, $진행상태, "stats_".$공구코드);
		$link_color = choiceCodeColor($sbox,$진행상태);
	?>
	<tr class='gpinfo_tr' bgcolor='<?=$link_color?>'>
		<td>
			<input type='hidden' id='gpcode_<?=$공구코드?>' name='gpcode' value='<?=$공구코드?>' />
			
			<a href='/shop/list.php?gpcode=<?=$공구코드?>&ca_id=GP' target='_blank'><?=$공구코드?></a><br>
			
			<input type='text' id='gpcode_name_<?=$공구코드?>' name='gpcode_name' value='<?=$공구명?>' style='width:100px;' /><br>
			<input type='text' id='menu_name_<?=$공구코드?>' name='menu_name' value='<?=$메뉴명?>' style='width:100px;' />
		</td>
		<td onclick="showItemList('<?=$공구코드?>')"><input type='hidden' id='gp_type_<?=$공구코드?>' />
			<?=$진행유형?>
		</td>
		<td onclick="showItemList('<?=$공구코드?>')">
			<font color='red'><b><?=$미결제수량?></b></font>
		</td>
		<td>
			<input type='text' id='links_<?=$공구코드?>' name='links_<?=$공구코드?>' value='<?=$공구상품?>' style='width:200px;' /><br>
			<input type='text' id='locks_<?=$공구코드?>' name='locks_<?=$공구코드?>' value='<?=$락킹상품?>' style='width:200px; background-color:#d7f6ff;' /><br>
			<input type='text' id='solds_<?=$공구코드?>' name='solds_<?=$공구코드?>' value='<?=$품절상품?>' style='width:200px; background-color:#ffe2e2;' />
		</td>
		<td>
			<input type='checkbox' id='cd1_<?=$공구코드?>' name='choice_dealer' value='ALL' <?=$CD_ALL?> />전체 <input type='checkbox' id='cd2_<?=$공구코드?>' name='choice_dealer' value='AP' <?=$CD_AP?> />APMEX <br>
			<input type='checkbox' id='cd3_<?=$공구코드?>' name='choice_dealer' value='GV' <?=$CD_GV?> />GainsVille <input type='checkbox' id='cd4_<?=$공구코드?>' name='choice_dealer' value='MC' <?=$CD_MC?> />MCM <br>
			<input type='checkbox' id='cd5_<?=$공구코드?>' name='choice_dealer' value='SD' <?=$CD_SD?> />ScottsDale <input type='checkbox' id='cd6_<?=$공구코드?>' name='choice_dealer' value='OD' <?=$CD_OD?> />OTHER</td>
		<td>
			S:<input type='text' id='start_date_<?=$공구코드?>' name='start_date_<?=$공구코드?>' value='<?=$시작일?>' style='width:80px;' /><br>
			E:<input type='text' id='end_date_<?=$공구코드?>' name='end_date_<?=$공구코드?>' value='<?=$종료일?>' style='width:80px;' />
		</td>
		<td>
			<input type='radio' name='volprice_yn_<?=$공구코드?>' value='Y' <?=$volprice_y?> />적용 <input type='radio' name='volprice_yn_<?=$공구코드?>' value='N' <?=$volprice_n?> />안함<br>
			<input type='radio' name='sell_title_<?=$공구코드?>' value='S01' <?=$sell_title_y?> />판매 <input type='radio' name='sell_title_<?=$공구코드?>' value='S02' <?=$sell_title_n?> />분양<br>
			<input type='radio' name='list_view_<?=$공구코드?>' value='Y' <?=$list_y?> />공개 <input type='radio' name='list_view_<?=$공구코드?>' value='N' <?=$list_n?> />비공개
		</td>
		<td>
			<?=$selectbox?><br>
			<input type='radio' name='menu_view_<?=$공구코드?>' value='Y' <?=$menu_y?> />공개 <input type='radio' name='menu_view_<?=$공구코드?>' value='N' <?=$menu_n?> />비공개<br>
			<input type='text' id='baesongbi_<?=$공구코드?>' name='baesongbi_<?=$공구코드?>' value='<?=$배송비?>' style='width:40px;' />
		</td>
		<td>
			카페공구게시글URL > <input type='text' id='cafe_url_<?=$공구코드?>' name='cafe_url_[<?=$공구코드?>]' value='<?=$CAFE_URL?>' style='width:300px;' /><br>
			간단메모 > <input type='text' id='memo_<?=$공구코드?>' name='memo[<?=$공구코드?>]' value='<?=$메모?>' style='width:300px;' /><br>
			<input type='button' value='수정' onclick="updateGpinfo('<?=$공구코드?>')" />
			<input type='button' value='발주' onclick="loadOrderDetail('orderlist','<?=$공구코드?>')" />
			<input type='button' value='주문자신청건' onclick="loadOrderDetail('allbuyerlist','<?=$공구코드?>')" />
			<script>$("#start_date_<?=$공구코드?>, #end_date_<?=$공구코드?>").datepicker();</script>
		</td>
	</tr>

	<tr id='<?=$공구코드?>_memo' class='gpinfolist DetailOff' height='80'>
		<td colspan='6'>
			<b>발주관련 메모</b>
			<textarea id='invoice_memo_<?=$공구코드?>' name='invoice_memo_<?=$공구코드?>' style='width:99%; height:150px;'><?=$발주메모?></textarea>
		</td>
		<td colspan='6'>
			<b>입고관련 메모</b>
			<textarea id='stock_memo_<?=$공구코드?>' name='stock_memo_<?=$공구코드?>' style='width:99%; height:150px;'><?=$입고메모?></textarea>
		</td>
	</tr>
	<tr id='<?=$공구코드?>' class='gpinfolist DetailOff'>
		<td id='gpinfodtl_<?=$공구코드?>' colspan='12'></td>
	</tr>

	<tr><td colspan='12' style='background-color:black; height:1px; padding:0px;'></td></tr>
	<?
	}
	?>
	</table>

	<?=get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['PHP_SELF']}?$qrystr&amp;page=");?>

</div>


<!-- link rel="stylesheet" href="http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css"-->

<script src="http://malsup.github.com/jquery.form.js"></script>

<script>
$(function(){
	$("#start_date, #end_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+90d" });
});



$(document).ready(function() {
    // bind 'myForm' and provide a simple callback function
    $('#gpinfo_form').ajaxForm(function(data) {
    	$("#gpinfo_loading").hide();
    	$("#gpinfo_complete").show();
    	document.location.reload();
			//disalbe 해제
    });
});


/*주문내역 다이얼로그 셋팅*/
$( "#dialog_orderDetail" ).dialog({
	autoOpen: false,
	width:'1250',
	height:'310'
});

/*공동구매추가 다이얼로그 셋팅*/
$( "#dialog" ).dialog({
	autoOpen: false,
	width:'auto'
});
$( "#opener" ).click(function() {
	$( "#dialog" ).dialog( "open" );
});

//DIALOG FORM SUBMIT
$( "#dialog_submit" ).click(function() {
	$("#gpinfo_tb").hide();
	$("#gpinfo_loading").show();
	$("#gpinfo_form").submit();
});

//DIALOG FORM RESET
$( "#dialog_reset" ).click(function() {
	$("#gpinfo_form").each(function(){
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

var ajaxLoadingImage = "<div style='margin:0px auto; width:260px; padding:100px;'><img src='/img/ajax-loader.gif' align=center /></div>";

/* 공구상품목록 로딩 */
function showItemList(id) {
	if($('#'+id).attr('class') == 'gpinfolist DetailOn') {
		$('#'+id).attr('class','gpinfolist DetailOff');
		$('#'+id+"_memo").attr('class','gpinfolist DetailOff');
	}
	else {
		$('#'+id).attr('class','gpinfolist DetailOn');
		$('#'+id+"_memo").attr('class','gpinfolist DetailOn');

		$('#gpinfodtl_'+id).html(ajaxLoadingImage);



		$.post('gpinfo_list.dtl.php',
				{
					'mode'		:	'itemlist',
					'gpcode'	: id,
					'start_date' : $('#start_date').val(),
					'end_date' : $('#end_date').val()
				},
				function( data ) {
					$('#gpinfodtl_'+id).html(data);
				}
		);
	}
}

/* 하단 주문내역 로딩 */
function loadOrderDetail(mode,gpcode,pk,hphone) {
	$("#dialog_orderDetail").dialog( "open" );
	$('#dialog_orderDetail_body').html(ajaxLoadingImage);


	/* 개별주문자별 총주문금액정보 */
	if(mode == 'orderlist_clayid') {
		$.post('gpinfo_list.dtl.php',
				{
					'mode'		:	mode,
					'gpcode'	: gpcode,
					'clay_id'	: pk,
					'hphone'	: hphone,
					'start_date' : $('#start_date').val(),
					'end_date' : $('#end_date').val()
				},
				function( data ) {
					$('#dialog_orderDetail_body').html(data);
				}
		);
	}

	/* 개별상품건별 총주문수량 목록 */
	if(mode == 'orderlist_itid') {
		$.post('gpinfo_list.dtl.php',
				{
					'mode'		:	mode,
					'gpcode'	: gpcode,
					'it_id'	: pk,
					'start_date' : $('#start_date').val(),
					'end_date' : $('#end_date').val()
				},
				function( data ) {
					$('#dialog_orderDetail_body').html(data);
				}
		);
	}

	/* 총주문목록 */
	if(mode == 'orderlist') {
		$('#ui-dialog-title-dialog_orderDetail').html("발주예정 내역");

		$.post('gpinfo_list.dtl.php',
			{
				'mode'		:	mode,
				'gpcode'	: gpcode,
				'start_date' : $('#start_date').val(),
				'end_date' : $('#end_date').val()
			},
			function( data ) {
				$('#dialog_orderDetail_body').html(data);
			}
		);
	}

	/* 전체 구매자의 각각의 신청건들 리스트 */
	if(mode == 'allbuyerlist') {

		$('#ui-dialog-title-dialog_orderDetail').html("주문자별 신청내역");

		$.post('gpinfo_list.dtl.php',
				{
					'mode'		:	mode,
					'gpcode'	: gpcode,
					'start_date' : $('#start_date').val(),
					'end_date' : $('#end_date').val()
				},
				function( data ) {
					$('#dialog_orderDetail_body').html(data);
				}
		);
	}

}


/* 공구내역 수정 */
function updateVolumePriceGpinfo(gpcode) {
	$.post('gpinfo_list.inp.php',
			{ 'mode'	: 'PRICE_UPDATE',
				'gpcode' : $('#gpcode_'+gpcode).val(),
				'volprice_yn' : $('input[name=volprice_yn_'+gpcode+']:checked').val()
			},
			function( data ) {
				if(data == 1) {
					alert('갱신 완료');
				} else {
					alert('갱신 실패');
				}
				//var content = $( data ).find( '#content' );
				//$( "#result" ).empty().append( content );
			}
	);
}


/* 공구내역 수정 */
function updateGpinfo(gpcode) {
	var cd1,cd2,cd3,cd4,cd5,cd6,total;

	/* 딜러업체 선택 */
	cd1 = $("input[id=cd1_"+gpcode+"]:checkbox:checked").attr('value');
	cd2 = $("input[id=cd2_"+gpcode+"]:checkbox:checked").attr('value');
	cd3 = $("input[id=cd3_"+gpcode+"]:checkbox:checked").attr('value');
	cd4 = $("input[id=cd4_"+gpcode+"]:checkbox:checked").attr('value');
	cd5 = $("input[id=cd5_"+gpcode+"]:checkbox:checked").attr('value');
	cd6 = $("input[id=cd6_"+gpcode+"]:checkbox:checked").attr('value');
	total = '|';
	total += (cd1) ? cd1+'|' : '';
	total += (cd2) ? cd2+'|' : '';
	total += (cd3) ? cd3+'|' : '';
	total += (cd4) ? cd4+'|' : '';
	total += (cd5) ? cd5+'|' : '';
	total += (cd6) ? cd6+'|' : '';



	$.post('gpinfo_list.inp.php',
					{ 'mode'	: 'mod',
						'gpcode' : $('#gpcode_'+gpcode).val(),
						'gpcode_name' : $('#gpcode_name_'+gpcode).val(),
						'menu_name' : $('#menu_name_'+gpcode).val(),
						'gp_type'	:	$('#gp_type_'+gpcode).val(),
						'links' : $('#links_'+gpcode).val(),
						'locks' : $('#locks_'+gpcode).val(),
						'solds' : $('#solds_'+gpcode).val(),
						'choice_dealer' : total,
						'baesongbi'		:	$('#baesongbi_'+gpcode).val(),
						'start_date'	: $('#start_date_'+gpcode).val(),
						'end_date'		: $('#end_date_'+gpcode).val(),
						'volprice_yn' : $('input[name=volprice_yn_'+gpcode+']:checked').val(),
						'sell_title' : $('input[name=sell_title_'+gpcode+']:checked').val(),
						'menu_view' : $('input[name=menu_view_'+gpcode+']:checked').val(),
						'list_view' : $('input[name=list_view_'+gpcode+']:checked').val(),
						'invoice_memo' : $('#invoice_memo_'+gpcode).val(),
						'stock_memo' : $('#stock_memo_'+gpcode).val(),
						'stats' : $("select[name=stats_"+gpcode+"]").val(),
						'memo' : $('#memo_'+gpcode).val(),
						'cafe_url' : $('#cafe_url_'+gpcode).val()

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