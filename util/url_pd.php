<?php
include_once('./_common.php');
include_once(G5_MSHOP_PATH.'/head.php');
global $is_admin;



if($_GET[mode] == 'insert') {

	/* APMEX */
	if(strlen($_POST[ap]) > 10) {

		$gp_site = $_POST[ap];
		$gp_charge = $_POST[ap_charge];
		$gp_duty = $_POST[ap_duty];

		if($mode == 'insert') {
			echo "<br><br><br>".$gp_site."<br>";
		}

		if(!$gp_site) {
			$fail_count++;

		}

		$min_site = str_replace('https:', '', $gp_site);
		$min_site = str_replace('http:', '', $min_site);

		$gpRow_sql = " 	SELECT	gp_id
										FROM		{$g5['g5_shop_group_purchase_table']}
										WHERE	gp_site	LIKE		'%$min_site'
		";
		$gpRow = sql_fetch($gpRow_sql);


		if($gpRow[gp_id]){
			$gp_id = $gpRow[gp_id];
		}else{

			if(!$tmp_gp_id){
				$iCount = 0;
				do{

					$gp_id = time()+$iCount;

					// it_id 중복체크
					$sql2 = " select count(*) as cnt from {$g5['g5_shop_group_purchase_table']} where gp_id = '$gp_id' ";
					$row2 = sql_fetch($sql2);
					$iCount++;
				}while($row2['cnt']>0);
			}else $gp_id = $tmp_gp_id;

		}



		echo $gp_site;

		$Response = get_httpRequest($gp_site,"http://www.gainesvillecoins.com");

		print_r($Response);
		exit;

// 		$curl = curl_init();
// 		curl_setopt($curl, CURLOPT_URL, $gp_site);
// 		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
// 		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, TRUE);	//가끔가다가 인증서버에 대한 옵션이 있는데 믿을만 하다면 FALSE설정해도 됨
// 		// curl_setopt($curl, CURLOPT_USERPWD, "vanchosun:van1158");
// 		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT,5);
// 		curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
// 		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,0);
// 		$Response = curl_exec($curl);
// 		curl_close($curl);

		/* APMEX 포워딩된 URL일경우 */
		preg_match_all("/Object moved to/i",$Response,$moved); //
		if($moved[0][0] == 'Object moved to') {
			preg_match_all("/\/product\/[\/a-zA-Z0-9_\.\?\=\-]+/i",$Response,$forward); //
			$gp_site = "http://www.apmex.com".$forward[0][0];
			$forward_site = $gp_site;

			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $gp_site);
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, TRUE);    //가끔가다가 인증서버에 대한 옵션이 있는데 믿을만 하다면 FALSE설정해도 됨
			// curl_setopt($curl, CURLOPT_USERPWD, "vanchosun:van1158");
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT,5);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,0);
			$Response = curl_exec($curl);
			curl_close($curl);
		}


		if(!$Response) {
			$fail_count++;

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

				if($mode == 'insert') {
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

					if($mode == 'insert') {
						echo $seq_sql;
						echo "<br><br>";
					}

					list($seq_code) = mysql_fetch_array(sql_query($seq_sql));

					$v_10진수 = hexdec($seq_code) + 1;
					$v_16진수 = dechex($v_10진수);

					if($mode == 'insert') {
						echo "10진수( $v_10진수 ) ,  16진수( $v_16진수 ) , ";
					}

					$v_16진수 = ($v_10진수 < 16) ? '0'.$v_16진수 : $v_16진수;

					if($mode == 'insert') {
						echo " 변환16진수( $v_16진수 ) <br><br>";
					}

					$연결카테고리 .=  strtoupper($v_16진수);

					if($mode == 'insert') {
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
											WHERE		ca_id = 'AP'
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

					if($mode == 'insert') {
						echo "패스 존재할경우 기존 코드로 대체 - $연결카테고리 <br>";
					}
				}


			} //for end

			/* 상품주문 최대신청 제한갯수 추출 */
			preg_match_all("/ShoppingCart.notifyAllowedMaxQty\([0-9]+\,.+\,[0-9]+\,/i",$Response,$match_jaego); //		\"\/product\/[\/a-zA-Z0-9_\.\?\=]+\">
			$prd_jaego = $match_jaego[0];

			/* 인덱스 추출 */
			preg_match_all("/\([0-9]+\,/i",$prd_jaego[0],$tmp_code);
			preg_match_all("/\,[0-9]+\,/i",$prd_jaego[0],$tmp_jaego);
			$rs_code = $tmp_code[0];
			$rs_jaego = $tmp_jaego[0];
			$code = str_replace(',', '', $rs_code[0]);
			$code = str_replace('(', '', $code);
			$product_jaego = str_replace(',','', $rs_jaego[0]);


			//APMEX - 상품명 추출
			preg_match_all("/<h1 class\=\"product-title\" [a-z0-9\-\'\"\=:;\s]+\s*>\s*\t*(.*)\s*\t*<\/h1>/",$Response,$match_title);
			$gp_name = trim($match_title[1][0]);//0은 전체, 1은 내용

			preg_match_all("/http:\/\/www.images-apmex.com\/images\/Catalog Images\/Products\/[\/a-zA-Z0-9_\.\?\=]+\&amp;width\=450\&amp;height\=450/i",$Response,$match_img); //
			$img_url = $match_img[0];
			$gp_img = $img_url[0];


			for($i = 0; $i < count($img_url); $i++) {
				$img_tag .= '<img src="'.$img_url[$i].'" width="450" height="450" /><br>';
			}

			//$gp_name = getExplodeValue($Response,"<h1 class=\"product-title\" itemprop=\"name\">","</h1>");
			//$gp_img = getExplodeValue(getExplodeValue($Response,"<div class=\"small\">","</div>"),"src=\"","\"");

			if(!$gp_img) $gp_img = getExplodeValue(getExplodeValue($Response,"<div class=\"small az-small\">","</div>"),"src=\"","\"");
			//$gp_explan = getExplodeValue($Response,"<div class=\"product-description\">"," <div class=\"product-specs\">");

			$gp_explan = getExplodeValue($Response,"<div class=\"product-specs\">","</div>");
			$gp_explan .= getExplodeValue($Response,"<div class=\"product-description\">","</div>");

			$gp_explan = "<center>".$img_tag."</center>".$gp_explan;
			$gp_explan = addslashes($gp_explan);

			$tmp_gp_pricing = getExplodeValue(getExplodeValue($Response,"<table class=\"table-volume-pricing\">","</table>"),"<tbody>","</tbody>");
			$gp_pricing = explode("</tr>",str_replace(" itemprop=price","",str_replace(" itemprop=\"price\"","",$tmp_gp_pricing)));


			if($mode == 'insert') {
				print_r($tmp_gp_pricing);
				echo "<br>";
				print_r($gp_pricing);
				echo "<br>";
			}


			for($i=0;$i<(count($gp_pricing)-1);$i++){

				$po_qty  = getExplodeValue($gp_pricing[$i],"<td>","</td>");
				$po_cash_price  = trim(preg_replace("/[a-z\$\<\>\/]/","",getShortExplodeValue($gp_pricing[$i],"</td>",1)));
				$po_card_price  = trim(preg_replace("/[a-z\$\<\>\/]/","",getShortExplodeValue($gp_pricing[$i],"</td>",2)));

				if(stristr($po_qty,"-")){
					$tmpQty = explode("-",$po_qty);
					$po_sqty = trim($tmpQty[0]);
					$po_eqty = trim($tmpQty[1]);
				}else{
					$tmpQty = explode("or",$po_qty);
					$po_sqty = trim($tmpQty[0]);
					$po_eqty = (count($gp_pricing) > 1 && $i == 0) ? 1 : 99999;
				}

				$gpPricing[$i][po_sqty] = $po_sqty;
				$gpPricing[$i][po_eqty] = $po_eqty;
				$gpPricing[$i][po_cash_price] = str_replace(",","",$po_cash_price);
				$gpPricing[$i][po_card_price] = str_replace(",","",$po_card_price);
			}
		}




		/* 추출한 데이터에서 코드번호가 존재할경우 대체 */
		if(strlen($gpcode) > 3) {
			$gp_id = $gpcode;
		}

		$gp_name = str_replace("'", "\'", $gp_name);


		/* 옵션 데이터 DELETE 후 INSERT */
		sql_query("delete from $g5[g5_shop_group_purchase_option_table] where gp_id = '$gp_id'");

		$ins_sql = "";

		for($i=0;$i<count($gpPricing);$i++){
			$ins_sql = "INSERT	INTO	$g5[g5_shop_group_purchase_option_table] 	SET
														gp_id = '$gp_id',
														po_num = '$i',
														po_sqty = '".$gpPricing[$i][po_sqty]."',
														po_eqty = '".$gpPricing[$i][po_eqty]."',
														po_cash_price = '".$gpPricing[$i][po_cash_price]."',
														po_card_price = '".$gpPricing[$i][po_card_price]."'
														,po_jaego		=	'$product_jaego'
			";
			sql_query($ins_sql);

			if($mode == 'insert') {
				echo $ins_sql."<br>";
			}
		}

		if(strlen($gpRow[gp_id]) > 3) {
			$편집모드 = "수정";
		} else {
			$편집모드 = "입력";
		}

		if(strlen($ins_sql) > 5 && count($gpPricing) > 1) {
			echo "<font color='green'>".$gp_id."상품의 볼륨프라이싱 데이터 ".count($gpPricing)."가지 범위 $편집모드 완료</font><br>";
		} else if(strlen($ins_sql) > 5 && count($gpPricing) == 1) {
			echo "<font color='blue'>".$gp_id."상품의 단품가격 데이터 $편집모드 완료</font><br>";
		} else {
			echo "<font color='red'>".$gp_id."상품 가격정보 $편집모드 실패 </font><br>";

			if($mode == 'insert') {
				print_r($gpPricing);
				echo "<br>";
				print_r($ins_sql);
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
										jaego			= '$product_jaego',
										gp_update_time	= '".G5_TIME_YMDHIS."'
		";

		$gpid_sql = "	SELECT	gp_id
									FROM		{$g5['g5_shop_group_purchase_table']}
									WHERE		gp_id	= '$gp_id'
		";

		$gpid = sql_fetch($gpid_sql);


		if($mode == 'insert') {
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

	}

	/* GAINSVILLE */
	if(strlen($_POST[gv]) > 10) {

		$gp_site = ($_POST[gv]) ? $_POST[gv] : '';
		$gpcode = 'GV_'.explodeCut($gp_site, '/products/', '/');
		$gain_code = explodeCut($gp_site,'/products/','/');

		$gp_charge = $_POST[gv_charge];
		$gp_duty = $_POST[gv_duty];

		if(!$gp_site) {
			$fail_count++;
			$fail_it_id.="$gp_site";

		}

		$min_site = str_replace('https:', '', $gp_site);
		$min_site = str_replace('http:', '', $min_site);

		$gpRow_sql = " 	SELECT	gp_id
										FROM		{$g5['g5_shop_group_purchase_table']}
										WHERE	gp_site	LIKE		'%$min_site'
		";
		$gpRow = sql_fetch($gpRow_sql);


		if($gpRow[gp_id]){
			$gp_id = $gpRow[gp_id];
		}else{

			if(!$tmp_gp_id){
				$iCount = 0;
				do{

					$gp_id = time()+$iCount;

					// it_id 중복체크
					$sql2 = " select count(*) as cnt from {$g5['g5_shop_group_purchase_table']} where gp_id = '$gp_id' ";
					$row2 = sql_fetch($sql2);
					$iCount++;
				}while($row2['cnt']>0);
			}else $gp_id = $tmp_gp_id;

		}


		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $gp_site);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);	//가끔가다가 인증서버에 대한 옵션이 있는데 믿을만 하다면 FALSE설정해도 됨
		// curl_setopt($curl, CURLOPT_USERPWD, "vanchosun:van1158");
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT,5);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,0);
		$Response = curl_exec($curl);
		curl_close($curl);

		/* APMEX 포워딩된 URL일경우 */
		preg_match_all("/Object moved to/i",$Response,$moved); //
		if($moved[0][0] == 'Object moved to') {
			preg_match_all("/\/product\/[\/a-zA-Z0-9_\.\?\=\-]+/i",$Response,$forward); //
			$gp_site = "http://www.gainesvillecoins.com".$forward[0][0];
			$forward_site = $gp_site;

			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $gp_site);
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, TRUE);    //가끔가다가 인증서버에 대한 옵션이 있는데 믿을만 하다면 FALSE설정해도 됨
			// curl_setopt($curl, CURLOPT_USERPWD, "vanchosun:van1158");
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT,5);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,0);
			$Response = curl_exec($curl);
			curl_close($curl);
		}


		if(!$Response) {
			$fail_count++;

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


		if(stristr($gp_site,"gainesvillecoins.com")){
			/* 카테고리 추출 */
			unset($cate_tmp);
			unset($ca_name);
			unset($srow);
			unset($seq_code);
			$cate_tmp = explodeCut($Response, '<div class="breadCrumbs overflow-wrap">', '</div>');
			preg_match_all("/<span itemprop\=\"name\">[A-Za-z0-9\s\+\-\=]+<\/span>/i",$cate_tmp, $cate_tmp2);
			$cate_export = $cate_tmp2[0];


			//카테고리명
			//0번째는 APMEX 대카테고리인 APMEX, 1번째부터 입력
			$연결카테고리 = 'GV';//APMEX


			for($i=1; $i <= count($cate_export)-1; $i++) {

				$cate_export[$i] = trim(strip_tags($cate_export[$i]));
				echo "카테고리 : ".$cate_export[$i]."<br>";

				$시작 = $i * 2 + 1;
				$끝 = $i * 2 + 2;

				$ca_name = $cate_export[$i];

				$연결카테고리조건 = ($연결카테고리) ? " AND	ca_id LIKE '".$연결카테고리."%' " : '';

				$sel_sql = "SELECT	*
										FROM		g5_shop_category
										WHERE		ca_name = '$ca_name'
										AND			LENGTH(ca_id) = $끝
										$연결카테고리조건
				";
				$srow = mysql_fetch_array(sql_query($sel_sql));

				echo $sel_sql."<br>";
				echo "$gpcode /  $ca_name / $연결카테고리 / $i 번째<br>";

				//카테고리가 존재하지 않을경우 새로 생성
				if(!$srow[ca_id]) {
					if(!$연결카테고리) $연결카테고리 = 'GV';

					$seq_sql = "	SELECT	LPAD( COALESCE(	(	SELECT	SUBSTR(MAX(ca_id),$시작,2)
																									FROM		g5_shop_category
																									WHERE		ca_id LIKE '".$연결카테고리."%'
																									AND			LENGTH(ca_id) = $끝
																								),'00'), 2, '0')
																AS ca_id
												FROM		DUAL
					";
					echo $seq_sql;
					echo "<br><br>";

					list($seq_code) = mysql_fetch_array(sql_query($seq_sql));

					$v_10진수 = hexdec($seq_code) + 1;
					$v_16진수 = dechex($v_10진수);

					echo "10진수( $v_10진수 ) ,  16진수( $v_16진수 ) , ";

					$v_16진수 = ($v_10진수 < 16) ? '0'.$v_16진수 : $v_16진수;

					echo " 변환16진수( $v_16진수 ) <br><br>";

					$연결카테고리 .= strtoupper($v_16진수);
					echo "새로생성 - $연결카테고리 <br>";


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
											WHERE		ca_id = 'GV'
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
					echo "패스 존재할경우 기존 코드로 대체 - $연결카테고리 <br>";
				}


			} //for end

	// 		/* 상품주문 최대신청 제한갯수 추출 */
	// 		preg_match_all("/ShoppingCart.notifyAllowedMaxQty\([0-9]+\,.+\,[0-9]+\,/i",$Response,$match_jaego); //		\"\/product\/[\/a-zA-Z0-9_\.\?\=]+\">
	// 		$prd_jaego = $match_jaego[0];

	// 		/* 인덱스 추출 */
	// 		preg_match_all("/\([0-9]+\,/i",$prd_jaego[0],$tmp_code);
	// 		preg_match_all("/\,[0-9]+\,/i",$prd_jaego[0],$tmp_jaego);
	// 		$rs_code = $tmp_code[0];
	// 		$rs_jaego = $tmp_jaego[0];
			$code = $gpcode;
			$product_jaego = 9999999;


			/* 상품명 추출 */
			preg_match_all("/<h1[a-z0-9\-\'\"\=:;\s]*\s*>\s*\t*(.*)\s*\t*<\/h1>/",$Response,$match_title);
			$gp_name = trim($match_title[1][0]);//0은 전체, 1은 내용


			/* 이미지 추출 */
			preg_match_all("/img-gainesvillecoins.netdna-ssl.com\/images\/products\/[\/a-zA-Z0-9_\.\?\=]+_t/i",$Response,$match_img); //
			$img_url = $match_img[0];

			for($i = 0; $i < count($img_url); $i++) {
				$imgsrc = str_replace('_t','_l',$img_url[$i]);

				if($i == 0) {
					$gp_img = 'http://'.$imgsrc.'.jpg';
				}
				$img_tag .= '<img src="http://'.$imgsrc.'.jpg" width="450" height="450" /><br>';
			}


			/* 상품상세설명 */
			$스펙정보 = explodeCut($Response,'<div class="float-right" id="pdSpecs">','</div>');
			$스펙스타일 = "<style>
			#pdSpecs table {
			  width: 80%;
			}
			#pdSpecs table tr:nth-child(even) {
			  background: #f1f1f1;
			}
			#pdSpecs table tr td:first-child {
			  color: #777;
			}
			#pdSpecs table tr td {
			  font-size: .96em;
			  padding: 8px;
			}
			</style>";
			$상세설명 = explodeCut($Response,'<div class="text">','</div>');
			$gp_explan = "$스펙스타일<center>".$img_tag."<div id='pdSpecs'>".$스펙정보."</div></center>".$상세설명;
			$gp_explan = addslashes($gp_explan);


			$가격정보테이블 = explodeCut($Response, '<table id="tbl-'.$gain_code.'" class="tbl-price">', '</table>');

			//가격제한대역폭
			preg_match_all("/[0-9\,]+[\s\-]+[0-9\,]+|[0-9\,]+\+/i",$가격정보테이블,$tmp_limit);
			$match_limit = $tmp_limit[0];

			//가격
			preg_match_all("/[\$\s]*([0-9\,]+\.[0-9]+)/i",$가격정보테이블,$tmp_price);	//$tmp_price[0]:현금, $tmp_price[1]:카드 ...
			$match_price = $tmp_price[1];



			/* 가격정보 테이블에 1+ 글자가 있을경우 단일옵션 */
			if(strstr($가격정보테이블,'1+')) {
				echo "단품<br>";

				/* 단품일경우 start */
				//  상품 Price <span id="product_price">199.00</span>
				//	 상품 Wire/Check Price  <span class="currency">$193.13</span>
				$gpPricing[0][po_sqty] = 0;
				$gpPricing[0][po_eqty] = 99999;
				$gpPricing[0][po_cash_price] = str_replace(",","",$match_price[0]);
				$gpPricing[0][po_card_price] = str_replace(",","",$match_price[1]);
				/* 단품일경우 end */
			}
			else {
				echo "볼륨<br>";

				$optcnt = 0;

				for($z = 0; $z < count($match_price); $z++) {
					//마지막 볼륨프라이싱 줄은 5 or more 라서 " "으로 분할, 그외는 '-'로 분할
					$분할문자 = ( ($z+1) == count($match_price) ) ? "+" : ' - ';	//마지막행 분할문자는 '+', 그외는 ' - '
					$범위 = explode($분할문자,$match_limit[$optcnt]);

					$옵션[$optcnt][po_sqty] = trim($범위[0]);

					//마지막 범위는 가격정보가 최소 2줄 이상이고 첫번째 행일경우 1로, 그외는 모두 99999로
					$po_eqty = (count($match_price) > 1 && $z == 0) ? 1 : 99999;

					// 분할문자 - 로 분할한 경우 범위[1]의 마지막범위값을 대입, 아니라면 위에서 설정한 po_eqty를 대입
					$옵션[$optcnt][po_eqty] = ($범위[1] && $범위[1] != '+') ? str_replace('&nbsp;','',trim($범위[1])) : $po_eqty;

					//첫번째는 구매가, 두번째 값은 카드가
					if($z%2 == 0) {
						$옵션[$optcnt][po_cash_price] = str_replace(',','',$match_price[$z]);
					} else {
						$옵션[$optcnt][po_card_price] = str_replace(',','',$match_price[$z]);
					}

					//구매가 입력후 카드가 입력후 다시 구매가 입력할때 옵션인덱스 증가
					if($z%2 == 1 && $z > 0) {
						$optcnt++;
					}
				}
				$gpPricing = $옵션;
			}

			$isDirectInp = true;
		}

		/* 추출한 데이터에서 코드번호가 존재할경우 대체 */
		if(strlen($gpcode) > 3) {
			$gp_id = $gpcode;
		}

		$gp_name = str_replace("'", "\'", $gp_name);


		/* 옵션 데이터 DELETE 후 INSERT */
		sql_query("delete from $g5[g5_shop_group_purchase_option_table] where gp_id = '$gp_id'");

		$ins_sql = "";


		if($gpPricing) {
			for($i=0;$i<count($gpPricing);$i++){
				$ins_sql = "INSERT	INTO	$g5[g5_shop_group_purchase_option_table] 	SET
															gp_id = '$gp_id',
															po_num = '$i',
															po_sqty = '".$gpPricing[$i]['po_sqty']."',
															po_eqty = '".$gpPricing[$i]['po_eqty']."',
															po_cash_price = '".$gpPricing[$i]['po_cash_price']."',
															po_card_price = '".$gpPricing[$i]['po_card_price']."'
															,po_jaego		=	'$product_jaego'
				";
				sql_query($ins_sql);
			}
		}

		if(strlen($gpRow[gp_id]) > 3) {
			$편집모드 = "수정";
		} else {
			$편집모드 = "입력";
		}

		if(strlen($ins_sql) > 5 && count($gpPricing) > 1) {
			echo "<font color='green'>".$gp_id."상품의 볼륨프라이싱 데이터 ".count($gpPricing)."가지 범위 $편집모드 완료</font>";
		} else if(strlen($ins_sql) > 5 && count($gpPricing) == 1) {
			echo "<font color='blue'>".$gp_id."상품의 단품가격 데이터 $편집모드 완료</font>";
		} else {
			echo "<font color='red'>".$gp_id."상품 가격정보 $편집모드 실패 </font>";
			print_r($gpPricing);
			print_r($ins_sql);
		}
		echo "<br>";


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
										gp_explan				= '".$gp_explan."',
										gp_use		  			= '1',
										gp_order					= '".$gp_order."',
										gp_type1					= '".$gp_type1."',
										gp_type2 				= '".$gp_type2."',
										gp_type3 				= '".$gp_type3."',
										gp_type4 				= '".$gp_type4."',
										gp_type5					= '".$gp_type5."',
										gp_charge				= '".$gp_charge."',
										gp_duty					= '".$gp_duty."',
										it_type					= '".$gp_type."',

										gp_sc_price				= '5000',
										gp_update_time = '".G5_TIME_YMDHIS."'
		";

		$gpid_sql = "	SELECT	gp_id
							FROM		{$g5['g5_shop_group_purchase_table']}
							WHERE		gp_id	= '$gp_id'
		";

		$gpid = sql_fetch($gpid_sql);


		if($개발자) {
	// 		echo "$gpRow_sql<br>";
	// 		echo "$gpid_sql<br>";
	// 		echo "$gpid<br><br><br>";
		}

		if($gpid[gpid]) {
			echo "기존에 입력되있는 상품정보가 존재합니다 ".$gpid[gpid];
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
		echo "<br><br><br>";
	}

	/* MCM */
	if(strlen($_POST[mc]) > 10) {

		if($mode == 'insert') echo $row[url]."<br>";
		$gp_site = ($_POST[mc]) ? $_POST[mc] : '';

		$gp_charge = $_POST[mc_charge];
		$gp_duty = $_POST[mc_duty];


		if(!$gp_site) {
			$fail_count++;
			$fail_it_id.="$gp_site";

		}

		$min_site = str_replace('https:', '', $gp_site);
		$min_site = str_replace('http:', '', $min_site);

		$gpRow_sql = " 	SELECT	gp_id
										FROM		{$g5['g5_shop_group_purchase_table']}
										WHERE	gp_site	LIKE		'%$min_site'
		";
		$gpRow = sql_fetch($gpRow_sql);


		if($gpRow[gp_id]){
			$gp_id = $gpRow[gp_id];
		}else{

			if(!$tmp_gp_id){
				$iCount = 0;
				do{

					$gp_id = time()+$iCount;

					// it_id 중복체크
					$sql2 = " select count(*) as cnt from {$g5['g5_shop_group_purchase_table']} where gp_id = '$gp_id' ";
					$row2 = sql_fetch($sql2);
					$iCount++;
				}while($row2['cnt']>0);
			}else $gp_id = $tmp_gp_id;

		}


		$agent = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.143 Safari/537.36';
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $gp_site);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, TRUE);	//가끔가다가 인증서버에 대한 옵션이 있는데 믿을만 하다면 FALSE설정해도 됨
		// curl_setopt($curl, CURLOPT_USERPWD, "vanchosun:van1158");
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT,5);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
		curl_setopt ($curl, CURLOPT_USERAGENT, $agent);

		$Response = curl_exec($curl);
		curl_close($curl);

		if(!$Response) {
			$fail_count++;

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


		/* 모던코인마트 */
		if(stristr($gp_site,"moderncoinmart.com")){
			/* 카테고리 추출 */
			unset($cate_tmp);
			unset($ca_name);
			unset($srow);
			unset($seq_code);
			$cate_tmp = explodeCut($Response, '<div id="location" xmlns:v="http://rdf.data-vocabulary.org/#">', '</div>');
			$백업_카테고리 = $cate_tmp;

			$cate_tmp = strip_tags($cate_tmp);
			$cate_tmp = preg_replace('/\R/i','<br>',$cate_tmp);
			$cate_tmp = preg_replace('/\s\s|br/i','',$cate_tmp);
			$cate_tmp = preg_replace('/[<>]+|Home/i','##',$cate_tmp);
			$cate_tmp = str_replace('####','',$cate_tmp);
			$cate_tmp = explode('##', $cate_tmp);


			$연결카테고리 = 'MC';//MCM

			/* 없는게 */
			if($cate_tmp[1] == '404 File Not Found') {

			}


			if($mode == 'insert') echo "######### ".$백업_카테고리." #########<br>";

			for($i=1; $i < count($cate_tmp)-2; $i++) {

				$cate_tmp[$i] = trim(strip_tags($cate_tmp[$i]));

				$시작 = $i * 2 + 1;
				$끝 = $i * 2 + 2;

				$ca_name = $cate_tmp[$i];

				/* 마지막 카테고리는 상품명이 붙어있는데 이건 카테고리가 아닌 상품명 */
				// eregi("<h1 itemprop=\"name\">(.*)</h1>",$Response,$match_title);

				if($mode == 'insert') echo "$ca_name : STRSTR : ".strstr($ca_name, '|');

				if( strstr($ca_name, '|') ) {
					if($mode == 'insert') echo "T : <br>";

				} else {
					if($mode == 'insert') echo "F : <br>";
				}

				$연결카테고리조건 = ($연결카테고리) ? " AND	ca_id LIKE '".$연결카테고리."%' " : '';

				$sel_sql = "SELECT	*
										FROM		g5_shop_category
										WHERE		ca_name = '$ca_name'
										AND			LENGTH(ca_id) = $끝
										$연결카테고리조건
				";
				$srow = mysql_fetch_array(sql_query($sel_sql));

				if($mode == 'insert') {
					echo $sel_sql."<br>";
					echo "$gpcode /  $ca_name / $연결카테고리 / $i 번째<br>";
				}

				//카테고리가 존재하지 않을경우 새로 생성
				if(!$srow[ca_id]) {
					if(!$연결카테고리) $연결카테고리 = 'MC';

					$seq_sql = "	SELECT	LPAD( COALESCE(	(	SELECT	SUBSTR(MAX(ca_id),$시작,2)
																									FROM		g5_shop_category
																									WHERE		ca_id LIKE '".$연결카테고리."%'
																									AND			LENGTH(ca_id) = $끝
																								),'00'), 2, '0')
																AS ca_id
												FROM		DUAL
					";

					if($mode == 'insert') {
						echo $seq_sql;
						echo "<br><br>";
					}

					list($seq_code) = mysql_fetch_array(sql_query($seq_sql));

					$v_10진수 = hexdec($seq_code) + 1;
					$v_16진수 = dechex($v_10진수);

					if($mode == 'insert') echo "10진수( $v_10진수 ) ,  16진수( $v_16진수 ) , ";

					$v_16진수 = ($v_10진수 < 16) ? '0'.$v_16진수 : $v_16진수;

					if($mode == 'insert') echo " 변환16진수( $v_16진수 ) <br><br>";

					$연결카테고리 .=  strtoupper($v_16진수);
					if($mode == 'insert') echo "새로생성 - $연결카테고리 <br>";


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
											WHERE		ca_id = 'MC'
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
					if($mode == 'insert') echo "패스 존재할경우 기존 코드로 대체 - $연결카테고리 <br>";
				}
			} //for end



			/* 상품코드 추출 <div id="product_code" class="product_code" itemprop="sku">SKU37505</div> */
			@eregi("<div id=\"product_code\" class=\"product_code\" itemprop=\"sku\">([A-Z0-9]+)<\/div>",$Response,$match_code);
			$gpcode = $match_code[1];

			/* 제목 */
			@eregi("<h1 itemprop=\"name\">(.*)</h1>",$Response,$match_title);
			$gp_name = strip_tags(str_replace('<br />',' ',$match_title[1]));//0은 전체, 1은

			//preg_match_all("/<img src\=\"(https:\/\/www\.moderncoinmart.com\/images\/D[A-Za-z0-9\.\/\-\s\%\_]+)/i",$Response,$match_img); //
			/* 이미지 */
			//https://www.moderncoinmart.com/images/D/2016_s%241_ngc_BlackCore_MS69_30thann_obv_web.jpg
			preg_match_all("/\'(https:\/\/www.moderncoinmart.com\/images\/D[A-Za-z0-9\.\/\-\s\%\_]+)/i",$Response,$match_img); //
			$img_url = $match_img[1];
			$gp_img = $img_url[0];

			$max_cnt = ( (count($img_url)-1) == 0 ) ? 1 : (count($img_url));
			for($i = 0; $i < $max_cnt; $i++) {
				$img_tag .= "<img src='".$img_url[$i]."' width='450' /><br>";
			}

			//preg_match_all("/\<table cellspacing\=\"0\" cellpadding\=\"0\" summary\=\"Description\"\>([.\s\t]*)\<\/table\>/",$Response,$match_bodytext);
			//$gp_explan = $match_bodytext[1];

			/* 본문설명 */
			$gp_explan = explodeCut($Response,'<td class="descr" itemprop="description">','</td>');
			$gp_explan = "<center>".$img_tag."</center>".$gp_explan;
			$gp_explan = str_replace("'", '', $gp_explan);
			$gp_explan = str_replace('"', '', $gp_explan);

			/* 스펙 */
			$스펙 = explodeCut($Response,'<table class="data-table">','</table>');
			$gp_explan = "<table align=\"center\">".$스펙."</table>".$gp_explan;


			/*옵션테이블 추출*/
			@eregi('var product_avail \= ([0-9]+)',$Response,$tmp_jaego);//상품최대신청가능수량(재고)
			$jaego = $tmp_jaego[1];
			@eregi("<table class=\"data-table volume-pricing-table\">",$Response,$match_wcmtable);

			/* 볼륨 테이블 존재할경우 옵션으로 등록 */
			if(strlen($match_wcmtable[0]) > 10) {

				$tabletag = explodeCut($Response,'<table class="data-table volume-pricing-table">','</table>');
				$tabletag = str_replace("\r\n",'',$tabletag);
				$tabletag = str_replace("\t",'',$tabletag);
				$tabletag = preg_replace("/\s\s/","",$tabletag);

				preg_match_all("/[0-9\,]+\-*[0-9\,]*[a-z\s]*&nbsp;/",$tabletag,$tmp_opt);
				$match_opt = $tmp_opt[0];
				preg_match_all("/<span class=\"currency\">[\$]*([0-9\.\,]+)<\/span>/",$tabletag,$tmp_price);
				$match_price = $tmp_price[1];

				$optcnt = 0;

				for($i = 0; $i < count($match_price); $i++) {
					$분할문자 = ( ($i+1) == count($match_price) ) ? " " : '-';
					$범위 = explode($분할문자,$match_opt[$optcnt]);

					$옵션[$optcnt][po_sqty] = trim($범위[0]);

					$po_eqty = (count($match_price) > 1 && $optcnt == 0) ? 1 : 99999;

					$옵션[$optcnt][po_eqty] = ($범위[1] && $범위[1] != 'or') ? str_replace('&nbsp;','',trim($범위[1])) : $po_eqty;

					//첫번째는 카드가, 두번째 값은 구매가
					if($i%2 == 0) {
						$옵션[$optcnt][po_card_price] = str_replace(',','',$match_price[$i]);
					} else {
						$옵션[$optcnt][po_cash_price] = str_replace(',','',$match_price[$i]);
					}

					//카드가 입력후 구매가 입력후 다시 카드가 입력할때 옵션인덱스 증가
					if($i%2 == 1 && $i > 0) {
						$optcnt++;
					}

					$gpPricing = $옵션;
				}
			}
			else {
				/*'
				<table width="100%" cellpadding="0" cellspacing="0">
				<tbody><tr>
				<td class="property-name product-taxed-price">Retail Price:</td>
				<td class="property-value product-taxed-price" colspan="2" style="font-weight: bold; color:#C21515;text-decoration:line-through;"><span class="currency">$89.00</span></td>
				</tr>

				<tr>
				<td valign="top">
				<span class="product-price-text">Price:</span>
				</td>
				<td class="property-value" valign="top" colspan="2">
				<span class="product-price-currency"><span class="currency">$<span id="product_price">59.00</span></span></span>
				<span class="product-price-currency"></span>
				</td>
				</tr>
				<tr>
				<td valign="top">
				<span class="product-price-text">Wire/Check Price<span class="helptip-question wcm-helptip-anchor" data-type="wireprice"></span>:</span>
				</td>
				<td class="property-value" valign="top" colspan="2">
				<span class="product-price-currency"><span class="currency">$57.26</span></span>
				</td>
				</tr>
				</tbody></table>
				';*/

				/* 리테일 가격이 들어가있을경우 제거해야함 */
				if(strstr($Response, 'Retail Price')) {
					$temp = explode('<td class="property-name product-taxed-price">Retail Price:</td>', $Response);
					$결과 = explode('</tr>',$temp[1]);
					$필터링된소스 = $결과[1].$결과[2];
				} else {
					$필터링된소스 = $Response;
				}

				/* 단품일경우 start */
				//  상품 Price <span id="product_price">199.00</span>
				//	 상품 Wire/Check Price  <span class="currency">$193.13</span>
				@eregi("<span id=\"product_price\">([0-9\.\,]+)<\/span>",$필터링된소스,$match_card);
				$po_card_price = $match_card[1];

				@eregi("<span class=\"currency\">[\$]*([0-9\.\,]+)<\/span>",$필터링된소스,$match_cash);
				$po_cash_price = $match_cash[1];

				$gpPricing[0][po_sqty] = 0;
				$gpPricing[0][po_eqty] = 99999;
				$gpPricing[0][po_cash_price] = str_replace(",","",$po_cash_price);
				$gpPricing[0][po_card_price] = str_replace(",","",$po_card_price);
				/* 단품일경우 end */
			}

			$isDirectInp = true;
		}



		/* 추출한 데이터에서 코드번호가 존재할경우 대체 */
		if(strlen($gpcode) > 3) {
			$gp_id = $gpcode;
		}

		$gp_name = str_replace("'", "\'", $gp_name);


		/* 옵션 데이터 DELETE 후 INSERT */
		sql_query("delete from $g5[g5_shop_group_purchase_option_table] where gp_id = '$gp_id'");

		$ins_sql = "";


		if($gpPricing) {
			for($i=0;$i<count($gpPricing);$i++){
				$ins_sql = "INSERT	INTO	$g5[g5_shop_group_purchase_option_table] 	SET
															gp_id = '$gp_id',
															po_num = '$i',
															po_sqty = '".$gpPricing[$i]['po_sqty']."',
															po_eqty = '".$gpPricing[$i]['po_eqty']."',
															po_cash_price = '".$gpPricing[$i]['po_cash_price']."',
															po_card_price = '".$gpPricing[$i]['po_card_price']."'
															,po_jaego		=	'$product_jaego'
				";
				sql_query($ins_sql);
			}
		}

		if(strlen($gpRow[gp_id]) > 3) {
			$편집모드 = "수정";
		} else {
			$편집모드 = "입력";
		}

		if(strlen($ins_sql) > 5 && count($gpPricing) > 1) {
			echo "<font color='green'>".$gp_id."상품의 볼륨프라이싱 데이터 ".count($gpPricing)."가지 범위 $편집모드 완료</font>";
		} else if(strlen($ins_sql) > 5 && count($gpPricing) == 1) {
			echo "<font color='blue'>".$gp_id."상품의 단품가격 데이터 $편집모드 완료</font>";
		} else {
			echo "<font color='red'>".$gp_id."상품 가격정보 $편집모드 실패 </font>";
			print_r($gpPricing);
			print_r($ins_sql);
		}
		echo "<br>";


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
										gp_explan				= '".$gp_explan."',
										gp_use		  			= '1',
										gp_order					= '".$gp_order."',
										gp_type1					= '".$gp_type1."',
										gp_type2 				= '".$gp_type2."',
										gp_type3 				= '".$gp_type3."',
										gp_type4 				= '".$gp_type4."',
										gp_type5					= '".$gp_type5."',
										gp_charge				= '".$gp_charge."',
										gp_duty					= '".$gp_duty."',
										it_type					= '".$gp_type."',

										gp_sc_price				= '5000',
										gp_update_time = '".G5_TIME_YMDHIS."'
		";

		$gpid_sql = "	SELECT	gp_id
							FROM		{$g5['g5_shop_group_purchase_table']}
							WHERE		gp_id	= '$gp_id'
		";

		$gpid = sql_fetch($gpid_sql);


		if($개발자) {
	// 		echo "$gpRow_sql<br>";
	// 		echo "$gpid_sql<br>";
	// 		echo "$gpid<br><br><br>";
		}

		if($gpid[gpid]) {
			echo "기존에 입력되있는 상품정보가 존재합니다 ".$gpid[gpid];
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
		$seq_code = '';
		$srow = '';
		unset($가격정보테이블);
		unset($옵션);
		unset($gpPricing);

	}

}

?>
<style>
.urlPd table { border-collapse: collapse; border-spacing: 0; }
.urlPd table tr th { background-color: #EEEEEE; border:1px solid #d1dee2; padding:0px; text-align:center; height:25px; }
.urlPd table tr td { padding-left:10px; border:1px solid #d1dee2; }
</style>
<form name='clayOrderForm' action='url_pd.php?mode=insert' method='post'>
<div class='urlPd'>
<table width='800' align='center'>
<tr>
	<th>딜러</th>
	<th>URL</th>
	<th>수수료</th>
	<th>관세</th>
</tr>
<tr>
	<td>APMEX</td>
	<td><input type='text' name='ap' value='' style='width:700px' /></td>
	<td><input type='text' name='ap_charge' value='' style='width:60px' /></td>
	<td><input type='text' name='ap_duty' value='' style='width:60px' /></td>
</tr>
<tr>
	<td>GAINSVILLE</td>
	<td><input type='text' name='gv' value='' style='width:700px' /></td>
	<td><input type='text' name='gv_charge' value='' style='width:60px' /></td>
	<td><input type='text' name='gv_duty' value='' style='width:60px' /></td>
</tr>
<tr>
	<td>MCM</td>
	<td><input type='text' name='mc' value='' style='width:700px' /></td>
	<td><input type='text' name='mc_charge' value='' style='width:60px' /></td>
	<td><input type='text' name='mc_duty' value='' style='width:60px' /></td>
</tr>
<tr><td colspan='2' align='center'><input type='submit' value='입력' /></td></tr>
</table>

</div>

</form>



<div>
	<?php
		include_once(G5_MSHOP_PATH . '/tail.php');
	?>
</div>