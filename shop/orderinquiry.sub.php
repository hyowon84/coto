<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가
if (!defined("_ORDERINQUIRY_")) exit; // 개별 페이지 접근 불가

//상태/상품/브랜드
if($my_sch_sel == "입금대기"){
	$search_que = " and od_status='".$my_sch_sel."' ";
}else if($my_sch_sel == "결제완료"){
	$search_que = " and od_status='".$my_sch_sel."' ";
}else if($my_sch_sel == "상품준비중"){
	$search_que = " and od_status='".$my_sch_sel."' ";
}else if($my_sch_sel == "배송중"){
	$search_que = " and od_status='".$my_sch_sel."' ";
}else if($my_sch_sel == "배송완료"){
	$search_que = " and od_status='".$my_sch_sel."' ";
}

//날짜
if($my_sch_dt_f){
	$search_que .= " and od_time > '".date("Y-m-d H:i:s", strtotime($my_sch_dt_f))."' ";
}
if($my_sch_dt_l){
	$search_que .= " and od_time < '".date("Y-m-d ", strtotime($my_sch_dt_l))."59:59:59' ";
}

//입력창 검색
if($my_sch_val != "" && $my_sch_val != "상품명 혹은 브랜드 명으로 검색하세요."){
	$search_que .= " and it_name like '%".$my_sch_val."%' ";
}

$sql = " select * from {$g5['g5_shop_order_table']}
		 where od_id in ( select distinct od_id from g5_shop_cart where ct_gubun = 'N' and ct_type='' )
		 and mb_id = '{$member['mb_id']}'
		 $search_que
		 order by
		 combine_deli_code desc
		 , od_id desc
		 $limit ";

$result = sql_query($sql);

?>

<div id="aside2"></div>

<!-- 주문 내역 목록 시작 { -->
<?php if (!$limit) { ?>총 <?php echo $cnt; ?> 건<?php } ?>

<?include_once("../inc/mypage_menu.php");?>

<div class="contentWrap" style="background:#fff;">
	

	<?include_once("../inc/mypage_submenu.php");?>

	<form name="fsearch" id="fsearch" method="GET">
	<div id="my_month_tab">
		<table width="100%">
			<tr>
				<td class="title">조회기간</td>
				<td>
					<div class="date_diff">
						<div <?if($date_diff == "0"){echo "class='on'";}?>>1주일</div>
						<div <?if($date_diff == "1"){echo "class='on'";}?>>1개월</div>
						<div <?if($date_diff == "2"){echo "class='on'";}?>>3개월</div>
						<div class="right <?if($date_diff == ""){echo "on";}?>">기간</div>
					</div>
					<div class="date_diff1">
						<div><input type="text" name="my_sch_dt_f" id="my_sch_dt_f" class="my_sch_dt" readOnly value="<?=$my_sch_dt_f?>"></div>
						<div style="margin:3px 2px 0 2px;"> ~ </div>
						<div><input type="text" name="my_sch_dt_l" id="my_sch_dt_l" class="my_sch_dt" readOnly value="<?=$my_sch_dt_l?>"></div>
					</div>
					<input type="hidden" name="date_diff" value="<?=$date_diff?>">
				</td>
				<td rowspan="2" class="my_month_bn">
					<img src="<?=G5_URL?>/img/my_sch_bn.gif" border="0" align="absmiddle" style="cursor:pointer;">
				</td>
			</tr>
			<tr>
				<td class="title">상태/상품/브랜드</td>
				<td class="searchContainer">
					<select name="my_sch_sel" class="my_sel">
						<option <?if($my_sch_sel == "") echo "selected";?>>전체</option>
						<option value="입금대기" <?if($my_sch_sel == "입금대기") echo "selected";?>>입금대기</option>
						<option value="결제완료" <?if($my_sch_sel == "결제완료") echo "selected";?>>결제완료</option>
						<option value="상품준비중" <?if($my_sch_sel == "상품준비중") echo "selected";?>>상품준비중</option>
						<option value="배송대기" <?if($my_sch_sel == "배송대기") echo "selected";?>>배송대기</option>
						<option value="배송중" <?if($my_sch_sel == "배송중") echo "selected";?>>배송중</option>
						<option value="배송완료" <?if($my_sch_sel == "배송완료") echo "selected";?>>배송완료</option>
					</select>
					<input type="text" name="my_sch_val" class="my_val" value="<?if($my_sch_val){echo $my_sch_val;}?>" placeholder="상품명 혹은 브랜드 명 검색">
				</td>
			</tr>
			<tr class="descContainer" height="20px">
				<td class="desc" colspan="3" style="text-align:left;padding:2px 0 0 145px;font-size:12px;">
					구매 이력을 3개월 단위로 조회가 가능합니다.
				</td>
			</tr>
		</table>
	</div>
	</form>

	<div id="general_box">
		<table border="0" cellspacing="0" cellpadding="0" width="100%" height="80px">
			<tr>
				<td class="storeTitle" rowspan="2" width="92px">
					공동구매</br>
					상품
				</td>
				<td width="120px" class="title">입금대기</td>
				<td width="120px" class="title">결제완료</td>
				<td width="45px" class="partition"></td>
				<td width="130px" class="title">상품준비중</td>
				<td width="130px" class="title">배송중</td>
				<td class="title">배송완료</td>
			</tr>
			<tr>
				<td class="des"><?=od_status_cnt("입금대기")?></td>
				<td class="des"><?=od_status_cnt("결제완료")?></td>
				<td></td>
				<td class="des"><?=od_status_cnt("상품준비중")?></td>
				<td class="des"><?=od_status_cnt("배송중")?></td>
				<td class="des"><?=od_status_cnt("배송완료")?></td>
			</tr>
		</table>
	</div>

	<div class="tbl_head01 tbl_wrap" id="orderListContainer" style="margin:10px 0 0 0;border-top:2px #545454 solid;">
	<?if(G5_IS_MOBILE) {?>

	<?} else {?>
		<form name="forderinquiry" id="forderinquiry" method="POST">
			<table>
				<thead>
				<tr>
					<th scope="col" class="my_td" style="background:#fff;width:85px;">종류</th>
					<th scope="col" style="background:#fff;">주문일자/주문번호</th>
					<th scope="col" style="background:#fff;">상품정보</th>
					<th scope="col" class="my_td" style="background:#fff;width:80px;">주문금액(수량)</th>
					<th scope="col" class="my_td" style="background:#faf9f9;width:60px;">입금액</th>
					<th scope="col" class="my_td" style="background:#faf9f9;width:80px;">미입금액</th>
					<th scope="col" class="my_td" style="background:#faf9f9;width:60px;">진행상태</th>
					<th scope="col" style="background:#faf9f9;width:60px;">배송상태</th>
				</tr>
				</thead>
				<tbody>
				<?php
				$k = 0;
				for ($i=0; $row=sql_fetch_array($result); $i++) {
					$uid = md5($row['od_id'].$row['od_time'].$row['od_ip']);

					switch($row['od_status']) {
						case '입금대기':	$od_status = '입금대기';	break;
						case '결제완료':	$od_status = '결제완료';	break;
						case '상품준비중':	$od_status = '상품준비중';	$od_status1 = '상품준비중';		break;
						case '배송대기':	$od_status = '배송대기';	$od_status1 = '배송대기';		break;
						case '배송중':		$od_status = '배송중';		$od_status1 = '배송중';			break;
						case '배송완료':	$od_status = '배송완료';	$od_status1 = '배송완료';		break;
						default:			$od_status = '주문취소';	$od_status1 = '주문취소';		break;
					}

					$cart_row = sql_fetch("select * from {$g5['g5_shop_cart_table']} where od_id='".$row[od_id]."' order by ct_time desc limit 0, 1 ");
					$image = get_it_image($cart_row['it_id'], 70, 70);

					if($k < 0){
						$k = 0;
					}

					if($k == 0){
						$rowspan = sql_fetch("select count(*) as cnt from {$g5['g5_shop_order_table']} where combine_deli_code != '' and combine_deli_code='".$row[combine_deli_code]."' and combine_deli_status='y' ");
						$k = $rowspan[cnt];
					}
					?>

					<tr style="font-size:11px;">
						<?if($rowspan[cnt] == $k){?>
							<td class="my_td" style="width:50px;text-align:center;" rowspan="<?=$rowspan[cnt]?>">
								<?=item_deli_type($row[od_id], "today", $row[combine_deli_code], "T")?>
							</td>
							<?
						}
						?>

						<td style="width:105px;">
							<?php echo str_replace("-", ".", substr($row['od_time'],0,10)); ?></br>
							<input type="hidden" name="ct_id[<?php echo $i; ?>]" value="<?php echo $row['ct_id']; ?>">
							<a href="<?php echo G5_SHOP_URL; ?>/orderinquiryview.php?od_id=<?php echo $row['od_id']; ?>&amp;uid=<?php echo $uid; ?>" style="font-size:9px;font-weight:normal;text-decoration:underline;color:#56ccc8;"><?php echo $row['od_id']; ?></a></br>
							<img src="<?=G5_URL?>/img/my_view_bn.gif" class="layer_trigger_my" T_od_id="<?=$row[od_id]?>" status="N" dealer_status="today">
						</td>
						<td>
							<table border="0" cellspacing="0" cellpadding="0" width="100%">
								<tr>
									<td style="border:0px;"><a href="<?=G5_SHOP_URL?>/item.php?it_id=<?=$cart_row[it_id]?>"><?=$image?></a></td>
									<td style="border:0px;">
										<a href="<?=G5_SHOP_URL?>/item.php?it_id=<?=$cart_row[it_id]?>"><?=$cart_row[it_name]?></a>
										<?
										if($row['od_cart_count'] > 1){
											$od_cart_count = $row['od_cart_count'] - 1;
											echo "외 ".$od_cart_count."건";
										}
										?>
									</td>
								</tr>
							</table>
						</td>
						<td class="my_td" style="text-align:center;">
							<?php echo display_price($row['od_cart_price'] + $row['od_send_cost'] + $row['od_send_cost2']); ?></br>
							(<?php echo $row['od_cart_count']; ?>개)
						</td>
						<td class="my_td" style="background:#faf9f9;text-align:center;"><?php echo display_price($row['od_receipt_price']); ?></td>
						<td class="my_td" style="background:#faf9f9;text-align:center;"><?php echo display_price($row['od_misu']); ?></td>
						<td class="my_td" style="background:#faf9f9;text-align:center;">
							<div style="color:#3db2ff;"><?php echo $od_status; ?></div>
						</td>

						<?if($rowspan[cnt] == $k){?>

							<td style="background:#faf9f9;width:70px;text-align:center;font-size:9px;color:#3db2ff;" rowspan="<?=$rowspan[cnt]?>">
								<div style="color:#3db2ff;"><?php echo $od_status1; ?></div>

								<?if($row[od_status] == "상품준비중" || $row[od_status] == "해외배송중" || $row[od_status] == "배송중" || $row[od_status] == "배송완료"){?>

									<?if($row[combine_deli_status] == "y"){	//통합배송일떄?>
										<div class="my_deli_info_bn" T_od_id="<?=$row[combine_deli_code]?>" dt="<?=$row[combine_deli_date]?>" status="today">배송조회</div>
									<?}else{								//일반배송일떄?>
										<div class="my_deli_info_bn1" od_id="<?=$row[od_id]?>" status="today">배송조회</div>
									<?}?>

								<?}?>

								<!--<div class="my_status_bn">반품요청</div>-->
							</td>

						<?}?>

					</tr>

					<input type="hidden" name="ex_bn_input[<?php echo $i; ?>]" value="">

					<?php
					$k--;
				}
				if ($i == 0)
					echo '<tr><td colspan="8" class="empty_table">주문 내역이 없습니다.</td></tr>';
				?>
				</tbody>
			</table>
		</form>
	<?}?>


	</div>
	<!-- } 주문 내역 목록 끝 -->

	<div id="my_cost_info">
		<h2 class="title" style="color:#545454;font-size:15px;padding:0 0 0 10px;">주문배송안내</h2>
		<p class="desc" style="margin:10px 0 0 0;padding:0 0 0 10px;">
			<?if(G5_IS_MOBILE) {?>
				<i class="fa fa-info-circle"></i>
			<?} else {?>
				<img src="<?=G5_URL?>/img/mem_conf_ico.gif">
			<?}?>
			 통합배송의 경우 배송지연이 발생 할 수 있는 점 양해 바랍니다.
		</p>
		<p style="margin:10px 0 0 0;">
		<?if(G5_IS_MOBILE) {?>
		<div class="orderInfoWrap">
			<div class="orderInfo grey">
				<span class="stepContainer">
					<div class="step">STEP 1.</div>
					<div class="title">주문완료</div>
				</span>
				<span class="desc">
					주문이 완료된 단계입니다.
				</span>
			</div>
			<div class="orderInfo white">
				<span class="stepContainer">
					<div class="step">STEP 2.</div>
					<div class="title">입금대기</div>
				</span>
				<span class="desc">
					입금이 아직 이뤄지지 않은 단계입니다.<br>3일 이내 미입금시 주문이 취소가 될 수 있습니다.
				</span>
			</div>
			<div class="orderInfo grey">
				<span class="stepContainer">
					<div class="step">STEP 3.</div>
					<div class="title">결제완료</div>
				</span>
				<span class="desc">
					결제가 완료된 단계입니다.
				</span>
			</div>
			<div class="orderInfo white">
				<span class="stepContainer">
					<div class="step">STEP 4.</div>
					<div class="title">배송준비중</div>
				</span>
				<span class="desc">
					주문 상품을 확인하고 상품을 배송준비 상태입니다.
				</span>
			</div>
			<div class="orderInfo grey">
				<span class="stepContainer">
					<div class="step">STEP 5.</div>
					<div class="title">배송중</div>
				</span>
				<span class="desc">
					택배사로 상품이 전달되어 배송이 시작되었습니다.<br>보통의 경우 4일내에 고객에게 도착합니다.
				</span>
			</div>
			<div class="orderInfo white">
				<span class="stepContainer">
					<div class="step">STEP 6.</div>
					<div class="title">배송완료</div>
				</span>
				<span class="desc">
					고객님께서 상품을<br>
					안전하게 받으셔서 배송이 완료된 상태입니다.
				</span>
			</div>
		</div>
		<?} else {?>
			<img class="costInfoImg" src="<?=G5_URL?>/img/my_cost_info.gif" border="0" align="absmiddle">
		<?}?>
		</p>
	</div>
</div>

<link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/themes/base/jquery-ui.css" rel="stylesheet" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/jquery-ui.min.js"></script>
<script type="text/javascript">

$(function() {
	$("input[name='my_sch_val']").focus(function(){
		$("input[name='my_sch_val']").val("");
	});

	$(".ex_bn").click(function(){
		var idx = $(this).attr("idx");
		var od_id = $(this).attr("od_id");
		$("input[name='ex_bn_input["+idx+"]']").val(od_id);
		$("form[name='forderinquiry']").attr("action", "./itemexchange.php").submit();
	});

	$(".date_diff").find("div").click(function(){
		var idx = $(".date_diff").find("div").index($(this));
		var settingDate = new Date(<?=date("Y")?>, <?=date("m")?>, <?=date("d")?>);
		$(".date_diff").find("div").each(function(i){
			if(i == idx){
				$(".date_diff").find("div").eq(i).addClass("on");
				if(idx == 0){
					$("input[name='my_sch_dt_f']").val("<?php echo date("Ymd", strtotime("-7 day"));?>");
					$("input[name='my_sch_dt_l']").val("<?php echo date("Ymd");?>");
					$("input[name='date_diff']").val("0");
				}else if(idx == 1){
					$("input[name='my_sch_dt_f']").val("<?php echo date("Ymd", strtotime("-1 month"));?>");
					$("input[name='my_sch_dt_l']").val("<?php echo date("Ymd");?>");
					$("input[name='date_diff']").val("1");
				}else if(idx == 2){
					$("input[name='my_sch_dt_f']").val("<?php echo date("Ymd", strtotime("-3 month"));?>");
					$("input[name='my_sch_dt_l']").val("<?php echo date("Ymd");?>");
					$("input[name='date_diff']").val("2");
				}else{
					$("input[name='my_sch_dt_f']").val("");
					$("input[name='my_sch_dt_l']").val("");
					$("input[name='date_diff']").val("");
				}
			}else{
				$(".date_diff").find("div").eq(i).removeClass("on");
			}
		});
	});

	$(".my_month_bn").click(function(){
		$("form[name='fsearch']").submit();
	});
});

jQuery(function($){
	$.datepicker.regional['ko'] = {
		closeText: '닫기',
		prevText: '이전달',
		nextText: '다음달',
		currentText: '오늘',
		monthNames: ['1월(JAN)','2월(FEB)','3월(MAR)','4월(APR)','5월(MAY)','6월(JUN)',
		'7월(JUL)','8월(AUG)','9월(SEP)','10월(OCT)','11월(NOV)','12월(DEC)'],
		monthNamesShort: ['1월','2월','3월','4월','5월','6월',
		'7월','8월','9월','10월','11월','12월'],
		dayNames: ['일','월','화','수','목','금','토'],
		dayNamesShort: ['일','월','화','수','목','금','토'],
		dayNamesMin: ['일','월','화','수','목','금','토'],
		weekHeader: 'Wk',
		dateFormat: 'yymmdd',
		firstDay: 0,
		isRTL: false,
		showMonthAfterYear: true,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['ko']);

    $('#my_sch_dt_f').datepicker({
        showOn: 'button',
		buttonImage: '../img/my_sch_dt_ico.gif',
		buttonImageOnly: true,
        buttonText: "달력",
        changeMonth: true,
		changeYear: true,
        showButtonPanel: true,
        yearRange: 'c-99:c+99',
        maxDate: '+0d'
    }); 

	$('#my_sch_dt_l').datepicker({
        showOn: 'button',
		buttonImage: '../img/my_sch_dt_ico.gif',
		buttonImageOnly: true,
        buttonText: "달력",
        changeMonth: true,
		changeYear: true,
        showButtonPanel: true,
        yearRange: 'c-99:c+99',
        maxDate: '+0d'
    }); 
});
</script>