<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가


// 선택옵션으로 인해 셀합치기가 가변적으로 변함
$colspan = 5;

if ($is_checkbox) $colspan++;
if ($is_good) $colspan++;
if ($is_nogood) $colspan++;

$tit_sub = "주요 질문과 답변을 안내해 드립니다.";
?>

<div id="aside2"></div>

<link rel="stylesheet" href="<?php echo $board_skin_url ?>/style.css">

<script>
function ViewContent( ids ){
	$('.answer').hide();
	document.getElementById(ids).style.display = "block";
}
</script>

<div class="title">
	<span><?php echo $board['bo_subject'] ?></span>
	<span style="font-size:14px;font-weight:normal;"><?=$tit_sub?></span>
</div>

<div style="clear:both;" class="list_box">

    <form name="fboardlist" id="fboardlist" action="./board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="spt" value="<?php echo $spt ?>">
    <input type="hidden" name="sca" value="<?php echo $sca ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <input type="hidden" name="sw" value="">
				<ul class="ultable4">
        <?php
        for ($i=0; $i<count($list); $i++) {
         ?>
					<li>
						<?php if ($is_checkbox) { ?>
						
							<label for="chk_wr_id_<?php echo $i ?>" class="sound_only"><?php echo $list[$i]['subject'] ?></label>
							<input type="checkbox" name="chk_wr_id[]" value="<?php echo $list[$i]['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>">
						
						<?php } ?>
						<span style="color:#8eddda;font-size:20px;font-weight:bold;">Q</span>

						<? if ($is_admin == "super") { ?>
								<span style="padding-left:10px;"><a href="<?php echo $list[$i]['href'] ?>"><?php echo $list[$i]['wr_subject'] ?></a></span>
						  <?}else{?>
								<span style="padding-left:10px;"><a href="javascript:;" onClick="ViewContent('li<?=$i?>');"><?php echo $list[$i]['wr_subject'] ?></a></span>
						<?}?>



						
					</li>
					<li class="answer" id="li<?=$i?>">
						<span style="float:left;color:#6b6b6b;font-size:20px;font-weight:bold;">A</span>
						<span style="float:left;padding-left:17px;width:700px;"><?php echo $list[$i]['wr_content']; ?></span>
					</li>
        <?php } ?>
        <?php if (count($list) == 0) { echo '<li>게시물이 없습니다.</li>'; } ?>

				</ul>
	
				<?php echo $write_pages;  ?>

				<div class="confirmbutton">
					<?php if ($list_href || $is_checkbox || $write_href) { ?>
					<div class="bo_fx">
						<?php if ($is_checkbox) { ?>
						<ul class="btn_bo_adm">
							<li><input type="submit" name="btn_submit" value="선택삭제" onclick="document.pressed=this.value"></li>
							<li><input type="submit" name="btn_submit" value="선택복사" onclick="document.pressed=this.value"></li>
							<li><input type="submit" name="btn_submit" value="선택이동" onclick="document.pressed=this.value"></li>
						</ul>
						<?php } ?>

						<?php if ($list_href || $write_href) { ?>
						<ul class="btn_bo_user">
							<?php if ($list_href) { ?><li><a href="<?php echo $list_href ?>" class="btn_b01">목록</a></li><?php } ?>
							<?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b02">글쓰기</a></li><?php } ?>
							<?php if ($admin_href) { ?><li><a href="<?php echo $admin_href ?>" class="btn_admin">관리자</a></li><?php } ?>
						</ul>
						<?php } ?>
					</div>
					<?php } ?>
				</div>
		</form>

</div>


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

    if (sw == "copy")
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
<!-- } 게시판 목록 끝 -->
