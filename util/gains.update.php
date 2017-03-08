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
							WHERE		DU.site = 'GAINESVILLE'
							#AND			DU.it_id = 'GV_170135'
							ORDER BY DU.mod_date ASC
							LIMIT  60
";

$result = sql_query($list_sql);


$업데이트대상목록 = '';

while($row = mysql_fetch_array($result)) {
	if($mode == 'jhw') echo $row[url]."<br>";
	$gp_site = ($row[url]) ? "http://www.gainesvillecoins.com".$row[url] : '';
	$gpcode = 'GV_'.explodeCut($gp_site, '/products/', '/');
	$gain_code = explodeCut($gp_site,'/products/','/');

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
	}


	/* 재고파악을 위한 카트페이지 결과 */
 	preg_match_all("/\/([0-9]+)\//",$row[url],$temp);
	$pid = $temp[1][0];
	$jaego_url = "http://www.gainesvillecoins.com/products/addtocart?pid=$pid&qty=99999&bw=false";
	$Response = get_httpRequest($jaego_url,"http://www.gainesvillecoins.com");

	//재고추출
	preg_match_all("/The max allowed quantity of [<\/strong>]*([0-9]+)[<\/strong>]* for/",$Response,$temp);
	$jaego = $temp[1][0];




	/* 상품상세페이지 결과 */
	$Response = get_httpRequest($gp_site,"http://www.gainesvillecoins.com");
// 	print_r($Response);

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
			if($mode == 'jhw') echo "카테고리 : ".$cate_export[$i]."<br>";

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

			if($mode == 'jhw') echo $sel_sql."<br>"."$gpcode /  $ca_name / $연결카테고리 / $i 번째<br>";

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
				if($mode == 'jhw') echo $seq_sql."<br><br>";

				list($seq_code) = mysql_fetch_array(sql_query($seq_sql));

				$v_10진수 = hexdec($seq_code) + 1;
				$v_16진수 = dechex($v_10진수);

				if($mode == 'jhw') echo "10진수( $v_10진수 ) ,  16진수( $v_16진수 ) , ";

				$v_16진수 = ($v_10진수 < 16) ? '0'.$v_16진수 : $v_16진수;

				if($mode == 'jhw') echo " 변환16진수( $v_16진수 ) <br><br>";

				$연결카테고리 .= strtoupper($v_16진수);
				if($mode == 'jhw') echo "새로생성 - $연결카테고리 <br>";


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

				if($mode == 'jhw') echo "패스 존재할경우 기존 코드로 대체 - $연결카테고리 <br>";
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
		.pdSpecs table {
		  width: 80%;
		}
		.pdSpecs table tr:nth-child(even) {
		  background: #f1f1f1;
		}
		.pdSpecs table tr td:first-child {
		  color: #777;
		}
		.pdSpecs table tr td {
		  font-size: .96em;
		  padding: 8px;
		}
		</style>";
		$상세설명 = explodeCut($Response,'<div class="text">','</div>');
		$gp_explan = "$스펙스타일<center>".$img_tag."<div class='pdSpecs'>".$스펙정보."</div></center>".$상세설명;
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
			echo "단품/";

			/* 단품일경우 start */
			//  상품 Price <span id="product_price">199.00</span>
			//	 상품 Wire/Check Price  <span class="currency">$193.13</span>
			$gpPricing[0][po_sqty] = 0;
			$gpPricing[0][po_eqty] = 99999;
			$gpPricing[0][po_cash_price] = str_replace(",","",$match_price[0]);
			$gpPricing[0][po_card_price] = str_replace(",","",$match_price[1]);
			/* 단품일경우 end */
		}
		else if(stripos($Response,"Out Of Stock")) {
			echo "품절";
		}
		else {
			echo "볼륨/";

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

				//첫번째는 현금가, 두번째 값은 카드가
				if($z%2 == 0) {
					$옵션[$optcnt][po_cash_price] = str_replace(',','',$match_price[$z]);
				} else {
					$옵션[$optcnt][po_card_price] = str_replace(',','',$match_price[$z]);
				}

				//현금가 입력후 카드가 입력후 다시 현금가 입력할때 옵션인덱스 증가
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
														gp_id					= '$gp_id',
														po_num				= '$i',
														po_sqty				= '".$gpPricing[$i]['po_sqty']."',
														po_eqty 			= '".$gpPricing[$i]['po_eqty']."',
														po_cash_price = '".$gpPricing[$i]['po_cash_price']."',
														po_card_price = '".$gpPricing[$i]['po_card_price']."'
														,po_jaego			=	'$jaego'
			";
			sql_query($ins_sql);
		}
	}

	if(strlen($gpRow[gp_id]) > 3) {
		$편집모드 = "수정";
	} else {
		$편집모드 = "입력";
	}


	$조건 = (!stripos($Response,"Out Of Stock")) && (strlen($gpPricing[0] > 0));

	/* 상품가격 변동성 기록 */
	if( $조건 ) {
		flowProductPriceSave($gp_id,$gpPricing,$jaego);
	}

	/* 금/은 여부 판단 */
	$gp_metal_type = distinctGoldSilver($연결카테고리,$gp_name,$gp_explan);
	if($mode == 'jhw') echo ", 금은구분 : ".$gp_metal_type."<br>";

	/* 무게함량 추출 및 계산 */
	$raw_spec =	strip_tags(str_replace(PHP_EOL,"",nl2br($스펙정보)));
	$tempspec = str_replace(" ","",$raw_spec);
	preg_match_all("/ActualMetalWeight:([0-9]+\.*[0-9]*)ozt/i",$tempspec,$match_spec);
	$gp_metal_don = $match_spec[1][0];
	echo "oz : $gp_metal_don ";



	if(strlen($ins_sql) > 5 && count($gpPricing) > 1) {
		echo "<br><font color='green'>".$gp_id."상품($jaego ea)의 볼륨프라이싱 데이터 ".count($gpPricing)."가지 범위 $편집모드 완료</font><br>";
	} else if(strlen($ins_sql) > 5 && count($gpPricing) == 1) {
		echo "<br><font color='blue'>".$gp_id."상품($jaego ea)의 단품가격 데이터 $편집모드 완료</font><br>";
	} else {
		echo "<br><font color='red'>".$gp_id."상품 가격정보 $편집모드 실패 </font><br>";
		if($mode == 'jhw') print_r($gpPricing);
		if($mode == 'jhw') print_r($ins_sql);
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
									jaego						=	'$jaego',
									gp_objective_price	= '0',
									gp_metal_type		= '$gp_metal_type',
									gp_metal_don		= '$gp_metal_don',
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

	if($mode == 'jhw') echo $sql."<br />";
	//exit;
	sql_query($sql);
	db_log($sql,'g5_shop_group_purchase',"딜러업체상품 입력 또는 갱신 ");

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
