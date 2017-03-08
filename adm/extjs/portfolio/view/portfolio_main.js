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
	
	var tabpanel =  Ext.create('Ext.tab.Panel', {
		id : 'tab_panel',
		xtype: 'framed-tabs',
		controllers: 'tab-view',
		defaults: {
			scrollable: true
		},
		width: '100%',
		height: 840,
   	items : [{
   					title : 'DATA',
   					style : 'padding:5px',
   					items : [grid_pfDataSummary,grid_pfItemList]
   				},{
   					tabConfig: {
   						title : 'PORTFOLIO'/*,
   						tooltip : '포트폴리오입니다'*/
   					},   					
   					items : [panel_investform]
   				},{
   					title : 'CHART',
   					items : [panel_chart]
   				}
		],
		listeners: {
			tabchange: function(tabs, newTab, oldTab) {
				Ext.suspendLayouts();
//				newTab.setTitle('Active Tab');
//				oldTab.setTitle('Inactive Tab');
				Ext.resumeLayouts(true);
			}
		}
   });
	
	
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
		height: 850,
//		autoScroll : true,
		bodyBorder: false,
		defaults: {
			collapsible: true,
			split: true,
			bodyPadding: 0,
			scrollable: true
		},
		items: [
			{ 
				//left 
				id : 'navi_west',
				title: '<b>VIP MEMBER</b>',
				region: 'west', 
				floatable: false,
//				collapsible: true,
//				headerPosition:'left',
//				split: true,
//				width: '25%',
				width : 400,
				maxWidth:400,
				minWidth: 400,
				style : 'float:left; margin:0px; padding:0px;',
				items	: [grid_vipMbList]
			},
			{
				//top 
				id : 'navi_center',
				region: 'center',
				collapsible: false,
//				headerPosition:'left',
//				floatable: false,
//				width: '75%',
				minWidth: 800,
				style : 'float:left; margin:0px; padding:0px;',
				items : [tabpanel]		//panel_grid
			}
			
		]
		,renderTo: 'extjsBody'
	});//panel_body
	
	Ext.EventManager.onWindowResize(function () {
		var width = Ext.getBody().getViewSize().width;
		var height = Ext.getBody().getViewSize().height;
		main_panel.setSize(width, height);
		tabpanel.setSize(width-430, height);
	});
});