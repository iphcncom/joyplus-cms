<?php

class ProgramController extends Controller
{


	function actionPublish(){
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
			  $dynamic->content_id=$model->id;
		   	  $dynamic->status=Constants::OBJECT_APPROVAL;
			  $dynamic->create_date=new CDbExpression('NOW()');
			  $dynamic->content_type=$model->pro_type;
			  $dynamic->content_name=$model->name;
			  $dynamic->dynamic_type=Constants::DYNAMIC_TYPE_PUBLISH_PROGRAM;
			  $dynamic->content_pic_url=$model->poster;
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
				$program->save();

				$dynamic = new Dynamic();
				$dynamic->author_id=$owner_id;
				$dynamic->content_id=$program->id;
				$dynamic->status=Constants::OBJECT_APPROVAL;
				$dynamic->create_date=new CDbExpression('NOW()');
				$dynamic->content_type=$program->pro_type;
				$dynamic->content_name=$program->name;
				$dynamic->dynamic_type=Constants::DYNAMIC_TYPE_LIKE;
				$dynamic->content_pic_url=$program->poster;
				$dynamic->save();

				if(isset($program->publish_owner_id) && !is_null($program->publish_owner_id)){

					if($program->publish_owner_id !== $owner_id){
						// add notify msg
						$msg = new NotifyMsg();
						$msg->author_id=$program->publish_owner_id;
						$msg->nofity_user_id=Yii::app()->user->id;
						$msg->notify_user_name=Yii::app()->user->getState("username");
						$msg->notify_user_pic_url=Yii::app()->user->getState("pic_url");
						$msg->content_id=$program->id;
						$msg->content_info=$program->name;
						$msg->content_type=$program->pro_type;
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

		function actionWatch(){
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
			$prod_id= Yii::app()->request->getParam("prod_id");
			if( (!isset($prod_id)) || is_null($prod_id)  ){
				IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
				return;
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
					$program->watch_user_count=$program->watch_user_count+1;
					$program->save();

					$dynamic = new Dynamic();
					$dynamic->author_id=$owner_id;
					$dynamic->content_id=$program->id;
					$dynamic->status=Constants::OBJECT_APPROVAL;
					$dynamic->create_date=new CDbExpression('NOW()');
					$dynamic->content_type=$program->pro_type;
					$dynamic->content_name=$program->name;
					$dynamic->dynamic_type=Constants::DYNAMIC_TYPE_WATCH;
					$dynamic->content_pic_url=$program->poster;
					$dynamic->save();

					if(isset($program->publish_owner_id) && !is_null($program->publish_owner_id) && $program->publish_owner_id !== $owner_id){
						// add notify msg
						$msg = new NotifyMsg();
						$msg->author_id=$program->publish_owner_id;
						$msg->nofity_user_id=Yii::app()->user->id;
						$msg->notify_user_name=Yii::app()->user->getState("username");
						$msg->notify_user_pic_url=Yii::app()->user->getState("pic_url");
						$msg->content_id=$program->id;
						$msg->content_info=$program->name;
						$msg->content_type=$program->pro_type;
						$msg->created_date=new CDbExpression('NOW()');
						$msg->status=Constants::OBJECT_APPROVAL;
						$msg->notify_type=Constants::NOTIFY_TYPE_WATCH_PROGRAM;
						$msg->save();
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

		function actionFavority(){
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
			$prod_id= Yii::app()->request->getParam("prod_id");
			if( (!isset($prod_id)) || is_null($prod_id)  ){
				IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
				return;
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
                 	if(!(isset($favority) && !is_null($favority) && $favority->status ==Constants::OBJECT_APPROVAL)){
                 	  if(isset($favority) && !is_null($favority)) {
                 	     $favority->status=Constants::OBJECT_APPROVAL;
                 	     $favority->save();
                 	  }else{
						$dynamic = new Dynamic();
						$dynamic->author_id=$owner_id;
						$dynamic->content_id=$program->id;
						$dynamic->status=Constants::OBJECT_APPROVAL;
						$dynamic->create_date=new CDbExpression('NOW()');
						$dynamic->content_type=$program->pro_type;
						$dynamic->content_name=$program->name;
						$dynamic->dynamic_type=Constants::DYNAMIC_TYPE_FAVORITY;
						$dynamic->content_pic_url=$program->poster;
						$dynamic->save();
                 	  }
	                    $program->favority_user_count=$program->favority_user_count+1;
						$program->save();
						if(isset($program->publish_owner_id) && !is_null($program->publish_owner_id) && $program->publish_owner_id !== $owner_id){
							// add notify msg
							$msg = new NotifyMsg();
							$msg->author_id=$program->publish_owner_id;
							$msg->nofity_user_id=Yii::app()->user->id;
							$msg->notify_user_name=Yii::app()->user->getState("username");
							$msg->notify_user_pic_url=Yii::app()->user->getState("pic_url");
							$msg->content_id=$program->id;
							$msg->content_info=$program->name;
							$msg->content_type=$program->pro_type;
							$msg->created_date=new CDbExpression('NOW()');
							$msg->status=Constants::OBJECT_APPROVAL;
							$msg->notify_type=Constants::NOTIFY_TYPE_FAVORITY;
							$msg->save();
						}
                 	}else {
                 	   IjoyPlusServiceUtils::exportServiceError(Constants::PROGRAM_IS_FAVORITY);
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
			$prod_id= Yii::app()->request->getParam("prod_id");
			if( (!isset($prod_id)) || is_null($prod_id)  ){
				IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
				return;
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
						
						$program->favority_user_count=$program->favority_user_count-1;
						$program->save();
	
						$dynamic = new Dynamic();
						$dynamic->author_id=$owner_id;
						$dynamic->content_id=$program->id;
						$dynamic->status=Constants::OBJECT_APPROVAL;
						$dynamic->create_date=new CDbExpression('NOW()');
						$dynamic->content_type=$program->pro_type;
						$dynamic->content_name=$program->name;
						$dynamic->dynamic_type=Constants::DYNAMIC_TYPE_UN_FAVORITY;
						$dynamic->content_pic_url=$program->poster;
						$dynamic->save();
	
						if(isset($program->publish_owner_id) && !is_null($program->publish_owner_id) && $program->publish_owner_id !== $owner_id){
							// add notify msg
							$msg = new NotifyMsg();
							$msg->author_id=$program->publish_owner_id;
							$msg->nofity_user_id=Yii::app()->user->id;
							$msg->notify_user_name=Yii::app()->user->getState("username");
							$msg->notify_user_pic_url=Yii::app()->user->getState("pic_url");
							$msg->content_id=$program->id;
							$msg->content_info=$program->name;
							$msg->content_type=$program->pro_type;
							$msg->created_date=new CDbExpression('NOW()');
							$msg->status=Constants::OBJECT_APPROVAL;
							$msg->notify_type=Constants::NOTIFY_TYPE_UN_FAVORITY;
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

		function actionShare(){
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
			$prod_id= Yii::app()->request->getParam("prod_id");
			if( (!isset($prod_id)) || is_null($prod_id)  ){
				IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
				return;
			}
			$prod_id= Yii::app()->request->getParam("prod_id");
			$share_to_where= Yii::app()->request->getParam("where");
			if(!IjoyPlusServiceUtils::validateThirdPartSource($share_to_where)){
				IjoyPlusServiceUtils::exportServiceError(Constants::THIRD_PART_SOURCE_TYPE_INVALID);
			}
			if( (!isset($prod_id)) || is_null($prod_id)  ){
				IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
				return;
			}
			$program = Program::model()->findByPk($prod_id);
			if($program !== null){
				$owner_id=Yii::app()->user->id;
				$transaction = Yii::app()->db->beginTransaction();
				try {
					$dynamic = new Dynamic();
					$dynamic->author_id=$owner_id;
					$dynamic->content_id=$program->id;
					$dynamic->status=Constants::OBJECT_APPROVAL;
					$dynamic->create_date=new CDbExpression('NOW()');
					$dynamic->content_type=$program->pro_type;
					$dynamic->content_name=$program->name;
					$dynamic->dynamic_type=Constants::DYNAMIC_TYPE_SHARE;
					$dynamic->content_pic_url=$program->poster;
					$dynamic->content_desc=$share_to_where;
					$dynamic->save();

					if(isset($program->publish_owner_id) && !is_null($program->publish_owner_id) && $program->publish_owner_id !== $owner_id){
						// add notify msg
						$msg = new NotifyMsg();
						$msg->author_id=$program->publish_owner_id;
						$msg->nofity_user_id=Yii::app()->user->id;
						$msg->notify_user_name=Yii::app()->user->getState("username");
						$msg->notify_user_pic_url=Yii::app()->user->getState("pic_url");
						$msg->content_id=$program->id;
						$msg->content_info=$program->name;
						$msg->content_type=$program->pro_type;
						$msg->created_date=new CDbExpression('NOW()');
						$msg->status=Constants::OBJECT_APPROVAL;
						$msg->notify_type=Constants::NOTIFY_TYPE_SHARE;
						$msg->content_desc=$share_to_where;
						$msg->save();
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

		public function  actionComments(){
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
			$prod_id= Yii::app()->request->getParam("prod_id");
			if( (!isset($prod_id)) || is_null($prod_id)  ){
				IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
				return;
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
		/**
		 * Creates a new model.
		 * If creation is successful, the browser will be redirected to the 'view' page.
		 */
		public function actionComment(){
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
			$model->author_username=Yii::app()->user->getState("username");
			$model->author_photo_url=Yii::app()->user->getState("pic_url");
			//var_dump($model);
			if($model->createComments()){
		      IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);
			}else{
		      IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
			}
		}

		public function actionView(){
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
			$prod_id= Yii::app()->request->getParam("prod_id");
			if( (!isset($prod_id)) || is_null($prod_id)  ){
				IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
				return;
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

			$prod = array();
			switch ($program->pro_type){
				case Constants::PROGRAM_TYPE_TV:
					$prod['tv']=$this->genTV($program);
					break;
						
				case Constants::PROGRAM_TYPE_SHOW:
					$prod['show']=$this->genTVShow($program);
					break;
						
				case Constants::PROGRAM_TYPE_MOVIE:
					$prod['movie']=$this->genMovie($program);
					break;
			}

			$comments = Comment::model()->getCommentsByProgram($prod_id,10,0);
			$commentTemps = array();
			if(isset($comments) && is_array($comments)){
				foreach ($comments as $comment){
					$commentTemps[]=IjoyPlusServiceUtils::transferComments($comment);
				}
			}
			$prod['comments']=$commentTemps;
			IjoyPlusServiceUtils::exportEntity($prod);
		}

		public function actionViewRecommend(){
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
			$prod_id= Yii::app()->request->getParam("prod_id");
			if( (!isset($prod_id)) || is_null($prod_id)  ){
				IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
				return;
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
	
		    $prod = array();
		    switch ($program->pro_type){
		    	case Constants::PROGRAM_TYPE_TV:
		    		$prod=$this->genTV($program);
		    		break;
	
		    	case Constants::PROGRAM_TYPE_SHOW:
		    		$prod=$this->genTVShow($program);
		    		break;
	
		    	case Constants::PROGRAM_TYPE_MOVIE:
		    		$prod=$this->genMovie($program);
		    		break;
		    }
	
		    $comments = Comment::model()->getCommentsByProgram($prod_id,5,0);
		    if(isset($comments) && is_array($comments)){
		    	$commentTemps = array();
		    	foreach ($comments as $comment){
		    		$commentTemps[]=IjoyPlusServiceUtils::transferComments($comment);
		    	}
		    	$prod['comments']=$commentTemps;
		    }else {
		    	$prod['comments']=array();
		    }
		    $dynamic = Dynamic::model()->friendDynamicForProgram(Yii::app()->user->id,$prod_id,5,0);
		    if(isset($dynamic) && is_array($dynamic)){
		    	$prod['dynamics']=$this->transferDynamics($dynamic);
		    }else{
		    	$prod['dynamics']=array();
		    }
		    IjoyPlusServiceUtils::exportEntity($prod);
		}
		
       private function transferDynamics($dynamics){
    	 $temp =array();
    	 foreach ($dynamics as $dynamic){
    	   switch ($dynamic['dynamic_type']){
    	   	case Constants::DYNAMIC_TYPE_WATCH:
    	   	  $temp["watch"] = array(
    	   	    'user_id'=>$dynamic['friend_id'],
    	   	    'user_name'=>$dynamic['friend_username'],
    	   	    'user_pic_url'=>$dynamic['friend_photo_url'],
    	   	    'create_date'=>$dynamic['create_date'],
    	   	  );
    	   	  break;

    	   	  case Constants::DYNAMIC_TYPE_SHARE:
    	   	  $temp["share"] = array(
    	   	    'user_id'=>$dynamic['friend_id'],
    	   	    'user_name'=>$dynamic['friend_username'],
    	   	    'user_pic_url'=>$dynamic['friend_photo_url'],
    	   	    'create_date'=>$dynamic['create_date'],
    	   	    'share_where_type'=>$dynamic['content_desc'],
    	   	  );
    	   	  break;
    	   	  
    	   	  case Constants::DYNAMIC_TYPE_COMMENTS:
    	   	  $temp["comment"] = array(
    	   	    'user_id'=>$dynamic['friend_id'],
    	   	    'user_name'=>$dynamic['friend_username'],
    	   	    'user_pic_url'=>$dynamic['friend_photo_url'],
    	   	    'create_date'=>$dynamic['create_date'],
    	   	    'content'=>$dynamic['content_desc'],
    	   	  );
    	   	  break;
    	   	  
    	   	  case Constants::DYNAMIC_TYPE_FAVORITY:
    	   	  $temp["favority"] = array(
    	   	    'user_id'=>$dynamic['friend_id'],
    	   	    'user_name'=>$dynamic['friend_username'],
    	   	    'user_pic_url'=>$dynamic['friend_photo_url'],
    	   	    'create_date'=>$dynamic['create_date'],
    	   	  );
    	   	  break;
    	   	  
    	   	  case Constants::DYNAMIC_TYPE_LIKE:
    	   	  $temp["like"] = array(
    	   	    'user_id'=>$dynamic['friend_id'],
    	   	    'user_name'=>$dynamic['friend_username'],
    	   	    'user_pic_url'=>$dynamic['friend_photo_url'],
    	   	    'create_date'=>$dynamic['create_date'],
    	   	  );
    	   	  break;
    	   	  
    	   	 case Constants::DYNAMIC_TYPE_PUBLISH_PROGRAM:
    	   	  $temp["publish"] = array(
    	   	    'user_id'=>$dynamic['friend_id'],
    	   	    'user_name'=>$dynamic['friend_username'],
    	   	    'user_pic_url'=>$dynamic['friend_photo_url'],
    	   	    'create_date'=>$dynamic['create_date'],
    	   	  );
    	   	  break;
    	   	  
    	   }
    	}
    	return $temp;
    }
	private function genTV($program){
	  $prod= array(
          'name'=>$program->name,
          'summary'=>$program->summary,
          'poster'=>$program->poster,
          'closed'=>$program->enable,
          'episodes_count'=>1,
          'sources'=>$program->sources,
          'like_num'=>$program->love_user_count,
          'watch_num'=>$program->watch_user_count,
          'favority_num'=>$program->favority_user_count,       
		);
		$episodes= array();
		$videos = Program::model()->getVedios($program->id);

		if(isset($videos) &&is_array($videos)){
		$name='';
		$video_urls = array();
		$episodeIndex=0;
		foreach ($videos as $video){
			$video_urls[]=array(
                'source'=>$video->source_id,
                'url'=>$video->url,
							);
			if($name !=='' && $name !== $video->name){
			   $episodes[]=array(
	                'name'=>$name,
	                'video_urls'=>$video_urls,
				);
				$video_urls = array();
				$name= $video->name;
			}else{
			  if($name ===''){
				$name= $video->name;
				if(count($videos) ===1){
				  $episodes[]=array(
	                'name'=>$name,
	                'video_urls'=>$video_urls,);}
				}									
			}
		}
	  }
	  $prod['episodes']=$episodes;
	  return $prod;
   }

   private function genMovie($program){
	  $prod= array(
          'name'=>$program->name,
          'summary'=>$program->summary,
          'poster'=>$program->poster,
          'like_num'=>$program->love_user_count,
          'watch_num'=>$program->watch_user_count,
          'favority_num'=>$program->favority_user_count,       
	  );
	  $video_urls= array();
	  $videos = Program::model()->getVedios($program->id);
	  if(isset($videos) &&is_array($videos)){
	    foreach ($videos as $video){
		  $video_urls[]=array(
                'source'=>$video->source_id,
                'url'=>$video->url,
		  );
		}
	  }
	  $prod['video_urls']=$video_urls;
	  return $prod;
    }

	private function genTVShow($program){
	   $prod= array(
          'name'=>$program->name,
          'summary'=>$program->summary,
          'poster'=>$program->poster,
          'closed'=>$program->enable,
          'episodes_count'=>1,
          'sources'=>$program->sources,
          'like_num'=>$program->love_user_count,
          'watch_num'=>$program->watch_user_count,
          'favority_num'=>$program->favority_user_count,       
		);
		$episodes= array();
		$videos = Program::model()->getVedios($program->id);

		if(isset($videos) &&is_array($videos)){
			$name='';
			$video_urls = array();
			foreach ($videos as $video){
			  $video_urls[]=array(
                'source'=>$video->source_id,
                'url'=>$video->url,
			  );
			  if($name !=='' && $name !== $video->name){
				$episodes[]=array(
	                'name'=>$name,
	                'video_urls'=>$video_urls,
				);
				$video_urls = array();
				$name= $video->name;
			  }else{
				if($name ===''){
					$name= $video->name;
					if(count($videos) ===1){
						$episodes[]=array(
                          'name'=>$name,
                          'video_urls'=>$video_urls,
						);
				     }
				}
              }
			}
		  }
		  $prod['episodes']=$episodes;
		  return $prod;
		}
	}
