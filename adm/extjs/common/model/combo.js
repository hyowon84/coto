/************* ----------------  모델 START -------------- ******************/

Ext.define('Ext.combobox.item.metaltype', {
	extend: 'Ext.form.ComboBox',
	xtype: 'combobox',
	editable: false,
	displayField: 'name',
	valueField: 'value',
	name: 'metaltype',
	store: Ext.create('Ext.store.item.metaltype')
});


Ext.define('Ext.combobox.item.moneytype', {
	extend: 'Ext.form.ComboBox',
	xtype: 'combobox',
	editable: false,
	displayField: 'name',
	valueField: 'value',
	name: 'moneytype',
	store: Ext.create('Ext.store.item.moneytype')
});




Ext.define('Ext.combobox.bank.banktype', {
	extend: 'Ext.form.ComboBox',
	xtype: 'combobox',
	editable: false,
	displayField: 'name',
	valueField: 'value',
	name: 'banktype',
	labelWidth : 80,
	store: Ext.create('Ext.store.bank.banktype')
});


Ext.define('Ext.combobox.bank.taxtype', {
	extend: 'Ext.form.ComboBox',
	xtype: 'combobox',
	editable: false,
	displayField: 'name',
	valueField: 'value',
	name: 'taxtype',
	labelWidth : 90,
	store: Ext.create('Ext.store.bank.taxtype')
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


Ext.define('Ext.combobox.item.ivstats', {
	extend: 'Ext.form.ComboBox',
	xtype: 'combobox',
	editable: false,
	displayField: 'name',
	valueField: 'value',
	name: 'ivstats',
	store: Ext.create('Ext.store.item.ivstats')
});



/* 재활용 콤보박스 선언, 그리드에도 사용하려면 필드레이블을 정의하면 안됨.  */

/*데이트필드*/
Ext.define('Ext.dateField.common',{
	extend: 'Ext.form.DateField',
	xtype: 'datefield',
	labelWidth : 45,		width : 160,
	format: "Y-m-d",		submitFormat : "Y-m-d"
});


/*주문상태*/
Ext.define('Ext.combobox.order.stats', {
	extend: 'Ext.form.ComboBox',
	xtype: 'combobox',
	editable: false,
	displayField: 'name',
	valueField: 'value',
	name: 'stats',
	labelWidth : 60,
	store: Ext.create('Ext.store.order.stats')
});

/*SMS예제콤보*/
Ext.define('Ext.combobox.order.smsex', {
	extend: 'Ext.form.ComboBox',
	xtype: 'combobox',
	editable: false,
	displayField: 'name',
	valueField: 'value',
	name: 'stats',
	labelWidth : 60,
	store: Ext.create('Ext.store.order.smsex')
});

/* 검색유형 */
Ext.define('Ext.combobox.order.searchtype', {
	extend: 'Ext.form.ComboBox',
	xtype: 'combobox',
	editable: false,
	displayField: 'name',
	valueField: 'value',
	name: 'searchtype',
	labelWidth : 60,
	width : 120,
	store: Ext.create('Ext.store.order.searchtype')
});


/*결제유형*/
Ext.define('Ext.combobox.order.paytype', {
	extend: 'Ext.form.ComboBox',
	xtype: 'combobox',
	editable: false,
	displayField: 'name',
	valueField: 'value',
	name: 'paytype',
	value : 'P01',
	labelWidth : 60,
	width : 170,
	store: Ext.create('Ext.store.order.paytype')
});

/*배송유형*/
Ext.define('Ext.combobox.order.delivery_type', {
	extend: 'Ext.form.ComboBox',
	xtype: 'combobox',
	editable: false,
	displayField: 'name',
	valueField: 'value',
	name: 'delivery_type',
	value : 'D01',
	labelWidth : 60,
	width : 130,
	store: Ext.create('Ext.store.order.delivery_type')
});

/* 현금영수증 신청여부 */
Ext.define('Ext.combobox.order.cashreceipt_yn', {
	extend: 'Ext.form.ComboBox',
	xtype: 'combobox',
	editable: false,
	displayField: 'name',
	valueField: 'value',
	name: 'cash_receipt_yn',
	value : 'N',
	margin: '0 0 0 10',
	labelWidth : 80,
	width : 160,
	store: Ext.create('Ext.store.order.cashreceipt_yn')
});

/*현금영수증 신청유형*/
Ext.define('Ext.combobox.order.cashreceipt_type', {
	extend: 'Ext.form.ComboBox',
	xtype: 'combobox',
	editable: false,
	displayField: 'name',
	valueField: 'value',
	name: 'cash_receipt_type',
	value : '',
	margin: '0 0 0 10',
	labelWidth : 70,
	width : 190,
	store: Ext.create('Ext.store.order.cashreceipt_type')
});



/************* ----------------  모델 END -------------- ******************/