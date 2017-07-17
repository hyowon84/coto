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
	$FILTER_BY = " AND	 GI.".$filter[$i][property]." ".$filter[$i][operator]." '%".$filter[$i][value]."%' ";
}



$AND_SQL = "";


if($keyword) {
	$분할_검색어 = explode(" ",$keyword);
	$IVID조건 = $WRID조건 = $인보이스번호조건 = $발주서별칭조건 = $발주서메모조건 = $송금별칭조건 = $송금메모조건 = $통관별칭_조건 = " 1=1 ";
	$공구코드_조건 = $공구명_조건 = $상품명_조건 = $상품ID_조건 = " 1=1 ";
	for($s = 0; $s < count($분할_검색어); $s++) {
		/*WHERE밑에 바로 들어가는조건 OR로 분리*/
		$IVID조건 .= " AND	II.iv_id LIKE '%$분할_검색어[$s]%' ";
		$WRID조건 .= " AND	II.wr_id LIKE '%$분할_검색어[$s]%' ";
		$인보이스번호조건 .= " AND	II.iv_order_no LIKE '%$분할_검색어[$s]%' ";
		$발주서별칭조건 .= " AND II.iv_name LIKE '%$분할_검색어[$s]%' ";
		$발주서메모조건 .= " AND II.iv_memo LIKE '%$분할_검색어[$s]%' ";
		$송금별칭조건 .= " AND WR.wr_name LIKE '%$분할_검색어[$s]%' ";
		$송금메모조건 .= " AND WR.wr_memo LIKE '%$분할_검색어[$s]%' ";
		$통관별칭_조건 .= " AND	CR.cr_name LIKE '%$분할_검색어[$s]%' ";

		/*발주품목에 관련된 조건, 카운팅 하나라도 있을시 집계*/
		$상품명_조건 .= " AND	IV.iv_it_name LIKE '%$분할_검색어[$s]%'	";
		$상품ID_조건 .= " AND	IV.iv_it_id LIKE '%$분할_검색어[$s]%' ";
		$공구명_조건 .= " AND	GI.gpcode_name LIKE '%$분할_검색어[$s]%' ";
		$공구코드_조건 .= " AND	GI.gpcode LIKE '%$분할_검색어[$s]%' ";
		
	}
}



/* 좌측상단 송금예정 발주서 목록 */
if($mode == 'naviFindInvoice') {
	if($keyword) {
		$AND_SQL.= " AND ( ($IVID조건) OR ($인보이스번호조건) OR ($발주서별칭조건) OR ($발주서메모조건) OR (ITC.CNT > 0) )";	//OR ($상품명조건)
		$OR_SQL .= " AND	(($상품명_조건) OR	($상품ID_조건) OR	($공구명_조건) OR	($공구코드_조건)) ";
	}

	/* TOTAL COUNT */
	$SELECT_SQL = "	SELECT	II.iv_id,							/*인보이스ID*/
													II.iv_name,						/*발주서 별칭*/
													II.gpcode,						/*같이 묶어서 보길 원할경우 입력*/
													II.iv_dealer,					/*인보이스 딜러업체*/
													II.iv_order_no,				/*인보이스 주문번호*/
													II.iv_receipt_link,		/*해외송금 입출금내역 링크*/
													II.iv_date,						/*인보이스 날짜*/
													II.iv_name,
													II.od_exch_rate,			/*주문기준 환율*/
													II.money_type,				/*통화유형*/
													II.iv_discountfee,		/*할인금액*/
													II.iv_tax,						/*관세*/
													II.iv_shippingfee,		/*배송비*/
													IFNULL(IVS.SUM_PRICE,0) + IFNULL(II.iv_tax,0) + IFNULL(II.iv_shippingfee,0) - IFNULL(II.iv_discountfee,0) AS TOTAL_PRICE,
													II.iv_memo,						/*내용*/
													II.admin_id,
													MB.mb_name AS admin_name,		/* 담당자명 */
													II.reg_date,
													IFNULL(CNT_WR.CNT,0) AS CNT_WIRE,
													IFNULL(CNT_CLR.CNT,0) AS CNT_CLR,
													IFNULL(CNT_INP.CNT,0) AS CNT_INP
									FROM		invoice_info II
													LEFT JOIN (	SELECT	IV.gpcode,
																							IV.iv_id,
																							SUM(IV.iv_dealer_worldprice * IV.iv_qty) AS SUM_PRICE		/* 주문당시 발주총액(해외) */
																			FROM		invoice_item IV
																			WHERE		1=1
																			AND			IV.iv_stats NOT IN ('99')
																			GROUP BY 	IV.iv_id
													) IVS ON (IVS.iv_id = II.iv_id)
													LEFT JOIN (	SELECT	IV.iv_id,
																			COUNT(*) AS CNT
															FROM		invoice_item IV
																			LEFT JOIN gp_info GI ON (GI.gpcode = IV.gpcode)
															WHERE		1=1
															$OR_SQL
															GROUP BY IV.iv_id
													) ITC ON (ITC.iv_id = II.iv_id)
													
													LEFT JOIN g5_member MB ON (MB.mb_id = II.admin_id)
													LEFT JOIN gp_info GI ON (GI.gpcode = II.gpcode)
													
													
													LEFT JOIN (	SELECT	iv_id,
																							COUNT(*) AS CNT
																			FROM		wire_info
																			GROUP BY iv_id													
													) CNT_WR ON (CNT_WR.iv_id = II.iv_id)
													LEFT JOIN (	SELECT	iv_id,
																							COUNT(*) AS CNT
																			FROM		clearance_info
																			GROUP BY iv_id													
													) CNT_CLR ON (CNT_CLR.iv_id = II.iv_id)
													LEFT JOIN (	SELECT	iv_id,
																							COUNT(*) AS CNT
																			FROM		invoice_item
																			WHERE		iv_stats = 40
																			GROUP BY iv_id													
													) CNT_INP ON (CNT_INP.iv_id = II.iv_id)
									WHERE		1=1
									$AND_SQL
	";

//	echo $SELECT_SQL." $ORDER_BY";
}

/* 공동구매목록 */
else if($mode == 'gpinfo') {
	if($gpcode) $AND_SQL.=" AND GI.gpcode IN (".str_replace("\'","'",$gpcode).")";
	if($cr_id) $AND_SQL.=" AND CLR.cr_id IN (".str_replace("\'","'",$cr_id).")";
	if($keyword) {
		$AND_SQL.=" AND ( ( GI.gpcode_name LIKE '%$keyword%' OR GI.gpcode LIKE '%$keyword%' OR GI.links LIKE '%$keyword%'
											)
		 									OR	GI.gpcode IN	(	SELECT	IV.gpcode
																					FROM		invoice_item IV
																					WHERE		1=1
																					AND			(IV.iv_it_name LIKE '%$keyword%'
																					OR			IV.iv_it_id LIKE '%$keyword%' )
											)
								)
		";
	}

	$SELECT_SQL = "	SELECT	DISTINCT
													GI.gpcode,					/*공구코드*/
													GI.gpcode_name,			/*공구이름 간략하게*/
													GI.menu_name,				/*홈페이지 카테고리메뉴명*/
													GI.menu_view,				/*홈페이지 메뉴 공개여부*/
													GI.gp_type,					/*정기 or 긴급*/
													GI.links,						/*공구진행할 상품코드들*/
													GI.locks,						/*가격락킹할 상품코드들*/
													GI.solds,						/*딜러업체 품절상품코드들 */
													GI.choice_dealer,		/*전체 또는 딜러(브랜드) 선택*/
													GI.start_date,			/*시작일*/
													GI.end_date,				/*종료일*/
													GI.volprice_yn,			/*볼륨프라이스 적용여부 Y/N*/
													GI.sell_title,			/*S01 : 판매가 , S02 : 분양가 */
													GI.list_view,				/*공구진행내역 보기/안보기*/
													GI.baesongbi,				/*배송비*/
													GI.memo,						/*메모*/
													GI.invoice_memo,
													GI.stats,						/*공구진행상태*/
													GI.reg_date,				/*입력일자*/
													CLS.SUM_PAY,
													IFNULL(CLS.SUM_QTY,0) AS SUM_QTY,				/*주문량 합*/
													IFNULL(IVS.SUM_IV_QTY,0) AS SUM_IV_QTY,	/*발주수량 합*/
													/* 주문수량 - 인보이스수량 */
													IF( (IFNULL(CLS.SUM_QTY,0) - IFNULL(IVS.SUM_IV_QTY,0)) < 0, 0, (IFNULL(CLS.SUM_QTY,0) - IFNULL(IVS.SUM_IV_QTY,0)) ) AS NEED_IV_QTY,
													ITC.CNT AS ITC_CNT,
													IVC.CNT AS IVC_CNT
									FROM		gp_info GI
													LEFT JOIN (	SELECT	CL.gpcode,
																							GI.reg_date,
																							SUM(CL.it_qty) AS SUM_QTY,		/* 주문 합계수량 */
																							SUM(CL.it_qty * CL.it_org_price) AS SUM_PAY		/* 총 주문금액 */
																			FROM		clay_order CL
																							LEFT JOIN gp_info GI ON (GI.gpcode = CL.gpcode)
																			WHERE		1=1
																			AND			CL.stats <= 60
																			GROUP BY 	CL.gpcode
													) CLS ON (CLS.gpcode = GI.gpcode)
													/*인보이스 합계*/
													LEFT JOIN (	SELECT	IV.gpcode,
																							SUM(IV.iv_qty) AS SUM_IV_QTY		/* 주문당시 합계수량 */
																			FROM		invoice_item IV
																							LEFT JOIN gp_info GI ON (GI.gpcode = IV.gpcode)
																			WHERE		1=1
																			AND			IV.iv_stats NOT IN ('99')
																			GROUP BY 	IV.gpcode
													) IVS ON (IVS.gpcode = GI.gpcode)

													/* 공구별 품목 갯수 */
													LEFT JOIN (	SELECT	V.gpcode,
																							COUNT(V.links_itid) AS CNT
																			FROM		v_gpinfo_links V
																			WHERE		1=1
																			GROUP BY V.gpcode
													) ITC ON (ITC.gpcode = GI.gpcode)

													/*인보이스 합계*/
													LEFT JOIN (	SELECT	T.gpcode,
																							COUNT(T.iv_it_id) AS CNT
																			FROM		(	SELECT	DISTINCT
																												IV.gpcode,
																												IV.iv_it_id
																								FROM		invoice_item IV
																								WHERE		1=1
																								AND			IV.iv_stats NOT IN ('99')
																							) T
																			GROUP BY 	T.gpcode
													) IVC ON (IVC.gpcode = GI.gpcode)

													/*통관진행된 발주서에 관련된 공동구매 검색을 위한 JOIN*/
													LEFT JOIN (	SELECT	DISTINCT
																							CI.iv_id,
																							CI.cr_id,
																							II.gpcode
																			FROM		clearance_item CI
																							LEFT JOIN invoice_info II ON (II.iv_id = CI.iv_id)
													) CLR ON ( CLR.gpcode LIKE CONCAT('%',GI.gpcode,'%') )
									WHERE		1=1
									#AND			GI.stats NOT IN (99)	/* 마감종료는 제외 */
									#AND			GI.gpcode NOT IN ('QUICK')
									$FILTER_BY
									$AND_SQL
	";

//	echo $SELECT_SQL;
}


/* 발주예상목록 */
else if($mode == 'orderitems') {
	if($gpcode) {
		$AND_SQL.=" AND vGL.gpcode IN (".str_replace("\'","'",$gpcode).")";
		$NOT_IN_GPCODE = "	AND			gpcode NOT IN (".str_replace("\'","'",$gpcode).")	";
	}

	if($keyword) {
//		$AND_SQL.=" AND (	GI.gpcode_name LIKE '%$keyword%' OR  GP.gp_name LIKE '%$keyword%' OR GP.gp_id LIKE '%$keyword%' )";

		$공구명조건 = $상품명조건 = $상품ID조건 = $상품메모조건 = " 1=1 ";
		$분할_검색어 = explode(" ",$keyword);

		for($s = 0; $s < count($분할_검색어); $s++) {
			$공구명조건 .= " AND		GI.gpcode_name LIKE '%$분할_검색어[$s]%' ";
			$상품명조건 .= " AND		GP.gp_name LIKE '%$분할_검색어[$s]%' ";
			$상품ID조건 .= " AND		GP.gp_id LIKE '%$분할_검색어[$s]%' ";
			$상품메모조건 .= " AND		GP.admin_memo LIKE '%$분할_검색어[$s]%' ";
		}

		$AND_SQL.= " AND ( ($공구명조건) OR ($상품명조건) OR ($상품ID조건) OR ($상품메모조건) )";

	}

	/* TOTAL COUNT */
	$SELECT_SQL = "	SELECT	T.*,
													T.real_jaego,
													IF( T.NEED_IV_QTY > 0, T.NEED_IV_QTY, 0) AS NEED_IV_QTY	/* (집계량 - 발주량) + (전체집계량 - 전체발주량) */
									FROM		(	SELECT	GI.reg_date,
																		GI.gpcode_name,
																		vGL.gpcode,
																		vGL.links_itid AS it_id,
																		GP.gp_img AS it_img,
																		GP.gp_name AS it_name,
																		IFNULL(GP.jaego,0) AS jaego,
																		IFNULL(GP.gp_jaego,0) AS gp_jaego,
																		GP.admin_memo,
																		
																		CLS.it_org_price,
																		IFNULL(CLS.it_org_price,0) * IFNULL(CLS.SUM_QTY,0) AS total_price,	/*주문총액*/

																		IFNULL(IVS.SUM_IV_WORLDPRICE,0) AS SUM_IV_WORLDPRICE,	/*발주총액*/													
																		IFNULL(IF(GI.volprice_yn = 'Y',(CEIL((( GPO.po_cash_price * FD.USD * (1+ (GP.gp_charge + GP.gp_duty)/100) ) * 1.1) /100)*100),GP.gp_price),0) AS volprice,
																		/*발주 관련 합계 컬럼, 공구단위로 묶어야*/
																		
																		IFNULL(CLS.SUM_QTY,0) AS SUM_QTY,								/*해당공구의 주문수량*/
																		
																		/*발주필요수량*/
																		( ( IFNULL(CLS.SUM_QTY,0) - IFNULL(IVS.SUM_IV_QTY,0) - IFNULL(GP.jaego,0) ) - IF( ((IFNULL(RIV.RIV_QTY,0)-IFNULL(IVS.SUM_IV_QTY,0)) - (IFNULL(CO.ORDER_QTY,0) - IFNULL(CLS.SUM_QTY,0)) ) > 0, ((IFNULL(RIV.RIV_QTY,0)-IFNULL(IVS.SUM_IV_QTY,0)) - (IFNULL(CO.ORDER_QTY,0) - IFNULL(CLS.SUM_QTY,0))), 0) )  AS NEED_IV_QTY,
																		
																		IFNULL(IVS.SUM_IV_QTY,0) AS SUM_IV_QTY,					/*해당공구의 발주수량*/
																		
																		IF( (IFNULL(IVS.SUM_IV_QTY,0) - IFNULL(CLS.SUM_QTY,0)) > 0, (IFNULL(IVS.SUM_IV_QTY,0) - IFNULL(CLS.SUM_QTY,0)), 0 ) AS OVER_IV_QTY,	/*과발주 수량*/
																		
																		IFNULL(CO.ORDER_QTY,0) AS ORDER_QTY,						/*모든공구 총주문수량*/
																		IFNULL(RIV.RIV_QTY,0) AS RIV_QTY,								/*모든공구 총발주수량*/
																		
																		/*상품의 총 발주수량 - 상품의 총 주문수량 = 재고 */
																		IF( IFNULL(GP.jaego,0) + IFNULL(RIV.RIV_QTY,0) - IFNULL(CO.ORDER_QTY,0) > 0, IFNULL(GP.jaego,0) + IFNULL(RIV.RIV_QTY,0) - IFNULL(CO.ORDER_QTY,0), 0) AS real_jaego
																		
														FROM		v_gpinfo_links vGL
																		
																		/* 해당공구 발주수량 */
																		LEFT JOIN (	SELECT	IV.gpcode,
																												IV.iv_it_id,										/* 주문상품코드 */
																												SUM(IV.iv_dealer_worldprice * IV.iv_qty) AS SUM_IV_WORLDPRICE,				/* 주문당시 외화금액 */
																												SUM(IV.iv_qty) AS SUM_IV_QTY		/* 주문당시 합계수량 */
																								FROM		invoice_item IV																							
																								WHERE		1=1
																								AND			IV.iv_stats NOT IN ('99')	/*가상발주 제외 */
																								GROUP BY 	IV.gpcode, IV.iv_it_id
																		) IVS ON (IVS.gpcode = vGL.gpcode AND IVS.iv_it_id = vGL.links_itid)
																		
																		/* 해당공구 집계수량 */
																		LEFT JOIN ( SELECT	gpcode,
																												it_id,
																												it_org_price,
																												SUM(it_qty) AS SUM_QTY
																								FROM		clay_order
																								WHERE		stats <= 60		/* 취소,환불건 제외, 모든 신청수량 */
																								GROUP BY gpcode, it_id
																		) CLS ON (CLS.gpcode = vGL.gpcode AND CLS.it_id = vGL.links_itid)
																		
																		/* 전체집계수량 */
																		LEFT JOIN ( SELECT	it_id,
																												SUM(it_qty) AS ORDER_QTY
																								FROM		clay_order
																								WHERE		stats <= 60		/* 취소,환불건 제외, 모든 신청수량 */
																								GROUP BY it_id
																		) CO ON (CO.it_id = vGL.links_itid )
																		
																		/* 전체발주수량*/
																		LEFT JOIN (	SELECT	iv_it_id,
																												SUM(iv_qty) AS RIV_QTY
																								FROM		invoice_item
																								WHERE		1=1
																								AND			iv_stats >= 00
																								GROUP BY iv_it_id				
																		) RIV ON (RIV.iv_it_id = vGL.links_itid)
																		
																		
																		LEFT JOIN gp_info GI ON (GI.gpcode = vGL.gpcode)
																		LEFT JOIN g5_shop_group_purchase GP ON (GP.gp_id = vGL.links_itid)
																		LEFT JOIN g5_shop_group_purchase_option GPO ON (GPO.gp_id = vGL.links_itid AND GPO.po_sqty <= CLS.SUM_QTY AND GPO.po_eqty >= CLS.SUM_QTY)
																		,(SELECT	*
																			FROM		flow_price
																			ORDER BY reg_date DESC
																			LIMIT 1
																		) FD
														WHERE		1=1
														$AND_SQL
														$OR_SQL
									) T
	";
//	echo $SELECT_SQL;
}

/* 좌측상단 송금예정 발주서 목록 */
else if($mode == 'invoiceTodoWire') {
	if($keyword) {
		$AND_SQL.= " AND ( ($IVID조건) OR ($인보이스번호조건) OR ($발주서별칭조건) OR ($발주서메모조건) OR (ITC.CNT > 0) )";	//OR ($상품명조건)
		$OR_SQL .= " AND	(($상품명_조건) OR	($상품ID_조건) OR	($공구명_조건) OR	($공구코드_조건)) ";
	}
	
	/* TOTAL COUNT */
	$SELECT_SQL = "	SELECT	II.iv_id,							/*인보이스ID*/
													II.iv_name,						/*발주서 별칭*/
													II.gpcode,						/*같이 묶어서 보길 원할경우 입력*/
													II.iv_dealer,					/*인보이스 딜러업체*/
													II.iv_order_no,				/*인보이스 주문번호*/
													II.iv_receipt_link,		/*해외송금 입출금내역 링크*/
													II.iv_date,						/*인보이스 날짜*/
													II.iv_name,
													II.money_type,				/*통화유형*/
													II.iv_discountfee,		/*할인금액*/
													II.iv_tax,						/*관세*/
													II.iv_shippingfee,		/*배송비*/
													IFNULL(IVS.SUM_PRICE,0) + IFNULL(II.iv_tax,0) + IFNULL(II.iv_shippingfee,0) - IFNULL(II.iv_discountfee,0) AS TOTAL_PRICE,
													II.iv_memo,						/*내용*/
													II.admin_id,
													MB.mb_name AS admin_name,		/* 담당자명 */
													II.reg_date
									FROM		invoice_info II
													LEFT JOIN (	SELECT	IV.gpcode,
																							IV.iv_id,
																							SUM(IV.iv_dealer_worldprice * IV.iv_qty) AS SUM_PRICE		/* 주문당시 발주총액(해외) */
																			FROM		invoice_item IV
																			WHERE		1=1
																			AND			IV.iv_stats NOT IN ('99')
																			GROUP BY 	IV.iv_id
													) IVS ON (IVS.iv_id = II.iv_id)
													LEFT JOIN (	SELECT	IV.iv_id,
																			COUNT(*) AS CNT
															FROM		invoice_item IV
																			LEFT JOIN gp_info GI ON (GI.gpcode = IV.gpcode)
															WHERE		1=1
															$OR_SQL
															GROUP BY IV.iv_id
													) ITC ON (ITC.iv_id = II.iv_id)
													
													LEFT JOIN g5_member MB ON (MB.mb_id = II.admin_id)
													LEFT JOIN gp_info GI ON (GI.gpcode = II.gpcode)
									WHERE		1=1
									AND			(II.wr_id IS NULL OR II.wr_id = '')
									$AND_SQL
	";
	
//	echo $SELECT_SQL." $ORDER_BY";
}

/* 계속 누적되는 송금완료 발주서 */
else if($mode == 'invoiceEndWire') {
	if($wr_id) $AND_SQL.=" AND WR.wr_id = '$wr_id' ";

	if($keyword) {
		$AND_SQL.= " AND ( ($IVID조건) OR ($WRID조건) OR ($인보이스번호조건) OR ($송금별칭조건) OR ($발주서별칭조건) OR ($발주서메모조건) OR (ITC.CNT > 0) )";	//OR ($상품명조건)
		$OR_SQL .= " AND	(($상품명_조건) OR	($상품ID_조건) OR	($공구명_조건) OR	($공구코드_조건)) ";
	}

	/* TOTAL COUNT */
	$SELECT_SQL = "	SELECT	II.cr_id,										/*통관ID*/
													II.wr_id,										/*송금ID*/
													II.iv_id,										/*인보이스ID*/
													IFNULL(IVS.SUM_PRICE,0) + IFNULL(II.iv_tax,0) + IFNULL(II.iv_shippingfee,0) - IFNULL(II.iv_discountfee,0) AS TOTAL_PRICE,
													CONCAT('[',WR.wr_id,'] [',IFNULL(CCWR.value,'NULL'),'] ',WR.wr_name, ' - 송금액 [',IFNULL(II.money_type,'-'),']',IFNULL(FORMAT(IFNULL(WR.wr_totalprice,0),2),0),' (￦',IFNULL(FORMAT(IFNULL(WR.wr_exchrate,0) * IFNULL(WR.wr_totalprice,0),0),0),'원)') AS 'Group',
													II.od_exch_rate,						/*주문기준 환율*/
													II.money_type,							/*통화유형*/
													II.iv_discountfee,					/*할인금액*/
													II.iv_tax,									/*관세*/
													II.iv_shippingfee,					/*배송비*/
													WR.wr_name,									/*송금내역 별칭*/
													WR.wr_out_fee,							/*송금수수료(해외)*/
													WR.wr_in_fee,								/*송금수수료(국내)*/
													WR.wr_memo,									/*메모*/
													II.iv_name,									/*발주서 별칭*/
													II.gpcode,									/*같이 묶어서 보길 원할경우 입력*/
													II.iv_dealer,								/*인보이스 딜러업체*/
													II.iv_order_no,							/*인보이스 주문번호*/
													II.iv_receipt_link,					/*해외송금 입출금내역 링크*/
													II.iv_date,									/*인보이스 날짜*/
													II.iv_name,
													II.iv_memo,									/*내용*/
													II.admin_id,
													MB.mb_name AS admin_name,		/* 담당자명 */
													II.reg_date,
													WR.wr_exchrate
									FROM		invoice_info II
													LEFT JOIN wire_info WR ON (WR.wr_id = II.wr_id)
													LEFT JOIN comcode CCWR ON (CCWR.ctype = 'wire' AND CCWR.col = 'wr_type' AND CCWR.code = WR.wr_type)
													LEFT JOIN (	SELECT	IV.gpcode,
																							IV.iv_id,
																							SUM(IV.iv_dealer_worldprice * IV.iv_qty) AS SUM_PRICE		/* 주문당시 발주총액(￦) */
																			FROM		invoice_item IV
																			WHERE		1=1
																			AND			IV.iv_stats NOT IN ('99')
																			GROUP BY 	IV.iv_id
													) IVS ON (IVS.iv_id = II.iv_id)

													LEFT JOIN (	SELECT	II.wr_id,
																							SUM(IV.iv_dealer_worldprice * IV.iv_qty) AS SUM_PRICE		/* 송금총액(￦) */
																			FROM		invoice_item IV
																							LEFT JOIN invoice_info II ON (II.iv_id = IV.iv_id)
																			WHERE		1=1
																			AND			IV.iv_stats NOT IN ('99')
																			GROUP BY 	II.wr_id
													) WRS ON (WRS.wr_id = II.wr_id)

													LEFT JOIN (	SELECT	IV.iv_id,
																			COUNT(*) AS CNT
															FROM		invoice_item IV
																			LEFT JOIN gp_info GI ON (GI.gpcode = IV.gpcode)
															WHERE		1=1
															$OR_SQL
															GROUP BY iv_id
													) ITC ON (ITC.iv_id = II.iv_id)
													
													
													LEFT JOIN g5_member MB ON (MB.mb_id = WR.admin_id)
													LEFT JOIN gp_info GI ON (GI.gpcode = II.gpcode)
									WHERE		1=1
									
									/*송금완료조건*/
									AND			WR.wr_id != ''
									AND			WR.wr_id IS NOT NULL
									$AND_SQL
	";
//	echo $SELECT_SQL." $ORDER_BY";
}

/* 통관예정 송금내역 목록 */
else if($mode == 'invoiceTodoClearance') {
	if($wr_id) $AND_SQL.=" AND WR.wr_id = '$wr_id' ";

	if($keyword) {
		$AND_SQL.= " AND ( ($IVID조건) OR ($WRID조건) OR ($인보이스번호조건) OR ($송금별칭조건) OR ($발주서별칭조건) OR ($발주서메모조건) OR (ITC.CNT > 0) )";	//OR ($상품명조건)
		$OR_SQL .= " AND	(($상품명_조건) OR	($상품ID_조건) OR	($공구명_조건) OR	($공구코드_조건)) ";
	}	
	
	/* TOTAL COUNT */
	$SELECT_SQL = "	SELECT	CAL.IV_IT_CNT,							/*품목유형의 수*/
													CAL.CP_CNT,									/*통관완료된 품목유형의 수*/
													CONCAT(IFNULL( ROUND(CAL.CP_CNT / CAL.IV_IT_CNT * 100,0) ,0),'%') AS complete_per,	/*통관진행률%*/
													II.cr_id,										/*통관ID*/
													II.wr_id,										/*송금ID*/
													II.iv_id,										/*인보이스ID*/
													IFNULL(IVS.SUM_PRICE,0) + IFNULL(II.iv_tax,0) + IFNULL(II.iv_shippingfee,0) - IFNULL(II.iv_discountfee,0) AS TOTAL_PRICE,
													CONCAT('[',WR.wr_id,'] [',IFNULL(CCWR.value,'NULL'),'] ',WR.wr_name,' - 송금액 [',IFNULL(II.money_type,'-'),']',IFNULL(FORMAT(IFNULL(WR.wr_totalprice,0),2),0),' (￦',IFNULL(FORMAT(IFNULL(WR.wr_exchrate,0) * IFNULL(WR.wr_totalprice,0),0),0),'원)') AS 'Group',
													II.od_exch_rate,						/*주문기준 환율*/
													II.money_type,							/*통화유형*/
													II.iv_discountfee,					/*할인금액*/
													II.iv_tax,									/*관세*/
													II.iv_shippingfee,					/*배송비*/
													WR.wr_name,									/*송금내역 별칭*/
													WR.wr_out_fee,							/*송금수수료(해외)*/
													WR.wr_in_fee,								/*송금수수료(국내)*/
													WR.wr_memo,									/*메모*/
													II.iv_name,									/*발주서 별칭*/
													II.gpcode,									/*같이 묶어서 보길 원할경우 입력*/
													II.iv_dealer,								/*인보이스 딜러업체*/
													II.iv_order_no,							/*인보이스 주문번호*/
													II.iv_receipt_link,					/*해외송금 입출금내역 링크*/
													II.iv_date,									/*인보이스 날짜*/
													II.iv_name,
													II.iv_memo,									/*내용*/
													II.admin_id,
													MB.mb_name AS admin_name,		/* 담당자명 */
													II.reg_date
									FROM		invoice_info II
													LEFT JOIN wire_info WR ON (WR.wr_id = II.wr_id)
													LEFT JOIN comcode CCWR ON (CCWR.ctype = 'wire' AND CCWR.col = 'wr_type' AND CCWR.code = WR.wr_type)									
									
													/*발주품목 = 통관품목 전부 완료되었는지 통계*/
													LEFT JOIN (	SELECT	T.iv_id,
																							IV.IV_IT_CNT,								/*발주된 품목 유형의 수*/
																							SUM(T.CP_VALUE) AS CP_CNT	/*통관완료된 품목 유형의 수*/				
																			FROM		(	SELECT	IV.number,
																												IV.gpcode,
																												IV.iv_id,
																												IV.iv_it_id,
																												IV.iv_qty AS IV_QTY,
																												CR.CR_QTY,
																												IF(IV.iv_qty <= CR.cr_qty,1,0) AS CP_VALUE
																								FROM		invoice_item IV
																												LEFT JOIN (	SELECT	CR.iv_pkno,
																																						CR.cr_it_id,
																																						SUM(CR.cr_qty) AS CR_QTY
																																		FROM		clearance_item CR
																																		WHERE		1=1
																																		GROUP BY CR.iv_pkno, CR.cr_it_id
																												) CR ON (CR.iv_pkno = IV.number AND CR.cr_it_id = IV.iv_it_id)
																								WHERE		1=1
																								GROUP BY IV.number, IV.iv_it_id
																							) T
																							LEFT JOIN (	SELECT	iv_id,
																																	COUNT(*) AS IV_IT_CNT	/*발주서내 발주한 품목유형의 수*/
																													FROM		invoice_item
																													GROUP BY iv_id
																							) IV ON (IV.iv_id = T.iv_id)
																			WHERE		1=1
																			GROUP BY T.iv_id
													) CAL ON (CAL.iv_id = II.iv_id)
													
													LEFT JOIN (	SELECT	IV.gpcode,
																							IV.iv_id,
																							SUM(IV.iv_dealer_worldprice * IV.iv_qty) AS SUM_PRICE		/* 주문당시 발주총액(￦) */
																			FROM		invoice_item IV
																			WHERE		1=1
																			AND			IV.iv_stats NOT IN ('99')
																			GROUP BY 	IV.iv_id
													) IVS ON (IVS.iv_id = II.iv_id)
													
													LEFT JOIN (	SELECT	IV.iv_id,
																			COUNT(*) AS CNT
															FROM		invoice_item IV
																			LEFT JOIN gp_info GI ON (GI.gpcode = IV.gpcode)
															WHERE		1=1
															$OR_SQL
															GROUP BY iv_id
													) ITC ON (ITC.iv_id = II.iv_id)
													
													LEFT JOIN g5_member MB ON (MB.mb_id = WR.admin_id)
													LEFT JOIN gp_info GI ON (GI.gpcode = II.gpcode)
									WHERE		1=1
									/*통관을 더 진행시켜야할지에 대한 조건*/
									AND			CAL.CP_CNT != CAL.IV_IT_CNT
									
									/*송금완료조건*/
									AND			WR.wr_id != ''
									AND			WR.wr_id IS NOT NULL
									$AND_SQL
	";
//	echo $SELECT_SQL." $ORDER_BY";
}


/* 통관완료내역(입고예정내역) */
else if($mode == 'invoiceEndClearance') {

	if($keyword) {
		$AND_SQL.= " AND ( ITC.CNT > 0 OR ($통관별칭_조건) ) ";	//OR ($상품명조건)
		$OR_SQL .= " AND	( ($IVID조건) OR ($WRID조건) OR ($인보이스번호조건) OR ($발주서별칭조건) OR ($발주서메모조건)
		 										OR ($상품명_조건) OR	($상품ID_조건) OR	($공구명_조건) OR	($공구코드_조건) OR	($공구코드_조건) )	";
	}
	
	/* 통관완료 발주서 내역 */
	$SELECT_SQL = "	SELECT	DISTINCT
													CONCAT('[',CR.cr_id,'] \"',CR.cr_name,'\", 관/부가세:', IFNULL(CR.cr_taxfee,0),'원, 배송비:',IFNULL(CR.cr_shipfee,0),'원') AS 'Group',
													CR.cr_id,				/*통관ID*/
													CR.cr_name,			/*통관내역 별칭*/
													CR.cr_refno,		/*통관번호*/
													CR.iv_id,				/*연결된 발주ID*/
													MB.mb_name AS admin_id,
													CR.cr_dutyfee,	/*관부가세(원화)*/
													CR.cr_taxfee,		/*관부가세(원화)*/
													CR.cr_shipfee,	/*배송비(원화)*/
													CR.cr_file,			/*통관내역서 파일첨부*/
													CR.cr_memo,			/*메모*/
													CR.cr_date,			/*통관일*/
													CR.reg_date,		/*작성일*/
													CR_EA.CNT AS CR_EA,
													IP_EA.CNT AS IP_EA,
													IF(CR_EA.CNT <= IP_EA.CNT,'Y','N') AS IP_COMPLETE
									FROM		clearance_info CR
													LEFT JOIN g5_member MB ON (MB.mb_id = CR.admin_id)
													LEFT JOIN (	SELECT	IV.iv_id,
																			COUNT(*) AS CNT
															FROM		invoice_item IV
																			LEFT JOIN invoice_info II ON (II.iv_id = IV.iv_id)
																			LEFT JOIN gp_info GI ON (GI.gpcode = IV.gpcode)
															WHERE		1=1
															$OR_SQL
															GROUP BY iv_id
													) ITC ON (CR.iv_id LIKE CONCAT('%',ITC.iv_id,'%'))
													LEFT JOIN (	SELECT	cr_id,
																							COUNT(*) AS CNT
																			FROM		clearance_item
																			GROUP BY cr_id
													) CR_EA ON (CR_EA.cr_id = CR.cr_id)
													LEFT JOIN (	SELECT	CI.cr_id,
																							COUNT(*) AS CNT
																			FROM		clearance_item CI
																							LEFT JOIN invoice_item IV ON (IV.iv_id = CI.iv_id AND IV.iv_it_id = CI.cr_it_id)
																			WHERE		IV.iv_stats = 40
																			GROUP BY cr_id
													) IP_EA ON (IP_EA.cr_id = CR.cr_id)
									WHERE		1=1
									$AND_SQL
	";
//	echo $SELECT_SQL;
}

/* 송금탭의 송금예정과 관련된 발주품목 */
else if($mode == 'invoice_item') {

	if($keyword) {
//		$AND_SQL.= " AND ( ($IVID조건) OR ($인보이스번호조건) OR ($발주서별칭조건) OR ($발주서메모조건)  )";	//OR ($상품명조건)
		$AND_SQL .= " AND	(($상품명_조건) OR	($상품ID_조건) OR	($공구명_조건) OR	($공구코드_조건)) ";
	}
	
	
	if($iv_id) {
		$AND_SQL.=" AND IV.iv_id IN (".str_replace("\'","'",$iv_id).") ";
	}

	/* TOTAL COUNT */
	$SELECT_SQL = "	SELECT	IV.number,
													IV.gpcode,									/*같이 묶어서 보길 원할경우 입력*/
													GI.gpcode_name,
													II.ip_id,
													II.wr_id,
													IV.iv_id,
													CLR.cr_id,
													IT.gp_img AS iv_it_img,
													IV.iv_it_id,								/*주문상품코드*/
													IV.iv_it_name,							/*주문상품명*/
													II.money_type,
													IV.iv_dealer_worldprice,		/*주문단가($)*/
													IV.iv_dealer_price,					/*주문단가*/
													IFNULL(GPO.GP_ORDER_QTY,0) AS GP_ORDER_QTY,						/*공동구매의 품목 주문수량 실시간 확인*/
													IFNULL(GPT.GPT_QTY,0) AS GPT_QTY,								/*공동구매의 배송완료 이하 총 주문수량*/
													
													IV.iv_qty,									/*발주수량*/
													IP.iv_qty AS ip_qty,				/*입고수량*/
													IV.iv_dealer,								/*딜러*/
													(IV.iv_dealer_worldprice * IV.iv_qty) AS total_price,
													IV.iv_stats,
													IV.reg_date,
													CR.cr_it_id,
													CR.cr_qty,
													CR.cr_cancel_qty
									FROM		invoice_item IV
													
													LEFT JOIN invoice_info II ON (II.iv_id = IV.iv_id)
													LEFT JOIN g5_shop_group_purchase IT ON (IT.gp_id = IV.iv_it_id)
													LEFT JOIN gp_info GI ON (GI.gpcode = IV.gpcode)
													
													/* 해당공구 총주문수량(배송완료 이하) */
													LEFT JOIN ( SELECT	gpcode,
																							it_id,
																							it_org_price,
																							SUM(it_qty) AS GPT_QTY
																			FROM		clay_order
																			WHERE		stats <= 60		/* 취소건 제외, 모든 신청수량 */
																			GROUP BY gpcode, it_id
													) GPT ON (GPT.gpcode = IV.gpcode AND GPT.it_id = IV.iv_it_id)
													
													/* 해당공구 총주문수량(포장 이하) */
													LEFT JOIN ( SELECT	gpcode,
																							it_id,
																							it_org_price,
																							SUM(it_qty) AS GP_ORDER_QTY
																			FROM		clay_order
																			WHERE		stats <= 23		/* 취소건 제외, 모든 신청수량 */
																			GROUP BY gpcode, it_id
													) GPO ON (GPO.gpcode = IV.gpcode AND GPO.it_id = IV.iv_it_id)
													LEFT JOIN (	SELECT	iv_id,
																							cr_it_id,
																							SUM(cr_qty) AS cr_qty,
																							SUM(cr_cancel_qty) AS cr_cancel_qty
																			FROM		clearance_item
																			GROUP BY iv_id, cr_it_id
													) CR ON (CR.iv_id = IV.iv_id AND CR.cr_it_id = IV.iv_it_id)
													LEFT JOIN (	SELECT	DISTINCT
																							cr_id,
																							iv_id
																			FROM		clearance_item
													) CLR ON (CLR.iv_id = IV.iv_id)
													LEFT JOIN (	SELECT	*
																			FROM		invoice_item
																			WHERE		iv_stats = 40
													) IP ON (IP.iv_id = IV.iv_id AND IP.iv_it_id = IV.iv_it_id)													
									WHERE		1=1
									$AND_SQL
									$OR_SQL
	";
//	ECHO $SELECT_SQL;
}


/* 통관탭의 통관예정과 관련된 발주품목
 * Step1. 발주품목 10
 * Step2. 통관품목 5
 * Step3. 통관품목 5
 * Step4. 해당 발주서 안나오게
*/
else if($mode == 'todoClearanceItem') {

	if($iv_id) {
		$AND_SQL.=" AND IV.iv_id IN (".str_replace("\'","'",$iv_id).") ";
	}

	/* TOTAL COUNT */
	$SELECT_SQL = "	SELECT	IV.number,
													IV.gpcode,						/*같이 묶어서 보길 원할경우 입력*/
													GI.gpcode_name,
													II.ip_id,
													II.wr_id,
													IV.iv_id,
													IFNULL(CLR.cr_id,'- 없음 -') AS cr_id,
													IT.gp_img AS iv_it_img,
													IV.iv_it_id,					/*주문상품코드*/
													IV.iv_it_name,				/*주문상품명*/
													II.money_type,
													IV.iv_dealer_worldprice,			/*주문단가($)*/
													IV.iv_dealer_price,		/*주문단가*/
													IV.iv_qty,						/*발주수량*/
													IV.ip_qty,						/*입고수량*/
													IV.iv_dealer,					/*딜러*/
													(IV.iv_dealer_worldprice * IV.iv_qty) AS total_price,
													IV.iv_stats,
													IV.reg_date,
													CR.cr_it_id,
													IFNULL(CR.cr_qty,0) AS cr_qty,
													IFNULL(CR.cr_cancel_qty,0) AS cr_cancel_qty
									FROM		invoice_item IV
													LEFT JOIN invoice_info II ON (II.iv_id = IV.iv_id)
													LEFT JOIN g5_shop_group_purchase IT ON (IT.gp_id = IV.iv_it_id)
													LEFT JOIN gp_info GI ON (GI.gpcode = IV.gpcode)
													LEFT JOIN (	SELECT	iv_pkno,
																							iv_id,
																							cr_it_id,
																							SUM(cr_qty) AS cr_qty,
																							SUM(cr_cancel_qty) AS cr_cancel_qty
																			FROM		clearance_item
																			GROUP BY iv_pkno, iv_id, cr_it_id
													) CR ON (CR.iv_pkno = IV.number AND CR.cr_it_id = IV.iv_it_id)
													LEFT JOIN (	SELECT	cr_id,
																							iv_pkno,
																							cr_it_id
																			FROM		clearance_item
													) CLR ON (CLR.iv_pkno = IV.number AND CLR.cr_it_id = IV.iv_it_id)
									WHERE		1=1
									AND			IV.iv_stats NOT IN ('99')
									$AND_SQL
	";
//	ECHO $SELECT_SQL;

}

/* 통관완료품목 리스트, 입고관리품목 */
else if($mode == 'endClearanceItem') {
	
	if($cr_id) {
		$AND_SQL.=" AND CI.cr_id IN (".str_replace("\'","'",$cr_id).") ";
	}

	/* TOTAL COUNT */
	$SELECT_SQL = "	SELECT		IV.number,									/*발주품목 상태값 변경을 위한*/ 
														CI.cr_id,										/*통관코드*/
														CI.iv_id,										/*발주코드*/
														II.wr_id,
														II.iv_order_no,							/*인보이스번호*/
														CI.cr_stats,								/*통관현황 상태값*/
														IT.gp_img AS iv_it_img,
														CI.cr_it_id,								/*통관 상품코드*/
														CI.cr_it_name,							/*통관 상품명*/
														II.money_type,							/*통화유형*/
														CI.cr_qty,									/*통관수량*/
														CI.cr_cancel_qty,						/*통관취소수량*/
														CI.reg_date,								/*생성일*/
														II.iv_order_no,							/*인보이스주문번호*/
														IV.gpcode,									/*공구코드*/
														GI.gpcode_name,							/*공구명*/
														IV.iv_it_id,								/*주문상품코드*/
														IV.iv_it_name,							/*주문상품명*/
														IV.iv_dealer_worldprice,		/*주문단가($)*/
														IV.iv_dealer_price,					/*주문단가*/
														
														IV.ip_qty,									/*입고수량*/
														IV.iv_dealer,								/*딜러*/
														(IV.iv_dealer_worldprice * IV.iv_qty) AS total_price,
														IV.iv_stats,
														IFNULL(GPO.SUM_QTY,0) AS SUM_QTY,				/*주문집계*/
														IV.iv_qty,															/*발주수량*/
														IFNULL(IT.jaego	,0) + IFNULL(RIV.RIV_QTY,0) - IFNULL(CO.ORDER_QTY,0) AS real_jaego
									FROM			clearance_item CI
														LEFT JOIN invoice_info II ON (II.iv_id = CI.iv_id)
														LEFT JOIN invoice_item IV ON (IV.gpcode = CI.gpcode AND IV.iv_id = CI.iv_id AND IV.iv_it_id = CI.cr_it_id)
														LEFT JOIN gp_info GI ON (GI.gpcode = IV.gpcode)
														LEFT JOIN g5_shop_group_purchase IT ON (IT.gp_id = CI.cr_it_id)
														
														/*실제발주수량, 가발주포함하는걸로*/
														LEFT JOIN (	SELECT	iv_it_id,
																								SUM(iv_qty) AS RIV_QTY
																				FROM		invoice_item
																				WHERE		1=1
																				#AND			gpcode LIKE '%$목록공구코드%'
																				AND			iv_stats >= 00
																				GROUP BY iv_it_id				
														)	RIV ON (RIV.iv_it_id = CI.cr_it_id)
														
														/* 총주문수량 */
														LEFT JOIN ( SELECT	it_id,
																								SUM(it_qty) AS ORDER_QTY
																				FROM		clay_order
																				WHERE		stats <= 60		/* 취소건 제외, 모든 신청수량 */
																				GROUP BY it_id
														) CO ON (CO.it_id = CI.cr_it_id)
														
														/* 해당공구 총주문수량 */
														LEFT JOIN ( SELECT	gpcode,
																								it_id,
																								it_org_price,
																								SUM(it_qty) AS SUM_QTY
																				FROM		clay_order
																				WHERE		stats <= 22		/* 취소건 제외, 모든 신청수량 */
																				GROUP BY gpcode, it_id
														) GPO ON (GPO.gpcode = CI.gpcode AND GPO.it_id = CI.cr_it_id)
									WHERE		1=1
									$AND_SQL
	";
//	ECHO $SELECT_SQL;

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
		if($key == 'invoice_memo' || $key == 'memo') $row[$key] = preg_replace('/\r\n|\r|\n/','<br>',$val);
		else $row[$key] = 개행문자삭제($val);
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