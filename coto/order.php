<?php
	include_once('./_common.php');
	include_once(G5_MSHOP_PATH.'/head.php');
	global $is_admin;

	//외환은행
	$bankUrl = "http://fx.keb.co.kr/FER1101C.web?schID=fex&mID=FER1101C";
	$bankSource = iconv("euc-kr","utf-8",curl($bankUrl));

	$temp_unit = explode("<td class='grid_money' title='송금보내실때'>",$bankSource);
	$temp_unit2 = explode("<td class='grid_money' title='매매기준율'>",$bankSource);

	/* 미국 달러 */
	$usd_unit = explode("</td>",$temp_unit[2]);
	$usd_unit2 = explode("</td>",$temp_unit2[2]);
	$송금보낼때환율 = $usd_unit[0];		//송금보낼때
	$매매기준환율 = $usd_unit2[0];	//매매기준율

	alert('현재 페이지는 개편되어 사용할수 없습니다. 공지된 새로운 URL을 확인후 접속해주세요','/');
	exit;
?>
<script src="<?php echo G5_JS_URL; ?>/shop.mobile.main.js"></script>
<script src="/mobile/js/jquery.flexslider.js"></script>
<script src="<?php echo G5_JS_URL; ?>/common.js"></script>
<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<script charset="UTF-8" type="text/javascript" src="http://s1.daumcdn.net/svc/attach/U03/cssjs/postcode/1438157450275/150729.js"></script>

<script src="<?=G5_JS_URL?>/common_order.js"></script>

<style>
.clayOrderTitle { margin:10px; font-size:1.5em; font-weight:bolder; text-align:center; }
.clayOrderTB th, .clayOrderTB td { font-size:0.9em; letter-spacing:-0.5px; }
.clayOrderTB th{ padding-left:5px; width:25%; height:30px; text-align:left; }
.clayOrderTB td{ padding-left:5px; width:75%; height:30px; text-align:left; }
.clayOrderTB .text { font-size:1.3em; width:160px; }

input.clayOrderTB { height:26px; font-size:1.3em; margin:3px; border:1px solid #d7d7d7; }
.clayOrderTB .btn { width:105px; }
.clayOrderTB .zip { width:70px; }
.clayOrderTB .address { width:200px; font-size:0.9em; }
.clayOrderTB .hphone { width:40px; }
.clayOrderTB .bno { width:50px; }
.clayOrderTB .divAddrTitle { height:24px; padding-top:10px; padding-left:10px; }

#ft { padding-top:0px; }

.div_volprice table
{
	clear: both;
	width: 100%;
	border-collapse: collapse;
	border-spacing: 0;
	border-color: grey;
}
.div_volprice table tr th, .div_volprice table tr td{ text-align:center; border:1px solid #d1dee2; }
.div_volprice table tr th { height:25px; background-color:#EEEEEE; }
.div_volprice table tr td { height:25px; }

</style>


<div style='width:100%;'>
	<form name='clayOrderForm' action='order_insert.php?gpcode=<?=$gpcode?>' method='post' onsubmit='return chkForm();'>
<?

	//공구코드 없이, 다이렉트주문의 케이스
	if(!$gpcode && $개인구매코드 && $it_id) {
		$gpcode = $개인구매코드;
		$개인주문 = true;
	}

	/* 현재 진행중인 공구정보 로딩 */
	$gpinfo_sql = "	SELECT	*
									FROM		gp_info
									WHERE		gpcode = '$gpcode'
									AND			start_date <= DATE_FORMAT(now(),'%Y-%m-%d')
									AND			end_date	>= DATE_FORMAT(now(),'%Y-%m-%d')
									AND			stats IN ('00','10')
	";
	$공구정보 = sql_fetch($gpinfo_sql);


	/* 진행상황이 마감일경우 경고메시지 띄우고 종료 */
	if(!$공구정보[stats]) {
		echo "<script>
			alert('해당 공동구매는 마감되었습니다');
			location.href='/';
		</script>";
		exit;
	}

	/* 공구정보가 존재할경우 */
	$공구유형 = $공구정보[gp_type];
	$시작일 = $공구정보[start_date];
	$종료일 = $공구정보[end_date];
	$배송비 = $공구정보[baesongbi];


	//공동구매 셋팅
	if(!$개인주문 && $공구정보[gpcode]) {
		$it_id = $공구정보[links];
		$id_list = explode(',',$it_id);
		$구매유형별조건 = "	AND		(	gpcode = '$gpcode' OR gpcode = '$개인구매코드' )	/* 해당공구 및 해당공구기간안의 개별주문 */
												AND			od_date BETWEEN '$시작일 00:00:00' AND '$종료일 23:59:59'	";
	}
	//개인주문 셋팅
	else {	//직접구매일경우
		$id_list[] = $it_id;
		$구매유형별조건 = "";
	}


?>

<script>
var v_baesong = '<?=$배송비?>';
var v_usd = '<?=$매매기준환율?>';
</script>

<input type='hidden' id='it_cnt' name='it_cnt' value='<?=count($id_list)?>' />
<input type='hidden' name='it_id' value='<?=$it_id?>' />


<?
if($it_id) {

	// it_cnt 공구 총개수
	//$i 가 문제..  $id_list = explode(',',$it_id);  이걸 쿼리로 해결가능하면...

	for($i=0; $i < count($id_list); $i++) {
		$it_id = 공백문자삭제($id_list[$i]);

		$sql_product_rp = str_replace('#공동구매조건#', " AND		IT.gp_id = '$it_id' ", $sql_product);
		
		
		/* 상품정보 */
		$it_sql = " SELECT		T.gp_id,
													T.ca_id,
													T.ca_id2,
													T.ca_id3,
													T.event_yn,
													T.gp_name,
													T.gp_site,
													T.gp_img,
													T.gp_explan,
													T.gp_objective_price,
													T.gp_have_qty,
													T.gp_buy_min_qty,
													T.gp_buy_max_qty,
													T.gp_charge,
													T.gp_duty,
													T.gp_use,
													T.gp_order,
													T.gp_stock,
													T.gp_time,
													T.gp_update_time,
													T.gp_price,
													T.gp_price_org,
													T.gp_card_price,
													T.gp_price_type,
													T.gp_metal_type,
													T.gp_metal_don,
													T.gp_metal_etc_price,
													T.gp_sc_method,
													T.gp_sc_price,
													T.it_type,
													T.gp_type1,
													T.gp_type2,
													T.gp_type3,
													T.gp_type4,
													T.gp_type5,
													T.gp_type6,
													T.real_jaego AS jaego,
													CASE
														WHEN	T.ca_id LIKE 'CT%' || T.ca_id = 'GP'	THEN
															CASE
																WHEN	T.gp_price_type = 'Y'	THEN	/*실시간스팟시세*/
																	CEIL(T.gp_realprice / 100) * 100
																WHEN	T.gp_price_type = 'N'	THEN	/*고정가달러환율*/
																	CEIL(T.gp_fixprice / 100) * 100
																WHEN	T.gp_price_type = 'W'	THEN	/*원화적용*/
																	T.gp_price
																ELSE
																	0
															END
														ELSE
															/* 딜러업체 상품 */
															CASE
																WHEN	T.gp_price_type = 'Y'	THEN	/*실시간스팟시세*/
																	CEIL(T.gp_realprice / 100) * 100
																WHEN	T.gp_price_type = 'N'	THEN	/*고정가달러환율*/
																	CEIL(IFNULL(T.po_cash_price,T.gp_price) / 100) * 100
																WHEN	T.gp_price_type = 'W'	THEN	/*원화적용*/
																	T.gp_price
																ELSE
																	0
															END
													END po_cash_price,
													T.USD,
													CT.ca_name,
													CT.ca_use,
													CT.ca_include_head,
													CT.ca_include_tail,
													CT.ca_cert_use,
													CT.ca_adult_use,
													SD.solds_itid
									FROM		$sql_product_rp

													LEFT JOIN v_gpinfo_solds SD ON (SD.gpcode = '$gpcode' AND SD.solds_itid = T.gp_id)

													LEFT JOIN g5_shop_category CT ON (CT.ca_id = T.ca_id)
		";
		$result = sql_query($it_sql);

		if($mode == 'jhw') echo $it_sql;

		$it = mysql_fetch_array($result);


		/*빠른배송상품 주문하기를 카테고리가 CT가 아닌 상품을 주문하려고한다면 종료*/
		if($gpcode == 'QUICK' && substr($it[ca_id],0,2) != 'CT') {
			echo "<script>
				alert('해당 상품을 진행했던 공동구매는 마감되었습니다');
				location.href='/';
			</script>";
			exit;
		}

		/* 배송비 셋팅 */
		$배송비 = ($배송비 >= 0) ? $배송비 : $it[gp_sc_price];


		/* 상품에 대한 신청수량 */
		$clay_sql = "	SELECT	IFNULL(SUM(it_qty),0) AS total_qty
									FROM		clay_order
									WHERE		it_id = '$it_id'
									$구매유형별조건
									AND			stats NOT IN ('99')		/*취소제외*/
		";
		$t = mysql_fetch_array(sql_query($clay_sql));


		if(substr($it[ca_id],0,2) != 'CT') {
			$현재공구총신청수량 = $t[total_qty];
		} else {
			$현재공구총신청수량 = 0;
		}

		$현재공구총신청수량조건 = ($t[total_qty]) ? $t[total_qty] : 1;

		/* 신청수량에 따른 볼륨프라이싱 가격 */
		if($공구정보[volprice_yn] == 'Y'){
			$price_sql = "	SELECT	PO.	gp_id,
															PO.	po_num,
															PO.	po_sqty,	/*최소신청수량*/
															PO.	po_eqty,	/*최대신청수량*/
															PO.	po_cash_price,
															PO.	po_card_price,
															PO.	po_add_price,
															PO.	po_jaego	/*단품상품을 위한 재고정보 기입*/
											FROM		g5_shop_group_purchase_option PO
											WHERE		PO.gp_id = '$it_id'
											AND			PO.po_sqty <= '$현재공구총신청수량조건'
											AND			PO.po_eqty >= '$현재공구총신청수량조건'
			";
			$price = mysql_fetch_array(sql_query($price_sql));

			if($mode == 'jhw') echo "<br>".$price_sql;

			$po_cash_price = $it[po_cash_price];
// 			$po_cash_price = getExchangeRate($price[po_cash_price],$it_id);
		}	/* 고정가격, 미리 원화설정된 금액 */
		else {
			$po_cash_price = $it[po_cash_price];
		}


		$예상달러금액 = CEIL(($po_cash_price / ($it[USD] * 1.06 * 1.1)) * 100) / 100;

		/* 코투 보유분이 우선순위(설정되어있는게 우선적으로 적용) */
		//재고컬럼은 jaego로 일괄통일
		//$상품재고 = ($it[gp_have_qty] > 0) ? $it[gp_have_qty]*1 : $it[jaego]*1;
		$상품재고 = $it[jaego]*1;
		$신청가능수량 = $상품재고 - $현재공구총신청수량;
		$신청가능수량 = ($신청가능수량 > 0) ? $신청가능수량 : 0;

		/* 품절여부는 딜러업체품절상품코드에 포함되거나 예상남은수량이 0개일때 품절처리 */
		$품절여부 = ( $it_id == $it[solds_itid] ) ? true : false;
		$style = ($품절여부) ? "style='display:none;'":"";
		?>
		<div id='div_<?=$it_id?>'>
		<?
		$최대신청가능수량 = $it[gp_buy_max_qty];

		if($is_admin != 'super') $수량검사함수 = " onchange='chk_max_qty()' ";


		/* 재고가 있는거에 대해서만 이미지 로딩 */
// 		if($신청가능수량 > 0) {
			/* http 가 들어간건 다이렉트로, 아닌건 get_it_thumb함수로 */
			if( strstr($it[gp_img],'http')) {
				$image = "<img src='$it[gp_img]' width=260 />";
			}
			else {
	 			$image = get_it_thumbnail1($it[gp_img], 260, 260, '', 1);
			}

			/* 자사URL이미지로 잡혀있는거는 리사이징 */
			if( strstr($it[gp_img],'http://coinstoday.co.kr') ) {
				$path = str_replace("http://coinstoday.co.kr","",$it[gp_img]);
				$image = "<img src='/image.php/$it[gp_id].jpg?width=260&height=260&image=$path' alt='$it[gp_id]'>";
			}
// 		}
		?>
		<div style='position:relative; text-align:center;'>
			<?=$image?>
			<?
				if(strlen($it[gp_site]) > 10) {
			?>
			<br><input type='button' onclick="window.open('<?=$it[gp_site]?>','_blank')" value='원문보기' />
			<?
				}
			?>
		</div>
		<div style='padding:5px; font-size:1.2em; font-weight:bolder;text-align:center;' class='itname<?=$i?>'>
			<?=$it[gp_name]?><br>
			<?
			if($품절여부) {
				echo "<div style='padding:5px; font-size:1.5em; font-weight:bolder; color:red; text-align:center;'>위 상품은 딜러업체의 품절로 추가신청이 불가능합니다</div>";
			}
			?>
		</div>

		<?
		if($공구정보[volprice_yn] == 'Y') {
			## /* 볼륨프라이싱 */
			$vp_sql = "	SELECT	DL.it_id,
													DL.gpcode,
													IFNULL(CL.sum_qty,0) AS sum_qty,
													GP.gp_name,
													GP.gp_img,
													GPO.po_num,
													GPO.po_sqty,			/*최소신청수량*/
													GPO.po_eqty,			/*최대신청수량*/
													GPO.po_cash_price,
													GPO.po_card_price,
													CEIL((( GPO.po_cash_price * FD.USD * (1+ (GP.gp_charge + GP.gp_duty)/100) ) * 1.1) /100) *100 AS volprice
									FROM		(	SELECT	'$gpcode' AS gpcode,
																		'$it_id' AS it_id
														FROM		DUAL
													) AS DL
													LEFT JOIN (	SELECT	CL.gpcode,
																							CL.it_id,					/*주문상품코드*/
																							SUM(CL.it_qty) AS sum_qty
																			FROM		clay_order CL
																							LEFT JOIN gp_info GI ON (GI.gpcode = CL.gpcode)
																			WHERE		CL.gpcode = '$gpcode'
																			AND			CL.stats NOT IN ('75','85','90','99')
																			GROUP BY 	CL.gpcode, CL.it_id
													) CL ON (CL.it_id = DL.it_id)
													LEFT JOIN g5_shop_group_purchase GP ON (GP.gp_id = DL.it_id)
													LEFT JOIN g5_shop_group_purchase_option GPO ON (GPO.gp_id = GP.gp_id)
													,(SELECT	*
														FROM		flow_price
														ORDER BY reg_date DESC
														LIMIT 1
													) FD
									WHERE		1=1
									AND			DL.gpcode	= '$gpcode'
									AND			DL.it_id = '$it_id'
									ORDER BY 	CL.it_id ASC, GPO.po_num ASC
			";
			$vp_result = sql_query($vp_sql);

			if($mode == 'jhw') echo "<br>".$vp_sql;

			$vp_cnt = 0;
			$vp_maxcnt = mysql_num_rows($vp_result);


			$합계수량 =	$총주문금액 = $총주문금액_달러 = 0;


			$vpmaxcnt = mysql_num_rows($vp_result);



			while($vp = mysql_fetch_array($vp_result)) {
				/* 신청수량이 하나도 없을경우, 예상볼륨가는 첫번째값 */
				if( (($vp[sum_qty]*1) == 0 && $vp_cnt == 0) || $vp_maxcnt == ($vp_cnt+1) ) $po_cash_price = $vp[volprice];

				$vp_cnt++;

				if($이전it_id != $vp[it_id]) {
					echo "<div class='div_volprice' style='width:300px; margin:0px auto;'><table class='hover_skyblue' style='width:300px;'>";
		// 		echo ($vp_cnt == 1) ? $html : "<tr><td colspan='2'>공구누적수량: $합계수량 개<br>예상총액: $".$총주문금액_달러." ( {$총주문금액}원 )</td></tr></table></div>".$html;
					echo "<tr><th>수량</th><th>구매가</th></tr>";
				}

				/* 현재 볼륨가에 해당할경우 혹은 레코드가 총 1개일경우 */
				if( ($vp[po_sqty]*1 <= $vp[sum_qty]*1 && $vp[po_eqty]*1 >= $vp[sum_qty]*1) || $vpmaxcnt == 1 ) {
					$po_cash_price = $vp[volprice];

					$bgcolor = "style='background-color:#fff8c7;'";
					$합계수량 = number_format($vp[sum_qty]);


					$총주문금액 = $po_cash_price * $vp[sum_qty];
					$총주문금액 = ($총주문금액 > 0) ? number_format($총주문금액) : 0;

					$총주문금액_달러 = $vp[po_cash_price] * $vp[sum_qty];
					$총주문금액_달러 = ($총주문금액_달러 > 0) ? number_format($총주문금액_달러,2) : 0;

				} else {
					$bgcolor = "";
				}
				?>
				<tr <?=$bgcolor?>>
					<td title='볼륨수량'><?=number_format($vp[po_sqty])?> ~ <?=number_format($vp[po_eqty])?></td>
					<td title='볼륨가'>$<?=number_format($vp[po_cash_price],2)?> (<?=number_format($vp[volprice])?>원)</td>
				</tr>
				<?
				if($vp_cnt == $vp_maxcnt) {

					echo "<tr><td colspan='2'>
									<font color='red' style='font-size:1.2em; font-weight:bolder;'>
										딜러업체 예상보유수량 : ".number_format($상품재고)."개<br>
										($it[gp_update_time] 기준정보)<br><br>
										현재 ".number_format($합계수량)."개 공구신청중</font><br>
										예상총액: $".$총주문금액_달러." ( ${총주문금액}원 )<br>
								</td></tr></table></div>";
				}

				$이전it_id = $vp[it_id];
			}//while end

		}//if($공구정보[volprice_yn] == 'Y') {

		/* <!-- 신청자 --> */
		if($공구정보[list_view] == 'Y') {
			echo "<div class='div_volprice' style='width:300px; margin:0px auto;'><table align='center' style='border-top:0px;'>";
			echo "<tr><td colspan='3'><b>신청자현황</b></td></tr>";
			echo "<tr><td>순번</td><td>신청자</td><td>신청수량</td></tr>";

			$신청자_SQL = "	SELECT	CO.number,
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
											WHERE		gpcode = '$공구정보[gpcode]'
											AND			it_id = '$it_id'
											AND			stats NOT IN (99)
											ORDER BY od_id ASC
			";
			$신청자결과 = sql_query($신청자_SQL);

			$no = 0;
			while($신청자목록 = mysql_fetch_array($신청자결과)) {
				echo "<tr><td>$no</td><td>".$신청자목록[clay_id]."</td><td>$신청자목록[it_qty] EA</td></tr>";
				$no++;
			}

			echo "</table></div>";
		}

		switch($공구정보[sell_title]) {
			case 'S01':
				$가격타이틀 = '공구가';
				break;
			case 'S02':
				$가격타이틀 = '분양가';
				break;
		}

		//!$품절여부 개선해야됨..
		if(1) {
		?>
			<div <?=$style?>>
			<div style='padding:5px; font-size:1.5em; font-weight:bolder; color:blue; text-align:center;'>
				<?=($공구정보[volprice_yn] == 'Y') ? "<font color='red' style='font-size:0.7em; display:block; margin-bottom:10px;'>※ 위 상품은 공구마감시 최종신청수량에 따른<br>최종볼륨가로 안내SMS가 발송됩니다</font>".'예상볼륨가' : $가격타이틀 ?> : <?=display_price($po_cash_price)?>
				 <?=($공구정보[volprice_yn] == 'Y') ? "" : "<br><font color=red>".$가격타이틀."달러환산=>$".$예상달러금액."</font>"?>
				 <input type='hidden' class='it_price<?=$i?>' name='it_price[<?=$i?>]' value='<?=$po_cash_price?>' /><br>
				신청가능수량 : <?=$신청가능수량?>개<input type='hidden' id='gp_have_qty<?=$i?>' name='gp_have_qty[<?=$i?>]' value='<?=$신청가능수량?>' /><br>
				<span style='display:none;'>최대신청가능수량 : <?=$최대신청가능수량?>개<input type='hidden' id='gp_buy_max_qty<?=$i?>' name='gp_buy_max_qty[<?=$i?>]' value='<?=$최대신청가능수량?>' /></span>
			</div>
			<div style='padding:5px; font-weight:bolder; text-align:center;'>※  가격이 0원으로 나온다면 새로고침 해주세요</div>
			<div style='font-size:1.5em; color:red; font-weight:bolder; text-align:center;'>
				※ 주문수량 : <input type='text' class='itemqty it_qty<?=$i?>' name='it_qty[<?=$i?>]' value='0' style='width:60px; text-align:right;' <?=$수량검사함수?> />개
				<input type='button' value='+' onclick="order_add('it_qty<?=$i?>',1,'plus')" style='padding:0px; border-radius:0px; width:20px; text-align:center;' />
				<input type='button' value='-' onclick="order_add('it_qty<?=$i?>',1,'minus')" style='padding:0px; border-radius:0px; width:20px; text-align:center;' />
			</div>
			</div>

			<br><br><br>
		</div>
		<?
		}

		$모든상품남은수량 += $신청가능수량;

	}//for end  상품배열 for문
}// if($it_id) end

// 	$배송비 = ($배송비 >= 0) ? $배송비 : '5000';
//echo <div style='padding:5px; font-size:1.2em; font-weight:bolder; color:blue; text-align:center;'>※신한은행 110-408-552944<br>(예금주: 코인즈투데이,박민우)</div>

if( $모든상품남은수량 > 0 || $is_admin == 'super') {
?>

<table class='clayOrderTB' style='margin-top:50px;' align='center'>
	<tr>
		<td colspan='2'>
			<div class='clayOrderTitle'>주문서작성</div>
		</td>
	</tr>
	<tr>
		<th colspan='2'>
			<div class='orderinfo' style='margin-bottom:10px;'>※ 위 상품의 주문수량을 입력해주세요</div>
			<div style='font-size:1.2em; font-weight:bolder; color:blue;'>총 주문금액 : <span id='txt_price'>0원</span></div>
			<div class='paytype' style='margin-bottom:10px; font-size:1.3em; font-weight:bold; color:red;'></div>
			<div style='width:100%; font-size:1.3em; font-weight:bold; color:red; margin-top:15px;'>
				<b>※ 환율변동 또는 상황에 따라 가격변동이 있을수 있습니다.<br>
				* 항목은 필수항목이니 꼭 입력해주시기 바랍니다!
				</b>
			</div>
			<div style='font-size:1.3em; font-weight:bold; color:#eb5800; margin-top:15px;'>
				<b>※ 공동구매중인 상품은 해외업체의 상품으로 국내배송까지 2주에서 4주정도 소요될수 있습니다. 딜러업체 재고상태에 따라 배송기간이 늘어날 수 있습니다.
				</b>
			</div>
		</th>
	</tr>
		<?

		$이전정보SQL = "SELECT	CI.*
										FROM		clay_order_info CI
										WHERE		CI.hphone = '$member[mb_hp]'
										AND     CI.clay_id = '$member[mb_nick]'
										ORDER BY CI.od_id DESC
		";
		$mb = mysql_fetch_array(sql_query($이전정보SQL));

		$hp = explode('-',$mb[hphone]);

		?>
	<tr>
		<th><font color=red>*닉네임</font></th>
		<td>
			<input class='clayOrderTB text' type='text' name='clay_id' title='닉네임' value='<?=$mb[clay_id]?>' />
			<input type='hidden' name='mb_id' title='계정' value='<?=$member[mb_id]?>' />
		</td>
	</tr>
	<tr>
		<th><font color=red>*주문자 성함</font></th>
		<td><input class='clayOrderTB text' type='text' name='name' title='주문자성함' value='<?=$mb[name]?>' /></td>
	</tr>
	<tr>
		<th><font color=red>*연락처</font></th>
		<td>
			<input class='clayOrderTB hphone' type='tel' name='hp1' title='연락처(첫번째번호)' maxlength="3" value='<?=($hp[0])?$hp[0]:'010'?>' />-
			<input class='clayOrderTB hphone' type='tel' name='hp2' title='연락처(가운데번호)' maxlength="4" value='<?=$hp[1]?>' />-
			<input class='clayOrderTB hphone' type='tel' name='hp3' title='연락처(마지막번호)' maxlength="4" value='<?=$hp[2]?>' />
		</td>
	</tr>
	<tr>
		<th>현금영수증</th>
		<td>
			<div style='border:1px #d7d7d7 solid; padding:5px; width:200px;'>
				<p>
					<input type='radio' name='cash_receipt_yn' value='N' checked onclick="showHide_cashreceipt('N')">신청안함
					<input type='radio' name='cash_receipt_yn' value='Y' onclick="showHide_cashreceipt('Y')">신청
				</p>

				<div id='cash_receipt_info' style='display:none;'>
					<input type='radio' name='cash_receipt_type' value='C01' checked onclick="choiceOption_cashReceipt('C01')">개인소득공제
					<input type='radio' name='cash_receipt_type' value='C02' onclick="choiceOption_cashReceipt('C02')">사업자지출증빙

					<div id='cash_hp'>휴대폰번호
						<input class='clayOrderTB hphone' type='tel' name='cash_hp1' value='010' style='width:30px;'>-
						<input class='clayOrderTB hphone' type='tel' name='cash_hp2' value=''>-
						<input class='clayOrderTB hphone' type='tel' name='cash_hp3' value=''>
					</div>
					<div id='cash_bno' style='display:none;'>
						사업자번호
						<input class='clayOrderTB bno' type='tel' name='cash_bno1' value='' style='width:30px;'>-
						<input class='clayOrderTB bno' type='tel' name='cash_bno2' value='' style='width:20px;'>-
						<input class='clayOrderTB bno' type='tel' name='cash_bno3' value='' style='width:50px;'></div>
				</div>
			</div>
		</td>
	</tr>
	<tr>
		<th>배송비</th>
		<td>
			<select id='delivery_type'  name='delivery_type' onchange="setRcvAddress(); calc_orderinfo();">
				<option value='D01'>주문시 결제</option>
				<option value='D02'>수령후 지불</option>
				<option value='D03'>방문수령</option>
				<option value='D04'>통합배송요청</option>
			</select>
		</td>
	</tr>
	<tr>
		<th valign=''>
			<div class='divAddrTitle' style='padding-left:0px;'><font color=red>*받으실곳</font></div>
			<div class='divAddrTitle'>지번주소</div>
			<div class='divAddrTitle'>도로명주소</div>
			<div class='divAddrTitle'>상세주소</div>
		</th>
		<td>
			<input class='clayOrderTB zip'type='text' class='zip' id='zip' name='zip' title='우편번호' value='<?=$mb[zip]?>' /> <input class='clayOrderTB btn' type='button' value='우편번호검색' onclick='searchPostcode()' style='background-color:black; color:white; font-size:1.1em;' /><br>
			<input class='clayOrderTB text'type='text' class='address' id='addr1' name='addr1' value='<?=$mb[addr1]?>' /><br>
			<input class='clayOrderTB text'type='text' class='address' id='addr1_2' name='addr1_2' value='<?=$mb[addr1_2]?>' /><br>
			<input class='clayOrderTB text'type='text' id='addr2' name='addr2' title='상세주소' value='<?=$mb[addr2]?>' />
		</td>
	</tr>
	<tr>
		<td colspan='2' style='height:20px; font-size:12px; text-align:left; font-weight:bolder;'>
			※ 지번주소와 도로명주소 중 편한 주소로 입력하세요
			<span id='guide' name='guide'></span><br>
		</td>
	</tr>

	<tr>
		<th><font color=red>*결제방법</font></th>
		<td>
			<select id='paytype' name='paytype' onchange="calc_orderinfo();">
				<option value='P01'>무통장입금</option>
				<option value='P02'>카드결제(+3.5%수수료)</option>
				<option value='P03'>외화결제(달러,위안..)</option>
				<option value='P04'>귀금속결제(금화,은화..)</option>
			</select>
		</td>
	</tr>
	<tr>
		<th><font color=red>*입금자성함</font></th>
		<td><input class='clayOrderTB text'type='text' name='receipt_name' title='입금자성함' value='<?=$mb[receipt_name]?>' /></td>
	</tr>
	<tr>
		<th>요청사항</th>
		<td><textarea name='memo'  style='width:200px; height:70px;'></textarea></td>
	</tr>
	<tr>
		<td colspan='2'  style='text-align:center; height:80px;'>
			<input type='submit' value='주문신청' style='font-size:1.5em; font-weight:bolder; background-color:black; color:white; width:100px; height:40px; border:2px solid black; border-radius:10px;' />
		</td>
	</tr>
</table>
<?
}
else
{
?>
	<div style='text-align:center; font-size:1.2em; font-weight:bold; margin-top:20px;'>
	금일 신청가능한 수량이 모두 소진되었습니다.<br>
	이용해주셔서 감사합니다<br><br>
	-코인투데이-
	</div>
<?
}
?>
	</form>
</div>

<div>
	<?php
		include_once(G5_MSHOP_PATH . '/tail.php');
	?>
</div>