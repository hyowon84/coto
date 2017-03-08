
//발주관리 > 발주탭 컨텐츠
var panel_invoice = Ext.create('Ext.Panel', {
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
		collapsible: true,
		split: true,
		bodyPadding: 0
	},
	items: [
        {
        	title: '<b>주문 목록</b>',
        	style : 'color:red;',
        	items : []
        }
	]
});//panel_body