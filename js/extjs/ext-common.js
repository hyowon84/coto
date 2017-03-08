var icn_e1 = "<img src='/images/icons/comment_edit.png' />";



/*그리드 서머리컬럼 포맷 */
function rendererSummaryFormat(value, summaryData, dataIndex) {
	var formatValue = Ext.util.Format.number(value, "0,000");
	var template;
	
	/* DEFAULT : 합계, 예외적인것만 정의 */
	switch(true) {
		case (dataIndex.search(/QTY/i) != -1):
			template = '{0} EA';
			break;
		case (dataIndex.search(/OZ/i) != -1):
			template = '{0} oz';
			formatValue = Ext.util.Format.number(value, "0,000.00");
			break;
		case (dataIndex.search(/DON/i) != -1):
			
			template = '{0} 돈';
			formatValue = Ext.util.Format.number(value, "0,000.00");
			break;
		case (dataIndex.search(/.*GRAM/i) != -1):
			template = '{0} g';
			break;
		case (dataIndex.search(/(TOTAL_PRICE)/i) != -1):
			template = '합계: {0}';
			formatValue = Ext.util.Format.number(value, "0,000.00");
			break;
		case (dataIndex.search(/.*PRICE/i) != -1):
			template = '￦ {0}';
			break;
		case (dataIndex.search(/PER/i) != -1):
			template = '{0} %';
			break;
		case (dataIndex.search(/(metal|fund)_type/i) != -1):
			template = 'TOTAL : ';
			break;
		case (dataIndex.search(/(ESTIMATE|FLOWPRICE)/i) != -1):
			template = '￦ {0}';
			break;
		default:
			template = '{0}';
			break;				
	}
	
	
	return Ext.String.format(template, formatValue);
}

/*그리드 컬럼의 데이터 포맷*/
function rendererColumnFormat(value,metaData,record) {
	
	var formatValue = Ext.util.Format.number(value, "0,000");
	var template;
	
	var v_colNM = (metaData.column) ? metaData.column.dataIndex : '';
	
	/* DEFAULT : 합계, 예외적인것만 정의 */
	switch(true) {
		case  (v_colNM.search(/QTY/i) != -1):
			template = '{0} EA';
			break;
		case (v_colNM.search(/OZ/i) != -1):
			template = '{0} oz';
			formatValue = Ext.util.Format.number(value, "0,000.00");
			break;
		case (v_colNM.search(/DON/i) != -1):
			template = '{0} 돈';
			formatValue = Ext.util.Format.number(value, "0,000.00");
			break;
		case (v_colNM.search(/GRAM/i) != -1):
			template = '{0} g';
			break;
		case (v_colNM.search(/PRICE/i) != -1):
			template = '{0}';
			break;
		case  (v_colNM.search(/PER/i) != -1):
			template = '{0} %';
			formatValue = Ext.util.Format.number(value, "0,000.0");
			break;
		case  (v_colNM.search(/YEAR/i) != -1):
			template = '{0} 년';
			formatValue = Ext.util.Format.number(value, "0,000");
			break;
		default:
			template = '{0}';
			break;				
	}
	
	return Ext.String.format(template, formatValue);
}


/*그리드 컬럼 렌더러 - 이미지태그*/
function rendererImage (value) {
	return '<img src="' + value + '" width=40 height=40 />';
}

/*그리드 컬럼 렌더러 - 콤보박스*/
function rendererCombo(value,metaData,record) {
	
	var combo = metaData.column.getEditor();
	combo.allowBlank = true;
	
	if(value && combo && combo.store && combo.displayField){
		var index = combo.store.findExact(combo.valueField, value);
		if(index >= 0){
			return combo.store.getAt(index).get(combo.displayField);
		}
	}
	return (value) ? value : '' ;
}

/*그리드 리스너 - edit*/
function listenerEditFunc (editor, e, eOpts) {
	if(globalData.temp == null) {
		globalData.temp = [];
	}
	globalData.temp.push([editor.context.rowIdx, editor.context.field, editor.context.originalValue]);
}

/*그리드 리스너 - afterRenderer*/
function listenerAfterRendererFunc(obj, opt) 
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

/*주문상태값에 따른 css클래스 셋팅*/
function orderStatsColorSetting(record, index) {
	var stats = record.get('stats');
	var v_class = 'clayorder_stats_' + stats;
	return v_class;
}


/*입출금유형에 따른 css클래스 셋팅*/
function colorSettingBankStats(record, index) {
	var stats = record.get('bank_type');
	var v_class = 'bankdb_bank_type_' + stats;
	return v_class;
}



/*공구상태값에 따른 css클래스 셋팅*/
function gpinfoStatsColorSetting(record, index) {
	var stats = record.get('stats');
	var v_class = 'gpinfo_stats_' + stats;
	return v_class;
}


function ivStatsColorSetting(record, index) {
	var stats = record.get('iv_stats');
	var v_class = 'invoice_stats_' + stats;
	return v_class;
}



/*리스너 기본 셋팅*/
var defaultListener = {
	selectionchange: function(view, records) {
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

/* 통계용 리스너 기본셋팅(서머리 토글 off) */
var statsDefaultListener = {
	selectionchange: function(view, records) {
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
		
		this.getView().getFeature('group').toggleSummaryRow();
	}
}

var statsFeatures = [
	{
		id : 'group',
		ftype : 'groupingsummary',
		groupHeaderTpl: '{name}',
		hideGroupedHeader: true,
		enableGroupingMenu: false,
		collapsible : false
	},{
		ftype: 'summary',
		dock: 'bottom'
	}
];


var statsViewConfig = {
	stripeRows: true,
	forceFit: true,
	getRowClass: function(record, index) {
	}
}

/* 그리드 레코드 삭제 */
function deleteGridRecord(grid) {
	var sm = grid.getSelectionModel().getSelection();

	if(sm.length) {

		for(var i = 0; i < sm.length; i++) {
			grid.store.remove(sm[i]);
		}
	}
}


/* 그리드의 선택된 1행 삭제 */
function delSelectedGrid1Row(grid) {
	var sm = grid.getSelectionModel().getSelection();


	if(sm.length == 1) {
		Ext.MessageBox.confirm('확인', '정말 삭제하시겠습니까?', 
			function(button) {
				if (button == 'yes') {
					grid.store.remove(sm[0]);
				}
				else {
					
				}
			});
	} else {
		Ext.Msg.alert('위험','발주서가 '+sm.length+'개 이상 선택되어있습니다 1개씩 꼼꼼히 삭제바랍니다');
		return false;
	}
}

/* 창 리사이징할때 패널도 자동리사이징 */
function panelResize(main_panel) {
	Ext.EventManager.onWindowResize(function () {
		var width = Ext.getBody().getViewSize().width;
		var height = Ext.getBody().getViewSize().height;
		main_panel.setSize(width, height);
	});
}