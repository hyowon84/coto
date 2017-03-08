<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

$path = "../data/member";
chmod($path, 0707);
?>

<script>
// 글자수 제한
var char_min = parseInt(<?php echo $comment_min ?>); // 최소
var char_max = parseInt(<?php echo $comment_max ?>); // 최대
</script>

<!-- 댓글 시작 { -->
<section id="bo_vc">
    <h2>덧글 <?php echo number_format($view['wr_comment']) ?>개</h2>
    <?php
    $cmt_amt = count($list);
    for ($i=0; $i<$cmt_amt; $i++) {
        $comment_id = $list[$i]['wr_id'];
        $cmt_depth = ""; // 댓글단계
        $cmt_depth = strlen($list[$i]['wr_comment_reply']) * 20;
        $comment = $list[$i]['content'];
        /*
        if (strstr($list[$i]['wr_option'], "secret")) {
            $str = $str;
        }
        */
        $comment = preg_replace("/\[\<a\s.*href\=\"(http|https|ftp|mms)\:\/\/([^[:space:]]+)\.(mp3|wma|wmv|asf|asx|mpg|mpeg)\".*\<\/a\>\]/i", "<script>doc_write(obj_movie('$1://$2.$3'));</script>", $comment);
        $cmt_sv = $cmt_amt - $i + 1; // 댓글 헤더 z-index 재설정 ie8 이하 사이드뷰 겹침 문제 해결

		$mem_row = sql_fetch("select * from {$g5['member_table']} where mb_id='".$list[$i][mb_id]."'");

		//각 댓글 갯수
		$com_row = sql_fetch("select count(*) as cnt from {$g5['g5_write_free_table']} where wr_parent='$wr_id' and wr_comment='".$list[$i]['wr_comment']."' ");
		$cnt = $com_row[cnt]-1;
     ?>

	<div style="border-top:1px dotted #ccc"></div>
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
		<tr>
			<td width="50px" valign="top" style="padding:20px 5px 20px 5px;">
			<?if($cmt_depth == ""){?>
				<?if($mem_row[mb_img]){?>
					<img src="<?=$path?>/<?=$mem_row[mb_img]?>">
				<?}else{?>
					<img src="<?=G5_URL?>/img/pofile_no.png" border="0" align="absmiddle" style="width:45px;height:45px;">
				<?}?>
			<?}?>
			</td>
			<td>
				<article id="c_<?php echo $comment_id ?>" <?php if ($cmt_depth) { ?>style="margin-left:<?php echo $cmt_depth ?>px;border:0px;"<?php } ?>>
					<header style="z-index:<?php echo $cmt_sv; ?>">

						<table border="0" cellspacing="0" cellpadding="0" width="100%">
						<?if($cmt_depth == ""){?>
							<tr height="20px">
								<td colspan="3" style="text-align:left;"><?php echo $comment ?></td>
							</tr>
						<?}?>
							<tr height="20px">
								<td style="width:100px;"><?php if ($cmt_depth) { ?><img src="<?php echo G5_URL ?>/img/b_free_ico.gif" class="icon_reply" alt="댓글의 댓글" style="margin:5px 0 0 0;"><?php } ?><?php echo $list[$i]['name'] ?></td>
								<td>
								<?if($cmt_depth != ""){?>
									<?php echo $comment ?>
								<?}?>

									<?php if($list[$i]['is_reply'] || $list[$i]['is_edit'] || $list[$i]['is_del']) {
										$query_string = str_replace("&", "&amp;", $_SERVER['QUERY_STRING']);

										if($w == 'cu') {
											$sql = " select wr_id, wr_content from $write_table where wr_id = '$c_id' and wr_is_comment = '1' ";
											$cmt = sql_fetch($sql);
											$c_wr_content = $cmt['wr_content'];
										}

										$c_reply_href = './board.php?'.$query_string.'&amp;c_id='.$comment_id.'&amp;w=c#bo_vc_w';
										$c_edit_href = './board.php?'.$query_string.'&amp;c_id='.$comment_id.'&amp;w=cu#bo_vc_w';
									 ?>
										<?php if ($list[$i]['is_reply']) { ?><a onclick="comment_box('<?php echo $comment_id ?>', 'c'); return false;" style="cursor:pointer;"><img src="<?=G5_URL?>/img/free_view_ico.png"> 답변쓰기<?if($cmt_depth == ""){echo "[".$cnt."]";}?></a><?php } ?>
										<?php if ($list[$i]['is_edit']) { ?><a onclick="comment_box('<?php echo $comment_id ?>', 'cu'); return false;" style="cursor:pointer;">수정</a><?php } ?>
										<?php if ($list[$i]['is_del'])  { ?><a href="<?php echo $list[$i]['del_link'];  ?>" onclick="return comment_delete();" style="cursor:pointer;">삭제</a><?php } ?>
									<?php } ?>
								</td>
								<td style="width:100px;text-align:right;"><span class="bo_vc_hdinfo" style="font-style:italic;"><time datetime="<?php echo date('Y-m-d\TH:i:s+09:00', strtotime($list[$i]['wr_datetime'])) ?>"><?php echo date("Y.m.d", strtotime($list[$i]['wr_datetime'])) ?></time></span></td>
							</tr>
						</table>
					   
					</header>

					<!-- 댓글 출력 -->
					<!--
					<p>
						<?php// if (strstr($list[$i]['wr_option'], "secret")) { ?><img src="<?php// echo $board_skin_url; ?>/img/icon_secret.gif" alt="비밀글"><?php// } ?>
						<?php// echo $comment ?>
					</p>-->

					<span id="edit_<?php echo $comment_id ?>"></span><!-- 수정 -->
					<span id="reply_<?php echo $comment_id ?>"></span><!-- 답변 -->

					<input type="hidden" value="<?php echo strstr($list[$i]['wr_option'],"secret") ?>" id="secret_comment_<?php echo $comment_id ?>">
					<input type="text" id="save_comment_<?php echo $comment_id ?>" value="<?php echo get_text($list[$i]['content1'], 0) ?>" style="display:none">

				</article>
			</td>
		</tr>
	</table>
    <?php } ?>
    <?php if ($i == 0) { //댓글이 없다면 ?><p id="bo_vc_empty">등록된 댓글이 없습니다.</p><?php } ?>

</section>
<!-- } 댓글 끝 -->

<?php if ($is_comment_write) {
    if($w == '')
        $w = 'c';
?>
<!-- 댓글 쓰기 시작 { -->
<aside id="bo_vc_w">
    <form name="fviewcomment" action="./write_comment_update.php" onsubmit="return fviewcomment_submit(this);" method="post" autocomplete="off">
    <input type="hidden" name="w" value="<?php echo $w ?>" id="w">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="wr_id" value="<?php echo $wr_id ?>">
    <input type="hidden" name="comment_id" value="<?php echo $c_id ?>" id="comment_id">
    <input type="hidden" name="sca" value="<?php echo $sca ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="spt" value="<?php echo $spt ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <input type="hidden" name="is_good" value="">

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <tbody>
        <?php if ($is_guest) { ?>
        <tr>
            <th scope="row"><label for="wr_name">이름<strong class="sound_only">필수</strong></label></th>
            <td><input type="text" name="wr_name" id="wr_name"  required class="frm_input required" size="5" maxLength="20" value="<?php echo get_cookie("ck_sns_name"); ?>"></td>
        </tr>
        <tr>
            <th scope="row"><label for="wr_password">비밀번호<strong class="sound_only">필수</strong></label></th>
            <td><input type="password" name="wr_password" id="wr_password"  required class="frm_input required" size="10" maxLength="20"></td>
        </tr>
        <?php } ?>
        <!--<tr>
            <th scope="row"><label for="wr_secret">비밀글사용</label></th>
            <td><input type="checkbox" name="wr_secret" value="secret" id="wr_secret"></td>
        </tr>-->
        <?php if ($is_guest) { ?>
        <tr>
            <th scope="row">자동등록방지</th>
            <td><?php echo $captcha_html; ?></td>
        </tr>
        <?php } ?>
        <?php
        include(G5_SNS_PATH."/view_comment_write.sns.skin.php");
        ?>
        <tr height="35px">
            <td scope="row" style="width:50px;padding:20px 5px 20px 5px;" valign="top">
			<?if($member[mb_img]){?>
				<img src="<?=$path?>/<?=$member[mb_img]?>" border="0" align="absmiddle" style="width:45px;height:45px;">
			<?}else{?>
				<img src="<?=G5_URL?>/img/pofile_no.png" border="0" align="absmiddle" style="width:45px;height:45px;">
			<?}?>
			</td>
            <td>
                <?php if ($comment_min || $comment_max) { ?><strong id="char_cnt"><span id="char_count"></span>글자</strong><?php } ?>
                <input id="wr_content" name="wr_content" maxlength="100" required class="required" style="width:100%;height:20px;" title="내용"
                <?php if ($comment_min || $comment_max) { ?>onkeyup="check_byte('wr_content', 'char_count');"<?php } ?>><?php echo $c_wr_content;  ?>
                <?php if ($comment_min || $comment_max) { ?><script> check_byte('wr_content', 'char_count'); </script><?php } ?>
                <script>
                $("input#wr_content[maxlength]").live("keyup change", function() {
                    var str = $(this).val()
                    var mx = parseInt($(this).attr("maxlength"))
                    if (str.length > mx) {
                        $(this).val(str.substr(0, mx));
                        return false;
                    }
                });
                </script>
				<div style="margin:5px 0 0 0;height:20px;font-weight:bold;"><?=$member[mb_nick]?></div>
            </td>
			<td valign="top"><img src="<?=G5_URL?>/img/new_fa_ico.png" border="0" align="absmiddle"> <input type="submit" value="확인" id="btn_submit" style="border:0px;background:#fff;"></td>
        </tr>
        </tbody>
        </table>
    </div>

    </form>
</aside>


<script>
var save_before = '';
var save_html = document.getElementById('bo_vc_w').innerHTML;

function good_and_write()
{
    var f = document.fviewcomment;
    if (fviewcomment_submit(f)) {
        f.is_good.value = 1;
        f.submit();
    } else {
        f.is_good.value = 0;
    }
}

function fviewcomment_submit(f)
{
    var pattern = /(^\s*)|(\s*$)/g; // \s 공백 문자

    f.is_good.value = 0;

    var subject = "";
    var content = "";
    $.ajax({
        url: g5_bbs_url+"/ajax.filter.php",
        type: "POST",
        data: {
            "subject": "",
            "content": f.wr_content.value
        },
        dataType: "json",
        async: false,
        cache: false,
        success: function(data, textStatus) {
            subject = data.subject;
            content = data.content;
        }
    });

    if (content) {
        alert("내용에 금지단어('"+content+"')가 포함되어있습니다");
        f.wr_content.focus();
        return false;
    }

    // 양쪽 공백 없애기
    var pattern = /(^\s*)|(\s*$)/g; // \s 공백 문자
    document.getElementById('wr_content').value = document.getElementById('wr_content').value.replace(pattern, "");
    if (char_min > 0 || char_max > 0)
    {
        check_byte('wr_content', 'char_count');
        var cnt = parseInt(document.getElementById('char_count').innerHTML);
        if (char_min > 0 && char_min > cnt)
        {
            alert("댓글은 "+char_min+"글자 이상 쓰셔야 합니다.");
            return false;
        } else if (char_max > 0 && char_max < cnt)
        {
            alert("댓글은 "+char_max+"글자 이하로 쓰셔야 합니다.");
            return false;
        }
    }
    else if (!document.getElementById('wr_content').value)
    {
        alert("댓글을 입력하여 주십시오.");
        return false;
    }

    if (typeof(f.wr_name) != 'undefined')
    {
        f.wr_name.value = f.wr_name.value.replace(pattern, "");
        if (f.wr_name.value == '')
        {
            alert('이름이 입력되지 않았습니다.');
            f.wr_name.focus();
            return false;
        }
    }

    if (typeof(f.wr_password) != 'undefined')
    {
        f.wr_password.value = f.wr_password.value.replace(pattern, "");
        if (f.wr_password.value == '')
        {
            alert('비밀번호가 입력되지 않았습니다.');
            f.wr_password.focus();
            return false;
        }
    }

    <?php if($is_guest) echo chk_captcha_js();  ?>

    document.getElementById("btn_submit").disabled = "disabled";

    return true;
}


function fviewcomment1_submit(f)
{
    var pattern = /(^\s*)|(\s*$)/g; // \s 공백 문자

    f.is_good.value = 0;

    var subject = "";
    var content = "";
    $.ajax({
        url: g5_bbs_url+"/ajax.filter.php",
        type: "POST",
        data: {
            "subject": "",
            "content": f.wr_content.value
        },
        dataType: "json",
        async: false,
        cache: false,
        success: function(data, textStatus) {
            subject = data.subject;
            content = data.content;
        }
    });

    if (content) {
        alert("내용에 금지단어('"+content+"')가 포함되어있습니다");
        f.wr_content.focus();
        return false;
    }

    // 양쪽 공백 없애기
    var pattern = /(^\s*)|(\s*$)/g; // \s 공백 문자
    document.getElementById('wr_content').value = document.getElementById('wr_content').value.replace(pattern, "");
    if (char_min > 0 || char_max > 0)
    {
        check_byte('wr_content', 'char_count');
        var cnt = parseInt(document.getElementById('char_count').innerHTML);
        if (char_min > 0 && char_min > cnt)
        {
            alert("댓글은 "+char_min+"글자 이상 쓰셔야 합니다.");
            return false;
        } else if (char_max > 0 && char_max < cnt)
        {
            alert("댓글은 "+char_max+"글자 이하로 쓰셔야 합니다.");
            return false;
        }
    }
    else if (!document.getElementById('wr_content').value)
    {
        alert("댓글을 입력하여 주십시오.");
        return false;
    }

    if (typeof(f.wr_name) != 'undefined')
    {
        f.wr_name.value = f.wr_name.value.replace(pattern, "");
        if (f.wr_name.value == '')
        {
            alert('이름이 입력되지 않았습니다.');
            f.wr_name.focus();
            return false;
        }
    }

    if (typeof(f.wr_password) != 'undefined')
    {
        f.wr_password.value = f.wr_password.value.replace(pattern, "");
        if (f.wr_password.value == '')
        {
            alert('비밀번호가 입력되지 않았습니다.');
            f.wr_password.focus();
            return false;
        }
    }

    <?php if($is_guest) echo chk_captcha_js();  ?>

    document.getElementById("btn_submit1").disabled = "disabled";

    return true;
}

function comment_box(comment_id, work)
{
    var el_id;
    // 댓글 아이디가 넘어오면 답변, 수정
    if (comment_id)
    {
        if (work == 'c')
            el_id = 'reply_' + comment_id;
        else
            el_id = 'edit_' + comment_id;
    }
    else
        el_id = 'bo_vc_w';

    if (save_before != el_id)
    {
        if (save_before)
        {
            document.getElementById(save_before).style.display = 'none';
            document.getElementById(save_before).innerHTML = '';
        }

		document.getElementById('bo_vc_w').style.display = "block";
        document.getElementById(el_id).style.display = '';
        document.getElementById(el_id).innerHTML = save_html;
        // 댓글 수정
        if (work == 'cu')
        {
            document.getElementById('wr_content').value = document.getElementById('save_comment_' + comment_id).value;
            if (typeof char_count != 'undefined')
                check_byte('wr_content', 'char_count');
            //if (document.getElementById('secret_comment_'+comment_id).value)
                //document.getElementById('wr_secret').checked = true;
            //else
                //document.getElementById('wr_secret').checked = false;
        }

        document.getElementById('comment_id').value = comment_id;
        document.getElementById('w').value = work;

        if(save_before)
            $("#captcha_reload").trigger("click");

        save_before = el_id;
    }
}

function comment_delete()
{
    return confirm("이 댓글을 삭제하시겠습니까?");
}

//comment_box('', 'c'); // 댓글 입력폼이 보이도록 처리하기위해서 추가 (root님)
</script>
<?php } ?>
<!-- } 댓글 쓰기 끝 -->