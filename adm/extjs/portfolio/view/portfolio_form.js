/**
 *	Ext.onReady Similar Code
 *
 * = JAVASCRIPT :	window.onload = function(){
 *						// codehere
 *					}
 * = jQuery : $(document).ready({
 *					// codehere
 *			})
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
	var panel_investform = Ext.create('Ext.Panel', {
		id : 'panel_investform',
		extend: 'Ext.form.Panel',
		xtype: 'layout-vertical-box',
		requires: [
		    'Ext.layout.container.VBox'
		],		
		width: '100%',
		height: 800,
		bodyPadding: 10,
		border:0,
		defaults: {
			frame: false
		},
		style: 'margin:0px auto; text-align:center;',
		items: [
					{
						width : bodyWidth,
						height: 180,
						border:0,
						style: 'margin:0px auto; text-align:center;',
						items: [grid_expectInvest, grid_beginFundSet, grid_beginFundBuy]
					},
					{
						xtype: 'panel',
						width: bodyWidth,
						height: midGridHeight + 40,
						style: 'margin:0px auto; text-align:center;',
						border:0,
						items: [
									{
										xtype: 'panel',
										title: { cls:'center',	text:'성향별 목표 예상수치'},
										width: midGridWidth*2+3,
										height: (midGridHeight+33),
										frame: true,
										border: 0,
										style: 'float:left; padding:0px; margin:0px;',
										items: [grid_invest, grid_investdtl]	//, txt_investdtl
									},
									{
										xtype: 'panel',
										title: { cls:'center',	text:'성향별 목표 달성수치'},
										cls: 'center',
										width: midGridWidth*2+3,
										height: (midGridHeight+33),
										frame: true,
										border: 0,
										style: 'float:left; padding:0px; margin:0px;',
										items: [grid_achInvest, grid_achInvestdtl]	//, txt_achinvestdtl
									}
					]	//,grid_investdtl
			    },
			    {
					//title: '시세반영 자산평가',
					width : 1454,
					height: 180,
					border:0,
					style: 'margin:0px auto; text-align:center;',
					items: [grid_estimate]
			    }
		],
		renderTo: 'extjsBody'
	});//panel_body
	
	
});