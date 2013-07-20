<?php
ob_implicit_flush(true);
ini_set('max_execution_time', '0');

require_once ("../admin_conn.php");
require_once ("collect_fun.php");
require_once ("MovieType.php");
require_once ("tools/ContentManager.php");

    $id = be("all", "id");
    $name = be("all", "name");
    $download_urls = be("all", "urls");    
    $playfrom = be("all", "from"); 

    $action = be("all","action");
    
    if(isN($id)){
    	exit(-1);
    }
    
    if(isN($download_urls)){
    	 global $db;
    	 $db->Add("tbl_video_feedback", array('prod_id','feedback_type','create_date'), array($id,'10',date('Y-m-d H:i:s',time())));
    }
    
	switch($action)
	{   case "episode" :  editall();break; 
		case "video" : updateVideoUrl($id,$playfrom);break;
		default : updateVideoUrl($id,$playfrom);break;
	}
    
	

	function updateVideoUrl($id,$playfrom){	
		 if(!isN(playfrom)){
	        global $db;		
		 	writetofile("updateVideoUrl.log", 'check item for vod playfrom{=}'.$playfrom .'{=}id{=}'.$id);
		    $sql = "SELECT webUrls,d_downurl, d_playfrom,d_id FROM {pre}vod WHERE  d_playfrom like '%".$playfrom."%'  and d_id=".$id;
	        $rs = $db->query($sql); 
		    parseVodPad($rs,$playfrom);
		    unset($rs);
		 }
		
	}


    function parseVodPad($rs,$playfrom){
    	global $db;
        while ($row = $db ->fetch_array($rs))	{
    		$webUrls=$row["webUrls"];$d_downurl=$row["d_downurl"]; $d_playfrom=$row["d_playfrom"];
			$d_id=$row["d_id"];	
		      parseOneVod($webUrls,$d_downurl,$d_playfrom,$d_id,$playfrom);
	    }
	    unset($rs);
    }
    
    function parseOneVod($webUrls,$d_downurl,$d_playfrom,$d_id,$from){
       global $db; 
       writetofile('updateVideoUrl.log', 'd_id===='.$d_id);   		
	   $playurlarr1 = explode("$$$",$webUrls);
	   $playfromarr = explode("$$$",$d_playfrom);
	   $playurl='';
	   for ($i=0;$i<count($playurlarr1);$i++){
            if(!isN($playurlarr1[$i])){
	           $playfrom = $playfromarr[$i];
	           if($playfrom === $from){
                   $playurl =$playurlarr1[$i];
                   break;
	           }
            }
	   }
	   $videoUrl='';
	   if(!isN($playurl)){
	       $playurlArray = explode("{Array}", $playurl);
	       $playnum=0;
	       foreach ($playurlArray as $nameUrls){
	          $playnum++; 
	          $nameUrlsArray   = explode("$", $nameUrls);
	          $name='';
	          $url='';
	          if(count($nameUrlsArray)==2){
	          	$name=$nameUrlsArray[0];
	          	$url=$nameUrlsArray[1];
	          }
	          
	          if(count($nameUrlsArray)==1){
	          	$name=$nameUrlsArray[0];
	          	if(strpos($name, 'http') !==false){
	          		$url=$name;
	          		$name='';	          		
	          	}
	          }
	          if(isN($name)){
	          	$name=$playnum;
	          }
	          writetofile('updateVideoUrl.log', 'name===='.$name.'====url===='.$url); 
	          if(!isN($url)){
	          	$isovideoUrl = ContentProviderFactory::getContentProvider($from)->parseIOSVideoUrl($url, "utf-8", '');
	    		$androidUrl = ContentProviderFactory::getContentProvider($from)->parseAndroidVideoUrl($url, "utf-8", '');
	            
	    		if(!isN($isovideoUrl)){
					if(!isN($androidUrl)){
					  $isovideoUrl=$androidUrl.MovieType::VIDEO_SEP_VERSION.$isovideoUrl;
					}
				}else {
					$isovideoUrl=$androidUrl;
				}
	             if ($playnum ===1) {
					$videoUrl .= $name.'$'.$isovideoUrl;
			     }else{
					$videoUrl .= "{Array}".$name.'$'.$isovideoUrl;
				 }				 
	          }	         
	       }
	   }
	   $oldLetvVedioUrls='';
	   if(!isN($videoUrl) && strpos($videoUrl, "http") !==false){
	   	  $videoUrl=$from.'$$'.$videoUrl  ;	   
	   	  if (isN($d_downurl)){
	   	  	$d_downurl=$videoUrl;
	   	  }else {
		   	  $d_downurlArray = explode("$$$", $d_downurl);  		   	  
		   	  foreach ($d_downurlArray as $downUrls){
		   	  	$downUrlsArray = explode("$$", $downUrls);
		   	  	if($downUrlsArray[0]===$from){
		   	  		$oldLetvVedioUrls=$downUrls;
		   	  		break;
		   	  	}
		   	  }
		   	  if(isN($oldLetvVedioUrls)){
		   	  	$d_downurl=$d_downurl."$$$".$videoUrl;
		   	  }else {
		   	  	$d_downurl=replaceStr($d_downurl, $oldLetvVedioUrls, $videoUrl);
		   	  }
		   	  $d_downurl=replaceStr($d_downurl, '$$$$$$', '$$$');
	   	  }
	   	  $sql= "update {pre}vod set d_downurl='".$d_downurl."' where d_id=" .$d_id;
	   	  writetofile('updateVideoUrl.log', $sql);
	   	  $db->query($sql);
	   }
	   
    }

?>