function getPlayer(){
	getPlayers(play_vodnum);
}

function prev(){
	getPlayers(play_prenum);
}

function next(){
	getPlayers(play_nextnum);
}
function getPlayers(current_num){
	//alert(joyplus_playlist);
	var lists= joyplus_playlist.split('#');
	var vodid="";
	var length=lists.length;
	for(var i=0;i<length;i++){
		var tempValue= lists[i].split('$');
		if(tempValue[0] ==current_num){
			vodid=tempValue[1];
			var j=i-1;
			if(j<0){
				j=0;
			}
			var tempValue= lists[j].split('$');
			play_prenum=tempValue[0];
			
			var j=i+1;
			if(j>=length){
				j=length-1;
			}
			var tempValue= lists[j].split('$');
			play_nextnum=tempValue[0];
			//alert(play_nextnum);
			//alert(play_prenum);
		}
		
		
	}
	
	
	document.getElementById("naviTitle").innerHTML=playname+">>"+current_num;
	getPlayInfo(play_from,vodid);
}

function getPlayInfo(playfrom,vodid){
	var tempValue= vodid.split('{Array}');
	var vodid =tempValue[0];
	if(tempValue.length==2){
	  document.getElementById("download_play").href="/vod/download.php?path="+tempValue[1]+"=&from="+playfrom;
    }
    var info;
	if(playfrom=='letv'){
		info = playLetv(vodid);
	}
	if(playfrom=='youku'){
		info = playYouku(vodid);
	}if(playfrom=='pptv'){
		info = playinfopptv(vodid);
	}if(playfrom=='tudou'){
		info = playinfotudou(vodid);
	}
	document.getElementById("sohuplayer").innerHTML=info;
}

function playLetv(vodid){
	return "	<object width=\"620\" height=\"550\"> <param name=\"allowFullScreen\" value=\"true\">"
	+"<param name=\"flashVars\" value=\"id="+vodid+"\" />"
	+"<param name=\"movie\" value=\"http://i7.imgs.letv.com/player/swfPlayer.swf?autoplay=1\" />"
	+"<embed src=\"http://i7.imgs.letv.com/player/swfPlayer.swf?autoplay=1\" "
	+"	flashVars=\"id="+vodid+"\" width=\"620\" height=\"460\" "
	+" allowFullScreen=\"true\" type=\"application/x-shockwave-flash\" /></object>";
}

function playYouku(vodid){
	return "<embed src=\"http://player.youku.com/player.php/sid/"+vodid+"/v.swf?autoplay=1\"  "
	+"allowFullScreen=\"true\" quality=\"high\" width=\"620\" "
	+" height=\"470\" "
	+"	align=\"middle\" allowScriptAccess=\"always\" type=\"application/x-shockwave-flash\"></embed>";
}

function playinfopptv(vodid){
	return "<embed src=\"http://player.pptv.com/v/"+vodid+".swf?autoplay=1\" quality=\"high\" width=\"620\" height=\"460\" align=\"middle\" allowScriptAccess=\"always\" allownetworking=\"all\" type=\"application/x-shockwave-flash\" wmode=\"window\" allowFullScreen=\"true\"></embed>";
}


function playinfotudou(vodid){
	var tempValue= vodid.split('_');
	var id =tempValue[0];
	var resId= vodid.replace(id+"_","");
	//alert(id);
	//alert(resId);
	return "<embed src=\"http://www.tudou.com/a/"+resId+"/&resourceId=0_05_05_99&iid="+id+"&bid=05/v.swf\" "
	+" type=\"application/x-shockwave-flash\"  "
	+" allowscriptaccess=\"always\" allowfullscreen=\"true\" wmode=\"opaque\" width=\"620\" height=\"460\"></embed>";
}
