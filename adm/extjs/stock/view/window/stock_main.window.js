

/*공구참고사항 메모수정 팝업창*/
var winMemoGpinfo = Ext.create('widget.window', {
	id : 'winMemoGpinfo',
	title: '공구 메모수정',
	reference: 'popupWindow',
	header: {
		titlePosition: 2,
		titleAlign: 'center'
	},
	closable: true,
	closeAction: 'hide',
	maximizable: false,
	resizable : false,
	animateTarget: 'winMemoGpinfo',		/*발주*/
	width: 700,
	minWidth: 350,
	height: 500,
	layout: {
		type: 'border',
		padding: 5
	},
	items: [
		{
			xtype : 'form',
			id : 'winMemoGpinfoForm',
			url : '/adm/extjs/stock/crud/gpinfo.update.php?mode=memo',
			width: '100%',
			height : 450,
			autoHeight : true,
			split: false,
			collapsible: false,
			floatable: true,
			border: 0,
			style: 'margin-top:10px;',
			items:[
				{
					xtype: 'container',
					flex: 1,
					width: '100%',
					style : 'float:left;',
					items: [
						{
							xtype: 'textfield',
							fieldLabel: '공구코드',
							name : 'gpcode',
							readOnly : true,
							hidden : true
						},
						{
							xtype: 'textarea',
							fieldLabel: '발주관련메모',
							labelAlign : 'top',
							width: '98%',
							height : 180,
							margin: '0 0 10 10',
							style:'float:left;',
							name: 'invoice_memo'
						},
						{
							xtype: 'textarea',
							fieldLabel: '메모',
							labelAlign : 'top',
							width: '98%',
							height : 180,
							margin: '0 0 10 6',
							style:'float:left;',
							name: 'memo'
						}
					]
				}
			],	//items item end
			buttons: [{
				text: '취소',
				handler: function() {
					Ext.getCmp('winMemoGpinfoForm').getForm().reset();
					winMemoGpinfo.hide();
				}
			}, {
				text: '수정',
				handler: function() {
					var form = Ext.getCmp('winMemoGpinfoForm');

					form.submit({
						params : {	mode : 'memo'	},
						success : function(form,action) {
							Ext.Msg.alert('수정완료', action.result.message);
							form.reset();

							winMemoGpinfo.hide();
							store_memo_gpinfo.load();
						},
						failure : function (form, action) {
							Ext.Msg.alert('수정실패', action.result ? action.result.message : '실패하였습니다');
						}
					});

				}
			}]
		}
	]
});


/*발주서 작성폼*/
var winInvoice = Ext.create('widget.window', {
	id : 'winInvoice',
	title: '발주 입력폼',
	reference: 'popupWindow',
	header: {
		titlePosition: 2,
		titleAlign: 'center'
	},
	closable: true,
	closeAction: 'hide',
	maximizable: true,
	animateTarget: 'write_invoice',
	width: 1400,
	minWidth: 350,
	height: 460,
	border : 0,
	layout: {
		type: 'border',
		padding: 0
	},
	items: [
		{
			xtype : 'form',
			id : 'ivFormPanel',
			url : '/adm/extjs/stock/crud/stock_update.php',
			layout: {type: 'column'},
			region: 'west',
			width: 1394,
			height: 390,
			border : 0,
			split: false,
			items:[
				{
					title: '발주서 작성',
					labelAlign : 'top',
					columnWidth: 0.3,
					defaults: {
						anchor: '100%',
						border : 1
					},
					border : 0,
					items: [
						//{	xtype: 'label',	text: '- 발주시 입력정보 -',	style: 'margin:10px 0 0 10px; display:block;'	},
						{
							xtype: 'fieldset',
							title: '발주시 입력정보',
							labelAlign : 'top',
							collapsible: true,
							defaultType: 'textfield',
							style : 'padding:10px',
							defaults: {
								labelWidth: 110,
								anchor: '100%',
								layout: 'hbox'
							},
							items: [
								{
									fieldLabel: '공구코드',
									name: 'gpcode',
									readOnly: true
								},
								{
									fieldLabel: '발주서 별칭',
									name: 'iv_name'
								},
								{
									xtype: 'combo',
									fieldLabel: '딜러',
									reference: 'dealers',
									store: {
										type: 'dealers'
									},
									publishes: 'value',
									emptyText: '선택 또는 입력',
									name: 'iv_dealer',
									displayField: 'ct_name',
									anchor: '0',
									queryMode: 'local',
									listConfig: {
										itemTpl: ['<div data-qtip="{ct_id}: {ct_name}">{ct_name} ({ct_id})</div>']
									}
								},
								{
									fieldLabel: '인보이스번호',
									emptyText: '인보이스번호',
									name: 'iv_order_no'
								},
								{
									xtype: 'datefield',
									format: 'Y-m-d',
									fieldLabel: '인보이스날짜',
									name: 'iv_date',
									allowBlank: false,
									maxValue: new Date(),
									value : new Date()
								},
								{
									xtype: 'combo',
									fieldLabel: '통화유형',
									reference: 'moneytype',
									store: {
										type: 'moneytype'
									},
									emptyText: '선택 또는 입력',
									name: 'money_type',
									displayField: 'title',
									publishes: 'value',
									anchor: '0',
									queryMode: 'local',
									listConfig: {
										itemTpl: ['<div data-qtip="{value}">{value}</div>']
									}
								},
								{
									fieldLabel: '환율',
									emptyText: '0',
									name: 'od_exch_rate'
								},
								{
									fieldLabel: 'TAX',
									emptyText: '0',
									name: 'iv_tax'
								},
								{
									fieldLabel: 'SHIP.FEE',
									emptyText: '0',
									name: 'iv_shippingfee'
								},
								{
									fieldLabel: 'DISCOUNT.FEE',
									emptyText: '0',
									name: 'iv_discountfee'
								}
							]
						}
					]	//필드셋 items end
				},	//필드셋 엘리먼트 end
				{
					region: 'center',
					columnWidth: 0.7,
					split: true,
					collapsible: false,
					floatable: false,
					border : 0,
					items:[
						{
							autoWidth : true,
							height: 390,
							border : 1,
							items : [grid_window_invoice]
						}
					]		/*발주입력품목그리드*/
				}
			],	//items item end
			buttons: [
				{
					id : 'win_iv_qty',
					fieldLabel: '수량',
					xtype: 'numberfield'
				},
				{
					text: '일괄수정',
					handler: function() {
						var store = grid_window_invoice.store;
						var cnt = store.getCount();
						var v_qty = Ext.getCmp('win_iv_qty').getValue();
							
						for(var i = 0; i < cnt; i++) {
							store.getData().getAt(i).set('iv_qty',v_qty);
						}
						
					}
				},
				
				{
					text: '인쇄',
					handler: function() {
						Ext.ux.grid.Printer.mainTitle = Ext.util.Format.date(new Date(),'Y-m-d g:i:s') +' INVOICE LIST';
						Ext.ux.grid.Printer.print(grid_window_invoice);						
					}
				},
				{
					text: '취소',
					handler: function() {
						Ext.getCmp('ivFormPanel').getForm().reset();
						winInvoice.hide();
					}
				},
				{
					text: '기록',
					id : 'BtnInvoice',
					handler: function() {
						Ext.getCmp('BtnInvoice').hide();
						
						var jsonData = "[";
						var cnt = grid_window_invoice.getStore().data.items.length;
						var form = Ext.getCmp('ivFormPanel');
	
						for(var i = 0; i < cnt; i++) {
							jsonData += Ext.encode(grid_window_invoice.getStore().data.items[i].data)+",";
						}
	
						jsonData = jsonData.substring(0,jsonData.length-1) + "]";
	
						form.submit({
							params : {	mode : 'new',
								grid : jsonData
							},
							success : function(form,action) {
								Ext.Msg.alert('기록완료', action.result.message);
								form.reset();
								grid_window_invoice.getStore().removeAll();
								grid_orderitems.getStore().load();//발주대상 주문품목들 리로딩							
								grid_invoiceTodoWire.getStore().load();//송금예정발주서 리로딩
								winInvoice.hide();
								Ext.getCmp('BtnInvoice').show();
							},
							failure : function (form, action) {
								Ext.Msg.alert('기록실패', action.result ? action.result.message : '실패하였습니다');
								Ext.getCmp('BtnInvoice').show();
							}
						});
					}
				}//기록
			]
		}
	]
});





/*송금확인서 작성폼*/
var winWireConfirm = Ext.create('widget.window', {
	id : 'winWireConfirm',
	title: '송금내역 작성',
	reference: 'popupWindow',
	header: {
		titlePosition: 2,
		titleAlign: 'center'
	},
	closable: true,
	closeAction: 'hide',
	maximizable: true,
	animateTarget: 'winWireConfirm',		/*발주*/
	width: 900,
	minWidth: 350,
	height: 480,
	border : 0,
	layout: {
		type: 'border',
		padding: 3
	},
	items: [
		{
			xtype : 'form',
			id : 'wireFormPanel',
			url : '/adm/extjs/stock/crud/wire.create.php',
			layout: {type: 'column'},
			region: 'west',
			width: 894,
			height: 285,
			border : 0,
			floatable : true,
			style : 'float:left;',
			split: false,
			items:[
				{
					title: '송금내역 기본정보',
					labelAlign : 'top',
					columnWidth: 0.45,
					defaults: {
						anchor: '100%'
					},
					floatable: true,
					border : 0,
					items: [
						{
							xtype: 'fieldset',
							title: '송금정보',
							labelAlign : 'top',
							collapsible: true,
							defaultType: 'textfield',
							style : 'padding:10px',
							defaults: {
								labelWidth: 150,
								anchor: '100%',
								layout: 'hbox'
							},
							items: [
								{
									fieldLabel: '발주ID',
									name: 'iv_id',
									readOnly: true
								},
								{
									fieldLabel: '송금내역별칭',
									name: 'wr_name'
								},
								{
									xtype: 'datefield',
									format: 'Y-m-d',
									fieldLabel: '송금일',
									name: 'wr_date',
									allowBlank: false,
									maxValue: new Date(),
									value : new Date()
								},
								{
									xtype: 'combo',
									fieldLabel: '송금유형',
									reference: 'wrtype',
									store: {
										type: 'wrtype'
									},
									emptyText: '선택 또는 입력',
									name: 'wr_type',
									displayField: 'title',
									valueField : 'value',
									anchor: '0',
									queryMode: 'local',
									listConfig: {
										itemTpl: ['<div data-qtip="{value}">{title}</div>']
									}
								},
								{
									xtype: 'combo',
									fieldLabel: '통화유형',
									reference: 'moneytype',
									store: {
										type: 'moneytype'
									},
									emptyText: '선택 또는 입력',
									name: 'wr_currency',
									displayField: 'title',
									valueField : 'value',
									anchor: '0',
									queryMode: 'local',
									listConfig: {
										itemTpl: ['<div data-qtip="{value}">{title}</div>']
									}
								},
								{
									fieldLabel: '송금기준환율',
									id: 'wr_exchrate',
									name: 'wr_exchrate',
									allowBlank: false
								},
								{
									xtype: 'numberfield',
									id : 'wr_totalprice',
									name: 'wr_totalprice',
									fieldLabel: '송금 총액',
									labelWidth:150,
									allowBlank: false,
									enforceMaxLength: true,
									maskRe: /\d/
								},
								{
									fieldLabel: '송금수수료(국외)',
									name: 'wr_out_fee',
									allowBlank: false
								},
								{
									fieldLabel: '송금수수료(국내)',
									name: 'wr_in_fee',
									allowBlank: false
								},
								{
									xtype: 'textarea',
									fieldLabel: '메모',
									labelAlign : 'top',
									width: '98%',
									height : 60,
									margin: '0 0 10 6',
									style:'float:left;',
									name: 'wr_memo'
								}
							]
						}
					]	//필드셋 items end
				},	//필드셋 엘리먼트 end
				{
					region: 'center',
					columnWidth: 0.55,
					split: true,
					collapsible: false,
					floatable: true,
					border : 0,
					style : 'float:left;',
					items:[{
						autoWidth : true,
						//autoHeight : true,
						autoHeight : true,
						items : [grid_window_wire]
					}]		/*송금입력품목그리드*/
				}
			],	//items item end
			buttons: [{
				text: '취소',
				handler: function() {
					Ext.getCmp('wireFormPanel').getForm().reset();
					winWireConfirm.hide();
					Ext.getCmp('BtnWire').show();
				}
			}, {
				text: '송금처리',
				id : 'BtnWire',
				handler: function() {
					
					var jsonData = "[";
					var cnt = grid_window_invoice.getStore().data.items.length;
					var form = Ext.getCmp('wireFormPanel');

					//환율값이 100원이하는 입력안함으로 간주
					if(Ext.getCmp('wr_exchrate').getValue()*1 < 5 ) {
						Ext.Msg.alert('알림','기준환율을 제대로 입력해주세요');
						return false;
					}
					
					//5000원 이하는 값을 입력안한 것으로 간주
					if(Ext.getCmp('wr_totalprice').getValue()*1 < 5) {
						Ext.Msg.alert('알림','송금액을 제대로 입력해주세요');
						return false;
					}
					
					
					for(var i = 0; i < cnt; i++) {
						jsonData += Ext.encode(grid_window_invoice.getStore().data.items[i].data)+",";
					}

					jsonData = jsonData.substring(0,jsonData.length-1) + "]";

					Ext.getCmp('BtnWire').hide();
					form.submit({
						params : {	mode : 'new',
							grid : jsonData
						},
						success : function(form,action) {
							Ext.Msg.alert('기록완료', action.result.message);
							form.reset();
							
							grid_invoiceTodoWire.getStore().load();	//좌측상단 송금예정 발주서 로딩
							grid_invoiceEndWire.getStore().load();	//좌측하단 송금완료된 발주서 로딩
							grid_todoClearance.getStore().load();	//좌측하단 송금완료된 발주서 로딩
							

							//송금팝업, 연결된공구정보, 발주품목목록 초기화
							grid_window_invoice.getStore().removeAll();
							grid_wire_gpinfo.getStore().removeAll();
							grid_wire_dtl.getStore().removeAll();

							winWireConfirm.hide();
							Ext.getCmp('BtnWire').show();
						},
						failure : function (form, action) {
							Ext.Msg.alert('기록실패', action.result ? action.result.message : '실패하였습니다');
							Ext.getCmp('BtnWire').show();
						}
					});

				}
			}]
		}
	]
});



//통관확인서
var winClearanceConfirm = Ext.create('widget.window', {
	id : 'winClearanceConfirm',
	title: '통관내역 작성',
	reference: 'popupWindow',
	header: {
		titlePosition: 2,
		titleAlign: 'center'
	},
	closable: true,
	closeAction: 'hide',
	maximizable: true,
	animateTarget: 'winClearanceConfirm',		/*발주*/
	width: 1150,
	minWidth: 350,
	height: 480,
	border : 0,
	layout: {
		type: 'border',
		padding: 3
	},
	items: [
		{
			xtype : 'form',
			id : 'clearanceFormPanel',
			url : '/adm/extjs/stock/crud/clearance.create.php',
			layout: {type: 'column'},
			region: 'west',
			width: 1144,
			height: 285,
			border : 0,
			floatable : true,
			style : 'float:left;',
			split: false,
			items:[
				{
					/*필드셋 세로는 내맘대로 조절안됨, 엘리먼트 크기만큼 늘어남*/
					title: '통관내역 기본정보',
					labelAlign : 'top',
					columnWidth: 0.35,
					defaults: {
						anchor: '100%'
					},
					floatable: true,
					border : 0,
					items: [
						{
							xtype: 'fieldset',
							title: '통관정보',
							labelAlign : 'top',
							collapsible: true,
							defaultType: 'textfield',
							style : 'padding:10px',
							defaults: {
								labelWidth: 150,
								anchor: '100%',
								layout: 'hbox'
							},
							items: [
								{
									fieldLabel: '발주ID',
									name: 'iv_id',
									readOnly: true
								},
								{
									fieldLabel: '통관번호',
									name: 'cr_refno'
								},
								{
									fieldLabel: '통관내역별칭',
									name: 'cr_name'
								},
								{
									xtype: 'numberfield',
									name: 'cr_taxfee',
									fieldLabel: '관/부가세',
									emptyText: '0',
									labelWidth:150,
									allowBlank: false,
									enforceMaxLength: true,
									maskRe: /\d/
								},
								{
									fieldLabel: '배송비',
									emptyText: '0',
									name: 'cr_shipfee'
								},
								{
									xtype: 'datefield',
									format: 'Y-m-d',
									fieldLabel: '통관일',
									name: 'cr_date',
									allowBlank: false,
									maxValue: new Date(),
									value : new Date()
								},
								{
									xtype: 'textarea',
									fieldLabel: '메모',
									labelAlign : 'top',
									width: '98%',
									height : 150,
									margin: '0 0 10 6',
									style:'float:left;',
									name: 'cr_memo'
								}
							]
						}
					]	//필드셋 items end
				},	//필드셋 엘리먼트 end
				{
					region: 'center',
					columnWidth: 0.65,
					split: true,
					collapsible: false,
					floatable: true,
					border : 0,
					style : 'float:left;',
					items:[{
						autoWidth : true,
						autoHeight : true,
						items : [grid_window_clearance]
					}]		/*송금입력품목그리드*/
				}
			],	//items item end
			buttons: [{
				text: '취소',
				handler: function() {
					Ext.getCmp('clearanceFormPanel').getForm().reset();
					winClearanceConfirm.hide();
				}
			}, {
				text: '통관처리',
				id : 'BtnClearance',
				handler: function() {
					Ext.getCmp('BtnClearance').hide();
					
					var jsonData = "[";
					var cnt = grid_window_clearance.getStore().data.items.length;
					var form = Ext.getCmp('clearanceFormPanel');

					for(var i = 0; i < cnt; i++) {
						jsonData += Ext.encode(grid_window_clearance.getStore().data.items[i].data)+",";
					}

					jsonData = jsonData.substring(0,jsonData.length-1) + "]";

					form.submit({
						params : {	mode : 'new',
							grid : jsonData
						},
						success : function(form,action) {
							Ext.Msg.alert('통관내역 작성완료', action.result.message);
							form.reset();

							grid_invoiceTodoWire.getStore().load();	//좌측상단 송금예정 발주서 로딩
							grid_invoiceEndWire.getStore().load();	//좌측하단 송금완료된 발주서 로딩
							grid_todoClearance.getStore().load();		//통관예정내역 리로딩
							grid_endClearance.getStore().load();		//통관완료내역 리로딩
							grid_todoWarehousing.getStore().load();	//입고예정내역 리로딩
							
							//송금팝업, 연결된공구정보, 발주품목목록 초기화
							grid_window_clearance.getStore().removeAll();
							grid_clearance_gpinfo.getStore().removeAll();
							grid_clearance_dtl.getStore().removeAll();

							winClearanceConfirm.hide();
							Ext.getCmp('BtnClearance').show();
						},
						failure : function (form, action) {
							Ext.Msg.alert('통관내역 작성실패', action.result ? action.result.message : '실패하였습니다');
							Ext.getCmp('BtnClearance').show();
						}
					});

				}
			}]
		}
	]
});