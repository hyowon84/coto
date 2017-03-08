<?
include_once('./_common.php');

header("Content-Encoding: utf-8");

/* 오래된 순서대로 */
$src_sql = "SELECT	*
						FROM		data_src
						WHERE		site = 'MCM'
						ORDER BY mod_date ASC
						LIMIT 1
";
$src_result = sql_query($src_sql);
echo $src_sql."<br>";

while ($src = mysql_fetch_array($src_result)) {
	$gp_site = $src[url];
	$cate1 = $src[cate1];
	$cate2 = $src[cate2];
	$maxpage = 7;	//MCM 특성상 2페이지 넘어가는게 거의 없음.

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
				preg_match_all("/<a title\=\"[\/a-zA-Z0-9\-_\.\?\=\s\|\(\)\$]+\" href\=\"[\/a-zA-Z0-9\-_\.\?\=\s]+\" class\=\"product-title\"><span/i",$Response,$match_url); //		\"\/product\/[\/a-zA-Z0-9_\.\?\=]+\">
				$prd_url = $match_url[0];

				/* 상품주문 최대신청 제한갯수 추출 */
				preg_match_all("/check_quantity\([0-9]+\,[\s\t\'\"0-9]+\,[\s\t\'\"0-9]+\,[\s\t\'\"0-9\);]+ value/i",$Response,$match_jaego); //		\"\/product\/[\/a-zA-Z0-9_\.\?\=]+\">
				$prd_jaego = $match_jaego[0];
				//check_quantity(37876, '', 5, 1)

				/* bak_url 인덱스대로 정보 돌아가면서 입력 */
				for($i = 0; $i < count($prd_url); $i++) {
					$url = explodeCut($prd_url[$i],'href="','" class="');
					$bak_url[$i] = $url;

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
																	url		=	'$url',
																	jaego = '99999999',
																	reg_date	= now(),
																	mod_date	= '0000-00-00 00:00:00'
						";
						$msg = "INSERT : ";
						sql_query($upd_sql);
					}

					echo $msg.$url."<br>";
				} // for end


				if($mode == 'jhw') {
					echo "<br>############### bak_url : ";
					print_r($bak_url);
					echo "###############<br>";
				}

				/* 재고는 따로 루프문 돌려서 갱신 */
				for($i = 0; $i < count($prd_jaego); $i++) {
					/* 인덱스 추출 */
					preg_match_all("/\([0-9]+\,/i", $prd_jaego[$i], $tmp_code);
					preg_match_all("/\, [0-9]+\,/i", $prd_jaego[$i], $tmp_jaego);
					$rs_code = $tmp_code[0];
					$rs_jaego = $tmp_jaego[0];

					if($mode == 'jhw') {
						echo "<br>########<br>";
						print_r($prd_jaego);
						echo "<br>########<br>";
						print_r($tmp_jaego);
						echo "<br>########<br>";
						print_r($rs_jaego);
						echo "<br>########<br>";
					}

					$code = str_replace(',', '', $rs_code[0]);
					$code = "SKU".str_replace('(', '', $code);
					$jaego = str_replace(',', '', $rs_jaego[0]);

					$upd_sql = "	UPDATE	data_url	SET
																	it_id	=	'$code',
																	jaego	=	$jaego,
																	mod_date	= now()
												WHERE		url = '".$bak_url[$i]."'
					";
					sql_query($upd_sql);
					if($mode == 'jhw') echo "<pre>".$upd_sql."<pre><br>";


					$upd_sql = "	UPDATE	g5_shop_group_purchase	SET
																jaego =	$jaego,
																gp_update_time	= now()
												WHERE		gp_id = '$code'
												AND			ca_id LIKE 'MC%'
					";
					sql_query($upd_sql);
					db_log($upd_sql,'g5_shop_group_purchase',"딜러업체상품 재고값 갱신");
					
					if($mode == 'jhw') echo "<pre>".$upd_sql."<pre><br>";
				}

			} //if end

		} //if($Response) end

	} //for( $p )end	페이지별로 request

	$upd_sql = "	UPDATE	data_src	SET
													mod_date = now()
								WHERE		number = $src[number]
	";
	sql_query($upd_sql);

}//while

$script = "<script>
setTimeout(function(){
	location.reload();
},$geturl_time);
</script>";

// echo $script;

?>