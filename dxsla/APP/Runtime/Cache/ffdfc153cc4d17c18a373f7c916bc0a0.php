<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>大学生恋爱网 一个大学生认识新同学、交友的网站</title>
<meta content="大学生恋爱网，恋爱网，大学生恋爱，大学生谈恋爱，大学生爱情，大学生交友，校园交友，校园恋爱，高校交友，大学生恋爱观，大学生爱情观，大学生恋爱心理，大学生情书，大学生爱情故事" name="keywords" />
<meta content="大学生恋爱网，大学生交友的网站，大学校园里的真实照片的匿名交友，除了上QQ空间你还可以玩的网站。" name="description" />
<link href="<?php echo ($urlstatic2); ?>/css/head_global_main_ask.css<?php echo ($urltail); ?>" rel="stylesheet" type="text/css" />
<script language="javascript" src="<?php echo ($urlstatic2); ?>/js/global_jquery_hello_dialog_chat.js<?php echo ($urltail); ?>"></script>

<link href="<?php echo ($urlstatic2); ?>/css/newindex.css?gv=220_1" rel="stylesheet" type="text/css" />
<link href="<?php echo ($urlstatic2); ?>/css/login.css?gv=220_1" rel="stylesheet" type="text/css" />
<style type="text/css">
html {overflow-x: hidden;}
body {_overflow-x:hidden;}
.friend_link {margin:30px 0 -25px 20px; color:#999;}
.friend_link a {color:#999; text-decoration:none; margin-right:8px;}
.friend_link a:hover {text-decoration:underline;}
</style>
</head>
<body class="page_main clip_wrap">

<?php if(empty($GLOBALS['islogin'])): ?><!--header start-->
<div class="headerbox" style="background:black;">  
  <div class="header clear">
	<div id="list_con" class="fl header_l">
	  <ul id="news_list" class="list">
	  <li><a style="color:#ffffff" target="_blank" href="/blog/?p=23">大学生的恋爱心理</a></li>
	  <li><a style="color:#ffffff" target="_blank" href="/blog/?p=20">树立正确的恋爱观</a></li>
	  <li><a style="color:#ffffff" target="_blank" href="/blog/?p=16">当代大学生的爱情观</a></li>
	  <li><a style="color:#ffffff" target="_blank" href="/blog/?p=12">教你谈恋爱</a></li>
	  <li><a style="color:#ffffff" target="_blank" href="/blog/?p=10">社交礼仪常识</a></li>
	  <li><a style="color:#ffffff" target="_blank" href="/blog/?p=7">一封千古绝唱的情书</a></li>
	  <li><a style="color:#ffffff" target="_blank" href="/blog/?p=1">感人的爱情故事</a></li>	  
	</div>
	
    <div class="fr header_r"><a class="hovf60" href="javascript:;" onclick="addFavorite()">加入收藏</a><a class="hovf60" href="<?php echo ($urlsite); ?>/member/register/">免费注册</a><a class="ico_img2 hovf60" href="<?php echo ($urlsite); ?>/member/login/" style="padding-left:20px;">登录</a><a class="ico_img3 hovf60" href="<?php echo ($urlsite); ?>/user/signqq/" style="padding-left:20px;">QQ登录</a></div>
  </div>
</div>
<!--header end-->
<div class="wrap">
<div class="head clear">
  <h1 class="newlogo fl clear"><a href=/ class="g_logo_a fl"><img src="<?php echo ($urlstatic); ?>/img/logo.gif" alt="大学生恋爱网" title="大学生恋爱网" /></a></h1>
<script type="text/javascript">
var gShowLog = 0;
var g_chat_msg = 0;
var gGetLoseMsg2 = 0;

var j = 7;
var margin_top = 0;
var timer = null;
var scroll_news = function() {
	clearTimeout(timer);
	$('#news_list').animate({'marginTop':-margin_top},1000,function(){
		if(margin_top == j*30){
			$(this).css({'margin-top':'0px'});
			margin_top = 0;
		}
		margin_top += 30;
		timer = setTimeout(scroll_news, 3000);
	});
};
$('#list_con').append($("#news_list").clone());
timer = setTimeout(scroll_news, 3000);
</script>
</div>
<?php else: ?>
<script type="text/javascript">
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
      </span><a class="hovf60" href="javascript:;" onclick="addFavorite()">加入收藏</a> <a class="hovf60" href=/>首页</a> <a class="hovf60 p_l10" href="<?php echo ($urldomain); ?>/<?php echo ($GLOBALS['i']['uid']); ?>" target="_blank">我的主页</a> <!--<a class="prop" id="prop_link" href="javascript:void(0);">道具礼品</a>--> <a class="prop" id="admprop_link" href="javascript:void(0);" style="background-image:url(<?php echo ($urlstatic); ?>/img/top_arrow1.png)">我的管理</a> <a class="hovf60" href="<?php echo ($urlsite); ?>/member/logout">退出</a> </div>
    <!-- 道具礼品下拉选项 start-->
    <div class="propbox" id="propbox" style="">
      <p><a href="<?php echo ($urlsite); ?>/goods">道具礼品中心</a></p>
      <p><a href="<?php echo ($urlsite); ?>/goods/my">我的小仓库</a></p>
      <p><a href="<?php echo ($urlsite); ?>/goods/log/?uid=0">历史记录</a></p>
    </div>
    <!--道具礼品下拉选项 end-->
    <!-- 我的管理下拉选项 start-->
    <div class="admbox" id="admbox" style="">
      <p class="clear"><a href="<?php echo ($urlsite); ?>/profile/"><span class="ico_in"></span><span class="fl">基本资料</span></a></p>
      <P><a href="<?php echo ($urlsite); ?>/user/password/"><span class="ico_set"></span><span class="fl">账号设置</span></a></P>
      <P><a href="<?php echo ($urlsite); ?>/pay/card_log/?type=link"><span class="ico_redbean"></span><span class="fl">红豆账户</span></a></P>
    </div>
    <!--我的管理下拉选项 end-->
  </div>
</div>
<!--header end-->
<script type="text/javascript">
$(function(){
    //搜索框获得焦点时
    $('.top_search').focus(function(){
        if(/^d+$/.test($(this)[0].value) == false){
            $(this)[0].value = '';
        }
    });
    //搜索框失去焦点时
    $('.top_search').blur(function(){
        if(/^s*$/.test($(this)[0].value)){
            $(this)[0].value = "搜索ID找人";
        }
    });
    
    function search_logic(obj){
        if(/^d+$/.test(obj.value) == false){
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
  <h1 class="newlogo fl clear"> <a href=/ class="g_logo_a fl"><img src="<?php echo ($urlstatic); ?>/img/logo.gif?gv=88_1" alt="大学生恋爱网" title="大学生恋爱网" /></a></h1>
  <script>
            var gShowLog = 0;
            var g_chat_msg = 0;
			var gGetLoseMsg2 = 0;
        </script>
  <div class="g_nav"> </div>
  <script type="text/javascript">
            //var is_more_open = false;
            //鼠标滑过事件
            $('#menu_more_link').mouseover(function(){
				$(this).css({'background':'url(<?php echo $urlstatic; ?>/img/menu_morebg.png?gv=88_1) no-repeat scroll 0 0 transparent'});
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
      <param name="bgcolor"  />
      <param name="wmode" value="transparent" />
    </object>
  </div>
</div><?php endif; ?>
<script type="text/javascript">
function check_reg(f) {
	var msg = '', enter = null;
	if($("#username2").val() == '') {
		msg = '用户名未填';
		enter = function() { $("#username2").select() };
	} else if($("#password2").val() == '') {
		msg = '密码未填';
		enter = function() { $("#password2").select() };
	} else if($("#password2").val() != $("#rpassword").val()) {
		msg = '密码不一致';
	}
	if(msg) {
		Win.dialog({msg:msg, type:'info', enter:enter});
		return false;	
	}
   	$.ajax({
	   type: "POST",
	   url: "/index.php?s=/member/register/",
	   data: 'username=' + $('#username2').val() + '&password=' + $('#password2').val(),
	   success: function show_desc_result(re)
	   {
	   		var obj = jQuery.parseJSON(re);
	   		if(obj.errno != 200) {
				//$("#msg").html(obj.msg);
				var enter = function() { $("#username2").select() };
				Win.dialog({msg:obj.msg, type:'info', enter:enter});
			} else {
				location.href = '<?php echo $urlsite; ?>/member/reg_active/';
			}
	   }
	});	
	return false;
}
function check_login(f) {
	var msg = '', enter = null;
	if($("#username").val() == '') {
		msg = '用户名未填';
		enter = function() { $("#username").select() };
	} else if($("#password").val() == '') {
		msg = '密码未填';
		enter = function() { $("#password").select() };
	}
	if(msg) {
		Win.dialog({msg:msg, type:'info', enter:enter});
		return false;	
	}
   	$.ajax({
	   type: "POST",
	   url: "/index.php?s=/member/login/",
	   data: 'username=' + $('#username').val() + '&password=' + $('#password').val(),
	   success: function show_desc_result(re)
	   {
	   		var obj = jQuery.parseJSON(re);
	   		if(obj.errno != 200) {
				Win.dialog({msg:obj.msg, type:'info'});
			} else {
				location.href = '/';
			}
	   }
	});	
	return false;
}
</script> 
  <div id="wrap1">
    <?php $guest_name = cookie('username'); ?>
    <!--<div style="position:absolute; top:-80px; left:420px;"><a <?php if(empty($guest_name)): ?>onclick="show_guest_form();return false;"<?php endif; ?> href="<?php echo ($urldomain); ?>/guest/xiaoyuannannv/" title="校园男女" alt="校园男女"><img src="<?php echo ($urlstatic); ?>/img/up_icon.png" /></a></div>-->
    <div class="mp_mosaic">
      <div class="mp_canvas" id="mp_canvas">
        <?php echo ($mosaic); ?>
        <!--登录框开始-->
        <div class="mp_sg mp_sgin">
          <ul class="mp_sg_ul clear">
		  	<li class="upload_pic_btn1" id="upload_show_btn" onclick="change_show(1)"><a href="javascript:;">免费注册</a></li>
            <li class="login_btn1" id="login_show_btn" onclick="change_show(2)"><a href="javascript:;" class="login_btn_text">已有账号登录</a></li>
          </ul>
		  <div id="upload_show" class="mp_sg_cont" style="display:none;">
            <div class="login_form">
              <form id="regform" name="loginform" method="post" action="<?php echo ($urlsite); ?>/member/register/" onsubmit="return check_reg()">
                <div class="set_box">
                  <div id="msg" class="item3"></div>
                  <div class="set_box_main">
                    <div class="clear"> <b class="fl">用户名：</b>
                      <div class="fl">
                        <input type="text" class="inputbg2" value="" maxlength="60" name="username2" id="username2" />
                      </div>
                    </div>
                    <div id="div_pass" class="clear m_t30"> <b class="fl">密码：</b>
                      <div class="fl">
                        <input type="password" maxlength="32" id="password2" name="password2" />
                      </div>
                    </div>
					<div id="div_pass" class="clear m_t30"> <b class="fl">重复密码：</b>
                      <div class="fl">
                        <input type="password" maxlength="32" id="rpassword" name="rpassword" />
                      </div>
                    </div>
                  </div>
                  <div class="list2 clear"> <span class="fl">
                    <input type="submit" class="btn1" value="注册" />
                    </span> <a href="javascript:;" onclick="change_show(2)" class="f1 fs_12">已有账号登录</a> </div>
                </div>
              </form>
            </div>
			<div class="login_line"></div>
            <div class="login_text1 m_t20"><a href="<?php echo ($urlsite); ?>/user/signqq"><img src="<?php echo ($urlstatic); ?>/img/index_signqq.png" /></a></div>
		  </div>
          <div id="login_show" class="mp_sg_cont">
            <div class="login_form">
              <form id="loginform" name="loginform" method="post" action="<?php echo ($urlsite); ?>/member/login/" onsubmit="return check_login()">
                <div class="set_box">
                  <div id="msg" class="item3"></div>
                  <div class="set_box_main">
                    <div class="clear"> <b class="fl"> 帐号：</b>
                      <div class="fl">
                        <input type="text" onblur="if($('#username').val()==''){ $('#username').val('请输入用户名'); };$('#username').css('color','#999');" onfocus="if($('#username').val()=='请输入用户名'){ $('#username').val(''); };$('#username').css('color','#000');" class="inputbg2" value="" maxlength="60" name="username" id="username">
                      </div>
                    </div>
                    <div id="div_pass" class="clear m_t30"> <b class="fl">密码：</b>
                      <div class="fl">
                        <input type="password" onblur="$('#password').css('color','#999');" onfocus="$('#password').css('color','#000');" onkeydown="if(event.keyCode==13 &amp;&amp; login_check()){ $('#loginform').submit(); }" class="inputbg2" maxlength="32" id="password" name="password">
                      </div>
                    </div>
                  </div>
                  <div id="div_box" class="clear checkbox_login m_b25"  style="margin-top:10px;">
                    <label>
                    <input type="checkbox" name="auto_login" id="auto_login" value="1" checked="checked" />
                    在此电脑上记住我</label>
                  </div>
                  <div class="list2 clear"> <span class="fl">
                    <input type="submit" class="btn1" value="登录" />
                    </span> <a href="javascript:;" onclick="change_show(1)" class="f1 fs_12">免费注册</a> </div>
                </div>
              </form>
            </div>
            <div class="login_line"></div>
            <div class="login_text1 m_t20"><a href="<?php echo ($urlsite); ?>/user/signqq"><img src="<?php echo ($urlstatic); ?>/img/index_signqq.png" title="大学生恋爱网QQ登录" alt="大学生恋爱网QQ登录" /></a></div>
          </div>
        </div>
        <!--登录框结束-->
      </div>
    </div>
    <div class="container_cwrap mp_ma">
      <div class="mp_sl">
        <div class="mp_sl_h">真实照片的匿名交友，同校交友！</div>
        <p class="mp_t">大学校园的轻型交友平台，加入后您可以给他人评分，搭讪，发布校园任务等等！</p>
      </div>
      <div class="mp_cnt mp_t" id="counter">
        <!--<div class="mp_cnt_num"><?php echo ($counter); ?></div>
        已经有这么多人在这里！ -->
		<a style="margin:20px 0 0 110px; display:block;" <?php if(empty($guest_name)): ?>onclick="show_guest_form();return false;"<?php endif; ?> href="<?php echo ($urldomain); ?>/guest/xiaoyuannannv/" title="校园男女" alt="校园男女"><img src="<?php echo ($urlstatic); ?>/img/up_icon.png" /></a>
<br />	
<!-- Baidu Button BEGIN -->
    <div id="bdshare" class="bdshare_t bds_tools get-codes-bdshare" style="margin-left:100px;">
        <span class="bds_more">分享到：</span>
        <a class="bds_qzone"></a>
        <a class="bds_tsina"></a>
        <a class="bds_baidu"></a>
		<a class="shareCount"></a>
    </div>
<script type="text/javascript" id="bdshare_js" data="type=tools&amp;uid=658305" ></script>
<script type="text/javascript" id="bdshell_js"></script>
<script type="text/javascript">
	document.getElementById("bdshell_js").src = "http://bdimg.share.baidu.com/static/js/shell_v2.js?cdnversion=" + new Date().getHours();
</script>
<!-- Baidu Button END -->
		
	  </div>
    </div>
  </div>
  <div class="bp_tt" id="pop_tips">
    <div class="pngbox bp_ttp">
      <div class="tpt" id="pop_tips_info">
        <div class="bpt"></div>
      </div>
      <div class="tcn"></div>
      <div class="bcn"></div>
    </div>
    <div class="pngbox bp_ttp_tail"></div>
  </div>

<div class="friend_link">友情链接: <a href="http://jianjiandandan.ivu1314.com/">大学生恋爱网</a><a href="http://www.php163.com/" target="_blank">PHP技术分享网站</a><a href="http://aizaoqi.com/" target="_blank">爱早起</a><a href="http://www.jhun.net" target="_blank">江汉大学论坛</a><a href="http://www.mycugb.com" target="_blank">中国地质大学BBS</a><a href="http://www.hustky.com/forum.php" target="_blank">华中科技大学考研论坛</a></div>

<div id="login_form" style="display:none;">
  <div class="pop_login">
    <h1>童鞋，打酱油也得留个名啊</h1>
    <div class="login_main">
        <p><label class="zd_w">游客名</label><input name="guest_accout" id="guest_accout" type="text" size="30" class="login_input" value="" /></p>
		<p id="login_msg" class="f_r1"></p>
        <p class="login_buttom clear"><input name="submit" type="submit"  value="开始浏览" class="btn1 btn_b1 fl" onclick="popGuest()" /></p>
    </div>
    <div class="p_t10"></div>
  </div>
</div>
<script type="text/javascript">
function change_show(code){
    if(code==1){
        $("#upload_show").show();
        $("#login_show").hide();
        $("#login_show_btn").attr("class","login_btn");	
        $("#upload_show_btn").attr("class","upload_pic_btn");
    }else if(code==2){
        $("#login_show").show();
        $("#upload_show").hide();
        $("#login_show_btn").attr("class","login_btn1");	
        $("#upload_show_btn").attr("class","upload_pic_btn1");	
    }
}

function show_user_tips(u_index,u){
    var newer_sex= '女';
    if(u.sex==2){
        newer_sex= '男';
    }
    var movex = -60;
    var movey = 100
    if(u_index<=4){
        movex = 0;
    }else if (u_index<=16){
        movex = -30;
        movey = 90;
    }else if (u_index<=27){
        movex = -45;
        movey = 80;
    }
	$("#img_0_"+u_index).mouseout(function(){ $("#pop_tips").hide(); });
    var pop_left = $("#img_0_"+u_index).offset().left+movex;
    var pop_top = $("#img_0_"+u_index).offset().top+movey;
	u.want_content = u.want_content ? u.want_content : '一起告别单身'
    var tip_userinfo = '<div class="bpt">'+
                        '<div id="tc_0_'+u_index+'" class="cont">'+
                            '<div class="bp_ui"><a  href="/'+u.uid+'" target="_blank" class="bp_un"  >'+u.username+'</a>, '+u.college+'</div>'+
                            '<p>想和一个'+newer_sex+'生,'+u.want_content+'</p>'+
                            '<p class="bp_us"><span class="bp_us_mark">'+u.score_impress+'分</span><small class="bp_uss">'+u.photonum+'&nbsp;照片</small></p>'+
                        '</div>'+
                        '<div class="bpc"></div>';
    $("#pop_tips_info").html(tip_userinfo);   
    $("#pop_tips").css({"display":"block","left":pop_left,"top":pop_top});
    
}
function show_guest_form()
{
	Win.dialog({ msg:'#login_form',width:360,height:130 });
}
function popGuest()
{
	if($('#guest_accout').val() == '') {
		$("#login_msg").html('请输入名字');
		return false;
	}
	
	$.ajax({
	   type: "POST",
	   url: "/index.php?s=/member/chk_username/",
	   data: 'username=' + $('#guest_accout').val(),
	   success: function show_desc_result(re)
	   {
	   		var obj = jQuery.parseJSON(re);
	   		if(obj.errno != 200) {
				//alert($('#login_msg'));
				$("#login_msg").html('这个名字没啥个性，已经有人用了，换个吧');
			} else {
				location.href = '<?php echo $urldomain; ?>/guest/xiaoyuannannv/';
			}
	   }
	});
	
	return false;
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