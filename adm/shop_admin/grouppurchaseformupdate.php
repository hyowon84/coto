<?php
$sub_menu = '500200';
$sub_sub_menu = '2';

include_once('./_common.php');
header("Content-Encoding: utf-8");

if ($w == "u" || $w == "d")
		check_demo();

if ($w == '' || $w == 'u')
		auth_check($auth[$sub_menu], "w");
else if ($w == 'd')
		auth_check($auth[$sub_menu], "d");


// input vars 체크
check_input_vars();



$isDirectInp = false;

if($w=="" || $w=="u"){
	sql_query("delete from $g5[g5_shop_group_purchase_option_table] where gp_id = '$gp_id'");


	if($gp_site){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $gp_site);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);    //가끔가다가 인증서버에 대한 옵션이 있는데 믿을만 하다면 FALSE설정해도 됨
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


		if($Response){
			$gpPricing = array();

			/* 모던 코인마트 */
			if(stristr($gp_site,"moderncoinmart.com")){

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
				@eregi("<div id=\"product_code\" class=\"product_code\" itemprop=\"sku\">([A-Z0-9]+)<\/div>",$Response,$match_code);
				$gpcode = $match_code[1];


				$gp_explan = explodeCut($Response,'<td class="descr" itemprop="description">','</td>');
				$gp_explan = "<center>".$img_tag."</center>".$gp_explan;
				$gp_explan = str_replace("'", '"', $gp_explan);

				$gp_360img = str_replace("'", '"', $gp_360img);
				

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
				$gpcode = 'ap_'.explodeCut($gp_site,'/product/','/');

				//APMEX - 상품명 추출
				//preg_match_all("/<h1 class\=\"product-title\" [a-z0-9\-\'\"\=:;\s]+\s*>\s*\t*(.*)\s*\t*<\/h1>/",$Response,$match_title);
				preg_match_all("/<h1[a-z0-9\-\'\"\=:;\s]*\s*>\s*\t*(.*)\s*\t*<\/h1>/",$Response,$match_title);

				$gp_name = trim($match_title[1][0]);//0은 전체, 1은 내용
				$gp_name = str_replace('\"', "", $gp_name);

				preg_match_all("/http:\/\/www.images-apmex.com\/images\/Catalog Images\/Products\/[\/a-zA-Z0-9_\.\?\=]+\&amp;width\=450\&amp;height\=450/i",$Response,$match_img); //
				$img_url = $match_img[0];
				$gp_img = $img_url[0];


				for($i = 0; $i < count($img_url); $i++) {
					$img_tag .= '<img src="'.$img_url[$i].'" width="450" height="450" /><br>';
				}

				// 				$gp_name = getExplodeValue($Response,"<h1 class=\"product-title\" itemprop=\"name\">","</h1>");
				// 				$gp_img = getExplodeValue(getExplodeValue($Response,"<div class=\"small\">","</div>"),"src=\"","\"");

				if(!$gp_img) $gp_img = getExplodeValue(getExplodeValue($Response,"<div class=\"small az-small\">","</div>"),"src=\"","\"");
				//$gp_explan = getExplodeValue($Response,"<div class=\"product-description\">"," <div class=\"product-specs\">");
				$gp_explan = getExplodeValue($Response,"<div class=\"product-description\">","</div>");
				$gp_explan .= getExplodeValue($Response,"<div class=\"product-specs\">","</div>");
				$gp_explan = "<center>".$img_tag."</center>".$gp_explan;
				$gp_explan = addslashes($gp_explan);



				$tmp_gp_pricing = getExplodeValue(getExplodeValue($Response,"<table class=\"table-volume-pricing\">","</table>"),"<tbody>","</tbody>");
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

				$isDirectInp = true;

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
				$스펙스타일 = "<style> #pdSpecs table { width: 80%;	}
				#pdSpecs table tr:nth-child(even) {  background: #f1f1f1;}
				#pdSpecs table tr td:first-child {  color: #777;}
				#pdSpecs table tr td { font-size: .96em; padding: 8px;	} </style>";
				$상세설명 = explodeCut($Response,'<div class="text">','</div>');
				$gp_explan = $스펙스타일.'<center>'.$img_tag.'<div id="pdSpecs">'.$스펙정보."</div></center>".$상세설명;
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
		}
	}


	$gp_id = $_POST['gp_id'];
	if(strlen($gpcode) > 3) {
		$gp_id = $gpcode;
	}




	/* 상품 수동으로 수정여부인듯? */
	if($isDirectInp){


		for($i=0;$i<count($gpPricing);$i++){

			$po_cash_price = $gpPricing[$i][po_cash_price];
			$po_card_price = $gpPricing[$i][po_card_price];
			//echo "insert into $g5[g5_shop_group_purchase_option_table] set gp_id = '$gp_id', po_num = '$i', po_sqty = '".$gpPricing[$i][po_sqty]."', po_eqty = '".$gpPricing[$i][po_eqty]."', po_cash_price = '".$po_cash_price."', po_card_price = '".$po_card_price."'</br>";
			$ins_sql = "INSERT	INTO	$g5[g5_shop_group_purchase_option_table]	SET
														gp_id		= '$gp_id',
														po_num	= '$i',
														po_sqty	= '".$gpPricing[$i][po_sqty]."',
														po_eqty	= '".$gpPricing[$i][po_eqty]."',
														po_cash_price = '".$po_cash_price."',
														po_card_price = '".$po_card_price."'
														,po_jaego		=	'$jaego'
			";
			sql_query($ins_sql);
		}

	}else{

		for($i=0,$k=0;$i<count($_POST[po_qty_type]);$i++){

			if(($_POST['gp_price_type']=="N" && $_POST[po_cash_price][$i]) || $_POST['gp_price_type']=="Y"){

				$po_sqty = $po_eqty = "";
				if($_POST[po_qty_type][$i]==1){
					$po_sqty = 0;
					$po_eqty = (count($gp_pricing) > 1) ? 1 : 99999;
				}else{
					$po_sqty = $_POST[po_sqty][$i];
					$po_eqty = $_POST[po_eqty][$i];
				}

				if($po_sqty || $po_eqty){

					$po_cash_price = $_POST[po_cash_price][$i];
					$po_card_price = $_POST[po_card_price][$i];
					sql_query("insert into $g5[g5_shop_group_purchase_option_table] set gp_id = '$gp_id', po_num = '$k', po_sqty = '".$po_sqty."', po_eqty = '".$po_eqty."', po_add_price = '".$_POST[po_add_price][$i]."', po_cash_price = '".$po_cash_price."', po_card_price = '".$po_card_price."'");

					$k++;
				}
			}
		}

	}

}

if(!$isDirectInp){
	$gp_name = $_POST[gp_name];
	$gp_img = $_POST[gp_img];
	$gp_explan = $_POST[gp_explan];
	$gp_360img = $_POST[gp_360img];
}

$gp_name = str_replace('\"', "", $gp_name);

if($_POST[gp_type0] == "1"){
	$gp_type1 = "";
	$gp_type2 = "";
	$gp_type3 = "";
	$gp_type4 = "";
	$gp_type5 = "";
}else{
	if($_POST[gp_type] == 1) $gp_type1 = 1;
	if($_POST[gp_type] == 2) $gp_type2 = 1;
	if($_POST[gp_type] == 3) $gp_type3 = 1;
	if($_POST[gp_type] == 4) $gp_type4 = 1;
	if($_POST[gp_type] == 5) $gp_type5 = 1;
}


$event_yn = ($event_yn) ? $event_yn : 'N';

$sql_common = "
				ca_id								= '$ca_id',
				/*ca_id2							= '$ca_id2',
				ca_id3							= '$ca_id3',*/
				event_yn			    	= '$event_yn',
				b2b_yn							= '$b2b_yn',
				gp_site							= '$gp_site',
				gp_name							= \"$gp_name\",
				gp_img							= '$gp_img',
				gp_charge						= '$gp_charge',
				gp_duty							= '$gp_duty',
				gp_objective_price	= '$gp_objective_price',
				gp_explan						= '".$gp_explan."',
				gp_360img						= '".$gp_360img."',
				gp_use							= '$gp_use',
				gp_order						= '$gp_order',
				gp_type3						= '$gp_type3',
				gp_type4						= '$gp_type4',
				gp_type5						= '$gp_type5',
				gp_type6						= '$gp_type6',
				gp_metal_type				= '".$_POST[gp_metal_type]."',
				gp_metal_don				= '".$_POST[gp_metal_don]."',
				gp_metal_etc_price	= '".$_POST[gp_metal_etc_price]."',
				gp_price_type				= '".$_POST[gp_price_type]."',
				gp_sc_method				= '".$_POST[gp_sc_method]."',
				gp_sc_price					= '".$_POST[gp_sc_price]."',
				it_type							= '".$_POST[it_type]."'

				";

if ($w == "")
{

		if (!trim($gp_id)) {
				alert('상품 코드가 없으므로 상품을 추가하실 수 없습니다.');
		}

//		$t_gp_id = preg_replace("/[A-Za-z0-9\-_]/", "", $gp_id);
//		if($t_gp_id)
//				alert('상품 코드는 영문자, 숫자, -, _ 만 사용할 수 있습니다.');

		$sql_common .= " , gp_time = '".G5_TIME_YMDHIS."' ";
		$sql_common .= " , gp_update_time = '".G5_TIME_YMDHIS."' ";
		$sql = " insert {$g5['g5_shop_group_purchase_table']}
								set gp_id = '$gp_id',
					$sql_common	";

		sql_query($sql);
}
else if ($w == "u")
{
		$sql_common .= " , gp_update_time = '".G5_TIME_YMDHIS."' ";
		$sql = " update {$g5['g5_shop_group_purchase_table']}
								set $sql_common
							where gp_id = '$gp_id' ";
		sql_query($sql);
	
	db_log($sql,'g5_shop_group_purchase','상품수정페이지');
}


//옵션 추가, 수정
$op_chk = false;

sql_query("delete from {$g5['g5_shop_option1_table']} where it_id='".$gp_id."' ");
sql_query("delete from {$g5['g5_shop_option2_table']} where it_id='".$gp_id."' ");

for($i = 0; $i < count($_POST[gp_op_title]); $i++){
	if($_POST[gp_op_title][$i]){
		$sql = "
			insert into {$g5['g5_shop_option1_table']} set
			gubun='P',
			con='".$_POST[gp_op_title][$i]."',
			it_id='".$gp_id."'
		";
		//echo $sql."</br>";
		sql_query($sql);

		$num = mysql_insert_id();

		for($j = 0; $j < count($_POST["gp_op_name".$i]); $j++){
			$_POST["gp_op_name".$i][$j] = $_POST["gp_op_name".$i][$j];
			$_POST["gp_op_price".$i][$j] = $_POST["gp_op_price".$i][$j];
			$sql1 = "
				insert into {$g5['g5_shop_option2_table']} set
				num='".$num."',
				con='".$_POST["gp_op_name".$i][$j]."',
				price='".$_POST["gp_op_price".$i][$j]."',
				it_id='".$gp_id."'
			";
			//echo $sql1."</br>";
			sql_query($sql1);
		}
	}
}


$qstr = "$qstr&amp;sca=$sca&amp;page=$page";

if ($w == "d")  {
		$qstr = "ca_id=$ca_id&amp;sfl=$sfl&amp;sca=$sca&amp;page=$page&amp;stx=".urlencode($stx)."&amp;save_stx=".urlencode($save_stx);
		goto_url("./grouppurchaselist.php?$qstr");
}else goto_url("./grouppurchaseform.php?w=u&amp;gp_id=$gp_id&amp;$qstr");
?>