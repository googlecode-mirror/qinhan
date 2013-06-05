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
		//快捷输入右键菜单
		var menu =  new Ext.menu.Menu({
			items : [
				{
					text : '日期输入',
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
					text : '时间输入',
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
					text:'sql 模板',
					menu:[{text:'Update语句',handler:function(){
								str = "update " + Ext.getCmp('dbTable').getValue() + "\nset\n A=B\nwhere\n C=D";
								Ext.getCmp('sql-window').getActiveTab().items.items[0].setValue(str);
							}
						},{text:'Insert语句',handler:function(){
								str = "insert into " + Ext.getCmp('dbTable').getValue() + "() \nvalues()";
								Ext.getCmp('sql-window').getActiveTab().items.items[0].setValue(str);
							}
						},{text:'Select语句',handler:function(){
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
		closeAction:'hide',width:300,height:150,title:'数据 - 可拖动缩放',x:5,y:5,
		listeners:{
			close:function(){
				dataWin.isShowing = false;
			}
		}
	});
	var helpWin = new Ext.Window({
		title:'使用说明',height:250,width:500,bodyStyle:'padding:5px;',autoScroll:true,
		html:	'<ul style="font-size:12px;line-height:20px;">' +
                    '<li>Web PL/SQL 由PPS员工王钟凯(328725540@qq.com)同学开发，蔡旭东(fifsky@gmail.com)于2013-05-10分离出独立版本</li>' +
					'<li><p style="color:red">激动人心的新功能：数据直接修改<br/>从1.1版本开始，只需要在sql语句后面加上for update 关键字便可以单击一行进行修改。<br/>另外注意，如果有个小红三角形在左上角，表示数据库其实没有更新，确认你有write权限，并且填写数据的格式正确！</p></li>' +
					'<li>其他新特性,<br/>导出excel：导出用TAB分隔的文本；<br/>lock：将某个数据库设置为当前标签专有。这个标签的所有sql都会用这个库。</li>' +
					'<li>1、使用任何功能前必须指定一个数据库，在左上角第一个SELECT框中选择。</li>' +
					'<li>2、左上角下方有一些常用功能按钮包括本帮助。</li>' +
					'<li>3、SQL语句可以直接按F8执行，如果选中某些sql，将执行选中的部分。</li>' +
					'<li>4、select ,desc ,keys等执行结果将会出现一个表格，双击表格某一项将会打开一个数据窗口，用来查看较大的文本数据。文本框在打开的状态下，仅需要单击便可改变显示的字段和列。</li>' +
					'<li>5、一些指令<br/>' +
								'key table_name ：查看索引<br/>' +
								'desc table_name ：查看表结构<br/>' +
						'</li>' +
					'<li>6、sql可以保存，双击保存后的SQL将直接写入当前标签中，并设置好数据库</li>' +
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
			Ext.Msg.alert('保存错误','你需要选择一个数据库并将SQL书写在输入框中');return;
		}
		if(saveSqlTitle == null) {
			saveSqlTitle = new Ext.Window({
				width:300,height:110,title:'标题(相同标题将覆盖原来的纪录)',layout:'fit',closeAction:'hide',modal:true,
				items:{
					xtype:'textfield'
				},buttons:[{
					text:'保存',handler:function(btn){
						var inthis = btn.ownerCt.ownerCt;
						eF.ajax({
							url:'webplsql.php?c=savesql',
							params:{sql:inthis.sql,db_name:inthis.db_name,title:inthis.items.items[0].getValue()},
							success:function(o){
								Ext.Msg.alert('消息','保存成功');
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
				width:300,height:400,title:'双击选择SQL ',layout:'fit',closeAction:'hide',
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
			Ext.Msg.alert('错误','必须是一个select语句');
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
			text:'新窗口',
			handler:function(){
				tab.addNew('SQL WINDOW',true).show();
			}
		},'-',{
			text:'导出Excel数据',
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
		},'-','->',{text:'耗时：0',id:'query_time'},'-','开窗多,占内存'],
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
						dataIndex:'db_name',	header:'没有数据'
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
				title:'数据库 Web PL/SQL 1.1 未连接',bodyStyle:'padding:5px;',
				width:300,layout:'form',
				items:[new eF.adminDbComboBox({
					fieldLabel:'数据库',id:'eF-DbComboBox',
					listeners:{
						select :function(c,record,index){
							var db_name = record.get('db_name');
							eF.db.setDb(db_name);
							//Ext.getCmp('databaseLabel').setText('已经选择数据库: ' + db_name);
							north.items.items[0].setTitle('数据库 Web PL/SQL 1.1 已连接 ' + db_name);
						}
					}
				}),new eF.adminDbTables({
					fieldLabel:'数据表',id:'dbTable'
				})],
				bbar:[{
					'text':'查看数据',
					handler:function(btn){
						var inthis = btn.ownerCt.ownerCt;
						var table = inthis.items.items[1].getValue();
						if(table.length <= 0) {
							Ext.Msg.alert('错误','请选择一个表');
							return false;
						}
						var n = tab.addNew('SQL WINDOW',true);
						n.show();
						n.items.items[0].setValue('select * from ' + table);
						query();
					}
				},'-',{
					'text':'显示表结构',
					handler:function(btn){
						var inthis = btn.ownerCt.ownerCt;
						var table = inthis.items.items[1].getValue();
						if(table.length <= 0) {
							Ext.Msg.alert('错误','请选择一个表');
							return false;
						}
						var n = tab.addNew('SQL WINDOW',true);
						n.show();
						n.items.items[0].setValue('desc ' + table);
						query();
					}
				},{
					'text':'查看索引',
					handler:function(btn){
						var inthis = btn.ownerCt.ownerCt;
						var table = inthis.items.items[1].getValue();
						if(table.length <= 0) {
							Ext.Msg.alert('错误','请选择一个表');
							return false;
						}
						var n = tab.addNew('SQL WINDOW',true);
						n.show();
						n.items.items[0].setValue('keys ' + table);
						query();
					}
				},'-',{
					text:'帮助',
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
				Ext.Msg.confirm('警告','语句中没有任何条件限制，您确定要操作吗？',function(o) {
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
	//BUG列表显示
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
			Ext.Msg.alert('错误','请先选择一个数据库');
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
		/*ie浏览器*/
		select_field.selectionStart || select_field.selectionStart == '0') {
		var startP = select_field.selectionStart;
		var endP = select_field.selectionEnd;
		if (startP != endP) {
			word = select_field.value.substring(startP, endP);
		}
	}   /*标准浏览器*/
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