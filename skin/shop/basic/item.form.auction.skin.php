<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>

<link rel="stylesheet" href="<?=G5_SHOP_SKIN_URL;?>/style.css">

<form name="fitem" method="post" action="<?=$action_url;?>" onsubmit="return fitem_submit(this);">
<input type="hidden" name="it_id[]" value="<?=$it_id;?>">
<input type="hidden" name="sw_direct">
<input type="hidden" name="url">

<div id="sit_ov_wrap">
    <!-- 상품이미지 미리보기 시작 item.form.auction.skin { -->
    <div id="sit_pvi">
        <div id="sit_pvi_big">
        <?php
        $big_img_count = 0;
        $thumbnails = array();
        for($i=1; $i<=10; $i++) {
            if(!$it['it_img'.$i])
                continue;

            $img = get_it_thumbnail($it['it_img'.$i], $default['de_mimg_width'], $default['de_mimg_height']);

            if($img) {
                // 썸네일
                $thumb = get_it_thumbnail($it['it_img'.$i], 60, 60);
                $thumbnails[] = $thumb;
                $big_img_count++;

                echo '<a href="'.G5_SHOP_URL.'/largeimage.php?it_id='.$it['it_id'].'&amp;no='.$i.'" target="_blank" class="popup_item_image">'.$img.'</a>';
            }
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
            echo '<ul id="sit_pvi_thumb">';
            foreach($thumbnails as $val) {
                $thumb_count++;
                $sit_pvi_last ='';
                if ($thumb_count % 5 == 0) $sit_pvi_last = 'class="li_last"';
                    echo '<li '.$sit_pvi_last.'>';
                    echo '<a href="'.G5_SHOP_URL.'/largeimage.php?it_id='.$it['it_id'].'&amp;no='.$thumb_count.'" target="_blank" class="popup_item_image img_thumb">'.$val.'<span class="sound_only"> '.$thumb_count.'번째 이미지 새창</span></a>';
                    echo '</li>';
            }
            echo '</ul>';
        }
       ?>
    </div>
    <!-- } 상품이미지 미리보기 끝 -->

    <!-- 상품 요약정보 및 구매 시작 { -->
    <section id="sit_ov">
        <h2 id="sit_title"><?=stripslashes($it['it_name']);?> <span class="sound_only">요약정보 및 구매</span></h2>
        <p id="sit_desc"><?=$it['it_basic'];?></p>
        <?php if($is_orderable) {?>
        <p id="sit_opt_info">
            상품 선택옵션 <?=$option_count;?> 개, 추가옵션 <?=$supply_count;?> 개
        </p>
        <?php }?>
        <div id="sit_star_sns">
            <?php if ($star_score) {?>
            고객평점 <span>별<?=$star_score?>개</span>
            <img src="<?=G5_SHOP_URL;?>/img/s_star<?=$star_score?>.png" alt="" class="sit_star">
            <?php }?>
            <?=$sns_share_links;?>
        </div>
        <table class="sit_ov_tbl">
        <colgroup>
            <col class="grid_3">
            <col>
        </colgroup>
        <tbody>
        <?php if ($it['it_maker']) {?>
        <tr>
            <th scope="row">제조사</th>
            <td><?=$it['it_maker'];?></td>
        </tr>
        <?php }?>

        <?php if ($it['it_origin']) {?>
        <tr>
            <th scope="row">원산지</th>
            <td><?=$it['it_origin'];?></td>
        </tr>
        <?php }?>

        <?php if ($it['it_brand']) {?>
        <tr>
            <th scope="row">브랜드</th>
            <td><?=$it['it_brand'];?></td>
        </tr>
        <?php }?>

        <?php if ($it['it_model']) {?>
        <tr>
            <th scope="row">모델</th>
            <td><?=$it['it_model'];?></td>
        </tr>
        <?php }?>

        <?php if (!$it['it_use']) { // 판매가능이 아닐 경우?>
        <tr>
            <th scope="row">판매가격</th>
            <td>판매중지</td>
        </tr>
        <?php } else if ($it['it_tel_inq']) { // 전화문의일 경우?>
        <tr>
            <th scope="row">판매가격</th>
            <td>전화문의</td>
        </tr>
        <?php } else { // 전화문의가 아닐 경우?>
        <tr>
            <th scope="row">판매가격</th>
            <td>
                <?=display_price(get_price($it));?>
                <input type="hidden" id="it_price" value="<?=get_price($it);?>">
            </td>
        </tr>
        <?php }?>

        <?php
        /* 재고 표시하는 경우 주석 해제
        <tr>
            <th scope="row">재고수량</th>
            <td><?=number_format(get_it_stock_qty($it_id));?> 개</td>
        </tr>
        */
       ?>

        <?php if ($config['cf_use_point']) { // 포인트 사용한다면?>
        <tr>
            <th scope="row">포인트</th>
            <td>
                <?php
                $it_point = get_item_point($it);
                echo number_format($it_point);
               ?> 점
            </td>
        </tr>
        <?php }?>
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
        <tr>
            <th><?=$ct_send_cost_label;?></th>
            <td><?=$sc_method;?></td>
        </tr>
        <?php if($it['it_buy_min_qty']) {?>
        <tr>
            <th>최소구매수량</th>
            <td><?=number_format($it['it_buy_min_qty']);?> 개<td>
        </tr>
        <?php }?>
        <?php if($it['it_buy_max_qty']) {?>
        <tr>
            <th>최대구매수량</th>
            <td><?=number_format($it['it_buy_max_qty']);?> 개<td>
        </tr>
        <?php }?>
        </tbody>
        </table>

        <?php
        if($option_item) {
       ?>
        <!-- 선택옵션 시작 { -->
        <section>
            <h3>선택옵션</h3>
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
            <h3>추가옵션</h3>
            <table class="sit_ov_tbl">
            <colgroup>
                <col class="grid_3">
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

        <?php if ($is_orderable) {?>
        <!-- 선택된 옵션 시작 { -->
        <section id="sit_sel_option">
            <h3>선택된 옵션</h3>
            <?php
            if(!$option_item) {
                if(!$it['it_buy_min_qty'])
                    $it['it_buy_min_qty'] = 1;
           ?>
            <ul id="sit_opt_added">
                <li class="sit_opt_list">
                    <input type="hidden" name="io_type[<?=$it_id;?>][]" value="0">
                    <input type="hidden" name="io_id[<?=$it_id;?>][]" value="">
                    <input type="hidden" name="io_value[<?=$it_id;?>][]" value="<?=$it['it_name'];?>">
                    <input type="hidden" class="io_price" value="0">
                    <input type="hidden" class="io_stock" value="<?=$it['it_stock_qty'];?>">
                    <span class="sit_opt_subj"><?=$it['it_name'];?></span>
                    <span class="sit_opt_prc"></span>
                    <div>
                        <input type="text" name="ct_qty[<?=$it_id;?>][]" value="<?=$it['it_buy_min_qty'];?>" class="frm_input" size="5">
                        <button type="button" class="sit_qty_plus btn_frmline">증가</button>
                        <button type="button" class="sit_qty_minus btn_frmline">감소</button>
                    </div>
                </li>
            </ul>
            <script>
            $(function() {
                price_calculate();
            });
            </script>
            <?php }?>
        </section>
        <!-- } 선택된 옵션 끝 -->

        <!-- 총 구매액 -->
        <div id="sit_tot_price"></div>
        <?php }?>

        <?php if($is_soldout) {?>
        <p id="sit_ov_soldout">상품의 재고가 부족하여 구매할 수 없습니다.</p>
        <?php }?>

        <div id="sit_ov_btn">
            <?php if ($is_orderable) {?>
            <input type="submit" onclick="document.pressed=this.value;" value="바로구매" id="sit_btn_buy">
            <input type="submit" onclick="document.pressed=this.value;" value="장바구니" id="sit_btn_cart">
            <?php }?>
            <a href="javascript:item_wish(document.fitem, '<?=$it['it_id'];?>');" id="sit_btn_wish">위시리스트</a>
            <a href="javascript:popup_item_recommend('<?=$it['it_id'];?>');" id="sit_btn_rec">추천하기</a>
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