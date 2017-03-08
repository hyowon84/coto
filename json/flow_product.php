<?
include_once('./_common.php');
include_once('../lib/coinstoday.lib.php');


// 코드값 검색
$sql = "SELECT	FP.ymd,	
								FP.min_price,	/*최소가격*/
								FP.max_price	/*최대가격*/
				FROM		v_flow_product FP
				WHERE		1=1
				AND			FP.gp_id = '$gp_id'
				
				ORDER BY FP.ymd ASC
";
$result = sql_query($sql);

$cart_count = mysql_num_rows($result);

$json = array();

$i = 0;
while($row = mysql_fetch_assoc($result)) {
	$datetime = date_create("$row[ymd] 0:0:0");
	$timestamp = date_timestamp_get($datetime)."000";
	
	$temp= array($timestamp*1,$row[min_price]*1,$row[max_price]*1);
	array_push($json, $temp);
}

$json_data = json_encode_unicode($json);
echo $json_data;
?>