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
    <div class="ask_nav clear"> <a target="_blank" href="<?php echo ($urlsite); ?>/other/kf/?mtype=7" class="opinion_fb f_6">对“问问”的意见反馈</a>
      <ul class="fl">
        <li><a href="<?php echo ($urlsite); ?>/question/plaza/" class="" >问问广场</a></li>
        <li><a href="<?php echo ($urlsite); ?>/question/add/" class="">我要问问</a></li>
        <li><a href="<?php echo ($urlsite); ?>/question/sender/" class=" active_nav">问问管理</a></li>
        <li><a href="<?php echo ($urlsite); ?>/wenwen/" class="" >小编专访</a></li>
      </ul>
    </div>
    <div class="ask_main1">
      <div class="question_options"><span class="fl"><a href="<?php echo ($urlsite); ?>/question/sender/" class="fontstyle1">我的提问</a> <a href="<?php echo ($urlsite); ?>/answer/my_answer/?order=0">我的回答</a></span> <span class="fr"><a href="<?php echo ($urlsite); ?>/question/sender/?order=0" class="<?php if(($order) == "1"): ?>f_bl<?php else: ?>f_9<?php endif; ?>">按最新未读回复</a> <a href="<?php echo ($urlsite); ?>/question/sender/?order=1" class="<?php if(($order) == "0"): ?>f_bl<?php else: ?>f_9<?php endif; ?>">按最新提问时间</a></span> </div>
      <script language="javascript" src="<?php echo ($urlstatic2); ?>/js/face.js?gv=148_1"></script>
      <script language="javascript" src="<?php echo ($urlstatic2); ?>/js/pub_face_all.js?gv=148_1"></script>
      <div class="question_list clear">
        <ul>
          <li class="clear">
            <div class="img"><img src="<?php echo ($urlupload); ?>/<?php echo ($GLOBALS['i']['default_pic']); ?>_48x48.jpg" width="48" height="48"/> </div>
            <div class="rightbar"  id="choose_question_id">
			<?php if(empty($list)): ?><div class="no_question">您还没有提过问题,<a href="<?php echo ($urlsite); ?>/question/add/">马上去提问&gt;&gt;</a></div><?php endif; ?>
            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; $agree = round(($vo['agree_count']/($vo['agree_count']+$vo['against_count']))*100); $against = round(($vo['against_count']/($vo['agree_count']+$vo['against_count']))*100); ?>	
              <div class="title clear"  id="li_<?php echo ($vo["id"]); ?>" onmouseout="$('#show_<?php echo ($vo["id"]); ?>').hide();" onmouseover="$('#show_<?php echo ($vo["id"]); ?>').show();">
                <p class="fs_14 word_break"><?php if($vo[is_anonymity] == 1): ?>匿名<?php endif; ?><span class="f_9 word_nowrap">[<?php echo ($vo['type_name']); ?>]</span>&nbsp;<?php echo ($vo["question"]); ?></p>
                <?php if(!empty($vo['photo_url'])): ?><div class="m_t10"> <img id="imgsmall_<?php echo ($vo["id"]); ?>" src="<?php echo ($urlupload); ?>/<?php echo ($vo["photo_url"]); ?>_120x120.jpg" onclick="scaleImg(<?php echo ($vo["id"]); ?>,'<?php echo ($urlupload); ?>/<?php echo ($vo["photo_url"]); ?>_480x480.jpg')" class="mousezoom_tip"/><img id="imgbig_<?php echo ($vo["id"]); ?>" style="display:none;" class="mousezoom_min" onclick="scaleImg(<?php echo ($vo["id"]); ?>,'')"> </div><?php else: ?><div class="no_img"></div><?php endif; ?>
                <div class="m_t10 botinfo clear">
                  <p class="fr" id="show_<?php echo ($vo["id"]); ?>" style="display:none;"><span id="sync<?php echo ($vo["id"]); ?>"><!--<a class="set_top" onclick="question_up(<?php echo ($vo["id"]); ?>)">置顶到我的主页</a><a class="open_myhome" onclick="sync_home(0,<?php echo ($vo["id"]); ?>,0)">取消公开到我的主页</a>--></span><a class="close" title="删除" id="del_<?php echo ($vo["id"]); ?>" onclick="delete_confirm(<?php echo ($vo["id"]); ?>, 'question')"></a></p>
                  <p class="fl clear"> <span class="agree fl"><?php echo ($agree); ?>%</span> <span class="opposition fl"><?php echo ($against); ?>%</span> <a class="current fl" id="all_reply_<?php echo ($vo["id"]); ?>" onclick="load_answer_more(<?php echo ($vo["id"]); ?>,0,0);this.className='current fl';$('#comment_reply_<?php echo ($vo["id"]); ?>').removeClass('current');">全部回复<span id="is_comment_0_<?php echo ($vo["id"]); ?>">(<?php echo ($vo["answer_count"]); ?>)</span></a> <a class="fl" id="comment_reply_<?php echo ($vo["id"]); ?>" onclick="load_answer_more(<?php echo ($vo["id"]); ?>,0,1);this.className='current fl';$('#all_reply_<?php echo ($vo["id"]); ?>').removeClass('current');">只看有内容的<span id="is_comment_1_<?php echo ($vo["id"]); ?>">(<?php echo ($vo["answer_cont_num"]); ?>)</span></a> </p>
                </div>
                <div class="my_reply" id="qreply_<?php echo ($vo["id"]); ?>" style="display:none;">
                  <div class="topbg3" id="topbg_<?php echo ($vo["id"]); ?>"></div>
                  <div class="box clear" >
                    <dl id="answer_<?php echo ($vo["id"]); ?>" a_limit="1" a_filter='2' answer_news="0" last_answer="0">
                    </dl>
                    <div class="more" onclick="load_answer_more(<?php echo ($vo["id"]); ?>,1,'2')" id="load_more_answer_<?php echo ($vo["id"]); ?>"><a>查看更多&gt;&gt;</a></div>
                    <p class="m_t10"><a class="f_bl fr" onclick="$('#qreply_<?php echo ($vo["id"]); ?>').hide(); $('#answer_<?php echo ($vo["id"]); ?>').attr('last_answer', '0');">收起↑</a></p>
                  </div>
                </div>
                <span id="qtime_<?php echo ($vo["id"]); ?>" class="time f_9 fs_12"><?php echo (formattime($vo["add_time"])); ?></span><?php if($vo['new_answer'] != 0): ?><div id ="new_answer_<?php echo ($vo["id"]); ?>" class="new_askper" onclick="load_answer_more(<?php echo ($vo["id"]); ?>,0,0)"><span class="u_number"><a><?php echo ($vo["new_answer"]); ?></a></span><span class="u_numberbg"></span></div><?php endif; ?>
				</div><?php endforeach; endif; else: echo "" ;endif; ?>
			</div>
          </li>
        </ul>
        <div class="turn_page" id="page"> </div>
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
var related = '';
var receiver_uid = '';
var setstar_id = 0;
var setpoor_id = 0;
var star_num = 0;
var star_type = 0;
var js_ping_id = 0;
function delete_iquestion(id) {
    if (id > 0)
    {
        $.ajax({
            type: "POST",
            url: "/index.php?s=/question/delete/",
            data: 'qid_list='+id,
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
function delete_ianswer(id,is_comment,q_id)
{
    if (id > 0)
    {
        $.ajax({
            type: "POST",
            url: "/index.php?s=/answer/delete_receiver/",
            data: 'aid_list='+id,
            success: function(re){ 
                var obj = jQuery.parseJSON(re);
                $("#dl_"+id).remove();
                var num_all = $("#is_comment_0_"+q_id).html();
                num_all = num_all.replace('(', '');
                num_all = num_all.replace(')', '');
                if(num_all > 0){ $("#is_comment_0_"+q_id).html('('+(num_all - 1)+')');}
                if(is_comment == 1){
                    var num_comment = $("#is_comment_1_"+q_id).html();
                    num_comment = num_comment.replace('(', '');
                    num_comment = num_comment.replace(')', '');
                    if(num_comment > 0){ $("#is_comment_1_"+q_id).html('('+(num_comment - 1)+')');}
                }
                Win.dialog({'msg':obj.msg,'type':'info'});
                
            }
        });
    }
    else
    {
        Win.dialog({title:'警告', msg:'请选择要删除的问题', type:'alert'});
        return false;
    }
}
function delete_confirm()
{
    var id = arguments[0];
    if ('answer' == arguments[1])
    {
        is_comment = arguments[2];
        q_id = arguments[3];
        Win.dialog({type:'confirm',msg:'是否删除该回答？',height:100,enter:function(){delete_ianswer(id,is_comment,q_id);},enterName:'确定'});
    }else{
        Win.dialog({type:'confirm',msg:'是否删除该问题？',height:100,enter:function(){delete_iquestion(id);},enterName:'确定'});
    }
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

function load_answer_more(id,first_answer,filter)
{
    var a_limit = parseInt($("#answer_"+id).attr('a_limit'));//上次页数
    var a_filter = $("#answer_"+id).attr('a_filter');//上次状态
    var answer_news = parseInt($("#answer_"+id).attr('answer_news'));
    var last_answer = 0;
    $("#qreply_"+id).show();
    if(filter == 2){
        filter = a_filter;
        last_answer = parseInt($("#answer_"+id).attr('last_answer'));
    }
    if(first_answer == 0)
    {
        if(a_limit > 0)
        {
            if(a_filter == filter)
            {
                $('#qreply_'+id).hide();
                $("#answer_"+id).html('');
                $("#answer_"+id).attr('a_limit', 1);
                $("#answer_"+id).attr('a_filter', '2');
                $("#answer_"+id).attr('last_answer', '0');
                return false;
            }else if(a_filter != filter)
            {
                if(filter == 0)
                {
                    $("#topbg_"+id).attr('class', 'topbg3');
                }else{
                    $("#topbg_"+id).attr('class', 'topbg2');
                }
                $("#answer_"+id).html('');
                $("#answer_"+id).attr('a_filter', filter);
                a_limit = 1;
                $("#load_more_answer_"+id).show();
                $("#new_answer_"+id).hide();
                if(answer_news > 0)
                {
                    $("#answer_"+id).attr('answer_news', 0);
                }
            }
        }
    }
    $.ajax({
        type: "POST",
        url: "/index.php?s=/answer/load_more_answer",
        data: 'limit='+a_limit+'&question_id='+id+'&filter='+filter+'&first_answer='+first_answer+'&answer_news='+answer_news+'&last_answer='+last_answer,
        success: function(re){ 
            var obj = jQuery.parseJSON(re);
            if(obj.errno == 200) {
                $("#answer_"+id).append(obj.more);
                a_limit = a_limit+1;
                $("#answer_"+id).attr('a_limit', a_limit);
               
            }else{
                Win.dialog({'msg':obj.msg,'type':'info'});
            }
            if(obj.hide_more == 1) {
                $("#load_more_answer_"+id).hide();
            }
            if(obj.last_answer > 0) {
                $("#answer_"+id).attr('last_answer', obj.last_answer);
            }
            if(obj.answer_news >0){
                minus_unread('answer', obj.answer_news, 0);
            }
        }
    });
}
function ping_star(div_id){
    if (star_num < 1) return false;
    var q_uid,q_id,a_id,a_uid,answer,answer_id,content,txt_starnum;
    answer = $("#"+div_id).attr('data').split('|');
    q_uid = answer[0];
    q_id = answer[1];
    a_id = answer[2];
    a_uid = answer[3];
    answer_id = answer[4];
    txt_starnum = {'1':'一', '2':'二', '3':'三'};
    if(star_type == 1){
        content = '给你的答案评了'+txt_starnum[star_num]+'个差评';
    }else{
        content = '给你的答案评了'+txt_starnum[star_num]+'个好评';
    }
    $.post('/index.php?s=/answer/answer_star/', {'q_uid':q_uid, 'q_id':q_id, 'a_id':a_id, 'a_uid':a_uid,'star_num':star_num,'type':star_type}, function(data){
        if (data == 1) {
            $('#set_'+a_id).html('<a class="sp_star bg_postion'+star_num+'"></a>');
            if(star_type == 1){
                $('#tippoor_'+a_id+'_'+a_uid).html('差评成功：<span class="sp_defecate bg_postion'+star_num+'"></span>');
                $('#tip_'+a_id+'_'+a_uid).html('给他好评：<span class="sp_star bg_postion"></span>');
            }else{
                $('#tippoor_'+a_id+'_'+a_uid).html('给他差评：<span class="sp_defecate bg_postion"></span>');
                $('#tip_'+a_id+'_'+a_uid).html('好评成功：<span class="sp_star bg_postion'+star_num+'"></span>');
            }
                
        }
        if(data == 5){
            Win.dialog({type:'info',width:460,msg:'你已经给过评价'});
            return false;
        }
    });
}
function DisplayCoord(event,type)   
{  
    var pageX = event.clientX;
    if(type == 1){
        var pos = $("#setpoor_"+setpoor_id).offset();
    }else{
        var pos = $("#setstar_"+setstar_id).offset();
    }
   
    var star_pos = pageX - pos.left;
    if (star_pos < 18) star_num = 1;
    if (star_pos > 18 && star_pos < 36) star_num = 2; 
    if (star_pos > 36) star_num = 3;
    if(type == 1){
        $('#setpoor_'+setpoor_id).attr('class', 'sp_defecate  bg_postion'+star_num);
        star_type = 1;
    }else{
        $('#setstar_'+setstar_id).attr('class', 'sp_star bg_postion'+star_num);
        star_type = 0
    }
    return star_num;
}

function submit_comment(pay_card)
{
    if(IM.gFlash) {
        var comment_content = IM.chatFilter($('#comment_content').html());
    }
    else {
         var comment_content = $.trim($('#comment_content').val());
    }
    if(typeof myuserinfo != "object" || !myuserinfo.uid){
         show_login_form();
         return false ;
    }

    if(myuserinfo.uid == receiver_uid){
        Win.dialog({type:'info',msg:'不能给自己评论！'});
        return false;
    }

    if(myuserinfo.profile_completed < 0.5 && myuserinfo.login_times > 6){
        var msg_code = sysmessage_addprofile('reply',(myuserinfo.sex==1)?2:1) ;
        Win.dialog({ type:'info',width:460,msg:msg_code});
        return false ;
    }

    if (comment_content.length<1) {
        Win.dialog({type:'info',msg:'回复内容不能为空！'});
        return;
    }

    if(related<0)
    {
        Win.dialog({type:'info',msg:'评论的回答不能为空！'});
        return ;
    }
    
    $("#comment_button").attr("disabled",true);
    $.ajax({
       type: "POST",
       url: "/index.php?s=/msg/send/",
       data: 'receiver_uid='+receiver_uid+'&content='+encodeURIComponent(comment_content)+'&type=10&related='+related+'&pay_card='+pay_card+'&category=1&from=my_question',
       success: function comment_success(re)
                {
                    $("#comment_button").attr("disabled",false);
                    var result_obj = jQuery.parseJSON(re);
                    switch(result_obj.stat)
                    {
                        case 0: //
                            Win.dialog({type:'info',msg:'回复成功'});
                           $('.comment').empty();
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

function sync_home(id,qid,order_time){
    $.get('/index.php?s=/question/sync_home/',{'sync':id, 'question_id':qid}, function(){
        if (id){
            if(order_time > 0){
                up = "<span class=\"f_9 fl\" >已置顶到我的主页</span>";
            }else{
                up = "<a class=\"set_top\" onclick=\"question_up("+qid+")\">置顶到我的主页</a>";
            }
            $("#sync"+qid).html(up+"<a class=\"open_myhome\" onclick=\"sync_home(0,"+qid+","+order_time+")\">取消公开到我的主页</a>");
        }
        else{
            $("#sync"+qid).html("<a class=\"open_myhome\" onclick=\"sync_home(1,"+qid+","+order_time+")\">公开到我的主页</a>");
        }
    });
}

function question_up(qid){
    $.get('/index.php?s=/question/question_up/',{'question_id':qid}, function(){
        window.location.reload();
        //$("#sync"+qid).html("<a class=\"set_top\" >已置顶到我的主页</a><a class=\"open_myhome\" onclick=\"sync_home(0,"+qid+")\">取消公开到我的主页</a>");
    });
}

var show_reply_comment = false;
function reply(answer_id,r_uid,id){
	$.post("/index.php?s=/msg/check/", { friend:r_uid}, function (data) {
		if(data.stat == 5) {
			var msg_info = redbeans(data.nickname,data.pay_card);
			Win.dialog({'msg':msg_info,'height':400,'width':580,'pay_card':data.pay_card,'enter':function(data){
				$.post("/msg/check/", { friend:r_uid, pay_card:data.pay_card}, function (data) {
					if(data.stat) {
						Win.dialog({'msg':data.error, 'type':'alert'});
					}
					else reply_div(answer_id,r_uid,id);
				}, 'json');
			}});
		}
		else if(data.stat) {
			Win.dialog({'msg':data.error, 'type':'alert'});
		}
		else reply_div(answer_id,r_uid,id);
	}, 'json');
}
function reply_div(answer_id,r_uid,id){
    if(IM.gFlash) {
        var edit = '<div contenteditable="true" onfocus="setRange(this)" onkeyup="setRange(this)" onmouseup="setRange(this)" ondrop="return false" ondragover="return false" onKeyDown="if(event.keyCode==13 && event.ctrlKey) submit_comment(0)" onClick="" name="comment_content" id="comment_content" type="text"  maxlength="200" style="padding:3px;overflow-x:hidden;overflow-y:scroll;background:#fff;border-color:#7E7E7E #CFCFCF #CFCFCF #7E7E7E;border-style:solid;border-width:1px;height:60px;box-shadow:2px 2px 2px rgba(0, 0, 0, 0.1) inset;"></div>';
    }
    else {
        var edit = '<textarea onkeydown="if(event.keyCode==13 &amp;&amp; event.ctrlKey) submit_comment(0)" name="comment_content" id="comment_content" class="input1" maxlength="200" style="overflow-y:hidden;" onkeyup="setRange(this)" onmouseup="setRange(this)"></textarea>';
    }
    var html='<p>'+edit+'</p><p class="clear m_t10"><span class="fl"><a onClick="submit_comment(0)"  id="comment_button" class="btn1">发送回复</a></span> <span class="fl"><a onClick="face51New.show(this,\'comment_content\',\'_div\');" class="vface" href="javascript:;"><img src="http://pic.jjdd.com/v1/i/pub/ico_ce.gif"></a></span> <span class="fl list"> (评论只有对方本人可见)</span></p>';
    //
    var comment_obj = '#comment_'+id;
    related = answer_id;
    $('.comment').empty();
    receiver_uid = r_uid;
    related = answer_id;
    $(comment_obj).html(html);
    if($.browser.msie&&($.browser.version == "8.0") && !show_reply_comment)
    {
        $(comment_obj).html(html);
    }
    show_reply_comment = true;		
    $(function(){
       var _area=$('#comment_content'); 
       var _max=_area.attr('maxlength'); 
        _area.bind('keyup change',function(){
            if(IM.gFlash) {
                _val=$(this).html(); 
                _cur=_val.length; 
                if(_cur>_max){
                    $(this).html(_val.substring(0,_max)); 
                } 
            }
            else { 
                _val=$(this).val(); 
                _cur=_val.length; 
                if(_cur>_max){
                    $(this).val(_val.substring(0,_max)); 
                } 
            }
        });
    }); 
    $("#comment_content").focus();

//	var text = document.getElementById("comment_content"); //用户看到的文本框
//	var shadow = document.getElementById("shadow"); //隐藏的文本框
//	text.oninput = text.onpropertychange = onchange;
//	function onchange() {
//		shadow.value = text.value;
//		setHeight();
//		setTimeout(setHeight, 0); //针对IE 6/7/8的延迟, 否则有时会有一个字符的出入
//		function setHeight() { 
//			if(shadow.scrollHeight == 0)
//			{
//				height = 20;
//			}else{
//				height = ((shadow.scrollHeight/16)*18);
//			}
//			text.style.height = height + "px"; }
//		}
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