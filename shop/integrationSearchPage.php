<?
include_once('./_common.php');

if (G5_IS_MOBILE) {
    include_once(G5_MSHOP_PATH.'/head.php');
} else {
    include_once('./_head.php');
}

$searchWord = $_REQUEST['s'];
$pageNum = $_REQUEST['p'];

if($searchWord != "" && $searchWord != "undefined" ) {
    $result = getItemListUsingIntegrationSearching($searchWord, $pageNum, 10);
}

?>
<script>
    var results = <?=$result?>;
    var offset = 10;

    $(function() {
        var searchStr = "<?=$searchWord?>";
        $('.searchBarInput').val(searchStr);

        if(results.result.total == 0) {
            $("#content").append("<p class='noItem'>검색결과가 없습니다.</p>");
        } else {
            $.each(results.result.item, function(num, value) {
                var item =
                    '<div class="searchResultItem" data-gpid="' + value.it_id + '" data-caid="' + value.ca_id + '">' +
                    '<img src=' + decodeURIComponent(value.it_img).replace("+", "%20") + '>' +
                    '<p class="categoryName">' + returnCategoryName(value.ca_id) + '</p>' +
                    '<p class="productName">' + value.it_name + '</p>' +
                    '</div>';

                $("#content").append(item);
            });
        }

        $("#content .searchResultItem").click(function() {
            var gpId = $(this).data("gpid");
            var caId = $(this).data("caid");
            location.href = "/shop/grouppurchase.php?gp_id=" + gpId + "&ca_id=" + caId;
        });

        $('#page-selection').bootpag({
            total: Math.round(results.result.total/offset),
            page:<?=$pageNum?>,
            maxVisible: 6
        }).on("page", function(event, num){
            location.href = "/shop/integrationSearchPage.php?s=" + searchStr + "&p=" + num;
        });
    });

    function returnCategoryName(caId) {
        var returnStr;
        switch(caId) {
            case "2010": returnStr = "APMEX"; break;
            case "2020": returnStr = "GAINESVILLE"; break;
            case "2030": returnStr = "MCM"; break;
            case "2040": returnStr = "SCOTTS DALE"; break;
            case "2050": returnStr = "OTHER DEALER"; break;
        }
        return returnStr;
    }
</script>

<div>
    <div id="content"></div>
    <div id="page-selection"></div>
</div>

<?php
if (G5_IS_MOBILE) {
    include_once(G5_MSHOP_PATH.'/tail.php');
} else {
    include_once('./_tail.php');
}
?>
