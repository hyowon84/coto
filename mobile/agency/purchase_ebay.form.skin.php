<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>

<link rel="stylesheet" href="<?php echo G5_SHOP_SKIN_URL; ?>/style.css">



<form name="forderform" method="post" action="purchase_request.php">
<input type="hidden" name="ebay_title" value="<?php echo $resp->Item->Title; ?>">
<input type="hidden" name="ebay_link" value="<?php echo $resp->Item->ViewItemURLForNaturalSearch?>">
<input type="hidden" name="ebay_price" value="<?php echo $total?>">
<input type="hidden" name="ebay_price1" value="<?php echo getExchangeBRate($total)?>">
<input type="hidden" name="ebay_gubun" value="<?php echo $payGubun?>">
<input type="hidden" name="buy_kind" value="구매대행">

<div id="sit_ov_wrap">
    <!-- 상품이미지 미리보기 시작 { -->
    <div id="sit_pvi" >
        <div id="sit_pvi_big"><img src="<?php echo $resp->Item->PictureURL; ?>" width="302px" height="302px"></div>
		<div id="sit_pvi_small">
			<ul>
		<?php foreach($resp->Item->PictureURL as $vars){?>
			<li><img src="<?php echo $vars; ?>" width="50px" height="50px" onclick="imageChange('<?php echo $vars; ?>');"></li>
		<?php }?>
			</ul>
		</div>
    </div>
    <!-- } 상품이미지 미리보기 끝 -->

    <!-- 상품 요약정보 및 구매 시작 { -->
    <section id="sit_ov">
		<!--<h2 id="sit_title2"><?php //echo $payGubun?>상품</h2>-->
		<div class="seller_box">
			<div class="seller_title">
				판매자 정보
			</div>
			<div style="float:left;width:1px;border-right:1px #cfcfcf dashed;height:50px;"></div>
			<div class="seller_info">
				<table border="0" cellspacing="0" cellpadding="0" width="100%">
					<tr height="20px">
						<td width="80px">판매자 아이디</td>
						<td align="right"><?php echo $resp->Item->Seller->UserID;?></td>
					</tr>
				</table>
			</div>
		</div>
        <h2 id="sit_title"><?php echo $resp->Item->Title; ?> <span class="sound_only">요약정보 및 구매</span></h2>
		<div class="frm_line"></div>

		<div class="auc_info_box">
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr height="20px">
					<td width="50%" style="font-size:12px;">아이템번호 : <?php echo $resp->Item->ItemID;?></td>
					<td width="50%" align="right" style="font-size:12px;">
						<div style="float:right;border:1px #545454 solid;font-size:12px;padding:2px;"><a href="<?php echo $resp->Item->ViewItemURLForNaturalSearch?>" target="_blank">이베이원문보기</a></div>
					</td>
				</tr>
				<tr height="13px"><td colspan="2"></td></tr>
				<tr height="20px">
					<td>상품상태</td>
					<td align="right"><?php 
					if($resp->Item->ListingStatus=="Completed")echo "판매완료";
					elseif($resp->Item->ListingStatus=="Active")echo "판매중";
					else echo "알수가 없음";?></td>
				</tr>
				<tr height="20px">
					<td>종료일시</td>
					<td align="right" style="color:#003ca5;">
					<?php echo date("Y-m-d H:i:s",time()+getPrettyTimeFromEbayTimeSec($resp->Item->TimeLeft))?></td>
				</tr>
				<tr height="20px">
					<td>출품위치/국가</td>
					<td align="right"><?php echo $resp->Item->Location;?></td>
				</tr>
			</table>
		</div>
		<div class="frm_line"></div>

        <table class="sit_ov_tbl">
        <colgroup>
            <col>
        </colgroup>
        <tbody>
        <?php
		if ($resp->Item->ConvertedCurrentPrice) {
		?>
		<tr>
			<td>
				<div style="float:left;color:#fff;background:#003ca5;font-size:12px;padding:2px;"><?=$payGubun?></div>
				<div style="float:left;padding:0 0 0 10px;">
					<?php if ($timeLeft) { ?>
					남은시간 :
					<span class="timeleft" style="color:red;"><?php// echo $timeLeft; ?></span>
					<?php }?>
				</div>
			</td>
		</tr>
        <tr>
			<?php if($payGubun=="경매"){?>
            <td>
				<div style="float:left;">현재입찰가</div>
				<div style="float:left;color:#545454;font-size:18px;font-weight:bold;padding:0 0 0 15px;margin:-5px 0 0 0;">US $<?php echo $resp->Item->ConvertedCurrentPrice; ?> ( 입찰수 : <?php echo $resp->Item->BidCount;?> )</div>
			</td>
			<?php }else{?>
			<td>
				<div style="float:left;">판매금액</div>
				<div style="float:left;color:#545454;font-size:18px;font-weight:bold;padding:0 0 0 15px;margin:-5px 0 0 0;">US $<?php echo $resp->Item->ConvertedCurrentPrice; ?></div>
			</td>
			<?php }?>
        </tr>
        <?php } ?>

		<tr style="height:10px;"><td></td></tr>
		<tr>
			<td>
				<div style="float:left;">현지운송료</div>
				<div style="float:right;padding:0 5px 0 0;">US $<?php echo $ship; ?></div>
			</td>
		</tr>
		<tr>
			<td>
				<div style="float:left;">예상국제운송료</div>
				<div style="float:right;padding:0 5px 0 0;color:red;">중량에 따라 추가운임이 발생 할수 있음</div>
			</td>
		</tr>

        </tbody>
        </table>
		<div class="frm_line"></div>

		<div class="price_box">
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td colspan="2">예상구매금액</td>
				</tr>
				<tr height="40px">
					<td style="color:#545454;font-size:20px;font-weight:bold;">$<?php echo $total; ?></td>
					<td style="color:#545454;font-size:27px;font-weight:bold;text-align:right;"><font color="#ff5100"><?php echo number_format(getExchangeBRate($total));?></font><font color="#ff5100" style="font-size:20px;">원</font></td>
				</tr>
			</table>
		</div>
		<div class="frm_line"></div>

        <div id="sit_ov_btn" style="text-align:center;margin:15px 0 0 0;">
            <input type="image" src="<?=G5_URL?>/img/daehang_bn.jpg" value="구매대행신청" id="sit_btn_buy" style="width:370px;height:60px;">
        </div>

</div>

</form>

<script type="text/javascript">

$(document).ready(function(){
	var ebay_timer = setInterval(function(){
		$.post("./_Ajax.ebay.php", {itemID : "<?=$itemID?>"}, function(data){
			$(".timeleft").html(data);
		});
	},1000);
});

function imageChange(img){
	$("#sit_pvi_big > img").attr("src",img);
}
</script>