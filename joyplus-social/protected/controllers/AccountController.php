<?php

class AccountController extends Controller{
	
	/**
	 * login
	 * Enter description here ...
	 */
	function actionLogin(){		
	    header('Content-type: application/json');	
	   	if(Yii::app()->request->isPostRequest){   
	   		 IjoyPlusServiceUtils::exportServiceError(Constants::METHOD_NOT_SUPPORT);
	   		 return ;
	   	}
	   if(!IjoyPlusServiceUtils::validateAPPKey()){
              IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
		  return ;
		}		 	
   	    $email = Yii::app()->request->getParam("email");
   		$pwd = Yii::app()->request->getParam("password");
   		$rememberMe = Yii::app()->request->getParam("rememberMe");
   		$identity=new IjoyPlusUserIdentity($email,$pwd);
		if($identity->authenticate()){
		    $duration=$rememberMe ? 3600*24*30 : 0; // 30 days
		    Yii::app()->user->login($identity,$duration);
		    IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);
		}else {
		    IjoyPlusServiceUtils::exportServiceError($identity->errorCode);
		}
	 
	}
	
/**
	 * login
	 * Enter description here ...
	 */
	function actionForgotPwd(){		
//	header('Content-type: application/json');	
//	   	if(Yii::app()->request->isPostRequest){   
			   if(!IjoyPlusServiceUtils::validateAPPKey()){
  	  	          IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
				  return ;
				}		 	
	   		    $username = Yii::app()->request->getParam("username");
		   		
	   		
//	   	}else {	   	
//              IjoyPlusServiceUtils::exportServiceError(Constants::METHOD_NOT_SUPPORT);
//	   	}
	}
	
	function actionUserInfo(){
	    header('Content-type: application/json');	
	   	if(Yii::app()->request->isPostRequest){   
	   		 IjoyPlusServiceUtils::exportServiceError(Constants::METHOD_NOT_SUPPORT);
	   		 return ;
	   	}
	   if(!IjoyPlusServiceUtils::validateAPPKey()){
              IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
		  return ;
		}
		if(!Yii::app()->user->isGuest){
			$id=Yii::app()->user->id;
			$user = new UserVO;
			$user->id=$id;
			$user->username=Yii::app()->user->getState("username");
			echo CJSON::encode($user);
	        Yii::app()->end();
		}else {
			IjoyPlusServiceUtils::exportServiceError(Constants::SEESION_IS_EXPIRED);	
		}
	}
	/**
	 * bind third part account
	 * Enter description here ...
	 */
	function actionBindAccount(){
        header('Content-type: application/json');
	    if(Yii::app()->request->isPostRequest){   
	   		 IjoyPlusServiceUtils::exportServiceError(Constants::METHOD_NOT_SUPPORT);
	   		 return ;
	   	}
	    if(!IjoyPlusServiceUtils::validateAPPKey()){
  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
		   return ;
		}
		if(Yii::app()->user->isGuest){
			IjoyPlusServiceUtils::exportServiceError(Constants::SEESION_IS_EXPIRED);	
			return ;
		}
		$sourceid= Yii::app()->request->getParam("source_id");
		$source_type= Yii::app()->request->getParam("source_type");
		$userid=Yii::app()->user->id;
		if(IjoyPlusServiceUtils::validateThirdPartSource($source_type)){
			$code = User::model()->bindAccount($userid, $sourceid, $source_type);
			IjoyPlusServiceUtils::exportServiceError($code);
		}else{
			IjoyPlusServiceUtils::exportServiceError(Constants::THIRD_PART_SOURCE_TYPE_INVALID);
		}
	}
	
    function actionBindPhone(){
        header('Content-type: application/json');
	    if(Yii::app()->request->isPostRequest){   
	   		 IjoyPlusServiceUtils::exportServiceError(Constants::METHOD_NOT_SUPPORT);
	   		 return ;
	   	}
	    if(!IjoyPlusServiceUtils::validateAPPKey()){
  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
		   return ;
		}
		if(Yii::app()->user->isGuest){
			IjoyPlusServiceUtils::exportServiceError(Constants::SEESION_IS_EXPIRED);	
			return ;
		}
		$phone= Yii::app()->request->getParam("phone");
		$userid=Yii::app()->user->id;
		$code = User::model()->bindAccount($userid, $phone, Constants::THIRD_PART_ACCOUNT_LOCAL_CONTACT);
		IjoyPlusServiceUtils::exportServiceError($code);
	}
	
      function actionUnbindAccount(){
        header('Content-type: application/json');
	    if(Yii::app()->request->isPostRequest){   
	   		 IjoyPlusServiceUtils::exportServiceError(Constants::METHOD_NOT_SUPPORT);
	   		 return ;
	   	}
	    if(!IjoyPlusServiceUtils::validateAPPKey()){
  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
		   return ;
		}
		if(Yii::app()->user->isGuest){
			IjoyPlusServiceUtils::exportServiceError(Constants::SEESION_IS_EXPIRED);	
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
	    if(Yii::app()->request->isPostRequest){   
	   		 IjoyPlusServiceUtils::exportServiceError(Constants::METHOD_NOT_SUPPORT);
	   		 return ;
	   	}
	    if(!IjoyPlusServiceUtils::validateAPPKey()){
  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
		   return ;
		}
		if(Yii::app()->user->isGuest){
			IjoyPlusServiceUtils::exportServiceError(Constants::SEESION_IS_EXPIRED);	
			return ;
		}
   		$username = Yii::app()->request->getParam("username");
   		$pwd = Yii::app()->request->getParam("password");
   		$email = Yii::app()->request->getParam("email");
   		
   		if( !(isset($username) && !is_null($username) && strlen($username) >0) ) {
   			IjoyPlusServiceUtils::exportServiceError(Constants::USERNAME_IS_NULL);	
   			return ;	   			
   		}
   	   if( !(isset($pwd) && !is_null($pwd) && strlen($pwd) >0) ) {
   			IjoyPlusServiceUtils::exportServiceError(Constants::PWD_IS_NULL);	
   			return ;	   			
   		}
        if( (isset($email) && !is_null($email) && strlen($email) >0) ) {	        	
        	$emailValidator = new CEmailValidator;
        	if(!$emailValidator->validateValue($email)){
        		IjoyPlusServiceUtils::exportServiceError(Constants::EMAIL_INVALID);
        		return ;
        	}else{
        		$record=User::model()->find('LOWER(email)=?',array(strtolower($email)));
        		if($record !== null){
        		  IjoyPlusServiceUtils::exportServiceError(Constants::EMAIL_EXIST);	
   			      return ;
        		}
        	}  			
   		}
   		try{
   		  $record=User::model()->find('LOWER(username)=?',array(strtolower($username)));
   		  if($record !== null){
   		  	IjoyPlusServiceUtils::exportServiceError(Constants::USERNAME_EXIST);	
   			return ;
   		  }else {
		    $model=new User;
   		    $model->username=$username;
   		    $model->password=md5($pwd);
   		    $model->email=$email;
   		    $model->status=Constants::USER_APPROVAL;
   		    $model->create_date=new CDbExpression('NOW()');
   		    
   		    
   		    if($model->save()){
   		    	$identity=new IjoyPlusUserIdentity($username,$pwd);
   		    	$identity->setId($model->id);
   		    	$identity->setState('username', $model->username);
   		    	Yii::app()->user->login($identity);
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
	    if(Yii::app()->request->isPostRequest){   
	   		 IjoyPlusServiceUtils::exportServiceError(Constants::METHOD_NOT_SUPPORT);
	   		 return ;
	   	}
	    if(!IjoyPlusServiceUtils::validateAPPKey()){
  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
		   return ;
		}
		if(Yii::app()->user->isGuest){
			IjoyPlusServiceUtils::exportServiceError(Constants::SEESION_IS_EXPIRED);	
			return ;
		}
   		$username = Yii::app()->request->getParam("username");
   		$pwd = Yii::app()->request->getParam("password");
   		$email = Yii::app()->request->getParam("email");
	    $sourceid= Yii::app()->request->getParam("source_id");
	    $source_type= Yii::app()->request->getParam("source_type");
        if(!IjoyPlusServiceUtils::validateThirdPartSource($source_type)){
		   IjoyPlusServiceUtils::exportServiceError(Constants::THIRD_PART_SOURCE_TYPE_INVALID);
		   return ;
	    }
   		if( !(isset($username) && !is_null($username) && strlen($username) >0) ) {
   			IjoyPlusServiceUtils::exportServiceError(Constants::USERNAME_IS_NULL);	
   			return ;	   			
   		}
   	   if( !(isset($pwd) && !is_null($pwd) && strlen($pwd) >0) ) {
   			IjoyPlusServiceUtils::exportServiceError(Constants::PWD_IS_NULL);	
   			return ;	   			
   		}
        if( (isset($email) && !is_null($email) && strlen($email) >0) ) {	        	
        	$emailValidator = new CEmailValidator;
        	if(!$emailValidator->validateValue($email)){
        		IjoyPlusServiceUtils::exportServiceError(Constants::EMAIL_INVALID);
        		return ;
        	}else{
        		$record=User::model()->find('LOWER(email)=?',array(strtolower($email)));
        		if($record !== null){
        		  IjoyPlusServiceUtils::exportServiceError(Constants::EMAIL_EXIST);	
   			      return ;
        		}
        	}  			
   		}
   		try{
   		  $record=User::model()->find('LOWER(username)=?',array(strtolower($username)));
   		  if($record !== null){
   		  	IjoyPlusServiceUtils::exportServiceError(Constants::USERNAME_EXIST);	
   			return ;
   		  }else {
		    $model=new User;
   		    $model->username=$username;
   		    $model->password=md5($pwd);
   		    $model->email=$email;
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
   		    	$identity->setState('username', $model->username);
   		    	Yii::app()->user->login($identity);
   		    	IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);
   		    }else {		    	
   		    	IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
   		    }
   		    
              } 
   		}catch(Exception $e){
   			IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
   		}   	
	}
}

?>