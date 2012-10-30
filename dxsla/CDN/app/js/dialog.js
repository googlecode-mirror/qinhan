var gURL_TITLE='';
var gURL_ROOT = 'jianjiandandan.ivu1314.com';
var gURL_WWW = 'http://jianjiandandan.ivu1314.com';
var gURL_IMG = 'http://jjdd01.ivu1314.com';
var gwinIsDrag=true;
var gHideSelects = false;
function core_init() {
	g_isLoad=true;
}

// 刷新页面
function refresh(url) {
	location.reload();
}

function $I(id) {
	return document.getElementById(id);
}
var UA = navigator.userAgent.toLowerCase();
g_isOpera  = (UA.indexOf('opera') != -1);
g_isFF = (UA.indexOf('firefox')!= -1);
g_isIE = document.all ? true : false;
g_ieVersion = parseInt(window.navigator.appVersion.charAt(0), 10);
var g_isLoad=false;

function hideSelectBoxes(type) {
	var obj=document.getElementsByTagName("SELECT");
	for(var i=0; i<obj.length; i++) {
		obj[i].style.visibility=type;
	}
	if(type=="hidden") {
		try{
			var show=$I("pop_content").getElementsByTagName("SELECT");
			for(var j=0; j<show.length; j++) {
				show[j].style.visibility="visible";
			}
		}catch(e){};
	}
}

var Drag = {
	obj : null,
	init : function(o, oRoot, minX, maxX, minY, maxY, bSwapHorzRef, bSwapVertRef, fXMapper, fYMapper) {
		o.onmousedown	= Drag.start;
		o.hmode			= bSwapHorzRef ? false : true ;
		o.vmode			= bSwapVertRef ? false : true ;

		o.root = oRoot && oRoot != null ? oRoot : o ;

		if (o.hmode  && isNaN(parseInt(o.root.style.left  ))) o.root.style.left   = "0px";
		if (o.vmode  && isNaN(parseInt(o.root.style.top   ))) o.root.style.top    = "0px";
		if (!o.hmode && isNaN(parseInt(o.root.style.right ))) o.root.style.right  = "0px";
		if (!o.vmode && isNaN(parseInt(o.root.style.bottom))) o.root.style.bottom = "0px";

		o.minX	= typeof minX != 'undefined' ? minX : null;
		o.minY	= typeof minY != 'undefined' ? minY : null;
		o.maxX	= typeof maxX != 'undefined' ? maxX : null;
		o.maxY	= typeof maxY != 'undefined' ? maxY : null;

		o.xMapper = fXMapper ? fXMapper : null;
		o.yMapper = fYMapper ? fYMapper : null;

		o.root.onDragStart	= new Function();
		o.root.onDragEnd	= new Function();
		o.root.onDrag		= new Function();
	},

	start : function(e) {
		var o = Drag.obj = this;
		e = Drag.fixE(e);
		var y = parseInt(o.vmode ? o.root.style.top  : o.root.style.bottom);
		var x = parseInt(o.hmode ? o.root.style.left : o.root.style.right );
		o.root.onDragStart(x, y);

		o.lastMouseX	= e.clientX;
		o.lastMouseY	= e.clientY;

		if (o.hmode) {
			if (o.minX != null)	o.minMouseX	= e.clientX - x + o.minX;
			if (o.maxX != null)	o.maxMouseX	= o.minMouseX + o.maxX - o.minX;
		} else {
			if (o.minX != null) o.maxMouseX = -o.minX + e.clientX + x;
			if (o.maxX != null) o.minMouseX = -o.maxX + e.clientX + x;
		}

		if (o.vmode) {
			if (o.minY != null)	o.minMouseY	= e.clientY - y + o.minY;
			if (o.maxY != null)	o.maxMouseY	= o.minMouseY + o.maxY - o.minY;
		} else {
			if (o.minY != null) o.maxMouseY = -o.minY + e.clientY + y;
			if (o.maxY != null) o.minMouseY = -o.maxY + e.clientY + y;
		}

		document.onmousemove	= Drag.drag;
		document.onmouseup		= Drag.end;

		return false;
	},

	drag : function(e){
		if(!gwinIsDrag) return false;
		e = Drag.fixE(e);
		var o = Drag.obj;

		var ey	= e.clientY;
		var ex	= e.clientX;
		var y = parseInt(o.vmode ? o.root.style.top  : o.root.style.bottom);
		var x = parseInt(o.hmode ? o.root.style.left : o.root.style.right );
		var nx, ny;

		if (o.minX != null) ex = o.hmode ? Math.max(ex, o.minMouseX) : Math.min(ex, o.maxMouseX);
		if (o.maxX != null) ex = o.hmode ? Math.min(ex, o.maxMouseX) : Math.max(ex, o.minMouseX);
		if (o.minY != null) ey = o.vmode ? Math.max(ey, o.minMouseY) : Math.min(ey, o.maxMouseY);
		if (o.maxY != null) ey = o.vmode ? Math.min(ey, o.maxMouseY) : Math.max(ey, o.minMouseY);

		nx = x + ((ex - o.lastMouseX) * (o.hmode ? 1 : -1));
		ny = y + ((ey - o.lastMouseY) * (o.vmode ? 1 : -1));

		if (o.xMapper)		nx = o.xMapper(y);
		else if (o.yMapper)	ny = o.yMapper(x);

		Drag.obj.root.style[o.hmode ? "left" : "right"] = nx + "px";
		Drag.obj.root.style[o.vmode ? "top" : "bottom"] = ny + "px";
		Drag.obj.lastMouseX	= ex;
		Drag.obj.lastMouseY	= ey;

		Drag.obj.root.onDrag(nx, ny);
		return false;
	},

	end : function(){
		document.onmousemove = null;
		document.onmouseup   = null;
		Drag.obj.root.onDragEnd(	parseInt(Drag.obj.root.style[Drag.obj.hmode ? "left" : "right"]), 
									parseInt(Drag.obj.root.style[Drag.obj.vmode ? "top" : "bottom"]));
		Drag.obj = null;
	},

	fixE : function(e){
		if (typeof e == 'undefined') e = window.event;
		if (typeof e.layerX == 'undefined') e.layerX = e.offsetX;
		if (typeof e.layerY == 'undefined') e.layerY = e.offsetY;
		return e;
	}
}

var Win = {
	is_creat:false,
	pid:null,
	cid:null,
	txtid:null,
	time:0,
	sid:null,
	shtml:null,
	sObj:null,
	gCreat:0,
	init : function (Obj) {
		var html='<div id="popup" class="popup_wrap">'
				+'	<div class="popup_content">'
				+'		<div class="popup_main">'
				+'			<div class="popup_main2">'
				+'				<div id="pop_top" class="popup_title clear">'
				+'					<a href="javascript:void(0)" class="fr"><img id="pop_close_ico" src="' + version_img('close.gif') + '" alt="关闭" onclick="Win.close()" /></a>'
				+'				</div>'
				+'				<div id="pop_content"></div>'
				+'			</div>'
				+'		</div>'
				+'	</div>'
				+'	<div id="pop_bottom" class="popup_b"></div>'
				+'</div>' ;

		if(this.is_creat) {
			this.pid.style.display="block";
			this.cid.style.display="block";
			return;
		}		
		var pos=this.pos(Obj.width, Obj.height);
		var popMask = document.createElement('div');
		popMask.id = "WinMask";
		popMask['style']['position'] = "absolute";
		popMask['style']['display']	= "block";		
		popMask['style']['opacity']	= "0.5";
		popMask['style']['filter'] = "alpha(opacity=50)";
		popMask['style']['background']	= "#999";
		popMask['style']['zIndex'] = "9990";
		popMask['style']['top'] = "0";
		popMask['style']['left'] = "0";
		popMask['style']['width'] = pos.ww+"px";
		popMask['style']['height'] = pos.wh+"px";
		this.pid=popMask;

		var popCont = document.createElement('div');
		popCont.id = "WinDiv";
		popCont['style']['position'] = "absolute";
		popCont['style']['display'] = "block";
		popCont['style']['zIndex'] = "9999";
		popCont['style']['top'] = pos.wt+"px";
		//popCont['style']['width']="100%";
		//popCont['style']['left'] = pos.wl+"px";
		this.cid=popCont;
		//popCont['style']['width']= "300px";
		//popCont['style']['height']= "100px";
		popCont.innerHTML=html;
		var wBody = document.body;
		wBody.appendChild(popMask);
		wBody.appendChild(popCont);
		this.gCreat = parseInt((new Date).getTime()/1000);
		this.txtid=$I("pop_content");
		window.onresize=function(){
			if(parseInt((new Date).getTime()/1000) - Win.gCreat < 3) return;
			if(Win.is_creat) {
				Win.setpos();
			}
		};
		if (window.navigator.userAgent.indexOf("MSIE") > -1) {
				var ver=window.navigator.appVersion.indexOf("MSIE");
				if(window.navigator.appVersion.substr(ver+5,1)<=6) gHideSelects = true;
		}
		
		//Drag.init($I("pop_top"), popCont);
	},
	pos : function(width, height) {
		if (width == null || isNaN(width)) {
			width = this.txtid.offsetWidth;
		}
		if (height == null || isNaN(height)) {
			height = this.txtid.offsetHeight;
		}	
		var db = document.body;
		var de = document.documentElement;
		var ws = window.screen;
		var fullHeight;
		var fullWidth;
		var left=0;
		var topheight=0;
		var eh=0;
		fullWidth=Math.max(db.scrollWidth,de.scrollWidth);
		fullHeight=Math.max(db.scrollHeight,de.scrollHeight);
		if(fullHeight<de.offsetHeight) fullHeight=de.offsetHeight;
		
		left=de.scrollLeft;
		topheight=db.scrollTop||de.scrollTop;
		eh=Math.max(db.clientHeight,de.clientHeight);

		//alert(eh+" "+ws.availHeight);
		if(eh==0) eh=db.clientHeight;
		else if(eh<30) eh=de.clientHeight;
		if(height < 200) height = 150;
		
		if(ws.availHeight>=eh) 
			topheight+=(eh-height)/2-70;
		else topheight+=(ws.availHeight-height)/2-70;
		//if(!$.browser.msie) {alert(ws.availHeight+" "+db.clientHeight+" "+de.clientHeight+" "+height+" "+topheight);}
		if(parseInt(topheight)<=0) topheight=80;
		if(topheight<100 && height<500) {
			topheight = 80;
		}
		left = left + (de.offsetWidth - width)/2;

		return {wh:fullHeight,ww:fullWidth,wt:topheight,wl:left};
		
	},
	close : function (status) {
		this.cid.style.display="none";
		this.pid.style.display="none";
		if (gHideSelects == true) {
			hideSelectBoxes("visible");
		}
		// callback
		if(this.sObj.enter!="" && status==true) {
			var gRetEnter=this.sObj.enter;
			if(typeof gRetEnter=="function") {
				gRetEnter(this.sObj);
			}
			else eval(gRetEnter);
		}
		else if(this.sObj.cancel!="") {
			var gRetEnter=this.sObj.cancel;
			if(typeof gRetEnter=="function") {
				gRetEnter(this.sObj);
			}
			else eval(gRetEnter);
		};
		if(this.sid) {
			$I(this.sid).innerHTML=this.shtml;
			this.sid=null;
		}
	},
	setpos: function (o) {
		if(typeof o == "object" && o.height) {
			var pt=this.pos(o.width, o.height);
		}
		else {
			var pt=this.pos(this.txtid.offsetWidth, this.txtid.offsetHeight);
		}
		this.pid.style.width = pt.ww + "px";
		this.pid.style.height = pt.wh + "px";
		this.cid.style.top=pt.wt+"px";
		this.cid.style.left=pt.wl+"px";
	},
	cont: function (Obj) {
		var ent='<a onclick="Win.close(true)" class="btn1">确 定</a>';
		var can_b='<a onclick="Win.close()" class="btn1">取 消</a>';
		var can='<a href="javascript:void(0)" onclick="Win.close()">取 消</a>';
		var cont='';
		
		var entName='';
		if(Obj.enterName != "" && Obj.enterName != undefined){
			entName=Obj.enterName;
		}
		else{
			entName='确 定';
		}

		var entClass='';
		if(Obj.enterClass != "" && Obj.enterClass != undefined){
			entClass=Obj.enterClass;
		}
		else{
			entClass='btn1';
		}

		ent='<a onclick="Win.close(true)" class="' + entClass + '">' + entName + '</a>';

		if(Obj.btn != "" && Obj.btn != undefined){
			ent=Obj.btn;
		}

		switch(Obj.type) {
			case "info":
				cont='<div class="popup_c"><div class="popup_s_info">'+Obj.msg+'</div><div class="opt">'+ent+'</div></div>';
				break;
			case "alert":
				cont='<div class="popup_c"><div class="popup_s_alert">'+Obj.msg+'</div><div class="opt">'+ent+'</div></div>';
				break;
			case "confirm":
				cont='<div class="popup_c"><div class="popup_s_confirm">'+Obj.msg+'</div><div class="opt">'+ent+' &nbsp; 或 &nbsp; '+can+'</div></div>';
				break;
			case "cancel":
				cont='<div class="popup_c"><div class="popup_s_cancel">'+Obj.msg+'</div><div class="opt">'+can_b+'</div></div>';
				break;
			case "warn":
				cont=Obj.msg;
				break;
			default:
				cont=Obj.msg;
		}

		//$I("pop_top_title").innerHTML=Obj.title;
		this.txtid.innerHTML=cont;
		
	},
	dialog : function(Obj) {
		if(typeof(Obj)!="object") {
			Obj={msg:Obj};
		}
		if(Obj.msg=='') return;

		if(Obj.drag==false) gwinIsDrag=false;
		else gwinIsDrag=true;

		if(this.time>0) {
			window.clearTimeout(this.time);
			this.time=0;
		}

		this.sObj=Obj;
		if(Obj.msg.substring(0,1)=='#') {
			this.sid=Obj.msg.substring(1);
			Obj.msg=$I(this.sid).innerHTML;
			this.shtml=Obj.msg;
			$I(this.sid).innerHTML="";
		}
		Obj.width=Obj.width||400;
		Obj.height=Obj.height||200;
		Obj.msg=Obj.msg||'';
		Obj.type=Obj.type||'other';
		Obj.title=Obj.title||'';
		this.init(Obj);
		this.cont(Obj);
		if(gHideSelects) {
			hideSelectBoxes("hidden");
		}
		if(parseInt(Obj.timeout)>0) {
			this.time=window.setTimeout('Win.close()',Obj.timeout);
		}

		$I("popup").style.width=Obj.width+"px";
		//$I("popup").style.height=Obj.height+"px";

		//$I("pop_top").style.width=Obj.width-28+"px";
		//$I("pop_bottom").style.width=Obj.width-28+"px";

		var cont=$I("pop_content");
		//cont.style.height=Obj.height-70+"px";
		//cont.style.width=Obj.width-30+"px";

		this.setpos(Obj);
		//获得焦点
		var obj=cont.getElementsByTagName("INPUT");
		var objsubmit="", objtext="";
		for(var k=0; k<obj.length; k++) {
			if(obj[k].type=="button" && obj[k].parentNode.style.display=="") {
				obj[k].focus();
				objsubmit=obj[k];
				break;
			}
			else if(obj[k].type=="text" && obj[k].parentNode.style.display=="") {
				objtext=obj[k];
			}
		}
		if(obj.length == 0) {
			var obj_a=cont.getElementsByTagName("A");
			for(var m=0; m<obj_a.length; m++) {
				if(obj_a[m].className == "btn1") {
					obj_a[m].focus();
					objsubmit=obj_a[m];
					break;
				}
			}
		}
		if(typeof Obj.focus!='undefined'&& Obj.focus!='') {
			objsubmit=$I(Obj.focus);
			objsubmit.focus();
		}
		if(objtext && objsubmit) {
			objtext.select();
			objtext.onkeyup=function(e) {
				if((e || window.event).keyCode==13){
					objsubmit.click();
				}
			}
		}
		
		if(Obj.noclose == true)
		{
			$I("pop_close_ico").style.display='none';
		}
		else
		{
			$I("pop_close_ico").style.display='';
		}
		this.is_creat=true;
	}
};

function get_url_val(val) {
	var str_arr=location.search.split("&");
	for(var i=0; i<str_arr.length; i++) {
		var sp=str_arr[i].split("=");
		if(sp[0]==val) return sp[1];
	}
	return false;
}

String.prototype.trim = function() {
	return this.replace(/(^\s*)|(\s*$)/g, "");
}
/*
String.prototype.substr = function (len){
	if(!this || !len) { return ''; }
	var a = 0, i=0;
	var temp = '';
	for (i=0;i<this.length;i++) {
		if (this.charCodeAt(i)>255) { a+=2;}
		else{ a++;}
		if(a > len) { return temp; }
		temp += this.charAt(i);
	}
	return this;
}
*/
//计算长度
function str_len(fData)
{
	var intLength=0;
	for (var i=0;i<fData.length;i++) {
		if ((fData.charCodeAt(i) < 0) || (fData.charCodeAt(i) > 255))
			intLength=intLength+2;
		else
			intLength=intLength+1;   
	}
	return intLength;
}

function get_pos(e){
	var t=e.offsetTop;
	var l=e.offsetLeft;
	while(e=e.offsetParent){
		t+=e.offsetTop;
		l+=e.offsetLeft;
	}
	return {top:t,left:l};
}

var regEnum = 
{
	intege:"^([+-]?)\\d+$",					//整数
	intege1:"^([+]?)\\d+$",					//正整数
	intege2:"^-\\d+$",						//负整数
	num:"^([+-]?)\\d*\\.?\\d+$",			//数字
	num1:"^([+]?)\\d*\\.?\\d+$",			//正数
	num2:"^-\\d*\\.?\\d+$",					//负数
	decmal:"^([+-]?)\\d*\\.\\d+$",			//浮点数
	decmal1:"^([+]?)\\d*\\.\\d+$",			//正浮点数
	decmal2:"^-\\d*\\.\\d+$",				//负浮点数
	email:"^\\w+((-\\w+)|(\\.\\w+))*\\@[A-Za-z0-9]+((\\.|-)[A-Za-z0-9]+)*\\.[A-Za-z0-9]+$", //邮件
	color:"^[a-fA-F0-9]{6}$",				//颜色
	url:"^http[s]?:\\/\\/([\\w-]+\\.)+[\\w-]+([\\w-./?%&=]*)?$",	//url
	chinese:"^[\\u4E00-\\u9FA5\\uF900-\\uFA2D]+$",					//仅中文
	numletterzh:"^[0-9a-zA-Z\\u4E00-\\u9FA5\\uF900-\\uFA2D\\s\(\)]+$",	//数字字母中文
	letterzh:"^[a-zA-Z\\u4E00-\\u9FA5\\uF900-\\uFA2D]+$",			//字母中文
	ascii:"^[\\x00-\\xFF]+$",				//仅ACSII字符
	zipcode:"^\\d{6}$",						//邮编
	mobile:"^(13|15)[0-9]{9}$",				//手机
	ip4:"^(\\d{1,2}|1\\d\\d|2[0-4]\\d|25[0-5]).(\\d{1,2}|1\\d\\d|2[0-4]\\d|25[0-5]).(d{1,2}|1\\d\\d|2[0-4]\\d|25[0-5]).(\\d{1,2}|1\\d\\d|2[0-4]\\d|25[0-5])$",				//ip地址
	notempty:"^\\S+$",						//非空
	picture:"(.*)\\.(jpg|bmp|gif|ico|pcx|jpeg|tif|png|raw|tga)$",	//图片
	rar:"(.*)\\.(rar|zip|7zip|tgz)$",								//压缩文件
	date:"^\\d{4}(\\-|\\/|\.)\\d{1,2}\\1\\d{1,2}$",					//日期
	qq:"^[1-9]*[1-9][0-9]*$",				//QQ号码
	tel:"(\\d{3}-|\\d{4}-)?(\\d{8}|\\d{7})",	//国内电话
	username:"^\\w+$",						//用来用户注册。匹配由数字、26个英文字母或者下划线组成的字符串
	account:"^[a-zA-Z][a-zA-Z0-9_]{1,14}[a-zA-Z0-9]$", //注册用户
	letter:"^[A-Za-z]+$",					//字母
	letter_u:"^[A-Z]+$",					//大写字母
	letter_l:"^[a-z]+$",					//小写字母
	idcard:"^[1-9]([0-9]{14}|[0-9]{17})$"	//身份证
}
//正则表达式判断
String.prototype.exp=function(reg) {
	return (new RegExp(reg,"ig").test(this));
}

function str_to_safe(str)
{
	str = str.replace(/\"/g,'＂');
	str = str.replace(/\'/g,'＇');
	str = str.replace(/</g,'＜');
	str = str.replace(/>/g,'＞');
	return str.replace(/\\/g,'＼');
}

//去除所有的空格(并且以空格开头和结束的也去掉)
function comm_str_trim(str){
	if('' != str){
		var str = str.replace(/ {1,}/g,'');
		var str = str.replace(/(.*)(\s$)/g, "$1");
		return str.replace(/(^\s)(.*)/g, "$2");
	}else{
		return '';
	}
}

document.onload=core_init;
