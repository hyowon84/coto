<?php
include_once('./_common.php');

if (G5_IS_MOBILE) {
    include_once(G5_MSHOP_PATH.'/head.php');
} else {
    include_once(G5_PATH.'/head.php');
}

$customer_slide_res = sql_query("select * from {$g5['g5_cus_slide_table']} order by no desc ");
$customer_slide_num = mysql_num_rows($customer_slide_res);
?>

<link rel="stylesheet" type="text/css" href="<?=G5_URL?>/css/slide.css">
<style type="text/css">
/* The slider container */
#sliderContainer {
width: 100%;
margin: 0 auto;
}
/* The slider ul stypes, important to note that we hide all the overflow! */
#simpleSlider {
width: 100%;
height: 155px;
overflow: hidden;
position: relative;
list-style: none;
padding: 0;
margin: 0 auto;
}
/* styles for each item */
#simpleSlider li {
position: absolute;
top: 0px;
left: 0px;
display: none;
}
/* display the first item */
#simpleSlider li:first-child {
display: block;
}
/* Our style for the next button, this can be anything */
#simpleSliderNext {
position:absolute;
margin:185px 0 0 1070px;
font-weight: bold;
cursor: pointer;
z-index:10;
}
/* Our style for the previous button, this can be anything */
#simpleSliderPrevious {
position: absolute;
margin:185px 0 0 0px;
font-weight: bold;
cursor: pointer;
z-index:10;
}
li img {
border-radius: 2px;
}
/* Some margin for your navigation */
#simpleSliderNav {
position:absolute;
z-index:2;
padding: 20px;
margin-top: -50px;
margin-left:0px;
}
/* Styling for the navigation items, this can also be anything we want, but I am using circles */
.simpleSliderNavItem {
border:1px #eee solid;
height: 5px;
width: 5px;
float: left;
background: url(/new_img/main/slide_dot.gif);
margin-left: 5px;
border-radius: 5px;
cursor: pointer;
}
/* styles for the active nav item */
.active {
border:1px #eee solid;
background-color:#eee;

}

a, a.link, a.hover, a.focus{text-decoration:none !important;}
</style>

<script type="text/javascript" src="<?=G5_URL?>/js/jquery.simpleSlider.js"></script>

<script type="text/javascript">

<?if($customer_slide_num){?>
$(document).ready(function(){
	$('#simpleSlider').simpleSlider({
		interval: 3000,
		wantNav: true,
		navContainer: "#sliderContainer",
		pauseOnHover: true
	});
});
<?}?>

</script>


<?php 
if(G5_IS_MOBILE) {
?>

<div class='ctc_top ctc_fontstyle1'>
	Customer Center
</div>


<?php
	
	//게시판 목록만큼
	 
	$tbname = array('공지사항','건의사항','FAQ');
	$comment = array('코인즈투데이의 소식을 알려드립니다','더 좋은 서비스를 약속 드립니다','주요 질문과 답변을 안내해 드립니다.');
	$tblist = array($g5['g5_bo_notice_table'],$g5['g5_bo_suggest_table'],$g5['g5_bo_faq_table']);
	
	
	for($i = 0; $i < count($tblist); $i++) {
		$table = $tblist[$i];
		
		$m_table = str_replace('g5_write_','',$table);
		
		
		$ORDER_BY = "order by wr_id";
		$LIMIT = ($i == 2) ? 10 : 5;
		$LIMIT_SQL = "LIMIT	0, $LIMIT";
		
		$qry = "	SELECT	*
					FROM		$table
					$ORDER_BY
					$LIMIT_SQL
		";
		$result = sql_query($qry);
	?>
	
<section class='ctc_board'>
	<header class='ctc_board_head'>
		<div class='ctc_board_head_left'>
			<span class='ctc_fontstyle1'><?=$tbname[$i]?></span>
			<span class='ctc_fontstyle2'><?=$comment[$i]?></span>
		</div>
		<div class='ctc_board_head_right'>
			<a href='<?=G5_URL?>/bbs/board.php?bo_table=<?=$m_table?>' class='ctc_board_head_more'>더보기</a>
		</div>		
	</header>
	<div class='ctc_board_contents'>
		<ul class='ctc_tb_ul'>
	<?
		$cnt = 0;
		while($row = mysql_fetch_array($result)) {
			$cnt++;
	?>
				<li class='ctc_tb_li'>
					<a href="<?=G5_URL?>/bbs/board.php?bo_table=<?=$m_table?>&wr_id=<?=$row[wr_id]?>">
						<div class='ctc_tb_left'>
							<div class='ctc_fontstyle3 ellipsis'><?=$row[wr_subject]?></div>
						</div>
						<div class='ctc_tb_right'>
							<div class='ctc_fontstyle4'><?=date('Y.m.d',strtotime($row[wr_datetime]))?></div>
						</div>
					</a>
				</li>
		<?
			echo ($cnt != $LIMIT) ? "<li class='ctc_tb_line'></li>" : '';
		?>
		
				
	<?
			
		}//while end
	?>
		</ul>
	</div>
</section>

	<?
	}//for end
}//if and else 
else {
?>

<div id="aside2"></div>

<div>

	<table border="0" cellspacing="0" cellpadding="0" width="100%" class="cus_tb">
		<tr>
			<td class="bg">
				<!-- 배너 -->
				<div id="sliderContainer">
					<ul id="simpleSlider">

						<?
						if($customer_slide_num){
							for($i = 0; $i < $customer_slide_num; $i++){
								$customer_slide_row = mysql_fetch_array($customer_slide_res);
						?>
							<li style="cursor:pointer;width:100%;" onclick="goto_url('<?=$customer_slide_row1[URL]?>');"><img src="../data/customer_img/<?=$customer_slide_row[img_file]?>" border="0" align="absmiddle" width="100%" height="155px" /></li>
						<?
							}
						}else{
						?>
							<li style="width:100%;cursor:pointer;margin:5em 0 0 0;height:30px;text-align:center;">등록된 이미지가 없습니다.</li>
						<?
						}
						?>

					</ul>
				</div>
			</td>
		</tr>
		<tr height="10px"><td></td></tr>
		<tr>
			<td>

				<table border="0" cellspacing="0" cellpadding="0" width="100%">
					<tr height="220px">
						<td class="bg" valign="top">
							<table border="0" cellspacing="0" cellpadding="0" width="100%">
								<tr>
									<td colspan="2" class="title">공지사항</td>
								</tr>
								<tr>
									<td style="padding:5px 5px 5px 20px;"><i>코인즈투데이의 소식을 알려드립니다.</i></td>
									<td style="padding:5px;text-align:right;"><a href="<?=G5_URL?>/bbs/board.php?bo_table=notice">더보기></a></td>
								</tr>
								<tr height="2px"><td colspan="2" style="background:#c5cdd1;"></td></tr>

								<?
								$sql = "select * from {$g5['g5_bo_notice_table']} order by wr_id desc limit 0, 5 ";
								$no_res = sql_query($sql);
								$no_num = mysql_num_rows($no_res);

								if($no_num){

									for($i = 0; $row = mysql_fetch_array($no_res); $i++){
								?>
									<tr height="30px">
										<td class="subject"><a href="<?=G5_URL?>/bbs/board.php?bo_table=notice&wr_id=<?=$row[wr_id]?>">ㆍ<?=cut_str($row[wr_subject], 20)?></a></td>
										<td><?=date("Y.m.d", strtotime($row[wr_datetime]))?></td>
									</tr>
								<?
									}
								}else{
								?>
									<tr><td align="center" colspan="2" style="padding:5px;"><i>등록된 게시물이 없습니다.</i></td></tr>
								<?
								}
								?>

							</table>
						</td>
						<td width="10px"></td>
						<td class="bg" valign="top">
							<table border="0" cellspacing="0" cellpadding="0" width="100%">
								<tr>
									<td colspan="2" class="title">건의사항</td>
								</tr>
								<tr>
									<td style="padding:5px 5px 5px 20px;"><i>더 좋은 서비스를 약속 드립니다.</i></td>
									<td style="padding:5px;text-align:right;"><a href="<?=G5_URL?>/bbs/board.php?bo_table=suggest">더보기></a></td>
								</tr>
								<tr height="2px"><td colspan="2" style="background:#c5cdd1;"></td></tr>
								
								<?
								$sql = "select * from {$g5['g5_bo_suggest_table']} order by wr_id desc limit 0, 5 ";
								$su_res = sql_query($sql);
								$su_num = mysql_num_rows($su_res);

								if($su_num){

									for($i = 0; $row = mysql_fetch_array($su_res); $i++){
								?>
									<tr height="30px">
										<td class="subject"><a href="<?=G5_URL?>/bbs/board.php?bo_table=suggest&wr_id=<?=$row[wr_id]?>">ㆍ<?=cut_str($row[wr_subject], 20)?></a></td>
										<td><?=date("Y.m.d", strtotime($row[wr_datetime]))?></td>
									</tr>
								<?
									}
								}else{
								?>
									<tr><td align="center" colspan="2" style="padding:5px;"><i>등록된 게시물이 없습니다.</i></td></tr>
								<?
								}
								?>

							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr height="10px"><td></td></tr>
		<tr height="220px">
			<td class="bg" valign="top">
				<table border="0" cellspacing="0" cellpadding="0" width="100%">
					<tr>
						<td class="title" colspan="2">FAQ</td>
					</tr>
					<tr>
						<td style="padding:5px 5px 5px 20px;"><i>주요 질문과 답변을 안내해 드립니다.</i></td>
						<td style="padding:5px;text-align:right;"><a href="<?=G5_URL?>/bbs/board.php?bo_table=FAQ">더보기></a></td>
					</tr>
					<tr height="2px"><td colspan="2" style="background:#c5cdd1;"></td></tr>
					
					<?
					$sql = "select * from {$g5['g5_bo_faq_table']} order by wr_id desc ";
					$faq_res = sql_query($sql);
					$faq_num = mysql_num_rows($faq_res);

					if($faq_num){
					?>
					<tr>
						<td width="50%">
							<table border="0" cellspacing="0" cellpadding="0" width="100%">
								<?
								$sql = "select * from {$g5['g5_bo_faq_table']} order by wr_id desc limit 0, 5 ";
								$faq_res = sql_query($sql);
								$faq_num = mysql_num_rows($faq_res);

								for($i = 0; $row = mysql_fetch_array($faq_res); $i++){
								?>
								<tr height="30px">
									<td class="subject"><a href="<?=G5_URL?>/bbs/board.php?bo_table=FAQ">ㆍ<?=cut_str($row[wr_subject], 35)?></a></td>
								</tr>
								<?
								}
								?>
							</table>
						</td>

						<td width="50%">
							<table border="0" cellspacing="0" cellpadding="0" width="100%">
								<?
								$sql = "select * from {$g5['g5_bo_faq_table']} order by wr_id desc limit 5, 5 ";
								$faq_res = sql_query($sql);
								$faq_num = mysql_num_rows($faq_res);

								for($i = 0; $row = mysql_fetch_array($faq_res); $i++){
								?>
								<tr height="30px">
									<td class="subject"><a href="<?=G5_URL?>/bbs/board.php?bo_table=FAQ">ㆍ<?=cut_str($row[wr_subject], 35)?></a></td>
								</tr>
								<?
								}
								?>
							</table>
						</td>
						
					</tr>
					<?}else{?>
					<tr height="30px">
						<td colspan="2" style="text-align:center;padding:5px;"><i>등록된 게시물이 없습니다.</i></td>
					</tr>
					<?}?>

				</table>
			</td>
		</tr>
		<tr height="12px"><td></td></tr>
		<tr height="220px">
			<td valign="top">
				<table border="0" cellspacing="0" cellpadding="0" width="100%">
					<tr>
						<td colspan="2" style="padding:10px 10px 5px 15px;font-size:17px;color:#fff;font-weight:bold;background:#56ccc8;">진행중인 이벤트</td>
					</tr>
					<tr>
						<td style="padding:5px 10px 10px 15px;font-size:15px;color:#fff;background:#56ccc8;"><i>이벤트에 많은 참여 부탁드립니다.</i></td>
						<td style="text-align:right;padding:5px 10px 5px 10px;background:#56ccc8;"><a href="<?=G5_URL?>/bbs/board.php?bo_table=event" style="color:#fff;">더보기></a></td>
					</tr>
					<tr>
						<td class="bg" style="padding:5px;" colspan="2">
							<table border="0" cellspacing="0" cellpadding="0" width="100%">
								<tr>
								<?
								$sql = "select * from {$g5['g5_bo_event_table']} order by wr_id desc limit 0, 3 ";
								$ev_res = sql_query($sql);
								$ev_num = mysql_num_rows($ev_res);

								for($i = 0; $row = mysql_fetch_array($ev_res); $i++){
									$thumb = get_list_thumbnail("event", $row['wr_id'], 250, 250);

									if($thumb['src']) {
										$img_content = '<img src="'.$thumb['src'].'" alt="'.$thumb['alt'].'" width="250px" height="250">';
									} else {
										$img_content = '<span style="width:250px;height:250px">no image</span>';
									}
								?>
									<td style="padding:5px;text-align:center;width:263px;"><a href="<?=G5_URL?>/bbs/board.php?bo_table=event&wr_id=<?=$row[wr_id]?>"><?=$img_content?></a></td>
								<?
								}
								?>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

</div>
<?
}
?>



<script type="text/javascript">

</script>

<?php


if (G5_IS_MOBILE) {
    include_once(G5_MSHOP_PATH.'/tail.php');
} else {
    include_once(G5_PATH.'/tail.php');
}

?>