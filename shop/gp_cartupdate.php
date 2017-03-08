<?php
include_once('./_common.php');

// print_r2($_POST); exit;

if(get_session("mem_order_se")){
	$member[mb_id] = get_session("mem_order_se");
	$member = sql_fetch("select * from {$g5['member_table']} where mb_id='".$member[mb_id]."'");
}


if($default[de_guest_cart_use] == 0){
	if($member[mb_id] == ""){
		alert("로그인 후 이용 가능합니다.");
	}
}

$orderform_url = "orderform_gp.php";

if($_POST[prog] != "wish" && $act != "multi"){
	$_POST[ca_id] = substr($_POST[ca_id], 0, 4);
	$ca_id = substr($_POST[ca_id], 0, 4);
}

// cart id 설정
set_cart_id($sw_direct);

if($sw_direct)
	$tmp_cart_id = get_session('ss_cart_direct');
else
	$tmp_cart_id = get_session('ss_cart_id');

// 브라우저에서 쿠키를 허용하지 않은 경우라고 볼 수 있음.
if (!$tmp_cart_id)
{
	alert('더 이상 작업을 진행할 수 없습니다.\\n\\n브라우저의 쿠키 허용을 사용하지 않음으로 설정한것 같습니다.\\n\\n브라우저의 인터넷 옵션에서 쿠키 허용을 사용으로 설정해 주십시오.\\n\\n그래도 진행이 되지 않는다면 쇼핑몰 운영자에게 문의 바랍니다.');
}


// 레벨(권한)이 상품구입 권한보다 작다면 상품을 구입할 수 없음.
if ($member['mb_level'] < $default['de_level_sell'])
{
	alert('상품을 구입할 수 있는 권한이 없습니다.');
}






if($act == "buy")
{
	if(!count($_POST['ct_chk']))
		alert("주문하실 상품을 하나이상 선택해 주십시오.");

	for($i=0; $i<count($_POST['ct_chk']); $i++) {
		$k = $_POST['ct_chk'][$i];

		if($gp_id[$k])
		{
			$sql = " update {$g5['g5_shop_cart_table']} set ct_select = '1' where it_id = '".$gp_id[$k]."' and od_id = '$tmp_cart_id' and ct_status='쇼핑'";
			sql_query($sql);
		}
	}

	if ($is_member) // 회원인 경우
		goto_url(G5_SHOP_URL.'/'.$orderform_url."?ca_id=".$_POST[ca_id]."&ct_all_gubun=".$_POST[ct_all_gubun]);
	else
		goto_url(G5_BBS_URL.'/login.php?url='.urlencode(G5_SHOP_URL.'/'.$orderform_url."?ca_id=".$_POST[ca_id]));
}

else if ($act == "seldelete") // 선택삭제
{
	if(!count($_POST['ct_chk']))
		alert("삭제하실 상품을 하나이상 선택해 주십시오.");
	

	$msg = "";
	for($i=0; $i<count($_POST['ct_chk']); $i++) {
		$ct_id = $_POST['ct_chk'][$i];	/*카트번호*/
		
		$sel_sql = "	SELECT	CT.*,
											CP.gc_state	
							FROM		g5_shop_cart	CT
											LEFT JOIN g5_group_cnt_pay CP ON (CP.gubun_code = 20)
							WHERE	ct_id = '$ct_id'							
		";
		$cart = sql_fetch_array(sql_query($sel_sql));
		
		
		
		/* 삭제가능여부 체크,  */
		if( isPurchaseDeleteCheck2($ct_id) || $cart[gc_state] == 'S' ){
			$upd_sql = "	UPDATE		g5_shop_cart		SET
															ct_status = '신청취소'
									WHERE		ct_id = '$ct_id'
									AND			mb_id = '$member[mb_id]'
			";
			sql_query($upd_sql);
			/*if($_POST['ct_id'][$k]) {
				$sql = " delete from {$g5['g5_shop_cart_table']} where it_id = '".$_POST['it_id'][$k]."' and mb_id = '$member[mb_id]' and total_amount_code = '".$_POST['gp_code'][$k]."' ";

			   sql_query($sql);
			}*/
		}else{
			if($msg)$msg.="\\n";
			$msg .= $cart['it_name']."는 집계또는 완료중으로 취소가 불가능합니다.";
		}
	}

	if($msg)alert($msg);
}

else // 장바구니에 담기
{
	
	if (!$_POST['gp_id']) 
		alert('구매 하실 상품 정보가 넘어오지 않았습니다.');

	if($sw_direct){
		$ct_card_status = 'y';
		$ct_card_price = $_POST[it_card_price];
		
	}else{
		
	}

	$gp_id = $_POST['gp_id'];

	if ($_POST['ct_qty'] < 1)
		alert('수량은 1 이상 입력해 주십시오.');

	// 상품정보
	$sql = " select * from {$g5['g5_shop_group_purchase_table']} where gp_id = '$gp_id' ";
	$it = sql_fetch($sql);
	if(!$it['gp_id'])
		alert('상품정보가 존재하지 않습니다.');

	// 공동구매 구매가능 여부 확인
	isPurchaseBuyCheck($it['ca_id']);

	/* ??? */
	$ct_select = 1;

	$gp_code = getPurchaseBuyCode($it['ca_id']);
	
  	if(!$gp_code && !$개발자)alert("공동구매 코드값이 존재하지 않습니다.");


	if($gp_code){
		$etc_sql = ",total_amount_code = '$gp_code' ";
	}

	$po_qty = sql_fetch("select SUM(ct_qty) as total_qty from {$g5['g5_shop_cart_table']} where it_id='".$it['gp_id']."' and ct_gubun='P' and ct_status='쇼핑' ");

	$total_qty = $po_qty[total_qty] + $_POST['ct_qty'];

	
	/* 볼륨프라이싱에 해당하는 가격정보 로딩  */
	$poqty_sql = "	SELECT	*
								FROM		{$g5['g5_shop_group_purchase_option_table']}
								WHERE	gp_id='".$it['gp_id']."'
								AND		po_sqty <= '".$total_qty."'
								AND		po_eqty >= '".$total_qty."'
	";
	$po_qty_op = sql_fetch($poqty_sql);

	/* 달러 -> 원화 & 수수료,관세 금액 계산 */
	$it_price = getExchangeRate($po_qty_op[po_cash_price],$it[gp_id]);

	// 공동구매 총액계산
	//isPurchaseBuyTotalAmountCheck($it['ca_id'],$it_price*$_POST['ct_qty']);


	// 장바구니에 Insert
	$comma = '';
	$sql = " INSERT INTO {$g5['g5_shop_cart_table']}	SET 
						od_id	= '$tmp_cart_id',
						ct_gubun	= 'P',
						mb_id	=	'{$member['mb_id']}',
						it_id	=	'{$it['gp_id']}',
						it_name	=	'".addslashes(strip_tags($it['gp_name']))."',
						ct_status	=	'쇼핑',
						
						ct_price	=	'$it_price',	/* 달러 -> 원화 & 수수료,관세 금액 계산 */
						ct_usd_price	=	'".$po_qty_op[po_cash_price]."',
						
						cp_price	=	'".$_SESSION[unit_kor_duty]."',	/* 현재 달러환율 */
						ct_point	=	'0',
						ct_point_use	=	'0',
						ct_stock_use	=	'0',
						ct_option	=	'$io_value',
						ct_qty	=	'".$_POST['ct_qty']."',
						ct_time	=	'".G5_TIME_YMDHIS."',
						ct_ip	=	'$REMOTE_ADDR',
						ct_send_cost	=	'0',
						ct_direct	=	'$sw_direct',
						ct_select	=	'$ct_select',
						ct_payment	=	'{$_POST['ct_payment']}',
						ct_type	=	'$ca_id',
						ct_time_code	=	".strtotime(G5_TIME_YMDHIS).",
						ct_card_status	=	'$ct_card_status',
						ct_card_price	=	'$ct_card_price',
						ct_op_option = '$op_name'
						$etc_sql
	";

	sql_query($sql);

	// 코드 및 상품에 따른 그룹화
	if($gp_code) purchaseItemGorupUpdate($gp_code,$it['gp_id']);

}
// 바로 구매일 경우
if ($sw_direct)
{
	if ($is_member)
	{
		goto_url(G5_SHOP_URL."/".$orderform_url."?sw_direct=$sw_direct&buy_kind=$buy_kind&ca_id=".$_POST[ca_id]);
	}
	else
	{
		goto_url(G5_BBS_URL."/login.php?url=".urlencode(G5_SHOP_URL."/".$orderform_url."?sw_direct=$sw_direct&buy_kind=$buy_kind&ca_id=".$_POST[ca_id]));
	}
}
else
{
	//공구신청하기 후 마이페이지 주문조회 페이지
	//공동구매 신청정보 페이지
	if(G5_IS_MOBILE) {
		//주문조회페이지
		goto_url(G5_SHOP_URL.'/orderinquiry.php');	
	} else {
		goto_url(G5_SHOP_URL.'/cart_gp.php?ct_type='.$ca_id);
	}
	

}
?>
