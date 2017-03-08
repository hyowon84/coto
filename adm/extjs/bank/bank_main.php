<?php
$sub_menu = '400200';
$sub_sub_menu = '2';

include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$g5['title'] = '입출금내역 관리';
include_once (G5_ADMIN_PATH.'/extjs.head.php');
?>
<link href="/js/extjs/packages/ext-theme-crisp/build/resources/ext-theme-crisp-all.css" rel="stylesheet">
<link href="/css/extjs.css" rel="stylesheet">
<script type="text/javascript" src="/js/extjs/build/ext-all.js"></script>
<script type="text/javascript" src="/js/extjs/ext-common.js"></script>
<script type="text/javascript" src="/js/extjs/plugin/ProgressBarPager.js"></script>
<script type="text/javascript" src="/js/extjs/plugin/SlidingPager.js"></script>
<script type="text/javascript" src="/js/extjs/plugin/SlidingPager.js"></script>

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
	'Ext.ux.RowExpander'
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



<!-- 공통 모델 데이터 -->
<script type="text/javascript" src="../common/model/data.js"></script>
<script type="text/javascript" src="../common/model/store.js"></script>
<script type="text/javascript" src="../common/model/combo.js"></script>



<!-- 모델 데이터 -->
<script type="text/javascript" src="./model/bank.model.js"></script>

<!-- 스토어 데이터 -->
<script type="text/javascript" src="./store/bank_main.store.js"></script>

<!-- 공통 콤보박스 정의 -->
<script type="text/javascript" src="model/bank.common.js"></script>


<!-- 그리드 -->
<script type="text/javascript" src="./view/grid/bank_main.grid.js"></script>

<!-- 패널 -->
<script type="text/javascript" src="./view/panel/bank_main.panel.js"></script>

<!-- 윈도우팝업 -->
<!--script type="text/javascript" src="./view/window/bank_main.window.js"></script-->

<!-- 패널 -->
<script type="text/javascript" src="./view/bank_main.js"></script>


<div class='extjsHead'><?=$g5['title']?></div>
<div id='extjsBody'></div>
<? include_once (G5_ADMIN_PATH.'/admin.tail.php'); ?>