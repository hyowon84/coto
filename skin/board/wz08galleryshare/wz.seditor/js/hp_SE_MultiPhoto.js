nhn.husky.SE_MultiPhoto = jindo.$Class({
    name : "SE_MultiPhoto",
    $init : function(elAppContainer){
        this._assignHTMLObjects(elAppContainer);
    },

    _assignHTMLObjects : function(elAppContainer){
        this.oInputButton = cssquery.getSingle(".se_button_photo_multi", elAppContainer);
    },

    $ON_MSG_APP_READY : function(){
        this.oApp.exec("REGISTER_UI_EVENT",
                ["MultiPhoto", "click", "SE_TOGGLE_MULTIPHOTO_LAYER"]);
        //input button에 click 이벤트를 할당.
        this.oApp.registerBrowserEvent(this.oInputButton, 'click','OPEN_PHOTO_EDITOR');
    },

    $ON_SE_TOGGLE_MULTIPHOTO_LAYER : function(){
        this.oApp.exec("TOGGLE_TOOLBAR_ACTIVE_LAYER", [this.oDropdownLayer]);
    },
	
    $ON_OPEN_PHOTO : function(){
		alert("1");
    },	
	
    $ON_OPEN_PHOTO_EDITOR : function(){
		var _site_url = "http://" + document.domain;
		var _document_root = location.pathname;
		_document_root = _document_root.substr(0, _document_root.lastIndexOf("/")+1);

		var suburl = decodeURIComponent(location.href);
		suburl = decodeURIComponent(suburl);
		suburl = suburl.substring(suburl.indexOf('?')+1, suburl.length);
		var params = "?import=";
		params += "&exportMethod=BROWSER";
		params += "&exportTitle="+ encodeURIComponent("포토에디터");
		params += "&exportTo="+encodeURIComponent(_site_url+"/"+ _document_root +"/imgeditor.php?appId="+this.oApp.elPlaceHolder.id+"&"+suburl);
		window.open("http://s.lab.naver.com/pe/service"+ params ,"_blank","resizable=yes,fullscreen=yes,directories=no,menubar=no,toolbar=no,location=no,status=no,copyhistory=no,width=1000,height=650");
    },
	
    $ON_PASTE_NOW_DATE : function(){
		alert("1");
        this.oApp.exec("PASTE_HTML", [new Date()]);
    }
});
