<?php
require_once (dirname(__FILE__)."/ContentManager.php");
class DefaultContent extends Content{
	//3gphd ,3gp http://m.youku.com/wap/pvs?id=XNDIxOTcyMTU2&format=3gphd
	const BASE_URL="http://m.youku.com/wap/pvs?id={id}&format={format}";
	
	private $contentparmStart="var videoId2= '"; //
  	private $contentparaend="';";
  	private $htmlparmStart="id_";
  	private $htmlparaend=".html";
  	private $notfound='/wap/wrong';

  	private $p_code="UTF-8";
    public function parseAndroidVideoUrl($url,$p_coding,$p_script){
    	return "";
  	}
  	public function parseAndroidVideoUrlByContent($content, $p_coding,$p_script){
  		return "";
  	}
  	
  	private function getAndroidVideoUrl($vid){
    	return "";
  	}
  	
  	public function checkHtmlCanPlay($url,$p_coding){
//  		$content = getPage($url, $this->p_code);
  		return false;
  	}
  	
    private $p_videocodeApiUrl="http://v.youku.com/player/getM3U8/vid/{PROD_ID}/type/{mType}/ts/{now_date}/useKeyframe/0/v.m3u8";
	private $p_videourlstart="videoId = '";
	private $p_videourlend="'";  //http://v.pptv.com/show/rOeRD3fdTYvubNQ.html
    public function parseIOSVideoUrl($url,$p_coding,$p_script){
    	return "";
  	}
  	
  	public function parseIOSVideoUrlByContent($content, $p_coding,$p_script){
    	return "";
  	}
  	
  }
?>