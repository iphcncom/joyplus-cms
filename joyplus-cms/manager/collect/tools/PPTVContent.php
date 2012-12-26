<?php
require_once (dirname(__FILE__)."/ContentManager.php");

class PPTVContent extends Content{
	private $p_videocodeApiUrlParamstart="\"rid\":\"";
	private $p_videocodeApiUrlParamend="\"";
	private $p_videocodeApiUrl="http://api.v.pptv.com/api/ipad/play.js?rid={PROD_ID}";
	private $p_videourlstart="\"data\":\"";
	private $p_videourlend="\"}";  //http://v.pptv.com/show/rOeRD3fdTYvubNQ.html
	
  	private $p_code="UTF-8";
    public function parseAndroidVideoUrl($url,$p_coding,$p_script){
      return "";
  	}
  	public function parseAndroidVideoUrlByContent($content, $p_coding,$p_script){
  		return "";
  	}
    public function checkHtmlCanPlay($url,$p_coding){
  		$content = getPage($url, $this->p_code);
  		return false;
  	}
  	
    public function parseIOSVideoUrl($url,$p_coding,$p_script){
  		$content = getPage($url, "utf-8");  
//  		var_dump($url);		
  		return $this->parseIOSVideoUrlByContent($content, $p_coding,$p_script);
  	}
  	
  	public function parseIOSVideoUrlByContent($content, $p_coding,$p_script){
  		$videoUrlParam = getBody($content,$this->p_videocodeApiUrlParamstart,$this->p_videocodeApiUrlParamend);
		$videoUrlParam = replaceLine($videoUrlParam);
//		var_dump($videoUrlParam);
		$p_videoUrlApi = replaceStr($this->p_videocodeApiUrl,"{PROD_ID}",$videoUrlParam);
		$videoUrlApiCode =getPageWindow($p_videoUrlApi,$this->p_code);			
		$videoAddressUrl = getBody($videoUrlApiCode,$this->p_videourlstart,$this->p_videourlend);
		return $videoAddressUrl;
  	}
  }
?>