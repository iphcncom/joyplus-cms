function AdsStart() {
    mplayer.height = 62;
    $('#buffer')[0].style.height = "88%"
    $('#buffer')[0].style.display = 'block'
}
function AdsEnd() {
    $('#buffer')[0].style.display = 'none';
    mplayer.height = "100%"
}
function BaiduStatus() {
    if (mplayer.IsPlaying()) {
        AdsEnd();
    } else {
        AdsStart();
    }
}
function QvodStatus() {
    if (mplayer.Full == 0) {
        if (mplayer.PlayState == 3) {
            AdsEnd();
        } else {
            AdsStart();
        }
    }
}

function QvodNextDown(){
	if(mplayer.get_CurTaskProcess() > 900 && !bstartnextplay){
		mplayer.StartNextDown(Player.NextUrl);
		bstartnextplay = true;
	}
}
function Qvodurl(url,urlname){
	if(url == undefined){return "";}
	if(url == ""){return "";}
	var qvodname = parent.playname.replace(/\//g,"")+urlname;
	if(url.indexOf("vod://")>0){
		url = url.split("|");
		qvodurl = url[0]+"|"+url[1]+"|[www.maccms.com]"+qvodname+".rmvb|";
		return qvodurl;
	}
	return url;
}