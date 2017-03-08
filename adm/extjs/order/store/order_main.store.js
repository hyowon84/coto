/************* ----------------  스토어 START -------------- ******************/



/* 주문정보 목록  */
var store_orderlist = Ext.create('Ext.data.Store',{
	pageSize : 50,
	model : 'model.orderlist',
	groupField: 'Group',
	remoteSort: true,
	autoLoad : true,
	autoSync : true,
	sorters: [
		{
			property: 'CL.od_date',
			direction: 'DESC'
		},
		{
			property: 'CL.od_id',
			direction: 'DESC'
		}
	],
	proxy : {
		type : 'ajax',
		extraParams : {
		},
		api : {
			read	: '/adm/extjs/order/json/order.php?mode=orderlist',
			update: '/adm/extjs/order/orderdtl_update.php'
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


/* SMS로그 */
var store_smslog = Ext.create('Ext.data.Store',{
		pageSize : 10,
		autoLoad : false,
		remoteSort: true,
		fields : [
					{ name : 'wr_no',			type: 'string'},
					{ name : 'wr_message',	type: 'string'},
					{ name : 'wr_datetime',	type: 'date'},
					{ name : 'wr_target',	type: 'string'},
					{ name : 'wr_reply',		type: 'string'}
		],
		sorters:[
			{
				property:'wr_datetime',
				direction:'DESC'
			}
		],
		proxy : {
			type : 'ajax',
			extraParams : {
				
			},
			api : {
				read : '/adm/extjs/order/json/smslog.php'
			},
			reader : {
				rootProperty : 'data',
				totalProperty : 'total'
			}			
		}
});

/* 입금로그 */
var store_banklog = Ext.create('Ext.data.Store',{
	pageSize : 10,
	autoLoad : false,
	remoteSort: true,
	fields : [
		{ name : 'tr_date',				type: 'string'},
		{ name : 'tr_time',				type: 'string'},
		{ name : 'input_price',		type: 'int'},
		{ name : 'trader_name',		type: 'string'}
	],
	sorters:[
		{
			property:'BANK_STAT',
			direction:'DESC'
		},
		{
			property:'tr_date',
			direction:'DESC'
		},
		{
			property:'tr_time',
			direction:'DESC'
		}
	],
	proxy : {
		type : 'ajax',
		extraParams : {

		},
		api : {
			read : '/adm/extjs/order/json/banklog.php',
			update: '/adm/extjs/order/crud/banklog.update.php'
		},
		reader : {
			rootProperty : 'data',
			totalProperty : 'total'
		}
	}
});

/* 변경로그 */
var store_log = Ext.create('Ext.data.Store',{
		pageSize : 10,
		autoLoad : false,
		remoteSort: true,
		fields : [
					{ name : 'memo',			type: 'string'},
					{ name : 'key_id',		type: 'string'},
					{ name : 'value',			type: 'string'},
					{ name : 'reg_date',		type: 'date'},					
					{ name : 'mb_name',		type: 'string'}
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
				read : '/adm/extjs/order/json/log.php'
			},
			reader : {
				rootProperty : 'data',
				totalProperty : 'total'
			}			
		}
});


Ext.tip.QuickTipManager.init();

var store_window_wire = Ext.create('Ext.data.Store',{
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


var store_winSms = Ext.create('Ext.data.Store',{
	model	:	'model.SmsSendForm',
	sorters:	{	property:'od_id',		direction:'ASC'}
	//groupField: 'project'
});
