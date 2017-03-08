<?
include_once('./_common.php');
header("Content-Encoding: utf-8");

// $data_config = mysql_fetch_array(sql_query("SELECT	*	FROM	data_config"));
// if($data_config['APMEX'] != 'Y') exit;

// $proxyip_sql = "SELECT	*
// 								FROM		data_proxyip
// 								WHERE		stats NOT IN (99)
// 								LIMIT 70
// ";
// $proxy_result = sql_query($proxyip_sql);
// $proxy = mysql_fetch_array($proxy_result);
// $proxy_ip = $proxy[ip].":".$proxy[port];
// if($mode == 'jhw') echo $proxy_ip."<br><br>";

// $list_sql = "	SELECT	DU.number,
// 											DU.url,
// 											DU.mod_date,
// 											DS.charge,
// 											DS.duty
// 							FROM		data_url DU
// 											LEFT JOIN data_src DS ON (DS.cate1 = DU.cate1 AND DS.cate2 = DU.cate2)
// 							WHERE		DU.site = 'APMEX'
// 							AND			DU.jaego > 0
// 							#AND			DU.it_id IN ('AP9024')
// 							ORDER BY DU.mod_date ASC
// 							LIMIT  70
// ";
// $result = sql_query($list_sql);



$ch = curl_init();


$url = "http://www.freeproxylists.net/?page=1";

$ch = curl_init();

curl_setopt( $ch, CURLOPT_HEADER, 1);
curl_setopt( $ch, CURLINFO_HEADER_OUT, 1);
curl_setopt( $ch, CURLOPT_AUTOREFERER, 1);
curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt( $ch, CURLOPT_POST, 1);
curl_setopt( $ch, CURLOPT_REFERER, $url);
curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:2.0.1) Gecko/20100101 Firefox/4.0.1');
curl_setopt( $ch, CURLOPT_URL, $url);
$store = curl_exec($ch);

// curl_setopt( $ch, CURLOPT_COOKIEJAR, $ckfile);
// curl_setopt( $ch, CURLOPT_COOKIEFILE, $ckfile);
// curl_setopt( $ch, CURLOPT_POSTFIELDS, $to_post_field);

print_r($store);
exit;

for($i=1; $i < 30; $i++) {
	$site_url = "http://www.freeproxylists.net/?page=".$i;
	$Response = get_httpRequest($site_url);


	//IP   href="http://www.freeproxylists.net/101.96.11.35.html"
	preg_match_all("/href\=\"http:\/\/www.freeproxylists.net\/([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+\.html)\"/i",$Response,$match_ip);
	$IP목록 = trim($match_ip[1][0]);//0은 전체, 1은 내용
// 	$IP목록 = 개행문자삭제($IP목록);
	print_r($IP목록);

	PRINT_R($Response);

	exit;
}


$업데이트대상목록 = '';

while($row = mysql_fetch_array($result)) {
	$업데이트대상목록 .= "$row[number],";
	$gp_site = "http://www.apmex.com".$row[url];

	$gp_charge = $row[charge];
	$gp_duty = $row[duty];

	if($mode == 'jhw') {
		echo "<br><br><br>".$gp_site."<br>";
	}

	$min_site = str_replace('https:', '', $gp_site);
	$min_site = str_replace('http:', '', $min_site);

	$gpRow_sql = " 	SELECT	GP.gp_id
									FROM		g5_shop_group_purchase GP
									WHERE		GP.gp_site	LIKE	'%$min_site'
	";
	$gpRow = sql_fetch($gpRow_sql);


	$Response = curl($gp_site,$proxy_ip);

	/* APMEX 포워딩된 URL일경우 */
	preg_match_all("/Object moved to/i",$Response,$moved); //
	if($moved[0][0] == 'Object moved to') {
		preg_match_all("/\/product\/[\/a-zA-Z0-9_\.\?\=\-]+/i",$Response,$forward); //
		$gp_site = "http://www.apmex.com".$forward[0][0];

		$Response = curl($gp_site,$proxy_ip);
	}

	if(!$Response) {
		$fail_count++;
		$updProxy_sql = "	UPDATE	data_proxyip	SET
																stats = 99,
																upd_date = now()
											WHERE		ip = '$proxy[ip]'
											AND			port = '$proxy[port]'
		";
		sql_query($updProxy_sql);

		$proxy = mysql_fetch_array($proxy_result);
		$proxy_ip = $proxy[ip].":".$proxy[port];
		if($mode == 'jhw') echo $proxy_ip."<br><br>";
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


	if(stristr($gp_site,"apmex.com")){
		//APMEX - 상품명 추출


		/* 상품명이 존재하지 않을경우 IP차단의심되므로 현재 사용중인 프록시IP 사용불가로 업데이트후 중지 */
		if( !(strlen($gp_name) > 5) ) {
			exit;
		}

		/* 상품주문 최대신청 제한갯수 추출 */
		preg_match_all($정규패턴['APMEX']['재고수량'],$Response,$match_jaego); //		\"\/product\/[\/a-zA-Z0-9_\.\?\=]+\">
		$prd_jaego = $match_jaego[0];

		/* 인덱스 추출 */
		preg_match_all("/\([0-9]+\,/i",$prd_jaego[0],$tmp_code);
		preg_match_all("/\,[0-9]+\,/i",$prd_jaego[0],$tmp_jaego);
		$rs_jaego = $tmp_jaego[0];
		$jaego = str_replace(',','', $rs_jaego[0]);

		if(!$jaego) {
			continue;
		}


		/* APMEX는 URL에 코드번호가 존재함 */
		$gpcode = 'AP_'.explodeCut($gp_site, '/product/', '/');

		/* 카테고리 추출 */
		unset($cate_tmp);
		unset($ca_name);
		unset($srow);
		unset($seq_code);
		$cate_tmp = explodeCut($Response, '<ol class="breadcrumb">', '</ol>');
		$cate_tmp = explode('</li>', $cate_tmp);

		//카테고리명
		//0번째는 APMEX 대카테고리인 APMEX, 1번째부터 입력
		$연결카테고리 = 'AP';//APMEX


		for($i=1; $i < count($cate_tmp)-1; $i++) {

			$cate_tmp[$i] = trim(strip_tags($cate_tmp[$i]));

			$시작 = $i * 2 + 1;
			$끝 = $i * 2 + 2;

			$ca_name = $cate_tmp[$i];

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
				if(!$연결카테고리) $연결카테고리 = 'AP';

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
					echo "<br>";
				}

				list($seq_code) = mysql_fetch_array(sql_query($seq_sql));

				$v_10진수 = hexdec($seq_code) + 1;
				$v_16진수 = dechex($v_10진수);

				if($mode == 'jhw') {
					echo "10진수( $v_10진수 ) ,  16진수( $v_16진수 ) , ";
				}

				$v_16진수 = ($v_10진수 < 16) ? '0'.$v_16진수 : $v_16진수;

				if($mode == 'jhw') {
					echo " 변환16진수( $v_16진수 ) <br>";
				}

				$연결카테고리 .=  strtoupper($v_16진수);

				if($mode == 'jhw') {
					echo "새로생성 - $연결카테고리 <br><br><br>";
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
										WHERE		ca_id = 'AP'
				";
				$v_result = sql_query($val_sql);
				$value = mysql_fetch_array($v_result);

				$ins_sql = "INSERT INTO		g5_shop_category	SET
																			ca_id = '$연결카테고리',
																			ca_order = '$value[ca_order]',
																			ca_name = '$ca_name',
																			ca_skin = '$value[ca_skin]',
																			ca_mobile_skin = '$value[ca_mobile_skin]',
																			ca_img_width = '$value[ca_img_width]',
																			ca_img_height = '$value[ca_img_height]',
																			ca_mobile_img_width = '$value[ca_mobile_img_width]',
																			ca_mobile_img_height = '$value[ca_mobile_img_height]',
																			ca_sell_email = '$value[ca_sell_email]',
																			ca_use = '$value[ca_use]',
																			ca_stock_qty = '$value[ca_stock_qty]',
																			ca_explan_html = '$value[ca_explan_html]',
																			ca_head_html = '$value[ca_head_html]',
																			ca_tail_html = '$value[ca_tail_html]',
																			ca_mobile_head_html = '$value[ca_mobile_head_html]',
																			ca_mobile_tail_html = '$value[ca_mobile_tail_html]',
																			ca_list_mod = '$value[ca_list_mod]',
																			ca_list_row = '$value[ca_list_row]',
																			ca_mobile_list_mod = '$value[ca_mobile_list_mod]',
																			ca_include_head = '$value[ca_include_head]',
																			ca_include_tail = '$value[ca_include_tail]',
																			ca_mb_id = '$value[ca_mb_id]',
																			ca_cert_use = '$value[ca_cert_use]',
																			ca_adult_use = '$value[ca_adult_use]',
																			ca_1_subj = '$value[ca_1_subj]',
																			ca_2_subj = '$value[ca_2_subj]',
																			ca_3_subj = '$value[ca_3_subj]',
																			ca_4_subj = '$value[ca_4_subj]',
																			ca_5_subj = '$value[ca_5_subj]',
																			ca_6_subj = '$value[ca_6_subj]',
																			ca_7_subj = '$value[ca_7_subj]',
																			ca_8_subj = '$value[ca_8_subj]',
																			ca_9_subj = '$value[ca_9_subj]',
																			ca_10_subj = '$value[ca_10_subj]',
																			ca_1 = '$value[ca_1]',
																			ca_2 = '$value[ca_2]',
																			ca_3 = '$value[ca_3]',
																			ca_4 = '$value[ca_4]',
																			ca_5 = '$value[ca_5]',
																			ca_6 = '$value[ca_6]',
																			ca_7 = '$value[ca_7]',
																			ca_8 = '$value[ca_8]',
																			ca_9 = '$value[ca_9]',
																			ca_10 = '$value[ca_10]'
				";

				sql_query($ins_sql);
				echo $ins_sql."<br>";

			}
			else {
				$연결카테고리 = $srow[ca_id];

				if($mode == 'jhw') {
					echo "패스 존재할경우 기존 코드로 대체 - $연결카테고리 <br><br><br>";
				}
			}


		} //for end


		preg_match_all($정규패턴['APMEX']['상품IMG'],$Response,$match_img); //
		$img_url = $match_img[0];	//모든이미지
		$gp_img = $img_url[0];	//첫번째 이미지


		for($i = 0; $i < count($img_url); $i++) {
			$img_tag .= '<img src="'.$img_url[$i].'" width="450" height="450" /><br>';
		}

		if(!$gp_img) $gp_img = getExplodeValue(getExplodeValue($Response,"<div class=\"small az-small\">","</div>"),"src=\"","\"");

		/* 본체 */
		$gp_explan = explodeCut($Response,"<div id=\"productdetails\">","<div class=\"clearfix");
		$spec = getExplodeValue($Response,"<div class=\"product-specs\">",'<div id="recently-viewed-products');

		/* SPEC 관련 */
		//oz 데이터    troy oz
		$spec = preg_replace("/\s+/","",$spec);

		//MetalContent:</th><td>32.15troyoz</td></tr>
		//<\/th><td>([0-9]+\.*[0-9]*)
// 		preg_match_all("/Metal Content:<\/th><td>([0-9]+\.*[0-9]*)/i",$spec,$match_spec);
		$spec_oz = explodeCut($Response,"Metal Content:","<div class=\"spec\">");
		$spec_oz = strip_tags($spec_oz);
		$gp_metal_don = str_replace(' troy oz', '', $spec_oz);

		$gp_explan = "<center>".$img_tag."</center>".$gp_explan;
		$gp_explan = addslashes($gp_explan);

		$tmp_gp_pricing = getExplodeValue(getExplodeValue($Response,"<table class=\"table-volume-pricing\">","</table>"),"<tbody>","</tbody>");



		$gp_pricing = explode("</tr>",str_replace(" itemprop=price","",str_replace(" itemprop=\"price\"","",$tmp_gp_pricing)));

		/*볼륨프라이싱 변환 */
		for($i=0;$i<(count($gp_pricing)-1);$i++){

			$po_qty  = getExplodeValue($gp_pricing[$i],"<td>","</td>");
			$po_cash_price  = trim(preg_replace("/[a-z\$\<\>\/]/","",getShortExplodeValue($gp_pricing[$i],"</td>",1)));
			$po_card_price  = trim(preg_replace("/[a-z\$\<\>\/]/","",getShortExplodeValue($gp_pricing[$i],"</td>",2)));

			/* Any Qunatity는 명확한 범위가 없기에 1 ~ 99999 로 설정*/
			if(strstr($po_qty,'Any&nbsp;Quantity')) {
				$po_sqty = 1;
				$po_eqty = 99999;
			}
			else if(stristr($po_qty,"-")){
				$tmpQty = explode("-",$po_qty);
				$po_sqty = trim($tmpQty[0]);
				$po_eqty = trim($tmpQty[1]);
			}

			else
			{
				$tmpQty = explode("or",$po_qty);
				$po_sqty = trim($tmpQty[0]);
				$po_eqty = (count($gp_pricing) > 1 && $i == 0) ? 1 : 99999;
			}



			$gpPricing[$i][po_sqty] = $po_sqty;
			$gpPricing[$i][po_eqty] = $po_eqty;
			$gpPricing[$i][po_cash_price] = str_replace(",","",$po_cash_price);
			$gpPricing[$i][po_card_price] = str_replace(",","",$po_card_price);
		} // for end

	} // if(stristr($gp_site,"apmex.com")) end

	if($mode == 'jhw') {
		print_r($tmp_gp_pricing);
		echo "<br>";
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
	$gp_metal_type = distinctGoldSilver($연결카테고리,$gp_name,$gp_explan);

	if(strstr($연결카테고리,'AP05')) {
		$gp_metal_type = 'GL';
	}
	else if(strstr($연결카테고리,'AP01')) {
		$gp_metal_type = 'SL';
	}

	echo "$gp_metal_type : $gp_metal_don OZ : ";

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

	if($gpRow[gp_id] || $gpid)	{
		if($gpid) {
			$갱신ID = $gpid[gp_id];
		} else {
			$갱신ID = $gpRow[gp_id];
		}

		$sql = "	UPDATE {$g5['g5_shop_group_purchase_table']}	SET
												$sql_common
							WHERE		gp_id = '$갱신ID'
		";
	}
	else {
		$sql = " INSERT INTO {$g5['g5_shop_group_purchase_table']} SET gp_id = '$gp_id', $sql_common	";
	}
	//echo $sql."<br />";
	//exit;
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
