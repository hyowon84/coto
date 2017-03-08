/************* ----------------  스토어 START -------------- ******************/


/* 공구목록 */
var store_banklist = Ext.create('Ext.data.Store',{
		pageSize : 50,
		remoteSort: true,
		autoLoad : false,
		autoSync : false,
		fields : [
								{ name : 'number',				type : 'int' },
								{ name : 'account_name',	type : 'string' },
								{	name : 'tr_date',				type : 'date'},
								{	name : 'tr_time',				type : 'string'},
								{ name : 'tr_type',				type : 'string' },
								{ name : 'output_price',	type : 'int' },
								{ name : 'input_price',		type : 'int' },
								{ name : 'trader_name',		type : 'string' },
								{ name : 'remain_money',	type : 'int' },
								{ name : 'bank',					type : 'string' },
								{ name : 'bank_type',			type : 'string' },
								{ name : 'tax_type',			type : 'string' },
								{ name : 'tax_no',				type : 'string' },
								{ name : 'tax_refno',			type : 'string' },			
								{ name : 'admin_link',		type : 'string' },
								{ name : 'admin_memo',		type : 'string' }			
		],
		sorters:[
			{
				property:'BD.tr_date',
				direction:'DESC'
			},
			{
				property:'BD.tr_time',
				direction:'DESC'
			}
		],
		proxy : {
			type : 'ajax',
			extraParams : {
				
			},
			api : {
				read : '/adm/extjs/bank/json/bank.php?mode=banklist',
				update: '/adm/extjs/bank/crud/bank.update.php'
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


/* 입출금에 연결된 주문정보 목록  */
var store_banklinklist = Ext.create('Ext.data.Store',{
	pageSize : 50,
	model : 'model.orderlist',
	groupField: 'Group',
	remoteSort: true,
	autoLoad : false,
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
			read	: '/adm/extjs/order/json/order.php?mode=banklinklist',
			update: '/adm/extjs/order/crud/bank.orderdtl.update.php'
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

