<?php
require_once ("ContentManager.php");
class LetvContent extends Content{
//3gphd ,3gp
	const BASE_URL="http://m.letv.com/playvideo.php?id={id}&mmsid={mmsid}";
	
	private $contentparmStart="vid:";
  	private $contentparaend=",";
  	private $contentparmStart2="mmsid:";
  	private $contentparaend2=",";
  	private $notfound='/wap/wrong';

    public function parseAndroidVideoUrl($url,$p_coding,$p_script){    	
  		$content = getPage($url, $p_coding);
//  		var_dump($content);
  		return $this->parseAndroidVideoUrlByContent($content, $p_coding,$p_script);
  	}
  	public function parseAndroidVideoUrlByContent($content, $p_coding,$p_script){
  		$vid = getBody($content,$this->contentparmStart,$this->contentparaend); 
	    $mmsid = getBody($content,$this->contentparmStart2,$this->contentparaend2); 
  		return $this->getAndroidVideoUrl($vid,$mmsid);
  	}
  	
  	private function getAndroidVideoUrl($vid,$mmsid){
  		$videoAddressUrl="";
  	  if(isset($vid) && !is_null($vid)){
  		  $url = replaceStr(LetvContent::BASE_URL,"{id}",$vid);
  		  //check gaoqing
  		  $hdurl = replaceStr($url,"{mmsid}",$mmsid);
  		  var_dump($hdurl);
  		  $location = getLocation($hdurl);
//  		  writetofile("daa.txt", $hdurl);
  		  if(!isN($location) && strpos($location, $this->notfound) ===false){
  		  	$videoAddressUrl=$videoAddressUrl.MovieType::HIGH_CLEAR.MovieType::VIDEO_NAME_URL_SEP.$location;			 
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