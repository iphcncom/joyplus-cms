var str1=MacPlayer.playurl;
if (str1.indexOf("/") >0){ str1= str1.split("/")[8] ; }
MacPlayer.playhtml = '<embed type="application/x-shockwave-flash" src="http://www.56.com/flashApp/56.10.07.23.swf" id="Player" bgcolor="#FFFFFF" quality="high" allowfullscreen="true" allowNetworking="internal" allowscriptaccess="never" wmode="transparent" menu="false" always="false"  pluginspage="http://www.macromedia.com/go/getflashplayer" width="100%" height="'+MacPlayer.height+'" flashvars="vid='+str1+'">';
MacPlayer.show();