<?php
require_once (dirname(__FILE__)."/ContentManager.php");
class PPTVContent extends Content{
    public function parseAndroidVideoUrl($url,$p_coding,$p_script){
  		$content = getPage($url, $p_coding);
  		return $this->parseAndroidVideoUrlByContent($content, $p_coding,$p_script);
  	}
  	public function parseAndroidVideoUrlByContent($content, $p_coding,$p_script){
  		return "";
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