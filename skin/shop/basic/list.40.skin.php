<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 관련상품 스킨은 사품을 한줄에 하나만 표시하며 해당 상품에 관련상품이 등록되어 있는 경우 기본으로 7개까지 노출합니다.
?>

<link rel="stylesheet" href="<?php echo G5_SHOP_SKIN_URL; ?>/style.css">

<!-- 상품진열 40 시작 { -->
<?php
for ($i=1; $row=sql_fetch_array($result); $i++) {

    $href = G5_SHOP_URL.'/item.php?it_id='.$row['it_id'];
    if ($list_mod >= 2) { // 1줄 이미지 : 2개 이상
        if ($i%$list_mod == 0) $sct_last = ' sct_last'; // 줄 마지막
        else if ($i%$list_mod == 1) $sct_last = ' sct_clear'; // 줄 첫번째
        else $sct_last = '';
    } else { // 1줄 이미지 : 1개
        $sct_last = ' sct_clear';
    }

    if ($i == 1) {
        if ($this->css) {
            echo "<ul class=\"{$this->css}\">\n";
        } else {
            echo "<ul class=\"sct sct_40\">\n";
        }
   }

    $list_top_pad = 5;
    $list_right_pad = 5;
    $list_bottom_pad = 5;
    //$list_left_pad = $this->img_width + 5;
	$list_left_pad = 5;
    $list_real_width = 740;
    //$list_width = $list_real_width - $list_right_pad - $list_left_pad;
	$list_width = 260;
    //$list_height = $this->img_height - $list_top_pad - $list_bottom_pad;
	$list_height = 372;

	if($i % 3 == 0){
		$mar_left = "margin:7px 0 0 0;";
	}else{
		$mar_left = "margin:7px 7px 0 0;";
	}

    echo "<li class=\"sct_li{$sct_last}\" style=\"float:left;".$mar_left."width:{$list_width}px;height:{$list_height}px;background:#fff;\">\n";

	echo "<div style='text-align:center;background:#efeeee;'>";
	if($row[auc_status] == 2 || $row[it_price_type] == "A"){
		if ($this->auc_href) {
			//echo "<a href=\"{$this->auc_href}{$row['it_id']}\" class=\"sct_a sct_img\">\n";
			echo "<a href=\"{$this->auc_href}{$row['it_id']}\">\n";
		}

		$auc_row = sql_fetch("select * from g5_shop_auction where it_id='".$row[it_id]."' order by no desc limit 0, 1 ");

		$auc_stat = "경매상품";
		if($auc_row[it_last_bid]){
			$price = number_format($auc_row[it_last_bid])." 원";
		}else{
			$price = display_price(get_price($row), $row['it_tel_inq']);
		}
	}else{
		if ($this->href) {
			//echo "<a href=\"{$this->href}{$row['it_id']}\" class=\"sct_a sct_img\">\n";
			echo "<a href=\"{$this->href}{$row['it_id']}\">\n";
		}

		if($row[it_price_type] == "Y" || $row[it_price_type] == "U"){
			$auc_stat = "실시간상품";
		}else{
			$auc_stat = "일반상품";
		}
		$price = display_price(get_price($row), $row['it_tel_inq']);
	}

    if ($this->view_it_img) {
        echo get_it_image($row['it_id'], 260, 260, '', '', stripslashes($row['it_name']))."\n";
    }

    if ($this->href) {
        echo "</a>\n";
    }

    if ($this->view_it_icon) {
        echo "<span class=\"sct_icon\" style=\"top:0;left:0;\">".item_icon($row)."</span>\n";
    }

    if ($this->view_it_id) {
        echo "<span class=\"sct_id\">&lt;".stripslashes($row['it_id'])."&gt;</span>\n";
    }

	echo "</div>";
	echo "<div>";
	echo "<div style='padding:10px 5px 3px 10px;height:70px;'>";

    if ($this->href) {
        echo "<a href=\"{$this->href}{$row['it_id']}\" class=\"sct_a sct_txt\" style='height:30px;font-weight:bold;'>\n";
    }

    if ($this->view_it_name) {
		echo cut_str($row['it_year'], 30)."</br>";
        echo stripslashes($row['it_name'])."\n";
    }

    if ($this->href) {
        echo "</a>\n";
    }

    if ($this->view_it_basic && $row['it_basic']) {
        //echo "<div class=\"sct_basic\">".stripslashes($row['it_basic'])."</div>\n";
    }

    if ($this->view_it_cust_price || $this->view_it_price) {

        echo "<div class=\"sct_cost\" style='text-align:right;color:#ff4e00;font-size:17px;'>\n";

        if ($this->view_it_cust_price && $row['it_cust_price']) {
            echo "<strike>".display_price($row['it_cust_price'])."</strike>\n";
        }

        if ($this->view_it_price) {
            echo $price."\n";
        }

        echo "</div>\n";

    }


    if ($this->view_sns) {
        $sns_url  = G5_SHOP_URL.'/item.php?it_id='.$row['it_id'];
        $sns_title = get_text($row['it_name']).' | '.get_text($config['cf_title']);
        echo "<div class=\"sct_sns\">";
        echo get_sns_share_link('facebook', $sns_url, $sns_title, G5_SHOP_SKIN_URL.'/img/sns_fb.png');
        echo get_sns_share_link('twitter', $sns_url, $sns_title, G5_SHOP_SKIN_URL.'/img/sns_twt.png');
        echo get_sns_share_link('googleplus', $sns_url, $sns_title, G5_SHOP_SKIN_URL.'/img/sns_goo.png');
        echo "</div>\n";
    }

	echo "</div>";
	echo "<div style='clear:both;border-bottom:1px #cfcfcf solid;margin:0 5px 0 5px;'></div>";
	echo "<div style='clear:both;padding:7px;'>";

	echo $auc_stat;

	echo "</div>";
	echo "</div>";

    // 관련상품
    //echo "<div class=\"sct_rel\">".relation_item($row['it_id'], 70, 0, 5)."</div>\n";

    echo "</li>\n";
}

if ($i > 1) echo "</ul>\n";

if($i == 1) echo "<p class=\"sct_noitem\">등록된 상품이 없습니다.</p>\n";
?>
<!-- } 상품진열13 끝 -->
