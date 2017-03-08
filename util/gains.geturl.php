<?
include_once('./_common.php');

header("Content-Encoding: utf-8");


$curl = curl_init();
$site_url = "http://www.gainesvillecoins.com/api/updatedproducts";
echo $site_url."<br>";
$Response = get_httpRequest($site_url);


if($Response){
	$json = json_decode($Response);

	$신규상품목록 = explode(',',$json->New);
	$업데이트목록 = explode(',',$json->Updated);

	$배열[0] = $신규상품목록;
	$배열[1] = $업데이트목록;


	for($z = 0; $z < count($배열); $z++) {
		$상품목록 = $배열[$z];
		$갱신유형 = ($z == 0) ? '신규' : '갱신';

		if($상품목록[0] == '') continue;

		for($i=0; $i < count($상품목록); $i++) {
			$prd_url = "http://www.gainesvillecoins.com/api/products/".trim($상품목록[$i]);
			$prdResponse = get_httpRequest($prd_url);

			$prd = json_decode($prdResponse);

			if(stripos($prd->Message,"error")) {
				echo "API에러($prd_url)<br><br>";
				continue;
			}


			$gp_id = 'GV_'.$prd->ProductId;

			echo $갱신유형.$i.": $prd_url (http://coinstoday.co.kr/) <br>";

			$SKU_ID = $prd->Sku;
			$gp_name = str_replace("'", '', $prd->ProductName);
			$gp_name = str_replace('\"', "", $gp_name);
			$gp_name = str_replace('\'', "'", $gp_name);
			
			$jaego = $prd->Stock;
			$최대주문수량 = $prd->MaxQtyPerOrder;
			$gp_img = $prd->ImageUrl;
			$gp_metal_don = $prd->Melt;
			$gp_metal_type = ($prd->Metal == 'Gold') ? 'GL' : 'SL';

			$볼륨가 = $prd->PricingTiers;

			//수수료, 관부가세를 구하기 위해선 카테고리정보 필요
			$gp_charge = 8;
			$gp_duty = 0;

			unset($gpPricing);

			/* 단품가격 */
			if($볼륨가[0]->TierRange == '1+') {

				$gpPricing[0][po_sqty] = 0;
				$gpPricing[0][po_eqty] = 99999;
				$gpPricing[0][po_cash_price] = $볼륨가[0]->BankWirePrice;
				$gpPricing[0][po_card_price] = $볼륨가[0]->CreditCardPrice;

			}
			else {

				unset($옵션);
				unset($범위);

				for($p = 0; $p < count($볼륨가); $p++) {

					$분할문자 = ( ($p+1) == count($볼륨가) ) ? "+" : ' - ';	//마지막행 분할문자는 '+', 그외는 ' - '
					$범위 = explode($분할문자,$볼륨가[$p]->TierRange);

					$옵션[$p][po_sqty] = trim($범위[0]);

					//마지막행이면
					if( ($p+1) == count($볼륨가) ) {
						$옵션[$p][po_eqty] = 99999;
					}
					else { //마지막행이 아니면
						$옵션[$p][po_eqty] = trim($범위[1]);
					}

					$옵션[$p][po_cash_price] = $볼륨가[$p]->BankWirePrice;
					$옵션[$p][po_card_price] = $볼륨가[$p]->CreditCardPrice;

				}

				$gpPricing = $옵션;

			}


			//볼륨가격정보 입력
			/* 옵션 데이터 DELETE 후 INSERT */
			$del_sql = "DELETE	FROM	g5_shop_group_purchase_option 	WHERE		gp_id = '$gp_id'";
			if($mode == 'jhw') echo $del_sql."<br><br><br>";
			sql_query($del_sql);



			if($gpPricing) {

				for($x=0; $x < count($gpPricing); $x++){
					$ins_sql = "INSERT	INTO	g5_shop_group_purchase_option 	SET
														gp_id					= '$gp_id',
														po_num				= '$x',
														po_sqty				= '".$gpPricing[$x]['po_sqty']."',
														po_eqty 			= '".$gpPricing[$x]['po_eqty']."',
														po_cash_price = '".$gpPricing[$x]['po_cash_price']."',
														po_card_price = '".$gpPricing[$x]['po_card_price']."'
														,po_jaego			=	'$jaego'
					";
					sql_query($ins_sql);

					if($mode == 'jhw') echo $ins_sql."<br><br><br>";
				}
			}


			/* 상품가격 변동성 기록 */
// 			echo "count:".count($gpPricing)." > 0 && $jaego > 0";
			if( count($gpPricing) > 0 && $jaego > 0 ) {
				flowProductPriceSave($gp_id,$gpPricing,$jaego);
			}


			//정렬순서 갱신순서대로 하기 위한 값 조절
			$gp_order = ($gp_order >= 0) ? $gp_order : (-1 * mktime());

			//0은 신규상품목록

			if($z == 0) {
				$type = 'NEW';

				/* 이미 등록한 상품이 있을경우 갱신 */
				$find_sql = "	SELECT	*
											FROM		g5_shop_group_purchase
											WHERE		gp_id = '$gp_id'
				";
				$find = mysql_fetch_array(sql_query($find_sql));
				if($find) $type = 'UPDATE';

			} else {
				$type = 'UPDATE';
			}

			if($type == 'NEW') {
				$sql_common = "		ca_id		 				= 'GV',
													ca_id2					= '".$ca_id2."',
													ca_id3					= '".$ca_id3."',
													gp_site					= 'API',
													gp_name					= \"$gp_name\",
													gp_img					= '$gp_img',
													jaego						=	'$jaego',
													gp_objective_price	= '0',
													gp_metal_type		= '$gp_metal_type',
													gp_metal_don		= '$gp_metal_don',
													gp_use		  		= '1',
													gp_order				= '".$gp_order."',
													gp_type1				= '".$gp_type1."',
													gp_type2 				= '".$gp_type2."',
													gp_type3 				= '".$gp_type3."',
													gp_type4 				= '".$gp_type4."',
													gp_type5				= '".$gp_type5."',
													gp_charge				= '".$gp_charge."',
													gp_duty					= '".$gp_duty."',
													it_type					= '".$gp_type."',
													gp_sc_price			= '3500',
													gp_update_time	= '".G5_TIME_YMDHIS."',
													gp_time 				= '".G5_TIME_YMDHIS."'

				"; ///* gp_explan				= '".$gp_explan."', */

				$sql = " INSERT INTO {$g5['g5_shop_group_purchase_table']} SET gp_id = '$gp_id', $sql_common	";
			}
			else {
				$sql_common = "	ca_id2					= '".$ca_id2."',
												ca_id3					= '".$ca_id3."',
												gp_site					= '$prd_url',
												gp_name					= '$gp_name',
												gp_img					= '$gp_img',
												jaego						=	'$jaego',
												gp_objective_price	= '0',
												gp_metal_type		= '$gp_metal_type',
												gp_metal_don		= '$gp_metal_don',
												gp_use		  		= '1',
												gp_order				= '".$gp_order."',
												gp_type1				= '".$gp_type1."',
												gp_type2 				= '".$gp_type2."',
												gp_type3 				= '".$gp_type3."',
												gp_type4 				= '".$gp_type4."',
												gp_type5				= '".$gp_type5."',
												gp_charge				= '".$gp_charge."',
												gp_duty					= '".$gp_duty."',
												it_type					= '".$gp_type."',
												gp_sc_price			= '3500',
												gp_update_time	= '".G5_TIME_YMDHIS."'
				";
				/* gp_explan				= '".$gp_explan."',
				 * ca_id		 				= 'GV',		*/

				$sql = "	UPDATE {$g5['g5_shop_group_purchase_table']}	SET
															$sql_common
									WHERE		gp_id = '$gp_id'
									AND			ca_id LIKE 'GV%'
				";
			}
			sql_query($sql);
			db_log($sql,'g5_shop_group_purchase',"딜러업체상품 재고값 갱신 ");
			
			if($mode == 'jhw') echo $sql."<br><br><br>";

		} //for 상품목록

	} //for 배열

}


?>