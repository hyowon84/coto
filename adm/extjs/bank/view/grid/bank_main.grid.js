

/************* ----------------	그리드 START -------------- ******************/


var combo_findbanktype = Ext.create('Ext.combobox.bank.banktype', {
	extend: 'Ext.form.ComboBox',
	xtype: 'combobox',
	editable: false,
	displayField: 'name',
	valueField: 'value',
	name: 'banktype',
	labelWidth : 60,
	store: Ext.create('Ext.store.bank.banktype'),
	value : 'ALL',
	fieldLabel : '거래유형',
	
	id : 'combo_findbanktype',
	width : 180,
	listeners : {
		change: function (view, records) {
			searchBanklist();
		}
	}
});


var df_bank_sdate = Ext.create('Ext.dateField.common');		var df_bank_edate = Ext.create('Ext.dateField.common');
df_bank_sdate.id = 'bank_sdate';	df_bank_sdate.name = 'bank_sdate';	df_bank_sdate.fieldLabel = '시작일';
df_bank_edate.id = 'bank_edate';	df_bank_edate.name = 'bank_edate';	df_bank_edate.fieldLabel = '종료일';


var combo_banktype = Ext.create('Ext.combobox.bank.banktype');	combo_banktype.id = 'combo_banktype';	combo_banktype.width = 210; 
var combo_taxtype = Ext.create('Ext.combobox.bank.taxtype');		combo_taxtype.id = 'combo_taxtype';		combo_taxtype.width = 230;

combo_banktype.fieldLabel = '입출금유형';
combo_taxtype.fieldLabel = '세금처리유형';


function searchBanklist() {
	store_banklist.loadData([], false);
	Ext.apply(store_banklist.getProxy().extraParams, {
		keyword: Ext.getCmp('keyword_banklist').getValue(),
		bank_type: Ext.getCmp('combo_findbanktype').getValue(),
		sdate: df_bank_sdate.rawValue,
		edate: df_bank_edate.rawValue
	});
	store_banklist.load();
}


/*연결된 주문정보 그리드에서 입금처리 및 환불처리*/
function updateLinkBankdata(v_stats) {
	var v_title;

	switch(v_stats) {
		case 'B01':
			v_title = "입금처리";
			v_odstats = '20';
			break;
		case 'B07':
			v_title = "환불처리";
			v_odstats = '90';
			break;
		default:
			break;
	}

	var sm_bank = grid_banklist.getSelectionModel().getSelection()[0];
	var sm_od = grid_banklinklist.getSelectionModel().getSelection()[0];

	if( sm_od == '' || sm_bank == '') {
		Ext.Msg.alert('알림','입출금내역과 주문내역을 선택해주세요');
		return false;
	}

	if(sm_od && sm_bank) {
		var sm_bank = grid_banklist.getSelection();
		var sm_od = grid_banklinklist.getSelection();

		var v_odid_list = '';
		var v_bank_list = '';
		var v_prev_od_id = '';
		var msg_odid_list = '';

		for(var i = 0; i < sm_od.length; i++) {	//sm[i].data
			/*중복주문번호에 대해서는 중복발송 방지위해 필터링*/
			if(sm_od[i].data.od_id == v_prev_od_id) continue;

			v_odid_list += sm_od[i].data.od_id + ",";
			msg_odid_list += sm_od[i].data.od_id + "<br>";
			v_prev_od_id = sm_od[i].data.od_id;
		}
		v_odid_list = v_odid_list.substr(0,v_odid_list.length-1);

		for(var i = 0; i < sm_bank.length; i++) {	//sm[i].data
			v_bank_list += '"'+sm_bank[i].data.trader_name + "의 입금("+Ext.util.Format.number(sm_bank[i].data.input_price, "0,000")+"원), 출금("+Ext.util.Format.number(sm_bank[i].data.output_price, "0,000")+"원)\", <br>";
		}
		
		

		var msg = v_bank_list + "<br>위 내역에 대한 연결될 주문번호는 아래와 같습니다<br>"+ msg_odid_list;

		Ext.MessageBox.confirm(v_title, msg, function(btn, text) {
			if(btn == 'yes') {
				for(var i = 0; i < sm_bank.length; i++) {	//sm[i].data
					sm_bank[i].set('bank_type', v_stats);	//상품주문
					sm_bank[i].set('admin_link', v_odid_list);
				}
				grid_banklist.store.sync();

				for(var i = 0; i < sm_od.length; i++) {	//sm[i].data
					sm_od[i].set('stats',v_odstats);					
				}
				
			}
		}, function(){

		});
	}
}


/* 입출금 내역 */
var grid_banklist = Ext.create('Ext.grid.Panel',{
	plugins	: Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1}),
	selModel: Ext.create('Ext.selection.CheckboxModel'),
	viewConfig: {
		stripeRows: true,
		getRowClass: colorSettingBankStats,
		enableTextSelection: true
	},
	autoWidth : true,
	height	: 400,
	store : store_banklist,
	columns : [
		{ text : '통장',		 				width : 90,		dataIndex : 'account_name',		style:'text-align:center',	align:'center',	hidden:true	},
		{ text : '거래일시', 			width : 140,	dataIndex : 'tr_date',				style:'text-align:center',	align:'center',	renderer: Ext.util.Format.dateRenderer('Y-m-d H:i:s') },
		{ text : '거래수단',			 	width : 70,		dataIndex : 'tr_type',				style:'text-align:center',	hidden:true	},
		{ text : '출금액',					width : 100,	dataIndex : 'output_price',		style:'text-align:center',				align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '입금액',					width : 100,	dataIndex : 'input_price',		style:'text-align:center',				align:'right',	renderer: Ext.util.Format.numberRenderer('0,000') },
		{ text : '거래자명',	 			width : 120,	dataIndex : 'trader_name',		style:'text-align:center'		},
		{ text : '입출금유형', 		width: 120,		dataIndex : 'bank_type',			editor: Ext.create('Ext.combobox.bank.banktype'),		renderer: rendererCombo	},
		{ text : '연결된 주문',	 	width : 250,	dataIndex : 'admin_link',			editor: { allowBlank : false },							style:'text-align:center'		},
		{ text : '관리자 메모',	 	width : 200,	dataIndex : 'admin_memo',			editor: { allowBlank : false },							style:'text-align:center'		},
		{ text : '세금처리유형', 	width: 120,		dataIndex : 'tax_type',				editor: Ext.create('Ext.combobox.bank.taxtype'),		renderer: rendererCombo	},
		{ text : '입력번호',	 			width : 120,	dataIndex : 'tax_no',					editor: { allowBlank : false },							style:'text-align:center'		},
		{ text : '후처리번호',			width : 120,	dataIndex : 'tax_refno',			editor: { allowBlank : false },							style:'text-align:center'		},
		{ text : '  ',						width : 100,	dataIndex : ''	}
		
	],
	dockedItems: [
		{
			xtype : 'toolbar',
			dock : 'top',
			items : [
				{	xtype: 'label',	text: '검색어 : ',		width:60,	style : 'font-weight:bold;'},
				{
					xtype: 'textfield',
					id : 'keyword_banklist',
					name: 'keyword_banklist',
					style: 'padding:0px;',
					enableKeyEvents: true,
					listeners:{
						keydown:function(t,e){
							if(e.keyCode == 13){
								searchBanklist();
							}
						}
					}
				},
				combo_findbanktype,
				df_bank_sdate,
				df_bank_edate
			]
		},		
		{
			xtype : 'toolbar',
			dock : 'top',
			items : [
				combo_banktype,
				combo_taxtype,
				{
					id		: 'bankUpdBtn',
					text	: '변경',
					iconCls	: 'icon-table_edit',
					handler : function() {

						var sm = grid_banklist.getSelection();
						if( sm == '' ) {
							Ext.Msg.alert('알림','입출금 내역을 선택해주세요');
							return false;
						}

						var banktype = combo_banktype.getValue();
						var taxtype = combo_taxtype.getValue();

						for(var i = 0; i < sm.length; i++) {
							if(banktype) sm[i].set('bank_type',banktype);
							if(taxtype) sm[i].set('tax_type',taxtype);
						}

					}
				},
				{
					text	: '페이지 저장',
					iconCls	: 'icon-pagesave',
					handler: function() {
						
						Ext.MessageBox.confirm('현재 페이지 저장', "현재 입출금내역 페이지를 DB에 저장합니다.", function(btn, text) {
							if(btn == 'yes') {
								grid_banklist.store.sync();
							}
						}, function(){

						});
					}
				},
				{
					text	: '인쇄',
					iconCls	: 'icon-table_print',
					handler: function() {
						Ext.ux.grid.Printer.mainTitle = '선택된 입출금목록';
						Ext.ux.grid.Printer.print(grid_banklist);
					}
				}
			]
		}
	],
	bbar : {
		plugins: new Ext.ux.SlidingPager(),
		xtype : 'pagingtoolbar',
		store : store_banklist,
		displayInfo : true,
		displayMsg : '{0}/{1} Total - {2}',
		emptyMsg : 'No Data'
	},
	listeners : {
		selectionchange: function(view, records) {
			store_banklinklist.loadData([],false);
			
			var sm = grid_banklist.getSelectionModel().getSelection()[0];

			if(sm) {
				var sm = grid_banklist.getSelection();

				if( sm == '' ) {
					Ext.Msg.alert('알림','상품들을 선택해주세요');
					return false;
				}

				var v_number = '';
				for(var i = 0; i < sm.length; i++) {	//sm[i].data
					v_number += sm[i].data.number + ",";
				}

				v_number = v_number.substr(0,v_number.length-1);

				v_keyword = Ext.getCmp('banklink_keyword').getValue();
				var v_param = { 'number' : v_number,
												'keyword' : v_keyword
				};
				
				Ext.apply(store_banklinklist.getProxy().extraParams, v_param);
				store_banklinklist.load();
			}

		},
		edit: listenerEditFunc,
		afterrender: listenerAfterRendererFunc
	}
});




/* 우측 주문 목록 */
var grid_banklinklist = Ext.create('Ext.grid.Panel',{
	plugins	: Ext.create('Ext.grid.plugin.CellEditing',{clicksToEdit: 1}),
	selModel: Ext.create('Ext.selection.CheckboxModel'),
	autoWidth : true,
	height	: 400,
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
	store : store_banklinklist,
	columns	: [
		{ text: '공구코드',			dataIndex: 'gpcode',						width: 100,		hidden:true	},
		{ text: 'number',				dataIndex: 'number',						width: 100,		hidden:true	},
		{ text: '공구명', 				dataIndex: 'gpcode_name',				width: 120,		style:'text-align:center'	},
		{ text: '주문번호', 			dataIndex: 'od_id',							width: 130,		style:'text-align:center',		align:'center'	},
		{ text: '주문일시',			dataIndex: 'od_date',						width: 150,		renderer: Ext.util.Format.dateRenderer('Y-m-d H:i:s'),	hidden:true	},
		{ text: '주문상태', 			dataIndex: 'stats',							width: 120,		editor: Ext.create('Ext.combobox.order.stats'),		renderer: rendererCombo	},
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
				{	xtype: 'label',	text: '확장 검색어 : ',		autoWidth:true,	style : 'font-weight:bold;'},
				{
					xtype: 'textfield',
					id : 'banklink_keyword',
					name: 'keyword',
					style: 'padding:0px;',
					enableKeyEvents: true,
					listeners:{
						keydown:function(t,e){
							if(e.keyCode == 13){
								var v_param = {
									keyword : Ext.getCmp('banklink_keyword').getValue()
								}
								
								grid_banklinklist.store.loadData([],false);
								Ext.apply(grid_banklinklist.store.getProxy().extraParams, v_param);
								Ext.getCmp('ptb_banklinklist').moveFirst();
							}
						}
					}
				},
				{
					text: '입금처리',
					id: 'btn_link_B01',
					iconCls: 'icon-link',
					handler: function () {
						updateLinkBankdata('B01');
					}
				},
				{
					text: '환불처리',
					id: 'btn_link_B07',
					iconCls: 'icon-link',
					handler: function () {
						updateLinkBankdata('B07');
					}
				},
				{
					text	: 'SMS',
					id		: 'bankSendSMS',
					iconCls	: 'icon-sms',
					handler: function() {
						var sm = grid_banklinklist.getSelection();

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
				} //SMS버튼 END
			]
		}
	],
	bbar : {
		plugins: new Ext.ux.SlidingPager(),
		id : 'ptb_banklinklist',
		xtype : 'pagingtoolbar',
		store : store_banklinklist,
		displayInfo : true,
		displayMsg : '{0}/{1} Total - {2}',
		emptyMsg : 'No Data'
	},
	listeners : {
		selectionchange: function(view, records) {

			//var sm = grid_banklinklist.getSelectionModel().getSelection()[0];
			//
			//if(sm) {
			//	Ext.getCmp('navi_center').setTitle('> "'+sm.get('gpcode_name')+"("+sm.get('od_id')+")"+'"의 주문정보');
			//	sm.data.admin_memo = sm.data.admin_memo.replace(/<br>/gi, "\r\n");	//개행문자를 <BR>로 변경한걸 다시 원상복구
			//	sm.data.memo = sm.data.memo.replace(/<br>/gi, "\r\n");
			//	Ext.getCmp('frmOrderInfo').loadRecord(sm);
			//
			//	var v_param = {od_id : sm.data.od_id}
			//
			//	store_smslog.loadData([],false);
			//	store_banklog.loadData([],false);
			//	store_log.loadData([],false);
			//
			//	Ext.apply(store_smslog.getProxy().extraParams, v_param);
			//	Ext.apply(store_banklog.getProxy().extraParams, v_param);
			//	Ext.apply(store_log.getProxy().extraParams, v_param);
			//
			//	store_smslog.load();
			//	store_banklog.load();
			//	store_log.load();
			//
			//} else {
			//	Ext.getCmp('navi_center').setTitle('> 주문내역을 선택하세요~');
			//	Ext.getCmp('frmOrderInfo').getForm().reset();
			//}

		},
		edit: listenerEditFunc,
		afterrender: listenerAfterRendererFunc
	}
});


/***********************************	그리드 END ***********************************/