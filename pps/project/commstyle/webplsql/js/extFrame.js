/**
	* 0����ȷ
	* 1��ָ������
	* 2��δ��¼����½��Զ���᷵��������֣���ֹ�ظ������γɵݹ�
	* 3��û��Ȩ��
	ext����AJAX���󶼷���JSON��ʽ���ݡ���������ʽΪ
	{
		error:0,//������룬0�޴�
		data:mix,//�����Ǹ�����������,��ѡ
		msg:�ɺ�̨���ص���Ϣ,����д��ֶ����Զ�������ʾ�������ɵ��ó��������β�����
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
	//������չ
	// --grid��ѡ
	if (!Ext.grid.GridView.prototype.templates) {
	   Ext.grid.GridView.prototype.templates = {};
	}
	Ext.grid.GridView.prototype.templates.cell = new Ext.Template(
	   '<td class="x-grid3-col x-grid3-cell x-grid3-td-{id} x-selectable {css}" style="{style}" tabIndex="0" {cellAttr}>',
	   '<div class="x-grid3-cell-inner x-grid3-col-{id}" {attr}>{value}</div>',
	   '</td>');
	//iframe��չ ----
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
		title:'�û���½',
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
				loginFunction(this.ownerCt.ownerCt);//�û��Զ����½����
			};
			this.items = [{
				fieldLabel:'�û���'
			},{
				fieldLabel:'����',
				inputType:'password'
			}];
			eF.win.loginWin.superclass.initComponent.call(this);
		},
		buttons:[{
			text:'��½'
		},{
			text:'ȡ��',
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
						if(b)Ext.Ajax.request(b);//����ִ�иղŵ�����
						win.close();
					},params:d,method:'get'});
				}
			});
		win.show();
		return win;
	};
	/*
		�û������success��failure����ԭ�����ˡ�ʧ�ܽ�ʹ��ͬһ�ķ�������
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
				Ext.Msg.alert('����',(o.msg ? o.msg : '��û��Ȩ�ޣ�'));
			} else if(o.error == 2) {
				eF.win.loginWinShow(b,'login.php');
			} else if(o.error == 1) {
				if(oldfailure) {
					oldfailure(o,r,b);
				}
				if(o.msg) {
					Ext.Msg.alert('����',o.msg);
				}
			} else {
				if(oldsuccess) {
					oldsuccess(o,r,b);
				}
				if(o.msg) {
					Ext.Msg.alert('��Ϣ',o.msg);
				}
			}
		};
		config.failure = function(){Ext.Msg.alert('����','�������!����û�����ҳ�档')}
		Ext.Ajax.request(config);
	};
});