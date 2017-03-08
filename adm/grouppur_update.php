<?
include_once('./_common.php');

if($_POST[HTTP_CHK] != "CHK_OK"){
	alert("잘못된 경로입니다.");
}

// 시작
if($_POST[mode]!= "modify" && $_POST[mode]!= "reOrder"){
	$row = sql_fetch("select group_code from {$g5['g5_group_cnt_pay_table']} where group_code='".$_POST[group_code][$_POST[idx]]."'");
	if(!$row['group_code'])alert("지금 입력하신 코드는 한번도 확인이 되지 않은 코드입니다.");
}

if($_POST[mode] == "modify"){
	
	// 시작	
	$row = sql_fetch("select type_code from {$g5['g5_total_amount_table']} where type_code='".$_POST[group_code][$_POST[idx]]."'");

	// 초기 검샘을 위한 값 입력
	if(!$row['type_code']){
		sql_query("
			insert into {$g5['g5_total_amount_table']} set
			dt_f='".strtotime($_POST[fr_date][$_POST[idx]]." 00:00:00")."',
			dt_l='".strtotime($_POST[to_date][$_POST[idx]]." 59:59:59")."',
			type_code='".$_POST[group_code][$_POST[idx]]."',
			total_appli='0',
			gc_state = 'S',
			dealer='".$_POST[dealer]."'");
	}else{
		sql_query("
			update  {$g5['g5_total_amount_table']} set
			dt_f='".strtotime($_POST[fr_date][$_POST[idx]]." 00:00:00")."',
			dt_l='".strtotime($_POST[to_date][$_POST[idx]]." 11:59:59")."',
			gc_state = 'S', dealer='".$_POST[dealer]."' where type_code = '".$row['type_code']."'");
	}

	/* 카트DB 업데이트? */
	
	$sel_sql = "	SELECT	group_code
						FROM		g5_group_cnt_pay
						WHERE		no='".$_POST[idx]."'
	";
	$gc_row = sql_fetch($sel_sql);
	
	/* 공동구매 코드가 존재하는 경우에만 갱신
	 * 
	 
	 공동구매코드 관련 갱신여부
	 
	 ex) 현재공동구매코드 -> 변경될공동구매코드
	 * 정기 -> 긴급		기존 cart 주문정보 갱신되야함
	 * 긴급 -> 정기
	 * '' -> 정기			기존 ''으로 되있는 카트정보만 갱신되야함
	 *  '' -> 긴급
	 
	 cart주문정보가 갱신되면 안돼는 케이스
	 * 정기 -> ''
	 * 긴급 -> ''
		
	 * 변경될공동구매코드만 4글자 이상 존재하는경우에만 갱신.
	 *  */
	if(strlen($_POST[group_code][$_POST[idx]]) > 4) 
		updateReCartPurchaseCode($_POST[group_code][$_POST[idx]],$_POST[fr_date][$_POST[idx]],$_POST[to_date][$_POST[idx]],$_POST[dealer],$gc_row[group_code]);
	
	$upd_sql = "	UPDATE		g5_group_cnt_pay	SET
													group_code='".$_POST[group_code][$_POST[idx]]."',	/* 공동구매코드 */
													fr_date='".$_POST[fr_date][$_POST[idx]]."',
													to_date='".$_POST[to_date][$_POST[idx]]."',
													cnt_pay='".str_replace(",", "", $_POST[cnt_pay][$_POST[idx]])."',													
													gc_state = 'S'
							WHERE		no='".$_POST[idx]."'
	";
	sql_query($upd_sql);

} /* 집계 */
elseif($_POST[mode] == "wait"){
	
	sql_query("update {$g5['g5_total_amount_table']} set gc_state = 'W' where type_code='".$_POST[group_code][$_POST[idx]]."'");
	sql_query("update {$g5['g5_group_cnt_pay_table']} set gc_state = 'W' where no='".$_POST[idx]."'");

}else if($_POST[mode] == "end"){

	//중복체크


	$appli_row = sql_fetch("
		select SUM(a.ct_price * a.ct_qty) as total from {$g5['g5_shop_cart_table']} a
		left join {$g5['g5_shop_group_purchase_table']} b
		on ( a.it_id = b.gp_id )
		where a.ct_type = '".$_POST[dealer]."' and total_amount_code='".$_POST[group_code][$_POST[idx]]."'");

	sql_query("update {$g5['g5_total_amount_table']} set comp_dt='".strtotime("now")."', total_appli='".$appli_row[total]."', gc_state = 'E' where type_code='".$_POST[group_code][$_POST[idx]]."'");

	sql_query("
		update {$g5['g5_group_cnt_pay_table']} set
		fr_date='',
		to_date='',
		cnt_pay='',
		group_code='',
		gc_state = 'E'
		where no='".$_POST[idx]."'
	");

	do{
		// 주문서 생성
		 makeGroupPurchaseOrder($_POST[group_code][$_POST[idx]]);

		 $ctChk = sql_fetch("select count(*) as cnt from {$g5['g5_shop_cart_table']} where total_amount_code = '".$_POST[group_code][$_POST[idx]]."' and ct_status='쇼핑'");

	}while($ctChk['cnt']);
}else if($_POST[mode] == "reOrder"){

	if(!$_POST[group_code])alert("공동구매코드값이 넘어오지 않았습니다.");

	//중복체크
	do{
		// 주문서 생성
		 makeGroupPurchaseOrder($_POST[group_code]);

		 $ctChk = sql_fetch("select count(*) as cnt from {$g5['g5_shop_cart_table']} where total_amount_code = '".$_POST[group_code]."' and ct_status='쇼핑'");

	}while($ctChk['cnt']);
}


alert("정상적으로 수정 되었습니다.", "./grouppur_form.php");
?>