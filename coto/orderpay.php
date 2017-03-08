<?php
include_once('./_common.php');
$g5['title'] = '주문서 작성';
include_once('./_head.php');

/* 상품목록에서 바로 주문하기일 경우 */
if($_GET[it_id]) {

	if (!($_GET[it_qty] > 0)) {
		alert('수량이 입력되지 않았습니다', '/');
		exit;
	}
	
	if($gpcode == 'QUICK') {
		$sql_product = makeProductSql($gpcode);
		$sql_product_rp = str_replace('#상품기본조건#', " AND		gp_id = '$_GET[it_id]' ", $sql_product);
	} else {
		$sql_product = makeProductSql($gpcode);
		$sql_product_rp = str_replace('#공동구매조건#', " AND		IT.gp_id = '$_GET[it_id]' ", $sql_product);	
	}
	
	/* 상품정보 */
	$it_sql = " SELECT		T.gp_id AS it_id,
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
												CT.ca_adult_use
								FROM		$sql_product_rp
												LEFT JOIN g5_shop_category CT ON (CT.ca_id = T.ca_id)
	";
	$result = sql_query($it_sql);
}
else {
	//장바구니 자료 쿼리
	$cart_sql = "	SELECT	T.gp_id,
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
														CEIL(IFNULL(T.po_cash_price,T.gp_price) / 100) * 100
												END po_cash_price,
												T.number,
												T.it_id,				/*상품코드*/
												T.mb_id,				/*계정 또는 세션아이디*/
												T.gpcode,				/*연결된 공구코드*/
												T.it_qty,				/*상품수량*/
												T.it_name,			/*상품명*/
												T.stats,				/*상태값*/
												T.reg_date,			/*등록일시*/
												CA.ca_name
								FROM		$sql_cartproduct
												LEFT JOIN g5_shop_category CA ON (CA.ca_id = T.ca_id)
								WHERE		T.real_jaego >= T.it_qty
	";
	$result = sql_query($cart_sql);
}
?>
<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>
<script charset="UTF-8" type="text/javascript" src="http://s1.daumcdn.net/svc/attach/U03/cssjs/postcode/1438157450275/150729.js"></script>
<script src="<?=G5_JS_URL?>/common_order.js"></script>
<script>
var v_baesong = 3500;
</script>
<?
//장바구니 페이지
if (G5_IS_MOBILE) {
	include_once('./m/orderpay.php');
	exit;
}
?>

<style>
ff4e00 { color:#ff4e00; }
.totalpriceinfo b1 { font-size:1.4em; font-weight:bold; }
.totalpriceinfo table { border:6px solid #545454; border-collapse: collapse; border-spacing: 0; background-color: white; }
.totalpriceinfo table th, .totalpriceinfo table td { border:1px solid #cfcfcf; }

.totalpriceinfo table th { height:32px; background-color: #f9f9f9; }
.totalpriceinfo table td { height:53px; font-size:1.2em; font-weight:bold; text-align:right; padding-right:10px; }

.totalpriceinfo .totalpricefont { color: #ff4e00; }

.clayOrderTitle { margin:10px; font-size:1.5em; font-weight:bolder; text-align:center; }
.clayOrderTB th, .clayOrderTB td { font-size:0.9em; letter-spacing:-0.5px; }
.clayOrderTB th{ padding-left:5px; width:25%; height:30px; text-align:left; }
.clayOrderTB td{ padding-left:5px; width:75%; height:30px; text-align:left; }
.clayOrderTB .text { font-size:1.3em; width:400px; }

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

<!-- 장바구니 시작 { -->
<script src="<?=G5_JS_URL?>/shop.js"></script>
<script src="<?=G5_JS_URL?>/imgLiquid.js"></script>

<script>
$(document).ready(function() {
	$(".imgLiquidNoFill").imgLiquid({fill:false});
});
</script>

<!-- 타이틀 -->
<div class="cart_title"><?=$g5['title']?></div>

<!-- 탭메뉴 -->
<input type="hidden" id='mode' name="mode" value="UPDATE">

<div class="list_box">
	<div id="sod_bsk">
		<div class="tbl_head01 tbl_wrap product1">
			<table>
				<thead>
				<tr>
					<th scope="col" class="right" colspan="2">상품정보</th>
					<th scope="col" class="right" width="70px">수량</th>
					<th scope="col" class="right">주문금액</th>
				</tr>
				</thead>
				<tbody>
	<?
			$i = 0;
			while($row = mysql_fetch_array($result))
			{

				/*다이렉트 주문일경우 재고가 부족하면 back */
				if($it_id && $row[jaego] < $it_qty) {
					alert("신청수량[$it_qty]이 재고[$row[jaego]]를 초과했습니다. 다시 신청해주세요","/");
					exit;
				}

				$주문수량 = ($it_id && $it_qty > 0) ? $it_qty : $row[it_qty];
				$총무게 += ($row[gp_metal_don] * $주문수량 * 31.1035);
				$상품명 = $row[gp_name];
				$이미지 = "<img src='$row[gp_img]' />";
				$상품판매가 = $row[po_cash_price];
				$예상재고수량 = $row[jaego];
				$주문금액 = $주문수량 * $row[po_cash_price];
				$총상품금액 += $주문금액;
	?>
			<tr>
				<td title='이미지' class="sod_img">
					<div class='imgLiquidNoFill imgLiquid' style='float:left;width:70px;height:70px;padding:0 15px 0 15px;'>
		 				<a href="<?=G5_SHOP_URL.'/grouppurchase.php?gp_id='.$row['it_id']."&ca_id=".$_GET[ca_id]."\" class=\"sct_a sct_img\""?>><?=$이미지?></a>
			 		</div>
				</td>

				<td title='상품명' class="right">
					<input type="hidden" name="it_id[<?=$i?>]"    value="<?=$row['it_id']?>">
					<input type="hidden" name="it_name[<?=$i?>]"  value="<?=get_text($row['it_name'])?>">
					<?=$상품명?>
					<div style="margin:7px 0 0 0;">
						<div style="float:left;width:100%; text-align:right;">판매가 <?=number_format($상품판매가)?>원</div>
					</div>
				</td>

				<td title='수량' class="td_num right" align="center">
					<?=number_format($주문수량)?> EA
				</td>

				<td class="td_numbig right">
					<span id="sell_price_<?=$i?>" class="sell_price sell_price<?=$row['it_id']?>">
						<?=number_format($주문금액)?> 원
					</span>
				</td>
			</tr>

			<?
				$i++;
			} // while


			if ($i == 0) {
				echo '<tr><td colspan="8" class="empty_table">장바구니에 담긴 상품이 없습니다.</td></tr>';
			} else {
				$총무게kg = $총무게 / 1000;
				$누적배송비 = getDeliveryPricePerKg($총무게kg);
				$총결제예상금액 = $누적배송비 + $총상품금액;
			}

			?>
			</tbody>
			</table>

		</div>

	</div>
</div>


<!-- 총가격 정보 -->
<div class='totalpriceinfo'>

	<table width='100%'>
		<tr>
			<td rowspan='2' width='96' style='text-align:center; padding:0px;'><b1>총합계</b1></td>
			<th width='186'>상품금액</td>
			<th width='147'>배송비</td>
			<th width='234'>총결제예상금액</th>
		</tr>
		<tr>
			<td><?=number_format($총상품금액)?>원</td>
			<td><?=number_format($누적배송비)?>원 (<?=number_format($총무게kg,2)?>kg)</td>
			<td><ff4e00><?=number_format($총결제예상금액)?>원</ff4e00></td>
		</tr>
	</table>

</div>


<form name="coto_cart" id="coto_cart" method="post" action="/coto/orderpay.insert.php">
<input type="hidden" name="gpcode" value="<?=$_GET[gpcode]?>" />
<input type="hidden" name="it_id" value="<?=$_GET[it_id]?>" />
<input type="hidden" name="it_qty" value="<?=$_GET[it_qty]?>" />

<div id="sod_bsk_act" style='background-color:white;'>
	<table class='clayOrderTB' style='margin-top:50px;' align='center'>
		<tr>
			<td colspan='2'>
				<div class='clayOrderTitle'>주문서작성</div>
			</td>
		</tr>
		<tr>
			<th>요청사항</th>
			<td><textarea name='memo'  style='width:400px; height:70px;'></textarea></td>
			<?

			if(strlen($member[mb_id]) > 5 && $member[mb_hp] != '' && $member[mb_nick] != '') {

				$이전정보SQL = "	SELECT	CI.*
												FROM		clay_order_info CI
												WHERE		CI.mb_id = '$member[mb_id]'
												ORDER BY CI.od_id DESC
				";			
				$mb = mysql_fetch_array(sql_query($이전정보SQL));
				$hp = explode('-',$mb[hphone]);
			}

			?>
		</tr>
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
				<input class='clayOrderTB hphone' type='tel' id="hp1" name='hp1' title='연락처(첫번째번호)' maxlength="3" value='<?=($hp[0])?$hp[0]:'010'?>' onkeydown="keyNumeric()" />-
				<input class='clayOrderTB hphone' type='tel' id="hp2" name='hp2' title='연락처(가운데번호)' maxlength="4" value='<?=$hp[1]?>' onkeydown="keyNumeric()" />-
				<input class='clayOrderTB hphone' type='tel' id="hp3" name='hp3' title='연락처(마지막번호)' maxlength="4" value='<?=$hp[2]?>' onkeydown="keyNumeric()" />
			</td>
		</tr>
		<tr>
			<th>현금영수증</th>
			<td>
				<div style='border:1px #d7d7d7 solid; padding:5px;'>
					<p>
						<input type='radio' name='cash_receipt_yn' value='N' checked onclick="showHide_cashreceipt('N')">신청안함
						<input type='radio' name='cash_receipt_yn' value='Y' onclick="showHide_cashreceipt('Y')">신청
					</p>

					<div id='cash_receipt_info' style='display:none;'>
						<input type='radio' name='cash_receipt_type' value='C01' checked onclick="choiceOption_cashReceipt('C01')">개인소득공제
						<input type='radio' name='cash_receipt_type' value='C02' onclick="choiceOption_cashReceipt('C02')">사업자지출증빙

						<div id='cash_hp'>휴대폰번호
							<input class='clayOrderTB hphone' type='tel' name='cash_hp1' maxlength="3" value='010' style='width:30px;'>-
							<input class='clayOrderTB hphone' type='tel' name='cash_hp2' maxlength="4" value=''>-
							<input class='clayOrderTB hphone' type='tel' name='cash_hp3' maxlength="4" value=''>
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
				<select id='delivery_type'  name='delivery_type'  onchange="setRcvAddress(); calc_orderinfo();">
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
				<input class='clayOrderTB zip'type='text' class='zip' id='zip' name='zip' title='우편번호' value='<?=$mb[zip]?>' /> <input class='clayOrderTB btn' type='button' value='우편번호검색' onclick='searchPostcode()' style='background-color:black; color:white; font-size:1.1em;' value='<?=$mb[zip]?>' /><br>
				<input class='clayOrderTB text'type='text' class='address' id='addr1' name='addr1' value='<?=$mb[addr1]?>' /><br>
				<input class='clayOrderTB text'type='text' class='address' id='addr1_2' name='addr1_2' value='<?=$mb[addr1_2]?>' /><br>
				<input class='clayOrderTB text'type='text' id='addr2' name='addr2' title='상세주소' value='<?=$mb[addr2]?>' />
			</td>
		</tr>
		<tr>
			<td colspan='2' style='height:20px; font-size:12px; text-align:left; font-weight:bolder;'>
				※ 지번주소와 도로명주소 중 편한 주소로 입력하세요
				<span id='guide'></span><br>
			</td>
		</tr>
		<tr>
			<th><font color=red>*입금자성함</font></th>
			<td><input class='clayOrderTB text'type='text' name='receipt_name' title='입금자성함' value='<?=$mb[receipt_name]?>' /></td>
		</tr>
		<tr>
			<td colspan='2'  style='text-align:center; height:80px;'>
				<input id="btnOrder" type='button' value='주문신청' onclick="quickOrderSubmit()" style='font-size:1.5em; font-weight:bolder; background-color:black; color:white; width:100px; height:40px; border:2px solid black; border-radius:10px;' />
				<input id="btnWait" type='button' value='입력중..' style='display:none;font-size:1.5em; font-weight:bolder; background-color:black; color:white; width:100px; height:40px; border:2px solid black; border-radius:10px;' />
			</td>
		</tr>
	</table>

</div>

</form>

<?php
include_once('./_tail.php');
?>