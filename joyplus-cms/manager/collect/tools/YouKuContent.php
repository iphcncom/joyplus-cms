<?php
require_once (dirname(__FILE__)."/ContentManager.php");
class YouKuContent extends Content{
	//3gphd ,3gp http://m.youku.com/wap/pvs?id=XNDIxOTcyMTU2&format=3gphd
	const BASE_URL="http://m.youku.com/wap/pvs?id={id}&format={format}";
	
	private $contentparmStart="var videoId2= '"; //
  	private $contentparaend="';";
  	private $htmlparmStart="id_";
  	private $htmlparaend=".html";
  	private $notfound='/wap/wrong';

  	private $p_code="UTF-8";
    public function parseAndroidVideoUrl($url,$p_coding,$p_script){
    	$vid = getBody($url,$this->htmlparmStart,$this->htmlparaend);  
//    	$vid=null;
    	if(!isN($vid)){
    		return $this->getAndroidVideoUrl($vid);
    	}
  		$content = getPage($url, $this->p_code);
//  		var_dump($content);
  		return $this->parseAndroidVideoUrlByContent($content, $p_coding,$p_script);
  	}
  	public function parseAndroidVideoUrlByContent($content, $p_coding,$p_script){
  		//writetofile("content.txt", $content);
  		$vid = getBody($content,$this->contentparmStart,$this->contentparaend); 
//  		writetofile("daa.txt", $vid); 	
//  		var_dump($vid)	;
  		if($vid===false  || $vid==='' ){
  			return '';
  		}
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
          $flag=false;
  		  if(!isN($location) && strpos($location, $this->notfound) ===false){
  		  	$videoAddressUrl=$videoAddressUrl.MovieType::HIGH_CLEAR.MovieType::VIDEO_NAME_URL_SEP.$hdurl;	
  		  	$flag=true;		 
  		  }
  		  
  	      $hdurl = replaceStr($url,"{format}","3gp");
  		  $location = getLocation($hdurl);
//  		  writetofile("daa.txt", $hdurl);
  		  if(!isN($location) && strpos($location, $this->notfound) ===false){
  		  	if($flag){
  		  		$videoAddressUrl=$videoAddressUrl.MovieType::VIDEO_SEP_VERSION;
  		  	}
  		  	$videoAddressUrl=$videoAddressUrl.MovieType::NORMAL.MovieType::VIDEO_NAME_URL_SEP.$hdurl;
  		  }
  		  
  		}
  		return $videoAddressUrl;
  	}
  	
  	public function checkHtmlCanPlay($url,$p_coding){
  		$content = getPage($url, $this->p_code);
  		return false;
  	}
  	
    private $p_videocodeApiUrl="http://v.youku.com/player/getM3U8/vid/{PROD_ID}/type/{mType}/ts/{now_date}/useKeyframe/0/v.m3u8";
	private $p_videourlstart="videoId = '";
	private $p_videourlend="'";  //http://v.pptv.com/show/rOeRD3fdTYvubNQ.html
    public function parseIOSVideoUrl($url,$p_coding,$p_script){
  		$content = getPage($url, $this->p_code);
  		return $this->parseIOSVideoUrlByContent($content, $p_coding,$p_script);
  	}
  	
  	public function parseIOSVideoUrlByContent($content, $p_coding,$p_script){
  	    $videoUrlParam = getBody($content,$this->p_videourlstart,$this->p_videourlend);
  	    $videoUrlParam=replaceLine($videoUrlParam);
//  	    var_dump($videoUrlParam);
  	    if($videoUrlParam===false || $videoUrlParam==='' ){
  			return '';
  		}
		$videoAddressUrl = replaceStr($this->p_videocodeApiUrl,"{PROD_ID}",$videoUrlParam);
		if(strpos($videoAddressUrl, MovieType::VIDEO_SEP_VERSION) !==false){
			$videoAddressUrls=MovieType::TOP_CLEAR.MovieType::VIDEO_NAME_URL_SEP.replaceStr($videoAddressUrl,MovieType::VIDEO_SEP_VERSION,MovieType::TOP_CLEAR).MovieType::VIDEO_SEP_VERSION;
		    $videoAddressUrls=$videoAddressUrls.MovieType::HIGH_CLEAR.MovieType::VIDEO_NAME_URL_SEP.replaceStr($videoAddressUrl,MovieType::VIDEO_SEP_VERSION,MovieType::HIGH_CLEAR).MovieType::VIDEO_SEP_VERSION;
		    $videoAddressUrls=$videoAddressUrls.MovieType::NORMAL.MovieType::VIDEO_NAME_URL_SEP.replaceStr($videoAddressUrl,MovieType::VIDEO_SEP_VERSION,MovieType::NORMAL);
		    $videoAddressUrl=$videoAddressUrls;
		}
		return $videoAddressUrl;
  	}
  	
  }
?>