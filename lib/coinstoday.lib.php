<?php
/**
 * Created by IntelliJ IDEA.
 * User: CTRL
 * Date: 2015-03-04
 * Time: 오후 5:37
 */

/*************************************************************************
 **
 **  코인즈 투데이용 공통 함수
 **
 *************************************************************************/

function getCategoryMenuList($caIdLikeStr) {
	global $g5;

	$ca_id_len = strlen(str_replace("_", "", $caIdLikeStr));

	$len2 = $ca_id_len + 2;

	$sql = "SELECT ca_id, ca_name FROM {$g5['g5_shop_category_table']} WHERE ca_id LIKE '".$caIdLikeStr."' AND LENGTH(ca_id) = $len2 AND ca_use = '1' ORDER BY ca_id";

	$return_array = array();
	$result = mysql_query($sql);

	while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{

		$row2 = sql_fetch(" select count(*) as cnt from {$g5['g5_shop_item_table']} where (ca_id like '{$row['ca_id']}%' or ca_id2 like '{$row['ca_id']}%' or ca_id3 like '{$row['ca_id']}%') and it_use = '1'  ");

		$row_array["id"] = $row["ca_id"];
		$row_array["name"] = $row["ca_name"];
		$row_array["cnt"] = $row2["cnt"];

		array_push($return_array, $row_array);
	}

	return json_encode($return_array);
}

//상품 이름 통합 검색
function getItemListUsingIntegrationSearching($searchWord,$curPage,$requestRow) {
	global $g5;

	$delimiter = " ";
	$result_array = array();
	$offset_array = array();
	$return_array = array();

	// 투데이 스토어 상품 조회
	// $sqlItem = "SELECT * FROM {$g5['g5_shop_item_table']}";
	// $whereClauseItem = " WHERE ";
	// $orderClauseItem = " ORDER BY it_id";

	// $arrWord = split($delimiter, $searchWord);
	// for($i=0; $i<sizeof($arrWord) ; $i++) {
	//	 $whereClauseItem .= "it_name LIKE '%".$arrWord[$i]."%'";
	//	 if($i < ( sizeof($arrWord) - 1 ) ) {
	//		 $whereClauseItem .= " AND ";
	//	 }
	// }

	// $resultItem = mysql_query($sqlItem.$whereClauseItem.$orderClauseItem);

	// while($row = mysql_fetch_array($resultItem))
	// {
	//	 $row_array["ca_id"] = $row["ca_id"];
	//	 $row_array["it_name"] = $row["it_name"];

	//	 array_push($return_array, $row_array);
	// }

	// 공동구매 상품 조회

	$포인터 = ($curPage-1)*$requestRow;

	$cntSqlGroup = "SELECT COUNT(*) AS CNT FROM {$g5['g5_shop_group_purchase_table']}";
	$sqlGroup = "SELECT * FROM {$g5['g5_shop_group_purchase_table']}";
	$whereClauseGroup = " WHERE ";
	$orderClauseGroup = " ORDER BY gp_update_time DESC ";
	$limitClauseGroup = " LIMIT		$포인터,$requestRow";

	$arrWord2 = split($delimiter, $searchWord);
	for($i=0; $i<sizeof($arrWord2) ; $i++) {
		$whereClauseGroup .= "gp_name LIKE '%".$arrWord2[$i]."%'";
		if($i < ( sizeof($arrWord2) - 1 ) ) {
			$whereClauseGroup .= " AND ";
		}
	}

	$row = sql_fetch($cntSqlGroup.$whereClauseGroup);

	$search_sql = $sqlGroup.$whereClauseGroup.$orderClauseGroup.$limitClauseGroup;

	$resultGroup = mysql_query($search_sql);

	while($row2 = mysql_fetch_array($resultGroup))
	{
		$row_array2["it_id"] = $row2["gp_id"];
		$row_array2["ca_id"] = $row2["ca_id"];
		$row_array2["it_name"] = $row2["gp_name"];
		$row_array2["it_img"] = urlencode($row2["gp_img"]);
		$row_array2["it_price"] = $row2["gp_price"];
		$row_array2["it_card_price"] = $row2["gp_card_price"];

		array_push($offset_array, $row_array2);
	}

	$return_array = array('result'=>array('total'=>$row[CNT],'item'=>$offset_array));

	return json_encode_unicode($return_array);
}

function getJointPurchaseDate($gubunCode) {
	global $g5;

	$where = ($gubunCode) ? " AND gubun_code='$gubunCode'" :  "";

	$sql = "SELECT  fr_date,
					to_date,
					group_code
			FROM	g5_group_cnt_pay
			WHERE   fr_date != '0000-00-00'
			$where";

	$return_array = array();
	$result = mysql_query($sql);

	while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$row_array["frDate"] = $row["fr_date"];
		$row_array["toDate"] = $row["to_date"];
		$row_array["groupCode"] = $row["group_code"];

		array_push($return_array, $row_array);
	}

	return json_encode($return_array);
}
?>