<?php
require_once (dirname(__FILE__)."/ContentManager.php");
class TudouContent extends Content{
    
//3gphd ,3gp
	const BASE_URL="http://m.tudou.com/view.do?code={id}"; //http://m.tudou.com/view.do?code=154944264
	
	private $contentparmStart="iid: ";
  	private $contentparaend=",cdn:";
  	private $htmlparmStart="down.do";
  	private $htmlparaend="\"";
  	private $notfound='/wap/wrong';
  	private $p_code="GB2312";
  	
  	private $downloadUrl="http://m.tudou.com/down.do";

    public function parseAndroidVideoUrl($url,$p_coding,$p_script){    	
  		$content = getPage($url, $this->p_code);
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
  		  $pageCode = getPageWindow($url, $this->p_code);
  		  $downUrls=getArray($pageCode, $this->htmlparmStart,$this->htmlparaend); 
  		  $downArray =explode("{Array}",$downUrls);
  	      for ($i=0 ;$i< count($downArray);$i++){
  	      	 $tempUrl=$downArray[$i];  
//  	      	 var_dump($tempUrl)	;	  
  	      	 		
  		  	 if(strpos( $tempUrl,"codetype=5") !==false && strpos($videoTyps, "codetype=5") ===false){  		  	 	 
//  		  	 	$location = getLocation($this->downloadUrl.replaceStr($tempUrl,"&amp;","&"));
//  		  	    if(!isN($location) && strpos($location, $this->notfound) ===false){
//  		      	  $videoAddressUrl4=MovieType::TOP_CLEAR.MovieType::VIDEO_NAME_URL_SEP.$location;	
//  		      	  $videoTyps=$videoTyps."codetype=5";		 
//  		        }
  		        $location=$this->getRealUrls($tempUrl);
  		        if($location !==false && !isN($location)){
  		        	$videoAddressUrl4=MovieType::TOP_CLEAR.MovieType::VIDEO_NAME_URL_SEP.$location;	
  		      	  $videoTyps=$videoTyps."codetype=5";
  		        }
  		  	 }  		  	
  		  	 if(strpos( $tempUrl,"codetype=3") !==false && strpos($videoTyps, "codetype=3") ===false){  		  	 	 
//  		  	 	$location = getLocation($this->downloadUrl.replaceStr($tempUrl,"&amp;","&"));
//  		  	    if(!isN($location) && strpos($location, $this->notfound) ===false){
  		  	 	$location=$this->getRealUrls($tempUrl);
  		        if($location !==false && !isN($location)){
  		      	  $videoAddressUrl3=MovieType::HIGH_CLEAR.MovieType::VIDEO_NAME_URL_SEP.$location;	
  		      	  $videoTyps=$videoTyps."codetype=3";		 			 
  		        }
  		  	 }  		  	
  		  	 if(strpos( $tempUrl,"codetype=2") !==false && strpos($videoTyps, "codetype=2") ===false){  		  	 	 
//  		  	 	$location = getLocation($this->downloadUrl.replaceStr($tempUrl,"&amp;","&"));
//  		  	    if(!isN($location) && strpos($location, $this->notfound) ===false){
  		  	 	$location=$this->getRealUrls($tempUrl);
  		        if($location !==false && !isN($location)){
  		      	  $videoAddressUrl2=MovieType::NORMAL.MovieType::VIDEO_NAME_URL_SEP.$location;	
  		      	  $videoTyps=$videoTyps."codetype=2";		 			 
  		        }
  		  	 }  		  	
  		  	 if(strpos( $tempUrl,"codetype=1") !==false && strpos($videoTyps, "codetype=1") ===false){  		  	 	 
//  		  	 	$location = getLocation($this->downloadUrl.replaceStr($tempUrl,"&amp;","&"));
//  		  	    if(!isN($location) && strpos($location, $this->notfound) ===false){
  		  	 	$location=$this->getRealUrls($tempUrl);
  		        if($location !==false && !isN($location)){
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
  		    		   		  
  		}
  		return $videoAddressUrl;
  	}
  	
    public function checkHtmlCanPlay($url,$p_coding){
  		$content = getPage($url, $this->p_code);
  		return false;
  	}
  	const ENCODE_URL_STAT="encodeurl=";
  	
  	function getRealUrls($url){  		
  		$url = getBodys($url, TudouContent::ENCODE_URL_STAT);
  		try{
  			return base64_decode($url);
  		}catch(Exception $e) {
  			return false;
  		}
  	}
  	
	private $p_videocodeApiUrl="http://v2.tudou.com/v.action?noCache=64648&ui=0&hd=2&retc=1&mt=0&sid=11000&st=3&si=11000&vn=02&it={PROD_ID}&pw=";
	private $p_videourlstart="iid: ";
	private $p_videourlend=",cdn:";  //http://v.pptv.com/show/rOeRD3fdTYvubNQ.html
    public function parseIOSVideoUrl($url,$p_coding,$p_script){
  		$content = getPage($url, $this->p_code);
  		return "";
//  		return $this->parseIOSVideoUrlByContent($content, $p_coding,$p_script);
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