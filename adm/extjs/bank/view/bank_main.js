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
	grid_banklist.height = 800;
	grid_banklinklist.height = 800;

	grid_banklist.store.load();
	
	/* 화면 */
	var main_panel = Ext.create('Ext.Panel', {
		id : 'main_panel',
		extend: 'Ext.panel.Panel',
		xtype: 'layout-border',
		requires: [
			'Ext.layout.container.Border'
		],
		layout: 'border',
		width: '1900',
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
				id : 'navi_west',
				title: '<b>입출금내역</b>',
				region: 'west', 
				floatable: false,
				
//				collapsible: true,
//				headerPosition:'left',
//				split: true,
//				width: '25%',
				width : '70%',
				minWidth: 400,
				style : 'float:left; margin:0px; padding:0px;',
				items	: [grid_banklist]
			},
			{
				//top 
				id : 'navi_center',
				title: '<b>연결된 주문내역</b> [ '+'금: $'+v_GL+' / 은: $'+v_SL+' / $환율: '+v_USD+'원 ]',
				region: 'center',
				collapsible: false,
//				headerPosition:'left',
//				floatable: false,
				width: '30%',
				minWidth: 400,
				height: 830,
				style : 'float:left; margin:0px; padding:0px;',
				items : [grid_banklinklist]		//panel_grid
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


	panelResize(main_panel);	
});