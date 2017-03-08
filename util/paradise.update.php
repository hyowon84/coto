<?
include_once('./_common.php');
header("Content-Encoding: utf-8");

$list_sql = "	SELECT	DU.number,
											DU.url,
											DU.mod_date,
											DS.charge,
											DS.duty
							FROM		data_url DU
											LEFT JOIN data_src DS ON (DS.cate1 = DU.cate1 AND DS.cate2 = DU.cate2)
							WHERE		DU.site = 'PARADISE'
							AND			DU.jaego > 0
							ORDER BY DU.mod_date ASC
							LIMIT  50
";
$result = sql_query($list_sql);


$업데이트대상목록 = '';

while($row = mysql_fetch_array($result)) {
	$업데이트대상목록 .= "$row[number],";
	$gp_site = $row[url];

	$gp_charge = $row[charge];
	$gp_duty = $row[duty];

	if($mode == 'jhw') {
		echo "<br><br><br>".$gp_site."<br>";
	}

	if(!$gp_site) {
		$fail_count++;
		continue;
	}

	$gpRow_sql = " 	SELECT	GP.gp_id
									FROM		g5_shop_group_purchase GP
									WHERE		GP.gp_site	=	'$gp_site'
	";
	$gpRow = sql_fetch($gpRow_sql);


	if($gpRow[gp_id]){
		$gp_id = $gpRow[gp_id];
	}

	if($mode == 'jhw') {
		echo "gpRow : ";
		print_r($gpRow);
		echo "<br>";
	}

	$Response = curl($gp_site);


	if(!$Response) {
		$fail_count++;
		continue;
	}


	//초기화
	$gpPricing = array();
	$img_tag = '';
	$옵션 = '';
	$match_title = '';
	$match_img = '';
	$tmp_jaego = '';
	$match_wcmtable = '';
	$match_code = '';


	if(stristr($gp_site,"paradisemint.com")){
		echo "site : ".$gp_site."<br>";

		/* APMEX는 URL에 코드번호가 존재함 */
		preg_match_all("/_([0-9]+)\.html/",$gp_site,$match_code);
		$gpcode = "PM_".$match_code[1][0];

		/* 카테고리 추출 */
		unset($cate_tmp);
		unset($ca_name);
		unset($srow);
		unset($seq_code);


		$본체 = explodeCut($Response, '<!--START: CATEGORY_FULLINE-->', '<!--END: extended_description-->');
		$cate_tmp = strip_tags(explodeCut($본체, '<td class="item" colspan="2" valign="top">', '<!--END: CATEGORY_FULLINE-->'));
		preg_match_all("/([A-Za-z0-9\s]+)/",$cate_tmp,$match_cate);



		//상품명 추출
		$gp_name = trim(explodeCut($본체,'<td class="page_headers" width="90%">','</td>'));
		$gp_name = 개행문자삭제($gp_name);
		// 	$gp_name = 공백문자삭제($gp_name);

		/* 상품명이 존재하지 않을경우 IP차단의심되므로 현재 사용중인 프록시IP 사용불가로 업데이트후 중지 */
		if( !(strlen($gp_name) > 5) ) {
			print_r($gp_name);
			continue;
		}

		/* 상품주문 최대신청 제한갯수 추출 */
		preg_match_all("/<div id\=\"availability\">([0-9]+)\s*In Stock<\/div>/",$본체,$match_jaego); //		\"\/product\/[\/a-zA-Z0-9_\.\?\=]+\">
		$jaego = $match_jaego[1][0];



		//카테고리명
		//0번째는 대카테고리인, 1번째부터 입력
		$연결카테고리 = 'PM';

		for($i=1; $i < count($match_cate[1]); $i++) {
			if($mode == 'jhw') {
				echo "match_cate : ".$match_cate[1][$i]."<br>";
			}

			$시작 = $i * 2 + 1;
			$끝 = $i * 2 + 2;

			$ca_name = $match_cate[1][$i];

			$연결카테고리조건 = ($연결카테고리) ? " AND	ca_id LIKE '".$연결카테고리."%' " : '';

			$sel_sql = "SELECT	*
									FROM		g5_shop_category
									WHERE		ca_name = '$ca_name'
									AND			LENGTH(ca_id) = $끝
									$연결카테고리조건
			";
			$srow = mysql_fetch_array(sql_query($sel_sql));

			if($mode == 'jhw') {
				echo $sel_sql."<br>";
				echo "$gpcode /  $ca_name / $연결카테고리 / $i 번째<br>";
			}

			//카테고리가 존재하지 않을경우 새로 생성
			if(!$srow[ca_id]) {
				if(!$연결카테고리) $연결카테고리 = 'PM';

				$seq_sql = "	SELECT	LPAD( COALESCE(	(	SELECT	SUBSTR(MAX(ca_id),$시작,2)
																								FROM		g5_shop_category
																								WHERE		ca_id LIKE '".$연결카테고리."%'
																								AND			LENGTH(ca_id) = $끝
																							),'00'), 2, '0')
															AS ca_id
											FROM		DUAL
				";

				if($mode == 'jhw') {
					echo $seq_sql;
					echo "<br><br>";
				}

				list($seq_code) = mysql_fetch_array(sql_query($seq_sql));

				$v_10진수 = hexdec($seq_code) + 1;
				$v_16진수 = dechex($v_10진수);

				if($mode == 'jhw') {
					echo "10진수( $v_10진수 ) ,  16진수( $v_16진수 ) , ";
				}

				$v_16진수 = ($v_10진수 < 16) ? '0'.$v_16진수 : $v_16진수;

				if($mode == 'jhw') {
					echo " 변환16진수( $v_16진수 ) <br><br>";
				}

				$연결카테고리 .=  strtoupper($v_16진수);

				if($mode == 'jhw') {
					echo "새로생성 - $연결카테고리 <br>";
				}

				/* INSERT SQL 자동생성*/
				$sql = "SELECT	*
								FROM		v_tb A
								WHERE		A.tb = 'g5_shop_category'
								AND			A.sel_col != 'ca_id,'
								AND			A.sel_col != 'ca_name,'
				";

				$vtbresult = sql_query($sql);

				/* 기준이 되는 카테고리 템플릿 레코드. CA_NAME, CA_ID 제외하곤 나머지 값들은 변동사항이 없는한 모두 동일 */
				$val_sql = "SELECT	*
										FROM		g5_shop_category
										WHERE		ca_id = 'PM'
				";
				$v_result = sql_query($val_sql);
				$value = mysql_fetch_array($v_result);

				$ins_sql = "INSERT INTO g5_shop_category	SET	\r\n";

				while($vtb = mysql_fetch_array($vtbresult)) {
					$값 = $value[$vtb[COL]];
					$ins_sql .= " $vtb[COL] = '".$값."', \r\n";
				}
				$ins_sql .= "ca_name = '$ca_name', ";
				$ins_sql .= "ca_id = '$연결카테고리' ";

				sql_query($ins_sql);

			}
			else {
				$연결카테고리 = $srow[ca_id];

				if($mode == 'jhw') {
					echo "패스 존재할경우 기존 코드로 대체 - $연결카테고리 <br>";
				}
			}


		} //for end


		//이미지 추출
		preg_match_all("/thumbnail.asp\?file\=assets\/images\/[\/a-zA-Z0-9\_\.\?\=]+\&maxx\=500\&maxy\=0/",$본체,$match_img); //
		$img_url = $match_img[0];
		$gp_img = "http://paradisemint.com/".$img_url[0];


		for($i = 0; $i < count($img_url); $i++) {
			$img_tag .= '<img src="http://paradisemint.com/'.$img_url[$i].'" width="450" height="450" /><br>';
		}


		/* 본체 */
		$gp_explan_temp = explodeCut($본체,'<!--START: extended_description-->','<!--END: extended_description-->');
		$gp_explan_temp = explode('Description</td>',$gp_explan_temp);
		$gp_explan = $gp_explan_temp[1];

		$gp_explan = iconv("utf-8","utf-8//IGNORE",$gp_explan);


		$gp_explan = "<center>".$img_tag."</center>".$gp_explan;
		$gp_explan = addslashes($gp_explan);

		$tmp_gp_pricing = getExplodeValue(getExplodeValue($Response,"<table class=\"table-volume-pricing\">","</table>"),"<tbody>","</tbody>");
		$gp_pricing = explode("</tr>",str_replace(" itemprop=price","",str_replace(" itemprop=\"price\"","",$tmp_gp_pricing)));

		preg_match_all("/<div id\=\"price\" style\=\"display:inline\" class\=\"price\">[\$]*([0-9]+\.*[0-9]*)<\/div>/",$본체,$match_price);


		$gpPricing[0][po_sqty] = 0;
		$gpPricing[0][po_eqty] = 99999;
		$gpPricing[0][po_cash_price] = $match_price[1][0];
		$gpPricing[0][po_card_price] = $match_price[1][0]*1.03;


	} // if(stristr($gp_site,"apmex.com")) end

	if($mode == 'jhw') {
		print_r($gp_pricing);
		echo "<br>";
	}


	/* 추출한 데이터에서 코드번호가 존재할경우 대체 */
	if(strlen($gpcode) > 3) {
		$gp_id = $gpcode;
	}

	$gp_name = str_replace("'", "\'", $gp_name);


	/* 옵션 데이터 DELETE 후 INSERT */
	sql_query("delete from $g5[g5_shop_group_purchase_option_table] where gp_id = '$gp_id'");

	$opt_sql = "";

	for($i=0;$i<count($gpPricing);$i++){

		$opt_sql = "INSERT	INTO	$g5[g5_shop_group_purchase_option_table] 	SET
													gp_id = '$gp_id',
													po_num = '$i',
													po_sqty = '".$gpPricing[$i][po_sqty]."',
													po_eqty = '".$gpPricing[$i][po_eqty]."',
													po_cash_price = '".$gpPricing[$i][po_cash_price]."',
													po_card_price = '".$gpPricing[$i][po_card_price]."'
													,po_jaego		=	'$jaego'
		";
		sql_query($opt_sql);

		if($mode == 'jhw') {
			echo $opt_sql."<br>";
		}
	}

	if(strlen($gpRow[gp_id]) > 3) {
		$편집모드 = "수정";
	} else {
		$편집모드 = "입력";
	}

	/* 상품가격 변동성 기록 */
	flowProductPriceSave($gp_id,$gpPricing,$jaego);

	/* 금/은 여부 판단 */
	//$gp_metal_type = distinctGoldSilver($연결카테고리,$gp_name,$gp_explan);
	//echo "$gp_metal_type : $gp_metal_don OZ : ";

	/* 볼륨프라이싱은 2개이상 */
	if(strlen($opt_sql) > 5 && count($gpPricing) >= 2) {

		echo "<font color='green'>".$gp_id."상품의 볼륨프라이싱 데이터 ".count($gpPricing)."가지 범위 $편집모드 완료</font><br>";

	} else if(strlen($opt_sql) > 5 && count($gpPricing) == 1) {

		echo "<font color='blue'>".$gp_id."상품의 단품가격 데이터 $편집모드 완료</font><br>";

	} else {

		echo "<font color='red'>".$gp_id."상품 가격정보 $편집모드 실패 </font><br>";

		if($mode == 'jhw') {
			print_r($gpPricing);
			echo "<br>";
			print_r($opt_sql);
			echo "<br>";
		}

	}


	if($gp_type == 1) $gp_type1 = 1;
	if($gp_type == 2) $gp_type2 = 1;
	if($gp_type == 3) $gp_type3 = 1;
	if($gp_type == 4) $gp_type4 = 1;
	if($gp_type == 5) $gp_type5 = 1;
	if($gp_type==""){
		$gp_type1 = "";
		$gp_type2 = "";
		$gp_type3 = "";
		$gp_type4 = "";
		$gp_type5 = "";
	}

	//정렬순서 갱신순서대로 하기 위한 값 조절
	$gp_order = ($gp_order >= 0) ? $gp_order : (-1 * mktime());


	$sql_common = "	ca_id		 				= '$연결카테고리',
									ca_id2					= '".$ca_id2."',
									ca_id3					= '".$ca_id3."',
									gp_site					= '$gp_site',
									gp_name					= '$gp_name',
									gp_img					= '$gp_img',
									gp_objective_price	= '0',
									gp_metal_type		= '$gp_metal_type',
									gp_metal_don		= '$gp_metal_don',
									gp_explan				= '".$gp_explan."',
									gp_use		  		= '1',
									gp_order				= '".$gp_order."',
									gp_type1				= '".$gp_type1."',
									gp_type2 				= '".$gp_type2."',
									gp_type3 				= '".$gp_type3."',
									gp_type4 				= '".$gp_type4."',
									gp_type5				= '".$gp_type5."',
									gp_charge				= '".$gp_charge."',
									gp_duty					= '".$gp_duty."',
									it_type					= '".$gp_type."',
									gp_sc_price			= '5000',
									jaego			= '$jaego',
									gp_update_time	= '".G5_TIME_YMDHIS."'
	";

	$gpid_sql = "	SELECT	gp_id
								FROM		{$g5['g5_shop_group_purchase_table']}
								WHERE		gp_id	= '$gp_id'
	";

	$gpid = sql_fetch($gpid_sql);


	if($mode == 'jhw') {
// 		echo "$gpRow_sql<br>";
// 		echo "$gpid_sql<br>";
// 		echo "$gpid<br><br><br>";
	}

	if($gpid[gpid]) {
		echo "기존에 입력되있는 상품정보가 존재합니다 ".$gpid[gpid]."<br>";
	}

	if($gpRow[gp_id])	{
		if($gpid) {
			$갱신ID = $gpid[gp_id];
		} else {
			$갱신ID = $gpRow[gp_id];
		}

		$sql = "	UPDATE {$g5['g5_shop_group_purchase_table']}	SET
												$sql_common
							WHERE		gp_id = '$갱신ID'
							AND			ca_id NOT LIKE 'CT%'
							AND			ca_id NOT LIKE 'OD%'
		";
	}
	else {
		$sql = " INSERT INTO {$g5['g5_shop_group_purchase_table']} SET gp_id = '$gp_id', $sql_common	";
	}
	db_log($sql,'g5_shop_group_purchase',"딜러업체상품 입력 또는 갱신 ");

	if($mode == 'jhw') {
		echo $sql."<br />";
	}
	sql_query($sql);

	$succ_count++;

	flush();
	ob_flush();
	ob_end_flush();
	usleep($sleepsec);

	$연결카테고리 = '';

	$upd_sql = "	UPDATE	data_url	SET
													it_id	=	'$gp_id',
													jaego = '$jaego',
													mod_date	= now()
								WHERE		number = '$row[number]'
	";
	sql_query($upd_sql);

	$연결카테고리 = '';
	$seq_code = '';
	$srow = '';

} // while end

$업데이트대상목록 = substr($업데이트대상목록,0,strlen($업데이트대상목록)-1);


if(strlen($업데이트대상목록) > 0) {
	$upd_sql = "	UPDATE	data_url	SET
													mod_date	= now()
								WHERE		number IN ($업데이트대상목록)
	";
	sql_query($upd_sql);
}
?>

<h1><?php echo $g5['title']; ?></h1>

<div class="local_desc01 local_desc">
<p>상품등록을 완료했습니다.</p>
</div>

<dl id="excelfile_result">
<dt>총상품수</dt>
<dd><?php echo number_format($total_count); ?></dd>
<dt>완료건수</dt>
<dd><?php echo number_format($succ_count); ?></dd>
<dt>실패건수</dt>
<dd><?php echo number_format($fail_count); ?></dd>
<?php if($fail_count > 0) { ?>
<dt>실패상품코드</dt>
<dd><?php echo implode(', ', $fail_it_id); ?></dd>
<?php } ?>
<?php if($dup_count > 0) { ?>
<dt>상품코드중복건수</dt>
<dd><?php echo number_format($dup_count); ?></dd>
<dt>중복상품코드</dt>
<dd><?php echo implode(', ', $dup_it_id); ?></dd>
<?php } ?>
</dl>