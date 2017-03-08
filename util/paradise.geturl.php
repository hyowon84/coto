<?
include_once('./_common.php');

header("Content-Encoding: utf-8");

/* 오래된 순서대로 */
$src_sql = "SELECT	number,
										site,	/*APMEX*/
										cate1,	/*카테고리*/
										cate2,
										url,	/*URL*/
										charge,	/*수수료*/
										duty,	/*관세*/
										reg_date,	/*최초등록일*/
										mod_date	/*갱신일*/
						FROM		data_src
						WHERE		site = 'PARADISE'
						ORDER BY mod_date ASC
						LIMIT 1
";
$src_result = sql_query($src_sql);
echo $src_sql."<br>";

while ($src = mysql_fetch_array($src_result)) {
	$gp_site = $src[url];
	$cate1 = $src[cate1];
	$cate2 = $src[cate2];
	$maxpage = 1;	//PARADISE 특성상 2페이지 넘어가는게 거의 없음.

	for($p = 1; $p <= $maxpage; $p++) {
		$curl = curl_init();


		$site_url = $gp_site;

// 		if($p == 1) {
// 			$site_url = $gp_site.'/?view_all=1&objects_per_page=72&sort=date_added';
// 		} else {
// 			$site_url = $gp_site.'/index'.$p.'.html?view_all=1&objects_per_page=72&sort=date_added';
// 		}

		echo $site_url."<br>";
		curl_setopt($curl, CURLOPT_URL, $site_url);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, TRUE);    //가끔가다가 인증서버에 대한 옵션이 있는데 믿을만 하다면 FALSE설정해도 됨
		// curl_setopt($curl, CURLOPT_USERPWD, "vanchosun:van1158");
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT,5);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,0);
		$response_result = curl_exec($curl);
		curl_close($curl);


		if($response_result){

			if(stristr($gp_site,"paradisemint.com")){

				$Response = explodeCut($response_result,'<!--START: ITEMS-->','<!--END: ITEMS-->');

				/* 상품URL 추출 */
				//([A-Za-z\s0-9\-\.\_]+)\"
				preg_match_all("/<td class\=\"item\" align\=\"center\" height\=\"60\"><a href\=\"([A-Za-z\s0-9\-\.\_]+)/",$Response,$match_url); //		\"\/product\/[\/a-zA-Z0-9_\.\?\=]+\">
				$prd_url = $match_url[1];


				/* 상품주문 최대신청 제한갯수 추출 */
				preg_match_all("/([0-9]+)\s*In Stock/",$Response,$match_jaego); //		\"\/product\/[\/a-zA-Z0-9_\.\?\=]+\">
				$jaego = $match_jaego[1][0];


				/* bak_url 인덱스대로 정보 돌아가면서 입력 */
				for($i = 0; $i < count($prd_url); $i++) {
					$url = "http://paradisemint.com/".$prd_url[$i];

					preg_match_all("/_([0-9]+)\.html/",$url,$match_code);
					$code = "PM_".$match_code[1][0];

					$sel_sql = "	SELECT	*
												FROM		data_url
												WHERE		site	= 'PARADISE'
												AND			url 	= '$url'
					";
					$result = sql_query($sel_sql);
					$row = mysql_fetch_array($result);

					if($row[number] > 0 || $row[number]) {
						$msg = "UPDATE : $url";

						$upd_sql = "UPDATE	data_url	SET
																	it_id	=	'$code',
																	jaego	=	$jaego,
																	mod_date	= now()
												WHERE		url = '$url'
						";
						sql_query($upd_sql);
						if($mode == 'jhw') echo "<pre>".$upd_sql."<pre><br>";


						$upd_sql = "	UPDATE	g5_shop_group_purchase	SET
																	jaego =	$jaego,
																	gp_update_time	= now()
													WHERE		gp_id = '$code'
						";
						sql_query($upd_sql);

						if($mode == 'jhw') echo "<pre>".$upd_sql."<pre><br>";

					} else {
						//		it_id = 'PARADISE_$code',
						$upd_sql = "	INSERT	INTO	data_url	SET
																	it_id	=	'$code',
																	site	=	'PARADISE',
																	cate1 = '$cate1',
																	cate2 = '$cate2',
																	url		=	'$url',
																	jaego = '$jaego',
																	reg_date	= now(),
																	mod_date	= '0000-00-00 00:00:00'
						";
						$msg = "INSERT : ";
						sql_query($upd_sql);
					}

					echo $msg.$url."<br>";
				} // for end

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
	//location.href="gains.geturl.php";
	location.reload();
},$geturl_time);
</script>";

// echo $script;

?>