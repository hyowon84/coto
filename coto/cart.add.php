<?php
include_once('./_common.php');

## 상품목록 페이지에서 사용하는 장바구니 담기 소스
## 장바구니 담기 버튼은 개별주문 공구코드 활성화 되있는경우에만 가능하다
## 코투상품은 현재 재고값 = jaego - 누적주문수량
## 딜러상품은 현재 재고값 = jaego

$ss_id = $_SESSION[ss_id];
$mb_id = $member[mb_id];
$fail = "1";

// 브라우저에서 쿠키를 허용하지 않은 경우라고 볼 수 있음.
if (!$ss_id && !$mb_id)
{
	$msg = '더 이상 작업을 진행할 수 없습니다.\\n\\n브라우저의 쿠키 허용을 사용하지 않음으로 설정한것 같습니다.\\n\\n브라우저의 인터넷 옵션에서 쿠키 허용을 사용으로 설정해 주십시오.\\n\\n그래도 진행이 되지 않는다면 쇼핑몰 운영자에게 문의 바랍니다.';

}

//장바구니에 추가할 상품
else if($it_id) {

	$chk_sql = str_replace('!공구코드!', $gpcode, $sql_cart_update);
	$chk_sql = str_replace('!상품ID!', $it_id, $chk_sql);
	$chk_sql = str_replace('!WHERE_CART!', $WHERE_CART, $chk_sql);
	$chk = mysql_fetch_array(sql_query($chk_sql));
	
//	echo $chk_sql;
	//현재재고(상품재고-총주문신청수량)보다 내가담을수량(카트수량 + 내가담을수량)이 현재재고보다 오버되면 못담게 수정
	//현재재고 = DB의 jaego    APMEX는 정보갱신시 차감, COTO는 누적주문수량을 빼줘야함.
	$현재재고 = $chk[real_jaego];
	$담을수량 = ($it_qty + $chk[CT_SUM]);	//장바구니 총 담는수량


	$회원계정조건 = (strlen($mb_id) > 4) ? "	OR	CL.mb_id = '$mb_id'	" : '';
	$od_sql = "	SELECT	CL.mb_id,
											CL.it_id,
											SUM(it_qty) AS SUM_QTY
							FROM		clay_order CL
							WHERE		1=1
							AND			CL.it_id = '$it_id'
							AND			( CL.mb_id = '$ss_id' $회원계정조건)
							AND			CL.stats >= '00'
							AND			CL.stats <= '60'
							AND			CL.od_date >= DATE_FORMAT(DATE_ADD(NOW(),INTERVAL -1 DAY ),'%Y-%m-%d')
							GROUP BY CL.mb_id, CL.it_id
	";
	$sumdata = mysql_fetch_array(sql_query($od_sql));

	$최소구매수량 = $chk[gp_buy_min_qty];
	$최대구매수량 = $chk[gp_buy_max_qty];
	$주문내역수량 = ($sumdata[SUM_QTY]) ? $sumdata[SUM_QTY] : 0;
	$회원전용여부 = ($chk[only_member] && !$mb_id);

	
//	echo "$최대구매수량 / $주문내역수량 / $it_qty + $chk[CT_SUM] ";
//	echo $chk_sql;
//	echo $od_sql;
	
	
	if($회원전용여부) {
		$msg = "본 상품은 코인즈투데이 회원만 주문이 가능한 상품입니다. 회원가입후 다시 시도해주세요";
		$fail = 'true';
	}
	//최대구매수량초과
	else if($최대구매수량 < ($담을수량 + $주문내역수량) && $최대구매수량 != 0) {
		//하루내에 주문내역수량 + 주문하려는 수량의 합이 최대구매수량을 초과시 경고
		if($담을수량 > 0 && $주문내역수량 > 0) {
			$msg = "장바구니 수량({$담을수량}ea)과 금일 주문수량({$주문내역수량}ea)의 합이 최대구매수량({$최대구매수량}ea)을 초과하였습니다";
			$fail = 'true';
		}
		//이미 담은 수량이 있는 상태에서 더 담기시 "장바구니에 담을수 있는 수량 초과" 경고
		else if($담을수량 > 0 && $주문내역수량 == 0) {
			$msg = "장바구니 수량({$담을수량}ea)이 최대구매수량({$최대구매수량}ea)을 초과하였습니다";
			$fail = 'true';
		}

	}

	/*초과해서 장바구니 담는경우 에러 리턴*/
	else if( $현재재고 < $담을수량 ) {
		$msg = "남은수량을 초과하였습니다 대량구매는 유선상으로 문의주세요";
		$fail = 'true';
	}
	

	/* 장바구니에 등록된게 있으면 더하기 */
	if( $chk[CT_SUM] > 0 && $chk[CT_ID] ) {
		$SQLTYPE = "UPDATE";
		$수량설정 = " ,it_qty = '$담을수량'	/*상품수량*/ ";
	}
	else {
		$SQLTYPE = "INSERT INTO";
		$수량설정 = " ,it_qty = '$it_qty'	/*상품수량*/ ";
		$초기값 = " ,stats = '00'	,reg_date = now() ";
		$WHERE = '';
		$WHERE_CART = '';
	}

	//실패케이스
	if($fail == 'true') {
		$json['success'] = "true";
		$json['msg'] = $msg;

		$json_data = json_encode_unicode($json);
		echo $json_data;
		exit;
	}
	else {
		$cart_sql = "$SQLTYPE	 coto_cart	 SET
															ss_id = '$ss_id'
															,mb_id = '$mb_id'			/*계정 또는 세션아이디*/
															,gpcode = '$gpcode'		/*연결된 공구코드*/
															,it_id = '$it_id'			/*상품코드*/
															,it_name = '$it_name'	/*상품명*/
															$수량설정
															$초기값
								$WHERE_CART
		";
		$result = sql_query($cart_sql);
	}

	if($result) {
		$msg = "장바구니에 담았습니다";	//성공
	} else {
		$msg = "장바구니 담기 실패"; //실패
	}


	//장바구니 담은 상품 있는지 확인
	$chk_sql = "SELECT	it_qty
							FROM		coto_cart CT
							$WHERE_CART
	";
	$chk_cnt = mysql_fetch_array(sql_query($chk_sql));

	//담을수량 = 내카트에담겼던수량 + 방금담은수량
	$담을수량 = $chk_cnt[it_qty] + $it_qty;

	//상품에 대한 회원전체 주문수량
	$cnt_sql = "	SELECT	SUM(CO.it_qty) AS SUM_QTY,
												IT.jaego
								FROM		clay_order CO
												,(	SELECT		jaego
														FROM		g5_shop_group_purchase
														WHERE		gp_id = '$it_id'
												) IT
								WHERE		CO.it_id = '$it_id'
								AND			CO.stats NOT IN (99)	/* 취소건 제외, 모든 신청수량 */
	";
	$row = mysql_fetch_array(sql_query($cnt_sql));

}
else {
	$msg = "상품ID가 존재하지 않습니다";	//에러, 아이템존재하지 않음
}



$json['success'] = "true";
$json['msg'] = $msg;

$json_data = json_encode_unicode($json);
echo $json_data;

/*
 * 
					/*var msg;
					switch(code) {
						case 999:
							msg = '상품ID가 없음';
							break;
						case 100:
							msg = '장바구니에 담았습니다';
							break;
						case 99:
							msg = '장바구니 담기에 실패';
							break;
						case 96:
							msg = '장바구니에 담을수량이 최대구매수량('+val+')을 초과하였습니다';
							break;
						case 95:
							msg = '최대구매수량을 초과하였습니다';
							break;
						case 90:
							msg = '남은수량을 초과하였습니다\r\n대량구매는 유선상으로 문의주세요';
							break;
						case 80:
							msg = '본 상품은 코인즈투데이 회원만 주문이 가능한 상품입니다. 회원가입후 다시 시도해주세요';
							break;
						default:
							msg = '잘못된실행';
							break;
					}

*/

?>