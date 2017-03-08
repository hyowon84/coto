<?php
$menu['menu300'] = array (
	array('300000', '게시판관리', '#', ''),
	array('300100', '게시판관리', ''.G5_ADMIN_URL.'/board_list.php', 'bbs_board'),
	array('300200', '게시판그룹관리', ''.G5_ADMIN_URL.'/boardgroup_list.php', 'bbs_group'),
	array('300500', '1:1문의설정', ''.G5_ADMIN_URL.'/qa_config.php', 'qa'),
	array('300600', '제휴문의', ''.G5_ADMIN_URL.'/alliance.php', 'alliance'),
	array('300800', '이벤트관리', G5_ADMIN_URL.'/shop_admin/itemevent.php', 'scf_event'),
	array('300810', '이벤트일괄처리', G5_ADMIN_URL.'/shop_admin/itemeventlist.php', 'scf_event_mng'),
	array('300900', '내용관리', G5_ADMIN_URL.'/shop_admin/contentlist.php', 'scf_contents'),
	array('300910', 'FAQ관리', G5_ADMIN_URL.'/shop_admin/faqmasterlist.php', 'scf_faq'),
	array('300700', '국내세시관리', ''.G5_ADMIN_URL.'/etc/domesticprice_form.php', 'domesticprice_form'),
);
?>