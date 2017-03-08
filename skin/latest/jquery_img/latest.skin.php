<?
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가 

//옵션분리
list($width, $height, $border_width, $border_color, $img_link, $btn_view, $rand) = explode("|", $options);

if(!$width) $width = 746;
if(!$height) $height = 328;
//if(!$border_width) $border_width = 1;
//if(!$border_color) $border_color = "#8c91a1";
//if($btn_view != "block" && $btn_view != "none") $btn_view = "none";

//기존 추출 지우기
unset($list);

// 이미지 뽑아오기
if(!$rows) $rows=5; //노출 이미지 갯수

$slideRes = sql_query("SELECT * FROM g5_write_$bo_table AS a LEFT JOIN g5_board_file AS b ON a.wr_id=b.wr_id WHERE b.bo_table='$bo_table' ORDER BY a.wr_id DESC LIMIT $rows");

$i = 0;
while($slideRow = mysql_fetch_array($slideRes)){

	//이미지가 아니면 그냥 통과
	if(!preg_match("/\.(jpg|gif|png)$/i", $slideRow[bf_file])) continue;

	switch($img_link) {
		case 'link'	: $post_link = $slideRow[wr_link1]; break;
		case 'post'	: $post_link = G5_BBS_URL."/board.php?bo_table=".$bo_table."&wr_id=".$slideRow[wr_id]; break;
		default		: $post_link = ""; break;

	}

	if($post_link) {
		$target = "";
		if($img_link == "link") $target = "target='_blank'";
		$list[$i] = "<a href='$post_link' {$target}><img src='./data/file/".$bo_table."/".$slideRow[bf_file]."' width='$width'  height='$height' border=0></a>";
	} else {
		$list[$i] = "<a><img src='./data/file/".$bo_table."/".$slideRow[bf_file]."' width='$width'  height='$height' border=0></a>";
	}

	$i++;
}

//랜덤 출력
if($rand == "rand") shuffle($list);

//버튼 위치
$btn_left =(int)($width - 25 * $i)/2;

?>

<link rel="stylesheet" href="<?php echo $latest_skin_url ?>/latest.jquery_img.css" type="text/css">

<style>
	.jquery_img { width:<?=$width?>px; height:<?=$height?>px; border:<?=$border_width?>px solid <?=$border_color?>; }
	.exhibition { width:<?=$width?>px; height:<?=$height?>px; position:relative; z-index:1; }
	.theme-default .nivo-controlNav { left:<?=$btn_left?>px; bottom:4px; display:<?=$btn_view?>;}
</style>

<script type="text/javascript" src="<?php echo $latest_skin_url ?>/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="<?php echo $latest_skin_url ?>/jquery.nivo.slider.js"></script>

<table border=0 cellpadding=0 cellspacing=0>
<tr><td class="jquery_img">
	<div class="exhibition">
		<div class="slider-wrapper theme-default">
   			<div id="slider" class="nivoSlider">
				<? for($i=0;$i<count($list);$i++) { echo $list[$i]; } ?>
			</div>
		</div>
	</div>

	<script type="text/javascript">
	$(window).load(function() {
		$('#slider').nivoSlider();
	});
	</script>
</td></tr>
</table>