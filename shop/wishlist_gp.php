<?php
include_once('./_common.php');

/* 반응형으로 갈 것임
if (G5_IS_MOBILE) {
    include_once(G5_MSHOP_PATH.'/wishlist.php');
    return;
}
*/

if (!$is_member)
    goto_url(G5_BBS_URL."/login.php?url=".urlencode(G5_SHOP_URL.'/mypage.php'));

$g5['title'] = "위시리스트";
if (G5_IS_MOBILE) {
	include_once(G5_MSHOP_PATH.'/head.php');
} else {
	include_once(G5_PATH.'/_head.php');
}

echo '<link rel="stylesheet" href="'.G5_CSS_URL.'/'.(G5_IS_MOBILE?'mobile_shop_group':'style').'.css">'.PHP_EOL;
?>



<div id="aside2"></div>

<?include_once("../inc/mypage_menu.php");?>


<!-- 공동구매 신청 레이어팝업 -->
<div class="mw_layer_gp" style='width:100%;'>
	<div class="bg_gp"></div>

	<div id="layer_gp" style='width:100%;'>
		<div style="width:100%;font-size:17px;text-align:right"><a href="#" class="close_gp" style="padding-right:5px">X</a></div>
		<div class='gp_view_loading' style="width:100%; height:280px; margin-top:150px; font-size:20px; font-weight:bold; color:black;">
			<center>
				데이터를 불러오고 있습니다<br>
				잠시만 기다려주세요<br>
				<img src='/img/ajax-loader.gif' />
			</center>
		</div>
		<div class="gp_view">
		</div>
	</div>
</div>




<div class="contentWrap" style="background:#fff;">
	<!-- 1차오픈에서 제외
	<div id="my_tab_nav">
		<ul>
			<li <?if($buy_ok_status == ""){?>class="on"<?}?> onclick="goto_url('<?=G5_SHOP_URL?>/wishlist_gp.php?buy_ok_status=');">전체보기</li>
			<li <?if($buy_ok_status == "입금대기"){?>class="on"<?}?> onclick="goto_url('<?=G5_SHOP_URL?>/wishlist_gp.php?buy_ok_status=입금대기');">구매가능상품</li>
		</ul>
	</div>
	-->

	<!-- 위시리스트 탭메뉴 -->
	<!-- 1차 오픈에서 제외
	<div id="my_sub_tab">
		<ul>
			<li onclick="goto_url('<?=G5_URL?>/shop/wishlist.php');">투데이스토어</li>
			<li class="right on" onclick="goto_url('<?=G5_URL?>/shop/wishlist_gp.php');">공동구매</li>
		</ul>
	</div>
	-->

	<div id="my_wish_bn">
		<ul>
<!--			<li onclick="return fwishlist_check(document.fwishlist,'');">카트담기</li>-->
			<li class="seldel">선택상품 삭제</li>
		</ul>
	</div>

	<!-- 위시리스트 시작 { -->
	<div id="sod_ws">
		
		<form name="fwishlist" method="post" action="./gp_cartupdate.php">
		
		<? if(G5_IS_MOBILE) {?>
		
		<?} else {?>
		
		<input type="hidden" name="act"       value="multi">
		<input type="hidden" name="sw_direct" value="">
		<input type="hidden" name="prog"      value="wish">
		<input type="hidden" name="buy_kind" value="공동구매">
		<input type="hidden" name="w" value="">

		<div class="tbl_head01 tbl_wrap" style="border-top:2px #545454 solid;">
			<table>
			<thead>
			<tr>
				<th scope="col" class="my_wish_th">선택</th>
				<th scope="col" class="my_wish_th" colspan="2">상품정보</th>
				<th scope="col" class="my_wish_th" style="border-left:1px #efefef solid;">수량</th>
				<th scope="col" class="my_wish_th" style="border-left:1px #efefef solid;background:#faf9f9;">찜한날짜</th>
				<th scope="col" class="my_wish_th" style="border-left:1px #efefef solid;background:#faf9f9;">관리</th>
			</tr>
			</thead>
			<tbody>
			<?php
			if($buy_ok_status){
				$buy_status = " and a.it_id in (select distinct it_id from g5_shop_cart where mb_id='".$member[mb_id]."' and ct_status='".$buy_ok_status."') ";
			}

			/*
			$sql  = "
			select a.wi_id, a.wi_time, b.* from {$g5['g5_shop_wish_table']} a
			RIGHT join {$g5['g5_shop_group_purchase_table']} b on ( a.it_id = b.gp_id )
			LEFT JOIN g5_shop_cart c ON (b.gp_id = c.it_id)
			";
			*/
			$sql  = "
			select a.wi_id, a.wi_time, a.wi_cnt, b.* from {$g5['g5_shop_wish_table']} a
			RIGHT join {$g5['g5_shop_group_purchase_table']} b on ( a.it_id = b.gp_id )
			";
			$sql .= " where a.mb_id = '{$member['mb_id']}' $buy_status order by a.wi_id desc ";

			$s_cart_id = get_session('ss_cart_id');

			$result = sql_query($sql);
			for ($i=0; $row = mysql_fetch_array($result); $i++) {

				$out_cd = '';
				$sql = " select count(*) as cnt from {$g5['g5_shop_item_option_table']} where it_id = '{$row['it_id']}' and io_type = '0' ";
				$tmp = sql_fetch($sql);
				if($tmp['cnt'])
					$out_cd = 'no';

				$row[it_price] = getGroupPurchaseBasicPrice($row[gp_id]);
				$row[it_card_price] = getGroupPurchaseBasicPrice1($row[gp_id]);

				if ($row['it_tel_inq']) $out_cd = 'tel_inq';

				$image = get_gp_image($row['gp_id'], 70, 70);
				$it_point = get_item_point($row);

				$it = sql_fetch("
					select * from {$g5['g5_shop_group_purchase_table']}
					where gp_id='".$row['gp_id']."'
				");

				$po_cash_price = getGroupPurchaseBasicPrice($row[gp_id]);

				$sql = " select count(*) as cnt
							from {$g5['g5_shop_cart_table']}
							where it_id = '{$row['gp_id']}'
							and od_id = '$s_cart_id'
							and `ct_gp_status`='c'
							and `ct_gp_status`!='y'
							";
				$buy_st = sql_fetch($sql);
			?>

			<tr style="font-size:12px;">
				<td class="td_chk" style="border-bottom:1px #efefef solid;">
					<?php
					// 품절검사
					//if(is_soldout_gp($row['gp_id']))
					//{
					?>
					<!--품절-->
					<?php// } else { //품절이 아니면 체크할수 있도록한다 ?>
					<input type="checkbox" name="chk_it_id[<?php echo $i; ?>]" value="<?php echo $row['gp_id']; ?>" onclick="out_cd_check(this, '<?php echo $out_cd; ?>');">
					<?php// } ?>
					<input type="hidden" name="wi_id[<?php echo $row['gp_id']; ?>]" value="<?php echo $row['wi_id']; ?>">
					<input type="hidden" name="gp_id[<?php echo $i; ?>]" value="<?php echo $row['gp_id']; ?>">
					<input type="hidden" name="io_type[<?php echo $row['gp_id']; ?>]" value="0">
					<input type="hidden" name="io_id[<?php echo $row['gp_id']; ?>]" value="">
					<input type="hidden" name="io_value[<?php echo $row['gp_id']; ?>]" value="<?php echo $row['gp_name']; ?>">
					<input type="hidden" name="ct_qty[<?php echo $row['gp_id']; ?>]" value="<?php echo $row[wi_cnt]; ?>">
					<input type="hidden" name="ca_id[<?php echo $row['gp_id']; ?>]" value="<?=$row['ca_id']?>">
					<input type="hidden" name="it_price[<?php echo $row['gp_id']; ?>]" value="<?=$row[it_price]?>">
					<input type="hidden" name="it_card_price[<?php echo $row['gp_id']; ?>]" value="<?=$row[it_card_price]?>">
				</td>
				<td class="sod_ws_img" style="border-bottom:1px #efefef solid;"><?php echo $image; ?></td>
				<td style="border-bottom:1px #efefef solid;">
					<a href="./grouppurchase.php?gp_id=<?php echo $row['gp_id']; ?>"><?php echo stripslashes($row['gp_name']); ?></a></br>
					<span style="color:#f45100;font-style:italic;font-weight:bold;"><?=display_price($po_cash_price, $row['it_tel_inq'])?></span>
				</td>
				<td class="td_mngsmall" style="border-left:1px #efefef solid;border-bottom:1px #efefef solid;"><?php echo $row[wi_cnt]; ?></td>
				<td class="td_datetime" style="border-left:1px #efefef solid;border-bottom:1px #efefef solid;background:#faf9f9;"><?php echo date("Y.m.d", strtotime($row['wi_time'])); ?></td>
				<td class="td_mngsmall" style="border-left:1px #efefef solid;border-bottom:1px #efefef solid;background:#faf9f9;">
					<!--<div class="wish_bn buy_bn" style="border:1px #d82d00 solid;background:#ff4c03;color:#fff;" idx="<?=$i?>">구매하기</div>-->

					<?if($buy_st[cnt]){?>
						<div class="wish_bn" style="border:1px #d82d00 solid;background:#ff4c03;color:#fff;">구매가능</div>
					<?}else{?>
						<div class="wish_bn" style="border:1px #cfcfcf solid;" onclick="goto_url('./wishupdate.php?w=d&amp;buy_kind=공동구매&amp;wi_id=<?php echo $row['wi_id']; ?>');">삭제</div>
					<?}?>
				</td>
			</tr>
			<?php
			}

			if ($i == 0)
				echo '<tr><td colspan="5" class="empty_table">보관함이 비었습니다.</td></tr>';
			?>
			</tr>
			</tbody>
			</table>
		</div>

		<div id="sod_ws_act">
			<!--<button type="submit" class="btn01" onclick="return fwishlist_check(document.fwishlist,'');">장바구니 담기</button>
			<button type="submit" class="btn02" onclick="return fwishlist_check(document.fwishlist,'direct_buy');">주문하기</button>-->
		</div>
		
		<?}?>
		
		</form>
		
	</div>

	<div id="my_wish_bn">
		<ul>
<!--			<li onclick="return fwishlist_check(document.fwishlist,'');">카트담기</li>-->
			<li class="seldel">선택상품 삭제</li>
			<li style="float:right;color:#82c8ff;font-size:12px;font-style:italic;width:300px;border:0px;">
				<img src="<?=G5_URL?>/img/my_wish_ico.gif"> 찜한 상품 구매시 목록에서 자동으로 삭제됩니다.
			</li>
		</ul>
	</div>
</div>

<script>
	
	$(document).ready(function(){

		if(g5_is_mobile == 1) {
			createWishListItems();
			/* 위시리스트 단품 삭제 */
			$(document).on('click', '.del', function() {
				if(confirm("정말 삭제 하시겠습니까?")){
					goto_url('./wishupdate.php?w=d&buy_kind=공동구매&wi_id=' + $(this).attr('data-wiId'));
				}
			});
		}

		/* 선택된 위시리스트 복수 삭제  */
		$(".seldel").click(function(){
			if(confirm("정말 삭제 하시겠습니까?")){
				$("input[name='w']").val("seldel");
				$("form[name='fwishlist']").attr("action", "./wishupdate.php").submit();
			}
		});

		$(".cart_bn").click(function(){
			var idx = $(this).attr("idx");
			$("form[name='fwishlist']").find("input[name='sw_direct']").val(0);

			$("input[name^='chk_it_id']").attr("checked", false);
			$("input[name='chk_it_id["+idx+"]']").attr("checked", true);

			$("form[name='fwishlist']").submit();
		});



		$(".buy_bn").click(function(){
			var idx = $(this).attr("idx");

			$("form[name='fwishlist']").find("input[name='sw_direct']").val(1);

			$("input[name^='chk_it_id']").attr("checked", false);
			$("input[name='chk_it_id["+idx+"]']").attr("checked", true);

			$("form[name='fwishlist']").submit();
		});
	});

	function createWishListItems() {
		$.post("/ajax/req_wishlist.php" ,function(data) {

			var list = $.parseJSON(data).data;

			var container = $('#sod_ws');

			$.each(list, function(i, item) {
				var resultItem = createWishListItem(item);
				container.append(resultItem);
			});

			$('.prodImgContainer').imgLiquid({fill:false});
			
		});
	}

	function createWishListItem(item) {

		var itemHtml =
			"<div class='wishListItemContainer'>" +
				"<input type='checkbox' name class='chk_wishList' wi_id='"+item.wi_id+"'>" +
				"<div class='prodImgContainer'>" +
					"<img class='prodImg' src=" + decodeURIComponent(item.gp_img).replace("+", "%20") + ">" +
				"</div>" +
				"<div class='prodInfoContainer'>" +
					"<div class='prodName ellipsis'>" + item.gp_name + "</div>" +
					"<div class='indent'>" +
						"<div class='prodPrice'>" + $.number(item.gp_price) + "원</div>" +
						"<div class='prodQty'><span>수량</span> " + item.wi_cnt + " <input type='hidden' name='ct_qty"+item.gp_id+"' value='"+item.wi_cnt+"'></div>" +
						"<div class='prodDate ellipsis'><span>찜한날짜</span>" + item.wi_time + "</div>" +
						"<ul class='prodBtnContainer'>" +
							"<li class='gp_view_bn buy' ca_id='"+item.ca_id+"' gp_id='"+item.gp_id+"'  >신청하기</li>" +
							//"<li class='cart'>카트담기</li>" +
							"<li class='del' data-wiId='" + item.wi_id + "'>삭제</li>" +
						"</ul>" +
					"</div>" +
				"</div>" +
			"</div>";

		return itemHtml;
	}

    function out_cd_check(fld, out_cd) {
        if (out_cd == 'no'){
            alert("옵션이 있는 상품입니다.\n\n상품을 클릭하여 상품페이지에서 옵션을 선택한 후 주문하십시오.");
            fld.checked = false;
            return;
        }
        if (out_cd == 'tel_inq'){
            alert("이 상품은 전화로 문의해 주십시오.\n\n장바구니에 담아 구입하실 수 없습니다.");
            fld.checked = false;

        }
    }

    function fwishlist_check(f, act) {
        var k = 0;
        var length = f.elements.length;

        for(i=0; i<length; i++) {
            if (f.elements[i].checked) {
                k++;
            }
        }

        if(k == 0) {
            alert("상품을 하나 이상 체크 하십시오");
            return false;
        }

        if (act == "direct_buy") {
            f.sw_direct.value = 1;
        } else {
            f.sw_direct.value = 0;
        }

        f.submit();
    }
//-->
</script>
<!-- } 위시리스트 끝 -->

<!-- 공동구매 관련 자바스크립트 라이브러리 -->
<script>
var ca_id = '<?=$ca_id?>'; 
var apm_type = '<?=$apm_type?>';
var topmargin = -100;	//공동구매 레이어팝업 보정
</script>

<script type="text/javascript" src="<?=G5_URL?>/js/group_purchase.js"></script>


<?php
if (G5_IS_MOBILE) {
	include_once(G5_MSHOP_PATH.'/tail.php');
} else {
	include_once(G5_PATH.'/tail.php');
}
?>