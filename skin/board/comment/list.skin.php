<?
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가 

?>
 
<link rel="stylesheet" href="<?php echo $board_skin_url ?>/style.css"> 

<script language="JavaScript">

// 글자수 제한
var char_min = parseInt(<?=$write_min?>); // 최소
var char_max = parseInt(<?=$write_max?>); // 최대
</script>
 
<?

set_session("ss_delete_token", $token = uniqid(time()));

$path = "../data/member";
chmod($path, 0707);

if ($w == "") {
	$is_name = false;
	$is_password = false;

	if (!$member[mb_id] || ($is_admin && $w == 'u' && $member[mb_id] != $write[mb_id])) {
		$is_name = true;
	    $is_password = true;
	}

	$password_required = "required";
	$content = $board[bo_insert_content];

	// 글자수 제한 설정값
	if ($is_admin)
	{
		$write_min = $write_max = 0;
	}
	else
	{
	    $write_min = (int)$board[bo_write_min];
	    $write_max = (int)$board[bo_write_max];
	}
	//include_once("./norobot.inc.php");
}

if (!$subject) $subject = 1;

if($bo_table == "new_family"){
	$sub_com = "환영해요";
	$back_clr = "#56ccc8";
}else{
	$sub_com = "친해져요";
	$back_clr = "#4fc6f8";
}

$com_bef_view_row = sql_fetch("select * from g5_write_".$bo_table." where wr_id='".$no."' ");
?>

<h2 class="bo_title"><?php echo $board['bo_subject'] ?> <span><?=$sub_com?></span></h2>

<div id="aside2"></div>


<div style="background:<?=$back_clr?>;padding:10px;">

<form name="ffwrite" method="post" action="javascript:fwrite_check(document.ffwrite);" enctype="multipart/form-data" autocomplete="off">
<input type=hidden name=w          value="">
<input type=hidden name=bo_table   value="<?=$bo_table?>">
<input type=hidden name=wr_id      value="">
<input type=hidden name=page       value="<?=$page?>">
<input type=hidden name=wr_subject value="<?=$subject?>">
<? if ($is_password) { ?><input type="hidden" name="wr_password"  value="<?=$norobot_str?>"><? } ?>
<? if ($is_norobot) { ?><input type="hidden" name="wr_key"  value="<?=$norobot_key?>"><? } ?>

<table width="<?=$width?>" align="center" cellpadding="0" cellspacing="5">
<tr><td align="center">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr> 

<? if ($is_name) { ?>
<tr>
    <td>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
        	<td>
        		<input class="bbs_ft" maxLength="20" size="13" name="wr_name" itemname="이름" required style="height:23; padding:4;" value="손님" onFocus="clearText(this)"> ※ 손님은 등록만 가능하며 <span style="color:red">수정</span> 및 <span style="color:red">삭제</span>를 할 수 없습니다.
        	</td>

        	<td align="right" class=write_head><img id='kcaptcha_image' border='0' width=120 height=20 onclick="imageClick();" style="cursor:pointer;" title="글자가 잘안보이는 경우 클릭하시면 새로운 글자가 나옵니다."></td>
            <td align="right"><input class='ed' type=input size=10 name=wr_key itemname="자동등록방지" required></td>

        </table>
    </td>
</tr>    
<? } ?>

	
	
	<td>
		<table border="0" cellspacing="0" cellpadding="0" style="width:100%;">
			<tr>
				<td style="width:70px;">
				<?if($member[mb_img]){?>
					<img src="<?=$path?>/<?=$member[mb_img]?>" style="width:45px;height:45px;">
				<?}else{?>
					<img src="<?=G5_URL?>/img/pofile_no.png" style="width:45px;height:45px;">
				<?}?>
				</td>
				<td>
					<textarea class="bbs_ft" name="wr_content" itemname="내용" required style="width:98%;height:60; padding:4;color:#1e1e1e;font-style:italic;" onFocus="clearText(this)">200자 이내로 입력이 가능합니다.</textarea>
				</td>
				<td style="width:50px;padding:0 0 0 10px;color:#fff;">
					<img src="<?=G5_URL?>/img/new_fa_ico.png" border="0" align="absmiddle">
					<input type="submit" id="btn_submit" value="확인" border="0" align="absmiddle" style="background:#56ccc8;border:0px;color:#fff;"></br>
					0/200
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td style="color:#333333;font-weight:bold;text-align:left;"><?=$member[mb_nick]?></td>
				<td>&nbsp;</td>
			</tr>
			<tr height="7px"><td colspan="3"></td></tr>
		</table>
	</td>

</tr>
</table>
</td></tr></table>
</form>

<table width="<?=$width?>" align="center" cellpadding="0" cellspacing="0"><tr><td>
<!-- 게시판 리스트 시작 -->
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="family_tb">

<?

$wr_num = 0;
$tempArr= null;

for ($i=0; $i<count($list); $i++) {
$list_id = $list[$i][wr_id];

$mem_row = sql_fetch("select * from {$g5['member_table']} where mb_id='".$list[$i][mb_id]."'");

//댓글 갯수
$com_cnt = sql_fetch("select * from g5_write_".$bo_table." where wr_id='".$list_id."'");
$com_cnt_row = sql_fetch("select count(*) as cnt from g5_write_".$bo_table." where wr_num='".$com_cnt[wr_num]."'");

if($i == 0) $tempArr .= "\"$list_id\"";
else        $tempArr .= ",\"$list_id\"";

?>
<a name="c_<?=$list_id?>"></a>

<?
// 부모글
if($wr_num != $list[$i][wr_num]){
    
?>
<tr><td colspan="6" style="border-top:1px #fff dashed;"></td></tr>

<tr class="bn bn_<?=abs($list[$i][wr_num])?>" idx="<?=abs($list[$i][wr_num])?>" style="height:60px;cursor:pointer;">
	<td height="25" colspan="5" style="padding:7px 5px;display:block;">

	    <div class="num" style="float:left;">
		<?if($mem_row[mb_img]){?>
			<img src="<?=$path?>/<?=$mem_row[mb_img]?>" style="width:40px;height:40px;">
		<?}else{?>
			<img src="<?=G5_URL?>/img/pofile_no.png" style="width:40px;height:40px;">
		<?}?>
		</div>

		<div id="content_<?= $list_id?>" style="float:left;padding:0 0 0 25px;color:#333333;">
			<a href="javascript:com_view('<?=abs($list[$i][wr_num])?>');" title="답변" class="bbs" style="color:#fff;padding:0 0 0 0;">
				<?= nl2br($list[$i][wr_content])?>
			</a></br>
			<div style="margin:5px 0 0 0;">
				<div style="float:left;font-weight:bold;"><?=$list[$i][name]?></div>
				<div style="float:left;padding:0 0 0 10px;">
					<img src="<?=G5_URL?>/img/new_fa_ico1.png">
					<span style="color:#fff;" onClick="javascript:list_box('<?=$list_id?>', 'r', '<?=abs($list[$i][wr_num])?>');" title="댓글">댓글쓰기[<?=$com_cnt_row[cnt]-1?>]</span>
				</div>
			</div>
		</div>
    </td>
    <td align="right" style="padding-right:5px;color:#fff;">
    <?
	if (($member[mb_id] && ($member[mb_id] == $list[$i][mb_id])) || $is_admin) {
	?>
	    <a href="javascript:list_box('<?=$list_id?>', 'u', '<?=abs($list[$i][wr_num])?>');" style="color:#fff;">수정</a> | 
		<a href="javascript:if (confirm('삭제하시겠습니까?')) { location='./delete.php?w=d&bo_table=<?=$bo_table?>&wr_id=<?=$list[$i][wr_id]?>&token=<?=$token?>&page=<?=$page?>';}" style="color:#fff;">삭제</a></br>
		<span style="color:#545f5e;font-weight:bold;"><?=date("Y.m.d", strtotime($list[$i][wr_datetime]))?></span>
	<? } ?>
	</td>
</tr>

<?
    $Display = "none";
?>
<tr class="dis dis_<?=abs($list[$i][wr_num])?>" idx="<?=abs($list[$i][wr_num])?>" style="display:none;">
	<td colspan="5" class="bbs_pp" style="display:block;">
		<textarea class="bbs_ft" id='save_content_<?=$list_id?>' type=text style='display:<?= $Display ?>;width:98%;height:40; padding:4;' required><?= $list[$i][wr_content]?></textarea>

		<span id='reply_<?=$list_id?>' style='display:<?= $Display ?>; width:100%; padding:5;'></span><!-- 답변 -->
		<span id='edit_<?=$list_id?>' style='display:<?= $Display ?>; width:100%; padding:5;'></span><!-- 수정 -->
	</td>
</tr>

<?

// 코멘트(댓글)
}else{
    $totalCount++;
    
?>
<tr id="comment_<?= $list_id?>" class="dis dis_<?=abs($list[$i][wr_num])?>" idx="<?=abs($list[$i][wr_num])?>" style="display:none;">
    <td colspan="5" style="padding-left : 5px;padding-left:70px;height:25px;">

		<table border="0" cellspacing="0" cellpadding="0" style="color:#064aa7;font-weight:bold;">
			<tr>
				<td>
					<?for($k = 1; $k <= strlen($list[$i][wr_reply]); $k++){echo "&nbsp;&nbsp;&nbsp;";}?>
				</td>
				<td valign="top">
					<?
					//if(strlen($list[$i][wr_reply]) > 1){
						//echo $list[$i][reply];
						echo "<img src='".G5_URL."/img/new_fa_ico2.png' style='margin:-3px 0 0 0;'>";
						//echo $list[$i][icon_reply];
					//}

					?>
				</td>
				<td valign="top"><span class="c_name" style="color:#333;font-weight:bold;"><NOBR><?=$list[$i][name]?></NOBR></span>&nbsp;:&nbsp;</td>
				<td>
					<p style="width:100%;"><?= ($list[$i][wr_content])?>&nbsp;<span class="c_date" style="color:#545f5e;">(<?=$list[$i][datetime]?>)</span>&nbsp;<span class="c_name" style="color:#333;font-weight:bold;"></span></p>
				</td>
				<td width="110px">
					<a href="javascript:list_box('<?=$list_id?>', 'r', '<?=abs($list[$i][wr_num])?>');" title="이 댓글에 댓글달기" class="bbs" style="color:#fff;font-weight:normal;">
						<img src="<?=G5_URL?>/img/new_fa_ico1.png">
						댓글
						<!--<img src="<?php// echo $board_skin_url; ?>/img/new_fa_ico1.png" title="이 댓글에 댓글달기" border="0" align="absmiddle">-->
					</a>
					<?
					if (($member[mb_id] && ($member[mb_id] == $list[$i][mb_id])) || $is_admin) {
					?>
					<a href="javascript:list_box('<?=$list_id?>', 'u', '<?=abs($list[$i][wr_num])?>');" class="bbs" idx="<?=$totalCount?>" style="padding-right:2px;color:#fff;font-weight:normal;">
						수정
						<!--<img src="<?//=$board_skin_url?>/img/btn_edit.gif" title="수정" border="0" align="absmiddle">-->
					</a>
					<a href="javascript:if (confirm('삭제하시겠습니까?')) { location='./delete.php?w=d&bo_table=<?=$bo_table?>&wr_id=<?=$list[$i][wr_id]?>&token=<?=$token?>&page=<?=$page?>';}" class="bbs" style="color:#fff;font-weight:normal;">
						삭제
						<!--<img src="<?//=$board_skin_url?>/img/btn_del.gif" title="삭제" border="0" align="absmiddle">-->
					</a>
					<? } ?>
				</td>
			</tr>
		</table>
			
				
    </td>
</tr>

<?
    $Display = "none";
?>
<tr class="dis dis_<?=abs($list[$i][wr_num])?> save_content_<?= $list_id?>" style="display:none;"> 
	<td colspan="5" class="bbs_pp">
		<img id='save_emoticon_<?=$list_id?>' style='display:<?= $Display ?>;' border="0" src="<?=$board_skin_url?>/emoticons/<?=$list[$i][subject]?>.gif">
		<textarea class="bbs_ft" id='save_content_<?=$list_id?>' type=text style='display:<?= $Display ?>;width:100%;height:40; padding:4;'><?= $list[$i][wr_content]?></textarea>

		<span id='reply_<?=$list_id?>' style='display:<?= $Display ?>; width:100%; padding:5;'></span><!-- 답변 -->
		<span id='edit_<?=$list_id?>' style='display:<?= $Display ?>; width:100%; padding:5;'></span><!-- 수정 -->
	</td>
</tr>
		
<? } ?>


<?
$wr_num = $list[$i][wr_num];

}
?>
<? if (count($list) == 0) { ?>
<tr><td height="100" align="center">게시물이 없습니다.</td></tr>

<? } ?>
</table>


<!-- 페이지 표시 시작 -->
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<? if ($write_pages || $prev_part_href || $next_part_href ) { ?>
<tr>
    <td height="25" align="center" class="bbs_no">
		<? if ($prev_part_href) { echo "<a href='$prev_part_href'><img src='$board_skin_url/img/btn_search_prev.gif' width=50 height=20 border=0 align=absmiddle title='이전검색'></a>"; } ?>
		<?
		// 기본으로 넘어오는 페이지를 아래와 같이 변환하여 이미지로도 출력할 수 있습니다.
		//echo $write_pages;
		$write_pages = str_replace("처음", "<img src='$board_skin_url/img/page_first.gif' border='0' align='absmiddle' title='처음' width='38' height='15'>", $write_pages);
		$write_pages = str_replace("이전", "<img src='$board_skin_url/img/page_prev.gif' border='0' align='absmiddle' title='이전' width='17' height='15'>", $write_pages);
		$write_pages = str_replace("다음", "<img src='$board_skin_url/img/page_next.gif' border='0' align='absmiddle' title='다음' width='17' height='15'>", $write_pages);
		$write_pages = str_replace("맨끝", "<img src='$board_skin_url/img/page_end.gif' border='0' align='absmiddle' title='맨끝' width='38' height='15'>", $write_pages);
		$write_pages = preg_replace("/<span>([0-9]*)<\/span>/", "<font style=\"font-family:돋움; font-size:9pt; color:#797979\">$1</font>", $write_pages);
		$write_pages = preg_replace("/<b>([0-9]*)<\/b>/", "<font style=\"font-family:돋움; font-size:9pt; color:orange;\">$1</font>", $write_pages);
		?>
		<?=$write_pages?>
		<? if ($next_part_href) { echo "<a href='$next_part_href'><img src='$board_skin_url/img/btn_search_next.gif' width=50 height=20 border=0 align=absmiddle title='다음검색'></a>"; } ?>
	</td>
</tr>
<? } ?>

<tr><td colspan="6" style="border-top:1px #fff dashed;"></td></tr>

</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr> 
	<td width="" height="40">
		<? if ($admin_href) { ?><a href='<?=$admin_href?>'><img src="<?=$board_skin_url?>/img/btn_admin.gif" border="0" width="55" height="20" align="absmiddle" title="관리자"></a><?	} ?>
	</td>
</tr>
</table>
</td></tr></table>

<span id=list_write style='display:inline; width:100%; padding:10;'>
<form name="fwrite" method="post" action="javascript:fwrite_check(document.fwrite);" enctype="multipart/form-data" autocomplete="off">
<input type=hidden name=w          value="" id="list_w">
<input type=hidden name=bo_table   value="<?=$bo_table?>">
<input type=hidden name=wr_id      value="<?=$list[$i][wr_id]?>" id="list_id">
<input type=hidden name=page       value="<?=$page?>">
<input type=hidden name=wr_subject value="<?=$subject?>">
<? if ($is_password) { ?><input type="hidden" name="wr_password"  value="<?=$norobot_str?>"><? } ?>
<? if ($is_norobot) { ?><input type="hidden" name="wr_key"  value="<?=$norobot_key?>"><? } ?>


<table width="<?=$width?>" align="center" cellpadding="5" cellspacing="0"><tr><td align="center">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<? if ($is_name) { ?>
<tr>
    <td colspan="2">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                	<td>
                		<input class="bbs_ft" maxLength="20" size="13" name="wr_name" itemname="이름" required style="height:23; padding:4;" value="손님" onFocus="clearText(this)"> ※ 손님은 등록만 가능하며 <span style="color:red">수정</span> 및 <span style="color:red">삭제</span>를 할 수 없습니다.
                	</td>
        
                	<td align="right" class=write_head><img id='kcaptcha_image_copy' border='0' width=120 height=20 onclick="imageClick();" style="cursor:pointer;" title="글자가 잘안보이는 경우 클릭하시면 새로운 글자가 나옵니다."></td>
                    <td align="right"><input class='ed' type=input size=10 name=wr_key itemname="자동등록방지" required></td>
        
                </table>
            </td>
        </tr>  
    </table>
	       
    </td>
</tr>
<? } ?> 

<tr> 

	<td style="width:500px;padding-right:3px;padding-left:70px;display:block; ">
		<div style="float:left;"><input class="bbs_ft" id="list_content" name="wr_content" itemname="내용" required style="width:400px;height:33; padding:4;display:block;" onFocus="clearText(this)" maxlength="200" value="<?= $noContentValue ?>"k></div>
		<!--<div style="float:left;"><input type="image" id="btn_submit" src="<?//=$board_skin_url?>/img/btn_write_comment.gif" border="0" align="absmiddle"></div>-->

		 &nbsp;&nbsp;<img src="<?=G5_URL?>/img/new_fa_ico.png" border="0" align="absmiddle">
		<input type="submit" id="btn_submit" value="확인" border="0" align="absmiddle" style="background:#56ccc8;border:0px;color:#fff;">
	</td>

	
</tr>
</table>
</td></tr></table>
</form>
</span>

<table width="<?=$width?>" cellspacing="0" cellpadding="0">
<tr>
    <form name=fsearch method=get>
    <input type=hidden name=bo_table value="<?=$bo_table?>">
    <input type=hidden name=sca      value="<?=$sca?>">
    <td width="100%" align="center">
        <select name=sfl class=select style="height:34px;">
            <option value='wr_content' <?if($sfl == "wr_content" || $sfl == ""){echo "selected";}?>>글내용</option>
            <option value='wr_name' <?if($sfl == "wr_name"){echo "selected";}?>>글작성자</option>
            <!--<option value='mb_id'>회원아이디</option>-->
        </select>
        <INPUT maxLength=15 style="width:200px;height:30px;" name=stx itemname="검색어" value="<?=$stx?>" class=input>
        <!--<SELECT name=sop class=select>
            <OPTION value=and>And</OPTION>
            <OPTION value=or>Or</OPTION>
        </SELECT>-->
		&nbsp;<INPUT type=image  src="<?=G5_URL?>/img/new_fa_submit.gif" align=absmiddle border=0>
    </td>
    </form>
</tr>
</table><br>

		</td>
	</tr>
</table>


</div>

<script type="text/javascript"> var md5_norobot_key = ''; </script>
<script type="text/javascript" src="<?="$g5[path]/js/prototype.js"?>"></script>
<script type="text/javascript">

$(document).ready(function(){
	var all_idx = $(".family_tb").find("tr").eq(2).attr("idx");
	$(".dis_" + all_idx).css("display", "block");

	$(".dis").css("display", "none");
	$(".dis_<?=abs($com_bef_view_row[wr_num])?>").css("display", "block");

	/*
	$(".bn").click(function(){
		var idx = $(this).attr("idx");

		$(".dis").each(function(i){
			if(idx == $(".dis").eq(i).attr("idx")){
				$(".dis").eq(i).css("display", "block");
			}else{
				$(".dis").eq(i).css("display", "none");
			}
		});
		
	});
	*/
});

function imageClick() {
    var url = "<?=$g5[bbs_path]?>/kcaptcha_session.php";
    var para = "";
    var myAjax = new Ajax.Request(
        url, 
        {
            method: 'post', 
            asynchronous: true,
            parameters: para, 
            onComplete: imageClickResult
        });
}

function imageClickResult(req) { 
    var result = req.responseText;
    var img = document.createElement("IMG");
    img.setAttribute("src", "<?=$g5[bbs_path]?>/kcaptcha_image.php?t=" + (new Date).getTime());
    document.getElementById('kcaptcha_image').src = img.getAttribute('src');
    document.getElementById('kcaptcha_image_copy').src = img.getAttribute('src');

    md5_norobot_key = result;
}

<? if (!$is_member) { ?>Event.observe(window, "load", imageClick);<? } ?>

function clearText(thefield){
	if (thefield.defaultValue==thefield.value) thefield.value = "";
}

function fwrite_check(f)
{
    var s = "";
<?if($is_guest){ ?>    
	if (f.wr_name.value == "손님" || false){
	        
	    f.wr_name.focus();
	    
	        
		alert("닉네임을 적어주세요.")
		return;
	}
<? } ?>    
	if (f.wr_content.value == "200자 이내로 입력이 가능합니다." || f.wr_content.value == "<?= $noLoginValue ?>" || f.wr_content.value == "<?= $noContentValue ?>" || false){
	    f.wr_content.focus();
		alert("내용을 입력해 주세요...")
		return;
	}

	//if (s = word_filter_check(f.wr_content.value)) {
    //    alert("내용에 금지단어('"+s+"')가 포함되어있습니다");
    //    return;
	//}


<?//if($is_guest){ ?>
    //if (typeof(f.wr_key) != "undefined") {
    //    if (hex_md5(f.wr_key.value) != md5_norobot_key) {
    //        alert("자동등록방지용 빨간글자가 순서대로 입력되지 않았습니다.");
    //        f.wr_key.focus();
    //        return;
    //    }
    //}
<? //} ?>
    f.action = "./write_update.php";
    f.submit();
}

var old_id, new_id;
var save_html = document.getElementById('list_write').innerHTML;

function commentTotalClose(){
    
    var tempArr = Array(<?= $tempArr ?>);



    for($i = 0 ; $i < tempArr.length ; $i++){
        var commentObj = eval("document.getElementById('comment_" + tempArr[$i] + "')");
        //if(commentObj != null)  commentObj.style.display = 'block';
    }

}

function list_box(list_id, work, idx)
{
			
    commentTotalClose();
            
    //imageClick();
	// 답변, 수정
    if (list_id)
    {
        if (work == 'r')
            new_id = 'reply_' + list_id;
        else
            new_id = 'edit_' + list_id;
    }
    else
        new_id = 'list_write';

	if (old_id == new_id) {

		var mode = document.getElementById(new_id).style.display; 
		if (mode == 'block') {
			$(".dis_" + idx).css("display", "none");
			document.getElementById(new_id).style.display = 'none';
		}

		else {
			$(".dis_" + idx).css("display", "block");
			document.getElementById(new_id).style.display = 'block'; 
		}

	}
	else 
    {
		if (old_id)
        {
            document.getElementById(old_id).style.display = 'none';
            document.getElementById(old_id).innerHTML = '';
            
        }

		$(".save_content_"+list_id).css("display", "block");
		$(".dis_" + idx).css("display", "block");
        document.getElementById(new_id).style.display = 'block';
		document.getElementById(new_id).innerHTML = save_html;

		// 수정
        if (work == 'u')
        {
			document.getElementById('list_content').value = document.getElementById('save_content_' + list_id).value;

            var commentObj = eval("document.getElementById('comment_" + list_id + "')");
        
            if(commentObj != null)
            if(commentObj.style.display == 'none')  commentObj.style.display = 'block';
            else                                    commentObj.style.display = 'none';
    
        }

        document.getElementById('list_id').value = list_id;
        document.getElementById('list_w').value = work;
		old_id = new_id;
	}
}

function com_view(idx){
	if($(".dis_" + idx).css("display") == "block" || $(".dis_" + idx).css("display") == "inline"){
		$(".dis_" + idx).css("display", "none");
	}else{
		$(".dis_" + idx).css("display", "block");
	}
}

list_box('', '', '');
new_id = 'list_write';
document.getElementById(new_id).style.display = 'none';
</script>

<?
if ($w == "") {
if (!$member[mb_id]) 
    echo "<script language='javascript' src='$g5[path]/js/md5.js'></script>\n";

// 필터
echo "<script language='javascript'> var g5_cf_filter = '$config[cf_filter],&nbsp'; </script>\n";
echo "<script language='javascript' src='$g5[path]/js/filter.js'></script>\n";
}
?>