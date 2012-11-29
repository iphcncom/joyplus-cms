<?php
require_once (dirname(__FILE__)."/ContentManager.php");
class WLContent extends Content{ //56
	//3gphd ,3gp http://m.youku.com/wap/pvs?id=XNDIxOTcyMTU2&format=3gphd
	const BASE_URL="http://m.56.com/view/id-{ID}.html";
	
	private $contentparmStart="<div class=\"t56_module02_content\">"; //
  	private $contentparaend="<!-- share box -->";
  	private $htmlparmStart="v_";
  	private $htmlparaend=".htm";
  	private $notfound='/wap/wrong';
    private $htmlparmStartUrl="<a href=\"";
  	private $htmlparaendUrl="\" class=\"";
  	private $p_code="UTF-8";
  	
    public function parseAndroidVideoUrl($url,$p_coding,$p_script){
    	$vid = getBody($url,$this->htmlparmStart,$this->htmlparaend); 
    	$url = replaceStr(WLContent::BASE_URL, "{ID}", $vid);
//  		$content = getPage($url, "UTF-8");
       return "";
//  		return $this->parseAndroidVideoUrlByContent($content, $p_coding,$p_script);
  	}
  	
  	public function parseAndroidVideoUrlByContent($content, $p_coding,$p_script){
  		return "";
  		//writetofile("content.txt", $content);
//  		$vid = getBody($content,$this->contentparmStart,$this->contentparaend);
//  		$downUrls=getArray($vid, $this->htmlparmStartUrl,$this->htmlparaendUrl); 
//  		$downArray =explode("{Array}",$downUrls);
//  	    if(isset($downArray) && is_array($downArray)){
//		  if(count($downArray)==2){
//		  	if(!isN($downArray[1])){
//		  	  $videoAddressUrl=MovieType::HIGH_CLEAR.MovieType::VIDEO_NAME_URL_SEP.$downArray[1].MovieType::VIDEO_SEP_VERSION;
//		  	  $videoAddressUrl=$videoAddressUrl.MovieType::NORMAL.MovieType::VIDEO_NAME_URL_SEP.$downArray[0];
//		  	}else {
//		  		$videoAddressUrl=$videoAddressUrl.MovieType::HIGH_CLEAR.MovieType::VIDEO_NAME_URL_SEP.$downArray[0];
//		  	}			  	
//		  }else if(count($videoUrls)==1){
//		  	$videoAddressUrl=MovieType::HIGH_CLEAR.MovieType::VIDEO_NAME_URL_SEP.$downArray[0];
//		  }
//		}
//		return $videoAddressUrl;
  	}
  	
  	
  	
  	public function checkHtmlCanPlay($url,$p_coding){
  		$content = getPage($url, $this->p_code);
  		return false;
  	}
  	
  	const BASE_IOS_URL="http://vxml.56.com/m3u8/{ID}/"; // http://vxml.56.com/m3u8/66715295
//    private $p_videocodeApiUrl="http://v.youku.com/player/getM3U8/vid/{PROD_ID}/type/{mType}/ts/{now_date}/useKeyframe/0/v.m3u8";
	private $p_videourlstart="v_";
	private $p_videourlend=".htm";  //http://v.pptv.com/show/rOeRD3fdTYvubNQ.html
	
    public function parseIOSVideoUrl($url,$p_coding,$p_script){
  		$id = getBody($url,$this->p_videourlstart,$this->p_videourlend);
         $id=base64_decode($id);
         $url = replaceStr(WLContent::BASE_IOS_URL, "{ID}", $id);
         $videoAddressUrl=$videoAddressUrl.MovieType::HIGH_CLEAR.MovieType::VIDEO_NAME_URL_SEP.$url;
          return $videoAddressUrl;
  	}
  	
  	public function parseIOSVideoUrlByContent($content, $p_coding,$p_script){
//  	    $videoUrlParam = getBody($content,$this->p_videourlstart,$this->p_videourlend);
//  	    $videoUrlParam=replaceLine($videoUrlParam);
//		$videoAddressUrl = replaceStr($this->p_videocodeApiUrl,"{PROD_ID}",$videoUrlParam);
//		if(strpos($videoAddressUrl, MovieType::VIDEO_SEP_VERSION) !==false){
//			$videoAddressUrls=MovieType::TOP_CLEAR.MovieType::VIDEO_NAME_URL_SEP.replaceStr($videoAddressUrl,MovieType::VIDEO_SEP_VERSION,MovieType::TOP_CLEAR).MovieType::VIDEO_SEP_VERSION;
//		    $videoAddressUrls=$videoAddressUrls.MovieType::HIGH_CLEAR.MovieType::VIDEO_NAME_URL_SEP.replaceStr($videoAddressUrl,MovieType::VIDEO_SEP_VERSION,MovieType::HIGH_CLEAR).MovieType::VIDEO_SEP_VERSION;
//		    $videoAddressUrls=$videoAddressUrls.MovieType::NORMAL.MovieType::VIDEO_NAME_URL_SEP.replaceStr($videoAddressUrl,MovieType::VIDEO_SEP_VERSION,MovieType::NORMAL);
//		    $videoAddressUrl=$videoAddressUrls;
//		}
//		return $videoAddressUrl;
       return "";
  	}
  	
  }
?>