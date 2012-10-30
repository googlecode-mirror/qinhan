<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>大学生恋爱网 师兄帮帮忙</title>
<meta content="大学生恋爱网，恋爱网，大学生恋爱，大学生谈恋爱，大学生爱情，大学生交友，校园交友，校园恋爱，高校交友，大学生恋爱观，大学生爱情观，大学生恋爱心理，大学生情书，大学生爱情故事" name="keywords" />
<meta content="大学生恋爱网，大学生交友的网站，大学校园里的真实照片的匿名交友，除了上QQ空间你还可以玩的网站。" name="description" />
<link href="<?php echo ($urlstatic2); ?>/css/head_global_main_ask.css<?php echo ($urltail); ?>" rel="stylesheet" type="text/css" />
<script language="javascript" src="<?php echo ($urlstatic2); ?>/js/global_jquery_hello_dialog_chat.js<?php echo ($urltail); ?>"></script>

<script language="javascript" src="<?php echo ($urlstatic2); ?>/js/pub_face_all.js?gv=148_1"></script>
<script language="javascript" src="<?php echo ($urlstatic2); ?>/js/skill.js?gv=179_1"></script>
<style type="text/css">
.icon-title, .icon-desc, .icon-award {background-image:url("<?php echo $urlstatic; ?>/img/icons-deep-50X50.png"); float:left; height:20px; width:20px; margin:3px 5px 0 0;}
.icon-title {background-position:-88px -123px;}
.icon-desc {background-position:-316px -9px;}
.icon-award {background-position:-162px -85px;}
</style>
</head>
<body>
<?php $nav = 5; ?>
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
	<div class="fm_l_200"> <div class="myhead_box">
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
		
    <!--中间中间部分开始-->
    <div class="nearby_box">
	  <div class="ad_play"><a href="javascript:;" id="banner_vip" style="display:inline;"><img src="<?php echo ($urlstatic); ?>/img/task2.png<?php echo ($urltail); ?>"></a><a style="display:none;" href="javascript:;" id="banner_map"><img src="<?php echo ($urlstatic); ?>/img/task1.png<?php echo ($urltail); ?>"></a></div>
<script>
setTimeout("turn_banner()",5000);
function  turn_banner(){
	if($("#banner_vip").css('display') == 'none'){
		$("#banner_map").hide();
		$("#banner_vip").show();
		
	}else{
		$("#banner_vip").hide();
		$("#banner_map").show();
	}
	
	setTimeout("turn_banner()",6000);
}
</script>	  
	  <div class="skill_mgnav clear" style="margin-top:20px;">
        <p class="navbg fl clear"><a href="<?php echo ($urlsite); ?>/task/" class="selected unline">任务大厅</a> <a href="<?php echo ($urlsite); ?>/task/my_task/" class="unline">我的任务</a> <a href="<?php echo ($urlsite); ?>/task/my_help/" class="unline">我参与帮忙的任务</a></p>
        <p class="fr" style="margin-top:2px;"><a href="<?php echo ($urlsite); ?>/task/add/" class="btn1" onclick="return check_task_sex()">我要发布任务</a></p>
		<p class="fr f_6" style="margin:5px 5px 0 0;">目前仅限女生发布任务</p>
      </div>
<script type="text/javascript">
function check_task_sex() {
	if(myuserinfo.sex == 1) {
		alert('仅限女生发布任务');
		return false;
	} else {
		return true;
	}
}
</script>
	  <!--<div class="area_mg clear">
        <p class="fl" style="margin-left:20px;"><b class="fs_20">三峡大学</b>&nbsp;&nbsp;<a onclick="alert('其他校区暂未开放');" class="unline f_6 skmore_down" href="javascript:void(0);">更改学校</a></p>
		<p class="fr m_t10"></p>
      </div>-->
      <div class="search_skill clear" style="margin-top:12px; border:0;">
        <form action="<?php echo ($urlsite); ?>/task/skilllearn/" method="POST"  name="search_form" >
          <input type="text" value="输入你要搜索的技能名称(支持模糊搜索)" id="input_skill" name="input_skill" class="input3 f_9" onfocus="input_focus();" onblur="input_blur();" onkeydown="if(event.keyCode == 13){if(check_input_skill()){ search_form.submit();}}">
          <input type="submit" class="btn1" value="搜索" onclick="if(check_input_skill()){ search_form.submit();}">
		  <span class="fl">&nbsp;&nbsp;<a href="#" onclick="search_hot_skill('打羽毛球');">打羽毛球</a>&nbsp;&nbsp;<a href="#" onclick="search_hot_skill('旅游');">旅游</a>&nbsp;&nbsp;<a href="#" onclick="search_hot_skill('摄影');">摄影</a>&nbsp;&nbsp;<a href="#" onclick="search_hot_skill('吃零食');">吃零食</a>&nbsp;&nbsp;<a href="###" onclick="search_hot_skill('游泳');">游泳</a>&nbsp;&nbsp;<a href="###" onclick="search_hot_skill('K歌');">K歌</a></span>      
        </form>
      </div>
      <div class="people_skill_w m_b25">
	  	
		<?php if(is_array($tasklist)): $i = 0; $__LIST__ = $tasklist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$t): $mod = ($i % 2 );++$i;?><div class="people_skill clear  ">
          <ul class="user_info">
            <li><a href="<?php echo ($urldomain); ?>/<?php echo ($t['uid']); ?>" target="_blank"> <img src="<?php echo ($GLOBALS['s']['urlupload']); echo ($t['default_pic']); ?>_120x120.jpg" alt="" class="face_s<?php echo ($t['sex']); ?>_b2" /> </a> </li>
            <li><a href="<?php echo ($urldomain); ?>/<?php echo ($t['uid']); ?>" class="unline fb_14" target="_blank" ><?php echo ($t['username']); ?></a></li>
          </ul>
          <div class="skill" style="width:570px;">
            <dl class="know">
             
              <dd class="clear default">
                <div class="clear">
                  <p class="text_w"><span class="icon-title"></span><span class="simple f_3"><?php echo ($t['title']); ?></span></p>
                  <p class="text_w2"><span class="fr f_6"><?php echo (formattime($t['add_time'])); ?></span></p>
                </div>			  
                <div class="clear">
                  <p class="text_w"><span class="icon-desc"></span><span class="detail"><?php echo ($t['content']); ?></span></p>
                  <p class="text_w2 js_text_w2" style="display:none;"><a href="javascript:void(0);" class="exchange_btn" onclick="show_answer_form(<?php echo ($t['uid']); ?>,<?php echo ($t['tid']); ?>);">我要帮</a><a class="study_btn" target="_blank" href="<?php echo ($urlsite); ?>/home/task/?uid=<?php echo ($t['uid']); ?>&tid=<?php echo ($t['tid']); ?>">详情</a></p>
                </div>
				<div class="clear">
				  <p class="text_w"><span class="icon-award"></span><span class="f_3"><?php echo ($t['reward']); ?></span></p>
				  <p class="text_w2">已有<?php echo ($t['answer_count']); ?>人帮忙</p>
				</div>
                <div class="reply_box clear show_comment" id="comment_know_<?php echo ($t['uid']); ?>_<?php echo ($t['tid']); ?>" style="display:none"></div>
              </dd>
            </dl>
          </div>
        </div><?php endforeach; endif; else: echo "" ;endif; ?>
		        
      </div>
      <!--分页-->
      <div class='page'><?php echo ($pages); ?></div>
      <!--中间中间部分结束-->
    </div>
    <!--中间部分结束-->
  </div>
  <script type="text/javascript">
$(document).ready(function(){
	//登陆3次以上用户 如果资料完整度小于50%，显示提示框
	/*if(myuserinfo.profile_completed < 0.5 && 1087 > 3){
		var msg_code = sysmessage_addprofile('profile',(myuserinfo.sex==1)?2:1) ;
		//Win.dialog({width:460,msg:msg_code});
		return false ;
	}
	var tip = 0 ;
	if(1 == tip){
		var msg_code = '3页以后，只允许正式会员查看';
		Win.dialog({type:'info',msg:msg_code,width:450, enterName:'我是正式会员'});
				}else if(3 == tip){
		var msg_code = '<div class="man_invitefriends f_3"><h3 class="m_15">正式会员，才能继续翻页浏览</h3><p class="m_t5">方法一：<a href="/pay/order/?id=2" class="underline">十元升级正式会员</a></p><p class="m_t10">方法二：<span>发送以下注册链接给<img src="<?php echo $urlstatic ?>/img/1.png" align="absmiddle">位好友成功加入，即可成为正式会员</span></p><p class="m_t5"><input type="text" class="input_1 fl" value="http://www.jjdd.com/?iv=18090226"/><span id="clipinner"><object width="50" height="28" id="copyObjIE" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"><param value="always" name="allowScriptAccess"><param value="transparent" name="wmode"><param value="<?php echo $urlstatic ?>/flash/clipboard.swf" name="movie"><param value="high" name="quality"><embed  id="copyObj" width="50" height="28" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" allowscriptaccess="sameDomain" swliveconnect="true" quality="high" wmode="transparent" src="<?php echo $urlstatic ?>/flash/clipboard.swf"></object></span></p><p style="color:red;">您邀请的好友有客服严格审核，虚假注册不予通过，并停用您的帐号</p></div>';
		Win.dialog({msg:msg_code,width:450});
	}else if(2 == tip){
		var msg_code = '通过视频认证后，才能继续翻页浏览。' ;
		Win.dialog({type:'info',msg:msg_code,width:450,enterName:'快速申请认证！',enter:function(){self.location.href='/videoauth/'}});
	}
	alert(1);*/
	$("dd").hover(function(){
		
		$(this).find("p").filter(".js_text_w2").removeAttr("style");
	},function(){
		$(this).find("p").filter(".js_text_w2").attr("style","display:none");
	});
});

$(document).bind("click",function(e){
	if($.browser.msie) {
		var el = event.srcElement;
	}
	else {
		var el = e.target;
	}
	if(!(IM.fInObj(el,"selectbox")) && !(IM.fInObj(el,"down_img"))) {
		hide_option();
	}
});
function show_answer_form(uid,id)
{
	var div_id = 'comment_know' ;
	var div_from = 'home' ;
	var div_content_id = 'comment';
	$.post("/index.php?s=/msg/check/", { friend:uid}, function (data) {
		if(data.stat == 5) {
			var msg_info = redbeans(data.nickname,data.pay_card);
			Win.dialog({'msg':msg_info,'height':400,'width':580,'pay_card':data.pay_card,'enter':function(data){
				$.post("/index.php?s=/msg/check/", { friend:uid, pay_card:data.pay_card}, function (data) {
					if(data.stat) {
						Win.dialog({'msg':data.error, 'type':'alert'});
					}
					else show_answer_div(uid,id,div_id,div_from,div_content_id);
				}, 'json');
			}});
		}
		else if(data.stat) {
			Win.dialog({'msg':data.error, 'type':'alert'});
		}
		else show_answer_div(uid,id,div_id,div_from,div_content_id);
	}, 'json');
}
function show_answer_div(uid,id,div_id,div_from,div_content_id) 
{	
    var edit = show_edit(div_id+'_content');
	var sub_html = '<p class="clear"><span class="fl"><a onclick="submit_answer(0)" id="comment_button" class="btn1">帮忙</a></span><span class="fl"><img src="'+ version_img('ico_ce.gif') +'" alt="表情" onclick="face51New.show(this,\''+div_id+'_content\',\'_textarea\');" style="cursor:pointer;" /></span><label class="fl f_6" style="margin-left:10px;"><input type="checkbox" onclick="" value="1" class="checkbox1" id="anonymity" name="sync">匿名</label></p>';
	var comment_html = '<p class="top_img"><img src="'+ version_img('reaply_bg.gif') +'" alt="" /></p><div class="reply_box_bg">'+ edit +'</div>'+ sub_html +'<div id="divFace" style="display:none;"></div>';

	if(typeof myuserinfo != "object" || !myuserinfo.uid){
		 show_login_form();
		 return false ;
	}
	if(myuserinfo.uid == uid){
		Win.dialog({type:'info',msg:'不能帮忙自己的任务！'});
		return false;
	}

	if(current_comment_id>0)
	{
		$("#"+div_id+"_"+current_comment_uid+'_'+current_comment_id).slideUp("fast").html('');
		if(div_from == 'home'){
			$(".show_comment").html('');
		}
	}

	$("#"+div_id+"_"+uid+'_'+id).html(comment_html).slideDown("slow",function(){ $("#"+div_id+"_content").select().focus();$("#"+div_id+"_content").focus(); });
	current_comment_id = id;
	current_comment_uid = uid;
	current_comment_type = 14;
	current_comment_div = div_id;
    select_show = 0;
}

function hide_answer_form()
{
	if(current_comment_id>0)
	{
		$("#"+current_comment_div+"_"+current_comment_uid+'_'+current_comment_id).slideUp("fast").html('');
		current_comment_id = 0;
		current_comment_uid = 0;
		$("#comment_button").attr("disabled",false);
	}
}

function submit_answer(pay_card){
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
		Win.dialog({type:'info',msg:'不能帮忙自己的任务！'});
		return false;
	}

	if(myuserinfo.profile_completed < 0.5 && myuserinfo.login_times > 6){
		var msg_code = sysmessage_addprofile('reply',(myuserinfo.sex==1)?2:1) ;
		Win.dialog({width:460,msg:msg_code});
		return false ;
	}

    if (comment_content.length<1) {
        Win.dialog({type:'info',msg:'内容不能为空！'});
        return;
    }
	var is_anonymity = $('#anonymity').attr('checked') ? 1 : 0;

    $("#comment_button").attr("disabled",true);
    $.ajax({
	   type: "POST",
	   url: "/index.php?s=/task/answer_task/",
	   data: 'receiver_uid='+current_comment_uid+'&content='+encodeURIComponent(comment_content)+'&related='+related+'&is_anonymity='+is_anonymity+'&pay_card='+pay_card,
	   success: function comment_success(re)
				{
					$("#comment_button").attr("disabled",false);
					var result_obj = jQuery.parseJSON(re);
					switch(result_obj.stat)
					{
						case 0: 
							Win.dialog({type:'info',msg:'参与帮忙成功！'});
							hide_answer_form();
							break;
						default://
							Win.dialog({type:'info',msg:result_obj.error,cancel:function(){}});
							break;
					}
				}
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