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
	 $sql = "SELECT count(*)  FROM {pre}vod WHERE  d_id not in (SELECT DISTINCT content_id FROM tbl_comments WHERE author_id IS NULL AND comment_type=1) and d_type in (1,2) and d_douban_id !=-1" ;
	 $nums = $db->getOne($sql);  
	 $app_pagenum=10; 
	 $pagecount=ceil($nums/$app_pagenum);
//	 $pagecount=2;
	 for($i=$pagenum;$i<=$pagecount;$i++){
	 	writetofile("updateVodThumb.txt", 'check item for vod type{=}'.$nums .'{=}Total{=}'.$pagecount.'{=}'.$i);
	    $sql = "SELECT d_name,d_area, d_year,d_id,d_type, d_douban_id FROM {pre}vod WHERE d_id not in (SELECT DISTINCT content_id FROM tbl_comments WHERE author_id IS NULL AND comment_type=1)  and d_type in (1,2,131)  and d_douban_id !=-1 order by d_time desc, d_type asc limit ".($app_pagenum * ($i-1)) .",".$app_pagenum;
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
            writetofile("updateVodReviews.txt", 'sssdoubanid{=}'.$doubanid );
			 $flag=true;
			 
			 if(!isN($doubanid) && $doubanid !==0 && $doubanid !=='0' ){
			   $flag=false;writetofile("updateVodReviews.txt",'getThumb:'.$name);
		       $comments= $scoreDouban->getReviewsById($doubanid);
			 }else {
			 	 writetofile("updateVodReviews.txt",'getDoubanThumb:'.$name);
			 	$comments= $scoreDouban->getDoubanReviews($name, $year, $area);
			 }
			 
             if($comments !==false) {
			     	$doubanid=$comments['id'];
			     	 writetofile("updateVodReviews.txt",'getDoubanThumb:'.$doubanid);
			     	if(!isN($doubanid) && $flag){
			     		$db->Update ("{pre}vod", array("d_douban_id"), array($doubanid), "d_id=" . $d_id);
			     	}
			 }
			 
             if(is_array($comments)&& !isN( $comments['comments'])){			 
			 	$commentsArray = $comments['comments'];
			 	$titlesArray =$comments['title'];		 	
			 	$reviewidsArray= $comments['reviewid'];
			 	$total= count($commentsArray);
			 	if($total>0){
			 		$db->Delete("tbl_comments", "content_id=".$d_id ." and author_id is null and comment_type=1");
			 		//$db->Delete("mac_comment", "c_vid=".$d_id );
			 	}
			 	
			 	for ($i=0;$i<$total;$i++) {
			 		$com=$commentsArray[$i];
			 		
			 		$title=$titlesArray[$i];
			 		$reviewid=$reviewidsArray[$i];
			 		if(!isN($com)){
			 		  $com=filterScript($com,8191);
			 		  $db->Add("tbl_comments", array("status","content_type","content_name","content_id","create_date","comments","comment_type","douban_comment_id","comment_title"),
			 		  array('1',$type,$name,$d_id,date("Y-m-d H:i:s"),$com,'1',$reviewid,$title));
			 		  
			 		  $db->Add("mac_comment", array("c_audit","c_type","c_vid","c_time","c_content","c_name"),
			 		  array('1',$type,$d_id,date("Y-m-d H:i:s"),$com,$author));
			 		  
			 		}
			 	}
				 
				 }

	    }
	    unset($rs);
    }
    
    
   
    
    
  
	
	

?>