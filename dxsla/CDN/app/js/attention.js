/**
 * 动态JS类库
 */

// 动态主页载入
$(document).ready(function() {
	
	//var answers = <?=json_encode($questions)?>;
	if(Cookies.get("locat_pay_card_"+myuserinfo.uid) == 1 && Cookies.get("locat_is_pay_"+myuserinfo.uid) == 1 && myuserinfo.card_num > 30) {
		show_mask_info(2,0);
	}
	Cookies.clear("locat_pay_card_"+myuserinfo.uid);
	Cookies.clear("locat_pay_uid");
	Cookies.clear("locat_is_pay_"+myuserinfo.uid);
});

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

function scaleImg2(id,img)
{
    bid = 'imgbig2_'+id;
    sid = 'imgsmall2_'+id;
    if(img){
        $('#'+bid).attr('src',img);
        $('#'+bid).show();
        $('#'+sid).hide();
    }else{
        $('#'+bid).hide();
        $('#'+sid).show();
    }
}

function change_skill_box(attention_uid,skill_id,type)
{
    $("[name="+attention_uid+"]").removeClass("bg_blueclor");
    $("#"+attention_uid+"_"+skill_id).addClass("bg_blueclor");
    show_comment_form(attention_uid,skill_id,type);
}

// 日记评论 
function praise_diary(uid,d_id,sex,type)
{
	if(type == 0) {
		if(sex == 1) {
		  var act = '不能给自己叶子！';
		  var act1 = '同性之间不能送叶子！';
		}else{
		  var act = '不能给自己花！';
		  var act1 = '同性之间不能送花！';
		}
	}else{
		var act = '不能拍自己！';
		var act1 = '同性之间不能拍砖！';
	}
	 
	if(myuserinfo.uid == uid){
		Win.dialog({type:'info',msg:act});
		return false;
	}
	if(myuserinfo.sex == sex){
		 Win.dialog({type:'info',msg:act1});
		return false;
	}
	$.post('/index.php?s=/diary/praise', {uid:uid, diary_id:d_id, type:type}, function(data) {
		if(data.errno == 200){
			if(type == 1){
				var diary_path = version_img("ico_brick.gif");
			}else{
				 if(sex  ==1){
				  var diary_path = version_img("ico_diay1.gif");
				}else{
				  var diary_path = version_img("ico_diay.gif");
				}
			}

			$("#praise_"+d_id).html(' <img class="ico" src="'+diary_path+'">');
		}
		if(data.errno == 500){
			Win.dialog({type:'info',msg:data.msg});
			return false;
		}
		return false;
	},'json');
} 

// 动态广场查看更多
function load_more()
{
	$.ajax({
        type: "POST",
        url: "/index.php?s=/attention/load_more/",
        data: 'is_last='+is_last+'&last_id='+last_id+'&page='+limit+'&face_size='+face_size,
        success: function(re){ 
            var obj = jQuery.parseJSON(re);
            if(obj.errno == 200) {
				if(obj.show_member_tips == 1) {
					if(myuserinfo.sex==1) {
						if(myuserinfo.is_member == 1) {
							if(obj.show_member_tips_ssesion != 1) {
								var msg_code = '3页以后，只允许正式会员查看';
								Win.dialog({type:'info',msg:msg_code,width:450, enterName:'我是正式会员'});
							}
						}

						if(myuserinfo.is_member==0) {
							var msg_code = '<div class="man_invitefriends f_3"><h3 class="m_15">正式会员，才能继续翻页浏览</h3><p class="m_t5">方法一：<a href="/pay/order/?id=2" class="underline">十元升级正式会员</a></p><p class="m_t10">方法二：<span>发送以下注册链接给<img src="'+ version_img('1.png')+ '" align="absmiddle">位好友成功加入，即可成为正式会员</span></p><p class="m_t5"><input type="text" class="input_1 fl" value="http://jianjiandandan.ivu1314.com/?iv='+myuserinfo.uid+'"/><span id="clipinner"><object width="50" height="28" id="copyObjIE" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"><param value="always" name="allowScriptAccess"><param value="transparent" name="wmode"><param value="/i/clipboard.swf?1310729489" name="movie"><param value="high" name="quality"><embed  id="copyObj" width="50" height="28" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" allowscriptaccess="sameDomain" swliveconnect="true" quality="high" wmode="transparent" src="/i/clipboard.swf?1310729489"></object></span></p><p style="color:red;">您邀请的好友有客服严格审核，虚假注册不予通过，并停用您的帐号</p></div>';
							Win.dialog({msg:msg_code,width:450});
							return false;
						}
					}
					else if(myuserinfo.is_videoauth==0) {
							var msg_code = '通过视频认证后，才能继续翻页浏览。' ;
							Win.dialog({type:'info',msg:msg_code,width:450,enterName:'快速申请认证！',enter:function(){self.location.href='/videoauth/'}});
							return false;
					}
				}

				$("#attention_more").append(obj.more);
				is_last = obj.is_last;
				last_id = obj.last_id;
                limit = limit+1;
				
				if(limit > 10) {
					$("#load_more_dt").hide();
				}
				if(obj.is_new_dt == 1) {
					$("#reload_more_attention").show();
				}
            }else{
                $("#load_more_dt").hide();
            }
        }
    });
}

// 个人动态查看更多
function load_index()
{
	$.ajax({
        type: "POST",
        url: "/index.php?s=/attention/load_index/",
        data: 'page='+limit+'&face_size='+face_size,
        success: function(re){ 
            var obj = jQuery.parseJSON(re);
            if(obj.errno == 200) {	
				$("#attention_more").append(obj.more);
                limit = obj.page+1;
				
				if(limit > 10) {
					$("#load_more_dt").hide();
				}
				if(obj.is_new_dt == 1) {
					$("#reload_more_attention").show();
				}
            }else{
                $("#load_more_dt").hide();
            }
        }
    });
}

// 隐藏动态类型信息
function hide_attention(type,uid,data_id,flag,obj)
{
	if ($('.'+type+'_'+uid+'_'+flag).length > 0) {
		if (type == 'question') {
			$('.myfrend_'+uid+'_'+flag).hide();
		} else {
			$('.reply_'+uid+'_'+flag).hide();
		}
		$('.'+type+'_'+uid+'_'+flag).hide();
		$(obj).parent().html('<a onclick="load_attention(\''+type+'\',\''+uid+'\',\''+data_id+'\',\''+flag+'\',this);">查看更多</a>');
	}
}

// 查看动态类型信息
function load_attention(type,uid,data_id,flag,obj)
{
	if (type == null || uid == null || data_id == null) return false;

	if ($('.'+type+'_'+uid+'_'+flag).length > 0 ) {
		$('.'+type+'_'+uid+'_'+flag).show();
		$(obj).parent().html('<a onclick="hide_attention(\''+type+'\',\''+uid+'\',\''+data_id+'\',\''+flag+'\',this);">收起</a>');
	} else {
		$.ajax({
			type: "POST",
			url: "/index.php?s=/attention/load_attention",
			data: 'type='+type+'&uid='+uid+'&data_id='+data_id+'&flag='+flag,
			success: function(re) {
				var res = jQuery.parseJSON(re);
				if (res.errno == 200) {
					//$(obj).parent().prepend(res.more);	
					$(obj).parent().before(res.more);	
					$(obj).parent().html('<a onclick="hide_attention(\''+type+'\',\''+uid+'\',\''+data_id+'\',\''+flag+'\',this);">收起</a>');
				} else {
                    Win.dialog({type:'info',msg:'没有查询到数据!'});			
				}
			}
		});
	}
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

function get_copy_str()
{
    return 'http://jianjiandandan.ivu1314.com/?iv='+myuserinfo.uid;
}

function copy_finish()
{
    alert('邀请链接地址复制成功\n您可以在QQ、MSN上粘贴发送给您的朋友！'); 
}
