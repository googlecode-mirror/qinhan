/**
 * 用户地区控制
 */

/* module object */
function GlobalProvincesModule ()
{
	this.debug = false;
	this.def_province = ["--", ""];
	this.def_city1 = ["--", ""];
	this.def_city2 = ["--", ""];
	this.def_city3 = ["--", ""];

	this.initProvince = function (obj1)
	{
		try{
			var i;
			for(i = obj1.options.length -1; i >= 0 ; i--)
			{
				removeOptionItem(obj1, i);
			}

			if(this.def_province)
				obj1.options.add(new Option(this.def_province[0], this.def_province[1]));

			if(!GP) return;

			for(i=0; i < GP.length; i++)
			{
				obj1.options.add(new Option(GP[i], GP[i]));
			}
		}catch(e){if(this.debug) alert("执行方法\"initProvince\"时，遇到" + e.message);}
	}

	this.initCity1 = function (obj1, key)
	{
		try{
			var i;
			for(i = obj1.options.length -1; i >= 0 ; i--)
			{
				this.removeOptionItem(obj1, i);
			}

			if(this.def_city1)
				obj1.options.add(new Option(this.def_city1[0], this.def_city1[1]));

			if(!GC1[key]) return;

			for(i=0; i < GC1[key].length; i++)
			{
				obj1.options.add(new Option(GC1[key][i], GC1[key][i]));
			}
		}catch(e){if(this.debug) alert("执行方法\"initCity1\"时，遇到" + e.message);}
	}

	this.initCity2 = function (obj1, key, key2)
	{
		try{
			var i;
			for(i = obj1.options.length -1; i >= 0 ; i--)
			{
				this.removeOptionItem(obj1, i);
			}

			if(this.def_city2)
				obj1.options.add(new Option(this.def_city2[0], this.def_city2[1]));

			if(!GC2[key]) return;
			if(!GC2[key][key2])
			{
				obj1.options[0].selected = true;
			}else{
				var equal_second_location = "";
				for(i=0; i < GC2[key][key2].length; i++)
				{
					if (GC2[key][key2][i] == key2 + "市") {
						equal_second_location = GC2[key][key2][i];
					} else {
						obj1.options.add(new Option(GC2[key][key2][i], GC2[key][key2][i]));
					}
				}
				if (equal_second_location != "") {
					obj1.options.add(new Option(equal_second_location, equal_second_location));
				}
				obj1.options.add(new Option("其他", "其他"));

				if(GC2[key][key2].length == 1)
					obj1.options[GC2[key][key2].length - 1].selected = true;
			}

		}catch(e){if(this.debug) alert("执行方法\"initCity2\"时，遇到" + e.message);}
	}

	this.initCity3 = function (obj1, key, key2, key3)
	{
		try{
			var i;
			for(i = obj1.options.length -1; i >= 0 ; i--)
			{
				this.removeOptionItem(obj1, i);
			}

			if(this.def_city3)
				obj1.options.add(new Option(this.def_city3[0], this.def_city3[1]));

			if(!GC3[key][key2] || !GC3[key][key2][key3])
			{
				obj1.options[obj1.options.length - 1].selected = true;
			}else{
				var count = 0;
				for(i=0; i < GC3[key][key2][key3].length; i++)
				{
					obj1.options.add(new Option(GC3[key][key2][key3][i], GC3[key][key2][key3][i]));
					count++;
				}
				if (count > 0) {
					obj1.options.add(new Option("其他", "其他"));
				}

				if(GC3[key][key2][key3].length == 1)
					obj1.options[GC3[key][key2][key3].length - 1].selected = true;
			}

		}catch(e){if(this.debug) alert("执行方法\"initCity2\"时，遇到" + e.message);}
	}

	this.selectProvincesItem = function (obj1, value)
	{
		try{
			var ret = false;
			for(var i = 0; i < obj1.options.length; i++)
			{
				if(obj1.options[i].text == value)
				{
					ret = obj1.options[i].selected = true;
					break;
				}
			}
			return ret;
		}catch(e){if(this.debug) alert("执行方法\"selectProvincesItem\"时，遇到" + e.message);}
	}

	this.selectCity1Item = function (obj1, value)
	{
		try{
			var ret = false;
			for(var i = 0; i < obj1.options.length; i++)
			{
				if(obj1.options[i].text == value)
				{
					ret = obj1.options[i].selected = true;
					break;
				}
			}
			return ret;
		}catch(e){if(this.debug) alert("执行方法\"selectCity1Item\"时，遇到" + e.message);}
	}

	this.selectCity2Item = function (obj1, value)
	{
		try{
			var ret = false;
			for(var i = 0; i < obj1.options.length; i++)
			{
				if(obj1.options[i].text == value)
				{
					ret = obj1.options[i].selected = true;
					break;
				}
			}
			return ret;
		}catch(e){if(this.debug) alert("执行方法\"selectCity2Item\"时，遇到" + e.message);}
	}

	this.getSelValue = function (obj1)
	{
		if(obj1 && obj1.options && obj1.options.length > 0)
			return obj1.options[obj1.selectedIndex].value;
		else
			return null;
	}

	this.getProvinceNameById = function (id)
	{
		try{
			var ret = "";
			for(var i = 0; i< GP.length; i++)
			{
				if(GP[i][1] == id)
				{
					ret = GP[i];
					break;
				}
			}

			return ret;
		}catch(e){if(this.debug) alert("执行方法\"getProvinceNameById\"时，遇到" + e.message);}
	}

	this.getProvinceIdByName = function (name)
	{
		try{
			var ret = -1;
			for(var i = 0; i< GP.length; i++)
			{
				if(GP[i] == name)
				{
					ret = GP[i][1];
					break;
				}
			}

			return ret;
		}catch(e){if(this.debug) alert("执行方法\"getProvinceIdByName\"时，遇到" + e.message);}
	}

	this.removeOptionItem = function(obj, index)
	{
		if(typeof obj.options.remove == "undefined")
		{
			obj.remove(index);
		}else{
			obj.options.remove(index);
		}
	}
}

/********** 省份数据 **********/
var GP = new Array('北京','上海','重庆','四川','广东','广西','湖南','湖北','浙江','江苏','江西','河北','河南','山东','山西','海南','福建','安徽','天津','甘肃','贵州','黑龙江','辽宁','吉林','宁夏','青海','陕西','内蒙古','新疆','西藏','云南','台湾','香港','澳门','海外');
/********** 市级数据 **********/
var GC1 = new Array();
GC1['安徽']=new Array('合肥','安庆','蚌埠','亳州','巢湖','池州','滁州','阜阳','淮北','淮南','黄山','六安','马鞍山','宿州','铜陵','芜湖','宣城');
GC1['澳门']=new Array('澳门');
GC1['北京']=new Array('昌平','朝阳','崇文','大兴','东城','房山','丰台','海淀','怀柔','门头沟','密云','平谷','石景山','顺义','通州','西城','宣武','延庆');
GC1['福建']=new Array('福州','龙岩','南平','宁德','莆田','泉州','三明','厦门','漳州');
GC1['甘肃']=new Array('兰州','白银','定西','甘南','嘉峪关','金昌','酒泉','临夏','陇南','平凉','庆阳','天水','武威','张掖');
GC1['广东']=new Array('广州','潮州','东莞','佛山','河源','惠州','江门','揭阳','茂名','梅州','清远','汕头','汕尾','韶关','深圳','阳江','云浮','湛江','肇庆','中山','珠海');
GC1['广西']=new Array('桂林','百色','北海','崇左','防城港','贵港','河池','贺州','来宾','柳州','南宁','钦州','梧州','玉林');
GC1['贵州']=new Array('贵阳','安顺','毕节','六盘水','黔东南','黔南','黔西南','铜仁','遵义');
GC1['海南']=new Array('海口','白沙','保亭','昌江','澄迈','儋州','定安','东方','乐东','临高','陵水','南沙群岛','琼海','琼中','三亚','屯昌','万宁','文昌','五指山','西沙群岛','中沙群岛');
GC1['河北']=new Array('石家庄','保定','沧州','承德','邯郸','衡水','廊坊','秦皇岛','唐山','邢台','张家口');
GC1['河南']=new Array('郑州','安阳','鹤壁','焦作','济源','开封','洛阳','漯河','南阳','平顶山','濮阳','三门峡','商丘','新乡','信阳','许昌','周口','驻马店');
GC1['黑龙江']=new Array('哈尔滨','大庆','大兴安岭','鹤岗','黑河','鸡西','佳木斯','牡丹江','七台河','齐齐哈尔','双鸭山','绥化','伊春');
GC1['湖北']=new Array('武汉','鄂州','恩施','黄冈','黄石','荆门','荆州','潜江','神农架','十堰','随州','天门','仙桃','咸宁','襄樊','孝感','宜昌');
GC1['湖南']=new Array('长沙','常德','郴州','衡阳','怀化','娄底','邵阳','湘潭','湘西','益阳','永州','岳阳','张家界','株洲');
GC1['吉林']=new Array('长春','白城','白山','吉林','辽源','四平','松原','通化','延边');
GC1['江苏']=new Array('南京','常州','淮安','连云港','南通','苏州','宿迁','泰州','无锡','徐州','盐城','扬州','镇江');
GC1['江西']=new Array('南昌','抚州','赣州','吉安','景德镇','九江','萍乡','上饶','新余','宜春','鹰潭');
GC1['辽宁']=new Array('沈阳','鞍山','本溪','朝阳','大连','丹东','抚顺','阜新','葫芦岛','锦州','辽阳','盘锦','铁岭','营口');
GC1['内蒙古']=new Array('呼和浩特','阿拉善','巴彦淖尔','包头','赤峰','鄂尔多斯','呼伦贝尔','通辽','乌海','乌兰察布','锡林郭勒','兴安');
GC1['宁夏']=new Array('银川','固原','石嘴山','吴忠','中卫');
GC1['青海']=new Array('西宁','果洛','海北','海东','海南','海西','黄南','玉树');
GC1['山东']=new Array('济南','滨州','德州','东营','菏泽','济宁','莱芜','聊城','临沂','青岛','日照','泰安','威海','潍坊','烟台','枣庄','淄博');
GC1['山西']=new Array('太原','长治','大同','晋城','晋中','临汾','吕梁','朔州','忻州','阳泉','运城');
GC1['陕西']=new Array('西安','安康','宝鸡','汉中','商洛','铜川','渭南','咸阳','延安','榆林');
GC1['上海']=new Array('宝山','长宁','崇明','奉贤','虹口','黄浦','嘉定','金山','静安','卢湾','闵行','南汇','浦东','普陀','青浦','松江','徐汇','杨浦','闸北');
GC1['四川']=new Array('成都','阿坝','巴中','达州','德阳','甘孜','广安','广元','乐山','凉山','泸州','眉山','绵阳','内江','南充','攀枝花','遂宁','雅安','宜宾','资阳','自贡');
GC1['台湾']=new Array('台北','阿莲','安定','安平','八德','八里','白河','白沙','板桥','褒忠','宝山','卑南','北斗','北港','北门','北埔','北投','补子','布袋','草屯','长宾','长治','潮州','车城','成功','城中区','池上','春日','刺桐','高雄','花莲','基隆','嘉义','苗栗','南投','屏东','台东','台南','台中','桃园','新竹','宜兰','彰化');
GC1['天津']=new Array('宝坻','北辰','大港','东丽','汉沽','和平','河北','河东','河西','红桥','蓟县','津南','静海','南开','宁河','塘沽','武清','西青');
GC1['西藏']=new Array('拉萨','阿里','昌都','林芝','那曲','日喀则','山南');
GC1['香港']=new Array('北区','大埔区','东区','观塘区','黄大仙区','九龙','葵青区','离岛区','南区','荃湾区','沙田区','深水埗区','屯门区','湾仔区','西贡区','香港','新界','油尖旺区','元朗区','中西区');
GC1['新疆']=new Array('乌鲁木齐','阿克苏','阿拉尔','阿勒泰','巴音郭楞','博尔塔拉','昌吉','哈密','和田','喀什','克拉玛依','克孜勒苏柯尔克孜','石河子','塔城','图木舒克','吐鲁番','五家渠','伊犁');
GC1['云南']=new Array('昆明','保山','楚雄','大理','德宏','迪庆','红河','丽江','临沧','怒江','曲靖','普洱','文山','西双版纳','玉溪','昭通');
GC1['浙江']=new Array('杭州','湖州','嘉兴','金华','丽水','宁波','衢州','绍兴','台州','温州','舟山');
GC1['重庆']=new Array('巴南','北碚','璧山','长寿','城口','大渡口','大足','垫江','丰都','奉节','涪陵','合川','江北','江津','九龙坡','开县','梁平','南岸','南川','彭水','綦江','黔江','荣昌','沙坪坝','石柱','双桥','铜梁','潼南','万盛','万州','巫山','巫溪','武隆','秀山','永川','酉阳','渝北','渝中','云阳','忠县');
GC1['海外']=new Array('阿根廷','埃及','爱尔兰','奥地利','奥克兰','澳大利亚','巴基斯坦','巴西','保加利亚','比利时','冰岛','朝鲜','丹麦','德国','俄罗斯','法国','菲律宾','芬兰','哥伦比亚','韩国','荷兰','加拿大','柬埔寨','喀麦隆','老挝','卢森堡','罗马尼亚','马达加斯加','马来西亚','毛里求斯','美国','秘鲁','缅甸','墨西哥','南非','尼泊尔','挪威','葡萄牙','其它地区','日本','瑞典','瑞士','斯里兰卡','泰国','土耳其','委内瑞拉','文莱','乌克兰','西班牙','希腊','新加坡','新西兰','匈牙利','以色列','意大利','印度','印度尼西亚','英国','越南','智利','其他');

/********** 县乡数据 **********/
var GC2 = new Array();
var GC3 = new Array();