<?php
include_once('./_common.php');
include_once(G5_MSHOP_PATH.'/head.php');
global $is_admin;



if($mode == 'update') {
	include_once(G5_LIB_PATH.'/Excel/reader.php');


	// 상품이 많을 경우 대비 설정변경
	// set_time_limit ( 0 );
	// ini_set('memory_limit', '50M');

	function only_number($n)
	{
		return preg_replace('/[^0-9]/', '', $n);
	}

	if($_FILES['excelfile']['tmp_name']) {

		$file = $_FILES['excelfile']['tmp_name'];

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

		for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
			$total_count++;

			$j = 1;

			$gp_id				= addslashes($data->sheets[0]['cells'][$i][1]);//1
			$gp_price			= addslashes($data->sheets[0]['cells'][$i][2]);//2
			$gp_price_org	= addslashes($data->sheets[0]['cells'][$i][3]);//3

			if( ($gp_id < 1) && ($gp_price < 1) ) {
				$fail_count++;
				continue;
			}

			$sql = "	UPDATE	g5_shop_group_purchase	SET
													gp_price = '$gp_price',
													gp_price_org = '$gp_price_org'
								WHERE		gp_id = '$gp_id'
								AND			gp_price = 0
			";
			sql_query($sql);
			echo $sql;
			echo "<br>";

			$succ_count++;
		}

	} //if end

}//if ($mode == update) end
?>

<script src="http://malsup.github.com/jquery.form.js"></script>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">



<style>
/* 다이얼로그 스타일 */
.bank_dialog table tr th { padding:0px; text-align:left; height:25px; }
.bank_dialog table tr td { padding-left:10px; border:1px #d1dee2; }
.bank_dialog table tr td input { border:1px solid #EAEAEA; }

#divBankTableArea table tr th, #divBankTableArea table tr td{
	text-align:center; border:1px solid #d1dee2;
}


#divBankTableArea table tr th { height:25px; background-color:#EEEEEE; }
#divBankTableArea table tr td { height:25px; }


#divBankDtlTableArea { width:30%; float:left; margin:10px; }
#divBankDtlTableArea table tr th { height:25px; background-color:#fbffd7; }
/* #divBankDtlTableArea table { width:50%; } */

.bank_tr:hover { background-color:#f1fbff; cursor:pointer; }
.bank_dtl_tr:hover { background-color:#fcffe4; cursor:pointer; }

.DetailOn { display:''; }
.DetailOff { display:none; }

.banklist_inp_text { border:1px solid #d1dee2; width:90%; }

.yellow { background-color:#fffcda; }
</style>

<!-- AJAX DB업데이트 -->
<form id='bank_form' name='bank_form' method="post" action="prd_price.php" enctype="MULTIPART/FORM-DATA" autocomplete="off">
<input type='hidden' name='mode' value='update' />

<table id='bank_excel_tb' border='0'>
	<tr>
		<th>상품</th>
		<td><input type='file' name='excelfile' /></td>
	</tr>
	<tr>
		<td colspan='2' height='40' align='center'>
			<input type="submit" value="입력" />
		</td>
	</tr>
</table>

</form>


<div>
	<?php
		include_once(G5_MSHOP_PATH . '/tail.php');
	?>
</div>