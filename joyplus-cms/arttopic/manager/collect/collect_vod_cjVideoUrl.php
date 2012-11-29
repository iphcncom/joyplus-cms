<?php
ob_implicit_flush(true);
ini_set('max_execution_time', '0');
require_once ("../admin_conn.php");
require_once ("collect_fun.php");
require_once ("MovieType.php");
require_once ("tools/ContentManager.php");
//chkLogin();

//$sql = "SELECT url.u_id as u_id, url.u_weburl as u_weburl, p.p_id as p_id, m.m_urltest as m_urltest, m.m_type as m_type,
//    	        m.m_typeid as m_typeid 
//    	        FROM {pre}cj_vod_url url, {pre}cj_vod_projects p, {pre}cj_vod m
//                WHERE m.m_id = url.u_movieid AND m.m_pid = p.p_id  AND (url.iso_video_url IS NUaL OR url.iso_video_url ='') and m.m_id=13079";
//
////parseVideoUrlsByMovieId('13079');
//$p_ids = be("all","p_id");
////parseVideoTypes($p_ids);
//if(!isN($p_ids)){
//parseVideoUrlsByProjectIdNullUrls($p_ids);
//}
function parseVideoUrlsByMovieId($movieid){
	$sql = "SELECT url.u_id as u_id, url.u_weburl as u_weburl, p.p_id as p_id, m.m_urltest as m_urltest 
    	        FROM {pre}cj_vod_url url, {pre}cj_vod_projects p, {pre}cj_vod m
                WHERE m.m_id = url.u_movieid AND m.m_pid = p.p_id and m.m_id=".$movieid;
	parseVideoUrls($sql);
}

function parseVideoUrlsByNullVideoUrls(){
	$sql = "SELECT url.u_id as u_id, url.u_weburl as u_weburl, p.p_id as p_id, m.m_urltest as m_urltest 
    	        FROM {pre}cj_vod_url url, {pre}cj_vod_projects p, {pre}cj_vod m
                WHERE m.m_id = url.u_movieid AND m.m_pid = p.p_id  AND (url.iso_video_url IS NULL OR url.iso_video_url ='') ";
	parseVideoUrls($sql);
}

function parseVideoUrlsByProjectId($pid){
	$sql = "SELECT url.u_id as u_id, url.u_weburl as u_weburl, p.p_id as p_id, m.m_urltest as m_urltest 
    	        FROM {pre}cj_vod_url url, {pre}cj_vod_projects p, {pre}cj_vod m
                WHERE m.m_id = url.u_movieid AND m.m_pid = p.p_id and p.p_id=".$pid;
	parseVideoUrls($sql);
}

function parseVideoUrlsByProjectIdNullUrls($pid){
	$sql = "SELECT url.u_id as u_id, url.u_weburl as u_weburl, p.p_id as p_id, m.m_urltest as m_urltest 
    	        FROM {pre}cj_vod_url url, {pre}cj_vod_projects p, {pre}cj_vod m
                WHERE m.m_id = url.u_movieid AND m.m_pid = p.p_id AND (url.iso_video_url IS NULL OR url.iso_video_url ='') and p.p_id=".$pid;
	parseVideoUrls($sql);
}

function parseVideoAndroidUrlsByProjectIdNullUrls($pid){
	$sql = "SELECT url.u_id as u_id, url.u_weburl as u_weburl, p.p_id as p_id, m.m_urltest as m_urltest 
    	        FROM {pre}cj_vod_url url, {pre}cj_vod_projects p, {pre}cj_vod m
                WHERE m.m_id = url.u_movieid AND m.m_pid = p.p_id AND (url.android_vedio_url IS NULL OR url.android_vedio_url ='') and p.p_id=".$pid;
	parseVideoUrls($sql);
}

    function parseVideoUrls($sql){
    	global $db;
    	writetofile("parseVideo.txt", $sql);
    	$rs = $db->query($sql);
	    $rscount = $db -> num_rows($rs);
	    if($rscount==0){		
			errmsg ("没有可用的数据");
	    }else {
	    	while ($row = $db ->fetch_array($rs))	{
	    		$u_id=$row["u_id"];
	    		$u_weburl=$row["u_weburl"];
	    		$p_id=$row["p_id"];
	    		$m_urltest=$row["m_urltest"];
	    		if(isN($u_weburl)){
	    			$u_weburl=$m_urltest;
	    		}
	    		
	    		$project = getProgject($p_id);
	    		$webCode = getPage($u_weburl,$project->p_coding);	
	    		$videoUrl = crawleVideoByPlayUrl($webCode,$project);
	    		$androidUrl = ContentProviderFactory::getContentProvider($project->p_playtype)->parseAndroidVideoUrlByContent($webCode, $project->p_coding, $project->p_script);
				writetofile("android_log.txt", $strlink.'{===}'.$androidUrl);
	    		if(!isN($videoUrl)){
	    			$sql = "update {pre}cj_vod_url set iso_video_url='".$videoUrl."', u_weburl='".$u_weburl."',android_vedio_url ='".$androidUrl."' where u_id=".$u_id;
			        writetofile("parseVideo.txt", $sql);
			        $db->query($sql);
	    		}else {
	    			writetofile("videoUrlErrors.txt", '{===}'.$p_id.'{===}'.$u_id.'{===}'.$u_weburl);
	    		}	    		
	    	}
	    }
	    unset($rs);
    }
    
    function getVideoUrlByProjectAndUrl($url,$pid){
//    	global $db;
    	$project = getProgject($pid);
	    $webCode = getPage($url,$project->p_coding);	
	    $videoUrl = ContentProviderFactory::getContentProvider($project->p_playtype)->parseIOSVideoUrlByContent($webCode, $project->p_coding, $project->p_script);
	    $androidUrl = ContentProviderFactory::getContentProvider($project->p_playtype)->parseAndroidVideoUrlByContent($webCode, $project->p_coding, $project->p_script);
		if(!isN($videoUrl) || !isN($androidUrl) ){
	    	return $videoUrl.MovieType::VIDEO_SEP_VERSION.$androidUrl;
	    }else {
	    	return "Can't find";
	    }
    }
    
    function parseVideoTypes($p_ids){
    	global $db;
    	$sql = "SELECT m.m_pid as p_id, m.m_urltest as m_urltest, m.m_id as m_id 
    	        FROM   {pre}cj_vod m where m.m_pid=".$p_ids
                ;
//               AND (url.iso_video_url IS NUaL OR url.iso_video_url ='') ";
         writetofile("parseVideoTypes.txt", $sql);
         echo 'start parse '.$p_ids;
//         var_dump($sql);
    	$rs = $db->query($sql);
	    $rscount = $db -> num_rows($rs);
//	    var_dump($rscount);
	    if($rscount==0){		
			errmsg ("没有可用的数据");
	    }else {
	    	while ($row = $db ->fetch_array($rs))	{
	    		$movieid=$row["m_id"];
	    		$p_id=$row["p_id"];
	    		$m_urltest=$row["m_urltest"];
	    		if(!isN($m_urltest)){
	    		  $project = getProgject($p_id);	    		
	    		  updateType($m_urltest,$project,$movieid,$p_id);
	    		} 		
	    	}
	    }
	    unset($rs);
	    echo 'end parse '.$p_ids;
    }
    
    
    
	function crawleVideoByPlayUrl($webCode,$project){
	    				
		 /**
		  * get video url 
		  */
		 if($project->p_videocodeType ==3){ //api pptv/tudou
			$videoUrlParam = getBody($webCode,$project->p_videocodeApiUrlParamstart,$project->p_videocodeApiUrlParamend);
			$videoUrlParam = replaceLine($videoUrlParam);
			$p_videoUrlApi = replaceStr($project->p_videocodeApiUrl,"{PROD_ID}",$videoUrlParam);
			$videoUrlApiCode =getPageWindow($p_videoUrlApi,$project->p_coding);			
			$videoAddressUrl = getBody($videoUrlApiCode,$project->p_videourlstart,$project->p_videourlend);
			$videoAddressUrl=getHrefFromLink($videoAddressUrl);
		 }else if($project->p_videocodeType ==2){//直接构造 youku
			$videoUrlParam = getBody($webCode,$project->p_videourlstart,$project->p_videourlend);
			$videoAddressUrl = replaceStr($project->p_videocodeApiUrl,"{PROD_ID}",$videoUrlParam);
			if(strpos($videoAddressUrl, MovieType::VIDEO_SEP_VERSION) !==false){
				$videoAddressUrls=MovieType::TOP_CLEAR.MovieType::VIDEO_NAME_URL_SEP.replaceStr($videoAddressUrl,MovieType::VIDEO_SEP_VERSION,MovieType::TOP_CLEAR).MovieType::VIDEO_SEP_VERSION;
			    $videoAddressUrls=$videoAddressUrls.MovieType::HIGH_CLEAR.MovieType::VIDEO_NAME_URL_SEP.replaceStr($videoAddressUrl,MovieType::VIDEO_SEP_VERSION,MovieType::HIGH_CLEAR).MovieType::VIDEO_SEP_VERSION;
			    $videoAddressUrls=$videoAddressUrls.MovieType::NORMAL.MovieType::VIDEO_NAME_URL_SEP.replaceStr($videoAddressUrl,MovieType::VIDEO_SEP_VERSION,MovieType::NORMAL);
			    $videoAddressUrl=$videoAddressUrls;
			}
		 }else if($project->p_videocodeType ==1){  //Base64Decode 获得视频地址 ,letv
			$videoUrlParam = getBody($webCode,$project->p_videourlstart,$project->p_videourlend);	
		 if(!isN($videoUrlParam)){					
		    $videoUrls = explode("\",\"", $videoUrlParam);
		    if(isset($videoUrls) && is_array($videoUrls)){
			  if(count($videoUrls)==3){
			  	$videoAddressUrl=MovieType::TOP_CLEAR.MovieType::VIDEO_NAME_URL_SEP.base64_decode($videoUrls[2]).MovieType::VIDEO_SEP_VERSION;
			  	$videoAddressUrl=$videoAddressUrl.MovieType::HIGH_CLEAR.MovieType::VIDEO_NAME_URL_SEP.base64_decode($videoUrls[1]).MovieType::VIDEO_SEP_VERSION;
			  	$videoAddressUrl=$videoAddressUrl.MovieType::NORMAL.MovieType::VIDEO_NAME_URL_SEP.base64_decode($videoUrls[0]);
			  }else if(count($videoUrls)==2){
			  	if(!isN($videoUrls)){
			  	  $videoAddressUrl=MovieType::HIGH_CLEAR.MovieType::VIDEO_NAME_URL_SEP.base64_decode($videoUrls[1]).MovieType::VIDEO_SEP_VERSION;
			  	  $videoAddressUrl=$videoAddressUrl.MovieType::NORMAL.MovieType::VIDEO_NAME_URL_SEP.base64_decode($videoUrls[0]);
			  	}else {
			  		$videoAddressUrl=$videoAddressUrl.MovieType::HIGH_CLEAR.MovieType::VIDEO_NAME_URL_SEP.base64_decode($videoUrls[0]);
			  	}
			  	
			  }else if(count($videoUrls)==1){
			  	$videoAddressUrl=MovieType::HIGH_CLEAR.MovieType::VIDEO_NAME_URL_SEP.base64_decode($videoUrls[0]);
			  }
		   }
	      }
		    
		 }else {
		 	$videoAddressUrl="";
		 }
		 
		 return $videoAddressUrl;
	}
	
	function getProgject($p_id){
		global $db;
		$sql = "select * from {pre}cj_vod_projects where p_id=".$p_id;
		$row= $db->getRow($sql);
		$proejct = new ProjectVO();
		$proejct->p_playlinktype = $row["p_playlinktype"];
		$proejct->p_videocodeApiUrl= $row["p_videocodeApiUrl"];
		$proejct->p_videocodeApiUrlParamstart= $row["p_videocodeApiUrlParamstart"];
		$proejct->p_videocodeApiUrlParamend= $row["p_videocodeApiUrlParamend"];
		$proejct->p_videourlstart= $row["p_videourlstart"];
		$proejct->p_videourlend= $row["p_videourlend"];
		$proejct->p_videocodeType= $row["p_videocodeType"];
		//api start
		$proejct->playcodeApiUrl =$row["p_playcodeApiUrl"] ; 
		$proejct->playcodeApiUrltype= $row["p_playcodeApiUrltype"] ;
		$proejct->p_playcodeApiUrlParamend = $row["p_playcodeApiUrlParamend"] ;
		$proejct->playcodeApiUrlParamstart=  $row["p_playcodeApiUrlParamstart"] ;
		if (isN($proejct->playcodeApiUrltype)) { $proejct->playcodeApiUrltype = 0;}
		if (isN($proejct->p_videocodeType)) { $proejct->p_videocodeType = 0;}
		$proejct->p_coding = $row["p_coding"];
		
	   $proejct->p_classtype=$row["p_classtype"];
	   $proejct->p_typestart = $row["p_typestart"];
	   $proejct->p_typeend= $row["p_typeend"];
	   $proejct->p_collect_type= $row["p_collect_type"];
	   $proejct->p_script = $row["p_script"];
	   $proejct->p_playtype=$row["p_playtype"];
		unset($row);
		return $proejct;
	}
	
	class ProjectVO {
	   public $p_playlinktype;
	   public $p_videocodeApiUrl;
	   public $p_videocodeApiUrlParamstart;
	   public $p_videocodeApiUrlParamend;
	   public $p_videourlstart;
	   public $p_videourlend;
	   public $p_videocodeType;
	   public $playcodeApiUrl;
	   public $p_coding;
	   public $playcodeApiUrltype;
	   public $p_playcodeApiUrlParamend;
	   public $playcodeApiUrlParamstart;
	   public $p_classtype;
	   public $p_typestart;
	   public $p_typeend;
	   public $p_collect_type;
	   public $p_script;
	   public $p_playtype;
	   
	}

?>