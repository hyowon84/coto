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
	var main_panel = Ext.create('Ext.tab.Panel', {
		id : 'main_panel',
		xtype: 'framed-tabs',
		controllers: 'tab-view',
		width : '100%',
		height: 850,
		items : [{
			title : '상품가격',
			autoScroll: false,
			autoWidth: true,
			autoHeight: true,
			items : [panel_prdprice]
		},{
			title : '경매',
			autoWidth: true,
			autoHeight: true,
			autoScroll: true,
			items : [panel_prdauction]
		}
		],
		listeners: {
			tabchange: function(tabs, newTab, oldTab) {
				Ext.suspendLayouts();
				Ext.resumeLayouts(true);
			}
		}
		,renderTo: 'extjsBody'
	});//panel_body
	
	panelResize(main_panel);
});