<?php
ob_implicit_flush(true);
ini_set('max_execution_time', '0');
require_once ("../admin_conn.php");
require_once ("collect_fun.php");
require_once ("MovieType.php");
require_once ("Project.php");
require_once ("tools/ContentManager.php");


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
	    		$videoUrl = ContentProviderFactory::getContentProvider($project->p_playtype)->parseIOSVideoUrlByContent($webCode, $project->p_coding, $project->p_script);
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
    
   
    
    
  
	
	

?>