/************* ----------------  스토어 START -------------- ******************/

/*공동구매 상태값*/
Ext.define('Ext.store.item.gpstats',{
	extend: 'Ext.data.Store',
	fields:['name','value'],
	data:[
		{	name:'공구접수',			value:'00'	},
		{	name:'주문마감',			value:'05'	},
		{	name:'미발주포함',		value:'07'	},
		{	name:'발주신청',			value:'10'	},
		{	name:'송금완료',			value:'20'	},
		{	name:'통관완료',			value:'30'	},
		{	name:'국내도착',			value:'40'	},
		{	name:'공구종료',			value:'99'	},
	]
});

Ext.define('Ext.combobox.item.gpstats', {
	extend: 'Ext.form.ComboBox',
	xtype: 'combobox',
	editable: false,
	displayField: 'name',
	valueField: 'value',
	name: 'gpstats',
	store: Ext.create('Ext.store.item.gpstats')
});

/*발주서 상태값*/
Ext.define('Ext.store.item.ivstats',{
	extend: 'Ext.data.Store',
	fields:['name','value'],
	data:[
		{	name:'재고세팅',			value:'00'	},
		{	name:'발주완료',			value:'05'	},
		{	name:'송금완료',			value:'10'	},
		{	name:'통관완료',			value:'20'	},
		{	name:'국내도착',			value:'30'	},
		{	name:'입고완료',			value:'40'	},
		{	name:'삭제(숨김)',		value:'99'	}
	]
});

Ext.define('Ext.combobox.item.ivstats', {
	extend: 'Ext.form.ComboBox',
	xtype: 'combobox',
	editable: false,
	displayField: 'name',
	valueField: 'value',
	name: 'ivstats',
	store: Ext.create('Ext.store.item.ivstats')
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
		[5, 'OD', 'OTHER DEALER']
	]
});

Ext.define('store.moneytype', {
	extend: 'Ext.data.ArrayStore',
	alias: 'store.moneytype',
	model: 'model.combo',
	storeId: 'moneytype',
	data: [//보이는거, 선택후값
		[0, 'USD', 'USD'],
		[1, 'CNY', 'CNY'],
		[2, 'EUR', 'EUR'],
		[3, 'HKD', 'HKD'],
		[4, 'AUD', 'AUD']
	]
});
//fields : [ 'number','title','value']
Ext.define('store.wrtype', {
	extend: 'Ext.data.ArrayStore',
	alias: 'store.wrtype',
	model: 'model.combo',
	storeId: 'wrtype',
	data: [
		[0, '00', '은행-코인즈투데이'],
		[1, '01', '은행-투데이(주)'],
		[2, '10', '페이팔'],
		[3, '20', '마운틴']
	]
});

var store_ivstats = Ext.create('Ext.data.SimpleStore', {
	fields: [ "name", "value" ],
	data: [
	    [ "미도착", "00" ],
	    [ "도착", "20" ]
	]
});

/******************************* 콤보박스 END *************************************/



/******************************* 그리드용 START *************************************/

//좌측 상단 송금예정 발주서 목록
Ext.define('store.invoice_list',{
	extend: 'Ext.data.Store',
	fields:['name','value'],
	pageSize : 300,
	//model	:	'model_stockManage',
	fields : [
		'gpcode',
		'iv_id',
		'iv_dealer',
		'iv_order_no',
		'iv_receipt_link',
		'iv_date',
		'od_exch_rate',
		'money_type',
		'iv_tax',
		'iv_shippingfee',
		'iv_memo',
		'admin_id',
		'admin_name',
		'reg_date'
	],
	remoteSort: true,
	autoLoad : false,
	autoSync : true,
	//remoteFilter: true,
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
			read		: '/adm/extjs/stock/crud/stock.php?mode=invoiceTodoWire',
			update	: '/adm/extjs/stock/crud/stockinfo_update.php',
			destroy	: '/adm/extjs/stock/crud/invoice_info.delete.php',
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


//좌측 하단 발주서 관련 품목 목록
Ext.define('store.invoice_dtl',{
	//id : 'store_invoiceEndWire',
	extend: 'Ext.data.Store',
	pageSize : 300,
	fields : ['number',
		'iv_id',
		'gpcode',
		'gpcode_name',
		'iv_dealer',
		'iv_order_no',
		'iv_it_img',
		'iv_it_id',
		'iv_it_name',
		'GP_ORDER_QTY',
		'iv_qty',
		'iv_dealer_worldprice',
		'iv_dealer_price',
		'total_price',
		'money_type',
		'iv_receipt_link',
		'ip_qty',
		'reg_date'],
	remoteSort: true,
	autoLoad : false,
	autoSync : true,
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
			read : '/adm/extjs/stock/crud/stock.php?mode=invoice_item',
			update	: '/adm/extjs/stock/crud/stockitem_update.php'
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

//상단 검색 발주서 통계 목록
var store_navi_invoice = Ext.create('store.invoice_list');
store_navi_invoice.autoLoad = false;
store_navi_invoice.proxy.api.read = '/adm/extjs/stock/crud/stock.php?mode=naviFindInvoice';

//상단 발주서와 관련된 발주품목들
var store_navi_invoice_dtl = Ext.create('store.invoice_dtl',{
	//id : 'store_invoiceEndWire',
	extend: 'Ext.data.Store',
	groupField : 'iv_it_id',
	pageSize : 300,
	fields : ['number',
		'iv_id',
		'gpcode',
		'gpcode_name',
		'iv_dealer',
		'iv_order_no',
		'iv_it_img',
		'iv_it_id',
		'iv_it_name',
		{name: 'GP_ORDER_QTY',	type: 'int'},
		{name: 'GPT_QTY',				type: 'int'},
		{name: 'iv_qty',				type: 'int'},
		{name: 'ip_qty',				type: 'int'},
		'iv_dealer_worldprice',
		'iv_dealer_price',
		'total_price',
		'money_type',
		'iv_receipt_link',
		'reg_date'],
	remoteSort: true,
	autoLoad : false,
	autoSync : true,
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
			read : '/adm/extjs/stock/crud/stock.php?mode=invoice_item',
			update	: '/adm/extjs/stock/crud/stockitem_update.php'
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
//store_navi_invoice_dtl.groupField = 'iv_it_id';


/************************************************************************************/


/* 공구목록 */
var store_gpinfo = Ext.create('Ext.data.Store',{
	pageSize : 50,
	remoteFilter:true,
	remoteSort:true,
	autoSync : true,
	fields : [
		'gpcode_name',
		'gpcode',
		{name: 'SUM_PAY',			sortDir: 'DESC', sortType: 'asInt', mapping: 'SUM_PAY',			type: 'int'},
		{name: 'SUM_QTY',			sortDir: 'DESC', sortType: 'asInt', mapping: 'SUM_QTY',			type: 'int'},
		{name: 'SUM_IV_QTY',	sortDir: 'DESC', sortType: 'asInt', mapping: 'SUM_IV_QTY',	type: 'int'},
		{name: 'NEED_IV_QTY',	sortDir: 'DESC', sortType: 'asInt', mapping: 'NEED_IV_QTY',	type: 'int'},
		{name: 'ITC_CNT',			sortDir: 'DESC', sortType: 'asInt', mapping: 'ITC_CNT',			type: 'int'},
		{name: 'IVC_CNT',			sortDir: 'DESC', sortType: 'asInt', mapping: 'IVC_CNT',			type: 'int'},
		{name: 'stats',				type: 'string'},
		{name: 'memo',				type: 'string'},
	],
	sorters:[
		{
			property:'GI.reg_date',
			direction:'DESC'
		}
	],
	proxy : {
		type : 'ajax',
		extraParams : {
		},
		api : {
			read : '/adm/extjs/stock/crud/stock.php?mode=gpinfo',
			update : '/adm/extjs/stock/crud/gpinfo.update.php?mode=grid'
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

/* 공구목록 */
var store_memo_gpinfo = Ext.create('Ext.data.Store',{
	autoLoad : false,
	autoSync : true,
	fields : [
		{ name : 'gpcode',				type : 'string'},
		{ name : 'gpcode_name',		type : 'string'},
		{ name : 'invoice_memo',	type : 'string'},
		{ name : 'memo',					type : 'string'},
		{ name : 'reg_date',			type : 'date'	 }
	],
	sorters:[
		{
			property:'reg_date',
			direction:'DESC'
		}
	],
	proxy : {
		type : 'ajax',	//extraParams : {},
		api : {
			read : '/adm/extjs/stock/crud/stock.php?mode=gpinfo'
		},
		reader : {
			rootProperty: 'data',
			totalProperty: 'total'
		}
	}
});



/* 통계  */
var store_orderitems = Ext.create('Ext.data.Store',{
	id : 'store_orderitems',
	pageSize : 300,
	autoLoad : false,
	autoSync : true,
	model	:	'model_stockManage',
	remoteSort: true,
	sorters:[
		{
			property:'reg_date',		
			direction:'DESC'
		}
	],
	proxy : {
		type : 'ajax',
		api : {
			read : '/adm/extjs/stock/crud/stock.php?mode=orderitems',
			update : '/adm/extjs/stock/crud/product.update.php'
		},
		reader : {
			rootProperty : 'data',
			totalProperty : 'total'
		},
		writer: {
			type: 'json',
			writeAllFields: true,
			encode: true,
			rootProperty: 'data'
		}
	}
});


/*************************** 송금 탭 START ***************************/


//좌측 상단 송금예정 발주서 목록
var store_invoiceTodoWire = Ext.create('store.invoice_list');

//좌측 하단 송금완료내역 관련 발주서 목록
var store_invoiceEndWire = Ext.create('Ext.data.Store', {
	id: 'store_invoiceEndWire',
	pageSize: 100,
	model: 'model.invoiceEndWire',
	groupField: 'Group',
	remoteSort: true,
	autoLoad: false,
	autoSync: true,
	sorters: [
		{
			property: 'wr_id',
			direction: 'DESC'
		}
	],
	proxy: {
		type: 'ajax',
		extraParams: {},
		api: {
			read: '/adm/extjs/stock/crud/stock.php?mode=invoiceEndWire',
			update: '/adm/extjs/stock/crud/stockinfo_update.php'
		},
		reader: {
			rootProperty: 'data',
			totalProperty: 'total'
		},
		writer: {
			type: 'json',
			writeAllFields: true,
			encode: true,
			rootProperty: 'data'
		}
	}
});


var store_wire_gpinfo = Ext.create('Ext.data.Store',{
	pageSize : 100,
	remoteFilter:true,
	remoteSort:true,
	fields : [	'gpcode_name',
		'gpcode',
		{name: 'SUM_PAY',			sortDir: 'DESC', sortType: 'asInt', mapping: 'SUM_PAY',			type: 'int'},
		{name: 'SUM_QTY',			sortDir: 'DESC', sortType: 'asInt', mapping: 'SUM_QTY',			type: 'int'},
		{name: 'SUM_IV_QTY',	sortDir: 'DESC', sortType: 'asInt', mapping: 'SUM_IV_QTY',	type: 'int'},
		{name: 'NEED_IV_QTY',	sortDir: 'DESC', sortType: 'asInt', mapping: 'NEED_IV_QTY',	type: 'int'},
		{name: 'ITC_CNT',			sortDir: 'DESC', sortType: 'asInt', mapping: 'ITC_CNT',			type: 'int'},
		{name: 'IVC_CNT',			sortDir: 'DESC', sortType: 'asInt', mapping: 'IVC_CNT',			type: 'int'}
	],
	sorters:[
		{
			property:'GI.reg_date',
			direction:'DESC'
		}
	],
	proxy : {
		type : 'ajax',
		extraParams : {
			//param1: Ext.getCmp("grid_object").getSelectionModel().getSelection()
			/*,
			 param2: 'test2'
			 */
		},
		api : {
			read : '/adm/extjs/stock/crud/stock.php?mode=gpinfo'
		},
		reader : {
			rootProperty : 'data',
			totalProperty : 'total'
		}
	}
});



//발주건 발주품목들
var store_wire_dtl = Ext.create('Ext.data.Store',{
	id : 'store_wire_dtl',
	pageSize : 300,
	fields : ['number',
						'iv_id',
						'gpcode',
						'gpcode_name',
						'iv_dealer',
						'iv_order_no',
						'iv_it_img',
						'iv_it_id',
						'iv_it_name',
						'GP_ORDER_QTY',
						'iv_qty',
						'iv_dealer_worldprice',
						'iv_dealer_price',
						'total_price',
						'money_type',
						'iv_receipt_link',
						'ip_qty',
						'SUM_QTY',
						'reg_date'
	],
	remoteSort: true,
	autoLoad : false,
	autoSync : true,
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
			read : '/adm/extjs/stock/crud/stock.php?mode=invoice_item',
			update	: '/adm/extjs/stock/crud/stockitem_update.php'
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

/*************************** 송금 탭 END ***************************/

/*************************** 통관 탭 START *************************/


//통관예정 목록
var store_todoClearance = Ext.create('Ext.data.Store',{
	id : 'store_todoClearance',
	pageSize : 100,
	model : 'model.invoiceEndWire',
	groupField: 'Group',
	remoteSort: true,
	autoLoad : false,
	autoSync : true,
	sorters:[
		{
			property:'wr_id',
			direction:'DESC'
		}
	],
	proxy : {
		type : 'ajax',
		extraParams : {

		},
		api : {
			read : '/adm/extjs/stock/crud/stock.php?mode=invoiceTodoClearance',
			update	: '/adm/extjs/stock/crud/stockinfo_update.php'
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


//통관완료 목록
var store_endClearance = Ext.create('Ext.data.Store',{
	model	:	'model_endClearance',
	id : 'store_endClearance',
	pageSize : 100,	
	remoteSort: true,
	autoLoad : true,
	autoSync : true,
	//remoteFilter: true,
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
			read : '/adm/extjs/stock/crud/stock.php?mode=invoiceEndClearance',
			update	: '/adm/extjs/stock/crud/clearance.update.php'
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

var store_clearance_gpinfo = Ext.create('Ext.data.Store',{
	pageSize : 100,
	remoteFilter:true,
	remoteSort:true,
	fields : [	'gpcode_name',
		'gpcode',
		{name: 'SUM_PAY',			sortDir: 'DESC', sortType: 'asInt', mapping: 'SUM_PAY',			type: 'int'},
		{name: 'SUM_QTY',			sortDir: 'DESC', sortType: 'asInt', mapping: 'SUM_QTY',			type: 'int'},
		{name: 'SUM_IV_QTY',	sortDir: 'DESC', sortType: 'asInt', mapping: 'SUM_IV_QTY',	type: 'int'},
		{name: 'NEED_IV_QTY',	sortDir: 'DESC', sortType: 'asInt', mapping: 'NEED_IV_QTY',	type: 'int'},
		{name: 'ITC_CNT',			sortDir: 'DESC', sortType: 'asInt', mapping: 'ITC_CNT',			type: 'int'},
		{name: 'IVC_CNT',			sortDir: 'DESC', sortType: 'asInt', mapping: 'IVC_CNT',			type: 'int'}
	],
	sorters:[
		{
			property:'GI.reg_date',
			direction:'DESC'
		}
	],
	proxy : {
		type : 'ajax',
		extraParams : {
			//param1: Ext.getCmp("grid_object").getSelectionModel().getSelection()
			/*,
			 param2: 'test2'
			 */
		},
		api : {
			read : '/adm/extjs/stock/crud/stock.php?mode=gpinfo'
		},
		reader : {
			rootProperty : 'data',
			totalProperty : 'total'
		}
	}
});

//통관탭 발주품목들
var store_clearance_dtl = Ext.create('Ext.data.Store',{
	id : 'store_clearance_dtl',
	pageSize : 300,
	fields : ['number',
		'iv_id',
		'gpcode',
		'iv_dealer',
		'iv_order_no',
		'iv_it_img',
		'iv_it_id',
		'iv_it_name',
		'iv_qty',
		'iv_dealer_worldprice',
		'iv_dealer_price',
		'total_price',
		'money_type',
		'iv_receipt_link',
		'ip_qty',
		'reg_date'
	],
	remoteSort: true,
	autoLoad : false,
	autoSync : true,
	sorters:[
		{
			property:'cr_id',
			direction:'ASC'
		}
	],
	proxy : {
		type : 'ajax',
		extraParams : {
		},
		api : {
			read : '/adm/extjs/stock/crud/stock.php',
			update	: '/adm/extjs/stock/crud/stockitem_update.php'
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

/*************************** 통관 - END *************************/

/*************************** 입고 - START *************************/

//입고예정 목록
var store_todoWarehousing = Ext.create('Ext.data.Store',{
	model	:	'model_endClearance',
	id : 'store_todoWarehousing',
	pageSize : 100,	
	remoteSort: true,
	autoLoad : true,
	autoSync : true,
	//remoteFilter: true,
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
			read : '/adm/extjs/stock/crud/stock.php?mode=invoiceEndClearance',
			update	: '/adm/extjs/stock/crud/clearance.update.php'
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


var store_warehousing_gpinfo = Ext.create('Ext.data.Store',{
	id : 'store_warehousing_gpinfo',
	pageSize : 100,
	remoteFilter:true,
	remoteSort:true,
	fields : [	'gpcode_name',
		'gpcode',
		{name: 'SUM_PAY',			sortDir: 'DESC', sortType: 'asInt', mapping: 'SUM_PAY',			type: 'int'},
		{name: 'SUM_QTY',			sortDir: 'DESC', sortType: 'asInt', mapping: 'SUM_QTY',			type: 'int'},
		{name: 'SUM_IV_QTY',	sortDir: 'DESC', sortType: 'asInt', mapping: 'SUM_IV_QTY',	type: 'int'},
		{name: 'NEED_IV_QTY',	sortDir: 'DESC', sortType: 'asInt', mapping: 'NEED_IV_QTY',	type: 'int'},
		{name: 'ITC_CNT',			sortDir: 'DESC', sortType: 'asInt', mapping: 'ITC_CNT',			type: 'int'},
		{name: 'IVC_CNT',			sortDir: 'DESC', sortType: 'asInt', mapping: 'IVC_CNT',			type: 'int'}
	],
	sorters:[
		{
			property:'GI.reg_date',
			direction:'DESC'
		}
	],
	proxy : {
		type : 'ajax',
		extraParams : {
			//param1: Ext.getCmp("grid_object").getSelectionModel().getSelection()
			/*,
			 param2: 'test2'
			 */
		},
		api : {
			read : '/adm/extjs/stock/crud/stock.php?mode=gpinfo'
		},
		reader : {
			rootProperty : 'data',
			totalProperty : 'total'
		}
	}
});

//입고탭 발주품목들
var store_warehousing_dtl = Ext.create('Ext.data.Store',{
	pageSize : 300,
	fields : ['number',
		'iv_id',
		'gpcode',
		'iv_dealer',
		'iv_order_no',
		'iv_it_img',
		'iv_it_id',
		'iv_it_name',
		'iv_qty',
		'iv_dealer_worldprice',
		'iv_dealer_price',
		'total_price',
		'money_type',
		'iv_receipt_link',
		'ip_qty',
		'reg_date'
	],
	remoteSort: true,
	autoLoad : false,
	autoSync : true,
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
			read : '/adm/extjs/stock/crud/stock.php?mode=endClearanceItem',
			update	: '/adm/extjs/stock/crud/stockitem_update.php'
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

/*************************** 입고 - END *************************/
	
	
	
var store_window_invoice = Ext.create('Ext.data.Store',{
	id : 'store_window_invoice',
	pageSize : 100,
	groupField: 'Group',
	model : 'model.invoiceEndWire',
	/*fields : ['number',
				'iv_id',
				'gpcode',
				'iv_dealer',
				'iv_order_no',
				'iv_it_id',
				'iv_it_name',
				'iv_qty',
				'iv_dealer_price',
				'total_price',
				'iv_receipt_link',
				'ip_qty',
				'reg_date'],
	*/
	remoteSort: true,
	//remoteFilter: true,
	autoLoad : false	,
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
			read : '/adm/extjs/stock/crud/stock.php?mode=invoice_info'
		},
		reader : {
			rootProperty : 'data',
			totalProperty : 'total'
		}
	}
});


var store_window_wire = Ext.create('Ext.data.Store',{
	id : 'store_window_wire',
	pageSize : 100,
	model : 'model.invoiceEndWire',
	groupField: 'Group',
	/*fields : ['number',
		'iv_id',
		'gpcode',
		'iv_dealer',
		'iv_order_no',
		'iv_it_id',
		'iv_it_name',
		'iv_qty',
		'iv_dealer_price',
		'TOTAL_PRICE',
		'iv_receipt_link',
		'ip_qty',
		'reg_date'],
		*/
	remoteSort: true,
	//remoteFilter: true,
	autoLoad : true	,
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
			read : '/adm/extjs/stock/crud/stock.php?mode=invoice_info'
		},
		reader : {
			rootProperty : 'data',
			totalProperty : 'total'
		}
	}
});


var store_window_clearance = Ext.create('Ext.data.Store',{
	id : 'store_window_clearance',
	pageSize : 100,
	//model	:	'model_stockManage',
	fields : ['number',
						'iv_id',
						'gpcode',
						'iv_dealer',
						'iv_order_no',
						'iv_it_id',
						'iv_it_name',
						'iv_qty',
						'iv_dealer_price',
						'total_price',
						'iv_receipt_link',
						'ip_qty',
						'reg_date'],
	remoteSort: true,
	//remoteFilter: true,
	autoLoad : true	,
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
			read : '/adm/extjs/stock/crud/stock.php?mode=invoice_info'
		},
		reader : {
			rootProperty : 'data',
			totalProperty : 'total'
		}
	}
});



/* 통계정보에서 선택된 품목들 데이터저장용 스토어 */
var storeTempInvoice = Ext.create('Ext.data.Store',{
	id : 'storeTempInvoice',
	autoLoad : true	,
	fields : ['number',
				'iv_id',
				'gpcode',
				'iv_dealer',
				'iv_order_no',
				'iv_it_id',
				'iv_it_name',
				'iv_qty',
				'iv_dealer_price',
				'total_price',
				'iv_receipt_link',
				'ip_qty',
				'reg_date']
});
