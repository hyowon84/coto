<?
if (!defined('_GNUBOARD_')) exit; //개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

$set_value = explode("/","397/220/2");
$thumb_width = $set_value[0]; //썸네일 가로 크기
$thumb_height = $set_value[1]; //썸네일 세로 크기
$bach72_width = ($thumb_width +10 ) * $set_value[2] -10 ; //전체 가로 크기
?>

<style>
.bach72_img_thumb {position:relative;width:<?=$bach72_width?>px}
.bach72_title{display:block;padding:10px 0}
.bach72_title a:hover {color:#000}
.bach72_thumb ul {margin:0;padding:0;list-style:none;width:<?=$bach72_width?>px;zoom:1}
.bach72_thumb ul:after{display:block;visibility:hidden;clear:both;content:""}
.bach72_thumb li {position:relative;float:left;}

.bach72_img {margin:0 6px 6px 0}
.bach72_new {z-index:5;position:absolute;top:0;left:0;width:27px;height:10px;background:url(<?=$latest_skin_url?>/img/icon_new.gif) no-repeat 3px 3px;overflow:hidden}
.bach72_thumb_front {z-index:1;position:absolute;top:0;left:0;width:<?=$thumb_width?>px;height:<?=$thumb_height?>px;overflow:hidden;cursor:pointer}
.bach72_thumb_back {width:<?=$thumb_width?>px;height:<?=$thumb_height?>px;overflow:hidden}
.bach72_thumb_subject {display:block;padding: 5px 0 !important;width:<?=$thumb_width?>px;;font-size:0.95em;height:50px;overflow:hidden;background:#efefef}
.bach72_thumb_subject:link,
.bach72_thumb_subject:visited,
.bach72_thumb_subject:active {display:block;color:#777;font-size:0.95em;text-decoration:none;white-space:nowrap}
.bach72_thumb_subject:hover {color:#000}
.bach72_no_list {padding-top:10px;height:<?=$thumb_height?>px;color:#777;text-align:center}
.bach72_more {position:absolute;top:10px;right:0}
</style>

<div class="bach72_img_thumb" >
    <!--<strong class="bach72_title"><a href="<?=G5_BBS_URL?>/board.php?bo_table=<?=$bo_table?>"><?=$bo_subject?></a></strong>-->
    <div class="bach72_thumb" >
        <? if (!count($list) == 0) { ?>
        <ul style="margin:0;padding:0;">
            <?
            for ($i = 0; $i < count($list); $i++) {
                $noimg = $latest_skin_url.'/img/_noimg.gif';
                $thumb = get_list_thumbnail($bo_table, $list[$i]['wr_id'], $thumb_width, $thumb_height);

                if($thumb['src']) {
                    $img_src = $thumb['src'];
                } else {
                    $img_src = $noimg;
                }
                $img_alt = $thumb['alt'];

                if($i>0 && ($i%$set_value[2]) == ($set_value[2] - 1)) {
                    $li_class = '';
                } else {
                    $li_class = ' class="bach72_img" style="margin:0 6px 6px 0;" ';
                }
				$mem_row = sql_fetch("select * from {$g5[member_table]} where mb_id='".$list[$i][mb_id]."' ");
            ?>
			
            <li<?=$li_class?> >
		
				<div class="bach72_thumb_subject">
					<p style="font-size:12px;margin-left:25px;font-weight:bold;padding:3px 0 1px 0;font-style:italic">Photo by <?=$mem_row[mb_nick]?></p>
					<p style="font-size:14px;margin-left:25px;width:350px;font-style:italic"><?=$list[$i]['subject'] ?></p>					
				</div>
				<div style="clear:both;position:relative">
					<a href="<?=G5_URL?>/bbs/board.php?bo_table=photo_gallery" >
						
						<!--<? if ($list[$i]['icon_new']) { ?><span class="bach72_new"></span><? } ?>-->
						
						<img src="<?= $img_src?>" alt="<?=$img_alt?>" width="<?= $thumb_width?>" height="<?= $thumb_height?>" class="bach72_thumb_back">
						<!--<span class="bach72_thumb_subject"><?//=$list[$i]['subject'] ?></span>-->
					</a>
				</div>
            </li>
            <? } ?>
        </ul>
        <? } else { ?>
        <p class="bach72_no_list">게시물이 없습니다.</p>
        <? } ?>
    </div>
    <!--<div class="bach72_more"><a href="<?=G5_BBS_URL?>/board.php?bo_table=<?=$bo_table?>"><span class="sound_only"><?=$bo_subject?></span>더보기</a></div>-->
</div>
