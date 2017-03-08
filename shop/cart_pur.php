<?php
include_once('./_common.php');

// cart id 설정
set_cart_id($sw_direct);

$s_cart_id = get_session('ss_cart_id');
// 선택필드 초기화
$sql = " update {$g5['g5_shop_cart_table']} set ct_select = '0' where od_id = '$s_cart_id' ";
sql_query($sql);

$cart_action_url = G5_SHOP_URL.'/cartupdate.php';

if (G5_IS_MOBILE) {
    include_once(G5_MSHOP_PATH.'/cart.php');
    return;
}

$k = 0;

if($ct_type == "2010" || $ct_type == ""){
	$ct_type = "2010";
}else if($ct_type == "2020"){
	$ct_type = $ct_type;
}else if($ct_type == "2030"){
	$ct_type = $ct_type;
}else if($ct_type == "2040"){
	$ct_type = $ct_type;
}else if($ct_type == "2050"){
	$ct_type = $ct_type;
}

$g5['title'] = '쇼핑카트';
include_once('./_head.php');
?>

<!-- 장바구니 시작 { -->
<script src="<?php echo G5_JS_URL; ?>/shop.js"></script>

<!-- 타이틀 -->
<div class="cart_title"><?=$g5['title']?></div>

<!-- navi -->
<div class="cart_nav">
	<ul>
		<li><img src="img/cart_nav1_on.gif" border="0" align="absmiddle"></li>
		<li><img src="img/cart_nav2.gif" border="0" align="absmiddle"></li>
		<li><img src="img/cart_nav3.gif" border="0" align="absmiddle"></li>
	</ul>
</div>

<!-- 탭메뉴 -->
<div class="cl cart_tab" style="margin:10px 0 0 0;">
	<ul>
		<li onclick="goto_url('./cart.php');">투데이 스토어 상품</li>
		<li onclick="goto_url('./cart_gp.php');">공동구매 상품</li>
		<li class="right on" onclick="goto_url('./cart_pur.php');">구매대행 상품</li>
	</ul>
</div>


<form name="frmcartlist" id="sod_bsk_list" method="post" action="<?php echo $cart_action_url; ?>">
<input type="hidden" name="buy_kind" value="구매대행">

<div class="list_box">


	<div id="sod_bsk">

		<div class="tbl_head01 tbl_wrap product1">
			<table>
			<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="ct_all" value="1" id="ct_all" checked="checked" idx="1" status="1"></th>
				<th scope="col" class="right" colspan="2">상품정보</th>
				<th scope="col" class="right" width="70px">수량</th>
				<th scope="col" class="right">상품금액</th>
				<th scope="col" class="right">할인</th>
				<th scope="col" class="right">배송</th>
				<th scope="col" width="100px">선택</th>
			</tr>
			</thead>
			<tbody>
			<?php
			$tot_point = 0;
			$tot_sell_price = 0;

			// $s_cart_id 로 현재 장바구니 자료 쿼리
			$sql = " select a.ct_id,
							a.it_id,
							a.it_name,
							a.ct_price,
							a.ct_point,
							a.ct_qty,
							a.ct_status,
							a.ct_send_cost,
							b.ca_id,
							b.ca_id2,
							b.ca_id3
					   from {$g5['g5_shop_cart_table']} a left join {$g5['g5_shop_item_table']} b on ( a.it_id = b.it_id )
					  where a.od_id = '$s_cart_id' and pur_status='y' ";
			if($default['de_cart_keep_term']) {
				$ctime = date('Y-m-d', G5_SERVER_TIME - ($default['de_cart_keep_term'] * 86400));
				$sql .= " and substring(a.ct_time, 1, 10) >= '$ctime' ";
			}
			$sql .= " group by a.it_id ";
			$sql .= " order by a.ct_id ";
			$result = sql_query($sql);

			$it_send_cost = 0;

			for ($i=0; $row=mysql_fetch_array($result); $i++)
			{
				// 합계금액 계산
				$sql = " select SUM(IF(io_type = 1, (io_price * ct_qty), ((ct_price + io_price) * ct_qty))) as price,
								SUM(ct_point * ct_qty) as point,
								SUM(ct_qty) as qty
							from {$g5['g5_shop_cart_table']}
							where it_id = '{$row['it_id']}'
							  and od_id = '$s_cart_id' ";
				$sum = sql_fetch($sql);

				if ($i==0) { // 계속쇼핑
					$continue_ca_id = $row['ca_id'];
				}

				$a1 = '<a href="./item.php?it_id='.$row['it_id'].'"><b>';
				$a2 = '</b></a>';
				$image = get_it_image($row['it_id'], 70, 70);

				$it_name = $a1 . stripslashes($row['it_name']) . $a2;
				$it_options = print_item_options($row['it_id'], $s_cart_id);
				if($it_options) {
					$mod_options = '<div class="sod_option_btn"><button type="button" class="mod_options">선택사항수정</button></div>';
					$it_name .= '<div class="sod_opt">'.$it_options.'</div>';
				}

				// 배송비
				switch($row['ct_send_cost'])
				{
					case 1:
						$ct_send_cost = '착불';
						break;
					case 2:
						$ct_send_cost = '무료';
						break;
					default:
						$ct_send_cost = '선불';
						break;
				}

				$point      = $sum['point'];
				$sell_price = $sum['price'];
			?>

			<tr>
				<td class="td_chk">
					<?=$row[auc_status]?>
					<input type="checkbox" name="ct_chk[<?php echo $k; ?>]" value="1" id="ct_chk_<?php echo $k; ?>" checked="checked">
				</td>
				<td class="sod_img"><?php echo $image; ?></td>
				<td class="right">
					<input type="hidden" name="it_id[<?php echo $k; ?>]"    value="<?php echo $row['it_id']; ?>">
					<input type="hidden" name="it_name[<?php echo $k; ?>]"  value="<?php echo get_text($row['it_name']); ?>">
					<?php// echo $it_name.$mod_options; ?>
					<div><?php echo get_text($row['it_name']); ?></div>
					<div style="margin:7px 0 0 0;">
						<div style="float:left;width:50%;text-align:right;">판매가</div>
						<div class="pro_price"><?php echo number_format($row['ct_price']); ?></div>
					</div>
				</td>
				<td class="td_num right" align="center">
					<div class="ct_cnt"><input type="text" name="ct_qty[<?php echo $row['it_id']; ?>]" id="ct_qty" value="<?php echo $sum['qty']; ?>" cart_id="<?=$s_cart_id?>" idx="<?php echo $row['it_id']; ?>" status="1" size="4" style="height:17px;"></div>
					<div style="float:left;margin:0 0 0 3px;width:13px;">
						<div class="cnt_p" idx="<?php echo $row['it_id']; ?>" status="1" cart_id="<?=$s_cart_id?>"><img src="<?=G5_URL?>/img/cart_arrow_top.gif" style="border:0px;"></div>
						<div class="cnt_m" idx="<?php echo $row['it_id']; ?>" status="1" cart_id="<?=$s_cart_id?>"><img src="<?=G5_URL?>/img/cart_arrow_bottom.gif" style="border:0px;"></div>
					</div>
				</td>
				<td class="td_numbig right"><span id="sell_price_<?php echo $k; ?>" class="sell_price sell_price<?php echo $row['it_id']; ?>"><?php echo number_format($sell_price); ?></span></td>
				<!--<td class="td_numbig"><?php// echo number_format($point); ?></td>-->
				<td class="td_num right">-</td>
				<td class="td_num right"><?php echo number_format($ct_send_cost); ?></td>
				<td class="td_num">
					<img src="img/cart_buy_bn.gif" align="absmiddle" class="cart_buy"></br>
					<a href="javascript:item_wish(document.fwish, '<?php echo $row['it_id']; ?>');" id="sit_btn_wish"><img src="img/cart_wishlist_bn.gif" align="absmiddle" style="border:0px;cursor:pointer;"></a></br>
					<img src="img/cart_del_bn.gif" align="absmiddle" style="border:0px;cursor:pointer;" onclick="return form_check('delete', '', '<?php echo $row['it_id']; ?>');">
				</td>
			</tr>

			<?php
				$tot_point      += $point;
				$tot_sell_price += $sell_price;

				$k++;
			} // for 끝

			if ($i == 0) {
				echo '<tr><td colspan="8" class="empty_table">장바구니에 담긴 상품이 없습니다.</td></tr>';
			} else {
				// 배송비 계산
				$send_cost1 = get_sendcost1($s_cart_id, 0, "n");
				$send_cost = get_sendcost($s_cart_id, 0);
			}
			?>
			</tbody>
			</table>
		</div>

		<!-- 상품 선택 버튼 -->
		<div class="pro_choi_bn">
			<ul>
				<li class="pro_all_chk" idx="1">상품 전체선택</li>
				<li class="pro_all_rel" idx="1">상품 선택해제</li>
				<li onclick="return form_check('seldelete1', '1');">선택상품 삭제</li>
			</ul>
		</div>

		<!-- 일반상품 가격 정보 -->
		<div class="price_info_gp price_info1">
			<table border="0" cellspacing="0" cellpadding="0" width="100%" height="100px" class="price_info_tb">
				<tr height="40px">
					<td rowspan="2" width="87px" class="gubun">
						일반상품 
					</td>
					<td width="188px">
						상품금액
					</td>
					<td width="135px">
						할인금액
					</td>
					<td width="150px">
						배송비
					</td>
					<td>
						결제예상금액
					</td>
				</tr>
				<tr>
					<td><div class="price_box"><span class="price">0</span><span style="color:#818181;font-weight:normal;font-size:14px;">원</span></div></td>
					<td><div class="price_box"><span class="price">0</span><span style="color:#818181;font-weight:normal;font-size:14px;">원</span></div></td>
					<td><div class="price_box"><span class="price"><?=number_format($send_cost1)?></span><span style="color:#818181;font-weight:normal;font-size:14px;">원</span></div></td>
					<td><div class="price_box all"><span class="price">0</span><span style="font-weight:normal;font-size:14px;">원</span></div></td>
				</tr>
			</table>
		</div>

	</div>

</div>

<!--
<?php
//$tot_price = $tot_sell_price + $send_cost; // 총계 = 주문상품금액합계 + 배송비
//if ($tot_price > 0 || $send_cost > 0) {
?>
<dl id="sod_bsk_tot">
	<?php// if ($send_cost > 0) { // 배송비가 0 보다 크다면 (있다면) ?>
	<dt class="sod_bsk_dvr">배송비</dt>
	<dd class="sod_bsk_dvr"><strong><?php// echo number_format($send_cost); ?> 원</strong></dd>
	<?php// } ?>

	<?php
	//if ($tot_price > 0) {
	?>

	<dt class="sod_bsk_cnt">총계 가격/포인트</dt>
	<dd class="sod_bsk_cnt"><strong><?php// echo number_format($tot_price); ?> 원 / <?php// echo number_format($tot_point); ?> 점</strong></dd>
	<?php// } ?>

</dl>
<?php// } ?>
-->


<div id="sod_bsk_act">
	<?php if ($k == 0) { ?>
	<a href="<?php echo G5_URL; ?>/agency/purchase_ebay_list.php"><img src="<?G5_URL?>/shop/img/cart_shop_bn.gif" align="absmiddle" style="border:0;" onclick="return form_check('buy');"></a>
	<?php } else { ?>
	<input type="hidden" name="url" value="./orderform.php">
	<input type="hidden" name="records" value="<?php echo $k; ?>">
	<input type="hidden" name="act" value="">
	<input type="hidden" name="no" value="">
	<img src="<?G5_URL?>/shop/img/cart_buy_all_bn.gif" align="absmiddle" style="border:0;cursor:pointer;" onclick="return form_check('buy');">
	<a href="<?php echo G5_SHOP_URL; ?>/list.php?ca_id=<?php echo $continue_ca_id; ?>"><img src="<?G5_URL?>/shop/img/cart_shop_bn.gif" align="absmiddle" style="border:0;" onclick="return form_check('buy');"></a>
	<!--<a href="<?php// echo G5_SHOP_URL; ?>/list.php?ca_id=<?php echo $continue_ca_id; ?>" class="btn01">쇼핑 계속하기</a>
	<button type="button" onclick="return form_check('buy');" class="btn_submit">주문하기</button>
	<button type="button" onclick="return form_check('seldelete');" class="btn01">선택삭제</button>
	<button type="button" onclick="return form_check('alldelete');" class="btn01">비우기</button>-->
	<?php } ?>
</div>

</form>

<form name="fwish" id="fwish" method="POST">
<input type="hidden" name="url" id="url" value="">
<input type="hidden" name="it_id" id="it_id" value="">
</form>

<form name="fdel" id="fdel" method="POST">
<input type="hidden" name="url" id="url" value="">
<input type="hidden" name="it_id" id="it_id" value="">
<input type="hidden" name="act" id="act" value="">
</form>

<script>
$(function() {
    var close_btn_idx;
	var idx, status, old_cnt, cnt, pro_price, price1_1, price1, send_cost, cart_id, price3, cost, cost1, cost2, all_price;

	<?if($send_cost){?>
		send_cost = <?=$send_cost?>;
	<?}else{?>
		send_cost = 0;
	<?}?>

    // 선택사항수정
    $(".mod_options").click(function() {
        var it_id = $(this).closest("tr").find("input[name^=it_id]").val();
        var $this = $(this);
        close_btn_idx = $(".mod_options").index($(this));

        $.post(
            "./cartoption.php",
            { it_id: it_id },
            function(data) {
                $("#mod_option_frm").remove();
                $this.after("<div id=\"mod_option_frm\"></div>");
                $("#mod_option_frm").html(data);
                price_calculate();
            }
        );
    });

    // 모두선택
    $("input[name=ct_all]").click(function() {
		var idx = $(this).attr("idx");
		price1_1 = 0;
		price1 = 0;
		price3 = 0;

        if($(this).is(":checked")){
            $(".product" + idx).find("input[name^=ct_chk]").attr("checked", true);
			
			$(".product1").find(".sell_price").each(function(i){
				price1_1 = removeComma($(".product1").find(".sell_price").eq(i).html());
				price1 = parseInt(price1_1) + parseInt(price1);
			});

			$(".price_info1").find(".price").eq(0).html(commaNum(price1));
			$(".all_price_info").find(".price_all").eq(0).html(commaNum(price1));
			price3 = removeComma($(".price_info1").find(".price").eq(2).html());
			price3 = parseInt(price3) + price1;

			if(price3){
				price3 = price3;
			}else{
				price3 = 0;
			}
			
			$(".price_info1").find(".price").eq(3).html(commaNum(price3));
        }else{
            $(".product" + idx).find("input[name^=ct_chk]").attr("checked", false);
			$(".price_info1").find(".price").eq(0).html(0);
			$(".price_info1").find(".price").eq(1).html(0);
			$(".price_info1").find(".price").eq(2).html(0);
			$(".price_info1").find(".price").eq(3).html(0);
		}

    });

	// 체크박스 클릭시
	$("input:checkbox[name^='ct_chk']").click(function(){
		var sell_price = removeComma($(this).parent().parent().find(".sell_price").html());
		var price = removeComma($(".price_info1").find(".price").eq(0).html());
		var cost = removeComma($(".price_info1").find(".price").eq(2).html());
		var num = 0;
		var num1 = 0;

		if($(this).is(":checked") == true){
			num = parseInt(price) + parseInt(sell_price);
			num1 = parseInt(price) + parseInt(sell_price) + parseInt(cost);
			$(".price_info1").find(".price").eq(0).html(commaNum(num));
			$(".price_info1").find(".price").eq(3).html(commaNum(num1));
		}else{
			num = parseInt(price) - parseInt(sell_price);
			num1 = parseInt(price) - parseInt(sell_price) + parseInt(cost);
			$(".price_info1").find(".price").eq(0).html(commaNum(num));
			$(".price_info1").find(".price").eq(3).html(commaNum(num1));
		}
	});

    // 옵션수정 닫기
    $("#mod_option_close").live("click", function() {
        $("#mod_option_frm").remove();
        $(".mod_options").eq(close_btn_idx).focus();
    });
    $("#win_mask").click(function () {
        $("#mod_option_frm").remove();
        $(".mod_options").eq(close_btn_idx).focus();
    });

	//상품 전체선택
	$(".pro_all_chk").click(function(){
		var idx = $(this).attr("idx");
		var n = idx - 1;
		$(".product" + idx).find("input[name^=ct_chk]").attr("checked", true);
		$("input[name=ct_all]").eq(n).attr("checked", true);
	});

	//상품 선택해제
	$(".pro_all_rel").click(function(){
		var idx = $(this).attr("idx");
		var n = idx - 1;
		$(".product" + idx).find("input[name^=ct_chk]").attr("checked", false);
		$("input[name=ct_all]").eq(n).attr("checked", false);
	});

	//수량
	price1_1 = 0;
	price1 = 0;
	price3 = 0;
	
	$(".product1").find(".sell_price").each(function(i){
		price1_1 = removeComma($(".product1").find(".sell_price").eq(i).html());
		price1 = parseInt(price1_1) + parseInt(price1);
	});

	$(".price_info1").find(".price").eq(0).html(commaNum(price1));
	$(".all_price_info").find(".price_all").eq(0).html(commaNum(price1));

	price3 = $(".price_info1").find(".price").eq(2).html();
	price3 = parseInt(price3) + price1;

	if(price3){
		price3 = price3;
	}else{
		price3 = 0;
	}
	
	$(".price_info1").find(".price").eq(3).html(commaNum(price3));
	
	price1_1 = 0;
	price1 = 0;
	price3 = 0;

	$(".product2").find(".sell_price").each(function(i){
		price1_1 = removeComma($(".product2").find(".sell_price").eq(i).html());
		price1 = parseInt(price1_1) + parseInt(price1);
	});

	$(".price_info2").find(".price").eq(0).html(commaNum(price1));
	$(".all_price_info").find(".price_all_auc").eq(0).html(commaNum(price1));

	price3 = removeComma($(".price_info1").find(".price").eq(2).html());
	price3 = parseInt(price3) + price1;

	if(price3){
		price3 = price3;
	}else{
		price3 = 0;
	}
	
	$(".price_info2").find(".price").eq(3).html(commaNum(price3));

	$(".cnt_p").click(function(){
		idx = $(this).attr("idx");
		status = $(this).attr("status");
		old_cnt = $(this).parent().parent().find(".ct_cnt").find("input:text[name='ct_qty["+idx+"]']").val();
		cnt = parseInt(old_cnt) + 1;
		pro_price = removeComma($(this).parent().parent().parent().find(".pro_price").html());
		price1_1 = 0;
		price1 = 0;
		price3 = 0;
		cart_id = $(this).attr("cart_id");
		
		pro_price = pro_price * cnt;
		
		cart_cnt(this, idx, old_cnt, cnt, pro_price, price1_1, price1, cart_id, status);

	});

	$(".cnt_m").click(function(){
		idx = $(this).attr("idx");
		status = $(this).attr("status");
		old_cnt = $(this).parent().parent().find(".ct_cnt").find("input:text[name='ct_qty["+idx+"]']").val();
		cnt = parseInt(old_cnt) - 1;
		pro_price = removeComma($(this).parent().parent().parent().find(".pro_price").html());
		price1_1 = 0;
		price1 = 0;
		cart_id = $(this).attr("cart_id");
		
		pro_price = pro_price * cnt;

		if(cnt < 1){
			alert("최소 구매 수량은 1 입니다.");
			return false;
		}

		cart_cnt(this, idx, old_cnt, cnt, pro_price, price1_1, price1, cart_id, status);

	});

	$("input[name^=ct_qty]").keyup(function(){
		idx = $(this).attr("idx");
		status = $(this).attr("status");
		old_cnt = $(this).val();
		cnt = parseInt(old_cnt);
		pro_price = removeComma($(this).parent().parent().parent().find(".pro_price").html());
		price1_1 = 0;
		price1 = 0;
		cart_id = $(this).attr("cart_id");
		
		pro_price = pro_price * cnt;

		if(old_cnt < 1){
			alert("최소 구매 수량은 1 입니다.");
			$(this).val(1);
			return false;
		}

		$(this).parent().parent().parent().find(".sell_price" + idx).html(commaNum(pro_price));

		if(status == 1){
			$(".product1").find(".sell_price").each(function(i){
				price1_1 = removeComma($(".product1").find(".sell_price").eq(i).html());
				price1 = parseInt(price1_1) + parseInt(price1);
			});

			$(".price_info1").find(".price").eq(0).html(commaNum(price1));
			$(".all_price_info").find(".price_all").eq(0).html(commaNum(price1));

			price3 = removeComma($(".price_info1").find(".price").eq(2).html());
			price3 = parseInt(price3) + price1;
			
			$(".price_info1").find(".price").eq(3).html(commaNum(price3));
		}else{
			$(".product2").find(".sell_price").each(function(i){
				price1_1 = removeComma($(".product2").find(".sell_price").eq(i).html());
				price1 = parseInt(price1_1) + parseInt(price1);
			});

			$(".price_info2").find(".price").eq(0).html(commaNum(price1));
			$(".all_price_info").find(".price_all_auc").eq(0).html(commaNum(price1));

			price3 = removeComma($(".price_info1").find(".price").eq(2).html());
			price3 = parseInt(price3) + price1;
			
			$(".price_info2").find(".price").eq(3).html(commaNum(price3));
		}

		$.ajax({
			type : "POST",
			dataType : "HTML",
			url : "./_Ajax.cart.php",
			data : "idx=" + idx + "&cnt=" + cnt + "&cart_id=" + cart_id + "&status=" + status,
			success : function(data){
				$(".price_info" + status).find(".price").eq(2).html(commaNum(data));
				cost1 = removeComma($(".price_info1").find(".price").eq(2).html());
				cost2 = removeComma($(".price_info2").find(".price").eq(2).html());
				cost = parseInt(cost1) + parseInt(cost2);
				
				$(".all_price_info").find(".price_all_cost").html(commaNum(cost));

				all_price = parseInt(removeComma($(".price_info1").find(".price").eq(3).html())) + parseInt(removeComma($(".price_info2").find(".price").eq(3).html()));
				$(".all_price_info").find(".price_all_price").html(commaNum(all_price));
			}
		});
	});

	$(".cart_buy").click(function(){
		var f = document.frmcartlist;
		var idx = $(".cart_buy").index(this);

		$("input:checkbox[name^='ct_chk']").each(function(i){
			if(idx == i){
				$("input:checkbox[name^='ct_chk']").eq(i).attr("checked", true);
			}else{
				$("input:checkbox[name^='ct_chk']").eq(i).attr("checked", false);
			}
		});
		
		f.act.value = "buy";
        f.submit();
	});

});

function cart_cnt(th, idx, old_cnt, cnt, pro_price, price1_1, price1, cart_id, status){

	all_price = 0;

	$(th).parent().parent().parent().find(".sell_price" + idx).html(commaNum(pro_price));
	$(th).parent().parent().find(".ct_cnt").find("input:text[name='ct_qty["+idx+"]']").val(cnt);

	if(status == 1){
		$(".product1").find(".sell_price").each(function(i){
			price1_1 = removeComma($(".product1").find(".sell_price").eq(i).html());
			price1 = parseInt(price1_1) + parseInt(price1);
		});

		$(".price_info1").find(".price").eq(0).html(commaNum(price1));
		$(".all_price_info").find(".price_all").eq(0).html(commaNum(price1));

		price3 = removeComma($(".price_info1").find(".price").eq(2).html());
		price3 = parseInt(price3) + price1;
		
		$(".price_info1").find(".price").eq(3).html(commaNum(price3));
	}else{
		$(".product2").find(".sell_price").each(function(i){
			price1_1 = removeComma($(".product2").find(".sell_price").eq(i).html());
			price1 = parseInt(price1_1) + parseInt(price1);
		});

		$(".price_info2").find(".price").eq(0).html(commaNum(price1));
		$(".all_price_info").find(".price_all_auc").eq(0).html(commaNum(price1));

		price3 = removeComma($(".price_info1").find(".price").eq(2).html());
		price3 = parseInt(price3) + price1;
		
		$(".price_info2").find(".price").eq(3).html(commaNum(price3));
	}

	$.ajax({
		type : "POST",
		dataType : "HTML",
		url : "./_Ajax.cart.php",
		data : "idx=" + idx + "&cnt=" + cnt + "&cart_id=" + cart_id + "&status=" + status,
		success : function(data){
			$(".price_info" + status).find(".price").eq(2).html(commaNum(data));

			cost1 = removeComma($(".price_info1").find(".price").eq(2).html());
			cost2 = removeComma($(".price_info2").find(".price").eq(2).html());
			cost = parseInt(cost1) + parseInt(cost2);
			
			$(".all_price_info").find(".price_all_cost").html(commaNum(cost));

			all_price = parseInt(removeComma($(".price_info1").find(".price").eq(3).html())) + parseInt(removeComma($(".price_info2").find(".price").eq(3).html()));
			$(".all_price_info").find(".price_all_price").html(commaNum(all_price));
		}
	});
}

// 상품보관
function item_wish(f, it_id)
{
	f.url.value = "<?php echo G5_SHOP_URL; ?>/wishupdate.php?it_id="+it_id;
	f.it_id.value = it_id;
	f.action = "<?php echo G5_SHOP_URL; ?>/wishupdate.php";
	f.submit();
}

function form_check(act, num, idx) {

    var f = document.frmcartlist;
    var cnt = f.records.value;
	var data = "";

    if (act == "buy")
    {
        if($("input[name^=ct_chk]:checked").size() < 1) {
            alert("주문하실 상품을 하나이상 선택해 주십시오.");
            return false;
        }

        f.act.value = act;
        f.submit();
    }
    else if (act == "alldelete")
    {
        f.act.value = act;
        f.submit();
    }
    else if (act == "seldelete1")
    {
		
        if($("input[name^=ct_chk]:checked").size() < 1) {
            alert("삭제하실 상품을 하나이상 선택해 주십시오.");
            return false;
        }

		$(".product1").find("input[name^=ct_chk]").each(function(i){

			if($(".product1").find("input[name^=ct_chk]").eq(i).is(":checked") == true){
				data += $(".product1").find("input[name^=it_id]").eq(i).val() + "|";
			}
		});

		data = data.substr(0, data.length - 1);


		f.no.value = data;
        f.act.value = act;
        f.submit();
    }
	else if (act == "seldelete2")
    {
        if($("input[name^=ct_chk]:checked").size() < 1) {
            alert("삭제하실 상품을 하나이상 선택해 주십시오.");
            return false;
        }

		$(".product2").find("input[name^=ct_chk]").each(function(i){

			if($(".product2").find("input[name^=ct_chk]").eq(i).is(":checked") == true){
				data += $(".product2").find("input[name^=it_id]").eq(i).val() + "|";
			}
		});

		data = data.substr(0, data.length - 1);

		f.no.value = data;
        f.act.value = act;
        f.submit();
    }
	else if(act == "delete")
	{
		if(confirm("정말 삭제 하시겠습니까?")){
			document.fdel.url.value = "<?=G5_URL?>/shop/cart.php";
			document.fdel.act.value = act;
			document.fdel.it_id.value = idx;
			document.fdel.action = "./cartupdate.php";
			document.fdel.submit();
		}
	}

    return true;
}

</script>
<!-- } 장바구니 끝 -->

<?php
include_once('./_tail.php');
?>