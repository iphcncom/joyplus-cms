<?php
ob_implicit_flush(true);
ini_set('max_execution_time', '0');
require_once ("../admin_conn.php");
require_once ("collect_fun.php");
require_once ("collect_vod.php");
require_once ("../score/AutoDouBanParseScore.php");

$host=$_SERVER['HTTP_HOST'];
 $crontab = be("all", "crontab");
crawler($crontab);

exit(-1);

function crawler($crontab){	
     global $db;
     writetofile("crawler_collect.sql", 'crawler start: crontab: ' .$crontab); 
	
    $sql = "SELECT m_pid, m_urltest FROM mac_cj_zhuiju  where status=0 and crontab_desc like'%".$crontab."%' GROUP BY m_urltest order by m_urltest ";
     writetofile("crawler_collect.sql", 'crawler start: sql: ' .$sql); 
    $rs = $db->query($sql); 
    parseVodPad($rs);
    unset($rs);
	
	 
	 writetofile("crawler_collect.sql", 'crawler stop.');
	 collect($crontab);
}
 
function collect($crontab){
	writetofile("crawler_collect.sql", 'collect start.');
	global $db;
	$count = $db->getOne("SELECT count(*) FROM mac_cj_zhuiju  where status=0 and crontab_desc like'%".$crontab."%'  ");
	$sql="select a.* from {pre}cj_vod as a , mac_cj_zhuiju as b where a.m_id= b.m_id and b.crontab_desc like'%".$crontab."%' and a.m_typeid>0 and a.m_name IS NOT NULL AND a.m_name != '' and a.m_playfrom not in ('tudou','kankan','cntv','wasu')";
    
	MovieInflow($sql,$count,true);
	writetofile("crawler_collect.sql", 'collect stop.');
}

    function parseVodPad($rs){
    	global $db,$host;
        while ($row = $db ->fetch_array($rs))	{
    		$m_urltest=$row["m_urltest"];
			$m_pid=$row["m_pid"]; 	
            $url=  "http://".$host."/manager/collect/collect_vod_cj.php?action=collectSimpl&p_id=".$m_pid.'&site_url='.urlencode($m_urltest);
            
            writetofile("crawler_collect.sql",'url: '. $m_urltest .' pid:'.$m_pid.'  crawler url : '. $url);
            $body= getPage($url, 'utf-8');
	    }
	    unset($rs);
    }
    
    
   
    
    
  
	
	

?>