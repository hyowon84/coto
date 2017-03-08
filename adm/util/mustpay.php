<?php
$sub_menu = '400350';
// $sub_sub_menu = '2';

include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$g5['title'] = '미입금자 미입금액 랭킹';
include_once (G5_ADMIN_PATH.'/admin.head.php');
include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');
?>

<style>
/* 주문내역 다이얼로그 */
#dialog_orderDetail table tr th { background-color: #EEEEEE; border:1px solid #d1dee2; padding:0px; text-align:center; height:25px; }
#dialog_orderDetail table tr td { padding-left:10px; border:1px solid #d1dee2; }


/* 다이얼로그 스타일 */
.gpinfo_dialog table tr th { padding:0px; text-align:left; height:25px; }
.gpinfo_dialog table tr td { padding-left:10px; border:1px #d1dee2; }
.gpinfo_dialog table tr td input { border:1px solid #EAEAEA; }

#divBankTableArea table tr th, #divBankTableArea table tr td{
	text-align:center; border:1px solid #d1dee2;
}

#divBankTableArea table tr th { height:25px; background-color:#EEEEEE; }
#divBankTableArea table tr td { height:25px; }


#divBankDtlTableArea { width:30%; float:left; margin:10px; }
#divBankDtlTableArea table tr th { height:25px; background-color:#fbffd7; }
/* #divBankDtlTableArea table { width:50%; } */



.gpinfo_tr:hover { background-color:#f1fbff; cursor:pointer; }
.gpinfo_dtl_tr:hover { background-color:#fcffe4; cursor:pointer; }

.divTr_order { height:83px; }

.hover_skyblue:hover { background-color:#f1fbff; cursor:pointer; }

.DetailOn { display:''; }
.DetailOff { display:none; }

.gpinfolist_inp_text { border:1px solid #d1dee2; width:90%; }

.yellow { background-color:#fffcda; }

</style>

<?

$총합계액SQL = "SELECT		SUM(CL.it_qty) AS SUM_QTY,
												SUM(CL.it_org_price * CL.it_qty) AS SUM_PAY,
												DP.baesongbi
								FROM		clay_order CL
												LEFT JOIN (		SELECT	DI.hphone,
																							DI.clay_id,
																							SUM(DI.delivery_price) AS baesongbi
																			FROM		(	SELECT	DISTINCT
																												CL.od_id,
																												CI.hphone,
																												CI.clay_id,
																												CI.delivery_price
																								FROM		clay_order CL
																												LEFT JOIN  clay_order_info CI ON (CI.od_id = CL.od_id)
																								WHERE		1=1
																								AND			CL.stats IN ('00','10')
																							) DI
																			GROUP BY DI.hphone, DI.clay_id
												) DP ON (DP.hphone = CL.hphone AND DP.clay_id = CL.clay_id)
								WHERE		1=1
								AND				CL.stats IN ('00','10')
								ORDER BY 	SUM(CL.it_qty * CL.it_org_price) DESC, CL.clay_id ASC
";
$TOTAL = mysql_fetch_array(sql_query($총합계액SQL));



$미결제SQL = "		SELECT	CL.clay_id,					/*주문상품코드*/
												CL.name,
												CL.hphone,
												SUM(CL.it_qty) AS SUM_QTY,
												SUM(CL.it_org_price * CL.it_qty) AS SUM_PAY,
												DP.baesongbi
								FROM		clay_order CL
												LEFT JOIN (		SELECT	DI.hphone,
																							DI.clay_id,
																							SUM(DI.delivery_price) AS baesongbi
																			FROM		(	SELECT	DISTINCT
																												CL.od_id,
																												CI.hphone,
																												CI.clay_id,
																												CI.delivery_price
																								FROM		clay_order CL
																												LEFT JOIN  clay_order_info CI ON (CI.od_id = CL.od_id)
																								WHERE		1=1
																								AND			CL.stats IN ('00','10')
																							) DI
																			GROUP BY DI.hphone, DI.clay_id
												) DP ON (DP.hphone = CL.hphone AND DP.clay_id = CL.clay_id)
								WHERE		1=1
								AND				CL.stats IN ('00','10')
								GROUP BY 	CL.hphone, CL.clay_id
								ORDER BY 	SUM(CL.it_qty * CL.it_org_price) DESC, CL.clay_id ASC
";
$result = sql_query($미결제SQL);
?>

<script>

function updateCsMemo(no,id,hp) {

	$.post('mustpay.update.php',
		{ 'clay_id' : id,
			'hphone': hp,
			'cs_memo': $('#cs_memo'+no).val()
		},
		function( data ) {
			if(data == 1) {
				alert('수정 완료');
			} else {
				alert('수정 실패');
			}
		}
	);

}
</script>



<div id='divBankTableArea' style='width:100%;'>

	<table width='80%' align='center'><tr><td>
	미입금 총액 : <?=number_format($TOTAL[SUM_PAY])?>원, 미입금 건수 : <?=number_format($TOTAL[SUM_QTY])?>건
	</td></tr>
	</table>

	<table width='80%' align='center'>
	<?
	while($row = mysql_fetch_array($result)) {
		$no++;
	?>
	<tr class='gpinfo_tr' bgcolor='<?=$link_color?>'>
		<td>
			<table width='100%'>
				<tr>
					<th width='120'>닉네임</th>
					<th width='120'>이름</th>
					<th width='100'>전화번호</th>
					<th width='90'>미결제수량</th>
					<th width='120'>미결제 주문금액</th>
					<th width='120'>미결제 배송비</th>
					<th width='120'>미결제 합계금액</th>
					<th width='500'>CS로그</th>
				</tr>


				<tr>
					<td><?=$row[clay_id]?></td>
					<td><?=$row[name]?></td>
					<td><?=$row[hphone]?></td>
					<td style='text-align:right;'><?=number_format($row[SUM_QTY])?></td>
					<td style='text-align:right;'><?=number_format($row[SUM_PAY])?></td>
					<td style='text-align:right;'><?=number_format($row[baesongbi])?></td>
					<td style='text-align:right;'><?=number_format($row[SUM_PAY] + $row[baesongbi])?></td>
					<td>
						<input type='text' id='cs_memo<?=$no?>' name='cs_memo' value='<?=$row[cs_memo]?>' style='width:86%;' />
						<input type='button' value='기록' onclick="updateCsMemo('<?=$no?>','<?=$row[clay_id]?>','<?=$row[hphone]?>')" />


						<select style='width:90%;'>
						<?
							$log_sql = "	SELECT	*
														FROM		log_table
														WHERE		logtype = 'mustpay'
														AND			gr_id = '$row[clay_id]'
														AND			pk_id = '$row[hphone]'
														ORDER BY reg_date DESC
							";
							$log_result = sql_query($log_sql);

							while($log = mysql_fetch_array($log_result))
							{
								?>
								<option><?=$log[memo]." : ".substr($log[reg_date],0,10)?></option>
								<?
							}

						?>
						</select>


					</td>
				</tr>
				<tr>
					<td colspan='8'>
						<table width='100%' align='center'>
							<tr>
								<th>공구명<br>주문번호</th>
								<th width='900'>상품명</th>
								<th>수량</th>
								<th>판매가</th>
								<th>주문금액</th>
								<th>주문상태</th>
							</tr>
						<?
							//주문내역
							$주문SQL = "	SELECT	GI.gpcode_name,
																		CO.od_id,
																		CO.clay_id,
																		CO.it_name,
																		CO.it_qty,
																		CO.it_org_price,
																		CO.it_qty * CO.it_org_price AS SUM_PAY,
																		CC.value AS stats_name,
																		CI.memo,
																		CI.admin_memo,
																		DC.value AS delivery_type_name,
																		CI.delivery_price
														FROM		clay_order CO
																		LEFT JOIN clay_order_info CI ON (CI.od_id = CO.od_id)
																		LEFT JOIN gp_info GI ON (GI.gpcode = CO.gpcode)
																		LEFT JOIN comcode CC ON (CC.ctype = 'clayorder' AND CC.col = 'stats' AND CC.code = CO.stats)
																		LEFT JOIN comcode DC ON (DC.ctype = 'clayorder' AND DC.col = 'delivery_type' AND DC.code = CI.delivery_type)
														WHERE		CO.clay_id = '$row[clay_id]'
														AND			CO.hphone = '$row[hphone]'
														AND			CO.stats IN ('00','10')
							";
							$od_result = sql_query($주문SQL);

							while ($od = mysql_fetch_array($od_result)) {

								if($od[od_id] != $이전od_id) {
									$메모 = "<font color=red><b>".$od[memo].$od[admin_memo]."</b></font>";
									$배송메모 = "<font color=blue><b>".$od[delivery_type_name]."(".$od[delivery_price].") </b></font>";
								} else {
									$메모 = '';
									$배송메모 = '';
								}

						?>
								<tr>
									<td>
										<b><?=$od[gpcode_name]?></b><br>
										<font color='#ff5a00'>
										<?=reform_substr($od[od_id],array(2,4,4,4),'-')?>
										</font>
									</td>
									<td style='text-align:left;'>
										<?=$od[it_name]?><br>
										<?=$메모?><br>
										<?=$배송메모?>
									</td>
									<td><?=number_format($od[it_qty])?></td>
									<td style='text-align:right;'><?=number_format($od[it_org_price])?></td>
									<td style='text-align:right;'><?=number_format($od[SUM_PAY])?></td>
									<td><?=$od[stats_name]?></td>
								</tr>
						<?
								$이전od_id = $od[od_id];
							} //while end
						?>

						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>

	<tr><td style='background-color:gray; height:5px;'></td></tr>
	<?
	} //while end
	?>
	</table>

</div>

<!-- link rel="stylesheet" href="http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css"-->

<script src="http://malsup.github.com/jquery.form.js"></script>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>