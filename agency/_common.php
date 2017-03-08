<?php
include_once('../common.php');

define('_SHOP_', true);

if(!$member[mb_id])alert('로그인 후 이용 가능합니다.', G5_BBS_URL.'/login.php');
?>