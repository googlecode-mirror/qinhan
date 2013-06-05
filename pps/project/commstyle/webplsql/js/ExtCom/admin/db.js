/*
* �ṩĿǰpps�Ŀɲ������ݿ��б�
* @author			���ӿ� (Kevin)
* @copyright		PPS
* @E-mail			328725540@qq.com kevin@dev.ppstream.com
* @version			1.1
*/

(function() {
	//���ݿ��б�������������ҳ
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
			return "<a style='color:red;' href=\"javascript:eF.adminDbChangeAuth(" + rowIndex + ",'" + column + "','" + store_id + "')\"> �� </a>";
		} else {
			return "<a href=\"javascript:eF.adminDbChangeAuth(" + rowIndex + ",'" + column + "','" + store_id + "')\"> �� </a>";
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
				dataIndex:'db_name',	header:'���ݿ���'
			},{
				dataIndex:'read_flag',	header:'�ɶ�',renderer:authRenderer
			},{
				dataIndex:'write_flag',	header:'��д',renderer:authRenderer
			},{
				dataIndex:'delete_flag',	header:'��ɾ',renderer:authRenderer
			}],
		getTbar:function() {
			return [{text:'�������ݿ�',
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
				title:'���ݿ�',width:250,height:130,bodyStyle:'padding:5px;',modal:true,
				layout:'form',
				items:[{
					fieldLabel:'���ݿ���',
					xtype:'textfield',width:100
				}],
				buttons:[{
					text:'����',
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
			win.setTitle('�޸����ݿ� - ' + db_name);
		} else {
			win.setTitle('�������ݿ�');
			win.old_db_name = '';
		}
		win.show();
	}
	eF.adminDbInfo.win = null;

	/*
		���²�������ȫ�ֱ�����
	*/
	eF.db = {};
	eF.db.setDb = function(db_name) {
		eF.db.db_name = db_name;
	};
	eF.db.db_name = '';
	eF.db.old_db_name = 'null';
	/*
		���ݿ������б�
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
	//�������ѡ��,û�취����ȫ�ֱ�����
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
		һЩ���ڷ�װ����
	*/
	var bugWin = null;
	eF.adminDbshowBugWin = function() {
		if(bugWin == null) {
			bugWin = new Ext.Window({
				title:'BUG���޸Ľ��� �ύ�뷴��',height:300,width:500,layout:'fit',
				closeAction:'hide',
				items:{xtype:'textarea'},
				tbar:['�ύǰ���Ȳ鿴�Ƿ��Ѿ���ͬ��BUG���ύ���ˡ�'],
				buttons:[{
					text:'�ύ',
					handler:function(btn){
						var inthis = btn.ownerCt.ownerCt;
						var bugs = inthis.items.items[0].getValue();
						if(bugs.length < 5) {
							Ext.Msg.alert('����','��������ϸbug��Ϣ����ð���bug������ʱʹ�õ�db��table,Ҳ���Ը����޸Ľ���');
							return false;
						}
						inthis.hide();
						eF.ajax({
							url:'modifybugs',
							method:'post',
							params:{"bugs":bugs},
							success:function(){
								Ext.Msg.alert("��Ϣ","�ύ�ɹ���Thanks!");
								inthis.items.items[0].setValue('');
							},
							failure:function(){
								inthis.show();
							}
						});
					}
				},{
					text:'�鿴bug',
					handler:function(){
						if(eF.adminDbBuglist){
							eF.adminDbBuglist();
						} else {
							Ext.Msg.alert("��Ϣ","�˹���δʵ��");
						}
					}
				}]
			});
			
		}
		bugWin.show();
	}
	/*
		��ȡԤ��sql
	*/
	eF.adminDbUserSqlList = Ext.extend(Ext.grid.GridPanel,{
		stripeRows : true,
		gridSearch : function(user_id) {
			this.getStore().load();
		},
		columns:[{
				dataIndex:'title',	header:'����'
			},{
				dataIndex:'db_name',	header:'����',width:80
			}],
		getTBar:function() {
			return [{
				text:'ɾ��',handler:function(btn) {
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
						Ext.Msg.alert('����','����ѡ��һ��Ԥ���SQL');
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