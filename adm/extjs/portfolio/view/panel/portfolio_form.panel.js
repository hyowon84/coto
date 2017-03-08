//
//var panel_window_wire = Ext.create('Ext.Panel', {
//	extend: 'Ext.panel.Panel',
//	xtype: 'form-register',
//	frame: true,
//	title: 'Register',
//	bodyPadding: 10,
//	scrollable:true,
//	width: 300,
//	height:600,
//	fieldDefaults: {
//		labelAlign: 'right',
//		labelWidth: 115,
//		labelHeight:35,
//		msgTarget: 'side'
//	},
//	items:[
//	],
//	buttons: [{
//        text: 'Cancel',
//        handler: 'onFormCancel'
//    }, {
//        text: '',
//        handler: 'onFormSubmit'
//    }]
//});
//
//
////발주관리 > 발주탭 컨텐츠
//var panel_invoice = Ext.create('Ext.Panel', {
//	extend: 'Ext.panel.Panel',
//	xtype: 'layout-border',
//	requires: [
//		'Ext.layout.container.Border'
//	],
//	layout: 'auto',
//	autoWidth : true,
//	autoHeight: true,
//	bodyBorder: false,
//	defaults: {
//		collapsible: true,
//		split: true,
//		bodyPadding: 0
//	},
//	items: [
//        {
//        	title: '<b>주문 목록</b>',
//        	style : 'color:red;',
//        	items : []
//        }	
//	]
//});//panel_body


/* 발주관리 내용물(아코디언 패널) */
/*
	var panel_grid = Ext.create('Ext.Panel', {
		extend: 'Ext.panel.Panel',
		requires: [
			'Ext.layout.container.Accordion',
			'Ext.grid.*',
			'KitchenSink.model.Company'
		],
		xtype: 'layout-accordion',
		layout: 'accordion',
		width: 900,
		height: 400,
		defaults: {
			bodyPadding: 0
		},

		items: [grid_manage,panel_invoice],

// 		initComponent: function() {
// 			Ext.apply(this, {
// 				items: [grid_manage,grid_wire]
// 			});
// 			this.callParent();
// 		},
		changeRenderer: function(val) {
			if (val > 0) {
				return '<span style="color:green;">' + val + '</span>';
			} else if(val < 0) {
				return '<span style="color:red;">' + val + '</span>';
			}
			return val;
		},
		pctChangeRenderer: function(val){
			if (val > 0) {
				return '<span style="color:green;">' + val + '%</span>';
			} else if(val < 0) {
				return '<span style="color:red;">' + val + '%</span>';
			}
			return val;
		}
	});
	*/

/*
var panel_grid = Ext.create('Ext.tab.Panel', {
	xtype: 'framed-tabs',
	//controller: 'tab-view',
	frame: true,
	width: '100%',
	height: 800,
	defaults: {
		bodyPadding: 5,
		scrollable: true
	},
	items: [{
		title: '통계'
//		items:	grid_manage
	}, {
		title: '발주',
		items:	panel_invoice
	}, {
		title: 'Disabled Tab',
		disabled: true
	}],

	listeners: {
		tabchange: function(tabs, newTab, oldTab) {
			Ext.suspendLayouts();
// 				newTab.setTitle('Active Tab');
// 				oldTab.setTitle('Inactive Tab');
			Ext.resumeLayouts(true);
		}
	}
});
*/