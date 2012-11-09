//==========================================================================================
//软件名称：魅魔MacCMS
//开发作者：MagicBlack    '官方网站：http://www.maccms.com/
//Copyright (C) 2009 - 2010 ... maccms.com  All Rights Reserved.
//郑重声明:
//    1、任何个人或组织不得以盈利为目的发布,修改,本软件及其他副本上一切关于版权的信息；
//    2、本人保留此软件的法律追究权利；
//==========================================================================================

String.prototype.replaceAll  = function(s1,s2){
   return this.replace(new RegExp(s1,"gm"),s2);
}
String.prototype.trim=function(){
   return this.replace(/(^\s*)|(\s*$)/g, "");
}
function copyData(text){
	if (window.clipboardData){
		window.clipboardData.setData("Text",text);
	} 
	else{
		var flash_copy = null;
		if( !$('#flash_copy') ){
			var flash_copy = document.createElement("div");
	    	flash_copy.id = 'flash_copy';
	    	document.body.appendChild(flash_copy);
		}
		flash_copy = $('#flash_copy');
		flash_copy.innerHTML = '<embed src='+maccms_path+'"images/_clipboard.swf" FlashVars="clipboard='+escape(text)+'" width="0" height="0" type="application/x-shockwave-flash"></embed>';
	}
	alert("复制成功");
	return true;
}

function sitehome(obj,strUrl){
    try{
    	obj.style.behavior='url(#default#homepage)';
    	obj.setHomePage(strUrl);
	}
    catch(e){
         if(window.netscape){
         	try{netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");}
            catch (e){alert("此操作被浏览器拒绝！请手动设置");}
            var moz = Components.classes['@mozilla.org/preferences-service;1'].getService(Components.interfaces.nsIPrefBranch);
            moz.setCharPref('browser.startup.homepage',strUrl);
          }
     }
}
function sitefav(strUrl,strTitle){
	try{ window.external.addFavorite(strUrl, strTitle);}
	catch (e){
		try{window.sidebar.addPanel(strTitle, strUrl, "");}
		catch (e){alert("加入收藏出错，请使用键盘Ctrl+D进行添加");}
	}
}
function OpenWindow1(url,w,h){
	window.open(url,'openplay1','toolbars=0, scrollbars=0, location=0, statusbars=0,menubars=0,resizable=yes,width='+w+',height='+h+'');
}

function creatediv(z,w,h){
	$('<div id="adddiv"></div>')
	.css('top', '0')
	.css('width',w+"px")
	.css('height',h+"px")
	.css('z-index',z)
	.css('filter','Alpha(Opacity=0)')
	.css('position','absolute')
	.appendTo("body");
	$('<div id="confirm"></div>')
	.css('position','absolute')
	.css('z-index',z+1)
	.css('top','200px')
	.css('left','300px')
	.appendTo("body");
}
function closew(){	$("#confirm").remove(); }

function getHit(action,id){
	$.get(maccms_path+"inc/ajax.php?action="+action+"hit&id="+id,function(obj){
		if(obj=="err"){ $('#hit').html('发生错误');}else{ $('#hit').html(obj);}
	});
}
function getGoodBad(action,id){
	$.get(maccms_path+"inc/ajax.php?action="+action+"num&id="+id+"&rnd="+Math.random(),function(obj){
		if(obj=="err"){ $('#'+action+'_num').html('发生错误');}else{ $('#'+action+'_num').html(obj);}
	});
}
function vodError(id,name){
	location.href= maccms_path + "gbook.php?id="+id+"&name="+ encodeURI(name);
}
function userFav(id){
	$.get(maccms_path+"inc/ajax.php?action=userfav&id="+id+"&rnd="+Math.random(),function(obj){
		if(obj=="ok"){
			alert("会员收藏成功");
		}
		else if(obj=="login"){
			alert('请先登录会员中心再进行会员收藏操作');
		}
		else if(obj=="haved"){
			alert('您已经收藏过了');
		}
		else{
			alert('发生错误');
		}
	});
}
function desktop(name){
	location.href= maccms_path + "inc/ajax.php?action=desktop&name="+encodeURI(name)+"&url=" + encodeURI(location.href);
}
function vodGood(id,div){
	$.get(maccms_path+"inc/ajax.php?id="+id+"&action=vodgood&rnd="+Math.random(),function (obj){
		if (!isNaN(obj)){
			try{ $('#'+div).html(obj);}catch(e) {}
			alert('感谢你的支持！');
		}
		else if(obj=="haved"){alert('您已经顶过了！');}
		else{alert('没有顶上去啊!');}
	});	
}

function vodBad(id,div){
	$.get(maccms_path+"inc/ajax.php?id="+id+"&action=vodbad&rnd="+Math.random(),function (obj){
		if(!isNaN(obj)){
			try{$('#'+div).html(obj);}catch(e) {}
			alert('踩我好悲哀！');
		}
		else if(obj=="haved"){alert('您已经踩过了！');}
		else{alert('没有踩下去啊');}
	});
}
function getScore(action,id){
	$.get(maccms_path+"inc/ajax.php?action=getscore&ac2="+action+"&id="+id+"&rnd="+Math.random(),function(obj){
		if(obj=="err"){ $('#score'+action+'_num').html('发生错误');}else{ $('#score'+action+'_num').html(obj);}
	});
}
function vodScoreMark(id,sc,s){
	var pjf = (parseInt(s / sc * 10) * 0.1) || 0;
	pjf = pjf.toFixed(1);
	document.write("<div id=\"vod-rating\" class=\"vod-rating\"></div>");
	$.ajax({ cache: false, dataType: 'html', type: 'GET', url: maccms_path +'inc/ajax.php?id='+id+'&action=getscore&ac2=pjf',
	success: function(r){
		pjf = Number(r);
	},
	complete:function(a,b){
		$("#vod-rating").rater({maxvalue:10,curvalue:pjf ,style:'inline-normal',url: maccms_path +'inc/ajax.php?id='+id+'&action=score&score='});
	}});
}
function vodScoreMark1(id,sc,s){
	var pjf = (parseInt(s / sc * 10) * 0.1) || 0;
	pjf = pjf.toFixed(1);
	var str="";
	str = '<div style="padding:5px 10px;border:1px solid #CCC"><div style="color:#000"><strong>我要评分(感谢参与评分，发表您的观点)</strong></div><div>共 <strong style="font-size:14px;color:red" id="rating_msg1"> '+sc+' </strong> 个人评分， 平均分 <strong style="font-size:14px;color:red" id="rating_msg2"> '+pjf+' </strong>， 总得分 <strong style="font-size:14px;color:red" id="rating_msg3"> '+s+' </strong></div><div>';
	for(var i=1;i<=10;i++){
		str += '<input type="radio" name="score" id="rating'+i+'" value="1"/><label for="rating'+i+'">'+i+'</label>';
	}
	document.write(str +'&nbsp;<input type="button" value=" 评 分 " id="scoresend" style="width:55px;height:21px"/></div></div>');
	
	$.ajax({ cache: false, dataType: 'html', type: 'GET', url: maccms_path +'inc/ajax.php?id='+id+'&action=getscore&ac2=pjf&ac3=all',
		success: function(r){
			var arr = r.split(",");
			$("#rating_msg1").html(arr[1]);
			$("#rating_msg2").html(arr[2]);
			$("#rating_msg3").html(arr[0]);
		}
	});
	
	$("#scoresend").click(function(){
		var rc=false;
		for(var i=1;i<=10;i++){
			if( $('#rating'+i).get(0).checked){
				rc=true;
				break;
			}
		}
		if(!rc){alert('你还没选取分数');return;}
		
		$.get(maccms_path +'inc/ajax.php?id='+id+'&action=score&ac3=all&score='+ i ,function (obj){
			if(obj.indexOf("haved")!=-1){
				alert('你已经评过分啦');
			}else{
				var arr = obj.split(",");
				$("#rating_msg1").html(arr[1]);
				$("#rating_msg2").html(arr[2]);
				$("#rating_msg3").html(arr[0]);
				alert('感谢你的参与!');
			}
			return false;
		});
	});
}

function getComment(url){
	$.get(url,function(obj){
		if (obj=="err"){
			$("#maccms_comment").html("<font color='red'>发生错误</font>");
		}else{
			$("#maccms_comment").html(obj);
		}
	});
}
function getGbook(id,name){
	$.get(maccms_path + "plus/gbook/?action=main&id="+id+"&name="+ encodeURI(name),function(obj){
		if (obj=="err"){
			$("#maccms_gbook").html("<font color='red'>发生错误</font>");
		}else{
			$("#maccms_gbook").html(obj);
		}
	});
}

function history_New(vurl, vname) {
    var urla,
    flag;
    flag = true;
    for (i = 0; i < 20; i++) {
        urla = $.cookie("vurl" + i);
        if (urla == vurl) {
            $.cookie("vurl" + i, vurl, {
                path: '/'
            });
            $.cookie("vname" + i, vname, {
                path: '/'
            });
            flag = false;
            break
        }
    }
    if (flag == true) {
        for (i = 20 - 1; i > 0; i--) {
            if ($.cookie("vurl" + (i - 1)) != null) {
                $.cookie("vurl" + i, $.cookie("vurl" + (i - 1)), {
                    path: '/'
                });
                $.cookie("vname" + i, $.cookie("vname" + (i - 1)), {
                    path: '/'
                })
            }
        }
        $.cookie("vurl0", vurl, {
            path: '/'
        });
        $.cookie("vname0", vname, {
            path: '/'
        })
    }
}
function history_Look(num) {
    var i;
    var tvurl,
    tvname,
    s,
    str;
    str = "<li><a href='{vurl}' target='_blank'>{vname}</a></li>";
    for (i = 0; i < 20; i++) {
        tvurl = $.cookie("vurl" + i);
        tvname = $.cookie("vname" + i);
        if (tvurl != null && tvname != null) {
            s = str.replace(/{vname}/gi, tvname).replace(/{vurl}/gi, tvurl);
            document.writeln(s)
        }
        if (i == num) {
            break
        }
    }
}
function history_del(){
    var name,
    name1,
    domain,
    path;
    path = "/";
    domain = "";
    for (i = 0; i < 20; i++) {
        name = "vurl" + i;
        name1 = "vname" + i;
        if ($.cookie(name)) {
            document.cookie = name + "=" + ((path) ? "; path=" + path: "") + ((domain) ? "; domain=" + domain: "") + "; expires=Thu, 01-Jan-70 00:00:01 GMT";
            document.cookie = name1 + "=" + ((path) ? "; path=" + path: "") + ((domain) ? "; domain=" + domain: "") + "; expires=Thu, 01-Jan-70 00:00:01 GMT"
        }
    }
    window.location.reload()
}
window.onload = function(){
try {
    var timmingRun = (new Image());
    timmingRun.src = maccms_path + 'inc/timming.php?t=' + Math.random()
} catch(e) {}
};