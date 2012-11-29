$(function(){
	$(".tb tr").mouseover(function(){
		$(this).addClass("odd");
	}).mouseout(function(){
		$(this).removeClass("odd");
	})
	//$(".tb tr:even").addClass("highlight");
})

String.prototype.trim=function(){
        return this.replace(/(^[\s\u3000]*)|([\s\u3000]*$)/g, "");
}

String.prototype.ltrim=function(){
	return this.replace(/(^\s*)/g, "");
}

String.prototype.rtrim=function(){
	return this.replace(/(\s*$)/g, "");
}

function checkAll(val,objname){
	$("input[name='"+objname+"']").each(function() {
		this.checked = val;
	});
}

playi=false;
function appendplay(i,playStr,serverStr){
	playStr=unescape(playStr);
	serverStr=unescape(serverStr);
	if(playi==false){
		playi=i;
	}
	else{
		i=++playi;
	}
	var obj = $("#playurldiv" + i)[0];
	if(obj ==undefined){
	var area="<table width='100%' class='tb2'><tr><td width='11%'><input id='urlid"+i+"' name='urlid[]' type='hidden' value='0'/>&nbsp;播放器"+i+"：</td><td>&nbsp;播放器：<select id='urlfrom"+i+"' name='urlfrom[]'><option value='no'>暂无数据"+i+"</option>"+playStr+"</select>&nbsp;服务器组：<select id='urlserver"+i+"' name='urlserver[]'><option value='0'>暂无数据"+i+"</option>"+serverStr+"</select>&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"removeplay("+i+")\">删除</a>&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"moveUp("+i+")\">上移</a>&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"moveDown("+i+")\">下移</a>说明:每行一个地址，不能有空行。</td></tr><tr><td>&nbsp;数据地址"+i+":<br><input type='button' value='校正' title='校正右侧文本框中的数据格式' class='btn' onclick='repairUrl("+i+")' /><input type='button' value='倒序' title='把右侧文本框中的数据倒序排列' class='btn' onclick='orderUrl("+i+")' /><input type='button' value='去前缀' title='把右侧文本框中的数据前缀去掉' class='btn' onclick='delnameUrl("+i+")' /></td><td><textarea id='url"+i+"' name='url[]' style='width:700px;height:150px;'></textarea></td></tr></table>"
	var urldiv=document.createElement("div");
	urldiv.id = "playurldiv"+i;
	urldiv.className="playurldiv";
	urldiv.innerHTML=area;
	$("#urlarr").append(urldiv);
	}
	else{
		$("#playurldiv"+i).css("display","")
	}
}
function removeplay(m,n){	$('#playurldiv'+m).remove(); }

downi=false;
function appenddown(i,downStr){
	downStr=unescape(downStr);
	if(downi==false){
		downi=i;
	}
	else{
		i=++downi;
	}
	var obj = $("#downurldiv" + i)[0];
	if(obj ==undefined){
	var area="<table width='100%' class='tb2'><tr><td width='11%'><input id='downurlid"+i+"' name='downurlid[]' type='hidden' value='0'/>&nbsp;下载"+i+"：</td><td>&nbsp;类型：<select id='downurlfrom"+i+"' name='downurlfrom[]'><option value='no'>暂无数据"+i+"</option>"+downStr+"</select>&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"removedown("+i+")\">删除</a>&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"moveUp('down',"+i+")\">上移</a>&nbsp;&nbsp;<a href=\"javascript:void(0)\" onclick=\"moveDown('down',"+i+")\">下移</a>说明:每行一个地址，不能有空行。</td></tr><tr><td>&nbsp;数据地址"+i+":</td><td><textarea id='downurl"+i+"' name='downurl[]' rows='8' cols='120'></textarea></td></tr></table>"
	var urldiv=document.createElement("div");
	urldiv.id = "downurldiv"+i;
	urldiv.className="downurldiv";
	urldiv.innerHTML=area;
	$("#downurlarr").append(urldiv);
	}
	else{
		$("#downurldiv"+i).css("display","")
	}
}
function removedown(m,n){	$('#downurldiv'+m).remove(); }

function moveUp(div_id){
	div_id="playurldiv"+div_id;
	var l_div,c_div,e_i
	$(".playurldiv").each(function(i){
	   e_i = i;
	   if(this.id == div_id){
	    c_div = this;
	    return false;
	   }else{
	    l_div = this;
	   } 
	});
	if(e_i != 0){
	   $(c_div).slideUp(100).delay(300).slideDown(600);
	   $(l_div).slideUp(100).delay(300).slideDown(600);
	}
	$(c_div).insertBefore($(l_div));
}
function moveDown(div_id){
	div_id= "playurldiv"+div_id;
	var n_div,c_div,q_i,e_i
	$(".playurldiv").each(function(i){
	   e_i = i+1;
	   if(this.id == div_id){
	    c_div = this;
	    return false;
	   }
	});
	n_div = $(".playurldiv").eq(e_i);
	if($(".playurldiv").length != e_i){
	   $(c_div).slideUp(100).delay(300).slideDown(600);
	   $(n_div).slideUp(100).delay(300).slideDown(600);
	}
	$(n_div).insertBefore($(c_div));
}

function repairUrl(i){
	var arr1,s1,s2,urlarr,urlarrcount;
	s1 = $('#url'+i).attr("value"); s2="";
	if (s1.length==0){alert('请填写地址');return false;}
	if(navigator.userAgent.indexOf("Firefox")>0){ arr1=s1.split("\n");}else{ arr1=s1.split("\r\n");}
	for(j=0;j<arr1.length;j++){
		if(arr1[j].length>0){
			urlarr = arr1[j].split('$'); urlarrcount = urlarr.length-1;
			if(urlarrcount==0){
				arr1[j]='第'+(j<9 ? '0' : '')+(j+1)+'集$'+arr1[j];
			}
			s2+=arr1[j]+"\r\n";
		}
	}
	$('#url'+i).attr( "value",s2.trim() ) ;
}
function orderUrl(i){
	var arr1,s1,s2,urlarr,urlarrcount;
	s1 = $('#url'+i).attr("value"); s2="";
	if (s1.length==0){alert('请填写地址');return false;}
	if(navigator.userAgent.indexOf("Firefox")>0){ arr1=s1.split("\n");}else{ arr1=s1.split("\r\n");}
	for(j=arr1.length-1;j>=0;j--){
		if(arr1[j].length>0){
			s2+=arr1[j]+"\r\n";
		}
	}
	$('#url'+i).attr( "value",s2.trim() ) ;
}

function delnameUrl(i){
	var arr1,s1,s2,urlarr,urlarrcount;
	s1 = $('#url'+i).attr("value"); s2="";
	if (s1.length==0){alert('请填写地址');return false;}
	if(navigator.userAgent.indexOf("Firefox")>0){ arr1=s1.split("\n");}else{ arr1=s1.split("\r\n");}
	
	for(j=0;j<arr1.length;j++){
		if(arr1[j].length>0){
			urlarr = arr1[j].split('$'); urlarrcount = urlarr.length-1;
			if(urlarrcount==0){
				arr1[j] = arr1[j];
			}
			else{
				arr1[j] = urlarr[1];
			}
			s2+=arr1[j]+"\r\n";
		}
	}
	$('#url'+i).attr( "value",s2.trim() ) ;
}

function creatediv(z,w,h){
	
	$('<div id="confirm"></div>')
	.css('position','absolute')
	.css('z-index',z+1)
	.css('top','200px')
	.css('left','300px')
	.appendTo("body");
}
function closew(){	$("#confirm").remove(); }


function plset(type,flag)
{
	var ids = "",tid="";
	if(flag=="art"){ tid="a_id[]"; }else{ tid="d_id[]";	}
	$("input[name='"+tid+"']").each(function() {
		if(this.checked){ ids =  ids + this.value + ","; }
	});
	if (ids != ""){
		ids = ids.substring(0,ids.length-1);
		var topicName=$("#"+type);
		var offset=topicName.offset();
		var topicTop=offset.top;
		var topicLeft=offset.left;
	    creatediv(99997,250,20);
		var ShowDiv=$("#confirm");
		ShowDiv.css('border','1px solid #55BBFF').css('background','#C1E7FF').css('padding',' 3px 0px 3px 4px').css('top',topicTop-4+'px').css('left',topicLeft-100+'px').html('正在加载内容......');
		ShowDiv.load("admin_ajax.php?id="+ ids +"&show=1&flag="+flag+"&action="+ type);
	}
	else{
		alert("请至少选择一个数据!");
	}
}

function setday(type,movieid,flag)
{ 
	var topicName=$("#"+type+movieid);
	var offset=topicName.offset();
	var topicTop=offset.top;
	var topicLeft=offset.left;
    creatediv(99997,250,20);
	var ShowDiv=$("#confirm");
	ShowDiv.css('border','1px solid #55BBFF').css('background','#C1E7FF').css('padding',' 3px 0px 3px 4px').css('top',topicTop-4+'px').css('left',topicLeft-100+'px').html('正在加载内容......');
	ShowDiv.load("admin_ajax.php?id="+ movieid +"&show=1&flag="+flag+"&action="+ type);
}

function ajaxsubmit(movieid,type,flag)
{
	if (type=="plrq"){
		var num1 = $("#num1").val();
		var num2 = $("#num2").val();
		$.get("admin_ajax.php","id="+movieid+"&show=2&num1="+num1+"&num2="+num2+"&action="+type+"&flag="+flag, function(obj) {
			oncomplete(obj);
		});
	}
	else{
		var ajaxcontent = $("#ajaxcontent").val();
		$.get("admin_ajax.php","id="+movieid+"&show=2&ajaxcontent="+ajaxcontent+"&action="+type+"&flag="+flag, function(obj) {
			oncomplete(obj);
		});
	}
}
function ajaxdivdel(movieid,type,flag)
{
	$.get("admin_ajax.php","id="+movieid+"&show=3&action="+type+"&flag="+flag, function(obj) {
		oncomplete(obj);
	});
}
function ajaxckname(moviename)
{
	$.get("admin_ajax.php?name="+ encodeURI(moviename) + "&action=ckname",function(obj){
		oncomplete(obj);
	});
}
function oncomplete(ajaxobj)
{
	if(ajaxobj){
		var fhajax = ajaxobj;
		if (fhajax == "reload"){ location=location; }
		else{
			var plarr = fhajax.split('|||');
			for (i=0;i<plarr.length;i++){
				if (plarr[i] !=""){
					fhajax=plarr[i].split('$');
					$("#"+fhajax[0]).html(fhajax[1]);
				}
			}
		}
		closew();
	}
}