
/* 포트폴리오 통계 */
Ext.define('model.investdtl', {
   extend: 'Ext.data.Model',
	fields : [	
					{name: 'pf_id',			type: 'string'},
					{name: 'invest_type',			type: 'string'},
					{name: 'metal_type',		type: 'int'},
					{name: 'target_per',		type: 'int'},
					{name: 'TARGET_PRICE',	type: 'float'}
    ]
});


/************* ----------------	그리드 START -------------- ******************/

var store_expectInvest = Ext.create('Ext.data.Store',{
	autoLoad : true,
	autoSync : true,
	remoteSort: true,
	groupField: 'pf_id',
	fields : [	
					{name: 'pf_id',			type: 'string'},
					{name: 'fund_type',		type: 'string'},
					{name: 'EXPECT_PRICE',	type: 'int'},
					{name: 'EXPECT_PER',		type: 'float'}
	],
	proxy : {
		type : 'ajax',
		extraParams : {
		},
		api : {
			read	: '/adm/portfolio/json/invest.php?mode=expectInvest&pf_id=PF201607250002'
		},
		reader : {
			rootProperty : 'data',
			totalProperty : 'total'
		}
	}
});

var store_beginFundSet = Ext.create('Ext.data.Store',{
	autoLoad : true,
	autoSync : true,
	remoteSort: true,
	groupField: 'pf_id',
	fields : [	
					{name: 'pf_id',			type: 'string'},
					{name: 'fund_type',		type: 'string'},
					{name: 'EXPECT_PRICE',	type: 'int'},
					{name: 'EXPECT_PER',		type: 'float'}
	],
	proxy : {
		type : 'ajax',
		extraParams : {
		},
		api : {
			read	: '/adm/portfolio/json/invest.php?mode=beginFundSet&pf_id=PF201607250002'
		},
		reader : {
			rootProperty : 'data',
			totalProperty : 'total'
		}
	}
});

var store_beginFundBuy = Ext.create('Ext.data.Store',{
	autoLoad : true,
	autoSync : true,
	remoteSort: true,
	groupField: 'pf_id',
	fields : [	
					{name: 'pf_id',			type: 'string'},
					{name: 'fund_type',		type: 'string'},
					{name: 'surplus_year',	type: 'int'},
					{name: 'surplus_fund',	type: 'float'}
	],
	proxy : {
		type : 'ajax',
		extraParams : {
		},
		api : {
			read	: '/adm/portfolio/json/invest.php?mode=beginFundBuy&pf_id=PF201607250002',
			update : '/adm/portfolio/crud/beginfund.update.php'
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



var store_invest = Ext.create('Ext.data.Store',{
	autoLoad : true,
	autoSync : true,
	remoteSort: true,
	groupField: 'pf_id',
	fields : [	
					{name: 'pf_id',			type: 'string'},
					{name: 'invest_type',	type: 'string'},
					{name: 'target_per',		type: 'float'},
					{name: 'TARGET_PRICE',	type: 'int'}
	],
	proxy : {
		type : 'ajax',
		extraParams : {
		},
		api : {
			read	: '/adm/portfolio/json/invest.php?mode=invest&pf_id=PF201607250002',
			update : '/adm/portfolio/crud/invest.update.php'
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



var store_investdtl = Ext.create('Ext.data.Store',{
		autoLoad : true,
		autoSync : true,
		remoteSort: true,
		groupField: 'invest_type',
		fields : [	
						{name: 'pf_id',			type: 'string'},
						{name: 'invest_type',	type: 'string'},
						{name: 'target_per',		type: 'float'},
						{name: 'TARGET_PRICE',	type: 'int'}
		],
		proxy : {
			type : 'ajax',
			extraParams : {
			},
			api : {
				read	: '/adm/portfolio/json/invest.php?mode=investdtl&pf_id=PF201607250002',
				update : '/adm/portfolio/crud/investdtl.update.php'
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


var store_achInvest = Ext.create('Ext.data.Store',{
	autoLoad : true,
	autoSync : true,
	remoteSort: true,
	groupField: 'pf_id',
	fields : [	
					{name: 'pf_id',				type: 'string'},
					{name: 'invest_type',		type: 'string'},
					{name: 'target_per',			type: 'float'},
					{name: 'TARGET_PRICE',		type: 'int'},
					{name: 'ACH_PER',				type: 'float'},
					{name: 'ACH_PRICE',			type: 'int'}
	],
	proxy : {
		type : 'ajax',
		extraParams : {
		},
		api : {
			read	: '/adm/portfolio/json/invest.php?mode=invest&pf_id=PF201607250002',
			update : '/adm/portfolio/crud/invest.update.php'
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

var store_achInvestdtl = Ext.create('Ext.data.Store',{
		autoLoad : true,
		autoSync : true,
		remoteSort: true,
		groupField: 'invest_type',
		fields : [	
						{name: 'pf_id',			type: 'string'},
						{name: 'invest_type',	type: 'string'},
						{name: 'target_per',		type: 'float'},
						{name: 'ACH_PRICE',		type: 'int'}
		],
		proxy : {
			type : 'ajax',
			extraParams : {
			},
			api : {
				read	: '/adm/portfolio/json/invest.php?mode=achinvestdtl&pf_id=PF201607250002',
				update : '/adm/portfolio/crud/investdtl.update.php'
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


var store_estimate = Ext.create('Ext.data.Store',{
	autoLoad : true,
	autoSync : true,
	remoteSort: true,
	groupField: 'pf_id',
	fields : [	
					{name: 'pf_id',						type: 'string'},
					{name: 'metal_type',					type: 'string'},
					{name: 'TOTAL_GRAM',					type: 'int'},
					{name: 'ESTIMATE_NOW',				type: 'int'},
					{name: 'ESTIMATE_BUYED',			type: 'int'},
					{name: 'ESTIMATE_PROFIT',			type: 'int'},
					{name: 'ESTIMATE_PROFIT_PER',		type: 'float'},
					{name: 'flowprice_now',				type: 'int'},
					{name: 'flowprice_buyed',			type: 'int'},
					{name: 'flowprice_profit',			type: 'int'},
					{name: 'flowprice_profit_per',	type: 'float'}
	],
	proxy : {
		type : 'ajax',
		extraParams : {
		},
		api : {
			read	: '/adm/portfolio/json/invest.php?mode=estimate&pf_id=PF201607250002'
		},
		reader : {
			rootProperty : 'data',
			totalProperty : 'total'
		}
	}
});



/** 상단 **********************************************************************************/

/* 상단 탑 예상투자규모 */
var grid_expectInvest = Ext.create('Ext.grid.Panel',{
	plugins: [Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1,errorSummary: false})],
	title: { cls:'center',	text:'예상 투자 규모'},
	width	: topGridWidth,
	height : topGridHeight,
	border: 0,
	frame : true,
	autoScroll: false,
	features: statsFeatures,
	viewConfig: statsViewConfig,
	margin: '0 20 0 0',
	style : 'float:left;',
	bodyStyle: 'overflow-x:hidden;overflow-y:hidden;',
	store : store_expectInvest,
	columns	: [
    	   { text : 'pf_id',		dataIndex: 'pf_id',				width: 140,		style:'text-align:center',		align:'center',	sortable:false,	hidden:true	},
			{ text : '분류',		dataIndex: 'fund_type',			width: 122,		style:'text-align:center',		align:'center',	sortable:false	},
			{ text : 'metal',		dataIndex: 'metal_type',		width: 80,		style:'text-align:center',		align:'center',	sortable:false,		summaryRenderer: rendererSummaryFormat	},
			{ text : '금액',		dataIndex: 'EXPECT_PRICE',		width: 180,		style:'text-align:center',		align:'right',		summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	renderer: rendererColumnFormat,	sortable:false },
			{ text : '비중',		dataIndex: 'EXPECT_PER',		width: 100,		style:'text-align:center',		align:'right',		summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	renderer: rendererColumnFormat,	sortable:false	}
			
	],
	listeners : statsDefaultListener
});

/* 상단 탑 초기구성자금 */
var grid_beginFundSet = Ext.create('Ext.grid.Panel',{
	plugins: [Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1,errorSummary: false})],
	title: { cls:'center',	text:'초기 구성 자금'},
	width	: topGridWidth,
	height : topGridHeight,
	border: 0,
	frame : true,
	autoScroll: false,
	features: statsFeatures,
	viewConfig: statsViewConfig,
	margin: '0 20 0 0',
	style : 'float:left;',
	bodyStyle: 'overflow-x:hidden;overflow-y:hidden;',
	store : store_beginFundSet,
	columns	: [
    	   { text : 'pf_id',		dataIndex: 'pf_id',				width: 140,		style:'text-align:center',		align:'center',	sortable:false,		hidden:true	},
			{ text : '분류',		dataIndex: 'fund_type',			width: 192,		style:'text-align:center',		align:'center',	sortable:false,		hidden:false },
			{ text : 'metal',		dataIndex: 'metal_type',		width: 80,		style:'text-align:center',		align:'center',	sortable:false,		summaryRenderer: rendererSummaryFormat	},
			{ text : '금액',		dataIndex: 'EXPECT_PRICE',		width: 130,		style:'text-align:center',		align:'right',		summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	renderer: rendererColumnFormat,	sortable:false },
			{ text : '비중',		dataIndex: 'EXPECT_PER',		width: 80,		style:'text-align:center',		align:'right',		summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	renderer: rendererColumnFormat,	sortable:false	}
	],
	listeners : statsDefaultListener
});

/* 상단 탑 추가매수자금 */
var grid_beginFundBuy = Ext.create('Ext.grid.Panel',{
	plugins: [Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1,errorSummary: false})],
	title: { cls:'center',	text:'추가 매수 자금'},
	width	: 444,
	height : topGridHeight,
	border: 0,
	frame : true,
	autoScroll: false,
	features: statsFeatures,
	viewConfig: statsViewConfig,
	style : 'float:right;',
	bodyStyle: 'overflow-x:hidden;overflow-y:hidden;',
	store : store_beginFundBuy,
	columns	: [
    	   { text : '분류',		dataIndex: 'fund_type',			width: 160,		style:'text-align:center',		align:'center',	sortable:false	},
			{ text : '기간',		dataIndex: 'surplus_year',		width: 130,		style:'text-align:center',		align:'center',	editor: { allowBlank : false },		sortable:false,		renderer: rendererColumnFormat },
			{ text : '금액',		dataIndex: 'surplus_fund',		width: 150,		style:'text-align:center',		align:'right',		editor: { allowBlank : false },		summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	renderer: rendererColumnFormat,	sortable:false }
	],
	listeners : statsDefaultListener
});



/** 중앙 **********************************************************************************/

/* 투자성향 설정 */
var grid_invest = Ext.create('Ext.grid.Panel',{
	plugins: [Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1,errorSummary: false})],
	width	: midGridWidth,
	height : midGridHeight,
	border: 0,
	autoScroll: false,
	features: [
					{
						ftype : 'groupingsummary',
						groupHeaderTpl: '{name}',
						hideGroupedHeader: true,
            		enableGroupingMenu: true,
            		collapsible : false
					}	           
	],
	viewConfig: {
		stripeRows: true,
		forceFit: true,
		getRowClass: function(record, index) {
			return 'invest_row';
		}
	},
	style : 'float:left;',
	bodyStyle: 'overflow-x:hidden;overflow-y:hidden;',
	store : store_invest,
	columns	: [
    	   { text : 'pf_id',			dataIndex: 'pf_id',				width: 140,		style:'text-align:center',		align:'center',	hidden:true,	sortable:false	},
			{ text : '성향',			dataIndex: 'invest_type',		width: 130,		style:'text-align:center',		align:'center',	sortable:false	},
			{ text : '목표 %',		dataIndex: 'target_per',		width: 100,		style:'text-align:center',		align:'right',	summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	renderer: rendererColumnFormat,	editor: { allowBlank : false },	sortable:false },
			{ text : '목표금액 ￦',	dataIndex: 'TARGET_PRICE',		width: 130,		style:'text-align:center',		align:'right',	summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	renderer: Ext.util.Format.numberRenderer('0,000'),	sortable:false	},
	],
	listeners : defaultListener
});

/* 투자성향 설정 */
var grid_investdtl = Ext.create('Ext.grid.Panel',{
	plugins: [Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1,errorSummary: false})],
	width	: midGridWidth,
	height : midGridHeight-4,
	border: 0,
	features: [
					{
						ftype : 'groupingsummary',
						groupHeaderTpl: '{name}',
						hideGroupedHeader: true,
            		enableGroupingMenu: true,
            		collapsible : false
					},{
						ftype: 'summary',
						dock: 'bottom'
					}	           
	],
	viewConfig: {
		stripeRows: true,
		getRowClass: function(record, index) {
			//return 'invest_row';
		}
	},
	style : 'float:left;',
	store : store_investdtl,
	columns	: [
    	   { text : 'pf_id',			dataIndex: 'pf_id',				width: 140,		style:'text-align:center',		align:'center',	hidden:true,	sortable:false	},
			{ text : '성향',			dataIndex: 'invest_type',		width: 130,		style:'text-align:center',		align:'center',	sortable:false   },
			{ text : '금속',			dataIndex: 'metal_type',		width: 100,		style:'text-align:center',		align:'center',	summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	sortable:false   },
			{ text : '포지션 %',		dataIndex: 'target_per',		width: 100,		style:'text-align:center',		align:'right',	renderer: rendererColumnFormat,	summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	editor: { allowBlank : false },	sortable:false },
			{ text : '포지션 ￦',	dataIndex: 'TARGET_PRICE',		width: 158,		style:'text-align:center',		align:'right',	renderer: rendererColumnFormat,	summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	sortable:false	},
	],
	listeners : defaultListener
});

/* 투자성향 설정 */
var grid_achInvest = Ext.create('Ext.grid.Panel',{
	width	: midGridWidth,
	height : midGridHeight,
	border: 0,
	features: [{ ftype : 'summary' }],
	viewConfig: {
		stripeRows: true,
		getRowClass: function(record, index) {
			return 'invest_row';
		}
	},
	style : 'float:left;',
	store : store_achInvest,
	columns	: [
    	   { text : 'pf_id',			dataIndex: 'pf_id',				width: 140,		style:'text-align:center',		align:'center',	hidden:true,	sortable:false	},
			{ text : '성향',			dataIndex: 'invest_type',		width: 130,		style:'text-align:center',		align:'center',	sortable:false	},
			{ text : '목표 %',		dataIndex: 'ACH_PER',			width: 100,		style:'text-align:center',		align:'right',		summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	renderer: rendererColumnFormat,	sortable:false },
			{ text : '목표금액 ￦',	dataIndex: 'ACH_PRICE',			width: 130,		style:'text-align:center',		align:'right',		summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	renderer: Ext.util.Format.numberRenderer('0,000'),	sortable:false	},
	],
	listeners : defaultListener
});



/* 투자성향 설정 */
var grid_achInvestdtl = Ext.create('Ext.grid.Panel',{
	plugins: [Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1,errorSummary: false})],
	width	: midGridWidth,
	height : midGridHeight-4,
	border: 0,
	features: [
					{
						ftype : 'groupingsummary',
						groupHeaderTpl: '{name}',
						hideGroupedHeader: true,
            		enableGroupingMenu: true,
            		collapsible : false
					},{
						ftype: 'summary',
						dock: 'bottom'
					}	           
	],
	viewConfig: {
		stripeRows: true,
		getRowClass: function(record, index) {
			//return 'invest_row';
		}
	},
	style : 'float:left;',
	store : store_achInvestdtl,
	columns	: [
    	   { text : 'pf_id',			dataIndex: 'pf_id',				width: 140,		style:'text-align:center',		align:'center',	hidden:true,	sortable:false	},
			{ text : '성향',			dataIndex: 'invest_type',		width: 130,		style:'text-align:center',		align:'center',	sortable:false   },
			{ text : '금속',			dataIndex: 'metal_type',		width: 100,		style:'text-align:center',		align:'center',	summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	sortable:false   },
			{ text : '포지션 %',		dataIndex: 'ACH_PER',			width: 100,		style:'text-align:center',		align:'right',	renderer: rendererColumnFormat,	summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	editor: { allowBlank : false },	sortable:false },
			{ text : '포지션 ￦',	dataIndex: 'ACH_PRICE',			width: 158,		style:'text-align:center',		align:'right',	renderer: rendererColumnFormat,	summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	sortable:false	},
	],
	listeners : defaultListener
});

/** 하단 **********************************************************************************/

/* 하단 자금 평가 금액 */
var grid_estimate = Ext.create('Ext.grid.Panel',{
	plugins: [Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1,errorSummary: false})],
	title: { cls:'center',	text:'추가 매수 자금'},
	width	: botGridWidth,
	height : botGridHeight,
	border: 0,
	frame : true,
	autoScroll: false,
	features: statsFeatures,
	viewConfig: statsViewConfig,
	style : 'float:left;',
	bodyStyle: 'overflow-x:hidden;overflow-y:hidden;',
	store : store_estimate,
	columns	: [
    	   { text : '금속',					dataIndex: 'metal_type',				width: 100,		style:'text-align:center',		align:'center',	sortable:false	},
    	   { text : '중량',					dataIndex: 'TOTAL_GRAM',				width: 130,		style:'text-align:center',		align:'right',		summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	renderer: rendererColumnFormat,	sortable:false },
			{ text : '현재 평가 금액',		dataIndex: 'ESTIMATE_NOW',				width: 130,		style:'text-align:center',		align:'right',		summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	renderer: rendererColumnFormat,	sortable:false },
			{ text : '구매 평가 금액',		dataIndex: 'ESTIMATE_BUYED',			width: 150,		style:'text-align:center',		align:'right',		summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	renderer: rendererColumnFormat,	sortable:false },
			{ text : '손익 금액',			dataIndex: 'ESTIMATE_PROFIT',			width: 130,		style:'text-align:center',		align:'right',		summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	renderer: rendererColumnFormat,	sortable:false },
			{ text : '손익%',					dataIndex: 'ESTIMATE_PROFIT_PER',	width: 110,		style:'text-align:center',		align:'right',		summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	renderer: rendererColumnFormat,	sortable:false },
			{ text : '현재 금은시세(1돈)',dataIndex: 'flowprice_now',			width: 150,		style:'text-align:center',		align:'right',		renderer: rendererColumnFormat,	sortable:false },
			{ text : '구매 평균시세(1돈)',dataIndex: 'flowprice_buyed',			width: 150,		style:'text-align:center',		align:'right',		renderer: rendererColumnFormat,	sortable:false },
			{ text : '평단가 손익',			dataIndex: 'flowprice_profit',		width: 150,		style:'text-align:center',		align:'right',		renderer: rendererColumnFormat,	sortable:false },
			{ text : '평단가 손익(%)',		dataIndex: 'flowprice_profit_per',	width: 130,		style:'text-align:center',		align:'right',		renderer: rendererColumnFormat,	sortable:false }
	],
	listeners : statsDefaultListener
});





/************* ----------------	그리드 END -------------- ******************/