/************* ----------------  모델 START -------------- ******************/

Ext.define('model.dealers', {
	extend: 'Ext.data.Model',
	fields : [ 'number','ct_id','ct_name']
});

Ext.define('model.combo', {
	extend: 'Ext.data.Model',
	fields : [ 'number','value','title']
});


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


/*  */
Ext.define('model.banklist', {
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


/************* ----------------  모델 END -------------- ******************/