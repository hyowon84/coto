<?php
$sub_menu = '500300';
$sub_sub_menu = '3';

include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$g5['title'] = '공동구매신청합계';
include_once (G5_ADMIN_PATH.'/admin.head.php');

// 분류
$ca_list  = '<option value="">선택</option>'.PHP_EOL;
$sql = " select * from {$g5['g5_shop_category_table']} ";
if ($is_admin != 'super')
    $sql .= " where ca_mb_id = '{$member['mb_id']}' ";
$sql .= " order by ca_id ";
$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++)
{
    $len = strlen($row['ca_id']) / 2 - 1;
    $nbsp = '';
    for ($i=0; $i<$len; $i++) {
        $nbsp .= '&nbsp;&nbsp;&nbsp;';
    }
    $ca_list .= '<option value="'.$row['ca_id'].'">'.$nbsp.$row['ca_name'].'</option>'.PHP_EOL;
}


$sql_common = " FROM	{$g5['g5_shop_group_purchase_group_table']} as pg
											LEFT OUTER JOIN	{$g5['g5_shop_group_purchase_table']} AS gp ON(pg.gp_id = gp.gp_id)
";


$sql_search = "";
if ($search != "") {
    if ($sel_field != ""){
        switch($sel_field){
        	case "gp.gp_name":
        		$분할_검색어 = explode(" ",$search);
        		
        		$검색조건 = " 1=1 ";
        		
        		for($s = 0; $s < count($분할_검색어); $s++) {
        			$검색조건 .= " AND	gp.gp_name LIKE '%$분할_검색어[$s]%' ";
        		}
        	
        		$where[] = " $검색조건 ";
        		break;
         case "pg.gp_wearing":
             $where[] = " pg.gp_wearing > 0";
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

$where[] = " pg.gp_code <> '' ";


if($od_status){
    $where[] = " pg.gp_code in ( select type_code from {$g5['g5_total_amount_table']} where gc_state='$od_status') ";
}


if($sfl_code2 != ""){
    $where[] = " pg.gp_code ='".$sfl_code2."' ";
}


if ($where) {
    $sql_search = ' where '.implode(' and ', $where);
}


// 테이블의 전체 레코드수만 얻음
$sql = "	SELECT	count(pg.gp_id) as cnt
				$sql_common
				$sql_search
";


$row = sql_fetch($sql);
$total_count = $row['cnt'];

// $rec_qty = 100;
$rows = ($rec_qty) ? $rec_qty : '30';

$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

if (!$sst) {
    $sst  = "pg.gp_datetime";
    $sod = "desc";
}
$sql_order = "order by $sst $sod";

$sql = "	SELECT	pg.*,
								gp.ca_id,
								gp.gp_name,
								gp.gp_site
				$sql_common
				$sql_search
				$sql_order
				LIMIT	$from_record, $rows
";
echo "<textarea style='height:300px;'>$sql</textarea>";
$result = sql_query($sql);

//$qstr  = $qstr.'&amp;sca='.$sca.'&amp;page='.$page;
$qstr1 = "sel_field=$sel_field&amp;sfl_code=$sfl_code&amp;sfl_code2=$sfl_code2&amp;od_status=$od_status&amp;search=$search&amp;save_search=$search";
$qstr = "$qstr1&amp;sort1=$sort1&amp;sort2=$sort2&amp;page=$page";


$listall = '<a href="'.$_SERVER['PHP_SELF'].'" class="ov_listall">전체목록</a>';
?>

<div class="local_ov01 local_ov">
    <?php echo $listall; ?>
    등록된 상품 <?php echo $total_count; ?>건
</div>

<form class="local_sch02 local_sch">
    <input type="hidden" name="doc" value="<?php echo $doc; ?>">
    <input type="hidden" name="sort1" value="<?php echo $sort1; ?>">
    <input type="hidden" name="sort2" value="<?php echo $sort2; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <input type="hidden" name="save_search" value="<?php echo $search; ?>">

    <div class="sch_last">
        <strong>진행상태</strong>
        <select name="od_status" id="od_status">
            <option value="">전체</option>
            <option value="S" <?php echo get_selected($od_status, 'S'); ?>>신청중</option>
            <option value="W" <?php echo get_selected($od_status, 'W'); ?>>집계중</option>
            <option value="E" <?php echo get_selected($od_status, 'E'); ?>>주문완료</option>
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
        <select name="sel_field" id="sel_field">
            <option value="gp.gp_name" <?php echo get_selected($sel_field, 'gp.gp_name'); ?>>상품명</option>
            <option value="pg.gp_wearing" <?php echo get_selected($sel_field, 'pg.gp_wearing'); ?>>미입고(유)</option>
            <option value="pg.gp_code" <?php echo get_selected($sel_field, 'pg.gp_code'); ?>>공동구매코드</option>
        </select>

        <label for="search" class="sound_only">검색어</label>
        <input type="text" name="search" value="<?php echo $search; ?>" id="search" class="frm_input" autocomplete="off">

        <input type="submit" value="검색" class="btn_submit">
        
      	화면출력설정 : 
        <select name='rec_qty' onchange='move_page(this.value)'>
        	<option value='30' <?=($rec_qty == 30) ? 'selected' : ''?>>30개</option>
        	<option value='60' <?=($rec_qty == 60) ? 'selected' : ''?>>60개</option>
        	<option value='100' <?=($rec_qty == 100) ? 'selected' : ''?>>100개</option>
        	<option value='200' <?=($rec_qty == 200) ? 'selected' : ''?>>200개</option>        	
        </select>
        
    </div>
</form>

<script>
function move_page(rec_qty) {
	location.href = '<?="$_SERVER[PHP_SELF]?$qstr&amp;page=$page&rec_qty="?>'+rec_qty;
}
</script>

<form name="fitemlistupdate" method="post" autocomplete="off">
<input type="hidden" name="sel_field" value="<?php echo $sel_field; ?>">
<input type="hidden" name="sst" value="<?php echo $sst; ?>">
<input type="hidden" name="sod" value="<?php echo $sod; ?>">
<input type="hidden" name="mode">
<input type="hidden" name="page" value="<?=$page?>">
<input type="hidden" name="save_search" value="<?=$save_search?>">
<input type="hidden" name="od_status" value="<?=$od_status?>">
<input type="hidden" name="search" value="<?=$search?>">
<input type="hidden" name="fr_date" value="<?=$fr_date?>">
<input type="hidden" name="to_date" value="<?=$to_date?>">
<input type="hidden" name="sfl_code" value="<?=$sfl_code?>">
<input type="hidden" name="sfl_code2" value="<?=$sfl_code2?>">
<input type="hidden" name="od_status" value="<?php echo $od_status; ?>">
</form>


<div class="btn_add01 btn_add">

    <div style="clear:both;">
        <div style="float:right;margin-bottom:10px;">
            <a href="#" class="go_excel">엑셀다운로드</a>
        </div>
    </div>
</div>


<div class="tbl_head01 tbl_wrap">
    <table>
        <caption><?php echo $g5['title']; ?> 목록</caption>
        <thead>
        <tr>
            <th scope="col" width="150px">공동구매 코드</th>
            <th scope="col" width="150px">브랜드</th>
            <th scope="col" id="th_img" width="100px">이미지</th>
            <th scope="col" id="th_pc_title">상품명</th>
            <th scope="col" width="70px">신청수량</th>
            <th scope="col" width="70px">품절수량</th>
            <th scope="col" width="70px">주문수량</th>
            <th scope="col" width="70px">미입고수량</th>
            <th scope="col" width="70px">입고수량</th>
            <th scope="col" width="70px">진행상태</th>
        </tr>
        </thead>
        <tbody>
        <?php
        for ($i=0; $row=mysql_fetch_array($result); $i++)
        {
            $href = G5_SHOP_URL.'/grouppurchase.php?gp_id='.$row['gp_id'];
            $bg = 'bg'.($i%2);

            if($row[ca_id] == "2010"){
                $ct_type = "APMEX";
            }else if($row[ca_id] == "2020"){
                $ct_type = "GAINSVILLE";
            }else if($row[ca_id] == "2030"){
                $ct_type = "MCM";
            }else if($row[ca_id] == "2040"){
                $ct_type = "SCOTTS DALE";
            }else{
                $ct_type = "OTHER DEALER";
            }

            $image = get_gp_image($row['gp_id'], 70, 70);

            $order_qty = $row['gp_cart_qty']-$row['gp_soldout'];
            $notstocked = $order_qty - $row['gp_wearing'];

            if(purchaseItemGorupUpdate($row['gp_code'],$row['gp_id']))continue;
            ?>
            <form name="gp_frm_<?php echo $i; ?>" id="gp_frm_<?php echo $i; ?>" action="grouppurchase_appli_sum_list_update.php" method="post" style="margin:0" target="hiddenframe">
                <input type="hidden" name="w" value="u">
                <input type="hidden" name="gp_code" value="<?php echo $row['gp_code']; ?>">
                <input type="hidden" name="gp_id" value="<?php echo $row['gp_id']; ?>">
                <tr class="<?php echo $bg; ?>">
                    <td class="td_num">
                        <?php echo $row['gp_code']; ?>
                    </td>
                    <td class="td_num">
                        <?php echo $ct_type; ?>
                    </td>
                    <td class="td_img">
                        <?=$image?>
                    </td>
                    <td>
                        <div><a href="#" gp_code="<?php echo $row['gp_code']; ?>" gp_id="<?php echo $row['gp_id']; ?>" class="purchaseqty"><?=cut_str($row[gp_name], 80)?></a></div>
                        <div style="padding:5px 0 0;">(URL : <a href="#" gp_code="<?php echo $row['gp_code']; ?>" gp_id="<?php echo $row['gp_id']; ?>" class="purchaseqty"><?=cut_str($row[gp_site], 50)?></a>) </div>
                    </td>
                    <td class="td_num"><span id="gp_cartqty_<?php echo $i; ?>"><?php echo number_format($row['gp_cart_qty']);?></span></td>
                    <td class="td_num">
                        <label for="gp_soldout_<?php echo $i; ?>" class="sound_only">품절</label>
                        <input type="text" name="gp_soldout" value="<?php echo $row['gp_soldout']; ?>" id="gp_soldout_<?php echo $i; ?>" idx="<?php echo $i; ?>" class="frm_input sit_qty" size="4">
                        <div class="btn_chg01 btn_chg">
                            <a href="#" onclick="chg_wearing('<?php echo $i; ?>');return false;">변경</a>
                        </div>
                    </td>
                    <td class="td_num"><span id="gp_orderqty_<?php echo $i; ?>"><?php echo number_format($order_qty);?></span></td>
                    <td class="td_num">
                        <label for="gp_soldout_<?php echo $i; ?>" class="sound_only">미입고</label>
                        <input type="text" name="gp_wearing" value="<?php echo $row['gp_wearing']; ?>" id="gp_wearing_<?php echo $i; ?>" idx="<?php echo $i; ?>" class="frm_input sit_qty" size="4">
                        <div class="btn_chg01 btn_chg">
                            <a href="#" onclick="chg_wearing('<?php echo $i; ?>');return false;">변경</a>
                        </div>
                    </td>
                    <td class="td_num"><span id="gp_notstocked_<?php echo $i; ?>"><?php echo number_format($notstocked);?></span></td>
                    <td class="td_mngsmall go_status"><?php echo getPurchaseStateText($row['gp_code']);?></td>
                </tr>
            </form>
        <?php
        }
        if ($i == 0)
            echo '<tr><td colspan="11" class="empty_table">자료가 한건도 없습니다.</td></tr>';
        ?>
        </tbody>
    </table>
</div>


<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['PHP_SELF']}?$qstr&amp;page="); ?>

<script type="text/javascript">

    $(document).ready(function(){

        $(".go_excel").click(function(){
			var page = '<?=$page?>';
			var rec_qty = '<?=$rec_qty?>';
            $("form[name='fitemlistupdate']").attr("action", "./grouppurchase_appli_sum_excel.php?page="+page+"&rec_qty="+rec_qty).submit();
        });

    });

    $(function(){

        // 주문상품보기
        $(".purchaseqty").on("click", function() {
            var $this = $(this);
            var gp_code = $(this).attr("gp_code");
            var gp_id = $(this).attr("gp_id");

            if($this.next("#orderitemlist").size())
                return false;

            $("#orderitemlist").remove();

            $.post(
                "./ajax.grouppurchase_qty.php",
                {
                    gp_code: gp_code,
                    gp_id: gp_id

                },
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

        $(".sit_qty").blur(function(){
            var idx = $(this).attr("idx");
            var gp_cartqty = parseInt(removeComma($("#gp_cartqty_"+idx).text()));
            var gp_soldout = parseInt($("#gp_soldout_"+idx).val());
            var gp_orderqty = gp_cartqty - gp_soldout;
            var gp_wearing = parseInt($("#gp_wearing_"+idx).val());
            if(gp_orderqty < gp_wearing){
                gp_wearing = gp_orderqty;
                $("#gp_wearing_"+idx).val(gp_wearing);
            }

            var gp_notstocked = gp_orderqty - gp_wearing;

            $("#gp_orderqty_"+idx).text(gp_orderqty);
            $("#gp_notstocked_"+idx).text(gp_notstocked);

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
    });

    function chg_wearing(idx){

        var gp_cartqty = parseInt(removeComma($("#gp_cartqty_"+idx).text()));
        var gp_soldout = parseInt($("#gp_soldout_"+idx).val());
        var gp_orderqty = gp_cartqty - gp_soldout;
        var gp_wearing = parseInt($("#gp_wearing_"+idx).val());
        if(gp_orderqty < gp_wearing){
            gp_wearing = gp_orderqty;
            $("#gp_wearing_"+idx).val(gp_wearing);
        }

        var gp_notstocked = gp_orderqty - gp_wearing;

        $("#gp_orderqty_"+idx).text(gp_orderqty);
        $("#gp_notstocked_"+idx).text(gp_notstocked);

        $("#gp_frm_"+idx).submit();
    }

    function excelform(url)
    {
        var opt = "width=600,height=450,left=10,top=10";
        window.open(url, "win_excel", opt);
        return false;
    }
</script>

<!-- 새창 대신 사용하는 iframe -->
<iframe width=0 height=0 name='hiddenframe' style='display:none;'></iframe>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
