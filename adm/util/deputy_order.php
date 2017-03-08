<?
$sub_menu = '510100';
// $sub_sub_menu = '3';

include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$g5['title'] = '대리주문 대량등록';
include_once (G5_ADMIN_PATH.'/admin.head.php');
include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');


//엑셀입력
if($mode == 'input')
{

	include_once(G5_LIB_PATH.'/Excel/reader.php');

	// 상품이 많을 경우 대비 설정변경
	// set_time_limit ( 0 );
	// ini_set('memory_limit', '50M');

	function only_number($n)
	{
		return preg_replace('/[^0-9]/', '', $n);
	}

	echo "input모드 : ";

	if($_FILES['excelfile']['tmp_name']) {
		echo "파일존재 : ";


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

		$배열 = array();
		$대리주문 = array();

		########################/* step1. 엑셀 데이터를 배열화 시키기 */###########################
		for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
			$total_count++;

			$clay_id = addslashes($data->sheets[0]['cells'][$i][1]);//1
			$it_id = addslashes($data->sheets[0]['cells'][$i][2]);//2
			$it_qty = addslashes($data->sheets[0]['cells'][$i][3]);//3
			$buyer_memo = addslashes($data->sheets[0]['cells'][$i][4]);//4
			$admin_memo = addslashes($data->sheets[0]['cells'][$i][5]);//5
			$delivery_type = addslashes($data->sheets[0]['cells'][$i][6]);//6

			if( !$clay_id || !$it_id || !$it_qty ) {
				$fail_count++;
				continue;
			}

			$row = array();
			$row[clay_id] = $clay_id;
			$row[it_id] = $it_id;
			$row[it_qty] = $it_qty;
			$row[buyer_memo] = $buyer_memo;
			$row[admin_memo] = $admin_memo;
			$row[delivery_type] = $delivery_type;

			$배열[$clay_id][] = $row;
		}

		/* 현재 진행중인 공구정보 로딩 */
		$gpinfo_sql = "	SELECT	*
										FROM		gp_info
										WHERE		gpcode = '$gpcode'
		";
		$공구정보 = sql_fetch($gpinfo_sql);

		/* 공구정보가 존재할경우 */
		if($공구정보[gpcode]) {
			$시작일 = $공구정보[start_date];
			$종료일 = $공구정보[end_date];
			$배송비 = $공구정보[baesongbi];
			$볼륨가적용 = $공구정보[volprice_yn];
			$it_id = $공구정보[links];
		}


		###############################/* step2. 주문 데이터 생성 */#############################
		foreach ($배열 as $clay_id => $주문정보) {

			/* 옵션 고유ID 생성 SQL    by. JHW */
			$seq_sql = "	SELECT	CONCAT(	'CL',
																		DATE_FORMAT(now(),'%Y%m%d'),
																		LPAD(COALESCE(	(	SELECT	MAX(SUBSTR(od_id,11,4))
																											FROM		clay_order_info
																											WHERE		od_id LIKE CONCAT('%',DATE_FORMAT(now(),'%Y%m%d'),'%')
																											ORDER BY od_id DESC
																										)
																		,'0000') +1,4,'0')
														)	AS oid
										FROM		DUAL
			";
			list($od_id) = mysql_fetch_array(sql_query($seq_sql));


			if($od_id == $이전od_id) {
				echo "현재주문번호($od_id) == 이전주문번호($이전od_id) 중복! 소스 또는 엑셀폼 문제\r\n";
				exit;
			}


			$대리주문[] = $od_id;

			$succ_cnt = $fail_cnt = 0;


			/* 회원정보 */
			// 이전 주문정보에서 회원정보 추출.
			$mem_sql = "SELECT	CNT.MB_CNT,
													CI.*,
													MB.*
									FROM		clay_order_info CI
													LEFT JOIN g5_member MB ON (MB.mb_nick = CI.clay_id AND MB.mb_hp = CI.hphone AND MB.mb_leave_date = '')
													LEFT JOIN (	SELECT	T.clay_id,
																							COUNT(*) AS MB_CNT
																			FROM		(	SELECT	DISTINCT
																												CI.clay_id,
																												CI.name
																								FROM		clay_order_info CI
																							) T
																			GROUP BY T.clay_id
													) CNT ON (CNT.clay_id = CI.clay_id)
									WHERE		MB.mb_nick = '$clay_id'
									ORDER BY CI.od_date DESC
									LIMIT 1
			";
			$member = mysql_fetch_array(sql_query($mem_sql));

			if($member[MB_CNT] > 1) {
				$check .= $member[mb_nick]."(".$member[MB_CNT]."), ";
			}

			/* 기준데이터는 이전 주문정보, 이전주문정보가 없을경우 회원정보 */
			$mb_id = $member[mb_id];
			$name = ($member[name]) ? $member[name] : $member[mb_name];
			$receipt_name = ($member[receipt_name]) ? $member[receipt_name] : $name;
			$hphone = (strlen($member[hphone]) > 3) ? $member[hphone] : $member[mb_hp];
			$zip = (strlen($member[zip]) > 3) ? $member[zip] : ($member[mb_zip1].$member[mb_zip2]);
			$addr1 = (strlen($member[addr1]) > 3) ? $member[addr1] : $member[mb_addr_jibeon];	//지번
			$addr1_2 = (strlen($member[addr1_2]) > 3) ? $member[addr1_2] : $member[mb_addr1];	//도로명
			$addr2 = (strlen($member[addr2]) > 3) ? $member[addr2] : $member[mb_addr2];	//상세주소

			$memo = $구매자메모;
			$admin_memo = '대리주문 : '.$관리자메모;

			$cash_receipt_yn = $member[cash_receipt_yn];
			$cash_receipt_type = $member[cash_receipt_type];
			$cash_receipt_info = $member[cash_receipt_info];


			$delivery_direct= 'N';



			############################/* 전체신청상품 주문서 작성 */################################
			for($i = 0; $i < count($주문정보); $i++) {

				$it_id = $주문정보[$i][it_id];
				$신청수량 = $주문정보[$i][it_qty];
				$배송방법 = $주문정보[$i][delivery_type];
				$구매자메모 = $주문정보[$i][buyer_memo];
				$관리자메모 = $주문정보[$i][admin_memo];

				if($신청수량 == 0) continue;

				/* 상품정보 */
				$it_sql = "	SELECT	gp_name,
														gp_price,
														jaego,
														gp_have_qty,
														gp_sc_price
										FROM		g5_shop_group_purchase
										WHERE		gp_id = '$it_id'
				";
				$상품정보 = mysql_fetch_array(sql_query($it_sql));
				$it_name = $상품정보[gp_name];


				/* 신청수량 */
				$clay_sql = "	SELECT	IFNULL(SUM(it_qty),0) AS total_qty
											FROM		clay_order
											WHERE		it_id = '$it_id'
											AND			stats NOT IN (99)	/* 취소건 제외 */
				";
				$신청수량정보 = mysql_fetch_array(sql_query($clay_sql));
				$현재공구총신청수량 = $신청수량정보[total_qty];

				/* 코투 보유분이 우선순위(설정되어있는게 우선적으로 적용) */
				$상품재고 = ($상품정보[gp_have_qty] > 0) ? $상품정보[gp_have_qty]*1 : $상품정보[jaego]*1;
				$남은수량 = $상품재고 - $현재공구총신청수량;
				$남은수량 = ($남은수량 > 0) ? $남은수량 : 0;
				//$신청가능수량 = $상품정보[it_buy_max_qty];


				/* 실시간형 or 고정형 */
				/* 신청수량에 따른 볼륨프라이싱 가격 */
				if($공구정보[volprice_yn] == 'Y'){
					$price_sql = "	SELECT	PO.	gp_id,
																	PO.	po_num,
																	PO.	po_sqty,	/*최소신청수량*/
																	PO.	po_eqty,	/*최대신청수량*/
																	PO.	po_cash_price,
																	PO.	po_card_price,
																	PO.	po_add_price,
																	PO.	po_jaego	/*단품상품을 위한 재고정보 기입*/
													FROM		g5_shop_group_purchase_option PO
													WHERE		PO.gp_id = '$it_id'
													AND			PO.po_sqty <= '$현재공구총신청수량'
													AND			PO.po_eqty >= '$현재공구총신청수량'
					";
					$price = mysql_fetch_array(sql_query($price_sql));

					$신청당시상품가격 = getExchangeRate($price[po_cash_price],$it_id);
				}	/* 고정가격, 미리 원화설정된 금액 */
				else {
					$신청당시상품가격 = $상품정보[gp_price];
				}


				if($신청수량 > $남은수량 && $is_admin != 'super') {
					$경고메시지 .= "{$상품정보[gp_name]} 상품의 경우 고객님의 신청수량({$신청수량}개)이 남은수량(현재 {$남은수량}개)을 초과하였습니다. 새로고침후 다시 주문해주세요";
					echo "
					<script>
					alert($경고메시지);
					history.go(-1);
					</script>";
					exit;
				}
				else {
					$주문금액 += ($신청당시상품가격 * $신청수량);

					/* 볼륨가적용일땐 주문신청(00), 볼륨가적용안함일땐 주문금액문자 전송되니 입금요청 상태로 변경*/
					$기본주문상태 = '00';

					$it_name = str_replace("'", "", $it_name);
					$ins_sql = "	INSERT	INTO 	clay_order		SET
																		gpcode = '$gpcode',
																		od_id = '$od_id',
																		clay_id = '$clay_id',
																		mb_id = '$mb_id',
																		name = '$name',
																		hphone = '$hphone',
																		it_id = '$it_id',
																		it_name = '$it_name',
																		it_qty	=	'$신청수량',
																		it_org_price = '$신청당시상품가격',
																		stats = '$기본주문상태',
																		od_date = now()
					";
					$result = sql_query($ins_sql);

				}


				if($result) {
					$succ_cnt++;
					$성공메시지 .= "{$상품정보[gp_name]} 상품(수량 {$신청수량}개)의 주문신청이 정상처리되었습니다\\n";
				} else {
					$fail_cnt++;
					$경고메시지 .= "{$상품정보[gp_name]} 상품(수량 {$신청수량}개)의 주문신청이 실패하였습니다\\n";
				}

			} //for end


			$delivery_price = 0;

			switch ($배송방법) {
				case '무료':
					$delivery_type = 'D00';
					break;
				case '선불':
					$delivery_type = 'D01';
					$delivery_price = $배송비;
					break;
				case '착불':
					$delivery_type = 'D02';
					break;
				case '방문':
					$delivery_type = 'D03';
					break;
				case '통합':
					$delivery_type = 'D04';
					break;
				case '배달':
					$delivery_type = 'D05';
					$delivery_direct= 'Y';
					break;
				default:
					$delivery_type = 'D01';
					$delivery_price = $배송비;
					break;
			}


			/* 연말 코투세일 10만원 이상 구매자 배송비 무료 */
			if($주문금액 > 100000 && $gpcode == 'E201512_09') $delivery_price = 0;

			/* 배송비 관련 계산 */
			if($delivery_type == 'D01') {
				/* 계산공식 추가 필요, 3kg당 5,000원 추가 */
				//$배송비 = ($배송비 > 0) ? $배송비 : $it[gp_sc_price];
				$주문금액 += $배송비;
			}

			$ins_sql = "	INSERT	INTO 	clay_order_info		SET
																gpcode = '$gpcode',
																od_id = '$od_id',
																clay_id = '$clay_id',
																mb_id = '$mb_id',
																name = '$name',
																receipt_name = '$receipt_name',
																hphone = '$hphone',
																zip = '$zip',
																addr1 = '$addr1',
																addr1_2 = '$addr1_2',
																addr2 = '$addr2',
																memo = '$memo',
																admin_memo = '$admin_memo',
																cash_receipt_yn = '$cash_receipt_yn',
																cash_receipt_type = '$cash_receipt_type',
																cash_receipt_info = '$cash_receipt_info',
																delivery_type = '$delivery_type',
																delivery_price= '$delivery_price',
																delivery_direct= 'N',
																od_date = now()
			";
			$result = sql_query($ins_sql);

			$이전od_id = $od_id;
		} // foreach end

	} //if end

}



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
<form id='bank_form' name='bank_form' method="post" action="deputy_order.php?mode=input" enctype="MULTIPART/FORM-DATA" autocomplete="off">

<table width='400' id='bank_excel_tb' border='0' align='center'>
	<tr>
		<td height='30'>공동구매코드</td>
		<td align='center'>
			<select name='gpcode'>
			<?
				$select_sql = "	SELECT	*
												FROM		gp_info GI
												WHERE		GI.stats NOT IN ('FINISH')
												ORDER BY GI.reg_date DESC
				";
				$result = sql_query($select_sql);

				while($row = mysql_fetch_array($result)) {
			?>
				<option value='<?=$row[gpcode]?>'><?=$row[gpcode_name]?></option>
			<?
				}
			?>
			</select>
		</td>
	</tr>
	<tr>
		<td height='30'>대리주문 엑셀파일</td>
		<td align='center'>
			<input type='file' name='excelfile' /><br>
			<a href='sample.xls'><u>샘플양식 다운</u></a>
		</td>
	</tr>
	<tr>
		<td colspan='2' height='40' align='center'>
			<input type="submit" value="입력" />
		</td>
	</tr>
</table>

<style>
#order_result table tr th{
	text-align:center;
	height:20px;
}
#order_result table tr td{
	height:20px;
	padding:5px;
}


</style>

<div id='order_result' style='text-align:center;'>
<?=$check?>

<?
//엑셀 입력 데이터가 있을경우
if(count($대리주문) > 0) {
	echo "<table width='1200' align='center'>";

	for($i = 0; $i < count($대리주문); $i++) {
		$od_id = $대리주문[$i];


		$order_html .= "<tr>
						<th width='260'>주문번호</th>
						<th width='130'>닉네임</th>
						<th width='80'>성함</th>
						<th width='160'>상품코드</th>
						<th width='400'>상품명</th>
						<th width='60'>상품수량</th>
						<th width='130'>상품단가</th>
						<th width='200'>상품신청금액</th>
					</tr>";

		$od_sql = "	SELECT	CL.*,
												CI.*
								FROM		clay_order CL
												LEFT JOIN clay_order_info CI ON (CI.gpcode = CL.gpcode AND CI.od_id = CL.od_id)
								WHERE		CL.od_id = '$od_id'
		";
		$result = sql_query($od_sql);
		$개인별신청금액합계 = 0;

		while($row = mysql_fetch_array($result)) {
			$상품신청가격 = $row[it_org_price] * $row[it_qty];
			$개인별신청금액합계 += $상품신청가격;
			$마지막레코드 = $row;
			$order_html .= "<tr>
					<td>$row[od_id]</td>
					<td>$row[clay_id]</td>
					<td>$row[name]</td>
					<td>$row[it_id]</td>
					<td>$row[it_name]</td>
					<td align='right'>".number_format($row[it_qty])."</td>
					<td align='right'>".number_format($row[it_org_price])."</td>
					<td align='right'>".number_format($상품신청가격)."</td>
				</tr>";

		}// while end - 레코드 단위

		echo "<tr>
						<th width='120'>닉네임/이름</th>
						<td width='180'>$마지막레코드[clay_id] / $마지막레코드[name]</td>
						<th>H.P</th>
						<td>$마지막레코드[hphone]</td>
						<th>주소</th>
						<td>[$마지막레코드[zip]] $마지막레코드[addr1] $마지막레코드[addr2]</td>
					</tr>";
	} // for end - 주문번호 단위

	echo "</table>
				<table align='center'>".$order_html."</table>";

}
?>

</div>

</form>


<script language='javascript'>
function noEvent(){
   if(event.keyCode == 116) {
      event.keyCode = 2;
      return false;
   }else if(event.ctrlKey && (event.keyCode==78 || event.keyCode == 82)) {
      return false;
   }
}
document.onkeydown = noEvent;
</script>

<!-- 새창 대신 사용하는 iframe -->
<iframe width=0 height=0 name='hiddenframe' style='display:none;'></iframe>
<?
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>