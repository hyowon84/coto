<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>

<link rel="stylesheet" href="<?php echo G5_SHOP_SKIN_URL; ?>/style.css">
<link rel="stylesheet" href="/css/default.css">

<script src="<?php echo G5_JS_URL; ?>/shop.gp.js"></script>

<input type="hidden" name="cnt_save" id="cnt_save" value="">

<script src="<?=G5_JS_URL?>/imgLiquid.js"></script>



<!-- gplist.php 상품목록 list.50.skin php 상품진열 10 시작 { -->
<?php
global $is_admin, $개발자, $개별오더활성화, $상품노출개수; //재고가 있는경우에만 장바구니 및 주문이 가능
$height = (G5_IS_MOBILE) ? '140px' : '230px';
$margin = (G5_IS_MOBILE) ? '0px' : '15px';


echo "<section class='slide' style=''>
						<div class='slide_contain'>";

for ($i=1; $it=sql_fetch_array($result); $i++) {

	
	$it[card_price] = ceil($it[cash_price] * 1.03 / 100) * 100;
	$jaego = ($it[real_jaego] > 0) ? $it[real_jaego] : 0;
	$현재가 = ($it[MAX_BID_LAST_PRICE]) ? $it[MAX_BID_LAST_PRICE] : $it[ac_startprice];
	$즉시구매가 = ($it[ac_buyprice]) ? $it[ac_buyprice] : $it[po_cash_price];	//즉구가 설정값이 없으면 실시간시세값으로 설정
	$종료일 = date("n/j H:i",strtotime($it[ac_enddate]));
	$남은시간 = getLeftTime($it[ac_enddate]);

	//나의입찰액이 최고가인 경우 나의 입찰금액 노출
	$나의입찰금액 = ($it[MAX_BID_PRICE] == $it[MY_BID_PRICE]) ? "<dl><font color='blue' style='font-size:1.1em; font-weight:bold;'>최고가 입찰중 ".number_format($it[MY_BID_PRICE])."원</font></dl>" : "";

	echo "<div class='prdlist_item'>
						<a href='/shop/auction.php?gp_id=$it[gp_id]'>
						<span class='sct_icon' style='position:absolute;'>".item_icon1($it)."</span>
						<div class='imgLiquidNoFill imgLiquid' style='width:98%;height:$height; margin:$margin auto;'>
							<img src='$it[gp_img]' />
						</div>
						<div class='prdlist_itemname cut_text'>$it[gp_name]</div>
						</a>
						<div class='prdlist_btn'>							
						</div>
						<div class='prdlist_bottom'>
							<dl> <dt>현재가 ".number_format($현재가)."원</dt></dt>
							$나의입찰금액
							<dl>시세정보 ".number_format($즉시구매가)."원</dt>
							<dl>종료일 $종료일</dl>
							<dl>남은시간 $남은시간</dl>
						</div>
					</div>
		";
	$item_cnt++;

	//마지막아이템은 </div> 하나
	if($item_cnt == $item_size) {

		if(G5_IS_MOBILE) {
			echo "</li>";
		} else {
			echo "</div>";
		}

	} else if( ( ($item_cnt % $상품노출개수) == 0) && $item_cnt != 0) {
		if(G5_IS_MOBILE) {
			echo "</li><li class='slide_contain'>";
		} else {
			//줄바꿈시
			echo "</div><div class='slide_contain'>";
		}
	}
	
}
echo "</section>";


if ($i > 1) echo "</ul>\n";

if($i == 1) echo "<p class=\"sct_noitem\">등록된 상품이 없습니다.</p>\n";
?>

<script>
	$(document).ready(function() {
		$(".imgLiquidNoFill").imgLiquid({fill:false});
	});
</script>

<!-- } 상품진열 10 끝 -->


