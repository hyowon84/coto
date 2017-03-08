<?
$sub_menu = '500300';
$sub_sub_menu = '3';

include_once('./_common.php');

$cart_title3 = '주문번호';
$cart_title4 = '배송완료';

auth_check($auth[$sub_menu], "w");

$g5['title'] = "주문 내역 수정";
include_once(G5_ADMIN_PATH.'/admin.head.php');

//------------------------------------------------------------------------------
// 설정 시간이 지난 주문서 없는 장바구니 자료 삭제
//------------------------------------------------------------------------------
$keep_term = $default['de_cart_keep_term'];
if (!$keep_term) $keep_term = 15; // 기본값 15일
$beforetime = date('Y-m-d H:i:s', ( G5_SERVER_TIME - (86400 * ($keep_term - 1)) ) );
$sql = " delete from {$g5['g5_shop_cart_table']} where ct_status = '쇼핑' and ct_time <= '$beforetime' ";
sql_query($sql);
//------------------------------------------------------------------------------


// 완료된 주문에 포인트를 적립한다.
save_order_point("배송완료");


//------------------------------------------------------------------------------
// 주문서 정보
//------------------------------------------------------------------------------
$sql = " select * from {$g5['g5_shop_order_table']} where od_id = '$od_id' ";
$od = sql_fetch($sql);
if (!$od['od_id']) {
	alert("해당 주문번호로 주문서가 존재하지 않습니다.");
}

$od['mb_id'] = $od['mb_id'] ? $od['mb_id'] : "비회원";
//------------------------------------------------------------------------------


$pg_anchor = '<ul class="anchor">
<li><a href="#anc_sodr_list">주문상품 목록</a></li>
<li><a href="#anc_sodr_memo">상점메모</a></li>
<li><a href="#anc_sodr_pay">주문결제 내역</a></li>
<li><a href="#anc_sodr_orderer">주문하신 분</a></li>
</ul>';

$html_receipt_chk = '<input type="checkbox" id="od_receipt_chk" value="'.$od['od_misu'].'" onclick="chk_receipt_price()">
<label for="od_receipt_chk">결제금액 입력</label><br>';

$qstr = "sort1=$sort1&amp;sort2=$sort2&amp;sel_field=$sel_field&amp;search=$search&amp;page=$page";

// 상품목록
$sql = " select		it_id,
							ct_id,
							it_name,
							ct_gubun,
							cp_price,
							ct_notax,
							total_amount_code,
							ct_status
		   from {$g5['g5_shop_cart_table']}
		  where od_id = '{$od['od_id']}'
		  group by it_id
		  order by ct_id ";
$result = sql_query($sql);
echo $sql;


// 주소 참고항목 필드추가
if(!isset($od['od_addr3'])) {
	sql_query(" ALTER TABLE `{$g5['g5_shop_order_table']}`
					ADD `od_addr3` varchar(255) NOT NULL DEFAULT '' AFTER `od_addr2`,
					ADD `od_b_addr3` varchar(255) NOT NULL DEFAULT '' AFTER `od_b_addr2` ", true);
}

// 배송목록에 참고항목 필드추가
if(!sql_query(" select ad_addr3 from {$g5['g5_shop_order_address_table']} limit 1", false)) {
	sql_query(" ALTER TABLE `{$g5['g5_shop_order_address_table']}`
					ADD `ad_addr3` varchar(255) NOT NULL DEFAULT '' AFTER `ad_addr2` ", true);
}
?>




<section id="anc_sodr_list">
	<h2 class="h2_frm">주문상품 목록</h2>
	<? echo $pg_anchor; ?>
   

	<form name="frmorderform" method="post" action="./orderformcartupdate.php" onsubmit="return form_submit(this);">
	<input type="hidden" name="od_id" value="<? echo $od_id; ?>">
	<input type="hidden" name="mb_id" value="<? echo $od['mb_id']; ?>">
	<input type="hidden" name="od_email" value="<? echo $od['od_email']; ?>">
	<input type="hidden" name="sort1" value="<? echo $sort1; ?>">
	<input type="hidden" name="sort2" value="<? echo $sort2; ?>">
	<input type="hidden" name="sel_field" value="<? echo $sel_field; ?>">
	<input type="hidden" name="search" value="<? echo $search; ?>">
	<input type="hidden" name="page" value="<? echo $page;?>">

	<div class="tbl_head01 tbl_wrap">

		<table>
		<caption>주문 상품 목록</caption>
		<thead>
		<tr>
			<th scope="col">상품명</th>
			<th scope="col">
				<label for="sit_select_all" class="sound_only">주문 상품 전체</label>
				<input type="checkbox" id="sit_select_all">
			</th>
			<th scope="col">옵션항목</th>
			<th scope="col">상품단가($)</th>
			<th scope="col">환율(\)</th>
			<th scope="col">상품단가</th>
			<th scope="col">신청수량</th>
			<th scope="col">품절수량</th>
			<th scope="col">주문수량</th>
			<th scope="col">상품금액</th>
			<th scope="col" style="color:red">미입고수량</th>
			<th scope="col" style="color:blue">입고수량</th>
			<th scope="col">운송장번호</th>
		</tr>
		</thead>
		<tbody>
		<?
		$chk_cnt = 0;
		for($i=0; $row=sql_fetch_array($result); $i++) {
			// 상품이미지
			$image = get_it_image($row['it_id'], 50, 50);

			// 상품의 옵션정보
			$sql = " select ct_id, it_id, ct_price, ct_point, ct_qty, ct_option, ct_status, cp_price, ct_stock_use, ct_point_use, ct_send_cost, io_type, io_price, ct_notstocked_cnt , ct_gp_soldout , ct_usd_price , ct_wearing_cnt  
						from {$g5['g5_shop_cart_table']}
						where od_id = '{$od['od_id']}'
						  and it_id = '{$row['it_id']}'
						order by io_type asc, ct_id asc ";
			$res = sql_query($sql);
			$rowspan = mysql_num_rows($res);

			for($k=0; $opt=sql_fetch_array($res); $k++) {
				if($opt['io_type'])
					$opt_price = $opt['io_price'];
				else
					$opt_price = $opt['ct_price'] + $opt['io_price'];

				// 소계
				$realQty = $opt['ct_qty'] - $opt['ct_gp_soldout'];
				$ct_price['stotal'] = $opt_price * $realQty;
				$ct_point['stotal'] = $opt['ct_point'] * $realQty;
			?>
			<tr>
<? if($k == 0) {
		
		if($row[ct_gubun]=="P"){
					$image = get_gp_image($row['it_id'], 150, 150);
				?>
				<td rowspan="<?=$rowspan?>">
					<a href="./grouppurchaseform.php?w=u&amp;gp_id=<? echo $row['it_id']; ?>">
						<? echo $image; ?></a>
						
						<a href="./grouppurchaseform.php?w=u&amp;gp_id=<? echo $row['it_id']; ?>">
						<? echo stripslashes($row['it_name']); ?><br>	| CART_ID[<?=$row['ct_id']?>] | 주문상태 [<?=$row['ct_status']?>] 
						</a>
					
				</td>
	<? }else{?>
				<td rowspan="<?=$rowspan?>">
					<a href="./itemform.php?w=u&amp;it_id=<? echo $row['it_id']; ?>"> 일반구매</a>
				</td>
				<td rowspan="<?=$rowspan?>">
					<a href="./itemform.php?w=u&amp;it_id=<? echo $row['it_id']; ?>"><? echo $image; ?> <? echo stripslashes($row['it_name']); ?></a>
					<? if($od['od_tax_flag'] && $row['ct_notax']) echo '[비과세상품]'; ?>
				</td>
				<? }?>
				<td rowspan="<?=$rowspan?>" class="td_chk">
					<label for="sit_sel_<? echo $i; ?>" class="sound_only"><? echo $row['it_name']; ?> 옵션 전체선택</label>
					<input type="checkbox" id="sit_sel_<? echo $i; ?>" name="it_sel[]">
				</td>
<? } ?>
				<td>
					<label for="ct_opt_chk_<?=$chk_cnt?>" class="sound_only"><? echo $opt['ct_option']; ?></label>
					<input type="checkbox" name="ct_chk[<?=$chk_cnt?>]" id="ct_chk_<?=$chk_cnt?>" value="<?=$chk_cnt?>" class="sct_sel_<? echo $i; ?>">
					<input type="hidden" name="ct_id[<?=$chk_cnt?>]" value="<? echo $opt['ct_id']; ?>">
					<? echo $opt['ct_option']; ?>
				</td>
				<td class="td_numbig"><? echo $opt['ct_usd_price']; ?></td>
				<td class="td_numbig"><? echo number_format($opt['cp_price']); ?></td>
				<td class="td_numbig"><? echo number_format($opt['ct_price']); ?></td>
				<td class="td_num"><? echo number_format($opt['ct_qty']); ?></td>
				<td class="td_num"><? echo number_format($opt['ct_gp_soldout']); ?></td>
				<td class="td_num"><? echo number_format($realQty); ?></td>
				<td class="td_num"><? echo number_format($ct_price['stotal']); ?></td>
				<td class="td_num"><? echo number_format($opt['ct_wearing_cnt']); ?></td>
				<td class="td_num"><? echo number_format($realQty-$opt['ct_wearing_cnt']); ?></td>
				<td class="td_postalbig"><? echo $od['od_invoice']; ?></td>
			</tr>
			<?
				$chk_cnt++;
			}
			?>
		<?
		}
		?>
		</tbody>
		</table>

	</div>

	<div class="btn_list02 btn_list">
		<p>
			<input type="hidden" name="chk_cnt" value="<?=$chk_cnt?>">
			<strong>주문 및 장바구니 상태 변경</strong>
			<input type="submit" name="ct_status" value="입금대기" onclick="document.pressed=this.value">
			<input type="submit" name="ct_status" value="결제완료" onclick="document.pressed=this.value">
			<input type="submit" name="ct_status" value="상품준비중" onclick="document.pressed=this.value">
			<input type="submit" name="ct_status" value="배송중" onclick="document.pressed=this.value">
			<input type="submit" name="ct_status" value="배송완료" onclick="document.pressed=this.value">
			<input type="submit" name="ct_status" value="취소" onclick="document.pressed=this.value">
		</p>
	</div>

	<div class="local_desc01 local_desc">
		<p>입금대기, 결제완료, 해외배송대기, 해외배송중, 상품준비중, 배송대기, 배송중, 배송완료는 장바구니와 주문서 상태를 모두 변경하지만, 취소, 교환, 반품, 품절은 장바구니의 상태만 변경하며, 주문서 상태는 변경하지 않습니다.</p>
		<p>개별적인(이곳에서의) 상태 변경은 모든 작업을 수동으로 처리합니다. 예를 들어 주문에서 결제완료으로 상태 변경시 입금액(결제금액)을 포함한 모든 정보는 수동 입력으로 처리하셔야 합니다.</p>
	</div>

	</form>

	<? if ($od['od_mod_history']) { ?>
	<section id="sodr_qty_log">
		<h3>상품 수량변경 내역</h3>
		<div>
			<? echo conv_content($od['od_mod_history'], 0); ?>
		</div>
	</section>
	<? } ?>

</section>


<section id="anc_sodr_memo">
	<h2 class="h2_frm">상점메모</h2>
	<? echo $pg_anchor; ?>
	<div class="local_desc02 local_desc">
		<p>
			현재 열람 중인 주문에 대한 내용을 메모하는곳입니다.<br>
			입금, 배송 내역을 메일로 발송할 경우 함께 기록됩니다.
		</p>
	</div>

	<form name="frmorderform2" action="./orderformupdate.php" method="post">
	<input type="hidden" name="od_id" value="<? echo $od_id; ?>">
	<input type="hidden" name="sort1" value="<? echo $sort1; ?>">
	<input type="hidden" name="sort2" value="<? echo $sort2; ?>">
	<input type="hidden" name="sel_field" value="<? echo $sel_field; ?>">
	<input type="hidden" name="search" value="<? echo $search; ?>">
	<input type="hidden" name="page" value="<? echo $page; ?>">
	<input type="hidden" name="mod_type" value="memo">

	<div class="tbl_wrap">
		<label for="od_shop_memo" class="sound_only">상점메모</label>
		<textarea name="od_shop_memo" id="od_shop_memo" rows="8"><? echo stripslashes($od['od_shop_memo']); ?></textarea>
	</div>

	<div class="btn_confirm01 btn_confirm">
		<input type="submit" value="메모 수정" class="btn_submit">
	</div>

	</form>
</section>



<section id="anc_sodr_pay">
	<h2 class="h2_frm">주문결제 내역</h2>
	<? echo $pg_anchor; ?>

	<?
	// 주문금액 = 상품구입금액 + 배송비 + 추가배송비
	$amount['order'] = $od['od_cart_price'] + $od['od_send_cost'] + $od['od_send_cost2'];

	// 입금액 = 결제금액 + 포인트
	$amount['receipt'] = $od['od_receipt_price'] + $od['od_receipt_point'];

	// 쿠폰금액
	$amount['coupon'] = $od['od_cart_coupon'] + $od['od_coupon'] + $od['od_send_coupon'];

	// 취소금액
	$amount['cancel'] = $od['od_cancel_price'];

	// 미수금 = 주문금액 - 취소금액 - 입금금액 - 쿠폰금액
	//$amount['미수'] = $amount['order'] - $amount['receipt'] - $amount['coupon'];

	// 결제방법
	$s_receipt_way = $od['od_settle_case'];

	if ($od['od_receipt_point'] > 0)
		$s_receipt_way .= "+포인트";

	$mb = get_member($od['mb_id']);
	$od_nick = $mb['mb_nick'];

	$od_tax = "미발행";
	if($od['od_tax']=="1")$od_tax = "지출증빙용 - [ ".$od['tax_status'].":".$od['od_tax_hp']." ]";
	elseif($od['od_tax']=="0")$od_tax = "현금영수증 - [ ".$od['tax_status'].":".$od['od_tax_hp']." ]";
	?>

	<div class="tbl_head01 tbl_wrap">
		<!--strong class="sodr_nonpay">미수금 <? echo display_price($od['od_misu']); ?></strong-->

		<table>
		<caption>주문결제 내역</caption>
		<thead>
		<tr>
			<th scope="col">공동구매코드</th>
			<th scope="col">닉네임</th>
			<th scope="col">총신청수량</th>
			<th scope="col">상품금액</th>
			<th scope="col">포인트결제</th>
			<th scope="col">배송비</th>
			<th scope="col">주문총액</th>
			<th scope="col">진행상태</th>
			<th scope="col">결제정보</th>
			<th scope="col">현금영수증</th>
			
		</tr>
		</thead>
		<tbody>
		<tr>
			<td class="td_gpcode"><? echo $od['gp_code']; ?></td>
			
			<td class="td_mbnick"><? echo $od_nick; ?></td>
			<td class="td_num"><? echo $od['od_cart_count']; ?></td>
			<td class="td_numbig td_numsum"><? echo display_price($od['od_cart_price']); ?></td>
			<td class="td_numbig"><? echo display_point($od['od_receipt_point']); ?></td>
			<td class="td_numbig"><? echo display_price($od['od_send_cost']); ?></td>
			<td class="td_numbig td_numsum"><? echo display_price($amount['order']); ?></td>

			<td class="td_numbig td_numincome"><? echo $od['od_status']; ?></td>
			<td class="td_numbig td_numcoupon"><? echo $s_receipt_way; ?></td>
			<td><? echo $od_tax; ?></td>
		</tr>
		</tbody>
		</table>
	</div>
</section>


<section id="anc_sodr_pay_chg">
	<h2 class="h2_frm">가격 변경</h2>
<form name="frmorderform" method="post" action="./orderformreceiptupdate.php">
<input type="hidden" name="od_id" value="<? echo $od_id; ?>">
<input type="hidden" name="mb_id" value="<? echo $od['mb_id']; ?>">
<input type="hidden" name="od_email" value="<? echo $od['od_email']; ?>">
<input type="hidden" name="sort1" value="<? echo $sort1; ?>">
<input type="hidden" name="sort2" value="<? echo $sort2; ?>">
<input type="hidden" name="sel_field" value="<? echo $sel_field; ?>">
<input type="hidden" name="search" value="<? echo $search; ?>">
<input type="hidden" name="page" value="<? echo $page;?>">

	<div class="compare_wrap">
		<section id="anc_sodr_pay_chg_info">
			<div class="tbl_frm01">
				<table>
				<caption>가격 변경지 정보</caption>
				<colgroup>
					<col class="grid_4">
					<col>
				</colgroup>
				<tbody>
				<tr>
					<th scope="row"><label for="od_send_cost">배송비<span class="sound_only">필수</span></label></th>
					<td><input type="text" name="od_send_cost" value="<? echo $od['od_send_cost']; ?>" id="od_send_cost" required class="frm_input required"></td>
				</tr>
				</tbody>
				</table>
			</div>
		</section>
	</div>

	<div class="btn_confirm01 btn_confirm">
		<input type="submit" value="가격 수정" class="btn_submit">
		<a href="./orderlist.php?<? echo $qstr; ?>">목록</a>
	</div>


</form>
</section>

<section>
	<h2 class="h2_frm">주문자/배송지 정보</h2>
	<? echo $pg_anchor; ?>

	<form name="frmorderform3" action="./orderformupdate.php" method="post">
	<input type="hidden" name="od_id" value="<? echo $od_id; ?>">
	<input type="hidden" name="sort1" value="<? echo $sort1; ?>">
	<input type="hidden" name="sort2" value="<? echo $sort2; ?>">
	<input type="hidden" name="sel_field" value="<? echo $sel_field; ?>">
	<input type="hidden" name="search" value="<? echo $search; ?>">
	<input type="hidden" name="page" value="<? echo $page; ?>">
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
					<td><input type="text" name="od_name" value="<? echo $od['od_name']; ?>" id="od_name" required class="frm_input required"></td>
				</tr>
				<tr>
					<th scope="row"><label for="od_tel"><span class="sound_only">주문하신 분 </span>전화번호</label></th>
					<td><input type="text" name="od_tel" value="<? echo $od['od_tel']; ?>" id="od_tel" required class="frm_input required"></td>
				</tr>
				<tr>
					<th scope="row"><label for="od_hp"><span class="sound_only">주문하신 분 </span>핸드폰</label></th>
					<td><input type="text" name="od_hp" value="<? echo $od['od_hp']; ?>" id="od_hp" class="frm_input"></td>
				</tr>
				<tr>
					<th scope="row"><span class="sound_only">주문하시는 분 </span>주소</th>
					<td>
						<label for="od_zip1" class="sound_only">우편번호 앞자리</label>
						<input type="text" name="od_zip1" value="<? echo $od['od_zip1']; ?>" id="od_zip1" required class="frm_input required" size="4">
						-
						<label for="od_zip2" class="sound_only">우편번호 뒷자리</label>
						<input type="text" name="od_zip2" value="<? echo $od['od_zip2']; ?>" id="od_zip2" required class="frm_input required" size="4">
						<a href="<? echo G5_BBS_URL; ?>/zip.php?frm_name=frmorderform3&amp;frm_zip1=od_zip1&amp;frm_zip2=od_zip2&amp;frm_addr1=od_addr1&amp;frm_addr2=od_addr2&amp;frm_addr3=od_addr3&amp;frm_jibeon=od_addr_jibeon" id="od_zip_find" class="btn_frmline win_zip_find" target="_blank">주소 검색</a><br>
						<span id="od_win_zip" style="display:block"></span>
						<input type="text" name="od_addr1" value="<? echo $od['od_addr1']; ?>" id="od_addr1" required class="frm_input required" size="35">
						<label for="od_addr1">기본주소</label><br>
						<input type="text" name="od_addr2" value="<? echo $od['od_addr2']; ?>" id="od_addr2" class="frm_input" size="35">
						<label for="od_addr2">상세주소</label><br>
						<input type="text" name="od_addr3" value="<? echo $od['od_addr3']; ?>" id="od_addr3" class="frm_input" size="35">
						<label for="od_addr3">참고항목</label>
						<input type="hidden" name="od_addr_jibeon" value="<? echo $od['od_addr_jibeon']; ?>"><br>
						<span id="od_addr_jibeon">지번주소 : <? echo $od['od_addr_jibeon']; ?></span>
				</tr>
				<tr>
					<th scope="row"><label for="od_email"><span class="sound_only">주문하신 분 </span>E-mail</label></th>
					<td><input type="text" name="od_email" value="<? echo $od['od_email']; ?>" id="od_email" required class="frm_input email required" size="30"></td>
				</tr>
				<tr>
					<th scope="row"><span class="sound_only">주문하신 분 </span>IP Address</th>
					<td><? echo $od['od_ip']; ?></td>
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
					<td><input type="text" name="od_b_name" value="<? echo $od['od_b_name']; ?>" id="od_b_name" required class="frm_input required"></td>
				</tr>
				<tr>
					<th scope="row"><label for="od_b_tel"><span class="sound_only">받으시는 분 </span>전화번호</label></th>
					<td><input type="text" name="od_b_tel" value="<? echo $od['od_b_tel']; ?>" id="od_b_tel" required class="frm_input required"></td>
				</tr>
				<tr>
					<th scope="row"><label for="od_b_hp"><span class="sound_only">받으시는 분 </span>핸드폰</label></th>
					<td><input type="text" name="od_b_hp" value="<? echo $od['od_b_hp']; ?>" id="od_b_hp" class="frm_input required"></td>
				</tr>
				<tr>
					<th scope="row"><span class="sound_only">받으시는 분 </span>주소</th>
					<td>
						<label for="od_b_zip1" class="sound_only">우편번호 앞자리</label>
						<input type="text" name="od_b_zip1" value="<? echo $od['od_b_zip1']; ?>" id="od_b_zip1" required class="frm_input required" size="4">
						-
						<label for="od_b_zip2" class="sound_only">우편번호 뒷자리</label>
						<input type="text" name="od_b_zip2" value="<? echo $od['od_b_zip2']; ?>" id="od_b_zip2" required class="frm_input required" size="4">
						<a href="<? echo G5_BBS_URL; ?>/zip.php?frm_name=frmorderform3&amp;frm_zip1=od_b_zip1&amp;frm_zip2=od_b_zip2&amp;frm_addr1=od_b_addr1&amp;frm_addr2=od_b_addr2&amp;frm_addr3=od_b_addr3&amp;frm_jibeon=od_b_addr_jibeon" id="od_zip_findb" class="btn_frmline win_zip_find" target="_blank">주소 검색</a><br>
						<input type="text" name="od_b_addr1" value="<? echo $od['od_b_addr1']; ?>" id="od_b_addr1" required class="frm_input required" size="35">
						<label for="od_b_addr1">기본주소</label>
						<input type="text" name="od_b_addr2" value="<? echo $od['od_b_addr2']; ?>" id="od_b_addr2" class="frm_input" size="35">
						<label for="od_b_addr2">상세주소</label>
						<input type="text" name="od_b_addr3" value="<? echo $od['od_b_addr3']; ?>" id="od_b_addr3" class="frm_input" size="35">
						<label for="od_b_addr3">참고항목</label>
						<input type="hidden" name="od_b_addr_jibeon" value="<? echo $od['od_b_addr_jibeon']; ?>"><br>
						<span id="od_b_addr_jibeon">지번주소 : <? echo $od['od_b_addr_jibeon']; ?></span>
					</td>
				</tr>

				<? if ($default['de_hope_date_use']) { ?>
				<tr>
					<th scope="row"><label for="od_hope_date">희망배송일</label></th>
					<td>
						<input type="text" name="od_hope_date" value="<? echo $od['od_hope_date']; ?>" id="od_hopedate" required class="frm_input required" maxlength="10" minlength="10"> (<? echo get_yoil($od['od_hope_date']); ?>)
					</td>
				</tr>
				<? } ?>

				<tr>
					<th scope="row">전달 메세지</th>
					<td><? if ($od['od_memo']) echo nl2br($od['od_memo']);else echo "없음";?></td>
				</tr>
				</tbody>
				</table>
			</div>
		</section>

	</div>

	<div class="btn_confirm01 btn_confirm">
		<input type="submit" value="주문자/배송지 정보 수정" class="btn_submit">
		<a href="./orderlist.php?<? echo $qstr; ?>">목록</a>
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

<?
include_once(G5_ADMIN_PATH.'/admin.tail.php');
?>