<?php
require_once (dirname(__FILE__)."/../admin_conn.php");
require_once (dirname(__FILE__)."/collect_fun.php");
require_once (dirname(__FILE__)."/MovieType.php");
require_once (dirname(__FILE__)."/SinaTeachParse.php");
require_once (dirname(__FILE__)."/NeteaseTeachParse.php");
$p_id = be("all","p_id"); 
if (isN($p_id)) { 
	writetofile("crawel_api_info.log", $p_id.'采集提示","采集项目ID不能为空!'); 
	return;
}else{
	for ($page=1;$page<100;$page++){
		$results =false;
		if($p_id ==='184'){
		    $results = SinaTeachParse::parseMovieInfoPage($page, "utf-8");
			if($results !== false){
				foreach ($results as $result){
					$result->p_id=$p_id;
					cjAPIView($result);
				}
			}
		}
		else if($p_id ==='183'){
		  $results = NeteaseTeachParse::parseMovieInfoPage($page, "utf-8");
		  var_dump($results);
		  if($results !== false){
		    foreach ($results as $result){
				$result->p_id=$p_id;
				cjAPIView($result);
			}
		  }
		  break;		  
		}
	}
}



 

function cjAPIView($info){
	
	    $strlink =  $info->videoUrl;
	    $p_id =  $info->p_id;
		$titlecode = $info->title;
		$titlecode = filterScript($titlecode,$p_script);
		$titlecode = replaceFilters($titlecode,$p_id,1,0);
		$titlecode = replaceStr(replaceStr(replaceStr($titlecode,","," "),"'",""),"\"\"","");
		$titlecode = trim($titlecode);
		
	    $lzcode = $info->curr_episode;
		$lzcode = replaceStr($lzcode,"false","0");
		$lzcode = trim($lzcode);
		try{
		  $lzcode = intval($lzcode);
		}catch(Exception $e){
			$lzcode=0;
		}
		
		//演员
		$starringcode = $info->actor;
		$piccode = $info->big_poster;
		//图片
		$piccode = trim($piccode);
	   
		$m_typeid =$info->typeid;
		
		$typecode=!isN($info->type)?$info->type:"其他";;
		$typecode = filterScript($typecode,$p_script);
		
		
		$weburl=$info->sites;
		
		if ($weburl ==false) {
				return;
		}else{
			$directedcode = "";
		    if (!isN($info->director)){
			  $directedcode = $info->director;
		    }
			//备注
			
			$remarkscode = "";
			if (!isN($info->max_episode)){
			  $remarkscode = $info->max_episode;
			}
			
			$languagecode = !isN($info->language)?$info->language:"其他";
			$languagecode = trim($languagecode);
			
			
		
			
			$areacode = !isN($info->area)?$info->area:"其他";
			$areacode = trim($areacode);
			
			
			$contentcode = !isN($info->brief)?$info->brief:"";
			$contentcode = filterScript(replaceFilters($contentcode,$p_id,2,0),$p_script);
			$contentcode = replaceStr(replaceStr(replaceStr($contentcode,","," "),"'",""),"\"\"","");
			$contentcode = trim($contentcode);
			
			$timecode  = !isN($info->pubdate)?$info->pubdate:"其他"; 
			
			$duration = !isN($info->duration)?$info->duration:"";
			$m_area = $areacode;
			$m_languageid = $languagecode;
			
			foreach ($weburl as $weburlitem){
				$p_playtypebaiduweb = $weburlitem['site_name'];
				$baiduwebUrls=$weburlitem['episodes'];
				$movieid = updateVod($duration,$baiduwebUrls,$p_id,$titlecode,$piccode,$typecode,$areacode,$strlink,$starringcode,$directedcode,$timecode,$p_playtypebaiduweb,$contentcode,$m_typeid,$lzcode,$languagecode,$remarkscode);
			  
			}		
	   }
	
}

function updateVod($duration,$baiduwebUrls,$p_id,$titlecode,$piccode,$typecode,$areacode,$strlink,$starringcode,$directedcode,$timecode,$p_playtype,$contentcode,$m_typeid,$lzcode,$languagecode,$remarkscode){
	global $db,$cg;
       $sql="select m_id,m_name,m_type,m_area,m_playfrom,m_starring,m_directed,m_pic,m_content,m_year,m_addtime,m_urltest,m_zt,m_pid,m_typeid,m_hits,m_playserver,m_state from {pre}cj_vod where m_pid='".$p_id."' and m_name='".$titlecode."'  and m_playfrom='".$p_playtype."'  order by m_id desc";
			
			$rowvod=$db->getRow($sql);
			
		    if ($rowvod) {
				$cg=$cg+1;
				$movieid=$rowvod["m_id"];
				if(isN($titlecode)){
					$titlecode = $rowvod["m_name"];
				}
		    
				if(isN($starringcode)){
					$starringcode = $rowvod["m_starring"];
				}
		    
				if(isN($piccode)){
					$piccode = $rowvod["m_pic"];
				}
				$sql = "update {pre}cj_vod  set duraning='".$duration."' , m_pic='".$piccode."', m_type='".$typecode."',m_area='".$areacode."',m_urltest='".$strlink."',m_name='".$titlecode."',m_starring='".$starringcode."',m_directed='".$directedcode."',m_year='".$timecode."',m_playfrom='".$p_playtype."',m_content='".$contentcode."',m_addtime='".date('Y-m-d H:i:s',time())."',m_zt='0',m_pid='".$p_id."',m_typeid='".$m_typeid."',m_playserver='',m_state='".$lzcode."',m_language='".$languagecode."',m_remarks='".$remarkscode."' where m_id=".$rowvod["m_id"];
				writetofile("sql.txt", $sql);
				$db->query($sql);
			}
			else{
				$cg=$cg+1;
				$sql="insert {pre}cj_vod (duraning,m_name,m_type,m_area,m_playfrom,m_starring,m_directed,m_pic,m_content,m_year,m_urltest,m_zt,m_pid,m_typeid,m_hits,m_playserver,m_state,m_addtime,m_language,m_remarks) values('".$duration."', '".$titlecode."','".$typecode."','".$areacode."','".$p_playtype."','".$starringcode."','".$directedcode."','".$piccode."','".$contentcode."','".$timecode."','".$strlink."','0','".$p_id."','".$m_typeid."','0','','".$lzcode."','".date('Y-m-d H:i:s',time())."','".$languagecode."','".$remarkscode."')";
				writetofile("sql.txt", $sql);
	 			$db->query($sql);
				$movieid= $db->insert_id();
			}
//			var_dump($baiduwebUrls);
   
			foreach ($baiduwebUrls as $baiduweburl){ 
				if(array_key_exists('url', $baiduweburl)){
				   $WebTestx = $baiduweburl['url'];	
				}else {
					continue;
				}
				
			    if(array_key_exists('img_url', $baiduweburl)){
				 $picurl = $baiduweburl['img_url'];	
				}else {
					$picurl='';
				}
						
					
				writetofile("crawel_api_info.log", $p_id.'{=====}'.$WebTestx ."{=====}ViewList===start");
				
			    
			   
			    if(array_key_exists('androidUrl', $baiduweburl)){
				   $androidUrl= $baiduweburl['androidUrl'];
				}else {
					$androidUrl='';
				}
				
			    if(array_key_exists('videoAddressUrl', $baiduweburl)){
				   $videoAddressUrl= $baiduweburl['videoAddressUrl'];
				}else {
					$videoAddressUrl='';
				}
				
				if(isN($videoAddressUrl)){
					if(array_key_exists('stream_url', $baiduweburl)){
					   $videoAddressUrl= $baiduweburl['stream_url'];
					}else {
						$videoAddressUrl='';
					}
				}
				
				if($videoAddressUrl===$androidUrl){
					$videoAddressUrl='';
				}
				 
				writetofile("android_log.txt", $WebTestx.'{===}'.$androidUrl .'{===}'.$videoAddressUrl );
			    
//			    $url = getBody($playCode,$p_playurlstart,$p_playurlend);
			    $url = "";
			    if(array_key_exists('episode', $baiduweburl)){
				   $setname= $baiduweburl['episode'];
				}else {
					$setname='';
				}
				
			    if(array_key_exists('time', $baiduweburl)){
				   $time= $baiduweburl['time'];
				}else {
					$time='';
				}
				
				if(isN($setname)){
					if(array_key_exists('name', $baiduweburl)){
					   $setname=$baiduweburl['name'];
					}
					
				}
				$setname=trim($setname);
				
			   $sql="SELECT {pre}cj_vod_url.u_url FROM ({pre}cj_vod_url INNER JOIN {pre}cj_vod ON {pre}cj_vod_url.u_movieid = {pre}cj_vod.m_id)  where {pre}cj_vod_url.u_weburl='" . $WebTestx . "' and {pre}cj_vod.m_pid=" . $p_id . " and {pre}cj_vod.m_id=" . $movieid;
			   
     		   $rowurl = $db->getRow($sql);
     		   
			    if ($rowurl) {
					 $db->Delete('{pre}cj_vod_url', "u_id=".$rowurl['u_id']);
			   }
			   
			   writetofile("sql.txt","insert into {pre}cj_vod_url(u_url,u_movieid,u_weburl,iso_video_url,name,android_vedio_url) values('".$url."','".$movieid."','".$WebTestx."','".$videoAddressUrl."','".$setname."' ,'".$androidUrl."' )");
			   $db->query("insert into {pre}cj_vod_url(pic,u_url,u_movieid,u_weburl,iso_video_url,name,android_vedio_url) values('".$picurl."','".$url."','".$movieid."','".$WebTestx."','".$videoAddressUrl."','".$setname."' ,'".$androidUrl."' )");
			}
			
}




?>