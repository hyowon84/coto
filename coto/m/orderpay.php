<style>
	ff4e00 { color:#ff4e00; }
	.totalpriceinfo b1 { font-size:1.4em; font-weight:bold; }
	.totalpriceinfo table { border:6px solid #545454; border-collapse: collapse; border-spacing: 0; background-color: white; }
	.totalpriceinfo table th, .totalpriceinfo table td { border:1px solid #cfcfcf; }

	.totalpriceinfo table th { height:32px; background-color: #f9f9f9; }
	.totalpriceinfo table td { height:53px; font-size:1.2em; font-weight:bold; text-align:right; padding-right:10px; }

	.totalpriceinfo .totalpricefont { color: #ff4e00; }
</style>

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

<!-- 장바구니 시작 { -->
<script src="<?=G5_JS_URL?>/common_order.js"></script>
<script src="<?=G5_JS_URL?>/shop.js"></script>
<script src="<?=G5_JS_URL?>/imgLiquid.js"></script>
<script>
$(document).ready(function() {
	$(".imgLiquidNoFill").imgLiquid({fill:false});
});
</script>

<!-- 타이틀 -->
<div class="cart_title" style='font-size:1.3em; font-weight:bold; margin-bottom:20px;'><?=$g5['title']?></div>

<!-- 주문할 목록 -->
<ul>
<?
$tot_point = 0;
$tot_sell_price = 0;
$send_cost1 = 0;

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
	$상품명 = "<b>[$row[ca_name]][$row[it_id]]</b><br>$row[gp_name]";
	$이미지 = "<img src='$row[gp_img]' />";
	$상품판매가 = $row[po_cash_price];
	$예상재고수량 = $row[jaego];
	$주문금액 = $주문수량 * $row[po_cash_price];
	$총상품금액 += $주문금액;


	/* http 가 들어간건 다이렉트로, 아닌건 get_it_thumb함수로 */
	if( strstr($row[gp_img],'http')) {
		$image = "<img src='$row[gp_img]' width=$default[de_mimg_width] />";
	}
	else {
		$image = get_it_thumbnail1($row[gp_img],$default['de_mimg_width'],$default['de_mimg_height'], '', 1);
	}
	?>
	<li class="rtLi">
		<a <?="href=\"".G5_SHOP_URL."/grouppurchase.php?gpcode=".$row[gpcode]."&gp_id=".$row[it_id]."&ca_id=".$row[ca_id]."\" class=\"sct_a sct_img\""?>>
		<div class="productName line1row taCenter">
			<b><?=stripslashes($상품명)?></b>
		</div>
		</a>
		<div class="productImg"><?=$image?></div>
		<div class="productInfo">
			<div class="price"><label>판매가</label><p><?=number_format($상품판매가)?>원</p></div>
	<?	if($is_admin == 'super') {	?>
			<div class="qty"><label>예상재고수량</label><p><?=number_format($예상재고수량)?>ea</p></div>
	<?	}	?>
			<div class="qty"><label>주문수량</label><p><?=number_format($주문수량)?>ea</div>
		</div>
	</li>
<?
	$i++;
} // while end

if ($i == 0) {
	echo '<li>장바구니에 담긴 상품이 없습니다.</li>';
} else {
	//kg단위로 변환
	$총무게kg = $총무게 / 1000;
	$누적배송비 = getDeliveryPricePerKg($총무게kg);
	$총결제예상금액 = $누적배송비 + $총상품금액;

}
?>
</ul>


<!-- 총가격 정보 -->
<div class='totalpriceinfo' style='margin-bottom:20px;'>
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



<div id="sod_bsk_act" style='background-color:white;'>


<script>
//+,- 버튼에 따른 수량증가
function order_add(qty_id,qty,type) {
	var total_qty;

	if(type == 'plus') {
		total_qty = $('.'+qty_id).val()*1+qty;
		total_qty = (total_qty >= 0) ? total_qty : 0;
	}
	if(type == 'minus') {
		total_qty = $('.'+qty_id).val()*1-qty;
		total_qty = (total_qty >= 0) ? total_qty : 0;
	}
	$('.'+qty_id).val(total_qty);

	chk_max_qty();
}

/* 수량체크 초과주문 방지*/
function chk_max_qty() {

	for(var v_id = 0; v_id < $('#it_cnt').val(); v_id++) {
		var v_qty = $('.it_qty'+v_id).val()*1;

		if(v_qty > ($('#gp_have_qty'+v_id).val()*1)) {
			alert('남은수량을 초과하였습니다');
			$('.it_qty'+v_id).val('');
		}
		if(v_qty > ($('#gp_buy_max_qty'+v_id).val()*1)) {
			alert('최대신청가능수량을 초과하였습니다');
			$('.it_qty'+v_id).val('');
		}
	}
	calc_orderinfo();
}

function calc_orderinfo() {
	//주문서정보 html생성
	var v_html = '';

	//주문총금액 계산
	v_total = 0;
	for(var i = 0; i < $('#it_cnt').val(); i++) {
		if($('.it_qty'+i).val() == '0') continue;
		v_total += ($('.it_price'+i).val()*1) * ($('.it_qty'+i).val()*1);
		v_html += $('.itname'+i).html()+" <font color='blue'>[ 수량 : "+$('.it_qty'+i).val()+"개 ]</font><br><br>";
	}

	/* 선불 */
	if($('#delivery_type').val() == 'D01') {
		var v_baesong = '<?=$배송비?>'';
		v_total += v_baesong;
		v_html += "+ 선불 택배비 "+v_baesong+"원(3kg당 <?=$배송비?>원)";
	}

	v_total = number_format(v_total+'');
	$('#txt_price').html(v_total+'원');
	$('.orderinfo').html(v_html);
}

/* 방문수령 선택시 주소자동입력*/
function setRcvAddress() {
	if($('#delivery_type').val() == 'D03') {
		$('#zip').val('06364');
		$('#addr1').val('서울특별시 강남구 자곡동 274');
		$('#addr1_2').val('서울특별시 강남구 밤고개로14길 13-34');
		$('#addr2').val('2층 투데이');
	} else {

	}
}

</script>

<form name="coto_cart" id="coto_cart" method="post" action="/coto/orderpay.insert.php">
<input type="hidden" name="gpcode" value="<?=$_GET[gpcode]?>" />
<input type="hidden" name="it_id" value="<?=$_GET[it_id]?>" />
<input type="hidden" name="it_qty" value="<?=$_GET[it_qty]?>" />

<table class='clayOrderTB' align='center'>
	<tr>
		<td colspan='2'>
			<div class='clayOrderTitle'>주문서작성</div>
		</td>
	</tr>
	<tr>
		<th colspan='2'>
			<div style='color:red; margin-top:5px;'>
				<b>※ 환율변동 또는 상황에 따라 가격변동이 있을수 있습니다.<br>
				* 항목은 필수항목이니 꼭 입력해주시기 바랍니다!
				</b>
			</div>
		</th>
	</tr>
	<tr>
		<th>요청사항</th>
		<td><textarea name='memo'  style='width:200px; height:70px;'></textarea></td>
		<?

		if(strlen($member[mb_id]) > 5 && $member[mb_hp] != '' && $member[mb_nick] != '') {
			$이전정보SQL = "	SELECT	CI.*
											FROM		clay_order_info CI
											WHERE		CI.mb_id = '$member[mb_id]'
											ORDER BY CI.od_id DESC
			";
			$mb = mysql_fetch_array(sql_query($이전정보SQL));
			$hp = explode('-', $mb[hphone]);
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
			<input class='clayOrderTB hphone' type='tel' name='hp1' title='연락처(첫번째번호)' maxlength="3" value='<?=($hp[0])?$hp[0]:'010'?>' />-
			<input class='clayOrderTB hphone' type='tel' name='hp2' title='연락처(가운데번호)' maxlength="4" value='<?=$hp[1]?>' />-
			<input class='clayOrderTB hphone' type='tel' name='hp3' title='연락처(마지막번호)' maxlength="4" value='<?=$hp[2]?>' />
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

					<div id='cash_hp'>H.P
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

			<script>

				/* 현금영수증 신청/미신청 */
				function showHide_cashreceipt(val) {
					if(val == 'Y') {
						$('#cash_receipt_info').show();
					}
					else {
						$('#cash_receipt_info').hide();
					}
				}

				/* 현금영수증 개인/사업자 선택 */
				function choiceOption_cashReceipt(val) {
					if(val == 'C01') {
						$('#cash_hp').show();
						$('#cash_bno').hide();
					}
					else {
						$('#cash_hp').hide();
						$('#cash_bno').show();
					}
				}

			</script>
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