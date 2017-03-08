/************* ----------------  스토어 START -------------- ******************/

/*  */
Ext.define('Task', {
    extend: 'Ext.data.Model',
    idProperty: 'taskId',
    fields: [
		{name: 'projectId',		type: 'int'},
		{name: 'project',			type: 'string'},		
		{name: 'taskId',			type: 'int'},
		{name: 'gpcode',			type: 'string'},
		{name: 'gpcode_name',	type: 'string'},
		{name: 'gpstats',			type: 'string'},
		{name: 'gpstats_name',	type: 'string'},
		{name: 'od_id',			type: 'string'},
		{name: 'gpcode_name',	type: 'string'},
		{name: 'it_id',			type: 'string'},
		{name: 'it_name',			type: 'string'},
		{name: 'it_org_price',	type: 'float'},
		{name: 'it_qty', 			type: 'int'},
		{name: 'total_price',	type: 'int'},
		{name: 'od_date',			type: 'date'}
    ]
});


/*금속유형*/
Ext.define('Ext.store.item.metaltype',{
	extend: 'Ext.data.Store',
	fields:['name','value'],
	data:[
			{	name:'Gold',		value:'Gold'	},
			{	name:'Silver',		value:'Silver'	}
	]
});

/*투자유형*/
Ext.define('Ext.store.item.investtype',{
	extend: 'Ext.data.Store',
	fields:['name','value'],
	data:[
			{	name:'-선택-',			value:''		},
			{	name:'투자',			value:'투자'	},
			{	name:'수집',			value:'수집'	},
			{	name:'대비,생존',		value:'대비,생존'	},
			{	name:'증여',			value:'증여'	},
			{	name:'기타',			value:'기타'	}
			/*
			{	name:'-선택-',			value:''		},
			{	name:'투자',			value:'I01'	},
			{	name:'수집',			value:'I02'	},
			{	name:'대비,생존',		value:'I03'	},
			{	name:'증여',			value:'I04'	},
			{	name:'기타',			value:'I05'	}*/
	]
});


/*가격반영유형*/
Ext.define('Ext.store.item.pricetype',{
	extend: 'Ext.data.Store',
	fields:['name','value'],
	data:[
			{	name:'-선택-',		value:''		},
			{	name:'원화(￦)',	value:'W'	},
			{	name:'달러($)',	value:'N'	},
			{	name:'스팟시세',	value:'Y'	}
	]
});





Ext.define('store.dealers', {
	extend: 'Ext.data.ArrayStore',
	alias: 'store.dealers',
	model: 'model.dealers',
	storeId: 'dealers',
	data: [
		[0, 'AP', 'APMEX'],
		[1, 'GV', 'GAINSVILLE'],
		[2, 'MC', 'MCM'],
		[3, 'PA', 'PARADISE'],
		[4, 'BX', 'BULLION EXCHANGE'],
		[5, 'OD', 'OTHER DEALER'],
		[6, 'CT', '코인스투데이'],
	]
});


/* VIP회원 목록 */
var store_vipMbList = Ext.create('Ext.data.Store',{
		pageSize : 50,
		autoLoad : true,
		remoteSort: true,
		autoSync : true,
		model : 'model.vipMbInfo',
		sorters:[
			{
				property:'reg_date',
				direction:'DESC'
			}
		],
		proxy : {
			type : 'ajax',
			extraParams : {
				
			},
			api : {
				read : '/adm/extjs/portfolio/json/portfolio.php?mode=vipMbList',
				create : '/adm/extjs/portfolio/crud/portfolio_mb.create.php',
				update : '/adm/extjs/portfolio/crud/portfolio_mb.update.php'
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


/**/
var store_pfDataSummary = Ext.create('Ext.data.Store',{
	pageSize : 50,
	model : 'model.pfDataSummary',
	groupField: 'metal_type_nm',
	remoteSort: true,
	autoLoad : false,
	proxy : {
		type : 'ajax',
		extraParams : {
		},
		api : {
			read	: '/adm/extjs/portfolio/json/portfolio_dataSummary.php?mode=summary'
		},
		reader : {
			rootProperty : 'data',
			totalProperty : 'total'
		}
	}
});




/* 포트폴리오 자료입력  */
var store_pfItemList = Ext.create('Ext.data.Store',{
	pageSize : 100,
	model : 'model.pfItemList',
	remoteSort: true,
	autoLoad : false,
	autoSync : false,
	sorters:[
		{
			property:'PD.reg_date',		
			direction:'DESC'
		}
	],
	proxy : {
		type : 'ajax',
		extraParams : {},
		reader : {
			rootProperty : 'data',
			totalProperty : 'total'
		},
		api : {
			read		: '/adm/extjs/portfolio/json/portfolio.php?mode=itemlist',
			create	: '/adm/extjs/portfolio/crud/portfolio_item.create.php',
			update	: '/adm/extjs/portfolio/crud/portfolio_item.update.php',
			destroy	: '/adm/extjs/portfolio/crud/portfolio_item.delete.php'
		},
		actionMethods : {
			destroy : 'POST',
			read : 'GET',
			create : 'POST',
			update : 'POST'
		},
		writer : {
			type : 'json',
			writeAllFields : true,
			encode : true,
			rootProperty : 'data'
		}
	}
});



Ext.tip.QuickTipManager.init();

var store_window_portfolio = Ext.create('Ext.data.Store',{
	model	:	'Task',
	sorters:	{	property:'od_date',		direction:'DESC'},
	groupField: 'project'
});

/* 통계정보에서 선택된 품목들 데이터저장용 스토어 */
var storeTempInvoice = Ext.create('Ext.data.Store',{
	id : 'storeTempInvoice',
	fields: [
		{name: 'projectId',		type: 'int'},
		{name: 'project',			type: 'string'},		
		{name: 'taskId',			type: 'int'},
		{name: 'gpcode',			type: 'string'},
		{name: 'gpcode_name',	type: 'string'},
		{name: 'gpstats',			type: 'string'},
		{name: 'gpstats_name',	type: 'string'},
		{name: 'od_id',			type: 'string'},
		{name: 'gpcode_name',	type: 'string'},		
		{name: 'it_id',			type: 'string'},
		{name: 'it_name',			type: 'string'},
		{name: 'it_org_price',	type: 'float'},
		{name: 'it_qty', 			type: 'int'},
		{name: 'total_price',	type: 'int'},
		{name: 'od_date',			type: 'date'}
    ]
});


/* 탭:포트폴리오 그리드의 스토어 */



var store_expectInvest = Ext.create('Ext.data.Store',{
	autoLoad : false,
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
			read	: '/adm/extjs/portfolio/json/invest.php?mode=expectInvest'
		},
		reader : {
			rootProperty : 'data',
			totalProperty : 'total'
		}
	}
});

var store_beginFundSet = Ext.create('Ext.data.Store',{
	autoLoad : false,
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
			read	: '/adm/extjs/portfolio/json/invest.php?mode=beginFundSet'
		},
		reader : {
			rootProperty : 'data',
			totalProperty : 'total'
		}
	}
});

var store_beginFundBuy = Ext.create('Ext.data.Store',{
	autoLoad : false,
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
			read	: '/adm/extjs/portfolio/json/invest.php?mode=beginFundBuy',
			update : '/adm/extjs/portfolio/crud/beginfund.update.php'
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
	autoLoad : false,
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
			read	: '/adm/extjs/portfolio/json/invest.php?mode=invest',
			update : '/adm/extjs/portfolio/crud/invest.update.php'
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
		autoLoad : false,
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
				read	: '/adm/extjs/portfolio/json/invest.php?mode=investdtl',
				update : '/adm/extjs/portfolio/crud/investdtl.update.php'
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
	autoLoad : false,
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
			read	: '/adm/extjs/portfolio/json/invest.php?mode=invest',
			update : '/adm/extjs/portfolio/crud/invest.update.php'
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
		autoLoad : false,
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
				read	: '/adm/extjs/portfolio/json/invest.php?mode=achinvestdtl',
				update : '/adm/extjs/portfolio/crud/investdtl.update.php'
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
	autoLoad : false,
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
			read	: '/adm/extjs/portfolio/json/invest.php?mode=estimate'
		},
		reader : {
			rootProperty : 'data',
			totalProperty : 'total'
		}
	}
});


/*탭:차트*/
var store_chartInvest = Ext.create('Ext.data.Store',{
	fields: ['colName', 'data1', 'data2'],
	autoLoad : false,
	proxy : {
		type : 'ajax',
		extraParams : {
		},
		api : {
			read	: '/adm/extjs/portfolio/json/chart.php?mode=invest'
		},
		reader : {
			rootProperty : 'data',
			totalProperty : 'total'
		}
	}
});

/*금,은 비중*/
var store_chartMetalPer = Ext.create('Ext.data.Store',{
	fields: ['colName', 'data1', 'data2'],
	autoLoad : false,
	proxy : {
		type : 'ajax',
		extraParams : {
		},
		api : {
			read	: '/adm/extjs/portfolio/json/chart.php?mode=MetalPer'
		},
		reader : {
			rootProperty : 'data',
			totalProperty : 'total'
		}
	}
});

/*금 투자성향*/
var store_chartGoldPer = Ext.create('Ext.data.Store',{
	fields: ['colName', 'data1', 'data2'],
	autoLoad : false,
	proxy : {
		type : 'ajax',
		extraParams : {
		},
		api : {
			read	: '/adm/extjs/portfolio/json/chart.php?mode=MetalFundPer&metal_type=Gold'
		},
		reader : {
			rootProperty : 'data',
			totalProperty : 'total'
		}
	}
});


/*은 투자성향*/
var store_chartSilverPer = Ext.create('Ext.data.Store',{
	fields: ['colName', 'data1', 'data2'],
	autoLoad : false,
	proxy : {
		type : 'ajax',
		extraParams : {
		},
		api : {
			read	: '/adm/extjs/portfolio/json/chart.php?mode=MetalFundPer&metal_type=Silver'
		},
		reader : {
			rootProperty : 'data',
			totalProperty : 'total'
		}
	}
});

/*투자성향별 달성금액 */
var store_MetalInvestPrice = Ext.create('Ext.data.Store',{
	fields: ['colName', 'data1', 'data2'],
	autoLoad : false,
	proxy : {
		type : 'ajax',
		extraParams : {
		},
		api : {
			read	: '/adm/extjs/portfolio/json/chart.php?mode=MetalInvestPrice'
		},
		reader : {
			rootProperty : 'data',
			totalProperty : 'total'
		}
	}
});

/*투자성향별 달성백분율 */
var store_MetalInvestPer = Ext.create('Ext.data.Store',{
	fields: ['colName', 'data1', 'data2'],
	autoLoad : false,
	proxy : {
		type : 'ajax',
		extraParams : {
		},
		api : {
			read	: '/adm/extjs/portfolio/json/chart.php?mode=MetalInvestPer'
		},
		reader : {
			rootProperty : 'data',
			totalProperty : 'total'
		}
	}
});


/*투자성향 목표율vs달성률 */
var store_TargetAchieve = Ext.create('Ext.data.Store',{
	fields: ['colName', 'data1', 'data2'],
	autoLoad : false,
	proxy : {
		type : 'ajax',
		extraParams : {
		},
		api : {
			read	: '/adm/extjs/portfolio/json/chart.php?mode=TargetAchieve'
		},
		reader : {
			rootProperty : 'data',
			totalProperty : 'total'
		}
	}
});

