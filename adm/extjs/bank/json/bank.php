<?
include "_common.php";
$json = array();
$data = array();



/* REMOTE SORT */
$sort = json_decode(str_replace('\"','"',$_GET[sort]),true);

for($i = 0; $i < count($sort); $i++) {
	if($i == 0) {
		$ORDER_BY = "ORDER BY ".$sort[$i][property]." ".$sort[$i][direction];
	}
	else {
		$ORDER_BY .= ",".$sort[$i][property]." ".$sort[$i][direction];
	}
}


$OR_SQL = "";

/* 입출금목록 */
if($mode == 'banklist') {
	if($keyword) {
		$분할_검색어 = explode(" ",$keyword);
		$거래자명 = $관리자메모 = Array();

		$조건목록 = array();
		for($s = 0; $s < count($분할_검색어); $s++) {
			$조건 = array();
			
			/* WHERE밑에 바로 들어가는조건 OR로 분리
				 검색영역 : 거래자명, 관리자메모, 세금처리번호, 연결주문번호
			*/
			$조건[] = " BD.trader_name LIKE '%$분할_검색어[$s]%' ";
			$조건[] = " BD.admin_memo LIKE '%$분할_검색어[$s]%' ";
			$조건[] = " BD.tax_no LIKE '%$분할_검색어[$s]%' ";
			$조건[] = " BD.admin_link LIKE '%$분할_검색어[$s]%' ";
			
			$조건목록[] = "(".implode(' OR ',$조건). ")";
			/*발주품목에 관련된 조건, 카운팅 하나라도 있을시 집계*/
		}
		$AND_SQL .= "	AND	".implode(' AND ',$조건목록);
	}
	
	
	if($bank_type != 'ALL' && $bank_type != 'EMPTY' && $bank_type != '') $AND_SQL .= "	AND	BD.bank_type = '$bank_type' ";
	if($bank_type == 'EMPTY') $AND_SQL .= "	AND	BD.admin_memo = '' ";
	
	if($sdate) $AND_SQL .= "	AND	BD.tr_date >= '$sdate' ";
	if($edate) $AND_SQL .= "	AND	BD.tr_date <= '$edate' ";
		
	//20170222.임시조건
	$AND_SQL .= " AND		BD.tr_date >= '2016-07-01'
								AND		BD.admin_link NOT LIKE '%201605%'
								AND		BD.admin_link NOT LIKE '%201606%'
								AND		BD.admin_link NOT LIKE '%201607%' 
	";

	/* 주문금액 큰 순서대로 회원목록 추출 */
	$SELECT_SQL = "	SELECT	BD.number,	
													BD.account_name,	/*계좌이름*/
													CONCAT(BD.tr_date,' ',BD.tr_time) AS tr_date,	/*거래일*/
													BD.tr_time,				/*거래시간*/
													BD.tr_type,				/*거래수단*/
													BD.output_price,	/*출금액*/
													BD.input_price,		/*입금액*/
													BD.trader_name,		/*거래자명*/
													BD.remain_money,	/*잔액*/
													BD.bank,					/*거래은행*/
													BD.bank_type,			/*입/출금 유형*/
													BD.tax_type,			/*세금관련 처리유형*/
													BD.tax_no,				/*세금관련 처리번호*/
													BD.tax_refno,			/*세금관련 후처리번호*/
													BD.admin_link,		/*연결된 주문번호들*/
													BD.admin_memo,		/*관리자 메모*/
													CC.bgcolor
									FROM		bank_db BD
													LEFT JOIN comcode CC ON (CC.ctype = 'bankdb' AND CC.col = 'bank_type' AND CC.code = BD.bank_type)
									WHERE		1=1				
									$AND_SQL
	";
	
}

else if($mode == '') {
	$SELECT_SQL = "";
}

$total_count = mysql_num_rows(sql_query($SELECT_SQL));

/* 코드값 검색 */
$main_sql = "	$SELECT_SQL
							$ORDER_BY
							LIMIT $start, $limit
";
$result = sql_query($main_sql);

while($row = mysql_fetch_assoc($result)) {
	foreach($row as $key => $val) {
		$row[$key] = 개행문자삭제($val);
		if($key == 'gp_realprice') $row[$key] = CEIL($val / 100) * 100;
	}
	array_push($data, $row);
}

if($total_count > 0) {
	$json[total] = "$total_count";
	$json[success] = "true";
	$json[data] = $data;
} else {
	$json[total] = 0;
	$json[success] = "false";
}

$json_data = json_encode_unicode($json);
echo $json_data;
?>