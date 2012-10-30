
/***************************** 技能互学 （跟他学，去教他，技能互学）*************************************/


function hide_option(){
	$("#options").hide();
}
function show_option(){
	$("#options").show();
}
function show_city(){
	$("#area").toggle();
}
function change_city(){
	$("#area").hide();
}
function check_input_skill(){
	var skill_type = $("#skill_type").val();
	var input_skill = $.trim($("#input_skill").val()) ;
	
//	if(input_skill == "输入你要搜索的技能名称(支持模糊搜索)" || input_skill=='')
//	{
//		$("#input_skill").select();
//		return false;
//	}
	
	if(input_skill.length > 20){
		Win.dialog({type:'info',msg:'技能类型不能多于20个字！',enter:function(){$('#input_skill').select();}});
		return false ;
	}
	return true;
}
function search_hot_skill(skill_title){
	$("#input_skill").val(skill_title);
}
function input_text(type,ta){
	if((1 == type) || (2 == type)){
		$("#skill_type").val(type);
		if(2 == type){
			$(".selectbox").val(ta+"想学");
		}else{
			$(".selectbox").val("能教我");
		}
		hide_option();
		if('' == type){
			$(".selectbox").focus();
			show_option();
		}
	}
}

function input_focus(){
	var value = $("#input_skill").val();
	if("输入你要搜索的技能名称(支持模糊搜索)" == value){
		$("#input_skill").val('');
		$("#input_skill").attr("class","input3");
	}
}
function input_blur(){
	var value = $("#input_skill").val();
	if("" == value){
		$("#input_skill").val("输入你要搜索的技能名称(支持模糊搜索)")
		$("#input_skill").attr("class","input3 f_9");
	}
}

//------------- 控制灰色字体
function skill_clear_textarea(title,tag_id){
	var textarea = $("#learn_msg_"+tag_id).val();
	if(title == textarea){
		$("#learn_msg_"+tag_id).empty();
		$("#learn_msg_"+tag_id).attr("class","input_1 learn_message f_0");
	}
}
function skill_infill_textarea(title,tag_id){
	var title = title;
	if(tag_id >= 0){
		var textarea = $("#learn_msg_"+tag_id).val();
		if("" == textarea){
			$("#learn_msg_"+tag_id).text(title);
			$("#learn_msg_"+tag_id).attr("class","input_1 learn_message f_9");
		}
	}
}

function skill_clear_input(title,tag_id){
	var textarea = $("#learn_msg_"+tag_id).val();
	if(title == textarea){
		$("#learn_msg_"+tag_id).val('');
		$("#learn_msg_"+tag_id).attr("class","input_1 f_0 fs_12");
	}
}
function skill_infill_input(title,tag_id){
	var title = title;
	if(tag_id >= 0){
		var textarea = $("#learn_msg_"+tag_id).val();
		if("" == textarea){
			$("#learn_msg_"+tag_id).val(title);
			$("#learn_msg_"+tag_id).attr("class","input_1 f_6 fs_12");
		}
	}
}

//---------------------------描述的层效果
function mover(object){
	var i = $(object).attr("i");
	$(object).attr("class",i+" zindx100");
}
function mout(object){
	var i = $(object).attr("i");
	var k = $(object).attr("k");
	$(object).attr("class",i+" "+k);
}
function mclick(object,tag){
	var text = $(object).text();
	$("#learn_msg_"+tag).val(text);
	$("#learn_msg_"+tag).attr("class","input_1 learn_message f_0");
}
//---------------------------

/*
 * 加载 教她和跟他学的弹窗口
 */
function followlearn(type,uid,nickname,skill_id,ta){
	$.post("/index.php?s=/msg/check/", { friend:uid}, function (data) {
		if(data.stat == 5) {
			var msg_info = redbeans(data.nickname,data.pay_card);
			Win.dialog({'msg':msg_info,'height':400,'width':580,'pay_card':data.pay_card,'enter':function(data){
				$.post("/index.php?s=/msg/check/", { friend:uid, pay_card:data.pay_card}, function (data) {
					if(data.stat) {
						Win.dialog({'msg':data.error, 'type':'alert'});
					}
					else followlearn_div(type,uid,nickname,skill_id,ta);
				}, 'json');
			}});
		}
		else if(data.stat) {
			Win.dialog({'msg':data.error, 'type':'alert'});
		}
		else followlearn_div(type,uid,nickname,skill_id,ta);
	}, 'json');
}
function followlearn_div(type,uid,nickname,skill_id,ta){
	var type = parseInt(type);
	var uid = parseInt(uid);
	var skill_id = parseInt(skill_id);
	var nickname = comm_str_trim(str_to_safe(nickname));
	var skill_title = comm_str_trim(str_to_safe($("#get_skill_value"+type+"_"+uid+"_"+skill_id).text()));
	var ta = comm_str_trim(str_to_safe(ta));
	if('' == ta) ta = 'TA';
	var msg_code = '<div class="learnt_skill f_3">';
	if((0 == type) || (1 == type)){
		$.post("/index.php?s=/skilllearn/get_skill","type="+type+"&skill_title="+skill_title,function(re){
			switch(re.stat){
				case 1:
					if(1 == type){
						var title = '跟';
						var textarea = '师傅收下徒儿吧！';
						var pay_info = '可以填写愿意付费多少？';
						msg_code += '<div class="select_wd1 zindx2" i="select_wd1" k="zindx2" onmouseover="mover(this);" onmouseout="mout(this);" onclick="mclick(this,0);"><p>师傅，你收下徒儿吧！</p></div>'
									+'<div class="select_wd2 zindx5" i="select_wd2" k="zindx5" onmouseover="mover(this);" onmouseout="mout(this);" onclick="mclick(this,0);"><p>授人玫瑰，手有余香。</p></div>'
									+'<div class="select_wd3 zindx1" i="select_wd3" k="zindx1" onmouseover="mover(this);" onmouseout="mout(this);" onclick="mclick(this,0);"><p>大爷我不耻下问</p></div>'
									+'<div class="select_wd4 zindx3" i="select_wd4" k="zindx3" onmouseover="mover(this);" onmouseout="mout(this);" onclick="mclick(this,0);"><p>你骑着白马，我挑着担。</p></div>'
									+'<div class="select_wd5 zindx4" i="select_wd5" k="zindx4" onmouseover="mover(this);" onmouseout="mout(this);" onclick="mclick(this,0);"><p>请受徒儿三拜！</p></div>'
									+'<div class="select_wd6 zindx6" i="select_wd6" k="zindx6" onmouseover="mover(this);" onmouseout="mout(this);" onclick="mclick(this,0);"><p>求赐予秘籍！</p></div>';
					}else{
						var title = '教';
						var textarea = '乖乖徒儿，让师傅来教你！';
						msg_code += '<div class="select_wd1 zindx2" i="select_wd1" k="zindx2" onmouseover="mover(this);" onmouseout="mout(this);" onclick="mclick(this,0);"><p>技能不是你想学就能学！</p></div>'
									+'<div class="select_wd2 zindx5" i="select_wd2" k="zindx5" onmouseover="mover(this);" onmouseout="mout(this);" onclick="mclick(this,0);"><p>乖乖徒儿，让师傅来教你！！</p></div>'
									+'<div class="select_wd3 zindx1" i="select_wd3" k="zindx1" onmouseover="mover(this);" onmouseout="mout(this);" onclick="mclick(this,0);"><p>今天你学习技能了吗？</p></div>'
									+'<div class="select_wd4 zindx3" i="select_wd4" k="zindx3" onmouseover="mover(this);" onmouseout="mout(this);" onclick="mclick(this,0);"><p>忙或不忙，都等你来学。</p></div>'
									+'<div class="select_wd5 zindx4" i="select_wd5" k="zindx4" onmouseover="mover(this);" onmouseout="mout(this);" onclick="mclick(this,0);"><p>这项技能我hold住！</p></div>'
									+'<div class="select_wd6 zindx6" i="select_wd6" k="zindx6" onmouseover="mover(this);" onmouseout="mout(this);" onclick="mclick(this,0);"><p>天生善为师</p></div>';
					}
					
					msg_code += '<p class="fs_14">我想'+title+'<b class="f_blue">'+nickname+'</b>学'
							 + '<span class="f_r" id="s_title">'+skill_title+'</span></p>'
							 + '<form id="form1" name="form1" method="post" action="">'
							 + '<p class="f_9 m_t15">和'+ta+'说两句</p><p class="m_t5">'
							 + '<textarea name="textarea_text" class="input_1 learn_message f_9" id="learn_msg_0"'
							 + 'onfocus="skill_clear_textarea(\''+textarea+'\',0);" onblur="skill_infill_textarea(\''+textarea+'\',0);"'
							 + '>'+textarea+'</textarea></p>';

					if(1 == type){
						msg_code +=  '<p class="m_t10 f_6"><label><input name="check_box" id="check_box" type="checkbox" value="1" class="checkbox1" onclick="show_input(3);"/>'
									+'<span>愿意付费</span></label> <input name="textfield" class="input_1 f_6 fs_12" type="text" style="display:none;"'
									+'value="'+pay_info+'" id="learn_msg_3" onfocus="skill_clear_input(\''+pay_info+'\',3);" onblur="skill_infill_input(\''+pay_info+'\',3);"/></p>';
					}

					msg_code += '<p class="m_t30"><a class="btn1" onclick="send_learn('+type+','+uid+',\''+nickname+'\','+skill_id+','+re.re_skill_id+');"'
							  + '>发送</a><span class="p_l10">或&nbsp;</span> <a class="underline p_l10" onclick="Win.close();">取消</a></p></form></div>';

					Win.dialog({width:620,msg:msg_code});
					break;
				case 0:
					Win.dialog({type:'info',msg:'参数错误！',enter:function(){Win.close();}});
					break;
			}
		},'json');
	}
}

/*
 * 开始发送教她和跟他学的消息
 */
function send_learn(type,uid,nickname,skill_id,re_skill_id){
	var type = parseInt(type);
	var uid = parseInt(uid);
	var skill_id = parseInt(skill_id);
	var nickname = comm_str_trim(str_to_safe(nickname));
	var re_skill_id = parseInt(re_skill_id);

	var skill_title, content, check_box;
	var pay_info='';
	
	skill_title = comm_str_trim($("#s_title").text());
	content = comm_str_trim($("#learn_msg_0").val());

	if(1 == type){
		check_box = $("input[name=check_box]:checked").val();
		if(1 == check_box){
			pay_info = comm_str_trim($("#learn_msg_3").val());
			if(('' == pay_info) || ('可以填写愿意付费多少？' == pay_info)){
				pay_info = '愿意付费';
			}
		}
	}
	if('' == content){
		alert("请先输入你的消息内容");return false;
	}else if((0 == type) || (1 == type)){
		send_skill_msg(uid,0,content,type,skill_id,re_skill_id,pay_info,0);
	}
}

/*
 * 加载 交换学习的弹窗口   
 */
function changeskill(uid,nickname,skill_id,ta){
	$.post("/index.php?s=/msg/check/", { friend:uid}, function (data) {
		if(data.stat == 5) {
			var msg_info = redbeans(data.nickname,data.pay_card);
			Win.dialog({'msg':msg_info,'height':400,'width':580,'pay_card':data.pay_card,'enter':function(data){
				$.post("/index.php?s=/msg/check/", { friend:uid, pay_card:data.pay_card}, function (data) {
					if(data.stat) {
						Win.dialog({'msg':data.error, 'type':'alert'});
					}
					else changeskill_div(uid,nickname,skill_id,ta);
				}, 'json');
			}});
		}
		else if(data.stat) {
			Win.dialog({'msg':data.error, 'type':'alert'});
		}
		else changeskill_div(uid,nickname,skill_id,ta);
	}, 'json');
}
function changeskill_div(uid,nickname,skill_id,ta){
	var uid = parseInt(uid);
	var nickname = comm_str_trim(str_to_safe(nickname));
	var ta = comm_str_trim(str_to_safe(ta));
	if('' == ta) ta = 'TA';
	var skill_id = parseInt(skill_id);
	var skill_title = comm_str_trim(str_to_safe($("#get_skill_value1_"+uid+"_"+skill_id).text()));
	var content;

	$.post("/index.php?s=/skilllearn/change_skill","skill_title="+skill_title,function(data){
		switch(data.stat){
			case 1:
				content =	'<div class="exchange_skills" >'
							+'<div class="select_wd1 zindx2" i="select_wd1" k="zindx2" onmouseover="mover(this);" onmouseout="mout(this);" onclick="mclick(this,1);"><p>遇君方知孤芳自赏是个错</p></div>'
							+'<div class="select_wd2 zindx5" i="select_wd2" k="zindx5" onmouseover="mover(this);" onmouseout="mout(this);" onclick="mclick(this,1);"><p>咱们换着学吧</p></div>'
							+'<div class="select_wd3 zindx1" i="select_wd3" k="zindx1" onmouseover="mover(this);" onmouseout="mout(this);" onclick="mclick(this,1);"><p>1+1>3哦</p></div>'
							+'<div class="select_wd4 zindx3" i="select_wd4" k="zindx3" onmouseover="mover(this);" onmouseout="mout(this);" onclick="mclick(this,1);"><p>自学弱爆了，互学走一个</p></div>'
							+'<div class="select_wd5 zindx4" i="select_wd5" k="zindx4" onmouseover="mover(this);" onmouseout="mout(this);" onclick="mclick(this,1);"><p>我们一起学习吧！</p></div>'
							+'<div class="select_wd6 zindx6" i="select_wd6" k="zindx6" onmouseover="mover(this);" onmouseout="mout(this);" onclick="mclick(this,1);"><p>与君共勉！</p></div>'
							+'<h3>选择你要互换学习的技能：</h3><p class="fs_14 f_3 m_t20"> <span>您想用<select id="sel_my_skill" class="fs_12">';

					$.each(data.me_skill,function(k,skill){
						  content += '<option value="'+skill.skill_id+'">'+skill.skill_title+'</option>';
					});

				content += '</select></span> <span>跟<span class="f_blue">'+nickname+'</span>互换学习<span class="fs_14 f_r">'+skill_title+'</span></span> </p>'
						 + '<p class="f_9 m_t15">和'+ta+'说两句</p><p class="m_t10"><textarea name="textarea_text" class="input_1 learn_message f_9" id="learn_msg_1" onfocus="skill_clear_textarea(\'我们一起学习吧！\',1);" onblur="skill_infill_textarea(\'我们一起学习吧！\',1)";>我们一起学习吧！</textarea></p>'
						 + '<p class="m_t30"><a class="btn1" onclick="send_change_skill('+uid+',\''+nickname+'\',\''+skill_title+'\','+skill_id+','+data.re_skill_id+');"'
						 + '>发送</a><span class="p_l10">或&nbsp;</span> <a class="underline p_l10" onclick="Win.close();">取消</a></p></div>';

				Win.dialog({width:620,msg:content});
				break;
			case 0:
				Win.dialog({type:'info',msg:'参数错误！！',enter:function(){Win.close();}});
				break;
			case 2:
				content = '<div class="popup_s_info">你没有填写懂得的技能！还不能跟他进行技能交换！<p class="addskill_link"><a href="/skillmanage/" class="underline">请先去添加你懂得的技能</a></p></div>';
				Win.dialog({type:'info',msg:content,enter:function(){Win.close();}});
				break;
		}
	},'json');
}

/*
 * 发送交换学习的消息
 */
function send_change_skill(uid,nickname,skill_title,skill_id,re_skill_id){
	var uid = parseInt(uid);
	var nickname = comm_str_trim(str_to_safe(nickname));
	var skill_title = comm_str_trim(str_to_safe(skill_title));
	var re_skill_id = parseInt(re_skill_id);
	var other_skill_id = parseInt(skill_id);

	var my_skill_id = $("#sel_my_skill").val();
	var content = $("textarea[name=textarea_text]").val();

	if('' == content){
		alert("请先输入你的消息内容");return false;
	}else if((other_skill_id > 0) && (my_skill_id > 0)) {
		send_skill_msg(uid,0,content,2,other_skill_id,re_skill_id,'',my_skill_id);
	}
}
function show_input(tag){
	$("#learn_msg_"+tag).toggle();
}

/* 
 * 发送消息
 */
function send_skill_msg(uid,pay_card,content,category,skill_id,re_skill_id,pay_money,my_skill_id){
	var uid = parseInt(uid);
	var pay_card = parseInt(pay_card);
	var content = encodeURIComponent(comm_str_trim(str_to_safe(content)));
	var category = parseInt(category);
	var skill_id = parseInt(skill_id);
	var re_skill_id = parseInt(re_skill_id);
	var pay_money = comm_str_trim(str_to_safe(pay_money));
	var my_skill_id = parseInt(my_skill_id);
    $.ajax({
	   type: "POST",
	   url: "/index.php?s=/msg/send/",
	   data: 'receiver_uid='+uid+'&content='+content+'&type=12&pay_card='+pay_card+'&category='+category+'&pay_money='+pay_money+'&other_skill_id='+skill_id+'&re_skill_id='+re_skill_id+'&my_skill_id='+my_skill_id,
	   success: function reply_success(re)
			{
				var result_obj = jQuery.parseJSON(re);
				switch(result_obj.stat)
				{
					case 0: //
							Win.dialog({type:'info',msg:'<img src="'+version_img('ico_mail_sended.png')+'" /> 发送成功！',cancel:function(){}});
							break;
					case 5://
							Win.dialog({msg:redbeans(result_obj.nickname,result_obj.pay_card),width:580,height:400,enter:function(){send_skill_msg(uid,result_obj.pay_card,content,category,skill_id,re_skill_id,pay_money,my_skill_id);}});
							break;
					case 10:
							$("#fengkoujiao").remove();
							$(result_obj.error).appendTo("body");
							var pos = Win.pos(320,250);
							$("#fengkoujiao").css({"top":pos.wt,"left":pos.wl});
							break;
					case 4://
							Win.dialog({type:'info',msg:result_obj.error,enter:function(){},cancel:function(){}});
							break;
					default://
							Win.dialog({type:'info',msg:result_obj.error,cancel:function(){self.location.href=self.location.href;}});
							break;
				}
			}
	});
}

function send_sys_msg(opt,friend, time, mtime){
	if(opt == 1) {
		var msg_alert = '好的，什么时候呢？';
	}
	else {
		var msg_alert = '过段时间再说！';
	}
    $.ajax({
	   type: "POST",
	   url: "/index.php?s=/msg/send/",
	   data: 'receiver_uid='+friend+'&opt='+opt+'&type=13&time='+time+'&mtime='+mtime,
	   success: function reply_success(re)
				{
					var result_obj = jQuery.parseJSON(re);
					switch(result_obj.stat)
					{
						case 0: //
								Win.dialog({type:'info',msg:'<img src="'+version_img('ico_mail_sended.png')+'" /> 发送成功！',cancel:function(){}});
								$("#jn_"+time).attr("class", "reply_mes").html(msg_alert);
								$("#new_"+friend).remove();
								break;
						case 5://
								Win.dialog({msg:redbeans(result_obj.nickname,result_obj.pay_card),width:580,height:400,enter:function(){send_skill_msg(uid,sex,nickname,result_obj.pay_card,content);}});
								break;
						case 10:
								$("#fengkoujiao").remove();
								$(result_obj.error).appendTo("body");
								var pos = Win.pos(320,250);
								$("#fengkoujiao").css({"top":pos.wt,"left":pos.wl});
								break;
						case 4://
								Win.dialog({type:'info',msg:result_obj.error,enter:function(){},cancel:function(){}});
								break;
						default://
								Win.dialog({type:'info',msg:result_obj.error,cancel:function(){self.location.href=self.location.href;}});
								break;
					}
				}
	});
}

/**********************************************技能管理 skillmanage **************************************************/

//textarea框获得或失去焦点时 触发
function mskill_clear_textarea(title){
	var textarea = $("#level_text").val();
	if(title == textarea){
		$("#level_text").empty();
		$("#level_text").attr("class","input_1 learn_message f_0");
	}
}
function mskill_infill_textarea(title){
	var textarea = $("#level_text").val();
	if("" == textarea){
		$("#level_text").text(title);
		$("#level_text").attr("class","input_1 learn_message f_9");
	}
}
//输入框获得或失去焦点时 触发
function mskill_clear_input(title){
	var textarea = $("#pay_input").val();
	if(title == textarea){
		$("#pay_input").val('');
		$("#pay_input").attr("class","input_3 f_0");
	}
}
function mskill_infill_input(title){
	var textarea = $("#pay_input").val();
	if("" == textarea){
		$("#pay_input").val(title);
		$("#pay_input").attr("class","input_3 f_9");
	}
}
//显示div 设置选中项
function mshow_div(tag){
	if('know' == tag){
		$("#know_skill").addClass("selected");
		$("#learn_skill").removeClass("selected");
		$("#know_div").show();
		$("#learn_div").hide();
	}else{
		$("#learn_skill").addClass("selected");
		$("#know_skill").removeClass("selected");
		$("#learn_div").show();
		$("#know_div").hide();
	}
}

//点击添加
function mshow_skill(tag){
	var kl = (0 == tag)?'know':'learn';
	var size = $("#skill_"+kl+"_list li").size();
	if(10 == size){
		alert("技能最多添加十项！");
//		var msg_code = '';
//		Win.dialog({width:450,msg:msg_code});
	}else{
		$("#add_skill"+tag).toggle();
	}
}

function mload_group(group_id,tag){
	$.post("/index.php?s=/skillmanage/skill_group","group_id="+group_id+"&tag="+tag,function(data){
		if(0 == tag){
			$("#knowskill_by_group").empty();
			$("#knowskill_by_group").html(data);
		}else{
			$("#learnskill_by_group").empty();
			$("#learnskill_by_group").html(data);
		}
		$("#group_"+tag+"_"+group_id).siblings().removeClass("selected");
		$("#group_"+tag+"_"+group_id).addClass("selected");
	},'html');
}

//加载添加/修改的技能 ——————tag:添加懂得为0，想学为1。		num：修改时的排序号。		change：添加为0，修改为1。
function mload_skill(tag,num,change,skill_text,level_text,pay_info,gskill_id,is_pay){
	var tag = parseInt(tag);
	var num = parseInt(num);
	var gskill_id = parseInt(gskill_id);
	var is_pay = parseInt(is_pay);
	var skill_text = comm_str_trim(skill_text);
	var level_text = comm_str_trim(level_text);
	var pay_info = comm_str_trim(pay_info);
	var pay_default_info;
	var defult = '可以填写付费的信息';

	var discr = (0 == tag) ? '我的技能水平' : '我想达到的水平';
	if(0 == change){
		var title = (0 == tag) ? '添加懂得的技能：' : '添加想学的技能：';
		var skill_input1 = (skill_text == '') ? "" : skill_text;
		var skill_input2 = (0 == tag) ? '如：骨灰级人物、善于偷各种懒':'如：砖家水平、带我入门';
		var click = "madd_skill("+tag+")";
		var closeclick = "closeclick("+tag+");";
		var btn_title = '添加';
	}else{
		var title = (0 == tag) ? '修改我懂得的技能：' : '修改我想学的技能：';
		var skill_input1 = skill_text;
		var skill_input2 = level_text;
		var click = "mupdate_skill("+tag+","+num+","+is_pay+")";
		var closeclick = "Win.close();";
		var btn_title = '修改';
	}
	if('' == pay_info){
		pay_default_info = defult;
	}else{
		pay_default_info = pay_info;
	}
	var level = (0 == tag) ? '你还没有填写你的技能水平' : '你还没有填写你想达到的水平';
	
	var msg_code = '<div class="addskill_popup"><ul><li class="fs_14">'+title+'</li>'
				 + '<li class="m_t10"><input type="text" value="'+skill_input1+'" class="input_2" id="skill_text"/>'
				 + '<input type="hidden" value="'+skill_input1+'" id="skill_text_hide" /><input type="hidden" '
				 + 'value="'+gskill_id+'" id="gskill_id_hide'+tag+'" /></li>'
				 + '<li class="m_t15"><span class="fs_14">'+discr+'</span> <span class="f_6">(必填，5到500个字)</span> '
				 + '<a href="javascript:void(0);" onclick="example();">'
				 + '范例</a></li><li class="m_t10" id="example" style="display:none;">'
				 + '<p class="prompt_box f_6" style="cursor:default;">如：摄影（有10年的经验，喜欢去户外拍摄，经常参加一些摄影的活动或比赛）</p></li>'
				 + '<li class="m_t5"><p class="nodescription f_6" style="display:none;">'+level+'</p></li>'
				 + '<li class="m_t10"><textarea name="" id="level_text" ';
	if(0 == change){
		msg_code += 'class="input_1 learn_message f_9" onfocus="mskill_clear_textarea(\''+skill_input2+'\');" onblur="mskill_infill_textarea(\''+skill_input2+'\');"'; 
	}else{
		msg_code += 'class="input_1 learn_message f_0" ';
	}
				 
		msg_code += '>'+skill_input2+'</textarea>'
				 + '<input type="hidden" value="'+skill_input2+'" id="level_text_hide"></li>';
	if(1 == tag){
			msg_code += '<li class="m_t10 f_6"><label><input id="checkbox_text" type="checkbox" value="1" class="checkbox1" onclick="mshow_input();"';
			if(1 == is_pay){
				msg_code += 'checked="true" ';
			}
			msg_code += '/>愿意付费</label><input id="pay_input" class="input_3 f_9" type="text" value="'+pay_default_info+'" onfocus="mskill_clear_input(\''+defult+'\');" onblur="mskill_infill_input(\''+defult+'\');" ';
			if(1 != is_pay){
				msg_code += 'style="display:none;"';
			}
			msg_code += '/><input id="pay_hide_input" type="hidden" value="'+pay_default_info+'" /><input id="ispay_hide_input" type="hidden" value="'+is_pay+'" /></li>';
	}

			msg_code += '<li class="m_t30"><a class="btn1" onclick="'+click+'">'+btn_title+'</a><span class="p_l10">或&nbsp;&nbsp;</span>'
					 + '<a class="underline p_l10" onclick="'+closeclick+'">取消</a></li></ul></div>';

	Win.dialog({width:524,msg:msg_code});
}
function example(){
	$("#example").toggle();
}
function mshow_input(){
	$("#pay_input").toggle();
	if($("checkbox_text").attr('checked')!=true){
		$("#pay_input").val('可以填写付费的信息');
	}
}
function closeclick(tag){
	Win.close();
	$("#add_skill"+tag).hide();
}
//添加技能
function madd_skill(tag){
	var num = parseInt(num);
	var title = (0 == tag) ? '如：骨灰级人物、善于偷各种懒':'如：砖家水平、带我入门';
	var pay_info = '';
	var skill_text = comm_str_trim(str_to_safe($("#skill_text").val()));
	var level_text = comm_str_trim(str_to_safe($("#level_text").val()));
	var title = comm_str_trim(str_to_safe(title));
	if(1 == tag){
		pay_info = comm_str_trim(str_to_safe($("#pay_input").val()));

	}
	var check_box = $("#checkbox_text:checked").val();
	var is_pay = 0;
	if(1 != check_box){
		pay_info = '';
	}else{
		is_pay = 1;
	}
	if('可以填写付费的信息' == pay_info){
		pay_info = '';
	}

	if('' == skill_text){
		alert('请填写技能的名称');
	}else if(('' == level_text) || (title == level_text)){
		$(".nodescription").show();
	}else if(skill_text.length > 10){
		alert('你输入的技能已经超过十个字，请简要输入！');
	}else if(level_text.length > 500 || level_text.length < 5){
		alert('技能的描述请输入5到500个字！');
	}else if(pay_info.length >10){
		alert('你输入的付费信息过长，请简要输入！');
	}else{
		$.post("/index.php?s=/skillmanage/add_skill","skill_title="+encodeURIComponent(skill_text)+"&level_title="+encodeURIComponent(level_text)+"&skill_type="+tag+"&pay_info="+encodeURIComponent(pay_info)+"&is_pay="+is_pay,function(re){
				
				switch(re.stat)
				{
					case 0 :alert('参数类型为空');Win.close();break;
					case 1 :
							alert('添加技能成功');Win.close();
							if(0 == tag){
								$("#skill_know_list").prepend('<li class="clear" id="skill_know_li_'+re.msg+'"><p class="text_w">'+skill_text+'<span class="f_6">，'+level_text+'</span></p><p class="btn_w"><a href=\"javascript:void(0);\" onclick=\"mload_skill('+tag+','+re.msg+',1,\''+skill_text+'\',\''+level_text+'\',\'\',0,0)\">修改</a><a href=\"javascript:void(0);\" onclick=\"mupdate_confirm_skill('+tag+',\''+skill_text+'\',\''+level_text+'\','+re.msg+')\">删除</a></p></li>');
							}else{
								var prepend = '';
								if(('' == pay_info) && (1 == is_pay)){
									prepend = '<span class="f_r">(愿意付费)</span>';
								}else if(('' != pay_info) && (1 == is_pay)){
									prepend = '<span class="f_r">(愿意付费，'+pay_info+')</span>';
								}

								$("#skill_learn_list").prepend('<li class="clear" id="skill_learn_li_'+re.msg+'"><p class="fl">'+skill_text+'<span class="f_6">'+prepend+'，'+level_text+'</span></p><p class="btn_w"><a href=\"javascript:void(0);\" onclick=\"mload_skill('+tag+','+re.msg+',1,\''+skill_text+'\',\''+level_text+'\',\''+pay_info+'\',0,'+is_pay+')\">修改</a><a href=\"javascript:void(0);\" onclick=\"mupdate_confirm_skill('+tag+',\''+skill_text+'\',\''+level_text+'\','+re.msg+')\">删除</a></p></li>');
							}

							var kl = (0 == tag)?'know':'learn';

							//如果当前没有添加技能的按钮 就显示一个
							var size = $("#skill_"+kl+"_list + div").size();
							if(0 == size){
								var htm = '<div class="add_btnw clear" id="add_btn'+tag+'"><a href="javascript:void(0);" onclick="mshow_skill('+tag+');"></a> </div>';
								$("#skill_"+kl+"_list").after(htm);
							}else{
								$("#add_btn"+tag).show();
							}
							
							//隐藏 无技能的div和添加技能的按钮
							$("#no_skill"+tag).hide();
							$("#add_skill"+tag).hide();

							//使得已添加过的技能类型 变灰
							var gskill_id = parseInt($("#gskill_id_hide"+tag).val());

							if((gskill_id > 0) && (gskill_id < 1000)){
								$("#group_"+kl+"_a"+gskill_id).attr("class","graybg").removeAttr("onclick");
							}

							break;
					case 2 :
							alert('添加技能失败，请联系管理员！');
							Win.close();
							break;
					case 3 :
							alert('该技能您已经添加过，请填写其他类型！');
							break;
					case 4 :
							alert('你输入的我懂得的技能含有敏感词');
							break;
					case 5 :
							alert('你输入的我想学的技能含有敏感词');
							break;
					case 6 :
							alert('我懂得的技能，不能超过十项！');
							break;
					case 7 :
							alert('我想学的技能，不能超过十项！');
							break;
				}
		},'json');
	}
}

//修改技能
function mupdate_skill(tag,skill_id,is_pay){
	var tag = parseInt(tag);
	var skill_id = parseInt(skill_id);
	var is_pay = parseInt(is_pay);
	var skill_text = comm_str_trim(str_to_safe($("#skill_text").val()));
	var level_text = comm_str_trim(str_to_safe($("#level_text").val()));
	var skill_text_hide = comm_str_trim(str_to_safe($("#skill_text_hide").val()));
	var level_text_hide = comm_str_trim(str_to_safe($("#level_text_hide").val()));
	if(1 == tag){
		var pay_info = comm_str_trim(str_to_safe($("#pay_input").val()));
		var pay_info_hide = comm_str_trim(str_to_safe($("#pay_hide_input").val()));
		var is_pay_hide = comm_str_trim(str_to_safe($("#ispay_hide_input").val()));
	}else{
		var pay_info = '';
		var pay_info_hide = '';
	}
	var check_box = $("#checkbox_text:checked").val();
	var input = '';
	var kl = '';

	if(1 != check_box){
		pay_info = '';
		is_pay = 0;
	}else if(('可以填写付费的信息' == pay_info) && (1 == check_box)){
		pay_info = '';
		is_pay = 1;
	}else{
		is_pay = 1;
	}

	if(skill_text == skill_text_hide){
		if((level_text == level_text_hide) && (pay_info == pay_info_hide) && (is_pay == is_pay_hide)){
			alert('技能修改成功');Win.close();return false;
		}else{
			var input = 'single';
		}
	}

	if('' == skill_text){
		alert('请填写技能类型');
	}else if('' == level_text){
//		alert('请填写技能类型对应的描述！');
		$(".nodescription").show();
	}else if(skill_text.length > 10){
		alert('你输入的技能过长，请简要输入！');
	}else if(level_text.length > 500 || level_text.length < 5){
		alert('技能的描述请输入5到500个字！');
	}else if(pay_info.length >10){
		alert('你输入的付费信息过长，请简要输入！');
	}
	else{
		$.post("/index.php?s=/skillmanage/update_skill","skill_title="+encodeURIComponent(skill_text)+"&level_title="+encodeURIComponent(level_text)+"&pay_info="+encodeURIComponent(pay_info)+"&is_pay="+is_pay+"&skill_id="+skill_id+"&skill_type="+tag+"&input="+input,function(re){
				switch(re.stat){
					case 0 :alert('参数类型为空');Win.close();break;
					case 1 :
							alert('技能修改成功');Win.close();
							kl = (0 == tag)?'know':'learn';
							$("#skill_"+kl+"_li_"+skill_id).empty();

							var prepend = '';
							if(('' == pay_info) && (1 == is_pay)){
								prepend = '<span class="f_r">(愿意付费)</span>';
							}else if(('' != pay_info) && (1 == is_pay)){
								prepend = '<span class="f_r">(愿意付费，'+pay_info+')</span>';
							}
							$("#skill_"+kl+"_li_"+skill_id).prepend('<p class="text_w">'+skill_text+'<span class="f_6">'+prepend+'，'+level_text+'</span></p><p class="btn_w"><a href=\"javascript:void(0);\" onclick=\"mload_skill('+tag+','+re.msg+',1,\''+skill_text+'\',\''+level_text+'\',\''+pay_info+'\',0,'+is_pay+')\">修改</a><a href=\"javascript:void(0);\" onclick=\"mupdate_confirm_skill('+tag+',\''+skill_text+'\',\''+level_text+'\','+re.msg+')\">删除</a></p>');
							break;
					case 2 :
							alert('技能修改失败，请联系管理员！');
							Win.close();
							break;
					case 3 :
							alert('该技能您已经添加过，请填写其他类型！');
							break;
					case 4 :
							alert('你输入的我懂得的技能含有敏感词');
							break;
					case 5 :
							alert('你输入的我想学的技能含有敏感词');
							break;
				}
		},'json');
	}
}

function mupdate_confirm_skill(tag,skill_text,level_text,skill_id){
	if(confirm("你确定要删除 "+skill_text)) 
	mdel_skill(tag,skill_id,skill_text);
}

function mdel_skill(tag,skill_id,skill_text){
	var tag = parseInt(tag);
	var skill_id = parseInt(skill_id);
	var skill_text = comm_str_trim(str_to_safe(skill_text));
	var kl = '';
	$.post("/index.php?s=/skillmanage/delete_skill","skill_id="+skill_id+"&skill_type="+tag+"&skill_title="+encodeURIComponent(skill_text),function(re){
		switch(re){
			case '0':
				Win.dialog({type:'info',msg:'参数错误！！',enter:function(){Win.close();}});
				break;
			case '1':
				kl = (0 == tag)?'know':'learn';
				Win.dialog({type:'info',msg:'删除技能成功',enter:function(){Win.close();}});
				$("#skill_"+kl+"_li_"+skill_id).remove();

				//如果技能全部删掉了，就显示 你还没有任何技能
				var size = $("#skill_"+kl+"_list li").size();
				if(0 == size){
					var htm = '<div class="no_skill" id="no_skill'+tag+'">你还没有任何技能，<a href="javascript:void(0);" onclick="mshow_skill('+tag+');">点击添加</a></div>';
					$("#skill_"+kl+"_list").html(htm);

					//隐藏 无技能的div和添加技能的按钮
					if(0 == tag){
						$("#add_skill0").hide();
						$("#add_btn0").hide();
					}else{
						$("#add_skill1").hide();
						$("#add_btn1").hide();
					}
				}

				break;
			case '2':
				Win.dialog({type:'info',msg:'删除技能失败',enter:function(){Win.close();}});
				break;
		}
	});
}