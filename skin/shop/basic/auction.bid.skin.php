<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>

<html>
<head>
	<title>코투 경매</title>

	<script src="<?=G5_JS_URL?>/jquery-1.8.3.min.js"></script>
	<script src="/js/common.js"></script>


	<script>
		window.onload = function() {
			resize_popup(520,570);
		}
	</script>
	<style>
		body { margin:0px; padding:0px; }
		h1,h2,h3 {margin:0px; padding:0px; display:''}
		.ac_bid_form {width:500px; height:auto; background:white; margin:10px; }
		.ac_bid_form h3 {font-size:1.3em; padding:0px; color:red;}
		.ac_bid_form table {
			clear: both;
			border-collapse: collapse;
			border-spacing: 0;
			margin-bottom:20px;
		}
		.ac_bid_form table th {height:35px; padding:10px; background:#eee; border:1px solid #d1dee2; text-align:left;}
		.ac_bid_form table td {padding:10px; background:white; border:1px solid #d1dee2; text-align:left;}
		.ac_btns {margin:0 auto; margin-top:10px;	text-align: center;	display: table;	clear: both;}
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


</head>

<body topmargin="0">

<script>
	function form_submit() {
		$("#bid_form").submit();
	}
</script>

<form id="bid_form" name="bid_form" method="post" action="auction.bid.php">
	<input type="hidden" name="mode" value="auc_bid" />
	<input type="hidden" name="ac_code" value="<?=$it[ac_code]?>" />
	<input type="hidden" name="gp_id" value="<?=$it[gp_id]?>" />

	<div class="ac_bid_form" style="">
		<h3>*입찰 상품정보</h3>
		<table width="500">
			<tr>
				<th width="150">상품명</th>
				<td><?=$it[gp_name]?></td>
			</tr>
			<tr>
				<th width="150">경매마감일자</th>
				<td><?=$it[ac_enddate]?></td>
			</tr>
			<tr>
				<th>최대구매가능수량</th>
				<td>1개</td>
			</tr>
		</table>

		<script>
			function descPrice(el) {
				el.value = Math.floor(el.value/1000)*1000;
			}
		</script>

		<h3>*입찰하기</h3>
		<table width="500">
			<tr>
				<th width="100">현재가</th>
				<td width="150" class=""><?=number_format($현재가)?>원</td>
				<th width="100">즉구가</th>
				<td width="150"><?=number_format($즉구가)?>원</td>
			</tr>
			<tr style="display:none;">
				<th>입찰수량</th>
				<td colspan="3">
					<input type="text" id="bid_qty" name="bid_qty" onkeydown="keyNumeric()" value="1" size="10">개
					<br>(최대입찰수량 : 1개+총1개까지 입찰하실 수 있습니다.)
				</td>
			</tr>
			<tr>
				<th>입찰금액</th>
				<td colspan="3">
					현재 <?=number_format($최소입찰금액)?>원 부터 입찰하실 수 있습니다<br>
					<input type="text" id="bid_price" name="bid_price" onkeydown="keyNumeric();" onchange="descPrice(this);" value="<?=$최소입찰금액?>" />원
				</td>
			</tr>
		</table>

		<h3>*경매관련안내</h3>
		<table width="500">
			<tr>
				<td width="150" style="font-size:1em; color:red; font-weight:bold;">경매상품은 판매가보다 저렴하게 낙찰 받아가실 수 있는 기회를 드리고 있다보니 상품의 상태가 완전한 A급이 아닐 수도 있습니다. 또한 경매 상품은 교환, 환불이 어려우니 이 점 숙지하셔서 신중한 입찰을 부탁드리겠습니다.
					* 경매 상품은 매주 목요일에만 배송됩니다. </td>
			</tr>
		</table>
		
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
</html>

