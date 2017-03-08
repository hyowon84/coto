<?php
include_once('./_common.php');
auth_check($auth[$sub_menu], "r");


//스트립슬래시를 안하면 json_decode가 안됨
$arr = json_decode(str_replace('\"','"',stripslashes( iconv('utf-8', 'cp949', $_POST['data'] ) )),true);
$mb_id = $member[mb_id];



/* 단일레코드일때 */
if( strlen($arr[no]) >= 1 ) {

	$no = $arr[no];
	$GL_G_BUY = $arr[GL_G_BUY];
	$GL_G_SELL = $arr[GL_G_SELL];
	$GL_OZ_BUY = $arr[GL_OZ_BUY];
	$GL_OZ_SELL = $arr[GL_OZ_SELL];
	$GL_DON_BUY = $arr[GL_DON_BUY];
	$GL_DON_SELL = $arr[GL_DON_SELL];
	$GL_18K_BUY = $arr[GL_18K_BUY];
	$GL_18K_SELL = $arr[GL_18K_SELL];
	$GL_14K_BUY = $arr[GL_14K_BUY];
	$GL_14K_SELL = $arr[GL_14K_SELL];
	$SL_G_BUY = $arr[SL_G_BUY];
	$SL_G_SELL = $arr[SL_G_SELL];
	$SL_OZ_BUY = $arr[SL_OZ_BUY];
	$SL_OZ_SELL = $arr[SL_OZ_SELL];
	$PT_G_BUY = $arr[PT_G_BUY];
	$PT_G_SELL = $arr[PT_G_SELL];
	$PD_G_BUY = $arr[PD_G_BUY];
	$PD_G_SELL = $arr[PD_G_SELL];


	/* 상품정보 수정 */
	$common_sql = "	UPDATE	g5_domestic_price	SET
																GL_G_BUY = '$GL_G_BUY',					/*금(1g) 살때*/
																GL_G_SELL = '$GL_G_SELL',				/*금(1g) 팔때*/
																GL_OZ_BUY = '$GL_OZ_BUY',				/*금(1oz) 살때*/
																GL_OZ_SELL = '$GL_OZ_SELL',			/*금(1oz) 팔때*/
																GL_DON_BUY = '$GL_DON_BUY',			/*금(1돈) 살때*/
																GL_DON_SELL = '$GL_DON_SELL',		/*금(1돈) 팔때*/
																GL_18K_BUY = '$GL_18K_BUY',			/*금(18k) 살때*/
																GL_18K_SELL = '$GL_18K_SELL',		/*금(18k) 팔때 */
																GL_14K_BUY = '$GL_14K_BUY',			/*금(14k) 살때*/
																GL_14K_SELL = '$GL_14K_SELL',		/*금(14k) 팔때*/
																SL_G_BUY = '$SL_G_BUY',					/*은(1g) 살때*/
																SL_G_SELL = '$SL_G_SELL',				/*은(1g) 팔때*/
																SL_OZ_BUY = '$SL_OZ_BUY',				/*은(1oz) 살때*/
																SL_OZ_SELL = '$SL_OZ_SELL',			/*은(1oz) 팔때*/
																PT_G_BUY = '$PT_G_BUY',					/*백금(1g) 살때*/
																PT_G_SELL = '$PT_G_SELL',				/*백금(1g) 팔때*/
																PD_G_BUY = '$PD_G_BUY',					/*팔라듐(1g) 살때*/
																PD_G_SELL = '$PD_G_SELL'				/*팔라듐(1g) 팔때*/
									WHERE		no = '1'
	";
	sql_query($common_sql);

}
else {	/* 복수레코드일때 */

	for($i = 0; $i < count($arr); $i++) {
		$grid = $arr[$i];
		$no = $grid[no];
		$GL_G_BUY = $grid[GL_G_BUY];
		$GL_G_SELL = $grid[GL_G_SELL];
		$GL_OZ_BUY = $grid[GL_OZ_BUY];
		$GL_OZ_SELL = $grid[GL_OZ_SELL];
		$GL_DON_BUY = $grid[GL_DON_BUY];
		$GL_DON_SELL = $grid[GL_DON_SELL];
		$GL_18K_BUY = $grid[GL_18K_BUY];
		$GL_18K_SELL = $grid[GL_18K_SELL];
		$GL_14K_BUY = $grid[GL_14K_BUY];
		$GL_14K_SELL = $grid[GL_14K_SELL];
		$SL_G_BUY = $grid[SL_G_BUY];
		$SL_G_SELL = $grid[SL_G_SELL];
		$SL_OZ_BUY = $grid[SL_OZ_BUY];
		$SL_OZ_SELL = $grid[SL_OZ_SELL];
		$PT_G_BUY = $grid[PT_G_BUY];
		$PT_G_SELL = $grid[PT_G_SELL];
		$PD_G_BUY = $grid[PD_G_BUY];
		$PD_G_SELL = $grid[PD_G_SELL];


		/* 상품정보 수정 */
		$common_sql = "	UPDATE	g5_domestic_price	SET
																GL_G_BUY = '$GL_G_BUY',					/*금(1g) 살때*/
																GL_G_SELL = '$GL_G_SELL',				/*금(1g) 팔때*/
																GL_OZ_BUY = '$GL_OZ_BUY',				/*금(1oz) 살때*/
																GL_OZ_SELL = '$GL_OZ_SELL',			/*금(1oz) 팔때*/
																GL_DON_BUY = '$GL_DON_BUY',			/*금(1돈) 살때*/
																GL_DON_SELL = '$GL_DON_SELL',		/*금(1돈) 팔때*/
																GL_18K_BUY = '$GL_18K_BUY',			/*금(18k) 살때*/
																GL_18K_SELL = '$GL_18K_SELL',		/*금(18k) 팔때 */
																GL_14K_BUY = '$GL_14K_BUY',			/*금(14k) 살때*/
																GL_14K_SELL = '$GL_14K_SELL',		/*금(14k) 팔때*/
																SL_G_BUY = '$SL_G_BUY',					/*은(1g) 살때*/
																SL_G_SELL = '$SL_G_SELL',				/*은(1g) 팔때*/
																SL_OZ_BUY = '$SL_OZ_BUY',				/*은(1oz) 살때*/
																SL_OZ_SELL = '$SL_OZ_SELL',			/*은(1oz) 팔때*/
																PT_G_BUY = '$PT_G_BUY',					/*백금(1g) 살때*/
																PT_G_SELL = '$PT_G_SELL',				/*백금(1g) 팔때*/
																PD_G_BUY = '$PD_G_BUY',					/*팔라듐(1g) 살때*/
																PD_G_SELL = '$PD_G_SELL'				/*팔라듐(1g) 팔때*/
										WHERE		no = '1'
		";
		sql_query($common_sql);

	}
}


if($result) {
	$json[success] = "true";
	$json[message] = '수정되었습니다';
} else {
	$json[success] = "false";
	$json[message] = '수정되지 않았습니다. 관리자에게 문의바랍니다.';
}

$json_data = json_encode_unicode($json);
echo $json_data;
?>