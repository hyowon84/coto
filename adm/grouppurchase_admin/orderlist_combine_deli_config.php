<?php
$sub_menu = '500300';
$sub_sub_menu = '3';

include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$g5['title'] = '통합배송관리';
include_once (G5_ADMIN_PATH.'/admin.head.php');
include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');

$where = array();

$sql_search = "";
if ($search != "") {
    if ($sel_field != "") {
        $where[] = " $sel_field like '%$search%' ";
    }

    if ($save_search != $search) {
        $page = 1;
    }
}

$where[] = " od_id in ( select distinct od_id from $g5[g5_shop_cart_table] where ct_gubun='P' ) ";

if ($od_status) {
    switch($od_status) {
        case '전체취소':
            $where[] = " od_status = '취소' ";
            break;
        case '부분취소':
            $where[] = " od_status IN('입금대기', '결제완료', '상품준비중', '배송대기', '배송중', '배송완료') and od_cancel_price > 0 ";
            break;
        default:
            $where[] = " od_status = '$od_status' ";
            break;
    }

    switch ($od_status) {
        case '입금대기' :
            $sort1 = "od_id";
            $sort2 = "desc";
            break;
        case '결제완료' :   // 결제완료
            $sort1 = "od_receipt_time";
            $sort2 = "desc";
            break;
        case '배송중' :   // 배송중
            $sort1 = "od_invoice_time";
            $sort2 = "desc";
            break;
    }
}

if ($od_settle_case) {
    $where[] = " od_settle_case = '$od_settle_case' ";
}

if ($od_misu) {
    $where[] = " od_misu != 0 ";
}

if ($od_cancel_price) {
    $where[] = " od_cancel_price != 0 ";
}

if ($od_refund_price) {
    $where[] = " od_refund_price != 0 ";
}

if ($od_receipt_point) {
    $where[] = " od_receipt_point != 0 ";
}

if ($od_coupon) {
    $where[] = " od_coupon != 0 ";
}

if ($od_escrow) {
    $where[] = " od_escrow = 1 ";
}

if ($fr_date && $to_date) {
    $where[] = " od_time between '$fr_date 00:00:00' and '$to_date 23:59:59' ";
}

if ($where) {
    $sql_search = ' where '.implode(' and ', $where);
}

if ($sel_field == "")  $sel_field = "od_id";
if ($sort1 == "") $sort1 = "od_id";
if ($sort2 == "") $sort2 = "desc";

$sql_common = " from {$g5['g5_shop_order_table']} $sql_search and combine_deli_status='y' ";

$sql = " select count(od_id) as cnt " . $sql_common;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql  = " select *,
            (od_cart_coupon + od_coupon + od_send_coupon) as couponprice
           $sql_common
           order by $sort1 $sort2
		   , combine_deli_code desc
           limit $from_record, $rows ";

$result = sql_query($sql);

$qstr1 = "sel_field=$sel_field&amp;search=$search&amp;save_search=$search";
$qstr = "$qstr1&amp;sort1=$sort1&amp;sort2=$sort2&amp;page=$page";

$listall = '<a href="'.$_SERVER['PHP_SELF'].'" class="ov_listall">전체목록</a>';

// 주문삭제 히스토리 테이블 필드 추가
if(!sql_query(" select mb_id from {$g5['g5_shop_order_delete_table']} limit 1 ", false)) {
    sql_query(" ALTER TABLE `{$g5['g5_shop_order_delete_table']}`
                    ADD `mb_id` varchar(20) NOT NULL DEFAULT '' AFTER `de_data`,
                    ADD `de_ip` varchar(255) NOT NULL DEFAULT '' AFTER `mb_id`,
                    ADD `de_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `de_ip` ", true);
}
?>

<div class="local_ov01 local_ov">
    <?php echo $listall; ?>
    전체 주문내역 <?php echo number_format($total_count); ?>건
    <?php if($od_status == '상품준비중' && $total_count > 0) { ?>
    <a href="./orderdelivery.php" id="order_delivery" class="ov_a">엑셀배송처리</a>
    <?php } ?>
</div>

<form name="frmorderlist" class="local_sch01 local_sch">
<input type="hidden" name="doc" value="<?php echo $doc; ?>">
<input type="hidden" name="sort1" value="<?php echo $sort1; ?>">
<input type="hidden" name="sort2" value="<?php echo $sort2; ?>">
<input type="hidden" name="page" value="<?php echo $page; ?>">
<input type="hidden" name="save_search" value="<?php echo $search; ?>">

<label for="sel_field" class="sound_only">검색대상</label>
<select name="sel_field" id="sel_field">
    <option value="od_id" <?php echo get_selected($sel_field, 'od_id'); ?>>주문번호</option>
    <option value="mb_id" <?php echo get_selected($sel_field, 'mb_id'); ?>>회원 ID</option>
    <option value="od_name" <?php echo get_selected($sel_field, 'od_name'); ?>>주문자</option>
    <option value="od_tel" <?php echo get_selected($sel_field, 'od_tel'); ?>>주문자전화</option>
    <option value="od_hp" <?php echo get_selected($sel_field, 'od_hp'); ?>>주문자핸드폰</option>
    <option value="od_b_name" <?php echo get_selected($sel_field, 'od_b_name'); ?>>받는분</option>
    <option value="od_b_tel" <?php echo get_selected($sel_field, 'od_b_tel'); ?>>받는분전화</option>
    <option value="od_b_hp" <?php echo get_selected($sel_field, 'od_b_hp'); ?>>받는분핸드폰</option>
    <option value="od_deposit_name" <?php echo get_selected($sel_field, 'od_deposit_name'); ?>>입금자</option>
    <option value="od_invoice" <?php echo get_selected($sel_field, 'od_invoice'); ?>>운송장번호</option>
</select>

<label for="search" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="search" value="<?php echo $search; ?>" id="search" required class="required frm_input" autocomplete="off">
<input type="submit" value="검색" class="btn_submit">

</form>

<form class="local_sch02 local_sch">

<div>
    <strong>결제수단</strong>
    <input type="radio" name="od_settle_case" value="" id="od_settle_case01"        <?php echo get_checked($od_settle_case, '');          ?>>
    <label for="od_settle_case01">전체</label>
    <input type="radio" name="od_settle_case" value="무통장" id="od_settle_case02"   <?php echo get_checked($od_settle_case, '무통장');    ?>>
    <label for="od_settle_case02">무통장</label>
    <input type="radio" name="od_settle_case" value="가상계좌" id="od_settle_case03" <?php echo get_checked($od_settle_case, '가상계좌');  ?>>
    <label for="od_settle_case03">가상계좌</label>
    <input type="radio" name="od_settle_case" value="계좌이체" id="od_settle_case04" <?php echo get_checked($od_settle_case, '계좌이체');  ?>>
    <label for="od_settle_case04">계좌이체</label>
    <input type="radio" name="od_settle_case" value="휴대폰" id="od_settle_case05"   <?php echo get_checked($od_settle_case, '휴대폰');    ?>>
    <label for="od_settle_case05">휴대폰</label>
    <input type="radio" name="od_settle_case" value="신용카드" id="od_settle_case06" <?php echo get_checked($od_settle_case, '신용카드');  ?>>
    <label for="od_settle_case06">신용카드</label>
</div>

<div>
    <strong>기타선택</strong>
    <input type="checkbox" name="od_misu" value="Y" id="od_misu01" <?php echo get_checked($od_misu, 'Y'); ?>>
    <label for="od_misu01">미수금</label>
    <input type="checkbox" name="od_cancel_price" value="Y" id="od_misu02" <?php echo get_checked($od_cancel_price, 'Y'); ?>>
    <label for="od_misu02">반품,품절</label>
    <input type="checkbox" name="od_refund_price" value="Y" id="od_misu03" <?php echo get_checked($od_refund_price, 'Y'); ?>>
    <label for="od_misu03">환불</label>
    <input type="checkbox" name="od_receipt_point" value="Y" id="od_misu04" <?php echo get_checked($od_receipt_point, 'Y'); ?>>
    <label for="od_misu04">포인트주문</label>
    <input type="checkbox" name="od_coupon" value="Y" id="od_misu05" <?php echo get_checked($od_coupon, 'Y'); ?>>
    <label for="od_misu05">쿠폰</label>
    <?php if($default['de_escrow_use']) { ?>
    <input type="checkbox" name="od_escrow" value="Y" id="od_misu06" <?php echo get_checked($od_escrow, 'Y'); ?>>
    <label for="od_misu06">에스크로</label>
    <?php } ?>
</div>

<div class="sch_last">
    <strong>주문일자</strong>
    <input type="text" id="fr_date"  name="fr_date" value="<?php echo $fr_date; ?>" class="frm_input" size="10" maxlength="10"> ~
    <input type="text" id="to_date"  name="to_date" value="<?php echo $to_date; ?>" class="frm_input" size="10" maxlength="10">
    <button type="button" onclick="javascript:set_date('오늘');">오늘</button>
    <button type="button" onclick="javascript:set_date('어제');">어제</button>
    <button type="button" onclick="javascript:set_date('이번주');">이번주</button>
    <button type="button" onclick="javascript:set_date('이번달');">이번달</button>
    <button type="button" onclick="javascript:set_date('지난주');">지난주</button>
    <button type="button" onclick="javascript:set_date('지난달');">지난달</button>
    <button type="button" onclick="javascript:set_date('전체');">전체</button>
    <input type="submit" value="검색" class="btn_submit">
</div>
</form>

<!--<form name="forderlist" id="forderlist" onsubmit="return forderlist_submit(this);" method="post" autocomplete="off">-->
<form name="forderlist" id="forderlist" method="post">
<input type="hidden" name="search_od_status" value="<?php echo $od_status; ?>">
<input type="hidden" name="url" value="<?=$_SERVER[REQUEST_URI]?>">
<input type="hidden" name="modify_status" value="">

<div style="margin:0 0 10px 20px;">
	<select name="od_status" style="height:36px;">
		<option value="배송중">배송중</option>
		<option value="취소">주문취소</option>
	</select>
	<div class="order_modify" idx="1" style="display:inline-block;padding:10px;border:1px solid #ccc;background:#f0f0f0;text-decoration:none;cursor:pointer">상태변경</div>
	/
	<div class="order_modify" idx="2" style="display:inline-block;padding:10px;border:1px solid #ccc;background:#f0f0f0;text-decoration:none;cursor:pointer">선택수정</div>
</div>

<div class="tbl_head02 tbl_wrap">
    <table id="sodr_list">
    <caption>주문 내역 목록</caption>
    <thead>
    <tr>
        <th scope="col" rowspan="3">
            <label for="chkall" class="sound_only">주문 전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
		<th scope="col" rowspan="3">
			통합배송
		</th>
        <!-- <th scope="col" id="th_odrnum"><a href="<?php echo title_sort("od_id", 1)."&amp;$qstr1"; ?>">주문번호</a></th> -->
        <th scope="col" id="th_odrnum" rowspan="2" colspan="2"><a href="<?php echo title_sort("od_id", 1)."&amp;$qstr1"; ?>">주문번호</a></th>
        <th scope="col" id="th_odrer">주문자</th>
        <th scope="col" id="th_odrertel">주문자전화</th>
        <th scope="col" id="th_recvr">받는분</th>
		<th scope="col" rowspan="3">상품명</th>
        <th scope="col" rowspan="3">주문금액</th>
		<th scope="col" rowspan="3">배송금액</th>
        <th scope="col" rowspan="3">총금액</th>
        <th scope="col" rowspan="3">입금합계</th>
        <th scope="col" rowspan="3">주문취소</th>
        <th scope="col" rowspan="3">쿠폰</th>
        <th scope="col" rowspan="3">미수금</th>
        <th scope="col" rowspan="3">보기</th>
    </tr>
    <tr>
        <!-- <th scope="col" id="th_odrdate">주문일시</th> -->
        <!-- <th scope="col">결제수단</th> -->
        <!-- <th scope="col" id="th_odrid"><a href="<?php echo title_sort("mb_id")."&amp;$qstr1"; ?>">회원ID</a></th> -->
        <th scope="col" id="th_odrid">회원ID</th>
        <th scope="col" id="th_odrcnt">주문상품수</th>
        <th scope="col" id="th_odrall">누적주문수</th>
    </tr>
    <tr>
        <!-- <th scope="col">배송일시</th> -->
        <th scope="col" id="odrstat">주문상태</th>
        <th scope="col" id="odrpay">결제수단</th>
        <th scope="col" id="delino">운송장번호</th>
        <th scope="col" id="delicom">배송회사</th>
        <th scope="col" id="delidate">배송일시</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++)
    {
        // 결제 수단
        $s_receipt_way = $s_br = "";
        if ($row['od_settle_case'])
        {
            $s_receipt_way = $row['od_settle_case'];
            $s_br = '<br />';
        }
        else
        {
            $s_receipt_way = '결제수단없음';
            $s_br = '<br />';
        }

        if ($row['od_receipt_point'] > 0)
            $s_receipt_way .= $s_br."포인트";

        $mb_nick = get_sideview($row['mb_id'], $row['od_name'], $row['od_email'], '');

        $od_cnt = 0;
        if ($row['mb_id'])
        {
            $sql2 = " select count(*) as cnt from {$g5['g5_shop_order_table']} where mb_id = '{$row['mb_id']}' ";
            $row2 = sql_fetch($sql2);
            $od_cnt = $row2['cnt'];
        }

        // 주문 번호에 device 표시
        $od_mobile = '';
        if($row['od_mobile'])
            $od_mobile = '(M)';

        // 주문 번호에 에스크로 표시
        $od_paytype = '';
        if($default['de_escrow_use'] && $row['od_escrow'])
            $od_paytype = '<span class="list_escrow">에스크로</span>';

        $uid = md5($row['od_id'].$row['od_time'].$row['od_ip']);

        $invoice_time = is_null_time($row['od_invoice_time']) ? G5_TIME_YMDHIS : $row['od_invoice_time'];
        $delivery_company = $row['od_delivery_company'] ? $row['od_delivery_company'] : $default['de_delivery_company'];

        $bg = 'bg'.($i%2);
        $td_color = 0;
        if($row['od_cancel_price'] > 0) {
            $bg .= 'cancel';
            $td_color = 1;
        }

		if($k < 0){
			$k = 0;
		}

		if($k == 0){
			$rowspan = sql_fetch("select count(*) as cnt from {$g5['g5_shop_order_table']} where combine_deli_code != '' and combine_deli_code='".$row[combine_deli_code]."' and combine_deli_status='y' and od_id in (select distinct od_id from g5_shop_cart where ct_gubun = 'P') ");

			if($rowspan[cnt] == 0){
				$rowspan[cnt] = 1;
				$k = 1;
				$item_deli_type = "일반배송";
			}else{
				$rowspan[cnt] = $rowspan[cnt];
				$k = $rowspan[cnt];
				$item_deli_type = "통합배송";
			}
		}

		$it_name = sql_fetch("select * from {$g5['g5_shop_cart_table']} where od_id='".$row['od_id']."' order by ct_id desc limit 0, 1 ");
    ?>
    <tr class="orderlist<?php echo ' '.$bg; ?>">

		<?if($rowspan[cnt] == $k){?>
        <td rowspan="<?=$rowspan[cnt] * 3?>" class="td_chk">
            <!--<input type="hidden" name="od_id[<?php echo $i ?>]" value="<?php echo $row['od_id'] ?>" id="od_id_<?php echo $i ?>">-->
			<input type="hidden" name="od_id[<?php echo $i ?>]" value="<?php echo $row['combine_deli_code'] ?>" id="od_id_<?php echo $i ?>">
            <label for="chk_<?php echo $i; ?>" class="sound_only">주문번호 <?php echo $row['od_id']; ?></label>
            <input type="checkbox" name="chk[]" value="<?php echo $i ?>" id="chk_<?php echo $i ?>">
        </td>
		<?}?>

		<?
		if($rowspan[cnt] == $k){
			if($row['od_invoice_time'] != "0000-00-00 00:00:00"){
				$row['od_invoice_time'] = $row['od_invoice_time'];
			}else{
				$row['od_invoice_time'] = date("Y-m-d H:i:s");
			}
		?>
		<td rowspan="<?=$rowspan[cnt] * 3?>" class="td_odrnum2">
            <!--<span style="font-size:14px;font-weight:bold;"><?=$item_deli_type?></span></br>-->
			운송장번호 <input type="text" name="od_invoice_com[<?php echo $i ?>]" class="frm_input" value="<?php echo $row['od_invoice']; ?>">
			배송회사
			<select name="od_delivery_company_com[<?php echo $i ?>]">
				<?php echo get_delivery_company($delivery_company); ?>
			</select>
			배송일시 <input type="text" name="od_invoice_time_com[<?php echo $i ?>]" class="frm_input" value="<?php echo $row['od_invoice_time']; ?>">
        </td>
		<?}?>

        <!-- <td headers="th_ordnum" class="td_odrnum2">
            <?php echo $od_mobile; ?>
            <a href="<?php echo G5_SHOP_URL; ?>/orderinquiryview.php?od_id=<?php echo $row['od_id']; ?>&amp;uid=<?php echo $uid; ?>"><?php echo $row['od_id']; ?></a><br>
        </td> -->
        <td headers="th_ordnum" class="td_odrnum2" rowspan="2" colspan="2">
            <a href="<?php echo G5_SHOP_URL; ?>/orderinquiryview.php?od_id=<?php echo $row['od_id']; ?>&amp;uid=<?php echo $uid; ?>" class="orderitem"><?php echo substr($row['od_id'],0,8).'-'.substr($row['od_id'],8); ?></a>
            <?php echo $od_mobile; ?>
            <?php echo $od_paytype; ?>
        </td>
        <td headers="th_odrer" class="td_name"><?php echo $mb_nick; ?></td>
        <td headers="th_odrertel" class="td_tel"><?php echo $row['od_tel']; ?></td>
        <td headers="th_recvr" class="td_name"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?sort1=<?php echo $sort1; ?>&amp;sort2=<?php echo $sort2; ?>&amp;sel_field=od_b_name&amp;search=<?php echo $row['od_b_name']; ?>"><?php echo $row['od_b_name']; ?></a></td>
		<td rowspan="3" style="text-align:left;">
			<?php echo $it_name['it_name']; ?>
			<?
			if($row['od_cart_count'] > 1){
				$od_cart_count = $row['od_cart_count']-1;
				echo " 외 ".$od_cart_count."건";
			}
			?>
		</td>
		<td rowspan="3" class="td_numsum"><?php echo number_format($row['od_cart_price']); ?></td>
		<td rowspan="3" class="td_numsum"><?php echo number_format($row['od_send_cost'] + $row['od_send_cost2']); ?></td>
        <td rowspan="3" class="td_numsum"><?php echo number_format($row['od_cart_price'] + $row['od_send_cost'] + $row['od_send_cost2']); ?></td>
        <td rowspan="3" class="td_numincome"><?php echo number_format($row['od_receipt_price']); ?></td>
        <td rowspan="3" class="td_numcancel<?php echo $td_color; ?>"><?php echo number_format($row['od_cancel_price']); ?></td>
        <td rowspan="3" class="td_numcoupon"><?php echo number_format($row['couponprice']); ?></td>
        <td rowspan="3" class="td_numrdy"><?php echo number_format($row['od_misu']); ?></td>
        <td rowspan="3" class="td_mngsmall">
            <a href="./orderform.php?od_id=<?php echo $row['od_id']; ?>&amp;<?php echo $qstr; ?>" class="mng_mod"><span class="sound_only"><?php echo $row['od_id']; ?> </span>보기</a>
        </td>
    </tr>
    <tr class="<?php echo $bg; ?>">
        <!-- <td headers="th_odrdate"><span class="sound_only">주문일시 </span><?php echo $row['od_time']; ?></td> -->
        <!-- <td class="td_payby">
            <input type="hidden" name="current_settle_case[<?php echo $i ?>]" value="<?php echo $row['od_settle_case'] ?>">
            <?php echo $s_receipt_way; ?>
        </td> -->
        <!-- <td headers="th_odrid" class="td_name"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?sort1=<?php echo $sort1; ?>&amp;sort2=<?php echo $sort2; ?>&amp;sel_field=mb_id&amp;search=<?php echo $row['mb_id']; ?>"><?php echo $row['mb_id']; ?></a></td> -->
        <td headers="th_odrid">
            <?php if ($row['mb_id']) { ?>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?sort1=<?php echo $sort1; ?>&amp;sort2=<?php echo $sort2; ?>&amp;sel_field=mb_id&amp;search=<?php echo $row['mb_id']; ?>"><?php echo $row['mb_id']; ?></a>
            <?php } else { ?>
            비회원
            <?php } ?>
        </td>
        <td headers="th_odrcnt"><?php echo $row['od_cart_count']; ?>건</td>
        <td headers="th_odrall"><?php echo $od_cnt; ?>건</td>
    </tr>
    <tr class="<?php echo $bg; ?>">
        <td headers="th_odrstat" class="td_odrstatus">
            <input type="hidden" name="current_status[<?php echo $i ?>]" value="<?php echo $row['od_status'] ?>">
            <?php echo $row['od_status']; ?>
        </td>
        <td headers="th_odrpay" class="td_payby">
            <input type="hidden" name="current_settle_case[<?php echo $i ?>]" value="<?php echo $row['od_settle_case'] ?>">
            <?php echo $s_receipt_way; ?>
        </td>
        <td headers="th_delino" class="td_delino">
			<input type="text" name="od_invoice[<?php echo $row['combine_deli_code'] ?>][<?php echo $row['od_id']; ?>]" value="<?php echo $row['od_invoice']; ?>" class="frm_input" size="10">
        </td>
        <td headers="th_delicom">
			<select name="od_delivery_company[<?php echo $row['combine_deli_code'] ?>][<?php echo $row['od_id']; ?>]">
				<?php echo get_delivery_company($delivery_company); ?>
			</select>
			<!--<input type="text" name="od_delivery_company[<?php echo $i; ?>]" value="<?php echo $delivery_company; ?>" class="frm_input" size="10">-->
        </td>
        <td headers="th_delidate">
			<input type="text" name="od_invoice_time[<?php echo $row['combine_deli_code'] ?>][<?php echo $row['od_id']; ?>]" value="<?php echo $invoice_time; ?>" class="frm_input" size="10" maxlength="19">
        </td>
    </tr>
    <?php
        $tot_itemcount     += $row['od_cart_count'];
        $tot_orderprice    += ($row['od_cart_price'] + $row['od_send_cost'] + $row['od_send_cost2']);
        $tot_ordercancel   += $row['od_cancel_price'];
        $tot_receiptprice  += $row['od_receipt_price'];
        $tot_couponprice   += $row['couponprice'];
        $tot_misu          += $row['od_misu'];
		$tot_od_cart_price += $row['od_cart_price'];
		$tot_od_send_cost  += $row['od_send_cost'] + $row['od_send_cost2'];

		$k--;
    }
    mysql_free_result($result);
    if ($i == 0)
        echo '<tr><td colspan="12" class="empty_table">자료가 없습니다.</td></tr>';
    ?>
    </tbody>
    <tfoot>
    <tr class="orderlist">
        <th scope="row" colspan="3">&nbsp;</th>
        <td>&nbsp;</td>
		<td>&nbsp;</td>
        <td><?php echo number_format($tot_itemcount); ?>건</td>
        <th scope="row">합 계</th>
		<td></td>
		<td><?php echo number_format($tot_od_cart_price); ?></td>
		<td><?php echo number_format($tot_od_send_cost); ?></td>
        <td><?php echo number_format($tot_orderprice); ?></td>
        <td><?php echo number_format($tot_receiptprice); ?></td>
        <td><?php echo number_format($tot_ordercancel); ?></td>
        <td><?php echo number_format($tot_couponprice); ?></td>
        <td><?php echo number_format($tot_misu); ?></td>
        <td></td>
    </tr>
    </tfoot>
    </table>
</div>

<div class="local_cmd01 local_cmd">
<?php if (($od_status == '' || $od_status == '완료' || $od_status == '전체취소' || $od_status == '부분취소') == false) {
    // 검색된 주문상태가 '전체', '완료', '전체취소', '부분취소' 가 아니라면
?>
    <label for="od_status" class="cmd_tit">주문상태 변경</label>
    <?php
    $change_status = "";
    if ($od_status == '입금대기') $change_status = "결재완료";
    if ($od_status == '결재완료') $change_status = "상품준비중";
	if ($od_status == '상품준비중') $change_status = "배송대기";
    if ($od_status == '상품준비중') $change_status = "배송중";
    if ($od_status == '배송중') $change_status = "배송완료";
    ?>
    <label><input type="checkbox" name="od_status" value="<?php echo $change_status; ?>"> '<?php echo $od_status ?>'상태에서 '<strong><?php echo $change_status ?></strong>'상태로 변경합니다.</label>
    <?php if($od_status == '입금대기' || $od_status == '상품준비중') { ?>
    <input type="checkbox" name="od_send_mail" value="1" id="od_send_mail" checked="checked">
    <label for="od_send_mail"><?php echo $change_status; ?>안내 메일</label>
    <input type="checkbox" name="send_sms" value="1" id="od_send_sms" checked="checked">
    <label for="od_send_sms"><?php echo $change_status; ?>안내 SMS</label>
    <?php } ?>
    <?php if($od_status == '상품준비중') { ?>
    <input type="checkbox" name="send_escrow" value="1" id="od_send_escrow">
    <label for="od_send_escrow">에스크로배송등록</label>
    <?php } ?>
    <input type="submit" value="선택수정" class="btn_submit" onclick="document.pressed=this.value">
<?php } ?>
    <?php if ($od_status == '입금대기' || $od_status == '전체취소') { ?> <span>주문상태에서만 삭제가 가능합니다.</span> <input type="submit" value="선택삭제" class="btn_submit" onclick="document.pressed=this.value"><?php } ?>
</div>

<div class="local_desc02 local_desc">
<p>
    &lt;무통장&gt;인 경우에만 &lt;입금대기&gt;에서 &lt;결재완료&gt;으로 변경됩니다. 가상계좌는 입금시 자동으로 &lt;결재완료&gt;처리됩니다.<br>
    &lt;상품준비중&gt;에서 &lt;배송중&gt;으로 변경시 &lt;에스크로배송등록&gt;을 체크하시면 에스크로 주문에 한해 KCP에 배송정보가 자동 등록됩니다.<br>
    <strong>주의!</strong> 주문번호를 클릭하여 나오는 주문상세내역의 주소를 외부에서 조회가 가능한곳에 올리지 마십시오.
</p>
</div>

</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['PHP_SELF']}?$qstr&amp;page="); ?>

<script>
$(function(){
    $("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });

    // 주문상품보기
    $(".orderitem").on("click", function() {
        var $this = $(this);
        var od_id = $this.text().replace(/[^0-9]/g, "");

        if($this.next("#orderitemlist").size())
            return false;

        $("#orderitemlist").remove();

        $.post(
            "./ajax.orderitem.php",
            { od_id: od_id },
            function(data) {
                $this.after("<div id=\"orderitemlist\"><div class=\"itemlist\"></div></div>");
                $("#orderitemlist .itemlist")
                    .html(data)
                    .append("<div id=\"orderitemlist_close\"><button type=\"button\" id=\"orderitemlist-x\" class=\"btn_frmline\">닫기</button></div>");
            }
        );

        return false;
    });

    // 상품리스트 닫기
    $(".orderitemlist-x").on("click", function() {
        $("#orderitemlist").remove();
    });

    $("body").on("click", function() {
        $("#orderitemlist").remove();
    });

    // 엑셀배송처리창
    $("#order_delivery").on("click", function() {
        var opt = "width=600,height=450,left=10,top=10";
        window.open(this.href, "win_excel", opt);
        return false;
    });

	// 선택수정
	$(".order_modify").click(function(){
		var idx = $(this).attr("idx");
		var chk = false;

		$("input[name^='chk']").each(function(i){
			if($("input[name^='chk']").eq(i).is(":checked") == true){
				chk = true;
			}
		});

		if(chk == false){
			alert("수정하실 항목을 하나 이상 선택하세요");
			return false;
		}

		if(confirm("선택하신 항목을 수정하시겠습니까?")){
			$("form[name='forderlist']").find("input[name='modify_status']").val(idx);
			$("form[name='forderlist']").attr({"action":"./orderlist_combine_deli_config_update.php"}).submit();
		}
	});
});

function set_date(today)
{
    <?php
    $date_term = date('w', G5_SERVER_TIME);
    $week_term = $date_term + 7;
    $last_term = strtotime(date('Y-m-01', G5_SERVER_TIME));
    ?>
    if (today == "오늘") {
        document.getElementById("fr_date").value = "<?php echo G5_TIME_YMD; ?>";
        document.getElementById("to_date").value = "<?php echo G5_TIME_YMD; ?>";
    } else if (today == "어제") {
        document.getElementById("fr_date").value = "<?php echo date('Y-m-d', G5_SERVER_TIME - 86400); ?>";
        document.getElementById("to_date").value = "<?php echo date('Y-m-d', G5_SERVER_TIME - 86400); ?>";
    } else if (today == "이번주") {
        document.getElementById("fr_date").value = "<?php echo date('Y-m-d', strtotime('-'.$date_term.' days', G5_SERVER_TIME)); ?>";
        document.getElementById("to_date").value = "<?php echo date('Y-m-d', G5_SERVER_TIME); ?>";
    } else if (today == "이번달") {
        document.getElementById("fr_date").value = "<?php echo date('Y-m-01', G5_SERVER_TIME); ?>";
        document.getElementById("to_date").value = "<?php echo date('Y-m-d', G5_SERVER_TIME); ?>";
    } else if (today == "지난주") {
        document.getElementById("fr_date").value = "<?php echo date('Y-m-d', strtotime('-'.$week_term.' days', G5_SERVER_TIME)); ?>";
        document.getElementById("to_date").value = "<?php echo date('Y-m-d', strtotime('-'.($week_term - 6).' days', G5_SERVER_TIME)); ?>";
    } else if (today == "지난달") {
        document.getElementById("fr_date").value = "<?php echo date('Y-m-01', strtotime('-1 Month', $last_term)); ?>";
        document.getElementById("to_date").value = "<?php echo date('Y-m-t', strtotime('-1 Month', $last_term)); ?>";
    } else if (today == "전체") {
        document.getElementById("fr_date").value = "";
        document.getElementById("to_date").value = "";
    }
}
</script>

<script>
function forderlist_submit(f)
{
    if (!is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
    }

    /*
    switch (f.od_status.value) {
        case "" :
            alert("변경하실 주문상태를 선택하세요.");
            return false;
        case '주문' :

        default :

    }
    */

    if(document.pressed == "선택삭제") {
        if(confirm("선택한 자료를 정말 삭제하시겠습니까?")) {
            f.action = "./orderlistdelete.php";
            return true;
        }
        return false;
    }

    var change_status = f.od_status.value;

    if (f.od_status.checked == false) {
        alert("주문상태 변경에 체크하세요.");
        return false;
    }

    var chk = document.getElementsByName("chk[]");

    for (var i=0; i<chk.length; i++)
    {
        if (chk[i].checked)
        {
            var k = chk[i].value;
            var current_settle_case = f.elements['current_settle_case['+k+']'].value;
            var current_status = f.elements['current_status['+k+']'].value;

            switch (change_status)
            {
                case "결제완료" :
                    if (!(current_status == "입금대기" && current_settle_case == "무통장")) {
                        alert("'입금대기' 상태의 '무통장'(결제수단)인 경우에만 '결제완료' 처리 가능합니다.");
                        return false;
                    }
                    break;

                case "상품준비중" :
                    if (current_status != "결제완료") {
                        alert("'결제완료' 상태의 주문만 '상품준비중'로 변경이 가능합니다.");
                        return false;
                    }
                    break;

                case "배송중" :
                    if (current_status != "상품준비중") {
                        alert("'상품준비중' 상태의 주문만 '배송중'으로 변경이 가능합니다.");
                        return false;
                    }

                    var invoice      = f.elements['od_invoice['+k+']'];
                    var invoice_time = f.elements['od_invoice_time['+k+']'];
                    var delivery_company = f.elements['od_delivery_company['+k+']'];

                    if ($.trim(invoice_time.value) == '') {
                        alert("배송일시를 입력하시기 바랍니다.");
                        invoice_time.focus();
                        return false;
                    }

                    if ($.trim(delivery_company.value) == '') {
                        alert("배송업체를 입력하시기 바랍니다.");
                        delivery_company.focus();
                        return false;
                    }

                    if ($.trim(invoice.value) == '') {
                        alert("운송장번호를 입력하시기 바랍니다.");
                        invoice.focus();
                        return false;
                    }

                    break;
            }
        }
    }

    if (!confirm("선택하신 주문서의 주문상태를 '"+change_status+"'상태로 변경하시겠습니까?"))
        return false;

    f.action = "./orderlistupdate.php";
    return true;
}
</script>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
