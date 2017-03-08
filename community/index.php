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
margin-top: 260px;
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

<div id="content_community">
<div class="test"></div>
	<div id="community_top">
		<ul>
			<li style="height:310px;background:#787878">
				<div id="sliderContainer" style="height:310px;">
					<ul id="simpleSlider" style="height:310px;">

						<?
						$community_res = sql_query("select * from {$g5['g5_community_slide_table']} where status='community' order by no desc limit 0, 20");

						for($i = 0; $i < $community_row = mysql_fetch_array($community_res); $i++){
						?>
							<li style="float:left;position:absolute;cursor:pointer;width:100%;height:310px;margin:0 0 0 0;" onclick="goto_url('<?=$community_row[url]?>');"><img src="../data/community_slide/<?=$community_row[img_file]?>" border="0" align="absmiddle" style="width:800px;height:310px;"></li>
						<?
						}
						?>

					</ul>
					
				</div>
			</li>
			<li style="height:68px;background:#c5cdd1">
				<p id="ctext1">베스트샷</p>
				<p id="ctext2">이벤트진행중</p>
				<p id="ctext3" style="cursor:pointer;" onclick="goto_url('<?=G5_BBS_URL?>/board.php?bo_table=bestshot');">더보기 ></p>
			</li>
		
	</div>
	<div id="community_top2">
		<ul>
			<li style="width:534px;margin-right:6px;background:#4fc6f8" class="clearfix">
				<div style="height:68px;margin:0;padding:0;">
					<p id="ctext1">프리톡</p>
					<p id="ctext2">친해져요</p>
					<p id="ctext3" onclick="goto_url('<?=G5_URL?>/bbs/board.php?bo_table=free_talk');">더보기 ></p>
				</div>
				<div style="border-top:1px #7bd4fa solid;"></div>
				<div class="free_talk scrollpanel no3">

					<table border="0" cellspacing="0" cellpadding="0" width="100%">
						
					<?
					$free_talk_res = sql_query("
						select * from {$g5['g5_write_free_talk_table']}
						where wr_reply=''
						order by wr_id desc
						limit 0, 20
					");
					$free_talk_num = mysql_num_rows($free_talk_res);

					if($free_talk_num){
						for($i = 0; $free_talk_row = mysql_fetch_array($free_talk_res); $i++){

							$mem_row = sql_fetch("
								select * from {$g5['member_table']}
								where mb_id='".$free_talk_row[mb_id]."'
							");

							//댓글 갯수
							$com_cnt = sql_fetch("select * from g5_write_free_talk where wr_id='".$free_talk_row[wr_id]."'");
							$com_cnt_row = sql_fetch("select count(*) as cnt from g5_write_free_talk where wr_num='".$com_cnt[wr_num]."'");

					
					?>
						<tr>
							<td>
								<table border="0" cellspacing="0" cellpadding="0" width="100%" class="free_talk<?=$free_talk_row[wr_id]?>">
									<tr class="free_talk_tr<?=$free_talk_row[wr_id]?>">
										<td style="width:70px;height:45px;text-align:center;">
										<?if($mem_row[mb_img]){?>
											<img src="<?=G5_URL?>/data/member/<?=$mem_row[mb_img]?>" style="width:40px;height:40px;">
										<?}else{?>
											<img src="<?=G5_URL?>/img/pofile_no.png" style="width:40px;height:40px;">
										<?}?>
										</td>
										<td>
											<table border="0" cellspacing="0" cellpadding="0" width="100%">
												<tr height="25px">
													<td class="free_talk_tr_bn" idx="<?=$free_talk_row[wr_id]?>">
														<?=$free_talk_row[wr_content]?>
													</td>
												</tr>
												<tr height="25px">
													<td style="margin:5px 0 0 0;font-size:11px;font-weight:bold;">
														<?=$mem_row[mb_nick]?>
														<img src="<?=G5_URL?>/img/new_fa_ico1.png">
														<span idx="<?=$free_talk_row[wr_id]?>" wr_num="<?=$free_talk_row[wr_num]?>" wr_replay="" mode="main" class="ft_bn">댓글쓰기[<?=$com_cnt_row[cnt]-1?>]</span>
													</td>
													<td style="text-align:right;;padding:0 10px 0 0;margin:5px 0 0 0;font-size:9px;color:#545e63;font-style:italic;font-weight:bold;">
														<?=date("Y.m.d", strtotime($free_talk_row[wr_datetime]))?>
													</td>
												</tr>
											</table>
										</td>
									</tr>

									<?
									$sub_free_talk_res = sql_query("
										select * from {$g5['g5_write_free_talk_table']}
										where wr_num='".$free_talk_row[wr_num]."'
										and wr_reply != ''
										order by wr_reply asc, wr_id asc
									");

									for($k = 0; $sub_free_talk_row = mysql_fetch_array($sub_free_talk_res); $k++){

										$sub_mem_row = sql_fetch("
											select * from {$g5['member_table']}
											where mb_id='".$sub_free_talk_row[mb_id]."'
										");
									?>
									<tr class="free_talk_tr_view free_talk_tr_view<?=$free_talk_row[wr_id]?>">
										<td></td>
										<td>
											<table border="0" cellspacing="0" cellpadding="0" width="100%">
												<tr height="20px">
													<td>
														<?
														if(strlen($sub_free_talk_row[wr_reply]) > 1){
															for($k = 1; $k <= strlen($sub_free_talk_row[wr_reply]); $k++){echo "&nbsp;";}
														}
														?>
													</td>
													<td style="font-size:11px;white-space:nowrap;width:7%;font-weight:bold;">
														<img src="<?=G5_URL?>/img/new_fa_ico2.png">
														<?=$sub_mem_row[mb_nick]?>
													</td>
													<td style="float:left;padding:0 0 0 20px;color:#0549ab;word-break:break-all;font-size:11px;">
														<?=$sub_free_talk_row[wr_content]?>
													</td>
													<td style="float:left;padding:3px 0 0 7px;font-size:9px;color:#545e63;font-style:italic;font-weight:bold;">
														<?=date("Y.m.d H:i", strtotime($sub_free_talk_row[wr_datetime]))?>
													</td>
													<td style="white-space:nowrap;">
														<span href="#" idx="<?=$sub_free_talk_row[wr_id]?>" wr_num="<?=$sub_free_talk_row[wr_num]?>" wr_replay="<?=$sub_free_talk_row[wr_reply]?>" mode="sub" class="ft_bn">
															<img src="<?=G5_URL?>/img/new_fa_ico1.png">
															댓글
														</span>
													</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr class="free_talk_tr_view free_talk_tr_view<?=$free_talk_row[wr_id]?> sub_free_talk_tr_view<?=$sub_free_talk_row[wr_id]?>">
										<td></td>
										<td class="ft_input ft_input<?=$sub_free_talk_row[wr_id]?>">
										</td>
									</tr>

									<?
									}
									?>

									<tr>
										<td></td>
										<td class="ft_input ft_input<?=$free_talk_row[wr_id]?>">
										</td>
									</tr>

									
								</table>

							</td>
						</tr>
						<tr height="10px"><td></td></tr>

					<?
						}
					}else{
					?>

						<tr>
							<td>등록된 게시물이 없습니다.</td>
						</tr>

					<?
					}
					?>
						
					</table>

				</div>

				<form name="ffreetalk" id="ffreetalk" method="post">
					<input type="hidden" name="wr_id" value="">
					<input type="hidden" name="wr_num" value="">
					<input type="hidden" name="wr_reply" value="">
					<input type="hidden" name="wr_content" value="">
				</form>
			</li>
			<li style="width:260px;background:#56ccc8" class="clearfix">
				<div style="height:68px;margin:0;padding:0;">
					<p id="ctext1">새로운 가족</p>
					<p id="ctext2">환영해요</p>
					<p id="ctext3" onclick="goto_url('<?=G5_URL?>/bbs/board.php?bo_table=new_family');">더보기 ></p>
				</div>
				<div class="new_family scrollpanel no4" id="scrollpanel1">
					<table border="0" cellspacing="0" cellpadding="0" width="100%">

					<?
					$sql = "
						select * from {$g5['g5_new_family_table']}
						where wr_reply=''
						order by wr_id desc limit 0, 30
					";
					$new_family_res = sql_query($sql);

					for($i = 0; $new_family_row = mysql_fetch_array($new_family_res); $i++){
						$mem_row = sql_fetch("select * from {$g5['member_table']} where mb_id='".$new_family_row[mb_id]."' ");
						$cnt = sql_fetch("select count(*) as cnt from {$g5['g5_new_family_table']} where wr_num='".$new_family_row[wr_num]."' ");
					?>
						<tr>
							<td style="width:55px;padding:5px;cursor:pointer;" onclick="goto_url('<?=G5_URL?>/bbs/board.php?bo_table=new_family&no=<?=$new_family_row[wr_id]?>');">
							<?if($mem_row[mb_img]){?>
								<img src="<?=G5_URL?>/data/member/<?=$mem_row[mb_img]?>" border="0" align="absmiddle" style="width:40px;height:40px;">
							<?}else{?>
								<img src="<?=G5_URL?>/img/pofile_no.png" border="0" align="absmiddle" style="width:40px;height:40px;">
							<?}?>
							</td>
							<td style="cursor:pointer;" onclick="goto_url('<?=G5_URL?>/bbs/board.php?bo_table=new_family&no=<?=$new_family_row[wr_id]?>');">
								
								<table border="0" cellspacing="0" cellpadding="0" width="100%">
									<tr height="20px">
										<td style="font-size:11px;font-weight:bold;"><?=$mem_row[mb_nick]?></td>
									</tr>
									<tr height="20px">
										<td style="color:#fff;font-size:11px;">
											<?=cut_str($new_family_row[wr_content], 20)?>
											<span style="color:#f62b1e;font-weight:bold;">[<?=$cnt[cnt]-1?>]</span>
										</td>
									</tr>
								</table>

							</td>
						</tr>
					<?
					}
					?>
					</table>
				</div>
			</li>
		</ul>
	</div>
	<div id="community_top3"">
		<ul>
			<li style="width:140px;background:#1f3045">
				<p style="color:#fff;font-size:19px;font-weight:bold;line-height:20px;margin:15px 0 5px 15px;text-align:left;width:120px">공지사항</p>
				<p style="color:#fff;text-align:right;font-size:11px;width:120px;cursor:pointer;" onclick="goto_url('<?=G5_URL?>/bbs/board.php?bo_table=notice');">더보기 ></p>		
			</li>
			<li style="background:#ffffff;width:660px">
				<div style="padding:0;margin:0">
					<ul style="padding:0;margin:10px 0 0 15px">
					<?
						$sql = "select * from {$g5['g5_bo_notice_table']} order by wr_id desc limit 0, 4 ";
						$no_res = sql_query($sql);
						$no_num = mysql_num_rows($no_res);

						if($no_num){

							for($i = 0; $row = mysql_fetch_array($no_res); $i++){
						?>
							<li style="width:310px;list-style:none;padding:0;margin:0;height:26px;font-size:13px;font-style:italic">
								<a href="<?=G5_URL?>/bbs/board.php?bo_table=notice&wr_id=<?=$row[wr_id]?>">ㆍ<?=cut_str($row[wr_subject], 20)?></a>
							</li>
						<?
							}
						}else{
						?>
							<li><i>등록된 게시물이 없습니다.</i></li>
						<?
						}
					?>
				</ul>
				</div>
			</li>
			
		</ul>
	</div>
	<div id="community_top4"">
		<ul>
			<li style="width:397px;background:#ffffff;margin-right:6px;">
				<div style="height:68px;background:#c5cdd1;margin:0;padding:0;">
					<p id="ctext1">베스트게시물</p>
					<p id="ctext2" style="font-style:italic;">인기글</p>
					<p id="ctext3" onclick="goto_url('<?=G5_URL?>/bbs/board.php?bo_table=best');">더보기 ></p>
				</div>
				<div class="best">
					<table border="0" cellspacing="0" cellpadding="0" width="100%">

					<?
					$best_res = sql_query("select * from {$g5['g5_write_best_table']} where wr_reply='' and wr_is_comment='0' order by wr_id desc limit 0, 5 ");
					for($i = 0; $best_row = mysql_fetch_array($best_res); $i++){
						++$num;
					?>
						<tr height="25px">
							<td style="padding:5px 0 5px 0;cursor:pointer;font-style:normal;" onclick="goto_url('<?=G5_URL?>/bbs/board.php?bo_table=best&wr_id=<?=$best_row[wr_id]?>');"><?if($i == 0){echo "<font color='#ff1301'>".$num."</font>";}else{echo "<font color='#475055'>".$num."</font>";}?> <?=cut_str($best_row[wr_content], 25)?><font color="#475055">[<?=$best_row[wr_comment]?>]</font></td>
						</tr>
					<?
					}
					?>
					</table>
				</div>
			</li>
			<li style="width:397px;background:#ffffff;">
				<div style="height:68px;background:#c5cdd1;margin:0;padding:0;">
					<p id="ctext1">자유게시판</p>
					<p id="ctext2">일상다반사</p>
					<p id="ctext3" onclick="goto_url('<?=G5_URL?>/bbs/board.php?bo_table=free');">더보기 ></p>
				</div>
				<div class="free">
					<table border="0" cellspacing="0" cellpadding="0" width="100%">

					<?
					$num = 0;
					$free_res = sql_query("select * from {$g5['g5_write_free_table']} where wr_reply='' and wr_is_comment='0' order by wr_id desc limit 0, 5 ");
					for($i = 0; $free_row = mysql_fetch_array($free_res); $i++){
						++$num;
					?>
						<tr height="25px">
							<td style="padding:5px 0 5px 0;cursor:pointer;" onclick="goto_url('<?=G5_URL?>/bbs/board.php?bo_table=free&wr_id=<?=$free_row[wr_id]?>');">· <?=cut_str($free_row[wr_content], 25)?><font color="#475055">[<?=$free_row[wr_comment]?>]</font></td>
						</tr>
					<?
					}
					?>
					</table>
				</div>
			</li>
			<li style="width:397px;background:#ffffff;margin-right:6px;margin-top:6px">
				<div style="height:68px;background:#c5cdd1;margin:0;padding:0;">
					<p id="ctext1">코인수집에세이</p>
					<p id="ctext2">코인일기</p>
					<p id="ctext3" style="cursor:pointer;" onclick="goto_url('<?=G5_BBS_URL?>/board.php?bo_table=essey');">더보기 ></p>
				</div>
				<div class="essey">
					<table border="0" cellspacing="0" cellpadding="0" width="100%">

					<?
					$num = 0;
					$essey_res = sql_query("select * from {$g5['g5_write_essey_table']} where wr_reply='' and wr_is_comment='0' order by wr_id desc limit 0, 5 ");
					for($i = 0; $essey_row = mysql_fetch_array($essey_res); $i++){
						++$num;
					?>
						<tr height="25px">
							<td style="padding:5px 0 5px 0;cursor:pointer;" onclick="goto_url('<?=G5_URL?>/bbs/board.php?bo_table=essey&wr_id=<?=$essey_row[wr_id]?>');">· <?=cut_str($essey_row[wr_content], 25)?><font color="#475055">[<?=$essey_row[wr_comment]?>]</font></td>
						</tr>
					<?
					}
					?>
					</table>
				</div>
			</li>
			<li style="width:397px;background:#ffffff;margin-top:6px">
				<div style="height:68px;background:#c5cdd1;margin:0;padding:0;">
					<p id="ctext1">코인지식인</p>
					<p id="ctext2">코인 지식 공유</p>
					<p id="ctext3" style="cursor:pointer;" onclick="goto_url('<?=G5_BBS_URL?>/board.php?bo_table=know');">더보기 ></p>
				</div>
				<div class="know">
					<table border="0" cellspacing="0" cellpadding="0" width="100%">

					<?
					$num = 0;
					$know_res = sql_query("select * from {$g5['g5_write_know_table']} where wr_reply='' and wr_is_comment='0' order by wr_id desc limit 0, 5 ");
					for($i = 0; $know_row = mysql_fetch_array($know_res); $i++){
						++$num;
					?>
						<tr height="25px">
							<td style="padding:5px 0 5px 0;cursor:pointer;" onclick="goto_url('<?=G5_URL?>/bbs/board.php?bo_table=know&wr_id=<?=$know_row[wr_id]?>');">· <?=cut_str($know_row[wr_content], 25)?><font color="#475055">[<?=$know_row[wr_comment]?>]</font></td>
						</tr>
					<?
					}
					?>
					</table>
				</div>
			</li>
			<li style="width:800px;background:#ffffff;margin-right:6px;margin-top:6px">
				<div style="height:68px;background:#c5cdd1;margin:0;padding:0;">
					<p id="ctext1">건의사항</p>
					<p id="ctext2">함꼐 만들어 가는 코인즈투데이</p>
					<p id="ctext3" onclick="goto_url('<?=G5_URL?>/bbs/board.php?bo_table=comm_suggest');">더보기 ></p>
				</div>
				<div class="comm_suggest">

				<table border="0" cellspacing="0" cellpadding="0" width="100%">
					<tr>
						<td width="50%">
							<table border="0" cellspacing="0" cellpadding="0" width="100%">

							<?
							$num = 0;
							$free_res = sql_query("select * from {$g5['g5_write_comm_suggest_table']} where wr_reply='' and wr_is_comment='0' order by wr_id desc limit 0, 5 ");
							for($i = 0; $free_row = mysql_fetch_array($free_res); $i++){
								++$num;
							?>
								<tr height="25px">
									<td style="padding:5px 0 5px 0;cursor:pointer;" onclick="goto_url('<?=G5_URL?>/bbs/board.php?bo_table=comm_suggest&wr_id=<?=$free_row[wr_id]?>');">· <?=cut_str($free_row[wr_content], 25)?><font color="#475055">[<?=$free_row[wr_comment]?>]</font></td>
								</tr>
							<?
							}
							?>
							</table>
						</td>
						<td width="50%" valign="top">
							<table border="0" cellspacing="0" cellpadding="0" width="100%">

							<?
							$num = 0;
							$free_res = sql_query("select * from {$g5['g5_write_comm_suggest_table']} where wr_reply='' and wr_is_comment='0' order by wr_id desc limit 5, 5 ");
							for($i = 0; $free_row = mysql_fetch_array($free_res); $i++){
								++$num;
							?>
								<tr height="25px">
									<td style="padding:5px 0 5px 0;cursor:pointer;" onclick="goto_url('<?=G5_URL?>/bbs/board.php?bo_table=comm_suggest&wr_id=<?=$free_row[wr_id]?>');">· <?=cut_str($free_row[wr_content], 25)?><font color="#475055">[<?=$free_row[wr_comment]?>]</font></td>
								</tr>
							<?
							}
							?>
							</table>
						</td>
					</tr>
				</table>

				</div>
			</li>
			<li style="width:800px;background:#ffffff;height:584px;margin-right:6px;margin-top:6px">
				<div style="height:68px;background:#c5cdd1;margin:0;padding:0;">
					<p id="ctext1">포토겔러리</p>
					<p id="ctext2">포토북</p>
					<p id="ctext3" style="cursor:pointer;" onclick="goto_url('<?=G5_BBS_URL?>/board.php?bo_table=photo_gallery');">더보기 ></p>
				</div>
				<div class="photo_gallery">
					<? echo latest("bach72_pc", "photo_gallery", 4, 100);?>
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

		$(".sub_free_talk_tr_view" + idx).css("display", "table-row");

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
		data += "<td width='70px' style='white-space:nowrap;font-weight:bold;font-size:11px;'>";
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

		if($(".free_talk_tr_view" + idx).css("display") == "table-row"){
			$(".free_talk_tr_view" + idx).css("display", "none");
		}else{
			$(".free_talk_tr_view" + idx).css("display", "table-row");
		}
	});

});

function com_input_bn(idx, mode){
	var val = $("input[name='com_input" + idx + "']").val();
	var num = $(".free_talk_tr_view" + idx).length - 1;
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

			$(".sub_free_talk_tr_view" + idx).css("display", "none");
			$(".ft_input").html("");
			//$(".ft_input" + idx).html(data);
		}
	});
}

function ft_bn1(idx, mode){
	var data = "";

	$(".ft_input").html("");

	data += "<table border='0' cellspacing='0' cellpadding='0' width='100%'>";
	data += "<tr>";
	data += "<td width='70px' style='white-space:nowrap;font-weight:bold;font-size:11px;'>";
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