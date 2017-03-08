<html>
<body onload='<? echo "window.print();"; ?>'>

<?php
include_once('./_common.php');
auth_check($auth[$sub_menu], "r");

$g5['title'] = '출력페이지';


$주문진행상태조건 = " AND A.ct_status IN ('결제완료','입금','상품준비중','배송중','배송완료') ";

//개별 주문정보 한건 로딩
$sql = "SELECT	A.*,
					M.mb_name,
					M.mb_nick
			FROM	g5_shop_cart A
					LEFT OUTER JOIN g5_member M ON (M.mb_id = A.mb_id)
			WHERE	1=1
			AND	A.ct_id = '$ct_id'
";
// echo $sql."<br>";
$result = sql_query($sql);
$cart = sql_fetch_array($result);

// echo $sql;

$공구코드 = $cart[total_amount_code];
$구매자ID = $cart[mb_id];

/* 모두출력 or 기존출력건 제외 조건*/
if($all_yn == 'N' && $type == 'GP') {
	$중복출력조건 = " AND	print_yn = 'N' ";
}

/* 해당공구 전체 출력*/
if($type == 'GP') {
	$공구내역인쇄조건 = " AND A.total_amount_code = '$공구코드' ";
	$업데이트조건 = " AND		total_amount_code = '$공구코드' ";
	
	$ordupd_sql = "	UPDATE	g5_shop_order	SET
												od_status = '상품준비중'
								WHERE		mb_id = '$구매자ID'	AND		od_status = '결제완료'
								AND		gp_code = '$공구코드'					
	";
} /*해당카트만 출력*/
else if($type == 'CART') {
	//where조건 추가
	$카트내역인쇄조건 = "AND	A.ct_id = '$ct_id' ";
	$업데이트조건 = " AND		ct_id = '$ct_id' ";
	
} /* 선택된 주문내역만 출력 */
else if($type == 'CHOICE') {
	$카트내역인쇄조건 = "AND	A.ct_id IN ($ct_id) ";
	$업데이트조건 = " AND		ct_id IN ($ct_id) ";
}



$upd_sql = "	UPDATE	g5_shop_cart	SET
												ct_status = '상품준비중',
												ct_history = CONCAT('상품준비중||',ct_history),
												print_yn = 'Y'
						WHERE		mb_id = '$구매자ID'	AND		ct_status = '결제완료'
									$업데이트조건
";


$sql = "	SELECT	A.*,
								C.ca_name
				FROM		g5_shop_cart A
								LEFT JOIN g5_shop_category C ON (C.ca_id = A.ct_type)
				WHERE		1=1
				AND		A.mb_id = '$구매자ID'
				$주문진행상태조건
				$중복출력조건
				$공구내역인쇄조건
				$카트내역인쇄조건
";//B.* LEFT OUTER JOIN g5_shop_group_purchase_option B ON (B.gp_id = A.it_id)


/*
echo $ordupd_sql;
echo "<br>";
echo $upd_sql;
echo "<br>";
echo $sql."<br>";
*/

$result = sql_query($sql);
//프린트출력여부 업데이트는 조회를 하고나서 실

@mysql_query($ordupd_sql);



sql_query($upd_sql);


?>




<style tyle="text/css">
<!--

@media screen { 
	body,th,td { font-size:1em }
	.orderInfo_td { background-color:white; padding:3px; width:90%; height:80px; }
} 
@media print { 
	body,th,td { font-size:0.5em }
	table { page : rotated; border:1px solid gray; background-color:#d1dee2; border-spacing:2px; }
	.ordertable_th { font-weight:bolder; background:#e5ecef; color:#383838; border:0px solid #d1dee2;}
	.ordertable_td { text-align:center; background-color:white; border:0px solid #d1dee2;}
	
	.orderInfo_td { background-color:white; padding:3px; width:90%; height:60px; } 
}

@page { size : landscape; }
@page rotated { size : landscape; }

table { page : rotated; border:1px solid gray; background-color:#d1dee2; border-spacing:1px; }

.orderBuyerinfo {float:left; margin-bottom:20px; width:20%; }
.orderBuyerinfo_th { text-align:left; background-color:#e5ecef; padding:3px; width:35% }
.orderBuyerinfo_td { text-align:left; background-color:white; padding:3px; width:65% }

.orderPriceinfo {float:left; margin-left:3%; width:20%; }
.orderPriceinfo_th { text-align:left; background-color:#e5ecef; padding:3px; width:35%; }
.orderPriceinfo_td { text-align:right; background-color:white; padding:3px; width:65%; }

.orderInfo{ float:left; margin-left:3%; width:50%; }
.orderInfo_th { text-align:center; background-color:#e5ecef; padding:2px; width:10%; }


.ordertable_th { font-weight:bolder; background:#e5ecef; color:#383838; border:0px solid #d1dee2;}
.ordertable_td { text-align:center; background-color:white; border:0px solid #d1dee2; padding:3px;}
.ordertable_td.image { padding:5px; }

-->
</style>


<table class='orderBuyerinfo'>
	<tr>
		<th class='orderBuyerinfo_th'>닉네임</th>
		<td class='orderBuyerinfo_td'><?=$cart[mb_nick]?></td>
	</tr>
	<tr>
		<th class='orderBuyerinfo_th'>성함</th>
		<td class='orderBuyerinfo_td'><?=$cart[mb_name]?></td>
	</tr>
	<tr>
		<th class='orderBuyerinfo_th'>공동구매코드</th>
		<td class='orderBuyerinfo_td'><?=$공구코드?></td>
	</tr>
</table>


<?
$total_sql = "	
			SELECT	A.mb_id,
							O.od_send_cost+ O.od_send_cost2 AS 'baesongbi',
							CASE
								WHEN	io_type != '' THEN
									SUM((A.ct_price+A.io_price) * (A.ct_qty-A.ct_gp_soldout)) 
								ELSE
									SUM((A.ct_price) * (A.ct_qty-A.ct_gp_soldout))
							END	total_price,
							O.od_shop_memo,
							O.od_status,
							O.od_settle_case,
							O.od_send_cost,
							O.od_send_cost2
			FROM		g5_shop_cart A
							LEFT OUTER JOIN g5_shop_order O ON (O.od_id = A.od_id)
			WHERE		1=1
			AND		A.mb_id = '$구매자ID'
			$주문진행상태조건
			$중복출력조건
			$공구내역인쇄조건
			$카트내역인쇄조건
			GROUP BY A.mb_id
"; //LEFT OUTER JOIN g5_shop_group_purchase_option B ON (B.gp_id = A.it_id)

// 소계
$realQty = $row['ct_qty'] - $row['ct_gp_soldout'];
$상품금액 = $opt_price * $realQty;
$ct_point['stotal'] = $row['ct_point'] * $realQty;


// echo $sql."<br>";
$total_result = sql_query($total_sql);
$total = sql_fetch_array($total_result);
$배송비 = $total[baesongbi];

$입금금액 = $total[total_price] + $total[baesongbi];
?>
<table class='orderPriceinfo'>
	<tr>
		<th class='orderPriceinfo_th'>총 상품금액 </th>
		<td class='orderPriceinfo_td' width='140px'><?=number_format($total[total_price])?></td>
	</tr>
	<tr>
		<th class='orderPriceinfo_th'>배송비</th>
		<td class='orderPriceinfo_td'><?=number_format($배송비)?></td>
	</tr>
	<tr>
		<th class='orderPriceinfo_th'>입금금액</th>
		<td class='orderPriceinfo_td'><?=number_format($입금금액)?></td>
	</tr>
</table>

<table class='orderinfo'>
	<tr>
		<th class='orderInfo_th'>주문<br />메모</th>
		<td class='orderInfo_td'>&nbsp;<?=$total[od_shop_memo]?></td>
	</tr>
</table>




<br><br>

<table width='100%' border=0 cellpadding=0 cellspacing=0>
	<thead>
	<tr>
		<th scope="col" class="ordertable_th">사진</th>
		<th scope="col" width='40%' class="ordertable_th">상품명</th>
		<th scope="col" class="ordertable_th">단가</th>
		<th scope="col" class="ordertable_th">환율</th>
		<th scope="col" class="ordertable_th">상품단가</th>
		<th scope="col" class="ordertable_th">신청수량</th>
		<th scope="col" class="ordertable_th">품절수량</th>
		<th scope="col" class="ordertable_th">주문수량</th>
		<th scope="col" class="ordertable_th">상품금액</th>
		<th scope="col" class="ordertable_th">미입고수량</th>
		<th scope="col" class="ordertable_th">검수여부</th>
		<th scope="col" class="ordertable_th">주문상태</th>
	</tr>
	</thead>
	<tbody>
<?
//$환율 = number_format($_SESSION[unit_kor_duty]);

$k = 0;
while($row = sql_fetch_array($result)) {
	if($row[ct_gubun]=="P"){
		$image = get_gp_image($row['it_id'], 60, 60,false);
		$name = get_gp_image1($row['it_id']);
	}else{
		$image = get_it_image($row['it_id'], 60, 60);
		$name = $row['it_name'];
	}
	
	$it_name = '<b>' . stripslashes($row['it_name']) . '</b>';
	$it_options = print_item_options($row['it_id'], $s_cart_id);
	if($it_options) {
		$it_name .= '<div class="sod_opt">'.$it_options.'</div>';
	}
	
	if($row['io_type'])
		$opt_price = $row['io_price'];
	else
		$opt_price = $row['ct_price'] + $row['io_price'];
	
	// 소계
	$realQty = $row['ct_qty'] - $row['ct_gp_soldout'];
	$상품금액 = $opt_price * $realQty;
	$ct_point['stotal'] = $row['ct_point'] * $realQty;
?>
	<tr>
		<td class="ordertable_td image"><?=$image?></td>
<? 	if($row[ct_gubun]=="P") { ?>
		<td class="ordertable_td" style='text-align:left;'><?="[$row[ca_name]] ".stripslashes($row['it_name'])?></td>
<? 	}
		else
		{	?>
		<td class="ordertable_td" style='text-align:left;'>[일반구매]
			<?=stripslashes($row['it_name'])?>
			<? if($od['od_tax_flag'] && $row['ct_notax']) echo '[비과세상품]'; ?>
		</td>
<?		}	?>
		<td class="ordertable_td">$<?=$row['ct_usd_price']?></td>
		<td class="ordertable_td"><?=number_format($row['cp_price'])?></td>
		<td class="ordertable_td"><?=number_format($row['ct_price'])?></td>
		<td class="ordertable_td"><?=number_format($row['ct_qty'])?></td>
		<td class="ordertable_td"><b><font color='red'><?=number_format($row['ct_gp_soldout'])?></font></b></td>
		<td class="ordertable_td"><?=number_format($realQty)?></td>
		<td class="ordertable_td"><?=number_format($상품금액)?></td>
		<td class="ordertable_td"><?=number_format($row['ct_wearing_cnt'])?></td>
		<td class="ordertable_td"><?=number_format($realQty-$row['ct_wearing_cnt'])?></td>
		<td class="ordertable_td"><?=$row[ct_status]?></td>
	</tr>
<?	
}
?>

		<!-- tr>
			<td title='사진'><?=$image?></td>
			<td title='품명'>
				<?=$it_name?>
			</td>
			<td title='단가$'><?=$현금단가?></td>
			<td title='환율'><?=$환율?></td>
			<td title='상품단가'><?=$상품단가?></td>
			<td title='품절수량'></td>
			<td title='주문수량'><?=$주문수량?></td>
			<td title='상품금액'><?=$상품금액?></td>
			<td title='미입고수량'></td>
			<td title='검수여부'></td>
		</tr-->

	</tbody>
</table>

</body>
</html>