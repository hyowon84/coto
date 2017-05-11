<?php
	if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

	$begin_time = get_microtime();

	if (!isset($g5['title'])) {
		$g5['title'] = $config['cf_title'];
		$g5_head_title = $g5['title'];
	} else {
		$g5_head_title = $g5['title']; // 상태바에 표시될 제목
		$g5_head_title .= " | ".$config['cf_title'];
	}

//공동구매 APMEX 퍼센트
$group_per_res = sql_query("select * from g5_group_cnt_pay order by no asc ");

for($i = 0; $group_per_row = mysql_fetch_array($group_per_res); $i++){
	if($group_per_row[group_code]) {
		//$cart_pay_cnt_row = sql_fetch("select sum(ct_price * ct_qty) as all_price from {$g5['g5_shop_cart_table']} where ct_type='".$group_per_row[gubun_code]."' and ct_time_code > ".$group_per_row[f_date]." and ct_time_code < ".$group_per_row[l_date]." ");

		$cart_pay_cnt_row = sql_fetch("select sum(ct_price * ct_qty) as all_price from {$g5['g5_shop_cart_table']} where ct_type='".$group_per_row[gubun_code]."' and total_amount_code='".$group_per_row[group_code]."' ");

		$group_cnt_pay = $group_per_row[cnt_pay];								//총액
		//$APMEX_percent = $cart_pay_cnt_row[all_price] / $group_cnt_pay * 100;	//퍼센트 수식
		if ($group_cnt_pay != 0) {
			$percent[$group_per_row[gubun_code]] = $cart_pay_cnt_row[all_price] / $group_cnt_pay * 100;	//퍼센트 수식
		}
		else {
			$percent[$group_per_row[gubun_code]] = 0;	//퍼센트 수식
		}
		//echo $cart_pay_cnt_row[all_price]."</br>";
	}

	if($percent[$group_per_row[gubun_code]] > 100){
		$percent[$group_per_row[gubun_code]] = 100;
	}else{
		$percent[$group_per_row[gubun_code]] = $percent[$group_per_row[gubun_code]];
	}
}

	include_once(G5_LIB_PATH.'/coinstoday.lib.php');
	include_once(G5_LIB_PATH.'/latest.lib.php');
	include_once(G5_LIB_PATH.'/outlogin.lib.php');
	include_once(G5_LIB_PATH.'/poll.lib.php');
	include_once(G5_LIB_PATH.'/visit.lib.php');
	include_once(G5_LIB_PATH.'/connect.lib.php');
	include_once(G5_LIB_PATH.'/popular.lib.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<html lang="ko">
<head>
	<title><?php echo $g5_head_title; ?></title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0">
	<meta name="HandheldFriendly" content="true">
	<meta name="format-detection" content="telephone=no">

	<?php
	if (defined('G5_IS_ADMIN')) {
		echo '<link rel="stylesheet" href="'.G5_CSS_URL.'/admin.css">'.PHP_EOL;
	} else {
		$shop_css = '';
		if (defined('_SHOP_')) $shop_css = '_shop';
		if(defined('_GROUPSHOP_'))$shop_css .= '_group';
		echo '<link rel="stylesheet" href="'.G5_CSS_URL.'/'.(G5_IS_MOBILE?'mobile':'default').$shop_css.'.css">'.PHP_EOL;
		// canonical 지정
		$canonical = '';
		if ($bo_table && $wr_id) $canonical = 'http://'.$_SERVER['HTTP_HOST'].'/bbs/board.php?bo_table='.$bo_table.'&wr_id='.$wr_id;
		else $canonical = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		echo '<link rel="canonical" href="'.$canonical.'">'.PHP_EOL;
	}
	?>
	<link rel="stylesheet" type="text/css" href="<?=G5_URL?>/css/font-awesome.css" />
	<link rel="stylesheet" type="text/css" href="<?=G5_URL?>/css/circle.css" />
	<link rel="stylesheet" type="text/css" href="<?=G5_URL?>/css/magnific-popup.css">
	<link rel="stylesheet" type="text/css" href="<?=G5_MOBILE_URL?>/css/jquery.mmenu.all.css">
	<link rel="stylesheet" type="text/css" href="/css/mobile.css">
	<style>
		#mobile-menu {
			font-weight:bold;
		}

		.depth1 {
			border-top: 1px solid #2e3845;
			border-bottom: 1px solid #151b23;
		}

		.mm-list > li > a, .mm-list > li > span {
			/*padding: 5;*/
		}

		.mm-header .memberInfo {
			position:absolute;
			color:#ffffff;
		}

		.mm-header .memberIco {
			margin-right:10px;
		}

		.mm-header .btnContainer {
			float:right;
			width:54px;
		}

		.mm-header {
			padding:8px 12px;
		}

		.mm-header .btnContainer a img {
			margin-bottom:1px;
		}
	</style>
	<script>
		// 자바스크립트에서 사용하는 전역변수 선언
		var g5_url	   = "<?=G5_URL?>";
		var g5_bbs_url   = "<?=G5_BBS_URL?>";
		var g5_shop_url = '<?=G5_SHOP_URL?>';
		var g5_is_member = "<?php echo isset($is_member)?$is_member:''; ?>";
		var g5_is_admin  = "<?php echo isset($is_admin)?$is_admin:''; ?>";
		var g5_is_mobile = "<?=G5_IS_MOBILE?>";
		var g5_bo_table  = "<?php echo isset($bo_table)?$bo_table:''; ?>";
		var g5_sca	   = "<?php echo isset($sca)?$sca:''; ?>";
		var g5_editor	= "<?php echo isset($config['cf_editor'])?$config['cf_editor']:''; ?>";
		var g5_cookie_domain = "<?=G5_COOKIE_DOMAIN?>";
		<?php
		if ($is_admin) {
			echo 'var g5_admin_url = "'.G5_ADMIN_URL.'";'.PHP_EOL;
		}
		?>

	</script>
	<script src="<?=G5_JS_URL?>/jquery-1.11.2.min.js"></script>
	<script src="<?=G5_JS_URL?>/common.js"></script>
	<script src="<?=G5_JS_URL?>/common_product.js"></script>
	<script src="<?=G5_JS_URL?>/jquery-migrate-1.1.1.js"></script>
	<script type="text/javascript" src="<?=G5_MOBILE_URL?>/js/jquery.mmenu.min.all.js"></script>
	<script type="text/javascript" src="<?php echo G5_JS_URL?>/moment-with-locales.js"></script>
	<script type="text/javascript" src="<?php echo G5_JS_URL?>/countdown.js"></script>
	<script type="text/javascript" src="<?php echo G5_JS_URL?>/moment-countdown.js"></script>
	<script type="text/javascript" src="<?php echo G5_JS_URL?>/jquery.number.js"></script>
	<script type="text/javascript" src="<?php echo G5_JS_URL?>/imgLiquid.js"></script>
	<script type="text/javascript" src="<?php echo G5_JS_URL?>/jquery.magnific-popup.js"></script>
	<script src="/js/bootstrap.js"></script>
	<script src="/js/jquery.bootpag.js"></script>
	<script src="/js/jquery.vticker.min.js"></script>
	<script type="text/javascript">


	/* 모바일 */
	/*
		var headerContent =
			"<div>" +
				"<div class='memberInfo'><img class='memberIco' src='<?=G5_IMG_MOBILE_URL?>/memberIco.png'><?=$member[mb_name]?>[<?=$member[mb_nick]?>]님</div>" +
				"<div class='btnContainer'>" +
					"<a class='loginBtn' href='/bbs/login.php'><img src='<?=G5_IMG_MOBILE_URL?>/loginBtn.png'></a>" +
					"<a class='regiBtn' href='/bbs/register.php'><img src='<?=G5_IMG_MOBILE_URL?>/regiBtn.png'></a>" +
					"<a class='logoutBtn' href='/bbs/logout.php'><img src='<?=G5_IMG_MOBILE_URL?>/logoutBtn.png'></a>" +
					"<a class='infoBtn' href='/bbs/member_confirm.php?url=/bbs/register_form.php'><img src='<?=G5_IMG_MOBILE_URL?>/memberInfoBtn.png'></a>" +
				"</div>" +
			"</div>";

		var myMenu =
				"<span>MyMenu</span>" +
				"<ul>" +
				"<li><a href='/shop/orderinquiry.php'>주문배송조회</a></li>" +
				"<li><a href='/shop/cart_gp.php'>공동구매 신청조회</a></li>" +
				"<li><a href='/shop/wishlist_gp.php'>위시리스트</a></li>" +
				"<li><a href='/shop/gpitemqalist.php'>문의사항</a></li>" +
				"<li><a href='/bbs/member_confirm.php?url=/bbs/register_form.php'>회원정보</a></li>" +
				"</ul>";
				*/
		var myMenu;
		var headerContent;

		var footerContent =
			"<div class='footerContent'>" +
				"<img class='smallSearchBtn' src='<?=G5_IMG_MOBILE_URL?>/smallSearchBtn.png'>" +
				"<input id='searchBar' value='' type='text'>" +
			"<div/>";

		$(function() {
			$('#container').addClass('mobile');

			// 마이메뉴 추가
			if(g5_is_member) {
				//$('#mobile-menu>ul').append(myMenu);
			  $('.depth1.order').append(myMenu);
			}
			$("#mobile-menu").mmenu({
				header : {
					add:true,
					content:headerContent
				},
				label:true,
				slidingSubmenus: false,
				classes: "mm-slide",
				labels: true,
				footer : {
					add:true,
					content:footerContent
				}
			});

			$('#mobile-menu').on('closed.mm', function() {
				$('#mobile-menu').hide();
			});
			if(g5_is_member) {
				$('.loginBtn').hide();
				$('.regiBtn').hide();
			} else {
				$('.memberInfo').hide();
				$('.logoutBtn').hide();
				$('.infoBtn').hide();
			}


			// Store 기본 활성화
			$(".store").addClass('mm-opened');

			// 공동구매 활성화
			$(".store li").addClass('mm-opened');

			$(".menuBtn").click(function() {
				$('#mobile-menu').show();
				$("#mobile-menu").trigger("open.mm");
			});

			$(".cartBtn").click(function() {	// 장바구니
				location.href = "/shop/orderinquiry.php";
			});

			$(".smallSearchBtn").click(function() {
				search();
			});

			$("#searchBar").keypress(function(e) {
				if(e.keyCode == 13) {
					search();
				}
			});

			function search() {
				if($('#searchBar').val() == "") {
					alert("검색어를 입력해 주세요.");
				} else {
					var searchStr = $('#searchBar').val();
					location.href = "/shop/integrationSearchPage.php?s=" + searchStr + '&p=1';
				}
			}
		});

	</script>
	<?include_once(G5_GOLDSPOT_PATH."/goldspot_search_header.php");?>

	<script>
// 	setInterval(function(){ loadGoldspot(); },60000);
	loadGoldspot();
	</script>
</head>

<body>
<!-- GOOGLE_LOG -->
<?
global $is_admin;
if(!defined('G5_IS_ADMIN') && !$is_admin) include_once(G5_PATH.'/analyticstracking.php');
?>

<nav id="mobile-menu">
	<ul>
		<li class="depth1">
			<span onclick="document.location.href='<?=G5_SHOP_URL?>/auclist.php'" style="color:#56ccc8">오늘의 경매<font color=white>&nbsp;&nbsp;&nbsp;☜ 전체보기</font></span>
		</li>
		
		<li class="store depth1">
			<span style="color:#56ccc8">진행중인 공동구매</span>
			<ul>
				<?=createGpCategoryMenu();?>
			</ul>
		</li>

		<li class="store depth1">
			<span style="color:#56ccc8">코인스투데이</span>
			<ul>
				<?=getCotoCategory()?>
			</ul>
		</li>

		<li>
			<!--<li><a href="">위탁판매대행</a></li>-->
			<a href="/sec_lock.php">실물투자 컨설팅 & 보안금고</a>
		</li>

		<?
		/*
		?>
		<li>
			<span style="color:#56ccc8">개별오더신청</span>
			<ul>
				<li><a href="/shop/gplist.php?ca_id=AP">APMEX</a></li>
				<li><a href="/shop/gplist.php?ca_id=GV">GAINESVILLE</a></li>
				<li><a href="/shop/gplist.php?ca_id=MC">MCM</a></li>
				<li><a href="/shop/gplist.php?ca_id=PM">PARADISE MINT</a></li>
				<li><a href="/shop/gplist.php?ca_id=OD">OTHER DEALER</a></li>
				<!-- li><a href="/shop/gplist.php?ca_id=SC">SCOTTS DALE</a></li-->
			</ul>
		</li>
		
		<?
		*/
		?>

		<!-- 1차 오픈에서 제외
		<li><span>구매대행</span>
			<ul>
				<li><a href="">EBAY</a></li>
				<li><a href="">AMAZON</a></li>
			</ul>
		</li>
		<li><span>배송대행</span>
			<ul>
				<li><a href="">신청하기</a></li>
			</ul>
		</li>
		-->
		
		<?
		/*
		<li class="depth1 order" style=''>
		</li>
		*/
		?>
		<li class="depth1">
			<span>Customer Center</span>
			<ul>
				<li><a href='/customer/index.php'>전체보기</a></li>
				<li><a href="/bbs/board.php?bo_table=notice">공지사항</a></li>
				<li><a href="/bbs/board.php?bo_table=FAQ">FAQ</a></li>
				<li><a href="/bbs/board.php?bo_table=suggest">건의사항</a></li>
				<li><a href="/bbs/board.php?bo_table=event">이벤트</a></li>
				<li><a href="/customer/alliance.php">제휴문의</a></li>
			</ul>
		</li>

		<li class="depth1">
			<span>About</span>
			<ul>
				<li><a href="/guide1.php">회사소개</a></li>
			</ul>
		</li>
	</ul>
</nav>
<div>
	<header>
		<div>
<?include_once(G5_GOLDSPOT_PATH."/goldspot_search.php");?>
		</div>
		<div class="head">
			<a href="<?=G5_URL?>">
				<img class="logo" src="<?=G5_IMG_MOBILE_URL?>/logo.png" alt="<?php echo $config['cf_title']; ?>">
			</a>
			<a href="/coto/check_order.php">
				<img class="myBtn" src="<?=G5_IMG_MOBILE_URL?>/myBtn.png">
			</a>
			<a href="/shop/integrationSearchPage.php">
				<img class="searchBtn" src="<?=G5_IMG_MOBILE_URL?>/searchBtn.png">
			</a>
			<a href="/coto/cart.php">
				<img class="searchBtn" src="<?=G5_IMG_MOBILE_URL?>/cartBtn2.png">
			</a>
			<?
			if($member[mb_id]) {
			?>
			<a href="/bbs/logout.php">
				<img class="myBtn" src="<?=G5_IMG_MOBILE_URL?>/logoutBtn.png">
			</a>
			<?
			} else {
			?>
			<a href="/bbs/login.php">
				<img class="myBtn" src="<?=G5_IMG_MOBILE_URL?>/loginBtn.png">
			</a>
			<?
			}
			?>
		</div>
		<div class="barWrap">
			<div class="menuBtn" style='color:white; font-size:1.4em; '><img src="<?=G5_IMG_MOBILE_URL?>/menuBtn.png"> <B>MENU ☜</B></div>
		</div>
	</header>
	<div id="container">