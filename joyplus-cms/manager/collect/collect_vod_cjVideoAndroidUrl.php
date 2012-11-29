<?php
ob_implicit_flush(true);
ini_set('max_execution_time', '0');
require_once ("../admin_conn.php");
require_once ("collect_fun.php");
require_once ("MovieType.php");
require_once ("tools/ContentManager.php");
require_once ("Project.php");
$p_ids = be("all","p_id");
//parseVideoTypes($p_ids);
//echo $p_ids;

recoveryWrongVideoUrl($p_ids);

function parseVideoAndroidUrlsByMovieId($movieid){
	$sql = "SELECT url.u_id as u_id, url.u_weburl as u_weburl, p.p_id as p_id, m.m_urltest as m_urltest 
    	        FROM {pre}cj_vod_url url, {pre}cj_vod_projects p, {pre}cj_vod m
                WHERE m.m_id = url.u_movieid AND m.m_pid = p.p_id and m.m_id=".$movieid;
	parseVideoAndroidUrls($sql);
}

function parseVideoAndroidUrlsByNullVideoUrls(){
	$sql = "SELECT url.u_id as u_id, url.u_weburl as u_weburl, p.p_id as p_id, m.m_urltest as m_urltest 
    	        FROM {pre}cj_vod_url url, {pre}cj_vod_projects p, {pre}cj_vod m
                WHERE m.m_id = url.u_movieid AND m.m_pid = p.p_id  AND (url.android_vedio_url IS NULL OR url.android_vedio_url ='') ";
	parseVideoAndroidUrls($sql);
}

function parseVideoAndroidUrlsByProjectId($pid){
	$sql = "SELECT url.u_id as u_id, url.u_weburl as u_weburl, p.p_id as p_id, m.m_urltest as m_urltest 
    	        FROM {pre}cj_vod_url url, {pre}cj_vod_projects p, {pre}cj_vod m
                WHERE m.m_id = url.u_movieid AND m.m_pid = p.p_id and p.p_id=".$pid;
	parseVideoAndroidUrls($sql);
}

function recoveryWrongVideoAndroidUrl($pid){
	$sql=" SELECT url.u_id as u_id, url.u_weburl as u_weburl, p.p_id as p_id, m.m_urltest as m_urltest 
    	        FROM {pre}cj_vod_url url, {pre}cj_vod_projects p, {pre}cj_vod m
                WHERE m.m_id = url.u_movieid AND m.m_pid = p.p_id AND url.android_vedio_url NOT LIKE '%http://m.youku.com/wap/pvs%' and p.p_id=".$pid;
	parseVideoAndroidUrls($sql);
	
}

function parseVideoAndroidUrlsByProjectIdNullUrls($pid){
	$sql = "SELECT url.u_id as u_id, url.u_weburl as u_weburl, p.p_id as p_id, m.m_urltest as m_urltest 
    	        FROM {pre}cj_vod_url url, {pre}cj_vod_projects p, {pre}cj_vod m
                WHERE m.m_id = url.u_movieid AND m.m_pid = p.p_id AND (url.android_vedio_url IS NULL OR url.android_vedio_url ='') and p.p_id=".$pid;
	parseVideoAndroidUrls($sql);
}
 function recoveryWrongVideoUrl($pid){
 	$sql="SELECT url.u_id AS u_id, url.u_weburl AS u_weburl, p.p_id AS p_id, m.m_urltest AS m_urltest, url.android_vedio_url
			FROM mac_cj_vod_url url, mac_cj_vod_projects p, mac_cj_vod m
			WHERE m.m_id = url.u_movieid
			AND m.m_pid = p.p_id
			AND p.p_id =".$pid."  
			AND url.android_vedio_url NOT LIKE '%http://m.youku.com/wap/pvs%' and url.android_vedio_url IS NOT NULL
AND url.android_vedio_url != ''";
 	parseVideoAndroidUrls($sql);
 }
    function parseVideoAndroidUrls($sql){
    	global $db;
    	writetofile("parseVideoAndroid.txt", $sql);
//    	var_dump($sql);
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
	    		$videoUrl = crawleAndroidVideoByPlayUrl($u_weburl,$project);	    		
	    		if(!isN($videoUrl)){
	    			$sql = "update {pre}cj_vod_url set android_vedio_url='".$videoUrl."', u_weburl='".$u_weburl."' where u_id=".$u_id;
			        writetofile("parseVideoAndroid.txt", $sql);
			        $db->query($sql);
	    		}else {
	    			writetofile("videoUrlAndroidErrors.txt", '{===}'.$p_id.'{===}'.$u_id.'{===}'.$u_weburl);
	    		}	    		
	    	}
	    }
	    unset($rs);
    }
    
    
    
	function crawleAndroidVideoByPlayUrl($url,$project){
	     $webCode = getPage($url,$project->p_coding);					
		 $androidUrl = ContentProviderFactory::getContentProvider($project->p_playtype)->parseAndroidVideoUrlByContent($webCode, $project->p_coding, $project->p_script);
		 writetofile("android_log.txt", $strlink.'{===}'.$androidUrl);		 
		 return $androidUrl;
	}
	
	

?>