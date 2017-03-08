<?php
include_once('./_common.php');

if($_SERTVER[]) 
{
		
}

?>
<style>
body { font-size:12px; font-family: '돋움',Dotum,'굴림',Gulim; padding:0px; background-color: #FFFFFF;  }
table { font-size:12px; font-family: '돋움',Dotum,'굴림',Gulim; clear: both;  border-collapse: collapse; border-spacing: 0; }
table tr th { font-size:12px; font-family: '돋움',Dotum,'굴림',Gulim; background-color: #EEEEEE; border:1px solid #d1dee2; padding:0px; text-align:center; height:25px; }
table tr td {	font-size:12px; font-family: '돋움',Dotum,'굴림',Gulim; background-color: #FFFFFF;  padding-left:10px; border:1px solid #d1dee2; }
</style>

<body topmargin='0' leftmargin='0'>
<?
if($gpcode) {
	echo "<table width='720'><th width='100' align='center'>닉네임</th><th width='510'>상품명</th><th width='80' align='center'>신청수량</th></tr>";
	
	## 1.주문할 공구상품목록
	$vp_sql = "	SELECT	CO.number,	
											CO.gpcode,	/*연결된 공구코드*/
											CO.od_id,	/*주문번호*/
											CO.it_id,	/*주문상품코드*/
											CO.it_qty,	/*주문수량*/
											CO.it_org_price,	/*주문당시 개당 상품가격*/
											CO.clay_id,	/*클레이닉네임*/
											CO.mb_id,	/*홈페이지 계정*/
											CO.hphone,	/*연락처*/
											CO.stats,	/*상태 ( 취소:99, 신청:00, )*/
											CO.od_date,	/*주문일시*/
											CO.name,	/*주문자성함*/
											CO.print_yn,	/*출력여부 ( Y or N )*/
											GP.gp_name
							FROM		clay_order CO
											LEFT JOIN g5_shop_group_purchase GP ON (GP.gp_id = CO.it_id)
							WHERE		gpcode = '$gpcode'
							AND			stats NOT IN (99)
							ORDER BY od_id ASC
	";
	$vp_result = sql_query($vp_sql);
	
	$vp_cnt = 0;
	$vp_maxcnt = mysql_num_rows($vp_result);
	
	while($vp = mysql_fetch_array($vp_result)) {
		echo "<tr><td>$vp[clay_id]</td><td>".$vp[gp_name]."</td><td align='center'>".$vp[it_qty]."</td></tr>";
	}
	echo "</table>";
}

?>
</body>