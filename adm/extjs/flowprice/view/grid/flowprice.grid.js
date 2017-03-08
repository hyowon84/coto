
var pg_CellEdit = Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 2});
var pg_RowEdit = Ext.create('Ext.grid.plugin.RowEditing', {
	clicksToEdit: 2,
	clicksToMoveEditor: 1,
	autoCancel: false
});

var selModel = {
	type: 'spreadsheet'
	,columnSelect: true	// replaces click-to-sort on header
};


/************* ----------------	그리드 START -------------- ******************/

/* 귀금속 시세 셋팅 */
var grid_FpMetalSetting = Ext.create('Ext.grid.Panel',{
	title : '귀금속시세 항목설정',
	id : 'grid_FpMetalSetting',
	plugins	: Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 2}),
	selModel	: Ext.create('Ext.selection.CheckboxModel'),
	width : '50%',
	height: 400,
	store : store_FpMetalSetting,
	style : 'float:left;',
	columns : [
		{ text : 'ID',				width : 60,		dataIndex : 'number',			hidden:true	},
		{ text : '순서',				width : 80,		dataIndex : 'sortNo',			editor:{allowBlank:true},		style:'text-align:center',	align:'right',		sortable: false	},
		{
			xtype: 'gridcolumn',
			dataIndex: 'metal_type',
			text: '유형',
			style:'text-align:center',
			align:'center',
			allowBlank: true,
			editor: Ext.create('Ext.combobox.item.metaltype'),
			value : 'GL',
			renderer: rendererCombo
		},
		{ text : '중량(g)',		width : 100,	dataIndex : 'weight',			editor:{allowBlank:true},		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000.00') },
		{ text : '(+)원',			width : 100,	dataIndex : 'add_price',	editor:{allowBlank:true},		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '살때',				width : 60,		dataIndex : 'buy_price',	hidden:true	},
		{ text : '팔때',				width : 60,		dataIndex : 'sell_price',	hidden:true	}

	],
	viewConfig: {
		stripeRows: true,
		getRowClass: function(record, index) {

		}
	},
	tbar : [
		{
			text	: '생성',
			iconCls	: 'icon-add',
			handler: function() {
				var rec = Ext.create('model.MetalSetting', {
					metal_type : 'GL'
				});
				grid_FpMetalSetting.getStore().insert(0, rec);
				grid_FpMetalSetting.getStore().load();
			}
		},
		{
			text	: '삭제',
			iconCls	: 'icon-delete',
			handler: function() {
				deleteGridRecord(grid_FpMetalSetting);
			}
		},
		{
			text	: '새로고침',
			iconCls	: 'icon-refresh',
			handler: function() {
				grid_FpMetalSetting.store.load();
			}
		}
	]
});


/* 귀금속 시세 */
var grid_flowprice = Ext.create('Ext.grid.Panel',{
	title : '귀금속시세 데이터',
	id : 'grid_flowprice',
	plugins	: Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 2}),
	remoteSort: true,
	autoLoad : true,
	width : '50%',
	height	: 400,
	store : store_flowprice,
	viewConfig: {
		stripeRows: true,
		getRowClass: function(record, index) {

		}
	},
	style : 'float:left;',
	columns : [
		{ text : 'ID',		 							width : 60,		dataIndex : 'fp_id',					hidden:true	},
		{ text : 'number', 							width : 60,		dataIndex : 'number',					hidden:true	},
		{ text : '순서',			 						width : 80,		dataIndex : 'sortNo',					sortable: false,						hidden:true	},
		{ text : '금속유형',		 					width : 100,	dataIndex : 'metal_type',			sortable: false,						hidden:true	},
		{ text : '중량(g)',		 					width : 100,	dataIndex : 'weight',					style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000.00'),		hidden:true	},
		{ text : '타이틀', 		 					width : 200,		dataIndex : 'title',					sortable: false	},
		{ text : '살때', 			 					width : 120,	dataIndex : 'buy_price',			style:'text-align:center',	align:'right',	editor:{allowBlank:true},	sortable: false,	renderer: Ext.util.Format.numberRenderer('0,000')	},
		{ text : '팔때', 								width : 120,	dataIndex : 'sell_price',			style:'text-align:center',	align:'right',	editor:{allowBlank:true},	sortable: false,	renderer: Ext.util.Format.numberRenderer('0,000')	},
		{ text : '금속비율<br>금<->은',	width : 150,	dataIndex : 'metalExchrate',	style:'text-align:center',	align:'right',	sortable: false	},
		{ text : '작성일', 	 						width : 140,	dataIndex : 'start_date',			sortable: false	}
	],
	tbar : [
		{
			text	: '1.신규시세작성',
			iconCls	: 'icon-add',
			handler: function() {

				var cnt = grid_FpMetalSetting.getStore().data.length;
				var v_data = grid_FpMetalSetting.getStore().data;		//.items[0].data.id

				store_flowprice.removeAll();

				for(var i=0; i < cnt; i++) {
					var data = grid_FpMetalSetting.getStore().data.items[i].data;

					var rec = Ext.create('model.flowprice', {
						'fp_id'						: data.number,
						'metal_type'			: data.metal_type,
						'weight'					: data.weight,
						'title'						: data.title,
						'sell_price'			: (data.sell_price*1 + data.add_price*1),
						'buy_price'				: (data.buy_price*1 + data.add_price*1)
					});
					store_flowprice.add(rec);

				}
			}
		},
		{
			text	: '2.저장',
			iconCls	: 'icon-add',
			handler: function() {

				grid_flowprice.store.sync({
					success : function(batch, eOpts){
						store_flowprice.load();
						Ext.Msg.alert('Status', '저장 완료');
					},
					failure : function(record, eOpts){
						store_flowprice.load();
						Ext.Msg.alert('Status', '저장 완료');
					}
				});

			}
		},
		{
			text	: '3.일괄수정(1g기준)',
			iconCls	: 'icon-add',
			handler: function() {

				$.post(
					"./crud/flowprice.update.php",
					{ mode: 'allUpdate' },
					function(data) {
						var json = JSON.parse(data);
						Ext.Msg.alert('Status',json.message);
						store_flowprice.load();
					}
				);

			}
		},

		{
			text	: '삭제',
			iconCls	: 'icon-delete',
			handler: function() {
				deleteGridRecord(grid_flowprice);
			}
		},
		{
			text	: '새로고침',
			iconCls	: 'icon-refresh',
			handler: function() {
				store_flowprice.load();
			}
		}

	]
});




/* 환율 시세 셋팅 */
var grid_exchsetting = Ext.create('Ext.grid.Panel',{
	title : '환율 항목설정',
	id : 'grid_exchsetting',
	plugins	: Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 2}),
	selModel	: Ext.create('Ext.selection.CheckboxModel'),
	width : '50%',
	height: 400,
	store : store_exchsetting,
	style : 'float:left;',
	columns : [
		{ text : 'ID',				width : 60,		dataIndex : 'number',		hidden:true	},
		{ text : '순서',				width : 80,		dataIndex : 'sortNo',		editor:{allowBlank:true},		style:'text-align:center',	align:'right',		sortable: false	},
		{
			xtype: 'gridcolumn',
			dataIndex: 'money_type',
			text: '유형',
			style:'text-align:center',
			align:'center',
			allowBlank: true,
			editor: Ext.create('Ext.combobox.item.moneytype'),
			value : 'USD',
			renderer: rendererCombo
		},
		{ text : '단위',				width : 100,	dataIndex : 'qty',			editor:{allowBlank:true},		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000.00') },
		{ text : '판매fee',		width : 100,	dataIndex : 'sellfee',	editor:{allowBlank:true},		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000.00') },
		{ text : '매입fee',		width : 100,	dataIndex : 'buyfee',		editor:{allowBlank:true},		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000.00') },
		{ text : '타이틀', 		width : 120,	dataIndex : 'title',		editor:{allowBlank:true},		sortable: false	}
	],
	viewConfig: {
		stripeRows: true,
		getRowClass: function(record, index) {

		}
	},
	tbar : [
		{
			text	: '생성',
			iconCls	: 'icon-add',
			handler: function() {
				var rec = Ext.create('model.MoneySetting', {
					money_type : 'USD'
				});
				grid_exchsetting.getStore().insert(0, rec);
				grid_exchsetting.getStore().load();
			}
		},
		{
			text	: '삭제',
			iconCls	: 'icon-delete',
			handler: function() {
				deleteGridRecord(grid_exchsetting);
			}
		},
		{
			text	: '새로고침',
			iconCls	: 'icon-refresh',
			handler: function() {
				grid_exchsetting.getStore().load();
			}
		}
	]
});



var grid_exchrate = Ext.create('Ext.grid.Panel',{
	title : '액수별 환율 시세',
	id : 'grid_exchrate',
	remoteSort: true,
	autoLoad : true,
	width : '50%',
	height: 400,
	store : store_exchrate,
	viewConfig: {
		stripeRows: true,
		getRowClass: function(record, index) {

		}
	},
	style : 'float:left;',
	columns : [
		{ text : 'ID',					width : 60,		dataIndex : 'number',					hidden:true	},
		{ text : '순서',					width : 80,		dataIndex : 'sortNo',					hidden:true	},
		{ text : '타이틀', 			width : 120,	dataIndex : 'title'	},
		{ text : '기준환율',			width : 100,	dataIndex : 'exchrate',				style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000.00') },
		{ text : '살때환율',			width : 100,	dataIndex : 'exchrate_buy',		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000.00') },
		{ text : '팔때환율',			width : 100,	dataIndex : 'exchrate_sell',	style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000.00') }
	],
	tbar : [
		{
			text	: '새로고침',
			iconCls	: 'icon-refresh',
			handler: function() {
				grid_exchrate.getStore().load();
			}
		}
	]
});


/************* ----------------	그리드 END -------------- ******************/