<?php
include_once('./_common.php');
include_once(G5_MSHOP_PATH.'/head.php');



// 스킨을 지정했다면 지정한 스킨을 사용함 (스킨의 다양화)
//if ($skin) $ca[ca_skin] = $skin;

// 보안서버경로
if (G5_HTTPS_DOMAIN)
	$action_url = G5_HTTPS_DOMAIN.'/'.G5_SHOP_DIR.'/gp_cartupdate.php';
else
	$action_url = './gp_cartupdate.php';

?>

<!-- 공동구매 신청 레이어팝업 -->
<div class="mw_layer_gp" style='width:100%;'>
	<div class="bg_gp"></div>

	<div id="layer_gp" style='width:100%;'>
		<div style="width:100%;font-size:17px;text-align:right"><a href="#" class="close_gp" style="padding-right:5px">X</a></div>
		<div class='gp_view_loading' style="width:100%; height:280px; margin-top:150px; font-size:20px; font-weight:bold; color:black;">
			<center>
				데이터를 불러오고 있습니다<br>
				잠시만 기다려주세요<br>
				<img src='/img/ajax-loader.gif' />
			</center>
		</div>
		<div class="gp_view">
		</div>
	</div>
</div>


<form name="fcart" id="fcart" method="POST" action="<?=$action_url?>">
	<input type="hidden" name="gp_id">
	<input type="hidden" name="ca_id">
	<input type="hidden" name="it_price">
	<input type="hidden" name="it_card_price">
	<input type="hidden" name="ct_qty">
	<input type="hidden" name="buy_kind" value="공동구매">
	<input type="hidden" name="sw_direct" value="1">
</form>

<form name="fapmsearch" id="fapmsearch" method="GET" style="display:none;">
	<input type="hidden" name="apmval">
	<input type="hidden" name="ca_id" value="<?=$ca_id?>">

	<div class="apm_search_all">
		<ul>
			<li style="width:30%;border:0px #c4dff2 solid;">
				<div class="logo">
					<a href="<?=G5_URL?>/shop/gplist.php?ca_id=<?=$ca_id?>">

						<?if($search_logo){?>
							<img src="<?=G5_URL?>/img/<?=$search_logo?>" border="0" align="absmiddle" style="height:100%;">
						<?}?>
					</a>
				</div>
			</li>
			<li style="width:70%;border:0px #c4dff2 solid">
				<dl class="search_input" >

					<dd style="width:100%">
						<input type="text" name="sch_val_all" class="sch_val_all" value="<?=$sch_val_all?>" style="">
					</dd>
					<dd style="position:absolute;top:8px;bottom:8px;right:10px"><img src="<?=G5_URL?>/img/m/groupSearchBtn.png" border="0" align="absmiddle" class="sch_all_btn"></dd>

				</dl>

			</li>
			<li style="width:100%;height:30px;border:0px;background-color: #d4eaf9;padding-top: 10px;">
				<span style="font-size:0.833em;font-weight:bold;text-align:right;margin-left:10px;padding-right:15px;">인기검색어</span>
				<div style="position:relative;right:0;display: inline;">

					<?
					$apm_hit_res = sql_query("select * from g5_shop_gphit order by sch_cnt desc limit 0, 3 ");
					for($i = 0; $apm_hit_row = mysql_fetch_array($apm_hit_res); $i++){
						?>
						<span class="apm_hit_btn" con="<?=$apm_hit_row[sch_text]?>"><?=$apm_hit_row[sch_text]?></span>
						<?
					}
					?>

				</div>
			</li>
		</ul>
	</div>

</form>

<?
if ($is_admin)
	echo '<div class="cl sct_admin"><a href="'.G5_ADMIN_URL.'/shop_admin/categoryform.php?w=u&amp;ca_id='.$ca_id.'" class="btn_admin">분류 관리</a></div>';
?>

<script>
	var itemlist_ca_id = "<?php echo $ca_id; ?>";
</script>
<script src="<?php echo G5_JS_URL; ?>/shop.list.js"></script>


<span style="clear: both; margin-top:10px; width: 100%; height: 30px; font-size: 1.4em; font-weight: bold; display: block;"><?=$ca[ca_name]?></span>

<!-- 상품 목록 시작 { -->
<div id="sct">

	<?php
	$nav_ca_id = $ca_id;
	//include G5_MSHOP_SKIN_PATH.'/navigation.skin.php';

	// 상단 HTML
	echo '<div id="sct_hhtml">'.stripslashes($ca['ca_head_html']).'</div>';

	//include G5_MSHOP_SKIN_PATH.'/listcategory.skin.php';
	//$error = '<p class="sct_noitem">등록된 상품이 없습니다.</p>';

	// 리스트 유형별로 출력
	$list_file = G5_MSHOP_SKIN_PATH.'/'.$ca['ca_mobile_skin'];

	if (file_exists($list_file)) {
		/*
				echo '<div id="sct_sortlst">';
				include G5_SHOP_SKIN_PATH.'/list.sort.skin.php';
		
				// 상품 보기 타입 변경 버튼
				include G5_SHOP_SKIN_PATH.'/list.sub.skin.php';
				echo '</div>';
		*/
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

		#$sch_que .= ' AND PO.po_cash_price > 0 ';
		/*상품목록*/
		$order_by = "T.ac_yn DESC, T.ac_enddate	ASC";

		$list = new auction_list('list.auc.skin.php', 4, 8, 170, 170, '', $listnum);
		$list->set_mobile(true);
		$list->set_is_page(true);
		$list->set_acyn('Y');
		$list->set_order_by($order_by);
		$list->set_from_record(0);
		$list->set_view('gp_img', true);
		$list->set_view('gp_id', false);
		$list->set_view('gp_name', true);
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


	$order_by = "T.ac_yn DESC, T.ac_enddate	DESC";

	$list = new auction_list('list.auc.skin.php', 4, 8, 170, 170, $sch_que, $listnum);
	$list->set_mobile(true);
	$list->set_is_page(true);
	$list->set_acyn('N');
	$list->set_order_by($order_by);
	$list->set_from_record($from_record);
	$list->set_view('gp_img', true);
	$list->set_view('gp_id', false);
	$list->set_view('gp_name', true);
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
	//echo get_paging($config['cf_write_pages'], $page, $total_page, $_SERVER['PHP_SELF'].'?'.$qstr1.'&amp;apmval='.$apmval.'&amp;apm_type='.$apm_type.'&amp;sch_val='.$sch_val.'&amp;page=');
	
	
	
	
	?>
	<script>
		$(function() {
			$('#page-selection').bootpag({
				total: <?=$total_page?>,
				page:<?=$page?>,
				maxVisible: 6
			}).on("page", function(event, num){
				location.href = "<?=$_SERVER['PHP_SELF']."?gpcode=$gpcode&event_yn=$event_yn&".$qstr1.'&apmval='.$apmval.'&apm_type='.$apm_type.'&sch_val='.$sch_val.'&page='?>" + num;
			});
		});
	</script>
	<div id="page-selection"></div>
	<?php
	// 하단 HTML
	echo '<div id="sct_thtml">'.stripslashes($ca['ca_tail_html']).'</div>';

	?>
</div>
<!-- } 상품 목록 끝 -->

<!-- 공동구매 관련 자바스크립트 라이브러리 -->
<script>
	var ca_id = '<?=$ca_id?>';
	var apm_type = '<?=$apm_type?>';
	var topmargin = -100;	//공동구매 레이어팝업 보정
</script>

<script type="text/javascript" src="<?=G5_URL?>/js/group_purchase.js"></script>

<?php
include_once(G5_MSHOP_PATH . '/tail.php');

echo "\n<!-- {$ca['ca_skin']} -->\n";
?>
