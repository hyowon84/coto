<?
include_once("./_common.php");

$od_id = $_POST[od_id];
$dt = $_POST[dt];

$row = sql_fetch("select * from {$g5['g5_shop_order_table']} where od_id='".$od_id."' ");
$cart_row = sql_fetch("select * from {$g5['g5_shop_cart_table']} where od_id='".$od_id."' order by ct_time desc limit 0, 1 ");
?>

<style>
.deli_info1_tb{}
.deli_info1_tb th{text-align:center;padding:13px;border-top:2px #000 solid;border-bottom:1px #eeeff1 solid;font-weight:normal;}
.deli_info1_tb td{text-align:center;padding:13px;border-bottom:1px #eeeff1 solid;font-weight:normal;}
</style>

<div style="padding:10px 25px 10px 25px;color:#fff;font-size:17px;font-weight:bold;background:#56ccc8;">배송조회</div>

<div style="margin:0 auto;margin-top:27px;width:510px;">
	<div style="padding:20px 20px 30px 20px;border:5px #eeeff1 solid;">
		<div style="font-size:17px;"><span style="color:#000;font-weight:bold;">상품주문번호</span> <span style="color:#66d0cc;"><?=$od_id?></span></div>
		<div style="margin:5px 0 0 0;font-size:11px;color:#545454;">
			<?=$cart_row[it_name]?>
			<?
			if($row['od_cart_count'] > 1){
				$od_cart_count = $row['od_cart_count'] - 1;
				echo "외 ".$od_cart_count."건";
			}
			?>
		</div>
		<div style="margin:10px 0 0 0;font-size:12px;color:#2977ab;"><?=$row[od_status]?> (<?=date("Y.m.d", strtotime($row[od_invoice_time]))?>)</div>
	</div>

	<div style="margin:27px 0 0 0;color:#000;font-weight:bold;font-size:15px;">송장번호</div>
	<div style="margin:5px 0 0 0;height:200px;overflow-y:auto;">
		<table border="0" cellspacing="0" cellpadding="0" width="100%" class="deli_info1_tb">
			<tr>
				<th>구분</th>
				<th>택배사</th>
				<th>연락처</th>
				<th>송장번호</th>
				<th>수령인</th>
				<th>상태</th>
			</tr>

			<tr>
				<td>해외배송</td>
				<td style="color:#510d77;"><?=get_delivery_inquiry_company($row['od_delivery_company'], $row['od_invoice'], 'dvr_link')?></td>
				<td><?php echo get_delivery_inquiry_tel($row['od_delivery_company'], $row['od_invoice']); ?></td>
				<td><?=$row[od_invoice]?></td>
				<td><?=$row[od_b_name]?></td>
				<td style="color:#66d0cc;"><?=$row[od_status]?></td>
			</tr>

		</table>
	</div>
	<div style="margin:17px 0 0 0;text-align:center;">
		<img src="<?=G5_IMG_URL?>/mem_conf_ico.gif"> 송장번호를 클릭하시면 해당 택배사에서 배송현황을 파악하실 수 있습니다.
	</div>
</div>