<?		
include_once('./_common.php');

header("Content-Encoding: utf-8");

/* 오래된 순서대로 */
$src_sql = "SELECT	*
						FROM		data_src
						WHERE		site = 'MCM'
						AND			number = 61
						ORDER BY mod_date ASC
						LIMIT 1
";
$src_result = sql_query($src_sql);
echo $src_sql."<br>";

while ($src = mysql_fetch_array($src_result)) {
	$gp_site = $src[url];
	$gp_site = "https://www.moderncoinmart.com/silver/australian-silver-coins/australian-kookaburra-silver";
	
	$cate1 = $src[cate1];
	$cate2 = $src[cate2];
	$maxpage = 3;	//MCM 특성상 2페이지 넘어가는게 거의 없음.
	
	for($p = 1; $p <= $maxpage; $p++) {
		$curl = curl_init();
		
		if($p == 1) {
			$site_url = $gp_site.'/?view_all=1&objects_per_page=72&sort=date_added';
		} else {
			$site_url = $gp_site.'/index'.$p.'.html?view_all=1&objects_per_page=72&sort=date_added';
		}
		
		
		
		echo $site_url."<br>";
		curl_setopt($curl, CURLOPT_URL, $site_url);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, TRUE);    //가끔가다가 인증서버에 대한 옵션이 있는데 믿을만 하다면 FALSE설정해도 됨
		// curl_setopt($curl, CURLOPT_USERPWD, "vanchosun:van1158");
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT,5);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,0);
		$Response = curl_exec($curl);
		curl_close($curl);
		
		
		if($Response){
			
			if(stristr($gp_site,"moderncoinmart.com")){
				
				/* 상품URL 추출 */
				/* '<a title="2015-W First Spouse Gold Proof Lady Bird Johnson | In Original Mint Packaging" href="/2015-w-first-spouse-gold-proof-lady-bird-johnson-in-original-mint-packaging.html" class="product-title">
				2015-W First Spouse Gold Proof Lady Bird Johnson
				</a>' */
				
				preg_match_all("/<a title\=\"[\/a-zA-Z0-9\-_\.\?\=\s\|\(\)\$]+\" href\=\"[\/a-zA-Z0-9\-_\.\?\=\s]+\" class\=\"product-title\"><span/i",$Response,$match_url); //		\"\/product\/[\/a-zA-Z0-9_\.\?\=]+\">       
				$prd_url = $match_url[0];

				
				for($i = 0; $i < count($prd_url); $i++) {
					$url = explodeCut($prd_url[$i],'href="','" class="');
					$code = explodeCut($url,'/products/','/');
					
					$sel_sql = "	SELECT	*
												FROM		data_url
												WHERE		site	= 'MCM'
												AND			url 	= '$url'
					";
					$result = sql_query($sel_sql);
					$row = mysql_fetch_array($result);
					
					if($row[number] > 0 || $row[number]) {
						$msg = "UPDATE : $url";
						
					} else {
						//		it_id = 'MCM_$code',
						$upd_sql = "	INSERT	INTO	data_url	SET
																	site	=	'MCM',
																	cate1 = '$cate1',
																	cate2 = '$cate2',
																	url	=	'$url',
																	jaego = '99999999',
																	reg_date	= now(),
																	mod_date	= '0000-00-00 00:00:00'
						";
						$msg = "INSERT : ";
						sql_query($upd_sql);
					}
					
					echo $msg.$url."<br>";					
				} // for end
				
				
// 				/* 재고는 따로 루프문 돌려서 갱신 */
// 				for($i = 0; $i < count($prd_jaego); $i++) {
// 					/* 인덱스 추출 */
// 					preg_match_all("/\([0-9]+\,/i",$prd_jaego[$i],$tmp_code);
// 					preg_match_all("/\,[0-9]+\,/i",$prd_jaego[$i],$tmp_jaego);
// 					$rs_code = $tmp_code[0];
// 					$rs_jaego = $tmp_jaego[0];
// 					$code = str_replace(',', '', $rs_code[0]);
// 					$code = str_replace('(', '', $code);
// 					$jaego = str_replace(',', '', $rs_jaego[0]);
					
// 					$upd_sql = "	UPDATE	data_url	SET
// 																	it_id = 'AP_$code',
// 																	jaego = $jaego,
// 																	mod_date	= now()
// 												WHERE		url		LIKE	'%/$code/%'
// 					";
// 					echo $upd_sql."<br>";
// 					sql_query($upd_sql);
// 				}

			} //if end
			
		} //if($Response) end
		
	} //for( $p )end	페이지별로 request
	
	$upd_sql = "	UPDATE	data_src	SET
													mod_date = now()
								WHERE		number = $src[number]
	";
	sql_query($upd_sql);
	
}//while

?>


<!-- !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! -->


<?		
include_once('./_common.php');

header("Content-Encoding: utf-8");


$list_sql = "			SELECT	DU.number,
											DU.url,
											DU.mod_date,
											DS.charge,
											DS.duty
							FROM		data_url DU
											LEFT JOIN data_src DS ON (DS.cate1 = DU.cate1 AND DS.cate2 = DU.cate2)
							WHERE		DU.site = 'MCM'
							AND			DU.cate1 IN ('".$cate1."')
							AND			DU.cate2 IN ('".$cate2."')
							ORDER BY DU.mod_date ASC
							LIMIT  100
";
$result = sql_query($list_sql);


$업데이트대상목록 = '';

while($row = mysql_fetch_array($result)) {
	echo $row[url]."<br>";
	$gp_site = ($row[url]) ? "https://www.moderncoinmart.com".$row[url] : '';
	
	
	$업데이트대상목록 .= "$row[number],";
 
	$gp_charge = $row[charge]; 
	$gp_duty = $row[duty];
	
	if(!$gp_site) {
		$fail_count++;
		$fail_it_id.="$gp_site";
		continue;
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
			continue;
		}				
		
		
		echo "######### ".$백업_카테고리." #########<br>";
		for($i=1; $i < count($cate_tmp)-1; $i++) {
			
			$cate_tmp[$i] = trim(strip_tags($cate_tmp[$i]));
			
			$시작 = $i * 2 + 1;
			$끝 = $i * 2 + 2;
			
			$ca_name = $cate_tmp[$i];
			
			$연결카테고리조건 = ($연결카테고리) ? " AND	ca_id LIKE '".$연결카테고리."%' " : '';
			
			$sel_sql = "		SELECT	*
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
				if(!$연결카테고리) $연결카테고리 = 'MC';
				
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
				
				$연결카테고리 .=  strtoupper($v_16진수);
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
				echo "패스 존재할경우 기존 코드로 대체 - $연결카테고리 <br>";
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
		preg_match_all("/\'(https:\/\/www.moderncoinmart.com\/images\/D\/[A-Za-z0-9\.\/\-\s\%\_]+)/i",$Response,$match_img); //
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
		
		
		echo "VOLUME PRICING : ";
		print_r($match_wcmtable);
		echo "<br><br>";
		
		
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
	echo "<br><br><br>";
	
	$upd_sql = "	UPDATE	data_url	SET
													it_id	=	'$gp_id',
													jaego = '$product_jaego',
													mod_date	= now()
								WHERE		number = '$row[number]'
	";
	sql_query($upd_sql);
	
	$연결카테고리 = '';
	$seq_code = '';
	$srow = '';	
	unset($가격정보테이블);
	unset($옵션);
	unset($gpPricing);
}

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
