/************* ----------------  모델 START -------------- ******************/


/* 주문목록 */
Ext.define('model.orderlist', {
	extend: 'Ext.data.Model',
	fields : [
		{name: 'Group',								type: 'string'},
		{name: 'number',							type: 'string'},
		{name: 'gpcode',							type: 'string'},
		{name: 'gpcode_name',					type: 'string'},
		{name: 'od_id',								type: 'string'},
		{name: 'gp_img',							type: 'string'},
		{name: 'it_id',								type: 'string'},
		{name: 'it_org_price',				type: 'int'},
		{name: 'it_qty',							type: 'int'},
		{name: 'it_name',							type: 'string'},
		{name: 'clay_id',							type: 'string'},
		{name: 'name',								type: 'string'},
		{name: 'receipt_name',				type: 'string'},
		{name: 'cash_receipt_yn',			type: 'string'},
		{name: 'cash_receipt_type_nm',type: 'string'},
		{name: 'cash_receipt_info',		type: 'string'},
		{name: 'hphone',							type: 'string'},
		{name: 'zip',									type: 'string'},
		{name: 'addr1',								type: 'string'},
		{name: 'addr1_2',							type: 'string'},
		{name: 'addr2',								type: 'string'},
		{name: 'memo',								type: 'string'},
		{name: 'admin_memo',					type: 'string'},
		{name: 'delivery_type_nm',		type: 'string'},
		{name: 'delivery_company',		type: 'string'},
		{name: 'delivery_invoice',		type: 'string'},
		{name: 'delivery_price',			type: 'string'},
		{name: 'paytype_nm',					type: 'string'},
		{name: 'CNT_TOTAL_ORDER', 		type: 'int'},
		{name: 'CNT_ORDER', 					type: 'int'},
		{name: 'od_date',							type: 'date'},
		{name: 'gp_img',							type: 'string'},
		{name: 'it_id',								type: 'string'},
		{name: 'it_qty',							type: 'int'},
		{name: 'it_org_price',				type: 'int'},
		{name: 'TOTAL_PRICE',					type: 'int'},
		{name: 'SELL_PRICE',					type: 'int'},
		{name: 'stats',								type: 'string'}
	]
});


/************* ----------------  모델 END -------------- ******************/