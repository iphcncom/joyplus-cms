<?php

class FriendController extends Controller
{   
	/**
	 * ???????
	 * Enter description here ...
	 */
	public function actionFollow(){
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
		
		$friend_id = Yii::app()->request->getParam("friend_ids");
		if( (!isset($friend_id)) || is_null($friend_id)  ){
		   IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
		   return;
		}
		$friends=explode(',',$friend_id);
		if (Friend::model()->followFriends($friends)) {
			IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);	
		}else{
		  IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);	
		}
	}
	public function actionLike(){
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
		
		$user_id = Yii::app()->request->getParam("user_id");
	    $ownerid = Yii::app()->user->id;
		if( (!isset($user_id)) || is_null($user_id) || $ownerid ===  $user_id ){
		   IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
		   return;
		}
		try{
			$transaction = Yii::app()->db->beginTransaction(); 
			$user =User::model()->findByPk($user_id);
            if(isset($user) && !is_null($user)){
               IjoyPlusServiceUtils::exportServiceError(Constants::OBJECT_NOT_FOUND);	
            }   
               $dynamicObj = Dynamic::model()->find(array(
		      	'condition'=>'author_id=:author_id and content_id=:friend_id and status=:status and dynamic_type=:type',
			    'params'=>array(
			      ':author_id'=>$userid,
			      ':friend_id'=>$user_id,
			      ':type'=>Constants::DYNAMIC_TYPE_LIKE_FRIEND,
			      ':status'=>Constants::OBJECT_APPROVAL,
			     ),
	         ));
	         
              if( !(isset($dynamicObj) && !is_null($dynamicObj))){
                
                 $user->like_number=$user->like_number+1;
                 $user->save();
                 
                     //add dynamic
			     $dynamic = new Dynamic();
		         $dynamic->author_id=$userid;
		         $dynamic->content_id=$user_id;
		         $dynamic->status=Constants::OBJECT_APPROVAL;
		         $dynamic->create_date=new CDbExpression('NOW()');
		         $dynamic->content_name=$user->nickname; 
		         $dynamic->dynamic_type=Constants::DYNAMIC_TYPE_LIKE_FRIEND;
		         $dynamic->content_pic_url=$user->user_photo_url;
		         $dynamic->save();	
		         
		         //ADD NOTIFY MSG
		          $msg = new NotifyMsg();
			      $msg->author_id=$user_id;
			      $msg->nofity_user_id=Yii::app()->user->id;
			      $msg->notify_user_name=Yii::app()->user->getState("nickname");
		          $msg->notify_user_pic_url=Yii::app()->user->getState("pic_url");			      
			      $msg->created_date=new CDbExpression('NOW()');
			      $msg->status=Constants::OBJECT_APPROVAL;
			      $msg->notify_type=Constants::NOTIFY_TYPE_LIKE_FRIEND;
			      $msg->save();
			      $transaction->commit();
			      IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);	
                }else {
                  IjoyPlusServiceUtils::exportServiceError(Constants::PERSON_IS_LIKED);
                }
		}catch (Exception $e){
			$transaction->rollback();
			IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
		}
		
	  
	}
	
	/**
	 * ????? 
	 * Enter description here ...
	 */
	public function actionDestory(){
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
	    $friend_id = Yii::app()->request->getParam("friend_ids");
		if( (!isset($friend_id)) || is_null($friend_id)  ){
		   IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
		   return;
		}
		$friends=explode(',',$friend_id);
		if (Friend::model()->destroyFriends($friends)) {
			IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);	
		}else{
		  IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);	;
		}
	}
	
	public function actionRecommends(){
        header('Content-type: application/json');
	    if(!IjoyPlusServiceUtils::validateAPPKey()){
  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
		   return ;
		}
        if(IjoyPlusServiceUtils::validateUserID()){
			IjoyPlusServiceUtils::exportServiceError(Constants::USER_ID_INVALID);	
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
        
        try {
        	$recommends=Dynamic::model()->friendRecommends(Yii::app()->user->id,$page_size,$page_size*($page_num-1));
            if(isset($recommends) && !is_null($recommends) && is_array($recommends)){					
			  IjoyPlusServiceUtils::exportEntity(array('recommends'=>$recommends));
			}else {
			  IjoyPlusServiceUtils::exportEntity(array('recommends'=>array()));
			}
        } catch (Exception $e) {
        	IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
        }
	}
}
