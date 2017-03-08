<?
$my_menu_gp_url_arr = explode(".", basename($PHP_SELF));

if(strpos($my_menu_gp_url_arr[0], "_gp") !== false){
	$my_menu_gp_url = $my_menu_gp_url_arr[0];
}else{
	$my_menu_gp_url = $my_menu_gp_url_arr[0]."_gp";
}

$my_menu_url_arr = explode(".", basename($PHP_SELF));

if(strpos($my_menu_url_arr[0], "_gp") !== false){
	$my_menu_url = substr($my_menu_url_arr[0], 0, strlen($my_menu_url_arr[0]) - 3);
}else{
	$my_menu_url = $my_menu_url_arr[0];
}

?>

<!--<div id="my_tab_nav">-->
	<!--<ul>-->
		<!--<li <?if($cate1 == ""){echo "class='on'";}?> onclick="goto_url('<?=G5_SHOP_URL?>/orderinquiry.php');">주문확인/배송조회</li>-->
		<!--<li <?if($cate1 == "1"){echo "class='on'";}?> onclick="goto_url('<?=G5_SHOP_URL?>/orderinquiry_exchange.php?cate1=1');">취소/반품/교환</li>-->
		<!--<li <?if($cate1 == "2"){echo "class='on'";}?> onclick="goto_url('<?=G5_SHOP_URL?>/itemuselist.php?cate1=2');">상품리뷰</li>-->
		<!--<li style="float:right;margin:5px 0 0 0;cursor:pointer;"><img src="<?=G5_URL?>/img/my_all_cost_bn.gif" border="0" align="absmiddle"></li>-->
	<!--</ul>-->
<!--</div>-->
<!-- 1차 오픈에서 제외
<div id="my_sub_tab">
	<ul>
		<li onclick="goto_url('<?=G5_SHOP_URL?>/<?=$my_menu_url?>.php?cate1=<?=$cate1?>');" <?if($ct_type_status == ""){?> class="on" <?}?>>투데이 스토어 상품</li>
		<li onclick="goto_url('<?=G5_SHOP_URL?>/<?=$my_menu_gp_url?>.php?cate1=<?=$cate1?>&ct_type_status=1');" <?if($ct_type_status == "1"){?> class="on" <?}?>>공동구매 상품</li>
		<li onclick="goto_url('<?=G5_SHOP_URL?>/orderinquiry_pur.php?cate1=<?=$cate1?>&ct_type_status=2');" <?if($ct_type_status == "2"){?> class="on" <?}?>>구매대행 상품</li>
		<li <?if($ct_type_status == "3"){?> class="on" <?}?>>배송대행</li>
		<li class="<?if($ct_type_status == "4"){?>on<?}?> right">그레이딩 대행</li>
	</ul>
</div>
-->