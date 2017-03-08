<?php
include_once('./_common.php');
auth_check($auth[$sub_menu], "r");
auth_check($auth[$sub_menu], "w");

if($is_admin != 'super') exit;

// 상품이 많을 경우 대비 설정변경
// set_time_limit ( 0 );
// ini_set('memory_limit', '50M');

function only_number($n)
{
	return preg_replace('/[^0-9]/', '', $n);
}



if($_FILES['excelfile']['tmp_name']) {
	
	$file = $_FILES['excelfile']['tmp_name'];

	include_once(G5_LIB_PATH.'/Excel/reader.php');

	$data = new Spreadsheet_Excel_Reader();

	// Set output Encoding.
	$data->setOutputEncoding('UTF-8');

	/***
	* if you want you can change 'iconv' to mb_convert_encoding:
	* $data->setUTFEncoder('mb');
	*
	**/

	/***
	* By default rows & cols indeces start with 1
	* For change initial index use:
	* $data->setRowColOffset(0);
	*
	**/



	/***
	*  Some function for formatting output.
	* $data->setDefaultFormat('%.2f');
	* setDefaultFormat - set format for columns with unknown formatting
	*
	* $data->setColumnFormat(4, '%.3f');
	* setColumnFormat - set format for column (apply only to number fields)
	*
	**/

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
	$total_count = 0;
	$fail_count = 0;
	$succ_count = 0;

	for ($i = 3; $i <= $data->sheets[0]['numRows']; $i++) {
		$total_count++;

		$j = 1;

		$tr_date			= addslashes($data->sheets[0]['cells'][$i][1]);//1
		$tr_time			= addslashes($data->sheets[0]['cells'][$i][2]);//2
		$tr_type			= addslashes($data->sheets[0]['cells'][$i][3]);//3
		$output_price	= addslashes(only_number($data->sheets[0]['cells'][$i][4]));
		$input_price	= addslashes(only_number($data->sheets[0]['cells'][$i][5]));
		$trader_name	= addslashes($data->sheets[0]['cells'][$i][6]);
		$remain_money	= addslashes(only_number($data->sheets[0]['cells'][$i][7]));
		$bank		  		= addslashes($data->sheets[0]['cells'][$i][8]);
		
		if(!$tr_date || !$tr_time || ( !$input_price && !$output_price )) {
			$fail_count++;
			continue;
		}

		// it_id 중복체크
		$sql2 = " SELECT	count(*) as cnt
							FROM		bank_db
							WHERE		tr_date = '$tr_date'
							AND			tr_time = '$tr_time'
							AND			(	input_price = '$input_price'
												OR	output_price = '$output_price'
											)
							AND			trader_name = '$trader_name'							
		";
		$row2 = sql_fetch($sql2);
		if($row2['cnt']) {
			$fail_it_id[] = $it_id;
			$dup_it_id[] = $it_id;
			$dup_count++;
			$fail_count++;
			continue;
		}

		$sql = " INSERT INTO 	bank_db	SET
														tr_date = '$tr_date',						/*거래일*/
														tr_time = '$tr_time',						/*거래시간*/
														tr_type = '$tr_type',						/*거래수단*/
														output_price = '$output_price',	/*출금액*/
														input_price = '$input_price',		/*입금액*/
														trader_name = '$trader_name',		/*메모*/
														remain_money = '$remain_money',	/*잔액*/
														bank = '$bank',									/*거래은행*/
														admin_link = '$admin_link',			/*연결된 주문번호들*/
														admin_memo = '$admin_memo'			/*관리자 메모*/
		";
		sql_query($sql);
		
		$succ_count++;
	}
	
}

if($result) {
	echo "1";
}
else 
{
	echo "0";	
}
?>