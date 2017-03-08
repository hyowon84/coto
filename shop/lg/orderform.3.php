<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가
?>

<div id="display_pay_button" class="btn_confirm" style="display:none">
	<img src="<?G5_URL?>/shop/img/cart_buy_all_bn.gif" onclick="forderform_check(document.forderform);" style="cursor:pointer;">
	<a href="<?php echo G5_SHOP_URL; ?>/list.php?ca_id=<?php echo $continue_ca_id; ?>"><img src="<?G5_URL?>/shop/img/cart_shop_bn.gif" align="absmiddle" style="border:0;" onclick="return form_check('buy');"></a>
</div>
<div id="display_pay_process" style="display:none">
    <img src="<?php echo G5_URL; ?>/shop/img/loading.gif" alt="">
    <span>주문완료 중입니다. 잠시만 기다려 주십시오.</span>
</div>

<?php
// 무통장 입금만 사용할 때는 주문하기 버튼 보이게
if(!($default['de_iche_use'] || $default['de_vbank_use'] || $default['de_hp_use'] || $default['de_card_use'])) {
?>
<script>
document.getElementById("display_pay_button").style.display = "" ;
</script>
<?php } ?>