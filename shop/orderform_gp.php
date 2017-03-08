<?php
include_once('./_common.php');

if($default[de_guest_cart_use] == 0){

	if(get_session("mem_order_se")){
		$member[mb_id] = get_session("mem_order_se");
		$member = sql_fetch("select * from {$g5['member_table']} where mb_id='".$member[mb_id]."'");
	}

	if($member[mb_id] == ""){
		alert("로그인 후 이용 가능합니다.");
	}
}

if (G5_IS_MOBILE) {
	include_once(G5_MSHOP_PATH.'/orderform.php');
	return;
}

set_session("ss_direct", $sw_direct);
// 장바구니가 비어있는가?
if ($sw_direct) {
	$tmp_cart_id = get_session('ss_cart_direct');
}
else {
	$tmp_cart_id = get_session('ss_cart_id');
}

if (get_cart_count($tmp_cart_id) == 0)
	alert('장바구니가 비어 있습니다.', G5_SHOP_URL.'/cart.php');

$g5['title'] = '주문서 작성';

$od_now_date = date("Y-m-d H:i:s", strtotime("+ ".$default[de_deposit_keep_term]."day"));

include_once('./_head.php');
if ($default['de_hope_date_use']) {
	include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');
}

// 새로운 주문번호 생성
$od_id = get_uniqid();
set_session('ss_order_id', $od_id);
$s_cart_id = $tmp_cart_id;
$order_action_url = G5_HTTPS_SHOP_URL.'/orderformupdate.php';
?>

<!-- 구매 시작 { -->
<script src="<?php echo G5_JS_URL; ?>/shop.js"></script>

<!-- 타이틀 -->
<div class="cart_title"><?=$g5['title']?></div>

<!-- navi -->
<div class="cart_nav">
	<ul>
		<li><img src="img/cart_nav1.gif" border="0" align="absmiddle"></li>
		<li><img src="img/cart_nav2_on.gif" border="0" align="absmiddle"></li>
		<li><img src="img/cart_nav3.gif" border="0" align="absmiddle"></li>
	</ul>
</div>

<form name="forderform" id="forderform" method="post" action="<?php echo $order_action_url; ?>" autocomplete="off">
<input type="hidden" name="buy_kind" id="buy_kind" value="<?=$buy_kind?>">

<div class="list_box">

	<div id="sod_frm">
		<!-- 주문상품 확인 시작 { -->

		<div class="tbl_head01 tbl_wrap product1">
			<table>
			<thead>
			<tr>
				<th scope="col" class="right">공동구매코드</th>
				<th scope="col" class="right">이미지</th>
				<th scope="col" class="right">상품정보</th>
				<th scope="col" class="right">수량</th>
				<th scope="col" class="right">단가</th>
				<th scope="col">합계</th>
			</tr>
			</thead>
			<tbody>
			<?php
			$tot_point = 0;
			$tot_sell_price = 0;

			$gpCartList = array();

			// $s_cart_id 로 현재 장바구니 자료 쿼리
			$sql = " select a.ct_id,
							a.it_id,
							a.it_name,
							a.od_id,
							a.ct_price,
							a.ct_point,
							a.ct_qty,
							a.ct_status,
							a.ct_send_cost,
							a.ct_gp_status,
							a.ct_type,
							a.buy_status,
							a.ct_op_option,
							a.total_amount_code, 
							b.ca_id,
							b.ca_id2,
							b.ca_id3
						from {$g5['g5_shop_cart_table']} a left join {$g5['g5_shop_group_purchase_table']} b on ( a.it_id = b.gp_id )
					where a.od_id = '$s_cart_id' and (`ct_status`='쇼핑') and a.mb_id='".$member[mb_id]."' and a.ct_gubun = 'P' $ct_type_que ";
			if($default['de_cart_keep_term']) {
				$ctime = date('Y-m-d', G5_SERVER_TIME - ($default['de_cart_keep_term'] * 86400));
				$sql .= " and substring(a.ct_time, 1, 10) >= '$ctime' ";
			}
			$sql .= " group by a.it_id ";
			$sql .= " order by a.ct_id desc ";

			$result = sql_query($sql);

			$totalPurchasePrice = $it_send_cost = 0;

			$k=0;

			for ($i=0; $row=mysql_fetch_array($result); $i++)
			{

				//신청중이면서 코드가 틀리면 삭제
				if(isPurchaseCodeCheckCartDelete($row['ct_id']))continue;
				// 판매여부체크
				isPurchaseBuyCheck($row['ca_id']);
				

				// 합계금액 계산
				$sql = " select SUM(IF(io_type = 1, (io_price * ct_qty), ((ct_price + io_price) * ct_qty))) as price,
								SUM(ct_point * (ct_qty-ct_buy_qty)) as point,
								SUM(ct_qty-ct_buy_qty) as qty 
							from {$g5['g5_shop_cart_table']}
							where ct_id = '{$row['ct_id']}'
							and od_id = '$s_cart_id'
							";


				$sum = sql_fetch($sql);


				$a1 = '<a href="'.G5_URL.'/shop/grouppurchase.php?gp_id='.$row['it_id'].'">';
				$a2 = '</a>';

				$image = get_gp_image($row['it_id'], 70, 70);

				//옵션 상품
				$op_arr = explode("|", $row[ct_op_option]);
				$op_price = 0;
				for($b = 0; $b < count($op_arr); $b++){
					if($op_arr[$b]){
						$op_row = sql_fetch("select * from {$g5['g5_shop_option2_table']} where it_id='".$row[it_id]."' and con='".$op_arr[$b]."' ");
						$op_price = $op_price + $op_row[price];
						$op_name .= $op_row[con].",";
					}
				}
				$op_name = substr($op_name, 0, strlen($op_name)-1);
				if($op_name){
					$op_name = $op_name;
				}else{
					$op_name = "";
				}

				// 가격 및 포인트
				$sell_price = $row['ct_price'] * $sum['qty'] + $op_price;
				$point		= $sum['point'];

				// 공동구매 총액한도여부
				isPurchaseBuyTotalAmountCheck($row['ca_id'],$sell_price);

			?>

			<input type="hidden" name="ct_id[]"	value="<?php echo $row['ct_id']; ?>">

			<tr>
				<td class="td_gpcode right"><?php echo $row['total_amount_code']?></td>
				<td class="sod_img right"><?php echo $image; ?></td>
				<td class="right">
					<div><?php echo $a1.strip_tags($row['it_name']).$a2; ?></div>
					<div style="margin:7px 0 0 0;"><?php $op_name;?></div>
				</td>
				<td class="td_num right"><?php echo number_format($sum['qty']);?> 개</td>
				<td class="td_numbig right"><?php echo number_format($row['ct_price'])?> 원</span></td>
				<td class="td_numbig"><?php echo number_format($sell_price)?> 원</span></td>

			</tr>

			<?php
				$k++;

				$subCaId = substr($row['ca_id'],0,4);
				$gpCartList[$subCaId]['total_price'] += $sell_price;
				$totalPurchasePrice += $sell_price; //총가격

			} // for 끝

			?>
			</tbody>
			</table>
		</div>

		<?php if(count($gpCartList)>0){?>
		<div style="color:#ff4e00;padding:00;margin:25px 0;text-align:center;">* 현재 공동구매 신청 상품의 <strong>총결제예상금액</strong>입니다.</div>

		<?php
		$priceInfoRows = count($gpCartList)+1;
		?>
		<!-- 일반상품 가격 정보 -->
		<div class="tbl_wrap price_info_gp price_info1">
			<table>
				<tbody>
				<tr height="40px">
					<th rowspan="<?php echo $priceInfoRows;?>" width="117px" class="gubun">
						총합계 
					</th>
					<th width="228px">
						상품금액
					</th>
					<th width="135px">
						할인금액
					</th>
					<th width="150px">
						배송비
					</th>
					<th>
						결제예상금액
					</th>
				</tr>
				
				<?php

				// 배열정렬
				ksort($gpCartList);
				
				$ct_send_cost = $purchaseSendCost; // 기본배송비

				$iCount = 0;

				//제품금액
				$totalPurchaseProductPrice = $totalPurchasePrice;
				$totalSendCost = $ct_send_cost * count($gpCartList);

				$totalPurchasePrice = $totalPurchaseProductPrice + $totalSendCost;

				foreach($gpCartList as $key=>$vars){
				?>
				<tr>
					<td>
						<div class="purchase_box">
						<div class="purchase_ca_name"><?php echo getPurchaseCategoryName($key)?></div>
						<div class="price_box"><span class="price"><?=number_format($vars['total_price'])?></span><span style="color:#818181;font-weight:normal;font-size:14px;">원</span></div>
						</div>
					</td>
					<td>
						<div class="purchase_box">
							<div class="icon_minus"> </div>
							<div class="price_box"><span class="price">0</span><span style="color:#818181;font-weight:normal;font-size:14px;">원</span></div>
						</div>
					</td>
					<td>
						<div class="purchase_box">
							<div class="icon_plus"> </div>
							<div class="price_box"><span class="price"><?php echo number_format($ct_send_cost)?></span><span style="color:#818181;font-weight:normal;font-size:14px;">원</span></div>
						</div>
					</td>
					<?php if($iCount==0){?>
					<td rowspan="<?php echo $priceInfoRows-1;?>">
						<div class="purchase_box">
							<div class="icon_eq"> </div>
							<div class="price_box all"><span class="price"><?php echo number_format($totalPurchasePrice);?></span><span style="font-weight:normal;font-size:14px;">원</span></div>
						</div>
					</td>
					<?php }?>
				</tr>
				<?php
					$iCount++;
				}?>
				</tbody>
			</table>
		</div>
		<?php }?>


	</div>

	<!-- 배송비 -->
	<input type="hidden" name="od_price" value="<?php echo $totalPurchaseProductPrice?>">
	<input type="hidden" name="od_send_cost" value="<?php echo $totalSendCost?>">

	<!-- 구매자 정보 입력 시작 { -->
	<section id="sod_frm_orderer">
		<h2>구매자 정보</h2>

		<div class="tbl_frm01 tbl_wrap">
			<table>
			<tbody>
			<tr>
				<th scope="row"><label for="od_name">이름</label></th>
				<td><input type="text" name="od_name" value="<?php echo $member['mb_name']; ?>" id="od_name" class="frm_input required" maxlength="20"></td>
			</tr>

			<?php if (!$is_member) { // 비회원이면 ?>
			<tr>
				<th scope="row"><label for="od_pwd">비밀번호</label></th>
				<td>
					<span class="frm_info">영,숫자 3~20자 (주문서 조회시 필요)</span>
					<input type="password" name="od_pwd" id="od_pwd" class="frm_input required" maxlength="20">
				</td>
			</tr>
			<?php } ?>

			<tr>
				<th scope="row"><label for="od_tel">연락처</label></th>
				<td><input type="text" name="od_tel" value="<?php echo $member['mb_hp']; ?>" id="od_tel" class="frm_input required" maxlength="20"></td>
			</tr>
			
			<!--
			<tr>
				<th scope="row"><label for="od_hp">핸드폰</label></th>
				<td><input type="text" name="od_hp" value="<?php// echo $member['mb_hp']; ?>" id="od_hp" class="frm_input" maxlength="20"></td>
			</tr>
			-->

			<input type="hidden" name="od_hp" value="<?php echo $member['mb_hp']; ?>" id="od_hp" class="frm_input" maxlength="20">
			
			<!--
			<?php// $zip_href = G5_BBS_URL.'/zip.php?frm_name=forderform&amp;frm_zip1=od_zip1&amp;frm_zip2=od_zip2&amp;frm_addr1=od_addr1&amp;frm_addr2=od_addr2&amp;frm_addr3=od_addr3&amp;frm_jibeon=od_addr_jibeon'; ?>
			<tr>
				<th scope="row">주소</th>
				<td>
					<label for="od_zip1" class="sound_only">우편번호 앞자리<strong class="sound_only"> 필수</strong></label>
					<input type="text" name="od_zip1" value="<?php// echo $member['mb_zip1'] ?>" id="od_zip1" required class="frm_input required" size="3" maxlength="3">
					-
					<label for="od_zip2" class="sound_only">우편번호 뒷자리<strong class="sound_only"> 필수</strong></label>
					<input type="text" name="od_zip2" value="<?php// echo $member['mb_zip2'] ?>" id="od_zip2" required class="frm_input required" size="3" maxlength="3">
					<a href="<?php// echo $zip_href; ?>" class="btn_frmline win_zip_find" target="_blank">주소 검색</a><br>
					<input type="text" name="od_addr1" value="<?php// echo $member['mb_addr1'] ?>" id="od_addr1" required class="frm_input frm_address required" size="60">
					<label for="od_addr1">기본주소<strong class="sound_only"> 필수</strong></label><br>
					<input type="text" name="od_addr2" value="<?php// echo $member['mb_addr2'] ?>" id="od_addr2" class="frm_input frm_address" size="60">
					<label for="od_addr2">상세주소</label><br>
					<input type="text" name="od_addr3" value="<?php// echo $member['mb_addr3'] ?>" id="od_addr3" readonly="readonly" class="frm_input frm_address" size="60">
					<label for="od_addr3">참고항목</label>
					<input type="hidden" name="od_addr_jibeon" value="<?php// echo $member['mb_addr_jibeon']; ?>"><br>
					<span id="od_addr_jibeon"><?php// echo ($member['mb_addr_jibeon'] ? '지번주소 : '.$member['mb_addr_jibeon'] : ''); ?></span>
				</td>
			</tr>
			-->

			<tr>
				<th scope="row"><label for="od_email">이메일</label></th>
				<td>
					<input type="hidden" name="od_email" value="<?php echo $member['mb_email']; ?>" id="od_email" class="frm_input required">
					<?php echo $member['mb_id']; ?>
				</td>
			</tr>

			<?php if ($default['de_hope_date_use']) { // 배송희망일 사용 ?>
			<tr>
				<th scope="row"><label for="od_hope_date">희망배송일</label></th>
				<td>
					<!-- <select name="od_hope_date" id="od_hope_date">
					<option value="">선택하십시오.</option>
					<?php
					for ($i=0; $i<7; $i++) {
						$sdate = date("Y-m-d", time()+86400*($default['de_hope_date_after']+$i));
						echo '<option value="'.$sdate.'">'.$sdate.' ('.get_yoil($sdate).')</option>'.PHP_EOL;
					}
					?>
					</select> -->
					<input type="text" name="od_hope_date" value="" id="od_hope_date" class="frm_input required" size="11" maxlength="10" readonly="readonly"> 이후로 배송 바랍니다.
				</td>
			</tr>
			<?php } ?>
			</tbody>
			</table>
		</div>
	</section>
	<!-- } 구매자 정보 입력 끝 -->

	<!-- 배송지 정보 입력 시작 { -->
	<section id="sod_frm_taker">
		<h2>배송지 정보</h2>

		<div class="tbl_frm01 tbl_wrap">
			<table>
			<tbody>
			<?php
			if($is_member) {
				// 배송지 이력
				$addr_list = '';
				$sep = chr(30);

				// 주문자와 동일
				$addr_list .= '<input type="radio" name="ad_sel_addr" value="same" id="ad_sel_addr_same" checked>'.PHP_EOL;
				$addr_list .= '<label for="ad_sel_addr_same">주문자와 동일</label>'.PHP_EOL;

				// 기본배송지
				$sql = " select *
							from {$g5['g5_shop_order_address_table']}
							where mb_id = '{$member['mb_id']}'
							and ad_default = '1' ";
				$row = sql_fetch($sql);
				if($row['ad_id']) {
					$val1 = $row['ad_name'].$sep.$row['ad_tel'].$sep.$row['ad_hp'].$sep.$row['ad_zip1'].$sep.$row['ad_zip2'].$sep.$row['ad_addr1'].$sep.$row['ad_addr2'].$sep.$row['ad_addr3'].$sep.$row['ad_jibeon'].$sep.$row['ad_subject'];
					$addr_list .= '<input type="radio" name="ad_sel_addr" value="'.$val1.'" id="ad_sel_addr_def">'.PHP_EOL;
					$addr_list .= '<label for="ad_sel_addr_def">기본배송지</label>'.PHP_EOL;
				}

				// 최근배송지
				$sql = " select *
							from {$g5['g5_shop_order_address_table']}
							where mb_id = '{$member['mb_id']}'
							and ad_default = '0'
							order by ad_id desc
							limit 1 ";
				$result = sql_query($sql);
				for($i=0; $row=sql_fetch_array($result); $i++) {
					$val1 = $row['ad_name'].$sep.$row['ad_tel'].$sep.$row['ad_hp'].$sep.$row['ad_zip1'].$sep.$row['ad_zip2'].$sep.$row['ad_addr1'].$sep.$row['ad_addr2'].$sep.$row['ad_addr3'].$sep.$row['ad_jibeon'].$sep.$row['ad_subject'];
					$val2 = '<label for="ad_sel_addr_'.($i+1).'">최근 배송지('.($row['ad_subject'] ? $row['ad_subject'] : $row['ad_name']).')</label>';
					$addr_list .= '<input type="radio" name="ad_sel_addr" value="'.$val1.'" id="ad_sel_addr_'.($i+1).'"> '.PHP_EOL.$val2.PHP_EOL;
				}

				$addr_list .= '<input type="radio" name="ad_sel_addr" value="new" id="od_sel_addr_new">'.PHP_EOL;
				$addr_list .= '<label for="od_sel_addr_new">새로운 배송지</label>'.PHP_EOL;

				$addr_list .='<a href="'.G5_SHOP_URL.'/orderaddress.php" id="order_address" class="btn_frmline">배송지목록</a>';
			} else {
				// 주문자와 동일
				$addr_list .= '<input type="checkbox" name="ad_sel_addr" value="same" id="ad_sel_addr_same">'.PHP_EOL;
				$addr_list .= '<label for="ad_sel_addr_same">구매자 정보와 동일</label>'.PHP_EOL;
			}
			?>
			<tr>
				<th scope="row">배송지 선택</th>
				<td>
					<?php echo $addr_list; ?>
				</td>
			</tr>

			<!--
			<?php// if($is_member) { ?>
			<tr>
				<th scope="row"><label for="ad_subject">배송지명</label></th>
				<td>
					<input type="text" name="ad_subject" id="ad_subject" class="frm_input" maxlength="20">
					<input type="checkbox" name="ad_default" id="ad_default" value="1">
					<label for="ad_default">기본배송지로 설정</label>
				</td>
			</tr>
			<?php// } ?>
			-->
			<input type="hidden" name="ad_subject" id="ad_subject">

			<tr>
				<th scope="row"><label for="od_b_name">이름</label></th>
				<td><input type="text" name="od_b_name" id="od_b_name" class="frm_input required" maxlength="20" value="<?=$member[mb_name]?>"></td>
			</tr>
			<tr>
				<th scope="row"><label for="od_b_tel">연락처</label></th>
				<td><input type="text" name="od_b_tel" id="od_b_tel" class="frm_input required" maxlength="20" value="<?php echo $member['mb_hp']; ?>"></td>
			</tr>

			<!--
			<tr>
				<th scope="row"><label for="od_b_hp">핸드폰</label></th>
				<td><input type="text" name="od_b_hp" id="od_b_hp" class="frm_input" maxlength="20"></td>
			</tr>
			-->
			<input type="hidden" name="od_b_hp" id="od_b_hp">

			<?php $zip_href = G5_BBS_URL.'/zip.php?frm_name=forderform&amp;frm_zip1=od_b_zip1&amp;frm_zip2=od_b_zip2&amp;frm_addr1=od_b_addr1&amp;frm_addr2=od_b_addr2&amp;frm_addr3=od_b_addr3&amp;frm_jibeon=od_b_addr_jibeon'; ?>
			<tr>
				<th scope="row">주소</th>
				<td id="sod_frm_addr">
					<label for="od_b_zip1" class="sound_only">우편번호 앞자리<strong class="sound_only"> 필수</strong></label>
					<input type="text" name="od_b_zip1" id="od_b_zip1" class="frm_input required" size="3" maxlength="3" value="<?=$member[mb_zip1]?>">
					-
					<label for="od_b_zip2" class="sound_only">우편번호 뒷자리<strong class="sound_only"> 필수</strong></label>
					<input type="text" name="od_b_zip2" id="od_b_zip2" class="frm_input required" size="3" maxlength="3" value="<?=$member[mb_zip2]?>">
					<a href="<?php echo $zip_href; ?>" class="btn_frmline win_zip_find" target="_blank">주소 검색</a><br>
					<input type="text" name="od_b_addr1" id="od_b_addr1" class="frm_input frm_address required" size="40" value="<?=$member[mb_addr1]?>">
					
					<!--<input type="text" name="od_b_addr2" id="od_b_addr2" class="frm_input frm_address" size="60">
					<label for="od_b_addr2">상세주소</label>-->
					<input type="hidden" name="od_b_addr2" id="od_b_addr2" value="<?=$member[mb_addr2]?>">
					<input type="text" name="od_b_addr3" id="od_b_addr3" readonly="readonly" class="frm_input frm_address" size="40" value="<?=$member[mb_addr3]?>">
					
					<input type="hidden" name="od_b_addr_jibeon" value="<?=$member[mb_addr_jibeon]?>">
					<span id="od_b_addr_jibeon"></span>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="od_memo">배송 메모</label></th>
				<td>
					<div style="float:left;width:90%;"><textarea name="od_memo" id="od_memo"></textarea></div>
					<div style="float:left;text-align:left;margin:30px 0 0 0;font-size:13px;">(0/50자)</div>
				</td>
			</tr>
			</tbody>
			</table>
		</div>
	</section>
	<!-- } 배송지 정보 입력 끝 -->



	<!-- 결제정보 입력 시작 { -->
	<?php
	$oc_cnt = $sc_cnt = 0;
	if($is_member) {
		// 주문쿠폰
		$sql = " select cp_id
					from {$g5['g5_shop_coupon_table']}
					where mb_id IN ( '{$member['mb_id']}', '전체회원' )
					and cp_method = '2'
					and cp_start <= '".G5_TIM_YMD."'
					and cp_end >= '".G5_TIME_YMD."' ";
		$res = sql_query($sql);

		for($k=0; $cp=sql_fetch_array($res); $k++) {
			if(is_used_coupon($member['mb_id'], $cp['cp_id']))
				continue;

			$oc_cnt++;
		}

		if($send_cost > 0) {
			// 배송비쿠폰
			$sql = " select cp_id
						from {$g5['g5_shop_coupon_table']}
						where mb_id IN ( '{$member['mb_id']}', '전체회원' )
						and cp_method = '3'
						and cp_start <= '".G5_TIM_YMD."'
						and cp_end >= '".G5_TIME_YMD."' ";
			$res = sql_query($sql);

			for($k=0; $cp=sql_fetch_array($res); $k++) {
				if(is_used_coupon($member['mb_id'], $cp['cp_id']))
					continue;

				$sc_cnt++;
			}
		}
	}
	?>

	<section id="sod_frm_pay">
		<h2>결제정보입력(입금 기간은 <?=$default[de_deposit_keep_term]?>일입니다.)</h2>

		<div class="tbl_frm01 tbl_wrap">
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td style="padding:0;border:0;width:65%;">
						<table>
						<tbody>
						<tr>
							<th scope="row">적립금 사용</th>
							<td style="line-height:20px;">

								<?
								$temp_point = 0;
								// 회원이면서 포인트사용이면
								if ($is_member && $config['cf_use_point'])
								{
									// 포인트 결제 사용 포인트보다 회원의 포인트가 크다면
									if ($member['mb_point'] >= $default['de_settle_min_point'])
									{
										$temp_point = (int)$default['de_settle_max_point'];

										if($temp_point > (int)$tot_sell_price)
											$temp_point = (int)$tot_sell_price;

										if($temp_point > (int)$member['mb_point'])
											$temp_point = (int)$member['mb_point'];

										$point_unit = (int)$default['de_settle_point_unit'];
										$temp_point = (int)((int)($temp_point / $point_unit) * $point_unit);
								?>
								<input type="text" name="od_temp_point" value="0" id="od_temp_point" class="frm_input" size="10"> 원 <span style="color:#f1f1f3;">|</span> <input type="checkbox" name="point_all_use" id="point_all_use" value="y"> 모두 사용</br>
								· 사용가능 적립금 : <span style="color:#fb5626;"><?php echo display_point($temp_point); ?></span>원 사용가능,30일 이내 소멸예정 : <span style="color:#fb5626;"><?php echo display_point($temp_point); ?></span>원

								<?
									$multi_settle++;
									}
								}
								?>
							</td>
						</tr>
						<tr>
							<th scope="row">결제수단 선택</th>
							<td ><input type="hidden" id="od_settle_bank" name="od_settle_case" value="무통장"> <label for="od_settle_bank">무통장입금</label>
							</td>
						</tr>
						
						<tr class="card_dis" style="display:none;">
							<th>카드종류</th>
							<td>
								<select name="buy_status">
									<option>선택하세요</option>
									<option value="1">신한</option>
									<option value="1">비씨</option>
								</select>
							</td>
						</tr>
						<tr class="card_dis" style="display:none;">
							<th>할부개월</th>
							<td>
								<select name="card_month">
									<option>일시불</option>
									<option>일시불</option>
								</select>
							</td>
						</tr>

						<!--tr class="mu_dis">
							<th>출금은행</th>
							<td>
								<select name="RTR_InBank">
									<option value="">선택하세요</option>
									<option value="제일">제일은행</option>
									<option value="국민">국민은행</option>
									<option value="우리">우리은행</option>
									<option value="외환">외환은행</option>
									<option value="신한">신한은행</option>
									<option value="농협">농협</option>
									<option value="축협">축협</option> 
									<option value="하나">하나은행</option>
									<option value="조흥">조흥은행</option>
									<option value="신협">신협</option>
									<option value="한미">한미은행</option>
									<option value="제주">제주은행</option>
									<option value="전북">전북은행</option>
									<option value="우체국">우체국</option>
									<option value="씨티">씨티은행</option>
									<option value="수협">수협</option>
									<option value="새마을">새마을금고</option>
									<option value="산업">산업은행</option>
									<option value="부산">부산은행</option>
									<option value="대구">대구은행</option>
									<option value="기업">기업은행</option>
									<option value="광주">광주은행</option>
									<option value="경남">경남은행</option>
									<option value="기타">기타</option>
								</select>
								<br><span style="color:#ef4e18;">고객님이 출금하실 은행</span>을 선택해주세요.
							</td>
						</tr-->

						<tr class="mu_dis">
							<th>입금은행</th>
							<td>
								<select name="od_bank_account">
									<option value="">선택하십시오.</option>
									<?
									$str = explode("\n", trim($default['de_bank_account']));
									for ($i=0; $i<count($str); $i++)
									{
										//$str[$i] = str_replace("\r", "", $str[$i]);
										$str[$i] = trim($str[$i]);
										echo '<option value="'.$str[$i].'">'.$str[$i].'</option>'.PHP_EOL;
									}
									?>
								</select>
								<span style="color:#ef4e18;">입금계좌 정보</span>는 주문완료 페이지에서 확인 가능합니다.
							</td>
						</tr>
						<tr class="mu_dis">
							<th>입금기한</th>
							<td>
								<?=$od_now_date?>까지
								<input type="hidden" name="od_last_date" id="od_last_date" value="<?=$od_now_date?>">
							</td>
						</tr>
						<tr class="mu_dis">
							<th>현금영수증</th>
							<td>
								<p>
								<input type="radio" name="od_tax" value="0">소득공제용&nbsp;&nbsp;
								<input type="radio" name="od_tax" value="1">지출증빙용&nbsp;&nbsp;
								<input type="radio" name="od_tax" value="" checked>미발행&nbsp;&nbsp;
								</p>
								<p class="tax_st" style="margin:5px 0 0 0;display:none;">
								
								</p>
							</td>
						</tr>

						<script type="text/javascript">
						$(document).ready(function(){
							$("input[name='od_tax']").click(function(){
								var od_tax = $(this).val();

								if(od_tax == '0'){
									$(".tax_st").css("display", "block");
									$(".tax_st").html('휴대폰번호 <input type="text" name="od_tax_hp" style="width:80%;">');
								}else if(od_tax == '1'){
									$(".tax_st").css("display", "block");
									$(".tax_st").html('사업자번호 <input type="text" name="od_tax_hp" style="width:80%;">');
								}else{
									$(".tax_st").css("display", "none");
								}
								
							});
						});
						</script>

						<tr>
							<th>환불은행</th>
							<td><input type="text" name="od_bank[0]" size="20"></td>
						</tr>

						<tr>
							<th>환불계좌</th>
							<td><input type="text" name="od_bank[1]" style="width:100%;"></td>
						</tr>

						<tr>
							<th>환불예금주</th>
							<td><input type="text" name="od_remit" size="20"></td>
						</tr>

						</tbody>
						</table>
					</td>
					<td style="padding:17px;border:0;background:#eeeff3;" valign="top">
						<table border="0" cellspacing="0" cellpadding="0" width="100%" class="buy_all_price_tb">
							<tr>
								<td colspan="2" style="font-size:17px;">결제 총 금액</td>
							</tr>
							<tr style="font-size:12px;">
								<td>총 주문금액</td>
								<td align="right"><span class="tot_sell_price"><?php echo number_format($totalPurchasePrice); ?></span><span> 원</span></td>
							</tr>
							<tr style="font-size:12px;">
								<td>적립금 사용</td>
								<td align="right"><span><?=number_format($tot_point)?></span><span> 원</span></td>
							</tr>
							<tr><td colspan="2"><div style="height:1px;background:#e1e1e2;"></div></td></tr>
							<tr style="font-size:15px;">
								<td>총 결제 금액</td>
								<td align="right" class="tot_price" style="font-size:17px;color:#fc3f00;">
									<span class="total_all_price">
										<?php echo number_format($totalPurchasePrice); ?>
									</span>
									<span> 원</span>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>


		<?php
		/*if (!$default['de_card_point'])
			echo '<p id="sod_frm_pt_alert"><strong>무통장입금</strong> 이외의 결제 수단으로 결제하시는 경우 포인트를 적립해드리지 않습니다.</p>';

		$multi_settle == 0;
		$checked = '';

		$escrow_title = "";
		if ($default['de_escrow_use']) {
			$escrow_title = "에스크로 ";
		}else{
			$escrow_title = "실시간 ";
		}

		if ($default['de_bank_use'] || $default['de_vbank_use'] || $default['de_bank_use'] || $default['de_bank_use'] || $default['de_bank_use']) {
		echo '<fieldset id="sod_frm_paysel">';
		echo '<legend>결제방법 선택</legend>';
		}

		// 무통장입금 사용
		if ($default['de_bank_use'] && $is_bank_payment) {
			$multi_settle++;
			echo '<input type="radio" id="od_settle_bank" name="od_settle_case" value="무통장" '.$checked.'> <label for="od_settle_bank">무통장입금</label>'.PHP_EOL;
			$checked = '';
		}

		// 가상계좌 사용
		if ($default['de_vbank_use'] && $is_cash_payment) {
			$multi_settle++;
			echo '<input type="radio" id="od_settle_vbank" name="od_settle_case" value="가상계좌" '.$checked.'> <label for="od_settle_vbank">'.$escrow_title.'가상계좌</label>'.PHP_EOL;
			$checked = '';
		}

		// 계좌이체 사용
		if ($default['de_iche_use'] && $is_cash_payment) {
			$multi_settle++;
			echo '<input type="radio" id="od_settle_iche" name="od_settle_case" value="계좌이체" '.$checked.'> <label for="od_settle_iche">'.$escrow_title.'계좌이체</label>'.PHP_EOL;
			$checked = '';
		}

		// 휴대폰 사용
		if ($default['de_hp_use'] && $is_cash_payment) {
			$multi_settle++;
			echo '<input type="radio" id="od_settle_hp" name="od_settle_case" value="휴대폰" '.$checked.'> <label for="od_settle_hp">휴대폰</label>'.PHP_EOL;
			$checked = '';
		}

		// 신용카드 사용
		if ($default['de_card_use'] && $is_cash_payment) {
			$multi_settle++;
			echo '<input type="radio" id="od_settle_card" name="od_settle_case" value="신용카드" '.$checked.'> <label for="od_settle_card">신용카드</label>'.PHP_EOL;
			$checked = '';
		}

		$temp_point = 0;
		// 회원이면서 포인트사용이면
		if ($is_member && $config['cf_use_point'])
		{
			// 포인트 결제 사용 포인트보다 회원의 포인트가 크다면
			if ($member['mb_point'] >= $default['de_settle_min_point'])
			{
				$temp_point = (int)$default['de_settle_max_point'];

				if($temp_point > (int)$tot_sell_price)
					$temp_point = (int)$tot_sell_price;

				if($temp_point > (int)$member['mb_point'])
					$temp_point = (int)$member['mb_point'];

				$point_unit = (int)$default['de_settle_point_unit'];
				$temp_point = (int)((int)($temp_point / $point_unit) * $point_unit);
		?>
			<p id="sod_frm_pt">보유포인트(<?php echo display_point($member['mb_point']); ?>)중 <strong id="use_max_point">최대 <?php echo display_point($temp_point); ?></strong>까지 사용 가능</p>
			<input type="hidden" name="max_temp_point" value="<?php echo $temp_point; ?>">
			<label for="od_temp_point">사용 포인트</label>
			<input type="text" name="od_temp_point" value="0" id="od_temp_point" class="frm_input" size="10">점 (<?php echo $point_unit; ?>점 단위로 입력하세요.)
		<?php
			$multi_settle++;
			}
		}

		if ($default['de_bank_use']) {
			// 은행계좌를 배열로 만든후
			$str = explode("\n", trim($default['de_bank_account']));
			if (count($str) <= 1)
			{
				$bank_account = '<input type="hidden" name="od_bank_account" value="'.$str[0].'">'.$str[0].PHP_EOL;
			}
			else
			{
				$bank_account = '<select name="od_bank_account" id="od_bank_account">'.PHP_EOL;
				$bank_account .= '<option value="">선택하십시오.</option>';
				for ($i=0; $i<count($str); $i++)
				{
					//$str[$i] = str_replace("\r", "", $str[$i]);
					$str[$i] = trim($str[$i]);
					$bank_account .= '<option value="'.$str[$i].'">'.$str[$i].'</option>'.PHP_EOL;
				}
				$bank_account .= '</select>'.PHP_EOL;
			}
			echo '<div id="settle_bank" style="display:none">';
			echo '<label for="od_bank_account" class="sound_only">입금할 계좌</label>';
			echo $bank_account;
			echo '<br><label for="od_deposit_name">입금자명</label>';
			echo '<input type="text" name="od_deposit_name" id="od_deposit_name" class="frm_input" size="10" maxlength="20">';
			echo '</div>';
		}

		if ($default['de_bank_use'] || $default['de_vbank_use'] || $default['de_bank_use'] || $default['de_bank_use'] || $default['de_bank_use']) {
		echo '</fieldset>';

		}

		if ($multi_settle == 0)
			echo '<p>결제할 방법이 없습니다.<br>운영자에게 알려주시면 감사하겠습니다.</p>';*/
		?>
	</section>
	<!-- } 결제 정보 입력 끝 -->


	<!-- 동의 시작 -->
	<div class="agree">

		<h2>결제대행서비스 표준이용약관</h2>
		<table border="0" cellspacing="0" cellpadding="0" width="800px" class="agree1_tb">
			<tr>
				<td class="tab_on agree2_bn" idx="0">기본약관</td>
				<td class="agree2_bn" idx="1">개인정보 수집, 이용</td>
				<td class="agree2_bn" idx="2">개인정보 제공, 위탁</td>
				<td class="right">&nbsp;</td>
			</tr>
			<tr class="agree2_dis" style="display:table-row;">
				<td style="padding:0;border:0;" colspan="4">
					<textarea readOnly><?=$config[cf_pay_aree3];?></textarea>
				</td>
			</tr>
			<tr class="agree2_dis">
				<td style="padding:0;border:0;" colspan="4">
					<textarea readOnly><?=$config[cf_pay_aree4];?></textarea>
				</td>
			</tr>
			<tr class="agree2_dis">
				<td style="padding:0;border:0;" colspan="4">
					<textarea readOnly><?=$config[cf_pay_aree5];?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="4" style="font-weight:bold;text-align:left;border:0;">
					<input type="checkbox" name="agr_2[]" id="agr_2" value="y"> 본인은 위의 내용을 모두 읽어보았으며 이에 전체 동의합니다.
				</td>
			</tr>
		</table>

	</div>

	<!-- 동의 끝 -->

	<div id="display_pay_button" class="btn_confirm">
		<img src="<?G5_URL?>/shop/img/cart_buy_all_bn.gif" onclick="forderform_check(document.forderform);" style="cursor:pointer;">
		<a href="<?php echo G5_SHOP_URL; ?>/list.php?ca_id=<?php echo $continue_ca_id; ?>"><img src="<?G5_URL?>/shop/img/cart_shop_bn.gif" align="absmiddle" style="border:0;" onclick="return form_check('buy');"></a>
	</div>
	</form>

</div>

<script>

var close_btn_idx;
var idx, status, old_cnt, cnt, pro_price, price1_1, price1, send_cost, cart_id, price3, cost, cost1, cost2, all_price;

$(function() {
	var $cp_btn_el;
	var $cp_row_el;
	var zipcode = "";

	$(".agree1_bn").click(function(){
		var idx = $(this).attr("idx");

		$(".agree1_dis").each(function(i){
			if(idx == i){
				$(".agree1_bn").eq(i).addClass("tab_on");
				$(".agree1_dis").eq(i).css("display", "table-row");
			}else{
				$(".agree1_bn").eq(i).removeClass("tab_on");
				$(".agree1_dis").eq(i).css("display", "none");
			}
		});
	});

	$(".agree2_bn").click(function(){
		var idx = $(this).attr("idx");

		$(".agree2_dis").each(function(i){
			if(idx == i){
				$(".agree2_bn").eq(i).addClass("tab_on");
				$(".agree2_dis").eq(i).css("display", "table-row");
			}else{
				$(".agree2_bn").eq(i).removeClass("tab_on");
				$(".agree2_dis").eq(i).css("display", "none");
			}
		});

	});

	$("input[name='od_settle_case']").click(function(){
		var pro_price_sub = 0;
		var card_status = false;

		price1_1 = 0;
		price1 = 0;
		price3 = 0;

		if($(this).val() == "신용카드"){
			card_status = true;
		}else{
			card_status = false;
		}
		
		$(".product1").find(".sell_price").each(function(i){
			pro_price_sub = 0;
			if(card_status == true){
				$(".product1").find(".pro_price").eq(i).html(commaNum($("input[name='pro_price_sub1[]']").eq(i).val()));
				pro_price_sub = parseInt(removeComma($(".product1").find(".ct_qty").eq(i).html())) * parseInt(removeComma($(".pro_price").eq(i).html()));
				$(".product1").find(".sell_price").eq(i).html(commaNum(pro_price_sub));
			}else{
				$(".product1").find(".pro_price").eq(i).html(commaNum($("input[name='pro_price_sub[]']").eq(i).val()));
				pro_price_sub = parseInt(removeComma($(".product1").find(".ct_qty").eq(i).html())) * parseInt(removeComma($(".pro_price").eq(i).html()));
				$(".product1").find(".sell_price").eq(i).html(commaNum(pro_price_sub));
			}
			price1_1 = removeComma($(".product1").find(".sell_price").eq(i).html());
			price1 = parseInt(price1_1) + parseInt(price1);
		});

		$(".price_info1").find(".price").eq(0).html(commaNum(price1));
		$(".all_price_info").find(".price_all").eq(0).html(commaNum(price1));

		price3 = removeComma($(".price_info1").find(".price").eq(2).html());
		price3 = parseInt(price3) + price1;

		if(price3){
			price3 = price3;
		}else{
			price3 = 0;
		}
		
		$(".price_info1").find(".price").eq(3).html(commaNum(price3));
		$(".tot_price").html(commaNum(price3) + " 원");
		$(".tot_sell_price").html(commaNum(price3));
		$("input[name='od_price']").val(price3);
		$("input[name='org_od_price']").val(price3);

	});


	$("#od_b_addr2").focus(function() {
		var zip1 = $("#od_b_zip1").val().replace(/[^0-9]/g, "");
		var zip2 = $("#od_b_zip2").val().replace(/[^0-9]/g, "");
		if(zip1 == "" || zip2 == "")
				return false;

		var code = String(zip1) + String(zip2);

		if(zipcode == code)
				return false;

		zipcode = code;
		calculate_sendcost(code);
	});

	$("#od_settle_bank").on("click", function() {
		$("[name=od_deposit_name]").val( $("[name=od_name]").val() );
		$("#settle_bank").show();
	});

	$("#od_settle_iche,#od_settle_card,#od_settle_vbank,#od_settle_hp").bind("click", function() {
		$("#settle_bank").hide();
	});

	// 배송지선택
	$("input[name=ad_sel_addr]").on("click", function() {
		var addr = $(this).val().split(String.fromCharCode(30));

		if (addr[0] == "same") {
				if($(this).is(":checked"))
					gumae2baesong(true);
				else
					gumae2baesong(false);
		} else {
				if(addr[0] == "new") {
					for(i=0; i<10; i++) {
						addr[i] = "";
					}
				}

				var f = document.forderform;
				f.od_b_name.value		= addr[0];
				f.od_b_tel.value			= addr[1];
				f.od_b_hp.value			= addr[2];
				f.od_b_zip1.value		= addr[3];
				f.od_b_zip2.value		= addr[4];
				f.od_b_addr1.value		= addr[5];
				f.od_b_addr2.value		= addr[6];
				f.od_b_addr3.value		= addr[7];
				f.od_b_addr_jibeon.value = addr[8];
				f.ad_subject.value		= addr[9];

				document.getElementById("od_b_addr_jibeon").innerText = "지번주소 : "+addr[8];

				var zip1 = addr[3].replace(/[^0-9]/g, "");
				var zip2 = addr[4].replace(/[^0-9]/g, "");

				if(zip1 != "" && zip2 != "") {
					var code = String(zip1) + String(zip2);

					if(zipcode != code) {
						zipcode = code;
						calculate_sendcost(code);
					}
				}
		}
	});

	// 배송지목록
	$("#order_address").on("click", function() {
		var url = this.href;
		window.open(url, "win_address", "left=100,top=100,width=800,height=600,scrollbars=1");
		return false;
	});

});


function calculate_sendcost(code)
{
	$.post(
		"./ordersendcost.php",
		{ zipcode: code },
		function(data) {
				$("input[name=od_send_cost2]").val(data);
				$("#od_send_cost2").text(number_format(String(data)));

				calculate_order_price();
		}
	);
}


function forderform_check(f)
{
	errmsg = "";
	errfld = "";
	var deffld = "";

	if($("input[name='od_tel']").val() == ""){
		alert("구매자 연락처를 입력하세요.");
		return false;
	}

	if($("input[name='od_b_tel']").val() == ""){
		alert("배송지 연락처를 입력하세요.");
		return false;
	}

	if($("input[name='od_b_zip1']").val() == ""){
		alert("배송지 주소를 선택하세요.");
		return false;
	}

	if($("input[name='od_b_zip2']").val() == ""){
		alert("배송지 주소를 선택하세요.");
		return false;
	}

	if($("input[name='od_b_addr1']").val() == ""){
		alert("배송지 주소를 선택하세요.");
		return false;
	}

	

	if($("select[name='od_bank_account']").val() == ""){
		alert("입금은행을 선택하세요.");
		return false;
	}

	if($("input[name^='agr_2']").eq(0).is(":checked") == false){
		alert("결제대행서비스 표준이용약관에 체크 하시기 바랍니다.");
		return false;
	}


	f.submit();
}

// 구매자 정보와 동일합니다.
function gumae2baesong(checked) {
	var f = document.forderform;

	if(checked == true) {
		f.od_b_name.value = f.od_name.value;
		f.od_b_tel.value  = f.od_tel.value;
		//f.od_b_hp.value	= f.od_hp.value;
		//f.od_b_zip1.value = f.od_zip1.value;
		//f.od_b_zip2.value = f.od_zip2.value;
		//f.od_b_addr1.value = f.od_addr1.value;
		//f.od_b_addr2.value = f.od_addr2.value;
		//f.od_b_addr3.value = f.od_addr3.value;
		//f.od_b_addr_jibeon.value = f.od_addr_jibeon.value;
		//document.getElementById("od_b_addr_jibeon").innerText = document.getElementById("od_addr_jibeon").innerText;

		calculate_sendcost(String(f.od_b_zip1.value) + String(f.od_b_zip2.value));
	} else {
		f.od_b_name.value = "";
		f.od_b_tel.value  = "";
		//f.od_b_hp.value	= "";
		//f.od_b_zip1.value = "";
		//f.od_b_zip2.value = "";
		//f.od_b_addr1.value = "";
		//f.od_b_addr2.value = "";
		//f.od_b_addr3.value = "";
		//f.od_b_addr_jibeon.value = "";
		//document.getElementById("od_b_addr_jibeon").innerText = "";
	}
}

<?php if ($default['de_hope_date_use']) { ?>
$(function(){
	$("#od_hope_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", minDate: "+<?php echo (int)$default['de_hope_date_after']; ?>d;", maxDate: "+<?php echo (int)$default['de_hope_date_after'] + 6; ?>d;" });
});
<?php } ?>
</script>

<?php
include_once('./_tail.php');
?>