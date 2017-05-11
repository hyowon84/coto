<?php
//이파일 아님


if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

include_once(G5_PATH.'/head.sub.php');
include_once(G5_LIB_PATH.'/coinstoday.lib.php');
include_once(G5_LIB_PATH.'/latest.lib.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');
include_once(G5_LIB_PATH.'/poll.lib.php');
include_once(G5_LIB_PATH.'/visit.lib.php');
include_once(G5_LIB_PATH.'/connect.lib.php');
include_once(G5_LIB_PATH.'/popular.lib.php');

// 상단 파일 경로 지정 : 이 코드는 가능한 삭제하지 마십시오.
if ($config['cf_include_head']) {
    if (!@include_once($config['cf_include_head'])) {
        die('기본환경 설정에서 상단 파일 경로가 잘못 설정되어 있습니다.');
    }
    return; // 이 코드의 아래는 실행을 하지 않습니다.
}

if (G5_IS_MOBILE) {
    include_once(G5_MOBILE_PATH.'/shop/head.php');
    return;
}

$chk_url = basename($PHP_SELF);
?>

<!-- 아이프레임 높이 { -->
<link rel="stylesheet" type="text/css" href="<?=G5_URL?>/css/font-awesome.css" />
<link rel="stylesheet" type="text/css" href="<?=G5_URL?>/css/circle.css" />
<script src="/js/jquery.vticker.min.js"></script>
<script type="text/javascript" src="/js/moment-with-locales.js"></script>
<script type="text/javascript" src="/js/countdown.js"></script>
<script type="text/javascript" src="/js/moment-countdown.js"></script>
<script type="text/javascript" src="/js/common_product.js"></script>
<script language="JavaScript">
    $(function() {
        var req       = "<?php echo $_SERVER[REQUEST_URI] ?>";

        console.log("req:" + req);
    });

    function calcHeight()
    {
        //find the height of the internal page
        var the_height=
        document.getElementById('iframe').contentWindow.
        document.body.scrollHeight;

        //change the height of the iframe
        document.getElementById('iframe').height=the_height;
    }


</script>



<!-- 아이프레임 높이 끝{ -->
<script type="text/javascript">
jQuery(document).ready(function () {

	$mainScrollList = $('#mainScrollList img');
	$mainScrollContents = $("#mainScrollContents div.mainScrollTitle");

	$('#top_login').css({'height':($(document).height())+'px'});
    $(window).resize(function(){
        $('#top_login').css({'height':($(document).height())+'px'});
    });

	$mainScrollList.each(function (i) {
		$Item = $(this);

		$Item.css({ cursor: "pointer" });

		$Item.click(function () {
			$("html,body").animate({ scrollTop: $mainScrollContents.eq(i).position().top });
		});
	});


	<?
	if($_SESSION[quick_status] == "2"){
	?>
		$("#top_login").css("left", "101px");
	<?
	}else{
	?>
		//$("#top_login").css("left", "0");
	<?
	}
	?>



	// 통합배송 상세보기
	var layerWindow_com = $('.mw_layer_com');
	$('.layer_trigger_com').click(function(){
		layerWindow_com.addClass('open_com');

		var od_id = $(this).attr("od_id");
		var ct_type = $(this).attr("ct_type");

		$(".layer_view_com").html("");

		$.ajax({
			type : "POST",
			dataType : "HTML",
			url : "./_Ajax.combine_deli_view.php",
			data : "od_id=" + od_id + "&ct_type=" + ct_type,
			success : function(data){
				$(".layer_view_com").html(data);
			}
		});
	});
	$('#layer_com .close_com').click(function(){
		layerWindow_com.removeClass('open_com');
	});
	layerWindow_com.find('>.bg_com').mousedown(function(event){
		layerWindow_com.removeClass('open_com');
		return false;
	});

	// 마이페이지 상세보기
	var layerWindow_my = $('.mw_layer_my');
	$('.layer_trigger_my').click(function(){
		layerWindow_my.addClass('open_my');

		var T_od_id = $(this).attr("T_od_id");
		var status = $(this).attr("status");
		var dealer_status = $(this).attr("dealer_status");

		$(".layer_view_my").html("");

		if(dealer_status == "gp"){
			$.ajax({
				type : "POST",
				dataType : "HTML",
				url : "./_Ajax.mypage_view.php",
				data : "T_od_id=" + T_od_id + "&status=" + status,
				success : function(data){
					$(".layer_view_my").html(data);
				}
			});
		}else{
			$.ajax({
				type : "POST",
				dataType : "HTML",
				url : "./_Ajax.mypage_today_view.php",
				data : "T_od_id=" + T_od_id + "&status=" + status,
				success : function(data){
					$(".layer_view_my").html(data);
				}
			});
		}
	});
	$('#layer_my .close_my').click(function(){
		layerWindow_my.removeClass('open_my');
	});
	layerWindow_my.find('>.bg_my').mousedown(function(event){
		layerWindow_my.removeClass('open_my');
		return false;
	});



	var layerWindow = $('.mw_layer');
	$('.layer_trigger').click(function(){
		layerWindow.addClass('open');
	});
	$('#layer .close').click(function(){
		layerWindow.removeClass('open');
	});
	layerWindow.find('>.bg').mousedown(function(event){
		layerWindow.removeClass('open');
		return false;
	});



	// 마이페이지 통합배송조회
	var layerWindow_deli_info = $('.mw_layer_deli_info');
	$('.my_deli_info_bn').click(function(){
		layerWindow_deli_info.addClass('open_deli_info');

		var T_od_id = $(this).attr("T_od_id");
		var dt = $(this).attr("dt");
		var status = $(this).attr("status");

		$(".layer_view_deli_info").html("");

		if(status == "gp"){
			$.ajax({
				type : "POST",
				dataType : "HTML",
				url : "./_Ajax.mypage_deli_info.php",
				data : "T_od_id=" + T_od_id + "&dt=" + dt,
				success : function(data){
					$(".layer_view_deli_info").html(data);
				}
			});
		}else{
			$.ajax({
				type : "POST",
				dataType : "HTML",
				url : "./_Ajax.mypage_today_deli_info.php",
				data : "T_od_id=" + T_od_id + "&dt=" + dt,
				success : function(data){
					$(".layer_view_deli_info").html(data);
				}
			});
		}
	});
	$('#layer_deli_info .close_deli_info').click(function(){
		layerWindow_deli_info.removeClass('open_deli_info');
	});
	layerWindow_deli_info.find('>.bg_deli_info').mousedown(function(event){
		layerWindow_deli_info.removeClass('open_deli_info');
		return false;
	});

	// 마이페이지 투데이 배송조회
	/*
	var layerWindow_deli_info1 = $('.mw_layer_deli_info1');
	$('.my_deli_info_bn1').click(function(){
		layerWindow_deli_info1.addClass('open_deli_info1');

		var od_id = $(this).attr("od_id");
		var status = $(this).attr("status");

		$(".layer_view_deli_info1").html("");
		if(status == "gp"){
			$.ajax({
				type : "POST",
				dataType : "HTML",
				url : "./_Ajax.mypage_deli_info1.php",
				data : "od_id=" + od_id,
				success : function(data){
					$(".layer_view_deli_info1").html(data);
				}
			});
		}else{
			$.ajax({
				type : "POST",
				dataType : "HTML",
				url : "./_Ajax.mypage_today_deli_info1.php",
				data : "od_id=" + od_id,
				success : function(data){
					$(".layer_view_deli_info1").html(data);
				}
			});
		}
	});

	$('#layer_deli_info1 .close_deli_info1').click(function(){
		layerWindow_deli_info1.removeClass('open_deli_info1');
	});
	layerWindow_deli_info1.find('>.bg_deli_info1').mousedown(function(event){
		layerWindow_deli_info1.removeClass('open_deli_info1');
		return false;
	});

*/

	// 마이페이지 투데이 통합 > 배송조회
	/*var layerWindow_deli_info_com = $('.mw_layer_deli_info_com');
	$('.my_deli_info_bn_com').click(function(){
		layerWindow_deli_info_com.addClass('open_deli_info_com');

		var od_id = $(this).attr("od_id");
		var status = $(this).attr("status");

		$(".layer_view_deli_info_com").html("");
		if(status == "gp"){
			$.ajax({
				type : "POST",
				dataType : "HTML",
				url : "./_Ajax.mypage_deli_info_com.php",
				data : "od_id=" + od_id,
				success : function(data){
					$(".layer_view_deli_info_com").html(data);
				}
			});
		}else{
			$.ajax({
				type : "POST",
				dataType : "HTML",
				url : "./_Ajax.mypage_today_deli_info_com.php",
				data : "od_id=" + od_id,
				success : function(data){
					$(".layer_view_deli_info_com").html(data);
				}
			});
		}
	});
	$('#layer_deli_info_com .close_deli_info_com').click(function(){
		layerWindow_deli_info_com.removeClass('open_deli_info_com');
	});*/


	$(".quick_btn").click(function(){

		if($("#top_login").css("left") == "101px"){

			$("#top_login").animate({
				left: "-=101px"
			}, 500, function(){
				  $.ajax({
					type : "POST",
					url : "<?=G5_URL?>/bbs/_Ajax.quick.php",
					data : "",
					success : function(data){
					}
				});
			});

		}else{
			$("#top_login").animate({
				left: "+=101px"
			}, 500, function(){
				  $.ajax({
					type : "POST",
					url : "<?=G5_URL?>/bbs/_Ajax.quick.php",
					data : "",
					success : function(data){
					}
				});
			});
		}
	});

	$(".login_bn").click(function(){
		var f = $("form[name='flogin']");
		var url = f.find("input[name='url']").val();
		var mb_id = f.find("input[name='mb_id']").val();
		var mb_password = f.find("input[name='mb_password']").val();
		var auto_login = f.find("input[name='auto_login']").val();

		$.ajax({
			type: "POST",
			dataType: "HTML",
			url: "<?=G5_URL?>/bbs/_Ajax.login.php",
			data: "url=" + url + "&mb_id=" + mb_id + "&mb_password=" + mb_password + "&auto_login=" + auto_login,
			success: function(data){
				if(data == "err"){
					alert("회원아이디나 비밀번호가 공백이면 안됩니다.");
				}else if(data == "err1"){
					alert("가입된 회원아이디가 아니거나 비밀번호가 틀립니다.\n비밀번호는 대소문자를 구분합니다.");
				}else if(data == "err2"){
					alert("회원님의 아이디는 접근이 금지되어 있습니다.");
				}else if(data == "err3"){
					alert("탈퇴한 아이디이므로 접근하실 수 없습니다.");
				}else{
					goto_url(data);
				}
			}
		});
	});

	$("#login_id, #login_pw").keyup(function(e){

		if(e.keyCode == 13){
			var f = $("form[name='flogin']");
			var url = f.find("input[name='url']").val();
			var mb_id = f.find("input[name='mb_id']").val();
			var mb_password = f.find("input[name='mb_password']").val();
			var auto_login = f.find("input[name='auto_login']").val();

			$.ajax({
				type: "POST",
				dataType: "HTML",
				url: "<?=G5_URL?>/bbs/_Ajax.login.php",
				data: "url=" + url + "&mb_id=" + mb_id + "&mb_password=" + mb_password + "&auto_login=" + auto_login,
				success: function(data){
					if(data == "err"){
						alert("회원아이디나 비밀번호가 공백이면 안됩니다.");
					}else if(data == "err1"){
						alert("가입된 회원아이디가 아니거나 비밀번호가 틀립니다.\n비밀번호는 대소문자를 구분합니다.");
					}else if(data == "err2"){
						alert("회원님의 아이디는 접근이 금지되어 있습니다.");
					}else if(data == "err3"){
						alert("탈퇴한 아이디이므로 접근하실 수 없습니다.");
					}else{
						goto_url(data);
					}
				}
			});
		}
	});

	//포트폴리오
	$(".port_submit").click(function(){
		var chk = false;
		var chk1;
		var data;

		$("input:checkbox[name='port_chk[]']").each(function(i){
			if($("input:checkbox[name='port_chk[]']").eq(i).attr("checked") == "checked") chk = true;
		});

		$("input:checkbox[name='port_chk[]']").each(function(i){

			if(chk == false){
				alert("1개 이상 체크 하시기 바랍니다.");
				return false;
			}


			if($("input:checkbox[name='port_chk[]']").eq(i).attr("checked") == "checked"){

				chk1 = false;

				if($("input:text[name='port_title[]']").eq(i).val() == ""){
					alert("제목을 입력하세요.");
					$("input:text[name='port_title[]']").eq(i).focus();
					return false;
				}else if($("input:text[name='port_cnt[]']").eq(i).val() == ""){
					alert("수량을 입력하세요.");
					$("input:text[name='port_cnt[]']").eq(i).focus();
					return false;
				}else if($("input:text[name='port_oz[]']").eq(i).val() == ""){
					alert("온스를 입력하세요.");
					$("input:text[name='port_oz[]']").eq(i).focus();
					return false;
				}else if($("input:text[name='port_buy[]']").eq(i).val() == ""){
					alert("구입가를 입력하세요.");
					$("input:text[name='port_buy[]']").eq(i).focus();
					return false;
				}
				chk1 = true;
			}
		});

		if(chk1 == true){

			$(".getcon").find("td").each(function(i){
				data += $(this).text() + ",";
			});
			data = data.substr(0, data.length-1);
			var arr = data.split(",");

			//alert(arr[3]);
			//return false;

			if(confirm("등록 하시겠습니까?")){
				$("form[name='fportfolio']").attr({"action":"<?=G5_URL?>/portfolio/portfolio_update.php"}).submit();
			}
		}
	});

	$("input:checkbox[name='item_all_chk']").click(function(){

		if($("input:checkbox[name='item_all_chk']").attr("checked") == "checked"){
			$("input:checkbox[name='port_chk[]']").attr("checked", true);
		}else{
			$("input:checkbox[name='port_chk[]']").attr("checked", false);
		}
	});

});
</script>





<script type="text/javascript">
$(function() {
	//We initially hide the all dropdown menus
	$('#dropdown_nav li').find('.sub_nav').hide();

	//When hovering over the main nav link we find the dropdown menu to the corresponding link.
	$('#dropdown_nav li').hover(function() {
		//Find a child of 'this' with a class of .sub_nav and make the beauty fadeIn.
		$(this).find('.sub_nav').fadeIn(100);
	}, function() {
		//Do the same again, only fadeOut this time.
		$(this).find('.sub_nav').fadeOut(50);
	});
});
</script>


<!-- 우측 { -->


<!-- 상단 시작 { -->

<?php
if($_SERVER[REQUEST_URI] == "/") { // index에서만 실행
	include G5_BBS_PATH.'/newwin.inc.php'; // 팝업레이어
}
?>

<div class="mw_layer">
	<div class="bg"></div>

	<div id="layer">
	<div style="width:100%;font-size:17px;text-align:right"><a href="#" class="close" style="padding-right:5px">X</a></div>

	<div>
	<?
	include_once($member_skin_path.'/login.skin1.php');
	?>
	</div>

	</div>
</div>



<?if($bo_table == "portfolio"){?>
<div class="mw_layer_po">

	<div class="getcon"><table><tr><?=get_currency1(2)?></tr></table></div>

	<div class="bg_po"></div>
	<div id="layer_po">
	<div style="width:100%;font-size:17px;text-align:right">
		<div style="float:left;padding:15px;font-size:18px;font-weight:bold;color:#000;">아이템추가</div>
		<div style="float:right;"><a href="#" class="close_po" style="padding-right:5px">X</a></div>
	</div>

	<?
	include_once("../inc/portfolio_add_inc.php");
	?>

	</div>

</div>

<div class="mw_layer_po_modify">

	<div class="bg_po_modify"></div>
	<div id="layer_po_modify">
	<div style="width:100%;font-size:17px;text-align:right">
		<div style="float:left;padding:15px;font-size:18px;font-weight:bold;color:#000;">아이템수정</div>
		<div style="float:right;"><a href="#" class="close_po_modify" style="padding-right:5px">X</a></div>
	</div>

	<div class="po_con"></div>

	</div>

</div>

<?}?>

<div class="mw_layer_com">
	<div class="bg_com"></div>

	<div id="layer_com">
		<div class="layer_view_com">
			<!-- 내용 시작 -->
			<!-- 내용 끝 -->
		</div>
		<div style="margin:50px 0 0 0;width:100%;text-align:center"><a href="javascript:void(0);" class="close_com" style="padding-right:5px"><img src="<?=G5_URL?>/img/buy_combine_deli_close_bn.gif"></a></div>
	</div>
</div>

<div class="mw_layer_my">
	<div class="bg_my"></div>

	<div id="layer_my">
		<div class="layer_view_my">
			<!-- 내용 시작 -->
			<!-- 내용 끝 -->
		</div>
		<div style="margin:20px 0 0 0;width:100%;text-align:center"><a href="javascript:void(0);" class="close_my" style="padding-right:5px"><img src="<?=G5_URL?>/img/buy_combine_deli_close_bn.gif"></a></div>
	</div>
</div>

<div class="mw_layer_deli_info">
	<div class="bg_deli_info"></div>

	<div id="layer_deli_info">
		<div class="layer_view_deli_info">
			<!-- 내용 시작 -->
			<!-- 내용 끝 -->
		</div>
		<div style="margin:20px 0 0 0;width:100%;text-align:center"><a href="javascript:void(0);" class="close_deli_info" style="padding-right:5px"><img src="<?=G5_URL?>/img/buy_combine_deli_close_bn.gif"></a></div>
	</div>
</div>

<div class="mw_layer_deli_info1">
	<div class="bg_deli_info1"></div>

	<div id="layer_deli_info1">
		<div class="layer_view_deli_info1">
			<!-- 내용 시작 -->
			<!-- 내용 끝 -->
		</div>
		<div style="margin:20px 0 0 0;width:100%;text-align:center"><a href="javascript:void(0);" class="close_deli_info1" style="padding-right:5px"><img src="<?=G5_IMG_URL?>/deli_info_close_bn.gif"></a></div>
	</div>
</div>

<div class="mw_layer_deli_info_com">
	<div id="layer_deli_info_com">
		<div class="layer_view_deli_info_com">
			<!-- 내용 시작 -->
			<!-- 내용 끝 -->
		</div>
		<div style="margin:20px 0 0 0;width:100%;text-align:center"><a href="javascript:void(0);" class="close_deli_info_com" style="padding-right:5px"><img src="<?=G5_IMG_URL?>/deli_info_close_bn.gif"></a></div>
	</div>
</div>


<div style="position:fixed;width:100%;z-index:100000;">
	<div id="top_login">
		<div class="quick" style="width:100%;">

	<?
	if($member[mb_id] != ""){
	?>

			<div style="padding:25px 19px 25px 19px;">
				<div style="width:50px;padding:10px;color:#fff;background:#000;text-align:center;cursor:pointer;">검색</div>
			</div>
			<div style="padding:17px 26px 17px 26px;background:#3b3c44;border-bottom:1px #27262c solid;cursor:pointer;" onclick="goto_url('<?=G5_BBS_URL?>/logout.php');">
				<div style="width:50px;color:#fff;text-align:center;">
					<img src="<?=G5_URL?>/img/quick_logout_icon.gif" border="0" align="absmiddle">
				</div>
				<div style="width:50px;color:#56ccc8;text-align:center;margin:5px 0 0 0;font-weight:bold;">
					로그아웃
				</div>
			</div>

			<div style="padding:7px 0 10px 0;color:#fff;text-align:center;cursor:pointer;" onclick="goto_url('<?=G5_BBS_URL?>/member_confirm.php?url=/bbs/register_form.php');">
				회원정보
			</div>

			<!--
			<div style="padding:19px 20px 19px 20px;background:#27262E;border-top:1px solid #32323A;border-bottom:1px solid #1B1B1B;cursor:pointer;" onclick="goto_url('<?=G5_URL?>/portfolio/portfolio.php');">
				<div style="width:60px;color:#fff;text-align:center;">
					<img src="<?=G5_URL?>/img/quick_portfolio_icon_o.gif" border="0" align="absmiddle">
				</div>
				<div style="width:60px;color:#56ccc8;text-align:center;margin:5px 0 0 0;font-weight:bold;">
					포트폴리오
				</div>
			</div>
			<div style="padding:19px 20px 19px 20px;background:#27262E;border-top:1px solid #32323A;border-bottom:1px solid #1B1B1B;cursor:pointer;" onclick="goto_url('<?=G5_SHOP_URL?>/wishlist_gp.php');">
				<div style="width:60px;color:#fff;text-align:center;">
					<img src="<?=G5_URL?>/img/quick_wish_icon_o.gif" border="0" align="absmiddle">
				</div>
				<div style="width:60px;color:#56ccc8;text-align:center;margin:5px 0 0 0;font-weight:bold;">
					위시리스트
				</div>
			</div>
			<div style="padding:19px 20px 19px 20px;border-top:1px solid #32323A;">
				<div style="width:60px;color:#fff;text-align:center;">
					<img src="<?=G5_URL?>/img/timer.gif" border="0" align="absmiddle">
				</div>
				<div style="width:60px;color:#56ccc8;text-align:center;margin:5px 0 0 0;font-weight:bold;">
					실시간<br>주문현황
				</div>
			</div>

			<div style="padding:7px 0 0 0;color:#fff;text-align:center;cursor:pointer;" onclick="goto_url('<?=G5_SHOP_URL?>/cart_gp.php');">
				공동구매
			</div>
			<div style="padding:7px 0 0 0;color:#fff;text-align:center;cursor:pointer;">
				구매대행
			</div>
			<div style="padding:7px 0 0 0;color:#fff;text-align:center;cursor:pointer;">
				배송대행
			</div>
			<div style="padding:35px 0 0 0;color:#fff;text-align:center;cursor:pointer;" onclick="goto_url('<?=G5_SHOP_URL?>/realtime_gp.php');">
				<img src="<?=G5_URL?>/img/realtime_icon.png" border="0" align="absmiddle">
			</div>
			 -->

	<?
	}
	else{
	?>

			<div style="padding:19px 26px 19px 26px;background:#3b3c44;border-bottom:1px #27262c solid;cursor:pointer;" class="layer_trigger">
				<div style="width:50px;color:#fff;text-align:center;">
					<img src="<?=G5_URL?>/img/quick_log_icon.gif" border="0" align="absmiddle">
				</div>
				<div style="width:50px;color:#fff;text-align:center;margin:5px 0 0 0;font-weight:bold;">
					로그인
				</div>
			</div>

			<div style="padding:10px 26px 10px 26px;background:#3b3c44;cursor:pointer;" onclick="goto_url('<?=G5_BBS_URL?>/register.php');">
				<div style="width:50px;text-align:center;color:#56ccc8;font-weight:bold;">
					회원가입
				</div>
			</div>

	<?
	}
	?>

			<div style="padding:10px 20px 0px 10px;cursor:pointer;" onclick="goto_url('/coto/check_order.php');">
				<div style="width:80px;text-align:center;color:#56ccc8;font-weight:bold;">
					주문조회
				</div>
			</div>

			<div style="padding:19px 20px 19px 20px;background:#27262E;border-top:1px solid #32323A;border-bottom:1px solid #1B1B1B;cursor:pointer;" onclick="goto_url('/coto/cart.php');">
				<div style="width:60px;color:#fff;text-align:center;">
					<img src="<?=G5_URL?>/img/quick_cart_icon_o.gif" border="0" align="absmiddle">
				</div>
				<div style="width:60px;color:#56ccc8;text-align:center;margin:5px 0 0 0;font-weight:bold;">
					장바구니
				</div>
			</div>


		</div>
	</div>
</div>


<div id="hd">


<div id="top_right">


	<div id="top_right_wrap">
		<fieldset id="hd_sch">
			<div id="login">
				 <ul id="tnb2">

					<?php if ($is_member) {  ?>

					<?php if ($is_admin) {  ?>
					<li><a href="<?=G5_ADMIN_URL?>/shop_admin/"><b>관리자</b></a></li>
					<?php }  ?>

					<!--<li>&nbsp;<a href="<?php// echo G5_BBS_URL ?>/member_confirm.php?url=<?php// echo G5_BBS_URL ?>/register_form.php">정보수정</a></li>
					<li>&nbsp;<a href="<?php// echo G5_BBS_URL ?>/logout.php">로그아웃</a></li>-->
					<?php }// else {  ?>
					<!--<li>&nbsp;<a href="<?php// echo G5_BBS_URL ?>/register_form.php">회원가입</a></li>
					<li>&nbsp;<a href="<?php// echo G5_BBS_URL ?>/login.php"><b>로그인</b></a></li>
					<li>&nbsp;<a href=# class="layer_trigger"><b>로그인</b></a></li>-->
					<?php// }  ?>
				</ul>
			</div>


			<div style="padding:0 25px 0 0;text-align:right;margin:0 85px 0 0;">
				<!--<legend>사이트 내 전체검색</legend>
				<form name="fsearchbox" method="get" action="<?php// echo G5_BBS_URL ?>/search.php" onsubmit="return fsearchbox_submit(this);">
				<input type="hidden" name="sfl" value="wr_subject||wr_content">
				<input type="hidden" name="sop" value="and">
				<label for="sch_stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
				<input type="text" name="stx" id="sch_stx" maxlength="20">
				<input type="image" src="<?php// echo G5_URL ?>/img/new_search.png" id="sch_submit" value="검색" style="padding:0;margin:0;width:60px;height:26px">
				</form>-->
			</div>

            <script>
            function fsearchbox_submit(f)
            {
                if (f.stx.value.length < 2) {
                    alert("검색어는 두글자 이상 입력하십시오.");
                    f.stx.select();
                    f.stx.focus();
                    return false;
                }

                // 검색에 많은 부하가 걸리는 경우 이 주석을 제거하세요.
                var cnt = 0;
                for (var i=0; i<f.stx.value.length; i++) {
                    if (f.stx.value.charAt(i) == ' ')
                        cnt++;
                }

                if (cnt > 1) {
                    alert("빠른 검색을 위하여 검색어에 공백은 한개만 입력할 수 있습니다.");
                    f.stx.select();
                    f.stx.focus();
                    return false;
                }

                return true;
            }
            </script>


			<script type="text/javascript" src="<?=G5_URL?>/js/jquery.fixedMenu.js"></script>
			<link rel="stylesheet" type="text/css" href="<?=G5_URL?>/css/fixedMenu_style1.css" />
				<script>
        //$('document').ready(function(){
        //    $('.menu2').fixedMenu();
        //});
        </script>

			<div id="gnb">
				 <div class="menu2">
					<ul>


						<li>
							<a href="<?=G5_URL?>/guide1.php">Coin`s Today<!--<span class="arrow"></span>--></a>

							<!--
							<ul>
								<li><a href="#">회사소개</a></li>
								<li><a href="#">서비스 가이드</a></li>
								<li><a href="#">Membership 안내</a></li>

							</ul>
							-->

						</li>
						<li>
							<a href="<?=G5_URL?>/news/main.php">News<!--<span class="arrow"></span>--></a>

							<!--
							<ul>
								<li><a href="www.htmldrive.net">국제시세</a></li>
								<li><a href="www.htmldrive.net">국내시세</a></li>
								<li><a href="www.htmldrive.net">금속 관련 시세</a></li>
								<li><a href="www.htmldrive.net">금화,은화 관련 시세</a></li>
								<li><a href="www.htmldrive.net">돈버는 뉴스&정보</a></li>

							</ul>
							-->

						</li>
						<li>
							<a href="<?=G5_URL?>/community/index.php">Community<!--<span class="arrow"></span>--></a>

							<!--
							<ul>
								<li><a href="http://www.twitter.com/htmldrive">정보&지식 공유</a></li>
								<li><a href="#">내 코인 자랑</a></li>
								<li><a href="#">자유수다방</a></li>
							</ul>
							-->

						</li>
						<li>
							<a href="<?=G5_URL?>/customer/index.php">Customer<!--<span class="arrow"></span>--></a>

							<!--
							<ul>
								<li><a href="www.htmldrive.net">공지사항</a></li>
								<li><a href="www.htmldrive.net">건의사항</a></li>
								<li><a href="www.htmldrive.net">이벤트</a></li>
								<li><a href="www.htmldrive.net">제휴문의</a></li>
								<li><a href="www.htmldrive.net">Q&A</a></li>

							</ul>
							-->

						</li>
					</ul>
				</div>
			</div>


        </fieldset>

	</div>

</div>

 <div id="logo">
	<a href="<?=G5_URL?>"><img src="<?=G5_URL?>/img/logo_new.png" ></a>
</div>

<div class="result">
<!-- 실시간 시세 -->
<?
include_once(G5_GOLDSPOT_PATH."/goldspot_search.php");
include_once(G5_GOLDSPOT_PATH."/goldspot_search_header.php");
?>
</div>

<h1 id="hd_h1"><?php echo $g5['title'] ?></h1>

<div id="skip_to_container"><a href="#container">본문 바로가기</a></div>

	<!--
    <div id="hd_wrapper">
        <div id="text_size">
            font_resize('엘리먼트id', '제거할 class', '추가할 class');
            <button id="size_down" onclick="font_resize('container', 'ts_up ts_up2', '');"><img src="<?php echo G5_URL; ?>/img/ts01.gif" alt="기본"></button>
            <button id="size_def" onclick="font_resize('container', 'ts_up ts_up2', 'ts_up');"><img src="<?php echo G5_URL; ?>/img/ts02.gif" alt="크게"></button>
            <button id="size_up" onclick="font_resize('container', 'ts_up ts_up2', 'ts_up2');"><img src="<?php echo G5_URL; ?>/img/ts03.gif" alt="더크게"></button>
        </div>
    </div>
	-->

    <hr>

	<div id="line"></div>
</div>
<!-- } 상단 끝 -->

<hr>

<!-- 콘텐츠 시작 { -->

<script type="text/javascript">
	$(function() {

		/* 실시간 시세 */
 		loadGoldspot();

		//graceful degradation

		$(".store_close").click(function(){
			$('#ui_element').find("ul").css("display", "none");
		});

		<?if($_SERVER[REQUEST_URI] != "/index.php" && $_SERVER[REQUEST_URI] != "/" && strpos($_SERVER[REQUEST_URI], "customer") !== false || $_SERVER[REQUEST_URI] == "/guide1.php" || $_SERVER[REQUEST_URI] == "/guide2.php" || $_SERVER[REQUEST_URI] == "/guide3.php" || $_SERVER[REQUEST_URI] == "/news/main.php" || $bo_table == "community" || $bo_table == "event" || $bo_table == "FAQ" || $bo_table == "suggest" || $bo_table == "notice" || $bo_table == "new_family" || $bo_table == "free_talk" || $bo_table == "best" || strpos($_SERVER[REQUEST_URI], "community") !== false || $bo_table == "free" || $bo_table == "comm_suggest" || $bo_table == "photo_gallery" || $bo_table == "bestshot" || strpos($_SERVER[REQUEST_URI], "/portfolio.php") !== false || stristr($_SERVER[REQUEST_URI], "/orderinquiry") == true || strpos($_SERVER[REQUEST_URI], "/wishlist") !== false || strpos($_SERVER[REQUEST_URI], "/member_confirm1") !== false || $bo_table=="essey" || $bo_table == "know" || $bo_table == "news_gold_economy" || $bo_table == "news_gold_sise" || $bo_table == "news_silver_news" || $bo_table == "news_info" || $bo_table == "portfolio" || stristr($_SERVER[REQUEST_URI], "itemuselist")){?>

		$('#ui_element').find('ul').hide();
		$('#ui_element').find('.m_itemMain').addClass('m_down').removeClass('m_up');

		$('#ui_element').find('.m_itemMain').click(function(){

			if($('#ui_element').find("ul").css("display") == "none"){
				$('#ui_element').find('.m_itemMain').addClass('m_up').removeClass('m_down');
				$('#ui_element').find("ul").css("display", "block");
			}else{
				$('#ui_element').find('.m_itemMain').addClass('m_down').removeClass('m_up');
				$('#ui_element').find("ul").css("display", "none");
			}

		});
		<?}else{?>
		$('#ui_element').find('.m_itemMain').toggle(
			function(){
				var $this 	= $(this);
				$this.addClass('m_down').removeClass('m_up');
				var $menu	= $this.next();
				var t = 10;
				$($menu.find('li').get()).each(function(){
					var $li = $(this);
					var showmenu = function(){$li.hide();};
					setTimeout(showmenu,t+=20);
				});
			},
			function(){
				var $this 	= $(this);
				$this.addClass('m_up').removeClass('m_down');
				var $menu	= $this.next();
				var t = 10;
				$($menu.find('li').get()).each(function(){
					var $li = $(this);
					var hidemenu = function(){$li.show();};
					setTimeout(hidemenu,t+=20);
				});

			}
		);
		<?}?>
	});
</script>


<div id="wrapper">
	<div id="aside">
			<div class="content">
				<div class="box">
					<div id="ui_element" class="m_wrapper">

						<div class="m_itemMain m_up" idx="2">카테고리</div>

							<!-- PC 화면 -->
							<ul>
								<li id="menu_title" style="cursor:pointer" onclick="document.location.href='<?=G5_SHOP_URL?>/auclist.php'">오늘의 경매 <font color=white>&nbsp;&nbsp;&nbsp;☜ 전체보기</font></li>
								
								<li id="menu_title" style="cursor:pointer" onclick="">진행중인 공동구매</li>
								<?=createGpCategoryMenu();?>
								<li id="menu_line"><img src="<?=G5_URL?>/img/menu_line.png"></li>

								<li id="menu_title" style="cursor:pointer" onclick="location.href='<?=G5_SHOP_URL?>/gplist.php?ca_id=CT'">코인스투데이</li>
								<?=getCotoCategory()?>

								<li id="menu_line"><img src="<?=G5_URL?>/img/menu_line.png"></li>

								<?
								/*
								<li id="menu_title">개별오더신청</li>
								<li><a href="<?=G5_SHOP_URL?>/gplist.php?ca_id=AP">APMEX</a></li>
								<li><a href="<?=G5_SHOP_URL?>/gplist.php?ca_id=GV">GAINSVILLE</a></li>
								<li><a href="<?=G5_SHOP_URL?>/gplist.php?ca_id=MC">MCM</a></li>
								<li><a href="<?=G5_SHOP_URL?>/gplist.php?ca_id=PM">PARADISE MINT</a></li>
								<!-- li><a href="<?=G5_SHOP_URL?>/gplist.php?ca_id=SD">Scotts Dale</a></li-->
								<li><a href="<?=G5_SHOP_URL?>/gplist.php?ca_id=OD">Other Dealer</a></li>
								<li id="menu_line"><img src="<?=G5_URL?>/img/menu_line.png"></li>
								*/
								?>


								<!-- 1차 오픈에서 제외
								<li id="menu_title">구매대행</li>
								<li><a href="<?=G5_URL?>/agency/purchase_ebay_list.php">Ebay</a></li>
								<li><a href="#">Amazon</a></li>
								<li id="menu_line"><img src="<?=G5_URL?>/img/menu_line.png"></li>
								-->

								<!-- 1차 오픈에서 제외
								<li id="menu_title">배송대행</li>
								<li><a href="<?=G5_URL?>/agency/shipping_request.php">신청하기</a></li>
								<li id="menu_line"><img src="<?=G5_URL?>/img/menu_line.png"></li>

								<li id="menu_title">그레이딩대행</li>
								<li><a href="#" onclick="alert('준비중입니다.'); return false;">PCGS</a></li>
								<li><a onclick="alert('준비중입니다.'); return false;">NGC</a></li>
								<li id="menu_line"><img src="<?=G5_URL?>/img/menu_line.png"></li>

								<li id="menu_title" style="cursor:pointer" onclick="alert('준비중입니다.'); return false;">위탁판매대행</li>
								<li id="menu_line"><img src="<?=G5_URL?>/img/menu_line.png"></li>
								-->

								<li id="menu_title" style="cursor:pointer;height:40px;line-height:1.2;margin-top:7px;" onclick="goto_url('<?=G5_URL?>/sec_lock.php');">실물투자 컨설팅 &</br>보안금고</li>
								<li id="menu_line"><img src="<?=G5_URL?>/img/menu_line.png"></li>

								<!--<li style="padding-top:0px;padding-left:0px">
									<img src="<?=G5_URL?>/img/banner.jpg" style="height:120px;width:100%">
								</li>-->
							</ul>
						</div>
	<!--		</div> -->

					<?if(strpos($_SERVER[REQUEST_URI], "/news/main.php") !== false || $bo_table == "news_gold_sise" || $bo_table == "news_gold_economy" || $bo_table == "news_silver_news" || $bo_table == "news_info"){?>

					<div id="sub_cate" style="z-index:-1;">
						<h1 style="cursor:pointer;" onclick="goto_url('<?=G5_URL?>/news/main.php');">News</h1>
						<ul>

							<li><a href="<?=G5_URL?>/bbs/board.php?bo_table=news_gold_sise" <?php if($bo_table=="news_gold_sise") echo " class='sub_cate_on'";?> >금속시세</a></li>
							<li><a href="<?=G5_URL?>/bbs/board.php?bo_table=news_gold_economy" <?php if($bo_table=="news_gold_economy") echo " class='sub_cate_on'";?>>금속 경제 뉴스</a></li>
							<li><a href="<?=G5_URL?>/bbs/board.php?bo_table=news_silver_news" <?php if($bo_table=="news_silver_news") echo " class='sub_cate_on'";?>>금화 은화 뉴스</a></li>
							<li><a href="<?=G5_URL?>/bbs/board.php?bo_table=news_info" <?php if($bo_table=="news_info") echo " class='sub_cate_on'";?>>돈버는 뉴스 & 정보</a></li>

						</ul>
					</div>

					<?}else if(strpos($_SERVER[REQUEST_URI], "/guide1.php") !== false || strpos($_SERVER[REQUEST_URI], "/guide2.php") !== false || strpos($_SERVER[REQUEST_URI], "/guide3.php") !== false || strpos($_SERVER[REQUEST_URI], "/guide4.php") !== false || strpos($_SERVER[REQUEST_URI], "/guide5.php") !== false || strpos($_SERVER[REQUEST_URI], "/guide6.php") !== false){?>

					<div id="sub_cate" style="z-index:-1;">
						<h1>About</h1>
						<ul>
							<li><a href="<?=G5_URL?>/guide1.php" <?php if(strpos($_SERVER[REQUEST_URI], "/guide1.php") !== false) echo " class='sub_cate_on'";?> >회사소개</a></li>
							<li><a href="<?=G5_URL?>/guide2.php" <?php if(strpos($_SERVER[REQUEST_URI], "/guide2.php") !== false) echo " class='sub_cate_on'";?>>서비스가이드</a></li>
							<li><a onclick="alert('준비중입니다.'); return false;" <?php if(strpos($_SERVER[REQUEST_URI], "/guide3.php") !== false) echo " class='sub_cate_on'";?>>Membership 안내</a></li>
						</ul>
					</div>

					<?}else if(stristr($_SERVER[REQUEST_URI], "portfolio") || stristr($_SERVER[REQUEST_URI], "/orderinquiry") == true || strpos($_SERVER[REQUEST_URI], "/wishlist") !== false || strpos($_SERVER[REQUEST_URI], "/member_confirm1") !== false || stristr($_SERVER[REQUEST_URI], "itemuselist")){?>

					<div id="sub_cate" style="z-index:-1;">
						<div style="margin:60px 0 0 0;text-align:center;">
						<?php
						if($member[mb_img]){
						?>
							<img src="<?=G5_URL?>/data/member/<?php echo $member[mb_img]; ?>" border="0" align="absmiddle" width="140px" height="140px">
						<?php }else{ ?>
							<img src="<?=G5_URL?>/img/po_profile_no.gif" border="0" align="absmiddle" width="140px" height="140px">
						<?php } ?>
						</div>

						<div style="margin:10px 0 0 0;text-align:center;font-size:20px;font-weight:bold;">
							<?php echo $member[mb_name]; ?>
						</div>

						<div style="margin:5px 0 0 0;text-align:center;">
							닉네임 / <?php echo $member[mb_nick]; ?>
						</div>

						<div style="margin:15px 0 0 0;text-align:center;color:#898989;cursor:pointer;" onclick="goto_url('<?php echo G5_URL; ?>/bbs/member_confirm.php?url=/bbs/register_form.php');">
							프로필수정 ▶
						</div>
					</div>

					<?}else if($bo_table == "notice" || $bo_table == "suggest" || $bo_table == "FAQ" || $bo_table == "event" || strpos($_SERVER[REQUEST_URI], "/alliance.php") !== false || strpos($_SERVER[REQUEST_URI], "customer") !== false){?>

					<div id="sub_cate" style="z-index:-1;">
						<h1 style="cursor:pointer;" onclick="goto_url('<?=G5_URL?>/customer/index.php');">Customer</h1>
						<ul>

							<li><a href="<?=G5_URL?>/bbs/board.php?bo_table=notice" <?php if($bo_table=="notice") echo " class='sub_cate_on'";?> >공지사항</a></li>
							<li><a href="<?=G5_URL?>/bbs/board.php?bo_table=FAQ" <?php if($bo_table=="FAQ") echo " class='sub_cate_on'";?>>FAQ</a></li>
							<li><a href="<?=G5_URL?>/bbs/board.php?bo_table=suggest" <?php if($bo_table=="suggest") echo " class='sub_cate_on'";?>>건의사항</a></li>
							<li><a href="<?=G5_URL?>/bbs/board.php?bo_table=event" <?php if($bo_table=="event") echo " class='sub_cate_on'";?>>이벤트</a></li>
							<li><a href="<?=G5_URL?>/customer/alliance.php" <?php if($bo_table=="alliance") echo " class='sub_cate_on'";?>>제휴문의</a></li>

						</ul>
					</div>

					<?}else if($bo_table == "new_family" || $bo_table == "free_talk" || $bo_table == "best" || $bo_table == "free" || strpos($_SERVER[REQUEST_URI], "community") !== false || $bo_table == "comm_suggest" || $bo_table == "photo_gallery" || $bo_table == "bestshot" || $bo_table == "essey" || $bo_table == "know"){?>

					<div id="sub_cate" style="z-index:-1;">
						<h1 style="cursor:pointer;" onclick="goto_url('<?=G5_URL?>/community/index.php');">Community</h1>
						<h1 style="font-size:17px;margin:10px 0 0 0;font-weight:bold">WE</h1>
						<ul>

							<li><a href="<?=G5_URL?>/bbs/board.php?bo_table=new_family" <?php if($bo_table=="new_family") echo " class='sub_cate_on'";?> >새로운 가족</a></li>
							<li><a href="<?=G5_URL?>/bbs/board.php?bo_table=free_talk" <?php if($bo_table=="free_talk") echo " class='sub_cate_on'";?>>프리톡</a></li>

						</ul>

						<h1 style="font-size:17px;font-weight:bold">CREATE & SHARE</h1>
						<ul>
							<li><a href="<?=G5_URL?>/bbs/board.php?bo_table=best" <?php if($bo_table=="best") echo " class='sub_cate_on'";?> >베스트게시물</a></li>
							<li><a href="<?=G5_URL?>/bbs/board.php?bo_table=free" <?php if($bo_table=="free") echo " class='sub_cate_on'";?>>자유게시판</a></li>
							<li><a href="<?=G5_URL?>/bbs/board.php?bo_table=essey" <?php if($bo_table=="essey") echo " class='sub_cate_on'";?>>코인수집에세이</a></li>
							<li><a href="<?=G5_URL?>/bbs/board.php?bo_table=know" <?php if($bo_table=="know") echo " class='sub_cate_on'";?>>코인지식인</a></li>
							<li><a href="<?=G5_URL?>/community/photo.php" <?php if(strpos($_SERVER[REQUEST_URI], "community")) echo " class='sub_cate_on'";?>>갤러리</a></li>
							<li><a href="<?=G5_URL?>/bbs/board.php?bo_table=bestshot" <?php if($bo_table=="bestshot") echo " class='sub_cate_on'";?>>· 베스트샷</a></li>
							<li><a href="<?=G5_URL?>/bbs/board.php?bo_table=photo_gallery" <?php if($bo_table=="photo_gallery") echo " class='sub_cate_on'";?>>· 포토갤러리</a></li>
							<li><a href="<?=G5_URL?>/bbs/board.php?bo_table=comm_suggest" <?php if($bo_table=="comm_suggest") echo " class='sub_cate_on'";?>>건의사항</a></li>
						</ul>
					</div>

					<?}?>

				</div>
			</div>

			<!--<ul id="menu">
			<li>
				<a href="#">전체카테고리 <img src="<?=G5_URL?>/img/bn_img1.png" alt="thumb06" /></a>
				<ul>
					<li><a href="<?=G5_SHOP_URL?>/list.php?ca_id=1010">· Gold</a></li>
					<li><a href="<?=G5_SHOP_URL?>/list.php?ca_id=1020">· Silver</a></li>
					<li><a href="<?=G5_SHOP_URL?>/list.php?ca_id=1030">· Other Metal</a></li>
					<li><a href="#">· Aution</a></li>
				</ul>
			</li>-->

			<!--<li style="padding-top:0px;padding-left:0px">
				<img src="<?=G5_URL?>/img/banner.jpg">
			</li>
			<li style="padding-top:10px;padding-left:6px">
				<img src="<?=G5_URL?>/img/side_img2.png">
			</li>
		</ul>-->
	</div>


</div>

<!-- 메인 프레임 -->
<div style='display:block; width:100%; height:auto; float:left; '>

<div id="container">

		<?
			if(strpos($_SERVER[REQUEST_URI], "sec_lock.php") !== false) {
		?>
			<div id="content" style="width:1070px;background:#fff;">
		<?}
			else
			{
		?>
			<div id="content" style="">
		<?
			}
?>