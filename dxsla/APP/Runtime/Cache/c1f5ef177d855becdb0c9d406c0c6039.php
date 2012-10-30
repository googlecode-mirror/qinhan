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

<!--中间部分开始-->
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
    <div class="ask_nav clear"> <a target="_blank" href="/other/kf/?mtype=7" class="opinion_fb f_6">对“问问”的意见反馈</a>
      <ul class="fl">
        <li><a href="<?php echo ($urlsite); ?>/question/plaza/" class="" >问问广场</a></li>
        <li><a href="<?php echo ($urlsite); ?>/question/add/" class="">我要问问</a></li>
        <li><a href="<?php echo ($urlsite); ?>/question/sender/" class=" active_nav">问问管理</a></li>
        <li><a href="<?php echo ($urlsite); ?>/wenwen/" class="" >小编专访</a></li>
      </ul>
    </div>
    <div class="ask_main1">
      <div class="question_options"> <span class="fl"><a href="<?php echo ($urlsite); ?>/question/sender/">我的提问</a> <a href="<?php echo ($urlsite); ?>/answer/my_answer/?order=0" class="fontstyle1">我的回答</a> </span>
        <span class="fr"><a class="<?php if(($order) == "1"): ?>f_bl<?php else: ?>f_9<?php endif; ?>" href="<?php echo ($urlsite); ?>/answer/my_answer/?order=0" class="f_9">全部回答</a> <a href="<?php echo ($urlsite); ?>/answer/my_answer/?order=1" class="<?php if(($order) == "0"): ?>f_bl<?php else: ?>f_9<?php endif; ?>">只看有内容的</a></span>
      </div>
      <div class="question_list clear">
        <ul id="choose_question_id">
		  <?php if(empty($list)): ?><div class="no_question">您还没有回答过问题,<a href="<?php echo ($urlsite); ?>/question/plaza/">马上去回答&gt;&gt;</a></div><?php endif; ?>
          <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li id="li_<?php echo ($vo["id"]); ?>" class="clear" onmouseout="this.className='clear';$('#show_<?php echo ($vo["id"]); ?>').hide();" onmouseover="this.className='bluebg clear';$('#show_<?php echo ($vo["id"]); ?>').show();">
			  	<div class="img">
			  	<?php if(($vo['is_anonymity']) == "0"): ?><a href="<?php echo ($urldomain); ?>/<?php echo ($m['uid']); ?>"  target="_blank"><img src="<?php echo ($urlupload); ?>/<?php echo ($vo["default_pic"]); ?>_120x120.jpg" width="48" height="48"/></a>
				<?php else: ?>
				<?php $sex = 3 - $GLOBALS['i']['sex'] ?>
				<img src="<?php echo ($urlstatic); ?>/img/none_s<?php echo ($sex); ?>_48_c.jpg" width="48" height="48"/><?php endif; ?>
                  <!--头像-->
                </div>
                <div class="rightbar">
                  <div class="title clear">
                    <p class="fs_14 word_break"><?php if($vo[is_anonymity] == 1): ?>匿名<?php else: ?><a class="f_bl word_nowrap"  href="<?php echo ($urldomain); ?>/<?php echo ($vo['uid']); ?>"  target="_blank"><?php echo ($vo["username"]); ?></a><?php endif; ?>&nbsp;<span class="f_9 word_nowrap">[<?php echo (($vo['type_name'])?($vo['type_name']):'其它'); ?>]</span>&nbsp;<?php echo ($vo["question"]); ?></p>
                    <div class="m_t20"><?php if(!empty($vo['photo_url'])): ?><img id="imgsmall_<?php echo ($vo["photo_id"]); ?>" src="<?php echo ($urlupload); ?>/<?php echo ($vo["photo_url"]); ?>_120x120.jpg" onclick="scaleImg(<?php echo ($vo["photo_id"]); ?>,'<?php echo ($urlupload); ?>/<?php echo ($vo["photo_url"]); ?>_480x480.jpg')" class="mousezoom_tip"/> <img id="imgbig_<?php echo ($vo["photo_id"]); ?>" style="display:none;" class="mousezoom_min" onclick="scaleImg(<?php echo ($vo["photo_id"]); ?>,'')"><?php endif; ?></div>
                    <div class="m_t20 botinfo clear">
                      <p class="fr" id="show_<?php echo ($vo["id"]); ?>" style="display:none;"> <a class="close" title="删除" id="del_<?php echo ($vo["id"]); ?>" onclick="delete_confirm(<?php echo ($vo["id"]); ?>)"></a></p>
					  <?php $agree = round(($vo['agree_count']/($vo['agree_count']+$vo['against_count']))*100); $against = round(($vo['against_count']/($vo['agree_count']+$vo['against_count']))*100); ?>					  
                      <p class="fl clear"> <span class="agree fl"><?php echo ($agree); ?>%</span> <span class="opposition fl"><?php echo ($against); ?>%</span> <span class="fl">回答总数(<?php echo ($vo["answer_count"]); ?>)</span><span class="fl"><a target="_blank" href="<?php echo ($urlsite); ?>/home/question/?uid=<?php echo ($vo["uid"]); ?>&amp;qid=<?php echo ($vo["qid"]); ?>">点击查看全部答案</a></span></p>
                    </div>
                    <div class="my_reply">
                      <div class="box clear">
                        <div class="text fl"><span class="fl f_3">我的回答：</span><a class="agree fl"></a><?php echo ($vo["answer_cont"]); ?>
                          <p class="word_break"></p>
                        </div>
						<?php if(($vo['star_num']) > "0"): $num = $vo['star_num'] > 3 ? $vo['star_num'] - 3 : $vo['star_num']; ?>
						<span class="<?php if(($vo['star_num']) > "3"): ?>sp_defecate<?php else: ?>sp_star<?php endif; ?> bg_postion<?php echo ($num); ?> fr"></span><?php endif; ?>
                      </div>
                    </div>
                  </div>
                </div>
                <span id="qtime_<?php echo ($vo["id"]); ?>" class="time f_9 fs_12"><?php echo (formattime($vo["add_time"])); ?></span></li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
        <div class="turn_page" id="page">
          <div class='page'><?php echo ($page); ?> </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!--中间部分 end-->
</div>
<!--内容部分结束-->
</div>
</div>
<script>
var limit =1;
function delete_ianswer(id) {
    if (id > 0)
    {
        $.ajax({
            type: "POST",
            url: "<?php echo ($urlsite); ?>/answer/delete",
            data: 'aid_list='+id,
            success: function(re){ 
                var obj = jQuery.parseJSON(re);
                //$("#li_"+id).remove();
                //Win.dialog({'msg':obj.msg,'type':'info'});
                location.reload(true);
            }
        });
    }
    else
    {
        Win.dialog({title:'警告', msg:'请选择要删除的问题', type:'alert'});
        return false;
    }
} 
function delete_confirm(id)
{
    Win.dialog({type:'confirm',msg:'是否删除该问题？',height:100,enter:function(){delete_ianswer(id);},enterName:'确定'});
}
function scaleImg(id,img)
{
    bid = 'imgbig_'+id;
    sid = 'imgsmall_'+id;
    if(img){
        $('#'+bid).attr('src',img);
        $('#'+bid).show();
        $('#'+sid).hide();
    }else{
        $('#'+bid).hide();
        $('#'+sid).show();
    }
}
function sync_home(id,aid,order_time){
    $.get('/answer/sync_home/',{'sync':id, 'answer_id':aid}, function(){
        if (id){
            if(order_time > 0){
                up = "<span class=\"f_9 fl\" >已置顶到我的主页</span>";
            }else{
                up = "<a class=\"set_top fl\" onclick=\"answer_up("+aid+")\">置顶到我的主页</a>";
            }
            $("#sync"+aid).html(up+"<a class=\"open_myhome fl\" onclick=\"sync_home(0,"+aid+","+order_time+")\">取消公开到我的主页</a>");
        }
        else{
            $("#sync"+aid).html("<a class=\"open_myhome fl\" onclick=\"sync_home(1,"+aid+","+order_time+")\">公开到我的主页</a>");
        }
    });
}
function answer_up(aid){
    $.get('/answer/answer_up/',{'answer_id':aid}, function(){
        window.location.reload();
    });
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