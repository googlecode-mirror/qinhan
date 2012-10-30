var g_siteUrl = '/index.php?s=';
var g_staticUrl = '/CDN/app';

function version_img(i){
	return g_staticUrl + '/img/' + i;
}

function ui_sex(sex,type){
	var rs = '';
	if(sex==1){
		if(type==0){
			rs = '男' ;
		}else{
			rs = '他' ;
		}
	}else if(sex==2){
		if(type==0){
			rs = '女' ;
		}else{
			rs = '她' ;
		}
	}else{
		rs = 'TA' ;
	}

	return rs ;
}

function ui_check_face(face,checking_face){
	if(face == '' && checking_face== ''){
		return 0 ;
	}else {
		return 1 ;
	}
}

function addFavorite() {
	var url = 'http://jianjiandandan.ivu1314.com/';
	var title = '简简单单网';
	try {
		window.external.addFavorite(url, title);
	} catch (e){
		try {
			window.sidebar.addPanel(title, url, '');
        } catch (e) {
			alert("请按 Ctrl+D 键添加到收藏夹");
		}
	}
}