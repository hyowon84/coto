/************* ----------------  스토어 START -------------- ******************/

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

Ext.define('Ext.store.item.odstats',{
	extend: 'Ext.data.Store',
	fields:['name','value'],
	data:[
		{	name:'선배송예정,미결제',	value:'15'	},
		{	name:'선배송완료,미결제',	value:'17'	},
		{	name:'통합배송요청',	value:'22'	},
		{	name:'포장완료',	value:'23'	},
		{	name:'배송대기',	value:'25'	},
		{	name:'픽업대기',	value:'35'	},
		{	name:'직배대기',	value:'30'	},
		{	name:'배송완료',	value:'40'	},
		{	name:'픽업완료',	value:'60'	},
		{	name:'직배완료',	value:'50'	}
	]
});


/* 공구목록 */
var store_mblist = Ext.create('Ext.data.Store',{
		pageSize : 50,
		remoteSort: true,
		fields : [
					{	name : 'mb_nick'	},
					{	name : 'mb_name'	},
					{	name : 'hphone'		},
					{	name : 'SUM_QTY',					sortDir: 'DESC', sortType: 'asInt', mapping: 'SUM_QTY',					type: 'int'},
					{	name : 'SUM_TOTAL',				sortDir: 'DESC', sortType: 'asInt', mapping: 'SUM_TOTAL',				type: 'int'},
					{	name : 'S40_SUM_QTY',			sortDir: 'DESC', sortType: 'asInt', mapping: 'S40_SUM_QTY',			type: 'int'},
					{	name : 'S40_SUM_TOTAL',		sortDir: 'DESC', sortType: 'asInt', mapping: 'S40_SUM_TOTAL',		type: 'int'},
					{	name : 'NS40_SUM_QTY',		sortDir: 'DESC', sortType: 'asInt', mapping: 'NS40_SUM_QTY',		type: 'int'},
					{	name : 'NS40_SUM_TOTAL',	sortDir: 'DESC', sortType: 'asInt', mapping: 'NS40_SUM_TOTAL',	type: 'int'}
		],
		sorters:[
			{
				property:'QCK_SUM_QTY',
				direction:'DESC'
			}
		],
		proxy : {
			type : 'ajax',
			extraParams : {
				
			},
			api : {
				read : '/adm/extjs/baesong/json/baesong.php?mode=mblist'
			},
			reader : {
				rootProperty : 'data',
				totalProperty : 'total'
			}
			/*,
			writer : {
				type : 'json',
				writeAllFields : true,
				encode : true,
				rootProperty : 'data'
			}*/
		}
		//data : [['1-1','1-2','1-3'],['2-1','2-2','2-3'],['3-1','3-2','3-3']]
});

/* 배송완료목록  */
var store_shiped_list = Ext.create('Ext.data.Store',{
	pageSize : 100,
	model	:	'model_orderlist',
	//remoteSort: true,
	//remoteFilter: true,
	autoLoad : false,
	remoteSort: true,
	sorters:[
		{
			property:'od_date',		
			direction:'DESC'
		}
	],
	proxy : {
		type : 'ajax',
		extraParams : {
		},
		api : {
			read : '/adm/extjs/baesong/json/baesong.php?mode=shipedlist'
		},
		reader : {
			rootProperty : 'data',
			totalProperty : 'total'
		}
		/*,
		writer : {
			type : 'json',
			writeAllFields : true,
			encode : true,
			rootProperty : 'data'
		}*/
	}
});



/* 주문정보 목록  */
var store_orderlist = Ext.create('Ext.data.Store',{
	pageSize : 100,
	model	:	'model_orderlist',
	//remoteSort: true,
	//remoteFilter: true,
	autoSync : true,
	autoLoad : false,
	remoteSort: true,
	sorters:[
		{
			property:'IV_STATS_NAME',		
			direction:'DESC'
		},
		{
			property:'od_date',
			direction:'DESC'
		}
	],
	proxy : {
		type : 'ajax',
		extraParams : {
		},
		api : {
			read : '/adm/extjs/baesong/json/baesong.php?mode=orderlist',
			update : '/adm/extjs/baesong/crud/baesong.update.php'
		},
		reader : {
			rootProperty : 'data',
			totalProperty : 'total'
		}
		,
		writer : {
			type : 'json',
			writeAllFields : true,
			encode : true,
			rootProperty : 'data'
		}
	}
});

Ext.tip.QuickTipManager.init();

var store_window_baesong = Ext.create('Ext.data.Store',{
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
		{name: 'od_date',			type: 'string'}
    ]
});

