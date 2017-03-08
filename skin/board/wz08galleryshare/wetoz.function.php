<?php

/*************************************************************************
** 짧은주소
*************************************************************************/
	function googl_short_url($long_url) {
		$googl_url = "https://www.googleapis.com/urlshortener/v1/url";
		$post_data = array('longUrl' => $long_url);
		$headers = array('Content-Type:application/json');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $googl_url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($post_data));
		$result = curl_exec($ch);
		curl_close($ch);
		//print_r2($result);
		$obj = json_decode($result);
		$short_url = $obj->{'id'};
		return $short_url;
	}


	function array_sort($arr, $dimension) {
		if($dimension)
		{
			for($i = 0; $i < sizeof($arr); $i++) {
				array_unshift($arr[$i], $arr[$i][$dimension]);
			}
				@sort($arr);
				for($i = 0; $i < sizeof($arr); $i++) {
					array_shift($arr[$i]);
				}
		} else {
				@sort($arr);
		}
		return $arr;
	}
?>