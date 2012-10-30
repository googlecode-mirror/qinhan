var group_action={"add_group":function(e){

     if(typeof(e)=='undefined'){
        var e={'name':'请输入相册名称','content':'','per_type':0,'gid':0};
        var up_url='api_add_group';
     }else{
        var up_url='api_edit_group';
     }
     var html=group_action.shtml(e);
     Win.dialog({msg:html,width:455,height:325});
     $("._js_group_title").focus(function(){
         var title_val = $.trim($(this).val());
         if(title_val=="请输入相册名称"){
            $(this).val("");
            $(this).css({'color':'#000'});
         }
     });
    $("._js_group_title").blur(function(){
         var title_val = $.trim($(this).val());
         if(title_val==""){
            $(this).val("请输入相册名称");
            $(this).css({'color':'#666'});
         }
     });
     $("._js_content").bind("keyup change drop",function(){
        var _cstr = $("._js_content").val();
        var num = (166-_cstr.length);
        var _js_num_v1=$("._js_num_v1");
        var _js_num_v2=$("._js_num_v2");
         if(num<0)
        {
            _js_num_v1.hide();
            _js_num_v2.show();
            $("#_js_num_v2").html((0-num));
        }else{
            _js_num_v2.hide();
            _js_num_v1.show();
            $("#_js_num_v1").html(num);
        }
    });
     $("#_js_submit").click(function(){
        var return_str = true;
        var group_title = $.trim($("._js_group_title").val());
        var per_type = $.trim($("._js_per_type").val());
        var input_1  = $.trim($("._js_pass").val());
        var content  = $.trim($("._js_content").val());
        if(content.length>166){
            return false;
        }
        if(group_title=="请输入相册名称" || group_title.length<1 || group_title.length>15 ){
            if(group_title.length>15){
                $('#_js_error_title').html('请不要超过16个字符');
            }else{
                $('#_js_error_title').html('请输入相册名称');
            }
            $("._js_show_error").show();
            return_str = false;
        }else{
            $("._js_show_error").hide();
        }

        if(per_type==3 && (input_1=="请输入密码" || input_1.length<1) ){
            $('._js_show_pass_error').show();
            return_str = false;
        }else{
            $('._js_show_pass_error').hide();
        }
        if(return_str==true){
            $.post("/index.php?s=/photo/"+up_url,{'name':group_title,'per_type':per_type,'pass':input_1,'content':content,'gid':e.gid},
            function(data){if(data.status!=1){alert(data.msg);}else{
                if(up_url=='api_add_group'){
                    location.href='/index.php?s=/photo/up_form?gid='+data.msg;
                }else{
                    location.reload();
                }
            }},"json");
            Win.close();
        }
     });
     $('._js_per_type').change(function(){
        var per_type = $("._js_per_type").val();
        if(per_type==3){
            $("._js_show_pass").show();
        }else{
            $("._js_show_pass").hide();
        }
     });
    },"del_group":function(e){
        if(e.pass==1 || e.pass=='')
        {
           var re =group_action.check_second_password({'action':'del_group','gid':e.gid,'del_type':e.del_type,'content':e.content});
           return false;
        }
        var ret=confirm('确定将相册以及相册内所有图片删除?');
        if(ret)
        {
            $.post("/index.php?s=/photo/api_del_photo_bygroup",{'gid':e.gid,'del_type':e.del_type,'content':e.content},
                function(data){if(data.status!=1){alert(data.msg);}else{
                    location.reload();
                }},"json");
        }
    },"shtml":function(e){
        var groups= ['公开我的照片','我收藏的人','需要密码查看','我自己'];
		var group_num=[0,2,3,1];
        var showp;
        var i=0;
        if(e.per_type==3)
        {
            showp='style="display:block;"';
           var pass_title='修改密码';
        }else{
           var pass_title='';
           showp='style="display:none;"';
        }
        if(e.name=='请输入相册名称'){
            var name_title='创建相册';
            var action_button = '创建';
            var _css = 'style="color:#666666;"';
        }else{
            var name_title='修改相册';
            var action_button = '保存';
            var _css='';
        }
        var html ='<div class="set_album">'
              +'<h3>'+name_title+'</h3>'
              +'<dl class="m_t10 clear">'
              +'<dt>相册名称：</dt><dd>'
              +'<input type="text" dl="'+e.name+'" '+_css+' value="'+e.name+'" class="input_1 _js_group_title"/><br/><span class="f_r _js_show_error" style="display:none;"><img src="'+version_img('ico_alert.gif')+'" class="ico"><span id="_js_error_title">请输入相册名称</span></span></dd>'
              +'<dt>描述：</dt><dd><textarea name="content"  class="input_1 _js_content">'+e.content+'</textarea><br/><span class="f_9 _js_num_v1">最多可输入<span id="_js_num_v1">166</span>/166字</span><span class="f_r _js_num_v2" style="display:none;"><img src="'+version_img('ico_alert.gif')+'" class="ico">已超过<span id="_js_num_v2">166</span>字</span></dd>'
              +'</dl>'
              +'<p class="bot_btn"><a class="btn1" id="_js_submit">'+action_button+'</a> 或 <a href="javascript:void(0);" onclick="Win.close();">取消</a></p>'
              +'</div>'
              +'</div>';
            return html;
    },"change_order":function(e){
        if(e.type=='group')
        {
            var goto_url = 'api_edit_grouporder';
        }else{
            var goto_url = 'api_edit_photo_order';
        }
        $.post("/index.php?s=/photo/"+goto_url,{'now':e.now,'next':e.next,'now_or':e.now_or,'next_or':e.next_or,'type':e.type},
                function(data){if(data.status!=1){alert(data.msg);}else{
                    location.reload();
                }},"json");
    },"check_second_password":function check_second_password(e){
            var html='<div class="input_tpw">'
                    +'<h3>输入二级密码</h3>'
                    +'<p class="m_t10"><input type="password" size="20" class="input_1" id="_js_second_pass" value=""></p>'
                    +'<p class="m_t30"><a class="btn1 _js_submit">确定</a><a href="/user/second_password?type=1">忘记二级密码？</a></p>'
                    +'</div>';
            Win.dialog({msg:html,width:250,height:325});
            $('._js_submit').click(function(){
                var pass=$('#_js_second_pass').val();
                $.post("/index.php?s=/photo/api_check_pass",{'second_pass':pass},
                        function(data){
                            if(data.status!=1)
                            {
                                alert(data.msg);
                                return false;
                            }else{
                                if(typeof(e)=='undefined'){
                                    location.reload();
                                }else{
                                    if(e.action=='del_group')
                                    {
                                        group_action.del_group({'gid':e.gid,'del_type':e.del_type,'pass':2});
                                    }
                                    if(e.action=='del_photo')
                                    {
                                        photo_action.del_photo({'photo_id':e.photo_id,'gid':e.gid,'pass':2});
                                    }
                                    if(e.action=='del')
                                    {
                                        recover_action.del({'del_type':e.del_type,'gid':e.gid,'pass':2});
                                    }
                                }
                            }
                        },"json");
            });
    }
}
//相册加密的提示
function load_photo_group(uid,gid){
    if(uid<0 || gid <0){
        Win.dialog({type:'info',msg:'参数错误！'});
        return false;
    }
    if(myuserinfo.uid == uid){
        self.location.href='/index.php?s=/home/photo?uid='+uid+'&gid='+gid;
        return false;
    }
    $.post("/index.php?s=/photo/get_permission_type", { uid: uid, gid: gid}, function (data) {
            switch(data.stat){
                case -1:
                    Win.dialog({type:'info',msg:data.error});
                    return false;
                    break;
                case -2:
                    show_nophoto_tips('/index.php?s=/home/photo?uid='+uid+'&gid='+gid+'',''+data.ui_sex+'',''+data.is_mask+'',''+uid+'');
                    return false;
                    break;
                case 0:
                    self.location.href='/index.php?s=/home/photo?uid='+uid+'&gid='+gid;
                    break;
                case 1:
                    Win.dialog({type:'info',msg:data.error});
                    return false;
                    break;
                case 2:
                    Win.dialog({type:'info',msg:data.error});
                    return false;
                    break;
                case 3:
                    var msg ='<div class="input_pw fs_14">请输入密码：<input id="photo_pwd" name="photo_pwd" type="password" class="input_1"/><p class="m_t20 bot_btn"><a href="javascript:void(0);" class="btn1" onclick = "photo_pwd_submit('+uid+','+gid+',1)">确定</a><a href="javascript:void(0);" onclick="photo_pwd_submit('+uid+','+gid+',2)" class="btn2">询问</a></p></div></div>';
                    Win.dialog({msg:msg,width:400});
                    break;
            }
        }, 'json');
}
//验证相册密码
function photo_pwd_submit(uid,gid,type){
    if(uid<0 || gid <0 || type<0){
            Win.dialog({type:'info',msg:'参数错误！'});
            return false;
    }
    if(myuserinfo.uid == uid){
        self.location.href='/home/new_photo?uid='+uid+'&gid='+gid;
        return false;
    }
    if(type == 1){
        if($("#photo_pwd").val().trim().length <=0){
            Win.dialog({type:'info',msg:'请输入密码！'});
            return false;
        }
        var pwd = encodeURIComponent($("#photo_pwd").val());
    }
    $.post("/index.php?s=/photo/is_group_pwd/", { uid: uid, gid: gid,type:type,pwd:pwd}, function (data) {
            switch(data.stat){
                case -1:
                    Win.dialog({type:'info',msg:data.error});
                    return false;
                    break;
                case -2:
                    Win.dialog({type:'info',msg:data.error});
                    return false;
                    break;
                case -3:
                    var msg = '<div class="input_pw fs_14 ct"><p class="f_r"><img src="'+version_img("ico_alert.gif")+'" width="18" height="24" class="ico"/> 密码输入错误</p><p class="m_t20"><a class="btn1" onclick="restart_photo_pwd('+uid+','+gid+')">重输密码</a><a href="#" class="btn2" onclick ="photo_pwd_submit('+uid+','+gid+',2)">询问</a></p></div></div>';
                    Win.dialog({msg:msg,width:400});
                    break;
                case 1:
                    self.location.href='/home/new_photo?uid='+uid+'&gid='+gid+'&token='+data.token;
                    break;
                case 2:
                    Win.dialog({type:'info',msg:data.error});
                    return false;
                    break;
            }
        }, 'json');
}
function restart_photo_pwd(uid,gid){
    if(uid<0 || gid <0){
            Win.dialog({type:'info',msg:'参数错误！'});
            return false;
    }
      var msg ='<div class="input_pw fs_14">请输入密码：<input id="photo_pwd" name="photo_pwd" type="password" class="input_1"/><p class="m_t20 bot_btn"><a href="javascript:;" class="btn1" onclick = "photo_pwd_submit('+uid+','+gid+',1)">确定</a><a href="javascript:;" onclick="photo_pwd_submit('+uid+','+gid+',2)" class="btn2">询问</a></p></div></div>';
       Win.dialog({msg:msg,width:400});
}