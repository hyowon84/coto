/**
 *	Ext.onReady Similar Code
 *
 * = JAVASCRIPT :	window.onload = function(){
 *						// codehere
 *					}
 * = jQuery : $(document).ready({
 *					// codehere
 *			 })
 */

function renderGpImg(value, p, record) {
	return Ext.String.format(
		"<img src='{3}' />{4}",
		value,
		record.getId()
	);
}


Ext.onReady(function(){
	/************* ----------------  패널 START -------------- ******************/
	
	/* 화면 */
	var main_panel = Ext.create('Ext.Panel', {
		id : 'main_panel',
		extend: 'Ext.panel.Panel',
		xtype: 'layout-border',
		requires: [
			'Ext.layout.container.Border'
		],
		layout: 'border',
		width: '100%',
		height : 1300,
		bodyBorder: false,
		defaults: {
			collapsible: true,
			split: true,
			bodyPadding: 0
		},
		items: [
			{
				id : 'navi_north',
				title: '<b>입/출금 관리</b>',
				region: 'north',
				floatable: false,
				autoScroll: false,
				width : '100%',
				autoHeight: true,
				style : 'float:left; margin:0px; padding:0px;',
				items : [bank_panel]
			},
			{
				id : 'navi_center',
				region: 'center',
				collapsible: false,
				floatable: false,
				width : '100%',
				autoHeight: true,
				autoScroll: true,
				style : 'float:left; margin:0px; padding:0px;',
				items : [order_panel]
			}			
		]
		,renderTo: 'extjsBody'
	});//panel_body
	grid_banklist.store.load();

	panelResize(main_panel);	
});