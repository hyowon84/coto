<?php
$sub_menu = '400200';
$sub_sub_menu = '2';

include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$g5['title'] = '발주/송금/통관/입고 관리';
include_once (G5_ADMIN_PATH.'/extjs.head.php');
?>

<link href="/js/extjs/packages/ext-theme-crisp/build/resources/ext-theme-crisp-all.css" rel="stylesheet">
<link href="/css/extjs.css" rel="stylesheet">
<script type="text/javascript" src="/js/extjs/build/ext-all.js"></script>
<script type="text/javascript" src="/js/extjs/ext-common.js"></script>
<script type="text/javascript" src="/js/extjs/plugin/ProgressBarPager.js"></script>
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
var v_SmsMsg = new Array();

<?
	foreach($v_sms as $stats => $msg) {
		echo "v_SmsMsg[".$stats."] = '".$msg."';\r\n";
	}
?>
</script>




<!-- 모델 데이터 -->
<script type="text/javascript" src="/adm/extjs/flowprice/model/flowprice.model.js"></script>

<!-- 스토어 데이터 -->
<script type="text/javascript" src="/adm/extjs/flowprice/store/flowprice.store.js"></script>

<!-- 공통 콤보박스 정의 -->
<script type="text/javascript" src="/adm/extjs/flowprice/view/combo/flowprice.common.js"></script>




<!-- 공통 모델 데이터 -->
<script type="text/javascript" src="/adm/extjs/common/model/data.js"></script>
<script type="text/javascript" src="/adm/extjs/common/model/store.js"></script>
<script type="text/javascript" src="/adm/extjs/common/model/combo.js"></script>
<script type="text/javascript" src="/adm/extjs/common/model/grid.js"></script>


<!-- 모델 데이터 -->
<script type="text/javascript" src="model/stock_main.model.js"></script>


<!-- 스토어 데이터 -->
<script type="text/javascript" src="store/stock_main.store.js"></script>

<!-- 그리드 -->
<script type="text/javascript" src="view/grid/stock_main.grid.js"></script>

<!-- 패널 -->
<script type="text/javascript" src="view/panel/stock_main.panel.js"></script>

<!-- 윈도우팝업 -->
<script type="text/javascript" src="view/window/stock_main.window.js"></script>

<!-- 패널 -->
<script type="text/javascript" src="view/stock_main.js"></script>


<div class='extjsHead'><?=$g5['title']?></div>
<div id='extjsBody'></div>
<? include_once (G5_ADMIN_PATH.'/extjs.tail.php'); ?>