<?php
ob_implicit_flush(true);
ini_set('max_execution_time', '0');
require_once ("../admin_conn.php");
require_once ("collect_fun.php");
require_once ("../score/AutoDouBanParseScore.php");

 $pagenum = be("all", "pagenum");
 
 if (!isNum($pagenum)){ $pagenum = 1;} else { $pagenum = intval($pagenum);}

updateVodPic($pagenum);

writetofile("mac_cj_vod.txt", 'finish');

function updateVodPic($pagenum){	
     global $db;
     $scoreDouban = new AutoDouBanParseScore();
	 $sql = "SELECT count(*)  FROM ( SELECT m_name
FROM mac_cj_vod
WHERE m_pid =180 AND m_typeid =131
GROUP BY m_name) as c" ;
	 $nums = $db->getOne($sql);  
	 $app_pagenum=10; 
	 $pagecount=ceil($nums/$app_pagenum);
//	 $pagecount=2;
$flag=true;
	 for($i=$pagenum;$i<=$pagecount && $flag;$i++){
	 	writetofile("mac_cj_vod.txt", 'check item for vod type{=}'.$nums .'{=}Total{=}'.$pagecount.'{=}'.$i);
	    $sql = "SELECT m_name, m_pic, m_pic_ipad, m_year,m_language 
FROM mac_cj_vod
WHERE m_pid =180 
AND m_typeid =131
GROUP BY m_name order by m_name asc  limit ".($app_pagenum * ($i-1)) .",".$app_pagenum;
//	    var_dump($sql);
	    $rs = $db->query($sql); 
	   $flag= parseVodPad($rs,$scoreDouban);
	    unset($rs);
	    sleep(60);
	    if(!flag){
	    	writetofile("mac_cj_vod.txt", 'You API access rate limit check item for vod type{=}'.$nums .'{=}Total{=}'.$pagecount.'{=}'.$i);
	    }
	 }
}


    function parseVodPad($rs,$scoreDouban){
    	global $db;
        while ($row = $db ->fetch_array($rs))	{
    		$names=$row["m_name"];$m_pic=$row["m_pic"]; $year=$row["m_year"];
			$m_pic_ipad=$row["m_pic_ipad"]; 
			$m_language	=$row["m_language"]; 
			$name=$names;
			if(!isN($m_language)){
				$name=trim(replaceStr($name, $m_language, ''));
			}
            writetofile("mac_cj_vod.txt", 'name{=}'.$name );
			$flag=false;
			 
			 if(isN($m_pic)){			   
			 	$pic= $scoreDouban->getDoubanThumb($name, $year, '');
			 	if($pic !==false) {
			     	$doubanid=$pic['id'];
			     	$pic=$pic['pic'];
			     	 writetofile("mac_cj_vod.txt", 'doubanid{=}'.$doubanid );
			     	 writetofile("mac_cj_vod.txt", 'pic{=}'.$pic );
			     }else {
			     	writetofile("mac_cj_vod.txt", 'You API access rate limit');
			     	return false;
			     }
			     $sql = 'update mac_cj_vod set ';
			     if($pic !==false && !isN($pic)){
			     	$flag=true;
			     	$sql =$sql ." m_pic='".$pic."', ";		     	
			     } 
			 }
			
		     if(!isN($doubanid) && $doubanid ==='-1'){
		     	writetofile("mac_cj_vod_not_find.txt", $name );
		     }
		     if(isN($m_pic_ipad)){
	             if(!isN($doubanid) && $doubanid !==0 && $doubanid !=='0' ){
	             	 writetofile("mac_cj_vod.txt", 'scoreDouban{=}'.$doubanid );
			       $padpic= $scoreDouban->getPicById($doubanid,7/5);
			       
				 }else {
				 	$padpic= $scoreDouban->getDouBanPics($name, $year, '',7/5);
				 }
				 if($padpic !==false) {
			     	$padpic=$padpic['pic'];
			     }
			      writetofile("mac_cj_vod.txt", 'padpic{=}'.$padpic );
			      if($padpic !==false && !isN($padpic)){
			     	$padpic= explode("{Array}", $padpic);
			     	if(count($padpic)>0){
			     		$padPic=$padpic[0];
			     		writetofile("mac_cj_vod.txt", '2 padpic{=}'.$padPic );
			     		$flag=true;
			     		$sql =$sql ." m_pic_ipad='".$padPic."', ";		
			     	}
			     } 
		     }
			 
            

		    $sql =$sql ." where m_name='".$names."' AND m_pid =180 AND m_typeid =131 and  m_year='".$year."'   ";	 
		     writetofile("mac_cj_vod.txt", 'sql{=}'.$sql );
		     
		     if($flag){
		     	$db->query($sql);
		     }

	    }
	    
	    return true;
	   
    }
    
    
   
    
    
  
	
	

?>