<?php

class TopController extends Controller
{
  function actionTopItems(){
       header('Content-type: application/json');
	    if(!IjoyPlusServiceUtils::validateAPPKey()){
  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
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
		
        $top_id= Yii::app()->request->getParam("top_id");
		if( (!isset($top_id)) || is_null($top_id)  ){
			IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
			return;
		}
		try{
		  $lists = SearchManager::listItems($top_id,$page_size,$page_size*($page_num-1));
		  if(isset($lists) && is_array($lists)){				
		    IjoyPlusServiceUtils::exportEntity(array('items'=>$lists));
		    }else {
			  IjoyPlusServiceUtils::exportEntity(array('items'=>array()));
			}
		}catch (Exception $e){
			Yii::log( CJSON::encode($e), "error");
		  IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);	
		}
	}
	
   function actionShowTopItems(){
       header('Content-type: application/json');
	    if(!IjoyPlusServiceUtils::validateAPPKey()){
  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
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
		
        $top_id= Yii::app()->request->getParam("top_id");
		if( (!isset($top_id)) || is_null($top_id)  ){
			IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
			return;
		}
		try{
		  $lists = SearchManager::listShowItems($top_id,$page_size,$page_size*($page_num-1));
		  if(isset($lists) && is_array($lists)){				
		    IjoyPlusServiceUtils::exportEntity(array('items'=>$lists));
		    }else {
			  IjoyPlusServiceUtils::exportEntity(array('items'=>array()));
			}
		}catch (Exception $e){
			Yii::log( CJSON::encode($e), "error");
		  IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);	
		}
	}
	
	//悦单
   function actionSystemTop(){
       header('Content-type: application/json');
	    if(!IjoyPlusServiceUtils::validateAPPKey()){
  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
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
		try{
		  $lists = SearchManager::tops($page_size,$page_size*($page_num-1));
		  if(isset($lists) && is_array($lists)){				
		    IjoyPlusServiceUtils::exportEntity(array('tops'=>$lists));
		    }else {
			  IjoyPlusServiceUtils::exporptEntity(array('tops'=>array()));
			}
		}catch (Exception $e){
			Yii::log( CJSON::encode($e), "error");
		  IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);	
		}
	}
	
	function actionSystemMovieTop(){
       header('Content-type: application/json');
	    if(!IjoyPlusServiceUtils::validateAPPKey()){
  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
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
		try{
		  $lists = SearchManager::movie_tops($page_size,$page_size*($page_num-1));
		  if(isset($lists) && is_array($lists)){				
		      IjoyPlusServiceUtils::exportEntity(array('tops'=>$lists));
		    }else {
			  IjoyPlusServiceUtils::exporptEntity(array('tops'=>array()));
			}
		}catch (Exception $e){
			Yii::log( CJSON::encode($e), "error");
		  IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);	
		}
	}
	
   function actionSystemTVTop(){
       header('Content-type: application/json');
	    if(!IjoyPlusServiceUtils::validateAPPKey()){
  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
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
		try{
		  $lists = SearchManager::tv_tops($page_size,$page_size*($page_num-1));
		  if(isset($lists) && is_array($lists)){				
		    IjoyPlusServiceUtils::exportEntity(array('tops'=>$lists));
		    }else {
			  IjoyPlusServiceUtils::exporptEntity(array('tops'=>array()));
			}
		}catch (Exception $e){
			Yii::log( CJSON::encode($e), "error");
		  IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);	
		}
	}
	
   function actionSystemCartTop(){
       header('Content-type: application/json');
	    if(!IjoyPlusServiceUtils::validateAPPKey()){
  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
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
		try{
		  $lists = SearchManager::animation_tops($page_size,$page_size*($page_num-1));
		  if(isset($lists) && is_array($lists)){				
		    IjoyPlusServiceUtils::exportEntity(array('tops'=>$lists));
		    }else {
			  IjoyPlusServiceUtils::exporptEntity(array('tops'=>array()));
			}
		}catch (Exception $e){
			Yii::log( CJSON::encode($e), "error");
		  IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);	
		}
	}	
	
    function actionSystemShowTop(){
       header('Content-type: application/json');
	    if(!IjoyPlusServiceUtils::validateAPPKey()){
  	  	   IjoyPlusServiceUtils::exportServiceError(Constants::APP_KEY_INVALID);		
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
		try{
		  $lists = SearchManager::show_tops($page_size,$page_size*($page_num-1));
		  if(isset($lists) && is_array($lists)){				
		    IjoyPlusServiceUtils::exportEntity(array('tops'=>$lists));
		    }else {
			  IjoyPlusServiceUtils::exporptEntity(array('tops'=>array()));
			}
		}catch (Exception $e){
			Yii::log( CJSON::encode($e), "error");
		  IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);	
		}
	}
	
    function actionNew(){
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
			
			$topicName= Yii::app()->request->getParam("name");
			if( (!isset($topicName)) || is_null($topicName)  ){
				IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
				return;
			}					
			$owner_id=Yii::app()->user->id;
			$topic = Topic::model()->getTopic($owner_id, $topicName);
			if($topic == null){
				$transaction = Yii::app()->db->beginTransaction();
				try {
                    $top = new Topic();
                    $top->t_flag=1;
                    $top->t_name=$topicName;
                    $top->create_date=new CDbExpression('NOW()');
                    $top->t_userid=$owner_id;
                    $top->t_pic=Yii::app()->request->getParam("pic");
                    $top->t_des=Yii::app()->request->getParam("content");
                    $top->t_type=Yii::app()->request->getParam("type");
                    $top->save();
                    User::model()->updateTopBDCount($owner_id, 1);
					$transaction->commit();
					IjoyPlusServiceUtils::exportEntity(array('topic_id'=>$top->t_id));
				} catch (Exception $e) {
			        Yii::log( CJSON::encode($e), "error");
					$transaction->rollback();
					IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
				}
			}else {
			   IjoyPlusServiceUtils::exportServiceError(Constants::OBJECT_EXIST);
			}			
		}
		
      function actionAddItem(){
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
			$topic_id= Yii::app()->request->getParam("topic_id");
			if( (!isset($topic_id)) || is_null($topic_id)  ){
				IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
				return;
			}									
			$owner_id=Yii::app()->user->id;			
            $topic = Topic::model()->findByPk($topic_id);
			if($topic !== null){
				if(isset($topic->t_userid) && !is_null($topic->t_userid) && $topic->t_userid ===$owner_id ){
				  $prods= explode(",", $prod_id);
				  if(is_array($prods)) {
				     $transaction = Yii::app()->db->beginTransaction();
				     try{
					     foreach ($prods as $prodid){
						     $topicItem = TopicItems::model()->getItem($topic_id, $prodid);
						     if($topicItem == null){
			                     $item = new TopicItems();
			                     $item->flag=Constants::OBJECT_APPROVAL;
			                     $item->author_id=$owner_id;
			                     $item->topic_id=$topic_id;
			                     $item->vod_id=$prodid;
			                     $item->create_date=new CDbExpression('NOW()');
			                     $item->save();
						     }
					     }
				     	$transaction->commit();
				     	IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);
				     }catch (Exception $e){
				     	$transaction->rollback();
				        Yii::log( CJSON::encode($e), "error");
				  	    IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
				     }
				     
				  }				  
				}else {
				   IjoyPlusServiceUtils::exportServiceError(Constants::NO_RIGHT);
				}
			}else {
			   IjoyPlusServiceUtils::exportServiceError(Constants::OBJECT_NOT_FOUND);
			}		
		}
		
      function actionRemoveItem(){
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
			
			$item_id= Yii::app()->request->getParam("item_id");
			//echo($item_id);
			if( ( !isset($item_id)) || is_null($item_id) || strlen(trim($item_id)) == 0 ){
				IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
				return;
			}					
			$owner_id=Yii::app()->user->id;
		  	try{
		  	  Yii::app()->db->createCommand('delete from mac_vod_topic_items where author_id='.$owner_id.' and id in ('.$item_id.')')->execute();
		  	  IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);
		    }catch(Exception $e){
		      Yii::log( CJSON::encode($e), "error");
		  	  IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
		    }
		}
		
      function actionDel(){
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
			
			$topic_id= Yii::app()->request->getParam("topic_id");
			if( (!isset($topic_id)) || is_null($topic_id)  ){
				IjoyPlusServiceUtils::exportServiceError(Constants::PARAM_IS_INVALID);
				return;
			}					
			$owner_id=Yii::app()->user->id;
			$topic = Topic::model()->findByPk($topic_id);
			if($topic !== null){
				if(isset($topic->t_userid) && !is_null($topic->t_userid) && $topic->t_userid ===$owner_id ){				   
				  if(Topic::model()->deleteTopic($topic_id)){				  	
                    User::model()->updateTopBDCount($owner_id, -1);
				  	IjoyPlusServiceUtils::exportServiceError(Constants::SUCC);
				  }else {
				   IjoyPlusServiceUtils::exportServiceError(Constants::SYSTEM_ERROR);
				  }
				}else {
				   IjoyPlusServiceUtils::exportServiceError(Constants::NO_RIGHT);
				}
			}else {
			   IjoyPlusServiceUtils::exportServiceError(Constants::OBJECT_NOT_FOUND);
			}
			
		}
	
}
