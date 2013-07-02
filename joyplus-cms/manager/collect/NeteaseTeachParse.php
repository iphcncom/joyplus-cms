<?php
  ob_implicit_flush(true);
ini_set('max_execution_time', '0');
require_once (dirname(__FILE__)."/../admin_conn.php");
require_once (dirname(__FILE__)."/collect_fun.php");
require_once (dirname(__FILE__)."/MovieType.php");
 class NeteaseTeachParse{ 
 	 	
  	 const contentparmStart="var aldJson = ["; //
  	 const contentparaend="];";
  	 const cookieStart="Set-Cookie: "; //
  	 const cookieend=";";
  	 const BASE_EPISODE="http://mobile.open.163.com/movie/2/getPlaysForAndroid.htm";
  	 const BASE_SHOW_EPISODE="http://mobile.open.163.com/movie/{COURSE_ID}/getMoviesForAndroid.htm";
  	
  	 static function parseMovieInfoPage($page,$p_code){
  	 	return NeteaseTeachParse::parseMovieInfoByUrl(NeteaseTeachParse::BASE_EPISODE,$p_code);
  	 }
  	 
  	 
  	 static function parseMovieInfoByUrl($url,$p_code){ 	 	  	 	
  	 	$content = getPage($url, $p_code);  	 	
  	 	return NeteaseTeachParse::parseMovieInfoByContent($content, $p_code,'133');
  	 }
  	
  	 
  	 
     static function parseMovieInfoByContent($content,$p_code,$type){  	 	
  	 	$dataArray=json_decode($content);
  	 	
  	 	if( is_array($dataArray) && count($dataArray)>0){	 		
  	 		$results= array();  	 		
  	 		foreach ($dataArray as $content){
//  	 			var_dump($content);
  	 			$info= new VideoInfo();  
		  	 	$info->title=property_exists($content, 'title')?$content->title:"";
		  	 	$info->type=property_exists($content, 'source')?$content->source:"";
		  	 	$info->director=property_exists($content, 'school')?$content->school:"";
		  	 	$info->type=property_exists($content, 'tags')?$info->type.' '.$content->tags:$info->type;
		  	 	$info->type=property_exists($content, 'type')?$info->type.' '.$content->type:$info->type;
		  	 	$info->big_poster=property_exists($content, 'imgpath')?$content->imgpath:""; //
		  	 	
		  	 	$info->videoUrl=property_exists($content, 'course_url')?$content->course_url:"";
		  	 	
		       
		  	 	
		  	 	$info->id=property_exists($content, 'plid')?$content->plid:""; 
		  	 	
		  	 	$info->sites=NeteaseTeachParse::parseMovie($info->id,$p_code,$info);
		  	 	$info->typeid=$type;
		  	 	$results[] = $info;
		  	 	
  	 		}
  	 		return $results;
  	 	}else {
  	 		return false;
  	 		
  	 	}
  	 	
  	 }
  	 static function parseMovie($id,$p_code,$info){
  	 	$url = replaceStr(NeteaseTeachParse::BASE_SHOW_EPISODE, '{COURSE_ID}', $id);
  	 	$content =getPage($url, $p_code);
  	 	$content= json_decode($content);
  	 	if(is_object($content)){
  	 	 $info->actor=property_exists($content, 'director')?$content->director:"";
  	 	  $info->brief=property_exists($content, 'description')?$content->description:"";
  	 	 $contents=property_exists($content, 'videoList')?$content->videoList:array(); 	 
  	 	 if(is_array($contents) && count($contents) >0){  	 	 	
  	 	 	 $sites = array();
	  	 	 $site = array();
	  	 	 $site['site_url']="126";
	  	 	 $site['site_name']="126";
	  	 	 $site['max_episode']='true';
	  	 	 $episodes = array();
  	 	 	 foreach ($contents as $content){ 
  	 	 		$episodes[]=array(
	   				  'name' =>property_exists($content, 'title')?$content->title:"",
			          'guest' =>property_exists($content, 'subtitle')?$content->subtitle:"",
			          'episode' =>property_exists($content, 'pnumber')?$content->pnumber:"",
			          'url' => property_exists($content, 'weburl')?$content->weburl:"",
			          'img_url' => property_exists($content, 'imgpath')?$content->imgpath:"",
  	 	 		      'androidUrl' => property_exists($content, 'repovideourl')&&!isN($content->repovideourl)?MovieType::HIGH_CLEAR.MovieType::VIDEO_NAME_URL_SEP.$content->repovideourl:"",
  	 	 		      'videoAddressUrl' => property_exists($content, 'repoMP3url')&&!isN($content->repoMP3url)?MovieType::HIGH_CLEAR.MovieType::VIDEO_NAME_URL_SEP.$content->repoMP3url:"",
	   				);
  	 	 		
  	 	 	 }
  	 	 	 $site['episodes']=$episodes;
  	 	 	 $sites[]=$site;
//  	 	 	 var_dump($episodes);
  	 	 	 return $sites;
  	 	 }
  	 	
  	 	}
  	 	return false;
  	 }
  	 
  static function obj2arr($array) {
	if (is_object ( $array )) {
		$array = ( array ) $array;
	}
	if (is_array ( $array )) {
		foreach ( $array as $key => $value ) {
			$array [$key] = NeteaseTeachParse::obj2arr ( $value );
		}
	}
	return $array;
}
     
  	 
	  static function parseArrayToString($array){
	  	if(is_array($array)){
	  		return implode(",", $array);
	  	}
	  	return "";
	  }
	  
	  
	 
	  
	  
   static function slang($num){
  	 	if($num ==='1'){
  	 		return '一';
  	 	}
  	    if($num ==='2'){
  	 		return '二';
  	 	}
  	    if($num ==='3'){
  	 		return '三';
  	 	}
  	    if($num ==='4'){
  	 		return '四';
  	 	}
  	    if($num ==='5'){
  	 		return '五';
  	 	}
  	    if($num ==='6'){
  	 		return '六';
  	 	}
  	    if($num ==='7'){
  	 		return '七';
  	 	}
  	    if($num ==='8'){
  	 		return '八';
  	 	}
  	    if($num ==='9'){
  	 		return '九';
  	 	} 
  	 	return ""; 	   
  	 }
  	 
  	 static function lang($num){
  	 	if(strlen($num) ===1){
  	 		return NeteaseTeachParse::slang($num);
  	 	}
  	 	if(strlen($num) ===2){
  	 		return NeteaseTeachParse::slang(substr($num, 0,1)).'十'.NeteaseTeachParse::slang(substr($num, 1,1));
  	 	}
  	 }
	  
  }
  
  

 
  
  
  
?>