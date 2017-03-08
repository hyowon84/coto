<?php
include_once('./_common.php');

if (G5_IS_MOBILE) {
    include_once(G5_MSHOP_PATH.'/itemuseform.php');
    return;
}

include_once(G5_EDITOR_LIB);

if (!$is_member) {
    alert_close("사용후기는 회원만 작성 가능합니다.");
}

$w     = escape_trim($_REQUEST['w']);
$it_id = escape_trim($_REQUEST['it_id']);
$is_id = escape_trim($_REQUEST['is_id']);

// 사용후기 작성 설정에 따른 체크
check_itemuse_write();

if ($w == "") {
    $is_score = 5;
} else if ($w == "u") {
    $use = sql_fetch(" select * from g5_shop_gpitem_use where is_id = '$is_id' ");
    if (!$use) {
        alert_close("사용후기 정보가 없습니다.");
    }

    $gp_id    = $use['gp_id'];
    $is_score = $use['is_score'];

    if (!$is_admin && $use['mb_id'] != $member['mb_id']) {
        alert_close("자신의 사용후기만 수정이 가능합니다.");
    }
}

include_once(G5_PATH.'/head.sub.php');

$is_dhtml_editor = false;
// 모바일에서는 DHTML 에디터 사용불가
if ($config['cf_editor'] && !G5_IS_MOBILE) {
    $is_dhtml_editor = true;
}
$editor_html = editor_html('is_content', $use['is_content'], $is_dhtml_editor);
$editor_js = '';
$editor_js .= get_editor_js('is_content', $is_dhtml_editor);
$editor_js .= chk_editor_js('is_content', $is_dhtml_editor);

$itemuseform_skin = G5_SHOP_SKIN_PATH.'/gpitemuseform.skin.php';

if(!file_exists($itemuseform_skin)) {
    echo str_replace(G5_PATH.'/', '', $itemuseform_skin).' 스킨 파일이 존재하지 않습니다.';
} else {
    include_once($itemuseform_skin);
}

include_once(G5_PATH.'/tail.sub.php');
?>