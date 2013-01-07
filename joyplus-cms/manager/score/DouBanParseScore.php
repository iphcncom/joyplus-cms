<?php
require_once('HttpClient.class.php');
require_once (dirname(__FILE__)."/../admin_conn.php");
require_once (dirname(__FILE__)."/../collect/collect_fun.php");
class DouBanParseScore{
	const BASE_URL="https://api.douban.com/v2/movie/search?count=10000&q=";
	const COMMENTS_URL="http://movie.douban.com/subject/{ID}/comments?sort=time";//10569156
	
	const PIC_URL="http://movie.douban.com/subject/{ID}/photos?type=R&start=0&sortby=size";
	const PIC_URL_NORMAL="http://movie.douban.com/subject/{ID}/photos?type=R&sortby=size&size=a&subtype=o";
	const PIC_JIZHAO="http://movie.douban.com/subject/{ID}/photos?type=S&start=0&sortby=size&size=a&subtype=a";
	
	
	public function readContent($url){
		$httpClient = new HttpClient("api.douban.com",80);
		return $httpClient->quickGet($url);
	}
	
	public function searchMovie($name){
//		echo $name;

		$url = DouBanParseScore::BASE_URL.urlencode($name);
//echo $url;
		$content = $this->readContent($url);
        writetofile("updateVodPicContent.txt", 'check item for vod type{=}'.$content );
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
	
	public function getScoreForWeb($name,$year,$area){
		$url = DouBanParseScore::WEB_BASE_URL. urlencode($name);
 	$content = getPage($url, "utf-8");
 	$content=getBody($content, DouBanParseScore::WEB_CONTENT_START, DouBanParseScore::WEB_CONTENT_END);
 	$scores= getArray($content, DouBanParseScore::WEB_SCORE_START, DouBanParseScore::WEB_SCORE_END);
 	$titles= getArray($content, DouBanParseScore::WEB_TITLE_START, DouBanParseScore::WEB_TITLE_END);
 	$dates= getArray($content, DouBanParseScore::WEB_DATE_START, DouBanParseScore::WEB_DATE_END);
 	$titles=filterScript($titles, 8192);
 	$scoresArray=explode("{Array}", $scores);
 	$titlesArray=explode("{Array}", $titles);
 	$datesArray=explode("{Array}", $dates);
// 	var_dump($idsArray);
// 	var_dump($titlesArray);
// 	var_dump($datesArray);
 	$count = count($scoresArray);
 	for ($i=0;$i<$count;$i++){
 		$movieName="";
 		$movieYear="";
 		if($i<count($titlesArray)){
 			$movieName=$titlesArray[$i];
 		}
 	  if($i<count($datesArray)){
 			$movieYear=$datesArray[$i];
 	  }
 	
 	  $movieNameArray = explode("/", $movieName);
 	  $flag=false;
 	  
 	  for($j=0;$j<count($movieNameArray);$j++){
 	  	if(!isN($movieNameArray[$j]) && (strcasecmp(trim($movieNameArray[$j]) ,$name) == 0 || strpos($name,trim($movieNameArray[$j])) !==false) ){
	        $flag=true;
			 break;
	      }
 	  }
 	  
 	  if(!$flag){
       continue;
      }
     //var_dump($name.'='.$movieName.'='.(strcasecmp($movieName ,$name)) );
                 
      if(strpos($year, $movieYear) ===false && strpos($movieYear, $year) ===false ){ 
        continue;
      }
      //var_dump($year.'='.$movieYear.'='.(!strstr($year,$movieYear)) ); 
      return $scoresArray[$i];
 	}
 	  return "";
	}
	public function getScore($name,$year,$area){
	    $score =  $this->getScoreForWeb($name, $year, $area);
		if(isN($score)){
			$score = $this->getScoreApi($name, $year, $area);
		}
	    if(isN($score)){
			return 6;
		}
		return $score;
	}
	public function getScoreApi($name,$year,$area){
		$score=0;
	  try{
		$moviesArray = $this->searchMovie($name);
		
		if(isset($moviesArray) && is_array($moviesArray)){
			foreach($moviesArray as $movie){
			   if(isset($movie['attrs']) && is_array($movie['attrs'])){
                  $movieName = $movie['attrs']['title'][0];
                  $movieYear = $movie['attrs']['year'][0];
                  $movieArea = $movie['attrs']['country'][0];
                  
                  if(strcasecmp($movieName ,$name) != 0){
                  	continue;
                  }
                  
				if( isN($year)  || isN($movieYear) || (strpos($year, $movieYear) ===false && strpos($movieYear, $year) ===false) ){ 
                  	continue;
                  }
                  
                  if(isset($movie['rating']) && is_array($movie['attrs']) && isset($movie['rating']['average']) && !is_null($movie['rating']['average'])){
                    var_dump($movie);
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
	  return "";
	}
	
	function getDoubanComments($name,$year,$area){
	    $id = $this->getDoubanIDForWeb($name, $year, $area);
		if(isN($id)){
			$id = $this->getDoubanID($name, $year, $area);
		}
		
	    if(isN($id)){
			return false;
		}
	
		return $this->getCommentById($id);
		
	}
	
	function getDouBanPics($name,$year,$area,$rate){
		$id = $this->getDoubanIDForWeb($name, $year, $area);
		
		if(isN($id)){
			$id = $this->getDoubanID($name, $year, $area);
		}
	    if(isN($id)){
			return false;
		}
		writetofile("updateVodPic.txt", 'check item for vod id{=}'.$id );
		return $this->getPicById($id,$rate);
	}
	
    function getComments($name,$year,$area){
		$comments= $this->getDoubanComments($name, $year, $area);
		return $comments;
	}
	const WEB_BASE_URL="http://movie.douban.com/subject_search?cat=1002&search_text=";
	const WEB_ID_START='<a class="nbg" href="http://movie.douban.com/subject/';
	const WEB_ID_END='/"';
	const WEB_TITLE_START='<div class="pl2">';
	const WEB_TITLE_END='</div>';
	const WEB_DATE_START='<p class="pl">';
	const WEB_DATE_END='/';
	
	const WEB_CONTENT_START='<div class="article">';
	const WEB_CONTENT_END='<div class="aside">';
	
	const WEB_SCORE_START='<span class="rating_nums">';
	const WEB_SCORE_END='</span>';
	
 public function getDoubanIDForWeb($name,$year,$area){
 	$yearArray = explode("-", $year);
 	if(count($yearArray)>0){
 		$year=$yearArray[0];
 	}
//    $yearArray = explode("/", $year);
// 	if(count($yearArray)>0){
// 		$year=$yearArray[0];
// 	}
 	$url = DouBanParseScore::WEB_BASE_URL. urlencode($name);
 	writetofile("updateVodPic.txt", 'getDoubanIDForWeb for vod url{=}'.$url.'{year}'.$year );
 	$content = getPage($url, "utf-8");
 	writetofile("updateVodPicContent.txt", 'check item for vod content{=}'.$content );
 	$content=getBody($content, DouBanParseScore::WEB_CONTENT_START, DouBanParseScore::WEB_CONTENT_END);
// 	writetofile("updateVodPicContent.txt", 'check item for vod id{=}'.$content );
 	$ids= getArray($content, DouBanParseScore::WEB_ID_START, DouBanParseScore::WEB_ID_END);
 	$titles= getArray($content, DouBanParseScore::WEB_TITLE_START, DouBanParseScore::WEB_TITLE_END);
 	$titles=filterScript($titles, 8192);
 	$dates= getArray($content, DouBanParseScore::WEB_DATE_START, DouBanParseScore::WEB_DATE_END);
 	$idsArray=explode("{Array}", $ids);
 	$titlesArray=explode("{Array}", $titles);
 	$datesArray=explode("{Array}", $dates);
// 	var_dump($idsArray);
 	//var_dump($titlesArray);
 //	var_dump($datesArray);
 	$count = count($idsArray);
 	for ($i=0;$i<$count;$i++){
 		$movieName="";
 		$movieYear="";
 		if($i<count($titlesArray)){
 			$movieName=$titlesArray[$i];
 		}
 	  if($i<count($datesArray)){
 			$movieYear=$datesArray[$i];
 	  }
 	  $movieNameArray = explode("/", $movieName);
 	  $flag=false;
 	  
 	  for($j=0;$j<count($movieNameArray);$j++){
 	  	if(!isN($movieNameArray[$j]) && (strcasecmp(trim($movieNameArray[$j]) ,$name) == 0 || strpos($name,trim($movieNameArray[$j])) !==false) ){
	        $flag=true;
			 break;
	      }
 	  }
 	  
 	  if(!$flag){
       continue;
      }
    // var_dump($name.'='.$movieName.'='.(strcasecmp($movieName ,$name)) );
                 
      if( isN($year)  || isN($movieYear) || (strpos($year, $movieYear) ===false && strpos($movieYear, $year) ===false) ){ 
        continue;
      }
     // var_dump($year.'='.$movieYear.'='.(!strstr($year,$movieYear)) ); 
      return $idsArray[$i];
 	}
 	return "";
 	
 }
public function getDoubanID($name,$year,$area){
		$score=0;
	  try{
		$moviesArray = $this->searchMovie($name);
//		var_dump($moviesArray);
		if(isset($moviesArray) && is_array($moviesArray)){
			foreach($moviesArray as $movie){
			   if(isset($movie['attrs']) && is_array($movie['attrs'])){
                  $movieName = $movie['title'].'/'.$movie['alt_title'];
                  $movieYear = $movie['attrs']['year'][0];
                  $movieArea = $movie['attrs']['country'][0];
                  $movieNameArray = explode("/", $movieName);
//                  var_dump($movieYear);
                  
			 	  $flag=false;
			 	  
			 	  for($j=0;$j<count($movieNameArray);$j++){
//			 	  	  var_dump($movieNameArray[$j] .'---'.$name);
				 	  if(!isN($movieNameArray[$j]) && (strcasecmp(trim($movieNameArray[$j]) ,$name) == 0 || strpos($name,trim($movieNameArray[$j])) !==false )){
				        $flag=true;
				        break;
				      }
			 	  }
			 	  
			 	  if(!$flag){
			       continue;
			      }
//                  var_dump($movieYear);
				if( isN($year)  || isN($movieYear) || (strpos($year, $movieYear) ===false && strpos($movieYear, $year) ===false) ){ 
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
    
 const PIC_CONTENT_START='<ul class="poster-col4 clearfix">';
 const PIC_CONTENT_END='</ul>';
 const PIC_URL_START='<img src="';
 const PIC_URL_END='"';
 const PIC_RATES_START='<div class="prop">';
 const PIC_RATES_END='</div>';
 
    public function  getPicById($id,$rate){
	  $url = str_replace("{ID}", $id, DouBanParseScore::PIC_URL_NORMAL);
	  $pic=$this->getPicByUrl($url, $rate);
	  writetofile("updateVodPic.txt", 'check item for PIC_URL_NORMAL url{=}'.$url );
      if($pic ===false ){
      	$url = str_replace("{ID}", $id, DouBanParseScore::PIC_URL);
	  writetofile("updateVodPic.txt", 'check item for PIC_URL url{=}'.$url );
      	$pic=$this->getPicByUrl($url, $rate);
      }
      if($pic ===false ){
       	$url = str_replace("{ID}", $id, DouBanParseScore::PIC_JIZHAO);
	    writetofile("updateVodPic.txt", 'check item for PIC_JIZHAO url{=}'.$url );
      	$pic=$this->getPicByUrl($url, $rate);
      }
      
     return $pic;
    }
    
    private function getPicByUrl($url,$rate){
      $content = $this->readContent($url);   
	  if(isset($content) && !is_null($content)){
        $content=getBody($content,DouBanParseScore::PIC_CONTENT_START,DouBanParseScore::PIC_CONTENT_END);
		$picUrls = getArray($content, DouBanParseScore::PIC_URL_START, DouBanParseScore::PIC_URL_END);
		$picRates = getArray($content, DouBanParseScore::PIC_RATES_START, DouBanParseScore::PIC_RATES_END);
		$picUrlsArray = explode("{Array}", $picUrls);			 	
		$picRatesArray= explode("{Array}", $picRates);
	    $total= count($picUrlsArray);
	    $tempA= array();
	 	for ($i=0;$i<$total;$i++) {
	 	  try{
	 		$url=$picUrlsArray[$i];
	 		$rateT=$picRatesArray[$i];
	 		$rateT=replaceLine($rateT);
	 		$rateA=explode("x", $rateT);
//	 		 $log=$url .'==='.$rateT ;
	 		if(count($rateA)==2){
	 			$rateR=intval($rateA[1])/intval($rateA[0]);
	 		}
//	 		 $log=$log .'==='.$rateR ;
	 		 $rateR=abs($rateR/$rate-1);
	 		 
//	 		  $log=$log .'==='.$rateR ;
//	 		var_dump($log );
            //比率不能少于80%
            if($rateR<0.2){
	 		  $tempA[$rateR.'']=$url;
            }
	 	  }catch(Exception $e){}
	 	}
	 	if(count($tempA)>0){
			$tempAKeys=array_keys($tempA);
			sort($tempAKeys,SORT_NUMERIC);
	        $pic=$tempA[$tempAKeys[0]];
	        var_dump($tempA);
//	        var_dump($tempAKeys);
		    if(!isN($pic)){
				$pic = replaceStr($pic, 'thumb', 'photo');
			}
            
			if(count($tempAKeys)>1){
				$pic1=$tempA[$tempAKeys[1]];
			    if(!isN($pic1)){
					$pic1 = replaceStr($pic1, 'thumb', 'photo');
					$pic=$pic.'{Array}'.$pic1;
				}	
			}
			
	 	   if(count($tempAKeys)>2){
				$pic1=$tempA[$tempAKeys[2]];
			    if(!isN($pic1)){
					$pic1 = replaceStr($pic1, 'thumb', 'photo');
					$pic=$pic.'{Array}'.$pic1;
				}	
			}
			
	 	   if(count($tempAKeys)>3){
				$pic1=$tempA[$tempAKeys[3]];
			    if(!isN($pic1)){
					$pic1 = replaceStr($pic1, 'thumb', 'photo');
					$pic=$pic.'{Array}'.$pic1;
				}	
			}
			
			if(!isN($pic)){
				return $pic;
			}
	 	}
	}
	return false;
    }

 const DATE_START="<span class=\"fleft ml8\">";
 const DATE_END="</span>";
 const CONTENT_START="<p class=\"w490\">";
 const CONTENT_END="</p>";
 const USERS_START="<span class=\"fleft\">";
 const USERS_END="</span>";
 const COMMENT_CONTENT_START='<div class="mod-bd">';
 
 public function  getCommentsByUrl($url){
	
    $content =getPage($url, "utf-8");
   
	if(isset($content) && !is_null($content)){
//		 echo $content;
        $content=getBodys($content,DouBanParseScore::COMMENT_CONTENT_START);
        
		$dates = getArray($content, DouBanParseScore::DATE_START, DouBanParseScore::DATE_END);
		$coments= getArray($content, DouBanParseScore::CONTENT_START, DouBanParseScore::CONTENT_END);
		$USERS = getArray($content, DouBanParseScore::USERS_START, DouBanParseScore::USERS_END);
		$USERS=filterScriptStar($USERS,'8191');
		$USERS=replaceStr($USERS, ",", "");
//		var_dump($USERS);var_dump($dates);
		if(isset($coments) && !is_null($coments)){
		    $coments=filterScriptStar($coments,'8191');
			// var_dump($coments);
			return array(
				'dates'=>$dates,
			    'comments'=>$coments,
			    'authors'=>$USERS,
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
//$d = nes= $d->getCommentById('10569156');
//var_dump($coments);
//$d = new DouBanParseScore();
//var_dump($d->getDouBanPics("深海异形", '2005', '',7/5));

//$s=rand(0, 10)+rand(0, 10)/10;
//echo $s;
//$httpClient = new HttpClient("api.douban.com",80);
//$content = $httpClient->quickGet('http://api2.v.pptv.com/api/page/episodes.js?page=1&channel_id=10034178');
//
//var_dump( DouBanParseScore::format($content));



?>