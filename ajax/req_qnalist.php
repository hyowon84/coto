<?php
/* 
 * AJAX 위시리스트 JSON 데이터 추출 
 * */
include_once('./_common.php');
include_once('../lib/coinstoday.lib.php');



$s_cart_id = get_session('ss_cart_id');

// 코드값 검색
if($ct_type)$ct_type_que = " and ct_type='".$ct_type."' ";
else $ct_type_que = " and ct_type != '' ";




$정렬조건 = "	ORDER	BY	Q.iq_id DESC";


switch($mode) {
	case 'gp':	/* 공동구매 관련 QNA */
		$sql = "	SELECT	Q.iq_id,
											Q.gp_id AS it_id,
											Q.mb_id,
											Q.iq_secret,
											Q.iq_name,
											Q.iq_email,
											Q.iq_hp,
											Q.iq_password,
											Q.iq_subject,
											Q.iq_question,
											Q.iq_answer,
											Q.iq_time,
											Q.iq_ip,
											GP.gp_img AS img_url
							FROM		g5_shop_gpitem_qa Q
											LEFT OUTER JOIN g5_shop_group_purchase GP ON (GP.gp_id = Q.gp_id)
		";
		break;
	case 'today':	/* 투데이스토어 관련 QNA */
		$sql = "	SELECT	Q.iq_id,
											Q.it_id,
											Q.mb_id,
											Q.iq_secret,
											Q.iq_name,
											Q.iq_email,
											Q.iq_hp,
											Q.iq_password,
											Q.iq_subject,
											Q.iq_question,
											Q.iq_answer,
											Q.iq_time,
											Q.iq_ip,
											Q.iq_gubun,
											IT.it_img1 AS img_url
							FROM		g5_shop_item_qa Q
											LEFT OUTER JOIN g5_shop_item IT ON (IT.it_id = Q.it_id)
		";
		break;
	case 'ex':
		$sql = "	SELECT	Q.iq_id,
											Q.it_id,
											Q.mb_id,
											Q.iq_secret,
											Q.iq_name,
											Q.iq_email,
											Q.iq_hp,
											Q.iq_password,
											Q.iq_subject,
											Q.iq_question,
											Q.iq_answer,
											Q.iq_time,
											Q.iq_ip,
											IT.it_img1 AS img_url
							FROM		g5_shop_item_ex Q
											LEFT OUTER JOIN g5_shop_item IT ON (IT.it_id = Q.it_id)
		";
		break;
}



/* 답변여부 관련 조건 */
switch($answer_status) {
	case 'y':
		$추가조건 = " AND (Q.iq_answer IS NOT NULL AND Q.iq_answer != '') ";
		break;
	case 'n':
		$추가조건 = " AND (Q.iq_answer = '' OR Q.iq_answer IS NULL ) ";
		break;
	default:
		break;

}


$기본조건 = "	WHERE		1=1
AND		Q.mb_id = '".$member[mb_id]."'
$추가조건
";




$qa_sql = "	$sql
				$기본조건
				$정렬조건
";

$result = sql_query($qa_sql);

//



$cart_count = mysql_num_rows($result);

$json = array();
$json['data'] = array();


$i = 0;
while($row = mysql_fetch_assoc($result)) {
	array_push($json['data'], $row);
}

$json['total_count'] = $cart_count;
$json_data = json_encode_unicode($json);

echo $json_data;
?>