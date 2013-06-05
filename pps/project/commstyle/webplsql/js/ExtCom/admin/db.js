/*
* 提供目前pps的可操作数据库列表
* @author			王钟凯 (Kevin)
* @copyright		PPS
* @E-mail			328725540@qq.com kevin@dev.ppstream.com
* @version			1.1
*/

(function() {
	//数据库列表，不搜索。不分页
	eF.adminDbChangeAuth = function(rowIndex,column,store_id) {
		var record = Ext.getCmp('grid_' + store_id).getStore().getAt(rowIndex);
		var url = "changeauth";
		var data = {
			user_id : record.get('user_id'),
			flag : (record.get(column) == '0' ? 1 : 0),
			column : column,
			db_name: record.get('db_name')
		};
		eF.ajax({
			url:url,
			params:data,
			method:'get',
			success:function(o){
				record.set('read_flag',o.data.read_flag);
				record.set('write_flag',o.data.write_flag);
				record.set('delete_flag',o.data.delete_flag);
				record.commit();
			}
		});
	}
	
	function authRenderer(str,view,record,rowIndex,colIndex)
	{
		var db_name = record.get('db_name');
		var column = record.fields.get(colIndex).name;
		var store_id = record.store.storeId;
		if(str == '0') {
			return "<a style='color:red;' href=\"javascript:eF.adminDbChangeAuth(" + rowIndex + ",'" + column + "','" + store_id + "')\"> 否 </a>";
		} else {
			return "<a href=\"javascript:eF.adminDbChangeAuth(" + rowIndex + ",'" + column + "','" + store_id + "')\"> 是 </a>";
		}
	}
	eF.adminDbList = Ext.extend(Ext.grid.GridPanel,{
		stripeRows : true,
		gridSearch : function(user_id) {
			this.getStore().setBaseParam('user_id',user_id);
			this.getStore().load();
			this.user_id = user_id;
		},
		columns:[{
				dataIndex:'db_name',	header:'数据库名'
			},{
				dataIndex:'read_flag',	header:'可读',renderer:authRenderer
			},{
				dataIndex:'write_flag',	header:'可写',renderer:authRenderer
			},{
				dataIndex:'delete_flag',	header:'可删',renderer:authRenderer
			}],
		getTbar:function() {
			return [{text:'新增数据库',
				handler:function(btn) {
					var g = btn.ownerCt.ownerCt;
					eF.adminDbInfo('',g.getStore());
				}
			}];
		},
		viewConfig :{forceFit:true},
		constructor:function(config){
			this.store = new Ext.data.JsonStore({
					fields:['db_name','read_flag','write_flag','delete_flag','user_id'],
					url: '/webplsql.php?c=searchjson',
					root:'data',
					idProperty:'db_name',
					id:'ext-comp-' + (++Ext.Component.AUTO_ID)
			});
			
			this.id = 'grid_ext-comp-' + (Ext.Component.AUTO_ID);
			this.tbar = this.getTbar();
			Ext.apply(this,config);
			eF.adminDbList.superclass.constructor.call(this);
		},
		listeners:{
			rowdblclick:function(g,rowIndex) {
				var record = g.getStore().getAt(rowIndex);
				eF.adminDbInfo(record.get('db_name'),record);
			}
		}
	});
	
	eF.adminDbInfo = function(db_name,record) {
		var win = new Ext.Window({
				title:'数据库',width:250,height:130,bodyStyle:'padding:5px;',modal:true,
				layout:'form',
				items:[{
					fieldLabel:'数据库名',
					xtype:'textfield',width:100
				}],
				buttons:[{
					text:'保存',
					handler:function(btn){
						var data = {
							db_name:win.items.items[0].getValue(),
							old_db_name:win.old_db_name
						};
						eF.ajax({
							url:'modifyjson',
							params:data,
							success:function() {
								if(win.old_db_name.length > 0) {
									record.set('db_name',data.db_name);
									record.commit();
								} else {
									record.load();
								}
							}
						});
					}				
				}]
			});
			
		win.items.items[0].setValue(db_name);
		if(db_name.length > 0) {
			win.old_db_name = db_name;
			win.setTitle('修改数据库 - ' + db_name);
		} else {
			win.setTitle('新增数据库');
			win.old_db_name = '';
		}
		win.show();
	}
	eF.adminDbInfo.win = null;

	/*
		以下操作依赖全局变量。
	*/
	eF.db = {};
	eF.db.setDb = function(db_name) {
		eF.db.db_name = db_name;
	};
	eF.db.db_name = '';
	eF.db.old_db_name = 'null';
	/*
		数据库下拉列表
	*/
	eF.adminDbComboBox = Ext.extend(Ext.form.ComboBox,{
		typeAhead: true,
		emptyText:'Select a database',
		forceSelection: true,
        triggerAction: 'all',
        displayField: 'db_name',
        valueField: 'db_name',
		mode:'local',
		constructor:function(config){
			Ext.apply(this,config);
			this.store = new Ext.data.Store({
					url: '/webplsql.php?c=authdb',
					reader:new eF.jsonReader({root:'data',idProperty:'db_name'}, Ext.data.Record.create(['db_name']))
			});
			this.store.load();
			eF.adminDbComboBox.superclass.constructor.call(this);
		}
	});
	//表格下拉选单,没办法，用全局变量。
	eF.adminDbTables = Ext.extend(Ext.form.ComboBox,{
        typeAhead: true,
		emptyText:'Select a table',
		forceSelection: true,
        triggerAction: 'all',
        displayField: 'table_name',
        valueField: 'table_name',
		getParams:function(){
			return {db_name:eF.db.db_name};
		},
		onTriggerClick :function(){
			if(this.readOnly || this.disabled){
				return;
			}
			
			
			if(this.isExpanded()){
				this.collapse();
				this.el.focus();
			}else {
				this.onFocus({});
				if(eF.db.db_name != eF.db.old_db_name) {
					if(this.triggerAction == 'all') {
						this.doQuery(this.allQuery, true);
					} else {
						this.doQuery(this.getRawValue());
					}
				} else {
					this.expand();
				}
				this.el.focus();
			}
			
			eF.db.old_db_name = eF.db.db_name;
		},
		queryParam :'db_name',
		constructor:function(config){
			Ext.apply(this,config);
			this.store = new Ext.data.Store({
					url: '/webplsql.php?c=tables',
					reader:new eF.jsonReader({root:'data',idProperty:'table_name'}, Ext.data.Record.create(['table_name']))
			});
			var inthis = this;
			this.store.on('load',function(){
				inthis.bindStore(inthis.store,false);
				inthis.expand();
			});
			eF.adminDbTables.superclass.constructor.call(this);
		}
	});
	/*
		一些窗口封装函数
	*/
	var bugWin = null;
	eF.adminDbshowBugWin = function() {
		if(bugWin == null) {
			bugWin = new Ext.Window({
				title:'BUG、修改建议 提交与反馈',height:300,width:500,layout:'fit',
				closeAction:'hide',
				items:{xtype:'textarea'},
				tbar:['提交前请先查看是否已经有同样BUG被提交过了。'],
				buttons:[{
					text:'提交',
					handler:function(btn){
						var inthis = btn.ownerCt.ownerCt;
						var bugs = inthis.items.items[0].getValue();
						if(bugs.length < 5) {
							Ext.Msg.alert('错误','请输入详细bug信息，最好包含bug被发现时使用的db和table,也可以附上修改建议');
							return false;
						}
						inthis.hide();
						eF.ajax({
							url:'modifybugs',
							method:'post',
							params:{"bugs":bugs},
							success:function(){
								Ext.Msg.alert("消息","提交成功，Thanks!");
								inthis.items.items[0].setValue('');
							},
							failure:function(){
								inthis.show();
							}
						});
					}
				},{
					text:'查看bug',
					handler:function(){
						if(eF.adminDbBuglist){
							eF.adminDbBuglist();
						} else {
							Ext.Msg.alert("消息","此功能未实现");
						}
					}
				}]
			});
			
		}
		bugWin.show();
	}
	/*
		获取预存sql
	*/
	eF.adminDbUserSqlList = Ext.extend(Ext.grid.GridPanel,{
		stripeRows : true,
		gridSearch : function(user_id) {
			this.getStore().load();
		},
		columns:[{
				dataIndex:'title',	header:'标题'
			},{
				dataIndex:'db_name',	header:'表名',width:80
			}],
		getTBar:function() {
			return [{
				text:'删除',handler:function(btn) {
					var g = btn.ownerCt.ownerCt;
					record = g.getSelectionModel().getSelected();
					if(record) {
						eF.ajax({
							url:'deletesql',params:{title:record.get('title')},
							success:function(){
								g.getStore().remove(record);
								//g.getStore().
							}
						});
					} else {
						Ext.Msg.alert('错误','单击选择一个预存的SQL');
					}
				}
			}]
		},
		viewConfig :{forceFit:true},
		constructor:function(config){
			this.store = new Ext.data.Store({
					url: '/webplsql.php?c=usersql',
					reader:new eF.jsonReader({root:'data',idProperty:'title',fields:['title','sql','db_name']})
			});
			this.tbar = this.getTBar();
			Ext.apply(this,config);
			eF.adminDbList.superclass.constructor.call(this);
		}
	});
})();