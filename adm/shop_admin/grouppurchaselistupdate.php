<?php
$sub_menu = '500200';
$sub_sub_menu = '2';

include_once('./_common.php');

check_demo();

if (!count($_POST['chk'])) {
		alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
}

if ($_POST['act_button'] == "선택수정") {

		auth_check($auth[$sub_menu], 'w');

		for ($i=0; $i<count($_POST['chk']); $i++) {

			// 실제 번호를 넘김
			$k = $_POST['chk'][$i];

			$_POST['gp_name'][$k] = str_replace('\"', "", $_POST['gp_name'][$k]);			
			$sql = "update {$g5['g5_shop_group_purchase_table']}
								 set ca_id          = '{$_POST['ca_id'][$k]}',
										 gp_name        = \"{$_POST['gp_name'][$k]}\",
										 gp_use         = '{$_POST['gp_use'][$k]}',
										 gp_price      	= '{$_POST['gp_price'][$k]}',
										 gp_price_org  	= '{$_POST['gp_price_org'][$k]}',
										 gp_duty        = '{$_POST['gp_duty'][$k]}',
										 gp_charge      = '{$_POST['gp_charge'][$k]}',
										 jaego					=	'{$_POST['jaego'][$k]}',
										 gp_have_qty		=	'{$_POST['gp_have_qty'][$k]}',
										 gp_order				= '{$_POST['gp_order'][$k]}',
										 b2b_yn					=	'{$_POST['b2b_yn'][$k]}',
										 gp_update_time = '".G5_TIME_YMDHIS."'
							 where gp_id   = '{$_POST['gp_id'][$k]}' ";
			sql_query($sql);
			
			db_log($sql,'g5_shop_group_purchase','상품목록에서 일괄수정');
		}
} else if ($_POST['act_button'] == "선택삭제") {

		if ($is_admin != 'super')
				alert('상품 삭제는 최고관리자만 가능합니다.');

		auth_check($auth[$sub_menu], 'd');

		// _ITEM_DELETE_ 상수를 선언해야 itemdelete.inc.php 가 정상 작동함
		define('_ITEM_DELETE_', true);

		for ($i=0; $i<count($_POST['chk']); $i++) {
				// 실제 번호를 넘김
				$k = $_POST['chk'][$i];

				// include 전에 $it_id 값을 반드시 넘겨야 함
				$gp_id = $_POST['gp_id'][$k];

		$sql = " delete from {$g5['g5_shop_group_purchase_table']} where gp_id = '$gp_id' ";
		sql_query($sql);

		$sql = " delete from {$g5['g5_shop_option1_table']} where it_id = '$gp_id' ";
		sql_query($sql);

		$sql = " delete from {$g5['g5_shop_option2_table']} where it_id = '$gp_id' ";
		sql_query($sql);

		}
}else if($_POST['act_button'] == "투데이스토어로이동"){

	/*
	for ($i=0; $i<count($_POST['chk']); $i++) {
				// 실제 번호를 넘김
				$k = $_POST['chk'][$i];

				// include 전에 $it_id 값을 반드시 넘겨야 함
				$gp_id = $_POST['gp_id'][$k];

		$gp = sql_fetch("select * from {$g5['g5_shop_group_purchase_table']} where gp_id='$gp_id' ");

		$sql = "
		INSERT INTO `g5_shop_item` set
		`it_id`='".strtotime("now")."',
		`ca_id`='10',
		`ca_id2`='1020',
		`ca_id3`='',
		`it_metal_type`='SL',
		`it_metal_don`=1.000,
		`it_name`='".$gp[gp_name]."',
		`it_mobile_name`='".$gp[gp_name]."',
		`it_maker`='Perth Mint',
		`it_origin`='호주',
		`it_brand`='',
		`it_model`='',
		`it_basic`='',
		`it_explan`='',
		`it_explan2`='',
		`it_mobile_explan`='',
		`it_cust_price`=0,
		`it_price`=1000,
		`it_price_type`='A',
		`it_metal_etc_price`=0,
		`it_real_add_price`=0,
		`it_real_add_unit`='W',
		`it_use`=1,
		`it_stock_qty`=1,
		`it_noti_qty`=0,
		`it_sc_type`=3,
		`it_sc_method`=0,
		`it_sc_price`=3500,
		`it_time`='".date("Y-m-d H:i:s")."',
		`it_update_time`='".date("Y-m-d H:i:s")."',
		`it_ip`='".$REMOTE_ADDR."',
		`it_img1`='1408523296/1122.jpg',
		`it_img2`='1408523296/111.jpg',
		`it_img3`='',
		`it_img4`='',
		`it_img5`='',
		`it_img6`='',
		`it_img7`='',
		`it_img8`='',
		`it_img9`='',
		`it_img10`='',
		`it_1_subj`='발행년도',
		`it_2_subj`='금속타입',
		`it_3_subj`='금속함량',
		`it_4_subj`='액면가',
		`it_5_subj`='지름,두께',
		`it_6_subj`='중량',
		`it_7_subj`='상태',
		`it_8_subj`='',
		`it_9_subj`='',
		`it_10_subj`='',
		`it_1`='2014',
		`it_2`='silver',
		`it_3`='999',
		`it_4`='$1 (AUD)',
		`it_5`='40.6mm',
		`it_6`='1 oz,t',
		`it_7`='Brilliant Uncirculated',
		`it_8`='',
		`it_9`='',
		`it_10`='',
		`it_year`='2014 Australia 1-oz Wedge Tailed Silver Eagle PCGS MS-69',
		";
		
		//sql_query($sql);
	}
	exit;
	*/
}

goto_url("./grouppurchaselist.php?sca=$sca&amp;sst=$sst&amp;sod=$sod&amp;sfl=$sfl&amp;stx=$stx&amp;page=$page");
?>
