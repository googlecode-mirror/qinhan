var g_staticUrl = 'http://pic.dxslaw.com/CDN/app';
function version_img(i){
	return g_staticUrl + '/img/' + i;
}
var gRange = "";
var gCurPos = "";
function setRange(obj) {
	delete gRange;
	try {
		if($.browser.msie) {
			gRange = document.selection.createRange();
		}
		else {
			gRange = window.getSelection().getRangeAt(0);
		}
	}
	catch (e) {
		obj.focus();
	}

	if(obj.tagName.toLowerCase() == "textarea") {
		gCurPos = getCursorPostion(obj);
	}
	else {
		// 没有解决div 获得光标位置的问题
		gCurPos = {start:gRange.startOffset,end:gRange.endOffset};
	}
	gCurPos.top = obj.scrollTop;
}
function getCursorPostion(textBox) {
    var start = 0, end = 0;
    if(typeof(textBox.selectionStart) == "number"){
        start = textBox.selectionStart;
        end = textBox.selectionEnd;
    }
    else if(document.selection) {
        var range = document.selection.createRange();
        if(range.parentElement().id == textBox.id) {
            var range_all = document.body.createTextRange();
            range_all.moveToElementText(textBox);
            for (start=0; range_all.compareEndPoints("StartToStart", range) < 0; start++)
                range_all.moveStart('character', 1);
                for (var i = 0; i <= start; i ++) {
                if (textBox.value.charAt(i) == '/n')
                    start++;
            }
            var range_all = document.body.createTextRange();
            range_all.moveToElementText(textBox);
            for (end = 0; range_all.compareEndPoints('StartToEnd', range) < 0; end ++) {
                range_all.moveStart('character', 1);
            }
            for (var i = 0; i <= end; i ++) {
                if (textBox.value.charAt(i) == '/n')
                    end ++;
            }
        }
    }
    return { "start": start, "end": end, "item": [start, end] };	
}
//设置光标位置
function setCursorPosition(ctrl, pos){ 
	if(ctrl.setSelectionRange){ 
		ctrl.focus(); 
		ctrl.setSelectionRange(pos,pos); 
	} 
	else if (ctrl.createTextRange) { 
		var range = ctrl.createTextRange(); 
		range.collapse(true); 
		range.moveEnd('character', pos); 
		range.moveStart('character', pos); 
		range.select(); 
	} 
	else if(!$.browser.msie){
		ctrl.focus();
		var range,selection;
		range = document.createRange();
		range.selectNodeContents(ctrl);
		range.collapse(false);
		selection = window.getSelection();
		selection.removeAllRanges();
		selection.addRange(range);
	}
	if(gCurPos.top) {
		ctrl.scrollTop = gCurPos.top;
	}
} 
function faceTo51All(a,face_h)
{
	if(typeof(a)!="string"||a=="")
	{
		throw (new Error(-1,"创建类实例的时候请把类实例的引用变量名传递进来！"));
	}
	var b=this;
	this.name=a;
	this.face_h = face_h;
	this.isInit=false;
	this.titles=
	{
		mr:["","微笑","可爱","吐舌头","龇牙","偷笑","大笑","鼓掌","捂眼","色迷迷","财迷","晕","感动",
		    "酷","大惊","糗","哈欠","难过","眼花","痛","快哭了","抓狂","可怜","泪汪汪","流泪",
		    "大哭","寒","汗水","瀑布汗","惊恐","尴尬","怒","咒骂","闭嘴","哼","便便","衰",
		    "鄙视","不屑","挖鼻孔","害羞","媚眼","吐","阴险","坏笑","鼻血","敲打","鼻涕","嘘",
		    "惊讶","白眼","疑问","睡了","拜拜","拥抱","亲爱的","亲","手机","西瓜","骷髅","猪",
		    "足球","月亮","太阳","闪电","彩虹","示爱","爱心","心碎","玫瑰","凋谢","蛋糕","棒棒糖",
		    "饭","勾引","OK","胜利","握手","强","弱","咖啡","酒","磨刀","刀子"],
		tsj:["","砍","摇头","高兴","音乐","碎砖","开小差","生日快乐","爬墙","雷到了","东张西望","拜拜","醒醒","晃","砸","猛晃","星星","光芒万丈","宝贝","睡了","示爱","差得远呢","扭腰","腹黑","顶","左便便","右便便","左鄙视","左杂耍","右鄙视","右杂耍","左囧囧","右囧囧","练功","鄙","顶","萌","视","谦让","按摩","左星星","右星星","左星星","黑锅你背","得意","魔术","左晃右晃","右晃左晃","炫耀","火星","倒霉","哭","自残","自由自在","接着睡","悠闲","高兴","撞墙","摇头","擦汗"],
		kb:["","怒","拥抱","示爱","寒","拜拜","晕","疑问","得意","撞墙","口水","郊游","衰","摇头","抓狂","偷瞄","害羞","飘过","傲娇","臭美","耶","灰机","泪汪汪","小期待","跳舞","转圈","离开","惊讶","汗","发愁","大哭","流泪","东张西望","偷吃","大笑","黑眼圈","大惊","挠头","白天路过","鼻涕","报到","紧张","摔","捶胸","妻管严","大期待","睡了","盼望","醉了","逃跑","扭腰","等累了","悠闲","哇哦","吐舌头","抖","吃萝卜","HIGH","飞吻","外遇","低落","音乐","左出现","晚上路","憨笑","心动","冷汗","亲","偷笑","无聊"]
	};
	
	
	this.butt=null;
	this.otxt=null;
	this.ontab=null;
	this.onclass="mr";
	this.onimg="";
	this.isIE=jQuery.browser.msie;
	this.isFF=jQuery.browser.mozilla;
	this.isGG=/Chrome/.test(navigator.userAgent);
	this.isIE6=jQuery.browser.version==6;
	this.isIE7=jQuery.browser.version==7;
	this.isIE8=jQuery.browser.version==8;
	
	/*
	 * _id		ID
	 * _type	_div	_areatext
	 */
	this.init=function(_id,_type)
	{
		if(this.isInit){return;}

		document.body.insertAdjacentHTML("beforeEnd",'			<div id="public_51face_box" class="expression" style="display:none;position:absolute;z-index:1198;width:397px;height:297px;background:#fff;border:1px solid #aaa">				<ul id="public_51face_tab" style="background: url(\'' + version_img('exp_03.png') + '\') repeat scroll 0 0 transparent;border-left: 1px solid #CCCCCC;border-right: 1px solid #CCCCCC;height: 33px;overflow:hidden;list-style-type:none;">					<li class="changeli" id="public_51face_tab_def">系统表情</li>				<li>兔斯基</li>					<li>可白</li>					 <li class="close" onclick="'+this.name+'.hide();"><img src="' + version_img('exp_09.jpg') + '"/></li>				</ul>				<div style="clear:both; display:block; height:0;font-size:0/0; overflow:hidden; margin:0 auto; padding:0; border:0; background-color:#fff;"></div>				<div>					<div id="public_51face_img_div" style="width:397px; height:265px; cursor:pointer;cursor:hand;margin:-1px auto 0 auto;" onmousemove="'+this.name+'.moveFaceEvent(event);" onmouseout="'+this.name+'.moveFaceOutEvent(event)">						<img id="public_51face_img" src="' + 'http://pic.dxslaw.com/CDN/app/face/mr.png' + '" width="397" height="265" border="0" />					</div>				</div>				<div id="public_51face_select_box" style="display:none;position:absolute;border:1px #f00 solid;width:32px;height:32px;background:transparent;"><div id="public_51face_title_box" style="filter:alpha(opacity=0);-moz-opacity:0;opacity:0;background:red;height:100%;width:100%;"></div></div>				<table id="public_51face_show_left_box" style="display:none;left:2px;position:absolute;top:33px;width:64px!important;height:64px;border:1px #777 solid;background:#fff;" width="64" height="64" border="0" cellspacing="0" cellpadding="0"><tr><td align="center" valign="middle"><img id="public_51face_show_left_img" src="http://pic.dxslaw.com/CDN/app/face/mr/162.gif" /></td></tr></table>				<table id="public_51face_show_right_box" style="display:none;right:2px;position:absolute;top:33px;width:64px!important;height:64px;border:1px #777 solid;background:#fff;" width="64" height="64" border="0" cellspacing="0" cellpadding="0"><tr><td align="center" valign="middle"><img id="public_51face_show_right_img" src="http://pic.dxslaw.com/CDN/app/face/mr/162.gif" /></td></tr></table>			</div>');

		jQuery("li","#public_51face_tab").click(function(h){
			h=h||event;
			var f=h.srcElement;
			var g=jQuery(f).text().trim();
			var d="";
			var c="mr";
			switch(g)
			{
				case"系统表情":
					d="mr.png";
					c="mr";
				break;

				case"兔斯基":
					d="tsj.png";
					c="tsj";
				break;

				case"可白":
					d="kb.png";
					c="kb";
				break;
				
				default:d="";
			}
		
			if(d==""){return;}
			b.gID("public_51face_img").src='http://pic.dxslaw.com/CDN/app/face/'+d;
			
			if(b.ontab==null)
			{
				b.ontab=b.gID("public_51face_tab_def");
			}

			b.onclass=c;						//this.onclass = face8 or kb or
			$("li").removeClass("changeli");
			$(this).addClass("changeli");
		});
		
		jQuery("#public_51face_img_div,#public_51face_select_box").click(function(d){
			d=d||event;
			var c=b.onimg;
			if(c==""){return;}
			b.faceInstallIco(c,_id,_type);
		});
		
		 
		this.isInit=true;
		return;
	};

	/*
	 * f	this
	 * d	ID
	 * z	_div _areatext
	 */
	this.show=function(f,d,z,g,p){
		this.gNameId = d;
		if(!this.isInit){this.init(d,z);}

		if(jQuery("#public_51face_box").is(":visible"))
		{
			return this.hide();
		}
		
		if(typeof f!="object"){return window.alert("参数错误.");}
		if(d=="")
		{
			return window.alert("参数错误 .");
		}else{
			this.otxt=this.gID(d);
			if(!this.otxt)
			{				
				return window.alert(d+"DOM不存在.");
			}
			if(!jQuery(this.otxt).attr("caretPos"))
			{
				jQuery(this.otxt).attr("caretPos",{text:""});
			}
		}
		
		this.butt=f;
		if(jQuery(this.otxt).attr("isfaceevent")!="1")
		{
			jQuery(this.otxt).attr("isfaceevent",1);
		}
		jQuery(document).bind("mousedown",this.faceDocumentClick);
		var e=jQuery(this.butt).offset();
		var c=this.butt.offsetHeight;
		var hh = this.isIE8?this.face_h:0;
		var t=(p=="" || p== undefined) ? (e.top-300-hh) : (e.top+30);
		jQuery("#public_51face_box").css({top:(t),left:e.left+1}).show();};
		
		this.hide=function()
		{
			jQuery(document).unbind("mousedown",this.faceDocumentClick);
			this.butt=null;
			this.otxt.removeAttribute("caretPos");
			this.otxt=null;
			jQuery("#public_51face_box").hide();
			this.faceEventclear();
		};
		
		this.faceDocumentClick=function(d)
		{
			d=d||event;
			var c=d.srcElement;
			if(b.gID("public_51face_box").contains(c)||c==b.gID("public_51face_select_box")||c==b.butt){return;}
			b.hide();
		};
		//b.faceInstallIco(c,_id,_type);
		/*
		 * z	_div	_textarea
		 * d	ID
		 * g	图片路径
		 */
		this.faceInstallIco=function(g,_id,_type)
		{
			_id = this.gNameId;
			var obj = $("#"+_id)[0];
			if(obj.tagName.toLowerCase() != "textarea") {
				var img_html = '<img src="'+g+'" class="ico">';
				this.format(_id, img_html);
			}
			else {
				g = g.replace('http://pic.dxslaw.com/CDN/app/face/','');
				var img_html ="[img]"+g+"[/img]";
				this.textInsert(_id, img_html);
			}
			
			this.hide();
		};
		this.textInsert = function(id, html) {
			var txt = $("#"+id)[0];
			if(gCurPos) {
				var source = txt.value; 
				txt.value = source.slice(0, gCurPos.start) + html + source.slice(gCurPos.end);
				setCursorPosition(txt, gCurPos.start+html.length);
				gCurPos = "";
			}
			else {
				txt.value += html;
			}
		}
		this.format = function (id, html) {
			if($("#"+id).html() == "") {
				$("#"+id).focus().html(html);
				setCursorPosition($("#"+id)[0], html.length);
				setRange($("#"+id)[0]);
				return;
			}
			if(gRange != null) {
				try{
					if($.browser.msie) {
						gRange.select();
						gRange.pasteHTML(html);
					}
					else {
						$("#"+id).focus();
						var temp = document.createElement("DIV");
						temp.innerHTML = html;

						var elems = [];
						for (var i = 0; i < temp.childNodes.length; i++)
						{
							elems.push(temp.childNodes[i]);
						}
						gRange.deleteContents();

						for (var i in elems) {
							temp.removeChild(elems[i]);
							gRange.insertNode(elems[i]);
						}

					}
					gRange = null;
				}catch(e) {$(html).appendTo("#"+id);}
			}
			else $(html).appendTo("#"+id);
			if(gCurPos) {
				setCursorPosition($("#"+id)[0], gCurPos.start+html.length);
			}
		}
		this.faceEventclear=function(){
			this.onimg="";
			jQuery("#public_51face_show_left_box,#public_51face_show_right_box,#public_51face_select_box").hide();
		};
		
		this.moveFaceOutEvent=function(d){
			d=d||event;
			var c=d.toElement;
			if(c==this.gID("public_51face_select_box")||c==this.gID("public_51face_title_box")){return;}
			this.faceEventclear();
		};
		
		this.moveFaceEvent=function(g){
			g=g||event;
			var h=this.isFF?g.offsetY-27:g.offsetY;
			var i=g.offsetX;
			var c=Math.floor((i+Math.floor(i/35))/35)+1;	//横向
			var l=Math.floor((h+Math.floor(h/36))/36)+1;	//纵向	1*33-2+1
	
			var k=(l-1)*12+c;
			var m=this.onclass;
			var j=this.titles[m][k];
			
			if(k>0&&c<13)
			{
				jQuery("#public_51face_select_box").css({top:(l*32-1+l),left:((c-1)*33+(!this.isIE?0:0))}).show();				
				jQuery("#public_51face_title_box").attr("title",j);
				if(m == 'mr')
				{
					var d=83;
				}

				if(m == 'kb')
				{
					var d=69;
				}

				if(m == 'tsj')
				{
					var d=59;
				}

				if(k>d)
				{
					this.faceEventclear();
					return;
				}
				
				//k=m=="face8"?(k+201):k;
				k=100+k;
				var f='http://pic.dxslaw.com/CDN/app/face/'+m+"/"+(k-1)+".gif";
				this.onimg=f;
				if(c<4)
				{
					jQuery("#public_51face_show_left_box").hide();
					jQuery("#public_51face_show_right_img").attr("src",f);
					jQuery("#public_51face_show_right_box").show();
				}else{
					jQuery("#public_51face_show_right_box").hide();
					jQuery("#public_51face_show_left_img").attr("src",f);
					jQuery("#public_51face_show_left_box").show();
				}
				
			}else{
				this.faceEventclear();
			}
		};
		
		this.faceSetCaret=function(c){
			if(c.createTextRange)
			{
				c.setAttribute("caretPos",document.selection.createRange().duplicate());
			}
		};
		
		this.gID=function(c){
			return typeof(c)!="string"?c:document.getElementById(c);
		};
}
if(!face_h)
{
	var face_h = 0;
}
var face51New=new faceTo51All("face51New",face_h);/* 2010-08-25 14:54:26 */ 

//点击显示用的function 
if(typeof HTMLElement != "undefined" && !jQuery.browser.opera && !jQuery.browser.msie){
    try{
        Event.prototype.__defineSetter__("returnValue", function(b){if(!b)this.preventDefault(); return b;});
        Event.prototype.__defineGetter__("offsetX", function(){return this.layerX;});
        Event.prototype.__defineGetter__("offsetY", function(){return this.layerY;});
    //  Event.prototype.__defineGetter__("srcElement", function(){var n=this.target;try{while(1!=n.nodeType)n=n.parentNode;}catch(_e){};return n;});
        Event.prototype.__defineGetter__("srcElement", function(){var n=this.target;while(n.nodeType!=1){n=n.parentNode}return n;});
        Event.prototype.__defineGetter__("srcElement", function(){return this.target;});
        Event.prototype.__defineGetter__("fromElement",function(){var node;if(this.type=="mouseover"){node=this.relatedTarget;}else if(this.type=="mouseout"){node=this.target;}if(!node){return;}while(node.nodeType!=1){node=node.parentNode;}return node;});
        Event.prototype.__defineGetter__("toElement",function(){var node;if(this.type=="mouseout"){node=this.relatedTarget;}else if(this.type=="mouseover"){node=this.target;}if(!node){return;}while(node.nodeType!=1){node=node.parentNode;}return node;});

        HTMLElement.prototype.contains=function(oNode){if(!oNode){return false;}do if(oNode==this){return true;}while(oNode=oNode.parentNode){return false;}};
        HTMLElement.prototype.insertAdjacentHTML=function(where,html){var e=this.ownerDocument.createRange();e.setStartBefore(this);e=e.createContextualFragment(html);this.insertAdjacentElement(where,e);};
        HTMLElement.prototype.insertAdjacentElement=function(where,e){switch(where){case 'beforeBegin':this.parentNode.insertBefore(e,this);break;case 'afterBegin':this.insertBefore(e,this.firstChild);break;case 'beforeEnd':this.appendChild(e);break;case 'afterEnd':if(!this.nextSibling){this.parentNode.appendChild(e);}else{this.parentNode.insertBefore(e,this.nextSibling);}break;}};
    }catch(e){
    }
}