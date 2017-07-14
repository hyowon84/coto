/* 기간버튼에 따른 날짜 셋팅 함수  */
function setDate(v) {
	var sdate, edate;
	edate = new Date();


	switch(v.text) {
		case '오늘':
			sdate = new Date();
			break;
		case '일주일':
			sdate = Ext.Date.add(new Date(), Ext.Date.DAY, -7);
			break;
		case '한달':
			sdate = Ext.Date.add(new Date(), Ext.Date.MONTH, -1);
			break;
		case '3개월':
			sdate = Ext.Date.add(new Date(), Ext.Date.MONTH, -3);
			break;
		default:
			sdate = new Date();
			break;
	}

	df_sdate.setValue(sdate);
	df_edate.setValue(edate);
}

//공구선택 초기화
function resetGpinfo() {

	var sm = grid_gpinfo.getSelectionModel();
	sm.deselectAll();
	
	/*
	var params = {
		gpcode : ''
	}

	// >>회원정보 리프레시
	Ext.apply(store_mblist.getProxy().extraParams, params);
	store_mblist.load();
	*/
	
}


var pg_CellEdit = Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 2});
var pg_RowEdit = Ext.create('Ext.grid.plugin.RowEditing', {
	clicksToMoveEditor: 1,
	autoCancel: false
});

var selModel = {
	type: 'spreadsheet'
	,columnSelect: true	// replaces click-to-sort on header
};


var df_sdate = Ext.create('Ext.dateField.common');		var df_edate = Ext.create('Ext.dateField.common');
df_sdate.id = 'sdate';	df_sdate.name = 'sdate';	df_sdate.fieldLabel = 'S:';	df_sdate.labelWidth = 20;	df_sdate.width = 120;
df_edate.id = 'edate';	df_edate.name = 'edate';	df_edate.fieldLabel = 'E:';	df_edate.labelWidth = 20;	df_edate.width = 120;



/************* ----------------  그리드 START -------------- ******************/
/* 좌측 공구코드 */
var grid_gpinfo = Ext.create('Ext.grid.Panel',{
	plugins	: Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1}),
	selModel: Ext.create('Ext.selection.CheckboxModel'),
	remoteSort: true,
	autoLoad : true,
	autoWidth : true,
	height : 1000,
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
		{ text : ' ',						width : 50,		dataIndex : 'NULL'	}
	],
	store : store_gpinfo,
	tbar: [
		{
			xtype: 'button',
			text: '선택초기화',
			listeners : [{
				click : resetGpinfo
			}]
		},
		{	xtype: 'label',	text: '검색어 : ',		autoWidth:true,	style : 'font-weight:bold;'},
		{
			xtype: 'textfield',
			id : 'gp_keyword',
			name: 'gp_keyword',
			width : 100,
			style: 'padding:0px;',
			enableKeyEvents: true,
			listeners:{
				keydown:function(t,e){
					if(e.keyCode == 13){
						var params = {
							keyword : this.getValue()
						}

						grid_gpinfo.store.loadData([],false);
						Ext.apply(grid_gpinfo.store.getProxy().extraParams, params);
						Ext.getCmp('ptb_gpinfo').moveFirst();
					}
				}}
		},
		{
			xtype: 'button',
			text: '조회',
			listeners : [{
				click : function(){
					//store_mblist.loadData([],false);
					store_orderlist.loadData([],false);
					store_shiped_list.loadData([],false);

					/* 공구목록 선택된 레코드 */
					var sm = grid_gpinfo.getSelectionModel().getSelection();

					if(sm) {
						var v_gpcode = '';
						var v_gpcode_name = '';

						for(var i = 0; i < sm.length; i++) {	//sm[i].data
							v_gpcode += "'"+sm[i].data.gpcode + "',";
							v_gpcode_name += "'"+sm[i].data.gpcode_name + "',";
						}
						v_gpcode = v_gpcode.substr(0,v_gpcode.length-1);
						v_gpcode_name = v_gpcode_name.substr(0,v_gpcode_name.length-1);

						var params = {
							gpcode : v_gpcode
						}

						Ext.getCmp('grid_orderlist').setTitle('> "' + v_gpcode_name + ' 배송예정목록');
						Ext.getCmp('grid_shiped_list').setTitle('> "' + v_gpcode_name + ' 배송완료목록');
						//Ext.getCmp('hf_hphone').setValue(sm.get('hphone'));

						/* >>주문내역 리프레시 */
						Ext.apply(store_shiped_list.getProxy().extraParams, params);
						Ext.apply(store_orderlist.getProxy().extraParams, params);

						store_orderlist.load();
						store_shiped_list.load();
					}
				}
			}]
		}
	],
	bbar : {
		plugins: new Ext.ux.SlidingPager(),
		xtype : 'pagingtoolbar',
		id : 'ptb_gpinfo',
		store : store_gpinfo,
		displayInfo : true,
		displayMsg : '{0}/{1} Total - {2}',
		emptyMsg : 'No Data'
	},
	listeners : {
		selectionchange: function(view, records) {

			
		},		
		afterrender: listenerAfterRendererFunc
	}
});



/* 좌측 회원 */
var grid_mblist = Ext.create('Ext.grid.Panel',{
	id : 'grid_mblist',
	plugins	: ['clipboard'],
	selModel: Ext.create('Ext.selection.CheckboxModel'),
	remoteSort: true,
	autoLoad : false,
	autoWidth : true,
	height : 1000,
	store : store_mblist,
	viewConfig: {
		stripeRows: true,
		enableTextSelection: true,
		getRowClass: function(record, index) {
		}
	},
	tbar: [
					df_sdate,
					df_edate,
					{
						xtype: 'button',
						text: '오늘',
						listeners : [{
							click : setDate
						}]
					},
					{
						xtype: 'button',
						text: '일주일',
						listeners : [{
							click : setDate
						}]
					},
					{
						xtype: 'button',
						text: '한달',
						listeners : [{
							click : setDate
						}]
					},
					{
						xtype: 'button',
						text: '3개월',
						listeners : [{
							click : setDate
						}]
					}
	],
	columns : [
		{	text : '닉네임',				width : 140,		dataIndex : 'mb_nick'		},
		{	text : '이름',					width : 70,			dataIndex : 'mb_name'		},
		{ text : '연락처',				width : 120,		dataIndex : 'hphone'		},
		{ text : '퀵주문',				width : 90,			dataIndex : 'QCK_SUM_QTY',		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '퀵주문총액',		width : 120,		dataIndex : 'QCK_SUM_TOTAL',	style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '발송예정',			width : 90,			dataIndex : 'S40_SUM_QTY',		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '발송예정총액',	width : 120,		dataIndex : 'S40_SUM_TOTAL',	style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '발송불가',			width : 90,			dataIndex : 'NS40_SUM_QTY',	style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '발송불가총액',	width : 120,		dataIndex : 'NS40_SUM_TOTAL',	style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '전체주문수량',	width : 90,			dataIndex : 'SUM_QTY',			style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '전체주문총액',	width : 120,		dataIndex : 'SUM_TOTAL',		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') }
		
	],
	bbar : {
		plugins: new Ext.ux.SlidingPager(),
		xtype : 'pagingtoolbar',
		id : 'ptb_mblist',
		store : store_mblist,
		displayInfo : true,
		displayMsg : '{0}/{1} Total - {2}',
		emptyMsg : 'No Data'
	},
	listeners : {
		selectionchange: function(view, records) {
			
			store_orderlist.loadData([],false);
			store_shiped_list.loadData([],false);
			
			/* 회원목록의 선택된 레코드 */
			var sm = grid_mblist.getSelectionModel().getSelection()[0];
			
			if(sm) {
				Ext.getCmp('grid_orderlist').setTitle('> "'+sm.get('mb_nick')+'"님의 배송예정 목록');
				Ext.getCmp('grid_shiped_list').setTitle('> "'+sm.get('mb_nick')+'"님의 배송완료 목록');
				Ext.getCmp('hf_hphone').setValue(sm.get('hphone'));
				
				/* >>주문내역 리프레시 */
				Ext.apply(store_shiped_list.getProxy().extraParams, sm.data);
				Ext.apply(store_orderlist.getProxy().extraParams, sm.data);
				
				store_orderlist.load();
				store_shiped_list.load();				
			}
			
	 	}
	}
	
});



/* 우측 상단 주문신청내역 */
var grid_orderlist = Ext.create('Ext.grid.Panel',{
	id : 'grid_orderlist',
	headerPosition: 'left',
	title : '배송예정 목록',
	multiColumnSort: false,
	plugins: ['clipboard',Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1})],
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
		getRowClass: function(record, index) {
			var iv_stats = record.get('IV_STATS');
			var gpcode = record.get('gpcode');

			/* 국내도착 이상일경우에만 배송가능 */
			if (iv_stats >= 40 || gpcode == 'QUICK') {
				return 'cell_font_blue';
			}
		}
	},
	selModel: Ext.create('Ext.selection.CheckboxModel', {}),
	width : '100%',
	height	: 680,
	store : store_orderlist,
	columns : [
		{ text : 'project',			dataIndex : 'project',				hidden:true,	 sortable: true	},
		{ text : 'projectId',		dataIndex : 'projectId',			hidden:true	},
		{ text : 'taskId',			dataIndex : 'taskId',					hidden:true	},
		{ text : 'project',			dataIndex : 'project',				hidden:true },
		{ text : '주문자',			dataIndex : 'buyer',					width:120	},
		{ text : '주문일시',		dataIndex : 'od_date'				},
		{ text : '공구코드',		dataIndex : 'gpcode',					hidden:true	},		
		{ text : '공구명',			dataIndex : 'gpcode_name',		style:'text-align:center',	width:220	},
		{ text : '상태코드',		dataIndex : 'IV_STATS',				width:70,	hidden:true	},
		{ text : '입고상태',		dataIndex : 'IV_STATS_NAME',	width:120	},
		{ text : '주문ID',			dataIndex : 'od_id',					width:140	},
		{ text : '주문상태',		dataIndex : 'stats_name',			width:100	},
		{ text : 'img',					dataIndex : 'gp_img',					width:60,			renderer: function(value){	return '<img src="' + value + '" width=40 height=40 />';}			},
		{ text : '상품코드',		dataIndex : 'it_id',					width:160	},
		{ text : '품목별 메모',	dataIndex : 'it_memo',				width: 160,		style:'text-align:center',			editor:{allowBlank:false},	hidden:true },
		{ text : '품목명',			dataIndex : 'it_name',				width:450	},
		{ text : '주문가',			dataIndex : 'it_org_price',		sortable: true,		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '주문수량',		dataIndex : 'it_qty',					sortable: true,		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '주문총액',		dataIndex : 'total_price',		sortable: true,		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '관리자메모',	dataIndex : 'admin_memo',			width:120,	hidden:true	},
		{ text : '구매자메모',	dataIndex : 'memo',						width:120,	hidden:true	},		
		{ text : '통관수량',		dataIndex : 'CR_CNT',					width:120,	hidden:true	},
		{ text : '입고수량',		dataIndex : 'IP_CNT',					width:120,	hidden:true	},
		{ text : '예상재고',		dataIndex : 'real_jaego',			width:120,	hidden:true	}
	],
	tbar : [
		{	xtype: 'label',	text: '검색어 : ',		autoWidth:true,	style : 'font-weight:bold;'},
		{
			xtype: 'textfield',
			id : 'keyword',
			name: 'keyword',
			width : 100,
			style: 'padding:0px;',
			enableKeyEvents: true,
			listeners:{
				keydown:function(t,e){
					if(e.keyCode == 13){
						var params = {
							keyword : this.getValue()
							//sdate : df_sdate.rawValue,
							//edate : df_edate.rawValue
						}

						grid_orderlist.store.loadData([],false);
						Ext.apply(grid_orderlist.store.getProxy().extraParams, params);
						Ext.getCmp('ptb_orderlist').moveFirst();
					}
				}
			}
		},
		{
			xtype : 'textfield',
			fieldLabel: '품목별 메모 변경',
			labelWidth : 140,
			name: 'message',
			width : 300,
			style : 'float:left',
			enableKeyEvents: true,
			listeners : {
				keyup: function(f,e){
					var sm = grid_orderlist.getSelectionModel().getSelection();
					
					if(sm.length) {
						for(var i = 0; i < sm.length; i++) {
							sm[i].set('it_memo',this.getValue());
						}
					}
					else {
					}
				}
			}
		},
		{	
			id : 'output_order',
			text	: '추출',
			iconCls	: 'icon-table_print_add',
			handler : function() {
				
				//if( grid_mblist.getSelectionModel().getSelection() == '' ) {
				//	Ext.Msg.alert('알림','좌측 회원목록에서 회원을 선택하세요');
				//	return false;
				//}
				
				var sm = grid_orderlist.getSelection();
				if( sm == '' ) {
					Ext.Msg.alert('알림','상품들을 선택해주세요');
					return false;
				}

				var mblist_sm = grid_orderlist.getSelectionModel().getSelection()[0];
				winInvoice.setTitle(mblist_sm.get('clay_id')+'('+mblist_sm.get('name')+')님의 배송예정 목록');
				
				
				store_window_baesong.loadData([],false);
				for(var i = 0; i < sm.length; i++) {
					var rec = Ext.create('Task', {
									'taskId'				:	sm[i].data.taskId,
									'projectId'			:	sm[i].data.projectId,
									'project'				:	sm[i].data.project,
									'buyer'					: sm[i].data.buyer,
									'number'				:	sm[i].data.number,
									'gpcode'				:	sm[i].data.gpcode,
									'gpcode_name'		:	sm[i].data.gpcode_name,
									'gpstats'				:	sm[i].data.gpstats,
									'gpstats_name'	:	sm[i].data.gpstats_name,
									'od_id'					:	sm[i].data.od_id,
									'clay_id'				:	sm[i].data.clay_id,
									'stats'					:	sm[i].data.stats,
									'stats_name'		:	sm[i].data.stats_name,
									'gp_img'				:	sm[i].data.gp_img,
									'it_id'					:	sm[i].data.it_id,
									'it_name'				:	sm[i].data.it_name,
									'it_org_price'	:	sm[i].data.it_org_price,
									'it_qty'				:	sm[i].data.it_qty,
									'total_price'		:	sm[i].data.total_price,
									'od_date'				:	sm[i].data.od_date
					});
					store_window_baesong.add(rec);
				}
				
				
				var button = Ext.get('output_order');
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
				
				grid_window_baesong.reconfigure(store_window_baesong);
				
			}
		},
		{	xtype: 'label',		fieldLabel: 'alert',		style : 'margin-left:20px; font-weight:bold; font-size:1.5em; color:red;',
			text: '묶음대기중인것은 미리 포장하지 마시오, 배송나갈때 한번에 포장하시오!!!'
		}
	],
	bbar : {
		plugins: new Ext.ux.SlidingPager(),
		xtype : 'pagingtoolbar',
		id : 'ptb_orderlist',
		store : store_orderlist,
		displayInfo : true,
		displayMsg : '{0}/{1} Total - {2}',
		emptyMsg : 'No Data'
	},
	listeners : {
		
	}
});


/* 우측 하단 배송완료목록 */
var grid_shiped_list = Ext.create('Ext.grid.Panel',{
	id : 'grid_shiped_list',
	headerPosition: 'left',
	title : '배송완료 목록',
	multiColumnSort: false,
	plugins: ['clipboard'],
	height: 355,
	requires: [
		'Ext.grid.plugin.Clipboard'	//,'Ext.grid.selection.SpreadsheetModel'
	],
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
		getRowClass: function(record, index) {
			var c = record.get('gpstats');

			/* 국내도착 이상일경우에만 배송가능 */
			if (c >= 40) {
				return 'cell_font_blue';
			}
		}
	},
	columns : [
		{ text : 'projectId',				dataIndex : 'projectId',				hidden:true	},
		{ text : 'taskId',					dataIndex : 'taskId',						hidden:true	},
		{ text : 'project',					dataIndex : 'project',					hidden:true,	 	sortable: true },
		{ text : '주문일시',				dataIndex : 'od_date',					sortable: true	},
		{ text : '공구코드',				dataIndex : 'gpcode',						hidden:true	},
		{ text : '주문자',					dataIndex : 'buyer',						width:120	},
		{ text : '공구명',					dataIndex : 'gpcode_name',			style:'text-align:center',	width:220	},
		{ text : '주문ID',					dataIndex : 'od_id',						width:120	},
		{ text : '주문상태',				dataIndex : 'stats_name',				width:100	},
		{ text : '품목별 송장번호',	dataIndex : 'delivery_invoice',	width:130	},
		{ text : '최근 송장번호',		dataIndex : 'delivery_invoice2',width:130	},
		{ text : 'img',							dataIndex : 'gp_img',						width:60,			renderer: function(value){	return '<img src="' + value + '" width=40 height=40 />';}			},
		{ text : '상품코드',				dataIndex : 'it_id',						width:160	},
		{ text : '품목별 메모',			dataIndex : 'it_memo',					width: 160,		style:'text-align:center' },
		{ text : '품목명',					dataIndex : 'it_name',					width:450	},
		{ text : '주문가',					dataIndex : 'it_org_price',			sortable: true,		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '주문수량',				dataIndex : 'it_qty',						sortable: true,		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '주문총액',				dataIndex : 'total_price',			sortable: true,		style:'text-align:center',	align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') }
	],
	store : store_shiped_list,
	selModel: Ext.create('Ext.selection.CheckboxModel', {
	}),
	tbar : [
		{
			id : 'output_shiped',
			text	: '추출',
			iconCls	: 'icon-table_print_add',
			handler : function() {

				if( grid_mblist.getSelectionModel().getSelection() == '' ) {
					Ext.Msg.alert('알림','좌측 회원목록에서 회원을 선택하세요');
					return false;
				}

				var sm = grid_shiped_list.getSelection();
				if( sm == '' ) {
					Ext.Msg.alert('알림','상품들을 선택해주세요');
					return false;
				}

				var mblist_sm = grid_mblist.getSelectionModel().getSelection()[0];
				winInvoice.setTitle(mblist_sm.get('mb_nick')+'('+mblist_sm.get('mb_name')+')님의 배송완료 목록');


				store_window_baesong.loadData([],false);
				for(var i = 0; i < sm.length; i++) {
					var rec = Ext.create('Task', {
						'taskId'			:	sm[i].data.taskId,
						'projectId'		:	sm[i].data.projectId,
						'project'			:	sm[i].data.project,
						'buyer'				: sm[i].data.buyer,
						'number'			:	sm[i].data.number,
						'gpcode'			:	sm[i].data.gpcode,
						'gpcode_name'	:	sm[i].data.gpcode_name,
						'gpstats'			:	sm[i].data.gpstats,
						'gpstats_name':	sm[i].data.gpstats_name,
						'od_id'				:	sm[i].data.od_id,
						'stats_name'	:	sm[i].data.stats_name,
						'gp_img'			:	sm[i].data.gp_img,
						'it_id'				:	sm[i].data.it_id,
						'it_name'			:	sm[i].data.it_name,
						'it_org_price':	sm[i].data.it_org_price,
						'it_qty'			:	sm[i].data.it_qty,
						'total_price'	:	sm[i].data.total_price,
						'od_date'			:	sm[i].data.od_date
					});
					store_window_baesong.add(rec);
				}


				var button = Ext.get('output_shiped');
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

				grid_window_baesong.reconfigure(store_window_baesong);

			}
		}
	],
	bbar : {
		plugins: new Ext.ux.SlidingPager(),
		xtype : 'pagingtoolbar',
		store : store_shiped_list,
		displayInfo : true,
		displayMsg : '{0}/{1} Total - {2}',
		emptyMsg : 'No Data'
	},
	listeners : {
	}
});






/* 팝업윈도우 > 발주입력폼에 쓰이는 에디터그리드 */
var grid_window_baesong = Ext.create('Ext.grid.Panel',{
	id : 'grid_window_baesong',
	autoScroll : true,
	store : store_window_baesong,
	plugins: [pg_CellEdit],
	dockedItems: [
				{
					dock: 'top',
					xtype: 'toolbar',
					items: [{
								tooltip: 'Toggle the visibility of the summary row',
								text: '주석 숨김/활성',
								enableToggle: true,
								pressed: true,
								handler: function() {
									grid_window_baesong.getView().getFeature('group').toggleSummaryRow();
								}
							},
							{	xtype: 'label',		fieldLabel: 'alert',	autoWidth:true,		style : 'margin-left:20px; font-weight:bold; font-size:1.5em; color:red;',
								text: '묶음대기중인것은 미리 포장하지 마시오, 배송나갈때 한번에 포장하시오!!!'
							}
					]
				}				
	],
	features: [{
		id: 'group',
		ftype: 'groupingsummary',
		groupHeaderTpl: '{name}',
		hideGroupedHeader: true,
		enableGroupingMenu: false
	}],
	tbar : [
			{
				text	: '인쇄',
				iconCls	: 'icon-table_print',
				handler: function() {
					var smt = grid_mblist.getSelectionModel().getSelection()[0];

					//Ext.ux.grid.Printer.mainTitle = Ext.util.Format.date(new Date(),'Y-m-d g:i:s') +' 발주목록';
					
					Ext.ux.grid.Printer.mainTitle = Ext.util.Format.date(new Date(),'Y-m-d H:i:s ') + smt.get('mb_nick')+'('+smt.get('mb_name')+')님의 발송목록';
					
					var sm = grid_orderlist.getSelection();
					var tag_list = new Array();
					var text = '';
					
					for(var i = 0; i < sm.length; i++) {
						var memo = '';

						if(sm[i].data.memo) {
							memo += ' [*구매자메모: ' + sm[i].data.memo + ']';
						}
						if(sm[i].data.admin_memo) {
							memo += ' [*관리자메모: ' + sm[i].data.admin_memo+']';
						}
						
						tag_list[sm[i].data.od_id] = (sm[i].data.od_id + memo + '<br>');
					}
					
					for( var i in tag_list) {
						text += tag_list[i];
					}
					
					Ext.ux.grid.Printer.tag = text;
					Ext.ux.grid.Printer.print(grid_window_baesong);
				}
			},
	],
	columns : [
		{ text : '주문자',			dataIndex : 'buyer',			width:120	},
		{	header : 'IMG',				dataIndex : 'gp_img',			width:60,			renderer: function(value){	return '<img src="' + value + '" width=40 height=40 />';}			},
		{ text : '상품코드',		dataIndex : 'it_id',			width:120,		hidden:true	},
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
		{	header : 'Project',		dataIndex : 'project',		width:180,		sortable: true	},
		{	header : '주문상태',	dataIndex : 'stats_name',	style:'text-align:center',	align:'center',	sortable: true	},
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
	listeners : {
		selectionchange: function(view, records) {
			var sm = grid_window_baesong.getSelectionModel();
			//grid_window_baesong.down('#delJaego').setDisabled(!view.store.getCount());
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

/************* ----------------  그리드 END -------------- ******************/