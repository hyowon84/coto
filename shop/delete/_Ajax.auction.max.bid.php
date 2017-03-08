<?
include_once('./_common.php');

//마지막 경매
$auc_res1 = sql_query("select * from g5_shop_auction where it_id='".$_POST[it_id]."' order by no desc limit 0, 1 ");
$auc_num1 = mysql_num_rows($auc_res1);
$auc_row1 = mysql_fetch_array($auc_res1);

//날짜 체크
$row = sql_fetch("select * from g5_shop_item where it_id='".$_POST[it_id]."' and ca_id like '10%' ");

if($auc_num1){
	$auc_row1[it_last_bid] = $auc_row1[it_last_bid];
}else{
	$auc_row1[it_last_bid] = get_price($row);
}

if($row[it_last_date] < date("YmdHis")){
	echo json_encode(array("status" => "no"));
	exit;
}

//최대경매입찰가와 현재마지막입찰가 비교
$max_bid_res = sql_query("select * from {$g5['g5_shop_auction_max_table']} where it_id='".$_POST[it_id]."' order by no desc limit 0, 1 ");
$max_bid_row = mysql_fetch_array($max_bid_res);

$it_max_bid = ceil($_POST[it_max_bid]/100) * 100;

if($max_bid_row[it_max_bid] >= $it_max_bid){	//현재 입력한 최고경매입칠가보다 더 높은 최대경매입찰가가 있을때
	echo json_encode(array("status" => "no1"));
	exit;
}

if($max_bid_row[it_max_bid] >= $it_max_bid){	//현재 입력한 최대경매입찰가보다 현재 최대경매입찰가가 높을때
	echo json_encode(array("status" => "no2"));
	exit;
}

if($auc_row1[it_last_bid] >= $it_max_bid){	//현재 입력한 최대경매입찰가보다 현재 마지막입찰가가 높을때
	echo json_encode(array("status" => "no3"));
	exit;
}

$bid_price = $max_bid_row[it_max_bid] * 0.11;
$bid_price = ceil($bid_price/100) * 100;		//다음 입찰가

$it_last_bid = $max_bid_row[it_max_bid];
$it_last_bid = $it_last_bid + $bid_price;		//현재 경매가

if($it_last_bid > $it_max_bid){

	$bid_price = $_POST[it_max_bid] * 0.11;
	$bid_price = ceil($bid_price/100) * 100;		//다음 입찰가

	$it_last_bid = $_POST[it_max_bid];				//현재 경매가

	sql_query("
		insert into g5_shop_auction set
		it_id='".$_POST[it_id]."',
		ca_id='".$_POST[ca_id]."',
		it_bid='".$it_max_bid."',
		it_last_bid='".$it_max_bid."',
		loginid='".$member[mb_id]."',
		bid_status='',
		date='".strtotime("now")."'
	");

}else if($auc_row1[it_last_bid] > $max_bid_row[it_max_bid]){

	$bid_price = $auc_row1[it_last_bid] * 0.11;
	$bid_price = ceil($bid_price/100) * 100;		//다음 입찰가

	$it_last_bid = $auc_row1[it_last_bid] + $bid_price;			//현재 경매가

	sql_query("
		insert into g5_shop_auction set
		it_id='".$_POST[it_id]."',
		ca_id='".$_POST[ca_id]."',
		it_bid='".$bid_price."',
		it_last_bid='".$it_last_bid."',
		loginid='".$member[mb_id]."',
		bid_status='',
		date='".strtotime("now")."'
	");

}else{

	if($auc_row1[loginid] != $member[mb_id]){
		sql_query("
			insert into g5_shop_auction set
			it_id='".$_POST[it_id]."',
			ca_id='".$_POST[ca_id]."',
			it_bid='".$bid_price."',
			it_last_bid='".$it_last_bid."',
			loginid='".$member[mb_id]."',
			bid_status='',
			date='".strtotime("now")."'
		");
	}

}

sql_query("
	insert into {$g5['g5_shop_auction_max_table']} set
	it_id='".$_POST[it_id]."',
	ca_id='".$_POST[ca_id]."',
	it_max_bid='".$it_max_bid."',
	loginid='".$member[mb_id]."',
	date='".strtotime("now")."'
");






$cart_chk_res = sql_query("select * from {$g5['g5_shop_cart_table']} where it_id='".$_POST[it_id]."' and mb_id='".$member[mb_id]."' and auc_status='y' ");
$cart_chk = mysql_num_rows($cart_chk_res);

if(!$cart_chk){
	$it = sql_fetch("select * from {$g5['g5_shop_item_table']} where it_id='".$_POST[it_id]."' ");

	// 포인트
	$point = 0;
	if($config['cf_use_point'])
		$point = get_item_point($it);

	$tmp_cart_id = get_session('ss_cart_id');

	$sql = " INSERT INTO {$g5['g5_shop_cart_table']}
					( ct_gubun, od_id, mb_id, it_id, it_name, ct_status, ct_price, ct_point, ct_point_use, ct_stock_use, ct_option, ct_qty, ct_notax, io_id, io_type, io_price, ct_time, ct_ip, ct_send_cost, ct_direct, ct_select, auc_status )
				VALUES ";


	$it_price = $it[it_price];
	$auction_status = "y";

	$io_id = $_POST['io_id'];
	$io_type = $_POST['io_type'];
	$io_value = $_POST['io_value'];

	// 선택옵션정보가 존재하는데 선택된 옵션이 없으면 건너뜀
	if($lst_count && $io_id == '')
		continue;

	// 구매할 수 없는 옵션은 건너뜀
	if($io_id && !$opt_list[$io_type][$io_id]['use'])
		continue;

	$io_price = $opt_list[$io_type][$io_id]['price'];

	// 구매가격이 음수인지 체크
	if($io_type) {
		if((int)$io_price < 0)
			alert('구매금액이 음수인 상품은 구매할 수 없습니다.');
	} else {
		if((int)$it['it_price'] + (int)$io_price < 0)
			alert('구매금액이 음수인 상품은 구매할 수 없습니다.');
	}

	// 동일옵션의 상품이 있으면 수량 더함
	$sql2 = " select ct_id
				from {$g5['g5_shop_cart_table']}
				where od_id = '$tmp_cart_id'
				  and it_id = '$it_id'
				  and io_id = '$io_id'
				  and ct_status = '쇼핑' ";
	$row2 = sql_fetch($sql2);

	if($row2['ct_id']) {

		$sql3 = " update {$g5['g5_shop_cart_table']}
					set ct_qty = ct_qty + '$ct_qty'
					where ct_id = '{$row2['ct_id']}' ";
		sql_query($sql3);
	}

	// 배송비결제
	if($it['it_sc_type'] == 1)
		$ct_send_cost = 2; // 무료
	else if($it['it_sc_type'] > 1 && $it['it_sc_method'] == 1)
		$ct_send_cost = 1; // 착불

	$sql .= $comma."( 'N', '$tmp_cart_id', '{$member['mb_id']}', '".$_POST[it_id]."', '".addslashes($it['it_name'])."', '쇼핑', '{$it_price}', '$point', '0', '0', '$io_value', '".$_POST[ct_qty]."', '{$it['it_notax']}', '$io_id', '$io_type', '$io_price', '".G5_TIME_YMDHIS."', '$REMOTE_ADDR', '$ct_send_cost', '$sw_direct', '$ct_select', '$auction_status' )";
	$comma = ' , ';

	sql_query($sql);
}







$now_max_bid_row = sql_fetch("select * from {$g5['g5_shop_auction_max_table']} where it_id='".$_POST[it_id]."' and loginid='".$member[mb_id]."' order by no desc limit 0, 1 ");

$auc_res2 = sql_query("select * from g5_shop_auction where it_id='".$_POST[it_id]."' order by no desc limit 0, 1 ");
$auc_row2 = mysql_fetch_array($auc_res2);

$bid_price1 = $auc_row2[it_last_bid] * 0.11;
$bid_price1 = ceil($bid_price1/100) * 100;		//다음 입찰가

echo json_encode(array("status" => "ok", "max_bid_price" => $now_max_bid_row[it_max_bid], "bid_price" => $bid_price1, "it_last_bid" => $it_last_bid));
exit;
?>