<?php
include_once('./_common.php');
define('_GROUPSHOP_', true);



if(!$member[mb_id]) {
	alert_close("회원만 조회가능합니다");
}

//경매상품 정보
$sql_auction_item = str_replace('#상품기본조건#', " AND		gp_id = '$gp_id' ", $sql_auction_item);
$it = sql_fetch($sql_auction_item);
//echo "<textarea>".$sql_auction_item."</textarea>";


$시작가 = $it[ac_startprice];
$즉구가 = ($it[ac_buyprice]) ? $it[ac_buyprice] : $it[po_cash_price];	//즉구가 설정값이 없으면 실시간시세값으로 설정

$최고입찰가 = $it[MAX_BID_PRICE];
$최고현재가 = $it[MAX_BID_LAST_PRICE];


$현재가 = ($최고현재가 > 0) ? $최고현재가 : $시작가;	//bid정보가 없으면 시작가로 시작
$최소입찰금액 = ($최고현재가 > 0) ? calcBidPrice($최고현재가) : $시작가;	//bid정보가 없으면 시작가로 시작
$진행수량 = $it[ac_qty];
$종료일 = $it[ac_enddate];
$입찰수 = $it[BID_CNT];
$경매진행여부 = (date("Y-m-d H:i:s") < $종료일) ? 'Y' : 'N';
//echo "<textarea>".$sql."</textarea>";

if($mode == 'jhw') {
	echo $sql;
}

?>

<html>
<head>
<title>코투 경매</title>

<script src="<?=G5_JS_URL?>/jquery-1.8.3.min.js"></script>
<script src="/js/common.js"></script>
</head>

<body topmargin="0">
<style>
	body { margin:0px; padding:0px;}
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

	.ac_bid_form .bid_list th { padding:5px; font-size:12px; text-align:center; }
	.ac_bid_form .bid_list td { padding:5px; font-size:12px; text-align:center; }
	
	
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
	.maxbid { color:blue; font-weight:bold; }
</style>
<?
$t = explode('.',_microtime());
$timestamp = date("Y-m-d H:i:s.",$t[0]).$t[1];
?>

<div class="ac_bid_form" style="">
	<h3>*입찰 상품정보</h3>
	<table width="100%">
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

	<h3>*입찰목록</h3>
	<?
	$bid_sql = "	SELECT	AL.*
								FROM		auction_log AL
								WHERE		AL.ac_code = '$it[ac_code]'
								AND			AL.it_id = '$it[gp_id]'
								AND			AL.bid_stats <= 90
								ORDER BY AL.bid_last_price DESC, AL.bid_date DESC
	";
	$bid_result = sql_query($bid_sql);
	?>
	<table class="bid_list" width="100%">
		<tr>
			<th width="100">입찰자</th>
			<th width="150">입찰일자</th>
			<th width="100">입찰가격</th>
			<th width="100">수량</th>
		</tr>
		<?
		
		function makeHideID($str) {
			$f = substr($str,0,2);
//			$max = strlen($str)-4;
			$max = 5;
			
			for($i=0; $i < $max; $i++) {
				$l .= '*';
			}
			return $f.$l;
		}	
		
		$cnt = 0;
		$마킹 = "class='maxbid' ";
		while($bid = mysql_fetch_array($bid_result)) {
			if($cnt == '1') $마킹 = '';
			$계정 = ($bid[mb_id] == $member[mb_id]) ? "<font color='#f45100'><b>".$bid[mb_id]."<b></font>" : makeHideID($bid[mb_id])
		?>
		<tr>
			<td <?=$마킹?>><?=$계정?></td>
			<td><?=str_pad($bid[bid_date],24,'0')?></td>
			<td><?=number_format($bid[bid_last_price])?> 원</td>
			<td align="right"><?=number_format($bid[bid_qty])?> 개</td>
		</tr>
		<?
			$cnt++;
		}
		?>
	</table>

	<?
	if($경매진행여부 == 'Y') {
		?>
		<div class="ac_btns">
			<input type="button" class="ac_btn1" value="입찰하기" onclick="openPopup('auction.bid.php?gp_id=<?=$gp_id?>','width=544,height=589,directories=no,toolbar=no')" />
		</div>
		<?
	}
	?>
</div>

</body>
</html>