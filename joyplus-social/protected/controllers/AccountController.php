<?php

class AccountController extends Controller{
	
	/**
	 * login
	 * Enter description here ...
	 */
	function actionLogin(){		
	    header('Content-type: application/json');	
//	   	if(!Yii::app()->request->isPostRequest){   
//	   		 IjoyPlusServiceUtils::exportServiceError(Constants::METHOD_NOT_SUPPORT);
//	   		 return ;
//	   	}
	   if(!IjoyPlusServiceUtils::validateAPPKey()){
              IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
		  return ;
		}		 	
   	    $username = Yii::app()->request->getParam("username");
   		$pwd = Yii::app()->request->getParam("password");
   		$rememberMe = Yii::app()->request->getParam("rememberMe");
   		$identity=new IjoyPlusUserIdentity($username,$pwd);
		if($identity->authenticate()){
		    $duration=$rememberMe ? 3600*24*30 : 0; // 30 days
		    Yii::app()->user->login($identity,0);
		    IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);
		}else {
		    IjoyPlusServiceUtils::exportServiceError($identity->errorCode);
		}
	 
	}
    private function makeUIID($uid){
	  $length=strlen($uid);
	  if($length<8){
	     for($i=0;$i<(8-$length-1);$i++){
	       $uid='0'.$uid;
	     }
	     $uid='1'.$uid;
	  }
	  return $uid;
	  
	}
	function actionGenerateUIID(){	
	    header('Content-type: application/json');	
//	   	if(!Yii::app()->request->isPostRequest){   
//	   		 IjoyPlusServiceUtils::exportServiceError(Constants::METHOD_NOT_SUPPORT);
//	   		 return ;
//	   	}
	   if(!IjoyPlusServiceUtils::validateAPPKey()){
              IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
		  return ;
		}		 	
   	    $uiid = Yii::app()->request->getParam("uiid");
   		$device_type = Yii::app()->request->getParam("device_type");
   		$device_type= isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"";
   		try{
	   		if(isset($uiid) && !is_null($uiid)){
//			    $record=User::model()->find('device_number=? and status=?',array($uiid,Constants::USER_APPROVAL));
                $record=User::model()->find(array(
					'condition'=>'device_number=:device_number and status=:status',
					'params'=>array(
					    ':device_number'=>$uiid,
					    ':status'=>Constants::USER_APPROVAL,
					 ),
				));
//				var_dump($record);
		        if(isset($record) && !is_null($record)){
		           IjoyPlusServiceUtils::exportEntity( array(
		             'user_id'=>$record->id,
		           	 'nickname'=>$record->nickname,
		             'pic_url'=>$record->user_photo_url,
		             'username'=>$record->username,
		           ));
		           return ;
		        }
		    }
		  $user = User::model()->generateUIID($uiid, $device_type);
		  IjoyPlusServiceUtils::exportEntity($user);   			
   		}catch(Exception $e){  	   			
   	        Yii::log( CJSON::encode($e), "error");
   			IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR); 
   		} 
	}
	
/**
	 * login
	 * Enter description here ...
	 */
	function actionForgotPwd(){		
//	header('Content-type: application/json');	
//	   	if(!Yii::app()->request->isPostRequest){   
			   if(!IjoyPlusServiceUtils::validateAPPKey()){
  	  	          IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
				  return ;
				}		 	
	   		    $username = Yii::app()->request->getParam("username");
		   		
	   		
//	   	}else {	   	
//              IjoyPlusServiceUtils::exportServiceError(Constants::METHOD_NOT_SUPPORT);
//	   	}
	}
	
	
	/**
	 * bind third part account
	 * Enter description here ...
	 */
	function actionBindAccount(){		
	    header('Content-type: application/json');
//	    if(!Yii::app()->request->isPostRequest){   
//	   		 IjoyPlusServiceUtils::exportServiceError(Constants::METHOD_NOT_SUPPORT);
//	   		 return ;
//	   	}
	    if(!IjoyPlusServiceUtils::validateAPPKey()){
  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
		   return ;
		}
		if(IjoyPlusServiceUtils::validateUserID()){
			IjoyPlusServiceUtils::exportServiceError(Constants::USER_ID_INVALID);	
			return ;
		}
		$sourceid= Yii::app()->request->getParam("source_id");
		$source_type= Yii::app()->request->getParam("source_type");
		$nickname= Yii::app()->request->getParam("nickname");
		$pic_url= Yii::app()->request->getParam("pic_url");
		$userid=Yii::app()->user->id;
		if(IjoyPlusServiceUtils::validateThirdPartSource($source_type)){
			$code = User::model()->bindAccount($userid, $sourceid, $source_type);
			if(Constants::SUCC === $code){
				if(isset($nickname) && !is_null($nickname)){
					User::model()->updateNickname($userid, $nickname);
				}
				if(isset($pic_url) && !is_null($pic_url)){
					User::model()->updatePicUrl($userid, $pic_url);
				}
			}
		Yii::app()->user->setState('nickname', $nickname);
	    Yii::app()->user->setState('pic_url', $pic_url);
			IjoyPlusServiceUtils::exportServiceError($code);
		}else{
			IjoyPlusServiceUtils::exportServiceError(Constants::THIRD_PART_SOURCE_TYPE_INVALID);
		}
	}
	
   function actionValidateThirdParty(){		
	    header('Content-type: application/json');
	    if(!Yii::app()->request->isPostRequest){   
	   		 IjoyPlusServiceUtils::exportServiceError(Constants::METHOD_NOT_SUPPORT);
	   		 return ;
	   	}
	    if(!IjoyPlusServiceUtils::validateAPPKey()){
  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
		   return ;
		}
//		
		$sourceid= Yii::app()->request->getParam("source_id");
		$source_type= Yii::app()->request->getParam("source_type");
        if( (!isset($sourceid)) || is_null($sourceid)  ){
			IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
			return;
		}
		if(IjoyPlusServiceUtils::validateThirdPartSource($source_type)){
			try{
				$user = User::model()->searchUserByThirdParty( $source_type,$sourceid);
				if (isset($user) && !is_null($user)){					
	   		    	$identity=new IjoyPlusUserIdentity($user->username,'');
	   		    	$identity->setId($user->id);
	   		    	$identity->setState('nickname', $user->nickname);
	   		    	$identity->setState('pic_url', $user->user_photo_url);
	   		    	Yii::app()->user->login($identity);					
					IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);
				}else {
					IjoyPlusServiceUtils::exportServiceError(Constants::USER_NOT_EXIST);
				}
			}catch (Exception $e){
				IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
			}
						
		}else{
			IjoyPlusServiceUtils::exportServiceError(Constants::THIRD_PART_SOURCE_TYPE_INVALID);
		}
	}
	
    function actionBindPhone(){		
	    header('Content-type: application/json');
	    if(!Yii::app()->request->isPostRequest){   
	   		 IjoyPlusServiceUtils::exportServiceError(Constants::METHOD_NOT_SUPPORT);
	   		 return ;
	   	}
	    if(!IjoyPlusServiceUtils::validateAPPKey()){
  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
		   return ;
		}
        if(IjoyPlusServiceUtils::validateUserID()){
			IjoyPlusServiceUtils::exportServiceError(Constants::USER_ID_INVALID);	
			return ;
		}
		$phone= Yii::app()->request->getParam("phone");
		$userid=Yii::app()->user->id;
		$code = User::model()->bindAccount($userid, $phone, Constants::THIRD_PART_ACCOUNT_LOCAL_CONTACT);
		IjoyPlusServiceUtils::exportServiceError($code);
	}
	
      function actionUnbindAccount(){		
	    header('Content-type: application/json');
	    if(!Yii::app()->request->isPostRequest){   
	   		 IjoyPlusServiceUtils::exportServiceError(Constants::METHOD_NOT_SUPPORT);
	   		 return ;
	   	}
	    if(!IjoyPlusServiceUtils::validateAPPKey()){
  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
		   return ;
		}
      
        if(IjoyPlusServiceUtils::validateUserID()){
			IjoyPlusServiceUtils::exportServiceError(Constants::USER_ID_INVALID);	
			return ;
		}
		$source_type= Yii::app()->request->getParam("source_type");
		$userid=Yii::app()->user->id;
		if(IjoyPlusServiceUtils::validateThirdPartSource($source_type)){
			$code = User::model()->unBindAccount($userid,  $source_type);
			IjoyPlusServiceUtils::exportServiceError($code);
		}else{
			IjoyPlusServiceUtils::exportServiceError(Constants::THIRD_PART_SOURCE_TYPE_INVALID);
		}
	}
	/**
	 * logout
	 * Enter description here ...
	 */
	function actionLogout(){		
	    header('Content-type: application/json');
	    if(!IjoyPlusServiceUtils::validateAPPKey()){
  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
		   return ;
		}
        Yii::app()->user->logout();
	   	IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);
	}
	
	
	/**
	 * register user
	 * Enter description here ...
	 */
	function actionRegister(){		
	    header('Content-type: application/json');	
	   	if(!Yii::app()->request->isPostRequest){   
	   		 IjoyPlusServiceUtils::exportServiceError(Constants::METHOD_NOT_SUPPORT);
	   		 return ;
	   	}
	   if(!IjoyPlusServiceUtils::validateAPPKey()){
              IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
		  return ;
		}		 	
		
   		$nickname = Yii::app()->request->getParam("nickname");
   		$pwd = Yii::app()->request->getParam("password");
   		$username = Yii::app()->request->getParam("username");
   		
   		if( !(isset($nickname) && !is_null($nickname) && strlen($nickname) >0) ) {
   			IjoyPlusServiceUtils::exportServiceError(Constants::NICKNAME_IS_NULL);	
   			return ;	   			
   		}
   	   if( !(isset($pwd) && !is_null($pwd) && strlen($pwd) >0) ) {
   			IjoyPlusServiceUtils::exportServiceError(Constants::PWD_IS_NULL);	
   			return ;	   			
   		}
        if( (isset($username) && !is_null($username) && strlen($username) >0) ) {	        	
        	$emailValidator = new CEmailValidator;
        	if(!$emailValidator->validateValue($username)){
        		IjoyPlusServiceUtils::exportServiceError(Constants::EMAIL_INVALID);
        		return ;
        	}else{
        		$record=User::model()->find('LOWER(username)=?',array(strtolower($username)));
        		if($record !== null){
        		  IjoyPlusServiceUtils::exportServiceError(Constants::USERNAME_EXIST);	
   			      return ;
        		}
        	}  			
   		}else {
          IjoyPlusServiceUtils::exportServiceError(Constants::USERNAME_IS_NULL);	
   	      return ;
   		}
   		try{
   		  $record=User::model()->find('LOWER(nickname)=?',array(strtolower($nickname)));
   		  if($record !== null){
   		  	IjoyPlusServiceUtils::exportServiceError(Constants::NICKNAME_IS_EXSTING);	
   			return ;
   		  }else {
		    $model=new User;
   		    $model->nickname=$nickname;
   		    $model->password=md5($pwd);
   		    $model->username=$username;
   		    $model->status=Constants::USER_APPROVAL;
   		    $model->create_date=new CDbExpression('NOW()');
   		    
   		    
   		    if($model->save()){
   		    	$identity=new IjoyPlusUserIdentity($username,$pwd);
   		    	$identity->setId($model->id);
   		    	$identity->setState('nickname', $model->nickname);
   		    	Yii::app()->user->login($identity);
   		    	UserManager::followPrestiges($model->id);
   		    	IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);
   		    }else {		    	
   		    	IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
   		    }
   		    
              } 
   		}catch(Exception $e){
   			IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
   		}
	   	
	}
	
/**
	 * register user
	 * Enter description here ...
	 */
	function actionUpdateProfile(){		
	    header('Content-type: application/json');
//	    if(!Yii::app()->request->isPostRequest){   
//	   		 IjoyPlusServiceUtils::exportServiceError(Constants::METHOD_NOT_SUPPORT);
//	   		 return ;
//	   	}
	    if(!IjoyPlusServiceUtils::validateAPPKey()){
  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
		   return ;
		}
		
   		$nickname= Yii::app()->request->getParam("nickname");
   		$pwd = Yii::app()->request->getParam("password");
   		$username = Yii::app()->request->getParam("username");
	    $sourceid= Yii::app()->request->getParam("source_id");
	    $source_type= Yii::app()->request->getParam("source_type");
        if(!IjoyPlusServiceUtils::validateThirdPartSource($source_type)){
		   IjoyPlusServiceUtils::exportServiceError(Constants::THIRD_PART_SOURCE_TYPE_INVALID);
		   return ;
	    }
   		if( !(isset($nickname) && !is_null($nickname) && strlen($nickname) >0) ) {
   			IjoyPlusServiceUtils::exportServiceError(Constants::NICKNAME_IS_NULL);	
   			return ;	   			
   		}
   	   if( !(isset($pwd) && !is_null($pwd) && strlen($pwd) >0) ) {
   			IjoyPlusServiceUtils::exportServiceError(Constants::PWD_IS_NULL);	
   			return ;	   			
   		}
        if( (isset($username) && !is_null($username) && strlen($username) >0) ) {	        	
        	$emailValidator = new CEmailValidator;
        	if(!$emailValidator->validateValue($username)){
        		IjoyPlusServiceUtils::exportServiceError(Constants::EMAIL_INVALID);
        		return ;
        	}else{
        		$record=User::model()->find('LOWER(username)=?',array(strtolower($username)));
        		if($record !== null){
        		  IjoyPlusServiceUtils::exportServiceError(Constants::USERNAME_EXIST);	
   			      return ;
        		}
        	}  			
   		}
   		try{
   		  $record=User::model()->find('LOWER(nickname)=?',array(strtolower($nickname)));
   		  if($record !== null){
   		  	IjoyPlusServiceUtils::exportServiceError(Constants::NICKNAME_IS_EXSTING);	
   			return ;
   		  }else {
		    $model=new User;
   		    $model->nickname=$nickname;
   		    $model->password=md5($pwd);
   		    $model->username=$username;
   		    $model->status=Constants::USER_APPROVAL;
   		    $model->create_date=new CDbExpression('NOW()');
   		    switch ($source_type){
				case Constants::THIRD_PART_ACCOUNT_DOUBAN:
					$model->douban_user_id=$sourceid;
					break;
				case Constants::THIRD_PART_ACCOUNT_QQ:
					$model->qq_wb_user_id=$sourceid;
					break;
				case Constants::THIRD_PART_ACCOUNT_REN_REN:
					$model->ren_user_id=$sourceid;
					break;
				case Constants::THIRD_PART_ACCOUNT_SINA:
					$model->sina_wb_user_id=$sourceid;
					break;
		   }
   		    
   		    if($model->save()){
   		    	$identity=new IjoyPlusUserIdentity($username,$pwd);
   		    	$identity->setId($model->id);
   		    	$identity->setState('nickname', $model->nickname);
   		    	Yii::app()->user->login($identity);
   		    	UserManager::followPrestiges($model->id);
   		    	IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);
   		    	//
   		    }else {	
   		    	 Yii::log( CJSON::encode($model->getErrors()), "warning");
//   		    	var_dump();    	
   		    	IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
   		    }
   		    
              } 
   		}catch(Exception $e){  	   			
   	        Yii::log( CJSON::encode($e), "error");
   			IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR); 
   		}   	
	}
}

?>