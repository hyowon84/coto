<?
include_once('./_common.php');

if($_GET['page']) {
	$page_name = "_".$_GET['page']."page";
} else {
	$page_name = '';
	$page = '';
}

$where = array();
$sql_search = "";

/* 통합검색 조건 start */
if ($search != "") {
	/* 상품명 */
	$분할_검색어 = explode(" ",$search);
	for($s = 0; $s < count($분할_검색어); $s++) {
		$검색조건 .= " AND	IT.gp_name LIKE '%$분할_검색어[$s]%' ";
	}
	$where[] =  "( (1=1 $검색조건 ) ";

	/* 주문번호 */
	$where[] = " CL.od_id LIKE '%$search%' ";

	/* 공구코드 */
	$where[] = " CL.gpcode LIKE '%$search%' ";

	/* 공구명 */
	$where[] = " GI.gpcode_name LIKE '%$search%' ";

	/* 상품코드 */
	$where[] = " IT.gp_id LIKE '%$search%' ";

	/* 클레이닉네임 */
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
/* 주문상태 체크박스대로 선택 */
if(count($_POST[stats]) > 0) {
	foreach($_POST[stats] as $val) {
		$stats_in .= "'$val',";
	}
	$stats_in = substr($stats_in,0,strlen($stats_in)-1);
} else {	/* 체크박스가 없을경우 취소를 제외한 모든 상태 */
	$stats_in = "'00','10','20','25','30','35','40','50','60'";
}
$sql_search = " WHERE	1=1
							AND	CL.stats IN ($stats_in) ";

/* 날짜조건 */
if ($fr_date && $to_date) {
	$where[] = " CL.od_date	BETWEEN	'$fr_date 00:00:00' and '$to_date 23:59:59' ";
}

/* 현금영수증 신청여부 */
if($cash_receipt_yn == 'Y') {
	$where[] = " CI.cash_receipt_yn = 'Y' ";
}



if ($where) {
	$sql_search .= ' AND  '.implode(' OR ', $where);
}


if ($sel_field == "")  $sel_field = "od_id";
if (!$sst) {
    $sst = "od_id";
    $sod = "desc";
}


// if ($sel_field == "")  $sel_field = "CL.od_id";
if (!$sst) {
	$sst = "CL.od_id";
	$sod = "DESC";
}

$sql_order = " ORDER	BY	{$sst} {$sod} ";


$sql_common = " FROM		clay_order	CL
												LEFT JOIN clay_order_info CI ON (CI.od_id = CL.od_id)
												LEFT JOIN g5_shop_group_purchase IT ON (IT.gp_id = CL.it_id)
												LEFT JOIN gp_info GI ON (GI.gpcode = CL.gpcode)
								$sql_search

";

/*  DB레코드 카운팅 */
$sql = "	SELECT		COUNT(CL.od_id) AS cnt
										,SUM(CL.it_org_price * CL.it_qty) as total_price
					$sql_common
";
$row = sql_fetch($sql);
$total_count = $row['cnt'];
$total_price = $row['total_price'];



$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

if($_GET['page'] >= '0') {
	$LIMIT = "LIMIT		$from_record, $rows";
}

/* DB레코드 목록 */
$sql  = "	SELECT		CL.*,
										CI.*,
										CL.gpcode,
										GI.gpcode_name,
										IT.gp_name,
										IT.gp_price
					$sql_common

					$sql_order
					$LIMIT ";

$result = sql_query($sql);



/* 데이터 확인시 아래 두줄만 주석처리*/
header("Content-Type: application/x-msexcel; name=\"gp-".date("ymd", time()).$page_name.".xls\"");
header("Content-Disposition: inline; filename=\"gp-".date("ymd", time()).$page_name.".xls\"");
// echo $sql; exit;


$EXCEL_STR = "
<table border='1'>
<tr>
	<td>공구코드</td>
	<td>주문번호</td>
  <td>닉네임</td>
  <td>성명</td>
  <td>전화번호</td>
  <td>주소</td>
  <td>우편번호</td>
  <td>상품코드</td>
  <td>상품명</td>
  <td>주문수량</td>
  <td>단가</td>
  <td>총합계</td>
  <td>입금자성함</td>
  <td>주문상태</td>
  <td>주문일시</td>
  <td>메모</td>
</tr>";

for($j=0;$row =sql_fetch_array($result);$j++) {
	$총합계 = $row[it_org_price] * $row[it_qty];

	$주문상태 = $v_stats[$row[stats]];

	$주소조합 = ($row[addr1]) ? "$row[addr1] $row[addr2]" : "$row[addr1_2] $row[addr2]";

	$EXCEL_STR .= "
	<tr>
		<td>$row[gpcode]</td>
		<td>$row[od_id]</td>
		<td>$row[clay_id]</td>
		<td>$row[name]</td>
		<td>$row[hphone]</td>
		<td>$주소조합</td>
		<td>$row[zip]</td>
		<td>$row[it_id]</td>
		<td>$row[it_name]</td>
		<td>$row[it_qty]</td>
		<td>$row[it_org_price]</td>
		<td>$총합계</td>
		<td>$row[receipt_name]</td>
		<td>$주문상태</td>
		<td>$row[od_date]</td>
		<td>구매자) $row[memo] | 관리자) $row[admin_memo]</td>
	</tr>
	";
}

$EXCEL_STR .= "</table>";



echo "<meta content=\"application/vnd.ms-excel; charset=UTF-8\" name=\"Content-type\"> ";
echo $EXCEL_STR;
?>