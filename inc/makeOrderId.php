<?php
/**
 * Created by PhpStorm.
 * User: lucael
 * Date: 2017-06-23
 * Time: 오후 5:59
 */

$시퀀스명 = ($시퀀스명) ? $시퀀스명 : "PB";

/* 옵션 고유ID 생성 SQL    by. JHW */
$seq_sql = "	SELECT	CONCAT(	'$시퀀스명',
																		DATE_FORMAT(now(),'%Y%m%d'),
																		LPAD(COALESCE(	(	SELECT	MAX(SUBSTR(od_id,11,4))
																											FROM		clay_order_info
																											WHERE		od_id LIKE CONCAT('%',DATE_FORMAT(now(),'%Y%m%d'),'%')
																											ORDER BY od_id DESC
																										)
																		,'0000') +1,4,'0')
														)	AS oid
							FROM		DUAL
	";
list($od_id) = mysql_fetch_array(sql_query($seq_sql));

$t = explode('.',_microtime());
$od_id = $od_id."-".$t[1];
?>