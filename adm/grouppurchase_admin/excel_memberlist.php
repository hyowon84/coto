<?php
$sub_menu = '200100';

include_once('./_common.php');
include_once(G5_PATH.'/adm/shop_admin/admin.shop.lib.php');


/*================================================================================
php_writeexcel http://www.bettina-attack.de/jonny/view.php/projects/php_writeexcel/
=================================================================================*/

include_once(G5_LIB_PATH.'/Excel/php_writeexcel/class.writeexcel_workbook.inc.php');
include_once(G5_LIB_PATH.'/Excel/php_writeexcel/class.writeexcel_worksheet.inc.php');

if($mode == 'excel') {
	
	$fname = tempnam(G5_DATA_PATH, "tmp-memberlist.xls");
	$workbook = new writeexcel_workbook($fname);
	$worksheet = $workbook->addworksheet();

	// Put Excel data
	$data = array('수취인명', '배송메세지', '수취인우편번호', '주소.1', '수취인 e-mail 주소(현영)', '수취인이동통신', '상품명', '수량', '요금구분코드');
	$data = array_map('iconv_euckr', $data);
	
	$col = 0;
	foreach($data as $cell) {
		$worksheet->write(0, $col++, $cell);
	}
	
	
	//화면에서 입력한 회원ID 목록을 구함
	$arr_mb_id = explode(PHP_EOL, $_POST['list_mb_id']);		//이메일주소
	$arr_mb_nick = explode(PHP_EOL, $_POST['list_mb_nick']);	//닉네임
	
// 	$arr_mb_id = array_map('iconv_euckr', $arr_mb_id);
// 	$arr_mb_nick = array_map('iconv_euckr', $arr_mb_nick);
	
	$신청자목록 = array_merge($arr_mb_id,$arr_mb_nick);
	
	for ($i = 0, $iCount=1; $i < count($신청자목록); $i++)
	{
// 		if(!$신청자목록[$i]) continue;

		// 실제 번호를 넘김
		$mb_id = trim($신청자목록[$i]);
		
		if(!$mb_id) continue;
		
		$sql = " select * from {$g5['member_table']} where	mb_id = '$mb_id'	OR	mb_nick = '$mb_id'	";
		$result = sql_query($sql);
		$mb = mysql_fetch_array($result);

// 		echo $sql;
// 		echo "<br>";
// 		print_r($mb);
// 		echo "<br>";
		
		
		$수취인명 = iconv("utf-8","euc-kr",$mb['mb_name']);
		$배송메시지 = iconv("utf-8","euc-kr",$mb['mb_nick']);
		$주소지 = iconv("utf-8","euc-kr",$mb['mb_addr1'].' '.$mb['mb_addr2']);
		$it_name = iconv("utf-8","euc-kr","기념품");
		$it_state = iconv("utf-8","euc-kr","즉납");
		
		
		$worksheet->write($iCount, 0, $수취인명);
		$worksheet->write($iCount, 1, $배송메시지);
		$worksheet->write($iCount, 2, $mb['mb_zip1'].'-'.$mb['mb_zip2']);
		$worksheet->write($iCount, 3, $주소지);
		$worksheet->write($iCount, 4, ' '.$mb['mb_hp']);
		$worksheet->write($iCount, 5, ' '.$mb['mb_hp']);
		$worksheet->write($iCount, 6, $it_name);
		$worksheet->write($iCount, 7, 3);
		$worksheet->write($iCount, 8, $it_state);
		
		$iCount++;
	}
	
	
	$workbook->close();
	if($iCount==1)alert("자료가 존재하지 않습니다.");
	else{
		
		header("Content-Type: application/x-msexcel; name=\"memberlist-".date("ymd", time()).".xls\"");
		header("Content-Disposition: inline; filename=\"memberlist-".date("ymd", time()).".xls\"");
		$fh=fopen($fname, "rb");
		fpassthru($fh);
		unlink($fname);
	
		exit;
	}
	
	
	
} else {	/* if ( $mode == excel ) end if */

	include_once (G5_ADMIN_PATH.'/admin.head.php');
?>



<style>
.deputy_th { width:30%; padding:10px; }
.deputy_td { width:70%; padding:10px; }
</style>

<form action='<?=$PHP_SELF?>' method='post'>
<input type='hidden' name='mode' value='excel'> 



<table align='center' style='width:800px;'>
	<tr>
		<td>주소록 우체국 추출</td>
	</tr>
	<tr>
		<td class='deputy_th'>신청자 닉네임</th>
		<td class='deputy_td'>
			<textarea name='list_mb_nick' style='height:100px;'><?=$list_mb_nick?></textarea>
		</td>
	</tr>
	<tr>
		<td class='deputy_th'>신청자ID(이메일주소)</th>
		<td class='deputy_td'>
		<textarea name='list_mb_id' style='height:100px;'><?=$list_mb_id?></textarea></td>
	</tr>
	<tr>
		<td colspan='2' align='center' style='padding:10px;'><input type='submit' value='추출'></td>
	</tr>
</table>
</form>

<?
	include_once (G5_ADMIN_PATH.'/admin.tail.php');	
}	//else if end
?>