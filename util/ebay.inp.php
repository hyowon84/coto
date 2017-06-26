<?php
include_once('./_common.php');


include_once(G5_LIB_PATH.'/Excel/reader.php');


// 상품이 많을 경우 대비 설정변경
// set_time_limit ( 0 );
// ini_set('memory_limit', '50M');

function only_number($n)
{
	return preg_replace('/\-*[^0-9]/', '', $n);
}


for($z = 1; $z <= 1; $z++) {

	if($_FILES['excelfile'.$z]['tmp_name']) {

		$file = $_FILES['excelfile'.$z]['tmp_name'];


		$data = new Spreadsheet_Excel_Reader();

		// Set output Encoding.
		$data->setOutputEncoding('UTF-8');

		$data->read($file);

		/*
		 $data->sheets[0]['numRows'] - count rows
		 $data->sheets[0]['numCols'] - count columns
		 $data->sheets[0]['cells'][$i][$j] - data from $i-row $j-column

		 $data->sheets[0]['cellsInfo'][$i][$j] - extended info about cell

			$data->sheets[0]['cellsInfo'][$i][$j]['type'] = "date" | "number" | "unknown"
				if 'type' == "unknown" - use 'raw' value, because  cell contain value with format '0.00';
			$data->sheets[0]['cellsInfo'][$i][$j]['raw'] = value if cell without format
			$data->sheets[0]['cellsInfo'][$i][$j]['colspan']
			$data->sheets[0]['cellsInfo'][$i][$j]['rowspan']
		*/

		error_reporting(E_ALL ^ E_NOTICE);

		$dup_it_id = array();
		$fail_it_id = array();
		$dup_count = 0;
		$fail_count = 0;
		$succ_count = 0;

		for ($i = 3; $i <= $data->sheets[0]['numRows']; $i++) {

			
			$sales_no	= addslashes($data->sheets[0]['cells'][$i][1]);
			$user_id = addslashes($data->sheets[0]['cells'][$i][2]);
			$name = addslashes($data->sheets[0]['cells'][$i][3]);
			$phone = addslashes($data->sheets[0]['cells'][$i][4]);
			$email = addslashes($data->sheets[0]['cells'][$i][5]);
			$addr1 = addslashes($data->sheets[0]['cells'][$i][6]);
			$addr2 = addslashes($data->sheets[0]['cells'][$i][7]);
			$city = addslashes($data->sheets[0]['cells'][$i][8]);
			$state = addslashes($data->sheets[0]['cells'][$i][9]);
			$zip = addslashes($data->sheets[0]['cells'][$i][10]);
			$country = addslashes($data->sheets[0]['cells'][$i][11]);
			
			$item_number = addslashes($data->sheets[0]['cells'][$i][12]);
			$item_name = addslashes($data->sheets[0]['cells'][$i][13]);
//		$label = addslashes($data->sheets[0]['cells'][$i][14]);
			$qty = addslashes($data->sheets[0]['cells'][$i][15]);
			$sale_price = addslashes($data->sheets[0]['cells'][$i][16]);
			
			$ship_fee = addslashes($data->sheets[0]['cells'][$i][17]);
			$us_tax = addslashes($data->sheets[0]['cells'][$i][18]);
			$insurance = addslashes($data->sheets[0]['cells'][$i][19]);
			$cash_delivery_fee = addslashes($data->sheets[0]['cells'][$i][20]);
			$total_price = addslashes($data->sheets[0]['cells'][$i][21]);
			$payment_method = addslashes($data->sheets[0]['cells'][$i][22]);
			$sale_date = addslashes($data->sheets[0]['cells'][$i][23]);
			$checkout_date = addslashes($data->sheets[0]['cells'][$i][24]);
			$paid_on_date = addslashes($data->sheets[0]['cells'][$i][25]);
			$shipped_on_date = addslashes($data->sheets[0]['cells'][$i][26]);
			$feedback_left = addslashes($data->sheets[0]['cells'][$i][27]);
			$feedback_received = addslashes($data->sheets[0]['cells'][$i][28]);
//		$notes_to_yourself = addslashes($data->sheets[0]['cells'][$i][29]);
//		$unique_product_id = addslashes($data->sheets[0]['cells'][$i][30]);
			$paypal_transaction_id = addslashes($data->sheets[0]['cells'][$i][35]);
			$shipping_service = addslashes($data->sheets[0]['cells'][$i][36]);
			$transaction_id = addslashes($data->sheets[0]['cells'][$i][37]);
			
			
			//sales_no가 없는 부분이 마지막 경계선이므로 브레이크
			if(!$sales_no || !$user_id || !$item_number) {
				$fail_count++;
				break;
			}

			
			$chk_sql = "	SELECT	*
										FROM		global_order_info GI
										WHERE		GI.sales_no = '$sales_no'
			";
			$prev_od = mysql_fetch_array(sql_query($chk_sql));
			if($prev_od[sales_no] > 0) continue;
			
			
			//주문ID 생성, 마이크로타임까지 계산
			include G5_PATH."/inc/makeOrderId.php";				
			
			
			/* 이베이 주문정보 원본 레코드 기록 */
			$sql = " INSERT INTO 	global_order_info		SET
 															od_id = '$od_id',
															sales_no = '$sales_no',							/*해외(이베이) 판매순번*/
															user_id = '$user_id',					/*해외 주문자 ID*/
															name = '$name',								/*주문자 이름*/
															phone = '$phone',							/*폰번호*/
															email = '$email',							/*이메일주소*/
															addr1 = '$addr1',							/*주소1*/
															addr2 = '$addr2',							/*주소2*/
															city = '$city',								/*도시*/
															state = '$state',							/*주*/
															zip = '$zip',									/*우편번호(5자리)*/
															country = '$country',					/*입출금내역 연결번호*/
															item_number = '$item_number',	
															item_name = '$item_name',	
															qty = '$qty',	
															sale_price = '$sale_price',	
															shipping_fee = '$shipping_fee',				/*수송,운송 및 출하비용*/
															us_tax = '$us_tax',										/*미국 관세*/
															insurance = '$insurance',							/*보험금액*/
															cash_delivery_fee = '$cash_delivery_fee',	/*배송비*/
															total_price = '$total_price',	
															payment_method = '$payment_method',	
															sale_date = '$sale_date',							/*입금자명*/
															checkout_date = '$checkout_date',	
															paid_on_date = '$paid_on_date',	
															shipped_on_date = '$shipped_on_date',	
															feedback_left = '$feedback_left',	
															feedback_received = '$feedback_received',	
															paypal_transaction_id = '$paypal_transaction_id',	
															shipping_service = '$shipping_service',	
															transaction_id = '$transaction_id'	
			";
			sql_query($sql);

			$도시주국가 = "$city, $state, $country";
			
			$chk_sql = "	SELECT	*
										FROM		g5_shop_group_purchase GP
										WHERE		GP.ebay_id = '$item_number'
			";
			$item = mysql_fetch_array(sql_query($chk_sql));
			
			
			/*품목코드 기록*/
			$ins_sql = "	INSERT	INTO 	clay_order		SET
																gpcode = 'EBAY',
																od_id = '$od_id',
																it_id = '$item[it_id]',
																it_name = \"$item[gp_name]\",
																it_qty	=	'$qty',
																it_org_price = '$sale_price',
																stats = '00',
																clay_id = '$name',
																mb_id	= '',
																name = '$name',
																hphone = '$phone',
																od_date = now()
			";
			sql_query($ins_sql);

			$ins_sql = "	INSERT	INTO 	clay_order_info		SET
																gpcode = 'EBAY',
																od_id = '$od_id',
																clay_id = '$name',
																mb_id = '',
																name = '$name',
																receipt_name = 'PAYPAL',
																hphone = '$phone',
																zip = '$zip',
																addr1 = '$addr2',
																addr1_2 = '$addr1',
																addr2 = '$도시주국가',
																memo = '$memo',
																cash_receipt_yn = '',
																cash_receipt_type = '',
																cash_receipt_info = '',
																delivery_type = '',
																delivery_price= '',
																delivery_direct= 'N',
																delivery_invoice = '',
																od_ip = '',
																od_browser = '',
																od_date = now()
			";
			$result = sql_query($ins_sql);
			
			
			$succ_count++;
		}

	} //if end
} //for z=1 end



if($result) {
	echo "<script>location.href='/util/ebay.php';</script>";
}
else
{
	echo "0";
}
?>