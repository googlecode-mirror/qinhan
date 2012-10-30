function cancel_forbid(uid,name,ok_fun)
{
	Win.dialog({type:'confirm',msg:'是否取消阻止“'+name+'”？',height:100,enter:function(){send_cancel_forbid(uid,ok_fun);},enterName:'确定'});
}

function refresh()
{
	self.location.href=self.location.href;
}

function send_cancel_forbid(uid,ok_fun)
{
	$.ajax({
	   type: "POST",
	   url: "/index.php?s=/link/cancel_forbid/",
	   data: 'forbid_uid='+uid+'&',
	   success: function order_result(re)
	   {
	   		switch(re)
	   		{
	   			case '1': Win.dialog({type:'info',msg:'<img class="ico" src="'+version_img('popup_g.jpg')+'" />取消阻止成功！',height:100,cancel:ok_fun,enter:ok_fun}); break;
	   			case '-1': Win.dialog({type:'info',msg:'参数错误<br/>',height:100,cancel:ok_fun,enter:ok_fun}); break;
	   		}
	   }
	});
}

function forbid_link(uid,sex,nickname,ok_fun,status){
	if(typeof myuserinfo != "object" || !myuserinfo.uid){
		 show_login_form();
		 return false ;
	}

	if(myuserinfo.uid == uid){
		Win.dialog({type:'info',msg:'不能阻止自己！'});
		return false;
	}

	var sex_ta = (sex == 2)?'她':'他';
	if(status==1){
		var msg_code='您确定要静默处理“'+nickname+'”吗？(确认后，您还能收到'+sex_ta+'的信息，但不会有新信息提示。对方不会知道您使用了此功能。）';
	}else{
		var msg_code='您确认要阻止和“'+nickname+'”的联系吗？ 确认后，系统会通知对方此操作。';
	}
	Win.dialog({type:'confirm',msg:msg_code,width:500,enter:function(){send_forbid_link(uid,sex,nickname,ok_fun,status);},cancel:function(){}});
}

function send_forbid_link(uid,sex,nickname,ok_fun,status){
	if(typeof myuserinfo != "object" || !myuserinfo.uid){
		 show_login_form();
		 return false ;
	}

	if(myuserinfo.uid == uid){
		return false;
	}

	$.ajax({
	   type: "POST",
	   url: "/index.php?s=/link/forbid/",
	   data: 'forbid_uid='+uid+'&status='+status+'&',
	   success: function reply_success(re){
			switch(re){
				case '1'://
						var msg_code = sysmessage_success('forbid_link',sex) ;
						Win.dialog({width:400,msg:msg_code,enter:ok_fun,cancel:ok_fun});
						break ;
			    case '2'://
						var msg_code = sysmessage_success('forbid_link1',sex) ;
						Win.dialog({width:400,msg:msg_code,enter:ok_fun,cancel:ok_fun});
						break ;
				case '-1'://
						Win.dialog({type:'info',msg:'参数错误！'});
						break;
				case '-2'://
						Win.dialog({type:'info',msg:'您已经阻止过“'+nickname+'”了！'});
						break;
			}
		}
	});
}
function hello(uid,sex,nickname,type){
	$.post("/index.php?s=/msg/check/", { friend:uid}, function (data) {
		if(data.stat == 5) {
			var msg_info = redbeans(data.nickname,data.pay_card);
			Win.dialog({'msg':msg_info,'height':400,'width':580,'pay_card':data.pay_card,'enter':function(data){
				$.post("/index.php?s=/msg/check/", { friend:uid, pay_card:data.pay_card}, function (data) {
					if(data.stat) {
						Win.dialog({'msg':data.error, 'type':'alert'});
					}
					else hello_div(uid,sex,nickname,type);
				}, 'json');
			}});
		}
		else if(data.stat) {
			Win.dialog({'msg':data.error, 'type':'alert'});
		}
		else hello_div(uid,sex,nickname,type);
	}, 'json');
}
function hello_div(uid,sex,nickname,type){
	if(typeof myuserinfo != "object" || !myuserinfo.uid){
		 show_login_form();
		 return false ;
	}

	if(myuserinfo.uid == uid){
		Win.dialog({type:'info',title:'打招呼',msg:'不能给自己打招呼！'});
		return false;
	}

	if(myuserinfo.profile_completed < 0.5 && myuserinfo.login_times > 6){
		var msg_code = sysmessage_addprofile('hello',(myuserinfo.sex==1)?2:1) ;
		Win.dialog({width:460,msg:msg_code});
		return false ;
	}

	if(myuserinfo.sex==sex){
		Win.dialog({type:'info',msg:'同性不能操作此功能！',cancel:function(){}});
		return false;
	}
	var sex_ta = (sex == 2)?'她':'他';
	
	var post_paid = (myuserinfo.pay_card)?'<div class="opt"><label><input type="checkbox" id="pay_card"> 给'+sex_ta+'贴邮票</label></div>':'';
    $.ajax({
        type: "POST",
        url: "/index.php?s=/msgquick/",
        data: 'type='+type+'&nickname='+nickname+'&sex='+sex+'&uid='+uid,
        success: function(re){ 
            var obj = jQuery.parseJSON(re);
			if(obj.errno == 200) {
				Win.dialog({title:'打招呼',msg:obj.msg,width:470});
				code = $("#msg_list input");
				if(code.length>0){
					$(function(){
						$("label").click(function(){
							$(this).find("input[type='radio']").attr("checked",true);
						});
					}); 
				}else{
					if(type == 1)add_hello();
				}


			}else{
				 Win.dialog({'msg':obj.msg,'type':'info'});
			}			
        }
    });
}

function add_hello()
{
	$("#add_hello").attr('key', '');
	$("#content").val('');
	$('#hello_list').hide();
	$('#add_hello').show();
	$("#do_type").text('添加');
	$(function(){
	   var _area=$('textarea#content'); 
	   var _max=_area.attr('maxlength'); 
		_area.bind('keyup change',function(){
			_val=$(this).val(); 
			_cur=_val.length; 
			if(_cur>_max){//当默认值小于限制数时,可输入数为max-cur 
				$(this).val(_val.substring(0,_max)); 
			} 
		});
	}); 
}
function edit_hello()
{
	$("#add_hello").attr('key', '');
	msg_value = $("input[name=select_hello]:checked").val();
	msg_key = $("input[name=select_hello]:checked").attr('id');
	$('#hello_list').hide();
	$('#add_hello').show();
	$("#content").val(msg_value);
	$("#add_hello").attr('key', msg_key);
	if(msg_key)
	{
		$("#do_type").text('修改');
	}
}
var is_delete = true;
function delete_msg()
{
	if(is_delete === false) return false;
	is_delete = false;
	msg_key = $("input[name=select_hello]:checked").attr('id');	
	$.ajax({
		type: "POST",
		url: "/index.php?s=/msgquick/delete/",
		data: 'key='+msg_key+'&',	
		success: function(re){ 
			var obj = jQuery.parseJSON(re);
			if(obj.errno == 200) {
				$('#li'+msg_key).remove();
				is_delete = true;
				if(obj.ok == 2){
					var arr = jQuery.parseJSON(obj.ret);
					$.each(arr,function(n,value) {
						$("#"+n).attr('id', value);
						$("#li"+n).attr('id', 'li'+value);
					});
				}
				code = $("#msg_list input");
				if(code.length>0){
					$("input[type='radio'][name='select_hello']").get(0).checked = true;
				}else{
					$("#no_msg").show(); 
				}
			}else{
				 Win.dialog({'msg':obj.msg,'type':'info'});
			}
		}
	});
}
var is_submit = true;
function modify_msg(type)
{
	var content = $.trim($("#content").val());
	var key = $("#add_hello").attr("key");
	if(content == ""){
		$("#content").focus();
		return false;
	}
	if(key){
			if(is_submit === false) return false;
			is_submit = false;
			$.ajax({
			type: "POST",
			url: "/index.php?s=/msgquick/edit/",
			data: 'content='+$('#content').val()+'&type='+type+'&key='+key,	
			success: function(re){ 
				var obj = jQuery.parseJSON(re);
				if(obj.errno == 200) {
					$("input[name='select_hello'][checked]").attr("checked",false);
					$('#hello_list').show();$('#add_hello').hide();
					$('#li'+key).html(obj.msg);
					$("#li"+key).attr('id',  'li'+obj.key);
					if(obj.ok == 2){
						var arr = jQuery.parseJSON(obj.ret);
						$.each(arr,function(n,value) {
							$("#"+n).attr('id', value);
							$("#li"+n).attr('id', 'li'+value);
						});
					}
					$(function(){
						$("label").click(function(){
							$(this).find("input[type='radio']").attr("checked",true);
						});
					}); 
				}else{
					 Win.dialog({'msg':obj.msg,'type':'info'});
				}
				is_submit = true;
			}
		});
	}else{
		$.ajax({
			type: "POST",
			url: "/index.php?s=/msgquick/add/",
			data: 'content='+$('#content').val()+'&type='+type,	
			success: function(re){ 
				var obj = jQuery.parseJSON(re);
				$("#no_msg").hide(); 
				if(obj.errno == 200) {
					$("input[name='select_hello'][checked]").attr("checked",false);
					$('#hello_list').show();$('#add_hello').hide();
					$("#msg_list").prepend(obj.msg);
					$(function(){
						$("label").click(function(){
							$(this).find("input[type='radio']").attr("checked",true);
						});
					}); 
				}else{
					 Win.dialog({'msg':obj.msg,'type':'info'});
				}
				is_submit = true;
			}
		});
	}
}
function send_quick_msg()
{
	var msg_value = $("input[name=select_hello]:checked").val();
	Win.close();
	IM.chatSend(msg_value);
}

//显示8个相似的用户
function similar_hello(uid,sex,hello_content){
	//判断是否为非法用户
	if(typeof myuserinfo != "object" || !myuserinfo.uid){
		 show_login_form();
		 return false ;
	}
	var uid = parseInt(uid);
	var sex = parseInt(sex);
	var hello_content = comm_str_trim(str_to_safe(hello_content));

	//取得8个相似的用户数据
	$.post("/index.php?s=/home/similar_user","uid="+uid,function(re){
		if(1 == re.stat){
			var msg_code = '<div class="recommend_pop"><div class="fs_14 title">和她打招呼成功，还找到'+re.num+'个与她相似你可能感兴趣的人</div><div class="clear m_t20">';
			$.each(re.data,function(index,result){
					msg_code += '<div class="poplist"><div class="img"><a href="'+result.home_url+'" target="_blank"><img src="'+result.face_url+'" width="120" height="120"/></a></div><p class="f_6"><input name="hello_checkbox" type="checkbox" class="checkbox1" value="'+result.uid+'" checked="true"><a href="'+result.home_url+'" target="_blank">'+result.nickname+'</a>,'+result.birth_y+'岁</p><p class="f_6">'+result.prov_city+'</p></div>';
			});
			msg_code += '</div><div class="bot_btn"><select name="select_morehello" id="select_morehello">';
			code = $("#msg_list input");
			for(var i=0;i<code.length;i++){
				msg_code += '<option value="'+code[i].value+'">'+code[i].value+'</option>';
			}
			msg_code += '</select><a class="btn1" onclick="send_more_hello('+sex+');">一键打招呼</a></div></div>';
			Win.dialog({title:'一键打招呼',msg:msg_code,width:650});
			$("#select_morehello").val(hello_content);
		}else{
			Win.dialog({type:'info',msg:'<img src="'+version_img('ico_mail_sended.png')+'" /> 打招呼成功！',cancel:function(){}});
		}
	},'json');
}

//一键发送打招呼
function send_more_hello(sex){
	var uids = '';
	var hello_content = '';
	var sex = parseInt(sex);
	//判断是否为非法用户
	if(typeof myuserinfo != "object" || !myuserinfo.uid){
		 show_login_form();
		 return false ;
	}
	//取得checkbox的值并用，分隔开 组成字符串；页面出来再做 uids
	$("input[name='hello_checkbox']:checked").each(function(){
		uids += $(this).val() + ',';
	});
	if(2 == sex){
		hello_content = $("#select_morehello").val();
	}else{
		hello_content = 'Hi~';
	}

	if('' != uids){
		//ajax取得用户的uid sex nickname，如果热度不为0 就不发送
		$.post("/index.php?s=/home/check_linkcount","uids="+uids,function(uidstr){
			var tag = 0;
			if(uids.indexOf(',') > 0){
				tag = 1;
			}
			send_hello(uidstr,sex,'他们',hello_content,0,tag);
		},'json');
	}else{
		Win.close();
	}
}

//发送打招呼
function send_hello(uid,sex,nickname,hello_content,pay_card,tag){
	if(typeof myuserinfo != "object" || !myuserinfo.uid){
		 show_login_form();
		 return false ;
	}
	if(myuserinfo.uid == uid){
		Win.dialog({type:'info',title:'打招呼',msg:'不能给自己打招呼！'});
		return false;
	}
	if(myuserinfo.uid == uid){
		Win.dialog({type:'info',title:'打招呼',msg:'不能给自己打招呼！'});
		return false;
	}
	if(myuserinfo.sex == sex){
		Win.dialog({type:'info',title:'打招呼',msg:'同性不能操作此功能！'});
		return false;
	}
	if(myuserinfo.sex == 2){
		hello_content = 'Hi~';
	}else{
		if(hello_content==''){
				code = $("#msg_list input");
				if(code.length>0){
					hello_content = $("input[name='select_hello']:checked").val();
				}else{
					Win.dialog({type:'info',title:'打招呼',msg:'请先添加打招呼内容'});
					return false;
				}
			
		}
	}
    if (hello_content == ''){
        return  false;
    }
	var send = 'send';
	if(1 == tag) send = 'more_send';
    $.ajax({
	   type: "POST",
	   url: "/index.php?s=/msg/"+send+"/",
	   data: 'receiver_uid='+uid+'&content='+encodeURIComponent(hello_content)+'&type=1&pay_card='+pay_card,
	   success: function comment_success(re)
		{
			$("#comment_button").attr("disabled",false);
			var result_obj = jQuery.parseJSON(re);
			switch(result_obj.stat)
			{
				case 0: //
					   if(myuserinfo.sex==2){
							Win.dialog({type:'info',msg:'<img src="'+version_img('ico_mail_sended.png')+'" /> 打招呼成功！<br/>您对'+nickname+'说了一句：Hi~',cancel:function(){}});     
					   }else{
							if(1 == tag){
								Win.dialog({type:'info',msg:'<img src="'+version_img('ico_mail_sended.png')+'" /> 打招呼成功！',cancel:function(){}});
							}else{
								similar_hello(uid,sex,hello_content);
							}						     
					   }
						break;
				case 5://
						Win.dialog({msg:redbeans(result_obj.nickname,result_obj.pay_card),width:580,height:400,enter:function(){send_hello(uid,sex,nickname,hello_content,result_obj.pay_card);},cancel:function(){}});
						break;
				case 4://
						Win.dialog({type:'info',msg:result_obj.error,enter:function(){},cancel:function(){}});
						break;
				case 10:
						$("#fengkoujiao").remove();
						$(result_obj.error).appendTo("body");
						var pos = Win.pos(320,250);
						$("#fengkoujiao").css({"top":pos.wt,"left":pos.wl});
						break;
				default://
						Win.dialog({type:'info',msg:result_obj.error,cancel:function(){}});
						break;
			}
		}
	});
}
function try_meet(uid,sex,nickname,makefriend_do_things) {
	$.post("/index.php?s=/msg/check/", { friend:uid}, function (data) {
		if(data.stat == 5) {
			var msg_info = redbeans(data.nickname,data.pay_card);
			Win.dialog({'msg':msg_info,'height':400,'width':580,'pay_card':data.pay_card,'enter':function(data){
				$.post("/index.php?s=/msg/check/", { friend:uid, pay_card:data.pay_card}, function (data) {
					if(data.stat) {
						Win.dialog({'msg':data.error, 'type':'alert'});
					}
					else try_meet_div(uid,sex,nickname,makefriend_do_things);
				}, 'json');
			}});
		}
		else if(data.stat) {
			Win.dialog({'msg':data.error, 'type':'alert'});
		}
		else try_meet_div(uid,sex,nickname,makefriend_do_things);
	}, 'json');
}
function try_meet_div(uid,sex,nickname,makefriend_do_things) {
	if(typeof myuserinfo != "object" || !myuserinfo.uid){
		 show_login_form();
		 return false ;
	}

	if(myuserinfo.sex==sex){
		Win.dialog({type:'info',msg:'同性不能操作此功能！',cancel:function(){}});
		return false;
	}

	var sex_show = ui_sex(sex,1);

	if(myuserinfo.profile_completed < 0.5 && myuserinfo.login_times > 6){
		var msg_code = sysmessage_addprofile('meet',(myuserinfo.sex==1)?2:1) ;
		Win.dialog({width:460,msg:msg_code});
		return false ;
	}
	
	if(myuserinfo.makefriend_do_things==''){
		var warn_msg = '<p>提示：请先在网站首页右侧填写“我想和一个' + ui_sex(3 - myuserinfo.sex, 0) + '生...”</p><br/><p>再使用便捷约会功能！</p>';
		Win.dialog({'msg':warn_msg,'type':'info'});
		return false;
	}

	var msg_meet = '<ul>' ;
	if(sex=='1'){
		var _tmpDothings1 = 'checked=true ' ;
		if(myuserinfo.makefriend_do_things != ''){
			msg_meet = msg_meet + '<li><label><input type="radio" value="2" name="meet_do" checked=true /> <span class="f_yelo fb_14">'+myuserinfo.makefriend_do_things+'</span> <span class="f_9">(我想做的事)</span></label></li>';
			_tmpDothings1 = ' ' ;
		}

		if(makefriend_do_things != ''){
			msg_meet = msg_meet + '<li><label><input type="radio" value="1" name="meet_do" '+_tmpDothings1+'/> <span class="f_yelo fb_14">'+makefriend_do_things+'</span> <span class="f_9">('+sex_show+'想做的事)</span></label></li>';
		}
	}else{
		var _tmpDothings2 = 'checked=true ' ;
		if(makefriend_do_things != ''){
			msg_meet = msg_meet + '<li><label><input type="radio" value="1" name="meet_do" checked=true /> <span class="f_yelo fb_14">'+makefriend_do_things+'！</span> <span class="f_9">('+sex_show+'想做的事)</span></label></li>';
			_tmpDothings2 = ' ' ;
		}
		if(myuserinfo.makefriend_do_things != ''){
			msg_meet = msg_meet + '<li><label><input type="radio" value="2" name="meet_do" '+_tmpDothings2+'/> <span class="f_yelo fb_14">'+myuserinfo.makefriend_do_things+'！</span> <span class="f_9">(我想做的事)</span></label></li>';
		}
	}
	msg_meet = msg_meet + '</ul>' ;

	var title='';
	title='约会 '+nickname;

	var msg_code = '<div class="popup_meet">'
				+ '<p class="fb_14">'+title+'：</p>'
				+ msg_meet
				+ '<div class="opt"><a onclick="send_meet('+uid+','+sex+',\''+nickname+'\',0)" class="btn1">确 定</a> &nbsp; 或 &nbsp; <a onclick="Win.close();" class="dashed">取消</a></div>'
				+ '</div>' ;

	Win.dialog({width:460,msg:msg_code});
	
	$.ajax({
	   type: "POST",
	   url: "/index.php?s=/stat/meet_click/",
	   data: 'uid='+uid+'&',
	   success: function reply_success(re)
				{
				}
	});
}


function send_meet(uid,sex,nickname,pay_card, meet_do) {
	if(typeof myuserinfo != "object" || !myuserinfo.uid){
		 show_login_form();
		 return false ;
	}

	if(myuserinfo.makefriend_do_things==''){
		if(myuserinfo.sex==1){
			Win.dialog({type:'info',enterName:'知道了，去填',msg:'<p>系统提示：请先填写“<a href="/main/nearby/?_sid=meet" target="_blank">我想和一个女生...</a>”</p><br/><p> 再使用便捷约会功能！</p>',cancel:function(){},enter:function(){self.location.href='/main/nearby/?_sid=meet';}});
		}else{
			Win.dialog({type:'info',enterName:'知道了，去填',width:500,msg:'<p>友情提示：您还未填“<a href="/main/nearby/?_sid=meet" target="_blank">我想和一个男生...</a>”</p><br/><p>填写此信息，方可使用便捷约会功能并获得更多优秀男士关注！</p>',cancel:function(){},enter:function(){self.location.href='/main/nearby/?_sid=meet';}});
		}
		return false;
	}
	if(!meet_do) { 	
		meet_do = $("input[name='meet_do']:checked").val();
	}

    $.ajax({
	   type: "POST",
	   url: "/index.php?s=/msg/send/",
	   data: 'receiver_uid='+uid+'&meet_do='+meet_do+'&type=8&pay_card='+pay_card,
	   success: function reply_success(re)
				{
					var result_obj = jQuery.parseJSON(re);
					switch(result_obj.stat)
					{
						case 0: //
								Win.dialog({type:'info',msg:'<img src="'+version_img('ico_mail_sended.png')+'" /> 发送成功！',cancel:function(){}});
								break;
						case 5://
								Win.dialog({msg:redbeans(result_obj.nickname,result_obj.pay_card),width:580,height:400,enter:function(){send_meet(uid,sex,nickname,result_obj.pay_card,meet_do);}});
								break;
						case 4://
								Win.dialog({type:'info',msg:result_obj.error,enter:function(){},cancel:function(){}});
								break;
						case 10:
								$("#fengkoujiao").remove();
								$(result_obj.error).appendTo("body");
								var pos = Win.pos(320,250);
								$("#fengkoujiao").css({"top":pos.wt,"left":pos.wl});
								break;
						default://
								Win.dialog({type:'info',msg:result_obj.error,cancel:function(){self.location.href=self.location.href;}});
								break;
					}
				}
	});
}

function check_send_mail(uid,sex,nickname){
	if(typeof myuserinfo != "object" || !myuserinfo.uid){
		 show_login_form();
		 return false ;
	}

	if(myuserinfo.uid == uid){
		Win.dialog({type:'info',title:'打招呼',msg:'不能给自己写信！'});
		return false;
	}

	if(myuserinfo.profile_completed < 0.5){
		var msg_code = sysmessage_addprofile('mail',(myuserinfo.sex==1)?2:1) ;
		Win.dialog({width:460,msg:msg_code});
		return false ;
	}

	if(myuserinfo.sex==sex){
		//Win.dialog({type:'info',msg:'同性不能操作此功能！',cancel:function(){}});
		return false;
	}
	
	return true;
}

function fav(uid,sex,nickname,status){
	if(typeof myuserinfo != "object" || !myuserinfo.uid){
		 show_login_form();
		 return false ;
	}

	if(myuserinfo.uid == uid){
		 Win.dialog({type:'info',msg:'不能收藏自己！'});
		 return false ;
	}

	if(myuserinfo.profile_completed < 0.5 && myuserinfo.login_times > 6){
		var msg_code = sysmessage_addprofile('fav',(myuserinfo.sex==1)?2:1) ;
		Win.dialog({width:460,msg:msg_code});
		return false ;
	}

	if(myuserinfo.sex==sex){
		Win.dialog({type:'info',msg:'同性不能操作此功能！',cancel:function(){}});
		return false;
	}
	$.ajax({
	   type: "POST",
	   url: "/index.php?s=/fav/add",
	   data: 'uid='+uid+'&status='+status+'&',
	   success: function order_result(re)
	   {
	   		switch(re)
	   		{
	   			case '1': 
					var msg_code = sysmessage_success('fav',sex) ;
					Win.dialog({width:360,msg:msg_code});
					$("#fav_btn_"+uid).html("");
					break ;
	   			case '2':
					var msg_code = sysmessage_success('fav2',sex) ;
					Win.dialog({width:360,msg:msg_code});
					$("#fav_btn_"+uid).html("");
					break ;
				case '3':
				   var msg_code = sysmessage_success('fav1',sex);
				    Win.dialog({width:360,msg:msg_code});
					$("#fav_btn_"+uid).html("");
					break ;
				case '-1':
					Win.dialog({type:'info',msg:'您收藏的人数已经超过2000'});
					break ;
	   			case '0': Win.dialog({type:'info',msg:'收藏失败!'}); break;
	   		}
	   }
	});
}

function sysmessage_upface(type,sex){
	var msg = '';
	switch(type){
		//查看联系方式
		case 'contact':
			msg = '<div>要看'+ui_sex(sex,1)+'的联系方式，请先<a href="/photo/up_form/">上传自己的照片</a>！</div>';
			break;
		//交友目的
		case 'makefriend':
			msg = '<div>要看'+ui_sex(sex,1)+'的交友目的，请先<a href="/photo/up_form/">上传自己的照片</a>！</div>';
			break;
		//最后登录时间
		case 'logintime':
			msg = '<div>要看'+ui_sex(sex,1)+'的最后登录时间，请先<a href="/photo/up_form/">上传自己的照片</a>！</div>';
			break;
		//评论
		case 'comment':
			msg = '<div>请先<a href="/photo/up_form/">上传自己的照片</a>，再给'+ui_sex(sex,1)+'评论吧！</div>';
			break;
		//评分
		case 'rate':
			msg = '<div>请先<a href="/photo/up_form/">上传自己的照片</a>，再给'+ui_sex(sex,1)+'评分！</div>';
			break;
		//加为好友
		case 'feel':
			msg = '<div>请先<a href="/photo/up_form/">上传自己的照片</a>，再给'+ui_sex(sex,1)+'写评语！</div>';
			break;
		//加为好友
		case 'fav'://JS
			msg = '<div>要加'+ui_sex(sex,1)+'为好友，请先<a href="/photo/up_form/">上传自己的照片</a>！</div>';
			break;
		case 'meet'://JS
			msg = '<div>请先<a href="/photo/up_form/">上传自己的照片</a>，再约'+ui_sex(sex,1)+'吧！</div>';
			break;
		//打招呼
		case 'hello'://JS
			msg = '<div>请先<a href="/photo/up_form/">上传自己的照片</a>，再给'+ui_sex(sex,1)+'打招呼！</div>';
			break;
		//发送站内信
		case 'mail'://JS
			msg = '<div>请先<a href="/photo/up_form/">上传自己的照片</a>，再给'+ui_sex(sex,1)+'发站内信吧！</div>';
			break;
		default :
			msg = '<div>请先<a href="/photo/up_form/">上传自己的照片</a>！';
	}

	msg = msg + '<div class="opt"><a href="/photo/up_form/" class="btn1">上传照片</a></div>';
	msg = '<div class="popup_c">'+msg+'</div>' ;
	return msg ;
}

//补充资料
function sysmessage_addprofile(type,sex){
	var msg_content='';
	switch(type){
		//查看联系方式
		case 'contact':
			msg_content = '您还不能查看'+ui_sex(sex,1)+'的联系方式！' ;
			break;
		//交友目的
		case 'makefriend':
			msg_content = '您还不能查看'+ui_sex(sex,1)+'的交友目的！' ;
			break;
		//最后登录时间
		case 'logintime':
			msg_content = '您还不能查看'+ui_sex(sex,1)+'的最后登录时间！' ;
			break;
		//评论
		case 'reply':
			msg_content = '您还不能给'+ui_sex(sex,1)+'评论！' ;
			break;
        case 'hello':
			msg_content = '您还不能给'+ui_sex(sex,1)+'打招呼！' ;
			break;
		//发送站内信
		case 'mail'://JS
			msg_content = '您还不能给'+ui_sex(sex,1)+'发送站内信！' ;
			break;
		//发送站内信
		case 'fav'://JS
			msg_content = '您还不能将'+ui_sex(sex,1)+'添加收藏！' ;
			break;
		//评论
		case 'feel':
			msg_content = '您还不能给'+ui_sex(sex,1)+'写评语！' ;
			break;
		case 'meet':
			msg_content = '您还不能约'+ui_sex(sex,1)+'！' ;
			break;
		case 'chat':
			msg_content = '您还不能和'+ui_sex(sex,1)+'聊天！' ;
			break;
		case 'photo':
			msg_content = '您还不能查看'+ui_sex(sex,1)+'的照片！' ;
			break;
		case 'photo_comment':
			msg_content = '您还不能给'+ui_sex(sex,1)+'的照片评论！' ;
			break;
		//查看资料
		case 'profile':
			msg_content = '基于公平原则，如果您想查看'+ui_sex(sex,1)+'的更多资料。' ;
			break;
		default :
			msg_content = '';
	}

	var msg = '<div class="pop_info_contact">'
		+ '	<p>'+msg_content+'</p>'
		+ '	<p style="margin:15px 0 0 0;">请先<a href="/profile/">完善您的资料</a> <span class="f_r1">50%</span> 以上！</p>'
		+ '	<div class="clear" style="margin:15px 0 0 100px;">'
		+ '		<div class="fl">您目前的资料完成度为 <span class="f_blue1">'+myuserinfo.profile_completed_show2+'</span>&nbsp;</div>'
		+ '		<div class="fl" style="margin:3px 0 0 0;">'+myuserinfo.profile_completed_show3+'</div>'
		+ '	</div>'
		+ '</div>'
		+ '<div class="opt"><a href="/profile/" class="btn1">知道了</a></div>'

	msg = '<div class="popup_c">'+msg+'</div>';

	return msg ;
}

//操作成功提示信息
function sysmessage_success(type,sex){
	var msg_content='';
	switch(type){
		case 'forbid_link':
			msg_content = '<img src="'+version_img('popup_g.jpg')+'" border="0" alt="" class="ico" /> 阻止成功！';
			break;
		case 'forbid_link1':
			msg_content = '<img src="'+version_img('popup_g.jpg')+'" border="0" alt="" class="ico" /> 静默处理成功！对方不会知道此操作！';
			break;
			//打招呼
		case 'hello':
			msg_content = '<img src="'+version_img('popup_g.jpg')+'" border="0" alt="" class="ico" /> 发送成功！';
			break;
		//加为好友
		case 'fav':
			msg_content = '<img src="'+version_img('popup_g.jpg')+'" border="0" alt="" class="ico" /> 收藏成功！<a href="/index.php?s=/fav/out">点这里查看我的收藏</a>';
			break;
		case 'fav1':
		   if(sex==1){
				msg_content = '<img src="'+version_img('popup_g.jpg')+'" border="0" alt="" class="ico" /> 悄悄收藏成功！该用户不会知道您收藏了他！<a href="/index.php?s=/fav/out">点这里查看我的收藏</a>';
		   }else{
		   	msg_content = '<img src="'+version_img('popup_g.jpg')+'" border="0" alt="" class="ico" /> 悄悄收藏成功！该用户不会知道您收藏了她！<a href="/index.php?s=/fav/out">点这里查看我的收藏</a>';
		   }
			break;
		//加为好友
		case 'fav2':
			msg_content = '你之前已经收藏过了！<a href="/index.php?s=/fav/out"> 点这里查看我的收藏</a>';
			break;
		//回信
		case 'reply':
			msg_content = '<img src="'+version_img('popup_g.jpg')+'" border="0" alt="" class="ico" /> 评论发送成功！';
			break;
		//回信
		case 'feel':
			msg_content = '<img src="'+version_img('popup_g.jpg')+'" border="0" alt="" class="ico" /> 评语发送成功！';
			break;
		default :
			msg_content = '<img src="'+version_img('popup_g.jpg')+'" border="0" alt="" class="ico" /> 操作成功！';
	}

	var msg = '<div class="pop_info_success">'
		+ '	<p class="success_content">'+msg_content+'</p>'
		+ '</div>'
		+ '<div class="opt"><a onclick="Win.close();" class="btn1">关 闭</a></div>';

	msg = '<div class="popup_c">'+msg+'</div>' ;

	return msg ;
}

var current_comment_uid = 0;
var current_comment_id = 0;
var current_answer_id = 0;
var current_answer_home_id = 0;
var current_comment_type = 0;
var current_comment_div = 'comment';
var select_show = 0;
function show_edit(idname) {
	idname = idname||'comment_content';

	var tmp = '';
	if (idname != "answer_content") {
		if (IM.gFlash) {
			tmp = 'if(event.keyCode==13 && event.ctrlKey) {submit_comment(0)}';
		} else {
			tmp = 'if(event.keyCode==13 &amp;&amp; event.ctrlKey) {submit_comment(0)}';
		}
	}
	if(IM.gFlash) {
		var edit = '<div contenteditable="true" onkeyup="setRange(this);qt_keydown_edit(\''+idname+'\')" onblur="qt_keydown_edit(\''+idname+'\')" onmouseup="setRange(this)" ondrop="return false" ondragover="return false" onKeyDown="'+tmp+'"  onClick="" name="'+idname+'" id="'+idname+'" type="text"  maxlength="200" style="padding:3px;overflow-x:hidden;overflow-y:scroll;background:#fff;border-color:#7E7E7E #CFCFCF #CFCFCF #7E7E7E;border-style:solid;border-width:1px;height:60px;box-shadow:2px 2px 2px rgba(0, 0, 0, 0.1) inset;"></div>';
	}
	else {
		var edit = '<textarea onkeydown="'+tmp+' qt_keydown_edit(\''+idname+'\')"  onClick="" onblur="qt_keydown_edit(\''+idname+'\')" name="'+idname+'" id="'+idname+'" class="input1" maxlength="200" style="overflow-y:hidden;" onkeyup="setRange(this)" onmouseup="setRange(this)"></textarea>';
	}
	return edit;
}

function show_comment_form(uid,id,type)
{
	var div_id = arguments[3]?arguments[3]:'comment' ;
	var div_from = arguments[4]?arguments[4]:'' ;
	var div_content_id = arguments[5]?arguments[5]:div_id ;
	$.post("/index.php?s=/msg/check/", { friend:uid}, function (data) {
		if(data.stat == 5) {
			var msg_info = redbeans(data.nickname,data.pay_card);
			Win.dialog({'msg':msg_info,'height':400,'width':580,'pay_card':data.pay_card,'enter':function(data){
				$.post("/index.php?s=/msg/check/", { friend:uid, pay_card:data.pay_card}, function (data) {
					if(data.stat) {
						Win.dialog({'msg':data.error, 'type':'alert'});
					}
					else show_comment_div(uid,id,type,div_id,div_from,div_content_id);
				}, 'json');
			}});
		}
		else if(data.stat) {
			Win.dialog({'msg':data.error, 'type':'alert'});
		}
		else show_comment_div(uid,id,type,div_id,div_from,div_content_id);
	}, 'json');
}
function show_comment_div(uid,id,type,div_id,div_from,div_content_id) 
{	
	if(arguments[3] == "answer") {
        var edit = show_edit(div_id+'_content');
		var sub_html = '<div class="clear"><p class="fl left_tip"> <a class="fl"><img width="39" height="20" src="'+ version_img('ico_ce.gif') +'" onclick="face51New.show(this,\''+div_id+'_cont\',\'_textarea\');"></a><label><input type="checkbox" value="1" name="sync" id="sync_home" class="checkbox1" onclick="sync_check_wenwen(\'sync_home\')" disabled="disabled"><span class="f_9" id="sync_home_p" >公开到我的主页</span></label><label><input type="checkbox" name="attention" id="attention" class="checkbox1" value="1"  onclick="sync_check_wenwen(\'attention\')" disabled="disabled"><span class="f_9" id="sync_attention_p">发布到动态</span></label></p><div class="fr f_6"><p class="fl"></p><p class="f_green tip">点击拇指即可发出→</p> <a id="answer_oppos" onclick="post_answer(2)" class="nobg" title="反对" value="2"></a><a id="answer_agree" onclick="post_answer(1)" class="yesbg" title="赞成" value="1"></a><p></p></div></div>';
	}
	else {
        var edit = show_edit(div_id+'_content');
		var sub_html = '<p class="clear"><span class="fl"><a onclick="submit_comment(0)" id="comment_button" class="btn1">发送评论</a></span><span class="fl"><img src="'+ version_img('ico_ce.gif') +'" alt="表情" onclick="face51New.show(this,\''+div_id+'_content\',\'_textarea\');" style="cursor:pointer;" /></span><span class="fl f_9">(评论只有对方本人可见)</span></p>';
	}
	var comment_html = '<p class="top_img"><img src="'+ version_img('reaply_bg.gif') +'" alt="" /></p><div class="reply_box_bg">'+ edit +'</div>'+ sub_html +'<div id="divFace" style="display:none;"></div>';

	if(typeof myuserinfo != "object" || !myuserinfo.uid){
		 show_login_form();
		 return false ;
	}
	if(myuserinfo.uid == uid){
		if(div_id == 'answer'){
			Win.dialog({type:'info',msg:'不能对自己回答！'});
		}else{
			Win.dialog({type:'info',msg:'不能给自己评论！'});
		}
		return false;
	}
	if((type != 4) && (type != 5) && (type != 2) && (type != 10) && (type != 14) && (type != 15) && (type != 16)){
		Win.dialog({type:'info',msg:'评论类型错误！'});
		return false;
	}
	//alert(current_comment_id);
	//if(current_comment_id == id) return ;

	if(current_comment_id>0)
	{
		$("#"+div_id+"_"+current_comment_uid+'_'+current_comment_id).slideUp("fast").html('');
		//$("#"+div_id+"_"+current_comment_uid+'_'+current_comment_id).html('');
		if(div_from == 'home'){
			$(".show_comment").html('');
		}
	}   

	//$("#"+div_id+"_"+uid+'_'+id).html($("#"+div_content_id+"_div").html());
	$("#"+div_id+"_"+uid+'_'+id).html(comment_html).slideDown("slow",function(){$("#"+div_id+"_content").select().focus();$("#"+div_id+"_content").focus();});
	current_comment_id = id;
	current_comment_uid = uid;
	current_comment_type = type;
	current_comment_div = div_id;
    select_show = 0;
}

function cont_filter(dsc) {
	if(dsc === "" || !dsc) return '';
	var reg = /<img(.*?)src=(\"|\')http:\/\/jjdd01.ivu1314.com\/CDN\/app\/face\/(.*?)(\"|\')(.*?)>/ig;
	dsc = dsc.replace(reg,"[img]$3[/img]");
	if($.browser.safari) {
		dsc = dsc.replace(/(<div>)|(<p>)|(<br>)|(<li>)/ig,"\r\n");
	}
	else {
		dsc = dsc.replace(/(<\/div>)|(<\/p>)|(<br\/?>)|(<\/li>)/ig,"\r\n");
	}
	dsc = dsc.replace(/<[^>]+>/ig,"");
	dsc = dsc.replace(/<script(.*?)>(.*?)<\/script>/ig,"");
	dsc = dsc.replace(/^(&nbsp;)*/,"").replace(/(&nbsp;)*$/,"");
	dsc = dsc.replace(/(&|＆)nbsp;/ig," ");
	dsc = dsc.replace(/(&|＆)lt;/gi,"<");
	dsc = dsc.replace(/(&|＆)gt;/gi,">");
	dsc = dsc.replace(/(&|＆)amp;/gi,"&");
	dsc = dsc.trim();
	return dsc;
}

function hide_comment_form()
{
	if(current_comment_id>0)
	{
		$("#"+current_comment_div+"_"+current_comment_uid+'_'+current_comment_id).slideUp("fast").html('');
		//$("#"+current_comment_div+"_"+current_comment_uid+'_'+current_comment_id);
		current_comment_id = 0;
		current_comment_uid = 0;
		$("#comment_button").attr("disabled",false);
	}
}

function hide_answer_form(obj)
{
    if (current_answer_home_id != 0) {
        $('#answer_'+current_answer_home_id+'_home').slideUp('normal').html('');
        current_answer_home_id = 0;
    }	
    
    if (current_answer_id != 0 ) {
       
        $('#answer_'+current_answer_id).slideUp('normal').html('');
        current_answer_id = 0;
    }
}

function submit_comment(pay_card){
    //var mail_from = 4;
    var related = current_comment_id;
	if(IM.gFlash) {
		var comment_content = cont_filter($("#"+current_comment_div+"_content").html());
	}
	else {
		 var comment_content = $.trim($("#"+current_comment_div+"_content").val());
	}
    
	if(typeof myuserinfo != "object" || !myuserinfo.uid){
		 show_login_form();
		 return false ;
	}

	if(myuserinfo.uid == current_comment_uid){
		Win.dialog({type:'info',msg:'不能给自己评论！'});
		return false;
	}

	if(myuserinfo.profile_completed < 0.5 && myuserinfo.login_times > 6){
		var msg_code = sysmessage_addprofile('reply',(myuserinfo.sex==1)?2:1) ;
		Win.dialog({width:460,msg:msg_code});
		return false ;
	}

    if (comment_content.length<1) {
        Win.dialog({type:'info',msg:'回复内容不能为空！'});
        return;
    }

    if(related<0)
    {
    	Win.dialog({type:'info',msg:'评论的小编专访不能为空！'});
    	return ;
    }

    $("#comment_button").attr("disabled",true);
    $.ajax({
	   type: "POST",
	   url: "/index.php?s=/msg/send/",
	   data: 'receiver_uid='+current_comment_uid+'&content='+encodeURIComponent(comment_content)+'&type='+current_comment_type+'&related='+related+'&pay_card='+pay_card,
	   success: function comment_success(re)
				{
					$("#comment_button").attr("disabled",false);
					var result_obj = jQuery.parseJSON(re);
					switch(result_obj.stat)
					{
						case 0: //
								Win.dialog({type:'info',msg:'评论发送成功！'});
								hide_comment_form();
								break;
						case 5://
								Win.dialog({msg:redbeans(result_obj.nickname,result_obj.pay_card),width:580,height:400,enter:function(){submit_comment(result_obj.pay_card);},cancel:function(){}});
								break;
						case 4://
								Win.dialog({type:'info',msg:result_obj.error,enter:function(){},cancel:function(){}});
								break;
						case 10:
								$("#fengkoujiao").remove();
								$(result_obj.error).appendTo("body");
								var pos = Win.pos(320,250);
								$("#fengkoujiao").css({"top":pos.wt,"left":pos.wl});
								break;
						default://
								Win.dialog({type:'info',msg:result_obj.error,cancel:function(){}});
								break;
					}
				}
	});
}
// 红豆信息
function redbeans(nickname, pay_card) 
{
	var sex_cn = (myuserinfo.sex == 1)?'她':'他';
	var msg_code='<div class="look_send" style="padding:10px 20px;">'
				+'	<div class="fs_14 f_green">'
				+'		<p><span class="fb_14 f_blue">'+nickname+'</span> 目前是热度用户，联系'+sex_cn+'需要预扣您<span class="fb_14 f_green">'+pay_card+'</span>颗红豆！（双方联系成功才扣除）联系成功后，将来与'+sex_cn+'联络不再需要红豆。如果对方一周内未回复您的信息，系统将退还您被预扣的红豆。</p>'
				+'	</div>'
				+'	<div class="fs_13 f_3" style="margin-top:20px;">'
				+'		<p class="f_0" style="text-indent:7mm">为何要用热度限制？<span class="f_r">（诚请认真查看以下文字）</span></p>'
				+'		<p style="text-indent:7mm">为保证每个人都会受到关注，我们强制限制那些受到过多关注的用户。当一个人在24小时内收到十个人以上的联系请求，系统就将其设为热度用户，此时我们建议您与其他热度低的用户进行联系，那是免费的且没有任何限制。您也可以等待这个用户的热度降低至绿色免费状态。如果您要与热度用户联系，您可以购买红豆 1元/1颗 <a href="/pay/order/?card_num='+pay_card+'" target="_blank">购买</a></p>'
				+'		<p style="text-indent:7mm;margin-top:16px">1、有了限制后，对方收到的消息将有限，您发送的消息才不会淹没在大量的消息里，她更能用心阅读，从而提升你与她交友的成功性。</p>'
				+'		<p style="text-indent:7mm;display:none;" class="all_info">2、对部分服务进行正常收费将用于保障网站的持续运营、保证服务品质。</p>'
				+'		<p style="text-indent:7mm;display:none;" class="all_info">3、交友网站现象：少部份照片好看的用户，受到热烈关注，而大多数照片普通的用户却少有人理睬。其实照片好看的用户不一定就都好。反之，照片(形象)普通的用户，往往有许多优秀之处。比如一个相貌平平的女生，可能有开朗的笑容、做一手好菜等等。在压力与浮躁的社会，一个哪怕相貌平平却善良单纯到可以感化你心灵的女孩，应是你不悔的选择。人生本是一个追求幸福快乐的过程，并不是只有和长得好或是有钱的人在一起才快乐。</p>'
				+'		<p><a href="javascript:;" onclick="reason_all()" class="f_9">更详细原因</a></p>'
				+'	</div>'
				+'	<div style="padding:10px 0;text-align:right;">'
				+'		<span class="f_9">您的红豆：</span><span class="f_r1">'+myuserinfo.card_num+'颗</span> &nbsp;&nbsp;<a onclick="Win.close(true)" class="btn1 btn_b1" >预扣红豆，和'+sex_cn+'聊天</a> &nbsp; 或 &nbsp; <a onclick="Win.close(false);" class="dashed">取 消</a>'
				+'	</div>'
				+'</div>';
	return msg_code;
}
function reason_all() {
	if($(".all_info").css("display") != "none") {
		$(".all_info").hide();
		Win.setpos({height:400});
	}
	else {
		$(".all_info").show();
		Win.setpos({height:490});
	}
}

function show_nophoto_tips(go_url,sex,is_mask,uid)
{
	if(go_url=='')
	{
		if(is_mask == 0){
			var msg_code = '<div class="pop_nophoto"><p>您还没有上传照片，这将导致您不能使用80%以上的功能！</p><p>上传照片后，您的受关注度将会立即上升！</p>'
			+'	<div class="opt">'
			+'	<a  class="btn1 btn_b1" href="/photo/up_form/" >上传照片</a> &nbsp; 或 &nbsp; <a onclick="Win.close();" class="dashed">跳过</a>'
			+'	</div>'
			+'</div>';
			Win.dialog({msg:msg_code,width:400});
		}
	}
	else
	{
                   sex = (('' == sex) || (undefined == sex)) ? 'ta' : sex;
                var msg_code = '<div class="vmske_info f_3 clear">'
					msg_code += '<p class="m_b15">基于公平原则，如果您想查看'+sex+'的更多照片<br />请至少<a class="underline" href="/photo/up_form/">上传3张您的本人照片！</a></p>'
					if(sex == '她'){
						msg_code +=  '<p class="m_b15">如果您不想上传照片，您可购买“贵宾面具”。女生知道，使用贵宾面具的男性都是优质用户。';
						if(is_mask == 1){
							msg_code += '<a class="underline" style="color:#FF6600;" href="/goods/my/">继续使用</a></p>';
						}else{
							msg_code += '<a class="underline" style="color:#FF6600;" f_r onclick="show_mask_info(1,'+uid+')">购买贵宾面具</a></p>'
						}
					}
				   	msg_code += '<center><a  class="btn1 btn_b1" href="/photo/up_form/" >上传照片</a></center>'
					msg_code += '</div>';
					Win.dialog({msg:msg_code,width:460});
	}
}
function show_link_hot_tips()
{
	var msg_code='<div class="pop_nophoto"><p>热度是绿色的用户，您可以免费与ta交流；热度出现红色的用户，说明与ta联系的人过多（如一天超出10人）。为保证每个人受到的关注均等，系统自动进行限制，此时如果还要与ta联系，则需要您花费“红豆”（1元一颗)。</p>'
					+'	<div class="opt">'
						+'	<a  class="btn1 btn_b1" onclick="Win.close();" >知道了</a>'
						+'	</div>'
						+'</div>';
	Win.dialog({msg:msg_code,width:450});
}
function qt_length(memo) {
    var reg = /<img(.*?)src=('|")?(.*?).(dxslaw)[\.com](.*?).(gif|png|jpg|jpeg|ico)(.*?)('|")(.*?)>/gim;
    memo = memo.replace(reg,"I");
	memo = memo.replace(/<[^>]+>/ig,"");
    return memo.length;
}

function qt_keydown_edit(id){
    if(id != 'answer_content'){
        return false;
    }
    if(IM.gFlash) {
        var memo = $("#"+id).html();
    }
    else {
        var memo = $("#"+id).val();
    }
    var input_count = qt_length(memo.trim());
    if(input_count > 0 && select_show == 0){
        $("#sync_home").attr('checked','checked');
        $("#attention").attr('checked','checked');
        document.getElementById('sync_home').disabled=false;
        document.getElementById('attention').disabled=false;
        $("#sync_home_p").removeClass().addClass('f_6');
        $("#sync_attention_p").removeClass().addClass('f_6');
    }else if(input_count < 0 || input_count == 0){
        $("#sync_home").attr('checked',false);
        $("#attention").attr('checked',false);
        $("#sync_home").attr('disabled','disabled');
        $("#attention").attr('disabled','disabled');
        $("#sync_home_p").removeClass().addClass('f_9');
        $("#sync_attention_p").removeClass().addClass('f_9');
    }else{
        document.getElementById('sync_home').disabled=false;
        document.getElementById('attention').disabled=false;
        $("#sync_home_p").removeClass().addClass('f_6');
        $("#sync_attention_p").removeClass().addClass('f_6');
        }
 }
 function sync_check_wenwen(id){
    var checkinfo = $("#"+id).attr('checked');
    if(id == 'attention'){
         if(checkinfo != 'checked'){
            select_show = 1;
        }else{
            $("#sync_home").attr('checked','checked');
             $("#anonymity").attr('checked',false);
        }
    }else{
        if(checkinfo == 'checked'){
                $("#"+id).attr('checked','checked');
            }else{
                if(id == 'sync_home'){
                    $("#attention").attr('checked',false);
                }
            select_show = 1;
        }
    }
}

// 回答展开
function show_answer_form(obj)
{
    if(typeof myuserinfo != "object" || !myuserinfo.uid){
         show_login_form();
         return false ;
    }
    var question_id = $(obj).attr('id');	
    if (!question_id) return false;
    var question_uid = '';

    if ($(obj).attr('type') == 'home') { // 个人主页问问回答
        if (question_id == current_answer_home_id) return false;
        question_uid = $('#answer_'+question_id+'_home').attr("data");
    } else {
        if (question_id == current_answer_id) return false;
        question_uid = $('#answer_'+question_id).attr("data");
    }
    $.post("/index.php?s=/msg/check/", { friend:question_uid}, function (data) {
        if(data.stat == 5) {
            var msg_info = redbeans(data.nickname,data.pay_card,data.ret.receiver_userinfo.sex);
            if(data.vip_linkcount){
               var vip_linkcount = '<div style="margin-left:20px;"><font class="fb_14">您已经用完了今天的10个穿越热度用户名额。</font><font class="fs_12">（单月vip每天最多穿越10个热度用户）</font></div>';
               msg_info = vip_linkcount+msg_info;
            }
            Win.dialog({'msg':msg_info,'height':400,'width':580,'pay_card':data.pay_card,'enter':function(data){
                $.post("/index.php?s=/msg/check/", { friend:question_uid, pay_card:data.pay_card}, function (data) {
                    if(data.stat) {
                        Win.dialog({'msg':data.error, 'type':'alert'});
                    }
                    else show_answer_form_div(obj);
                }, 'json');
            }});
        }else if(data.stat == 12) {
            Win.dialog({'msg':data.error, 'type':'alert','enter':function(){
                show_answer_form_div(obj);
            }});
        }
        else if(data.stat) {
            Win.dialog({'msg':data.error, 'type':'alert'});
        }
        else show_answer_form_div(obj);
    }, 'json');
}

function show_answer_form_div(obj)
{
    
    var question_id = $(obj).attr('id');
    var answer_obj = '';
    select_show = 0;
    if (!question_id) return false;
    // 个人主页问问回答标示
    ($(obj).attr('type') == 'home') ? (answer_obj = '#answer_'+question_id+'_home') : (answer_obj = '#answer_'+question_id);
    $('.myfrend_reply,.reply_box').html("");
    if (answers != null && answers[question_id]){Win.dialog({type:'info',msg:'你已经回答过这个问题了'});return false;}
    var edit = show_edit('answer_content');
    var edit_html = '<p class="topimg"><img alt="" src="'+ version_img('reaply_bg.png') +'"></p><div class="reply_box_bg clear">'+edit+'</div><div class="clear"><p class="fl left_tip"> <a class="fl"><img width="39" height="20" src="'+ version_img('ico_ce.gif') +'" onclick="face51New.show(this,\'answer_content\',\'_textarea\');"></a><label><input type="checkbox" onclick="sync_check(\'anonymity\')" value="1" class="checkbox1" id="anonymity" name="sync"><span title="匿名回答后，答案将不会出现在您的个人主页上，对方也不会知道是谁回答" class="f_6">匿名回答</span></label><label><input type="checkbox" value="2" name="sync" id="sync_home" onclick="sync_check(\'sync_home\')" class="checkbox1" disabled="disabled"><span class="f_9" id="sync_home_p" >公开到主页</span></label>&nbsp;<label><input type="checkbox" name="attention" id="attention" class="checkbox1" value="1" onclick="sync_check(\'attention\')" disabled="disabled"><span class="f_9" id="sync_attention_p" >发布动态</span></label></p><div class="fr f_6"><p class="fl"></p><a id="answer_oppos" class="nobg" onclick="post_answer_from('+question_id+',2)" title="反对" value="2"></a><a id="answer_agree" class="yesbg" title="赞成" onclick="post_answer_from('+question_id+',1)" value="1"></a><p></p></div></div>';
    
    //$(obj).parent("p").next("div").find('#answer_content').focus();
    hide_answer_form(obj);
    $(answer_obj).html(edit_html).slideDown("slow",function(){$("#answer_content").select().focus();$("#answer_content").focus();});
    ($(obj).attr('type') == 'home') ? (current_answer_home_id = question_id) : (current_answer_id = question_id);
}

function post_answer_from(q_id,vote)
{
    if(IM.gFlash) {
        var answer_cont = cont_filter($('#answer_content').html());
    }
    else {
        var answer_cont = $.trim($('#answer_content').val());
    }
    var answer_sid = 'question_attention';
    if($('#'+q_id).attr('type') == 'home'){
        $ans = $('#answer_'+q_id+'_home');
        answer_sid = 'question_home';
    }else{
        $ans = $('#answer_'+q_id);
    }
    var question_uid = $ans.attr('data');
    
    var answer_vote = vote;
    if (!q_id) return false;

    $.post('/index.php?s=/answer/answer/', {'q_uid':question_uid, 'q_id':q_id, 'answer_cont': answer_cont, 'vote':answer_vote, 'from':'other','_sid':answer_sid,'attention':$("input[name='attention']:checked").val(),'show':$("input[name='sync']:checked").val()}, function(data){
        if(data.errno == 510) {
            Win.dialog({'msg':data.msg,'type':'info',enter:function(){location.href="/login/logout/"}});
        }else{
            Win.dialog({type:'info',msg:data.msg});
             if (data.code == 1 || data.code == 7){
                //$('#answer_'+data.answer.question_id).hide().empty();
                $ans.hide().empty();
                $('#'+data.answer.question_id).html('已回答');
                $('#answer_more_'+data.answer.question_id).show();
                //$('#'+data.answer.question_id).unbind('click');
                $('#'+data.answer.question_id).removeAttr('id');
             }
        }
         
    }, 'json');
}

function show_quesiton_answer_form_div(obj)
{
    if(typeof myuserinfo != "object" || !myuserinfo.uid){
         show_login_form();
         return false ;
    }
    var a_link_id = $(obj).attr('id');
    var tmp_strs= new Array(); 
    tmp_strs=a_link_id.split("_");  
    var question_id = tmp_strs[0];
    var question_uid = tmp_strs[1];
    var answer_obj = '';
    select_show = 0;
    if(question_uid == myuserinfo.uid){
        Win.dialog({type:'info',msg:'您回答的是您自己的问题'});
        return false;
    }
    if( current_answer_id != 0 ){
        $('#answer_'+current_answer_id).slideUp('normal').html('');
    }
    current_answer_id = a_link_id;
    if (!question_id) return false;
    answer_obj = '#answer_'+a_link_id;
    current_answer_home_id = 0;
    $('.reply_box,.show_comment').html("").hide();
    if (answers != null && answers[question_id]){Win.dialog({type:'info',msg:'你已经回答过这个问题了'});return false;}
    var edit = show_edit('answer_content');
    var edit_html = '<p class="topimg"><img alt="" src="'+version_img('reaply_bg.png')+'"></p><div class="reply_box_bg clear">'+edit+'</div><div class="clear"><p class="fl left_tip"> <a class="fl"><img width="39" height="20" src="'+ version_img('ico_ce.gif') +'" onclick="face51New.show(this,\'answer_content\',\'_textarea\');"></a><label><input type="checkbox" onclick="sync_check(\'anonymity\')" value="1" class="checkbox1" id="anonymity" name="sync"><span title="匿名回答后，答案将不会出现在您的个人主页上，对方也不会知道是谁回答" class="f_6">匿名回答</span></label><label><input type="checkbox" value="2" name="sync" id="sync_home" onclick="sync_check(\'sync_home\')" class="checkbox1" disabled="disabled"><span class="f_9" id="sync_home_p" >公开到主页</span></label>&nbsp;<label><input type="checkbox" name="attention" id="attention" class="checkbox1" value="1" onclick="sync_check(\'attention\')" disabled="disabled"><span class="f_9" id="sync_attention_p" >发布动态</span></label></p><div class="fr f_6"><p class="fl"></p><a id="answer_oppos" class="nobg" onclick="post_question_answer_from(\''+a_link_id+'\',2)" title="反对" value="2"></a><a id="answer_agree" class="yesbg" title="赞成" onclick="post_question_answer_from(\''+a_link_id+'\',1)" value="1"></a><p></p></div></div>';
    
    $(answer_obj).html(edit_html).slideDown("slow",function(){$("#answer_content").select().focus();$("#answer_content").focus();});
    //$('#answer_content').focus();

}

function post_question_answer_from(a_link_id,vote)
{
    var tmp_strs= new Array(); 
    tmp_strs=a_link_id.split("_");  
    var q_id = tmp_strs[0];
    if(IM.gFlash) {
        var answer_cont = cont_filter($('#answer_content').html());
    }
    else {
        var answer_cont = $.trim($('#answer_content').val());
    }
    $ans = $('#answer_'+a_link_id);
    var answer_sid = 'answer_attention';
    
    if($('#'+a_link_id).attr('type') == 'home'){
        answer_sid = 'answer_home';
    }
    var question_uid = $ans.attr('data');
    
    var answer_vote = vote;
    if (!q_id) return false;

    $.post('/index.php?s=/answer/answer/', {'q_uid':question_uid, 'q_id':q_id, 'answer_cont': answer_cont, 'vote':answer_vote, 'from':'other','_sid':answer_sid,'attention':$("input[name='attention']:checked").val(),'show':$("input[name='sync']:checked").val()}, function(data){
        if(data.errno == 510) {
            Win.dialog({'msg':data.msg,'type':'info',enter:function(){location.href='/login/logout/'}});
        }else{
            Win.dialog({type:'info',msg:data.msg});
            if (data.code == 1 || data.code == 7){
                $ans.hide().empty();
                $('#'+a_link_id).html('已回答');
                $('#'+a_link_id).removeAttr('onclick');
                $('#'+a_link_id).removeClass('f_r dashed');
                $('#'+a_link_id).addClass('f_6 unline');
                $('#'+a_link_id).removeAttr('title');
                $('#'+a_link_id).removeAttr('id');
                
            }
        }
         
    }, 'json');
}