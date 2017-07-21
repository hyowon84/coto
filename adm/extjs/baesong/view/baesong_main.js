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
		requires: ['Ext.layout.container.Border'],
		layout: 'border',
		width: '100%',
		height: 1040,
		bodyBorder: false,
		defaults: {
			bodyPadding: 0
		},
		items: [
			{ 
				//left 
				id : 'navi_west',
				title: '<b>공동구매 목록</b>',
				region: 'west', 
				width: '47%',
				height: 1040,
				collapsible: true,
				style : 'float:left; margin:0px; padding:0px;',
				items	: [panel_gpmb]
			},
			{
				//top 
				id : 'navi_center',
				region: 'center',
				collapsible: false,
				width: '53%',
				height: 1040,
				style : 'float:left; margin:0px; padding:0px;',
				items : [panel_order]		//panel_grid
			}
		]
		,renderTo: 'extjsBody'
	});//panel_body


	panelResize(main_panel);
	
});