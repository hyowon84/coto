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

$AND_SQL = "";
$조건문 = array();


/* 공구목록 */
if($mode == 'orderlist') {
	//주문번호, 상품명, 상품코드, 주문자명, 휴대폰번호, 공구명, 메모, 관리자메모, 현금영수증번호

	
	/*
	 * ['전체',''],
		['닉네임','nick'],
		['이름','name'],
		['공구명','gpcode_name'],
		['공구코드','gpcode'],
		['연락처','hphone'],
		['주소','addr']
	 */
	
	switch ($searchtype) {
		case 'nick':
			if ($keyword) $AND_SQL .= "AND	CI.clay_id LIKE '%$keyword%'	";
			break;
		case 'name':
			if ($keyword) $AND_SQL .= "AND	CI.name LIKE '%$keyword%'	";
			break;
		case 'gpcode_name':
			if ($keyword) $AND_SQL .= "AND	GI.gpcode_name LIKE '%$keyword%'	";
			break;
		case 'gpcode':
			if ($keyword) $AND_SQL .= "AND	GI.gpcode LIKE '%$keyword%'	";
			break;
		case 'hphone':
			if ($keyword) $AND_SQL .= "AND	CI.hphone LIKE '%$keyword%'	";
			break;
		case 'od_id':
			if ($keyword) $AND_SQL .= "AND	CI.od_id LIKE '%$keyword%'	";
			break;
		case 'addr':
			if ($keyword) $AND_SQL .= "AND	CONCAT(CI.addr1,CI.addr1_2,CI.addr2) LIKE '%$keyword%'	";
			break;
		default:	
			if ($keyword) $AND_SQL .= "AND ( CL.od_id LIKE '%$keyword%' OR CL.it_id LIKE '%$keyword%' 
																		OR CL.it_name LIKE '%$keyword%' OR CI.clay_id LIKE '%$keyword%'
																		OR CI.hphone LIKE '%$keyword%' OR CI.name LIKE '%$keyword%'
																		OR GI.gpcode_name LIKE '%$keyword%' OR CI.memo LIKE '%$keyword%'
																		OR CI.receipt_name LIKE '%$keyword%' OR CI.admin_memo LIKE '%$keyword%'
																		OR CI.cash_receipt_info LIKE '%$keyword%'
																	)";
			break;
	}
	
	if ($stats) $AND_SQL .= " AND CL.stats IN ('$stats') ";
	if ($sdate) $AND_SQL .= " AND CL.od_date >= '$sdate 00:00:00' ";
	if ($edate) $AND_SQL .= " AND CL.od_date <= '$edate 23:59:59' ";
	
	
	
	$조건문SQL = " $AND_SQL ";
}
else if($mode == 'banklinklist') {
	
	if($number) {
		$BANK_SQL = "	SELECT	DISTINCT
													BD.admin_link
									FROM		bank_db BD
									WHERE		BD.number IN ($number)
		";
		$bank_result = sql_query($BANK_SQL);
		
		$od_id = array();
		while($bd = mysql_fetch_array($bank_result)) {
			$alink = explode(",",$bd[admin_link]);
			for($i = 0; $i < count($alink); $i++) {
				$od_id[] .= "'".$alink[$i]."'";
			}
		}
		$odid_list = implode(",",$od_id);

		$조건문[] = " CL.od_id IN ($odid_list) ";		
	}

	if ($keyword) {
		$조건문[] = " CL.od_id LIKE '%$keyword%' OR CL.it_id LIKE '%$keyword%' 
										OR CL.it_name LIKE '%$keyword%' OR CI.clay_id LIKE '%$keyword%'
										OR CI.hphone LIKE '%$keyword%' OR CI.name LIKE '%$keyword%'
										OR GI.gpcode_name LIKE '%$keyword%' OR CI.memo LIKE '%$keyword%'
										OR CI.receipt_name LIKE '%$keyword%' OR CI.admin_memo LIKE '%$keyword%'	";
	}
	if($number || $keyword) {
		$조건문SQL = " AND ( ".implode('OR', $조건문)." ) ";

		//환불제외 로딩, 전체로딩
		if($except_refund) {
			$조건문SQL .= " AND CL.stats <= 60 ";
		}

	}
	
}

//20170222.임시조건
//$조건문SQL.= " AND CL.od_date >= '2016-07-01 00:00:00' ";



/* 주문금액 큰 순서대로 회원목록 추출 */
$SELECT_SQL = "	SELECT	CL.*,
												CONCAT(CL.od_id,' ',CL.clay_id, ', 총  ' ,IFNULL(CLS.CNT_ORDER,0),'건(취소제외)') AS 'Group',
												GP.gp_img,
												CLC.CNT_TOTAL_ORDER,																	/* 주문번호와 연결되있는 개별레코드전체 갯수 */
												IFNULL(CLS.CNT_ORDER,0) AS CNT_ORDER,									/* 취소제외, 총 주문금액에 해당하는 개별레코드 갯수 */
												CLS.TOTAL_PRICE,
												DSN.value AS delivery_type_nm,
												CSN.value AS cash_receipt_type_nm,
												PTN.value AS paytype_nm,
												DSN.value AS delivery_type_nm,
												CSN.value AS cash_receipt_type_nm
								FROM		(	SELECT	CL.it_id,
																	CL.it_qty,
																	CL.it_org_price,
																	CL.number,
																	CL.it_name,
																	CASE
																		WHEN	CL.stats = 99	THEN
																			0
																		ELSE
																			(CL.it_org_price * CL.it_qty)
																	END	AS SELL_PRICE,
																	CL.stats,
																	CL.od_date,
																	CL.gpcode,
																	CL.od_id,												/* 주문ID*/
																	CL.clay_id,
																	CI.name,
																	CI.receipt_name,
																	CI.cash_receipt_yn,
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
																	CL.delivery_invoice,
																	CI.delivery_invoice AS delivery_invoice2,
																	CI.delivery_price,
																	CI.paytype,
																	CI.refund_money,
																	GI.gpcode_name
													FROM		clay_order CL
																	LEFT JOIN clay_order_info CI ON (CI.od_id = CL.od_id)
																	LEFT JOIN gp_info GI ON (GI.gpcode = CL.gpcode)
													WHERE		1=1
													$조건문SQL
													$ORDER_BY
													LIMIT $start, $limit
												) CL
												LEFT JOIN g5_shop_group_purchase GP ON (GP.gp_id = CL.it_id)
												LEFT JOIN g5_shop_category CA ON (CA.ca_id = GP.ca_id)

												LEFT JOIN comcode DSN ON (DSN.ctype = 'clayorder' AND DSN.col = 'delivery_type' AND DSN.code = CL.delivery_type)
												LEFT JOIN comcode CSN ON (CSN.ctype = 'clayorder' AND CSN.col = 'cash_receipt_type' AND CSN.code = CL.cash_receipt_type)
												LEFT JOIN comcode PTN ON (PTN.ctype = 'clayorder' AND PTN.col = 'paytype' AND PTN.code = CL.paytype)

												/* od_id별 실제 연결된 주문건수 */
												LEFT JOIN (	SELECT	T.od_id
																						,COUNT(T.od_id) AS CNT_TOTAL_ORDER
																		FROM	(	SELECT	CL.od_id
																						FROM		clay_order CL
																					) AS T
																		GROUP BY T.od_id
												) AS CLC ON (CLC.od_id = CL.od_id)

												/* 주문건별 총주문금액, 주문건수 */
												LEFT JOIN (		SELECT	CL.od_id,
																							COUNT(CL.od_id) AS CNT_ORDER,
																							SUM(CL.it_qty) AS SUM_QTY,
																							SUM(CL.it_org_price * CL.it_qty) + CI.delivery_price AS TOTAL_PRICE		/* 주문당시금액 */
																			FROM	clay_order CL
																						LEFT JOIN clay_order_info CI ON (CI.od_id = CL.od_id)
																			WHERE	1=1
																			AND		CL.stats NOT IN (99)
																			AND		CL.stats <= 60
																			GROUP BY CL.od_id
												) AS CLS ON (CLS.od_id = CL.od_id)
";
//echo $SELECT_SQL;


$TOTAL_SQL = "SELECT	CL.*
							FROM		clay_order CL
											LEFT JOIN clay_order_info CI ON (CI.od_id = CL.od_id)
											LEFT JOIN gp_info GI ON (GI.gpcode = CL.gpcode)
							WHERE		1=1
							$조건문SQL
							$ORDER_BY
";
$total_count = mysql_num_rows(sql_query($TOTAL_SQL));

/* 코드값 검색 */
$main_sql = "	$SELECT_SQL
							$ORDER_BY
";	#LIMIT $start, $limit
$result = sql_query($main_sql);



while($row = mysql_fetch_assoc($result)) {
	foreach($row as $key => $val) {
		$row[$key] = 개행문자삭제($val);
		if($key == 'admin_memo' || $key == 'memo') $row[$key] = preg_replace('/\r\n|\r|\n/','<br>',$val);
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