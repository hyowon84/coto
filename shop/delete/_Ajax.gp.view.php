<?php
include_once('./_common.php');
define('_GROUPSHOP_', true);
$gp_id = escape_trim($_GET['gp_id']);

include_once(G5_LIB_PATH.'/iteminfo.lib.php');

// 판매여부
isPurchaseBuyCheck($ca_id);

// 상품가격 업데이트
UpdateGroupPurchasePrice($gp_id);

// 분류사용, 상품사용하는 상품의 정보를 얻음
$sql = " select a.*, b.ca_name, b.ca_use from {$g5['g5_shop_group_purchase_table']} a, {$g5['g5_shop_category_table']} b where a.gp_id = '$gp_id' and a.ca_id = b.ca_id ";
$it = sql_fetch($sql);
if (!$it['gp_id'])
	alert('자료가 없습니다.');
if (!($it['ca_use'] && $it['gp_use'])) {
	if (!$is_admin)
		alert('현재 판매가능한 상품이 아닙니다.');
}

// 분류 테이블에서 분류 상단, 하단 코드를 얻음
$sql = " select ca_include_head, ca_include_tail, ca_cert_use, ca_adult_use from {$g5['g5_shop_category_table']} where ca_id = '{$it['ca_id']}' ";
$ca = sql_fetch($sql);

// 본인인증, 성인인증체크
if(!$is_admin) {
	$msg = shop_member_cert_check($it_id, 'item');
	if($msg)
		alert($msg);
}


?>

<!-- 상품 상세보기 시작 { -->
<?php
// 소셜 관련
$sns_title = get_text($it['it_name']).' | '.get_text($config['cf_title']);
$sns_url  = G5_SHOP_URL.'/item.php?it_id='.$it['it_id'];
$sns_share_links .= get_sns_share_link('facebook', $sns_url, $sns_title, G5_SHOP_SKIN_URL.'/img/sns_fb2.png').' ';
$sns_share_links .= get_sns_share_link('twitter', $sns_url, $sns_title, G5_SHOP_SKIN_URL.'/img/sns_twt2.png').' ';
$sns_share_links .= get_sns_share_link('googleplus', $sns_url, $sns_title, G5_SHOP_SKIN_URL.'/img/sns_goo2.png');

// 보안서버경로
if (G5_HTTPS_DOMAIN)
	$action_url = G5_HTTPS_DOMAIN.'/'.G5_SHOP_DIR.'/gp_cartupdate.php';
else
	$action_url = './gp_cartupdate.php';

// 주문가능체크
$is_orderable = true;
if(!$it['gp_use'])
	$is_orderable = false;
?>


<?php
// 상품 구입폼
if(G5_IS_MOBILE) {
	echo "<!-- mobile -->";
	include_once(G5_MSHOP_SKIN_PATH.'/_Ajax.grouppurchase.form.skin.php');
}
else {
	echo "<!-- web -->";
	include_once(G5_SHOP_SKIN_PATH.'/_Ajax.grouppurchase.form.skin.php');
}	
?>

