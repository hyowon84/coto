<?php
include_once('./_common.php');

if (G5_IS_MOBILE) {
    include_once(G5_MSHOP_PATH.'/orderinquiryview.php');
    return;
}

if(get_session("mem_order_se")){
	$member[mb_id] = get_session("mem_order_se");
	$member = sql_fetch("select * from {$g5['member_table']} where mb_id='".$member[mb_id]."'");
}


// 불법접속을 할 수 없도록 세션에 아무값이나 저장하여 hidden 으로 넘겨서 다음 페이지에서 비교함
$token = md5(uniqid(rand(), true));
set_session("ss_token", $token);

if (!$is_member) {
    if (get_session('ss_orderview_uid') != $_GET['uid'])
        alert("직접 링크로는 주문서 조회가 불가합니다.\\n\\n주문조회 화면을 통하여 조회하시기 바랍니다.", G5_SHOP_URL);
}

$sql = "select * from {$g5['g5_shop_order_table']} where od_id = '$od_id' ";
$od = sql_fetch($sql);


if(strtotime("now") > strtotime($od[od_last_date]) || !$od[od_last_date]){
	alert("결제 가능한 기간이 지났습니다.");
}

if (!$od['od_id'] || (!$is_member && md5($od['od_id'].$od['od_time'].$od['od_ip']) != get_session('ss_orderview_uid'))) {
    alert("조회하실 주문서가 없습니다.", G5_SHOP_URL);
}

$od_bank_account = explode(" ", $od['od_bank_account']);
$od_bank = explode("|", $od['od_bank']);

// 총계 = 주문상품금액합계 + 배송비 - 상품할인 - 결제할인 - 배송비할인
$tot_price = $od['od_cart_price'] + $od['od_send_cost'] + $od['od_send_cost2']
			- $od['od_cart_coupon'] - $od['od_coupon'] - $od['od_send_coupon']
			- $od['od_cancel_price'];

// 결제방법
$settle_case = $od['od_settle_case'];

$g5['title'] = '주문상세내역';
include_once('./_head.php');
?>

<!-- 주문상세내역 시작 { -->
<script>
var openwin = window.open( './kcp/proc_win.html', 'proc_win', '' );
if(openwin != null) {
    openwin.close();
}
</script>

<div id="aside2"></div>

<!-- navi -->
<div class="cart_nav">
	<ul>
		<li><img src="img/cart_nav1.gif" border="0" align="absmiddle"></li>
		<li><img src="img/cart_nav2.gif" border="0" align="absmiddle"></li>
		<li><img src="img/cart_nav3_on.gif" border="0" align="absmiddle"></li>
	</ul>
</div>

<div class="list_box">

	<div id="sod_fin">

		<!--<div id="sod_fin_no">주문번호 <strong><?php// echo $od_id; ?></strong></div>-->
		<div id="comp_guide">
			<?php echo $member['mb_nick']?>님의 <span style="color:#ff4e00;">주문이 확인</span>되었습니다.
		</div>

		<div id="comp_buy_info">
			<table border="0" cellspacing="0" cellpadding="0" style="width:100%;">
				<tr>
					<td align="center">
						
						<table border="0" cellspacing="0" cellpadding="0" class="comp_buy_info_tb">
							<tr height="1px"><td colspan="2" style="background:#d9d9d9;"></td></tr>
							<tr height="40px">
								<td style="width:110px;">입금하실 금액</td>
								<td>
									<span style="color:#ff4e00;font-size:17px;"><?=number_format($tot_price)?> 원</span>
									(<?=date("Y년 m월 d일 H시 i분", strtotime($od[od_last_date]))?>까지)
								</td>
							</tr>
							<tr height="1px"><td colspan="2" style="background:#d9d9d9;"></td></tr>
							<tr height="40px">
								<td>입금은행</td>
								<td><?php echo $od_bank_account[0]; ?></td>
							</tr>
							<tr height="1px"><td colspan="2" style="background:#d9d9d9;"></td></tr>
							<tr height="40px">
								<td>입금계좌</td>
								<td><?php echo $od_bank_account[1]; ?> <?php echo $od_bank_account[2]; ?></td>
							</tr>
							<tr height="1px"><td colspan="2" style="background:#d9d9d9;"></td></tr>
							<tr height="25px"><td colspan="2"></td></tr>
							<tr>
								<td colspan="2" style="text-align:center;font-size:11px;">
									입금확인이 되어야 주문이 완료되어 배송을 시작합니다. 입금기한 내에 해당 은행의 계좌번호로 입금해 주세요.
								</td>
							</tr>
							<tr height="25px"><td colspan="2"></td></tr>
						</table>

					</td>
				</tr>
			</table>
		</div>

		<div class="cl" style="border-top:1px #d9d9d9 solid;"></div>

		<section id="sod_fin_list" class="cl">
			<h2>주문번호 <span><?php echo $od_id; ?></span></h2>

			<?php
			$st_count1 = $st_count2 = 0;
			$custom_cancel = false;

			$chk = sql_fetch("select count(*) as cnt from {$g5['g5_shop_cart_table']} where od_id='$od_id' and ct_type != '' and mb_id ='".$member['mb_id']."' ");
			$chk = $chk[cnt];

			if($chk){
				$gourpby = " group by ct_type ";
			}else{
				$gourpby = " group by it_id ";
			}

			$sql = " select ct_id, it_id, ct_payment, ct_gubun, it_name, ct_send_cost, ct_type, ct_op_option
						from {$g5['g5_shop_cart_table']}
						where od_id = '$od_id' and mb_id ='".$member['mb_id']."' 
						$gourpby
						order by ct_id ";

			$result = sql_query($sql);
			?>
			<div class="tbl_head01 tbl_wrap">
				<table>
				<thead>
				<tr>
					<?if($chk){?>
					<th scope="col" class="right">브랜드</th>
					<?}?>
					<th scope="col" class="right" colspan="2">상품정보</th>
					<th scope="col" class="right">수량</th>
					<th scope="col" class="right">상품금액</th>
					<th scope="col" class="right">옵션</th>
					<th scope="col" class="right">할인</th>
					<th scope="col" class="right">배송비</th>
				</tr>
				</thead>
				<tbody>
				<?php
				for($i=0; $row=sql_fetch_array($result); $i++) {
					if($row[ct_gubun]=="P")$image = get_gp_image($row['it_id'], 90, 90);
					else $image = get_it_image($row['it_id'], 90, 90);


					$sql = " select ct_id, it_name, ct_option, ct_qty, ct_price, ct_point, ct_status, io_type, io_price, ct_send_cost
								from {$g5['g5_shop_cart_table']}
								where od_id = '$od_id' and ct_type = '".$row[ct_type]."' and mb_id ='".$member['mb_id']."' 
								order by io_type asc, ct_id asc ";
					$res = sql_query($sql);
					$rowspan = mysql_num_rows($res) + 1;

					// 배송비
					switch($row['ct_send_cost'])
					{
						case 1:
							$ct_send_cost = '착불';
							break;
						case 2:
							$ct_send_cost = '무료';
							break;
						default:
							$ct_send_cost = '선불';
							break;
					}

					//옵션 상품
					$op_arr = explode("|", $row[ct_op_option]);
					$op_price = 0;
					$op_name = "";
					for($b = 0; $b < count($op_arr); $b++){
						if($op_arr[$b]){
							$op_row = sql_fetch("select * from {$g5['g5_shop_option2_table']} where it_id='".$row[it_id]."' and con='".$op_arr[$b]."' ");
							$op_price = $op_price + $op_row[price];
							$op_name .= $op_row[con].",";
						}
					}
					$op_name = substr($op_name, 0, strlen($op_name)-1);

					$gp_row = sql_fetch("select * from {$g5['g5_shop_group_purchase_table']} where gp_id='".$row['it_id']."' ");

					$a1 = '<a href="'.G5_URL.'/shop/grouppurchase.php?gp_id='.$gp_row[gp_id].'">';
					$a2 = '</a>';

					/*
					if($row[ct_type]){
						$rem_row = sql_fetch("select count(*) as cnt from {$g5['g5_shop_cart_table']} where od_id='".$od_id."' and ct_type='".$row[ct_type]."' and mb_id ='".$member['mb_id']."' ");
						if($rem_row[cnt] > 1){
							$rem = $rem_row[cnt] - 1;
							$rem = " 외 ".$rem."개";
						}else{
							$rem = "";
						}
					}
					*/

					for($k=0; $opt=sql_fetch_array($res); $k++) {
						if($opt['io_type'])
							$opt_price = $opt['io_price'];
						else
							$opt_price = $opt['ct_price'] + $opt['io_price'];

						$sell_price = $opt_price * $opt['ct_qty'] + $op_price;
						$point = $opt['ct_point'] * $opt['ct_qty'];

						if($k == 0) {
				?>
				<?php } ?>
				<tr>
					<?if($chk){?>
					<td class="td_num right"><?=$a1.category_name($row[ct_type]).$a2?></td>
					<?}?>
					<td class="td_imgsmall"><?php echo $image; ?></td>
					<td headers="th_itname" class="right" style="font-size:12px;font-weight:bold;">
						<p><a href="./item.php?it_id=<?php echo $row['it_id']; ?>"><?php echo $row['it_name'].$rem; ?></a></p>
						<p style="margin:10px 0 0 0;padding:0 0 0 100px;"><span style="width:75px;float:left;">판매가</span><span style="float:left;width:75px;"><?php echo number_format($opt_price); ?></span></p>
					</td>
					<td headers="th_itqty" class="td_mngsmall right"><?php echo number_format($opt['ct_qty']); ?></td>
					<!--<td headers="th_itprice" class="td_numbig right"><?php echo number_format($opt_price); ?></td>-->
					<td headers="th_itsum" class="td_numbig right"><?php echo number_format($sell_price); ?></td>
					<td headers="th_itsum" class="td_numbig right"><?php echo $op_name; ?></td>
					<td headers="th_itpt" class="td_num right"><?php echo number_format($point); ?></td>
					<td headers="th_itpt" class="td_num right"><?php echo $ct_send_cost; ?></td>
					<!--<td headers="th_itst" class="td_mngsmall"><?php echo $opt['ct_status']; ?></td>-->
				</tr>
				<?php
						$tot_point       += $point;

						$st_count1++;
						if($opt['ct_status'] == '입금대기')
							$st_count2++;
					}
				}

				if($chk){
					$cols = "5";
					$cols2 = "8";
				}else{
					$cols = "4";
					$cols2 = "7";
				}

				// 주문 상품의 상태가 모두 주문이면 고객 취소 가능
				if($st_count1 > 0 && $st_count1 == $st_count2)
					$custom_cancel = true;
				?>

				<tr class="buy_success_tr">
					<td colspan="<?=$cols?>" class="line_del">
						
					</td>
					<td colspan="3" class="line_del buy_success">
						<div class="title">총상품금액</div>
						<div class="con"><?php echo number_format($od['od_cart_price']); ?>원</div>
						<div class="cl title">배송비</div>
						<div class="con"><?php echo number_format($od['od_send_cost']); ?>원</div>
						<div class="cl title">총할인금액</div>
						<div class="con">(-)0원</div>
						<div class="cl title" style="margin:20px 0 20px 0;">결제금액</div>
						<div class="con" style="margin:20px 0 20px 0;font-size:15px;"><?php echo number_format($tot_price); ?><span style="font-size:11px;">원</span></div>
					</td>
				</tr>
				
				<tr style="height:2px;"><td colspan="<?=$cols2?>" style="height:2px;border:0px;padding:0px;background:#545454;"></td></tr>

				<tr class="buy_success_tr">
					<td colspan="<?=$cols?>" class="line_del buy_success_f">
						<div class="title_f">배송지정보</div>
						<div class="con_f"><?php echo $od['od_b_name']; ?> | <?php echo $od['od_b_tel']; ?></div>
						<div class="cl title_f"></div>
						<div class="con_f"><?php echo sprintf("(%s-%s)", $od['od_b_zip1'], $od['od_b_zip2']).' '.print_address($od['od_b_addr1'], $od['od_b_addr2'], $od['od_b_addr3']); ?></div>
						<div class="cl title_f">&nbsp;</div>
						<div class="cl title_f">배송지메모</div>
						<div class="con_f"><?php echo $od['od_memo']; ?></div>
						<div class="cl title_f">&nbsp;</div>
						<div class="cl title_f">&nbsp;</div>
					</td>
					<td colspan="3" class="line_del buy_success">
						<div class="title" style="color:#000;">결제정보</div>
						<div class="cl title">결제방식</div>
						<div class="con"><?php echo $od['od_settle_case']; ?></div>
						<div class="cl title" style="color:#000;">주문취소/반품시 환급 받을 본인계좌</div>
						<div class="cl title">환불은행</div>
						<div class="con"><?=$od_bank[0]?></div>
						<div class="cl title">환불계좌</div>
						<div class="con" style="width:140px;word-break:word-all;"><?=$od_bank[1]?></div>
						<div class="cl title">예금주명</div>
						<div class="con"><?=$od['od_remit']?></div>
					</td>
				</tr>

				<tr>
					<td colspan="<?=$cols2?>" style="text-align:center;">
						<img src="<?=G5_URL?>/img/buy_history_bn.gif" border="0" align="absmiddle" style="cursor:pointer;" onclick="goto_url('<?=G5_SHOP_URL?>/orderinquiry.php');">
						<img src="<?=G5_SHOP_URL?>/img/cart_shop_bn.gif" border="0" align="absmiddle" style="cursor:pointer;" onclick="goto_url('<?=G5_URL?>');">
					</td>
				</tr>
				</tbody>
				</table>
			</div>

			<!--
			<div id="sod_sts_wrap">
				<span class="sound_only">상품 상태 설명</span>
				<button type="button" id="sod_sts_explan_open" class="btn_frmline">상태설명보기</button>
				<div id="sod_sts_explan">
					<dl id="sod_fin_legend">
						<dt>주문</dt>
						<dd>주문이 접수되었습니다.</dd>
						<dt>입금</dt>
						<dd>입금(결제)이 완료 되었습니다.</dd>
						<dt>준비</dt>
						<dd>상품 준비 중입니다.</dd>
						<dt>배송</dt>
						<dd>상품 배송 중입니다.</dd>
						<dt>완료</dt>
						<dd>상품 배송이 완료 되었습니다.</dd>
					</dl>
					<button type="button" id="sod_sts_explan_close" class="btn_frmline">상태설명닫기</button>
				</div>
			</div>
			-->

			

			<!--
			<dl id="sod_bsk_tot">
				<dt class="sod_bsk_dvr">주문총액</dt>
				<dd class="sod_bsk_dvr"><strong><?php echo number_format($od['od_cart_price']); ?> 원</strong></dd>

				<?php if($od['od_cart_coupon'] > 0) { ?>
				<dt class="sod_bsk_dvr">개별상품 쿠폰할인</dt>
				<dd class="sod_bsk_dvr"><strong><?php echo number_format($od['od_cart_coupon']); ?> 원</strong></dd>
				<?php } ?>

				<?php if($od['od_coupon'] > 0) { ?>
				<dt class="sod_bsk_dvr">주문금액 쿠폰할인</dt>
				<dd class="sod_bsk_dvr"><strong><?php echo number_format($od['od_coupon']); ?> 원</strong></dd>
				<?php } ?>

				<?php if ($od['od_send_cost'] > 0) { ?>
				<dt class="sod_bsk_dvr">배송비</dt>
				<dd class="sod_bsk_dvr"><strong><?php echo number_format($od['od_send_cost']); ?> 원</strong></dd>
				<?php } ?>

				<?php if($od['od_send_coupon'] > 0) { ?>
				<dt class="sod_bsk_dvr">배송비 쿠폰할인</dt>
				<dd class="sod_bsk_dvr"><strong><?php echo number_format($od['od_send_coupon']); ?> 원</strong></dd>
				<?php } ?>

				<?php if ($od['od_send_cost2'] > 0) { ?>
				<dt class="sod_bsk_dvr">추가배송비</dt>
				<dd class="sod_bsk_dvr"><strong><?php echo number_format($od['od_send_cost2']); ?> 원</strong></dd>
				<?php } ?>

				<?php if ($od['od_cancel_price'] > 0) { ?>
				<dt class="sod_bsk_dvr">취소금액</dt>
				<dd class="sod_bsk_dvr"><strong><?php echo number_format($od['od_cancel_price']); ?> 원</strong></dd>
				<?php } ?>

				<dt class="sod_bsk_cnt">총계</dt>
				<dd class="sod_bsk_cnt"><strong><?php echo number_format($tot_price); ?> 원</strong></dd>

				<dt class="sod_bsk_point">포인트</dt>
				<dd class="sod_bsk_point"><strong><?php echo number_format($tot_point); ?> 점</strong></dd>
			</dl>
			-->
		</section>

		<!--
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

			$app_no_subj = '';
			$disp_bank = true;
			$disp_receipt = false;
			if($od['od_settle_case'] == '신용카드') {
				$app_no_subj = '승인번호';
				$app_no = $od['od_app_no'];
				$disp_bank = false;
				$disp_receipt = true;
			} else if($od['od_settle_case'] == '휴대폰') {
				$app_no_subj = '휴대폰번호';
				$app_no = $od['od_bank_account'];
				$disp_bank = false;
				$disp_receipt = true;
			} else if($od['od_settle_case'] == '가상계좌' || $od['od_settle_case'] == '계좌이체') {
				$app_no_subj = 'KCP 거래번호';
				$app_no = $od['od_tno'];
			}
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
						<th scope="row">결제방식</th>
						<td><?php echo $od['od_settle_case']; ?></td>
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

					// 승인번호, 휴대폰번호, KCP 거래번호
					if($app_no_subj)
					{
					?>
					<tr>
						<th scope="row"><?php echo $app_no_subj; ?></th>
						<td><?php echo $app_no; ?></td>
					</tr>
					<?php
					}

					// 계좌정보
					if($disp_bank)
					{
					?>
					<tr>
						<th scope="row">입금자명</th>
						<td><?php echo $od['od_deposit_name']; ?></td>
					</tr>
					<tr>
						<th scope="row">입금계좌</th>
						<td><?php echo $od['od_bank_account']; ?></td>
					</tr>
					<?php
					}

					if($disp_receipt) {
					?>
					<tr>
						<th scope="row">영수증</th>
						<td>
							<?php
							if($od['od_settle_case'] == '휴대폰')
							{
							?>
							<a href="javascript:;" onclick="window.open('https://admin.kcp.co.kr/Modules/Bill/ADSA_MCASH_N_Receipt.jsp?a_trade_no=<?php echo $od['od_tno']; ?>', 'winreceipt', 'width=500,height=690')">영수증 출력</a>
							<?php
							}

							if($od['od_settle_case'] == '신용카드')
							{
							?>
							<a href="javascript:;" onclick="window.open('http://admin.kcp.co.kr/Modules/Sale/Card/ADSA_CARD_BILL_Receipt.jsp?c_trade_no=<?php echo $od['od_tno']; ?>', 'winreceipt', 'width=620,height=800')">영수증 출력</a>
							<?php
							}
							?>
						<td>
						</td>
					</tr>
					<?php
					}

					if ($od['od_receipt_point'] > 0)
					{
					?>
					<tr>
						<th scope="row">포인트사용</th>
						<td><?php echo display_point($od['od_receipt_point']); ?></td>
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

					// 현금영수증 발급을 사용하는 경우에만
					if ($default['de_taxsave_use']) {
						// 미수금이 없고 현금일 경우에만 현금영수증을 발급 할 수 있습니다.
						if ($misu_price == 0 && $od['od_receipt_price'] && ($od['od_settle_case'] == '무통장' || $od['od_settle_case'] == '계좌이체' || $od['od_settle_case'] == '가상계좌')) {
					?>
					<tr>
						<th scope="row">현금영수증</th>
						<td>
						<?php
						if ($od['od_cash'])
						{
						?>
							<a href="javascript:;" onclick="window.open('https://admin.kcp.co.kr/Modules/Service/Cash/Cash_Bill_Common_View.jsp?cash_no=<?php echo $od['od_cash_no']; ?>', 'taxsave_receipt', 'width=360,height=647,scrollbars=0,menus=0');" class="btn_frmline">현금영수증 확인하기</a>
						<?php
						}
						else
						{
						?>
							<a href="javascript:;" onclick="window.open('<?php echo G5_SHOP_URL; ?>/taxsave_kcp.php?od_id=<?php echo $od_id; ?>', 'taxsave', 'width=550,height=400,scrollbars=1,menus=0');" class="btn_frmline">현금영수증을 발급하시려면 클릭하십시오.</a>
						<?php } ?>
						</td>
					</tr>
					<?php
						}
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
						<th scope="row">전화번호</th>
						<td><?php echo $od['od_tel']; ?></td>
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
						<th scope="row">전화번호</th>
						<td><?php echo $od['od_b_tel']; ?></td>
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
					// 희망배송일을 사용한다면
					if ($default['de_hope_date_use'])
					{
					?>
					<tr>
						<th scope="row">희망배송일</td>
						<td><?php echo substr($od['od_hope_date'],0,10).' ('.get_yoil($od['od_hope_date']).')' ;?></td>
					</tr>
					<?php }
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
		-->

		<section id="sod_fin_cancel">
			<h2>주문취소</h2>
			<?php
			// 취소한 내역이 없다면
			if ($cancel_price == 0) {
				if ($custom_cancel) {
			?>
			<button type="button" onclick="document.getElementById('sod_fin_cancelfrm').style.display='block';">주문 취소하기</button>

			<div id="sod_fin_cancelfrm">
				<form method="post" action="./orderinquirycancel.php" onsubmit="return fcancel_check(this);">
				<input type="hidden" name="od_id"  value="<?php echo $od['od_id']; ?>">
				<input type="hidden" name="token"  value="<?php echo $token; ?>">

				<label for="cancel_memo">취소사유</label>
				<input type="text" name="cancel_memo" id="cancel_memo" required class="frm_input required" size="40" maxlength="100">
				<input type="submit" value="확인" class="btn_frmline">

				</form>
			</div>
			<?php
				}
			} else {
			?>
			<p>주문 취소, 반품, 품절된 내역이 있습니다.</p>
			<?php } ?>
		</section>
		

		<?php if ($od['od_settle_case'] == '가상계좌' && $od['od_misu'] > 0 && $default['de_card_test'] && $is_admin) {
		preg_match("/\s{1}([^\s]+)\s?/", $od['od_bank_account'], $matchs);
		$deposit_no = trim($matchs[1]);
		?>
		<fieldset>
		<legend>모의입금처리</legend>
		<p>관리자가 가상계좌 테스트를 한 경우에만 보입니다.</p>
		<form method="post" action="http://devadmin.kcp.co.kr/Modules/Noti/TEST_Vcnt_Noti_Proc.jsp" target="_blank">
		<input type="text" name="e_trade_no" value="<?php echo $od['od_tno']; ?>" size="80"><br />
		<input type="text" name="deposit_no" value="<?php echo $deposit_no; ?>" size="80"><br />
		<input type="text" name="req_name" value="<?php echo $od['od_name']; ?>" size="80"><br />
		<input type="text" name="noti_url" value="<?php echo G5_SHOP_URL; ?>/settle_kcp_common.php" size="80"><br /><br />
		<input type="submit" value="입금통보 테스트">
		</form>
		</fieldset>
		<?php } ?>

	</div>
</div>
<!-- } 주문상세내역 끝 -->

<script>
$(function() {
    $("#sod_sts_explan_open").on("click", function() {
        var $explan = $("#sod_sts_explan");
        if($explan.is(":animated"))
            return false;

        if($explan.is(":visible")) {
            $explan.slideUp(200);
            $("#sod_sts_explan_open").text("상태설명보기");
        } else {
            $explan.slideDown(200);
            $("#sod_sts_explan_open").text("상태설명닫기");
        }
    });

    $("#sod_sts_explan_close").on("click", function() {
        var $explan = $("#sod_sts_explan");
        if($explan.is(":animated"))
            return false;

        $explan.slideUp(200);
        $("#sod_sts_explan_open").text("상태설명보기");
    });
});

function fcancel_check(f)
{
    if(!confirm("주문을 정말 취소하시겠습니까?"))
        return false;

    var memo = f.cancel_memo.value;
    if(memo == "") {
        alert("취소사유를 입력해 주십시오.");
        return false;
    }

    return true;
}
</script>

<?php
include_once('./_tail.php');
?>