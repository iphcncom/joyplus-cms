<?php

require_once ("admin_conn.php");
require_once ("collect/MovieType.php");
require_once ("collect/tools/ContentManager.php");
$webUrls  = be("all", "weburls");
$playerfrom= be("all", "playerfrom");
$webUrls=replaceStr(trim($webUrls), Chr(10), "{Array}");
$contentM=ContentProviderFactory::getContentProvider($playerfrom);
//$contentM=null;
if($contentM ==null){
	echo "不支持此播放器,烦请选择正确的播放器，目前支持乐视，56（我乐），风行，优酷，新浪,奇艺。"; //，56（我乐），风行
}else {
if(!isN($webUrls)){
   $webUrlsArray= explode("{Array}", $webUrls);  
    $videoUrls="";
   foreach ($webUrlsArray as $weburl){
   	 $name="";
   	 $url="";
   	 $nameUrl= explode("$", $weburl);
   	 if(count($nameUrl)==1){
   	 	$url=$nameUrl[0];
   	 }
   	 
     if(count($nameUrl)==2){
   	 	$url=$nameUrl[1];
   	 	$name=$nameUrl[0].'$';
   	 }
   	 $name=replaceStr($name, '\"', '"');
   	 $videoUrl = $contentM->parseIOSVideoUrl($url, "utf-8", null);
	 $androidUrl = $contentM->parseAndroidVideoUrl($url, "utf-8", null);
	 $url="";
     if(!isN($androidUrl) ){
	    	$url= $androidUrl;
	 }
     if( !isN($videoUrl) ){
     	    if(isN($url)){
     	    	$url= $videoUrl;
     	    }else {
	    	  $url= $url.MovieType::VIDEO_SEP_VERSION.$videoUrl;
     	    }
	 }
	 if(!isN($url)){
	 	$url=$name.$url;
	 }
	 if(isN($videoUrls)){
	 	$videoUrls=$url;
	 }else {	 	
	   $videoUrls=$videoUrls.'{Array}'.$url;   	 
	 }
   }
   $videoUrls= replaceStr($videoUrls,"{Array}",chr(13));
   echo  $videoUrls;
   	
}
}

//echo $webUrls

?>