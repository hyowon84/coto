/************* ----------------  모델 START -------------- ******************/

/* 공동구매 모델 */
Ext.define('model.gpinfo', {
	extend: 'Ext.data.Model',
	fields: [
		{	name : 'gpcode',				type: 'string'},
		{	name : 'gpcode_name',		type: 'string'},
		{	name : 'invoice_memo',	type: 'string'},
		{	name : 'memo',					type: 'string'},
		{	name : 'reg_date',			type: 'date'}
	]
});


/* 공동구매 신청정보 모델 */
Ext.define('model_stockManage', {
	extend: 'Ext.data.Model',
	fields: [
			'ivchk',
			'gpcode',
			'gpcode_name',
			'it_id',
			'admin_memo',
			'gp_img',
			'gp_name',
			'it_org_price',
			'SUM_QTY',
			'total_price',
			'SUM_IV_QTY',
			'NEED_IV_QTY'
	]
});


/* 인보이스 발주서 기본정보 */
Ext.define('model.invoice', {
	extend: 'Ext.data.Model',
	fields : [
		{ name : 'Group',							type: 'string'},
		{ name : 'iv_id',						type : 'string' },
		{ name : 'wr_id',						type : 'string' },
		{ name : 'iv_name',					type : 'string' },
		{ name : 'gpcode',					type : 'string' },
		{ name : 'iv_dealer',				type : 'string' },
		{ name : 'iv_order_no',			type : 'string' },
		{ name : 'iv_receipt_link',	type : 'string' },
		{ name : 'iv_date',					type : 'string' },
		{ name : 'money_type',			type : 'string' },
		{ name : 'iv_memo',					type : 'string' },
		{ name : 'reg_date',				type : 'date'		},
		{ name : 'admin_id',				type : 'string'	},
		{ name : 'TOTAL_PRICE',			type : 'float'	},
		{ name : 'iv_discountfee',	type : 'float'	},
		{ name : 'iv_shippingfee',	type : 'float'	},
		{ name : 'iv_tax',					type : 'float'	},
		{ name : 'od_exch_rate',		type : 'float'	},
		{ name : 'arv_exch_rate',		type : 'float'	}
	]
});

/* 송금완료&통관예정 발주서 */
Ext.define('model.invoiceEndWire', {
	extend: 'Ext.data.Model',
	fields : [
		{ name : 'Group',						type: 'string'	},
		{ name : 'cr_id',						type: 'string'	},
		{ name : 'wr_id',						type: 'string'	},
		{ name : 'wr_name',					type: 'string'	},
		{ name : 'wr_in_fee',				type: 'float'		},
		{ name : 'wr_out_fee',			type: 'float'		},
		{ name : 'gpcode',					type: 'string'	},
		{ name : 'iv_id',						type: 'string'	},
		{ name : 'iv_dealer',				type: 'string'	},
		{ name : 'iv_order_no',			type: 'string'	},
		{ name : 'iv_receipt_link',	type: 'string'	},
		{ name : 'iv_date',					type: 'date'		},
		{ name : 'od_exch_rate',		type: 'string'	},
		{ name : 'money_type',			type: 'string'	},
		{ name : 'iv_tax',					type: 'float'		},
		{ name : 'iv_shippingfee',	type: 'float'		},
		{ name : 'iv_memo',					type: 'string'	},
		{ name : 'TOTAL_PRICE',			type: 'float'		},
		{ name : 'admin_id',				type: 'string'	},
		{ name : 'admin_name',			type: 'string'	},
		{ name : 'reg_date',				type: 'string'	}
	]
});



/* 인보이스 발주서 상세품목 */
Ext.define('model.invoice_detail', {
    extend: 'Ext.data.Model',
    fields : [ 'number',
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
				'reg_date'
	],
	validators: {
	    iv_qty: {
	        type: 'length',
	        min: 1
	    },
	    iv_dealer_price: {
	        type: 'length',
	        min: 1
	    }
	}
});

Ext.define('model.dealers', {
    extend: 'Ext.data.Model',
    fields : [ 'number','ct_id','ct_name']
});


Ext.define('model.combo', {
	extend: 'Ext.data.Model',
	fields : [ 'number','value','title']
});


/* 통관완료&입고예정 발주서 */
Ext.define('model_endClearance', {
	extend: 'Ext.data.Model',
	fields : [
		{name: 'Group',								type: 'string'},
		{name: 'gpcode',							type: 'string'},
		{name: 'iv_id',								type: 'string'},
		{name: 'cr_id',								type: 'string'},
		{name: 'wr_id',								type: 'string'},
		{name: 'wr_name',							type: 'string'},
		{name: 'wr_in_fee',						type: 'float'},
		{name: 'wr_out_fee',					type: 'float'},
		{name: 'iv_dealer',						type: 'string'},
		{name: 'iv_order_no',					type: 'string'},
		{name: 'iv_receipt_link',			type: 'string'},
		{name: 'iv_date',							type: 'date'},
		{name: 'od_exch_rate',				type: 'string'},
		{name: 'money_type',					type: 'string'},
		{name: 'iv_tax',							type: 'float'},
		{name: 'iv_shippingfee',			type: 'float'},
		{name: 'iv_memo',							type: 'string'},
		{name: 'admin_id',						type: 'string'},
		{name: 'admin_name',					type: 'string'},
		{name: 'reg_date',						type: 'string'}
	]
});






/************* ----------------  모델 END -------------- ******************/