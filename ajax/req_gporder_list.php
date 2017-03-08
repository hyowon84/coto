<?php
/* 
 * AJAX 위시리스트 JSON 데이터 추출 
 * */
include_once('./_common.php');
include_once('../lib/coinstoday.lib.php');



/* 대리주문 로그인시 대리주문자의 주문정보를 위한 세션정보 교체 */
if(get_session("mem_order_se")){
	$member[mb_id] = get_session("mem_order_se");
	$member = sql_fetch("select * from {$g5['member_table']} where mb_id='".$member[mb_id]."'");
}


// 코드값 검색
if($ct_type)$ct_type_que = " and ct_type='".$ct_type."' ";
else $ct_type_que = " and ct_type != '' ";


/* 필요한 항목이 있을경우 아래에서 갖다쓰기바람 */

// 			O.od_cancel_price,
// 			O.od_receipt_point,
// 			O.od_refund_price,
	
// 			C.ct_history,		/*상태변경시 기록하는 로그*/
// 			C.ct_payment,		/* 결제방식?  B, ...  */
// 			C.ct_usd_price,	/*상품 가격(달러)*/
// 			C.ct_point,			/**/
// 			C.cp_price,			/* 주문당시 환율금액 */
// 			C.ct_point_use,	/* 포인트 사용가능여부? */
// 			C.ct_stock_use,	/* ? */
// 			C.ct_option,		/* 선택옵션정보? */
	
	
// 			C.ct_notax,
// 			C.io_id,
// 			C.io_type,
// 			C.io_price,
	
// 			C.ct_ip,
// 			C.ct_send_cost,
// 			C.ct_direct,
// 			C.ct_select,
// 			C.ct_type,	/*공동구매 카테 구분*/
// 			C.ct_time_code,	/*날짜 암호화*/
// 			C.auc_status,	/*경매구분*/
// 			C.pur_status,	/*구매대행 여부*/
// 			C.ct_card_status,	/*카드여부*/
// 			C.ct_card_price,	/*카드금액*/
// 			C.ct_gp_status,	/*공동구매 확인 여부*/
// 			C.buy_status,	/*판매가능여부*/
// 			C.ct_buy_qty,	/*실판매수량*/
// 			C.total_amount_code,	/*공동구매 건별 코드*/
// 			C.ex_status,	/*교환여부*/
// 			C.ex_reason,	/*사유선택*/
// 			C.ex_content,	/*사유내용*/
// 			C.ex_addr,	/*교환주소*/
	
// 			/* 주문자 정보 */
// 			O.od_name,	/*주문자명*/
// 			O.od_email,	/*주문자 이메일*/
// 			O.od_tel,	/*주문자 연락처*/
// 			O.od_hp,		/*주문자 휴대폰*/
// 			O.od_zip1,
// 			O.od_zip2,
// 			O.od_addr1,
// 			O.od_addr2,
// 			O.od_addr3,
// 			O.od_addr_jibeon,
	
// 			O.od_deposit_name,
// 			O.od_b_name,
// 			O.od_b_tel,
// 			O.od_b_hp,
// 			O.od_b_zip1,
// 			O.od_b_zip2,
// 			O.od_b_addr1,
// 			O.od_b_addr2,
// 			O.od_b_addr3,
// 			O.od_b_addr_jibeon,
// 			O.od_memo,
// 			O.od_cart_count,
// 			O.od_cart_price,
// 			O.od_cart_coupon,
// 			O.od_cart_usd_price,
// 			O.od_exchange_rate,

// 			O.od_bank_account,
// 			O.od_receipt_time,
// 			O.od_coupon,
	
// 			O.od_shop_memo,
// 			O.od_mod_history,
// 			O.od_status,
// 			O.od_hope_date,
// 			O.od_settle_case,
// 			O.od_mobile,
// 			O.od_tno,
// 			O.od_app_no,
// 			O.od_escrow,
// 			O.od_tax_flag,
// 			O.od_tax_mny,
// 			O.od_vat_mny,
// 			O.od_free_mny,
// 			O.od_delivery_company,
// 			O.od_invoice,
// 			O.od_invoice_time,
// 			O.od_cash,
// 			O.od_cash_no,
// 			O.od_cash_info,
// 			O.od_time,
// 			O.od_pwd,
// 			O.od_ip,
// 			O.buy_status,	/*구매 종류*/
// 			O.od_bank,	/*입금은행*/
// 			O.od_remit,	/*예금주*/
// 			O.od_tax,	/*현금영수증여부*/
// 			O.tax_status,	/*영수증 발행 종류*/
// 			O.od_tax_hp,	/*영수증 발행 휴대폰*/
// 			O.od_last_date,	/*입금대기기간*/
// 			O.combine_deli_code,	/*통합배송 코드*/
// 			O.combine_deli_date,	/*통합배송 날짜*/
// 			O.combine_deli_status,	/*통합배송 여부*/
// 			O.gp_code,
// 			O.od_wearing_cnt,
// 			O.od_tax_state


$카테고리조건 = ($ca_id != 'all') ? " AND	SUBSTR(GP.ca_id,1,4)	= '$ca_id' " : "";
$키워드조건 = ($keyword) ? " AND (CT.it_name LIKE '%$keyword%' OR  CA.ca_name  LIKE '%$keyword%' )" : "";
$날짜조건 = ($sdate) ? " AND	CT.ct_time > date_format('$sdate','%Y-%m-%d') " : '';
$날짜조건 .= ($edate) ? " AND	CT.ct_time < date_format('$edate','%Y-%m-%d') " : '';

$json = array();
$json['data'] = array();
$json['data_total'] = array();

/* 공동구매 */
if($mode == 'order') {

	$sql = "	SELECT	CT.ct_gubun,	/*N:투데이스토어, P:공동구매, (A:사용안하는중)*/
									CASE
										WHEN	CT.ct_gubun = 'N' THEN
											IF(IT.it_img1,CONCAT('".G5_DATA_URL."','/item/',IT.it_img1),NULL)
										WHEN	CT.ct_gubun = 'P' THEN
											GP.gp_img
										ELSE
											NULL
									END AS img_url,
									CT.ct_id,				/* 개별 주문ID */
									CT.mb_id,				/* 주문한 회원ID */
									CT.ct_status,		/*주문진행상태*/
									CT.ct_time,			/* 주문일자 */
									CT.it_id,				/* 상품ID */
									CT.it_name,			/* 상품명 */
									CT.od_id,				/* 주문번호(그룹ID) */						
									CT.ct_price,			/* 상품가격(달러) 원화로 변환 */
									CT.ct_qty,			/* 신청수량 */
									CT.ct_price*CT.ct_qty AS calc_price,	/* 상품수량*상품금액 = 합계*/
									GP.ca_id,
									IFNULL(CA.ca_id,'0000') AS ca_id,											/* 브랜드ID(CA_ID) */
									IFNULL(CA.ca_name,'OTHER DEALER') AS brand_name	/* 브랜드명 */
				FROM		g5_shop_cart CT 
								LEFT OUTER JOIN g5_shop_group_purchase GP ON ( GP.gp_id = CT.it_id )
								LEFT OUTER JOIN g5_shop_item IT ON (IT.it_id = CT.it_id)
								LEFT OUTER JOIN g5_shop_category CA ON (CA.ca_id = SUBSTR(GP.ca_id,1,4))
				WHERE	1=1
				AND		CT.ct_status IN ('쇼핑','집계중','주문')
				AND		CT.mb_id='".$member[mb_id]."'
				AND		CT.ct_gubun = 'P'
				$카테고리조건
				$키워드조건
				$날짜조건
				$페이징조건
	";
	
	$result = sql_query($sql);
	
	//g5_shop_wish_table
	
	
	
	$cart_count = mysql_num_rows($result);
	
	$i = 0;
	while($row = mysql_fetch_assoc($result)) {
		$row[gp_img] = urlencode($row[img_url]);
		$row[it_name] = str_replace('"',"\"",$row[it_name]);
		$row[it_name] = str_replace("\t","",$row[it_name]);
		$row[it_name] = str_replace("\r\n","",$row[it_name]);
		array_push($json['data'], $row);
	}
}
/* 브랜드별 주문합계금액 */
else if($mode == 'brand') {
	
	$sql = "	SELECT		IFNULL(CA.ca_id,'0000') AS ca_id,				/* 브랜드ID */
										IFNULL(CA.ca_name,'OTHER DEALER') AS ca_name,	/* 브랜드명 */
										SUM(CT.ct_price * CT.ct_qty) AS brand_price,	/* 브랜드별 상품합계금액 */
										SUM(CT.ct_point) AS use_point,							/* 사용포인트 */
										3000 AS baesongbi												/* CT.ct_send_cost는.. 공구신청시 0원으로 되있음.. 추후 별도 수정필요 */
					FROM		g5_shop_cart CT
									LEFT OUTER JOIN g5_shop_order OD ON (OD.od_id = CT.od_id)
									LEFT OUTER JOIN g5_shop_group_purchase GP ON (GP.gp_id = CT.it_id)
									LEFT OUTER JOIN g5_shop_category CA ON (CA.ca_id = SUBSTR(GP.ca_id,1,4))
						WHERE		1=1
						AND		CT.ct_status = '쇼핑'
						AND		CT.mb_id='".$member[mb_id]."'
						GROUP BY CA.ca_id
	";
	$result = sql_query($sql);
	$json['data_total_rows'] = mysql_num_rows($result);
	
	while($row = mysql_fetch_array($result)) {
		$total_price += ($row[brand_price] - $row[use_point] + $row[baesongbi]);
		
		$row[brand_price] = number_format($row[brand_price]);
		$row[use_point] = number_format($row[use_point]);
		$row[baesongbi] = number_format($row[baesongbi]);
		array_push($json['data_total'], $row);
	}
	
	//CA_ID가 NULL인 경우 2050(OTHER DEALER)랑 병합.
	if($json['data_total']['0000'][total_price]) {
		$json['data_total']['2050'][total_price] += $json['data_total']['0000'][total_price]; 
		$json['data_total']['2050'][use_point] += $json['data_total']['0000'][use_point];
	// 	$json['data_total']['2050'][baesongbi] += $json['data_total']['0000'][baesongbi];
		$json['data_total']['0000'] = null;
		$json['total_count'] = $cart_count;
	}
	
	$json[total_price] = number_format($total_price);
}


$json_data = json_encode_unicode($json);

echo $json_data;
exit;






/*cart_gp.php 의 데이터가져오는 소스.. 참고해서 옵션부분 넣어야됨 */
$tot_point = 0;
$tot_sell_price = 0;

// 쇼핑중이 장바구니 업데이트
updatePurchaseAllCartid($member['mb_id'],$s_cart_id);

$gpCartList = array();

// $s_cart_id 로 현재 장바구니 자료 쿼리
$sql = " select		a.ct_id,
							a.it_id,
							a.it_name,
							a.od_id,
							a.ct_price,
							a.ct_point,
							a.ct_qty,
							a.ct_status,
							a.ct_send_cost,
							a.ct_gp_status,
							a.ct_type,
							a.buy_status,
							a.ct_op_option,
							a.total_amount_code, 
							b.ca_id,
							b.ca_id2,
							b.ca_id3
		   from {$g5['g5_shop_cart_table']} a left join {$g5['g5_shop_group_purchase_table']} b on ( a.it_id = b.gp_id )
		  where a.od_id = '$s_cart_id' and (`ct_status`='쇼핑') and a.mb_id='".$member[mb_id]."' and a.ct_gubun = 'P' $ct_type_que ";
if($default['de_cart_keep_term']) {
	$ctime = date('Y-m-d', G5_SERVER_TIME - ($default['de_cart_keep_term'] * 86400));
	$sql .= " and substring(a.ct_time, 1, 10) >= '$ctime' ";
}
$sql .= " group by a.it_id ";
$sql .= " order by a.ct_id desc ";

$result = sql_query($sql);

$totalPurchasePrice = $it_send_cost = 0;

$k=0;

for ($i=0; $row=mysql_fetch_array($result); $i++)
{

	//신청중이면서 코드가 틀리면 삭제
	//if(isPurchaseCodeCheckCartDelete($row['ct_id']))continue;

	// 합계금액 계산
	$sql = " select 	SUM(IF(io_type = 1, (io_price * ct_qty), ((ct_price + io_price) * ct_qty))) as price,
							SUM(ct_point * (ct_qty-ct_buy_qty)) as point,
							SUM(ct_qty-ct_buy_qty) as qty 
				from		{$g5['g5_shop_cart_table']}
				where		it_id = '{$row['it_id']}'
				and			total_amount_code = '$row[total_amount_code]'
				and			mb_id='".$member[mb_id]."' ";


	$sum = sql_fetch($sql);



	if ($i==0) { // 계속쇼핑
		$continue_ca_id = $row['ca_id'];
	}

	
	
	// 상태가 상품구입일경우 가격 갱신
	if(isPurchaseDeleteCheck($row['ct_id'])){
		$sql = " select SUM(IF(io_type = 1, (io_price * ct_qty), ((ct_price + io_price) * ct_qty))) as price,
						SUM(ct_point * (ct_qty-ct_buy_qty)) as point,
						SUM(ct_qty-ct_buy_qty) as qty 
					from {$g5['g5_shop_cart_table']}
					where it_id = '{$row['it_id']}' and total_amount_code = '$row[total_amount_code]' ";
		$sum2 = sql_fetch($sql);


		// 상품정보 갱신
		UpdateGroupPurchasePrice($row['it_id']);
		$new_it_price = getGroupPurchaseQtyBasicPrice($row['it_id'],$sum2['qty']);
		// 가격이 다를시 업데이트
		//if($row['it_price']!=$new_it_price){

			$row['it_price'] = $new_it_price;
			
			$ct_usd_price = getGroupPurchaseQtyBasicUSD($row['it_id'],$sum2['qty']);

			$sql = "update $g5[g5_shop_cart_table] set ct_price = '".$new_it_price."',ct_usd_price = '".$ct_usd_price."',cp_price = '".$_SESSION[unit_kor_duty]."' where it_id = '".$row['it_id']."' and total_amount_code = '$row[total_amount_code]' and  ct_status = '쇼핑' ";
			sql_query($sql);
		//}
	}
	

	$a1 = '<a href="'.G5_URL.'/shop/grouppurchase.php?gp_id='.$row['it_id'].'">';
	$a2 = '</a>';

	$image = get_gp_image($row['it_id'], 70, 70);

	//옵션 상품
	$op_arr = explode("|", $row[ct_op_option]);
	$op_price = 0;
	for($b = 0; $b < count($op_arr); $b++){
		if($op_arr[$b]){
			$op_row = sql_fetch("select * from {$g5['g5_shop_option2_table']} where it_id='".$row[it_id]."' and con='".$op_arr[$b]."' ");
			$op_price = $op_price + $op_row[price];
			$op_name .= $op_row[con].",";
		}
	}
	$op_name = substr($op_name, 0, strlen($op_name)-1);
	if($op_name){
		$op_name = $op_name;
	}else{
		$op_name = "";
	}

	// 가격 및 포인트
	$sell_price = $row['ct_price'] * $sum['qty'] + $op_price;
	$point      = $sum['point'];
?>

<tr>
	<td class="td_chk right">
	<input type="hidden" name="ct_id[<?php echo $k; ?>]"    value="<?php echo $row['ct_id']; ?>">
	<input type="hidden" name="it_id[<?php echo $k; ?>]"  value="<?php echo $row['it_id']; ?>">
	<input type="hidden" name="gp_code[<?php echo $k; ?>]"  value="<?php echo $row[total_amount_code]; ?>">
	<input type="hidden" name="it_name[<?php echo $k; ?>]"  value="<?php echo strip_tags($row['it_name']); ?>">
	<input type="checkbox" name="ct_chk[]" class="ct_chk_inac" value="<?php echo $k; ?>" id="ct_chk_<?php echo $k; ?>"<?php if(!isPurchaseDeleteCheck($row['ct_id']))echo " disabled=true";?>>
	</td>
	<td class="td_gpcode right"><?php echo printGrouppurCode($row['total_amount_code'])?></td>
	<td class="sod_img right"><?php echo $image; ?></td>
	<td class="right">
		<div><?php echo $a1.strip_tags($row['it_name']).$a2; ?></div>
		<div style="margin:7px 0 0 0;"><?php $op_name;?></div>
	</td>
	<td class="td_num right"><?php echo number_format($sum['qty']);?> 개</td>
	<td class="td_numbig right"><?php echo number_format($row['ct_price'])?> 원</span></td>
	<td class="td_numbig"><?php echo number_format($sell_price)?> 원</span></td>

</tr>

<?php
	$k++;

	$subCaId = substr($row['ca_id'],0,4);
	$gpCartList[$subCaId]['total_price'] += $sell_price;
	$totalPurchasePrice += $sell_price; //총가격

} // for 끝

if ($i == 0) {
	echo '<tr><td colspan="8" class="empty_table">장바구니에 담긴 상품이 없습니다.</td></tr>';
}


?>