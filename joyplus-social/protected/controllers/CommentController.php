<?php

class CommentController extends Controller
{

	function actionView(){
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
		$thread_id=Yii::app()->request->getParam("thread_id");
		if( (!isset($thread_id)) || is_null($thread_id)  ){
			IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
			return;
		}

		$comment= Comment::model()->findByPk($thread_id);
		if($comment=== null){
			IjoyPlusServiceUtils::exportEntity(array('comment'=>array()));
		}else {
		    $temp = IjoyPlusServiceUtils::transferComments($comment);
		    $comments= Comment::model()->getCommentReplies($thread_id,10,0);
			$commentTemps = array();
			if(isset($comments) && is_array($comments)){
				foreach ($comments as $comment){
					$commentTemps[]=IjoyPlusServiceUtils::transferComments($comment);					
				}
			}
			$temp->replies=$commentTemps;
			IjoyPlusServiceUtils::exportEntity(array('comment'=>$temp));
		} 

	}

	public function  actionReplies(){
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
		$thread_id= Yii::app()->request->getParam("thread_id");
		if( (!isset($thread_id)) || is_null($thread_id)  ){
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
		$comments= Comment::model()->getCommentReplies($thread_id,$page_size,$page_size*($page_num-1));
		if(isset($comments) && is_array($comments)){
			$commentTemps = array();
			$count=0;
			foreach ($comments as $comment){
				$commentTemps[$count]=IjoyPlusServiceUtils::transferComments($comment);
				$count++;
			}
			IjoyPlusServiceUtils::exportEntity(array('replies'=>$commentTemps));
		}else {
			IjoyPlusServiceUtils::exportEntity(array('replies'=>array()));
		}
	}

	function actionReply(){
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
	    $transaction = Yii::app()->db->beginTransaction(); 
        try {
          $thread_id=Yii::app()->request->getParam("thread_id");
          $parentComm = Comment::model()->findByPk($thread_id);
          if(isset($parentComm) && !is_null($parentComm)){       	 	  	  
			$model=new Comment;
			$model->status=Constants::OBJECT_APPROVAL;
			$model->create_date=new CDbExpression('NOW()');
			$model->comments = Yii::app()->request->getParam("content");
			$model->thread_id = Yii::app()->request->getParam("thread_id");
			$model->author_id = Yii::app()->user->id;
			$model->author_username=Yii::app()->user->getState("username");
			$model->author_photo_url=Yii::app()->user->getState("pic_url");
			$model->save();
                 //add dynamic
          $dynamic = new Dynamic();
	      $dynamic->author_id=Yii::app()->user->id;
	      $dynamic->content_id=$thread_id;
	      $dynamic->status=Constants::OBJECT_APPROVAL;
	      $dynamic->create_date=new CDbExpression('NOW()');
	      $dynamic->content_desc=$model->comments;
	      $dynamic->dynamic_type=Constants::DYNAMIC_TYPE_COMMENT_REPLI;
	      $dynamic->content_name=$parentComm->content_name;
	      $dynamic->content_type=$parentComm->content_type;
	      $dynamic->content_pic_url=$parentComm->content_pic_url;
	      $dynamic->save();
	      
	      // add notify msg		      
	      $msg = new NotifyMsg();
	      $msg->author_id=$parentComm->author_id;
	      $msg->nofity_user_id=Yii::app()->user->id;
	      $msg->notify_user_name=Yii::app()->user->getState("username");
	      $msg->notify_user_pic_url=Yii::app()->user->getState("pic_url");
	      $msg->content_id=$thread_id;
	      $msg->content_desc=$model->comments;
	      $msg->content_type=$parentComm->content_type;
	      $msg->content_info=$parentComm->content_name;
	      $msg->created_date=new CDbExpression('NOW()');
	      $msg->status=Constants::OBJECT_APPROVAL;
	      $msg->notify_type=Constants::NOTIFY_TYPE_REPLIE_COMMENT;
	      $msg->save();
        }
	    $transaction->commit();
	    IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);
       } catch (Exception $e) {
       	  $transaction->rollback();
       	  IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
       }
	}
}
