<?php
include_once('./_common.php');
$gp_id = escape_trim($_GET['gp_id']);
include_once(G5_MSHOP_PATH.'/head.php');
include_once(G5_LIB_PATH.'/iteminfo.lib.php');


if (!$it['gp_id'])
    alert('자료가 없습니다.');
if (!($it['ca_use'] && $it['gp_use'])) {
    if (!$is_admin)
        alert('현재 판매가능한 상품이 아닙니다.');
}



//분류 테이블에서 분류 상단, 하단 코드를 얻음
$ca = $it;


// 본인인증, 성인인증체크
if(!$is_admin) {
    $msg = shop_member_cert_check($it_id, 'item');
    if($msg)
        alert($msg);
}


$g5['title'] = $it['gp_name'].' &gt; '.$it['ca_name'];


// 분류 위치
// HOME > 1단계 > 2단계 ... > 6단계 분류
$ca_id = $it['ca_id'];
//include G5_MSHOP_SKIN_PATH.'/navigation.skin.php';

// 이 분류에 속한 하위분류 출력
//include G5_MSHOP_SKIN_PATH.'/listcategory.skin.php';

?>

<!-- 공동구매 신청 레이어팝업 -->
<script>
var topmargin = -100;
</script>
<script src="<?=G5_JS_URL?>/group_purchase.js"></script>
<div class="mw_layer_gp">
	<div class="bg_gp"></div>

	<div id="layer_gp">
		<div style="width:100%;font-size:17px;text-align:right"><a href="#" class="close_gp" style="padding-right:5px">X</a></div>

		<div class='gp_view_loading' style="display:none; width:100%; height:300px; margin-top:150px; font-size:20px; font-weight:bold; color:black;">
			<center>
				데이터를 불러오고 있습니다<br>
				잠시만 기다려주세요<br>
				<img src='/img/ajax-loader.gif' />
			</center>
		</div>
		<div class="gp_view"></div>
	</div>
</div>


<!-- 상품 상세보기 시작 { grouppurchase(m)-->
<?php
// 소셜 관련
//$sns_title = get_text($it['it_name']).' | '.get_text($config['cf_title']);
//$sns_url  = G5_SHOP_URL.'/item.php?it_id='.$it['it_id'];
//$sns_share_links .= get_sns_share_link('facebook', $sns_url, $sns_title, G5_SHOP_SKIN_URL.'/img/sns_fb2.png').' ';
//$sns_share_links .= get_sns_share_link('twitter', $sns_url, $sns_title, G5_SHOP_SKIN_URL.'/img/sns_twt2.png').' ';
//$sns_share_links .= get_sns_share_link('googleplus', $sns_url, $sns_title, G5_SHOP_SKIN_URL.'/img/sns_goo2.png');

// 보안서버경로
if (G5_HTTPS_DOMAIN)
    $action_url = G5_HTTPS_DOMAIN.'/'.G5_SHOP_DIR.'/gp_cartupdate.php';
else
    $action_url = './gp_cartupdate.php';

// 주문가능체크
$is_orderable = true;
if(!$it['gp_use'])
    $is_orderable = false;

// 상세 하단 탭메뉴
function pg_anchor1($anc_id) {
    global $default;
    global $item_use_count, $item_qa_count, $item_relation_count;
?>
    <ul class="sanchor">
        <li style="border-left:0;" <?php if ($anc_id == 'inf') echo 'class="sanchor_on"'; ?>><a href="#sit_inf">상품정보</a></li>
		<li <?php if ($anc_id == 'use') echo 'class="sanchor_on"'; ?>><a href="#sit_use">고객상품평 <span class="item_use_count"><?php// echo $item_use_count; ?></span></a></li>
		<li <?php if ($anc_id == 'qa') echo 'class="sanchor_on"'; ?>><a href="#sit_qa">상품 Q & A <span class="item_qa_count"><?php// echo $item_qa_count; ?></span></a></li>
        <?php if ($default['de_baesong_content']) { ?><li style="border-right:0;" <?php if ($anc_id == 'dvr') echo 'class="sanchor_on"'; ?>><a href="#sit_dvr">배송안내</a></li><?php } ?>
        <!--<?php// if ($default['de_change_content']) { ?><li><a href="#sit_ex" <?php// if ($anc_id == 'ex') echo 'class="sanchor_on"'; ?>>교환정보</a></li><?php// } ?>
        <li><a href="#sit_rel" <?php// if ($anc_id == 'rel') echo 'class="sanchor_on"'; ?>>관련상품 <span class="item_relation_count"><?php// echo $item_relation_count; ?></span></a></li>-->
    </ul>
<?php
}
?>

<?php if($is_orderable) { ?>
<script src="<?php echo G5_JS_URL; ?>/shop.gp.js"></script>
<?php } ?>

<?
if ($is_admin) {
    echo '<div class="sit_admin"><a href="'.G5_ADMIN_URL.'/shop_admin/grouppurchaseform.php?w=u&amp;gp_id='.$gp_id.'" class="btn_admin">상품수정</a></div>';
}
?>

<div id="sit">
	
	<?php
	// 상품 구입폼
	//  include_once(G5_MSHOP_SKIN_PATH.'/grouppurchase.form.skin.php');
	include_once(G5_MSHOP_SKIN_PATH.'/auction.form.skin.php');
	?>

	<?php
	// 상품 상세정보
	include_once(G5_MSHOP_SKIN_PATH.'/grouppurchase.info.skin.php');
	?>

</div>

<?php
include_once(G5_MSHOP_PATH . '/tail.php');
?>
