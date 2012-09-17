<?php
  class IjoyPlusServiceUtils{
  	
  	
  	public static function validate($app_key){
  		$app_keys = explode(',',Yii::app()->params['app_key']);
  		if(isset($app_keys) && is_array($app_keys)){
  			foreach ($app_keys as  $key){
  				if($key === $app_key){
  					return true;
  				}
  			}
  		}
  		return false;
  	}
  	public static function validateThirdPartSource($source){
  		if(Constants::THIRD_PART_ACCOUNT_DOUBAN === $source){
  			return true;
  		}else if(Constants::THIRD_PART_ACCOUNT_QQ === $source){
  			return true;
  		}else if(Constants::THIRD_PART_ACCOUNT_REN_REN === $source){
  			return true;
  		}else if(Constants::THIRD_PART_ACCOUNT_SINA === $source){
  			return true;
  		}
  		return false;
  	}
  	
  	public static function validateAPPKey(){
  	  $appKey = Yii::app()->request->getParam("app_key");
  	  if(! IjoyPlusServiceUtils::validate( $appKey)   ){
	   	return false;
  	  }
  	  return true; 		
  	}
  	public static function exportServiceError($errorCode){
  		$repsonse = new IJoyPlusResponse;	
	   	$repsonse->res_code=$errorCode;
	   	$repsonse->res_desc=Yii::app()->params['errors'][$errorCode];
	    echo CJSON::encode($repsonse);
	    Yii::app()->end();
  	}
  }
?>