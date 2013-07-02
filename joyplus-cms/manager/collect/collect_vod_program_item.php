

<?php
ob_implicit_flush(true);
ini_set('max_execution_time', '0');
require_once (dirname(__FILE__)."/../admin_conn.php");
require_once (dirname(__FILE__)."/collect_fun.php");
require_once (dirname(__FILE__)."/CnTVLiveParse.php");
require_once (dirname(__FILE__)."/TVSouLiveParse.php");

 $pagenum = be("all", "pagenum");
 
 $action = be("all", "action");
 if (!isNum($pagenum)){ $pagenum = 1;} else { $pagenum = intval($pagenum);}
 if($action ==='auto'){
    updateVodPic($pagenum);
 }


function updateVodPic($pagenum){	
     global $db;
     
	 	
	    $sql = "SELECT tv_id, tv_code, tv_playfrom
FROM mac_tv_egp_config where tv_code is not null or tv_code !=''
GROUP BY tv_id
ORDER BY tv_playfrom ASC ";
//	    var_dump($sql);
	    $rs = $db->query($sql); 
	    $lists=array();
	    while ($row = $db ->fetch_array($rs)){
	    	$list=array();
	    	$list['id']=$row['tv_id'];
	    	$list['tv_code']=$row['tv_code'];
	    	$list['tv_playfrom']=$row['tv_playfrom'];
	    	$lists[]=$list; 
	    }
//	    var_dump($lists);
         unset($rs);
	    foreach ($lists as $list) {
	      parseVodPad($list);	      
	    }	    
}


    function parseVodPad($list){
    	global $db;
        $id=$list['id'];
        $tv_code=$list['tv_code'];
        $tv_playfrom=$list['tv_playfrom'];
         $day=date('Y-m-d',time());
    $dateTime = strtotime($date) 	 ;	
  	 	$offset =date('w',time());
  	 	 for($i=1;$i<8;$i++){
		  $day= date('Y-m-d',strtotime($i-$offset.' day'))	;
		  $dayItem= $db->getRow('select * from mac_tv_program_item where tv_id='.$id .' and day=\''.$day.'\'');
		  if (!$dayItem){
		  	  if($tv_playfrom==='cntv'){
			    $result=CnTVLiveParse::crawlerProgramItems($day, $tv_code);
		  	  }else {
		  	  	 $result=TVSouLiveParse::crawlerProgramItems($day, $tv_code);
		  	  }
			  if($result !==false && is_array($result) && count($result)>0){
			  	 writetofile("program_live_item_crawler_result.log", "program items exist: channel:[".$tv_code."];day:[".$day."]");
			  	 $db->Delete("mac_tv_program_item", 'tv_id='.$id .' and day=\''.$day.'\'');		  	 
			  	 foreach (array_keys($result) as $play_time){
				 	 $video_name=$result[$play_time];
			  	 	 $program_type = $db->getOne("select program_type from mac_tv_program_type_item where program_name='".$video_name."'");
			  	 	
			  	 	 if(!$program_type){
			  	 	 	$program_type='';
			  	 	 }
			  	 	 
			  	 	 $db->Add("mac_tv_program_item", array("tv_id","day","play_time","video_name","program_type"),
				 		  array($id,$day,$play_time,$video_name,$program_type));
			    }
			  }else {
			  	writetofile("program_live_item_crawler_no_result.log", "program items exist: channel:[".$tv_code."];day:[".$day."]");
			  }
		 }else {
		 	writetofile("program_live_item_exist.log", "program items exist: channel:[".$tv_code."];day:[".$day."]");
		 }
	    }
    }
    
    
    function parseVodPadSimple($id,$tv_code,$day,$tv_playfrom){
      global $db;
      if($tv_playfrom==='cntv'){
	    $result=CnTVLiveParse::crawlerProgramItems($day, $tv_code);
  	  }else {
  	  	 $result=TVSouLiveParse::crawlerProgramItems($day, $tv_code);
  	  }
	  if($result !==false && is_array($result) && count($result)>0){
	  	 writetofile("program_live_item_crawler_result.log", "program items exist: channel:[".$tv_code."];day:[".$day."]");
	  	 $db->Delete("mac_tv_program_item", 'tv_id='.$id .' and day=\''.$day.'\'');		  	 
	  	 foreach (array_keys($result) as $play_time){
	  	 	 $video_name=$result[$play_time];
	  	 	 $program_type = $db->getOne("select program_type from mac_tv_program_type_item where program_name='".$video_name."'");
	  	 	
	  	 	 if(!$program_type){
	  	 	 	$program_type='';
	  	 	 }
	  	 	 $db->Add("mac_tv_program_item", array("tv_id","day","play_time","video_name","program_type"),
		 		  array($id,$day,$play_time,$video_name,$program_type));
	  	 }
	  }else {
	  	writetofile("program_live_item_crawler_no_result.log", "program items exist: channel:[".$tv_code."];day:[".$day."]");
	  }
    }
    
   
    
    
  
	
	

?>