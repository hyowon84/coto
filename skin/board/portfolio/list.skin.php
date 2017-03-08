<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

chmod("../data/file/portfolio/", 0707);

$cnt = sql_fetch("select SUM(wr_4 * wr_3) as wr_4, SUM(wr_5) as wr_5, SUM(wr_6) as wr_6 from g5_write_portfolio where mb_id='".$member[mb_id]."' ");

$metalUsdPrice = get_session("metalUsdPrice");
?>

<link rel="stylesheet" href="<?php echo $board_skin_url ?>/style.css">

<div id="aside2"></div>

<!-- 게시판 목록 시작 { -->
<div id="bo_gall" style="width:<?php echo $width; ?>">

	<div class="top">
		<ul>
			<li class="title">포트폴리오</li>
			<li class="nav">홈 > 포트폴리오</li>
		</ul>
	</div>


    <div class="bo_fx">
        <div id="bo_list_total">
            <span>Total <?php echo number_format($total_count) ?>건</span>
            <?php echo $page ?> 페이지
        </div>

        <?php if ($rss_href || $write_href) { ?>
        <ul class="btn_bo_user">
            <?php if ($rss_href) { ?><li><a href="<?php echo $rss_href ?>" class="btn_b01">RSS</a></li><?php } ?>
            <?php if ($admin_href) { ?><li><a href="<?php echo $admin_href ?>" class="btn_admin">관리자</a></li><?php } ?>
        </ul>
        <?php } ?>
    </div>


	<!-- 게시물 검색 시작 { -->
	<fieldset id="bo_sch">
		<form name="fsearch" method="get">
		<input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
		<input type="hidden" name="sca" value="<?php echo $sca ?>">
		<input type="hidden" name="sop" value="and">
		<input type="hidden" name="sfl" id="sfl" value="wr_subject">

		<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
		<input type="text" name="stx" value="<?php echo stripslashes($stx) ?>" required id="stx" class="frm_input required" size="15" maxlength="15" style="border:1px #3290bb solid;width:150px;height:33.5px;"><input type="image" src="<?=G5_URL?>/img/search_bn.gif">
		</form>
	</fieldset>
	<!-- } 게시물 검색 끝 -->

	<form name="fboardlist"  id="fboardlist" action="./board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="spt" value="<?php echo $spt ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <input type="hidden" name="sw" value="">

	<?php if ($is_checkbox) { ?>
    <div id="gall_allchk">
        <!--<label for="chkall" class="sound_only">현재 페이지 게시물 전체</label>-->
		<label for="chkall">현재 페이지 게시물 전체</label>
        <input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);">
    </div>
    <?php } ?>

	<div class="cl assets_tile">
		<div style="float:left;width:370px;padding:0 0 7px 0;">
			<?php if ($write_href) { ?><img src="<?=G5_URL?>/img/port_add_bn.gif" border="0" align="absmiddle" style="cursor:pointer;" class="layer_trigger_po"><?}?>
			<!--<img src="<?//=G5_URL?>/img/port_modify_bn.gif" border="0" align="absmiddle" style="cursor:pointer;">-->
		</div>
		<div class="cate1">
			<div style="width:120px;" ca_name=""><img src="<?=G5_URL?>/img/assets_icon.png" border="0" align="absmiddle">&nbsp;&nbsp;&nbsp;&nbsp;모든 METALS</div>
			<div ca_name="GL">금</div>
			<div ca_name="SL">은</div>
			<div ca_name="PT">백금</div>
			<div ca_name="PD">팔라듐</div>
			<div ca_name="other">기타</div>
		</div>
	</div>

	<div class="cl assets_list_box">
		<div class="box">
			<ul>
				<li class="t1"><?php echo date("Y/m/d");?> 현재</li>
				<li class="t2">모든METALS</li>
				<li class="t3"><?php echo number_format($cnt[wr_4]);?>oz</li>
				<li class="t4">투자금액</li>
				<li class="t5">$ <?php echo $cnt[wr_5];?></li>
				<li class="t6">\ <?php echo number_format($cnt[wr_6]);?></li>
			</ul>
		</div>
	</div>

	<div class="cl assets_list">
		<ul>

			<?php for ($i=0; $i<count($list); $i++) {
				if($i>0 && ($i % $bo_gallery_cols == 0))
					$style = 'clear:both;';
				else
					$style = '';

				$it = sql_fetch("select * from {$g5['g5_shop_item_table']} where it_name='".$list[$i]['wr_subject']."' ");
				$gp = sql_fetch("select * from {$g5['g5_shop_group_purchase_table']} where gp_name='".$list[$i]['wr_subject']."' ");

				$price1 = $list[$i]['wr_5'];
				$price2 = get_dollar($it);

				if($list[$i][wr_7] == "N"){
					$price2 = get_dollar($it) * $list[$i][wr_3];
					$price1_ko = $list[$i]['wr_6'];
					$price2_ko = get_price($it) * $list[$i][wr_3];
				}else if($list[$i][wr_7] == "P"){
					$price2 = getGroupPurchaseQtyBasicUSD($gp[gp_id], 1) * $list[$i][wr_3];
					$price1_ko = $list[$i]['wr_6'];
					$price2_ko = getGroupPurchaseQtyBasicPrice($gp[gp_id], 1) * $list[$i][wr_3];
				}

				if ($i == 0) $k = 0;
				$k += 1;
				if ($k % 2 == 0){
			 ?>
			<li>
			<?}else{?>
			<li class="li_l">
			<?}?>
				<div class="top">
					<!--<div class="img" onclick="goto_url('<?php// echo $list[$i]['href'] ?>');">-->
					<div class="img layer_trigger_po_modify" wr_id="<?=$list[$i]['wr_id']?>">
						<?php
						if ($list[$i]['is_notice']) { // 공지사항  ?>
							<strong style="width:<?php echo $board['bo_gallery_width'] ?>px;height:<?php echo $board['bo_gallery_height'] ?>px">공지</strong>
						<?php } else {
							
							$thumb = $list[$i]['wr_1'];

							if(stristr($thumb, "http")) {
								$img_content = '<img src="'.$thumb.'" alt="'.$thumb.'" width="150px" height="150px">';
							} else {
								$img_content = '<img src="'.G5_URL."/data/file/portfolio/".$thumb.'" alt="'.$thumb.'" width="150px" height="150px">';
							}

							echo $img_content;
						}
						 ?>
					</div>
					<div class="info">
						<div class="title">
							<div style="float:right;margin:0 -5px 0 0;">
								<?php if ($is_checkbox) { ?>
								<label for="chk_wr_id_<?php echo $i ?>" class="sound_only"><?php echo $list[$i]['subject'] ?></label>
								<input type="checkbox" name="chk_wr_id[]" value="<?php echo $list[$i]['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>">
								<?}?>
								<img src="<?=G5_URL?>/img/port_modify_bn.gif" border="0" align="absmiddle" style="cursor:pointer;" class="layer_trigger_po_modify" wr_id="<?=$list[$i]['wr_id']?>">
								<img src="<?=G5_URL?>/img/port_del_bn.png" border="0" align="absmiddle" style="cursor:pointer;" class="port_del" wr_id="<?=$list[$i]['wr_id']?>">
							</div>
							<div class="cl">
								<?php echo $list[$i]['subject'] ?>
							</div>
						</div>
						<div class="info1">
							<span style="float:left;font-weight:bold;">금속</span>
							<span style="float:right;"><?php echo metal_type($list[$i]['wr_2']) ?></span>
							<span class="cl" style="float:left;font-weight:bold;">수량</span>
							<span style="float:right;"><?php echo $list[$i]['wr_3'] ?></span>
							<span class="cl" style="float:left;font-weight:bold;">온스</span>
							<span style="float:right;"><?php echo $list[$i]['wr_4'] ?></span>
						</div>
					</div>
				</div>
				<div class="cl line"></div>
				<div class="bottom">
					<div class="t1">
						<div class="s1">구입가</div>
						<div class="s2">$<?php echo $price1 ?></div>
						<div class="s3">\<?php echo number_format($price1_ko) ?></div>
					</div>
					<div class="t2">
						<div class="s1">현재가</div>
						<div class="s2">$<?php echo $price2 ?></div>
						<div class="s3">\<?php echo number_format($price2_ko)?></div>
					</div>
					<div class="t3">
						<div class="s1">차익</div>
						<div class="s2">$<?php echo round($price2 - $price1, 2);?></div>
						<div class="s3">\<?php echo number_format($price2_ko - $price1_ko);?></div>
					</div>
				</div>
			</li>

			<?
			}
			?>
		</ul>
			
	</div>

	
	<?php// if ($list_href || $is_checkbox || $write_href) { ?>
    <div class="cl bo_fx">
        <?php// if ($is_checkbox) { ?>
        <ul class="btn_bo_adm">
            <li><input type="submit" name="btn_submit" value="선택삭제" onclick="document.pressed=this.value"></li>
            <!--<li><input type="submit" name="btn_submit" value="선택복사" onclick="document.pressed=this.value"></li>
            <li><input type="submit" name="btn_submit" value="선택이동" onclick="document.pressed=this.value"></li>-->
        </ul>
        <?php// } ?>

        <?php if ($list_href || $write_href) { ?>
        <ul class="btn_bo_user">
            <?php if ($list_href) { ?><li><a href="<?php echo $list_href ?>" class="btn_b01">목록</a></li><?php } ?>
        </ul>
        <?php } ?>
    </div>
    <?php// } ?>

	</form>

</div>

<form name="fportdel" id="fportdel" method="POST">
<input type="hidden" name="HTTP_CHK" value="CHK_OK">
<input type="hidden" name="wr_id">
</form>

<?php if($is_checkbox) { ?>
<noscript>
<p>자바스크립트를 사용하지 않는 경우<br>별도의 확인 절차 없이 바로 선택삭제 처리하므로 주의하시기 바랍니다.</p>
</noscript>
<?php } ?>

<!-- 페이지 -->
<?php echo $write_pages;  ?>


<?php if ($is_checkbox) { ?>
<script>
function all_checked(sw) {
    var f = document.fboardlist;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]")
            f.elements[i].checked = sw;
    }
}

function fboardlist_submit(f) {
    var chk_count = 0;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]" && f.elements[i].checked)
            chk_count++;
    }

    if (!chk_count) {
        alert(document.pressed + "할 게시물을 하나 이상 선택하세요.");
        return false;
    }

    if(document.pressed == "선택복사") {
        select_copy("copy");
        return;
    }

    if(document.pressed == "선택이동") {
        select_copy("move");
        return;
    }

    if(document.pressed == "선택삭제") {
        if (!confirm("선택한 게시물을 정말 삭제하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다\n\n답변글이 있는 게시글을 선택하신 경우\n답변글도 선택하셔야 게시글이 삭제됩니다."))
            return false;

        f.removeAttribute("target");
        f.action = "./board_list_update.php";
    }

    return true;
}

// 선택한 게시물 복사 및 이동
function select_copy(sw) {
    var f = document.fboardlist;

    if (sw == 'copy')
        str = "복사";
    else
        str = "이동";

    var sub_win = window.open("", "move", "left=50, top=50, width=500, height=550, scrollbars=1");

    f.sw.value = sw;
    f.target = "move";
    f.action = "./move.php";
    f.submit();
}
</script>
<?php } ?>

<script type="text/javascript">
$(document).ready(function(){
	var layerWindow_po = $('.mw_layer_po');
	var layerWindow_po_modify = $('.mw_layer_po_modify');

	$('.layer_trigger_po').click(function(){
		layerWindow_po.addClass('open_po');
	});
	$('#layer_po .close_po').click(function(){
		layerWindow_po.removeClass('open_po');
	});
	layerWindow_po.find('>.bg_po').mousedown(function(event){
		layerWindow_po.removeClass('open_po');
		return false;
	});

	$('.layer_trigger_po_modify').click(function(){
		var wr_id = $(this).attr("wr_id");
		layerWindow_po_modify.addClass('open_po_modify');

		layerWindow_po_modify.find(".po_con").html("<div class='cl' style='text-align:center;'><img src='<?=G5_URL?>/img/loading.gif' border='0' style='margin:155px 0 0 0;width:40px;height:40px;' align='absmiddle'></div>");

		$.post("../portfolio/_Ajax.portfolio.php", {wr_id : wr_id}, function(data){
			layerWindow_po_modify.find(".po_con").html(data);
		});
	});
	$('#layer_po_modify .close_po_modify').click(function(){
		layerWindow_po_modify.removeClass('open_po_modify');
	});
	layerWindow_po_modify.find('>.bg_po_modify').mousedown(function(event){
		layerWindow_po_modify.removeClass('open_po_modify');
		return false;
	});


	$(".port_del").click(function(){
		var wr_id = $(this).attr("wr_id");
		if(confirm("정말 삭제 하시겠습니까?")){
			$("input:hidden[name='wr_id']").val(wr_id);
			$("form[name='fportdel']").attr("action", "../portfolio/portfolio_del.php").submit();
		}
	});

	$(".port_modify").click(function(){
		
	});

	$(".cate1").find("div").click(function(){
		var ca_name = $(this).attr("ca_name");
		
		$(".cate1").find("div").each(function(i){
			if(ca_name == $(".cate1").find("div").eq(i).attr("ca_name")){
				location.href = "<?=G5_URL?>/bbs/board.php?bo_table=<?=$bo_table?>&ca_name=" + ca_name;
			}
		});
	});
});
</script>
<!-- } 게시판 목록 끝 -->
