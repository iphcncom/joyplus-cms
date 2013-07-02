<?php

require_once('Utils.php');
require_once('ParseClient.php');
InstallationManager::getInstallations(array());
class InstallationManager {
	
 static function getInstallations($condition,$pageSize=40,$pageno=1){
		   $skip=($pageno-1)*$pageSize;
		   $args = array (
				'className' => '_Installation',
				'limit' =>$pageSize,
		        'skip' =>$skip,
		        'order' =>'-createAt'
		   
		);
		
        if(isset($condition) && count($condition)>0){
			$args['query']=$condition;
		}
		
		$result = ParseClient::getInstance ()->query ( $args );
		$list = obj2arr ( $result->results );
	    return $list;
	}
	
    static function getInstallationsCounts($condition){
		   $args = array (
				'className' => '_Installation',
				'limit' =>'0',
		        'count' =>'1' 
		);
      if(isset($condition) && count($condition)>0){
			$args['query']=$condition;
		}
		$result = ParseClient::getInstance ()->query ( $args );		
	    return $result->count;
	}
	



    static function deleteInstallation($InstallationID){	
	$args = array (
			'className' => '_Installation',
			'objectId' => $InstallationID,
			 'object'   => array (
				'pushFlag' =>'Y' 
		      ) 
	);
	if (ParseClient::getInstance ()->update ( $args )) {
		return true;
	}
	return false;
}


}
?>