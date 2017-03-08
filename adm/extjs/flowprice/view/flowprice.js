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
		height: 850,
//		autoScroll : true,
		bodyBorder: false,
		defaults: {
			collapsible: true,
			split: true,
			bodyPadding: 0
		},
		items: [
			{ 
				//left 
				id : 'navi_north',
				title: '<b>귀금속시세항목 & 환율항목 설정</b>',
				collapsible: false,
				region: 'north',
				floatable: false,
				width : '100%',
				style : 'float:left; margin:0px; padding:0px;',
				items	: [grid_FpMetalSetting,grid_flowprice]
			},
			{
				//top 
				id : 'navi_center',
				region: 'center',
				collapsible: false,
				minWidth: 800,
				width : '100%',
				height: 990,
				style : 'float:left; margin:0px; padding:0px;',
				items : [grid_exchsetting,grid_exchrate]	//panel_grid
			}
			/*,
			{
				//bottom 
				title: '재고관련일정',
				region: 'south',
				height: 200,
				minHeight: 75,
				maxHeight: 150,
			}
			*/
		]
		,renderTo: 'extjsBody'
	});//panel_body

	Ext.EventManager.onWindowResize(function () {
		var width = Ext.getBody().getViewSize().width-20;
		var height = Ext.getBody().getViewSize().height;
		main_panel.setSize(width, 850);
	});

});