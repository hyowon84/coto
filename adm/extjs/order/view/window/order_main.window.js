
var winSmsForm = Ext.create('widget.window', {
	id : 'win_SmsForm',
	title: 'SMS전송창',
	reference: 'popupWindow',
	header: {
		titlePosition: 2,
		titleAlign: 'center'
	},
	closable: true,
	closeAction: 'hide',
	resizable   : false,
	maximizable: true,
	animateTarget: 'sendSMS',		/*발주*/
	width: 1080,
	height: 340,
	tools: [],
	layout: {
		type: 'border',
		padding: 5
	},
	items: [panel_winSms]	//items item end
});
