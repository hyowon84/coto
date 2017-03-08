<?php
$sub_menu = '500300';
$sub_sub_menu = '3';

include_once('./_common.php');
include_once (G5_ADMIN_PATH.'/admin.head.php');
?>

<style>
.deputy_th { width:30%; padding:10px; }
.deputy_td { width:70%; padding:10px; }
</style>

<form action='<?=$PHP_SELF?>' method='post'>
<input type='hidden' name='mode' value='order'> 

<table align='center' style='width:800px;'>
	<tr>
		<td class='deputy_th' width='30%'>공구상품코드</th>
		<td class='deputy_td' width='70%'><input type='text' name='gp_id' value='<?=$gp_id?>' /></td>
	</tr>
	<tr>
		<td class='deputy_th'>신청자ID(이메일주소)</th>
		<td class='deputy_td'>
<textarea name='list_mb_id' style='height:100px;'><?=$list_mb_id?></textarea></td>
	</tr>
	<tr>
		<td class='deputy_th'>신청자 닉네임</th>
		<td class='deputy_td'>
<textarea name='list_mb_nick' style='height:100px;'><?=$list_mb_nick?></textarea></td>
	</tr>
	
	<tr>
		<td class='deputy_th'>신청수량</th>
		<td class='deputy_td'><input type='' name='ct_qty' value='<?=$ct_qty?>'></td>
	</tr>
	<tr>
		<td colspan='2' align='center' style='padding:10px;'><input type='submit' value='신청'></td>
	</tr>
</table>
</form>

<?

if($mode == 'order') {
	echo "<center style='margin-top:30px;'>";
	
	/* 추후 주문정보 db 생성시 입력할 od_id 설정 */
	/* $sw_direct 바로구매 값*/
	
	/*
	if($sw_direct)
		$tmp_cart_id = get_session('ss_cart_direct');
	else
		$tmp_cart_id = get_session('ss_cart_id');
	
	// 브라우저에서 쿠키를 허용하지 않은 경우라고 볼 수 있음.
	if (!$tmp_cart_id)
	{
		alert('더 이상 작업을 진행할 수 없습니다.\\n\\n브라우저의 쿠키 허용을 사용하지 않음으로 설정한것 같습니다.\\n\\n브라우저의 인터넷 옵션에서 쿠키 허용을 사용으로 설정해 주십시오.\\n\\n그래도 진행이 되지 않는다면 쇼핑몰 운영자에게 문의 바랍니다.');
	}
	*/
	
	$tmp_cart_id = get_uniqid();
	
	if (!$_POST['gp_id']) 
		alert('구매 하실 상품 정보가 넘어오지 않았습니다.');

	/* 바로구매로 넘어왔을경우 셋팅..인데 필요없어보임 추후 삭제  */
	if($sw_direct){
		$ct_card_status = 'y';
		$ct_card_price = $it_card_price;
	}

	$gp_id = $_POST['gp_id'];

	if ($_POST['ct_qty'] < 1)
		alert('수량은 1 이상 입력해 주십시오.');

	// 상품정보
	$sql = " select * from {$g5['g5_shop_group_purchase_table']} where gp_id = '$gp_id' ";
	$it = sql_fetch($sql);
	if(!$it['gp_id']) alert('상품정보가 존재하지 않습니다.');
	
	//브랜드 코드
	$ca_id = substr($it['ca_id'], 0, 4);

	// 공동구매 구매가능 여부 확인
	isPurchaseBuyCheck($it['ca_id']);

	$ct_select = 1;	//의미없는 값으로 보임...

	/* 공동구매 진행중 정보 가져오기 */
	$gp_code = getPurchaseBuyCode($it['ca_id']);
	
	if(!$gp_code && !$개발자) alert("공동구매 코드값이 존재하지 않습니다.");
	
	if($gp_code){
		$etc_sql = ",total_amount_code = '$gp_code' ";
	} else {
		echo '해당 상품은 공동구매 진행중이 아닙니다';
		exit;
	}

	//화면에서 입력한 회원ID 목록을 구함
	$arr_mb_id = explode(PHP_EOL, $_POST['list_mb_id']);		//이메일주소
	$arr_mb_nick = explode(PHP_EOL, $_POST['list_mb_nick']);	//닉네임
	
	$신청자목록 = array_merge($arr_mb_id,$arr_mb_nick);
	
	
	for($i = 0; $i < count($신청자목록); $i++) {
		//신청자 MB_ID 존재하지 않을경우 패스
		if($신청자목록[$i] == '' || !$신청자목록[$i]) continue;
		
		$신청정보 = '';
		$신청정보 = explode('/',$신청자목록[$i]);
		$신청자KEY = trim($신청정보[0]);
		$수량 = ($신청정보[1]) ? $신청정보[1] : $ct_qty;

		$mem_sql = "	SELECT	mb_id
									FROM		g5_member
									WHERE	mb_id = '$신청자KEY'
									OR			mb_nick = '$신청자KEY'
		";
		$회원정보 = sql_fetch($mem_sql);
		
		if(!$회원정보[mb_id]) {
			echo "$신청자KEY 회원은 존재 하지 않습니다 확인바랍니다<br><br>";
			continue;	
		}
		$신청자ID = $회원정보[mb_id];
		
	
		/* 현재까지 공동구매 요청 전체수량 가져오기 */
		$poqty_sql = "	SELECT	SUM(ct_qty) AS total_qty
									FROM		{$g5['g5_shop_cart_table']}
									WHERE	it_id='".$it['gp_id']."'
									AND		ct_gubun='P'
									AND		ct_status='쇼핑'
		";
		$po_qty = sql_fetch($poqty_sql);
		
		/* 공동구매 전체요청수량 + 구매자가 요청한 수량을 더하면 볼륨프라이싱을 구할수 있음 */
		$total_qty = $po_qty[total_qty] + $_POST['ct_qty'];
		
		/* 볼륨프라이싱에 해당하는 가격정보 로딩  */
		$price_sql = "	SELECT	*
									FROM		{$g5['g5_shop_group_purchase_option_table']}
									WHERE	gp_id='".$it['gp_id']."'
									AND		po_sqty <= '".$total_qty."'
									AND		po_eqty >= '".$total_qty."'
		";
		$수량별가격정보 = sql_fetch($price_sql);
// 		echo $poqty_sql."<br>";
		
		/* 달러 -> 원화 & 수수료,관세 금액 계산 */
// 		echo $수량별가격정보[po_cash_price]."<br>";
		$원화가격 = getExchangeRate($수량별가격정보[po_cash_price],$it[gp_id]);
// 		echo $원화가격."<br>";
		
		// 공동구매 총액계산
		//isPurchaseBuyTotalAmountCheck($it['ca_id'],$원화가격*$_POST['ct_qty']);
		$ct_payment = 'B';	//B : 현금결제
	
		// 장바구니에 Insert
		$comma = '';
		
		
		
		$ins_sql = " INSERT INTO {$g5['g5_shop_cart_table']}	SET 
												od_id	= '$tmp_cart_id',
												ct_gubun	= 'P',
												mb_id	=	'$신청자ID',
												it_id	=	'{$it['gp_id']}',
												it_name	=	'".addslashes(strip_tags($it['gp_name']))."',
												ct_status	=	'쇼핑',
												
												ct_usd_price	=	'".$수량별가격정보[po_cash_price]."',		/* 결제시 달러 현금or카드 */
												ct_price	=	'$원화가격',	/* 달러 -> 원화 & 수수료,관세 금액 계산 */
												cp_price	=	'".$_SESSION[unit_kor_duty]."',	/* 현재의 달러 환율 기록 */
												ct_payment	=	'$ct_payment',	/* 결제방식  B 현금, C 카드 */
												
												ct_point	=	'0',
												ct_point_use	=	'0',
												ct_stock_use	=	'0',
												
												ct_qty		=	'".$수량."',
												ct_time	=	'".G5_TIME_YMDHIS."',
												ct_ip		=	'$REMOTE_ADDR',
												ct_send_cost	=	'0',	/* 배송비 */
												ct_direct	=	'$sw_direct',	/* 바로구매 여부 */
												
												ct_type	=	'$ca_id',						/* 카테고리ID */
												ct_time_code	=	".strtotime(G5_TIME_YMDHIS).",
												
												ct_op_option = '$op_name'
												$etc_sql
		";
		
		if(sql_query($ins_sql)) {
			echo "$gp_id 상품 / $신청자ID / $수량"."개 공동구매 대리신청 완료<br>";
		}
		
		
		if($개발자) {
			echo "<textarea style='width:100%; height:50px;'>공구진행정보로딩 : ".$gp_code."</textarea><br>";
			echo "<textarea style='width:100%; height:50px;'>공구신청수량로딩 : ".$poqty_sql."</textarea><br>";
			echo "<textarea style='width:100%; height:50px;'>공구상품볼륨프라이싱로딩 : ".$price_sql."</textarea><br>";
			echo "<textarea style='width:100%; height:50px;'>공구정보신청 : ".$ins_sql."</textarea><br>";
		}		
			
	}
	
	$deputy_sql = "	INSERT	INTO	deputy_order_list	SET
										list_mb_id = '$list_mb_id',
										list_mb_nick = '$list_mb_nick',
										gp_id = '$gp_id',
										qty = '$ct_qty',
										reg_date = now()
	";
	sql_query($deputy_sql);
	
	// 해당상품에 대한 공구진행정보 카운팅 기록
	if($gp_code) purchaseItemGorupUpdate($gp_code,$it['gp_id']);
	
	echo '-----종료----';
	echo "<center>";
/* 추후 정리되야할 값들 */
// 	ct_card_price	=	'$ct_card_price',		/* 카드결제시 금액 사실상 없어도 될값..ct_price가 결제시금액, payment로 현금인지 카드인지 구분 */
// 	ct_card_status	=	'$ct_card_status',	/* 카드결제 상태? */
// 	ct_option	=	'$io_value',		/* 옵션값인듯한데... 연결된게 없음.. */
// 	ct_select	=	'$ct_select',			/* 의미를 모르겠음.. 의미 찾는중 */ 

}
?>