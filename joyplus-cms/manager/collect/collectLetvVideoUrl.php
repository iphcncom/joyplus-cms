<?php
ob_implicit_flush(true);
ini_set('max_execution_time', '0');

require_once ("../admin_conn.php");
require_once ("collect_fun.php");
require_once ("MovieType.php");
require_once ("tools/ContentManager.php");
require_once ("Project.php");

    $pagenum = be("all", "startpage");
    
    $endPage = be("all", "endpage");
 
    if (!isNum($pagenum)){
    	$pagenum = 1;
    } else { 
    	$pagenum = intval($pagenum);
    }

	updateLetvVideoUrl($pagenum,$endPage);



	function updateLetvVideoUrl($pagenum,$endPage){	
	     global $db;
		 $sql = "SELECT count(*)  FROM {pre}vod WHERE webUrls IS not NULL and webUrls != ''  and d_hide =0 and d_type in (1,2,3,131)  and d_status = 0 and d_playfrom like '%letv%'  " ;
		 $nums = $db->getOne($sql);  
		 $app_pagenum=10; 
		 $pagecount=ceil($nums/$app_pagenum);
		 if (!isNum($endPage)){
	    	$endPage = $pagecount;
	     } else { 
	    	$endPage = intval($endPage);
	     }
	//	 $pagecount=2;
		 for($i=$pagenum;$i<=$pagecount&&$i<=$endPage;$i++){
		 	writetofile("updateLetvVideoUrl.log", 'check item for vod type{=}'.$nums .'{=}Total{=}'.$pagecount.'{=}'.$i);
		    $sql = "SELECT webUrls,d_downurl, d_playfrom,d_id FROM {pre}vod WHERE webUrls IS not NULL and webUrls != '' and d_hide =0 and d_type in (1,2,3,131)   and d_playfrom like '%letv%' and d_status = 0 order by d_type asc,d_play_num desc limit ".($app_pagenum * ($i-1)) .",".$app_pagenum;
	//	    var_dump($sql);
		    $rs = $db->query($sql); 
		    parseVodPad($rs);
		    unset($rs);
		    //sleep(60);
		 }
	}


    function parseVodPad($rs){
    	global $db;
        while ($row = $db ->fetch_array($rs))	{
    		$webUrls=$row["webUrls"];$d_downurl=$row["d_downurl"]; $d_playfrom=$row["d_playfrom"];
			$d_id=$row["d_id"];	
		      parseOneVod($webUrls,$d_downurl,$d_playfrom,$d_id);
	    }
	    unset($rs);
    }
    
    function parseOneVod($webUrls,$d_downurl,$d_playfrom,$d_id){
       global $db; 
       writetofile('updateLetvVideoUrl.log', 'd_id===='.$d_id);   		
	   $playurlarr1 = explode("$$$",$webUrls);
	   $playfromarr = explode("$$$",$d_playfrom);
	   $playurl='';
	   for ($i=0;$i<count($playurlarr1);$i++){
            if(!isN($playurlarr1[$i])){
	           $playfrom = $playfromarr[$i];
	           if($playfrom === 'letv'){
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
	          writetofile('updateLetvVideoUrl.log', 'name===='.$name.'====url===='.$url); 
//	          var_dump($name);
//	          var_dump($url);
	          if(!isN($url)){
	          	$isovideoUrl = ContentProviderFactory::getContentProvider(ContentProviderFactory::LETV)->parseIOSVideoUrl($url, "utf-8", '');
	    		$androidUrl = ContentProviderFactory::getContentProvider(ContentProviderFactory::LETV)->parseAndroidVideoUrl($url, "utf-8", '');
	            
	    		if(!isN($isovideoUrl)){
					if(!isN($androidUrl)){
					  $isovideoUrl=$androidUrl.MovieType::VIDEO_SEP_VERSION.$isovideoUrl;
					}
				}else {
					$isovideoUrl=$androidUrl;
				}
//				 var_dump($isovideoUrl);
	             if ($playnum ===1) {
					$videoUrl .= $name.'$'.$isovideoUrl;
			     }else{
					$videoUrl .= "{Array}".$name.'$'.$isovideoUrl;
				 }				 
	          }	         
	       }
//	       var_dump($videoUrl);
	   }
	   $oldLetvVedioUrls='';
	   if(!isN($videoUrl) && strpos($videoUrl, "http") !==false){
	   	  $videoUrl='letv$$'.$videoUrl  ;	   
	   	  if (isN($d_downurl)){
	   	  	$d_downurl=$videoUrl;
	   	  }else {
		   	  $d_downurlArray = explode("$$$", $d_downurl);  		   	  
		   	  foreach ($d_downurlArray as $downUrls){
		   	  	$downUrlsArray = explode("$$", $downUrls);
		   	  	if($downUrlsArray[0]==='letv'){
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
	   	  writetofile('updateLetvVideoUrl_sql.log', $sql);
	   	  $db->query($sql);
	   }
	   
    }

?>