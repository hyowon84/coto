<?php
$menu['menu200'] = array (
    array('200000', '회원관리', '#', ''),
    array('200100', '기본회원관리', G5_ADMIN_URL.'/member_list.php', 'mb_list'),
    array('200300', '회원메일발송', G5_ADMIN_URL.'/mail_list.php', 'mb_mail'),
    array('200810', '접속회원', G5_ADMIN_URL.'/visit_search.php', 'mb_search'),
    array('200200', '포인트관리', G5_ADMIN_URL.'/point_list.php', 'mb_point'),
    array('200900', '투표관리', G5_ADMIN_URL.'/poll_list.php', 'mb_poll'),
		array('200910', 'SMS관리', G5_ADMIN_URL.'/sms_admin/config.php', 'mb_sms'),
);
?>