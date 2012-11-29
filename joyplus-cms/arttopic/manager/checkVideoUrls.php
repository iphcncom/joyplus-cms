<?php
	require_once ("admin_conn.php");
	require_once (dirname(__FILE__)."/collect/MovieType.php");
	$sql = "SELECT d_id,d_downurl FROM {pre}vod  where d_id=14920";
	removeInvalidVideoAddress($sql);
	function removeInvalidVideoAddress($sql){
		global $db;
	      
		$rs = $db->query($sql);
	
		$rscount = $db -> num_rows($rs);
	
		if($rscount==0){
			errmsg ("没有可用的数据");
		}else {
			while ($row = $db ->fetch_array($rs))	{			
				$d_id=$row["d_id"];
				$d_downurl = $row[d_downurl];
				checkMovieVideoUrl($d_id,$d_downurl)	;		
			}
		}
		unset($rs);
	}
	
	function checkMovieVideoUrl($id,$d_downurl){
		if(!isN($d_downurl)){
			$playWebUrls=explode("$$$",$d_downurl);
			for ($k=0;$k<count($playWebUrls);$k++){
			  $weburlarr2=explode("$$",$playWebUrls[$k]);
			  $tmpplayfrom=$weburlarr2[0];
			  $platformWebUrl= $weburlarr2[1];
			  var_dump("playfrom: ".$tmpplayfrom);
			  if (is_null($platformWebUrl) || $platformWebUrl=='') { $platformWebUrl="";}
			  $webUrls=explode("{Array}",$platformWebUrl);
			  for ($j=0;$j<count($webUrls);$j++){
			    	$webUrl=$webUrls[$j];
			    	$nameUrl=explode("$",$webUrl);
			    	$name="";
			    	$url="";
			    	if(count($nameUrl)==2){
			    		$name=$nameUrl[0];
			    		$url=$nameUrl[1];
			    	}
			        if(count($nameUrl)==1){
			    		$name=$index;
			    		$url=$nameUrl[0];
			    	}
			    	var_dump("name: ".$name);
			    	
			    	parseDownVideoUrls($url);
			  }
			}
		}	
	}
	
	function parseDownVideoUrls($url){
   	 if(isN($url)){
   	 	return array();
   	 }
   	 $temp = array();
   	 $videoTypeArrs= explode(MovieType::VIDEO_SEP_VERSION, $url);
   	 foreach ($videoTypeArrs as $videoType){
   	 	$videoTypeNameUrl = explode(MovieType::VIDEO_NAME_URL_SEP,$videoType);
   	 	
   	 	if(count($videoTypeNameUrl)==2){
   	 		$type=$videoTypeNameUrl[0];
   	 		$url=$videoTypeNameUrl[1];
   	 	}
   	   if(count($videoTypeNameUrl)==1){
   	   	    $type=MovieType::HIGH_CLEAR;
   	 		$url=$videoTypeNameUrl[0];
   	 		
   	 	}
   	 	var_dump("movie Type: " . $type);
   	 	var_dump("movie url: " . $url);
   	 }
   	 return $temp;
   }

?>