<?php
$sub_menu = '500300';
$sub_sub_menu = '3';

include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$g5['title'] = '주문완료';
include_once (G5_ADMIN_PATH.'/admin.head.php');
include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');

$where = array();

$sql_search = "";
if ($search != "") {
    if ($sel_field != "") {
		switch($sel_field){
			case "it_name":
				$분할_검색어 = explode(" ",$search);
				
				for($s = 0; $s < count($분할_검색어); $s++) {
					$검색조건 .= " AND	it_name LIKE '%$분할_검색어[$s]%' ";
				}
				
				$where[] = " od_id in ( select od_id from {$g5[g5_shop_cart_table]} where 1=1	$검색조건 )";
				break;
			case "mb_nick":
				$where[] = " mb_id in ( select mb_id from {$g5['member_table']} where mb_nick like '%".$search."%' ) ";
				break;
			
			case "od_wearing_cnt":
				$where[] = " od_wearing_cnt > 0 ";
				break;

			default :
		        $where[] = " $sel_field like '%$search%' ";
				break;
		}
    }

    if ($save_search != $search) {
        $page = 1;
    }
}

$where[] = " gp_code <> '' and od_cart_price > 0";

if ($od_settle_case) {
    $where[] = " od_settle_case = '$od_settle_case' ";
}

if($od_status){
   $where[] = " od_status = '$od_status' ";
}

if ($fr_date && $to_date) {
    $where[] = " od_time between '$fr_date 00:00:00' and '$to_date 23:59:59' ";
}


if($sfl_code2 != ""){
	$where[] = " gp_code ='".$sfl_code2."' ";
}


if ($where) {
    $sql_search = ' where '.implode(' and ', $where);
}

if ($sel_field == "")  $sel_field = "od_id";

if (!$sst) {
    $sst = "od_id";
    $sod = "desc";
}
$sql_order = " order by {$sst} {$sod} ";


$sql_common = " from {$g5['g5_shop_order_table']} $sql_search $sql_group ";

$sql = " select count(od_id) as cnt, sum(od_cart_price + od_send_cost + od_send_cost2) as total_price " . $sql_common;
$row = sql_fetch($sql);
$total_count = $row['cnt'];
$total_price = $row['total_price'];

$rows = $config['cf_page_rows'];	//default : 200개 DB설정값
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql  = " select *,
            (od_cart_coupon + od_coupon + od_send_coupon) as couponprice, ( case when od_tax <> '' then '1' else '0' end ) as od_tax_sort
           $sql_common 
           $sql_order 
           limit $from_record, $rows ";

$result = sql_query($sql);

$qstr1 = "sel_field=$sel_field&amp;sfl_code=$sfl_code&amp;fr_date=$fr_date&amp;to_date=$to_date&amp;sfl_code2=$sfl_code2&amp;od_status=$od_status&amp;search=$search&amp;save_search=$search";
$qstr = "$qstr1&amp;sort1=$sort1&amp;sort2=$sort2&amp;page=$page";

$listall = '<a href="'.$_SERVER['PHP_SELF'].'" class="ov_listall">전체목록</a>';

?>

<div class="local_ov01 local_ov">
    <?=$listall;?>
    전체 주문내역 <?=number_format($total_count);?>건 
	 | 주문 총 금액 <span style="color:blue"><?=number_format($total_price);?></span> 원
    <?php if($od_status == '상품준비중' && $total_count > 0) {?>
    <a href="./orderdelivery.php" id="order_delivery" class="ov_a">엑셀배송처리</a>
    <?php }?>
</div>


<form name="forderlist2" id="forderlist2" class="local_sch02 local_sch">
<input type="hidden" name="doc" value="<?=$doc;?>">
<input type="hidden" name="sst" value="<?=$sst;?>">
<input type="hidden" name="sod" value="<?=$sod;?>">
<input type="hidden" name="sort1" value="<?=$sort1;?>">
<input type="hidden" name="sort2" value="<?=$sort2;?>">
<input type="hidden" name="page" value="<?=$page;?>">
<input type="hidden" name="save_search" value="<?=$search;?>">

<div class="sch_last">
	<strong>주문상태</strong>
	<select name="od_status" id="od_status">
		<option value="">전체</option>
		<option value="입금대기" <?=get_selected($od_status, '입금대기');?>>입금대기</option>
		<option value="결제완료" <?=get_selected($od_status, '결제완료');?>>결제완료</option>
		<option value="상품준비중" <?=get_selected($od_status, '상품준비중');?>>상품준비중</option>
		<option value="배송중" <?=get_selected($od_status, '배송중');?>>배송중</option>
		<option value="배송완료" <?=get_selected($od_status, '배송완료');?>>배송완료</option>
	</select>
	&nbsp;&nbsp;&nbsp;
	<strong>공동구매코드</strong>
	<select name="sfl_code">
		<option value="">1차 분류</option>
		<option value="2010" <?if($sfl_code == "2010"){echo "selected";}?>>APMEX</option>
		<option value="2020" <?if($sfl_code == "2020"){echo "selected";}?>>GAINSVILLE</option>
		<option value="2030" <?if($sfl_code == "2030"){echo "selected";}?>>MCM</option>
		<option value="2040" <?if($sfl_code == "2040"){echo "selected";}?>>SCOTTS DALE</option>
		<option value="2050" <?if($sfl_code == "2050"){echo "selected";}?>>OTHER DEALER</option>
	</select>
	<select name="sfl_code2">
		<option value="">2차 분류</option>
	</select>
	&nbsp;&nbsp;&nbsp;
    <strong>주문일자</strong>
    <input type="text" id="fr_date"  name="fr_date" value="<?=$fr_date;?>" class="frm_input" size="10" maxlength="10"> ~
    <input type="text" id="to_date"  name="to_date" value="<?=$to_date;?>" class="frm_input" size="10" maxlength="10">
    <button type="button" onclick="javascript:set_date('오늘');">오늘</button>
    <button type="button" onclick="javascript:set_date('어제');">어제</button>
    <button type="button" onclick="javascript:set_date('이번주');">이번주</button>
    <button type="button" onclick="javascript:set_date('이번달');">이번달</button>
    <button type="button" onclick="javascript:set_date('지난주');">지난주</button>
    <button type="button" onclick="javascript:set_date('지난달');">지난달</button>
    <button type="button" onclick="javascript:set_date('전체');">전체</button>
	&nbsp;&nbsp;&nbsp;
	<select name="sel_field" id="sel_field">
		<option value="it_name" <?=get_selected($sel_field, 'it_name');?>>상품명</option>
		<option value="od_name" <?=get_selected($sel_field, 'od_name');?>>주문자</option>
		<option value="mb_nick" <?=get_selected($sel_field, 'mb_nick');?>>닉네임</option>
		<option value="od_wearing_cnt" <?=get_selected($sel_field, 'od_wearing_cnt');?>>미입고(유)</option>
		<option value="gp_code" <?=get_selected($sel_field, 'gp_code');?>>공동구매코드</option>
	</select>

	<label for="search" class="sound_only">검색어</label>
	<input type="text" name="search" value="<?=$search;?>" id="search" class="frm_input" autocomplete="off">

    <input type="submit" value="검색" class="btn_submit">
</div>
</form>

<div style="position:absolute;margin:-68px 0 0 0;text-align:right;top:189px;right:0;padding:20px;">
	<div class="order_tax_excel" style="display:inline-block;padding:10px;border:1px solid #ccc;background:#f0f0f0;text-decoration:none;cursor:pointer">현금영수증다운로드</div>
	<div class="order_excel_all" style="display:inline-block;padding:10px;border:1px solid #ccc;background:#f0f0f0;text-decoration:none;cursor:pointer">엑셀다운(전체)</div>
	<div class="order_excel_page" style="display:inline-block;padding:10px;border:1px solid #ccc;background:#f0f0f0;text-decoration:none;cursor:pointer">엑셀다운(<?=$page?>page)</div>
</div>


<form name="forderlist3" id="forderlist3" method="post" autocomplete="off">
<input type="hidden" name="sel_field" value="<?=$sel_field;?>">
<input type="hidden" name="sst" value="<?=$sst;?>">
<input type="hidden" name="sod" value="<?=$sod;?>">
<input type="hidden" name="mode">
<input type="hidden" name="page" value="<?=$page?>">
<input type="hidden" name="od_status" value="<?=$od_status?>">
<input type="hidden" name="save_search" value="<?=$save_search?>">
<input type="hidden" name="search" value="<?=$search?>">
<input type="hidden" name="fr_date" value="<?=$fr_date?>">
<input type="hidden" name="to_date" value="<?=$to_date?>">
<input type="hidden" name="sfl_code" value="<?=$sfl_code?>">
<input type="hidden" name="sfl_code2" value="<?=$sfl_code2?>">
</form>

<form name="forderlist" id="forderlist" onsubmit="return forderlist_submit(this);" method="post" autocomplete="off">
<input type="hidden" name="sel_field" value="<?=$sel_field;?>">
<input type="hidden" name="sst" value="<?=$sst;?>">
<input type="hidden" name="sod" value="<?=$sod;?>">
<input type="hidden" name="mode">
<input type="hidden" name="page" value="<?=$page?>">
<input type="hidden" name="od_status" value="<?=$od_status?>">
<input type="hidden" name="save_search" value="<?=$save_search?>">
<input type="hidden" name="search" value="<?=$search?>">
<input type="hidden" name="fr_date" value="<?=$fr_date?>">
<input type="hidden" name="to_date" value="<?=$to_date?>">
<input type="hidden" name="sfl_code" value="<?=$sfl_code?>">
<input type="hidden" name="sfl_code2" value="<?=$sfl_code2?>">


<div class="local_cmd01 local_cmd">
    <label for="od_status" class="cmd_tit">주문상태 변경</label>
	
	<select name="od_chg_status" id="od_chg_status">
		<option value="입금대기">입금대기</option>
		<option value="결제완료">결제완료</option>
		<option value="상품준비중">상품준비중</option>
		<option value="배송중">배송중</option>
		<option value="배송완료">배송완료</option>
	</select>

    <input type="submit" value="선택수정" class="btn_submit" onclick="document.pressed=this.value">
    <input type="submit" value="선택삭제" class="btn_submit" onclick="document.pressed=this.value">
    <input type="submit" value="선택SMS" class="btn_sms01" onclick="document.pressed=this.value">
	<input type="submit" value="우체국엑셀" class="btn_sms01" onclick="document.pressed=this.value">
</div>

<div class="tbl_head01 tbl_wrap">
    <table id="sodr_list">
    <caption>주문 내역 목록</caption>
    <thead>
    <tr>
		<th scope="col">
            <label for="chkall" class="sound_only">주문 전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall" onclick="check_all(this.form)">
        </th>
        <th scope="col" width='600' id="th_gpcode"><?=subject_sort_link('gp_code')?>공동구매코드</a></th>
        <th scope="col" id="th_nick"><?=subject_sort_link('mb_id')?>닉네임</a></th>
        <th scope="col" id="th_odrer">주문자</th>
        <th scope="col" id="th_odrertel"><?=subject_sort_link('od_wearing_cnt')?>미입고유무</a></th>
        <th scope="col">주문금액</th>
		<th scope="col">배송금액</th>
        <th scope="col"><?=subject_sort_link('od_cart_price')?>총금액</a></th>
        <th scope="col">진행상태</th>
        <th scope="col">결제정보</th>
        <th scope="col">운송장번호</th>
        <!-- th scope="col">현금영수증</th>
        <th scope="col"><?=subject_sort_link('od_tax_sort')?>발행유무</a></th -->
		<th scope="col">보기</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++)
    {


        $uid = md5($row['od_id'].$row['od_time'].$row['od_ip']);

        $invoice_time = is_null_time($row['od_invoice_time']) ? G5_TIME_YMDHIS : $row['od_invoice_time'];
        $delivery_company = $row['od_delivery_company'] ? $row['od_delivery_company'] : $default['de_delivery_company'];

        $bg = 'bg'.($i%2);
        $td_color = 0;
        if($row['od_cancel_price'] > 0) {
            $bg .= 'cancel';
            $td_color = 1;
        }

		$od_status_color = "";
		switch($row['od_status']){
			case "입금대기":
				$od_status_color = "red";
				break;
			case "결제완료":
			case "상품준비중":
				$od_status_color = "green";
				break;
			case "배송중":
			case "배송완료":
				$od_status_color = "blue";
				break;
			
		}

		
		$total_notstocked_txt = "-";
		if($row['od_wearing_cnt'])$total_notstocked_txt = "유(".$row['od_wearing_cnt'].")";

		$od_tax = "미발행";
		if($row['od_tax']=="1")$od_tax = "지출증빙용<br>[ ".$row['tax_status'].":".$row['od_tax_hp']." ]";
		elseif($row['od_tax']=="0")$od_tax = "현금영수증<br>[ ".$row['tax_status'].":".$row['od_tax_hp']." ]";


		$mb = get_member($row['mb_id']);
		$mb_nick = get_sideview($mb['mb_id'], $mb['mb_nick'], $mb['mb_email'], '');

		// 금액 없을때 배송비 0원
		if($row['od_cart_price']<=0){
			sql_query("update {$g5['g5_shop_order_table']} set od_send_cost = 0, od_send_cost2 = 0 where od_id = '".$row['od_id']."'");
			$row['od_send_cost'] = $row['od_send_cost2'] = 0;
		}
   ?>
    <tr class="orderlist<?=' '.$bg;?>">
		<td class="td_chk">
        <input type="hidden" name="od_id[<?=$i?>]" value="<?=$row['od_id']?>" id="od_id_<?=$i?>">
        <label for="chk_<?=$i;?>" class="sound_only">주문번호 <?=$row['od_id'];?></label>
        <input type="checkbox" name="chk[]" value="<?=$i?>" id="chk_<?=$i?>">
      </td>
        <td headers="th_ordnum" class="td_odrnum2" style='text-align:left;'>
        	<?=$row['gp_code']?>
        	<? 
        	$cart_sql = "	SELECT	*
        							FROM		g5_shop_cart
        							WHERE	od_id = '$row[od_id]'
        							ORDER BY	io_type asc, ct_id ASC
        	";
			$cart_result = sql_query($cart_sql);
			while($cart = mysql_fetch_array($cart_result)) {
        		echo "<br>".$cart[it_name]." | 수량 ".$cart[ct_qty]."개";
			}
        	?>        	
        </td>
        <td headers="th_nick" class="td_name"><?=$mb_nick;?></td>
        <td headers="th_odrer" class="td_name"><?=$row['od_name'];?></td>
        <td headers="th_recvr" class="td_name" style="color:#ff0000"><?=$total_notstocked_txt;?></td>
		<td class="td_numsum"><?=number_format($row['od_cart_price']);?></td>
		<td class="td_numsum"><?=number_format($row['od_send_cost'] + $row['od_send_cost2']);?></td>
        <td class="td_numsum"><?=number_format($row['od_cart_price'] + $row['od_send_cost'] + $row['od_send_cost2']);?></td>
        <td class="td_numincome"<?php if($od_status_color)echo " style='color:$od_status_color'";?>><?=$row['od_status'];?></td>
        <td class="td_numincome2"><?=$row['od_settle_case'];?></td>
        <td class="td_numcancel"><input type="text" name="od_invoice[<?=$i?>]" id="od_invoice_<?=$i?>"  class="frm_input" value="<?=$row['od_invoice'];?>">
		<span class="btn_chg01 btn_chg">
			<a href="#" onclick="chg_invoice('<?=$row['od_id']?>','<?=$i;?>');return false;">변경</a>
		</span>
		</td>
        <!-- td class="td_numcancel"><?=$od_tax;?></td>
		<td class="td_mngsmall"><?php if($row['od_tax']<>'') echo getPurchaseGroupTaxSelect("od_tax_state", $row['od_tax_state'],' class="od_tax_state" od_idx="'.$row['od_id'].'"'); else echo "-";?></td-->
        <td class="td_mngsmall"><a href="./orderform.php?od_id=<?=$row['od_id'];?>&amp;<?=$qstr;?>" class="mng_mod"><span class="sound_only"><?=$row['od_id'];?> </span>보기</a></td>
    </tr>
    <?php
    }
    mysql_free_result($result);
    if ($i == 0)
        echo '<tr><td colspan="12" class="empty_table">자료가 없습니다.</td></tr>';
   ?>
    </tbody>
    </table>
</div>
</form>

<form name="invoice_frm" id="invoice_frm" method="post" action="order_invoice_update.php" target="hiddenframe">
<input type="hidden" name="od_id">
<input type="hidden" name="od_invoice">
</form>

<?=get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['PHP_SELF']}?$qstr&amp;page=");?>

<script>
$(function(){
    $("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", maxDate: "+0d" });

    // 주문상품보기
    $(".orderitem").on("click", function() {
        var $this = $(this);
        var od_id = $this.text().replace(/[^0-9]/g, "");

        if($this.next("#orderitemlist").size())
            return false;

        $("#orderitemlist").remove();

        $.post(
            "./ajax.orderitem.php",
            { od_id: od_id },
            function(data) {
                $this.after("<div id=\"orderitemlist\"><div class=\"itemlist\"></div></div>");
                $("#orderitemlist .itemlist")
                    .html(data)
                    .append("<div id=\"orderitemlist_close\"><button type=\"button\" id=\"orderitemlist-x\" class=\"btn_frmline\">닫기</button></div>");
            }
        );

        return false;
    });

    // 상품리스트 닫기
    $(".orderitemlist-x").on("click", function() {
        $("#orderitemlist").remove();
    });

    $("body").on("click", function() {
        $("#orderitemlist").remove();
    });

	

	$(".order_tax_excel").click(function(){
		$("form[name='forderlist3']").attr("action", "./orderlist_tax_excel.php").submit();
	});

	//엑셀 다운로드
	$(".order_excel_page").click(function(){
		$("form[name='forderlist3']").attr("action", "./orderlist_excel.php?page="+"<?=$page?>").submit();
	});

	$(".order_excel_all").click(function(){
		$("form[name='forderlist3']").attr("action", "./orderlist_excel.php").submit();
	});
	
    // 엑셀배송처리창
    $("#order_delivery").on("click", function() {
        var opt = "width=600,height=450,left=10,top=10";
        window.open(this.href, "win_excel", opt);
        return false;
    });

	<?if($sfl_code){?>
	$.ajax({
		type: "POST",
		dataType: "HTML",
		url: "../shop_admin/_Ajax.grouppurchase_appli_list.php",
		data: "status=gpcode_status&gp_code=<?=$sfl_code?>&sfl_code2=<?=$sfl_code2?>",
		success: function(data){
			//$(".test").html(data);
			$("select[name='sfl_code2']").html(data);
		}
	});
	<?}?>


	$("select[name='sfl_code']").change(function(){
		var gp_code = $(this).val();
		$.ajax({
			type: "POST",
			dataType: "HTML",
			url: "../shop_admin/_Ajax.grouppurchase_appli_list.php",
			data: "status=gpcode_status&gp_code=" + gp_code,
			success: function(data){
				//$(".test").html(data);
				$("select[name='sfl_code2']").html(data);
			}
		});
	});

	$(".od_tax_state").change(function(){
		var od_idx = $(this).attr("od_idx");
		var od_tax_state = $(this).val();
		$.ajax({
			type: "POST",
			dataType: "HTML",
			url: "_Ajax.orderlist_tax_update.php",
			data: "od_idx=" + od_idx+"&od_tax_state=" + od_tax_state,
			success: function(data){
			}
		});
	});
});

function chg_invoice(od_id,idx)
{
	var f = document.invoice_frm;

	f.od_id.value = od_id;
	f.od_invoice.value = $("#od_invoice_"+idx).val();
	f.submit();
}

function set_date(today)
{
    <?php
    $date_term = date('w', G5_SERVER_TIME);
    $week_term = $date_term + 7;
    $last_term = strtotime(date('Y-m-01', G5_SERVER_TIME));
   ?>
    if (today == "오늘") {
        document.getElementById("fr_date").value = "<?=G5_TIME_YMD;?>";
        document.getElementById("to_date").value = "<?=G5_TIME_YMD;?>";
    } else if (today == "어제") {
        document.getElementById("fr_date").value = "<?=date('Y-m-d', G5_SERVER_TIME - 86400);?>";
        document.getElementById("to_date").value = "<?=date('Y-m-d', G5_SERVER_TIME - 86400);?>";
    } else if (today == "이번주") {
        document.getElementById("fr_date").value = "<?=date('Y-m-d', strtotime('-'.$date_term.' days', G5_SERVER_TIME));?>";
        document.getElementById("to_date").value = "<?=date('Y-m-d', G5_SERVER_TIME);?>";
    } else if (today == "이번달") {
        document.getElementById("fr_date").value = "<?=date('Y-m-01', G5_SERVER_TIME);?>";
        document.getElementById("to_date").value = "<?=date('Y-m-d', G5_SERVER_TIME);?>";
    } else if (today == "지난주") {
        document.getElementById("fr_date").value = "<?=date('Y-m-d', strtotime('-'.$week_term.' days', G5_SERVER_TIME));?>";
        document.getElementById("to_date").value = "<?=date('Y-m-d', strtotime('-'.($week_term - 6).' days', G5_SERVER_TIME));?>";
    } else if (today == "지난달") {
        document.getElementById("fr_date").value = "<?=date('Y-m-01', strtotime('-1 Month', $last_term));?>";
        document.getElementById("to_date").value = "<?=date('Y-m-t', strtotime('-1 Month', $last_term));?>";
    } else if (today == "전체") {
        document.getElementById("fr_date").value = "";
        document.getElementById("to_date").value = "";
    }
}
</script>

<script>
function forderlist_submit(f)
{
    if (!is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
    }

    /*
    switch (f.od_status.value) {
        case "" :
            alert("변경하실 주문상태를 선택하세요.");
            return false;
        case '입금대기' :

        default :

    }
    */

    if(document.pressed == "선택삭제") {
        if(confirm("선택한 자료를 정말 삭제하시겠습니까?")) {
            f.action = "./orderlistdelete.php";
            return true;
        }
        return false;
	}else if(document.pressed == "우체국엑셀"){
	
		f.action = "./orderlist_tax_select_excel.php";
        return true;
    }else  if(document.pressed == "선택SMS") {
		window.open("","_sms","left=10,top=10,width=500,height=600");
		f.target="_sms";
		f.action = "<?=G5_PLUGIN_URL?>/sms5/grouporder.php";
		return true;
    }

    var change_status = f.od_chg_status.value;


    if (!confirm("선택하신 주문서의 주문상태를 '"+change_status+"'상태로 변경하시겠습니까?"))
        return false;

    f.action = "./orderlistupdate.php";
    return true;
}

function forderlist_sms(f)
{
    if (!is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
    }

    if(document.pressed == "선택삭제") {
        if(confirm("선택한 자료를 정말 삭제하시겠습니까?")) {
            f.action = "./orderlistdelete.php";
            return true;
        }
        return false;
    }

    var change_status = f.od_chg_status.value;


    if (!confirm("선택하신 주문서의 주문상태를 '"+change_status+"'상태로 변경하시겠습니까?"))
        return false;

    f.action = "./orderlistupdate.php";
    return true;
}
</script>
<!-- 새창 대신 사용하는 iframe -->
<iframe width=0 height=0 name='hiddenframe' style='display:none;'></iframe>
<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
