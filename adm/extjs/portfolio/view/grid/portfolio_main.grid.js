
/************* ----------------	그리드 START -------------- ******************/

/* 탭:자료입력 */


/* DATA 탭내 스토어 초기화 */
function loadPFtab1(sm) {
	store_pfItemList.loadData([],false);
	store_pfDataSummary.loadData([],false);
	
	if(sm) {
		if(sm.data.pf_id.length > 3) {
		
			Ext.apply(store_pfDataSummary.getProxy().extraParams, sm.data);
			Ext.apply(store_pfItemList.getProxy().extraParams, sm.data);
			
			
			/* DATA 탭내 스토어 초기화 */
			store_pfItemList.load();
			store_pfDataSummary.load();
			
		} else {
			Ext.getCmp('navi_center').setTitle('> ');
		}
	}
	
}

/* PORTFOLIO 탭내 스토어 초기화 */
function loadPFtab2(sm) {
	store_expectInvest.loadData([],false);
	store_beginFundSet.loadData([],false);
	store_beginFundBuy.loadData([],false);
	store_invest.loadData([],false);
	store_investdtl.loadData([],false);
	store_achInvest.loadData([],false);
	store_achInvestdtl.loadData([],false);
	store_estimate.loadData([],false);
	
	if(sm) {
		if(sm.data.pf_id.length > 3) {
			Ext.apply(store_expectInvest.getProxy().extraParams, sm.data);
			Ext.apply(store_beginFundSet.getProxy().extraParams, sm.data);
			Ext.apply(store_beginFundBuy.getProxy().extraParams, sm.data);
			Ext.apply(store_invest.getProxy().extraParams, sm.data);
			Ext.apply(store_investdtl.getProxy().extraParams, sm.data);
			Ext.apply(store_achInvest.getProxy().extraParams, sm.data);
			Ext.apply(store_achInvestdtl.getProxy().extraParams, sm.data);
			Ext.apply(store_estimate.getProxy().extraParams, sm.data);
			
			/* PORTFOLIO 탭내 스토어 초기화 */
			store_expectInvest.load();
			store_beginFundSet.load();
			store_beginFundBuy.load();
			store_invest.load();
			store_investdtl.load();
			store_achInvest.load();
			store_achInvestdtl.load();
			store_estimate.load();
			
		} else {
			Ext.getCmp('navi_center').setTitle('> ');
		}
	}
	
}

/* CHART 탭내 스토어 초기화 */
function loadPFtab3(sm) {
	store_chartInvest.loadData([],false);
	store_chartMetalPer.loadData([],false);
	store_chartGoldPer.loadData([],false);
	store_chartSilverPer.loadData([],false);
	store_MetalInvestPrice.loadData([],false);
	store_MetalInvestPer.loadData([],false);
	store_TargetAchieve.loadData([],false);
	
	if(sm) {
		if(sm.data.pf_id.length > 3) {
			Ext.apply(store_chartInvest.getProxy().extraParams, sm.data);
			Ext.apply(store_chartMetalPer.getProxy().extraParams, sm.data);
			Ext.apply(store_chartGoldPer.getProxy().extraParams, sm.data);
			Ext.apply(store_chartSilverPer.getProxy().extraParams, sm.data);
			Ext.apply(store_MetalInvestPrice.getProxy().extraParams, sm.data);
			Ext.apply(store_MetalInvestPer.getProxy().extraParams, sm.data);
			Ext.apply(store_TargetAchieve.getProxy().extraParams, sm.data);
			
			/* CHART 탭내 스토어 초기화 */
			store_chartInvest.load();
			store_chartMetalPer.load();
			store_chartGoldPer.load();
			store_chartSilverPer.load();
			store_MetalInvestPrice.load();
			store_MetalInvestPer.load();
			store_TargetAchieve.load();
		} else {
			Ext.getCmp('navi_center').setTitle('> ');
		}
	}
	
}



/* 자산관리회원 목록 */
var grid_vipMbList = Ext.create('Ext.grid.Panel',{
	id : 'grid_vipMbList',
	remoteSort: true,
	plugins	: Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 2}),
	selModel	: Ext.create('Ext.selection.CheckboxModel'),
	autoLoad : true,
	height	: 810,
	autoWidth : true,
	autoHeight : true,
	columns : [
		{ text : '관리번호',		width : 150,	dataIndex : 'pf_id'	},
		{ text : '닉네임', 		width : 120,	dataIndex : 'nick',			editor:{allowBlank:true}	},
		{ text : '이름',				width : 90,		dataIndex : 'name',			editor:{allowBlank:true}	},
		{ text : '휴대폰',			width : 120,	dataIndex : 'hphone',		editor:{allowBlank:true},	hidden:true	}
		/*,
		{ text : '자산총액',		width : 120,	dataIndex : 'TOTAL_PRICE',	style:'text-align:center',		align:'right',		renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '금(g)',			width : 120,	dataIndex : 'GL_GRAM',		style:'text-align:center',		align:'right',		renderer: rendererColumnFormat },
		{ text : '금 가격',		width : 120,	dataIndex : 'GL_PRICE',		style:'text-align:center',		align:'right',		renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '은(g)',			width : 120,	dataIndex : 'SL_GRAM',		style:'text-align:center',		align:'right',		renderer: rendererColumnFormat },
		{ text : '은 가격',		width : 120,	dataIndex : 'SL_PRICE',		style:'text-align:center',		align:'right',		renderer: Ext.util.Format.numberRenderer('0,000') }*/		
	],
	store : store_vipMbList,
	viewConfig: {
		stripeRows: true,
		getRowClass: function(record, index) {}
	},
	tbar : [
				{
					text	: '생성',
					iconCls	: 'icon-add',
					handler: function() {			
						var rec = Ext.create('model.vipMbInfo', {
							nick : '신규'
						});
						grid_vipMbList.getStore().insert(0, rec);
						grid_vipMbList.getStore().load();
					}
				},
				{
					text	: '삭제',
					iconCls	: 'icon-delete',
					handler: function() {
						
					}
				},
				{
					text	: '통계 새로고침',
					iconCls	: 'icon-refresh',
					handler: function() {
						/* 공구목록의 선택된 레코드 */
						var sm = grid_vipMbList.getSelectionModel().getSelection()[0];
						loadPFtab1(sm);
						loadPFtab2(sm);
						loadPFtab3(sm);
					}
				}
				
	],
	bbar : {
		plugins: new Ext.ux.SlidingPager(),
		xtype : 'pagingtoolbar',
		store : store_vipMbList,
		displayInfo : true,
		displayMsg : '{0}/{1} Total - {2}',
		emptyMsg : 'No Data'
	},
	listeners : {
		selectionchange: function(view, records) {
			/* 공구목록의 선택된 레코드 */
			var sm = grid_vipMbList.getSelectionModel().getSelection()[0];
			loadPFtab1(sm);
			loadPFtab2(sm);
			loadPFtab3(sm);
	 	}
	}
});

/* 우측 상단 보유귀금속자산 통계 */
var grid_pfDataSummary = Ext.create('Ext.grid.Panel',{
	title : '통계',
	features: [{ ftype : 'groupingsummary' }],
	collapsible : true,
	width : '100%',
	height	: 410,
	autoWidth : true,
	columns	: [
		{ text: '금속', 			dataIndex: 'metal_type_nm',			width: 130,		style:'text-align:center',		align:'center'	},
		{ text: '성향',			dataIndex: 'invest_type_nm',			width: 150	},
		{ text: '총 합계금액',	dataIndex: 'TOTAL_PRICE',				width: 120,		style:'text-align:center',		align:'right',			renderer: rendererColumnFormat, 	summaryType : 'sum',		summaryRenderer : rendererSummaryFormat	},
		{ text: '총 무게(돈)',	dataIndex: 'TOTAL_DON',					width: 120,		style:'text-align:center',		align:'right',			renderer: rendererColumnFormat, 	summaryType : 'sum',		summaryRenderer : rendererSummaryFormat	},
		{ text: '총 무게(g)',	dataIndex: 'TOTAL_GRAM',				width: 120,		style:'text-align:center',		align:'right',			renderer: rendererColumnFormat, 	summaryType : 'sum',		summaryRenderer : rendererSummaryFormat	},
		{ text: '총 무게(oz)',	dataIndex: 'TOTAL_OZ',					width: 120,		style:'text-align:center',		align:'right',			renderer: rendererColumnFormat, 	summaryType : 'sum',		summaryRenderer : rendererSummaryFormat	}
	],
	viewConfig: {
		stripeRows: true,
		getRowClass: orderStatsColorSetting
	},
	store : store_pfDataSummary,
	dockedItems: [
		{
			xtype : 'toolbar',
			dock : 'top',
			items : [
				'-',
				{xtype : 'tbfill'},
				{	xtype: 'label',		fieldLabel: 'alert',		style : 'margin-left:20px; font-weight:bold; color:blue;',
					text: '금: $'+v_GL+' / 은: $'+v_SL+' / $환율: '+v_USD+'원'
				}
			]
		}
	]
});


/* 우측 하단 보유금속 목록 */
var grid_pfItemList = Ext.create('Ext.grid.Panel',{
	title : '보유금속목록',
	xtype: 'spreadsheet-checked',
	height	: 430,
	autoWidth : true,
	columnLines: true,
	plugins: [	Ext.create('Ext.grid.plugin.CellEditing',{
						clicksToEdit: 1
					}),
					'clipboard'],
	selModel: {
		type: 'spreadsheet',
		// Disables sorting by header click, though it will be still available via menu
		columnSelect: true,
		checkboxSelect: true,
		pruneRemoved: false
	},
	resizable: true,
	columns : [
		//{ text : 'img',				dataIndex : 'gp_img',			width : 60,			renderer: function(value){	return '<img src="' + value + '" width=40 height=40 />';}	},
		{ text : 'd_id',			dataIndex : 'd_id',				width : 160,		hidden:true	},
		{ text : '관리번호',		dataIndex : 'pf_id',				width : 160,		hidden:true	},
		{ text : '상품코드',		dataIndex : 'it_id',				width : 100,	editor:{allowBlank:true},	hidden:true	},
		{
			xtype: 'gridcolumn',
			dataIndex: 'metal_type',
			text: '금속유형',
			style:'text-align:center',
			align:'center',
			allowBlank: true,
			editor: Ext.create('Ext.combobox.item.metaltype'),
			renderer: rendererCombo
		},
		{
			xtype: 'gridcolumn',
			dataIndex: 'invest_type',
			text: '투자성향',
			style:'text-align:center',
			align:'center',
			allowBlank: true,
			editor: Ext.create('Ext.combobox.item.investtype'),
			renderer: rendererCombo
		},
		{ text : '품목명',		dataIndex : 'item_name',		width : 350,	editor:{allowBlank:false},		sortable:true	},
		{ text : '시세반영금액',dataIndex : 'CALC_PRICE',		width : 100,	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '구매당시총액',dataIndex : 'BUYED_PRICE',		width : 100,	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '중량(g)',		dataIndex : 'gram',				width : 160,	editor:{allowBlank:false},		renderer: rendererColumnFormat },
		{ text : '중량(3.75g)',	dataIndex : 'don',				width : 160,	renderer: rendererColumnFormat },
		{ text : '중량(t.oz)',	dataIndex : 'oz',					width : 160,	renderer: rendererColumnFormat },		
		{ text : '중량당 가격',	dataIndex : 'gram_per_price',	width : 160,	editor:{allowBlank:false},		renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '등록일',		dataIndex : 'reg_date',			width : 160,	sortable: true,		renderer: Ext.util.Format.dateRenderer('Y-m-d H:i:s'),		field: { xtype: 'datefield',	format:'Y-m-d H:i:s' },	hidden:true	}
	],
	store : store_pfItemList,
	viewConfig: {
		columnLines: true,
		trackOver: true
	},
	tbar : [
				{
					xtype : 'numberfield',
					fieldLabel: '행',
					labelWidth : 20,
					minValue : 0,
					maxValue : 100,
					width : 80,
					id : 'makeCnt',
					value : 5
				},
				{
					text	: '행추가',
					iconCls	: 'icon-add',
					handler: function() {
						
						grid_pfItemList.getSelectionModel().getSelection()[0];
						
						var sm = grid_vipMbList.getSelectionModel().getSelection()[0];
						
						if(sm) {
							var v_pf_id = sm.data.pf_id	;
							
							
							for(var i = 0; i < Ext.getCmp('makeCnt').getValue(); i++) {
								var rec = Ext.create('model.pfItemList', {
									pf_id : v_pf_id,
									gp_id : '신규'
								});
								grid_pfItemList.store.insert(i, rec);
							}
						}
						else {
							Ext.Msg.alert('안내', '좌측 목록에서 회원을 선택하세요');
						}
					}
				},
				{
					text	: '행삭제',
					iconCls	: 'icon-delete',
					handler: function() {
						var sm = grid_pfItemList.getSelectionModel().getSelection();

						if(sm.length) {

							for(var i = 0; i < sm.length; i++) {
								grid_pfItemList.store.remove(sm[i]);
							}
						}
					}
				},
				{
					text	: '저장',
					iconCls	: 'icon-add',
					handler: function() {
						
						grid_pfItemList.store.sync({
							success : function(batch, eOpts){
								Ext.Msg.alert('Status', '저장 완료');
							},
							failure : function(record, eOpts){
								Ext.Msg.alert('Status', '저장 실패');
							}
						});
						
					}
				},
				 '->',
				{
					xtype: 'component',
					id : 'status',
					reference: 'status'
				}
	],
	bbar : {
		plugins: new Ext.ux.SlidingPager(),
		xtype : 'pagingtoolbar',
		store : store_pfItemList,
		displayInfo : true,
		displayMsg : '{0}/{1} Total - {2}',
		emptyMsg : 'No Data'
	},
	listeners : {
		selectionchange : function (grid, selection) {
			//this.lookupReference('status')
			var status = Ext.getCmp('status'),
				message = '??',
				firstRowIndex,
				firstColumnIndex,
				lastRowIndex,
				lastColumnIndex;
	
			if (!selection) {
				message = 'No selection';
			}
			else if (selection.isCells) {
				firstRowIndex = selection.getFirstRowIndex();
				firstColumnIndex = selection.getFirstColumnIndex();
				lastRowIndex = selection.getLastRowIndex();
				lastColumnIndex = selection.getLastColumnIndex();
	
				message = 'Selected cells: ' + (lastColumnIndex - firstColumnIndex + 1) + 'x' + (lastRowIndex - firstRowIndex + 1) +
					' at (' + firstColumnIndex + ',' + firstRowIndex + ')';
			}
			else if (selection.isRows) {
				message = 'Selected rows: ' + selection.getCount();
			}
			else if (selection.isColumns) {
				message = 'Selected columns: ' + selection.getCount();
			}
	
			//status.update(message);
		},
		edit: function (editor, e, eOpts) {
			if(globalData.temp == null) {
				globalData.temp = [];
			}
			globalData.temp.push([editor.context.rowIdx, editor.context.field, editor.context.originalValue]);
		},
		afterrender: function(obj, opt) 
		{
		new Ext.util.KeyMap({
			target: document,
			binding: [
					{
						key: "z",
						ctrl:true,
						fn: function(){
							if(globalData.temp != null && globalData.temp.length > 0) {
							var store = obj.getStore();
							var temp = globalData.temp;
							var length = temp.length-1;
							
							//rowIdx, field, value 순으로 temp의 값을 store에 입력
							store.getData().getAt(temp[length][0]).set(temp[length][1],temp[length][2]);
							globalData.temp.pop(length);
							} else {
								return;
							}
						}
					},
					{
						key: "tab",
						fn: function(){
							alert(1);
						}
					}					
				],
				scope: this
			}); 
		}
	}
});


/* 팝업윈도우 > 발주입력폼에 쓰이는 에디터그리드 */
var grid_window_portfolio = Ext.create('Ext.grid.Panel',{
	id : 'grid_window_portfolio',
//	width : '100%',
	autoScroll : true,
//	frame: false,
//	autoWidth : true,
//	autoHeight: true,
//	flex : 1,
	store : store_window_portfolio,
	//plugins: [pg_CellEdit],
	dockedItems: [{
		dock: 'top',
		xtype: 'toolbar',
		items: [{
			tooltip: 'Toggle the visibility of the summary row',
			text: 'Toggle Summary',
			enableToggle: true,
			pressed: true,
			handler: function() {
				grid_window_portfolio.getView().getFeature('group').toggleSummaryRow();
			}
		}]
	}],
	features: [{
		id: 'group',
		ftype: 'groupingsummary',
		groupHeaderTpl: '{name}',
		hideGroupedHeader: true,
		enableGroupingMenu: false
	}],
	columns : [
		/*
		{ text : '공구코드',	dataIndex : 'gpcode',		hidden:true	},
		{ text : '공구명',		dataIndex : 'gpcode_name',	hidden:true,		width	:220 },
		{ text : '날짜',		dataIndex : 'od_date',		sortable: true,		renderer: Ext.util.Format.dateRenderer('Y-m-d'),	field: { xtype: 'datefield' },		hidden:true	},
//		{	header : '주문ID',		width:120,		sortable : true,	dataIndex : 'od_id'		},
//		{	header : '주문상태',	dataIndex : 'stats_name',		width:100	},
//		{	header : '상품코드',	dataIndex : 'it_id',			width:160	},
//		{	header : '품목명',		dataIndex : 'it_name',			width:450	},
		*/
		{	header : 'IMG',			dataIndex : 'gp_img',			width:60,			renderer: rendererImage			},		
		{
			text: '품목',
			flex: 1,
			tdCls: 'task',
			sortable: true,
			dataIndex: 'it_name',
			hideable: false,
			summaryType: 'count',
			summaryRenderer: function(value, summaryData, dataIndex) {
					return ( (value == 1 || !value ) ? '(1개의 품목)' : '(' + value + '개의 품목들)');
			}
		},
		{	header : 'Project',		width:180,		sortable: true,		dataIndex : 'project'	},
		{	
			header : '주문가',
			sortable: true,
			style:'text-align:center',
			align:'right',
			renderer: Ext.util.Format.numberRenderer('0,000'),
			summaryRenderer: Ext.util.Format.numberRenderer('0,000'),
			dataIndex : 'it_org_price'
		},
		{	
			header : '주문수량',
			sortable: true,
			style:'text-align:center',
			align:'right',
			renderer: Ext.util.Format.numberRenderer('0,000'),
			dataIndex : 'it_qty',
			summaryType : 'sum',
			summaryRenderer: Ext.util.Format.numberRenderer('0,000'),
			field: {
								xtype: 'numberfield'
						}
		},
		{
			header : '주문총액',
			sortable: false,
						groupable: false,
			dataIndex : 'total_price',
			style:'text-align:center',
			align:'right',
			renderer: Ext.util.Format.numberRenderer('0,000'),
			summaryType: function(records, values) {
				var i = 0,
					length = records.length,
					total = 0,
					record;

				for (; i < length; ++i) {
					record = records[i];
					total += record.get('it_org_price') * record.get('it_qty');
				}
				return total;
			},
			summaryRenderer: Ext.util.Format.numberRenderer('0,000')
		}
	],
	tbar : [
			{
				text	: '인쇄',
								iconCls	: 'icon-table_print',
								handler: function() {
									var sm = grid_vipMbList.getSelectionModel().getSelection()[0];
									Ext.ux.grid.Printer.mainTitle = sm.get('mb_nick')+'('+sm.get('mb_name')+')님의 발송목록';
										Ext.ux.grid.Printer.print(grid_window_portfolio);
								}
			},
	],
	listeners : {
		selectionchange: function(view, records) {
			var sm = grid_window_portfolio.getSelectionModel();
			//grid_window_portfolio.down('#delJaego').setDisabled(!view.store.getCount());
			//store_save_dtl
			//sm.select(0);
	 	},
		edit: function (editor, e, eOpts) {
			if(globalData.temp == null) {
				globalData.temp = [];
			}
			globalData.temp.push([editor.context.rowIdx, editor.context.field, editor.context.originalValue]);
		},
		afterrender: function(obj, opt) 
		{
		new Ext.util.KeyMap({
			target: document,
			binding: [
					{
						key: "z",
						ctrl:true,
						fn: function(){
							if(globalData.temp != null && globalData.temp.length > 0) {
							var store = obj.getStore();
							var temp = globalData.temp;
							var length = temp.length-1;
							
							//rowIdx, field, value 순으로 temp의 값을 store에 입력
							store.getData().getAt(temp[length][0]).set(temp[length][1],temp[length][2]);
							globalData.temp.pop(length);
							} else {
								return;
							}
						}
					}
				],
				scope: this
			});
		}
	}
});


/* 탭:포트폴리오 */


/** 상단 **********************************************************************************/

/* 상단 탑 예상투자규모 */
var grid_expectInvest = Ext.create('Ext.grid.Panel',{
	plugins: [Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1,errorSummary: false})],
	title: { cls:'center',	text:'예상 투자 규모'},
	width	: topGridWidth,
	height : topGridHeight,
	border: 0,
	frame : true,
	autoScroll: false,
	features: statsFeatures,
	viewConfig: statsViewConfig,
	margin: '0 20 0 0',
	style : 'float:left;',
	bodyStyle: 'overflow-x:hidden;overflow-y:hidden;',
	store : store_expectInvest,
	columns	: [
    	   { text : 'pf_id',		dataIndex: 'pf_id',				width: 140,		style:'text-align:center',		align:'center',	sortable:false,	hidden:true	},
			{ text : '분류',		dataIndex: 'fund_type',			width: 122,		style:'text-align:center',		align:'center',	sortable:false	},
			{ text : 'metal',		dataIndex: 'metal_type',		width: 80,		style:'text-align:center',		align:'center',	sortable:false,		summaryRenderer: rendererSummaryFormat	},
			{ text : '금액',		dataIndex: 'EXPECT_PRICE',		width: 180,		style:'text-align:center',		align:'right',		summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	renderer: rendererColumnFormat,	sortable:false },
			{ text : '비중',		dataIndex: 'EXPECT_PER',		width: 100,		style:'text-align:center',		align:'right',		summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	renderer: rendererColumnFormat,	sortable:false	}
			
	],
	listeners : statsDefaultListener
});

/* 상단 탑 초기구성자금 */
var grid_beginFundSet = Ext.create('Ext.grid.Panel',{
	plugins: [Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1,errorSummary: false})],
	title: { cls:'center',	text:'초기 구성 자금'},
	width	: topGridWidth,
	height : topGridHeight,
	border: 0,
	frame : true,
	autoScroll: false,
	features: statsFeatures,
	viewConfig: statsViewConfig,
	margin: '0 20 0 0',
	style : 'float:left;',
	bodyStyle: 'overflow-x:hidden;overflow-y:hidden;',
	store : store_beginFundSet,
	columns	: [
    	   { text : 'pf_id',		dataIndex: 'pf_id',				width: 140,		style:'text-align:center',		align:'center',	sortable:false,		hidden:true	},
			{ text : '분류',		dataIndex: 'fund_type',			width: 192,		style:'text-align:center',		align:'center',	sortable:false,		hidden:false },
			{ text : 'metal',		dataIndex: 'metal_type',		width: 80,		style:'text-align:center',		align:'center',	sortable:false,		summaryRenderer: rendererSummaryFormat	},
			{ text : '금액',		dataIndex: 'EXPECT_PRICE',		width: 130,		style:'text-align:center',		align:'right',		summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	renderer: rendererColumnFormat,	sortable:false },
			{ text : '비중',		dataIndex: 'EXPECT_PER',		width: 80,		style:'text-align:center',		align:'right',		summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	renderer: rendererColumnFormat,	sortable:false	}
	],
	listeners : statsDefaultListener
});

/* 상단 탑 추가매수자금 */
var grid_beginFundBuy = Ext.create('Ext.grid.Panel',{
	plugins: [Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1,errorSummary: false})],
	title: { cls:'center',	text:'추가 매수 자금'},
	width	: 444,
	height : topGridHeight,
	border: 0,
	frame : true,
	autoScroll: false,
	
	features: statsFeatures,
	viewConfig: statsViewConfig,
	style : 'float:right;',
	bodyStyle: 'overflow-x:hidden;overflow-y:hidden;',
	store : store_beginFundBuy,
	columns	: [
    	   { text : '분류',		dataIndex: 'fund_type',			width: 160,		style:'text-align:center',		align:'center',	sortable:false	},
			{ text : '기간',		dataIndex: 'surplus_year',		width: 130,		style:'text-align:center',		align:'center',	editor: { allowBlank : false },		sortable:false,		renderer: rendererColumnFormat },
			{ text : '금액',		dataIndex: 'surplus_fund',		width: 150,		style:'text-align:center',		align:'right',		editor: { allowBlank : false },		summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	renderer: rendererColumnFormat,	sortable:false }
	],
	listeners : statsDefaultListener
});



/** 중앙 **********************************************************************************/

/* 투자성향 설정 */
var grid_invest = Ext.create('Ext.grid.Panel',{
	plugins: [Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1,errorSummary: false})],
	width	: midGridWidth,
	height : midGridHeight,
	border: 0,
	autoScroll: false,
	features: [
					{
						id : 'group',
						ftype : 'groupingsummary',
						groupHeaderTpl: '{name}',
						hideGroupedHeader: true,
            		enableGroupingMenu: false,
            		collapsible : false
					}	           
	],
	viewConfig: {
		stripeRows: true,
		forceFit: true,
		getRowClass: function(record, index) {
			return 'invest_row';
		}
	},
	style : 'float:left;',
	bodyStyle: 'overflow-x:hidden;overflow-y:hidden;',
	store : store_invest,
	columns	: [
    	   { text : 'pf_id',			dataIndex: 'pf_id',				width: 140,		style:'text-align:center',		align:'center',	hidden:true,	sortable:false	},
			{ text : '성향',			dataIndex: 'invest_type',		width: 130,		style:'text-align:center',		align:'center',	sortable:false	},
			{ text : '목표 %',		dataIndex: 'target_per',		width: 100,		style:'text-align:center',		align:'right',	summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	renderer: rendererColumnFormat,	editor: { allowBlank : false },	sortable:false },
			{ text : '목표금액 ￦',	dataIndex: 'TARGET_PRICE',		width: 130,		style:'text-align:center',		align:'right',	summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	renderer: Ext.util.Format.numberRenderer('0,000'),	sortable:false	},
	],
	listeners : {
		selectionchange: function(view, records) {
	 	},
	 	change : function(field, newValue,o ,e) {
	 		var sm = grid_vipMbList.getSelectionModel().getSelection()[0];
			loadPFtab1(sm);
			loadPFtab2(sm);
			loadPFtab3(sm);
	 	},
		edit: function (editor, e, eOpts) {
			if(globalData.temp == null) {
				globalData.temp = [];
			}
			globalData.temp.push([editor.context.rowIdx, editor.context.field, editor.context.originalValue]);
		},
		afterrender: function(obj, opt) 
		{
			new Ext.util.KeyMap({
				target: document,
				binding: [
						{
							key: "z",
							ctrl:true,
							fn: function(){
								if(globalData.temp != null && globalData.temp.length > 0) {
								var store = obj.getStore();
								var temp = globalData.temp;
								var length = temp.length-1;
								
								//rowIdx, field, value 순으로 temp의 값을 store에 입력
								store.getData().getAt(temp[length][0]).set(temp[length][1],temp[length][2]);
								globalData.temp.pop(length);
								} else {
									return;
								}
							}
						}
				],
				scope: this
			}); 
		}
	}
});


/* 투자성향 설정 */
var grid_achInvest = Ext.create('Ext.grid.Panel',{
	width	: midGridWidth,
	height : midGridHeight,
	border: 0,
	features: [
					{
						id : 'group',
						ftype : 'groupingsummary',
						groupHeaderTpl: '{name}',
						hideGroupedHeader: true,
            		enableGroupingMenu: false,
            		collapsible : false
					}	           
	],
	viewConfig: {
		stripeRows: true,
		getRowClass: function(record, index) {
			return 'invest_row';
		}
	},
	style : 'float:left;',
	store : store_achInvest,
	columns	: [
    	   { text : 'pf_id',			dataIndex: 'pf_id',				width: 140,		style:'text-align:center',		align:'center',	hidden:true,	sortable:false	},
			{ text : '성향',			dataIndex: 'invest_type',		width: 130,		style:'text-align:center',		align:'center',	sortable:false	},
			{ text : '목표 %',		dataIndex: 'ACH_PER',			width: 100,		style:'text-align:center',		align:'right',		summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	renderer: rendererColumnFormat,	sortable:false },
			{ text : '목표금액 ￦',	dataIndex: 'ACH_PRICE',			width: 130,		style:'text-align:center',		align:'right',		summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	renderer: Ext.util.Format.numberRenderer('0,000'),	sortable:false	},
	],
	listeners : defaultListener
});



/* 투자성향 설정 */
var grid_investdtl = Ext.create('Ext.grid.Panel',{
	plugins: [Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1,errorSummary: false})],
	width	: midGridWidth,
	height : midGridHeight-4,
	border: 0,
	features: [
					{
						ftype : 'groupingsummary',
						groupHeaderTpl: '{name}',
						hideGroupedHeader: true,
            		enableGroupingMenu: false,
            		collapsible : false
					},{
						ftype: 'summary',
						dock: 'bottom'
					}	           
	],
	viewConfig: {
		stripeRows: true,
		getRowClass: function(record, index) {
			//return 'invest_row';
		}
	},
	style : 'float:left;',
	store : store_investdtl,
	columns	: [
    	   { text : 'pf_id',			dataIndex: 'pf_id',				width: 140,		style:'text-align:center',		align:'center',	hidden:true,	sortable:false	},
			{ text : '성향',			dataIndex: 'invest_type',		width: 130,		style:'text-align:center',		align:'center',	sortable:false   },
			{ text : '금속',			dataIndex: 'metal_type',		width: 100,		style:'text-align:center',		align:'center',	summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	sortable:false   },
			{ text : '포지션 %',		dataIndex: 'target_per',		width: 100,		style:'text-align:center',		align:'right',	renderer: rendererColumnFormat,	summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	editor: { allowBlank : false },	sortable:false },
			{ text : '포지션 ￦',	dataIndex: 'TARGET_PRICE',		width: 158,		style:'text-align:center',		align:'right',	renderer: rendererColumnFormat,	summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	sortable:false	},
	],
	listeners : defaultListener
});


/* 투자성향 설정 */
var grid_achInvestdtl = Ext.create('Ext.grid.Panel',{
	plugins: [Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1,errorSummary: false})],
	width	: midGridWidth,
	height : midGridHeight-4,
	border: 0,
	features: [
					{
						ftype : 'groupingsummary',
						groupHeaderTpl: '{name}',
						hideGroupedHeader: true,
            		enableGroupingMenu: false,
            		collapsible : false
					},{
						ftype: 'summary',
						dock: 'bottom'
					}	           
	],
	viewConfig: {
		stripeRows: true,
		getRowClass: function(record, index) {
			//return 'invest_row';
		}
	},
	style : 'float:left;',
	store : store_achInvestdtl,
	columns	: [
    	   { text : 'pf_id',			dataIndex: 'pf_id',				width: 140,		style:'text-align:center',		align:'center',	hidden:true,	sortable:false	},
			{ text : '성향',			dataIndex: 'invest_type',		width: 130,		style:'text-align:center',		align:'center',	sortable:false   },
			{ text : '금속',			dataIndex: 'metal_type',		width: 100,		style:'text-align:center',		align:'center',	summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	sortable:false   },
			{ text : '포지션 %',		dataIndex: 'ACH_PER',			width: 100,		style:'text-align:center',		align:'right',	renderer: rendererColumnFormat,	summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	editor: { allowBlank : false },	sortable:false },
			{ text : '포지션 ￦',	dataIndex: 'ACH_PRICE',			width: 158,		style:'text-align:center',		align:'right',	renderer: rendererColumnFormat,	summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	sortable:false	},
	],
	listeners : defaultListener
});

/** 하단 **********************************************************************************/

/* 하단 자금 평가 금액 */
var grid_estimate = Ext.create('Ext.grid.Panel',{
	plugins: [Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1,errorSummary: false})],
	title: { cls:'center',	text:'추가 매수 자금'},
	width	: botGridWidth,
	height : botGridHeight,
	border: 0,
	frame : true,
	autoScroll: false,
	features: statsFeatures,
	viewConfig: statsViewConfig,
	style : 'float:left;',
	bodyStyle: 'overflow-x:hidden;overflow-y:hidden;',
	store : store_estimate,
	columns	: [
    	   { text : '금속',					dataIndex: 'metal_type',				width: 100,		style:'text-align:center',		align:'center',	sortable:false	},
    	   { text : '중량',					dataIndex: 'TOTAL_GRAM',				width: 130,		style:'text-align:center',		align:'right',		summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	renderer: rendererColumnFormat,	sortable:false },
			{ text : '현재 평가 금액',		dataIndex: 'ESTIMATE_NOW',				width: 130,		style:'text-align:center',		align:'right',		summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	renderer: rendererColumnFormat,	sortable:false },
			{ text : '구매 평가 금액',		dataIndex: 'ESTIMATE_BUYED',			width: 150,		style:'text-align:center',		align:'right',		summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	renderer: rendererColumnFormat,	sortable:false },
			{ text : '손익 금액',			dataIndex: 'ESTIMATE_PROFIT',			width: 130,		style:'text-align:center',		align:'right',		summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	renderer: rendererColumnFormat,	sortable:false },
			{ text : '손익%',					dataIndex: 'ESTIMATE_PROFIT_PER',	width: 110,		style:'text-align:center',		align:'right',		summaryType: 'sum',	summaryRenderer: rendererSummaryFormat,	renderer: rendererColumnFormat,	sortable:false },
			{ text : '현재 금은시세(1돈)',dataIndex: 'flowprice_now',			width: 150,		style:'text-align:center',		align:'right',		renderer: rendererColumnFormat,	sortable:false },
			{ text : '구매 평균시세(1돈)',dataIndex: 'flowprice_buyed',			width: 150,		style:'text-align:center',		align:'right',		renderer: rendererColumnFormat,	sortable:false },
			{ text : '평단가 손익',			dataIndex: 'flowprice_profit',		width: 150,		style:'text-align:center',		align:'right',		renderer: rendererColumnFormat,	sortable:false },
			{ text : '평단가 손익(%)',		dataIndex: 'flowprice_profit_per',	width: 130,		style:'text-align:center',		align:'right',		renderer: rendererColumnFormat,	sortable:false }
	],
	listeners : statsDefaultListener
});


/************* ----------------	그리드 END -------------- ******************/