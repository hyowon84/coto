
//좌측 공구,회원목록
var panel_gpmb = Ext.create('Ext.Panel', {
	extend: 'Ext.panel.Panel',
	xtype: 'layout-border',
	requires: [
		'Ext.layout.container.Border'
	],
	layout: 'auto',
	autoWidth : true,
	autoHeight: true,
	bodyBorder: false,
	defaults: {
		collapsible: false,
		split: true,
		bodyPadding: 0,
		style : 'float:left;'
	},
	items: [
		{
			region: 'west',
			width : '30%',
			items : [grid_gpinfo]
		},
		{
			region: 'center',
			width : '70%',
			items : [grid_mblist]
		}
	]
});


//발주관리 > 발주탭 컨텐츠
var panel_order = Ext.create('Ext.Panel', {
	extend: 'Ext.panel.Panel',
	xtype: 'layout-border',
	requires: [
		'Ext.layout.container.Border'
	],
	layout: 'auto',
	autoWidth : true,
	autoHeight: true,
	bodyBorder: false,
	defaults: {
		collapsible: false,
		split: true,
		bodyPadding: 0
	},
	items: [
			{
				//bottom 
				region: 'north',
				autoWidth : true,
				height: 680,
				minHeight: 680,
				items : [grid_orderlist]
			},
			{
				//bottom 
				region: 'center',
				autoWidth : true,
				height: 390,
				minHeight: 200,
				items : [grid_shiped_list]
			}
	]
});//panel_body
