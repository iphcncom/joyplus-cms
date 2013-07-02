<?php
ob_implicit_flush(true);
ini_set('max_execution_time', '0');
require_once ("../admin_conn.php");
require_once ("collect_fun.php");
require_once ("../score/AutoDouBanParseScore.php");

 $pagenum = be("all", "pagenum");
 
 if (!isNum($pagenum)){ $pagenum = 1;} else { $pagenum = intval($pagenum);}

updateVodPic($pagenum);



function updateVodPic($pagenum){	
     global $db;
     $scoreDouban = new AutoDouBanParseScore();
	 $sql = "SELECT count(*)  FROM {pre}vod WHERE (d_pic IS NULL OR d_pic = '' ) and d_type in (1,2,131) and d_douban_id !=-1" ;
	 $nums = $db->getOne($sql);  
	 $app_pagenum=10; 
	 $pagecount=ceil($nums/$app_pagenum);
//	 $pagecount=2;
	 for($i=$pagenum;$i<=$pagecount;$i++){
	 	writetofile("updateVodThumb.txt", 'check item for vod type{=}'.$nums .'{=}Total{=}'.$pagecount.'{=}'.$i);
	    $sql = "SELECT d_name,d_area, d_year,d_id,d_type, d_douban_id FROM {pre}vod WHERE (d_pic IS NULL OR d_pic = '') and d_type in (1,2,131)  and d_douban_id !=-1 order by d_type asc limit ".($app_pagenum * ($i-1)) .",".$app_pagenum;
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
            $doubanid=$row['d_douban_id'];
            writetofile("updateVodThumb.txt", 'sssdoubanid{=}'.$doubanid );
			 $flag=true;
			 
			 if(!isN($doubanid) && $doubanid !==0 && $doubanid !=='0' ){
			   $flag=false;
			   writetofile("updateVodThumb.txt",'getThumb:'.$name);
		       $pic= $scoreDouban->getThumb($doubanid);
			 }else {
			 	writetofile("updateVodThumb.txt",'getDoubanThumb:'.$name);
			 	$pic= $scoreDouban->getDoubanThumb($name, $year, $area);
			 }
			
		     if($pic !==false) {
		     	$doubanid=$pic['id'];
		     	writetofile("updateVodThumb.txt", 'doubanid{=}'.$doubanid );
		     	if(!isN($doubanid) && $flag){
		     		$db->Update ("{pre}vod", array("d_douban_id"), array($doubanid), "d_id=" . $d_id);
		     	}
		     	$pic=$pic['pic'];
		     }
		     
		     if($pic !==false && !isN($pic)){
		     	writetofile("updateVodThumb.txt", 'd_pic{=}'.$pic );
		     	$db->Update ("{pre}vod", array("d_pic"), array($pic), "d_id=" . $d_id);	
		     }  

	    }
	    unset($rs);
    }
    
    
   
    
    
  
	
	

?>