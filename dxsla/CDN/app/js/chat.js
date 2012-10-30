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
		if (IM.gFlash == 0) window.setInterval("IM.ajax_new()", 10000);
		if (gGetLoseMsg2 == 1) window.setInterval("IM.getLoseMsg2()", 6000);
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
	ajax_new: function() {},
	userGet: function(A) {},
	userInfo: function(G) {},
	friendGet: function(A) {},
	getLoseMsg: function() {},
	getLoseMsg2: function(E) {
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
			t: new Date().getTime()
		},
		function(R) {
			//alert(R.news);
			if (R.news > 0) {
				B = 1;
				if ($("#_new_mail_num").length == 0) return;
				$("#_new_mail_num").html(Math.abs(parseInt(R.news)));
				if (!new_mail_call_enable) {
					new_mail_call_enable = true;
					new_mail_call()
				}
			} else {
				if ($("#_new_mail_num").length == 0) return;
				$("#_new_mail_num").html('0');
				$("#new_mail_call").css("display", "none");
				document.title = window_title;
				new_mail_call_enable = false;
			}
		})
	},
	friendAdd: function(B, A) {},
	friendOpt: function() {},
	friendForbid: function(A) {},
	callback: function() {},
	friendDate: function() {},
	sendGift: function() {},
	friendOnline: function() {},
	hide: function(A) {},
	popShow: function(D) {},
	popClose: function(A) {},
	isMinStatus: function() {},
	flicker: function(A) {},
	flickerClose: function() {},
	setChatOpen: function() {},
	type: function(A) {},
	typeClean: function(A) {},
	chat: function(A) {},
	chatDetail: function(A) {},
	chatClick: function() {},
	chatKeyDown: function(A) {},
	chatReply: function(A) {},
	photoReply: function(A, C, E, D) {},
	chatTyping: function() {},
	chatTypeClean: function() {},
	chatSet: function(A) {},
	chatSend: function(B) {},
	chatFocus: function() {},
	chatOpen: function(A) {},
	chatShow: function(B) {},
	chatScroll: function() {},
	chatBlock: function(A) {},
	chatSetRange: function(B, A) {},
	chatPaste: function(D) {},
	chatDrop: function(B) {},
	toHtml: function(B) {},
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
	popFilter: function(B) {},
	face: function(A) {},
	faceInsert: function(C) {},
	faceClose: function() {},
	addRemarkTable: function() {},
	canceltxt: function(A) {},
	remarkClose: function() {},
	favClose: function() {},
	addRemark: function(A) {}
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