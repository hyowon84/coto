<?php
$goodsMetalListArray = array("GL"=>"금","SL"=>"은","PT"=>"백금","PD"=>"팔라듐","EC"=>"기타");

//구매대행 설정
$purchaseShoppingSiteList = array("amazon.com","ebay.com");
$purchaseShoppingType = array("N"=>"일반","A"=>"입찰");
$purchaseExceptionList = array("1"=>"구매보류","구매후 추가정산");

// 구매대행 기본 배송비
$purchaseSendCost = 3000;

$purchaseOrderStatus = array("주문","입금요청","입금확인","구매진행","배송","완료","취소");

$purchaseCartStatus1 = array("대기","구매완료","구매보류","취소");
$purchaseCartStatus2 = array("대기","입찰중","낙찰","입찰보류","입찰실패","취소");

//배송대행 설정
$shippingDistributionList = array("OR"=>"오레곤(OR)","NJ"=>"뉴저지(NJ)","LA"=>"엘에이(LA)");
$shippingCheckingOptionList = array("1"=>"기본","검품","재포장","검품+재포장");
$shippingShoppingSiteList = array("1"=>"amazon.com","ebay.com","ralphlauren.com","gap.com","drugstore.com","6pm.com","yankeecandle.com","hockeymonkey.com","diapers.com","hm.com","carters.com","jcrew.com","coachfactory.com");

// 시세 종류
$domesticPriceGubunList = array("gold"=>"순금","18gold"=>"18k","14gold"=>"14k","platinum"=>"백금","palladium"=>"팔라듐","silver"=>"은");
$domesticPriceGubunColorList = array("gold"=>"#ff983c","18gold"=>"#ff983c","14gold"=>"#ff983c","platinum"=>"#005640","palladium"=>"#002855","silver"=>"#545454");

// 상점테이블추가
$g5['g5_group_cnt_pay_table'] = G5_TABLE_PREFIX.'group_cnt_pay'; // 공동구매 설정 테이블

$g5['g5_shop_group_purchase_table'] = G5_SHOP_TABLE_PREFIX.'group_purchase'; // 공동구매 테이블
$g5['g5_shop_group_purchase_option_table'] = G5_SHOP_TABLE_PREFIX.'group_purchase_option'; // 공동구매 테이블
$g5['g5_shop_group_purchase_group_table'] = G5_SHOP_TABLE_PREFIX.'group_purchase_group'; // 공동구매 그룹 테이블
$g5['g5_shop_group_purchase_addr_table'] = G5_SHOP_TABLE_PREFIX.'group_purchase_addr'; // 공동구매 그룹 테이블

$g5['g5_purchase_order_table'] = G5_TABLE_PREFIX.'purchase_order'; // 구매 테이블
$g5['g5_purchase_cart_table'] = G5_TABLE_PREFIX.'purchase_cart'; // 구매 테이블

$g5['g5_domestic_price_table'] = G5_TABLE_PREFIX.'domestic_price'; // 국내시세 테이블
$g5['g5_alliance_table'] = G5_TABLE_PREFIX.'alliance'; // 제휴문의 테이블

$g5['g5_bo_notice_table'] = G5_TABLE_PREFIX.'write_notice'; // 공지사항 테이블
$g5['g5_bo_suggest_table'] = G5_TABLE_PREFIX.'write_suggest'; // 건의사항 테이블
$g5['g5_bo_faq_table'] = G5_TABLE_PREFIX.'write_FAQ'; // faq 테이블
$g5['g5_bo_event_table'] = G5_TABLE_PREFIX.'write_event'; // 이벤트 테이블
$g5['g5_cus_slide_table'] = G5_TABLE_PREFIX.'customer_slide'; // Customer Slide 관리 테이블
$g5['g5_idpw_auth_table'] = G5_TABLE_PREFIX.'idpw_auth'; // 아이디,비번 찾기 테이블
$g5['g5_new_family_table'] = G5_TABLE_PREFIX.'write_new_family'; // 새로운 가족 테이블
$g5['g5_write_free_table'] = G5_TABLE_PREFIX.'write_free'; // 자유게시판 테이블
$g5['g5_write_best_table'] = G5_TABLE_PREFIX.'write_best'; // 베스트게시판 테이블
$g5['g5_write_free_talk_table'] = G5_TABLE_PREFIX.'write_free_talk'; // 프리톡 테이블
$g5['g5_write_essey_table'] = G5_TABLE_PREFIX.'write_essey'; // 코인수집에세이 테이블
$g5['g5_write_know_table'] = G5_TABLE_PREFIX.'write_know'; // 코인지식인 테이블
$g5['g5_community_slide_table'] = G5_TABLE_PREFIX.'community_slide'; // 커뮤니티 슬라이드관리 테이블
$g5['g5_community_gall_slide_table'] = G5_TABLE_PREFIX.'community_gall_slide'; // 커뮤니티 슬라이드(갤러리)관리 테이블
$g5['g5_write_photo_gallery_table'] = G5_TABLE_PREFIX.'write_photo_gallery'; // 포토갤러리 테이블
$g5['g5_photo_gal_recomm_table'] = G5_TABLE_PREFIX.'photo_gal_recomm'; // 포토갤러리 추천 테이블
$g5['g5_write_bestshot_table'] = G5_TABLE_PREFIX.'write_bestshot'; // 베스트샷 테이블
$g5['g5_write_comm_suggest_table'] = G5_TABLE_PREFIX.'write_comm_suggest'; // 커뮤니티 건의사항 테이블
$g5['g5_item_type_icon_table'] = G5_TABLE_PREFIX.'item_type_icon'; // 상품유형아이콘관리 테이블
$g5['g5_gp_item_type_icon_table'] = G5_TABLE_PREFIX.'gp_item_type_icon'; // 공동구매 상품유형아이콘관리 테이블
$g5['g5_total_amount_table'] = G5_TABLE_PREFIX.'total_amount'; // 공동구매 총액관리 테이블
$g5['g5_shop_gpitem_qa_table'] = G5_TABLE_PREFIX.'shop_gpitem_qa'; // 공동구매 상품문의 테이블
$g5['new_win_table'] = G5_TABLE_PREFIX.'shop_new_win'; // 새창 테이블
$g5['g5_shop_auction_max_table'] = G5_TABLE_PREFIX.'shop_auction_max'; // 최대경매가입찰 테이블
$g5['g5_shop_option1_table'] = G5_TABLE_PREFIX.'shop_option1'; // 상품 옵션1 테이블
$g5['g5_shop_option2_table'] = G5_TABLE_PREFIX.'shop_option2'; // 상품 옵션2 테이블

$g5['g5_consulting_file_table'] = G5_TABLE_PREFIX.'consulting_file'; // 컨설팅 설명회 파일

$g5['g5_shop_group_purchase_default_table'] = G5_SHOP_TABLE_PREFIX.'group_purchase_default'; // 컨설팅 설명회 파일


function getSelectArrayList($arrayList,$name,$id,$selected="",$event="")
{
	$str = "<select id=\"$id\" name=\"$name\" $event>";
    foreach($arrayList as $key=>$vars) {
        $str .= option_selected_nobr($key, $selected, $vars);
    }
    $str .= "</select>";
    return $str;
}

function getSelectArrayList2($arrayList,$name,$id,$selected="",$event="")
{
	$str = "<select id=\"$id\" name=\"$name\" $event>";
    foreach($arrayList as $vars) {
        $str .= option_selected_nobr($vars, $selected, $vars);
    }
    $str .= "</select>";
    return $str;
}

include_once(G5_LIB_PATH.'/purchase.lib.php');
?>