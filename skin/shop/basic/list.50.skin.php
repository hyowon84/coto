<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>

<link rel="stylesheet" href="<?php echo G5_SHOP_SKIN_URL; ?>/style.css">
<script src="<?php echo G5_JS_URL; ?>/shop.gp.js"></script>

<input type="hidden" name="cnt_save" id="cnt_save" value="">

<script src="<?=G5_JS_URL?>/imgLiquid.js"></script>
<script>
$(document).ready(function() {
	$(".imgLiquidNoFill").imgLiquid({fill:false});
});
</script>


<!-- gplist.php 상품목록 list.50.skin php 상품진열 10 시작 { -->
<?php
global $is_admin, $개발자, $개별오더활성화; //재고가 있는경우에만 장바구니 및 주문이 가능

for ($i=1; $row=sql_fetch_array($result); $i++) {

	$po_cash_price = $row[po_cash_price];
	$po_card_price = ceil($po_cash_price * 1.03 / 100) * 100;
	if($is_admin == 'super') $재고 = ($row[real_jaego] > 0) ? "남은수량 ".$row[real_jaego]."개<br>" : "남은수량 0개<br>";
	$gpcode = ($_GET[gpcode]) ? $_GET[gpcode] : 'QUICK';
	
	if($po_cash_price > 0 && $row[real_jaego] > 0 && $개별오더활성화) {
		$장바구니버튼 = "<input type='button' onclick=\"cart_add('add','$row[gp_id]','$gpcode')\" value='장바구니 담기' id='sit_btn_buy2' style='width:120px; height:45px; font-size:1.2em; font-weight:bold;'>";
		$주문하기버튼 = "<input type='button' onclick=\"quickOrder('$row[gp_id]','$gpcode')\" value='주문하기' id='sit_btn_buy2' style='width:120px; height:45px; font-size:1.2em; font-weight:bold;'>";
	} else {
		$장바구니버튼 = "";
		$주문하기버튼 = "";
	}
	
	echo "<ul class=\"sct sct_50\">\n";
	echo "<li class=\"sct_li {$sct_last}\">\n";

	if ($this->href) {
		 echo "<div class='imgLiquidNoFill imgLiquid' style='float:left;width:170px;height:170px;padding:0 15px 0 15px;'>
		 				<a href=\"{$this->href}{$row['gp_id']}&gpcode=".$gpcode."&ca_id=".$_GET[ca_id]."\" class=\"sct_a sct_img\">\n";
	}

	if ($this->view_it_img) {
		echo "<img src='$row[gp_img]' width='$this->img_width' height='$this->img_height' />";
	}

	if ($this->href) {
		echo "</a>";
		echo "</div>";
	}

	if ($this->view_it_icon) {
		echo "<span class=\"sct_icon\" style=\"top:0;left:0;\">".item_icon1($row)."</span>\n";
	}

	if ($row[gp_360img]) {
		//echo "<span class=\"icon_gp360\" style=\"position:absolute;bottom:0;left:0;\">".$row[gp_360img]."</span>\n";
	}
	
	echo "<div style='float:left;width:840px;'>";
	if ($this->href) {
		echo "<div style='float:left;width:840px;height:115px;color:#475055;font-weight:bold;font-size:17px;'><div style='height:70px;'><a href=\"{$this->href}{$row['gp_id']}&gpcode=".$gpcode."&ca_id=".$_GET[ca_id]."\" class=\"sct_a sct_txt\">\n";
	}

	if ($this->view_it_name) {

		switch($row[gp_price_type]) {
			case "Y":	//스팟시세
				$가격유형 = "[％]";
				break;
			case "N":	//달러가(환율적용)
				$가격유형 = "[$]";
				break;
			case "W":	//원화적용
				$가격유형 = "[￦]";
				break;
			default:
				break;
		}

		echo $가격유형.stripslashes($row['gp_name'])."\n";
	}

	

	if ($this->href) {
		 echo "</a></div>";

		echo "	<div style='float:left;  width:110px;'>
						<a href=\"{$this->href}{$row['gp_id']}&ca_id=".$_GET[ca_id]."\">\n
						<img src='".G5_URL."/img/view_product.gif' border='0' align='absmiddle'>
						</a>
					</div>
				</div>";
	}


	if ($this->view_it_cust_price || $this->view_it_price) {
		


	}

	if ($this->view_it_price) {
		$구매가 = display_price($po_cash_price, $row['it_tel_inq'])."\n";
		$dollar = $po_cash_price / $row[USD];
		$달러가 = "$".number_format($dollar,2,'.','');
	}

	if ($this->view_it_price) {
		if($it[gp_card] == '사용안함') {
			$카드가 ="<div class='sct_cost1'>
									<div class='sct_cost_title'>카드가</div>
									<div style='width:225px;font-weight:bold;'>".display_price($po_card_price)."\n"."</div>
								</div>";
		}

	}

	echo "<div style='float:left;width:840px;'>
					<div style='float:left;width:440px; height:80px;'>
						<div class='sct_cost'>
							<div class='sct_cost_title'>구매가</div>
							<div style='width:225px;font-weight:bold;'>$구매가 ($달러가)</div>
							
						</div>
						
						$카드가
						
						<div style='text-align:left; float:left; margin:12px; width:300px; font-size:1.2em; font-weight:bold;'>$재고</div>
					</div>

					<div style='float:right;'>
			
						<div style='width:233px; margin:-25px 0 10px 0;'>
							신청수량 <input type='text' id='".$row['gp_id']."_qty' name='ct_qty".$row['gp_id']."' class='frm_input' size='5' value='1' style='text-align:right; margin-left:10px; padding:0px; padding-right:10px; font-size:15px;'>
						</div>
			
						<div>
							<!--<a href=\"#\" class='go_cart' gp_id='".$row['gp_id']."' ca_id='".$row[ca_id]."' it_price='".$row[it_price]."' it_card_price='".$po_card_price."'><img src='".G5_URL."/img/gp_submit_btn.gif' border='0' align='absmiddle'></a>-->
							$장바구니버튼
							$주문하기버튼
						</div>
					</div>
				</div>
			</div>
	";

	if ($this->view_it_id) {
		 echo "<span class=\"sct_id\">&lt;".stripslashes($row['gp_id'])."&gt;</span>\n";
	}

	echo "</li>\n";
	echo "</ul>\n";
}

if ($i > 1) echo "</ul>\n";

if($i == 1) echo "<p class=\"sct_noitem\">등록된 상품이 없습니다.</p>\n";
?>
<!-- } 상품진열 10 끝 -->


