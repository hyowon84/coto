<?php
include_once('./_common.php');
include_once(G5_CAPTCHA_PATH.'/captcha.lib.php');

$g5['title'] = '게시글 저장';

$wr_link1 = '';
if (isset($_POST['wr_link1'])) {
    $wr_link1 = substr($_POST['wr_link1'],0,1000);
}

$wr_link2 = '';
if (isset($_POST['wr_link2'])) {
    $wr_link2 = substr($_POST['wr_link2'],0,1000);
}

// 090710
if (substr_count($wr_content, '&#') > 50) {
    alert('내용에 올바르지 않은 코드가 다수 포함되어 있습니다.');
    exit;
}

$w = $_POST['w'];
$wr_link1 = escape_trim(strip_tags($_POST['wr_link1']));
$wr_link2 = escape_trim(strip_tags($_POST['wr_link2']));

$notice_array = explode(",", $board['bo_notice']);

$html = '';
if (isset($_POST['html']) && $_POST['html']) {
    $html = $_POST['html'];
}

$mail = '';
if (isset($_POST['mail']) && $_POST['mail']) {
    $mail = $_POST['mail'];
}

$notice = '';
if (isset($_POST['notice']) && $_POST['notice']) {
    $notice = $_POST['notice'];
}

for ($i=1; $i<=10; $i++) {
    $var = "wr_$i";
    $$var = "";
    if (isset($_POST['wr_'.$i]) && $_POST['wr_'.$i]) {
        $$var = escape_trim($_POST['wr_'.$i]);
    }
}

@include_once($board_skin_path.'/write_update.head.skin.php');

if ($w == '' || $w == 'u') {

    // 김선용 1.00 : 글쓰기 권한과 수정은 별도로 처리되어야 함
    if($w =='u' && $member['mb_id'] && $wr['mb_id'] == $member['mb_id']) {
        ;
    } else if ($member['mb_level'] < $board['bo_write_level']) {
        alert('글을 쓸 권한이 없습니다.');
    }

	// 외부에서 글을 등록할 수 있는 버그가 존재하므로 공지는 관리자만 등록이 가능해야 함
	if (!$is_admin && $notice) {
		alert('관리자만 공지할 수 있습니다.');
    }

} else if ($w == 'r') {

	$wr = get_write("g5_write_".$ca_id, $wr_id);

    if (in_array((int)$wr_id, $notice_array)) {
        alert('공지에는 답변 할 수 없습니다.');
    }

    if ($member['mb_level'] < $board['bo_reply_level']) {
        alert('글을 답변할 권한이 없습니다.');
    }

    // 게시글 배열 참조
    $reply_array = &$wr;

    // 최대 답변은 테이블에 잡아놓은 wr_reply 사이즈만큼만 가능합니다.
    if (strlen($reply_array['wr_reply']) == 10) {
        alert("더 이상 답변하실 수 없습니다.\\n답변은 10단계 까지만 가능합니다.");
    }

    $reply_len = strlen($reply_array['wr_reply']) + 1;
    if ($board['bo_reply_order']) {
        $begin_reply_char = 'A';
        $end_reply_char = 'Z';
        $reply_number = +1;
        $sql = " select MAX(SUBSTRING(wr_reply, $reply_len, 1)) as reply from g5_write_".$ca_id." where wr_num = '{$reply_array['wr_num']}' and SUBSTRING(wr_reply, {$reply_len}, 1) <> '' ";
    } else {
        $begin_reply_char = 'Z';
        $end_reply_char = 'A';
        $reply_number = -1;
        $sql = " select MIN(SUBSTRING(wr_reply, {$reply_len}, 1)) as reply from g5_write_".$ca_id." where wr_num = '{$reply_array['wr_num']}' and SUBSTRING(wr_reply, {$reply_len}, 1) <> '' ";
    }
    if ($reply_array['wr_reply']) $sql .= " and wr_reply like '{$reply_array['wr_reply']}%' ";
    $row = sql_fetch($sql);

    if (!$row['reply']) {
        $reply_char = $begin_reply_char;
    } else if ($row['reply'] == $end_reply_char) { // A~Z은 26 입니다.
        alert("더 이상 답변하실 수 없습니다.\\n답변은 26개 까지만 가능합니다.");
    } else {
        $reply_char = chr(ord($row['reply']) + $reply_number);
    }

    $reply = $reply_array['wr_reply'] . $reply_char;

} else {
    alert('w 값이 제대로 넘어오지 않았습니다.');
}

$chars_array = array_merge(range(0,9), range('a','z'), range('A','Z'));

if ($w == '' || $w == 'r') {

    if ($member['mb_id']) {
        $mb_id = $member['mb_id'];
        $wr_name = $board['bo_use_name'] ? $member['mb_name'] : $member['mb_nick'];
        $wr_password = $member['mb_password'];
        $wr_email = $member['mb_email'];
        $wr_homepage = $member['mb_homepage'];
    } else {
        $mb_id = '';
        // 비회원의 경우 이름이 누락되는 경우가 있음
        $wr_name = escape_trim($_POST['wr_name']);
        if (!$wr_name)
            alert('이름은 필히 입력하셔야 합니다.');
        $wr_password = sql_password($wr_password);
    }

    if ($w == 'r') {
        // 답변의 원글이 비밀글이라면 비밀번호는 원글과 동일하게 넣는다.
        if ($secret)
            $wr_password = $wr['wr_password'];

        $wr_id = $wr_id . $reply;
        $wr_num = $write['wr_num'];
        $wr_reply = $reply;
    } else {
        $wr_num = get_next_num("g5_write_".$ca_id);
        $wr_reply = '';
    }

    $sql = " insert into g5_write_".$ca_id."
                set wr_num = '$wr_num',
                     wr_reply = '$wr_reply',
                     wr_comment = 0,
                     ca_name = '$ca_name',
                     wr_option = '$html,$secret,$mail',
                     wr_subject = '$wr_subject',
                     wr_content = '$wr_content',
                     wr_link1 = '$wr_link1',
                     wr_link2 = '$wr_link2',
                     wr_link1_hit = 0,
                     wr_link2_hit = 0,
                     wr_hit = 0,
                     wr_good = 0,
                     wr_nogood = 0,
                     mb_id = '{$member['mb_id']}',
                     wr_password = '$wr_password',
                     wr_name = '{$member['mb_nick']}',
                     wr_email = '$wr_email',
                     wr_homepage = '$wr_homepage',
                     wr_datetime = '".G5_TIME_YMDHIS."',
                     wr_last = '".G5_TIME_YMDHIS."',
                     wr_ip = '{$_SERVER['REMOTE_ADDR']}',
                     wr_1 = '$wr_1',
                     wr_2 = '$wr_2',
                     wr_3 = '$wr_3',
                     wr_4 = '".strtotime($wr_4)."',
                     wr_5 = '".strtotime($wr_5)."',
                     wr_6 = '$wr_6',
                     wr_7 = '$wr_7',
                     wr_8 = '$wr_8',
                     wr_9 = '$wr_9',
                     wr_10 = '$wr_10' ";


    sql_query($sql);

    $wr_id = mysql_insert_id();

    // 부모 아이디에 UPDATE
    sql_query(" update g5_write_".$ca_id." set wr_parent = '$wr_id' where wr_id = '$wr_id' ");

    // 게시글 1 증가
    sql_query("update {$g5['board_table']} set bo_count_write = bo_count_write + 1 where bo_table = '{$bo_table}'");

}
// syndication ping
//include G5_SYNDI_PATH.'/include/include.bbs.write_update.php';

$com_row = sql_fetch("
	select * from g5_write_".$ca_id."
	where wr_id='".$wr_id."'
");

echo "<tr class='free_talk_tr_view free_talk_tr_view".$wr_id."' style='display:table-row;'>";
echo "<td></td>";
echo "<td>";
echo "<table border='0' cellspacing='0' cellpadding='0' width='100%'>";
echo "<tr height='20px'>";
echo "<td>";
if(strlen($com_row[wr_reply]) > 1){
	for($k = 1; $k <= strlen($com_row[wr_reply]); $k++){echo "&nbsp;";}
}else{
	$mode = "main";
}
echo "</td>";
echo "<td style='font-size:11px;white-space:nowrap;width:7%;font-weight:bold;'>";
echo "<img src='".G5_URL."/img/new_fa_ico2.png'>";
echo $member[mb_nick];
echo "</td>";
echo "<td style='float:left;padding:0 0 0 20px;color:#0549ab;font-size:11px;'>";
echo $com_row[wr_content];
echo "</td>";
echo "<td style='float:left;padding:3px 0 0 7px;font-size:9px;color:#545e63;font-style:italic;font-weight:bold;'>";
echo date("Y.m.d H:i", strtotime($com_row[wr_datetime]));
echo "</td>";
echo "<td style='color:#fff;cursor:pointer;font-size:11px;' idx='".$com_row[wr_id]."' wr_num='".$com_row[wr_num]."' wr_replay='".$com_row[wr_reply]."' onclick='return ft_bn1(\"".$com_row[wr_id]."\", \"".$mode."\");'>";
echo "<img src='http://coinstoday.co.kr/img/new_fa_ico1.png'>";
echo "댓글";
echo "</td>";
echo "</tr>";
echo "</table>";
echo "</td>";
echo "</tr>";
echo "<tr class='free_talk_tr_view free_talk_tr_view".$wr_id." sub_free_talk_tr_view".$com_row[wr_id]."' style='display:table-row;'>";
echo "<td></td>";
echo "<td class='ft_input ft_input".$com_row[wr_id]."'>";
echo "</td>";
echo "</tr>";


delete_cache_latest($bo_table);

?>