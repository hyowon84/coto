<?php
include_once('./_common.php');
include_once(G5_PATH.'/head.php');

/*
function get_currency(){

$url = "http://www.naver.com/include/timesquare/widget/exchange.xml";

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

curl_setopt($ch, CURLOPT_REFERER, 'http://blog.naver.com/usback');

$currency_xml = curl_exec($ch);

print_r($currency_xml);

curl_close ($ch);

}
*/
?>

<style>
#news_text {font-size:11px;font-weight:bold;padding:5px;color:#475055}
.rates td{font-size:11px;padding:7px 3px 7px 5px;text-align:center;font-weight:bold;}
.tbl_Cal td{font-size:11px;font-weight:bold;}

.tb_time{margin:15px 15px 0 0;}
.tb_time td{width:110px;text-align:center;font-weight:bold;font-size:22px;}
.tb_time .space{width:18px;}
</style>


<div id="aside2"></div>

<div class="getContent" style="display:none;"></div>
<div class="getContent1" style="display:none;"><table><tr><?=get_currency1(2)?></tr></table></div>

<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<!-- 금속 경제와 금화/은화의 가장 최근 뉴스 -->
	<tr>
		<td>
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td height="20px"></td></tr>

	<!-- 금속 경제 뉴스 -->
	<tr>
		<td>
		<div style="width:810px;background:#fff;">
			<table border="0" cellspacing="0" cellpadding="0" width="100%">

				<?
				$sise_row = sql_fetch("
					select * from g5_write_news_gold_economy as a
					LEFT OUTER JOIN g5_write_news_silver_news as b
					ON a.wr_1 = b.wr_1
					order by a.wr_id desc, b.wr_id desc limit 0, 1
				");
				$sise_img_row = sql_fetch("select * from {$g5['board_file_table']} where bo_table='".$sise_row[wr_2]."' and wr_id=".$sise_row[wr_id]." and bf_no='1' ");
				if(!$sise_img_row[bf_file]){
					$sise_img_row = sql_fetch("select * from {$g5['board_file_table']} where bo_table='".$sise_row[wr_2]."' and wr_id=".$sise_row[wr_id]." and bf_no='0' ");
				}
				?>
				<tr style="cursor:pointer;" onclick="goto_url('<?=G5_URL?>/bbs/board.php?bo_table=<?=$sise_row[wr_2]?>&wr_id=<?=$sise_row[wr_id]?>');">
					<td>
						<img src="<?=G5_URL?>/data/file/<?=$sise_row[wr_2]?>/<?=$sise_img_row[bf_file]?>" border="0" align="absmiddle" width="100%" height="240px">
						<div style="width:81px;height:81px;position:absolute;margin:-240px 0 0 0;"><img src="<?=G5_URL?>/img/news_new_bn.png" border="0" align="absmiddle"></div>
					</td>
				</tr>
				<tr style="cursor:pointer;" onclick="goto_url('<?=G5_URL?>/bbs/board.php?bo_table=<?=$sise_row[wr_2]?>&wr_id=<?=$sise_row[wr_id]?>');">
					<td style="padding:10px 50px 0 50px;font-size:17px;font-weight:bold;">
						<div style="position:absolute;left:0px;"><img src="<?=G5_URL?>/img/news_list_bn.png" border="0" align="absmiddle"></div>
						<div style="padding:10px 0 0 0;"><?=cut_str($sise_row['wr_subject'], 70)?></div>
					</td>
				</tr>
				<tr style="cursor:pointer;" onclick="goto_url('<?=G5_URL?>/bbs/board.php?bo_table=<?=$sise_row[wr_2]?>&wr_id=<?=$sise_row[wr_id]?>');">
					<td style="height:100px;padding:10px 50px 0 50px;line-height:1.5;" valign="top">
						<div class="gall_text_comments" con='<?=$sise_row['wr_content']?>'><?//=$sise_row['wr_content']?></div>
						<a href="<?=G5_URL?>/bbs/board.php?bo_table=<?=$sise_row[wr_2]?>"><font color="#ea6060">more</font></a>
					</td>
				</tr>
			</table>
		</div>
		</td>
	</tr>
	<tr><td height="5px"></td></tr>

	<!-- 금화 경제 뉴스 -->
	<tr>
		<td>
		
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
				
				<tr style="cursor:pointer;background:#fff;" onclick="goto_url('<?=G5_URL?>/bbs/board.php?bo_table=news_gold_economy');">
					<td style="font-size:14px;font-weight:bold;padding:10px 10px 5px 20px;">금속 경제 뉴스</td>
					<td id="news_text" align="right">더보기 ></td>
				</tr>
				<tr><td height="2px"></td></tr>
				<tr style="background:#fff;">
					<td colspan="2" style="padding:15px 15px 10px 10px;;">
						<?=latest("clean_gallery1", "news_gold_economy", 4, 30);?>
					</td>
				</tr>
			</table>
		</div>
		</td>
	</tr>
	<tr><td height="5px"></td></tr>

	<!-- 금화/은화 뉴스 -->
	<tr>
		<td>
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
				
				<tr style="cursor:pointer;background:#fff;" onclick="goto_url('<?=G5_URL?>/bbs/board.php?bo_table=news_silver_news');">
					<td style="font-size:14px;font-weight:bold;padding:10px 10px 5px 20px;">금화/은화 뉴스</td>
					<td id="news_text" align="right">더보기 ></td>
				</tr>
				<tr><td height="2px"></td></tr>
				<tr style="background:#fff;">
					<td colspan="2" style="padding:15px 15px 10px 10px;">
						<?=latest("clean_gallery1", "news_silver_news", 4, 30);?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td height="5px"></td></tr>

	<!-- 돈버는 뉴스 & 정보 -->
	<tr>
		<td>
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
				
				<tr style="cursor:pointer;background:#fff;" onclick="goto_url('<?=G5_URL?>/bbs/board.php?bo_table=news_info');">
					<td style="font-size:14px;font-weight:bold;padding:10px 10px 5px 20px;">돈버는 뉴스 & 정보</td>
					<td id="news_text" align="right">더보기 ></td>
				</tr>
				<tr><td height="2px"></td></tr>
				<tr style="background:#fff;">
					<td colspan="2" style="padding:10px;">
						<?=latest("clean_gallery2", "news_info", 20, 30);?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td height="5px"></td></tr>

	<!-- 환율정보 및 계산기 -->
	<tr>
		<td>
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td>
						<div style="background:#002855;padding:10px 5px 25px 5px;">
							<div style="float:left;color:#56ccc8;">환율정보</div>
							<div style="float:right;color:#fff;"><?=date("Y.m.d")?></div>
						</div>
					</td>
					<td style="width:5px;"></td>
					<td>
						<div style="background:#002855;padding:10px 5px 25px 5px;">
							<div style="float:left;color:#56ccc8;">코인스투데이 계산기</div>
						</div>
					</td>
				</tr>
				<tr>
					<td style="background:#fff;width:275px;padding:0;" valign="top">
						<!-- 환율정보 시작 -->
						<table border="0" cellspacing="0" cellpadding="0" width="100%" class="rates">
							<tr>
								<td>통화명</td>
								<td style="line-height:1.6em;">공동구매환율</br>우대환율적용</td>
								<td>+/-</td>
								<td style="line-height:1.6em;">Ebay/Amazon</br>우대환율미적용</td>
							</tr>
							<tr><td colspan="4" style="height:1px;background:#e1e1e1;padding:0;"></td></tr>
							<tr>
								<td style="text-align:left;font-weight:default;"><img src="<?=G5_URL?>/img/news_country1.gif" border="0" align="absmiddle"> 미국</td>
								<td class="getcon1"></td>
								<td class="getcon2"></td>
								<td class="getcon"><table><tr><?=get_currency1(2)?></tr></table></td>
							</tr>
							<tr>
								<td style="text-align:left;"><img src="<?=G5_URL?>/img/news_country2.gif" border="0" align="absmiddle"> 영국</td>
								<td class="getcon1"></td>
								<td class="getcon2"></td>
								<td class="getcon"><table><tr><?=get_currency1(6)?></tr></table></td>
							</tr>
							<tr>
								<td style="text-align:left;"><img src="<?=G5_URL?>/img/news_country3.gif" border="0" align="absmiddle"> 유럽연합</td>
								<td class="getcon1"></td>
								<td class="getcon2"></td>
								<td class="getcon"><table><tr><?=get_currency1(4)?></tr></table></td>
							</tr>
							<tr>
								<td style="text-align:left;"><img src="<?=G5_URL?>/img/news_country4.gif" border="0" align="absmiddle"> 호주</td>
								<td class="getcon1"></td>
								<td class="getcon2"></td>
								<td class="getcon"><table><tr><?=get_currency1(9)?></tr></table></td>
							</tr>
							<tr>
								<td style="text-align:left;"><img src="<?=G5_URL?>/img/news_country5.gif" border="0" align="absmiddle"> 중국</td>
								<td class="getcon1"></td>
								<td class="getcon2"></td>
								<td class="getcon"><table><tr><?=get_currency1(5)?></tr></table></td>
							</tr>
							<tr>
								<td style="text-align:left;"><img src="<?=G5_URL?>/img/news_country6.gif" border="0" align="absmiddle"> 캐나다</td>
								<td class="getcon1"></td>
								<td class="getcon2"></td>
								<td class="getcon"><table><tr><?=get_currency1(8)?></tr></table></td>
							</tr>
						</table>
						<!-- 환율정보 끝 -->
					</td>
					<td></td>
					<td style="background:#fff;padding:5px;">
						<!-- 계산기 -->&nbsp;
						<div id="cointoday_cal" class="tbl_Cal">
							<form id="productFrm" style="margin:0">
							<table>
								<tbody>
								<td class="first">물품가격<br /><span id="v_currency">$</span><input type="text" name="product_price" id="product_price" class="frm_input" size=7></td>
								<td><input type="radio" name="product_rate" id="product_rate1" value="1" checked> 공동구매 우대 환율적용
								<br /><br /><input type="radio" name="product_rate" id="product_rate2" value="2"> Ebay,Amazone 구매<br />우대환율미적용</td>
								<td><input type="radio" name="product_currency" id="product_currency1" value="1" onclick="$('#v_currency').html('$');" checked> 미국
								<br /><br /><input type="radio" name="product_currency" id="product_currency2" onclick="$('#v_currency').html('￥');" value="2"> 중국</td>
								<td><input type="radio" name="product_tax" value="1" id="product_tax1" checked> 0%<br />현행주화,<br />고대주화
								<br /><br /><input type="radio" name="product_tax" id="product_tax2" value="2"> 3%<br />금화,은괴
								<br /><br /><input type="radio" name="product_tax" id="product_tax3" value="3"> 8%<br />은화,쥬얼리,<br />은라운드,<br />코인제품</td>
								<td>부가세<br />(10%)<br />+<br />제비용<br />(2% or 3.5%)<br />+<br />코인스투데이<br />수수료(3%)</td>
								<td><div id="product_totalprice">0원</div> 
								<div id="cointoday_cal_btn">
								<input type="button" class="btn01" value="계산" onclick="product_cal();"><br /><br /><input type="button" value="초기화" onclick="product_reset();">
								</div>
								</tbody>
							</table>
							</form>
						</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td height="10px"></td></tr>
	
	<!-- 세계시각 -->
	<tr>
		<td>
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td background="<?=G5_URL?>/img/time_bg.jpg" width=800 height=100 align="center">
						
						<table border="0" cellspacing="0" cellpadding="0" width="750px" class="tb_time">
							<tr>
								<td>
									<?
									date_default_timezone_set("America/New_York");
									echo date("H:i");
									?>
								</td>
								<td class="space"></td>
								<td>
									<?
									date_default_timezone_set("Europe/London");
									echo date("H:i");
									?>
								</td>
								<td class="space"></td>
								<td>
									<?
									date_default_timezone_set("Europe/Paris");
									echo date("H:i");
									?>
								</td>
								<td class="space"></td>
								<td>
									<?
									date_default_timezone_set("Australia/Melbourne");
									echo date("H:i");
									?>
								</td>
								<td class="space"></td>
								<td>
									<?
									date_default_timezone_set("Asia/Shanghai");
									echo date("H:i");
									?>
								</td>
								<td class="space"></td>
								<td>
									<?
									date_default_timezone_set("Canada/Atlantic");
									echo date("H:i");
									?>
								</td>
							</tr>
						</table>

					</td>
				</tr>
			</table>
		</td>
	</tr>


	<tr><td height="10px"></td></tr>
	<!-- 배너 -->
	<tr>
		<td>
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td><img src="<?=G5_URL?>/img/new_bottom.jpg" border="0" align="absmiddle"></td>
				</tr>
			</table>
		</td>
	</tr>
	
	<tr><td height="30px"></td></tr>
</table>
<script type="text/javascript">

$(function(){
	$("#product_price").live("keyup",function(){
		product_cal();
	});

	$("#product_rate1,#product_rate2,#product_currency1,#product_currency2,#product_tax1,#product_tax2,#product_tax3").live("click",function(){
		product_cal();
	});
});

$(document).ready(function(){
	var con;
	var getCon;
	var chgCon;

	con = "";
	getCon = "";
	chgCon = "";

	$(".gall_text_comments").find("span").each(function(i){
	
		chgCon += $(".gall_text_comments").find("span").text();
	
	});

	con = $(".gall_text_comments").attr("con");
	$(".getContent").html(con);
	$(".getContent").find("img").remove();
	if($(".getContent").text().length > 70){
		getCon = $(".getContent").text().substring(0, 270) + "...";
	}else{
		getCon = $(".getContent").text();
	}
	$(".gall_text_comments").html(getCon);

	var data;
	var arr;
	var num;
	var gpnum;
	var num1;
	
	$(".getcon").each(function(i){
		data = "";
		arr = "";
		gpnum = "";	//공동구매 환율
		num = "";	//송금보낼때 환율
		num1 = "";	//매매기준율

		$(this).find("td").each(function(i){
			data += $(this).text() + ",";
		});
		data = data.substr(0, data.length-1);
		var arr = data.split(",");

		num = arr[3];
		num1 = arr[6];

		//$(".getcon").eq(i).html(arr[3]);

		$.ajax({
			type : "POST",
			dataType : "json",
			url : "./_Ajax.main.php",
			data : "num=" + num + "&num1=" + num1 + "&getcon=" + arr[3],
			success : function(data){
				var a = i +1
				$(".getcon").eq(i).html(data.getcon);
				$(".getcon1").eq(i).html(data.num_res);
				$(".getcon2").eq(i).html(data.num_res1);
			}
		});
	});

});
 
function product_reset(){
	$('#productFrm')[0].reset();
	
	$("#v_currency").html("$");
	$("#product_totalprice").html("0원");
}

function product_cal(){
	var product_price = $("#product_price").val();
	var totalPrice = 0;

	if(!product_price){ alert("물품가격을 입력하세요!"); $("#product_price").focus(); return; }

	if($("#product_rate1").attr("checked")){
		if($("#product_currency1").attr("checked")) totalPrice = product_price * <?php echo get_session("unit_kor_duty")?>;
		else if($("#product_currency2").attr("checked")) totalPrice = product_price * <?php echo get_session("unit_cha_duty")?>;
	}else if($("#product_rate2").attr("checked")){
		if($("#product_currency1").attr("checked")) totalPrice = product_price * <?php echo get_session("unit_kor")?>;
		else if($("#product_currency2").attr("checked")) totalPrice = product_price * <?php echo get_session("unit_cha")?>;
	}

	if($("#product_tax2").attr("checked")) totalPrice = totalPrice * 1.03;
	else if($("#product_tax3").attr("checked")) totalPrice = totalPrice * 1.08;


	if($("#product_rate1").attr("checked")) totalPrice = totalPrice * 1.15;
	else if($("#product_rate2").attr("checked")) totalPrice = totalPrice * 1.165;

	$("#product_totalprice").html(number_format(String(Round(totalPrice, 1 , "i")))+"원");
}

</script>

<?php
include_once(G5_PATH.'/tail.php');
?>