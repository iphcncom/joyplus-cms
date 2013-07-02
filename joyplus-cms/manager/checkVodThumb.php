<?php
require_once ("admin_conn.php");
require_once ("collect/tools/ContentManager.php");


//chkLogin();

$type = be("all","type");
if(!isNum($type)) {
//	topicVideo();
//	bandanVideo();
//	lunboVideo();
} else { 
	$type = intval($type);
	videoType($type);
}


dispseObj();

function videoType($type){
	writetofile("checkVod.txt", 'Start to check item for vod type{=}'.$type);
	global $db,$template,$cache;
	 $sql = "SELECT count(*)  FROM {pre}vod where d_topic=0 and  d_hide !=-100 and d_play_check=0 and  d_type=".$type ;
	 $nums = $db->getOne($sql);  
	 $app_pagenum=20; 
	 $pagecount=ceil($nums/$app_pagenum);
	 for($i=1;$i<=$pagecount;$i++){
	 	writetofile("checkVod.txt", 'check item for vod type{=}'.$type .'{=}Total{=}'.$pagecount.'{=}'.$i);
	    $sql = "SELECT d_id,d_name,d_playfrom,webUrls 
	    FROM {pre}vod where d_topic=0 and  d_hide !=-100 and d_play_check=0 and d_type=".$type ."  limit ".($app_pagenum * ($i-1)) .",".$app_pagenum;  
	      
	    $rs = $db->query($sql); 
	    checkWebUrls($rs);
	    unset($rs);
	 }
    writetofile("checkVod.txt", 'End to check item  for vod type{=}'.$type);
}

function topicVideo(){
	writetofile("checkVod.txt", 'Start to check item for topicVideo');
	global $db,$template,$cache;
    $sql = "SELECT d_id,d_name,d_playfrom,webUrls 
    FROM {pre}vod where d_topic!=0 and  d_hide !=-100 and d_play_check=0";//  limit ".($app_pagenum * ($pagenum-1)) .",".$app_pagenum;    
    $rs = $db->query($sql); 
    checkWebUrls($rs);
	    unset($rs);
    writetofile("checkVod.txt", 'End to check item for topicVideo');
}

function bandanVideo(){
	writetofile("checkVod.txt", 'Start to check item for topic');
	global $db,$template,$cache;
    $sql = "SELECT d_id,d_name,d_playfrom,webUrls 
    FROM {pre}vod ,{pre}vod_topic_items where vod_id=d_id and d_hide !=-100 and d_play_check=0";//  limit ".($app_pagenum * ($pagenum-1)) .",".$app_pagenum;    
    $rs = $db->query($sql); 
    checkWebUrls($rs);
	    unset($rs);
    writetofile("checkVod.txt", 'End to check item for topic');
}

function lunboVideo(){
	global $db,$template,$cache;
	writetofile("checkVod.txt", 'Start to check item for lunbo');
    $sql = "SELECT d_id,d_name,d_playfrom,webUrls 
    FROM {pre}vod ,{pre}vod_popular where vod_id=d_id and d_hide !=-100 and d_play_check=0";//  limit ".($app_pagenum * ($pagenum-1)) .",".$app_pagenum;    
    $rs = $db->query($sql); 
    checkWebUrls($rs);
	    unset($rs);
    writetofile("checkVod.txt", 'end to check item for lunbo');
}

function checkWebUrls($rs){
	global $db,$template,$cache;
	 $ids='';
	$flag=false;
    while ($row = $db ->fetch_array($rs)){
		$url=$row['webUrls'];
		$id=$row['d_id'];
		$d_playfrom=$row['d_playfrom'];		
		$d_playfrom=explode("$$$", $d_playfrom);
		$d_playfrom=$d_playfrom[0];		
		$url=getFirstWeburl($url);
		if(!ContentProviderFactory::checkHtmlCanPlay($d_playfrom, $url)){
//			writetofileNoAppend("checkVoditem.txt",$id.'{===}'.$row['d_name']);
			writetofile("checkVod.txt",$id.'{===}'.$row['d_name'].' can\'t play');
			if($flag){
			  $ids=','.$ids;
			}
			$flag=true;
			$ids=$id.$ids;
		}else {
//			writetofile("checkVod.txt",$id.'{===}'.$row['d_name'].' can play');
		}
    }
    unset($rs);
    if(isset($ids) && !is_null($ids) && strlen($ids)>0){
    	 $db->query('update {pre}vod set d_hide=-100, d_play_check=2 where d_id in ('.$ids.')');
    } 
}

function getFirstWeburl($weburls){
	$cur_url='';
	if(isset($weburls) && !is_null($weburls)){
		$weburlsA = explode("{Array}", $weburls);		
		if(isset($weburlsA) && is_array($weburlsA)  && count($weburlsA)>0){
			$temp= $weburlsA[0];
			if(isset($temp) && !is_null($temp)){
				$tempA = explode('$', $temp);
				if(count($tempA)==2){
					$cur_url=$tempA[1];
				}
				if(count($tempA)==1){
					$cur_url=$tempA[0];
				}
			}
		}
	}
	
	return $cur_url;
}


?>
</body>
</html>