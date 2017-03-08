<?php
include_once('./_common.php');

// 불법접속을 할 수 없도록 세션에 아무값이나 저장하여 hidden 으로 넘겨서 다음 페이지에서 비교함
$token = md5(uniqid(rand(), true));
set_session("ss_token", $token);

if (!$is_member) {
    if (get_session('ss_orderview_uid') != $_GET['uid'])
        alert("직접 링크로는 주문서 조회가 불가합니다.\\n\\n주문조회 화면을 통하여 조회하시기 바랍니다.", G5_SHOP_URL);
}

$sql = "select * from {$g5['g5_purchase_order_table']} where od_id = '$od_id' ";
$od = sql_fetch($sql);
if (!$od['od_id'] || (!$is_member && md5($od['od_id'].$od['od_time'].$od['od_ip']) != get_session('ss_orderview_uid'))) {
    alert("조회하실 주문서가 없습니다.", G5_SHOP_URL);
}

// 결제방법
$settle_case = $od['od_settle_case'];

$g5['title'] = '주문상세내역';
include_once(G5_PATH.'/head.php');
?>


<div id="sod_fin">

    <p>주문번호 <strong><?php echo $od_id; ?></strong></p>

    <section id="sod_fin_list">
        <h2>주문하신 상품</h2>

        <?php
        $st_count1 = $st_count2 = 0;
        $custom_cancel = false;

        $sql = " select pc_num, pc_item, pc_item_url, pc_item_option, pc_type, pc_price, pc_qty, pc_status  
                    from {$g5['g5_purchase_cart_table']}
                    where od_id = '$od_id'
                    order by pc_num ";
        $result = sql_query($sql);
        ?>
        <div class="tbl_head02 tbl_wrap">
            <table>
            <thead>
            <tr>
                <th scope="col" id="th_itopt">구분</th>
                <th scope="col" id="th_item">상품명</th>
                <th scope="col" id="th_itoption">옵션</th>
                <th scope="col" id="th_itprice">판매가</th>
                <th scope="col" id="th_itqty">수량</th>
                <th scope="col" id="th_itsum">소계</th>
                <th scope="col" id="th_itst">상태</th>
            </tr>
            </thead>
            <tbody>
            <?php
            for($i=0; $row=sql_fetch_array($result); $i++) {

            ?>
            <tr>
				<td headers="th_ittype"><?php echo $purchaseShoppingType[$row[pc_type]];?></td>
                <td headers="th_itname"><a href="<?php echo $row['pc_item_url']; ?>" target="_blank"><?php echo $row['pc_item']; ?></a></td>
                <td headers="th_itopt"><?php echo $opt['pc_item_option']; ?></td>
				<?php if($row[pc_type]=="N"){?>
                <td headers="th_itprice" class="td_numbig">USD <?php echo number_format($row[pc_price],2); ?></td>
                <td headers="th_itqty" class="td_mngsmall"><?php echo number_format($row['pc_qty']); ?></td>
                <td headers="th_itsum" class="td_numbig">USD <?php echo number_format($row[pc_price]*$row['pc_qty'],2); ?></td>
				<?php }else{?>
				 <td headers="th_itprice" class="td_numbig" colspan="3">USD <?php echo number_format($row[pc_price],2); ?> 내에 입찰요청</td>
				<?php }?>
                <td headers="th_itst" class="td_mngsmall"><?php echo $row['pc_status']; ?></td>
            </tr>
            <?php
             
            }

            ?>
            </tbody>
            </table>
        </div>


        <?php
        // 총계 = 주문상품금액합계 + 배송비 - 상품할인 - 결제할인 - 배송비할인
        $tot_price = $od['od_cart_price'] + $od['od_send_cost'] + $od['od_send_cost2']
                        - $od['od_cart_coupon'] - $od['od_coupon'] - $od['od_send_coupon']
                        - $od['od_cancel_price'];
        ?>

        <dl id="sod_bsk_tot">
            <dt class="sod_bsk_dvr">주문총액</dt>
            <dd class="sod_bsk_dvr"><strong>USD <?php echo number_format($od['od_cart_item_price'],2); ?></strong></dd>

            <?php if ($od['od_send_cost2'] > 0) { ?>
            <dt class="sod_bsk_dvr">추가금액</dt>
            <dd class="sod_bsk_dvr"><strong><?php echo number_format($od['od_send_cost2']); ?> 원</strong></dd>
            <?php } ?>

            <dt class="sod_bsk_cnt">입금하실 금액</dt>
            <dd class="sod_bsk_cnt"><strong><?php echo number_format($tot_price); ?> 원</strong></dd>

        </dl>
    </section>

    <div id="sod_fin_view">
        <h2>결제/배송 정보</h2>
        <?php
        $receipt_price  = $od['od_receipt_price']
                        + $od['od_receipt_point'];
        $cancel_price   = $od['od_cancel_price'];

        $misu = true;
        $misu_price = $tot_price - $receipt_price - $cancel_price;

        if ($misu_price == 0 && ($od['od_cart_price'] > $od['od_cancel_price'])) {
            $wanbul = " (완불)";
            $misu = false; // 미수금 없음
        }
        else
        {
            $wanbul = display_price($receipt_price);
        }

        // 결제정보처리
        if($od['od_receipt_price'] > 0)
            $od_receipt_price = display_price($od['od_receipt_price']);
        else
            $od_receipt_price = '아직 입금되지 않았거나 입금정보를 입력하지 못하였습니다.';

        ?>

        <section id="sod_fin_pay">
            <h3>결제정보</h3>

            <div class="tbl_head01 tbl_wrap">
                <table>
                <colgroup>
                    <col class="grid_3">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th scope="row">주문번호</th>
                    <td><?php echo $od_id; ?></td>
                </tr>
                <tr>
                    <th scope="row">주문일시</th>
                    <td><?php echo $od['od_time']; ?></td>
                </tr>

                <tr>
                    <th scope="row">결제금액</th>
                    <td><?php echo $od_receipt_price; ?></td>
                </tr>
                <?php
                if($od['od_receipt_price'] > 0)
                {
                ?>
                <tr>
                    <th scope="row">결제일시</th>
                    <td><?php echo $od['od_receipt_time']; ?></td>
                </tr>
                <?php
                }

                if ($od['od_refund_price'] > 0)
                {
                ?>
                <tr>
                    <th scope="row">환불 금액</th>
                    <td><?php echo display_price($od['od_refund_price']); ?></td>
                </tr>
                <?php
                }
                ?>
                </tbody>
                </table>
            </div>
        </section>

        <section id="sod_fin_orderer">
            <h3>주문하신 분</h3>

            <div class="tbl_head01 tbl_wrap">
                <table>
                <colgroup>
                    <col class="grid_3">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th scope="row">이 름</th>
                    <td><?php echo $od['od_name']; ?></td>
                </tr>
                <tr>
                    <th scope="row">핸드폰</th>
                    <td><?php echo $od['od_hp']; ?></td>
                </tr>
                <tr>
                    <th scope="row">주 소</th>
                    <td><?php echo sprintf("(%s-%s)", $od['od_zip1'], $od['od_zip2']).' '.print_address($od['od_addr1'], $od['od_addr2'], $od['od_addr3']); ?></td>
                </tr>
                <tr>
                    <th scope="row">E-mail</th>
                    <td><?php echo $od['od_email']; ?></td>
                </tr>
                </tbody>
                </table>
            </div>
        </section>

        <section id="sod_fin_receiver">
            <h3>받으시는 분</h3>

            <div class="tbl_head01 tbl_wrap">
                <table>
                <colgroup>
                    <col class="grid_3">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th scope="row">이 름</th>
                    <td><?php echo $od['od_b_name']; ?></td>
                </tr>
                <tr>
                    <th scope="row">핸드폰</th>
                    <td><?php echo $od['od_b_hp']; ?></td>
                </tr>
                <tr>
                    <th scope="row">주 소</th>
                    <td><?php echo sprintf("(%s-%s)", $od['od_b_zip1'], $od['od_b_zip2']).' '.print_address($od['od_b_addr1'], $od['od_b_addr2'], $od['od_b_addr3']); ?></td>
                </tr>
                <?php
                if ($od['od_memo'])
                {
                ?>
                <tr>
                    <th scope="row">전하실 말씀</td>
                    <td><?php echo conv_content($od['od_memo'], 0); ?></td>
                </tr>
                <?php } ?>
                </tbody>
                </table>
            </div>
        </section>

        <section id="sod_fin_dvr">
            <h3>배송정보</h3>

            <div class="tbl_head01 tbl_wrap">
                <table>
                <colgroup>
                    <col class="grid_3">
                    <col>
                </colgroup>
                <tbody>
                <?php
                if ($od['od_invoice'] && $od['od_delivery_company'])
                {
                ?>
                <tr>
                    <th scope="row">배송회사</th>
                    <td><?php echo $od['od_delivery_company']; ?> <?php echo get_delivery_inquiry($od['od_delivery_company'], $od['od_invoice'], 'dvr_link'); ?></td>
                </tr>
                <tr>
                    <th scope="row">운송장번호</th>
                    <td><?php echo $od['od_invoice']; ?></td>
                </tr>
                <tr>
                    <th scope="row">배송일시</th>
                    <td><?php echo $od['od_invoice_time']; ?></td>
                </tr>
                <?php
                }
                else
                {
                ?>
                <tr>
                    <td class="empty_table">아직 배송하지 않았거나 배송정보를 입력하지 못하였습니다.</td>
                </tr>
                <?php
                }
                ?>
                </tbody>
                </table>
            </div>
        </section>
    </div>

    <section id="sod_fin_tot">
        <h2>결제합계</h2>

        <ul>
            <li>
                총 구매액
                <strong><?php echo display_price($tot_price); ?></strong>
            </li>
            <?php
            if ($misu_price > 0) {
            echo '<li>';
            echo '미결제액'.PHP_EOL;
            echo '<strong>'.display_price($misu_price).'</strong>';
            echo '</li>';
            }
            ?>
            <li id="alrdy">
                결제액
                <strong><?php echo $wanbul; ?></strong>
            </li>
        </ul>
    </section>

	<div class="agency_confirm1 agency_confirm">
        <a href="./purchase_request_list.php?<?php echo $qstr; ?>">목록</a>
    </div>
</div>
<!-- } 주문상세내역 끝 -->


<?php
include_once(G5_PATH.'/tail.php');
?>