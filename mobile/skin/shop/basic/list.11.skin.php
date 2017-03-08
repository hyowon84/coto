<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가
?>

<link rel="stylesheet" href="<?php echo G5_MSHOP_SKIN_URL; ?>/style.css">
<script src="<?=G5_JS_URL?>/jquery.fancylist.js"></script>

<!-- 상품진열 10 시작 { list.11.skin -->
<?php
for ($i=0; $row=sql_fetch_array($result); $i++) {
    if ($i == 0) {
        if ($this->css) {
            echo "<ul id=\"sct_wrap\" class=\"{$this->css}\">\n";
        } else {
            echo "<ul id=\"sct_wrap\" class=\"sct sct_11\">\n";
        }
    }

    echo "<li class=\"sct_li\">\n";

// 	echo "<div>";

//     if ($this->href) {
//         echo "<a href=\"{$this->href}{$row['it_id']}&ca_id=".$_GET[ca_id]."\" class=\"sct_a sct_img\" style='margin:-5px 0 0 2px;'>\n";
//     }
    //     if ($this->view_it_img) {
    //         echo get_it_image($row['it_id'], $this->img_width, $this->img_height, '', '', stripslashes($row['it_name']))."\n";
    //     }
    
    //     if ($this->href) {
    //         echo "</a>\n";
    //     }
// 	if ($this->view_it_icon) {
//         echo "<div class=\"sct_icon2\">".item_icon($row)."</div>\n";
//     }

    /*제품이미지 시작*/
    echo "<div class='sct_prd_img'>";
	    if ($this->href) echo "<a href=\"{$this->href}{$row['it_id']}\" class=\"sct_a sct_img\">\n";
	    if ($this->view_it_img) echo get_it_image($row['it_id'], $this->img_width, $this->img_height, '', '', stripslashes($row['it_name']))."\n";
	    if ($this->href) echo "</a>\n";
    echo "</div>";

    echo "<div class='sct_prd_contents'>";
	    /*제품제목 시작*/
	    if ($this->href) echo "<a href=\"{$this->href}{$row['it_id']}\" class=\"sct_a sct_txt\">\n";
	    echo "<div class='line1row ellipsis'>".stripslashes($row['it_year'])."</div>\n";
	    echo "<div class='line1row ellipsis'>"."<b>".stripslashes($row['it_name'])."</b></div>\n";
	    if ($this->href) echo "</a>\n";
	    /*제품제목 끝*/
	
	    echo "<div class=\"sct_cost_val\">\n";
		    if ($this->view_it_cust_price && $row['it_cust_price']) {
		    	//echo "<strike>".display_price($row['it_cust_price'])."</strike><br>\n";
		    }
		    
		    if ($this->view_it_price) {
		    	echo display_price(get_price($row), $row['it_tel_inq'])."\n";
		    }
	    echo "</div>\n";
    echo "</div>";
    
//     if ($this->view_it_cust_price || $this->view_it_price) {
//     	echo "<div class=\"sct_cost\">\n";
//     	if ($this->view_it_cust_price && $row['it_cust_price']) {
//     		//echo "<strike>".display_price($row['it_cust_price'])."</strike>\n";
//     	}
//     	if ($this->view_it_price) {
//     		echo display_price(get_price($row), $row['it_tel_inq'])."\n";
//     	}
//     	echo "</div>\n";
//     }
    
    
//     if ($this->view_it_id) {
//         echo "<span class=\"sct_id\" >&lt;".stripslashes($row['it_id'])."&gt;</span>\n";
//     }

//     if ($this->href) {
//         echo "<a href=\"{$this->href}{$row['it_id']}\" class=\"sct_a sct_txt\">\n" ;
//     }

//     if ($this->view_it_name) {
//         echo cut_str(stripslashes($row['it_name']), 20)."\n";
//     }

//     if ($this->href) {
//         echo "</a>\n";
//     }

    //if ($this->view_it_basic && $row['it_basic']) {
        //echo "<div class=\"sct_basic\">".stripslashes($row['it_basic'])."</div>\n";
    //}

    /*
	if ($this->view_sns) {
        $sns_url  = G5_SHOP_URL.'/item.php?it_id='.$row['it_id'];
        $sns_title = get_text($row['it_name']).' | '.get_text($config['cf_title']);
        echo "<div class=\"sct_sns\">";
        echo get_sns_share_link('facebook', $sns_url, $sns_title, G5_MSHOP_SKIN_URL.'/img/sns_fb.png');
        echo get_sns_share_link('twitter', $sns_url, $sns_title, G5_MSHOP_SKIN_URL.'/img/sns_twt.png');
        echo get_sns_share_link('googleplus', $sns_url, $sns_title, G5_MSHOP_SKIN_URL.'/img/sns_goo.png');
        echo "</div>\n";
    }
	*/

// 	echo "<div class=\"sct_status\">";
// 	if($row['auc_status'] == 2){
// 		echo "경매상품";
// 	}else{
// 		echo "일반상품";
// 	}
// 	echo "</div>\n";
//     if ($this->href) {
//         echo "</a>\n";
//     }

// 	echo "</div>";

    echo "</li>\n";
}

if ($i > 0) echo "</ul>\n";

if($i == 0) echo "<p class=\"sct_noitem\">등록된 상품이 없습니다.</p>\n";
?>
<!-- } 상품진열 10 끝 -->

<script>
/*$(function() {
	$("#sct_wrap").fancyList("li.sct_li", "sct_clear");
});*/
</script>