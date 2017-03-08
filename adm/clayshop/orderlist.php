<?
$sub_menu = '510100';
// $sub_sub_menu = '3';

include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$g5['title'] = '주문신청내역';
include_once (G5_ADMIN_PATH.'/admin.head.php');
include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');

$where = array();
$where2 = array();

$sql_search = "";

/* 통합검색 조건 start */
if ($search != "") {
	/* 상품명 */
	$분할_검색어 = explode(" ",$search);
	for($s = 0; $s < count($분할_검색어); $s++) {
		$검색조건 .= " AND	IT.gp_name LIKE '%$분할_검색어[$s]%' ";
	}
	$where[] =  "( (1=1 $검색조건 ) ";

	/* 공구명 */
	$where[] = " GI.gpcode_name LIKE '%$search%' ";

	/* 주문번호 */
	$where[] = " CL.od_id LIKE '%$search%' ";

	/* 상품코드 */
	$where[] = " IT.gp_id LIKE '%$search%' ";

	/* 닉네임 */
	$where[] = " CI.clay_id LIKE '%".$search."%' ";

	/* 주문자명 */
	$where[] = " CI.name LIKE '%".$search."%' ";

	/* 입금자명 */
	$where[] = " CI.receipt_name LIKE '%".$search."%' ";

	/* 주문자연락처 */
	$where[] = " CI.hphone LIKE '%".$search."%' ";

	/* 주소 */
	$where[] = "( CI.addr1 LIKE '%".$search."%' OR CI.addr1_2 LIKE '%".$search."%' OR CI.addr2 LIKE '%".$search."%'  )";

	/* 메모 */
	$where[] = " CI.memo LIKE '%".$search."%' ";

	/* 관리자 메모 */
	$where[] = " CI.admin_memo LIKE '%".$search."%'

	)";

	if ($save_search != $search) {
		$page = 1;
	}
} //if end
$sql_search = " WHERE	1=1 ";
if ($where) {
	$sql_search .= ' AND  '.implode(' OR ', $where);
}
/* 통합검색 조건 end */


/* 추가조건
 * 주문상태, 날짜, 현금영수증 신청여부, 수령방법
 *  */
if($stats) {
	$where2[] = "CL.stats = '$stats'";
}

if ($fr_date && $to_date) {
	$where2[] = " CL.od_date	BETWEEN	'$fr_date 00:00:00' and '$to_date 23:59:59' ";
}

if($where2) {
	$sql_search .= ' AND  '.implode(' AND ', $where2);
}


// if($sel_field == "")  $sel_field = "CL.od_id";
if(!$sst) {
	$sst = "CL.od_date DESC, CL.od_id DESC";
	#$sod = "DESC";
}


$rows = $config['cf_page_rows'];	//default : 200개 DB설정값
if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함



$sql_order = " ORDER	BY	{$sst} {$sod} ";

//(($USD * $_SESSION[unit_kor_duty]) * (1 + (($row[gp_charge]+$row[gp_duty])/100))) * 1.1;

$sql_common = " FROM		clay_order	CL
												LEFT JOIN clay_order_info CI ON (CI.od_id = CL.od_id)

												LEFT JOIN comcode DSN ON (DSN.ctype = 'clayorder' AND DSN.col = 'delivery_type' AND DSN.code = CI.delivery_type)
												LEFT JOIN comcode CSN ON (CSN.ctype = 'clayorder' AND CSN.col = 'cash_receipt_type' AND CSN.code = CI.cash_receipt_type)
												LEFT JOIN comcode PTN ON (PTN.ctype = 'clayorder' AND PTN.col = 'paytype' AND PTN.code = CI.paytype)

												LEFT JOIN g5_shop_group_purchase IT ON (IT.gp_id = CL.it_id)
												LEFT JOIN g5_shop_group_purchase_option GPO ON (GPO.gp_id = CL.it_id AND GPO.po_num = 0)
												LEFT JOIN gp_info GI ON (GI.gpcode = CL.gpcode)

												/* od_id별 실제 연결된 주문건수 */
												LEFT JOIN (			SELECT	T.od_id
																							,COUNT(T.od_id) AS cnt
																			FROM	(	SELECT	CL.od_id
																							FROM		clay_order CL
																											LEFT JOIN gp_info GI ON (GI.gpcode = CL.gpcode)
																											LEFT JOIN clay_order_info CI ON (CI.od_id = CL.od_id)
																											LEFT JOIN g5_shop_group_purchase IT ON (IT.gp_id = CL.it_id)
																							$sql_search
																							$sql_order
																							 LIMIT $from_record, $rows
																						) AS T
																			GROUP BY T.od_id
												) AS CLC ON (CLC.od_id = CL.od_id)

												/* 개인주문건별 총주문금액, 주문건수 */
												LEFT JOIN (		SELECT	CL.od_id,
																							COUNT(CL.od_id) AS order_cnt,
																							SUM( IF(GI.volprice_yn = 'Y', /*참:*/ (( GPO.po_cash_price * (SELECT USD FROM flow_price ORDER BY ymd DESC LIMIT 1) * (1+ (IT.gp_charge + IT.gp_duty)/100) ) * 1.1),  /*거짓:*/IT.gp_price) * CL.it_qty) AS total_price, /* 이전 주문건들은 gp_price를 사용하고 있어서 앞으로는 po_cash_price기준으로 사용  */
																							SUM( ROUND(CL.it_org_price/100)*100 * CL.it_qty) AS total_orgprice	/* 주문당시금액 */
																			FROM	clay_order CL
																						LEFT JOIN clay_order_info CI ON (CI.od_id = CL.od_id)
																						LEFT JOIN g5_shop_group_purchase IT ON (IT.gp_id = CL.it_id)
																						LEFT JOIN gp_info GI ON (GI.gpcode = CL.gpcode)
																						LEFT JOIN g5_shop_group_purchase_option GPO ON (GPO.gp_id = CL.it_id AND GPO.po_num = 0)
																			WHERE	1=1
																			AND		CL.stats NOT IN (99)
																			GROUP BY CL.od_id
												) AS CLS ON (CLS.od_id = CL.od_id)

						  ";//$sql_search

/*  DB레코드 카운팅 */
$sql = "	SELECT	COUNT(*) AS cnt,		/* 전체주문건수 */
									TT.realcnt,					/* 취소건 제외 주문건수 */
									TT.total_price,			/* 주문당시 기준 주문금액 총합 */
									TT.total_realprice,				/* 현재시세 기준 주문금액 총합 */
									CC.total_cancelprice,			/* 취소건 총합계액(주문당시 기준 주문금액 총합) */
									(	SELECT	COUNT(*)
										FROM		clay_order
										WHERE		stats IN ('00')
									) AS 'S00',
									(	SELECT	COUNT(*)
										FROM		clay_order
										WHERE		stats IN ('10')
									) AS 'S10',
									$total_입금요청건

									(	SELECT	COUNT(*)
										FROM		clay_order
										WHERE		stats = '20'
									) AS 'S20',
									(	SELECT	COUNT(*)
										FROM		clay_order
										WHERE		stats IN ('25','30','35')
									) AS 'S25'
					$sql_common
					,(		SELECT	COUNT(*) AS realcnt,
												SUM(IT.gp_price * CL.it_qty) AS total_realprice,
												SUM(CL.it_org_price * CL.it_qty) AS total_price
							FROM		clay_order CL
											LEFT JOIN gp_info GI ON (GI.gpcode = CL.gpcode)
											LEFT JOIN clay_order_info CI ON (CI.od_id = CL.od_id)
											LEFT JOIN g5_shop_group_purchase IT ON (IT.gp_id = CL.it_id)
							$sql_search
							AND	CL.stats != 99
					) AS TT
					,(		SELECT	COUNT(*) AS realcnt,
												SUM(IT.gp_price * CL.it_qty) AS total_realcancelprice,
												SUM(CL.it_org_price * CL.it_qty) AS total_cancelprice
							FROM		clay_order CL
											LEFT JOIN clay_order_info CI ON (CI.od_id = CL.od_id)
											LEFT JOIN g5_shop_group_purchase IT ON (IT.gp_id = CL.it_id)
							WHERE		CL.stats IN (00,10)
					) AS CC

					$sql_search

";
$sql_common .= " $sql_search ";

$row = sql_fetch($sql);
$total_count = $row['cnt'];
$total_price = $row['total_price'];
$total_cancelprice = $row['total_cancelprice'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산


/* DB레코드 목록 */
$sql  = " SELECT	CLC.cnt,									/* 화면에 보여지는 연결된 주문내역 개수 */
									CLS.order_cnt,						/* od_id 에 연결된 총 주문내역 */
									ROUND(CLS.total_price/100) * 100 AS total_price,
									CLS.total_orgprice,
									DSN.value AS delivery_type_nm,			/*  */
									CI.delivery_price,
									CSN.value AS cash_receipt_type_nm,		/*  */
									CI.cash_receipt_info,
									CL.number,
									CL.gpcode,					/* 공구코드 */
									GI.gpcode_name,			/* 공구명 */
									CL.od_id,						/* 주문번호*/
									CL.it_id,						/* 주문상품코드*/
									CL.it_qty,					/* 주문수량*/
									CL.it_org_price,		/* 주문당시 개당 상품가격*/

									/* 볼륨가적용 여부에 따른 가격 계산 */
									IF(GI.volprice_yn = 'Y',/*참:*/ ROUND((( GPO.po_cash_price * (SELECT USD FROM flow_price ORDER BY ymd DESC LIMIT 1) * (1+ (IT.gp_charge + IT.gp_duty)/100) ) * 1.1) /100)*100 ,/*거짓:*/ IT.gp_price) /*IF_END*/   AS gp_price, /* 이전 주문건들은 gp_price를 사용하고 있어서 앞으로는 po_cash_price기준으로 사용  */
									CL.stats,						/* 주문상태 */
									CL.print_yn,				/* 출력여부 */
									CI.od_id,						/* 주문ID*/
									CI.clay_id,
									CI.name,
									CI.receipt_name,
									CI.cash_receipt_yn,
									CI.cash_receipt_type,
									CI.cash_receipt_type,
									CI.cash_receipt_info,
									CI.hphone,
									CI.zip,
									CI.addr1,
									CI.addr1_2,
									CI.addr2,
									CI.memo,
									CI.admin_memo,
									CI.delivery_type,
									CI.delivery_company,
									CI.delivery_invoice,

									CI.od_date,
									CI.delivery_price,
									IT.gp_id,
									CL.it_name AS gp_name,
									CI.paytype,
									PTN.value AS paytype_name
				 $sql_common

				 $sql_order
				 LIMIT $from_record, $rows
";
$result = sql_query($sql);

echo "<textarea>";
echo $sql;
echo "</textarea>";


if($mode == 'jhw') echo "<textarea style='margin-bottom:10px;'>$sql</textarea>";

$qrystr = "sel_field=$sel_field&amp;sfl_code=$sfl_code&amp;fr_date=$fr_date&amp;to_date=$to_date&amp;sfl_code2=$sfl_code2&amp;od_status=$od_status&amp;stats=$stats&amp;search=$search&amp;save_search=$search&amp;sort1=$sort1&amp;sort2=$sort2&amp;stats=$stats&amp;page=$page";

$listall = '<a href="'.$_SERVER['PHP_SELF'].'" class="ov_listall">전체목록</a>';

?>
<div class="local_ov01 local_ov">
	<?=$listall;?>
	전체 주문내역 <?=number_format($total_count);?>건
	 | 주문 총 금액 <span style="color:blue"><?=number_format($total_price);?></span> 원 | 미입금 금액 <font color=red><?=number_format($total_cancelprice)?></font>원
	 <br>
	 <font color=red>주문신청(<?=$row[S00]?>건)</font> | <font color=red>입금요청(<?=$row[S10]?>건)</font> | <font color=blue>결제완료(<?=$row[S20]?>건)</font> | <font color=green>배송대기중(<?=$row[S25]?>건)</font>
	<? if($od_status == '상품준비중' && $total_count > 0) {?>
	<a href="./orderdelivery.php" id="order_delivery" class="ov_a">엑셀배송처리</a>
	<? }?>
</div>

클레이 상품링크 : <input type='text' size='70' value='http://coinstoday.co.kr/coto/order.php?it_id='>

<form name="forderlist2" id="forderlist2" class="local_sch02 local_sch">
<input type="hidden" name="doc" value="<?=$doc;?>">
<input type="hidden" name="sst" value="<?=$sst;?>">
<input type="hidden" name="sod" value="<?=$sod;?>">
<input type="hidden" name="sort1" value="<?=$sort1;?>">
<input type="hidden" name="sort2" value="<?=$sort2;?>">
<input type="hidden" name="page" value="<?=$page;?>">
<input type="hidden" name="save_search" value="<?=$search;?>">

<div class="sch_last">
	<strong>주문일자</strong>
	<input type="text" id="fr_date"  name="fr_date" value="<?=$fr_date;?>" class="frm_input" size="12" maxlength="10"> ~
	<input type="text" id="to_date"  name="to_date" value="<?=$to_date;?>" class="frm_input" size="12" maxlength="10">
	<button type="button" onclick="javascript:set_date('오늘');">오늘</button>
	<button type="button" onclick="javascript:set_date('어제');">어제</button>
	<button type="button" onclick="javascript:set_date('이번주');">이번주</button>
	<button type="button" onclick="javascript:set_date('지난주');">지난주</button>
	<button type="button" onclick="javascript:set_date('이번달');">이번달</button>
	<button type="button" onclick="javascript:set_date('지난달');">지난달</button>
	<button type="button" onclick="javascript:set_date('전체');">전체</button>
	&nbsp;&nbsp;&nbsp;


	<!-- select name="sel_field" id="sel_field">
		<option value="name" <?=get_selected($sel_field, 'name');?>>주문자</option>
		<option value="clay_id" <?=get_selected($sel_field, 'clay_id');?>>닉네임</option>
		<option value="od_id" <?=get_selected($sel_field, 'od_id');?>>주문번호</option>
		<option value="addr" <?=get_selected($sel_field, 'addr');?>>주문번호</option>
		<option value="it_name" <?=get_selected($sel_field, 'it_name');?>>상품명</option>
		<option value="it_id" <?=get_selected($sel_field, 'it_id');?>>상품코드</option>
		<option value="receipt_name" <?=get_selected($sel_field, 'receipt_name');?>>입금자</option>
	</select-->

	<label for="search" class="sound_only">검색어</label>
	검색어 :
	<input type="text" name="search" value="<?=$search;?>" id="search" class="frm_input" autocomplete="off">

	<b>주문상태</b>
	<select name="stats" id="top_stats">
		<option value=''>-전체보기-</option>
	<?
		foreach($v_stats as $key => $val) {
			echo "<option value='$key' ".get_selected($stats, $key)." style='background-color:".$v_stats_bgcolor[$key]."'>$val</option>";
		}
	?>
	</select>

	<input type="submit" value="검색" class="btn_submit">

</div>
</form>




<form name="forderlist3" id="forderlist3" method="post" autocomplete="off">
<input type="hidden" name="sel_field" value="<?=$sel_field;?>">
<input type="hidden" name="sst" value="<?=$sst;?>">
<input type="hidden" name="sod" value="<?=$sod;?>">
<input type="hidden" name="mode">
<input type="hidden" name="page" value="<?=$page?>">
<input type="hidden" name="od_status" value="<?=$od_status?>">
<input type="hidden" name="save_search" value="<?=$save_search?>">
<input type="hidden" name="search" value="<?=$search?>">
<input type="hidden" name="fr_date" value="<?=$fr_date?>">
<input type="hidden" name="to_date" value="<?=$to_date?>">
<input type="hidden" name="sfl_code" value="<?=$sfl_code?>">
<input type="hidden" name="sfl_code2" value="<?=$sfl_code2?>">

<div style="text-align:right; border:0px solid #d7d7d7; padding-right:5px;">
	<div style='width:auto; float:left; border:1px solid green; padding:5px;'>
		<b>현금영수증 [</b>
		<input type='radio' name='cash_receipt_yn' value='Y' />미출력
		<input type='radio' name='cash_receipt_yn' value='ALL' />모두출력
		<b>]</b>
	</div>
	<div style='width:auto; float:left; border:1px solid red; padding:5px;'>
		<b>주문상태 [</b>
		<?
		foreach($v_stats as $key => $val) {
			echo "<input type='checkbox' name='stats[]' value='$key' /> $val | ";
		}
		?>
		<b>]</b>
	</div>
	<div style='margin-left:10px; width:auto; border:1px solid gray; float:left;'>
		<div class="order_excel_all" style="display:inline-block;padding:5px;border:1px solid #ccc;background:#f0f0f0;text-decoration:none;cursor:pointer">EXC(전체)</div>
		<div class="order_excel_page" style="display:inline-block;padding:5px;border:1px solid #ccc;background:#f0f0f0;text-decoration:none;cursor:pointer">EXC(<?=$page?>page)</div>
		<div class="order_excel_item" style="display:inline-block;padding:5px;border:1px solid #ccc;background:#f0f0f0;text-decoration:none;cursor:pointer">EXC(품목별)</div>
		<div class="order_excel_cash" style="display:inline-block;padding:5px;border:1px solid #ccc;background:yellow;text-decoration:none;cursor:pointer">EXC(현금)</div>
	</div>
</div>
</form>

<br><br><br>

<form name="forderlist" id="forderlist" onsubmit="return forderlist_submit(this);" method="post" autocomplete="off">

	<input type='hidden' name='qrystr' value='<?=$qrystr?>' size=100>

	<input type="hidden" name="sel_field" value="<?=$sel_field;?>">
	<input type="hidden" name="sst" value="<?=$sst;?>">
	<input type="hidden" name="sod" value="<?=$sod;?>">
	<input type="hidden" name="mode">
	<input type="hidden" name="page" value="<?=$page?>">
	<input type="hidden" name="od_status" value="<?=$od_status?>">
	<input type="hidden" name="save_search" value="<?=$save_search?>">
	<input type="hidden" name="search" value="<?=$search?>">
	<input type="hidden" name="fr_date" value="<?=$fr_date?>">
	<input type="hidden" name="to_date" value="<?=$to_date?>">
	<input type="hidden" name="sfl_code" value="<?=$sfl_code?>">
	<input type="hidden" name="sfl_code2" value="<?=$sfl_code2?>">


	<div class="local_cmd01 local_cmd">

		<!--
		<select name="od_status" id="od_status">
			<option value="">전체</option>
			<option value="입금대기" <?=get_selected($od_status, '입금대기');?>>입금대기</option>
			<option value="결제완료" <?=get_selected($od_status, '결제완료');?>>결제완료</option>
			<option value="상품준비중" <?=get_selected($od_status, '상품준비중');?>>상품준비중</option>
			<option value="배송중" <?=get_selected($od_status, '배송중');?>>배송중</option>
			<option value="배송완료" <?=get_selected($od_status, '배송완료');?>>배송완료</option>
		</select>

		<label for="od_status" class="cmd_tit">주문상태 변경</label>
		<select name="od_chg_status" id="od_chg_status">
			<option value="입금대기">입금대기</option>
			<option value="결제완료">결제완료</option>
			<option value="상품준비중">상품준비중</option>
			<option value="배송중">배송중</option>
			<option value="배송완료">배송완료</option>
		</select>

		<input type="submit" value="우체국엑셀" class="btn_sms01" onclick="document.pressed=this.value">
		-->

		<strong>주문상태</strong>

	<select name="stats" id="stats">
		<option value=''>-전체보기-</option>
	<?
		foreach($v_stats as $key => $val) {
			echo "<option value='$key' ".get_selected($stats, $key)." style='background-color:".$v_stats_bgcolor[$key]."'>$val</option>";
		}
	?>
	</select>

	<input type="submit" value="선택SMS" class="btn_sms01" onclick="document.pressed=this.value">
	<input type="submit" value="선택수정" class="btn_submit" onclick="document.pressed=this.value">
	<!-- input type="submit" value="선택취소" class="btn_submit" onclick="document.pressed=this.value" -->
	<input type="button" value="주문서(개별)" class="btn_sms01" onclick="print_order('each')">
	<!-- input type="button" value="개별출력" class="btn_sms01" onclick="print_order('detail')"-->
	<input type='checkbox' name='not_send_sms' value='1' />문자발송방지

</div>

<div class="tbl_head01 tbl_wrap">
	<table id="sodr_list">
	<caption>주문 내역 목록</caption>
	<thead>
	<tr>
		<th>그룹<br>선택<br><input type="checkbox" name="grchkall" value="1" id="grchkall" onclick="group_check_all(this.form)"></th>
		<th scope="col" id="th_odid"  width='162'>
			<?=subject_sort_link('CL.od_id')?>접수번호</a><br>
			주문일시<br>
			총 주문금액<br>
			연결된 주문 건수<br>
			결제방법
		</th>
		<th scope="col">
			<label for="chkall" class="sound_only">주문상세내역 전체</label>
			개별<br>선택<br>
			<input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
		</th>
		<th scope="col">
			[<?=subject_sort_link('IT.gp_id')?>상품코드</a>]<?=subject_sort_link('IT.gp_name')?>상품명</a><br>요청사항메모
		</th>
		<th scope="col" width='40'>수량</th>
		<th scope="col" width='120'>
			신청당시 주문금액<br>
			현재기준 주문금액
		</th>
		<th scope="col" width='60'>주문상태</th>
		<th scope="col" width='40'>출력</th>
		<th scope="col" width='40'>현금<br>영수증</th>
		<th scope="col" width='50'>배송유형</th>
		<th scope="col" id="th_clayid">
			<?=subject_sort_link('clay_id')?>닉네임</a><br>
			<?=subject_sort_link('name')?>주문자</a> | <?=subject_sort_link('receipt_name')?>입금자성함</a><br>
			연락처
		</th>
		<th scope="col" width='300'>
			[우편번호] 배송지
		</th>
	</tr>
	</thead>
	<tbody>
	<?

for ($i=0; $row=sql_fetch_array($result); $i++)
{
	$묶음조건 = ($i == 0 || ($row[od_id] != $이전od_id));

	$uid = md5($row['od_id'].$row['od_time'].$row['od_ip']);

	if($묶음조건) {
		$oddtlcnt = 1;
		$g++;
	}

	//$bg = 'bg'.($g%2);


	$td_color = 0;

// 		$row[it_price] = getGroupPurchaseBasicPrice($row[it_id]);
	$신청당시주문금액 = number_format($row[it_org_price] * $row[it_qty]);
	$현재주문금액 = number_format($row[gp_price] * $row[it_qty]);

	$stats_style = ($v_stats_bgcolor[$row[stats]]) ? " style='background-color:".$v_stats_bgcolor[$row[stats]].";' " : "";
	$주문상태 = $v_stats[$row[stats]];
?>

	<tr class="orderlist<?=' '.$bg;?>" <?=$stats_style?> od_id='<?=$row[od_id]?>'>
<?
	//
	$분할단위배열 = array(2,4,4,4);
	$주문번호_리폼 = reform_substr($row[od_id],array(2,4,4,4),'-');
	if($묶음조건) {
?>
		<td rowspan='<?=($row[cnt])?>'><input type="checkbox" name="gr_chk[]" value="<?=$row[od_id]?>" id="gr_chk_<?=$i?>"></td>
		<td rowspan='<?=($row[cnt])?>'>
			<div style='color:red; font-weight:bold;'><?=$row[gpcode]?></div>
			<div style='color:red; font-weight:bold;'><?=$row[gpcode_name]?></div>
			<div style='color:red; font-weight:bold;'><?=$주문번호_리폼."<br>".$row[od_id]?></div>
			<div style='color:black; font-weight:bold;'><?=$row[od_date]?></div>
			<div style='color:blue; font-weight:bold;'>입금금액 : <?=number_format($row[total_orgprice]+$row[delivery_price])?>원</div>
			<div style='color:blue; font-weight:bold;'>주문당시 : <?=number_format($row[total_orgprice])?>원</div>
			<div style='color:blue; font-weight:bold;'>현재기준 : <?=number_format($row[total_price])?>원</div>
			<div style='color:red; font-weight:bold;'>연결된 주문 <?=number_format($row[order_cnt])?>건</div>
			<div style='color:red; font-weight:bold;'><?=$row[paytype].".".$row[paytype_name]?></div>
		</td>
	<?
	}
	?>
		<td class="td_chk" title='개별선택'>
			<input type="hidden" id="od_id_<?=$i?>" name="od_id[<?=$row[number]?>]" value="<?=$row['od_id']?>">
			<label for="chk_<?=$i;?>" class="sound_only">주문번호 <?=$row['od_id'];?></label>
			<input type="checkbox" name="chk[]" value="<?=$row[number]?>" id="chk_<?=$i?>"><br>
		</td>
		<td title='상품명' style='text-align:left;' onclick="showOrderDetail('<?=$row[od_id]?>');">
			[<a href='http://coinstoday.co.kr/coto/order.php?it_id=<?=$row[it_id]?>' target='_blank'><?=$row[it_id]?></a>] <?=$row[gp_name]?>
			<br><font color=red>신청기준단가:<?=number_format($row[it_org_price])?>원, 현재기준단가:<?=number_format($row[gp_price])?>원</font>
		</td>
		<td title='수량'><input type='text' name='it_qty[<?=$row[number]?>]' size='5'  value='<?=$row[it_qty]?>' style='text-align:right;' /></td>
		<td title='주문금액' style='text-align:right; padding-right:10px;'>
			<?=$신청당시주문금액?>원<br>
			<?=$현재주문금액?>원
		</td>
		<td title='주문상태' <?=$stats_style?>>

			<select id='paytype_[<?=$row[od_id]?>]' name='paytype[<?=$row[od_id]?>]'>
				<?
				$pay_sql = "SELECT	*
										FROM		comcode CC
										WHERE		CC.ctype = 'clayorder'
										AND			CC.col = 'paytype'
										ORDER BY CC.order ASC
				";
				$pay_result = sql_query($pay_sql);

				while($pay = mysql_fetch_array($pay_result)) {

					if($pay[code] == $row[paytype]) {
						$selected = "selected";
					} else {
						$selected = "";
					}

					echo "<option value='".$pay[code]."' ".$selected.">".$pay[value]."</option>";
				}

				?>
			</select>
			<?=$주문상태?>
		</td>
		<td title='출력'>
			<?=($row[print_yn] == 'Y') ? 'O' : ''?>
		</td>
<?
	if($묶음조건) {
?>
		<td title='현금영수증' rowspan='<?=$row[cnt]?>'>
			<select	name='cash_receipt_yn[<?=$row[od_id]?>]'>
				<option value='N'>X</option>
				<option value='Y' <?=($row[cash_receipt_yn] == 'Y') ? 'selected' : ''?>>O</option>
			</select>
		</td>
		<td title='배송방법' rowspan='<?=$row[cnt]?>'>
			<?=$v_delivery_type[$row[delivery_type]]?>
			<?=($row[delivery_price] > 0) ? '<br>'.$row[delivery_price].'원' : ''?>

			<select	id='delivery_type_<?=$row[od_id]?>'	name='delivery_type[<?=$row[od_id]?>]'>
				<option value=''></option>
			<?
				foreach($v_delivery_type as $key => $val) {
					echo "<option value='$key' ".get_selected($row[delivery_type],$key).">$val</option>";
				}
			?>
			</select>

		</td>
		<td id="th_nick" rowspan='<?=$row[cnt]?>'>
			<input type='text' name='clay_id[<?=$row[od_id]?>]' value='<?=$row[clay_id]?>' /> <br>

			<input type='text' name='name[<?=$row[od_id]?>]' value='<?=$row[name]?>' size=7 /> |
			<input type='text' name='receipt_name[<?=$row[od_id]?>]' value='<?=$row[receipt_name]?>' size=7 /> <br>

			<input type='text' name='hphone[<?=$row[od_id]?>]' value='<?=$row[hphone]?>' /> <br>
		</td>
		<td style='text-align:left;' rowspan='<?=$row[cnt]?>'>
			주소(지번) <input type='text' class='tdInp Addr' name='addr1[<?=$row[od_id]?>]' value='<?=$row[addr1]?>' /><br>
			주소(도로) <input type='text' class='tdInp Addr' name='addr1_2[<?=$row[od_id]?>]' value='<?=$row[addr1_2]?>' /><br>
			상세주소 <input type='text' class='tdInp Addr2' name='addr2[<?=$row[od_id]?>]' value='<?=$row[addr2]?>' /> ZIP <input type='text' class='tdInp Zip' name='zip[<?=$row[od_id]?>]' value='<?=$row[zip]?>' /><br>
			<?
				if($row[memo] || $row[admin_memo]) {
					echo "<br>";
				}
				if($row[memo]) {
					echo "<br><font color='red'><b>구매자 > ".cut_str($row[memo],18)."</b></font>";
				}
				if($row[admin_memo]) {
					echo "<br><font color='blue'><b>관리자 > ".cut_str($row[admin_memo],18)."</b></font>";
				}
			?>
		</td>
<?
	}
?>
	</tr>

<?	if($oddtlcnt == $row[cnt]) {	?>
	<tr class="orderlist<?=' '.$bg;?> orderDetailOff" <?=$stats_style?> id='<?=$row[od_id]?>'>
		<td colspan='14' style='padding:0px;'>
			<table>
				<thead>
				<tr>
					<th width='40%'>└▶ 주문로그</th>
					<th width='30%'>└▶ SMS로그</th>
					<th width='10%'>└▶ 현금영수증</th>
					<th width='10%'>└▶ 고객요청사항</th>
					<th width='10%'>└▶ 관리자메모</th>
				<tr>
					<td style='padding:0px;' title='주문변경이력로그'>

						<table class='orderlog'>
						<tr>
							<th style='padding:0px;'>변경내용</th>
							<th style='padding:0px;'>변경대상</th>
							<th style='width:48%; padding:0px;'>변경값</th>
							<th style='padding:0px;'>변경일시</th>
							<th style='padding:0px;'>관리자</th>
						</tr>
						<tbody style='overflow:auto; height:40px;'>
						<?
						$log_sql = "SELECT	LT.*,
																MB.*,
																CD.value AS code_name
												FROM		log_table LT
																LEFT JOIN g5_member MB ON (MB.mb_id = LT.admin_id)
																LEFT JOIN comcode CD ON (CD.ctype = 'clayorder' AND CD.col = 'stats' AND CD.code = LT.value)
												WHERE		LT.logtype = 'clayorder'
												AND		LT.gr_id = '$row[od_id]'
												ORDER BY LT.reg_date DESC
						";
						$log_result = sql_query($log_sql);
						$log_cnt = mysql_num_rows($log_result);

						while($log = mysql_fetch_array($log_result)) {

							if($log[pk_id] > 0 && $log[gr_id] != '') {
								$code_value = explode(' -> ',$log[value]);
								$code_value = $v_stats[$code_value[0]]." -> ".$v_stats[$code_value[1]];
							}
							else {
								$code_value = $log[value];
							}

						?>
						<tr>
							<td style='padding:0px;'><?=$log[memo]?></td>
							<td style='padding:0px;'><?=($log[it_id])?$log[it_id]:$log[gr_id]?></td>
							<td style='padding:0px;'><?=$code_value?></td>
							<td style='padding:0px;'><?=$log[reg_date]?></td>
							<td style='padding:0px;'><?=$log[mb_name]?></td>
						</tr>
						<?
						}
						if(!$log_cnt) {
							echo "<tr><td colspan='5'>로그가 존재하지 않습니다</td></tr>";
						}
						?>
						</tbody>
						</table>
					</td>
					<td style='padding:0px;' title='SMS로그'>
						<table class='orderlog'>
						<tr>
							<th style='padding:0px; width:75px;'>보낸날짜<br>담당자HP</th>
							<th style='width:48%; padding:0px;'>메시지</th>
							<th style='padding:0px; width:80px;'>받는사람HP</th>
						</tr>
						<tbody style='overflow:auto; height:40px;'>

						<?
						$sms_sql = "	SELECT	wr_no,				/*자동증가번호*/
																	wr_renum,
																	od_id,				/*관련된 주문번호*/
																	wr_reply,			/*보내는사람번호*/
																	wr_target,		/*받는사람번호*/
																	wr_message,		/*메시지내용*/
																	wr_memo,
																	wr_datetime		/*보낸날짜*/
													FROM		sms5_write
													WHERE		od_id = '$row[od_id]'
													ORDER 	BY wr_no DESC
						";
						$sms_result = sql_query($sms_sql);
						$sms_cnt = mysql_num_rows($sms_result);

						while($sms = mysql_fetch_array($sms_result)) {
						?>
						<tr>
							<td style='padding:0px;'>
								<?=$sms[wr_datetime]?><br>
								<?=$sms[wr_reply]?>
							</td>
							<td style='padding:0px; text-align:left;'><?=$sms[wr_message]?></td>
							<td style='padding:0px;'><?=$sms[wr_target]?></td>
						</tr>
						<?
						}
						if(!$sms_cnt) {
							echo "<tr><td colspan='5'>로그가 존재하지 않습니다</td></tr>";
						}
					?>
						</tbody>
						</table>
					</td>
					<td title='현금영수증'>
						<select name='cash_receipt_type[<?=$row[od_id]?>]'>
							<option>-선택안함-</option>
							<option value='C01' <?=($row[cash_receipt_type]=='C01') ? 'selected' : ''?>>개인소득공제</option>
							<option value='C02' <?=($row[cash_receipt_type]=='C02') ? 'selected' : ''?>>사업자지출증빙</option>
						</select>
						<input type='text' name='cash_receipt_info[<?=$row[od_id]?>]' value='<?=$row[cash_receipt_info]?>'>

					</td>
					<td style='text-align:left;' title='고객요청사항'><?=($row[memo]) ? "<font color='red'>메모 : {$row[memo]}</font>" : ""?></td>
					<td title='관리자메모'>
						송장번호 : <input type='text' name='delivery_invoice[<?=$row[od_id]?>]' value='<?=$row[delivery_invoice]?>' /><br>
						<textarea name='admin_memo[<?=$row[od_id]?>]' style='width:250px; height:80px;'><?=$row[admin_memo]?></textarea>
					</td>
				</tr>
				</thead>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan='14' bgcolor='black' style='height:1px; padding:0px; '></td>
	</tr>

<?	}

	$이전od_id = $row[od_id];
	$oddtlcnt++;
}
	mysql_free_result($result);
	if ($i == 0)
		echo '<tr><td colspan="14" class="empty_table">자료가 없습니다.</td></tr>';
   ?>
	</tbody>
	</table>
</div>
</form>

<form name="invoice_frm" id="invoice_frm" method="post" action="order_invoice_update.php" target="hiddenframe">
<input type="hidden" name="od_id">
<input type="hidden" name="od_invoice">
</form>

<?=get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['PHP_SELF']}?$qrystr&amp;page=");?>

<style>
.orderlist { cursor:pointer; }
.orderDetailOn { display:''; }
.orderDetailOff { display:none; }

.tdInp { height:20px; border:1px solid #d7d7d7; }
.tdInp.Zip { width:40px; }
.tdInp.Addr { width:200px; }
.tdInp.Addr2 { width:140px; }

.orderlogTd { padding:0px; height:20px; !important; }
</style>

<script>

$(function(){
	$("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });

	// 주문상품보기
	$(".orderitem").on("click", function() {
		var $this = $(this);
		var od_id = $this.text().replace(/[^0-9]/g, "");

		if($this.next("#orderitemlist").size())
			return false;

		$("#orderitemlist").remove();

		$.post(
			"./ajax.orderitem.php",
			{ od_id: od_id },
			function(data) {
				$this.after("<div id=\"orderitemlist\"><div class=\"itemlist\"></div></div>");
				$("#orderitemlist .itemlist")
					.html(data)
					.append("<div id=\"orderitemlist_close\"><button type=\"button\" id=\"orderitemlist-x\" class=\"btn_frmline\">닫기</button></div>");
			}
		);

		return false;
	});

	// 상품리스트 닫기
	$(".orderitemlist-x").on("click", function() {
		$("#orderitemlist").remove();
	});

	$("body").on("click", function() {
		$("#orderitemlist").remove();
	});

	$(".order_tax_excel").click(function(){
		$("form[name='forderlist3']").attr("action", "./orderlist_tax_excel.php").submit();
	});

	//엑셀 다운로드
	$(".order_excel_page").click(function(){
		$("form[name='forderlist3']").attr("action", "./orderlist_excel.php?page="+"<?=$page?>").submit();
	});

	//엑셀다운전체
	$(".order_excel_all").click(function(){
		$("form[name='forderlist3']").attr("action", "./orderlist_excel.php").submit();
	});

	//엑셀다운품목별
	$(".order_excel_item").click(function(){
		$("form[name='forderlist3']").attr("action", "./orderlist_excel.item.php").submit();
	});

	//엑셀다운전체
	$(".order_excel_cash").click(function(){
		$("form[name='forderlist3']").attr("action", "./cashreceipt_excel.php").submit();
	});

	// 엑셀배송처리창
	$("#order_delivery").on("click", function() {
		var opt = "width=600,height=450,left=10,top=10";
		window.open(this.href, "win_excel", opt);
		return false;
	});

	<?if($sfl_code){?>
	$.ajax({
		type: "POST",
		dataType: "HTML",
		url: "../shop_admin/_Ajax.grouppurchase_appli_list.php",
		data: "status=gpcode_status&gp_code=<?=$sfl_code?>&sfl_code2=<?=$sfl_code2?>",
		success: function(data){
			//$(".test").html(data);
			$("select[name='sfl_code2']").html(data);
		}
	});
	<?}?>


	$("select[name='sfl_code']").change(function(){
		var gp_code = $(this).val();
		$.ajax({
			type: "POST",
			dataType: "HTML",
			url: "../shop_admin/_Ajax.grouppurchase_appli_list.php",
			data: "status=gpcode_status&gp_code=" + gp_code,
			success: function(data){
				//$(".test").html(data);
				$("select[name='sfl_code2']").html(data);
			}
		});
	});

	$(".od_tax_state").change(function(){
		var od_idx = $(this).attr("od_idx");
		var od_tax_state = $(this).val();
		$.ajax({
			type: "POST",
			dataType: "HTML",
			url: "_Ajax.orderlist_tax_update.php",
			data: "od_idx=" + od_idx+"&od_tax_state=" + od_tax_state,
			success: function(data){
			}
		});
	});
});

function chg_invoice(od_id,idx)
{
	var f = document.invoice_frm;

	f.od_id.value = od_id;
	f.od_invoice.value = $("#od_invoice_"+idx).val();
	f.submit();
}

function set_date(today)
{
	<?
	$date_term = date('w', G5_SERVER_TIME);
	$week_term = $date_term + 7;
	$last_term = strtotime(date('Y-m-01', G5_SERVER_TIME));
   ?>
	if (today == "오늘") {
		document.getElementById("fr_date").value = "<?=G5_TIME_YMD;?>";
		document.getElementById("to_date").value = "<?=G5_TIME_YMD;?>";
	} else if (today == "어제") {
		document.getElementById("fr_date").value = "<?=date('Y-m-d', G5_SERVER_TIME - 86400);?>";
		document.getElementById("to_date").value = "<?=date('Y-m-d', G5_SERVER_TIME - 86400);?>";
	} else if (today == "이번주") {
		document.getElementById("fr_date").value = "<?=date('Y-m-d', strtotime('-'.$date_term.' days', G5_SERVER_TIME));?>";
		document.getElementById("to_date").value = "<?=date('Y-m-d', G5_SERVER_TIME);?>";
	} else if (today == "이번달") {
		document.getElementById("fr_date").value = "<?=date('Y-m-01', G5_SERVER_TIME);?>";
		document.getElementById("to_date").value = "<?=date('Y-m-d', G5_SERVER_TIME);?>";
	} else if (today == "지난주") {
		document.getElementById("fr_date").value = "<?=date('Y-m-d', strtotime('-'.$week_term.' days', G5_SERVER_TIME));?>";
		document.getElementById("to_date").value = "<?=date('Y-m-d', strtotime('-'.($week_term - 6).' days', G5_SERVER_TIME));?>";
	} else if (today == "지난달") {
		document.getElementById("fr_date").value = "<?=date('Y-m-01', strtotime('-1 Month', $last_term));?>";
		document.getElementById("to_date").value = "<?=date('Y-m-t', strtotime('-1 Month', $last_term));?>";
	} else if (today == "전체") {
		document.getElementById("fr_date").value = "";
		document.getElementById("to_date").value = "";
	}
}


function showOrderDetail(od_id) {
	if($('#'+od_id).attr('class') == 'orderlist orderDetailOn') {
		$('#'+od_id).attr('class','orderlist orderDetailOff');
	}
	else {
		$('#'+od_id).attr('class','orderlist orderDetailOn');
	}
}

function forderlist_submit(f)
{
	if (!is_checked("chk[]") && !is_checked("gr_chk[]")) {
		alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
		return false;
	}

	if(document.pressed == "선택취소") {
		if(confirm("선택한 자료를 정말 취소하시겠습니까?")) {
			f.action = "./orderlistdelete.php";
			return true;
		}
		return false;
	}
	if(document.pressed == "선택수정") {
		if(confirm("선택한 자료를 수정하시겠습니까?")) {
			f.action = "./orderlistupdate.php";
			return true;
		}
		return false;
	}
	else if(document.pressed == "우체국엑셀"){

		f.action = "./orderlist_tax_select_excel.php";
		return true;
	}
	else  if(document.pressed == "선택SMS") {
		window.open("","_sms","left=10,top=10,width=500,height=850,scrollbars=yes");
		f.target="_sms";
		f.action = "<?=G5_PLUGIN_URL?>/sms5/clay_sms.php";
		return true;
	}

	var change_status = f.od_chg_status.value;


	if (!confirm("선택하신 주문서의 주문상태를 '"+change_status+"'상태로 변경하시겠습니까?"))
		return false;

	f.action = "./orderlistupdate.php";
	return true;
}

/* 인쇄관련 전체인쇄/선택인쇄 */
function print_order(mode) {
	var f = document.forderlist;
	var stats = $('#top_stats').val();

	/*
	if(yn == 'Y') {
		quest = confirm('이미 출력한적이 있습니다. 재출력하시겠습니까?');
	} else {
		quest = true;
	}
	*/

	/* 선택인쇄만 */
	/*
	if(mode == 'CHOICE') {
		var v_list = '';

		$('.chkCart').each(function(e){
			if($(this).attr('checked') == 'checked') {
				v_list += $(this).val()+',';
			}
		});

		if(!v_list || v_list == '') {
			alert('선택된 주문내역이 없습니다. 주문내역을 선택해주세요.');
			return false;
		}

		od_id = v_list.substring(0,v_list.length-1);
	}*/

	window.open("","_print");
	f.target="_print";
	f.action = 'order_printpage.php?mode='+mode+'&stats='+stats;
	f.submit();

	return;
}

/*
function forderlist_sms(f)
{
	if (!is_checked("chk[]")) {
		alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
		return false;
	}

	if(document.pressed == "선택삭제") {
		if(confirm("선택한 자료를 정말 삭제하시겠습니까?")) {
			f.action = "./orderlistdelete.php";
			return true;
		}
		return false;
	}

	var change_status = f.od_chg_status.value;


	if (!confirm("선택하신 주문서의 주문상태를 '"+change_status+"'상태로 변경하시겠습니까?"))
		return false;

	f.action = "./orderlistupdate.php";
	return true;
}
*/
</script>
<!-- 새창 대신 사용하는 iframe -->
<iframe width=0 height=0 name='hiddenframe' style='display:none;'></iframe>
<?
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>