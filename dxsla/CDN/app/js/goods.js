var goods_stealth;
var goods_receiver = 0;
var goods_ajax = 0;
var goods_is_alert = false;
/*
function goods_view(page) {
	Win.dialog({'msg':'<div class="progress">正在加载数据</div>','type':'warn','noclose':true,width:580,height:400});
	if(goods_ajax == 1) return;
	goods_ajax = 1;
	$.getJSON("/goods/page", {page:page}, function(data) {
		goods_ajax = 0;
		var g_html = '<div class="storage_box clear">'+data.html+'</div><div class="page_w">'+data.page+'</div>';
		Win.dialog({"msg":g_html,width:580,height:400});
		var goods_list = data.goods;
		$(".storage_box img").mousemove(function(e){
			var id = $(this).attr("id");
			$('.state_tip').html('<ul><li class="fb_13">主要作用：</li><li class="f_6">'+goods_list[id].effect+'</li><li class="m_t5">使用时长：'+goods_list[id].days+'天</li><li class="m_t5">价格：'+goods_list[id].price+'红豆</li></ul>').show().css({"top":e.pageY+20,"left":e.pageX+10});

		}).mouseout(function(e){
			$('.state_tip').hide();
		});
	});
}
function goods_check(gid, page, type) {
	Win.dialog({'msg':'<div class="progress">正在加载数据</div>','type':'warn','noclose':true,width:580,height:400});
	if(goods_ajax == 1) return;
	goods_ajax = 1;
	$.getJSON("/goods/check", {gid:gid,receiver:goods_receiver,page:page,type:type}, function(data) {
		goods_ajax = 0;
		Win.dialog({"msg":data.msg,width:580,height:300});
		$("#user_id").val(goods_receiver);
		show_userinfo();
	});
}
// 直接赠送或使用
function send_goods(gid, price, type) {
	if($("#user_id").val().length != 8) {
		$("#user_id").focus();
		alert("请输入正确的ID号");
		show_userinfo();
		return;
	}
	if($("#goods_memo").val().length > 100) {
		alert("说的内容最多100个汉字");
		$("#goods_memo").focus();
		return;
	};
	if(window.confirm(type?"确定对此用户使用吗？":"确定赠送给此用户吗？")) {
		var buy_num = $("#buy_num").val();
		var way = $("input[name='way']:checked").val();
		if(goods_ajax == 1) return;
		goods_ajax = 1;
		if(type == 3) var url = 'clear';
		else var url = 'sendorused';
		$.post('/goods/'+url,{num:buy_num,gid:gid,receiver:$("#user_id").val(),way:way,type:type,memo:$("#goods_memo").val()}, function(data) {
			goods_ajax = 0;
			if(data.stat == 200) {
				Win.dialog({msg:data.msg,type:'alert',enter:function() {location.reload(true);},cancel:function(){location.reload(true);}});
			}
			else {
				Win.dialog({msg:data.msg,type:'alert'});
			}
		}, 'json');
	}
}
*/
// 索要
function goods_extort(id,img,name) {
	var g_html = '<div class="pop_shopgift clear"><div class="clear">'+
		'<div class="pop_shopgift_l">'+
		'	<dl>'+
		'		<dt><img src="'+img+'" alt="" /></dt>'+
		'		<dd class="m_t10 fb_12 f_yelo">'+name+'</dd>'+
		'	</dl>'+
		'</div>'+
		'<div class="pop_shopgift_r">'+
		'	<dl class="clear">'+
		'		<dt class="fl">'+
		'			<p><span  class="fb_12 f_6">索要信息将发到您的好友动态里</span></p>'+
		'		</dt>'+
		'	</dl>'+
		'	<ul>'+
		'		<li class="m_t10 fb_12 f_6">索要数量：</li>'+
		'		<li><select id="buy_num" name="buy_num" class="input_1"><option selected="" value="1">1</option><option value="2">2</option><option value="3">3</option><option value="5">5</option><option value="10">10</option></select></li>'+
		'	</ul>'+
		'</div></div>'+
		'<div class="opt"><a class="btn1 btn_w" onclick="extort_ajax('+id+')" href="javascript:;">索要</a></div>'+
		'</div>';

	Win.dialog({msg:g_html,height:200,width:460});
}
function extort_ajax(id) {
	var buy_num = $("#buy_num").val();
	$.post('/index.php?s=/goods/extort',{num:buy_num,id:id}, function(data) {
		if(data.stat == 200) {
			Win.dialog({msg:data.msg,type:'alert'});
		}
		else {
			Win.dialog({msg:data.msg,type:'alert'});
		}
	}, 'json');
}

function goods_buy(id,price) {
	if(myuserinfo.card_num > 0) {
		var opt = '<a onclick="buy_ajax('+id+','+price+')" class="btn1 btn_b1" >购买</a>';
	}
	else {
		var opt = '<a href="/pay/order" class="btn1 btn_b1" target="_blank">充值</a>';
	}
	var g_html = '<div class="popup_c"><div class="popup_s_info"><p>购买数量：<select name="buy_num" id="buy_num" onchange="$(\'#price_num\').html(this.options[this.options.selectedIndex].value*'+price+')"><option value="1" selected>1</option><option value="2">2</option><option value="3">3</option><option value="5">5</option><option value="10">10</option></select><span class="f_6">(需要<b id="price_num" class="f_yelo">'+price+'</b>颗红豆)</span></p><div class="opt"><span class="f_9">您的红豆：</span><span class="f_r1">'+myuserinfo.card_num+'颗</span> &nbsp;&nbsp;'+opt+'  &nbsp; <a onclick="Win.close(false);" class="dashed">取 消</a></p></div></div>';
	Win.dialog({msg:g_html,height:300,width:400});
}

function buy_ajax(id, price) {
	var buy_num = $("#buy_num").val();
	if(myuserinfo.card_num < buy_num*price) {
		Win.dialog({type:'info',msg:'您的红豆不足，请先充值红豆',enter:function(){window.open("/pay/order");}});
		return;
	}
	if(goods_ajax == 1) return;
	goods_ajax = 1;
	$.post('/index.php?s=/goods/buy',{num:buy_num,id:id}, function(data) {
		goods_ajax = 0;
		if(data.stat == 200) {
			myuserinfo.card_num -= buy_num*price;
			var search = location.search.split("&");
			Win.dialog({msg:data.msg,type:'alert',enter:function(){location.href="/goods/my"+search[0];}});
		}
		else {
			Win.dialog({msg:data.msg,type:'alert'});
		}
	}, 'json');
}

function goods_toclear(id,img,name,total,price,memo,sid, uid, sex) {
	if(typeof myuserinfo != "object" || !myuserinfo.uid){
		 show_login_form();
		 return false ;
	}

	for(var i=1,num_html=''; i <= total; i++) {
		num_html += '<option value="'+i+'" selected>'+i+'</option>';
	}
	if(uid) goods_receiver = uid;

	if(goods_stealth) {
		var stealth_html = '<li><span class="f_6"><input name="way" type="checkbox" value="2"/> 是否匿名</span></li>';
	}
	else var stealth_html = '<li><span class="f_6">您想匿名使用吗？<a href="/goods" target="_blank" style="color:#000000">请先购买隐身衣</a></span></li>';

	if(uid == gUid) {
		var g_html = '<div class="pop_shopgift clear">'+
			'<div class="pop_shopgift_l">'+
			'	<dl>'+
			'		<dt><img src="'+img+'" alt="" /></dt>'+
			'		<dd class="m_t10 fb_12 f_yelo">'+name+'</dd>'+
			'	</dl>'+
			'</div>'+
			'<div class="pop_shopgift_r">'+
			'	<dl class="clear">'+
			'		<dt class="fl">'+
			'			<p><input type="hidden" value="'+uid+'" class="input_1 input_w1 f_6" id="user_id" maxlength=8/></p>'+
			'		</dt>'+
			'		<dd class="fl" id="user_info" style="display:none"></dd>'+
			'	</dl>'+
			'	<ul>'+
			'		<input type="hidden" id="goods_memo">'+
			'		<li class="m_t10 fb_12 f_6">使用数量：</li>'+
			'		<li><select id="buy_num" name="buy_num" class="input_1">'+num_html+'</select> <span class="f_6"></li>'+
			'		<li class="m_t10"><a href="javascript:;" onclick="clear_ajax('+id+','+price+', '+sid+')" class="btn1 btn_w">使用</a></li>'+
			'	</ul>'+
			'</div>'+
			'</div>';
	}
	else {
		if(goods_receiver == gUid || goods_receiver == 0) {
			var opt_self = '&nbsp;&nbsp;<a href="javascript:;" onclick="$(\'#user_id\').val('+gUid+');show_userinfo();clear_ajax('+id+','+price+', '+sid+')" class="btn1 btn_w">对自己使用</a>';
		}
		else {
			var opt_self = '';
		}
		var g_html = '<div class="pop_shopgift clear">'+
			'<div class="pop_shopgift_l">'+
			'	<dl>'+
			'		<dt><img src="'+img+'" alt="" /></dt>'+
			'		<dd class="m_t10 fb_12 f_yelo">'+name+'</dd>'+
			'	</dl>'+
			'</div>'+
			'<div class="pop_shopgift_r">'+
			'	<dl class="clear">'+
			'		<dt class="fl">'+
			'			<p><span  class="fb_12 f_6">对'+ui_sex(sex,1)+'使用：</span> <a href="javascript:;" title="每位简简单单网用户都有一个独一无二的简简单单网ID。您在访问别人的首页时，网址链接的最后一串数字就是这个ID。" class="p_l20 fs_12 f_6 unline">(什么是ID号？)</a></p>'+
			'			<p><input type="text" value="'+((goods_receiver != 0)?goods_receiver:'请输入朋友的ID号')+'" class="input_1 input_w1 f_6" onclick="if(this.value==\'请输入朋友的ID号\') this.value =\'\';" id="user_id" maxlength=8 onkeyup="show_userinfo(event)" onblur="show_userinfo()"/><div style="display:none" id="uid_list"></div></p>'+
			'			<p class="fs_12 f_r" id="msg_alert"></p>'+
			'		</dt>'+
			'		<dd class="fl" id="user_info"></dd>'+
			'	</dl>'+
			'	<ul>'+
			'		<li class="m_t10 fb_12 f_6">使用数量：</li>'+
			'		<li><select id="buy_num" name="buy_num" class="input_1">'+num_html+'</select> <span class="f_6"></li>'+
			'		<li  class="m_t5 fb_12 f_6">写写您想对'+ui_sex(sex,1)+'说的话：</li>'+
			'		<li><textarea  maxlength="512" rows="2" cols="20" id="goods_memo" class="input_1 input_w f_9" maxlength=100>'+memo+'</textarea></li>'+
					stealth_html +
			'		<li class="m_t10"><a href="javascript:;" onclick="clear_ajax('+id+','+price+', '+sid+')" class="btn1 btn_w">对'+ui_sex(sex,1)+'使用</a>'+opt_self+'</li>'+
			'	</ul>'+
			'</div>'+
			'</div>';

	}

	Win.dialog({msg:g_html,height:350,width:450});
	$("#WinDiv").css("z-index",10000);
	if(goods_receiver.length == 8) {
		window.setTimeout("show_userinfo()",500);
	}
}
function clear_ajax(gid, price, sid) {
	if($("#user_id").val().length != 8) {
		show_userinfo();
		return;
	}
	if($("#goods_memo").val().length > 100) {
		alert("说的话内容最多100个汉字");
		$("#goods_memo").focus();
		return;
	};
	if(window.confirm("确定要使用吗？")) {
		var buy_num = $("#buy_num").val();
		var way = parseInt($("input[name='way']:checked").val());
		if(goods_ajax == 1) return;
		goods_ajax = 1;
		$.post('/index.php?s=/goods/clear',{num:buy_num,gid:gid,receiver:$("#user_id").val(),way:way,sid:sid,memo:$("#goods_memo").val()}, function(data) {
			goods_ajax = 0;
			if(data.stat == 200) {
				Win.dialog({msg:data.msg,type:'alert',enter:function() {location.reload();},cancel:function() {location.reload();}});
			}
			else if(data.stat == 504) {
				Win.dialog({msg:data.msg,type:'alert',enterName:'关闭'});
			}
			else if(data.stat == 405) {
				Win.dialog({msg:data.msg,type:'alert',enter:function() {location.href='/login/logout';},cancel:function() {location.href='/login/logout';}});
			}
			else {
				Win.dialog({msg:data.msg,type:'alert'});
			}
		}, 'json');
	}
}

function goods_toused(id, total, img, name,memo) {
	for(var i=1,num_html=''; i <= total; i++) {
		num_html += '<option value="'+i+'">'+i+'</option>';
	}
	if(goods_stealth) {
		var stealth_html = '<li><span class="f_6"><input name="way" type="checkbox" value="2"/> 是否匿名</span></li>';
	}
	else var stealth_html = '<li><span class="f_6">您想匿名使用吗？<a href="/goods" target="_blank" style="color:#000000">请先购买隐身衣</a></span></li>';
	var g_html = '<div class="pop_shopgift clear">'+
		'<div class="pop_shopgift_l">'+
		'	<dl>'+
		'		<dt><img src="'+img+'" alt="" /></dt>'+
		'		<dd class="m_t10 fb_12 f_yelo">'+name+'</dd>'+
		'	</dl>'+
		'</div>'+
		'<div class="pop_shopgift_r">'+
		'	<dl class="clear">'+
		'		<dt class="fl">'+
		'			<p><span  class="fb_12 f_6">对'+ui_sex((myuserinfo.sex==1?2:1),1)+'使用：</span> <a href="#" title="每位简简单单网用户都有一个独一无二的简简单单网ID。您在访问别人的首页时，网址链接的最后一串数字就是这个ID。" class="p_l50 fs_12 f_6 unline">(什么是ID号？)</a></p>'+
		'			<p><input type="text" value="'+((goods_receiver !=0)?goods_receiver:'请输入您朋友的ID号')+'" class="input_1 input_w1 f_6" onclick="if(this.value==\'请输入您朋友的ID号\') this.value =\'\';" id="user_id" maxlength=8 onkeyup="show_userinfo(event)" onblur="show_userinfo()"/><div style="display:none" id="uid_list"></div></p>'+
		'			<p class="fs_12 f_r" id="msg_alert"></p>'+
		'		</dt>'+
		'		<dd class="fl" id="user_info"></dd>'+
		'	</dl>'+
		'	<ul>'+
		'		<li class="m_t10 fb_12 f_6">使用数量：</li>'+
		'		<li><select onchange="$(\'#price_num\').html(this.options[this.options.selectedIndex].value*2)" id="buy_num" name="buy_num" class="input_1">'+num_html+'</select></li>'+
		'		<li name="self_goods" class="m_t5 fb_12 f_6">写写您想对'+ui_sex((myuserinfo.sex==1?2:1),1)+'说的话：</li>'+
		'		<li name="self_goods"><textarea maxlength="512" rows="2" cols="20" id="goods_memo" class="input_1 input_w f_9" maxlength=100>'+memo+'</textarea></li>'+
				stealth_html +
		'		<li class="m_t10"><a href="javascript:;" onclick="send_ajax('+id+','+total+', 2)" class="btn1 btn_w">使用</a></li>'+
		'	</ul>'+
		'</div>'+
		'</div>';
	Win.dialog({msg:g_html,height:300,width:500,title:'购买'});
	if(goods_receiver.length == 8) {
		show_userinfo();
	}
}
function goods_send(id, total, img, name,memo) {
	for(var i=1,num_html=''; i <= total; i++) {
		num_html += '<option value="'+i+'">'+i+'</option>';
	}
	if(goods_stealth) {
		var stealth_html = '<li><span class="f_6"><input name="way" type="checkbox" value="2"/> 是否匿名</span></li>';
	}
	else var stealth_html = '<li><span class="f_6">您想匿名赠送吗？<a href="/goods" target="_blank" style="color:#000000">请先购买隐身衣</a></span></li>';
	var g_html = '<div class="pop_shopgift clear">'+
		'<div class="pop_shopgift_l">'+
		'	<dl>'+
		'		<dt><img src="'+img+'" alt="" /></dt>'+
		'		<dd class="m_t10 fb_12 f_yelo">'+name+'</dd>'+
		'	</dl>'+
		'</div>'+
		'<div class="pop_shopgift_r">'+
		'	<dl class="clear">'+
		'		<dt class="fl">'+
		'			<p><span  class="fb_12 f_6">赠送给：</span> <a href="#" title="每位简简单单网用户都有一个独一无二的简简单单网ID。您在访问别人的首页时，网址链接的最后一串数字就是这个ID。" class="p_l50 fs_12 f_6 unline">(什么是ID号？)</a></p>'+
		'			<p><input type="text" value="'+((goods_receiver != 0)?goods_receiver:'请输入您所赠送朋友的ID号')+'" class="input_1 input_w1 f_6" onclick="if(this.value==\'请输入您所赠送朋友的ID号\') this.value =\'\';" id="user_id" maxlength=8 onkeyup="show_userinfo(event)" onblur="show_userinfo()"/><div style="display:none" id="uid_list"></div></p>'+
		'			<p class="fs_12 f_r" id="msg_alert"></p>'+
		'		</dt>'+
		'		<dd class="fl" id="user_info"></dd>'+
		'	</dl>'+
		'	<ul>'+
		'		<li class="m_t10 fb_12 f_6">赠送数量：</li>'+
		'		<li><select onchange="$(\'#price_num\').html(this.options[this.options.selectedIndex].value*2)" id="buy_num" name="buy_num" class="input_1">'+num_html+'</select></li>'+
		'		<li  class="m_t5 fb_12 f_6">写写您想对'+ui_sex((myuserinfo.sex==1?2:1),1)+'说的话：</li>'+
		'		<li><textarea maxlength="512" rows="2" cols="20" id="goods_memo" class="input_1 input_w f_9" maxlength=100>'+memo+'</textarea></li>'+
				stealth_html +
		'		<li class="m_t10"><a href="javascript:;" onclick="send_ajax('+id+','+total+', 0)" class="btn1 btn_w">赠送</a></li>'+
		'	</ul>'+
		'</div>'+
		'</div>';
	Win.dialog({msg:g_html,height:300,width:500});
	if(goods_receiver.length == 8) {
		show_userinfo();
	}
}


function show_userinfo() {
	var send_uid = $("#user_id").val();
	send_uid = send_uid.replace(/[^\d]/g,'');
	if(send_uid.length == 0) {
		$("#msg_alert").html("请输入正确的ID号");
		return;
	}
	if(send_uid.length != 8) {
		//goods_receiver = 0;
		if(window.localStorage && window.JSON) {
			var length = window.localStorage.length;
			var timeout = 0;
			var show_list = '';
			for(var i=0; i<length; i++) {
				var id_sub = window.localStorage.key(i).substring(0,send_uid.length);
				if(send_uid == id_sub && window.localStorage.key(i).length == 8) {
					var data = window.JSON.parse(window.localStorage.getItem(window.localStorage.key(i)));
					show_list += '<div style="width:189px;float:left;vertical-align: middle;background:#aaaaaa;border:1px solid #dddddd" onclick="$(\'#uid_list\').hide();$(\'#user_id\').val('+data.uid+').blur();"><div style="float:left;padding:2px;">ID:'+data.uid+'<br>昵称：'+data.nickname+'</div><div style="float:right"><img src="'+data.face+'" style="vertical-align: middle;height:50px"></div></div>';
				}
			}
			if($("#uid_list").html() != show_list) {
				$("#uid_list").html(show_list).css({"position":"absolute"});
			}
			$("#uid_list").show();
			$("#user_info").html('');
		}
		//$("#user_info").html("<font color=red>请输入正确的用户ID</font>");
		//$("#user_id").focus();
		return;
	}
	else {
		$("#uid_list").hide();
		$("#user_info").html("");
		$("#msg_alert").html("");
	}

	if(window.localStorage && window.JSON) {
		var data = window.JSON.parse(window.localStorage.getItem(send_uid));
		if(data && data.uid == send_uid && ((new Date).getTime() - data.timeout) < 100*3600*24) {
			$("#user_info").html('<p><img src="'+data.face+'"></p><p class="font_w">'+data.nickname+'</p>');
			goods_receiver = send_uid;
			return;
		}
	}
	$.getJSON('/msg/userinfo', {fid:send_uid}, function(data) {
		if(data.uid == send_uid) {
			goods_receiver = send_uid;
			if(window.localStorage && window.JSON) {
				data.timeout = (new Date).getTime();
				window.localStorage.setItem(send_uid,window.JSON.stringify(data));
			}
			$("#user_info").html('<p><img src="'+data.face+'"></p><p class="font_w">'+data.nickname+'</p>');
			$("#uid_list").hide();
			$("#msg_alert").html("");
		}
		else {
			$("#msg_alert").html("请输入正确的ID号");
			//goods_receiver = 0;
		}
	});
}
// 赠送或使用
function send_ajax(id, price, used) {
	if($("#user_id").val().length != 8) {
		$("#user_id").focus();
		alert("请输入正确的ID号");
		show_userinfo();
		return;
	}
	if($("#goods_memo").val().length > 100) {
		alert("说的内容最多100个汉字");
		$("#goods_memo").focus();
		return;
	};
	if(window.confirm(used?"确定对此用户使用吗？":"确定赠送给此用户吗？")) {
		var buy_num = $("#buy_num").val();
		var way = $("input[name='way']:checked").val();
		if(goods_ajax == 1) return;
		goods_ajax = 1;
		$.post('/index.php?s=/goods/sendbuy',{num:buy_num,id:id,receiver:$("#user_id").val(),way:way,type:used,memo:$("#goods_memo").val()}, function(data) {
			goods_ajax = 0;
			if(data.stat == 200) {
				Win.dialog({msg:data.msg,type:'info',enter:function() {location.reload(true);},cancel:function() {location.reload(true);}});
			}
			else {
				Win.dialog({msg:data.msg,type:'info'});
			}
		}, 'json');
	}
}
function goods_usednum(id,status,gid,num){
	var msg = '<div class="vmske_info f_3 clear" style="width:200px;">'
	  msg += '<p class="m_b15" style="width:200px;">使用数量：<select id="used_num" name="used_num">';
	for(i=1;i<=num;i++){
		msg += '<option value="'+i+'">'+i+'</option>';
	}
	msg +=  '</select></p>';
	msg += '<a class="btn1 btn_b1" onclick="goods_myused('+id+','+status+','+gid+')">使用</a>  &nbsp; <a class="dashed" onclick="Win.close(false);">取 消</a></div>';
	Win.dialog({msg:msg,width:250});
}
function goods_myused(id, status,gid) {
	var num = 0;
	
	if(status==1){
		var msg = '确定要使用吗？';
		if(gid == 1){
			var num = $("#used_num").val();
		}
	}else{
		var msg = '确定要暂停使用吗？';
	}
	if(window.confirm(msg)) {
		
		$.post('/index.php?s=/goods/used', {id:id,status:status,gid:gid,num:num}, function(data) {
			alert(data.msg);
			if(data.stat == 200) {
				location.href='/goods/my?uid='+goods_receiver;
			}
		},'json');
	}
}
function goods_del(id) {
	if(id == "") return;
	$.getJSON('/goods/del', {id:id}, function(data) {
		alert(data.msg);
		if(data.stat == 200) {
			location.reload();
		}
	});
}
function show_flash() {
	var pos = Win.pos(800,500);
	$("#show_flash").css({"top":pos.wt,"left":pos.wl});
}
var goods_pop_html = '';
function playPopup(){
	$("#show_flash").remove();
	if(goods_pop_html == "") return;
	Win.dialog({msg:goods_pop_html,width:500,height:400});
	$("#WinMask").css("background", "#999999");
}
function check_gift() {
	$.getJSON('/index.php?s=/goods/gift/', {}, function(data) {
		var swf_url = data.swf;
		var rand_time = (new Date).getTime();
		if(swf_url && swf_url.indexOf(".swf") != -1 && data.type <= 2) {
			var msg_html = '<div style="text-align:center;height:100px;padding:20px;font-weight:bold"><p style="height:50px;font-size:18px"><font color="red">好消息</font>，您收到<font color="red">1</font>个新礼物</p><p><a href="javascript:;" onclick="Win.close(true)" class="btn1">确定</a></p></div>';
			Win.dialog({msg:msg_html,enter:function(obj){
				var data = obj.data;
				var goods_html = '<div id="show_flash" style="position:absolute;z-index:9999;top:0px;left:0px;width:800px;height:500px;alpha(opacity=100);opacity:1"><object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" name="roseIE" width="100%" height="100%" align="middle" id="roseIE"><PARAM NAME="WMode" VALUE="Transparent"><param name="allowScriptAccess" value="always" /><param name="movie" value="'+g_staticUrl+'/shopgift/'+data.swf+'?uid='+data.total+'&t='+rand_time+'" /><param name="quality" value="high" /><embed src="'+g_staticUrl+'/shopgift/'+data.swf+'?uid='+data.total+'&t='+rand_time+'" quality="high" width="100%" height="100%" name="roseIE" align="middle" allowScriptAccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"  wmode="transparent"/></object></div>';
				$(goods_html).appendTo("body");
				show_flash();
				goods_pop_html = data.html;
			}, 'noclose':true,height:300,width:400,data:data});
			$("#WinMask").css("background", "#000000");
			$.getJSON('/index.php?s=/goods/gift_up/', {id:data.id}, function(data) {});
		}
		else if(data.html) {
			Win.dialog({msg:data.html,width:500,height:400});
			$.getJSON('/index.php?s=/goods/gift_up/', {id:data.id}, function(data) {});
		}
	});
}

function goods_preview(url) {
	if(url && url.indexOf(".swf") != -1) {
		var msg = '<div style="height:360px"><OBJECT codeBase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" classid=clsid:d27cdb6e-ae6d-11cf-96b8-444553540000 width="100%" height="350px"><param name="Movie" value="'+g_staticUrl+'/shopgift/'+url+'"><param name="WMode" value="Transparent"><param name="AllowScriptAccess" value="always"><embed  width="100%" height="350px" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" allowscriptaccess="always" wmode="transparent" src="'+g_staticUrl+'/shopgift/'+url+'"></OBJECT></div>';
	}
	else var msg = '<div style="height:320px"><img src="'+g_staticUrl+'/shopgift/'+url+'"></div>';
	Win.dialog({msg:msg,width:560,height:350});
}

function goods_memo(id, face, nickname, memo, title) {
	if(id) {
		var img = '<p><a href="/'+id+'" target="_blank"><img src="'+face+'" alt="" /></a></p><p><a href="/'+id+'" target="_blank">'+nickname+'</a></p>';
	}
	else {
		var img = '<p><img src="'+face+'" alt="" /></p><p>'+nickname+'</p>';
	}
	var msg_html = '<div class="pop_giftmessage">'+
	  '<dl style="text-align:left;font-weight:bold">'+title+'</dl>'+
	  '<dl class="clear">'+
		'<dt>'+
		img+
		'</dt>'+
		'<dd class="fb_14">'+memo+'</dd>'+
	  '</dl>'+
	'</div>';

	Win.dialog({msg:msg_html,height:300,type:'alert'});
}
function goods_alert() {
	Win.dialog({msg:'您还没有魔法棒，进入<a href="/goods" target="_blank">道具礼品中心</a>',type:'alert',enterName:'道具礼品中心',enter:'location.href="/goods/"'});
}
function goods_roach() {
	if(typeof myuserinfo != "object" || !myuserinfo.uid){
		 show_login_form();
		 return false ;
	}

	if(goods_is_alert) return;
	Win.dialog({'msg':'<div class="progress">正在加载数据</div>','type':'warn','noclose':true,width:580,height:400});
	if(goods_ajax == 1) return;
	goods_ajax = 1;
	$.getJSON("/goods/roach", {gid:13,receiver:goods_receiver}, function(data) {
		goods_ajax = 0;
		if(data.stat == 200) {
			Win.dialog({"msg":data.msg,width:450,height:300});
		}
		else {
			Win.dialog({"msg":data.msg,width:300,height:100,type:'alert'});
		}
	});
}
var g_roach_total = 0, g_roach_alt = 0;
var g_used_num = 0, g_used_alt = 0;
var g_used_way = 0;
var g_used_memo = '';
//杀虫
function goods_kill(total) {
	if(goods_receiver == 0) return;
	g_roach_alt = g_roach_total = total;
	g_used_alt = g_used_num = $("#buy_num").val();
	g_used_way = $("input[name='way']:checked").val();
	g_used_memo = $("#goods_memo").val();
	goods_is_alert = true;
	Win.close();
	if($.browser.msie) {
		$("#flash_roach")[0].scj_count(g_used_num);
	}
	else {
		$("#flash_roachff")[0].scj_count(g_used_num);
	}
}
function scj_alert() {
	goods_roach();
}
function pesticides_info() {
	if(goods_receiver == 0 || g_used_num <= 0) return;
	$.post('/index.php?s=/goods/clear',{num:1,gid:14,receiver:goods_receiver,way:g_used_way,sid:13,memo:g_used_memo}, function(data) {
		goods_ajax = 0;
		if(data.stat == 200) {
			g_used_num--;
			g_roach_total--;
			if(data.sex == 2) {
				var huifu = ((goods_receiver==gUid)?'您的':'帮助TA')+'魅力恢复了<font color=red>+'+g_used_alt*10+'</font>';
			}
			else var huifu = '';
			if(g_roach_total <= 0) {
				// 清除flash
				Win.dialog({msg:'<p>您已经使用<font color=red>'+g_used_alt+'</font>瓶杀虫剂，杀死<font color=red>'+g_used_alt+'</font>只蟑螂，'+huifu+'</p><p>该主页上已经没有蟑螂了！</p>',type:'alert',width:400});
				$("#show_flash_13").remove();
				$("#show_gift_html").remove();
			}
			else if(g_used_num <= 0 && g_roach_total > 0){
				Win.dialog({msg:'<p>您已经使用<font color=red>'+g_used_alt+'</font>瓶杀虫剂，杀死<font color=red>'+g_used_alt+'</font>只蟑螂!</p><p>'+huifu+'</p><p>该主页上还有<font color=red>'+g_roach_total+'</font>只蟑螂没有消灭！继续使用<a href="javascript:;" onclick="goods_is_alert=false;goods_roach()">杀虫剂</a></p>',type:'alert',enter:function(){goods_is_alert=false;goods_roach();},cancel:function(){goods_is_alert=false;goods_roach();},width:400});
			}
			if($.browser.msie) {
				$("#flash_roach")[0].scj_count(g_used_num);
			}
			else {
				$("#flash_roachff")[0].scj_count(g_used_num);
			}
		}
		else if(data.stat == 504) {
			Win.dialog({msg:data.msg,type:'alert',enterName:'关闭'});
		}
		else {
			Win.dialog({msg:data.msg,type:'alert'});
		}

	}, 'json');
}
function show_mask_info(type,uid){
	     if(myuserinfo.sex == 2){
	     	var msg_code ='<span class="fb_14">该道具仅限男性用户使用 </span>';
				  Win.dialog({msg:msg_code,width:400,type:'info'});
				  return;
	     }
	    if(type == 1){
       
	    	var msg_code ='<div class="vmske_intro f_3 clear">'
	    	+ '<p class="fl mske_img"><img src="'+version_img("noface/vmske_72.png")+'"/></p>'
	    	+ '<div class="fl deatl">'
	    	+ '<p><span class="fb_14">贵宾面具</span><span class="f_6">（男性专用）</span></p>'
	    	+ '<dl class="m_t10 clear">'
	    	+ '<dt class="fl">主要作用：</dt>'
	    	+ '<dd class="f_6">'
	    	+ '<p>1.使用贵宾面具的男性用户不用上传自己的照片也能浏览女性用户更多的照片；</p>'
	    	+ '<p>2.使用贵宾道具的男性用户有不同于普通用户的尊贵VIP头像。</p>'
	    	+ '</dd>'
	    	+ '</dl>'
	    	+ '<dl class="m_t10 clear">'
	    	+ '<dt class="fl">价格：</dt>'
	    	+ '<dd class="f_6">'
	    	+ '<p>30红豆（包月）</p>'
	    	+ '<p>200红豆（包年）</p>'
	    	+ '<p>'
	    	+ '<span class="f_r fl">(1元=1红豆）</span>'
	    	+ '<span class="fr"> <a onclick="locat_pay_card('+uid+')">先充值红豆</a>&nbsp;&nbsp;'
	    	+ '<input type="button" value="购买" class="btn1" onclick="show_mask_info(2)">'
	    	+ '</span></p>'
	    	+ '</dd>'
	    	+ '</dl>'
	    	+ '</div>'
	    	+ '</div>';
				  Win.dialog({msg:msg_code,width:550});
	}else{
		var msg_code = '<div class="vmske_info f_3 clear">'
		+ '<div class="hdchange clear m_b15">'
		+ '<span class="fl fb_14">付费模式：</span>'
		+ '<form id="form1" name="form1" method="post" action="">'
		+ ' <p class="fl">'
		+ '<label>'
		+ '<input name="RadioGroup1" type="radio" id="RadioGroup1_0" value="1" checked="checked" class="checkbox1"/>'
		+ ' 包月 (30红豆)</label>'
		+ '<br />'
		+ '<label>'
		+ '<input type="radio" name="RadioGroup1" value="2" id="RadioGroup1_1" class="checkbox1"/>'
		+ ' 包年 (200红豆)</label>'
		+ '<br />'
		+ '</p>'
		+ '</form>'
		+ '</div>'
		+ '<p>您的红豆：<e class="f_r">'+myuserinfo.card_num+'颗</e> <input type="button" class="btn1" onclick="buy_mask()" value="购买并使用" class="btn1"> <a href="javascript:;" onclick="Win.close()">取消</a></p>'
		+ '</div>'
		Win.dialog({msg:msg_code,width:400});
	}
				  
}
function locat_pay_card(uid){
	Cookies.set("locat_pay_card_"+myuserinfo.uid,1);
	if(uid !=0 || uid !=''){
		Cookies.set("locat_pay_uid",uid);
	}
	window.location.href="/pay/order";
	return;
}
function buy_mask() {
	var RadioGroup = parseInt($("input[name='RadioGroup1']:checked").val())
      if(RadioGroup == 1){
      	var buy_card_num = 30;
      	var days = 31;
      }
      if(RadioGroup == 2){
      	var buy_card_num = 200;
      	var days = 366;
      }
	if(myuserinfo.card_num < buy_card_num) {
		Win.dialog({type:'info',msg:'您的红豆不足，请先充值红豆',enter:function(){locat_pay_card(0)}});
		return;
	}
	if(goods_ajax == 1) return;
	goods_ajax = 1;
	$.post('/index.php?s=/goods/buy_mask',{price:buy_card_num,days:days,gid:25}, function(data) {
		goods_ajax = 0;
		if(data.stat == 200) {
			myuserinfo.card_num -= buy_card_num;
			Win.dialog({msg:data.msg,type:'info',width:400});
		}
		else {
			Win.dialog({msg:data.msg,type:'info',width:400});
		}
	}, 'json');
}
$(document).ready(function() {
	// 判断是否有新礼品
	check_gift();
	// 判断是否使用隐身衣提示
	if(Cookies.get("login_hide") == 1) {
		Win.dialog({msg:"您没有使用隐形衣道具，隐身登录无效<br><br><a href='/goods/' target='_blank' style='font-size:12px'>进入礼品道具中心查看</a>",type:"alert"});
		Cookies.clear("login_hide");
	}
	var fav_online = Cookies.get("fav_online");
	if(!fav_online) fav_online = 0;
	if($("#fav_online") && ((new Date).getTime() - fav_online) > 120000) {
		$.get('/index.php?s=/fav/fav_online/',{}, function(data){
			if(parseInt(data) > 0) {
				$("#fav_online").html(data).attr("class","online f_green fr");
			}
			else {
				$("#fav_online").attr("class","").html('');
			}
		});
		Cookies.set("fav_online", (new Date).getTime());
	}
});