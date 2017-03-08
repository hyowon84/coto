<?php
include_once('./_common.php');

if($buy_kind == "공동구매" || $mode == 'gp'){
	$url = "/shop/wishlist_gp.php";
}else{
	$url = "/shop/wishlist.php";
}

$login_url = $url;

/*공동구매에서 위시리스트 추가시 */
if($mode == 'gp_add') {
	$login_url = "/shop/wishupdate.php?mode=gp&it_id=$it_id&ct_qty=$ct_qty";
}



if (!$is_member)
    alert('회원 전용 서비스 입니다.', G5_BBS_URL.'/login.php?url='.urlencode($login_url));

/* 단일 삭제 */
if ($w == "d"){
    $wi_id = trim($_GET['wi_id']);
    $sql = " delete from {$g5['g5_shop_wish_table']}
              where wi_id = '$wi_id'
                and mb_id = '{$member['mb_id']}' ";
    sql_query($sql);
}/* 복구 선택 삭제 */
else if($_POST[w] == "seldel"){

	for($i = 0; $i < count($_POST['wi_id']); $i++){

		if($_POST['chk_it_id'][$i]){
			$wi_id = $_POST['wi_id'][$_POST['chk_it_id'][$i]];
			$sql = " delete from {$g5['g5_shop_wish_table']}
					  where wi_id = '$wi_id'
						and mb_id = '{$member['mb_id']}' ";

			sql_query($sql);
		}
	}
}	/* 위시리스트 추가 */
else{
    if(is_array($it_id)) $it_id = $_POST['it_id'][0];
    
    $sql = " select wi_id, wi_cnt from {$g5['g5_shop_wish_table']}
              where mb_id = '{$member['mb_id']}' and it_id = '$it_id' ";
    $row = sql_fetch($sql);

	$wi_cnt = $row[wi_cnt];

    if ($row['wi_id']) { // 이미 있다면 삭제함
        $sql = " delete from {$g5['g5_shop_wish_table']} where wi_id = '{$row['wi_id']}' ";
        sql_query($sql);
    }
	
    
	if($_POST["ct_qty".$it_id]){
		$wi_cnt = $wi_cnt + $_POST["ct_qty".$it_id];
	}
	else{
		$wi_cnt = $wi_cnt + $ct_qty;
	}

    $sql = " insert {$g5['g5_shop_wish_table']} SET
    					mb_id = '{$member['mb_id']}',
                  it_id = '$it_id',
                  wi_time = '".G5_TIME_YMDHIS."',
                  wi_ip = '$REMOTE_ADDR',
						wi_cnt = '".$wi_cnt."'
					";
    sql_query($sql);
}

goto_url($url);
?>