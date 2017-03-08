var pg_CellEdit = Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 2});
var pg_RowEdit = Ext.create('Ext.grid.plugin.RowEditing', {	clicksToEdit: 2,	clicksToMoveEditor: 1,	autoCancel: false	});

var selModel = {
	type: 'spreadsheet'
	,columnSelect: true	// replaces click-to-sort on header
};

function getParams() {
	var v_params;
	
	v_params = {	
		keyword : Ext.getCmp('keyword').getValue(),
		stats : Ext.getCmp('combo_orderStats').getValue(),
		sdate : df_sdate.rawValue,
		edate : df_edate.rawValue
 	}
	
	return v_params;
}

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


/************* ----------------	그리드 START -------------- ******************/
var combo_deliverytype = Ext.create('Ext.combobox.order.delivery_type');	combo_deliverytype.id = 'combo_deliverytype';	combo_deliverytype.width = 180;
var combo_cashreceipt_yn = Ext.create('Ext.combobox.order.cashreceipt_yn');	combo_cashreceipt_yn.id = 'combo_cashreceipt_yn';
var combo_cashreceipt_type = Ext.create('Ext.combobox.order.cashreceipt_type');	combo_cashreceipt_type.id = 'combo_cashreceipt_type';
//var combo_orderStats = Ext.create('Ext.combobox.order.stats');		combo_orderStats.id = 'combo_orderStats';

combo_deliverytype.fieldLabel = '배송유형';
combo_cashreceipt_yn.fieldLabel = '현금영수증';
combo_cashreceipt_type.fieldLabel = '현.영 유형';

var combo_orderStats = Ext.create('Ext.combobox.order.stats', {
	extend: 'Ext.form.ComboBox',
	xtype: 'combobox',
	editable: false,
	displayField: 'name',
	valueField: 'value',
	name: 'stats',
	value : '',
	fieldLabel : '검색',
	store: Ext.create('Ext.store.order.stats'),
	id : 'combo_orderStats',
	labelWidth : 60,
	width : 230,
	listeners : {
		change: function(view, records) {
			store_orderlist.loadData([],false);
			Ext.apply(store_orderlist.getProxy().extraParams, getParams());
			store_orderlist.load();
	 	}
	}
});

var combo_editStats = Ext.create('Ext.combobox.order.stats', {
	extend: 'Ext.form.ComboBox',
	xtype: 'combobox',
	editable: false,
	displayField: 'name',
	valueField: 'value',
	name: 'stats',
	value : '',
	fieldLabel : '변경',
	store: Ext.create('Ext.store.order.stats'),
	id : 'combo_editStats',
	labelWidth : 60,
	width : 190
});

var combo_smsStats = Ext.create('Ext.combobox.order.stats', {
	extend: 'Ext.form.ComboBox',
	xtype: 'combobox',
	editable: false,
	displayField: 'name',
	valueField: 'value',
	name: 'stats',
	value : '',
	fieldLabel : '변경',
	store: Ext.create('Ext.store.order.smsex'),
	id : 'combo_smsStats',
	labelWidth : 60,
	width : 190
});


var df_sdate = Ext.create('Ext.dateField.common');		var df_edate = Ext.create('Ext.dateField.common');
df_sdate.id = 'sdate';	df_sdate.name = 'sdate';	df_sdate.fieldLabel = '시작일';
df_edate.id = 'edate';	df_edate.name = 'edate';	df_edate.fieldLabel = '종료일';






/************* ----------------	그리드 START -------------- ******************/

/* 우측 주문 목록 */
var grid_orderlist = Ext.create('Ext.grid.Panel',{
	plugins	: Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1}),
	selModel: Ext.create('Ext.selection.CheckboxModel'),
	width : '100%',
	height	: 660,
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
		getRowClass: orderStatsColorSetting,
		enableTextSelection: true
	},
	store : store_orderlist,
	columns	: [
		{ text: '공구코드',			dataIndex: 'gpcode',						width: 100,		hidden:true	},
		{ text: 'number',				dataIndex: 'number',						width: 100,		hidden:true	},
		{ text: '공구명', 				dataIndex: 'gpcode_name',				width: 120,		style:'text-align:center'	},
		{ text: '주문번호', 			dataIndex: 'od_id',							width: 130,		style:'text-align:center',		align:'center'	},
		{ text: '주문일시',			dataIndex: 'od_date',						width: 150,		renderer: Ext.util.Format.dateRenderer('Y-m-d H:i:s'),	hidden:true	},
		{ text: '주문상태', 			dataIndex: 'stats',							width: 120,		editor: Ext.create('Ext.combobox.order.stats'),		renderer: rendererCombo	},
		{ text: '개별송장번호',	dataIndex: 'delivery_invoice',	width: 120,		style:'text-align:center'	},
		{ text: '총 합계금액',		dataIndex: 'TOTAL_PRICE',				width: 120,		style:'text-align:center',		align:'right',			renderer: Ext.util.Format.numberRenderer('0,000')	},
		{ text: '품목판매금액',	dataIndex: 'SELL_PRICE',				width: 120,		style:'text-align:center',		align:'right',			renderer: Ext.util.Format.numberRenderer('0,000'), 	summaryType : 'sum',				summaryRenderer : rendererSummaryFormat	},
		{ text: '판매단가',			dataIndex: 'it_org_price',			width: 120,		style:'text-align:center',		align:'right',			editor:{allowBlank:false},		renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text: '수량',					dataIndex: 'it_qty',						width: 70,		style:'text-align:center',		align:'right',			editor:{allowBlank:false},		renderer: Ext.util.Format.numberRenderer('0,000'),		summaryType : 'sum',		summaryRenderer : rendererSummaryFormat	},
		{ text: '주문자',				dataIndex: 'name',							width: 70,		style:'text-align:center',		align:'center'	},
		{ text: '닉네임',				dataIndex: 'clay_id',	 					width: 120,		style:'text-align:center',		align:'center'	},
		{ text: 'IMG', 					dataIndex: 'gp_img',						width: 50,		renderer:rendererImage 		},
		{ text: '상품코드', 			dataIndex: 'it_id',							width: 120,		hidden:true	},
		{ text: '품목명', 				dataIndex: 'it_name',						width: 260		},
		{ text: '배송유형',			dataIndex: 'delivery_type_nm',	width: 120,		style:'text-align:center',		align:'center'	},
		{ text: '배송비',				dataIndex: 'delivery_price',		width: 70,		style:'text-align:center',		align:'center',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text: 'H.P',					dataIndex: 'hphone',	 					width: 120,		style:'text-align:center',		align:'center'	},
		{ text: '결제방법', 			dataIndex: 'paytype_nm',				width: 120,		style:'text-align:center',		align:'center'	},
		{ text: '입금자명',			dataIndex: 'receipt_name',			width: 80,		style:'text-align:left',		align:'left'	},
		{ text: '송장번호',			dataIndex: 'delivery_invoice',	width: 130,		style:'text-align:center',		align:'center'	},
		{ text: '기본주소',			dataIndex: 'addr1',	 						width: 200,		style:'text-align:center'	},
		{ text: '기본주소(신)',	dataIndex: 'addr1_2',						width: 200,		style:'text-align:center'	},
		{ text: '상세주소',			dataIndex: 'addr2',	 						width: 200,		style:'text-align:center'	},
		{ text: 'ZIP',					dataIndex: 'zip',								width: 100,		style:'text-align:center',		align:'center'	},
		{ text: '현.영', 				dataIndex: 'cash_receipt_yn',				width: 80,		style:'text-align:left',		align:'left'	},
		{ text: '현.영 유형',		dataIndex: 'cash_receipt_type_nm',	width: 120,		style:'text-align:center',		align:'left'	},
		{ text: '현.영 정보',		dataIndex: 'cash_receipt_info',			width: 170,		style:'text-align:center',		align:'left'	}
	],
	dockedItems: [
		{
			xtype : 'toolbar',
			dock : 'top',
			items : [
				combo_orderStats,
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
				},
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
								v_param = getParams();
								grid_orderlist.store.loadData([],false);
								Ext.apply(grid_orderlist.store.getProxy().extraParams, v_param);
								Ext.getCmp('ptb_orderlist').moveFirst();
							}
						}
					}
				}
			]
		},
		{
			xtype : 'toolbar',
			dock : 'top',
			items : [
				combo_editStats,
				{
					id		: 'ordStatsUpdBtn',
					text	: '변경',
					iconCls	: 'icon-table_edit',
					handler : function() {
						
						var sm = grid_orderlist.getSelection();
						if( sm == '' ) {
							Ext.Msg.alert('알림','품목들을 선택해주세요');
							return false;
						}
						
						var editStats = combo_editStats.getValue();
						var deliverytype = combo_deliverytype.getValue();
						var cashreceipt_yn = combo_cashreceipt_yn.getValue();
						var cashreceipt_type = combo_cashreceipt_type.getValue();
						
						for(var i = 0; i < sm.length; i++) {
							if(editStats) sm[i].set('stats',editStats);
							if(deliverytype) sm[i].set('delivery_type',deliverytype);
							if(cashreceipt_yn) sm[i].set('cash_receipt_yn',cashreceipt_yn);
							if(cashreceipt_type) sm[i].set('cash_receipt_type',cashreceipt_type);
						}
						
					}
				},
				{
					text	: '인쇄',
					iconCls	: 'icon-table_print',
					handler: function() {
						Ext.ux.grid.Printer.mainTitle = '선택된 주문목록';
						Ext.ux.grid.Printer.print(grid_orderlist);
					}
				},
				{
					text	: 'SMS',
					id		: 'sendSMS',
					iconCls	: 'icon-sms',
					handler: function() {
						var sm = grid_orderlist.getSelection();
						
						if( sm == '' ) {
							Ext.Msg.alert('알림','주문내역들을 선택해주세요');
							return false;
						}
						
						store_winSms.removeAll();
						var v_prev_od_id;
						
						for(var i = 0; i < sm.length; i++) {
							sm[i].data.message = v_SmsMsg[sm[i].data.stats];
							
							/*중복주문번호에 대해서는 중복발송 방지위해 필터링*/
							if(sm[i].data.od_id == v_prev_od_id) continue;
							
							var stats = sm[i].data.stats;
							if( (stats >= 10 && stats <= 40) || stats == 90) 
								stats = stats;
							else
								stats = '';
							
							var rec = Ext.create('model.SmsSendForm', {
											'stats'			: stats,			
											'message'		: sm[i].data.message,
											'nickname'		: sm[i].data.nickname,
											'name'			: sm[i].data.name,
											'hphone'			: sm[i].data.hphone,
											'od_id'			: sm[i].data.od_id,
											'TOTAL_PRICE'	: sm[i].data.TOTAL_PRICE,
											'it_name'		: sm[i].data.it_name
							});
							store_winSms.add(rec);
							
							v_prev_od_id = sm[i].data.od_id;
						}
						
						
						var button = Ext.get('sendSMS');
						button.dom.disabled = true;
						//this.container.dom.style.visibility=true
						
						if (winSmsForm.isVisible()) {
							winSmsForm.hide(this, function() {
								button.dom.disabled = false;
							});
						} else {
							winSmsForm.show(this, function() {
								button.dom.disabled = false;
							});
						}
						
						//grid_winSms.reconfigure(store_winSms);
						
					}
				}
			]		
		}
	],
	bbar : {
		plugins: new Ext.ux.SlidingPager(),
		id : 'ptb_orderlist',
		xtype : 'pagingtoolbar',
		store : store_orderlist,
		displayInfo : true,
		displayMsg : '{0}/{1} Total - {2}',
		emptyMsg : 'No Data'
	},
	listeners : {
		selectionchange: function(view, records) {
			
			var sm = grid_orderlist.getSelectionModel().getSelection()[0];
			
			if(sm) {
				Ext.getCmp('navi_center').setTitle('> "'+sm.get('gpcode_name')+"("+sm.get('od_id')+")"+'"의 주문정보');
				sm.data.admin_memo = sm.data.admin_memo.replace(/<br>/gi, "\r\n");	//개행문자를 <BR>로 변경한걸 다시 원상복구
				sm.data.memo = sm.data.memo.replace(/<br>/gi, "\r\n");
				Ext.getCmp('frmOrderInfo').loadRecord(sm);
				
				var v_param = {od_id : sm.data.od_id}

				store_smslog.loadData([],false);
				store_banklog.loadData([],false);
				store_log.loadData([],false);

				Ext.apply(store_smslog.getProxy().extraParams, v_param);
				Ext.apply(store_banklog.getProxy().extraParams, v_param);
				Ext.apply(store_log.getProxy().extraParams, v_param);

				store_smslog.load();
				store_banklog.load();
			 	store_log.load();

			} else {
				Ext.getCmp('navi_center').setTitle('> 주문내역을 선택하세요~');
				Ext.getCmp('frmOrderInfo').getForm().reset();
			}
			
	 	},
		edit: listenerEditFunc,
		afterrender: listenerAfterRendererFunc
	}
});


/* SMS전송예정 그리드 */
var grid_winSms = Ext.create('Ext.grid.Panel',{
	plugins	: Ext.create('Ext.grid.plugin.RowEditing', {	clicksToEdit: 1,	clicksToMoveEditor: 1,	autoCancel: false	}),
	selModel	: Ext.create('Ext.selection.CheckboxModel'),
	width : 1060,
	height : 200,
	columns : [
		{	
			text : '보낼유형',
			dataIndex : 'stats',
			width : 120,
			editor : {	//Ext.create('Ext.combobox.order.smsex')
				xtype: 'combobox',
				displayField: 'name',
				store: Ext.create('Ext.store.order.smsex'),
				valueField: 'value',
				listeners: {
					select: function(combo, newValue){
						var text = v_SmsMsg[newValue.data.value];
						var sm = grid_winSms.getSelectionModel().getSelection()[0];
						
						sm.set('message',text);
					}
				}
			},
			renderer: rendererCombo
		},
		{ text : '보낼내용', 	dataIndex : 'message',			width : 450,	sortable: false		},
		{ text : '닉네임',			dataIndex : 'nickname',			width : 120	},
		{ text : '주문자',			dataIndex : 'name',					width : 120	},
		{ text : 'H.P',				dataIndex : 'hphone',				width : 120	},
		{ text : '주문ID', 		dataIndex : 'od_id',				width : 150,	sortable: true		},
		{ text : '총주문금액',	dataIndex : 'TOTAL_PRICE',	width : 120,	style:'text-align:center',		align :'right',		renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '참고품목', 	dataIndex : 'it_name',			width : 450,	sortable: true		}
		
	],
	store : store_winSms,
	tbar : [
				combo_smsStats,
				{
					id		: 'smsStatsUpdBtn',
					text	: '변경',
					iconCls	: 'icon-table_edit',
					handler : function() {
						
						var sm = grid_winSms.getSelection();
						if( sm == '' ) {
							Ext.Msg.alert('알림','품목들을 선택해주세요');
							return false;
						}
						
						var smsStats = combo_smsStats.getValue();
						
						for(var i = 0; i < sm.length; i++) {
							if(smsStats) sm[i].set('stats',smsStats);
							
							sm[i].set('message', v_SmsMsg[sm[i].data.stats]);
						}
						
					}
				}
	],
	viewConfig: {
		stripeRows: true,
		getRowClass: orderStatsColorSetting
	},
	listeners : {
		selectionchange: function(view, records) {
			var sm = grid_winSms.getSelectionModel().getSelection()[0];
			
			if(sm) {
				if(sm.data.message != '') sm.data.message = sm.data.message.replace(/<br>/gi, "\r\n");	//개행문자를 <BR>로 변경한걸 다시 원상복구
				Ext.getCmp('frm_WinSms').loadRecord(sm);
				Ext.getCmp('sizecnt').setValue(sm.data.message.length);
			}
			else {
				Ext.getCmp('frm_WinSms').reset();
			}
	 	},
		edit: function (editor, e, eOpts) {
			if(globalData.temp == null) {
				globalData.temp = [];
			}
			globalData.temp.push([editor.context.rowIdx, editor.context.field, editor.context.originalValue]);
		},		
		afterrender: listenerAfterRendererFunc
	}
});


/* SMS전송 로그 */
var grid_smslog = Ext.create('Ext.grid.Panel',{
	width : 650,
	height : 200,
	columns : [
		{ text : 'no', 				dataIndex : 'wr_no',				width : 60,		sortable: true		},
		{ text : '메시지내용',	dataIndex : 'wr_message',		width : 450,	sortable: false		},
		{ text : '전송날짜',		dataIndex : 'wr_datetime',	width : 150,	renderer: Ext.util.Format.dateRenderer('Y-m-d H:i:s')	},
		{ text : '받는사람',		dataIndex : 'wr_target',		width : 120	},
		{ text : '보낸사람',		dataIndex : 'wr_reply',			width : 120	}
	],
	store : store_smslog,
	bbar : {
		plugins: new Ext.ux.SlidingPager(),
		xtype : 'pagingtoolbar',
		store : store_smslog,
		displayInfo : true,
		displayMsg : '{0}/{1} Total - {2}',
		emptyMsg : 'No Data'
	}
});


/* 입금내역 로그 */
var grid_banklog = Ext.create('Ext.grid.Panel',{
	plugins	: Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1}),
	width : 650,
	height : 150,
	columns : [
		{ text : '확인여부',		dataIndex : 'BANK_STAT',		width : 80,	style:'text-align:center'	},
		{ text : '거래일', 		dataIndex : 'tr_date',			width : 100,	sortable: true		},
		{ text : '거래시간',		dataIndex : 'tr_time',			width : 100,	sortable: false		},
		{ text : '입금액',			dataIndex : 'input_price',	width : 120,	style:'text-align:center',		align :'right',	renderer: Ext.util.Format.numberRenderer('0,000')	},
		{ text : '입금자명',		dataIndex : 'trader_name',	width : 90,		style:'text-align:center',		align :'center'	},
		{ text : '연결주문ID',	dataIndex : 'admin_link',		width : 120,	style:'text-align:center',		editor:{allowBlank:false}	},
		{ text : '메모',				dataIndex : 'admin_memo',		width : 120,	style:'text-align:center',		editor:{allowBlank:false}	}
	],
	store : store_banklog,
	bbar : {
		plugins: new Ext.ux.SlidingPager(),
		xtype : 'pagingtoolbar',
		store : store_banklog,
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


/* 로그 */
var grid_log = Ext.create('Ext.grid.Panel',{
    width : 650,
	height	: 200,
	columns : [
		{ text : '변경유형', 	dataIndex : 'memo',					width : 90,		sortable: true		},
		{ text : '변경대상', 	dataIndex : 'key_id',				width : 90,		sortable: false		},
		{ text : '변경내용',		dataIndex : 'value',				width : 250	},		
		{ text : '수정일자',		dataIndex : 'reg_date',			width : 150,	renderer: Ext.util.Format.dateRenderer('Y-m-d H:i:s')	},
		{ text : '변경인',			dataIndex : 'mb_name',			width : 120	}
	],
	store : store_log,
	bbar : {
		plugins: new Ext.ux.SlidingPager(),
		xtype : 'pagingtoolbar',
		store : store_log,
		displayInfo : true,
		displayMsg : '{0}/{1} Total - {2}',
		emptyMsg : 'No Data'
	}
});


/************* ----------------	그리드 END -------------- ******************/