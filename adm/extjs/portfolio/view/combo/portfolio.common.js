
Ext.define('Ext.combobox.item.metaltype', {
	extend: 'Ext.form.ComboBox',
	xtype: 'combobox',
	editable: false,
	displayField: 'name',
	valueField: 'value',
	name: 'metaltype',
	store: Ext.create('Ext.store.item.metaltype')
});


Ext.define('Ext.combobox.item.investtype', {
	extend: 'Ext.form.ComboBox',
	//alias : 'widget.combobox_spottype',
	haseReverseFilter:true,
	xtype: 'combobox',
	editable: false,
	displayField: 'name',
	valueField: 'value',
	name: 'investtype',
	store: Ext.create('Ext.store.item.investtype')
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
	/*listeners:{
		  expand:function(picker){
				var store = picker.getStore();
					 store.suspendEvents();
					 store.clearFilter();
					 store.resumeEvents();
				if(picker.haseReverseFilter){
					 store.filter([{
						  fn: function(record) {
								return record.get('type') >= 2;
						  }
					 }]);
				}else{
					 store.filter([{
							  fn: function(record) {
								 return record.get('type') <= 2;
							  }
					 }]);
				}
		  }
	 }
	*/
});
