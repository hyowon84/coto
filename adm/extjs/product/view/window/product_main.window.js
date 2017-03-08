
var winInvoice;

winInvoice = Ext.create('widget.window', {
	id : 'win_Invocie',
	title: '인쇄예정 목록',
	reference: 'popupWindow',
	header: {
		titlePosition: 2,
		titleAlign: 'center'
	},
	closable: true,
	closeAction: 'hide',
	maximizable: true,
	animateTarget: 'write_invoice',		/*발주*/
	width: 1400,
	minWidth: 350,
	height: 600,
	tools: [{type: 'pin'}],
	layout: {
		type: 'border',
		padding: 5
	},
	items: [
			{
				xtype : 'form',
				id : 'frmInvoice',
				url : 'product_update.php',
				layout: {type: 'auto'},
				region: 'west',
				width: '100%',
				height:600,
				split: false,
				collapsible: false,
				floatable: false,
				items:[
						/*{
							xtype: 'fieldset',
							title: '인보이스 정보',
							columnWidth: 0.3,
							defaultType: 'textfield',
							defaults: {
								anchor: '100%'
							},
							items: [
							        {
										fieldLabel: '공구코드',
										name: 'gpcode',
										readOnly: true
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
										itemTpl: [
												'<div data-qtip="{ct_id}: {ct_name}">{ct_name} ({ct_id})</div>'
											]
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
										xtype: 'textareafield',
										fieldLabel: '메모',
										name : 'iv_memo',
										labelAlign: 'top',
										flex: 1,
										height:300,
										margin: '10',
										afterLabelTextTpl: [
											'<span style="color:red;font-weight:bold" data-qtip="Required">*</span>'
										],
										allowBlank: false
									}
							]
						},*/
						{
							region: 'center',
							columnWidth: 1,
//							split: true,
//							collapsible: false,
//							floatable: false,
							items:[grid_window_product]		/*발주입력품목그리드*/
						}
				]	//items item end
				,
				buttons: [{
					text: '취소',
					handler: function() {
						Ext.getCmp('frmInvoice').getForm().reset();
						winInvoice.hide();
					}
				},{
					text: '통합배송요청',
					handler: function() {
						updateOrderStats(22);	//통합배송요청
					}
				},{
					text: '배송대기',
					handler: function() {
						updateOrderStats(25);	//배송대기
					}
				}
				
				]
			}	//form element
		]
});

function updateOrderStats(stats) {
	var jsonData = "[";
	var cnt = grid_window_product.getStore().data.items.length;
	var form = Ext.getCmp('frmInvoice');
	
	for(var i = 0; i < cnt; i++) {
		jsonData += Ext.encode(grid_window_product.getStore().data.items[i].data)+",";
	}
	
	jsonData = jsonData.substring(0,jsonData.length-1) + "]";
	
	form.submit({
		target : '',
		params : {	grid : jsonData,
					stats : stats
		},
		success : function(form,action) {
			Ext.Msg.alert('변경완료', action.result.message);
//								form.getForm().reset();
			form.reset();
			grid_window_product.getStore().removeAll();
			grid_itemlist.getStore().load();
			winInvoice.hide();
			
		},
		failure : function (form, action) {
			Ext.Msg.alert('기록실패', action.result ? action.result.message : '실패하였습니다');
		}
	});	
	
}