Ext.onReady(function(){
	/************* ----------------  패널 START -------------- ******************/

	var store_salesMonth = Ext.create('Ext.data.Store',{
		fields: ['colName', 'data1', 'data2'],
		autoLoad : true,
		proxy : {
			type : 'ajax',
			extraParams : {
			},
			api : {
				read	: '/adm/json/sales_data.php?mode=month'
			},
			reader : {
				rootProperty : 'data',
				totalProperty : 'total'
			}
		}
	});
	
	var store_salesWeek = Ext.create('Ext.data.Store',{
		fields: ['colName', 'data1', 'data2'],
		autoLoad : true,
		proxy : {
			type : 'ajax',
			extraParams : {
			},
			api : {
				read	: '/adm/json/sales_data.php?mode=12days'
			},
			reader : {
				rootProperty : 'data',
				totalProperty : 'total'
			}
		}
	});
	
	var store_statsCnt = Ext.create('Ext.data.Store',{
		fields: ['colName', 'data1'],
		autoLoad : true,
		proxy : {
			type : 'ajax',
			extraParams : {
			},
			api : {
				read	: '/adm/json/sales_data.php?mode=statscnt'
			},
			reader : {
				rootProperty : 'data',
				totalProperty : 'total'
			}
		}
	});
	
	var store_oldinvoice = Ext.create('Ext.data.Store',{
		fields: ['colName', 'data1'],
		autoLoad : true,
		proxy : {
			type : 'ajax',
			extraParams : {
			},
			api : {
				read	: '/adm/json/sales_data.php?mode=oldinvoice'
			},
			reader : {
				rootProperty : 'data',
				totalProperty : 'total'
			}
		}
	});
	
	var store_visitinfo = Ext.create('Ext.data.Store',{
		autoLoad : true,
		pageSize : 50,
		remoteFilter:true,
		remoteSort:true,
		sorters:[
			{
				property: 'vi_id',
				direction:'DESC'
			}
		],
		proxy : {
			type : 'ajax',
			extraParams : {
			},
			api : {
				read	: '/adm/json/visitinfo.php'
			},
			reader : {
				rootProperty : 'data',
				totalProperty : 'total'
			}
		}
	});
	
	
	/*주문상태 통계*/
	var grid_statsCnt = Ext.create('Ext.grid.Panel',{
		width		: 420,
		height	: 260,
		columns	: [
			{ text: '주문상태', 		dataIndex: 'stats',				width: 100,		style:'text-align:center',		align:'center',	hidden:true	},
			{ text: '주문상태', 		dataIndex: 'stats_nm',			width: 130,		style:'text-align:center',		align:'center'	},
			{ text: '건수',			dataIndex: 'STATS_CNT',			width: 60,		style:'text-align:center',		align:'right',		renderer: rendererColumnFormat	},
			{ text: '물품수량',		dataIndex: 'TOTAL_QTY',			width: 85,		style:'text-align:center',		align:'right',		renderer: rendererColumnFormat	},
			{ text: '총 합계금액',	dataIndex: 'TOTAL_PRICE',		width: 140,		style:'text-align:center',		align:'right',		renderer: rendererColumnFormat	}
		],
		store : store_statsCnt,
		viewConfig: {
			stripeRows: true,
			getRowClass: orderStatsColorSetting
		}
	});
	
	/*발주건 미도착*/
	var grid_oldinvoice = Ext.create('Ext.grid.Panel',{
		width		: 920,
		height	: 260,
		columns	: [
			{ text: '공구코드', 		dataIndex: 'gpcode',				width: 130,		style:'text-align:center',		align:'center',	hidden:true	},
			{ text: '공구명', 		dataIndex: 'gpcode_name',		width: 130,		style:'text-align:center',		align:'center'	},
			{ text: 'IV_ID',			dataIndex: 'iv_id',				width: 120,		style:'text-align:center',		align:'center'	},
			{ text: '품목명',			dataIndex: 'iv_it_name',		width: 320,		style:'text-align:center',		align:'left'	},
			{ text: '발주량',			dataIndex: 'iv_qty',				width: 80,		style:'text-align:center',		align:'right',		renderer: rendererColumnFormat	},
			{ text: '매입가(￦)',	dataIndex: 'iv_dealer_price',	width: 130,		style:'text-align:center',		align:'right',		renderer: rendererColumnFormat	},
			{ text: '매입가($)',		dataIndex: 'iv_dealer_price',	width: 130,		style:'text-align:center',		align:'right',		renderer: rendererColumnFormat	},
			{ text: '딜러',			dataIndex: 'iv_dealer',			width: 80,		style:'text-align:center',		align:'left'	}
		],
		store : store_oldinvoice
	});
	
	/*마지막 방문자정보 */
	var grid_visitinfo = Ext.create('Ext.grid.Panel',{
		width		: '100%',
		height	: 260,
		columns	: [
			{ text: '방문일', 		dataIndex: 'vi_date',		width: 100,		style:'text-align:center',		align:'center'	},
			{ text: '방문시간', 		dataIndex: 'vi_time',		width: 100,		style:'text-align:center',		align:'center'	},
			{ text: 'IP주소',			dataIndex: 'vi_ip',			width: 160,		style:'text-align:center',		align:'left'	},
			{ text: '이동경로출처',	dataIndex: 'vi_referer',	width: 600,		style:'text-align:center',		align:'left'	},
			{ text: '접속환경',		dataIndex: 'vi_agent',		width: 600,		style:'text-align:center',		align:'left'	}
		],
		store : store_visitinfo,
		bbar : {
			plugins: new Ext.ux.SlidingPager(),
			xtype : 'pagingtoolbar',
			store : store_visitinfo,
			displayInfo : true,
			displayMsg : '{0}/{1} Total - {2}',
			emptyMsg : 'No Data'
		}
	});
	
	
	var store_domesticprice = Ext.create('Ext.data.Store',{
		autoLoad : true,
		autoSync : true,
		proxy : {
			type : 'ajax',
			extraParams : {
			},
			api : {
				read	: '/adm/json/domesticprice.php',
				update : '/adm/json/domesticprice.update.php'
			},
			reader : {
				rootProperty : 'data',
				totalProperty : 'total'
			},
			writer : {
				type : 'json',
				writeAllFields : true,
				encode : true,
				rootProperty : 'data'
			}
		}
	});
	
	/*국내 금은백금팔라듐 시세 */
	var grid_domesticprice = Ext.create('Ext.grid.Panel',{
		plugins: [Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1})],
		width		: '100%',
		height	: 100,
		columns	: [
       	   { text : 'NO',						dataIndex: 'no',				hidden:true,		width: 90,		style:'text-align:center',		align:'center'   },
				{ text : '금(1g) 살때',			dataIndex: 'GL_G_BUY',		editor: { allowBlank : false },	width: 90,		style:'text-align:center',		align:'center',	renderer: Ext.util.Format.numberRenderer('0,000')   },
				{ text : '금(1g) 팔때',			dataIndex: 'GL_G_SELL',		editor: { allowBlank : false },	width: 90,		style:'text-align:center',		align:'center',	renderer: Ext.util.Format.numberRenderer('0,000')   },
				{ text : '금(1oz) 살때',		dataIndex: 'GL_OZ_BUY',		editor: { allowBlank : false },	width: 90,		style:'text-align:center',		align:'center',	renderer: Ext.util.Format.numberRenderer('0,000')   },
				{ text : '금(1oz) 팔때',		dataIndex: 'GL_OZ_SELL',	editor: { allowBlank : false },	width: 90,		style:'text-align:center',		align:'center',	renderer: Ext.util.Format.numberRenderer('0,000')   },
				{ text : '금(1돈) 살때',		dataIndex: 'GL_DON_BUY',	editor: { allowBlank : false },	width: 90,		style:'text-align:center',		align:'center',	renderer: Ext.util.Format.numberRenderer('0,000')   },
				{ text : '금(1돈) 팔때',		dataIndex: 'GL_DON_SELL',	editor: { allowBlank : false },	width: 90,		style:'text-align:center',		align:'center',	renderer: Ext.util.Format.numberRenderer('0,000')   },
				{ text : '금(18k) 살때',		dataIndex: 'GL_18K_BUY',	editor: { allowBlank : false },	width: 90,		style:'text-align:center',		align:'center',	renderer: Ext.util.Format.numberRenderer('0,000')   },
				{ text : '금(18k) 팔때 ',		dataIndex: 'GL_18K_SELL',	editor: { allowBlank : false },	width: 90,		style:'text-align:center',		align:'center',	renderer: Ext.util.Format.numberRenderer('0,000')   },
				{ text : '금(14k) 살때',		dataIndex: 'GL_14K_BUY',	editor: { allowBlank : false },	width: 90,		style:'text-align:center',		align:'center',	renderer: Ext.util.Format.numberRenderer('0,000')   },
				{ text : '금(14k) 팔때',		dataIndex: 'GL_14K_SELL',	editor: { allowBlank : false },	width: 90,		style:'text-align:center',		align:'center',	renderer: Ext.util.Format.numberRenderer('0,000')   },
				{ text : '은(1g) 살때',			dataIndex: 'SL_G_BUY',		editor: { allowBlank : false },	width: 90,		style:'text-align:center',		align:'center',	renderer: Ext.util.Format.numberRenderer('0,000')   },
				{ text : '은(1g) 팔때',			dataIndex: 'SL_G_SELL',		editor: { allowBlank : false },	width: 90,		style:'text-align:center',		align:'center',	renderer: Ext.util.Format.numberRenderer('0,000')   },
				{ text : '은(1oz) 살때',		dataIndex: 'SL_OZ_BUY',		editor: { allowBlank : false },	width: 90,		style:'text-align:center',		align:'center',	renderer: Ext.util.Format.numberRenderer('0,000')   },
				{ text : '은(1oz) 팔때',		dataIndex: 'SL_OZ_SELL',	editor: { allowBlank : false },	width: 90,		style:'text-align:center',		align:'center',	renderer: Ext.util.Format.numberRenderer('0,000')   },
				{ text : '백금(1g) 살때',		dataIndex: 'PT_G_BUY',		editor: { allowBlank : false },	width: 90,		style:'text-align:center',		align:'center',	renderer: Ext.util.Format.numberRenderer('0,000')   },
				{ text : '백금(1g) 팔때',		dataIndex: 'PT_G_SELL',		editor: { allowBlank : false },	width: 90,		style:'text-align:center',		align:'center',	renderer: Ext.util.Format.numberRenderer('0,000')   },
				{ text : '팔라듐(1g) 살때',	dataIndex: 'PD_G_BUY',		editor: { allowBlank : false },	width: 90,		style:'text-align:center',		align:'center',	renderer: Ext.util.Format.numberRenderer('0,000')   },
				{ text : '팔라듐(1g) 팔때',	dataIndex: 'PD_G_SELL',		editor: { allowBlank : false },	width: 90,		style:'text-align:center',		align:'center',	renderer: Ext.util.Format.numberRenderer('0,000')   }
		],
		store : store_domesticprice,
		listeners : {
			selectionchange: function(view, records) {
		 	},
			edit: function (editor, e, eOpts) {
				if(globalData.temp == null) {
					globalData.temp = [];
				}
				globalData.temp.push([editor.context.rowIdx, editor.context.field, editor.context.originalValue]);
			},
			afterrender: function(obj, opt) 
			{
			new Ext.util.KeyMap({
				target: document,
				binding: [
						{
							key: "z",
							ctrl:true,
							fn: function(){
								if(globalData.temp != null && globalData.temp.length > 0) {
								var store = obj.getStore();
								var temp = globalData.temp;
								var length = temp.length-1;
								
								//rowIdx, field, value 순으로 temp의 값을 store에 입력
								store.getData().getAt(temp[length][0]).set(temp[length][1],temp[length][2]);
								globalData.temp.pop(length);
								} else {
									return;
								}
							}
						}
					],
					scope: this
				}); 
			}
		}
	});
	
	
	var chartPanel = Ext.create('Ext.Panel', {
		id : 'chartPanel',
		extend: 'Ext.panel.Panel',
		xtype: 'clustered-column',
		width: '100%',
		height: 410,
		items: [
					{
						xtype: 'chart',
						width: 650,
						height: 410,
						padding: '10 0 0 0',
						animate: true,
						shadow: false,
						style: 'background: #fff;',
						legend: {
							position: 'bottom',
							boxStrokeWidth: 0,
							labelFont: '12px Helvetica'
						},
						store: store_salesMonth,
						insetPadding: 40,
						items: [	{
										type  : 'text',
										text  : '월간 매출/지출',
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
								title: [ '주문(취소제외)', '지출' ],
								xField: 'colName',
								yField: [ 'data1', 'data2' ],
								style: {
								opacity: 0.80
								},
								highlight: {
								fill: '#000',
								'stroke-width': 1,
								stroke: '#000'
								},
								tips: {
									trackMouse: true,
									style: 'background: #FFF',
									height: 20,
									renderer: function(storeItem, item) {
										var browser = item.series.title[Ext.Array.indexOf(item.series.yField, item.yField)];
										this.setTitle(storeItem.get('colName') + ' ' + browser + ' : ' + Ext.util.Format.number(storeItem.get(item.yField), "0,000") + '원');
									}
								}
						}]
					},{
						xtype: 'chart',
						width: 650,
						height: 410,
						padding: '10 0 0 0',
						animate: true,
						shadow: false,
						style: 'background: #fff;',
						legend: {
							position: 'bottom',
							boxStrokeWidth: 0,
							labelFont: '12px Helvetica'
						},
						store: store_salesWeek,
						insetPadding: 40,
						items: [	{
										type  : 'text',
										text  : '주간 매출/지출',
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
							renderer: function(v) { return v / 10000000 + '천'; }
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
								title: [ '주문(취소제외)', '지출' ],
								xField: 'colName',
								yField: [ 'data1', 'data2' ],
								style: {
								opacity: 0.80
								},
								highlight: {
								fill: '#000',
								'stroke-width': 1,
								stroke: '#000'
								},
								tips: {
									trackMouse: true,
									style: 'background: #FFF',
									height: 20,
									renderer: function(storeItem, item) {
										var browser = item.series.title[Ext.Array.indexOf(item.series.yField, item.yField)];
										this.setTitle(storeItem.get('colName') + ' ' + browser + ' : ' + Ext.util.Format.number(storeItem.get(item.yField), "0,000") + '원');
									}
								}
						}]
					}
		]		
	});//chartPanel end

	
	
	/* 화면 */
	var main_panel = Ext.create('Ext.Panel', {
		id : 'main_panel',
		extend: 'Ext.panel.Panel',
		xtype: 'layout-absolute',
		layout: 'absolute',
		requires: [
			'Ext.layout.container.Absolute'
		],
		width: 1350,
		height: 1200,
		bodyBorder: false,
		style : 'margin:0px auto;',
		defaults: {
			split: true,
			bodyPadding: 0,
			width : 650,
			height : 450
		},
		items: [
		        {
		      	  title : '오늘의 금/은/백금/팔라듐 시세',
	      		  x : 0,
	      		  y : 0,
	      		  width : 1350,
	      		  height: 150,
	      		  items : [grid_domesticprice]
		        },
		        {
		      	  title : '차트',
		      	  width : 1350,
		      	  height: 450,
	      		  x : 0,
	      		  y : 151,
	      		  items : [chartPanel]
		        },
		        {
		      	  title : '금일까지 주문내역 통계',
	      		  x : 0,
	      		  y : 601,
	      		  width : 424,
	      		  height: 300,
	      		  items : [grid_statsCnt]
		        },
		        {
		      	  title : '미도착 발주내역',
	      		  x : 425,
	      		  y : 601,
	      		  width : 920,
	      		  height: 300,
	      		  items : [grid_oldinvoice]
		        },
		        {
		      	  title : '방문자 접속정보',
	      		  x : 0,
	      		  y : 901,
	      		  width : 1350,
	      		  height: 300,
	      		  items : [grid_visitinfo]
		        }
		        
		]
		,renderTo: 'extjsBody'
	});//panel_body
	
});
