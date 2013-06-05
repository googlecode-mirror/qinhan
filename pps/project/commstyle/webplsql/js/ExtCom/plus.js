/*
	动态GRID的 columns
	用reconfig 避免重复创建新的grid
*/
Ext.onReady(function(){
Ext.data.DynamicJsonReader = function(config){
    Ext.data.DynamicJsonReader.superclass.constructor.call(this, config, []);
};
Ext.extend(Ext.data.DynamicJsonReader, Ext.data.JsonReader, {
    getRecordType : function(data) {
        var i = 0, arr = [];
        for (var name in data[0]) { arr[i++] = name; } // is there a built-in to do this?

        this.recordType = Ext.data.Record.create(arr);
        return this.recordType;
        },

    readRecords : function(o){ // this is just the same as base class, with call to getRecordType injected
		var success = true;
		if(o.error > 0 ) {
			Ext.Msg.alert('错误',o.msg);
		} else if(o.msg.length > 0){
			Ext.Msg.alert('消息',o.msg);
		}
		var recordType;
		var fields;
		if(o.data) {//更新meta
			recordType = this.getRecordType(o.data);
			fields = recordType.prototype.fields;
			var metaData = this.meta;
			metaData.fields = fields.items;
			metaData.idProperty = 'db_row_id';
			this.onMetaChange(metaData);
		}
        this.jsonData = o;
        var s = this.meta;
    	var sid = s.id;
    	var totalRecords = 0;
		var records = [];
		
		if(s.successProperty){
            v = this.getSuccess(o);
            if(v === false || v === 'false'){
                success = false;
            }
        }

		totalRecords = o.c;
        //
        Ext.getCmp('query_time').setText('耗时' + o.query_time);
        //
    	var root = s.root ? eval("o." + s.root) : o;
    	recordType = this.getRecordType(root);
    	fields = recordType.prototype.fields;

        
	    for(var i = 0; i < root.length; i++){
		    var n = root[i];
	        var values = {};
	        var id = (n[sid] !== undefined && n[sid] !== "" ? n[sid] : null);
	        for(var j = 0, jlen = fields.length; j < jlen; j++){
	            var f = fields.items[j];
	            var map = f.mapping || f.name;
	            var v = n[map] !== undefined ? n[map] : f.defaultValue;
	            v = f.convert(v);
	            values[f.name] = v;
	        }
	        var record = new recordType(values, id);
	        record.json = n;
	        records[records.length] = record;
	    }

	    return {
			success : success,
	        records : records,
	        totalRecords : totalRecords || records.length
	    };
    }
});
eF.jsonReader = function(config){
    eF.jsonReader.superclass.constructor.call(this, config, []);
};
Ext.extend(eF.jsonReader,Ext.data.JsonReader, {
	readRecords : function(o){
		var success = true;
		if(o.error > 0 ) {
			Ext.Msg.alert('错误',o.msg);
			o.data = [];o.c = 0;
		} else {
			if(o.msg.length > 0)
				Ext.Msg.alert('消息',o.msg);
		}

        this.jsonData = o;
        if(o.metaData){
            this.onMetaChange(o.metaData);
        }
		
        var s = this.meta, Record = this.recordType,f = Record.prototype.fields,fi = f.items, fl = f.length, 
			v,root = o.data,c = root.length,totalRecords = c,sid = s.id;

        if(s.totalProperty){
            v = o.c ? o.c : eval("o." + s.totalProperty);
			if(v !== undefined) totalRecords = v;
        }
		var records = [];
		var n = null;

		for(var i = 0; i < root.length; i++){
			n = root[i];
			var id = (n[sid] !== undefined && n[sid] !== "" ? n[sid] : null);
	        var record = new Record(root[i], n[sid]);
	        record.json = n;
	        records[records.length] = record;
	    }
        // TODO return Ext.data.Response instance instead.  @see #readResponse

        return {
            success : success,
            records : records, // <-- true to return [Ext.data.Record]
            totalRecords : totalRecords
        };
    }
});
/*
Ext.grid.DynamicColumnModel = function(store){
	var cols = [];
	var recordType = store.recordType;
	var fields = recordType.prototype.fields;
	
	for (var i = 0; i < fields.keys.length; i++) {
		var fieldName = fields.keys[i];
		var field = recordType.getField(fieldName);
		cols[i] = {header: field.name, dataIndex: field.name, width:300};
	}
	Ext.grid.DynamicColumnModel.superclass.constructor.call(this, cols);
};
Ext.extend(Ext.grid.DynamicColumnModel, Ext.grid.ColumnModel, {});
*/
Ext.grid.DynamicColumnModel = Ext.extend(Ext.grid.ColumnModel,{
	constructor:function(store){
		var cols = [];
		var recordType = store.recordType;
		var fields = recordType.prototype.fields;
		var record = store.getAt(0);
		var fieldName = '',field='',l;
		var width = 150,editable =false;
		cols[0] = new Ext.grid.RowNumberer();
		for(var i = 0; i < fields.keys.length; i++) {
			fieldName = fields.keys[i];
			if(fieldName == 'db_row_id') {
				editable = true;
				break;
			}
		}
		for (var i = 0; i < fields.keys.length; i++) {
			fieldName = fields.keys[i];
			field = recordType.getField(fieldName);
			if(record) {
				l = record.get(field.name).length;
				if(l > 80 ) {
					width = 400;
				} else if( l > 20) {
					width = 300;
				}
			}
			cols[i+1] = {header: field.name, dataIndex: field.name,sortable :true,width:width,renderer:function( str ){
                return  document.createElement('b').appendChild(document.createTextNode(str)).parentNode.innerHTML;}};
			if(editable && fieldName != 'db_row_id') {
				cols[i+1].editor = {xtype: 'textfield'};
			}
		}
		this.editable = editable;
		Ext.grid.DynamicColumnModel.superclass.constructor.call(this, cols);
	}
});
//动态grid

	eF.dynamicGrid = Ext.extend(Ext.grid.GridPanel,{
		constructor:function(config){
			var editable = false;
			var initialized = false;
			Ext.apply(this,config);
			this.store = new Ext.data.Store({
				proxy: new Ext.data.HttpProxy({
					url: config.searchUrl,method:'post',
					api: {
						update:{
							url:'update',
							method:'post'
						},
						create:{
							url:'update',
							method:'post'
						}
					}
					}),		
				writer:new Ext.data.JsonWriter({writeAllFields :true}),
				reader: new Ext.data.DynamicJsonReader({root: 'data'}),
				remoteSort: false,
				totalProperty:'c'
			});
			this.selModel = new Ext.grid.RowSelectionModel({singleSelect:true});
			this.enableColLock = true;
			var inthis = this;
			var store = this.store;
			var editor = new Ext.ux.grid.RowEditor({
					saveText: 'Update'
				});
				window.ed = editor;
			this.store.on('load',function(){
				store.recordType = store.reader.recordType;
				store.fields = store.recordType.prototype.fields;
				var cm = new Ext.grid.DynamicColumnModel(store);
				inthis.reconfigure(store,cm);
				editable = cm.editable;
			});
			//重新载入时移除旧的数据 
			this.store.on('beforeload',function(){
				if(editor && initialized) {
					editor.stopEditing(false);
					editor.removeOld();
				}
			});
			
			this.plugins = [editor];

			this.bbar = new Ext.PagingToolbar({
						pageSize: 25,
						store: this.store,
						displayInfo: true,
						displayMsg: '显示数据 {0} - {1} 预计总数 {2} &nbsp;&nbsp;&nbsp;&nbsp;',
						emptyMsg: "没有数据"
					});
			eF.dynamicGrid.superclass.constructor.call(this);
			initialized = true;
		}
	});
});