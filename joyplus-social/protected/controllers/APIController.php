<?php

class APIController extends Controller
{   
	
		public function actionOpenApiConfig(){
	        header('Content-type: application/json');
		    if(!Yii::app()->request->isPostRequest){   
		   		 IjoyPlusServiceUtils::exportServiceError(Constants::METHOD_NOT_SUPPORT);
		   		 return ;
		   	}
		    if(!IjoyPlusServiceUtils::validateAPPKey()){
	  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
			   return ;
			}		
	       
   			try {
   				$device_name = Yii::app()->request->getParam("device_name");
	   			if( !(isset($device_name) && !is_null($device_name) && strlen($device_name) >0) ) {
	   			  IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);	
	   			  return ;	   			
	   		    }
	   		    $apiConfig= ApiConfig::model()->find(array(
			     "condition"=>"device_name like '%".$device_name.",%' "
		        ));
		        if($apiConfig ==null){
		          IjoyPlusServiceUtils::exportEntity(array());
		        }else {
   		 		   IjoyPlusServiceUtils::exportEntity(array(
   		 		     'api_url'=>$apiConfig['api_url'],
   		 		     'logo_url'=>$apiConfig['logo_url'],   		 		   
   		 		     'app_key'=>$apiConfig['app_key'],
   		 		  ));
		        }
   			} catch (Exception $e) {
   				IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
   			}
   		}

}
