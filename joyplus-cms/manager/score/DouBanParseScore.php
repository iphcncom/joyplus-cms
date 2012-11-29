<?php
require_once('HttpClient.class.php');
require_once (dirname(__FILE__)."/../admin_conn.php");
class DouBanParseScore{
	const BASE_URL="https://api.douban.com/v2/movie/search?count=10000&q=";
	const COMMENTS_URL="http://movie.douban.com/subject/{ID}/comments?sort=time";//10569156
	
	
	public function readContent($url){
		$httpClient = new HttpClient("api.douban.com",80);
		return $httpClient->quickGet($url);
	}
	
	public function searchMovie($name){
//		echo $name;
		$url = DouBanParseScore::BASE_URL.urlencode($name);
//echo $url;
		$content = $this->readContent($url);
// echo $content;
		if(!isset($content) || is_null($content))
		{
			return $content;
		}else {
//			echo $content;
			$movies= json_decode($content);
			
			if(isset($movies) && !is_null($movies) && isset($movies->movies) && is_array($movies->movies) && count($movies->movies)>0){
				return $this->obj2arr($movies->movies);
			}
		}
	}
	
	function obj2arr($array) {
		if (is_object ( $array )) {
			$array = ( array ) $array;
		}
		if (is_array ( $array )) {
			foreach ( $array as $key => $value ) {
				$array [$key] = $this->obj2arr ( $value );
			}
		}
		return $array;
	}
	
	public function getScore($name,$year,$area){
		$score=0;
	  try{
		$moviesArray = $this->searchMovie($name);
		
		if(isset($moviesArray) && is_array($moviesArray)){
			foreach($moviesArray as $movie){
			   if(isset($movie['attrs']) && is_array($movie['attrs'])){
                  $movieName = $movie['attrs']['title'][0];
                  $movieYear = $movie['attrs']['year'][0];
                  $movieArea = $movie['attrs']['country'][0];
//                  var_dump($movie['attrs']);
                  if(strcasecmp($movieName ,$name) === 1){
                  	continue;
                  }
                  
				  if(!strstr($year,$movieYear)){ 
                  	continue;
                  }
                  
                  if(isset($movie['rating']) && is_array($movie['attrs']) && isset($movie['rating']['average']) && !is_null($movie['rating']['average'])){
                     $score=  $movie['rating']['average'];
                     return $score;
                  }else {
                  	$score= 0;
                  }
                  
			   }
			}
			if($score ==0 && count($moviesArray)>0){
				$score=  $moviesArray[0]['rating']['average'];
				return $score;
			}
			
		}
	  }catch (Exception $e){			
	  }
//	  return 0;
	  return 6;
	}
	
	function getDoubanComments($name,$year,$area){
		$id = $this->getDoubanID($name, $year, $area);
		if(isN($id)){
			return false;
		}
		
		return $this->getCommentById($id);
		
	}
	
 function getComments($name,$year,$area){
		$comments= $this->getDoubanComments($name, $year, $area);
		
	}
	
public function getDoubanID($name,$year,$area){
		$score=0;
	  try{
		$moviesArray = $this->searchMovie($name);
//		var_dump($moviesArray);
		if(isset($moviesArray) && is_array($moviesArray)){
			foreach($moviesArray as $movie){
			   if(isset($movie['attrs']) && is_array($movie['attrs'])){
                  $movieName = $movie['attrs']['title'][0];
                  $movieYear = $movie['attrs']['year'][0];
                  $movieArea = $movie['attrs']['country'][0];
//                  var_dump($movie['attrs']);
                  if(strcasecmp($movieName ,$name) === 1){
                  	continue;
                  }
                  
				  if(!strstr($year,$movieYear)){ 
                  	continue;
                  }
                  
                  if(isset($movie['id']) && !is_null($movie['id'])){
                     return str_replace("http://api.douban.com/movie/", "", $movie['id']);
                  }else {
                  	return "";
                  }
                  
			   }
			}
			return "";
		}
	  }catch (Exception $e){			
	  }
//	  return 0;
	  return "";
	}   
	public function  getCommentById($id){
	$url = str_replace("{ID}", $id, DouBanParseScore::COMMENTS_URL);
	return $this->getCommentsByUrl($url);
}

 const DATE_START="<span class=\"fleft ml8\">";
 const DATE_END="</span>";
 const CONTENT_START="<p class=\"w490\" style=\"margin-bottom:0;\">";
 const CONTENT_END="</p>";
 
 public function  getCommentsByUrl($url){
//	echo $url;
    $content = $this->readContent($url);
   
	if(isset($content) && !is_null($content)){
//		 echo $content;
		$dates = getArray($content, DouBanParseScore::DATE_START, DouBanParseScore::DATE_END);
		$coments= getArray($content, DouBanParseScore::CONTENT_START, DouBanParseScore::CONTENT_END);
		if(isset($coments) && !is_null($coments)){
			return array(
				'dates'=>$dates,
			    'comments'=>$coments,
			);
		}
	}
	return false;
}
}






// $videoUrls="\$ss\$s";
// echo strpos( $videoUrls,"$");
//        if (strpos( $videoUrls,"$") ===0){
//        	$videoUrls=substr($videoUrls, 1);
//        }
//        echo time();
//$d = new DouBanParseScore();
//$coments= $d->getCommentById('10569156');
//var_dump($coments);
//var_dump($d->getScore("金陵十三钗", '2011', ''));

//$s=rand(0, 10)+rand(0, 10)/10;
//echo $s;
//$httpClient = new HttpClient("api.douban.com",80);
//$content = $httpClient->quickGet('http://api2.v.pptv.com/api/page/episodes.js?page=1&channel_id=10034178');
//
//var_dump( DouBanParseScore::format($content));



?>