<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>大学生恋爱网 问问</title>
<meta content="大学生恋爱网，恋爱网，大学生恋爱，大学生谈恋爱，大学生爱情，大学生交友，校园交友，校园恋爱，高校交友，大学生恋爱观，大学生爱情观，大学生恋爱心理，大学生情书，大学生爱情故事" name="keywords" />
<meta content="大学生恋爱网，大学生交友的网站，大学校园里的真实照片的匿名交友，除了上QQ空间你还可以玩的网站。" name="description" />
<link href="<?php echo ($urlstatic2); ?>/css/head_global_main_ask.css<?php echo ($urltail); ?>" rel="stylesheet" type="text/css" />
<script language="javascript" src="<?php echo ($urlstatic2); ?>/js/global_jquery_hello_dialog_chat.js<?php echo ($urltail); ?>"></script>
</head>
<body>
<?php $nav = 4; ?>
<script>
var myuserinfo = <?php echo (json_encode($myuserinfo)); ?>;
</script>
<!--header start-->
<div class="headerbox" style="background:black;">
  <div class="header clear">
    <div class="fl header_l" id="list_con">
      <ul class="list" id="news_list">
      </ul>
    </div>
    <script language="javascript">
        $.get('/index.php?s=/main/operations/',function(data){
			if(data != ''){
				var json_data = eval("("+data+")");
				var lihtml = '';
				var j = 0;
				for(var i in json_data){
					lihtml += '<li>' + json_data[i]['memo'] + '</li>';
					j++;
				}
				$('#news_list').html(lihtml);

				var margin_top = 0;
				$('#list_con').append($("#news_list").clone());
				var scroll_news = function(){

					$('#news_list').animate({'marginTop':-margin_top},1000,function(){
						if(margin_top == j*30){
							$(this).css({'margin-top':'0px'});
							margin_top = 0;
						}
						margin_top += 30;
						setTimeout(scroll_news, 5000);
					});
				};
				if(j>1){
				scroll_news();
				}
			}
        });
        </script>
    <div class="fr header_r" id="header-right"> <span class="search">
      <input class="top_search" name="搜索ID" type="text" value="搜索ID找人">
      <input id="top_search_btn" class="btn" name="搜索" type="button">
      </span><a class="hovf60" href="javascript:;" onclick="addFavorite()">加入收藏</a> <a class="hovf60" href="/">首页</a> <a class="hovf60 p_l10" href="<?php echo ($urldomain); ?>/<?php echo ($GLOBALS['i']['uid']); ?>" target="_blank">我的主页</a>
      <!--<a class="prop" id="prop_link" href="javascript:void(0);">道具礼品</a>-->
      <a class="prop" id="admprop_link" href="javascript:void(0);" style="background-image:url(<?php echo ($urlstatic); ?>/img/top_arrow1.png)">我的管理</a> <a class="hovf60" href="<?php echo ($urlsite); ?>/member/logout">退出</a> </div>
    <!-- 道具礼品下拉选项 start-->
    <div class="propbox" id="propbox" style="">
      <p><a href="<?php echo ($urlsite); ?>/goods">道具礼品中心</a></p>
      <p><a href="<?php echo ($urlsite); ?>/goods/my">我的小仓库</a></p>
      <p><a href="<?php echo ($urlsite); ?>/goods/log/?uid=0">历史记录</a></p>
    </div>
    <!--道具礼品下拉选项 end-->
    <!-- 我的管理下拉选项 start-->
    <div class="admbox" id="admbox" style="">
      <p class="clear"><a href="<?php echo ($urlsite); ?>/profile"><span class="ico_in"></span><span class="fl">基本资料</span></a></p>
      <P><a href="<?php echo ($urlsite); ?>/user/password/"><span class="ico_set"></span><span class="fl">账号设置</span></a></P>
      <P><a href="<?php echo ($urlsite); ?>/pay/card_log?type=link"><span class="ico_redbean"></span><span class="fl">红豆账户</span></a></P>
    </div>
    <!--我的管理下拉选项 end-->
  </div>
</div>
<!--header end-->
<script type="text/javascript">
$(function(){
    //搜索框获得焦点时
    $('.top_search').focus(function(){
        if(/^\d+$/.test($(this)[0].value) == false){
            $(this)[0].value = '';
        }
    });
    //搜索框失去焦点时
    $('.top_search').blur(function(){
        if(/^\s*$/.test($(this)[0].value)){
            $(this)[0].value = "搜索ID找人";
        }
    });
    
    function search_logic(obj){
        if(/^\d+$/.test(obj.value) == false){
                Win.dialog({'type':'info','width':250,'msg':'ID为数字类型，输入无效！'});
            }else{
                var uid = obj.value
                $.get('/user/search?uid='+uid,function(data){
                    var j_data = eval("("+data+")");
                    if(parseInt(j_data['error']) != 0){
                        Win.dialog({'type':'info','width':250,'msg':j_data['msg']});
                    }else{
                        window.location.href="/"+uid;
                    }
                });
            }
    }
    //搜索框输入enter时
    $('.top_search').keydown(function(e){
        if(e.which == 13){
            search_logic($(this)[0]);
        }
    });
    //或者点击搜索按钮时
    $('#top_search_btn').click(function(){
        search_logic($('.top_search').eq(0)[0]);
    });

    //道具礼品下拉菜单
    $('#prop_link').mouseover(function(){
        $(this).css({'color':'black','background':'#FFF url(<?php echo $urlstatic ?>/img/top_arrow2.png?gv=88_1) no-repeat scroll 65px 50%'});
        $('#propbox').css({'display':'block'});
        
    });
    $('#header-right').mouseover(function(e){
        if($(e.target).parents('#propbox')[0] == undefined && e.target.id != 'prop_link'){
            $('#propbox').css({'display':'none'});
            $('#prop_link').css({'color':'white','background':'url(<?php echo $urlstatic ?>/img/top_arrow1.png?gv=88_1) no-repeat scroll 65px 50%'});
        }
    });
    $('#propbox').mouseleave(function(){
        $('#propbox').css({'display':'none'});
        $('#prop_link').css({'color':'white','background':'url(<?php echo $urlstatic ?>/img/top_arrow1.png?gv=88_1) no-repeat scroll 65px 50%'});
    });
    
    //我的管理下拉菜单
    
    $('#admprop_link').mouseover(function(){
         $(this).css({'color':'black','background':'#FFF url(<?php echo $urlstatic ?>/img/top_arrow2.png?gv=88_1) no-repeat scroll 65px 50%'});
        $('#admbox').css({'display':'block'});
        
    });
    $('#header-right').mouseover(function(e){
        if($(e.target).parents('#admbox')[0] == undefined && e.target.id != 'admprop_link'){
            $('#admprop_link').css({'color':'white','background':'url(<?php echo $urlstatic ?>/img/top_arrow1.png?gv=88_1) no-repeat scroll 65px 50%'});
            $('#admbox').css({'display':'none'});
        }
    });
    $('#admbox').mouseleave(function(){
        $('#admbox').css({'display':'none'});
        $('#admprop_link').css({'color':'white','background':'url(<?php echo $urlstatic ?>/img/top_arrow1.png?gv=88_1) no-repeat scroll 65px 50%'});
    });
});
</script>
<div class="wrap">
<div class="head clear">
  <h1 class="newlogo fl clear"><a href="/" class="g_logo_a fl"><img src="<?php echo ($urlstatic); ?>/img/logo.gif?gv=88_1" alt="大学生恋爱网" title="大学生恋爱网" /></a></h1>
  <script>
            var gShowLog = 0;
            var g_chat_msg = 0;
			var gGetLoseMsg2 = 1;
        </script>
  <div class="g_nav">
    <ul>
      <li class="g_nav_bg1"> <a href="/" class="<?php if(($nav) == "1"): ?>current<?php endif; ?>"><span>打分</span></a> </li>
      <li class="g_nav_bg6"> <a href="<?php echo ($urlsite); ?>/find" class="<?php if(($nav) == "2"): ?>current<?php endif; ?>"><span><?php echo (ui_sex($GLOBALS['i']['sex'],'2')); ?></span></a> </li>
      <li class="g_nav_bg2">
        <b class="sm_mb sm_mb_w" id="new_mail_call" <?php if(($f['new_msg']) < "1"): ?>style="display:none;"<?php endif; ?>> <b class="sm_mb sm_mb_a"> <b id="_new_mail_num" class="sm_mb sm_mb_b"><?php echo ($f['new_msg']); ?></b> </b></b>
        <a href="<?php echo ($urlsite); ?>/msg/" class="<?php if(($nav) == "3"): ?>current<?php endif; ?>"><span>搭讪</span></a> </li>
      <li class="g_nav_bg8"> <a href="<?php echo ($urlsite); ?>/task/" class="<?php if(($nav) == "5"): ?>current<?php endif; ?>"><span>师兄帮帮忙</span></a> </li>
      <li class="g_nav_bg9">
        <?php if($f['new_answer'] != 0): ?><div style="" id="new_answer_call" class="new_messages"><b id="_new_answer_num"><?php echo ($f['new_answer']); ?></b></div><?php endif; ?>
        <a href="<?php echo ($urlsite); ?>/question/<?php if($f['new_answer'] == 0): ?>plaza<?php endif; if($f['new_answer'] != 0): ?>sender<?php endif; ?>" class="<?php if(($nav) == "4"): ?>current<?php endif; ?>"><span>问问</span></a> </li>	  
      <li class="pos_re_box" id="menu_more_parent">
        <div href="javascript:void(0);" class="menu_more clear" id="menu_more_link" style="padding:5px 12px;"> <span class="menu_mo"></span><span class="fl pd0" style="white-space:nowrap;">更多(3)</span>
          <!--menu更多下拉框 start-->
          <ul class="menu_morelist" id="menu_morelist" style="display:none;">
            <li style="width:80px;margin-left:0;"> <a href="<?php echo ($urlsite); ?>/diary/" class="more_skillbox"  style="width:80px;height:40px;">
              <p class="more_skillico"></p>
              <p>写两句</p>
              </a> </li>
            <li style="width:80px;margin-left:0;"> <a href="<?php echo ($urlsite); ?>/photo/" class="more_skillbox"  style="width:80px;height:40px;">
              <p class="more_skillico2"></p>
              <p>我的相册</p>
              </a> </li>
            <li style="border:0;width:80px;margin-left:0;"> <a href="<?php echo ($urlsite); ?>/wenwen/" class="more_skillbox"  style="width:80px;height:40px;">
              <p class="more_skillico3"></p>
              <p>小编专访</p>
              </a> </li>
          </ul>
          <!--menu更多下拉框 end-->
        </div>
      </li>
    </ul>
  </div>
  <script type="text/javascript">
            //var is_more_open = false;
            //鼠标滑过事件
            $('#menu_more_link').mouseover(function(){
				$(this).css({'background':'url(<?php echo $urlstatic ?>/img/menu_morebg.png?gv=88_1) no-repeat scroll 0 0 transparent'});
                $('#menu_morelist').css({'display':'block'});
            });
            //鼠标移动监测
            $("body").mousemove(function(e){
                if($(e.target).parents('#menu_more_parent')[0]==undefined){
                    $('#menu_more_link').css({'background':''});
                    $('#menu_morelist').css({'display':'none'});
                    //is_more_open = false;
                }
            });
        </script>
  <script>
                    var gUid = "18090226";
                    var gSex = "1";
                    var gVip = "0";
                    var gIfChat = "0";
                    var gAccount = "jaredwu@qq.com";
                    var gNickName = "jaredwu";
                    var gAuthKey = "c56ba6a2a263491ab3239d9dce5b223e";
                    var gShowLog = 0;
                </script>
  <div id="pop_list"></div>
  <div style="display:none;position:absolute;left:900px;top:680px;z-index:1000;" id="closelog">
    <input type="button" value="关闭" onclick=" $('#sock').css('height',0);">
    <input type="button" onclick="$('#sock').css('height',300)" value="显示">
  </div>
  <div style="width:0px;height:0px;position:absolute;left:100px;top:100px;z-index:1000" id="imflash">
    <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="0" height="0"  id="sock" name="sock" align="middle"  >
      <param name="allowScriptAccess" value="sameDomain" />
      <param name="movie" value="<?php echo ($urlstatic); ?>/flash/chat.swf" />
      <param name="quality" value="high" />
      <param name="bgcolor" />
      <param name="wmode" value="transparent" />
    </object>
  </div>
</div>

<div class="container_cwrap clear">
  <!--左边部分开始-->
  <div class="fm_l_200">
    <div class="myhead_box">
  <div class="my_info clear">
    <p class="person_img fl"> <a href="<?php echo ($urlsite); ?>/photo"> <img src="<?php echo ($urlupload); echo ($GLOBALS['i']['default_pic']); ?>_72x72.jpg" /> </a> </p>
    <div class="base_info fl">
      <p class="fs_14"><a href="<?php echo ($urlsite); ?>/profile" class="f_0 unline"><?php echo ($GLOBALS['i']['username']); ?></a></p>
      <p class="fs_12 f_9"><?php echo ($GLOBALS['i']['college']); ?></p>
	  <p class="f_6"><?php if(($GLOBALS['i']['group_type']) < "2"): ?>普通会员<?php else: ?>正式会员<?php endif; ?></p>  
      <div class="photo_msg_box2"><?php if(($GLOBALS['i']['group_type']) < "2"): ?><a target="_blank" href="<?php echo ($urlsite); ?>/usergroup/">成为正式会员！</a><?php endif; ?></div>
    </div>
  </div>
  <div class="f_6 mark_box">平均：<a class="f_green1 fs_16" href="<?php echo ($urlsite); ?>/usergroup/share_score/" target="_blank"><?php echo ($GLOBALS['i']['score_impress']); ?>分</a></div>
</div>
<div class="newsbox">
  <div class="newsbox_m">
    <ul>
      <li class="clear line" style="overflow:hidden;display:none;" id="notice_div">
        <div id="infozone" style="position:absolute;width:160px;height:40px;overflow:hidden;" class="f_green1"></div>
      </li>
    </ul>
    <ul id="left_newsbox">
      <li class="clear line"> <span class="fl fs_14"><a href="<?php echo ($urlsite); ?>/hot/in/" class="f_blue1">收到打分</a></span>
        <?php if($f['new_receive_score'] != 0): ?><span class="fr"><a href="<?php echo ($urlsite); ?>/hot/in/" class="f_r fb_12 unline" id="m_ping">新</a></span><?php endif; ?>
        <?php if($f['new_receive_score'] == 0): ?><span class="fr"><a href="<?php echo ($urlsite); ?>/hot/in/" class="f_6 unline" id="m_ping"><?php echo ($f['receive_score_num']); ?></a></span><?php endif; ?>
      </li>
      <li class="clear line"><span class="fl fs_14"><a href="<?php echo ($urlsite); ?>/fav/out/" class="f_blue1">我收藏的人</a></span><span class="fr f_6"><a href="<?php echo ($urlsite); ?>/fav/out/" class="f_6 unline" id="m_fav_new"><?php echo ($f['fav_out_num']); ?></a></span><a title="我收藏的人当前在线"  href="<?php echo ($urlsite); ?>/fav/out/?online=1" id='fav_online'></a></li>
      <li class="clear line"> <span class="fl fs_14"><a href="<?php echo ($urlsite); ?>/fav/in/" class="f_blue1">谁收藏了我</a></span>
        <?php if($f['new_fav_in'] != 0): ?><span class="fr  f_r fb_12"><a href="<?php echo ($urlsite); ?>/fav/in/" class="f_r fb_12 unline" id="m_fav_new">新</a></span><?php endif; ?>
        <?php if($f['new_fav_in'] == 0): ?><span class="fr f_6"><a href="<?php echo ($urlsite); ?>/fav/in/" class="f_6 unline" id="m_fav_new"><?php echo ($f['fav_in_num']); ?></a></span><?php endif; ?>
      </li>
      <li class="clear line"> <span class="fl fs_14"><a href="<?php echo ($urlsite); ?>/attention/more/" class="f_blue1">动态</a></span>
        <?php if($f['new_attention'] != 0): ?><span class="fr  f_r fb_12" ><a href="<?php echo ($urlsite); ?>/attention" class="f_r fb_12 unline" id="m_attention_new">新动态</a></span><?php endif; ?>
      </li>
      <li class="clear">
        <div class="clear list"> <span class="fl fs_14"><a href="<?php echo ($urlsite); ?>/visit/in/" class="f_blue1">来访者</a></span> <span class="old_messages" > <b> <b> <b>
          <?php if($f['new_visitor'] != 0): ?><b><a href="<?php echo ($urlsite); ?>/visit/in/" id="m_visit_new_count"><?php echo ($f['new_visitor']); ?></a></b><?php endif; ?>
          </b> </b> </b> </span> </div>
        <div class="list2" id="visit_face">
          <?php if(is_array($visitlist1)): $i = 0; $__LIST__ = $visitlist1;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$d): $mod = ($i % 2 );++$i;?><a href="<?php echo ($urldomain); ?>/<?php echo ($d["uid"]); ?>" target="_blank"> <img src="<?php echo ($urlupload); ?>/<?php echo ($d['default_pic']); ?>_72x72.jpg" width="38" height="38" alt="" title="" border="0" /> </a><?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
      </li>
    </ul>
  </div>
  <div class="newsbox_b"></div>
</div>
<script>
                var visit_new_uid_str = '19506395';
			</script>
<!--给新人打分开始-->
<!--<div class="newsbox m_t5" id="reg_ping_div" style="">
  <div class="newsbox_m">
    <div class="grade_bar clear"> <a class="ga3"></a> <a class="ga4"></a> <a class="ga5"></a> <a class="ga6"></a> <a class="ga7"></a> <a class="ga8"></a> <a class="ga9"></a> <a class="ga10"></a> </div>
    <div class="newpersongrade">
      <dl class="clear" id="wait_reg_ping_div">
        <dt><img class="hover" id="wait_reg_ping_photo" src="http://img1.jjdd.com/c72/99/67/9967d9cef59081387899293bebdea214.jpg" alt="" /></dt>
        <dd id="reg_ping_result" >
          <p> <span class="f_r">有新人！</span>您的打<br/>
            分决定<?php echo (ui_sex($m['sex'])); ?>是否能<br/>
            加入,6分以下表<br/>
            示不欢迎加入！ </p>
        </dd>
      </dl>
      <div  id="big_img" class="big_img"> <img id="big_img_url" alt="" src="http://img1.jjdd.com/c240/70/c4/70c442c58673bd296c5262c68b9fe57e.jpg"> </div>
    </div>
  </div>
  <div class="newsbox_b"></div>
</div>-->
<!--给新人打分结束-->
<p class="comment_tip" id="comment_tip_flag" style="left:400px;top:200px;z-index:99999"></p>
<script>
var reg_photo_id = 9627888;
var reg_photo_url = '';
var big_img_url = '';
$("#wait_reg_ping_photo").mouseover(function() {
    $("#big_img").show();
});
$("#big_img").mouseout(function(){
     $("#big_img").hide();
});
function reg_mark(score){
	clearTimeout(time_reg_ping);
	$.ajax({
	   type: "POST",
	   url: "/score/user_mark/",
	   data: 'pid='+reg_photo_id+'&score='+score,
	   success: function order_result(re){
	   		var obj = jQuery.parseJSON(re);
	   		show_ping_form(reg_photo_id,score,obj.ping_ret);
	   		
	   		if(obj.result==1)
	   		{
	   			if(obj.photo_id>0) reg_photo_id = obj.photo_id;
	   			if(obj.img_url!='') reg_photo_url = obj.img_url;
	   			if(obj.big_img_url!='') big_img_url = obj.big_img_url;
	   		}
	   		else
	   		{
	   			reg_photo_id = 0;
	   			reg_photo_url = '';
	   		}
	   }
	});
	
}
function show_reg_ping_tips()
{
	var code = '<p>您的打分至关重要，低于<span class="fb_16 f_yelo">60分</span>的人将不予进入，请认真打分！</p>';
	Win.dialog({type:'info',width:560,msg:code});
}
function show_ping_form(photo_id,score,ping_ret)
{
	var code = '<div class="pop_appraise">\
			    	<dl class="clear">\
			        	<dt><img width=48 src="'+$("#wait_reg_ping_photo").attr("src")+'" alt=""  /></dt>\
			            <dd>您给<?php echo ui_sex($m['sex'], 3) ?>打了：<span class="fb_20 f_yelo">'+score+'</span> 分</dd>\
			        </dl>\
			         <div class="word_box">\
			            <p>请选择您对<?php echo ui_sex($m['sex'], 3) ?>的评价：</p>\
			              <ul class="clear">\
			              ';
	var i=0;
	for(index in ping_ret)
	{
		var ping_arr = ping_ret[index].split(",");
		code += '<li  class="font_w'+Math.floor(i/3)+' font_w_flag"  onclick="reg_ping('+photo_id+','+i+')" tip="'+ping_arr[1]+'">'+ping_arr[0]+'</li>';
		i++;
	}
	code +='</ul>\
			        </div>\
			    </div>\
			';
	Win.dialog({width:580,height:350,msg:code,noclose:true,cancel:function(){show_next_reg_ping();}});
 	mouseover_display_font();
}
function mouseover_display_font(){
	$(".font_w_flag").mousemove(function(e) {
		if($.browser.msie) {
			$("#comment_tip_flag").css({"width":$(this).attr("tip").length*10});
		}
		$("#comment_tip_flag").html($(this).attr("tip")).css({"position":"absolute","top":e.pageY+20,"left":e.pageX+10}).show();
	  });

	$(".font_w_flag").mouseout(function() {
		$("#comment_tip_flag").hide();
	  });
	  
}
function click_custom_ping()
{
	if($("#custom_ping").val()=='如果以上选项不够，请点击此处自写，每个人都希望收到别人为他单独写的东西！')
	{
		$("#custom_ping").val('');
	}
}
function blur_custom_ping()
{
	if($("#custom_ping").val()=='')
	{
		$("#custom_ping").val('如果以上选项不够，请点击此处自写，每个人都希望收到别人为他单独写的东西！');
	}
}
function reg_ping(photo_id,content_id) {
	var content = $("#comment_tip_flag").html();
    $('#send_ping_btn').attr('disabled',true);
    $.ajax({
	   type: "POST",
	   url: "/score/user_ping/",
	   data: 'pid='+photo_id+'&content='+encodeURIComponent(content),
	   success: function ping_success(re)
				{
					var obj = jQuery.parseJSON(re);
					Win.close();
					switch(obj.result)
					{
						case 1://
								reg_photo_id = obj.photo_id;
								reg_photo_url = obj.img_url;
								if(obj.big_img_url!='') big_img_url = obj.big_img_url;
								show_next_reg_ping();
								break;
					 	case 0://
					 		reg_photo_id = 0;
							reg_photo_url = '';
					 		$("#reg_ping_div").fadeOut();
					}
				}
	});
}
function show_next_reg_ping()
{
	var tmp_code='<div class="font_b">\
			          <p>点评成功</p>\
			      </div>';
	$("#reg_ping_result").html(tmp_code);
	$("#wait_reg_ping_div").fadeOut('slow',function(){show_next_reg_photo();});
}
function show_next_reg_photo()
{
	if(reg_photo_url!='')
	{
		$("#wait_reg_ping_photo").attr("src",reg_photo_url);
		$("#big_img_url").attr("src",big_img_url);
		$("#reg_ping_result").html('<p><span class="f_r">有新人！</span>您的打<br/>分决定他是否能<br/>加入,6分以下表<br/>示不欢迎加入！</p>');
		$("#wait_reg_ping_div").fadeIn();
		
		clearTimeout(time_reg_ping);
		time_reg_ping = setInterval("notice_reg_ping();",500);
	}
	else
	{
		$("#reg_ping_div").fadeOut();
		setTimeout("get_need_ping()",5000);
	}
}
var time_reg_ping;
$(document).ready(function() { 
	$(".grade_bar a").click(function(){
		var score = this.className.substring(2);
		reg_mark(score);
	});
    if(0 == 0 && 775 >= 3 && 0.5 < 0.5){
		var msg = '<div class="pop_info_contact">'
				   + '	<p style="color:#676767";>你还不能进行其他操作！</p>'
				   + '	<p style="margin:15px 0 0 0;">请先<a href="/profile/">完善您的资料</a> <span class="f_r1">50%</span> 以上！</p>'
				   + '	<div class="clear" style="margin:15px 0 0 100px;">'
				   + '		<div class="fl">您目前的资料完成度为 <span class="f_blue1">50%</span>&nbsp;</div>'
				   + '		<div class="fl" style="margin:3px 0 0 0;"><div class="fillbar fl" title="50% completed"><span style="width:25px"></span></div></div>'
				   + '	</div>'
				   + '</div>'
				   + '<div class="opt"><a href="/profile/" class="btn1">知道了</a></div>'
		msg = '<div class="popup_c">'+msg+'</div>';
		Win.dialog({'msg':msg,noclose:true});
	}
	time_reg_ping = setInterval("notice_reg_ping();",500);
});
function notice_reg_ping()
{
	if($("#wait_reg_ping_photo").attr('class') == 'hover')
	{
		$("#wait_reg_ping_photo").removeClass();
	}
	else
	{
		$("#wait_reg_ping_photo").addClass('hover');
	}
}
function get_need_ping(score){
	clearTimeout(time_reg_ping);
	$.ajax({
	   type: "GET",
	   url: "/score/get_need_ping/",
	   success: function order_result(re){
	   				var obj = jQuery.parseJSON(re);
					switch(obj.result)
					{
						case 1://
								reg_photo_id = obj.photo_id;
								reg_photo_url = obj.img_url;
								if(obj.big_img_url!='') big_img_url = obj.big_img_url;
								$("#reg_ping_div").show();
								show_next_reg_photo();
								break;
						default ://
								setTimeout("get_need_ping()",5000);
					}
	   }
	});
	
}
</script>
<div class="vda_video_box">
  <div class="box">
    <h3>如何保证照片真实？</h3>
    <p class="list">网络虚拟，但不应虚假。为了营造一个真实轻松的校园交友场所，需要大家从上传真实照片开始。</p>
    <p class="list">所以，我们将人工审核每位会员的个人形象照。审核标准是性别一致、非网络照片，生活照最佳。</p>
    <p><a target="_blank" class="input" href="<?php echo ($urlsite); ?>/photo/up_form/?gid=0">上传形象照</a></p>
  </div>
  <p><img src="<?php echo ($urlstatic); ?>/img/approve_boxbg_b.gif<?php echo ($urltail); ?>"></p>
</div>

  </div>
  <!--左边部分结束-->
  <!--中间部分 start-->
  <div class="ask_center ask_w2 clear">
    <!--问问菜单 start-->
    <div class="ask_nav clear"> <a target="_blank" href="<?php echo ($urlsite); ?>/other/kf/?mtype=7" class="opinion_fb f_6">对“问问”的意见反馈</a>
      <ul class="fl">
        <li><a href="<?php echo ($urlsite); ?>/question/plaza/" class="" >问问广场</a></li>
        <li><a href="<?php echo ($urlsite); ?>/question/add/" class=" active_nav">我要问问</a></li>
        <li><a href="<?php echo ($urlsite); ?>/question/sender/" class="">问问管理</a></li>
        <li><a href="<?php echo ($urlsite); ?>/wenwen/" class="" >小编专访</a></li>
      </ul>
    </div>
    <!--问问菜单 end-->
    <!--问问表单 start-->
    <div class="ask_main  clear">
      <h4>想得到建议或有什么想问的，快来写下你的问题吧！<span class="f_r">（带有图片的问题,会得到更多的回答者帮助
        ）</span></h4>
      <div class="war_box clear">
        <div class="put_question">
          <div class="l">
            <textarea name="question" id="question">请在此输入您的问题..</textarea>
            <p class="f_9" id="msg_question">最多可输入120字</p>
          </div>
          <ul class="r">
            <li>
              <select name="question_type" id="question_type">
                <option value="">请选择您的问题分类</option>
                <option value="1"  >情感人生</option>
                <option value="2"  >娱乐八卦</option>
                <option value="3"  >工作学习</option>
                <option value="4"  >音乐电影、文艺</option>
                <option value="5"  >健康、美食</option>
                <option value="6"  >旅游、运动</option>
                <option value="7"  >咸得蛋疼</option>
                <option value="8"  >宠物</option>
                <option value="9"  >其它</option>
              </select>
              <span class="f_r" id="msg_question_type"></span> </li>
            <li class="m_t10">
              <label class="nm">
              <input type="checkbox" class="checkbox1" name="sync" id="is_anonymity" onclick="sync_check('is_anonymity')">
              <span class="f_6" title="匿名提问后，问题将不会出现在您的个人主页上，对方也不会知道是谁提问">匿名提问</span></label>
              <label class="nm">
              <input type="checkbox" class="checkbox1" checked="checked" name="sync" id="is_show" onclick="sync_check('is_show')" disabled="disabled">
              <span class="f_6">公开到我的主页</span></label>
              <label class="nm" title="发布动态后,收藏您的人将会在他关注的动态中看到您的问题">
              <input type="checkbox" class="checkbox1" checked="checked" name="sync" id="is_attention" onclick="sync_check('is_attention')">
              <span class="f_6">发布动态</span></label>
            </li>
          </ul>
        </div>
        <div class="rightbar">
          <div class="upload_pic" onclick="question_open_photo_windows();">
            <div class="_js_image_show"></div>
            <input type="hidden" name="photo_id" id="photo_id" value="" />
          </div>
          <p class="clear f_r" id="msg_question_photo"></p>
        </div>
      </div>
      <div class="askbtn clear">
        <input id="modify_submit" onclick="return check_question_form()" name="modify_submit" type="button" class="btn1" value="发送问题">
        <!--<p class="m_t5 f_9"><font class="f_r">温馨提示：</font>每1小时内最多能提问2个问题。请把握机会提出自己的优质问题，有可能会得到推荐被置顶哦！</p>-->
      </div>
    </div>
  </div>
  <!--问问表单 end-->
  <!--中间部分 end-->
  <!--中间中间部分结束-->
</div>
<!--中间部分结束-->
<!-----------------图片弹出层-------------------------->
<iframe name="photo_frame" id="photo_frame" style="width:0;height:0;border:1px solid #ccc; display:none;" src=""></iframe>
<div id="_js_my_photo" style="display:none;"></div>
<div id="_js_other_photo" style="display:none;"></div>
<div id="_js_wenwen_photo" style="display:none;"></div>
<!--end-->
<!-- 图片表单 -->
<div id="_js_querstion_file_form" style="display:none;">
  <input name="photo_num" value="5" type="hidden" />
  <input name="time" value="1322381055" type="hidden" />
  <input name="key" value="8279ea464fae708d3f6099106733d59e" type="hidden" />
  <input name="uid" value="18090226" type="hidden" />
  <input name="from" value="question" type="hidden" />
  <input name="up_type" class="_js_up_type" value="flash" type="hidden" />
</div>
<div id="tip_hot" style="background-color: #CAE5F8; border: 1px solid #A6C8E0; position: absolute; display:none;  z-index: 10001; width: 147px; height: 107px;"> 需要购买3个红豆 </div>
<div id="tip_hot_buy" style="background-color: #ffffff; border: 3px solid #A6C8E0; position: absolute; display:none;  z-index: 10001; width: 420px; height: 200px;"> </div>
<!--------------------------end-------------------------->
<script>
var warn = 0;
jQuery(function($){
    $("#question").bind("focus",function(){foucsQuestion()});
    $("#question").bind("blur",function(){blurQuestion()});
    $("#question").bind("keyup change drop",function(){countQuestion()});
    $("#question_type").bind("change",function(){changeQuestionType()});
});
setInterval(function (){
if($("#question").val() !='请在此输入您的问题..'){countQuestion()}
},500);
function foucsQuestion(){
    if($("#question").val() == "请在此输入您的问题..") {
        $("#question").val("");
    }
    $("#msg_question").attr("class","f_9");
}

function countQuestion(){
    
    var msg_sum = 120;
    var input_count = $("#question").val().length;
    var msg_count = msg_sum-input_count;
    var msg = "您还可以输入"+ msg_count +"/120字";
    
    if( input_count > msg_sum )
    {
        var out_count =  input_count - msg_sum;
        msg = "<img class='ico' src='"+version_img('ico_alert.gif')+"' />已超过"+out_count+"字";
        warn = out_count;
        $("#msg_question").attr("class","f_r");
    }
    
    $("#msg_question").html(msg);
    $("#question").css("color","#000000");
}

function blurQuestion(){
    
    if($("#question").val().trim() == "") {
    
        $("#question").val("请在此输入您的问题..");
        $("#question").css("color","#999999");
        var msg = "<img class='ico' src='"+version_img('ico_alert.gif')+"' /> 问题内容不能为空.";
        $("#msg_question").attr("class","f_r");
        $("#msg_question").html(msg);
    }
    if(warn >0)
    {
        var msg = "<img class='ico' src='"+version_img('ico_alert.gif')+"' />已超过"+warn+"字";
         $("#msg_question").attr("class","f_r");
        $("#msg_question").html(msg);
        warn = 0;
    }
    
}
function changeQuestionType(){
    if(Number($("#question_type").val()) >0) {
        $("#msg_question_type").html("");
    }
}
function sync_check(id){
    var checkinfo = $("#"+id).attr('checked');
    if(id=="is_anonymity"){
        if(checkinfo == 'checked' ){
            $("input[name='sync']").attr('checked',false);
            $("#"+id).attr('checked','checked');
//             $("#is_show").attr('disabled',false); 
            document.getElementById('is_show').disabled=false;
        }
    }
    else if(id=="is_show"){
        if(checkinfo == 'checked' ){
            $("#is_anonymity").attr('checked',false);
            $("#"+id).attr('checked','checked');
        }
    }
    else if(id=="is_attention"){
        if(checkinfo == 'checked' ){
            $("input[name='sync']").attr('checked',true);
            $("#is_anonymity").attr('checked',false);
            $("#is_show").attr('disabled','disabled'); 
        }else{
//             $("#is_show").attr('disabled',false); 
            document.getElementById('is_show').disabled=false;
        }
    }
    
}

//检查问题表单
function check_question_form(){
    var check_flag =0;
    if($("#question").val().trim() == "" || $("#question").val().trim() == "请在此输入您的问题..") {
        var msg = "<img class='ico' src='"+version_img('ico_alert.gif')+"' /> 问题内容不能为空";
        $("#msg_question").attr("class","f_r");
        $("#msg_question").html(msg);
        check_flag =1;
    }
    if($("#question_type").val().trim() == 0) {
        var msg = "<img class='ico' src='"+version_img('ico_alert.gif')+"' /> 请选择提问问题的类型.";
        $("#msg_question_type").html(msg);
        $("#question_type").select();
        check_flag =1;
    }
//     if($("#photo_id").val().trim() == "") {
//         var msg = "<img class='ico' src='"+version_img('ico_alert.gif')+"' /> 你还没有上传图片.";
//         $("#msg_question_photo").html(msg);
//         check_flag =1;
//     }
    
    var msg_sum = 120;
    var input_count = $("#question").val().length;
    if( input_count > msg_sum )
    {
        var out_count =  input_count - msg_sum;
        msg = "<img class='ico' src='"+version_img('ico_alert.gif')+"' />已超过"+out_count+"字";
        $("#msg_question").attr("class","f_r");
        $("#msg_question").html(msg);
        check_flag =1;
    }
    if(check_flag==1){
        return false;	
    }
    if($("#photo_id").val().trim() == "") {
        var tmpflag = 1; 
        Win.dialog({msg:'<div class="popup_c"><div class="popup_s_info">确定不上传图片发布问题吗？<br></div><div class="opt"><a class="btn1" onclick="question_open_photo_windows();">上传图片</a> 或 <a class="dashed"  onclick="Win.close(true);">确定</a></div></div>',height:300,width:400,enter:function(){tmpflag = 2;submit_question();}});
        if(tmpflag == 1){
            return false;
        }
        

    }
    submit_question();
    //return true;
}
//发布问题
function submit_question() {
//     if(check_question_form()==false){
//         return false;
//     }
    
    var is_anonymity=0;
    if($('#is_anonymity').attr("checked")=="checked"){
        is_anonymity=1;
    }
    var is_show=0;
    if($('#is_show').attr("checked")=="checked"){
        is_show=1;
    }
    var is_attention=0;
    if($('#is_attention').attr("checked")=="checked"){
        is_attention=1;
    }

    Win.dialog({'msg':'正在发布问题，请稍等......','type':'warn','noclose':true});
    $.ajax({
        type: "POST",
        url: "<?php echo ($urlsite); ?>/question/submit_question/",
        data: 'question='+$('#question').val()+'&question_type='+$('#question_type').val()+'&photo_id='+$('#photo_id').val()+'&is_anonymity='+is_anonymity+'&is_show='+is_show+'&is_attention='+is_attention,
        success: function(re){ 
            var obj = jQuery.parseJSON(re);
            if(obj.errno == 200) {
                //sync_weibo(obj);
                Win.dialog({'msg':'问题发布成功','type':'info',enter:function(){location.href='<?php echo $urlsite ?>/question/add';},cancel:function(){location.href='<?php echo $urlsite ?>/question/add';}});
            }else{
                Win.dialog({'msg':obj.msg,'type':'info'});
            }
        }
    });
}
function show_warn(){
    
    Win.dialog({msg:'<div class="popup_c"><div class="popup_s_info">确定不上传问题相关的图片吗? <br></div><div class="opt"><a class="btn1" onclick="question_open_photo_windows();">上传图片</a> 或 <a onclick="submit_question();">发布问题</a></div></div>',height:300,width:400});

  
}
//显示选人页面
var fav_cache;
function request_show_fav_ui(){
    if(typeof (fav_cache) === "object") {
        show_fav_ui(fav_cache);
    }
    else {
        $.post('/question/get_fav_and_sync', {is_sync:0}, function(data) {
            fav_cache = data;
            show_fav_ui(data);
        }, 'json');
    }
    
}
//显示选人页面
function show_fav_ui(fav){
    var my_fav = ''; 
    var fav_my = '';
    if(typeof (fav.fav_result) === "object") {
        var i = 0;
        for(var uid in fav.fav_result) {
            my_fav += '<li id="fav_user_'+i+'" onclick="select_fav_user(this)" uid='+uid+'  card_num='+fav.fav_result[uid].card_num+' nickname="'+fav.fav_result[uid].nickname+'">'
                   +'<div class="photo"><img src="'+fav.fav_result[uid].face+'" width="72" height="72"></div>'
                   +'<p>'+fav.fav_result[uid].nickname+'</p>'
                   +'<p class="f_6">'+fav.fav_result[uid].location_prov+'，'+fav.fav_result[uid].location_city+'</p>'
                   +fav.fav_result[uid].hot+'</li>';
            i++;
        }
    }
    else my_fav = '<li>您还没有收藏任何人</li>';
    if(typeof (fav.fav_i_result) === "object") {
        var i = 0;
        for(var uid in fav.fav_i_result) {
            fav_my += '<li id="fav_i_user_'+i+'" onclick="select_fav_user(this)" uid='+uid+'  card_num='+fav.fav_i_result[uid].card_num+' nickname="'+fav.fav_i_result[uid].nickname+'">'
                   +'<div class="photo"><img src="'+fav.fav_i_result[uid].face+'" width="72" height="72"></div>'
                   +'<p>'+fav.fav_i_result[uid].nickname+'</p>'
                   +'<p class="f_6">'+fav.fav_i_result[uid].location_prov+'，'+fav.fav_i_result[uid].location_city+'</p>'
                   +fav.fav_i_result[uid].hot+'</li>';
            i++;
        }
    }
    else fav_my = '<li>还没有人收藏你</li>';
    /*
    <a id="select_curr_all" onclick="select_fav_all(\'all\')">选择所有</a><a onclick="select_fav_all(\'clear\')">取消所选</a>
    */
    var msg_code='<div class="choose_box">'
                +'	<p class="fs_14"><b>请选择回答问题的人：</b><span class="f_6">（选人回答时需要为热度用户支付红豆，若已建立联系，则不需要再次支付）</span></p>'
                +'	    <div class="nav clear">'
                +'		<div class="fl"><a href="javascript:;" class="active" id="fav_ui_a" onclick="select_show_fav_ui(\'my\')">我收藏的人</a><a href="javascript:;" id="fav_i_ui_a" onclick="select_show_fav_ui(\'friend\')">收藏我的人</a></div>'
                +'	    <div class="fr f_6"><label><input id="select_curr_all" name="" type="checkbox" class="checkbox1" onclick="select_fav_all()"> 全选</label></div>'
                +'		</div>'
           
                +'		<div class="content">'
                +'		<div class="search">'
                +'		<input name="" type="text" class="input_1 fs_12" value="昵称模糊搜索"><img src=\"http://pic.jjdd.com/v1/i/pub/search_ico.png?gv=82_1\"/>'
                +'		</div>'
                
                +'	<ul id="choose_fav_user" class="clear" style="display:block;">'
                +   my_fav 
                +'	</ul>'
                +'	<ul id="choose_fav_i_user" class="clear" style="display:none;">'
                +   fav_my 
                +'	</ul>'
                +'	</div>'
                +'	<div class="clear btn"><a href="javascript:;" onclick="set_uid_list()" class="btn3">确认</a></div>'
                +'</div>';
     
    Win.dialog({msg:msg_code,width:760,height:540});
    init_select_user();
    jQuery(function($){
       // $(".hot_bar").bind("mouseover",function(event) {
            //get_pageXY(event);
       // });
        $(".hot_bar").bind("mouseout",function() { $("#tip_hot").hide();});
    });
}
function show_hot_tip(obj,nickname,card_num){
       //console.log($(".choose_box").offset().left+",top:"+$(".choose_box").offset().top);
    var current_left = $(obj).offset().left-40;
    var current_top = $(obj).offset().top-112;
    //console.log(current_left+",top:"+current_top);
    $("#tip_hot").css({"left":current_left,"top":current_top});
    $("#tip_hot").html(nickname+"是热度用户，需要预扣您"+card_num+"颗红豆才能与他建立联系，否则他将无法看到您的问题。");
    $("#tip_hot").show();
}

function show_hot_tip_buy(nicknamecon,sum_card){

//     var box_left = $(".choose_box").offset().left+140;
//     var box_top = $(".choose_box").offset().top+112;
    var sex_cn = (myuserinfo.sex == 1)?'她':'他';
    var buy_html = '您需要为您指定的热度用户'+nicknamecon+'预扣总共'+sum_card+'颗红豆，他们才能看到您的问题（对方回复您后才扣除红豆）回复成功后，将来与他们联系将不再需要红豆。如果对方一周内未回复您的问题，系统将退回您预付的红豆！（红豆1元一颗）<a target="_blank" href="/pay/order/?card_num='+sum_card+'">购买</a>'
        +'<br/>'
        +'	<div style="padding:10px 0;text-align:right;">'
        +'		<span class="f_9">您的红豆：</span><span class="f_r1">'+myuserinfo.card_num+'颗</span> &nbsp;&nbsp;<a onclick="tip_redbean('+sum_card+','+myuserinfo.card_num+')" class="btn1 btn_b1" >预扣红豆，和'+sex_cn+'聊天</a> &nbsp; 或 &nbsp; <a onclick="request_show_fav_ui();" class="dashed">取 消</a>'
        +'	</div>';
        
//     $("#WinDiv").css({"z-index":2});
//     $("#tip_hot_buy").css({"left":box_left,"top":box_top});
//     $("#tip_hot_buy").html(buy_html);
//     $("#tip_hot_buy").show();
    Win.dialog({msg:buy_html,noclose:true,enter:function(){request_show_fav_ui();}});
}
function hide_hot_tip_buy(flag)
{
    $("#WinDiv").css({"z-index":9999});
    if(flag)
    {
        tip_redbean();
    }
    $("#tip_hot_buy").hide();
}
function tip_redbean(sum_card,card_num)
{
    if(sum_card>card_num)
    {
        var err_msg = '你的红豆不够，请<a href="http://www.jjdd.com/pay/order/" target="_blank">购买红豆</a>!';
        Win.dialog({type:'info',msg:err_msg,noclose:true,enter:function(){request_show_fav_ui();}});
    }
    else
    {
        $.ajax({
        type: "POST",
        url: "/link/multi_link/",
        data: 'uid_list='+$('#to_uid_list').val()+'&my_card_num'+card_num,
        success: function(re){ 
            var obj = jQuery.parseJSON(re);
            if(obj.errno == 200) {
                Win.dialog({'msg':'成功指定回答的人','type':'info'});
            }else{
                Win.dialog({'msg':obj.msg,'type':'info'});
            }
        }
        });
    }
    
}

function get_pageXY(e)
{   
    console.log($(".choose_box").offset().left+",top:"+$(".choose_box").offset().top-105);
    console.log("e.pageX: " + e.pageX + ", e.pageY: " + e.pageY);
}
//发布成功后同步页面
function sync_weibo(obj){

    var msg_code='<div class="ask_weibo">'
                +'	<div class="clear tit"><img class="fl" src="http://pic.jjdd.com/v1/i/pub/popup_g3.png?gv=82_1" width="40" height="39" /><h2 class="fl">发送问题成功!</h2></div>'
                +'	<div class="cont clear">'
                +'	    <div class="contleft fl">'
                +'		<div class="bgtop">'
                +'	    <div class="pic"><img src="'+obj.photo_path+'" /></div>'
                +'	    </div>'
                +'		<div class="bgcenter">'
                +'	        <p class="question">问题：<span class="fs_14">'+obj.question+'</span></p>'
                +'	    </div>'
                +'	    </div>'
                +'	    <div class="btn fl">'
                +'		<p class="f_9">将问题同步出去，获得更多答案！</p>'
                +'	    <a onclick="auth_weibo(1,'+obj.qid+')" class="btn_bg">发布到我的新浪微博</a>'
                +'	    <a onclick="auth_weibo(2,'+obj.qid+')" class="btn_bg2">发布到我的腾讯微博</a>'
                +'	    <a href="/question/plaza/" class="btn1 clear"><img src="http://pic.jjdd.com/v1/i/pub/icon_weiboback.png?gv=82_1" width="18" height="17" /><span>返回</span></a>'
                +'	    </div>'
                +'	</div>'
                +'</div>';

    Win.dialog({msg:msg_code,width:720,height:520});
    
}

function auth_weibo(weibo,qid)
{   
    Win.close();
    if(weibo==1)
    {
        if(0)
        {
            window.open("http://www.jjdd.com/tsina/bind/?question_id="+qid,"","height=500, width=600");
        }
        else
        {
            $.ajax({ 
                type: "GET",
                data: 'question_id='+qid+'&ts=1',
                url: "/question/sync_weibo/",
                success: function(re){ 
                    var obj = jQuery.parseJSON(re);
                    if(obj.errno == 200) {
                        Win.dialog({'msg':obj.msg,'type':'info',enter:function(){location.href='/question/plaza';},cancel:function(){location.href='/question/plaza';}});
                    }else{
                        Win.dialog({'msg':obj.msg,'type':'info',enter:function(){location.href='/question/plaza';},cancel:function(){location.href='/question/plaza';}});
                    }
                }
            });
        }
    }
    else if(weibo==2)
    {
        if(1)
        {
            window.open("http://www.jjdd.com/tqq/bind/?question_id="+qid,"","height=600, width=700");
        }
        else
        {
            $.ajax({ 
                type: "GET",
                url: "/question/sync_weibo/",
                data: 'question_id='+qid+'&tqq=1',
                success: function(re){ 
                    var obj = jQuery.parseJSON(re);
                    if(obj.errno == 200) {
                        Win.dialog({'msg':obj.msg,'type':'info',enter:function(){location.href='/question/plaza';},cancel:function(){location.href='/question/plaza';}});
                    }else{
                        Win.dialog({'msg':obj.msg,'type':'info',enter:function(){location.href='/question/plaza';},cancel:function(){location.href='/question/plaza';}});
                    }
                }
            });
        }
    }


}
//初始化以选中的用户
function init_select_user()
{

    var i = 0 ;
    var j = 0 ;
    var to_uid_list = $("#to_uid_list").val();

    fav_length = $("#choose_fav_user li").length;
    fav_i_length = $("#choose_fav_i_user li").length;
    //获得选取我收藏的人的uid队列
    if(fav_length>1)
    {
        for (i = 0; i < fav_length; i++) 
        {
            if(to_uid_list.indexOf($("#fav_user_"+i).attr("uid")) != -1)
            {
                $("#fav_user_"+i).addClass("active");
            }
            
        }
    }

    //获得选取收藏我的人的uid队列
    if(fav_i_length>1)
    {
        for (j = 0; j < fav_i_length; j++) 
        {
            if(to_uid_list.indexOf($("#fav_i_user_"+j).attr("uid")) != -1)
            {
                $("#fav_i_user_"+j).addClass("active");
            }
            
        }
    }
}
//获取已选择的uidlist
function set_uid_list()
{

    var i = 0 ;
    var j = 0 ;
    var num = 0;
    var to_uid_list;
    var nicknamecon ;
    var sum_card = 0 ;
    
    fav_length = $("#choose_fav_user li").length;
    fav_i_length = $("#choose_fav_i_user li").length;
    //获得选取我收藏的人的uid队列
    if(fav_length>1)
    {
        for (i = 0; i < fav_length; i++) 
        {
            if($("#fav_user_"+i).attr("class").indexOf("active") != -1)
            {
                
                if(to_uid_list === undefined)
                {
                    to_uid_list = $("#fav_user_"+i).attr("uid");
                    if(parseInt($("#fav_user_"+i).attr("card_num"))>0)
                    {
                        nicknamecon = $("#fav_user_"+i).attr("nickname");
                        sum_card = parseInt($("#fav_user_"+i).attr("card_num"));
                    }
                    
                    
                }
                else
                {
                    to_uid_list = to_uid_list+","+$("#fav_user_"+i).attr("uid");
                    if(parseInt($("#fav_user_"+i).attr("card_num"))>0)
                    {
                        nicknamecon = nicknamecon+","+$("#fav_user_"+i).attr("nickname");
                        sum_card = sum_card + parseInt($("#fav_user_"+i).attr("card_num"));
                    }
                }
                num++;
            }
            
        }
    }

    //获得选取收藏我的人的uid队列
    if(fav_i_length>1)
    {
        for (j = 0; j < fav_i_length; j++) 
        {
            if($("#fav_i_user_"+j).attr("class").indexOf("active") != -1)
            {
            
                fav_i_uid = $("#fav_i_user_"+j).attr("uid");
                if(to_uid_list === undefined)
                {
                    to_uid_list = fav_i_uid;
                    num++;
                }
                else
                {
                    if(to_uid_list.indexOf(fav_i_uid) == -1)
                    {
                        to_uid_list = to_uid_list+","+fav_i_uid; 
                        num++;
                    }
                }
                if(sum_card === 0)
                {
                    if(parseInt($("#fav_i_user_"+j).attr("card_num"))>0)
                    {
                        nicknamecon = $("#fav_i_user_"+j).attr("nickname");
                        sum_card = parseInt($("#fav_i_user_"+j).attr("card_num"));
                    }
                }
                else
                {
                    if(nicknamecon.indexOf($("#fav_i_user_"+j).attr("nickname")) == -1)
                    {
                        if(parseInt($("#fav_i_user_"+j).attr("card_num"))>0)
                        {
                            nicknamecon = nicknamecon+","+$("#fav_i_user_"+j).attr("nickname");
                            sum_card = sum_card +  parseInt($("#fav_i_user_"+j).attr("card_num"));
                        }
                    }
                }

            }
            
        }
    }

    if(sum_card>0)
    {
    tip_redbean();
       show_hot_tip_buy(nicknamecon,sum_card);
    }
    $("#to_uid_list").val(to_uid_list);
    $("#select_user").html("指定人回答(已选择"+num+"人)");
    
    //Win.close();
}
//点击选中或取消选人
function select_fav_user(obj)
{  
    if($(obj).attr('class').indexOf("active") != -1)
    {
        $(obj).removeClass("active");
    }
    else{
        $(obj).addClass("active");
    }
}

//选中或取消所有我收藏的人
function select_show_fav_ui(type)
{  
    if(type=="my")
    {
        $("#choose_fav_i_user").css("display","none");
        $("#choose_fav_user").css("display","block");
        $("#fav_ui_a").addClass("active");
        $("#fav_i_ui_a").removeClass("active");
    }
    else if(type=="friend"){
        $("#choose_fav_user").css("display","none");
        $("#choose_fav_i_user").css("display","block");
        $("#fav_i_ui_a").addClass("active");
        $("#fav_ui_a").removeClass("active");
    }
}
//选中或取消所有我收藏的人
function select_fav_all()
{  
    var type = "clear";
    if($('#select_curr_all').attr("checked")==true){
        type = "all";
    }
    var fav_user_show = $("#choose_fav_user").attr("style");
    var fav_i_user_show = $("#choose_fav_i_user").attr("style");
    if(type=="all")
    {

        if(fav_user_show.indexOf("block") != -1)
        {
            $("#choose_fav_user li").addClass("active");
        }
        else if(fav_i_user_show.indexOf("block") != -1)
        {
           $("#choose_fav_i_user li").addClass("active");
        }
    }
    else if(type=="clear"){
        if(fav_user_show.indexOf("block") != -1)
        {
            $("#choose_fav_user li").removeClass("active");
        }
        else if(fav_i_user_show.indexOf("block") != -1)
        {
           $("#choose_fav_i_user li").removeClass("active");
        }
        
    }
}

//选中或取消所有收藏我的人
function select_fav_i_all(type)
{  
    
    if(type=="all")
    {
        $("#choose_fav_i_user li").addClass("active");
    }
    else if(type=="clear"){
        $("#choose_fav_i_user li").removeClass("active");
    }
}
//---------------------------------------------//

//--------------高级上传--------------//
    var up_fail = 0;
    var file_selected = false;
    var window_top  ='<div class="upload_photo">'
                +'<h3>请上传一张与您问题相关的图片</h3>'
                /* +'<div class="options">'
                +'<ul>'
                +'<li class="computer"><a onclick="question_upfile()">从电脑</a></li>'
                +'<li class="photo nowli"><a onclick="question_change_photo(1017632,\'_js_my_photo\');">从已上传的图片中</a></li>'
                +'</ul>'
                +'</div>' */
                +'<div class="content m_t30">';
var photo_top='';
var photo_bottom='';
/* var photo_top='<div class="photo_nav clear"><a class="" href="javascript:question_change_photo(1017632,\'\')">个人形象照</a><a class="_js_other_photo" href="javascript:question_change_photo(1017633,\'_js_other_photo\')">其它照片</a><a class="_js_other_photo" href="javascript:question_change_photo(1277522,\'_js_other_photo\')">童年与家乡</a><a class="_js_other_photo" href="javascript:question_change_photo(1277523,\'_js_other_photo\')">吃喝玩乐</a><a class="_js_other_photo" href="javascript:question_change_photo(1277524,\'_js_other_photo\')">摄影作品</a><a class="_js_other_photo" href="javascript:question_change_photo(1277525,\'_js_other_photo\')">猪一样的队友</a><a class="_js_other_photo" href="javascript:question_change_photo(1277526,\'_js_other_photo\')">私密相册</a><a class="_js_other_photo" href="javascript:question_change_photo(1340839,\'_js_other_photo\')">dddd</a>'
             +'</div>'
             +'<div class="photo_list clear">';
var photo_bottom='</div><input onclick="Win.close()" type="button" value="   确定    " class="btn3"></div>'; */
var up_photo='<p class="f_6 m_t10">'
             +'<span class="f_r">温馨提示：</span>请上传一张与您问题相关的图片，尽量不要使用<span class="f_r">个人形象照</span>提问！'
             +'</p>'
             +'<p class="text_c m_t5"><img src="http://pic.jjdd.com/v1/i/pub/img_sl.png"/></p>'
             +'<b class="fl fs_14 f_green">例：</b><p class="fs_14 f_3 m_l">问：心情不好时你怎么办？<span class="f_6">'
             +'（可以找一些<span class="f_r">失落、难过、烦躁</span>等意境的图！)</span><br />'
             +'问：你对另一半有怎么的要求了？<span class="f_6">(可以找一些<span class="f_r">情侣、恋爱</span>等意境的图！)</span></p>';
var window_bottom='</div>';
    
    function check_upload_status()
    {
        if(up_fail==0)
        {
            Win.close();
            var datas = $("iframe").contents().find("body").html();
            $.post("<?php echo ($urlsite); ?>/photo/api_querstion_up_photo",{'data': datas},
            function(data){
                if(data.status==1){
                    $('#photo_id').val(data.photo_id);
                    var html = '<img border="0" src="'+data.img_path+'">';
                    $('._js_image_show').html(html);
                    $('.text').html('<span>点击这里</span>重新上传问题图片');
                }
            },"json");
        }
        return true;
    }


    //回调函数
    function upload_fail(message,i)
    {
        up_fail = 1;
        Win.dialog({msg:message,height:100,width:500,type:'info'});
        $('#uprow_'+i).html($('#uprow_'+i).html());
        if(i==1){
            return false;
        }
        init_flash();
        file_selected = false;
    }
    
    function init_flash()
    {
        try{
        
            if($.browser.msie)
            {
                $("#markObjIE")[0].init_flash();
            }
            else
            {
                $("#markObj")[0].init_flash();
            }
               

        }
        catch(e)
        {
            alert(e);
        }
        return true;
    }

    var check_flash = 0;

    function flash_onload(){
        check_flash = 1;
    }

    function up_finish(result){
        var obj = jQuery.parseJSON(result);
        if(obj.result=='1' && obj.file_md5!='')
        {
            $("#upfile1").val(obj.file_md5);
            check_form();
        }
    }
    function check_timeout()
    {
        if(check_flash==0){
            var html='';
            html='<div class="clear up_txtbox"><p class="fl">图片：</p><p class="fl"><input style="float:left;" size="35" onkeypress="return false;" id="upfile1" name="upfile1" onpaste="return false" type="file"  class="btn3" value="浏览..."></p></div>'
                +'<div class="bot_btn"><input type="button" value="上传图片" onclick="check_form()" class="btn1">支持 jpg , jpeg , png , bmp 四种图片格式</div>'+up_photo;
            $("#_js_flash_flash").html(html);
            $("._js_up_type").val('my');
            }else{
                return false;
        }
    }
    //js调用flash内部函数提交图片数据
    function start_upload(){
        try{
            if($.browser.msie)
            {
                $("#markObjIE")[0].startUpload();
            }
            else
            {
                $("#markObj")[0].startUpload();
            }
        }
        catch(e)
        {
            alert(e);
        }
        return true;
    }

    //flash调用成选中状态
    function file_select(name)
    {
        if(name!='')
        {
            file_selected = true;
        }
        else
        {
            file_selected = false;
        }
    }
    //flash调用显示错误信息
    function flash_error(message)
    {
        up_fail = 1;
        file_selected = false;
        Win.dialog({msg:message,height:100,width:500,type:'info'});
    }
    //---------------------------//
    //上传等待
    function show_up_process()
    {
        var msg_code = '<div class="popup_c" style="padding:0 0 30px 70px; text-align:left;">'
                     + ' <p>图片正在上传，请稍等....<br/><br/></p>'
                     + ' <p class="photo_progress"><img src="http://pic.jjdd.com/v1/i/pub/uping.gif?gv=82_1" alt="loading" /></p>'
                     + '</div>' ;
        Win.dialog({width:400,msg:msg_code,noclose:true});
    }
//---------------------------------------------//

//图片选择加载
function question_change_photo(groups,group_name){
    return false;
    /* if(groups!=0){
        var photo_data =$('#'+group_name).html();
        if(photo_data){
            $('.content').html(photo_data);
        }else{
            $.post("/photo/api_querstion_get_photo",{'uid': 18090226,group:groups,key:'8279ea464fae708d3f6099106733d59e',time:'1322381055'},
            function(data){
                var html='';
                photo_top = photo_top.replace('f_6','');
                photo_top = photo_top.replace(group_name,group_name+' f_6');
                if(data.status==1){
                    $.each(data.datas,function(id,item){
                        html = html+'<div onclick="question_checked_photo(this)" class="'+item.photo_id+'" style="cursor:pointer;"><img width="72" height="72" border="0" alt="" src="'+item.img_path+'"></div>';
                    });
                    html = photo_top+html+photo_bottom;
                }
                if(data.status==2){
                    html = photo_top+'<span onclick="question_upfile();" style="cursor:pointer;">请上传您的图片</span>'+photo_bottom;
                    
                }
                if(data.status==0){
                    html = photo_top+'参数错误'+photo_bottom;
                }
                $('#'+group_name).html(html);
                $('.content').html(html);
           },"json");
        }
        $('.photo').addClass('nowli');
        $('.computer').attr('class','computer');
    } */
}

//初始化弹出层
function question_open_photo_windows()
{
    var html='';
    html = '....';
    html = window_top+photo_top+html+photo_bottom+window_bottom;
    Win.dialog({msg:html,width:555,height:520});
    question_upfile();
}


//图片上传
function question_upfile(){
    var from_top = '<form name="photo_form" action="/index.php?s=/photo/wenwenup" target="photo_frame" method="post" enctype="multipart/form-data">'
                 +'<div>';
    var now =new Date();
    now =now.getSeconds();
	var win_upload = {msg:"请先点击 [浏览...] 选择一张图片！",type:"info"};
    var flash_up ='<div id="_js_flash_flash"><div class="clear up_txtbox"><p class="fl">图片：</p>'
                 +''
                 +'<p class="fl"><object width="320" height="28" align="middle" id="markObjIE" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000">'
                 +'<param value="always" name="allowScriptAccess">'
                 +'<param value="transparent" name="wmode">'
                 +'<param value="/i/MiniUpload.swf?onselect=file_select&callback=up_finish&onload=flash_onload&min_width=200&min_height=120&on_error=flash_error&ver=0.2&v='+now+'" name="movie">'
                 +'<param value="high" name="quality">'
                 +'<embed id="markObj" width="320" height="28"  align="middle" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" allowscriptaccess="sameDomain" swliveconnect="true" quality="high" wmode="transparent" src="/i/MiniUpload.swf?onselect=file_select&callback=up_finish&onload=flash_onload&min_width=200&min_height=120&on_error=flash_error&ver=0.2&v='+now+'">'
                 +'</object></p>'
                 +'<input type="hidden" name="upfile1" value="" id="upfile1" />'
                 +'</div>'
                 +'<div class="bot_btn"><input class="btn1" type="button" onclick="if(file_selected){start_upload();}else{Win.dialog(win_upload);return false;}" value="上传图片" />支持 jpg , jpeg , png , bmp 四种图片格式</div>'+up_photo+'</div>';
    var form_bottom ='</form></div>';
    $('.content').html(from_top+flash_up+$('#_js_querstion_file_form').html()+form_bottom);
    $('.computer').addClass('nowli');
    $('.photo').attr('class','photo');
    setTimeout('check_timeout();',1000);
}
//图片框样式更改
/* function question_checked_photo(obj){
    var get_text_photo_id = $('#photo_id').val();
    var get_photo_id      = $(obj).attr('class');
    $('#photo_id').val(get_photo_id);
    $(obj).css('border','1px solid red');
    if(get_text_photo_id != get_photo_id && get_text_photo_id !=''){
        $('.'+get_text_photo_id).css('border','1px solid #cccccc');
    }
    var box_html = $(obj).children('img').attr('src');
    box_html = '<img src="'+box_html+'" />';
    $('._js_image_show').html(box_html);
    $("#msg_question_photo").html("");
} */

function check_form()
{
    var num = 0;
    var file=$("#upfile1").val();
    if(file!="" && file!="http://"  )
    {
        num++;
    }
    if(num == 0)
    {
        Win.dialog({msg:'请先点击 [浏览...] 选择一张图片！',type:'info'});
        return false;
    }
    var _photo_num=document.getElementsByName("photo_num");
    var photo_now_num = num + parseInt(_photo_num[1].value);
    if(photo_now_num >200)
    {
        Win.dialog({msg:'图片数量超过上限！',type:'info'});
        
        return false;
    }
    document.photo_form.submit();
    show_up_process();
    
    $('#photo_frame').get(0).onload = check_upload_status;
    if ( $('#photo_frame').get(0).attachEvent != null )
    {
        $('#photo_frame').get(0).attachEvent( 'onload', check_upload_status);
    }
    $("#msg_question_photo").html("");
    
    return true;
}

</script>
  <div class="hristmas_footer clear" style="margin-top:50px; border-top:1px solid #C9C9C9; background:none; color:#666666; font-size:12px;">
    <div class="fl">
      <p style="margin:0;">Copyright © 2011 大学生恋爱网. All Rights Reserved</p>
      <p style="margin:0;">ICP证：鄂ICP备09029087号 &nbsp; 鄂ICP备09029087号-2</p>
    </div>
    <p class="fr"><a style="color:#666" href="<?php echo ($urlsite); ?>/other/about/" target="_blank">关于我们</a>|<a style="color:#666" href="<?php echo ($urlsite); ?>/other/job/" target="_blank">招贤纳士</a>|<a style="color:#666" href="<?php echo ($urlsite); ?>/other/contact/" target="_blank">联系方式</a>|<a style="color:#666" href="<?php echo ($urlsite); ?>/other/kf/" target="_blank">意见建议</a>|<a style="color:#666" href="<?php echo ($urlsite); ?>/other/reg_agreement/" target="_blank">服务条款</a></p>
  </div>
  <div style="display:none"><script src="http://s16.cnzz.com/stat.php?id=3754210&web_id=3754210" language="JavaScript"></script></div>
</div>
</body>
</html>