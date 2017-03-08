<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

$imgwidth = 130; //표시할 이미지의 가로사이즈
$imgheight = 160; //표시할 이미지의 세로사이즈
?>

<style>
#oneshot1 { position:relative;margin:0 0 0 -5px;}
#oneshot1 .la_title1{position:absolute; left:0; top:0; z-index:100; background:#000; padding:5px; font-size:1em; color:#fff;margin:0 0 0 5px;filter:alpha(opacity=50);opacity:.5;}
#oneshot1 .img_set1{float:left;width:<?php echo $imgwidth ?>px; height:<?php echo $imgheight ?>px; background:#fafafa;padding:0;}
#oneshot1 .subject_set1{float:left;width:90px; height:58px; padding:5px 10px 10px 10px; z-index:1; bottom:0; left:0;}

#oneshot1 .subject_set1 .sub_title2{position:relative;color:#475055;overflow:hidden;padding:3px 0 0 0;font-size:1.2em;width:100px;}

#oneshot1 .subject_set1 .sub_title1{color:#475055;overflow:hidden;padding:3px 0 0 0;font-size:1.2em;width:180px;}
#oneshot1 .subject_set1 .sub_content1{color:#8c8a8a;height:120px;overflow:hidden;padding:10px 0 0;width:100px;font-size:11px}

#oneshot1 ul {float:left;list-style:none;margin:0;padding:0;width:270px;}
#oneshot1 li{float:left;list-style:none;text-decoration:none;padding:0 0 0 5px}
.subject_set1  a:link, a:visited {color:#333;text-decoration:none}
.subject_set1  a:hover, a:focus, a:active {color:#e60012;text-decoration:none}

/* 폰트불러오기 */
@font-face {font-family:'NanumBarunGothic';src: url('<?php echo $latest_skin_url ?>/NanumBarunGothic.eot');}
@font-face {font-family:'NanumGothic';src: url('<?php echo $latest_skin_url ?>/NanumGothic.eot');}

</style>

<div id="oneshot1">
	<ul style="width:250px;">
	<?php
	$num = 1;

	for ($i=0; $i<$num; $i++) {
	?>
		<li style="float:left;width:250px;">
			<div class="img_set1">
				<a href="<?php echo $list[$i]['href'] ?>">
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
			<div class="subject_set1">
				<div class="sub_title2"><a href="<?php echo $list[$i]['href'] ?>"><?php echo cut_str($list[$i]['subject'], 12, "..") ?></a></div>
				<div class="sub_content1">
					<?php echo get_text(cut_str(strip_tags($list[$i][wr_content]), 70, '...' )) ?></br>
					<div style="color:#ea6060;margin:10px 0 0 0;cursor:pointer;font-szie:1.0em" onclick="goto_url('<?=G5_URL?>/bbs/board.php?bo_table=<?=$bo_table?>');">more</div>
					<div style="font-szie:10px"><?php echo str_replace("-", ".", substr($list[$i][wr_datetime], 0, 10)); ?></div>
				</div>
			</div>
		</li>
	<?php
	}
	?>
	</ul>
	<ul>
	<?php
	for ($i=$num; $i<count($list); $i++) {
	?>
		<li style="height:75px;padding:0 0 10px 0;">
			<div class="img_set1" style="width:75px;height:75px;">
				<a href="<?php echo $list[$i]['href'] ?>">
					<?php                
					$thumb = get_list_thumbnail($bo_table, $list[$i]['wr_id'], $imgwidth, $imgheight);    					            
					if($thumb['src']) {
					$img_content = '<img class="img_left" src="'.$thumb['src'].'" alt="'.$list[$i]['subject'].'" width="75px" height="75px">';
					} else {
					$img_content = 'NO IMAGE';
					}                
					echo $img_content;
					?>
				</a>
			</div>
			<div class="subject_set1" style="width:120px;height:16px;">
				<div class="sub_title1"><a href="<?php echo $list[$i]['href'] ?>"><?php echo cut_str($list[$i]['subject'], 50, "..") ?></a></div>
				<div class="sub_content1" style="width:150px;height:40px;"><?php echo str_replace("-", ".", substr($list[$i][wr_datetime], 0, 10)); ?></div>
			</div>
		</li>
	<?php
	}
	?>
	</ul>
</div>
<div style="clear:both;"></div>
