<?php
require_once ("ContentManager.php");
class SohuContent extends Content{
//3gphd ,3gp
	const BASE_URL="http://api.tv.sohu.com/video/playinfo/{id}.json?callback=?&encoding=gbk&api_key=f351515304020cad28c92f70f002261c&from=mweb";
	
	private $contentparmStart="var vid=\"";
  	private $contentparaend="\";";
  	private $contentparmStart2="\"downloadurl\":\"";
  	private $contentparaend2="\",\"";
  	private $notfound='/wap/wrong';
  	private $p_code="UTF-8";

    public function parseAndroidVideoUrl($url,$p_coding="UTF-8",$p_script){    	
  		$content = getPage($url, $this->p_code);
//  		var_dump($content);
  		return $this->parseAndroidVideoUrlByContent($content, $p_coding,$p_script);
  	}
  	public function parseAndroidVideoUrlByContent($content, $p_coding,$p_script){
  		$vid = getBody($content,$this->contentparmStart,$this->contentparaend); 
  		
  		return $this->getAndroidVideoUrl($vid);
  	}
  	
  	private function getAndroidVideoUrl($vid){
  		$videoAddressUrl="";
  	  if(isset($vid) && !is_null($vid)){
  		  $url = replaceStr(SohuContent::BASE_URL,"{id}",$vid);
  		 
  		  $location = getPage($url,"utf-8");
  		  $location=getBody($location,$this->contentparmStart2,$this->contentparaend2); 
  		  var_dump($location);
  		  if(!isN($location) ){
  		  	$videoAddressUrl=$videoAddressUrl.MovieType::HIGH_CLEAR.MovieType::VIDEO_NAME_URL_SEP.$location;			 
  		  }  		    		  
  		}
  		return $videoAddressUrl;
  	}
    public function checkHtmlCanPlay($url,$p_coding){
  		$content = getPage($url, $this->p_code);
  		return false;
  	}
    public function parseIOSVideoUrl($url,$p_coding,$p_script){
  		return "";
  	}
  	
  	private $p_videourlstart="{v:[\"";
	private $p_videourlend="\"]";  //http://v.pptv.com/show/rOeRD3fdTYvubNQ.html
  	public function parseIOSVideoUrlByContent($content, $p_coding,$p_script){  		
  		return "";
  	}
  }
?>