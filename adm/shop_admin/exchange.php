<?php
$sub_menu = '400790';
include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$g5['title'] = '사용후기';
include_once (G5_ADMIN_PATH.'/admin.head.php');

// 회원인 경우
$sql_common = " from {$g5['g5_shop_order_table']} as a LEFT JOIN {$g5['g5_shop_cart_table']} as b ON a.od_id=b.od_id where a.mb_id = '{$member['mb_id']}' ";
$sql_common .= " and b.ct_type='' ";
$sql_common .= " and b.ex_status='y' ";

// 테이블의 전체 레코드수만 얻음
$sql = " select count(*) as cnt " . $sql_common;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$sql = " select * from {$g5['g5_shop_order_table']} as a
		 LEFT JOIN {$g5['g5_shop_cart_table']} as b
		 ON a.od_id=b.od_id
		 where b.ct_type=''
		 and b.ex_status='y'
		 $search_que
		 order by a.od_id desc
		 $limit ";

$result = sql_query($sql);
?>

<div class="local_ov01 local_ov">
    <?php echo $listall; ?>
    전체 후기내역 <?php echo $total_count; ?>건
</div>

<form name="flist" class="local_sch01 local_sch">
<input type="hidden" name="page" value="<?php echo $page; ?>">
<input type="hidden" name="save_stx" value="<?php echo $stx; ?>">

<label for="sca" class="sound_only">분류선택</label>
<select name="sca" id="sca">
    <option value=''>전체분류</option>
    <?php
    $sql1 = " select ca_id, ca_name from {$g5['g5_shop_category_table']} order by ca_id ";
    $result1 = sql_query($sql1);
    for ($i=0; $row1=mysql_fetch_array($result1); $i++) {
        $len = strlen($row1['ca_id']) / 2 - 1;
        $nbsp = "";
        for ($i=0; $i<$len; $i++) $nbsp .= "&nbsp;&nbsp;&nbsp;";
        echo "<option value='{$row1['ca_id']}'>$nbsp{$row1['ca_name']}\n";
    }
    ?>
</select>

<label for="sfl" class="sound_only">검색대상</label>
<select name="sfl" id="sfl">
    <option value="it_name" <?php echo get_selected($sfl, 'it_name'); ?>>상품명</option>
    <option value="a.it_id" <?php echo get_selected($sfl, 'a.it_id'); ?>>상품코드</option>
    <option value="is_name" <?php echo get_selected($sfl, 'is_name'); ?>>이름</option>
</select>

<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="stx" value="<?php echo $stx; ?>" required class="frm_input required">
<input type="submit" value="검색" class="btn_submit">

</form>

<form name="fitemuselist" method="post" action="./itemuselistupdate.php" onsubmit="return fitemuselist_submit(this);" autocomplete="off">
<input type="hidden" name="sca" value="<?php echo $sca; ?>">
<input type="hidden" name="sst" value="<?php echo $sst; ?>">
<input type="hidden" name="sod" value="<?php echo $sod; ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl; ?>">
<input type="hidden" name="stx" value="<?php echo $stx; ?>">
<input type="hidden" name="page" value="<?php echo $page; ?>">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 목록</caption>
    <thead>
    <tr>
        <th scope="col">
            종류
        </th>
        <th scope="col">주문일자/주문번호</th>
        <th scope="col">상품정보</th>
        <th scope="col">주문금액(수량)</th>
        <th scope="col">입금액</th>
        <th scope="col">미입금액</th>
        <th scope="col">진행상태</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        $uid = md5($row['od_id'].$row['od_time'].$row['od_ip']);

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
		$cart_row = sql_fetch("select * from {$g5['g5_shop_cart_table']} where od_id='".$row[od_id]."' order by ct_time desc limit 0, 1 ");
		//$cart_num = mysql_num_rows($cart_res);
		//$cart_num = $cart_num - 1;

		$image = get_it_image($row['it_id'], 70, 70);
    ?>

    <tr class="<?php echo $bg; ?>">
        <td style="text-align:center;">
            <?=item_type($row[it_id])?>
        </td>
        <td>
			<?php echo str_replace("-", ".", substr($row['od_time'],0,10)); ?></br>
			<input type="hidden" name="ct_id[<?php echo $i; ?>]" value="<?php echo $row['ct_id']; ?>">
			<?php echo $row['od_id']; ?>
		</td>
        <td>
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td style="border:0px;"><?=$image?></td>
					<td style="border:0px;"><?=$cart_row[it_name]?></td>
				</tr>
			</table>
		</td>
        <td style="text-align:center;">
            <?php echo display_price($row['od_cart_price'] + $row['od_send_cost'] + $row['od_send_cost2']); ?></br>
			(<?php echo $row['od_cart_count']; ?>개)
        </td>
        <td class="td_mngsmall">
            <?php echo display_price($row['od_receipt_price']); ?>
        </td>
        <td class="td_mngsmall"><?php echo display_price($row['od_misu']); ?></td>
        <td class="td_mngsmall">

			<?if($row[ex_status] == "y"){?>
				<div style="color:red;">교환요청중</div>
			<?}?>

        </td>
    </tr>

    <?php
    }

    if ($i == 0) {
        echo '<tr><td colspan="7" class="empty_table">자료가 없습니다.</td></tr>';
    }
    ?>
    </tbody>
    </table>
</div>

<!--
<div class="btn_list01 btn_list">
    <input type="submit" name="act_button" value="선택수정" onclick="document.pressed=this.value">
    <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value">
</div>
-->
</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['PHP_SELF']}?$qstr&amp;page="); ?>

<script>
function fitemuselist_submit(f)
{
    if (!is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
    }

    if(document.pressed == "선택삭제") {
        if(!confirm("선택한 자료를 정말 삭제하시겠습니까?")) {
            return false;
        }
    }

    return true;
}

$(function(){
    $(".use_href").click(function(){
        var $content = $("#use_div"+$(this).attr("target"));
        $(".use_div").each(function(index, value){
            if ($(this).get(0) == $content.get(0)) { // 객체의 비교시 .get(0) 를 사용한다.
                $(this).is(":hidden") ? $(this).show() : $(this).hide();
            } else {
                $(this).hide();
            }
        });
    });
});
</script>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
