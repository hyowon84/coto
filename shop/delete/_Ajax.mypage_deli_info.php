<?
include_once("./_common.php");

$T_od_id = $_POST[T_od_id];
$dt = $_POST[dt];
?>

<style>
.my_deli_info_tb{}
.my_deli_info_tb th{background:#f2f2f2;border-top:1px #cfcfcf solid;border-bottom:1px #cfcfcf solid;text-align:center;}
.my_deli_info_tb td{border-bottom:1px #cfcfcf solid;padding:5px 10px 5px 10px;}
.my_deli_info_tb .left{border-left:1px #cfcfcf solid;}
.my_deli_info_tb .right{border-right:1px #cfcfcf solid;}
</style>

<div style="color:#223753;font-size:20px;font-weight:bold;">공동구매</div>
<div style="margin:7px 0 0 0;font-size:17px;font-weight:bold;"><span style="color:#000;">통합배송신청날짜</span><span style="color:#74d3d0;margin:0 0 0 10px;font-weight:normal;font-size:15px;"><?=date("Y/m/d", $dt)?></span></div>
<div style="margin:7px 0 0 0;font-size:12px;overflow-y:auto;height:450px;">
	
	<table border="0" cellspacing="0" cellpadding="0" width="100%" class="my_deli_info_tb">
		<tr height="25px">
			<th class="left">주문일자/주문번호</th>
			<th>브랜드/주문번호</th>
			<th class="right">상품정보</th>
			<th class="right">배송상태</th>
		</tr>


		<?
		$sql = "
			select * from {$g5['g5_shop_order_table']}
			where combine_deli_code='".$T_od_id."'
			order by od_id desc
		";

		$res = sql_query($sql);

		for($i = 0; $row = mysql_fetch_array($res); $i++){
			$cart_row = sql_fetch("select * from {$g5['g5_shop_cart_table']} where od_id='".$row[od_id]."' order by ct_time desc limit 0, 1 ");
		?>
		<tr>
			<td style="width:100px;">
				<?=date("Y.m.d", strtotime($row[od_time]))?></br>
				<span style="font-size:9px;text-decoration:underline;color:#56ccc8;">
					<a href="<?php echo G5_SHOP_URL; ?>/orderinquiryview.php?od_id=<?php echo $row['od_id']; ?>&amp;uid=<?php echo $uid; ?>" style="font-size:9px;font-weight:normal;text-decoration:underline;color:#56ccc8;"><?php echo $row['od_id']; ?></a>
				</span>
			</td>
			<td>APMEX_140530</td>
			<td class="right">
				<a href="<?=G5_SHOP_URL?>/grouppurchase.php?gp_id=<?=$cart_row[it_id]?>&ca_id=<?=$cart_row[ct_type]?>"><?=$cart_row[it_name]?></a>
				<?
				if($row['od_cart_count'] > 1){
					$od_cart_count = $row['od_cart_count'] - 1;
					echo "외 ".$od_cart_count."건";
				}
				?>
			</td>
			<td style="text-align:center;width:80px;">
				<?=$row[od_status]?></br>
				<?if($row[od_status] == "배송준비중" || $row[od_status] == "해외배송중" || $row[od_status] == "국내배송중" || $row[od_status] == "배송완료"){?>
					<div class="my_deli_info_bn1" style="margin-top:3px;padding:2px 7px 2px 7px;border:1px #cfcfcf solid;font-size:9px;width:40px;text-align:center;margin:0 auto;cursor:pointer;">배송조회</div>
				<?}?>
			</td>
		</tr>
		<?
		}
		?>


	</table>

</div>