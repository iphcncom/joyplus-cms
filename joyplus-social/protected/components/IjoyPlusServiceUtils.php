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
  	
  public static function transferComments($comment){
  		$vo = new CommentVO;
  		if($comment instanceof Comment){
  			$vo->content=$comment->comments;
  			$vo->id = $comment->id;
  			$vo->create_date=$comment->create_date;
  			$vo->owner_id=$comment->author_id;
  			$vo->owner_name=$comment->author_username;
  			$vo->owner_pic_url=$comment->author_photo_url;
  		}
  		return $vo;
  	} 
  	
  public static function checkCSRCToken(){
  	$token = Yii::app()->request->getParam("token");
  	if(isset($token) && !is_null($token)){
  	  $tokenKey = Yii::app()->user->getState(Constants::CRSC_TOKEN_KEY);
  	  if(isset($tokenKey) && !is_null($tokenKey) && $tokenKey ===$token){
  	  	return false;
  	  }
  	  Yii::app()->user->setState(Constants::CRSC_TOKEN_KEY,$token);
  	  return true;
  	}else {
  		return false;
  	}
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
  		}else if(Constants::THIRD_PART_ACCOUNT_LOCAL_CONTACT === $source){
  			return true;
  		}
  		return false;
  	}
  	
  	public static function validateAPPKey(){
   	  $appKey= isset($_SERVER['HTTP_APP_KEY'])?$_SERVER['HTTP_APP_KEY']:"";
   	  if(!(isset($appKey) && !is_null($appKey) && strlen($appKey) >0)){
   	  	 $appKey = Yii::app()->request->getParam("app_key");
//        return false;
   	  }
  	  if(! IjoyPlusServiceUtils::validate( $appKey)   ){
	   	return false;
  	  }
  	  return true; 		
  	}
  	
   public static function validateUserID(){
   	  $userid= isset($_SERVER['HTTP_USER_ID'])?$_SERVER['HTTP_USER_ID']:"";
   	  if(!(isset($userid) && !is_null($userid) && strlen($userid) >0)){
   	  	 $userid = Yii::app()->request->getParam("user_id");
   	  }   	  
   	  if(isset($userid) && !is_null($userid) && strlen($userid) >0){
   	  	return IjoyPlusServiceUtils::login($userid);
  	  }
  	  return true; 		
  	}
  	
  	private static function login($userid){
  		$user = User::model()->findUser($userid);
  		if($user!==false && isset($user) && !is_null($user)) {  						
	       	$identity=new IjoyPlusUserIdentity($user['username'],'');
	       	$identity->setId($userid);
	       	$identity->setState('nickname', $user['nickname']);
	       	$identity->setState('pic_url', $user['user_photo_url']);
	       	Yii::app()->user->login($identity);
	       	return false;	
  		}else {
  			return true;
  		}
  	}
  	
  	public static function exportServiceError($errorCode){
  		$repsonse = new IJoyPlusResponse;	
	   	$repsonse->res_code=$errorCode;
	   	$repsonse->res_desc=Yii::app()->params['errors'][$errorCode];
	    echo CJSON::encode($repsonse);
	    Yii::app()->end();
  	}
  	
  public static function exportEntity($entity){  		
	    echo  CJSON::encode($entity);
	    Yii::app()->end();
  	}
  }
?>