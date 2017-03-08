<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if($it[auc_status] == "2"){
	if(date("m") < 10){
		$cnt_m = substr(date("m"), 1, 1); 
	}else{
		$cnt_m = date("m"); 
	}
	if(date("d") < 10){
		$cnt_d = substr(date("d"), 1, 1); 
	}else{
		$cnt_d = date("d"); 
	}
	if(date("H") < 10){
		$cnt_h = substr(date("h"), 1, 1); 
	}else{
		$cnt_h = date("h"); 
	}
	if(date("i") < 10){
		$cnt_i = substr(date("i"), 1, 1); 
	}else{
		$cnt_i = date("i"); 
	}
	if(date("s") < 10){
		$cnt_s = substr(date("s"), 1, 1); 
	}else{
		$cnt_s = date("s"); 
	}

	$now_date = date("Y").",".$cnt_m.",".$cnt_d.",".$cnt_h.",".$cnt_i.",".$cnt_s;

	$it_last_date1 = substr($it['it_last_date'], 0, 4);
	$it_last_date2 = substr($it['it_last_date'], 4, 2);
	$it_last_date3 = substr($it['it_last_date'], 6, 2);
	$it_last_date4 = substr($it['it_last_date'], 8, 2);
	$it_last_date5 = substr($it['it_last_date'], 10, 2);
	$it_last_date6 = substr($it['it_last_date'], 12, 2);

	if($it_last_date2 < 10){
		$end_date_m = substr($it['it_last_date'], 5, 1);
	}else{
		$end_date_m = substr($it['it_last_date'], 4, 2);
	}

	if($it_last_date3 < 10){
		$end_date_d = substr($it['it_last_date'], 7, 1);
	}else{
		$end_date_d = substr($it['it_last_date'], 6, 2);
	}

	if($it_last_date4 < 10){
		$end_date_h = substr($it['it_last_date'], 9, 1);
	}else{
		$end_date_h = substr($it['it_last_date'], 8, 2);
	}

	if($it_last_date5 < 10){
		$end_date_i = substr($it['it_last_date'], 11, 1);
	}else{
		$end_date_i = substr($it['it_last_date'], 10, 2);
	}

	if($it_last_date6 < 10){
		$end_date_s = substr($it['it_last_date'], 13, 1);
	}else{
		$end_date_s = substr($it['it_last_date'], 12, 2);
	}

	$end_date = $it_last_date1.",".$end_date_m.",".$end_date_d.",".$end_date_h.",".$end_date_i.",".$end_date_s;

	$auc_res1 = sql_query("select * from g5_shop_auction where it_id='$it_id' order by it_last_bid desc limit 0, 1 ");
	$auc_num1 = mysql_num_rows($auc_res1);
	$auc_row1 = mysql_fetch_array($auc_res1);
}
?>



<link rel="stylesheet" href="<?php echo G5_SHOP_SKIN_URL; ?>/style.css">
<link rel="stylesheet" href="<?=G5_URL?>/css/vanillabox.css">
<link rel="stylesheet" type="text/css" href="<?=G5_URL?>/css/default_shop_group.css" />

<script type="text/javascript" src="<?=G5_URL?>/js/jquery.loupe.min.js"></script>
<script type="text/javascript" src="<?=G5_URL?>/js/jquery.vanillabox.js"></script>
<script type="text/javascript" src="<?=G5_URL?>/js/configform.js"></script>
<script type="text/javascript" src="<?=G5_URL?>/js/configpre.js"></script>
<script type="text/javascript">
/*$(document).ready(function(){
	$(".loupe").find("img").css({"width":"500px", "height":"500px"});
});*/
</script>

<form name="fitem" method="post" action="<?php echo $action_url; ?>" onsubmit="return fitem_submit(this);">
<input type="hidden" name="it_id[]" value="<?php echo $it_id; ?>">
<input type="hidden" name="sw_direct">
<input type="hidden" name="url">
<input type="hidden" name="buy_kind" value="투데이스토어">
<input type="hidden" name="ct_bid" value="">

<div id="sit_ov_wrap">
	<div id="sit_titlebox">
		<div style="margin:0px 0 0 0;"><?=$it['it_year']?></div>
        <h2 id="sit_title"><?php echo stripslashes($it['it_name']); ?> <span class="sound_only">요약정보 및 구매</span></h2>

		<h2 id="sit_title2">
		<?if($it[it_price_type] == "Y" || $it[it_price_type] == "U"){?>
			실시간상품
		<?}else{?>
			일반상품
		<?}?>
		</h2>
	</div>
    <!-- 상품이미지 미리보기 시작 item.form.skin { -->
    <div id="sit_pvi" >
        <div id="sit_pvi_big">
        <?php
        $big_img_count = 0;
        $thumbnails = array();
        for($i=1; $i<=10; $i++) {
            if(!$it['it_img'.$i])
                continue;

            $img = get_it_thumbnail1($it['it_img'.$i], $default['de_mimg_width'], $default['de_mimg_height'], '', $i);

            if($img) {
                // 썸네일
                $thumb = get_it_thumbnail1($it['it_img'.$i], 60, 60, '', $i);
                $thumbnails[] = $thumb;
                $big_img_count++;

                //echo '<a class="anum anum'.$i.'" idx="'.$i.'" href="'.G5_SHOP_URL.'/largeimage.php?it_id='.$it['it_id'].'&amp;no='.$i.'" target="_blank" class="popup_item_image">'.$img.'</a>';
				echo '<a href="javascript:void(0);" class="popup_item_image" onclick="show_button(\''.$it_id.'\')">'.$img.'</a>';
				
            }
			/*echo "
			<script type='text/javascript'>
			$('.demo".$i."').loupe();
			</script>
			";*/
        }

        if($big_img_count == 0) {
            echo '<img src="'.G5_SHOP_URL.'/img/no_image.gif" alt="">';
        }
        ?>
        </div>
        <?php
        // 썸네일
        $thumb1 = true;
        $thumb_count = 0;
        $total_count = count($thumbnails);
        if($total_count > 0) {
			echo '<table width=100% bgcolor="#fff" border=0 height=130 cellpadding=0 cellspacing=0 ><tr><td style="border:0px;background:#fff">';


			echo '<table align=center ><tr><td>';
            echo '<ul id="sit_pvi_thumb">';
			
            foreach($thumbnails as $val) {
                $thumb_count++;
                $sit_pvi_last ='';
                if ($thumb_count % 5 == 0) $sit_pvi_last = 'class="li_last"';
                    echo '<li '.$sit_pvi_last.'>';
                    //echo '<a href="'.G5_SHOP_URL.'/largeimage.php?it_id='.$it['it_id'].'&amp;no='.$thumb_count.'" target="_blank" class="popup_item_image img_thumb">'.$val.'<span class="sound_only"> '.$thumb_count.'번째 이미지 새창</span></a>';
					echo '<a href="#none"  class="popup_item_image img_thumb">'.$val.'<span class="sound_only"> '.$thumb_count.'번째 이미지 새창</span></a>';
                    echo '</li>';
            }
			 
            echo '</ul>';
			echo '</td></tr></table>';


			echo '</td></tr></table>';
        }
        ?>

		<ul id="interactive-image-list<?=$it_id?>" style="display:none;">
		<?
		for($k = 1; $k <= 10; $k++){
			if($it["it_img".$k]){
		?>
			<li class='zoom ex<?=$it_id?>'><a href="<?=G5_DATA_URL?>/item/<?=$it["it_img".$k]?>" title="">&nbsp;</a></li>
		<?
			}
		}
		?>
		</ul>
    </div>
    <!-- } 상품이미지 미리보기 끝 -->

	<script type="text/javascript">
	
	function show_button(wr_id){
		// Options Demo
		var configForm = new ConfigForm($('#config-form' + wr_id));
		var configPre = new ConfigPre($('#config-pre' + wr_id));

		var optionBox = null;
		if (optionBox) {
			optionBox.dispose();
		}

		var config = configForm.buildConfig();
		var targetElems = (config.type === 'iframe') ?
			$('#interactive-page-list' + wr_id + ' a') :
			$('#interactive-image-list' + wr_id + ' a');

		optionBox = targetElems.vanillabox(config);
		optionBox.show();

		$(".vnbx-mask").find("img").addClass("ex1");
	}

	</script>

	<p id="sit_desc"><?php echo $it['it_basic']; ?></p>
	<?php if($is_orderable) { ?>
	<p id="sit_opt_info">
		상품 선택옵션 <?php echo $option_count; ?> 개, 추가옵션 <?php echo $supply_count; ?> 개
	</p>
	<?php } ?>
	<div style="float:right;height:45px;margin:0 15px 0 0"><?php echo $sns_share_links; ?></div>


    <!-- 상품 요약정보 및 구매 시작 { -->
    <section id="sit_ov">
		
		
        
		

		<div id="sit_title_wrap">
			<ul>
				<li style="width:199px">가격정보</li>
				<li style="width:199px">선택옵션</li>
				<li style="width:292px">예상구매금액</li>
			</ul>
		</div>

		<div id="sit_title_wrap_content">
			<ul>
				<li style="width:199px">
					<div class="price_en2">		
						<ul>
							<li>구매예상가</li>
							<li style="font-size:25px;text-align:right"> 
								<?php echo display_price(get_price($it)); ?>
								<input type="hidden" id="it_price" value="<?php echo get_price($it); ?>">
							</li>
							<!--<li style="padding-top:53px">
								<div style="font-weight:normal;background:#f5f6f7;border:1px solid #cfcfcf;float:left;width:190px;text-align:center;padding:12px 0">수량 : <?=$it[it_stock_qty]?></div>
							</li>-->
						</ul>		
					</div>
				</li>

				<li style="width:199px">
					 <?php
						if($option_item) {
						?>
						<!-- 선택옵션 시작 { -->
						<section>
							<!--<h3>선택옵션</h3>-->
							<table class="sit_ov_tbl">
							<colgroup>
								<col class="grid_3">
								<col>
							</colgroup>
							<tbody>
							<?php // 선택옵션
							echo $option_item;
							?>
							</tbody>
							</table>
						</section>
						<!-- } 선택옵션 끝 -->
						<?php
						}
						?>

					<?php
						if($supply_item) {
						?>
						<!-- 추가옵션 시작 { -->
						<section>
							
							<table class="sit_ov_tbl">
							<colgroup>
								<col class="grid_3" >
								<col>
							</colgroup>
							<tbody>
							<?php // 추가옵션
							echo $supply_item;
							?>
							</tbody>
							</table>
						</section>
						<!-- } 추가옵션 끝 -->
						<?php
						}
						?>		

					<?php if ($is_orderable) { ?>
						<!-- 선택된 옵션 시작 { -->
						<section id="sit_sel_option" class="cl">
							<h3>선택된 옵션</h3>
							<?php
							if(!$option_item) {
								if(!$it['it_buy_min_qty'])
									$it['it_buy_min_qty'] = 1;
							?>
							<ul id="sit_opt_added" style="background:#f9f9fa">
								<li class="sit_opt_list" >
									<input type="hidden" name="io_type[<?php echo $it_id; ?>][]" value="0">
									<input type="hidden" name="io_id[<?php echo $it_id; ?>][]" value="">
									<input type="hidden" name="io_value[<?php echo $it_id; ?>][]" value="<?php echo $it['it_name']; ?>">
									<input type="hidden" class="io_price" value="0">
									<input type="hidden" class="io_stock" value="<?php echo $it['it_stock_qty']; ?>">
									<!--<span class="sit_opt_subj"><?php// echo $it['it_name']; ?></span>
									<span class="sit_opt_prc"></span>-->
									<!--<div style="float:left;width:165px;text-align:center;">재고수량 : <?=$it[it_stock_qty]?></div>-->
									<div style="float:left;width:150px;">수량 : 
										<button type="button" class="sit_qty_minus btn_frmline" style="background:#fff;color:#828282;padding:0 9px;border-top:1px #cfcfcf solid;border-left:1px #cfcfcf solid;border-bottom:1px #cfcfcf solid;">-</button>
										<input type="text" name="ct_qty[<?php echo $it_id; ?>][]" value="<?php echo $it['it_buy_min_qty']; ?>" class="frm_input" size="5" style="border:1px #cfcfcf solid;text-align:center;">
										<button type="button" class="sit_qty_plus btn_frmline" style="background:#fff;color:#828282;padding:0 9px;border-top:1px #cfcfcf solid;border-right:1px #cfcfcf solid;border-bottom:1px #cfcfcf solid;">+</button>
									</div>
								</li>
							</ul>
							<script>
								$(function() {
									price_calculate();
								});
							</script>
							<?php } ?>
						</section>
						<!-- } 선택된 옵션 끝 -->
					   <!-- <div id="sit_tot_price"></div>-->
					<?php } ?>

				</li>

				<li style="width:292px">	
					
						
					<div id="sit_tot_price"></div>
					
					 <div id="sit_ov_btn">
						<?php if ($is_orderable) { ?>
						<input type="image" src="<?=G5_URL?>/img/store_cart_btn.gif" onclick="document.pressed=this.value;" value="장바구니" class="input" />
						<input type="image" src="<?=G5_URL?>/img/store_buy_btn.gif" onclick="document.pressed=this.value;" value="바로구매" class="input" />

						<!--<input type="submit" onclick="document.pressed=this.value;" value="바로구매" id="sit_btn_buy">
						<input type="submit" onclick="document.pressed=this.value;" value="장바구니" id="sit_btn_cart">
						-->
						<?php } ?>
						<!--
						<a href="javascript:item_wish(document.fitem, '<?php// echo $it['it_id']; ?>');" id="sit_btn_wish">위시리스트</a>
						<a href="javascript:popup_item_recommend('<?php// echo $it['it_id']; ?>');" id="sit_btn_rec">추천하기</a>
						-->
					</div>
				
				
				</li>
			</ul>
		</div>



		 <!-- 총 구매액 
		<div class="price_en2">		
			<ul>
				<li>예상구매금액</li>
				<li> <div id="sit_tot_price"></div></li>
			</ul>		
		</div>-->

        <table class="sit_ov_tbl">
        <colgroup>
            <col class="grid_3">
            <col>
        </colgroup>
        <tbody>

        <?php if (!$it['it_use']) { // 판매가능이 아닐 경우 ?>
        <!--
		<tr>
            <th scope="row">판매가격</th>
            <td>판매중지</td>
        </tr>
		-->
        <?php } else if ($it['it_tel_inq']) { // 전화문의일 경우 ?>
        <!--
		<tr>
            <th scope="row">판매가격</th>
            <td>전화문의</td>
        </tr>
		-->
        <?php } else { // 전화문의가 아닐 경우?>
		<!--
        <tr>
            <th scope="row">판매가격</th>
            <td>
                <?php echo display_price(get_price($it)); ?>
                <input type="hidden" id="it_price" value="<?php echo get_price($it); ?>">
            </td>
        </tr>-->
		<input type="hidden" id="it_price" value="<?php echo get_price($it); ?>">
        <?php } ?>

        <?php
        /* 재고 표시하는 경우 주석 해제
        <tr>
            <th scope="row">재고수량</th>
            <td><?php echo number_format(get_it_stock_qty($it_id)); ?> 개</td>
        </tr>
        */
        ?>

        <!--<?php if ($config['cf_use_point']) { // 포인트 사용한다면 ?>
        <tr>
            <th scope="row">포인트</th>
            <td>
                <?php
                $it_point = get_item_point($it);
                echo number_format($it_point);
                ?> 점
            </td>
        </tr>
        <?php } ?>-->
        <?php
        $ct_send_cost_label = '배송비결제';

        if($default['de_send_cost_case'] == '무료')
            $sc_method = '무료배송';
        else
            $sc_method = '주문시 결제';

        if($it['it_sc_type'] == 1)
            $sc_method = '무료배송';
        else if($it['it_sc_type'] > 1) {
            if($it['it_sc_method'] == 1)
                $sc_method = '수령후 지불';
            else if($it['it_sc_method'] == 2) {
                $ct_send_cost_label = '<label for="ct_send_cost">배송비결제</label>';
                $sc_method = '<select name="ct_send_cost" id="ct_send_cost">
                                  <option value="0">주문시 결제</option>
                                  <option value="1">수령후 지불</option>
                              </select>';
            }
            else
                $sc_method = '주문시 결제';
        }
        ?>

		<!-- 배송비 선택 { -->
		<table style="border:0;margin:10px;padding:5px;border:0px solid #cfcfcf;width:400px;font-size:15px;">
		<!--
		<tr>
            <th style="text-align:left;width:80px"><?php echo $ct_send_cost_label; ?></th>
            <td><?php echo $sc_method; ?></td>
        </tr>
		-->
		</table>
		<input type="hidden" name="ct_send_cost" id="ct_send_cost" value="<?=$sc_method?>">


       
        <?php if($it['it_buy_min_qty']) { ?>
       <!-- <tr>
            <th>최소구매수량</th>
            <td><?php // echo number_format($it['it_buy_min_qty']); ?> 개<td>
        </tr>-->
        <?php } ?>
        <?php if($it['it_buy_max_qty']) { ?>
        <tr>
            <th>최대구매수량</th>
            <td><?php echo number_format($it['it_buy_max_qty']); ?> 개<td>
        </tr>
        <?php } ?>

		<!--<tr>
			<td><div style="float:right;height:45px;"><?php // echo $sns_share_links; ?></div></td>
		</tr>-->

		<tr>
			<td style="padding:0 0 10px 0;">

				<div style="clear:both;border:0px #cfcfcf solid;background:#f5f6f8;">

                <table border="0" cellspacing="0" cellpadding="0" width="100%">
					<?
					$op_res = sql_query("select * from {$g5['g5_shop_option1_table']} where it_id='".$it_id."' and gubun='N' order by no asc ");
					for($i = 0; $op_row = mysql_fetch_array($op_res); $i++){
					?>
					<tr>
						<td style="width:20%;padding:10px 0 10px 20px;"><?=$op_row[con]?></td>
						<td style="width:80%;">
							<select name="it_option[]">
								<option value="">선택없음</option>
								<?
								$op2_res = sql_query("select * from {$g5['g5_shop_option2_table']} where num='".$op_row[no]."' and it_id='".$it_id."' order by no asc ");
								for($k = 0; $op2_row = mysql_fetch_array($op2_res); $k++){
								?>
								<option value="<?=$op2_row[con]?>"><?=$op2_row[con]?>(+<?=$op2_row[price]?>)</option>
								<?
								}
								?>
							</select>
						</td>
					</tr>
					<?
					}
					?>
				</table>

				</div>

            </td>
		</tr>
		
		<input type="hidden" name="op_price" value="">
		<input type="hidden" name="op_name" value="">

        </tbody>
        </table>
		

		<script>
		$(document).ready(function(){
			$("select[name^='it_option']").change(function(){
				var price = 0;
				var it_price = $("input[name='it_price']").val();
				var con = "";
				
				$("select[name^='it_option']").each(function(i){
					con += $("select[name^='it_option']").eq(i).val() + "|";
				});

				con = con.substring(0, con.length-1);

				$("input[name='op_name']").val(con);

				$.ajax({
					type: "POST",
					dataType: "HTML",
					url: "./_Ajax.option.php",
					data: "it_id=<?=$it_id?>&gubun=N&op_name=" + con,
					success: function(data){
						//$(".test").html(data);
						price = parseInt(data);
						$("input[name='op_price']").val(price);

						price_calculate();
					}
				});

			});
		});
		</script>
		



       

        

		<div id="sit_star_sns">
            <!--<?php// if ($star_score) { ?>
            고객평점 <span>별<?php// echo $star_score?>개</span>
            <img src="<?php// echo G5_SHOP_URL; ?>/img/s_star<?php// echo $star_score?>.png" alt="" class="sit_star">
            <?php// } ?>
			-->
           <!-- <?php // echo $sns_share_links; ?>-->
        </div>


       
		<section style="margin:20px 0 0 0;padding:0px;position:relative;top:100px">
            <table width="100%">
            <colgroup>
                <col class="grid_3">
                <col>
            </colgroup>
            <tbody>
				<tr>
					<td align="left">결제방식</td>
					<td align="right">
						<input type="radio" name="buy_status" value="무통장" checked>
						송금결제
					</td>
				</tr>
            </tbody>
            </table>
        </section>


		<!--<div style="margin:0 0 15px 0;height:1px;border-top:1px #cfcfcf solid;"></div>-->

        <?php if($is_soldout) { ?>
        <p id="sit_ov_soldout">상품의 재고가 부족하여 구매할 수 없습니다.</p>
        <?php } ?>

     
        </div>

    </section>
    <!-- } 상품 요약정보 및 구매 끝 -->

        <!-- 다른 상품 보기 시작 { -->
        <div id="sit_siblings">
            <?php
            if ($prev_href || $next_href) {
                echo $prev_href.$prev_title.$prev_href2;
                echo $next_href.$next_title.$next_href2;
            } else {
                echo '<span class="sound_only">이 분류에 등록된 다른 상품이 없습니다.</span>';
            }
            ?>
        </div>
        <!-- } 다른 상품 보기 끝 -->
</div>

</form>

<script type="text/javascript" src="<?=G5_URL?>/js/common_product.js"></script>