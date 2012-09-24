<?php

class UserController extends Controller
{
	public function actionThirdpartUsers(){
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
		if( (!isset($source_type)) || is_null($source_type)  ){
			IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
			return;
		}
		if(IjoyPlusServiceUtils::validateThirdPartSource($source_type)){
			try{
				while(Yii::app()->user->hasState("generateUsersByThirdPart_".$source_type) && Yii::app()->user->getState("generateUsersByThirdPart_".$source_type) === '1'){
					sleep(2);
				}
				$users = User::model()->searchThirdPartUsers(Yii::app()->user->id,$source_type);
				if(isset($users) && !is_null($users) && is_array($users)){
					IjoyPlusServiceUtils::exportEntity(array('users'=>$users));
				}else {
					IjoyPlusServiceUtils::exportEntity(array('users'=>array()));
				}
			}catch(Exception $e){
				IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
			}
		}else{
			IjoyPlusServiceUtils::exportServiceError(Constants::THIRD_PART_SOURCE_TYPE_INVALID);
		}
	}

	public function actionPreGenThirdpartUsers(){
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
		$sourceid= Yii::app()->request->getParam("source_ids");
		$source_type= Yii::app()->request->getParam("source_type");
		if( (!isset($sourceid)) || is_null($sourceid)  ){
			IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
			return;
		}
		if(IjoyPlusServiceUtils::validateThirdPartSource($source_type)){
			try{
				Yii::app()->user->setState("generateUsersByThirdPart_".$source_type,'1');
				User::model()->generateUsersByThirdPart($source_type, explode(',', $sourceid));
				Yii::app()->user->setState("generateUsersByThirdPart_".$source_type,'0');
				IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);
			}catch(Exception $e){
				Yii::app()->user->setState("generateUsersByThirdPart_".$source_type,'0');
				IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
			}
		}else{
			IjoyPlusServiceUtils::exportServiceError(Constants::THIRD_PART_SOURCE_TYPE_INVALID);
		}
	}

	public function actionFans(){
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
		$userid=Yii::app()->request->getParam("userid");
		if( (!isset($userid)) || is_null($userid)  ){
			$userid=Yii::app()->user->id;
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
			$fans=Friend::model()->searchFans($userid,$page_size,$page_size*($page_num-1));
			if(isset($fans) && !is_null($fans) && is_array($fans)){
		  IjoyPlusServiceUtils::exportEntity(array('fans'=>$fans));
			}else {
		  IjoyPlusServiceUtils::exportEntity(array('fans'=>array()));
			}
		} catch (Exception $e) {
			IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
		}
	}

	public function actionFriends(){
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
		$userid=Yii::app()->request->getParam("userid");
		if( (!isset($userid)) || is_null($userid)  ){
		   $userid=Yii::app()->user->id;
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
			$friends=Friend::model()->searchFriends($userid,$page_size,$page_size*($page_num-1));
			if(isset($friends) && !is_null($friends) && is_array($friends)){
				IjoyPlusServiceUtils::exportEntity(array('friends'=>$friends));
			}else {
				IjoyPlusServiceUtils::exportEntity(array('friends'=>array()));
			}
		} catch (Exception $e) {
			IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
		}
		IjoyPlusServiceUtils::exportEntity(Friend::model()->searchFriends($userid,$limit,$offset));
	}

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
	   	$userid=Yii::app()->request->getParam("userid");
	   	if( (!isset($userid)) || is_null($userid)  ){
	   		$userid=Yii::app()->user->id;
	   	}
	   	try {
	   		$user=User::model()->findByPk($userid);
	   		if($user === null){
	   			IjoyPlusServiceUtils::exportServiceError(Constants::OBJECT_NOT_FOUND);
	   		}else {
	   			$temp = array(
				     'id'=>$user->id,
				     'username'=>$user->username,
				     'email'=>$user->email,
				     'phone'=>$user->phone,
				     'pic_url'=>$user->user_photo_url,
				     'bg_url'=>$user->user_bg_photo_url,
				     'like_num'=>$user->like_number,
				     'follow_num'=>$user->watch_number,
				     'fan_num'=>$user->fan_number,
	   			);
	   			IjoyPlusServiceUtils::exportEntity($temp);
	   		}
	   	}catch (Exception $e){
	   		IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
	   	}
	}

	public function actionWatchs(){
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
   		$userid=Yii::app()->request->getParam("userid");
   		if( (!isset($userid)) || is_null($userid)  ){
   			$userid=Yii::app()->user->id;
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
   			$watchs=Dynamic::model()->searchUserWatchs($userid,$page_size,$page_size*($page_num-1));
   			if(isset($watchs) && !is_null($watchs) && is_array($watchs)){
   				IjoyPlusServiceUtils::exportEntity(array('watchs'=>$watchs));
   			}else {
   				IjoyPlusServiceUtils::exportEntity(array('watchs'=>array()));
   			}
   		} catch (Exception $e) {
   			IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
   		}
	}
	
	public function actionFriendAndMeDynamics(){
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
		$userid=Yii::app()->request->getParam("user_id");
		if( (!isset($userid)) || is_null($userid)  ){
		   $userid=Yii::app()->user->id;
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
			$favorities=Dynamic::model()->friendAndMeDynamics($userid,$page_size,$page_size*($page_num-1));
			if(isset($favorities) && !is_null($favorities) && is_array($favorities)){
				IjoyPlusServiceUtils::exportEntity(array('dynamics'=>$this->transferDynamic($favorities)));
			}else {
				IjoyPlusServiceUtils::exportEntity(array('dynamics'=>array()));
			}
		} catch (Exception $e) {
			IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
		}
	}
	
	
   public function actionFriendDynamics(){
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
   
		$userid=Yii::app()->request->getParam("user_id");
		if( (!isset($userid)) || is_null($userid)  ){
		   $userid=Yii::app()->user->id;
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
			$favorities=Dynamic::model()->friendDynamics($userid,$page_size,$page_size*($page_num-1));
			if(isset($favorities) && !is_null($favorities) && is_array($favorities)){
				IjoyPlusServiceUtils::exportEntity(array('dynamics'=>$this->transferDynamic($favorities)));
			}else {
				IjoyPlusServiceUtils::exportEntity(array('dynamics'=>array()));
			}
		} catch (Exception $e) {
			IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
		}
	}
	
public function actionOwnDynamics(){
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

		$userid=Yii::app()->request->getParam("user_id");
		if( (!isset($userid)) || is_null($userid)  ){
		   $userid=Yii::app()->user->id;
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
			$favorities=Dynamic::model()->myDynamics($userid,$page_size,$page_size*($page_num-1));
			if(isset($favorities) && !is_null($favorities) && is_array($favorities)){
				IjoyPlusServiceUtils::exportEntity(array('dynamics'=>$this->transferDynamic($favorities)));
			}else {
				IjoyPlusServiceUtils::exportEntity(array('dynamics'=>array()));
			}
		} catch (Exception $e) {
			IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
		}
	}
	
	private function transferDynamic($dynamics){
		$temp =array();
		foreach ($dynamics as $dynamic){
			$user=CacheManager::getUserByID($dynamic['author_id']);
			switch ($dynamic['dynamic_type']){
				case Constants::DYNAMIC_TYPE_WATCH:
					$temp["watch"] = array(
		    	   	    'user_id'=>$user->id,
		    	   	    'user_name'=>$user->username,
		    	   	    'user_pic_url'=>$user->user_pic_url,
		    	   	    'create_date'=>$dynamic['create_date'],
		    	   	    'prod_type'=>$dynamic['content_type'],
		    	   	    'prod_name'=>$dynamic['content_name'],
		    	   	    'prod_poster'=>$dynamic['content_pic_url'],
		    	   	    'prod_id'=>$dynamic['content_id'],
                      );
                      break;

				case Constants::DYNAMIC_TYPE_SHARE:
					$temp["share"] = array(
		    	   	    'user_id'=>$user->id,
		    	   	    'user_name'=>$user->username,
		    	   	    'user_pic_url'=>$user->user_pic_url,
		    	   	    'create_date'=>$dynamic['create_date'],
		    	   	    'prod_type'=>$dynamic['content_type'],
		    	   	    'prod_name'=>$dynamic['content_name'],
		    	   	    'prod_poster'=>$dynamic['content_pic_url'],
		    	   	    'prod_id'=>$dynamic['content_id'],
		    	   	    'share_where_type'=>$dynamic['content_desc'],
					);
					break;

				case Constants::DYNAMIC_TYPE_COMMENTS:
					$temp["comment"] = array(
		    	   	    'user_id'=>$user->id,
		    	   	    'user_name'=>$user->username,
		    	   	    'user_pic_url'=>$user->user_pic_url,
		    	   	    'create_date'=>$dynamic['create_date'],
		    	   	    'prod_type'=>$dynamic['content_type'],
		    	   	    'prod_name'=>$dynamic['content_name'],
		    	   	    'prod_poster'=>$dynamic['content_pic_url'],
		    	   	    'prod_id'=>$dynamic['content_id'],
		    	   	    'content'=>$dynamic['content_desc'],
					);
					break;

				case Constants::DYNAMIC_TYPE_FAVORITY:
					$temp["favority"] = array(
		    	   	    'user_id'=>$user->id,
		    	   	    'user_name'=>$user->username,
		    	   	    'user_pic_url'=>$user->user_pic_url,
		    	   	    'create_date'=>$dynamic['create_date'],
		    	   	    'prod_type'=>$dynamic['content_type'],
		    	   	    'prod_name'=>$dynamic['content_name'],
		    	   	    'prod_poster'=>$dynamic['content_pic_url'],
		    	   	    'prod_id'=>$dynamic['content_id'],
					);
					break;

				case Constants::DYNAMIC_TYPE_LIKE:
					$temp["like"] = array(
		    	   	    'user_id'=>$user->id,
		    	   	    'user_name'=>$user->username,
		    	   	    'user_pic_url'=>$user->user_pic_url,
		    	   	    'create_date'=>$dynamic['create_date'],
		    	   	    'prod_type'=>$dynamic['content_type'],
		    	   	    'prod_name'=>$dynamic['content_name'],
		    	   	    'prod_poster'=>$dynamic['content_pic_url'],
		    	   	    'prod_id'=>$dynamic['content_id'],
					);
					break;

				case Constants::DYNAMIC_TYPE_PUBLISH_PROGRAM:
					$temp["publish"] = array(
		    	   	    'user_id'=>$user->id,
		    	   	    'user_name'=>$user->username,
		    	   	    'user_pic_url'=>$user->user_pic_url,
		    	   	    'create_date'=>$dynamic['create_date'],
		    	   	    'prod_type'=>$dynamic['content_type'],
		    	   	    'prod_name'=>$dynamic['content_name'],
		    	   	    'prod_poster'=>$dynamic['content_pic_url'],
		    	   	    'prod_id'=>$dynamic['content_id'],
					);
					break;

				case Constants::DYNAMIC_TYPE_UN_FAVORITY:
					$temp["unfavority"] = array(
		    	   	    'user_id'=>$user->id,
		    	   	    'user_name'=>$user->username,
		    	   	    'user_pic_url'=>$user->user_pic_url,
		    	   	    'create_date'=>$dynamic['create_date'],
		    	   	    'prod_type'=>$dynamic['content_type'],
		    	   	    'prod_name'=>$dynamic['content_name'],
		    	   	    'prod_poster'=>$dynamic['content_pic_url'],
		    	   	    'prod_id'=>$dynamic['content_id'],
					);
					break;

				case Constants::DYNAMIC_TYPE_COMMENT_REPLI:
					$prod = CacheManager::getCommentProgram($dynamic['content_id']);
					$temp["reply"] = array(
		    	   	    'user_id'=>$user->id,
		    	   	    'user_name'=>$user->username,
		    	   	    'user_pic_url'=>$user->user_pic_url,
		    	   	    'create_date'=>$dynamic['create_date'],
		    	   	    'prod_type'=>$prod['type'],
		    	   	    'prod_id'=>$prod['id'],
		    	   	    'prod_name'=>$prod['name'],
		    	   	    'prod_poster'=>$prod['poster'],
		    	   	    'thread_id'=>$dynamic['content_id'],
		    	   	    'thread_comment'=>CacheManager::getCommentContent($dynamic['content_id']),
		    	   	    'content'=>$dynamic['content_desc'],
					);
					break;

				case Constants::DYNAMIC_TYPE_FOLLOW:
					$temp["follow"] = array(
		    	   	    'user_id'=>$user->id,
		    	   	    'user_name'=>$user->username,
		    	   	    'user_pic_url'=>$user->user_pic_url,
		    	   	    'create_date'=>$dynamic['create_date'],
		    	   	    'friend_name'=>$dynamic['content_name'],
		    	   	    'friend_pic_url'=>$dynamic['content_pic_url'],
		    	   	    'friend_id'=>$dynamic['content_id'],
					);
					break;
					
					
				case Constants::DYNAMIC_TYPE_LIKE_FRIEND:
					$temp["like_person"] = array(
		    	   	    'user_id'=>$user->id,
		    	   	    'user_name'=>$user->username,
		    	   	    'user_pic_url'=>$user->user_pic_url,
		    	   	    'create_date'=>$dynamic['create_date'],
		    	   	    'friend_name'=>$dynamic['content_name'],
		    	   	    'friend_pic_url'=>$dynamic['content_pic_url'],
		    	   	    'friend_id'=>$dynamic['content_id'],
					);
					break;

				case Constants::DYNAMIC_TYPE_UN_FOLLOW:
					$temp["destory"] = array(
		    	   	    'user_id'=>$user->id,
		    	   	    'user_name'=>$user->username,
		    	   	    'user_pic_url'=>$user->user_pic_url,
		    	   	    'create_date'=>$dynamic['create_date'],
		    	   	    'friend_name'=>$dynamic['content_name'],
		    	   	    'friend_pic_url'=>$dynamic['content_pic_url'],
		    	   	    'friend_id'=>$dynamic['content_id'],
					);
					break;
			}
		  }
		  return $temp;
	 }
	public function actionFavorities(){
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
   		$userid=Yii::app()->request->getParam("userid");
   		if( (!isset($userid)) || is_null($userid)  ){
   			$userid=Yii::app()->user->id;
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
   			$favorities=Dynamic::model()->searchUserFavorities($userid,$page_size,$page_size*($page_num-1));
   			if(isset($favorities) && !is_null($favorities) && is_array($favorities)){
   				IjoyPlusServiceUtils::exportEntity(array('favorities'=>$favorities));
   			}else {
   				IjoyPlusServiceUtils::exportEntity(array('favorities'=>array()));
   			}
   		} catch (Exception $e) {
   			IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
   		}
	}

	public function actionMsgs(){
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
   		$page_size=Yii::app()->request->getParam("page_size");
   		$page_num=Yii::app()->request->getParam("page_num");
   		if(!(isset($page_size) && is_numeric($page_size))){
   			$page_size=10;
   			$page_num=1;
   		}else if(!(isset($page_num) && is_numeric($page_num))){
   			$page_num=1;
   		}

   		try {
   			$msgs=NotifyMsg::model()->myNotifyMsgs(Yii::app()->user->id,$page_size,$page_size*($page_num-1));
   			if(isset($msgs) && !is_null($msgs) && is_array($msgs)){
   				IjoyPlusServiceUtils::exportEntity(array('msgs'=>$this->transferMsgs($msgs)));
   			}else {
   				IjoyPlusServiceUtils::exportEntity(array('msgs'=>array()));
   			}
   		} catch (Exception $e) {
   			IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
   		}
	}
    private function transferMsgs($msgs){
		$temp =array();
		foreach ($msgs as $msg){
		  switch ($msg['notify_type']){
			case Constants::NOTIFY_TYPE_COMMENT:
             $temp["comment"] = array(
    	   	    'user_id'=>$msg['notify_user_id'],
    	   	    'user_name'=>$msg['notify_user_name'],
    	   	    'user_pic_url'=>$msg['notify_user_pic_url'],
    	   	    'create_date'=>$msg['created_date'],
    	   	    'prod_type'=>$msg['content_type'],
    	   	    'prod_name'=>$msg['content_info'],
    	   	    'prod_id'=>$msg['content_id'],
			    'content'=>$msg['content_desc'],
                      );
			  break;
			case Constants::NOTIFY_TYPE_FAVORITY:
			  $temp["favority"] = array(
    	   	    'user_id'=>$msg['notify_user_id'],
    	   	    'user_name'=>$msg['notify_user_name'],
    	   	    'user_pic_url'=>$msg['notify_user_pic_url'],
    	   	    'create_date'=>$msg['created_date'],
    	   	    'prod_type'=>$msg['content_type'],
    	   	    'prod_name'=>$msg['content_info'],
    	   	    'prod_id'=>$msg['content_id'],
                      );
			  break;
			case Constants::NOTIFY_TYPE_FOLLOW:
			  $temp["follow"] = array(
    	   	    'user_id'=>$msg['notify_user_id'],
    	   	    'user_name'=>$msg['notify_user_name'],
    	   	    'user_pic_url'=>$msg['notify_user_pic_url'],
    	   	    'create_date'=>$msg['created_date'],
                      );
			  break;					  
			case Constants::NOTIFY_TYPE_LIKE_FRIEND:
			  $temp["like_person"] = array(
    	   	    'user_id'=>$msg['notify_user_id'],
    	   	    'user_name'=>$msg['notify_user_name'],
    	   	    'user_pic_url'=>$msg['notify_user_pic_url'],
    	   	    'create_date'=>$msg['created_date'],
                      );
			  break;
			case Constants::NOTIFY_TYPE_UN_FOLLOW:
			  $temp["destory"] = array(
    	   	    'user_id'=>$msg['notify_user_id'],
    	   	    'user_name'=>$msg['notify_user_name'],
    	   	    'user_pic_url'=>$msg['notify_user_pic_url'],
    	   	    'create_date'=>$msg['created_date'],
                      );
			  break;
			case Constants::NOTIFY_TYPE_LIKE_PROGRAM:
			  $temp["like"] = array(
    	   	    'user_id'=>$msg['notify_user_id'],
    	   	    'user_name'=>$msg['notify_user_name'],
    	   	    'user_pic_url'=>$msg['notify_user_pic_url'],
    	   	    'create_date'=>$msg['created_date'],
    	   	    'prod_type'=>$msg['content_type'],
    	   	    'prod_name'=>$msg['content_info'],
    	   	    'prod_id'=>$msg['content_id'],
                      );
			  break;
			case Constants::NOTIFY_TYPE_UN_FAVORITY:
			  $temp["unfavority"] = array(
    	   	    'user_id'=>$msg['notify_user_id'],
    	   	    'user_name'=>$msg['notify_user_name'],
    	   	    'user_pic_url'=>$msg['notify_user_pic_url'],
    	   	    'create_date'=>$msg['created_date'],
    	   	    'prod_type'=>$msg['content_type'],
    	   	    'prod_name'=>$msg['content_info'],
    	   	    'prod_id'=>$msg['content_id'],
                      );
			  break;
			case Constants::NOTIFY_TYPE_SHARE:					  
			  $temp["share"] = array(
    	   	    'user_id'=>$msg['notify_user_id'],
    	   	    'user_name'=>$msg['notify_user_name'],
    	   	    'user_pic_url'=>$msg['notify_user_pic_url'],
    	   	    'create_date'=>$msg['created_date'],
    	   	    'prod_type'=> $msg['content_type'],
    	   	    'prod_name'=>$msg['content_info'],
    	   	    'prod_id'=>$msg['content_id'],
			    'share_to_where'=>$msg['content_desc'],
                      );
			  break;
			case Constants::NOTIFY_TYPE_WATCH_PROGRAM:
			  $temp["watch"] = array(
    	   	    'user_id'=>$msg['notify_user_id'],
    	   	    'user_name'=>$msg['notify_user_name'],
    	   	    'user_pic_url'=>$msg['notify_user_pic_url'],
    	   	    'create_date'=>$msg['created_date'],
    	   	    'prod_type'=>$msg['content_type'],
    	   	    'prod_name'=>$msg['content_info'],
    	   	    'prod_id'=>$msg['content_id'],
                      );
			  break;
			  
			  
			case Constants::NOTIFY_TYPE_REPLIE_COMMENT:
			  $prod = CacheManager::getCommentProgram($msg['content_id']);									
			  $temp["reply"] = array(
    	   	    'user_id'=>$msg['notify_user_id'],
    	   	    'user_name'=>$msg['notify_user_name'],
    	   	    'user_pic_url'=>$msg['notify_user_pic_url'],
    	   	    'create_date'=>$msg['created_date'],
    	   	    'prod_type'=>$prod['type'],
                   'prod_id'=>$prod['id'],
                   'prod_name'=>$prod['name'],
    	   	    'thread_id'=>$msg['content_id'],					  
			    'content'=>$msg['content_desc'],
			    'thread_comment'=>CacheManager::getCommentContent($msg['content_id']),
                      );
			  break;
		    }
		  }
		  return  $temp;
		}
		public function actionUpdatePicUrl(){
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
   			try {
   				$url = Yii::app()->request->getParam("url");
   				$validator = new CUrlValidator;
   				if(!$validator->validateValue($url)){
   					IjoyPlusServiceUtils::exportServiceError(Constants::URL_INVALID);
   				}
   				$msgs=User::model()->updatePicUrl(Yii::app()->user->id,$url);
   				IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);
   			} catch (Exception $e) {
   				IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
   			}
		}
             
		public function actionUpdateBGPicUrl(){
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
   			try {
   				$url = Yii::app()->request->getParam("url");
   				$validator = new CUrlValidator;
   				if(!$validator->validateValue($url)){
   					IjoyPlusServiceUtils::exportServiceError(Constants::URL_INVALID);
   				}
   				$msgs=User::model()->updateBGPicUrl(Yii::app()->user->id,$url);
   				IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);
   			} catch (Exception $e) {
   				IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
   			}
   		}

}
