<?php
require_once('HttpClient.class.php');
class DouBanParseScore{
	const BASE_URL="https://api.douban.com/v2/movie/search?count=10000&q=";
	
	
	public function readContent($url){
		$httpClient = new HttpClient("api.douban.com",80);
		return $httpClient->quickGet($url);
	}
	
	public function searchMovie($name){
		$url = DouBanParseScore::BASE_URL.urlencode($name);
		//echo $url;
		$content = $this->readContent($url);

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
                  
                  if(isset($movie['rating']) && is_array($movie['attrs']) && isset($movie['rating']['average']) && !is_null($movie['rating']['average'])){
                     $score=  $movie['rating']['average'];
                  }else {
                  	$score= 0;
                  }
                  
			   }
			}
			if($score ==0 && count($moviesArray)>0){
				$score=  $moviesArray[0]['rating']['average'];
			}
			return $score;
		}
	  }catch (Exception $e){			
	  }
//	  return 0;
	  return 6;
	}
  
}

// $videoUrls="\$ss\$s";
// echo strpos( $videoUrls,"$");
//        if (strpos( $videoUrls,"$") ===0){
//        	$videoUrls=substr($videoUrls, 1);
//        }
//        echo time();
//$d = new DouBanParseScore();
//echo $d->getScore("盗梦空间", '201-09-11', '');

//$s=rand(0, 10)+rand(0, 10)/10;
//echo $s;
//$httpClient = new HttpClient("api.douban.com",80);
//$content = $httpClient->quickGet('http://api2.v.pptv.com/api/page/episodes.js?page=1&channel_id=10034178');
//
//var_dump( DouBanParseScore::format($content));



?>