<?php

class ProgramController extends Controller
{

     
	function actionPublish(){
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
		$prod_id= Yii::app()->request->getParam("prod_id");
		if( (!isset($prod_id)) || is_null($prod_id)  ){
			IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
			return;
		}
		$owner_id=Yii::app()->user->id;
		$transaction = Yii::app()->db->beginTransaction();
		try {	
	      $model= Program::model()->findByPk($prod_id);
		  if($model !==null){
		  	if(isset($model->publish_owner_id) && !is_null($model->publish_owner_id) && strlen($model->publish_owner_id)>0){	 
			  	 IjoyPlusServiceUtils::exportServiceError(Constants::PROGRAM_IS_PUBLISHED);
			}else {
		  	  $model->publish_owner_id=$owner_id;	
			  $model->save();
			  $dynamic = new Dynamic();
			  $dynamic->author_id=$owner_id;
			  $dynamic->content_id=$model->d_id;
		   	  $dynamic->status=Constants::OBJECT_APPROVAL;
			  $dynamic->create_date=new CDbExpression('NOW()');
			  $dynamic->content_type=$model->d_type;
			  $dynamic->content_name=$model->d_name;
			  $dynamic->dynamic_type=Constants::DYNAMIC_TYPE_PUBLISH_PROGRAM;
			  $dynamic->content_pic_url=$model->d_pic;
			  $dynamic->save();
			  $transaction->commit();
		      IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);
			 }			  
		   }else {
		      IjoyPlusServiceUtils::exportServiceError(Constants::OBJECT_NOT_FOUND);
		  }
		}catch (Exception $e){
		    $transaction->rollback();
			IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
	    }		
	}

	function actionLike(){
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
		$prod_id= Yii::app()->request->getParam("prod_id");
		if( (!isset($prod_id)) || is_null($prod_id)  ){
			IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
			return;
		}
		$program = Program::model()->findByPk($prod_id);
		if($program !== null){
			$owner_id=Yii::app()->user->id;
			$transaction = Yii::app()->db->beginTransaction();
			try {
				$program->love_user_count=$program->love_user_count+1;
//				$program->save();
				Program::model()->incLoveUserCount($prod_id);
				
				CacheManager::synProgramCache($program);

				$dynamic = new Dynamic();
				$dynamic->author_id=$owner_id;
				$dynamic->content_id=$program->d_id;
				$dynamic->status=Constants::OBJECT_APPROVAL;
				$dynamic->create_date=new CDbExpression('NOW()');
				$dynamic->content_type=$program->d_type;
				$dynamic->content_name=$program->d_name;
				$dynamic->dynamic_type=Constants::DYNAMIC_TYPE_LIKE;
				$dynamic->content_pic_url=$program->d_pic;
				$dynamic->save();

				if(isset($program->publish_owner_id) && !is_null($program->publish_owner_id)){

					if($program->publish_owner_id !== $owner_id){
						// add notify msg
						$msg = new NotifyMsg();
						$msg->author_id=$program->publish_owner_id;
						$msg->nofity_user_id=Yii::app()->user->id;
						$msg->notify_user_name=Yii::app()->user->getState("nickname");
						$msg->notify_user_pic_url=Yii::app()->user->getState("pic_url");
						$msg->content_id=$program->d_id;
						$msg->content_info=$program->d_name;
						$msg->content_type=$program->d_type;
						$msg->created_date=new CDbExpression('NOW()');
						$msg->status=Constants::OBJECT_APPROVAL;
						$msg->notify_type=Constants::NOTIFY_TYPE_LIKE_PROGRAM;
						$msg->save();
					}
				}
				$transaction->commit();
				IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);
			} catch (Exception $e) {
				$transaction->rollback();
				IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
			}
			}else {
				IjoyPlusServiceUtils::exportServiceError(Constants::OBJECT_NOT_FOUND);
			}
		}
		
        function actionSupport(){
	        header('Content-type: application/json');
		    if(!Yii::app()->request->isPostRequest){   
		   		 IjoyPlusServiceUtils::exportServiceError(Constants::METHOD_NOT_SUPPORT);
		   		 return ;
		   	}
		    if(!IjoyPlusServiceUtils::validateAPPKey()){
	  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
			   return ;
			}
			$prod_id= Yii::app()->request->getParam("prod_id");
			if( (!isset($prod_id)) || is_null($prod_id)  ){
				IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
				return;
			}
			$program = Program::model()->findByPk($prod_id);
			if($program !== null){
			     $program->good_number=$program->good_number+1;
				 Program::model()->incGoodCount($prod_id);
				 CacheManager::synProgramCache($program);
				 if(IjoyPlusServiceUtils::validateUserID()){
					IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);	
					return ;
				 }
				$owner_id=Yii::app()->user->id;
				$transaction = Yii::app()->db->beginTransaction();
				try {
                    $favority = Dynamic::model()->getDynamicByProd($owner_id, $prod_id,Constants::DYNAMIC_TYPE_MAKE_GOOD);
                 	if(!(isset($favority) && !is_null($favority))){
						$dynamic = new Dynamic();
						$dynamic->author_id=$owner_id;
						$dynamic->content_id=$program->d_id;
						$dynamic->status=Constants::OBJECT_APPROVAL;
						$dynamic->create_date=new CDbExpression('NOW()');
						$dynamic->content_type=$program->d_type;
						$dynamic->content_name=$program->d_name;
						$dynamic->dynamic_type=Constants::DYNAMIC_TYPE_MAKE_GOOD;
						$dynamic->content_pic_url=$program->d_pic;
						$dynamic->save();
	                    User::model()->updateProgramGoodCount($owner_id, 1);
                 	}else {
                 	   IjoyPlusServiceUtils::exportServiceError(Constants::OBJECT_EXIST);
                 	}
					$transaction->commit();
					IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);
				} catch (Exception $e) {
					$transaction->rollback();
					IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
				}
			}else {
				IjoyPlusServiceUtils::exportServiceError(Constants::OBJECT_NOT_FOUND);
			}
		}
		
	   function actionRecordPlay(){
	     header('Content-type: application/json');
		    if(!Yii::app()->request->isPostRequest){   
		   		 IjoyPlusServiceUtils::exportServiceError(Constants::METHOD_NOT_SUPPORT);
		   		 return ;
		   	}
		    if(!IjoyPlusServiceUtils::validateAPPKey()){
	  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
			   return ;
			}
			$prod_id= Yii::app()->request->getParam("prod_id");
			$prod_name= Yii::app()->request->getParam("prod_name");
			$prod_subname= Yii::app()->request->getParam("prod_subname");
			$prod_type= Yii::app()->request->getParam("prod_type");
			
			if( (!isset($prod_id)) || is_null($prod_id) || (!isset($prod_type)) || is_null($prod_type)  ){
				IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
				return;
			}
						
            if(IjoyPlusServiceUtils::validateUserID()){
				IjoyPlusServiceUtils::exportServiceError(Constants::USER_ID_INVALID);	
				return ;
			}
			
			 try{
			    $userid=Yii::app()->user->id;			
				Program::model()->incPlayCount($prod_id);
				$HTTP_CLIENT= isset($_SERVER['HTTP_CLIENT'])?$_SERVER['HTTP_CLIENT']:"";
				$history = new PlayRecords();		
				$history->author_id=$userid;
				$history->client=$HTTP_CLIENT;
				$history->prod_type=$prod_type;
				$history->prod_name=$prod_name;
				$history->prod_subname=$prod_subname;
				$history->prod_id=$prod_id;
				$history->create_date=new CDbExpression('NOW()');
				$history->save();
			    IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);
			  } catch (Exception $e) {
				$transaction->rollback();
				IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
			  }
		
	   }
	   
       function actionPlay(){
	        header('Content-type: application/json');
		    if(!Yii::app()->request->isPostRequest){   
		   		 IjoyPlusServiceUtils::exportServiceError(Constants::METHOD_NOT_SUPPORT);
		   		 return ;
		   	}
		    if(!IjoyPlusServiceUtils::validateAPPKey()){
	  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
			   return ;
			}
			$prod_id= Yii::app()->request->getParam("prod_id");
			$prod_name= Yii::app()->request->getParam("prod_name");
			$prod_subname= Yii::app()->request->getParam("prod_subname");
			$prod_type= Yii::app()->request->getParam("prod_type");
			$play_type= Yii::app()->request->getParam("play_type");
			$playback_time= Yii::app()->request->getParam("playback_time");
			$video_url= Yii::app()->request->getParam("video_url");
			$duration= Yii::app()->request->getParam("duration");
			
			if( (!isset($prod_id)) || is_null($prod_id) || (!isset($prod_type)) || is_null($prod_type)  ){
				IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
				return;
			}
			
            if( (!isset($video_url)) || is_null($video_url)  || (!isset($play_type)) || is_null($play_type)  ){
				IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
				return;
			}
			
            if(IjoyPlusServiceUtils::validateUserID()){
				IjoyPlusServiceUtils::exportServiceError(Constants::USER_ID_INVALID);	
				return ;
			}
			
		 try{
		    $userid=Yii::app()->user->id;
		    		    
			$HTTP_CLIENT= isset($_SERVER['HTTP_CLIENT'])?$_SERVER['HTTP_CLIENT']:"";
			
			if($HTTP_CLIENT ===null || $HTTP_CLIENT ===''){
			  Program::model()->incPlayCount($prod_id);
			}
			if($prod_type ==='3'){
			   $history = PlayHistory::model()->getHisotryByShowProd($userid, $prod_id,$prod_subname);
			}else {
			   $history = PlayHistory::model()->getHisotryByProd($userid, $prod_id);
			}
			if($history === null){
			     $history = new PlayHistory();
			}		
			$history->author_id=$userid;
			$history->prod_type=$prod_type;
			$history->prod_name=$prod_name;
			$history->prod_subname=$prod_subname;
			$history->prod_id=$prod_id;
			$history->status=Constants::OBJECT_APPROVAL;
			$history->play_type=$play_type;
			$history->playback_time=$playback_time;
			$history->video_url=$video_url;
			$history->duration=$duration;
			$history->create_date=new CDbExpression('NOW()');
			$history->save();
		    IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);
		  } catch (Exception $e) {
			$transaction->rollback();
			IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
		  }
		}
		
       function actionSubscribe(){
	        header('Content-type: application/json');
		    if(!Yii::app()->request->isPostRequest){   
		   		 IjoyPlusServiceUtils::exportServiceError(Constants::METHOD_NOT_SUPPORT);
		   		 return ;
		   	}
		    if(!IjoyPlusServiceUtils::validateAPPKey()){
	  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
			   return ;
			}
			$prod_id= Yii::app()->request->getParam("prod_id");
			
			if( (!isset($prod_id)) || is_null($prod_id)  ){
				IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
				return;
			}
           			
            if(IjoyPlusServiceUtils::validateUserID()){
				IjoyPlusServiceUtils::exportServiceError(Constants::USER_ID_INVALID);	
				return ;
			}
			
		 try{
		 	
		    $userid=Yii::app()->user->id;		    		    
			
			$subscribe = Subscribe::model()->getSubscribeByProd($userid,$prod_id);
			if($subscribe === null){
			     $subscribe = new Subscribe();
			     $subscribe->author_id=$userid;
				 $subscribe->prod_id=$prod_id;
				 $subscribe->create_date=new CDbExpression('NOW()');
				 $subscribe->save();
			}	
			
			
		    IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);
		  } catch (Exception $e) {
			IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
		  }
		}
		
       function actionUnSubscribe(){
       	
	        header('Content-type: application/json');
		    if(!Yii::app()->request->isPostRequest){   
		   		 IjoyPlusServiceUtils::exportServiceError(Constants::METHOD_NOT_SUPPORT);
		   		 return ;
		   	}
		    if(!IjoyPlusServiceUtils::validateAPPKey()){
	  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
			   return ;
			}
			$prod_id= Yii::app()->request->getParam("prod_id");
			
			if( (!isset($prod_id)) || is_null($prod_id)  ){
				IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
				return;
			}
           			
            if(IjoyPlusServiceUtils::validateUserID()){
				IjoyPlusServiceUtils::exportServiceError(Constants::USER_ID_INVALID);	
				return ;
			}
			
		 try{
		 	
		    $userid=Yii::app()->user->id;
		    		    
			$subscribe = Subscribe::model()->getSubscribeByProd($userid,$prod_id);
			if($subscribe !== null){
			  $subscribe->delete();
			}
			IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);		
		  } catch (Exception $e) {
			IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
		  }
		}
		
       function actionHiddenPlay(){
	
	        header('Content-type: application/json');
		    if(!Yii::app()->request->isPostRequest){   
		   		 IjoyPlusServiceUtils::exportServiceError(Constants::METHOD_NOT_SUPPORT);
		   		 return ;
		   	}
		   	
		    if(!IjoyPlusServiceUtils::validateAPPKey()){
	  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
			   return ;
			}
			
			$prod_id= Yii::app()->request->getParam("prod_id");
			
			if( (!isset($prod_id)) || is_null($prod_id)   ){
				IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
				return;
			}
			
            if(IjoyPlusServiceUtils::validateUserID()){
				IjoyPlusServiceUtils::exportServiceError(Constants::USER_ID_INVALID);	
				return ;
			}
			
		 try{
		    $userid=Yii::app()->user->id;
			
			$history = PlayHistory::model()->getHisotryByProd($userid, $prod_id);
			
			if($history !== null){
			    $history->status=Constants::OBJECT_DELETE;
			    $history->save();
			}		
			
		    IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);
		  } catch (Exception $e) {
			IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
		  }
		}
		
       function actionInvalid(){
       	
	        header('Content-type: application/json');
	        
		    if(!Yii::app()->request->isPostRequest){   
		   		 IjoyPlusServiceUtils::exportServiceError(Constants::METHOD_NOT_SUPPORT);
		   		 return ;
		   	}
		   	
		    if(!IjoyPlusServiceUtils::validateAPPKey()){
	  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
			   return ;
			}
			
			$prod_id= Yii::app()->request->getParam("prod_id");
			$prod_name= Yii::app()->request->getParam("prod_name");
			$feedback_memo= Yii::app()->request->getParam("memo");
			$prod_type= Yii::app()->request->getParam("prod_type");
			
			if( (!isset($prod_id)) || is_null($prod_id)   ){
				IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
				return;
			}
			
			$invalid_type = Yii::app()->request->getParam("invalid_type");
			IjoyPlusServiceUtils::validateUserID();
            try{
			    $userid=Yii::app()->user->id;
				$HTTP_CLIENT= isset($_SERVER['HTTP_CLIENT'])?$_SERVER['HTTP_CLIENT']:"";
				$history = new VideoFeedback();		
				$history->author_id=$userid;
				$history->client=$HTTP_CLIENT;
				$history->prod_type=$prod_type;
				$history->prod_name=$prod_name;
				$history->feedback_memo=$feedback_memo;
				$history->prod_id=$prod_id;				
				$history->feedback_type=$invalid_type;
				$history->create_date=new CDbExpression('NOW()');
				$history->save();
			} catch (Exception $e) {
				Yii::log( CJSON::encode($e), "error");
			}
            IjoyPlusServiceUtils::exportServiceError(Program::model()->invalid($prod_id));
          
		}
		
        function actionWatch(){
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
			$prod_id= Yii::app()->request->getParam("prod_id");
			if( (!isset($prod_id)) || is_null($prod_id)  ){
				IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
				return;
			}
			$program = Program::model()->findByPk($prod_id);
			if($program !== null){
				$owner_id=Yii::app()->user->id;
				$transaction = Yii::app()->db->beginTransaction();
				try {
                    $favority = Dynamic::model()->getDynamicByProd($owner_id, $prod_id,Constants::DYNAMIC_TYPE_WATCH);
                 	if(!(isset($favority) && !is_null($favority))){
						$dynamic = new Dynamic();
						$dynamic->author_id=$owner_id;
						$dynamic->content_id=$program->d_id;
						$dynamic->status=Constants::OBJECT_APPROVAL;
						$dynamic->create_date=new CDbExpression('NOW()');
						$dynamic->content_type=$program->d_type;
						$dynamic->content_name=$program->d_name;
						$dynamic->dynamic_type=Constants::DYNAMIC_TYPE_WATCH;
						$dynamic->content_pic_url=$program->d_pic;
						$dynamic->save();
//                 	  }
	                    $program->watch_user_count=$program->watch_user_count+1;
						Program::model()->incWatchUserCount($prod_id);
						CacheManager::synProgramCache($program);
//						if(isset($program->publish_owner_id) && !is_null($program->publish_owner_id) && $program->publish_owner_id !== $owner_id){
//							// add notify msg
//							$msg = new NotifyMsg();
//							$msg->author_id=$program->publish_owner_id;
//							$msg->nofity_user_id=Yii::app()->user->id;
//							$msg->notify_user_name=Yii::app()->user->getState("username");
//							$msg->notify_user_pic_url=Yii::app()->user->getState("pic_url");
//							$msg->content_id=$program->d_id;
//							$msg->content_info=$program->d_name;
//							$msg->content_type=$program->d_type;
//							$msg->created_date=new CDbExpression('NOW()');
//							$msg->status=Constants::OBJECT_APPROVAL;
//							$msg->notify_type=Constants::NOTIFY_TYPE_FAVORITY;
//							$msg->save();
//						}
                 	}else {
                 	   IjoyPlusServiceUtils::exportServiceError(Constants::OBJECT_EXIST);
                 	}
					$transaction->commit();
					IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);
				} catch (Exception $e) {
					$transaction->rollback();
					IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
				}
			}else {
				IjoyPlusServiceUtils::exportServiceError(Constants::OBJECT_NOT_FOUND);
			}
		}
		
        function actionIs_favority(){
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
			$prod_id= Yii::app()->request->getParam("prod_id");
			if( (!isset($prod_id)) || is_null($prod_id)  ){
				IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
				return;
			}
			$owner_id=Yii::app()->user->id;
            $favority = Dynamic::model()->getDynamicByProd($owner_id, $prod_id,Constants::DYNAMIC_TYPE_FAVORITY);
            if(isset($favority) && !is_null($favority) && $favority->status ==Constants::OBJECT_APPROVAL){
               IjoyPlusServiceUtils::exportEntity(array('flag'=>true));
            }else {
				IjoyPlusServiceUtils::exportEntity(array('flag'=>false));
			}
		}
		function actionFavority(){
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
			$prod_id= Yii::app()->request->getParam("prod_id");
			if( (!isset($prod_id)) || is_null($prod_id)  ){
				IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
				return;
			}
			$program = Program::model()->findByPk($prod_id);
			if($program !== null){
				$owner_id=Yii::app()->user->id;
				$transaction = Yii::app()->db->beginTransaction();
				try {
                    $favority = Dynamic::model()->getDynamicByProd($owner_id, $prod_id,Constants::DYNAMIC_TYPE_FAVORITY);
                 	if(!(isset($favority) && !is_null($favority) && $favority->status ==Constants::OBJECT_APPROVAL)){
                 	  if(isset($favority) && !is_null($favority)) {
                 	     $favority->status=Constants::OBJECT_APPROVAL;
                 	     $favority->create_date=new CDbExpression('NOW()');
                 	     $favority->save();
                 	  }else{
						$dynamic = new Dynamic();
						$dynamic->author_id=$owner_id;
						$dynamic->content_id=$program->d_id;
						$dynamic->status=Constants::OBJECT_APPROVAL;
						$dynamic->create_date=new CDbExpression('NOW()');
						$dynamic->content_type=$program->d_type;
						$dynamic->content_name=$program->d_name;
						$dynamic->dynamic_type=Constants::DYNAMIC_TYPE_FAVORITY;
						$dynamic->content_pic_url=$program->d_pic;
						$dynamic->save();
                 	  }
	                    $program->favority_user_count=$program->favority_user_count+1;
//						$program->save();
                        Program::model()->incFavorityUserCount($prod_id);
                 	    User::model()->updateFavorityCount($owner_id, 1);
						CacheManager::synProgramCache($program);
//						if(isset($program->publish_owner_id) && !is_null($program->publish_owner_id) && $program->publish_owner_id !== $owner_id){
//							// add notify msg
//							$msg = new NotifyMsg();
//							$msg->author_id=$program->publish_owner_id;
//							$msg->nofity_user_id=Yii::app()->user->id;
//							$msg->notify_user_name=Yii::app()->user->getState("username");
//							$msg->notify_user_pic_url=Yii::app()->user->getState("pic_url");
//							$msg->content_id=$program->d_id;
//							$msg->content_info=$program->d_name;
//							$msg->content_type=$program->d_type;
//							$msg->created_date=new CDbExpression('NOW()');
//							$msg->status=Constants::OBJECT_APPROVAL;
//							$msg->notify_type=Constants::NOTIFY_TYPE_FAVORITY;
//							$msg->save();
//						}
                 	}else {
                 	   IjoyPlusServiceUtils::exportServiceError(Constants::OBJECT_EXIST);
                 	}
					$transaction->commit();
					IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);
				} catch (Exception $e) {
					$transaction->rollback();
					IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
				}
			}else {
				IjoyPlusServiceUtils::exportServiceError(Constants::OBJECT_NOT_FOUND);
			}
		}
		
        function actionRecommend(){
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
			$prod_id= Yii::app()->request->getParam("prod_id");
			if( (!isset($prod_id)) || is_null($prod_id)  ){
				IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
				return;
			}
			
			$program = Program::model()->findByPk($prod_id);
			if($program !== null){
				$owner_id=Yii::app()->user->id;
				$transaction = Yii::app()->db->beginTransaction();
				try {
                    $favority = Dynamic::model()->getDynamicByProd($owner_id, $prod_id,Constants::DYNAMIC_TYPE_RECOMMEND);
//                 	if(!(isset($favority) && !is_null($favority) && $favority->status ==Constants::OBJECT_APPROVAL)){
                    if(!(isset($favority) && !is_null($favority))){
//                 		$program->love_user_count=$program->love_user_count+1;
//				        $program->save();
//				        CacheManager::synProgramCache($program);
//                 	  if(isset($favority) && !is_null($favority)) {
//                 	     $favority->status=Constants::OBJECT_APPROVAL;
//                 	     $favority->create_date=new CDbExpression('NOW()');                 	     
//                 	     $favority->save();
//                 	  }else{
						$dynamic = new Dynamic();
						$dynamic->author_id=$owner_id;
						$dynamic->content_id=$program->d_id;
						$dynamic->status=Constants::OBJECT_APPROVAL;
						$dynamic->create_date=new CDbExpression('NOW()');
						$dynamic->content_type=$program->d_type;
						$dynamic->content_name=$program->d_name;						
						$dynamic->content_desc=Yii::app()->request->getParam("reason");
						$dynamic->dynamic_type=Constants::DYNAMIC_TYPE_RECOMMEND;
						$dynamic->content_pic_url=$program->d_pic;
						$dynamic->save();
//                 	  }
	                    
//						if(isset($program->publish_owner_id) && !is_null($program->publish_owner_id) && $program->publish_owner_id !== $owner_id){
//							// add notify msg
//							$msg = new NotifyMsg();
//							$msg->author_id=$program->publish_owner_id;
//							$msg->nofity_user_id=Yii::app()->user->id;
//							$msg->notify_user_name=Yii::app()->user->getState("username");
//							$msg->notify_user_pic_url=Yii::app()->user->getState("pic_url");
//							$msg->content_id=$program->d_id;
//							$msg->content_info=$program->d_name;
//							$msg->content_type=$program->d_type;
//							$msg->created_date=new CDbExpression('NOW()');
//							$msg->status=Constants::OBJECT_APPROVAL;
//							$msg->notify_type=Constants::NOTIFY_TYPE_FAVORITY;
//							$msg->save();
//						}
                 	}else {
                 	   IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);
                 	}
					$transaction->commit();
					IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);
				} catch (Exception $e) {
					$transaction->rollback();
					IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
				}
			}else {
				IjoyPlusServiceUtils::exportServiceError(Constants::OBJECT_NOT_FOUND);
			}
		}
		
       
		
       function actionHiddenWatch(){
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
			$prod_id= Yii::app()->request->getParam("prod_id");
			if( (!isset($prod_id)) || is_null($prod_id)  ){
				IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
				return;
			}
			$program = CacheManager::getProgramCache($prod_id);
			if($program !== null){
				$owner_id=Yii::app()->user->id;
				$transaction = Yii::app()->db->beginTransaction();
				try {
					$favority = Dynamic::model()->getDynamicByProd($owner_id ,$prod_id,Constants::DYNAMIC_TYPE_WATCH);
                 	if(isset($favority) && !is_null($favority) && $favority->status ==Constants::OBJECT_APPROVAL){                 		                 		
						$favority->status=Constants::OBJECT_DELETE;						
						$favority->save();
//						$dynamic = new Dynamic();
//						$dynamic->author_id=$owner_id;
//						$dynamic->content_id=$program->d_id;
//						$dynamic->status=Constants::OBJECT_APPROVAL;
//						$dynamic->create_date=new CDbExpression('NOW()');
//						$dynamic->content_type=$program->d_type;
//						$dynamic->content_name=$program->d_name;
//						$dynamic->dynamic_type=Constants::DYNAMIC_TYPE_UN_FAVORITY;
//						$dynamic->content_pic_url=$program->d_pic;
//						$dynamic->save();
//	
//						if(isset($program->publish_owner_id) && !is_null($program->publish_owner_id) && $program->publish_owner_id !== $owner_id){
//							// add notify msg
//							$msg = new NotifyMsg();
//							$msg->author_id=$program->publish_owner_id;
//							$msg->nofity_user_id=Yii::app()->user->id;
//							$msg->notify_user_name=Yii::app()->user->getState("username");
//							$msg->notify_user_pic_url=Yii::app()->user->getState("pic_url");
//							$msg->content_id=$program->d_id;
//							$msg->content_info=$program->d_name;
//							$msg->content_type=$program->d_type;
//							$msg->created_date=new CDbExpression('NOW()');
//							$msg->status=Constants::OBJECT_APPROVAL;
//							$msg->notify_type=Constants::NOTIFY_TYPE_UN_FAVORITY;
//							$msg->save();
//						}
                 	}
					$transaction->commit();
					IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);
				} catch (Exception $e) {
					$transaction->rollback();
					IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
				}
			}else {
				IjoyPlusServiceUtils::exportServiceError(Constants::OBJECT_NOT_FOUND);
			}
		}
		
		function actionUnfavority(){
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
			
			$prod_id= Yii::app()->request->getParam("prod_id");
			if( (!isset($prod_id)) || is_null($prod_id)  ){
				IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
				return;
			}
			
			$program = Program::model()->findByPk($prod_id);
			if($program !== null){
				$owner_id=Yii::app()->user->id;
				$transaction = Yii::app()->db->beginTransaction();
				try {
					$favority = Dynamic::model()->getFavorityByProd($owner_id, $prod_id);
                 	if(isset($favority) && !is_null($favority) && $favority->status ==Constants::OBJECT_APPROVAL){
                 		
						$favority->status=Constants::OBJECT_DELETE;
						$favority->save();
                 	    if( $program->favority_user_count >=1){
		                   $program->favority_user_count=$program->favority_user_count-1;
		                   $program->save();
		                 }
		                 if( $program->favority_user_count <0){
		                 	$program->favority_user_count=0;
		                    $program->save();
		                 }
						User::model()->updateFavorityCount($owner_id, -1);
	                    CacheManager::synProgramCache($program);
//						$dynamic = new Dynamic();
//						$dynamic->author_id=$owner_id;
//						$dynamic->content_id=$program->d_id;
//						$dynamic->status=Constants::OBJECT_APPROVAL;
//						$dynamic->create_date=new CDbExpression('NOW()');
//						$dynamic->content_type=$program->d_type;
//						$dynamic->content_name=$program->d_name;
//						$dynamic->dynamic_type=Constants::DYNAMIC_TYPE_UN_FAVORITY;
//						$dynamic->content_pic_url=$program->d_pic;
//						$dynamic->save();
	
//						if(isset($program->publish_owner_id) && !is_null($program->publish_owner_id) && $program->publish_owner_id !== $owner_id){
//							// add notify msg
//							$msg = new NotifyMsg();
//							$msg->author_id=$program->publish_owner_id;
//							$msg->nofity_user_id=Yii::app()->user->id;
//							$msg->notify_user_name=Yii::app()->user->getState("username");
//							$msg->notify_user_pic_url=Yii::app()->user->getState("pic_url");
//							$msg->content_id=$program->d_id;
//							$msg->content_info=$program->d_name;
//							$msg->content_type=$program->d_type;
//							$msg->created_date=new CDbExpression('NOW()');
//							$msg->status=Constants::OBJECT_APPROVAL;
//							$msg->notify_type=Constants::NOTIFY_TYPE_UN_FAVORITY;
//							$msg->save();
//						}
                 	}
					$transaction->commit();
					IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);
				} catch (Exception $e) {
					$transaction->rollback();
					IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
				}
			}else {
				IjoyPlusServiceUtils::exportServiceError(Constants::OBJECT_NOT_FOUND);
			}
		}
		

		function actionHiddenRecommend(){
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
			$prod_id= Yii::app()->request->getParam("prod_id");
			if( (!isset($prod_id)) || is_null($prod_id)  ){
				IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
				return;
			}
			$program = Program::model()->findByPk($prod_id);
			if($program !== null){
				$owner_id=Yii::app()->user->id;
				$transaction = Yii::app()->db->beginTransaction();
				try {
					$favority = Dynamic::model()->getDynamicByProd($owner_id, $prod_id,Constants::DYNAMIC_TYPE_RECOMMEND);
                 	if(isset($favority) && !is_null($favority) && $favority->status ==Constants::OBJECT_APPROVAL){                 		
						$favority->status=Constants::OBJECT_DELETE;
						$favority->save();
						
//						$program->love_user_count=$program->love_user_count-1;
//						$program->save();
//	                    CacheManager::synProgramCache($program);
//						$dynamic = new Dynamic();
//						$dynamic->author_id=$owner_id;
//						$dynamic->content_id=$program->d_id;
//						$dynamic->status=Constants::OBJECT_APPROVAL;
//						$dynamic->create_date=new CDbExpression('NOW()');
//						$dynamic->content_type=$program->d_type;
//						$dynamic->content_name=$program->d_name;
//						$dynamic->dynamic_type=Constants::DYNAMIC_TYPE_UN_FAVORITY;
//						$dynamic->content_pic_url=$program->d_pic;
//						$dynamic->save();
//	
//						if(isset($program->publish_owner_id) && !is_null($program->publish_owner_id) && $program->publish_owner_id !== $owner_id){
//							// add notify msg
//							$msg = new NotifyMsg();
//							$msg->author_id=$program->publish_owner_id;
//							$msg->nofity_user_id=Yii::app()->user->id;
//							$msg->notify_user_name=Yii::app()->user->getState("username");
//							$msg->notify_user_pic_url=Yii::app()->user->getState("pic_url");
//							$msg->content_id=$program->d_id;
//							$msg->content_info=$program->d_name;
//							$msg->content_type=$program->d_type;
//							$msg->created_date=new CDbExpression('NOW()');
//							$msg->status=Constants::OBJECT_APPROVAL;
//							$msg->notify_type=Constants::NOTIFY_TYPE_UN_FAVORITY;
//							$msg->save();
//						}
                 	}
					$transaction->commit();
					IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);
				} catch (Exception $e) {
					$transaction->rollback();
					IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
				}
			}else {
				IjoyPlusServiceUtils::exportServiceError(Constants::OBJECT_NOT_FOUND);
			}
		}
        
		
		function actionShare(){
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
			$prod_id= Yii::app()->request->getParam("prod_id");
			if( (!isset($prod_id)) || is_null($prod_id)  ){
				IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
				return;
			}
			$program = Program::model()->findByPk($prod_id);
			if($program !== null){
				$owner_id=Yii::app()->user->id;
				$transaction = Yii::app()->db->beginTransaction();
				try {
					$favority = Dynamic::model()->getDynamicByProd($owner_id, $prod_id,Constants::DYNAMIC_TYPE_SHARE);
                 	if(!(isset($favority) && !is_null($favority) )){                 		
						$dynamic = new Dynamic();
						$dynamic->author_id=$owner_id;
						$dynamic->content_id=$program->d_id;
						$dynamic->status=Constants::OBJECT_APPROVAL;
						$dynamic->create_date=new CDbExpression('NOW()');
						$dynamic->content_type=$program->d_type;
						$dynamic->content_name=$program->d_name;
						$dynamic->dynamic_type=Constants::DYNAMIC_TYPE_SHARE;
						$dynamic->content_pic_url=$program->d_pic;
//						$dynamic->content_desc=$share_to_where;
						$dynamic->save();
                 		User::model()->updateShareCount($owner_id, 1);
                 		 Program::model()->incShareCount($prod_id);
                 		$program->share_number=$program->share_number+1;
						CacheManager::synProgramCache($program);
                 	}
//					if(isset($program->publish_owner_id) && !is_null($program->publish_owner_id) && $program->publish_owner_id !== $owner_id){
//						// add notify msg
//						$msg = new NotifyMsg();
//						$msg->author_id=$program->publish_owner_id;
//						$msg->nofity_user_id=Yii::app()->user->id;
//						$msg->notify_user_name=Yii::app()->user->getState("username");
//						$msg->notify_user_pic_url=Yii::app()->user->getState("pic_url");
//						$msg->content_id=$program->d_id;
//						$msg->content_info=$program->d_name;
//						$msg->content_type=$program->d_type;
//						$msg->created_date=new CDbExpression('NOW()');
//						$msg->status=Constants::OBJECT_APPROVAL;
//						$msg->notify_type=Constants::NOTIFY_TYPE_SHARE;
//						$msg->content_desc=$share_to_where;
//						$msg->save();
//					}
					$transaction->commit();
					IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);
				} catch (Exception $e) {
					$transaction->rollback();
					IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
				}
			}else {
				IjoyPlusServiceUtils::exportServiceError(Constants::OBJECT_NOT_FOUND);
			}
		}

		public function  actionComments(){
	        header('Content-type: application/json');
		    if(!IjoyPlusServiceUtils::validateAPPKey()){
	  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
			   return ;
			}
			
			$prod_id= Yii::app()->request->getParam("prod_id");
			if( (!isset($prod_id)) || is_null($prod_id)  ){
				IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
				return;
			}
			$page_size=Yii::app()->request->getParam("page_size");
			$page_num=Yii::app()->request->getParam("page_num");
			if(!(isset($page_size) && is_numeric($page_size))){
				$page_size=10;
				$page_num=1;
			}else if(!(isset($page_num) && is_numeric($page_num))){
				$page_num=1;
			}
			$comments= Comment::model()->getCommentsByProgram($prod_id,$page_size,$page_size*($page_num-1));
			if(isset($comments) && is_array($comments)){
				$commentTemps = array();
				foreach ($comments as $comment){
					$commentTemps[]=IjoyPlusServiceUtils::transferComments($comment);
				}
				IjoyPlusServiceUtils::exportEntity(array('comments'=>$commentTemps));
			}else {
				IjoyPlusServiceUtils::exportEntity(array('comments'=>array()));
			}
		}
		
       public function  actionReviews(){
	        header('Content-type: application/json');
		    if(!IjoyPlusServiceUtils::validateAPPKey()){
	  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
			   return ;
			}
			
			$prod_id= Yii::app()->request->getParam("prod_id");
			if( (!isset($prod_id)) || is_null($prod_id)  ){
				IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
				return;
			}
			$page_size=Yii::app()->request->getParam("page_size");
			$page_num=Yii::app()->request->getParam("page_num");
			if(!(isset($page_size) && is_numeric($page_size))){
				$page_size=10;
				$page_num=1;
			}else if(!(isset($page_num) && is_numeric($page_num))){
				$page_num=1;
			}
			
			$comments= Comment::model()->getReviewsByProgram($prod_id,$page_size,$page_size*($page_num-1));
			if(isset($comments) && is_array($comments)){				
				IjoyPlusServiceUtils::exportEntity(array('reviews'=>$comments));
			}else {
				IjoyPlusServiceUtils::exportEntity(array('reviews'=>array()));
			}
		}
		
		/**
		 * Creates a new model.
		 * If creation is successful, the browser will be redirected to the 'view' page.
		 */
		public function actionComment(){
	        header('Content-type: application/json');
//		    if(!Yii::app()->request->isPostRequest){   
//		   		 IjoyPlusServiceUtils::exportServiceError(Constants::METHOD_NOT_SUPPORT);
//		   		 return ;
//		   	}
		    if(!IjoyPlusServiceUtils::validateAPPKey()){
	  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
			   return ;
			}
	        if(IjoyPlusServiceUtils::validateUserID()){
				IjoyPlusServiceUtils::exportServiceError(Constants::USER_ID_INVALID);	
				return ;
			}
		    if(!IjoyPlusServiceUtils::checkCSRCToken()){
		     IjoyPlusServiceUtils::exportServiceError(Constants::DUPLICAT_REQUEST);	
		    	return ;			
		    }
			$prod_id= Yii::app()->request->getParam("prod_id");
			if( (!isset($prod_id)) || is_null($prod_id)  ){
				IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
				return;
			}
			$model=new Comment;
			$model->status=Constants::OBJECT_APPROVAL;
			$model->create_date=new CDbExpression('NOW()');
			$model->comments = Yii::app()->request->getParam("content");
			$model->content_id = Yii::app()->request->getParam("prod_id");
			$model->author_id = Yii::app()->user->id;
			$model->author_username=Yii::app()->user->getState("nickname");
			$model->author_photo_url=Yii::app()->user->getState("pic_url");
//			var_dump($model->comments);
			if($model->createComments()){
		      IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);
			}else{
		      IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
			}
		}

		public function actionView(){
            header('Content-type: application/json');
		    if(!IjoyPlusServiceUtils::validateAPPKey()){
	  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
			   return ;
			}
//			if(Yii::app()->user->isGuest){
//				IjoyPlusServiceUtils::exportServiceError(Constants::SEESION_IS_EXPIRED);	
//				return ;
//			}
			$prod_id= Yii::app()->request->getParam("prod_id");
			if( (!isset($prod_id)) || is_null($prod_id)  ){
				IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
				return;
			}

			$program= CacheManager::getProgramCache($prod_id);
            //var_dump($program->can_play_device);
			if($program === null){
		      IjoyPlusServiceUtils::exportServiceError(Constants::OBJECT_NOT_FOUND);
		      return;
			}

			$prod =ProgramUtil::exportProgramEntity($program);
			
            if(isset($program->can_play_device) && $program->can_play_device !== '0'){
              $prod['can_play_device']=$program->can_play_device;
            } else {
              $prod['can_play_device']='';
            }
			$comments = Comment::model()->getCommentsByProgram($prod_id,10,0);
			$commentTemps = array();
			if(isset($comments) && is_array($comments)){
				foreach ($comments as $comment){
					$commentTemps[]=IjoyPlusServiceUtils::transferComments($comment);
				}
			}
			$prod['comments']=$commentTemps;
		    $topics= Topic::model()->getRelatedTops($prod_id);
		    if(isset($topics) && is_array($topics)){
		    	$prod['topics']=$topics;
		    }else{
		    	$prod['topics']=array();
		    }
			IjoyPlusServiceUtils::exportEntity($prod);
		}
		
        public function actionRelatedVideos(){
            header('Content-type: application/json');
		    if(!IjoyPlusServiceUtils::validateAPPKey()){
	  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
			   return ;
			}
			$prod_id= Yii::app()->request->getParam("prod_id");
			if( (!isset($prod_id)) || is_null($prod_id)  ){
				IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
				return;
			}  
			      
	        $key ='PROGRAM_RELEATE_VEDIO_LIST_PROD_ID'.$prod_id;
	        $lists = CacheManager::getValueFromCache($key);
		    if($lists){
		    	IjoyPlusServiceUtils::exportEntity(array('items'=>$lists));
		    }
		    		
			$prodExpired = CacheManager::getExpireByCache(CacheManager::CACHE_PARAM_EXPIRED_POPULAR_PROGRAM);
			$sql="SELECT topic_id FROM mac_vod_topic_items,mac_vod_topic WHERE flag=1 and  topic_id=t_id and t_bdtype=1 and vod_id =".$prod_id;
			$lists= Yii::app()->db->createCommand($sql)->queryAll();
			$movie=array();
			if(isset($lists) && !is_null($lists)){
				foreach ($lists as $list){
				  $movie[]=$list['topic_id'];
				}
			}
			
	        $device=IjoyPlusServiceUtils::getDevice();	   	    
	   	    if($device ===false){
	            $where=' ';
	   	    }else {
	   	    	$where=' AND (can_search_device like \'%'.$device.'%\' or can_search_device is null or can_search_device =\'\' ) ';
	   	    	
	   	    }	
	   	    
	       if(count($movie) ===0){
		    	$sql='SELECT  d_type_name  FROM mac_vod WHERE d_id ='.$prod_id;
				$d_type_name = Yii::app()->db->createCommand($sql)->queryRow();
				$d_type_name=$d_type_name['d_type_name'];
				$d_type_name =str_replace(",", " ", $d_type_name);
				$d_type_name=explode(" ", $d_type_name);
				foreach ($d_type_name as $typename){
				  $where=$where.' and d_type_name like \'%'.$typename.'%\' ';
				  break;
				}
				$sql='SELECT  d_id as prod_id, d_name as prod_name, d_level as definition, d_type as prod_type,d_pic as prod_pic_url,  substring_index( d_pic_ipad, \'{Array}\', 1 )  as big_prod_pic_url,d_content as prod_sumary,d_starring as star,d_directed as director,d_score as score ,favority_user_count as favority_num ,good_number as support_num ,d_year as publish_date,d_area as area, d_remarks as max_episode, d_state as cur_episode, duraning as duration   FROM mac_vod WHERE d_hide =0 '.$where.' AND  d_id !='.$prod_id.' ORDER BY   d_play_num DESC  limit 0 , 6 ';
				$lists= Yii::app()->db->createCommand($sql)->queryAll();
				if(count($lists)>0){
			  	    CacheManager::setValueToCache($key, $lists,$prodExpired);
			  	}
				IjoyPlusServiceUtils::exportEntity(array('items'=>$lists));
				
		    }else {
			   	$topicid=implode(",", $movie);			   	
//			    $sql='SELECT  count(DISTINCT d_id) as num FROM mac_vod, mac_vod_topic_items WHERE   flag=1 and d_hide =0 AND vod_id = d_id AND topic_id in ('.$topicid.') and d_id !='.$prod_id;
//				$nums = Yii::app()->db->createCommand($sql)->queryRow();
//				$nums=$nums['num'];
				if(true){
				  $sql='SELECT  DISTINCT d_id as prod_id, d_name as prod_name, d_level as definition, d_type as prod_type,d_pic as prod_pic_url,  substring_index( d_pic_ipad, \'{Array}\', 1 )  as big_prod_pic_url,d_content as prod_sumary,d_starring as star,d_directed as director,d_score as score ,favority_user_count as favority_num ,good_number as support_num ,d_year as publish_date,d_area as area, d_remarks as max_episode, d_state as cur_episode, duraning as duration  FROM mac_vod, mac_vod_topic_items WHERE   flag=1 '.$where.' and d_hide =0 AND vod_id = d_id AND topic_id in ('.$topicid.') and d_id !='.$prod_id.' ORDER BY disp_order DESC , d_level DESC , d_play_num DESC , d_type ASC , d_good DESC , d_time DESC limit 0,6 ';
				}else {
					 $random = rand(0, $nums-6);
					 $sql='SELECT  DISTINCT d_id as prod_id, d_name as prod_name, d_level as definition, d_type as prod_type,d_pic as prod_pic_url,  substring_index( d_pic_ipad, \'{Array}\', 1 )  as big_prod_pic_url,d_content as prod_sumary,d_starring as star,d_directed as director,d_score as score ,favority_user_count as favority_num ,good_number as support_num ,d_year as publish_date,d_area as area, d_remarks as max_episode, d_state as cur_episode, duraning as duration  FROM mac_vod, mac_vod_topic_items WHERE   flag=1 and d_hide =0 AND vod_id = d_id AND topic_id in ('.$topicid.') and d_id !='.$prod_id.' ORDER BY disp_order DESC , d_level DESC , d_play_num DESC , d_type ASC , d_good DESC , d_time DESC  limit '.$random." , 6";
				}
				
				$lists= Yii::app()->db->createCommand($sql)->queryAll();
				
				if(count($lists)>0){
			  	    CacheManager::setValueToCache($key, $lists,$prodExpired);
			  	}
				IjoyPlusServiceUtils::exportEntity(array('items'=>$lists));
				
		    }
			
		}

		public function actionViewRecommend(){
	        header('Content-type: application/json');
		    if(!IjoyPlusServiceUtils::validateAPPKey()){
	  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
			   return ;
			}
	        if(IjoyPlusServiceUtils::validateUserID()){
				IjoyPlusServiceUtils::exportServiceError(Constants::USER_ID_INVALID);	
				return ;
			}
			$prod_id= Yii::app()->request->getParam("prod_id");
			if( (!isset($prod_id)) || is_null($prod_id)  ){
				IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
				return;
			}
	
		    $program= Program::model()->findByPk($prod_id);
	
		    if($program === null){
		    	IjoyPlusServiceUtils::exportServiceError(Constants::OBJECT_NOT_FOUND);
		    	return;
		    }
		    
			$userid=Yii::app()->request->getParam("user_id");
	   		if( (!isset($userid)) || is_null($userid)  ){
				$userid=Yii::app()->user->id;	   			
	   		}
	
		    $prod =ProgramUtil::exportProgramEntity($program);
	        $reCom = Dynamic::model()->getDynamicByProd($userid,$prod_id ,Constants::DYNAMIC_TYPE_RECOMMEND);
	        if(isset($reCom) && !is_null($reCom)){
	          $prod['reason']=$reCom->content_desc;
	        }
		    $comments = Comment::model()->getCommentsByProgram($prod_id,10,0);
		    if(isset($comments) && is_array($comments)){
		    	$commentTemps = array();
		    	foreach ($comments as $comment){
		    		$commentTemps[]=IjoyPlusServiceUtils::transferComments($comment);
		    	}
		    	$prod['comments']=$commentTemps;
		    }else {
		    	$prod['comments']=array();
		    }
		    $dynamic = Dynamic::model()->friendDynamicForProgram(Yii::app()->user->id,$prod_id,10,0);
		    if(isset($dynamic) && is_array($dynamic)){
		    	$prod['dynamics']=$this->transferDynamics($dynamic);
		    }else{
		    	$prod['dynamics']=array();
		    }
		    $topics= Topic::model()->getRelatedTops($prod_id);
		    if(isset($topics) && is_array($topics)){
		    	$prod['topics']=$topics;
		    }else{
		    	$prod['topics']=array();
		    }
		    IjoyPlusServiceUtils::exportEntity($prod);
		}
		
       private function transferDynamics($dynamics){
    	 $temp =array();
    	 foreach ($dynamics as $dynamic){
    	   switch ($dynamic['dynamic_type']){
    	   	case Constants::DYNAMIC_TYPE_WATCH:
    	   	  $temp[] = array(
    	   	    'type'=>'watch',
    	   	    'user_id'=>$dynamic['friend_id'],
    	   	    'user_name'=>$dynamic['friend_username'],
    	   	    'user_pic_url'=>$dynamic['friend_photo_url'],
    	   	    'create_date'=>$dynamic['create_date'],
    	   	  );
    	   	  break;

    	   	  case Constants::DYNAMIC_TYPE_SHARE:
    	   	  $temp[] = array(
    	   	    'type'=>'share',
    	   	    'user_id'=>$dynamic['friend_id'],
    	   	    'user_name'=>$dynamic['friend_username'],
    	   	    'user_pic_url'=>$dynamic['friend_photo_url'],
    	   	    'create_date'=>$dynamic['create_date'],
    	   	    'share_where_type'=>$dynamic['content_desc'],
    	   	  );
    	   	  break;
    	   	  
    	   	  case Constants::DYNAMIC_TYPE_COMMENTS:
    	   	  $temp[] = array(
    	   	    'type'=>'comment',
    	   	    'user_id'=>$dynamic['friend_id'],
    	   	    'user_name'=>$dynamic['friend_username'],
    	   	    'user_pic_url'=>$dynamic['friend_photo_url'],
    	   	    'create_date'=>$dynamic['create_date'],
    	   	    'content'=>$dynamic['content_desc'],
    	   	  );
    	   	  break;
    	   	  
    	   	  case Constants::DYNAMIC_TYPE_FAVORITY:
    	   	  $temp[] = array(
    	   	    'type'=>'favority',
    	   	    'user_id'=>$dynamic['friend_id'],
    	   	    'user_name'=>$dynamic['friend_username'],
    	   	    'user_pic_url'=>$dynamic['friend_photo_url'],
    	   	    'create_date'=>$dynamic['create_date'],
    	   	  );
    	   	  break;
    	   	  
    	   	  case Constants::DYNAMIC_TYPE_LIKE:
    	   	  $temp[] = array(
    	   	    'type'=>'like',
    	   	    'user_id'=>$dynamic['friend_id'],
    	   	    'user_name'=>$dynamic['friend_username'],
    	   	    'user_pic_url'=>$dynamic['friend_photo_url'],
    	   	    'create_date'=>$dynamic['create_date'],
    	   	  );
    	   	  break;
    	   	  
    	   	 case Constants::DYNAMIC_TYPE_PUBLISH_PROGRAM:
    	   	  $temp[] = array(
    	   	    'type'=>'publish',
    	   	    'user_id'=>$dynamic['friend_id'],
    	   	    'user_name'=>$dynamic['friend_username'],
    	   	    'user_pic_url'=>$dynamic['friend_photo_url'],
    	   	    'create_date'=>$dynamic['create_date'],
    	   	  );
    	   	  break;
    	   	  
    	   	  case Constants::DYNAMIC_TYPE_RECOMMEND:
    	   	  $temp[] = array(
    	   	    'type'=>'recommend',
    	   	    'user_id'=>$dynamic['friend_id'],
    	   	    'user_name'=>$dynamic['friend_username'],
    	   	    'user_pic_url'=>$dynamic['friend_photo_url'],
    	   	    'create_date'=>$dynamic['create_date'],
    	   	    'reason'=>$dynamic['content_desc'],
    	   	  );
    	   	  break;
    	   	  
    	   }
    	}
    	return $temp;
    }
	
	}
