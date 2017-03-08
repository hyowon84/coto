<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

include_once(G5_PATH.'/head.sub.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');
include_once(G5_LIB_PATH.'/visit.lib.php');
include_once(G5_LIB_PATH.'/connect.lib.php');
include_once(G5_LIB_PATH.'/popular.lib.php');
include_once(G5_LIB_PATH.'/latest.lib.php');
?>

<header id="hd">
    <h1 id="hd_h1"><?php echo $g5['title'] ?></h1>

   <!-- <div class="to_content"><a href="#container">본문 바로가기</a></div>
    <div class="to_content"><a href="#gnb">메인메뉴 바로가기</a></div>-->

    <div id="hd_wrapper">

        <div id="logo">
            <a href="<?=G5_URL?>"><img src="<?=G5_IMG_URL?>/logo_new.png" alt="<?php echo $config['cf_title']; ?>"></a>
        </div>

		<div style="float:right;margin-right:0;padding:10px 5px 5px 0px;cursor:pointer" class="layer_trigger">

				<?if($member[mb_id] == ""){?>

					<div style="width:40px;color:#fff;text-align:center;">
						<img src="<?=G5_URL?>/img/quick_log_icon.gif" border="0" align="absmiddle">
					</div>
					<div style="width:40px;color:#fff;text-align:center;margin:5px 0 0 0;font-weight:normail">
						<a href="<?=G5_BBS_URL?>/login.php" id="snb_login" style="color:#fff;text-decoration:none">로그인</a>
					</div>

				<?}else{?>

					<div style="float:left;">
						<div style="width:60px;color:#fff;text-align:center;" onclick="goto_url('<?=G5_MSHOP_URL?>/cart.php');">
							<img src="<?=G5_URL?>/img/m_cart.jpg" border="0" align="absmiddle">
						</div>
						<div style="width:60px;color:#56ccc8;text-align:center;margin:5px 0 0 0;font-weight:bold;" onclick="goto_url('<?=G5_MSHOP_URL?>/cart.php');">
							쇼핑카트
						</div>
					</div>

					<div style="float:left;">
						<div style="width:60px;color:#fff;text-align:center;" onclick="goto_url('<?=G5_BBS_URL?>/logout.php');">
							<img src="<?=G5_URL?>/img/quick_logout_icon.gif" border="0" align="absmiddle">
						</div>
						<div style="width:60px;color:#56ccc8;text-align:center;margin:5px 0 0 0;font-weight:bold;">
							<a href="<?=G5_BBS_URL?>/logout.php" id="snb_login" style="color:#56ccc8;text-decoration:none">로그아웃</a>
						</div>
					</div>

				<?}?>
		</div>

		<!--
        <button type="button" id="hd_sch_open">검색<span class="sound_only"> 열기</span></button>


        <aside id="hd_sch">
            <div class="sch_inner">
                <h2>사이트 내 전체검색</h2>
                <form name="fsearchbox" action="<?=G5_BBS_URL?>/search.php" onsubmit="return fsearchbox_submit(this);" method="get">
                <input type="hidden" name="sfl" value="wr_subject||wr_content">
                <input type="hidden" name="sop" value="and">
                <input type="text" name="stx" id="sch_stx" placeholder="검색어(필수)" required class="frm_input required" maxlength="20">
                <input type="submit" value="검색" class="btn_submit">
                <button type="button" class="pop_close"><span class="sound_only">검색 </span>닫기</button>
                </form>

                <script>
                function fsearchbox_submit(f)
                {
                    if (f.stx.value.length < 2) {
                        alert("검색어는 두글자 이상 입력하십시오.");
                        f.stx.select();
                        f.stx.focus();
                        return false;
                    }

                    // 검색에 많은 부하가 걸리는 경우 이 주석을 제거하세요.
                    var cnt = 0;
                    for (var i=0; i<f.stx.value.length; i++) {
                        if (f.stx.value.charAt(i) == ' ')
                            cnt++;
                    }

                    if (cnt > 1) {
                        alert("빠른 검색을 위하여 검색어에 공백은 한개만 입력할 수 있습니다.");
                        f.stx.select();
                        f.stx.focus();
                        return false;
                    }

                    return true;

                }
                </script>
            </div>
        </aside>
		-->
        <script>
            $(function (){
                var $hd_sch = $("#hd_sch");
                $("#hd_sch_open").click(function(){
                    $hd_sch.css("display","block");
                });
                $("#hd_sch .pop_close").click(function(){
                    $hd_sch.css("display","none");
                });
            });
        </script>

		<div class="cl" id="sise_rolling">
			<!-- 실시간 시세 -->
			<?php include_once(G5_GOLDSPOT_PATH."/mgoldspot_search.php");?>
		</div>

		
		<script type="text/javascript">
		/* 정상작동하지 않아 주석처리함.
		goldspot = setInterval(function(){
			$.get("./_Ajax.index.php", function(data){
				alert(1);
				$("#sise_rolling").html(data);
			});
		}, 5000);
		*/
		</script>

		<!--<ul id="hd_search">
		<div id="topmenu">
				 <div class="menu2">
					<ul>
						<li>
							<a href="#">Store</a>
						</li>
						<li>
							<a href="<?php // echo G5_URL ?>/guide1.php">About</a>


						</li>
						<li>
							<a href="<?php // echo G5_URL ?>/news/main.php">News</a>


						</li>
						<li>
							<a href="<?=G5_URL?>/bbs/board.php?bo_table=community">Community</a>

						</li>
						<li>
							<a href="<?=G5_URL?>/bbs/board.php?bo_table=notice">Customer</a>

						</li>
					</ul>
				</div>
			</div>
		</ul>-->

		<!--<ul id="hd_search">

			<!--<li class="result">-->
			<!--<?php //include_once(G5_GOLDSPOT_PATH."/goldspot_search.php");?>-->
			<!--11111111
			</li>

		</ul>-->
        <ul id="hd_nb">
			<li><a href="<?=G5_URL?>/shop/list.php?ca_id=1010" id="snb_new">투데이스토어</a></li>
            <li><a href="<?=G5_URL?>/shop/gplist.php?ca_id=2010" id="snb_cnt">공동구매</a></li>
            <li><a href="<?=G5_URL?>/agency/purchase_ebay_list.php" id="snb_new">구매대행</a></li>
			<li><a href="<?=G5_BBS_URL?>/current_connect.php" id="snb_cnt">배송대행</a></li>
            <!--<li><a href="<?=G5_BBS_URL?>/new.php" id="snb_new">그레이딩대행</a></li>
			<li><a href="<?=G5_BBS_URL?>/new.php" id="snb_new">위탁판매대행</a></li>
            <li><a href="<?=G5_SHOP_URL?>/">보안금고</a></li>-->


            <!--<li><a href="<?=G5_BBS_URL?>/qalist.php" id="snb_new">1:1문의</a></li>
            <li><a href="<?=G5_BBS_URL?>/current_connect.php" id="snb_cnt">접속자 <?php echo connect(); // 현재 접속자수 ?></a></li>
            <li><a href="<?=G5_BBS_URL?>/new.php" id="snb_new">새글</a></li>
            <?php if ($is_member) { ?>
            <?php if ($is_admin) { ?>
            <li><a href="<?=G5_ADMIN_URL?>" id="snb_adm"><b>관리자</b></a></li>
            <?php } ?>
            <li><a href="<?=G5_BBS_URL?>/member_confirm.php?url=<?=G5_BBS_URL?>/register_form.php" id="snb_modify">정보수정</a></li>
            <li><a href="<?=G5_BBS_URL?>/logout.php" id="snb_logout">로그아웃</a></li>
            <?php } else { ?>
            <li><a href="<?=G5_BBS_URL?>/register.php" id="snb_join">회원가입</a></li>
            <li><a href="<?=G5_BBS_URL?>/login.php" id="snb_login">로그인</a></li>
            <?php } ?>
            <?php if (defined('G5_USE_SHOP') && G5_USE_SHOP) { ?>
            <li><a href="<?=G5_SHOP_URL?>/">쇼핑몰</a></li>-->
            <?php } ?>
        </ul>

    </div>
</header>

<hr>

<!--<div id="lnb">
    <ul>
        <?php
        $sql2 = " select * from {$g5['board_table']} where bo_show_menu = 1 and bo_device <> 'pc' ";
        if ($gr_id) $sql2 .= " and gr_id = '$gr_id' ";
        $sql2 .= " order by bo_order ";
        $result2 = sql_query($sql2);
        for ($bi=0; $row2=sql_fetch_array($result2); $bi++) { // bi 는 board index
            $bo_subject = $row2['bo_subject'];
            if (G5_IS_MOBILE && $row2['bo_mobile_subject']) {
                $bo_subject = $row2['bo_mobile_subject'];
            }
        ?>
        <li><a href="<?=G5_BBS_URL?>/board.php?bo_table=<?php echo $row2['bo_table'] ?>"><?php echo $bo_subject; ?></a></li>
        <?php } ?>
    </ul>
</div>-->

<hr>

<div id="wrapper">
    <!--<div id="aside">
        <?php // echo outlogin('basic'); // 외부 로그인 ?>
    </div>-->
    <div id="container">



        <!-- <?php // if ((!$bo_table || $w == 's' ) && !defined("_INDEX_")) { ?><div id="container_title"><?php // echo $g5['title'] ?></div><?php // } ?>-->
        <!--<div id="text_size">
             font_resize('엘리먼트id', '제거할 class', '추가할 class');
            <button id="size_down" onclick="font_resize('container', 'ts_up ts_up2', '');"><img src="<?php echo G5_URL; ?>/img/ts01.gif" alt="기본"></button>
            <button id="size_def" onclick="font_resize('container', 'ts_up ts_up2', 'ts_up');"><img src="<?php echo G5_URL; ?>/img/ts02.gif" alt="크게"></button>
            <button id="size_up" onclick="font_resize('container', 'ts_up ts_up2', 'ts_up2');"><img src="<?php echo G5_URL; ?>/img/ts03.gif" alt="더크게"></button>
        </div>-->
