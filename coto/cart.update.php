<?php
include_once('./_common.php');



//장바구니 페이지에서 사용하는 UPDATE, DELETE

$ss_id = $_SESSION[ss_id];
$mb_id = $member[mb_id];


// 브라우저에서 쿠키를 허용하지 않은 경우라고 볼 수 있음.
if (!$ss_id && !$mb_id)
{
	alert('더 이상 작업을 진행할 수 없습니다.\\n\\n브라우저의 쿠키 허용을 사용하지 않음으로 설정한것 같습니다.\\n\\n브라우저의 인터넷 옵션에서 쿠키 허용을 사용으로 설정해 주십시오.\\n\\n그래도 진행이 되지 않는다면 쇼핑몰 운영자에게 문의 바랍니다.');
}


//선택상품들 업데이트
if($mode == 'CHK_UPDATE') {
 	if(!count($_POST['ct_chk']))	alert("수정하실 상품을 하나이상 선택해 주십시오.");

	$error = 0;
	
	for($i = 0; $i < count($_POST['it_id']); $i++) {
		$ct_chk = $_POST['ct_chk'][$i];


		if($ct_chk)
		{
			$gpcode = $_POST['gpcode'][$i];
			$it_id = $_POST['it_id'][$i];
			$it_qty = $_POST['it_qty'][$i];


			$WHERE_CART = "WHERE		(ss_id = '$ssid'	OR	mb_id = '$mb_id')
											AND			gpcode	= '$gpcode'
											AND			it_id		= '$it_id'
											AND			stats IN ('00')
			";

			$chk_sql = str_replace('!공구코드!',$gpcode,$sql_cart_update);
			$chk_sql = str_replace('!상품ID!',$it_id,$chk_sql);
			$chk_sql = str_replace('!WHERE_CART!',$WHERE_CART,$chk_sql);
			$chk = mysql_fetch_array(sql_query($chk_sql));
			
			
			//현재재고(상품재고-총주문신청수량)보다 내가담을수량(카트수량 + 내가담을수량)이 현재재고보다 오버되면 못담게 수정
			//현재재고 = DB의 jaego    APMEX는 정보갱신시 차감, COTO는 누적주문수량을 빼줘야함.
			$현재재고 = $chk[real_jaego];
			$담을수량 = ($it_qty);

			if( $현재재고 < $담을수량 ) {
				$error++;
				$msg .= strip_tags($chk[it_name]." 해당상품의 장바구니에 담을수량(총 {$담을수량}ea)이 현재 재고수량({$현재재고}ea)을 초과하였습니다.\\r\\n");
			}
			else {
				$upd_sql = "UPDATE	coto_cart		SET
													it_qty = '$it_qty'
										$WHERE_CART
				";
				sql_query($upd_sql);
			}
		}
	}

	if($error > 0) alert($msg);
}



//선택상품들 삭제
if ($mode == "CHK_DELETE")
{
 	if(!count($_POST['ct_chk']))	alert("삭제하실 상품을 하나이상 선택해 주십시오.");

	for($i = 0; $i < count($_POST['it_id']); $i++) {

		if($_POST['ct_chk'][$i])
		{
			$gpcode = $_POST['gpcode'][$i];
			$it_id = $_POST['it_id'][$i];

			//세션&계정 관련 값 추출 위해 coto_cart에만 사용, SELECT, UPDATE
			$WHERE = "WHERE		(ss_id = '$ss_id'	OR	mb_id = '$mb_id')
								AND			gpcode	= '$gpcode'
								AND			it_id		= '$it_id'
			";
			
			

			$sql = "UPDATE	coto_cart	SET
												stats = 99
							$WHERE
			";
			sql_query($sql);
		}
	}
}

//상품 하나 삭제
if ($mode == "DELETE")
{
	if(!$_POST['del_it_id']) alert("삭제하실 상품을 하나이상 선택해 주십시오.");

		$it_id = $_POST['del_it_id'];

		//세션&계정 관련 값 추출 위해 coto_cart에만 사용, SELECT, UPDATE
		$WHERE = "WHERE		(ss_id = '$ss_id'	OR	mb_id = '$mb_id')
							AND			gpcode	= '$gpcode'
							AND			it_id = '$it_id'
		";
		$sql = "UPDATE	coto_cart	SET
											stats = 99
						$WHERE
		";
		sql_query($sql);
}

goto_url('/coto/cart.php');

?>