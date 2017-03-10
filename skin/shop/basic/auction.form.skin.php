<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

//$시작가 = $it[ac_startprice];
//$현재가 = ($it[bid_last_price]) ? $it[bid_last_price] : $시작가;	//bid정보가 없으면 시작가로 시작
//$즉시구매가 = ($it[ac_buyprice]) ? $it[ac_buyprice] : $it[po_cash_price];	//즉구가 설정값이 없으면 실시간시세값으로 설정
//$입찰금액 = ($it[bid_last_price]) ? calcBidPrice($현재가) : $시작가;
//$진행수량 = $it[ac_qty];
//$종료일 = $it[ac_enddate];
//$입찰수 = $it[BID_CNT];

//종료일 자바스크립트용
$t = explode(",",date("Y,n,j,H,i,s",strtotime($종료일)));
?>
<script type="text/javascript" src="<?=G5_URL?>/js/common_product.js"></script>
<link rel="stylesheet" href="<?=G5_SHOP_SKIN_URL; ?>/style.css">


<script type="text/javascript" src="<?=G5_URL?>/js/jquery.loupe.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){

	$(".loupe").find("img").css({"width":"500px", "height":"500px"});

});
</script>


<form name="fitem" method="post" action="<?=$action_url; ?>" onsubmit="return fitem_submit(this);">
<input type="hidden" name="gp_id" value="<?=$gp_id; ?>">
<input type="hidden" name="url">

<style>
	dl,dt,dd{margin:0}
	.ac_frame { width:800px; height:300px; margin:0px auto; }
	.ac_img { width:300px; height:300px; float:left; }
	.ac_contents { margin:10px; width:480px; height:auto; float:left; }
	.ac_item_title { font-size:1.9em; font-weight:bold; }
	
	.ac_line_b { width:100%; height:10px; border-top:2px solid; float:left; }
	.ac_line { width:100%; height:10px; border-top:1px solid gray; float:left; }

	.ac_contents dl {width:100%; height:auto; display:table; border-bottom:1px solid #CCC; padding:5px 0px}
	.ac_contents dl dt {width:130px; height:auto; display:table; float:left; font-size:1.7em; padding:5px 0px;}
	.ac_contents dl dd {width:350px; height:auto; display:table; float:left; font-size:1.7em; padding:5px 0px;}

	.ac_btns {width:480px; height:auto; float:left; margin-top:15px;}
	.ac_btn1 {margin-bottom: 4px;	font-familiy: 'NanumGothicBold'; background: DEEPSKYBLUE; border: 0px; color: #fff; font-size: 1.5em;	font-weight: 900;	width: 130px;	height: 47px; }
	.ac_btn1:hover {background: skyblue;}
	.ac_btn2 {margin-bottom: 4px;	font-familiy: 'NanumGothicBold'; background: #f45100; border: 0px; color: #fff; font-size: 1.5em; font-weight: 900; width: 130px; height: 47px; }
	.ac_btn2:hover {background:#ffa273;}
	.ac_btn3 {margin-bottom: 4px;	font-familiy: 'NanumGothicBold'; background: #676767; border: 0px; color: #fff; font-size: 1.5em; font-weight: 900; width: 150px; height: 47px; }
	.ac_btn3:hover {background:#878787;}

	.ac_bid_form th {padding:5px;}
	.ac_bid_form td {padding:5px;}
</style>	

<div class="ac_frame">
	<div class="ac_item_title"><?=stripslashes($it['gp_name'])?></div>
	<div class="ac_line_b"></div>
	
	<div class="ac_img">
		<img src='<?=$it[gp_img]?>' width='300' class="demo">
	</div>
	<div class="ac_contents">
		<dl>
			<dt>현재가</dt>
			<dd><?=number_format($현재가)?>원 / 시작가: <?=number_format($시작가)?>원</dd>
			<dt>즉시구매가</dt>
			<dd><?=number_format($즉시구매가)?>원</dd>
			<!--dt>진행수량</dt>
			<dd><?=number_format($진행수량)?>ea</dd-->
		</dl>

		<dl>
			<dt>입찰수</dt>
			<dd><?=$입찰수?>회 <a href="javascript:openPopup('auction.log.php?gp_id=<?=$gp_id?>','width=524,height=489,directories=no,toolbar=no');" style="text-decoration: underline;">경매기록</a></dd>
			<dt>남은시간</dt>
			<dd><div id="itemlefttime" style="float:left; height:30px;"></div> <div style="margin-left:10px; float:left;">[연장없음]</div></dd>

			<dt>종료날짜</dt>
			<dd><span style="letter-spacing:-1px;"><?=$종료일?> </span></dd>
			
		</dl>

		<dl>
			<dt>배송방법</dt>
			<dd>택배(평균 1~2일)</dd>
			<dt>배송비</dt>
			<dd>선결제(3,500원)</dd>
		</dl>

		<?
		if($경매진행여부 == 'Y') {
		?>
		<div class="ac_btns">
			<input type="button" class="ac_btn1" value="입찰하기" onclick="openPopup('auction.bid.php?gp_id=<?=$gp_id?>','width=544,height=589,directories=no,toolbar=no')" />
			<a href="/coto/orderpay.php?it_id=<?=$gp_id?>&it_qty=1&gpcode=QUICK"><input type="button" class="ac_btn2" value="구매하기" /></a>
			<!-- input type="button" class="ac_btn3" value="관심상품등록" /-->
		</div>
		<?
		}
		?>		
	</div>
</div>

<div id="sit_ov_wrap">
	<div id="sit_pvi">
		<div id="sit_pvi_big" style="margin:0;">
			
		</div>
		<h2 id="sit_title" style="text-align:center;">
			
			<span class="sound_only">요약정보 및 구매</span>
		</h2>
		<script type="text/javascript">
		$(".demo").loupe();
		</script>
		<!--<div style="padding:0 9px 0 9px;"><div style="background:#f9f9fb;height:152px;"></div></div>-->
		<div style="float:right;height:45px;"><?=$sns_share_links; ?></div>
		<?
		if($it[gp_site]) {
		?>
		<div style="float:right;border:1px #545454 solid;font-size:12px;padding:2px;margin:3px 10px 0 0px"><a href="<?=$it[gp_site]?>" target="_blank">원문보기</a></div>
		<?
		}
		?>
	</div>
	<!-- } 상품이미지 미리보기 끝 -->
</div>
</form>

<script>
var closeTime = new Date(<?="$t[0],".($t[1]-1).",$t[2],$t[3],$t[4],$t[5]"?>);
changeItemLeftTime();
</script>