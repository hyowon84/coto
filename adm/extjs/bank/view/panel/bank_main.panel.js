


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
// 				items: [grid_manage,grid_invoice]
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