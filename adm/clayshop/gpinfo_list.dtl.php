<?php
include_once('./_common.php');
auth_check($auth[$sub_menu], "r");


if($start_date) {
	$시작일 = date("Y-m-d", strtotime($start_date));
	$시작일조건 = "	AND	CL.od_date >= '$시작일 00:00:00' ";
}

if($end_date) {
	$종료일 = date("Y-m-d",strtotime($end_date));
	$종료일조건 = "	AND	CL.od_date <= '$종료일 23:59:59' ";
}

/* 볼륨가 업데이트 */
$gpinfo_sql = "	SELECT	GI.*,
												FD.USD
								FROM		gp_info GI,
												(	SELECT	*
													FROM		flow_price
													ORDER BY reg_date DESC
													LIMIT 1
												) FD
								WHERE		GI.gpcode = '$gpcode'
";
$gi_result = sql_query($gpinfo_sql);

$공구정보 = mysql_fetch_array($gi_result);



/*******************************************************************************************************************************************************************************************/
$총합계금액 = 0;
$총수량 = 0;


## 발주 버튼 - 주문할 공구상품목록 출력
if($mode == 'orderlist') {

	echo "[$시작일 ~ $종료일] 발주예정목록<br>";
	echo "<table width='100%'><tr><td>상품코드</td><td>상품명</td><td>주문수량</td><td>단가</td></tr>";
	$vp_sql = "			SELECT	CLS.it_id,
													CLS.gpcode,
													CLS.it_org_price,
													CLS.SUM_QTY,
													CLS.it_org_price * CLS.SUM_QTY AS total_price,
													GP.gp_name,
													GP.gp_img,
													GP.jaego,
													GP.gp_have_qty,
													GPO.po_num,
													GPO.po_sqty,			/*최소신청수량*/
													GPO.po_eqty,			/*최대신청수량*/
													GPO.po_cash_price,
													GPO.po_card_price,
													CEIL((( GPO.po_cash_price * FD.USD * (1+ (GP.gp_charge + GP.gp_duty)/100) ) * 1.1) /100)*100 AS volprice
									FROM		(	SELECT	CL.gpcode,
																		CL.it_id,					/* 주문상품코드 */
																		CL.it_org_price,	/* 주문당시 금액 */
																		SUM(CL.it_qty) AS SUM_QTY		/* 주문당시 합계수량 */
														FROM		clay_order CL
																		LEFT JOIN gp_info GI ON (GI.gpcode = CL.gpcode)
														WHERE		1=1
														AND			CL.stats NOT IN ('99','90')
														$시작일조건
														$종료일조건
														GROUP BY 	CL.gpcode, CL.it_id
													) CLS

													LEFT JOIN g5_shop_group_purchase GP ON (GP.gp_id = CLS.it_id)
													LEFT JOIN g5_shop_group_purchase_option GPO ON (GPO.gp_id = CLS.it_id AND GPO.po_sqty <= CLS.SUM_QTY AND GPO.po_eqty >= CLS.SUM_QTY)
													,(SELECT	*
														FROM		flow_price
														ORDER BY reg_date DESC
														LIMIT 1
													) FD
									WHERE		1=1
									AND				CLS.gpcode = '$gpcode'

									ORDER BY 	(CLS.SUM_QTY * CLS.it_org_price) DESC, CLS.it_id ASC, GPO.po_num ASC
	";
	$vp_result = sql_query($vp_sql);

	$vp_cnt = 0;
	$vp_maxcnt = mysql_num_rows($vp_result);

	while($vp = mysql_fetch_array($vp_result)) {
		echo "<tr><td>$vp[it_id]</td><td>$vp[gp_name]</td><td align='right'>".$vp[SUM_QTY]."</td><td align='right'>$ ".$vp[po_cash_price]."</td></tr>";
	}
	echo "</table>";
	exit;

}

## 공구레코드 - 개별 상품통계 목록들
if($mode == 'itemlist') {

	echo "<table width='100%'><tr><td><textarea style='width:100%; height:25px;'>";
	echo "<a href='/shop/list.php?gpcode={$공구정보[gpcode]}'>
					<img src='http://coinstoday.co.kr/screenshot/img/order_{$공구정보[gpcode]}.jpg' />
				</a>";
	echo "<br>";
	echo "<a href='/shop/list.php?gpcode={$공구정보[gpcode]}'>
					<img src='http://coinstoday.co.kr/screenshot/img/buyer_{$공구정보[gpcode]}.jpg' />
				</a>";
	echo "</textarea></td></tr><tr><td><font color=red>ERROR주문(상품가 0원) : ";


	$ERROR_SQL = "	SELECT	*
									FROM		clay_order CL
									WHERE		CL.gpcode = '$gpcode'
									AND			(CL.it_org_price = 0 OR CL.it_org_price IS NULL OR CL.it_org_price = '')
									AND			CL.stats NOT IN ('99','90')
	";
	$ERROR_RESULT = sql_query($ERROR_SQL);

	while($ERROR = mysql_fetch_array($ERROR_RESULT)) {
		echo "[$ERROR[od_id]] $ERROR[it_id], ";
	}

	echo "</font></td></tr>";



	/* 시작태그 */
	echo "<tr><td>";

	## 1.주문할 공구상품목록
	$vp_sql = "			SELECT	CLS.it_id,
													CLS.gpcode,
													CLS.it_org_price,
													CLS.SUM_PAY,
													CLS.it_org_price * CLS.SUM_QTY AS total_price,
													GP.gp_name,
													GP.gp_img,
													GP.jaego,
													GP.gp_have_qty,
													GPO.po_num,
													GPO.po_sqty,			/*최소신청수량*/
													GPO.po_eqty,			/*최대신청수량*/
													GPO.po_cash_price,
													GPO.po_card_price,
													
													CLS.SUM_QTY,	/*합계수량*/
													IFNULL(S00.CNT,0) AS S00,
													IFNULL(S10.CNT,0) AS S10,
													IFNULL(S20.CNT,0) AS S20,
													IFNULL(S22.CNT,0) AS S22,
													CEIL((( GPO.po_cash_price * FD.USD * (1+ (GP.gp_charge + GP.gp_duty)/100) ) * 1.1) /100)*100 AS volprice
									FROM		(	SELECT	CL.gpcode,
																		CL.it_id,					/* 주문상품코드 */
																		CL.it_org_price,	/* 주문당시 금액 */
																		SUM(CL.it_qty) AS SUM_QTY,		/* 주문당시 합계수량 */
																		SUM(CL.it_qty * CL.it_org_price) AS SUM_PAY		/* 주문당시 합계수량 */
														FROM		clay_order CL
														WHERE		1=1
														AND			CL.stats <= '60'
														$시작일조건
														$종료일조건
														GROUP BY 	CL.gpcode, CL.it_id
													) CLS
													LEFT JOIN g5_shop_group_purchase GP ON (GP.gp_id = CLS.it_id)
													LEFT JOIN g5_shop_group_purchase_option GPO ON (GPO.gp_id = CLS.it_id)
													LEFT JOIN	(	SELECT	CL.gpcode,
																							CL.it_id,					/* 주문상품코드 */
																							CL.stats,
																							SUM(CL.it_qty) AS CNT
																			FROM		clay_order CL
																			WHERE		1=1
																			AND			CL.stats IN ('00')
																			$시작일조건
																			$종료일조건
																			GROUP BY 	CL.gpcode, CL.it_id, CL.stats
													) S00 ON (S00.gpcode = CLS.gpcode AND S00.it_id = CLS.it_id)
													LEFT JOIN	(	SELECT	CL.gpcode,
																							CL.it_id,					/* 주문상품코드 */
																							CL.stats,
																							SUM(CL.it_qty) AS CNT
																			FROM		clay_order CL
																			WHERE		1=1
																			AND			CL.stats IN ('10')
																			$시작일조건
																			$종료일조건
																			GROUP BY 	CL.gpcode, CL.it_id, CL.stats
													) S10 ON (S10.gpcode = CLS.gpcode AND S10.it_id = CLS.it_id)
													LEFT JOIN	(	SELECT	CL.gpcode,
																							CL.it_id,					/* 주문상품코드 */
																							CL.stats,
																							SUM(CL.it_qty) AS CNT
																			FROM		clay_order CL
																			WHERE		1=1
																			AND			CL.stats IN ('20')
																			$시작일조건
																			$종료일조건
																			GROUP BY 	CL.gpcode, CL.it_id, CL.stats
													) S20 ON (S20.gpcode = CLS.gpcode AND S20.it_id = CLS.it_id)
													LEFT JOIN	(	SELECT	CL.gpcode,
																							CL.it_id,					/* 주문상품코드 */
																							CL.stats,
																							SUM(CL.it_qty) AS CNT
																			FROM		clay_order CL
																			WHERE		1=1
																			AND			CL.stats IN ('22')
																			$시작일조건
																			$종료일조건
																			GROUP BY 	CL.gpcode, CL.it_id, CL.stats
													) S22 ON (S22.gpcode = CLS.gpcode AND S22.it_id = CLS.it_id)
													,(SELECT	*
														FROM		flow_price
														ORDER BY reg_date DESC
														LIMIT 1
													) FD
									WHERE		1=1
									AND				CLS.gpcode = '$gpcode'
									ORDER BY 	(CLS.SUM_QTY * CLS.it_org_price) DESC, CLS.it_id ASC, GPO.po_num ASC
	";
	$vp_result = sql_query($vp_sql);


	$vp_cnt = 0;
	$vp_maxcnt = mysql_num_rows($vp_result);


	while($vp = mysql_fetch_array($vp_result)) {
		$vp_cnt++;


		/* http 가 들어간건 다이렉트로, 아닌건 get_it_thumb함수로 */
		if( strstr($vp[gp_img],'http')) {
			$이미지 = "<img src='$vp[gp_img]' width=80 height=80 />";
		}
		else {
			$이미지 = get_it_thumbnail1($vp[gp_img], 80, 80, '', 1);
		}
		/* 자사URL이미지로 잡혀있는거는 리사이징 */
		if( strstr($vp[gp_img],'coinstoday') ) {
			$path = str_replace("http://coinstoday.co.kr","",$vp[gp_img]);
			$이미지 = "<img src='/image.php/$vp[gp_id].jpg?width=80&height=80&image=$path' alt='$vp[gp_id]'>";
		}


		if( ($이전it_id != $vp[it_id]) ) {
			$주문통계 = "<b><font color='green'>주문신청 : $주문신청<br>입금요청 : $입금요청</font><br><font color='#ff7e00'>결제완료 : $결제완료</font><br><font color='blue'>통합요청 : $통합요청</font></b><br>";

			$html = "<div class='div_volprice' style='width:300px; height:auto; float:left;'>
								<table class='hover_skyblue' style='width:300px; height:auto; float:left; clear:none;'>";
			echo ($vp_cnt == 1) ? $html : "<tr><td colspan='2'>재고수량: $재고수량 개<br> $주문통계 합계수량: $합계수량 개<br>예상총액: $".$총주문금액_달러." ( {$총주문금액}원 )</td></tr></table></div>".$html;

			echo "<tr class='divTr_order' onclick=\"loadOrderDetail('orderlist_itid','$vp[gpcode]','$vp[it_id]')\"><td>$이미지</td><td style='text-align:left;'>[$vp[it_id]] $vp[gp_name]</td></tr>";
			echo "<tr><th>수량</th><th>구매가</th></tr>";
		}

		$주문신청 = $vp[S00];
		$입금요청 = $vp[S10];
		$결제완료 = $vp[S20];
		$통합요청 = $vp[S22];
		$합계수량 = number_format($vp[SUM_QTY]);

		/* 볼륨가에 해당하는 경우 */
		if($공구정보[volprice_yn] == 'Y' && $vp[po_sqty]*1 <= $vp[SUM_QTY]*1 && $vp[po_eqty]*1 >= $vp[SUM_QTY]*1) {
			$bgcolor = "style='background-color:#fff8c7;'";

			$총주문금액 = number_format($vp[volprice] * $vp[SUM_QTY]);
			$총주문금액_달러 = number_format($vp[po_cash_price] * $vp[SUM_QTY],2);
		}
		else {
			/* 볼륨가 적용안함일경우 레코드는 한개이므로 통계낸 SUM_QTY컬럼을 이용 */

			if($공구정보[volprice_yn] == 'N' || !$vp[volprice] ) {

				$총주문금액 = number_format($vp[total_price]);
				$총주문금액_달러 = CEIL($vp[total_price] / ($공구정보[USD] * 1.06 * 1.1) * 100) / 100;
				//$예상달러금액 = CEIL(($po_cash_price / ($공구정보[USD] * 1.06 * 1.1)) * 100) / 100;
			}
			$bgcolor = "";
		}


		if($공구정보[volprice_yn] == 'Y') {
	?>
		<tr <?=$bgcolor?>>
			<td><?=number_format($vp[po_sqty])?> ~ <?=number_format($vp[po_eqty])?></td>
			<td>$<?=number_format($vp[po_cash_price],2)?> (<?=number_format($vp[volprice])?>원)</td>
		</tr>
	<?
		}

		if($공구정보[volprice_yn] == 'N') {
			$재고수량 = $vp[gp_have_qty];
		} else {
			$재고수량 = $vp[jaego];
		}

		if($vp_cnt == $vp_maxcnt) {
			$주문통계 = "<b></b><font color='green'>주문신청 : $주문신청<br>입금요청 : $입금요청</font><br><font color='#ff7e00'>결제완료 : $결제완료</font><br><font color='blue'>통합요청 : $통합요청</font></b><br>";
			echo "<tr><td colspan='2'>재고수량:$재고수량 개<br> $주문통계 합계수량: $합계수량 개<br>예상총액: $".$총주문금액_달러." ( {$총주문금액}원 )</td></tr></table></div>";
		}



		$이전it_id = $vp[it_id];

	}// while end
	echo "</td></tr></table>";


	$총합계금액 = 0;
	$총수량 = 0;
	//echo "<div style='clear:all; width:100%; height:3px; background-color:gray;'></div>";
	echo "<table width='100%'><tr><td>";

	## 2. 공구 참여자 목록
	$od_sql = "	SELECT	CL.gpcode,
											CL.clay_id,					/*주문상품코드*/
											CL.hphone,
											SUM(CL.it_qty) AS SUM_QTY,
											SUM(CL.it_org_price * CL.it_qty) AS SUM_PAY,
											S0.QTY_NOPAY,		/* 미결제된 수량 */
											S2.QTY_PAY			/* 결제된 수량 */
							FROM		clay_order CL
											LEFT JOIN (	SELECT	CL.gpcode,
																					CL.hphone,
																					SUM(CL.it_qty) AS QTY_NOPAY
																	FROM		clay_order CL
																	WHERE		CL.stats IN (00,10)
																	$시작일조건
																	$종료일조건
																	GROUP BY CL.gpcode,CL.hphone
											) S0 ON (S0.gpcode = CL.gpcode AND S0.hphone = CL.hphone)
											LEFT JOIN (	SELECT	CL.gpcode,
																					CL.hphone,
																					SUM(CL.it_qty) AS QTY_PAY
																	FROM		clay_order CL
																	WHERE		CL.stats NOT IN (99,00,10)
																	$시작일조건
																	$종료일조건
																	GROUP BY CL.gpcode,CL.hphone
											) S2 ON (S2.gpcode = CL.gpcode AND S2.hphone = CL.hphone)
							WHERE		1=1
							AND				CL.gpcode = '$gpcode'
							AND				CL.stats NOT IN ('99','90')
							$시작일조건
							$종료일조건
							GROUP BY 	CL.gpcode, CL.clay_id, CL.hphone
							ORDER BY 	SUM(CL.it_qty * CL.it_org_price) DESC, CL.clay_id ASC
	";
	$od_result = sql_query($od_sql);


	$od_cnt = 0;
	$od_maxcnt = mysql_num_rows($od_result);

	while($od = mysql_fetch_array($od_result)) {

		if(($od_cnt % 10) == 0) {
			$html = "<div class='div_order' style='width:500px; float:left; clear:none; margin-left:20px; margin-bottom:20px;'><table>";
			echo ($od_cnt == 0) ? $html : "</table></div>".$html;
			echo "<tr><th width=300>닉네임(HP)</th><th width=80>주문수량</th><th width=80 style='background-color:#ffc1c1'>미결제<br>수량</th><th width=120>총주문금액</th></tr>";
		}

		if($od[QTY_NOPAY] > 0)
		{
			$bgcolor = " bgcolor='#ffe7e7'";
			$style = " style='color:red; font-weight:bold;'";
		}
		else {
			$bgcolor = "";
			$style = "";
		}
	?>
		<tr class='hover_skyblue' <?=$bgcolor?> onclick="loadOrderDetail('orderlist_clayid','<?=$od[gpcode]?>','<?=$od[clay_id]?>','<?=$od[hphone]?>')">
			<td style='text-align:left; padding-left:10px;'><?="$od[clay_id]($od[hphone])"?></td>
			<td><?=number_format($od[SUM_QTY])?></td>
			<td <?=$style?>><?=number_format($od[QTY_NOPAY])?></td>
			<td style='text-align:right; padding-right:10px;'><?=number_format($od[SUM_PAY])?></td>
		</tr>
	<?
		if($od_cnt == ($od_maxcnt-1)) echo "</table></div>";
		$od_cnt++;
		$총합계금액 += ($od[SUM_PAY]);
		$총수량 += $od[SUM_QTY];
	}
	echo "</td></tr><tr><td>[$시작일 ~ $종료일] 총 수량 : $총수량 , 총 합계금액 : ".number_format($총합계금액)."</td></tr></table>";
}

/*******************************************************************************************************************************************************************************************/

/* 주문할 공구상품목록 */
if($mode == 'orderlist_clayid' || $mode == 'orderlist_itid') {

	if($mode == 'orderlist_clayid') {
		//그룹태그 or 상품코드 기준으로 SELECT SQL
		$common_sql = "	SELECT	CL.od_id,					/*주문번호*/
														CL.it_id,					/*주문상품코드*/
														CL.it_qty,				/*주문수량*/
														CL.it_org_price,	/*주문당시 개당 상품가격*/
														CL.clay_id,				/*클레이닉네임*/
														CL.mb_id,					/*홈페이지 계정*/
														CL.hphone,				/*연락처*/
														CL.stats,					/*상태 ( 취소:99, 신청:00, )*/
														CL.od_date,				/*주문일시*/
														CL.name,					/*주문자성함*/
														CL.print_yn,			/*출력여부 ( Y or N )*/
														GI.gpcode,
														GP.gp_id,
														GP.gp_name,
														GP.gp_img,
														CC.value AS stats_name
										FROM		clay_order CL
														LEFT JOIN gp_info GI ON (GI.gpcode = CL.gpcode)
														LEFT JOIN g5_shop_group_purchase GP ON (GP.gp_id = CL.it_id)
														LEFT JOIN comcode CC ON (CC.ctype = 'clayorder' AND CC.col = 'stats' AND CC.code = CL.stats)
										WHERE		1=1
										AND			GI.gpcode		= '$gpcode'
										AND			CL.clay_id	= '$clay_id'
										AND			CL.hphone		= '$hphone'
										AND			CL.stats NOT IN ('99','90')
										$시작일조건
										$종료일조건
										ORDER BY 	CL.stats ASC, (CL.it_qty * CL.it_org_price) DESC, CL.clay_id ASC, CL.it_id DESC
		";
	}
	else if($mode == 'orderlist_itid') {
		//그룹태그 or 상품코드 기준으로 SELECT SQL
		$common_sql = "	SELECT	CL.od_id,					/*주문번호*/
														CL.it_id,					/*주문상품코드*/
														CL.it_qty,				/*주문수량*/
														CL.it_org_price,	/*주문당시 개당 상품가격*/
														CL.clay_id,				/*클레이닉네임*/
														CL.mb_id,					/*홈페이지 계정*/
														CL.hphone,				/*연락처*/
														CL.stats,					/*상태 ( 취소:99, 신청:00, )*/
														CL.od_date,				/*주문일시*/
														CL.name,					/*주문자성함*/
														CL.print_yn,			/*출력여부 ( Y or N )*/
														GP.gp_id,
														GI.gpcode,
														GP.gp_name,
														GP.gp_img,
														CC.value AS stats_name
										FROM		clay_order CL
														LEFT JOIN gp_info GI ON (GI.gpcode = CL.gpcode)
														LEFT JOIN g5_shop_group_purchase GP ON (GP.gp_id = CL.it_id)
														LEFT JOIN comcode CC ON (CC.ctype = 'clayorder' AND CC.col = 'stats' AND CC.code = CL.stats)
										WHERE		1=1
										AND			GI.gpcode	= '$gpcode'
										AND			CL.it_id	= '$it_id'
										#AND		CL.stats NOT IN ( IF(GI.volprice_yn = 'N',CONCAT(00,',',10),'') )	/*일반 */
										AND			CL.stats NOT IN ('99','90')
										$시작일조건
										$종료일조건
										ORDER BY 	CL.stats ASC, (CL.it_qty * CL.it_org_price) DESC, CL.clay_id ASC, CL.it_id DESC
		";
	}

	$result = sql_query($common_sql);

	$total_count = mysql_num_rows(sql_query($common_sql));
	?>
	<script>

	/* 수동가격갱신 */
	function updateProductPrice() {
		var gpcode,gp_id,price;
		gpcode = '<?=$gpcode?>';
		it_id = '<?=$it_id?>';
		price = $('#price').val();

		$.post('gpinfo_list.inp.php',
				{
					'mode'		:	'updateProductPrice',
					'gpcode'	: gpcode,
					'it_id'		: it_id,
					'price'		: price
				},
				function( data ) {
					if(data == 1) {
						alert('갱신 완료');
					} else {
						alert('갱신 실패');
					}
				}
		);
	}
	</script>

	<input type='text' id='price' value='' />
	<input type='button' onclick='updateProductPrice()' value='가격수정' />


	<table style='clear:left;'>
		<tr>
			<th width='120'>닉네임</th>
			<th width='60'>이미지</th>
			<th width='400'>상품명</th>
			<th width='120'>단가</th>
			<th width='60'>수량</th>
			<th width='180'>주문금액</th>
			<th width='60'>상태</th>
		</tr>
		<?
		while($row = mysql_fetch_array($result)) {
			/* http 가 들어간건 다이렉트로, 아닌건 get_it_thumb함수로 */
			if( strstr($row[gp_img],'http')) {
				$이미지 = "<img src='$row[gp_img]' width=60 height=60 />";
			}
			else {
				$이미지 = get_it_thumbnail1($row[gp_img], 60, 60, '', 1);
			}

			/* 자사URL이미지로 잡혀있는거는 리사이징 */
			if( strstr($row[gp_img],'coinstoday') ) {
				$path = str_replace("http://coinstoday.co.kr","",$row[gp_img]);
				$이미지 = "<img src='/image.php/$row[gp_id].jpg?width=60&height=60&image=$path' alt='$row[gp_id]'>";
			}


			$닉네임 = $row[clay_id];
			$상품명 = "[$row[gp_id]] $row[gp_name]";
			$상품단가 = $row[it_org_price]*1;
			$신청수량 = $row[it_qty]*1;
			$주문금액 = number_format($상품단가 * $신청수량);
			$주문상태 = $row[stats_name];

			$총주문수량 += $신청수량;
			$총주문금액 += ($상품단가 * $신청수량);
		?>
		<tr>
			<td><?=$닉네임?></td>
			<td><?=$이미지?></td>
			<td style='text-align:left;'><?=$상품명?></td>
			<td style='text-align:right;'><?=number_format($상품단가)?></td>
			<td style='text-align:right;'>(<?=number_format($신청수량)?> 개)</td>
			<td style='text-align:right;'><?=$주문금액?></td>
			<td><?=$주문상태?></td>
		</tr>
		<?
		}
		?>
		<tr>
			<td colspan='7'>
				총 주문수량(<?=number_format($총주문수량)?>) ,  총 주문금액(<?=number_format($총주문금액)?>)
			</td>
		</tr>
		</table>
<?
}


/* 주문자신청건 버튼 */
if($mode == 'allbuyerlist') {
	//그룹태그 or 상품코드 기준으로 SELECT SQL
	$common_sql = "	SELECT	CL.od_id,					/*주문번호*/
													CL.it_id,					/*주문상품코드*/
													CL.it_qty,				/*주문수량*/
													CL.it_org_price,	/*주문당시 개당 상품가격*/
													CL.clay_id,				/*클레이닉네임*/
													CL.mb_id,					/*홈페이지 계정*/
													CL.hphone,				/*연락처*/
													CL.stats,					/*상태 ( 취소:99, 신청:00, )*/
													CL.od_date,				/*주문일시*/
													CL.name,					/*주문자성함*/
													CL.print_yn,			/*출력여부 ( Y or N )*/
													GP.gp_id,
													GI.gpcode,
													GP.gp_name,
													GP.gp_img,
													CI.delivery_price,
													CCDT.value AS deliverytype_name,
													CCST.value AS stats_name
									FROM		clay_order CL
													LEFT JOIN clay_order_info CI ON (CI.gpcode = CL.gpcode AND CI.od_id = CL.od_id)
													LEFT JOIN comcode CCDT ON (CCDT.ctype = 'clayorder' AND CCDT.col = 'delivery_type' AND CCDT.code = CI.delivery_type)
													LEFT JOIN gp_info GI ON (GI.gpcode = CL.gpcode)
													LEFT JOIN g5_shop_group_purchase GP ON (GP.gp_id = CL.it_id)
													LEFT JOIN comcode CCST ON (CCST.ctype = 'clayorder' AND CCST.col = 'stats' AND CCST.code = CL.stats)
									WHERE		1=1
									AND			GI.gpcode	= '$gpcode'
									#AND		CL.stats NOT IN ( IF(GI.volprice_yn = 'N',CONCAT(00,',',10),'') )	/*일반 */
									AND			CL.stats NOT IN ('99','90')
									$시작일조건
									$종료일조건
									ORDER BY 	CL.stats ASC, CL.clay_id ASC, CL.it_id DESC
	";
	$result = sql_query($common_sql);
	$total_count = mysql_num_rows(sql_query($common_sql));


	echo "[$시작일 ~ $종료일]  주문자별 신청건 ";
	?>



	<table style='width:740px; clear:left;'>
		<tr>
			<th width=''>닉네임</th>
			<th width=''>상품명</th>
			<th width=''>단가</th>
			<th width=''>수량</th>
			<th width=''>주문금액</th>
			<th width=''>주문상태</th>
		</tr>
		<?
		$cnt = 0;

		while($row = mysql_fetch_array($result)) {

			//$total_count
			if($cnt != 0 && $닉네임 != $row[clay_id]) {

				//배송비까지 합산
				$구매자별총주문금액 += $이전배송비;
			?>
			<tr style='background:#EEEEEE;'>
				<td style='text-align:right;' colspan=4>배송비(<?=$이전배송유형?>)</td>
				<td style='text-align:right;'><?=number_format($이전배송비)?>원</td>
				<td>&nbsp;</td>
			</tr>
			<tr style='background:#d2f5ff;'>
				<td style='text-align:right;' colspan=3>TOTAL</td>
				<td style='text-align:right;'><?=number_format($구매자별총주문수량)?>개</td>
				<td style='text-align:right;'><?=number_format($구매자별총주문금액)?>원</td>
				<td>&nbsp;</td>
			</tr>

			<?
				$구매자별총주문수량 = 0;
				$구매자별총주문금액 = 0;
			}

			$닉네임 = $row[clay_id];
			$상품명 = $row[gp_name];
			$상품단가 = $row[it_org_price]*1;
			$신청수량 = $row[it_qty]*1;
			$주문금액 = $상품단가 * $신청수량;
			$주문상태 = $row[stats_name];

			$구매자별총주문수량 += $신청수량;
			$구매자별총주문금액 += $주문금액;

			$총주문수량 += $신청수량;
			$총주문금액 += ($상품단가 * $신청수량);
		?>
		<tr>
			<td><?=$닉네임?></td>
			<td style='text-align:left; font-size:9px;'><?=$상품명?></td>
			<td style='text-align:right;'><?=number_format($상품단가)?></td>
			<td style='text-align:right;'><?=number_format($신청수량)?>개</td>
			<td style='text-align:right;'><?=number_format($주문금액)?></td>
			<td><?=$주문상태?></td>
		</tr>
		<?
			$cnt++;

			//마지막줄
			if($cnt == $total_count) {

				//배송비까지 합산
				$구매자별총주문금액 += $row[delivery_price];
				?>
				<tr style='background:#EEEEEE;'>
					<td style='text-align:right;' colspan=3>배송비</td>
					<td style='text-align:right;'><?=$row[deliverytype_name]?></td>
					<td style='text-align:right;'><?=number_format($row[delivery_price])?>원</td>
					<td></td>
				</tr>
				<tr style='background:#d2f5ff;'>
					<td style='text-align:right;' colspan=3>TOTAL</td>
					<td style='text-align:right;'><?=number_format($구매자별총주문수량)?>개</td>
					<td style='text-align:right;'><?=number_format($구매자별총주문금액)?>원</td>
					<td>&nbsp;</td>
				</tr>
		<?
			}
			$이전닉넴 = $row[clay_id];
			$이전배송유형 = $row[deliverytype_name];
			$이전배송비 = $row[delivery_price];
		}//while end
		?>
		</table>
<?
}
?>
</div>