/************* ----------------  스토어 START -------------- ******************/




/* 공구목록 */
var store_FpMetalSetting = Ext.create('Ext.data.Store',{
	autoLoad : true,
	remoteSort: true,
	autoSync : true,
	fields : [
				{	name : 'number',			type: 'string'},
				{	name : 'sortNo',			type: 'string'},
				{	name : 'metal_type',	type: 'string'},
				{	name : 'weight',			type: 'float'},
				{	name : 'title',				type: 'string'}
	],
	sorters:[
		{
			property:'sortNo',
			direction:'ASC'
		}
	],
	proxy : {
		type : 'ajax',
		extraParams : {},
		api : {
			read	 	: '/adm/extjs/flowprice/json/flowprice.php?mode=fpmetalsetting',
			create	: '/adm/extjs/flowprice/crud/fpsetting.create.php',
			update	: '/adm/extjs/flowprice/crud/fpsetting.update.php',
			destroy	: '/adm/extjs/flowprice/crud/fpsetting.delete.php'
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


/* 주문정보 목록  */
var store_flowprice = Ext.create('Ext.data.Store',{
	remoteSort: true,
	autoLoad : true,
	autoSync : false,
	fields : [
		{	name : 'number',			type: 'string'},
		{	name : 'sortNo',			type: 'string'},
		{	name : 'metal_type',	type: 'string'},
		{	name : 'weight',			type: 'float'},
		{	name : 'title',				type: 'string'},
		{	name : 'sell_price',	type: 'int'},
		{	name : 'buy_price',		type: 'int'}
	],
	sorters:[
		{
			property:'FC.sortNo',
			direction:'ASC'
		},
		{
			property:'FD.start_date',
			direction:'DESC'
		}
	],
	proxy : {
		type : 'ajax',
		extraParams : {
		},
		api : {
			read : '/adm/extjs/flowprice/json/flowprice.php?mode=flowprice',
			create	: '/adm/extjs/flowprice/crud/flowprice.create.php',
			update	: '/adm/extjs/flowprice/crud/flowprice.update.php',
			destroy	: '/adm/extjs/flowprice/crud/flowprice.delete.php'
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



/* 환율항목 설정  */
var store_exchsetting = Ext.create('Ext.data.Store',{
	autoLoad : true,
	remoteSort: true,
	autoSync : true,
	fields : [
		{ name : 'number',			type : 'string' },
		{ name : 'sortNo',			type : 'string' },
		{ name : 'money_type',	type : 'string' },
		{ name : 'qty',					type : 'float' },
		{ name : 'sellfee',			type : 'float' },
		{ name : 'buyfee',			type : 'float' },
		{ name : 'title',				type : 'string' },
		{ name : 'reg_date',		type : 'date' }
	],
	sorters:[
		{
			property:'sortNo',
			direction:'ASC'
		}
	],
	proxy : {
		type : 'ajax',
		extraParams : {},
		api : {
			read	 	: '/adm/extjs/flowprice/json/flowprice.php?mode=exchsetting',
			create	: '/adm/extjs/flowprice/crud/exchsetting.create.php',
			update	: '/adm/extjs/flowprice/crud/exchsetting.update.php',
			destroy	: '/adm/extjs/flowprice/crud/exchsetting.delete.php'
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


/* 환율 시세 */
var store_exchrate = Ext.create('Ext.data.Store',{
	remoteSort: true,
	autoLoad : true,
	autoSync : true,
	fields : [
		{	name : 'number',			type: 'string'},
		{	name : 'sortNo',			type: 'string'},
		{	name : 'metal_type',	type: 'string'},
		{	name : 'weight',			type: 'float'},
		{	name : 'title',				type: 'string'}
	],
	sorters:[
		{
			property:'sortNo',
			direction:'ASC'
		}
	],
	proxy : {
		type : 'ajax',
		extraParams : {
		},
		api : {
			read		: '/adm/extjs/flowprice/json/flowprice.php?mode=exchrate'
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

