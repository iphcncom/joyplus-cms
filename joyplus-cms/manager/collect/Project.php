<?php

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
	   $proejct->p_playtype=$row["p_playtype"];
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
	   public $p_playtype;
	   
	}

?>