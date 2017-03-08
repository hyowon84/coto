<?php
include_once('./_common.php');
include_once('./_head.php');

if(!$member[mb_id]){
	alert("로그인 하시기 바랍니다.");
}

$metalUsdPrice = get_session("metalUsdPrice");

$cnt = sql_fetch("select SUM(wr_4 * wr_3) as wr_4, SUM(wr_5) as wr_5, SUM(wr_6) as wr_6 from g5_write_portfolio where mb_id='".$member[mb_id]."' ");

$gold = metal_sum("GL");
$gold_now = metal_sum_now("GL");
$silver = metal_sum("SL");
$silver_now = metal_sum_now("SL");
$pt = metal_sum("PT");
$pt_now = metal_sum_now("PT");
$pd = metal_sum("PD");
$pd_now = metal_sum_now("PD");
$other = metal_sum("other");
$other_now = metal_sum_now("other");

?>
<script src="<?=G5_URL?>/js/jquery.knob.js"></script>
<script>
$(function($) {
	$(".knob").knob({
		change : function (value) {
			//console.log("change : " + value);
		},
		release : function (value) {
			//console.log(this.$.attr('value'));
			console.log("release : " + value);
		},
		cancel : function () {
			console.log("cancel : ", this);
		},
		/*format : function (value) {
			return value + '%';
		},*/
		draw : function () {
			// "tron" case
			if(this.$.data('skin') == 'tron') {
				this.cursorExt = 0.3;
				var a = this.arc(this.cv)  // Arc
					, pa                   // Previous arc
					, r = 1;
				this.g.lineWidth = this.lineWidth;
				if (this.o.displayPrevious) {
					pa = this.arc(this.v);
					this.g.beginPath();
					this.g.strokeStyle = this.pColor;
					this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, pa.s, pa.e, pa.d);
					this.g.stroke();
				}
				this.g.beginPath();
				this.g.strokeStyle = r ? this.o.fgColor : this.fgColor ;
				this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, a.s, a.e, a.d);
				this.g.stroke();
				this.g.lineWidth = 2;
				this.g.beginPath();
				this.g.strokeStyle = this.o.fgColor;
				this.g.arc( this.xy, this.xy, this.radius - this.lineWidth + 1 + this.lineWidth * 2 / 3, 0, 2 * PI, false);
				this.g.stroke();
				return false;
			}
		}
	});
});
</script>

<div id="aside2"></div>

<div id="portfolio">
	<div class="top">
		<ul>
			<li class="title">포트폴리오</li>
			<li class="nav">홈 > 포트폴리오</li>
		</ul>
	</div>

	<div class="line1"></div>
	<div class="line2"></div>

	<!-- 합계 -->
	<div class="port_sum">
		<ul>
			<li class="port_sum_l">
				<span class="font_bold"><?php echo $member[mb_name];?>[<?php echo $member[mb_nick];?>]</span><span class="font_basic"> 님의 전체 투자 금액</span>
				<span class="font_bold1 cl"><?php echo date("Y/m/d");?> 현재</span><span class="font_color">&nbsp;모든METALS</span>
			</li>
			<li class="port_sum_r">
				<div class="font_color1"><?php echo $cnt[wr_4];?>oz</div>
				<div class="font_color2">$<?php echo $cnt[wr_5];?></div>
				<div class="font_color3">\ <?php echo number_format($cnt[wr_6]);?></div>
			</li>
		</ul>
	</div>

	<div class="line1"></div>
	<div class="line2"></div>

	<!-- 금속자산 시가 -->
	<div class="port_siga_tile">금속자산 시가</div>
	<div class="port_siga_box">
		<div class="box">
			<ul>
				<li>
					<div class="title">금</div>
					<div class="price">
						<div class="price1">$<?php echo $gold[wr_5];?></div>
						<div class="price2">\<?php echo number_format($gold[wr_6]);?></div>
						<div class="price3"><?php echo $gold[wr_4];?>oz</div>
					</div>
				</li>
				<li class="line_l"></li>
				<li class="line_r"></li>
				<li>
					<div class="title">은</div>
					<div class="price">
						<div class="price1">$<?php echo $silver[wr_5];?></div>
						<div class="price2">\<?php echo number_format($silver[wr_6]);?></div>
						<div class="price3"><?php echo $silver[wr_4];?>oz</div>
					</div>
				</li>
				<li class="line_l"></li>
				<li class="line_r"></li><li>
					<div class="title">백금</div>
					<div class="price">
						<div class="price1">$<?php echo $pt[wr_5];?></div>
						<div class="price2">\<?php echo number_format($pt[wr_6]);?></div>
						<div class="price3"><?php echo $pt[wr_4];?>oz</div>
					</div>
				</li>
				<li class="line_l"></li>
				<li class="line_r"></li><li>
					<div class="title">팔라듐</div>
					<div class="price">
						<div class="price1">$<?php echo $pd[wr_5];?></div>
						<div class="price2">\<?php echo number_format($pd[wr_6]);?></div>
						<div class="price3"><?php echo $pd[wr_4];?>oz</div>
					</div>
				</li>
				<li class="line_l"></li>
				<li class="line_r"></li><li>
					<div class="title">기타</div>
					<div class="price">
						<div class="price1">$<?php echo $other[wr_5];?></div>
						<div class="price2">\<?php echo number_format($other[wr_6]);?></div>
						<div class="price3"><?php echo $other[wr_4];?>oz</div>
					</div>
				</li>
			</ul>
		</div>
	</div>

	<div class="line1"></div>
	<div class="line2"></div>

	<!-- 자산분배 -->
	<div class="port_divi_tile">자산분배</div>
	<div class="port_divi_box">
		<div class="title">
			<ul>
				<li class="t1">금속</li>
				<li class="t2">수량</li>
				<li class="t3">온스</li>
				<li class="t4">구입가</li>
				<li class="t5">현재가</li>
				<li class="t6">차익</li>
			</ul>
		</div>
		<div class="list">
			<ul>
				<li class="l1">금</li>
				<li class="l2"><?php echo number_format($gold[wr_3]);?></li>
				<li class="l3"><?php echo $gold[wr_4];?></li>
				<li class="l4">
					$<?php echo $gold[wr_5];?></br>
					\<?php echo number_format($gold[wr_6]);?>
				</li>
				<li class="l5">
					$<?php echo $gold_now[usd];?></br>
					\<?php echo number_format($gold_now[ko]);?>
				</li>
				<li class="l6">
					$<?php echo round($gold_now[usd]-$gold[wr_5], 2);?></br>
					\<?php echo number_format($gold_now[ko]-$gold[wr_6]);?>
				</li>
			</ul>
			<ul><li class="line"></li></ul>
			<ul>
				<li class="l1">은</li>
				<li class="l2"><?php echo number_format($silver[wr_3]);?></li>
				<li class="l3"><?php echo $silver[wr_4];?></li>
				<li class="l4">
					$<?php echo $silver[wr_5];?></br>
					\<?php echo number_format($silver[wr_6]);?>
				</li>
				<li class="l5">
					$<?php echo $silver_now[usd];?></br>
					\<?php echo number_format($silver_now[ko]);?>
				</li>
				<li class="l6">
					$<?php echo round($silver_now[usd]-$silver[wr_5], 2);?></br>
					\<?php echo number_format($silver_now[ko]-$silver[wr_6]);?>
				</li>
			</ul>
			<ul><li class="line"></li></ul>
			<ul>
				<li class="l1">백금</li>
				<li class="l2"><?php echo number_format($pt[wr_3]);?></li>
				<li class="l3"><?php echo $pt[wr_4];?></li>
				<li class="l4">
					$<?php echo $pt[wr_5];?></br>
					\<?php echo number_format($pt[wr_6]);?>
				</li>
				<li class="l5">
					$<?php echo $pt_now[usd];?></br>
					\<?php echo number_format($pt_now[ko]);?>
				</li>
				<li class="l6">
					$<?php echo round($pt_now[usd]-$pt[wr_5], 2);?></br>
					\<?php echo number_format($pt_now[ko]-$pt[wr_6]);?>
				</li>
			</ul>
			<ul><li class="line"></li></ul>
			<ul>
				<li class="l1">팔라듐</li>
				<li class="l2"><?php echo number_format($pd[wr_3]);?></li>
				<li class="l3"><?php echo $pd[wr_4];?></li>
				<li class="l4">
					$<?php echo $pd[wr_5];?></br>
					\<?php echo number_format($pd[wr_6]);?>
				</li>
				<li class="l5">
					$<?php echo $pd_now[usd];?></br>
					\<?php echo number_format($pd_now[ko]);?>
				</li>
				<li class="l6">
					$<?php echo round($pd_now[usd]-$pd[wr_5], 2);?></br>
					\<?php echo number_format($pd_now[ko]-$pd[wr_6]);?>
				</li>
			</ul>
			<ul><li class="line"></li></ul>
			<ul>
				<li class="l1">기타</li>
				<li class="l2"><?php echo number_format($other[wr_3]);?></li>
				<li class="l3"><?php echo $other[wr_4];?></li>
				<li class="l4">
					$<?php echo $other[wr_5];?></br>
					\<?php echo number_format($other[wr_6]);?>
				</li>
				<li class="l5">
					$<?php echo $other_now[usd];?></br>
					\<?php echo number_format($other_now[ko]);?>
				</li>
				<li class="l6">
					$<?php echo round($other_now[usd]-$other[wr_5], 2);?></br>
					\<?php echo number_format($other_now[ko]-$other[wr_6]);?>
				</li>
			</ul>
		</div>
	</div>

	<!-- 그래프 -->
	<div class="cl graph">
		<div class="back"></div>
		<ul>
			<li>
				<div class="percent"><?=port_percent($member[mb_id], "GL")?>%</div>
				<input class="knob" data-width="96" data-height="96" data-min="0" data-displayPrevious=true value="<?=port_percent($member[mb_id], "GL")?>" style="display:none;"></br>
				금
			</li>
			<li>
				<div class="percent"><?=port_percent($member[mb_id], "SL")?>%</div>
				<input class="knob" data-width="96" data-height="96" data-min="0" data-displayPrevious=true value="<?=port_percent($member[mb_id], "SL")?>" style="display:none;"></br>
				은
			</li>
			<li>
				<div class="percent"><?=port_percent($member[mb_id], "PT")?>%</div>
				<input class="knob" data-width="96" data-height="96" data-min="0" data-displayPrevious=true value="<?=port_percent($member[mb_id], "PT")?>" style="display:none;"></br>
				백금
			</li>
			<li>
				<div class="percent"><?=port_percent($member[mb_id], "PD")?>%</div>
				<input class="knob" data-width="96" data-height="96" data-min="0" data-displayPrevious=true value="<?=port_percent($member[mb_id], "PD")?>" style="display:none;"></br>
				팔라듐
			</li>
			<li>
				<div class="percent"><?=port_percent($member[mb_id], "")?>%</div>
				<input class="knob" data-width="96" data-height="96" data-min="0" data-displayPrevious=true value="<?=port_percent($member[mb_id], "")?>" style="display:none;"></br>
				기타
			</li>
		</ul>
	</div>

	<div class="interval_20"></div>
	<div class="line1"></div>
	<div class="line2"></div>

	<!-- 자산리스트 -->
	<div class="cl assets_tile">
		<div style="float:left;width:370px;">자산리스트 <span style="margin:0 0 0 20px;color:#cfcfcf;font-size:12px;font-weight:normal;cursor:pointer;" onclick="goto_url('<?=G5_URL?>/bbs/board.php?bo_table=portfolio');">더보기 ></span></div>
		<div class="cate cate1" style="width:auto;">
			<div style="width:120px;" ca_name=""><img src="<?=G5_URL?>/img/assets_icon.png" border="0" align="absmiddle">&nbsp;&nbsp;&nbsp;&nbsp;모든 METALS</div>
			<div ca_name="GL">금</div>
			<div ca_name="SL">은</div>
			<div ca_name="PT">백금</div>
			<div ca_name="PD">팔라듐</div>
			<div ca_name="OTHER">기타</div>
		</div>
	</div>
	<div class="cl assets_list_box">
		<div class="box">
			<ul>
				<li class="t1"><?php echo date("Y/m/d")?> 현재</li>
				<li class="t2">모든METALS</li>
				<li class="t3"><?php echo $cnt[wr_4];?>oz</li>
				<li class="t4">투자금액</li>
				<li class="t5">$ <?php echo $cnt[wr_5];?></li>
				<li class="t6">\ <?php echo number_format($cnt[wr_6]);?></li>
			</ul>
		</div>
	</div>

	<div class="cl assets_list">
		<?php
		$sql_que .= " and mb_id='".$member[mb_id]."' ";

		if($ca_name){
			if($ca_name == "OTHER"){
				$sql_que .= " and wr_2 not in('GL', 'SL', 'PT', 'PD') ";
			}else{
				$sql_que .= " and wr_2='$ca_name' ";
			}
		}else{
			$sql_que .= "";
		}

		$board = sql_fetch("select * from {$g5['board_table']} where bo_table='portfolio'");

		if ($board['bo_sort_field']) {
			$sst = $board['bo_sort_field'];
		} else {
			$sst  = "wr_num, wr_reply";
		}

		$port_res = sql_query("select * from g5_write_portfolio where 1 and mb_id='".$member[mb_id]."' $sql_que order by $sst limit 0, 4");
		?>
		<ul>

			<?php
			for($i = 0; $port_row = mysql_fetch_array($port_res); $i++){
				$thumb = $port_row['wr_1'];
							
				if(stristr($thumb, "http")) {
					$img_content = '<img src="'.$thumb.'" alt="'.$thumb.'" width="150px" height="150px">';
				} else {
					$img_content = '<img src="'.G5_URL."/data/file/portfolio/".$thumb.'" alt="'.$thumb.'" width="150px" height="150px">';
				}

				$it = sql_fetch("select * from {$g5['g5_shop_item_table']} where it_name='".$port_row['wr_subject']."' ");
				$gp = sql_fetch("select * from {$g5['g5_shop_group_purchase_table']} where gp_name='".$port_row['wr_subject']."' ");

				$price1 = $port_row['wr_5'];
				$price2 = get_dollar($it);

				if($port_row[wr_7] == "N"){
					$price2 = get_dollar($it) * $port_row[wr_3];
					$price1_ko = $port_row['wr_6'];
					$price2_ko = get_price($it) * $port_row[wr_3];
				}else if($port_row[wr_7] == "P"){
					$price2 = getGroupPurchaseQtyBasicUSD($gp[gp_id], 1) * $port_row[wr_3];
					$price1_ko = $port_row['wr_6'];
					$price2_ko = getGroupPurchaseQtyBasicPrice($gp[gp_id], 1) * $port_row[wr_3];
				}

				if ($i == 0) $k = 0;
				$k += 1;
				if ($k % 2 == 0){
			?>
			<li>
			<?}else{?>
			<li class="li_l">
			<?}?>
				<div class="top">
					<div class="img"><?php echo $img_content;?></div>
					<div class="info">
						<div class="title"><?php echo cut_str($port_row['wr_subject'], 30); ?></div>
						<div class="info1">
							<span style="float:left;font-weight:bold;">금속</span>
							<span style="float:right;"><?php echo metal_type($port_row['wr_2']) ?></span>
							<span class="cl" style="float:left;font-weight:bold;">수량</span>
							<span style="float:right;"><?php echo $port_row['wr_3'] ?></span>
							<span class="cl" style="float:left;font-weight:bold;">온스</span>
							<span style="float:right;"><?php echo $port_row['wr_4'] ?></span>
						</div>
					</div>
				</div>
				<div class="cl line"></div>
				<div class="bottom">
					<div class="t1">
						<div class="s1">구입가</div>
						<div class="s2">$<?php echo $price1 ?></div>
						<div class="s3">\<?php echo number_format($price1_ko) ?></div>
					</div>
					<div class="t2">
						<div class="s1">현재가</div>
						<div class="s2">$<?php echo $price2 ?></div>
						<div class="s3">\<?php echo number_format($price2_ko)?></div>
					</div>
					<div class="t3">
						<div class="s1">차익</div>
						<div class="s2">$<?php echo round($price2 - $price1, 2);?></div>
						<div class="s3">\<?php echo number_format($price2_ko - $price1_ko);?></div>
					</div>
				</div>
			</li>
			<?php
			}
			?>
			
		</ul>
	</div>
</div>



<script type="text/javascript">

$(document).ready(function(){
	$(".cate1").find("div").click(function(){
		var ca_name = $(this).attr("ca_name");
		
		$(".cate1").find("div").each(function(i){
			if(ca_name == $(".cate1").find("div").eq(i).attr("ca_name")){
				location.href = "<?=G5_URL?>/portfolio/portfolio.php?ca_name=" + ca_name;
			}
		});
	});
});

</script>

<?php
include_once(G5_PATH.'/tail.php');
?>