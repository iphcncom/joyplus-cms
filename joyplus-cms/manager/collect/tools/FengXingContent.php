<?php
require_once (dirname(__FILE__)."/ContentManager.php");
class FengXingContent extends Content{ //56
	//3gphd ,3gp http://m.youku.com/wap/pvs?id=XNDIxOTcyMTU2&format=3gphd
	const BASE_URL="http://api.funshion.com/ajax/get_webplayinfo/{ID}/mp4";  //http://api.funshion.com/ajax/get_webplayinfo/22672/1/mp4
	const BASE_URL_MP3="http://jobsfe.funshion.com/query/v1/mp4/{cid}.json?bits={byterate}";
	
	private $contentparmStart="mediaid\":\""; //
  	private $contentparaend="\"";
  	private $contentparmStartNum="\"number\":\""; //
  	private $contentparaendNum="\"";
  	private $htmlparmStart="play/";
  	private $htmlparaend=".html";
  	private $notfound='/wap/wrong';

  	private $p_code="UTF-8"; //http://www.funshion.com/subject/play/103299/4
    public function parseAndroidVideoUrl($url,$p_coding,$p_script){
//    	echo $url;
    	$vid = getBodys($url,$this->htmlparmStart);  
//    	var_dump($vid);
//    	$vid=null;
    	if(!isN($vid) && count(explode("/", $vid)) ===2){
    		return $this->getAndroidVideoUrl($vid);
    	}
  		$content = getPageWindow($url, "UTF-8");
//  		var_dump($content);
  		return $this->parseAndroidVideoUrlByContent($content, $p_coding,$p_script);
  	}
  	public function parseAndroidVideoUrlByContent($content, $p_coding,$p_script){
//		writetofile("content.txt", $content);
  		$mediaid = getBody($content,$this->contentparmStart,$this->contentparaend);
  		$number  = getBody($content,$this->contentparmStartNum,$this->contentparaendNum);
//  		writetofile("daa.txt", $vid); 
         $vid=$mediaid.'/'.$number;	
//  		var_dump($vid)	;
  		return $this->getAndroidVideoUrl($vid);
  	}
  	
  	private function getAndroidVideoUrl($vid){
  		$videoAddressUrl="";
  		$videoAddressUrl1="";
  		$videoAddressUrl2="";
  		$videoAddressUrl3="";
  		$videoAddressUrl4="";
  	  if(isset($vid) && !is_null($vid)){
  		  $url = replaceStr(FengXingContent::BASE_URL,"{ID}",$vid);
  		  $mediaid=explode("/", $vid);
  		  $mediaid=$mediaid[0];
//  		  var_dump($mediaid);
  		  $content = getPageWindow($url, "UTF-8");
  		  try{
  		    $json = ContentProviderFactory::obj2arr(json_decode($content));
  		    if(isset($json) && !is_null($json) && isset($json['playinfos']) && !is_null($json['playinfos'])  ){
  		    	$playinfos=$json['playinfos'];
  		    	foreach ($playinfos as $playinfo){
  		    		$cid=$playinfo['cid'];
  		    		$t_mediaid=$playinfo['mediaid'];
  		    		if($t_mediaid !== $mediaid){
  		    			continue;
  		    		}
  		    		$clarity=$playinfo['clarity'];
  		    		$byterate=$playinfo['byterate'];
  		    		$tempUrl=replaceStr(FengXingContent::BASE_URL_MP3,"{cid}",$cid);
  		    		$tempUrl=replaceStr($tempUrl,"{byterate}",$byterate);
  		    		 
  		    		$tempjson = ContentProviderFactory::obj2arr(json_decode(getPageWindow($tempUrl,"UTF-8")));
  		    		if(isset($tempjson) && !is_null($tempjson) && isset($tempjson['playlist']) && !is_null($tempjson['playlist']) && isset($tempjson['playlist'][0]) && !is_null($tempjson['playlist'][0] ) && isset($tempjson['playlist'][0]['urls']) && !is_null($tempjson['playlist'][0]['urls'] ) ){
  		    		 
  		    		  $rc=false;
  		    		  foreach ($tempjson['playlist'][0]['urls'] as $location){
  		    		  	if( $clarity === "high-dvd"  ){ 
  		    		  		if($rc){
  		    		  			$videoAddressUrl3=$videoAddressUrl3.MovieType::VIDEO_SEP_VERSION;
  		    		  		} 
				  	  	 	$videoAddressUrl3=$videoAddressUrl3.MovieType::HIGH_CLEAR.MovieType::VIDEO_NAME_URL_SEP.$location;
				  	  	 	$rc=true;
				  	  	 } 

				  	  	 if( $clarity === "dvd"  ){
  		    		  		if($rc){
  		    		  			$videoAddressUrl2=$videoAddressUrl2.MovieType::VIDEO_SEP_VERSION;
  		    		  		} 
				  	  	 	$videoAddressUrl2=$videoAddressUrl2.MovieType::NORMAL .MovieType::VIDEO_NAME_URL_SEP.$location;
				  	  	 	$rc=true;
				  	  	 } 
				  	  	 
				  	  	 if( $clarity === "tv"  ){ 
  		    		  		if($rc){
  		    		  			$videoAddressUrl1=$videoAddressUrl1.MovieType::VIDEO_SEP_VERSION;
  		    		  		} 
				  	  	 	$videoAddressUrl1=$videoAddressUrl1.MovieType::Liu_Chang.MovieType::VIDEO_NAME_URL_SEP.$location;
				  	  	 	$rc=true;
				  	  	 } 
  		    		  }  		    		  
  		    		}
  		    	}
  		    }
//  		     var_dump($videoAddressUrl3);
//  		    		    var_dump($videoAddressUrl1);
//  		    		     var_dump($videoAddressUrl2);
  		  }catch (Exception $e){}
  		 
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
//  		  var_dump($videoAddressUrl);
  		return $videoAddressUrl;
  	}
  	
  	public function checkHtmlCanPlay($url,$p_coding){
  		$content = getPage($url, $this->p_code);
  		return false;
  	}
  	
    private $p_videocodeApiUrl="http://jobsfe.funshion.com/query/v1/mp4/{cid}.json?bits={byterate}";
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