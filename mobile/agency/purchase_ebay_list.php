<?php
include_once('./_common.php');
include_once(G5_MSHOP_PATH.'/_head.php');

$g5['title'] = $ca['ca_name'].' 상품리스트';



?>
<link href='<?=G5_URL?>/css/pur_shop.css' rel='stylesheet' type='text/css' />
<?php
if(!$page)$page = 1;

require_once('DisplayUtils.php');  // functions to aid with display of information

//error_reporting(E_ALL);  // turn on all errors, warnings and notices for easier debugging

$results = '';

$endpoint = 'http://svcs.ebay.com/services/search/FindingService/v1';  // URL to call
$responseEncoding = 'XML';   // Format of the response

$priceRange = ($priceRangeMax - $priceRangeMin) / 3;  // find price ranges for three tables
$priceRangeMin =  sprintf("%01.2f", 0.00);
$priceRangeMax = $priceRangeMin;  // needed for initial setup

// OPERATION-NAME=findItemsByKeywords //검색
// keywords=검색

if($ebay_search){
	$findtype = "findItemsByKeywords";
	$keyword = "&keywords=".$ebay_search;
}else{
	$findtype = "findItemsByCategory";
	$keyword = "";
}

$results .=  "<h2>EBAY 상품 리스트</h2>\n";
// Construct the FindItems call
$apicall = "$endpoint?OPERATION-NAME=$findtype"
	 . $keyword
	 . "&SERVICE-VERSION=1.0.0"
	 . "&GLOBAL-ID=EBAY-US"
	 . "&SECURITY-APPNAME=KimSangK-3a18-43b7-b90e-2c395af0d75e" //replace with your app id
	 . "&categoryId=11116"
	 . "&REST-PAYLOAD"
	 . "&paginationInput.entriesPerPage=".$config['cf_write_pages']
	 . "&paginationInput.pageNumber=".$page	 
	 . "&sortOrder=BestMatch"
	 . "&RESPONSE-DATA-FORMAT=$responseEncoding";

if ($debug) {
  print "GET call = $apicall <br>";  // see GET request generated
}

// Load the call and capture the document returned by the Finding API
$resp = simplexml_load_file($apicall);

// Check to see if the response was loaded, else print an error
// Probably best to split into two different tests, but have as one for brevity

if ($resp && $resp->paginationOutput->totalEntries > 0) {

	$total_page = $resp->paginationOutput->totalPages;
	if($total_page>0) $total_page=$total_page;
	
?>

<form name="febaysearch" id="febaysearch" method="POST">
<div>
	<div style="float:left;">
		<h2>이베이 상품 리스트</h2>
	</div>
	<div style="float:right;">
		<input type="text" name="ebay_search" value="<?=$ebay_search?>">
		<input type="submit" value="검색">
	</div>
</div>
</form>

<div class="tbl_head02 tbl_wrap">
    <table>
    <thead id="mhead">
    <tr>
        <th scope="col">상품 이미지</th>
        <th scope="col">상품명</th>
        <th scope="col">상품금액<br />(배송료포함)</th>
        <th scope="col">구매방식</th>
        <th scope="col">남은시간</th>
    </tr>
	</thead>

	<tbody>
	<tr><td style="margin:0;padding:0">
<?
$i=0;
  // If the response was loaded, parse it and build links
  foreach($resp->searchResult->item as $item) {
	if ($item->galleryURL) {
	  $picURL = $item->galleryURL;
	} else {
	  $picURL = "http://pics.ebaystatic.com/aw/pics/express/icons/iconPlaceholder_96x96.gif";
	}
	$link  = $item->viewItemURL;
	$title = $item->title;

	$price = sprintf("%01.2f", $item->sellingStatus->convertedCurrentPrice);
	$ship  = sprintf("%01.2f", $item->shippingInfo->shippingServiceCost);
	$total = sprintf("%01.2f", ((float)$item->sellingStatus->convertedCurrentPrice
				  + (float)$item->shippingInfo->shippingServiceCost));

	// Determine currency to display - so far only seen cases where priceCurr = shipCurr, but may be others
	$priceCurr = (string) $item->sellingStatus->convertedCurrentPrice['currencyId'];
	$shipCurr  = (string) $item->shippingInfo->shippingServiceCost['currencyId'];
	if ($priceCurr == $shipCurr) {
	  $curr = $priceCurr;
	} else {
	  $curr = "$priceCurr / $shipCurr";  // potential case where price/ship currencies differ
	}

	$timeLeft = getPrettyTimeFromEbayTime($item->sellingStatus->timeLeft);
	$endTime = strtotime($item->listingInfo->endTime);   // returns Epoch seconds
	$endTime = $item->listingInfo->endTime;

	$payGubun = "일반";
	if($item->listingInfo->listingType=="Auction")$payGubun = "경매";   // returns Epoch seconds
?>
<form name="forderform<?php echo $i?>" method="post" action="purchase_request.php">
<input type="hidden" name="ebay_title" value="<?php echo $title?>">
<input type="hidden" name="ebay_link" value="<?php echo $link?>">
<input type="hidden" name="ebay_price" value="<?php echo $total?>">
<input type="hidden" name="ebay_gubun" value="<?php echo $payGubun?>">


	<div id="gu_wrap">
		<ul>
			<li id="b1"><img src="<?php echo $picURL?>"></li>
			<li id="b2"><a href="purchase_ebay_view.php?itemID=<?php echo $item->itemId?>" style="margin-left:10px;display:inline-block;"><?php echo $title?></a>
					<div id="sit_ov_btn">
						<a href="purchase_ebay_view.php?itemID=<?php echo $item->itemId?>" id="sit_btn_wish" style="margin-left:10px">상품상세</a>
					</div></li>
			<li id="b3">USD <?php echo $total;?></li>
			<li id="b4"><?php echo $payGubun;?></li>
			<li id="b5"><?php echo $timeLeft;?></li>
				
		</ul>
	</div>

</form>
<?
	$i++;
  }
?>
	</td></tr>
	</tbody>
	</table>
	
</div>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['PHP_SELF']}?$qstr&amp;ebay_search=$ebay_search&amp;page="); ?>

<?php
}
// If there was no response, print an error
else {
  $results = "<div><b>No items found<b></div>";
}
?>

<?php
if ($ca['ca_include_tail'])
    @include_once($ca['ca_include_tail']);
else
    include_once('_tail.php');

echo "\n<!-- {$ca['ca_skin']} -->\n";
?>
