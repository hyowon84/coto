
/* 1. 상품가격관리 탭 */
var panel_prdprice = Ext.create('Ext.Panel', {
	id : 'panel_prdprice',
	extend: 'Ext.panel.Panel',
	xtype: 'layout-border',
	requires: ['Ext.layout.container.Border'],
	layout: 'border',
	width: '100%',
	height : 810,
	bodyBorder: false,
	defaults: {
		collapsible: true,
		//scrollable: true
		bodyPadding: 0
	},
	items: [
		{
			id : 'PRICE_WEST',
			title: '<b>공구목록</b>',
			region: 'west',
			width : '40%',
			autoHeight : true,
			style : 'float:left; margin:0px; padding:0px;',
			items	: [
				{
					autoWidth : true,
					autoHeight : true,
					items : [grid_gplist]
				}
			]
		},
		{
			id : 'PRICE_CENTER',
			title: '<b>품목들</b> [ '+'금: $'+v_GL+' / 은: $'+v_SL+' / $환율: '+v_USD+'원 ]',
			region: 'center',
			width : '60%',
			autoHeight : true,
			style : 'float:left; margin:0px; padding:0px;',
			items : [
				{
					autoWidth : true,
					autoHeight : true,
					items : [grid_itemlist]
				}
			]
		}
	]
});


//2. 경매관리 탭 패널
var panel_prdauction = Ext.create('Ext.Panel', {
	id : 'panel_prdauction',
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
			id : 'AUCTION_WEST',
			title: '<b>상품목록</b>',
			region: 'west',
			collapsible: true,
			width : '60%',
			autoHeight : true,
			style : 'float:left; margin:0px; padding:0px;',
			items	: [
				{
					autoWidth : true,
					autoHeight : true,
					items : [grid_aucPrdList]
				}
			]
		},
		{
			id : 'AUCTION_CENTER',
			title: '<b>입찰기록</b>',
			region: 'center',
			width : '40%',
			autoHeight : true,
			style : 'float:left; margin:0px; padding:0px;',
			items : [
				{
					autoWidth : true,
					autoHeight : true,
					items : [grid_aucBidList]
				}
			]
		}
	]
});

