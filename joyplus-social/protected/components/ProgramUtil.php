<?php
class ProgramUtil{
	public static function exportProgramEntity($program){
        $prod = array();
		switch (CacheManager::getTopParentType($program->d_type)){
			case Constants::PROGRAM_TYPE_TV:
				$prod['tv']=ProgramUtil::genTV($program,true);
				break;
				
			case Constants::PROGRAM_ANIMATION:
				$prod['tv']=ProgramUtil::genTV($program,true);
				break;
					
			case Constants::PROGRAM_TYPE_SHOW:
				$prod['show']=ProgramUtil::genTV($program,false);
				break;
					
			case Constants::PROGRAM_TYPE_MOVIE:
				$prod['movie']=ProgramUtil::genTV($program,true);
				
				break;
			case Constants::PROGRAM_TYPE_VIDEO:
				$prod['video']=ProgramUtil::genMovie($program);
				break;
		}
		return $prod;
	}
private static function isN($str)
{
	if (is_null($str) || $str==''){ return true; }else{ return false;}
}

private static function escapeNum($numS){
 $temp = str_split($numS);
  $flag=false;
  $setNum="";
  if(isset($temp) && is_array($temp)){
  	foreach ($temp as $num){
  		if(is_numeric($num)){
  			$setNum=$setNum.$num;  
  			$flag=true;			
  		}else {
  			if($flag){
  				break;
  			}
  		}
  	}
  }
  return $setNum;
}
private static function parsePadPost($pic_url){
  if(isset($pic_url) && !is_null($pic_url)){
      $prodPicArray = explode("{Array}", $pic_url);	  
      if(count($prodPicArray)>0){
	      return $prodPicArray[0];
	  }
  }
  return $pic_url;
}
private static function genTV($program,$flag){
	  $prod= array(
          'name'=>$program->d_name,
          'summary'=>$program->d_content,
          'poster'=>$program->d_pic,
          'episodes_count'=>$program->d_state,
          'cur_episode'=>$program->d_state,
          'max_episode'=>$program->d_remarks,
          'sources'=>$program->d_playfrom,
          'like_num'=>$program->love_user_count,
          'watch_num'=>$program->watch_user_count,
          'favority_num'=>$program->favority_user_count,  
	      'score'=>$program->d_score,  
	      'ipad_poster'=>ProgramUtil::parsePadPost($program->d_pic_ipad), 
	      'support_num'=>$program->good_number,	      
	      'publish_date'=>$program->d_year, 	        
	      'directors'=>$program->d_directed,  	        
	      'stars'=>$program->d_starring, 
	      'id'=>$program->d_id,  
	      'definition'=>$program->d_level,  
	      'area'=>$program->d_area,  
	      'total_comment_number'=>$program->total_comment_number,
	      'douban_id'=>$program->d_douban_id,
//	      'typeName'=>$program->d_type_name  ,
		);
	try{
		$tmpweburl = $program->webUrls;
		$tmpplayfrom = $program->d_playfrom;
        $webUrlArray= ProgramUtil::getTVWebList($tmpweburl, $tmpplayfrom);
        
        $videoUrlArray=(ProgramUtil::getTVVideoList($program->d_downurl));
        $tempArray= array();
        $existVideos="{Array}";
        foreach ($webUrlArray as $webUrl){
        	$name = $webUrl['name'];
        	if(is_array($videoUrlArray) && array_key_exists($name, $videoUrlArray)){
        		$webUrl['down_urls']=$videoUrlArray[$name];
        		$existVideos=$existVideos.$name.'{Array}';
        	} 
        	if($flag){
        		$webUrl['name']=ProgramUtil::escapeNum($name);
        	}       	
        	$tempArray[]=$webUrl;
        }
        $keys= array_keys($videoUrlArray);
//        var_dump($keys);
        foreach ($keys as $key){
        	if (strpos($existVideos, "{Array}".$key."{Array}") === false) {
        		$tempkey=$key;
        		if($flag){
        			$tempkey=ProgramUtil::escapeNum($tempkey);
        		}
        		$tempArray[]=array('name'=>$tempkey,
        		'down_urls'=>$videoUrlArray[$key],);
        	}
        }
      
	}catch (Exception $e){
		$webUrlArray=array();
	}
	  $prod['episodes']=$tempArray;
	  return $prod;
   }
   
   private static function getTVWebList($tmpweburl,$tmpplayfrom){   	   	  
        if (is_null($tmpweburl) || $tmpweburl==''  ||is_null($tmpplayfrom) || $tmpplayfrom=='') { 
        	return  array();
        }		
		
		$webArrays = explode("$$$",$tmpweburl);
		$playfromArray = explode("$$$",$tmpplayfrom);
		$numPlatformUrl = array();
		
		for ($k=0;$k<count($playfromArray);$k++){
			$tmpplayfrom=$playfromArray[$k];
			
			if(is_null($tmpplayfrom) || $tmpplayfrom=='' || strpos("wasu,kankan,tudou,cntv", $tmpplayfrom) !==false){
				continue;
			}	
					
			$platformWebUrl= $webArrays[$k];
		    if (is_null($platformWebUrl) || $platformWebUrl=='') { $platformWebUrl="";}
		    $webUrls=explode("{Array}",$platformWebUrl);
		    $index=1;
		    for ($j=0;$j<count($webUrls);$j++){
		    	$webUrl=$webUrls[$j];
		    	$nameUrl=explode("$",$webUrl);
		    	$name="";
		    	$url="";
		    	if(count($nameUrl)==2){
		    		$name=$nameUrl[0];
		    		if(ProgramUtil::isN($name)){
		    			$name=$index;
		    		}
		    		$url=$nameUrl[1];
		    	}
		        if(count($nameUrl)==1){
		    		$name=$index;
		    		$url=$nameUrl[0];
		    	}
		    	$index++;
		    	$temp=array('name'=>$name,'video_urls'=>array());		    		
		    	if((isset($numPlatformUrl[$name]) && is_array($numPlatformUrl[$name]))){
		    		$temp =$numPlatformUrl[$name];
		    	}
		    	
		    	if( !ProgramUtil::isN($url) && (ProgramUtil::urlValid($url))){
			    	$temp['video_urls'][]=array(
			    	  'source'=>$tmpplayfrom,
	                  'url'=>$url
			    	);
		    	}
		    	$numPlatformUrl[$name]=$temp;
		    }
		}
		$keys= array_keys($numPlatformUrl);
		$sp = array();
		for($i=0;$i<count($keys);$i++){
			$sp[]=$numPlatformUrl[$keys[$i]];
		}
		return $sp;
   }
   
   private static function getContentType($url,$tmpplayfrom){
   	 if(strpos($url, 'm3u8') !==false || strpos($url, 'm3u') !==false  ||( $tmpplayfrom ==='letv' && strpos($url, 'tss=ios') !==false ) ){
   	 	return "m3u8";
   	 }else {
   	 	return "mp4";
   	 }
   }
   
   private static function getTVVideoList($tmpvideourl){   	   	  
        if (is_null($tmpvideourl) || $tmpvideourl=='' ) { 
        	return  array();
        }		
		
		$webArrays = explode("$$$",$tmpvideourl);
		$numPlatformUrl = array();
		$playfroms="";
		for ($k=0;$k<count($webArrays);$k++){
			$weburlarr2=explode("$$",$webArrays[$k]);
			$tmpplayfrom=$weburlarr2[0];
			
			if(is_null($tmpplayfrom) || $tmpplayfrom=='' || strpos("wasu,kankan,tudou,cntv", $tmpplayfrom) !==false){
				continue;
			}			
			if(count($weburlarr2)>=2 && isset($tmpplayfrom) && !is_null($tmpplayfrom) && strlen(trim($tmpplayfrom))>0 && strpos($playfroms, $tmpplayfrom) ===false ){
				$playfroms=$playfroms.'.'.$tmpplayfrom;
				$platformWebUrl= $weburlarr2[1];
			    if (is_null($platformWebUrl) || $platformWebUrl=='') { $platformWebUrl="";}
			    $webUrls=explode("{Array}",$platformWebUrl);
			    $index=1;
			    for ($j=0;$j<count($webUrls);$j++){
			    	$webUrl=$webUrls[$j];
			    	$nameUrl=explode("$",$webUrl);
			    	$name="";
			    	$url="";
			    	if(count($nameUrl)==2){
			    		$name=$nameUrl[0];
			    		if(ProgramUtil::isN($name)){
			    			$name=$index;
			    		}
			    		$url=$nameUrl[1];
			    	}
			        if(count($nameUrl)==1){
			    		$name=$index;
			    		$url=$nameUrl[0];
			    	}
			    	$index++;
			    	$temp=array();		    		
			    	if((isset($numPlatformUrl[$name]) && is_array($numPlatformUrl[$name]))){
			    		$temp =$numPlatformUrl[$name];
			    	}
			    	
			    	$temp[]=array(
			    	  'source'=>$tmpplayfrom,
	                  'urls'=>ProgramUtil::parseDownVideoUrls($url,$tmpplayfrom),
			    	);
			    	$numPlatformUrl[$name]=$temp;
			    }
			}
		}
		return $numPlatformUrl;
   }
   private static function urlValid($url){
   	 $urlValid= new CUrlValidator();
   	 return $urlValid->validateValue($url);
   }
  
   public static function parseDownVideoUrls($url,$tmpplayfrom){
   	 if(ProgramUtil::isN($url)){
   	 	return array();
   	 }
   	 $temp = array();
   	 $videoTypeArrs= explode(MovieType::VIDEO_SEP_VERSION, $url);
   	 foreach ($videoTypeArrs as $videoType){
   	 	$videoTypeNameUrl = explode(MovieType::VIDEO_NAME_URL_SEP,$videoType);
   	 	if(count($videoTypeNameUrl)==2 && !ProgramUtil::isN($videoTypeNameUrl[1])){
   	 		if(ProgramUtil::urlValid($videoTypeNameUrl[1])){
	   	 		$temp[]= array(
	   	 		    "type"=>$videoTypeNameUrl[0],
	   	 		    "url"=>$videoTypeNameUrl[1],
				    'file'=>ProgramUtil::getContentType($videoTypeNameUrl[1],$tmpplayfrom),
	   	 		);
   	 		}
   	 	}
   	   if(count($videoTypeNameUrl)==1 && !ProgramUtil::isN($videoTypeNameUrl[0])){
   	 		if(ProgramUtil::urlValid($videoTypeNameUrl[0])){
	   	 		$temp[]= array(
	   	 		    "type"=>MovieType::HIGH_CLEAR,
	   	 		    "url"=>$videoTypeNameUrl[0],
				    'file'=>ProgramUtil::getContentType($videoTypeNameUrl[0],$tmpplayfrom),
	   	 		);
   	 		}
   	 	}
   	 }
   	 return $temp;
   }
   private static function genMovie($program){
	  $prod= array(
          'name'=>$program->d_name,
          'summary'=>$program->d_content,
          'poster'=>$program->d_pic,
          'sources'=>$program->d_playfrom,
          'like_num'=>$program->love_user_count,
          'watch_num'=>$program->watch_user_count,
          'favority_num'=>$program->favority_user_count, 
	      'score'=>$program->d_score,  
	      'ipad_poster'=>ProgramUtil::parsePadPost($program->d_pic_ipad),
	      'support_num'=>$program->good_number,	      
	      'publish_date'=>$program->d_year, 	        
	      'directors'=>$program->d_directed,  	        
	      'stars'=>$program->d_starring,   
	      'id'=>$program->d_id,       
	      'area'=>$program->d_area,   
	      'total_comment_number'=>$program->total_comment_number, 
//	      'typeName'=>$program->d_type_name  ,     
	  );
      $tmpweburl = $program->webUrls;
      $tmpdownurl = $program->d_downurl;
	  $tmpplayfrom = $program->d_playfrom;
      if (!(is_null($tmpweburl) || $tmpweburl==''  ||is_null($tmpplayfrom) || $tmpplayfrom=='')) {         	
        $webArrays = explode("$$$",$tmpweburl);
		$playfromArray = explode("$$$",$tmpplayfrom);
		$video_urls = array();		
		for ($k=0;$k<count($playfromArray);$k++){
			$tmpplayfrom=$playfromArray[$k];
						
			$platformWebUrl= $webArrays[$k];
		    $tempUrl = explode("$", $platformWebUrl);
		    if(count($tempUrl)==2){
		    	$platformWebUrl=$tempUrl[1];
		    }
		    
		    if(!ProgramUtil::isN($platformWebUrl)){
				$video_urls[]=array(
				  'source'=>$tmpplayfrom,
	               'url'=>$platformWebUrl,
				);
		    }
			
		}		
	    $prod['video_urls']=$video_urls;
      }
      $playfroms="";
     if (!(is_null($tmpdownurl) || $tmpdownurl=='' )) {         	
        $webArrays = explode("$$$",$tmpdownurl);
		$video_urls = array();		
		for ($k=0;$k<count($webArrays);$k++){
//			var_dump($webArrays[$k]);
			$weburlarr2=explode("$$",$webArrays[$k]);
//			var_dump($weburlarr2);
			$tmpplayfrom=$weburlarr2[0];	
					
			
			if(isset($tmpplayfrom) && !is_null($tmpplayfrom) && strlen(trim($tmpplayfrom))>0 && strpos($playfroms, $tmpplayfrom) ===false ){
				if(count($weburlarr2)<2){
					continue;
				}
				$playfroms=$playfroms.','.$tmpplayfrom;
				$platformWebUrl= $weburlarr2[1];
			    if (is_null($platformWebUrl) || $platformWebUrl=='') { $platformWebUrl="";}
			    $tempUrl = explode("$", $platformWebUrl);
			    if(count($tempUrl)==2){
			    	$platformWebUrl=$tempUrl[1];
			    }
				$video_urls[]=array(
				  'source'=>$tmpplayfrom,
	                  'urls'=>ProgramUtil::parseDownVideoUrls($platformWebUrl,$tmpplayfrom),
				);
			}
		}		
	    $prod['down_urls']=$video_urls;
      }
	  return $prod;
    }
    
    public static function parseMovidePlayurl($tmpdownurl){
    	$playfroms="";$video_urls = array();
     if (!(is_null($tmpdownurl) || $tmpdownurl=='' )) {         	
        $webArrays = explode("$$$",$tmpdownurl);
				
		for ($k=0;$k<count($webArrays);$k++){
//			var_dump($webArrays[$k]);
			$weburlarr2=explode("$$",$webArrays[$k]);
//			var_dump($weburlarr2);
			$tmpplayfrom=$weburlarr2[0];	
					
			
			if(isset($tmpplayfrom) && !is_null($tmpplayfrom) && strlen(trim($tmpplayfrom))>0 && strpos($playfroms, $tmpplayfrom) ===false ){
				if(count($weburlarr2)<2){
					continue;
				}
				$playfroms=$playfroms.','.$tmpplayfrom;
				$platformWebUrl= $weburlarr2[1];
			    if (is_null($platformWebUrl) || $platformWebUrl=='') { $platformWebUrl="";}
			    $tempUrl = explode("$", $platformWebUrl);
			    if(count($tempUrl)==2){
			    	$platformWebUrl=$tempUrl[1];
			    }
				$video_urls[]=array(
				  'source'=>$tmpplayfrom,
	                  'urls'=>ProgramUtil::parseDownVideoUrls($platformWebUrl,$tmpplayfrom),
				);
			}
		}
     }	
     return $video_urls;
    }

}
?>