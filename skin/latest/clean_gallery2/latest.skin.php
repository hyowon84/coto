<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

$imgwidth = 120; //표시할 이미지의 가로사이즈
$imgheight = 88; //표시할 이미지의 세로사이즈
?>

<style>
#oneshot { position:relative;margin:0 0 0 -5px;}
#oneshot .la_title{position:absolute; left:0; top:0; z-index:100; background:#000; padding:5px; font-size:1em; color:#fff;margin:0 0 0 5px;filter:alpha(opacity=50);opacity:.5;}
#oneshot .img_set{width:<?php echo $imgwidth ?>px; height:<?php echo $imgheight ?>px; background:#fafafa;padding:0;}
#oneshot .subject_set{width:<?php echo $imgwidth - 13 ?>px; height:58px; padding:5px 10px 10px 2px; z-index:1; bottom:0; left:0;text-align:left;border:1px solid #dde2e4}
#oneshot .subject_set .sub_title{color:#333;height:17px;overflow:hidden;padding:3px 0 0 5px;font-size:0.9em;font-family:NanumBarunGothic;}
#oneshot .subject_set .sub_content{color:#8c8a8a;height:30px;overflow:hidden;padding:3px 0 0 5px;font-family:NanumGothic;font-size:0.9em}


#oneshot ul {list-style:none;clear:both;margin:0;padding:0;}
#oneshot li{float:left;list-style:none;text-decoration:none;padding:0 0 0 5px}
.subject_set  a:link, a:visited {color:#333;text-decoration:none}
.subject_set  a:hover, a:focus, a:active {color:#e60012;text-decoration:none}


.als-container {
	position: relative;
	width: 100%;
	margin: 0px auto;
}

.als-viewport {
	position: relative;
	overflow: hidden;
	margin: 0px auto;
	padding:0 0 0 7px;
}

.als-wrapper {
	position: relative;
	list-style: none;
}

.als-item {
	position: relative;
	display: block;
	text-align: center;
	cursor: pointer;
	float: left;
}

.als-prev, .als-next {
	width:20px;
	height:20px;
	font-size:17px;
	position: absolute;
	cursor: pointer;
	clear: both;
	z-index:100;
}

#lista1 {
	width:100%;
	margin: 10px auto 10px auto;
}

#lista1 .als-item {
	margin: 0px 0px;
	min-height: 140px;
	min-width: 125px;	
}

#lista1 .als-item img {
	position: relative;
	display: block;
	vertical-align: middle;
	margin-bottom: 8px;
}

#lista1 .als-prev, #lista1 .als-next {
	top: 58px;
	width: 15px;
	height: 22px;
}

#lista1 .als-prev {
	left: 90px;
}

#lista1 .als-next {
	right: 100px;
}

/* 폰트불러오기 */
@font-face {font-family:'NanumBarunGothic';src: url('<?php echo $latest_skin_url ?>/NanumBarunGothic.eot');}
@font-face {font-family:'NanumGothic';src: url('<?php echo $latest_skin_url ?>/NanumGothic.eot');}

</style>

<script type="text/javascript" src="<?=G5_URL?>/js/jquery.alsEN-1.0.min.js"></script>


<div id="oneshot">

	<div id="lista1">
		<span class="als-prev" style="margin-left:-85px;"><</span>
		<div class="als-viewport">
			<ul class="als-wrapper">
			<?php for ($i=0; $i<count($list); $i++) { ?>
				<li class="als-item">
					<div class="img_set">
						<!--<a href="<?php //echo $list[$i]['href'] ?>">-->
						<a href="#">
							<?php                
							$thumb = get_list_thumbnail($bo_table, $list[$i]['wr_id'], $imgwidth, $imgheight);    					            
							if($thumb['src']) {
								$img_content = '<img class="img_left" src="'.$thumb['src'].'" alt="'.$list[$i]['subject'].'" width="'.$imgwidth.'" height="'.$imgheight.'">';
							} else {
								$img_content = 'NO IMAGE';
							}                
							echo $img_content;
							?>
						</a>
					</div>
					<div class="subject_set">
						<div class="sub_title"><a href="<?php echo $list[$i]['href'] ?>"><?php echo cut_str($list[$i]['subject'], 8, "..") ?></a></div>
						<div class="sub_content"><?php echo get_text(cut_str(strip_tags($list[$i][wr_content]), 15, '...' )) ?></div>
					</div>
				</li>
			<?php } ?>
			</ul>
		</div>
		<span class="als-next" style="margin-right:-110px">></span>
	</div>
</div>
<div style="clear:both;"></div>
<script type="text/javascript">
$(document).ready(function(){
	$("#lista1").als({
		visible_items: <?php echo count($list);?>,
		scrolling_items: 4,
		orientation: "horizontal",
		circular: "yes",
		autoscroll: "no",
		interval: 5000,
		direction: "right"
	});
});
</script>