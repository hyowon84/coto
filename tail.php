<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 하단 파일 경로 지정 : 이 코드는 가능한 삭제하지 마십시오.
if ($config['cf_include_tail']) {
    if (!@include_once($config['cf_include_tail'])) {
        die('기본환경 설정에서 하단 파일 경로가 잘못 설정되어 있습니다.');
    }
    return; // 이 코드의 아래는 실행을 하지 않습니다.
}
?>

<script>

//장바구니담기 함수
function cart_add(mode,it_id,gpcode) {
	var it_qty = $('#'+it_id+'_qty').val();

	$.ajax({
		dataType:"json",
		type: "POST",
		url: "/coto/cart.add.php",
		data: {
			'mode'	: mode,
			'gpcode' : gpcode,
			'it_id' : it_id,
			'it_qty' : it_qty
		},
		cache: false,
		success: function(data) {
			alert(data.msg);
		}
	});

}

</script>


<?
if (G5_IS_MOBILE) {
    include_once(G5_MOBILE_PATH.'/shop/tail.php');
    return;
}
?>
    </div>
</div>

</div>



<?if($bo_table == "photo_gallery" || $bo_table == "bestshot"){?>
<div id="blueimp-gallery" class="blueimp-gallery">
	<!-- The container for the modal slides -->
	<div class="slides"></div>
	<!-- Controls for the borderless lightbox -->
	<h3 class="title"></h3>
	<a class="prev">‹</a>
	<a class="next">›</a>
	<a class="close">×</a>
	<a class="play-pause"></a>
	<ol class="indicator"></ol>
	<!-- The modal dialog, which will be used to wrap the lightbox content -->
	<div class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" aria-hidden="true">&times;</button>
					<h4 class="modal-title"></h4>
				</div>
				<div class="modal-body next"></div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default pull-left prev">
						<i class="glyphicon glyphicon-chevron-left"></i>
						Previous
					</button>
					<button type="button" class="btn btn-primary next">
						Next
						<i class="glyphicon glyphicon-chevron-right"></i>
					</button>
				</div>
			</div>
		</div>
	</div>
</div>
<?}?>




<!-- } 콘텐츠 끝 -->

<script type="text/javascript">
function onPopKBAuthMark()
{
window.open('','KB_AUTHMARK','height=604, width=648, status=yes, toolbar=no, menubar=no, location=no');
document.KB_AUTHMARK_FORM.action='http://escrow1.kbstar.com/quics';
document.KB_AUTHMARK_FORM.target='KB_AUTHMARK';
document.KB_AUTHMARK_FORM.submit();
}
</script>

<form name="KB_AUTHMARK_FORM" method="get">
<input type="hidden" name="page" value="B009111"/>
<input type="hidden" name="cc" value="b010807:b008491"/>
<input type="hidden" name="mHValue" value='53c33b910404b57b879243764e930592201405151656495'/>
</form>

<hr>

<!-- 하단 시작 { -->
<div id="ft" style="clear:both;width:100%;">
    <div id="ft_wrapper">
    <div id="ft_catch2">
		<li onclick="goto_url('<?=G5_URL?>/guide1.php');">회사소개</li>
		<li style="font-weight:normal">|</li>
		<li onclick="goto_url('<?=G5_URL?>/company1.php');">이용약관</li>
		<li style="font-weight:normal">|</li>
		<li onclick="goto_url('<?=G5_URL?>/company1.php?cate=2');">코인즈투데이특약</li>
		<li style="font-weight:normal">|</li>
		<li onclick="goto_url('<?=G5_URL?>/company1.php?cate=3');">개인정보취급방침</li>
		<li style="font-weight:normal">|</li>
		<li onclick="goto_url('<?=G5_URL?>/contactus.php');">CONTACT US</li>


		<div style="float:right;padding-right:20px;margin:0 90px 0 0;"><img src="<?=G5_IMG_URL?>/bottom_btn.jpg" /> </div>
	</div>

	 <div id="ft_catch">
				<li style="padding-top:5px;padding-left:20px;color:#505050">
					회사명: 코인즈투데이 | 대표: 박민우 | 서울시 강남구 밤고개로14길 13-34(자곡동 274) 2층</br>
					통신판매업신고: 제2014-경기양주- 0130호 | 사업자등록번호:127-46-73320 </br>
					Tel: 070-4323-6999,6998 | Fax: 070-8230-0777 | 이메일: minwoo@coinstoday.co.kr | 개인정보관리책임자: 박민우<br>
					전자상거래 소비자보호 법률에 따른 구매 안전 서비스 안내 : 본 판매자는 KB국민은행과 계약을 통해 구매 안전 서비스를 자동으로 제공중입니다. <a href="#" onclick="javascript:onPopKBAuthMark();return false;" style="color:#fff;background:#707070;border-radius:5px;padding:2px 5px 2px 5px;">서비스 가입사실 확인</a></br>
					Copyright &copy; <b>coinstoday.co.kr</b> All rights reserved.
				</li>
				<li style="float:right;margin:0 110px 0 0;"><img src="<?=G5_IMG_URL?>/logo_bottom.png" /></li>
	 </div>
   </div>
</div>

<?php
if(G5_USE_MOBILE && !G5_IS_MOBILE) {
    $seq = 0;
    $href = $_SERVER['PHP_SELF'];
    if($_SERVER['QUERY_STRING']) {
        $sep = '?';
        foreach($_GET as $key=>$val) {
            if($key == 'device')
                continue;

            $href .= $sep.$key.'='.strip_tags($val);
            $sep = '&amp;';
            $seq++;
        }
    }
    if($seq)
        $href .= '&amp;device=mobile';
    else
        $href .= '?device=mobile';
?>

<?php
}

if ($config['cf_analytics']) {
    echo $config['cf_analytics'];
}
?>

<?if(is_mobile()){?>
	<!--<a href="<?php  echo $href; ?>" id="device_change" >모바일 버전으로 보기</a>-->
<?}?>

<!-- } 하단 끝 -->

<script>

/*
$(window).resize(sizeContent());
function sizeContent() {
	var newHeight = $("html").height() + "px";
	$("#ft_catch").css("height", newHeight);
}
*/

$(function() {
    // 폰트 리사이즈 쿠키있으면 실행
    font_resize("container", get_cookie("ck_font_resize_rmv_class"), get_cookie("ck_font_resize_add_class"));
});

</script>

<?
include_once(G5_PATH."/tail.script.php");
//include_once(G5_PATH."/tail.sub.php");
?>