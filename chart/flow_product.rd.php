<?php
include_once('./_common.php');

?>
<html>
<head>
<script src="<?=G5_JS_URL?>/jquery-1.8.3.min.js"></script>
<script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>

<style>
body { font-size:12px; font-family:'굴림',Gulim; padding:0px; background-color: #FFFFFF;  }
table { font-size:12px; font-family:'굴림',Gulim; clear: both;  border-collapse: collapse; border-spacing: 0; }
table tr th { font-size:12px; font-family:'굴림',Gulim; background-color: #EEEEEE; border:1px solid #d1dee2; padding:0px; text-align:center; height:25px; }
table tr td {	font-size:12px; font-family:'굴림',Gulim; background-color: #FFFFFF;  padding:0px; border:1px solid #d1dee2; }
</style>

<script>
$(function () {
<?

$fp_sql = "	SELECT	gp_id
						FROM		v_flow_product
						WHERE		ymd = '2016-01-13'
						ORDER BY rand()
						LIMIT 56
";
$fp_result = sql_query($fp_sql);

while($fp = mysql_fetch_array($fp_result)) {
	$gplist[] = $fp[gp_id];	
}


for($i = 0; $i < count($gplist); $i++) {
	
	$gp_id = $gplist[$i];
	
	$sql = "SELECT	GP.*
					FROM		g5_shop_group_purchase GP
					WHERE		1=1
					AND			GP.gp_id = '$gp_id'
	";
	$result = sql_query($sql);
	$gp = mysql_fetch_array($result);
	
	if(!$gp[gp_id]) exit;
	
	?>
	
	$.getJSON('/json/flow_product.php?gp_id=<?=$gp_id?>', function (data) {

		$('#container<?=$i?>').highcharts({
			chart: {
				type: 'arearange',
				zoomType: 'x'
			},
			title: {
				text: '[<?=$gp_id?>] <?=$gp[gp_name]?>'
			},
			xAxis: {
				type: 'datetime'
			},
			yAxis: {
				title: {
					text: '최소가($)-최대가($)'
				}
			},
			tooltip: {
				crosshairs: true,
				shared: true,
				valueSuffix: '($)'
			},
			legend: {
				enabled: false
			},
			series: [{
				name: '상품시세',
				data: data
			}]
		});
	});
<? 
} // for end
?>
   
});
</script>
</head>

<body topmargin='0' leftmargin='0'>
<? 
for($i = 0; $i < count($gplist); $i++) {
?>
<div style='float:left; width:auto; height:auto;'>
	<div id="container<?=$i?>" style="width: 410px; height: 400px; margin: 0 auto;"></div>
</div>
<? 
}
?>
</body>
</html>