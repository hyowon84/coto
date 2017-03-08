<?php
include_once('./_common.php');
define('_GROUPSHOP_', true);


if($listnum){
	$listnum = $listnum;
}else{
	$listnum = 32;
}

// 상품 출력순서가 있다면
if ($ca_id == 'GP') {
	$order_by = "gp_order, gp_id desc";
	if($sort != "") $order_by .= "$sort $sortodr, ".$order_by;
}
else { //공구카테고리 이외 카테고리는 랜덤으로..
	//$order_by = 'gp_order, gp_update_time DESC, gp_id DESC';
	$order_by = 'rand()';
}


if(substr($ca_id,0,2) == "AP"){
	$search_logo = "apmex_logo.jpg";
}else if(substr($ca_id,0,2) == "GV"){
	$search_logo = "gain_logo.jpg";
}else if(substr($ca_id,0,2) == "MC"){
	$search_logo = "mcm_logo.jpg";
}else if(substr($ca_id,0,2) == "SC"){
	$search_logo = "scot_logo.jpg";
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

//APM 타입
if(!$apmval){
	if($apm_type == "gold"){
		$sch_que .= " and gp_name like '%gold%' ";
	}else if($apm_type == "silver"){
		$sch_que .= " and gp_name like '%silver%' ";
	}else if($apm_type == "platinum"){
		$sch_que .= " and gp_name like '%platinum%' ";
	}else if($apm_type == "palladium"){
		$sch_que .= " and gp_name like '%palladium%' ";
	}else if($apm_type == "other"){
		$sch_que .= " and gp_name not like '%gold%' and gp_name not like '%silver%' and gp_name not like '%platinum%' and gp_name not like '%palladium%' ";
	}else{
		$sch_que .= "";
	}
}

//APM 검색 값
if($sch_val_all){
	$sch_val_all_arr = explode(" ", $sch_val_all);

	$sch_que .= "AND	( ( 1=1 ";
	for($i = 0; $i < count($sch_val_all_arr); $i++){
		$sch_que .= " AND gp_name like '%".$sch_val_all_arr[$i]."%' ";
	}
	$sch_que .= ")";

	$sch_que .= " OR gp_id like '%".$sch_val_all."%' ) ";
}
if($apmval){
	$apmval_arr = explode("|", $apmval);
	for($i = 0; $i < count($apmval_arr); $i++){
		if($apmval_arr[$i] == "bar&round"){
			$val_arr = explode("&", $apmval_arr[$i]);
			$sch_que .= " and (";
			$sch_que1 = "";
			for($a = 0; $a < count($val_arr); $a++){
				$sch_que1 .= " gp_name like '%".$val_arr[$a]."%' or ";
			}

			$sch_que .= substr($sch_que1, 0, strlen($sch_que1)-3).") ";
		}else if(strpos($apmval_arr[$i], "~") !== false){
			$val_arr = explode("~", $apmval_arr[$i]);
			$sch_que .= " and (";
			$sch_que1 = "";

			if($val_arr[1] <= $val_arr[0]){
				for($a = $val_arr[1]; $a <= $val_arr[0]; $a++){
					$sch_que1 .= " gp_name like '%".$a."%' or ";
				}
			}else{
				for($a = $val_arr[0]; $a <= $val_arr[1]; $a++){
					$sch_que1 .= " gp_name like '%".$a."%' or ";
				}
			}

			$sch_que .= substr($sch_que1, 0, strlen($sch_que1)-3).") ";

		}else if(strpos($apmval_arr[$i], "america") !== false){
			$sch_que .= " and (gp_name like '%america%' ";
			$sch_que .= " or gp_name like '%american%' ";
			$sch_que .= " or gp_name like '% us%') ";
		}else{
			$sch_que .= " and gp_name like '%".$apmval_arr[$i]."%' ";
		}
	}

}


//이벤트상품 메뉴 링크 클릭시
if($event_yn == 'y') {
	$sch_que .= "	AND event_yn = 'Y' ";
	$ca_id = 'OD';
}

$sql = "	SELECT		*
					FROM		{$g5['g5_shop_category_table']}
					WHERE		ca_id = '$ca_id'
";
$ca = sql_fetch($sql);



//관리자가 아닌 상황에서 
if ( !($ca['ca_id'] && $ca['ca_use'] == 1) && $is_admin != 'super' ) {
	alert('등록된 분류가 없습니다.');
}
	
	

// 본인인증, 성인인증체크
if(!$is_admin) {
	$msg = shop_member_cert_check($ca_id, 'list');
	if($msg)
		alert($msg);
}

$g5['title'] = $ca['ca_name'].' 상품리스트';

if (G5_IS_MOBILE) {
	include_once(G5_MSHOP_PATH.'/gplist.php');
	return;
}


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

<!-- 공동구매 신청 레이어팝업 -->
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


<!-- APM 통합 검색 -->
<div class="apm_search_all">

	<div class="logo">
		<a href="<?=G5_URL?>/shop/gplist.php?ca_id=<?=$ca_id?>">
			<?if($search_logo){?>
				<img src="<?=G5_URL?>/img/<?=$search_logo?>" border="0" align="absmiddle">
			<?}?>
		</a>
	</div>

	<div class="search_input">
		
		<input type="text" name="sch_val_all" class="sch_val_all" value="<?=$sch_val_all?>"><img src="<?=G5_URL?>/img/sch_all_bn.gif" border="0" align="absmiddle" class="sch_all_btn">
		
	</div>
</div>

</form>

<?
$nav_skin = $skin_dir.'/navigation.skin.php';
if(!is_file($nav_skin))
	$nav_skin = G5_SHOP_SKIN_PATH.'/navigation.skin.php';
include $nav_skin;

// 상단 HTML
echo '<div id="sct_hhtml">'.conv_content($ca['ca_head_html'], 1).'</div>';

$cate_skin = $skin_dir.'/listcategory.skin.php';
if(!is_file($cate_skin))
	$cate_skin = G5_SHOP_SKIN_PATH.'/listcategory.skin.php';
include $cate_skin;
?>
<!-- APM 펼쳐짐 -->
<!--<div class="apm_search">
	<div class="apm_search_title">
		<div>Smart Finder</div>
		<div class="dis_btn">-</div>
	</div>

	<div class="apm_menu" <?if($apmval){?>style="display:block;"<?}else{?>style="display:block;"<?}?>>
		<ul>
			<li>형태</li>
			<li>연도</li>
			<li>국가</li>
			<li>중량</li>
			<li>Graded</li>
			<li style="width:284px;border-right:1px #c3dff2 solid;">Top 25</li>
		</ul>
		<div class="apm_search_sub">
			<ul>
				<li>
					<table border="0" cellpadding="0" cellspacing="0">
						<?
						$apm_sch_res = sql_query("select * from g5_shop_gpsearch_cate where gp_title='유형' ");
						for($i = 0; $apm_sch_row = mysql_fetch_array($apm_sch_res); $i++){
						?>
						<tr>
							<td><input type="checkbox" name="chk_sch[]" value="<?=$apm_sch_row[gp_val]?>" status="유형" <?if(strpos($apmval, $apm_sch_row[gp_val]) !== false){echo "checked";}?>> <?=$apm_sch_row[gp_title_sub]?></td>
						</tr>
						<?
						}
						?>
					</table>
				</li>
				<li>
					<table border="0" cellpadding="0" cellspacing="0">
						<?
						$apm_sch_res = sql_query("select * from g5_shop_gpsearch_cate where gp_title='연도' ");
						for($i = 0; $apm_sch_row = mysql_fetch_array($apm_sch_res); $i++){
						?>
						<tr>
							<td><input type="checkbox" name="chk_sch[]" value="<?=$apm_sch_row[gp_val]?>" status="연도" <?if(strpos($apmval, $apm_sch_row[gp_val]) !== false){echo "checked";}?>> <?=$apm_sch_row[gp_title_sub]?></td>
						</tr>
						<?
						}
						?>
					</table>
				</li>
				<li>
					<table border="0" cellpadding="0" cellspacing="0">

						<?
						$apm_sch_row = sql_fetch("select * from g5_shop_gpsearch_cate where gp_title='국가' and gp_title_sub='미국' ");
						?>
						<tr>
							<td><input type="checkbox" name="chk_sch[]" value="america" status="국가" <?if(strpos($apmval, $apm_sch_row[gp_val]) !== false){echo "checked";}?>> <?=$apm_sch_row[gp_title_sub]?></td>
						</tr>

					</table>
				</li>
				<li>
					<table border="0" cellpadding="0" cellspacing="0">
						<?
						$apm_sch_res = sql_query("select * from g5_shop_gpsearch_cate where gp_title='중량' ");
						for($i = 0; $apm_sch_row = mysql_fetch_array($apm_sch_res); $i++){

						?>
						<tr>
							<td><input type="checkbox" name="chk_sch[]" value="<?=$apm_sch_row[gp_val]?>" status="중량" <?for($a = 0; $a < count($apmval_arr); $a++){if($apmval_arr[$a] == $apm_sch_row[gp_val]){echo "checked";}}?>> <?=$apm_sch_row[gp_title_sub]?></td>
						</tr>
						<?
						}
						?>
					</table>
				</li>
				<li>
					<table border="0" cellpadding="0" cellspacing="0">
						<?
						$apm_sch_res = sql_query("select * from g5_shop_gpsearch_cate where gp_title='Graded' ");
						for($i = 0; $apm_sch_row = mysql_fetch_array($apm_sch_res); $i++){
						?>
						<tr>
							<td><input type="checkbox" name="chk_sch[]" value="<?=$apm_sch_row[gp_val]?>" status="Graded" <?if(strpos($apmval, $apm_sch_row[gp_val]) !== false){echo "checked";}?>> <?=$apm_sch_row[gp_title_sub]?></td>
						</tr>
						<?
						}
						?>
					</table>
				</li>
				<li style="width:284px;border-right:1px #ddd solid;">
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td valign="top" width="130px">
								<table border="0" cellpadding="0" cellspacing="0">
							<?
							$apm_sch_res = sql_query("select * from g5_shop_gpsearch_cate where gp_title='Top 25' ");
							for($i = 0; $apm_sch_row = mysql_fetch_array($apm_sch_res); $i++){
								if(($i + 1) % 11 == 0){
							?>
									<tr>
										<td style="letter-spacing:-0.05em;"><input type="checkbox" name="chk_sch[]" value="<?=$apm_sch_row[gp_val]?>" status="Top 25" <?if(strpos($apmval, $apm_sch_row[gp_val]) !== false){echo "checked";}?>> <?=$apm_sch_row[gp_title_sub]?></td>
									</tr>
								</table>
							</td>
							<td valign="top" width="130px">
								<table border="0" cellpadding="0" cellspacing="0">
							<?
								}else{
							?>
							<tr>
								<td style="letter-spacing:-0.05em;" width="130px"><input type="checkbox" name="chk_sch[]" value="<?=$apm_sch_row[gp_val]?>" status="Top 25" <?if(strpos($apmval, $apm_sch_row[gp_val]) !== false){echo "checked";}?>> <?=$apm_sch_row[gp_title_sub]?></td>
							</tr>
							<?
								}
							}
							?>
								</table>
							</td>
						</tr>
					</table>
				</li>
			</ul>
		</div>
		<div class="apm_sch_status">
			<div style="width:85.5px;background:#f0f0f0;">선택한 속성</div>
			<div class="apm_sch_param">

			<?
			for($i = 0; $i < count($apmval_arr); $i++){
				if($apmval_arr[$i]){
					$apm_sel_row = sql_fetch("select * from g5_shop_gpsearch_cate where gp_val='".$apmval_arr[$i]."' limit 0, 1 ");
			?>
					<div style='float:left;padding:0 0 0 3px;border:0px;'><?=$apm_sel_row[gp_title]?><font color='#74d3d0'><?=$apmval_arr[$i]?></font><img src='<?=G5_URL?>/img/apm_search_del.gif' border='0' align='absmiddle' style='cursor:pointer;' onclick='apm_del_btn(this, "<?=$apmval_arr[$i]?>");'><input type='hidden' name='apm_val[]' value='<?=$apmval_arr[$i]?>'></div>
			<?
				}
			}
			?>

			</div>
			<div class="apm_all_del">전체삭제</div>
			<div class="apm_submit_btn"><img src="<?=G5_URL?>/img/apm_search_btn.gif" border="0" align="absmiddle"></div>
		</div>
	</div>

</div>-->



<?
//추천상품 카테고리 클릭시에는 숨김처리
// if($event_yn != 'y')
?>

<!-- APM 바
<div class="search_bar">
	<div><input type="checkbox" name="search_btn_all" style="background:#000;" class="checkbox" value="" <?if($sch_val == ""){echo "checked";}?>> ALL</div>

	<?
	$item_type_icon_res = sql_query("select * from {$g5['g5_gp_item_type_icon_table']} order by no desc ");
	for($i = 0; $item_type_icon_row = mysql_fetch_array($item_type_icon_res); $i++){
		$sch_val_arr1 = explode("|", $sch_val);
	?>
		<div><input type="checkbox" name="search_btn[]" value="<?=$item_type_icon_row[no]?>" <?for($k = 0; $k < count($sch_val_arr1); $k++){if($sch_val_arr1[$k] == $item_type_icon_row[no]){echo "checked";}}?>> <?=$item_type_icon_row[tp_name]?></div>
	<?
	}
	?>

	<div style="float:right;text-align:center;cursor:pointer;" onclick="goto_url('./gplist.php?ca_id=<?=$ca_id?>&sort=<?=$sort?>&sortodr=<?=$sortodr?>&apmval=<?=$apmval?>&apm_type=<?=$apm_type?>&sch_val=<?=$sch_val?>&sch_val_all=<?=$sch_val_all?>&page=<?=$page?>&listnum=80');">80개</div>
	<div style="float:right;text-align:center;cursor:pointer;" onclick="goto_url('./gplist.php?ca_id=<?=$ca_id?>&sort=<?=$sort?>&sortodr=<?=$sortodr?>&apmval=<?=$apmval?>&apm_type=<?=$apm_type?>&sch_val=<?=$sch_val?>&sch_val_all=<?=$sch_val_all?>&page=<?=$page?>&listnum=40');">40개</div>
	<div style="float:right;text-align:center;cursor:pointer;" onclick="goto_url('./gplist.php?ca_id=<?=$ca_id?>&sort=<?=$sort?>&sortodr=<?=$sortodr?>&apmval=<?=$apmval?>&apm_type=<?=$apm_type?>&sch_val=<?=$sch_val?>&sch_val_all=<?=$sch_val_all?>&page=<?=$page?>&listnum=20');">20개</div>
	<div style="float:right;text-align:center;cursor:pointer;" onclick="goto_url('./gplist.php?ca_id=<?=$ca_id?>&sort=<?=$sort?>&sortodr=<?=$sortodr?>&apmval=<?=$apmval?>&apm_type=<?=$apm_type?>&sch_val=<?=$sch_val?>&sch_val_all=<?=$sch_val_all?>&page=<?=$page?>&listnum=');">기본</div>
</div>
-->
<?
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
	//$error = '<p class="sct_noitem">등록된 상품이 없습니다.</p>';

	// 리스트 유형별로 출력
	$list_file = G5_SHOP_SKIN_PATH.'/'.$ca['ca_skin'];

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


		/*상품목록*/
		$list = new group_purchase_list($ca['ca_skin'], $ca['ca_list_mod'], $ca['ca_list_row'], $ca['ca_img_width'], $ca['ca_img_height'], $sch_que, $listnum);

		/* 이벤트 진행중 체크시 카테고리id 해제 */
//		if($event_yn != 'y') ;
		
		$list->set_category($ca['ca_id'], 1);
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
	$소스URL = $_SERVER['PHP_SELF']."?event_yn=$event_yn&".$qstr1.'&amp;apmval='.$apmval.'&amp;apm_type='.$apm_type.'&amp;sch_val='.$sch_val.'&amp;sch_val_all='.$sch_val_all.'&amp;listnum='.$listnum.'&amp;page=';
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