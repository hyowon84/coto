<?
include_once("./_common.php");

require_once('DisplayUtils.php');  // functions to aid with display of information

error_reporting(E_ALL);  // turn on all errors, warnings and notices for easier debugging

$endpoint = 'http://open.api.ebay.com/shopping';  // URL to call
$responseEncoding = 'XML';   // Format of the response

$apicall = "$endpoint?callname=GetSingleItem"
	 . "&version=515"
	 . "&GLOBAL-ID=EBAY-US"
	 . "&appid=KimSangK-3a18-43b7-b90e-2c395af0d75e" //replace with your app id
	 . "&ItemID=$itemID"
	 . "&siteid=0"
	 . "&IncludeSelector=Details,Description,ItemSpecifics,ShippingCosts"
	 . "&responseencoding=$responseEncoding";

$resp = simplexml_load_file($apicall);

$timeLeft = getPrettyTimeFromEbayTime($resp->Item->TimeLeft);

echo $timeLeft;
?>