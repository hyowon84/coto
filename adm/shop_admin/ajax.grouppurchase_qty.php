<?php
$sub_menu = '500300';
include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$gp_code = $_POST['gp_code'];
$gp_id = $_POST['gp_id'];

$sql = " select gp.*, pg.gp_name from {$g5['g5_shop_group_purchase_group_table']} as gp left join {$g5['g5_shop_group_purchase_table']}  as pg on gp.gp_id = pg.gp_id where gp.gp_code = '$gp_code' and gp.gp_id = '$gp_id' ";
$gp = sql_fetch($sql);

if(!$gp['gp_id'])
    die('<div>정보가 존재하지 않습니다.</div>');

// 상품목록
$sql = " SELECT	ct_id, it_id,mb_id, ct_qty, ct_gp_soldout, ct_wearing_cnt
			FROM		{$g5['g5_shop_cart_table']}
			WHERE		total_amount_code = '$gp_code'
			AND		it_id = '$gp_id'
			AND		ct_status NOT IN ('판매취소')
          order by ct_id desc ";
$result = sql_query($sql);
?>
<form name="frm" id="frm" method="post" action="grouppurchase_qty_update.php" target="hiddenframe">
<input type="hidden" name="gp_code" value="<?php echo $gp_code?>">
<input type="hidden" name="gp_id" value="<?php echo $gp_id?>">
<div id="orderitemlist_change"><button type="button" id="orderitemlist-change" class="btn_frmline2">변경</button></div> 
<section id="cart_list">
    <h2 class="h2_frm">주문상품 목록</h2>
	<div class="gp_item_name"><?php echo $gp['gp_name']?></div>
    <div class="tbl_head01 tbl_wrap">
        <table>
        <caption>주문 상품 목록</caption>
        <thead>
        <tr>
            <th scope="col">No.</th>
            <th scope="col">주문자</th>
            <th scope="col">신청수량</th>
            <th scope="col">주문수량</th>
            <th scope="col">품절수량</th>
            <th scope="col">미입고수량</th>
            <th scope="col">입고수량</th>
        </tr>
        </thead>
        <tbody>
        <?php
        for($i=0; $row=sql_fetch_array($result); $i++) {
			$mb =get_member($row[mb_id]);

			$orderQty = $row['ct_qty']-$row['ct_gp_soldout'];
			$notStocked = $orderQty - $row['ct_wearing_cnt'];
            ?>
			<input type="hidden" name="ct_chk[]" value="<?php echo $i?>">
			<input type="hidden" name="ct_id[<?php echo $i?>]" value="<?php echo $row['ct_id']?>">
            <tr>
				<td class="td_num"><?php echo $row['ct_id']?></td>
                <td class="td_itopt_tl"><?php echo $mb['mb_nick']?></td>
                <td class="td_num"><?php echo number_format($row['ct_qty']); ?></td>
                <td class="td_num"><?php echo number_format($orderQty); ?></td>
                <td class="td_num"><input type="text" name="ct_gp_soldout[<?php echo $i?>]" value="<?php echo $row['ct_gp_soldout']; ?>" id="ct_gp_soldout_<?php echo $i; ?>" class="frm_input sit_qty" size="4"></td>
                <td class="td_num"><input type="text" name="ct_wearing_cnt[<?php echo $i?>]" value="<?php echo $row['ct_wearing_cnt']; ?>" id="ct_wearing_cnt_<?php echo $i; ?>" class="frm_input sit_qty" size="4"></td>
                <td class="td_num"><?php echo number_format($notStocked); ?></td>
            </tr>
            <?php
            }
            ?>
        </tbody>
        </table>
    </div>
</section>
</form>

<script>
$(function(){
	
	$("#orderitemlist-change").on("click",function(){
		$("#frm").submit();
	});
});
</script>