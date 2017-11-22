/* 상품목록, 상품상세페이지 관련 공통 함수 정리 by. JHW */



/*퀵오더 주문서작성페이지로 연결*/
function quickOrder(it_id,gpcode) {
	var it_qty = $('#'+it_id+'_qty').val();

	if(it_qty > 0) {
		document.location.href = '/coto/orderpay.php?it_id='+it_id+'&it_qty='+it_qty+'&gpcode='+gpcode;
	}
	else{
		alert('1개 이상 수량을 입력해주세요 ');
		return;
	}
}

/*수량 조절*/
function qtyCnt(id,cnt) {
	var qty = $('#'+id).val()*1;
	$('#'+id).val( ((qty + cnt) < 0) ? 0 : (qty + cnt) );
}

// 상품보관
function item_wish(item_id,item_qty)
{
	/*
	f.url.value = g5_shop_url+"/wishupdate.php?it_id="+it_id+"&mode=gp";
	f.action = g5_shop_url+"/wishupdate.php";
	f.submit();
	*/
	document.location.href=g5_shop_url+"/wishupdate.php?mode=gp_add&it_id="+item_id+"&ct_qty="+item_qty;
}

// 추천메일
function popup_item_recommend(it_id)
{
	if (!g5_is_member)
	{
		if (confirm("회원만 추천하실 수 있습니다."))
			document.location.href = g5_bbs_url+"/login.php?url="+g5_shop_url+"/item.php?it_id="+it_id;
	}
	else
	{
		url = "./itemrecommend.php?it_id=" + it_id;
		opt = "scrollbars=yes,width=616,height=420,top=10,left=10";
		popup_window(url, "itemrecommend", opt);
	}
}


$(function(){
	// 상품이미지 첫번째 링크
	$("#sit_pvi_big a:first").addClass("visible");

	// 상품이미지 미리보기 (썸네일에 마우스 오버시)
	$("#sit_pvi .img_thumb").bind("mouseover focus", function(){
		var idx = $("#sit_pvi .img_thumb").index($(this));
		$("#sit_pvi_big a.visible").removeClass("visible");
		$("#sit_pvi_big a:eq("+idx+")").addClass("visible");
	});

	// 상품이미지 크게보기
	$(".popup_item_image").click(function() {
		var url = $(this).attr("href");
		var top = 10;
		var left = 10;
		var opt = 'scrollbars=yes,top='+top+',left='+left;
		popup_window(url, "largeimage", opt);

		return false;
	});
});


// 바로구매, 장바구니 폼 전송
function fitem_submit(f)
{
	
	if($('#gp_agr:checked').val() == 'n') {
		alert('약관에 동의하셔야 신청이 가능합니다');
		return false;
	}
	
	if (document.pressed == "장바구니") {
		f.sw_direct.value = 0;
	} else { // 바로구매
		f.sw_direct.value = 1;
	}

	// 판매가격이 0 보다 작다면
	if (document.getElementById("it_price").value < 0) {
		alert("전화로 문의해 주시면 감사하겠습니다.");
		return false;
	}

/*	옵션이 완성되면 사용예정
	if($(".sit_opt_list").size() < 1) {
		alert("상품의 선택옵션을 선택해 주십시오.");
		return false;
	}
*/
	
	var val, io_type, result = true;
	var sum_qty = 0;
	var min_qty = parseInt(it_buy_min_qty);
	var max_qty = parseInt(it_buy_max_qty);
	var $el_type = $("input[name^=io_type]");

	$("input[name^=ct_qty]").each(function(index) {
		val = $(this).val();

		if(val.length < 1) {
			alert("수량을 입력해 주십시오.");
			result = false;
			return false;
		}

		if(val.replace(/[0-9]/g, "").length > 0) {
			alert("수량은 숫자로 입력해 주십시오.");
			result = false;
			return false;
		}

		if(parseInt(val.replace(/[^0-9]/g, "")) < 1) {
			alert("수량은 1이상 입력해 주십시오.");
			result = false;
			return false;
		}

		io_type = $el_type.eq(index).val();
		if(io_type == "0")
			sum_qty += parseInt(val);
	});

	if(!result) {
		return false;
	}

	if(min_qty > 0 && sum_qty < min_qty) {
		alert("선택옵션 개수 총합 "+number_format(String(min_qty))+"개 이상 주문해 주십시오.");
		return false;
	}

	if(max_qty > 0 && sum_qty > max_qty) {
		alert("선택옵션 개수 총합 "+number_format(String(max_qty))+"개 이하로 주문해 주십시오.");
		return false;
	}

	return true;
}

//경매종료까지 남은 시간 구하기
function GetLeftTime(closeDate, serverNow)
{
	var str = "";
	var iDay = 0,iHour,	iMin;

	var iMinGap = (closeDate - serverNow) / (1000 * 60) ; //1초는 1000ms초
	var iSecGap = (closeDate - serverNow) / 1000 ; //1초는 1000ms초

	if(iMinGap < 0)
		str = "경매종료";
	else if(iMinGap == 0)
		str = "마감임박";
	else {
		//남은 일 구하기
		iDay= Math.floor(iMinGap/(60*24));

		//남은 분 구하기
		//21일,  10시간 33분 남은경우    남은일 * 24시간 * 60분 
		iSecGap = iSecGap - (iDay * 60 * 60 * 24);
		iHour = Math.floor(iSecGap / (60 * 60));

		iMin = Math.floor((iSecGap - (iHour * 60 * 60) ) / 60);
		iSec = Math.floor( iSecGap - (iHour * 60 * 60) - (iMin * 60) );

		if (iDay > 0) str = iDay.toString() + "일";
		if (iHour > 0) str = str + " " + iHour + "시간";
		if (iMin > 0) str = str + " " + iMin + "분";
		if (iSec >= 0) str = str + " " + iSec + "초";
	}

	return str;
}


/*남은시간 계산 후  출력*/
function changeItemLeftTime(){

	var nowTime = new Date();

	var leftTime = GetLeftTime(closeTime, nowTime);
	$('#itemlefttime').html(leftTime);

	if(leftTime == '경매종료') {
		$('.ac_btns').html('');
	}

	window.setTimeout(changeItemLeftTime,1000);
}