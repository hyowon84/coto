<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가
?>

<link rel="stylesheet" href="<?php echo G5_SHOP_SKIN_URL; ?>/style.css">
<script src="<?php echo G5_JS_URL; ?>/iteminfoimageresize.js"></script>
<script>
    $(function() {
        var isMobile = "<?=G5_IS_MOBILE?>";

        if(isMobile == 1) {
            createQnAListItems();
        }

        function createQnAListItems() {
            var answer_status = $('#answer_status').val();
            $.post("/ajax/req_qnalist.php", {mode:"gp",answer_status:answer_status} ,function(data) {
                var list = $.parseJSON(data).data;

                var container = $('#qaListContainer');

                $.each(list, function(i, item) {
                    var resultItem = createQnAListItem(item);
                    container.append(resultItem);
                });
                //$('.prodImgContainer').imgLiquid({fill:false});
            });
        }

        function createQnAListItem(item) {
            console.log(item);
            var answerState ="";
            var secretIcon = "";
            var answerContents = "";
            var qaDate = "";
            answerState = (item.iq_answer.trim() == "")?"미답변":"답변완료";
            secretIcon = (item.iq_secret == "1")?"&nbsp;<img src='../skin/shop/basic/img/icon_secret.gif' alt='비밀글''>":"";
            answerContents = (item.iq_answer == "")?"<p>답변이 등록되지않았습니다.</p>":item.iq_answer;
            qaDate = item.iq_time.replace(/-/gi,".").substring(0,10);
            var itemHtml =
                "<div class='qaListItemContainer'>" +
                    "<div class='qaHeaderContainer'>" + 
                        "<div class='qaTitle'>" + item.iq_subject + secretIcon + "</div>" +
                        "<div class='qaState'> " + answerState + "</div>" +
                    "</div>" +
									
                    "<div class='qaInfoContainer' >" +
                        "<div class='qaImgContainer'>" +
                            "<img class='qaImg' src=" + decodeURIComponent(item.img_url).replace(" ", "%20") + ">" +
                        "</div>" +
                        "<div class='qaContentContainer'>" +
                            "<div class='qaQuestion ellipsis'><span>Q </span>" + item.iq_question + "</div>" +
                            "<div class='qaAnswer'><span>A [답변] </span>" + $(answerContents).text() + "</div>" +
                            "<div class='qaAnswerToggle'><button class='toggleBtn' onclick='toggleQnaExp(" + item.iq_id + ")'>보기</button></div>" +
                            "<div class='qaTime'>등록일 : " + qaDate + "</div>" +
                        "</div>" +
                    "</div>" +
                    "<div class='qaDivider' ></div>" + 
                    "<div class='qaExpContainer' id='qaExpContainer_"+ item.iq_id+"' >" +
                        "<div class='qaExpQuestion'>문의내용</div>" + 
                        "<div class='qaExpQuestionContents'>" + item.iq_question + "</div>" + 
                        "<div class='qaExpAnswer'>답변</div>" + 
                        "<div class='qaExpAnswerContents'>" + $(answerContents).html() + "</div>" +
                    "</div>" +
                "</div>";

            return itemHtml;
        }
        
        
    });

    function toggleQnaExp(iq_id) {
        var obj = $("#qaExpContainer_"+iq_id);
        if(obj.css('display') == "none") {
            obj.css('display','block');
        } else {
            obj.css('display','none');
        }
    }
</script>

<?include_once("../inc/mypage_menu.php");?>

<!-- 전체 상품 문의 목록 시작 { -->

<!--
<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div id="sqa_sch">
    <a href="<?php echo $_SERVER['PHP_SELF']; ?>">전체보기</a>
    <label for="sfl" class="sound_only">검색항목</label>
    <select name="sfl" required id="sfl">
        <option value="">선택</option>
        <option value="b.it_name"    <?php echo get_selected($sfl, "b.it_name", true); ?>>상품명</option>
        <option value="a.it_id"      <?php echo get_selected($sfl, "a.it_id"); ?>>상품코드</option>
        <option value="a.iq_subject" <?php echo get_selected($sfl, "a.is_subject"); ?>>문의제목</option>
        <option value="a.iq_question"<?php echo get_selected($sfl, "a.iq_question"); ?>>문의내용</option>
        <option value="a.iq_name"    <?php echo get_selected($sfl, "a.it_id"); ?>>작성자명</option>
        <option value="a.mb_id"      <?php echo get_selected($sfl, "a.mb_id"); ?>>작성자아이디</option>
    </select>

    <label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
    <input type="text" name="stx" value="<?php echo $stx; ?>" id="stx" required class="required frm_input">
    <input type="submit" value="검색" class="btn_submit">
</div>
</form>
-->

<div class="contentWrap" id="sqa" style="background:#fff;">

	<ul>
        <!-- 1차 오픈에서 제외
		<li style="border:0px;">

			<div id="my_tab_nav">
				<ul>
					<li class="on" style="border:0px;" onclick="goto_url('<?=G5_SHOP_URL?>/itemqalist.php');">상품 문의/답변</li>
					<li style="border:0px;" onclick="goto_url('<?=G5_SHOP_URL?>/itemqalist.php');">1:1 문의/답변</li>
				</ul>
			</div>
		</li>
		-->
	</ul>

	<ul>
        <!-- 1차 오픈에서 제외
		<li style="border:0px;float:left;cursor:pointer;">
			<div style="float:left;" onclick="goto_url('<?=G5_SHOP_URL?>/itemqalist.php');">
				투데이스토어
			</div>
			<div style="float:left;margin:0 0 0 10px;cursor:pointer;" onclick="goto_url('<?=G5_SHOP_URL?>/gpitemqalist.php');">
				공동구매
			</div>
		</li>
		-->
		<li class="fSearchContainer" style="border:0px;float:right;">
			<form name="fsearch" id="fsearch" method="GET">
			<div class="date_diff">
				<div class="date_diff_bn <?if($answer_month == ""){echo "on";}?>">전체</div>
				<div class="date_diff_bn <?if($answer_month == "3"){echo "on";}?>" idx="3">3개월</div>
				<div class="date_diff_bn <?if($answer_month == "6"){echo "on";}?>" idx="6">6개월</div>
				<div class="date_diff_bn right <?if($answer_month == "12"){echo "on";}?>" idx="12">1년</div>
				<div class="selectContainer" style="border:0px;background:#fff;padding:0px 3px 0px 3px;">
					<input type="hidden" name="answer_month" value="<?=$answer_month?>">
					<select id="answer_status" name="answer_status" style="height:26px;">
						<option value="" <?if($answer_status == ""){echo "selected";}?>>전체 문의</option>
						<option value="n" <?if($answer_status == "n"){echo "selected";}?>>미답변</option>
						<option value="y" <?if($answer_status == "y"){echo "selected";}?>>답변완료</option>
					</select>
				</div>
			</div>
			</form>

		</li>
	</ul>

	<ul class="cl" id="qaListContainer">
        <? if(G5_IS_MOBILE) {?>

        <?} else {?>
		<li style="border:0px;padding:0px;">
			<div id="custom_title">
				<ul>
					<li style="border:0px;width:280px;">상품정보</li>
					<li style="border:0px;width:280px;">문의 내용</li>
					<li style="border:0px;width:80px;">등록일</li>
					<li style="border:0px;width:80px;">답변상태</li>
				</ul>
			</div>
		</li>

    <?php
    $thumbnail_width = 500;
    $num = $total_count - ($page - 1) * $rows;

    for ($i=0; $row=sql_fetch_array($result); $i++)
    {
        $iq_subject = conv_subject($row['iq_subject'],50,"…");

        $is_secret = false;
        if($row['iq_secret']) {
            $iq_subject .= ' <img src="'.G5_SHOP_SKIN_URL.'/img/icon_secret.gif" alt="비밀글">';

            if($is_admin || $member['mb_id' ] == $row['mb_id']) {
                $iq_question = get_view_thumbnail($row['iq_question'], $thumbnail_width);
            } else {
                $iq_question = '비밀글로 보호된 문의입니다.';
                $is_secret = true;
            }
        } else {
            $iq_question = get_view_thumbnail($row['iq_question'], $thumbnail_width);
        }

        $it_href = G5_SHOP_URL.'/grouppurchase.php?gp_id='.$row['gp_id'];

        if(!$is_secret) {
            if ($row['iq_answer'])
            {
                $iq_answer = get_view_thumbnail($row['iq_answer'], $thumbnail_width);
                $iq_stats = '답변완료';
                $iq_style = 'sit_qaa_done';
                $is_answer = true;
            } else {
                $iq_stats = '미답변';
                $iq_style = 'sit_qaa_yet';
                $iq_answer = '답변이 등록되지 않았습니다.';
                $is_answer = false;
            }
        }

        if ($i == 0) echo '<ol style="padding:0px;border:0px">';
    ?>
    <li style="padding:10px 0 10px 0;">

        <div class="sqa_img" style="width:80px;height:80px;margin:0 0 0 10px;">
            <a href="<?php echo $it_href; ?>">
                <?php echo get_gp_image($row['gp_id'], 70, 70); ?>
                <span><?php echo $row['gp_name']; ?></span>
            </a>
        </div>

        <section class="sqa_section" style="width:710px;">

			<div style="float:left;width:210px;">
				<h2><?php echo $iq_subject; ?></h2>
			</div>
			<div style="float:left;width:300px;">
				<div style="height:30px;"><span style="color:#56ccc8;float:left;font-weight:bold;">Q&nbsp;</span><span style="float:left;"><?php echo $iq_question; // 상품 문의 내용 ?></span></div>
				<div style="clear:both;height:30px;margin:5px 0 0 0;">
					<span style="color:#56ccc8;float:left;font-weight:bold;">A&nbsp;[답변]</span><span style="float:left;"><?php echo $iq_answer; ?></span>
				</div>
			</div>
			<div style="float:left;width:99px;text-align:center;font-size:13px;">
				<?php echo substr($row['iq_time'],0,10); ?>
			</div>
			<div style="float:left;width:99px;text-align:center;font-size:13px;color:#56ccc8;">
				<?php echo $iq_stats; ?>
			</div>

            <!--<dl class="sqa_dl" style="width:230px;">
                <dt>작성자</dt>
                <dd><?php echo $row['iq_name']; ?></dd>
                <dt>작성일</dt>
                <dd><?php echo substr($row['iq_time'],0,10); ?></dd>
                <dt>상태</dt>
                <dd class="<?php echo $iq_style; ?>"><?php echo $iq_stats; ?></dd>
            </dl>

            <div id="sqa_con_<?php echo $i; ?>" class="sqa_con" style="display:none;">
                <div class="sit_qa_qaq">
                    <strong>문의내용</strong><br>
                    <?php echo $iq_question; // 상품 문의 내용 ?>
                </div>
                <?php if(!$is_secret) { ?>
                <div class="sit_qa_qaa">
                    <strong>답변</strong><br>
                    <?php echo $iq_answer; ?>
                </div>
                <?php } ?>
            </div>

            <div class="sqa_con_btn"><button class="sqa_con_<?php echo $i; ?>">보기</button></div>
			-->
        </section>
		<div class="cl">
			<div id="sqa_con_<?php echo $i; ?>" class="sqa_con" style="display:none;">
                <div class="sit_qa_qaq">
                    <strong>문의내용</strong><br>
                    <?php echo $iq_question; // 상품 문의 내용 ?>
                </div>
                <?php if(!$is_secret) { ?>
                <div class="sit_qa_qaa">
                    <strong>답변</strong><br>
                    <?php echo $iq_answer; ?>
                </div>
                <?php } ?>
            </div>

            <div class="sqa_con_btn"><button class="sqa_con_<?php echo $i; ?>">보기</button></div>
		</div>

    </li>
    <?php
        $num--;
    }

    if ($i > 0) echo '</ol>';
    if ($i == 0) echo '<p id="sqa_empty">자료가 없습니다.</p>';
    ?>

    <?}?>
	</ul>
</div>

<?php echo get_paging($config['cf_write_pages'], $page, $total_page, "{$_SERVER['PHP_SELF']}?$qstr&amp;answer_month=$answer_month&amp;answer_status=$answer_status&amp;page="); ?>

<script>
$(function(){
    // 상품문의 더보기
    $(".sqa_con_btn button").click(function(){
        var $con = $(this).parent().prev();
        if($con.is(":visible")) {
            $con.slideUp();
            $(this).text("보기");
        } else {
            $(".sqa_con_btn button").text("보기");
            $("div[id^=sqa_con]:visible").hide();
            $con.slideDown(
                function() {
                    // 이미지 리사이즈
                    $con.iteminfoimageresize();
                }
            );
            $(this).text("닫기");
        }
    });

	$(".date_diff_bn").click(function(){
		var idx = $(this).attr("idx");
		$("form[name='fsearch']").find("input[name='answer_month']").val(idx);
		$("form[name='fsearch']").attr("action", "./gpitemqalist.php").submit();
	});

	$("select[name='answer_status']").change(function(){
		$("form[name='fsearch']").attr("action", "./gpitemqalist.php").submit();
	});
});
</script>
<!-- } 전체 상품 사용후기 목록 끝 -->