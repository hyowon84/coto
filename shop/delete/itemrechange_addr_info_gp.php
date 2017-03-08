<?php
include_once('./_common.php');

if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

if($_POST[HTTP_CHK] != "CHK_OK") alert("잘못된 접근 방식입니다.");

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
		<div><img src="<?=G5_URL?>/img/my_re_step1.gif" border="0" align="absmiddle"></div>
		<div><img src="<?=G5_URL?>/img/my_re_step2_o.gif" border="0" align="absmiddle"></div>
		<div><img src="<?=G5_URL?>/img/my_re_step3.gif" border="0" align="absmiddle"></div>
	</div>
	
	<div style="margin:30px 0 0 0;padding:0 0 0 15px;font-size:14px;font-weight:bold;color:#545454;">
		택배기사님이 방문할 수거지 정보를 입력하여 주십시오.
	</div>

	<form name="fitemrechange" id="fitemrechange" method="POST">
	<input type="hidden" name="HTTP_CHK" value="CHK_OK">
	<input type="hidden" name="cate1" value="<?=$cate1?>">
	<input type="hidden" name="re_status" value="<?=$_POST[re_status]?>">
	<input type="hidden" name="re_con" value="<?=$_POST[re_con]?>">

	<?php
	$k = 0;
	for($i = 0; $i < count($_POST[od_id]); $i++){
		if($_POST[od_id][$i]){

			$sql = " select * from {$g5['g5_shop_order_table']} as a
			 LEFT JOIN {$g5['g5_shop_cart_table']} as b
			 ON a.od_id=b.od_id
			 where a.mb_id = '{$member['mb_id']}'
			 and b.ct_type!=''
			 and a.od_id='".$_POST[od_id][$i]."'
			 ";

			$it = sql_fetch($sql);

			$cart_row = sql_fetch("select * from {$g5['g5_shop_cart_table']} where od_id='".$it[od_id]."' order by ct_time desc limit 0, 1 ");
	?>

	<input type="hidden" name="od_id[<?=$k?>]" value="<?php echo $it['od_id']; ?>">
	<input type="hidden" name="it_id[<?=$k?>]" value="<?php echo $it['it_id']; ?>">

	<?php
			$k++;
		}
	}
	?>
	<!-- } 교환요청 끝 -->


	<div id="my_ex_tb" style="margin:5px 0 0 0;border-top:2px #545454 solid;border-bottom:1px #efefef solid;">
		<table border="0" cellspacing="0" cellpadding="0">
			<tr height="40px">
				<td style="width:120px;"><b>수거지선택</b></td>
				<td>
					<div class="my_ex_addr_bn on">기존상품주문 주소</div>
					<div class="my_ex_addr_bn">새로운 주소</div>
				</td>
			</tr>
		</table>
	</div>

	<div id="my_ex_tb" style="margin:10px 0 0 0;font-weight:bold;border-bottom:1px #efefef solid;">
		<table border="0" cellspacing="0" cellpadding="0">
			<tr height="40px">
				<td style="width:120px;"><b>보내시는 분</b></td>
				<td class="my_ex_tb_view0">
					<?=$member[mb_name]?>
					<input type="hidden" name="my_ex_name" value="<?=$member[mb_name]?>">
				</td>
			</tr>
			<tr height="40px">
				<td><b>배송지 주소</b></td>
				<td class="my_ex_tb_view1">
					(<?=$member[mb_zip1]?>-<?=$member[mb_zip2]?>)<?=$member[mb_addr_jibeon]?>
					<input type="hidden" name="my_ex_addr" value="(<?=$member[mb_zip1]?>-<?=$member[mb_zip2]?>)<?=$member[mb_addr_jibeon]?>">
				</td>
			</tr>
			<tr height="40px">
				<td><b>전화번호</b></td>
				<td class="my_ex_tb_view2">
					<?=$member[mb_tel]?>
					<input type="hidden" name="my_ex_tel" value="<?=$member[mb_tel]?>">
				</td>
			</tr>
			<tr height="40px">
				<td><b>휴대폰번호</b></td>
				<td class="my_ex_tb_view3">
					<?=$member[mb_hp]?>
					<input type="hidden" name="my_ex_hp" value="<?=$member[mb_hp]?>">
				</td>
			</tr>
			<tr height="40px">
				<td><b></b></td>
				<td>
					받으시는 분, 주소, 전화번호를 변경하시려면 '새로운 주소'를 선택하세요.&nbsp;&nbsp;&nbsp;
					<span class="ex_new_addr">새로운 주소 입력</span>
				</td>
			</tr>
			<tr height="40px">
				<td><b>남기실 말씀</b></td>
				<td>
					<input type="text" name="ex_content1" value="">
				</td>
			</tr>
		</table>
	</div>

	<div id="my_ex_bn">
		<img src="<?=G5_URL?>/img/my_re_submit1.gif" border="0" align="absmiddle" class="my_re_submit">
		<img src="<?=G5_URL?>/img/my_ex_cancel.gif" border="0" align="absmiddle" onclick="goto_url('<?=G5_SHOP_URL?>/orderinquiry_exchange.php');">
	</div>

	</form>

</div>

<script type="text/javascript">

$(document).ready(function(){
	$(".my_re_submit").click(function(){
		if(confirm("반품요청을 하시겠습니까?")){
			$("form[name='fitemrechange']").attr("action", "./itemreturnupdate.php").submit();
		}
	});

	$(".my_ex_addr_bn").click(function(){
		var idx = $(".my_ex_addr_bn").index(this);
		$(".my_ex_addr_bn").each(function(i){
			if(idx == i){
				$(".my_ex_addr_bn").eq(i).addClass("on");
			}else{
				$(".my_ex_addr_bn").eq(i).removeClass("on");
			}
		});

		if(idx == 0){
			
			$(".my_ex_tb_view0").html("<?=$member[mb_name]?><input type='hidden' name='my_ex_name' value='<?=$member[mb_name]?>'>");
			$(".my_ex_tb_view1").html("(<?=$member[mb_zip1]?>-<?=$member[mb_zip2]?>)<?=$member[mb_addr_jibeon]?><input type='hidden' name='my_ex_addr' value='(<?=$member[mb_zip1]?>-<?=$member[mb_zip2]?>)<?=$member[mb_addr_jibeon]?>'>");
			$(".my_ex_tb_view2").html("<?=$member[mb_tel]?><input type='hidden' name='my_ex_tel' value='<?=$member[mb_tel]?>'>");
			$(".my_ex_tb_view3").html("<?=$member[mb_hp]?><input type='hidden' name='my_ex_hp' value='<?=$member[mb_hp]?>'>");
			
		}else{

			$(".my_ex_tb_view0").html("<input type='text' name='my_ex_name' value=''>");
			$(".my_ex_tb_view1").html("<input type='text' name='my_ex_addr' style='width:300px;' value=''>");
			$(".my_ex_tb_view2").html("<input type='text' name='my_ex_tel' value=''>");
			$(".my_ex_tb_view3").html("<input type='text' name='my_ex_hp' value=''>");

		}
	});

	$(".ex_new_addr").click(function(){
		goto_url('<?=G5_BBS_URL?>/member_confirm1.php?url=/bbs/register_form.php');
	});
});

</script>
