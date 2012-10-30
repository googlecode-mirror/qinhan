String.prototype.lengthByte = function() {
	return this.replace(/[^\x00-\xff]/g, "**").length
};
String.prototype.trim = function() {
	return this.replace(/(^\s*)|(\s*$)/g, "")
};
function substrByte(A, D) {
	A = A.replace(/\</ig, "&lt;");
	A = A.replace(/\>/ig, "&gt;");
	if (!A || !D) return "";
	var B = 0,
	E = 0,
	C = "";
	for (E = 0; E < A.length; E++) {
		if (A.charCodeAt(E) > 255) B += 2;
		else B++;
		if (B > D) return C;
		C += A.charAt(E)
	}
	return A
}
var gURL_WWW = "",
gURL_IMG = "http://jjdd01.ivu1314.com/",
ConnectAlive = false,
is_login = false,
gLoginNums = 0,
gSendTime = 0,
gSETTIME = 0,
gMSGTITLE = new Array(),
gShowNum = 0,
gWindow = 0,
gCurrTime = 0,
gNewsMsg = "",
gTime = 0,
Cookies = {};
Cookies.set = function(C, H) {
	Cookies.clear(C);
	var G = arguments,
	B = arguments.length,
	A = (B > 2) ? G[2] : null,
	F = (B > 3) ? G[3] : "/",
	E = (B > 4) ? G[4] : "",
	D = (B > 5) ? G[5] : false;
	document.cookie = C + "=" + escape(H) + ((A == null) ? "": ("; expires=" + A.toGMTString())) + ((F == null) ? "": ("; path=" + F)) + ((E == null) ? "": ("; domain=" + E)) + ((D == true) ? "; secure": "")
};
Cookies.get = function(B) {
	var A = document.cookie.split("; "),
	E = A.length;
	for (var D = 0; D < E; D++) {
		var C = A[D].split("=");
		if (B == C[0]) return unescape(C[1])
	}
	delete A;
	return null
};
Cookies.clear = function(A) {
	if (Cookies.get(A)) document.cookie = A + "=" + "; path=/; domain=jianjiandandan.ivu1314.com; expires=Thu, 01-Jan-70 00:00:01 GMT"
};
var LStorage = {
	syncself: 0,
	issync: 0,
	support: window.localStorage,
	json: (window.JSON && window.JSON.parse),
	length: function() {
		if (!this.support) return 0;
		return window.localStorage.length
	},
	set: function(D, A) {
		if (!this.support) return false;
		if (this.json) A = JSON.stringify(A);
		if ($.browser.msie && parseInt($.browser.version) <= 8) {
			if (this.syncself) {
				var C = Cookies.get("dxslaw_key");
				if (C) {
					setTimeout(function() {
						if (LStorage.json) A = JSON.parse(A);
						LStorage.set(D, A)
					},
					100);
					return
				}
			}
			this.syncself = (new Date).getTime();
			var B = D + ":" + this.syncself;
			Cookies.set("dxslaw_key", B, (new Date(this.syncself + 1000)))
		}
		this.issync = 1;
		if ($.browser.msie) setTimeout(function() {
			LStorage.issync = 0
		},
		100);
		return window.localStorage.setItem(D, A)
	},
	get: function(B) {
		if (!this.support) return false;
		var A = window.localStorage.getItem(B);
		if (this.json) return JSON.parse(A);
		else return A
	},
	del: function(A) {
		if (!this.support) return false;
		return window.localStorage.removeItem(A)
	},
	clear: function() {
		if (!this.support) return false;
		return window.localStorage.clear()
	},
	addEvent: function(A) {
		if (!this.support) return false;
		if (document.attachEvent && !$.browser.opera && ($.browser.msie && parseInt($.browser.version) < 9)) document.attachEvent("onstorage", A, false);
		else window.addEventListener("storage", A, false)
	}
};
function $I(A) {
	return document.getElementById(A)
}
var gStorage = (!($.browser.msie && $.browser.version < 8) && (LStorage.support && LStorage.json)) ? 1: 0;
function socket_send(C) {
	if (C.trim() == "") return false;
	try {
		var B = $I("sock");
		B.socket_send(C)
	} catch(A) {}
}
function socket_init() {
	rsync_action();
	IM.gFlash = 1
}
var gConnect = false;
function isReady() {
	gConnect = true;
	return true
}
function socket_ondata(F) {
	var C = F.split(":");
	switch (C[0]) {
	case "NOTIFY":
		var A = F.substring(7).split(",");
		if (parseInt(A[0]) == 11) {
			var G = Cookies.get("gChatWin"),
			B = [];
			if (G) B = G.split("|");
			if (B && (new Date).getTime() - B[0] < 3000 && B[1] == A[1]) IM.gChatOpen = 1;
			else IM.gChatOpen = 0;
			if (g_chat_msg == 1) {
				var L = "",
				I = F.substring(7).split(",");
				send_id = I[1];
				recv_id = gUid;
				L = I.slice(2).join(",").split("||");
				var J = L[1];
				if (L[0] == 10000) var D = 2;
				else D = 0;
				IM.chatShow({
					"send": send_id,
					"recv": recv_id,
					"time": J,
					"msg": decodeURIComponent(L[2]),
					"type": D
				});
				$("#f_" + send_id + " .status_icon").addClass("online");
				IM.soundPlay()
			} else if (IM.gChatOpen == 0) IM.popShow({
				"type": A[0],
				"uid": A[1],
				"data": A[2]
			})
		} else IM.popShow({
			"type": A[0],
			"uid": A[1],
			"data": A[2]
		});
		break;
	case "SENDTO":
		L = "",
		I = F.substring(7).split(",");
		send_id = I[0];
		recv_id = I[1];
		J = I[2];
		L = I.slice(3).join(",");
		IM.chatShow({
			"send": send_id,
			"recv": recv_id,
			"time": J,
			"msg": decodeURIComponent(L),
			"type": 0
		});
		IM.soundPlay();
		break;
	case "ERROR":
		break;
	case "ACTION":
		if (C[1] == "ONLINE" && g_chat_msg == 1) {
			IM.gChatOpen = 1;
			$(".userlist span").removeClass("online");
			var H = F.substring(14).split(",");
			for (var E = 0; E < H.length; E++) {
				if (H[E] == "") continue;
				var M = H[E].split(":");
				$("#f_" + M[0] + " .status_icon").addClass("online")
			}
		} else if (C[1] == "TYPESTATE") {
			var K = C[2].split(",");
			if (g_chat_msg == 1) {
				IM.type(K[0]);
				IM.gChatOpen = 1
			}
		} else if (C[1] == "LOGIN_OK") IM.getLoseMsg();
		break;
	case "RSYNC":
		if (C[1] != "") $("#left_newsbox").html(unescape(C[1]));
		break;
	case "RSYNCCHATOPEN":
		IM.gChatOpen = C[1];
		break;
	case "RSYNCASK":
		if (g_chat_msg == 1) rsync_action();
		break;
	case "RSYNCMINUS":
		if (g_chat_msg == 0) IM.popShow({
			"type": 12,
			"uid": C[1],
			"data": C[2]
		});
		break;
	case "RSYNCMINUSANSWER":
		if (g_chat_msg == 0) IM.popShow({
			"type": 17,
			"uid": C[1],
			"data": C[2]
		});
		break
	}
}
function socket_config() {
	gCurrTime = (new Date).getTime();
	if ($.browser.msie) var B = "ie";
	else B = "other";
	var A = gStorage;
	if (($.browser.mozilla && $.browser.version > 4) || window.navigator.userAgent.indexOf("Chrome") >= 0 || ($.browser.msie && parseInt($.browser.version) < 9)) A = 0;
	return {
		"uid": gUid,
		"ip": "60.190.39.7",
		"plolicyPort": 843,
		"connPort": 8000,
		"loginStr": "LOGIN," + gUid + "," + gVip + "," + gSex + "," + gAuthKey,
		"isChat": g_chat_msg,
		"sync": A,
		"version": B + $.browser.version
	}
}
function socket_close() {
	if (g_chat_msg == 1) $("#pop_list").html("\u7f51\u7edc\u5df2\u65ad\u5f00\uff0c\u6b63\u5728\u8fde\u63a5\u4e2d...").show()
}
function reload_chat_swf() {
	var A = $("#imflash").html();
	$("#imflash").html("");
	A = A.replace(/chat\.swf\?v=[\d]+/g, "chat.swf?v=" + (new Date).getTime());
	$("#imflash").html(A)
}
var client_heart_timeout = 0;
function client_heart_start() {
	try {
		var B = $I("sock");
		B.client_heart()
	} catch(A) {}
	clearTimeout(client_heart_timeout);
	client_heart_timeout = setTimeout("client_heart_start()", 2000)
}
function rsync_action() {
	if (gStorage) {
		if (g_chat_msg == 1) LStorage.set("dxslaw_chatopen", 1);
		else {
			var A = $("#left_newsbox").html();
			if (! (!A || A.length < 50)) LStorage.set("dxslaw_newsbox", A)
		}
	} else if (g_chat_msg != 1) {
		A = $("#left_newsbox").html();
		if (! (!A || A.length < 50)) {
			var C = "RSYNC:" + escape(A),
			B = $I("sock");
			B.rsync_action(C)
		}
	}
}
function rsync_msg(A) {
	LStorage.set("dxslaw_newmsg", A)
}
function rsync_ondata(B) {
	if (LStorage.issync && $.browser.msie) {
		LStorage.issync = 0;
		return
	}
	var A = B.newValue;
	if (LStorage.json && B.newValue) A = JSON.parse(B.newValue);
	if (!B.key) {
		var C = Cookies.get("dxslaw_key");
		if (C) {
			var D = C.split(":");
			if (D[1] == LStorage.syncself) return;
			B.key = D[0];
			A = LStorage.get(B.key)
		}
	}
	switch (B.key) {
	case "dxslaw_chatopen":
		IM.gChatOpen = A;
		if (IM.gChatOpen == 0) Cookies.set("gChatWin", 0 + "|" + IM.gSendToUid);
		break;
	case "dxslaw_newsbox":
		$("#left_newsbox").html(A);
		break;
	case "dxslaw_newmsg":
		socket_ondata(A);
		break
	}
}
var new_mail_call_enable = false,
window_title = document.title,
new_mail_style = 0;
function new_mail_call() {
	if (new_mail_call_enable) {
		if (new_mail_style == 0) {
			new_mail_style = 1;
			$("#new_mail_call").css("display", "");
			document.title = "\u3010\u60a8\u6709\u65b0\u4fe1\u606f\u3011" + window_title
		} else {
			new_mail_style = 0;
			$("#new_mail_call").css("display", "none");
			document.title = window_title
		}
		setTimeout("new_mail_call()", 500)
	}
}
function minus_unread(D, B, A) {
	switch (D) {
	case "msg":
		gNewsMsg = "RSYNCMINUS:" + A + ":" + B + ":" + (new Date).getTime();
		break;
	case "answer":
		gNewsMsg = "RSYNCMINUSANSWER:" + A + ":" + B + ":" + (new Date).getTime();
		IM.popShow({
			"type": 17,
			"uid": A,
			"data": B
		});
		break
	}
	if (gStorage) rsync_msg(gNewsMsg);
	else {
		var C = 3000;
		if (IM.gFlash) C = 0;
		setTimeout(function() {
			var A = $I("sock");
			A.rsync_action(gNewsMsg)
		},
		C)
	}
}
var IM = {
	gShowLog: false,
	gUserList: [],
	gCurrentWin: 0,
	gMsgType: ["", "\u6b63\u5728\u6d4f\u89c8\u4e86\u60a8\u7684\u8d44\u6599!", "\u7ed9\u4f60\u6253\u4e86"],
	gUserInfo: {},
	gSendTime: 0,
	gSendToUid: 82007389,
	gFriends: [],
	gPicUrl: "http://jjdd01.ivu1314.com/CDN/app/img/",
	g_timeOut: 0,
	g_titleTimeout: 0,
	gChatOpen: 0,
	gFriendNew: "",
	gDefaultTxt: ($.browser.mozilla) ? "": "",
	gTyping: 0,
	gSendStatus: 0,
	gMsgTotal: 0,
	gAjaxSend: false,
	gFlash: 0,
	gFocus: true,
	gBlurTime: 0,
	gAjaxTime: 600000,
	gSayNum: 0,
	gFirstUid: 0,
	gStatus: window.webkitNotifications,
	gOpt: {
		id: 10000,
		title: "\u7b80\u7b80\u5355\u5355",
		icon: "http://jjdd01.ivu1314.com/CDN/app/img/logo.gif",
		timeout: (new Date).getTime() / 1000
	},
	gIsOpen: false,
	gUpChatTime: 0,
	RequestPermission: function(A) {
		window.webkitNotifications.requestPermission(A)
	},
	popup: function(C) {
		if (!this.gStatus) return;
		if (C) for (var B in C) IM.gOpt[B] = C[B];
		if (IM.gOpt.msg == "") return;
		if (window.webkitNotifications.checkPermission() > 0) IM.RequestPermission(IM.popup);
		else {
			var A = window.webkitNotifications.createNotification(IM.gOpt.icon, IM.gOpt.title, IM.gOpt.msg);
			A.replaceId = IM.gOpt.id;
			A.onclick = function() {
				var B = Cookies.get("gChatWin"),
				C = [];
				if (B) C = B.split("|");
				if (C && (new Date).getTime() - C[0] < 3000) {
					var D = window.open("javascript:IM.chat(" + A.replaceId + ")", "dxslawchat", "height=550,width=750,top=200,left=200,toolbar=no,menubar=no,scrollbars=no,location=no,status=no");
					D.focus()
				} else window.open("http://jianjiandandan.ivu1314.com/index.php?s=/msg");
				A.cancel()
			};
			setTimeout(function() {
				A.cancel()
			},
			IM.gOpt.timeout);
			A.show()
		}
	},
	showTime: function(B, F) {
		B = B.toString().substring(0, 10);
		var A = new Date(),
		C = new Date(parseInt(B) * 1000),
		D = C.getHours();
		if (D < 10) D = "0" + D;
		var E = C.getMinutes();
		if (E < 10) E = "0" + E;
		var G = C.getSeconds();
		if (G < 10) G = "0" + G;
		if (F == 1) return C.getFullYear() + "-" + (C.getMonth() + 1) + "-" + C.getDate();
		if (A.getYear() != C.getYear()) return C.getFullYear() + "-" + (C.getMonth() + 1) + "-" + C.getDate() + " " + D + ":" + E + ":" + G;
		if (A.getMonth() != C.getMonth()) return C.getFullYear() + "-" + (C.getMonth() + 1) + "-" + C.getDate() + " " + D + ":" + E + ":" + G;
		if (A.getDate() != C.getDate()) return C.getFullYear() + "-" + (C.getMonth() + 1) + "-" + C.getDate() + " " + D + ":" + E + ":" + G;
		return D + ":" + E + ":" + G
	},
	fInObj: function(B, A) {
		if (B) if (B.className == A || B.id == A) return true;
		else if (B.parentNode) return this.fInObj(B.parentNode, A);
		else return false
	},
	soundPlay: function() {
		if ($("#im_sound").attr("title") == "\u5173\u95ed\u58f0\u97f3") $I("sock").sounder_play()
	},
	soundSet: function() {
		if ($("#im_sound").attr("title") == "\u5173\u95ed\u58f0\u97f3") $("#im_sound").attr("title", "\u6253\u5f00\u58f0\u97f3").attr("src", "http://jjdd01.ivu1314.com/CDN/app/img/nosound.gif");
		else $("#im_sound").attr("title", "\u5173\u95ed\u58f0\u97f3").attr("src", "http://jjdd01.ivu1314.com/CDN/app/img/sound.gif");
		if (this.gStatus) if (window.webkitNotifications.checkPermission() > 0) IM.RequestPermission(IM.isMinStatus)
	},
	open: function(A) {
		if (myuserinfo.login_times > 6 && myuserinfo.profile_completed < 0.5 && !(A == 10000001 || A == 10000002 || A == 10000)) {
			var F = sysmessage_addprofile("chat", (myuserinfo.sex == 1) ? 2: 1);
			Win.dialog({
				width: 460,
				msg: F
			});
			return false
		}
		var D = "";
		for (var I in navigator) {
			if (typeof navigator[I] == "function") continue;
			if (typeof navigator[I] == "object") continue;
			if (navigator[I] == "") continue
		}
		var E = "";
		if (window.ActiveXObject) {
			try {
				var H = new ActiveXObject("ShockwaveFlash.ShockwaveFlash");
				if (H) {
					IM.gFlash = 1;
					D += "ActiveXObject=" + H.GetVariable("$version") + "|"
				}
			} catch(B) {}
		}
		if (navigator.plugins) for (var J = 0; J < navigator.plugins.length; J++) if (navigator.plugins[J].name.toLowerCase().indexOf("shockwave flash") >= 0) this.gFlash = 1;
		if (window.localStorage) D += "storage=1|";
		if (window.WebSocket) D += "websocket=1|";
		D += "type=" + this.gFlash;
		$.post("/index.php?s=/msg/browser", {
			info: D
		});
		var G = window.open(gURL_WWW + '/index.php?s=/msg/look?u=' + A, "dxslawchat", "height=550,width=750,top=200,left=200,toolbar=no,menubar=no,scrollbars=no,location=no,status=no");
		G.focus()
	},
	init: function() {
		if (!$.browser.msie) $("#imflash").html("<embed src=\"" + g_staticUrl + "/flash/chat.swf\" quality=\"high\"  width=\"0px\" height=\"0px\" id=\"sock\" name=\"sock\" align=\"middle\" allowScriptAccess=\"sameDomain\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" />");
		this.gUserList[10000] = {
			uid: 10000,
			nickname: "\u7cfb\u7edf\u901a\u77e5",
			face: version_img('noface/systemface01_48.jpg')
		};
		if (parseInt($("#_new_mail_num").html()) > 0) {
			new_mail_call_enable = true;
			new_mail_call()
		}
		if (window.ActiveXObject) {
			try {
				var B = new ActiveXObject("ShockwaveFlash.ShockwaveFlash");
				if (B) IM.gFlash = 1
			} catch(A) {}
		}
		if (navigator.plugins) for (var C = 0; C < navigator.plugins.length; C++) if (navigator.plugins[C].name.toLowerCase().indexOf("shockwave flash") >= 0) IM.gFlash = 1;
		if (IM.gFlash == 0) window.setTimeout("IM.ajax_new()", 10000);
		if (g_chat_msg == 1) {
			$(window).bind("resize",
			function() {
				if (IM.gSendToUid == 10000) $("#messages").css({
					height: document.documentElement.clientHeight - 93
				});
				else $("#messages").css({
					height: document.documentElement.clientHeight - 225
				})
			});
			$(document).bind("click",
			function(A) {
				if ($.browser.msie) var B = event.srcElement;
				else B = A.target;
				if (! (IM.fInObj(B, "tools clear") || IM.fInObj(B, "divFace"))) $("#chat_face").hide();
				if (!IM.fInObj(B, "u_opt")) $("#u_stop").hide();
				IM.flickerClose()
			});
			$(".userlist>ul>li").mouseover(function() {
				$(this).children(".user_delete").show()
			}).mouseout(function() {
				$(this).children(".user_delete").hide()
			});
			$("#chat_msg").html(this.gDefaultTxt).focus().bind("paste",
			function(A) {
				IM.chatPaste(A)
			}).bind("drop",
			function(A) {
				IM.chatDrop(A)
			});
			this.gUpChatTime = window.setInterval("IM.setChatOpen()", 1000);
			window.setTimeout("IM.friendOnline()", this.gAjaxTime);
			$("#chat_msg").html(this.gDefaultTxt);
			setRange($("#chat_msg")[0])
		}
		window.onbeforeunload = function() {
			if (parseInt(g_chat_msg) == 1) {
				window.clearTimeout(IM.gUpChatTime);
				Cookies.set("gChatWin", 0 + "|" + IM.gSendToUid);
				if (gStorage) LStorage.set("dxslaw_chatopen", 0)
			}
			if (!$.browser.msie) {
				try {
					var B = $I("sock");
					B.swf_unload()
				} catch(A) {}
			}
		};
		window.onblur = function() {
			IM.gFocus = false;
			IM.gBlurTime = (new Date).getTime()
		};
		window.onfocus = function() {
			IM.gFocus = true;
			IM.flickerClose();
			if (g_chat_msg == 1 && (new Date).getTime() - IM.gBlurTime > 60000 && IM.gBlurTime > 0) {
				IM.gBlurTime = 0;
				IM.friendOnline()
			}
		};
		if (gStorage) LStorage.addEvent(rsync_ondata);
		if (gShowLog == 1) {
			$("#imflash").css({
				"width": 400,
				"height": 400
			});
			$("#sock").css({
				"width": "100%",
				"height": "100%"
			})
		}
	},
	ajax_new: function() {
		$.post("/index.php?s=/msg/ajax_new", {
			time: gTime,
			flash: 1
		},
		function(B) {
			B.unread = B.news;
			if (B.unread > 0) {
				if ($("#_new_mail_num").length == 0) return;
				$("#_new_mail_num").html(Math.abs(parseInt(B.unread)));
				if (!new_mail_call_enable) {
					new_mail_call_enable = true;
					new_mail_call()
				}
			}
			window.setTimeout("IM.ajax_new()", 30000)
		},
		"json")
	},
	userGet: function(A) {
		if (A.uid == 0) return;
		$.getJSON("/msg/userinfo/?fid=" + A.uid, {},
		function(B) {
			if (A.uid == B.uid) {
				IM.gUserList[A.uid] = B;
				IM.popShow(A)
			}
		})
	},
	userInfo: function(G) {
		if (!G) return;
		var A = 0;
		for (var I in G) {
			if (!IM.gFriends[this.gSendToUid][I]) {
				IM.gFriends[this.gSendToUid][I] = G[I];
				continue
			}
			if (G[I] != IM.gFriends[this.gSendToUid][I]) {
				IM.gFriends[this.gSendToUid][I] = G[I];
				if (I == "face") $("#f_" + this.gSendToUid + " .ico").attr("src", G[I]);
				else if (I == "nickname") $("#f_" + this.gSendToUid + " .nickname").html(G[I]);
				A = 1
			}
		}
		if (A) $.post("/index.php?s=/msg/upcache/", {
			friend: this.gSendToUid
		});
		if (this.gSendToUid != 10000) {
			$("#chat_send").attr("disabled", false);
			if (IM.gFriends[this.gSendToUid].forbid == 1) {
				$("#friendtype").val(1);
				$("#forbid").html("<p><a onclick=\"IM.friendForbid(0)\" class=\"clear\"><span class=\"drpdwn_link2 fl\"></span><span class=\"fl p_l5\">\u53d6\u6d88\u963b\u6b62</span></a></p>");
				$("#chat_msg").attr("contenteditable", false)
			} else {
				$("#friendtype").val(0);
				$("#forbid").html("<p><a onclick=\"IM.friendForbid(1)\" class=\"clear\"><span class=\"drpdwn_link2 fl\"></span><span class=\"fl p_l5\">\u9759\u9ed8\u5904\u7406</span></a></p><p><a onclick=\"IM.friendForbid(2)\" class=\"clear\"><span class=\"drpdwn_link fl\"></span><span class=\"fl p_l5\">\u660e\u786e\u963b\u6b62</span></a></p>");
				$("#chat_msg").attr("contenteditable", true)
			}
			if (G.fav == 1) {
				$("#friend_fav").hide();
				$("#u_opt").addClass("perhome_chat2");
				if (G.remark) {
					var C = G.remark;
					if (C.length > 10) C = C.substring(0, 10) + "....";
					var H = "bz_on",
					E = G.remark,
					D = "\u4fee\u6539"
				} else C = "\u5907\u6ce8\u7535\u8bdd\u3001\u59d3\u540d\u7b49...",
				H = "bz_off",
				E = "\u5907\u6ce8\u7535\u8bdd\u3001\u59d3\u540d\u7b49...",
				D = "\u5907\u6ce8"
			} else {
				$("#friend_fav").show();
				C = "\u5907\u6ce8\u7535\u8bdd\u3001\u59d3\u540d\u7b49...",
				H = "bz_off",
				E = "\u5907\u6ce8\u7535\u8bdd\u3001\u59d3\u540d\u7b49...",
				D = "\u5907\u6ce8"
			}
			$("#u_opt").show();
			if (this.gFriends[this.gSendToUid].age != 0) var B = this.gFriends[this.gSendToUid].age + ", ";
			else B = "";
			var F = "<div class=\"bz\"><span class=\"" + H + "\">" + C + "</span><div class=\"bz_r\"><a class=\"bz_btn\" onclick=\"IM.addRemarkTable()\">" + D + "</a><div class=\"bz_window\" style=\"display:none;\"><textarea id=\"area_" + this.gSendToUid + "\" onclick=\"IM.canceltxt(" + this.gSendToUid + ");\">" + E + "</textarea><p><a onclick=\"IM.addRemark(" + this.gSendToUid + ");\" class=\"btn1\" id=\"remarkSave\">\u4fdd\u5b58</a><a class=\"btn3\" onclick=\"IM.remarkClose();\">\u53d6\u6d88</a></p></div></div></div>";
			$("#u_baseinfo").html("<a href=\"/" + this.gSendToUid + "/\" target=\"_blank\"><b>" + this.gFriends[this.gSendToUid].nickname + "</b></a> " + B + this.gFriends[this.gSendToUid].city + F)
		} else {
			$("#chat_send").attr("disabled", true);
			$("#chat_msg").attr("contenteditable", false);
			$("#u_opt").hide();
			$("#u_baseinfo").html("<b>" + this.gFriends[this.gSendToUid].nickname + "</b>")
		}
		document.title = "\u548c" + this.gFriends[this.gSendToUid].nickname + "\u6b63\u5728\u804a\u5929";
		if (G.is_mask == 2) {
			$("#u_face").attr("href", "/" + this.gSendToUid).html("<img width=\"72\" height=\"72\" alt=\"\" src=\"" + version_img("noface/vmske_72.png") + "\" title=\"\u8be5\u7528\u6237\u4f7f\u7528\u4e86V\u8d35\u5bbe\u9762\u5177\uff0c\u662f\u4f18\u8d28\u7537\u6027\u7528\u6237\">");
			$("#f_" + this.gSendToUid + " img").attr("src", version_img("noface/vmske_48.png"))
		} else $("#u_face").attr("href", "/" + this.gSendToUid).html("<img width=\"72\" height=\"72\" alt=\"" + G.is_mask + "\" src=\"" + this.gFriends[this.gSendToUid].face2 + "\">");
		$("#u_want").html(this.gFriends[this.gSendToUid].want);
		if (this.gSendToUid == 10000) return;
		if (!G.twoway) if (myuserinfo.sex == 1) $("#msg_history").html("<div style=\"color:#aaaaaa;background:#fbfbfb;line-height:120%\">\u63d0\u793a\uff1a\u8bf7\u52ff\u7740\u6025\u4e0e\u5973\u751f\u89c1\u9762\uff0c\u4e0d\u8981\u5b8c\u5168\u7528\u4e0b\u534a\u8eab\u601d\u8003\uff0c\u4f60\u81f3\u5c11\u6709\u4e00\u534a\u662f\u6587\u660e\u4eba\uff1b\u5f81\u5a5a\u5c31\u662f\u5f81\u5a5a\uff0c\u4ea4\u53cb\u5c31\u662f\u4ea4\u53cb\uff0c\u8981\u8d1f\u5f97\u8d77\u8d23\u4efb\uff0c\u5982\u679c\u8d1f\u4e0d\u8d77 \uff0c\u81f3\u5c11\u4e0d\u6b3a\u9a97\u3002\u8bf7\u52ff\u4e00\u5f00\u59cb\u804a\u5929\u5c31\u7d22\u8981\u5bf9\u65b9QQ\u3001\u7535\u8bdd\u7b49\u8054\u7cfb\u65b9\u5f0f\uff0c\u5927\u591a\u5973\u751f\u4f1a\u53cd\u611f\u3002\u5c0a\u91cd\u5973\u4eba\uff0c\u4ece\u8010\u5fc3\u5f00\u59cb\uff01\u7b80\u7b80\u5355\u5355jianjiandandan.ivu1314.com\u662f\u4e00\u4e2a \u4ea4\u5fc3\u4ea4\u53cb\u7684\u7f51\u7edc\u7a7a\u95f4\uff0c\u60a8\u53ef\u4ee5\u5728\u5de5\u4f59\u65f6\u95f4\uff0c\u6ce1\u676f\u5496\u5561\uff0c\u5728\u8fd9\u91cc\u4e0e\u964c\u751f\u7684\u670b\u53cb\u8c08\u4eba\u751f\u3001\u4e8b\u4e1a\u3001\u611f\u60c5\uff0c\u53ef\u4ee5\u6ca1\u6709\u4f2a\u88c5\uff0c\u65e0\u6240\u4e0d\u804a\u3002\u4ec5\u6b64\u800c\u5df2\uff0c\u8fd9 \u5df2\u7ecf\u662f\u4e00\u4ef6\u591a\u4e48\u8f7b\u677e\u5feb\u4e50\u7684\u4e8b\uff1a\uff09<br>\u795d\u60a8\u7b80\u7b80\u5355\u5355\uff01\u5feb\u5feb\u4e50\u4e50\uff01</div><div style=\"color:red;font-weight:bold\">\u8bf7\u52ff\u53d1\u5e03\u201c\u4e00\u591c\u60c5\u3001\u591a\u5c11\u94b1\u201d\u6216\u5176\u5b83\u4fae\u8fb1\u6027\u810f\u8bdd\uff0c\u7cfb\u7edf\u5bf9\u6b64\u7c7b\u5173\u952e\u5b57\u5c06\u81ea\u52a8\u76d1\u6d4b\u5e76\u5173\u95ed\u5e10\u53f7\u3002</div><p class=\"m_t5\"><a href=\"javascript:;\" onclick=\"if($('#c_dashan').css('display')!='none') {$('#c_dashan').hide();} else {$('#c_dashan').show();};\" class=\"f_r\">\u4e3a\u4ec0\u4e48\u5f88\u591a\u5973\u751f\u4e0d\u56de\u6d88\u606f\uff1f</a></p><div  class=\"m_t5 f_6\" id=\"c_dashan\" style=\"display:none\"><p>1\u3001\u4e0d\u8981\u8bf4\u201cHi\u3001\u4f60\u597d\u3001\u4f60\u662f\u54ea\u91cc\u4eba\u3001\u4f60\u53eb\u4ec0\u4e48..\u201d\u8fd9\u6837\u7684\u8bdd\uff0c\u8fd9\u6837\u7684\u8bdd\u592a\u591a\u4eba\u95ee\u4e86\uff0c\u5973\u751f\u61d2\u5f97\u56de\u7b54\u3002\u600e\u4e48\u529e\uff1a\u53bb\u770b\u5979\u7684\u5c0f\u7f16\u4e13\u8bbf\uff0c\u770b\u5979\u8d44\u6599\uff0c\u627e\u5230\u8bdd\u9898\uff01\u5b9e\u5728\u6ca1\u8bdd\u8bf4\uff0c\u4f60\u5c31\u5728\u5979\u7167\u7247\u4e0b\u65b9\uff0c\u70b9\u51fb\u9080\u8bf7\u5979\u4e0a\u4f20\u66f4\u591a\u7167\u7247\uff0c\u6216\u662f\u9080\u8bf7\u5979\u586b\u5199\u5c0f\u7f16\u4e13\u8bbf\uff0c\u8fd9\u6837\u7cfb\u7edf\u4f1a\u5e2e\u4f60\u53d1\u6d88\u606f\u7ed9\u5979\uff01\uff08\u8fd9\u62db\u5f88\u7ba1\u7528\uff09\u603b\u4e4b\uff0c\u5973\u751f\u559c\u6b22\u6709\u65b0\u610f\u4e00\u70b9\u7684\uff0c\u4e0d\u559c\u6b22\u65e0\u804a\u7684\u4eba\u3002</p><p class=\"m_t5\">2\u3001\u8981\u591a\u53d1\u6d88\u606f\uff0c\u4e00\u822c\u53d110\u4e2a\u4eba\u4f1a\u67093\u30014\u4e2a\u56de\u590d\uff0c\u4f60\u5982\u679c\u53d1\u5f97\u5c11\u53c8\u6ca1\u521b\u610f\uff0c\u5f88\u53ef\u80fd\u6ca1\u4eba\u56de\u4f60\u3002</p><p class=\"m_t5\">3\u3001\u586b\u5199\u597d\u4f60\u81ea\u5df1\u7684\u8d44\u6599\uff0c\u591a\u4f20\u4e24\u5f20\u7167\u7247\u3002\u4f60\u8d44\u6599\u7a7a\u7a7a\u7684\uff0c\u7167\u7247\u4e5f\u5c31\u4e00\u4e24\u5f20\uff0c\u8c01\u613f\u610f\u56de\u4f60\u5462\uff1f\u603b\u4e4b\uff1a\u8ba9\u81ea\u5df1\u8d44\u6599\u548c\u7167\u7247\u591a\u70b9+\u591a\u4e3b\u52a8\u53d1\u6d88\u606f+\u8bf4\u8bdd\u521b\u610f\u4e00\u70b9\uff01</p></div>");
		else $("#msg_history").html("<div style=\"color:#aaaaaa;background:#fbfbfb;line-height:120%\">\u63d0\u793a\uff1a\u8bf7\u52ff\u5c06\u7535\u8bdd\u3001MSN\u7b49\u8f7b\u6613\u544a\u8bc9\u522b\u4eba\uff0c\u6211\u4eec\u66f4\u4e0d\u63d0\u5021\u65e9\u671f\u7f51\u53cb\u89c1\u9762\u3002\u5927\u91cf\u4e8b\u5b9e\u8bc1\u660e\uff0c\u65e0\u8bba\u4ee5\u4f55\u79cd\u7406\u7531\u7740\u6025\u4e0e\u4f60\u89c1\u9762\u7684\u4eba\uff0c\u5927\u591a\u7f3a\u4e4f\u771f\u8bda\uff0c \u5e76\u5c06\u7ed9\u4f60\u5e26\u6765\u70e6\u607c\u3002\u7b80\u7b80\u5355\u5355jianjiandandan.ivu1314.com\u662f\u4e00\u4e2a\u4ea4\u5fc3\u4ea4\u53cb\u7684\u7f51\u7edc\u7a7a\u95f4\uff0c\u60a8\u53ef\u4ee5\u5728\u5de5\u4f59\u65f6\u95f4\uff0c\u6ce1\u676f\u5496\u5561\uff0c\u5728\u8fd9\u91cc\u4e0e\u964c\u751f\u7684\u670b\u53cb\u8c08\u4eba\u751f\u3001\u4e8b\u4e1a\u3001\u611f\u60c5\uff0c\u53ef\u4ee5\u6ca1\u6709\u4f2a\u88c5\uff0c\u65e0\u6240\u4e0d\u804a\u3002\u4ec5\u6b64\u800c\u5df2\uff0c\u8fd9\u5df2\u7ecf\u662f\u4e00\u4ef6\u591a\u4e48\u8f7b\u677e\u5feb\u4e50\u7684\u4e8b\uff1a\uff09<br>\u6211\u4eec\u4e5f\u5c06\u544a\u8bc9\u7537\u751f\uff1a\u5c0a\u91cd\u5973\u751f\uff0c\u4ece\u8010\u5fc3\u5f00\u59cb\u3002<br>\u795d\u60a8\u7b80\u7b80\u5355\u5355 \uff01\u5feb\u5feb\u4e50\u4e50\uff01</div>")
	},
	friendGet: function(A) {
		if (A.send == 0) return;
		$.getJSON("/msg/userinfo/?fid=" + A.send, {},
		function(B) {
			if (A.send == B.uid) {
				IM.gFriends[A.send] = B;
				if (A.visit == 1) IM.chatShow(A);
				else if (A.visit == 3) {
					if ($("#visit_face a").length > 3) $("#visit_face a:last").remove();
					$("#visit_face").prepend("<a target=\"_blank\" href=\"/" + B.uid + "\"><img width=\"38\" height=\"38\" border=\"0\" title=\"\" alt=\"\" src=\"" + B.face + "\"></a>")
				} else IM.chat(A.send)
			}
		})
	},
	getLoseMsg: function(E) {
		if (g_chat_msg) {
			$("#pop_list").html("\u8fde\u63a5\u6210\u529f").fadeOut(3000);
			var B = 0,
			C = $(".newmsg");
			for (var D in C) {
				var A = parseInt(C[D].innerHTML);
				if (isNaN(A)) A = 0;
				if (A > 0) B += A
			}
		} else {
			B = parseInt($("#_new_mail_num").html());
			if (isNaN(B)) B = 0
		}
		$.getJSON("/index.php?s=/msg/get_new/", {
			lose: B
		},
		function(R) {
			if (R.news > 0) {
				B = 1;
				if ($("#_new_mail_num").length == 0) return;
				$("#_new_mail_num").html(Math.abs(parseInt(R.news)));
				if (!new_mail_call_enable) {
					new_mail_call_enable = true;
					new_mail_call()
				}
			}
		})
	},
	friendAdd: function(B, A) {
		$.post("/index.php?s=/fav/add/", {
			uid: this.gSendToUid,
			status: B
		},
		function(B) {
			if (B == 1) {
				var C = "\u6210\u529f\u6536\u85cf";
				$("#friend_fav").hide();
				$("#u_opt").addClass("perhome_chat2")
			} else if (B == 3) {
				C = "\u6084\u6084\u6536\u85cf\u6210\u529f\uff01\u8be5\u7528\u6237\u4e0d\u4f1a\u77e5\u9053\u60a8\u6536\u85cf\u4e86\u4ed6\uff01";
				$("#friend_fav").hide();
				$("#u_opt").addClass("perhome_chat2")
			} else if (B == 2) {
				C = "\u5df2\u7ecf\u6536\u85cf\u4e86";
				$("#friend_fav").hide();
				$("#u_opt").addClass("perhome_chat2")
			} else C = "\u6536\u85cf\u5931\u8d25";
			Win.dialog({
				msg: C,
				"type": "info"
			});
			if (A) Win.dialog({
				msg: C,
				"type": "info"
			});
			else {
				IM.gFriends[IM.gSendToUid].fav = 1;
				IM.favClose()
			}
		})
	},
	friendOpt: function() {
		if ($("#u_stop").css("display") != "none") $("#u_stop").hide();
		else $("#u_stop").show()
	},
	friendForbid: function(A) {
		if (A != 0) {
			$("#friendtype").val(1);
			forbid_link(this.gSendToUid, this.gFriends[this.gSendToUid].sex, this.gFriends[this.gSendToUid].nickname, IM.callback, A)
		} else {
			$("#friendtype").val(0);
			cancel_forbid(this.gSendToUid, this.gFriends[this.gSendToUid].nickname, IM.callback)
		}
	},
	callback: function() {
		if ($("#friendtype").val() == 1) {
			$("#forbid").html("<p><a onclick=\"IM.friendForbid(0)\" class=\"clear\"><span class=\"drpdwn_link2 fl\"></span><span class=\"fl p_l5\">\u53d6\u6d88\u963b\u6b62</span></a></p>");
			$("#chat_msg").attr("contenteditable", false);
			IM.gFriends[IM.gSendToUid].forbid = 1
		} else {
			$("#forbid").html("<p><a onclick=\"IM.friendForbid(1)\" class=\"clear\"><span class=\"drpdwn_link2 fl\"></span><span class=\"fl p_l5\">\u9759\u9ed8\u5904\u7406</span></a></p><p><a onclick=\"IM.friendForbid(2)\" class=\"clear\"><span class=\"drpdwn_link fl\"></span><span class=\"fl p_l5\">\u660e\u786e\u963b\u6b62</span></a></p>");
			$("#chat_msg").attr("contenteditable", true);
			IM.gFriends[IM.gSendToUid].forbid = 0
		}
	},
	friendDate: function() {
		var A = "";
		if (this.gFriends[this.gSendToUid].want != "") {
			var B = this.gFriends[this.gSendToUid].want.split("\u5c81");
			if (B[2] != "") A = B[2].split("\u201d")[0]
		}
		try_meet(this.gSendToUid, this.gFriends[this.gSendToUid].sex, this.gFriends[this.gSendToUid].nickname, A);
		$("#u_stop").hide()
	},
	sendGift: function() {
		window.open("/goods/?uid=" + IM.gSendToUid)
	},
	friendOnline: function() {
		var B = $(".userlist>ul>li"),
		A = Array();
		for (var D = 0, C = 0; D < B.length; D++) {
			if (B[D].id.split("_")[1] == 10000) continue;
			A[C++] = B[D].id.split("_")[1]
		}
		if (A.length > 0) {
			$.post("/index.php?s=/msg/online", {
				uids: A.join(",")
			},
			function(C) {
				if (C.stat == 200) {
					var D = C.list,
					B = "";
					for (var A in D) if (D[A]) $("#f_" + A + " .status_icon").addClass("online");
					else $("#f_" + A + " .status_icon").removeClass("online")
				}
			},
			"json");
			window.setTimeout("IM.friendOnline()", this.gAjaxTime)
		}
	},
	hide: function(A) {
		if (parseInt(A) <= 0) return false;
		$.post("/index.php?s=/msg/hide/", {
			friend: A
		},
		function() {
			$("#f_" + A).fadeOut(500,
			function() {
				$(this).remove()
			});
			if (parseInt(IM.gSendToUid) == parseInt(A)) {
				var B = $(".userlist>ul>li");
				for (var C = 0; C < B.length; C++) {
					if (B[C].id.indexOf(A) > 0) continue;
					IM.gSendToUid == parseInt(B[C].id.substring(2));
					IM.chat(parseInt(B[C].id.substring(2)));
					break
				}
				if (B.length == 0 || IM.gSendToUid == A) window.close()
			}
		})
	},
	popShow: function(D) {
		if (typeof D != "object") return;
		switch (parseInt(D.type)) {
		case 1:
			if (typeof(visit_new_uid_str) == "string" && visit_new_uid_str.indexOf(D.uid) == -1) {
				var F = $("#m_visit_new_count").html().substring(1);
				if (parseInt(F) == "NaN") F = 0;
				$("#m_visit_new_count").html("+" + (1 + parseInt(F)));
				$(".old_messages").show();
				visit_new_uid_str += "," + D.uid;
				var C = $("#visit_face a");
				visit_exists = false;
				for (var G = 0; G < C.length; G++) if (C[G].href.indexOf(D.uid) != -1) visit_exists = true;
				if (visit_exists == false) {
					D.visit = 3;
					D.send = D.uid;
					IM.friendGet(D)
				}
			}
			break;
		case 2:
			$("#m_ping").html("\u65b0").addClass("f_r fb_12");
			break;
		case 4:
			if (this.gFriendNew != D.uid) {
				if ($("#m_fav_new").parent().css("display") == "none") {
					$("#m_fav_new").html("\u65b0");
					$("#m_fav_new").parent().show()
				} else {
					var B = parseInt($("#m_fav_new").html());
					$("#m_fav_new").html("\u65b0")
				}
				this.gFriendNew = D.uid
			}
			break;
		case 5:
			if ($("#reg_other").length == 0) {
				if (!this.gUserList[D.uid]) {
					D.visit = 1;
					this.userGet(D);
					return
				} else {
					D.face = this.gUserList[D.uid].face;
					D.nickname = this.gUserList[D.uid].nickname
				}

				if (D.data == "agree") {
					var E = "<div class=\"popup_c\">" + "\t<p>\u606d\u559c\u60a8\uff01\u60a8\u7684\u7167\u7247\u5ba1\u6838\u901a\u8fc7\u3002</p>" + "\t<div class=\"opt\"><a onclick=\"self.location.href=self.location.href;\" class=\"btn1\">\u786e \u5b9a</a></div>" + "</div>";
					Win.dialog({
						width: 420,
						msg: E,
						cancel: function() {
							self.location.href = self.location.href
						}
					})
				} else {
					E = "<div class=\"popup_c\">" + "\t<p>\u60a8\u7684\u6253\u5206\u7167\u5ba1\u6838\u672a\u901a\u8fc7\uff01\u8bf7<a href=\"/photo/up_form/\">\u91cd\u65b0\u4e0a\u4f20</a>\u3002</p>" + "\t<div class=\"opt\"><a href=\"/photo/up_form/\" class=\"btn1\">\u4e0a\u4f20\u7167\u7247</a></div>" + "</div>";
					Win.dialog({
						width: 420,
						msg: E
					})
				}
			}
			$.getJSON("/main/upface/", {
				type: D.data
			},
			function(A) {});
			break;
		case 6:
			$("#m_attention_new").html("\u65b0\u52a8\u6001");
			$("#m_attention_new").parent().show();
			break;
		case 7:
			break;
		case 8:
			break;
		case 9:
			$.getJSON("/main/upsession/", {},
			function(A) {});
			break;
		case 3:
		case 11:
			if ($("#_new_mail_num").length == 0) return;
			var A = parseInt($("#_new_mail_num").html());
			if (isNaN(A)) A = 0;
			$("#_new_mail_num").html(Math.abs(parseInt(1 + A)));
			if (!new_mail_call_enable) {
				new_mail_call_enable = true;
				new_mail_call()
			}
			break;
		case 12:
			if ($("#_new_mail_num").length == 0) return;
			A = parseInt($("#_new_mail_num").html());
			if (isNaN(A)) return;
			A = A - parseInt(D.data);
			if (A <= 0) {
				$("#_new_mail_num").html(0);
				$("#new_mail_call").hide();
				new_mail_call_enable = false;
				document.title = window_title
			} else $("#_new_mail_num").html(A);
			$("#new_" + D.uid).remove();
		case 13:
			if (!$("#show_flash")) check_gift();
			break;
		case 14:
			$.getJSON("/main/upsession/", {},
			function(A) {});
			break;
		case 15:
			myuserinfo.card_num += parseInt(D.data);
			break;
		case 16:
			if ($("#_new_answer_num").length == 0) return;
			A = parseInt($("#_new_answer_num").html());
			if (isNaN(A)) A = 0;
			$("#_new_answer_num").html(Math.abs(parseInt(1 + A)));
			$("#new_answer_call").show();
			if (A == 0) $("#new_answer_call").next("a").attr("href", "/question/sender");
			break;
		case 17:
			if ($("#_new_answer_num").length == 0) return;
			A = parseInt($("#_new_answer_num").html());
			if (isNaN(A)) return;
			A = A - parseInt(D.data);
			if (A <= 0) {
				$("#_new_answer_num").html("");
				$("#new_answer_call").hide().next("a").attr("href", "/question/plaza")
			} else $("#_new_answer_num").html(A);
		default:
		}
	},
	popClose: function(A) {
		$("#pop_" + A).animate({
			height: "toggle",
			opacity: "toggle"
		},
		"slow", "",
		function() {
			$(this).remove()
		})
	},
	isMinStatus: function() {
		var A = false;
		if (window.outerWidth != undefined) A = window.outerWidth <= 160 && window.outerHeight <= 27;
		else A = window.screenTop < -30000 && window.screenLeft < -30000;
		if (!IM.gFocus) A = true;
		return A
	},
	flicker: function(A) {
		if (this.g_timeOut > 0) {
			if (this.g_titleTimeout > 0) window.clearTimeout(this.g_titleTimeout);
			var B = document.title,
			E = "";
			if (B.indexOf("\u3011") > 0) E = B.split("\u3011")[1];
			else E = B;
			if (B.indexOf("\u6d88\u606f\u3011") > 0) {
				var C = B.split("\u3011")[0].length,
				D = "";
				for (var F = 0; F < C - 1; F++) D += "\u3000";
				document.title = "\u3010" + D + "\u3011" + B.split("\u3011")[1]
			} else document.title = "\u3010\u65b0\u6d88\u606f\u3011" + E;
			this.g_titleTimeout = window.setTimeout("IM.flicker(" + A + ")", 500)
		}
	},
	flickerClose: function() {
		IM.g_timeOut = -1;
		if (IM.g_titleTimeout > 0) window.clearTimeout(IM.g_titleTimeout);
		var A = document.title;
		if (A.indexOf("\u3011") != -1) document.title = A.split("\u3011")[1]
	},
	setChatOpen: function() {
		Cookies.set("gChatWin", (new Date).getTime() + "|" + this.gSendToUid)
	},
	type: function(A) {
		$("#f_" + A + " .p_5").html("\u6b63\u5728\u8f93\u5165...");
		window.setTimeout("IM.typeClean(" + A + ")", 10000)
	},
	typeClean: function(A) {
		$("#f_" + A + " .p_5").html(substrByte(this.gFriends[A].nickname, 10))
	},
	chat: function(A) {
		if (g_chat_msg == 0) return;
		this.gIsOpen = false;
		this.gSendToUid = parseInt(A);
		if (this.gSendToUid != 10000) {
			$("#chat_send").attr("disabled", false);
			$("#chat_msg").attr("contenteditable", true);
			$("#u_opt").show();
			$(".type").show();
			$("#messages").css({
				height: document.documentElement.clientHeight - 225
			})
		} else {
			$("#chat_send").attr("disabled", true);
			$("#chat_msg").attr("contenteditable", false);
			$("#u_opt").hide();
			$(".type").hide();
			$("#messages").css({
				height: document.documentElement.clientHeight - 93
			})
		}
		Win.dialog({
			"msg": "<div class=\"progress\">\u6b63\u5728\u52a0\u8f7d\u6570\u636e</div>",
			"type": "warn",
			"noclose": true
		});
		$.getJSON("/msg/get_detail/?fid=" + A + "&news=" + this.gFriends[A].news, {},
		function(E) {
			Win.close();
			if (parseInt(E.news) > 0) minus_unread("msg", E.news, A);
			var D = "";
			IM.gMsgTotal = E.total;
			var C = E.data;
			if (E.total > C.length) $("#msg_history").html("<a href=\"javascript:;\" onclick=\"IM.chatDetail(" + C[0].time + ")\">\u67e5\u770b\u66f4\u591a\u8bb0\u5f55</a>");
			else $("#msg_history").html("");
			IM.userInfo(E.userinfo);
			if (IM.gMsgTotal == 0) IM.gFirstUid = gUid;
			else IM.gFirstUid = C[0].send;
			for (var F = 0; F < C.length; F++) {
				if (C[F].send == "") continue;
				if (C[F].send != A) {
					if (C[F].send != 10000) D += "<li><dl><dt class=\"me\"><b>" + gNickName + "</b> ";
					else D += "<li><dl class=\"clear\"><dt class=\"me\"><b>\u7cfb\u7edf\u901a\u77e5</b>";
					if (C[F].send == 10000 && decodeURIComponent(C[F].cont).indexOf("\u6084\u6084\u6536\u85cf") != -1) D += " \u2014 " + IM.showTime(C[F].time, 1);
					else D += " \u2014 " + IM.showTime(C[F].time);
					if (C[F].send != 10000) D += "</dt><dd>" + decodeURIComponent(C[F].cont) + "</dd></dl></li>";
					else D += "</dt><dd>" + decodeURIComponent(C[F].cont) + "<input type=\"hidden\" value=\"" + C[F].time + "\" readonly=\"readonly\"/></dd></dl></li>";
					IM.gSayNum++
				} else {
					D += "<li><dl><dt class=\"ta\"><b>" + IM.gFriends[IM.gSendToUid].nickname + "</b>";
					if (C[F].send == 10000 && decodeURIComponent(C[F].cont).indexOf("\u6084\u6084\u6536\u85cf") != -1) D += " \u2014 " + IM.showTime(C[F].time, 1);
					else D += " \u2014 " + IM.showTime(C[F].time);
					if (C[F].send != 10000) D += "</dt><dd>" + decodeURIComponent(C[F].cont) + "</dd></dl></li>";
					else D += "</dt><dd>" + decodeURIComponent(C[F].cont) + "<input type=\"hidden\" value=\"" + C[F].time + "\" readonly=\"readonly\"/></dd></dl></li>"
				}
			}
			$("#chat_msg_show").html(D);
			IM.chatScroll();
			if (E.stat) if (E.stat == 5) {
				var B = redbeans(E.nickname, E.pay_card);
				Win.dialog({
					"msg": B,
					"height": 400,
					"width": 580,
					"pay_card": E.pay_card,
					"enter": function(B) {
						$.post("/index.php?s=/msg/check/", {
							friend: A,
							pay_card: E.pay_card
						},
						function(A) {
							if (A.stat) Win.dialog({
								"msg": A.error,
								"type": "alert"
							})
						},
						"json")
					}
				})
			} else if (E.stat) Win.dialog({
				"msg": E.error,
				"type": "alert"
			})
		});
		$(".userlist>ul>li").attr("class", "clear");
		$("#f_" + A).attr("class", "clear current");
		$("#f_" + A + " .status_icon").removeClass("newmsg").html("")
	},
	chatDetail: function(A) {
		if (this.gAjaxSend == true) return;
		this.gAjaxSend = true;
		$.getJSON("/msg/detail_page/?fid=" + this.gSendToUid + "&time=" + A, {},
		function(A) {
			IM.gAjaxSend = false;
			var B = "";
			if (A.length == 0) return;
			if (A.length == 10) $("#msg_history").html("<a href=\"javascript:;\" onclick=\"IM.chatDetail(" + A[0].time + ")\">\u67e5\u770b\u66f4\u591a\u8bb0\u5f55</a>");
			else $("#msg_history").html("");
			for (var C = 0; C < A.length; C++) {
				if (A[C].send == "") continue;
				if (A[C].send != IM.gSendToUid) {
					if (A[C].send != 10000) B += "<li><dl><dt class=\"me\"><b>" + gNickName + "</b>";
					else B += "<li><dl class=\"clear\"><dt class=\"me\"><b>\u7cfb\u7edf\u901a\u77e5</b>";
					if (A[C].send == 10000 && decodeURIComponent(A[C].cont).indexOf("\u6084\u6084\u6536\u85cf") != -1) B += " \u2014 " + IM.showTime(A[C].time, 1);
					else B += " \u2014 " + IM.showTime(A[C].time);
					if (A[C].send != 10000) B += "</dt><dd>" + decodeURIComponent(A[C].cont) + "</dd></dl></li>";
					else B += "</dt><dd>" + decodeURIComponent(A[C].cont) + "<input type=\"hidden\" value=\"" + A[C].time + "\" readonly=\"readonly\"/></dd></dl></li>"
				} else {
					B += "<li><dl><dt class=\"ta\"><b>" + IM.gFriends[IM.gSendToUid].nickname + "</b>";
					if (A[C].send == 10000 && decodeURIComponent(A[C].cont).indexOf("\u6084\u6084\u6536\u85cf") != -1) B += " \u2014 " + IM.showTime(A[C].time, 1);
					else B += " \u2014 " + IM.showTime(A[C].time);
					if (A[C].send != 10000) B += "</dt><dd>" + decodeURIComponent(A[C].cont) + "</dd></dl></li>";
					else B += "</dt><dd>" + decodeURIComponent(A[C].cont) + "<input type=\"hidden\" value=\"" + A[C].time + "\" readonly=\"readonly\"/></dd></dl></li>"
				}
			}
			$("#chat_msg_show").prepend(B)
		})
	},
	chatClick: function() {
		$("#chat_msg").focus()
	},
	chatKeyDown: function(A) {
		if (this.gSendToUid == 10000) return;
		if (A.keyCode == 13) this.chatSend();
		else if (this.gTyping == 0);
	},
	chatReply: function(A) {
		if (this.gSendToUid == 10000) return;
		$("#chat_msg").html($("#" + A).html())
	},
	photoReply: function(A, C, E, D) {
		if (A <= 0 || C <= 0 || E <= 0) return;
		var B = $(D).parent("p").next("input").val();
		if (B == "") return;
		$.post("/index.php?s=/photo/enquire_result/", {
			uid: A,
			gid: C,
			type: E,
			mtime: B
		},
		function(A) {
			switch (A.stat) {
			case - 1: Win.dialog({
					type: "info",
					msg: A.error
				});
				return false;
				break;
			case 1:
				$(D).parent("p").html(A.cont);
				break
			}
		},
		"json")
	},
	chatTyping: function() {
		$I("sock").socket_send("TYPESTATE," + this.gSendToUid);
		this.gTyping = setTimeout("IM.chatTypeClean()", 10000)
	},
	chatTypeClean: function() {
		this.gTyping = 0
	},
	chatSet: function(A) {
		this.gSendToUid = A
	},
	chatSend: function(B) {
		if (this.gFriends[this.gSendToUid].forbid == 1) {
			alert("\u4f60\u5df2\u7ecf\u963b\u6b62\u4e0e\u6b64\u4eba\u8054\u7cfb\uff0c\u8bf7\u5148\u53d6\u6d88\u963b\u6b62");
			return
		}
		if (B) var E = B;
		else E = $("#chat_msg").html();
		var C = (new Date()).getTime();
		if (C - this.gSendTime < 500) {
			alert("\u4f60\u8bf4\u7684\u592a\u5feb\u4e86");
			this.chatFocus();
			return
		}
		$("#hide_area").focus();
		var D = this.chatFilter(E);
		if (D == "") {
			this.chatFocus();
			return
		}
		if (D.length > 500) {
			alert("\u8f93\u5165\u5185\u5bb9\u592a\u591a");
			this.chatFocus();
			return
		}
		if (E.trim() == "" || E == this.gDefaultTxt || D == "") {
			$("#chat_msg").html(this.gDefaultTxt).focus();
			window.setTimeout("IM.chatFocus()", 0);
			return
		}
		this.gSendTime = C;
		if (this.gSendStatus == 1) return false;
		this.gSendStatus = 1;
		var A = (new Date).getTime();
		if (IM.gMsgTotal == 0) IM.gSayNum = 0;
		$.post("/index.php?s=/msg/chat/", {
			fid: this.gSendToUid,
			msg: D,
			is_auto_fav: IM.gSayNum,
			firstuid: IM.gFirstUid
		},
		function(G) {
			var B = (new Date).getTime();
			if (B - A > 1000) $.get("msg/ajax_time", {
				time: B - A
			});
			IM.gSendStatus = 0;
			if (G.stat) {
				if (G.stat == 10) {
					$("#fengkoujiao").remove();
					$(G.error).appendTo("body");
					var I = Win.pos(320, 250);
					$("#fengkoujiao").css({
						"top": I.wt,
						"left": I.wl
					})
				} else if (G.stat == 5) {
					var F = redbeans(IM.gFriends[IM.gSendToUid].nickname, G.pay_card);
					Win.dialog({
						"msg": F,
						"height": 400,
						"width": 580,
						"pay_card": G.pay_card,
						"enter": function(A) {
							$.post("/index.php?s=/msg/chat/", {
								fid: IM.gSendToUid,
								msg: D,
								pay_card: A.pay_card
							},
							function(A) {
								if (A.stat) Win.dialog({
									"msg": A.error,
									"type": "alert"
								});
								else {
									IM.chatShow({
										"send": gUid,
										"recv": IM.gSendToUid,
										"time": C / 1000,
										"msg": D,
										"type": 1
									});
									window.setTimeout("IM.chatFocus()", 0);
									myuserinfo.card_num = myuserinfo.card_num - A.pay_card
								}
							},
							"json")
						}
					})
				} else Win.dialog({
					"msg": G.error,
					"type": "alert"
				})
			} else {
				if (G.auto_fav == 1 && IM.gMsgTotal <= 10 && IM.gFriends[IM.gSendToUid].fav == 0) {
					if (IM.gFriends[IM.gSendToUid].sex == 1) var E = "\u4ed6";
					else E = "\u5979";
					var H = "<div class=\"popup_c\"><div class=\"order_box\">";
					H += "\u7cfb\u7edf\u63d0\u793a\uff1a\u662f\u5426\u6536\u85cf" + E + "\uff1f";
					H += "\t<div style=\"padding:30px 0px;\"><a onclick=\"IM.friendAdd(1);\" class=\"btn1 btn_b1\">\u597d</a> &nbsp; <a onclick=\"IM.favClose();\" class=\"btn1 btn_b1\">\u4e0d\u8981</a>";
					H += "\t</div>";
					H += "</div></div>";
					Win.dialog({
						msg: H,
						width: 500
					})
				}
				IM.chatShow({
					"send": gUid,
					"recv": IM.gSendToUid,
					"time": C / 1000,
					"msg": D,
					"type": 1
				});
				window.setTimeout("IM.chatFocus()", 0);
				myuserinfo.card_num = myuserinfo.card_num - G.pay_card;
				IM.gSayNum++
			}
		},
		"json")
	},
	chatFocus: function() {
		$("#chat_msg").html(this.gDefaultTxt).focus()
	},
	chatOpen: function(A) {
		if (A == this.gSendToUid) return;
		else if (!this.gFriends[A]) {
			var B = {
				send: A,
				visit: 2
			};
			this.friendGet(B);
			return
		} else this.chat(A)
	},
	chatShow: function(B) {
		if (g_chat_msg != 1) return;
		if (B.type != 1 && this.isMinStatus()) {
			this.g_timeOut = 1;
			this.flicker();
			if (this.gFriends[B.send]) IM.popup({
				id: B.send,
				title: this.gFriends[B.send].nickname + " -- " + this.showTime(B.time),
				icon: this.gFriends[B.send].face,
				msg: IM.popFilter(B.msg)
			})
		}
		if (B.send != this.gSendToUid && B.type != 1) {
			if (!this.gFriends[B.send]) {
				B.visit = 1;
				this.friendGet(B);
				return
			}
			if (B.visit == 1) {
				$(".userlist>ul").prepend("<li id=\"f_" + B.send + "\" onclick=\"IM.chat(" + B.send + ")\" class=\"clear\"><span class=\"status_icon_w fl\"><b class=\"status_icon newmsg online\">1</b></span><span class=\"fl\">" + this.gFriends[B.send].nickname + "</span><span class=\"fr pr5\"><img src=\"" + this.gFriends[B.send].face + "\" width=\"24\" height=\"24\"  class=\"ico\"/></span><a class=\"user_delete\" href=\"javascript:;\" onclick=\"IM.hide(" + B.send + ");event.cancelBubble=true\"><span></span></a></li>");
				$(".userlist>ul>li").mouseover(function() {
					$(this).children(".user_delete").show()
				}).mouseout(function() {
					$(this).children(".user_delete").hide()
				})
			} else {
				var A = parseInt($("#f_" + B.send + " .status_icon").html());
				if (isNaN(A)) A = 0;
				$("#f_" + B.send + " .status_icon").addClass("newmsg").html(parseInt(1 + A))
			}
			return
		}
		if (B.type == 2) var C = "<li><dl class=\"clear\"><dt class=\"me\"><b>\u7cfb\u7edf\u901a\u77e5</b> \u2014 " + this.showTime(B.time) + "</dt><dd class=\"clear\">" + B.msg + "</dd></dl></li>";
		else if (B.type == 1) C = "<li><dl><dt class=\"me\"><b>" + gNickName + "</b> \u2014 " + this.showTime(B.time) + "</dt><dd>" + IM.toHtml(B.msg) + "</dd></dl></li>";
		else C = "<li><dl><dt class=\"ta\"><b>" + this.gFriends[this.gSendToUid].nickname + "</b> \u2014 " + this.showTime(B.time) + "</dt><dd>" + B.msg + "</dd></dl></li>";
		if (B.type != 1) $.post("/index.php?s=/msg/minus", {
			friend: this.gSendToUid
		},
		function(D) {
			if (parseInt(D.stat) == 200 && parseInt(D.news) > 1 && B.lose != 1) {
				var A = D.data;
				for (var E in A) if (A[E].sender == 10000) C += "<li><dl class=\"clear\"><dt class=\"me\"><b>\u7cfb\u7edf\u901a\u77e5</b> \u2014 " + IM.showTime(A[E].time) + "</dt><dd class=\"clear\">" + A[E].msg + "</dd></dl></li>";
				else C += "<li><dl><dt class=\"ta\"><b>" + IM.gFriends[IM.gSendToUid].nickname + "</b> \u2014 " + IM.showTime(A[E].time) + "</dt><dd>" + IM.toHtml(A[E].msg) + "</dd></dl></li>"
			}
			$("#chat_msg_show").append(C);
			IM.chatScroll()
		},
		"json");
		else {
			$("#chat_msg_show").append(C);
			this.chatScroll()
		}
	},
	chatScroll: function() {
		var B = $I("chat_msg_show").parentNode;
		B.scrollTop = B.scrollHeight;
		try {
			B.doScroll("bottom")
		} catch(A) {}
	},
	chatBlock: function(A) {
		A.preventDefault()
	},
	chatSetRange: function(B, A) {
		B.removeAllRanges();
		B.addRange(A)
	},
	chatPaste: function(D) {
		if ($.browser.msie) {
			var E = document.selection.createRange();
			$("#hide_area").focus();
			document.execCommand("Paste", false, null);
			var C = $("#hide_area").html();
			$("#chat_msg").focus();
			C = this.chatFilter(C);
			if (C) E.pasteHTML(C);
			E = null;
			if (D) {
				D.returnValue = false;
				if (D.preventDefault) D.preventDefault()
			}
			$("#hide_area").html("");
			return false
		} else {
			var A = document.getElementById("chat_msg");
			enableKeyDown = false;
			A.addEventListener("mousedown", IM.chatBlock, false);
			A.addEventListener("keydown", IM.chatBlock, false);
			enableKeyDown = false;
			var B = window.getSelection().getRangeAt(0);
			$("#hide_area").focus();
			window.setTimeout(function() {
				var A = $("#hide_area").html();
				$("#chat_msg").focus();
				if (B) IM.chatSetRange(window.getSelection(), B);
				A = IM.chatFilter(A);
				if (A) {
					document.execCommand("insertHtml", false, A);
					$("#hide_area").html("")
				}
			},
			0);
			enableKeyDown = true;
			A.removeEventListener("mousedown", IM.chatBlock, false);
			A.removeEventListener("keydown", IM.chatBlock, false);
			return true
		}
	},
	chatDrop: function(B) {
		if ($.browser.msie) {
			var H = document.selection,
			C = H.createRange().htmlText;
			$("#chat_msg").focus();
			C = this.chatFilter(C);
			if (C) H.createRange().pasteHTML(C);
			H = null;
			if (B) {
				B.returnValue = false;
				if (B.preventDefault) B.preventDefault()
			}
			return false
		} else {
			var A = document.getElementById("chat_msg");
			enableKeyDown = false;
			A.addEventListener("mousedown", IM.chatBlock, false);
			A.addEventListener("keydown", IM.chatBlock, false);
			enableKeyDown = false;
			var F = window.getSelection(),
			D = F.getRangeAt(0),
			G = D.commonAncestorContainer,
			E = G.innerHTML;
			E = IM.chatFilter(E);
			$("#chat_msg").focus();
			IM.chatSetRange(window.getSelection(), D);
			if (!E) document.execCommand("insertHTML", true, D);
			else document.execCommand("insertHTML", false, E);
			enableKeyDown = true;
			A.removeEventListener("mousedown", IM.chatBlock, false);
			A.removeEventListener("keydown", IM.chatBlock, false);
			return true
		}
	},
	toHtml: function(B) {
		var A = /\[img\](.*?)\[\/img\]/ig;
		B = B.replace(A, "<img src=\"http://jjdd01.ivu1314.com/CDN/app/face/$1\" class=\"ico\">");
		B = B.replace(/(&|＆)nbsp;/ig, " ");
		return B
	},
	chatFilter: function(E) {
		if (E == "") return "";
		var C = /<IMG src=""+this.gPicUrl+"\/(\d{1,3})\.gif"(.*?)>/ig;
		E = E.replace(C, "[I:0:$1]");
		var B = /<img(.*?)src=('|")?(.*?).(dxslaw)[\.com](.*?).(gif|png|jpg|jpeg|ico)(.*?)('|")(.*?)>/gim;
		E = E.replace(B, "[I:7:$3.$4.$5.$6$7$1$9]");
		if ($.browser.safari) E = E.replace(/(<div>)|(<p>)|(<br>)|(<li>)/ig, "\r\n");
		else E = E.replace(/(<\/div>)|(<\/p>)|(<br\/?>)|(<\/li>)/ig, "\r\n");
		E = E.replace(/<[^>]+>/ig, "");
		E = E.replace(/<script(.*?)>(.*?)<\/script>/ig, "");
		if (E[0] == "<") E = E.replace(/<br>/i, "");
		var A = /\[I:0:(\d{1,3})\]/ig;
		E = E.replace(A, "<img src='" + this.gPicUrl + "/$1.gif'>");
		var D = /\[I:7:(.*?)\.(dxslaw)\.com(.*?)\.(gif|png|jpeg|jpg|ico)(.*?)\]/gim;
		E = E.replace(D, "<img src='$1.$2.com$3.$4'$5>");
		E = E.replace(/\r\n/gi, "");
		E = E.replace(/<br\/><br\/>/ig, "");
		E = E.replace(/<br>/gi, "");
		E = E.replace(/^(&nbsp;)*/, "").replace(/(&nbsp;)*$/, "");
		E = E.replace(/(&|＆)nbsp;/ig, " ");
		E = E.trim();
		return E
	},
	popFilter: function(B) {
		var A = /<img(.*?)src=('|")?(.*?).(dxslaw)[\.com](.*?).(gif|png|jpg|jpeg|ico)(.*?)('|")(.*?)>/gim;
		B = B.replace(A, "\u3010\u56fe\u7247\u3011");
		B = B.replace(/<[^>]+>/ig, "");
		B = B.replace(/<script(.*?)>(.*?)<\/script>/ig, "");
		B = B.replace(/(&nbsp;){1,}/ig, " ");
		return B
	},
	face: function(A) {
		if ($("#chat_face").css("display") != "block") {
			$("#chat_face").css("display", "block");
			if ($.browser.msie && $.browser.version <= 6) {
				var B = $("#chat_send").offset();
				$("#chat_face").css("left", B.left)
			}
		} else $("#chat_face").css("display", "none")
	},
	faceInsert: function(C) {
		if (IM.chatFilter($("#chat_msg").val()) == "") $("#chat_msg").focus();
		if ($.browser.msie) {
			var B = document.getElementById("chat_msg");
			B.focus();
			var A = document.selection.createRange();
			A.pasteHTML("<img src='" + C + "'>");
			A = null
		} else if ($.browser.mozilla) {
			$("#chat_msg").focus();
			document.execCommand("InsertImage", false, C)
		} else document.execCommand("InsertImage", false, C);
		this.faceClose()
	},
	faceClose: function() {
		$("#chat_face").hide()
	},
	addRemarkTable: function() {
		var A = this.gFriends[this.gSendToUid].fav;
		if (A == 1) {
			if (this.gIsOpen == false) {
				$(".bz_window").show();
				this.gIsOpen = true
			} else {
				$(".bz_window").hide();
				this.gIsOpen = false
			}
		} else {
			var B = "<div class=\"popup_c\"><div class=\"order_box\">";
			B += "\u9700\u5148\u6536\u85cf\uff0c\u624d\u80fd\u5907\u6ce8\u54e6\uff01";
			B += "\t<div style=\"padding:30px 0px;\"><a onclick=\"IM.friendAdd(1);\" class=\"btn1 btn_b1\">\u597d</a> &nbsp; <a onclick=\"IM.favClose();\" class=\"btn1 btn_b1\">\u4e0d\u8981</a>";
			B += "\t</div>";
			B += "</div></div>";
			Win.dialog({
				msg: B,
				width: 500
			})
		}
	},
	canceltxt: function(A) {
		var B = $("#area_" + A);
		if (B.val() == "\u5907\u6ce8\u7535\u8bdd\u3001\u59d3\u540d\u7b49...") $("#area_" + A).html("")
	},
	remarkClose: function() {
		this.gIsOpen = false;
		$(".bz_window").hide()
	},
	favClose: function() {
		$("#WinDiv").hide();
		$("#WinMask").hide()
	},
	addRemark: function(A) {
		this.gIsOpen = false;
		if (A == "") return false;
		var C = $("#area_" + A),
		B = C.val();
		if (B == "\u5907\u6ce8\u7535\u8bdd\u3001\u59d3\u540d\u7b49...") C.val("");
		if (C.val().length > 500) {
			alert("\u586b\u5199\u5907\u6ce8\u5185\u5bb9\u8fc7\u957f\uff01");
			return false
		}
		$.ajax({
			type: "POST",
			url: "/index.php?s=/fav/add_remark/",
			data: "uid=" + A + "&remark=" + C.val() + "&",
			success: function D(B) {
				var A = jQuery.parseJSON(B);
				switch (A.ret) {
				case "2":
					Win.dialog({
						type:
						"alert",
						msg: "\u6dfb\u52a0\u5907\u6ce8\u5931\u8d25<br/>",
						height: 100,
						cancel: function() {
							top.location.href = "?"
						},
						enter: function() {
							IM.favClose()
						}
					});
					break;
				case "3":
					alert("\u5907\u6ce8\u5185\u5bb9\u4e0d\u80fd\u8d85\u8fc7500\u5b57!");
					break;
				default:
					$(".bz_window").hide();
					IM.gFriends[IM.gSendToUid].remark = C.val();
					$(".bz_off").addClass("bz_on").removeClass("bz_off");
					$(".bz_btn").html("\u4fee\u6539");
					if (C.val()) {
						if (C.val().length > 10) bz1 = C.val().substring(0, 10) + "....";
						else bz1 = C.val();
						$(".bz_on").html(bz1)
					} else {
						$(".bz_on").addClass("bz_off").removeClass("bz_on");
						$(".bz_btn").html("\u5907\u6ce8");
						$(".bz_off").html("\u5907\u6ce8\u7535\u8bdd\u3001\u59d3\u540d\u7b49...");
						C.html("\u5907\u6ce8\u7535\u8bdd\u3001\u59d3\u540d\u7b49...")
					}
					break
				}
			}
		})
	}
};
$(function() {
	IM.init();
	//notice_get()
});
var stopscroll = false;
function notice_get() {
	if ($("#infozone").length == 0) return;
	$.getJSON("/index.php?s=/main/operations/", {},
	function(A) {
		if (typeof A == "object" && A && A.length > 0) {
			notices = A;
			var C = "";
			for (var D = 0; D < A.length; D++) C += "<div style=\"height:40px;overflow:hidden;\"><table><tr><td valign=\"middle\" height=\"32px\"><div style=\"line-height:16px;overflow:hidden;width:160px;word-break:break-all;cursor:pointer\">" + A[D].memo + "</div></td></tr></table></div>";
			$("#notice_div").css("display", "");
			$("#infozone").html(C);
			if (A.length <= 1) return;
			var B = document.getElementById("infozone");
			window.setInterval(function() {
				scrollup(B, 40, 0)
			},
			6000);
			B.onmouseover = new Function("stopscroll=true");
			B.onmouseout = new Function("stopscroll=false")
		}
	})
}
function scrollup(E, A, D) {
	if (stopscroll) return;
	if (A == D) {
		var B = E.firstChild.cloneNode(true);
		E.removeChild(E.firstChild);
		E.appendChild(B);
		B.style.marginTop = E.firstChild.style.marginTop = "0px"
	} else {
		var C = 3,
		D = D + C,
		F = (D >= A ? D - A: 0);
		E.firstChild.style.marginTop = -D + F + "px";
		window.setTimeout(function() {
			scrollup(E, A, D - F)
		},
		40)
	}
}