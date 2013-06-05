/**
	* 0、正确
	* 1、指定错误
	* 2、未登录，登陆永远不会返回这个数字，防止重复请求形成递归
	* 3、没有权限
	ext所有AJAX请求都返回JSON格式数据。其完整格式为
	{
		error:0,//错误代码，0无错
		data:mix,//可以是各种类型数据,可选
		msg:由后台传回的消息,如果有此字段则自动弹窗显示，否则由调用程序决定如何操作。
	}
	Ext.Ajax.request({
	   url: 'foo.php',
	   success: someFn,
	   failure: otherFn,
	   headers: {
		   'my-header': 'foo'
	   },
	   params: { foo: 'bar' }
	});

*/
var eF = {};
eF.win = {};

Ext.onReady(function(){
	//基本扩展
	// --grid可选
	if (!Ext.grid.GridView.prototype.templates) {
	   Ext.grid.GridView.prototype.templates = {};
	}
	Ext.grid.GridView.prototype.templates.cell = new Ext.Template(
	   '<td class="x-grid3-col x-grid3-cell x-grid3-td-{id} x-selectable {css}" style="{style}" tabIndex="0" {cellAttr}>',
	   '<div class="x-grid3-cell-inner x-grid3-col-{id}" {attr}>{value}</div>',
	   '</td>');
	//iframe扩展 ----
	Ext.ux.IFrameComponent = Ext.extend(Ext.BoxComponent, {
		onRender : function(ct, position){
			this.el = ct.createChild({tag:'iframe',id: 'iframe-'+ this.id, frameBorder: 0,width:'100%',height:'100%',
				src:Ext.isObject(this.autoLoad) ? this.autoLoad.url :this.autoLoad});
		}
	});

	Ext.TabPanel.prototype.addIframe = function(conf){
		var config = {
			items:[new Ext.ux.IFrameComponent({ id: conf.id+'_1', autoLoad:conf.autoLoad})]
		};
		Ext.applyIf(config,conf);
		config.autoLoad = undefined;
		return this.add(config);
	};
	eF.win.loginWin = Ext.extend(Ext.Window,{
		title:'用户登陆',
		layout:'form',
		labelWidth:45,bodyStyle:'padding:8px;',
		closable:false,
		width:285,
		height:130,
		modal:true,
		defaults:{xtype:'textfield',width:200},
		initComponent:function(){
			var loginFunction = this.loginFunction;
			this.buttons[0].handler = function() {
				loginFunction(this.ownerCt.ownerCt);//用户自定义登陆函数
			};
			this.items = [{
				fieldLabel:'用户名'
			},{
				fieldLabel:'密码',
				inputType:'password'
			}];
			eF.win.loginWin.superclass.initComponent.call(this);
		},
		buttons:[{
			text:'登陆'
		},{
			text:'取消',
			handler:function(){this.ownerCt.ownerCt.close();}
		}]
	});
	eF.win.loginWinShow = function(b,url){
		var win = new eF.win.loginWin({
				loginFunction:function(win){
					var d = {};
					d.username = win.items.items[0].getValue();
					d.password = win.items.items[1].getValue();
					eF.ajax({url:url,success:function(o){
						if(b)Ext.Ajax.request(b);//重新执行刚才的请求
						win.close();
					},params:d,method:'get'});
				}
			});
		win.show();
		return win;
	};
	/*
		用户输入的success和failure不是原来的了。失败将使用同一的方法处理
	*/
	eF.ajax = function(config) {
		if(!config.params)config.params = {};
		config.params.isAjax = 1;
		if(config.method && config.method == 'get') {
			var params = [];
			for(var i in config.params) {
				params.push(i + '=' + config.params[i]);
			}
			if(/\?/.test(config.url)) {
				config.url += '&' + params.join('&');
			} else {
				config.url += '?' + params.join('&');
			}
		}
		var oldsuccess = config.success;
		var oldfailure = config.failure;
		config.success = function(r,b) {
			var o = eval('(' + r.responseText + ')');
			if(o.error == 3) {
				Ext.Msg.alert('错误',(o.msg ? o.msg : '您没有权限！'));
			} else if(o.error == 2) {
				eF.win.loginWinShow(b,'login.php');
			} else if(o.error == 1) {
				if(oldfailure) {
					oldfailure(o,r,b);
				}
				if(o.msg) {
					Ext.Msg.alert('错误',o.msg);
				}
			} else {
				if(oldsuccess) {
					oldsuccess(o,r,b);
				}
				if(o.msg) {
					Ext.Msg.alert('消息',o.msg);
				}
			}
		};
		config.failure = function(){Ext.Msg.alert('错误','网络错误!可能没有这个页面。')}
		Ext.Ajax.request(config);
	};
});