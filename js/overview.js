AC.define("iphone/shared/assetLoader/AssetLoader",["require","defer/Deferred"],function(f){var d=f("defer/Deferred");
function e(a,b){this._assetsToLoad=[].concat(a);this._type=b||"img"}e.prototype={load:function(){this._assetsLoaded=[];
this._assetsCountLoaded=0;this._defer=new d();this._failure=false;this._assetsToLoad.forEach(this._loadAsset.bind(this));
return this._defer.promise()},_progress:function(a,b){this._defer.progress(this._assetsLoaded[a]=b);
this._assetsCountLoaded+=1;if(this._assetsCountLoaded===this._assetsToLoad.length){this._defer.resolve(this._assetsLoaded)
}},_error:function(a){this._failure=true;this._defer.reject(a.target)},_loadAsset:function(b,a){var c;
if(!this._failure){c=document.createElement(this._type);c.onload=this._progress.bind(this,a,c);
c.onerror=this._error.bind(this);c.src=b}}};return e});AC.define("iphone/shared/element/eachAncestor",["require"],function(d){function c(b,a){b=AC.Element.getElementById(b);
var f=b.parentNode;if(AC.Element.isElement(f)){while(f){if(typeof a==="function"){if(a(f)===false){break
}}if(f!==document.body){f=f.parentNode}else{f=null}}}}return c});AC.define("iphone/shared/element/descendantOf",["require","iphone/shared/element/eachAncestor"],function(d){var c=d("iphone/shared/element/eachAncestor");
return function(a,b){var f=false;c(a,function(e){if(e===b){f=true;return false}});
return f}});AC.define("eventEmitter/EventEmitter",[],function(){var i=function(a){this.context=a
};var j=i.prototype;var f=function(){if(!this.hasOwnProperty("_events")&&typeof this._events!=="object"){this._events={}
}return this._events};var g=function(e,c){var b=e[0];var a=e[1];var d=e[2];if(typeof b==="object"){for(var l in b){c.call(this,l,b[l],d)
}}if(typeof b==="string"){b=b.split(" ");b.forEach(function(k){c.call(this,k,a,d)
},this)}};var h=function(b,a){var e;var d;var c;e=f.call(this)[b];if(!e){return
}for(d=0,c=e.length;d<c;d++){if(a(e[d],d)){break}}};j.on=function(){var a=f.call(this);
g.call(this,arguments,function(c,b,d){a[c]=a[c]||(a[c]=[]);a[c].push({callback:b,context:d})
});return this};j.once=function(){g.call(this,arguments,function(c,a,d){var b=function(e){a.call(d||this,e);
this.off(c,b)};this.on(c,b,this)});return this};j.off=function(b,a){var c=f.call(this);
if(arguments.length===0){c={};return this}if(arguments.length===1&&c[b]){c[b]=[];
return this}var d=-1;h.call(this,b,function(e,l){d=l;if(e.callback===a){return true
}});if(d===-1){return}c[b].splice(d,1);return this};j.trigger=function(b,a){b=b.split(" ");
b.forEach(function(c){h.call(this,c,function(d){d.callback.call(d.context||this.context||this,a)
}.bind(this))},this);return this};return i});AC.define("iphone/shared/responsive/windowResizeTimeout",["require"],function(q){var o=AC.Element;
var p=AC.Environment;var n=[];var r;var m;var j=function(a){if(m){clearTimeout(m)
}m=window.setTimeout(a,300)};var k=function(b){var a,c;for(a=0,c=n.length;a<c;a+=1){n[a]()
}};var l=window.innerWidth;r=o.addEventListener(window,"resize",function(c){var b=window.innerWidth;
var a=false;if(p.Browser.os.toLowerCase()==="ios"&&(b>l||b<l)){a=true;if(p.Browser.version>=7){a=false
}}l=b;if(a===false){j(k)}});return function(a){n.push(a)}});AC.define("iphone/shared/responsive/Controller",["require","iphone/shared/assetLoader/AssetLoader","iphone/shared/element/descendantOf","eventEmitter/EventEmitter","iphone/shared/responsive/windowResizeTimeout"],function(C){var y=C("iphone/shared/assetLoader/AssetLoader");
var x=AC.Element;var z=AC.Environment;var D=AC.Retina;var v=AC.ViewMaster;var u=C("iphone/shared/element/descendantOf");
var p=C("eventEmitter/EventEmitter");var r=Event.Listener.listenForEvent||function(){};
var q=C("iphone/shared/responsive/windowResizeTimeout");var E=[];var t=function(b){var a=(b.indexOf("#")>-1)?b.replace(/.*#/,""):"";
var c=D.sharedInstance().bestSrc(b).replace(/#.*/,"");return(a)?c+"#"+a:c};var w=function(){return{width:window.innerWidth||document.documentElement.clientWidth||document.body.clientWidth,height:window.innerHeight||document.documentElement.clientHeight||document.body.clientHeight}
};var B=function(){return(Math.abs(window.orientation)!==90)};var A=function(b,a,c){this.element=b||document.body;
this.responsePoints=a||{};this.options=c||{};this.options.classNamePrefix=this.options.classNamePrefix||"ac-responsive";
this.options.attribute=this.options.attribute||"data-responsive-src";this.options.tabletPortraitAttribute=this.options.tabletPortraitAttribute||"data-responsive-tablet-portrait-src";
this.options.tabletLandscapeAttribute=this.options.tabletLandscapeAttribute||"data-responsive-tablet-landscape-src";
this.options.handheldPortraitAttribute=this.options.handheldPortraitAttribute||"data-responsive-handheld-portrait-src";
this.options.handheldLandscapeAttribute=this.options.handheldLandscapeAttribute||"data-responsive-handheld-landscape-src";
this.delegate=this.options.delegate||{};this.items=x.selectAll("["+this.options.attribute+"]",this.element);
E.push(this);return this};A.prototype=new p();A.prototype.addListeners=function(){this.respond();
q(this.respond.bind(this));var a=r(v,"ViewMasterWillShowNotification",false,function(e){var f,d,c,g;
f=e.event_data.data.incomingView;d=e.event_data.data.outgoingView;for(c=0,g=E.length;
c<g;c+=1){if(d&&u(d.content,E[c].element)){E[c].respondForItem(f.content)}}});var b=r(v,"ViewMasterDidShowNotification",false,function(e){var f,d,c,g;
f=e.event_data.data.incomingView;d=e.event_data.data.outgoingView;for(c=0,g=E.length;
c<g;c+=1){if(f&&u(f.content,E[c].element)){E[c].addItem(x.select("img",f.content)||f.content)
}}})};A.prototype.respondForClassNameAndItemAndSource=function(e,c,b){var g,d,h,a,f=this;
if(!z.Feature.isDesktop()){g=c.getAttribute(this.options.tabletPortraitAttribute);
d=c.getAttribute(this.options.tabletLandscapeAttribute);handheldPortraitSource=c.getAttribute(this.options.handheldPortraitAttribute);
handheldLandscapeSource=c.getAttribute(this.options.handheldLandscapeAttribute);
if(z.Feature.isHandheld()&&B()&&handheldPortraitSource){e="handheld-portrait";b=handheldPortraitSource
}else{if(z.Feature.isHandheld()&&!B()&&handheldLandscapeSource){e="handheld-landscape";
b=handheldLandscapeSource}else{if(B()&&g){e="tablet-portrait";b=g}else{if(!B()&&d){e="tablet-landscape";
b=d}}}}}b=t(b);h=new y(b);this.toggleClassName(this.element,e);if(c.src){c.setAttribute("data-previous-src",c.getAttribute("src"));
c.removeAttribute("src");c.removeAttribute("width");c.removeAttribute("height")
}else{if(c.href){c.setAttribute("href",b)}}this.trigger("responded",{target:this,snap:e});
this._className=e;a=h.load();a=a.then(function(m,i){var k;var l;var j=new Image();
j.src=m;k=(j.src.match("2x"))?Math.round(j.width/2):j.width;l=(j.src.match("2x"))?Math.round(j.height/2):j.height;
if(i.tagName.toLowerCase==="a"){i.href=j.src}else{if(i.style.cssText.match("DXImageTransform.Microsoft.AlphaImageLoader")){i.setAttribute("src",i.getAttribute("data-previous-src"));
x.setStyle(i,{filter:i.style.filter.replace(/(src=['"]*)[^'"]*(['"]*)/,"$1"+m+"$2"),width:k+"px",height:l+"px"})
}else{i.src=m;i.width=k;i.height=l}}if(typeof f.delegate.didLoad==="function"){f.delegate.didLoad(i)
}}.bind(this,b,c))};A.prototype.getClassName=function(){return this._className};
A.prototype.toggleClassName=function(a,b){a.className=a.className.replace(/\s*ac\-responsive\-[^\s]*/g,"").replace(/  /g," ");
x.addClassName(a,(this.options.classNamePrefix+"-"+b).toLowerCase())};A.prototype.addItem=function(a){if(this.items.indexOf(a)===-1){this.items.push(a)
}};A.prototype.respondForItem=function(f,h){if(!h){h=w()}var d,g,e,i,a,j,b,c;for(d in this.responsePoints){g=this.responsePoints[d];
for(a=0,j=g.length;a<j;a+=1){e=g[a];if(e<=h.height){i=e}else{break}}b=g.indexOf(i);
if(!f.getAttribute(this.options.attribute)){f=x.select("["+this.options.attribute+"]",f)
}c=JSON.parse(f.getAttribute(this.options.attribute));this.respondForClassNameAndItemAndSource(d+"-"+i+"-to-"+g[b+1],f,c[b])
}};A.prototype.respond=function(a){var b,c;for(b=0,c=this.items.length;b<c;b+=1){this.respondForItem(this.items[b],a)
}};return A});AC.define("iphone/shared/responsive/builder",["require","iphone/shared/responsive/Controller"],function(v){var q=AC.Element;
var o=v("iphone/shared/responsive/Controller");var r=function(a){return(a.match(/:\/\//))?a.replace(/(\w+:\/\/[^\/]*)\/.*/,"$1/"):""
};var m=function(a){return(a.indexOf("#")>-1)?a.replace(/.*#/,"#"):""};var l=function(d,c,a){var b=new RegExp(d.replace(/\w*:\/\/[^\/]*/,""));
if(!a){return a}if(!a.match(/:\/\//)&&d.match(/:\/\//)){a=d+a}a=a.replace(/([^:]\/)\//,"$1");
a=a+c;return a};var p=function(a,c,b){if(!b.match(/:\/\//)&&a.match(/:\/\//)){b=b.replace(/([^,;:}{\s\[\]]*)([:,\]])/g,'"'+a+"$1"+c+'"$2');
b=b.replace(/([^:]\/)\//g,"$1")}else{b=b.replace(/([^,;:}{\s\[\]]*)([:,\]])/g,'"$1'+c+'"$2');
b=b.replace(/([^:]\/)\//g,"$1")}return JSON.parse(b)};var n=function(a){a=a.replace(/([^,;:}{\s\[\]]*)([:,\]])/g,'"$1"$2');
return JSON.parse(a)};var u=function(e){var a,c,d,b;a=n("{"+e+"}");for(c in a){for(d=0,b=a[c].length;
d<b;d+=1){a[c][d]=parseInt(a[c][d],10)}if(a[c].indexOf(0)<0){a[c].push(0)}if(a[c].indexOf(Infinity)<0){a[c].push(Infinity)
}a[c].sort(function(f,g){return(f-g)})}return a};var w=function(j,c,C,f,D,k){var a,B,i,g,h,E,e,b,d;
for(a=0,B=j.length;a<B;a+=1){i=j[a];g=i.src||i.href;h=[];h[0]=g;E=r(g);e=m(g);h=h.concat(p(E,e,i.getAttribute(c)));
i.setAttribute(c,JSON.stringify(h));b=l(E,e,i.getAttribute(C));if(b){i.setAttribute(C,b)
}d=l(E,e,i.getAttribute(f));if(d){i.setAttribute(f,d)}mobilePortraitSource=l(E,e,i.getAttribute(D));
if(mobilePortraitSource){i.setAttribute(D,mobilePortraitSource)}mobileLandscapeSource=l(E,e,i.getAttribute(k));
if(mobileLandscapeSource){i.setAttribute(k,mobileLandscapeSource)}}};var t=function(d,h){var f,a,k,i,g;
d=d||document.body;h=h||{};h.classNamePrefix=h.classNamePrefix||"ac-responsive";
h.attribute=h.attribute||"data-responsive-value";h.tabletPortraitAttribute=h.tabletPortraitAttribute||"data-responsive-tablet-portrait-src";
h.tabletLandscapeAttribute=h.tabletLandscapeAttribute||"data-responsive-tablet-landscape-src";
h.handheldPortraitAttribute=h.handheldPortraitAttribute||"data-responsive-handheld-portrait-src";
h.handheldLandscapeAttribute=h.handheldLandscapeAttribute||"data-responsive-handheld-landscape-src";
h.elementAttribute=h.elementAttribute||"data-responsive-src";try{f=document.createElement("style");
f.id="ac-responsive-loading";f.innerHTML="."+h.classNamePrefix+" *["+h.elementAttribute+"] { visibility:hidden; }";
document.head.appendChild(f)}catch(j){}i=[];g=q.selectAll("."+h.classNamePrefix,d);
var y;var c=[];for(a=0,k=g.length;a<k;a+=1){var b=g[a];var e=u(b.getAttribute(h.attribute));
w(q.selectAll("["+h.elementAttribute+"]",b),h.elementAttribute,h.tabletPortraitAttribute,h.tabletLandscapeAttribute,h.handheldPortraitAttribute,h.handheldLandscapeAttribute);
y=new o(b,e,{classNamePrefix:h.classNamePrefix,attribute:h.elementAttribute,handheldPortraitAttribute:h.handheldPortraitAttribute,handheldLandscapeAttribute:h.handheldLandscapeAttribute,tabletPortraitAttribute:h.tabletPortraitAttribute,tabletLandscapeAttribute:h.tabletLandscapeAttribute,delegate:{didLoad:(a===k-1)?function(){if(f.parentNode){f.parentNode.removeChild(f)
}}:null}});c.push(y)}return c};return t});AC.define("iphone/shared/responsive/ControllerSet",["require"],function(c){function d(a){this._controllers=a
}d.prototype={getControllerByElement:function(a){var b,f;for(b=0,f=this._controllers.length;
b<f;b+=1){if(this._controllers[b].element===a){return this._controllers[b]}}}};
return d});AC.define("iphone/shared/scene/observer/nav/Focus",["require"],function(c){function d(b,a){this.indicies=[];
for(var g=0,h=a.length;g<h;g++){this.indicies[g]=g;AC.Element.addEventListener(a[g]["element"],"focus",this.onFocused.bind(this,g,b),true)
}}d.prototype={onFocused:function(i,k,m){var l=this.indicies[i];var n=(document.documentElement.clientHeight||window.innerHeight||document.documentElement.offsetHeight);
var b=AC.Event.target(m);var a=k.show(l);if(a){a.then(function(){if(AC.Element.getBoundingBox(b).top>n){k.show(++l)
}else{if(AC.Element.getBoundingBox(b).bottom<0){k.show(--l)}}})}else{if(AC.Element.getBoundingBox(b).top>n){k.show(++l)
}else{if(AC.Element.getBoundingBox(b).bottom<0){k.show(--l)}}}this.indicies[i]=l;
document.body.scrollTop=0;window.scrollTo(0,0);AC.Element.setStyle(document.body,{overflow:"visible"});
window.requestAnimationFrame(function(){document.body.scrollTop=0;window.scrollTo(0,0);
AC.Element.setStyle(document.body,{overflow:""})})}};return d});AC.define("iphone/shared/ac_autogallery/Types/cases",[],function(){var f=AC.Element;
var d=AC.AutoGallery;var e=function(b){var i={},a,j;for(a=0,j=b.length;a<j;a+=1){if(b[a].name.indexOf("data-")===0){var c=b[a].name;
i[c]=b[a].value}}return i};d.addType("cases",{},function(){},"image-fadein",{delegate:{_links:[],_deviceLinks:[],_caseLinks:[],getDefaultDeviceColor:function(){var i,j;
var b=this.viewer.view.view().getAttribute("data-autogallery-cases-default-device-color")||"";
var c=this._deviceLinks[0].getAttribute("data-color")||"";var a="";for(i=0,j=this.viewer.orderedSections.length;
i<j;i+=1){if(!this.viewer.orderedSections[i].match("-default")){a=this.viewer.orderedSections[i].split("-")[2];
break}}return(b!=="")?b:(c!=="")?c:a},getDefaultCaseColor:function(){var i,j;var b=this.viewer.view.view().getAttribute("data-autogallery-cases-default-case-color")||"";
var c=this._caseLinks[0].getAttribute("data-color")||"";var a="";for(i=0,j=this.viewer.orderedSections.length;
i<j;i+=1){if(!this.viewer.orderedSections[i].match("-default")){a=this.viewer.orderedSections[i].split("-")[3];
break}}return(b!=="")?b:(c!=="")?c:a},getCurrentDeviceColorFromId:function(a){var b=a.split("-");
if(b[2]&&b[2]!=="default"){return b[2]}return this.getDefaultDeviceColor()},getCurrentCaseColorFromId:function(a){var b=a.split("-");
if(b[3]&&b[3]!=="default"){return b[3]}return this.getDefaultCaseColor()},activateLinksForSectionId:function(b){var c,i;
var j=this.getCurrentDeviceColorFromId(b);var a=this.getCurrentCaseColorFromId(b);
for(c=0,i=this._deviceLinks.length;c<i;c+=1){if(j===this._deviceLinks[c].getAttribute("data-color")&&this.viewer.currentSection.id.indexOf("-default")<0){this._deviceLinks[c].className+=" active"
}else{this._deviceLinks[c].className=this._deviceLinks[c].className.replace(/active/g,"").replace(/  /g," ")
}}for(c=0,i=this._caseLinks.length;c<i;c+=1){if(a===this._caseLinks[c].getAttribute("data-color")&&this.viewer.currentSection.id.indexOf("-default")<0){this._caseLinks[c].className+=" active"
}else{this._caseLinks[c].className=this._caseLinks[c].className.replace(/active/g,"").replace(/  /g," ")
}}},setLinksHrefValuesForSectionId:function(b){var c,i;var j=this.getCurrentDeviceColorFromId(b);
var a=this.getCurrentCaseColorFromId(b);for(c=0,i=this._deviceLinks.length;c<i;
c+=1){this._deviceLinks[c].setAttribute("href","#"+this.viewer.triggerClassName+"-"+this._deviceLinks[c].getAttribute("data-color")+"-"+a)
}for(c=0,i=this._caseLinks.length;c<i;c+=1){this._caseLinks[c].setAttribute("href","#"+this.viewer.triggerClassName+"-"+j+"-"+this._caseLinks[c].getAttribute("data-color"))
}},copySectionDataAttributesToRemoteContent:function(a){var b,c,i,n,m,l;b=f.select("img",a.content)||a.content;
c=a.triggers();for(i=0,n=c.length;i<n;i+=1){m=e(c[i].attributes);for(l in m){b.setAttribute(l,m[l])
}}},willShow:function(a,b,c){if(c&&c.id){this.activateLinksForSectionId(c.id)}},didAppendContent:function(a,b){var c=a.currentSection.triggers(),i,m,n,l;
for(i=0,m=c.length;i<m;i+=1){if(c[i].getAttribute("data-screen-src")){n=c[i].getAttribute("data-screen-src");
break}}if(n){l=new Image();l.src=n;l.className="screen";b.appendChild(l)}},didShow:function(b,i,k){var c,l,a;
this.viewer=b;if(!this._didShowInitial){a=this.viewer.view.view().up(".autogallery");
if(a){this._deviceLinks=f.selectAll(".color-nav-devices a",a);this._caseLinks=f.selectAll(".color-nav-cases a",a);
this._links=this._deviceLinks.concat(this._caseLinks)}for(c=0,l=this._links.length;
c<l;c+=1){f.addClassName(this._links[c],this.viewer.triggerClassName)}}if(k&&k.id){this.copySectionDataAttributesToRemoteContent(k);
this.setLinksHrefValuesForSectionId(k.id);if(!this._didShowInitial&&this.viewer.currentSection&&this.viewer.currentSection.id.indexOf("-default")<0){this.activateLinksForSectionId(k.id);
this.viewer.view.view().style.height=AC.Element.getBoundingBox(k.content).height+"px";
this.didAppendContent(this.viewer,k.content)}}this._didShowInitial=true}}})});AC.define("animationSequencer/Clock",[],function(){function c(){this._currentTimeMS=0;
this._playbackRate=1;this._paused=true;this._resetStartTime()}var d=c.prototype;
d._updateCurrentTime=function(){var a,b=Date.now();if(this._paused){a=0}else{a=(b-this._startTime)
}this._currentTimeMS+=(a*this._playbackRate);this._startTime=b};d._resetStartTime=function(){this._startTime=Date.now()
};d.play=function(){this._resetStartTime();this._paused=false;return this};d.pause=function(){this._updateCurrentTime();
this._paused=true;return this};d.isPaused=function(){return this._paused};d.getCurrentTime=function(){this._updateCurrentTime();
return this._currentTimeMS/1000};d.setCurrentTime=function(a){if(isNaN(a)){return
}this._resetStartTime();this._currentTimeMS=a*1000};d.getPlaybackRate=function(){return this._playbackRate
};d.setPlaybackRate=function(a){if(isNaN(a)){return}this._playbackRate=a};return c
});AC.define("animationSequencer/Player/BasicPlayer",["require","eventEmitter/EventEmitter","animationSequencer/Clock"],function(f){var i=f("eventEmitter/EventEmitter");
var h=f("animationSequencer/Clock");function g(a,b){this.options=b||{};this._clip=a;
this._clock=this.options.clock||new h();this._paused=true;window.setTimeout(function(){this.trigger("canplay")
}.bind(this),0)}var j=g.prototype=new i();j.addEventListener=j.on;j.removeEventListener=j.off;
j.play=function(){this._paused=false;this._clock.play();this._update();this.trigger("play")
};j.pause=function(){this.setPaused(true);this._clock.pause();this.trigger("pause")
};j._updateCurrentTime=function(a){this._clock.setCurrentTime(a);this._lastTime=this._clip.setCurrentTime(a)
};j._update=function(){var c=this._clock.getCurrentTime();var b=this.getDuration();
var d=this._clock.getPlaybackRate();var e=d>0;var a=e&&c>=b;var l=!e&&c<=0;if(a||l){c=(a)?b:0;
this.pause();this._updateCurrentTime(c)}this.trigger("timeupdate",{previous:this._lastTime,time:c});
if(a){this.trigger("ended")}if(l){this.trigger("returned")}if(!this.isPaused()){this._updateCurrentTime(c);
window.requestAnimationFrame(this._update.bind(this))}};j._isValidTime=function(a){return(0<=a)&&(a<=this.getDuration())
};j.isPaused=function(){return this._paused};j.setPaused=function(a){this._paused=!!a
};j.getCurrentTime=function(){return this._clock.getCurrentTime()};j.setCurrentTime=function(a){if(this._isValidTime(a)){this.trigger("seeking",{time:a});
this._updateCurrentTime(a);this.trigger("seeked",{time:a})}};j.getPlaybackRate=function(){return this._clock.getPlaybackRate()
};j.setPlaybackRate=function(a){this._clock.setPlaybackRate(a);this.trigger("ratechange",{rate:a})
};j.getDuration=function(){return this._clip.getDuration()};return g});AC.define("animationSequencer/clip/TimedClip",[],function(){function c(a,b){b=b||{};
this._clip=a;this._startDelay=b.startDelay||0;this._loop=b.loop||false;this._fill=b.fill||"both"
}c.FILL_MODES=["none","forwards","backwards","both"];var d=c.prototype;d._show=function(){if(this._isHidden){this._isHidden=false;
this._clip.show()}};d.setEasingDirection=function(a){return this._clip.setEasingDirection(a)
};d._applyFill=function(a){if(this.getFill()==="none"){return}var b=this.getDuration();
var k=a>b;var l=this.getFill();var m=k&&l==="forwards";var n=!k&&l==="backwards";
var j=l==="both"||m||n;if(j){this._clip.setCurrentTime((k)?b:0)}};d._hide=function(){if(!this._isHidden){this._isHidden=true;
this._clip.hide()}};d.isPaused=function(){return this._paused};d.getCurrentTime=function(){return this._currentTime
};d.setCurrentTime=function(a,b){if(a<0||a>this.getDuration()){this._clip.inEffect=false;
this._applyFill(a)}else{this._clip.inEffect=true;this._clip.setCurrentTime(a,b)
}};d.getDuration=function(){return this._clip.getDuration()};d.getStartDelay=function(){return this._startDelay
};d.setStartDelay=function(){if(!isNaN(delay)){this._startDelay=delay}};d.getLoop=function(){return this._loop
};d.setLoop=function(a){this._loop=!!a};d.getFill=function(){return this._fill};
d.setFill=function(a){var b=c.FILL_MODES;if(b.indexOf(a.toLowerCase())!==-1){this._fill=a
}};return c});AC.define("animationSequencer/clip/CompositeClip",["require","animationSequencer/clip/TimedClip"],function(f){var g=f("animationSequencer/clip/TimedClip");
function h(a){if(a&&a.length){this._clips=a.map(this._ensureTimedClip);this._duration=this._calcDuration()
}}var e=h.prototype;e.addClip=function(a){a=this._ensureTimedClip(a);this._clips.push(a);
this._duration=this._calcDuration()};e._calcDuration=function(b){b=b||this._clips;
var a=b.reduce(function(d,c){var j=c.getStartDelay()+c.getDuration();return(j>d)?j:d
},0);return a};e.setEasingDirection=function(a){this._clips.forEach(function(b){b.setEasingDirection(a)
})};e._ensureTimedClip=function(a){if(!(a instanceof g)){a=new g(a)}return a};e._getLocalTime=function(b,a){return a-b.getStartDelay()
};e._getEligibleClips=function(){return this._clips};e.getDuration=function(){return this._duration
};e.getCurrentTime=function(){return this._currentTime};e.setCurrentTime=function(a,b){var c=this._getEligibleClips();
if(!c||!c.length){return}c.forEach(function(j){var d=this._getLocalTime(j,a);j.setCurrentTime(d,b)
}.bind(this))};e.getPlaybackRate=function(){return this._playbackRate};e.setPlaybackRate=function(a){if(isNaN(a)){return
}this._playbackRate=a};return h});AC.define("iphone/shared/story/Story",["require","animationSequencer/Player/BasicPlayer","animationSequencer/clip/CompositeClip","defer/Deferred"],function(j){var g=j("animationSequencer/Player/BasicPlayer");
var h=j("animationSequencer/clip/CompositeClip");var f=j("defer/Deferred");function i(b,a){g.call(this,new h());
this._scenes;this._currentSceneIndex=0;this.setScenes(b);this._locked=false;this._stageTracker=a
}i.prototype=new g();i.prototype.isLocked=function(){return this._locked};i.prototype.setScenes=function(b){var a=[];
this._scenes=b;this._scenes.forEach(function(c,d){a.push(c.getClip())}.bind(this));
this.__setClip(new h(a))};i.prototype.__getLastSceneIndex=function(){var a;var b;
for(var c=this._scenes.length-1;c>=0;c-=1){b=this._scenes[c];if(b.getDuration()<=this.getDuration()){a=c;
break}}return a};i.prototype.__setClip=function(a){var b=this.getCurrentTime();
var c=this.getDuration();this._clip=a;if(b>a.getDuration()){this.__afterShow(this.__getLastSceneIndex(),this._currentSlideIndex);
this.setCurrentTime(this._clip.getDuration())}else{if(b===c){this.setCurrentTime(this.getDuration());
this._currentSceneIndex=this._scenes.length-1}else{this.setCurrentTime(b)}}};i.prototype.showFirst=function(a){return this.show(0,a)
};i.prototype.showLast=function(a){var b=this._scenes.length-1;return this.show(b,a)
};i.prototype.getSceneById=function(b){var a;this._scenes.forEach(function(c){if(c.get("id")===b){a=c
}});return a};i.prototype.getSceneIndex=function(a){return this._scenes.indexOf(a)
};i.prototype.showPrevious=function(b){var a=this._currentSceneIndex-1;var c=this._scenes[a];
if(c){return this.show(a,b)}};i.prototype.showNext=function(b){var a=this._currentSceneIndex+1;
var c=this._scenes[a];if(c){return this.show(a,b)}};i.prototype.__triggerWillShow=function(a,c,d){var b;
this._scenes.forEach(function(e,n){var m=e.getDuration();if((a===1&&c<m&&d>=m)||(a===-1&&c>m&&d<=m)){b={target:this,incomingIndex:n,outgoingIndex:(a<0)?n+1:n-1}
}}.bind(this));if(b){this.trigger("willShow",b)}return b};i.prototype.jumpTo=function(b){var e=new f();
var a=false;var c;function d(l){if(a===true){AC.Element.removeVendorPrefixEventListener(document.body,"transitionEnd",c);
a=false;e.resolve()}if(l.target===document.body&&l.propertyName==="opacity"){a=true;
this.setCurrentTime(b);AC.Element.removeClassName(document.body,"player-jump")}}c=d.bind(this);
AC.Element.addVendorPrefixEventListener(document.body,"transitionEnd",c);AC.Element.addClassName(document.body,"player-jump");
return e.promise()};i.prototype.show=function(d,r){if(d===this._currentSceneIndex||d>this.__getLastSceneIndex()){return
}var b=d-this._currentSceneIndex;var o=this._scenes[d];var c=(b>0)?this._currentSceneIndex+b:d+Math.abs(b);
var q=this._scenes[c];var p=(d===0)?0:o.getDuration();var a;var e=AC.Element.select("#progress-nav");
if(p>this.getDuration()||this._locked===true){return}this._locked=true;this.trigger("willShow",{target:this,incomingIndex:d,outgoingIndex:this._currentSceneIndex});
if(Math.abs(b)<=1){if(b<0){q.setPlaybackRate(-1)}else{q.setPlaybackRate(1)}a=q.play().then(function(k){this.setCurrentTime(k)
}.bind(this))}else{a=this.jumpTo(p).then(function(){if(p===0){AC.Element.setStyle(e,{opacity:1})
}}.bind(this))}if(r){this._stageTracker.trackInteraction(r,d)}return a.then(this.__afterShow.bind(this,d,this._currentSceneIndex))
};i.prototype.__afterShow=function(b,a){this._locked=false;this._currentSceneIndex=b;
this.trigger("didShow",{target:this,incomingIndex:b,outgoingIndex:a})};i.prototype.playToScene=function(b){var a=this._scenes.indexOf(b);
var c=b.getDuration();this._locked=true;this.trigger("willShow",{target:this,incomingIndex:a,outgoingIndex:this._currentSceneIndex});
if(a===0){c=0}this._stageTracker.trackInteraction("touch",a);return this.playTo(c).then(this.__afterShow.bind(this,a,this._currentSceneIndex))
};i.prototype.playToNext=function(){var a=this._currentSceneIndex+1;var b=this._scenes[a];
if(b){return this.playToScene(b)}};i.prototype.playToPrevious=function(){var b=this._currentSceneIndex-1;
var c=this._scenes[b];var a=new f();if(c){return this.playToScene(c)}else{a.resolve();
return a.promise()}};i.prototype.playTo=function(b){var l=b;var a=new f();var d=this.getCurrentTime();
var c=(b<this.getCurrentTime())?-1:1;if(b>this.getDuration()){l=this.getDuration()
}else{if(b<0){l=0}}var e=function(k){if((c<0&&(k.time<=l||k.time===0))||(c>0&&k.time>=l)){this.pause();
this.off("timeupdate",e);this.setCurrentTime(l);a.resolve()}}.bind(this);this.on("timeupdate",e);
if((c===-1&&this.getPlaybackRate()>0)||(c===1&&this.getPlaybackRate()<0)){this.setPlaybackRate(this.getPlaybackRate()*-1)
}this.play();return a.promise()};return i});AC.define("iphone/shared/element/selectAncestor",["require","iphone/shared/element/eachAncestor"],function(d){var c=d("iphone/shared/element/eachAncestor");
return function(b,a){b=AC.Element.getElementById(b);var f=null;if(b!==null&&a===undefined){return b.parentNode
}c(b,function(e){if(AC.Element.matchesSelector(e,a)){f=e;return false}});return f
}});AC.define("iphone/shared/scene/controller/trigger/Trigger",["require","iphone/shared/element/selectAncestor"],function(e){var d=e("iphone/shared/element/selectAncestor");
function f(a,b){this._player=a;this._scenes=b}f.prototype={onClick:function(k){var l,c,a,j;
var b="bubble";if(AC.Element.hasClassName(AC.Event.target(k),"progress-nav-trigger")){c=AC.Event.target(k).getAttribute("data-scene")
}else{j=d(AC.Event.target(k),".progress-nav-trigger");if(j){c=j.getAttribute("data-scene")
}}if(c){a=this._player.getSceneById(c);this._player.show(this._player.getSceneIndex(a),b);
AC.Event.stop(k)}}};return f});AC.define("iphone/shared/scene/controller/trigger/builder",["require","iphone/shared/scene/controller/trigger/Trigger"],function(d){var c=d("iphone/shared/scene/controller/trigger/Trigger");
return function(b,i,j){var a=new c(b,j);var h=AC.Element.select("#scroll-to-continue");
AC.Element.addEventListener(i,"click",a.onClick.bind(a));if(h){AC.Element.addEventListener(h,"click",a.onClick.bind(a))
}return a}});AC.define("iphone/shared/scene/controller/mouseWheel/MouseWheel",["require"],function(c){function d(b,a,f){this._player=b;
this._locked=false;this._scrollLockDuration=a;this._horizontalThreshold=f||0}d.prototype={onMouseWheel:function(f){var b="scroll";
var a=f.wheelDeltaY||-1*f.deltaY;if(a>this._horizontalThreshold||a<-this._horizontalThreshold){AC.Event.stop(f)
}if(this._locked===true||(a<=this._horizontalThreshold&&a>=-this._horizontalThreshold)){return
}this._locked=true;if(a>this._horizontalThreshold){this._player.showPrevious(b)
}else{if(a<-this._horizontalThreshold){this._player.showNext(b)}}window.setTimeout(function(){this._locked=false
}.bind(this),this._scrollLockDuration)}};return d});AC.define("iphone/shared/scene/controller/mouseWheel/builder",["require","iphone/shared/scene/controller/mouseWheel/MouseWheel"],function(d){var e=d("iphone/shared/scene/controller/mouseWheel/MouseWheel");
var f="onwheel" in document.createElement("div")?"wheel":document.onmousewheel!==undefined?"mousewheel":"DOMMouseScroll";
return function(b){var a=new e(b,1400);AC.Element.addEventListener(document,f,a.onMouseWheel.bind(a));
return a}});AC.define("iphone/shared/scene/controller/keyboard/Keyboard",["require"],function(c){function d(a){this._player=a
}d.prototype={onKeyDown:function(b){var a="keyboard";if(document.activeElement.tagName.toLowerCase()==="input"){return
}if(b.keyCode===35||(b.metaKey===true&&b.keyCode===40)){AC.Event.stop(b);this._player.showLast(a)
}else{if(b.keyCode===36||(b.metaKey===true&&b.keyCode===38)){AC.Event.stop(b);this._player.showFirst(a)
}else{if(b.keyCode===40||b.keyCode===34||(b.keyCode===32&&!b.shiftKey)){AC.Event.stop(b);
this._player.showNext(a)}else{if(b.keyCode===38||b.keyCode===33||(b.keyCode===32&&b.shiftKey)){AC.Event.stop(b);
this._player.showPrevious(a)}}}}}};return d});AC.define("iphone/shared/scene/controller/keyboard/builder",["require","iphone/shared/scene/controller/keyboard/Keyboard"],function(c){var d=c("iphone/shared/scene/controller/keyboard/Keyboard");
return function(b){var a=new d(b);AC.Element.addEventListener(document,"keydown",a.onKeyDown.bind(a));
return a}});AC.define("iphone/shared/scene/controller/touch/Touch",["require"],function(d){function c(a){this._player=a;
this._startY=null;this._windowHeight=(document.documentElement.clientHeight||window.innerHeight||document.documentElement.offsetHeight);
this._playerStartingTime=null;this._currentTime=0}c.prototype={onTouchStart:function(a){if(this._player.isLocked()||this._startY!==null){return
}this._startY=a.touches[0].screenY;this._playerStartingTime=this._player.getCurrentTime();
this._startingDirection=null;this._reverseEase=false},onTouchMove:function(l){var a;
var k;var b;var j=this._player._clip;var i=l.direction.y;if(this._startY===null||this._playerStartingTime===null){return
}a=this._startY-l.touches[0].screenY;k=a/this._windowHeight;this._currentTime=b=this._playerStartingTime+k;
if(b<this._playerStartingTime){this._player._clip.setEasingDirection(-1)}else{this._player._clip.setEasingDirection(0)
}this._player.setCurrentTime(b)},onTouchEnd:function(f){if(this._startY===null||this._playerStartingTime===null){return
}var b=f.difference.current.y/this._windowHeight;var a=this._player.getPlaybackRate();
this._player.setCurrentTime(this._currentTime);if(b>0.4||f.speed>=7){this._player.setPlaybackRate(1.8);
if(f.direction.y==="down"){this._player.playToNext().then(this._player.setPlaybackRate.bind(this._player,a))
}else{if(f.direction.y==="up"){this._player.playToPrevious().then(this._player.setPlaybackRate.bind(this._player,a))
}}}else{this._player._locked=true;this._player.playTo(this._playerStartingTime).then(function(){this._player._locked=false
}.bind(this))}this._startY=null;this._playerStartingTime=null;this._startingDirection=null
}};return c});AC.define("iphone/shared/scene/controller/touch/builder",["require","iphone/shared/scene/controller/touch/Touch"],function(e){var g=e("iphone/shared/scene/controller/touch/Touch");
var f=function(a){if(a.touches&&a.touches.length<=1&&a.target.tagName.toLowerCase()!=="a"){AC.Event.stop(a)
}};function h(a){var b=new g(a);AC.Element.addEventListener(document,"touchmove",f);
document.body.trackTouches(b.onTouchStart.bind(b),b.onTouchMove.bind(b),b.onTouchEnd.bind(b),{stopEvent:false})
}return function(a){var b=document.getElementsByTagName("head")[0];var c=document.createElement("script");
c.type="text/javascript";c.setAttribute("src","http://images.apple.com/global/scripts/pagingview.js");
b.appendChild(c);document.observe("ac:trackTouches:load",h.bind(undefined,a))}});
AC.define("iphone/shared/scene/observer/nav/Nav",["require"],function(d){function c(b,f,a){this._player=b;
this._element=f;this._navButtons=AC.Element.selectAll("a",this._element);this._footerIndex=a;
this._productNav=AC.Element.select("#product-nav-slide")}c.prototype={toggleNav:function(b){var a=(AC.Environment.Feature.isDesktop())?-1:0;
if(a<b.incomingIndex&&b.incomingIndex<this._footerIndex){AC.Element.addClassName(this._element,"on")
}else{AC.Element.removeClassName(this._element,"on")}},onWillShow:function(a){if(a.incomingIndex!==0){AC.Element.addClassName(this._productNav,"active")
}else{AC.Element.removeClassName(this._productNav,"active")}if(this._navButtons[a.incomingIndex]){AC.Element.addClassName(this._navButtons[a.incomingIndex],"active")
}AC.Element.removeClassName(this._navButtons[a.outgoingIndex],"active");this.toggleNav(a)
}};return c});AC.define("iphone/shared/scene/observer/nav/builder",["require","iphone/shared/scene/observer/nav/Nav"],function(d){var c=d("iphone/shared/scene/observer/nav/Nav");
return function(b,g,h){var a=new c(b,g,h);a.onWillShow({incomingIndex:0});b.on("willShow",a.onWillShow.bind(a));
return a}});AC.define("iphone/shared/scene/StageTracker",["require"],function(e){var d=AC.Tracking;
var f=Class.create({__defaultOptions:{decimals:1},initialize:function(c){var a;
this.scenes=[];this.trackClickQueue=[];if(typeof c!=="object"){c={}}if(typeof this.__defaultOptions!=="object"){this.__defaultOptions={}
}this._options=AC.Object.extend(AC.Object.clone(this.__defaultOptions),c);this.__dateSceneIn=new Date().getTime();
a=AC.Element.selectAll(".slide");for(var b=0;b<a.length;b++){this.scenes.push({element:a[b],shouldTrack:true})
}this.currentSceneIndex=0},findSceneId:function(a){return this.scenes[a].element.id
},timeInScene:function(){var c=new Date().getTime();var b=Math.pow(10,this._options.decimals);
var a=Math.round((c-this.__dateSceneIn)/(1000/b))/b;this.__dateSceneIn=c;return a
},trackInteraction:function(b,c){if(c===this.currentSceneIndex){return 0}if(c>this.scenes.length-1){if(this.currentSceneIndex<this.scenes.length-1){c=this.scenes.length-1
}else{return 0}}var h=this.findSceneId(this.currentSceneIndex);var a={prop1:b,prop34:AC.Tracking.pageName()+" - home - "+h+" - "+s.prop5,prop35:this.timeInScene()};
if(this.scenes[this.currentSceneIndex].shouldTrack){this.trackClickQueue.push(a);
this.scenes[this.currentSceneIndex].shouldTrack=false}if(c){this.currentSceneIndex=c
}},fireTrackClickQueue:function(){while(this.trackClickQueue.length>0){var a=this.trackClickQueue.shift();
d.trackClick(a,this,"o",a.prop34)}}});return f});AC.define("iphone/shared/story/builder",["require","iphone/shared/story/Story","iphone/shared/scene/controller/trigger/builder","iphone/shared/scene/controller/mouseWheel/builder","iphone/shared/scene/controller/keyboard/builder","iphone/shared/scene/controller/touch/builder","iphone/shared/scene/observer/nav/builder","iphone/shared/scene/StageTracker"],function(p){var k=p("iphone/shared/story/Story");
var l=p("iphone/shared/scene/controller/trigger/builder");var n=p("iphone/shared/scene/controller/mouseWheel/builder");
var i=p("iphone/shared/scene/controller/keyboard/builder");var j=p("iphone/shared/scene/controller/touch/builder");
var o=p("iphone/shared/scene/observer/nav/builder");var m=p("iphone/shared/scene/StageTracker");
return function(e,d,f){var a=new m();var c=window.story=new k(e,a);f=f||4;var b;
c.on("willShow",function(g){b=c.getPlaybackRate();if(g.incomingIndex>f){c.setPlaybackRate(b*2)
}});c.on("didShow",function(g){if(c.getPlaybackRate()!==b){c.setPlaybackRate(b)
}a.fireTrackClickQueue()});n(c);i(c);l(c,d,e);if("ontouchstart" in window){j(c)
}o(c,d,f);AC.Element.setStyle(AC.Element.select("#fluidfooter"),{height:AC.Element.select("#fluidfooter").offsetHeight+"px"});
return c}});AC.define("animationSequencer/player/BasicPlayer",["require","eventEmitter/EventEmitter","animationSequencer/Clock"],function(f){var i=f("eventEmitter/EventEmitter");
var h=f("animationSequencer/Clock");function g(a,b){this.options=b||{};this._clip=a;
this._clock=this.options.clock||new h();this._paused=true;window.setTimeout(function(){this.trigger("canplay")
}.bind(this),0)}var j=g.prototype=new i();j.addEventListener=j.on;j.removeEventListener=j.off;
j.play=function(){this._paused=false;this._clock.play();this._update();this.trigger("play")
};j.pause=function(){this.setPaused(true);this._clock.pause();this.trigger("pause")
};j._updateCurrentTime=function(a){this._clock.setCurrentTime(a);this._lastTime=this._clip.setCurrentTime(a)
};j._update=function(){var c=this._clock.getCurrentTime();var b=this.getDuration();
var d=this._clock.getPlaybackRate();var e=d>0;var a=e&&c>=b;var l=!e&&c<=0;if(a||l){c=(a)?b:0;
this.pause();this._updateCurrentTime(c)}this.trigger("timeupdate",{previous:this._lastTime,time:c});
if(a){this.trigger("ended")}if(l){this.trigger("returned")}if(!this.isPaused()){this._updateCurrentTime(c);
window.requestAnimationFrame(this._update.bind(this))}};j._isValidTime=function(a){return(0<=a)&&(a<=this.getDuration())
};j.isPaused=function(){return this._paused};j.setPaused=function(a){this._paused=!!a
};j.getCurrentTime=function(){return this._clock.getCurrentTime()};j.setCurrentTime=function(a){if(this._isValidTime(a)){this.trigger("seeking",{time:a});
this._updateCurrentTime(a);this.trigger("seeked",{time:a})}};j.getPlaybackRate=function(){return this._clock.getPlaybackRate()
};j.setPlaybackRate=function(a){this._clock.setPlaybackRate(a);this.trigger("ratechange",{rate:a})
};j.getDuration=function(){return this._clip.getDuration()};return g});AC.define("iphone/shared/scene/Scene",["require","animationSequencer/player/BasicPlayer","animationSequencer/clip/TimedClip","defer/Deferred"],function(i){var j=i("animationSequencer/player/BasicPlayer");
var h=i("animationSequencer/clip/TimedClip");var f=i("defer/Deferred");function g(a,b){this.options=b||{};
this._clip=a;this._cssPlayer=b.cssPlayer;this._jsPlayer=b.jsPlayer}g.prototype={__shouldUseJSPlayer:function(){return("ontouchstart" in window||!AC.Environment.Feature.cssPropertyAvailable("transform"))
},__getPlayer:function(){if(this.__shouldUseJSPlayer()){return this._jsPlayer}else{return this._cssPlayer
}},get:function(a){return this.options[a]},getDuration:function(){return this._clip.getDuration()
},play:function(){return this.__getPlayer().play().then(function(){var a;if(this.getPlaybackRate()>0){a=this.getDuration()
}else{a=this.get("startTime")}return""+a}.bind(this))},getPlaybackRate:function(){return this.__getPlayer().getPlaybackRate()
},setPlaybackRate:function(a){this.__getPlayer().setPlaybackRate(a)},getClip:function(){return this._clip
}};return g});
/*! MIT License
 *
 * KeySpline - use bezier curve for transition easing function
 * Copyright (c) 2012 Gaetan Renaudeau <renaudeau.gaetan@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 */
AC.define("animationSequencer/vendor/KeySpline",[],function(){function b(a,n,l,p){this.get=function(c){if(a===n&&l===p){return c
}return t(o(c),n,p)};function q(d,c){return 1-3*c+3*d}function r(d,c){return 3*c-6*d
}function u(c){return 3*c}function t(e,d,c){return((q(d,c)*e+r(d,c))*e+u(d))*e}function m(e,d,c){return 3*q(d,c)*e*e+2*r(d,c)*e+u(d)
}function o(f){var c=f;for(var g=0;g<4;++g){var e=m(c,a,l);if(e===0){return c}var d=t(c,a,l)-f;
c-=d/e}return c}}return b});AC.define("animationSequencer/vendor/EasingFunctions",[],function(){var O={linear:function T(a,c,b,d){return b*a/d+c
},easeInQuad:function ab(a,c,b,d){return b*(a/=d)*a+c},easeOutQuad:function al(a,c,b,d){return -b*(a/=d)*(a-2)+c
},easeInOutQuad:function M(a,c,b,d){if((a/=d/2)<1){return b/2*a*a+c}return -b/2*((--a)*(a-2)-1)+c
},easeInCubic:function V(a,c,b,d){return b*(a/=d)*a*a+c},easeOutCubic:function ag(a,c,b,d){return b*((a=a/d-1)*a*a+1)+c
},easeInOutCubic:function ah(a,c,b,d){if((a/=d/2)<1){return b/2*a*a*a+c}return b/2*((a-=2)*a*a+2)+c
},easeInQuart:function af(a,c,b,d){return b*(a/=d)*a*a*a+c},easeOutQuart:function L(a,c,b,d){return -b*((a=a/d-1)*a*a*a-1)+c
},easeInOutQuart:function Q(a,c,b,d){if((a/=d/2)<1){return b/2*a*a*a*a+c}return -b/2*((a-=2)*a*a*a-2)+c
},easeInQuint:function ac(a,c,b,d){return b*(a/=d)*a*a*a*a+c},easeOutQuint:function am(a,c,b,d){return b*((a=a/d-1)*a*a*a*a+1)+c
},easeInOutQuint:function N(a,c,b,d){if((a/=d/2)<1){return b/2*a*a*a*a*a+c}return b/2*((a-=2)*a*a*a*a+2)+c
},easeInSine:function Y(a,c,b,d){return -b*Math.cos(a/d*(Math.PI/2))+b+c},easeOutSine:function aj(a,c,b,d){return b*Math.sin(a/d*(Math.PI/2))+c
},easeInOutSine:function I(a,c,b,d){return -b/2*(Math.cos(Math.PI*a/d)-1)+c},easeInExpo:function ak(a,c,b,d){return(a===0)?c:b*Math.pow(2,10*(a/d-1))+c
},easeOutExpo:function U(a,c,b,d){return(a===d)?c+b:b*(-Math.pow(2,-10*a/d)+1)+c
},easeInOutExpo:function Z(a,c,b,d){if(a===0){return c}if(a===d){return c+b}if((a/=d/2)<1){return b/2*Math.pow(2,10*(a-1))+c
}return b/2*(-Math.pow(2,-10*--a)+2)+c},easeInCirc:function X(a,c,b,d){return -b*(Math.sqrt(1-(a/=d)*a)-1)+c
},easeOutCirc:function ai(a,c,b,d){return b*Math.sqrt(1-(a=a/d-1)*a)+c},easeInOutCirc:function H(a,c,b,d){if((a/=d/2)<1){return -b/2*(Math.sqrt(1-a*a)-1)+c
}return b/2*(Math.sqrt(1-(a-=2)*a)+1)+c},easeInElastic:function J(a,f,c,g){var d=1.70158;
var b=0;var e=c;if(a===0){return f}if((a/=g)===1){return f+c}if(!b){b=g*0.3}if(e<Math.abs(c)){e=c;
d=b/4}else{d=b/(2*Math.PI)*Math.asin(c/e)}return -(e*Math.pow(2,10*(a-=1))*Math.sin((a*g-d)*(2*Math.PI)/b))+f
},easeOutElastic:function K(a,f,c,g){var d=1.70158;var b=0;var e=c;if(a===0){return f
}if((a/=g)===1){return f+c}if(!b){b=g*0.3}if(e<Math.abs(c)){e=c;d=b/4}else{d=b/(2*Math.PI)*Math.asin(c/e)
}return e*Math.pow(2,-10*a)*Math.sin((a*g-d)*(2*Math.PI)/b)+c+f},easeInOutElastic:function W(a,f,c,g){var d=1.70158;
var b=0;var e=c;if(a===0){return f}if((a/=g/2)===2){return f+c}if(!b){b=g*(0.3*1.5)
}if(e<Math.abs(c)){e=c;d=b/4}else{d=b/(2*Math.PI)*Math.asin(c/e)}if(a<1){return -0.5*(e*Math.pow(2,10*(a-=1))*Math.sin((a*g-d)*(2*Math.PI)/b))+f
}return e*Math.pow(2,-10*(a-=1))*Math.sin((a*g-d)*(2*Math.PI)/b)*0.5+c+f},easeInBack:function P(b,e,c,a,d){if(d===undefined){d=1.70158
}return c*(b/=a)*b*((d+1)*b-d)+e},easeOutBack:function ad(b,e,c,a,d){if(d===undefined){d=1.70158
}return c*((b=b/a-1)*b*((d+1)*b+d)+1)+e},easeInOutBack:function R(b,e,c,a,d){if(d===undefined){d=1.70158
}if((b/=a/2)<1){return c/2*(b*b*(((d*=(1.525))+1)*b-d))+e}return c/2*((b-=2)*b*(((d*=(1.525))+1)*b+d)+2)+e
},easeInBounce:function S(a,c,b,d){return b-O.easeOutBounce(d-a,0,b,d)+c},easeOutBounce:function ae(a,c,b,d){if((a/=d)<(1/2.75)){return b*(7.5625*a*a)+c
}else{if(a<(2/2.75)){return b*(7.5625*(a-=(1.5/2.75))*a+0.75)+c}else{if(a<(2.5/2.75)){return b*(7.5625*(a-=(2.25/2.75))*a+0.9375)+c
}else{return b*(7.5625*(a-=(2.625/2.75))*a+0.984375)+c}}}},easeInOutBounce:function aa(a,c,b,d){if(a<d/2){return O.easeInBounce(a*2,0,b,d)*0.5+c
}return O.easeOutBounce(a*2-d,0,b,d)*0.5+b*0.5+c}};return O});AC.define("animationSequencer/clip/BaseClip",["require","animationSequencer/vendor/KeySpline","animationSequencer/vendor/EasingFunctions","eventEmitter/EventEmitter"],function(p){var k=p("animationSequencer/vendor/KeySpline");
var m=p("animationSequencer/vendor/EasingFunctions");var i="Easing option must be one of: String, Array[Number:4], or Function. Given: ";
var j="KeySpline easing expected an array of exactly four (4) numbers, given: ";
var n=p("eventEmitter/EventEmitter");function l(a,b){this.options=b||{};this._duration=a;
this._currentTime=0;this._easingFunction=this._createEasing(this.options.easing||l.DEFAULT_EASING)
}l.DEFAULT_EASING="linear";var o=l.prototype=new n();o._createEasing=function(b){var a;
if(typeof b==="string"){a=this._createPredefinedEasing(b)}else{if(Array.isArray(b)){a=this._createBezierEasing(b)
}else{if(typeof b==="function"){a=b}else{throw new TypeError(i+b)}}}return a};o._createBezierEasing=function(d){var b;
var a=d;var c=d.every(function(e){return(typeof e==="number")});if(d.length!==4||!c){throw new TypeError(j+d)
}b=new k(a[0],a[1],a[2],a[3]);return function(e,f,g,h){return b.get(e/h)*g}};o._createPredefinedEasing=function(a){var b=m[a];
var c="";if(!b){c+='Easing function "'+b;c+='" not recognized among the following: ';
c+=Object.keys(m).join(", ");throw new Error(c)}return b};o._getInterpolatedValue=function(d,a,b,c){return this._easingFunction(d,a,b,c)
};o.getDuration=function(){return this._duration};o.getCurrentTime=function(){return this._currentTime
};o.setCurrentTime=function(a){this._currentTime=a};return l});AC.define("animationSequencer/clip/TweenClip",["require","animationSequencer/clip/BaseClip"],function(e){var g=e("animationSequencer/clip/BaseClip");
function f(a,b,c){g.call(this,a,c);this.props=b||[];this._initializePropEasing();
this._lastComputedTime=0;this._easingDirection=1}f.create=function(a){return new f(a.selector,a.duration,a.props)
};f.validate=function(a){return(typeof a.selector==="string")&&Array.isArray(a.props)
};f.DEFAULT_EASING="linear";var h=f.prototype=new g();h._initializePropEasing=function(){this.props.forEach(function(a){a.easing=this._createEasing(a.easing||g.DEFAULT_EASING)
}.bind(this))};h.setEasingDirection=function(a){this._easingDirection=a};h.tween=function(a){shouldReverseEase=(this._easingDirection===-1);
if(this.options.reverseEase!==true){shouldReverseEase=false}var b=this.getDuration(),c={};
if(this.props.length<1){return}this.props.forEach(function(u){var d,o,p;var t=u.units;
var q=u.axis;var r=u.property;if(shouldReverseEase){d=u.easing(this.getDuration()-a,u.to,-(u.to-u.from),b)
}else{d=u.easing(a,u.from,(u.to-u.from),b)}c[r]=d}.bind(this));this.trigger("tween_update",c)
};h.getCurrentTime=function(){return this._currentTime};h.setCurrentTime=function(a){if(a<0){a=0
}if(a>this.getDuration()){a=this.getDuration()}if(a<0||a>this.getDuration()){return
}this._currentTime=a;this.tween(this._currentTime)};return f});AC.define("overview/scene/capable/tween/stage",["require","animationSequencer/clip/TweenClip","animationSequencer/clip/CompositeClip","animationSequencer/clip/TimedClip"],function(e){var f=e("animationSequencer/clip/TweenClip");
var g=e("animationSequencer/clip/CompositeClip");var h=e("animationSequencer/clip/TimedClip");
return function(b){var a=AC.Element.select("#main");return[{duration:1,startDelay:b,fill:"forwards",reverseEase:true,props:[{element:a,property:"y",from:-100,to:-200,easing:"easeOutQuad",units:"%"}]}]
}});AC.define("iphone/shared/scene/clip/footerSync",["require","animationSequencer/clip/TweenClip","animationSequencer/clip/CompositeClip","animationSequencer/clip/TimedClip"],function(e){var f=e("animationSequencer/clip/TweenClip");
var g=e("animationSequencer/clip/CompositeClip");var h=e("animationSequencer/clip/TimedClip");
return function(n,o){var a=AC.Element.select("#fluidfooter");var b=(document.documentElement.clientHeight||window.innerHeight||document.documentElement.offsetHeight);
var p=-(document.getElementById("fluidfooter").offsetTop-b);var c=p/3;var d=c*(o-1);
var m=c*o;return[{duration:1,startDelay:n,fill:"none",reverseEase:true,props:[{element:a,property:"y",from:d,to:m,units:"px",easing:"easeOutQuad"}]}]
}});AC.define("overview/scene/capable/tween/pink",["require","animationSequencer/clip/TweenClip","animationSequencer/clip/TimedClip","animationSequencer/clip/CompositeClip"],function(j){var g=j("animationSequencer/clip/TweenClip");
var h=j("animationSequencer/clip/TimedClip");var i=j("animationSequencer/clip/CompositeClip");
function f(a,b){a.on("tween_update",function(c){AC.Element.setVendorPrefixStyle(b,"transform","translate3d(0, "+c.y+"px, 0)")
})}return function(a){var b=AC.Element.select("#capable .capable-pink");return[{duration:1,startDelay:a,fill:"both",reverseEase:true,props:[{element:b,property:"y",from:300,to:-160,easing:"easeOutQuad",units:"px"}]}]
}});AC.define("overview/scene/capable/tween/blue",["require","animationSequencer/clip/TweenClip","animationSequencer/clip/TimedClip","animationSequencer/clip/CompositeClip"],function(e){var f=e("animationSequencer/clip/TweenClip");
var g=e("animationSequencer/clip/TimedClip");var h=e("animationSequencer/clip/CompositeClip");
return function(a){return[{duration:1,startDelay:a,fill:"both",reverseEase:true,props:[{element:AC.Element.select("#capable .capable-blue"),property:"y",from:500,to:380,units:"px",easing:"easeOutQuad"}]}]
}});AC.define("overview/scene/capable/tween/yellow",["require","animationSequencer/clip/TweenClip","animationSequencer/clip/TimedClip","animationSequencer/clip/CompositeClip"],function(j){var g=j("animationSequencer/clip/TweenClip");
var h=j("animationSequencer/clip/TimedClip");var i=j("animationSequencer/clip/CompositeClip");
function f(a,b){a.on("tween_update",function(c){AC.Element.setVendorPrefixStyle(b,"transform","translate3d(0, "+c.y+"px, 0)")
})}return function(a){var b=AC.Element.select("#capable .capable-yellow");return[{duration:1,startDelay:a,fill:"both",reverseEase:true,props:[{element:b,property:"y",from:500,to:49,easing:"easeOutQuad",units:"px"}]}]
}});AC.define("overview/scene/capable/tween/actor",["require","animationSequencer/clip/TweenClip","animationSequencer/clip/TimedClip","animationSequencer/clip/CompositeClip"],function(e){var f=e("animationSequencer/clip/TweenClip");
var g=e("animationSequencer/clip/TimedClip");var h=e("animationSequencer/clip/CompositeClip");
return function(c){var d=[];var b=1;var a=0.15;return[{duration:0.85,reverseEase:true,startDelay:c+a,relativeDelay:a,fill:"both",props:[{element:AC.Element.select("#capable-ios-actor .slide-content"),property:"y",from:555,to:-106,units:"px",easing:"easeOutQuad"}]}]
}});AC.define("overview/scene/capable/tween/builder",["require","animationSequencer/clip/CompositeClip","overview/scene/capable/tween/stage","iphone/shared/scene/clip/footerSync","overview/scene/capable/tween/pink","overview/scene/capable/tween/blue","overview/scene/capable/tween/yellow","overview/scene/capable/tween/actor"],function(n){var k=n("animationSequencer/clip/CompositeClip");
var m=n("overview/scene/capable/tween/stage");var o=n("iphone/shared/scene/clip/footerSync");
var i=n("overview/scene/capable/tween/pink");var j=n("overview/scene/capable/tween/blue");
var l=n("overview/scene/capable/tween/yellow");var p=n("overview/scene/capable/tween/actor");
return function(b){var a=[];a=a.concat(m(b));a=a.concat(i(b));a=a.concat(j(b));
a=a.concat(l(b));a=a.concat(p(b));a=a.concat(o(b,2));return a}});AC.define("iphone/shared/scene/player/TweenJS",["require","animationSequencer/player/BasicPlayer","defer/Deferred"],function(h){var f=h("animationSequencer/player/BasicPlayer");
var e=h("defer/Deferred");function g(a,b){f.apply(this,[a]);this._startTime=b}g.prototype=new f();
g.prototype.__onTimeUpdate=function(a,d,l){var b=false;var c=this.getDuration();
var k=(this.getPlaybackRate()>0);if((k&&l.time===c)||(!k&&l.time<=d)){b=true}if(b===true){this.pause();
this.off("timeupdate",this.__boundOnTimeUpdate);this.__boundOnTimeUpdate=null;a.resolve(this.getCurrentTime())
}};g.prototype.play=function(){var a=new e();var c=(this.getPlaybackRate()>0);var b=this.getDuration();
var d=c?this._startTime:b;this.__boundOnTimeUpdate=this.__onTimeUpdate.bind(this,a,this._startTime);
this.setCurrentTime(d);this.on("timeupdate",this.__boundOnTimeUpdate);if(c){this._clip.setEasingDirection(1)
}else{this._clip.setEasingDirection(-1)}f.prototype.play.apply(this,arguments);
return a.promise()};return g});AC.define("iphone/shared/scene/player/TransitionCSS",["require","defer/Deferred"],function(d){var e=d("defer/Deferred");
function f(c,b,a){this._renderers=c;this._playbackRate=1}f.prototype={__transitionsDone:function(){},getDuration:function(){var a=0;
this._renderers.forEach(function(b){if(b.getDuration()>a){a=b.getDuration()}});
return a},getCurrentTime:function(){},setPlaybackRate:function(a){this._playbackRate=a
},getPlaybackRate:function(){return this._playbackRate},play:function(){var b=[];
var a=this._playbackRate;this._renderers.forEach(function(c){if(a>0){b.push(c.renderToEnd())
}else{b.push(c.renderToStart())}});return e.all(b)}};return f});AC.define("iphone/shared/scene/renderer/js/Opacity",["require"],function(d){function c(a){this._element=a
}c.prototype={render:function(b){var a=b.opacity;if(b.opacity<0.003){a=0}AC.Element.setStyle(this._element,{opacity:a})
}};return c});AC.define("iphone/shared/scene/renderer/js/TranslateY",["require"],function(d){function c(a,b){this._element=a;
this._units=b||"px"}c.prototype.render=function(a){AC.Element.setVendorPrefixStyle(this._element,"transform","translate3d(0, "+a.y+this._units+", 0)")
};return c});AC.define("iphone/shared/scene/renderer/js/Width",["require"],function(d){function c(a,b){this._element=a;
this._units=b||"px"}c.prototype.render=function(a){AC.Element.setStyle(this._element,{width:a.width+this._units})
};return c});AC.define("iphone/shared/scene/renderer/transition/Opacity",["require","defer/Deferred"],function(d){var e=d("defer/Deferred");
function f(c,b,i,a,j){this._tweenData=i;this._transitionData=a;this._element=c;
this._duration=b;this._relatedTransitions=j}f.prototype.getDuration=function(){return this._duration
};f.prototype.__buildStringFromProperty=function(b,a){var c=b.property+" "+this._duration+"s";
if(this._tweenData.relativeDelay&&a>0){c+=" "+this._tweenData.relativeDelay+"s"
}return c};f.prototype.__getTransitionString=function(a){var b=[];b.push(this.__buildStringFromProperty(this._transitionData,a));
this._relatedTransitions.forEach(function(c){b.push(this.__buildStringFromProperty(c,a))
}.bind(this));return b.join(",")};f.prototype.__render=function(a,b,j){var c=new e();
this.__boundOnTransitionEnd=this.__onTransitionEnd.bind(this,c);AC.Element.setStyle(this._element,{opacity:a});
AC.Element.addVendorPrefixEventListener(this._element,"transitionEnd",this.__boundOnTransitionEnd);
var i=this.__getTransitionString(j);this._element.setVendorPrefixStyle("transition",i);
AC.Element.setStyle(this._element,{opacity:b});return c.promise()};f.prototype.__onTransitionEnd=function(a,b){if(b.target===this._element){AC.Element.removeVendorPrefixEventListener(this._element,"transitionEnd",this.__boundOnTransitionEnd);
this.__boundOnTransitionEnd=null;this._element.setVendorPrefixStyle("transition","");
if(this._tweenData.relativeDelay){this._element.setVendorPrefixStyle("transition-delay","")
}a.resolve()}};f.prototype.renderToStart=function(a){return this.__render(this._transitionData.to,this._transitionData.from,-1)
};f.prototype.renderToEnd=function(a){return this.__render(this._transitionData.from,this._transitionData.to,1)
};return f});AC.define("iphone/shared/scene/renderer/transition/TranslateY",["require","defer/Deferred"],function(d){var e=d("defer/Deferred");
function f(c,b,h,a){this._tweenData=h;this._transitionData=a;this._element=c;this._duration=b;
this._transitionProperty="-webkit-transform";this._lastTranslateValue}f.prototype.__render=function(a,b,j){var c=new e();
this.__boundOnTransitionEnd=this.__onTransitionEnd.bind(this,c);AC.Element.addVendorPrefixEventListener(this._element,"transitionEnd",this.__boundOnTransitionEnd);
var i=this._transitionProperty+" "+this._duration+"s ease";if(this._tweenData.relativeDelay&&j>0){i+=" "+this._tweenData.relativeDelay+"s"
}this._element.setVendorPrefixStyle("transition",i);AC.Element.setVendorPrefixStyle(this._element,"transform","translate3d(0, "+b+this._transitionData.units+", 0)");
return c.promise()};f.prototype.getDuration=function(){return this._duration};f.prototype.__onTransitionEnd=function(a,b){if(b.target===this._element){AC.Element.removeVendorPrefixEventListener(this._element,"transitionEnd",this.__boundOnTransitionEnd);
this.__boundOnTransitionEnd=null;this._element.setVendorPrefixStyle("transition","");
if(this._tweenData.relativeDelay){this._element.setVendorPrefixStyle("transition-delay","")
}a.resolve()}};f.prototype.renderToEnd=function(a){return this.__render(this._transitionData.from,this._transitionData.to,1)
};f.prototype.renderToStart=function(a){return this.__render(this._transitionData.to,this._transitionData.from,-1)
};return f});AC.define("iphone/shared/scene/renderer/transition/Width",["require","defer/Deferred"],function(d){var e=d("defer/Deferred");
function f(c,b,i,a,j){this._tweenData=i;this._transitionData=a;this._element=c;
this._duration=b;this._relatedTransitions=j}f.prototype.getDuration=function(){return this._duration
};f.prototype.__buildStringFromProperty=function(a){var b=a.property+" "+this._duration+"s";
if(this._tweenData.relativeDelay&&direction>0){transitionString+=" "+a.relativeDelay+"s"
}return b};f.prototype.__getTransitionString=function(){var a=[];a.push(this.__buildStringFromProperty(this._transitionData));
this._relatedTransitions.forEach(function(b){a.push(this.__buildStringFromProperty(b))
}.bind(this));return a.join(",")};f.prototype.__render=function(a,b,l,k){var c=new e();
this.__boundOnTransitionEnd=this.__onTransitionEnd.bind(this,c);AC.Element.setStyle(this._element,{width:a+l});
AC.Element.addVendorPrefixEventListener(this._element,"transitionEnd",this.__boundOnTransitionEnd);
var j=this.__getTransitionString();this._element.setVendorPrefixStyle("transition",j);
AC.Element.setStyle(this._element,{width:b+l});return c.promise()};f.prototype.__onTransitionEnd=function(a,b){if(b.target===this._element){AC.Element.removeVendorPrefixEventListener(this._element,"transitionEnd",this.__boundOnTransitionEnd);
this.__boundOnTransitionEnd=null;this._element.setVendorPrefixStyle("transition","");
if(this._tweenData.relativeDelay){this._element.setVendorPrefixStyle("transition-delay","")
}a.resolve()}};f.prototype.renderToStart=function(a){return this.__render(this._transitionData.to,this._transitionData.from,this._transitionData.units,-1)
};f.prototype.renderToEnd=function(a){return this.__render(this._transitionData.from,this._transitionData.to,this._transitionData.units,1)
};return f});AC.define("iphone/shared/clip/createClipFromData",["require","animationSequencer/clip/CompositeClip","animationSequencer/clip/TimedClip","animationSequencer/clip/TweenClip","iphone/shared/scene/renderer/js/Opacity","iphone/shared/scene/renderer/js/TranslateY","iphone/shared/scene/renderer/js/Width","iphone/shared/scene/renderer/transition/Opacity","iphone/shared/scene/renderer/transition/TranslateY","iphone/shared/scene/renderer/transition/Width","iphone/shared/scene/player/TweenJS","iphone/shared/scene/player/TransitionCSS"],function(w){var y=w("animationSequencer/clip/CompositeClip");
var u=w("animationSequencer/clip/TimedClip");var m=w("animationSequencer/clip/TweenClip");
var n=w("iphone/shared/scene/renderer/js/Opacity");var p=w("iphone/shared/scene/renderer/js/TranslateY");
var r=w("iphone/shared/scene/renderer/js/Width");var q=w("iphone/shared/scene/renderer/transition/Opacity");
var t=w("iphone/shared/scene/renderer/transition/TranslateY");var x=w("iphone/shared/scene/renderer/transition/Width");
var o=w("iphone/shared/scene/player/TweenJS");var v=w("iphone/shared/scene/player/TransitionCSS");
return function(a){var c=[];var b=[];a.forEach(function(d){var e=new m(d.duration,d.props,{reverseEase:d.reverseEase});
d.props.forEach(function(f){var g;var h;if(f.property==="y"){g=new p(f.element,f.units)
}else{if(f.property==="opacity"){g=new n(f.element)}else{if(f.property==="width"){g=new r(f.element)
}}}e.on("tween_update",g.render.bind(g));if(f.property==="y"){h=new t(f.element,d.duration,d,f,AC.Array.without(d.props,f))
}else{if(f.property==="opacity"){h=new q(f.element,d.duration,d,f,AC.Array.without(d.props,f))
}else{if(f.property==="width"){h=new x(f.element,d.duration,d,f,AC.Array.without(d.props,f))
}}}b.push(h)});if(d.startDelay||d.fill){e=new u(e,{fill:d.fill,startDelay:d.startDelay||0})
}c.push(e)});return{clips:c,cssRenderers:b}}});AC.define("overview/scene/hero/builder",["require","iphone/shared/scene/Scene","animationSequencer/clip/CompositeClip","overview/scene/capable/tween/builder","iphone/shared/scene/player/TweenJS","iphone/shared/scene/player/TransitionCSS","iphone/shared/clip/createClipFromData"],function(n){var h=n("iphone/shared/scene/Scene");
var j=n("animationSequencer/clip/CompositeClip");var l=n("overview/scene/capable/tween/builder");
var m=n("iphone/shared/scene/player/TweenJS");var k=n("iphone/shared/scene/player/TransitionCSS");
var i=n("iphone/shared/clip/createClipFromData");return function(f){var e=l(f.startTime);
var d=[];var c=i(e);var b=new j(c.clips);f.jsPlayer=new m(b,f.startTime);f.cssPlayer=new k(c.cssRenderers,f.startTime,0);
var a=new h(b,f);return a}});AC.define("overview/scene/color/tween/stage",["require","animationSequencer/clip/TweenClip","animationSequencer/clip/CompositeClip","animationSequencer/clip/TimedClip"],function(e){var f=e("animationSequencer/clip/TweenClip");
var g=e("animationSequencer/clip/CompositeClip");var h=e("animationSequencer/clip/TimedClip");
return function(b){var a=AC.Element.select("#main");return[{duration:1,startDelay:b,fill:"both",reverseEase:true,props:[{element:a,property:"y",from:0,to:-100,units:"%",easing:"easeOutQuad"}]}]
}});AC.define("overview/scene/color/tween/navFadeIn",["require","animationSequencer/clip/TweenClip","animationSequencer/clip/CompositeClip","animationSequencer/clip/TimedClip"],function(j){var g=j("animationSequencer/clip/TweenClip");
var h=j("animationSequencer/clip/CompositeClip");var i=j("animationSequencer/clip/TimedClip");
function f(a,b){a.on("tween_update",function(c){b.style.webkitTransform="translate3d(0, "+c.y+"%, 0)"
})}return function(a){var b=AC.Element.select("#progress-nav");if(AC.Environment.Feature.isDesktop()){return[]
}else{return[{duration:0.3,startDelay:0.7,fill:"backwards",props:[{element:b,property:"opacity",from:0,to:1}]}]
}}});AC.define("overview/scene/color/tween/builder",["require","animationSequencer/clip/CompositeClip","overview/scene/color/tween/stage","iphone/shared/scene/clip/footerSync","overview/scene/color/tween/navFadeIn"],function(f){var h=f("animationSequencer/clip/CompositeClip");
var i=f("overview/scene/color/tween/stage");var g=f("iphone/shared/scene/clip/footerSync");
var j=f("overview/scene/color/tween/navFadeIn");return function(b){var a=[];a=a.concat(i(b));
a=a.concat(j(b));a=a.concat(g(b,3));return a}});AC.define("overview/scene/color/builder",["require","iphone/shared/scene/Scene","animationSequencer/clip/CompositeClip","overview/scene/color/tween/builder","iphone/shared/scene/player/TweenJS","iphone/shared/scene/player/TransitionCSS","iphone/shared/clip/createClipFromData"],function(n){var h=n("iphone/shared/scene/Scene");
var j=n("animationSequencer/clip/CompositeClip");var l=n("overview/scene/color/tween/builder");
var m=n("iphone/shared/scene/player/TweenJS");var k=n("iphone/shared/scene/player/TransitionCSS");
var i=n("iphone/shared/clip/createClipFromData");return function(e){var d=l();var c=i(d);
var b=new j(c.clips);e.jsPlayer=new m(b,e.startTime);e.cssPlayer=new k(c.cssRenderers,e.startTime);
var a=new h(b,e);return a}});AC.define("overview/scene/capable/builder",["require","iphone/shared/scene/Scene","animationSequencer/clip/CompositeClip","overview/scene/capable/tween/builder","iphone/shared/scene/player/TweenJS","iphone/shared/scene/player/TransitionCSS","iphone/shared/clip/createClipFromData"],function(n){var h=n("iphone/shared/scene/Scene");
var j=n("animationSequencer/clip/CompositeClip");var l=n("overview/scene/capable/tween/builder");
var m=n("iphone/shared/scene/player/TweenJS");var k=n("iphone/shared/scene/player/TransitionCSS");
var i=n("iphone/shared/clip/createClipFromData");return function(f){var e=l(f.startTime);
var d=i(e);var c=[];var b=new j(d.clips);f.jsPlayer=new m(b,f.startTime);f.cssPlayer=new k(d.cssRenderers,f.startTime);
var a=new h(b,f);return a}});AC.define("overview/scene/ios/clip/stage",["require","animationSequencer/clip/TweenClip","animationSequencer/clip/CompositeClip","animationSequencer/clip/TimedClip"],function(e){var f=e("animationSequencer/clip/TweenClip");
var g=e("animationSequencer/clip/CompositeClip");var h=e("animationSequencer/clip/TimedClip");
return function(b){var a=AC.Element.select("#main");return[{duration:1,startDelay:b,fill:"forwards",reverseEase:true,props:[{element:a,property:"y",from:-200,to:-300,easing:"easeOutQuad",units:"%"}]}]
}});AC.define("overview/scene/ios/clip/actor",["require","animationSequencer/clip/TweenClip","animationSequencer/clip/TimedClip","animationSequencer/clip/CompositeClip"],function(l){var h=l("animationSequencer/clip/TweenClip");
var i=l("animationSequencer/clip/TimedClip");var j=l("animationSequencer/clip/CompositeClip");
function g(a,b,c){a.on("tween_update",function(d){AC.Element.setVendorPrefixStyle(b,"transform","translate3d(0, "+d.y+"px, 0)")
})}var k={"height-800-to-Infinity":[{property:"y",from:-106,to:-429,easing:"easeOutQuad"}],"height-0-to-620":[{property:"y",from:-106,to:-325,easing:"easeOutQuad"}],"height-620-to-800":[{property:"y",from:-106,to:-346,easing:"easeOutQuad"}],"tablet-portrait":[{property:"y",from:-106,to:-265,easing:"easeOutQuad"}],"handheld-landscape":[{property:"y",from:-106,to:-247,easing:"easeOutQuad"}],"handheld-portrait":[{property:"y",from:-106,to:-396,easing:"easeOutQuad"}]};
return function(c,a){var b=AC.Element.select("#capable-ios-actor .slide-content");
var d=a.getClassName();var e=k[d];e.forEach(function(f){f.element=b;f.units="px"
});return[{duration:0.6,startDelay:c,fill:"forwards",reverseEase:true,props:e}]
}});AC.define("overview/scene/ios/clip/actorContainer",["require","animationSequencer/clip/TweenClip","animationSequencer/clip/TimedClip","animationSequencer/clip/CompositeClip"],function(j){var g=j("animationSequencer/clip/TweenClip");
var h=j("animationSequencer/clip/TimedClip");var i=j("animationSequencer/clip/CompositeClip");
function f(a,b,c){a.on("tween_update",function(d){AC.Element.setVendorPrefixStyle(b,"transform","translate3d(0, "+d.y+c+", 0)")
})}return function(a){var b=AC.Element.select("#capable-ios-actor");return[{duration:1,startDelay:a,fill:"both",reverseEase:true,props:[{element:b,property:"y",from:0,to:100,easing:"easeOutQuad",units:"%"}]}]
}});AC.define("overview/scene/ios/clip/headerFade",["require","animationSequencer/clip/TweenClip","animationSequencer/clip/TimedClip"],function(d){var e=d("animationSequencer/clip/TweenClip");
var f=d("animationSequencer/clip/TimedClip");return function(b){var a=AC.Element.select("#ios .copy-container");
return[{duration:0.3,startDelay:b+0.7,relativeDelay:0.7,fill:"both",reverseEase:true,props:[{element:a,property:"opacity",from:0,to:1,easing:"easeInQuad"}]}]
}});AC.define("overview/scene/ios/clip/builder",["require","animationSequencer/clip/CompositeClip","overview/scene/ios/clip/stage","iphone/shared/scene/clip/footerSync","overview/scene/ios/clip/actor","overview/scene/ios/clip/actorContainer","overview/scene/ios/clip/headerFade"],function(n){var j=n("animationSequencer/clip/CompositeClip");
var k=n("overview/scene/ios/clip/stage");var h=n("iphone/shared/scene/clip/footerSync");
var i=n("overview/scene/ios/clip/actor");var m=n("overview/scene/ios/clip/actorContainer");
var l=n("overview/scene/ios/clip/headerFade");return function(c,a){var b=[];b=b.concat(k(c));
b=b.concat(i(c,a));b=b.concat(m(c));b=b.concat(l(c));b=b.concat(h(c,3));return b
}});AC.define("overview/scene/ios/builder",["require","iphone/shared/scene/Scene","animationSequencer/clip/CompositeClip","overview/scene/ios/clip/builder","iphone/shared/scene/player/TweenJS","iphone/shared/scene/player/TransitionCSS","iphone/shared/clip/createClipFromData"],function(n){var h=n("iphone/shared/scene/Scene");
var j=n("animationSequencer/clip/CompositeClip");var l=n("overview/scene/ios/clip/builder");
var m=n("iphone/shared/scene/player/TweenJS");var k=n("iphone/shared/scene/player/TransitionCSS");
var i=n("iphone/shared/clip/createClipFromData");return function(f,g){var e=l(f.startTime,g);
var d=i(e);var c=[];var b=new j(d.clips);f.jsPlayer=new m(b,f.startTime);f.cssPlayer=new k(d.cssRenderers,f.startTime);
var a=new h(b,f);return a}});AC.define("overview/scene/footer/clip/stage",["require","animationSequencer/clip/TweenClip","animationSequencer/clip/CompositeClip","animationSequencer/clip/TimedClip"],function(j){var g=j("animationSequencer/clip/TweenClip");
var h=j("animationSequencer/clip/CompositeClip");var i=j("animationSequencer/clip/TimedClip");
function f(a,b){a.on("tween_update",function(c){b.style.webkitTransform="translate3d(0, "+c.y+"px, 0)"
})}return function(c){var a=AC.Element.select("#fluidfooter");var b=(document.documentElement.clientHeight||window.innerHeight||document.documentElement.offsetHeight);
var d=new g(1,[{property:"y",from:-from,to:-(from-(b-a.offsetHeight)-90),easing:"easeOutQuad"}],{reverseEase:true});
f(d,a);d=new i(d,{startDelay:c,fill:"both"});return d}});AC.define("overview/scene/footer/clip/mainOut",["require","animationSequencer/clip/TweenClip","animationSequencer/clip/CompositeClip","animationSequencer/clip/TimedClip"],function(k){var g=k("animationSequencer/clip/TweenClip");
var i=k("animationSequencer/clip/CompositeClip");var j=k("animationSequencer/clip/TimedClip");
var l=AC.Element.select("#fluidfooter");var h=l.offsetHeight;return function(f){var a=AC.Element.select("#main");
var b=(document.documentElement.clientHeight||window.innerHeight||document.documentElement.offsetHeight);
var d=b-55;var e=b*3;var c=e+h;if(h>d){c=e+d}return[{duration:1,startDelay:f,fill:"forwards",reverseEase:true,props:[{element:a,property:"y",from:-e,to:-c,units:"px",easing:"easeOutQuad"}]}]
}});AC.define("overview/scene/footer/clip/navFadeOut",["require","animationSequencer/clip/TweenClip","animationSequencer/clip/CompositeClip","animationSequencer/clip/TimedClip"],function(e){var f=e("animationSequencer/clip/TweenClip");
var g=e("animationSequencer/clip/CompositeClip");var h=e("animationSequencer/clip/TimedClip");
return function(a){var b=AC.Element.select("#progress-nav");return[{duration:1,startDelay:a,fill:"forwards",reverseEase:true,props:[{element:b,property:"opacity",from:1,to:0,easing:"easeOutQuad"}]}]
}});AC.define("overview/scene/footer/clip/builder",["require","animationSequencer/clip/CompositeClip","overview/scene/footer/clip/stage","overview/scene/footer/clip/mainOut","overview/scene/footer/clip/navFadeOut"],function(g){var h=g("animationSequencer/clip/CompositeClip");
var j=g("overview/scene/footer/clip/stage");var f=g("overview/scene/footer/clip/mainOut");
var i=g("overview/scene/footer/clip/navFadeOut");return function(b){var a=[];a=a.concat(f(b));
a=a.concat(i(b));return a}});AC.define("iphone/shared/scene/clip/footerScrollPoints",["require"],function(j){function h(){var c=AC.Element.selectAll("#fluidfooter > *");
var a=[];for(var b=0;b<c.length;b++){a.push({section:c[b],height:AC.Element.getBoundingBox(c[b]).height,top:c[b].offsetTop})
}return a}function i(a,b){return[{easing:"easeOutQuad",from:a,to:b,property:"y"}]
}var g=AC.Element.select("#fluidfooter");var f=AC.Element.getBoundingBox(g).height;
return function(d){var C=h();var w=(document.documentElement.clientHeight||window.innerHeight||document.documentElement.offsetHeight);
footerDistanceToBottomOfWindow=AC.Element.select("#fluidfooter").offsetTop-w;var c=0;
var D=[];var A=[];var a=w-55;var B=-(footerDistanceToBottomOfWindow+f);if(f<a){D.push(i(-footerDistanceToBottomOfWindow,B))
}else{var b=C[0];var e=-(footerDistanceToBottomOfWindow+a);var x=b.height;D.push(i(-footerDistanceToBottomOfWindow,e));
for(var y=1,v=C.length;y<v;++y){b=C[y];if(x+b.height>a){var z=e-x;if(z<B){z=B;y=v
}D.push(i(e,z));e-=x;x=0}x+=b.height}}D.forEach(function(l,n){l.forEach(function(o){o.element=g;
o.units="px"});var k=(n===0)?"none":"forwards";var m=[{duration:1,startDelay:d+n,fill:k,reverseEase:true,props:l}];
A=A.concat(m)});return A}});AC.define("iphone/shared/scene/clip/footer",["require","iphone/shared/scene/Scene","animationSequencer/clip/CompositeClip","iphone/shared/clip/createClipFromData","iphone/shared/scene/clip/footerScrollPoints","iphone/shared/scene/player/TweenJS","iphone/shared/scene/player/TransitionCSS"],function(n){var h=n("iphone/shared/scene/Scene");
var j=n("animationSequencer/clip/CompositeClip");var i=n("iphone/shared/clip/createClipFromData");
var k=n("iphone/shared/scene/clip/footerScrollPoints");var m=n("iphone/shared/scene/player/TweenJS");
var l=n("iphone/shared/scene/player/TransitionCSS");return function(c,A,d,B){var w=c.startTime;
var f=c.id;var e=c.element;var C=k(c.startTime,A);var x=[];var a=[];var y=i(B);
var g=new j(y.clips);c.jsPlayer=new m(g,c.startTime);c.cssPlayer=new l(y.cssRenderers,c.startTime);
var z=[];var b=d;C.forEach(function(G,q){var t;var v=[G];var F=[];if(q!==0){var r=AC.Object.clone(G);
r.props=G.props.slice(0);r.props[0]=AC.Object.clone(G.props[0]);r.props[0].element=AC.Element.select("#main");
r.props[0].from=-b;var p=(b+(G.props[0].from-G.props[0].to));r.props[0].to=-p;b=p;
v.push(r)}t=i(v);var u;if(q===0){t.clips.push(g);F=F.concat(y.cssRenderers)}F=F.concat(t.cssRenderers);
var o=new j(t.clips);u=new h(new j(t.clips),{id:f+"-"+q,jsPlayer:new m(o,w+q),cssPlayer:new l(F,w+q),element:e,duration:1,startTime:w+q});
z.push(u)});return z}});AC.define("overview/scene/footer/builder",["require","overview/scene/footer/clip/builder","iphone/shared/scene/clip/footer"],function(e){var d=e("overview/scene/footer/clip/builder");
var f=e("iphone/shared/scene/clip/footer");return function(o,a){var r=o.startTime;
var q=o.id;var p=o.element;var n=(document.documentElement.clientHeight||window.innerHeight||document.documentElement.offsetHeight);
var b=(n*3+(n-55));var c=d(o.startTime,a);var m=f(o,a,b,c);return m}});AC.define("overview/scene/builder",["require","overview/scene/hero/builder","overview/scene/color/builder","overview/scene/capable/builder","overview/scene/ios/builder","overview/scene/footer/builder"],function(k){var g=k("overview/scene/hero/builder");
var l=k("overview/scene/color/builder");var i=k("overview/scene/capable/builder");
var j=k("overview/scene/ios/builder");var h=k("overview/scene/footer/builder");
return function(e,c){var a=(document.documentElement.clientHeight||window.innerHeight||document.documentElement.offsetHeight);
var f=(document.getElementById("fluidfooter").offsetTop-a);var b=c.getControllerByElement(AC.Element.select("#color"));
var d=h(e[4],f);var n=[g(e[0]),l(e[1]),i(e[2]),j(e[3],b)];n=n.concat(d);return n
}});AC.define("overview/story/tableOfContents",["require"],function(b){return function(){return[{id:"hero",element:AC.Element.select("#hero"),startTime:0,duration:0},{id:"color",element:AC.Element.select("#color"),startTime:0,duration:1},{id:"capable",element:AC.Element.select("#capable"),startTime:1,duration:1.1},{id:"ios",element:AC.Element.select("#ios"),startTime:2.1,duration:1},{id:"footer",element:AC.Element.select("#fluidfooter"),duration:1,startTime:3.1}]
}});AC.define("overview/scene/observer/color/Color",["require"],function(d){function c(b,f,a){this._player=b;
this._element=f;this._casesGallery=a;this._defaultKeyboardNavValue=this._casesGallery.options.alwaysUseKeyboardNav
}c.prototype={onWillShow:function(a){if(a.incomingIndex===1){AC.Element.addClassName(this._element,"active");
this._casesGallery.options.alwaysUseKeyboardNav=true}else{this._casesGallery.options.alwaysUseKeyboardNav=this._defaultKeyboardNavValue
}},onDidShow:function(a){if(a.incomingIndex===0){AC.Element.removeClassName(this._element,"active")
}}};return c});AC.define("overview/scene/observer/color/builder",["require","overview/scene/observer/color/Color"],function(d){var c=d("overview/scene/observer/color/Color");
return function(b,g,a){var h=new c(b,g,a);b.on("willShow",h.onWillShow.bind(h));
b.on("didShow",h.onDidShow.bind(h))}});AC.define("overview/bootstrap",["require","iphone/shared/responsive/builder","iphone/shared/responsive/ControllerSet","iphone/shared/responsive/windowResizeTimeout","iphone/shared/scene/observer/nav/Focus","iphone/shared/ac_autogallery/Types/cases","iphone/shared/story/builder","overview/scene/builder","overview/story/tableOfContents","overview/scene/observer/color/builder"],function(B){var z=B("iphone/shared/responsive/builder");
var I=B("iphone/shared/responsive/ControllerSet");var w=B("iphone/shared/responsive/windowResizeTimeout");
var v=B("iphone/shared/scene/observer/nav/Focus");var J=B("iphone/shared/ac_autogallery/Types/cases");
var D=B("iphone/shared/story/builder");var F=B("overview/scene/builder");var K=B("overview/story/tableOfContents");
var A=B("overview/scene/observer/color/builder");var t;var x=K();var u=AC.Element.select("#progress-nav");
var E=AC.Element.select("#color");var H=z(document.body);var C=new I(H);function G(){var b=F(x,C);
t=window.story=D(b,u,4);var a=new v(t,x)}function y(){var a=F(x,C);t.setScenes(a);
window.scrollTo(0,0)}AC.onDOMReady(function(){if(!document.all){var a,b;for(a=0,b=H.length;
a<b;a+=1){H[a].addListeners()}G();w(y);A(t,E,AC.AutoGallery.galleries["gallery-cases"])
}else{AC.Element.addClassName(document.getElementsByTagName("html")[0],"scroll");
AC.Element.addClassName(document.body,"scroll");var a,b,c=parseInt(AC.Element.getStyle(x[0].element,"height"),10)||999999;
for(a=0,b=H.length;a<b;a+=1){H[a].respond({width:999999,height:c})}}})});