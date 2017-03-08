<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

$str = '';
$exists = false;

$ca_id_len = strlen($ca_id);
$len2 = $ca_id_len + 2;
$len4 = $ca_id_len + 4;

$sql = "SELECT	C.ca_id,
								C.ca_name,
								(	SELECT	COUNT(*)
									FROM		g5_shop_group_purchase GP
									WHERE		GP.ca_id LIKE CONCAT(C.ca_id,'%')
									OR			GP.ca_id2 LIKE CONCAT(C.ca_id,'%')
									OR			GP.ca_id3 LIKE CONCAT(C.ca_id,'%')
								) AS cnt
				FROM 		g5_shop_category C	
				WHERE		C.ca_id LIKE '$ca_id%'
				AND			LENGTH(C.ca_id) = $len2
				AND			C.ca_use = '1'
				ORDER BY C.ca_order ASC, C.ca_id ASC
";
$result = sql_query($sql);

if($_GET[mode] == 'jhw') {
	echo $sql;
	echo "<br>";
}

while ($row=sql_fetch_array($result)) {
		//$상품테이블 = (1) ? 'g5_shop_group_purchase' : $g5['g5_shop_item_table'];
    //$row2 = sql_fetch(" select count(*) as cnt from $상품테이블 where (ca_id like '{$row['ca_id']}%' or ca_id2 like '{$row['ca_id']}%' or ca_id3 like '{$row['ca_id']}%')  ");
    $str .= '<li>★ <a href="./gplist.php?ca_id='.$row['ca_id'].'">'.$row['ca_name'].' <font color=red>('.$row['cnt'].')</font></a></li>';
    $exists = true;
}

if ($exists) {
?>

<link rel="stylesheet" href="<?php echo G5_SHOP_SKIN_URL; ?>/style.css">

<!-- 상품분류 1 시작 { -->
<aside id="sct_ct_1" class="sct_ct">
    <h2>현재 상품 분류와 관련된 분류</h2>
    <ul>
        <?php echo $str; ?>
    </ul>
</aside>
<!-- } 상품분류 1 끝 -->

<?php } ?>