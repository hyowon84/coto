/************* ----------------  스토어 START -------------- ******************/

/*  */
Ext.define('Task', {
    extend: 'Ext.data.Model',
    idProperty: 'taskId',
    fields: [
		{name: 'projectId',			type: 'int'},
		{name: 'project',				type: 'string'},
		{name: 'taskId',				type: 'int'},
		{name: 'gpcode',				type: 'string'},
		{name: 'gpcode_name',		type: 'string'},
		{name: 'gpstats',				type: 'string'},
		{name: 'gpstats_name',	type: 'string'},
		{name: 'od_id',					type: 'string'},
		{name: 'gpcode_name',		type: 'string'},
		{name: 'it_id',					type: 'string'},
		{name: 'it_name',				type: 'string'},
		{name: 'it_org_price',	type: 'float'},
		{name: 'it_qty', 				type: 'int'},
		{name: 'total_price',		type: 'int'},
		{name: 'od_date',				type: 'date'}
    ]
});

/*예 아니오*/
Ext.define('Ext.store.item.yesno',{
	extend: 'Ext.data.Store',
	fields:['name','value'],
	data:[
		{	name:'-선택-',	value:''	},
		{	name:'Y',			value:'1'	},
		{	name:'N',			value:'0'	}
	]
});

/*예 아니오*/
Ext.define('Ext.store.item.yn',{
	extend: 'Ext.data.Store',
	fields:['name','value'],
	data:[
		{	name:'-선택-',	value:''	},
		{	name:'Y',			value:'Y'	},
		{	name:'N',			value:'N'	}
	]
});

/*가격반영유형*/
Ext.define('Ext.store.item.pricetype',{
	extend: 'Ext.data.Store',
	fields:['name','value'],
	data:[
			{	name:'-유형-',		value:''	},
			{	name:'원화(￦)',	value:'W'	},
			{	name:'달러($)',	value:'N'	},
			{	name:'스팟시세',	value:'Y'	}
	]
});

/*스팟시세일 경우 계산공식*/
Ext.define('Ext.store.item.spottype',{
	extend: 'Ext.data.Store',
	fields:['name','value'],
	data:[
			{	name:'-스팟옵션-',	value:''	},
			{	name:'1oz이상($)',	value:'U$'},
			{	name:'1oz미만($)',	value:'D$'},
			{	name:'%',					value:'%'	},
			{	name:'￦',					value:'￦'	}
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



/* 카테고리 */
/*
var store_category = Ext.create('Ext.data.Store',{
		autoLoad : true,
		remoteSort: true,
		fields:['ca_name','ca_id'],
		fields : [
					{	name : 'ca_id',			type: 'string'},
					{	name : 'ca_name',			type: 'string'},
					{	name : 'ca_order',		type: 'string'}
		],
//		store: Ext.create('Ext.data.Store',{
//					fields:['name','value'],
//					data:[
//							{	name:'금',		value:'GL'	},
//							{	name:'은',		value:'SL'	}
//					]
//				})
//				
//		sorters:[
//			{
//				property:'reg_date',
//				direction:'DESC'
//			}
//		],
		proxy : {
			type : 'ajax',
			extraParams : {
				
			},
			api : {
				read : '/adm/extjs/product/json/category.php?mode=all'
			},
			reader : {
				rootProperty : 'data',
				totalProperty : 'total'
			},
		}
});
*/


/* 공구목록 */
var store_gplist = Ext.create('Ext.data.Store',{
		pageSize : 50,
		autoLoad : true,
		remoteSort: true,
		fields : [
					{	name : 'gpcode',			type: 'string'},
					{	name : 'gpcode_name',	type: 'string'},
					{	name : 'stats',			type: 'string'},
					{	name : 'start_date',		type: 'date'},
					{	name : 'end_date',		type: 'date'},
					{	name : 'reg_date',		type: 'date'}
		],
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
				read : '/adm/extjs/product/json/product.php?mode=gplist'
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


/*  */
Ext.define('model_itemlist', {
    extend: 'Ext.data.Model',
	fields : [	
					{name: 'gp_id',							type: 'string'},
					{name: 'location',					type: 'string'},
					{name: 'gp_name',						type: 'string'},
					{name: 'gp_price',					type: 'int'},
					{name: 'gp_usdprice',				type: 'float'},
					{name: 'gp_realprice',			type: 'float'},
					{name: 'gp_price_org',			type: 'float'},
					{name: 'jaego',							type: 'int'},
					{name: 'gp_price_type',			type: 'string'},
					{name: 'gp_spotprice',			type: 'float'},
					{name: 'gp_spotprice_type',	type: 'string'},
					{name: 'gp_metal_type',			type: 'string'},
					{name: 'gp_metal_don',			type: 'float'},
					{name: 'gp_use',						type: 'string'},
					{name: 'gp_order',					type: 'int'},
					{name: 'iv_qty',						type: 'int'},
					{name: 'CO_SUM',						type: 'int'},
					{name: 'real_jaego',				type: 'int'},
					{name: 'OPT_CNT',						type: 'int'},
					{name: 'gp_update_time',		type: 'date'}
    ]
});

/* 주문정보 목록  */
var store_itemlist = Ext.create('Ext.data.Store',{
	pageSize : 100,
	model : 'model_itemlist',
	remoteSort: true,
	autoLoad : false,
	autoSync : true,
	sorters:[
		{
			property:'gp_update_time',		
			direction:'DESC'
		}
	],
	proxy : {
		type : 'ajax',
		extraParams : {
		},
		api : {
			read : '/adm/extjs/product/json/product.php?mode=itemlist',
			update	: '/adm/extjs/product/crud/product.update.php'
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


/* 경매상품목록  */
var store_aucPrdList = Ext.create('Ext.data.Store',{
	pageSize : 100,
	model : 'model_itemlist',
	remoteSort: true,
	autoLoad : true,
	autoSync : true,
	sorters:[
		{
			property:'ac_code',
			direction:'DESC'
		}
	],
	proxy : {
		type : 'ajax',
		extraParams : {
		},
		api : {
			read : '/adm/extjs/product/json/auction.php?mode=auclist',
			update : '/adm/extjs/product/crud/auction.update.php'
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


/* 경매입찰목록 */
var store_aucBidList = Ext.create('Ext.data.Store',{
	pageSize : 50,
	autoLoad : false,
	remoteSort: true,
	fields : [
		{	name : 'ac_code',					type: 'string'},
		{	name : 'it_id',						type: 'string'},
		{	name : 'mb_id',						type: 'string'},
		{	name : 'mb_nick',					type: 'string'},
		{	name : 'mb_name',					type: 'string'},
		{	name : 'mb_hp',						type: 'string'},
		{	name : 'bid_qty',					type: 'int'},
		{	name : 'bid_price',				type: 'int'},
		{	name : 'bid_last_price',	type: 'int'},
		{	name : 'bid_stats',				type: 'string'},
		{	name : 'bid_date',				type: 'string'}
	],
	sorters:[
		{
			property:'bid_last_price',
			direction:'DESC'
		}
	],
	proxy : {
		type : 'ajax',
		extraParams : {

		},
		api : {
			read : '/adm/extjs/product/json/auction.php?mode=bidlist'
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





Ext.tip.QuickTipManager.init();

var store_window_product = Ext.create('Ext.data.Store',{
	model	:	'Task',
	sorters:	{	property:'od_date',		direction:'DESC'},
	groupField: 'project'
});

/* 통계정보에서 선택된 품목들 데이터저장용 스토어 */
var storeTempInvoice = Ext.create('Ext.data.Store',{
	id : 'storeTempInvoice',
	fields: [
		{name: 'projectId',		type: 'int'},
		{name: 'project',		type: 'string'},		
		{name: 'taskId',		type: 'int'},
		{name: 'gpcode',		type: 'string'},
		{name: 'gpcode_name',	type: 'string'},
		{name: 'gpstats',		type: 'string'},
		{name: 'gpstats_name',	type: 'string'},
		{name: 'od_id',			type: 'string'},
		{name: 'gpcode_name',	type: 'string'},		
		{name: 'it_id',			type: 'string'},
		{name: 'it_name',		type: 'string'},
		{name: 'it_org_price',	type: 'float'},
		{name: 'it_qty', 		type: 'int'},
		{name: 'total_price',	type: 'int'},
		{name: 'od_date',		type: 'date'}
    ]
});

