//모바일용 우편번호 display
function basket_order_zipcode_display()
{
	basket_order_change_Obj		= document.getElementById("basket_order_zipcode_layer");

	Height_Tmp	= document.body.scrollTop;
	Left_Tmp	= document.body.scrollWidth;

	Height_Box	= basket_order_change_Obj.style.height;
	Height_Box	= Height_Box.replace("px","");
	Height_Box	= parseInt(Height_Box);

	Width_Box	= basket_order_change_Obj.style.width;
	Width_Box	= Width_Box.replace("px","");
	Width_Box	= parseInt(Width_Box);

	// 현재 박스창 구하기
	if ( window.innerHeight == undefined )
	{
		Now_Window_Height = document.body.offsetHeight;
	}
	else
	{
		Now_Window_Height = window.innerHeight;
	}


	Height_Tmp	= Height_Tmp + ( Now_Window_Height / 2 ) - ( Height_Box / 2 );

	Left_Tmp	= (Left_Tmp / 2) - (Width_Box / 2);

	//alert(Height_Tmp);
	//alert(Left_Tmp);

	basket_order_change_Obj.style.top	= Height_Tmp + "px";
	basket_order_change_Obj.style.left	= Left_Tmp + "px";

	if( basket_order_change_Obj.style.display == 'none' )
	{
		basket_order_change_Obj.style.display = "";
	}
	else
	{
		basket_order_change_Obj.style.display = "none";
	}
}


//주문정보의 회원주소 찾기
function searchMbPostcode() {
	daum.postcode.load(function(){
		new daum.Postcode({
			oncomplete: function(data) {
				// 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

				// 도로명 주소의 노출 규칙에 따라 주소를 조합한다.
				// 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
				var fullRoadAddr = data.roadAddress; // 도로명 주소 변수
				var extraRoadAddr = ''; // 도로명 조합형 주소 변수

				// 법정동명이 있을 경우 추가한다.
				if(data.bname !== ''){
					extraRoadAddr += data.bname;
				}
				// 건물명이 있을 경우 추가한다.
				if(data.buildingName !== ''){
					extraRoadAddr += (extraRoadAddr !== '' ? ', ' + data.buildingName : data.buildingName);
				}
				// 도로명, 지번 조합형 주소가 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
				if(extraRoadAddr !== ''){
					extraRoadAddr = ' (' + extraRoadAddr + ')';
				}
				// 도로명, 지번 주소의 유무에 따라 해당 조합형 주소를 추가한다.
				if(fullRoadAddr !== ''){
					fullRoadAddr += extraRoadAddr;
				}

				// 우편번호와 주소 정보를 해당 필드에 넣는다.
				//document.getElementById("zip").value = data.postcode1+data.postcode2;
				document.getElementsByName("mb_zip1")[0].value = data.zonecode.substring(0,3);
				document.getElementsByName("mb_zip2")[0].value = data.zonecode.substring(0,2);


				document.getElementsByName("mb_addr1")[0].value = fullRoadAddr;
				document.getElementsByName("mb_addr_jibeon")[0].value = data.jibunAddress;
				//document.getElementsByName("addr1")[0].value = data.jibunAddress;
				//document.getElementById("zip").value = data.zonecode;
				//document.getElementById("addr1_2").value = fullRoadAddr;
				//document.getElementById("addr1").value = data.jibunAddress;

				// 사용자가 '선택 안함'을 클릭한 경우, 예상 주소라는 표시를 해준다.
				if(data.autoRoadAddress) {
					//예상되는 도로명 주소에 조합형 주소를 추가한다.
					var expRoadAddr = data.autoRoadAddress + extraRoadAddr;
					//document.getElementsByName("guide")[0].innerHTML = '(예상 도로명 주소 : ' + expRoadAddr + ')';
					//document.getElementById("guide").innerHTML = '(예상 도로명 주소 : ' + expRoadAddr + ')';

				} else if(data.autoJibunAddress) {
					var expJibunAddr = data.autoJibunAddress;
					//document.getElementsByName("guide")[0].innerHTML = '(예상 지번 주소 : ' + expJibunAddr + ')';
					//document.getElementById("guide").innerHTML = '(예상 지번 주소 : ' + expJibunAddr + ')';
				} else {
					//document.getElementsByName("guide")[0].innerHTML = '';
					//document.getElementById("guide").innerHTML = '';
				}
			}
		}).open();
	})
}


//주문정보의 회원주소 찾기
function searchPostcode() {
	daum.postcode.load(function(){
		new daum.Postcode({
			oncomplete: function(data) {
				// 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.
	
				// 도로명 주소의 노출 규칙에 따라 주소를 조합한다.
				// 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
				var fullRoadAddr = data.roadAddress; // 도로명 주소 변수
				var extraRoadAddr = ''; // 도로명 조합형 주소 변수
	
				// 법정동명이 있을 경우 추가한다.
				if(data.bname !== ''){
					extraRoadAddr += data.bname;
				}
				// 건물명이 있을 경우 추가한다.
				if(data.buildingName !== ''){
					extraRoadAddr += (extraRoadAddr !== '' ? ', ' + data.buildingName : data.buildingName);
				}
				// 도로명, 지번 조합형 주소가 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
				if(extraRoadAddr !== ''){
					extraRoadAddr = ' (' + extraRoadAddr + ')';
				}
				// 도로명, 지번 주소의 유무에 따라 해당 조합형 주소를 추가한다.
				if(fullRoadAddr !== ''){
					fullRoadAddr += extraRoadAddr;
				}
	
				// 우편번호와 주소 정보를 해당 필드에 넣는다.
				//document.getElementById("zip").value = data.postcode1+data.postcode2;
				document.getElementsByName("zip")[0].value = data.zonecode;
				document.getElementsByName("addr1_2")[0].value = fullRoadAddr;
				document.getElementsByName("addr1")[0].value = data.jibunAddress;			
				//document.getElementById("zip").value = data.zonecode;
				//document.getElementById("addr1_2").value = fullRoadAddr;
				//document.getElementById("addr1").value = data.jibunAddress;
	
				// 사용자가 '선택 안함'을 클릭한 경우, 예상 주소라는 표시를 해준다.
				if(data.autoRoadAddress) {
					//예상되는 도로명 주소에 조합형 주소를 추가한다.
					var expRoadAddr = data.autoRoadAddress + extraRoadAddr;
					//document.getElementsByName("guide")[0].innerHTML = '(예상 도로명 주소 : ' + expRoadAddr + ')';
					//document.getElementById("guide").innerHTML = '(예상 도로명 주소 : ' + expRoadAddr + ')';
	
				} else if(data.autoJibunAddress) {
					var expJibunAddr = data.autoJibunAddress;
					//document.getElementsByName("guide")[0].innerHTML = '(예상 지번 주소 : ' + expJibunAddr + ')';
					//document.getElementById("guide").innerHTML = '(예상 지번 주소 : ' + expJibunAddr + ')';
				} else {
					//document.getElementsByName("guide")[0].innerHTML = '';
					//document.getElementById("guide").innerHTML = '';
				}
			}
		}).open();
	})
}

/* submit */
function chkForm() {
	var v_chk = 0;
	var v_qty = 0;

	$('input').each(function(e){
		if( ($(this).attr('type') == 'text' || $(this).attr('type') == 'tel') && (!$(this).val() && $(this).attr('title') != undefined) ) {
			// 방문수령일때 ZIP또는 상세주소 입력통과
			if( !($('#delivery_type').val() == 'D03' && $(this).attr('name') == 'zip' || $(this).attr('name') == 'addr2') ) {
				alert($(this).attr('title')+'을(를) 입력해주세요'+$(this).val() );
				v_chk++;
			}
		}
	});

	$('.itemqty').each(function(e){
		v_qty += $(this).val();
	});

	if(v_qty == 0) {
		alert('최소 하나 이상 상품을 선택하셔야합니다');
		v_chk++;
	}

	if(v_chk > 0)
		return false;
	else
		return true;
}

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
			alert('남은수량을 초과하였습니다\r\n대량구매는 유선상으로 문의주세요');
			$('.it_qty'+v_id).val('');
		}
		if(v_qty > ($('#gp_buy_max_qty'+v_id).val()*1)) {
			alert('최대신청가능수량을 초과하였습니다');
			$('.it_qty'+v_id).val('');
		}
	}
	calc_orderinfo();
}

//총 주문금액 계산
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

	/* 결제타입에 따른 카드결제시 수수료추가 */
	if($('#paytype').val() == 'P02') v_total = (v_total * 1.03);

	/* 선불 */
	if($('#delivery_type').val() == 'D01') {
		v_total += (v_baesong*1);
		v_html += "+ 선불 택배비 "+v_baesong+"원(3kg당 "+v_baesong+"원)";
	}

	
	//달러결제시 예상달러제시
	if($('#paytype').val() == 'P03') {
		
		var v_payusd = ( (v_total*1) / (v_usd*1) );
		
		$('.paytype').html("예상달러결제금액($환율 "+v_usd+"원) : $" + v_payusd.toFixed(1) );
	}
	else {
		$('.paytype').html("");
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


/* 주문서작성폼 전송시 항목유효성 체크 */
function checkForm() {
	var v_chk = 0;

	$('input').each(function(e){
		if( ($(this).attr('type') == 'text' || $(this).attr('type') == 'tel') && (!$(this).val() && $(this).attr('title') != undefined) ) {
			// 방문수령일때 ZIP또는 상세주소 입력통과
			if( !($('#delivery_type').val() == 'D03' && $(this).attr('name') == 'zip' || $(this).attr('name') == 'addr2') ) {
				alert($(this).attr('title')+'을(를) 입력해주세요'+$(this).val() );
				v_chk++;
			}
		}
	});

	if(v_chk > 0)
		return false;
	else
		return true;
}


/* 주문서작성폼 전송 */
function quickOrderSubmit() {

	if(checkForm()) {
		$('#coto_cart').submit();
		$('#btnOrder').hide();
		$('#btnWait').show();
	}
	else {
		return false;
	}

}

//카트 갱신
function submitCart(mode) {
	var f = document.coto_cart;
	$('#mode').val(mode);
	f.submit();
}

//단일상품 수정
function submitCartItem(mode,no) {
	$(".product1").find("input[name^=ct_chk]").attr("checked", false);
	$("#ct_chk_"+no).attr("checked", true);

	var f = document.coto_cart;
	$('#mode').val(mode);
	f.submit();
}

function deleteCart(it_id) {
	var f = document.coto_cart;
	$('#mode').val('DELETE');
	$('#del_it_id').val(it_id);
	f.submit();
}

//주문하기
function orderCart() {
	document.location.href = '/coto/orderpay.php';
}