<?php
ob_implicit_flush(true);
ini_set('max_execution_time', '0');
require_once ("../admin_conn.php");
require_once ("collect_fun.php");



updateVodPic(1);
function updateVodPic($pagenum){	
     global $db;
    
	 $sql = "SELECT count(*)  FROM {pre}vod where  d_capital_name IS NULL OR d_capital_name = ''" ;
	 $nums = $db->getOne($sql);  
	 $app_pagenum=10; 
	 $pagecount=ceil($nums/$app_pagenum);
//	 $pagecount=2;
	 for($i=$pagenum;$i<=$pagecount;$i++){
	 	
	    $sql = "SELECT d_name,d_id FROM {pre}vod where  d_capital_name IS NULL OR d_capital_name = '' limit ".($app_pagenum * ($i-1)) .",".$app_pagenum;
//	    var_dump($sql);
	    $rs = $db->query($sql); 
	    parseVodPad($rs,$scoreDouban);
	    unset($rs);
	 }
}


    function parseVodPad($rs,$scoreDouban){
    	global $db;
        while ($row = $db ->fetch_array($rs))	{
    		$name=Hanzi2PinYin_Captial($row["d_name"]);
			$d_id=$row["d_id"];				
           $db->Update ("{pre}vod", array("d_capital_name"), array($name), "d_id=" . $d_id);
	    }
	    unset($rs);
    }
    
    
   
    
    
  
	
	

?>