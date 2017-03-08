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

$rates1 = get_rates_ret(2);
$rates2 = get_rates_ret(6);
$rates3 = get_rates_ret(4);
$rates4 = get_rates_ret(9);
$rates5 = get_rates_ret(5);
$rates6 = get_rates_ret(8);

$gp_rates1 = ($rates1[3] - $rates1[6]) * 0.2 + $rates1[6];
$gp_rates1 = gp_rates_inc($rates1[3], $gp_rates1);

$gp_rates2 = ($rates2[3] - $rates2[6]) * 0.2 + $rates2[6];
$gp_rates2 = gp_rates_inc($rates2[3], $gp_rates2);

$gp_rates3 = ($rates3[3] - $rates3[6]) * 0.2 + $rates3[6];
$gp_rates3 = gp_rates_inc($rates3[3], $gp_rates3);

$gp_rates4 = ($rates4[3] - $rates4[6]) * 0.2 + $rates4[6];
$gp_rates4 = gp_rates_inc($rates4[3], $gp_rates4);

$gp_rates5 = ($rates5[3] - $rates5[6]) * 0.2 + $rates5[6];
$gp_rates5 = gp_rates_inc($rates5[3], $gp_rates5);

$gp_rates6 = ($rates6[3] - $rates6[6]) * 0.2 + $rates6[6];
$gp_rates6 = gp_rates_inc($rates6[3], $gp_rates6);
?>

<style>
#news_text {font-size:11px;font-weight:bold;padding:5px;color:#475055}
.rates td{font-size:11px;padding:7px 3px 7px 5px;text-align:right;font-weight:bold;}
.tbl_Cal td{font-size:11px;font-weight:bold;}

.tb_time{margin:15px 15px 0 13px;}
.tb_time td{width:112px;text-align:center;font-weight:bold;font-size:22px;}
.tb_time .space{width:18px;}
#etable td {font-size:11px;font-weight:bold;border-bottom:1px solid #eaeaea;text-align:center}
</style>


<div id="aside2"></div>

<div class="getContent" style="display:none;"></div>
<div class="getContent1" style="display:none;"><table><tr><?=get_currency1(2)?></tr></table></div>

<table border="0" cellspacing="0" cellpadding="0" width="100%" >
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

		<?
		$news_res = sql_query("
			select * from g5_write_news_gold_economy
			order by wr_id desc limit 0, 1
		");
		$news_num = mysql_num_rows($news_res);

		$news_res1 = sql_query("
			select * from g5_write_news_silver_news
			order by wr_id desc limit 0, 1
		");
		$news_num1 = mysql_num_rows($news_res1);

		if(!$news_num){
			$sise_row = sql_fetch("select * from g5_write_news_silver_news where wr_is_comment='0'
			order by wr_id desc limit 0, 1");
		}else if(!$news_num1){
			$sise_row = sql_fetch("select * from g5_write_news_gold_economy where wr_is_comment='0'
			order by wr_id desc limit 0, 1");
		}else{
			$sise1_row = sql_fetch("select * from g5_write_news_silver_news where wr_is_comment='0'
			order by wr_id desc limit 0, 1");

			$sise2_row = sql_fetch("select * from g5_write_news_gold_economy where wr_is_comment='0'
			order by wr_id desc limit 0, 1");

			if(strtotime($sise1_row[wr_datetime]) > strtotime($sise2_row[wr_datetime])){
				$sise_row = sql_fetch("select * from g5_write_news_silver_news where wr_is_comment='0'
				order by wr_id desc limit 0, 1");
			}else{
				
				$sise_row = sql_fetch("select * from g5_write_news_gold_economy where wr_is_comment='0'
				order by wr_id desc limit 0, 1");
			}
		}


		if($sise_row[wr_2]){
			$sise_img_row = sql_fetch("select * from {$g5['board_file_table']} where bo_table='".$sise_row[wr_2]."' and wr_id=".$sise_row[wr_id]." and bf_no='1' ");
			if(!$sise_img_row[bf_file]){
				$sise_img_row = sql_fetch("select * from {$g5['board_file_table']} where bo_table='".$sise_row[wr_2]."' and wr_id=".$sise_row[wr_id]." and bf_no='0' ");
			}
		}
		?>

		<div style="width:553px;background:#fff;float:left;height:430px`">
			<table border="0" cellspacing="0" cellpadding="0" width="100%">

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
					<td style="height:120px;padding:10px 50px 0 50px;line-height:1.5;" valign="top">
						<div class="gall_text_comments" con='<?=$sise_row['wr_content']?>'><?//=$sise_row['wr_content']?></div>
						<a href="<?=G5_URL?>/bbs/board.php?bo_table=<?=$sise_row[wr_2]?>"><font color="#ea6060">more</font></a>
					</td>
				</tr>
			</table>
		</div>
		
		<div style="float:left">
			<ul style="padding:0;margin:0 0 0 7px">
				<li style="float:left;list-style:none;width:240px">
					<table border="0" cellspacing="0" cellpadding="0" width="100%">
					<tr>
						<td>
							<div style="background:#002855;padding:10px 5px 25px 5px;">
								<div style="float:left;color:#56ccc8;">국내시세</div>
								<div style="float:right;color:#fff;"><?=date("Y.m.d")?></div>
							</div>
							<div id=etable style="background:#fff;height:175px;padding:10px 5px 10px 5px">
								<table border="0" cellspacing="0" cellpadding="0" width="100%">
									<tr>
										<td height="25" width="45">단위/\</td>
										<td style="color:#003ca5;">내가살때<br>(VAT 별도)</td>
										<td style="color:#003ca5">내가팔때</td>
										<td>+/-</td>
									</tr>
									<?php
									foreach($domesticPriceGubunList as $key=>$vars) {

										$sql = "select * from $g5[g5_domestic_price_table] where dp_gubun = '".$key."' order by dp_date desc limit 1";
										$row = sql_fetch($sql);
									?>
									<tr>
										<td style="color:<?php echo $domesticPriceGubunColorList[$key]?>" height="25"><?php echo $vars?></td>
										<td><?php echo number_format($row[dp_buy_price]);?></td>
										<td><?php echo number_format($row[dp_sell_price]);?></td>
										<td><?php if($row[dp_arrow]=="up")echo "+"; else echo "-";?> <?php echo number_format($row[dp_rate]);?></td>
									</tr>
									<?php }?>
								</table>

							</div>
						</td>
					</tr>
					<?
					$foreignGoldRate = get_session("metalJsonList");

					?>
					<tr>
						<td style="padding-top:5px">
							<div style="background:#002855;padding:10px 5px 25px 5px;">
								<div style="float:left;color:#56ccc8;">국제시세</div>
								<div style="float:right;color:#fff;"><?=date("Y.m.d")?></div>
							</div>
							<div id=etable style="background:#fff;height:120px;padding:10px 5px 10px 5px">
								<table border="0" cellspacing="0" cellpadding="0" width="100%">
									<tr>
										<td height="25" width="45">단위/$</td>
										<td style="color:#003ca5;">Bid</td>
										<td style="color:#003ca5">Ask</td>
										<td>+/-</td>
									</tr>
									<tr>
										<td style="color:#ff983c" height="25">순금</td>
										<td><?php echo $foreignGoldRate[Gold][bid]?></td>
										<td><?php echo $foreignGoldRate[Gold][ask]?></td>
										<td><?php if($foreignGoldRate[Gold][arrow]=="up")echo "+";?><?php echo $foreignGoldRate[Gold][change]?></td>
									</tr>								
									<tr>
										<td style="color:#005640" height="25">백금</td>
										<td><?php echo $foreignGoldRate[Platinum][bid]?></td>
										<td><?php echo $foreignGoldRate[Platinum][ask]?></td>
										<td><?php if($foreignGoldRate[Platinum][arrow]=="up")echo "+";?><?php echo $foreignGoldRate[Platinum][change]?></td>
									</tr>
									<tr>
										<td style="color:#002855" height="25">팔라듐</td>
										<td><?php echo $foreignGoldRate[Palladium][bid]?></td>
										<td><?php echo $foreignGoldRate[Palladium][ask]?></td>
										<td><?php if($foreignGoldRate[Palladium][arrow]=="up")echo "+";?><?php echo $foreignGoldRate[Palladium][change]?></td>
									</tr>
									<tr>
										<td style="color:#545454" height="25">은</td>
										<td><?php echo $foreignGoldRate[Silver][bid]?></td>
										<td><?php echo $foreignGoldRate[Silver][ask]?></td>
										<td><?php if($foreignGoldRate[Silver][arrow]=="up")echo "+";?><?php echo $foreignGoldRate[Silver][change]?></td>
									</tr>
								</table>

							</div>
						</td>
					</tr>

					</table>
				</li>
			</ul>
		</div>
		</td>
	</tr>
	<tr><td height="5px"></td></tr>

	<!-- 금화 경제 뉴스 -->
	<tr>
		<td>
		<div style="width:553px;float:left">
			<table border="0" cellspacing="0" cellpadding="0" width="553px">
				
				<tr style="cursor:pointer;background:#fff;" onclick="goto_url('<?=G5_URL?>/bbs/board.php?bo_table=news_gold_economy');">
					<td style="font-size:14px;font-weight:bold;padding:10px 10px 5px 20px;">금속 경제 뉴스</td>
					<td id="news_text" align="right" style="padding-right:10px">더보기 ></td>
				</tr>
				<tr><td height="2px"></td></tr>
				<tr style="background:#fff;">
					<td colspan="2" style="padding:15px 15px 10px 10px;;">
						<?=latest("clean_gallery1", "news_gold_economy", 3, 30);?>
					</td>
				</tr>
			</table>
		</div>

		<div style="float:left">
			<ul style="padding:0;margin:0 0 0 7px">
				<li style="float:left;list-style:none;width:240px">
					<table border="0" cellspacing="0" cellpadding="0" width="100%">
					<tr>
						<td>
							<div style="background:#002855;padding:10px 5px 25px 5px;">
								<div style="float:left;color:#56ccc8;">국제/국내 실시간 상품 차트</div>
								
							</div>
							<div id=etable style="background:#fff;height:175px;padding:10px 5px 10px 5px">
								<table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
									<tr>
										<td style="border:0px;">준비중입니다.</td>
									</tr>
								</table>

							</div>
						</td>
					</tr>


					</table>
				</li>
			</ul>
		</div>


		</td>
	</tr>
	<tr><td height="5px"></td></tr>

	<!-- 금화/은화 뉴스 -->
	<tr>
		<td>
			<div style="width:553px;float:left">
			<table border="0" cellspacing="0" cellpadding="0" width="553px">
				
				<tr style="cursor:pointer;background:#fff;" onclick="goto_url('<?=G5_URL?>/bbs/board.php?bo_table=news_silver_news');">
					<td style="font-size:14px;font-weight:bold;padding:10px 10px 5px 20px;">금화/은화 뉴스</td>
					<td id="news_text" align="right" style="padding-right:10px">더보기 ></td>
				</tr>
				<tr><td height="2px"></td></tr>
				<tr style="background:#fff;">
					<td colspan="2" style="padding:15px 15px 10px 10px;">
						<?=latest("clean_gallery1", "news_silver_news", 3, 30);?>
					</td>
				</tr>
			</table>
			</div>
			
			<div style="float:left">
				<ul style="padding:0;margin:0 0 0 7px">
					<li style="float:left;list-style:none;width:240px">
						<table border="0" cellspacing="0" cellpadding="0" width="100%">
						<tr>
							<td>
								<div style="background:#002855;padding:10px 5px 25px 5px;">
									<div style="float:left;color:#56ccc8;">Gold/Silver Ratio Chart</div>
									
								</div>
								<div id=etable style="background:#fff;height:175px;padding:10px 5px 10px 5px">
									<table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
									<tr>
										<td style="border:0px;">준비중입니다.</td>
									</tr>
								</table>

								</div>
							</td>
						</tr>


						</table>
					</li>
				</ul>
			</div>

		</td>
	</tr>
	<tr><td height="5px"></td></tr>

	<!-- 돈버는 뉴스 & 정보 -->
	<tr>
		<td>
			<div style="width:553px;float:left">
			<table border="0" cellspacing="0" cellpadding="0" width="553px">
				
				<tr style="cursor:pointer;background:#fff;" onclick="goto_url('<?=G5_URL?>/bbs/board.php?bo_table=news_info');">
					<td style="font-size:14px;font-weight:bold;padding:10px 10px 5px 20px;">돈버는 뉴스 & 정보</td>
					<td id="news_text" align="right" style="padding-right:10px">더보기 ></td>
				</tr>
				<tr><td height="2px"></td></tr>
				<tr style="background:#fff;">
					<td colspan="2" style="padding:10px;">
						<?=latest("clean_gallery2", "news_info", 20, 30);?>
					</td>
				</tr>
			</table>
			</div>

			<div style="float:left">
				<ul style="padding:0;margin:0 0 0 7px">
					<li style="float:left;list-style:none;width:240px">
						<table border="0" cellspacing="0" cellpadding="0" width="100%">
						<tr>
							<td>
								<div style="background:#002855;padding:10px 5px 25px 5px;">
									<div style="float:left;color:#56ccc8;">Dollar index Chart</div>
									
								</div>
								<div id=etable style="background:#fff;height:175px;padding:10px 5px 10px 5px">

									<!--
									<table border="0" cellspacing="0" cellpadding="0" width="100%">
										<A HREF="http://www.kitco.com/connecting.html">
										<IMG SRC="http://www.kitconet.com/charts/metals/palladium/tny_pd_xx_usoz_4.gif" BORDER="0" ALT="[Most Recent Quotes from www.kitco.com]"></A>
									</table>
									-->

									<table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
										<tr>
											<td style="border:0px;">준비중입니다.</td>
										</tr>
									</table>

								</div>
							</td>
						</tr>


						</table>
					</li>
				</ul>
			</div>


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
								<td class="getcon1"><?=$gp_rates1[0]?></td>
								<td class="getcon2"><?=$gp_rates1[1]?></td>
								<td class="getcon"><?=$rates1[3]?><input type="hidden" name="getcon[]" value="<?=$rates1[6]?>"></td>
							</tr>
							<tr>
								<td style="text-align:left;"><img src="<?=G5_URL?>/img/news_country2.gif" border="0" align="absmiddle"> 영국</td>
								<td class="getcon1"><?=$gp_rates2[0]?></td>
								<td class="getcon2"><?=$gp_rates2[1]?></td>
								<td class="getcon"><?=$rates2[3]?><input type="hidden" name="getcon[]" value="<?=$rates2[6]?>"></td>
							</tr>
							<tr>
								<td style="text-align:left;"><img src="<?=G5_URL?>/img/news_country3.gif" border="0" align="absmiddle"> 유럽연합</td>
								<td class="getcon1"><?=$gp_rates3[0]?></td>
								<td class="getcon2"><?=$gp_rates3[1]?></td>
								<td class="getcon"><?=$rates3[3]?><input type="hidden" name="getcon[]" value="<?=$rates3[6]?>"></td>
							</tr>
							<tr>
								<td style="text-align:left;"><img src="<?=G5_URL?>/img/news_country4.gif" border="0" align="absmiddle"> 호주</td>
								<td class="getcon1"><?=$gp_rates4[0]?></td>
								<td class="getcon2"><?=$gp_rates4[1]?></td>
								<td class="getcon"><?=$rates4[3]?><input type="hidden" name="getcon[]" value="<?=$rates4[6]?>"></td>
							</tr>
							<tr>
								<td style="text-align:left;"><img src="<?=G5_URL?>/img/news_country5.gif" border="0" align="absmiddle"> 중국</td>
								<td class="getcon1"><?=$gp_rates5[0]?></td>
								<td class="getcon2"><?=$gp_rates5[1]?></td>
								<td class="getcon"><?=$rates5[3]?><input type="hidden" name="getcon[]" value="<?=$rates5[6]?>"></td>
							</tr>
							<tr>
								<td style="text-align:left;"><img src="<?=G5_URL?>/img/news_country6.gif" border="0" align="absmiddle"> 캐나다</td>
								<td class="getcon1"><?=$gp_rates6[0]?></td>
								<td class="getcon2"><?=$gp_rates6[1]?></td>
								<td class="getcon"><?=$rates6[3]?><input type="hidden" name="getcon[]" value="<?=$rates6[6]?>"></td>
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
					<td><img src="<?=G5_URL?>/img/new_bottom.jpg" border="0" align="absmiddle" usemap='#new_bottom'></td>
				</tr>
			</table>
		</td>
	</tr>
	
	<tr><td height="30px"></td></tr>
</table>


<map name="new_bottom" id="new_bottom">
	<area shape="rect" coords="0,0,154,44" href="http://www.ramint.gov.au/" target="_blank" />	
	<area shape="rect" coords="163,0,314,44" href="http://www.mint.ca/store/template/home.jsp" target="_blank" />	
	<area shape="rect" coords="323,0,478,44" href="http://www.monnaiedeparis.fr/" target="_blank" />	
	<area shape="rect" coords="486,0,641,44" href="http://www.pobjoy.com/" target="_blank" />	
	<area shape="rect" coords="649,0,798,44" href="http://www.royalmint.com/" target="_blank" />	

	<area shape="rect" coords="0,50,154,94" href="http://www.perthmint.com.au/" target="_blank" />	
	<area shape="rect" coords="163,50,314,94" href="http://www.samint.co.za/" target="_blank" />	
	<area shape="rect" coords="323,50,489,94" href="http://www.usmint.gov/" target="_blank" />	
	<area shape="rect" coords="486,50,641,94" href="https://www.deutsche-sammlermuenzen.de/" target="_blank" />	
	<area shape="rect" coords="649,50,798,94" href="http://www.chngc.net/Main/" target="_blank" />	
</map>


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