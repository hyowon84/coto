<?php

include_once('./_common.php');
include_once(G5_PATH.'/adm/shop_admin/admin.shop.lib.php');


/*================================================================================
php_writeexcel http://www.bettina-attack.de/jonny/view.php/projects/php_writeexcel/
=================================================================================*/

include_once(G5_LIB_PATH.'/Excel/php_writeexcel/class.writeexcel_workbook.inc.php');
include_once(G5_LIB_PATH.'/Excel/php_writeexcel/class.writeexcel_worksheet.inc.php');

global $v_delivery_type;

if($is_admin == 'super') {

	$fname = tempnam(G5_DATA_PATH, "tmp-memberlist.xls");
	$workbook = new writeexcel_workbook($fname);
	$worksheet = $workbook->addworksheet();

	// Put Excel data
	$data = array('H','거래일자', '상품명', '공급가액', '부가세', '봉사료', '거래총액', '거래자구분', '주민번호/핸드폰/사업자번호', '세금처리유형/번호', '상점연락처','신청자정보', '발급상태',' 연결주문번호');
	$data = array_map('iconv_euckr', $data);

	$col = 0;
	foreach($data as $cell) {
		$worksheet->write(0, $col++, $cell);
	}

	$출력여부조건 = "	AND		CI.cash_receipt_type IN ('C01','C02') ";

	//현금영수증 미출력에 체크시
	if($cash_receipt_yn == 'Y') {
		$출력여부조건 .= "	AND CI.cash_receipt_cnt = 0 ";
	}


	/* 임시로 사용할 합계 산출 */
	$sql = "	SELECT	BD.tr_date,
										BD.trader_name,
										GROUP_CONCAT( REPLACE(BD.admin_link,'-','') SEPARATOR ',') AS od_links,
										ROUND(SUM(BD.input_price) / 1.1,0) AS price_sp,
										ROUND(SUM(BD.input_price) - SUM(BD.input_price) / 1.1,0) AS price_vat,
										SUM(BD.input_price) AS price_total,
										IF(CI.cash_receipt_type = 'C01',0,1) AS cash_type,
										CI.cash_receipt_type,
										CI.cash_receipt_info,
										CONCAT(CI.clay_id,'/',CI.NAME,'/',CI.hphone) AS person_info,
										CI.clay_id,
										BD.tax_type,
										BD.tax_no
						FROM		(	SELECT	REPLACE(DB.trader_name,'　','') AS trader_name,
															DB.admin_link,
															DB.input_price,
															DB.bank_type,
															DB.tr_date,
															DB.tr_time,
															CC.value AS tax_type,
															DB.tax_no
											FROM		bank_db DB
															LEFT JOIN comcode CC ON (CC.ctype = 'bankdb' AND CC.col = 'tax_type' AND CC.code = DB.tax_type)
										) BD
										LEFT JOIN clay_order_info CI ON (CI.od_id = SUBSTR(REPLACE(BD.admin_link,'-',''),1,14) )
						WHERE		BD.bank_type = 'B01'
						AND			LENGTH(BD.admin_link) > 8
						AND			BD.tr_date >= '2016-01-01'
						GROUP BY BD.trader_name
						ORDER BY	BD.trader_name ASC, BD.tr_date DESC, BD.tr_time DESC
	";
	$result = sql_query($sql);

	$iCount=1;

	while($row = mysql_fetch_array($result)) {
		// 실제 번호를 넘김
		if (!$row[od_links]) continue;

		//C01:0:소득공제용,   C02:1:지출증빙용
		$거래자구분 = ($row[cash_receipt_type] == 'C02') ? 1 : 0;
		$상품명 = $row['trader_name'].'('.substr($row['od_links'],0,19)."...)";
		$세금처리정보 = $row['tax_type'].'('.$row['tax_no'].')';
		
		$worksheet->write($iCount, 0, iconv('utf-8','euc-kr','D'));	/* D */
		$worksheet->write($iCount, 1, iconv('utf-8','euc-kr',$row['tr_date']));	/* 거래일자 */
		$worksheet->write($iCount, 2, iconv('utf-8','euc-kr',$상품명));	/* 상품명 */
		$worksheet->write($iCount, 3, iconv('utf-8','euc-kr',$row['price_sp']));	/* 공급가액 */
		$worksheet->write($iCount, 4, iconv('utf-8','euc-kr',$row['price_vat']));	/* 부가세액 */
		$worksheet->write($iCount, 5, 0);	/* 봉사료 */
		$worksheet->write($iCount, 6, iconv('utf-8','euc-kr',$row['price_total']));	/* 거래총액 */
		$worksheet->write($iCount, 7, $거래자구분);	/* 0:소득공제용, 1:지출증빙용 */
		$worksheet->write($iCount, 8, iconv('utf-8','euc-kr',$row['cash_receipt_info']));	/* 현금영수증 신청 입력정보 */
		$worksheet->write($iCount, 9, iconv('utf-8','euc-kr',$세금처리정보));	/* 세금처리번호 */
		$worksheet->write($iCount, 10, '070-4323-6998');	/* 회사 대표번호 */
		$worksheet->write($iCount, 11, iconv('utf-8','euc-kr',$row['person_info']));	/* 현금영수증 발행을 위한 출력이력 */
		$worksheet->write($iCount, 12, iconv('utf-8','euc-kr','입출금내역수정필요'));
		$worksheet->write($iCount, 13, iconv('utf-8','euc-kr',$row['od_links']));	/* 현금영수증 발행을 위한 출력이력 */
		$iCount++;
	}

	$workbook->close();


	$upd_sql = "	UPDATE	clay_order_info CI	SET
													CI.cash_receipt_print = 'Y',
													CI.cash_receipt_cnt = (CI.cash_receipt_cnt * 1 + 1)
								WHERE		CI.cash_receipt_yn = 'Y'
								$출력여부조건
	";
	sql_query($upd_sql);


	if($iCount==1)alert("자료가 존재하지 않습니다.");
	else{
		header("Content-Type: application/x-msexcel; name=\"cash-".date("ymd", time()).".xls\"");
		header("Content-Disposition: inline; filename=\"cash-".date("ymd", time()).".xls\"");
		$fh=fopen($fname, "rb");
		fpassthru($fh);
		unlink($fname);

		exit;
	}

}

?>