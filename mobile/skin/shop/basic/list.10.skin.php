<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가
?>

<link rel="stylesheet" href="<?php echo G5_MSHOP_SKIN_URL; ?>/style.css">
<script src="<?=G5_JS_URL?>/jquery.fancylist.js"></script>

<!-- 상품진열 10 시작 { list.10.skin -->
<?php

/*경매날짜일시분초 변환*/
function convertStringDateTime($datetime) {

	$y = substr($datetime,0,4);
	$m = substr($datetime,4,2);
	$d = substr($datetime,6,2);
	$h = substr($datetime,8,2);
	$i = substr($datetime,10,2);
	$s = substr($datetime,12,2);

	
	$timestamp = mktime($h,$i,$s,$m,$d,$y);
	
	$str = $y."년 ".$m."월 ".$d."일 ".$h."시 ".$i."분 ".$s."초";
	
	return $str;
}


for ($i=0; $row=sql_fetch_array($result); $i++) {
	if ($i == 0) {
		if ($this->css) {
			echo "<ul id=\"sct_wrap\" class=\"{$this->css}\">\n";
		} else {
			echo "<ul id=\"sct_wrap\" class=\"sct sct_10\">\n";
		}
	}

	echo "<li class=\"sct_li\">\n";

	
	/*제품제목 시작*/
	if ($this->href) echo "<a href=\"{$this->href}{$row['it_id']}\" class=\"sct_a sct_txt\">\n";
	echo "<div class='line1row ellipsis'>".stripslashes($row['it_year'])."</div>\n";
	echo "<div class='line1row ellipsis'>"."<b>".stripslashes($row['it_name'])."</b></div>\n";	
	if ($this->href) echo "</a>\n";
	/*제품제목 끝*/
	
	/*제품이미지 시작*/
	echo "<div class='sct_prd_img'>";
	if ($this->href) echo "<a href=\"{$this->href}{$row['it_id']}\" class=\"sct_a sct_img\">\n";
   	if ($this->view_it_img) echo get_it_image($row['it_id'], $this->img_width, $this->img_height, '', '', stripslashes($row['it_name']))."\n";
	if ($this->href) echo "</a>\n";
	echo "</div>";
		
	echo "<div class='sct_prd_auction'>";
	/* 현재입찰 갯수 it_cnt */
		echo "<div class='sct_prd_auction_box1'>";
			echo "<div class='sct_prd_auction_tit'>현재입찰</div>";
			echo "<div class='sct_prd_auction_val'>".number_format($row['it_cnt']*1)."</div>";
			
		/* 남은 시간 카운팅 정보 it_last_date */
			echo "<div class='sct_prd_auction_tit'>남은 시간</div>";
			echo "<div class='sct_prd_auction_val'>".convertStringDateTime($row['it_last_date'])."</div>";
		echo "</div>";
		
		echo "<div class='sct_prd_auction_box2'>";
		if ($this->view_it_cust_price || $this->view_it_price) {
			echo "<div class=\"sct_cost_tit\">현재입찰가</div>\n";
			echo "<div class=\"sct_cost_val\">\n";	
			if ($this->view_it_cust_price && $row['it_cust_price']) {
				echo "<strike>".display_price($row['it_cust_price'])."</strike><br>";
			}
		
			if ($this->view_it_price) {
				echo display_price(get_price($row), $row['it_tel_inq'])."\n";
			}
			echo "</div>\n";
		}
		echo "</div>";	
		
	echo "</div>";
	
	/* 제품이미지 끝 */
	if ($this->view_it_icon) echo "<div class=\"sct_icon\">".item_icon($row)."</div>\n";
	if ($this->view_it_id) echo "<span class=\"sct_id\">&lt;".stripslashes($row['it_id'])."&gt;</span>\n";

	//if ($this->view_it_basic && $row['it_basic']) echo "<div class=\"sct_basic\">".stripslashes($row['it_basic'])."</div>\n";
	
	if ($this->href) {
		echo "</a>\n";
	}

	echo "</li>\n";
}

if ($i > 0) echo "</ul>\n";

if($i == 0) echo "<p class=\"sct_noitem\">등록된 상품이 없습니다.</p>\n";
?>
<!-- } 상품진열 10 끝 -->

<script>
$(function() {
	/* sct_li엘리먼트에 inline 스타일 적용하는 함수 */
	$("#sct_wrap").fancyList("li.sct_li", "sct_clear");
});
</script>