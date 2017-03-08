<?php
include_once('./_common.php');

if ($is_guest) alert('로그인 한 회원만 접근하실 수 있습니다.', G5_BBS_URL.'/login.php');

$g5['title'] = '회원 비밀번호 확인';
if (G5_IS_MOBILE) {
    include_once(G5_MSHOP_PATH.'/head.php');
}

include_once($member_skin_path.'/member_confirm.skin.php');

if (G5_IS_MOBILE) {
    include_once(G5_MSHOP_PATH.'/tail.php');
}
?>