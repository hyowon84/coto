<?php
include_once('./_common.php');
define('_GROUPSHOP_', true);

$listnum = 32;
$정렬순서 = ($ca_id == 'GP') ? "" : "";

// 상품 출력순서가 있다면
if ($ca_id == 'GP') {
	$order_by = "gp_order, gp_id desc";
	if($sort != "") $order_by .= "$sort $sortodr, ".$order_by;   
}
else { //공구카테고리 이외 카테고리는 랜덤으로..
	//$order_by = 'gp_order, gp_update_time DESC, gp_id DESC';
	$order_by = 'rand()';
}


/* 현재 진행중인 공구정보 로딩 */
$gpinfo_sql = "	SELECT	*
								FROM		gp_info
								WHERE		gpcode = '$gpcode'
								AND			start_date <= DATE_FORMAT(now(),'%Y-%m-%d')
								AND			end_date	>= DATE_FORMAT(now(),'%Y-%m-%d')
								AND			stats IN ('00','10')
";
$공구정보 = sql_fetch($gpinfo_sql);


/* 진행상황이 마감일경우 경고메시지 띄우고 종료 */
if(!$공구정보[stats]) {
	echo "<script>
			alert('해당 공동구매는 마감되었습니다');
			location.href='/';
		</script>";
	exit;
}

if (G5_IS_MOBILE) {
	include_once(G5_MSHOP_PATH.'/list.php');
	return;
}


$sch_val_arr = explode("|", $sch_val);
if($sch_val){
	$sch_que .= " and (";
	for($i = 0; $i < count($sch_val_arr)-1; $i++){
		if($sch_val_arr[$i] != ""){
			$sch_que .= " it_type='".$sch_val_arr[$i]."' or";
		}
	}
	$sch_que = substr($sch_que, 0, strlen($sch_que)-3);
	$sch_que .= ") ";
}



//이벤트상품 메뉴 링크 클릭시
if($event_yn == 'Y') {
	$sch_que .= "	AND event_yn = 'Y' ";
}

$sql = "	SELECT	GI.*
					FROM		gp_info GI
					WHERE		gpcode = '$gpcode'
					AND			stats = '00'
";

$gpinfo = sql_fetch($sql);
if (!$gpinfo['gpcode'])
	alert('진행중인 공구가 없습니다.');



//이벤트상품 메뉴 링크 클릭시
if($event_yn == 'y') {
	$sch_que .= "	AND event_yn = 'Y' ";
	$ca_id = 'OD';
}

$sql = "	SELECT		*
					FROM		{$g5['g5_shop_category_table']}
					WHERE		ca_id = '$ca_id'
					AND			ca_use = '1'
";

$ca = sql_fetch($sql);
if (!$ca['ca_id'])
	alert('등록된 분류가 없습니다.');




$g5['title'] = $ca['ca_name'].' 공동구매';

if ($ca['ca_include_head'])
	@include_once($ca['ca_include_head']);
else
	include_once('./_head.php');

// 스킨을 지정했다면 지정한 스킨을 사용함 (스킨의 다양화)
//if ($skin) $ca[ca_skin] = $skin;

// 보안서버경로
if (G5_HTTPS_DOMAIN)
	$action_url = G5_HTTPS_DOMAIN.'/'.G5_SHOP_DIR.'/gp_cartupdate.php';
else
	$action_url = './gp_cartupdate.php';
?>

	<style type="text/css">
		.apm_type{clear:both;}
		.apm_type ul{padding:0;}
		.apm_type li{float:left;border:1px #7eaed0 solid;cursor:pointer;padding:9px 0 9px 0;font-size:17px;font-weight:bold;text-align:center;width:131.3px;background:#c3dff2;}
		.apm_type .apm_on{border:1px #a6a6a6 solid;background:#efefef;}

		.apm_search{clear:both;}
		.apm_search ul{clear:both;padding:0;margin:0px;}
		.apm_search li{float:left;border-top:1px #c3dff2 solid;border-left:1px #c3dff2 solid;border-bottom:1px #c3dff2 solid;padding:7px;text-align:center;width:118.1px;background:#fff;}
		.apm_search .apm_search_sub li{background:#f5f6f7;height:190px;border-top:0px;background:#fff;border-left:1px #ddd solid;border-bottom:1px #ddd solid;}
		.apm_search .apm_search_sub li table tr td{text-align:left;font-size:10.5px;}
		.apm_search .apm_search_title{background:#c3dff2;height:20px;padding:7px;border:1px #86b3d3 solid;cursor:pointer;}
		.apm_search .apm_search_title div{float:left;}
		.apm_search .apm_search_title .dis_btn{float:right;font-size:20px;cursor:pointer;}
		.apm_search .apm_sch_status{clear:both;}
		.apm_search .apm_sch_status div{float:left;background:#fff;padding:6px 6.5px 6px 6.5px;text-align:center;border-left:1px #ddd solid;border-bottom:1px #ddd solid;}
		.apm_search .apm_sch_status .apm_submit_btn{width:98px;border-right:1px #ddd solid;padding:0;cursor:pointer;}
		.apm_search .apm_sch_status .apm_sch_param{width:522px;text-align:left;overflow:hidden;height:15.5px;}
		.apm_search .apm_sch_status .apm_all_del{width:50px;background:#f0f0f0;cursor:pointer;}

		.apm_search_all{ width:1100px; height:70px; }
		.apm_search_all ul{padding:0;margin:0;}
		.apm_search_all ul li{float:left;height:98px;background:#c3dff2;border-top:1px #86b3d3 solid;border-bottom:1px #86b3d3 solid;width:380px;margin:0 0 15px 0;}
		.apm_search_all .search_input{padding:30px 7px 7px 7px;text-align:center;}
		.apm_search_all .search_input .sch_val_all{border-top:1px #93aa46 solid;border-left:1px #93aa46 solid;border-bottom:1px #93aa46 solid;border-right:0px;width:244px;height:35px;}
		.apm_search_all .search_input .sch_all_btn{cursor:pointer;}
		.apm_search_all .logo{margin:7px 0 0 5px;}
		.apm_search_all .apm_hit_btn{float:left;margin:0 0 0 10px;cursor:pointer;}

		.search_bar{clear:both;}
		.search_bar div{float:left;padding:7px;}

		.apm_menu li{width:84.8px}

	</style>

	<div class="test"></div>

	<form name="fcart" id="fcart" method="POST" action="<?=$action_url?>">
		<input type="hidden" name="gp_id">
		<input type="hidden" name="ca_id">
		<input type="hidden" name="it_price">
		<input type="hidden" name="it_card_price">
		<input type="hidden" name="ct_qty">
		<input type="hidden" name="buy_kind" value="공동구매">
		<input type="hidden" name="sw_direct" value="0">
	</form>

	<form name="fapmsearch" id="fapmsearch" method="GET">
		<input type="hidden" name="apmval">
		<input type="hidden" name="ca_id" value="<?=$ca_id?>">
	</form>

<?
$nav_skin = $skin_dir.'/navigation.skin.php';
if(!is_file($nav_skin))
	$nav_skin = G5_SHOP_SKIN_PATH.'/navigation.skin.php';
include $nav_skin;

//추천상품 카테고리 클릭시에는 숨김처리
// if($event_yn != 'y')
if ($is_admin)
	echo '<div class="cl sct_admin"><a href="'.G5_ADMIN_URL.'/shop_admin/categoryform.php?w=u&amp;ca_id='.$ca_id.'" class="btn_admin">분류 관리</a></div>';
?>

	<script>
		var itemlist_ca_id = "<?php echo $ca_id; ?>";
	</script>
	<script src="<?php echo G5_JS_URL; ?>/shop.list.js"></script>

	<!-- 상품 목록 시작 { -->
	<div id="sct">

		<?php
		$nav_ca_id = $ca_id;
		//include G5_SHOP_SKIN_PATH.'/navigation.skin.php';

		// 상단 HTML
		echo '<div id="sct_hhtml" style="padding-top:15px">'.stripslashes($ca['ca_head_html']).'</div>';

		// include G5_SHOP_SKIN_PATH.'/listcategory.skin.php';
		//$error = '<p class="sct_noitem">등록된 상품이 없습니다.</p>';

		// 리스트 유형별로 출력
		$list_file = G5_SHOP_SKIN_PATH.'/'.$ca['ca_skin'];

		if (file_exists($list_file)) {
			// 총몇개 = 한줄에 몇개 * 몇줄
			if($listnum){
				$items = $listnum;
			}else{
				$items = $ca['ca_list_mod'] * $ca['ca_list_row'];
			}

			// 페이지가 없으면 첫 페이지 (1 페이지)
			if ($page == "") $page = 1;
			// 시작 레코드 구함
			$from_record = ($page - 1) * $items;


			/*상품목록*/
			
			$list = new group_purchase_list('list.50.skin.php', 1, 10, 170, 170, $sch_que, $listnum);
//			$list = new group_purchase_list($ca['ca_skin'], $ca['ca_list_mod'], $ca['ca_list_row'], $ca['ca_img_width'], $ca['ca_img_height'], $sch_que, $listnum);

			/* 이벤트 진행중 체크시 카테고리id 해제 */
			if($event_yn != 'y') $list->set_category($ca['ca_id'], 1);

			$list->set_is_page(true);
			$list->set_order_by($order_by);
			$list->set_from_record($from_record);
			$list->set_view('gp_img', true);
			$list->set_view('gp_id', false);
			$list->set_view('gp_name', true);
			$list->set_view('gc_state', true);
			$list->set_view('it_icon', true);
			$list->set_view('sns', false);
			echo $list->run();

			// where 된 전체 상품수
			$total_count = $list->total_count;
			// 전체 페이지 계산
			$total_page  = ceil($total_count / $items);
		}
		else
		{
			$i = 0;
			$error = '<p class="sct_nofile">'.$ca['ca_skin'].' 파일을 찾을 수 없습니다.<br>관리자에게 알려주시면 감사하겠습니다.</p>';
		}

		if ($i==0)
		{
			echo '<div>'.$error.'</div>';
		}
		?>

		<?php
		$qstr1 .= 'ca_id='.$ca_id;
		if($skin)
			$qstr1 .= '&amp;skin='.$skin;
		$qstr1 .="&sort=$sort&sortodr=$sortodr";
		$소스URL = $_SERVER['PHP_SELF']."?gpcode=$gpcode&event_yn=$event_yn&".$qstr1.'&amp;apmval='.$apmval.'&amp;apm_type='.$apm_type.'&amp;sch_val='.$sch_val.'&amp;sch_val_all='.$sch_val_all.'&amp;listnum='.$listnum.'&amp;page=';
		echo get_paging($config['cf_write_pages'], $page, $total_page, $소스URL);
		?>

		<?php
		// 하단 HTML
		echo '<div id="sct_thtml">'.stripslashes($ca['ca_tail_html']).'</div>';

		?>
	</div>
	<!-- } 상품 목록 끝 -->

	<script>
		var ca_id = '<?=$ca_id?>';
		var apm_type = '<?=$apm_type?>';
		var topmargin = -150;	//공동구매 레이어팝업 보정
	</script>
	<script type="text/javascript" src="<?=G5_URL?>/js/group_purchase.js"></script>

<?php
if ($ca['ca_include_tail'])
	@include_once($ca['ca_include_tail']);
else
	include_once('./_tail.php');

echo "\n<!-- {$ca['ca_skin']} -->\n";
?>