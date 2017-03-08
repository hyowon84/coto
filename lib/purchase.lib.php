<?php
// 공동구매용 ....


function getPurchaseGroupTaxSelect($name, $selected="", $event="")
{

	$tax_state = array("N"=>"처리중","Y"=>"완료");

    $str = '<select id="'.$name.'" name="'.$name.'" '.$event.'>';

	foreach($tax_state as $key=>$vars){
        $str .= '<option value="'.$key.'"';
        if ($key == $selected) $str .= ' selected';
        $str .= '>'.$vars.'</option>';
    }
    $str .= '</select>';
    return $str;
}


// 공동구매 코드 출력
function printGrouppurCode($purchaseCode)
{
	$str = "공동구매 신청대기";
	if($purchaseCode)$str = $purchaseCode;

	return $str;
}

// 공동구매 분류명
function getPurchaseCategoryName($ca_id)
{
	$msg = "";
	switch($ca_id){
		case "2010":
			$msg = "APMAX";
			break;
		case "2020":
			$msg = "Gaines Ville Coins";
			break;
		case "2030":
			$msg = "MCM";
			break;
		case "2040":
			$msg = "Scottsdale Silver";
			break;
		case "2050":
			$msg = "Other Dealer";
			break;
	}

	return $msg;
}

//공동구매 진행상태 표시
function getPurchaseStateText($purchaseCode)
{
	global $g5;

	$row = sql_fetch("select gc_state from {$g5['g5_total_amount_table']} where type_code='".$purchaseCode."'");

	$str = "";
	switch($row['gc_state']){
		case "S" :
			$str = "<font color='blue'>신청중</font>";
			break; 
		case "W" :
			$str = "<font color='green'>집계중</font>";
			break; 
		case "E" :
			$str = "<font color='red'>주문완료</font>";
			break; 
	}

	return $str;
}

function getPurchaseStateSelect($id, $name, $selected='', $event='')
{
	$purchaseState = array("S"=>"신청중","W"=>"집계중","E"=>"주문완료");

    $str = "<select id=\"$id\" name=\"$name\" $event>\n";
	$i=0;
    foreach($purchaseState as $key=>$vars) {
        if ($i == 0) $str .= "<option value=\"\">진행상태</option>";
        $str .= option_selected($key, $selected,$vars);
		$i++;
    }
    $str .= "</select>";
    return $str;
}

// 공동구매 삭제 가능여부
function isPurchaseDeleteCheck($ct_id)
{
	global $g5, $member;

	$is_del = true;
	$row = sql_fetch("select total_amount_code, ct_status from {$g5['g5_shop_cart_table']} where ct_id = '$ct_id' and mb_id = '".$member[mb_id]."'");
	if($row['total_amount_code']){

		$row2 = sql_fetch("select gc_state from {$g5['g5_total_amount_table']} where type_code = '".$row['total_amount_code']."'");
		if($row2['gc_state']!="S" || $row['ct_status']!="쇼핑")$is_del = false;
	}

	return $is_del;
}

// 공동구매 삭제 가능여부
function isPurchaseDeleteCheck2($ct_id)
{
	global $g5, $member;

	$is_del = true;
	
	$sql = "SELECT	CT.total_amount_code,
								CT.ct_status,
								TA.gc_state
				FROM		g5_shop_cart CT
								LEFT JOIN 	g5_total_amount TA ON (TA.type_code = CT.total_amount_code)
				WHERE	CT.mb_id = '".$member[mb_id]."'
				AND		CT.ct_id	=	'$ct_id'
	";
	$row = sql_fetch($sql);
	
	if($row['gc_state']!="S" || $row['ct_status']!="쇼핑") {
		$is_del = false;
	}

	return $is_del;
}

//공동구매 진행상태 사용안함
function getPurchaseBuyState($ca_id)
{
	global $g5,$개발자;
	$sub_ca_id  = substr($ca_id,0,4);

	$is_sell = false;
	
	$sql = "SELECT		gc_state 
				FROM		{$g5['g5_group_cnt_pay_table']}
				WHERE		gubun_code = '".$sub_ca_id."'
				AND		fr_date <= '".G5_TIME_YMD."'
				AND		to_date >= '".G5_TIME_YMD."'
				AND		gc_state ='S'
	";
	$row = sql_fetch($sql);
	if($row['gc_state'] || $개발자) $is_sell = true;

	$msg = getPurchaseCategoryName($sub_ca_id);
	if(!$is_sell)alert("죄송합니다.\\n[ ".$msg." ]는 현재 구매가 불가능합니다.");
}

//공동구매 가능 여부 체크
function isPurchaseBuyCheck($ca_id)
{
	global $g5,$member,$개발자;
	$sub_ca_id  = substr($ca_id,0,4);

	$is_sell = false;

	if($is_admin == 'super' || eregi('admin',$member[mb_id]) ) {
		/* 전체 */
		$sql = "	select gc_state 
					from {$g5['g5_group_cnt_pay_table']} 
					where gubun_code = '20' 
					and fr_date <= '".G5_TIME_YMD."' 
					and to_date >= '".G5_TIME_YMD."' 
					and gc_state ='S'
					";
		$all = sql_fetch($sql);
	}
	/* 개별 */
	$sql = "	select gc_state 
				from {$g5['g5_group_cnt_pay_table']} 
				where gubun_code = '".$sub_ca_id."' 
				and fr_date <= '".G5_TIME_YMD."' 
				and to_date >= '".G5_TIME_YMD."' 
				and gc_state ='S'
				";
	$row = sql_fetch($sql);
	
	if($row['gc_state'] || $all['gc_state'] || $개발자) $is_sell = true;

	$msg = getPurchaseCategoryName($sub_ca_id);
	if(!$is_sell)alert("죄송합니다.\\n[ ".$msg." ]는 현재 구매가 불가능합니다.");
}

// 공동구매 정보
function getPurchaseCodeInfo($ca_id)
{
	global $g5;
	$sub_ca_id  = substr($ca_id,0,4);

	$sql = "	select * 
				from {$g5['g5_group_cnt_pay_table']} 
				where 	gubun_code = '".$sub_ca_id."' 
				and fr_date <= '".G5_TIME_YMD."' 
				and to_date >= '".G5_TIME_YMD."' 
				/*and gc_state ='S'*/
				";
	$row = sql_fetch();

	return $row;
}

// 공동구매 코드
function getPurchaseBuyCode($ca_id)
{
	global $g5;
	$sub_ca_id  = substr($ca_id,0,4);


	$gp_code = "";

	$row = sql_fetch("select group_code from {$g5['g5_group_cnt_pay_table']} where gubun_code = '".$sub_ca_id."' and fr_date <= '".G5_TIME_YMD."' and to_date >= '".G5_TIME_YMD."' and gc_state ='S'");
	
	if($row['group_code']) {
		$gp_code = $row['group_code'];
	}
	else {
		/* 전체 */
		$sql = "	select group_code 
						from {$g5['g5_group_cnt_pay_table']} 
						where gubun_code = '20' 
						and fr_date <= '".G5_TIME_YMD."' 
						and to_date >= '".G5_TIME_YMD."' 
						and gc_state ='S'
					";
		$all = sql_fetch($sql);
		$gp_code = $all['group_code'];
	}
	return $gp_code;
}

// 공동구매 현재까지 총액
function getCartPurchaseBuyTotalPrice($gp_code)
{
	global $g5;

	$total_price = 0;
	$row = sql_fetch("select sum(ct_price * (ct_qty - ct_buy_qty)) as total_price from {$g5['g5_shop_cart_table']} where total_amount_code = '$gp_code' and ct_status in ('입금대기','결제완료', '해외배송대기', '해외배송중', '상품준비중', '배송대기', '배송중', '배송완료')");
	$total_price = $row['total_price'];
	
	return $total_price;
}

// 공동구매 총액 체크
function isPurchaseBuyTotalAmountCheck($ca_id,$total_price)
{
	global $g5;

	$sub_ca_id  = substr($ca_id,0,4);

	$row = getPurchaseCodeInfo($sub_ca_id);

	$nCartTotalPrice = getCartPurchaseBuyTotalPrice($row['group_code']);

	$nCartTotalPrice += $total_price;

	$msg = getPurchaseCategoryName($sub_ca_id);

	if($row['cnt_pay']<$nCartTotalPrice)alert("죄송합니다.\\n[ ".$msg." ]는 공동구매 총액이 넘어가 구매가 불가능합니다.");
	
}

// 장바구니 지난부분 업데이트
function updatePurchaseAllCartid($mb_id,$s_cart_id)
{
	global $g5;

	sql_query("update {$g5['g5_shop_cart_table']} set od_id = '$s_cart_id' where mb_id = '$mb_id' and od_id != '$s_cart_id' and ct_status='쇼핑'");
}

// 장바구니 신청중이면서 일치하지 않는 코드들 삭제
function isPurchaseCodeCheckCartDelete($ct_id)
{
	global $g5;

	$row = sql_fetch("select a.total_amount_code, b.ca_id from {$g5['g5_shop_cart_table']}  a left join {$g5['g5_shop_group_purchase_table']} b on ( a.it_id = b.gp_id ) where a.ct_id = '$ct_id'");
	$gp_code = getPurchaseBuyCode($row['ca_id']);

	$is_check = false;
	if($row['total_amount_code']!=$gp_code || $row['total_amount_code']==""){
		sql_query("delete from {$g5['g5_shop_cart_table']} where ct_id = '$ct_id'");
		$is_check=true;
	}

	return $is_check;	
}


// 종료후에 구매신청하신분 코드 삽입
function updateReCartPurchaseCode($purchaseCode,$fr_date,$to_date,$ca_id,$prevPurchaseCode)
{
	global $g5;

	$fr_time = date("Y-m-d H:i:s",strtotime($fr_date." 00:00:00"));
	$to_time = date("Y-m-d H:i:s",strtotime($to_date." 11:59:59"));
	
	/* 공동구매 코드가 있는 경우에는 기존주문내역, 카운팅 정보 업데이트, 없을경우에는 실행하면 안됨 */
	if($purchaseCode) {
		
		if($prevPurchaseCode) {
			$sel_sql = "	SELECT	A.ct_id
								FROM		g5_shop_cart	A
												LEFT OUTER JOIN g5_shop_group_purchase B ON (B.gp_id = A.it_id)
								WHERE	A.ct_status = '쇼핑'
								AND		A.ct_gubun = 'P'
								AND		B.ca_id LIKE '%$ca_id%'
								AND		(	A.ct_time between '$fr_time'		AND	'$to_time'	)	/* 신청시간 */
								AND		(	A.total_amount_code =''	OR	A.total_amount_code ='$prevPurchaseCode'	)	/* 입력안된것과 변경되기전 공구코드 대상 */
			";
			$result = sql_query($sel_sql);
			
			while($row = sql_fetch_array($result)) {
				$list_ct_id .= $row[ct_id].",";
			}
			$list_ct_id = substr($list_ct_id, 0, strlen($list_ct_id)-1);
			
			//존재할경우에만 변경
			if(strlen($list_ct_id) > 4) {
				//현재의 공구코드가 ' 또는 변경되기전 공구코드에 대해서만 		
				$upd_sql = "	UPDATE		g5_shop_cart		SET
																 total_amount_code = '".$purchaseCode."'
										WHERE		ct_id IN (	$list_ct_id )
				";
				sql_query($upd_sql);
			}
		}
		
		
		$sel_sql = "	SELECT	DISTINCT
											it_id
							FROM		g5_shop_cart
							WHERE	total_amount_code = '".$purchaseCode."'
							AND		ct_status = '쇼핑'
							AND		ct_gubun = 'P'
							AND		ct_type = '".$ca_id."'
							ORDER	BY	ct_time	ASC
		";
		$result = sql_query($sel_sql);
		
		for($i=0;$row=sql_fetch_array($result);$i++) {
			/* 상품별 공동구매 카운팅 정보 업데이트 */
			purchaseItemGorupUpdate($purchaseCode,$row['it_id']);
		}
	}
}


// 신청합계 그룹화로 인해 구매코드 및 상품아이디 값에 따른 그룹
function purchaseItemGorupUpdate($purchaseCode,$gp_id)
{
	global $g5;

	$row = sql_fetch("select count(gp_code) as cnt from {$g5['g5_shop_group_purchase_group_table']} where gp_code = '".$purchaseCode."' and gp_id = '".$gp_id."'");
	
	//첫 공구신청 주문일경우 신규정보생성
	if(!$row['cnt']){
		$ins_sql = "INSERT		INTO	g5_shop_group_purchase_group	SET
															gp_code = '".$purchaseCode."',
															gp_id = '".$gp_id."',
															gp_datetime = '".G5_TIME_YMDHIS."' ";
		sql_query($ins_sql);
	}
	

	// 해당 코드에 따른 구매 수및 가격 체크
	$sel_sql = "	SELECT	COUNT(ct_id) AS cart_cnt,
										SUM(ct_price * ct_qty) AS total_price,
										SUM(ct_qty) AS total_qty
						FROM		g5_shop_cart
						WHERE	it_id = '$gp_id'
						AND		total_amount_code = '$purchaseCode'
						AND		ct_status NOT LIKE '%취소%'
	"; 
	$row2 = sql_fetch($sel_sql);
	
	/* 값이 존재하면 수량갱신 */
	if($row2['cart_cnt']>0){
		$upd_sql = "	UPDATE		g5_shop_group_purchase_group	SET
														gp_cart_cnt = '".$row2['cart_cnt']."',
														gp_cart_price = '".$row2['total_price']."',
														gp_cart_qty = '".$row2['total_qty']."'
								WHERE		gp_code = '".$purchaseCode."'
								AND			gp_id = '".$gp_id."'	";
		sql_query($upd_sql);
		return false;
	}else{
		//값이 없으면 삭제?
		sql_query("delete from g5_shop_group_purchase_group	where gp_code = '".$purchaseCode."' and gp_id = '".$gp_id."'");

		return true;
	}
}

// 신청 품절 및 입고 미입고 정산
function updateGroupPurhchaseCartQtyCal($gp_code,$gp_id,$gp_soldout, $gp_wearing)
{
	global $g5;

	$n_soldout = $gp_soldout;
	$n_wearing = $gp_wearing;

	// 품절
	$result = sql_query("select * from {$g5['g5_shop_cart_table']} where total_amount_code = '".$gp_code."' and it_id = '".$gp_id."' order by ct_time desc");
	for($i=0;$row=sql_fetch_array($result);$i++){

		if($n_soldout>0){
			if($n_soldout>=$row['ct_qty']){
				sql_query("update {$g5['g5_shop_cart_table']} set ct_gp_soldout = '".$row['ct_qty']."' where ct_id ='$row[ct_id]'");
				$n_soldout = $n_soldout - $row['ct_qty'];
			}else{
				sql_query("update {$g5['g5_shop_cart_table']} set ct_gp_soldout = '".$n_soldout."' where ct_id ='$row[ct_id]'");
				$n_soldout = 0;
			}
		}else{
			sql_query("update {$g5['g5_shop_cart_table']} set ct_gp_soldout = 0 where ct_id ='$row[ct_id]'");
		}
	}

	//입고 미입고
	$result = sql_query("select * from {$g5['g5_shop_cart_table']} where total_amount_code = '".$gp_code."' and it_id = '".$gp_id."' order by ct_time desc");
	for($i=0;$row=sql_fetch_array($result);$i++){

		if($row['ct_qty']>$row['ct_gp_soldout']){
			$tmp_wearing = $row['ct_qty'] - $row['ct_gp_soldout'];

			if($n_wearing >= $tmp_wearing && $n_wearing>0){
				sql_query("update {$g5['g5_shop_cart_table']} set ct_wearing_cnt = '".$tmp_wearing."', ct_notstocked_cnt = 0 where ct_id ='$row[ct_id]'");
				$n_wearing = $n_wearing - $tmp_wearing;
			}elseif($n_wearing>0 && $tmp_wearing > $n_wearing){
				sql_query("update {$g5['g5_shop_cart_table']} set ct_wearing_cnt = '".$n_wearing."', ct_notstocked_cnt = '".($tmp_wearing-$n_wearing)."' where ct_id ='$row[ct_id]'");
				$n_wearing = 0;
			}elseif($n_wearing==0){
				sql_query("update {$g5['g5_shop_cart_table']} set ct_wearing_cnt = 0, ct_notstocked_cnt = '".$tmp_wearing."' where ct_id ='$row[ct_id]'");
			}
		}else{
			sql_query("update {$g5['g5_shop_cart_table']} set ct_wearing_cnt = 0, ct_notstocked_cnt = 0 where ct_id ='$row[ct_id]'");
		}

		//주문금액 정산
		if($row['ct_id'])ReCalOrderPriceUpdate($row['od_id']);
	}
}


function get_grouppurchase_info($od_id)
{
	global $g5;

    // 주문정보
    $sql = " select * from {$g5['g5_shop_order_table']} where od_id = '$od_id' ";
    $od = sql_fetch($sql);

    if(!$od['od_id'])
        return false;

    $info = array();

    // 장바구니 주문금액정보
    $sql = " select SUM(IF(io_type = 1, (io_price * (ct_qty-ct_gp_soldout)), ((ct_price + io_price) * (ct_qty-ct_gp_soldout)))) as price, sum(ct_wearing_cnt) as wearing_cnt,
                    SUM(cp_price) as coupon,
                    SUM( IF( ct_notax = 0, ( IF(io_type = 1, (io_price * (ct_qty-ct_gp_soldout)), ( (ct_price + io_price) * (ct_qty-ct_gp_soldout)) ) - cp_price ), 0 ) ) as tax_mny,
                    SUM( IF( ct_notax = 1, ( IF(io_type = 1, (io_price * (ct_qty-ct_gp_soldout)), ( (ct_price + io_price) * (ct_qty-ct_gp_soldout)) ) - cp_price ), 0 ) ) as free_mny
                from {$g5['g5_shop_cart_table']}
                where od_id = '$od_id'
                  and ct_status IN ( '입금대기', '결제완료', '상품준비중', '배송대기', '배송중', '배송완료' ) ";
    $sum = sql_fetch($sql);

    $cart_price = $sum['price'];
    $cart_coupon = $sum['coupon'];
	$cart_wearing_cnt = $sum['wearing_cnt'];

    // 배송비
    if($cart_price>0)$send_cost = $od['od_send_cost'];
	else $send_cost = 0;

    $od_coupon = $od_send_coupon = 0;

  

    // 과세, 비과세 금액정보
    $tax_mny = $sum['tax_mny'];
    $free_mny = $sum['free_mny'];

    if($od['od_tax_flag']) {
        $tot_tax_mny = ( $tax_mny + $send_cost + $od['od_send_cost2'] )
                       - ( $od_coupon + $od_send_coupon + $od['od_receipt_point'] );
        if($tot_tax_mny < 0) {
            $free_mny += $tot_tax_mny;
            $tot_tax_mny = 0;
        }
    } else {
        $tot_tax_mny = ( $tax_mny + $free_mny + $send_cost + $od['od_send_cost2'] )
                       - ( $od_coupon + $od_send_coupon + $od['od_receipt_point'] );
        $free_mny = 0;
    }

    $od_tax_mny = round($tot_tax_mny / 1.1);
    $od_vat_mny = $tot_tax_mny - $od_tax_mny;
    $od_free_mny = $free_mny;

    // 장바구니 취소금액 정보
    $sql = " select SUM(IF(io_type = 1, (io_price * (ct_qty-ct_gp_soldout)), ((ct_price + io_price) * (ct_qty-ct_gp_soldout)))) as price
                from {$g5['g5_shop_cart_table']}
                where od_id = '$od_id'
                  and ct_status IN ( '취소', '반품', '품절' ) ";
    $sum = sql_fetch($sql);
    $cancel_price = $sum['price'];

    // 미수금액
    $od_misu = ( $cart_price + $send_cost + $od['od_send_cost2'] )
               - ( $cart_coupon + $od_coupon + $od_send_coupon )
               - ( $od['od_receipt_price'] + $od['od_receipt_point'] - $od['od_refund_price'] );

	if($od_misu < 0){
		$od_misu = 0;
	}else{
		$od_misu = $od_misu;
	}

    // 장바구니상품금액
    $od_cart_price = $cart_price + $cancel_price;

    // 결과처리
    $info['od_cart_price']      = $od_cart_price;
    $info['od_send_cost']       = $send_cost;
    $info['od_coupon']          = $od_coupon;
    $info['od_send_coupon']     = $od_send_coupon;
    $info['od_cart_coupon']     = $cart_coupon;
    $info['od_tax_mny']         = $od_tax_mny;
    $info['od_vat_mny']         = $od_vat_mny;
    $info['od_free_mny']        = $od_free_mny;
    $info['od_cancel_price']    = $cancel_price;
    $info['od_misu']            = $od_misu;
    $info['od_wearing_cnt']            = $cart_wearing_cnt;
	

    return $info;
}


// 주문서 금액 재계산
function ReCalOrderPriceUpdate($od_id)
{
	global $g5;

	// 미수금 등의 정보
	$info = get_grouppurchase_info($od_id);

	if($info){

		$sql = " update {$g5['g5_shop_order_table']}
					set od_cart_price   = '{$info['od_cart_price']}',
						od_cart_coupon  = '{$info['od_cart_coupon']}',
						od_coupon       = '{$info['od_coupon']}',
						od_send_coupon  = '{$info['od_send_coupon']}',
						od_cancel_price = '{$info['od_cancel_price']}',
						od_send_cost    = '{$info['od_send_cost']}',
						od_misu         = '{$info['od_misu']}',
						od_tax_mny      = '{$info['od_tax_mny']}',
						od_vat_mny      = '{$info['od_vat_mny']}',
						od_free_mny     = '{$info['od_free_mny']}',
						od_wearing_cnt = '{$info['od_wearing_cnt']}'";

		$sql .= " where od_id = '$od_id' ";

		sql_query($sql);
	}

}


// 주문서 생성
function makeGroupPurchaseOrder($gp_code)
{
	global $g5, $purchaseSendCostm, $REMOTE_ADDR;

	$sql = "select mb_id from {$g5['g5_shop_cart_table']} where total_amount_code = '".$gp_code."' and ct_status='쇼핑' group by mb_id order by ct_time";
	$result = sql_query($sql);

	for($i=0;$ct=sql_fetch_array($result);$i++){

		$tmp_cart_id = get_uniqid();

	
		// 장바구니 코드 재업로드 및 상태변경
		sql_query("update {$g5['g5_shop_cart_table']} set od_id = '".$tmp_cart_id."', ct_status='입금대기' where ct_gubun = 'P' and total_amount_code = '".$gp_code."' and ct_status='쇼핑' and mb_id = '".$ct['mb_id']."'");


		// 포트폴리오 등록
		$gp_cart_res = sql_query("select * from {$g5['g5_shop_cart_table']} where od_id='$tmp_cart_id' and mb_id='".$ct[mb_id]."' and ct_gubun = 'P'");

		$all_it_name = "";
		
		for($j= 0; $gp_cart_row = sql_fetch_array($gp_cart_res); $j++){

			if($j==0)$all_it_name=$gp_cart_row['it_name'];

			$gp = sql_fetch("select * from {$g5['g5_shop_group_purchase_table']} where gp_name='".str_replace("'","''",$gp_cart_row[it_name])."'");
			
			$wr_5 = $gp_cart_row[ct_usd_price] * $gp_cart_row[ct_qty];
			$wr_6 = $gp_cart_row[ct_price] * $gp_cart_row[ct_qty];

			if(stristr($gp[gp_name], "gold")){
				$gp_metal_type = "GL";
			}else if(stristr($gp[gp_name], "silver")){
				$gp_metal_type = "SL";
			}else if(stristr($gp[gp_name], "platinum")){
				$gp_metal_type = "PT";
			}else if(stristr($gp[gp_name], "palladium")){
				$gp_metal_type = "PD";
			}else{
				$gp_metal_type = "ETC";
			}

			$Response = curl("http://www.apmex.com/product/32457");
			$gp_metal_don = getExplodeValue($Response,"<table class=\"table table-product-specs\">","</div>");
			$gp_metal_don = explodeTag("tr", $gp_metal_don);
			$gp_metal_don = explodeTag("td", $gp_metal_don[6]);
			$gp_metal_don = str_replace(" oz", "", str_replace(" troy", "", $gp_metal_don[0]));

			$gp_cart_row[ct_qty] = $gp_cart_row[ct_qty] - $gp_cart_row[ct_buy_qty];

			$sql = "
				insert into g5_write_portfolio set
				wr_subject='".str_replace("'","''",$gp_cart_row[it_name])."',
				wr_datetime='".date("Y-m-d H:i:s")."',
				mb_id='".$ct[mb_id]."',
				wr_last='".date("Y-m-d H:i:s")."',
				wr_ip='".$REMOTE_ADDR."',
				wr_1='".$gp[gp_img]."',
				wr_2='".$gp_metal_type."',
				wr_3='".$gp_cart_row[ct_qty]."',
				wr_4='".$gp_metal_don."',
				wr_5='".$wr_5."',
				wr_6='".$wr_6."',
				wr_7='P',
				img_width='170',
				img_height='170'
			";
			
			sql_query($sql);

			$wr_id = mysql_insert_id();

			sql_query("
			update g5_write_portfolio set
			wr_num='-".$wr_id."',
			wr_parent='".$wr_id."'
			where wr_id=".$wr_id."
			");
		}

		// 주문서 삽입

		$sum_sql = " select SUM(IF(io_type = 1, (io_price * (ct_qty-ct_gp_soldout)), ((ct_price + io_price) * (ct_qty-ct_gp_soldout)))) as price,
				COUNT(it_id) as cnt,
				SUM( IF( ct_notax = 0, ( IF(io_type = 1, (io_price * (ct_qty-ct_gp_soldout)), ( (ct_price + io_price) * (ct_qty-ct_gp_soldout)) ) - cp_price ), 0 ) ) as tax_mny,
				SUM( IF( ct_notax = 1, ( IF(io_type = 1, (io_price * (ct_qty-ct_gp_soldout)), ( (ct_price + io_price) * (ct_qty-ct_gp_soldout)) ) - cp_price ), 0 ) ) as free_mny
			from {$g5['g5_shop_cart_table']}
			where od_id = '$tmp_cart_id'
			  and ct_status IN ( '입금대기', '결제완료', '상품준비중', '배송대기', '배송중', '배송완료' ) ";
		$sum = sql_fetch($sum_sql);

		$cart_count1 = $sum[cnt];

		$tot_ct_price1 = $sum['price'];

		$od_send_cost1 = 3000; // 배송비

		$od_misu1 = $od_send_cost1 + $tot_ct_price1;


		$mb = get_member($ct['mb_id']);
		
		$gpa = sql_fetch("select * from {$g5['g5_shop_group_purchase_addr_table']} where mb_id = '".$ct['mb_id']."'");

		$od_tax = $gpa['gp_tax'];
		$od_tax_hp =  $gpa['gp_tax_number'];
		$tax_status = "";
		if($od_tax=="0")$tax_status = "휴대폰번호";
		elseif($od_tax=="1")$tax_status = "사업자번호";

		if($gpa['gp_sel_addr']=="new"){
			$od_b_name = $gpa['gp_name'];
			$od_b_tel = $gpa['gp_tel'];
			$od_b_hp = $gpa['gp_hp'];
			$od_b_zip1 = $gpa['gp_zip1'];
			$od_b_zip2 = $gpa['gp_zip2'];
			$od_b_addr1 = $gpa['gp_addr1'];
			$od_b_addr2 = $gpa['gp_addr2'];
			$od_b_addr3 = $gpa['gp_addr3'];
			$od_b_addr_jibeon = $gpa['gp_addr_jibeon'];
		}else{
			$od_b_name = $mb['mb_name'];
			$od_b_tel = $mb['mb_tel'];
			$od_b_hp = $mb['mb_hp'];
			$od_b_zip1 = $mb['mb_zip1'];
			$od_b_zip2 = $mb['mb_zip2'];
			$od_b_addr1 = $mb['mb_addr1'];
			$od_b_addr2 = $mb['mb_addr2'];
			$od_b_addr3 = $mb['mb_addr3'];
			$od_b_addr_jibeon = $mb['mb_addr_jibeon'];
		}


		$sql = " insert {$g5['g5_shop_order_table']}
				set od_id             = '".$tmp_cart_id."',
					mb_id             = '{$mb['mb_id']}',
					od_pwd            = '{$mb['mb_password']}',
					od_name           = '{$mb['mb_name']}',
					od_email          = '{$mb['mb_email']}',
					od_tel            = '{$mb['mb_tel']}',
					od_hp             = '{$mb['mb_hp']}',
					od_zip1           = '{$mb['mb_zip1']}',
					od_zip2           = '{$mb['mb_zip2']}',
					od_addr1          = '{$mb['mb_addr1']}',
					od_addr2          = '{$mb['mb_addr2']}',
					od_addr3          = '{$mb['mb_addr3']}',
					od_addr_jibeon    = '{$mb['mb_addr_jibeon']}',
					od_b_name         = '$od_b_name',
					od_b_tel          = '$od_b_tel',
					od_b_hp           = '$od_b_hp',
					od_b_zip1         = '$od_b_zip1',
					od_b_zip2         = '$od_b_zip2',
					od_b_addr1        = '$od_b_addr1',
					od_b_addr2        = '$od_b_addr2',
					od_b_addr3        = '$od_b_addr3',
					od_b_addr_jibeon  = '$od_b_addr_jibeon',
					od_deposit_name   = '{$mb['mb_name']}',
					od_memo           = '$od_memo',
					od_cart_count     = '$cart_count1',
					od_cart_price     = '$tot_ct_price1',
					od_cart_coupon    = '$tot_it_cp_price',
					od_cart_usd_price = '$od_cart_usd_price',
					od_exchange_rate  = '$od_exchange_rate',
					od_send_cost      = '$od_send_cost1',
					od_send_coupon    = '$tot_sc_cp_price',
					od_send_cost2     = '$od_send_cost2',
					od_coupon         = '$tot_od_cp_price',
					od_receipt_price  = '$od_receipt_price',
					od_receipt_point  = '$od_receipt_point',
					od_bank_account   = '신한은행 110-408-552944 (코인즈투데이,박민우)',
					od_receipt_time   = '$od_receipt_time',
					od_misu           = '$od_misu1',
					od_tno            = '$od_tno',
					od_app_no         = '$od_app_no',
					od_escrow         = '$od_escrow',
					od_tax_flag       = '{$default['de_tax_flag_use']}',
					od_tax_mny        = '$od_tax_mny1',
					od_vat_mny        = '$od_vat_mny1',
					od_free_mny       = '$od_free_mny',
					od_status         = '입금대기',
					od_shop_memo      = '',
					od_hope_date      = '$od_hope_date',
					od_time           = '".G5_TIME_YMDHIS."',
					od_ip             = '".$REMOTE_ADDR."',
					od_settle_case    = '무통장',
					buy_status		  = '$buy_kind',
					od_bank			  = '',
					od_tax			  = '".$od_tax."',
					tax_status = '".$tax_status."',
					od_tax_hp		  = '".$od_tax_hp."',
					gp_code = '".$gp_code."'
					";

			$result = sql_query($sql);


			// 알탐등록
			$sql = "INSERT INTO RTAM_REQUEST VALUES ('' ,'".$tmp_cart_id."', '".$mb['mb_id']."', '".G5_TIME_YMDHIS."',  '".str_replace("'","''",$all_it_name)."', '".$od_misu1."', '', '".$mb['mb_name']."', '".$od_misu1."', '0')";
			sql_query($sql);

		
	}
}
?>