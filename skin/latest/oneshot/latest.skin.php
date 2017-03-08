<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

$imgwidth = 310; //표시할 이미지의 가로사이즈
$imgheight = 160; //표시할 이미지의 세로사이즈
?>
<link rel="stylesheet" href="<?php echo $latest_skin_url ?>/style.css">

<div id="oneshot">
<div style="position:absolute; left:0; top:0; z-index:100; background:#4266b2; padding:5px; font-size:11px; color:#fff;"><?php echo $bo_subject ?></div>
<?php for ($i=0; $i<count($list); $i++) { ?>	
	<div style="width:<?php echo $imgwidth ?>px; height:<?php echo $imgheight ?>px; background:#fafafa;padding:0;">
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
    <div style="width:<?php echo $imgwidth - 20 ?>px; height:50px; background:#000000; padding:10px; z-index:1; bottom:0; left:0;">
    <ul style="list-style:none; display:block; padding:0; margin:0;">
    	<li><strong><a href="<?php echo $list[$i]['href'] ?>" style="color:#fff; font-size:15px; letter-spacing:-0.06em;"><?php echo cut_str($list[$i]['subject'], 20, "..") ?></a></strong></li>
        <li style="margin-top:5px;"><a href="<?php echo $list[$i]['href'] ?>" style="color:#888; font-size:11px; letter-spacing:-0.06em;"><?php echo get_text(cut_str(strip_tags($list[$i][wr_content]), 65, '...' )) ?></a></li>
    </ul>
    </div>
<?php } ?>
</div>