<?php

class SearchController extends Controller
{
	function actionTopKeywords(){
	        header('Content-type: application/json');
		    if(Yii::app()->request->isPostRequest){   
		   		 IjoyPlusServiceUtils::exportServiceError(Constants::METHOD_NOT_SUPPORT);
		   		 return ;
		   	}
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
}
