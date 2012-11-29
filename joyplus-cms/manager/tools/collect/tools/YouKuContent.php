<?php
require_once (dirname(__FILE__)."/ContentManager.php");
class YouKuContent extends Content{
	//3gphd ,3gp
	const BASE_URL="http://m.youku.com/wap/pvs?id={id}&format={format}";
	
	private $contentparmStart="var videoId2= '";
  	private $contentparaend="';";
  	private $htmlparmStart="id_";
  	private $htmlparaend=".html";
  	private $notfound='/wap/wrong';

    public function parseAndroidVideoUrl($url,$p_coding,$p_script){
    	$vid = getBody($url,$this->htmlparmStart,$this->htmlparaend);  
//    	$vid=null;
    	if(!isN($vid)){
    		return $this->getAndroidVideoUrl($vid);
    	}
  		$content = getPage($url, $p_coding);
//  		var_dump($content);
  		return $this->parseAndroidVideoUrlByContent($content, $p_coding,$p_script);
  	}
  	public function parseAndroidVideoUrlByContent($content, $p_coding,$p_script){
  		$vid = getBody($content,$this->contentparmStart,$this->contentparaend); 
//  		writetofile("daa.txt", $vid); 		
  		return $this->getAndroidVideoUrl($vid);
  	}
  	
  	private function getAndroidVideoUrl($vid){
  		$videoAddressUrl="";
  	  if(isset($vid) && !is_null($vid)){
  		  $url = replaceStr(YouKuContent::BASE_URL,"{id}",$vid);
  		  //check gaoqing
  		  $hdurl = replaceStr($url,"{format}","3gphd");
  		  $location = getLocation($hdurl);
//  		  writetofile("daa.txt", $hdurl);
  		  if(!isN($location) && strpos($location, $this->notfound) ===false){
  		  	$videoAddressUrl=$videoAddressUrl.MovieType::HIGH_CLEAR.MovieType::VIDEO_NAME_URL_SEP.$location.MovieType::VIDEO_SEP_VERSION;			 
  		  }
  		  
  	      $hdurl = replaceStr($url,"{format}","3gp");
  		  $location = getLocation($hdurl);
//  		  writetofile("daa.txt", $hdurl);
  		  if(!isN($location) && strpos($location, $this->notfound) ===false){
  		  	$videoAddressUrl=$videoAddressUrl.MovieType::NORMAL.MovieType::VIDEO_NAME_URL_SEP.$location;
  		  }
  		  
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