<?php
$sub_menu = '400500';
$sub_sub_menu = "5";

include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$fr_date = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3", $fr_date);
$to_date = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3", $to_date);

$g5['title'] = $fr_date.' ~ '.$to_date.' 일간 상품별 매출현황';
include_once (G5_ADMIN_PATH.'/admin.head.php');
include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');

if($fr_date){
	$fr_date = $fr_date;
}

if($to_date){
	$to_date = $to_date;
}

if($fr_date){
	$que = " and SUBSTRING(od_time,1,10) between '$fr_date' and '$to_date' ";
}

$sql = " select * from {$g5['g5_shop_cart_table']}
		where 1
		and od_id in (select DISTINCT od_id from {$g5['g5_shop_order_table']} where 1 $que )
		and ct_gubun='N'
		group by it_name ";

$result = sql_query($sql);
?>

<form name="fseach" id="fseach">
<div class="local_sch02 local_sch">
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
</div>
</form>

<div class="tbl_head01 tbl_wrap">

    <table>
    <caption><?php echo $g5['title']; ?></caption>
    <thead>
    <tr>
        <th scope="col">상품명</th>
        <th scope="col">주문건수</th>
        <th scope="col">판매건수</th>
		<th scope="col">판매물품갯수</th>
        <th scope="col">입금합계</th>
        <th scope="col">카드입금</th>
        <th scope="col">포인트입금</th>
        <th scope="col">무통장입금</th>
        <th scope="col">주문취소</th>
		<th scope="col">반품</th>
		<th scope="col">교환</th>
        <th scope="col">미수금</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++)
    {
		$order_cnt = order_cnt_fn($row[it_name]);			//주문건수
		$buy_cnt = buy_cnt_fn($row[it_name]);				//판매건수
		$item_cnt = item_cnt_fn($row[it_name]);				//판매물품갯수
		$buy_sum = buy_sum_fn($row[it_name]);				//입금합계
		$buy_card_sum = buy_card_sum_fn($row[it_name]);		//카드입금
		$buy_point_sum = buy_point_sum_fn($row[it_name]);	//포인트입금
		$buy_mu_sum = buy_mu_sum_fn($row[it_name]);			//무통장입금
		$buy_cancel_sum = buy_cancel_sum_fn($row[it_name]);	//취소
		$buy_re_sum = buy_re_sum_fn($row[it_name]);			//반품
		$buy_ex_sum = buy_ex_sum_fn($row[it_name]);			//교환
		$buy_misu_sum = buy_misu_sum_fn($row[it_name]);		//미수금
		
    ?>

	<tr style="text-align:center;">
		<td scope="col" style="text-align:left;"><a href="./today_item_sales_view.php?it_id=<?php echo $row[it_id]; ?>"><?=$row[it_name]?></a></td>
        <td scope="col"><?=$order_cnt?></td>
        <td scope="col"><?=$buy_cnt?></td>
		<td scope="col"><?=$item_cnt?></td>
        <td scope="col"><?=number_format($buy_sum)?></td>
        <td scope="col"><?=number_format($buy_card_sum)?></td>
        <td scope="col"><?=number_format($buy_point_sum)?></td>
        <td scope="col"><?=number_format($buy_mu_sum)?></td>
        <td scope="col"><?=number_format($buy_cancel_sum)?></td>
		<td scope="col"><?=number_format($buy_re_sum)?></td>
		<td scope="col"><?=number_format($buy_ex_sum)?></td>
        <td scope="col"><?=number_format($buy_misu_sum)?></td>
	</tr>
	<?

		$order_cnt_all += $order_cnt;			//주문건수
		$buy_cnt_all += $buy_cnt;				//판매건수
		$item_cnt_all += $item_cnt;				//판매물품갯수
		$buy_sum_all += $buy_sum;				//입금합계
		$buy_card_sum_all += $buy_card_sum;		//카드입금
		$buy_point_sum_all += $buy_point_sum;	//포인트입금
		$buy_mu_sum_all += $buy_mu_sum;			//무통장입금
		$buy_cancel_sum_all += $buy_cancel_sum;	//취소
		$buy_re_sum_all += $buy_re_sum;			//반품
		$buy_ex_sum_all += $buy_ex_sum;			//교환
		$buy_misu_sum_all += $buy_misu_sum;		//미수금
    }

    if ($i == 0) {
        echo '<tr><td colspan="11" class="empty_table">자료가 없습니다.</td></tr>';
    }
    ?>
    </tbody>
    <tfoot>
    <tr>
        <td>합계</td>
        <td><?php echo number_format($order_cnt_all); ?></td>
		<td><?php echo number_format($buy_cnt_all); ?></td>
		<td><?php echo number_format($item_cnt_all); ?></td>
		<td><?php echo number_format($buy_sum_all); ?></td>
        <td><?php echo number_format($buy_card_sum_all); ?></td>
        <td><?php echo number_format($buy_point_sum_all); ?></td>
        <td><?php echo number_format($buy_mu_sum_all); ?></td>
        <td><?php echo number_format($buy_cancel_sum_all); ?></td>
        <td><?php echo number_format($buy_re_sum_all); ?></td>
        <td><?php echo number_format($buy_ex_sum_all); ?></td>
        <td><?php echo number_format($buy_misu_sum_all); ?></td>
    </tr>
    </tfoot>
    </table>
</div>

<script type="text/javascript">

$(document).ready(function(){
	$("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });
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

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
