<?php
require_once('HttpClient.class.php');
require_once (dirname(__FILE__)."/../admin_conn.php");
require_once (dirname(__FILE__)."/../collect/collect_fun.php");
class AutoDouBanParseScore{
	const BASE_URL="https://api.douban.com/v2/movie/search?count=10000&q=";
	const COMMENTS_URL="http://movie.douban.com/subject/{ID}/comments?sort=time";//10569156
	
	const REVIEW_URL="http://movie.douban.com/subject/{ID}/reviews?score={STAR}";//10569156
	
	const PIC_URL="http://movie.douban.com/subject/{ID}/photos?type=R&start=0&sortby=size";
	const PIC_URL_NORMAL="http://movie.douban.com/subject/{ID}/photos?type=R&sortby=size&size=a&subtype=o";
	const PIC_JIZHAO="http://movie.douban.com/subject/{ID}/photos?type=S&start=0&sortby=size&size=a&subtype=a";
	const PIC_URL_THUMB="http://movie.douban.com/subject/{ID}/";
	
	public function readContent($url){
		$httpClient = new HttpClient("api.douban.com",80);
		return $httpClient->quickGet($url);
	}
	
	public function searchMovie($name){

		$url = AutoDouBanParseScore::BASE_URL.urlencode($name);

		$content = $this->readContent($url);
        writetofile("updateVodPicContent.txt", 'check item for vod type{=}'.$content );
		if(isset($content) && !is_null($content) && strpos($content, "You API access rate limit has been exceeded") !==false){
	 		return "-1";
	 	}
		if(!isset($content) || is_null($content))
		{
			return $content;
		}else {

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
		$url = AutoDouBanParseScore::WEB_BASE_URL. urlencode($name);
 	$content = getPage($url, "utf-8");
 	$content=getBody($content, AutoDouBanParseScore::WEB_CONTENT_START, AutoDouBanParseScore::WEB_CONTENT_END);
 	$scores= getArray($content, AutoDouBanParseScore::WEB_SCORE_START, AutoDouBanParseScore::WEB_SCORE_END);
 	$titles= getArray($content, AutoDouBanParseScore::WEB_TITLE_START, AutoDouBanParseScore::WEB_TITLE_END);
 	$dates= getArray($content, AutoDouBanParseScore::WEB_DATE_START, AutoDouBanParseScore::WEB_DATE_END);
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
	
     function getDoubanReviews($name,$year,$area){
     	
	    $id = $this->getDoubanIDForWeb($name, $year, $area);
	    writetofile("updateVodReviews.txt", 'getDoubanIDForWeb check item for vod id{=}'.$id );
	    $noRightflag=false;	
	    
	    if($id ==='-1'){
	    	$noRightflag=true;
	    	$id="";
	    }	
	    
		if(isN($id)){
			$id = $this->getDoubanID($name, $year, $area);
		}
		
		writetofile("updateVodReviews.txt", 'getDoubanID check item for vod id{=}'.$id );
		
		if($id ==='-1' || $noRightflag ){
			return false;
		}
		
	    if(isN($id)){
			 return array('comments'=>"",
					             'id'=>'-1',
					);
		}
		writetofile("updateVodReviews.txt", 'check item for vod id{=}'.$id );
	
		return $this->getReviewsById($id);
		
	}
	
	function getDouBanPics($name,$year,$area,$rate){
	$id = $this->getDoubanIDForWeb($name, $year, $area);
	    writetofile("updateVodPic.txt", 'getDoubanIDForWeb check item for vod id{=}'.$id );
	    $noRightflag=false;	
	    
	    if($id ==='-1'){
	    	$noRightflag=true;
	    	$id="";
	    }	
	    
		if(isN($id)){
			$id = $this->getDoubanID($name, $year, $area);
		}
		
		writetofile("updateVodPic.txt", 'getDoubanID check item for vod id{=}'.$id );
		
		if($id ==='-1' || $noRightflag ){
			return false;
		}
		
	    if(isN($id)){
			 return array('pic'=>false,
					             'id'=>'-1',
					);
		}
		writetofile("updateVodPic.txt", 'check item for vod id{=}'.$id );
		return $this->getPicById($id,$rate);
	}
	
	
	function getDoubanThumb($name,$year,$area){
		
	    $id = $this->getDoubanIDForWeb($name, $year, $area);
	    writetofile("updateVodThumb.txt", 'getDoubanIDForWeb check item for vod id{=}'.$id );
	    $noRightflag=false;	
	    
	    if($id ==='-1'){
	    	$noRightflag=true;
	    	$id="";
	    }	
	    
		if(isN($id)){
			$id = $this->getDoubanID($name, $year, $area);
		}
		
		writetofile("updateVodThumb.txt", 'getDoubanID check item for vod id{=}'.$id );
		
		if($id ==='-1' || $noRightflag ){
			return false;
		}
		
	    if(isN($id)){
			 return array('pic'=>false,
					             'id'=>'-1',
					);
		}
		
		writetofile("updateVodThumb.txt", 'check item for vod id{=}'.$id );
		return $this->getThumb($id);
	
	}
	
	function getThumb($id){
		 $url = str_replace("{ID}", $id, AutoDouBanParseScore::PIC_URL_THUMB);	     
	     writetofile("updateVodThumb.txt", 'check item for PIC_URL_NORMAL url{=}'.$url );
 	     $content = getPage($url, "utf-8");
 	     $content=getBody($content, AutoDouBanParseScore::WEB_THUMB_START, AutoDouBanParseScore::WEB_THUMB_END);
	     $content=getBody($content, AutoDouBanParseScore::WEB_PIC_THUMB_START, AutoDouBanParseScore::WEB_PIC_THUMB_END);
	     return array('pic'=>$content,
				             'id'=>$id,
				);
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
	
	const WEB_THUMB_START='<div id="mainpic">';
	const WEB_THUMB_END='</div>';
	
	const WEB_PIC_THUMB_START='<img src="';
	const WEB_PIC_THUMB_END='"';
	
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
 	$url = AutoDouBanParseScore::WEB_BASE_URL. urlencode($name);
 	writetofile("updateVodPic.txt", 'getDoubanIDForWeb for vod url{=}'.$url.'{year}'.$year );
 	$content = getPage($url, "utf-8");
 	writetofile("updateVodPicContent.txt", 'check item for vod content{=}'.$content );
 	if(isset($content) && !is_null($content) && strpos($content, "403 Forbidden") !==false){
 		return "-1";
 	}
 	$content=getBody($content, AutoDouBanParseScore::WEB_CONTENT_START, AutoDouBanParseScore::WEB_CONTENT_END);
// 	writetofile("updateVodPicContent.txt", 'check item for vod id{=}'.$content );
 	$ids= getArray($content, AutoDouBanParseScore::WEB_ID_START, AutoDouBanParseScore::WEB_ID_END);
 	$titles= getArray($content, AutoDouBanParseScore::WEB_TITLE_START, AutoDouBanParseScore::WEB_TITLE_END);
 	$titles=filterScript($titles, 8192);
 	$dates= getArray($content, AutoDouBanParseScore::WEB_DATE_START, AutoDouBanParseScore::WEB_DATE_END);
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
		if(isset($moviesArray) && is_string($moviesArray) && $moviesArray ==='-1'){
			return $moviesArray;
		}
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
	$url = str_replace("{ID}", $id, AutoDouBanParseScore::COMMENTS_URL);
	return $this->getCommentsByUrl($url);
    }
    
    //http://movie.douban.com/j/review/5684162/fullinfo?show_works=False
    
public function  getReviewsById($id){
    	$reviewArry=array();
		$titleArry=array();
		$idArry=array();
		
    	$turl = str_replace("{ID}", $id, AutoDouBanParseScore::REVIEW_URL);
    	$url = str_replace("{STAR}", '5', $turl);
    
    	$flag= $this->getReviewsByStar($url,$reviewArry,$titleArry,$idArry);
    	if($flag !==false ){
    		$reviewArry = array_merge($reviewArry,$flag['comments']);
    		$titleArry = array_merge($titleArry,$flag['title']);
    		$idArry = array_merge($idArry,$flag['reviewid']);
    	}
    	
    	
    	if(count($reviewArry)<3){
    		$url = str_replace("{STAR}", '4', $turl);
    		$flag= $this->getReviewsByStar($url,$reviewArry,$titleArry,$idArry); 
	    	 if($flag !==false ){
	    		$reviewArry = array_merge($reviewArry,$flag['comments']);
	    		$titleArry = array_merge($titleArry,$flag['title']);
	    		$idArry = array_merge($idArry,$flag['reviewid']);
	    	}   		
    	}
      
    	
       if(count($reviewArry)<3){
    		$url = str_replace("{STAR}", '3', $turl);
    		$flag= $this->getReviewsByStar($url,$reviewArry,$titleArry,$idArry);    
	        if($flag !==false ){
	    		$reviewArry = array_merge($reviewArry,$flag['comments']);
	    		$titleArry = array_merge($titleArry,$flag['title']);
	    		$idArry = array_merge($idArry,$flag['reviewid']);
	    	}		
    	}
    	
    	if(count($reviewArry)>0){
    		return array(
				    'comments'=>$reviewArry,
				    'title'=>$titleArry,
			        'reviewid'=>$idArry,
			        'id'=>$id,
			);
    	}
		return false;
    }
    
public function  getReviewsByStar($url){
    	writetofile("updateReview.txt", 'getReviewsById for vod url{=}'.$url );
    	$content =getPageWindow($url, "utf-8");
//    	$content=getBodys($content, '<div id="content">');var_dump($content);
        writetofile("updateReviewContent.txt", 'getReviewsById for vod url{=}'.$url );
        writetofile("updateReviewContent.txt", 'getReviewsById for vod url{=}'.$content );
		if(isset($content) && !is_null($content)){
			$reviewArry=array();
			$titleArry=array();
			$idArry=array();
			$titlesID = getArray($content,'<a target="_blank" title','onclick="moreurl(this');
			$titles = getArray($titlesID,'="','"href="http://movie.douban.com/review');
			$reviewids= getArray($titlesID, "/review/", "/\"");
			$reviewidsArray = split('{Array}', $reviewids);
			$titlesArray = split('{Array}', $titles);
			$count=count($reviewidsArray);			
			for($i=0;$i<$count && $i<3; $i++){
				$tempUrl= 'http://movie.douban.com/j/review/'.$reviewidsArray[$i].'/fullinfo?show_works=False';	
			   //var_dump($tempUrl);
			   writetofile("updateReview.txt", 'getReviewsById for vod url{=}'.$tempUrl );
			   $tempContent= getPage($tempUrl, "utf-8");
			   $review=getBody($tempContent, "\"html\":\"", "<div class=");//"html":"
			    writetofile("updateReviewContent.txt", 'getReviews'.$review );
			   $review=replaceStr($review, "\\r<br\/>", Chr(13));
			   if(!isN($review)){
				   $reviewArry[]=$review;
				   $titleArry[]=$titlesArray[$i];
				   $idArry[]=$reviewidsArray[$i];
			   }
			}
			if(count($reviewArry)>0){
				return array(
					    'comments'=>$reviewArry,
					    'title'=>$titleArry,
				        'reviewid'=>$idArry
				);
			}else {
				return false;
			}
		}
		return false;
		
    }
    
    
 const PIC_CONTENT_START='<ul class="poster-col4 clearfix">';
 const PIC_CONTENT_END='</ul>';
 const PIC_URL_START='<img src="';
 const PIC_URL_END='"';
 const PIC_RATES_START='<div class="prop">';
 const PIC_RATES_END='</div>';
 
    public function  getPicById($id,$rate){
	  $url = str_replace("{ID}", $id, AutoDouBanParseScore::PIC_URL_NORMAL);
	  $pic=$this->getPicByUrl($url, $rate);
	  writetofile("updateVodPic.txt", 'check item for PIC_URL_NORMAL url{=}'.$url );
      if($pic ===false ){
      	$url = str_replace("{ID}", $id, AutoDouBanParseScore::PIC_URL);
	  writetofile("updateVodPic.txt", 'check item for PIC_URL url{=}'.$url );
      	$pic=$this->getPicByUrl($url, $rate);
      }
      if($pic ===false ){
       	$url = str_replace("{ID}", $id, AutoDouBanParseScore::PIC_JIZHAO);
	    writetofile("updateVodPic.txt", 'check item for PIC_JIZHAO url{=}'.$url );
      	$pic=$this->getPicByUrl($url, $rate);
      }
      return array('pic'=>$pic,
				             'id'=>$id,
				);
     
    }
    
    private function getPicByUrl($url,$rate){
      $content = $this->readContent($url);   
	  if(isset($content) && !is_null($content)){
        $content=getBody($content,AutoDouBanParseScore::PIC_CONTENT_START,AutoDouBanParseScore::PIC_CONTENT_END);
		$picUrls = getArray($content, AutoDouBanParseScore::PIC_URL_START, AutoDouBanParseScore::PIC_URL_END);
		$picRates = getArray($content, AutoDouBanParseScore::PIC_RATES_START, AutoDouBanParseScore::PIC_RATES_END);
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
 
        $content=getBodys($content,AutoDouBanParseScore::COMMENT_CONTENT_START);
        
		$dates = getArray($content, AutoDouBanParseScore::DATE_START, AutoDouBanParseScore::DATE_END);
		$coments= getArray($content, AutoDouBanParseScore::CONTENT_START, AutoDouBanParseScore::CONTENT_END);
		$coments=replaceStr($coments, "&#34;", "\"");
		$USERS = getArray($content, AutoDouBanParseScore::USERS_START, AutoDouBanParseScore::USERS_END);
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
//$d = new AutoDouBanParseScore();
//var_dump( $d->getReviewsById('6844868'));
////var_dump( $d->getDouBanPics("大上海", '2032', '',5/7));
//var_dump( $d->getDoubanThumb("大上海", '2032', ''));

//$s=rand(0, 10)+rand(0, 10)/10;
//echo $s;
//$httpClient = new HttpClient("api.douban.com",80);
//$content = $httpClient->quickGet('http://api2.v.pptv.com/api/page/episodes.js?page=1&channel_id=10034178');
//
//var_dump( AutoDouBanParseScore::format($content));

$s='<table width="765" cellspacing="3" cellpadding="0" border="0">
        <tbody><tr> 
          <td width="65" valign="top"><img src="/images/icon_difang.gif"></td>
          <td width="200" class="listtop"><table width="180" cellspacing="0" cellpadding="0" border="0">
              <tbody><tr> 
                <td width="45"><img width="45" height="20" src="/images/loca_1.gif"></td>
<td nowrap="" background="/images/loca_bg1.gif">&nbsp;<a href="/program/TV_38/Channel_60/W3.htm">北京</a>&nbsp;<a href="/program/TV_11/Channel_81/W3.htm">上海</a>&nbsp;<a href="/program/TV_35/Channel_57/W3.htm">天津</a>&nbsp;<a href="/program/TV_37/Channel_59/W3.htm">重庆</a></td>
<td width="4"><img width="4" height="20" src="/images/loca_4.gif"></td>
</tr>
</tbody></table>
<table width="100" cellspacing="0" cellpadding="0" border="0">
<tbody><tr> 
<td height="5"></td>
</tr>
</tbody></table>
<table width="180" cellspacing="0" cellpadding="0" border="0">
<tbody><tr> 
<td width="45"><img width="45" height="20" src="/images/loca_2.gif"></td>
<td nowrap="" background="/images/loca_bg1.gif">&nbsp;<a href="/program/TV_3/Channel_22/W3.htm">香港</a>&nbsp;<a href="/program/TV_150/Channel_737/W3.htm">澳门</a>&nbsp;<a href="/program/TV_68/Channel_246/W3.htm">台湾</a>&nbsp;<a href="/programjw/TV_5/Channel_26/W3.htm">境外</a></td>
<td width="4"><img width="4" height="20" src="/images/loca_4.gif"></td>
</tr>
</tbody></table></td>
<td class="listtop"><table width="100%" cellspacing="0" cellpadding="0" border="0">
<tbody><tr> 
<td width="40"><img width="40" height="45" src="/images/loca_3.gif"></td>
<td nowrap="" background="/images/loca_bg2.gif"> <table width="100%" cellspacing="0" cellpadding="0" border="0">
<tbody><tr> 
<td width="8" nowrap=""></td>
<td nowrap=""><a href="/program/TV_24/Channel_46/W3.htm">湖南</a>&nbsp;<a href="/program/TV_25/Channel_47/W3.htm">湖北</a>&nbsp;<a href="/program/TV_22/Channel_44/W3.htm">江苏</a>&nbsp;<a href="/program/TV_23/Channel_45/W3.htm">江西</a>&nbsp;<a href="/program/TV_20/Channel_42/W3.htm">安徽</a>&nbsp;<a href="/program/TV_21/Channel_43/W3.htm ">浙江</a>&nbsp;<a href="/program/TV_29/Channel_51/W3.htm">福建</a><br><a href="/program/TV_39/Channel_70/W3.htm">广东</a>&nbsp;<a href="/program/TV_28/Channel_50/W3.htm">广西</a>&nbsp;<a href="/program/TV_36/Channel_58/W3.htm">四川</a>&nbsp;<a href="/program/TV_27/Channel_49/W3.htm">云南</a>&nbsp;<a href="/program/TV_26/Channel_48/W3.htm">贵州</a>&nbsp;<a href="/program/TV_8/Channel_31/W3.htm">海南</a>&nbsp;</td>
<td width="8" nowrap=""></td>
</tr>
</tbody></table></td>
<td width="1" nowrap="" background="/images/loca_bg2.gif"><img src="/images/loca_7.gif"></td>
<td nowrap="" background="/images/loca_bg2.gif"><table width="100%" cellspacing="0" cellpadding="0" border="0">
<tbody><tr>
<td width="8" nowrap=""></td>
<td nowrap=""> <a href="/program/TV_19/Channel_41/W3.htm">山东</a>&nbsp;<a href="/program/TV_17/Channel_39/W3.htm ">山西</a>&nbsp;<a href="/program/TV_16/Channel_38/W3.htm">河南</a>&nbsp;<a href="/program/TV_15/Channel_37/W3.htm">河北</a>&nbsp;<a href="/program/TV_18/Channel_40/W3.htm">陕西</a>&nbsp;<a href="/program/TV_30/Channel_52/W3.htm">甘肃</a>&nbsp;<a href="/program/TV_12/Channel_34/W3.htm">黑龙江</a><br><a href="/program/TV_14/Channel_36/W3.htm">辽宁</a>&nbsp;<a href="/program/TV_13/Channel_35/W3.htm">吉林</a>&nbsp;<a href="/program/TV_33/Channel_55/W3.htm">新疆</a>&nbsp;<a href="/program/TV_32/Channel_54/W3.htm">西藏</a>&nbsp;<a href="/program/TV_31/Channel_53/W3.htm">宁夏</a>&nbsp;<a href="/program/TV_114/Channel_592/W3.htm">青海</a>&nbsp;<a href="/program/TV_34/Channel_56/W3.htm">内蒙古</a></td>
                      <td></td>
                    </tr>
                  </tbody></table></td>
                <td width="4"><img width="4" src="/images/loca_5.gif"></td>
              </tr>
            </tbody></table></td>
        </tr>
       

</tbody></table></td>
 </tr>
 </tbody></table></td>
 </tr>
      </tbody></table>';
//$codes=getArray($s, "/program/", '</a>');
//
//$codesArray = explode("{Array}", $codes);
////http://epg.tvsou.com/program/
////var_dump($codesArray);
//$area=array();
//foreach ($codesArray as $code){
//	$ste= explode('">', $code);
//	$area[$ste[1]]='http://epg.tvsou.com/program/'.$ste[0];
//}
//var_dump($area);

?>