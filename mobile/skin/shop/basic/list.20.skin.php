<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>

<link rel="stylesheet" href="<?php echo G5_MSHOP_SKIN_URL; ?>/style.css">
<script type="text/javascript" src="<?=G5_URL?>/js/common_product.js"></script>

<script src="<?=G5_JS_URL?>/imgLiquid.js"></script>
<script>
	$(document).ready(function() {
		$(".imgLiquidNoFill").imgLiquid({fill:false});
	});
</script>


<!-- 상품진열 10 시작 {		list.20.skin.php -->
<?php
global $개발자, $개별오더활성화; //재고가 있는경우에만 장바구니 및 주문이 가능


echo "<ul class=\"sct sct_50\">\n";

for ($i=1; $row=sql_fetch_array($result); $i++) {
	//재고가 있는경우에만 장바구니 및 주문이 가능
	$po_cash_price = $row[po_cash_price];
	$po_card_price = ceil($po_cash_price * 1.03 / 100) * 100;
	
	if($is_admin == 'Y') $재고 = ($row[real_jaego] > 0) ? "남은수량 ".$row[real_jaego]."개<br>" : "남은수량 0개<br>";
	
	$gpcode = ($_GET[gpcode]) ? $_GET[gpcode] : 'QUICK';
	
	if($po_cash_price > 0 && $row[real_jaego] > 0 && $개별오더활성화) {
		$장바구니버튼 = "<input type='button' onclick=\"cart_add('add','$row[gp_id]','$gpcode')\" value='장바구니 담기' id='sit_btn_buy2' style='margin:0px auto; width:120px; height:38px; font-size:1.2em; font-weight:bold;'>";
		//$주문하기버튼 = "<input type='button' onclick=\"quickOrder('$row[gp_id]','$gpcode')\" value='주문하기' id='sit_btn_buy2' style='margin:0px auto; width:120px; height:38px; font-size:1.2em; font-weight:bold;'>";
	} else {
		$장바구니버튼 = "";
		$주문하기버튼 = "";
	}


	echo "<li class=\"sct_li {$sct_last}\">\n";

	if ($this->view_it_icon) {
		echo "<span class=\"sct_icon\" style=\"top:0;left:0;\">".item_icon1($row)."</span>\n";
	}

	echo "<a href=\"{$this->href}{$row['gp_id']}&gpcode=".$gpcode."&ca_id=".$_GET[ca_id]."\" class=\"sct_a sct_txt\">\n";
	echo "<div class=\"sct_imgcase imgLiquidNoFill imgLiquid\">\n";
	echo "<img src='$row[gp_img]' class=\"sct_imgcase_img\">";
	echo "</div>";

	/*상품명*/
	echo "<div class=\"txt_wrap cut_text\">";
	echo stripslashes($row['gp_name'])."\n";
	echo "</div>";
	echo "</a>";


	/*가격 그룹 레이어*/
	if ($this->view_it_cust_price || $this->view_it_price) {
		echo "<div class=\"sct_cost_wrap\">
						<div class=\"sct_cost\">
							<div class=\"sct_cost_title\" >구매가
								<span>";

								if ($this->view_it_price) {
									echo display_price($po_cash_price, $row['it_tel_inq'])."\n";
								}
		
					echo "</span>
							</div>
						</div>
						<div class=\"sct_cost1\" >
							<div class=\"sct_cost_title\">카드가
								<span>";
									if ($this->view_it_price) {
										echo display_price($po_card_price)."\n";
									}
					echo "</span>
							</div>
						</div>
						
						<div class=\"sct_cost2\">
							<div class=\"sct_cost2_txt\">
								<img class='minus_qty' name='minus_".$row['gp_id']."' src='".G5_URL."/img/m/groupMinusQty.png'/>
								<input type='text' id='".$row['gp_id']."_qty' name='ct_qty".$row['gp_id']."' class='qty_input' size='5' value='1' readonly/>
								<img class='plus_qty' name='plus_".$row['gp_id']."'' src='".G5_URL."/img/m/groupPlusQty.png'/>								
							</div>
							<div style='text-align:center; margin-top:2px;'>$마지막업데이트일 $재고</div>
						</div>

						<div class=\"sct_cost3\">
						<div class=\"sct_cost3_btn\">
							
						</div>
					</div>";
	}
//	echo "</div>";
	
	echo "<div class='actionButton' style='padding:10px; width:100%; clear:both; text-align:center;'>
					$장바구니버튼 $주문하기버튼
				</div>
	";

	if ($this->view_it_id) {
		echo "<span class=\"sct_id\">&lt;".stripslashes($row['gp_id'])."&gt;</span>\n";
	}

	echo "</li>\n";
}// end for문

if ($i > 1) echo "</ul>\n";

if($i == 1) echo "<p class=\"sct_noitem\">등록된 상품이 없습니다.</p>\n";
?>
<!-- } 상품진열 10 끝 -->