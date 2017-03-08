<?php

$prev_sql = "	SELECT	*
							FROM		clay_order
							WHERE		gpcode	= '$gpcode'		/*연결된 공구코드*/
							AND			od_id		= '$od_id'		/*주문ID*/
							AND			number = '$number'
";
$prev = mysql_fetch_array(sql_query($prev_sql));


/* 로그 관련 */
if($prev[it_qty] != $it_qty) {
	$ins_sql = "INSERT INTO 		log_table	SET
																logtype = 'clayorder',		/*로그유형 ( ex: clayorder )*/
																gr_id	= '$od_id',				/*pk_id*/
																pk_id	= '$number',			/*pk_id*/
																it_id		= '$prev[it_id]',
																memo = '주문수량',			/*메모태그*/
																admin_id = '$mb_id',		/*로그를 남긴 관리자ID*/
																col = 'it_qty',				/*변경 항목*/
																value = '$prev[it_qty] -> $it_qty',					/*변경된 값*/
																reg_date = now()				/*변경된 날짜*/
	";
	sql_query($ins_sql);
}
if($prev[it_org_price] != $it_org_price) {
	$ins_sql = "INSERT INTO 		log_table	SET
																logtype = 'clayorder',		/*로그유형 ( ex: clayorder )*/
																gr_id	= '$od_id',				/*pk_id*/
																pk_id	= '$number',			/*pk_id*/
																it_id		= '$prev[it_id]',
																memo = '판매가',			/*메모태그*/
																admin_id = '$mb_id',		/*로그를 남긴 관리자ID*/
																col = 'it_org_price',				/*변경 항목*/
																value = '".$prev[it_org_price].' -> '.$it_org_price."',					/*변경된 값*/
																reg_date = now()				/*변경된 날짜*/
	";
	sql_query($ins_sql);
}
if($prev[stats] != $stats) {
	$ins_sql = "INSERT INTO 		log_table	SET
																logtype = 'clayorder',		/*로그유형 ( ex: clayorder )*/
																gr_id	= '$od_id',				/*pk_id*/
																pk_id	= '$number',			/*pk_id*/
																it_id		= '$prev[it_id]',
																memo = '주문상태',			/*메모태그*/
																admin_id = '$mb_id',		/*로그를 남긴 관리자ID*/
																col = 'stats',				/*변경 항목*/
																value = '".$v_stats[$prev[stats]].' -> '.$v_stats[$stats]."',					/*변경된 값*/
																reg_date = now()				/*변경된 날짜*/
	";
	sql_query($ins_sql);
}

?>