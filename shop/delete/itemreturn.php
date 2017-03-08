<?php
include_once('./_common.php');

if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

include_once('./_head.php');
?>

<div id="aside2"></div>

<!-- 교환요청 시작 { -->

<?include_once("../inc/mypage_menu.php");?>

<div style="background:#fff;">
	

	<?include_once("../inc/mypage_submenu.php");?>

	<div id="my_ex_guide">
		<div style="width:272px;height:18px;font-size:14px;color:#fff;padding:2px 7px 2px 7px;background:#56ccc8;">투데이 스토어 상품만 반품/교환이 가능합니다.</div>
		<div class="cl" style="margin:10px 0 0 0;padding:0 0 0 7px;">
			반품/교환신청은 "배송완료" 후 30일 이내만 가능합니다.</br>
			단, 사전에 반품기간을 달리 정하여 이용자에게 고지한 경우에는 그 기간내에 반품을 신청할 수 있습니다.</br>
			추가로 구매한 상품만을 반품/교환하는 경우 추가 비용이 발생할 수 있습니다.
		</div>
	</div>

	<div id="my_ex_step">
		<div><img src="<?=G5_URL?>/img/my_re_step1_o.gif" border="0" align="absmiddle"></div>
		<div><img src="<?=G5_URL?>/img/my_re_step2.gif" border="0" align="absmiddle"></div>
		<div><img src="<?=G5_URL?>/img/my_re_step3.gif" border="0" align="absmiddle"></div>
	</div>
	
	<div style="margin:30px 0 0 0;padding:0 0 0 15px;font-size:14px;font-weight:bold;color:#545454;">
		반품요청 상품정보
	</div>

	<form name="fitemrechange" id="fitemrechange" method="POST">
	<input type="hidden" name="HTTP_CHK" value="CHK_OK">
	<input type="hidden" name="cate1" value="<?=$cate1?>">

	<div class="tbl_head01 tbl_wrap" style="margin:5px 0 0 0;border-top:2px #545454 solid;">
		<table>
		<thead>
		<tr>
			<th scope="col" style="width:100px;background:#fff;">주문일자/주문번호</th>
			<th scope="col" style="background:#fff;">상품정보</th>
			<th scope="col" class="my_td" style="width:100px;background:#fff;">주문금액(수량)</th>
		</tr>
		</thead>
		<tbody>
		<?php
		$k = 0;
		for ($i=0; $i < count($_POST[ex_bn_input]); $i++)
		{
			if($_POST[ex_bn_input][$i]){

				$sql = " select * from {$g5['g5_shop_order_table']} as a
				 LEFT JOIN {$g5['g5_shop_cart_table']} as b
				 ON a.od_id=b.od_id
				 where a.mb_id = '{$member['mb_id']}'
				 and b.ct_type=''
				 and a.od_id='".$_POST[ex_bn_input][$i]."'
				 ";

				$it = sql_fetch($sql);
			
				$uid = md5($it['od_id'].$it['od_time'].$it['od_ip']);

				switch($row['od_status']) {
					case '주문':
						$od_status = '입금확인중';
						break;
					case '입금':
						$od_status = '입금완료';
						break;
					case '준비':
						$od_status = '상품준비중';
						break;
					case '배송':
						$od_status = '상품배송';
						break;
					case '완료':
						$od_status = '배송완료';
						break;
					default:
						$od_status = '주문취소';
						break;
				}

				//$cart_res = sql_query("select * from {$g5['g5_shop_cart_table']} where od_id='".$row[od_id]."' ");
				$cart_row = sql_fetch("select * from {$g5['g5_shop_cart_table']} where od_id='".$it[od_id]."' order by ct_time desc limit 0, 1 ");
				//$cart_num = mysql_num_rows($cart_res);
				//$cart_num = $cart_num - 1;

				$image = get_it_image($it['it_id'], 70, 70);

		?>
		<input type="hidden" name="od_id[<?=$k?>]" value="<?php echo $it['od_id']; ?>">
		<input type="hidden" name="it_id[<?=$k?>]" value="<?php echo $it['it_id']; ?>">

		<tr style="font-size:11px;">
			<td style="width:100px;">
				<?php echo str_replace("-", ".", substr($it['od_time'],0,10)); ?></br>
				<input type="hidden" name="ct_id[<?php echo $i; ?>]" value="<?php echo $it['ct_id']; ?>">
				<a href="<?php echo G5_SHOP_URL; ?>/orderinquiryview.php?od_id=<?php echo $it['od_id']; ?>&amp;uid=<?php echo $uid; ?>" style="font-size:9px;font-weight:normal;text-decoration:underline;color:#56ccc8;"><?php echo $it['od_id']; ?></a>
			</td>
			<td>
				<table border="0" cellspacing="0" cellpadding="0" width="100%">
					<tr>
						<td style="border:0px;"><a href="<?=G5_SHOP_URL?>/item.php?it_id=<?=$cart_row[it_id]?>"><?=$image?></a></td>
						<td style="border:0px;"><a href="<?=G5_SHOP_URL?>/item.php?it_id=<?=$cart_row[it_id]?>"><?=$cart_row[it_name]?></a></td>
					</tr>
				</table>
			</td>
			<td class="td_numbig my_td" style="width:100px;">
				<?php echo display_price($it['od_cart_price'] + $it['od_send_cost'] + $it['od_send_cost2']); ?></br>
				(<?php echo $it['od_cart_count']; ?>개)
			</td>
		</tr>

		

		<?php
				$k++;
			}
		}

		if ($i == 0)
			echo '<tr><td colspan="7" class="empty_table">주문 내역이 없습니다.</td></tr>';
		?>
		</tbody>
		</table>
	</div>
	<!-- } 교환요청 끝 -->


	<div id="my_ex_tb">
		<table border="0" cellspacing="0" cellpadding="0">
			<tr height="40px">
				<td style="width:70px;"><b>사유선택</b></td>
				<td>
					<select name="re_status" style="border:1px #e5e5e5 solid;color:#737373;font-size:13px;">
						<option value="">교환사유를 선택해 주세요</option>
						<option value="1">서비스 및 상품 불만</option>
						<option value="2">상품파손</option>
						<option value="3">오배송</option>
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<b>사유입력</b></br>
					<span class="ex_con_len">0</span>/500
				</td>
				<td>
					<textarea name="re_con" style="width:520px;height:85px;border:1px #e5e5e5 solid;"></textarea>
				</td>
			</tr>
		</table>
	</div>

	<div id="my_ex_bn">
		<img src="<?=G5_URL?>/img/my_re_submit.gif" border="0" align="absmiddle" class="my_re_submit">
		<img src="<?=G5_URL?>/img/my_ex_cancel.gif" border="0" align="absmiddle" onclick="goto_url('<?=G5_SHOP_URL?>/orderinquiry_exchange.php');">
	</div>

	</form>

</div>

<script type="text/javascript">

$(document).ready(function(){
	$(".my_re_submit").click(function(){
		if($("select[name='re_status']").val() == ""){
			alert("사유를 선택해주세요");
			$("select[name='re_status']").focus();
			return false;
		}

		if($("textarea[name='re_con']").val() == ""){
			alert("사유를 입력해주세요");
			$("textarea[name='re_con']").focus();
			return false;
		}

		if(confirm("반품요청을 하시겠습니까?")){
			$("form[name='fitemrechange']").attr("action", "./itemrechange_addr_info.php").submit();
		}
	});
});

</script>
