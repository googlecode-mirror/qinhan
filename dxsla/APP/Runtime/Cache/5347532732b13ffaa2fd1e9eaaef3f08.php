<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>大学生恋爱网 问问</title>
<meta content="大学生恋爱网，恋爱网，大学生恋爱，大学生谈恋爱，大学生爱情，大学生交友，校园交友，校园恋爱，高校交友，大学生恋爱观，大学生爱情观，大学生恋爱心理，大学生情书，大学生爱情故事" name="keywords" />
<meta content="大学生恋爱网，大学生交友的网站，大学校园里的真实照片的匿名交友，除了上QQ空间你还可以玩的网站。" name="description" />
<link href="<?php echo ($urlstatic2); ?>/css/head_global_main_ask.css<?php echo ($urltail); ?>" rel="stylesheet" type="text/css" />
<script language="javascript" src="<?php echo ($urlstatic2); ?>/js/global_jquery_hello_dialog_chat.js<?php echo ($urltail); ?>"></script>

<script language="javascript" src="<?php echo ($urlstatic2); ?>/js/face.js?gv=148_1"></script>
<script language="javascript" src="<?php echo ($urlstatic2); ?>/js/pub_face_all.js?gv=148_1"></script>
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
  <div class="ask_center ask_w1">
    <!--问问菜单 start-->
    <div class="ask_nav clear">
      <ul class="fl">
        <li><a href="<?php echo ($urlsite); ?>/question/plaza/" class=" active_nav" >问问广场</a></li>
        <li><a href="<?php echo ($urlsite); ?>/question/add/" class="">我要问问</a></li>
        <li><a href="<?php echo ($urlsite); ?>/question/sender/" class="">问问管理</a></li>
        <li><a href="<?php echo ($urlsite); ?>/wenwen/" class="" >小编专访</a></li>
      </ul>
    </div>
    <!--问问菜单 end-->
    <div class="ask_main">
      <div class="ask_main_top clear">
       <!-- <div class="fl f_6"><span><b>问题分类：</b><a class="upbg" href="javascript:qt_type('qt_type')" id="qt_type_name">全部</a></span><span><b>地区：</b> <a class="upbg" href="javascript:qt_type('qt_city')" id="qt_city_name">全部</a></span> </div>-->
	   <div class="fl f_6"><span><a class="fs_12 f_3" href="<?php echo ($urlsite); ?>/question/add/">创建问题</a></span></div>
        <a href="javascript:qt_report();" class="fr fs_12 f_9 dp"><img src="<?php echo ($urlstatic); ?>/img/icon_jubao.png" width="14" height="16" align="absmiddle"> 举报此问题</a>
        <div class="ask_sort clear" style="display:none" id="qt_type" >
          <label>
          <input name="q_type" checked=true  type="checkbox" value="1">
          情感人生</label>
          <label>
          <input name="q_type" checked=true  type="checkbox" value="2">
          娱乐八卦</label>
          <label>
          <input name="q_type" checked=true  type="checkbox" value="3">
          工作学习</label>
          <label>
          <input name="q_type" checked=true  type="checkbox" value="4">
          音乐电影、文艺</label>
          <label>
          <input name="q_type" checked=true  type="checkbox" value="5">
          健康、美食</label>
          <label>
          <input name="q_type" checked=true  type="checkbox" value="6">
          旅游、运动</label>
          <label>
          <input name="q_type" checked=true  type="checkbox" value="7">
          咸得蛋疼</label>
          <label>
          <input name="q_type" checked=true  type="checkbox" value="8">
          宠物</label>
          <label>
          <input name="q_type" checked=true  type="checkbox" value="9">
          其它</label>
          <a class="close btn1" id="qt_type_checked" style="right:60px;" title="clear" onclick="qt_type_checked_all()" >全不选</a><a class="close btn1" onclick="qt_type_save()" >确定</a> </div>
        <div class="city_sort" style="display:none" id="qt_city" code=""> <a onclick="set_question_info('city',0)">全部</a><a onclick="set_question_info('city',1)">上海</a><a onclick="set_question_info('city',2)">北京</a><a onclick="set_question_info('city',3)">广东</a><a onclick="set_question_info('city',4)">浙江</a><a onclick="set_question_info('city',5)">江苏</a><a onclick="set_question_info('city',6)">四川</a><a onclick="set_question_info('city',7)">重庆</a><a onclick="set_question_info('city',8)">湖南</a><a onclick="set_question_info('city',9)">湖北</a><a onclick="set_question_info('city',10)">福建</a><a onclick="set_question_info('city',11)">广西</a><a onclick="set_question_info('city',12)">山东</a><a onclick="set_question_info('city',13)">河南</a><a onclick="set_question_info('city',14)">河北</a><a onclick="set_question_info('city',15)">辽宁</a><a onclick="set_question_info('city',16)">陕西</a><a onclick="set_question_info('city',17)">安徽</a><a onclick="set_question_info('city',18)">天津</a><a onclick="set_question_info('city',19)">江西</a><a onclick="set_question_info('city',20)">云南</a><a onclick="set_question_info('city',21)">黑龙江 </a><a onclick="set_question_info('city',22)">山西</a><a onclick="set_question_info('city',23)">吉林</a><a onclick="set_question_info('city',24)">贵州</a><a onclick="set_question_info('city',25)">海南</a><a onclick="set_question_info('city',26)">甘肃</a><a onclick="set_question_info('city',27)">青海</a><a onclick="set_question_info('city',28)">宁夏</a><a onclick="set_question_info('city',29)">内蒙古</a><a onclick="set_question_info('city',30)">新疆</a><a onclick="set_question_info('city',31)">西藏</a><a onclick="set_question_info('city',32)">香港</a><a onclick="set_question_info('city',33)">台湾</a><a onclick="set_question_info('city',34)">澳门</a><a onclick="set_question_info('city',35)">海外</a> </div>
        <!--<div class="sex" style="display:none" id="qt_sex" title="">
        <a  onclick="set_question_info('sex',3)">全部</a>
        <a onclick="set_question_info('sex',2)">异性</a>
        </div>-->
      </div>
      <div class="ask_question clear" id="question_info_div">
        <p class="question word_break fb_19" id="qt_ask" ><?php echo ($question['question']); ?></p>
		<p style="display:none" id="qt_ask_two" class="question word_break fb_19"><?php echo ($question2['question']); ?></p>
      </div>
      <!--提问图片 start-->
      <div class="ask_pic" <?php if(empty($question['photo_url'])): ?>style="display:none"<?php endif; ?>>
	  	  <?php if(!empty($question['photo_url'])): ?><img id="qt_photo" src="<?php echo ($urlupload); ?>/<?php echo ($question['photo_url']); ?>_480x480.jpg" width="<?php echo ($question['photo_width']); ?>" height="<?php echo ($question['photo_height']); ?>" />
		  <?php else: ?>
		  <img id="qt_photo" src="" /><?php endif; ?>
		  <img style="display:none" id="qt_photo_two" src=""/> 
      </div>
      <!--提问图片 end-->
      <div class="ask_reply clear" id="ask_answer" >
        <p id="leave" style="display:none;" class="f_r"></p>
        填写完答案后，点击拇指图标发出!也可以直接按拇指表态 <span class="tip"></span>
        <div class="asktop clear">
          <div id="edit_type">
            <div style="padding:3px;overflow-x:hidden;overflow-y:scroll;background:#fff;border-color:#7E7E7E #CFCFCF #CFCFCF #7E7E7E;border-style:solid;border-width:1px;box-shadow:2px 2px 2px rgba(0, 0, 0, 0.1) inset;height:88px;width:320px;float:left;" onfocus="qt_click(this)" id="memo" onblur="qt_blur(this)" contenteditable="true" onkeyup="setRange(this);qt_keydown(this)" onmouseup="setRange(this)" onkeydown="qt_keydown(this)" ondrop="return false" ondragover="return false">在此输入答案…</div>
          </div>
          <table class="btn_table" cellpadding="0" cellspacing="0">
            <tr>
              <td><a  href="javascript:qt_answer(2)" class="no" title="反对"></a></td>
              <td align="right"><a class="yes" title="赞成" href="javascript:qt_answer(1)"></a></td>
            </tr>
          </table>
        </div>
        <div class="btm clear">
          <p id="word_num" class="f_9">最多可输入200字</p>
          <a href="javascript:;" class="fl"><img src="<?php echo ($urlstatic); ?>/img/ico_ce.gif" width="39" height="20" onclick="$('#memo').trigger('click');face51New.show(this,'memo','_textarea');"></a>
          <p class="m_10">
            <label class="nm">
            <input type="checkbox" name="sync" id="anonymity" class="checkbox1" value="1" onclick="sync_check('anonymity')">
            <span class="f_6" title="匿名回答后，答案将不会出现在您的个人主页上，对方也不会知道是谁回答">匿名回答</span></label>
            <label class="nm" >
            <input type="checkbox" name="sync" id="sync_home" disabled="disabled" class="checkbox1" value="2" onclick="sync_check('sync_home')">
            <span class="f_9" id="sync_home_p">公开到我的主页</span></label>
            <label class="nm" >
            <input type="checkbox" name="attention" id="attention" disabled="disabled" class="checkbox1" value="1" onclick="sync_check('attention')">
            <span class="f_9" id="sync_attention_p">发布到动态</span></label>
          </p>
        </div>
      </div>
    </div>
  </div>
  <!--中间部分 end-->
  <div class="question_ple"> <a class="f_6" href="<?php echo ($urlsite); ?>/other/kf/?mtype=7" target="_blank">对“问问”的意见反馈</a>
    <div class="title">回答后看提问者</div>
    <ul class="content">
      <li id="question_userinfo_uid"><img id="question_uid_face" src="<?php echo ($urlstatic); ?>/img/ask_wh.png?gv=82_1"/></li>
      <li class="f_blue" id="question_uid_nickname"> </li>
      <li class="f_6" id="question_uid_prov"> </li>
    </ul>
    <p id="question_uid_type"> </p>
    <p id="question_uid_qtinfo" class="word_break"> </p>
    <p class="f_6 word_break" id="question_uid_answer"> </p>
    <p class="f_6 word_break" id="question_more_link"></p>
  </div>
</div>
<div class="vote_box" style="position:absolute;display:none;">
  <p>回复发送成功</p>
  <div class="clear vote_num">
    <p class="fl clear"><img src="<?php echo ($urlstatic); ?>/img/ico_yes.gif?gv=82_1" width="34" height="47"><span id="vote_yes">24%</span></p>
    <p class="fr clear"><img src="<?php echo ($urlstatic); ?>/img/ico_no.gif?gv=82_1" width="34" height="47"><span id="vote_no">76%</span></p>
  </div>
  <p class="fb_20">您是第<span id="vote_total"><?php echo ($count); ?></span>位回答者</p>
</div>
<script>

var qt_type_list = {"1":{"id":"1","title":"u60c5u611fu4ebau751f","status":"1","sex":"0"},"2":{"id":"2","title":"u5a31u4e50u516bu5366","status":"1","sex":"0"},"3":{"id":"3","title":"u5de5u4f5cu5b66u4e60","status":"1","sex":"0"},"4":{"id":"4","title":"u97f3u4e50u7535u5f71u3001u6587u827a","status":"1","sex":"0"},"5":{"id":"5","title":"u5065u5eb7u3001u7f8eu98df","status":"1","sex":"0"},"6":{"id":"6","title":"u65c5u6e38u3001u8fd0u52a8","status":"1","sex":"0"},"7":{"id":"7","title":"u54b8u5f97u86cbu75bc","status":"1","sex":"0"},"8":{"id":"8","title":"u5ba0u7269","status":"1","sex":"0"},"9":{"id":"9","title":"u5176u5b83","status":"1","sex":"0"}};
var qt_info = <?php echo (json_encode($question)); ?>;
var qt_info_two = <?php echo (json_encode($question2)); ?>;
var html_q_code = null;
var qt_ajax = false;
var qt_type_select = '';
var qt_city_select = '';
var first_p = 0;
var qq_code = 0;
var select_show = 0;
var qt_mesage = '';
var qq_qt_mesage = '';
var qq_qt_info = 0;
$(document).ready(function(){
    if(html_q_code == 1) first_p = 1;
    if(!IM.gFlash || ($.browser.msie && $.browser.version >= 9)) {
        $("#edit_type").html('<textarea class="input_1" onkeydown="qt_keydown(this)" onfocus="qt_click(this)" id="memo" onblur="qt_blur(this)" onkeyup="setRange(this)" onmouseup="setRange(this)">在此输入答案…</textarea>');
    }
});
/*
function sync_check(id){
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
                $("input[name='sync']").attr('checked',false);
                $("#"+id).attr('checked','checked');
                if(id == 'anonymity'){
                    $("#attention").attr('checked',false);
                }else{
//                     $("#attention").attr('disabled',false);
                     document.getElementById('attention').disabled=false;
                }
            }else{
                if(id == 'sync_home'){
                    $("#attention").attr('checked',false);
                }
                if(id !='anonymity'){
                    select_show = 1;
                }
                
        }
    }
}
 */
function qt_report() {
    window.open('<?php echo ($urlsite); ?>/other/kf/?mtype=9&fid='+qt_info.uid+'&pid='+qt_info.id);
}

function qt_click(obj) {
    if(IM.gFlash) {
        if(obj.innerHTML == "在此输入答案…") {
            obj.innerHTML = '';
        }
        else qt_keydown(obj);
    }
    else {
        if(obj.value == "在此输入答案…") {
            obj.value = '';
        }
        else qt_keydown(obj);
    }
    $("#memo").css("color","#000");
}

function qt_blur() {
    if(IM.gFlash) {
        var memo = $("#memo").html();
    }
    else {
        var memo = $("#memo").val();
    }
    if(memo.trim().length == 0 || memo == "在此输入答案…") {
        $("#word_num").html("你还可以输入200/200字");
        if(IM.gFlash) {
            $("#memo").html("在此输入答案…");
        }
        else $("#memo").val("在此输入答案…");
        $("#memo").css("color","#999999");
        if(select_show == 0){
            $("#sync_home").removeAttr('checked');
            $("#attention").removeAttr('checked');
            $("#sync_home_p").removeClass();
            $("#sync_home_p").addClass('f_9');
        }
    }
}
/*
function qt_keydown(obj)
{
    if(IM.gFlash) {
        var memo = $("#memo").html();
    }
    else {
        var memo = $("#memo").val();
    }
    var input_count = qt_length(memo.trim());
    var  out_count = 200-input_count;
    var type = arguments[0]; 
    if( input_count >0 && memo !='在此输入答案…'){
      $("#memo").css("color","#000000");
    }
    if( input_count > 200 )
    {
        var out_count =  input_count - 200;
        msg = "<img class='ico' src='"+version_img('/i/pub/ico_alert.gif')+"' />已超过"+out_count+"字";
        $("#word_num").html(msg);
        
    }else{
            $("#word_num").html("你还可以输入"+out_count+"/200字");	
            
            if(input_count ==0){
                    $("#sync_home").removeAttr('checked');
                    $("#sync_home").attr('disabled','disabled');
                    $("#sync_home_p").removeClass();
                    $("#sync_home_p").addClass('f_9');
                    $("#attention").removeAttr('checked');
                    $("#attention").attr('disabled','disabled');
                    $("#sync_attention_p").removeClass();
                    $("#sync_attention_p").addClass('f_9');
            }else{
                if(select_show == 0){
                        //$("#sync_home").removeAttr('disabled');
                        document.getElementById('sync_home').disabled=false;
                        if($("#anonymity").attr('checked')!="checked"){
                            $("#anonymity").attr('checked',false);
                            $("#sync_home").attr('checked','checked');
                            //$("#attention").removeAttr('disabled');
                            document.getElementById('attention').disabled=false;
                            $("#attention").attr('checked','checked');
                        }
                        
                }else{
//                       $("#sync_home").removeAttr('disabled');
//                       $("#attention").removeAttr('disabled');
                      document.getElementById('sync_home').disabled=false;
                      document.getElementById('attention').disabled=false;
                }
                $("#sync_home_p").removeClass();
                $("#sync_home_p").addClass('f_6');
                $("#sync_attention_p").removeClass();
                $("#sync_attention_p").addClass('f_6');
            }
    }
}

 */
function set_question_info(type,val){
    $("#qt_"+type).attr('code',val);
    $("#qt_"+type).hide();
    $.post('/question/get_question_info', {prov:$("#qt_city").attr("code")}, function(data) {
        qt_info = data.question;
        qt_info_two = data.question_two;
        qt_city_select = data.question_city;
        qt_type_select = '';
        if(qt_city_select =='' || qt_city_select == null){
            $("#qt_city_name").html('全部');
        }else{
            $("#qt_city_name").html(qt_city_select);
        }
        if(qt_info == false){
            $("#qt_ask").html('');
            $("#qt_ask").hide();
            $("#qt_photo").attr("src",'');
            $("#qt_photo").hide();
            $("#ask_answer").hide();
        }
        if(data.q_code == 1 && first_p!=1) {
            $("#question_info_div").prepend('<div style="height:30px; color:#FF6600;">您选择的地区暂时没有新的问题，请选择其他地区</div>');
            $("#qt_city_name").html('全部');
            first_p = 1;
            qt_city_select ='';
            setTimeout(function(){
                $("#question_info_div div:first").remove();
                $("#qt_photo").show();
                $("#qt_ask").show();
                first_p = 0;
            },3000);
        }
        if(data.q_code != 1 && qt_info)
        {
            $("#question_info_div div:first").remove();
            $("#ask_answer").show();
        }
        if(qt_info) {
            $("#qt_type_name").html('全部');
            opt_question();
        }
    }, 'json');
};
function qt_answer(vote) {
    if(IM.gFlash) {
        var memo = $("#memo").html();
    }
    else {
        var memo = $("#memo").val();
    }
    if(qt_length(memo) > 200){
        alert("回答的字数已超出");
        return;
    }
    if($("input[name='sync']:checked").val() == 2 && memo == '' || $("input[name='sync']:checked").val() == 2 && memo=='在此输入答案…'){
        alert("您没有输入答案，表态将不会公开到您的主页上");
        $("#memo").focus();
        if(IM.gFlash) {
            $("#memo").html('');
        }
        else $("#memo").val('');
        return;
    }
    if(qt_ajax == true) return;
    $("#vote_total").html(1+parseInt(qt_info.answer_count));
    if(vote == 1) qt_info.agree_count++;
    else if(vote == 2) qt_info.against_count++;
    if(parseInt(qt_info.agree_count)+parseInt(qt_info.against_count) > 0) {
        var procc = parseInt((qt_info.agree_count/(parseInt(qt_info.agree_count)+parseInt(qt_info.against_count)))*100);
    }
    else var procc = 50;
    $("#vote_yes").html(procc+'%');
    $("#vote_no").html(100-procc+'%');
    //var pos = Win.pos(400, 50);
    
    if($("#qt_photo").attr('src') != ''){
        var pos = $("#qt_photo").offset();
        var vote_top = pos.top + (parseInt($("#qt_photo").css("height"))-215)/2;
        var vote_left = pos.left + (parseInt($("#qt_photo").css("width"))-280)/2;
        if (vote_top<200 || vote_left<440){
            var vote_top = 250;
            var vote_left = 540;
        }
    }else{
        var vote_top = 250;
        var vote_left = 540;
    }
    
    qt_ajax = true;
    var answer_cont = cont_filter(memo);
    if(answer_cont == "在此输入答案…") {
        answer_cont = "";
    }
	//alert(qt_info.id);
    $.post('<?php echo ($urlsite); ?>/answer/answer', {from:"plaza", q_id:qt_info.id, q_uid:qt_info.uid,answer_cont:answer_cont,show:$("input[name='sync']:checked").val(),vote:vote,qw_id:qt_info_two.id,qw_uid:qt_info_two.uid,attention:$("input[name='attention']:checked").val(),prov:$("#qt_city").attr("title")}, function(data) {
        if(data.code >= 4 && data.code <= 6){
            Win.dialog({type:'info',msg:'你的回答含有敏感词请重新填写'});
            qt_ajax = false;
            return;
        }
        if(data.daty_num <= 20 && data.daty_num >0){
            $("#leave").html('<img class="ico" src="<?php echo $urlstatic ?>/img/ico_alert.gif" />你今天还有'+data.daty_num+'次回答问题的机会，请把握好机会说出自己最优质的答案吧！').show();
        }
        if(data.daty_num == 0){
           $("#leave").html('今天您已经回答完了50个问题，明天再来吧').show();
        }
        if(data.code == 9){
            Win.dialog({type:'info',msg:data.msg});
            qt_ajax = false;
            return;
        }
        qt_info = qt_info_two;
        qt_info_two = data.question;
        if(data.prev_userinfo != false){
            if(data.prev_userinfo.is_anonymity == 1){
                if(data.prev_userinfo.sex == 2) var no_face = version_img('none_s2_72_c.jpg');
                else var no_face = version_img('none_s1_72_c.jpg');
                $("#question_userinfo_uid").html('<img id="question_uid_face" src="'+no_face+'"/>');
                $("#question_uid_nickname").html('匿名');
                $("#question_more_link").html('<a href="<?php echo ($urlsite); ?>/home/anonymity/?uid='+data.answer.question_uid+'&qid='+data.answer.question_id+'$nofilter=1" target="_blank" >点击查看全部答案</a>');
            }else{
                $("#question_uid_nickname").html('<a href="<?php echo ($urldomain); ?>/'+data.prev_userinfo.question_uid+'" target="_blank">'+data.prev_userinfo.nickname+'</a>');
                $("#question_userinfo_uid").html('<a href="<?php echo ($urldomain); ?>/'+data.prev_userinfo.question_uid+'" target="_blank"><img id="question_uid_face" src="'+data.prev_userinfo.face+'"/></a>');
                $("#question_more_link").html('<a href="<?php echo ($urlsite); ?>/home/question/?uid='+data.answer.question_uid+'&qid='+data.answer.question_id+'&nofilter=1" target="_blank" >点击查看全部答案</a>');
                
            }
            $(".title").html('上一个问题的提问者');
            $("#question_uid_prov").html(data.prev_userinfo.birth_y+data.prev_userinfo.location_prov);
            $("#question_uid_qtinfo").html(data.prev_userinfo.u_sex+"问：<span class=\"word_break\">"+$("#qt_ask").html()+'</span>');
            $("#question_uid_type").html("问题分类："+data.prev_userinfo.question_type);
            answer_cont = data.answer.answer_cont;
            answer_cont =answer_cont.replace(/\</ig,"&lt;");
            answer_cont =answer_cont.replace(/\>/ig,"&gt;");
            var r_img1=/\[img]/ig;
            var r_img2=/\[\/img]/ig;
            answer_cont = answer_cont.replace(r_img1,'【');
            cc = answer_cont.replace(r_img2,'】');
            var bb=cc;
            bb=bb.substr(0,25);
            if(bb.lastIndexOf('】') < bb.lastIndexOf('【')){
                dd=cc.replace(bb,'');
                num=dd.indexOf('】');
                num =num+26;
                answer_cont=cc.substr(0,num);
            }else{
                answer_cont = bb;
            }
            var img_r=/\【/ig;
            var img_r1=/\】/ig;
            answer_cont = answer_cont.replace(img_r,'<img src="<?php echo ($urlstatic); ?>/face/');
            answer_cont = answer_cont.replace(img_r1,'">');
            if(vote == 1) var muzhi='agree';
            if(vote == 2) var muzhi='opposition';
            if(answer_cont !='') answer_cont = answer_cont+"……";
            $("#question_uid_answer").html("<span class=\"word_nowrap\">你回：</span><span class="+muzhi+"></span>"+answer_cont);
        }
        $(".vote_box").css({"position":"absolute","top":vote_top,"left":vote_left,"display":"block","z-index":"1000"});
        if(qt_info == false &&  data.question == false) {
            first_p = 1;
            setTimeout(function() {
                $("#qt_ask").html('');
                $("#qt_ask").hide();
                $("#qt_photo").attr('src','');
                $(".ask_pic").hide();
                $("#ask_answer").hide();
                $("#memo").blur();
                $("#question_info_div").prepend('<div class="sort_noqestion clear"><p class="fl"> <img src="<?php echo $urlstatic ?>/img/sweat.gif"></p><p class="text fl">问问新上线，问题可能还不多。去“<a href="/question/add/">我要问问</a>”提你自己想问的问题吧，马上就会收到新回复哦！<br></p></div>');
                $(".vote_box").hide();
            },3000);
            return false;
        }
        if(data.q_code == 1 && data.question != false) {
            if(qt_info != false){
                    if(qt_type_select !=''){
                        qt_type_select = "";
                         qq_qt_mesage = '您选择的分类暂时没有新的问题，请选择其他分类';
                         qq_qt_info = 1;
                    }else{
                        qt_city_select = '';
                        qq_qt_mesage ='您选择的地区暂时没有新的问题，请选择其他地区';
                        qq_qt_info = 2;
                    }
                qq_code = 1;  
            }else{
                    if(qt_type_select !=''){
                        qt_type_select = "";
                        qt_mesage = '您选择的分类暂时没有新的问题，请选择其他分类';
                        $("#qt_type_name").html('全部');
                    }
                    else{
                        qt_mesage ='您选择的地区暂时没有新的问题，请选择其他地区';
                         qt_city_select = '';
                        $("#qt_city_name").html('全部');
                    }
                 setTimeout(function(){
                    $("#question_info_div").prepend('<div style="height:30px; color:#FF6600;" >'+qt_mesage+'</div>'); 
                    first_p = 1;
                    setTimeout(function(){
                        $("#question_info_div div:first").remove();
                    },6000);
                    $(".vote_box").hide();
                    qt_ajax = false;
                    qq_code = 0;
                    first_p = 0;
                },3000);         
            }
        }
        if(qq_code == 1 && qt_info != false && data.q_code !=1){
            if(qq_qt_info ==1){
                $("#qt_type_name").html('全部');
            }else{
                $("#qt_city_name").html('全部');
            }
            setTimeout(function(){
                $("#question_info_div").prepend('<div style="height:30px; color:#FF6600;" >'+qq_qt_mesage+'</div>'); 
                first_p = 1;
                setTimeout(function(){
                    $("#question_info_div div:first").remove();
                },6000);
                $(".vote_box").hide();
                qt_ajax = false;
                qq_code = 0;
                first_p = 0;
            },3000);
        }
        
        if(data.question != false) {
            if(qt_info == false) {
                qt_info = data.question;
                qt_info_two = false;
            }
            setTimeout("opt_question()",'3000');
            if(data.code == 7){	
                var msg= "这个问题你已经回答";
                Win.dialog({type:'info',width:500,msg:msg});
            }
        }
        else {
            if(qt_info == false) {
                first_p = 1;
                setTimeout(function() {
                    $("#qt_ask").html('');
                    $("#qt_ask").hide();
                    $("#qt_photo").attr('src','');
                    $(".ask_pic").hide();
                    $("#ask_answer").hide();
                    $("#question_info_div").prepend('<div class="sort_noqestion clear"><p class="fl"> <img src="<?php echo $urlstatic ?>/img/sweat.gif"></p><p class="text fl">问问新上线，问题可能还不多。去“<a href="/question/add/">我要问问</a>”提你自己想问的问题吧，马上就会收到新回复哦！<br></p></div>');
                    $(".vote_box").hide();
                },3000);
                return false;
            }
            else setTimeout("opt_question()",'3000');
        }
    }, 'json');
}

function opt_question(){
// 显示用户信息
    $(".vote_box").hide();
    var act = document.activeElement.id;
    if(act == 'memo'){
        if(IM.gFlash) {
            $("#memo").html('');
        }
        else $("#memo").val('');
        $("#word_num").html("你还可以输入200/200字");
    }else{
        if(IM.gFlash) {
            $("#memo").html('在此输入答案…');
        }
        else $("#memo").val('在此输入答案…');
        qt_blur();
    }
    $(".ask_pic").show();
    $("#qt_ask").show();
    var img_width = qt_info.photo_width;
    var img_height = qt_info.photo_height;
    var max_width = 320;
    var max_height = 300;
    if(img_width/img_height>= max_width/max_height){
        if(img_width>max_width){
            wsize = max_width;
            hsize = (img_height*max_width)/img_width;
        }else{
            wsize = img_width;
            hsize = img_height;
        }
    } else{
        if(img_height>max_height){
            wsize=(img_width*max_width)/img_height;
            hsize = max_width;
        }else{
            wsize=img_width;
            hsize=img_height;
        }
    }
    if (qt_info.photo_id == 0) {
        $(".ask_main").css('height','0px');
        $(".ask_pic").css('display','none');
    }else{
        $(".ask_main").css('height','660px');
        $(".ask_pic").css('display','block');
    }
    $("#qt_ask").html(qt_info.question);
    $("#qt_ask_two").html(qt_info_two.question);
    //$("#qt_ask").removeClass();
    //$("#qt_ask").addClass(qt_info.w_class+" question word_break");
    $("#sync_home").attr('checked',false);
    $("#sync_home_p").removeClass();
    $("#sync_home_p").addClass('f_9');
    var img_css = {width:wsize,height:hsize};
    $("#qt_photo").attr("src",qt_info.photo_url);
    $("#qt_photo_two").attr("src", qt_info_two.photo_url);
    $("#qt_photo").attr("style", "").css(img_css);
    qt_ajax = false;
    select_show = 0;
}

function qt_type(type) {
    var display = $("#"+type).css("display");
    switch (type){
        case 'qt_type':
            $("#qt_sex").hide();
            $("#qt_city").hide();
        break;
        case 'qt_sex':
            $("#qt_type").hide();
            $("#qt_city").hide();
        break;
        case 'qt_city':
            $("#qt_sex").hide();
            $("#qt_type").hide();
        break;
    }
    if(display == "none") {
        $("#"+type+'_name').removeClass("upbg");
        $("#"+type+'_name').addClass("dnbg");
        $("#"+type).show();
         if(type == 'qt_type'){
          if(qt_type_select == '' || qt_type_select == null){
            $("input[name='q_type']").attr("checked","checked");
            $("#qt_type_checked").attr("title","clear");
            $("#qt_type_checked").html("全不选");
            }
        }
    }
    else {
        $("#"+type+'_name').removeClass("dnbg");
        $("#"+type+'_name').addClass("upbg");
        $("#"+type).hide();
    }
}
function qt_type_save() {
    var o_type = $("input[name='q_type']:checked");
    var type_ids = '';
    for(var i=0; i< o_type.length; i++) {
        type_ids += o_type[i].value +',';
    }
    if(type_ids == "") {
        alert("至少选择一个问题类型");
        return;
    }
    $.post('<?php echo ($urlsite); ?>/question/type_save', {ids:type_ids}, function (data) {
        if(data.errno == 500) {
            Win.dialog({'type':'alert','msg':data.msg,height:120,enter:function(){location.href="/wenwen/question/"}});
        }
        if(data.errno == 200){
            qt_type_select = data.msg;
            qt_city_select = '';
            qq_code == 1;
            $("#qt_type").hide();
            $("#qt_type_name").removeClass("upbg");
            $.post('/question/get_question_info', {}, function(datas) {
                if(!datas) {
                    alert("回答问题失败");
                    return;
                }
                qt_info = datas.question;
                qt_info_two = datas.question_two;
                if(qt_type_select == '' || qt_type_select == null){
                    $("#qt_type_name").html('全部');
                }else{
                    $("#qt_type_name").html('<b></b>'+qt_type_list[qt_info.type].title);
                }
                if(qt_info == false){
                    $("#qt_ask").html('');
                    $("#qt_ask").hide();
                    $("#qt_photo").attr("src",'');
                    $("#qt_photo").hide();
                    $("#ask_answer").hide();
                    
                }
                if(datas.q_code == 1 && first_p!=1) {
                    $("#question_info_div").prepend('<div style="height:30px; color:#FF6600;">您选择的分类暂时没有新的问题，请选择其他分类</div>');
                    first_p = 1;
                    $("#qt_type_name").html('全部');
                    qt_type_select = '';
                    setTimeout(function(){
                        $("#question_info_div div:first").remove();
                        $("#qt_photo").show();
                        $("#qt_ask").show();
                        first_p = 0;
                    },3000);
                }
                if(datas.q_code != 1 && qt_info)
                {
                    $("#question_info_div div:first").remove();
                    $("#ask_answer").show();
                }
                if(qt_info) {
                    $("#qt_city_name").html('全部');
                    opt_question();
                }
    }, 'json');
        }
    }, 'json');
}
function qt_type_checked_all(){
    var type = $("#qt_type_checked").attr("title");
    if(type == 'all'){
        $("input[name='q_type']").attr("checked","checked");
        $("#qt_type_checked").attr("title","clear");
        $("#qt_type_checked").html("全不选");
        $("#qt_type_name").html("全部");
    }
    if(type == 'clear'){
        $("input[name='q_type']").attr("checked",false);
        $("#qt_type_checked").attr("title","all");
        $("#qt_type_checked").html("全选");
    }
}
function set_weibo(weibo, bind){
    if(weibo==1)
    {
        if(parseInt(bind) !== 1)
        {
            window.open("http://www.jjdd.com/tsina/certification/","","height=500, width=600");
        }
        
    }
    else if(weibo==2)
    {
        if(parseInt(bind) !== 1)
        {
            window.open("http://www.jjdd.com/tqq/certification","","height=600, width=700");
        }
    }
    
    
}
function submit_forward_question()
{
    set_uid_list();
    var is_anonymity=0;
    if($('#anonymity').attr("checked")==true){
        is_anonymity=1;
    }
    var is_show=0;
    if($('#is_show').attr("checked")==true){
        is_show=1;
    }
    var sync_tsina=0;
    if($('#sync_tsina').attr("checked")==true){
        sync_tsina=1;
    }
    var sync_tqq=0;
    if($('#sync_tqq').attr("checked")==true){
        sync_tqq=1;
    }
    $.ajax({
            type: "POST",
            url: "/question/submit_forward_question/",
            data: 'question='+qt_info.question+'&question_id='+qt_info.id+'&question_uid='+qt_info.uid+'&question_type='+qt_info.type+'&photo_id='+qt_info.photo_id+'&is_anonymity='+is_anonymity+'&is_show='+is_show+'&sync_tsina='+sync_tsina+'&sync_tqq='+sync_tqq+'&to_uid_list='+$('#to_uid_list').val(),
            success: function(re){
                var obj = jQuery.parseJSON(re);
                if(obj.errno == 200) {
                    Win.dialog({'msg':obj.msg,'type':'info'});
                }else{
                    Win.dialog({'msg':obj.msg,'type':'info'});
                }
            }
        });
    
}

function qt_keydown(obj) 
{
    var id = $(obj).attr('id');
    if (id == null) return false;

    if(IM.gFlash) {
        var memo = $("#"+id).html();
    }
    else {
        var memo = $("#"+id).val();
    }
    var input_count = qt_length(memo.trim());
    var  out_count = 200-input_count;
    var type = arguments[0]; 
    if( input_count >0 && memo !='在此输入答案…'){
      $("#"+id).css("color","#000000");
    }
    if( input_count > 200 )
    {
        var out_count =  input_count - 200;
        msg = "<img class='ico' src='"+version_img('ico_alert.gif')+"' />已超过"+out_count+"字";
        $("#word_num").html(msg);
        
    }else{
            $("#word_num").html("你还可以输入"+out_count+"/200字");	
            
            if(input_count ==0){
                    $("#sync_home").removeAttr('checked');
                    $("#sync_home").attr('disabled','disabled');
                    $("#sync_home_p").removeClass();
                    $("#sync_home_p").addClass('f_9');
                    $("#attention").removeAttr('checked');
                    $("#attention").attr('disabled','disabled');
                    $("#sync_attention_p").removeClass();
                    $("#sync_attention_p").addClass('f_9');
            }else{
                if(select_show == 0){
                        //$("#sync_home").removeAttr('disabled');
                        document.getElementById('sync_home').disabled=false;
                        if($("#anonymity").attr('checked')!="checked"){
                            $("#anonymity").attr('checked',false);
                            $("#sync_home").attr('checked','checked');
                            //$("#attention").removeAttr('disabled');
                            document.getElementById('attention').disabled=false;
                            $("#attention").attr('checked','checked');
                        }
                        
                }else{
//                       $("#sync_home").removeAttr('disabled');
//                       $("#attention").removeAttr('disabled');
                      document.getElementById('sync_home').disabled=false;
                      document.getElementById('attention').disabled=false;
                }
                $("#sync_home_p").removeClass();
                $("#sync_home_p").addClass('f_6');
                $("#sync_attention_p").removeClass();
                $("#sync_attention_p").addClass('f_6');
            }
    }
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