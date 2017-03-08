
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
				url : 'baesong_update.php',
				layout: {type: 'auto'},
				region: 'west',
				width: '100%',
				height:600,
				autoScroll:true,
				split: false,
				collapsible: false,
				floatable: false,
				items:[
						{//상태변경시 pk값으로 이용
								xtype: 'textfield',
								id : 'hf_hphone',
								hidden : true,
								name: 'hphone'
						},
						{
							region: 'center',
							columnWidth: 1,
							items:[grid_window_baesong]		/*발주입력품목그리드*/
						}
				]	//items item end
				,
				buttons: [
							{
								id : 'combo_odstats',
								xtype: 'combobox',
								width : 140,
								forceSelection: true,
								editable: false,
								triggerAction: 'all',
								allowBlank: false, 
								displayField:'name',
								valueField:'value',
								value : '25',
								store: Ext.create('Ext.store.item.odstats')
							},
							{
								id		: 'btn_update_odstats',
								text	: '상태변경',
								iconCls	: 'icon-table_edit',
								handler : function() {
									var v_stats = Ext.getCmp('combo_odstats').getValue();
									updateOrderStats(v_stats);	//배송대기
								}
							},
							{
								xtype: 'textfield',
								id : 'refund_money',
								emptyText: '환불금액',
								name: 'refund_money'
							},
							{
								xtype: 'textfield',
								id : 'delivery_invoice',
								emptyText: '송장번호',
								name: 'delivery_invoice'
							},
							{
								id		: 'btn_update_dvno',
								text	: '운송장갱신',
								iconCls	: 'icon-table_edit',
								handler : function() {
									var v_no = Ext.getCmp('delivery_invoice').getValue();
									var v_refund_money = Ext.getCmp('refund_money').getValue();
									updateDeliveryInvoice(v_no, v_refund_money);	//배송대기
								}
							},
							{
								text: '취소',
								handler: function() {
									Ext.getCmp('frmInvoice').getForm().reset();
									winInvoice.hide();
								}
							}
				
				]
			}	//form element
		]
});

/* 주문목록 일괄 상태값 변경 */
function updateOrderStats(v_stats) {
	var jsonData = "[";
	var cnt = grid_window_baesong.getStore().data.items.length;
	var form = Ext.getCmp('frmInvoice');
	
	for(var i = 0; i < cnt; i++) {
		jsonData += Ext.encode(grid_window_baesong.getStore().data.items[i].data)+",";
	}
	
	jsonData = jsonData.substring(0,jsonData.length-1) + "]";
	
	form.submit({
		target : '',
		params : {	mode  : 'statsUpdate',
					grid : jsonData,
					stats : v_stats
		},
		success : function(form,action) {
			Ext.Msg.alert('변경완료', action.result.message);
//								form.getForm().reset();
			form.reset();
			grid_window_baesong.getStore().removeAll();
			grid_orderlist.getStore().load();
			winInvoice.hide();
			
		},
		failure : function (form, action) {
			Ext.Msg.alert('기록실패', action.result ? action.result.message : '실패하였습니다');
		}
	});	
	
}

/* 주문목록 일괄 운송장 변경 */
function updateDeliveryInvoice(v_no,v_refund_money) {
	var jsonData = "[";
	var cnt = grid_window_baesong.getStore().data.items.length;
	var form = Ext.getCmp('frmInvoice');
	
	for(var i = 0; i < cnt; i++) {
		jsonData += Ext.encode(grid_window_baesong.getStore().data.items[i].data)+",";
	}
	
	jsonData = jsonData.substring(0,jsonData.length-1) + "]";
	
	form.submit({
		target : '',
		params : {	mode : 'deliveryUpdate',
					grid : jsonData,
					delivery_invoice : v_no,
					refund_money : v_refund_money
		},
		success : function(form,action) {
			Ext.Msg.alert('변경완료', action.result.message);
			form.reset();
			grid_window_baesong.getStore().removeAll();
			grid_orderlist.getStore().load();
			grid_shiped_list.getStore().load();
			winInvoice.hide();
		},
		failure : function (form, action) {
			Ext.Msg.alert('기록실패', action.result ? action.result.message : '실패하였습니다');
		}
	});
}

/* 주문목록 일괄 환불금액 변경 */
function updateRefundMoney(v_refund_money) {
	var jsonData = "[";
	var cnt = grid_window_baesong.getStore().data.items.length;
	var form = Ext.getCmp('frmInvoice');

	for(var i = 0; i < cnt; i++) {
		jsonData += Ext.encode(grid_window_baesong.getStore().data.items[i].data)+",";
	}

	jsonData = jsonData.substring(0,jsonData.length-1) + "]";

	form.submit({
		target : '',
		params : {	mode : 'refundMoneyUpdate',
			grid : jsonData,
			refund_money : v_refund_money
		},
		success : function(form,action) {
			Ext.Msg.alert('변경완료', action.result.message);
//								form.getForm().reset();
			form.reset();
			grid_window_baesong.getStore().removeAll();
			grid_orderlist.getStore().load();
			winInvoice.hide();

		},
		failure : function (form, action) {
			Ext.Msg.alert('기록실패', action.result ? action.result.message : '실패하였습니다');
		}
	});

}