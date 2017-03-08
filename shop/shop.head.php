<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

include_once(G5_PATH.'/head.sub.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');
include_once(G5_LIB_PATH.'/poll.lib.php');
include_once(G5_LIB_PATH.'/visit.lib.php');
include_once(G5_LIB_PATH.'/connect.lib.php');
include_once(G5_LIB_PATH.'/popular.lib.php');
include_once(G5_LIB_PATH.'/latest.lib.php');
?>

<!-- 상단 시작 { -->
<div id="hd">
    <h1 id="hd_h1"><?php echo $g5['title'] ?></h1>

    <div id="skip_to_container"><a href="#container">본문 바로가기</a></div>

    <?php if(defined('_INDEX_')) { // index에서만 실행 ?>
    <div id="hd_pop">
        <h2>팝업레이어 알림</h2>
        <?php include G5_SHOP_PATH.'/newwin.inc.php'; // 팝업레이어 ?>
    </div>
    <?php } ?>

    <aside id="hd_qnb">
        <h2>쇼핑몰 퀵메뉴</h2>
        <div>
            <a href="<?php echo G5_SHOP_URL; ?>/cart.php"><img src="<?php echo G5_SHOP_URL; ?>/img/hd_nb_cart.gif" alt="장바구니"></a>
            <a href="<?php echo G5_SHOP_URL; ?>/wishlist.php"><img src="<?php echo G5_SHOP_URL; ?>/img/hd_nb_wish.gif" alt="위시리스트"></a>
            <a href="<?php echo G5_SHOP_URL; ?>/orderinquiry.php"><img src="<?php echo G5_SHOP_URL; ?>/img/hd_nb_deli.gif" alt="주문/배송조회"></a>
        </div>
    </aside>

    <div id="logo"><a href="<?php echo $default['de_root_index_use'] ? G5_URL : G5_SHOP_URL; ?>/"><img src="<?php echo G5_DATA_URL; ?>/common/logo_img" alt="<?php echo $config['cf_title']; ?>"></a></div>

    <aside id="hd_aside">
        <h2>편의메뉴</h2>
        <div class="aside_inner">
            <section id="hd_sch">
                <h3>쇼핑몰 검색</h3>
                <form name="frmsearch1" action="<?php echo G5_SHOP_URL; ?>/search.php" onsubmit="return search_submit(this);">
                <input type="hidden" name="sfl" value="wr_subject||wr_content">
                <input type="hidden" name="sop" value="and">

                <label for="sch_str" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
                <input type="text" name="q" value="<?php echo stripslashes(get_text($q)); ?>" id="sch_str" required>
                <input type="submit" value="검색" id="sch_submit">

                </form>
                <script>
                function search_submit(f) {
                    if (f.q.value.length < 2) {
                        alert("검색어는 두글자 이상 입력하십시오.");
                        f.q.select();
                        f.q.focus();
                        return false;
                    }

                    return true;
                }
                </script>
            </section>

            <section id="hd_nb">
                <h3>회원메뉴 및 FAQ</h3>
                <ul>
                    <?php if ($is_member) { ?>
                    <?php if ($is_admin) {  ?>
                    <li><a href="<?php echo G5_ADMIN_URL; ?>/shop_admin/"><b>관리자</b></a></li>
                    <?php }  ?>
                    <li><a href="<?php echo G5_BBS_URL; ?>/member_confirm.php?url=register_form.php">정보수정</a></li>
                    <li><a href="<?php echo G5_BBS_URL; ?>/logout.php?url=shop">로그아웃</a></li>
                    <?php } else { ?>
                    <li><a href="<?php echo G5_BBS_URL; ?>/register.php">회원가입</a></li>
                    <li><a href="<?php echo G5_BBS_URL; ?>/login.php?url=<?php echo $urlencode; ?>"><b>로그인</b></a></li>
                    <?php } ?>
                    <li><a href="<?php echo G5_SHOP_URL; ?>/mypage.php">마이페이지</a></li>
                    <li><a href="<?php echo G5_SHOP_URL; ?>/faq.php">FAQ</a></li>
                    <li><a href="<?php echo G5_BBS_URL; ?>/qalist.php">1:1문의</a></li>
                    <li><a href="<?php echo G5_SHOP_URL; ?>/personalpay.php">개인결제</a></li>
                    <li><a href="<?php echo G5_SHOP_URL; ?>/itemuselist.php">사용후기</a></li>
                    <?php if(!$default['de_root_index_use']) { ?>
                    <li><a href="<?php echo G5_URL; ?>/">커뮤니티</a></li>
                    <?php } ?>
                </ul>
            </section>
        </div>
    </aside>

</div>

<div id="wrapper">

    <aside id="stv">
        <?php include(G5_SHOP_SKIN_PATH.'/boxtodayview.skin.php'); ?>
    </aside>

    <div id="aside">
        <?php echo outlogin('shop_basic'); // 아웃로그인 ?>

        <?php include_once(G5_SHOP_SKIN_PATH.'/boxcategory.skin.php'); // 상품분류 ?>

        <?php include_once(G5_SHOP_SKIN_PATH.'/boxcart.skin.php'); // 장바구니 ?>

        <?php include_once(G5_SHOP_SKIN_PATH.'/boxwish.skin.php'); // 위시리스트 ?>

        <?php include_once(G5_SHOP_SKIN_PATH.'/boxevent.skin.php'); // 이벤트 ?>

        <?php include_once(G5_SHOP_SKIN_PATH.'/boxcommunity.skin.php'); // 커뮤니티 ?>

        <!-- 쇼핑몰 배너 시작 { -->
        <aside id="sbn_aside">
            <h2>쇼핑몰 배너</h2>
            <?php echo display_banner('왼쪽'); ?>
        </aside>
        <!-- } 쇼핑몰 배너 끝 -->
    </div>
<!-- } 상단 끝 -->

    <!-- 콘텐츠 시작 { -->
    <div id="container">
        <?php if ((!$bo_table || $w == 's' ) && !defined('_INDEX_')) { ?><div id="wrapper_title"><?php echo $g5['title'] ?></div><?php } ?>
        <!-- 글자크기 조정 display:none 되어 있음 시작 { -->
        <div id="text_size">
            <button class="no_text_resize" onclick="font_resize('container', 'decrease');">작게</button>
            <button class="no_text_resize" onclick="font_default('container');">기본</button>
            <button class="no_text_resize" onclick="font_resize('container', 'increase');">크게</button>
        </div>
        <!-- } 글자크기 조정 display:none 되어 있음 끝 -->