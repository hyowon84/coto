<?php
include_once('./_common.php');
define('_GROUPSHOP_', true);

if($listnum){
	$listnum = $listnum;
}else{
	$listnum = 50;
}


$정렬순서 = ($ca_id == 'AC') ? "" : "";

// 상품 출력순서가 있다면
if ($ca_id == 'GP') {
	$order_by = "gp_order, gp_id desc";
	if($sort != "") $order_by .= "$sort $sortodr, ".$order_by;
}



if (G5_IS_MOBILE) {
	include_once(G5_MSHOP_PATH.'/auclist.php');
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


$sql = "	SELECT		*
					FROM		{$g5['g5_shop_category_table']}
					WHERE		ca_id = 'AC'
					AND			ca_use = '1'
";

$ca = sql_fetch($sql);


$g5['title'] = $ca['ca_name'].'경매상품 목록';

if ($ca['ca_include_head'])
	@include_once($ca['ca_include_head']);
else
	include_once('./_head.php');

// 스킨을 지정했다면 지정한 스킨을 사용함 (스킨의 다양화)
//if ($skin) $ca[ca_skin] = $skin;

?>
<form name="fapmsearch" id="fapmsearch" method="GET">
	<input type="hidden" name="apmval">
	<input type="hidden" name="ca_id" value="<?=$ca_id?>">
</form>
<br>


<?
//추천상품 카테고리 클릭시에는 숨김처리
// if($event_yn != 'y')
if ($is_admin)
	echo '<div class="cl sct_admin"><a href="'.G5_ADMIN_URL.'/shop_admin/categoryform.php?w=u&amp;ca_id='.$ca_id.'" class="btn_admin">분류 관리</a></div>';
?>
<style>
	body {background:white;}
</style>

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
			$order_by = "T.ac_yn DESC, T.ac_enddate	ASC";
			$list = new auction_list('list.auc.skin.php', 4, 14, 170, 170, '', '100');
			$list->set_is_page(false);
			$list->set_order_by($order_by);
			$list->set_from_record(0);
			$list->set_acyn('Y');
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


		/* 경매종료 상품목록*/
		$order_by = "T.ac_enddate	DESC";
		$list = new auction_list('list.auc.skin.php', 4, 8, 170, 170, $sch_que, $listnum);
		$list->set_is_page(true);
		$list->set_order_by($order_by);
		$list->set_from_record($from_record);
		$list->set_acyn('N');
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


		$qstr1 .= 'ca_id='.$ca_id;
		if($skin)
			$qstr1 .= '&amp;skin='.$skin;
		$qstr1 .="&sort=$sort&sortodr=$sortodr";
		$소스URL = $_SERVER['PHP_SELF']."?".$qstr1.'&amp;apmval='.$apmval.'&amp;apm_type='.$apm_type.'&amp;sch_val='.$sch_val.'&amp;sch_val_all='.$sch_val_all.'&amp;listnum='.$listnum.'&amp;page=';
		echo get_paging($config['cf_write_pages'], $page, $total_page, $소스URL);
		
		
		
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