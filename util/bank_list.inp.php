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



	
//우리은행 공구/지출 통장
if($_FILES['excelfile4']['tmp_name']) {
	$file = $_FILES['excelfile4']['tmp_name'];
	$계좌명 = '우리-통합통장';

	$data = new Spreadsheet_Excel_Reader();
	// Set output Encoding.
	$data->setOutputEncoding('UTF-8');
	$data->read($file);
	error_reporting(E_ALL ^ E_NOTICE);

	$dup_it_id = array();
	$fail_it_id = array();
	$dup_count = 0;
	$total_count = 0;
	$fail_count = 0;
	$succ_count = 0;

	for ($i = 5; $i <= $data->sheets[0]['numRows']; $i++) {
		$total_count++;

//	$번호					= addslashes($data->sheets[0]['cells'][$i][1]);	//1번은 번호, 사용안함
		$date = addslashes($data->sheets[0]['cells'][$i][2]);					//"2017.11.01 (14:28:36)"
		$date = str_replace(".","-",$date);		$date = str_replace(")","",$date);		$date = explode('(',$date);		
		$tr_date			= trim($date[0]);	//2-0
		$tr_time			= trim($date[1]);	//2-1
		
		$tr_type			= addslashes($data->sheets[0]['cells'][$i][3]);	
		$trader_name	= addslashes($data->sheets[0]['cells'][$i][4]);	//입금자명or출금내용
		$output_price	= addslashes($data->sheets[0]['cells'][$i][5]);	//출금액
		$input_price	= addslashes($data->sheets[0]['cells'][$i][6]);	//입금액
		$remain_money	= addslashes($data->sheets[0]['cells'][$i][7]);	//남은잔액
		$bank		  		= addslashes($data->sheets[0]['cells'][$i][8]);


		if(!$tr_date || !$tr_time) {	// || ( !$input_price && !$output_price )
			$fail_count++;
			continue;
		}

		//입출금내역 중복체크
		$sql2 = " SELECT	count(*) as cnt
							FROM		bank_db
							WHERE		tr_date = '$tr_date'
							AND			tr_time = '$tr_time'
							AND			remain_money = '$remain_money'
							AND			output_price = '$output_price'
							AND			input_price = '$input_price'
							AND			trader_name = '$trader_name'
			";
		$row2 = sql_fetch($sql2);
		echo $sql2."<br>";

		if($row2['cnt']) {
			$dup_count++;
			$fail_count++;

			/* 중복일경우 계좌명만 업데이트 */
			$upd_sql = "	UPDATE	bank_db		SET
																account_name = '$계좌명'
											WHERE		tr_date = '$tr_date'
											AND			tr_time = '$tr_time'
											AND			output_price = '$output_price'
											AND			input_price = '$input_price'
											AND			trader_name = '$trader_name'
				";
			sql_query($upd_sql);
			continue;
		}

		$sql = " INSERT INTO 	bank_db	SET
															account_name = '$계좌명',
															tr_date				= '$tr_date',				/*거래일*/
															tr_time				= '$tr_time',				/*거래시간*/
															tr_type				= '$tr_type',				/*거래수단*/
															output_price	= '$output_price',	/*출금액*/
															input_price		= '$input_price',		/*입금액*/
															trader_name		= '$trader_name',		/*메모*/
															remain_money	= '$remain_money',	/*잔액*/
															bank				= '$bank',						/*거래은행*/
															bank_type		= '$bank_type',				/*거래유형*/
															admin_link	= '$admin_link',			/*연결된 주문번호들*/
															admin_memo	= '$admin_memo'				/*관리자 메모*/
			";
		sql_query($sql);

		$succ_count++;
	}

}


//기존 신한은행 공구통장, 지출통장.  3번파일은 현금영수증후처리번호 갱신목적
for($z = 1; $z <= 3; $z++) {

	if($_FILES['excelfile'.$z]['tmp_name']) {
		$file = $_FILES['excelfile'.$z]['tmp_name'];

		/* 지출통장이면 유형을 B08로 변경 */
		if($z == 2) {
			$bank_type = 'B08';
			$계좌명 = '신한-지출통장';
		}
		else {
			$bank_type = null;
			$계좌명 = '신한-공구통장';
		}

		$data = new Spreadsheet_Excel_Reader();
		// Set output Encoding.
		$data->setOutputEncoding('UTF-8');
		$data->read($file);
		error_reporting(E_ALL ^ E_NOTICE);

		$dup_it_id = array();
		$fail_it_id = array();
		$dup_count = 0;
		$total_count = 0;
		$fail_count = 0;
		$succ_count = 0;

		for ($i = 8; $i <= $data->sheets[0]['numRows']; $i++) {
			$total_count++;

			$j = 1;

			$tr_date			= addslashes($data->sheets[0]['cells'][$i][1]);//1
			$tr_time			= addslashes($data->sheets[0]['cells'][$i][2]);//2
			$tr_type			= addslashes($data->sheets[0]['cells'][$i][3]);//3
//		$output_price	= addslashes(only_number($data->sheets[0]['cells'][$i][4]));
//		$input_price	= addslashes(only_number($data->sheets[0]['cells'][$i][5]));
			$output_price	= addslashes($data->sheets[0]['cells'][$i][4]);
			$input_price	= addslashes($data->sheets[0]['cells'][$i][5]);
			$trader_name	= addslashes($data->sheets[0]['cells'][$i][6]);
			$remain_money	= addslashes(only_number($data->sheets[0]['cells'][$i][7]));
			$bank		  		= addslashes($data->sheets[0]['cells'][$i][8]);


			if(!$tr_date || !$tr_time) {	// || ( !$input_price && !$output_price )
				$fail_count++;
				continue;
			}

			// it_id 중복체크
			$sql2 = " SELECT	count(*) as cnt
								FROM		bank_db
								WHERE		tr_date = '$tr_date'
								AND			tr_time = '$tr_time'
								AND			remain_money = '$remain_money'
								AND			output_price = '$output_price'
								AND			input_price = '$input_price'
								AND			trader_name = '$trader_name'
			";
			$row2 = sql_fetch($sql2);

			echo $sql2."<br>";

			if($row2['cnt']) {
				$dup_count++;
				$fail_count++;

				/* 중복일경우 계좌명만 업데이트 */
				$upd_sql = "	UPDATE	bank_db		SET
																account_name = '$계좌명'
											WHERE		tr_date = '$tr_date'
											AND			tr_time = '$tr_time'
											AND			output_price = '$output_price'
											AND			input_price = '$input_price'
											AND			trader_name = '$trader_name'
				";
				sql_query($upd_sql);
				continue;
			}

			$sql = " INSERT INTO 	bank_db	SET
															account_name = '$계좌명',
															tr_date				= '$tr_date',				/*거래일*/
															tr_time				= '$tr_time',				/*거래시간*/
															tr_type				= '$tr_type',				/*거래수단*/
															output_price	= '$output_price',	/*출금액*/
															input_price		= '$input_price',		/*입금액*/
															trader_name		= '$trader_name',		/*메모*/
															remain_money	= '$remain_money',	/*잔액*/
															bank				= '$bank',						/*거래은행*/
															bank_type		= '$bank_type',				/*거래유형*/
															admin_link	= '$admin_link',			/*연결된 주문번호들*/
															admin_memo	= '$admin_memo'				/*관리자 메모*/
			";
			sql_query($sql);

			$succ_count++;
		}

	} //if end
} //for z=1 end





if($result) {
	echo "<script>location.href='/util/bank_list.php';</script>";
}
else
{
	echo "0";
}
?>