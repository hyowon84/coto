<?
include_once(G5_LIB_PATH.'/Snoopy/Snoopy.class.php');    // 공통 라이브러리

$정규패턴['APMEX']['상품명'] = "/<h1 class\=\"product-title\" [a-z0-9\-\'\"\=:;\s]+\s*>\s*\t*(.*)\s*\t*<\/h1>/";
$정규패턴['APMEX']['재고수량'] = "/ShoppingCart.notifyAllowedMaxQty\([0-9]+\,.+\,[0-9]+\,/i";
$정규패턴['APMEX']['상품IMG'] = "/http:\/\/www.images-apmex.com\/images\/Catalog Images\/Products\/[\/a-zA-Z0-9_\.\?\=]+\&amp;width\=450\&amp;height\=450/i";
$정규패턴['APMEX']['상품URL'] = "/<div class\=\"product-item-title\"><a href\=\"\/product\/[\/a-zA-Z0-9\-_\.\?\=]+\">/i";


/* 로그를 텍스트파일로 남기기 */
function echoLog($title,$msg)
{
	///home/sbridge/renewal/pg_module/kcp
	$path = "./log/";
	$file = $title."_".date("Ymd").".log";
	$msg_head = "[".date("Y-m-d")."_".date("H:i:s")."][P=".getmypid()."]";

	if(!is_dir($path))
	{
		mkdir($path, 0755);
	}

	$msg = $msg.PHP_EOL;

	if(!($fp = fopen($path.$file, "a+"))) return 0;

	fwrite($fp, $msg_head);

	if(!empty($cmt) || !empty($line))
	{
		$cmt = basename($cmt);
		fwrite($fp, "[$cmt($line)]");
	}

	ob_start();
	print_r($msg);

	$ob_msg = ob_get_contents();
	ob_clean();

	if(fwrite($fp, " ".$ob_msg."\n") === FALSE)
	{
		fclose($fp);
		return 0;
	}

	fclose($fp);
	return 1;
}


/* snoopy를 이용한 웹페이지 긁어오기 */
function get_httpRequest($긁어올URL, $proxy_ip='', $port='') {
	$snoopy = new Snoopy;

	if($proxy_ip && $port) {
		$snoopy->proxy_host = $proxy_ip;
		$snoopy->proxy_port = $port;
	}

	$snoopy->fetch($긁어올URL);

	return $snoopy->results;
}

// 외부파일 불러오기
function curl($url,$proxy_ip = '')
{
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_HTTPPROXYTUNNEL, 0);

	if(strlen($proxy_ip) > 9) curl_setopt($curl, CURLOPT_PROXY, $proxy_ip);

	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST,'GET');
	curl_setopt($curl, CURLOPT_HEADER, 1);
	curl_setopt($curl, CURLOPT_REFERER, "http://apmex.com");
	$Response = curl_exec($curl);
	curl_close($curl);

	return $Response;
}


/* curl을 이용한 응답결과 리턴 */
function get_curlResult($긁어올URL,$재시도URL) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $긁어올URL);
	curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, TRUE);	//가끔가다가 인증서버에 대한 옵션이 있는데 믿을만 하다면 FALSE설정해도 됨
	// curl_setopt($curl, CURLOPT_USERPWD, "vanchosun:van1158");
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT,5);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,0);
	$Response = curl_exec($curl);
	curl_close($curl);


	/* APMEX 포워딩된 URL일경우에만 해당 */
	preg_match_all("/Object moved to/i",$Response,$moved); //
	if($moved[0][0] == 'Object moved to') {
		preg_match_all("/\/product\/[\/a-zA-Z0-9_\.\?\=\-]+/i",$Response,$forward); //
		$gp_site = "$재시도URL".$forward[0][0];
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

	return $Response;
}


/* 가격정보가 있는경우에만 가격변동사항 기록 */
// 필요한 변수 $gp_id, $gpPricing
function flowProductPriceSave($gp_id,$볼륨가격표,$jaego = 0) {

	$vol_cnt = count($볼륨가격표);
	if($vol_cnt >= 1) {

		/* 오늘 이전 가격정보 */
		$flow_sql = "	SELECT	*
									FROM		flow_product
									WHERE		gp_id = '$gp_id'
									AND			ymd < DATE_FORMAT(now(),'%Y-%m-%d')
									ORDER BY ymd DESC
									LIMIT 1
		";
		$flow_result = sql_query($flow_sql);
		$이전데이터 = mysql_fetch_array($flow_result);

		$vol_idx = $vol_cnt-1;

		/* URL 긁어온 가격 */
		$최대가격 = $볼륨가격표[0]['po_cash_price'];
		$최소가격 = $볼륨가격표[$vol_idx]['po_cash_price'];

		/* 존재할경우 가격차이 확인 */
		if($이전데이터)
		{
			/* 가격차이 절대값 구하기 */
			$최대가격차이 = ($최대가격 - $이전데이터[max_price]) * 1;
			$최소가격차이 = ($최소가격 - $이전데이터[min_price]) * 1;


			/* 오늘 기록한 가격정보 */
			$todayflow_sql = "	SELECT	*
													FROM		flow_product
													WHERE		gp_id = '$gp_id'
													AND			ymd = DATE_FORMAT(now(),'%Y-%m-%d')
													ORDER BY ymd DESC
													LIMIT 1
			";
			$today_result = sql_query($todayflow_sql);
			$오늘기록여부 = mysql_num_rows($today_result);
			$가격변동여부 = ( abs($최대가격차이) > 0 || abs($최소가격차이) > 0);
			$재고갭 = $jaego - $이전데이터[jaego];		//이전데이터 기준 - 오늘의 재고를 빼야 갭


			/* 오늘 수집한 가격이 갑자기 변동될경우 UPDATE */
			if( $오늘기록여부 > 0 )
			{
				$upd_sql = "UPDATE	flow_product	SET
																min_price	=	'$최소가격',			/* 최소가격 */
																min_gap		= '$최소가격차이',	/* 전 최소가격과의 시세차이 */
																max_price	=	'$최대가격',			/* 최대가격 */
																max_gap		= '$최대가격차이',	/* 전 최대가격과의 시세차이 */
																jaego			= '$jaego',
																jaego_gap = '$재고갭',
																reg_date	=	now()							/*기준 날짜*/
										WHERE		gp_id 				=	'$gp_id'
										AND			ymd 					=	DATE_FORMAT(now(),'%Y-%m-%d')
				";
				sql_query($upd_sql);
			}
			/* 이전데이터와 지금수집한 데이터가 가격차이가 나는경우 기록 */
			else //if( !$오늘기록여부 )
			{
				$ins_sql = "INSERT	INTO	flow_product	SET
																			gp_id 		=	'$gp_id',
																			ymd 			=	DATE_FORMAT(now(),'%Y-%m-%d'),
																			min_price	=	'$최소가격',			/* 최소가격 */
																			min_gap 	= '$최소가격차이',	/* 전 최소가격과의 시세차이 */
																			max_price	=	'$최대가격',			/* 최대가격 */
																			max_gap 	= '$최대가격차이',	/* 전 최대가격과의 시세차이 */
																			jaego			= '$jaego',
																			jaego_gap = '$재고갭',
																			reg_date	=	now()							/*기준 날짜*/
				";
				sql_query($ins_sql);
			}

		}
		/* 없으면 새로 입력 */
		else {

			/* 오늘 기록한 가격정보 */
			$todayflow_sql = "	SELECT	*
													FROM		flow_product
													WHERE		gp_id = '$gp_id'
													AND			ymd = DATE_FORMAT(now(),'%Y-%m-%d')
													ORDER BY ymd DESC
													LIMIT 1
			";
			$today_result = sql_query($todayflow_sql);
			$오늘기록여부 = mysql_num_rows($today_result);

			if($오늘기록여부 <= 0) {
				/* 새로입력시 이전데이터가 없기 때문에 가격차이는 0원 */
				$최소가격차이 = 0; 	$최대가격차이 = 0;

				$ins_sql = "INSERT	INTO	flow_product	SET
																			gp_id 		=	'$gp_id',
																			ymd 			=	DATE_FORMAT(now(),'%Y-%m-%d'),
																			min_price	=	'$최소가격',			/* 최소가격 */
																			min_gap 	= '$최소가격차이',	/* 전 최소가격과의 시세차이 */
																			max_price	=	'$최대가격',			/* 최대가격 */
																			max_gap 	= '$최대가격차이',	/* 전 최대가격과의 시세차이 */
																			jaego			= '$jaego',
																			jaego_gap = '0',
																			reg_date	=	now()							/*기준 날짜*/
				";
				sql_query($ins_sql);

			}
		}

	}//vol cnt

}



/* 상품URL 데이터 추출시 금/은 구분 함수 */
function distinctGoldSilver($연결카테고리,$상품명,$본문내용) {

	$TC_sql = "	SELECT	CONCAT(A.ca_name,',',B.ca_name,',',C.ca_name,',',D.ca_name) AS TOTAL_NAME,
											A.*
							FROM		g5_shop_category A
											LEFT JOIN g5_shop_category B ON (B.ca_id = SUBSTR(A.ca_id,1,4))
											LEFT JOIN g5_shop_category C ON (C.ca_id = SUBSTR(A.ca_id,1,6))
											LEFT JOIN g5_shop_category D ON (D.ca_id = SUBSTR(A.ca_id,1,8))
							WHERE		A.ca_id = '$연결카테고리'
	";
	$TC = mysql_fetch_array(sql_query($TC_sql));

	preg_match_all("/(GOLD|Gold|gold)+/i",$TC[TOTAL_NAME].$상품명,$TEMP1);
	preg_match_all("/(SILVER|Silver|silver)+/i",$TC[TOTAL_NAME].$상품명,$TEMP2);

	$금단어개수 = COUNT($TEMP1[0]);
	$은단어개수 = COUNT($TEMP2[0]);

	//하나만 들어간 경우, 둘다 들어간경우

	if($금단어개수 > 0 && !$은단어개수) {
		$금은구분 = 'GL';
	}
	else if(!$금단어개수 && $은단어개수 > 0) {
		$금은구분 = 'SL';
	}
	else if($금단어개수 > 0 && $은단어개수 > 0) {
		$금은구분 = '';	//둘다 들어간건 패스
	}
	else {
		$금은구분 = '';
	}


	return $금은구분;
}



/* URL상품정보 추출시 사용하는 커팅함수 */
function explodeCut($문자열,$시작,$끝) {
	$ex = explode($시작,$문자열);
	$ex2 = explode($끝,$ex[1]);
	return $ex2[0];
}

/* SMS문자 전송 (주문정보배열변수, 보낼메시지) */
function send_sms($od, $mh_send_message) {
	global $config,$member;

	/* 상품준비중 or 배송완료 일때만 SMS문자 전송됨 */
	$SMS = new SMS5;
	$SMS->SMS_con($config['cf_icode_server_ip'], $config['cf_icode_id'], $config['cf_icode_pw'], $config['cf_icode_server_port']);

	unset($mh_hp);
	$hphone = get_hp($od[od_hp], 0);

	$mh_hp[]['bk_hp'] = $hphone;	//수신자번호
	$mh_reply = preg_replace('/-/','',$member['mb_hp']); //발신자번호

	$result = $SMS->Add($mh_hp, $mh_reply, '', '', $mh_send_message, $booking, 1);
	$result = $SMS->Send();
	$SMS->Init(); // 보관하고 있던 결과값을 지웁니다.

	/* 로그기록 */
	$ins_sql = "INSERT	INTO 	sms5_write		SET
															wr_renum = '$wr_renum',
															od_id			=	'$od[od_id]',			/* 관련 주문번호 */
															wr_reply = '$mh_reply',				/*보내는사람번호*/
															wr_target = '$od[od_hp]',			/*받는사람번호*/
															wr_message = '$mh_send_message',	/*메시지내용*/
															wr_datetime = now(),					/*보낸날짜*/
															wr_booking = '$wr_booking',	/*예약날짜*/
															wr_total = '1',
															wr_re_total = '$wr_re_total',
															wr_success = '1',
															wr_failure = '$wr_failure',
															wr_memo = '$wr_memo'
	";
	sql_query($ins_sql);
}



/* function flowProductPrice()
 * 금/은 상품시세 함수
 * 금속유형 : GL, SL
 * 정렬기준값 : min_price(최소가 가격), max_price(최대가 가격), min_gap(최소가 갭), max_gap(최대가 갭), min_gap_per(최소가 등락률), max_gap_per(최대가 등락률)
 * 등락유형 : +UP(상승), -DOWN(하락)
 * 정렬유형 : BIG(큰값부터), SMALL(작은값부터)
 * 출력갯수 : 기본 50개 DEFAULT
 * 검색날짜 : 0 DAY(오늘), -1 DAY(하루전), -1 WEEK(1주일전), -1 MONTH(한달전)
 */
function flowProductPrice($금속유형 = 'SL', $정렬기준값 = 'min_gap_per', $등락유형 = 'DOWN', $정렬유형 = 'BIG', $출력갯수 = 50, $검색날짜 = '0 DAY', $비교할날짜 = null) {
	global $is_admin;

	if($검색날짜) {
		$검색날짜조건 = " AND			FP.ymd = '$검색날짜' ";
		if(!$비교할날짜) {
			$비교할날짜 = date("Y-m-d",strtotime("-1 day"));
		}
// 		$비교검색날짜조건 = " AND			FP.ymd = DATE_FORMAT(DATE_ADD('$비교할날짜',INTERVAL -1 WEEK),'%Y-%m-%d') ";
	}
	else {
		/* 값이 없으면 오늘데이터 검색 */
		$검색날짜조건 = " AND			FP.ymd = DATE_FORMAT(now(),'%Y-%m-%d') ";
	}

	$alias = "FP.";

	$등락조건 = "AND	".$alias.$정렬기준값.(($등락유형 == 'DOWN') ? " < 0 " : " > 0 ");

	# 양수에서는 DESC가 내림차순, ASC가 오름차순
	if($등락유형 == 'UP') {
		$정렬쿼리 = "ORDER BY ".$alias.$정렬기준값.(($정렬유형 == 'BIG') ? " DESC" : " ASC");
	}
	# 음수에서는  ASC가 내림차순,  DESC가 오름차순
	if($등락유형 == 'DOWN') {
		$정렬쿼리 = "ORDER BY ".$alias.$정렬기준값.(($정렬유형 == 'BIG') ? " ASC" : " DESC");
	}

	$갯수제한조건 = ($출력갯수 > 0) ? "LIMIT $출력갯수 " : "";

	$FLOW_DATA_SQL = "	SELECT	FP.*
											FROM		(
																	SELECT	FP.gp_metal_type,																		/*GL, SL, PT, PD*/
																					FP.gp_metal_don,																		/*OZ*/
																					FP.gp_name,
																					FP.gp_site,																					/*상품 원본URL*/
																					FP.gp_img,
																					FP.gp_id,
																					FP.ymd,
																					FP.min_price,																				/*최소가격*/
																					(FP.min_price - PRFP.min_price) AS min_gap,					/*최소가격 갭*/
																					((FP.min_price - PRFP.min_price) / PRFP.min_price * 100) AS min_gap_per,
																					FP.max_price,																				/*최대가격*/
																					(FP.max_price - PRFP.max_price) AS max_gap,					/*최대가격 갭*/
																					((FP.max_price - PRFP.max_price) / PRFP.max_price * 100) AS max_gap_per,
																					FP.reg_date,																				/*기준 날짜*/
																					GP.jaego																						/*업체재고*/
																	FROM		v_flow_product FP
																					LEFT JOIN g5_shop_group_purchase GP ON (GP.gp_id = FP.gp_id)
																					LEFT JOIN (	SELECT	*
																											FROM		flow_product
																											WHERE		ymd = '$비교할날짜'
																					) PRFP ON (PRFP.gp_id = FP.gp_id)
																	WHERE		1=1
																	AND			(	FP.gp_name LIKE '%GOLD%' OR FP.gp_name LIKE '%SILVER%' OR FP.gp_name LIKE '%OZ%' )	/* 금 OR 은 */
																	AND			(	FP.gp_name LIKE '%BULLION%' OR FP.gp_name LIKE '%BU%' OR FP.gp_name LIKE '%COIN%' OR FP.gp_name LIKE '%PCGS%' OR FP.gp_name LIKE '%NGC%' OR FP.gp_name LIKE '%ROUND%'  OR FP.gp_name LIKE '%PROOF%' )
																	AND			FP.gp_name NOT LIKE '%GOLD%BAR%'
																	AND			FP.gp_name NOT LIKE '%SILVER%BAR%'
																	AND			(FP.gp_name NOT LIKE '%BOX%' AND FP.gp_name NOT LIKE '%figur%' AND FP.gp_name NOT LIKE '%BAG%' AND FP.gp_metal_don > 0)
																	$검색날짜조건
											) AS FP
											WHERE		1=1
											AND			FP.gp_metal_type = '$금속유형'
											#AND			ABS(FP.min_gap_per) >= 1
											$등락조건
											$정렬쿼리
											$갯수제한조건
	";
	if($_GET[mode] == 'jhw') echo $FLOW_DATA_SQL;

	$FLOW_DATA_RESULT = sql_query($FLOW_DATA_SQL);
	$gl_cnt = 0;
	$gl_maxcnt = mysql_num_rows($FLOW_DATA_RESULT);


	if($is_admin) {
		$관리자컬럼제목 = "<th width='60' align='center'>재고<br>(EA)</th>";
	}

	echo "<table>
				<tr>
				<th width='40'>■</th>
				<th>상품명</th>
				<th width='35'>종류</th>
				<th width='60' align='center'>무게<br>(oz)</th>
				<th width='80' align='center'>최소가격($)</th>
				<th width='50' align='center'>최소<br>등락%</th>
				<th width='80' align='center'>최대가격($)</th>
				<th width='50' align='center'>최대<br>등락%</th>
				$관리자컬럼제목
				</tr>
	";

	while($GF = mysql_fetch_array($FLOW_DATA_RESULT)) {
		if($is_admin) {
			$관리자컬럼값 = "<td width='60' align='center'>".$GF[jaego]."</td>";
		}

		$icon_up = "<img src='/img/coin_up.png' />";
		$icon_down = "<img src='/img/coin_down.png' />";

		$최소등락률 = number_format($GF[min_gap_per], 2);
		$최대등락률 = number_format($GF[max_gap_per], 2);

		$무게 = number_format($GF[gp_metal_don], 2);
		$GF[min_gap]  =  number_format($GF[min_gap], 2, '.', '');
		$GF[max_gap]  =  number_format($GF[max_gap], 2, '.', '');

		if($GF[min_gap] > 0) $최소갭 = "$icon_up ($GF[min_gap])";
		else if($GF[min_gap] < 0) $최소갭 = "$icon_down ($GF[min_gap])";
		else $최소갭 = "$GF[min_gap]";

		if($GF[max_gap] > 0) $최대갭 = "$icon_up ($GF[max_gap])";
		else if($GF[max_gap] < 0) $최대갭 = "$icon_down ($GF[max_gap])";
		else $최대갭 = "$GF[min_gap]";

		?>
		<tr>
			<td align='center'><img src='<?=$GF[gp_img]?>' width='30' height='30' /></td>
			<td><a href='<?=$GF['gp_site']?>' target='_blank' style='text-decoration: none;'>[<?=$GF[gp_id]?>] <?=$GF[gp_name]?></a></td>
			<td align='center'><?=$GF[gp_metal_type]?></td>
			<td align='center'><?=$무게?></td>
			<td align='center'>$<?=$GF[min_price]?><br><?=$최소갭?></td>
			<td align='center'><?=$최소등락률?>%</td>
			<td align='center'>$<?=$GF[max_price]?><br><?=$최대갭?></td>
			<td align='center'><?=$최대등락률?>%</td>
			<?=$관리자컬럼값?>
		</tr>
		<?
	}
	echo "</table>";
}//function flowProductPrice() end



/* 주문번호 2,4,4,4*/
// 0,2  3,6   10,14
function reform_substr($문자열,$분할단위배열,$사용할특수문자) {

	for($i = 0; $i < count($분할단위배열); $i++) {
		$시작 = ($끝) ? ($시작+$끝) : 0;
		$끝 = $분할단위배열[$i];
		$문자열조합 .= substr($문자열,$시작,$끝).$사용할특수문자;
	}

	$문자열조합 = substr($문자열조합,0,strlen($문자열조합)-1);

	return $문자열조합;
}

function 공백문자삭제($텍스트) {
	$결과 = preg_replace('/\t|\s/','',$텍스트);
	return $결과;
}

function 개행문자삭제($텍스트) {
	$결과 = preg_replace('/\r\n|\r|\n/','',$텍스트);
	return $결과;
}


// PHP 5.4 미만의 경우 json_encode 의 한글이 유니코드값으로 변경되는 현상을 해결하기 위한 대체 함수
function json_encode_unicode($data) {
	switch (gettype($data)) {
		case 'boolean':
			return $data?'true':'false';
		case 'integer':
		case 'double':
			return $data;
		case 'string':
			return '"'.strtr($data, array('\\'=>'\\\\','"'=>'\\"')).'"';
		case 'array':
			$rel = false; // relative array?
			$key = array_keys($data);
			foreach ($key as $v) {
				if (!is_int($v)) {
					$rel = true;
					break;
				}
			}

			$arr = array();
			foreach ($data as $k=>$v) {
				$arr[] = ($rel?'"'.strtr($k, array('\\'=>'\\\\','"'=>'\\"')).'":':'').json_encode_unicode($v);
			}

			return $rel?'{'.join(',', $arr).'}':'['.join(',', $arr).']';
		default:
			return '""';
	}
}


function getCotoCategory(){
	/*판매가능 카테고리만 로드*/
	$sql = "SELECT	*	FROM	g5_shop_category
 					WHERE	ca_id LIKE 'CT%'
 					AND		LENGTH(ca_id) >= 4
 					AND			ca_id NOT IN ('CTIP')
 					AND		ca_use = 1
 					ORDER BY ca_order ASC
 	";
	$result = sql_query($sql);

	while($ca = mysql_fetch_array($result)) {
		echo "<li><a href='/shop/gplist.php?ca_id=".$ca[ca_id]."'>".$ca[ca_name]."</a></li>";
	}
}

function jsonDecode($DATA) {
	/*
	 * JSON DECODE 관련 이슈
	 * 1. 상품명에 " OR ' 가 포함 되있는경우 디코딩 실패 str_replace로 변환필요
	 * 2. 넘겨받은 JSON텍스트 ICONV로 변환필요
	 * 3. STRIPSLASH
	 * */
	$JSON소스 = stripslashes( str_replace('\"','"', iconv('utf-8', 'euc-kr',  $DATA) ) );
	return json_decode($JSON소스,true);
}


/* PC웹/모바일 메인화면 상단 이미지 슬라이드 */
function makeMainSlideImage() {
	
	$slide_sql = "select * from g5_main_slide where status='2' order by no desc LIMIT 10";
	$main_slide_res = sql_query($slide_sql);
	$main_slide_num = mysql_num_rows($main_slide_res);

	//모바일 슬라이드
	if(G5_IS_MOBILE) {
		
		echo "
		<div class='flexslider'>
			<ul class='slides'>";
		
		for ($i = 0; $i < $main_slide_num; $i++) {
			$main_slide_row = mysql_fetch_array($main_slide_res);
			$img_url = "/image.php?image=/data/main_img/$main_slide_row[img_file]";

			if($main_slide_row[URL]) {
				$링크 = "<a href=\"$main_slide_row[URL]\"><img class='slick-slide-img' src=\"$img_url\"></a>";
			} else {
				$링크 = "<img class='slick-slide-img' src=\"$img_url\" >";
			}
			?>
			<li class="slick-slide-img"><?=$링크?></li>
			<?
		}
		
		echo "</ul>
		</div>";
	}
	//PC 슬라이드
	else {
		echo "<section class=\"slide\" style='display:none;'>";
		for ($i = 0; $main_slide_row = mysql_fetch_array($main_slide_res); $i++) {

			$URL = ($main_slide_row[URL]) ? $main_slide_row[URL] : '#';

			if ($main_slide_row[status] == "2") {

				$img_url = "/image.php?image=/data/main_img/$main_slide_row[img_file]";

				if (G5_IS_MOBILE) {
					$size = " height='120' ";
				} else {
					$size = " height='550' ";
				}


				?>
				<div><a href="<?= $URL ?>"><img class="slick-slide-img" src="<?= $img_url ?>" <?= $size ?> /></a></div>
				<?
			}
		}
		echo "</section>";
	}	
	
}

$나누기값 = (G5_IS_MOBILE) ? 2 : 4;

/* 진행중인 공동구매의 상품목록 메인화면 노출HTML코드 */
function makeHtmlGpPrdList() {
	global $상품노출개수, $is_admin;
	
	$height = (G5_IS_MOBILE) ? '140px' : '230px';
	$margin = (G5_IS_MOBILE) ? '5px' : '15px';
	
	/* 진행중인 공동구매 정보 가져오기*/
	$gp_sql = "	SELECT	*
							FROM		gp_info	GI
							WHERE		1=1
							AND			GI.start_date <= NOW()
							AND			GI.end_date >= NOW()
							AND			GI.stats = '00'
							AND			GI.menu_view = 'Y'
	";

	$gp_result = sql_query($gp_sql);
	$gpcnt = mysql_num_rows($gp_result);

	/*공동구매 목록*/
	while($gp = mysql_fetch_array($gp_result)) {
		$sql_product = makeProductSql($gp[gpcode]);

		echo "<div class='prdlist_title cut_text1line'>
						<a href='/shop/list.php?gpcode=$gp[gpcode]&ca_id=GP'>
							[ ~ $gp[end_date]까지 공동구매] $gp[menu_name]
							<font color=red>&nbsp;&nbsp;&nbsp;☜ 전체보기</font>
							<br>
						</a>
					</div>";



		/* 공동구매에 설정된 상품정보 가져오기 */
		$links = explodeMakeCode(',', $gp[links]);
		$replaced_sql_product = str_replace('#상품기본조건#', " AND	gp_id IN ($links) ", $sql_product);

		$prd_sql = "	SELECT	T.*,
													IF(T.real_jaego > 0,T.real_jaego,0) AS real_jaego,
													CASE
														WHEN	T.ca_id LIKE 'CT%' || T.ca_id = 'GP'	THEN
															CASE
																WHEN	T.gp_price_type = 'Y'	THEN	/*실시간스팟시세*/
																	CEIL(T.gp_realprice / 100) * 100
																WHEN	T.gp_price_type = 'N'	THEN	/*고정가달러환율*/
																	CEIL(T.gp_fixprice / 100) * 100
																WHEN	T.gp_price_type = 'W'	THEN	/*원화적용*/
																	T.gp_price
																ELSE
																	0
															END
														ELSE
															CEIL(IFNULL(T.po_cash_price,T.gp_price) / 100) * 100
													END cash_price
									FROM		$replaced_sql_product
									WHERE		T.real_jaego > 0
									AND			IF( (T.gp_realprice + T.gp_fixprice + T.gp_price) > 0,TRUE,FALSE)
									ORDER BY T.gp_update_time DESC
									LIMIT 16
		";
		$it_result = sql_query($prd_sql);
		if($_GET[mode] == 'jhw') echo "<textarea>$prd_sql</textarea>";

		$item_cnt = 0;
		$item_size = mysql_num_rows($it_result);
		
		
		if(G5_IS_MOBILE) {
			echo "<div class='flexslider'>
							<ul class='slides'>
								<li class='slide_contain'>
			";
		}
		else {
			echo "<section class='slide' style='display:none;'>
							<div class='slide_contain'>";
		}
		
		while($it = mysql_fetch_array($it_result)) {
			
			$it[card_price] = ceil($it[cash_price] * 1.03 / 100) * 100;
			$jaego = ($it[real_jaego] > 0) ? $it[real_jaego] : 0;
			$카드가 = ($it[gp_card] == '사용안함') ? "카드가 : ".number_format($it[card_price])."원<br>" : "";

			$imgthumb = getThumb($it);
			$it[gp_img] = $imgthumb[src];
			
			if($is_admin == 'super') {
				$재고 = " 남은수량 : ".number_format($it[real_jaego])." ea ";
			}
			
			echo "
					<div class='prdlist_item'>
						<a href='/shop/grouppurchase.php?gpcode=$gp[gpcode]&gp_id=$it[gp_id]&ca_id=$it[ca_id]'>
						<span class='sct_icon' style='position:absolute;'>".item_icon1($it)."</span>
						<div class='imgLiquidNoFill imgLiquid' style='width:98%; height:$height; margin:$margin auto;'>
							<img src='$it[gp_img]' />
						</div>
						<div class='prdlist_itemname cut_text'>$it[gp_name]</div>
						</a>
						<div class='prdlist_btn'>
							<input type=\"text\" id=\"".$it[gp_id]."_qty\" name=\".$it[gp_id].\" class=\"frm_input\" value=\"1\" style=\"width:35px; height:38px; text-align:right; padding:0px; font-size:18px;\">
							<input type=\"button\" onclick=\"cart_add('add','$it[gp_id]','$gp[gpcode]')\" value=\"장바구니 담기\" id='sit_btn_buy2' class='btn_cart' style=\"height:38px; font-size:1.1em; font-weight:bold;\">
						</div>

						<div class='prdlist_bottom'>
							$카드가
							구매가 : ".number_format($it[cash_price])."원<br>
							$재고
						</div>
					</div>
			";
			$item_cnt++;

			//마지막아이템은 </div> 하나
			if($item_cnt == $item_size) {
				
				if(G5_IS_MOBILE) {
					echo "</li>";	
				} else {
					echo "</div>";
				}				
				
			} else if( ($item_cnt % $상품노출개수 == 0) && $item_cnt != 0) {
				if(G5_IS_MOBILE) {
					echo "</li><li class='slide_contain'>";
				} else {
					//줄바꿈시
					echo "</div><div class='slide_contain'>";
				}
			}
		}

		if(G5_IS_MOBILE) {
			echo "</ul></div>";
		}
		else {
			echo "</section>";
		}	
		
	}
}


/* 노출여부 설정된 카테고리의 상품목록 HTML 생성코드 */
function makeHtmlCatePrdList() {
	global $is_admin, $sql_product, $상품노출개수;
	
	$height = (G5_IS_MOBILE) ? '140px' : '230px';
	$margin = (G5_IS_MOBILE) ? '5px' : '15px';

	$ca_sql = "	SELECT	*
							FROM		g5_shop_category
							WHERE		ca_id	LIKE 'CT%'
							AND			LENGTH(ca_id) >= 4
							AND			ca_use = 1
							ORDER BY ca_order ASC, ca_id ASC
	";
	$ca_result = sql_query($ca_sql);
	
	
	while($ca = mysql_fetch_array($ca_result)) {
		
		echo "<div class='prdlist_title cut_text1line'>
						<a href='/shop/gplist.php?gpcode=QUICK&ca_id=$ca[ca_id]'>$ca[ca_name]
						<font color=red>&nbsp;&nbsp;&nbsp;☜ 전체보기</font>
						<br></a>
					</div>";

		$replaced_sql_product = str_replace('#상품기본조건#', " AND		ca_id = '$ca[ca_id]' ", $sql_product);
		$it_sql = "	SELECT	T.*,
												IF(T.real_jaego > 0,T.real_jaego,0) AS real_jaego,
												CASE
													WHEN	T.ca_id LIKE 'CT%' || T.ca_id = 'GP'	THEN
														CASE
															WHEN	T.gp_price_type = 'Y'	THEN	/*실시간스팟시세*/
																CEIL(T.gp_realprice / 100) * 100
															WHEN	T.gp_price_type = 'N'	THEN	/*고정가달러환율*/
																CEIL(T.gp_fixprice / 100) * 100
															WHEN	T.gp_price_type = 'W'	THEN	/*원화적용*/
																T.gp_price
															ELSE
																0
														END
													ELSE
														CEIL(IFNULL(T.po_cash_price,T.gp_price) / 100) * 100
												END cash_price
								FROM		$replaced_sql_product
								WHERE		T.real_jaego > 0
								AND			T.gp_use = 1
								AND			IF( (T.gp_realprice + T.gp_fixprice + T.gp_price) > 0,TRUE,FALSE)
								ORDER BY T.gp_update_time DESC
								LIMIT 24
		";
		$it_result = sql_query($it_sql);
		
		if($_GET[mode] == 'jhw') echo "<textarea>$it_sql</textarea>";
		
		$item_cnt = 0;
		$item_size = mysql_num_rows($it_result);

		if(G5_IS_MOBILE) {
			echo "<div class='flexslider'>
							<ul class='slides'>
								<li class='slide_contain'>
			";

		}
		else {
			echo "<section class='slide' style='display:none;'>
							<div class='slide_contain'>";
		}
		
		while ($it = mysql_fetch_array($it_result)) {
			$it[card_price] = ceil($it[cash_price] * 1.03 / 100) * 100;
			$jaego = ($it[real_jaego] > 0) ? $it[real_jaego] : 0;
			$카드가 = ($it[gp_card] == '사용안함') ? "카드가 : ".number_format($it[card_price])."원<br>" : "";

			$imgthumb = getThumb($it);
			$it[gp_img] = $imgthumb[src];
			
			if($is_admin == 'super') {
				$재고 = " 남은수량 : " . number_format($it[real_jaego]) . " ea ";
			} 
			
			echo "<div class='prdlist_item'>
							<a href='/shop/grouppurchase.php?gpcode=QUICK&gp_id=$it[gp_id]&ca_id=$it[ca_id]'>
							<span class='sct_icon' style='position:absolute;'>".item_icon1($it)."</span>
							<div class='imgLiquidNoFill imgLiquid' style='width:98%;height:$height; margin:$margin auto;'>
								<img src='$it[gp_img]' />
							</div>
							<div class='prdlist_itemname cut_text'>$it[gp_name]</div>
							</a>
							<div class='prdlist_btn'>
								<input type=\"text\" id=\"" . $it[gp_id] . "_qty\" name=\".$it[gp_id].\" class=\"frm_input\" value=\"1\" style=\"width:35px; height:38px; text-align:right; padding:0px; font-size:18px;\">
								<input type=\"button\" onclick=\"cart_add('add','$it[gp_id]','QUICK')\" value=\"장바구니 담기\" id=\"sit_btn_buy2\" class='btn_cart' style=\"width:auto; height:38px; font-size:1.1em; font-weight:bold;\">
							</div>
	
							<div class='prdlist_bottom'>
								$카드가
								구매가 : " . number_format($it[cash_price]) . "원<br>
								$재고
							</div>
						</div>
			";
			$item_cnt++;

			//마지막아이템은 </div> 하나
			if($item_cnt == $item_size) {

				if(G5_IS_MOBILE) {
					echo "</li>";
				} else {
					echo "</div>";
				}

			} else if( ( ($item_cnt % $상품노출개수) == 0) && $item_cnt != 0) {
				if(G5_IS_MOBILE) {
					echo "</li><li class='slide_contain'>";
				} else {
					//줄바꿈시
					echo "</div><div class='slide_contain'>";
				}
			}
		}
		if(G5_IS_MOBILE) {
			echo "</ul></div>";
		}
		else {
			echo "</section>";
		}
	}
}

function getLeftTime($enddate) {
	$str = "";
	$iDay = 0;
	$Start = time();
	$End = strtotime($enddate);

	$DiffTime = $End - $Start;
	$iMinGap = $DiffTime / (1000 * 60) ; //1초는 1000ms초
	$iHourGap = $DiffTime / (1000 * 60 * 60) ; //1초는 1000ms초
	
	$DiffDay = floor($DiffTime / 86400);
	$DiffHour = floor($DiffTime % 86400 / 3600);
	$DiffMin = floor($DiffTime % 86400 % 3600 / 60);
	$DiffSec = $DiffTime % 86400 % 3600 % 60;
	
	
	if($iMinGap < 0)
		$str = "경매종료";
	else if($iHourGap == 0)
		$str = "마감임박";
	else {
		if ($DiffDay > 0) {
			$str = $DiffDay."일";
			if ($DiffHour > 0) $str = $str." ".$DiffHour."시간";
		}
		else if($DiffHour > 0) {
			if ($DiffHour > 0) $str = $str." ".$DiffHour."시간";
		}
		else {
			if ($DiffMin > 0) $str = $str." ".$DiffMin."분";
			if ($DiffSec >= 0) $str = $str." ".$DiffSec."초";	
		}	
		
	}
	
	return $str;
} 


/* 경매상품 목록 HTML 생성 */
function makeHtmlAucPrdList() {
	global $sql_auction_item, $상품노출개수, $member;

	$height = (G5_IS_MOBILE) ? '140px' : '230px';
	$margin = (G5_IS_MOBILE) ? '0px' : '15px';

	//경매상품 정보
	$sql_auction_item = str_replace('#상품기본조건#', " AND ac_yn = 'Y' AND	ac_enddate > DATE_ADD(NOW(), INTERVAL -18 HOUR)  ", $sql_auction_item);
	
	$sql_auction_item.=" ORDER BY T.ac_enddate ASC";
	$it_result = sql_query($sql_auction_item);

	$item_cnt = 0;
	$item_size = mysql_num_rows($it_result);
	
	if($_GET[mode] == 'jhw') echo "<textarea>".$sql_auction_item."  ".$sql."</textarea>";
	
	if($item_size < 1) return false;
	
	//<a href='/shop/auclist.php?gpcode=AUCTION'>오늘의 경매	<font color=red>&nbsp;&nbsp;&nbsp;☜ 전체보기</font><br></a>
	echo "<div class='prdlist_title cut_text1line'>
					<font color='red'>오늘의 경매</font>
				</div>";


	if(G5_IS_MOBILE) {
		echo "<div class='flexslider'>
						<ul class='slides'>
							<li class='slide_contain'>
		";

	}
	else {
		echo "<section class='slide' style='display:none;'>
						<div class='slide_contain'>";
	}

	while ($it = mysql_fetch_array($it_result)) {
		$it[card_price] = ceil($it[cash_price] * 1.03 / 100) * 100;
		$jaego = ($it[real_jaego] > 0) ? $it[real_jaego] : 0;
		$현재가 = ($it[MAX_BID_LAST_PRICE]) ? $it[MAX_BID_LAST_PRICE] : $it[ac_startprice];
		$즉시구매가 = ($it[ac_buyprice]) ? $it[ac_buyprice] : $it[po_cash_price];	//즉구가 설정값이 없으면 실시간시세값으로 설정
		$종료일 = date("n/j H:i",strtotime($it[ac_enddate]));
		$남은시간 = getLeftTime($it[ac_enddate]);
		
		//나의입찰액이 최고가인 경우 나의 입찰금액 노출
		$나의입찰금액 = ($it[MAX_BID_PRICE] == $it[MY_BID_PRICE]) ? "<dl><font color='blue' style='font-size:1.1em; font-weight:bold;'>최고가 입찰중 ".number_format($it[MY_BID_PRICE])."원</font></dl>" : "";
		
		echo "<div class='prdlist_item'>
						<a href='/shop/auction.php?gp_id=$it[gp_id]'>
						<span class='sct_icon' style='position:absolute;'>".item_icon1($it)."</span>
						<div class='imgLiquidNoFill imgLiquid' style='width:98%;height:$height; margin:$margin auto;'>
							<img src='$it[gp_img]' />
						</div>
						<div class='prdlist_itemname cut_text'>$it[gp_name]</div>
						</a>
						<div class='prdlist_btn'>							
						</div>
						<div class='prdlist_bottom'>
							<dl> <dt>현재가 ".number_format($현재가)."원</dt></dt>
							$나의입찰금액
							<dl>시세정보 ".number_format($즉시구매가)."원</dt>
							<dl>종료일 $종료일</dl>
							<dl>남은시간 $남은시간</dl>
						</div>
					</div>
		";
		$item_cnt++;

		//마지막아이템은 </div> 하나
		if($item_cnt == $item_size) {

			if(G5_IS_MOBILE) {
				echo "</li>";
			} else {
				echo "</div>";
			}

		} else if( ( ($item_cnt % $상품노출개수) == 0) && $item_cnt != 0) {
			if(G5_IS_MOBILE) {
				echo "</li><li class='slide_contain'>";
			} else {
				//줄바꿈시
				echo "</div><div class='slide_contain'>";
			}
		}
	}
	if(G5_IS_MOBILE) {
		echo "</ul></div>";
	}
	else {
		echo "</section>";
	}
	
//	exit;
}

/* 배열과 키값으로 셀렉트박스 만들기 */
function makeSelectbox($sbox,$key,$id) {

	$colname = $sbox[0][col];
	$html = "<select id='$id' name='$id'>";

	for($i = 0; $i < count($sbox); $i++) {
		$arr = $sbox[$i];
		$selected = ($arr[code] == $key) ? "selected" : "";

		$html .= "<option value='$arr[code]' $selected>$arr[value]</option>";
	}
	$html .= "</select>";

	return $html;
}

/* 컬러 선택 */
function choiceCodeColor($배열,$코드) {
	for($i = 0; $i < count($배열); $i++) {
		if($배열[$i][code] == $코드) return $배열[$i][bgcolor];
	}
}


/* 무게별 배송비 구하기 */
function getDeliveryPricePerKg($총무게kg) {
	global $개별공구,$member;

	$총무게kg = ($총무게kg) ? $총무게kg : 2;
	$배송비 = ($개별공구[baesongbi]) ? $개별공구[baesongbi] : 3500;
	
	/*30키로 단위 배송비 처리*/
	if(30 < $총무게kg) {
		$초과수량 = floor($총무게kg / 30);
		$남은kg = $총무게kg - ($초과수량 * 30);
		$누적배송비 = $초과수량 * 8500;
		if(strstr($member[mb_addr1],'제주')) $누적배송비 += ($초과수량*500);
	} 
	else {
		$남은kg = $총무게kg;
	}
	
	/*남은kg 배송비 처리*/
	switch($남은kg) {
		case ($남은kg > 20 && $남은kg < 30):
			$누적배송비 += 8500;
			break;
		case ($남은kg > 10 && $남은kg < 20):
			$누적배송비 += 7000;
			break;
		case ($남은kg > 5 && $남은kg < 10):
			$누적배송비 += 5500;
			break;
		case ($남은kg > 2 && $남은kg < 5):
			$누적배송비 += 4000;
			break;
		default:
			$누적배송비 += 3500;
			break;
	}
	if(strstr($member[mb_addr1],'제주')) $누적배송비 += 500;
	
	return $누적배송비;
}


/* 여러개의 키값들을 explode후 SQL조건문에 사용할 문자열로 재생성 */
function explodeMakeCode($구분문자,$문자열) {

	$키값들 = explode($구분문자, $문자열);

	for($i = 0; $i < count($키값들); $i++) {
		$완성문자열 .= "'".$키값들[$i]."',";
	}
	$완성문자열 = substr($완성문자열, 0, strlen($완성문자열) - 1 );

	return $완성문자열;
}


//공동구매 카테고리 메뉴
function createGpCategoryMenu() {

	$gp_sql = "	SELECT	*
							FROM		gp_info	GI
							WHERE		1=1
							AND			GI.start_date <= NOW()
							AND			GI.end_date >= NOW()
							AND			GI.stats = '00'
							AND			GI.menu_view = 'Y'
	";
	$gi_result = sql_query($gp_sql);

	while ($gi = mysql_fetch_array($gi_result)) {
		$html.="<li><a href='/shop/list.php?gpcode=$gi[gpcode]&ca_id=GP'>$gi[menu_name]</a></li>";
	}

	return $html;
}


function getDataGpJaego($gp_id) {
	$prev_sql = "	SELECT	gp_id,
												CONCAT('재고:',jaego) AS jaego,
												CONCAT('공구재고:',gp_jaego) AS gp_jaego
								FROM		g5_shop_group_purchase
								WHERE		gp_id = '$gp_id'
	";
	$prev = sql_fetch($prev_sql);
	return implode(", ",$prev);	
}

/*DB로그 작성*/
function db_log($쿼리,$테이블명,$메모,$이전데이터='',$현재데이터='') {
	global $member;
	$관리자ID = $member[mb_id];
	$파일명 = $_SERVER['PHP_SELF'];

	$쿼리 =	preg_replace("/\t\t\t\t/",' ',$쿼리);
	$쿼리 =	preg_replace("/\"/",'',$쿼리);
	$쿼리 =	preg_replace("/\'/",'',$쿼리);
	$쿼리 = strip_tags($쿼리);
	
	$sql = "	INSERT INTO 	db_log	SET
												src = '$파일명',							/*소스명*/
												sqltxt = \"$쿼리\",				/*쿼리SQL*/
												prev = '$이전데이터',
												after = '$현재데이터',
												tb = '$테이블명',								/*테이블명*/
												memo = '$메모',
												admin_id = '$관리자ID',	/*관리자 계정*/
												reg_date = now()					/*생성일*/
	";
	sql_query($sql);	
}

//최소입찰가 구하기
function calcBidPrice($price) {

	if(0 <= $price && $price < 100000 ) {
		$price += 1000;
	}
	else if(100000 <= $price && $price < 500000 ) {
		$price += 2000;
	}
	else if(500000 <= $price && $price < 1000000 ) {
		$price += 5000;
	}
	else if(1000000 <= $price ) {
		$price += 10000;
	}

	return $price;
}

function _microtime() { return array_sum(explode(' ',microtime())); }


?>