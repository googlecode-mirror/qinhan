
var gIsIE = document.all;
var currDivId="";
var e_isIE = document.all ? true : false;
var cur_input_obj = '';
var face_start=0;
var face_end=0;

function savePos(textBox){
	if(typeof(textBox.selectionStart) == "number"){
		face_start = textBox.selectionStart;
		face_end = textBox.selectionEnd;
	}
	else if(document.selection){
		var range = document.selection.createRange();
		if(range.parentElement().id == textBox.id){
			var range_all = document.body.createTextRange();
			range_all.moveToElementText(textBox);
			for (face_start=0; range_all.compareEndPoints("StartToStart", range) < 0; face_start++)
			range_all.moveStart('character', 1);
			for (var i = 0; i <= face_start; i ++){
				if (textBox.value.charAt(i) == '\n')
				face_start++;
			}
			var range_all = document.body.createTextRange();
			range_all.moveToElementText(textBox);
			for (face_end = 0; range_all.compareEndPoints('StartToEnd', range) < 0; face_end ++)
			range_all.moveStart('character', 1);
			for (var i = 0; i <= face_end; i ++){
				if (textBox.value.charAt(i) == '\n')
				face_end ++;
			}
		}
	}
}


/**
 * 判断el对象是否在另一个节点里
 */
function fInObj(el, id){
	if(el){
		if(el.id == id){
			return true;
		}else{
			if(el.parentNode){
				return fInObj(el.parentNode, id);
			}else{
				return false;
			}
		}
	}
}

function insertHTML(html)
{
	var old_content = cur_input_obj.value;
	var pre = old_content.substring(0, face_start);
	var post = old_content.substring(face_end);
	cur_input_obj.value = pre + html + post;
}

/**
 * 设置onclick事件
 */
document.onclick = function(e){
	if(gIsIE) var el = event.srcElement;
	else var el = e.target;
	var divFace = document.getElementById("divFace");

	if(el.tagName == "IMG"){
		try{
			if(fInObj(el, "divFace")){
				var tmp = el.src.split("/");
				divFace.style.display = "none";
				if(tmp[tmp.length-1] != 'close_img.gif')
				{
					var html='[img]'+tmp[tmp.length-1]+'[/img]';
					insertHTML(html);

				}
				return;
			}
		}catch(e){}
	}
}


function showFace(inputObj){
	var divFace =document.getElementById("divFace");
	cur_input_obj = document.getElementById(inputObj);
	
	divFace.style.display = "";
	
	divFace.innerHTML =  drawFace();
}

function drawFace() {
	var facetitle = ["撇嘴","色","发呆","得意","流泪","害羞","闭嘴","睡","大哭","尴尬","发怒","调皮","呲牙","微笑","难过","酷","非典","抓狂","我吐","偷笑","可爱","白眼","傲慢","饥饿","困","惊恐","流汗","憨笑","大兵","奋斗","咒骂","疑问","虚","晕","折磨","哀","骷髅","敲打","再见","闪人","发抖","爱情","跳","找","美妹","猪头","猫","狗","拥抱","钱","灯光","啤酒","生日","闪电","炸弹","刺刀","足球","音乐","大便","咖啡","吃饭","吃药","玫瑰","凋谢","亲嘴","爱心","心碎","礼物","电话","时间","闹钟","邮件","电视","太阳","月亮","强","弱","握手","胜利"];
	var pContent = '<p class="face_close clear"><span class="fl">插入表情</span><span class="fr"><img src="' + version_img('close_img.gif') + '" /></span></p>';
	pContent +='<ul class="clear">';
	for(i=100;i<184;i++)
	{
			var t=g_staticUrl+'/face/'+i+'.gif';
			pContent += '<li class="ef_of" onmouseover="this.className=\'ef_on\';" onmouseout="this.className=\'ef_of\';"><img src="'+t+'" title="表情：'+i+'" width="24" height="24" /></li>';
	}
	pContent +='</ul>';
	return pContent;
}
