Ext.define('Ext.combobox.item.yesno', {
	extend: 'Ext.form.ComboBox',
	xtype: 'combobox',
	editable: false,
	displayField: 'name',
	valueField: 'value',
	name: 'yesno',
	store: Ext.create('Ext.store.item.yesno')
});

Ext.define('Ext.combobox.item.yn', {
	extend: 'Ext.form.ComboBox',
	xtype: 'combobox',
	editable: false,
	displayField: 'name',
	valueField: 'value',
	name: 'yn',
	store: Ext.create('Ext.store.item.yn')
});

Ext.define('Ext.combobox.item.metaltype', {
	extend: 'Ext.form.ComboBox',
	xtype: 'combobox',
	editable: false,
	displayField: 'name',
	valueField: 'value',
	name: 'metaltype',
	store: Ext.create('Ext.store.item.metaltype')
});

Ext.define('Ext.combobox.item.pricetype', {
	extend: 'Ext.form.ComboBox',
	haseReverseFilter:false,
	xtype: 'combobox',
	editable: false,
	displayField: 'name',
	valueField: 'value',
	name: 'pricetype',
	store: Ext.create('Ext.store.item.pricetype')
});

Ext.define('Ext.combobox.item.spottype', {
	extend: 'Ext.form.ComboBox',
	//alias : 'widget.combobox_spottype',
	haseReverseFilter:true,
	xtype: 'combobox',
	editable: false,
	displayField: 'name',
	valueField: 'value',
	name: 'pricetype',
	store: Ext.create('Ext.store.item.spottype'),
	listeners:{
		  expand:function(picker){

		  }
	 }
});