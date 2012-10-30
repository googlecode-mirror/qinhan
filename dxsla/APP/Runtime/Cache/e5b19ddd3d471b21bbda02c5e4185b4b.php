<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>大学生恋爱网 一个大学生认识新同学、交友的网站</title>
<meta content="大学生恋爱网，恋爱网，大学生恋爱，大学生谈恋爱，大学生爱情，大学生交友，校园交友，校园恋爱，高校交友，大学生恋爱观，大学生爱情观，大学生恋爱心理，大学生情书，大学生爱情故事" name="keywords" />
<meta content="大学生恋爱网，大学生交友的网站，大学校园里的真实照片的匿名交友，除了上QQ空间你还可以玩的网站。" name="description" />
<link href="<?php echo ($urlstatic2); ?>/css/head_global_main_ask.css<?php echo ($urltail); ?>" rel="stylesheet" type="text/css" />
<script language="javascript" src="<?php echo ($urlstatic2); ?>/js/global_jquery_hello_dialog_chat.js<?php echo ($urltail); ?>"></script>

<script language="javascript" src="<?php echo ($urlstatic2); ?>/js/provinces_zh-CN.js"></script>
<script language="javascript" src="<?php echo ($urlstatic2); ?>/js/skill.js"></script>
</head>
<body>
<?php $nav = 1; ?>
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

  <!--中间部分-->
  <div class="container_cwrap clear">
    <!--中间左边部分开始-->
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

	<div style="background:url(<?php echo ($urlstatic); ?>/img/service_boxbg.png) no-repeat; height:50px; padding:8px;"><img style="vertical-align:middle;" src="<?php echo ($urlstatic); ?>/img/online_service.png" />&nbsp;<a style="text-decoration:none; color:#444;" href="javascript:;" onclick="IM.open(20853);">在线提意见或者建议</a></div>  
    </div>
    <!--中间左边部分结束-->
    <!--中间中间部分开始-->
    <div class="fm_c_560">
      <!--搜索部分开始-->
      <div class="index_area_box clear">
        <h1>凭感觉给下面的人打分！&nbsp; </h1>
        <script>
		function simpleSearch(){
			Win.dialog({type:'info',msg:'测试期，地区选择功能暂未开放。',enterName:'确 定',enter:function(){ $("#ping_prov").val("");Win.close();},cancel:function(){ $("#ping_prov").val(""); } });
			return false;
		}
        </script>
      </div>
      <!--搜索部结束-->
      <!--打分部分开始-->
      <div class="rating_box clear">
        <div class="rating_box_top2 clear">
          <div class="fl" style="height:72px; overflow:hidden;">
            <object width="320" height="72" align="middle" id="markObjIE" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000">
              <param value="always" name="allowScriptAccess">
              <param value="transparent" name="wmode">
              <param value="<?php echo ($urlstatic2); ?>/flash/norzPing.swf" name="movie">
              <param value="high" name="quality">
              <embed  id="markObj" width="320" height="72" align="middle" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" allowscriptaccess="sameDomain" swliveconnect="true" quality="high" wmode="transparent" src="<?php echo ($urlstatic2); ?>/flash/norzPing.swf">
            </object>
          </div>
          <a class="sound"> <img src="<?php echo ($urlstatic); ?>/img/ico_sound.png" onclick="set_sound_status('off');" id="sound_on" class="ico" style=""/> <img src="<?php echo ($urlstatic); ?>/img/ico_nosound.png" onclick="set_sound_status('on');" id="sound_off" class="ico" style="display:none"/> </a> </div>
        <div class="rating_box_l">
          <p class="list"><img  src="<?php echo ($urlstatic); ?>/img/ico_img2.gif" /></p>
          <ul id="ping_list_1" >
		  	<?php if(is_array($userlist)): $i = 0; $__LIST__ = array_slice($userlist,1,4,true);if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$u): $mod = ($i % 2 );++$i;?><li id="in_<?php echo ($u['uid']); ?>"<?php if(($i) == "1"): ?>class="list"<?php endif; ?>><a href="<?php echo ($urldomain); ?>/<?php echo ($u['uid']); ?>" target="_blank"><img src="<?php echo ($urlupload); echo ($u['default_pic']); ?>_72x72.jpg" border="0" alt="" /></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
          </ul>
        </div>
        <div class="rating_box_r">
          <div class="box_t clear">
            <div class="box_t_l">
              <div id="current_ping_div">
                <p class="list"><a href="<?php echo ($urldomain); ?>/<?php echo ($userlist[0]['uid']); ?>" target="_blank" title="点击查看更多资料，照片"><img id="current_user_img" src="<?php echo ($urlupload); echo ($userlist[0]['default_pic']); ?>_240x240.jpg" /></a></p>
                <p class="list2"><a href="<?php echo ($urldomain); ?>/<?php echo ($userlist[0]['uid']); ?>" target="_blank"><?php echo ($userlist[0]['username']); ?></a> <span class="f_yelo fs_12">(<?php echo ($userlist[0]['photonum']); ?>照片)</span>,<?php echo (getage($$userlist[0]['sex'])); ?>岁,<?php echo ($$userlist[0]['college']); ?> <a href="<?php echo ($urldomain); ?>/<?php echo ($userlist[0]['uid']); ?>" target="_blank">详细资料</a> </p>
                <p class="clear"><?php echo (do_things($userlist[0]['want_content'],$userlist[0]['sex'])); ?></p>
              </div>
            </div>
            <div class="box_t_r">
              <div class="" id="play_flash">
                <object width="0px" height="0px" align="middle" id="perfectie"  name="perfectie" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000">
                  <param value="always" name="allowScriptAccess">
                  <param value="transparent" name="wmode">
                  <param value="<?php echo ($urlstatic2); ?>/flash/perfect.swf" name="movie">
                  <param value="high" name="quality">
                  <embed src="<?php echo ($urlstatic2); ?>/flash/perfect.swf" id="perfectff" name="perfectff" width="0px" height="0px" align="middle" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" allowscriptaccess="always"  quality="high" wmode="transparent">
                </object>
              </div>
              <div class="box_t_r mt40" id="last_ping_div" >
                <p class="img_b"><img src="<?php echo ($urlstatic); ?>/img/img_b.png" border="0" alt=""/></p>
                <p class="mt30"><span class="fb_14">您的评分:</span> <span class="fs_13 f_6">暂无</span></p>
                <div class="bg">
                  <p class="fs_14 l76 f_6">得分待评</p>
                </div>
              </div>
            </div>
          </div>
          <div class="box_b clear">
            <div class="box_b_l">
              <ul id="ping_list_2" class="clear">
			    <?php if(is_array($userlist)): $i = 0; $__LIST__ = array_slice($userlist,5,7,true);if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$u): $mod = ($i % 2 );++$i;?><li id="in_<?php echo ($u['uid']); ?>"><a href="<?php echo ($urldomain); ?>/<?php echo ($u['uid']); ?>" target="_blank"><img src="<?php echo ($urlupload); echo ($u['default_pic']); ?>_72x72.jpg" border="0" alt="" /></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
              </ul>
            </div>
            <p class="box_b_r">&nbsp;</p>
          </div>
        </div>
      </div>
      <!--打分部分结束-->
      <!--谁来看过我开始-->
      <div class="index_wacth_box">
        <div class="tip_nav2">
          <p class="text_r"><a href="<?php echo ($urlsite); ?>/diary/" class="f_3">今天有什么想说的？写两句</a></p>
          <ul class="clear m_t10">
            <li class="clear"><a id="tag_attention" onclick="view_tag_content('attention');" >动态</a></li>
            <li class="clear"><a id="tag_visiti" onclick="view_tag_content('visiti');" >来访者</a></li>
            <li class="clear"><a id="tag_ivisit" onclick="view_tag_content('ivisit');" >我去看过谁</a></li>
            <li class="clear"><a id="tag_ifav" onclick="view_tag_content('ifav');" >我收藏的人</a></li>
            <li class="clear"><a id="tag_favi" onclick="view_tag_content('favi');" >谁收藏了我</a></li>
          </ul>
        </div>
        <div class="myfrend_item_top" id="attention_check"><a onclick="view_tag_content('attention');" id="my_attention" class="fb_13 f_yelo unline">我关注的动态</a> | <a onclick="view_tag_content('attention_more');" id="more_attention">更多人在干嘛</a><font class="f_r1" id="reload_more_attention" style="display:inline-block;">（新）</font></div>
        <div class="myfrend_list" id="tag_div"> </div>
      </div>
      <!--谁来看过我结束-->
    </div>
    <!--中间中间部分结束-->
    <div class="fm_r160">
      <!--最新消息开始-->
      <ul id="notice_div" style="position:relative;">
      </ul>
      <!--最新消息结束-->
      <div class="methinks_box">
        <div class="methinks_box_c" onclick="open_methinks_box()">
          <form id="formWant" name="formWant" onSubmit="return save_want_things();">
            <p class="fb_12">我想和一个<?php echo (ui_sex($GLOBALS['i']['sex'],3)); ?>生</p>
            <p class="m_t5 clear"> <span class="fl">
              <input id="want_content" name="want_content" type="text" class="input_1" maxlength="28" value="<?php echo ($GLOBALS['i']['want_content']); ?>"/>
              </span> <span class="handle fl"></span> </p>
            <div class="methinks_box2" id="want_box">
              <ul id="want_things_list">
                <li><a onclick="$('#want_content').val('真真切切的谈场恋爱');">真真切切的谈场恋爱</a></li>
                <li><a onclick="$('#want_content').val('将爱情进行到底');">将爱情进行到底</a></li>
                <li><a onclick="$('#want_content').val('一起寻找我们的未来');">一起寻找我们的未来</a></li>
                <li><a onclick="$('#want_content').val('拥有维尼夫妇一样的幸福');">拥有维尼夫妇一样的幸福</a></li>
                <li><a onclick="$('#want_content').val('一起无话不谈');">一起无话不谈</a></li>
              </ul>
              <div class="refresh clear">
                <input id="want_item_page" type="hidden" value="1" />
                <!--<p><a onclick="refresh_want_things();" class="fl fb_13 f_0">更多选项</a></p>
                <p class="refresh_ico fl"></p>-->
              </div>
              <div class="line1"></div>
              <div class="m_t20">
                <p>
                  <input id="edit_want_opt_y" type="submit" class="btn1" value="保 存" />
                </p>
                <p id="want_opt_msg" class="f_r1" style="display:none"></p>
              </div>
            </div>
          </form>
        </div>
        <div class="methinks_box_b">
        <script>
		function control_methinks_box(){
			if($('#want_box').css('display')=='none'){
				$('#want_box').css('display','');
				$('#want_box_button').html('<a onclick="control_methinks_box()">收起 <img src="<?php echo $urlstatic ?>/img/methinks_box_up.png?gv=87_1" alt="" class="ico" /></a>');
			}else{
				$('#want_box').css('display','none');
				$('#want_box_button').html('<a onclick="control_methinks_box()">展开更多 <img src="<?php echo $urlstatic ?>/img/methinks_box_down.png?gv=87_1" alt="" class="ico" /></a>');
			}
		}

		function open_methinks_box(){
			if($('#want_box').css('display')=='none'){
				$('#want_box').css('display','');
				$('#want_box_button').html('<a onclick="control_methinks_box()">收起 <img src="<?php echo $urlstatic ?>/img/methinks_box_up.png?gv=87_1" alt="" class="ico" /></a>');
			}
		}
		</script>
        </div>
      </div>
	  <div class="methinks_box m_t20">
	  <div class="methinks_box_c">
	  <div class="vip_service_tit clear" style="width:138px; _margin-right:3px; height:26px;"><img style="float:left;" src="<?php echo ($urlstatic); ?>/img/share_to_qq.png" border="0" /><p class="fb_12 f_r" style="float:left; line-height:26px;">分享网站到QQ空间</p>
	  </div>
	  <ul class="vip_service_cont" style="line-height:18px; font-size:12px; clear:both;">
      <li class="f_6 m_t5">分享一次将获得0.5颗红豆，每天限分享1次。</li>
	  <li class="f_6 m_t5">方法1: 分享自己的照片得分，<a target="_blank" href="<?php echo ($urlsite); ?>/usergroup/share_score/">现在就去分享</a></li>
	  <li class="f_6 m_t5">方法2: 分享网站链接</li>
	  <li class="f_6 m_t5" style="text-align:center;"><a class="btn1" href="javascript:;" onclick="share_to_qq()">分享网站</a></li>
      </ul>
	  </div>
	  <div class="methinks_box_b"></div>
	  </div>
	  
<script type="text/javascript">
function share_to_qq() {
	if(myuserinfo.qq_api != 1) {
        var msg_code = '<div class="pop_rader fb_14"><p>你不是通过QQ账号登录本网站，不能分享到QQ空间</p>'
                        +'<p>可以点击<a href="<?php echo $urlsite; ?>/user/rsync/" target="_blank">设置QQ账号同步</a>后再分享。</p></div>';
        Win.dialog({width:500,height:150,msg:msg_code,type:'info'});
        return false;
	}
	var html='<div class="photo_desc" style="padding-top:20px;"><h3>分享网站到QQ空间</h3>'
			+'<textarea id="_js_content" class="input_1" name="content" maxlength="166">推荐一个现在很火的网站——大学生恋爱网</textarea>'
			+'<div class="f_6">分享到QQ空间一次将获得0.5颗红豆，每天限分享1次</div></div>'
			+'<p style="padding:30px;padding-left:190px;" class="bot_btn"><a class="btn1" id="_js_submit">立即分享</a> 或 <a href="javascript:void(0);" onclick="Win.close();">取消</a></p>';
	Win.dialog({width:500,msg:html});
	$('#_js_submit').click(function(){
		var submit_content=$('#_js_content').val();
		if(submit_content.length>166){
			alert("最多输入166个字符");
			return false;
		}
		$.ajax({
		   type: "POST",
		   url: "/index.php?s=/user/share_to_qq/",
		   data: 'content='+encodeURIComponent(submit_content),
		   success: function show_desc_result(re)
		   {
				return true;
		   }
		});
		Win.dialog({type:'info',msg:'分享成功！'});
	});
}
</script>	  
    </div>
  </div>
  <!--中间结束-->
  <object width="0" height="0" align="middle" id="soundObjIE" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000">
    <param value="always" name="allowScriptAccess">
    <param value="<?php echo ($urlstatic2); ?>/flash/sound.swf?v=2" name="movie">
    <param value="high" name="quality">
    <embed src="<?php echo ($urlstatic2); ?>/flash/sound.swf?v=2" id="soundObj" width="0" height="0" align="middle" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" allowscriptaccess="always"  quality="high" >
  </object>
  <div class="tip2" id="queue_msg" style="display:none;">
    <div class="tip2_box">
      <div class="tip2_t">
        <div class="tip2_c">
          <div class="cont">
            <p>请先依次给前面的照片评分，评完后<br/>
              才可以查看详细资料。 <a onclick="hide_queue_msg();">关闭</a> </p>
          </div>
          <div class="tip2_b"></div>
        </div>
      </div>
      <div class="tip2_r"></div>
      <div class="tip2_l"></div>
    </div>
  </div>
  <div style="display:none;"> <img id="preload_img" width="0" height="0" src="<?php echo ($urlupload); echo ($userlist[1]['default_pic']); ?>_240x240.jpg" /> </div>
  <script>
function refresh_want_things()
{
    var page = $("#want_item_page").val() ;
    $.ajax({
       type: "POST",
       url: "/index.php?s=home/get_want_things/",
       data: "page="+page,
       success: function order_result(re)
       {
            var obj = jQuery.parseJSON(re);
            var code = '';
            for (key in obj['rs'])   {
               code += "<li><a onclick=\"$('#want_content').val('"+obj['rs'][key]+"');\" class=\"dashed\">"+obj['rs'][key]+"</a></li>";
            }
            $("#want_things_list").html(code);
            $("#want_item_page").val(''+obj['page']+'') ;
       }
    });
}

function save_want_things(){
    var age_min = $("#want_age_begin").val();
    var age_max = $("#want_age_end").val();
    var want_c = $.trim($("#want_content").val()) ;
    if($("#want_age_end").val() < $("#want_age_begin").val()){
        age_min = $("#want_age_end").val();
        age_max = $("#want_age_begin").val();
    }

    if(want_c == '')
    {
        var msg_code = '请填写我想...！' ;
        Win.dialog({type:'info',msg:msg_code});
        return false ;
    }
    if(want_c.length < 2 || want_c.length > 20)
    {
        var msg_code = '我想内容不能少于2个字，多于20个字！' ;
        Win.dialog({type:'info',msg:msg_code});
        return false ;
    }

    $('#edit_want_opt_y').attr("disabled",true);
    $('#edit_want_opt_y').after('<span id="edit_loading"><br/><img src="<?php echo $urlstatic ?>/img/loader.gif" alt="loading..." class="ico" /> 数据处理中...</span>');

    $.ajax({
       type: "POST",
       url: "/index.php?s=/user/edit_want",
       data: '&age_min='+age_min+'&age_max='+age_max+'&do_things='+$("#want_content").val(),
       success: function order_result(re){
            switch(re){
                case '-2' :
                    var msg_code = '请填写我想...！' ;
                    Win.dialog({type:'info',msg:msg_code});
                break ;
                case '0' :
                    $("#want_opt_msg").html('保存成功！');
                    $("#want_opt_msg").css('display','');
                    $('#edit_want_opt_y').attr('disabled',false);
                break ;
                case '1':
                    $("#want_opt_msg").html('保存成功！');
                    $("#want_opt_msg").css('display','');
                    $('#edit_want_opt_y').attr('disabled',false);
                break;
                case '2':
                    $("#want_opt_msg").html('保存成功！');
                    $("#want_opt_msg").css('display','');
                    $('#edit_want_opt_y').attr('disabled',false);
                break;
                case '3':
                    $("#want_opt_msg").html('保存成功！');
                    $("#want_opt_msg").css('display','');
                    $('#edit_want_opt_y').attr('disabled',false);
                break;
                case '4':
                    var msg_code = '您填写的信息有敏感字！' ;
                    Win.dialog({type:'info',msg:msg_code});			
                    $('#want_content').val('');
                break;
                case '5':
                    var msg_code = '您填写的信息有敏感字！' ;
                    Win.dialog({type:'info',msg:msg_code});			
                    $('#want_content').val('');
                break;
                case '6':
                    var msg_code = '您填写的信息有敏感字！' ;
                    Win.dialog({type:'info',msg:msg_code});
                    $('#want_content').val('');
                break;
                default:
                    var msg_code = '数据出错，请<a href="javascript:self.location.href=self.location.href">刷新页面</a>重试！' ;
                    Win.dialog({type:'info',msg:msg_code});
                break ;
            }
            $('#edit_loading').remove();
       }
    });
    return false;
}

function view_tag_content(action)
{
    $("#tag_ivisit").removeClass();
    $("#tag_visiti").removeClass();
    $("#tag_ifav").removeClass();
    $("#tag_favi").removeClass();
    $("#tag_attention").removeClass();
    $("#my_attention").removeClass();
    $("#more_attention").removeClass();
    switch(action)
    {
        case 'ivisit':$("#tag_ivisit").addClass('current');$("#attention_check").hide(); break;
        case 'visiti':$("#tag_visiti").addClass('current');$("#attention_check").hide();break;
        case 'ifav':$("#tag_ifav").addClass('current');$("#attention_check").hide();break;
        case 'favi':$("#tag_favi").addClass('current');$("#attention_check").hide();break;
        case 'attention':$("#tag_attention").addClass('current');$("#attention_check").show();$("#my_attention").addClass('fb_13 f_yelo unline');break;
        case 'attention_more':$("#tag_attention").addClass('current');$("#attention_check").show();$("#more_attention").addClass('fb_13 f_yelo unline');$("#reload_more_attention").hide();break;
    }
    
    $.ajax({
       type: "GET",
       url: "/index.php?s=/main/"+action+"/",
       success: function view_result(re){
            if(re!=''){
                $("#tag_div").html(re);
            }else{
                switch(action){
                    case 'ivisit':
                        $("#tag_div").html('<p style="padding:20px 0 60px 20px;">您还没有去看过别人。<p/>');
                        break;
                    case 'visiti':
                        var msg_code = '<p style="padding:30px 0 0 20px;">您暂未被展示，所以没有来访者。为何不被展示？因为您还没有上传照片！<p/>'
                                + '<p style="padding:5px 0 60px 20px;">在jjdd.com，大家都是上传自己的真实照片，请<a href="/photo/up_form/" class="f_bl">上传您本人的真实照片</a>。<p/>' ;
                        $("#tag_div").html(msg_code);
                        break;
                    case 'ifav':
                        $("#tag_div").html('<p style="padding:30px 0 60px 20px;">您暂未收藏任何人。<p/>');
                        break;
                    case 'favi':
                        $("#tag_div").html('<p style="padding:30px 0 60px 20px;">暂未有任何人收藏过您。<p/>');
                        break;
                    case 'attention':
                        $("#tag_div").html('<p style="padding:30px 0 60px 20px;">您没有收藏任何人，或者您收藏的人暂无新动态。赶快去收藏感兴趣的人或去看看<a href="/attention/more" class="f_bl">更多人在干嘛吧。<p/>');
                        break;
                }
            }
        }
    });
}

$(document).ready(function(){
  view_tag_content('attention_more');
  //get_new_notice();
  //var time_notice = setInterval("get_new_notice()",20000);
});

</script>
  <script>
var current_uid='<?php echo ($userlist[0]['uid']); ?>';
var current_sex=<?php echo ($userlist[0]['sex']); ?>;
var current_key='123456';
var wait_time;
if(1)
{
    wait_time = 1000;
}
else
{
    wait_time = 2000;
}
function setMarkFinish() {
    try{
        if($.browser.msie)
        {
            $("#markObjIE")[0].setMarkFinish();
        }
        else
        {
            $("#markObj")[0].setMarkFinish();
        }
    }
    catch(e)
    {
        alert(e);
    }
    return true;
}

function show_reg_tips()
{
    var msg_code = '<a href="/reg/">注册</a> 后才能继续打分！'
    Win.dialog({type:'info',msg:msg_code,enterName:'立即注册',enter:function(){self.location.href='/reg/';}});
    return;
}

function show_face_tips()
{
    if(1 == '2')
    {
        var msg_code = '<a href="/photo/up_form/">上传一张您的照片</a>，才能继续打分！<br/><br/>并且马上会有网友给您打分！'
    }
    else
    {
        var msg_code = '<a href="/photo/up_form/">上传一张您的照片</a>，才能继续打分！'
    }

    Win.dialog({type:'info',msg:msg_code,enterName:'上传照片',enter:function(){self.location.href='/photo/up_form/';}});
    return;
}

var is_vip = 0;
var marking = false;

function mark(score)
{	
    if((score<5 || score==10) && myuserinfo.group_type < 2)
    {
        var msg_code = '<div class="pop_rader fb_14"><p>5分以下，以及10分，都属特殊分数，仅向<span class="f_green">正式会员</span></p>'
                        +'<p>的用户开放！<a href="<?php echo $urlsite; ?>/usergroup/" target="_blank">我要成为正式会员</a></p></div>';
        Win.dialog({width:500,height:150,msg:msg_code,type:'info',enter:setMarkFinish,cancel:setMarkFinish});
        return false;
    }
	//hide_mark_btn(score);
    if(marking) return false;
    marking = true;
    
    $.ajax({
       type: "POST",
       url: "/index.php?s=/hot/user_ping",
       timeout: 15000,
       data: 'uid='+current_uid+'&score='+score+'&sex='+current_sex+'&key='+current_key+'&',
       success: function order_result(re)
       {
            if(re ==-1)
            {
                location.reload();
            }
            play_sound();
            //show_mark_btn(score);
            //setTimeout("show_mark_btn("+score+")",wait_time);
            //if(typeof re != "object") return;
            var result = jQuery.parseJSON(re);
            $("#current_ping_div").css('display','none');
            $("#last_ping_div").css("display","none");
            show_ping_queue(result.next_ping_user,result.end_ping_user);
            show_current_ping(result.next_ping_user,result.last_ping_user,result.preload_img_url);
            current_uid = result.next_ping_user.uid;
            current_sex = result.next_ping_user.sex;
            current_key = result.next_ping_user.key;
       },
       error: function(XMLHttpRequest, textStatus, errorThrown) 
       {
            alert('由于您的网速太慢，请求超时，请刷新页面后重试！');
            self.location.href = self.location.href;
       }
    });
}



function show_ping_queue(next_ping_user,end_ping_user)
{
    if($("#in_"+next_ping_user.uid).length >0)
    {
        //$("#queue_msg").hide('slow');
        $("#in_"+next_ping_user.uid).hide('slow',function(){show_ping_queue_finish(next_ping_user,end_ping_user)});
    }
    else
    {
        //self.location.href = self.location.href;
		//alert(next_ping_user.uid);
    }
}

function show_ping_queue_finish(next_ping_user,end_ping_user)
{

    $("#in_"+next_ping_user.uid).remove();
    $("#ping_list_1 li:first-child").removeClass();
    $("#ping_list_1 li:first-child").addClass("list");
    $("#ping_list_2 li:first-child").removeClass();
    $("#ping_list_2 li:first-child").addClass("list2");
    $("#ping_list_2 li:first-child").clone().appendTo($("#ping_list_1"));
    $("#ping_list_2 li:first-child").hide('slow',function(){ $("#ping_list_2 li:first-child").remove();marking = false;setMarkFinish(); });
    if(end_ping_user!='')
    {
                var code = '<li id="in_'+end_ping_user.uid+'"><a href="<?php echo $urldomain; ?>/'+end_ping_user.uid+'" target="_blank"><img src="'+end_ping_user.face_url+'" border="0" alt="" /></a></li>';
        $("#ping_list_2").append(code);
    }
}

function show_current_ping(next_ping_user,last_ping_user,preload_img_url)
{
    var photo_code = '';
    if(next_ping_user.uid >0){	
        if(next_ping_user.photo_count>0){
            photo_code = ' <span class="f_yelo fs_12">('+next_ping_user.photo_count+'照片)</span>';
        }
        var code = '<p class="list"><a href="<?php echo $urldomain; ?>/'+next_ping_user.uid+'" target="_blank" title="点击查看更多资料，照片"><img id="current_user_img" src="'+next_ping_user.face_url+'" /></a></p>'
                    +'<p class="list2"><a href="<?php echo $urldomain; ?>/'+next_ping_user.uid+'" target="_blank">'+next_ping_user.nickname+'</a>'+photo_code+','+next_ping_user.age_show+next_ping_user.location_prov+next_ping_user.location_city+' <a href="<?php echo $urldomain; ?>/'+next_ping_user.uid+'" target="_blank">详细资料</a></p>'
                    +'<p class="clear">'+next_ping_user.do_things+'</p>';

        $("#current_ping_div").css('display','none');
        $("#current_ping_div").css('visibility','visible');
        $("#current_ping_div").html(code);
        $("#current_ping_div").show('fast',function(){show_last_ping(last_ping_user,preload_img_url);});
    }
}

function set_sound_status(sound_status)
{
    if(sound_status=='on')
    {
        Cookies.set('sound_off',0);
        $('#sound_on').show();
        $('#sound_off').hide();
    }
    else
    {
        Cookies.set('sound_off',1);
        $('#sound_off').show();
        $('#sound_on').hide();
    }
}


function play_sound()
{
    if(Cookies.get('sound_off')==1) return false;
    try{
        if($.browser.msie)
        {
            $("#soundObjIE")[0].play_sound();
        }
        else
        {
            $("#soundObj")[0].play_sound();
        }
    }
    catch(e)
    {
        //alert(e);
    }
    return true;
}

function show_last_ping(last_ping_user,preload_img_url)
{
    var photo_code = '';
    if(last_ping_user.photo_count>0)
    {
        photo_code = ' <span class="f_yelo fs_12">('+last_ping_user.photo_count+'照片)</span>';
    }
    var code = ''
              +'<p class="fb_13">您给'+last_ping_user.ta+'打：<span class="f_r">'+last_ping_user.ping_score+'分</span></p>'
              +'<p class="fb_13">'+last_ping_user.ta+'的平均分：<span class="f_r">'+last_ping_user.score+'分</span></p>'
              +'<p class="rating_tipbg">'+last_ping_user.accurate+'</p>'
              +'<p class="img_b" id="last_ping_img"><a href="'+last_ping_user.home_url+'"  target="_blank"><img src="'+last_ping_user.face_url+'" /></a></p>'
              +'<p class="f_6"><a href="'+last_ping_user.home_url+'" target="_blank">'+last_ping_user.nickname+'</a>'+photo_code+'</p>'
              +'<p class="f_6">'+last_ping_user.age_show+last_ping_user.location_prov+last_ping_user.location_city+'</p>'
              //+last_ping_user.ico_button
              +'<p class="fs_12"><a href="<?php echo $urlsite; ?>/hot/out/" target="_blank" class="f_6">查看我打过的人..</a></p>'
              ;
    $("#last_ping_div").html(code);
    $("#last_ping_div").css("display","none");
    $("#last_ping_div").removeClass("mt40");
    $("#last_ping_div").show('slow',function(){preload_next_img(preload_img_url);});
    
    if(last_ping_user.is_play) {
        $("#perfectff").css({width:60,height:60});
        $("#perfectie").css({width:60,height:60});
        var sound_off;
        if(Cookies.get('sound_off')==1)
        {
            sound_off=1;
        }
        else
        {
            sound_off=0;
        }
        if($.browser.msie) {
            $("#perfectie")[0].play_movie(sound_off);
        }
        else {
            $("#perfectff")[0].play_movie(sound_off);
        }
    }

    else {
        $("#perfectie").css({width:0,height:0});
        $("#perfectff").css({width:0,height:0});
    }
}

function preload_next_img(preload_img_url)

{
    $("#preload_img").attr("src",preload_img_url);
}
var time_queue_msg;

function show_queue_msg(obj)
{
    if(obj.offsetTop<=0)
    {
        $("#queue_msg").css('top',event.clientY+document.documentElement.scrollTop+36-60);
        $("#queue_msg").css('left',event.clientX+document.documentElement.scrollLeft+36-130); 
    }
    else
    {
        $("#queue_msg").css('top',obj.offsetTop+36-25);
        $("#queue_msg").css('left',obj.offsetLeft+36-100); 
    }
    $("#queue_msg").css("display","");
    time_queue_msg = setTimeout("hide_queue_msg()",4000);
}

function hide_queue_msg()
{
    clearTimeout(time_queue_msg);
    $("#queue_msg").css("display","none");
}

function show_mark_btn(id)
{
    $("#mark_"+id).css('display','none');
    $("#mark_"+id).css('visibility','visible');
    $("#mark_"+id).fadeIn('fast',function(){disabled_mark_btn(false);});
}

function disabled_mark_btn(value)
{
    for(var i=1; i<=10; i++)
    {
        $("#mark_"+i).attr('disabled',value);
        $("#mark_"+i).removeClass();
        if(value)
        {
            $("#mark_"+i).addClass("rate_buttom"+(i+20)+"_0");
            $("#mark_"+i).css('cursor','default');
        }
        else
        {
            $("#mark_"+i).addClass("rate_buttom"+(i+20));
            $("#mark_"+i).css('cursor','pointer');
        }
    }
}
function hide_mark_btn(id)
{
    disabled_mark_btn(true);
    play_sound();
    $("#mark_"+id).fadeOut('fast',function(){ $("#mark_"+id).css('display','');$("#mark_"+id).css('visibility','hidden'); });
}

$(document).ready(function(){
    if($.browser.msie)
    {
        browser_str = 'IE '+$.browser.version;
        var browser_version_str = navigator.appVersion.toString();
        //alert(browser_version_str);
        if(browser_version_str.indexOf('TencentTraveler')>0)
        {
            browser_str += '(腾讯TT)';
        }
        else if(browser_version_str.indexOf('360SE')>0)
        {
            browser_str +=  '(360安全浏览器)';

        }
        else if(browser_version_str.indexOf('Maxthon')>0)
        {
            browser_str +=  '(傲游)';
        }
    }
    else if($.browser.safari)
    {
        browser_str = 'Safari';
    }
    else if($.browser.opera)
    {
        browser_str = 'Opera';
    }
    else if($.browser.mozilla)
    {
        browser_str = 'Firefox';
    }
    screen_size = window.screen.width+ '*'+window.screen.height;
    lang = navigator.systemLanguage?navigator.systemLanguage:navigator.language;
    //alert(browser_str + ' ' + screen_size);
    
    $.ajax({
       type: "POST",
       url: "/index.php?s=/stat/",
       data: "browser="+encodeURIComponent(browser_str)+"&screen="+screen_size+"&lang="+lang.toLowerCase(),
       success: function order_result(re)
       {
            //alert(re);
       }
    });
});

$("#sex_").attr("checked", true);
$("#ping_age").val('');
function show_contact(uid)
{
    if(18090226 == 0)
    {
        return false ;
    }
    if(18090226 == uid)
    {
        self.location.href='/user/contact/';
        //Win.dialog({type:'info',msg:'查看自己的联系方式，<a href="/user/contact/">点这里</a>！'});
        return false ;
    }
    if(!1)
    {
        var msg_code = '<div class="popup_c"><div>要看<?php echo ui_sex($m['sex']) ?>的联系方式，请先<a href="/photo/up_form/">上传自己的照片</a>！</div><div class="opt"><a href="/photo/up_form/" class="btn1">上传照片</a></div></div>' ;
        Win.dialog({width:460,msg:msg_code});
        return false ;
    }
    if(0.5 < 0.5)
    {
        var msg_code = '<div class="popup_c"><div class="pop_info_contact">	<p>您还不能查看<?php echo ui_sex($m['sex']) ?>的联系方式！</p>	<p style="margin:15px 0 0 0;">请先<a href="/18090226">完善您的资料</a> <span class="f_r1">50%</span> 以上！</p>	<div class="clear" style="margin:15px 0 0 100px;">		<div class="fl">您目前的资料完成度为 <span class="f_blue1">50%</span>&nbsp;</div>		<div class="fl" style="margin:3px 0 0 0;"><div class="fillbar fl" title="50% completed"><span style="width:25px"></span></div></div>	</div></div><div class="opt"><a href="/18090226" class="btn1">完善我的资料</a></div></div>' ;
        Win.dialog({width:460,msg:msg_code});
        return false ;
    }

    $.ajax({
       type: "POST",
       url: "/index.php?s=home/contact/",
       data: 'uid='+uid+'&',
       success: function reply_success(re)
                {
                    switch(re)
                    {
                        case '-2'://未登陆
                                show_login_form();
                                break;
                        case '-3'://提示升级vip
                                var msg_code = '<div class="popup_c"><div><p><span class="f_blue1">VIP</span>可以查看联系方式！</p></div><div class="opt"><a href="/pay/vip/" target="_blank" class="btn1 btn_b1">了解升级VIP！</a> &nbsp; 或 &nbsp; <a onclick="Win.close();" class="dashed">关闭</a></div></div>' ;
                                Win.dialog({title:'消息提示',width:460,msg:msg_code});
                                break;
                        case '-4'://联系方式隐藏
                                Win.dialog({type:'info',msg:'联系方式已被该用户隐藏！'});
                                break;
                        default://
                                var contact_info=jQuery.parseJSON(re);
                                //alert(contact_info);
                                var msg_code = '<div class="popup_c_link">'
                                            + '<p class="popup_c_link_t fb_13"><span class="f_blue">'+contact_info.nickname+'</span> 的联系方式</p>'
                                            + '	<div class="clear">'
                                            + '		<div class="fl">'
                                            + '			<img src="'+contact_info.face_url+'" alt="形象照" />'
                                            + '		</div>'
                                            + '		<div class="link_list fl">'
                                            + '			<dl class="clear">'
                                            + '				<dt class="f_6">QQ：</dt><dd>'+contact_info.qq+'</dd>'
                                            + '			</dl>'
                                            + '			<dl class="clear">'
                                            + '				<dt class="f_6">MSN：</dt><dd>'+contact_info.msn+'</dd>'
                                            + '			</dl>'
                                            + '			<dl class="clear">'
                                            + '				<dt class="f_6">联系电话：</dt><dd>'+contact_info.phone+'</dd>'
                                            + '			</dl>'
                                            + '			<dl class="clear">'
                                            + '				<dt class="f_6">其它：</dt><dd>'+contact_info.contact_other+'</dd>'
                                            + '			</dl>'
                                            + '		</div>'
                                            + '	</div>'
                                            + '	<div class="opt"><a onclick="Win.close();" class="btn1 btn_b1">确 定</a></div>'
                                            + '</div>';
                                Win.dialog({width:420,msg:msg_code});
                                break;
                    }
                }
    });

}

function reg_other() {
    var thisVal = $.trim($("#nickname").val()) ;
    if(thisVal.length<2 || thisVal.length > 16) {
        alert('昵称只能2-16个字符');
        $("#nickname").focus();
        return false;
    }
    if(!/^[a-zA-Z0-9_\s\-.\!]{2,16}$/.test(thisVal)){
        alert('请输入英文名(昵称)。');
        $("#nickname").focus();
        return false;
    }
    if($("#birth_y").val() == 0 || $("#birth_m").val() == 0 || $("#birth_d").val() == 0){
        alert('请选择出生日期');
        return false;
    }
    if($("#location_prov").val() == "" || $("#location_city").val() == ""){
        alert('请选择所在地资料');
        return false;
    }

    $.post('/reg/other',{nickname:thisVal,birth_y:$("#birth_y").val(),birth_m:$("#birth_m").val(),birth_d:$("#birth_d").val(),location_prov:$("#location_prov").val(),location_city:$("#location_city").val()},function(data){
        if(data.stat) {
            alert(data.errno);
        }
        else location.reload(true);
    },'json');
}

function reg_goto() {
    Win.dialog({msg:'#reg_other', noclose:true,width:500,height:300});
    var gpm = new GlobalProvincesModule;
    gpm.def_province = ["请选择", ""];
    gpm.def_city1 = ["请选择", ""];
    gpm.initProvince(document.getElementById('location_prov'));
    gpm.initCity1(document.getElementById('location_city'), gpm.getSelValue(document.getElementById('location_prov'))); 
    $("#location_prov").bind("change",function(){changeProv();});
    function changeProv() {
        gpm.initCity1(document.getElementById('location_city'), gpm.getSelValue(document.getElementById('location_prov')));
    }
}

function hide_msg(){
    $("#map_tip_msg").hide();
    //document.cooki
}
function back_to_tip()
 {
	var $backToTopTxt = '<a class="b_img" ></a>', $backToTopEle = $('<div class="back2top_fat"></div>').appendTo($("body"))
	.html($backToTopTxt).attr("title", '返回顶部').click(function() {
	$("html, body").animate({ scrollTop: 0 }, 120);
	}), $backToTopFun = function() {
	var st = $(document).scrollTop(), winh = $(window).height();
	(st > 0)? $backToTopEle.show(): $backToTopEle.hide();
	//IE6下的定位
	if (!window.XMLHttpRequest) {
	$backToTopEle.css("top", st + winh - 166);
	}
	};
	$(window).bind("scroll", $backToTopFun);
	$(function() { $backToTopFun(); });
}
back_to_tip();
</script>
  <link href="<?php echo ($urlstatic2); ?>/css/checkbox3.css<?php echo ($urltail); ?>" rel="stylesheet" type="text/css" />
  <div id="comment_div" class="reply_box" style="display:none">
    <p class="top_img"><img src="<?php echo ($urlstatic); ?>/img/reaply_bg.png" alt="背景" /></p>
    <div class="reply_box_bg">
      <textarea id="comment_content" name="comment_content" onclick="savePos(this);" onKeydown="if(event.keyCode==13 && event.ctrlKey) submit_comment()"  onKeyUp="savePos(this);" onPaste="savePos(this);"  onBlur="savePos(this);" class="list" ></textarea>
    </div>
    <p class="clear"> <span class="fl"><a onclick="submit_comment()" class="btn1">发送评论</a></span> <span class="fl"><img src="<?php echo ($urlstatic); ?>/img/ico_ce.gif" alt="表情" onclick="face51New.show(this,'comment_content','_textarea');" style="cursor:pointer;" /></span> <span class="fl list">(评论只有对方本人可见)</span> </p>
    <div id="divFace" style="display:none;"></div>
    <script language="javascript" src="<?php echo ($urlstatic2); ?>/js/face.js<?php echo ($urltail); ?>"></script>
    <script language="javascript" src="<?php echo ($urlstatic2); ?>/js/pub_face_all.js<?php echo ($urltail); ?>"></script>
  </div>
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