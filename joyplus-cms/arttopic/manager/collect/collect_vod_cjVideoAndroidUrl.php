<?php
ob_implicit_flush(true);
ini_set('max_execution_time', '0');
require_once ("../admin_conn.php");
require_once ("collect_fun.php");
require_once ("MovieType.php");
require_once ("tools/ContentManager.php");
$p_ids = be("all","p_id");
//parseVideoTypes($p_ids);
//echo $p_ids;

parseVideoUrlsByProjectIdNullUrls($p_ids);

function parseVideoUrlsByMovieId($movieid){
	$sql = "SELECT url.u_id as u_id, url.u_weburl as u_weburl, p.p_id as p_id, m.m_urltest as m_urltest 
    	        FROM {pre}cj_vod_url url, {pre}cj_vod_projects p, {pre}cj_vod m
                WHERE m.m_id = url.u_movieid AND m.m_pid = p.p_id and m.m_id=".$movieid;
	parseVideoUrls($sql);
}

function parseVideoUrlsByNullVideoUrls(){
	$sql = "SELECT url.u_id as u_id, url.u_weburl as u_weburl, p.p_id as p_id, m.m_urltest as m_urltest 
    	        FROM {pre}cj_vod_url url, {pre}cj_vod_projects p, {pre}cj_vod m
                WHERE m.m_id = url.u_movieid AND m.m_pid = p.p_id  AND (url.android_vedio_url IS NULL OR url.android_vedio_url ='') ";
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
                WHERE m.m_id = url.u_movieid AND m.m_pid = p.p_id AND (url.android_vedio_url IS NULL OR url.android_vedio_url ='') and p.p_id=".$pid;
	parseVideoUrls($sql);
}

    function parseVideoUrls($sql){
    	global $db;
    	writetofile("parseVideoAndroid.txt", $sql);
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
	    		$videoUrl = crawleVideoByPlayUrl($u_weburl,$project);	    		
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
    
    
    
	function crawleVideoByPlayUrl($url,$project){
	     $webCode = getPage($url,$project->p_coding);					
		 $androidUrl = ContentProviderFactory::getContentProvider($project->p_playtype)->parseAndroidVideoUrlByContent($webCode, $project->p_coding, $project->p_script);
		 writetofile("android_log.txt", $strlink.'{===}'.$androidUrl);		 
		 return $androidUrl;
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
	   
	}

?>