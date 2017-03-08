/*************************************************************************/
//주문관리 관련 start

/*결제유형*/
Ext.define('Ext.store.order.paytype',{
	extend: 'Ext.data.Store',
	fields:['name','value'],
	data:[
		{name:'무통장',		value:'P01'},
		{name:'카드결제',		value:'P02'},
		{name:'외화달러',		value:'P03'},
		{name:'귀금속결제',	value:'P04'},
		{name:'현금결제',	value:'P05'}
	]
});

/*배송유형*/
Ext.define('Ext.store.order.delivery_type',{
	extend: 'Ext.data.Store',
	fields:['name','value'],
	data:[
		{name:'주문시 결제',		value:'D01'},
		{name:'수령후 지불',		value:'D02'},
		{name:'방문수령',			value:'D03'},
		{name:'통합배송요청',	value:'D04'},
		{name:'배달',					value:'D05'},
		{name:'무료배송',			value:'D00'}
	]
});

/*현금영수증 신청유형*/
Ext.define('Ext.store.order.cashreceipt_type',{
	extend: 'Ext.data.Store',
	fields:['name','value'],
	data:[
		{	name:'-선택안함-',		value:''		},
		{	name:'개인소득공제',		value:'C01'	},
		{	name:'사업자지출증빙',	value:'C02'	},
		{	name:'세금계산서',		value:'C03'	}
	]
});

/*스팟시세일 경우 계산공식*/
Ext.define('Ext.store.order.cashreceipt_yn',{
	extend: 'Ext.data.Store',
	fields:['name','value'],
	data:[
		{	name:'미신청',	value:'N'	},
		{	name:'신청',	value:'Y'	}
	]
});



/* 주문상태 */
Ext.define('Ext.store.order.stats',{
	extend: 'Ext.data.Store',
	autoLoad : true,
	fields:['name','value','filter'],
	sorters:[
		{
			property:'order',
			direction:'ASC'
		}
	],
	data: [
		['-전체보기-'],
		['주문신청','00'],
		['귀금속결제대기','05'],
		['입금요청','10'],
		['선배송예정,미결제','15'],
		['선배송완료,미결제','17'],
		['결제완료','20'],
		['통합배송요청','22'],
		['배송대기중','25'],
		['직배대기중','30'],
		['픽업대기중','35'],
		['그레이딩대기','39'],
		['배송완료','40'],
		['직접배송완료','50'],
		['픽업완료','60'],
		['반품요청','70'],
		['반품완료','75'],
		['교환요청','80'],
		['교환완료','85'],
		['환불완료','90',''],
		['취소','99'],
		['코투재고','900','']
	]
});

/* SMS예제 */
Ext.define('Ext.store.order.smsex',{
	extend: 'Ext.data.Store',
	fields:['name','value'],
	sorters:[
		{
			property:'order',
			direction:'ASC'
		}
	],
	data: [
		['입금요청','10'],
		['결제완료','20'],
		['배송예정','25'],
		['직배예정','30'],
		['픽업예정','35'],
		['배송완료','40'],
		['환불완료','90']
	]
});

//주문관리 관련 end
/*************************************************************************/


/*************************************************************************/
//입출금 관련 start

/*입출금 유형*/
Ext.define('Ext.store.bank.banktype',{
	extend: 'Ext.data.Store',
	fields:['name','value'],
	data:[
		{	name:'-전체선택-',		value:'ALL'	},
		{	name:'상품주문',			value:'B01'	},
		{	name:'카드판매수금',	value:'B09'	},
		{	name:'해외주문',			value:'B02'	},
		{	name:'통관비',				value:'B03'	},
		{	name:'매장매입',			value:'B04'	},
		{	name:'식대',					value:'B05'	},
		{	name:'급여',					value:'B10'	},
		{	name:'기타지출',			value:'B06'	},
		{	name:'환불',					value:'B07'	},
		{	name:'지출통장',			value:'B08'	},
		{	name:'공란',					value:'EMPTY'	}
	]
});

/*입출금 세금처리유형*/
Ext.define('Ext.store.bank.taxtype',{
	extend: 'Ext.data.Store',
	fields:['name','value'],
	data:[
		{	name:'-선택안함-',			value:''	},
		{	name:'현금영수증',			value:'T01'	},
		{	name:'사업자지출증빙',	value:'T02'	},
		{	name:'세금계산서',			value:'T03'	}
	]
});
//입출금 관련 end
/***************************************************************************/



/*공동구매 상태값*/
Ext.define('Ext.store.item.gpstats',{
	extend: 'Ext.data.Store',
	fields:['name','value'],
	data:[
		{	name:'공구접수',			value:'00'	},
		{	name:'주문마감',			value:'05'	},
		{	name:'발주신청',			value:'10'	},
		{	name:'송금완료',			value:'20'	},
		{	name:'통관완료',			value:'30'	},
		{	name:'국내도착',			value:'40'	},
		{	name:'공구종료',			value:'99'	}
	]
});

/*발주서 상태값*/
Ext.define('Ext.store.item.ivstats',{
	extend: 'Ext.data.Store',
	fields:['name','value'],
	data:[
		{	name:'재고세팅',			value:'00'	},
		{	name:'발주완료',			value:'05'	},
		{	name:'송금완료',			value:'10'	},
		{	name:'통관완료',			value:'20'	},
		{	name:'국내도착',			value:'30'	},
		{	name:'입고완료',			value:'40'	},
		{	name:'삭제(숨김)',		value:'99'	}
	]
});

Ext.define('Ext.store.dealers', {
	extend: 'Ext.data.ArrayStore',
	alias: 'store.dealers',
	model: 'model.dealers',
	storeId: 'dealers',
	data: [
		[0, 'AP', 'APMEX'],
		[1, 'GV', 'GAINSVILLE'],
		[2, 'MC', 'MCM'],
		[3, 'PA', 'PARADISE'],
		[4, 'BX', 'BULLION EXCHANGE'],
		[5, 'OD', 'OTHER DEALER']
	]
});


/*예 아니오*/
Ext.define('Ext.store.item.yesno',{
	extend: 'Ext.data.Store',
	fields:['name','value'],
	data:[
		{	name:'-선택-',	value:''	},
		{	name:'Y',			value:'1'	},
		{	name:'N',			value:'0'	}
	]
});

/*금속유형*/
Ext.define('Ext.store.item.metaltype',{
	extend: 'Ext.data.Store',
	fields:['name','value'],
	data:[
		{	name:'-금속-',		value:''	},
		{	name:'금',				value:'GL'},
		{	name:'은',				value:'SL'},
		{	name:'기타',			value:'ETC'}
	]
});

/*가격반영유형*/
Ext.define('Ext.store.item.pricetype',{
	extend: 'Ext.data.Store',
	fields:['name','value'],
	data:[
		{	name:'-유형-',		value:''	},
		{	name:'원화(￦)',	value:'W'	},
		{	name:'달러($)',	value:'N'	},
		{	name:'스팟시세',	value:'Y'	}
	]
});

/*스팟시세일 경우 계산공식*/
Ext.define('Ext.store.item.spottype',{
	extend: 'Ext.data.Store',
	fields:['name','value'],
	data:[
		{	name:'-스팟옵션-',	value:''	},
		{	name:'1oz이상($)',	value:'U$'},
		{	name:'1oz이하($)',	value:'D$'},
		{	name:'%',					value:'%'	},
		{	name:'￦',					value:'￦'	}
	]
});


/*화폐유형*/
Ext.define('Ext.store.item.moneytype',{
	extend: 'Ext.data.Store',
	fields:['name','value'],
	data:[
		{	name:'USD',		value:'USD'	},
		{	name:'CNY',		value:'CNY'	},
		{	name:'EUR',		value:'EUR'	},
		{	name:'HKD',		value:'HKD'	},
		{	name:'AUD',		value:'AUD'	}
	]
});

Ext.define('store.dealers', {
	extend: 'Ext.data.ArrayStore',
	alias: 'store.dealers',
	model: 'model.dealers',
	storeId: 'dealers',
	data: [
		[0, 'AP', 'APMEX'],
		[1, 'GV', 'GAINSVILLE'],
		[2, 'MC', 'MCM'],
		[3, 'PA', 'PARADISE'],
		[4, 'BX', 'BULLION EXCHANGE'],
		[5, 'OD', 'OTHER DEALER'],
		[6, 'CT', '코인스투데이'],
	]
});


/*

Ext.define('Ext.store.moneytype', {
	extend: 'Ext.data.ArrayStore',
	alias: 'store.moneytype',
	model: 'model.combo',
	storeId: 'moneytype',
	data: [//보이는거, 선택후값
		[0, 'USD', 'USD'],
		[1, 'CNY', 'CNY'],
		[2, 'EUR', 'EUR'],
		[3, 'HKD', 'HKD'],
		[4, 'AUD', 'AUD']
	]
});

Ext.define('Ext.store.wrtype', {
	extend: 'Ext.data.ArrayStore',
	alias: 'store.wrtype',
	model: 'model.combo',
	storeId: 'wrtype',
	data: [
		[0, '00', '은행'],
		[1, '10', '페이팔'],
		[2, '20', '마운틴'],
	]
});
	
*/


