<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

if (!defined("_ORDERINQUIRY_")) exit; // 개별 페이지 접근 불가

//날짜
if($my_sch_dt_f){
	$search_que .= " and od_time > '".date("Y-m-d H:i:s", strtotime($my_sch_dt_f))."' ";
}
if($my_sch_dt_l){
	$search_que .= " and od_time < '".date("Y-m-d ", strtotime($my_sch_dt_l))."59:59:59' ";
}

//교환/반품 현황
if($cate1_1){
	$search_que .= " and (ex_status='".$cate1_1."' or re_status='".$cate1_1."') ";
}

//취소/반품/교환 select
if($my_sch_ex_sel){
	$search_que .= " and a.od_status='".$my_sch_ex_sel."' ";
}else{
	$search_que .= " and (a.od_status='배송완료' or a.od_status='교환' or a.od_status='반품') ";
}

$sql = " select * from {$g5['g5_shop_order_table']} as a
		 LEFT JOIN {$g5['g5_shop_cart_table']} as b
		 ON a.od_id=b.od_id
		 where a.mb_id = '{$member['mb_id']}'
		 and b.ct_type=''
		 $search_que
		 order by a.od_id desc
		 $limit ";

$result = sql_query($sql);

?>

<div id="aside2"></div>

<!-- 주문 내역 목록 시작 { -->
<?php if (!$limit) { ?>총 <?php echo $cnt; ?> 건<?php } ?>

<?include_once("../inc/mypage_menu.php");?>

<div style="background:#fff;">
	

	<?include_once("../inc/mypage_submenu.php");?>

	<div class="cl" style="margin:15px 0 0 20px;">
		<img src="<?=G5_URL?>/img/my_ex_sch_guide.gif">
	</div>

	<div class="my_sch_ex_tab">
		<ul>
			<li onclick="goto_url('<?=basename($PHP_SELF)?>?cate1=<?=$cate1?>&ct_type_status=<?=$ct_type_status?>&cate1_1=&my_sch_dt_f=<?=$my_sch_dt_f?>&my_sch_dt_l=<?=$my_sch_dt_l?>&date_diff=<?=$date_diff?>&my_sch_sel=<?=$my_sch_sel?>&my_sch_val=<?=$my_sch_val?>');" <?if($cate1_1 == ""){echo " class='on' ";}?>>취소/교환/반품접수</li>
			<li onclick="goto_url('<?=basename($PHP_SELF)?>?cate1=<?=$cate1?>&ct_type_status=<?=$ct_type_status?>&cate1_1=y&my_sch_dt_f=<?=$my_sch_dt_f?>&my_sch_dt_l=<?=$my_sch_dt_l?>&date_diff=<?=$date_diff?>&my_sch_sel=<?=$my_sch_sel?>&my_sch_val=<?=$my_sch_val?>');" class="right <?if($cate1_1 == "y"){echo " on ";}?>">교환/반품 현황조회</li>
		</ul>
	</div>

	<form name="fsearch" id="fsearch" method="GET">
	<input type="hidden" name="cate1" value="<?=$cate1?>">
	<input type="hidden" name="ct_type_status" value="<?=$ct_type_status?>">
	<input type="hidden" name="cate1_1" value="<?=$cate1_1?>">
	<input type="hidden" name="my_sch_dt_f" value="<?=$my_sch_dt_f?>">
	<input type="hidden" name="my_sch_dt_l" value="<?=$my_sch_dt_l?>">
	<input type="hidden" name="date_diff" value="<?=$date_diff?>">
	<input type="hidden" name="my_sch_sel" value="<?=$my_sch_sel?>">
	<input type="hidden" name="my_sch_val" value="<?=$my_sch_val?>">

	<div id="my_month_tab" style="height:30px;margin:5px 0 0 0;padding:5px 0 5px 0;border-top:1px #d9d9d9 solid;border-bottom:1px #d9d9d9 solid;">
		<table border="0" cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td class="title" style="width:80px;">
					조회기간
				</td>
				<td style="width:500px;">
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
				<td class="my_month_bn">
					<img src="<?=G5_URL?>/img/my_ex_sch_bn.gif" border="0" align="absmiddle" style="cursor:pointer;">
				</td>
				<td>
					<select name="my_sch_ex_sel" style="width:70px;height:24px;">
						<option value="">전체</option>
						<option value="취소" <?if($my_sch_ex_sel == "취소"){echo "selected";}?>>취소</option>
						<option value="반품" <?if($my_sch_ex_sel == "반품"){echo "selected";}?>>반품</option>
						<option value="교환" <?if($my_sch_ex_sel == "교환"){echo "selected";}?>>교환</option>
					</select>
				</td>
			</tr>
		</table>
	</div>
	</form>

	<div id="general_box">
		<table border="0" cellspacing="0" cellpadding="0" width="100%" height="80px">
			<tr>
				<td rowspan="2" width="92px">
					투데이</br>
					스토어</br>
					상품
				</td>
				<td width="120px" class="title">상품준비중</td>
				<td width="120px" class="title">결제완료</td>
				<td width="45px"></td>
				<td width="130px" class="title">상품준비중</td>
				<td width="130px" class="title">배송중</td>
				<td class="title">배송완료</td>
			</tr>
			<tr>
				<td class="des"><?=od_status_cnt_today("상품준비중")?></td>
				<td class="des"><?=od_status_cnt_today("결제완료")?></td>
				<td></td>
				<td class="des"><?=od_status_cnt_today("상품준비중")?></td>
				<td class="des"><?=od_status_cnt_today("배송중")?></td>
				<td class="des"><?=od_status_cnt_today("배송완료")?></td>
			</tr>
		</table>
	</div>

	<div class="tbl_head01 tbl_wrap" style="margin:10px 0 0 0;border-top:2px #545454 solid;">

	<form name="forderinquiry" id="forderinquiry" method="POST">
	<input type="hidden" name="cate1" value="<?=$cate1?>">

		<table>
		<thead>
		<tr>
			<th scope="col" style="background:#fff;">종류</th>
			<th scope="col" style="background:#fff;">주문일자/주문번호</th>
			<th scope="col" style="background:#fff;">상품정보</th>
			<th scope="col" class="my_td" style="background:#fff;">주문금액(수량)</th>
			<th scope="col" class="my_td" style="background:#faf9f9;">입금액</th>
			<th scope="col" class="my_td" style="background:#faf9f9;">미입금액</th>
			<th scope="col" style="background:#faf9f9;">진행상태</th>
		</tr>
		</thead>
		<tbody>
		<?php
		for ($i=0; $row=sql_fetch_array($result); $i++)
		{
			$uid = md5($row['od_id'].$row['od_time'].$row['od_ip']);

			switch($row['od_status']) {
				case '상품준비중':
					$od_status = '상품준비중';
					break;
				case '결제완료':
					$od_status = '결제완료';
					break;
				case '상품준비중':
					$od_status = '상품준비중';
					break;
				case '배송중':
					$od_status = '배송중';
					break;
				case '배송완료':
					$od_status = '배송완료';
					break;
				default:
					$od_status = '주문취소';
					break;
			}

			//$cart_res = sql_query("select * from {$g5['g5_shop_cart_table']} where od_id='".$row[od_id]."' ");
			$cart_row = sql_fetch("select * from {$g5['g5_shop_cart_table']} where od_id='".$row[od_id]."' order by ct_time desc limit 0, 1 ");
			//$cart_num = mysql_num_rows($cart_res);
			//$cart_num = $cart_num - 1;

			$image = get_it_image($row['it_id'], 70, 70);

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
				<?=item_deli_type($row[od_id], "today", $row[combine_deli_code], "T")?></br>
			</td>
			<?}?>

			<td style="width:100px;">
				<?php echo str_replace("-", ".", substr($row['od_time'],0,10)); ?></br>
				<input type="hidden" name="ct_id[<?php echo $i; ?>]" value="<?php echo $row['ct_id']; ?>">
				<a href="<?php echo G5_SHOP_URL; ?>/orderinquiryview.php?od_id=<?php echo $row['od_id']; ?>&amp;uid=<?php echo $uid; ?>" style="font-size:9px;font-weight:normal;text-decoration:underline;color:#56ccc8;"><?php echo $row['od_id']; ?></a>
			</td>
			<td>
				<table border="0" cellspacing="0" cellpadding="0" width="100%">
					<tr>
						<td style="border:0px;"><a href="<?=G5_SHOP_URL?>/item.php?it_id=<?=$cart_row[it_id]?>"><?=$image?></a></td>
						<td style="border:0px;"><a href="<?=G5_SHOP_URL?>/item.php?it_id=<?=$cart_row[it_id]?>"><?=$cart_row[it_name]?></a></td>
					</tr>
				</table>
			</td>
			<td class="td_numbig my_td">
				<?php echo display_price($row['od_cart_price'] + $row['od_send_cost'] + $row['od_send_cost2']); ?></br>
				(<?php echo $row['od_cart_count']; ?>개)
			</td>
			<td class="td_numbig my_td" style="background:#faf9f9;"><?php echo display_price($row['od_receipt_price']); ?></td>
			<td class="td_numbig my_td" style="background:#faf9f9;"><?php echo display_price($row['od_misu']); ?></td>
			<td style="background:#faf9f9;width:70px;text-align:center;font-size:9px;color:#3db2ff;">
				<div><?php echo $od_status; ?></div>

				<?if($cate1_1 == ""){?>
					<div class="my_status_bn">배송조회</div>
				<?}?>

				<?if($row[od_status] == "교환"){?>
					<div style="color:red;">교환완료</div>
				<?}else if($row[od_status] == "반품"){?>
					<div style="color:red;">반품완료</div>
				<?}else if($row[ex_status] == "y"){?>
					<div style="color:red;">교환요청중</div>
				<?}else if($row[re_status] == "y"){?>
					<div style="color:red;">반품요청중</div>
				<?}else{?>
					<div class="my_status_bn ex_bn" od_id="<?php echo $row['od_id']; ?>" idx="<?php echo $i;?>">교환요청</div>
					<div class="my_status_bn re_bn" od_id="<?php echo $row['od_id']; ?>" idx="<?php echo $i;?>">반품요청</div>
				<?}?>
			</td>
		</tr>

		<input type="hidden" name="ex_bn_input[<?php echo $i; ?>]" value="">
		<input type="hidden" name="re_bn_input[<?php echo $i; ?>]" value="">

		<?php
			$k--;
		}

		if ($i == 0)
			echo '<tr><td colspan="7" class="empty_table">주문 내역이 없습니다.</td></tr>';
		?>
		</tbody>
		</table>

	</form>

	</div>
	<!-- } 주문 내역 목록 끝 -->

	<div id="my_cost_info">
		<h2 style="color:#545454;font-size:15px;padding:0 0 0 10px;">주문배송안내</h2>
		<p style="margin:10px 0 0 0;">
			<img src="<?=G5_URL?>/img/my_cost_info.gif" border="0" align="absmiddle">
		</p>
	</div>
</div>

<link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/themes/base/jquery-ui.css" rel="stylesheet" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/jquery-ui.min.js"></script>
<script type="text/javascript">

$(document).ready(function(){
	$("input[name='my_sch_val']").focus(function(){
		$("input[name='my_sch_val']").val("");
	});

	$(".ex_bn").click(function(){
		var idx = $(this).attr("idx");
		var od_id = $(this).attr("od_id");
		$("input[name='ex_bn_input["+idx+"]']").val(od_id);
		$("form[name='forderinquiry']").attr("action", "./itemexchange.php").submit();
	});

	$(".re_bn").click(function(){
		var idx = $(this).attr("idx");
		var od_id = $(this).attr("od_id");
		$("input[name='ex_bn_input["+idx+"]']").val(od_id);
		$("form[name='forderinquiry']").attr("action", "./itemreturn.php").submit();
	});

	$("select[name='my_sch_ex_sel']").change(function(){
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

<script type="text/javascript">

$(document).ready(function(){
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

</script>