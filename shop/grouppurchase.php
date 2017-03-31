<?php
include_once('./_common.php');
define('_GROUPSHOP_', true);




// 상품가격 업데이트
// UpdateGroupPurchasePrice($gp_id);
// 분류사용, 상품사용하는 상품의 정보를 얻음
$sql_product = str_replace('#상품기본조건#', " AND		gp_id = '$gp_id' ", $sql_product);
$sql = " SELECT		T.gp_id,
									T.ca_id,
									T.ca_id2,
									T.ca_id3,
									T.event_yn,
									T.gp_name,
									T.gp_site,
									T.gp_img,
									T.gp_360img,
									T.gp_explan,
									T.gp_objective_price,
									T.gp_have_qty,
									T.gp_buy_min_qty,
									T.gp_buy_max_qty,
									T.gp_charge,
									T.gp_duty,
									T.gp_use,
									T.gp_order,
									T.gp_stock,
									T.gp_time,
									T.gp_update_time,
									T.gp_card,
									T.gp_price,
									T.gp_price_org,
									T.gp_card_price,
									T.gp_price_type,
									T.gp_metal_type,
									T.gp_metal_don,
									T.gp_metal_etc_price,
									T.gp_sc_method,
									T.gp_sc_price,
									T.it_type,
									T.gp_type1,
									T.gp_type2,
									T.gp_type3,
									T.gp_type4,
									T.gp_type5,
									T.gp_type6,
									T.real_jaego,
									CASE
										WHEN	T.ca_id LIKE 'CT%' || T.ca_id = 'GP'	THEN
											CASE
												WHEN	T.gp_price_type = 'Y'	THEN	/*실시간스팟시세*/
													CEIL(T.gp_realprice / 100) * 100
												WHEN	T.gp_price_type = 'N'	THEN	/*고정가달러환율*/
													CEIL(T.gp_fixprice / 100) * 100
												WHEN	T.gp_price_type = 'W'	THEN	/*원화적용*/
													T.gp_price
												ELSE
													0
											END
										ELSE
											CEIL(IFNULL(T.po_cash_price,T.gp_price) / 100) * 100
									END po_cash_price,
									CA.ca_name,
									CA.ca_use,
									CA.ca_include_head,
									CA.ca_include_tail,
									CA.ca_cert_use,
									CA.ca_adult_use
					FROM		$sql_product
									LEFT JOIN g5_shop_category CA ON (CA.ca_id = T.ca_id)
					WHERE		1=1
					AND			T.gp_id = '$gp_id'
";
$it = sql_fetch($sql);


if($mode == 'jhw') {
	echo $sql;
}


if (G5_IS_MOBILE) {
    include_once(G5_MSHOP_PATH.'/grouppurchase.php');
    return;
}

$gp_id = escape_trim($_GET['gp_id']);

include_once(G5_LIB_PATH.'/iteminfo.lib.php');


if (!$it['gp_id'])
    alert('자료가 없습니다.');

if (!($it['ca_use'] && $it['gp_use']) && !strstr($_GET[ca_id],'CT') ) {
    if (!$is_admin)
        alert('현재 판매가능한 상품이 아닙니다.');
}

// 분류 테이블에서 분류 상단, 하단 코드를 얻음
$ca = $it;

// 본인인증, 성인인증체크
if(!$is_admin) {
    $msg = shop_member_cert_check($it_id, 'item');
    if($msg)
        alert($msg);
}


$g5['title'] = $it['gp_name'].' &gt; '.$it['ca_name'];

// 분류 상단 코드가 있으면 출력하고 없으면 기본 상단 코드 출력
if ($ca['ca_include_head'])
    @include_once($ca['ca_include_head']);
else
    include_once('./_head.php');

// 분류 위치
// HOME > 1단계 > 2단계 ... > 6단계 분류
$ca_id = $it['ca_id'];
include G5_SHOP_SKIN_PATH.'/navigation.skin.php';

// 이 분류에 속한 하위분류 출력
include G5_SHOP_SKIN_PATH.'/listcategory.skin.php';

if ($is_admin) {
    echo '<div class="sit_admin"><a href="'.G5_ADMIN_URL.'/shop_admin/grouppurchaseform.php?w=u&amp;gp_id='.$gp_id.'" class="btn_admin">상품수정</a></div>';
}
?>

<? if($is_orderable) { ?>
<script src="<?=G5_JS_URL?>/shop.gp.js"></script>
<? } ?>


<!-- 공동구매 신청 레이어팝업 -->
<script>
var topmargin = -150;
</script>
<script src="<?=G5_JS_URL?>/group_purchase.js"></script>
<div class="mw_layer_gp">
	<div class="bg_gp"></div>

	<div id="layer_gp">
		<div style="width:100%;font-size:17px;text-align:right"><a href="#" class="close_gp" style="padding-right:5px">X</a></div>

		<div class='gp_view_loading' style="width:100%; height:400px; margin-top:250px; font-size:20px; font-weight:bold; color:black;">
			<center>
				데이터를 불러오고 있습니다<br>
				잠시만 기다려주세요<br>
				<img src='/img/ajax-loader.gif' />
			</center>
		</div>
		<div class="gp_view"></div>
	</div>
</div>


<!-- 상품 상세보기 시작 grouppurchase(w) { -->
<?
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

function pg_anchor($anc_id) {
    global $default;
    global $item_use_count, $item_qa_count, $item_relation_count;
?>
    <ul class="sanchor">
        <li style="border-left:0;" <?php if ($anc_id == 'inf') echo 'class="sanchor_on"'; ?>><a href="#sit_inf">상품정보</a></li>
		<li <?php if ($anc_id == 'use') echo 'class="sanchor_on"'; ?>><a href="#sit_use">제품평가 <span class="item_use_count"><?php// echo $item_use_count; ?></span></a></li>
		<li <?php if ($anc_id == 'qa') echo 'class="sanchor_on"'; ?>><a href="#sit_qa">제품문의 <span class="item_qa_count"><?php// echo $item_qa_count; ?></span></a></li>
        <?php if ($default['de_baesong_content']) { ?><li style="border-right:0;" <?php if ($anc_id == 'dvr') echo 'class="sanchor_on"'; ?>><a href="#sit_dvr">배송안내</a></li><?php } ?>

        <!--<?php// if ($default['de_change_content']) { ?><li><a href="#sit_ex" <?php// if ($anc_id == 'ex') echo 'class="sanchor_on"'; ?>>교환정보</a></li><?php// } ?>
        <li><a href="#sit_rel" <?php// if ($anc_id == 'rel') echo 'class="sanchor_on"'; ?>>관련상품 <span class="item_relation_count"><?php// echo $item_relation_count; ?></span></a></li>-->

    </ul>
<?php
}
?>


<div id="sit">

    <?php
    // 상품 구입폼
    include_once(G5_SHOP_SKIN_PATH.'/grouppurchase.form.skin.php');
    ?>

    <?php
    // 상품 상세정보
    include_once(G5_SHOP_SKIN_PATH.'/grouppurchase.info.skin.php');
    ?>

</div>


<?
if ($ca['ca_include_tail'])
    @include_once($ca['ca_include_tail']);
else
    include_once('./_tail.php');
?>
