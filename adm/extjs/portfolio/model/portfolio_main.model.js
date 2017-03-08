/************* ----------------  모델 START -------------- ******************/


Ext.define('model.dealers', {
    extend: 'Ext.data.Model',
    fields : [ 'number','ct_id','ct_name']
});



/* 포트폴리오 VIP회원 */
Ext.define('model.vipMbInfo', {
    extend: 'Ext.data.Model',
    fields: [
		{	name : 'pf_id',			type: 'string'},
		{	name : 'nick',				type: 'string'},
		{	name : 'name',				type: 'string'},
		{	name : 'hphone',			type: 'string'},
		{	name : 'TOTAL_ASSET',	type: 'int'},
		{	name : 'TOTAL_GL',		type: 'int'},
		{	name : 'TOTAL_SL',		type: 'int'}
    ]
});



/* 포트폴리오 통계 */
Ext.define('model.pfDataSummary', {
   extend: 'Ext.data.Model',
	fields : [	
					{name: 'metal_type_nm',		type: 'string'},
					{name: 'invest_type_nm',	type: 'string'},
					{name: 'TOTAL_PRICE',		type: 'int'},
					{name: 'TOTAL_GRAM',			type: 'int'},
					{name: 'TOTAL_DON',			type: 'float'},					
					{name: 'TOTAL_OZ',			type: 'float'}
    ]
});


/* 포트폴리오 입력항목 */
Ext.define('model.pfItemList', {
   extend: 'Ext.data.Model',
	fields : [	
					{name: 'pf_id',				type: 'string'},
					{name: 'd_id',					type: 'string'},
					{name: 'gp_id',				type: 'string'},
					{name: 'gp_img',				type: 'string'},
					{name: 'item_name',			type: 'string'},
					{name: 'metal_type',			type: 'string'},
					{name: 'invest_type',		type: 'string'},
					{name: 'gram_per_price',	type: 'int'},
					{name: 'CALC_PRICE',			type: 'int'},
					{name: 'gram',					type: 'int'},
					{name: 'don',					type: 'float'},
					{name: 'oz',					type: 'float'},
					{name: 'reg_date',			type: 'date'}
    ]
});


/* 포트폴리오 통계 */
Ext.define('model.investdtl', {
   extend: 'Ext.data.Model',
	fields : [	
					{name: 'pf_id',			type: 'string'},
					{name: 'invest_type',			type: 'string'},
					{name: 'metal_type',		type: 'int'},
					{name: 'target_per',		type: 'int'},
					{name: 'TARGET_PRICE',	type: 'float'}
    ]
});

/************* ----------------  모델 END -------------- ******************/