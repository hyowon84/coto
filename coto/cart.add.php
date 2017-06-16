<?php
include_once('./_common.php');

## 상품목록 페이지에서 사용하는 장바구니 담기 소스
## 장바구니 담기 버튼은 개별주문 공구코드 활성화 되있는경우에만 가능하다
## 코투상품은 현재 재고값 = jaego - 누적주문수량
## 딜러상품은 현재 재고값 = jaego

$ss_id = $_SESSION[ss_id];
$mb_id = $member[mb_id];


// 브라우저에서 쿠키를 허용하지 않은 경우라고 볼 수 있음.
if (!$ss_id && !$mb_id)
{
	alert('더 이상 작업을 진행할 수 없습니다.\\n\\n브라우저의 쿠키 허용을 사용하지 않음으로 설정한것 같습니다.\\n\\n브라우저의 인터넷 옵션에서 쿠키 허용을 사용으로 설정해 주십시오.\\n\\n그래도 진행이 되지 않는다면 쇼핑몰 운영자에게 문의 바랍니다.');
}


//장바구니에 추가할 상품
if($it_id) {

	$chk_sql = str_replace('!공구코드!', $gpcode, $sql_cart_update);
	$chk_sql = str_replace('!상품ID!', $it_id, $chk_sql);
	$chk_sql = str_replace('!WHERE_CART!', $WHERE_CART, $chk_sql);
	$chk = mysql_fetch_array(sql_query($chk_sql));
	
//	echo $chk_sql;
	//현재재고(상품재고-총주문신청수량)보다 내가담을수량(카트수량 + 내가담을수량)이 현재재고보다 오버되면 못담게 수정
	//현재재고 = DB의 jaego    APMEX는 정보갱신시 차감, COTO는 누적주문수량을 빼줘야함.
	$현재재고 = $chk[real_jaego];
	$담을수량 = ($it_qty + $chk[CT_SUM]);	//장바구니 총 담는수량




	$od_sql = "	SELECT	CL.mb_id,
											CL.it_id,
											SUM(it_qty) AS SUM_QTY
							FROM		clay_order CL
							WHERE		1=1
							AND			CL.it_id = '$it_id'
							AND			(CL.mb_id = '$mb_id' OR CL.mb_id = '$ss_id')
							AND			CL.stats >= '00'
							AND			CL.stats <= '60'
							GROUP BY CL.mb_id, CL.it_id
	";
	$sumdata = mysql_fetch_array(sql_query($od_sql));

	$최소구매수량 = $chk[gp_buy_min_qty];
	$최대구매수량 = $chk[gp_buy_max_qty];
	$주문내역수량 = ($sumdata[SUM_QTY]) ? $sumdata[SUM_QTY] : 0;
	$회원전용여부 = ($chk[only_member] && $mb_id);

	
	
	
	if(!$회원전용여부) {
		echo "80";
		exit;
	}
	//최대구매수량초과
	else if($최대구매수량 < ($담을수량 + $주문내역수량) ) {
		echo "95";
		exit;
	}

	/*초과해서 장바구니 담는경우 에러 리턴*/
	else if( $현재재고 < $담을수량 ) {
		echo "90";
		exit;
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

	if($result) {
		echo "100";	//성공
	} else {
		echo "99"; //실패
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
	echo "999";	//에러, 아이템존재하지 않음
}

?>