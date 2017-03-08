<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가 

include_once(G5_LIB_PATH.'/thumbnail.lib.php');
?>
	
<link rel="stylesheet" href="<?php echo $board_skin_url ?>/style.css">
<link rel="stylesheet" href="<?=G5_URL?>/css/vanillabox.css">
<h2 id="container_title"><?php echo $board['bo_subject'] ?><span class="sound_only"> 목록</span></h2>



	<!--<script type='text/javascript' src='<?php// echo $board_skin_url?>/js/jquery.tools.min.js'></script>-->
	<script type='text/javascript' src='<?php echo $board_skin_url?>/js/jquery.masonry.min.js'></script>
	<script type='text/javascript' src='<?php echo $board_skin_url?>/js/jquery.infinitescroll.min.js'></script>

	<script type="text/javascript" src="<?=G5_URL?>/js/jquery.vanillabox.js"></script>
	<script type="text/javascript" src="<?=G5_URL?>/js/configform.js"></script>
	<script type="text/javascript" src="<?=G5_URL?>/js/configpre.js"></script>
	<script src='http://coinstoday.co.kr/js/jquery.zoom.js'></script>

	<link rel="stylesheet" href="<?php echo $board_skin_url?>/wetoz.board.css?130625" type='text/css' />
	<style type='text/css'>
		a.btn_big{background:url(<?php echo $board_skin_url?>/img/bg_btn_default.gif) no-repeat 100% 0;}
		a.btn_big span,a.btn_big strong{background:url(<?php echo $board_skin_url?>/img/bg_btn_default.gif) no-repeat}
		a.btn_sml{background:url(<?php echo $board_skin_url?>/img/bg_btn_default.gif) no-repeat 100% -27px;}
		a.btn_sml span,a.btn_sml strong{background:url(<?php echo $board_skin_url?>/img/bg_btn_default.gif) no-repeat 0 -27px}

		#gallerysection figure, 
		#gallerysection figcaption {margin: 0; padding: 0;}
		#gallerysection {margin: 0 auto; width: 820px;}
		#gallerysection .sortlist .sortbox {display: block; margin-bottom: 13px; }
		#gallerysection .sortlist .iner {padding: 0px;background:#efefef}
		#gallerysection .sortlist .caption {padding: 5px 4px 2px 4px; font-weight: bold;word-wrap:break-word; word-break:break-all;}
		#gallerysection .sortlist a {color: #696969;}
		#gallerysection .sortlist a img {width: <?php echo THUMB_WIDTH?>px;}
		#gallerysection .sortlist a .caption .pubdate {line-height:23px; font-weight: normal; color: #6279ab;}

		#gallerysection .sortlist .iner {
			overflow: hidden;
			position: relative;
			border-top: 0px solid #bfc2c4;
			border-left: 0px solid #bfc2c4;
			border-right: 0px solid #a8aaab;
			border-bottom: 0px solid #a8aaab;
			background-color: #fff;
			box-shadow: 0 0px 0 rgba(164,168,171,0.2);
			-webkit-box-shadow: 0 0px 0 rgba(164,168,171,0.2);
			clear: both;
		}
		#gallerysection .sortlist .iner figure {width:<?php echo THUMB_WIDTH?>px;background-color:#eeeeee;word-wrap:break-word; word-break:break-all;margin:0;padding:0}

		/* zoom */
		.zoom {display:inline-block;position: relative;}
		.zoom:after {content:'';display:block;width:33px;height:33px;position:absolute;top:0;right:0;background:url(icon.png);}
		.zoom img {display: block;}
	</style>

	<div id="aside2"></div>

	<div class="test"></div>
	<!-- 게시판 목록 시작 -->
	<table width="<?php echo $width?>" align="center" cellpadding="0" cellspacing="0"><tr><td>
	
		<!-- 분류 셀렉트 박스, 게시물 몇건, 관리자화면 링크 -->
		<div>
			<div style="float:left;background:#ffffff;width:820px;height:50px">
				
				<div class="board_search" style="margin:7px 10px 0 10px">

				<form name="fsearch" method="get">
				<input type="hidden" name="bo_table" value="<?php echo $bo_table?>">
				<input type="hidden" name="sop"      value="and">
				

				<?if($bo_table == "photo_gallery"){?>
				<div style="float:left;">
					<?php if ($write_href) { ?><a href="<?php echo $write_href?>" border=0 style="border:0px"><img src="<?=G5_URL?>/img/photo_write.jpg" border=0 style="border:0px"> </a><?php } ?>
				</div>
				<?}?>

				<?if($bo_table == "bestshot"){?>
				<div style="float:left;">
					<img src="<?=G5_URL?>/img/photo_gall_ev.gif" border="0" align="absmiddle" style="cursor:pointer;margin:5px 0 0 0;" onclick="goto_url('<?=G5_URL?>/bbs/board.php?bo_table=event')">
				</div>
				<?}?>




				<fieldset class="srch" style="float:right">
					
					<?php if ($is_category) { ?>
					<select name=sca class="selectm" style="height:21px" onchange="location='<?php echo $category_location?>'+<?php echo strtolower($g4[charset])=='utf-8' ? "encodeURIComponent(this.value)" : "this.value"?>;">
					<option value=''>전체</option>
					<?php echo $category_option?>
					</select>
					<?} else {?>
					<input type="hidden" name="sca"      value="<?php echo $sca?>">
					<?php } ?>

					<select name="sfl" class="selectm" style="height:33px;border:1px solid #e1e1e1">
						<option value="wr_subject">제목</option>
						<option value="wr_content">내용</option>
						<option value="wr_subject||wr_content">제목+내용</option>
						<option value="mb_id,1">회원아이디</option>
						<option value="mb_id,0">회원아이디(코)</option>
						<option value="wr_name,1">글쓴이</option>
						<option value="wr_name,0">글쓴이(코)</option>
					</select>

					<input name="stx" class="stx" maxlength="15" itemname="검색어" style="height:33px;width:250px;border:1px solid #e1e1e1" required value='<?php echo stripslashes($stx)?>'>
					<button type="image" style="border:0px;"><img src="<?=G5_URL?>/img/photo_search.jpg"></button>

				</fieldset>
				</form>
				</div>
			</div>

		</div>


		<form name="fboardlist"  id="fboardlist" action="./board_list_update.php" onsubmit="return fboardlist_submit(this);" method="post">
		<input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
		<input type="hidden" name="sfl" value="<?php echo $sfl ?>">
		<input type="hidden" name="stx" value="<?php echo $stx ?>">
		<input type="hidden" name="spt" value="<?php echo $spt ?>">
		<input type="hidden" name="page" value="<?php echo $page ?>">
		<input type="hidden" name="sw" value="">
		<input type="hidden" name="btn_submit" value="">

		<div style="float:right;">
			<?php if ($is_checkbox) { ?>
				<a href="javascript:;" onclick="select_delete('선택삭제');" class="btn_sml"><span>선택삭제</span></a>
				<a href="javascript:;" onclick="select_copy('copy');" class="btn_sml"><span>선택복사</span></a>
				<a href="javascript:;" onclick="select_copy('move');" class="btn_sml"><span>선택이동</span></a>
			<?php } ?>
			<?php if ($rss_href) { ?><a href='<?php echo $rss_href?>' class="btn_sml"><span>RSS</span></a><?}?>
			<?php if ($admin_href) { ?><a href="<?php echo $admin_href?>" class="btn_sml"><span>관리자</span></a><?}?>
			<?php //  if ($write_href) { ?><!--<a href="<?php echo $write_href?>" class="btn_big"><strong>글쓰기</strong></a>--><?php //} ?>
		</div>

		<div class="clear" style="height:4px;"></div>
		
		<section id="gallerysection">

		<div class="sortlist">
		<ul>
		<?php 
		for ($i=0; $i<count($list); $i++) {
				
				$subject = $list[$i][subject];
				$wr_id = $list[$i][wr_id];

				$checkbox = "";
				if ($is_checkbox)
					$checkbox = "<input type=checkbox name=chk_wr_id[] value='{$list[$i][wr_id]}'>";

				$mem_row = sql_fetch("select * from {$g5[member_table]} where mb_id='".$list[$i][mb_id]."' ");

				// 썸네일.;
				?>
				
				<li class="sortbox">
					<div style="height:70px;background:#efefef;font-style:italic">
						<p style="font-size:12px;margin-left:25px;font-weight:bold;padding:15px 0 5px 0">Photo by <?=$mem_row[mb_nick]?></p>
						<p style="font-size:18px;margin-left:25px">
							<span><?php echo $nobr_begin.$checkbox.$subject.$nobr_end;?></span>
							<?if($board['bo_write_level'] <= $member['mb_level']){?>

								<?if($member[mb_id] == "admin"){?>
									<span style="float:right;margin:0 20px 0 0;"><a href="<?php echo $list[$i][href]; ?>">수정</a></span>

									<?if($bo_table == "photo_gallery"){?>

									<span style="float:right;margin:0 20px 0 0;"><a href="javascript:void(0);" onclick="bestshot_bn('<?=$wr_id?>')">베스트샷</a></span>

									<?}?>
								<?}?>
							<?}?>
						</p>
					</div>
					<div style="height:35px;background:#fff;">
						<p style="font-size:14px;margin-left:25px;font-weight:bold;padding:7px 0 7px 0"><?=cut_str($list[$i][wr_content], 50)?></p>
					
					</div>
					<div class="iner">
						<!--<a href="<?php// echo $list[$i][href]; ?>">-->
						
						<figure>
							<a href="javascript:void(0)" id="show-button<?=$wr_id?>" onclick="show_button('<?=$wr_id?>')">
							<?php
						if ($list[$i]['is_notice']) { // 공지사항  ?>
							<strong style="width:<?php echo $board['bo_gallery_width'] ?>px;height:<?php echo $board['bo_gallery_height'] ?>px">공지</strong>
						<?php } else {
							//$thumb = get_list_thumbnail($board['bo_table'], $list[$i]['wr_id'], $board['bo_gallery_width'], $board['bo_gallery_height']);
							$img_row = sql_fetch("select * from {$g5['board_file_table']} where bo_table='".$board['bo_table']."' and wr_id='".$list[$i]['wr_id']."' and bf_no='0' ");

							//if($thumb['src']) {
							if($img_row) {
								//$img_content = '<img src="'.$thumb['src'].'" alt="'.$thumb['alt'].'" width="'.$board['bo_gallery_width'].'" height="'.$board['bo_gallery_height'].'">';
								$img_content = '<img src="'.G5_URL.'/data/file/'.$board['bo_table'].'/'.$img_row[bf_file].'" alt="'.$thumb['alt'].'" width="'.$board['bo_gallery_width'].'" height="'.$board['bo_gallery_height'].'">';
							} else {
								$img_content = '<span style="width:'.$board['bo_gallery_width'].'px;height:'.$board['bo_gallery_height'].'px">no image</span>';
							}

							echo $img_content;
						}
						 ?>
							</a>
							<figcaption class="caption">
								<div style="position:relative;padding:10px 40px 5px 40px;">
									<span onclick="recomm_bn('<?=$wr_id?>')" style="float:left;border:1px #cfcfcf solid;padding:7px;cursor:pointer;">
										<img src="<?=G5_URL?>/img/heart_ico.png">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<span class="recomm_bn<?=$wr_id?>">
										<?
										if($list[$i][wr_1]){
											echo $list[$i][wr_1];
										}else{
											echo 0;
										}
										?>
										</span>
									</span>
									<span style="float:left;padding:5px 0 0 10px;">
									<?php
									include(G5_SNS_PATH."/list.sns.skin.php");
									?>
									</span>
								</div>
								<div class="cl" style="position:relative;padding:5px 40px 10px 40px;">
									<?php
									// 코멘트 입출력
									include('./list_comment.php');
									 ?>
								</div>
							</figcaption>
						</figure>
						

						<ul id="interactive-image-list<?=$wr_id?>" style="display:none;">
						<?
						$img_res1 = sql_query("select * from {$g5['board_file_table']} where bo_table='".$board['bo_table']."' and wr_id='".$list[$i]['wr_id']."' order by bf_no asc ");
						for($k = 0; $img_row1 = mysql_fetch_array($img_res1); $k++){
						?>
							<li class='zoom ex<?=$wr_id?>'><a href="<?=G5_URL?>/data/file/<?=$board['bo_table']?>/<?=$img_row1[bf_file]?>" title="<?=$subject?>">&nbsp;</a></li>
						<?}?>
						</ul>
					</div>
				</li><!-- .sortbox -->

				<?php
			}

		if ($i == 0)
			echo "<li class='sortbox' style='width:100%;text-align:center;'><div class='iner'><figure><figcaption>게시물이 없습니다.</figcaption></figure></div></li>";
		?>		
		</ul>			
		</div><!-- .sortlist -->

		<nav id="page-nav">
			<a href="<?php echo $board_skin_url?>/list.append.php?bo_table=<?php echo $bo_table; ?>&page=2"></a>
		</nav>


		</section>

		<div class="clear" style="height:5px;"></div>

		<div class="board_button">
			<div style="float:left;">
			<?php if ($is_checkbox) { ?>
				<a href="javascript:;" onclick="select_delete('선택삭제');" class="btn_sml"><span>선택삭제</span></a>
				<a href="javascript:;" onclick="select_copy('copy');" class="btn_sml"><span>선택복사</span></a>
				<a href="javascript:;" onclick="select_copy('move');" class="btn_sml"><span>선택이동</span></a>
			<?php } ?>
			</div>
			<div style="float:right;">
			<?php if ($write_href) { ?><a href="<?php echo $write_href?>" class="btn_big"><strong>글쓰기</strong></a><?php } ?>
			<?php if ($list_href) { ?><a href="<?php echo $list_href?>" class="btn_big"><span>목록으로</span></a><?php } ?>
			</div>
		</div>

		</form>

		<div class="clear" style="height:8px;"></div>

	</td></tr></table>


	<form name="fviewcomment" action="./list_comment_update.php" method="post" autocomplete="off">
    <input type="hidden" name="w" value="<?php echo $w ?>" id="w">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="wr_id" value="">
    <input type="hidden" name="comment_id" value="<?php echo $c_id ?>" id="comment_id">
    <input type="hidden" name="sca" value="<?php echo $sca ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="spt" value="<?php echo $spt ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <input type="hidden" name="is_good" value="">

	<input type="hidden" name="wr_name" id="wr_name">
	<input type="hidden" name="wr_password" id="wr_password">
	<input type="hidden" name="wr_secret" id="wr_secret">
	<input type="hidden" name="wr_content" id="wr_content" maxlength="10000">
	</form>

	<script type="text/javascript">

	var $container = $('.sortlist');
	$(document).ready(function(){
		$container.imagesLoaded( function(){
			$container.masonry({
				itemSelector : '.sortbox',
				columnWidth : 170,
				isFitWidth: true,
				isAnimated : false
			});
		});

	});

	

	$(function(){
		

		$container.infinitescroll({
			navSelector  : '#page-nav',    // selector for the paged navigation
			nextSelector : '#page-nav a',  // selector for the NEXT link (to page 2)
			itemSelector : '.sortbox',     // selector for all items you'll retrieve
			loading: {
				finishedMsg: 'No more pages to load.',
				img: '<?php echo $board_skin_url?>/img/loading.gif'
			}
		},
		// trigger Masonry as a callback
		function( newElements ) {
			// hide new items while they are loading
			var $newElems = $( newElements ).css({ opacity: 0 });
			// ensure that images load before adding to masonry layout
			$newElems.imagesLoaded(function(){
				// show elems now they're ready
				$newElems.animate({ opacity: 1 });
				$container.masonry( 'appended', $newElems, true );
			});
		}
		);

	});

	

	</script>
	<script type="text/javascript">

	function bestshot_bn(wr_id){
		$.ajax({
			type : "POST",
			dataType : "HTML",
			url : "./_Ajax.photo_gallery.php",
			data : "wr_id=" + wr_id + "&mode=bestshot",
			success : function(data){
				//$(".test").html(data);
				//return false;
				if(data == "n"){
					alert("이미 베스트샷에 등록 되어 있습니다.");
				}else{
					alert("베스트샷으로 등록 되었습니다.");
				}
			}
		});
	}

	function recomm_bn(wr_id){
		var num = $(".recomm_bn" + wr_id).html();
		num = parseInt(num) + 1;
		
		$.ajax({
			type : "POST",
			dataType : "HTML",
			url : "./_Ajax.photo_gallery.php",
			data : "wr_id=" + wr_id + "&num=" + num + "&mode=recomm",
			success : function(data){
				if(data == "y"){
					alert("추천이 완료 되었습니다.");
					$(".recomm_bn" + wr_id).html(num);
				}else{
					alert("이미 추천 하셨습니다.");
				}
			}
		});
	}

	function btn_com_submit(wr_id){

		var wr_id = wr_id;

		$("input[name='wr_id']").val(wr_id);
		<?php if ($is_guest) { ?>
		$("input[name='wr_name']").val($("input[name='wr_name1[" + wr_id + "]']").val());
		$("input[name='wr_password']").val($("input[name='wr_password1[" + wr_id + "]']").val());
		<?}else{?>
		$("input[name='wr_name']").val("<?=$member[mb_nick]?>");
		$("input[name='wr_password']").val("<?=$member[mb_password]?>");
		<?}?>
		if($("input[name='wr_secret1[" + wr_id + "]']").is(":checked") == true){
			$("input[name='wr_secret']").val($("input[name='wr_secret1[" + wr_id + "]']").val());
		}
		$("input[name='wr_content']").val($("input[name='wr_content1[" + wr_id + "]']").val());
		fviewcomment_submit(document.fviewcomment);

	}

	function show_button(wr_id){
		// Options Demo
		var configForm = new ConfigForm($('#config-form' + wr_id));
		var configPre = new ConfigPre($('#config-pre' + wr_id));

		var optionBox = null;
		if (optionBox) {
			optionBox.dispose();
		}

		var config = configForm.buildConfig();
		var targetElems = (config.type === 'iframe') ?
			$('#interactive-page-list' + wr_id + ' a') :
			$('#interactive-image-list' + wr_id + ' a');

		optionBox = targetElems.vanillabox(config);
		optionBox.show();

		$(".vnbx-mask").find("img").addClass("ex1");
	}

	// 더보기
	function more_append() {
		$.ajax({
			type:"post"
			,url:"<?php echo $board_skin_path?>/list.append.php"
			,data:$("#fboardlist").serialize()
			,success:function(msg){
				if (msg.replace(/^\s+/,"") != "") {
					$(".sortlist ul").append(msg);
					$("#fboardlist #page").val( parseInt($("#fboardlist #page").val()) + 1 );


				}
				else {
					alert("목록이 더 이상 존재하지 않습니다.");
				}					
			}
			,error:function(msg){
				alert(msg);
			}
		});
	}
	</script>
	<!-- /컨텐츠 스크립 - 페이지 정렬, 팝업 -->

	<script type="text/javascript">
		if ('<?php echo $sca?>') document.fsearch.sca.value = '<?php echo $sca?>';
		if ('<?php echo $stx?>') {
			document.fsearch.sfl.value = '<?php echo $sfl?>';
		}
	</script>

<?php if ($is_checkbox) { ?>
<script>
function all_checked(sw) {
    var f = document.fboardlist;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]")
            f.elements[i].checked = sw;
    }
}

function fboardlist_submit(f) {
    var chk_count = 0;

    for (var i=0; i<f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]" && f.elements[i].checked)
            chk_count++;
    }

    if (!chk_count) {
        alert(document.pressed + "할 게시물을 하나 이상 선택하세요.");
        return false;
    }

    if(document.pressed == "선택복사") {
        select_copy("copy");
        return;
    }

    if(document.pressed == "선택이동") {
        select_copy("move");
        return;
    }

    if(document.pressed == "선택삭제") {
        if (!confirm("선택한 게시물을 정말 삭제하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다\n\n답변글이 있는 게시글을 선택하신 경우\n답변글도 선택하셔야 게시글이 삭제됩니다."))
            return false;

        f.removeAttribute("target");
        f.action = "./board_list_update.php";
    }

    return true;
}

// 선택한 게시물 복사 및 이동
function select_copy(sw) {
    var f = document.fboardlist;

    if (sw == "copy")
        str = "복사";
    else
        str = "이동";

    var sub_win = window.open("", "move", "left=50, top=50, width=500, height=550, scrollbars=1");

    f.sw.value = sw;
    f.target = "move";
    f.action = "./move.php";
    f.submit();
}

//선택삭제
function select_delete(mode){
	var f = document.fboardlist;
	if (!confirm("선택한 게시물을 정말 삭제하시겠습니까?\n\n한번 삭제한 자료는 복구할 수 없습니다\n\n답변글이 있는 게시글을 선택하신 경우\n답변글도 선택하셔야 게시글이 삭제됩니다."))
		return false;

	$("input[name='btn_submit']").val(mode);
	f.action = "./board_list_update.php";
	f.submit();
}

</script>
<?php } ?>
<!-- } 게시판 목록 끝 -->
