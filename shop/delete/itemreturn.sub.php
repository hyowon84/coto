<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

if (!defined("_ORDERINQUIRY_")) exit; // 개별 페이지 접근 불가

//상태/상품/브랜드
if($my_sch_sel == "주문"){
	$search_que = " and od_status='".$my_sch_sel."' ";
}else if($my_sch_sel == "입금"){
	$search_que = " and od_status='".$my_sch_sel."' ";
}else if($my_sch_sel == "준비"){
	$search_que = " and od_status='".$my_sch_sel."' ";
}else if($my_sch_sel == "배송"){
	$search_que = " and od_status='".$my_sch_sel."' ";
}else if($my_sch_sel == "완료"){
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
	

	<div id="my_tab_nav">
		<ul>
			<li class="on">주문확인/배송조회</li>
			<li>취소/반품/교환</li>
			<li>상품리뷰</li>
			<li style="float:right;margin:5px 0 0 0;cursor:pointer;"><img src="<?=G5_URL?>/img/my_all_cost_bn.gif" border="0" align="absmiddle"></li>
		</ul>
	</div>

	<div id="my_sub_tab">
		<ul>
			<li onclick="goto_url('<?=G5_SHOP_URL?>/orderinquiry.php');" class="on">투데이 스토어 상품</li>
			<li onclick="goto_url('<?=G5_SHOP_URL?>/orderinquiry_gp.php');">공동구매 상품</li>
			<li onclick="goto_url('<?=G5_SHOP_URL?>/orderinquiry_pur.php');">구매대행 상품</li>
			<li>배송대행</li>
			<li class="right">그레이딩 대행</li>
		</ul>
	</div>

	<form name="fsearch" id="fsearch" method="GET">
	<div id="my_month_tab">
		<table border="0" cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td class="title">
					조회기간
				</td>
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
				<td class="title">
					상태/상품/브랜드
				</td>
				<td>
					<select name="my_sch_sel" class="my_sel">
						<option <?if($my_sch_sel == "") echo "selected";?>>전체</option>
						<option value="주문" <?if($my_sch_sel == "주문") echo "selected";?>>입금확인중</option>
						<option value="입금" <?if($my_sch_sel == "입금") echo "selected";?>>결제완료</option>
						<option value="준비" <?if($my_sch_sel == "준비") echo "selected";?>>배송준비중</option>
						<option value="배송" <?if($my_sch_sel == "배송") echo "selected";?>>배송중</option>
						<option value="완료" <?if($my_sch_sel == "완료") echo "selected";?>>배송완료</option>
					</select>
					<input type="text" name="my_sch_val" class="my_val" value="<?if($my_sch_val){echo $my_sch_val;}else{echo "상품명 혹은 브랜드 명으로 검색하세요.";}?>">
				</td>
			</tr>
			<tr height="20px">
				<td colspan="3" style="text-align:left;padding:2px 0 0 145px;font-size:12px;">
					구매 이력을 3개월 단위로 조회가 가능합니다.
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
				<td width="120px" class="title">입금확인중</td>
				<td width="120px" class="title">결제완료</td>
				<td width="45px"></td>
				<td width="130px" class="title">배송준비중</td>
				<td width="130px" class="title">배송중</td>
				<td class="title">배송완료</td>
			</tr>
			<tr>
				<td class="des"><?=od_status_cnt("주문")?></td>
				<td class="des"><?=od_status_cnt("입금")?></td>
				<td></td>
				<td class="des"><?=od_status_cnt("준비")?></td>
				<td class="des"><?=od_status_cnt("배송")?></td>
				<td class="des"><?=od_status_cnt("완료")?></td>
			</tr>
		</table>
	</div>

	<div class="tbl_head01 tbl_wrap" style="margin:10px 0 0 0;border-top:2px #545454 solid;">
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
				case '주문':
					$od_status = '입금확인중';
					break;
				case '입금':
					$od_status = '입금완료';
					break;
				case '준비':
					$od_status = '상품준비중';
					break;
				case '배송':
					$od_status = '상품배송';
					break;
				case '완료':
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

		?>

		<tr style="font-size:11px;">
			<td style="width:50px;text-align:center;">
				<?=item_type($row[it_id])?>
			</td>
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
				<div class="my_status_bn">배송조회</div>
				<div class="my_status_bn">교환요청</div>
				<div class="my_status_bn">반품요청</div>
			</td>
		</tr>

		<?php
		}

		if ($i == 0)
			echo '<tr><td colspan="7" class="empty_table">주문 내역이 없습니다.</td></tr>';
		?>
		</tbody>
		</table>
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