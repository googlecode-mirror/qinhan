<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>WEB/PL SQL</title>
<meta http-equiv="Content-Type" content="text/html; charset=GBK" />
<script type="text/javascript" src="/project/commstyle/webplsql/extjs3/adapter/ext/ext-base.js" ></script>
<script type="text/javascript" src="/project/commstyle/webplsql/extjs3/ext-all.js" ></script>
<script type="text/javascript" src="/project/commstyle/webplsql/js/extFrame.js" ></script>
<link rel="stylesheet" type="text/css" href="/project/commstyle/webplsql/css/ext_buttons.css" />

<script type="text/javascript" src="/project/commstyle/webplsql/js/ExtCom/admin/db.js" ></script>
<script type="text/javascript" src="/project/commstyle/webplsql/js/ExtCom/plus.js" ></script>
<script type="text/javascript" src="/project/commstyle/webplsql/js/ExtCom/admin/RowEditor_k.js" ></script>
<script type="text/javascript" >
//eF.db.db_name
Ext.onReady(function(){
		//��������Ҽ��˵�
		var menu =  new Ext.menu.Menu({
			items : [
				{
					text : '��������',
					menu:new Ext.menu.DateMenu({
						listeners:{
							select:function(selfObj,date){
								var str = "to_date('" + date.format('Y-m-d') + "','yyyy-mm-dd')";//h:i:s
								var textarea = Ext.getCmp('sql-window').getActiveTab().items.items[0].el.dom;
								addText(textarea,str);
							}
						}
					})
				},
				{
					text : 'ʱ������',
					menu:new Ext.menu.DateMenu({
						listeners:{
							select:function(selfObj,date){
								var str = "to_date('" + date.format('Y-m-d') + " 00:00:00','yyyy-mm-dd hh24:mi:ss')";//h:i:s
								var textarea = Ext.getCmp('sql-window').getActiveTab().items.items[0].el.dom;
								addText(textarea,str);
							}
						}
					})
				},
				{
					text:'sql ģ��',
					menu:[{text:'Update���',handler:function(){
								str = "update " + Ext.getCmp('dbTable').getValue() + "\nset\n A=B\nwhere\n C=D";
								Ext.getCmp('sql-window').getActiveTab().items.items[0].setValue(str);
							}
						},{text:'Insert���',handler:function(){
								str = "insert into " + Ext.getCmp('dbTable').getValue() + "() \nvalues()";
								Ext.getCmp('sql-window').getActiveTab().items.items[0].setValue(str);
							}
						},{text:'Select���',handler:function(){
								str = "Select * \nfrom " + Ext.getCmp('dbTable').getValue() + " \nwhere 1=1";
								Ext.getCmp('sql-window').getActiveTab().items.items[0].setValue(str);
							}
						}
					]
				}
		]});
window.m = menu;
	var tables = new eF.adminDbTables();
	var dataWin = new Ext.Window({
		layout:'fit',items:{xtype:'textarea',border:false},
		closeAction:'hide',width:300,height:150,title:'���� - ���϶�����',x:5,y:5,
		listeners:{
			close:function(){
				dataWin.isShowing = false;
			}
		}
	});
	var helpWin = new Ext.Window({
		title:'ʹ��˵��',height:250,width:500,bodyStyle:'padding:5px;',autoScroll:true,
		html:	'<ul style="font-size:12px;line-height:20px;">' +
                    '<li>Web PL/SQL ��PPSԱ�����ӿ�(328725540@qq.com)ͬѧ����������(fifsky@gmail.com)��2013-05-10����������汾</li>' +
					'<li><p style="color:red">�������ĵ��¹��ܣ�����ֱ���޸�<br/>��1.1�汾��ʼ��ֻ��Ҫ��sql���������for update �ؼ��ֱ���Ե���һ�н����޸ġ�<br/>����ע�⣬����и�С�������������Ͻǣ���ʾ���ݿ���ʵû�и��£�ȷ������writeȨ�ޣ�������д���ݵĸ�ʽ��ȷ��</p></li>' +
					'<li>����������,<br/>����excel��������TAB�ָ����ı���<br/>lock����ĳ�����ݿ�����Ϊ��ǰ��ǩר�С������ǩ������sql����������⡣</li>' +
					'<li>1��ʹ���κι���ǰ����ָ��һ�����ݿ⣬�����Ͻǵ�һ��SELECT����ѡ��</li>' +
					'<li>2�����Ͻ��·���һЩ���ù��ܰ�ť������������</li>' +
					'<li>3��SQL������ֱ�Ӱ�F8ִ�У����ѡ��ĳЩsql����ִ��ѡ�еĲ��֡�</li>' +
					'<li>4��select ,desc ,keys��ִ�н���������һ�����˫�����ĳһ����һ�����ݴ��ڣ������鿴�ϴ���ı����ݡ��ı����ڴ򿪵�״̬�£�����Ҫ������ɸı���ʾ���ֶκ��С�</li>' +
					'<li>5��һЩָ��<br/>' +
								'key table_name ���鿴����<br/>' +
								'desc table_name ���鿴��ṹ<br/>' +
						'</li>' +
					'<li>6��sql���Ա��棬˫��������SQL��ֱ��д�뵱ǰ��ǩ�У������ú����ݿ�</li>' +
				'</ul>',
		closeAction:'hide'
	});
	/**/
	dataWin.isShowing = false;
	var center = new Ext.Panel({
		region:'center',
		items:{html:'new'},
		layout:'fit'
	});
	var saveSqlTitle = null;
	function saveSql()
	{
		var db_name = eF.db.db_name;
		var sql = Ext.getCmp('sql-window').getActiveTab().items.items[0].getValue();
		if(db_name.length == 0 || sql.length == 0) {
			Ext.Msg.alert('�������','����Ҫѡ��һ�����ݿⲢ��SQL��д���������');return;
		}
		if(saveSqlTitle == null) {
			saveSqlTitle = new Ext.Window({
				width:300,height:110,title:'����(��ͬ���⽫����ԭ���ļ�¼)',layout:'fit',closeAction:'hide',modal:true,
				items:{
					xtype:'textfield'
				},buttons:[{
					text:'����',handler:function(btn){
						var inthis = btn.ownerCt.ownerCt;
						eF.ajax({
							url:'webplsql.php?c=savesql',
							params:{sql:inthis.sql,db_name:inthis.db_name,title:inthis.items.items[0].getValue()},
							success:function(o){
								Ext.Msg.alert('��Ϣ','����ɹ�');
								if(sqlListWin != null)sqlListWin.items.items[0].getStore().load();
							}
						});
					}
				}]
			});
		}

		saveSqlTitle.sql = sql;saveSqlTitle.db_name = db_name;
		saveSqlTitle.show();
	}
	var sqlListWin = null;
	function sqlList()
	{
		if(sqlListWin == null) {
			sqlListWin = new Ext.Window({
				width:300,height:400,title:'˫��ѡ��SQL ',layout:'fit',closeAction:'hide',
				items:new eF.adminDbUserSqlList({
					listeners:{
						rowdblclick:function(g,rowIndex,e) {
							var record = g.getStore().getAt(rowIndex);
							Ext.getCmp('sql-window').getActiveTab().items.items[0].setValue(record.get('sql'));
							Ext.getCmp('eF-DbComboBox').setValue(record.get('db_name'));
							eF.db.setDb(record.get('db_name'));
							if(saveSqlTitle != null) {
								saveSqlTitle.items.items[0].setValue(record.get('title'));
							}
						}
					}
				})
			});
			sqlListWin.items.items[0].getStore().load();
		}
		sqlListWin.show();
	}

	function csvDownload() {
		var sql = Ext.getCmp('sql-window').getActiveTab().items.items[0].getValue();
		var db_name;
		var data = {};
		db_name = getDbName();
		if(!db_name){
			return false;
		}
		sql = sqlRebuilt(sql);
		if(/^select /i.test(sql) == false) {
			Ext.Msg.alert('����','������һ��select���');
			return false;
		}
		data.csv = 1;
		data.sql = sql;
		data.db_name = db_name;
		eF.ajax({
			url:'webplsql.php?c=query',
			method:'POST',
			params:data,
			success:function(o){
				dataWin.show();
				dataWin.isShowing = true;
				dataWin.items.items[0].setValue(o.data);
			}
		});
	}
	var tab = new Ext.TabPanel({
		region:'center',id:'sql-window',margins:'0 0 0 5',
		resizeTabs:true, // turn on tab resizing
        enableTabScroll:true,
		minTabWidth: 115,
        tabWidth:135,
        enableTabScroll:true,
		bbar:[{
			text:'�´���',
			handler:function(){
				tab.addNew('SQL WINDOW',true).show();
			}
		},'-',{
			text:'����Excel����',
			handler:csvDownload
		},{
			text:'Lock',
			id:'lock-db',
			iconCls:'btnImgLock_5',
			listeners :{
				toggle :function(selfObj,pressed){
					var t = Ext.getCmp('sql-window').getActiveTab();
					if(pressed) {
						var db_name = getDbName();
						if(db_name) {
							t.db_name = db_name;
							selfObj.setText(db_name);
						} else {
							selfObj.toggle(false);
						}
					} else {
						t.db_name = null;
						selfObj.setText('Lock');
					}
				}
			},
			enableToggle:true
		},'-','->',{text:'��ʱ��0',id:'query_time'},'-','������,ռ�ڴ�'],
		listeners:{
			tabchange:function(tab,tabThis){
				if(center.items.items[0]) center.items.items[0].hide();
				center.removeAll(false);
				tabThis.bindGrid.show();
				center.add(tabThis.bindGrid);
				//center.update('');
				//center.doLayout();
				center.doLayout();
			}
		},
		addNew:function(title,close){
			var textarea = new Ext.form.TextArea({
					enableKeyEvents:true,
					listeners:{
						keydown:function(t,e) {
							if(e.keyCode == e.F8) {
								query();
								return false;
							}
						}
					}
				});

			var n = tab.add({
				title:title,layout:'fit',closable:close,border:false,
				items:textarea
			});
			textarea.on('afterrender',function(selfObj){
				selfObj.el.dom.oncontextmenu = function(ev){
							ev = window.event ? event : ev;
							if(ev.clientX) {
								menu.showAt([ev.clientX,ev.clientY]);
							}
							return false;
						};
			});

			n.bindGrid = new eF.dynamicGrid({
					searchUrl:'webplsql.php?c=query',split: true,border:false,
					region:'center',
					loadMask:true,
					listeners:{
						celldblclick:function(grid, rowIndex, columnIndex, e) {
								var record = grid.getStore().getAt(rowIndex);  // Get the Record
								var fieldName = grid.getColumnModel().getDataIndex(columnIndex); // Get field name
								var data = record.get(fieldName);
								dataWin.items.items[0].setValue(data);
								dataWin.show();
								dataWin.isShowing = true;
								return false;
							},
						cellclick:function(grid, rowIndex, columnIndex, e){
								if(dataWin.isShowing){
									var record = grid.getStore().getAt(rowIndex);  // Get the Record
									var fieldName = grid.getColumnModel().getDataIndex(columnIndex); // Get field name
									var data = record.get(fieldName);
									dataWin.items.items[0].setValue(data);
								}
						}
					},
					//margins:'5 0 0 0',
					columns:[{
						dataIndex:'db_name',	header:'û������'
					}]
				});
			return n;
		}
	});

	var north = new Ext.Panel({
		region:'north',split: true,border:false,id:'northPanel',
		margins:'5 5 0 5',
		height:150,
		layout:'border',
		items:[
			{
				region:'west',
				title:'���ݿ� Web PL/SQL 1.1 δ����',bodyStyle:'padding:5px;',
				width:300,layout:'form',
				items:[new eF.adminDbComboBox({
					fieldLabel:'���ݿ�',id:'eF-DbComboBox',
					listeners:{
						select :function(c,record,index){
							var db_name = record.get('db_name');
							eF.db.setDb(db_name);
							//Ext.getCmp('databaseLabel').setText('�Ѿ�ѡ�����ݿ�: ' + db_name);
							north.items.items[0].setTitle('���ݿ� Web PL/SQL 1.1 ������ ' + db_name);
						}
					}
				}),new eF.adminDbTables({
					fieldLabel:'���ݱ�',id:'dbTable'
				})],
				bbar:[{
					'text':'�鿴����',
					handler:function(btn){
						var inthis = btn.ownerCt.ownerCt;
						var table = inthis.items.items[1].getValue();
						if(table.length <= 0) {
							Ext.Msg.alert('����','��ѡ��һ����');
							return false;
						}
						var n = tab.addNew('SQL WINDOW',true);
						n.show();
						n.items.items[0].setValue('select * from ' + table);
						query();
					}
				},'-',{
					'text':'��ʾ��ṹ',
					handler:function(btn){
						var inthis = btn.ownerCt.ownerCt;
						var table = inthis.items.items[1].getValue();
						if(table.length <= 0) {
							Ext.Msg.alert('����','��ѡ��һ����');
							return false;
						}
						var n = tab.addNew('SQL WINDOW',true);
						n.show();
						n.items.items[0].setValue('desc ' + table);
						query();
					}
				},{
					'text':'�鿴����',
					handler:function(btn){
						var inthis = btn.ownerCt.ownerCt;
						var table = inthis.items.items[1].getValue();
						if(table.length <= 0) {
							Ext.Msg.alert('����','��ѡ��һ����');
							return false;
						}
						var n = tab.addNew('SQL WINDOW',true);
						n.show();
						n.items.items[0].setValue('keys ' + table);
						query();
					}
				},'-',{
					text:'����',
					handler:function(){
						helpWin.show();
					}
				}]
			},tab,{
				region:'east',width:100,margins:'0 0 0 5',border:false,layout:'border',
				items:[{
					xtype:'button',width:98,height:140,iconCls:'btnImEpiphany_2',scale:'large',iconAlign: 'top',
					text:'Execute(F8)',region:'center',
					handler:query
				}]
			}
		]
	});

	var mainView = new Ext.Viewport({
		layout:'border',
		items:[north,center]
	});
	//tables.searchFunc('pps216');
	window.t = mainView;
	//center.getStore().load();
	tab.addNew('SQL WINDOW',false).show();
	//mainView.doLayout();
	//sql
	function query()
	{
		var sql,db_name ;

		sql = getFieldSelection(Ext.getCmp('sql-window').getActiveTab().items.items[0].id);
		if(sql.length <= 0)
			sql = Ext.getCmp('sql-window').getActiveTab().items.items[0].getValue();

		if(/^\s*show bugs\s*$/i.test(sql)) {
			db_name = 'ppst';
		} else {
			db_name = getDbName();
		}
		if(!db_name)return false;
		var a = sql.match(/.{5,30}/);
		Ext.getCmp('sql-window').getActiveTab().setTitle(a[0]);

		var store = center.items.items[0].getStore();
		sql = sqlRebuilt(sql);
		store.setBaseParam('sql',sql);
		store.setBaseParam('db_name',db_name);

		if(/\s*(update |delete )/i.test(sql)) {
			if(/where/.test(sql) == false) {
				Ext.Msg.confirm('����','�����û���κ��������ƣ���ȷ��Ҫ������',function(o) {
					if(o == 'no') return false;
					store.load();
				});
				return true;
			}
		}
		store.load();
	}
	function sqlRebuilt(sql)
	{
		var a;
		if(a = sql.match(/^\s*desc\s+?(\w+)\s*$/i)) {
			sql = descTable(a[1],'col');
		} else if(a = sql.match(/^\s*keys\s+?(\w+)\s*$/i)) {
			sql = descTable(a[1],'con');
		}
		return sql;
	}
	function descTable(table_name,type)
	{
		/*
			select * from user_constraints where table_name = upper('pps_user_info');
			select * from user_tab_columns where table_name = upper('pps_user_info');
		*/
		var sql = '';
		if(type == 'con') {
			sql = "select * from user_indexes where table_name = upper('" + table_name + "')";
		} else if(type == 'col') {
			sql = "select a.column_name,comments,data_type,data_length,data_precision,data_scale,nullable,column_id,default_length,data_default from user_tab_columns a,user_col_comments b where a.table_name = upper('" + table_name + "') and a.column_name = b.column_name and a.table_name = b.table_name";
		}

		return sql;
	}
	//BUG�б���ʾ
	eF.adminDbBuglist = function(){
		var n = tab.addNew('SQL WINDOW',true);
		n.show();
		n.items.items[0].setValue('show bugs');
		query();
	}

	function getDbName ()
	{
		var db_name;
		var t = Ext.getCmp('sql-window').getActiveTab();
		if(t.db_name) {
			db_name = t.db_name;
		} else {
			db_name = eF.db.db_name;
		}

		if(!db_name.length || db_name.length <=0) {
			Ext.Msg.alert('����','����ѡ��һ�����ݿ�');
			return false;
		}

		return db_name;
	}

	Ext.getCmp('sql-window').on('tabchange',function(selfObj,tab){
		Ext.getCmp('lock-db').setText('Lock');
		Ext.getCmp('lock-db').el.removeClass('x-btn-pressed');
		if(tab.db_name) {
			Ext.getCmp('lock-db').setText(tab.db_name);
			Ext.getCmp('lock-db').el.addClass('x-btn-pressed');
		}
	});
});
function getFieldSelection(id)
{
	var select_field = document.getElementById(id);
	word='';
	if (document.selection) {
		var sel = document.selection.createRange();
		if (sel.text.length > 0) {
			word = sel.text;
		}
    } else if (
		/*ie�����*/
		select_field.selectionStart || select_field.selectionStart == '0') {
		var startP = select_field.selectionStart;
		var endP = select_field.selectionEnd;
		if (startP != endP) {
			word = select_field.value.substring(startP, endP);
		}
	}   /*��׼�����*/
    return word;
}
function addText(ubb,str)
{
	var ubbLength=ubb.value.length;
	ubb.focus();
	if(typeof document.selection !="undefined")
	{
		document.selection.createRange().text=str;
	} else {
		ubb.value=ubb.value.substr(0,ubb.selectionStart)+str+ubb.value.substring(ubb.selectionStart,ubbLength);
	}
}
</script>

<link rel="stylesheet" type="text/css" href="/project/commstyle/webplsql/extjs3/resources/css/ext-all.css" />
<link rel="stylesheet" type="text/css" href="/project/commstyle/webplsql/css/RowEditor.css" />
<link rel="stylesheet" type="text/css" href="/project/commstyle/webplsql/css/extFrame.css" />
</head>
<body>
</body>
</html>