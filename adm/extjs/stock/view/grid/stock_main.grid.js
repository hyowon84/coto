
var selModel = {
	type: 'spreadsheet'
	,columnSelect: true	// replaces click-to-sort on header
};

var combo_gp_stats = Ext.create('Ext.combobox.item.gpstats');
combo_gp_stats.id = 'combo_gp_stats';	combo_gp_stats.setValue('');	combo_gp_stats.width = 90;

/*송금탭*/
var combo_wr_stats = Ext.create('Ext.combobox.item.ivstats');
combo_wr_stats.id = 'combo_wr_stats';	combo_wr_stats.setValue('');	combo_wr_stats.width = 90;

/*통관탭*/
var combo_cr_stats = Ext.create('Ext.combobox.item.ivstats');
combo_cr_stats.id = 'combo_cr_stats';	combo_cr_stats.setValue('');	combo_cr_stats.width = 90;


/************* ----------------  상단 네비패널에서 사용하는 그리드 START -------------- ******************/


/* 좌측상단 송금예정 발주서 목록 */
var grid_navi_invoice = Ext.create('Ext.grid.Panel',{
	id : 'grid_navi_invoice',
	plugins: [Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1})],
	selModel: Ext.create('Ext.selection.CheckboxModel', {}),
	viewConfig: {
		stripeRows: true,
		enableTextSelection: true
	},
	autoLoad : false,
	autoWidth : true,
	height : 560,
	store : store_navi_invoice,
	columns : [
		{ text : '날짜',							dataIndex : 'iv_date',					sortable: true,	summaryType: 'max',		renderer: Ext.util.Format.dateRenderer('Y-m-d'),	summaryRenderer: Ext.util.Format.dateRenderer('Y-m-d'),		field: { xtype: 'datefield' },		hidden:true	},
		{ text : '공구코드',					dataIndex : 'gpcode',						width:230,			hidden:true	},
		{ text : '송금코드',					dataIndex : 'wr_id',						width:130,			hidden:true	},
		{ text : '발주코드',					dataIndex : 'iv_id',						width:120	},
		{ text : '발주서 별칭',			dataIndex : 'iv_name',					width:150	},
		{ text : '인보이스번호',			dataIndex : 'iv_order_no',			width:120	},
		{ text : '송금내역CNT',			dataIndex : 'CNT_WIRE',					width:120,		style:'text-align:center',		align:'right'	},
		{ text : '통관내역CNT',			dataIndex : 'CNT_CLR',					width:120,		style:'text-align:center',		align:'right'	},
		{ text : '입고품목CNT',			dataIndex : 'CNT_INP',					width:120,		style:'text-align:center',		align:'right'	},
		{ text : '메모',							dataIndex : 'iv_memo',					width:170,		editor: { allowBlank : false }	},
		{ text : 'TOTAL',						dataIndex : 'TOTAL_PRICE',			width:150,		style:'text-align:center',		align:'right',		renderer: Ext.util.Format.numberRenderer('0,000.00') },
		{ text : 'DC.FEE',					dataIndex : 'iv_discountfee',		width:100,		style:'text-align:center',		align:'right',		renderer: Ext.util.Format.numberRenderer('0,000.00'),		editor: { allowBlank : false } },
		{ text : 'TAX',							dataIndex : 'iv_tax',						width:100,		style:'text-align:center',		align:'right',		renderer: Ext.util.Format.numberRenderer('0,000.00'),		editor: { allowBlank : false } },
		{ text : 'SHIP.FEE',				dataIndex : 'iv_shippingfee',		width:120,		style:'text-align:center',		align:'right',		renderer: Ext.util.Format.numberRenderer('0,000.00'),		editor: { allowBlank : false } },
		{ text : '딜러',							dataIndex : 'iv_dealer',				width:120	},
		{ text : '인보이스날짜',			dataIndex : 'iv_date',					width:120	},
		{
			xtype: 'gridcolumn',
			dataIndex: 'money_type',
			text: '통화',
			style:'text-align:center',
			align:'center',
			allowBlank: true,
			editor: Ext.create('Ext.combobox.item.moneytype'),
			value : 'USD',
			renderer: rendererCombo
		},
		{ text : '환율(주문)',				dataIndex : 'wr_exch_rate',			width:90,			editor: { allowBlank : false }	},
		{ text : '담당자',						dataIndex : 'admin_name',				width:120	},
		{ text : '입출금링크',				dataIndex : 'iv_receipt_link',	width:120	}
	],
	dockedItems: [],
	tbar : [
		{	xtype: 'label',	text: '검색어 : ',		autoWidth:true,	style : 'font-weight:bold;'},
		{
			xtype: 'textfield',
			id : 'keyword_navi',
			name: 'keyword_navi',
			style: 'padding:0px;',
			enableKeyEvents: true,
			listeners:{
				keydown:function(t,e){
					if(e.keyCode == 13){

						var v_param = {
							keyword : Ext.getCmp('keyword_navi').getValue()
						}

						grid_navi_invoice.store.loadData([],false);
						Ext.apply(grid_navi_invoice.store.getProxy().extraParams, v_param);
						grid_navi_invoice.store.load();
					}
				}
			}
		}
	],
	bbar : {
		plugins: new Ext.ux.SlidingPager(),
		xtype : 'pagingtoolbar',
		store : store_navi_invoice,
		displayInfo : true,
		displayMsg : '{0}/{1} Total - {2}',
		emptyMsg : 'No Data'
	},
	listeners : {
		selectionchange: function(view, records) {
			//송금관련 품목 초기화
			grid_navi_invoice_dtl.store.loadData([],false);
			
			var sm = grid_navi_invoice.getSelectionModel().getSelection();

			if(sm[0]) {

				var v_gpcode = '';
				var v_iv_id = '';

				for(var i = 0; i < sm.length; i++) {	//sm[i].data

					//  ','단위로 분할한 공구코드 다시 합치기
					var v_arr = sm[i].data.gpcode.split(',');
					for(var a = 0; a < v_arr.length; a++) {
						v_gpcode += "'"+v_arr[a] + "',";
					}

					v_iv_id += "'"+sm[i].data.iv_id + "',";
				}

				v_gpcode = v_gpcode.substr(0,v_gpcode.length-1);
				v_iv_id = v_iv_id.substr(0,v_iv_id.length-1);

				//v_keyword = Ext.getCmp('orderitems_keyword').getValue();
				var v_param = { 'gpcode' : v_gpcode,		'iv_id' : v_iv_id };

				//발주관련 품목 로딩
				Ext.apply(grid_navi_invoice_dtl.store.getProxy().extraParams, v_param);
				grid_navi_invoice_dtl.store.load();
			}
		},
		edit: listenerEditFunc,
		afterrender: listenerAfterRendererFunc
	}//listeners end
});


/* 상단 네비검색영역 발주서 관련 품목들 */
var grid_navi_invoice_dtl = Ext.create('Ext.grid.Panel',{
	id : 'grid_navi_invoice_dtl',
	plugins: [Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1})],
	selModel: Ext.create('Ext.selection.CheckboxModel', {}),
	features: [
		{
			ftype : 'groupingsummary',
			groupHeaderTpl: '{name}',
			hideGroupedHeader: true,
			enableGroupingMenu: true,
			collapsible : false
		}
	],
	viewConfig: {
		stripeRows: true,
		enableTextSelection: true,
		getRowClass: ivStatsColorSetting
	},
	autoWidth : true,
	height : 560,
	store : store_navi_invoice_dtl,
	columns : [
		{ text : 'number',			dataIndex : 'number',						hidden:true	},
		{ text : '발주코드',			dataIndex : 'iv_id',						width:120		},
		{
			xtype: 'gridcolumn',
			dataIndex: 'iv_stats',
			text: '현황',
			style:'text-align:center',
			align:'center',
			allowBlank: true,
			editor: Ext.create('Ext.combobox.item.ivstats'),
			value : '00',
			renderer: rendererCombo
		},
		{ text : '날짜',					dataIndex : 'reg_date',							sortable: true,		summaryType: 'max',		renderer: Ext.util.Format.dateRenderer('Y-m-d'),	summaryRenderer: Ext.util.Format.dateRenderer('Y-m-d'),		field: { xtype: 'datefield' },		hidden:true	},
		{ text : '공구코드',			dataIndex : 'gpcode',								hidden:true		},
		{ text : '공구명',				dataIndex : 'gpcode_name'	},
		{ text : 'IMG', 					dataIndex : 'iv_it_img',						width: 50,		renderer:rendererImage 		},
		{ text : '상품코드',			dataIndex : 'iv_it_id',							width:160			},
		{ text : '분류',					dataIndex : 'ca_id',								width:100,		style:'text-align:center'		},
		{ text : '재고값',				dataIndex : 'jaego',								width:100,		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000')			},
		{ text : '주문집계',			dataIndex : 'GPT_QTY',							width:100,		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000'),	summaryType : 'sum',	summaryRenderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '발주수량',			dataIndex : 'iv_qty',								width:100,		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000'),	summaryType : 'sum',	summaryRenderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '입고수량',			dataIndex : 'ip_qty',								width:100,		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000'),	summaryType : 'sum',	summaryRenderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '통화',					dataIndex : 'money_type',						width:70			},
		{ text : '발주가',				dataIndex : 'iv_dealer_worldprice',	width:120,		editor: { allowBlank : false },		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000.00') },
		{ text : 'TOTAL',					dataIndex : 'total_price',					width:120,																			style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000.00') },
		{ text : '품목명',				dataIndex : 'iv_it_name',						width:450			}
	],
	tbar : [
		/*combo_wr_stats,
		{
			id		: 'iv_stats_update',
			text	: '변경',
			iconCls	: 'icon-table_edit',
			handler : function() {
				var sm = grid_navi_invoice_dtl.getSelection();
				if( sm == '' ) {
					Ext.Msg.alert('알림','품목들을 선택해주세요');
					return false;
				}

				var iv_stats = combo_wr_stats.getValue();

				for(var i = 0; i < sm.length; i++) {
					sm[i].set('iv_stats',iv_stats);
				}

				grid_navi_invoice_dtl.store.update();
			}
		},
		*/
		{
			text	: '인쇄',
			iconCls	: 'icon-table_print',
			handler: function() {
				Ext.ux.grid.Printer.mainTitle = Ext.util.Format.date(new Date(),'Y-m-d g:i:s') +' 발주목록';
				Ext.ux.grid.Printer.print(grid_navi_invoice_dtl);
			}
		}
	],
	tbar : [
		{	xtype: 'label',	text: '검색어 : ',		autoWidth:true,	style : 'font-weight:bold;'},
		{
			xtype: 'textfield',
			id : 'navi_ivdtl_keyword',
			name: 'keyword',
			style: 'padding:0px;',
			enableKeyEvents: true,
			listeners:{
				keydown:function(t,e) {
					if(e.keyCode == 13) {
						store_navi_invoice_dtl.removeAll();

						var v_param = {
							keyword : Ext.getCmp('navi_ivdtl_keyword').getValue()
						}

						store_navi_invoice_dtl.loadData([],false);
						Ext.apply(store_navi_invoice_dtl.getProxy().extraParams, v_param);
						store_navi_invoice_dtl.load();
					}
				}
			}
		}
	],
	bbar : {
		plugins: new Ext.ux.SlidingPager(),
		xtype : 'pagingtoolbar',
		store : store_navi_invoice_dtl,
		displayInfo : true,
		displayMsg : '{0}/{1} Total - {2}',
		emptyMsg : 'No Data'
	},
	listeners : {
		selectionchange: function(view, records) {
		},
		edit: listenerEditFunc,
		afterrender: listenerAfterRendererFunc
	}
});

/************* ----------------  상단 네비패널에서 사용하는 그리드 END -------------- ******************/



/************* ----------------  하단 탭패널에서 사용하는 그리드 START -------------- ******************/
/* 좌측 공구코드 */
var grid_gpinfo = Ext.create('Ext.grid.Panel',{
	plugins	: Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1}),
	selModel: Ext.create('Ext.selection.CheckboxModel'),
	remoteSort: true,
	autoLoad : false,
	autoWidth : true,
	height : 770,
	viewConfig: {
		stripeRows: true,
		getRowClass: gpinfoStatsColorSetting,
		enableTextSelection: true
	},
	columns : [
		{ text : '공구코드',		width : 120,	dataIndex : 'gpcode',				hidden:true	},
		{ text : '날짜',				width : 120,	dataIndex : 'reg_date',			hidden:true	},
		{ text : '공구명', 		width : 250,	dataIndex : 'gpcode_name',	sortable: false	},
		{
			xtype: 'gridcolumn',
			dataIndex: 'stats',
			text: '현황',
			style:'text-align:center',
			align:'center',
			allowBlank: true,
			editor: Ext.create('Ext.combobox.item.gpstats'),
			value : '00',
			renderer: rendererCombo
		},
		{ text : '발주',				width : 70,		dataIndex : 'SUM_IV_QTY',		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '주문',				width : 70,		dataIndex : 'SUM_QTY',			style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '미발주',			width : 80,		dataIndex : 'NEED_IV_QTY',	style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '주문총액',		width : 120,	dataIndex : 'SUM_PAY',			style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '메모',				width : 120,	dataIndex : 'memo'	},
		{ text : '품목(GP)',		width : 90,		dataIndex : 'ITC_CNT',			style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '품목(IV)',		width : 90,		dataIndex : 'IVC_CNT',			style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : ' ',					width : 50,		dataIndex : 'NULL'	}
	],
	store : store_gpinfo,
	tbar: [
		{	xtype: 'label',	text: '검색어 : ',		autoWidth:true,	style : 'font-weight:bold;'},
		{
			xtype: 'textfield',
			id : 'keyword',
			name: 'keyword',
			style: 'padding:0px;',
			enableKeyEvents: true,
			listeners:{
				keydown:function(t,e){
					if(e.keyCode == 13){

						var v_param = {
							keyword : Ext.getCmp('keyword').getValue()
						}

						grid_gpinfo.store.loadData([],false);
						Ext.apply(grid_gpinfo.store.getProxy().extraParams, v_param);
						grid_gpinfo.store.load();
					}
				}
			}
		},
		{
			id: 'unselect',
			text: '선택해제',
			iconCls: '',
			handler: function () {
				var sm = grid_gpinfo.getSelectionModel();
				sm.deselectAll();

				store_orderitems.getProxy().extraParams = null;
				store_orderitems.load();
			}
		}
	],
	bbar : {
		plugins: new Ext.ux.SlidingPager(),
		xtype : 'pagingtoolbar',
		store : store_gpinfo,
		displayInfo : true,
		displayMsg : '{0}/{1} Total - {2}',
		emptyMsg : 'No Data'
	},
	listeners : {
		selectionchange: function(view, records) {
			store_orderitems.loadData([],false);
			store_memo_gpinfo.loadData([],false);

			/* 공구목록의 선택된 레코드 */
			var sm = grid_gpinfo.getSelectionModel().getSelection()[0];

			/* 1개 이상 선택된게 있을경우 */
			if(sm) {
				storeTempInvoice.removeAll();

				var sm = grid_gpinfo.getSelection();

				if( sm == '' ) {
					Ext.Msg.alert('알림','상품들을 선택해주세요');
					return false;
				}

				var v_gpcode = '';
				var v_title = '';

				for(var i = 0; i < sm.length; i++) {	//sm[i].data
					v_gpcode += "'"+sm[i].data.gpcode + "',";
					v_title += sm[i].data.gpcode_name + ',';
				}

				v_gpcode = v_gpcode.substr(0,v_gpcode.length-1);
				v_title = v_title.substr(0,v_title.length-1);

				v_keyword = Ext.getCmp('orderitems_keyword').getValue();
				var v_param = { 'gpcode' : v_gpcode,
												'keyword' : v_keyword };

				/*공구별 참고사항 로딩*/
				Ext.apply(store_memo_gpinfo.getProxy().extraParams, v_param);
				Ext.apply(store_orderitems.getProxy().extraParams, v_param);
				Ext.getCmp('ptb_orderitems').moveFirst();
				store_orderitems.loadData([],false);

				/* >>발주예상 품목 로딩, 패러미터는 오브젝트로 전달 */
				store_memo_gpinfo.load();
				store_orderitems.load();
				
			} else {

				Ext.getCmp('WIRE_WEST').setTitle('>> "'+'" 발주서 목록');

			}
	 	},
		edit: listenerEditFunc,
		afterrender: listenerAfterRendererFunc
	}
});



/*우측 상단 공구별 참고사항 */
var grid_memo_gpinfo = Ext.create('Ext.grid.Panel', {
	id: 'grid_memo_gpinfo',
	headerPosition: 'left',
	title : '<b>공구별 참고사항</b>',
	plugins: Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1}),
	autoWidth: true,
	autoLoad : false,
	height: 130,
	store: store_memo_gpinfo,
	columns: [
		{ text : '공구명', 			width : 210,	dataIndex : 'gpcode_name',		sortable: false	},
		{ text : '공구코드',			width : 120,	dataIndex : 'gpcode',					hidden:true	},
		{ text : '날짜',					width : 120,	dataIndex : 'reg_date',				hidden:true	},
		{	text : '메모',					width : 620,	dataIndex : 'memo'					},
		{	text : '발주관련메모',	width : 620,	dataIndex : 'invoice_memo'	},		
		{ text : ' ',						width : 50,		dataIndex : 'NULL' }
	],
	listeners : {
		itemdblclick: {
			/**
				* @grid        그리드 오브젝트
				* @selRow      선택한 셀의 오브젝트
				* @selHtml     선택한 셀의  html
				*
				* 기본적으로 Ext.define에서 idProperty로 선언한 field가 internalId로 설정된다.
				*  그 외 데이터는 selRow.data.{field}로 접근할 수 있다.
			*/
			fn: function(grid, selRow, selHtml){
				//grid_memo_gpinfo.getStore()

				if (winMemoGpinfo.isVisible()) {
					winMemoGpinfo.hide(this, function() {

					});
				} else {
					winMemoGpinfo.show(this, function() {
						var sm = grid_memo_gpinfo.getSelectionModel().getSelection()[0];

						Ext.getCmp('winMemoGpinfo').setTitle(sm.data.gpcode+'|"'+sm.data.gpcode_name+'" 메모수정');

						sm.data.invoice_memo = sm.data.invoice_memo.replace(/<br>/gi, "\r\n");	//개행문자를 <BR>로 변경한걸 다시 원상복구
						sm.data.memo = sm.data.memo.replace(/<br>/gi, "\r\n");
						Ext.getCmp('winMemoGpinfoForm').loadRecord(sm);

					});
				}
			}
		},
		selectionchange: function(view, records) {
		},
		edit: listenerEditFunc,
		afterrender: listenerAfterRendererFunc
	}
});


/* 우측 하단 통계정보 */
var grid_orderitems = Ext.create('Ext.grid.Panel',{
	headerPosition: 'left',
	title : '<b>주문신청내역</b>',
	plugins	: Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1}),
	height : 634,
	resizable: true,
	selModel: Ext.create('Ext.selection.CheckboxModel'),
	viewConfig: {
		getRowClass: function(record, index) {
			var c = record.get('NEED_IV_QTY');
			if (c == 0) {
				return 'cell_font_blue';
			}
		},
		stripeRows: true,
		enableTextSelection: true
	},
	store : store_orderitems,
	columns : [
		{ text : '날짜',							dataIndex : 'reg_date',							sortable: true,	summaryType: 'max',		renderer: Ext.util.Format.dateRenderer('Y-m-d'),	summaryRenderer: Ext.util.Format.dateRenderer('Y-m-d'),		field: { xtype: 'datefield' },		hidden:true	},
		{ text : '관리자메모',				dataIndex : 'admin_memo',						width:150,			editor: { allowBlank : false }	},
		{ text : '공구코드',					dataIndex : 'gpcode',								hidden:true	},
		{ text : '공구명',						dataIndex : 'gpcode_name',					width:160		},
		{ text : '상품코드',					dataIndex : 'it_id',								width:160		},
		{ text : '▼주문집계',				dataIndex : 'SUM_QTY',							width:120,			style:'text-align:center',	align:'right',	editor: { allowBlank : false },		renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '△발주필요',				dataIndex : 'NEED_IV_QTY',					width:120,			style:'text-align:center',	align:'right'		},
		{ text : '▲발주완료',				dataIndex : 'SUM_IV_QTY',						width:120,			style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '과발주',						dataIndex : 'OVER_IV_QTY',					style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '│',								dataIndex : 'NULL',									width : 20 },
		{ text : icn_e1+"공구재고설정",	dataIndex : 'gp_jaego',								width:140,			style:'text-align:center',	align:'right',	editor: { allowBlank : false },	cls: 'font_edit'	},
		{ text : '초기재고값',				dataIndex : 'jaego',								width:120,			style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '전체발주수량',			dataIndex : 'RIV_QTY',							width:120,			style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '전체주문수량',			dataIndex : 'ORDER_QTY',						width:120,			style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '예상재고수량',			dataIndex : 'real_jaego',						width:120,			style:'text-align:center',	align:'right'		},
		
		{ text : '발주총액',					dataIndex : 'SUM_IV_WORLDPRICE',		width:100,			style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000.00') },
		{ text : 'IMG', 						dataIndex : 'it_img',								width: 50,			renderer:rendererImage 	},
		{ text : '품목명',						dataIndex : 'it_name',							width:450 },
		{ text : '주문총액',					dataIndex : 'total_price',					style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000'),	hidden:true },
		{ text : '주문가',						dataIndex : 'it_org_price',					style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000'),	hidden:true },
		{ text : '│',								dataIndex : 'NULL',									width : 40 }
	],
	tbar : [
		{	xtype: 'label',	text: '검색어 : ',		autoWidth:true,	style : 'font-weight:bold;'},
		{
			xtype: 'textfield',
			id : 'orderitems_keyword',
			name: 'keyword',
			style: 'padding:0px;',
			enableKeyEvents: true,
			listeners:{
				keydown:function(t,e) {
					if(e.keyCode == 13) {

						var sm = grid_gpinfo.getSelection();

						storeTempInvoice.removeAll();

						var v_gpcode = '';
						var v_title = '';

						for(var i = 0; i < sm.length; i++) {	//sm[i].data
							v_gpcode += "'"+sm[i].data.gpcode + "',";
						}
						v_gpcode = v_gpcode.substr(0,v_gpcode.length-1);

						var v_param = {
							gpcode : v_gpcode,
							keyword : Ext.getCmp('orderitems_keyword').getValue()
						}

						grid_orderitems.store.loadData([],false);
						Ext.apply(grid_orderitems.store.getProxy().extraParams, v_param);
						grid_orderitems.store.load();
					}
				}
			}
		},
		{	xtype: 'label',	text: '재고 : ',		autoWidth:true,	style : 'font-weight:bold;'},
		{
			xtype: 'textfield',
			id: 'orderitems_jaego',
			name: 'keyword',
			width : 50,
			style: 'padding:0px;',
			enableKeyEvents: true
		},
		{
			id: 'btn_jaego_edit',
			text: '변경',
			iconCls: 'icon-table_edit2',
			handler: function () {

				if( grid_gpinfo.getSelectionModel().getSelection() == '' ) {
					Ext.Msg.alert('알림','좌측 공동구매 항목을 선택하세요');
					return false;
				}

				/*선택된 품목들 */
				var sm = grid_orderitems.getSelection();
				if( sm == '' ) {
					Ext.Msg.alert('알림','상품들을 선택해주세요');
					return false;
				}

				var v_jaego = Ext.getCmp('orderitems_jaego').getValue();

				for(var i = 0; i < sm.length; i++) {
					sm[i].set('jaego',v_jaego);
				}

				//grid_orderitems.store.update();
			}
		},
		{
			id		: 'btn_invoice',
			text	: '발주작성',
			iconCls	: 'icon-table_edit',
			handler : function() {
				storeTempInvoice.removeAll();

				if( grid_gpinfo.getSelectionModel().getSelection() == '' ) {
					Ext.Msg.alert('알림','좌측 공동구매 항목을 선택하세요');
					return false;
				}

				/*선택된 품목들 */
				var sm = grid_orderitems.getSelection();
				if( sm == '' ) {
					Ext.Msg.alert('알림','상품들을 선택해주세요');
					return false;
				}

				for(var i = 0; i < sm.length; i++) {
					var rec = Ext.create('model.invoice_detail', {
									'gpcode'					: sm[i].data.gpcode,
									'iv_it_id'				: sm[i].data.it_id,
									'iv_it_name'			: sm[i].data.it_name,
									'iv_dealer_price'	: sm[i].data.it_org_price,
									'total_price'			: sm[i].data.total_price,
									'iv_qty'					: sm[i].data.NEED_IV_QTY
					});
					storeTempInvoice.add(rec);
				}

				var v_gpcode = '';
				var v_gpcode_name = '';
				var sm = grid_gpinfo.getSelection();
				
				for(var i = 0; i < sm.length; i++) {	//sm[i].data
					v_gpcode += sm[i].data.gpcode+',';
					v_gpcode_name += sm[i].data.gpcode_name+',';
				}
				
				v_gpcode = v_gpcode.substr(0,v_gpcode.length-1)
				v_gpcode_name = v_gpcode_name.substr(0,v_gpcode_name.length-1);


				var gpinfo_sm = grid_gpinfo.getSelectionModel().getSelection()[0];
				gpinfo_sm.data.gpcode = v_gpcode;
				gpinfo_sm.data.iv_name = v_gpcode_name;
				Ext.getCmp('ivFormPanel').loadRecord(gpinfo_sm);

				winInvoice.setTitle(v_gpcode_name+'"공구건에 대한 발주 입력폼');

				var button = Ext.get('btn_invoice');
				button.dom.disabled = true;
				//this.container.dom.style.visibility=true
				
				if (winInvoice.isVisible()) {
					winInvoice.hide(this, function() {
						button.dom.disabled = false;
					});
				} else {
					winInvoice.show(this, function() {
						button.dom.disabled = false;
					});
				}
				
				grid_window_invoice.reconfigure(storeTempInvoice);
			}
		},
		{
			id		: 'btn_invoice_add',
			text	: '발주추가',
			iconCls	: 'icon-add',
			handler : function() {

				if( grid_gpinfo.getSelectionModel().getSelection() == '' ) {
					Ext.Msg.alert('알림','좌측 공동구매 항목을 선택하세요');
					return false;
				}

				/*선택된 품목들 */
				var sm = grid_orderitems.getSelection();
				if( sm == '' ) {
					Ext.Msg.alert('알림','상품들을 선택해주세요');
					return false;
				}

				for(var i = 0; i < sm.length; i++) {
					var rec = Ext.create('model.invoice_detail', {
						'gpcode'					: sm[i].data.gpcode,
						'iv_it_id'				: sm[i].data.it_id,
						'iv_it_name'			: sm[i].data.it_name,
						'iv_dealer_price'	: sm[i].data.it_org_price,
						'total_price'			: sm[i].data.total_price,
						'iv_qty'					: sm[i].data.NEED_IV_QTY
					});
					storeTempInvoice.add(rec);
				}
				//
				//var v_gpcode = '';
				//var v_gpcode_name = '';
				//var sm = grid_gpinfo.getSelection();
				//
				//for(var i = 0; i < sm.length; i++) {	//sm[i].data
				//	v_gpcode += sm[i].data.gpcode+',';
				//	v_gpcode_name += sm[i].data.gpcode_name+',';
				//}
				//
				//v_gpcode = v_gpcode.substr(0,v_gpcode.length-1)
				//v_gpcode_name = v_gpcode_name.substr(0,v_gpcode_name.length-1);
				//
				//
				//var gpinfo_sm = grid_gpinfo.getSelectionModel().getSelection()[0];
				//gpinfo_sm.data.gpcode = v_gpcode;
				//gpinfo_sm.data.iv_name = v_gpcode_name;
				//Ext.getCmp('ivFormPanel').loadRecord(gpinfo_sm);
				//
				//winInvoice.setTitle(v_gpcode_name+'"공구건에 대한 발주 입력폼');
				//
				//var button = Ext.get('btn_invoice');
				//button.dom.disabled = true;
				////this.container.dom.style.visibility=true
				//
				//if (winInvoice.isVisible()) {
				//	winInvoice.hide(this, function() {
				//		button.dom.disabled = false;
				//	});
				//} else {
				//	winInvoice.show(this, function() {
				//		button.dom.disabled = false;
				//	});
				//}

				grid_window_invoice.reconfigure(storeTempInvoice);
			}
		}
	],
	bbar : {
		plugins: new Ext.ux.SlidingPager(),
		xtype : 'pagingtoolbar',
		id : 'ptb_orderitems',
		store : store_orderitems,
		displayInfo : true,
		displayMsg : '{0}/{1} Total - {2}',
		emptyMsg : 'No Data'
	},
	listeners : {
		selectionchange: function(view, records) {
		},
		edit: listenerEditFunc,
		afterrender: listenerAfterRendererFunc
	}
});


/******************************** 발주 - end *********************************/


/******************************** 송금 - start *********************************/


/* 좌측상단 송금예정 발주서 목록 */
var grid_invoiceTodoWire = Ext.create('Ext.grid.Panel',{
	id : 'grid_invoiceTodoWire',
	headerPosition: 'left',
	title : '송금예정발주서',
	plugins: [Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1})],
	selModel: Ext.create('Ext.selection.CheckboxModel', {}),
	viewConfig: {
		stripeRows: true,
		enableTextSelection: true
	},
	autoWidth : true,
	height : 400,
	autoLoad : true,
	store : store_invoiceTodoWire,
	columns : [
		{ text : '날짜',							dataIndex : 'iv_date',					sortable: true,	summaryType: 'max',		renderer: Ext.util.Format.dateRenderer('Y-m-d'),	summaryRenderer: Ext.util.Format.dateRenderer('Y-m-d'),		field: { xtype: 'datefield' },		hidden:true	},
		{ text : '공구코드',					dataIndex : 'gpcode',						width:230,			hidden:true	},
		{ text : '송금코드',					dataIndex : 'wr_id',						width:130,			hidden:true	},
		{ text : '발주코드',					dataIndex : 'iv_id',						width:120	},
		{ text : '발주서 별칭',			dataIndex : 'iv_name',					width:150	},
		{ text : '통화',							dataIndex : 'money_type',				width:70	},
		{ text : 'TOTAL',						dataIndex : 'TOTAL_PRICE',			width:150,		style:'text-align:center',		align:'right',		renderer: Ext.util.Format.numberRenderer('0,000.00') },
		{ text : '인보이스번호',			dataIndex : 'iv_order_no',			width:120	},
		{ text : '메모',							dataIndex : 'iv_memo',					width:170,		editor: { allowBlank : false }	},		
		{ text : 'DC.FEE',					dataIndex : 'iv_discountfee',		width:100,		style:'text-align:center',		align:'right',		renderer: Ext.util.Format.numberRenderer('0,000.00'),		editor: { allowBlank : false } },
		{ text : 'TAX',							dataIndex : 'iv_tax',						width:100,		style:'text-align:center',		align:'right',		renderer: Ext.util.Format.numberRenderer('0,000.00'),		editor: { allowBlank : false } },
		{ text : 'SHIP.FEE',				dataIndex : 'iv_shippingfee',		width:120,		style:'text-align:center',		align:'right',		renderer: Ext.util.Format.numberRenderer('0,000.00'),		editor: { allowBlank : false } },
		{ text : '딜러',							dataIndex : 'iv_dealer',				width:120	},		
		{ text : '인보이스날짜',			dataIndex : 'iv_date',					width:120	},
		{
			xtype: 'gridcolumn',
			dataIndex: 'money_type',
			text: '통화유형',
			style:'text-align:center',
			align:'center',
			allowBlank: true,
			editor: Ext.create('Ext.combobox.item.moneytype'),
			value : 'USD',
			renderer: rendererCombo
		},
		{ text : '담당자',						dataIndex : 'admin_name',				width:120	},
		{ text : '입출금링크',				dataIndex : 'iv_receipt_link',	width:120	}
	],
	dockedItems: [],
	tbar : [
		{	xtype: 'label',	text: '검색어 : ',		autoWidth:true,	style : 'font-weight:bold;'},
		{
			xtype: 'textfield',
			id : 'keyword_todoWire',
			name: 'keyword_todoWire',
			style: 'padding:0px;',
			enableKeyEvents: true,
			listeners:{
				keydown:function(t,e){
					if(e.keyCode == 13){

						var v_param = {
							keyword : Ext.getCmp('keyword_todoWire').getValue()
						}

						grid_invoiceTodoWire.store.loadData([],false);
						Ext.apply(grid_invoiceTodoWire.store.getProxy().extraParams, v_param);
						grid_invoiceTodoWire.store.load();
					}
				}
			}
		},
		{
			id		: 'btn_wire',
			text	: '송금',
			iconCls	: 'icon-table_edit',
			handler : function() {
				store_window_wire.removeAll();

				//송금탭 발주서 목록
				var sm = grid_invoiceTodoWire.getSelection();

				if( sm == '' ) {
					Ext.Msg.alert('알림','발주내역을 선택해주세요');
					return false;
				}

				var v_iv_id = '';
				var v_iv_name = '';
				for(var i = 0; i < sm.length; i++) {
					var rec = Ext.create('model.invoice', {
						'iv_id'						: sm[i].data.iv_id,
						'wr_id'						: sm[i].data.wr_id,
						'iv_name'					: sm[i].data.iv_name,
						'gpcode'					: sm[i].data.gpcode,
						'iv_dealer'				: sm[i].data.iv_dealer,
						'iv_order_no'			: sm[i].data.iv_order_no,
						'iv_receipt_link'	: sm[i].data.iv_receipt_link,
						'iv_date'					: sm[i].data.iv_date,
						'money_type'			: sm[i].data.money_type,
						'iv_memo'					: sm[i].data.iv_memo,
						'reg_date'				: sm[i].data.reg_date,
						'admin_id'				: sm[i].data.admin_id,
						'TOTAL_PRICE'			: parseFloat(sm[i].data.TOTAL_PRICE),
						'iv_discountfee'	: parseFloat(sm[i].data.iv_discountfee),
						'iv_shippingfee'	: parseFloat(sm[i].data.iv_shippingfee),
						'iv_tax'					: parseFloat(sm[i].data.iv_tax),
						'od_exch_rate'		: sm[i].data.od_exch_rate,
						'arv_exch_rate'		: sm[i].data.arv_exch_rate
					});
					store_window_wire.add(rec);

					v_iv_id += sm[i].data.iv_id+',';
					v_iv_name += sm[i].data.iv_name+',';
				}
				grid_window_wire.reconfigure(store_window_wire);
				
				v_iv_id = v_iv_id.substr(0,v_iv_id.length-1);
				v_iv_name = v_iv_name.substr(0,v_iv_name.length-1);


				/*송금내역 작성 기본폼 로딩*/
				var iv_sm = grid_invoiceTodoWire.getSelectionModel().getSelection()[0];
				iv_sm.data.iv_id = v_iv_id;
				Ext.getCmp('wireFormPanel').loadRecord(iv_sm);
				//winWireConfirm.setTitle(v_gpcode_name+'"송금내역작성');

				var button = Ext.get('btn_invoice');
				button.dom.disabled = true;
				//this.container.dom.style.visibility=true

				if (winWireConfirm.isVisible()) {
					winWireConfirm.hide(this, function() {
						button.dom.disabled = false;
					});
				} else {
					winWireConfirm.show(this, function() {
						button.dom.disabled = false;
					});
				}

			}
		},
		{
			text	: '삭제(1개)',
			iconCls	: 'icon-delete',
			handler: function() {
				delSelectedGrid1Row(grid_invoiceTodoWire);
			}
		},
	],
	bbar : {
		plugins: new Ext.ux.SlidingPager(),
		xtype : 'pagingtoolbar',
		store : store_invoiceTodoWire,
		displayInfo : true,
		displayMsg : '{0}/{1} Total - {2}',
		emptyMsg : 'No Data'
	},
	listeners : {
		selectionchange: function(view, records) {
			//송금관련 품목 초기화
			store_wire_dtl.loadData([],false);
			//좌측 하단 송금처리된 발주서 선택 초기화
			grid_invoiceEndWire.getSelectionModel().deselectAll(true);
			//우측 상단 연결된 공구정보
			grid_gpinfo.getSelectionModel().deselectAll(true);
			//우측 하단 송금관련 품목
			grid_wire_dtl.getSelectionModel().deselectAll(true);


			var sm = grid_invoiceTodoWire.getSelectionModel().getSelection();

			if(sm[0]) {

				var v_gpcode = '';
				var v_iv_id = '';

				for(var i = 0; i < sm.length; i++) {	//sm[i].data

					//  ','단위로 분할한 공구코드 다시 합치기
					var v_arr = sm[i].data.gpcode.split(',');
					for(var a = 0; a < v_arr.length; a++) {
						v_gpcode += "'"+v_arr[a] + "',";
					}

					v_iv_id += "'"+sm[i].data.iv_id + "',";
				}

				v_gpcode = v_gpcode.substr(0,v_gpcode.length-1);
				v_iv_id = v_iv_id.substr(0,v_iv_id.length-1);

				//v_keyword = Ext.getCmp('orderitems_keyword').getValue();
				var v_param = { 'gpcode' : v_gpcode,		'iv_id' : v_iv_id };

				/*공구별 참고사항 로딩*/
				Ext.apply(store_wire_gpinfo.getProxy().extraParams, v_param);
				store_wire_gpinfo.load();

				//송금관련 품목 로딩
				//store_wire_dtl.removeAll();
				Ext.apply(store_wire_dtl.getProxy().extraParams, v_param);
				store_wire_dtl.load();
			}
	 	},
		edit: listenerEditFunc,
		afterrender: listenerAfterRendererFunc
	}
});


/* 좌측하단 송금완료 발주서 목록 */
var grid_invoiceEndWire = Ext.create('Ext.grid.Panel',{
	id : 'grid_invoiceEndWire',
	headerPosition: 'left',
	title : '송금완료 발주서',
	plugins: [Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1})],
	selModel: Ext.create('Ext.selection.CheckboxModel', {}),
	autoWidth : true,
	height : 370,
	autoLoad : true,
	features: [
		{
			ftype : 'groupingsummary',
			groupHeaderTpl: '{name}',
			hideGroupedHeader: true,
			enableGroupingMenu: true,
			collapsible : false
		}
	],
	viewConfig: {
		stripeRows: true,
		/*getRowClass: orderStatsColorSetting,*/
		enableTextSelection: true
	},
	store : store_invoiceEndWire,
	columns : [
		{ text : '그룹코드',						dataIndex : 'Group',						width:230,			hidden:true	},
		{ text : '공구코드',						dataIndex : 'gpcode',						width:230,			hidden:true},
		{ text : '통관코드',						dataIndex : 'cr_id',						width:130,			hidden:true},
		{ text : '송금코드',						dataIndex : 'wr_id',						width:130,			hidden:true},
		{ text : '송금별칭',						dataIndex : 'wr_name',					width:120,			hidden:true},
		{ text : '송금수수료(해외)',		dataIndex : 'wr_out_fee',				width:120,			hidden:true},
		{ text : '송금수수료(국내)',		dataIndex : 'wr_in_fee',				width:120,			hidden:true},
		{ text : '송금메모',						dataIndex : 'wr_memo',					width:120,			hidden:true},
		{ text : '발주코드',						dataIndex : 'iv_id',						width:120	},
		{ text : '발주서 별칭',				dataIndex : 'iv_name',					width:150	},
		{
			xtype: 'gridcolumn',
			dataIndex: 'money_type',
			text: '통화유형',
			style:'text-align:center',
			align:'center',
			allowBlank: true,
			editor: Ext.create('Ext.combobox.item.moneytype'),
			value : 'USD',
			renderer: rendererCombo
		},
		{ text : 'TOTAL',							dataIndex : 'TOTAL_PRICE',			width:150,		style:'text-align:center',		align:'right',		renderer: Ext.util.Format.numberRenderer('0,000.00'), 	summaryType : 'sum',		summaryRenderer : rendererSummaryFormat },
		{ text : 'DC.FEE',						dataIndex : 'iv_discountfee',		width:100,		style:'text-align:center',		align:'right',		renderer: Ext.util.Format.numberRenderer('0,000.00'), 	summaryType : 'sum',		summaryRenderer : rendererSummaryFormat },
		{ text : 'TAX',								dataIndex : 'iv_tax',						width:100,		style:'text-align:center',		align:'right',		renderer: Ext.util.Format.numberRenderer('0,000.00'), 	summaryType : 'sum',		summaryRenderer : rendererSummaryFormat },
		{ text : 'SHIP.FEE',					dataIndex : 'iv_shippingfee',		width:120,		style:'text-align:center',		align:'right',		renderer: Ext.util.Format.numberRenderer('0,000.00'), 	summaryType : 'sum',		summaryRenderer : rendererSummaryFormat },
		{ text : '환율(송금)',					dataIndex : 'wr_exchrate',			width:100,		style:'text-align:center',		align:'right',		editor: { allowBlank : false }	},
		{ text : '딜러',								dataIndex : 'iv_dealer',				width:120	},
		{ text : '인보이스번호',				dataIndex : 'iv_order_no',			width:120	},
		{ text : '인보이스날짜',				dataIndex : 'iv_date',					sortable: true,	summaryType: 'min',		renderer: Ext.util.Format.dateRenderer('Y-m-d'),	summaryRenderer: Ext.util.Format.dateRenderer('Y-m-d'),		field: { xtype: 'datefield' }	},
		{ text : '담당자',							dataIndex : 'admin_name',				width:120	},
		{ text : '메모',								dataIndex : 'iv_memo',					width:170,		editor: { allowBlank : false }	},
		{ text : '입출금링크',					dataIndex : 'iv_receipt_link',	width:120	}
	],
	dockedItems: [],
	tbar : [
		{	xtype: 'label',	text: '검색어 : ',		autoWidth:true,	style : 'font-weight:bold;'},
		{
			xtype: 'textfield',
			id : 'keyword_endWire',
			name: 'keyword_endWire',
			style: 'padding:0px;',
			enableKeyEvents: true,
			listeners:{
				keydown:function(t,e){
					if(e.keyCode == 13){

						var v_param = {
							keyword : Ext.getCmp('keyword_endWire').getValue()
						}

						grid_invoiceEndWire.store.loadData([],false);
						Ext.apply(grid_invoiceEndWire.store.getProxy().extraParams, v_param);
						grid_invoiceEndWire.store.load();
					}
				}
			}
		}
	],
	bbar : {
		plugins: new Ext.ux.SlidingPager(),
		xtype : 'pagingtoolbar',
		store : store_invoiceEndWire,
		displayInfo : true,
		displayMsg : '{0}/{1} Total - {2}',
		emptyMsg : 'No Data'
	},
	listeners : {
		selectionchange: function(view, records) {

			store_wire_dtl.loadData([],false);

			//좌측 상단 송금예정 발주서
			grid_invoiceTodoWire.getSelectionModel().deselectAll(true);
			//좌측 하단 송금처리된 발주서 선택 초기화
			//grid_invoiceEndWire.getSelectionModel().deselectAll(true);
			//우측 상단 연결된 공구정보
			grid_gpinfo.getSelectionModel().deselectAll(true);
			//우측 하단 송금관련 품목
			grid_wire_dtl.getSelectionModel().deselectAll(true);


			var sm = grid_invoiceEndWire.getSelectionModel().getSelection();

			if(sm[0]) {

				var v_gpcode = '';
				var v_iv_id = '';

				for(var i = 0; i < sm.length; i++) {	//sm[i].data

					//  ','단위로 분할한 공구코드 다시 합치기
					var v_arr = sm[i].data.gpcode.split(',');
					for(var a = 0; a < v_arr.length; a++) {
						v_gpcode += "'"+v_arr[a] + "',";
					}

					v_iv_id += "'"+sm[i].data.iv_id + "',";
				}

				v_gpcode = v_gpcode.substr(0,v_gpcode.length-1);
				v_iv_id = v_iv_id.substr(0,v_iv_id.length-1);

				//v_keyword = Ext.getCmp('orderitems_keyword').getValue();
				var v_param = { 'gpcode' : v_gpcode,		'iv_id' : v_iv_id };

				//우측 상단 연결된공구정보 로딩
				Ext.apply(store_wire_gpinfo.getProxy().extraParams, v_param);
				store_wire_gpinfo.load();


				//우측 하단 송금관련 품목 로딩
				//store_wire_dtl.removeAll();
				Ext.apply(store_wire_dtl.getProxy().extraParams, v_param);
				store_wire_dtl.load();
			}
		},
		edit: listenerEditFunc,
		afterrender: listenerAfterRendererFunc
	}
});



/* 우측상단 연결된 공구정보 */
var grid_wire_gpinfo = Ext.create('Ext.grid.Panel',{
	id : 'grid_wire_gpinfo',
	headerPosition: 'left',
	title : '연결된 공구정보',
	remoteSort : true,
	autoLoad : false	,
	autoWidth : true,
	height : 160,
	plugins: ['clipboard'],
	columns : [
		{ text : '공구명', 			width : 380,	dataIndex : 'gpcode_name',	sortable: false	},
		{ text : '공구코드',		width : 120,	dataIndex : 'gpcode',				hidden:true	},
		{ text : '날짜',				width : 120,	dataIndex : 'reg_date',			hidden:true	},
		{ text : '주문',				width : 70,		dataIndex : 'SUM_QTY',			style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '미발주',			width : 80,		dataIndex : 'NEED_IV_QTY',	style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '발주',				width : 70,		dataIndex : 'SUM_IV_QTY',		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '주문총액',		width : 120,	dataIndex : 'SUM_PAY',			style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '주문량',			width : 90,		dataIndex : 'ITC_CNT',			style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '발주량',			width : 90,		dataIndex : 'IVC_CNT',			style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') }
	],
store : store_wire_gpinfo
});



/* 우측하단 좌측발주건에 대한 발주 품목들 */
var grid_wire_dtl = Ext.create('Ext.grid.Panel',{
	id : 'grid_wire_dtl',
	headerPosition: 'left',
	title : '송금관련품목',
	plugins: [Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1})],
	selModel: Ext.create('Ext.selection.CheckboxModel', {}),
	viewConfig: {
		stripeRows: true,
		enableTextSelection: true,
		getRowClass: ivStatsColorSetting
	},
	autoWidth : true,
	height : 645,
	store : store_wire_dtl,
	columns : [
		{ text : 'number',				dataIndex : 'number',						hidden:true	},
		{ text : '발주코드',			dataIndex : 'iv_id',						width:120		},
		{
			xtype: 'gridcolumn',
			dataIndex: 'iv_stats',
			text: '현황',
			style:'text-align:center',
			align:'center',
			allowBlank: true,
			editor: Ext.create('Ext.combobox.item.ivstats'),
			value : '00',
			renderer: rendererCombo
		},
		{ text : '날짜',					dataIndex : 'reg_date',							sortable: true,		summaryType: 'max',		renderer: Ext.util.Format.dateRenderer('Y-m-d'),	summaryRenderer: Ext.util.Format.dateRenderer('Y-m-d'),		field: { xtype: 'datefield' },		hidden:true	},
		{ text : '공구코드',			dataIndex : 'gpcode',								hidden:true		},
		{ text : '공구명',				dataIndex : 'gpcode_name'	},
		{ text : 'IMG', 				dataIndex : 'iv_it_img',						width: 50,		renderer:rendererImage 		},
		{ text : '상품코드',			dataIndex : 'iv_it_id',							width:160			},
		{ text : '발주수량',			dataIndex : 'iv_qty',								width:100,		editor: { allowBlank : false },		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '입고수량',			dataIndex : 'ip_qty',								width:100,		editor: { allowBlank : false },		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '통화',					dataIndex : 'money_type',						width:70			},
		{ text : '발주가',				dataIndex : 'iv_dealer_worldprice',	width:120,		editor: { allowBlank : false },		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000.00') },
		{ text : 'TOTAL',				dataIndex : 'total_price',					width:120,																			style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000.00') },
		{ text : '품목명',				dataIndex : 'iv_it_name',						width:450			}
	],
	tbar : [
			combo_wr_stats,
			{
				id		: 'iv_stats_update',
				text	: '변경',
				iconCls	: 'icon-table_edit',
				handler : function() {
					var sm = grid_wire_dtl.getSelection();
					if( sm == '' ) {
						Ext.Msg.alert('알림','품목들을 선택해주세요');
						return false;
					}

					var iv_stats = combo_wr_stats.getValue();

					for(var i = 0; i < sm.length; i++) {
						sm[i].set('iv_stats',iv_stats);
					}

					grid_wire_dtl.store.update();
				}
			},
			{
				text	: '인쇄',
				iconCls	: 'icon-table_print',
				handler: function() {
					Ext.ux.grid.Printer.mainTitle = Ext.util.Format.date(new Date(),'Y-m-d g:i:s') +' 발주목록';
					Ext.ux.grid.Printer.print(grid_wire_dtl);
				}
			}
	],
	bbar : {
		plugins: new Ext.ux.SlidingPager(),
		xtype : 'pagingtoolbar',
		store : store_wire_dtl,
		displayInfo : true,
		displayMsg : '{0}/{1} Total - {2}',
		emptyMsg : 'No Data'
	},
	listeners : {
		selectionchange: function(view, records) {
	 	},
		edit: listenerEditFunc,
		afterrender: listenerAfterRendererFunc
	}
});

/******************************** 송금 - END *********************************/


/******************************** 통관 - START *******************************/

/* 좌측상단 통관예정(송금완료) 목록 */
var grid_todoClearance = Ext.create('Ext.grid.Panel',{
	id : 'grid_todoClearance',
	headerPosition: 'left',
	title : '통관예정 발주서',
	plugins: [Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1})],
	selModel: Ext.create('Ext.selection.CheckboxModel', {}),
	autoWidth : true,
	height : 500,
	autoLoad : true,
	features: [
		{
			ftype : 'groupingsummary',
			groupHeaderTpl: '{name}',
			hideGroupedHeader: true,
			enableGroupingMenu: true,
			collapsible : false
		}
	],
	viewConfig: {
		stripeRows: true,
		enableTextSelection: true
	},
	store : store_todoClearance,
	columns : [
		{ text : '그룹코드',						dataIndex : 'Group',						width:230,			hidden:true	},
		{ text : '날짜',								dataIndex : 'iv_date',					sortable: true,	summaryType: 'max',		renderer: Ext.util.Format.dateRenderer('Y-m-d'),	summaryRenderer: Ext.util.Format.dateRenderer('Y-m-d'),		field: { xtype: 'datefield' },		hidden:true	},
		{ text : '공구코드',						dataIndex : 'gpcode',						width:230,			hidden:true	},
		{ text : '통관ID',							dataIndex : 'cr_id',						width:120,			hidden:true	},
		{ text : '송금코드',						dataIndex : 'wr_id',						width:130,			hidden:true	},
		{ text : '%',									dataIndex : 'complete_per',			width:60	},
		{ text : '발주코드',						dataIndex : 'iv_id',						width:120	},
		{ text : '인보이스번호',				dataIndex : 'iv_order_no',			width:120	},
		{ text : '발주서 별칭',				dataIndex : 'iv_name',					width:150	},
		{
			xtype: 'gridcolumn',
			dataIndex: 'money_type',
			text: '통화유형',
			style:'text-align:center',
			align:'center',
			allowBlank: true,
			editor: Ext.create('Ext.combobox.item.moneytype'),
			value : 'USD',
			renderer: rendererCombo
		},
		{ text : 'TOTAL',							dataIndex : 'TOTAL_PRICE',			width:150,			style:'text-align:center',		align:'right',		renderer: Ext.util.Format.numberRenderer('0,000.00'), 	summaryType : 'sum',		summaryRenderer : rendererSummaryFormat },
		{ text : 'DC.FEE',						dataIndex : 'iv_discountfee',		width:100,			style:'text-align:center',		align:'right',		renderer: Ext.util.Format.numberRenderer('0,000.00'), 	summaryType : 'sum',		summaryRenderer : rendererSummaryFormat },
		{ text : 'TAX',								dataIndex : 'iv_tax',						width:100,			style:'text-align:center',		align:'right',		renderer: Ext.util.Format.numberRenderer('0,000.00'), 	summaryType : 'sum',		summaryRenderer : rendererSummaryFormat },
		{ text : 'SHIP.FEE',					dataIndex : 'iv_shippingfee',		width:120,			style:'text-align:center',		align:'right',		renderer: Ext.util.Format.numberRenderer('0,000.00'), 	summaryType : 'sum',		summaryRenderer : rendererSummaryFormat },
		{ text : '환율(송금)',					dataIndex : 'wr_exch_rate',			width:100,			style:'text-align:center',		align:'right',		editor: { allowBlank : false }	},
		{ text : '딜러',								dataIndex : 'iv_dealer',				width:80	},
		{ text : '인보이스날짜',				dataIndex : 'iv_date',					sortable: true,	summaryType: 'max',		renderer: Ext.util.Format.dateRenderer('Y-m-d'),	summaryRenderer: Ext.util.Format.dateRenderer('Y-m-d'),		field: { xtype: 'datefield' }	},
		{ text : '담당자',							dataIndex : 'admin_name',				width:120	},
		{ text : '발주서메모',					dataIndex : 'iv_memo',					width:170,		editor: { allowBlank : false }	},
		{ text : '입출금링크',					dataIndex : 'iv_receipt_link',	width:120	}
	],
	dockedItems: [],
	tbar : [
		{	xtype: 'label',	text: '검색어 : ',		autoWidth:true,	style : 'font-weight:bold;'},
		{
			xtype: 'textfield',
			id : 'keyword_todoClearance',
			name: 'keyword_todoClearance',
			style: 'padding:0px;',
			enableKeyEvents: true,
			listeners:{
				keydown:function(t,e){
					if(e.keyCode == 13){
						var v_param = {
							keyword : Ext.getCmp('keyword_todoClearance').getValue()
						}

						var grid = grid_todoClearance;

						grid.store.loadData([],false);
						Ext.apply(grid.store.getProxy().extraParams, v_param);
						grid.store.load();
					}
				}
			}
		}
	],
	bbar : {
		plugins: new Ext.ux.SlidingPager(),
		xtype : 'pagingtoolbar',
		store : store_todoClearance,
		displayInfo : true,
		displayMsg : '{0}/{1} Total - {2}',
		emptyMsg : 'No Data'
	},
	listeners : {
		selectionchange: function(view, records) {

			store_clearance_dtl.loadData([],false);

			var sm = grid_todoClearance.getSelectionModel().getSelection();

			if(sm[0]) {

				grid_endClearance.getSelectionModel().deselectAll(true);

				var v_gpcode = '';
				var v_iv_id = '';

				for(var i = 0; i < sm.length; i++) {	//sm[i].data

					//  ','단위로 분할한 공구코드 다시 합치기
					var v_arr = sm[i].data.gpcode.split(',');
					for(var a = 0; a < v_arr.length; a++) {
						v_gpcode += "'"+v_arr[a] + "',";
					}

					v_iv_id += "'"+sm[i].data.iv_id + "',";
				}

				v_gpcode = v_gpcode.substr(0,v_gpcode.length-1);
				v_iv_id = v_iv_id.substr(0,v_iv_id.length-1);

				//v_keyword = Ext.getCmp('orderitems_keyword').getValue();
				var v_param = {
					'gpcode' : v_gpcode,
					'iv_id' : v_iv_id,
					'cr_id' : ''
				};

				/*공구별 참고사항 로딩*/
				v_param.mode = 'gpinfo';
				Ext.apply(store_clearance_gpinfo.getProxy().extraParams, v_param);
				store_clearance_gpinfo.load();


				//통관관련 품목 로딩
				v_param.mode = 'todoClearanceItem';
				Ext.apply(store_clearance_dtl.getProxy().extraParams, v_param);
				store_clearance_dtl.load();
			}
		},
		edit: listenerEditFunc,
		afterrender: listenerAfterRendererFunc
	}
});

/* 좌측하단 통관완료 목록 */
var grid_endClearance = Ext.create('Ext.grid.Panel',{
	id : 'grid_endClearance',
	headerPosition: 'left',
	title : '통관완료내역',
	plugins: [Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1})],
	selModel: Ext.create('Ext.selection.CheckboxModel', {}),
	viewConfig: {
		stripeRows: true,
		enableTextSelection: true
	},
	autoWidth : true,
	height : 270,
	autoLoad : true,
	store : store_endClearance,
	columns : [
		{ text : '통관코드',						dataIndex : 'cr_id',						width:140	},
		{ text : '통관별칭',						dataIndex : 'cr_name',					width:150	},
		{ text : '통관일',							dataIndex : 'cr_date',					sortable: true,		summaryType: 'max',						renderer: Ext.util.Format.dateRenderer('Y-m-d')	},
		{ text : '관세',								dataIndex : 'cr_dutyfee',				width:100,				style:'text-align:center',		align:'right',		renderer: Ext.util.Format.numberRenderer('0,000.00'),		editor: { allowBlank : false } },
		{ text : '부가세',							dataIndex : 'cr_taxfee',				width:100,				style:'text-align:center',		align:'right',		renderer: Ext.util.Format.numberRenderer('0,000.00'),		editor: { allowBlank : false } },
		{ text : '배송비',							dataIndex : 'cr_shipfee',				width:120,				style:'text-align:center',		align:'right',		renderer: Ext.util.Format.numberRenderer('0,000.00'),		editor: { allowBlank : false } },
		{ text : '담당자',							dataIndex : 'admin_id',					width:120	}
	],
	dockedItems: [],
	tbar : [
		{	xtype: 'label',	text: '검색어 : ',		autoWidth:true,	style : 'font-weight:bold;'},
		{
			xtype: 'textfield',
			id : 'keyword_endClearance',
			name: 'keyword_endClearance',
			style: 'padding:0px;',
			enableKeyEvents: true,
			listeners:{
				keydown:function(t,e){
					if(e.keyCode == 13){
						var v_param = {
							keyword : Ext.getCmp('keyword_endClearance').getValue()
						}
						
						var grid = grid_endClearance;
						
						grid.store.loadData([],false);
						Ext.apply(grid.store.getProxy().extraParams, v_param);
						grid.store.load();
					}
				}
			}
		}
	],
	bbar : {
		plugins: new Ext.ux.SlidingPager(),
		xtype : 'pagingtoolbar',
		store : store_endClearance,
		displayInfo : true,
		displayMsg : '{0}/{1} Total - {2}',
		emptyMsg : 'No Data'
	},
	listeners : {
		selectionchange: function(view, records) {
			store_clearance_dtl.loadData([],false);
			var sm = grid_endClearance.getSelectionModel().getSelection();

			if(sm[0]) {
				//통관예정그리드 언셀렉트
				grid_todoClearance.getSelectionModel().deselectAll(true);

				var v_gpcode = '';
				var v_cr_id = '';

				for(var i = 0; i < sm.length; i++) {	//sm[i].data

					//  ','단위로 분할한 공구코드 다시 합치기
					var v_arr = sm[i].data.gpcode.split(',');
					for(var a = 0; a < v_arr.length; a++) {
						v_gpcode += "'"+v_arr[a] + "',";
					}

					v_cr_id += "'"+sm[i].data.cr_id + "',";
				}

				v_gpcode = v_gpcode.substr(0,v_gpcode.length-1);
				v_cr_id = v_cr_id.substr(0,v_cr_id.length-1);

				//v_keyword = Ext.getCmp('orderitems_keyword').getValue();
				var v_param = {
												'gpcode': '',
												'iv_id'	:	'',												
												'cr_id' : v_cr_id
				};

				
				/*공구별 참고사항 로딩*/
				v_param.mode = 'gpinfo';
				Ext.apply(store_clearance_gpinfo.getProxy().extraParams, v_param);
				store_clearance_gpinfo.load();

				
				//통관관련 품목 로딩
				v_param.mode = 'endClearanceItem';
				Ext.apply(store_clearance_dtl.getProxy().extraParams, v_param);
				store_clearance_dtl.load();
				
			}
		},
		edit: listenerEditFunc,
		afterrender: listenerAfterRendererFunc
	}
});


/* 우측상단 연결된 공구정보 */
var grid_clearance_gpinfo = Ext.create('Ext.grid.Panel',{
	id : 'grid_clearance_gpinfo',
	headerPosition: 'left',
	title : '연결된 공구정보',
	remoteSort : true,
	autoLoad : false	,
	autoWidth : true,
	height : 160,
	plugins: ['clipboard'],
	columns : [
		{ text : '공구명', 		width : 380,	dataIndex : 'gpcode_name',	sortable: false	},
		{ text : '공구코드',		width : 120,	dataIndex : 'gpcode',				hidden:true	},
		{ text : '날짜',				width : 120,	dataIndex : 'reg_date',			hidden:true	},
		{ text : '주문',				width : 70,		dataIndex : 'SUM_QTY',			style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '미발주',			width : 80,		dataIndex : 'NEED_IV_QTY',	style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '발주',				width : 70,		dataIndex : 'SUM_IV_QTY',		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '공구총액',		width : 120,	dataIndex : 'SUM_PAY',			style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '공구수량',		width : 90,		dataIndex : 'ITC_CNT',			style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '발주수량',		width : 90,		dataIndex : 'IVC_CNT',			style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') }
	],
	store : store_clearance_gpinfo
});



/* 우측하단 좌측발주건에 대한 발주 품목들 */
var grid_clearance_dtl = Ext.create('Ext.grid.Panel',{
	id : 'grid_clearance_dtl',
	headerPosition: 'left',
	title : '통관진행품목',
	plugins: [Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1})],
	selModel: Ext.create('Ext.selection.CheckboxModel', {}),
	viewConfig: {
		stripeRows: true,
		enableTextSelection: true,
		getRowClass: ivStatsColorSetting
	},
	autoWidth : true,
	height : 645,
	store : store_clearance_dtl,
	columns : [
		{ text : 'number',			dataIndex : 'number',								hidden:true	},
		{ text : '통관코드',			dataIndex : 'cr_id',								width:130		},
		{ text : '발주코드',			dataIndex : 'iv_id',								width:120		},
		{ text : '인보이스번호',	dataIndex : 'iv_order_no',					width:120		},
		{
			xtype: 'gridcolumn',
			dataIndex: 'iv_stats',
			text: '현황',
			style:'text-align:center',
			align:'center',
			allowBlank: true,
			editor: Ext.create('Ext.combobox.item.ivstats'),
			value : '00',
			renderer: rendererCombo
		},
		{ text : '공구코드',			dataIndex : 'gpcode',								width:120,		hidden:true		},
		{ text : '공구명',				dataIndex : 'gpcode_name',					width:120		},
		{ text : '날짜',					dataIndex : 'reg_date',							sortable: true,		summaryType: 'max',		renderer: Ext.util.Format.dateRenderer('Y-m-d'),	summaryRenderer: Ext.util.Format.dateRenderer('Y-m-d'),		field: { xtype: 'datefield' },		hidden:true	},
		{ text : 'IMG', 				dataIndex : 'iv_it_img',						width:50,					renderer:rendererImage 		},
		{ text : '상품코드',			dataIndex : 'iv_it_id',							width:160		},
		{ text : '통화',					dataIndex : 'money_type',						width:70		},
		{ text : '발주가',				dataIndex : 'iv_dealer_worldprice',	width:80,		editor: { allowBlank : false },		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000.00') },
		{ text : '발주가(￦)',		dataIndex : 'iv_dealer_price',			width:120,	editor: { allowBlank : false },		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000'),	hidden:true },
		{ text : '발주수량',			dataIndex : 'iv_qty',								width:100,	editor: { allowBlank : false },		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '통관수량',			dataIndex : 'cr_qty',								width:100,	editor: { allowBlank : false },		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '취소수량',			dataIndex : 'cr_cancel_qty',				width:100,	editor: { allowBlank : false },		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '품목명',				dataIndex : 'iv_it_name',						width:450		}
		//{ text : '입고수량',		dataIndex : 'ip_qty',						editor: { allowBlank : false },		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		//{ text : '발주총액',		dataIndex : 'total_price',		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') }
	],
	tbar : [
		{
			id		: 'btn_clearance',
			text	: '통관',
			iconCls	: 'icon-table_edit',
			handler : function() {
				store_window_clearance.removeAll();

				//통관진행할 품목 목록
				var sm = grid_clearance_dtl.getSelection();

				if( sm == '' ) {
					Ext.Msg.alert('알림','발주내역을 선택해주세요');
					return false;
				}

				var v_iv_id = '';
				var v_iv_name = '';
				var last_iv_id = '';

				for(var i = 0; i < sm.length; i++) {

					var rec = Ext.create('model.invoice', {
						'number'				: sm[i].data.number,
						'gpcode'				: sm[i].data.gpcode,
						'cr_id'					: sm[i].data.cr_id,
						'iv_id'					: sm[i].data.iv_id,
						'ip_id'					: sm[i].data.ip_id,
						'wr_id'					: sm[i].data.wr_id,
						'cr_it_id'			: sm[i].data.iv_it_id,
						'cr_it_name'		: sm[i].data.iv_it_name,
						'cr_qty'				: sm[i].data.iv_qty,
						'cr_cancel_qty'	: 0
					});
					store_window_clearance.add(rec);

					if(sm[i].data.iv_id != last_iv_id)
						v_iv_id += sm[i].data.iv_id+',';

					last_iv_id = sm[i].data.iv_id;
				}
				v_iv_id = v_iv_id.substr(0,v_iv_id.length-1);
				v_iv_name = v_iv_name.substr(0,v_iv_name.length-1);

				/*통관내역 작성 기본폼 로딩*/
				var iv_sm = grid_todoClearance.getSelectionModel().getSelection()[0];
				iv_sm.data.iv_id = v_iv_id;
				Ext.getCmp('clearanceFormPanel').loadRecord(iv_sm);
				//winWireConfirm.setTitle(v_gpcode_name+'"송금내역작성');

				var button = Ext.get('btn_clearance');
				button.dom.disabled = true;
				//this.container.dom.style.visibility=true

				if (winClearanceConfirm.isVisible()) {
					winClearanceConfirm.hide(this, function() {
						button.dom.disabled = false;
					});
				} else {
					winClearanceConfirm.show(this, function() {
						button.dom.disabled = false;
					});
				}

			}
		},
		combo_cr_stats,
		{
			id		: 'cr_stats_update',
			text	: '변경',
			iconCls	: 'icon-table_edit',
			handler : function() {
				var sm = grid_clearance_dtl.getSelection();
				if( sm == '' ) {
					Ext.Msg.alert('알림','품목들을 선택해주세요');
					return false;
				}

				var iv_stats = combo_cr_stats.getValue();

				for(var i = 0; i < sm.length; i++) {
					sm[i].set('iv_stats',iv_stats);
				}

				grid_clearance_dtl.store.update();
			}
		},
		{
			text	: '인쇄',
			iconCls	: 'icon-table_print',
			handler: function() {
				Ext.ux.grid.Printer.mainTitle = Ext.util.Format.date(new Date(),'Y-m-d g:i:s') +' 통관목록';
				Ext.ux.grid.Printer.print(grid_clearance_dtl);
			}
		}
	],
	bbar : {
		plugins: new Ext.ux.SlidingPager(),
		xtype : 'pagingtoolbar',
		store : store_clearance_dtl,
		displayInfo : true,
		displayMsg : '{0}/{1} Total - {2}',
		emptyMsg : 'No Data'
	},
	listeners : {
		selectionchange: function(view, records) {
		},
		edit: listenerEditFunc,
		afterrender: listenerAfterRendererFunc
	}
});

/******************************** 통관 - END *********************************/

/******************************** 입고 - START *******************************/


/* 좌측 입고완료 목록 */
var grid_todoWarehousing = Ext.create('Ext.grid.Panel',{
	id : 'grid_todoWarehousing',
	headerPosition: 'left',
	title : '입고예정내역',
	plugins: [Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1})],
	selModel: Ext.create('Ext.selection.CheckboxModel', {}),
	viewConfig: {
		stripeRows: true,
		enableTextSelection: true,
		getRowClass: function(record, index) {
			var c = record.get('IP_COMPLETE');
			if (c == 'Y') {
				return 'cell_bg_skyblue';
			}
		}
	},
	autoWidth : true,
	height : 770,
	autoLoad : true,
	store : store_todoWarehousing,
	columns : [
		{ text : '통관코드',						dataIndex : 'cr_id',						width:140	},
		{ text : '통관별칭',						dataIndex : 'cr_name',					width:150	},
		{ text : '통관CNT',						dataIndex : 'CR_EA',						width:60,					style:'text-align:center',		align:'right'	},
		{ text : '입고CNT',						dataIndex : 'IP_EA',						width:60,					style:'text-align:center',		align:'right'	},
		{ text : '통관일',							dataIndex : 'cr_date',					sortable: true,		summaryType: 'max',						renderer: Ext.util.Format.dateRenderer('Y-m-d')	},
		{ text : '관세',								dataIndex : 'cr_dutyfee',				width:100,				style:'text-align:center',		align:'right',		renderer: Ext.util.Format.numberRenderer('0,000.00'),		editor: { allowBlank : false } },
		{ text : '부가세',							dataIndex : 'cr_taxfee',				width:100,				style:'text-align:center',		align:'right',		renderer: Ext.util.Format.numberRenderer('0,000.00'),		editor: { allowBlank : false } },
		{ text : '배송비',							dataIndex : 'cr_shipfee',				width:120,				style:'text-align:center',		align:'right',		renderer: Ext.util.Format.numberRenderer('0,000.00'),		editor: { allowBlank : false } },
		{ text : '담당자',							dataIndex : 'admin_id',					width:120	}
	],
	dockedItems: [],
	tbar : [
		{	xtype: 'label',	text: '검색어 : ',		autoWidth:true,	style : 'font-weight:bold;'},
		{
			xtype: 'textfield',
			id : 'keyword_todoWarehousing',
			name: 'keyword_todoWarehousing',
			style: 'padding:0px;',
			enableKeyEvents: true,
			listeners:{
				keydown:function(t,e){
					if(e.keyCode == 13){

						var v_param = {
							keyword : Ext.getCmp('keyword_todoWarehousing').getValue()
						}

						grid_todoWarehousing.store.loadData([],false);
						Ext.apply(grid_todoWarehousing.store.getProxy().extraParams, v_param);
						grid_todoWarehousing.store.load();
					}
				}
			}
		}
	],
	bbar : {
		plugins: new Ext.ux.SlidingPager(),
		xtype : 'pagingtoolbar',
		store : store_todoWarehousing,
		displayInfo : true,
		displayMsg : '{0}/{1} Total - {2}',
		emptyMsg : 'No Data'
	},
	listeners : {
		selectionchange: function(view, records) {
			//입고관리품목 초기화
			store_warehousing_dtl.loadData([],false);

			//좌측 입고예정내역 선택정보
			var sm = grid_todoWarehousing.getSelectionModel().getSelection();

			if(sm[0]) {
				var v_gpcode = '';
				var v_cr_id = '';

				for(var i = 0; i < sm.length; i++) {	//sm[i].data

					//  ','단위로 분할한 공구코드 다시 합치기
					var v_arr = sm[i].data.gpcode.split(',');
					for(var a = 0; a < v_arr.length; a++) {
						v_gpcode += "'"+v_arr[a] + "',";
					}

					v_cr_id += "'"+sm[i].data.cr_id + "',";
				}

				v_gpcode = v_gpcode.substr(0,v_gpcode.length-1);
				v_cr_id = v_cr_id.substr(0,v_cr_id.length-1);

				//v_keyword = Ext.getCmp('orderitems_keyword').getValue();
				var v_param = { 'gpcode' : '',
					'iv_id'	:	'',
					'cr_id' : v_cr_id
				};

				/*공구별 참고사항 로딩*/
				Ext.apply(store_warehousing_gpinfo.getProxy().extraParams, v_param);
				store_warehousing_gpinfo.load();


				//통관관련 품목 로딩
				//store_clearance_dtl.removeAll();
				Ext.apply(store_warehousing_dtl.getProxy().extraParams, v_param);
				store_warehousing_dtl.load();

			}


		},
		edit: listenerEditFunc,
		afterrender: listenerAfterRendererFunc
	}
});




/* 우측상단 연결된 공구정보 */
var grid_warehousing_gpinfo = Ext.create('Ext.grid.Panel',{
	id : 'grid_warehousing_gpinfo',
	headerPosition: 'left',
	title : '연결된 공구정보',
	remoteSort : true,
	autoLoad : false	,
	autoWidth : true,
	height : 160,
	plugins: ['clipboard'],
	columns : [
		{ text : '공구명', 		width : 380,	dataIndex : 'gpcode_name',	sortable: false	},
		{ text : '공구코드',		width : 120,	dataIndex : 'gpcode',				hidden:true	},
		{ text : '날짜',				width : 120,	dataIndex : 'reg_date',			hidden:true	},
		{ text : '주문',				width : 70,		dataIndex : 'SUM_QTY',			style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '미발주',			width : 80,		dataIndex : 'NEED_IV_QTY',	style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '발주',				width : 70,		dataIndex : 'SUM_IV_QTY',		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '주문총액',		width : 120,	dataIndex : 'SUM_PAY',			style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '주문량',			width : 90,		dataIndex : 'ITC_CNT',			style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '발주량',			width : 90,		dataIndex : 'IVC_CNT',			style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') }
	],
	store : store_warehousing_gpinfo
});



/* 우측하단 좌측발주건에 대한 발주 품목들 */
var grid_warehousing_dtl = Ext.create('Ext.grid.Panel',{
	id : 'grid_warehousing_dtl',
	headerPosition: 'left',
	title : '입고관리품목',
	plugins: [Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1})],
	selModel: Ext.create('Ext.selection.CheckboxModel', {}),
	viewConfig: {
		stripeRows: true,
		enableTextSelection: true,
		getRowClass: ivStatsColorSetting
	},
	autoWidth : true,
	height : 645,
	store : store_warehousing_dtl,
	columns : [
		{ text : 'number',			dataIndex : 'number',						hidden:true	},
		{ text : '실제재고수량',	dataIndex : 'real_jaego',				hidden:true	},
		{ text : '통관코드',			dataIndex : 'cr_id',						width:130		},
		{ text : '발주코드',			dataIndex : 'iv_id',						width:120		},
		{ text : '인보이스번호',	dataIndex : 'iv_order_no',			width:120		},
		{
			xtype: 'gridcolumn',
			dataIndex: 'iv_stats',
			text: '현황',
			style:'text-align:center',
			align:'center',
			allowBlank: true,
			editor: Ext.create('Ext.combobox.item.ivstats'),
			value : '00',
			renderer: rendererCombo
		},
		{ text : '날짜',					dataIndex : 'reg_date',							sortable: true,		summaryType: 'max',		renderer: Ext.util.Format.dateRenderer('Y-m-d'),	summaryRenderer: Ext.util.Format.dateRenderer('Y-m-d'),		field: { xtype: 'datefield' },		hidden:true	},
		{ text : '공구코드',			dataIndex : 'gpcode',								hidden:true		},
		{ text : 'IMG', 				dataIndex : 'iv_it_img',						width:50,					renderer:rendererImage 		},
		{ text : '상품코드',			dataIndex : 'iv_it_id',							width:160		},
		{ text : '품목명',				dataIndex : 'iv_it_name',						width:450		},
		{ text : '통화',					dataIndex : 'money_type',						width:70		},
		{ text : '발주가(해외)',	dataIndex : 'iv_dealer_worldprice',	width:120,				editor: { allowBlank : false },		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000.00') },
		{ text : '발주가(￦)',		dataIndex : 'iv_dealer_price',			editor: { allowBlank : false },		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000'),	hidden:true },
		{ text : '주문집계',			dataIndex : 'SUM_QTY',																								style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '발주수량',			dataIndex : 'iv_qty',								editor: { allowBlank : false },		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '통관수량',			dataIndex : 'cr_qty',								editor: { allowBlank : false },		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '취소수량',			dataIndex : 'cr_cancel_qty',				editor: { allowBlank : false },		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') }
	],
	tbar : [

		{
			id		: 'stats_update_40',
			text	: '입고처리',
			iconCls	: 'icon-table_edit',
			handler : function() {
				var sm = grid_warehousing_dtl.getSelection();
				if( sm == '' ) {
					Ext.Msg.alert('알림','품목들을 선택해주세요');
					return false;
				}

				for(var i = 0; i < sm.length; i++) {
					if(sm[i].data.iv_stats != '40') {
						sm[i].set('iv_stats','40');
					}
				}

				//grid_warehousing_dtl.store.update();
				grid_warehousing_dtl.store.sync();
			}
		},
		{
			text	: '인쇄',
			iconCls	: 'icon-table_print',
			handler: function() {
				Ext.ux.grid.Printer.mainTitle = Ext.util.Format.date(new Date(),'Y-m-d g:i:s') +' 입고목록';
				Ext.ux.grid.Printer.print(grid_warehousing_dtl);
			}
		}
	],
	bbar : {
		plugins: new Ext.ux.SlidingPager(),
		xtype : 'pagingtoolbar',
		store : store_warehousing_dtl,
		displayInfo : true,
		displayMsg : '{0}/{1} Total - {2}',
		emptyMsg : 'No Data'
	},
	listeners : {
		selectionchange: function(view, records) {
		},
		edit: listenerEditFunc,
		afterrender: listenerAfterRendererFunc
	}
});



/******************************** 입고 - END *********************************/


/* 팝업윈도우에서 쓰이는 그리드 정리 */

/* 팝업윈도우 > 발주서 작성시 사용하는 그리드 */
var grid_window_invoice = Ext.create('Ext.grid.Panel',{
	id : 'grid_window_invoice',
	plugins: ['clipboard', Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1})],
	requires: [
		'Ext.grid.plugin.Clipboard',
		'Ext.grid.selection.SpreadsheetModel'
	],
	width : '100%',
	height : 320,
	store : store_window_invoice,
	columns : [
		{ text : '날짜',					dataIndex : 'reg_date',							sortable: true,		summaryType: 'max',		renderer: Ext.util.Format.dateRenderer('Y-m-d'),	summaryRenderer: Ext.util.Format.dateRenderer('Y-m-d'),		field: { xtype: 'datefield' },		hidden:true	},
		{ text : '공구코드',			dataIndex : 'gpcode',								hidden:true		},
		{ text : '상품코드',			dataIndex : 'iv_it_id',							width:160		},
		{ text : '품목명',				dataIndex : 'iv_it_name',						width:450		},
		{ text : '발주가(해외)',	dataIndex : 'iv_dealer_worldprice',	width:120,	editor: { allowBlank : true },		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000.00') },
		{ text : '발주가(￦)',		dataIndex : 'iv_dealer_price',			editor: { allowBlank : false },		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000'),	hidden:true },
		{ text : '발주수량',			dataIndex : 'iv_qty',								editor: { allowBlank : false },		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '발주총액',			dataIndex : 'total_price',					editor: { allowBlank : false },		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000.00'),	hidden:true }
	],
	listeners : {
		selectionchange: function(view, records) {
			var sm = grid_window_invoice.getSelectionModel();
	 	},
		edit: listenerEditFunc,
		afterrender: listenerAfterRendererFunc
	}
});


/* 팝업윈도우 > 송금내역 작성시 사용하는 그리드 */
var grid_window_wire = Ext.create('Ext.grid.Panel',{
	id : 'grid_window_wire',
	plugins: ['clipboard'],
	requires: ['Ext.grid.plugin.Clipboard'],
	autoWidth : true,
	features: [
		{
			ftype : 'summary',
			groupHeaderTpl: '{name}',
			hideGroupedHeader: true,
			enableGroupingMenu: true,
			collapsible : false
		}
	],
	height : 400,
	store : store_window_wire,
	columns : [
		{ text : '공구코드',			dataIndex : 'gpcode',						hidden:true	},
		{ text : '발주코드',			dataIndex : 'iv_id',						width:160		},
		{ text : '발주서 별칭',	dataIndex : 'iv_name',					width:160		},
		{ text : '발주총액',			dataIndex : 'TOTAL_PRICE',			width:140,		style:'text-align:center',	align:'right',		renderer: Ext.util.Format.numberRenderer('0,000.00'), 	summaryType : 'sum',		summaryRenderer : rendererSummaryFormat }
	],

	listeners : {
		selectionchange: function(view, records) {
			var sm = grid_window_wire.getSelectionModel();
			//grid_window_wire.down('#delJaego').setDisabled(!view.store.getCount());
			//store_save_dtl
			//sm.select(0);
		},
		edit: listenerEditFunc,
		afterrender: listenerAfterRendererFunc
	}
});

/* 팝업윈도우 > 통관내역서 쓰이는 에디터그리드 */
var grid_window_clearance = Ext.create('Ext.grid.Panel',{
	id : 'grid_window_clearance',
	plugins: ['clipboard', Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1})],
	requires: [
		'Ext.grid.plugin.Clipboard',
		'Ext.grid.selection.SpreadsheetModel'
	],
	autoWidth : true,
	height : 400,
	store : store_window_clearance,
	columns : [
		{ text : 'PKNO',					dataIndex : 'number',					width:120,	hidden:true		},
		{ text : '공구코드',				dataIndex : 'gpcode',					width:120		},
		{ text : '발주코드',				dataIndex : 'iv_id',					width:120		},
		{ text : '통관 상품코드',	dataIndex : 'cr_it_id',				width:160		},
		{ text : '통관 상품명',		dataIndex : 'cr_it_name',			width:260		},
		{ text : '통관수량',				dataIndex : 'cr_qty',					width:90,		editor: { allowBlank : false },		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '취소수량',				dataIndex : 'cr_cancel_qty',	width:90,		editor: { allowBlank : false },		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') }
	],
	listeners : {
		selectionchange: function(view, records) {
			var sm = grid_window_clearance.getSelectionModel();
		},
		edit: listenerEditFunc,
		afterrender: listenerAfterRendererFunc
	}
});


/************* ----------------  그리드 END -------------- ******************/