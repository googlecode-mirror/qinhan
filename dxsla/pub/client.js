var startup = false;
var predata = null;
function qh_$(id) {
	return document.getElementById(id);
}
function ajaxpost(url, data, callback, domain) {
    var xhr = null, isTimeout = false;
    if(domain) {
	    xhr = window.frames["ajaxIframe"].GetDomainRequest();
    } else {
        if (window.XMLHttpRequest) {
            xhr = new XMLHttpRequest();
        } else if (window.ActiveXObject) {
            xhr = new ActiveXObject('Microsoft.XMLHTTP');
        }
    }
	xhr.open("post", url);
	xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	xhr.send('t=' + new Date().getTime() + '&' + data);
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4) {
			if (xhr.status == 200 && (!isTimeout || !domain)) {
				callback(xhr.responseText);
			}
		}
	}
	setTimeout(function() {
        isTimeout = true;
	}, 1000);
}
function connect() {
	ajaxpost(connect_url, '', function(data) {
		if(!startup) {
			startup = true;
			predata = data;
			return false;
		}
		if(predata != data && data) {
			predata = data;
			data = data.substr(21);
			var $json = null;
			try {
				eval('json = ' + data);
			} catch(e) {}
			if(json) {
				qh_$('chat_msg_show').innerHTML = qh_$('chat_msg_show').innerHTML + msgHtml(json, 0);
				qh_$('messages').scrollTop = qh_$('messages').scrollHeight;
			}
		}
	}, false);
}
function post() {
	var content = qh_$('chat_msg').innerHTML.replace(/(^\s*)|(\s*$)/g, "");
	content = cont_filter(content);
	content = content.replace(/(^\s*)|(\s*$)/g, "");
    if(content == '' || content == ' ') return false;
	if(/13\d{9}|15\d{9}|18\d{9}/.test(content)) {
		sys_notify('请不要轻易告诉对方您的手机号码');
		return false;
	}
	if(/\d{5,10}/.test(content) && /qq|QQ|\u6263\u6263|\u52a0q|\u52a0Q/.test(content)) {
		sys_notify('请不要轻易告诉对方您的QQ号');
		return false;
	}
    qh_$('chat_msg').innerHTML = '';
    if(content.length > 250) return false;
    ajaxpost(post_url, 'content=' + content, function(){}, false);
	qh_$('postcontent').value = content;
	try {
		qh_$('postForm').submit();
	} catch(e) {}
    qh_$('chat_msg_show').innerHTML = qh_$('chat_msg_show').innerHTML + msgHtml({'content':exp_content(content), 'add_time':nowTime()}, 1);
	qh_$('messages').scrollTop = qh_$('messages').scrollHeight;
}
function sys_notify($str) {
	var li = '<li><div class="textbg f_6">温馨提示：' + $str + '</div><li>';
	qh_$('chat_msg_show').innerHTML = qh_$('chat_msg_show').innerHTML + li;
	qh_$('chat_msg').innerHTML = '';
	qh_$('messages').scrollTop = qh_$('messages').scrollHeight;
}
function exp_content(content) {
	pattern = /\[img\](mr|tsj|kb)(\/\d{3}.gif)\[\/img\]/ig;
	replacement = "<img src=\"http://pic.dxslaw.com/CDN/app/face/$1$2\" />";
	content = content.replace(pattern, replacement);
	return content;
}
function cont_filter(dsc) {
	if(dsc === "" || !dsc) return '';
	var reg = /<img(.*?)src=(\"|\')http:\/\/pic.dxslaw.com\/CDN\/app\/face\/(.*?)(\"|\')(.*?)>/ig;
	dsc = dsc.replace(reg,"[img]$3[/img]");	
	var str = navigator.userAgent.toLowerCase();
	if(str.match(/version\/([\d.]+).*safari/)) {
		dsc = dsc.replace(/(<div>)|(<p>)|(<br>)|(<li>)/ig,"\r\n");
	} else {
		dsc = dsc.replace(/(<\/div>)|(<\/p>)|(<br\/?>)|(<\/li>)/ig,"\r\n");
	}
	dsc = dsc.replace(/<[^>]+>/ig,"");
	dsc = dsc.replace(/<script(.*?)>(.*?)<\/script>/ig,"");
	dsc = dsc.replace(/^(&nbsp;)*/,"").replace(/(&nbsp;)*$/,"");
	dsc = dsc.replace(/(&|＆)nbsp;/ig," ");
	dsc = dsc.replace(/(&|＆)lt;/gi,"<");
	dsc = dsc.replace(/(&|＆)gt;/gi,">");
	dsc = dsc.replace(/(&|＆)amp;/gi,"&");
	return dsc;
}
function gethistory(startnum) {
    ajaxpost(post_url, 'getdata=true&startnum=' + startnum, function(data) {
        qh_$('msg_history').innerHTML = data;
		if(!startnum) {
			qh_$('messages').scrollTop = qh_$('messages').scrollHeight;
		}
    }, false);
}
function msgHtml(data, is_me) {
	var user = is_me ? '我' : 'TA';
	var name = is_me ? 'me' : 'ta';
	var str = '<li><dl><dt class="' + name + '"><b>' + user + '</b> &mdash; ' + getLocalTime(data.add_time) + '</dt><dd>' + data.content + '</dd></dl></li>';
	return str;
}
Date.prototype.format = function(format) { 
	var o = { 
		"M+" : this.getMonth()+1, //month 
		"d+" : this.getDate(), //day 
		"h+" : this.getHours(), //hour 
		"m+" : this.getMinutes(), //minute 
		"s+" : this.getSeconds(), //second 
		"q+" : Math.floor((this.getMonth()+3)/3), //quarter 
		"S" : this.getMilliseconds() //millisecond 
	}
	if(/(y+)/.test(format)) { 
		format = format.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length)); 
	}
	for(var k in o) { 
		if(new RegExp("("+ k +")").test(format)) { 
		format = format.replace(RegExp.$1, RegExp.$1.length==1 ? o[k] : ("00"+ o[k]).substr((""+ o[k]).length)); 
		} 
	}
	return format; 
}
function getLocalTime(nS) {  
        return new Date(parseInt(nS) * 1000).format("yyyy-MM-dd hh:mm:ss");  
}
function nowTime() {  
	var timestamp = (new Date()).valueOf().toString().substr(0, 10);  
	return parseInt(timestamp);  
}
function getKeyCode() {
	var e = window.event || arguments.callee.caller.arguments[0];
    return e.keyCode;
}