<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>
	<section class="itemdtl_orderitem">
		<span class='fontst_orditem1'>이상품을 본 고객이</span> <span class='fontst_orditem2'>구매한 다른 상품</span>
		<!-- 회원의 다른구매한 상품 목록 -->
		<div class="sct_wrap">
			<?php
			$sql = " select b.* from {$g5['g5_shop_item_relation_table']} a left join {$g5['g5_shop_item_table']} b on (a.it_id2=b.it_id) where a.it_id = '{$it['it_id']}' and b.it_use='1' ";
			$list = new item_list($default['de_rel_list_skin'], $default['de_rel_list_mod'], 0, $default['de_rel_img_width'], $default['de_rel_img_height']);
			$list->set_query($sql);
			
			if($list->total_count > 0) echo $list->run();
			else echo "<center><br>관련 상품이 존재하지 않습니다.<br></center>";
			?>
		</div>
	</section>
	
	<section class="itemdtl_tab"  name='tab1'>
		<nav title='상품정보'>
			<a name='tab1'>
			<a href='#tab1' class='itemdtl_tab_bt n on'>상품정보</a>
			<a href='#tab2' class='itemdtl_tab_bt n'>상품Q&A</a>
			<a href='#tab3' class='itemdtl_tab_bt n'>고객상품평</a>
			<a href='#tab4' class='itemdtl_tab_bt n last'>배송안내</a>
			</a>
		</nav>
		<aside>
			<h3>상품 기본설명</h3>
			<?php if ($it['it_basic']) { // 상품 기본설명 ?>
			<div id="sit_inf_basic">
				 <?php echo $it['it_basic']; ?>
			</div>
			<?php } ?>
		
			<h3>상품 상세설명</h3>
			<?php if ($it['it_explan']) { // 상품 상세설명 ?>
			<div id="sit_inf_explan">
				<?php echo conv_content($it['it_explan'], 1); ?>
			</div>
			<?php } ?>
		
			<h3>상품 정보 고시</h3>
			<?php
			if ($it['it_info_value']) {
				$info_data = unserialize($it['it_info_value']);
				$gubun = $it['it_info_gubun'];
				$info_array = $item_info[$gubun]['article'];
			?>
			<!-- 상품정보고시 -->
			<table id="sit_inf_open">
			<colgroup>
				<col class="grid_4">
				<col>
			</colgroup>
			<tbody>
			<?php
			foreach($info_data as $key=>$val) {
				$ii_title = $info_array[$key][0];
				$ii_value = $val;
			?>
			<tr>
				<th scope="row"><?php echo $ii_title; ?></th>
				<td><?php echo $ii_value; ?></td>
			</tr>
			<?php } //foreach?>
			</tbody>
			</table>
			<!-- 상품정보고시 end -->
			<?php } //if?>
		</aside>
	</section>
	
	<section class="itemdtl_tab">
		<nav title='상품Q&A'>
			<a name='tab2'>
			<a href='#tab1' class='itemdtl_tab_bt n'>상품정보</a>
			<a href='#tab2' class='itemdtl_tab_bt n on'>상품Q&A</a>
			<a href='#tab3' class='itemdtl_tab_bt n'>고객상품평</a>
			<a href='#tab4' class='itemdtl_tab_bt n last'>배송안내</a>
			</a>
		</nav>
		<aside>
			<div id="itemqa"><?php include_once('./itemqa.php'); ?></div>
		</aside>
	</section>
	
	<section class="itemdtl_tab">
		<nav title='고객상품평'>
			<a name='tab3'>
			<a href='#tab1' class='itemdtl_tab_bt n'>상품정보</a>
			<a href='#tab2' class='itemdtl_tab_bt n'>상품Q&A</a>
			<a href='#tab3' class='itemdtl_tab_bt n on'>고객상품평</a>
			<a href='#tab4' class='itemdtl_tab_bt n last'>배송안내</a>
			</a>
		</nav>
		<aside>
			<div id="itemuse"><?php include_once('./itemuse.php'); ?></div>
		</aside>
	</section>
	
	<section class="itemdtl_tab" >
		<nav title='배송정보'>
			<a name='tab4'>
			<a href='#tab1' class='itemdtl_tab_bt n'>상품정보</a>
			<a href='#tab2' class='itemdtl_tab_bt n'>상품Q&A</a>
			<a href='#tab3' class='itemdtl_tab_bt n'>고객상품평</a>
			<a href='#tab4' class='itemdtl_tab_bt n last on'>배송안내</a>
			</a>
		</nav>
		<aside>
			<img src='/mobile/shop/img/itemdtl_baesong.jpg' width='100%' />
		</aside>
	</section>
	
	<!-- section class="itemdtl_tab">
		<nav title='오류신고'>
			<a name='tab2'>
			<a href='#tab1' class='itemdtl_tab_bt n'>상품정보</a>
			<a href='#tab2' class='itemdtl_tab_bt n'>상품Q&A</a>
			<a href='#tab3' class='itemdtl_tab_bt n'>고객상품평</a>
			<a href='#tab4' class='itemdtl_tab_bt n'>배송안내</a>
			<a href='#tab5' class='itemdtl_tab_bt last on'>오류신고</a>
			</a>
		</nav>
		<aside>
			<?php echo conv_content($default['de_change_content'], 1); ?>
		</aside>
	</section-->