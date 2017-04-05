<?php
$sub_menu = '700300';
include_once('./_common.php');

$g5['title'] = '상품 엑셀일괄등록 결과';
include_once(G5_PATH.'/head.sub.php');
?>
<style>
body { background-color:white; }
</style>

<div class="new_win" style='background-color:white;'>

<?
// 상품이 많을 경우 대비 설정변경
set_time_limit ( 0 );
ini_set('memory_limit', '50M');

auth_check($auth[$sub_menu], "w");

function only_number($n)
{
	return preg_replace('/[^0-9]/', '', $n);
}

if($_FILES['excelfile']['tmp_name']) {
	$file = $_FILES['excelfile']['tmp_name'];

	include_once(G5_LIB_PATH.'/Excel/reader.php');

	$data = new Spreadsheet_Excel_Reader();

	// Set output Encoding.
	$data->setOutputEncoding('UTF-8');

	/***
	* if you want you can change 'iconv' to mb_convert_encoding:
	* $data->setUTFEncoder('mb');
	*
	**/

	/***
	* By default rows & cols indeces start with 1
	* For change initial index use:
	* $data->setRowColOffset(0);
	*
	**/



	/***
	*  Some function for formatting output.
	* $data->setDefaultFormat('%.2f');
	* setDefaultFormat - set format for columns with unknown formatting
	*
	* $data->setColumnFormat(4, '%.3f');
	* setColumnFormat - set format for column (apply only to number fields)
	*
	**/

	$data->read($file);

	/*


	 $data->sheets[0]['numRows'] - count rows
	 $data->sheets[0]['numCols'] - count columns
	 $data->sheets[0]['cells'][$i][$j] - data from $i-row $j-column

	 $data->sheets[0]['cellsInfo'][$i][$j] - extended info about cell

	$data->sheets[0]['cellsInfo'][$i][$j]['type'] = "date" | "number" | "unknown"
		if 'type' == "unknown" - use 'raw' value, because  cell contain value with format '0.00';
	$data->sheets[0]['cellsInfo'][$i][$j]['raw'] = value if cell without format
	$data->sheets[0]['cellsInfo'][$i][$j]['colspan']
	$data->sheets[0]['cellsInfo'][$i][$j]['rowspan']
	*/

	error_reporting(E_ALL ^ E_NOTICE);

	$dup_it_id = array();
	$fail_it_id = array();
	$dup_count = 0;
	$total_count = 0;
	$fail_count = 0;
	$succ_count = 0;

	flush();
	ob_flush();
	//$sleepsec = 200;  // 천분의 몇초간 쉴지 설정

		for ($t = 2; $t <= $data->sheets[0]['numRows']; $t++) {
		$total_count++;
	
		$j = 1;
	
		$tmp_gp_id		  = addslashes($data->sheets[0]['cells'][$t][$j++]);
		$gp_site		  = addslashes($data->sheets[0]['cells'][$t][$j++]);
			$gp_charge			 = addslashes($data->sheets[0]['cells'][$t][$j++]);
			$gp_duty			 = addslashes($data->sheets[0]['cells'][$t][$j++]);
			$gp_order			 = addslashes($data->sheets[0]['cells'][$t][$j++]);
			$gp_use			 = addslashes($data->sheets[0]['cells'][$t][$j++]);
			$gp_price			 = addslashes($data->sheets[0]['cells'][$t][$j++]);
			$gp_type			 = addslashes($data->sheets[0]['cells'][$t][$j++]);
			$ca_id			 = addslashes($data->sheets[0]['cells'][$t][$j++]);
			$ca_id2			 = addslashes($data->sheets[0]['cells'][$t][$j++]);
			$ca_id3			 = addslashes($data->sheets[0]['cells'][$t][$j++]);
	
	
		if(!$gp_site) {
			$fail_count++;
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
			/* 상품코드 추출 */
			@eregi("<div id=\"product_code\" class=\"product_code\" itemprop=\"sku\">([A-Z0-9]+)<\/div>",$Response,$match_code);
			$gpcode = $match_code[1];
			
			
			@eregi("<h1 itemprop=\"name\">(.*)<\/h1>",$Response,$match_title);
			$gp_name = $match_title[1];//0은 전체, 1은
			
			preg_match_all("/https:\/\/static-moderncoinmart.netdna-ssl.com\/images\/[D]+\/[\/a-zA-Z0-9_\.\?\=\-\%]+/i",$Response,$match_img); // 
			$img_url = $match_img[0];
			$gp_img = $img_url[0];
			
			
			for($i = 1; $i < count($img_url); $i++) {
				$img_tag .= "<img src='".$img_url[$i]."' width='450' height='450' /><br>";
			}
			
			//preg_match_all("/\<table cellspacing\=\"0\" cellpadding\=\"0\" summary\=\"Description\"\>([.\s\t]*)\<\/table\>/",$Response,$match_bodytext);
			//$gp_explan = $match_bodytext[1];
			
			$gp_explan = explodeCut($Response,'<td class="descr" itemprop="description">','</td>');				
			$gp_explan = "<center>".$img_tag."</center>".$gp_explan;
			$gp_explan = str_replace("'", '', $gp_explan);
			$gp_explan = str_replace('"', '', $gp_explan);
			
			/*옵션테이블 추출*/
			@eregi('var product_avail \= ([0-9]+)',$Response,$tmp_jaego);//상품최대신청가능수량(재고)
			$jaego = $tmp_jaego[1];
			@eregi("<table class=\"wcm_volume_price_table\">",$Response,$match_wcmtable);
							
			/* 볼륨 테이블 존재할경우 옵션으로 등록 */
			if(strlen($match_wcmtable[0]) > 10) {
				
				$tabletag = explodeCut($Response,'<table class="wcm_volume_price_table">','</table>');
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
		else if(stristr($gp_site,"apmex.com")){
			/* APMEX는 URL에 코드번호가 존재함 */
			$gpcode = 'ap_'.explodeCut($gp_site, '/product/', '/');
			
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
			$gp_explan = getExplodeValue($Response,"<div class=\"product-description\">","</div>");
			$gp_explan .= getExplodeValue($Response,"<div class=\"product-specs\">","</div>");
			$gp_explan = "<center>".$img_tag."</center>".$gp_explan;
			$gp_explan = addslashes($gp_explan);
			
			
			
			$tmp_gp_pricing = getExplodeValue(getExplodeValue($Response,"<table class=\"table-volume-pricing\">","</table>"),"<tbody>","</tbody>");
			
			print_r($tmp_gp_pricing);
			
			$gp_pricing = explode("</tr>",str_replace(" itemprop=price","",str_replace(" itemprop=\"price\"","",$tmp_gp_pricing)));
	
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
			
		}elseif(stristr($gp_site,"gainesvillecoins.com")){
			/* 상품코드 추출 */
			$gain_code = explodeCut($gp_site,'/products/','/');
			$gpcode = 'gaines_'.$gain_code;
			
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
			
			preg_match_all("/[0-9]+[\s-]+[0-9]+|[0-9]+\+/i",$가격정보테이블,$tmp_limit);
			$match_limit = $tmp_limit[0];
			
			preg_match_all("/[\$]+([0-9]+\.[0-9]+)/i",$가격정보테이블,$tmp_price);	//인덱스 0:현금, 1:카드 ...
			$match_price = $tmp_price[1];
			
			
			/* 가격정보 테이블에 1+ 글자가 있을경우 단일옵션 */
			if(strstr($가격정보테이블,'1+')) {
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
				$optcnt = 0;
				for($i = 0; $i < count($match_price); $i++) {
					//마지막 볼륨프라이싱 줄은 5 or more 라서 " "으로 분할, 그외는 '-'로 분할
					$분할문자 = ( ($i+1) == count($match_price) ) ? "+" : ' - ';	//마지막행 분할문자는 '+', 그외는 ' - '
					$범위 = explode($분할문자,$match_limit[$optcnt]);
					
					$옵션[$optcnt][po_sqty] = trim($범위[0]);
					
					//마지막 범위는 가격정보가 최소 2줄 이상이고 첫번째 행일경우 1로, 그외는 모두 99999로
					$po_eqty = (count($match_price) > 1 && $i == 0) ? 1 : 99999;	
					
					// 분할문자 - 로 분할한 경우 범위[1]의 마지막범위값을 대입, 아니라면 위에서 설정한 po_eqty를 대입 
					$옵션[$optcnt][po_eqty] = ($범위[1] && $범위[1] != '+') ? str_replace('&nbsp;','',trim($범위[1])) : $po_eqty;
					
					//첫번째는 구매가, 두번째 값은 카드가
					if($i%2 == 0) {
						$옵션[$optcnt][po_cash_price] = str_replace(',','',$match_price[$i]);
					} else {
						$옵션[$optcnt][po_card_price] = str_replace(',','',$match_price[$i]);					
					}
					
					//구매가 입력후 카드가 입력후 다시 구매가 입력할때 옵션인덱스 증가
					if($i%2 == 1 && $i > 0) {
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
			
		$gp_name = str_replace('\"', "", $gp_name);	
		
		/* 옵션 데이터 DELETE 후 INSERT */
		sql_query("delete from $g5[g5_shop_group_purchase_option_table] where gp_id = '$gp_id'");
		
		$ins_sql = "";
		for($i=0;$i < count($gpPricing);$i++){
			$ins_sql = "INSERT	INTO	$g5[g5_shop_group_purchase_option_table] 	SET
															gp_id = '$gp_id',
															po_num = '$i',
															po_sqty = '".$gpPricing[$i][po_sqty]."',
															po_eqty = '".$gpPricing[$i][po_eqty]."',
															po_cash_price = '".$gpPricing[$i][po_cash_price]."',
															po_card_price = '".$gpPricing[$i][po_card_price]."'
															,po_jaego		=	'$jaego'
			";
			sql_query($ins_sql);
		}// for end
		
			
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
		
		$sql_common = " 	ca_id		  = '$ca_id',
											gp_site		= '$gp_site',
											gp_name		=	\"$gp_name\",
											gp_img		=	'$gp_img',
											gp_objective_price = '0',
											gp_explan	=	'".$gp_explan."',
											gp_use		= '".$gp_use."',
											gp_order		= '".$gp_order."',
											gp_type1 	= '".$gp_type1."',
											gp_type2 	= '".$gp_type2."',
											gp_type3 	= '".$gp_type3."',
											gp_type4 	= '".$gp_type4."',
											gp_type5 	= '".$gp_type5."',
											gp_charge	= '".$gp_charge."',
											gp_duty		= '".$gp_duty."',
											it_type		= '".$gp_type."',
											ca_id2		= '".$ca_id2."',
											ca_id3		= '".$ca_id3."',
											gp_sc_price	= '3500'
		";
		
		$sql_common .= " , gp_time = '".G5_TIME_YMDHIS."' ";
		$sql_common .= " , gp_update_time = '".G5_TIME_YMDHIS."' ";
	
		
		$gpid_sql = "	SELECT	gp_id
								FROM		{$g5['g5_shop_group_purchase_table']}
								WHERE	gp_id	= '$gp_id'
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
			
			$sql = " update {$g5['g5_shop_group_purchase_table']} set $sql_common	 where gp_id = '$갱신ID'";
		}
		else {
			$sql = " insert {$g5['g5_shop_group_purchase_table']} set gp_id = '$gp_id', $sql_common	";
		}
		//echo $sql."<br />";
		//exit;
		sql_query($sql);
	
	
	
		$succ_count++;
	
		flush();
		ob_flush();
		ob_end_flush();
		usleep($sleepsec);
	}	//for end
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

	<div class="btn_win01 btn_win">
	<button type="button" onclick="window.close();">창닫기</button>
	</div>

</div>

<?php
include_once(G5_PATH.'/tail.sub.php');
?>