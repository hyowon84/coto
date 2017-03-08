<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

//echo $ca_name;		//카테고리명
?>
<link rel="stylesheet" href="<?php echo G5_SHOP_SKIN_URL; ?>/style.css">
<style type="text/css">
.sanchor{border-top:#545454;margin:-20px 0 0 -9px;width:100%;border-top:2px #545454 solid;}
.sanchor li{width:33.33%;}
.sanchor li a{width:33.33%;text-align:center;padding:0;background:#fff;}
</style>

<div style="height:10px;"></div>


<div id="sit_inf" style="width:100%">
    <!--<h3>상품 상세설명</h3>-->
	<?php echo pg_anchor('inf'); ?>
    <?php if ($resp->Item->Description) { // 상품 상세설명 ?>
    <div id="sit_inf_ebay_explan">
        <?php echo $resp->Item->Description; ?>
    </div>
    <?php } ?>

</div>
<!-- } 상품 정보 끝 -->


</div>

<script>
$(window).on("load", function() {
    $("#sit_inf_explan").iteminfoimageresize();
});
</script>