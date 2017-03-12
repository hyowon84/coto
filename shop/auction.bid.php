<?php
include_once('./_common.php');
define('_GROUPSHOP_', true);


//마감 1분 이내에 입찰시도시 1분 연장, 최대 ?분까지 연장할지는 추후 협의시 아래 소스에서 작성
function delayAuctionEnddate($gp_id) {
	return true;	
}


if(!$member[mb_id]) {
	alert_close("회원만 입찰이 가능합니다");
}


//경매상품 정보
$sql_auction_item = str_replace('#상품기본조건#', " AND		gp_id = '$gp_id' ", $sql_auction_item);
$it = sql_fetch($sql_auction_item);
//echo "<textarea>".$sql_auction_item."</textarea>";


$시작가 = $it[ac_startprice];
$즉구가 = ($it[ac_buyprice]) ? $it[ac_buyprice] : $it[po_cash_price];	//즉구가 설정값이 없으면 실시간시세값으로 설정

$최고입찰가 = $it[MAX_BID_PRICE];
$최고현재가 = $it[MAX_BID_LAST_PRICE];


$현재가 = ($최고현재가 > 0) ? $최고현재가 : $시작가;	//bid정보가 없으면 시작가로 시작
$최소입찰금액 = ($최고현재가 > 0) ? calcBidPrice($최고현재가) : $시작가;	//bid정보가 없으면 시작가로 시작
$진행수량 = $it[ac_qty];
$종료일 = $it[ac_enddate];
$입찰수 = $it[BID_CNT];
$경매진행여부 = ( date("Y-m-d H:i:s") < $종료일 && $it[ac_yn] == 'Y') ? 'Y' : 'N';

if($경매진행여부 == 'N') alert_close("경매가 종료되었습니다"); 

/*경매입찰시도*/
$bid_qty = 1;	//입찰수량 1개로 고정

if($mode == 'auc_bid') {	
//	$bid_qty = $_POST[bid_qty];	//입찰수량, 입찰제한수량이 1개일경우 브레이크
	
	$입찰시도금액 = $_POST[bid_price]*1;	//입찰가
	$현재입찰가계산 = calcBidPrice($최고현재가);

	$mybid_sql = "	SELECT	*
									FROM		auction_log AL
									WHERE		AL.mb_id = '$member[mb_id]'
									AND			AL.ac_code = '$it[ac_code]'
									AND			AL.it_id = '$it[gp_id]'
									ORDER BY	AL.bid_price DESC
									LIMIT 1
	";
	$mybid = sql_fetch($mybid_sql);

	/* step1. 현재 입찰가보다 커야 입찰금액을 입력할수 있음*/
	if($입찰시도금액 <= $최고현재가) {
		alert("현재 입찰중인 가격(".number_format($최고현재가)."원)보다 높은 금액을 입력하셔야합니다");
	}
	
	//입찰시도금액이 이전 최고입찰가보다 높아야함
	else if($입찰시도금액 <= $mybid [bid_price]) {
		alert("이전에 입찰한 가격(".number_format($mybid[bid_price])."원)보다 높은 금액을 입력하셔야합니다");
	}
	
//	//입찰시도금액이 나의 이전 최고입찰가보다 높고, 현재의 최고입찰가보다 높은경우 상향조정
//	else if($입찰시도금액 > $mybid [bid_price] && $입찰시도금액 > $최고입찰가) {
//
//		$t = explode('.',_microtime());	$timestamp = date("Y-m-d H:i:s.",$t[0]).$t[1];
//		$UPD_SQL = "	UPDATE	auction_log	SET
//														bid_price				= '$입찰시도금액',			/*입찰가격*/
//														bid_from				=	'$접속기기',
//														bid_stats				= '01'
//									WHERE		mb_id						= '$member[mb_id]'	/*입찰회원계정*/
//									AND			ac_code					= '$ac_code'				/*경매진행코드*/
//									AND			it_id						= '$it[gp_id]'			/*경매상품코드*/
//		";
//		sql_query($UPD_SQL);
//
//		alert_close("입찰금액이 상향조정 되었습니다");
//		exit;
//	}

	/* step2. 입찰시도자는 다른사람의 최고입찰금액보다 커야 입찰이 가능함,
						다른사람의 최고입찰금액의 입찰정보는 현재 입찰시도금액 기준으로 새로 입력해줘야함	*/
	else if($입찰시도금액 < $최고입찰가) {
		
		//이전 최고입찰자의 오토비딩정보 입력
		$bid_last_price = calcBidPrice($입찰시도금액);
		$bid_last_price = ($bid_last_price > $최고입찰가) ? $최고입찰가 : $bid_last_price;
		$t = explode('.',_microtime());	$timestamp = date("Y-m-d H:i:s.",$t[0]).$t[1];
		$auto_sql = "	INSERT INTO		auction_log		SET
																	ac_code = '$it[ac_code]',							/*경매진행코드*/
																	it_id = '$it[gp_id]',									/*경매상품코드*/
																	it_name = '$it[gp_name]',							/*경매상품명*/
																	mb_id = '$it[MB_ID]',									/*입찰회원계정*/
																	bid_qty = '$bid_qty',									/*입찰수량*/
																	bid_price = '$it[MAX_BID_PRICE]',			/*입찰가격*/
																	bid_last_price = '$bid_last_price',		/*현재 입찰가 기록*/
																	bid_stats = '05',										/*낙찰여부*/
																	bid_dbdate			= NOW(),							/*등록일*/
																	bid_date				= '$timestamp',
																	bid_from				=	'SERVER'
		";
		sql_query($auto_sql);
		
		//입찰시도자의 비딩히스토리 입력
		$t = explode('.', _microtime());
		$timestamp = date("Y-m-d H:i:s.", $t[0]) . $t[1];
		$auto_sql = "	INSERT INTO		auction_log		SET
																ac_code					= '$it[ac_code]',							/*경매진행코드*/
																it_id						= '$it[gp_id]',									/*경매상품코드*/
																it_name					= '$it[gp_name]',							/*경매상품명*/
																mb_id						= '$member[mb_id]',									/*입찰회원계정*/
																bid_qty					= '$bid_qty',									/*입찰수량*/
																bid_price				= '$입찰시도금액',			/*입찰가격*/
																bid_last_price	= '$입찰시도금액',		/*현재 입찰가 기록*/
																bid_stats 			= '90',										/*낙찰여부*/
																bid_dbdate			= NOW(),							/*등록일*/
																bid_date				= '$timestamp',
																bid_from				=	'$접속기기'

		";
		sql_query($auto_sql);
		
		delayAuctionEnddate($it[gp_id]);
		
		alert("현재 입찰중인 가격(".number_format($bid_last_price)."원)보다 입력하신 금액(".number_format($입찰시도금액)."원)이 낮아 입찰에 실패하였습니다 좀더 높은 금액을 입력해주세요");
	}
	/* 5000원이 맥스 입찰가고  현재가 4000원이면  내가 6000원을 입력하면 5000원 자동입찰 기록을 해두고 나의 입찰가를 구해야함 */
	else if($입찰시도금액 >= $최고입찰가 && $최고입찰가 > 1000) {

		if( ($입찰시도금액 > $최고입찰가 && $최고입찰가 > $최고현재가) || ($입찰시도금액 == $최고입찰가 && $최고입찰가 > 1000) ) {
			//# 입찰금액이랑 최고입찰가가 같으면 오토비딩후 종료,
			//# 입찰금액이 최고입찰가보다 높으면 오토비딩도 하고 높은입찰금액도 입력
			//이전 최고입찰자의 오토비딩정보 입력
			$bid_last_price = $최고입찰가;
			$bid_last_price = ($bid_last_price > $최고입찰가) ? $최고입찰가 : $bid_last_price;
			$t = explode('.', _microtime());
			$timestamp = date("Y-m-d H:i:s.", $t[0]) . $t[1];
			$auto_sql = "	INSERT INTO		auction_log		SET
																	ac_code = '$it[ac_code]',							/*경매진행코드*/
																	it_id = '$it[gp_id]',									/*경매상품코드*/
																	it_name = '$it[gp_name]',							/*경매상품명*/
																	mb_id = '$it[MB_ID]',									/*입찰회원계정*/
																	bid_qty = '1',												/*입찰수량*/
																	bid_price = '$it[MAX_BID_PRICE]',			/*입찰가격*/
																	bid_last_price = '$bid_last_price',		/*현재 입찰가 기록*/
																	bid_stats = '05',											/*낙찰여부*/
																	bid_dbdate			= NOW(),							/*등록일*/
																	bid_date				= '$timestamp',
																	bid_from				=	'SERVER'
			";
			sql_query($auto_sql);

			delayAuctionEnddate($it[gp_id]);
		}
		
		
		if($입찰시도금액 == $최고입찰가 && $최고입찰가 > 1000) {
			alert("현재 입찰중인 가격(".number_format($bid_last_price)."원)과 동일하여 입찰에 실패하였습니다 좀더 높은 금액을 입력해주세요");
			//break
		}
		else {
			$최고현재가 = $최고입찰가;//갱신
		}

	}
	
	
	/* 
	시작가가 1000원,   입찰가 3000이면   시작가 1000이   last_bid_price
	입찰가 2000원 시도시 입찰실패, calcBidPrice(입찰시도가) 금액으로 오토비딩  
	*/
	if($최고현재가) {
		//이전에 누군가 입찰한 금액이 존재할경우 그사람보다 크면    calcBidPrice(현재최고가)
		$bid_last_price = calcBidPrice($최고현재가);
	}
	else {
		$bid_last_price = $시작가;	//입찰시도금액은 직접 입력했지만 시작가랑 같을수 있다
	}
	
	$t = explode('.',_microtime());	$timestamp = date("Y-m-d H:i:s.",$t[0]).$t[1];
	$UPD_SQL = "	INSERT INTO auction_log	SET
													ac_code					= '$ac_code',				/*경매진행코드*/
													it_id						= '$it[gp_id]',			/*경매상품코드*/
													it_name 				= '$it[gp_name]',		/*경매상품명*/
													mb_id						= '$member[mb_id]',	/*입찰회원계정*/
													bid_qty					= '$bid_qty',			
													bid_price				= '$bid_price',			/*입찰가격*/
													bid_last_price	= '$bid_last_price',		/*현재 입찰가 기록*/
													bid_stats 			= '00',	/* 00:입찰신청, 05:오토비딩, 10:낙찰, 20:결제완료, 80:구매자취소 ,90:판매자 취소*/
													bid_dbdate			= NOW(),							/*등록일*/
													bid_date				= '$timestamp',
													bid_from				=	'$접속기기'
	";
	sql_query($UPD_SQL);
	delayAuctionEnddate($it[gp_id]);
	
	alert_close("입찰되었습니다 현재 입찰가가 ".number_format($bid_last_price)."원으로 갱신되었습니다");
	exit;
}
//echo "<textarea>".$sql."</textarea>";

if($mode == 'jhw') {
	echo $sql;
}


if (G5_IS_MOBILE) {
	include_once(G5_MSHOP_SKIN_PATH.'/auction.bid.skin.php');
}
else {
	include_once(G5_SHOP_SKIN_PATH.'/auction.bid.skin.php');
}
?>

