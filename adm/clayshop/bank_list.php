<?php
$sub_menu = '510200';
// $sub_sub_menu = '2';

include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$g5['title'] = '입/출금내역';
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
<?
$bd_sql = "	SELECT	BD.bank_type,
										CC.value,
										SUM(BD.input_price) AS IPV,
										SUM(BD.output_price) AS OPV,
										TT.TIPV,
										TT.TOPV,
										SUM(BD.input_price)/TT.TIPV*100 AS PC_IP,
										SUM(BD.output_price)/TT.TOPV*100 AS PC_OP
						FROM		bank_db BD
										LEFT JOIN comcode CC ON (CC.ctype = 'bankdb' AND CC.col = 'bank_type' AND CC.code = BD.bank_type)
										,(	SELECT	SUM(input_price) AS TIPV,
																SUM(output_price) AS TOPV
												FROM		bank_db
												WHERE		bank_type LIKE 'B%'
											) TT
						WHERE		BD.bank_type LIKE 'B%'
						GROUP BY BD.bank_type
";
$bd_result = sql_query($bd_sql);

while($bd = mysql_fetch_array($bd_result)) {

	if(!$bd[value]) continue;

	$인덱스명 .= "'$bd[value]',";

	$차트1 .= "$bd[PC_IP],";
	$차트2 .= "$bd[PC_OP],";
}

$인덱스명 = "[".substr($인덱스명,0,strlen($인덱스명)-1)."]";
$차트1 = "[".substr($차트1,0,strlen($차트1)-1)."]";
$차트2 = "[".substr($차트2,0,strlen($차트2)-1)."]";



?>

<div style='width:100%; height:230px;'>

<div style='float:left;'>
	<div style='margin:0px auto; text-align:center; font-size:1.5em; font-weight:bold;'>입금 분포</div>
	<div id="chart1"></div>
</div>

<div style='float:left;'>
	<div style='margin:0px auto; text-align:center; font-size:1.5em; font-weight:bold;'>출금 분포</div>
	<div id="chart2"></div>
</div>

</div>

<script>
	var options = {
		'dataset':{
			title: 'Web accessibility status',
			values:<?=$차트1?>,
			colorset: ['#2EB400', '#2BC8C9', "#666666", '#f09a93', '#ff49fd', '#fbff8b', '#5599ff'],
			fields: <?=$인덱스명?>,
		},
		'donut_width' : 100,
		'core_circle_radius':0,
		'chartDiv': 'chart1',
		'chartType': 'pie',
		'chartSize': {width:600, height:230}
	};
	Nwagon.chart(options);

	var options = {
			'dataset':{
				title: 'Web accessibility status',
				values:<?=$차트2?>,
				colorset: ['#2EB400', '#2BC8C9', "#666666", '#f09a93', '#ff49fd', '#fbff8b', '#5599ff'],
				fields: <?=$인덱스명?>,
			},
			'donut_width' : 100,
			'core_circle_radius':0,
			'chartDiv': 'chart2',
			'chartType': 'pie',
			'chartSize': {width:600, height:230}
		};
		Nwagon.chart(options);
</script>

<div style='width:100%; ;'></div>

<form class='local_sch'>
	<div style='float:left; margin-left:10px;'>

		<input type='button' id="opener" value="입출금목록입력" />

		<input type='button' id="cashtax" value="엑셀(현금영수증)" onclick="window.open('cashreceipt_excel.php','_blank')" />

	</div>
	<div style='float:left; margin-left:50px;'>
		<label for="search" class="sound_only">검색어</label>
		검색어 : <input type="text" name="search" value="<?=$search;?>" id="search" class="frm_input" autocomplete="off">
	</div>&nbsp;
	<input type="submit" value="검색" class="btn_submit">
</form>



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
$common_sql = "	SELECT	BD.*,
												CC.bgcolor
								FROM		bank_db BD
												LEFT JOIN comcode CC ON (CC.ctype = 'bankdb' AND CC.col = 'bank_type' AND CC.code = BD.bank_type)
								WHERE		1=1
								$sql_search
								ORDER BY 	BD.tr_date DESC, BD.tr_time DESC
";
$sql = $common_sql."	LIMIT $from_record, $rows";
$result = sql_query($sql);

$total_count = mysql_num_rows(sql_query($common_sql));
// $total_price = $row['total_price'];
// $total_cancelprice = $row['total_cancelprice'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
?>


<div id='divBankTableArea' style='width:100%;'>
	<table width='100%'>
	<tr>
		<th width='120'>계좌명</th>
		<th width='120'>거래일시</th>
		<th width='120'>거래시간</th>
		<th width='120'>입금액</th>
		<th width='120'>출금액</th>
		<th width='120'>거래자명</th>
		<th width='30'>연관</th>
		<th width='40'>입력</th>
		<th width='40'>유형</th>
		<th width='60'>세금<br>처리유형</th>
		<th width='100'>세금<br>처리번호</th>
		<th>연결된 주문번호</th>
		<th>메모</th>
	</tr>
	<?
	while($row = mysql_fetch_array($result)) {
		$계좌명 = $row[account_name];
		$거래일 = $row[tr_date];
		$거래시간 = $row[tr_time];
		$입금액 = number_format($row[input_price]);
		$출금액 = number_format($row[output_price]);
		$거래자명 = $row[trader_name];
		$연결주문번호 = $row[admin_link];
		$관리자메모 = $row[admin_memo];


		/* 입금내역 정보를 기준으로 연관된 주문내역 검색 */
		$검색어 = substr(trim($거래자명),0,9);


		$연관주문번호목록 = '';
		$tmp = explode(',',$row[admin_link]);

		for($i = 0; $i < count($tmp); $i++) {
			$연관주문번호목록 .= "'".$tmp[$i]."',";
		}
		$연관주문번호목록 = substr($연관주문번호목록,0,strlen($연관주문번호목록)-1);

		$find_sql = "	SELECT	IFNULL(CC.cnt,0) AS cancel,		/* 취소 */
													IFNULL(CR.cnt,0) AS request,	/* 주문신청, 입금요청 */
													IFNULL(CP.cnt,0) AS payok,		/* 결제완료 */
													CLS.*,
													CI.*
								  FROM		(	SELECT	CL.od_id,
																		COUNT(CL.od_id) AS order_cnt,
																			SUM(IT.it_price * CL.it_qty) AS total_price,
																			SUM(CL.it_org_price * CL.it_qty) AS total_orgprice
														FROM	clay_order CL
																	LEFT JOIN clay_order_info CI ON (CI.od_id = CL.od_id)
																	LEFT JOIN g5_shop_item IT ON (IT.it_id = CL.it_id)
														GROUP BY CL.od_id
													) AS CLS
													LEFT JOIN clay_order_info CI ON (CI.od_id = CLS.od_id)
													LEFT JOIN (	SELECT	od_id,
																							COUNT(*) as cnt
																			FROM		clay_order
																			WHERE		stats = 99
																			GROUP BY od_id
													) CC ON (CC.od_id = CLS.od_id)
													LEFT JOIN (	SELECT	od_id,
																							COUNT(*) as cnt
																			FROM		clay_order
																			WHERE		stats IN ('00','10')
																			GROUP BY od_id
													) CR ON (CR.od_id = CLS.od_id)
													LEFT JOIN (	SELECT	od_id,
																							COUNT(*) as cnt
																			FROM		clay_order
																			WHERE		stats >= 20
																			AND			stats <= 60
																			GROUP BY od_id
													) CP ON (CP.od_id = CLS.od_id)
									WHERE	1=1
									AND		(	(CLS.total_orgprice + CI.delivery_price) = '$row[input_price]'	)

									AND		(
													CI.clay_id	LIKE	'%$검색어%'
													OR		CI.name		LIKE	'%$검색어%'
													OR		CI.receipt_name	LIKE	'%$검색어%'
									)

									OR		CI.od_id IN ($연관주문번호목록)

									ORDER	BY	CLS.od_id DESC
		";
		$find_result = sql_query($find_sql);
		$연관건수 = mysql_num_rows($find_result);


		if($연관건수 > 0) {
			$link_color = "yellow";
		} else {
			$link_color = "";
		}

		/* 유형선택시 색변경 */
		if($row[bank_type]) {
			$bgcolor = "style='background-color:$row[bgcolor];'";
		} else {
			$bgcolor = '';
		}

// 		echo "<textarea>";
// 		echo $find_sql;
// 		echo "</textarea>"

	?>
	<tr class='bank_tr <?=$link_color?>' id='<?=''?>' <?=$bgcolor?>>
		<td onclick="showDetail('b<?=$row[number]?>')"><?=$계좌명?></td>
		<td onclick="showDetail('b<?=$row[number]?>')"><?=$거래일?></td>
		<td onclick="showDetail('b<?=$row[number]?>')"><?=$거래시간?></td>
		<td onclick="showDetail('b<?=$row[number]?>')" style='text-align:right; padding-right:5px;'><?=$입금액?></td>
		<td onclick="showDetail('b<?=$row[number]?>')" style='text-align:right; padding-right:5px;'><?=$출금액?></td>
		<td onclick="showDetail('b<?=$row[number]?>')"><?=$거래자명?></td>
		<td onclick="showDetail('b<?=$row[number]?>')"><?=$연관건수?></td>
		<td><input type='button' id='bt_<?=$row[number]?>' value='수정' onclick="updateBankData('<?=$row[number]?>')" /></td>
		<td>
			<select	id='bank_type_<?=$row[number]?>' name='bank_type_<?=$row[number]?>'>
				<option>-유형선택-</option>
			<?
			$banktype_sql = "	SELECT	*
												FROM		comcode CC
												WHERE		CC.ctype = 'bankdb'
												AND			CC.col = 'bank_type'
												ORDER BY CC.order ASC
			";
			$banktype_result = sql_query($banktype_sql);

			while($bt = mysql_fetch_array($banktype_result)) {
				$selected = ($bt[code] == $row[bank_type]) ? 'selected' : '';
			?>
				<option value='<?=$bt[code]?>' <?=$selected?> style='background-color:<?=$bt[bgcolor]?>'><?=$bt[value]?></option>
			<?
			}
			?>
			</select>
		</td>

		<td>
			<select	id='tax_type_<?=$row[number]?>' name='tax_type_<?=$row[number]?>'>
				<option value=''>-유형선택-</option>
			<?
			$taxtype_sql = "	SELECT	*
												FROM		comcode CC
												WHERE		CC.ctype = 'bankdb'
												AND			CC.col = 'tax_type'
												ORDER BY CC.order ASC
			";
			$taxtype_result = sql_query($taxtype_sql);

			while($bt = mysql_fetch_array($taxtype_result)) {
				$selected = ($bt[code] == $row[tax_type]) ? 'selected' : '';
			?>
				<option value='<?=$bt[code]?>' <?=$selected?> style='background-color:<?=$bt[bgcolor]?>'><?=$bt[value]?></option>
			<?
			}
			?>
			</select>
		</td>
		<td><input class='banklist_inp_text' type='text' id='tax_no_<?=$row[number]?>' name='tax_no_[<?=$row[number]?>]' value='<?=$row[tax_no]?>' /></td>
		<td><input class='banklist_inp_text' type='text' id='admin_link_<?=$row[number]?>' name='admin_link[<?=$row[number]?>]' value='<?=$연결주문번호?>' /></td>
		<td><input class='banklist_inp_text' type='text' id='admin_memo_<?=$row[number]?>' name='admin_memo[<?=$row[number]?>]' value='<?=$관리자메모?>' /></td>
	</tr>

	<tr id='b<?=$row[number]?>' class='banklist DetailOff'><td colspan='13'>
		<?
			while($find = mysql_fetch_array($find_result)) {

				$주문번호_리폼 = reform_substr($find[od_id],array(2,4,4,4),'-');

				echo "[주문번호: $find[od_id]| <b>$주문번호_리폼</b> (<font color='red'><b>주문신청: $find[request]건</b></font>, <font color='gray'>결제완료부터 배송처리: $find[payok]건, 취소: $find[cancel]건</font> ) / 주문상태: $find[stats] / <b><font color='blue'>상품주문금액: ".number_format($find[total_orgprice])."</font></b> / 설정된 택배비: ".number_format($find[delivery_price])." / 닉네임: $find[clay_id] / 이름: $find[name] / 입금자명:$find[receipt_name] ] <br>";
			}
		?>
	</td></tr>
	<tr><td colspan='13' style='background-color:black; height:1px; padding:0px;'></td></tr>
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
						'tax_type'	:	$("select[name=tax_type_"+number+"]").val(),
						'tax_no': $('#tax_no_'+number).val(),
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