<?php
ob_implicit_flush(true);
ini_set('max_execution_time', '0');
require_once ("../admin_conn.php");
require_once ("collect_fun.php");
require_once ("../score/DouBanParseScore.php");

 $pagenum = be("all", "pagenum");
 
 if (!isNum($pagenum)){ $pagenum = 1;} else { $pagenum = intval($pagenum);}

updateVodPic($pagenum);

function updateBangDan($pagenum){
	global $db;
     $scoreDouban = new DouBanParseScore();
	 $sql = "SELECT count(*)  FROM {pre}vod ,{pre}vod_topic_items where vod_id=d_id and (d_pic_ipad IS NULL OR d_pic_ipad = '' ) " ;
	 $nums = $db->getOne($sql);  
	 $app_pagenum=10; 
	 $pagecount=ceil($nums/$app_pagenum);
     for($i=$pagenum;$i<=$pagecount;$i++){
	 	writetofile("updateVodPic.txt", 'check item for vod type{=}'.$nums .'{=}Total{=}'.$pagecount.'{=}'.$i);
	     $sql = 'SELECT  d_name,d_area, d_year,d_id,d_type 
      FROM {pre}vod ,{pre}vod_topic_items where vod_id=d_id and (d_pic_ipad IS NULL OR d_pic_ipad = \'\'  )  order by d_id  limit '.($app_pagenum * ($i-1)) .",".$app_pagenum;
	   $rs = $db->query($sql); 
	    parseVodPad($rs,$scoreDouban);
	    unset($rs);
	   sleep(60);
	 }
}

function updateVodPic($pagenum){	
     global $db;
     $scoreDouban = new DouBanParseScore();
	 $sql = "SELECT count(*)  FROM {pre}vod WHERE (d_pic_ipad IS NULL OR d_pic_ipad = '' )and d_type in (1,2)" ;
	 $nums = $db->getOne($sql);  
	 $app_pagenum=10; 
	 $pagecount=ceil($nums/$app_pagenum);
//	 $pagecount=2;
	 for($i=$pagenum;$i<=$pagecount;$i++){
	 	writetofile("updateVodPic.txt", 'check item for vod type{=}'.$nums .'{=}Total{=}'.$pagecount.'{=}'.$i);
	    $sql = "SELECT d_name,d_area, d_year,d_id,d_type  FROM {pre}vod WHERE (d_pic_ipad IS NULL OR d_pic_ipad = '') and d_type in (1,2) order by d_type asc limit ".($app_pagenum * ($i-1)) .",".$app_pagenum;
//	    var_dump($sql);
	    $rs = $db->query($sql); 
	    parseVodPad($rs,$scoreDouban);
	    unset($rs);
	    sleep(60);
	 }
}


    function parseVodPad($rs,$scoreDouban){
    	global $db;
        while ($row = $db ->fetch_array($rs))	{
    		$name=$row["d_name"];$area=$row["d_area"]; $year=$row["d_year"];
			$d_id=$row["d_id"];$type=$row["d_type"];  				
		     $pic= $scoreDouban->getDouBanPics($name, $year, $area,7/5);
		     if($pic !==false && !isN($pic)){
		     	$padPic= explode("{Array}", $pic);
		     	if(count($padPic)>0){
		     		$padPic=$padPic[0];
		     		writetofile("updateVodPic.txt", 'd_pic_ipad{=}'.$padPic .'{=}d_pic_ipad_tmp{=}'.$pic);
		     		$db->Update ("{pre}vod", array("d_pic_ipad","d_pic_ipad_tmp"), array($padPic,$pic), "d_id=" . $d_id);	
		     	}
		     }  		
	    }
	    unset($rs);
    }
    
    
   
    
    
  
	
	

?>