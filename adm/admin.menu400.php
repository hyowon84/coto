<?php
$menu['menu400'] = array (
	array('400000', '공구/구매대행', '#', 'orderlist'),
	array('400100', '공동구매설정', G5_ADMIN_URL.'/clayshop/gpinfo_list.php', 'gpinfo_list'),
	array('400190', '카테고리관리', G5_ADMIN_URL.'/shop_admin/categorylist.php', 'grouppurchaselist'),
	array('400300', '상품가격&경매관리', G5_ADMIN_URL.'/extjs/product/product_main.php', 'product'),
	array('400200', '상품관리', G5_ADMIN_URL.'/shop_admin/grouppurchaselist.php', 'grouppurchaselist'),
	array('900530', '상품유형관리', G5_ADMIN_URL.'/shop_admin/gpitemtypelist.php', 'scf_gpitemtype'),
	array('900540', '상품유형아이콘관리', G5_ADMIN_URL.'/shop_admin/gpitemtypeicon.php', 'scf_gpitemtypeicon'),
	array('400350', '미입금자 목록', ''.G5_ADMIN_URL.'/util/mustpay.php', 'mustpay'),
	array('400410', '입출금관리', G5_ADMIN_URL.'/extjs/bank/bank_main.php', 'bank_list'),
	array('400500', '발주/송금/통관/입고', G5_ADMIN_URL.'/extjs/stock/stock_main.php', 'stock'),
	array('400310', '주문신청관리', G5_ADMIN_URL.'/extjs/order/order_main.php', 'orderlist'),
	array('400550', '통합배송관리', ''.G5_ADMIN_URL.'/extjs/baesong/baesong_main.php', 'baesong'),
//	array('400300', '(구)주문신청관리', G5_ADMIN_URL.'/clayshop/orderlist.php', 'orderlist'),
//	array('400400', '(구)입출금관리', G5_ADMIN_URL.'/clayshop/bank_list.php', 'bank_list')
);
?>