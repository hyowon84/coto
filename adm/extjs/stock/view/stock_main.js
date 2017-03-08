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
		width: 1900,
		height : 1200,
		bodyBorder: false,
		defaults: {
			collapsible: true,
			split: true,
			bodyPadding: 0
		},
		items: [
			{
				id : 'navi_north',
				title : '검색',				
				region: 'north',
				floatable: false,
				autoScroll: false,
				width : '100%',
				autoHeight: true,
				style : 'float:left; margin:0px; padding:0px;',
				items : [navi_panel]
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
				items : [tab_panel]
			}
		]
		,renderTo: 'extjsBody'
	});//panel_body

	grid_navi_invoice.store.load();
	grid_gpinfo.store.load();
	
	/*
	Ext.EventManager.onWindowResize(function () {
		var width = Ext.getBody().getViewSize().width;
		var height = Ext.getBody().getViewSize().height;

		main_panel.setSize(width, height);
	*/

});