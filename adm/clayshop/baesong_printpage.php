<html>
<body onload='start()'>

<script>
function start() {
// 	window.print();
// 	window.opener.location.reload();
}
</script>

<style tyle="text/css">
p.divPage { pageBreakBefore:always; page-break-after: always }

@media screen {
	body,th,td { font-size:1em }
	.orderInfo_td { background-color:white; padding:3px; width:90%; height:80px; }
}

/* 프린트 스타일 */
@media print {
	body,th,td { font-size:0.5em }
	table { page : rotated; border:1px solid gray; background-color:#d1dee2; border-spacing:2px; }
	.ordertable_th { font-weight:bolder; background:#e5ecef; color:#383838; border:0px solid #d1dee2;}
	.ordertable_td { text-align:center; background-color:white; border:0px solid #d1dee2;}

	.orderInfo_td { width:55%; background-color:white; padding:3px; height:60px; }
	.orderInfo_th { width:45%; }

	.orderinfo_odid { font-size:2.5em; font-weight:bolder; }
	.orderinfo_clayid { font-size:2em; font-weight:bolder; }
}

@page { size : landscape; }
@page rotated { size : landscape; }

table { page : rotated; border:1px solid gray; background-color:#d1dee2; border-spacing:1px; }

.orderBuyerinfo {float:left; margin-bottom:20px; width:20%; }
.orderBuyerinfo_th { text-align:left; background-color:#e5ecef; padding:3px; width:35% }
.orderBuyerinfo_td { text-align:left; background-color:white; padding:3px; width:65% }

.orderPriceinfo {float:left; margin-left:3%; width:20%; }
.orderPriceinfo_th { text-align:left; background-color:#e5ecef; padding:3px; width:35%; }
.orderPriceinfo_td { text-align:right; background-color:white; padding:3px; width:65%; padding-right:15px; }

.orderInfo{ float:left; margin-left:3%; width:54%; }
.orderInfo_th { width:auto; text-align:center; background-color:#e5ecef; padding:2px; }

.ordertable_th { font-weight:bolder; background:#e5ecef; color:#383838; border:0px solid #d1dee2;}
.ordertable_td { text-align:center; background-color:white; border:0px solid #d1dee2; padding:3px;}
.ordertable_td.image { padding:5px; }

</style>


<?php
include_once('./_common.php');
auth_check($auth[$sub_menu], "r");

$g5['title'] = '출력페이지';


if($_GET[stats]) {
	$주문진행상태조건 = " AND CL.stats = '$stats' ";
} else {
	$주문진행상태조건 = " AND CL.stats IN (20,25,30,35) ";
}


/* 그룹인쇄 */
if($_GET[mode] == 'group' || $_GET[mode] == 'each') {
// 	$주문번호 = substr($주문번호,0,strlen($주문번호)-1);
// 	$첫번째PK = "AND		CL.od_id = '".$_POST['gr_chk'][0]."'";
// 	$PK목록 = "AND		CL.od_id IN ($주문번호) ";


	$cnt = 0;

	switch($_GET[mode]) {
		case 'group':
			$source = $_POST['gr_chk'];
			break;
		case 'each':
			$source = $_POST['chk'];
			/*마지막 행을 버릴 생각으로 */
			break;
		default:
			break;
	}

	for($i = 0; $i < count($source); $i++) {
		$key = $source[$i];
		$next_key = $od_id[$source[$i+1]];

		$cnt++;

		// else { //if($mode == 'detail') {
		// 	for($i=0; $i < count($_POST['chk']); $i++) {
		// 		$number .= "'".$_POST['chk'][$i]."',";
		// 	}
		// 	echo $number;

		// 	$number = substr($number,0,strlen($number)-1);

		// 	$첫번째PK = "AND		CL.number = '".$_POST['chk'][0]."'";
		// 	$PK목록 = "AND		CL.number IN ($number) ";
		// }

		if($_GET[mode] == 'group') {	//주문번호별 출력
			$총선택개수 = count($_POST['gr_chk']);
			$PK목록 = "	CL.od_id = '$key'	";
			$주문번호연결 = $PK목록;

			/*로그*/
			$ins_sql = "INSERT INTO 		log_table	SET
															logtype = 'clayorder',		/*로그유형 ( ex: clayorder )*/
															gr_id	= '$key',				/*pk_id*/
															pk_id	= '',							/*pk_id*/
															it_id		= '',
															memo = '주문서출력',		/*메모태그*/
															admin_id = '$member[mb_id]',		/*로그를 남긴 관리자ID*/
															col = 'print_yn',					/*변경 항목*/
															value = 'Y',							/*변경된 값*/
															reg_date = now()				/*변경된 날짜*/
			";
			sql_query($ins_sql);
		}
		else {		// 개별출력
			$총선택개수 = count($_POST['chk']);
			//echo "개별출력조건1 :  $cnt == 1 || ( $next_key != ".$od_id[$key]." && $cnt != 1 && $next_key != '') <br>";

			//첫번째 or  두번째부터 (현재부모키 != 이전부모키) && 현재부모키
			if($cnt == 1 || ($prev_key != $od_id[$key] && $cnt != 1 ) ) {
// 				echo "1-1<br>";
				$number_list = "'$key'";
			} else {
// 				echo "1-2<br>";
				/* 이전 OD_ID와 현재 OD_ID 일치할경우에만 IN */
				if($prev_key == $od_id[$key] || $cnt == count($source) ) {
					$number_list .= ",'$key'";
				}
			}

			//쿼리실행시점
			//1. 현재행과 전체행개수 일치 || (이전부모키 != 현재부모키 && 현재행이 두번째부터)
// 			echo " $cnt == (".count($source).") || ($next_key != ".$od_id[$key].") <br>";
			if($cnt == (count($source)) || ($next_key != $od_id[$key]) ) {
				$PK목록 = "	CL.number	IN	($number_list)	";

				if($next_key != $od_id[$key] || $cnt == (count($source))) {
					$주문번호연결 = " CL.od_id = '$od_id[$key]' ";
				}

				/*로그*/
				$ins_sql = "INSERT INTO 		log_table	SET
																logtype = 'clayorder',		/*로그유형 ( ex: clayorder )*/
																gr_id	= '".$od_id[$key]."',							/*pk_id*/
																pk_id	= '".str_replace("'",'',$number_list)."',	/*pk_id*/
																it_id		= '',
																memo = '주문서출력',		/*메모태그*/
																admin_id = '$member[mb_id]',		/*로그를 남긴 관리자ID*/
																col = 'print_yn',					/*변경 항목*/
																value = 'Y',							/*변경된 값*/
																reg_date = now()				/*변경된 날짜*/
				";
				sql_query($ins_sql);
			}
			else {
				$prev_key = $od_id[$key];
				continue;
			}

			if($cnt == (count($source))) {
				$총선택개수 = count($source);
			}

		}

		//개별 주문정보 한건 로딩
		$sql = "SELECT		CL.*,
											CI.*,
											CLS.order_cnt,
											GI.*
						FROM		clay_order	CL
										LEFT JOIN clay_order_info CI ON (CI.od_id = CL.od_id)
										LEFT JOIN gp_info GI ON (GI.gpcode = CL.gpcode)
										/* 개인주문건별 총주문금액, 주문건수 */
										LEFT JOIN (		SELECT	CL.od_id,
																					COUNT(CL.od_id) AS order_cnt,
																					SUM( ROUND(IF(GI.volprice_yn = 'Y',/*참:*/(( GPO.po_cash_price * (SELECT USD FROM flow_price ORDER BY ymd DESC LIMIT 1) * (1+ (IT.gp_charge + IT.gp_duty)/100) ) * 1.1),/*거짓:*/IT.gp_price) /100)*100 * CL.it_qty) AS total_price, /* 이전 주문건들은 gp_price를 사용하고 있어서 앞으로는 po_cash_price기준으로 사용  */
																					SUM( ROUND(CL.it_org_price/100)*100 * CL.it_qty) AS total_orgprice	/* 주문당시금액 */
																	FROM	clay_order CL
																				LEFT JOIN clay_order_info CI ON (CI.od_id = CL.od_id)
																				LEFT JOIN g5_shop_group_purchase IT ON (IT.gp_id = CL.it_id)
																				LEFT JOIN gp_info GI ON (GI.gpcode = CL.gpcode)
																				LEFT JOIN g5_shop_group_purchase_option GPO ON (GPO.gp_id = CL.it_id AND GPO.po_num = 0)
																	GROUP BY CL.od_id
										) AS CLS ON (CLS.od_id = CL.od_id)
						WHERE		$PK목록
		";
		// echo $sql."<br>";
		$one_result = sql_query($sql);
		$order = mysql_fetch_array($one_result);


		/* 모두출력 or 기존출력건 제외 조건*/
		// if($all_yn == 'N') {
		// 	$중복출력조건 = " AND	print_yn = 'N' ";
		// }

		// if($type == 'CART') {
		// 	//where조건 추가
		// 	$카트내역인쇄조건 = "AND	CL.number = '$number' ";
		// 	$PK목록 = " AND		number = '$number' ";

		// } /* 선택된 주문내역만 출력 */
		// else if($type == 'CHOICE') {
		// 	$카트내역인쇄조건 = "AND	CL.number IN ($number) ";
		// 	$PK목록 = " AND		number IN ($number) ";
		// }


		/* 최하단 모든 상품주문내역  1행,2행,3행... 주문내역 목록 SQL */
		$sql = "	SELECT	CL.*,
											CI.*,
											IT.*,
											CL.it_org_price * CL.it_qty AS total_price,
											GI.volprice_yn
							FROM		clay_order CL
											LEFT JOIN clay_order_info CI ON (CI.od_id = CL.od_id)
											LEFT JOIN g5_shop_group_purchase IT ON (IT.gp_id = CL.it_id)
											LEFT JOIN gp_info GI ON (GI.gpcode = CL.gpcode)
											LEFT JOIN g5_shop_group_purchase_option GPO ON (GPO.gp_id = CL.it_id AND GPO.po_num = 0)
							WHERE		$PK목록
							$주문진행상태조건
		";
		$all_result = sql_query($sql);




		//프린트출력여부 업데이트는 조회를 하고나서 수정되야됨
		$upd_sql = "	UPDATE	clay_order	 CL	SET
														CL.stats = '25'
									WHERE		$PK목록
									AND			CL.stats = '20'
		";
		sql_query($upd_sql);


		$upd_sql = "	UPDATE	clay_order	CL		SET
														CL.print_yn = 'Y'
									WHERE		$PK목록
									AND			CL.stats IN ('20','25','30','35','40','45')
		";
		sql_query($upd_sql);

		/* 주문ID별로 페이지 분할을 위한 divPage설정, 마지막페이지에는 붙지 않음. */
?>

	<p <?=($cnt != $총선택개수) ? "class='divPage'" : ''?>>

		<table class='orderBuyerinfo'>
			<tr>
				<th class='orderBuyerinfo_th'>닉네임</th>
				<td class='orderBuyerinfo_td'><?=$order[clay_id]?></td>
			</tr>
			<tr>
				<th class='orderBuyerinfo_th'>성함</th>
				<td class='orderBuyerinfo_td'><?=$order[name]?></td>
			</tr>
			<tr>
				<th class='orderBuyerinfo_th'>출력일시</th>
				<td class='orderBuyerinfo_td'><?=date("Y-m-d H:m:s")?></td>
			</tr>
		</table>
	<?
		/* 주문내역 상품 합계액 */
		$total_sql = "
					SELECT	CL.od_id,
									CL.clay_id,
									#IT.it_sc_price AS baesongbi,
									GI.baesongbi,
									SUM(CL.it_qty * CL.it_org_price)  AS total_price,
									CL.stats,
									CI.memo,
									CI.admin_memo,
									CC.value AS delivery_type,
									CI.delivery_price,
									GI.	gpcode,				/*공구코드*/
									GI.	gpcode_name,	/*공구이름 간략하게*/
									GI.	gp_type,			/*정기 or 긴급*/
									GI.	links,				/*공구진행할 상품코드들*/
									GI.	choice_dealer,		/*전체 또는 딜러(브랜드) 선택*/
									GI.	volprice_yn,	/*볼륨프라이스 적용여부 Y/N*/
									GI.	list_view,		/*공구진행내역 보기/안보기*/
									GI.	baesongbi		/*배송비*/
					FROM		clay_order CL
									LEFT JOIN clay_order_info CI ON (CI.od_id = CL.od_id)
									LEFT JOIN g5_shop_group_purchase IT ON (IT.gp_id = CL.it_id)
									LEFT JOIN g5_shop_group_purchase_option GPO ON (GPO.gp_id = CL.it_id AND GPO.po_num = 0)
									LEFT JOIN gp_info GI ON (GI.gpcode = CL.gpcode)
									LEFT JOIN comcode CC ON (CC.ctype = 'clayorder' AND CC.col = 'delivery_type' AND CC.code = CI.delivery_type)
					WHERE		$주문번호연결
					AND			CL.stats IN (20,25,30,35,40,50,60)
					GROUP BY CL.od_id
		"; //LEFT OUTER JOIN g5_shop_group_purchase_option B ON (B.gp_id = CL.it_id)

		//소계
		$realQty = $row['ct_qty'] - $row['ct_gp_soldout'];
		$상품금액 = $opt_price * $realQty;
		$ct_point['stotal'] = $row['ct_point'] * $realQty;


		// echo $sql."<br>";
		$total_result = sql_query($total_sql);
		$total = sql_fetch_array($total_result);

		$택배비부담방법 = $total[delivery_type];
		$배송비 = $total[delivery_price];

		$입금금액 = $total[total_price] + $total[baesongbi];
		?>
		<table class='orderPriceinfo'>
			<tr>
				<th class='orderPriceinfo_th'>총 상품금액</th>
				<td class='orderPriceinfo_td' width='140px'><?=number_format($total[total_price])?></td>
			</tr>
			<tr>
				<th class='orderPriceinfo_th'>+ 배송비</th>
				<td class='orderPriceinfo_td'><?=$택배비부담방법?>(<?=number_format($배송비)?>)</td>
			</tr>
			<tr>
				<th class='orderPriceinfo_th'>주문일자</th>
				<td class='orderPriceinfo_td'><?=$order[od_date]?></td>
			</tr>
		</table>

		<table class='orderinfo'>
			<tr>
				<td class='orderInfo_td'>&nbsp;<?=$total[memo]?><?=($total[admin_memo]) ? "<br><br>코인즈투데이 : ".$total[admin_memo] : ""?></td>
				<td class='orderInfo_th' style='border-left:1px solid black;'>
					<span class='orderinfo_odid'><?=$total[od_id]?> [<?=$order[order_cnt]?>건]</span><br>
					<span class='orderinfo_clayid'><?=$total[clay_id]?></span>
				</td>
			</tr>
		</table>

		<br><br>

		<table width='100%' border=0 cellpadding=0 cellspacing=0>
			<thead>
			<tr>
				<th scope="col" class="ordertable_th">사진</th>
				<th scope="col" width='40%' class="ordertable_th">상품명</th>
				<th scope="col" class="ordertable_th">상품단가</th>
				<th scope="col" class="ordertable_th">주문수량</th>
				<th scope="col" class="ordertable_th">상품금액</th>
				<th scope="col" class="ordertable_th">주문상태</th>
				<!-- th scope="col" class="ordertable_th">미입고수량</th>
				<th scope="col" class="ordertable_th">검수여부</th>
				<th scope="col" class="ordertable_th">단가</th>
				<th scope="col" class="ordertable_th">환율</th>
				<th scope="col" class="ordertable_th">신청수량</th>
				<th scope="col" class="ordertable_th">품절수량</th-->
			</tr>
			</thead>
			<tbody>
		<?

		$k = 0;
		while($row = sql_fetch_array($all_result)) {
			$gp_name = '<b>' . stripslashes($row['gp_name']) . '</b>';

			/* http 가 들어간건 다이렉트로, 아닌건 get_it_thumb함수로 */
			if( strstr($row[gp_img],'http')) {
				$image = "<img src='$row[gp_img]' width=69 />";
			}
			else {
				$image = get_it_thumbnail1($row[gp_img], 69, 69, '', 1);
			}

			/* 자사URL이미지로 잡혀있는거는 리사이징 */
			if( strstr($row[gp_img],'http://coinstoday.co.kr') ) {
				$path = str_replace("http://coinstoday.co.kr","",$row[gp_img]);
				$image = "<img src='/image.php/$row[gp_id].jpg?width=69&height=69&image=$path' alt='$row[gp_id]'>";
			}

		?>
			<tr>
				<td title='사진' class="ordertable_td image" width='80'>
					<div class='imgLiquidNoFill imgLiquid' style='margin:0px auto; width:69px;height:69px;padding:0 15px 0 15px;'>
						<?=$image?>
					</div>
				</td>
				<td title='상품명' class="ordertable_td" style='text-align:left;'><?=stripslashes($row['gp_name'])?></td>
				<td title='상품단가' width='90' class="ordertable_td"  style='text-align:right; padding-right:15px;'><?=number_format($row['it_org_price'])?></td>
				<td title='주문수량' width='60' class="ordertable_td"  style='text-align:center; padding-right:15px; font-size:14pt;'><?=number_format($row['it_qty'])?></td>
				<td title='상품금액' width='120' class="ordertable_td"  style='text-align:right; padding-right:15px;'><?=number_format($row['total_price'])?></td>
				<td title='주문상태' width='90' class="ordertable_td"><?=$v_stats[$row[stats]]?></td>
				<!-- td title='신청수량' class="ordertable_td"><?=number_format($row['ct_qty'])?></td>
				<td title='단가' class="ordertable_td">$<?=$row['ct_usd_price']?></td>
				<td title='환율' class="ordertable_td"><?=number_format($row['cp_price'])?></td>
				<td title='품절수량' class="ordertable_td"><b><font color='red'><?=number_format($row['ct_gp_soldout'])?></font></b></td>
				<td title='미입고수량' class="ordertable_td"><?=number_format($row['ct_wearing_cnt'])?></td>
				<td title='검수여부' class="ordertable_td"><?=number_format($realQty-$row['ct_wearing_cnt'])?></td-->
			</tr>
		<?
		}
		?>
				<!-- tr>
					<td title='사진'><?=$image?></td>
					<td title='품명'>
						<?=$gp_name?>
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
	</p>
<?

	}	//foreach
}// if-end
?>

<script src="<?=G5_JS_URL?>/jquery-1.8.3.min.js"></script>
<script src="<?=G5_JS_URL?>/imgLiquid.js"></script>
<script src="<?=G5_JS_URL?>/imgLiquid.js"></script>
<script>
$(document).ready(function() {
	$(".imgLiquidNoFill").imgLiquid({fill:false});
});
</script>

</body>
</html>