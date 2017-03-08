/************* ----------------  모델 START -------------- ******************/


Ext.define('model.dealers', {
    extend: 'Ext.data.Model',
    fields : [ 'number','ct_id','ct_name']
});


/* 주문목록 모델 */
Ext.define('model_orderlist', {
	extend: 'Ext.data.Model',
	fields : ['gpcode',
				'gpstats_name',
				'IV_STATS_NAME',
				'od_id',
				'clay_id',
				'paytype',
				'od_date',
				'delivery_invoice',
				'hidden',
				'stats',
				'od_ip',
				'admin_memo',
				'memo',
				{name: 'IV_STATS',		sortDir: 'DESC', sortType: 'asInt', mapping: 'IV_STATS',			type: 'int'},
				{name: 'gpstats',			sortDir: 'DESC', sortType: 'asInt', mapping: 'gpstats',			type: 'int'},
				{name: 'it_org_price',	sortDir: 'DESC', sortType: 'asInt', mapping: 'it_org_price',	type: 'int'},
				{name: 'it_qty',			sortDir: 'DESC', sortType: 'asInt', mapping: 'it_qty',			type: 'int'},
				{name: 'total_price',	sortDir: 'DESC', sortType: 'asInt', mapping: 'total_price',		type: 'int'}
	]
});



/* 배송할 주문목록 */
Ext.define('Task', {
    extend: 'Ext.data.Model',
    idProperty: 'taskId',
    fields: [
		{name: 'projectId',		type: 'int'},
		{name: 'project',			type: 'string'},		
		{name: 'taskId',			type: 'int'},
		{name: 'gpcode',			type: 'string'},
		{name: 'gpcode_name',	type: 'string'},
		{name: 'stats',			type: 'string'},
		{name: 'gpstats',			type: 'string'},		
		{name: 'gpstats_name',	type: 'string'},
		{name: 'od_id',			type: 'string'},
		{name: 'clay_id',			type: 'string'},		
		{name: 'gpcode_name',	type: 'string'},		
		{name: 'it_id',			type: 'string'},
		{name: 'it_name',			type: 'string'},
		{name: 'it_org_price',	type: 'float'},
		{name: 'it_qty', 			type: 'int'},
		{name: 'total_price',	type: 'int'},
		{name: 'od_date',			type: 'date'}
    ]
});


/************* ----------------  모델 END -------------- ******************/