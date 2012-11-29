<?php
require_once (dirname(__FILE__)."/ContentManager.php");
class TudouContent extends Content{
    
//3gphd ,3gp
	const BASE_URL="http://m.tudou.com/view.do?code={id}";
	
	private $contentparmStart="iid: ";
  	private $contentparaend=",cdn:";
  	private $htmlparmStart="down.do";
  	private $htmlparaend="\"";
  	private $notfound='/wap/wrong';
  	
  	private $downloadUrl="http://m.tudou.com/down.do";

    public function parseAndroidVideoUrl($url,$p_coding,$p_script){    	
  		$content = getPage($url, $p_coding);
  		return $this->parseAndroidVideoUrlByContent($content, $p_coding,$p_script);
  	}
  	public function parseAndroidVideoUrlByContent($content, $p_coding,$p_script){
  		$vid = getBody($content,$this->contentparmStart,$this->contentparaend); 
  		$vid=replaceLine($vid);		
  		return $this->getAndroidVideoUrl($vid, $p_coding,$p_script);
  	}
  	
  	private function getAndroidVideoUrl($vid, $p_coding,$p_script){  		
  		$videoAddressUrl="";
  		$videoAddressUrl1="";
  		$videoAddressUrl2="";
  		$videoAddressUrl3="";
  		$videoAddressUrl4="";
  		$videoTyps="";
  	  if(isset($vid) && !is_null($vid)){
  		  $url = replaceStr(TudouContent::BASE_URL,"{id}",$vid); 
//  		  var_dump($url);
  		  $pageCode = getPageWindow($url, $p_coding);
  		  $downUrls=getArray($pageCode, $this->htmlparmStart,$this->htmlparaend); 
  		  $downArray =explode("{Array}",$downUrls);
  	      for ($i=0 ;$i< count($downArray);$i++){
  	      	 $tempUrl=$downArray[$i];  
//  	      	 var_dump($tempUrl)	;	  
  	      	 		
  		  	 if(strpos( $tempUrl,"codetype=5") !==false && strpos($videoTyps, "codetype=5") ===false){  		  	 	 
  		  	 	$location = getLocation($this->downloadUrl.replaceStr($tempUrl,"&amp;","&"));
  		  	    if(!isN($location) && strpos($location, $this->notfound) ===false){
  		      	  $videoAddressUrl4=MovieType::TOP_CLEAR.MovieType::VIDEO_NAME_URL_SEP.$location;	
  		      	  $videoTyps=$videoTyps."codetype=5";		 
  		        }
  		  	 }  		  	
  		  	 if(strpos( $tempUrl,"codetype=3") !==false && strpos($videoTyps, "codetype=3") ===false){  		  	 	 
  		  	 	$location = getLocation($this->downloadUrl.replaceStr($tempUrl,"&amp;","&"));
  		  	    if(!isN($location) && strpos($location, $this->notfound) ===false){
  		      	  $videoAddressUrl3=MovieType::HIGH_CLEAR.MovieType::VIDEO_NAME_URL_SEP.$location;	
  		      	  $videoTyps=$videoTyps."codetype=3";		 			 
  		        }
  		  	 }  		  	
  		  	 if(strpos( $tempUrl,"codetype=2") !==false && strpos($videoTyps, "codetype=2") ===false){  		  	 	 
  		  	 	$location = getLocation($this->downloadUrl.replaceStr($tempUrl,"&amp;","&"));
  		  	    if(!isN($location) && strpos($location, $this->notfound) ===false){
  		      	  $videoAddressUrl2=MovieType::NORMAL.MovieType::VIDEO_NAME_URL_SEP.$location;	
  		      	  $videoTyps=$videoTyps."codetype=2";		 			 
  		        }
  		  	 }  		  	
  		  	 if(strpos( $tempUrl,"codetype=1") !==false && strpos($videoTyps, "codetype=1") ===false){  		  	 	 
  		  	 	$location = getLocation($this->downloadUrl.replaceStr($tempUrl,"&amp;","&"));
  		  	    if(!isN($location) && strpos($location, $this->notfound) ===false){
  		      	  $videoAddressUrl1=MovieType::Liu_Chang.MovieType::VIDEO_NAME_URL_SEP.$location;	
  		      	  $videoTyps=$videoTyps."codetype=1";		 			 
  		        }
  		  	 }
//  		  	 var_dump($videoTyps)	;
  		  }
  		  $flag=false;
  		  if(!isN($videoAddressUrl4)){
  		  	$videoAddressUrl=$videoAddressUrl.$videoAddressUrl4;
  		  	$flag=true;
  		  }
  	  
  		  if(!isN($videoAddressUrl3)){
  		  	if($flag){
  		  		$videoAddressUrl=$videoAddressUrl.MovieType::VIDEO_SEP_VERSION;
  		  	}
  		  	$videoAddressUrl=$videoAddressUrl.$videoAddressUrl3;
  		  	$flag=true;
  		  }
  		  if(!isN($videoAddressUrl2)){
  		  	if($flag){
  		  		$videoAddressUrl=$videoAddressUrl.MovieType::VIDEO_SEP_VERSION;
  		  	}
  		  	$videoAddressUrl=$videoAddressUrl.$videoAddressUrl2;
  		  	$flag=true;
  		  }
  		  if(!isN($videoAddressUrl1)){
  		  	if($flag){
  		  		$videoAddressUrl=$videoAddressUrl.MovieType::VIDEO_SEP_VERSION;
  		  	}
  		  	$videoAddressUrl=$videoAddressUrl.$videoAddressUrl1;
  		  	$flag=true;
  		  }
  		  
  		  
//  		  $location = getLocation($hdurl);
////  		  writetofile("daa.txt", $hdurl);
  		  
//  		  
//  	      $hdurl = replaceStr($url,"{format}","3gp");
//  		  $location = getLocation($hdurl);
////  		  writetofile("daa.txt", $hdurl);
//  		  if(!isN($location) && strpos($location, $this->notfound) ===false){
//  		  	$videoAddressUrl=$videoAddressUrl.MovieType::NORMAL.MovieType::VIDEO_NAME_URL_SEP.$location;
//  		  }
  		  
  		}
  		return $videoAddressUrl;
  	}
 public function checkHtmlCanPlay($url,$p_coding){
  		$content = getPage($url, $p_coding);
  		return false;
  	}
    public function parseIOSVideoUrl($url,$p_coding,$p_script){
  		$content = getPage($url, $p_coding);
  		return $this->parseIOSVideoUrlByContent($content, $p_coding,$p_script);
  	}
  	public function parseIOSVideoUrlByContent($content, $p_coding,$p_script){
  		return "";
  	}
  }
?>