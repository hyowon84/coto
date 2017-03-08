<?php
include_once('./_common.php');
//실시간 공동구매 현황 목록

if (!$member[mb_id]) alert("로그인후에 이용가능합니다.",G5_BBS_URL."/login.php");
if($member['mb_level']<1)alert("접근권한이 없습니다.");

// 분류
$ca_list  = '<option value="">선택</option>'.PHP_EOL;
$sql = " select * from {$g5['g5_shop_category_table']} ";
if ($is_admin != 'super')
    $sql .= " where ca_mb_id = '{$member['mb_id']}' ";
$sql .= " order by ca_id ";
$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++)
{
    $len = strlen($row['ca_id']) / 2 - 1;
    $nbsp = '';
    for ($i=0; $i<$len; $i++) {
        $nbsp .= '&nbsp;&nbsp;&nbsp;';
    }
    $ca_list .= '<option value="'.$row['ca_id'].'">'.$nbsp.$row['ca_name'].'</option>'.PHP_EOL;
}

$where = " and ";
$sql_search = "";
if ($stx != "") {
    if ($sfl != "") {
		if($sfl == "mb_nick") {
			$nick_row = sql_fetch("select * from {$g5['member_table']} where mb_nick='".$stx."' ");
			$sql_search .= " $where a.mb_id like '".$nick_row[mb_id]."' ";
		}else{
			$sql_search .= " $where a.$sfl like '%$stx%' ";
		}
        $where = " and ";
    }
    if ($save_stx != $stx)
        $page = 1;
}

if ($sca != "") {
    $sql_search .= " $where (b.ca_id like '$sca%') ";
}


$sql_search .= " and (";




$gp_cnt_pay_res = sql_query("select * from {$g5['g5_group_cnt_pay_table']}");
for($i = 0; $gp_cnt_pay_row = mysql_fetch_array($gp_cnt_pay_res); $i++){
	
	if($gp_cnt_pay_row[group_code]){
		$sql_search .= " a.total_amount_code='".$gp_cnt_pay_row[group_code]."' or ";
	}else{
		$total_amount_row = sql_fetch("
			select * from {$g5['g5_total_amount_table']}
			where dealer='".$gp_cnt_pay_row[gubun_code]."'
			order by no desc limit 0, 1
		");

		$sql_search .= " a.total_amount_code='".$total_amount_row[type_code]."' or ";
	}
}






$sql_search = substr($sql_search, 0, strlen($sql_search)-3);
$sql_search .= ")";


// 상태분류
if($ct_status){
	$sql_search .= " and ct_gp_status='$ct_status' ";
}

if($ct_type){
	$gp_per = (int)$percent[$ct_type];
	$sql_search .= " and ct_type='".$ct_type."' ";
}else{
	$gp_per = (int)$percent[$ct_type];
	$sql_search .= " and ct_type!='' ";
}

//결제예상금액
$sql = " select SUM( ct_price * (ct_qty - ct_buy_qty) ) AS sum_price
		   from {$g5['g5_shop_cart_table']} a left join {$g5['g5_shop_group_purchase_table']} b on ( a.it_id = b.gp_id )
		  where a.ct_type != '' $sql_search order by a.ct_id desc";

$sum_price = sql_fetch($sql);

// 테이블의 전체 레코드수만 얻음
$sql = " select count(*) as cnt
		   from {$g5['g5_shop_cart_table']} a left join {$g5['g5_shop_group_purchase_table']} b on ( a.it_id = b.gp_id )
		  where a.ct_type != '' $sql_search order by a.ct_id desc";

$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page == "") { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

if (!$sst) {
    $sst  = "ct_id";
    $sod = "desc";
}
$sql_order = "order by $sst $sod";

//total_amount_code
$sql_real = " 	SELECT	*
						FROM		g5_shop_cart	a
										left join g5_shop_group_purchase b on ( a.it_id = b.gp_id )
					 	WHERE	a.ct_type != ''
					 	AND		a.ct_status NOT LIKE '%취소%'
					 	$sql_search
						ORDER	BY	a.ct_id	DESC
						LIMIT	$from_record, $rows ";

$result_real = sql_query($sql_real);
echo $sql_real;


//$qstr  = $qstr.'&amp;sca='.$sca.'&amp;page='.$page;
$qstr  = $qstr.'&amp;sca='.$sca.'&amp;page='.$page.'&amp;save_stx='.$stx;

$g5['title'] = "실시간 공동구매 현황";
if (G5_IS_MOBILE) {
	include_once(G5_MSHOP_PATH.'/head.php');
} else {
	include_once('./_head.php');
}

if($ct_type) {
	$gp_per = (int)$percent[$ct_type];
	$gp_date = sql_fetch("select * from {$g5['g5_group_cnt_pay_table']} where fr_date != '0000-00-00' and gubun_code='".$ct_type."' order by fr_date asc limit 0, 1 ");
} else {
	$gp_per = "";
	$gp_date = sql_fetch("select * from {$g5['g5_group_cnt_pay_table']} where fr_date != '0000-00-00' order by fr_date asc limit 0, 1 ");
}
if($gp_date[f_date]){
	$date = date("Y/m/d", $gp_date[f_date]);
}else{
	$date = "";
}

?>

<h2 class="title" style="font-size:27px;font-family:NanumGothic;"><?=$g5['title']?></h2>
<div class="bg" style="background:#fff;margin:15px 0 0 0;padding:5px 0 0 0;">
	<div id="real_tab">
		<ul>
			<? if (G5_IS_MOBILE) { ?>
				<li <?if($ct_type == ""){?>class="on"<?}?>><a href="<?=G5_URL?>/shop/realtime_gp.php" style="vertical-align:middle;">ALL</a></li>
				<li <?if($ct_type == "2010"){?>class="on"<?}?>><a href="<?=G5_URL?>/shop/realtime_gp.php?ct_type=2010"><div class="name"><p>APMEX</p></div><div class="percent"><p><?=(int)$percent[2010]?>%</p></div></a></li>
				<li <?if($ct_type == "2020"){?>class="on"<?}?>><a href="<?=G5_URL?>/shop/realtime_gp.php?ct_type=2020"><div class="name"><p>GainsVille<br/>Coins</p></div><div class="percent"><p><?=(int)$percent[2020]?>%</p></div></a></li>
				<li <?if($ct_type == "2040"){?>class="on"<?}?>><a href="<?=G5_URL?>/shop/realtime_gp.php?ct_type=2040"><div class="name"><p>Scottsdale<br/>Silver</p></div><div class="percent"><p><?=(int)$percent[2040]?>%</p></div></a></li>
				<li <?if($ct_type == "2030"){?>class="on"<?}?>><a href="<?=G5_URL?>/shop/realtime_gp.php?ct_type=2030"><div class="name"><p>MCM</p></div><div class="percent"><p><?=(int)$percent[2030]?>%</p></div></a></li>
				<li <?if($ct_type == "2050"){?>class="on"<?}?>><a href="<?=G5_URL?>/shop/realtime_gp.php?ct_type=2050"><div class="name"><p>OTHER<br/>DEALER</p></div><div class="percent"><p><?=(int)$percent[2050]?>%</p></div></a></li>
			<?} else {?>
				<li <?if($ct_type == ""){?>class="on"<?}?> onclick="goto_url('<?=G5_URL?>/shop/realtime_gp.php');"><a href="javascript:void(0)">ALL</a></li>
				<li <?if($ct_type == "2010"){?>class="on"<?}?> onclick="goto_url('<?=G5_URL?>/shop/realtime_gp.php?ct_type=2010');">APMEX</li>
				<li <?if($ct_type == "2020"){?>class="on"<?}?> onclick="goto_url('<?=G5_URL?>/shop/realtime_gp.php?ct_type=2020');">GainsVille Coins</li>
				<li <?if($ct_type == "2040"){?>class="on"<?}?> onclick="goto_url('<?=G5_URL?>/shop/realtime_gp.php?ct_type=2040');">Scottsdale Silver</li>
				<li <?if($ct_type == "2030"){?>class="on"<?}?> onclick="goto_url('<?=G5_URL?>/shop/realtime_gp.php?ct_type=2030');">MCM</li>
				<li <?if($ct_type == "2050"){?>class="on"<?}?> onclick="goto_url('<?=G5_URL?>/shop/realtime_gp.php?ct_type=2050');">OTHER DEALER</li>
			<?}?>
		</ul>
	</div>

	<form name="freal_search" id="freal_search" method="GET">
	<input type="hidden" name="ct_type" value="<?=$ct_type?>">
	<div id="real_sch">
		<ul>
			<li class="right">
				<select name="sfl" id="sfl" style="border:1px #e1e1e1 solid;color:#545454;height:33px;">
					<option value="mb_nick" <?php echo get_selected($sfl, 'mb_nick'); ?>>닉네임</option>
					<option value="it_name" <?php echo get_selected($sfl, 'it_name'); ?>>상품명</option>
				</select>
				<input type="text" name="stx" id="stx" style="border:1px #e1e1e1 solid;width:250px;height:33px;" value="<?=$stx?>">
				<input type="submit" class="submit" value="검색" style="color:#fff;background:#cfcfcf;border:0px;font-weight:bold;padding:9px;cursor:pointer;">
			</li>
		</ul>
	</div>
	</form>


	<div id="real_dt">
		<ul>
			<li class="right">
				<img src="<?=G5_URL?>/img/real_ico.gif" border="0" align="absmiddle">
				구매자[닉네임]으로 구매내역 검색이 가능합니다.
			</li>
		</ul>
	</div>

	<div class="statusContainer" style="display:none;">
		<span class="endDate">
			<p class="title">공동구매 종료일자</p>
			<p class="date"></p>
		</span>

		<span class="countContainer">
			<span class="label">
				<p>남은시간<br/><i class="fa fa-clock-o"></i></p>
			</span>
			<span class="jointPurchaseDate"><?=$date?></span>
		</span>

		<div class="circleContainer">
			<div class="c100 p<?=$gp_per?> circle">
				<span><?=$gp_per?>%</span>
				<div class="slice">
					<div class="bar"></div>
					<div class="fill"></div>
				</div>
			</div>
		</div>
	</div>

	<!-- 실시간공동구매현황 시작 { -->
	<div id="sod_ws">
		<form name="fwishlist" method="post" action="./gp_cartupdate.php">
		<input type="hidden" name="act"       value="multi">
		<input type="hidden" name="sw_direct" value="">
		<input type="hidden" name="prog"      value="wish">
		<input type="hidden" name="buy_kind" value="공동구매">

		<div class="tbl_head01 tbl_wrap">
		<? if (G5_IS_MOBILE) {?>
			<ul>
			<?
			for ($i=0; $row=mysql_fetch_array($result_real); $i++) {
				$href = G5_SHOP_URL.'/grouppurchase.php?gp_id='.$row['it_id'];
				$bg = 'bg'.($i%2);

				if($row[ct_type] == "2010"){$ct_type = "APMEX";}
				else if($row[ct_type] == "2020"){$ct_type = "GAINSVILLE";}
				else if($row[ct_type] == "2030"){$ct_type = "MCM";}
				else if($row[ct_type] == "2040"){$ct_type = "SCOTTS DALE";}
				else{$ct_type = "OTHER DEALER";}

				$image = get_gp_image($row['it_id'], 70, 70);
				$gp_row = sql_fetch("select * from {$g5['g5_shop_group_purchase_table']} where gp_id='".$row[it_id]."' ");
				$member_row = sql_fetch("select * from {$g5['member_table']} where mb_id='".$row[mb_id]."' ");

				if($row[ct_buy_qty]){
					$ct_buy_qty = $row[ct_buy_qty];
				}else{
					$ct_buy_qty = 0;
				}

				$gp_price = getGroupPurchaseBasicPrice($row[gp_id]);
				?>

				<li class="rtLi">
					<div class="productName line1row taCenter"><b><a href="./grouppurchase.php?gp_id=<?php echo $row['gp_id']; ?>"><?php echo stripslashes($row[it_name]); ?></a></b></div>
					<div class="productImg"><a href="./grouppurchase.php?gp_id=<?php echo $row['gp_id']; ?>">
							<?php echo $image; ?>
					</div>
					<div class="productInfo">
						<div class="brand"><label>브랜드</label><p><? echo $ct_type; ?></p></div>
						<div class="qty"><label>수량</label><p><?=$row[ct_qty]-$ct_buy_qty?></p></div>
						<div class="price"><label>단가</label><p><?=number_format($row[ct_price])?></p></div>
						<div class="sumPrice"><label>합계</label><p><?=number_format($row[ct_price] * $row[ct_qty])?></p></div>
						<div class="nick"><label>닉네임</label><p><?=$member_row[mb_nick]?></p></div>
					</div>
				</li>
			<?}?>
			</ul>


			<? if ($i == 0) echo '<tr><td colspan="7" class="empty_table">보관함이 비었습니다.</td></tr>';?>
		<?} else {?>
			<table>
				<thead>
				<tr>
					<th scope="col" class="real_th">브랜드</th>
					<th scope="col" class="real_th">이미지</th>
					<th scope="col" class="real_th">상품명</th>
					<th scope="col" class="real_th">수량</th>
					<th scope="col" class="real_th">단가</th>
					<th scope="col" class="real_th">합계</th>
					<th scope="col" class="real_th">닉네임</th>
				</tr>
				</thead>
				<tbody>

				<?php
				for ($i=0; $row=mysql_fetch_array($result_real); $i++) {
					$href = G5_SHOP_URL.'/grouppurchase.php?gp_id='.$row['it_id'];
					$bg = 'bg'.($i%2);

					if($row[ct_type] == "2010"){$ct_type = "APMEX";}
					else if($row[ct_type] == "2020"){$ct_type = "GAINSVILLE";}
					else if($row[ct_type] == "2030"){$ct_type = "MCM";}
					else if($row[ct_type] == "2040"){$ct_type = "SCOTTS DALE";}
					else{$ct_type = "OTHER DEALER";}

					$image = get_gp_image($row['it_id'], 70, 70);
					$gp_row = sql_fetch("select * from {$g5['g5_shop_group_purchase_table']} where gp_id='".$row[it_id]."' ");
					$member_row = sql_fetch("select * from {$g5['member_table']} where mb_id='".$row[mb_id]."' ");

					if($row[ct_buy_qty]){
						$ct_buy_qty = $row[ct_buy_qty];
					}else{
						$ct_buy_qty = 0;
					}

					$gp_price = getGroupPurchaseBasicPrice($row[gp_id]);

					?>

					<tr>
						<td class="sod_ws_brand">
							<?php echo $ct_type; ?>
						</td>
						<td class="sod_ws_img"><?php echo $image; ?></td>
						<td>
							<a href="./grouppurchase.php?gp_id=<?php echo $row['gp_id']; ?>"><?php echo stripslashes($row[it_name]); ?></a>
						</td>
						<td class="td_datetime"> <?= $row[ct_qty]-$ct_buy_qty?>
						</td>
						<td class="td_price"><?=number_format($row[ct_price])?></td>
						<td class="td_sum_price"><?=number_format($row[ct_price] * $row[ct_qty])?></td>
						<td class="td_nick">
							<?=$member_row[mb_nick]?>
						</td>
					</tr>
					<?php
				}

				if ($i == 0)
					echo '<tr><td colspan="7" class="empty_table">보관함이 비었습니다.</td></tr>';
				?>
				</tr>
				</tbody>
			</table>
		<?}?>

		</div>

		<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['PHP_SELF']}?$qstr&amp;page="); ?>

		</form>
	</div>
<?if($sfl == "mb_nick"){?>
	<? if (G5_IS_MOBILE) {?>
	<div id="real_sum" style="margin:0 14px;">
		<table border="4" bordercolor="#545454" cellspacing="0" cellpadding="0" height="50px" style="margin:14px auto;">
			<tr style="background-color:#fff;">
				<td width="60%">
					<table border="0" cellspacing="0" cellpadding="0" width="100%">
						<tr>
							<td style="color:#535353;padding:5px;font-size:0.667em;">
								<b><? echo $stx; ?></b>님의 공동구매신청하신물품의 결제예상금액 입니다.
							</td>
							<td></td>
						</tr>
					</table>

				</td>
				<td width="40%">

					<table border="0" cellspacing="0" cellpadding="0" width="100%">
						<tr height="30px">
							<td style="text-align:center;background-color: #ebebeb;">결제예상금액</td>
						</tr>
						<tr height="58px">
							<td style="text-align:right;color:#ff4e00;padding:0 20px 0 0;">
						<span style="font-size:1em;font-weight:bold;">
							<?
							if($sfl == "mb_nick"){
								echo number_format($sum_price[sum_price]);
							}
							?>
						</span>
								<span>원</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
	<?}else{?>
	<div id="real_sum">
		<table border="0" cellspacing="0" cellpadding="0" width="100%" height="100px">
			<tr>
				<td width="65%">

					<table border="0" cellspacing="0" cellpadding="0" width="100%">
						<tr>
							<td style="color:#030303;width:150px;text-align:right;font-weight:bold;">
								<?
								if($sfl == "mb_nick"){
									echo $stx;
								}
								?>
							</td>
							<td style="color:#535353;padding:;">
								&nbsp;님의 공동구매 신청하신 물품의 결제예상금액 입니다.
							</td>
						</tr>
					</table>

				</td>
				<td width="35%">

					<table border="0" cellspacing="0" cellpadding="0" width="100%">
						<tr height="30px">
							<td style="text-align:center;">결제예상금액</td>
						</tr>
						<tr height="58px">
							<td style="text-align:right;color:#ff4e00;padding:0 20px 0 0;">
						<span style="font-size:17px;font-weight:bold;">
							<?
							if($sfl == "mb_nick"){
								echo number_format($sum_price[sum_price]);
							}
							?>
						</span>
								<span>원</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
	<?}?>
<?}?>
	<div id="real_bn">
		<? if (G5_IS_MOBILE) {?>
			<?if($sfl == "mb_nick"){?>
			<div style="font-size:0.792em;text-align:center;color:#747474;">
				<img src="<?=G5_URL?>/img/real_ico.gif" border="0" align="absmiddle">
				결제 예상금액은 실제 결제금액과 상이 할 수 있습니다.
			</div>
			<?}?>
			<div style="margin:10px auto;display:table;">
				<a href="<?=G5_URL?>/shop/cart_gp.php"><img src="<?=G5_URL?>/img/real_cart_bn.gif" border="0" align="absmiddle" style="cursor:pointer;"></a>&nbsp;
				<a href="<?=G5_URL?>/shop/gplist.php?ca_id=<?=$_GET[ct_type]?>"><img src="<?=G5_URL?>/img/real_cancel_bn.gif" border="0" align="absmiddle" style="cursor:pointer;""></a>
			</div>
		<?}else{?>
			<?if($sfl == "mb_nick"){?>
			<div style="font-size:12px;font-weight:bold;font-style:italic;">
				<img src="<?=G5_URL?>/img/real_ico.gif" border="0" align="absmiddle">
				결제 예상금액은 실제 결제금액과 상이 할 수 있습니다.
			</div>
			<?}?>
			<div style="margin:10px 0 0 0;">
				<img src="<?=G5_URL?>/img/real_cart_bn.gif" border="0" align="absmiddle" style="cursor:pointer;" onclick="goto_url('<?=G5_URL?>/shop/cart_gp.php');">&nbsp;
				<img src="<?=G5_URL?>/img/real_cancel_bn.gif" border="0" align="absmiddle" style="cursor:pointer;" onclick="goto_url('<?=G5_URL?>/shop/gplist.php?ca_id=<?=$_GET[ct_type]?>');">
			</div>
		<?}?>
	</div>
</div>

<script>
	$(function() {
		moment.lang('ko');
		countdown.setLabels('밀리초|초|분|시|일|주|월|년|0년|세기|천년', '밀리초|초|분|시|일|주|월|년|0년|세기|천년', ' ', ' ', '종료');

		var ctType = '<?=$_GET[ct_type]?>';

		var jointPurchaseDate = $.parseJSON('<?=getJointPurchaseDate($_GET[ct_type])?>');
		var endDate = jointPurchaseDate[0].toDate;
		var interval
		if(ctType) {
			$('.statusContainer').show();
			$('.jointPurchaseContainer').show();
			interval = startJointPurchaseDate( endDate );
		}

		$('.endDate .date').text(endDate);
	});

	function startJointPurchaseDate(endDate) {
			interval = setInterval(function() {
			$('.jointPurchaseDate').text( moment(endDate).countdown().toString() );
		}, 1000);

		return interval;
	}

	function endJointPurchaseDate(interval) {
		clearInterval(interval);
	}
</script>
<!-- } 실시간공동구매현황 끝 -->

<?php
if (G5_IS_MOBILE) {
	include_once(G5_MSHOP_PATH.'/tail.php');
} else {
	include_once('./_tail.php');
}
?>