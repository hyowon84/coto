<?php
include_once('./_common.php');

if (G5_IS_MOBILE) {
    include_once(G5_MSHOP_PATH.'/itemexform.php');
    return;
}

include_once(G5_EDITOR_LIB);

if (!$is_member) {
    alert_close("상품문의는 회원만 작성 가능합니다.");
}

$w     = escape_trim($_REQUEST['w']);
$it_id = escape_trim($_REQUEST['it_id']);
$iq_id = escape_trim($_REQUEST['iq_id']);

$chk_secret = '';

if($w == '') {
    $qa['iq_email'] = $member['mb_email'];
    $qa['iq_hp'] = $member['mb_hp'];
}

if ($w == "u")
{
    $ex = sql_fetch(" select * from g5_shop_item_ex where iq_id = '$iq_id' ");
    if (!$ex) {
        alert_close("상품문의 정보가 없습니다.");
    }

    $it_id    = $ex['it_id'];

    if (!$iq_admin && $ex['mb_id'] != $member['mb_id']) {
        alert_close("자신의 상품문의만 수정이 가능합니다.");
    }

    if($ex['iq_secret'])
        $chk_secret = 'checked="checked"';
}

include_once(G5_PATH.'/head.sub.php');

$is_dhtml_editor = false;
// 모바일에서는 DHTML 에디터 사용불가
if ($config['cf_editor'] && !G5_IS_MOBILE) {
    $is_dhtml_editor = true;
}
$editor_html = editor_html('iq_question', $ex['iq_question'], $is_dhtml_editor);
$editor_js = '';
$editor_js .= get_editor_js('iq_question', $is_dhtml_editor);
$editor_js .= chk_editor_js('iq_question', $is_dhtml_editor);

$itemexform_skin = G5_SHOP_SKIN_PATH.'/itemexform.skin.php';

if(!file_exists($itemexform_skin)) {
    echo str_replace(G5_PATH.'/', '', $itemexform_skin).' 스킨 파일이 존재하지 않습니다.';
} else {
    include_once($itemexform_skin);
}

include_once(G5_PATH.'/tail.sub.php');
?>