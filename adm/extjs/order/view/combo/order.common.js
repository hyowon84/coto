/*콤보박스 필터링속성
	haseReverseFilter:true,
	listeners:{
		  expand:function(picker){
				var store = picker.getStore();
					 store.suspendEvents();
					 store.clearFilter();
					 store.resumeEvents();
				if(picker.haseReverseFilter){
					 store.filter([{
						  fn: function(record) {
								return record.get('type') >= 2;
						  }
					 }]);
				}else{
					 store.filter([{
							  fn: function(record) {
								 return record.get('type') <= 2;
							  }
					 }]);
				}
		  }
	 }
*/
