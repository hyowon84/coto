<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>
<!DOCTYPEHTML>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, minimum-scale=1.0, user-scalable=yes">
	<meta charset="utf-8">
	<title>코투 경매</title>
	<script src="<?=G5_JS_URL?>/jquery-1.8.3.min.js"></script>
	<script src="/js/common.js"></script>

	<style>
		dl,dt,dd{margin:0}
		.ac_frame { width:100%; height:auto; margin:0px auto; }
		.ac_img { width:100%; height:auto; float:left; }
		.ac_contents { margin:0px; width:100%; height:auto; float:left; }
		.ac_item_title { font-size:1.2em; font-weight:bold; }
		.ac_line_b { width:100%; height:10px; border-top:2px solid; float:left; background:white; }
		.ac_line { width:100%; height:10px; border-top:1px solid gray; float:left; background:white; }

		.ac_contents h3 { margin:0px; padding:0px; margin-top:20px;}
		.ac_contents dl {width:100%; height:auto; display:table; border-bottom:1px solid #CCC; padding:5px 0px}
		.ac_contents dl dt {width:40%; height:auto; display:table; float:left; font-size:1.1em; padding:5px 0px;}
		.ac_contents dl dd {width:60%; height:auto; display:table; float:left; font-size:1.1em; padding:5px 0px;}
		.ac_contents dl dd.max_last_price { color:red; font-weight:bold; font-size:1.4em; padding:1px 0px; }
		
		#bid_price{ font-size:1.2em; text-align:right; width:140px; height:1.2em; }
		
		.ac_btns {margin:0 auto; margin-top:10px;	text-align: center;	display: block;	clear: both;}
		.ac_btn1 {margin-bottom: 4px;	font-familiy: 'NanumGothicBold'; background: DEEPSKYBLUE; border: 0px; color: #fff; font-size: 1.5em;	font-weight: 900;	width: 130px;	height: 47px; }
		.ac_btn1:hover {background: skyblue;}
		.ac_btn2 {margin-bottom: 4px;	font-familiy: 'NanumGothicBold'; background: #f45100; border: 0px; color: #fff; font-size: 1.5em; font-weight: 900; width: 130px; height: 47px; }
		.ac_btn2:hover {background:#ffa273;}
		.ac_btn3 {margin-bottom: 4px;	font-familiy: 'NanumGothicBold'; background: #676767; border: 0px; color: #fff; font-size: 1.5em; font-weight: 900; width: 150px; height: 47px; }
		.ac_btn3:hover {background:#878787;}

		
		.ac_btn1 { margin-bottom:4px; font-familiy:'NanumGothicBold'; background:DEEPSKYBLUE; border:0px; color:#fff; font-size:1.3em; font-weight:900; width:120px; height:57px;  }
		.ac_btn1:hover{ background:skyblue; }
		.ac_btn2 {
			margin-bottom: 4px;
			font-familiy: 'NanumGothicBold';
			background: #676767;
			border: 0px;
			color: #fff;
			font-size: 1.5em;
			font-weight: 900;
			width: 150px;
			height: 47px;
		}
	</style>

	<script>
		function form_submit() {
			$("#bid_form").submit();
		}
		function descPrice(el) {
			el.value = Math.floor(el.value/1000)*1000;
		}
	</script>

</head>

<body>

<form id="bid_form" name="bid_form" method="post" action="auction.bid.php">
	<input type="hidden" name="mode" value="auc_bid" />
	<input type="hidden" name="ac_code" value="<?=$it[ac_code]?>" />
	<input type="hidden" name="gp_id" value="<?=$it[gp_id]?>" />

	<div class="ac_contents">
		<p>
		<h3>*경매상품정보</h3>
		<dl>
			<div class='imgLiquidNoFill imgLiquid' style='width:160px; height:160px; margin:0px auto; margin-top:10px; margin-bottom:10px;'>
				<img src="<?=$it[gp_img]?>" align="left"  />
			</div>
			<div style=""><?=$it[gp_name]?></div>
		</dl>
		<dl>
			<dt>경매마감시간</dt>
			<dd><?=$it[ac_enddate]?></dd>
		</dl>
		</p>
		<!--dl>
			<dt>최대구매가능수량</dt>
			<dd>1개</dd>
		</dl-->
		<p>
		<h3>*입찰정보</h3>		
		<dl>
			<dt>현재가</dt>
			<dd class="max_last_price"><?=number_format($현재가)?>원</dd>
		</dl>
		<dl>
			<dt>즉구가</dt>
			<dd><?=number_format($즉구가)?>원</dd>
		</dl>
		<dl style="display:none;">
			<dt>입찰수량</dt>
			<dd colspan="3">
				<input type="text" id="bid_qty" name="bid_qty" onkeydown="keyNumeric()" value="1" size="10">개
				<br>(최대입찰수량 : 1개+총1개까지 입찰하실 수 있습니다.)
			</dd>
		</dl>
		<dl>
			<dt>입찰금액</dt>
			<dd colspan="3">
				<input type="text" id="bid_price" name="bid_price" onkeydown="keyNumeric();" onchange="descPrice(this);" value="<?=$최소입찰금액?>" />원
				현재 <?=number_format($최소입찰금액)?>원부터<br>입찰 가능합니다<br>
			</dd>
		</dl>
		</p>
		
		<?
		if($경매진행여부 == 'Y') {
			?>
			<div class="ac_btns">
				<input type="button" class="ac_btn1" value="입찰하기" onclick="javascript:form_submit()" />
			</div>
			<?
		}
		?>
	</div>

	</body>

<script src='<?=G5_JS_URL?>/imgLiquid.js'></script>
<script>
	$(document).on('ready', function() {
		$('.imgLiquidNoFill').imgLiquid({fill:false});
	});
</script>
</html>