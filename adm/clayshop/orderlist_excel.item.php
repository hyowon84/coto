<?
include_once('./_common.php');

if($_GET['page']) {
	$page_name = "_".$_GET['page']."page";	
} else {
	$page_name = '';
	$page = '';
}

header("Content-Type: application/x-msexcel; name=\"gp-".date("ymd", time()).$page_name.".xls\"");
header("Content-Disposition: inline; filename=\"gp-".date("ymd", time()).$page_name.".xls\"");

$where = array();
$sql_search = "";




if ($search != "") {
	if ($sel_field != "") {
		switch($sel_field){
			case "it_name":
				$분할_검색어 = explode(" ",$search);

				for($s = 0; $s < count($분할_검색어); $s++) {
					$검색조건 .= " AND	IT.gp_name LIKE '%$분할_검색어[$s]%' ";
				}

				$where[] = " (1=1 $검색조건 ) ";

				break;
			case "it_id":
				$where[] = " IT.gp_id LIKE '$search%' ";
				break;
			case "clay_id":
				$where[] = " CI.clay_id LIKE '%".$search."%' ";
				break;
			case "name":		/* 주문자 */
				$where[] = " CI.name LIKE '%".$search."%' ";
				break;
			case "receipt_name":		/* 입금자 */
				$where[] = " CI.receipt_name LIKE '%".$search."%' ";
				break;
			default :
				//$where[] = " IT.{$sel_field} like '%$search%' ";
				break;
		}
	}

	if ($save_search != $search) {
		$page = 1;
	}
}

/*
 $where[] = " gp_code <> '' and od_cart_price > 0";

if($od_status){
$where[] = " od_status = '$od_status' ";
}

if($sfl_code2 != ""){
$where[] = " gp_code ='".$sfl_code2."' ";
}
*/

if ($fr_date && $to_date) {
	$where[] = " CL.od_date	BETWEEN	'$fr_date 00:00:00' and '$to_date 23:59:59' ";
}

if(count($_POST[stats]) > 0) {
	foreach($_POST[stats] as $val) {
		$stats_in .= "'$val',";
	}
	$stats_in = substr($stats_in,0,strlen($stats_in)-1);
} else {
	$stats_in = "'00','10','20','25','30'";
}


$sql_search = " WHERE	1=1
							AND	CL.stats IN ($stats_in)
";

if ($where) {
	$sql_search .= ' AND  '.implode(' and ', $where);
}


if ($sel_field == "")  $sel_field = "od_id";
if (!$sst) {
    $sst = "od_id";
    $sod = "desc";
}


if ($sel_field == "")  $sel_field = "CL.od_id";
if (!$sst) {
	$sst = "CL.od_id";
	$sod = "DESC";
}

$sql_order = " ORDER	BY	{$sst} {$sod} ";
$sql_common = " FROM		clay_order	CL
												LEFT JOIN clay_order_info CI ON (CI.od_id = CL.od_id)
												LEFT JOIN g5_shop_group_purchase IT ON (IT.gp_id = CL.it_id)
								$sql_search
								GROUP BY CL.it_id	";

/*  DB레코드 카운팅 */
$sql = "	SELECT	COUNT(CL.od_id) AS cnt
								,SUM(IT.gp_price * CL.it_qty) as total_price
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
$sql  = " SELECT		CL.it_id,
									IT.gp_name,
									SUM(CL.it_qty) AS total_qty,
									SUM(CL.it_qty * CL.it_org_price) AS total_org_price,
									SUM(CL.it_qty * IT.gp_price) AS total_price
			 $sql_common
			 
			 $sql_order 
			 $LIMIT ";

$result = sql_query($sql);


$EXCEL_STR = "
<table border='1'>
<tr>
   <td>날짜</td>
   <td colspan='3'>$fr_date ~ $to_date | </td>
</tr>
<tr>
   <td>상품코드</td>
   <td>상품명</td>
   <td>총주문수량</td>
   <td>주문시간기준 총금액</td>
</tr>";

for($j=0;$row =sql_fetch_array($result);$j++) {
	$총합계 = $row[it_price] * $row[it_qty];
	
	$주문상태 = $v_stats[$row[stats]];
	
	$주소조합 = ($row[addr1]) ? "$row[addr1] $row[addr2]" : "$row[addr1_2] $row[addr2]";
	
	$EXCEL_STR .= "
	<tr>
		<td>$row[it_id]</td>
		<td>$row[gp_name]</td>
		<td>$row[total_qty]</td>
		<td>$row[total_org_price]</td>
	</tr>
	";
}
  
$EXCEL_STR .= "</table>";



echo "<meta content=\"application/vnd.ms-excel; charset=UTF-8\" name=\"Content-type\"> ";
echo $EXCEL_STR;
?>