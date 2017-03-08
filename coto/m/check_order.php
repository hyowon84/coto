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
						<div class="gpcode"><label>공동구매명</label><p><?=$공구명?></p></div>
						<div class="od_id"><label>주문번호</label><p><?=$row[od_id]?></p></div>
						<div class="ymd"><label>주문일시</label><p><?=date("Y.m.d H:m:s",strtotime($row[od_date]))?></p></div>
						<div class="qty"><label>수량</label><p><?=$row[it_qty]?></p></div>
						<div class="price"><label>단가</label><p><?=number_format($row[it_org_price])?></p></div>
						<div class="sumPrice"><label>합계</label><p><?=number_format($row[total_orgprice])?></p></div>
						<div class="stats"><label>주문상태</label><p><?=$v_stats[$row[stats]]?></p></div>
					</div>
				</li>
<?
	}
?>
</ul>
<?
}
?>