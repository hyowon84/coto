<?php
include_once('./_common.php');
$g5['title'] = '주문조회';
include_once('./_head.php');
global $is_admin;


if($member[mb_id]) {
	$조건 = "	AND		(CL.mb_id = '$member[mb_id]'	OR	(	CL.name		= '$member[mb_name]'	AND		CL.hphone = '$member[mb_hp]')
						)
	";
} else {
	$hphone = "$hp1-$hp2-$hp3";
	$조건 = "	AND		CL.name		= '$name'
						AND		CL.hphone = '$hphone'
						AND		CL.od_id	=	'$od_id'
	";
	$chk_sql = "	SELECT	CL.name,
												CL.hphone,
												CL.od_id
								FROM		clay_order CL
								WHERE		1=1
								$조건
	";
	$result = sql_query($chk_sql);

	$num_cnt = mysql_num_rows($result);

	$chk = mysql_fetch_array($result);

}

//수동입력 또는 회원로그인중이면 주문조회
if( ($chk[name] && $chk[hphone] && $chk[od_id]) || $member[mb_id]) {

	$ord_sql = "	SELECT	CLS.order_cnt,		/* od_id 에 연결된 총 주문내역 */
												CLS.total_price,
												CLS.total_orgprice,
												CL.number,
												CL.od_id,								/*주문번호*/
												CL.it_id,								/*주문상품코드*/
												CL.it_qty,							/*주문수량*/
												CL.it_org_price,				/*주문당시 개당 상품가격*/
												CL.stats,								/*주문상태코드*/
												CC.value AS stats_name,	/*주문상태명*/
												CC.bgcolor,
												CI.od_id,								/*주문ID*/
												CI.clay_id,
												CI.name,
												CI.receipt_name,
												CI.hphone,
												CI.zip,
												CI.addr1,
												CI.addr1_2,
												CI.addr2,
												CI.memo,
												CI.admin_memo,
												CI.delivery_company,
												CI.delivery_invoice,
												CI.od_date,
												IT.ca_id,
												IT.gp_id AS it_id,
												CL.it_name,
												IT.gp_price AS it_price,
												IT.gp_img,
												GI.gpcode,							/*공구코드*/
												GI.gpcode_name,					/*공구이름 간략하게*/
												GI.gp_type,							/*정기 or 긴급*/
												GI.volprice_yn,					/*볼륨프라이스 적용여부 Y/N*/
												CA.ca_name
								FROM		clay_order	CL
													LEFT JOIN clay_order_info CI ON (CI.od_id = CL.od_id)
													LEFT JOIN g5_shop_group_purchase IT ON (IT.gp_id = CL.it_id)
													LEFT JOIN g5_shop_category CA ON (CA.ca_id = IT.ca_id)
													LEFT JOIN (		SELECT	CL.od_id,
																							COUNT(CL.od_id) AS order_cnt,
																								SUM(IT.it_price * CL.it_qty) AS total_price,
																								SUM(CL.it_org_price * CL.it_qty) AS total_orgprice
																				FROM	clay_order CL
																							LEFT JOIN clay_order_info CI ON (CI.od_id = CL.od_id)
																							LEFT JOIN g5_shop_item IT ON (IT.it_id = CL.it_id)
																				WHERE	CL.stats NOT IN (99)
																				GROUP BY CL.od_id
													) AS CLS ON (CLS.od_id = CL.od_id)
													LEFT JOIN gp_info GI ON (GI.gpcode = CL.gpcode)
													LEFT JOIN comcode CC ON (CC.ctype = 'clayorder' AND CC.col = 'stats' AND CC.code = CL.stats)
								WHERE	1=1
								$조건
								AND		CI.hidden = 'N'
								AND		CL.stats NOT IN (99)
								ORDER BY	CL.od_date DESC
	";
	$result = sql_query($ord_sql);

	if($mode == 'jhw') echo $ord_sql;
	?>

	<script src="<?=G5_JS_URL?>/imgLiquid.js"></script>
	<script>
	$(document).ready(function() {
		$(".imgLiquidNoFill").imgLiquid({fill:false});
	});
	</script>


	<!-- 타이틀 -->
	<div class="cart_title" style='margin:20px 0px;'><?=$g5['title']?></div>


	<?

	//주문조회 모바일 페이지
	if (G5_IS_MOBILE) {
		include_once('./m/check_order.php');
		exit;
	}


	if( ($name && $od_id && $hp1 && $hp2 && $hp3) || $member[mb_id] ) {
	?>
	<div class="tbl_head01 tbl_wrap product1" style='background-color: white;'>
		<table width='100%'>
			<thead>
			<tr>
				<th scope="col" class="right" width='140'>
					공구명<br>
					주문일시
				</th>
				<th scope="col" class="right" width="70">이미지</th>
				<th scope="col" class="right" width="500">
					상품명<br>주문번호
				</th>
				<th scope="col" class="right" width="160">
					단가<br>
					수량<br>
					주문금액
				</th>
				<th scope="col" class="right" width="100">
					주문상태
				</th>
			</tr>
			</thead>
			<tbody>

			<?
			while($row = mysql_fetch_array($result)) {
				/*빠른배송상품은 카테고리명으로 대체*/
				$공구명 = ($row[gpcode] == 'QUICK') ? $row[ca_name] : $row[gpcode_name];

				if($row[ct_type] == "2010"){$ct_type = "APMEX";}
				else if($row[ct_type] == "2020"){$ct_type = "GAINSVILLE";}
				else if($row[ct_type] == "2030"){$ct_type = "MCM";}
				else if($row[ct_type] == "2040"){$ct_type = "SCOTTS DALE";}
				else{$ct_type = "OTHER DEALER";}

				/* http 가 들어간건 다이렉트로, 아닌건 get_it_thumb함수로 */
				if( strstr($row[gp_img],'http')) {
					$image = "<img src='$row[gp_img]' width=$default[de_mimg_width] />";
				}
				else {
					$image = get_it_thumbnail1($row[gp_img],$default['de_mimg_width'],$default['de_mimg_height'], '', 1);
				}

				$bgcolor = "style='background-color:$row[bgcolor];'";

				?>
					<tr <?=$bgcolor?>>
						<td style="padding:5px;">
							<?=$공구명?><br>
							<?=date("Y.m.d, H:i:s",strtotime($row[od_date]))?>
						</td>
						<td>
							<div class='imgLiquidNoFill imgLiquid' style='float:left;width:70px;height:70px;padding:0 15px 0 15px;'>
								<a <?="href=\"".G5_SHOP_URL."/grouppurchase.php?gpcode=".$row[gpcode]."&gp_id=".$row[it_id]."&ca_id=".$row[ca_id]."\" class=\"sct_a sct_img\""?>>
									<?=$image?>
								</a>
					 		</div>
						</td>
						<td>
							<b><a <?="href=\"".G5_SHOP_URL."/grouppurchase.php?gpcode=".$row[gpcode]."&gp_id=".$row[it_id]."&ca_id=".$row[ca_id]."\" class=\"sct_a sct_img\""?>>
									<?=stripslashes($row[it_name])?>
							</a></b><br>
							<?=$row[od_id]?>
						</td>
						<td align='right'>
							<?=number_format($row[it_org_price])?>원<br>
							<?=number_format($row[it_qty])?>ea<br>
							<?=number_format($row[total_orgprice])?>원
						</td>
						<td align='center'>
							<?=$row[stats_name]?>
						</td>
					</tr>
<?
			} //while end
?>
		</table>
	</div>
<?
	}	//if end

} //if end


?>
<script src="<?php echo G5_JS_URL; ?>/shop.mobile.main.js"></script>
<script src="/mobile/js/jquery.flexslider.js"></script>
<script src="<?php echo G5_JS_URL; ?>/common.js"></script>
<style>
	#ft { padding-top:0px; }
	.clayOrderTitle { margin:10px; font-size:1.5em; font-weight:bolder; text-align:center; }
	.clayOrderTB th, .clayOrderTB td { font-size:1.1em; letter-spacing:-0.5px; }
	.clayOrderTB th{ padding-left:5px; width:36%; height:30px; text-align:left; }
	.clayOrderTB td{ padding-left:5px; width:64%; height:30px; text-align:left; }
	.clayOrderTB input { font-size:1.3em; width:160px; height:26px; margin:3px; border:1px solid #d7d7d7; }

	.clayOrderTB .btn { width:105px; }
	.clayOrderTB .zip { width:70px; }
	.clayOrderTB .address { width:200px; font-size:0.9em; }
	.clayOrderTB .hphone { width:30px; font-size:0.9em; }
	.clayOrderTB .divAddrTitle { height:24px; padding-top:10px; padding-left:10px; }
</style>

<?
//최초 또는 결과가 없을경우
if(!$num_cnt && !$member[mb_id]) {

?>

<form name='chkOrderForm' action='<?=$PHP_SELF?>' method='post'>
<table class='clayOrderTB' style='margin-top:10px;' align='center'>
		<tr>
			<td colspan='2'>
				<div class='clayOrderTitle'>주문조회</div>
			</td>
		</tr>
		<tr>
			<th>성함</th>
			<td><input type='text' name='name' title='주문자성함' value='<?=$name?>' /></td>
		</tr>
		<tr>
			<th>연락처</th>
			<td>
				<input type='tel' class='hphone' name='hp1' title='연락처' value='<?=($hp1)?$hp1:'010'?>' maxlength="3" />-
				<input type='tel' class='hphone' name='hp2' title='연락처' value='<?=$hp2?>' maxlength="4" />-
				<input type='tel' class='hphone' name='hp3' title='연락처' value='<?=$hp3?>' maxlength="4" />
			</td>
		</tr>
		<tr>
			<th>최종주문번호</th>
			<td><input type='text' name='od_id' title='최종주문번호' value='<?=$od_id?>' maxlength="14" />
					<br>ex) CL201408150102
			</td>
		</tr>
		<tr>
			<td colspan='2'  style='text-align:center; height:80px;'>
				<input type='submit' value='주문내역조회' style='font-size:1.5em; font-weight:bolder; background-color:black; color:white; width:150px; height:40px; border:2px solid black; border-radius:10px;' />
			</td>
		</tr>
	</table>
</form>

<?
}
?>


<?
include_once('./_tail.php');
?>