<?php
include_once('./_common.php');

if (!$is_member)
	goto_url(G5_BBS_URL."/login.php?url=".urlencode(G5_SHOP_URL.'/mypage.php'));

$g5['title'] = "위시리스트";
include_once(G5_MSHOP_PATH.'/head.php');
?>

<div id="sod_ws">

	<form name="fwishlist" method="post" action="./cartupdate.php">
	<input type="hidden" name="act" value="multi">
	<input type="hidden" name="sw_direct" value="">
	<input type="hidden" name="prog" value="wish">

	<div class="tbl_wrap tbl_head01">
		<table>
		<thead>
		<tr>
			<th scope="col">이미지</th>
			<th scope="col">상품명</th>
			<th scope="col">선택</th>
			<th scope="col">삭제</th>
		</tr>
		</thead>
<!--  \ <tbody>-->
		<?php
		$sql = "	SELECT	a.wi_id,
								a.wi_time,
								b.*
					FROM		{$g5['g5_shop_wish_table']} a
								left join {$g5['g5_shop_item_table']} b ON ( a.it_id = b.it_id )
					WHERE		a.mb_id = '{$member['mb_id']}'
					ORDER	BY	a.wi_id desc ";
		$result = sql_query($sql);
		
		for ($i=0; $row = mysql_fetch_array($result); $i++) {

			$out_cd = '';
			$sql = " select count(*) as cnt from {$g5['g5_shop_item_option_table']} where it_id = '{$row['it_id']}' and io_type = '0' ";
			$tmp = sql_fetch($sql);
			if($tmp['cnt'])
				$out_cd = 'no';

			$it_price = get_price($row);

			if ($row['it_tel_inq']) $out_cd = 'tel_inq';

			$image = get_it_image($row['it_id'], 50, 50);
			$it_point = get_item_point($row);
		?>

		<tr>
			<td class="sod_ws_img"><?=$image;?></td>
			<td>
				<a href="<?=G5_SHOP_URL;?>/item.php?it_id=<?=$row['it_id'];?>"><?=stripslashes($row['it_name']);?></a>
				<br><small>보관일 <?=substr($row['wi_time'], 2, 8);?></small>
			</td>
			<td class="td_chk">
				<?php
				// 품절검사
				if(is_soldout($row['it_id']))
				{
				?>
				품절
				<?php } else { //품절이 아니면 체크할수 있도록한다?>
				<input type="checkbox" name="chk_it_id[<?=$i;?>]" value="1" onclick="out_cd_check(this, '<?=$out_cd;?>');">
				<?php }?>
				<input type="hidden" name="it_id[<?=$i;?>]" value="<?=$row['it_id'];?>">
				<input type="hidden" name="io_type[<?=$row['it_id'];?>][0]" value="0">
				<input type="hidden" name="io_id[<?=$row['it_id'];?>][0]" value="">
				<input type="hidden" name="io_value[<?=$row['it_id'];?>][0]" value="<?=$row['it_name'];?>">
				<input type="hidden"   name="ct_qty[<?=$row['it_id'];?>][0]" value="1">
			</td>
			<td class="td_mngsmall"><a href="<?=G5_SHOP_URL;?>/wishupdate.php?w=d&amp;wi_id=<?=$row['wi_id'];?>">삭제</a></td>
		</tr>
		<?php
		}

		if ($i == 0)
			echo '<tr><td colspan="5" class="empty_table">위시리스트가 비었습니다.</td></tr>';
		?>
		</tr>
		</table>
	</div>

	<div id="sod_ws_act">
		<button type="submit" class="btn01" onclick="return fwishlist_check(document.fwishlist,'');">장바구니 담기</button>
		<button type="submit" class="btn02" onclick="return fwishlist_check(document.fwishlist,'direct_buy');">주문하기</button>
	</div>
</div>

<script>
<!--
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

		return true;
	}
//-->
</script>

<?php
include_once(G5_MSHOP_PATH.'/tail.php');
?>