<?php
include_once('./_common.php');

if ($is_guest)
    alert('로그인 한 회원만 접근하실 수 있습니다.', G5_BBS_URL.'/login.php');

/*
if ($url)
    $urlencode = urlencode($url);
else
    $urlencode = urlencode($_SERVER[REQUEST_URI]);
*/

$g5['title'] = '회원 비밀번호 확인';
if (G5_IS_MOBILE) {
    include_once(G5_MSHOP_PATH.'/head.php');
} else {
    include_once(G5_PATH.'/_head.php');
}
?>

<!-- 주문 내역 시작 { -->
<div id="sod_v">

    <?php
    $limit = " limit $from_record, $rows ";
    include_once($member_skin_path.'/member_confirm.skin1.php');
    ?>

</div>
<!-- } 주문 내역 끝 -->

<?php
if (G5_IS_MOBILE) {
    include_once(G5_MSHOP_PATH.'/tail.php');
} else {
    include_once(G5_PATH.'/tail.php');
}
?>
