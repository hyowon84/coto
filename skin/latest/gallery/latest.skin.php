<?
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php'); //썸네일 라이브러리 

$latest_img_width = 400; //이미지 가로길이
$latest_img_height = 220; //이미지 세로길이
?>

<div class="lt_gallery">
    <!--<strong class="lt_gallery_title"><a href="<?=G5_BBS_URL?>/board.php?bo_table=<?=$bo_table?>"><?=$bo_subject?></a></strong>-->
    <ul>
    <? for ($i=0; $i<count($list); $i++) { ?>
<?
$lt_thumb = get_list_thumbnail($bo_table, $list[$i]['wr_id'], $latest_img_width, $latest_img_height);
if($lt_thumb['src']) {
	$img_content = '<img alt="" src="'.$lt_thumb['src'].'" width="'.$latest_img_width.'" height="'.$latest_img_height.'">';
} else {
	$img_content = '<span style="width:'.$latest_img_width.'px;height:'.$latest_img_height.'px">no image</span>';
}
?>		
		<p style="border:1px solid red">111111111111111</p>
        <li style="width:<?=$latest_img_width?>px; height:<?=$latest_img_height?>px;position:relative">
			
            <?
            //echo $list[$i]['icon_reply']." ";
            echo "<a href=\"".$list[$i]['href']."\" title=\"".$list[$i]['wr_subject']."\">";
            if ($list[$i]['is_notice'])
                echo "<strong>".$list[$i]['subject']."</strong>";
            else
            	echo $img_content;
			echo "<h6>".$list[$i]['subject']."</h6>";

            if ($list[$i]['comment_cnt'])
                echo $list[$i]['comment_cnt'];

            echo "</a>";

            // if ($list[$i]['link']['count']) { echo "[{$list[$i]['link']['count']}]"; }
            // if ($list[$i]['file']['count']) { echo "<{$list[$i]['file']['count']}>"; }

            if (isset($list[$i]['icon_new'])) echo " " . $list[$i]['icon_new'];
            if (isset($list[$i]['icon_hot'])) echo " " . $list[$i]['icon_hot'];
            if (isset($list[$i]['icon_file'])) echo " " . $list[$i]['icon_file'];
            if (isset($list[$i]['icon_link'])) echo " " . $list[$i]['icon_link'];
            if (isset($list[$i]['icon_secret'])) echo " " . $list[$i]['icon_secret'];
            ?>
        </li>
    <? } ?>
    <? if (count($list) == 0) { //게시물이 없을 때 ?>
    <li>게시물이 없습니다.</li>
    <? } ?>
    </ul>
    <!--<div class="lt_gallery_more"><a href="<?=G5_BBS_URL?>/board.php?bo_table=<?=$bo_table?>"><span class="sound_only"><?=$bo_subject?></span>더보기</a></div>-->
</div>
