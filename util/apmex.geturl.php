<?
include_once('./_common.php');
header("Content-Encoding: utf-8");

$data_config = mysql_fetch_array(sql_query("SELECT	*	FROM	data_config"));
if($data_config['APMEX'] != 'Y') exit;

$proxyip_sql = "SELECT	*
								FROM		data_proxyip
								WHERE		stats NOT IN (99)
								ORDER BY rand()
";
$proxy_result = sql_query($proxyip_sql);
$proxy = mysql_fetch_array($proxy_result);
$proxy_ip = $proxy[ip].":".$proxy[port];
if($mode == 'jhw') echo $proxy_ip."<br><br>";

/* 오래된 순서대로 */
$src_sql = "SELECT	*
						FROM		data_src
						WHERE		site = 'APMEX'
						ORDER BY mod_date ASC
						LIMIT 1
";
$src_result = sql_query($src_sql);
echo $src_sql."<br>";


while ($src = mysql_fetch_array($src_result)) {
	$gp_site = $src[url];
	$cate1 = $src[cate1];
	$cate2 = $src[cate2];
	$maxpage = 10;

	for($p = 1; $p <= $maxpage; $p++) {
		$curl = curl_init();
		$site_url = $gp_site.'&f_isinstock=true&page='.$p;
		echo $site_url."<br>";
		//   /all?page=2&ipp=120&vt=l

		$Response = curl($site_url,$proxy_ip);

		if(!$Response) {
			$fail_count++;
			continue;
		}


		if($Response){

			if(stristr($gp_site,"apmex.com")){
				/* 상품URL 추출 */
				preg_match_all($정규패턴['APMEX']['상품URL'],$Response,$match_url); //		\"\/product\/[\/a-zA-Z0-9_\.\?\=]+\">
				$prd_url = $match_url[0];

				/* 상품주문 최대신청 제한갯수 추출 */
				preg_match_all($정규패턴['APMEX']['재고수량'],$Response,$match_jaego); //		\"\/product\/[\/a-zA-Z0-9_\.\?\=]+\">
				$prd_jaego = $match_jaego[0];

				for($i = 0; $i < count($prd_url); $i++) {
					$url = explodeCut($prd_url[$i],'<div class="product-item-title"><a href="','">');

					$sel_sql = "	SELECT	*
												FROM		data_url
												WHERE		site	= 'APMEX'
												AND			url 	= '$url'
					";
					$result = sql_query($sel_sql);
					$row = mysql_fetch_array($result);

					if($row[number] > 0) {
						$msg = "UPDATE : ";

					} else {
						$upd_sql = "	INSERT	INTO	data_url	SET
																	site	=	'APMEX',
																	cate1 = '$cate1',
																	cate2 = '$cate2',
																	url	=	'$url',
																	jaego = '0',
																	reg_date	= now(),
																	mod_date	= '0000-00-00 00:00:00'
						";
						$msg = "INSERT : ";
						sql_query($upd_sql);
					}

					echo $msg.$url."<br>";
				} // for end


				/* 재고는 따로 루프문 돌려서 갱신 */
				for($i = 0; $i < count($prd_jaego); $i++) {
					/* 인덱스 추출 */
					preg_match_all("/\([0-9]+\,/i",$prd_jaego[$i],$tmp_code);
					preg_match_all("/\,[0-9]+\,/i",$prd_jaego[$i],$tmp_jaego);
					$rs_code = $tmp_code[0];
					$rs_jaego = $tmp_jaego[0];
					$code = str_replace(',', '', $rs_code[0]);
					$code = str_replace('(', '', $code);
					$jaego = str_replace(',', '', $rs_jaego[0]);

					$upd_sql = "	UPDATE	data_url	SET
																	it_id = 'AP_$code',
																	jaego = $jaego,
																	mod_date	= now()
												WHERE		url		LIKE	'%/$code/%'
					";
					echo $upd_sql."<br>";
					sql_query($upd_sql);

					$upd_sql = "	UPDATE	g5_shop_group_purchase	SET
																	jaego = $jaego
												WHERE		gp_id = 'AP_$code'
												AND			ca_id LIKE 'AP%'
					";
					echo $upd_sql."<br>";
					sql_query($upd_sql);
					db_log($upd_sql,'g5_shop_group_purchase',"딜러업체상품 재고값 갱신 ");					
					
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
	//location.href=\"gains.geturl.php\";
	location.reload();
},$geturl_time);
</script>";

// echo $script;

?>
