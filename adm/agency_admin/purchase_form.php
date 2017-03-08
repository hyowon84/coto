<?php
$sub_menu = '600100';
include_once('./_common.php');

$cart_title3 = '주문번호';
$cart_title4 = '배송완료';

auth_check($auth[$sub_menu], "w");

$g5['title'] = "구매대행 내역 수정";
include_once(G5_ADMIN_PATH.'/admin.head.php');


//------------------------------------------------------------------------------
// 주문서 정보
//------------------------------------------------------------------------------
$sql = " select * from {$g5['g5_purchase_order_table']} where od_id = '$od_id' ";
$od = sql_fetch($sql);
if (!$od['od_id']) {
    alert("해당 주문번호로 주문서가 존재하지 않습니다.");
}

$od['mb_id'] = $od['mb_id'] ? $od['mb_id'] : "비회원";
//------------------------------------------------------------------------------


$pg_anchor = '<ul class="anchor">
<li><a href="#anc_sodr_list">주문상품 목록</a></li>
<li><a href="#anc_sodr_pay">주문결제 내역</a></li>
<li><a href="#anc_sodr_chk">결제상세정보 확인</a></li>
<li><a href="#anc_sodr_paymo">결제상세정보 수정</a></li>
<li><a href="#anc_sodr_memo">상점메모</a></li>
<li><a href="#anc_sodr_orderer">주문하신 분</a></li>
<li><a href="#anc_sodr_taker">받으시는 분</a></li>
</ul>';

$html_receipt_chk = '<input type="checkbox" id="od_receipt_chk" value="'.$od['od_misu'].'" onclick="chk_receipt_price()">
<label for="od_receipt_chk">결제금액 입력</label><br>';

$qstr = "sort1=$sort1&amp;sort2=$sort2&amp;sel_field=$sel_field&amp;search=$search&amp;page=$page";
?>

<section id="anc_sodr_list">
    <h2 class="h2_frm">주문상품 목록</h2>
    <?php echo $pg_anchor; ?>
    <div class="local_desc02 local_desc">
        <p>
            현재 주문상태 <strong><?php echo $od['od_status'] ?></strong>
            |
            주문일시 <strong><?php echo substr($od['od_time'],0,16); ?> (<?php echo get_yoil($od['od_time']); ?>)</strong>
            |
            주문총액 <strong><?php echo number_format($od['od_cart_price'] + $od['od_send_cost'] + $od['od_send_cost2']); ?></strong>원
        </p>
    </div>

	 <div class="local_desc01 local_desc">
        <p>구매 사이트는 <b><?php echo $od[od_site]?></b> 입니다.</p>
        <p>상품이 품절 및 가격변동시 <b><?php echo $purchaseExceptionList[$od[od_exception]]?></b> 로 해주세요.</p>
    </div>


    <form name="frmorderform" method="post" action="./purchase_cart_update.php" onsubmit="return form_submit(this);">
    <input type="hidden" name="od_id" value="<?php echo $od_id; ?>">
    <input type="hidden" name="mb_id" value="<?php echo $od['mb_id']; ?>">
    <input type="hidden" name="od_email" value="<?php echo $od['od_email']; ?>">
    <input type="hidden" name="sort1" value="<?php echo $sort1; ?>">
    <input type="hidden" name="sort2" value="<?php echo $sort2; ?>">
    <input type="hidden" name="sel_field" value="<?php echo $sel_field; ?>">
    <input type="hidden" name="search" value="<?php echo $search; ?>">
    <input type="hidden" name="page" value="<?php echo $page;?>">

    <div class="tbl_head01 tbl_wrap">
		<strong class="sodr_nonpay">주문 상태 : <?php echo getSelectArrayList2($purchaseOrderStatus,"od_status","od_status",$od['od_status']);?></strong>
        <table>
        <caption>주문 상품 목록</caption>
        <thead>
        <tr>
			<th scope="col">구분</th>
            <th scope="col">상품명</th>
            <th scope="col">옵션항목</th>
            <th scope="col">판매가</th>
            <th scope="col">수량</th>            
            <th scope="col">소계</th>
			<th scope="col">상태</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $chk_cnt = 0;

		// 상품목록
		$sql = " select pc_num, pc_item, pc_item_url, pc_item_option, pc_type, pc_price, pc_qty, pc_status from {$g5['g5_purchase_cart_table']}
				  where od_id = '{$od['od_id']}' 
				  order by pc_num  ";
		$result = sql_query($sql);

        for($i=0; $row=sql_fetch_array($result); $i++) {
            ?>
			<input type="hidden" name="pc_num[]" value="<?php echo $row[pc_num]?>">
            <tr>
				<td class="td_mngsmall"><?php echo $purchaseShoppingType[$row[pc_type]];?></td>
                <td>
                    <a href="<?php echo $row[pc_item_url]?>" target="_blank"><?php echo stripslashes($row['pc_item']); ?></a></td>
                <td><?php echo $row['pc_item_option']; ?></td>
				<?php if($row[pc_type]=="N"){?>
                <td class="td_num">USD <?php echo $row['pc_price']; ?></td>                
                <td class="td_num"><?php echo $row['pc_qty']; ?></td>
                <td class="td_num">USD <?php echo number_format($row['pc_price']*$row['pc_qty'],2); ?></td>
				<td class="td_mngsmall"><?php echo getSelectArrayList2($purchaseCartStatus1,"pc_status[".$row[pc_num]."]","pc_status_".$row[pc_num],$row['pc_status']);?></td>
				<?php }else{?>
				<td class="td_num" colspan="3"><b>USD <?php echo $row['pc_price']; ?></b> 내에 입찰 요청</td>
				<td class="td_mngsmall"><?php echo getSelectArrayList2($purchaseCartStatus2,"pc_status[".$row[pc_num]."]","pc_status_".$row[pc_num],$row['pc_status']);?></td>
				<?php }?>
            </tr>
            <?php
        }
        ?>
        </tbody>
        </table>

    </div>

    <div class="btn_confirm01 btn_confirm">
        <input type="submit" value="주문 및 상품 상태변경" class="btn_submit">
        <a href="./purchase_list.php?<?php echo $qstr; ?>">목록</a>
    </div>

   
    </form>

    <?php if ($od['od_mod_history']) { ?>
    <section id="sodr_qty_log">
        <h3>상품 수량변경 내역</h3>
        <div>
            <?php echo conv_content($od['od_mod_history'], 0); ?>
        </div>
    </section>
    <?php } ?>

</section>

<section id="anc_sodr_pay">
    <h2 class="h2_frm">주문결제 내역</h2>
    <?php echo $pg_anchor; ?>

    <?php
    // 주문금액 = 상품구입금액 + 배송비 + 추가배송비
    $amount['order'] = $od['od_cart_price'] + $od['od_send_cost'] + $od['od_send_cost2'];

    // 입금액 = 결제금액 + 포인트
    $amount['receipt'] = $od['od_receipt_price'] + $od['od_receipt_point'];

    // 쿠폰금액
    $amount['coupon'] = $od['od_cart_coupon'] + $od['od_coupon'] + $od['od_send_coupon'];

    // 취소금액
    $amount['cancel'] = $od['od_refund_price'];

    // 미수금 = 주문금액 - 취소금액 - 입금금액 - 쿠폰금액
    //$amount['미수'] = $amount['order'] - $amount['receipt'] - $amount['coupon'];

    // 결제방법
    $s_receipt_way = $od['od_settle_case'];

    if ($od['od_receipt_point'] > 0)
        $s_receipt_way .= "+포인트";
    ?>

    <div class="tbl_head01 tbl_wrap">
        <strong class="sodr_nonpay">미수금 <?php echo display_price($od['od_misu']); ?></strong>

        <table>
        <caption>주문결제 내역</caption>
        <thead>
        <tr>
            <th scope="col">주문번호</th>
            <th scope="col">주문총액</th>
            <th scope="col">입금요청액</th>
            <th scope="col">추가비용</th>
            <th scope="col">총결제액</th>
            <th scope="col">환불금액</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><?php echo $od['od_id']; ?></td>
            <td class="td_numbig td_numsum">USD <?php echo $od['od_cart_item_price']; ?></td>
            <td class="td_numbig td_numsum"><?php echo display_price($od['od_cart_price']); ?></td>
            <td class="td_numbig"><?php echo display_price($od['od_send_cost2']); ?></td>
            <td class="td_numbig td_numincome"><?php echo number_format($amount['receipt']); ?>원</td>
            <td class="td_numbig td_numcancel"><?php echo number_format($amount['cancel']); ?>원</td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<section class="">
    <h2 class="h2_frm">결제상세정보</h2>
    <?php echo $pg_anchor; ?>

    <form name="frmorderreceiptform" action="./purchase_form_receipt_update.php" method="post" autocomplete="off">
    <input type="hidden" name="od_id" value="<?php echo $od_id; ?>">
    <input type="hidden" name="sort1" value="<?php echo $sort1; ?>">
    <input type="hidden" name="sort2" value="<?php echo $sort2; ?>">
    <input type="hidden" name="sel_field" value="<?php echo $sel_field; ?>">
    <input type="hidden" name="search" value="<?php echo $search; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <input type="hidden" name="od_name" value="<?php echo $od['od_name']; ?>">
    <input type="hidden" name="od_hp" value="<?php echo $od['od_hp']; ?>">
    <input type="hidden" name="od_tno" value="<?php echo $od['od_tno']; ?>">
    <input type="hidden" name="od_escrow" value="<?php echo $od['od_escrow']; ?>">

    <div class="compare_wrap">

        <section id="anc_sodr_chk" class="compare_left">
            <h3>결제상세정보 확인</h3>

            <div class="tbl_frm01">
                <table>
                <caption>결제상세정보</caption>
                <colgroup>
                    <col class="grid_3">
                    <col>
                </colgroup>
                <tbody>
           
                <tr>
                    <th scope="row"><label for="od_cart_price">입금요청액</label></th>
                    <td>
                        <input type="text" name="od_cart_price" value="<?php echo $od['od_cart_price']; ?>" id="od_cart_price" class="frm_input" size="10"> 원
                    </td>
                </tr>
              
                <tr>
                    <th scope="row"><label for="od_send_cost2">추가비용</label></th>
                    <td>
                        <input type="text" name="od_send_cost2" value="<?php echo $od['od_send_cost2']; ?>" id="od_send_cost2" class="frm_input" size="10"> 원
                    </td>
                </tr>

				
                <tr>
                    <th scope="row"><label for="od_refund_price">결제취소/환불 금액</label></th>
                    <td>
                        <input type="text" name="od_refund_price" value="<?php echo $od['od_refund_price']; ?>" class="frm_input" size="10"> 원
                    </td>
                </tr>


				<tr>
                    <th scope="row"><label for="od_receipt_price"><?php echo $od['od_settle_case']; ?> 입금액</label></th>
                    <td>
                        <?php echo $html_receipt_chk; ?>
                        <input type="text" name="od_receipt_price" value="<?php echo $od['od_receipt_price']; ?>" id="od_receipt_price" class="frm_input"> 원
                    </td>
                </tr>

				<tr>
                    <th scope="row"><label for="od_receipt_time">입금 확인일시</label></th>
                    <td>
                        <input type="checkbox" name="od_bank_chk" id="od_bank_chk" value="<?php echo date("Y-m-d H:i:s", G5_SERVER_TIME); ?>" onclick="if (this.checked == true) this.form.od_receipt_time.value=this.form.od_bank_chk.value; else this.form.od_receipt_time.value = this.form.od_receipt_time.defaultValue;">
                        <label for="od_bank_chk">현재 시간으로 설정</label><br>
                        <input type="text" name="od_receipt_time" value="<?php echo is_null_time($od['od_receipt_time']) ? "" : $od['od_receipt_time']; ?>" id="od_receipt_time" class="frm_input" maxlength="19">
                    </td>
                </tr>
                </tbody>
                </table>
            </div>
        </section>

        <section id="anc_sodr_paymo" class="compare_right">
            <h3>배송상세정보 수정</h3>

            <div class="tbl_frm01">
                <table>
                <caption>배송상세정보 수정</caption>
                <colgroup>
                    <col class="grid_3">
                    <col>
                </colgroup>
                <tbody>

                <tr>
                    <th scope="row"><label for="od_invoice">운송장번호</label></th>
                    <td>
                        <input type="text" name="od_invoice" value="<?php echo $od['od_invoice']; ?>" id="od_invoice" class="frm_input">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="od_delivery_company">배송회사</label></th>
                    <td>
                        <input type="checkbox" id="od_delivery_chk" value="<?php echo $default['de_delivery_company']; ?>" onclick="chk_delivery_company()">
                        <label for="od_delivery_chk">기본 배송회사로 설정</label><br>
                        <input type="text" name="od_delivery_company" id="od_delivery_company" value="<?php echo $od['od_delivery_company']; ?>" class="frm_input">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="od_invoice_time">배송일시</label></th>
                    <td>
                        <input type="checkbox" id="od_invoice_chk" value="<?php echo date("Y-m-d H:i:s", G5_SERVER_TIME); ?>" onclick="chk_invoice_time()">
                        <label for="od_invoice_chk">현재 시간으로 설정</label><br>
                        <input type="text" name="od_invoice_time" id="od_invoice_time" value="<?php echo is_null_time($od['od_invoice_time']) ? "" : $od['od_invoice_time']; ?>" class="frm_input" maxlength="19">
                    </td>
                </tr>

                </tbody>
                </table>
            </div>
        </section>

    </div>

    <div class="btn_confirm01 btn_confirm">
        <input type="submit" value="결제/배송내역 수정" class="btn_submit">
        <a href="./purchase_list.php?<?php echo $qstr; ?>">목록</a>
    </div>
    </form>
</section>

<section id="anc_sodr_memo">
    <h2 class="h2_frm">상점메모</h2>
    <?php echo $pg_anchor; ?>
    <div class="local_desc02 local_desc">
        <p>
            현재 열람 중인 주문에 대한 내용을 메모하는곳입니다.
        </p>
    </div>

    <form name="frmorderform2" action="./purchase_form_update.php" method="post">
    <input type="hidden" name="od_id" value="<?php echo $od_id; ?>">
    <input type="hidden" name="sort1" value="<?php echo $sort1; ?>">
    <input type="hidden" name="sort2" value="<?php echo $sort2; ?>">
    <input type="hidden" name="sel_field" value="<?php echo $sel_field; ?>">
    <input type="hidden" name="search" value="<?php echo $search; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <input type="hidden" name="mod_type" value="memo">

    <div class="tbl_wrap">
        <label for="od_shop_memo" class="sound_only">상점메모</label>
        <textarea name="od_shop_memo" id="od_shop_memo" rows="8"><?php echo stripslashes($od['od_shop_memo']); ?></textarea>
    </div>

    <div class="btn_confirm01 btn_confirm">
        <input type="submit" value="메모 수정" class="btn_submit">
    </div>

    </form>
</section>

<section>
    <h2 class="h2_frm">주문자/배송지 정보</h2>
    <?php echo $pg_anchor; ?>

    <form name="frmorderform3" action="./purchase_form_update.php" method="post">
    <input type="hidden" name="od_id" value="<?php echo $od_id; ?>">
    <input type="hidden" name="sort1" value="<?php echo $sort1; ?>">
    <input type="hidden" name="sort2" value="<?php echo $sort2; ?>">
    <input type="hidden" name="sel_field" value="<?php echo $sel_field; ?>">
    <input type="hidden" name="search" value="<?php echo $search; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <input type="hidden" name="mod_type" value="info">

    <div class="compare_wrap">

        <section id="anc_sodr_orderer" class="compare_left">
            <h3>주문하신 분</h3>

            <div class="tbl_frm01">
                <table>
                <caption>주문자/배송지 정보</caption>
                <colgroup>
                    <col class="grid_4">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th scope="row"><label for="od_name"><span class="sound_only">주문하신 분 </span>이름</label></th>
                    <td><input type="text" name="od_name" value="<?php echo $od['od_name']; ?>" id="od_name" required class="frm_input required"></td>
                </tr>
          
                <tr>
                    <th scope="row"><label for="od_hp"><span class="sound_only">주문하신 분 </span>핸드폰</label></th>
                    <td><input type="text" name="od_hp" value="<?php echo $od['od_hp']; ?>" id="od_hp" class="frm_input"></td>
                </tr>
                <tr>
                    <th scope="row"><span class="sound_only">주문하시는 분 </span>주소</th>
                    <td>
                        <label for="od_zip1" class="sound_only">우편번호 앞자리</label>
                        <input type="text" name="od_zip1" value="<?php echo $od['od_zip1']; ?>" id="od_zip1" required class="frm_input required" size="4">
                        -
                        <label for="od_zip2" class="sound_only">우편번호 뒷자리</label>
                        <input type="text" name="od_zip2" value="<?php echo $od['od_zip2']; ?>" id="od_zip2" required class="frm_input required" size="4">
                        <a href="<?php echo G5_BBS_URL; ?>/zip.php?frm_name=frmorderform3&amp;frm_zip1=od_zip1&amp;frm_zip2=od_zip2&amp;frm_addr1=od_addr1&amp;frm_addr2=od_addr2&amp;frm_addr3=od_addr3&amp;frm_jibeon=od_addr_jibeon" id="od_zip_find" class="btn_frmline win_zip_find" target="_blank">주소 검색</a><br>
                        <span id="od_win_zip" style="display:block"></span>
                        <input type="text" name="od_addr1" value="<?php echo $od['od_addr1']; ?>" id="od_addr1" required class="frm_input required" size="35">
                        <label for="od_addr1">기본주소</label><br>
                        <input type="text" name="od_addr2" value="<?php echo $od['od_addr2']; ?>" id="od_addr2" class="frm_input" size="35">
                        <label for="od_addr2">상세주소</label><br>
                        <input type="text" name="od_addr3" value="<?php echo $od['od_addr3']; ?>" id="od_addr3" class="frm_input" size="35">
                        <label for="od_addr3">참고항목</label>
                        <input type="hidden" name="od_addr_jibeon" value="<?php echo $od['od_addr_jibeon']; ?>"><br>
                        <span id="od_addr_jibeon">지번주소 : <?php echo $od['od_addr_jibeon']; ?></span>
                </tr>
                <tr>
                    <th scope="row"><label for="od_email"><span class="sound_only">주문하신 분 </span>E-mail</label></th>
                    <td><input type="text" name="od_email" value="<?php echo $od['od_email']; ?>" id="od_email" required class="frm_input email required" size="30"></td>
                </tr>
                <tr>
                    <th scope="row"><span class="sound_only">주문하신 분 </span>IP Address</th>
                    <td><?php echo $od['od_ip']; ?></td>
                </tr>
                </tbody>
                </table>
            </div>
        </section>

        <section id="anc_sodr_taker" class="compare_right">
            <h3>받으시는 분</h3>

            <div class="tbl_frm01">
                <table>
                <caption>받으시는 분 정보</caption>
                <colgroup>
                    <col class="grid_4">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th scope="row"><label for="od_b_name"><span class="sound_only">받으시는 분 </span>이름</label></th>
                    <td><input type="text" name="od_b_name" value="<?php echo $od['od_b_name']; ?>" id="od_b_name" required class="frm_input required"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="od_b_hp"><span class="sound_only">받으시는 분 </span>핸드폰</label></th>
                    <td><input type="text" name="od_b_hp" value="<?php echo $od['od_b_hp']; ?>" id="od_b_hp" class="frm_input required"></td>
                </tr>
                <tr>
                    <th scope="row"><span class="sound_only">받으시는 분 </span>주소</th>
                    <td>
                        <label for="od_b_zip1" class="sound_only">우편번호 앞자리</label>
                        <input type="text" name="od_b_zip1" value="<?php echo $od['od_b_zip1']; ?>" id="od_b_zip1" required class="frm_input required" size="4">
                        -
                        <label for="od_b_zip2" class="sound_only">우편번호 뒷자리</label>
                        <input type="text" name="od_b_zip2" value="<?php echo $od['od_b_zip2']; ?>" id="od_b_zip2" required class="frm_input required" size="4">
                        <a href="<?php echo G5_BBS_URL; ?>/zip.php?frm_name=frmorderform3&amp;frm_zip1=od_b_zip1&amp;frm_zip2=od_b_zip2&amp;frm_addr1=od_b_addr1&amp;frm_addr2=od_b_addr2&amp;frm_addr3=od_b_addr3&amp;frm_jibeon=od_b_addr_jibeon" id="od_zip_findb" class="btn_frmline win_zip_find" target="_blank">주소 검색</a><br>
                        <input type="text" name="od_b_addr1" value="<?php echo $od['od_b_addr1']; ?>" id="od_b_addr1" required class="frm_input required" size="35">
                        <label for="od_b_addr1">기본주소</label>
                        <input type="text" name="od_b_addr2" value="<?php echo $od['od_b_addr2']; ?>" id="od_b_addr2" class="frm_input" size="35">
                        <label for="od_b_addr2">상세주소</label>
                        <input type="text" name="od_b_addr3" value="<?php echo $od['od_b_addr3']; ?>" id="od_b_addr3" class="frm_input" size="35">
                        <label for="od_b_addr3">참고항목</label>
                        <input type="hidden" name="od_b_addr_jibeon" value="<?php echo $od['od_b_addr_jibeon']; ?>"><br>
                        <span id="od_b_addr_jibeon">지번주소 : <?php echo $od['od_b_addr_jibeon']; ?></span>
                    </td>
                </tr>

                <?php if ($default['de_hope_date_use']) { ?>
                <tr>
                    <th scope="row"><label for="od_hope_date">희망배송일</label></th>
                    <td>
                        <input type="text" name="od_hope_date" value="<?php echo $od['od_hope_date']; ?>" id="od_hopedate" required class="frm_input required" maxlength="10" minlength="10"> (<?php echo get_yoil($od['od_hope_date']); ?>)
                    </td>
                </tr>
                <?php } ?>

                <tr>
                    <th scope="row">전달 메세지</th>
                    <td><?php if ($od['od_memo']) echo nl2br($od['od_memo']);else echo "없음";?></td>
                </tr>
                </tbody>
                </table>
            </div>
        </section>

    </div>

    <div class="btn_confirm01 btn_confirm">
        <input type="submit" value="주문자/배송지 정보 수정" class="btn_submit">
        <a href="./purchase_list.php?<?php echo $qstr; ?>">목록</a>
    </div>

    </form>
</section>

<script>
$(function() {
    // 전체 옵션선택
    $("#sit_select_all").click(function() {
        if($(this).is(":checked")) {
            $("input[name='it_sel[]']").attr("checked", true);
            $("input[name^=ct_chk]").attr("checked", true);
        } else {
            $("input[name='it_sel[]']").attr("checked", false);
            $("input[name^=ct_chk]").attr("checked", false);
        }
    });

    // 상품의 옵션선택
    $("input[name='it_sel[]']").click(function() {
        var cls = $(this).attr("id").replace("sit_", "sct_");
        var $chk = $("input[name^=ct_chk]."+cls);
        if($(this).is(":checked"))
            $chk.attr("checked", true);
        else
            $chk.attr("checked", false);
    });

    // 개인결제추가
    $("#personalpay_add").on("click", function() {
        var href = this.href;
        window.open(href, "personalpaywin", "left=100, top=100, width=700, height=560, scrollbars=yes");
        return false;
    });

    // 부분취소창
    $("#orderpartcancel").on("click", function() {
        var href = this.href;
        window.open(href, "partcancelwin", "left=100, top=100, width=600, height=350, scrollbars=yes");
        return false;
    });
});

function form_submit(f)
{
    var check = false;
    var status = document.pressed;

    for (i=0; i<f.chk_cnt.value; i++) {
        if (document.getElementById('ct_chk_'+i).checked == true)
            check = true;
    }

    if (check == false) {
        alert("처리할 자료를 하나 이상 선택해 주십시오.");
        return false;
    }

    if (confirm("\'" + status + "\' 상태를 선택하셨습니다.\n\n처리 하시겠습니까?")) {
        return true;
    } else {
        return false;
    }
}

function del_confirm()
{
    if(confirm("주문서를 삭제하시겠습니까?")) {
        return true;
    } else {
        return false;
    }
}

// 기본 배송회사로 설정
function chk_delivery_company()
{
    var chk = document.getElementById("od_delivery_chk");
    var company = document.getElementById("od_delivery_company");
    company.value = chk.checked ? chk.value : company.defaultValue;
}

// 현재 시간으로 배송일시 설정
function chk_invoice_time()
{
    var chk = document.getElementById("od_invoice_chk");
    var time = document.getElementById("od_invoice_time");
    time.value = chk.checked ? chk.value : time.defaultValue;
}

// 결제금액 수동 설정
function chk_receipt_price()
{
    var chk = document.getElementById("od_receipt_chk");
    var price = document.getElementById("od_receipt_price");
    price.value = chk.checked ? (parseInt(chk.value) + parseInt(price.defaultValue)) : price.defaultValue;
}
</script>

<?php
include_once(G5_ADMIN_PATH.'/admin.tail.php');
?>