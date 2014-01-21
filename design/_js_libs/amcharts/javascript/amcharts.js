var inheriting={};var AmCharts={};
AmCharts.Class=function(c){var b=function(){if(arguments[0]===inheriting){return
}this.events={};this.construct.apply(this,arguments)
};if(c.inherits){b.prototype=new c.inherits(inheriting);
b.base=c.inherits.prototype;
delete c.inherits}else{b.prototype.createEvents=function(){for(var e=0,d=arguments.length;
e<d;e++){this.events[arguments[e]]=[]
}};b.prototype.listenTo=function(f,e,d){f.events[e].push({handler:d,scope:this})
};b.prototype.addListener=function(e,d,f){this.events[e].push({handler:d,scope:f})
};b.prototype.removeListener=function(h,g,e){var f=h.events[g];
for(var d=f.length-1;
d>=0;d--){if(f[d].handler===e){f.splice(d,1)
}}};b.prototype.fire=function(j,k){var e=this.events[j];
for(var f=0,d=e.length;
f<d;f++){var g=e[f];g.handler.call(g.scope,k)
}}}for(var a in c){b.prototype[a]=c[a]
}return b};AmCharts.charts=[];
AmCharts.addChart=function(a){AmCharts.charts.push(a)
};AmCharts.removeChart=function(b){for(var a=AmCharts.charts.length-1;
a>=0;a--){if(AmCharts.charts[a]==b){AmCharts.charts.splice(a,1)
}}};if(document.addEventListener){AmCharts.isNN=true;
AmCharts.isIE=false;AmCharts.ddd=0.5
}if(document.attachEvent){AmCharts.isNN=false;
AmCharts.isIE=true;AmCharts.ddd=0
}AmCharts.IEversion=0;
if(navigator.appVersion.indexOf("MSIE")!=-1){if(document.documentMode){AmCharts.IEversion=document.documentMode
}}if(AmCharts.IEversion>=9){AmCharts.ddd=0.5
}AmCharts.handleResize=function(){for(var a=0;
a<AmCharts.charts.length;
a++){var b=AmCharts.charts[a];
if(b){b.handleResize()
}}};AmCharts.handleMouseUp=function(c){for(var a=0;
a<AmCharts.charts.length;
a++){var b=AmCharts.charts[a];
if(b){b.handleReleaseOutside(c)
}}};AmCharts.handleMouseMove=function(c){for(var a=0;
a<AmCharts.charts.length;
a++){var b=AmCharts.charts[a];
if(b){b.handleMouseMove(c)
}}};if(AmCharts.isNN){document.addEventListener("mousemove",AmCharts.handleMouseMove,true);
window.addEventListener("resize",AmCharts.handleResize,true);
document.addEventListener("mouseup",AmCharts.handleMouseUp,true)
}if(AmCharts.isIE){document.attachEvent("onmousemove",AmCharts.handleMouseMove);
window.attachEvent("onresize",AmCharts.handleResize);
document.attachEvent("onmouseup",AmCharts.handleMouseUp)
}AmCharts.AmChart=AmCharts.Class({construct:function(){AmCharts.addChart(this);
this.createEvents("dataUpdated");
this.width="100%";this.height="100%";
this.dataChanged=true;
this.chartCreated=false;
this.previousHeight=0;
this.previousWidth=0;
this.backgroundColor="#FFFFFF";
this.backgroundAlpha=0;
this.borderAlpha=0;this.borderColor="#000000";
this.color="#000000";
this.fontFamily="Verdana";
this.fontSize=11;this.numberFormatter={precision:-1,decimalSeparator:".",thousandsSeparator:","};
this.percentFormatter={precision:2,decimalSeparator:".",thousandsSeparator:","};
this.labels=[];this.allLabels=[];
this.chartDiv=document.createElement("div");
this.chartDiv.style.overflow="hidden";
this.legendDiv=document.createElement("div");
this.legendDiv.style.overflow="hidden";
this.balloon=new AmCharts.AmBalloon();
this.balloon.chart=this;
this.prefixesOfBigNumbers=[{number:1000,prefix:"k"},{number:1000000,prefix:"M"},{number:1000000000,prefix:"G"},{number:1000000000000,prefix:"T"},{number:1000000000000000,prefix:"P"},{number:1000000000000000000,prefix:"E"},{number:1e+21,prefix:"Z"},{number:1e+24,prefix:"Y"}];
this.prefixesOfSmallNumbers=[{number:1e-24,prefix:"y"},{number:1e-21,prefix:"z"},{number:1e-18,prefix:"a"},{number:1e-15,prefix:"f"},{number:1e-12,prefix:"p"},{number:1e-9,prefix:"n"},{number:0.000001,prefix:"Î¼"},{number:0.001,prefix:"m"}];
try{document.createEvent("TouchEvent");
this.touchEventsEnabled=true
}catch(a){this.touchEventsEnabled=false
}this.panEventsEnabled=false
},drawChart:function(){this.destroy();
this.set=this.container.set();
if(this.backgroundColor!=undefined&&this.backgroundAlpha>0){this.background=AmCharts.rect(this.container,this.realWidth-1,this.realHeight,this.backgroundColor,this.backgroundAlpha,1,this.borderColor,this.borderAlpha);
this.set.push(this.background)
}if(this.backgroundImage){var a=this.backgroundImage;
if(this.path){a=this.path+a
}this.bgImg=this.container.image(a,0,0,this.realWidth,this.realHeight);
this.set.push(this.bgImg)
}},write:function(a){if(!this.listenersAdded){this.addListeners();
this.listenersAdded=true
}var c=this;this.div=document.getElementById(a);
this.div.style.overflow="hidden";
this.measure();if(this.legend){var b=this.legend.position;
switch(b){case"bottom":this.div.appendChild(this.chartDiv);
this.div.appendChild(this.legendDiv);
break;case"top":this.div.appendChild(this.legendDiv);
this.div.appendChild(this.chartDiv);
break;case"absolute":this.legendDiv.style.position="absolute";
this.chartDiv.style.position="absolute";
if(this.legend.left!=undefined){this.legendDiv.style.left=this.legend.left
}if(this.legend.right!=undefined){this.legendDiv.style.right=this.legend.right
}if(this.legend.top!=undefined){this.legendDiv.style.top=this.legend.top
}if(this.legend.bottom!=undefined){this.legendDiv.style.bottom=this.legend.bottom
}this.div.appendChild(this.chartDiv);
this.div.appendChild(this.legendDiv);
break;case"right":this.legendDiv.style.position="relative";
this.chartDiv.style.position="absolute";
this.div.appendChild(this.chartDiv);
this.div.appendChild(this.legendDiv);
break;case"left":this.legendDiv.style.position="relative";
this.chartDiv.style.position="absolute";
this.div.appendChild(this.chartDiv);
this.div.appendChild(this.legendDiv);
break}}else{this.div.appendChild(this.chartDiv)
}this.divIsFixed=AmCharts.findIfFixed(this.chartDiv);
this.container=Raphael(this.chartDiv,this.realWidth,this.realHeight);
this.initChart()},initChart:function(){this.previousHeight=this.realHeight;
this.previousWidth=this.realWidth;
if(this.container){this.destroySets();
this.container.clear()
}this.redrawLabels()},measure:function(){this.divRealWidth=this.div.offsetWidth;
this.divRealHeight=this.div.offsetHeight;
if(this.div.clientHeight){this.divRealWidth=this.div.clientWidth;
this.divRealHeight=this.div.clientHeight
}this.divRealHeight=this.div.clientHeight;
this.realWidth=AmCharts.toCoordinate(this.width,this.divRealWidth);
this.realHeight=AmCharts.toCoordinate(this.height,this.divRealHeight);
if(this.realWidth!=this.previousWidth||this.realHeight!=this.previousHeight){this.chartDiv.style.width=this.realWidth+"px";
this.chartDiv.style.height=this.realHeight+"px";
if(this.container){this.container.setSize(this.realWidth,this.realHeight)
}this.balloon.setBounds(2,2,this.realWidth-2,this.realHeight)
}},destroy:function(){AmCharts.removeSet(this.set);
this.clearTimeOuts()},clearTimeOuts:function(){if(this.timeOuts){for(var a=0;
a<this.timeOuts.length;
a++){clearTimeout(this.timeOuts[a])
}}this.timeOuts=[]},destroySets:function(){this.set=null;
if(this.balloon){this.balloon.set=null
}},clear:function(){if(this.chartScrollbar){this.chartScrollbar.clear();
this.chartScrollbar=null
}if(this.chartCursor){this.chartCursor.clear();
this.chartCursor=null
}this.clearTimeOuts();
this.container.clear();
AmCharts.removeChart(this)
},setMouseCursor:function(a){document.body.style.cursor=a
},bringLabelsToFront:function(){for(var a=this.labels.length-1;
a>=0;a--){this.labels[a].toFront()
}},redrawLabels:function(){this.labels=[];
for(var b=0;b<this.allLabels.length;
b++){var a=this.allLabels[b];
this.drawLabel(a.x,a.y,a.text,a.align,a.size,a.color,a.rotation,a.alpha,a.bold)
}},drawLabel:function(j,g,k,e,m,b,l,a,f){if(this.container){var d=AmCharts.toCoordinate(j,this.realWidth);
var c=AmCharts.toCoordinate(g,this.realHeight);
if(!d){d=0}if(!c){c=0
}if(b==undefined){b=this.color
}if(isNaN(m)){m=this.fontSize
}if(!e){e="start"}if(e=="left"){e="start"
}if(e=="right"){e="end"
}if(e=="center"){e="middle";
if(!l){d=this.realWidth/2-d
}else{c=this.realHeight-c+c/2
}}if(a==undefined){a=1
}if(l==undefined){l=0
}c+=m/2;var h=AmCharts.text(this.container,d,c,k,{fill:b,"fill-opacity":a,"text-anchor":e,"font-family":this.fontFamily,"font-size":m,rotation:l});
if(f){h.attr({"font-weight":"bold"})
}h.toFront();this.labels.push(h)
}},addLabel:function(f,e,h,c,k,b,j,a,d){if(this.container){this.drawLabel(f,e,h,c,k,b,j,a,d)
}var g={x:f,y:e,text:h,align:c,size:k,color:b,alpha:a,rotation:j,bold:d};
this.allLabels.push(g)
},clearLabels:function(){for(var a=this.labels.length-1;
a>=0;a--){this.labels[a].remove()
}this.labels=[]},updateHeight:function(){var a=this.divRealHeight;
if(this.legend){var b=Number(this.legendDiv.style.height.replace("px",""));
var c=this.legend.position;
if(c=="top"||c=="bottom"){a-=b;
if(a<0){a=0}this.chartDiv.style.height=a+"px"
}}return a},updateWidth:function(){var d=this.divRealWidth;
var a=this.divRealHeight;
if(this.legend){var e=Number(this.legendDiv.style.width.replace("px",""));
var b=Number(this.legendDiv.style.height.replace("px",""));
var c=this.legend.position;
if(c=="right"||c=="left"){d-=e;
if(d<0){d=0}this.chartDiv.style.width=d+"px";
if(c=="left"){this.chartDiv.style.left=(AmCharts.findPosX(this.div)+e)+"px"
}else{this.legendDiv.style.left=d+"px"
}this.legendDiv.style.top=(a-b)/2+"px"
}}return d},addListeners:function(){var a=this;
if(this.touchEventsEnabled&&this.panEventsEnabled){this.chartDiv.addEventListener("touchstart",function(b){a.handleTouchMove.call(a,b)
},true);this.chartDiv.addEventListener("touchmove",function(b){a.handleTouchMove.call(a,b)
},true);this.chartDiv.addEventListener("touchstart",function(b){a.handleTouchStart.call(a,b)
});this.chartDiv.addEventListener("touchend",function(b){a.handleTouchEnd.call(a,b)
})}else{if(AmCharts.isNN){this.chartDiv.addEventListener("mousedown",function(b){a.handleMouseDown.call(a,b)
},true);this.chartDiv.addEventListener("mouseover",function(b){a.handleMouseOver.call(a,b)
},true);this.chartDiv.addEventListener("mouseout",function(b){a.handleMouseOut.call(a,b)
},true)}if(AmCharts.isIE){this.chartDiv.attachEvent("onmousedown",function(b){a.handleMouseDown.call(a,b)
});this.chartDiv.attachEvent("onmouseover",function(b){a.handleMouseOver.call(a,b)
});this.chartDiv.attachEvent("onmouseout",function(b){a.handleMouseOut.call(a,b)
})}}},dispatchDataUpdatedEvent:function(){this.fire("dataUpdated",{type:"dataUpdated"})
},drb:function(){var a="moc.strahcma".split("").reverse().join("");
var k=window.location.hostname;
var h=k.split(".");if(h.length>=2){var e=h[h.length-2]+"."+h[h.length-1]
}if(e!=a){a=a+"/?utm_source=swf&utm_medium=demo&utm_campaign=jsDemo";
var b=this;var f=this.container.set();
var d=AmCharts.rect(this.container,145,20,"#FFFFFF",1);
var g=AmCharts.text(this.container,2,2,"moc.strahcma yb trahc".split("").reverse().join(""),{fill:"#000000","font-family":"Verdana","font-size":11,"text-anchor":"start"});
g.translate(5+","+8);
f.push(d);f.push(g);this.set.push(f);
f.click(function(){window.location.href="http://"+a
});for(var c=0;c<f.length;
c++){f[c].attr({cursor:"pointer"})
}}},invalidateSize:function(){this.measure();
if(this.realWidth!=this.previousWidth||this.realHeight!=this.previousHeight){if(this.chartCreated){if(this.legend){this.legend.invalidateSize()
}this.initChart()}}},validateData:function(){if(this.chartCreated){this.dataChanged=true;
this.initChart()}},validateNow:function(){this.initChart()
},showItem:function(a){a.hidden=false;
this.initChart()},hideItem:function(a){a.hidden=true;
this.initChart()},hideBalloon:function(){var a=this;
this.hoverInt=setTimeout(function(){a.hideBalloonReal.call(a)
},100)},hideBalloonReal:function(){if(this.balloon){this.balloon.hide()
}},showBalloon:function(e,c,b,a,f){var d=this;
if(this.balloon.enabled){this.balloon.followCursor(false);
this.balloon.changeColor(c);
if(!b){this.balloon.setPosition(a,f)
}this.balloon.followCursor(b);
if(e){this.balloon.showBalloon(e)
}}},handleTouchMove:function(c){var a;
var f;var d=this.chartDiv;
if(c.touches){var b=c.touches.item(0);
this.mouseX=b.clientX-AmCharts.findPosX(d);
this.mouseY=b.clientY-AmCharts.findPosY(d)
}},handleMouseOver:function(a){this.mouseIsOver=true
},handleMouseOut:function(a){this.mouseIsOver=false
},handleMouseMove:function(b){var d=this.chartDiv;
if(!b){b=window.event
}var a;var c;if(document.attachEvent&&!window.opera){if(AmCharts.IEversion<9){a=b.x;
c=b.y}else{a=b.offsetX;
c=b.offsetY}}if(AmCharts.isNN){if(!isNaN(b.layerX)){a=b.layerX;
c=b.layerY}if(!isNaN(b.offsetX)&&this.divIsFixed){a=b.offsetX;
c=b.offsetY}}if(window.opera){if(this.divIsFixed){a=b.clientX-AmCharts.findPosX(d);
c=b.clientY-AmCharts.findPosY(d)
}else{a=b.pageX-AmCharts.findPosX(d);
c=b.pageY-AmCharts.findPosY(d)
}}this.mouseX=a;this.mouseY=c
},handleTouchStart:function(a){this.handleMouseDown(a)
},handleTouchEnd:function(a){this.handleReleaseOutside(a)
},handleReleaseOutside:function(a){},handleMouseDown:function(a){if(a){if(a.preventDefault){a.preventDefault()
}}},addLegend:function(a){this.legend=a;
this.legend.chart=this;
this.legend.div=this.legendDiv;
this.listenTo(this.legend,"showItem",this.handleLegendEvent);
this.listenTo(this.legend,"hideItem",this.handleLegendEvent);
this.listenTo(this.legend,"clickMarker",this.handleLegendEvent);
this.listenTo(this.legend,"rollOverItem",this.handleLegendEvent);
this.listenTo(this.legend,"rollOutItem",this.handleLegendEvent);
this.listenTo(this.legend,"rollOverMarker",this.handleLegendEvent);
this.listenTo(this.legend,"rollOutMarker",this.handleLegendEvent);
this.listenTo(this.legend,"clickLabel",this.handleLegendEvent)
},removeLegend:function(){this.legend=undefined
},handleResize:function(){if(AmCharts.isPercents(this.width)||AmCharts.isPercents(this.height)){this.invalidateSize()
}}});AmCharts.Slice=AmCharts.Class({construct:function(){}});
AmCharts.SerialDataItem=AmCharts.Class({construct:function(){}});
AmCharts.GraphDataItem=AmCharts.Class({construct:function(){}});
AmCharts.Guide=AmCharts.Class({construct:function(){}});
AmCharts.toBoolean=function(b,a){if(b==undefined){return a
}switch(String(b).toLowerCase()){case"true":case"yes":case"1":return true;
case"false":case"no":case"0":case null:return false;
default:return Boolean(b)
}};AmCharts.formatMilliseconds=function(d,c){if(d.indexOf("fff")!=-1){var b=c.getMilliseconds();
var a=String(b);if(b<10){a="00"+b
}if(b>=10&&b<100){a="0"+b
}d=d.replace(/fff/g,a)
}return d};AmCharts.toNumber=function(a){if(typeof(a)=="number"){return a
}else{return Number(String(a).replace(/[^0-9\-.]+/g,""))
}};AmCharts.toColor=function(c){if(c!=""&&c!=undefined){if(c.indexOf(",")!=-1){var a=c.split(",");
for(var b=0;b<a.length;
b++){var d=a[b].substring(a[b].length-6,a[b].length);
a[b]="#"+d}c=a}else{c=c.substring(c.length-6,c.length);
c="#"+c}}return c},AmCharts.toSvgColor=function(a,d){if(typeof(a)=="object"){if(d==undefined){d=90
}var b=d;for(var c=0;
c<a.length;c++){b+="-"+a[c]
}return b}else{return a
}};AmCharts.toCoordinate=function(c,a,b){var d;
if(c!=undefined){c=c.toString();
if(b){if(b<a){a=b}}d=Number(c);
if(c.indexOf("!")!=-1){d=a-Number(c.substr(1))
}if(c.indexOf("%")!=-1){d=a*Number(c.substr(0,c.length-1))/100
}}return d};AmCharts.fitToBounds=function(c,b,a){if(c<b){c=b
}if(c>a){c=a}return c
};AmCharts.isDefined=function(a){if(a==undefined){return false
}else{return true}};AmCharts.stripNumbers=function(a){return a.replace(/[0-9]+/g,"")
};AmCharts.extractPeriod=function(c){var a=AmCharts.stripNumbers(c);
var b=1;if(a!=c){b=Number(c.slice(0,c.indexOf(a)))
}return{period:a,count:b}
};AmCharts.resetDateToMin=function(a,f,d){var g;
var e;var h;var j;var c;
var k;var b;switch(f){case"YYYY":g=Math.floor(a.getFullYear()/d)*d;
e=0;h=1;j=0;c=0;k=0;b=0;
break;case"MM":g=a.getFullYear();
e=Math.floor((a.getMonth())/d)*d;
h=1;j=0;c=0;k=0;b=0;break;
case"WW":g=a.getFullYear();
e=a.getMonth();var l=a.getDay();
if(l==0){l=7}h=a.getDate()-l+1;
j=0;c=0;k=0;b=0;break;
case"DD":g=a.getFullYear();
e=a.getMonth();h=Math.floor((a.getDate())/d)*d;
j=0;c=0;k=0;b=0;break;
case"hh":g=a.getFullYear();
e=a.getMonth();h=a.getDate();
j=Math.floor(a.getHours()/d)*d;
c=0;k=0;b=0;break;case"mm":g=a.getFullYear();
e=a.getMonth();h=a.getDate();
j=a.getHours();c=Math.floor(a.getMinutes()/d)*d;
k=0;b=0;break;case"ss":g=a.getFullYear();
e=a.getMonth();h=a.getDate();
j=a.getHours();c=a.getMinutes();
k=Math.floor(a.getSeconds()/d)*d;
b=0;break;case"fff":g=a.getFullYear();
e=a.getMonth();h=a.getDate();
j=a.getHours();c=a.getMinutes();
k=a.getSeconds();b=Math.floor(a.getMilliseconds()/d)*d;
break}a=new Date(g,e,h,j,c,k,b);
return a};AmCharts.getPeriodDuration=function(c,a){if(a==undefined){a=1
}var b;switch(c){case"YYYY":b=31622400000;
break;case"MM":b=2678400000;
break;case"WW":b=604800000;
break;case"DD":b=86400000;
break;case"hh":b=3600000;
break;case"mm":b=60000;
break;case"ss":b=1000;
break;case"fff":b=1;break
}return b*a};AmCharts.roundTo=function(b,a){if(a<0){return b
}else{var c=Math.pow(10,a);
return(Math.round(b*c)/c)
}};AmCharts.intervals={s:{nextInterval:"ss",contains:1000},ss:{nextInterval:"mm",contains:60,count:0},mm:{nextInterval:"hh",contains:60,count:1},hh:{nextInterval:"DD",contains:24,count:2},DD:{nextInterval:"",contains:Infinity,count:3}};
AmCharts.getMaxInterval=function(c,a){var b=AmCharts.intervals;
if(c>=b[a].contains){c=Math.round(c/b[a].contains);
a=b[a].nextInterval;return AmCharts.getMaxInterval(c,a)
}else{if(a=="ss"){return b[a].nextInterval
}else{return a}}};AmCharts.formatDuration=function(c,a,k,f,b,g){var e=AmCharts.intervals;
var j=g.decimalSeparator;
if(c>=e[a].contains){var h=c-Math.floor(c/e[a].contains)*e[a].contains;
if(a=="ss"){h=AmCharts.formatNumber(h,g);
if(h.split(j)[0].length==1){h="0"+h
}}if((a=="mm"||a=="hh")&&h<10){h="0"+h
}k=h+""+f[a]+""+k;c=Math.floor(c/e[a].contains);
a=e[a].nextInterval;return(AmCharts.formatDuration(c,a,k,f,b,g))
}else{if(a=="ss"){c=AmCharts.formatNumber(c,g);
if(c.split(j)[0].length==1){c="0"+c
}}if((a=="mm"||a=="hh")&&c<10){c="0"+c
}k=c+""+f[a]+""+k;if(e[b].count>e[a].count){for(var d=e[a].count;
d<e[b].count;d++){a=e[a].nextInterval;
if(a=="ss"||a=="mm"||a=="hh"){k="00"+f[a]+""+k
}else{if(a=="DD"){k="0"+f[a]+""+k
}}}}if(k.charAt(k.length-1)==":"){k=k.substring(0,k.length-1)
}return k}};AmCharts.formatNumber=function(c,j,g,e,m){c=AmCharts.roundTo(c,j.precision);
if(isNaN(g)){g=j.precision
}var n=j.decimalSeparator;
var h=j.thousandsSeparator;
if(c<0){var a="-"}else{var a=""
}c=Math.abs(c);var k=c.toString();
if(k.indexOf("e")==-1){var f=k.split(".");
var l="";var d=f[0].toString();
for(var b=d.length;b>=0;
b=b-3){if(b!=d.length){if(b!=0){l=d.substring(b-3,b)+h+l
}else{l=d.substring(b-3,b)+l
}}else{l=d.substring(b-3,b)
}}if(f[1]!=undefined){l=l+n+f[1]
}if(g!=undefined&&g>0&&l!="0"){l=AmCharts.addZeroes(l,n,g)
}}else{l=k}l=a+l;if(a==""&&e==true&&c!=0){l="+"+l
}if(m==true){l=l+"%"}return(l)
};AmCharts.addZeroes=function(b,c,a){var d=b.split(c);
if(d[1]==undefined&&a>0){d[1]="0"
}if(d[1].length<a){d[1]=d[1]+"0";
return AmCharts.addZeroes(d[0]+c+d[1],c,a)
}else{if(d[1]!=undefined){return d[0]+c+d[1]
}else{return d[0]}}};
AmCharts.scientificToNormal=function(b){var f=b.toString();
var e;var a=f.split("e");
if(a[1].substr(0,1)=="-"){e="0.";
for(var d=0;d<Math.abs(Number(a[1]))-1;
d++){e+="0"}e+=a[0].split(".").join("")
}else{var g=0;var c=a[0].split(".");
if(c[1]){g=c[1].length
}e=a[0].split(".").join("");
for(var d=0;d<Math.abs(Number(a[1]))-g;
d++){e+="0"}}return e
};AmCharts.toScientific=function(b,d){if(b==0){return"0"
}var c=Math.floor(Math.log(Math.abs(b))*Math.LOG10E);
var a=Math.pow(10,c);
mantissa=mantissa.toString().split(".").join(d);
return mantissa.toString()+"e"+c
};AmCharts.generateGradient=function(a,e,c){var d=e;
if(c){for(var b=c.length-1;
b>=0;b--){d+="-"+AmCharts.adjustLuminosity(a,c[b]/100)
}}else{if(typeof(a)=="object"){if(a.length>1){for(var b=0;
b<a.length;b++){d+="-"+a[b]
}}else{d=a[0]}}else{d=a
}}return d};AmCharts.randomColor=function(){function a(){return Math.floor(Math.random()*256).toString(16)
}return"#"+a()+a()+a()
};AmCharts.hitTest=function(g,e,h){var f=false;
var b=g.x;var a=g.x+g.width;
var d=g.y;var c=g.y+g.height;
if(!f){f=AmCharts.isInRectangle(b,d,e)
}if(!f){f=AmCharts.isInRectangle(b,c,e)
}if(!f){f=AmCharts.isInRectangle(a,d,e)
}if(!f){f=AmCharts.isInRectangle(a,c,e)
}if(!f&&h!=true){f=AmCharts.hitTest(e,g,true)
}return f};AmCharts.isInRectangle=function(a,c,b){if(a>=b.x&&a<=b.x+b.width&&c>=b.y&&c<=b.y+b.height){return true
}else{return false}};
AmCharts.isPercents=function(a){if(String(a).indexOf("%")!=-1){return true
}};AmCharts.dayNames=["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
AmCharts.shortDayNames=["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];
AmCharts.monthNames=["January","February","March","April","May","June","July","August","September","October","November","December"];
AmCharts.shortMonthNames=["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
AmCharts.formatDate=function(x,u){var j=x.getFullYear();
var v=String(j).substr(-2,2);
var B=x.getMonth();var g=B+1;
if(B<9){g="0"+g}var z=x.getDate();
var k=z;if(z<10){k="0"+z
}var r=x.getDay();var c="0"+r;
var t=x.getHours();var s=t;
if(s==24){s=0}var A=s;
if(A<10){A="0"+A}u=u.replace(/JJ/g,A);
u=u.replace(/J/g,s);var m=t;
if(m==0){m=24}var e=m;
if(e<10){e="0"+e}u=u.replace(/HH/g,e);
u=u.replace(/H/g,m);var y=t;
if(y>11){y-=12}var D=y;
if(D<10){D="0"+D}u=u.replace(/KK/g,D);
u=u.replace(/K/g,y);var a=t;
if(a>12){a-=12}var n=a;
if(n<10){n="0"+n}u=u.replace(/LL/g,n);
u=u.replace(/L/g,a);var q=x.getMinutes();
var b=q;if(b<10){b="0"+b
}u=u.replace(/NN/g,b);
u=u.replace(/N/g,q);var l=x.getSeconds();
var p=l;if(p<10){p="0"+p
}u=u.replace(/SS/g,p);
u=u.replace(/S/g,l);var h=x.getMilliseconds();
var o=h;if(o<10){o="00"+o
}if(o<100){o="0"+o}var C=h;
if(C<10){C="00"+C}u=u.replace(/QQQ/g,o);
u=u.replace(/QQ/g,C);
u=u.replace(/Q/g,h);if(t<12){u=u.replace(/A/g,"am")
}else{u=u.replace(/A/g,"pm")
}u=u.replace(/YYYY/g,"@IIII@");
u=u.replace(/YY/g,"@II@");
u=u.replace(/MMMM/g,"@XXXX@");
u=u.replace(/MMM/g,"@XXX@");
u=u.replace(/MM/g,"@XX@");
u=u.replace(/M/g,"@X@");
u=u.replace(/DD/g,"@RR@");
u=u.replace(/D/g,"@R@");
u=u.replace(/EEEE/g,"@PPPP@");
u=u.replace(/EEE/g,"@PPP@");
u=u.replace(/EE/g,"@PP@");
u=u.replace(/E/g,"@P@");
u=u.replace(/@IIII@/g,j);
u=u.replace(/@II@/g,v);
u=u.replace(/@XXXX@/g,AmCharts.monthNames[B]);
u=u.replace(/@XXX@/g,AmCharts.shortMonthNames[B]);
u=u.replace(/@XX@/g,g);
u=u.replace(/@X@/g,(B+1));
u=u.replace(/@RR@/g,k);
u=u.replace(/@R@/g,z);
u=u.replace(/@PPPP@/g,AmCharts.dayNames[r]);
u=u.replace(/@PPP@/g,AmCharts.shortDayNames[r]);
u=u.replace(/@PP@/g,c);
u=u.replace(/@P@/g,r);
return u};AmCharts.findPosX=function(a){var b=a.offsetLeft;
while((a=a.offsetParent)){b+=a.offsetLeft;
if(a!=document.body&&a!=document.documentElement){b-=a.scrollLeft
}}return b};AmCharts.findPosY=function(a){var b=a.offsetTop;
while((a=a.offsetParent)){b+=a.offsetTop;
if(a!=document.body&&a!=document.documentElement){b-=a.scrollTop
}}return b};AmCharts.findIfFixed=function(a){while((a=a.offsetParent)){if(a.style.position=="fixed"){return true
}}return false};AmCharts.formatString=function(a,b,d,c){a=a.replace(/<br>/g,"\n");
if(b.value!=undefined){a=a.replace(/\[\[value\]\]/g,AmCharts.formatNumber(b.value,d))
}if(b.open!=undefined){a=a.replace(/\[\[open\]\]/g,AmCharts.formatNumber(b.open,d))
}if(b.close!=undefined){a=a.replace(/\[\[close\]\]/g,AmCharts.formatNumber(b.close,d))
}if(b.low!=undefined){a=a.replace(/\[\[low\]\]/g,AmCharts.formatNumber(b.low,d))
}if(b.high!=undefined){a=a.replace(/\[\[high\]\]/g,AmCharts.formatNumber(b.high,d))
}if(b.percents!=undefined){a=a.replace(/\[\[percents\]\]/g,AmCharts.formatNumber(b.percents,c))
}if(b.title!=undefined){a=a.replace(/\[\[title\]\]/g,b.title)
}else{a=a.replace(/\[\[title\]\]/g,"")
}if(b.category!=undefined){a=a.replace(/\[\[category\]\]/g,b.category)
}else{a=a.replace(/\[\[category\]\]/g,"")
}if(b.graphTitle!=undefined){a=a.replace(/\[\[graphTitle\]\]/g,b.graphTitle)
}else{a=a.replace(/\[\[graphTitle\]\]/g,"")
}if(b.description!=undefined){a=a.replace(/\[\[description\]\]/g,b.description)
}else{a=a.replace(/\[\[description\]\]/g,"")
}return a};AmCharts.addPrefix=function(j,k,e,f){var h=AmCharts.formatNumber(j,f);
var d="";var g;var b;
var a;if(j==0){return"0"
}if(j<0){d="-"}j=Math.abs(j);
if(j>1){for(g=k.length-1;
g>-1;g--){if(j>=k[g].number){b=j/k[g].number;
a=Number(f.precision);
if(a<1){a=1}b=AmCharts.roundTo(b,a);
h=d+""+b+""+k[g].prefix;
break}}}else{for(g=0;
g<e.length;g++){if(j<=e[g].number){b=j/e[g].number;
a=Math.abs(Math.round(Math.log(b)*Math.LOG10E));
b=AmCharts.roundTo(b,a);
h=d+""+b+""+e[g].prefix;
break}}}return h};AmCharts.removeSet=function(e){if(e){for(var a=0;
a<e.length;a++){var d=e[a];
if(d.length>0){AmCharts.removeSet(d)
}var c=d.clip;var b=d.node;
if(b){if(b.clipRect){c=b.clipRect
}if(b.parentNode){d.remove()
}}if(c){if(c.parentNode){c.parentNode.removeChild(c)
}delete c}}}};AmCharts.recommended=function(){var b="js";
var a=document.implementation.hasFeature("http://www.w3.org/TR/SVG11/feature#BasicStructure","1.1");
if(!a){if(swfobject){if(swfobject.hasFlashPlayerVersion("8")){b="flash"
}}}return b};AmCharts.Bezier=AmCharts.Class({construct:function(a,p,m,g,f,o,c,d,b,r){if(typeof(c)=="object"){c=c[0]
}if(typeof(d)=="object"){d=d[0]
}var l="";if(b==1){l="."
}if(b>1){l="-"}var j={stroke:g,fill:c,"fill-opacity":d,"stroke-dasharray":l,opacity:f,"stroke-width":o};
var e="L";var n=p.length;
this.lineArray=["M",p[0],m[0]];
var q=[];for(var h=0;
h<n;h++){q.push({x:p[h],y:m[h]})
}if(q.length>1){var k=this.interpolate(q);
this.drawBeziers(k)}this.lineArray=this.lineArray.concat(r);
this.path=a.path(this.lineArray).attr(j)
},interpolate:function(l){var j=[];
j.push({x:l[0].x,y:l[0].y});
var e=l[1].x-l[0].x;var c=l[1].y-l[0].y;
j.push({x:l[0].x+e/6,y:l[0].y+c/6});
var b=3;var a=6;for(var d=1;
d<l.length-1;d++){var k=l[d-1];
var g=l[d];var f=l[d+1];
e=f.x-g.x;c=f.y-k.y;var h=g.x-k.x;
if(h>e){h=e}j.push({x:g.x-h/b,y:g.y-c/a});
j.push({x:g.x,y:g.y});
j.push({x:g.x+h/b,y:g.y+c/a})
}c=l[l.length-1].y-l[l.length-2].y;
e=l[l.length-1].x-l[l.length-2].x;
j.push({x:l[l.length-1].x-e/b,y:l[l.length-1].y-c/6});
j.push({x:l[l.length-1].x,y:l[l.length-1].y});
return j},drawBeziers:function(b){for(var a=0;
a<(b.length-1)/3;a++){this.drawBezierMidpoint(b[3*a],b[3*a+1],b[3*a+2],b[3*a+3])
}},drawBezierMidpoint:function(c,b,o,l){var f=this.getPointOnSegment(c,b,3/4);
var d=this.getPointOnSegment(l,o,3/4);
var p=(l.x-c.x)/16;var m=(l.y-c.y)/16;
var j=this.getPointOnSegment(c,b,3/8);
var h=this.getPointOnSegment(f,d,3/8);
h.x-=p;h.y-=m;var g=this.getPointOnSegment(d,f,3/8);
g.x+=p;g.y+=m;var e=this.getPointOnSegment(l,o,3/8);
var a=this.getMiddle(j,h);
var n=this.getMiddle(f,d);
var k=this.getMiddle(g,e);
this.lineArray.push("Q",j.x,j.y,a.x,a.y);
this.lineArray.push("Q",h.x,h.y,n.x,n.y);
this.lineArray.push("Q",g.x,g.y,k.x,k.y);
this.lineArray.push("Q",e.x,e.y,l.x,l.y)
},getMiddle:function(c,b){var a={x:(c.x+b.x)/2,y:(c.y+b.y)/2};
return a},getPointOnSegment:function(c,b,d){var a={x:c.x+(b.x-c.x)*d,y:c.y+(b.y-c.y)*d};
return a}});AmCharts.Cuboid=AmCharts.Class({construct:function(b,d,m,o,n,a,f,e,h,g,l,j){this.set=b.set();
this.container=b;this.h=m;
this.w=d;this.dx=o;this.dy=n;
this.colors=a;this.alpha=f;
this.bwidth=e;this.bcolor=h;
this.balpha=g;if(typeof(this.colors)!="object"){var k=this.colors;
this.colors=[k]}if(this.w<0&&l==0){l=180
}if(this.h<0&&l==270){l=90
}this.gradientRotation=l;
if(this.dx==0&&this.dy==0){this.cornerRadius=j
}this.draw()},draw:function(){this.set.remove();
var t=0;var m=Math.abs(this.w);
var s=Math.abs(this.h);
var n=this.dx;var l=this.dy;
if(this.dx>0||l>0){var c=this.colors[this.colors.length-1];
if(this.h>0){c=this.colors[0]
}var d=AmCharts.adjustLuminosity(c,-0.2);
var o=AmCharts.polygon(this.container,[0,n,m+n,m,0],[0,l,l,0,0],[d],this.alpha,0,0,0,this.gradientRotation);
this.set.push(o);var b=AmCharts.line(this.container,[0,n,m+n],[0,l,l],this.bcolor,this.balpha,this.bwidth);
this.set.push(b);var d=AmCharts.adjustLuminosity(this.colors[0],-0.2);
if(s>0&&m>0){var q=AmCharts.rect(this.container,m,s,d,this.alpha,0,0,0,0,this.gradientRotation);
this.set.push(q);q.translate(n+","+(-s+l));
var k=AmCharts.line(this.container,[n,n],[l,-s+l],this.bcolor,this.balpha,this.bwidth);
this.set.push(k);var r=AmCharts.polygon(this.container,[0,0,n,n,0],[0,-s,-s+l,l,0],d,this.alpha,0,0,0,this.gradientRotation);
this.set.push(r);var j=AmCharts.polygon(this.container,[0,0,n,n,0],[0,-s,-s+l,l,0],d,this.alpha,0,0,0,this.gradientRotation);
j.translate(m+","+0);
this.set.push(j);var f=AmCharts.line(this.container,[0,n,n,0],[-s,-s+l,l,0],this.bcolor,this.balpha,this.bwidth);
this.set.push(f);f.translate(m+","+0)
}var e=this.colors[0];
var g=this.alpha[0];if(this.h>0){e=this.colors[this.colors.length-1];
g=this.alpha[this.alpha.length-1]
}var d=AmCharts.adjustLuminosity(e,0.2);
var p=AmCharts.polygon(this.container,[0,n,m+n,m,0],[0,l,l,0,0],[d],this.alpha,0,0,0,this.gradientRotation);
p.translate(0+","+(-s));
this.set.push(p);var a=AmCharts.line(this.container,[0,n,m+n],[0,l,l],this.bcolor,this.balpha,this.bwidth);
a.translate(0+","+(-s));
this.set.push(a)}this.front=AmCharts.rect(this.container,m,s,this.colors,this.alpha,this.bwidth,this.bcolor,this.balpha,this.cornerRadius,this.gradientRotation);
this.front.translate(0+","+(-s));
this.set.push(this.front)
},y:function(a){a=Math.round(a*10)/10;
if(this.h<0){this.set.translate(0+","+a)
}else{this.set.translate(0+","+(a+this.h))
}},x:function(a){a=Math.round(a*10)/10;
if(this.w<0){this.set.translate((a+this.w)+","+0)
}else{this.set.translate(a+","+0)
}},width:function(a){this.w=a;
this.draw()},height:function(a){this.h=a;
this.draw()},getX:function(){return this.front.getBBox().x
},getY:function(){return this.front.getBBox().y
}});AmCharts.AmLegend=AmCharts.Class({construct:function(){this.createEvents("rollOverMarker","rollOverItem","rollOutMarker","rollOutItem","showItem","hideItem","clickMarker","rollOverItem","rollOutItem","clickLabel");
this.position="bottom";
this.color="#000000";
this.borderColor="#000000";
this.borderAlpha=0;this.markerLabelGap=5;
this.verticalGap=10;this.align="left";
this.horizontalGap=0;
this.spacing=10;this.markerDisabledColor="#AAB3B3";
this.markerType="square";
this.markerSize=16;this.markerBorderAlpha=0;
this.markerBorderThickness=1;
this.marginTop=10;this.marginBottom=10;
this.marginRight=15;this.marginLeft=80;
this.valueWidth=50;this.switchable=true;
this.switchType="x";this.switchColor="#FFFFFF";
this.rollOverColor="#CC0000";
this.selectedColor;this.reversedOrder=false;
this.labelText="[[title]]";
this.useMarkerColorForLabels=false;
this.rollOverGraphAlpha=1;
this.textClickEnabled=true;
this.usePositiveNegativeOnPercentsOnly=false
},setData:function(a){this.data=a;
this.invalidateSize()
},invalidateSize:function(){this.destroy();
this.entries=[];this.valueLabels=[];
if(this.data){if(this.data.length>0){this.drawLegend()
}}},drawLegend:function(){if(this.position=="right"||this.position=="left"){this.maxColumns=1;
this.marginRight=10;this.marginLeft=10
}if(this.width!=undefined){this.divWidth=AmCharts.toCoordinate(this.width,this.chart.realWidth)
}else{this.divWidth=this.chart.realWidth
}this.div.style.width=this.divWidth+"px";
if(!this.container){this.container=Raphael(this.div,this.divWidth,this.chart.realHeight)
}this.maxLabelWidth=0;
this.index=0;for(var a=0;
a<this.data.length;a++){this.createEntry(this.data[a])
}this.index=0;for(var a=0;
a<this.data.length;a++){this.createValue(this.data[a])
}this.arrangeEntries();
this.updateValues()},arrangeEntries:function(){w=this.divWidth-this.marginRight-this.marginLeft;
var q=0;var o=0;for(var p=0;
p<this.entries.length;
p++){var a=this.entries[p].getBBox();
var r=a.width;if(r>q){q=r
}var f=a.height;if(f>o){o=f
}}var k=0;var d=0;this.set=this.container.set();
for(var p=0;p<this.entries.length;
p++){var c=this.entries[p];
if(this.reversedOrder){c=this.entries[this.entries.length-p-1]
}var a=c.getBBox();var m=(this.horizontalGap+d*(q+this.spacing+this.markerLabelGap));
if(m+a.width>w&&p>0){k++;
d=0;m=this.horizontalGap
}var l=this.verticalGap+(o+this.verticalGap)*k;
c.translate(m+","+l);
d++;if(!isNaN(this.maxColumns)){if(d>=this.maxColumns){d=0;
k++}}this.set.push(c)
}var g=this.set.getBBox().height+2*this.verticalGap;
if(this.position=="left"||this.position=="right"){var s=this.set.getBBox().width+2*this.horizontalGap;
var b=s+this.marginLeft+this.marginRight;
this.div.style.width=b+"px"
}else{var s=this.divWidth-this.marginLeft-this.marginRight
}var j=this.marginLeft;
var h=this.marginTop;
var e=AmCharts.rect(this.container,s,g,this.backgroundColor,this.backgroundAlpha,1,this.borderColor,this.borderAlpha);
e.toBack();this.set.push(e);
this.set.translate(j+","+h);
if(this.position=="top"||this.position=="bottom"){this.set.pop();
if(this.align=="center"){this.set.translate(((s-(this.set.getBBox().width))/2)+","+0)
}}var n=g+this.marginTop+this.marginBottom;
this.div.style.height=n+"px"
},createEntry:function(h){if(h.visibleInLegend!==false){var g=h.markerType;
if(!g){g=this.markerType
}var c=h.color;var b=h.alpha;
if(h.legendKeyColor){c=h.legendKeyColor()
}if(h.legendKeyAlpha){b=h.legendKeyAlpha()
}if(h.hidden==true){c=this.markerDisabledColor
}var f=this.createMarker(g,c,b);
if(f.length>0){for(var e=0;
e<f.length;e++){f[e].dItem=h
}}else{f.dItem=h}if(this.switchType){if(this.switchType=="x"){var o=this.createX()
}else{var o=this.createV()
}}o.dItem=h;if(h.hidden!=true){if(this.switchType=="x"){o.hide()
}else{o.show()}}else{if(this.switchType!="x"){o.hide()
}}var k=this.container.set([f,o]);
var a=this;if(this.chart.touchEventsEnabled){k.touchend(function(){a.clickMarker(this.dItem)
});k.touchstart(function(){a.rollOverMarker(this.dItem)
})}else{k.hover(function(){a.rollOverMarker(this.dItem)
},function(){a.rollOutMarker(this.dItem)
}).click(function(){a.clickMarker(this.dItem)
})}var n=this.color;if(h.showBalloon&&this.textClickEnabled&&this.selectedColor!=undefined){n=this.selectedColor
}if(this.useMarkerColorForLabels){n=c
}if(h.hidden==true){n=this.markerDisabledColor
}var p=this.chart.fontSize;
if(!isNaN(this.fontSize)){p=this.fontSize
}var d=AmCharts.formatString(this.labelText,h,this.chart.numberFormatter,this.chart.percentFormatter);
if(d){var l=AmCharts.text(this.container,this.markerSize+this.markerLabelGap,this.markerSize/2,d,{fill:n,"text-anchor":"start","font-family":this.chart.fontFamily,"font-size":p});
var m=l.getBBox();lWidth=m.width;
if(this.maxLabelWidth<lWidth){this.maxLabelWidth=lWidth
}}var j=this.container.set();
if(f){j.push(f)}if(o){j.push(o)
}if(l){j.push(l)}this.entries[this.index]=j;
h.legendEntry=this.entries[this.index];
h.legendLabel=l;h.legendSwitch=o;
this.index++}},rollOverMarker:function(a){if(this.switchable){this.dispatch("rollOverMarker",a)
}else{this.dispatch("rollOverItem",a)
}},rollOutMarker:function(a){if(this.switchable){this.dispatch("rollOutMarker",a)
}else{this.dispatch("rollOutItem",a)
}},clickMarker:function(a){if(this.switchable){if(a.hidden==true){this.dispatch("showItem",a)
}else{this.dispatch("hideItem",a)
}}else{this.dispatch("clickMarker",a)
}},rollOverLabel:function(a){if(!a.hidden){if(a.legendLabel){a.legendLabel.attr({fill:this.rollOverColor})
}this.dispatch("rollOverItem",a)
}},rollOutLabel:function(b){if(!b.hidden){if(b.legendLabel){var a=this.color;
if(this.selectedColor!=undefined&&b.showBalloon){a=this.selectedColor
}b.legendLabel.attr({fill:a})
}this.dispatch("rollOutItem",b)
}},clickLabel:function(a){if(!a.hidden){this.dispatch("clickLabel",a)
}},dispatch:function(a,b){this.fire(a,{type:a,dataItem:b})
},createValue:function(d){if(d.visibleInLegend!==false){var c=this.maxLabelWidth;
if(this.valueText){var g=this.color;
if(this.useMarkerColorForLabels){g=color
}if(d.hidden==true){g=this.markerDisabledColor
}var j=this.chart.fontSize;
if(isNaN(this.fontSize)){j=this.fontSize
}var b=this.valueText;
var f=this.maxLabelWidth+this.markerSize+this.markerLabelGap*2+this.valueWidth;
var h=AmCharts.text(this.container,f,this.markerSize/2,b,{fill:g,"text-anchor":"end","font-family":this.chart.fontFamily,"font-size":j});
this.entries[this.index].push(h);
c+=this.valueWidth+this.markerLabelGap;
h.dItem=d;this.valueLabels.push(h)
}this.index++;var e=this.container.rect(this.markerSize+this.markerLabelGap,0,c,this.markerSize).attr({stroke:"none",fill:"#FFCCFF","fill-opacity":0});
e.dItem=d;this.entries[this.index-1].push(e);
var a=this;e.mouseover(function(){a.rollOverLabel(this.dItem)
}).mouseout(function(){a.rollOutLabel(this.dItem)
}).click(function(){a.clickLabel(this.dItem)
})}},createV:function(){var a=this.markerSize;
return this.container.path(["M",a/5,a/3,"L",a/2,a-a/5,"L",a-a/5,a/5,"L",a/2,a/1.7,"Z"]).attr({fill:this.switchColor,stroke:this.switchColor})
},createX:function(){var a=this.markerSize-3;
return this.container.path(["M",3,3,"L",a,a,"M",a,3,"L",3,a]).attr({stroke:this.switchColor,"stroke-width":3})
},createMarker:function(f,d,h){var e=this.markerSize;
var j=this.container;
var b;var g=this.markerBorderColor;
if(!g){g=d}var a={fill:d,stroke:g,opacity:h,"stroke-opacity":this.markerBorderAlpha,"stroke-width":this.markerBorderThickness};
switch(f){case"square":b=j.rect(0,0,e,e).attr(a);
break;case"circle":b=j.circle(e/2,e/2,e/2).attr(a);
break;case"line":b=j.path(["M",0,e/2,"L",e,e/2]).attr({stroke:d,"stroke-width":this.markerBorderThickness});
break;case"dashedLine":b=j.path(["M",0,e/2,"L",e/2-2,e/2,"M",e/2+2,e/2,"L",e,e/2]).attr({stroke:d,"stroke-width":this.markerBorderThickness});
break;case"triangleUp":b=j.path(["M",0,e,"L",e/2,0,"L",e,e,"L",0,e,"Z"]).attr(a);
break;case"triangleDown":b=j.path(["M",0,0,"L",e/2,e,"L",e,0,"L",0,0,"Z"]).attr(a);
break;case"bubble":a.fill=NaN;
a.gradient="r"+d+"-"+AmCharts.adjustLuminosity(d,-0.4);
b=j.circle(e/2,e/2,e/2).attr(a);
break;case"none":break
}return b},validateNow:function(){this.invalidateSize()
},updateValues:function(){for(var f=0;
f<this.valueLabels.length;
f++){var e=this.valueLabels[f];
var d=e.dItem;if(d.type!=undefined){if(d.currentDataItem){var h=this.valueText;
if(d.legendValueText){h=d.legendValueText
}var c=this.positiveValueColor;
var g=this.negativeValueColor;
if(d.hidden){c=NaN;g=NaN
}var b=AmCharts.formatString(h,d.currentDataItem.values,this.chart.numberFormatter,this.chart.percentFormatter,c,g,this.usePositiveNegativeOnPercentsOnly);
b=AmCharts.formatString(b,d.currentDataItem,this.chart.numberFormatter,this.chart.percentFormatter,c,g,this.usePositiveNegativeOnPercentsOnly);
b=this.cleanFromEmpty(b);
e.attr({text:b})}else{e.attr({text:" "})
}}else{var a=AmCharts.formatString(this.valueText,d,this.chart.numberFormatter,this.chart.percentFormatter);
e.attr({text:a})}}},cleanFromEmpty:function(b){var a=b.replace(/\[\[[^\]]*\]\]/,"");
return a},destroy:function(){if(this.container){this.container.clear()
}}});AmCharts.AmBalloon=AmCharts.Class({construct:function(){this.enabled=true;
this.fillColor="#CC0000";
this.fillAlpha=1;this.borderThickness=2;
this.borderColor="#FFFFFF";
this.borderAlpha=1;this.cornerRadius=6;
this.maximumWidth=220;
this.horizontalPadding=8;
this.verticalPadding=5;
this.pointerWidth=10;
this.pointerOrientation="vertical";
this.color="#FFFFFF";
this.textShadowColor="#000000";
this.adjustBorderColor=false;
this.showBullet=true;
this.follow=false;this.show=false
},draw:function(){var E=this.pointToX;
var D=this.pointToY;if(!isNaN(E)){var t=this.chart.container;
AmCharts.removeSet(this.set);
this.set=t.set();if(this.show){var s=this.l;
var a=this.t;var q=this.r;
var n=this.b;var p=this.textShadowColor;
if(this.color==p){p=null
}var G=this.balloonColor;
var m=this.fillColor;
var c=this.borderColor;
if(G!=undefined){if(this.adjustBorderColor){c=G
}else{m=G}}var C=this.horizontalPadding;
var e=this.verticalPadding;
var x=this.pointerWidth;
var k=this.pointerOrientation;
var d=this.cornerRadius;
var B=this.chart.fontFamily;
var u=this.fontSize;if(u==undefined){u=this.chart.fontSize
}var z=AmCharts.text(t,0,0,this.text,{fill:this.color,"font-family":B,"font-size":u});
this.set.push(z);if(p!=undefined){var l=AmCharts.text(t,1,1,this.text,{fill:p,opacity:0.4,"font-family":B,"font-size":u});
this.set.push(l)}var b=z.getBBox();
var A=b.height+2*e;var r=b.width+2*C;
if(window.opera){A+=6
}z.translate((r/2)+","+(A/2));
if(l){l.translate((r/2)+","+(A/2))
}var g;var f;if(k!="horizontal"){g=E-r/2;
if(D<a+30&&k!="down"){f=D+x
}else{f=D-A-x}}else{if(x*2>A){x=A/2
}f=D-A/2;if(E<s+(q-s)/2){g=E+x
}else{g=E-r-x}}if(f+A>=n){f=n-A
}if(f<a){f=a}if(g<s){g=s
}if(g+r>q){g=q-r}var j;
if(d>0){j=AmCharts.rect(t,r,A,[m],[this.fillAlpha],this.borderThickness,c,this.borderAlpha,this.cornerRadius);
if(this.showBullet){var y=AmCharts.circle(t,3,m,this.fillAlpha);
y.translate(E+","+D)}}else{var o=[];
var v=[];if(k!="horizontal"){var H=E-g;
if(H>r-x){H=r-x}if(H<x){H=x
}o=[0,H-x,E-g,H+x,r,r,0,0];
if(D<a+(n-a)/2&&k!="down"){v=[0,0,D-f+1,0,0,A,A,0]
}else{v=[A,A,D-f-1,A,A,0,0,A]
}}else{var F=D-f;if(F>A-x){F=A-x
}if(F<x){F=x}v=[0,F-x,D-f,F+x,A,A,0,0];
if(E<s+(q-s)/2){o=[0,0,E-g,0,0,r,r,0]
}else{o=[r,r,E-g,r,r,0,0,r]
}}j=AmCharts.polygon(t,o,v,m,this.fillAlpha,this.borderThickness,c,this.borderAlpha)
}this.set.push(j);j.toFront();
if(l){l.toFront()}z.toFront();
this.set.translate(g+","+f);
var b=j.getBBox();this.bottom=b.y+b.height;
this.yPos=b.y;if(y){this.set.push(y)
}}}},followMouse:function(){if(this.follow&&this.show){var c=this.chart.mouseX;
var b=this.chart.mouseY;
this.pointToX=c;this.pointToY=b;
if(c!=this.previousX||b!=this.previousY){this.previousX=c;
this.previousY=b;if(this.cornerRadius==0){this.draw()
}else{if(this.set){var d=this.set.getBBox();
var a=c-d.width/2;var e=b-d.height-10;
if(a<this.l){a=this.l
}if(a>this.r-d.width){a=this.r-d.width
}this.set.translate((a-d.x)+","+(e-d.y))
}}}}},changeColor:function(a){this.balloonColor=a
},setBounds:function(c,d,e,a){this.l=c;
this.t=d;this.r=e;this.b=a
},showBalloon:function(a){this.text=a;
this.show=true;this.draw()
},hide:function(){this.show=false;
this.follow=false;this.destroy()
},setPosition:function(a,c,b){this.pointToX=a;
this.pointToY=c;if(b){if(a!=this.previousX||c!=this.previousY){this.draw()
}}this.previousX=a;this.previousY=c
},followCursor:function(a){this.follow=a;
if(a){this.pShowBullet=this.showBullet;
this.showBullet=false
}else{if(this.pShowBullet!=undefined){this.showBullet=this.pShowBullet
}}clearInterval(this.interval);
if(!isNaN(this.chart.mouseX)){if(a){this.pointToX=this.chart.mouseX;
this.pointToY=this.chart.mouseY;
var b=this;this.interval=setInterval(function(){b.followMouse.call(b)
},20)}}},destroy:function(){clearInterval(this.interval);
AmCharts.removeSet(this.set)
}});AmCharts.AmCoordinateChart=AmCharts.Class({inherits:AmCharts.AmChart,construct:function(){AmCharts.AmCoordinateChart.base.construct.call(this);
this.createEvents("rollOverGraphItem","rollOutGraphItem","clickGraphItem","doubleClickGraphItem");
this.plotAreaFillColors="#FFFFFF";
this.plotAreaFillAlphas=0;
this.plotAreaBorderColor="#000000";
this.plotAreaBorderAlpha=0;
this.startAlpha=0;this.startDuration=0;
this.startEffect="elastic";
this.sequencedAnimation=true;
this.colors=["#FF6600","#FCD202","#B0DE09","#0D8ECF","#2A0CD0","#CD0D74","#CC0000","#00CC00","#0000CC","#DDDDDD","#999999","#333333","#990000"];
this.balloonDateFormat="MMM DD, YYYY";
this.valueAxes=[];this.graphs=[]
},initChart:function(){AmCharts.AmCoordinateChart.base.initChart.call(this);
if(this.valueAxes.length==0){var a=new AmCharts.ValueAxis();
this.addValueAxis(a)}if(this.legend){this.legend.setData(this.graphs)
}},parseData:function(){this.processValueAxes();
this.processGraphs()},parseSerialData:function(){AmCharts.AmSerialChart.base.parseData.call(this);
this.chartData=[];if(this.dataProvider){var f=this.dataProvider.length;
var g=false;if(this.categoryAxis){g=this.categoryAxis.parseDates
}if(g){var u=AmCharts.extractPeriod(this.categoryAxis.minPeriod);
var l=u.period;var d=u.count
}for(var q=0;q<f;q++){var t=[];
var c=this.dataProvider[q];
var n=c[this.categoryField];
t.category=n;if(g){n=new Date(n);
n=AmCharts.resetDateToMin(n,l,d);
t.category=n;t.time=n.getTime()
}var h=this.valueAxes;
var e=h.length;t.axes={};
t.x={};for(var p=0;p<e;
p++){var s=h[p].id;t.axes[s]={};
t.axes[s].graphs={};graphs=this.graphs;
var a=graphs.length;for(var o=0;
o<a;o++){graph=this.graphs[o];
var m=graph.id;if(graph.valueAxis.id==s){t.axes[s].graphs[m]={};
var r={};r.index=q;var b={};
var v=Number(c[graph.valueField]);
if(!isNaN(v)){b.value=v
}var v=Number(c[graph.openField]);
if(!isNaN(v)){b.open=v
}var v=Number(c[graph.closeField]);
if(!isNaN(v)){b.close=v
}var v=Number(c[graph.lowField]);
if(!isNaN(v)){b.low=v
}var v=Number(c[graph.highField]);
if(!isNaN(v)){b.high=v
}r.values=b;this.processFields(graph,r,c);
r.category=String(t.category);
r.serialDataItem=t;r.graphTitle=graph.title;
t.axes[s].graphs[m]=r
}}}this.chartData[q]=t
}}},addValueAxis:function(a){a.chart=this;
this.valueAxes.push(a);
this.validateData()},removeValueAxesAndGraphs:function(){for(var a=this.valueAxes.length-1;
a>-1;a--){this.removeValueAxis(this.valueAxes[a])
}},removeValueAxis:function(d){var b=this.graphs.length;
var a;for(a=b-1;a>=0;
a--){var c=this.graphs[a];
if(c){if(c.valueAxis==d){this.removeGraph(c)
}}}b=this.valueAxes.length;
for(a=b-1;a>=0;a--){if(this.valueAxes[a]==d){this.valueAxes.splice(a,1)
}}this.validateData()
},addGraph:function(a){this.graphs.push(a);
this.chooseGraphColor(a,this.graphs.length-1);
this.validateData()},removeGraph:function(c){var b=this.graphs.length;
for(var a=b-1;a>=0;a--){if(this.graphs[a]==c){this.graphs.splice(a,1);
c.destroy()}}this.validateData()
},processValueAxes:function(){for(var a=0;
a<this.valueAxes.length;
a++){var b=this.valueAxes[a];
b.chart=this;if(!b.id){b.id="valueAxis"+a
}if(this.rotate){b.orientation="horizontal"
}else{b.orientation="vertical"
}if(this.usePrefixes===true||this.usePrefixes===false){b.usePrefixes=this.usePrefixes
}}},processGraphs:function(){for(var a=0;
a<this.graphs.length;
a++){var b=this.graphs[a];
b.chart=this;if(!b.valueAxis){b.valueAxis=this.valueAxes[0]
}if(!b.id){b.id="graph"+a
}}},formatString:function(j,f){var c=f.serialDataItem;
if(this.categoryAxis){if(this.categoryAxis.parseDates){var h=this.balloonDateFormat;
if(this.chartCursor){h=this.chartCursor.categoryBalloonDateFormat
}if(j.indexOf("[[category]]")!=-1){var g=AmCharts.formatDate(c.category,h);
var e=AmCharts.formatDate(c.category,h);
if(e.indexOf("fff")!=-1){e=AmCharts.formatMilliseconds(g,c.category)
}j=j.split("[[category]]").join(e)
}}}var b=graph.numberFormatter;
if(!b){b=this.numberFormatter
}if(c){j=j.replace(/\[\[category\]\]/g,c.category)
}var a=graph.valueAxis;
if(a.duration){if(f.values.value){var d=AmCharts.formatDuration(f.values.value,a.duration,"",a.durationUnits,a.maxInterval,a.numberFormatter);
j=j.split("[[value]]").join(d)
}}j=AmCharts.formatString(j,f,b,this.percentFormatter);
j=AmCharts.formatString(j,f.values,b,this.percentFormatter);
return j},getBalloonColor:function(f,d){var c=f.lineColor;
var b=f.balloonColor;
var a=f.fillColors;if(typeof(a)=="object"){c=a[0]
}else{if(a!=undefined){c=a
}}if(d.isNegative){var e=f.negativeLineColor;
var g=f.negativeFillColors;
if(typeof(g)=="object"){e=g[0]
}else{if(g!=undefined){e=g
}}if(e!=undefined){c=e
}}if(d.color!=undefined){c=d.color
}if(b==undefined){b=c
}return b},getGraphById:function(e){var b;
var c=this.graphs.length;
for(var a=0;a<c;a++){var d=this.graphs[a];
if(d.id==e){b=d}}return b
},processFields:function(j,k,b){if(j.itemColors){var d=j.itemColors;
var f=k.index;if(f<d.length){k.color=d[f]
}else{k.color=AmCharts.randomColor()
}}var e=["color","alpha","fillColors","description","bullet","customBullet","bulletSize","bulletConfig","url"];
for(var c=0;c<e.length;
c++){var g=e[c];var h=j[g+"Field"];
if(h){var a=b[h];if(AmCharts.isDefined(a)){k[g]=a
}}}k.dataContext=b},chooseGraphColor:function(c,b){if(c.lineColor==undefined){var a;
if(this.colors.length-1>b){a=this.colors[b]
}else{a=AmCharts.randomColor()
}c.lineColor=a}},handleLegendEvent:function(d){var c=d.type;
var b=d.dataItem;if(b){var e=b.hidden;
var a=b.showBalloon;switch(c){case"clickMarker":if(a){this.hideGraphsBalloon(b)
}else{this.showGraphsBalloon(b)
}break;case"clickLabel":if(a){this.hideGraphsBalloon(b)
}else{this.showGraphsBalloon(b)
}break;case"rollOverItem":if(!e){this.highlightGraph(b)
}break;case"rollOutItem":if(!e){this.unhighlightGraph()
}break;case"hideItem":this.hideGraph(b);
break;case"showItem":this.showGraph(b);
break}}},highlightGraph:function(a){var c=this.graphs.length;
var b;var e=0.2;var d;
if(this.legend){e=this.legend.rollOverGraphAlpha
}for(b=0;b<c;b++){d=this.graphs[b];
if(d!=a){d.changeOpacity(e)
}}},unhighlightGraph:function(){var b=this.graphs.length;
for(var a=0;a<b;a++){var c=this.graphs[a];
c.changeOpacity(1)}},showGraph:function(a){a.hidden=false;
this.validateData()},hideGraph:function(a){a.hidden=true;
this.validateData()},hideGraphsBalloon:function(a){a.showBalloon=false;
this.updateLegend()},showGraphsBalloon:function(a){a.showBalloon=true;
this.updateLegend()},updateLegend:function(){if(this.legend){this.legend.invalidateSize()
}},destroySets:function(){AmCharts.AmCoordinateChart.base.destroySets.call(this);
if(this.graphs){for(var a=0;
a<this.graphs.length;
a++){this.graphs[a].set=null
}}if(this.valueAxes){for(var a=0;
a<this.valueAxes.length;
a++){this.valueAxes[a].set=null
}}}});AmCharts.AmRectangularChart=AmCharts.Class({inherits:AmCharts.AmCoordinateChart,construct:function(){AmCharts.AmRectangularChart.base.construct.call(this);
this.createEvents("zoomed");
this.marginLeft=80;this.marginTop=15;
this.marginBottom=35;
this.marginRight=15;this.angle=0;
this.depth3D=0;this.horizontalPosition=0;
this.verticalPosition=0;
this.widthMultiplyer=1;
this.heightMultiplyer=1;
this.zoomOutText="Show all";
this.zoomOutButtonSet;
this.zoomOutButton={backgroundColor:"#b2e1ff",backgroundAlpha:1}
},initChart:function(){AmCharts.AmRectangularChart.base.initChart.call(this);
this.updateDxy();this.updateMargins();
this.updatePlotArea();
this.updateScrollbars();
this.updateChartCursor();
this.updateValueAxes();
this.updateGraphs()},drawChart:function(){AmCharts.AmRectangularChart.base.drawChart.call(this);
this.drawPlotArea();if(this.chartData){if(this.chartData.length>0){if(this.chartCursor){this.chartCursor.draw()
}if(this.zoomOutText!=""&&this.zoomOutText){this.drawZoomOutButton()
}}}},drawZoomOutButton:function(){var a=this;
this.zoomOutButtonSet=this.container.set();
var b=this.color;var k=this.fontSize;
if(this.zoomOutButton){if(this.zoomOutButton.fontSize){k=this.zoomOutButton.fontSize
}if(this.zoomOutButton.color){b=this.zoomOutButton.color
}}var g=AmCharts.text(this.container,29,8,this.zoomOutText,{fill:b,"font-family":this.fontFamily,"font-size":k,"text-anchor":"start"});
var e=g.getBBox();g.translate(0+","+e.height/2);
var d=AmCharts.rect(this.container,e.width+40,e.height+15,[this.zoomOutButton.backgroundColor],[this.zoomOutButton.backgroundAlpha]);
var f=this.container.image(this.pathToImages+"lens.png",7,7,16,16);
f.translate(0+","+(e.height/2-6));
f.toFront();g.toFront();
d.hide();this.zoomOutButtonBG=d;
this.lens=f;this.zoomOutButtonSet.push(d);
this.zoomOutButtonSet.push(f);
this.zoomOutButtonSet.push(g);
this.set.push(this.zoomOutButtonSet);
var h=this.zoomOutButtonSet.getBBox();
this.zoomOutButtonSet.translate((this.marginLeftReal+this.plotAreaWidth-h.width)+","+this.marginTopReal);
this.zoomOutButtonSet.hide();
if(this.touchEventsEnabled){this.zoomOutButtonSet.touchstart(function(){a.rollOverZB()
}).touchend(function(){a.clickZB()
})}this.zoomOutButtonSet.mouseover(function(){a.rollOverZB()
}).mouseout(function(){a.rollOutZB()
}).click(function(){a.clickZB()
});for(var c=0;c<this.zoomOutButtonSet.length;
c++){this.zoomOutButtonSet[c].attr({cursor:"pointer"})
}},rollOverZB:function(){this.zoomOutButtonBG.show()
},rollOutZB:function(){this.zoomOutButtonBG.hide()
},clickZB:function(){this.zoomOut()
},zoomOut:function(){this.updateScrollbar=true;
this.zoom()},drawPlotArea:function(){var n=this.dx;
var m=this.dy;var a=this.marginLeftReal;
var j=this.marginTopReal;
var k=this.plotAreaWidth;
var e=this.plotAreaHeight;
var c=AmCharts.toSvgColor(this.plotAreaFillColors);
var b=this.plotAreaFillAlphas;
if(typeof(b)=="object"){b=b[0]
}var d=AmCharts.rect(this.container,k,e,this.plotAreaFillColors,b,1,this.plotAreaBorderColor,this.plotAreaBorderAlpha);
d.translate(a+","+j);
this.set.push(d);if(n!=0&&m!=0){d.translate(n+","+m);
c=this.plotAreaFillColors;
if(typeof(c)=="object"){c=c[0]
}c=AmCharts.adjustLuminosity(c,-0.15);
var f={fill:c,"fill-opacity":b,stroke:this.plotAreaBorderColor,"stroke-opacity":this.plotAreaBorderAlpha};
var l=this.container.path(["M",0,0,"L",n,m,"L",k+n,m,"L",k,0,"L",0,0,"Z"]).attr(f);
l.translate(a+","+(j+e));
this.set.push(l);var g=this.container.path(["M",0,0,"L",0,e,"L",n,e+m,"L",n,m,"L",0,0,"Z"]).attr(f);
g.translate(a+","+j);
this.set.push(g)}},updatePlotArea:function(){this.realWidth=this.updateWidth();
this.realHeight=this.updateHeight();
var c=this.dx;var b=this.dy;
var d=this.marginLeftReal;
var f=this.marginTopReal;
var a=this.realWidth-d-this.marginRightReal-c;
var e=this.realHeight-f-this.marginBottomReal;
if(a<1){a=1}if(e<1){e=1
}this.plotAreaWidth=Math.round(a);
this.plotAreaHeight=Math.round(e)
},updateDxy:function(){this.dx=this.depth3D*Math.cos(this.angle*Math.PI/180);
this.dy=-this.depth3D*Math.sin(this.angle*Math.PI/180)
},updateMargins:function(){this.marginTopReal=this.marginTop-this.dy;
this.marginBottomReal=this.marginBottom;
this.marginLeftReal=this.marginLeft;
this.marginRightReal=this.marginRight
},updateValueAxes:function(){for(var a=0;
a<this.valueAxes.length;
a++){var b=this.valueAxes[a];
b.axisRenderer=AmCharts.RectangularAxisRenderer;
b.guideFillRenderer=AmCharts.RectangularAxisGuideFillRenderer;
b.axisItemRenderer=AmCharts.RectangularAxisItemRenderer;
if(this.rotate){b.orientation="horizontal"
}else{b.orientation="vertical"
}b.x=this.marginLeftReal;
b.y=this.marginTopReal;
b.dx=this.dx;b.dy=this.dy;
b.width=this.plotAreaWidth;
b.height=this.plotAreaHeight;
b.visibleAxisWidth=this.plotAreaWidth;
b.visibleAxisHeight=this.plotAreaHeight;
b.visibleAxisX=this.marginLeftReal;
b.visibleAxisY=this.marginTopReal
}},updateGraphs:function(){var b=this.graphs.length;
for(var a=0;a<b;a++){var c=this.graphs[a];
c.x=this.marginLeftReal+this.horizontalPosition;
c.y=this.marginTopReal+this.verticalPosition;
c.width=this.plotAreaWidth*this.widthMultiplyer;
c.height=this.plotAreaHeight*this.heightMultiplyer;
c.index=a;c.dx=this.dx;
c.dy=this.dy;c.rotate=this.rotate;
c.chartType=this.chartType
}},updateChartCursor:function(){if(this.chartCursor){var a=this.chartCursor;
this.chartCursor.x=this.marginLeftReal;
this.chartCursor.y=this.marginTopReal;
this.chartCursor.width=this.plotAreaWidth;
this.chartCursor.height=this.plotAreaHeight;
this.chartCursor.chart=this
}},updateScrollbars:function(){},addChartCursor:function(a){if(this.chartCursor){this.chartCursor.destroy()
}this.chartCursor=a;if(this.chartCursor){this.listenTo(this.chartCursor,"changed",this.handleCursorChange);
this.listenTo(this.chartCursor,"zoomed",this.handleCursorZoom)
}},removeChartCursor:function(){if(this.chartCursor){this.chartCursor.destroy();
this.chartCursor=null
}},addChartScrollbar:function(a){if(this.chartScrollbar){this.chartScrollbar.destroy()
}this.chartScrollbar=a;
if(this.chartScrollbar){this.chartScrollbar.chart=this;
this.listenTo(this.chartScrollbar,"zoomed",this.handleScrollbarZoom)
}if(this.rotate){if(this.chartScrollbar.width==undefined){this.chartScrollbar.width=this.chartScrollbar.scrollbarHeight
}}else{if(this.chartScrollbar.height==undefined){this.chartScrollbar.height=this.chartScrollbar.scrollbarHeight
}}},removeChartScrollbar:function(){if(this.chartScrollbar){this.chartScrollbar.destroy();
this.chartScrollbar=null
}},handleReleaseOutside:function(a){AmCharts.AmRectangularChart.base.handleReleaseOutside.call(this,a);
if(this.chartScrollbar){this.chartScrollbar.handleReleaseOutside()
}if(this.chartCursor){this.chartCursor.handleReleaseOutside()
}},handleMouseDown:function(a){AmCharts.AmRectangularChart.base.handleMouseDown.call(this,a);
if(this.chartCursor){this.chartCursor.handleMouseDown(a)
}},destroySets:function(){AmCharts.AmRectangularChart.base.destroySets.call(this);
if(this.chartCursor){this.chartCursor.set=null
}if(this.chartScrollbar){this.chartScrollbar.set=null
}}});AmCharts.AmSerialChart=AmCharts.Class({inherits:AmCharts.AmRectangularChart,construct:function(){AmCharts.AmSerialChart.base.construct.call(this);
this.createEvents("changed","zoomed");
this.columnSpacing=5;
this.columnWidth=0.8;
this.maxSelectedSeries;
this.updateScrollbar=true;
this.maxSelectedTime;
this.categoryAxis=new AmCharts.CategoryAxis();
this.categoryAxis.chart=this;
this.chartType="serial";
this.zoomOutOnDataUpdate=true
},initChart:function(){AmCharts.AmSerialChart.base.initChart.call(this);
this.updateCategoryAxis();
if(this.dataChanged){this.updateData();
this.dataChanged=false;
this.dispatchDataUpdated=true
}this.updateScrollbar=true;
this.drawChart()},drawChart:function(){AmCharts.AmSerialChart.base.drawChart.call(this);
var d=this.chartData;
if(d){if(d.length>0){if(this.chartScrollbar){this.chartScrollbar.draw()
}var b=d.length-1;var c;
var a;if(this.categoryAxis.parseDates&&!this.categoryAxis.equalSpacing){c=this.startTime;
a=this.endTime;if(isNaN(c)||isNaN(a)){c=d[0].time;
a=d[b].time}}else{c=this.start;
a=this.end;if(isNaN(c)||isNaN(a)){c=0;
a=b}}this.start=undefined;
this.end=undefined;this.startTime=undefined;
this.endTime=undefined;
this.zoom(c,a)}else{this.cleanChart()
}}this.bringLabelsToFront();
this.chartCreated=true;
if(this.dispatchDataUpdated){this.dispatchDataUpdated=false;
this.dispatchDataUpdatedEvent()
}},cleanChart:function(){for(var a=0;
a<this.valueAxes.length;
a++){this.valueAxes[a].destroy()
}for(var a=0;a<this.graphs.length;
a++){this.graphs[a].destroy()
}if(this.categoryAxis){this.categoryAxis.destroy()
}if(this.chartCursor){this.chartCursor.destroy()
}if(this.chartScrollbar){this.chartScrollbar.destroy()
}},updateCategoryAxis:function(){this.categoryAxis.id="categoryAxis";
this.categoryAxis.axisRenderer=AmCharts.RectangularAxisRenderer;
this.categoryAxis.guideFillRenderer=AmCharts.RectangularAxisGuideFillRenderer;
this.categoryAxis.axisItemRenderer=AmCharts.RectangularAxisItemRenderer;
if(this.rotate){this.categoryAxis.orientation="vertical"
}else{this.categoryAxis.orientation="horizontal"
}this.categoryAxis.x=this.marginLeftReal;
this.categoryAxis.y=this.marginTopReal;
this.categoryAxis.dx=this.dx;
this.categoryAxis.dy=this.dy;
this.categoryAxis.width=this.plotAreaWidth;
this.categoryAxis.height=this.plotAreaHeight;
this.categoryAxis.visibleAxisWidth=this.plotAreaWidth;
this.categoryAxis.visibleAxisHeight=this.plotAreaHeight;
this.categoryAxis.visibleAxisX=this.marginLeftReal;
this.categoryAxis.visibleAxisY=this.marginTopReal
},updateValueAxes:function(){AmCharts.AmSerialChart.base.updateValueAxes.call(this);
var b=this.valueAxes.length;
for(var a=0;a<b;a++){var c=this.valueAxes[a];
if(!this.categoryAxis.startOnAxis||this.categoryAxis.parseDates){c.expandMinMax=true
}}},updateData:function(){if(this.zoomOutOnDataUpdate){this.start=NaN;
this.startTime=NaN;this.end=NaN;
this.endTime=NaN}this.parseData();
this.columnCount=this.countColumns();
if(this.chartCursor){this.chartCursor.updateData()
}count=this.graphs.length;
for(var a=0;a<count;a++){var b=this.graphs[a];
b.columnCount=this.columnCount;
b.data=this.chartData
}},updateMargins:function(){AmCharts.AmSerialChart.base.updateMargins.call(this);
this.scrollbarHeight=0;
if(this.chartScrollbar){if(this.rotate){this.scrollbarHeight=this.chartScrollbar.width
}else{this.scrollbarHeight=this.chartScrollbar.height
}if(this.rotate){if(this.categoryAxis.position=="bottom"||this.categoryAxis.position=="left"){this.scrollbarPosition="bottom"
}else{this.scrollbarPosition="top"
}}else{if(this.categoryAxis.position=="top"||this.categoryAxis.position=="right"){this.scrollbarPosition="bottom"
}else{this.scrollbarPosition="top"
}}if(this.scrollbarPosition=="top"){if(this.rotate){this.marginLeftReal+=this.scrollbarHeight
}else{this.marginTopReal+=this.scrollbarHeight
}}else{if(this.rotate){this.marginRightReal+=this.scrollbarHeight
}else{this.marginBottomReal+=this.scrollbarHeight
}}}},updateScrollbars:function(){if(this.chartScrollbar){if(this.scrollbarPosition=="top"){if(this.rotate){this.chartScrollbar.y=this.marginTopReal;
this.chartScrollbar.x=this.marginLeftReal-this.scrollbarHeight
}else{this.chartScrollbar.y=this.marginTopReal-this.scrollbarHeight+this.dy;
this.chartScrollbar.x=this.marginLeftReal+this.dx
}}else{if(this.rotate){this.chartScrollbar.y=this.marginTopReal+this.dy;
this.chartScrollbar.x=this.marginLeftReal+this.plotAreaWidth+this.dx
}else{this.chartScrollbar.y=this.marginTopReal+this.plotAreaHeight+1;
this.chartScrollbar.x=this.marginLeft
}}if(this.rotate){this.chartScrollbar.height=this.plotAreaHeight
}else{this.chartScrollbar.width=this.plotAreaWidth
}this.chartScrollbar.mainCategoryAxis=this.categoryAxis
}},zoom:function(b,a){if(this.categoryAxis.parseDates&&!this.categoryAxis.equalSpacing){this.timeZoom(b,a)
}else{this.indexZoom(b,a)
}this.updateDepths()},timeZoom:function(b,a){if(!isNaN(this.maxSelectedTime)){if(a!=this.endTime){if(a-b>this.maxSelectedTime){b=a-this.maxSelectedTime;
this.updateScrollbar=true
}}if(b!=this.startTime){if(a-b>this.maxSelectedTime){a=b+this.maxSelectedTime;
this.updateScrollbar=true
}}}if(this.chartData){if(this.chartData.length>0){if(b!=this.startTime||a!=this.endTime){var c=this.categoryAxis.minDuration();
this.firstTime=this.chartData[0].time;
this.lastTime=this.chartData[this.chartData.length-1].time;
if(!b){b=this.firstTime;
if(!isNaN(this.maxSelectedTime)){b=this.lastTime-this.maxSelectedTime
}}if(!a){a=this.lastTime
}if(b>this.lastTime){b=this.lastTime
}if(a<this.firstTime){a=this.firstTime
}if(b<this.firstTime){b=this.firstTime
}if(a>this.lastTime){a=this.lastTime
}if(a<b){a=b+c}this.startTime=b;
this.endTime=a;var d=this.chartData.length-1;
this.start=this.getClosestIndex(this.chartData,"time",this.startTime,true,0,d);
this.end=this.getClosestIndex(this.chartData,"time",this.endTime,false,this.start,d);
this.categoryAxis.timeZoom(this.startTime,this.endTime);
this.categoryAxis.zoom(this.start,this.end);
this.start=AmCharts.fitToBounds(this.start,0,d);
this.end=AmCharts.fitToBounds(this.end,0,d);
this.zoomAxesAndGraphs();
this.zoomScrollbar();
if(b!=this.firstTime||a!=this.lastTime){if(this.zoomOutButtonSet){this.zoomOutButtonSet.show();
this.zoomOutButtonBG.hide()
}}else{this.zoomOutButtonSet.hide()
}this.dispatchTimeZoomEvent()
}}}},indexZoom:function(c,a){if(!isNaN(this.maxSelectedSeries)){if(a!=this.end){if(a-c>this.maxSelectedSeries){c=a-this.maxSelectedSeries;
this.updateScrollbar=true
}}if(c!=this.start){if(a-c>this.maxSelectedSeries){a=c+this.maxSelectedSeries;
this.updateScrollbar=true
}}}if(c!=this.start||a!=this.end){var b=this.chartData.length-1;
if(isNaN(c)){c=0;if(!isNaN(this.maxSelectedSeries)){c=b-this.maxSelectedSeries
}}if(isNaN(a)){a=b}if(a<c){a=c
}if(a>b){a=b}if(c>b){c=b-1
}if(c<0){c=0}this.start=c;
this.end=a;this.categoryAxis.zoom(this.start,this.end);
this.zoomAxesAndGraphs();
this.zoomScrollbar();
if(c!=0||a!=this.dataProvider.length-1){if(this.zoomOutButtonSet){this.zoomOutButtonSet.show();
this.zoomOutButtonBG.hide()
}}else{if(this.zoomOutButtonSet){this.zoomOutButtonSet.hide()
}}this.dispatchIndexZoomEvent()
}},updateGraphs:function(){AmCharts.AmSerialChart.base.updateGraphs.call(this);
var b=this.graphs.length;
for(var a=0;a<b;a++){var c=this.graphs[a];
c.columnWidth=this.columnWidth;
c.categoryAxis=this.categoryAxis
}},updateDepths:function(){this.mostFrontObj=this.container.rect(0,0,10,10);
this.updateColumnsDepth();
var d=this.graphs.length;
for(var c=0;c<d;c++){var e=this.graphs[c];
if(e.type!="column"){e.set.insertBefore(this.mostFrontObj)
}if(e.allBullets){for(var b=0;
b<e.allBullets.length;
b++){e.allBullets[b].insertBefore(this.mostFrontObj)
}}if(e.positiveObjectsToClip){for(var b=0;
b<e.positiveObjectsToClip.length;
b++){e.setPositiveClipRect(e.positiveObjectsToClip[b])
}}if(e.negativeObjectsToClip){for(var b=0;
b<e.negativeObjectsToClip.length;
b++){e.setNegativeClipRect(e.negativeObjectsToClip[b])
}}var f=e.objectsToAddListeners;
if(f){for(var b=0;b<f.length;
b++){e.addClickListeners(f[b]);
if(!this.chartCursor){e.addHoverListeners(f[b])
}}}}if(this.chartCursor){this.chartCursor.set.insertBefore(this.mostFrontObj)
}if(this.zoomOutButtonSet){this.zoomOutButtonSet.insertBefore(this.mostFrontObj)
}d=this.valueAxes.length;
for(var c=0;c<d;c++){var g=this.valueAxes[c];
g.axisLine.set.toFront();
if(g.grid0){AmCharts.putSetToFront(g.grid0)
}AmCharts.putSetToFront(g.axisLine.set);
for(var b=0;b<g.allLabels.length;
b++){g.allLabels[b].toFront()
}}var a=this.categoryAxis;
a.axisLine.set.toFront();
for(var b=0;b<a.allLabels.length;
b++){a.allLabels[b].toFront()
}this.mostFrontObj.remove();
if(this.bgImg){this.bgImg.toBack()
}if(this.background){this.background.toBack()
}this.drb()},updateColumnsDepth:function(){var b;
var f=this.graphs.length;
this.columnsArray=[];
for(b=0;b<f;b++){var d=this.graphs[b];
var c=d.columnsArray;
if(c){for(var a=0;a<c.length;
a++){this.columnsArray.push(c[a])
}}}var e=this;this.columnsArray.sort(this.compareDepth);
f=this.columnsArray.length;
for(b=0;b<f;b++){this.columnsArray[b].column.set.insertBefore(this.mostFrontObj)
}},compareDepth:function(d,c){if(d.depth>c.depth){return 1
}else{return -1}},zoomScrollbar:function(){if(this.chartScrollbar){if(this.updateScrollbar){if(this.categoryAxis.parseDates&&!this.categoryAxis.equalSpacing){this.chartScrollbar.timeZoom(this.startTime,this.endTime)
}else{this.chartScrollbar.zoom(this.start,this.end)
}this.updateScrollbar=true
}}},zoomAxesAndGraphs:function(){var b=this.valueAxes.length;
for(var a=0;a<b;a++){var d=this.valueAxes[a];
d.zoom(this.start,this.end)
}b=this.graphs.length;
for(a=0;a<b;a++){var c=this.graphs[a];
c.zoom(this.start,this.end)
}if(this.chartCursor){this.chartCursor.zoom(this.start,this.end,this.startTime,this.endTime)
}},countColumns:function(){var f=0;
var k=this.valueAxes.length;
var b=this.graphs.length;
var h;var a;var g=false;
var c;for(var d=0;d<k;
d++){a=this.valueAxes[d];
var e=a.stackType;if(e=="100%"||e=="regular"){g=false;
for(c=0;c<b;c++){h=this.graphs[c];
if(!h.hidden){if(h.valueAxis==a&&h.type=="column"){if(!g&&h.stackable){f++;
g=true}if(!h.stackable){f++
}h.columnIndex=f-1}}}}if(e=="none"||e=="3d"){for(c=0;
c<b;c++){h=this.graphs[c];
if(!h.hidden){if(h.valueAxis==a&&h.type=="column"){h.columnIndex=f;
f++}}}}if(e=="3d"){for(d=0;
d<b;d++){h=this.graphs[d];
h.depthCount=f}f=1}}return f
},parseData:function(){AmCharts.AmSerialChart.base.parseData.call(this);
this.parseSerialData()
},getCategoryIndexByValue:function(d){var c=this.chartData.length;
var a;for(var b=0;b<c;
b++){if(this.chartData[b].category==d){a=b
}}return a},handleCursorChange:function(a){this.dispatchCursorEvent(a.index)
},handleCursorZoom:function(a){this.updateScrollbar=true;
this.zoom(a.start,a.end)
},handleScrollbarZoom:function(a){this.updateScrollbar=false;
this.zoom(a.start,a.end)
},dispatchTimeZoomEvent:function(){if(this.prevStartTime!=this.startTime||this.prevEndTime!=this.endTime){var a={};
a.type="zoomed";a.startDate=new Date(this.startTime);
a.endDate=new Date(this.endTime);
a.startIndex=this.start;
a.endIndex=this.end;this.startIndex=this.start;
this.endIndex=this.end;
this.prevStartTime=this.startTime;
this.prevEndTime=this.endTime;
a.startValue=AmCharts.formatDate(a.startDate,this.categoryAxis.dateFormatsObject[this.categoryAxis.minPeriod]);
a.endValue=AmCharts.formatDate(a.endDate,this.categoryAxis.dateFormatsObject[this.categoryAxis.minPeriod]);
this.fire(a.type,a)}},dispatchIndexZoomEvent:function(){if(this.prevStartIndex!=this.start||this.prevEndIndex!=this.end){this.startIndex=this.start;
this.endIndex=this.end;
if(this.chartData){if(this.chartData.length>0){if(!isNaN(this.start)&&!isNaN(this.end)){var a={};
a.type="zoomed";a.startIndex=this.start;
a.endIndex=this.end;a.startValue=this.chartData[this.start].category;
a.endValue=this.chartData[this.end].category;
if(this.categoryAxis.parseDates){this.startTime=this.chartData[this.start].time;
this.endTime=this.chartData[this.end].time;
a.startDate=new Date(this.startTime);
a.endDate=new Date(this.endTime)
}this.prevStartIndex=this.start;
this.prevEndIndex=this.end;
this.fire(a.type,a)}}}}},dispatchCursorEvent:function(c){var e=this.graphs.length;
for(var d=0;d<e;d++){var f=this.graphs[d];
if(isNaN(c)){f.currentDataItem=undefined
}else{var b=this.chartData[c];
var a=b.axes[f.valueAxis.id].graphs[f.id];
f.currentDataItem=a}}if(this.legend){this.legend.updateValues()
}},getClosestIndex:function(j,f,g,c,a,b){if(a<0){a=0
}if(b>j.length-1){b=j.length-1
}var e=a+Math.round((b-a)/2);
var k=j[e][f];if(b-a<=1){if(c){return a
}else{var d=j[a][f];var h=j[b][f];
if(Math.abs(d-g)<Math.abs(h-g)){return a
}else{return b}}}if(g==k){return e
}else{if(g<k){return this.getClosestIndex(j,f,g,c,a,e)
}else{return this.getClosestIndex(j,f,g,c,e,b)
}}},zoomToIndexes:function(b,a){this.updateScrollbar=true;
if(this.chartData){if(this.chartData.length>0){if(b<0){b=0
}if(a>this.chartData.length-1){a=this.chartData.length-1
}if(this.categoryAxis.parseDates&&!this.categoryAxis.equalSpacing){this.zoom(this.chartData[b].time,this.chartData[a].time)
}else{this.zoom(b,a)}}}},zoomToDates:function(d,a){this.updateScrollbar=true;
if(this.categoryAxis.equalSpacing){var c=this.getClosestIndex(this.chartData,"time",d.getTime(),true,0,this.chartData.length);
var b=this.getClosestIndex(this.chartData,"time",a.getTime(),false,0,this.chartData.length);
this.zoom(c,b)}else{this.zoom(d.getTime(),a.getTime())
}},zoomToCategoryValues:function(b,a){this.updateScrollbar=true;
this.zoom(this.getCategoryIndexByValue(b),this.getCategoryIndexByValue(a))
},destroySets:function(){AmCharts.AmSerialChart.base.destroySets.call(this);
if(this.categoryAxis){this.categoryAxis.set=null
}}});AmCharts.AmRadarChart=AmCharts.Class({inherits:AmCharts.AmCoordinateChart,construct:function(){AmCharts.AmRadarChart.base.construct.call(this);
this.chartType="radar";
this.radius="35%"},initChart:function(){AmCharts.AmRadarChart.base.initChart.call(this);
if(this.dataChanged){this.updateData();
this.dataChanged=false;
this.dispatchDataUpdated=true
}this.drawChart()},updateData:function(){this.parseData();
count=this.graphs.length;
for(i=0;i<count;i++){var a=this.graphs[i];
a.data=this.chartData
}},updateGraphs:function(){var b=this.graphs.length;
for(var a=0;a<b;a++){var c=this.graphs[a];
c.x=this.marginLeftReal+this.horizontalPosition;
c.y=this.marginTopReal+this.verticalPosition;
c.index=a;c.width=this.realRadius;
c.height=this.realRadius;
c.x=this.centerX;c.y=this.centerY;
c.chartType=this.chartType
}},parseData:function(){AmCharts.AmRadarChart.base.parseData.call(this);
this.parseSerialData()
},updateValueAxes:function(){for(var a=0;
a<this.valueAxes.length;
a++){var b=this.valueAxes[a];
b.axisRenderer=AmCharts.RadarAxisRenderer;
b.guideFillRenderer=AmCharts.RadarAxisGuideFillRenderer;
b.axisItemRenderer=AmCharts.RadarAxisItemRenderer;
b.x=this.centerX;b.y=this.centerY;
b.width=this.realRadius;
b.height=this.realRadius
}},drawChart:function(){AmCharts.AmRadarChart.base.drawChart.call(this);
this.realWidth=this.updateWidth();
this.realHeight=this.updateHeight();
this.centerX=this.realWidth/2;
this.centerY=this.realHeight/2;
this.realRadius=AmCharts.toCoordinate(this.radius,this.realWidth,this.realHeight);
this.updateValueAxes();
this.updateGraphs();var d=this.chartData;
if(d){if(d.length>0){for(var a=0;
a<this.valueAxes.length;
a++){var c=this.valueAxes[a];
c.zoom(0,d.length-1)}for(var a=0;
a<this.graphs.length;
a++){var b=this.graphs[a];
b.zoom(0,d.length-1)}}}this.bringLabelsToFront();
this.chartCreated=true;
if(this.dispatchDataUpdated){this.dispatchDataUpdated=false;
this.dispatchDataUpdatedEvent()
}this.drb()}});AmCharts.AxisBase=AmCharts.Class({construct:function(){this.dx=0;
this.dy=0;this.axisWidth;
this.axisThickness=1;
this.axisColor="#000000";
this.axisAlpha=1;this.tickLength=5;
this.gridCount=5;this.gridAlpha=0.2;
this.gridThickness=1;
this.gridColor="#000000";
this.dashLength=0;this.labelFrequency=1;
this.showFirstLabel=true;
this.showLastLabel=true;
this.fillColor="#FFFFFF";
this.fillAlpha=0;this.labelsEnabled=true;
this.labelRotation=0;
this.autoGridCount=false;
this.valueRollOverColor="#CC0000";
this.offset=0;this.guides=[];
this.visible=true;this.counter=0;
this.guides=[];this.inside=false
},zoom:function(b,a){this.start=b;
this.end=a;this.dataChanged=true;
this.draw()},draw:function(){this.allLabels=[];
this.counter=0;this.destroy();
this.rotate=this.chart.rotate;
this.set=this.chart.container.set();
var d=this.position;if(this.orientation=="horizontal"){if(d=="left"){d="bottom"
}if(d=="right"){d="top"
}}else{if(d=="bottom"){d="left"
}if(d=="top"){d="right"
}}this.position=d;this.axisLine=new this.axisRenderer(this);
var a=this.axisLine.axisWidth;
if(this.autoGridCount){var b;
if(this.orientation=="vertical"){b=a/35;
if(b<3){b=3}}else{b=a/75
}this.gridCount=b}this.axisWidth=a
},addGuide:function(a){this.guides.push(a)
},removeGuide:function(a){var c=this.guides.length;
for(var b=0;b<c;b++){if(this.guides[b]==a){this.guides.splice(b,1)
}}},handleGuideOver:function(d){clearTimeout(this.chart.hoverInt);
var b=this.guides[d];
var e=b.graphics.getBBox();
var a=e.x+e.width/2;var f=e.y+e.height/2;
var c=b.fillColor;if(c==undefined){c=b.lineColor
}this.chart.showBalloon(b.balloonText,c,true,a,f)
},handleGuideOut:function(a){this.chart.hideBalloon()
},destroy:function(){AmCharts.removeSet(this.set);
if(this.axisLine){AmCharts.removeSet(this.axisLine.set)
}}});AmCharts.ValueAxis=AmCharts.Class({inherits:AmCharts.AxisBase,construct:function(){this.createEvents("axisChanged","logarithmicAxisFailed");
AmCharts.ValueAxis.base.construct.call(this);
this.dataChanged=true;
this.gridCount=8;this.stackType="none";
this.position="left";
this.unitPosition="right";
this.integersOnly=false;
this.includeGuidesInMinMax=false;
this.includeHidden=false;
this.recalculateToPercents=false;
this.duration;this.durationUnits={DD:"d. ",hh:":",mm:":",ss:""};
this.scrollbar=false;
this.maxDecCount;this.baseValue=0;
this.radarCategoriesEnabled=true;
this.axisTitleOffset=10;
this.gridType="polygons";
this.useScientificNotation=false
},updateData:function(){if(this.gridCount<=0){this.gridCount=1
}this.data=this.chart.chartData;
if(this.chart.chartType!="xy"){this.stackGraphs("smoothedLine");
this.stackGraphs("line");
this.stackGraphs("column");
this.stackGraphs("step")
}if(this.recalculateToPercents){this.recalculate()
}if(this.synchronizationMultiplyer&&this.synchronizeWithAxis){this.foundGraphs=true
}else{this.foundGraphs=false;
this.getMinMax()}},draw:function(){AmCharts.ValueAxis.base.draw.call(this);
if(this.dataChanged==true){this.updateData();
this.dataChanged=false
}if(this.logarithmic){var N=this.getMin(0,this.data.length-1);
if(N<=0||this.minimum<=0){var m="logarithmicAxisFailed";
this.fire(m,{type:m});
return}}this.grid0=null;
var s;var L;var g=this.chart.dx;
var e=this.chart.dy;var I=false;
if(!isNaN(this.min)&&!isNaN(this.max)&&this.foundGraphs&&this.min!=Infinity&&this.max!=-Infinity){var B=this.labelFrequency;
var x=this.showFirstLabel;
var A=this.showLastLabel;
var b=1;var F=0;var Q=Math.round((this.max-this.min)/this.step)+1;
if(this.logarithmic==true){var h=Math.log(this.max)*Math.LOG10E-Math.log(this.minReal)*Math.LOG10E;
this.stepWidth=this.axisWidth/h;
if(h>2){Q=Math.ceil((Math.log(this.max)*Math.LOG10E))+1;
F=Math.round((Math.log(this.minReal)*Math.LOG10E));
if(Q>this.gridCount){b=Math.ceil(Q/this.gridCount)
}}}else{this.stepWidth=this.axisWidth/(this.max-this.min)
}var J=0;if(this.step<1&&this.step>-1){var o=this.step.toString();
if(o.indexOf("e-")!=-1){J=Number(o.split("-")[1])
}else{J=o.split(".")[1].length
}}if(this.integersOnly){J=0
}if(J>this.maxDecCount){J=this.maxDecCount
}this.max=AmCharts.roundTo(this.max,this.maxDecCount);
this.min=AmCharts.roundTo(this.min,this.maxDecCount);
var j={};j.precision=J;
j.decimalSeparator=this.chart.numberFormatter.decimalSeparator;
j.thousandsSeparator=this.chart.numberFormatter.thousandsSeparator;
this.numberFormatter=j;
if(this.guides.length>0){var H=this.guides.length;
var t=this.fillAlpha;
this.fillAlpha=0;for(L=0;
L<H;L++){var T=this.guides[L];
var z=NaN;if(!isNaN(T.toValue)){z=this.getCoordinate(T.toValue);
var a=new this.axisItemRenderer(this,z,"",true,NaN,NaN,T);
this.set.push(a.graphics())
}var R=NaN;if(!isNaN(T.value)){R=this.getCoordinate(T.value);
var U=(z-R)/2;var a=new this.axisItemRenderer(this,R,T.label,true,NaN,U,T);
this.set.push(a.graphics())
}if(!isNaN(z-R)){var y=new this.guideFillRenderer(this,z-R,R,T);
var K=y.graphics();this.set.push(K);
T.graphics=K;K.index=L;
var l=this;if(T.balloonText){K.mouseover(function(){l.handleGuideOver(this.index)
});K.mouseout(function(){l.handleGuideOut(this.index)
})}}}this.fillAlpha=t
}var v=false;var E=Number.MAX_VALUE;
for(L=F;L<Q;L+=b){var C=AmCharts.roundTo(this.step*L+this.min,J);
if(String(C).indexOf("e")!=-1){v=true;
var p=String(C).split("e");
var P=Number(p[1])}}if(this.duration){this.maxInterval=AmCharts.getMaxInterval(this.max,this.duration)
}for(L=F;L<Q;L+=b){var G=this.step*L+this.min;
G=AmCharts.roundTo(G,this.maxDecCount+1);
if(this.integersOnly&&Math.round(G)!=G){}else{if(this.logarithmic==true){if(G==0){G=this.minReal
}if(h>2){G=Math.pow(10,L)
}if(String(G).indexOf("e")!=-1){v=true
}else{v=false}}var q;
if(this.useScientificNotation){v=true
}if(this.usePrefixes){v=false
}if(!v){if(this.logarithmic){var k=String(G).split(".");
if(k[1]){j.precision=k[1].length
}else{j.precision=-1}}if(this.usePrefixes){q=AmCharts.addPrefix(G,this.chart.prefixesOfBigNumbers,this.chart.prefixesOfSmallNumbers,j)
}else{q=AmCharts.formatNumber(G,j,j.precision)
}}else{if(String(G).indexOf("e")==-1){q=G.toExponential(15)
}else{q=String(G)}var f=q.split("e");
var d=Number(f[0]);var c=Number(f[1]);
if(d==10){d=1;c+=1}q=d+"e"+c;
if(G==0){q="0"}if(G==1){q="1"
}}if(this.duration){q=AmCharts.formatDuration(G,this.duration,"",this.durationUnits,this.maxInterval,j)
}if(this.recalculateToPercents){q=q+"%"
}else{if(this.unit){if(this.unitPosition=="left"){q=this.unit+q
}else{q=q+this.unit}}}if(Math.round(L/B)!=L/B){q=undefined
}if((L==0&&!x)||(L==(Q-1)&&!A)){q=" "
}s=this.getCoordinate(G);
var a=new this.axisItemRenderer(this,s,q);
this.set.push(a.graphics());
if(G==this.baseValue&&this.chart.chartType!="radar"){var D;
var n;var M=this.visibleAxisWidth;
var O=this.visibleAxisHeight;
if(this.orientation=="horizontal"){if(s>=this.x&&s<=this.x+M+1){D=[s,s,s+g];
n=[0+O,0,e]}}else{if(s>=this.y&&s<=this.y+O+1){D=[0,M,M+g];
n=[s,s,s+e]}}if(D){this.grid0=AmCharts.line(this.chart.container,D,n,this.gridColor,this.gridAlpha*2,1,0);
this.set.push(this.grid0)
}}}}var u=this.baseValue;
if(this.min>this.baseValue&&this.max>this.baseValue){u=this.min
}if(this.min<this.baseValue&&this.max<this.baseValue){u=this.max
}if(this.logarithmic){u=this.minReal
}this.baseCoord=this.getCoordinate(u);
var r="axisChanged";var S={type:r};
if(this.logarithmic){S.min=this.minReal
}else{S.min=this.min}S.max=this.max;
this.fire(r,S);this.axisCreated=true
}else{I=true}if(this.chart.chartType!="radar"){if(this.rotate){this.set.translate(0+","+this.chart.marginTopReal)
}else{this.set.translate(this.chart.marginLeftReal+","+0)
}}else{this.axisLine.set.toFront()
}if(!this.visible||I){this.set.hide();
this.axisLine.set.hide()
}else{this.set.show();
this.axisLine.set.show()
}},stackGraphs:function(l){var g=[];
var b=[];var o=[];var h=[];
var m;var k=this.chart.graphs;
var e;var a;var n;var p;
var d;var f;if((l=="line"||l=="step"||l=="smoothedLine")&&(this.stackType=="regular"||this.stackType=="100%")){for(d=0;
d<k.length;d++){n=k[d];
if(!n.hidden){a=n.type;
if(n.chart==this.chart&&n.valueAxis==this&&l==a&&n.stackable){if(e){n.stackGraph=e;
e=n}else{e=n}}}}}for(f=this.start;
f<=this.end;f++){for(d=0;
d<k.length;d++){n=k[d];
if(!n.hidden){a=n.type;
if(n.chart==this.chart&&n.valueAxis==this&&l==a&&n.stackable){p=this.data[f].axes[this.id].graphs[n.id];
m=p.values.value;if(!isNaN(m)){if(isNaN(h[f])){h[f]=Math.abs(m)
}else{h[f]+=Math.abs(m)
}if(this.stackType=="regular"){if(l=="line"||l=="step"||l=="smoothedLine"){if(isNaN(g[f])){g[f]=m;
p.values.close=m}else{if(isNaN(m)){p.values.close=g[f]
}else{p.values.close=m+g[f]
}g[f]=p.values.close}}if(l=="column"){if(!isNaN(m)){p.values.close=m;
if(m<0){p.values.close=m;
if(!isNaN(b[f])){p.values.close+=b[f];
p.values.open=b[f]}b[f]=p.values.close
}else{p.values.close=m;
if(!isNaN(o[f])){p.values.close+=o[f];
p.values.open=o[f]}o[f]=p.values.close
}}}}}}}}}for(f=this.start;
f<=this.end;f++){for(d=0;
d<k.length;d++){n=k[d];
if(!n.hidden){a=n.type;
if(n.chart==this.chart&&n.valueAxis==this&&l==a&&n.stackable){p=this.data[f].axes[this.id].graphs[n.id];
m=p.values.value;if(!isNaN(m)){var c=m/h[f]*100;
p.values.percents=c;p.values.total=h[f];
if(this.stackType=="100%"){if(isNaN(b[f])){b[f]=0
}if(isNaN(o[f])){o[f]=0
}if(c<0){p.values.close=c+b[f];
p.values.open=b[f];b[f]=p.values.close
}else{p.values.close=c+o[f];
p.values.open=o[f];o[f]=p.values.close
}}}}}}}},recalculate:function(){var d=this.chart.graphs.length;
for(var g=0;g<d;g++){var q=this.chart.graphs[g];
if(q.valueAxis==this){var p="value";
if(q.type=="candlestick"||q.type=="ohlc"){p="open"
}var a;var r;var f=AmCharts.fitToBounds(this.end+1,0,this.data.length-1);
var b=this.start;if(b>0){b--
}for(var o=this.start;
o<=f;o++){r=this.data[o].axes[this.id].graphs[q.id];
a=r.values[p];if(!isNaN(a)){break
}}for(var h=b;h<=f;h++){r=this.data[h].axes[this.id].graphs[q.id];
r.percents={};var m=r.values;
for(var e in m){if(e!="percents"){var c=m[e];
var l=c/a*100-100;r.percents[e]=l
}else{r.percents[e]=m[e]
}}}}}},getMinMax:function(){var a=false;
for(var n=0;n<this.chart.graphs.length;
n++){var b=this.chart.graphs[n].type;
if(b=="line"||b=="step"||b=="smoothedLine"){if(this.expandMinMax){a=true
}}}if(a){if(this.start>0){this.start--
}if(this.end<this.data.length-1){this.end++
}}if(this.chart.chartType=="serial"){if(this.chart.categoryAxis.parseDates==true&&!a){if(this.end<this.data.length-1){this.end++
}}}this.min=this.getMin(this.start,this.end);
this.max=this.getMax();
var m=this.guides.length;
if(this.includeGuidesInMinMax&&m>0){for(var l=0;
l<m;l++){var t=this.guides[l];
if(t.toValue<this.min){this.min=t.toValue
}if(t.value<this.min){this.min=t.value
}if(t.toValue>this.max){this.max=t.toValue
}if(t.value>this.max){this.max=t.value
}}}if(!isNaN(this.minimum)){this.min=this.minimum
}if(!isNaN(this.maximum)){this.max=this.maximum
}if(this.min>this.max){var s=this.max;
this.max=this.min;this.min=s
}if(!isNaN(this.minTemp)){this.min=this.minTemp
}if(!isNaN(this.maxTemp)){this.max=this.maxTemp
}this.minReal=this.min;
this.maxReal=this.max;
if(this.min==0&&this.max==0){this.max=9
}if(this.min>this.max){this.min=this.max-1
}var f=this.min;var h=this.max;
var r=this.max-this.min;
var c;if(r==0){c=Math.pow(10,Math.floor(Math.log(Math.abs(this.max))*Math.LOG10E))/10
}else{c=Math.pow(10,Math.floor(Math.log(Math.abs(r))*Math.LOG10E))/10
}if(isNaN(this.maximum)&&isNaN(this.maxTemp)){this.max=Math.ceil(this.max/c)*c+c
}if(isNaN(this.minimum)&&isNaN(this.minTemp)){this.min=Math.floor(this.min/c)*c-c
}if(this.min<0&&f>=0){this.min=0
}if(this.max>0&&h<=0){this.max=0
}if(this.stackType=="100%"){if(this.min<0){this.min=-100
}else{this.min=0}if(this.max<0){this.max=0
}else{this.max=100}}r=this.max-this.min;
c=Math.pow(10,Math.floor(Math.log(Math.abs(r))*Math.LOG10E))/10;
this.step=Math.ceil((r/this.gridCount)/c)*c;
var k=Math.pow(10,Math.floor(Math.log(Math.abs(this.step))*Math.LOG10E));
var e=k.toExponential(0);
var q=e.split("e");var d=q[0];
var p=q[1];if(d==9){p++
}k=this.generateNumber(1,p);
var o=Math.ceil(this.step/k);
if(o>5){o=10}if(o<=5&&o>2){o=5
}this.step=Math.ceil(this.step/(k*o))*k*o;
if(k<1){this.maxDecCount=Math.abs(Math.log(Math.abs(k))*Math.LOG10E);
this.maxDecCount=Math.round(this.maxDecCount);
this.step=AmCharts.roundTo(this.step,this.maxDecCount+1)
}else{this.maxDecCount=0
}this.min=this.step*Math.floor(this.min/this.step);
this.max=this.step*Math.ceil(this.max/this.step);
if(this.min<0&&f>=0){this.min=0
}if(this.max>0&&h<=0){this.max=0
}if(this.minReal>1&&this.max-this.minReal>1){this.minReal=Math.floor(this.minReal)
}r=(Math.pow(10,Math.floor(Math.log(Math.abs(this.minReal))*Math.LOG10E)));
if(this.min==0){this.minReal=r
}if(this.min==0&&this.minReal>1){this.minReal=1
}if(this.min>0&&this.minReal-this.step>0){if(this.min+this.step<this.minReal){this.minReal=this.min+this.step
}else{this.minReal=this.min
}}var j=Math.log(h)*Math.LOG10E-Math.log(f)*Math.LOG10E;
if(this.logarithmic&&j>2){this.min=Math.pow(10,Math.floor(Math.log(Math.abs(f))*Math.LOG10E));
this.minReal=this.min;
this.max=Math.pow(10,Math.ceil(Math.log(Math.abs(h))*Math.LOG10E))
}},generateNumber:function(a,c){var d="";
var e;if(c<1){e=Math.abs(c)-1
}else{e=Math.abs(c)}for(var b=0;
b<e;b++){d=d+"0"}if(c<1){return Number("0."+d+String(a))
}else{return Number(String(a)+d)
}},getMin:function(a,d){var e;
for(var g=a;g<=d;g++){var h=this.data[g].axes[this.id].graphs;
for(var f in h){var m=this.chart.getGraphById(f);
if(m.includeInMinMax){if(!m.hidden||this.includeHidden){if(isNaN(e)){e=Infinity
}this.foundGraphs=true;
var l=h[f].values;if(this.recalculateToPercents){l=h[f].percents
}var b;if(this.minMaxField){b=l[this.minMaxField];
if(b<e){e=b}}else{for(var c in l){if(c!="percents"&&c!="total"){b=l[c];
if(b<e){e=b}}}}}}}}return e
},getMax:function(){var a;
for(var f=this.start;
f<=this.end;f++){var d=this.data[f].axes[this.id].graphs;
for(var e in d){var g=this.chart.getGraphById(e);
if(g.includeInMinMax){if(!g.hidden||this.includeHidden){if(isNaN(a)){a=-Infinity
}this.foundGraphs=true;
var c=d[e].values;if(this.recalculateToPercents){c=d[e].percents
}var h;if(this.minMaxField){h=c[this.minMaxField];
if(h>a){a=h}}else{for(var b in c){if(b!="percents"&&b!="total"){h=c[b];
if(h>a){a=h}}}}}}}}return a
},dispatchZoomEvent:function(b,a){var c=new ValueAxisEvent(ValueAxisEvent.AXIS_ZOOMED);
c.startValue=b;c.endValue=a;
dispatchEvent(c)},zoomToValues:function(b,a){},coordinateToValue:function(c){if(isNaN(c)){return NaN
}var a;if(this.logarithmic==true){var b;
if(this.chart.rotate){if(this.reversed==true){b=(this.axisWidth-c)/this.stepWidth
}else{b=c/this.stepWidth
}}else{if(this.reversed==true){b=c/this.stepWidth
}else{b=(this.axisWidth-c)/this.stepWidth
}}a=Math.pow(10,b+Math.log(this.minReal)*Math.LOG10E)
}else{if(this.reversed==true){if(this.chart.rotate){a=this.min-(c-this.axisWidth)/this.stepWidth
}else{a=c/this.stepWidth+this.min
}}else{if(this.chart.rotate){a=c/this.stepWidth+this.min
}else{a=this.min-(c-this.axisWidth)/this.stepWidth
}}}return a},getCoordinate:function(a){if(isNaN(a)){return NaN
}var c;if(this.logarithmic==true){var b=(Math.log(a)*Math.LOG10E)-Math.log(this.minReal)*Math.LOG10E;
if(this.chart.rotate){if(this.reversed==true){c=this.axisWidth-this.stepWidth*b
}else{c=this.stepWidth*b
}}else{if(this.reversed==true){c=this.stepWidth*b
}else{c=this.axisWidth-this.stepWidth*b
}}}else{if(this.reversed==true){if(this.chart.rotate){c=this.axisWidth-this.stepWidth*(a-this.min)
}else{c=this.stepWidth*(a-this.min)
}}else{if(this.chart.rotate){c=this.stepWidth*(a-this.min)
}else{c=this.axisWidth-this.stepWidth*(a-this.min)
}}}c=Math.round(c*10)/10;
if(this.rotate){c+=this.x
}else{c+=this.y}return c
},synchronizeWithAxis:function(a){this.synchronizeWithAxis=a;
this.removeListener(this.synchronizeWithAxis,"axisChanged",this.handleSynchronization);
this.listenTo(this.synchronizeWithAxis,"axisChanged",this.handleSynchronization)
},handleSynchronization:function(d){var e=this.synchronizeWithAxis.min;
var b=this.synchronizeWithAxis.max;
var c=this.synchronizeWithAxis.step;
if(this.synchronizationMultiplyer){this.min=e*this.synchronizationMultiplyer;
this.max=b*this.synchronizationMultiplyer;
this.step=c*this.synchronizationMultiplyer;
var a=Math.pow(10,Math.floor(Math.log(Math.abs(this.step))*Math.LOG10E));
this.maxDecCount=Math.abs(Math.log(Math.abs(a))*Math.LOG10E);
this.maxDecCount=Math.round(this.maxDecCount);
this.draw()}else{}}});
AmCharts.CategoryAxis=AmCharts.Class({inherits:AmCharts.AxisBase,construct:function(){AmCharts.CategoryAxis.base.construct.call(this);
this.minPeriod="DD";this.parseDates=false;
this.equalSpacing=false;
this.position="bottom";
this.startOnAxis=false;
this.gridPosition="middle";
this.periods=[{period:"ss",count:1},{period:"ss",count:5},{period:"ss",count:10},{period:"ss",count:30},{period:"mm",count:1},{period:"mm",count:5},{period:"mm",count:10},{period:"mm",count:30},{period:"hh",count:1},{period:"hh",count:3},{period:"hh",count:6},{period:"hh",count:12},{period:"DD",count:1},{period:"WW",count:1},{period:"MM",count:1},{period:"MM",count:2},{period:"MM",count:3},{period:"MM",count:6},{period:"YYYY",count:1},{period:"YYYY",count:2},{period:"YYYY",count:5},{period:"YYYY",count:10},{period:"YYYY",count:50},{period:"YYYY",count:100}];
this.dateFormats=[{period:"fff",format:"JJ:NN:SS"},{period:"ss",format:"JJ:NN:SS"},{period:"mm",format:"JJ:NN"},{period:"hh",format:"JJ:NN"},{period:"DD",format:"MMM DD"},{period:"MM",format:"MMM"},{period:"YYYY",format:"YYYY"}];
this.nextPeriod={};this.nextPeriod.fff="ss";
this.nextPeriod.ss="mm";
this.nextPeriod.mm="hh";
this.nextPeriod.hh="DD";
this.nextPeriod.DD="MM";
this.nextPeriod.MM="YYYY"
},draw:function(){AmCharts.CategoryAxis.base.draw.call(this);
this.generateDFObject();
this.data=this.chart.chartData;
if(this.data.length>0){var s=this.end;
var C=this.start;var I=this.labelFrequency;
var L=0;var l=s-C+1;var j=this.gridCount;
var y=this.showFirstLabel;
var F=this.showLastLabel;
var t;var n="";var o=AmCharts.extractPeriod(this.minPeriod);
var k=AmCharts.getPeriodDuration(o.period,o.count);
var H;var T;var E;var u;
var K;var z;var p;var e;
var C;var S;var Q;var r;
var B;var b;var Y=this.data[this.data.length-1].time;
var f=AmCharts.resetDateToMin(new Date(Y+k),this.minPeriod,1).getTime();
if(this.endTime>f){this.endTime=f
}if(this.parseDates&&!this.equalSpacing){this.timeDifference=this.endTime-this.startTime;
H=this.choosePeriod(0);
E=H.period;T=H.count;
u=AmCharts.getPeriodDuration(E,T);
if(u<k){E=o.period;T=o.count;
u=k}K=E;if(K=="WW"){K="DD"
}this.stepWidth=this.getStepWidth(this.timeDifference);
j=Math.ceil(this.timeDifference/u)+1;
z=AmCharts.resetDateToMin(new Date(this.startTime-u),E,T).getTime();
if(K==E&&T==1){p=u*this.stepWidth
}this.cellWidth=k*this.stepWidth;
e=Math.round(z/u);C=-1;
if(e/2==Math.round(e/2)){C=-2;
z-=u}if(this.gridCount>0){for(S=C;
S<=j;S++){Q=z+u*1.5;Q=AmCharts.resetDateToMin(new Date(Q),E,T).getTime();
t=(Q-this.startTime)*this.stepWidth;
if(this.rotate){t+=this.y
}else{t+=this.x}r=false;
if(this.nextPeriod[K]){r=this.checkPeriodChange(this.nextPeriod[K],1,Q,z)
}var c=false;if(r){B=this.dateFormatsObject[this.nextPeriod[K]];
c=true}else{B=this.dateFormatsObject[K]
}n=AmCharts.formatDate(new Date(Q),B);
if((S==C&&!y)||(S==j&&!F)){n=" "
}var a=new this.axisItemRenderer(this,t,n,false,p,0,false,c);
this.set.push(a.graphics());
z=Q}}}else{if(!this.parseDates){this.cellWidth=this.getStepWidth(l);
if(l<j){j=l}L+=this.start;
this.stepWidth=this.getStepWidth(l);
if(j>0){var M=Math.floor(l/j);
b=L;if(b/2==Math.round(b/2)){b--
}if(b<0){b=0}for(S=b;
S<=this.end+2;S+=M){if(S>=0&&S<this.data.length){var q=this.data[S];
n=q.category}else{n=""
}t=this.getCoordinate(S-L);
var h=0;if(this.gridPosition=="start"){t=t-this.cellWidth/2;
h=this.cellWidth/2}if((S==C&&!y)||(S==this.end&&!F)){n=" "
}if(Math.round(S/I)!=S/I){n=undefined
}var O=this.cellWidth;
if(this.rotate){O=NaN
}var a=new this.axisItemRenderer(this,t,n,true,O,h,undefined,false,h);
this.set.push(a.graphics())
}}}else{if(this.parseDates&&this.equalSpacing){L=this.start;
this.startTime=this.data[this.start].time;
this.endTime=this.data[this.end].time;
this.timeDifference=this.endTime-this.startTime;
H=this.choosePeriod(0);
E=H.period;T=H.count;
u=AmCharts.getPeriodDuration(E,T);
if(u<k){E=o.period;T=o.count;
u=k}K=E;if(K=="WW"){K="DD"
}this.stepWidth=this.getStepWidth(l);
j=Math.ceil(this.timeDifference/u)+1;
z=AmCharts.resetDateToMin(new Date(this.startTime-u),E,T).getTime();
this.cellWidth=this.getStepWidth(l);
e=Math.round(z/u);C=-1;
if(e/2==Math.round(e/2)){C=-2;
z-=u}var d=this.data.length;
b=this.start;if(b/2==Math.round(b/2)){b--
}if(b<0){b=0}var m=this.end+2;
if(m>=this.data.length){m=this.data.length
}var G=false;if(this.end-this.start>this.gridCount){G=true
}for(S=b;S<m;S++){Q=this.data[S].time;
if(this.checkPeriodChange(E,T,Q,z)){t=this.getCoordinate(S-this.start);
r=false;if(this.nextPeriod[K]){r=this.checkPeriodChange(this.nextPeriod[K],1,Q,z)
}var c=false;if(r){B=this.dateFormatsObject[this.nextPeriod[K]];
c=true}else{B=this.dateFormatsObject[K]
}n=AmCharts.formatDate(new Date(Q),B);
if((S==C&&!y)||(S==j&&!F)){n=" "
}if(!G){var a=new this.axisItemRenderer(this,t,n,undefined,undefined,undefined,undefined,c);
this.set.push(a.graphics())
}else{G=false}z=Q}}}}}for(S=0;
S<this.data.length;S++){var v=this.data[S];
if(v){var N;if(this.parseDates&&!this.equalSpacing){var J=v.time;
N=(J-this.startTime)*this.stepWidth+this.cellWidth/2;
if(this.rotate){N+=this.y
}else{N+=this.x}}else{N=this.getCoordinate(S-L)
}v.x[this.id]=N}}}var P=this.guides.length;
for(S=0;S<P;S++){var W=this.guides[S];
var D=NaN;var U=NaN;var X=NaN;
if(W.toCategory){var x=this.chart.getCategoryIndexByValue(W.toCategory);
if(!isNaN(x)){D=this.getCoordinate(x-L);
var a=new this.axisItemRenderer(this,D,"",true,NaN,NaN,W);
this.set.push(a.graphics())
}}if(W.category){var V=this.chart.getCategoryIndexByValue(W.category);
if(!isNaN(V)){U=this.getCoordinate(V-L);
X=(D-U)/2;var a=new this.axisItemRenderer(this,U,W.label,true,NaN,X,W)
}}if(W.toDate){if(this.equalSpacing){var x=this.chart.getClosestIndex(this.data,"time",W.toDate.getTime(),false,0,this.data.length-1);
if(!isNaN(x)){D=this.getCoordinate(x-L)
}}else{D=(W.toDate.getTime()-this.startTime)*this.stepWidth;
if(this.rotate){D+=this.y
}else{D+=this.x}}var a=new this.axisItemRenderer(this,D,"",true,NaN,NaN,W);
this.set.push(a.graphics())
}if(W.date){if(this.equalSpacing){var V=this.chart.getClosestIndex(this.data,"time",W.date.getTime(),false,0,this.data.length-1);
if(!isNaN(V)){U=this.getCoordinate(V-L)
}}else{U=(W.date.getTime()-this.startTime)*this.stepWidth;
if(this.rotate){U+=this.y
}else{U+=this.x}}X=(D-U)/2;
if(this.orientation=="horizontal"){var a=new this.axisItemRenderer(this,U,W.label,false,X*2,NaN,W)
}else{var a=new this.axisItemRenderer(this,U,W.label,false,NaN,X,W)
}}this.set.push(a.graphics());
var A=new this.guideFillRenderer(this,D-U,U,W);
var R=A.graphics();this.set.push(R);
W.graphics=R;R.index=S;
var g=this;if(W.balloonText){R.mouseover(function(){g.handleGuideOver(this.index)
});R.mouseout(function(){g.handleGuideOut(this.index)
})}}this.axisCreated=true;
if(this.rotate){this.set.translate(this.x+","+0)
}else{this.set.translate(0+","+this.y)
}if(this.axisLine.set){this.axisLine.set.toFront()
}},choosePeriod:function(b){var a=AmCharts.getPeriodDuration(this.periods[b].period,this.periods[b].count);
var c=Math.ceil(this.timeDifference/a);
if(c<=this.gridCount){return this.periods[b]
}else{if(b+1<this.periods.length){return this.choosePeriod(b+1)
}else{return this.periods[b]
}}},getStepWidth:function(a){var b;
if(this.startOnAxis){b=this.axisWidth/(a-1);
if(a==1){b=this.axisWidth
}}else{b=this.axisWidth/a
}return b},getCoordinate:function(a){var b=a*this.stepWidth;
if(!this.startOnAxis){b+=this.stepWidth/2
}if(this.rotate){b+=this.y
}else{b+=this.x}return b
},timeZoom:function(b,a){this.startTime=b;
this.endTime=a+this.minDuration()
},minDuration:function(){var a=AmCharts.extractPeriod(this.minPeriod);
return AmCharts.getPeriodDuration(a.period,a.count)
},checkPeriodChange:function(h,d,f,b){var a=new Date(f);
var g=new Date(b);var e=AmCharts.resetDateToMin(a,h,d).getTime();
var c=AmCharts.resetDateToMin(g,h,d).getTime();
if(e!=c){return true}else{return false
}},generateDFObject:function(){this.dateFormatsObject={};
for(var a=0;a<this.dateFormats.length;
a++){var b=this.dateFormats[a];
this.dateFormatsObject[b.period]=b.format
}},xToIndex:function(a){if(this.chart.rotate){a=a-this.y
}else{a=a-this.x}var b;
if(this.parseDates&&!this.equalSpacing){var d=this.startTime+Math.round(a/this.stepWidth)-this.minDuration()/2;
b=this.chart.getClosestIndex(this.data,"time",d,false,this.start,this.end+1)
}else{if(!this.startOnAxis){a-=this.stepWidth/2
}b=this.start+Math.round(a/this.stepWidth)
}b=AmCharts.fitToBounds(b,0,this.data.length-1);
var c;if(this.data[b]){c=this.data[b].x[this.id]
}if(this.chart.rotate){if(c>this.height+1+this.y){b--
}if(c<this.y){b++}}else{if(c>this.width+1+this.x){b--
}if(c<this.x){b++}}b=AmCharts.fitToBounds(b,0,this.data.length-1);
return b},dateToCoordinate:function(b){if(this.parseDates&&!this.equalSpacing){return(b.getTime()-this.startTime)*this.stepWidth
}else{if(this.parseDates&&this.equalSpacing){var a=this.chart.getClosestIndex(this.data,"time",b.getTime(),false,0,this.data.length-1);
return this.getCoordinate(a-_start)
}else{return NaN}}},categoryToCoordinate:function(b){if(this.chart){var a=this.chart.getCategoryIndexByValue(b);
return getCoordinate(a-this.start)
}else{return NaN}},coordinateToDate:function(a){return new Date(this.startTime+a/this.stepWidth)
}});AmCharts.RectangularAxisRenderer=AmCharts.Class({construct:function(d){var g=d.chart;
var s=d.axisThickness;
var j=d.axisColor;var q=d.axisAlpha;
var e=d.tickLength;var b=d.offset;
var v=d.dx;var r=d.dy;
var m=d.visibleAxisX;
var h=d.visibleAxisY;
var f=d.visibleAxisHeight;
var p=d.visibleAxisWidth;
var n;var k;this.set=g.container.set();
var u;if(d.orientation=="horizontal"){u=AmCharts.line(g.container,[0,p],[0,0],j,q,s);
this.axisWidth=d.width;
if(d.position=="bottom"){k=s/2+b+f+h-1;
n=m}else{k=-s/2-b+h+r;
n=v+m}}else{this.axisWidth=d.height;
if(d.position=="right"){u=AmCharts.line(g.container,[0,0,-v],[0,f,f-r],j,q,s);
k=h+r;n=s/2+b+v+p+m-1
}else{u=AmCharts.line(g.container,[0,0],[0,f],j,q,s);
k=h;n=-s/2-b+m}}this.set.push(u);
this.set.translate(Math.round(n)+","+Math.round(k))
}});AmCharts.RectangularAxisItemRenderer=AmCharts.Class({construct:function(A,t,R,I,p,ah,ad,c,y){if(R==undefined){R=""
}if(!y){y=0}if(I==undefined){I=true
}var a=A.chart.fontFamily;
var F=A.fontSize;if(F==undefined){F=A.chart.fontSize
}var E=A.color;if(E==undefined){E=A.chart.color
}var H=A.chart.container;
this.set=H.set();var K=3;
var X=4;var g=A.axisThickness;
var G=A.axisColor;var Z=A.axisAlpha;
var J=A.tickLength;var r=A.gridAlpha;
var aa=A.gridThickness;
var ab=A.gridColor;var l=A.dashLength;
var ae=A.fillColor;var s=A.fillAlpha;
var U=A.labelsEnabled;
var S=A.labelRotation;
var d=A.counter;var j=A.inside;
var f=A.dx;var e=A.dy;
var B=A.orientation;var O=A.position;
var L=A.previousCoord;
var Y=A.chart.rotate;
var h=A.autoTruncate;
var n=A.visibleAxisX;
var m=A.visibleAxisY;
var x=A.visibleAxisHeight;
var o=A.visibleAxisWidth;
var N=A.offset;var b;
var Q;if(ad){U=true;if(!isNaN(ad.tickLength)){J=ad.tickLength
}if(ad.lineColor!=undefined){ab=ad.lineColor
}if(!isNaN(ad.lineAlpha)){r=ad.lineAlpha
}if(!isNaN(ad.dashLength)){l=ad.dashLength
}if(!isNaN(ad.lineThickness)){aa=ad.lineThickness
}if(ad.inside==true){j=true
}}else{if(!R){r=r/3;J=J/2
}}var T="start";if(p){T="middle"
}var u=S*Math.PI/180;
var z;var k;var D=0;var C=0;
var W=0;var V=0;var M=0;
var P=0;var ag=(n+f)+","+(m+e)+","+o+","+x;
if(B=="vertical"){S=0
}if(U){var af=AmCharts.text(H,0,0,R,{fill:E,"text-anchor":T,"font-family":a,"font-size":F,rotation:-S});
if(c==true){af.attr({"font-weight":"bold"})
}this.set.push(af);var v=af.getBBox();
M=v.width;P=v.height}if(B=="horizontal"){if(t>=n&&t<=o+1+n){b=AmCharts.line(H,[t+y,t+y],[0,J],G,Z,aa);
this.set.push(b);if(t+y>o+n){b.hide()
}Q=AmCharts.line(H,[t,t+f,t+f],[x,x+e,e],ab,r,aa,l);
this.set.push(Q)}C=0;
D=t;if(I==false){T="start";
if(!Y){if(O=="bottom"){if(j){C+=J
}else{C-=J}}else{if(j){C-=J
}else{C+=J}}D+=3;if(p){D+=p/2;
T="middle"}}}else{T="middle"
}if(d==1&&s>0&&!ad){z=t-L;
fill=AmCharts.rect(H,z,A.height,[ae],[s]);
fill.translate((t-z+f)+","+e);
fill.attr({"clip-rect":ag});
this.set.push(fill)}if(O=="bottom"){C+=x+F/2+N;
if(j){C-=J+F+K+K;if(S>0){D+=(M/2)*Math.cos(u);
C-=(M/2)*Math.sin(u)-(P/2)*Math.sin(u)
}}else{C+=J+g+K+3;if(S>0){D-=(M/2)*Math.cos(u);
C+=(M/2)*Math.sin(u)-(P/2)*Math.cos(u);
if(S==90){C-=8;if(AmCharts.isNN){D+=1
}else{D+=3}}}}}else{C+=e+F/2-N;
D+=f;if(j){C+=J+K;if(S>0){D-=(M/2)*Math.cos(u);
C+=(M/2)*Math.sin(u)-((P/2))*Math.sin(u)+3
}}else{C-=J+F+K+g+3;if(S>0){D+=(M/2)*Math.cos(u);
C-=(M/2)*Math.sin(u)-((P/2))*Math.sin(u)+3
}}}if(O=="bottom"){if(j){V=x-J-1
}else{V=x+g-1}V+=N}else{W=f;
if(j){V=e}else{V=e-J-g+1
}V-=N}if(ah){D+=ah}var ac=D;
if(S>0){ac+=(M/2)*Math.cos(u)
}if(af){if(ac>n+o||ac<n){af.hide()
}}}else{if(t>=m&&t<=x+1+m){b=AmCharts.line(H,[0,J],[t+y,t+y],G,Z,aa);
this.set.push(b);if(t+y>x+m){b.hide()
}Q=AmCharts.line(H,[0,f,o+f],[t,t+e,t+e],ab,r,aa,l);
this.set.push(Q)}T="end";
if((j==true&&O=="left")||(j==false&&O=="right")){T="start"
}C=t-F/2;if(d==1&&s>0&&!ad){k=t-L;
fill=AmCharts.rect(H,A.width,k,[ae],[s]);
fill.translate(f+","+(t-k+e));
fill.attr({"clip-rect":ag});
this.set.push(fill)}C+=F/2;
if(O=="right"){D+=f+o+N;
C+=e;if(j==true){D-=J+X;
if(!ah){C-=F/2+3}}else{D+=J+X+g;
C-=2}}else{if(j==true){D+=J+X-N;
if(!ah){C-=F/2+3}if(ad){D+=f;
C+=e}}else{D+=-J-g-X-2-N;
C-=2}}if(b){if(O=="right"){W+=f+N+o;
V+=e;if(j==true){W-=g
}else{W+=g}}else{W-=N;
if(j==true){}else{W-=J+g
}}}if(ah){C+=ah}var q=m-3;
if(O=="right"){q+=e}if(af){if(C>x+m||C<q){af.hide()
}}}if(b){b.translate(W+","+V)
}if(af){af.attr({"text-anchor":T});
af.translate(D+","+C);
A.allLabels.push(af)}if(A.visible==false){if(b){b.hide()
}if(af){af.hide()}}if(d==0){A.counter=1
}else{A.counter=0}A.previousCoord=t
},graphics:function(){return this.set
}});AmCharts.RectangularAxisGuideFillRenderer=AmCharts.Class({construct:function(g,k,j,f){var d=g.orientation;
var e=0;var c=f.fillAlpha;
var a=g.chart.container;
var m=g.dx;var l=g.dy;
if(isNaN(k)){k=4;e=2;
c=0}var b=f.fillColor;
if(b==undefined){b="#000000"
}if(k<0){if(typeof(b)=="object"){b=b.join(",").split(",").reverse()
}}if(isNaN(c)){c=0}var h=(g.visibleAxisX+m)+","+(g.visibleAxisY+l)+","+g.visibleAxisWidth+","+g.visibleAxisHeight;
if(d=="vertical"){this.fill=AmCharts.rect(a,g.width,k,b,c);
this.fill.translate(m+","+(j-e+l))
}else{this.fill=AmCharts.rect(a,k,g.height,b,c);
this.fill.translate((j-e+m)+","+l)
}this.fill.attr({"clip-rect":h})
},graphics:function(){return this.fill
}});AmCharts.RadarAxisRenderer=AmCharts.Class({construct:function(d){var p=d.chart;
var o=d.axisThickness;
var D=d.axisColor;var E=d.axisAlpha;
var s=d.tickLength;var m=d.x;
var h=d.y;this.set=p.container.set();
var k=d.labelsEnabled;
var F=d.axisTitleOffset;
var C=d.radarCategoriesEnabled;
var B=d.chart.fontFamily;
var q=d.fontSize;if(q==undefined){q=d.chart.fontSize
}var u=d.color;if(u==undefined){u=d.chart.color
}if(p){this.axisWidth=d.height;
var G=p.chartData;var f=G.length;
for(var z=0;z<f;z++){var A=180-360/f*z;
var e=m+this.axisWidth*Math.sin((A)/(180)*Math.PI);
var r=h+this.axisWidth*Math.cos((A)/(180)*Math.PI);
var n=AmCharts.line(p.container,[m,e],[h,r],D,E,o);
this.set.push(n);if(C){var v="start";
var j=m+(this.axisWidth+F)*Math.sin((A)/(180)*Math.PI);
var g=h+(this.axisWidth+F)*Math.cos((A)/(180)*Math.PI);
if(A==180||A==0){v="middle";
j=j-5}if(A<0){v="end";
j=j-10}if(A==180){g-=5
}if(A==0){g+=5}var H=AmCharts.text(p.container,j+5,g,G[z].category,{fill:u,"text-anchor":v,"font-family":B,"font-size":q});
this.set.push(H);var b=H.getBBox()
}}}}});AmCharts.RadarAxisItemRenderer=AmCharts.Class({construct:function(p,n,I,A,j,Y,V){if(I==undefined){I=""
}var a=p.chart.fontFamily;
var t=p.fontSize;if(t==undefined){t=p.chart.fontSize
}var s=p.color;if(s==undefined){s=p.chart.color
}var z=p.chart.container;
this.set=z.set();var C=3;
var O=4;var e=p.axisThickness;
var v=p.axisColor;var R=p.axisAlpha;
var B=p.tickLength;var k=p.gridAlpha;
var T=p.gridThickness;
var U=p.gridColor;var g=p.dashLength;
var W=p.fillColor;var l=p.fillAlpha;
var N=p.labelsEnabled;
var L=p.labelRotation;
var d=p.counter;var f=p.inside;
var G=p.position;var D=p.previousCoord;
var c=p.gridType;n-=p.height;
var b;var H;var F=p.x;
var E=p.y;var r=0;var q=0;
if(V){N=true;if(!isNaN(V.tickLength)){B=V.tickLength
}if(V.lineColor!=undefined){U=V.lineColor
}if(!isNaN(V.lineAlpha)){k=V.lineAlpha
}if(!isNaN(V.dashLength)){g=V.dashLength
}if(!isNaN(V.lineThickness)){T=V.lineThickness
}if(V.inside==true){f=true
}}else{if(!I){k=k/3;B=B/2
}}var M="end";var S=-1;
if(f){M="start";S=1}if(N){var X=AmCharts.text(z,F+(B+3)*S,n,I,{fill:s,"text-anchor":M,"font-family":a,"font-size":t});
this.set.push(X);var b=AmCharts.line(z,[F,F+B*S],[n,n],v,R,T);
this.set.push(b)}var J=p.y-n;
if(c=="polygons"){var u=[];
var h=[];var K=p.data.length;
for(var P=0;P<K;P++){var o=180-360/K*P;
u.push(J*Math.sin((o)/(180)*Math.PI));
h.push(J*Math.cos((o)/(180)*Math.PI))
}u.push(u[0]);h.push(h[0]);
H=AmCharts.line(z,u,h,U,k,T,g)
}else{H=AmCharts.circle(z,J,0,0,T,U,k)
}this.set.push(H);H.translate(F+","+E);
if(d==1&&l>0&&!V){var Q=p.previousCoord;
var m;if(c=="polygons"){for(P=K;
P>=0;P--){o=180-360/K*P;
u.push(Q*Math.sin((o)/(180)*Math.PI));
h.push(Q*Math.cos((o)/(180)*Math.PI))
}m=AmCharts.polygon(z,u,h,[W],[l])
}else{m=AmCharts.wedge(z,0,0,0,-360,J,J,Q,0,{fill:W,"fill-opacity":l,stroke:0,"stroke-opacity":0,"stroke-width":0})
}this.set.push(m);m.translate(F+","+E)
}if(p.visible==false){if(b){b.hide()
}if(X){X.hide()}}if(d==0){p.counter=1
}else{p.counter=0}p.previousCoord=J
},graphics:function(){return this.set
}});AmCharts.RadarAxisGuideFillRenderer=AmCharts.Class({construct:function(g,p,o,f){var c=g.chart.container;
var e=f.fillAlpha;var d=f.fillColor;
var l=g.y-(o-g.height)-p;
var n=l+p;var h=-f.angle;
var b=-f.toAngle;if(isNaN(h)){h=0
}if(isNaN(b)){b=-360}this.set=c.set();
if(d==undefined){d="#000000"
}if(isNaN(e)){e=0}if(g.gridType=="polygons"){var a=[];
var m=[];var k=g.data.length;
for(var j=0;j<k;j++){var h=180-360/k*j;
a.push(l*Math.sin((h)/(180)*Math.PI));
m.push(l*Math.cos((h)/(180)*Math.PI))
}a.push(a[0]);m.push(m[0]);
for(j=k;j>=0;j--){h=180-360/k*j;
a.push(n*Math.sin((h)/(180)*Math.PI));
m.push(n*Math.cos((h)/(180)*Math.PI))
}this.fill=AmCharts.polygon(c,a,m,[d],[e])
}else{var n=l-Math.abs(p);
this.fill=AmCharts.wedge(c,0,0,h,(b-h),l,l,n,0,{fill:d,"fill-opacity":e,stroke:0,"stroke-opacity":0,"stroke-width":0})
}this.set.push(this.fill);
this.fill.translate(g.x+","+g.y)
},graphics:function(){return this.fill
}});AmCharts.AmGraph=AmCharts.Class({construct:function(){this.createEvents("rollOverGraphItem","rollOutGraphItem","clickGraphItem","doubleClickGraphItem");
this.type="line";this.stackable=true;
this.columnCount=1;this.columnIndex=0;
this.showBalloon=true;
this.centerCustomBullets=true;
this.maxBulletSize=50;
this.balloonText="[[value]]";
this.animationPlayed=false;
this.scrollbar=false;
this.hidden=false;this.columnWidth=0.8;
this.pointPosition="middle";
this.depthCount=1;this.includeInMinMax=true;
this.negativeBase=0;this.visibleInLegend=true;
this.showAllValueLabels=false;
this.showBalloonAt="close";
this.lineThickness=1;
this.dashLength=0;this.connect=true;
this.lineAlpha=1;this.bullet="none";
this.bulletBorderThickness=2;
this.bulletBorderAlpha=1;
this.bulletAlpha=1;this.bulletSize=8;
this.bulletOffset=0;this.hideBulletsCount=0;
this.labelPosition="top";
this.cornerRadiusTop=0;
this.cursorBulletAlpha=1;
this.gradientOrientation="vertical"
},draw:function(){var a=this;
this.container=this.chart.container;
this.destroy();this.set=this.container.set();
this.ownColumns=[];this.allBullets=[];
this.objectsToAddListeners=[];
if(this.data){if(this.data.length>0&&this.valueAxis.axisCreated){this.columnsArray=[];
if(!this.hidden){this.createGraph()
}}}},createGraph:function(){if(this.labelPosition=="inside"){this.labelPosition="bottom"
}this.sDur=this.chart.startDuration;
this.sEff=this.chart.startEffect;
this.startAlpha=this.chart.startAlpha;
this.seqAn=this.chart.sequencedAnimation;
this.baseCoord=this.valueAxis.baseCoord;
if(!this.fillColors){this.fillColors=[this.lineColor]
}if(this.fillAlphas==undefined){this.fillAlphas=0
}if(this.bulletColor==undefined){this.bulletColor=this.lineColor;
this.bulletColorNegative=this.negativeLineColor
}if(this.bulletAlpha==undefined){this.bulletAlpha=this.lineAlpha
}if(!this.bulletBorderColor){this.bulletBorderAlpha=0
}if(!isNaN(this.valueAxis.min)&&!isNaN(this.valueAxis.max)){this.positiveObjectsToClip=[];
this.negativeObjectsToClip=[];
this.animationArray=[];
switch(this.chartType){case"serial":this.createSerialGraph();
break;case"radar":this.createRadarGraph();
break;case"xy":this.createXYGraph();
break}this.animationPlayed=true
}},createRadarGraph:function(){var m=this.valueAxis.stackType;
var a=[];var p=[];var e;
var d;for(var k=this.start;
k<=this.end;k++){var n=this.data[k];
var t=n.axes[this.valueAxis.id].graphs[this.id];
var s;if(m=="none"||m=="3d"){s=t.values.value
}else{s=t.values.close
}if(isNaN(s)){this.drawLineGraph(a,p);
a=[];p=[]}else{var l=this.y-(this.valueAxis.getCoordinate(s)-this.height);
var f=180-360/(this.end-this.start+1)*k;
var b=(l*Math.sin((f)/(180)*Math.PI));
var r=(l*Math.cos((f)/(180)*Math.PI));
a.push(b);p.push(r);var h=this.createBullet(t,b,r,k);
if(!h){h=0}if(this.labelText){var q=this.createLabel(t,b,r);
this.positionLabel(q,this.labelPosition,h)
}if(isNaN(e)){e=b}if(isNaN(d)){d=r
}}}a.push(e);p.push(d);
this.drawLineGraph(a,p);
this.set.translate(this.x+","+this.y);
this.launchAnimation();
var c=this.objectsToAddListeners;
if(c){for(var g=0;g<c.length;
g++){this.addHoverListeners(c[g])
}}},positionLabel:function(c,f,b){var a=0;
var e=0;var d=c.getBBox();
switch(f){case"left":a=-((d.width+b)/2+5);
break;case"top":e=-((b+d.height)/2+3);
break;case"right":a=(d.width+b)/2+5;
break;case"bottom":e=(b+d.height)/2+3;
break}c.translate(a+","+e)
},createSerialGraph:function(){if(typeof(this.fillAlphas)=="object"){this.fillAlphas=this.fillAlphas[0]
}if(typeof(this.negativefillAlphas)=="object"){this.negativefillAlphas=this.negativefillAlphas[0]
}var at=this;var Z=this.columnWidth;
var d=this.valueAxis.getCoordinate(this.valueAxis.min);
if(this.valueAxis.logarithmic){d=this.valueAxis.getCoordinate(this.valueAxis.minReal)
}this.minCoord=d;this.pmh=this.height+1;
this.pmw=this.width+1;
this.pmx=this.x;this.pmy=this.y;
if(this.resetBullet){this.bullet="none"
}if(!this.scrollbar&&(this.type=="line"||this.type=="smoothedLine"||this.type=="step")){if(this.data.length==1&&this.type!="step"&&this.bullet=="none"){this.bullet="round";
this.resetBullet=true
}if(this.negativeFillColors||this.negativeLineColor!=undefined){var ap=this.negativeBase;
if(ap>this.valueAxis.max){ap=this.valueAxis.max
}if(ap<this.valueAxis.min){ap=this.valueAxis.min
}if(this.valueAxis.logarithmic){ap=this.valueAxis.minReal
}var g=this.valueAxis.getCoordinate(ap);
var a=this.valueAxis.getCoordinate(this.valueAxis.max);
if(this.rotate){this.pmh=this.height;
this.pmw=Math.abs(a-g);
this.nmh=this.height;
this.nmw=Math.abs(d-g);
this.nmx=this.y;this.nmy=this.y;
if(this.valueAxis.reversed){this.pmx=this.x;
this.nmx=g}else{this.pmx=g;
this.nmx=this.x}}else{this.pmw=this.width;
this.pmh=Math.abs(a-g);
this.nmw=this.width;this.nmh=Math.abs(d-g);
this.pmx=this.x;this.nmx=this.x;
if(this.valueAxis.reversed){this.nmy=this.y;
this.pmy=g}else{this.nmy=g
}}}}var aj=this.chart.columnSpacing;
var aL=this.categoryAxis.cellWidth;
var J=(aL*Z-this.columnCount)/this.columnCount;
if(aj>J){aj=J}if(this.type=="column"){Z=(aL*Z-(aj*(this.columnCount-1)))/this.columnCount
}else{Z=aL*Z}if(Z<1){Z=1
}var V=this.cornerRadiusTop;
var ar=AmCharts.toCoordinate(V,Z/2);
var aF=this.connect;var aA=[];
var am=[];var aD;var f;
var ae=this.chart.graphs.length;
var aC;var n=this.dx/this.depthCount;
var l=this.dy/this.depthCount;
var an=this.valueAxis.stackType;
var I=this.labelPosition;
if(I=="above"){I="top"
}if(I=="below"){I="bottom"
}var ag;var q=270;if(this.gradientOrientation=="horizontal"){q=0
}var Y;var ah;var G;var R;
if(this.type=="line"||this.type=="step"||this.type=="smoothedLine"){if(this.start>0){for(R=this.start-1;
R>-1;R--){Y=this.data[R];
ah=Y.axes[this.valueAxis.id].graphs[this.id];
G=ah.values.value;if(G){this.start=R;
break}}}if(this.end<this.data.length-1){for(R=this.end+1;
R<this.data.length;R++){Y=this.data[R];
ah=Y.axes[this.valueAxis.id].graphs[this.id];
G=ah.values.value;if(G){this.end=R;
break}}}}if(this.end<this.data.length-1){this.end++
}for(R=this.start;R<=this.end;
R++){Y=this.data[R];ah=Y.axes[this.valueAxis.id].graphs[this.id];
ah.index=R;var z="";if(ah.url){z="pointer"
}var k=NaN;var x=NaN;
var K=NaN;var aI=NaN;
var aq=NaN;var h=NaN;
var m=NaN;var ai=NaN;
var T=NaN;var aJ=NaN;
var aH=NaN;var al=NaN;
var ak=NaN;var S=NaN;
var aB=undefined;var e=this.fillColors;
if(ah.color!=undefined){e=[ah.color]
}if(ah.fillColors){e=ah.fillColors
}var L=this.fillAlphas;
if(!isNaN(ah.alpha)){L=[ah.alpha]
}var ac;var ab;var b;
var af;var ao;var y=ah.values;
if(this.valueAxis.recalculateToPercents){y=ah.percents
}if(!this.stackable||an=="none"||an=="3d"){S=y.value
}else{S=y.close}if(this.type=="candlestick"||this.type=="ohlc"){S=y.close;
var aK=y.low;m=this.valueAxis.getCoordinate(aK);
var o=y.high;T=this.valueAxis.getCoordinate(o)
}var M=y.open;K=this.valueAxis.getCoordinate(S);
if(!isNaN(M)){aq=this.valueAxis.getCoordinate(M)
}if(!this.scrollbar){if(this.showBalloonAt=="close"){ah.y=K
}if(this.showBalloonAt=="open"){ah.y=aq
}if(this.showBalloonAt=="high"){ah.y=T
}if(this.showBalloonAt=="low"){ah.y=m
}}k=Y.x[this.categoryAxis.id];
var P=aL/2;var O=aL/2;
if(this.pointPosition=="start"){k-=aL/2;
P=0;O=aL}if(!this.scrollbar){ah.x=k
}if(this.rotate){x=K;
aI=aq;K=k;aq=k;if(isNaN(M)){aI=this.baseCoord
}h=m;ai=T}else{x=k;aI=k;
if(isNaN(M)){aq=this.baseCoord
}}switch(this.type){case"line":if(!isNaN(S)){if(S<this.negativeBase){ah.isNegative=true
}aA.push(x);am.push(K);
aJ=x;aH=K;al=x;ak=K}else{if(!aF){this.drawLineGraph(aA,am);
aA=[];am=[]}}break;case"smoothedLine":if(!isNaN(S)){if(S<this.negativeBase){ah.isNegative=true
}aA.push(x);am.push(K);
aJ=x;aH=K;al=x;ak=K}else{if(!aF){this.drawSmoothedGraph(aA,am);
aA=[];am=[]}}break;case"step":if(!isNaN(S)){if(S<this.negativeBase){ah.isNegative=true
}if(this.rotate){if(aD&&aF){aA.push(aD);
am.push(K-P)}am.push(K-P);
aA.push(x);am.push(K+O);
aA.push(x)}else{if(f&&aF){am.push(f);
aA.push(x-P)}aA.push(x-P);
am.push(K);aA.push(x+O);
am.push(K)}aD=x;f=K;aJ=x;
aH=K;al=x;ak=K}else{if(!aF){this.drawLineGraph(aA,am);
aA=[];am=[]}}break;case"column":var aB;
if(!isNaN(S)){ac=e;b=this.lineColor;
if(S<this.negativeBase){ah.isNegative=true;
if(this.negativeFillColors){ac=this.negativeFillColors
}if(this.negativeLineColor!=undefined){b=this.negativeLineColor
}}var aG=this.valueAxis.min;
var s=this.valueAxis.max;
if((S<aG&&(M<aG||M==undefined))||(S>s&&M>s)){}else{if(this.rotate){if(an=="3d"){var A=K-0.5*(Z+aj)+aj/2+l*this.columnIndex;
var B=aI+n*this.columnIndex
}else{var A=K-(this.columnCount/2-this.columnIndex)*(Z+aj)+aj/2;
var B=aI}var C=Z;aJ=x;
aH=A+Z/2;al=x;ak=A+Z/2;
if(A+C>this.y+this.height){C=this.y+this.height-A
}if(A<this.y){C-=this.y-A;
A=this.y}var H=x-aI;var au=B;
B=AmCharts.fitToBounds(B,this.x,this.x+this.width);
H=H+(au-B);H=AmCharts.fitToBounds(H,this.x-B,this.x+this.width-B);
if(A<this.y+this.height&&C>0){aB=new AmCharts.Cuboid(this.container,H,C,n,l,ac,L,this.lineThickness,b,this.lineAlpha,q,ar);
aB.y(A);aB.x(B);if(I!="bottom"){I="right";
if(S<0){I="left"}else{aJ+=this.dx;
if(an!="regular"&&an!="100%"){aH+=this.dy
}}}}}else{I="top";if(an=="3d"){var B=x-0.5*(Z+aj)+aj/2+n*this.columnIndex;
var A=aq+l*this.columnIndex
}else{var B=x-(this.columnCount/2-this.columnIndex)*(Z+aj)+aj/2;
var A=aq}var C=Z;aJ=B+Z/2;
aH=K;al=B+Z/2;ak=K;if(B+C>this.x+this.width+this.columnIndex*n){C=this.x+this.width-B+this.columnIndex*n
}if(B<this.x){C-=this.x-B;
B=this.x}var H=K-aq;var ad=A;
A=AmCharts.fitToBounds(A,this.y,this.y+this.height);
H=H+(ad-A);H=AmCharts.fitToBounds(H,this.y-A,this.y+this.height-A);
if(B<this.x+this.width+this.columnIndex*n&&C>0){aB=new AmCharts.Cuboid(this.container,C,H,n,l,ac,L,this.lineThickness,b,this.lineAlpha,q,ar);
aB.y(A);aB.x(B);if(S<0){I="bottom"
}else{if(an!="regular"&&an!="100%"){aJ+=this.dx
}aH+=this.dy}}}}if(aB){if(!this.scrollbar){if(an=="none"){if(this.rotate){aC=(this.end+1-R)*ae-this.index
}else{aC=ae*R+this.index
}}if(an=="3d"){aC=(ae-this.index)*(R+1);
if(this.rotate){aH=A+Z/2;
aH+=l*this.columnIndex
}else{aJ+=n*this.columnIndex;
aH+=l*this.columnIndex
}}if(an=="regular"||an=="100%"){I="middle";
if(this.rotate){if(y.value>0){aC=(this.end+1-R)*ae+this.index
}else{aC=(this.end+1-R)*ae-this.index
}}else{if(y.value>0){aC=(ae*R)+this.index
}else{aC=ae*R-this.index
}}}this.columnsArray.push({column:aB,depth:aC});
if(this.rotate){ah.x=aB.getY()+C/2
}else{ah.x=aB.getX()+C/2
}this.ownColumns.push(aB);
if(this.dx==0&&this.dy==0){if(this.sDur>0&&!this.animationPlayed){if(this.rotate){af=x-aI;
pFinalDimension=x;pInitialDimension=aI
}else{af=K-aq;pFinalDimension=K;
pInitialDimension=aq}if(this.seqAn){aB.set.hide();
this.animationArray.push({obj:aB.set,fh:af,ip:pInitialDimension,fp:pFinalDimension});
ag=setTimeout(function(){at.animate.call(at)
},this.sDur/(this.end-this.start+1)*(R-this.start)*1000);
this.timeOuts.push(ag)
}else{this.animate(aB.set,af,pInitialDimension,pFinalDimension)
}}}var aM=aB.set;for(var Q=0;
Q<aM.length;Q++){aM[Q].dItem=ah;
aM[Q].attr({cursor:z})
}this.objectsToAddListeners.push(aB.set)
}this.set.push(aB.set);
ah.columnSprite=aM}}break;
case"candlestick":if(!isNaN(M)&&!isNaN(o)&&!isNaN(aK)&&!isNaN(S)){var W;
var az;ac=e;b=this.lineColor;
ab=this.fillAlphas;if(S<M){ah.isNegative=true;
if(this.negativeFillColors){ac=this.negativeFillColors
}if(this.negativeFillAlphas){ab=this.negativeFillAlphas
}if(this.negativeLineColor!=undefined){b=this.negativeLineColor
}}if(this.rotate){var A=K-Z/2;
var B=aI;var C=Z;if(A+C>this.y+this.height){C=this.y+this.height-A
}if(A<this.y){C-=this.y-A;
A=this.y}if(A<this.y+this.height&&C>0){var N;
var ay;if(S>M){N=[x,ai];
ay=[aI,h]}else{N=[aI,ai];
ay=[x,h]}if(K<this.y+this.height&&K>this.y){W=AmCharts.line(this.container,N,[K,K],b,this.lineAlpha,this.lineThickness);
az=AmCharts.line(this.container,ay,[K,K],b,this.lineAlpha,this.lineThickness)
}if(Math.abs(x-aI)<1){aB=new AmCharts.line(this.container,[0,0],[0,C],b,this.lineAlpha,1);
aB.translate(B+","+A)
}else{aB=new AmCharts.Cuboid(this.container,x-aI,C,n,l,ac,L,this.lineThickness,b,this.lineAlpha,q,ar);
aB.y(A);aB.x(B)}}}else{var B=x-Z/2;
var A=aq+this.lineThickness/2;
var C=Z;if(B+C>this.x+this.width){C=this.x+this.width-B
}if(B<this.x){C-=this.x-B;
B=this.x}if(B<this.x+this.width&&C>0){if(Math.abs(K-aq)<1){aB=new AmCharts.line(this.container,[0,C],[0,0],b,this.lineAlpha,1);
aB.translate(B+","+A)
}else{aB=new AmCharts.Cuboid(this.container,C,K-aq,n,l,ac,ab,this.lineThickness,b,this.lineAlpha,q,ar);
aB.x(B);aB.y(A)}var aE;
var D;if(S>M){aE=[K,T];
D=[aq,m]}else{aE=[aq,T];
D=[K,m]}if(x<this.x+this.width&&x>this.x){W=AmCharts.line(this.container,[x,x],aE,b,this.lineAlpha,this.lineThickness);
az=AmCharts.line(this.container,[x,x],D,b,this.lineAlpha,this.lineThickness)
}}}if(aB){if(aB.set){this.set.push(aB.set)
}else{this.set.push(aB)
}if(W){this.set.push(W);
this.set.push(az)}aJ=x;
aH=K;al=x;ak=K;if(!this.scrollbar){if(aB.getX){var X=aB.getX();
var U=aB.getY()}else{var X=B;
var U=A}if(this.rotate){ah.x=U+C/2
}else{ah.x=X+C/2}if(this.dx==0&&this.dy==0){if(this.sDur>0&&!this.animationPlayed){if(this.rotate){af=x-aI;
pFinalDimension=x;pInitialDimension=aI
}else{af=K-aq;pFinalDimension=K;
pInitialDimension=aq}if(this.seqAn){aB.set.show();
this.animationArray.push({obj:aB.set,fh:af,ip:pInitialDimension,fp:pFinalDimension});
ag=setTimeout(function(){at.animate.call(at)
},this.sDur/(this.end-this.start+1)*(R-this.start)*1000);
this.timeOuts.push(ag)
}else{this.animate(aB.set,af,pInitialDimension,pFinalDimension)
}}}if(aB.set){var aM=aB.set;
for(var Q=0;Q<aM.length;
Q++){aM[Q].dItem=ah;aM[Q].attr({cursor:z})
}this.objectsToAddListeners.push(aB.set)
}}}}break;case"ohlc":if(!isNaN(M)&&!isNaN(o)&&!isNaN(aK)&&!isNaN(S)){b=this.lineColor;
if(S<M){ah.isNegative=true;
if(this.negativeLineColor!=undefined){b=this.negativeLineColor
}}var u;var p;var t;if(this.rotate){p=AmCharts.line(this.container,[aI,aI],[K-Z/2,K],b,this.lineAlpha,this.lineThickness,this.dashLength);
u=AmCharts.line(this.container,[h,ai],[K,K],b,this.lineAlpha,this.lineThickness,this.dashLength);
t=AmCharts.line(this.container,[x,x],[K,K+Z/2],b,this.lineAlpha,this.lineThickness,this.dashLength)
}else{p=AmCharts.line(this.container,[x-Z/2,x],[aq,aq],b,this.lineAlpha,this.lineThickness,this.dashLength);
u=AmCharts.line(this.container,[x,x],[m,T],b,this.lineAlpha,this.lineThickness,this.dashLength);
t=AmCharts.line(this.container,[x,x+Z/2],[K,K],b,this.lineAlpha,this.lineThickness,this.dashLength)
}this.set.push(p);this.set.push(u);
this.set.push(t);aJ=x;
aH=K;al=x;ak=K}break}if(!this.scrollbar&&!isNaN(S)){if(this.end-this.start<=this.hideBulletsCount||this.hideBulletsCount==0){var r=this.createBullet(ah,al,ak,R);
if(!r){r=0}if(this.labelText){var aa=this.createLabel(ah,aJ,aH);
if(this.type=="column"){if(this.rotate){if(I=="right"||I=="bottom"){aa.attr({width:this.width})
}else{aa.attr({width:x-aI})
}}else{aa.attr({width:aL})
}}var ax=0;var av=0;var F=NaN;
var E=NaN;var aw=aa.getBBox();
var c=aw.width;var v=aw.height;
switch(I){case"left":ax=-(c/2+r/2+3);
break;case"top":av=-(v/2+r/2+3);
break;case"right":ax=r/2+2+c/2;
break;case"bottom":if(this.rotate&&this.type=="column"){if(S<0){F=aI-c/2-7
}else{F=aI+6+c/2}}else{av=r/2+v/2;
aa.x=-(c/2+2)}break;case"middle":if(this.type=="column"){if(this.rotate){F=(x-aI)/2+aI;
if(Math.abs(x-aI)<c){if(!this.showAllValueLabels){aa.remove()
}}}else{E=(K-aq)/2+aq+1;
if(Math.abs(K-aq)<v){if(!this.showAllValueLabels){aa.remove()
}}}}break}if(!isNaN(F)){aa.attr({x:F})
}if(!isNaN(E)){aa.attr({y:E})
}aa.translate(ax+","+av);
aw=aa.getBBox();if(aw.x<this.x||aw.y<this.y||aw.x+aw.width>this.x+this.width||aw.y+aw.height>this.y+this.height){aa.remove()
}}}}}if(this.type=="line"||this.type=="step"||this.type=="smoothedLine"){if(this.type=="smoothedLine"){this.drawSmoothedGraph(aA,am)
}else{this.drawLineGraph(aA,am)
}if(!this.scrollbar){this.launchAnimation()
}}},createLabel:function(c,d,b){var h=this.numberFormatter;
if(!h){h=this.chart.numberFormatter
}var a=this.color;if(a==undefined){a=this.chart.color
}var f=this.fontSize;
if(f==undefined){f=this.chart.fontSize
}var g=this.chart.formatString(this.labelText,c);
var e=AmCharts.text(this.container,d,b,g,{fill:a,"font-family":this.chart.fontFamily,"font-size":f});
this.set.push(e);this.allBullets.push(e);
return e},setPositiveClipRect:function(a){a.attr({"clip-rect":this.pmx+","+this.pmy+","+this.pmw+","+this.pmh})
},setNegativeClipRect:function(a){a.attr({"clip-rect":this.nmx+","+this.nmy+","+this.nmw+","+this.nmh})
},drawLineGraph:function(a,e){if(a.length>1){var k=AmCharts.line(this.container,a,e,this.lineColor,this.lineAlpha,this.lineThickness,this.dashLength);
this.positiveObjectsToClip.push(k);
this.set.push(k);if(this.negativeLineColor!=undefined){var d=AmCharts.line(this.container,a,e,this.negativeLineColor,this.lineAlpha,this.lineThickness,this.dashLength);
this.negativeObjectsToClip.push(d);
this.set.push(d)}if(this.fillAlphas!=undefined&&this.fillAlphas!=0){var b=a.join(";").split(";");
var h=e.join(";").split(";");
if(this.chartType=="serial"){if(this.rotate){h.push(h[h.length-1]);
b.push(this.baseCoord);
h.push(h[0]);b.push(this.baseCoord);
h.push(h[0]);b.push(b[0])
}else{b.push(b[b.length-1]);
h.push(this.baseCoord);
b.push(b[0]);h.push(this.baseCoord);
b.push(a[0]);h.push(h[0])
}}var j=AmCharts.polygon(this.container,b,h,this.fillColors,this.fillAlphas);
this.set.push(j);this.positiveObjectsToClip.push(j);
if(this.negativeFillColors||this.negativeLineColor!=undefined){var g=this.fillAlphas;
if(this.negativeFillAlphas){g=this.negativeFillAlphas
}var f=this.negativeLineColor;
if(this.negativeFillColors){f=this.negativeFillColors
}var c=AmCharts.polygon(this.container,b,h,f,g);
this.set.push(c);this.negativeObjectsToClip.push(c)
}}}},drawSmoothedGraph:function(a,d){if(a.length>1){var j=new AmCharts.Bezier(this.container,a,d,this.lineColor,this.lineAlpha,this.lineThickness,NaN,NaN,this.dashLength);
this.positiveObjectsToClip.push(j.path);
this.set.push(j.path);
if(this.negativeLineColor!=undefined){var c=new AmCharts.Bezier(this.container,a,d,this.negativeLineColor,this.lineAlpha,this.lineThickness,NaN,NaN,this.dashLength);
this.set.push(c.path);
this.negativeObjectsToClip.push(c.path)
}if(this.fillAlphas>0){var e=[];
if(this.rotate){e.push("L",this.baseCoord,d[d.length-1]);
e.push("L",this.baseCoord,d[0]);
e.push("L",a[0],d[0])
}else{e.push("L",a[a.length-1],this.baseCoord);
e.push("L",a[0],this.baseCoord);
e.push("L",a[0],d[0])
}var h=new AmCharts.Bezier(this.container,a,d,NaN,NaN,0,this.fillColors,this.fillAlphas,this.dashLength,e);
this.positiveObjectsToClip.push(h.path);
this.set.push(h.path);
if(this.negativeFillColors||this.negativeLineColor!=undefined){var g=this.fillAlphas;
if(this.negativeFillAlphas){g=this.negativeFillAlphas
}var f=this.negativeLineColor;
if(this.negativeFillColors){f=this.negativeFillColors
}var b=new AmCharts.Bezier(this.container,a,d,NaN,NaN,0,f,g,this.dashLength,e);
this.negativeObjectsToClip.push(b.path);
this.set.push(b.path)
}}}},launchAnimation:function(){if(this.sDur>0&&!this.animationPlayed){this.set.attr({opacity:this.startAlpha});
if(this.rotate){this.set.translate((-1000)+","+0)
}else{this.set.translate(0+","+(-1000))
}if(this.seqAn){var b=this;
var a=setTimeout(function(){b.animateGraphs.call(b)
},this.index*this.sDur*1000);
this.timeOuts.push(a)
}else{this.animateGraphs()
}}},animateGraphs:function(){if(this.set.length>0){if(this.rotate){this.set.animate({opacity:1,translation:(1000+","+0)},this.sDur*1000,this.sEff)
}else{this.set.animate({opacity:1,translation:(0+","+1000)},this.sDur*1000,this.sEff)
}}},animate:function(d,a,e,b){var c=this.animationArray;
if(!d&&c.length>0){d=c[0].obj;
a=c[0].fh;e=c[0].ip;b=c[0].fp;
c.shift()}d.show();if(this.rotate){if(a>0){d.attr({"fill-opacity":this.startAlpha,width:1});
d.animate({"fill-opacity":this.fillAlphas,width:Math.abs(a)},this.sDur*1000,this.sEff)
}else{if(a<0){d.attr({"fill-opacity":this.startAlpha,width:1,x:e});
d.animate({"fill-opacity":this.fillAlphas,width:Math.abs(a),x:b},this.sDur*1000,this.sEff)
}}}else{if(a>0){d.attr({"fill-opacity":this.startAlpha,height:0.1});
d.animate({"fill-opacity":this.fillAlphas,height:Math.abs(a)},this.sDur*1000,this.sEff)
}else{if(a<0){d.attr({"fill-opacity":this.startAlpha,height:0.1,y:e});
d.animate({"fill-opacity":this.fillAlphas,height:Math.abs(a),y:b},this.sDur*1000,this.sEff)
}}}},legendKeyColor:function(){var a=this.legendColor;
var b=this.lineAlpha;
if(a==undefined){a=this.lineColor;
if(b==0){var c=this.fillColors;
if(c){if(typeof(c)=="object"){a=c[0]
}else{a=c}}}}return a
},legendKeyAlpha:function(){var a=this.legendAlpha;
if(a==undefined){a=this.lineAlpha;
if(a==0){if(this.fillAlphas){a=this.fillAlphas
}}}return a},createBullet:function(q,p,o,f){var s=this;
var j="";if(q.url){j="pointer"
}var l=this.bulletOffset;
var b=this.bulletSize;
if(!isNaN(q.bulletSize)){b=q.bulletSize
}if(!isNaN(this.maxValue)){var n=q.values.value;
if(!isNaN(n)){b=n/this.maxValue*this.maxBulletSize
}}var r;if(this.bullet=="none"&&!q.bullet){}else{var g=this.bulletColor;
if(q.isNegative&&this.bulletColorNegative!=undefined){g=this.bulletColorNegative
}if(q.color!=undefined){g=q.color
}var m=this.bullet;if(q.bullet){m=q.bullet
}var c=this.bulletBorderThickness;
var h=this.bulletBorderColor;
var k=this.bulletBorderAlpha;
var d=g;var e=this.bulletAlpha;
switch(m){case"round":r=AmCharts.circle(this.container,b/2,d,e,c,h,k);
break;case"square":r=AmCharts.rect(this.container,b,b,d,e,c,h,k);
r.translate(-b/2+","+(-b/2));
break;case"triangleUp":r=AmCharts.triangle(this.container,b,0,d,e,c,h,k);
break;case"triangleDown":r=AmCharts.triangle(this.container,b,180,d,e,c,h,k);
break;case"triangleLeft":r=AmCharts.triangle(this.container,b,270,d,e,c,h,k);
break;case"triangleRight":r=AmCharts.triangle(this.container,b,90,d,e,c,h,k);
break;case"bubble":r=AmCharts.circle(this.container,b/2,d,e,c,h,k,true);
break}if(r){r.translate(p+","+o)
}}if(this.customBullet||q.customBullet){var a=this.customBullet;
if(q.customBullet){a=q.customBullet
}if(a){if(this.chart.path){a=this.chart.path+a
}r=this.container.image(a,p,o,b,b).attr({preserveAspectRatio:true});
if(this.centerCustomBullets){r.translate(-b/2+","+(-b/2))
}}}if(r){r.attr({cursor:j});
if(this.rotate){r.translate(l+","+0)
}else{r.translate(0+","+(-l))
}this.allBullets.push(r);
this.set.push(r);if(this.chartType=="serial"){if(p<this.x||p>this.x+this.width||o<this.y||o>this.y+this.height){r.remove();
r=null}}if(r){r.dItem=q;
this.objectsToAddListeners.push(r)
}}return b},showBullets:function(){for(var a=0;
a<this.allBullets.length;
a++){this.allBullets[a].show()
}},hideBullets:function(){for(var a=0;
a<this.allBullets.length;
a++){this.allBullets[a].hide()
}},addHoverListeners:function(a){var b=this;
a.mouseover(function(){b.handleRollOver.call(b,this.dItem)
}).mouseout(function(){b.handleRollOut.call(b,this.dItem)
})},addClickListeners:function(a){var b=this;
if(this.chart.touchEventsEnabled){a.touchstart(function(){b.handleRollOver(this.dItem)
}).touchend(function(){b.handleClick(this.dItem)
})}a.click(function(){b.handleClick.call(b,this.dItem)
}).dblclick(function(){b.handleDoubleClick.call(b,this.dItem)
})},handleRollOver:function(e){if(e){var b="rollOverGraphItem";
var c={type:b,item:e,index:e.index,graph:this};
this.fire(b,c);this.chart.fire(b,c);
clearTimeout(this.chart.hoverInt);
var d=this.chart.formatString(this.balloonText,e);
var a=this.chart.getBalloonColor(this,e);
this.chart.balloon.showBullet=false;
this.chart.balloon.pointerOrientation="down";
this.chart.showBalloon(d,a,true)
}},handleRollOut:function(c){if(c){var a="rollOutGraphItem";
var b={type:a,item:c,index:c.index,graph:this};
this.fire(a,b);this.chart.fire(a,b);
this.chart.hideBalloon()
}},handleClick:function(c){if(c){var a="clickGraphItem";
var b={type:a,item:c,index:c.index,graph:this};
this.fire(a,b);this.chart.fire(a,b);
if(c.url){if(this.urlTarget=="_self"||!this.urlTarget){window.location.href=c.url
}else{window.open(c.url)
}}}},handleDoubleClick:function(c){if(c){var a="doubleClickGraphItem";
var b={type:a,item:c,index:c.index,graph:this};
this.fire(a,b);this.chart.fire(a,b)
}},zoom:function(b,a){this.start=b;
this.end=a;this.draw()
},changeOpacity:function(b){if(this.set){this.set.attr({opacity:b})
}},destroy:function(){AmCharts.removeSet(this.set);
if(this.timeOuts){for(var a=0;
a<this.timeOuts.length;
a++){clearTimeout(this.timeOuts[a])
}}this.timeOuts=[]}});
AmCharts.ChartCursor=AmCharts.Class({construct:function(){this.createEvents("changed","zoomed");
this.cursorAlpha=1;this.selectionAlpha=0.2;
this.cursorColor="#CC0000";
this.categoryBalloonAlpha=1;
this.color="#FFFFFF";
this.type="cursor";this.zoomed=false;
this.inside=false;this.dx=0;
this.dy=0;this.rotate=false;
this.zoomable=true;this.pan=false;
this.animate=true;this.categoryBalloonDateFormat="MMM DD, YYYY";
this.valueBalloonsEnabled=true;
this.categoryBalloonEnabled=true;
this.rolledOver=false;
this.cursorPosition="middle";
this.skipZoomDispatch=false;
this.bulletsEnabled=false;
this.bulletSize=8},draw:function(){this.destroy();
var c=this;this.container=this.chart.container;
this.set=this.container.set();
var a=new AmCharts.AmBalloon();
a.cornerRadius=0;a.borderWidth=1;
a.borderAlpha=0;this.categoryBalloon=a;
this.categoryBalloon.chart=this.chart;
this.data=this.chart.chartData;
this.dx=this.chart.dx;
this.dy=this.chart.dy;
this.rotate=this.chart.rotate;
this.categoryAxis=this.chart.categoryAxis;
this.allBullets=this.container.set();
this.interval=setInterval(function(){c.detectMovement.call(c)
},20);var b=this.categoryBalloonColor;
if(b==undefined){b=this.cursorColor
}this.categoryBalloon.fillColor=b;
this.categoryBalloon.fillAlpha=this.categoryBalloonAlpha;
this.categoryBalloon.borderColor=b;
this.categoryBalloon.color=this.color;
if(this.rotate){this.categoryBalloon.pointerOrientation="horizontal"
}if(this.type=="cursor"){this.createCursor()
}else{this.createCrosshair()
}},updateData:function(){this.data=this.chart.chartData;
if(this.data){if(this.data.length>0){if(this.data){this.firstTime=this.data[0].time;
this.lastTime=this.data[this.data.length-1].time
}}}},createCursor:function(){var a=this.cursorAlpha;
this.categoryBalloonPosition=this.categoryAxis.position;
this.inside=this.categoryAxis.inside;
this.axisThickness=this.categoryAxis.axisThickness;
var c;var d;if(this.rotate){c=[0,this.width,this.width+this.dx];
d=[0,0,this.dy]}else{c=[this.dx,0,0];
d=[this.dy,0,this.height]
}this.line=AmCharts.line(this.container,c,d,this.cursorColor,a,1);
this.line.translate(this.x+","+this.y);
this.set.push(this.line);
var b=this.categoryAxis.tickLength;
this.categoryBalloon.pointerWidth=b;
if(this.rotate){if(this.inside){this.categoryBalloon.pointerWidth=0
}if(this.categoryBalloonPosition=="right"){if(this.inside){this.categoryBalloon.setBounds(this.x,this.y+this.dy,this.x+this.width+this.dx,this.y+this.height+this.dy)
}else{this.categoryBalloon.setBounds(this.x+this.width+this.dx+this.axisThickness,this.y+this.dy,this.x+this.width+1000,this.y+this.height+this.dy)
}}else{if(this.inside){this.categoryBalloon.setBounds(this.x,this.y,this.width+this.x,this.height+this.y)
}else{this.categoryBalloon.setBounds(-1000,-1000,this.x-b-this.axisThickness,this.y+this.height+15)
}}}else{this.categoryBalloon.maxWidth=this.width;
if(this.categoryAxis.parseDates){b=0;
this.categoryBalloon.pointerWidth=0
}if(this.categoryBalloonPosition=="top"){if(this.inside){this.categoryBalloon.setBounds(this.x+this.dx,this.y+this.dy,this.width+this.dx+this.x,this.height+this.y)
}else{this.categoryBalloon.setBounds(this.x+this.dx,-1000,this.width+this.dx+this.x,this.y+this.dy-b-this.axisThickness)
}}else{if(this.inside){this.categoryBalloon.setBounds(this.x,this.y,this.width+this.x,this.height+this.y-b)
}else{this.categoryBalloon.setBounds(this.x,this.y+this.height+b+this.axisThickness-1,this.x+this.width,this.y+this.height+b+this.axisThickness)
}}}this.hideCursor()},createCrosshair:function(){},detectMovement:function(){if(this.chart.mouseX>this.x&&this.chart.mouseX<this.x+this.width&&this.chart.mouseY>this.y&&this.chart.mouseY<this.height+this.y){if(this.pan){if(!this.rolledOver){this.chart.setMouseCursor("move")
}}this.rolledOver=true;
this.setPosition()}else{if(this.rolledOver){this.handleMouseOut();
this.rolledOver=false
}}},getMousePosition:function(){var a;
if(this.rotate){a=this.chart.mouseY;
if(a<this.y){a=this.y
}if(a>this.height+this.y){a=this.height+this.y
}}else{a=this.chart.mouseX;
if(a<this.x){a=this.x
}if(a>this.width+this.x){a=this.width+this.x
}}return a},updateCrosshair:function(){},updateSelectionHeight:function(b){if(this.selection){this.selection.remove()
}var c;var a;if(this.selectionPosY>b){c=b;
a=this.selectionPosY-b
}if(this.selectionPosY<b){c=this.selectionPosY;
a=b-this.selectionPosY
}if(this.selectionPosY==b){c=b;
a=0}if(a>0){this.selection=AmCharts.rect(this.container,this.width,a,[this.cursorColor],[this.selectionAlpha]);
this.selection.translate(this.x+","+c);
this.set.push(this.selection)
}},updateSelectionWidth:function(c){if(this.selection){this.selection.remove()
}var a;var b;if(this.selectionPosX>c){a=c;
b=this.selectionPosX-c
}if(this.selectionPosX<c){a=this.selectionPosX;
b=c-this.selectionPosX
}if(this.selectionPosX==c){a=c;
b=0}if(b>0){this.selection=AmCharts.rect(this.container,b,this.height,[this.cursorColor],[this.selectionAlpha]);
this.selection.translate(a+","+this.y);
this.set.push(this.selection)
}},arrangeBalloons:function(){this.valueBalloons.sort(this.compareY);
var c=this.valueBalloons.length;
var a=this.y+this.height;
for(var b=0;b<c;b++){var d=this.valueBalloons[b].balloon;
d.setBounds(this.x,this.y,this.x+this.width,a);
d.draw();a=d.yPos-3}this.arrangeBalloons2()
},compareY:function(d,c){if(d.yy<c.yy){return 1
}else{return -1}},arrangeBalloons2:function(){this.valueBalloons.reverse();
var f=this.valueBalloons.length;
var a;var e;for(var d=0;
d<f;d++){var g=this.valueBalloons[d].balloon;
a=g.bottom;var c=g.bottom-g.yPos;
if(d>0){if(a-c<e+3){g.setBounds(this.x,e+3,this.x+this.width,e+c+3);
g.draw()}}if(g.set){g.set.show()
}e=g.bottomCoordinate
}},showBullets:function(){this.allBullets.remove();
for(var d=0;d<this.chart.graphs.length;
d++){var g=this.chart.graphs[d];
if(g.showBalloon&&!g.hidden&&g.balloonText){var e=this.data[this.index];
var h=e.axes[g.valueAxis.id].graphs[g.id];
var f=h.y;if(!isNaN(f)){var b;
var c;var j;b=h.x;if(this.rotate){c=f;
j=b}else{c=b;j=f}var a=AmCharts.circle(this.container,this.bulletSize/2,this.chart.getBalloonColor(g,h),g.cursorBulletAlpha);
a.translate(c+","+j);
this.allBullets.push(a);
this.set.push(a)}}}},destroy:function(){this.clear();
if(this.categoryBalloon){this.categoryBalloon.destroy()
}this.destroyValueBalloons();
AmCharts.removeSet(this.set)
},clear:function(){clearInterval(this.interval)
},destroyValueBalloons:function(){if(this.valueBalloons){for(var a=0;
a<this.valueBalloons.length;
a++){this.valueBalloons[a].balloon.destroy()
}}},zoom:function(e,a,c,b){this.destroyValueBalloons();
this.zooming=false;if(this.rotate){currentMouse=this.chart.mouseY;
this.selectionPosY=currentMouse
}else{currentMouse=this.chart.mouseX;
this.selectionPosX=currentMouse
}this.start=e;this.end=a;
this.startTime=c;this.endTime=b;
this.zoomed=true;if(this.categoryAxis.parseDates&&!this.categoryAxis.equalSpacing){var d=this.endTime-this.startTime+this.categoryAxis.minDuration();
if(this.rotate){this.stepWidth=this.height/d
}else{this.stepWidth=this.width/d
}}else{if(this.rotate){this.stepWidth=this.height/(this.end-this.start)
}else{this.stepWidth=this.width/(this.end-this.start)
}}this.setPosition();
this.hideCursor()},hideCursor:function(a){this.set.hide();
this.categoryBalloon.hide();
this.destroyValueBalloons();
this.allBullets.remove();
this.previousIndex=NaN;
if(a){}},setPosition:function(a,c){if(c==undefined){c=true
}if(this.type=="cursor"){if(this.data.length>0){if(!a){a=this.getMousePosition()
}if(a!=this.previousMousePosition||this.zoomed==true){if(!isNaN(a)){var b=this.categoryAxis.xToIndex(a);
if(b!=this.previousIndex||this.zoomed||this.cursorPosition=="mouse"){this.updateCursor(b,c);
this.zoomed=false}}}this.previousMousePosition=a
}}else{this.updateCrosshair()
}},updateCursor:function(f,x){if(x==undefined){x=true
}this.index=f;var t=this.data[this.index];
var e=t.x[this.categoryAxis.id];
if(this.panning){var u;
if(this.rotate){u=this.panClickPos-this.chart.mouseY
}else{u=this.panClickPos-this.chart.mouseX
}var p=u/this.stepWidth;
if(!this.categoryAxis.parseDates||this.categoryAxis.equalSpacing){p=Math.round(p)
}if(p!=0){if(this.categoryAxis.parseDates&&!this.categoryAxis.equalSpacing){if(this.panClickEndTime+p>this.lastTime){p=this.lastTime-this.panClickEndTime
}if(this.panClickStartTime+p<this.firstTime){p=this.firstTime-this.panClickStartTime
}var a={};a.type="zoomed";
a.start=this.panClickStartTime+p;
a.end=this.panClickEndTime+p;
this.fire("zoomed",a)
}else{if(this.panClickEnd+p>=this.data.length||this.panClickStart+p<0){}else{var a={};
a.type="zoomed";a.start=this.panClickStart+p;
a.end=this.panClickEnd+p;
this.fire("zoomed",a)
}}}}else{if(this.cursorPosition=="start"){e-=this.categoryAxis.cellWidth/2
}if(this.cursorPosition=="mouse"){if(this.rotate){e=this.chart.mouseY-2
}else{e=this.chart.mouseX-2
}}if(this.rotate){if(e<this.y){if(this.zooming){e=this.y
}else{this.hideCursor();
return}}if(e>this.height+1+this.y){if(this.zooming){e=this.height+1+this.y
}else{this.hideCursor();
return}}}else{if(e<this.x){if(this.zooming){e=this.x
}else{this.hideCursor();
return}}if(e>this.width+this.x){if(this.zooming){e=this.width+this.x
}else{this.hideCursor();
return}}}if(this.cursorAlpha>0){var b=this.line.getBBox();
if(this.rotate){this.line.translate(0+","+Math.round((e-b.y+this.dy)))
}else{this.line.translate(Math.round((e-b.x))+","+0)
}this.line.show()}if(this.rotate){this.linePos=e+this.dy
}else{this.linePos=e}if(this.zooming){if(this.rotate){this.updateSelectionHeight(e)
}else{this.updateSelectionWidth(e)
}}var g=true;if(this.chart.touchEventsEnabled&&this.zooming){g=false
}if(this.categoryBalloonEnabled&&g){t=this.data[this.index];
if(this.rotate){if(this.inside){if(this.categoryBalloonPosition=="right"){this.categoryBalloon.setBounds(this.x,this.y+this.dy,this.x+this.width+this.dx,this.x+e+this.dy)
}else{this.categoryBalloon.setBounds(this.x,this.y+this.dy,this.x+this.width+this.dx,this.x+e)
}}var l=this.x+this.dx;
var k=this.y+this.dy;
if(this.categoryBalloonPosition=="right"){if(this.inside){this.categoryBalloon.setPosition(this.x+this.width+this.dx,e+this.dy)
}else{this.categoryBalloon.setPosition(this.x+this.width+this.dx+this.axisThickness,e+this.dy)
}}else{if(this.inside){this.categoryBalloon.setPosition(this.x,e)
}else{this.categoryBalloon.setPosition(this.x-this.axisThickness,e)
}}}else{if(this.categoryBalloonPosition=="top"){if(this.inside){this.categoryBalloon.setPosition(e+this.dx,this.y+this.dy)
}else{this.categoryBalloon.setPosition(e+this.dx,this.y+this.dy-this.axisThickness+1)
}}else{if(this.inside){this.categoryBalloon.setPosition(e,this.y+this.height)
}else{this.categoryBalloon.setPosition(e,this.y+this.height+this.axisThickness-1)
}}}if(this.categoryAxis.parseDates){var c=AmCharts.formatDate(t.category,this.categoryBalloonDateFormat);
if(c.indexOf("fff")!=-1){c=AmCharts.formatMilliseconds(c,t.category)
}this.categoryBalloon.showBalloon(c)
}else{this.categoryBalloon.showBalloon(t.category)
}}else{this.categoryBalloon.hide()
}if(this.chart.graphs&&this.bulletsEnabled){this.showBullets()
}this.destroyValueBalloons();
if(this.chart.graphs&&this.valueBalloonsEnabled&&g&&this.chart.balloon.enabled){this.valueBalloons=[];
for(var q=0;q<this.chart.graphs.length;
q++){var d=this.chart.graphs[q];
if(d.showBalloon&&!d.hidden&&d.balloonText){t=this.data[this.index];
var r=t.axes[d.valueAxis.id].graphs[d.id];
var n=r.y;if(!isNaN(n)){var o;
var m;var s;o=r.x;var j=true;
if(this.rotate){m=n;s=o;
if(s<this.y||s>this.y+this.height){j=false
}}else{m=o;s=n;if(m<this.x||m>this.x+this.width){j=false
}}if(j){var v=this.chart.getBalloonColor(d,r);
var h=new AmCharts.AmBalloon();
h.chart=this.chart;this.copyBalloonProperties(h);
h.setBounds(this.x,this.y,this.x+this.width,this.y+this.height);
h.pointerOrientation="horizontal";
h.changeColor(v);if(d.balloonAlpha!=undefined){h.fillAlpha=d.balloonAlpha
}if(d.balloonTextColor!=undefined){h.color=d.balloonTextColor
}h.setPosition(m,s);balloonText=this.chart.formatString(d.balloonText,r);
if(balloonText!=""){h.showBalloon(balloonText)
}if(!this.rotate&&h.set){h.set.hide()
}this.valueBalloons.push({yy:n,balloon:h})
}}}}if(!this.rotate){this.arrangeBalloons()
}}if(x){var y="changed";
var a={type:y};a.index=this.index;
a.zooming=this.zooming;
if(this.rotate){a.position=this.chart.mouseY
}else{a.position=this.chart.mouseX
}this.fire(y,a);this.chart.fire(y,a);
this.skipZoomDispatch=false
}else{this.skipZoomDispatch=true
}this.previousIndex=this.index
}if(!this.chart.mouseIsOver&&!this.zooming&&!this.panning&&!this.chart.touchEventsEnabled){this.hideCursor()
}},copyBalloonProperties:function(c){var d=this.chart.balloon;
var b=["fillColor","fillAlpha","borderThickness","borderColor","borderAlpha","cornerRadius","maximumWidth","horizontalPadding","verticalPadding","pointerWidth","color","fontSize","showBullet","textShadowColor","adjustBorderColor"];
for(var a=0;a<b.length;
a++){c[b[a]]=d[b[a]]}},isZooming:function(a){if(a&&a!=this.zooming){this.handleMouseDown("fake")
}if(!a&&a!=this.zooming){this.handleMouseUp()
}},handleMouseOut:function(){if(this.zooming){this.setPosition()
}else{this.index=undefined;
var a={};a.type="changed";
a.index=undefined;this.fire("changed",a);
this.hideCursor()}},handleReleaseOutside:function(){this.handleMouseUp()
},handleMouseUp:function(){if(this.pan){this.rolledOver=false
}else{if(this.zoomable){if(this.zooming){if(this.selection){this.selection.remove()
}var c;if(this.type=="cursor"){var b;
if(this.rotate){b=this.chart.mouseY;
this.selectionPosY=b}else{b=this.chart.mouseX;
this.selectionPosX=b}if(Math.abs(b-this.initialMouse)<2&&this.fromIndex==this.index){}else{c={type:"zoomed"};
if(this.index<this.fromIndex){c.end=this.fromIndex;
c.start=this.index}else{c.end=this.index;
c.start=this.fromIndex
}if(this.categoryAxis.parseDates&&!this.categoryAxis.equalSpacing){c.start=this.data[c.start].time;
c.end=this.data[c.end].time
}this.allBullets.remove();
if(!this.skipZoomDispatch){this.fire("zoomed",c)
}}}else{var a=this.chart.mouseY;
var d=this.chart.mouseX;
if(Math.abs(d-this.initialMouseX)<3&&Math.abs(a-this.initialMouseY)<3){}else{c={type:"zoomed"};
c.selectionHeight=this.selection.height;
c.selectionWidth=this.selection.width;
c.selectionY=this.selection.y;
c.selectionX=this.selection.x;
if(!this.skipZoomDispatch){this.fire("zoomed",c)
}}}}}}this.skipZoomDispatch=false;
this.zooming=false;this.panning=false
},handleMouseDown:function(a){if(this.zoomable||this.pan){if((this.chart.mouseX>this.x&&this.chart.mouseX<this.x+this.width&&this.chart.mouseY>this.y&&this.chart.mouseY<this.height+this.y)||a=="fake"){this.setPosition();
if(this.pan){this.zoomable=false;
this.chart.setMouseCursor("move");
this.panning=true;this.hideCursor(true);
if(this.rotate){this.panClickPos=this.chart.mouseY
}else{this.panClickPos=this.chart.mouseX
}this.panClickStart=this.start;
this.panClickEnd=this.end;
this.panClickStartTime=this.startTime;
this.panClickEndTime=this.endTime
}if(this.zoomable){if(this.type=="cursor"){this.fromIndex=this.index;
if(this.rotate){this.initialMouse=this.chart.mouseY;
this.selectionPosY=this.linePos
}else{this.initialMouse=this.chart.mouseX;
this.selectionPosX=this.linePos
}}else{}this.zooming=true
}}}}});AmCharts.SimpleChartScrollbar=AmCharts.Class({construct:function(){this.createEvents("zoomed");
this.backgroundColor="#D4D4D4";
this.backgroundAlpha=1;
this.selectedBackgroundColor="#EFEFEF";
this.selectedBackgroundAlpha=1;
this.scrollDuration=2;
this.resizeEnabled=true;
this.hideResizeGrips=true;
this.scrollbarHeight=20;
this.updateOnReleaseOnly=false;
this.dragIconWidth=11;
this.dragIconHeight=18
},draw:function(){if(this.chart.touchEventsEnabled){this.updateOnReleaseOnly=true
}var c=this;this.destroy();
this.interval=setInterval(function(){c.updateScrollbar.call(c)
},20);this.container=this.chart.container;
this.set=this.container.set();
this.data=this.chart.chartData;
this.dx=this.chart.dx;
this.dy=this.chart.dy;
this.rotate=this.chart.rotate;
this.categoryAxis=this.chart.categoryAxis;
if(this.rotate){this.width=this.scrollbarHeight;
this.height=this.chart.plotAreaHeight
}else{this.height=this.scrollbarHeight;
this.width=this.chart.plotAreaWidth
}if(this.height&&this.width){var a=AmCharts.rect(this.container,this.width,this.height,[this.backgroundColor],[this.backgroundAlpha]);
this.set.push(a);if(this.chart.touchEventsEnabled){a.touchend(function(){c.handleBackgroundClick()
})}a.click(function(){c.handleBackgroundClick()
}).mouseover(function(){c.handleMouseOver()
}).mouseout(function(){c.handleMouseOut()
});this.selectedBG=AmCharts.rect(this.container,this.width,this.height,[this.selectedBackgroundColor],[this.selectedBackgroundAlpha]);
this.set.push(this.selectedBG);
this.dragger=AmCharts.rect(this.container,this.width,this.height,["#0000CC"],[0]);
this.set.push(this.dragger);
if(this.chart.touchEventsEnabled){this.dragger.touchstart(function(d){c.handleDragStart(d)
}).touchend(function(){c.handleDragStop()
})}this.dragger.mousedown(function(d){c.handleDragStart(d)
}).mouseup(function(){c.handleDragStop()
}).mouseover(function(){c.handleDraggerOver()
}).mouseout(function(){c.handleMouseOut()
});this.dragIconLeft=this.container.image(this.chart.pathToImages+"dragIcon.gif",0,0,this.dragIconWidth,this.dragIconHeight);
this.set.push(this.dragIconLeft);
this.dragIconRight=this.container.image(this.chart.pathToImages+"dragIcon.gif",0,0,this.dragIconWidth,this.dragIconHeight);
this.set.push(this.dragIconRight);
var b;if(this.rotate){b=Math.round(this.width/2-this.dragIconWidth/2);
this.dragIconLeft.attr("x",b);
this.dragIconRight.attr("x",b);
this.dragIconRight.attr("rotation",90);
this.dragIconLeft.attr("rotation",90)
}else{b=Math.round(this.height/2-this.dragIconHeight/2)+AmCharts.ddd;
this.dragIconLeft.attr("y",b);
this.dragIconRight.attr("y",b)
}this.iconPosition=b;
this.dragIconLeft.mousedown(function(){c.handleLeftIconDragStart()
}).mouseup(function(){c.handleLeftIconDragStop()
}).mouseover(function(){c.handleIconRollOver()
}).mouseout(function(){c.handleIconRollOut()
});this.dragIconRight.mousedown(function(){c.handleRightIconDragStart()
}).mouseup(function(){c.handleRightIconDragStop()
}).mouseover(function(){c.handleIconRollOver()
}).mouseout(function(){c.handleIconRollOut()
});if(this.data.length>0){this.set.show()
}else{this.set.hide()
}if(this.hideResizeGrips){this.dragIconLeft.hide();
this.dragIconRight.hide()
}}this.set.translate(this.x+","+this.y)
},updateScrollbarSize:function(b,a){if(this.rotate){this.clipX=this.x;
this.clipY=b;this.clipW=this.width;
this.clipH=a-b;var c=a-b;
this.dragger.attr("height",c);
this.dragger.attr("y",this.clipY)
}else{this.clipX=b;this.clipY=this.y;
this.clipW=a-b;this.clipH=this.height;
var c=a-b;this.dragger.attr("width",c);
this.dragger.attr("x",this.clipX)
}this.clipRect=this.clipX+","+this.clipY+","+this.clipW+","+this.clipH;
this.selectedBG.attr({"clip-rect":this.clipRect});
this.updateDragIconPositions();
this.maskGraphs(this.clipRect)
},updateScrollbar:function(){var j;
var e=false;var f;var g;
var h=this.dragger.getBBox();
if(this.dragging){if(this.rotate){var k=this.initialDragCoordinate+(this.chart.mouseY-this.initialMouseCoordinate);
if(k<this.y){k=this.y
}var c=this.y+this.height-h.height;
if(k>c){k=c}this.dragger.attr({y:k})
}else{var a=this.initialDragCoordinate+(this.chart.mouseX-this.initialMouseCoordinate);
if(a<this.x){a=this.x
}var b=this.x+this.width-h.width;
if(a>b){a=b}this.dragger.attr({x:a})
}}if(this.resizingRight){if(this.rotate){j=this.chart.mouseY-h.y;
if(j+h.y>this.height+this.y){j=this.height-h.y+this.y
}if(j<0){this.resizingRight=false;
this.resizingLeft=true;
e=true}else{if(j==0){j=0.1
}this.dragger.attr("height",j)
}}else{j=this.chart.mouseX-h.x;
if(j+h.x>this.width+this.x){j=this.width-h.x+this.x
}if(j<0){this.resizingRight=false;
this.resizingLeft=true;
e=true}else{if(j==0){j=0.1
}this.dragger.attr("width",j)
}}}if(this.resizingLeft){if(this.rotate){f=h.y;
g=this.chart.mouseY;if(g<this.y){g=this.y
}if(g>this.height+this.y){g=this.height+this.y
}if(e==true){j=f-g}else{j=h.height+f-g
}if(j<0){this.resizingRight=true;
this.resizingLeft=false;
this.dragger.attr("y",f+h.height)
}else{if(j==0){j=0.1}this.dragger.attr("y",g);
this.dragger.attr("height",j)
}}else{f=h.x;g=this.chart.mouseX;
if(g<this.x){g=this.x
}if(g>this.width+this.x){g=this.width+this.x
}if(e==true){j=f-g}else{j=h.width+f-g
}if(j<0){this.resizingRight=true;
this.resizingLeft=false;
this.dragger.attr("x",f+h.width)
}else{if(j==0){j=0.1}this.dragger.attr("x",g);
this.dragger.attr("width",j)
}}}h=this.dragger.getBBox();
var d=false;if(this.rotate){if(this.clipY!=h.y||this.clipH!=h.height){d=true
}}else{if(this.clipX!=h.x||this.clipW!=h.width){d=true
}}if(d){this.clipX=h.x;
this.clipY=h.y;this.clipW=h.width;
this.clipH=h.height;this.clipRect=this.clipX+","+this.clipY+","+this.clipW+","+this.clipH;
this.selectedBG.attr({"clip-rect":this.clipRect});
this.updateDragIconPositions();
if(!this.updateOnReleaseOnly){this.dispatchScrollbarEvent()
}this.maskGraphs(this.clipRect)
}},maskGraphs:function(){},dispatchScrollbarEvent:function(){var g=this.dragger.getBBox();
var a=g.x-this.x;var h=g.y-this.y;
var e=g.width;var c=g.height;
var j;var d;var f;if(this.rotate){j=h;
d=c;f=this.height/c}else{j=a;
d=e;f=this.width/e}var b={type:"zoomed"};
b.position=j;b.multiplyer=f;
this.fire("zoomed",b)
},updateDragIconPositions:function(){var b=this.dragger.getBBox();
var a=b.x;var c=b.y;if(this.rotate){this.dragIconLeft.attr("y",Math.round(c-this.dragIconHeight/2));
this.dragIconRight.attr("y",Math.round(c+b.height-this.dragIconHeight/2))
}else{this.dragIconLeft.attr("x",Math.round(a-this.dragIconWidth/2));
this.dragIconRight.attr("x",Math.round(a-this.dragIconWidth/2+b.width))
}},showDragIcons:function(){if(this.resizeEnabled){this.dragIconLeft.show();
this.dragIconRight.show()
}},hideDragIcons:function(){if(!this.resizingLeft&&!this.resizingRight&&!this.dragging){if(this.hideResizeGrips){this.dragIconLeft.hide();
this.dragIconRight.hide()
}this.removeCursors()
}},removeCursors:function(){this.chart.setMouseCursor("auto")
},relativeZoom:function(b,a){this.multiplyer=b;
this.position=a;var d=a;
var c;if(this.rotate){c=d+this.height/b
}else{c=d+this.width/b
}this.updateScrollbarSize(d,c)
},destroy:function(){this.clear();
AmCharts.removeSet(this.set)
},clear:function(){clearInterval(this.interval)
},handleDragStart:function(a){if(a){a.preventDefault()
}this.removeCursors();
this.dragging=true;var b=this.dragger.getBBox();
if(this.rotate){this.initialDragCoordinate=b.y;
this.initialMouseCoordinate=this.chart.mouseY
}else{this.initialDragCoordinate=b.x;
this.initialMouseCoordinate=this.chart.mouseX
}},handleDragStop:function(a){if(this.updateOnReleaseOnly){this.updateScrollbar();
this.skipEvent=false;
this.dispatchScrollbarEvent()
}this.dragging=false;
if(this.mouseIsOver){this.removeCursors()
}this.updateScrollbar()
},handleDraggerOver:function(a){this.handleMouseOver()
},handleLeftIconDragStart:function(a){this.resizingLeft=true
},handleLeftIconDragStop:function(a){this.resizingLeft=false;
if(!this.mouseIsOver){this.removeCursors()
}},handleRightIconDragStart:function(a){this.resizingRight=true
},handleRightIconDragStop:function(a){this.resizingRight=false;
if(!this.mouseIsOver){this.removeCursors()
}},handleIconRollOut:function(){this.removeCursors()
},handleIconRollOver:function(a){if(this.rotate){this.chart.setMouseCursor("n-resize")
}else{this.chart.setMouseCursor("e-resize")
}this.handleMouseOver()
},handleBackgroundClick:function(b){if(!this.resizingRight&&!this.resizingLeft){this.zooming=true;
var c;var f;var a;var d=this.scrollDuration;
var e=this.dragger.getBBox();
if(this.rotate){c="y";
f=e.y;a=this.chart.mouseY-e.height/2;
a=AmCharts.fitToBounds(a,this.y,this.y+this.height-e.height)
}else{c="x";f=e.x;a=this.chart.mouseX-e.width/2;
a=AmCharts.fitToBounds(a,this.x,this.x+this.width-e.width)
}if(this.updateOnReleaseOnly){this.skipEvent=false;
this.dragger.attr(c,a);
this.dispatchScrollbarEvent()
}else{if(this.rotate){this.dragger.animate({translation:0+","+(a-e.y)},d*1000,">")
}else{this.dragger.animate({translation:(a-e.x)+","+0},d*1000,">")
}}}},handleReleaseOutside:function(){if(this.set){if(this.resizingLeft||this.resizingRight||this.dragging){if(this.updateOnReleaseOnly){this.updateScrollbar();
this.skipEvent=false;
this.dispatchScrollbarEvent()
}}this.resizingLeft=false;
this.resizingRight=false;
this.dragging=false;this.mouseIsOver=false;
this.removeCursors();
if(this.hideResizeGrips){this.dragIconLeft.hide();
this.dragIconRight.hide()
}this.updateScrollbar()
}},handleMouseOver:function(a){this.mouseIsOver=true;
this.showDragIcons()},handleMouseOut:function(a){this.mouseIsOver=false;
this.hideDragIcons()}});
AmCharts.ChartScrollbar=AmCharts.Class({inherits:AmCharts.SimpleChartScrollbar,construct:function(){AmCharts.ChartScrollbar.base.construct.call(this);
this.graphLineColor="#000000";
this.graphLineAlpha=0;
this.graphFillColor="#000000";
this.graphFillAlpha=0.1;
this.selectedGraphLineColor="#000000";
this.selectedGraphLineAlpha=0;
this.selectedGraphFillColor="#000000";
this.selectedGraphFillAlpha=0.5;
this.gridCount=0;this.gridColor="#FFFFFF";
this.gridAlpha=0.7;this.autoGridCount=false;
this.skipEvent=false;
this.scrollbarCreated=false
},init:function(){if(!this.cAxis){this.cAxis=new AmCharts.CategoryAxis()
}this.categoryAxis=this.cAxis;
this.cAxis.chart=this.chart;
this.cAxis.id="scrollbar";
this.cAxis.dateFormats=this.chart.categoryAxis.dateFormats;
this.cAxis.axisItemRenderer=AmCharts.RectangularAxisItemRenderer;
this.cAxis.axisRenderer=AmCharts.RectangularAxisRenderer;
this.cAxis.guideFillRenderer=AmCharts.RectangularAxisGuideFillRenderer;
this.cAxis.inside=true;
this.cAxis.tickLength=0;
this.cAxis.axisAlpha=0;
if(this.graph){if(!this.vAxis){this.vAxis=new AmCharts.ValueAxis();
this.vAxis.visible=false;
this.vAxis.scrollbar=true;
this.vAxis.axisItemRenderer=AmCharts.RectangularAxisItemRenderer;
this.vAxis.axisRenderer=AmCharts.RectangularAxisRenderer;
this.vAxis.guideFillRenderer=AmCharts.RectangularAxisGuideFillRenderer;
this.vAxis.chart=this.chart
}if(!this.selectedGraph){this.selectedGraph=new AmCharts.AmGraph();
this.selectedGraph.scrollbar=true
}if(!this.unselectedGraph){this.unselectedGraph=new AmCharts.AmGraph();
this.unselectedGraph.scrollbar=true
}}this.scrollbarCreated=true
},draw:function(){AmCharts.ChartScrollbar.base.draw.call(this);
if(!this.scrollbarCreated){this.init()
}var a=this;if(this.rotate){this.cAxis.orientation="vertical"
}else{this.cAxis.orientation="horizontal"
}this.cAxis.parseDates=this.chart.categoryAxis.parseDates;
this.cAxis.equalSpacing=this.chart.categoryAxis.equalSpacing;
this.cAxis.minPeriod=this.chart.categoryAxis.minPeriod;
this.cAxis.startOnAxis=this.chart.categoryAxis.startOnAxis;
this.cAxis.x=this.x;this.cAxis.y=this.y;
this.cAxis.visibleAxisWidth=this.width;
this.cAxis.visibleAxisHeight=this.height;
this.cAxis.visibleAxisX=this.x;
this.cAxis.visibleAxisY=this.y;
this.cAxis.width=this.width;
this.cAxis.height=this.height;
this.cAxis.gridCount=this.gridCount;
this.cAxis.gridColor=this.gridColor;
this.cAxis.gridAlpha=this.gridAlpha;
this.cAxis.color=this.color;
this.cAxis.autoGridCount=this.autoGridCount;
if(this.cAxis.parseDates&&!this.cAxis.equalSpacing){this.firstTime=this.data[0].time;
this.lastTime=this.data[this.data.length-1].time;
this.cAxis.timeZoom(this.firstTime,this.lastTime)
}this.cAxis.zoom(0,this.data.length-1);
if(this.graph){var h=this.graph;
this.vAxis.id=h.valueAxis.id;
this.vAxis.rotate=this.rotate;
if(!this.rotate){this.vAxis.orientation="vertical"
}else{this.vAxis.orientation="horizontal"
}this.vAxis.x=this.x;
this.vAxis.y=this.y;this.vAxis.width=this.width;
this.vAxis.height=this.height;
this.vAxis.visibleAxisX=this.x;
this.vAxis.visibleAxisY=this.y;
this.vAxis.visibleAxisWidth=this.width;
this.vAxis.visibleAxisHeight=this.height;
this.vAxis.dataProvider=this.data;
this.vAxis.reversed=h.valueAxis.reversed;
this.vAxis.logarithmic=h.valueAxis.logarithmic;
var d=Infinity;var j=-Infinity;
for(var e=0;e<this.data.length;
e++){var m=this.data[e].axes[h.valueAxis.id].graphs[h.id].values;
for(var c in m){if(c!="percents"&&c!="total"){var b=m[c];
if(b<d){d=b}if(b>j){j=b
}}}}if(d!=Infinity){this.vAxis.minimum=d
}if(j!=-Infinity){this.vAxis.maximum=j+(j-d)*0.1
}this.vAxis.zoom(0,this.data.length-1);
var l=this.unselectedGraph;
l.id=h.id;l.rotate=this.rotate;
l.chart=this.chart;l.chartType=this.chart.chartType;
l.data=this.chart.chartData;
l.valueAxis=this.vAxis;
l.chart=h.chart;l.categoryAxis=this.cAxis;
l.valueField=h.valueField;
l.openField=h.openField;
l.closeField=h.closeField;
l.highField=h.highField;
l.lowField=h.lowField;
l.lineAlpha=this.graphLineAlpha;
l.lineColor=this.graphLineColor;
l.fillAlphas=[this.graphFillAlpha];
l.fillColors=[this.graphFillColor];
l.connect=h.connect;l.hidden=h.hidden;
l.width=this.width;l.height=this.height;
l.x=this.x;l.y=this.y;
var f=this.selectedGraph;
f.id=h.id;f.rotate=this.rotate;
f.chart=this.chart;f.chartType=this.chart.chartType;
f.data=this.chart.chartData;
f.valueAxis=this.vAxis;
f.chart=h.chart;f.categoryAxis=this.cAxis;
f.valueField=h.valueField;
f.openField=h.openField;
f.closeField=h.closeField;
f.highField=h.highField;
f.lowField=h.lowField;
f.lineAlpha=this.selectedGraphLineAlpha;
f.lineColor=this.selectedGraphLineColor;
f.fillAlphas=[this.selectedGraphFillAlpha];
f.fillColors=[this.selectedGraphFillColor];
f.connect=h.connect;f.hidden=h.hidden;
f.width=this.width;f.height=this.height;
f.x=this.x;f.y=this.y;
if(this.graphType){f.type=this.graphType;
l.type=this.graphType
}else{l.type=h.type;f.type=h.type
}l.zoom(0,this.data.length-1);
f.zoom(0,this.data.length-1);
f.set.insertBefore(this.dragger);
l.set.insertBefore(this.dragger);
f.set.click(function(){a.handleBackgroundClick()
}).mouseover(function(){a.handleMouseOver()
}).mouseout(function(){a.handleMouseOut()
});l.set.click(function(){a.handleBackgroundClick()
}).mouseover(function(){a.handleMouseOver()
}).mouseout(function(){a.handleMouseOut()
})}},timeZoom:function(b,a){this.startTime=b;
this.endTime=a;this.timeDifference=a-b;
this.skipEvent=true;this.zoomScrollbar()
},zoom:function(b,a){this.start=b;
this.end=a;this.skipEvent=true;
this.zoomScrollbar()},dispatchScrollbarEvent:function(){if(this.skipEvent){this.skipEvent=false
}else{var o;var h;var l=this.dragger.getBBox();
var a=l.x;var n=l.y;var j=l.width;
var f=l.height;if(this.rotate){o=n;
h=f}else{o=a;h=j}var b;
if(this.cAxis.parseDates&&!this.cAxis.equalSpacing){if(this.rotate){o-=this.y
}else{o-=this.x}var d=this.cAxis.minDuration();
var e=Math.round(o/this.stepWidth)+this.firstTime;
var k;if(!this.dragging){k=Math.round((o+h)/this.stepWidth)+this.firstTime-d
}else{k=e+this.timeDifference
}if(e>k){e=k}if(e!=this.startTime||k!=this.endTime){this.startTime=e;
this.endTime=k;b={type:"zoomed"};
b.start=this.startTime;
b.end=this.endTime;b.startDate=new Date(this.startTime);
b.endDate=new Date(this.endTime);
this.fire("zoomed",b)
}}else{if(!this.cAxis.startOnAxis){var m=this.stepWidth/2;
o+=m}h-=this.stepWidth/2;
var c=this.cAxis.xToIndex(o);
var g=this.cAxis.xToIndex(o+h);
if(c!=this.start||this.end!=g){if(this.cAxis.startOnAxis){if(this.resizingRight&&c==g){g++
}if(this.resizingLeft&&c==g){if(c>0){c--
}else{g=1}}}this.start=c;
if(!this.dragging){this.end=g
}else{this.end=this.start+this.difference
}b={type:"zoomed"};b.start=this.start;
b.end=this.end;if(this.cAxis.parseDates){if(this.data[this.start]){b.startDate=new Date(this.data[this.start].time)
}if(this.data[this.end]){b.endDate=new Date(this.data[this.end].time)
}}this.fire("zoomed",b)
}}}},zoomScrollbar:function(){var c;
var a;if(this.cAxis.parseDates&&!this.cAxis.equalSpacing){this.stepWidth=this.cAxis.stepWidth;
c=this.stepWidth*(this.startTime-this.firstTime);
a=this.stepWidth*(this.endTime-this.firstTime+this.cAxis.minDuration());
if(this.rotate){c+=this.y;
a+=this.y}else{c+=this.x;
a+=this.x}}else{c=this.data[this.start].x[this.cAxis.id];
a=this.data[this.end].x[this.cAxis.id];
this.stepWidth=this.cAxis.stepWidth;
if(!this.cAxis.startOnAxis){var b=this.stepWidth/2;
c-=b;a+=b}}this.updateScrollbarSize(c,a)
},maskGraphs:function(b){if(this.selectedGraph){for(var a=0;
a<this.selectedGraph.set.length;
a++){this.selectedGraph.set[a].attr({"clip-rect":b})
}}},handleDragStart:function(){AmCharts.ChartScrollbar.base.handleDragStart.call(this);
this.difference=this.end-this.start;
this.timeDifference=this.endTime-this.startTime;
if(this.timeDifference<0){this.timeDifference=0
}},handleBackgroundClick:function(){AmCharts.ChartScrollbar.base.handleBackgroundClick.call(this);
if(!this.dragging){this.difference=this.end-this.start;
this.timeDifference=this.endTime-this.startTime;
if(this.timeDifference<0){this.timeDifference=0
}}}});AmCharts.circle=function(b,a,e,d,c,f,g,j){if(c==undefined||c==0){c=1
}if(f==undefined){f="#000000"
}if(g==undefined){g=0
}if(j){e="r"+e+"-"+AmCharts.adjustLuminosity(e,-0.6)
}var h={fill:e,stroke:f,"fill-opacity":d,"stroke-width":c,"stroke-opacity":g};
return b.circle(0,0,a).attr(h)
};AmCharts.text=function(d,c,f,e,b){var a=d.text(c,f,e).attr(b);
if(!AmCharts.isNN&&AmCharts.IEversion<9){a.translate(0+","+3)
}if(window.opera){a.translate(0+","+(-2))
}return a};AmCharts.polygon=function(c,o,l,a,b,d,f,g,n){if(typeof(b)=="object"){b=b[0]
}if(d==undefined||d==0){d=1
}if(f==undefined){f="#000000"
}if(g==undefined){g=0
}if(n==undefined){n=270
}var m=AmCharts.generateGradient(a,n);
var k={fill:String(m),stroke:f,"fill-opacity":b,"stroke-width":d,"stroke-opacity":g};
var j=AmCharts.ddd;var h=["M",Math.round(o[0])+j,Math.round(l[0])+j];
for(var e=1;e<o.length;
e++){h.push("L");h.push(Math.round(o[e])+j);
h.push(Math.round(l[e])+j)
}h.push("Z");return c.path(h).attr(k)
};AmCharts.rect=function(c,p,j,a,b,d,f,g,e,o){if(d==undefined||d==0){d=1
}if(f==undefined){f="#000000"
}if(g==undefined){g=0
}if(e==undefined){e=0
}if(o==undefined){o=270
}if(typeof(b)=="object"){b=b[0]
}if(b==undefined){b=0
}p=Math.round(p);j=Math.round(j);
var n=0;var l=0;if(p<0){p=Math.abs(p);
n=-p}if(j<0){j=Math.abs(j);
l=-j}n+=AmCharts.ddd;
l+=AmCharts.ddd;var m=AmCharts.generateGradient(a,o);
if(!m){m="#FFFFFF"}var k={fill:String(m),stroke:f,"fill-opacity":b,"stroke-width":d,"stroke-opacity":g};
return c.rect(n,l,p,j,e).attr(k)
};AmCharts.triangle=function(a,j,k,d,c,b,e,f){if(b==undefined||b==0){b=1
}if(e==undefined){e="#000000"
}if(f==undefined){f=0
}var h={fill:d,stroke:e,"fill-opacity":c,"stroke-width":b,"stroke-opacity":f};
var l=["M",-j/2,j/2,"L",0,-j/2,"L",j/2,j/2,"Z",-j/2,j/2];
var g=a.path(l).attr(h);
g.attr({rotation:k});
return g};AmCharts.line=function(a,o,m,e,d,n,b,k){var l="";
if(b==1){l=". "}if(b>1){l="- "
}var j={stroke:e,"stroke-dasharray":l,"stroke-opacity":d,"stroke-width":n};
var c="L";var h=AmCharts.ddd;
var g=["M",Math.round(o[0])+h,Math.round(m[0])+h];
for(var f=1;f<o.length;
f++){g.push(c);g.push(Math.round(o[f])+h);
g.push(Math.round(m[f])+h)
}return a.path(g).attr(j)
};AmCharts.wedge=function(t,q,o,C,m,f,k,l,E,n){var H=Math.PI/180;
var B=(k/f)*l;if(m<=-359.99){m=-359.99
}var u=q+Math.cos(C/180*Math.PI)*l;
var p=o+Math.sin(-C/180*Math.PI)*B;
var F=q+Math.cos(C/180*Math.PI)*f;
var D=o+Math.sin(-C/180*Math.PI)*k;
var d=q+Math.cos((C+m)/180*Math.PI)*f;
var b=o+Math.sin((-C-m)/180*Math.PI)*k;
var v=q+Math.cos((C+m)/180*Math.PI)*l;
var s=o+Math.sin((-C-m)/180*Math.PI)*B;
hsb=AmCharts.adjustLuminosity(n.fill,-0.2);
var e=n["fill-opacity"];
var a={fill:hsb,"fill-opacity":e,stroke:hsb,"stroke-width":0.000001,"stroke-opacity":0.00001};
var z=0;var I=1;if(Math.abs(m)>180){z=1
}var A=t.set();if(E>0){if(l>0){var G=t.path(["M",u,p+E,"L",F,D+E,"A",f,k,0,z,I,d,b+E,"L",v,s+E,"A",l,B,0,z,0,u,p+E,"z"]).attr(a)
}else{var G=t.path(["M",u,p+E,"L",F,D+E,"A",f,k,0,z,I,d,b+E,"L",v,s+E,"Z"]).attr(a)
}A.push(G);var j=t.path(["M",u,p,"L",u,p+E,"L",F,D+E,"L",F,D,"L",u,p,"z"]).attr(a);
var g=t.path(["M",d,b,"L",d,b+E,"L",v,s+E,"L",v,s,"L",d,b,"z"]).attr(a);
A.push(j);A.push(g)}if(l>0){var r=t.path(["M",u,p,"L",F,D,"A",f,k,0,z,I,d,b,"L",v,s,"A",l,B,0,z,0,u,p,"Z"]).attr(n)
}else{var r=t.path(["M",u,p,"L",F,D,"A",f,k,0,z,I,d,b,"L",v,s,"Z"]).attr(n)
}A.push(r);return A};
AmCharts.adjustLuminosity=function(e,b){var d=Raphael.rgb2hsb(e);
var a=d.toString().split(",");
var c=a[2];c=Number(c.substr(0,c.length-1));
c=c+c*b;return(a[0]+","+a[1]+","+c+")")
};AmCharts.putSetToFront=function(b){for(var a=b.length-1;
a<=0;a++){b[a].toFront()
}},AmCharts.putSetToBack=function(b){for(var a=0;
a<b.length-1;a++){b[a].toBack()
}};AmCharts.AmPieChart=AmCharts.Class({inherits:AmCharts.AmChart,construct:function(){this.createEvents("rollOverSlice","rollOutSlice","clickSlice","pullOutSlice","pullInSlice");
AmCharts.AmPieChart.base.construct.call(this);
this.colors=["#FF0F00","#FF6600","#FF9E01","#FCD202","#F8FF01","#B0DE09","#04D215","#0D8ECF","#0D52D1","#2A0CD0","#8A0CCF","#CD0D74","#754DEB","#DDDDDD","#999999","#333333","#000000","#57032A","#CA9726","#990000","#4B0C25"];
this.pieAlpha=1;this.pieBaseColor;
this.pieBrightnessStep=30;
this.groupPercent=0;this.groupedTitle="Other";
this.groupedPulled=false;
this.groupedAlpha=1;this.marginLeft=0;
this.marginTop=10;this.marginBottom=10;
this.marginRight=0;this.minRadius=10;
this.hoverAlpha=1;this.depth3D=0;
this.startAngle=90;this.innerRadius=0;
this.angle=0;this.outlineColor="#FFFFFF";
this.outlineAlpha=0;this.outlineThickness=1;
this.gradient="none";
this.gradientRatio=[0,80];
this.startRadius="500%";
this.startAlpha=0;this.startDuration=1;
this.startEffect="bounce";
this.sequencedAnimation=false;
this.pullOutRadius="20%";
this.pullOutDuration=1;
this.pullOutEffect="bounce";
this.pullOutOnlyOne=false;
this.pullOnHover=false;
this.labelsEnabled=true;
this.labelRadius=30;this.labelTickColor="#000000";
this.labelTickAlpha=0.2;
this.labelText="[[title]]: [[percents]]%";
this.hideLabelsPercent=0;
this.balloonText="[[title]]: [[percents]]% ([[value]])\n[[description]]";
this.dataProvider;this.urlTarget="_self";
this.previousScale=1},initChart:function(){AmCharts.AmPieChart.base.initChart.call(this);
if(this.dataChanged){this.parseData();
this.dispatchDataUpdated=true;
this.dataChanged=false;
if(this.legend){this.legend.setData(this.chartData)
}}this.drawChart()},handleLegendEvent:function(b){var a=b.type;
var d=b.dataItem;if(d){var c=d.hidden;
switch(a){case"clickMarker":if(!c){this.clickSlice(d)
}break;case"clickLabel":if(!c){this.clickSlice(d)
}break;case"rollOverItem":if(!c){this.rollOverSlice(d,false)
}break;case"rollOutItem":if(!c){this.rollOutSlice(d)
}break;case"hideItem":this.hideSlice(d);
break;case"showItem":this.showSlice(d);
break}}},invalidateVisibility:function(){this.recalculatePercents();
this.drawChart();if(this.legend){this.legend.invalidateSize()
}},drawChart:function(){AmCharts.AmPieChart.base.drawChart.call(this);
var k=this;var l=AmCharts.toCoordinate(this.marginLeft,this.realWidth);
var P=AmCharts.toCoordinate(this.marginRight,this.realWidth);
var D=AmCharts.toCoordinate(this.marginTop,this.realHeight);
var A=AmCharts.toCoordinate(this.marginBottom,this.realHeight);
if(this.chartData.length>0){this.realWidth=this.updateWidth();
this.realHeight=this.updateHeight();
this.chartDataLabels=[];
this.ticks=[];var O;var N;
var b=this.chartData.length;
var G;var u=AmCharts.toNumber(this.labelRadius);
var g=this.measureMaxLabel();
if(!this.labelText||!this.labelsEnabled){g=0;
u=0}if(this.pieX==undefined){O=(this.realWidth-l-P)/2+l
}else{O=AmCharts.toCoordinate(this.pieX,this.realWidth)
}if(this.pieY==undefined){N=(this.realHeight-D-A)/2+D
}else{N=AmCharts.toCoordinate(this.pieY,this.realHeight)
}G=AmCharts.toCoordinate(this.radius,this.realWidth,this.realHeight);
this.pullOutRadiusReal=AmCharts.toCoordinate(this.pullOutRadius,G);
if(!G){var d;if(u>=0){d=this.realWidth-l-P-g*2
}else{d=this.realWidth-l-P
}var H=this.realHeight-D-A;
G=Math.min(d,H);if(H<d){G=G/(1-this.angle/90);
if(G>d){G=d}}this.pullOutRadiusReal=AmCharts.toCoordinate(this.pullOutRadius,G);
if(u>=0){G-=(u+this.pullOutRadiusReal)*1.8
}else{G-=this.pullOutRadiusReal*1.8
}G=G/2}if(G<this.minRadius){G=this.minRadius
}this.pullOutRadiusReal=AmCharts.toCoordinate(this.pullOutRadius,G);
var a=AmCharts.toCoordinate(this.innerRadius,G);
if(a>=G){a=G-1}var h=AmCharts.fitToBounds(this.startAngle,0,360);
if(this.depth3D>0){if(h>=270){h=270
}else{h=90}}var z=G-G*this.angle/90;
for(var M=0;M<b;M++){var c=this.chartData[M];
if(c.hidden!=true&&c.percents>0){var s=-c.percents*360/100;
var q=Math.cos((h+s/2)/180*Math.PI);
var p=Math.sin((-h-s/2)/180*Math.PI)*(z/G);
var r;if(c.url){r="pointer"
}else{r=""}var o="90-"+c.color;
if(this.gradient!="none"){if(this.gradient=="radial"){var n=Math.abs(h+s/2)
}else{var n=90}o=AmCharts.generateGradient(c.color,n,this.gradientRatio)
}var E={fill:c.color,gradient:o,"fill-opacity":this.startAlpha,stroke:this.outlineColor,"stroke-opacity":this.outlineAlpha,"stroke-width":this.outlineThickness,"stroke-linecap":"round",cursor:r};
var y=O;var m=N;if(this.chartCreated){E["fill-opacity"]=c.alpha
}var C=AmCharts.wedge(this.container,y,m,h,s,G,z,a,this.depth3D,E);
this.chartData[M].wedge=C;
if((h<=90&&h>=0)||(h<=360&&h>270)){AmCharts.putSetToFront(C)
}else{if((h<=270&&h>180)||(h<=180&&h>90)){AmCharts.putSetToBack(C)
}}c.ix=q;c.iy=p;c.wedge=C;
c.index=M;if(this.labelsEnabled&&this.labelText&&c.percents>=this.hideLabelsPercent){var x=h+s/2;
if(x<=0){x=x+360}var L=O+q*(G+u);
var J=N+p*(G+u);var I;
var e=0;if(u>=0){var B;
if(x<=90&&x>=0){B=0;I="start";
e=8}else{if(x<=360&&x>270){B=1;
I="start";e=8}else{if(x<=270&&x>180){B=2;
I="end";e=-8}else{if((x<=180&&x>90)){B=3;
I="end";e=-8}}}}c.labelQuarter=B
}else{I="middle"}var v=AmCharts.formatString(this.labelText,c,this.numberFormatter,this.percentFormatter);
var f=AmCharts.text(this.container,L+e*1.5,J,v,{fill:this.color,"text-anchor":I,"font-family":this.fontFamily,"font-size":this.fontSize});
var F=setTimeout(function(){k.showLabels.call(k)
},this.startDuration*1000);
this.timeOuts.push(F);
if(this.touchEventsEnabled){C.touchend(function(){handleTouchEnd(k.chartData[this.index])
});C.touchstart(function(j){handleTouchStart(k.chartData[this.index])
})}C.push(f);c.labelObject=f;
this.chartDataLabels[M]=f;
f.cornerx=L;f.cornery=J;
f.cornerx2=L+e}for(var K=0;
K<C.length;K++){C[K].index=M
}C.hover(function(){k.rollOverSlice(k.chartData[this.index],true)
},function(){k.rollOutSlice(k.chartData[this.index])
}).click(function(){k.clickSlice(k.chartData[this.index])
});this.set.push(C);if(c.alpha==0){C.hide()
}h-=c.percents*360/100;
if(h<=0){h=h+360}}}if(u>0){this.arrangeLabels()
}for(var M=0;M<this.chartDataLabels.length;
M++){if(this.chartDataLabels[M]){this.chartDataLabels[M].toFront()
}}this.pieXReal=O;this.pieYReal=N;
this.radiusReal=G;this.innerRadiusReal=a;
if(u>0){this.drawTicks()
}var k=this;if(this.chartCreated){this.pullSlices(true)
}else{var F=setTimeout(function(){k.pullSlices.call(k)
},this.startDuration*1200);
this.timeOuts.push(F)
}if(!this.chartCreated){this.startSlices()
}this.bringLabelsToFront();
this.chartCreated=true;
if(this.dispatchDataUpdated){this.dispatchDataUpdated=false;
this.dispatchDataUpdatedEvent()
}}if(this.bgImg){this.bgImg.toBack()
}if(this.background){this.background.toBack()
}this.drb()},drawTicks:function(){for(var d=0;
d<this.chartData.length;
d++){if(this.chartDataLabels[d]){var f=this.chartData[d];
var c=f.ix;var b=f.iy;
var h=this.chartDataLabels[d];
var j=h.cornerx;var a=h.cornerx2;
var g=h.cornery;var e=this.container.path(["M",this.pieXReal+c*this.radiusReal,this.pieYReal+b*this.radiusReal,"L",j,g,"L",a,g]).attr({stroke:this.labelTickColor,"stroke-opacity":this.labelTickAlpha,"stroke-width":1,"stroke-linecap":"round"});
f.wedge.push(e);if(!this.chartCreated){f.wedge.hide()
}this.ticks[d]=e}}},arrangeLabels:function(){var d;
var e=0;var a=0;for(var c=this.chartData.length-1;
c>=0;c--){var f=this.chartData[c];
if(f.labelQuarter==0&&!f.hidden&&this.chartDataLabels[c]){var b=f.index;
this.checkOverlapping(b,0,true,0)
}}d=NaN;for(c=0;c<this.chartData.length;
c++){f=this.chartData[c];
if(f.labelQuarter==1&&!f.hidden&&this.chartDataLabels[c]){var b=f.index;
this.checkOverlapping(b,1,false,0)
}}d=NaN;for(c=this.chartData.length-1;
c>=0;c--){f=this.chartData[c];
if(f.labelQuarter==2&&!f.hidden&&this.chartDataLabels[c]){var b=f.index;
this.checkOverlapping(b,2,true,0)
}}d=NaN;for(c=0;c<this.chartData.length;
c++){f=this.chartData[c];
if(f.labelQuarter==3&&!f.hidden&&this.chartDataLabels[c]){var b=f.index;
this.checkOverlapping(b,3,false,0)
}}},checkOverlapping:function(d,c,b,f){var a;
var e;var h;if(b==true){for(e=d+1;
e<this.chartData.length;
e++){h=this.chartData[e];
if(h.labelQuarter==c&&!h.hidden&&this.chartDataLabels[e]){if(AmCharts.hitTest(this.chartDataLabels[d].getBBox(),this.chartDataLabels[e].getBBox())==true){a=true
}}}}else{for(e=d-1;e>=0;
e--){h=this.chartData[e];
if(h.labelQuarter==c&&!h.hidden&&this.chartDataLabels[e]){if(AmCharts.hitTest(this.chartDataLabels[d].getBBox(),this.chartDataLabels[e].getBBox())==true){a=true
}}}}var g=this.chartDataLabels[d].getBBox();
this.chartDataLabels[d].cornery=g.y+=g.height/2;
if(a==true&&f<100){h=this.chartData[d];
this.chartDataLabels[d].translate(0+","+(h.iy*3));
this.checkOverlapping(d,c,b,f+1)
}},startSlices:function(){var a=this.startDuration/this.chartData.length*500;
for(var c=0;c<this.chartData.length;
c++){if(this.startDuration>0&&this.sequencedAnimation){var d=this;
var b=setTimeout(function(){d.startSequenced.call(d)
},a*c);this.timeOuts.push(b)
}else{this.startSlice(this.chartData[c])
}}},pullSlices:function(a){for(var b=0;
b<this.chartData.length;
b++){if(this.chartData[b].pulled){this.pullSlice(this.chartData[b],1,a)
}}},startSequenced:function(){for(var a=0;
a<this.chartData.length;
a++){if(!this.chartData[a].started){dItem=this.chartData[a];
this.startSlice(dItem);
break}}},startSlice:function(c){c.started=true;
var a=c.wedge;if(a){if(c.alpha>0){a.show()
}var b=AmCharts.toCoordinate(this.startRadius,this.radiusReal);
a.translate((c.ix*b)+","+(c.iy*b));
a.animate({"fill-opacity":c.alpha,translation:((-c.ix*b)+","+(-c.iy*b))},this.startDuration*1000,this.startEffect)
}},showLabels:function(){for(var a=0;
a<this.chartData.length;
a++){var b=this.chartData[a];
if(b.alpha>0){if(this.chartDataLabels[a]){this.chartDataLabels[a].show()
}if(this.ticks[a]){this.ticks[a].show()
}}}},showSlice:function(a){if(isNaN(a)){a.hidden=false
}else{this.chartData[a].hidden=false
}this.hideBalloon();this.invalidateVisibility()
},hideSlice:function(a){if(isNaN(a)){a.hidden=true
}else{this.chartData[a].hidden=true
}this.hideBalloon();this.invalidateVisibility()
},rollOverSlice:function(g,c){if(!isNaN(g)){g=this.chartData[g]
}clearTimeout(this.hoverInt);
if(this.pullOnHover){this.pullSlice(g,1)
}var b=this.innerRadiusReal+(this.radiusReal-this.innerRadiusReal)/2;
if(g.pulled){b+=this.pullOutRadiusReal
}if(this.hoverAlpha<1){g.wedge.attr({"fill-opacity":this.hoverAlpha})
}var a=g.ix*b+this.pieXReal;
var h=g.iy*b+this.pieYReal;
var f=AmCharts.formatString(this.balloonText,g,this.numberFormatter,this.percentFormatter);
var e=AmCharts.adjustLuminosity(g.color,-0.15);
this.showBalloon(f,e,c,a,h);
var d={type:"rollOverSlice",dataItem:g};
this.fire(d.type,d)},rollOutSlice:function(b){if(!isNaN(b)){b=this.chartData[b]
}b.wedge.attr({"fill-opacity":b.alpha});
this.hideBalloon();var a={type:"rollOutSlice",dataItem:b};
this.fire(a.type,a)},clickSlice:function(b){if(!isNaN(b)){b=this.chartData[b]
}this.hideBalloon();if(b.pulled){this.pullSlice(b,-1)
}else{this.pullSlice(b,1)
}if(b.url){if(this.urlTarget=="_self"||!this.urlTarget){window.location.href=b.url
}else{window.open(b.url)
}}var a={type:"clickSlice",dataItem:b};
this.fire(a.type,a)},pullSlice:function(f,c,b){var g=f.ix;
var e=f.iy;var d=this.pullOutDuration*1000;
if(b===true){d=0}f.wedge.animate({translation:(c*g*this.pullOutRadiusReal)+","+(c*e*this.pullOutRadiusReal)},d,this.pullOutEffect);
if(c==1){f.pulled=true;
if(this.pullOutOnlyOne){this.pullInAll(f.index)
}var a={type:"pullOutSlice",dataItem:f};
this.fire(a.type,a)}else{f.pulled=false;
var a={type:"pullInSlice",dataItem:f};
this.fire(a.type,a)}},pullInAll:function(b){for(var a=0;
a<this.chartData.length;
a++){if(a!=b){if(this.chartData[a].pulled){this.pullSlice(this.chartData[a],-1)
}}}},pullOutAll:function(b){for(var a=0;
a<this.chartData.length;
a++){if(!this.chartData[a].pulled){this.pullSlice(this.chartData[a],1)
}}},parseData:function(){this.chartData=[];
var h=this.dataProvider;
if(h!=undefined){var f=h.length;
var d=0;for(var c=0;c<f;
c++){this.chartData[c]={};
this.chartData[c].value=Number(h[c][this.valueField]);
if(h[c][this.titleField]){this.chartData[c].title=h[c][this.titleField]
}else{this.chartData[c].title=""
}this.chartData[c].pulled=AmCharts.toBoolean(h[c][this.pulledField],false);
if(h[c][this.descriptionField]){this.chartData[c].description=h[c][this.descriptionField]
}else{this.chartData[c].description=""
}this.chartData[c].url=h[c][this.urlField];
if(AmCharts.toBoolean(h[c][this.visibleInLegendField])==false){this.chartData[c].visibleInLegend=false
}else{this.chartData[c].visibleInLegend=true
}if(h[c][this.alphaField]!=undefined){this.chartData[c].alpha=Number(h[c][this.alphaField])
}else{this.chartData[c].alpha=this.pieAlpha
}if(h[c][this.colorField]!=undefined){this.chartData[c].color=AmCharts.toColor(h[c][this.colorField])
}d+=this.chartData[c].value;
this.chartData[c].hidden=false
}var g=0;for(var c=0;
c<f;c++){this.chartData[c].percents=this.chartData[c].value/d*100;
if(this.chartData[c].percents<this.groupPercent){g++
}}if(g>1){this.groupValue=0;
this.removeSmallSlices();
var e=this.groupValue;
var a=this.groupValue/d*100;
this.chartData.push({title:this.groupedTitle,value:e,percents:a,pulled:this.groupedPulled,color:this.groupedColor,url:this.groupedUrl,description:this.groupedDescription,alpha:this.groupedAlpha})
}for(var c=0;c<this.chartData.length;
c++){var b;if(this.pieBaseColor){b=AmCharts.adjustLuminosity(this.pieBaseColor,c*this.pieBrightnessStep/100)
}else{b=this.colors[c];
if(b==undefined){b=AmCharts.randomColor()
}}if(this.chartData[c].color==undefined){this.chartData[c].color=b
}}this.recalculatePercents()
}},recalculatePercents:function(){var d=this.chartData.length;
var b=0;for(var a=0;a<d;
a++){var c=this.chartData[a];
if(!c.hidden&&c.value>0){b+=c.value
}}for(a=0;a<d;a++){c=this.chartData[a];
if(!c.hidden&&c.value>0){c.percents=c.value*100/b
}else{c.percents=0}}},handleTouchStart:function(c,a){if(!c.pulled){this.rolledOverSlice=c;
var b=this;this.pullTimeOut=setTimeout(function(){thisobj.padRollOver.call(b)
},100)}else{this.rolledOverSlice=undefined;
this.hideBalloon()}},padRollOver:function(){this.rollOverSlice(this.rolledOverSlice,false)
},handleTouchEnd:function(a){if(a.pulled){this.pullSlice(a,-1)
}else{this.pullSlice(a,1)
}},removeSmallSlices:function(){var b=this.chartData.length;
for(var a=b-1;a>=0;a--){if(this.chartData[a].percents<this.groupPercent){this.groupValue+=this.chartData[a].value;
this.chartData.splice(a,1)
}}},measureMaxLabel:function(){var d=0;
for(var c=0;c<this.chartData.length;
c++){var f=this.chartData[c];
var e=AmCharts.formatString(this.labelText,f,this.numberFormatter,this.percentFormatter);
var a=AmCharts.text(this.container,0,0,e,{fill:this.color,"font-family":this.fontFamily,"font-size":this.fontSize});
var b=a.getBBox().width;
if(b>d){d=b}a.remove()
}return d}});