
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

/*경매시작, 종료 함수*/
function auction_start(grid,val) {
	var sm = grid.getSelection();
	if( sm == '' ) {
		Ext.Msg.alert('알림','품목들을 선택해주세요');
		return false;
	}

	for(var i = 0; i < sm.length; i++) {
		sm[i].set('ac_yn',val);
	}
	grid.store.load();
}


/************* ----------------	1. 상품가격탭 START -------------- ******************/
	
//공동구매 목록
var grid_gplist = Ext.create('Ext.grid.Panel',{
	id : 'grid_gplist',
	remoteSort: true,
	//remoteFilter: true,
	autoLoad : true,
	height	: 770,
	columns : [
		{ text: '공구명', 		width: 210,	dataIndex : 'gpcode_name',	sortable: false		},
		{ text: '공구코드',	width: 120,	dataIndex : 'gpcode',		hidden:true	},
		{ text: '등록일',	 	width: 120,	dataIndex : 'reg_date',		sortable: true,		renderer: Ext.util.Format.dateRenderer('Y-m-d'),	field: { xtype: 'datefield' }},
		{ text: '시작일',	 	width: 120,	dataIndex : 'start_date',	sortable: true,		renderer: Ext.util.Format.dateRenderer('Y-m-d'),	field: { xtype: 'datefield' }},
		{ text: '종료일',	 	width: 120,	dataIndex : 'end_date',		sortable: true,		renderer: Ext.util.Format.dateRenderer('Y-m-d'),	field: { xtype: 'datefield' }},
		{ text: '품목수',		width: 120,	dataIndex : 'ITEM_CNT',		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') }		
	],
	store : store_gplist,
	viewConfig: {
		stripeRows: true,
		getRowClass: function(record, index) {
//			var c = record.get('NEED_IV_QTY');
//			if (c == 0) {
//				return 'cell_bg_skyblue';
//			}
		}
	},
	tbar : [
		{	xtype: 'label',	text: '검색어 : ',		autoWidth:true,	style : 'font-weight:bold;'},
		{
			xtype: 'textfield',
			id : 'gpkeyword',
			name: 'keyword',
			style: 'padding:0px;',
			enableKeyEvents: true,
			listeners:{
				keydown:function(t,e){
					if(e.keyCode == 13){
						grid_gplist.store.loadData([],false);
						Ext.apply(grid_gplist.store.getProxy().extraParams, {keyword : this.getValue()});
						grid_gplist.store.load();
					}
				}
			}
		}
	],
	bbar : {
		plugins: new Ext.ux.SlidingPager(),
		xtype : 'pagingtoolbar',
		store : store_gplist,
		displayInfo : true,
		displayMsg : '{0}/{1} Total - {2}',
		emptyMsg : 'No Data'
	},
	listeners : {
		selectionchange: function(view, records) {
			store_itemlist.loadData([],false);
			
			/* 공구목록의 선택된 레코드 */
			var sm = grid_gplist.getSelectionModel().getSelection()[0];
			
			if(sm) {
				Ext.getCmp('PRICE_CENTER').setTitle('> "'+sm.get('gpcode_name')+'"의 품목들');
			
				/* >>주문내역 리프레시 */
				Ext.apply(store_itemlist.getProxy().extraParams, sm.data);
				store_itemlist.load();
			
			} else {
				Ext.getCmp('PRICE_CENTER').setTitle('> ');
			}
			
	 	}
	}
});

/*노출여부*/
var combo_yesno = Ext.create('Ext.combobox.item.yesno');
combo_yesno.id = 'combo_yesno';	combo_yesno.setValue('');	combo_yesno.width = 70;

/*경매등록여부*/
var combo_yesno = Ext.create('Ext.combobox.item.yesno');
combo_yesno.id = 'combo_yesno';	combo_yesno.setValue('');	combo_yesno.width = 70;

var combo_pricetype = Ext.create('Ext.combobox.item.pricetype');
combo_pricetype.id = 'combo_pricetype';	combo_pricetype.setValue('');	combo_pricetype.width = 85;

var combo_metaltype = Ext.create('Ext.combobox.item.metaltype');
combo_metaltype.id = 'combo_metaltype';	combo_metaltype.setValue('');	combo_metaltype.width = 70;

var combo_spottype = Ext.create('Ext.combobox.item.spottype');
combo_spottype.id = 'combo_spottype';	combo_spottype.setValue('');	combo_spottype.width = 110;



/* 우측 하단 상품목록 */
var grid_itemlist = Ext.create('Ext.grid.Panel',{
	plugins: [Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1}) ],//pg_CellEdit  pg_RowEdit
	selModel: Ext.create('Ext.selection.CheckboxModel'),
	height	: 770,
	store : store_itemlist,
	viewConfig: {
		stripeRows: true,
		enableTextSelection: true
	},
	columns : [
		{
			xtype: 'gridcolumn',
			dataIndex: 'gp_use',
			text: '노출',
			style:'text-align:left',
			width: 65,
			align:'center',
			allowBlank: true,
			editor: Ext.create('Ext.combobox.item.yesno'),
			renderer: rendererCombo
		},
		{ groupIndex:'1',		text: 'img',						dataIndex : 'gp_img',					width: 60,		renderer: function(value){	return '<img src="' + value + '" width=40 height=40 />';}			},
		{ groupIndex:'1',		text: '상품코드',				dataIndex : 'gp_id',					width: 160		},
		{ groupIndex:'1',		text: '카테고리',				dataIndex : 'ca_id',					width: 100,		editor:{allowBlank:true}	},
		{ groupIndex:'1',		text: '재고위치',				dataIndex : 'location',				width: 100,		editor:{allowBlank:true}	},		
		{ groupIndex:'1',		text: '품목명',					dataIndex : 'gp_name',				width: 350,		editor:{allowBlank:false}	},
		{
			xtype: 'gridcolumn',
			dataIndex: 'gp_price_type',
			text: '가격유형',
			style:'text-align:center',
			align:'center',
			allowBlank: true,
			editor: Ext.create('Ext.combobox.item.pricetype'),
			renderer: rendererCombo
		},
		{ groupIndex:'1',		text: '판매가(￦)',			dataIndex : 'gp_price',				width: 140,		editor:{allowBlank:true},		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000')	},
		{ groupIndex:'1',		text: '달러가($)',				dataIndex : 'gp_usdprice',		width: 140,		editor:{allowBlank:true},		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000.00')	},
		{ groupIndex:'1',		text: '스팟시세가(￦)',	dataIndex : 'gp_realprice',		width: 140,		style:'text-align:center',		align:'right',					renderer: Ext.util.Format.numberRenderer('0,000')	},
		{ groupIndex:'1',		text: '매입가($)',				dataIndex : 'gp_price_org',		width: 140,		editor:{allowBlank:true},		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000.00')	},
		{
			xtype: 'gridcolumn',
			dataIndex: 'gp_metal_type',
			text: '유형',
			style:'text-align:center',
			align:'center',
			allowBlank: true,
			editor: Ext.create('Ext.combobox.item.metaltype'),
			renderer: rendererCombo
		},
		{ groupIndex:'1',		text: 'Oz',							dataIndex : 'gp_metal_don',		width: 60,		style :'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000.00'),	editor:{allowBlank:false}	},
		{
			xtype: 'gridcolumn',
			dataIndex: 'gp_spotprice_type',
			text: '스팟유형',
			style:'text-align:center',
			align:'center',
			allowBlank: true,
			editor: Ext.create('Ext.combobox.item.spottype'),
			renderer:function(value,metaData,record){
				var combo = metaData.column.getEditor();
				combo.allowBlank=true;
				
				if(value && combo && combo.store && combo.displayField){
					var index = combo.store.findExact(combo.valueField, value);
					if(index >= 0){
						return combo.store.getAt(index).get(combo.displayField);
					}
				}
				return (value) ? value : '' ;
			}
		},
		{ groupIndex:'1',		text: '+스팟시세',					dataIndex : 'gp_spotprice',		width: 120,			style :'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000.00'),	editor:{allowBlank:false}	},
		{ groupIndex:'1',		text: 'b.실재고(c-d+e)',		dataIndex : 'real_jaego',			width: 130,			style :'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000')		},		
		{ groupIndex:'1',		text: 'c.최초재고값',			dataIndex : 'jaego',					width: 120,			style :'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000'),	editor:{allowBlank:false}	},
		{ groupIndex:'1',		text: 'd.누적주문',				dataIndex : 'CO_SUM',					width: 120,			style :'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000')		},
		{ groupIndex:'1',		text: 'e.누적발주',				dataIndex : 'IV_SUM',					width: 90,			style :'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000')		},
		{ groupIndex:'1',		text: '가격옵션수',				dataIndex : 'OPT_CNT',				style :'text-align:center',				align:'right',			renderer: Ext.util.Format.numberRenderer('0,000') },
		{ groupIndex:'1',		text: '정렬순서',					dataIndex : 'gp_order',				width: 80,			editor:{allowBlank:true},		style:'text-align:center',	align:'right'	},
		{ groupIndex:'1',		text: '등록일',	 					dataIndex : 'gp_update_time',	width: 160,			sortable: true,		renderer: Ext.util.Format.dateRenderer('Y-m-d H:i:s')	},

		{
			xtype: 'gridcolumn',
			groupIndex:'2',
			dataIndex: 'ac_yn',
			text: '경매',
			style:'text-align:left',
			width: 65,
			align:'center',
			allowBlank: true,
			editor: Ext.create('Ext.combobox.item.yn'),
			renderer: rendererCombo
		},
		{ groupIndex:'2',		text: '경매진행코드',			dataIndex : 'ac_code',				width: 140,			style:'text-align:center',	align:'center'	},
		{ groupIndex:'2',		text: '경매마감일',	 			dataIndex : 'ac_enddate',			width: 160,			sortable: true,							field: { xtype: 'datefield',	format:'Y-m-d H:i:s' }},
		{ groupIndex:'2',		text: '경매수량',					dataIndex : 'ac_qty',					width: 120,			editor:{allowBlank:true},		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000')	},
		{ groupIndex:'2',		text: '경매시작가',				dataIndex : 'ac_startprice',	width: 140,			editor:{allowBlank:true},		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000')	},
		
		{ text: ' ',	 							dataIndex : ' ',							width: 60,			sortable: false	}
	],
	tbar : [
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
								grid_itemlist.store.loadData([],false);
							 	Ext.apply(grid_itemlist.store.getProxy().extraParams, {keyword : this.getValue()});
							 	grid_itemlist.store.load();
							}
						}
					}
				},
				combo_yesno,
				combo_pricetype,
				combo_metaltype,
				combo_spottype,
				{
					id		: 'iv_stats_update',
					text	: '변경',
					iconCls	: 'icon-table_edit',
					handler : function() {
						
						var sm = grid_itemlist.getSelection();
						if( sm == '' ) {
							Ext.Msg.alert('알림','품목들을 선택해주세요');
							return false;
						}

						var yesno = combo_yesno.getValue();
						var pricetype = combo_pricetype.getValue();
						var metaltype = combo_metaltype.getValue();
						var spottype = combo_spottype.getValue();
						
						for(var i = 0; i < sm.length; i++) {
							if(yesno) sm[i].set('gp_use',yesno);
							if(pricetype) sm[i].set('gp_price_type',pricetype);
							if(metaltype) sm[i].set('gp_metal_type',metaltype);
							if(spottype) sm[i].set('gp_spotprice_type',spottype);
						}

						//값이 있을경우 업데이트
						if(pricetype || metaltype || spottype) {
							grid_itemlist.store.sync();
						}
					}
				},
				{
					text	: '인쇄',
					iconCls	: 'icon-table_print',
					handler: function() {
						var sm = grid_gplist.getSelectionModel().getSelection()[0];
						Ext.ux.grid.Printer.mainTitle = sm.get('gpcode_name')+' 상품목록';
						Ext.ux.grid.Printer.print(grid_itemlist);
					}
				},
				{
					text	: '경매시작',
					iconCls	: 'icon-bell',
					handler: function() {
						auction_start(grid_itemlist,'Y');						
					}
				},
				{
					text	: '경매종료',
					iconCls	: 'icon-cancel',
					handler: function() {
						auction_start(grid_itemlist,'N');
					}
				},
				{
					text	: '상품수정PAGE',
					handler: function() {
						var sm = grid_itemlist.getSelectionModel().getSelection();
						if( sm == '' ) {
							Ext.Msg.alert('알림','품목을 선택해주세요');
							return false;
						}

						for(var i=0; i < sm.length; i++) {
							openPopup('/adm/shop_admin/grouppurchaseform.php?w=u&gp_id='+sm[i].get('gp_id'));
						}
					}
				},
				{
					text	: '경매상품PAGE',
					handler: function() {
						var sm = grid_itemlist.getSelectionModel().getSelection();
						if( sm == '' ) {
							Ext.Msg.alert('알림','품목을 선택해주세요');
							return false;
						}


						for(var i=0; i < sm.length; i++) {
							openPopup('/shop/auction.php?gp_id='+sm[i].get('gp_id'));
						}
					}
				}
	],
	bbar : {
		plugins: new Ext.ux.SlidingPager(),
		xtype : 'pagingtoolbar',
		store : store_itemlist,
		displayInfo : true,
		displayMsg : '{0}/{1} Total - {2}',
		emptyMsg : 'No Data'
	},
	listeners : {
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
/************* ----------------	1. 상품가격탭 END -------------- ******************/


/************* ----------------	2. 경매탭 START -------------- ******************/

/* 좌측 경매상품목록 */
var grid_aucPrdList = Ext.create('Ext.grid.Panel',{
	plugins: [Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1}) ],//pg_CellEdit  pg_RowEdit
	selModel: Ext.create('Ext.selection.CheckboxModel'),
	height	: 770,
	store : store_aucPrdList,
	viewConfig: {
		stripeRows: true,
		enableTextSelection: true
	},
	columns : [
		{
			xtype: 'gridcolumn',
			groupIndex:'2',
			dataIndex: 'ac_yn',
			text: '경매',
			style:'text-align:left',
			width: 65,
			align:'center',
			allowBlank: true,
			editor: Ext.create('Ext.combobox.item.yn'),
			renderer: rendererCombo
		},
		{ groupIndex:'2',		text: '경매진행코드',	dataIndex : 'ac_code',						width: 125,		style:'text-align:center',	align:'center'	},
		{ groupIndex:'2',		text: '경매마감일',	 	dataIndex : 'ac_enddate',					width: 160,		sortable: true,							renderer: Ext.util.Format.dateRenderer('Y-m-d H:i:s'),		field: { xtype: 'datefield',	format:'Y-m-d H:i:s' }},
		{ groupIndex:'2',		text: '현재입찰가',		dataIndex : 'MAX_BID_LAST_PRICE',	width: 130,		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000')	},
		{ groupIndex:'2',		text: '최고입찰가',		dataIndex : 'MAX_BID_PRICE',			width: 130,		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000')	},
		{ groupIndex:'2',		text: '경매시작가',		dataIndex : 'ac_startprice'	,			width: 130,		editor:{allowBlank:true},		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000')	},
		{ groupIndex:'2',		text: '경매수량',			dataIndex : 'ac_qty',							width: 90,		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000')	},
		{ groupIndex:'1',		text: 'img',					dataIndex : 'gp_img',							width: 60,		renderer: function(value){	return '<img src="' + value + '" width=40 height=40 />';}			},
		{ groupIndex:'1',		text: '상품코드',			dataIndex : 'gp_id',							width: 160		},
		{ groupIndex:'1',		text: '품목명',				dataIndex : 'gp_name',						width: 350,		editor:{allowBlank:false}	},
		{ 									text: ' ',	 					dataIndex : ' ',									width: 60,		sortable: false	}
	],
	tbar : [
		{	xtype: 'label',	text: '검색어 : ',		autoWidth:true,	style : 'font-weight:bold;'},
		{
			xtype: 'textfield',
			id : 'apl_keyword',
			name: 'keyword',
			style: 'padding:0px;',
			enableKeyEvents: true,
			listeners:{
				keydown:function(t,e){
					if(e.keyCode == 13){
						grid_aucPrdList.store.loadData([],false);
						Ext.apply(grid_aucPrdList.store.getProxy().extraParams, {keyword : this.getValue()});
						grid_aucPrdList.store.load();
					}
				}
			}
		},
		{
			text	: '경매시작',
			iconCls	: 'icon-bell',
			handler: function() {
				auction_start(grid_aucPrdList,'Y');
			}
		},
		{
			text	: '경매종료',
			iconCls	: 'icon-cancel',
			handler: function() {
				auction_start(grid_aucPrdList,'N');
			}
		},
		{
			text	: '경매페이지이동',
			handler: function() {
				var sm = grid_aucPrdList.getSelectionModel().getSelection();
				if( sm == '' ) {
					Ext.Msg.alert('알림','품목들을 선택해주세요');
					return false;
				}

				openPopup('/shop/auction.php?gp_id='+sm[0].get('gp_id'));
			}
		}
	],
	bbar : {
		plugins: new Ext.ux.SlidingPager(),
		xtype : 'pagingtoolbar',
		store : store_aucPrdList,
		displayInfo : true,
		displayMsg : '{0}/{1} Total - {2}',
		emptyMsg : 'No Data'
	},
	listeners : {
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
		},
		selectionchange: function(view, records) {
			store_aucBidList.loadData([],false);

			/* 경매상품목록 선택된 레코드 */
			var sm = grid_aucPrdList.getSelectionModel().getSelection()[0];

			if(sm) {
				Ext.getCmp('AUCTION_CENTER').setTitle(sm.get('ac_code')+': '+sm.get('gp_name')+'"의 입찰기록');

				/* >>주문내역 리프레시 */
				Ext.apply(store_aucBidList.getProxy().extraParams, sm.data);
				store_aucBidList.load();

			} else {
				Ext.getCmp('AUCTION_CENTER').setTitle(': ');
			}

		}
	}
});

var grid_aucBidList = Ext.create('Ext.grid.Panel',{
	id : 'grid_aucBidList',
	height	: 770,
	store : store_aucBidList,
	viewConfig: {
		stripeRows: true,
		enableTextSelection: true,
		getRowClass: function(record, index) {
//			var c = record.get('NEED_IV_QTY');
//			if (c == 0) {
//				return 'cell_bg_skyblue';
//			}
		}
	},
	columns : [
		{ text: '경매코드',		 		width: 130,	dataIndex : 'ac_code',					sortable: false	},
		{ text: '상품코드',				width: 120,	dataIndex : 'it_id',						sortable: false	},
		{ text: '상품명',					width: 220,	dataIndex : 'it_name',					sortable: false,		hidden:true	},
		{ text: '입찰상태',				width: 120,	dataIndex : 'bid_stats_name',		sortable: false},
		{ text: '입찰수량',			 	width: 90,	dataIndex : 'bid_qty',					sortable: false,			style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text: '입찰일시.밀리초',	width: 180,	dataIndex : 'bid_date'	},
		{ text: '현재입찰가',		 	width: 120,	dataIndex : 'bid_last_price',		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text: '희망입찰가',		 	width: 120,	dataIndex : 'bid_price',				style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text: '입찰회원계정',	 	width: 120,	dataIndex : 'mb_id'	},
		{ text: '이름',	 					width: 120,	dataIndex : 'mb_name'	},
		{ text: '닉네임',				 	width: 120,	dataIndex : 'mb_nick'	},
		{ text: '연락처',				 	width: 120,	dataIndex : 'mb_hp'	}
		
	],
	tbar : [
		{	xtype: 'label',	text: '검색어 : ',		autoWidth:true,	style : 'font-weight:bold;'},
		{
			xtype: 'textfield',
			id : 'abl_keyword',
			name: 'keyword',
			style: 'padding:0px;',
			enableKeyEvents: true,
			listeners:{
				keydown:function(t,e){
					if(e.keyCode == 13){
						grid_aucBidList.store.loadData([],false);
						Ext.apply(grid_aucBidList.store.getProxy().extraParams, {keyword : this.getValue()});
						grid_aucBidList.store.load();
					}
				}
			}
		},
		{
			id: 'abl_order',
			text: '낙찰건 주문변환',
			iconCls: 'icon-bell',
			handler: function () {
				var sm = grid_aucBidList.getSelection()[0];

				if (sm == '') {
					Ext.Msg.alert('알림', '입찰기록을 선택해주세요');
					return false;
				}

				var jsonData = "[" + Ext.encode(sm.data) + "]";
				
				
				Ext.MessageBox.confirm('알림',sm.data.mb_name+'님의 입찰건('+sm.data.bid_last_price+')을 주문입력합니다' , function(btn, text) {
					if(btn == 'yes') {

						Ext.Ajax.request({
							url: "/adm/extjs/product/crud/bid_order.insert.php",
							params: { data : jsonData},
							success: function (result, request) {
								var result = Ext.util.JSON.decode(result.responseText);
								Ext.MessageBox.alert('알림', result.message);
							},
							failure: function (result, request) {
								var result = Ext.util.JSON.decode(result.responseText);
								Ext.MessageBox.alert('알림', result.message);
							}
						});
						
					}
				}, function(){

				});
				
			}
		}
	],
	bbar : {
		plugins: new Ext.ux.SlidingPager(),
		xtype : 'pagingtoolbar',
		store : store_aucBidList,
		displayInfo : true,
		displayMsg : '{0}/{1} Total - {2}',
		emptyMsg : 'No Data'
	},
	listeners : {
		
	}
});



/* 팝업윈도우 > 발주입력폼에 쓰이는 에디터그리드 */
var grid_window_product = Ext.create('Ext.grid.Panel',{
	id : 'grid_window_product',
//	width: '100%',
	autoScroll : true,
//	frame: false,
//	autowidth: true,
//	autoHeight: true,
//	flex : 1,
	store : store_window_product,
	plugins: [pg_CellEdit],
	dockedItems: [{
		dock: 'top',
		xtype: 'toolbar',
		items: [{
			tooltip: 'Toggle the visibility of the summary row',
			text: 'Toggle Summary',
			enableToggle: true,
			pressed: true,
			handler: function() {
				grid_window_product.getView().getFeature('group').toggleSummaryRow();
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
		{ text: '공구코드',	dataIndex : 'gpcode',		hidden:true	},
		{ text: '공구명',		dataIndex : 'gpcode_name',	hidden:true,		width	:220 },
		{ text: '날짜',		dataIndex : 'od_date',		sortable: true,		renderer: Ext.util.Format.dateRenderer('Y-m-d'),	field: { xtype: 'datefield' },		hidden:true	},
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
									var sm = grid_gplist.getSelectionModel().getSelection()[0];
									Ext.ux.grid.Printer.mainTitle = sm.get('mb_nick')+'('+sm.get('mb_name')+')님의 발송목록';
										Ext.ux.grid.Printer.print(grid_window_product);
								}
			},
	],
	listeners : {
		selectionchange: function(view, records) {
			var sm = grid_window_product.getSelectionModel();
			//grid_window_product.down('#delJaego').setDisabled(!view.store.getCount());
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

/************* ----------------	그리드 END -------------- ******************/