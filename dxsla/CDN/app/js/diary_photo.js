var photo_action=
{
    "_overContent":function(e,photo_id){
        _overContent(e,photo_id);
    },"_outContent":function(e,photo_id){
        _outContent(e,photo_id);
    },"display_edit_content":function(e){
        display_edit_content(e);
    },"edit_content":function(photo_id,e){
        edit_content(photo_id,e);
    },"del_photo":function(e){
        del_photo(e);
    },"move_pre":function(e){
        move_pre(e);
    },"move_next":function(e){
        move_next(e);
    },"preview_diary":function(e){
        preview_diary(e);
    },"visible_choose_people":function(e){
        visible_choose_people(e);
    },"_overpointer":function(e){
        _overpointer(e);
    },"_outpointer":function(e){
        _outpointer(e);
    }
}

function _overpointer(obj)
{
    $(obj).find("a").show();
}

function _outpointer(obj)
{
    $(obj).find("a").hide();
}

//显示编辑的内容
function display_edit_content(obj)
{    
    var content = $(obj).find("a:eq(0)").attr("cc");
    if(!content){content = '';}
    var dl = $(obj).find("a:eq(0)").attr("dl");
    
    var html='<div class="photo_desc" style="padding-top:20px;"><h3>照片描述：</h3>'
            +'<textarea id="_js_content" class="input_1" name="content" maxlength="166">'+content+'</textarea>'
            +'</div>'
            +'<p style="padding:30px;padding-left:190px;" class="bot_btn"><a class="btn1" id="_js_submit" onclick="photo_action.edit_content('+dl+',this);">确定</a> 或 <a href="javascript:void(0);" onclick="Win.close();">取消</a></p>';
    Win.dialog({width:500,msg:html,cancel:function(){is_submit = true;}});

}

//编辑内容
function edit_content(photo_id,obj)
{
    var new_content;
    var photo_content_id = photo_id+"_content";
    var submit_content = $("#_js_content").val();
    if(submit_content.length >= 166)
    {
        alert('照片描述内容过多,最多输入166个字符');return false;
    }
    
    $("#"+photo_content_id).attr("cc",submit_content);
    
    new_content = get_defined_content(8,submit_content);
    $("#"+photo_content_id).html(new_content);
    
    Win.close();
}

//删除相册
function del_photo(obj)
{
    $(obj).parent().parent().remove();
}

//照片往上移动
function move_pre(obj)
{
    var _self_dl = $(obj).parent().parent();        
    _self_dl.find("dd:eq(1)").hide();
    _self_dl.prev().before(_self_dl);
    //alert(_self_dl.prev().html());
    //_self_dl.prev().find("dd:eq(1)").hide();
    //.find("a:eq(0)").attr("dl");
    //上一张的
}

//照片往下移动
function move_next(obj)
{
    var _self_dl = $(obj).parent().parent();
    _self_dl.find("dd:eq(1)").hide();
    _self_dl.next().after(_self_dl);
}

//获得已选好照片的数据
function get_already_dairy_data()
{
    var diary_photo_data = '';
    var img_num = $('#imgboxlist dl').length;
    
    if(img_num)
    {
        $('#imgboxlist dl').each(function(index){    
            var photo_id = $(this).children().children().children().children().attr("dl");
            var photo_url = $(this).children().children().children().children().attr("src");        
            var photo_content = $(this).find("dd:eq(0)").find("a:eq(0)").attr("cc");
            
            diary_photo_data+=photo_id+','+photo_url+','+photo_content+'|';
        });    
    }
    return diary_photo_data;
}


function preview_diary(obj)
{
    var diary_content = $('#content').html();
    var photo_id;
    var photo_url;
    var photo_content;
    
    
    var diary_photo_num = $('#imgboxlist dl').length;     //图片数量
    
    if(diary_photo_num)
    {
        diary_photo_data = get_already_dairy_data();
        
        $.ajax({
            type: "POST",
            url: "/index.php?s=/diary/preview_diary/",
            data: 'diary_content='+diary_content+'&diary_photo_data='+diary_photo_data,	
            success: function(re){ 
                is_submit = true;
                var obj = jQuery.parseJSON(re);
                if(obj.errno == 200) {
                     Win.dialog({'msg':obj.msg,'type':'content',width:580});
                }else{
                     Win.dialog({'msg':obj.msg,'type':'info'});
                }
            }
        });  
    }else{
         Win.dialog({'msg':'请添加图片后再预览','type':'info'});
    }
}



function _overContent(obj,photo_id)
{    
    var pre_photo_id_obj = photo_id + '_per';
    var next_photo_id_obj = photo_id + '_next';
    var del_id_obj = photo_id + '_del';
    var _the_pp;
    $(obj).attr("pp",1);
    var last_img_num = $('#imgboxlist dl').length - 1;
    $(obj).find("dd:eq(1)").show();
    
    $('#imgboxlist dl').each(function(index){
        //alert(index);
        if(index == 0)      //第一张图片
        {
            if($('#imgboxlist dl').length == 1)     //只有一张
            {
                if($(this).attr("pp") == 1)
                {
                    $('#'+pre_photo_id_obj).hide();
                    $('#'+next_photo_id_obj).hide();
                    $('#'+del_id_obj).show();
                }
            }else{                                  //一张以上
                if($(this).attr("pp") == 1)
                {
                    $('#'+pre_photo_id_obj).hide();
                    $('#'+next_photo_id_obj).show();
                    $('#'+del_id_obj).show();
                }            
            }
        }else if(index == last_img_num){    //最后一张
            if($(this).attr("pp") == 1)
            {
                $('#'+pre_photo_id_obj).show();
                $('#'+next_photo_id_obj).hide();            
                $('#'+del_id_obj).show();                     
            }
        }else{
            if($(this).attr("pp") == 1)
            {
                
                $('#'+pre_photo_id_obj).show();
                $('#'+next_photo_id_obj).show();
                $('#'+del_id_obj).show();                     
            }
        }
        
    });
}

function _outContent(obj,photo_id)
{
    var pre_photo_id_obj = photo_id + '_per';
    var next_photo_id_obj = photo_id + '_next';
    var del_id_obj = photo_id + '_del';
    
    $('#'+pre_photo_id_obj).hide();
    $('#'+next_photo_id_obj).hide();
    $('#'+del_id_obj).hide();   
    $(obj).attr("pp",0);
}


/*
    功能:显示相册
*/
function display_photo()
{
    var type = 0;           //初始化标志
	$.ajax({
		type: "POST",
		url: "/index.php?s=/diary/get_photo/",
		data: 'type='+type,	
		success: function(re){ 
            is_submit = true;
			var obj = jQuery.parseJSON(re);
			if(obj.errno == 200) {
				 Win.dialog({'msg':obj.msg,'type':'content',width:670,height:700});
                 setTimeout("_is_webup_status()",5000);
			}else{
				 Win.dialog({'msg':obj.msg,'type':'info'});
			}
		}
	});                    
}

/*
功能:翻页或者改变相册类型来改变相册内容
type    类型        1:改变相册类型        2:翻页
*/
function change_photo(type,page_num)
{
    var photo_group_id = $('#photo_g').val();
    if(type == 1)
    {
        var _page_num = $('#current_page').html();        
    }
    
    if(type == 2)
    {
       var _page_num = page_num;
    }
    
	$.ajax({
		type: "POST",
		url: "/index.php?s=/diary/get_photo/",
		data: 'photo_group_id='+photo_group_id+'&page_num='+_page_num+'&type='+type,	
		success: function(re){ 
            is_submit = true;
			var obj = jQuery.parseJSON(re);
			if(obj.errno == 200) {
                $('#photo_list').html(obj.photo_list);
                $('#photo_page').html(obj.photo_page);
                $('#already_choose_photo li').each(function(index) {                
                    dl = $(this).find("img").attr("dl");
                    var check_photo_id = 'check_'+dl;
                    if($('#'+check_photo_id))
                    {
                        $('#'+check_photo_id).attr("checked",true);                        
                    }                    
                });
			}else{
				Win.dialog({'msg':obj.msg,'type':'info'});
			}
		}
	});
}

/*
    获得照片选择是否是最大值
    true    未达到最大值
    flase   已达到最大值
*/
function get_check_max_photo()
{
    var photo_count = parseInt($('#photo_count').html());
    var already_choose_photo_num = parseInt($('#imgboxlist dl').length);    
    var new_photo_count = photo_count + already_choose_photo_num;

    if(new_photo_count >=10)
    {    
        alert('最多只能插入10张图片！');
        return false;
    }else{
        return true;
    }        
}

/*
    功能:选中和不选中相册
    type    1:checkbox操作    2:图片外框操作    
*/
function check_photo(type,obj)
{
    var check_photo_html;    
    
    if(type == 1)
    {
        var is_check_flag = $(obj).attr("checked");
        var photo_id = $(obj).val();
        var photo_src = $(obj).parent().parent().parent().find("span:eq(1)").children().children().children().attr("src");
        
        if(is_check_flag == "checked")
        {         
            var check_photo_max_flag = get_check_max_photo();
            if(check_photo_max_flag == true)
            {        
                check_photo_html = '<li><a class="closeimg" onclick="cancel_photo(this,'+photo_id+');"></a><img src='+photo_src+' dl='+photo_id+'></li>';
                    $('#already_choose_photo').append(check_photo_html);
            }else{
                $(obj).attr("checked",false);
            }
        }else{
            $('#already_choose_photo li').each(function(index) {
                dl = $(this).find("img").attr("dl");
                
                if(photo_id == dl)
                {
                    $(this).remove();
                }
            });                            
        }
    }
    
    if(type == 2)
    {
        var is_check_flag = $(obj).parent().children().children().children().attr("checked");
        var dl;
        var photo_id = $(obj).parent().children().children().children().val();
        var photo_src = $(obj).children().children().children().attr("src");
        
        if(is_check_flag == "checked")
        {        
            
            $(obj).parent().children().children().children().attr("checked",false);
            
            $('#already_choose_photo li').each(function(index) {
                dl = $(this).find("img").attr("dl");
                if(photo_id == dl)
                {
                    $(this).remove();
                }
            });
        }else{
            var check_photo_max_flag = get_check_max_photo();
            if(check_photo_max_flag == true)
            {
                $(obj).parent().children().children().children().attr("checked",true);
                
                check_photo_html = '<li onmouseover="photo_action._overpointer(this);" onmouseout="photo_action._outpointer(this);"><a class="closeimg" onclick="cancel_photo(this,'+photo_id+');" style="display:none;"></a><img src='+photo_src+' dl='+photo_id+'></li>';                
                $('#already_choose_photo').append(check_photo_html);
            }
        }    
    }
    
    display_photo_count();

}

//取消图片
function cancel_photo(obj,photo_id)
{
    $(obj).parent().remove();
    var check_id = 'check_'+photo_id;
    $('#'+check_id).attr("checked",false);
    
    display_photo_count();
}

function display_photo_count()
{
    var photo_count = $('#already_choose_photo li').length;
    $('#photo_count').html(photo_count);
}

//插入图片到日记里
function add_photo()
{
    var already_choose_photo_num = $('#already_choose_photo li').length;    

    if(already_choose_photo_num)    
    {
        Win.close();    
        var insert_photo_data = get_insert_photo_data();        
        $('#instertimgbox').css("display","block");
    }else{
        alert('请选择要插入的图片');return false;
    }
}


//获取要插入的数据,并把它拼成插入的格
function get_insert_photo_data()
{
    var dl;
    var img_src;
    var insert_photo_data_html = '';
    
    $('#already_choose_photo li').each(function(index) {
        dl = $(this).find("img").attr("dl");
        img_src = $(this).find("img").attr("src");
        
        insert_photo_data_html+= '<dl onmouseover="photo_action._overContent(this,'+dl+');" onmouseout="_outContent(this,'+dl+');" pp="0"><dt><p class="photo_m"><a><img src="'+img_src+'" title="照片描述" dl='+dl+'></a></p><dd class="clear eite" style="cursor:pointer;" onclick="photo_action.display_edit_content(this);"><a class="f_9 fl" cc="" id="'+dl+'_content" dl='+dl+'>暂无描述</a><a class="fr"><img src="' + version_img('ico_photowrite.png') + '" alt="编辑"></a></dd><dd><a onclick="photo_action.move_pre(this);" id="'+dl+'_per" style="display:none;">上移</a> <a onclick="photo_action.move_next(this);" id="'+dl+'_next" style="display:none;">下移</a> <a onclick="photo_action.del_photo(this);" id="'+dl+'_del" style="display:none;">删除</a></dd></dt></dl>';
    });
    
    $('#imgboxlist').append(insert_photo_data_html);
}

//切换
function current_tab(num)
{        
    var _tab_1 = 'tab_1';
    var _tab_2 = 'tab_2';
    
    //照片相册
    if(num == 2)
    {
        $('#'+_tab_2).removeClass("menu_tab2").addClass("menu_tab1");
        $('#'+_tab_1).removeClass("menu_tab1").addClass("menu_tab2");
        $('#photo_content').show();
        $('#photo_flash').hide();
    }
    
    //取flash
    if(num == 1)
    {
        $('#'+_tab_1).removeClass("menu_tab2").addClass("menu_tab1");
        $('#'+_tab_2).removeClass("menu_tab1").addClass("menu_tab2");   
        $('#photo_flash').show();
        $('#photo_content').hide(); 

        if(_is_webup == false)
        {                
            _is_webup_status_act();
        }
    }        
}

//显示好友选择
function visible_choose_people(obj)
{
    $.ajax({
        type: "POST",
        url: "/index.php?s=/diary/visible_choose_people/",
        //data: 'diary_content='+diary_content+'&diary_photo_data='+diary_photo_data,	
        success: function(re){ 
            is_submit = true;
            var obj = jQuery.parseJSON(re);
            if(obj.errno == 200) {
                 Win.dialog({'msg':obj.msg,'type':'content',width:480,height:300});
            }else{
                 Win.dialog({'msg':obj.msg,'type':'info'});
            }
        }
    });  
}