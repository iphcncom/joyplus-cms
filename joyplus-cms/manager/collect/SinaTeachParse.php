<?php
  ob_implicit_flush(true);
ini_set('max_execution_time', '0');
require_once (dirname(__FILE__)."/../admin_conn.php");
require_once (dirname(__FILE__)."/collect_fun.php");
require_once (dirname(__FILE__)."/MovieType.php");
require_once (dirname(__FILE__)."/api_collect_vod_cj.php");

 class SinaTeachParse{ 
 	 	
  	 const contentparmStart="var aldJson = ["; //
  	 const contentparaend="];";
  	 const cookieStart="Set-Cookie: "; //
  	 const cookieend=";";
  	 const BASE_EPISODE="http://kaoshi.edu.sina.com.cn/interface/video_edu.php?action=course&wm=9005_0002&page={PAGE}&pagenum=20&from=ME525+&device=356514044215812";
  	 const BASE_SHOW_EPISODE="http://kaoshi.edu.sina.com.cn/interface/video_edu.php?action=courselesson&wm=9005_0002&courseid={COURSE_ID}&from=ME525+&device=356514044215812";
  	
  	 static function parseMovieInfoPage($page,$p_code){
  	 	$url=replaceStr(SinaTeachParse::BASE_EPISODE, "{PAGE}", $page);
  	 	return SinaTeachParse::parseMovieInfoByUrl($url,$p_code);
  	 }
  	 
  	 
  	 static function parseMovieInfoByUrl($url,$p_code){  	 	  	 	
  	 	$content = getPage($url, $p_code);  	 	
  	 	return SinaTeachParse::parseMovieInfoByContent($content, $p_code,'133');
  	 }
  	
  	 
  	 
     static function parseMovieInfoByContent($content,$p_code,$type){  	 	
  	 	$content=json_decode($content);
  	 	
  	 	if(is_object($content) && property_exists($content, 'result') && property_exists($content->result, 'data') && is_array($content->result->data) && count($content->result->data)>0){
  	 		$dataArray=$content->result->data;
  	 		$results= array();  	 		
  	 		foreach ($dataArray as $content){
//  	 			var_dump($content);
  	 			$info= new VideoInfo();  	 		
		  	 	
		  	 	$info->title=property_exists($content, 'name')?$content->name:"";
		  	 	$info->type=property_exists($content, 'branch_name')?$content->branch_name:"";
		  	 	$info->director=property_exists($content, 'college_name')?$content->college_name:"";
		  	 	
		  	 	$info->big_poster=property_exists($content, 'thumb')?$content->thumb:""; //
		  	 	$info->actor=property_exists($content, 'teacher')?$content->teacher:"";
		  	 	$info->videoUrl=property_exists($content, 'course_url')?$content->course_url:"";
		  	 	
		        $info->brief=property_exists($content, 'course_des')?$content->course_des:"";
		  	 	
		  	 	$info->id=property_exists($content, 'id')?$content->id:""; 
		  	 	
		  	 	$info->sites=SinaTeachParse::parseMovie($info->id,$p_code);
		  	 	$info->typeid=$type;
		  	 	$results[] = $info;
  	 		}
  	 		return $results;
  	 	}else {
  	 		return false;
  	 		
  	 	}
  	 	
  	 }
  	 static function parseMovie($id,$p_code){
  	 	$url = replaceStr(SinaTeachParse::BASE_SHOW_EPISODE, '{COURSE_ID}', $id);
  	 	$content =getPage($url, $p_code);
  	 	$content= json_decode($content);
  	 	if(is_object($content) && property_exists($content, 'result') && property_exists($content->result, 'data') && property_exists($content->result->data, 'lessoninfo')){
  	 	 $contents=$content->result->data->lessoninfo;  	 	 
  	 	 if(is_array($contents) && count($contents) >0){
  	 	 	
  	 	 	 $sites = array();
	  	 	 $site = array();
	  	 	 $site['site_url']="sina";
	  	 	 $site['site_name']="sinahd";
	  	 	 $site['max_episode']='true';
	  	 	 $episodes = array();
  	 	 	 foreach ($contents as $content){ 
  	 	 		$episodes[]=array(
	   				  'name' =>property_exists($content, 'name')?$content->name:"1",
			          'guest' =>property_exists($content, 'short_name')?$content->short_name:"1",
			          'episode' =>property_exists($content, 'jieci')?$content->jieci:"1",
			          'url' => property_exists($content, 'burl')?$content->burl:"",
			          'img_url' => property_exists($content, 'thumb')?$content->thumb:"",
  	 	 		      'time' => property_exists($content, 'length')?$content->length:"",
  	 	 		       'stream_url' => property_exists($content, 'stream_url')&&!isN($content->stream_url)?MovieType::HIGH_CLEAR.MovieType::VIDEO_NAME_URL_SEP.$content->stream_url:"",
  	 	 		      'androidUrl' => property_exists($content, 'android_url')&&!isN($content->android_url)?MovieType::HIGH_CLEAR.MovieType::VIDEO_NAME_URL_SEP.$content->android_url:"",
  	 	 		      'videoAddressUrl' => property_exists($content, 'ipad_url')&&!isN($content->ipad_url)?MovieType::HIGH_CLEAR.MovieType::VIDEO_NAME_URL_SEP.$content->ipad_url:"",
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
			$array [$key] = SinaTeachParse::obj2arr ( $value );
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
  	 		return SinaTeachParse::slang($num);
  	 	}
  	 	if(strlen($num) ===2){
  	 		return SinaTeachParse::slang(substr($num, 0,1)).'十'.SinaTeachParse::slang(substr($num, 1,1));
  	 	}
  	 }
	  
  }
  
  

  class VideoInfo{
  	public $max_episode;
  	public $curr_episode;
  	public $title;
  	public $language;
  	public $director;
  	public $area;
  	public $type;
  	public $actor;
  	public $brief;
  	public $pubdate;
  	public $id; //al_date
  	public $sites; // site_url"/site_name
  	public $playfrom;
  	public $alias;
  	public $update_freq;
  	public $big_poster;
  	public $duration;
  	public $season_num;
  	public $videoUrl;
  	public $typeid;
  	public $p_id=0;
  	
  }
  
  
  
?>