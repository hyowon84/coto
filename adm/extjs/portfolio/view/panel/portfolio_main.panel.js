var panel_investform = Ext.create('Ext.Panel', {
	id : 'panel_investform',
	extend: 'Ext.form.Panel',
	xtype: 'layout-vertical-box',
	requires: [
			'Ext.layout.container.VBox'
	],		
	width: '100%',
	bodyPadding: 10,
	border:0,
	defaults: {
		frame: false
	},
	style: '',
	items: [
				{
					width : bodyWidth,
					height: 180,
					border:0,
					style: 'text-align:center;',
					items: [grid_expectInvest, grid_beginFundSet, grid_beginFundBuy]
				},
				{
					xtype: 'panel',
					width: bodyWidth,
					height: midGridHeight + 40,
					style: 'text-align:center;',
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
					style: 'text-align:center;',
					items: [grid_estimate]
				}
	]
});


var panel_chart = Ext.create('Ext.Panel', {
	id : 'panel_chart',
//	xtype: 'theme-chart',
	xtype: 'stacked-column-100',
	extend: 'Ext.Panel',
	width: '100%',
	defaults: {
		style : 'float:left; font-size:1.5em; border:none;',
		border: 0,
		fontFamily : 'NanumGothic',
		frame: false
	},
	items: [{
					xtype: 'chart',
					width: 360,
					height: 400,
					padding: '10 0 0 0',
					style: 'float:left;background: #fff',
					animate: true,
					shadow: true,
					store: store_chartMetalPer,
					insetPadding: 60,
					legend: {
							field: 'colName',
							position: 'bottom',
							boxStrokeWidth: 0,
							labelFont: '12px Helvetica'
					},
					/*텍스트 배치*/
					items: [	{
									type	: 'text',
									text	: '금/은 비중',
									font	: '22px Helvetica',
									width : 100,
									height: 30,
									x : 40, // the sprite x position
									y : 12	// the sprite y position
								}
					],
					/*차트 속성 설정*/
					series: [{
									type: 'pie',
									animate: true,
									angleField: 'data1',
									showInLegend: true,
									highlight: {
										segment: {
											margin: 40
										}
									},
									highlightCfg: {},
									label: {
											field: 'colName',
											display: 'outside',
											calloutLine: true,
											contrast: true
									},
									style: {
										'stroke-width': 1,
										'stroke': '#fff'
									},
									tips: {
											trackMouse: true,
											renderer: function(storeItem, item) {
													this.setTitle(storeItem.get('colName') + ': ' + Ext.util.Format.number(storeItem.get('data1'), "0,000.0") + '%');
											}
									}
								}
					]
				},
				{
					xtype: 'chart',
					width: 360,
					height: 400,
					padding: '10 0 0 0',
					style: 'float:left;background: #fff',
					animate: true,
					shadow: true,
					store: store_chartInvest,
					insetPadding: 60,
					legend: {
							field: 'colName',
							position: 'bottom',
							boxStrokeWidth: 0,
							labelFont: '12px Helvetica'
					},
					items: [{
							type	: 'text',
							text	: '예상투자규모',
							font	: '22px Helvetica',
							width : 100,
							height: 30,
							x : 40, // the sprite x position
							y : 12	// the sprite y position
					}],
					series: [{
									type: 'pie',
									animate: true,
									angleField: 'data1',
									showInLegend: true,
									highlight: {
										segment: {
											margin: 40
										}
									},
									highlightCfg: {},
									label: {
											field: 'colName',
											display: 'outside',
											calloutLine: true,
											contrast: true
									},
									style: {
										'stroke-width': 1,
										'stroke': '#fff'
									},
									tips: {
											trackMouse: true,
											renderer: function(storeItem, item) {
													this.setTitle(storeItem.get('colName') + ': ￦' + Ext.util.Format.number(storeItem.get('data1'), "0,000")  );
											}
									}
								}
					]
				},
				{
					xtype: 'chart',
					width: 360,
					height: 400,
					padding: '10 0 0 0',
					style: 'float:left;background: #fff',
					animate: true,
					shadow: true,
					store: store_chartGoldPer,
					insetPadding: 60,
					legend: {
							field: 'colName',
							position: 'bottom',
							boxStrokeWidth: 0,
							labelFont: '12px Helvetica'
					},
					items: [{
							type	: 'text',
							text	: '금 비중',
							font	: '22px Helvetica',
							width : 100,
							height: 30,
							x : 40, // the sprite x position
							y : 12	// the sprite y position
					}],
					series: [{
									type: 'pie',
									animate: true,
									angleField: 'data1',
									showInLegend: true,
									highlight: {
										segment: {
											margin: 40
										}
									},
									highlightCfg: {},
									label: {
											field: 'colName',
											display: 'outside',
											calloutLine: true,
											contrast: true
									},
									style: {
										'stroke-width': 1,
										'stroke': '#fff'
									},
									tips: {
											trackMouse: true,
											renderer: function(storeItem, item) {
													this.setTitle(storeItem.get('colName') + ': ' + Ext.util.Format.number(storeItem.get('data1'), "0,000.0") + '%');
											}
									}
								}
					]
				},
				{
					xtype: 'chart',
					width: 360,
					height: 400,
					padding: '10 0 0 0',
					style: 'float:left;background: #fff',
					animate: true,
					shadow: true,
					store: store_chartSilverPer,
					insetPadding: 60,
					legend: {
							field: 'colName',
							position: 'bottom',
							boxStrokeWidth: 0,
							labelFont: '12px Helvetica'
					},
					items: [{
							type	: 'text',
							text	: '은 비중',
							font	: '22px Helvetica',
							width : 100,
							height: 30,
							x : 40, // the sprite x position
							y : 12	// the sprite y position
					}],
					series: [{
									type: 'pie',
									animate: true,
									angleField: 'data1',
									showInLegend: true,
									highlight: {
										segment: {
											margin: 40
										}
									},
									highlightCfg: {
											
									},
									label: {
											field: 'colName',
											display: 'outside',
											calloutLine: true,
											contrast: true
									},
									style: {
										'stroke-width': 1,
										'stroke': '#fff'
									},
									tips: {
											trackMouse: true,
											renderer: function(storeItem, item) {
													this.setTitle(storeItem.get('colName') + ': ' + Ext.util.Format.number(storeItem.get('data1'), "0,000.0") + '%');
											}
									}
								}
					]
				},
				{/*성향별 예상자금분배액*/
						xtype: 'chart',
						store: store_MetalInvestPrice,
						style: 'float:left;background: #fff;',
						width: 450,
						height: 410,
						padding: '10 0 0 0',
						animate: true,
						shadow: true,
						legend: {
							position: 'bottom',
							boxStrokeWidth: 0,
							labelFont: '12px Helvetica'
						},
						insetPadding: 60,
						items: [	{
										type  : 'text',
										text  : '성향별 자금분배금액',
										font  : '22px Helvetica',
										width : 100,
										height: 30,
										x : 40, //the sprite x position
										y : 12  //the sprite y position
									}
						],
						axes: [{
							type: 'Numeric',
							position: 'left',
							fields: 'data1',
							grid: true,
							minimum: 0,
							label: {
								renderer: function(v) { return v / 100000000 + '억'; }
							}
						}, {
							type: 'Category',
							position: 'bottom',
							fields: 'colName',
							grid: true,
							label: {
							rotate: {
								degrees: -45
							}
							}
						}],
						series: [{
								type: 'column',
								axis: 'left',
								title: [ 'Gold', 'Silver' ],
								xField: 'colName',
								yField: [ 'data1', 'data2' ],
								style: {
								opacity: 0.80
								},
								highlight: {
									fill: '#000',
									'stroke-width': 2,
									stroke: '#000'
								},
								tips: {
									trackMouse: true,
									style: 'background: #FFF',
									height: 20,
									renderer: function(storeItem, item) {
										//var browser = item.series.title[Ext.Array.indexOf(item.series.yField, item.yField)];
										this.setTitle(storeItem.get('colName') + ' ' + ' : ' + Ext.util.Format.number(storeItem.get(item.yField), "0,000") + '원');
									}
								}
						}]
				},
				{/*성향별 예상자금분배율*/
						xtype: 'chart',
						store: store_MetalInvestPer,
						style: 'float:left;background: #fff;',
						width: 350,
						height: 410,
						padding: '10 0 0 0',
						animate: true,
						shadow: true,
						legend: {
							position: 'bottom',
							boxStrokeWidth: 0,
							labelFont: '12px Helvetica'
						},
						insetPadding: 40,
						items: [	{
										type  : 'text',
										text  : '성향별 목표설정율',
										font  : '22px Helvetica',
										width : 100,
										height: 30,
										x : 40, //the sprite x position
										y : 12  //the sprite y position
									}
						],
						axes: [{
							type: 'Numeric',
							position: 'left',
							fields: 'data1',
							grid: true,
							minimum: 0,
							label: {
								renderer: function(v) { return v + '%'; }
							}
						}, {
							type: 'Category',
							position: 'bottom',
							fields: 'colName',
							grid: true,
							label: {
							rotate: {
								degrees: -45
							}
							}
						}],
						series: [{
								type: 'column',
								axis: 'left',
								title: [ 'Gold', 'Silver' ],
								xField: 'colName',
								yField: [ 'data1', 'data2' ],
								stacked: true,
								style: {
								opacity: 0.80
								},
								highlight: {
									fill: '#000',
									'stroke-width': 2,
									stroke: '#000'
								},
								tips: {
									trackMouse: true,
									style: 'background: #FFF',
									height: 20,
									renderer: function(storeItem, item) {
										//var browser = item.series.title[Ext.Array.indexOf(item.series.yField, item.yField)];
										this.setTitle(storeItem.get('colName') + ' ' + ' : ' + Ext.util.Format.number(storeItem.get(item.yField), "0,000.0") + '%');
									}
								}
						}]
				},
				{/*목표금액, 달성금액 */
						xtype: 'chart',
						store: store_TargetAchieve,
						style: 'float:left;background: #fff;',
						width: 670,
						height: 410,
						padding: '10 0 0 0',
						animate: true,
						shadow: false,
						legend: {
							position: 'right',
							boxStrokeWidth: 0,
							labelFont: '12px Helvetica'
						},
						insetPadding: 40,
						items: [	{
										type  : 'text',
										text  : '목표금액, 달성금액',
										font  : '22px Helvetica',
										width : 100,
										height: 30,
										x : 40, //the sprite x position
										y : 12  //the sprite y position
									}
						],
						axes: [	{
										type: 'Numeric',
										fields: 'data1',
										position: 'bottom',										
										grid: true,
										minimum: 0,
										label: {
											renderer: function(v) { return v / 100000000 + '억'; }
										}
									}, 
									{
										type: 'Category',
										fields: 'colName',
										position: 'left',										
										grid: true
									}
						],
						series: [{
								type: 'bar',
								axis: 'bottom',
								title: [ '목표', '달성' ],
								xField: 'colName',
								yField: [ 'data1', 'data2' ],
								style: {
									opacity: 0.80
								},
								highlight: {
									fill: '#000',
									'stroke-width': 2,
									stroke: '#000'
								},
								tips: {
									trackMouse: true,
									style: 'background: #FFF',
									height: 20,
									renderer: function(storeItem, item) {
										//var browser = item.series.title[Ext.Array.indexOf(item.series.yField, item.yField)];
										this.setTitle(storeItem.get('colName') + ' ' + ' : ' + Ext.util.Format.number(storeItem.get(item.yField), "0,000") + '원');
									}
								}
						}]
				}
	]		
});//chartPanel end


Ext.define('Ext.chart.theme.CustomCharts', {
	extend: 'Ext.chart.theme.Base',
	config: {
		axis: {
			stroke: '#7F8C8D'
		},
		colors: [ '#1ABC9C', '#F1C40F', '#3498DB', '#C0392B', '#9B59B6' ]
	},

	constructor: function(config) {
        var titleLabel = {
                font: 'bold 18px Helvetica'
            },
            axisLabel = {
                fill: '#7F8C8D',
                font: '12px Helvetica',
                spacing: 2,
                padding: 5
            };

        this.callParent([Ext.apply(this.config, config,  {
            axisLabelLeft: axisLabel,
            axisLabelBottom: axisLabel,
            axisTitleLeft: titleLabel,
            axisTitleBottom: titleLabel
        })]);
    }
});