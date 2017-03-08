<?php
$sub_menu = "500600";
include_once('./_common.php');

set_session("mem_order_se", $_POST[mb_id]);

goto_url(G5_URL."/shop/gplist.php?ca_id=2010");
?>