<?php

class SearchController extends Controller
{
	function actionTopKeywords(){
       header('Content-type: application/json');
//	   if(Yii::app()->user->isGuest){
//			IjoyPlusServiceUtils::exportServiceError(Constants::SEESION_IS_EXPIRED);	
//			return ;
//		}
	    if(!IjoyPlusServiceUtils::validateAPPKey()){
  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
		   return ;
		}
		$num= Yii::app()->request->getParam("num");	
		if(!(isset($num) && is_numeric($num))){
			$num=10;
		}
	    $results = Lookup::model()->topKeypwords($num);
        if(isset($results) && is_array($results)){				
		  IjoyPlusServiceUtils::exportEntity(array('topKeywords'=>$results));
	    }else {
		  IjoyPlusServiceUtils::exportEntity(array('topKeywords'=>array()));
		}
	}
	
	function actionLunBo(){
		header('Content-type: application/json');
	    if(!IjoyPlusServiceUtils::validateAPPKey()){
  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
		   return ;
		}
	    try{
		  $prods = SearchManager::lunbo();
		  if(isset($prods) && is_array($prods)){				
		    IjoyPlusServiceUtils::exportEntity(array('results'=>$prods));
		    }else {
			  IjoyPlusServiceUtils::exportEntity(array('results'=>array()));
			}
		}catch (Exception $e){
			var_dump($e);
		    IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);	
		}
	}
	
    function actionSearch(){
        header('Content-type: application/json');
//	   if(Yii::app()->user->isGuest){
//			IjoyPlusServiceUtils::exportServiceError(Constants::SEESION_IS_EXPIRED);	
//			return ;
//		}
	    if(!IjoyPlusServiceUtils::validateAPPKey()){
  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
		   return ;
		}
		$keyword= Yii::app()->request->getParam("keyword");	
        if( !(isset($keyword) && !is_null($keyword) && strlen($keyword) >0) ) {
   			IjoyPlusServiceUtils::exportServiceError(Constants::KEYWORD_IS_NULL);	
   			return ;	   			
   		}
   		if( !FilterUtils::keyWordValid($keyword)){
   			 IjoyPlusServiceUtils::exportEntity(array('results'=>array()));
   			 return;
   		}
//   		$keyword= iconv("GBK","UTF-8",$keyword);n
//   		var_dump($keyword);
   		//$keyword='???';
		Lookup::model()->saveLookup($keyword);
//        var_dump($keyword);
		
   		$keyword=strtr($keyword, array('%'=>'\%', '_'=>'\_'));
//   		var_dump($keyword);
        $page_size=Yii::app()->request->getParam("page_size");
		$page_num=Yii::app()->request->getParam("page_num");
		if(!(isset($page_size) && is_numeric($page_size))){
			$page_size=10;
			$page_num=1;
		}else if(!(isset($page_num) && is_numeric($page_num))){
			$page_num=1;
		}
		$type= Yii::app()->request->getParam("type");	
		try{
		  if (is_null($type) || $type==''){
		    $prods = SearchManager::searchProgram($keyword,$page_size,$page_size*($page_num-1));
		  }else {
		    $prods = SearchManager::searchProgramByType($keyword,$type,$page_size,$page_size*($page_num-1));
		  }
		  if(isset($prods) && is_array($prods)){				
		    IjoyPlusServiceUtils::exportEntity(array('results'=>$prods));
		    }else {
			  IjoyPlusServiceUtils::exportEntity(array('results'=>array()));
			}
		}catch (Exception $e){
			var_dump($e);
		  IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);	
		}
	}
	
   function actionPopMovie(){
       header('Content-type: application/json');
//	   if(Yii::app()->user->isGuest){
//			IjoyPlusServiceUtils::exportServiceError(Constants::SEESION_IS_EXPIRED);	
//			return ;
//		}
	    if(!IjoyPlusServiceUtils::validateAPPKey()){
  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
		   return ;
		}
		$page_size=Yii::app()->request->getParam("page_size");
		$page_num=Yii::app()->request->getParam("page_num");
		if(!(isset($page_size) && is_numeric($page_size))){
			$page_size=10;
			$page_num=1;
		}else if(!(isset($page_num) && is_numeric($page_num))){
			$page_num=1;
		}
		try{
		  $prods = SearchManager::popularProgram(SearchManager::POPULAR_MOVIE_SPECIAL_ID,$page_size,$page_size*($page_num-1));
		  if(isset($prods) && is_array($prods)){				
		    IjoyPlusServiceUtils::exportEntity(array('movie'=>$prods));
		    }else {
			  IjoyPlusServiceUtils::exportEntity(array('movie'=>array()));
			}
		}catch (Exception $e){
		  IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);	
		}
	}
	
   function actionPopVedio(){
       header('Content-type: application/json');
//	   if(Yii::app()->user->isGuest){
//			IjoyPlusServiceUtils::exportServiceError(Constants::SEESION_IS_EXPIRED);	
//			return ;
//		}
	    if(!IjoyPlusServiceUtils::validateAPPKey()){
  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
		   return ;
		}
		$page_size=Yii::app()->request->getParam("page_size");
		$page_num=Yii::app()->request->getParam("page_num");
		if(!(isset($page_size) && is_numeric($page_size))){
			$page_size=10;
			$page_num=1;
		}else if(!(isset($page_num) && is_numeric($page_num))){
			$page_num=1;
		}
		
		try{
		  $prods = SearchManager::popularProgram(SearchManager::POPULAR_TV_VEDIO_SPECIAL_ID,$page_size,$page_size*($page_num-1));
		  if(isset($prods) && is_array($prods)){				
		    IjoyPlusServiceUtils::exportEntity(array('video'=>$prods));
		    }else {
			  IjoyPlusServiceUtils::exportEntity(array('video'=>array()));
			}
		}catch (Exception $e){
		  IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);	
		}
	}
	
   function actionPopTV(){
       header('Content-type: application/json');
//	   if(Yii::app()->user->isGuest){
//			IjoyPlusServiceUtils::exportServiceError(Constants::SEESION_IS_EXPIRED);	
//			return ;
//		}
	    if(!IjoyPlusServiceUtils::validateAPPKey()){
  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
		   return ;
		}
		$page_size=Yii::app()->request->getParam("page_size");
		$page_num=Yii::app()->request->getParam("page_num");
		if(!(isset($page_size) && is_numeric($page_size))){
			$page_size=10;
			$page_num=1;
		}else if(!(isset($page_num) && is_numeric($page_num))){
			$page_num=1;
		}
		try{
		  $prods = SearchManager::popularProgram(SearchManager::POPULAR_TV_SET_SPECIAL_ID,$page_size,$page_size*($page_num-1));
		  if(isset($prods) && is_array($prods)){				
		    IjoyPlusServiceUtils::exportEntity(array('tv'=>$prods));
		    }else {
			  IjoyPlusServiceUtils::exportEntity(array('tv'=>array()));
			}
		}catch (Exception $e){
		  IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);	
		}
	}
	
   function actionPopShow(){
       header('Content-type: application/json');
//	   if(Yii::app()->user->isGuest){
//			IjoyPlusServiceUtils::exportServiceError(Constants::SEESION_IS_EXPIRED);	
//			return ;
//		}
	    if(!IjoyPlusServiceUtils::validateAPPKey()){
  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
		   return ;
		}
		$page_size=Yii::app()->request->getParam("page_size");
		$page_num=Yii::app()->request->getParam("page_num");
		if(!(isset($page_size) && is_numeric($page_size))){
			$page_size=10;
			$page_num=1;
		}else if(!(isset($page_num) && is_numeric($page_num))){
			$page_num=1;
		}
		try{
		  $prods = SearchManager::popularProgram(SearchManager::POPULAR_TV_SHOW_SPECIAL_ID,$page_size,$page_size*($page_num-1));
		  if(isset($prods) && is_array($prods)){				
		    IjoyPlusServiceUtils::exportEntity(array('show'=>$prods));
		    }else {
			  IjoyPlusServiceUtils::exportEntity(array('show'=>array()));
			}
		}catch (Exception $e){
		  IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);	
		}
	}
	
}
