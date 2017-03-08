<?php
include_once('./_common.php');
define('_GROUPSHOP_', true);


//경매상품 정보
$sql_auction_item = str_replace('#상품기본조건#', " AND		gp_id = '$gp_id' ", $sql_auction_item);
$it = sql_fetch($sql_auction_item);

if($mode == 'jhw') echo "<textarea>".$sql_auction_item."  ".$sql."</textarea>";


$시작가 = $it[ac_startprice];
$현재가 = ($it[MAX_BID_LAST_PRICE] > 0) ? $it[MAX_BID_LAST_PRICE] : $시작가;	//bid정보가 없으면 시작가로 시작
$즉시구매가 = ($it[ac_buyprice]) ? $it[ac_buyprice] : $it[po_cash_price];	//즉구가 설정값이 없으면 실시간시세값으로 설정
$입찰금액 = ($it[MAX_BID_LAST_PRICE] > 0) ? calcBidPrice($현재가) : $시작가;
$진행수량 = $it[ac_qty];
$종료일 = $it[ac_enddate];
$입찰수 = $it[BID_CNT];
$경매진행여부 = ( date("Y-m-d H:i:s") < $종료일 && $it[ac_yn] == 'Y') ? 'Y' : 'N';


if (G5_IS_MOBILE) {
    include_once(G5_MSHOP_PATH.'/auction.php');
    return;
}


$gp_id = escape_trim($_GET['gp_id']);

include_once(G5_LIB_PATH.'/iteminfo.lib.php');


if (!$it['gp_id'])
    alert('자료가 없습니다.');

if ( $경매진행여부 != 'Y' ) {
    if (!$is_admin)
        alert('현재 경매 가능한 상품이 아닙니다.');
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
//include G5_SHOP_SKIN_PATH.'/listcategory.skin.php';

if ($is_admin) {
    echo '<div class="sit_admin"><a href="'.G5_ADMIN_URL.'/shop_admin/grouppurchaseform.php?w=u&amp;gp_id='.$gp_id.'" class="btn_admin">상품수정</a></div>';
}
?>
<!-- 상품 상세보기 시작 grouppurchase(w) { -->
<?
// 소셜 관련
$sns_title = get_text($it['gp_name']).' | 코인즈투데이';
$sns_url  = G5_SHOP_URL.'/auction.php?gp_id='.$it['gp_id'];
$sns_share_links .= get_sns_share_link('facebook', $sns_url, $sns_title, G5_SHOP_SKIN_URL.'/img/sns_fb2.png').' ';
$sns_share_links .= get_sns_share_link('twitter', $sns_url, $sns_title, G5_SHOP_SKIN_URL.'/img/sns_twt2.png').' ';
$sns_share_links .= get_sns_share_link('googleplus', $sns_url, $sns_title, G5_SHOP_SKIN_URL.'/img/sns_goo2.png');


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
		include_once(G5_SHOP_SKIN_PATH.'/auction.form.skin.php');
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
