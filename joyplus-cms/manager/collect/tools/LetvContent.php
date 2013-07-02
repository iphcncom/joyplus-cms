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
  	private $p_code="UTF-8";

    public function parseAndroidVideoUrl($url,$p_coding="UTF-8",$p_script){    	
  		//$content = getPageWindow($url, $this->p_code);
  		return "";
  	//	writetofile('stds.log',$content);var_dump($url);
//  		return $this->parseAndroidVideoUrlByContent($content, $p_coding,$p_script);
  	}
  	public function parseAndroidVideoUrlByContent($content, $p_coding,$p_script){
  		return "";
//  		$vid = getBody($content,$this->contentparmStart,$this->contentparaend); 
//	    $mmsid = getBody($content,$this->contentparmStart2,$this->contentparaend2); 
//	   // var_dump($mmsid); var_dump($vid);
//  		return $this->getAndroidVideoUrl($vid,$mmsid);
  	}
  	
  	private function getAndroidVideoUrl($vid,$mmsid){
  		$videoAddressUrl="";
  	  if(isset($vid) && !is_null($vid)){
  		  $url = replaceStr(LetvContent::BASE_URL,"{id}",$vid);
  		  //check gaoqing
  		  $hdurl = replaceStr($url,"{mmsid}",$mmsid);
//  		  var_dump($hdurl);
  		  //$location = getLocation($hdurl);
  		  
//  		  writetofile("daa.txt", $hdurl);
  		 // if(!isN($location) && strpos($location, $this->notfound) ===false){
  		  	$videoAddressUrl=$videoAddressUrl.MovieType::HIGH_CLEAR.MovieType::VIDEO_NAME_URL_SEP.$hdurl;			 
  		 // }  		    		  
  		}
  		return $videoAddressUrl;
  	}
    public function checkHtmlCanPlay($url,$p_coding){
  		$content = getPage($url, $this->p_code);
  		return false;
  	}
    public function parseIOSVideoUrl($url,$p_coding,$p_script){
  		$content = getPageWindow($url, $this->p_code);
  		return $this->parseIOSVideoUrlByContent($content, $p_coding,$p_script);
  	}
  	
  	private $p_videourlstart="{v:[\"";
	private $p_videourlend="\"]";  //http://v.pptv.com/show/rOeRD3fdTYvubNQ.html
  	public function parseIOSVideoUrlByContent($content, $p_coding,$p_script){
  	   $videoUrlParam = getBody($content,$this->p_videourlstart,$this->p_videourlend);	
//  	   var_dump($videoUrlParam);
  	   $videoAddressUrl="";
		 if(!isN($videoUrlParam)){					
		    $videoUrls = explode("\",\"", $videoUrlParam);
		    if(isset($videoUrls) && is_array($videoUrls)){
			  if(count($videoUrls)==3){
			  	$videoAddressUrl=MovieType::TOP_CLEAR.MovieType::VIDEO_NAME_URL_SEP.base64_decode($videoUrls[2]).MovieType::VIDEO_SEP_VERSION;
			  	$videoAddressUrl=$videoAddressUrl.MovieType::HIGH_CLEAR.MovieType::VIDEO_NAME_URL_SEP.base64_decode($videoUrls[1]).MovieType::VIDEO_SEP_VERSION;
			  	$videoAddressUrl=$videoAddressUrl.MovieType::NORMAL.MovieType::VIDEO_NAME_URL_SEP.base64_decode($videoUrls[0]);
			  }else if(count($videoUrls)==2){
			  	if(!isN($videoUrls[1])){
			  	  $videoAddressUrl=MovieType::HIGH_CLEAR.MovieType::VIDEO_NAME_URL_SEP.base64_decode($videoUrls[1]).MovieType::VIDEO_SEP_VERSION;
			  	  $videoAddressUrl=$videoAddressUrl.MovieType::NORMAL.MovieType::VIDEO_NAME_URL_SEP.base64_decode($videoUrls[0]);
			  	}else {
			  		$videoAddressUrl=$videoAddressUrl.MovieType::HIGH_CLEAR.MovieType::VIDEO_NAME_URL_SEP.base64_decode($videoUrls[0]);
			  	}
			  	
			  }else if(count($videoUrls)==1){
			  	$videoAddressUrl=MovieType::HIGH_CLEAR.MovieType::VIDEO_NAME_URL_SEP.base64_decode($videoUrls[0]);
			  }
		   }
	      }
	      return $videoAddressUrl;
  	}
  }
?>