

var navi_panel = Ext.create('Ext.Panel', {
	id: 'navi_panel',
	extend: 'Ext.panel.Panel',
	xtype: 'layout-border',
	requires: ['Ext.layout.container.Border'],
	layout: 'border',
	width: '100%',
	height : 300,
	bodyBorder: false,
	defaults: {
		collapsible: false,
		//scrollable: true
		bodyPadding: 0
	},
	items: [
					{
						id : 'NAVI_WEST',
						title : '발주서 정보',
						region: 'west',
						collapsible: true,
						width : '65%',
						autoHeight : true,
						style : 'float:left; margin:0px; padding:0px;',
						items	: [
							{
								autoWidth : true,
								autoHeight : true,
								items : [grid_navi_invoice]
							}
						]
					},
					{
						id : 'NAVI_CENTER',
						title : '발주서 정보',
						region: 'center',
						width : '35%',
						autoHeight : true,
						style : 'float:left; margin:0px; padding:0px;',
						items	: [
							{
								autoWidth : true,
								autoHeight : true,
								items : [grid_navi_invoice_dtl]
							}
						]
					}
		]
});



/* 발주탭 */
var panel_invoice = Ext.create('Ext.Panel', {
	id : 'panel_invoice',
	extend: 'Ext.panel.Panel',
	xtype: 'layout-border',
	requires: ['Ext.layout.container.Border'],
	layout: 'border',
	width: '100%',
	height : 850,
	bodyBorder: false,
	defaults: {
		collapsible: true,
		//scrollable: true
		bodyPadding: 0
	},
	items: [
		{
			id : 'IV_WEST',
			title: '<b>공동구매 목록</b>',
			region: 'west',
			width : '40%',
			autoHeight : true,
			style : 'float:left; margin:0px; padding:0px;',
			items	: [
				{
					autoWidth : true,
					autoHeight : true,
					items : [grid_gpinfo]
				}
			]
		},
		{
			id : 'IV_CENTER',
			region: 'center',
			width : '60%',
			autoHeight : true,
			style : 'float:left; margin:0px; padding:0px;',
			items : [
				{
					autoWidth : true,
					autoHeight : true,
					items : [grid_memo_gpinfo]
				},
				{
					autoWidth : true,
					autoHeight : true,
					items : [grid_orderitems]
				}
			]
		}
	]
});


//송금탭 패널
var panel_receipt = Ext.create('Ext.Panel', {
	id : 'panel_receipt',
	extend: 'Ext.panel.Panel',
	xtype: 'layout-border',
	requires: ['Ext.layout.container.Border'],
	layout: 'border',
	width: '100%',
	height : 850,
	bodyBorder: false,
	defaults: {
		collapsible: false,
		//split : true,
		bodyPadding: 0,
		scrollable: true
	},
	items: [
		{
			id : 'WIRE_WEST',
			title: '<b>발주서 목록</b>',
			region: 'west',
			width : '40%',
			autoHeight : true,
			style : 'float:left; margin:0px; padding:0px;',
			items	: [
				{
					autoWidth : true,
					autoHeight : true,
					items : [grid_invoiceTodoWire]
				},
				/*{
					autoWidth : true,
					autoHeight : true,
					items : [grid_wireInfo]
				},*/
				{
					autoWidth : true,
					autoHeight : true,
					items : [grid_invoiceEndWire]
				}
			]
		},
		{
			id : 'WIRE_CENTER',
			region: 'east',
			width : '60%',
			autoHeight : true,
			style : 'float:left; margin:0px; padding:0px;',
			items : [
				{
					autoWidth : true,
					autoHeight : true,
					items : [grid_wire_gpinfo]
				},
				{
					autoWidth : true,
					autoHeight : true,
					items : [grid_wire_dtl]
				}
			]
		}
	]
});



//통관탭 패널
var panel_clearance = Ext.create('Ext.Panel', {
	id : 'panel_clearance',
	extend: 'Ext.panel.Panel',
	xtype: 'layout-border',
	requires: ['Ext.layout.container.Border'],
	layout: 'border',
	width: '100%',
	height : 850,
	bodyBorder: false,
	defaults: {
		collapsible: false,
		//split : true,
		bodyPadding: 0,
		scrollable: true
	},
	items: [
		{
			id : 'CLR_WEST',
			title: '<b>통관 목록</b>',
			region: 'west',
			width : '40%',
			autoHeight : true,
			style : 'float:left; margin:0px; padding:0px;',
			items	: [
				{
					autoWidth : true,
					autoHeight : true,
					items : [grid_todoClearance]
				},
				{
					autoWidth : true,
					autoHeight : true,
					items : [grid_endClearance]
				}
			]
		},
		{
			id : 'CLR_CENTER',
			region: 'east',
			width : '60%',
			autoHeight : true,
			style : 'float:left; margin:0px; padding:0px;',
			items : [
				{
					autoWidth : true,
					autoHeight : true,
					items : [grid_clearance_gpinfo]
				},
				{
					autoWidth : true,
					autoHeight : true,
					items : [grid_clearance_dtl]
				}
			]
		}
	]
});



//입고탭 패널
var panel_warehousing = Ext.create('Ext.Panel', {
	id : 'panel_warehousing',
	extend: 'Ext.panel.Panel',
	xtype: 'layout-border',
	requires: ['Ext.layout.container.Border'],
	layout: 'border',
	width: '100%',
	height : 850,
	bodyBorder: false,
	defaults: {
		collapsible: false,
		//split : true,
		bodyPadding: 0,
		scrollable: true
	},
	items: [
		{
			id : 'WR_WEST',
			title: '<b>입고예정 목록</b>',
			region: 'west',
			width : '40%',
			autoHeight : true,
			style : 'float:left; margin:0px; padding:0px;',
			items	: [
				{
					autoWidth : true,
					autoHeight : true,
					items : [grid_todoWarehousing]
				}
			]
		},
		{
			id : 'WR_CENTER',
			region: 'east',
			width : '60%',
			autoHeight : true,
			style : 'float:left; margin:0px; padding:0px;',
			items : [
				{
					autoWidth : true,
					autoHeight : true,
					items : [grid_warehousing_gpinfo]
				},
				{
					autoWidth : true,
					autoHeight : true,
					items : [grid_warehousing_dtl]
				}
			]
		}
	]
});


/* 탭패널 */
var tab_panel = Ext.create('Ext.tab.Panel', {
	id : 'tab_panel',
	xtype: 'framed-tabs',
	controllers: 'tab-view',
	width: '100%',
	height : 850,
	items : [{
		title : '발주',
		items : [panel_invoice]
	},{
		title : '송금',
		items : [panel_receipt]
	},{
		title : '통관',
		items : [panel_clearance]
	},{
		title : '입고',
		items :	[panel_warehousing]
	}
	],
	listeners: {
		tabchange: function(tabs, newTab, oldTab) {
			Ext.suspendLayouts();
			Ext.resumeLayouts(true);
		}
	}
});
