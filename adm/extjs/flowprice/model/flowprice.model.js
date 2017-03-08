/************* ----------------  모델 START -------------- ******************/


Ext.define('model.dealers', {
    extend: 'Ext.data.Model',
    fields : [ 'number','ct_id','ct_name']
});


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


/* 귀금속 설정 항목값들 */
Ext.define('model.MetalSetting', {
	extend: 'Ext.data.Model',
	fields : [
		{	name : 'number',			type: 'string'},
		{	name : 'sortNo',			type: 'string'},
		{	name : 'metal_type',	type: 'string'},
		{	name : 'weight',			type: 'float'},
		{	name : 'title',				type: 'string'}
	]
});

/* 환율 설정 항목값들 */
Ext.define('model.MoneySetting', {
	extend: 'Ext.data.Model',
	fields : [
		{ name : 'number',      type : 'int' },
		{ name : 'ex_id',      	type : 'string' },
		{ name : 'sortNo',      type : 'int' },
		{ name : 'money_type',  type : 'string' },
		{ name : 'qty',     	 	type : 'int' },
		{ name : 'sellfee',     type : 'float' },
		{ name : 'buyfee',      type : 'float' },
		{ name : 'title',     	type : 'string' },
		{ name : 'reg_date',    type : 'date' }
	]
});

/*  */
Ext.define('model.flowprice', {
	extend: 'Ext.data.Model',
	fields : [
		{	name : 'number',			type: 'string'},
		{	name : 'sortNo',			type: 'string'},
		{	name : 'metal_type',	type: 'string'},
		{	name : 'weight',			type: 'float'},
		{	name : 'title',				type: 'string'},
		{	name : 'sell_price',	type: 'int'},
		{	name : 'buy_price',		type: 'int'}

	]
});


/************* ----------------  모델 END -------------- ******************/