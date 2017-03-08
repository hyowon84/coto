
<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
/*

 원작자 : Jeongum.com;

*/
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

function roll_strcut_utf8($str, $len, $checkmb=false, $tail='...') {
    preg_match_all('/[\xEA-\xED][\x80-\xFF]{2}|./', $str, $match);

    $m    = $match[0];
    $slen = strlen($str);  // length of source string
    $tlen = strlen($tail); // length of tail string
    $mlen = count($m); // length of matched characters
    
    if ($slen <= $len) return $str;
    if (!$checkmb && $mlen <= $len) return $str;
    
    $ret   = array();
    $count = 0;
    
    for ($i=0; $i < $len; $i++) {
        $count += ($checkmb && strlen($m[$i]) > 1)?2:1;
    
        if ($count + $tlen > $len) break;
        $ret[] = $m[$i];
    }
    
    return join('', $ret).$tail;
}
?>
<script>
var global_roll_issue_top = 0;
var global_roll_auto_lock = false;
$(document).ready(function(){
roll_issue_change("roll_issue_right_nav01",global_roll_issue_top);
setInterval(function(){
 if(global_roll_auto_lock == false){
 global_roll_issue_top = global_roll_issue_top - 300;
 if(global_roll_issue_top < -1500) global_roll_issue_top = 0;
 var o_code = '01';
 if(global_roll_issue_top == 0) o_code = '01';
 if(global_roll_issue_top == -300) o_code = '02';
 if(global_roll_issue_top == -600) o_code = '03';
 if(global_roll_issue_top == -900) o_code = '04';
 if(global_roll_issue_top == -1200) o_code = '05';
 if(global_roll_issue_top == -1500) o_code = '06';
 roll_issue_change("roll_issue_right_nav"+o_code,global_roll_issue_top);
 }
},3000);
});
function roll_issue_change(o_this,object){
 var img_width = $(".roll_issue_left_img").css('width');
 $(".roll_resizeimg").css('width',img_width);
 $(".roll_resizeimg").css('min-height','300');
 $(".roll_issue_right_nav").css('background','#555555');
 $("."+o_this).css('background','#888888');
 $(".roll_issue_right_nav").animate({opacity : 0.4 },100);
 $(".roll_issue_left_img_bottom").animate({opacity : 0.7 },0);
 $("."+o_this).animate({opacity : 1 },200);
  $(".roll_tc").animate({top : object},300);
  global_roll_issue_top = object;
  global_roll_auto_lock = true;
  setTimeout(function(){
    global_roll_auto_lock = false;
  },10000);
}
</script>
<style>
@import url('http://fonts.googleapis.com/earlyaccess/nanumgothic.css');
#roll_issue, #roll_issue div, #roll_issue a, #roll_issue span {font-family: 'Nanum Gothic', sans-serif;}
div:after { content : ".";display : block;height : 0;clear : both;visibility : hidden;}
#roll_issue {background:#555555; margin:0px; height: 300px;}
.roll_issue_left { float:left; width:64%; height:300px; overflow-y:hidden; position:relative;}
.roll_issue_left .roll_tc {position:absolute; width:100%; top:0px; height:1800px;}
.roll_issue_left .roll_tc .roll_issue_left_img {width:100%; height:300px; overflow:hidden; position:relative;}
.roll_issue_left .roll_tc .roll_issue_left_img img {top:0px; position:relative;}
.roll_issue_left .roll_tc .roll_issue_left_img .roll_issue_left_img_category {position:absolute; left:0px; top:0px; width:10px; height:10px; color:white; z-index:1;}
.roll_issue_left .roll_tc .roll_issue_left_img .roll_issue_left_img_bottom {cursor:pointer; position:absolute; bottom:0px; left:0px; padding-top:10px; padding-bottom:10px; width:100%; color:black; z-index:1; background:black;}
.roll_issue_left .roll_tc .roll_issue_left_img .roll_issue_left_img_bottom .roll_issue_left_img_bottom_title { font-size:12pt; font-weight:600; color:white; max-width:100%;}
.roll_issue_left .roll_tc .roll_issue_left_img .roll_issue_left_img_bottom .roll_issue_left_img_bottom_contents { font-size:10pt; font-weight:600; color:white; max-width:100%;}
.roll_issue_right { float:left; width:36%; height:300px;}
.roll_issue_right .roll_issue_right_nav {width:100%; height:36px; padding-top:7px; padding-bottom:5px; border-top:1px solid #222222; border-bottom:1px solid #888888; cursor:pointer; text-align:right;}
.roll_issue_right .roll_issue_right_nav .roll_issue_right_nav_title { font-size:10pt; font-weight:600; color:#eeeeee;}
.roll_issue_right .roll_issue_right_nav .roll_issue_right_nav_contents { font-size:9pt; font-weight:600; color:#cccccc;}
</style>
<div id="roll_issue">
  <div class="roll_issue_left">
    <div class="roll_tc">
<?php 
  for ($i=0; $i<6; $i++) {
  $thumb = get_list_thumbnail($bo_table, $list[$i]['wr_id'], 400, 300);
  $wr_id = $list[$i]['wr_id'];
  $num = $i + 1;
  if($thumb['src']) {
  $img_src = $thumb['src'];
  } else {
    $img_src = '';
  }
?>
      <div class="roll_issue_left_img roll_issue_img0<?php echo $num;?>">
        <div class="roll_issue_left_img_category">
        <?php
          if (isset($list[$i]['icon_new'])) echo " " . $list[$i]['icon_new'];
          if (isset($list[$i]['icon_hot'])) echo " " . $list[$i]['icon_hot'];
          if (isset($list[$i]['icon_link'])) echo " " . $list[$i]['icon_link'];
          if (isset($list[$i]['icon_secret'])) echo " " . $list[$i]['icon_secret'];
        ?>
        </div>
        <img class="roll_resizeimg" src="<?php echo $thumb['src'];?>" alt="<?php echo roll_strcut_utf8($list[$i]['subject'],40,'...');?>" />
        <a href="<?php echo $list[$i]['href'];?>">
        <div class="roll_issue_left_img_bottom">
          &nbsp;&nbsp;&nbsp;<span class="roll_issue_left_img_bottom_title">
          <?php echo roll_strcut_utf8($list[$i]['subject'],40,'...');?>
          </span><br />
          &nbsp;&nbsp;&nbsp;<span class="roll_issue_left_img_bottom_contents">
          <?php echo roll_strcut_utf8($list[$i]['wr_content'],60,'...');?>
          </span>
        </div>
        </a>
      </div>
<? } ?>
    </div>
  </div>
  <div class="roll_issue_right">
<?php 
  for ($i=0; $i<6; $i++) {
  $num = $i + 1;
  $object_top = $i*-300;
?>
    <div class="roll_issue_right_nav roll_issue_right_nav0<?php echo $num?>" onclick="roll_issue_change('roll_issue_right_nav0<?php echo $num?>','<?php echo $object_top?>');">
      <span class="roll_issue_right_nav_title"><?php echo roll_strcut_utf8($list[$i]['subject'],30,'...');?></span>&nbsp;&nbsp;&nbsp;&nbsp;<br/>
      <span class="roll_issue_right_nav_contents"><?php echo roll_strcut_utf8($list[$i]['wr_content'],35,'...');?></span>&nbsp;&nbsp;&nbsp;&nbsp;
    </div>
<? } ?>
  </div>
</div>
