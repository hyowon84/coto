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

/* REMOTE FILTER */
$filter = json_decode(str_replace('\"','"',stripslashes( iconv('utf-8', 'cp949', $_GET[filter] ) )),true);
for($i = 0; $i < count($filter); $i++) {
	$FILTER_BY .= " AND	 T.".$filter[$i][property]." ".$filter[$i][operator]." '%".$filter[$i][value]."%' ";
}


$AND_SQL = "";


/* 회원목록 */
if($mode == 'mblist') {
	if($gpcode) $공구코드조건 .=" AND ( CL.gpcode IN (".str_replace("\'","'",$gpcode).")	AND CL.stats <= 35 ) ";	
	if ($sdate) $기간조건 .= " AND CL.od_date >= '$sdate 00:00:00' ";
	if ($edate) $기간조건 .= " AND CL.od_date <= '$edate 23:59:59' ";
//	if($keyword) $AND_SQL .= "AND (T.mb_name LIKE '%$keyword%' OR T.hphone LIKE '%$keyword%' OR T.mb_nick LIKE '%$keyword%' )";
	if($keyword) $내부조건 = " AND (CL.name LIKE '%$keyword%' OR CL.hphone LIKE '%$keyword%' OR CL.clay_id LIKE '%$keyword%' OR ( GI.gpcode_name LIKE '%$keyword%' AND CL.stats <= 39) ) ";
	
	/* 주문금액 큰 순서대로 회원목록 추출 */
	$SELECT_SQL = "	SELECT	T.*
													,IFNULL(Q1.SUM_QTY,0) AS QCK_SUM_QTY				/*퀵주문건수*/
													,IFNULL(Q1.SUM_TOTAL,0) AS QCK_SUM_TOTAL
													,IFNULL(S1.SUM_QTY,0) AS S40_SUM_QTY				/*발송예정건수*/
													,IFNULL(S1.SUM_TOTAL,0) AS S40_SUM_TOTAL
													,IFNULL(S2.SUM_QTY,0) AS NS40_SUM_QTY				/*발송불가건수*/
													,IFNULL(S2.SUM_TOTAL,0) AS NS40_SUM_TOTAL		
									FROM		(
														SELECT	CL.clay_id AS mb_nick,
																		CL.name AS mb_name,
																		CL.hphone,
																		SUM(CL.it_qty) AS SUM_QTY,
																		SUM(CL.it_qty * CL.it_org_price) AS SUM_TOTAL
														FROM		clay_order CL
																		LEFT JOIN gp_info GI ON (GI.gpcode = CL.gpcode)
														WHERE		CL.stats >= 15
														$공구코드조건
														$기간조건
														$내부조건
														GROUP BY CL.hphone, CL.clay_id
													) T	/* 전체주문건수, 총액 */
													LEFT JOIN (
																			SELECT	CL.clay_id AS mb_nick,
																							CL.name AS mb_name,
																							CL.hphone,
																							SUM(CL.it_qty) AS SUM_QTY,
																							SUM(CL.it_qty * CL.it_org_price) AS SUM_TOTAL
																			FROM		clay_order CL
																							LEFT JOIN gp_info GI ON (GI.gpcode = CL.gpcode)
																			WHERE		CL.stats IN (15,20,22,25)
																			AND			( CL.gpcode IN ('QUICK','AUCTION')
																			OR			GI.gpcode_name LIKE '%릴레이%' )
																			GROUP BY CL.hphone, CL.clay_id
													) Q1 ON (Q1.hphone = T.hphone AND Q1.mb_nick = T.mb_nick) /* 퀵주문 건수, 총액 */
													LEFT JOIN (
																			SELECT	CL.clay_id AS mb_nick,
																							CL.name AS mb_name,
																							CL.hphone,
																							SUM(CL.it_qty) AS SUM_QTY,
																							SUM(CL.it_qty * CL.it_org_price) AS SUM_TOTAL,
																							IV.CNT,
																							IV.CNT_40
																			FROM		clay_order CL
																							LEFT JOIN (	SELECT	T.gpcode,
																																	T.iv_it_id,
																																	T.CNT,	/*인보이스 전체 발주건수*/
																																	T40.CNT_40	/*인보이스 발주건의 도착건수*/
																													FROM		v_invoice_cnt T
																																	LEFT JOIN v_invoice_cnt40 T40 ON (T40.gpcode = T.gpcode AND T40.iv_it_id = T.iv_it_id)
																							)	IV ON (IV.gpcode = CL.gpcode AND IV.iv_it_id = CL.it_id)
																			WHERE		CL.stats IN (20,22,23,25) /* 결제완료, 통합배송요청, 포장완료, 배송대기중까지만 픽업대기, 직배대기는 배송대상에 포함안함 */
																			AND			IV.CNT <= IV.CNT_40 
																			GROUP BY CL.hphone, CL.clay_id
													) S1 ON (S1.hphone = T.hphone AND S1.mb_nick = T.mb_nick) /* 발송가능 건수, 총액 */
													LEFT JOIN (	SELECT	DISTINCT
																							CL.clay_id,
																							CL.hphone,
																							SUM(CL.it_qty) AS SUM_QTY,
																							SUM(CL.it_qty * CL.it_org_price) AS SUM_TOTAL
																			FROM		clay_order CL
																							LEFT JOIN (	SELECT	II.gpcode,
																																	II.iv_it_id,
																																	COUNT(*) AS CNT	/* 도착안된 총 발주건수*/
																													FROM		invoice_item II
																													WHERE		II.iv_stats IN ('00')
																													GROUP BY II.gpcode, II.iv_it_id
																							)	IV ON (IV.gpcode = CL.gpcode AND IV.iv_it_id = CL.it_id)
																			WHERE		CL.stats IN (20,22,25)
																			AND			IV.CNT > 0		#해외배송이 시작안된 주문신청건들중 결제완료, 배송대기중인것들
																			GROUP BY CL.hphone, CL.clay_id
													) S2 ON (S2.hphone = T.hphone AND S2.clay_id = T.mb_nick)
									WHERE		1=1
									$FILTER_BY
									$AND_SQL
	";

}

/* 주문상세내역 */
else if($mode == 'orderlist' || $mode == 'shipedlist') {
	
	if($hphone) $AND_SQL.=" AND CL.hphone = '$hphone' ";
	if($mb_nick) $AND_SQL.=" AND CL.clay_id = '$mb_nick' ";
	if($od_id) $AND_SQL.=" AND CL.od_id = '$od_id' ";

	if($mode == 'orderlist') {
		$상태조건 = "15,17,20,22,23,25,30,35";
	}
	if($mode == 'shipedlist') {
		$상태조건 = "40,50,60";
	}


	//문제가 생길경우 주석처리한 부분 해제, v_invoice_cnt 테이블조인 제거
	/* 선택된 회원의 주문목록 가져오기 */
	$SELECT_SQL = "	SELECT	CL.number AS taskId,
													SUBSTR(CL.od_id,3,12) AS projectId,
													CONCAT('[', GI.gpcode_name, '] ', CL.od_id, ' - 배송비(', IFNULL(DN.value,'미설정'), ') ', IFNULL(CI.delivery_price,'') , '원') AS project,
													CL.number,
													CL.gpcode,
													CL.od_id,
													CONCAT(CI.clay_id,'(',CI.name,')') AS buyer,
													CI.clay_id,
													CI.paytype,
													IF(LENGTH(GP.gp_img) > 8,GP.gp_img,'/shop/img/no_image.gif') AS gp_img,
													CL.it_id,
													CL.it_name,
													CL.it_memo,
													CL.it_qty,
													CL.it_org_price,
													CL.it_qty * CL.it_org_price AS total_price,
													CL.od_date,
													CI.delivery_type,
													CI.delivery_direct,
													CL.delivery_invoice,	/*품목별 송장번호 우선*/
													CI.delivery_invoice AS delivery_invoice2,	/*최근입력한 송장번호*/
													CI.cash_receipt_yn,
													CI.memo,
													CI.admin_memo,
													CL.stats,
													GI.gpcode_name,
													GI.stats AS gpstats,
													GN.value AS gpstats_name,
													SN.value AS stats_name,
													CASE
														WHEN	CL.gpcode = 'QUICK'
																	|| CL.gpcode = 'AUCTION'
																	|| GI.gpcode_name LIKE '%릴레이%'
																	|| IV.CNT <= IV.CNT_40
																	|| RJ.real_jaego >= GPQTY.GP_QTY
																	|| RJ.qk_jaego >= GPQTY.GP_QTY
																	#|| RJ.iv_qty >= GPQTY.GP_QTY
														THEN
															'40'
														ELSE
															'00'
													END	AS IV_STATS,
													CASE
														WHEN	CL.gpcode = 'QUICK' 
																	|| CL.gpcode = 'AUCTION'
																	|| GI.gpcode_name LIKE '%릴레이%'
																	|| IV.CNT <= IV.CNT_40
																	|| RJ.real_jaego >= GPQTY.GP_QTY
																	|| RJ.qk_jaego >= GPQTY.GP_QTY
																	#|| RJ.iv_qty >= GPQTY.GP_QTY
														THEN
															'배송가능'
														ELSE
															'배송불가'
													END	AS IV_STATS_NAME,
													IV.CNT,
													IV.CNT_40,
													IF( CL.gpcode = 'QUICK' && CL.gpcode = 'AUCTION', RJ.qk_jaego, RJ.real_jaego) AS real_jaego,
													GPQTY.GP_QTY
									FROM		clay_order CL
													LEFT JOIN clay_order_info CI ON (CI.od_id = CL.od_id)
													LEFT JOIN gp_info GI ON (GI.gpcode = CL.gpcode)
													LEFT JOIN g5_shop_group_purchase GP ON (GP.gp_id = CL.it_id)
													LEFT JOIN comcode SN ON (SN.ctype = 'clayorder' AND SN.col = 'stats' AND SN.code = CL.stats)
													LEFT JOIN comcode GN ON (GN.ctype = 'gpinfo' AND GN.col = 'stats' AND GN.code = GI.stats)
													LEFT JOIN comcode DN ON (DN.ctype = 'clayorder' AND DN.col = 'delivery_type' AND DN.code = CI.delivery_type)
													
													LEFT JOIN (	SELECT	T.gpcode,
																							T.iv_it_id,
																							T.CNT,
																							T40.CNT_40
																			FROM		v_invoice_cnt T
																							LEFT JOIN v_invoice_cnt40 T40 ON (T40.gpcode = T.gpcode AND T40.iv_it_id = T.iv_it_id)
																			WHERE		T.CNT <= T40.CNT_40		/*정상발주수량부터 과발주수량까지 배송가능으로 출력*/
													)	IV ON (IV.gpcode = CL.gpcode AND IV.iv_it_id = CL.it_id)
													LEFT JOIN (	SELECT	gpcode,
																							it_id,
																							SUM(it_qty) AS GP_QTY
																			FROM		clay_order
																			WHERE		stats <= 60
																			GROUP BY gpcode, it_id
													) GPQTY ON (GPQTY.gpcode = CL.gpcode AND GPQTY.it_id = CL.it_id)
													LEFT JOIN (	SELECT	CL.it_id,
																							CL.od_qty,
																							GP.jaego,
																							II.iv_qty,
																							( GP.jaego + IFNULL(II.iv_qty,0) - IFNULL(CL.od_qty,0)) AS qk_jaego,	/*빠른상품 재고산출*/
																							( IFNULL(II.iv_qty,0) - IFNULL(CL.od_qty,0)) AS real_jaego						/*공구 재고산출*/
																			FROM		(	SELECT	it_id,
																												SUM(it_qty) AS od_qty
																								FROM		clay_order
																								WHERE		stats >= 00
																								AND			stats <= 60	
																								GROUP BY it_id
																							) CL
																							LEFT JOIN g5_shop_group_purchase GP ON (GP.gp_id = CL.it_id)
																							LEFT JOIN (	SELECT	iv_it_id,
																																	SUM(iv_qty) AS iv_qty
																													FROM		invoice_item
																													WHERE		iv_stats = 40
																													GROUP BY iv_it_id
																							) II ON (II.iv_it_id = CL.it_id)
																			GROUP BY it_id
													) RJ ON (RJ.it_id = CL.it_id)
									WHERE		1=1
									AND			CL.stats IN ($상태조건)
									$AND_SQL
	";
//	echo $SELECT_SQL;
	
}

/* 주문정보 */
else if($mode == 'orderlistdetail') {
	if($hphone) $AND_SQL.=" AND CI.hphone = '$hphone' ";

	/* 선택된 회원의 배송지정보 가져오기 */
	$SELECT_SQL = "	SELECT	CI.gpcode,
													CI.od_id,
													CI.clay_id,
													CI.mb_id,
													CI.paytype,
													CI.receipt_link,
													CONCAT(CI.clay_id,'(',CI.name,')') AS buyer,
													CI.name,
													CI.receipt_name,
													CI.hphone,
													CI.zip,
													CI.addr1,
													CI.addr1_2,
													CI.addr2,
													CI.memo,
													CI.admin_memo,
													CI.cash_receipt_yn,
													CI.cash_receipt_type,
													CI.cash_receipt_info,
													CI.cash_receipt_print,
													CI.cash_receipt_cnt,
													CI.delivery_type,
													DT.value AS delivery_type_name,
													CI.delivery_price,
													CI.delivery_direct,
													CI.delivery_company,
													CI.delivery_invoice,
													CI.od_date,
													CI.hidden,
													CI.od_ip
									FROM		clay_order_info CI
													LEFT JOIN comcode DT ON (DT.ctype = 'clayorder' AND DT.col = 'delivery_type' AND DT.code = CI.delivery_type)
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

//echo $SELECT_SQL;

while($row = mysql_fetch_assoc($result)) {
	foreach($row as $key => $val) {
		$row[$key] = 개행문자삭제($val);
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