<?php
include_once('./_common.php');
include_once(G5_MSHOP_PATH.'/head.php');
global $is_admin;

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
<form id='bank_form' name='bank_form' method="post" action="bank_list.inp.php" enctype="MULTIPART/FORM-DATA" autocomplete="off">

<table id='bank_excel_tb' border='0'>
	<tr>
		<th>(신)공구통장</th>
		<td><input type='file' name='excelfile3' /></td>
	</tr>
	<tr>
		<th>지출통장</th>
		<td><input type='file' name='excelfile2' /></td>
	</tr>

	<tr>
		<th>-</th>
		<td>-</td>
	</tr>
	
	<tr>
		<th>(구)공구통장</th>
		<td><input type='file' name='excelfile1' /></td>
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