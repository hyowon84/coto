<?php
define('_INDEX_', true);
include_once('./_common.php');
include_once(G5_PATH.'/head.php');
?>

<style type="text/css">
a, a:hover, a.link, a.focus{text-decoration:none;}
.ft_bn{color:#fff;cursor:pointer;font-size:11px;}
</style>

<link rel="stylesheet" href="<?php echo G5_URL ?>/css/main1.css">
<link rel="stylesheet" type="text/css" href="<?=G5_URL?>/css/slide.css">
<style type="text/css">
#simpleSliderNext {
position:absolute;
margin:160px 0 0 770px;
font-weight: bold;
cursor: pointer;
z-index:10;
color:#fff;
}

#simpleSliderPrevious {
position: absolute;
margin:160px 0 0 -1px;
font-weight: bold;
cursor: pointer;
z-index:10;
color:#fff;
}

#simpleSliderNav {
position:absolute;
z-index:2;
padding: 20px;
margin-top: 300px;
margin-left:0;
}

#simpleSlider{position:relative;}
/* Styling for the navigation items, this can also be anything we want, but I am using circles */
.simpleSliderNavItem {
border:1px #fff solid;
height: 8px;
width: 8px;
float: left;
background: url(/new_img/main/slide_dot.gif);
margin-left: 6px;
border-radius: 8px;
cursor: pointer;
}
/* styles for the active nav item */
.active {
border:1px #fff solid;
background-color:#fff;
}
</style>


<script type="text/javascript">

$(document).ready(function(){
	$('#simpleSlider').simpleSlider({
		interval: 3000,
		wantNav: true,
		navContainer: "#sliderContainer",
		pauseOnHover: true
	});
});

</script>
<script src="<?php echo G5_URL ?>/js/main1.js"></script>
<script type="text/javascript" src="<?=G5_URL?>/js/jquery.simpleSlider.js"></script>

<div id="aside2"></div>

<div id="content_community_photo">
<div class="test"></div>
	<div id="community_top">
		<ul>
			<li style="height:50px;background:#cfcfcf;color:#fff">
				<p id="ctext1">갤러리<span id="ctext2">포토북</span></p>
			
			</li>
			<li style="height:350px;background:#787878">
				<div id="sliderContainer" style="height:350px;">
					<ul id="simpleSlider" style="height:350px;">

						<?
						$community_res = sql_query("select * from {$g5['g5_community_slide_table']} where status='community_gall' order by no desc limit 0, 20");

						for($i = 0; $i < $community_row = mysql_fetch_array($community_res); $i++){


						?>
							<li style="float:left;position:absolute;cursor:pointer;width:100%;height:350px;margin:0 0 0 0;" onclick="goto_url('<?=$community_row[url]?>');"><img src="../data/community_slide/<?=$community_row[img_file]?>" border="0" align="absmiddle" style="width:800px;height:350px;"></li>
						<?
						}
						?>

					</ul>
					
				</div>
			</li>
			
		
	</div>
	
	
	<div id="community_top5"">
		<ul>			
			<li style="width:800px;height:55px;margin-top:6px;margin-bottom:0px;color:#fff">
				<div style="height:55px;margin:0;padding:0">
					<p id="ctext1">베스트샷<span id="ctext2">이벤트 진행중</span></p>
					<p id="ctext3" onclick="goto_url('<?=G5_URL?>/bbs/board.php?bo_table=bestshot');">더보기 ></p>
				</div>
			</li>

			<li style="width:800px;margin-top:0px;margin-bottom:0px">
				<div style="margin:0;padding:0;">
					<? echo latest("bach72_pc2", "bestshot", 2, 20);?>
				</div>
			</li>

			<li style="width:800px;height:55px;margin-top:6px;margin-bottom:0px;color:#fff">
				<div style="height:55px;margin:0;padding:0;">
					<p id="ctext1">포토갤러리<span id="ctext2">포토북</span></p>
					<p id="ctext3" onclick="goto_url('<?=G5_URL?>/bbs/board.php?bo_table=photo_gallery');">더보기 ></p>
				</div>
			</li>

			<li style="width:800px;background:#ffffff;margin-top:0px;margin-bottom:0px">
				<div style="background:#c5cdd1;margin:0;padding:0;">
					<? echo latest("bach72_pc", "photo_gallery", 6, 100);?>
				</div>
			</li>
			
		</ul>
	</div>


</div>





<script type="text/javascript">

$(document).ready(function(){
	$(".ft_bn").click(function(){
		var idx = $(this).attr("idx");
		var wr_num = $(this).attr("wr_num");
		var wr_reply = $(this).attr("wr_reply");
		var wr_content = $(this).attr("wr_content");
		var mode = $(this).attr("mode");

		if(mode == "main"){
			mode = "main";
		}else{
			mode = "sub";
		}

		if(wr_content){
			wr_content = wr_content;
		}else{
			wr_content = "";
		}
		var data = "";

		data += "<table border='0' cellspacing='0' cellpadding='0' width='100%'>";
		data += "<tr>";
		data += "<td width='70px' style='white-space:nowrap;font-weight:bold;'>";
		data += "<img src='<?=G5_URL?>/img/new_fa_ico2.png'> <?=$member[mb_nick]?>";
		data += "</td>";
		data += "<td>";
		data += "<input type='text' name='com_input"+idx+"' style='width:100%;border:0px;' maxlength='100' value='"+wr_content+"'>";
		data += "</td>";
		data += "<td style='width:50px;'>";
		data += "<div style='float:left;background:#fff;margin:1px 0 0 0;width:20px;cursor:pointer;' onclick=\"com_input_bn(\'"+idx+"\', \'"+mode+"\')\">";
		data += "<img src='<?=G5_URL?>/img/main_free_talk_ico.png'>";
		data += "</div>";
		data += "</td>";
		data += "</tr>";
		data += "</table>";

		$(".ft_input").html("");
		$(".ft_input" + idx).html(data);
	});

	$(".free_talk_tr_bn").click(function(){
		var idx = $(this).attr("idx");

		$(".free_talk_tr_view").css("display", "none");
		$(".free_talk_tr_view" + idx).css("display", "table-row");
	});

});

function com_input_bn(idx, mode){
	var val = $("input[name='com_input" + idx + "']").val();
	var num = $(".free_talk_tr_view" + idx).length - 2;
	num = parseInt(num);
	
	$.ajax({
		type : "POST",
		dataType : "HTML",
		url : "./_Ajax.community.php",
		data : "w=r&ca_id=free_talk&bo_table=free_talk&wr_id=" + idx + "&wr_content=" + val,
		success : function(data){
			//$(".test").html(data);

			if(mode == "main"){
				$(".free_talk_tr_view" + idx).eq(num).after(data);
			}else{
				$(".sub_free_talk_tr_view" + idx).after(data);
			}
			//$(".ft_input" + idx).html(data);
		}
	});
}

function ft_bn1(idx, mode){
	var data = "";

	$(".ft_input").html("");

	data += "<table border='0' cellspacing='0' cellpadding='0' width='100%'>";
	data += "<tr>";
	data += "<td width='70px' style='white-space:nowrap;font-weight:bold;'>";
	data += "<img src='<?=G5_URL?>/img/new_fa_ico2.png'> <?=$member[mb_nick]?>";
	data += "</td>";
	data += "<td>";
	data += "<input type='text' name='com_input"+idx+"' style='width:100%;border:0px;' maxlength='100' value=''>";
	data += "</td>";
	data += "<td style='width:50px;'>";
	data += "<div style='float:left;background:#fff;margin:1px 0 0 0;width:20px;cursor:pointer;' onclick='com_input_bn(\""+idx+"\", \""+mode+"\")'>";
	data += "<img src='<?=G5_URL?>/img/main_free_talk_ico.png'>";
	data += "</div>";
	data += "</td>";
	data += "</tr>";
	data += "</table>";

	$(".ft_input" + idx).html(data);
}

</script>






<?php
include_once(G5_PATH.'/tail.php');
?>