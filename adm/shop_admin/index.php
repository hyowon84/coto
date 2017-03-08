<?php
$sub_menu = '400000';
include_once('./_common.php');

$max_limit = 7; // 몇행 출력할 것인지?

$g5['title'] = ' 쇼핑몰관리';
include_once (G5_ADMIN_PATH.'/extjs.head.php');
// include_once (G5_ADMIN_PATH.'/admin.head.php');
?>
<link href="/js/extjs/packages/ext-theme-crisp/build/resources/ext-theme-crisp-all.css" rel="stylesheet">
<link href="/js/extjs/packages/ext-charts/build/resources/ext-charts-all.css" rel="stylesheet">
<link href="/css/extjs.css" rel="stylesheet">
<script type="text/javascript" src="/js/extjs/build/ext-all.js"></script>
<script type="text/javascript" src="/js/extjs/packages/ext-charts/build/ext-charts.js"></script>
<script type="text/javascript" src="/js/extjs/ext-common.js"></script>
<script type="text/javascript" src="/js/extjs/plugin/ProgressBarPager.js"></script>
<script type="text/javascript" src="/js/extjs/plugin/SlidingPager.js"></script>
<script type="text/javascript" src="./saleschart.js"></script>

<script>
Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('Ext.ux', '/js/extjs/ux/');
Ext.require([
	'Ext.data.*',
	'Ext.grid.*',
	'Ext.util.*',
	'Ext.toolbar.*',
	'Ext.form.field.Number',
	'Ext.form.field.Date',
	'Ext.tip.QuickTipManager',
	'Ext.ux.ToolbarDroppable',
	'Ext.ux.BoxReorderer',
	'Ext.ux.grid.Printer',
	'Ext.ux.RowExpander',
	'Ext.grid.selection.SpreadsheetModel',
	'Ext.grid.plugin.Clipboard'
]);

Ext.define('globalData', {
	sigleton: true,
	temp:	null
});

<?
$flow_sql = "	SELECT	*
							FROM		flow_price
							ORDER BY reg_date DESC
							LIMIT 1
";
$flow = mysql_fetch_array(sql_query($flow_sql));
?>

var v_GL = '<?=$flow[GL]?>';
var v_SL = '<?=$flow[SL]?>';
var v_USD = '<?=$flow[USD]?>';

</script>






<?
$pg_anchor = '<ul class="anchor sidx_anchor">
<li><a href="#anc_sidx_ord">주문현황</a></li>
<li><a href="#anc_sidx_rdy">입금완료미배송내역</a></li>
<li><a href="#anc_sidx_wait">미입금주문내역</a></li>
<li><a href="#anc_sidx_ps">사용후기</a></li>
<li><a href="#anc_sidx_qna">상품문의</a></li>
</ul>';

// 주문상태에 따른 합계 금액
function get_order_status_sum($status)
{
	global $g5;

	$sql = "	SELECT	count(*) as cnt,
										SUM(CO.it_org_price * CO.it_qty) + IFNULL(CI.delivery_price,0) as price
						FROM		clay_order CO
										LEFT JOIN clay_order_info CI ON (CI.od_id = CO.od_id)
		 			 	WHERE		stats IN ($status)
	";
	$row = sql_fetch($sql);

	$info = array();
	$info['count'] = (int)$row['cnt'];
	$info['price'] = (int)$row['price'];
	$info['href'] = './orderlist.php?od_status='.$status;

	return $info;
}


// 월별 주문 합계 금액
function get_order_month_sum($date)
{
	global $g5;

	$sql = "	SELECT	SUBSTRING(CL.od_date, 1, 7) AS od_date,
										SUM(CL.it_org_price * CL.it_qty) as orderprice,
										IFNULL(CC.price,0) AS cancelprice
						FROM		clay_order CL
										LEFT JOIN (	SELECT	SUBSTRING(od_date, 1, 7) AS od_date,
																				SUM(it_org_price * it_qty) AS price
																FROM		clay_order
																WHERE		stats IN (99)
																GROUP BY SUBSTRING(od_date, 1, 7)
										) CC ON ( CC.od_date = SUBSTRING(CL.od_date, 1, 7) )
						WHERE		1=1
						AND			SUBSTRING(CL.od_date, 1, 7) = '$date'
						GROUP BY SUBSTRING(od_date, 1, 7)
	";
	$row = sql_fetch($sql);


	$info = array();
	$info['order'] = (int)$row['orderprice'];
	$info['cancel'] = (int)$row['cancelprice'];

	return $info;
}



// 일자별 주문 합계 금액
function get_order_date_sum($date)
{
	global $g5;

	$sql = "	SELECT	SUBSTRING(CL.od_date, 1, 10) AS od_date,
										SUM(CL.it_org_price * CL.it_qty) as orderprice,
										IFNULL(CC.price,0) AS cancelprice
						FROM		clay_order CL
										LEFT JOIN (	SELECT	SUBSTRING(od_date, 1, 10) AS od_date,
																				SUM(it_org_price * it_qty) AS price
																FROM		clay_order
																WHERE		stats IN (99)
																GROUP BY SUBSTRING(od_date, 1, 10)
										) CC ON ( CC.od_date = SUBSTRING(CL.od_date, 1, 10) )
						WHERE		1=1
						AND			SUBSTRING(CL.od_date, 1, 10) = '$date'
						GROUP BY SUBSTRING(od_date, 1, 10)
	";
	$row = sql_fetch($sql);

	$info = array();
	$info['order'] = (int)$row['orderprice'];
	$info['cancel'] = (int)$row['cancelprice'];

	return $info;
}

// 일자별 결제수단 주문 합계 금액
function get_order_settle_sum($date)
{
	global $g5, $default;

	$case = array('신용카드', '계좌이체', '가상계좌', '무통장', '휴대폰');
	$info = array();

	// 결제수단별 합계
	foreach($case as $val)
	{
		$sql = " select sum(od_cart_price + od_send_cost + od_send_cost2 - od_receipt_point - od_cart_coupon - od_coupon - od_send_coupon) as price,
						count(*) as cnt
					from {$g5['g5_shop_order_table']}
					where SUBSTRING(od_time, 1, 10) = '$date'
					  and od_settle_case = '$val' ";
		$row = sql_fetch($sql);

		$info[$val]['price'] = (int)$row['price'];
		$info[$val]['count'] = (int)$row['cnt'];
	}

	// 포인트 합계
	$sql = " select sum(od_receipt_point) as price,
					count(*) as cnt
				from {$g5['g5_shop_order_table']}
				where SUBSTRING(od_time, 1, 10) = '$date'
				  and od_receipt_point > 0 ";
	$row = sql_fetch($sql);
	$info['포인트']['price'] = (int)$row['price'];
	$info['포인트']['count'] = (int)$row['cnt'];

	// 쿠폰 합계
	$sql = " select sum(od_cart_coupon + od_coupon + od_send_coupon) as price,
					count(*) as cnt
				from {$g5['g5_shop_order_table']}
				where SUBSTRING(od_time, 1, 10) = '$date'
				  and ( od_cart_coupon > 0 or od_coupon > 0 or od_send_coupon > 0 ) ";
	$row = sql_fetch($sql);
	$info['쿠폰']['price'] = (int)$row['price'];
	$info['쿠폰']['count'] = (int)$row['cnt'];

	return $info;
}

function get_max_value($arr)
{
	foreach($arr as $key => $val)
	{
		if(is_array($val))
		{
			$arr[$key] = get_max_value($val);
		}
	}

	sort($arr);

	return array_pop($arr);
}
?>


<div id="extjsBody"></div>

<div class="sidx sidx_cs">
	<section id="anc_sidx_oneq">
		<h2>1:1문의</h2>
		<? echo $pg_anchor; ?>

		<div class="ul_01 ul_wrap">
			<ul>
				<?php
				$sql = " select * from {$g5['qa_content_table']}
						  where qa_status = '0'
							and qa_type = '0'
						  order by qa_num
						  limit $max_limit ";
				$result = sql_query($sql);
				for ($i=0; $row=sql_fetch_array($result); $i++)
				{
					$sql1 = " select * from {$g5['member_table']} where mb_id = '{$row['mb_id']}' ";
					$row1 = sql_fetch($sql1);

					$name = get_sideview($row['mb_id'], get_text($row['qa_name']), $row1['mb_email'], $row1['mb_homepage']);
				?>
				<li>
					<span class="oneq_cate oneq_span"><? echo get_text($row['qa_category']); ?></span>
					<a href="<? echo G5_BBS_URL; ?>/qaview.php?qa_id=<? echo $row['qa_id']; ?>" target="_blank" class="oneq_link"><? echo cut_str($row['qa_subject'],40); ?></a>
					<? echo $name; ?>
				</li>
				<?php
				}

				if ($i == 0)
					echo '<li class="empty_table">자료가 없습니다.</li>';
				?>
			</ul>
		</div>

		<div class="btn_list03 btn_list">
			<a href="<? echo G5_BBS_URL; ?>/qalist.php" target="_blank">1:1문의 더보기</a>
		</div>
	</section>

	<section id="anc_sidx_qna">
		<h2>상품문의</h2>
		<? echo $pg_anchor; ?>

		<div class="ul_01 ul_wrap">
			<ul>
				<?php
				$sql = " select * from g5_shop_gpitem_qa
						  where iq_answer = ''
						  order by iq_id desc
						  limit $max_limit ";
				$result = sql_query($sql);
				for ($i=0; $row=sql_fetch_array($result); $i++)
				{
					$sql1 = " select * from {$g5['member_table']} where mb_id = '{$row['mb_id']}' ";
					$row1 = sql_fetch($sql1);

					$name = get_sideview($row['mb_id'], get_text($row['iq_name']), $row1['mb_email'], $row1['mb_homepage']);
				?>
				<li>
					<a href="./itemqaform.php?w=u&amp;iq_id=<? echo $row['iq_id']; ?>" class="qna_link"><? echo cut_str($row['iq_subject'],40); ?></a>
					<? echo $name; ?>
				</li>
				<?php
				}

				if ($i == 0)
					echo '<li class="empty_list">자료가 없습니다.</li>';
				?>
			</ul>
		</div>

		<div class="btn_list03 btn_list">
			<a href="./itemqalist.php?sort1=iq_answer&amp;sort2=asc">상품문의 더보기</a>
		</div>
	</section>

	<section id="anc_sidx_ps">
		<h2>사용후기</h2>
		<? echo $pg_anchor; ?>

		<div class="ul_01 ul_wrap">
			<ul>
			<?php
			$sql = " select * from {$g5['g5_shop_item_use_table']}
					  where is_confirm = 0
					  order by is_id desc
					  limit $max_limit ";
			$result = sql_query($sql);
			for ($i=0; $row=sql_fetch_array($result); $i++)
			{
				$sql1 = " select * from {$g5['member_table']} where mb_id = '{$row['mb_id']}' ";
				$row1 = sql_fetch($sql1);

				$name = get_sideview($row['mb_id'], get_text($row['is_name']), $row1['mb_email'], $row1['mb_homepage']);
			?>
				<li>
					<a href="./itemuseform.php?w=u&amp;is_id=<? echo $row['is_id']; ?>" class="ps_link"><? echo cut_str($row['is_subject'],40); ?></a>
					<? echo $name; ?>
				</li>
			<?php
			}
			if ($i == 0) echo '<li class="empty_list">자료가 없습니다.</li>';
			?>
			</ul>
		</div>

		<div class="btn_list03 btn_list">
			<a href="./itemuselist.php?sort1=is_confirm&amp;sort2=asc">사용후기 더보기</a>
		</div>
	</section>
</div>




<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
