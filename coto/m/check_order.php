<?
if( ($name && $od_id && $hp1 && $hp2 && $hp3) || $member[mb_id] ) {
?>
<ul>
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
				<li class="rtLi" <?=$bgcolor?>>
					<div class="productName line1row taCenter"><b><a <?="href=\"".G5_SHOP_URL."/grouppurchase.php?gpcode=".$row[gpcode]."&gp_id=".$row[it_id]."&ca_id=".$row[ca_id]."\" class=\"sct_a sct_img\""?>><?=stripslashes($row[it_name])?></a></b></div>
					<div class="productImg"><?=$image?></div>
					<div class="productInfo">
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<th width="60">공동구매</th>
								<td class="txtwrap"><?=$공구명?></td>
							</tr>
							<tr>
								<th>주문번호</th>
								<td class="txtwrap"><?=$row[od_id]?></td>
							</tr>
							<tr>
								<th>주문일시</th>
								<td class="txtwrap"><?=date("Y.m.d H:m:s",strtotime($row[od_date]))?></td>
							</tr>
							<tr>
								<th>수량</th>
								<td class="txtwrap"><?=$row[it_qty]?></td>
							</tr>
							<tr>
								<th>단가</th>
								<td class="txtwrap"><?=number_format($row[it_org_price])?></td>
							</tr>
							<tr>
								<th>합계</th>
								<td class="txtwrap"><?=number_format($row[total_orgprice])?></td>
							</tr>
							<tr>
								<th>주문상태</th>
								<td class="txtwrap"><?=$v_stats[$row[stats]]?></td>
							</tr>
						</table>
					</div>
				</li>
<?
	}
?>
</ul>
<?
}
?>

<style>
	.productInfo div label { float:left; }
	.productInfo .txtwrap {
	text-align: right;
	width: 124px;
	height: 14px;
	margin: 4px 0px 4px 0px;
	display: inline-block;
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: normal;
	line-height: 1.2;
	/*text-align: left;*/
	word-wrap: break-word;
	display: -webkit-box;
	-webkit-line-clamp: 3;
	-webkit-box-orient: vertical;
}

</style>
