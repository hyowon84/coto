<?php
include_once('./_common.php');

if (G5_IS_MOBILE) {
    include_once(G5_MSHOP_PATH.'/wishlist.php');
    return;
}

if (!$is_member)
    goto_url(G5_BBS_URL."/login.php?url=".urlencode(G5_SHOP_URL.'/mypage.php'));

$g5['title'] = "위시리스트";
include_once('./_head.php');
?>

<div id="aside2"></div>

<?include_once("../inc/mypage_menu.php");?>

<div style="background:#fff;">
	<div id="my_tab_nav">
		<ul>
			<li class="on">전체보기</li>
		</ul>
	</div>
	<!-- 위시리스트 탭메뉴 -->

	<div id="my_sub_tab">
		<ul>
			<li class="on" onclick="goto_url('<?=G5_URL?>/shop/wishlist.php');">투데이스토어</li>
			<li class="right" onclick="goto_url('<?=G5_URL?>/shop/wishlist_gp.php');">공동구매</li>
		</ul>
	</div>

	<div id="my_wish_bn">
		<ul>
			<li onclick="return fwishlist_check(document.fwishlist,'');">카트담기</li>
			<li class="seldel">선택상품 삭제</li>
		</ul>
	</div>

	<!-- 위시리스트 시작 { -->
	<div id="sod_ws" style="border-top:2px #545454 solid;">

		<form name="fwishlist" method="post" action="./cartupdate.php">
		<input type="hidden" name="act"       value="multi">
		<input type="hidden" name="sw_direct" value="">
		<input type="hidden" name="prog"      value="wish">
		<input type="hidden" name="w" value="">

		<div class="tbl_head01 tbl_wrap">
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
			$sql  = " select a.wi_id, a.wi_time, b.* from {$g5['g5_shop_wish_table']} a RIGHT join {$g5['g5_shop_item_table']} b on ( a.it_id = b.it_id ) ";
			$sql .= " where a.mb_id = '{$member['mb_id']}' order by a.wi_id desc ";

			$result = sql_query($sql);
			for ($i=0; $row = mysql_fetch_array($result); $i++) {

				$out_cd = '';
				$sql = " select count(*) as cnt from {$g5['g5_shop_item_option_table']} where it_id = '{$row['it_id']}' and io_type = '0' ";
				$tmp = sql_fetch($sql);
				if($tmp['cnt'])
					$out_cd = 'no';

				$it_price = get_price($row);

				if ($row['it_tel_inq']) $out_cd = 'tel_inq';

				$image = get_it_image($row['it_id'], 70, 70);
				$it_point = get_item_point($row);

				$it = sql_fetch("
					select * from {$g5['g5_shop_item_table']}
					where it_id='".$row['it_id']."'
				");
			?>

			<tr style="font-size:12px;">
				<td class="td_chk" style="border-bottom:1px #efefef solid;">
					<?php
					// 품절검사
					//if(is_soldout($row['it_id'])){
					?>
					<!--품절-->
					<?php// } else { //품절이 아니면 체크할수 있도록한다 ?>
					<input type="checkbox" name="chk_it_id[<?php echo $i; ?>]" value="<?php echo $row['it_id']; ?>" onclick="out_cd_check(this, '<?php echo $out_cd; ?>');">
					<?php// } ?>
					<input type="hidden" name="wi_id[<?php echo $row['it_id']; ?>]" value="<?php echo $row['wi_id']; ?>">
					<input type="hidden" name="it_id[<?php echo $i; ?>]" value="<?php echo $row['it_id']; ?>">
					<input type="hidden" name="io_type[<?php echo $row['it_id']; ?>]" value="0">
					<input type="hidden" name="io_id[<?php echo $row['it_id']; ?>]" value="">
					<input type="hidden" name="io_value[<?php echo $row['it_id']; ?>]" value="<?php echo $row['it_name']; ?>">
					<input type="hidden"   name="ct_qty[<?php echo $row['it_id']; ?>]" value="1">
				</td>
				<td class="sod_ws_img" style="border-bottom:1px #efefef solid;"><?php echo $image; ?></td>
				<td style="border-bottom:1px #efefef solid;">
					<a href="./item.php?it_id=<?php echo $row['it_id']; ?>"><?php echo stripslashes($row['it_name']); ?></a></br>
					<span style="color:#f45100;font-style:italic;font-weight:bold;"><?=display_price(get_price($it), $it['it_tel_inq'])?></span>
				</td>
				<td class="td_mngsmall" style="border-left:1px #efefef solid;border-bottom:1px #efefef solid;"><?php echo $row[wi_cnt]; ?></td>
				<td class="td_datetime" style="border-left:1px #efefef solid;border-bottom:1px #efefef solid;background:#faf9f9;"><?php echo date("Y.m.d", strtotime($row['wi_time'])); ?></td>
				<td class="td_mngsmall wish_bn" style="border-left:1px #efefef solid;border-bottom:1px #efefef solid;background:#faf9f9;">
				<?
				if(is_soldout($row['it_id'])){
				?>
					품절
				<?php } else { //품절이 아니면 체크할수 있도록한다 ?>
					<div class="wish_bn buy_bn" style="border:1px #d82d00 solid;background:#ff4c03;color:#fff;" idx="<?=$i?>">구매하기</div>
					<div class="wish_bn cart_bn" style="border:1px #cfcfcf solid;" idx="<?=$i?>">카트담기</div>
					<div class="wish_bn" style="border:1px #cfcfcf solid;" onclick="goto_url('./wishupdate.php?w=d&amp;wi_id=<?php echo $row['wi_id']; ?>');">삭제</div>
				<?php } ?>
					
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
		</form>
	</div>

	<div id="my_wish_bn">
		<ul>
			<li onclick="return fwishlist_check(document.fwishlist,'');">카트담기</li>
			<li class="seldel">선택상품 삭제</li>
			<li style="float:right;color:#82c8ff;font-size:12px;font-style:italic;width:300px;border:0px;">
				<img src="<?=G5_URL?>/img/my_wish_ico.gif"> 찜한 상품 구매시 목록에서 자동으로 삭제됩니다.
			</li>
		</ul>
	</div>
</div>

<script>

$(document).ready(function(){
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

/*
    function out_cd_check(fld, out_cd)
    {
        if (out_cd == 'no'){
            alert("옵션이 있는 상품입니다.\n\n상품을 클릭하여 상품페이지에서 옵션을 선택한 후 주문하십시오.");
            fld.checked = false;
            return;
        }

        if (out_cd == 'tel_inq'){
            alert("이 상품은 전화로 문의해 주십시오.\n\n장바구니에 담아 구입하실 수 없습니다.");
            fld.checked = false;
            return;
        }
    }

    function fwishlist_check(f, act)
    {
        var k = 0;
        var length = f.elements.length;

        for(i=0; i<length; i++) {
            if (f.elements[i].checked) {
                k++;
            }
        }

        if(k == 0)
        {
            alert("상품을 하나 이상 체크 하십시오");
            return false;
        }

        if (act == "direct_buy")
        {
            f.sw_direct.value = 1;
        }
        else
        {
            f.sw_direct.value = 0;
        }

        f.submit();
    }
*/
</script>
<!-- } 위시리스트 끝 -->

<?php
include_once('./_tail.php');
?>