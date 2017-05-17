Ext.Panel.prototype.buttonAlign = 'left';



/* 주문자정보 패널에 사용되는 폼 */
var form_orderinfo = {
	xtype : 'form',
	id : 'frmOrderInfo',
	url : 'order_update.php',
	width : 1900,
	height: 180,
	split: false,
	collapsible: false,
	floatable: true,
	border: 0,
	style: 'margin-top:10px;',
	items:[
			{
				xtype: 'container',
				flex: 1,
				width: 620,
				style : 'float:left;',
				items: [
					{
						xtype: 'fieldset',
		   			title: 'Address',
					   defaultType: 'textfield',
					   border: 0,
					   defaults: {
					   	
					   },
					   style:'float:left;',
						items: [
							{
								xtype: 'fieldcontainer',
								fieldLabel: '주문자정보',
								width : 550, 
								layout: 'hbox',
								combineErrors: true,
								defaultType: 'textfield',
								defaults: {
									hideLabel: true,
									style:'float:left;'
								},
								items: [
											{
												flex: 1,
												name: 'clay_id',
												itemId: 'clay_id',
												width: '150',
												fieldLabel: '닉네임',
												emptyText: '닉네임',
												allowBlank: false
											},
											{
												flex: 2,
												name: 'name',
												itemId: 'name',
												width: '150',
												fieldLabel: '주문자명',
												emptyText: '주문자명',
												margin: '0 0 0 10',
												allowBlank: false
											},
											{
												flex: 3,
												width: '150',
												name: 'hphone',
												fieldLabel: 'H.P',
												emptyText: 'H.P',
												margin: '0 0 0 10',
												allowBlank: false
											},
								] //items
					 		},
							{
								xtype: 'fieldcontainer',
								fieldLabel: '기본주소',
								width : 580,
								layout: 'hbox',
								combineErrors: true,
								defaultType: 'textfield',
								defaults: {
									hideLabel: true,
									style:'float:left;'
								},
								items: [
											{
												flex: 1,
												id : 'addr1',
												name: 'addr1',
												itemId: 'addr1',
												width: '40%',
												afterLabeTextTpl: [
													'<span style="color:red;font-weight:bold" data-qtip="Required">*</span>'
												],
												fieldLabel: '구주소',
												emptyText: '구주소',
												allowBlank: true
											},
											{
												id : 'addr1_2',
												name: 'addr1_2',
												itemId: 'addr1_2',
												width: '45%',
												margin : '0 0 0 5px',
												fieldLabel: '신주소',
												emptyText: '신주소',
												allowBlank: true
											},
											
											{
												xtype: 'button',
												text: '주소찾기',
												width: '15%',
												margin : '0 0 0 5px',
												handler: function() {
													searchPostcode();
												}
											},
											
											{
												id : 'guide',
												name: 'guide',
												hidden: true
											}
											
								] //items
					 		},
					 		{
								xtype: 'fieldcontainer',
								fieldLabel: '상세주소',
								width : 580,
								layout: 'hbox',
								combineErrors: true,
								defaultType: 'textfield',
								defaults: {
									hideLabel: true
								},
								items: [
									{
										flex: 1,
										name: 'addr2',
										afterLabelTextTpl: [
											 '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>'
										],
										fieldLabel: '상세주소',
										emptyText: '상세주소',
										allowBlank: true
									}, {
										width: 100,
										id : 'zip',
										name: 'zip',
										fieldLabel: '우편번호',
										emptyText: '우편번호',
										margin: '0 0 0 5'
									}									
								] //items
					 		},
					 		{
								xtype: 'fieldcontainer',
								fieldLabel: '배송',
								layout: 'hbox',
								combineErrors: true,
								defaultType: 'textfield',
								defaults: {
									hideLabel: false
								},
								items: [
									Ext.create('Ext.combobox.order.delivery_type'),
									{
										xtype: 'textfield',
										name: 'delivery_price',
										fieldLabel: '배송비',
										emptyText: '배송비',
										labelWidth:50,
										width:110,
										allowBlank: true,
										maxLength: 6,
										enforceMaxLength: true,
										margin: '0 0 0 10',
										maskRe: /\d/
									},
									{
										name: 'delivery_invoice2',
										fieldLabel: '주문별 송장번호',
										emptyText: '송장번호',
										labelWidth:100,
										width: 220,												
										margin: '0 0 0 10'
									}
								] //items
					 		}
					 		
						]//items
					}//fieldset end		
				]
			},
			{
				xtype: 'container',
				flex: 1,
				width: 620,
				style : 'float:left;',
				items: [
		 			{
						xtype: 'fieldcontainer',
						fieldLabel: '결제방식',
						layout: 'hbox',
						combineErrors: true,
						defaultType: 'textfield',
						defaults: {
							hideLabel: true
						},
						items: [
							Ext.create('Ext.combobox.order.paytype'),
							{
								width: 140,
								name: 'receipt_name',
								fieldLabel: '입금자명',
								emptyText: '입금자명(무통장결제)',
								margin: '0 0 0 10'
							}
						] //items
			 		},
			 		{
						xtype: 'fieldcontainer',
						fieldLabel: '현금영수증',
						layout: 'hbox',
						combineErrors: true,
						defaultType: 'textfield',
						defaults: {
							hideLabel: true
						},
						items: [
							Ext.create('Ext.combobox.order.cashreceipt_yn'),
							Ext.create('Ext.combobox.order.cashreceipt_type'),
							{
								width: 100,
								name: 'cash_receipt_info',
								fieldLabel: '신청자정보',
								emptyText: '신청자정보',
								margin: '0 0 0 10'
							}
						] //items
			 		},
					{
						xtype: 'fieldcontainer',
						fieldLabel: '환불금액',
						layout: 'hbox',
						combineErrors: true,
						defaultType: 'textfield',
						defaults: {
							hideLabel: true
						},
						items: [
							{
								width: 140,
								name: 'refund_money',
								fieldLabel: '환불금액',
								emptyText: '0',
								margin: '0 0 0 10'
							}
						] //items
					}
				]
			},
			{
				xtype: 'container',
				flex: 1,
				width: 620,
				style : 'float:left;',
				items: [
					{
						xtype: 'textarea',
						fieldLabel: '관리자메모',
						labelAlign : 'top',
						width: 300,
						height: 100,
						margin: '0 0 10 10',
						style:'float:left;',
						name: 'admin_memo'
					},
					{
						xtype: 'textarea',
						fieldLabel: '구매자메모',
						labelAlign : 'top',
						width: 300,
						height: 100,
						margin: '0 0 10 6',
						style:'float:left;',
						name: 'memo'
					}
				]
			}
	],	//items item end
	buttons: [
						{
							text: '수정',
							handler: function() {
								var sm = grid_orderlist.getSelectionModel().getSelection()[0];
								
								if(!sm) {
									Ext.Msg.alert('알림','주문목록을 선택해주세요');
									return false;
								}
								
								var form = Ext.getCmp('frmOrderInfo');
								
								form.submit({
									params : {	mode : 'form',
													gpcode : sm.get('gpcode'),
													od_id  : sm.get('od_id')
									},
									success : function(form,action) {
										Ext.Msg.alert('수정완료', action.result.message);
										form.reset();
										grid_orderlist.store.load();
									},
									failure : function (form, action) {
										Ext.Msg.alert('수정실패', action.result ? action.result.message : '실패하였습니다');
									}
								});
							}
						}
	]
};


/* 로그 패널  */
var panel_orderinfo = Ext.create('Ext.Panel', {
	extend: 'Ext.panel.Panel',
	xtype: 'layout-border',
	requires: [
		'Ext.layout.container.Border'
	],
	layout: 'auto',
	autoWidth : true,
	autoHeight: true,
	bodyBorder: false,
	defaults: {
		collapsible: true,
		split: true,
		bodyPadding: 0
	},
	items: [
				{
					split: false,
					collapsible: false,
					floatable: false,
					border: 0,
					title : 'SMS전송',
					items:[grid_smslog]
				},
				{
					split: false,
					collapsible: false,
					floatable: false,
					border: 0,
					title : '입금내역',
					items:[grid_banklog]
				},
				{
					split: false,
					collapsible: false,
					floatable: false,
					border: 0,
					title : '로그기록',
					items:[grid_log]
				}
	]
});//panel_body


/* SMS팝업 윈도우 패널 */
var panel_winSms = Ext.create('Ext.Panel', {
	xtype: 'layout-border',
	requires: [
		'Ext.layout.container.Border'
	],
	layout: 'auto',
	autoWidth : true,
	autoHeight: true,
	bodyBorder: false,
	defaults: {
		collapsible: true,
		split: true,
		bodyPadding: 0
	},
	items: [
				{
					split: false,
					collapsible: false,
					floatable: true,
					width : '100%',
					height: 200,
					items: [grid_winSms]
				},
				{
					xtype : 'form',
					id : 'frm_WinSms',
					url : 'send_sms.php',
					width : 1060,
					height : 100,
					split : false,
					collapsible: false,
					floatable: true,
					border: 0,
					items:[
								{	xtype: 'label',
									fieldLabel: 'example',
									margin : '10 0 0 10px',
									style : 'float:left; font-weight:bold; font:1.0em color:red;',
									text: '예약어 예) {주문ID} {주문금액} {회사명} {운송장번호}'
								},
								{
									xtype: 'fieldset',
									title: '인보이스 정보',
									defaultType: 'textfield',
									margin : '10 0 0 0px',
									border : 0,
									items : [
												{
									      	xtype : 'checkboxfield',
									        fieldLabel: '같이변경시 체크 ->',
													labelWidth : 120,
													width : 200,
													style : 'float:left',
									        id : 'checkedEdit',
									        value : 'Y'
												},
												{
													fieldLabel: '메시지',
													labelWidth : 50,
													name: 'message',
													width : 600,													
													style : 'float:left',
													enableKeyEvents: true,
													listeners : {
														keyup: function(f,e){
															var sm = grid_winSms.getSelectionModel().getSelection();
															Ext.getCmp('sizecnt').setValue(this.getValue().length);

															if(sm.length) {
																for(var i = 0; i < sm.length; i++) {
																	sm[i].set('message',this.getValue());
																}
															}
															else {
															}
														}
													}
												},
												{
													readOnly: true,
													id : 'sizecnt',
													border: 0,
													style : 'margin-left:10px; float:left; font-weight:bold;',
													width : 40
												},
												{	xtype: 'label',
													fieldLabel: 'alert',
													style : 'float:left; font-weight:bold; font:1.0em color:red;',
													text: 'byte(max : 80byte )'
												}
									]
								}
							
					],	//items item end
					buttons: [
						{
							text: '닫기',
							handler: function() {
								Ext.getCmp('frm_WinSms').getForm().reset();
								winSmsForm.hide();
							}
						},
						{
							text: '전송',
							handler: function() {
								
								var jsonData = "[";
								var cnt = grid_winSms.getStore().data.items.length;
								var form = Ext.getCmp('frm_WinSms');
								
								for(var i = 0; i < cnt; i++) {
									jsonData += Ext.encode(grid_winSms.getStore().data.items[i].data)+",";
								}
								
								jsonData = jsonData.substring(0,jsonData.length-1) + "]";
								
								form.submit({
									target : '',
									params : {	mode  : 'sendSms',
													grid : jsonData
									},
									success : function(form,action) {
										Ext.Msg.alert('변경완료', action.result.message);
										form.reset();
										grid_winSms.getStore().removeAll();
										winSmsForm.hide();										
									},
									failure : function (form, action) {
										Ext.Msg.alert('기록실패', action.result ? action.result.message : '실패하였습니다');
									}
								});
						
							}//handler end
						}//전송BTN end
					]
				}
	        
	]
});	//panel_winSms






/* 주문정보 패널 */
var bank_panel = Ext.create('Ext.Panel', {
	id: 'bank_panel',
	extend: 'Ext.panel.Panel',
	xtype: 'layout-border',
	requires: [
		'Ext.layout.container.Border'
	],
	layout: 'border',
	width: '100%',
	height: 400,
	bodyBorder: false,
	defaults: {
		collapsible: true,
		split: true,
		bodyPadding: 0
	},
	items: [
					{
						id : 'bank_west',
						region: 'west',
						title: '<b>입/출금 내역</b>',
						headerPosition: 'left',
						floatable: false,
						width: '60%',
						autoHeight : true,
						style : 'float:left; margin:0px; padding:0px;',
						items : [grid_banklist]
					},
					{
						//left 
						id : 'bank_center',
						region: 'center',
						headerPosition: 'left',
						title: '<b>연결된 주문정보</b>',
						collapsible: false,
						width: '40%',
						autoHeight : true,
						style : 'float:left; margin:0px; padding:0px;',
						items	: [grid_banklinklist]
					}
	]
});
	

/* 주문정보 패널 */
var order_panel = Ext.create('Ext.Panel', {
	id : 'order_panel',
	extend: 'Ext.panel.Panel',
	xtype: 'layout-border',
	requires: [
		'Ext.layout.container.Border'
	],
	layout: 'border',
	width: '100%',
	height: 930,
	bodyBorder: false,
	defaults: {
		collapsible: true,
		split: true,
		bodyPadding: 0
	},
	items: [
		{
			id : 'order_north',
			title: '<b>주문자정보</b>',
			region: 'north',
			floatable: false,
			autoScroll: false,
			height: 225,
			style : 'float:left; margin:0px; padding:0px;',
			items : [
				form_orderinfo
			]
		},
		{
			id : 'order_west',
			title: '<b>로그</b>',
			region: 'west',
			floatable: false,
			minWidth : 657,
			maxWidth : 657,
			autoScroll: true,
			style : 'float:left; margin:0px; padding:0px;',
			items : [panel_orderinfo]
		},
		{
			//left 
			id : 'order_center',
			title: '<b>주문목록 </b> [금: $'+v_GL+' / 은: $'+v_SL+' / $환율: '+v_USD+'원]',
			region: 'center',
			collapsible: false,
			autoHeight : true,
			style : 'float:left; margin:0px; padding:0px;',
			items	: [grid_orderlist]
		}
	]
	//,renderTo: 'extjsBody'
});