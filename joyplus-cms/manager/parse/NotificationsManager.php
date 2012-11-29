<?php

require_once('Utils.php');
require_once('ParseClient.php');

class NotificationsManager {
 const CHANNEL_ISO='channel_joyplus_ios'; 
 const CHANNEL_ANDROID='channel_joyplus_android';
 const DEVICE_ISO='ios';
 const DEVICE_ANDROID='android';
 
  static function push($notifyMsg){		   
		   $args = array (
//				'channels' =>
				'data' =>array(
		          'alert'=>$notifyMsg->alert,
		         ),
		   );
		   if(isset($notifyMsg->type) && !is_null($notifyMsg->type) && strlen($notifyMsg->type)>0){
		   	  $args['type']=$notifyMsg->type;
		   	  if($args['type'] === NotificationsManager::DEVICE_ISO){
		   	  	$args['channels']= array(NotificationsManager::CHANNEL_ISO);
		   	  }
		      if($args['type'] === NotificationsManager::DEVICE_ANDROID){
		   	  	$args['channels'] =array(NotificationsManager::CHANNEL_ANDROID);
		   	  }
		   }else {
		   	  $args['channels']= array(NotificationsManager::CHANNEL_ISO,NotificationsManager::CHANNEL_ANDROID);
		   }
//		   $args['channels']= array(NotificationsManager::CHANNEL_ISO,NotificationsManager::CHANNEL_ANDROID);
//		   var_dump($args);
		   
 
		   if(isset($notifyMsg->push_time) && !is_null($notifyMsg->push_time)){
		   	  $args['push_time']=$notifyMsg->push_time;
		   }
		   
 
		   if(isset($notifyMsg->expiration_time) && !is_null($notifyMsg->expiration_time)){
		   	  $args['expiration_time']=$notifyMsg->expiration_time;
		   }
		   
 
		   if(isset($notifyMsg->expiration_interval) && !is_null($notifyMsg->expiration_interval)){
		   	  $args['expiration_interval']=$notifyMsg->expiration_interval;
		   }
		   
 
		   if(isset($notifyMsg->badge) && !is_null($notifyMsg->badge)){
		   	  $args['data']['badge']=$notifyMsg->badge;
		   }
		   
 
		   if(isset($notifyMsg->sound) && !is_null($notifyMsg->sound)){
		   	  $args['data']['sound']=$notifyMsg->sound;
		   }
		   
 
		   if(isset($notifyMsg->content_available) && !is_null($notifyMsg->content_available)){
		   	  $args['data']['content-available']=$notifyMsg->badge;
		   }
		   
 
		   if(isset($notifyMsg->action) && !is_null($notifyMsg->action)){
		   	  $args['data']['action']=$notifyMsg->action;
		   }
		   
 
		   if(isset($notifyMsg->title) && !is_null($notifyMsg->title)){
		   	  $args['data']['title']=$notifyMsg->title;
		   }
		   
           if(isset($notifyMsg->prod_id) && !is_null($notifyMsg->prod_id)){
		   	  $args['data']['prod_id']=$notifyMsg->prod_id;
		   }
		   		   
      		
		$result = ParseClient::getInstance ()->push ( $args );
//		$list = obj2arr ( $result->results );
//echo ($result['code']);
//var_dump($result);
	    return $result;
	}
}


class Notification{
	public $prod_id;
	public $where;//
	public $expiration_interval;//he "expiration_interval" parameter to set a number of seconds after which the notification will expire. This parameter is relative to the "push_time" if it was specified, otherwise it is relative to now. 
	public $expiration_time;//": "2012-10-19T12:00:00Z"
	public $push_time;//You can schedule a push in advance by specifying a "push_time" for your notification.  "2012-10-19T12:00:00Z",
	public $channels; //redsox,yankees
	public $channel; //redsox,yankees
	public $type; //ios /android
	public $alert; // Red box win 7-1;
	public $badge; //is an iOS-specific value Increment
	public $sound; //is an iOS-specific 
	public $content_available;// is an iOS-specific number which should be set to 1 to signal a Newsstand app to begin a background download.
	public $action; 
//	is an Android-specific string indicating that an Intent should be fired with the given action type. 
//  If you specify an "action" and do not specify an "alert" or "title", then no system tray notification will be shown to Android users.
	public $title;//is an Android-specific string that will be used to set a title on the Android system tray notification. 
}

?>