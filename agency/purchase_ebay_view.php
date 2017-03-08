<?php
include_once('./_common.php');

if (G5_IS_MOBILE) {
    include_once(G5_MOBILE_PATH.'/agency/purchase_ebay_view.php');
    return;
}


include_once(G5_PATH.'/head.php');
?>
<?php
if(!$page)$page = 1;

require_once('DisplayUtils.php');  // functions to aid with display of information

//error_reporting(E_ALL);  // turn on all errors, warnings and notices for easier debugging

$results = '';


$endpoint = 'http://open.api.ebay.com/shopping';  // URL to call
$responseEncoding = 'XML';   // Format of the response


if(!$itemID)alert("값이 넘어오지 않았습니다.");

$results .=  "<h2>EBAY 상품 상세</h2>\n";
// Construct the FindItems call
$apicall = "$endpoint?callname=GetSingleItem"
	 . "&version=515"
	 . "&GLOBAL-ID=EBAY-US"
	 . "&appid=KimSangK-3a18-43b7-b90e-2c395af0d75e" //replace with your app id
	 . "&ItemID=$itemID"
	 . "&siteid=0"
	 . "&IncludeSelector=Details,Description,ItemSpecifics,ShippingCosts"
	 . "&responseencoding=$responseEncoding";

if ($debug) {
  print "GET call = $apicall <br>";  // see GET request generated
}

// Load the call and capture the document returned by the Finding API
$resp = simplexml_load_file($apicall);

// Check to see if the response was loaded, else print an error
// Probably best to split into two different tests, but have as one for brevity

//print_r($resp);

if ($resp) {

	$payGubun = "일반";
	if($resp->Item->ListingType=="Auction" || $resp->Item->ListingType=="Chinese" || $resp->Item->ListingType=="Live")$payGubun = "경매";   // returns Epoch seconds
	
	$timeLeft = getPrettyTimeFromEbayTime($resp->Item->TimeLeft);

	$price = sprintf("%01.2f", $resp->Item->ConvertedCurrentPrice);
	$ship  = sprintf("%01.2f", $resp->Item->ShippingCostSummary->ShippingServiceCost);

	$total = sprintf("%01.2f", ((float)$resp->Item->ConvertedCurrentPrice
				  + (float)$resp->Item->ShippingCostSummary->ShippingServiceCost));
	
function pg_anchor($anc_id) {
    global $default;
    global $item_use_count, $item_qa_count, $item_relation_count;
?>
    <ul class="sanchor">
        <li style="border-left:0;" <?php if ($anc_id == 'inf') echo 'class="sanchor_on"'; ?>><a href="#sit_inf">상품상세정보</a></li>
		<li <?php if ($anc_id == 'qa') echo 'class="sanchor_on"'; ?>><a href="#sit_qa">구매약관동의</a></li>
        <li <?php if ($anc_id == 'use') echo 'class="sanchor_on"'; ?>><a href="#sit_use">상세진행과정보기</a></li>
    </ul>
<?php
}
?>
<div id="sit">

    <?php
	include_once(G5_SHOP_SKIN_PATH.'/purchase_ebay.form.skin.php');

	include_once(G5_SHOP_SKIN_PATH.'/purchase_ebay.info.skin.php');
    ?>

</div>

<?php
}
// If there was no response, print an error
else {
  $results = "<div><b>No items found<b></div>";
}
?>
	
<?php
include_once(G5_PATH.'/tail.php');
?>