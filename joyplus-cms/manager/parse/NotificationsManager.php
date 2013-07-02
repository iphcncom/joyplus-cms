<?php

require_once('Utils.php');
require_once('ParseClient.php');
require_once ( dirname( __FILE__)."/baidu/Channel.class.php" ) ;


   
class NotificationsManager {
 const CHANNEL_ISO='channel_joyplus_ios'; 
 const CHANNEL_ANDROID='channel_joyplus_android';
 const DEVICE_ISO='ios';
 const DEVICE_ANDROID='android';
  static function push($notifyMsg){		   
		   $args = array (
				'data' =>array(
		          'alert'=>$notifyMsg->alert,
		          'badge'=>'Increment',
		         ),
		   );
		   $args['channels']= array('');
		   if(isset($notifyMsg->type) && !is_null($notifyMsg->type) && strlen($notifyMsg->type)>0){
		   	  $args['type']=$notifyMsg->type;		   	 
		   	  if($args['type'] === NotificationsManager::DEVICE_ISO){
		   	  	//$args['channels']= array(NotificationsManager::CHANNEL_ISO);
		   	  }
		      if($args['type'] === NotificationsManager::DEVICE_ANDROID){
		   	  //	$args['channels'] =array(NotificationsManager::CHANNEL_ANDROID);
		   	  }
		   }else {
		   	 // $args['channels']= array(NotificationsManager::CHANNEL_ISO,NotificationsManager::CHANNEL_ANDROID);
		   }
		   
		   if(isset($notifyMsg->channels) && is_array($notifyMsg->channels) && count($notifyMsg->channels)>0){
		   	 $args['channels']=$notifyMsg->channels;
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
          if(isset($notifyMsg->prod_type) && !is_null($notifyMsg->prod_type)){
		   	  $args['data']['prod_type']=$notifyMsg->prod_type;
		   }
          if(isset($notifyMsg->push_type) && !is_null($notifyMsg->push_type)){
		   	  $args['data']['push_type']=$notifyMsg->push_type;
		   }
		   		   
       // var_dump(json_encode( var_dump($args)));
       
		$result = ParseClient::getInstance ()->push ( $args,$notifyMsg->appid,$notifyMsg->restkey );
//		$list = obj2arr ( $result->results );
//echo ($result['code']);
//var_dump($result);

	    return $result;
	}
	 
	
	
	static function initBaiduCertIOS($notifyMsg){
		global $channels;
		$channel= ChannlesMap::getChannel($notifyMsg->appid.'_'.$notifyMsg->restkey); 
		if($channel !==false &&  $channel !=null){ 
			return $channel;
		}
       
		$channel = new Channel ( $notifyMsg->appid,$notifyMsg->restkey ) ; // 请正确设置apiKey和secretKey，在开发者中心应用基本信息中查看
		$channel->setHost('https://channel.iospush.api.duapp.com');
        $fd = fopen(dirname( __FILE__)."/baidu/cert/".$notifyMsg->iosCertPath, 'r');

        $dev_cert = fread($fd, filesize(dirname( __FILE__)."/baidu/cert/".$notifyMsg->iosCertPath)); // 开发版APNs pem证书
        
        // var_dump($fd);
         $fd_release = fopen(dirname( __FILE__)."/baidu/cert/".$notifyMsg->iosCertPathRel, 'r');

        $release_cert = fread($fd_release, filesize(dirname( __FILE__)."/baidu/cert/".$notifyMsg->iosCertPathRel)); // 开发版APNs pem证书
       // var_dump(dirname( __FILE__)."/baidu/cert/".$notifyMsg->iosCertPathRel);
       //  var_dump(dirname( __FILE__)."/baidu/cert/".$notifyMsg->iosCertPath);
        $ret = $channel->initAppIoscert('yongqing li (95XW9X94ZC)', 'ios certification', $release_cert, $dev_cert); // cert_name和cert_des您自定义字符串即可
        if (false === $ret) {
         	var_dump('initBaiduCertIOS fail.');
         	var_dump( $channel->errmsg ( ));
         }else {
         	var_dump('initBaiduCertIOS succ.');
            ChannlesMap::setChannel($notifyMsg->appid.'_'.$notifyMsg->restkey,$channel);
            
         }
        return $channel;
		
	}
	
	static function pushBaidu($notifyMsg){
		if($notifyMsg->type===NotificationsManager::DEVICE_ISO){
			$channel= NotificationsManager::initBaiduCertIOS($notifyMsg);
		}else {		
		  $channel = new Channel ( $notifyMsg->appid,$notifyMsg->restkey ) ;
		}
	     $message_key = md5(date('Y-m-d-H-M-S',time()));
	     $optional[Channel::MESSAGE_TYPE] = 1;
//	     if(isset($notifyMsg->channels) && is_array($notifyMsg->channels) && count($notifyMsg->channels)>0){		   	
//		   	 $push_type = 2;
//		 }else {
//		 	$push_type = 3; //推送广播消息
//		 }
		 $push_type = 3;
		 $message=array();	   
		   if($notifyMsg->type===NotificationsManager::DEVICE_ISO){
		   	   $optional[Channel::DEVICE_TYPE] = 4;
		   	   $apps =array();
		   	   
		        if(isset($notifyMsg->title) && !is_null($notifyMsg->title)){
		   	      $message['title']=$notifyMsg->title;
		        }
		   
		   	   $optional[Channel::DEVICE_TYPE] = 4;
	           if(isset($notifyMsg->prod_id) && !is_null($notifyMsg->prod_id)){
			   	  $message['prod_id']=$notifyMsg->prod_id;
			   }
	          if(isset($notifyMsg->prod_type) && !is_null($notifyMsg->prod_type)){
			   	  $message['prod_type']=$notifyMsg->prod_type;
			   }
	          if(isset($notifyMsg->push_type) && !is_null($notifyMsg->push_type)){
			   	  $message['push_type']=$notifyMsg->push_type;
			   }
		       if(isset($notifyMsg->alert) && !is_null($notifyMsg->alert)){
			   	  $apps['alert']=$notifyMsg->alert;
			   }
			    $apps['Sound']='';
			    $apps['Badge']=0;
			    $message['aps']=$apps;
		       
		   }else if ($notifyMsg->type===NotificationsManager::DEVICE_ANDROID){
		   	   $custom_content =array();
		   	    if(isset($notifyMsg->title) && !is_null($notifyMsg->title)){
		   	      $message['title']=$notifyMsg->title;
		        }
		   
		   	   $optional[Channel::DEVICE_TYPE] = 3;
	           if(isset($notifyMsg->prod_id) && !is_null($notifyMsg->prod_id)){
			   	  $custom_content['prod_id']=$notifyMsg->prod_id;
			   }
	          if(isset($notifyMsg->prod_type) && !is_null($notifyMsg->prod_type)){
			   	  $custom_content['prod_type']=$notifyMsg->prod_type;
			   }
	          if(isset($notifyMsg->push_type) && !is_null($notifyMsg->push_type)){
			   	  $custom_content['push_type']=$notifyMsg->push_type;
			   }
		       if(isset($notifyMsg->alert) && !is_null($notifyMsg->alert)){
			   	  $message['description']=$notifyMsg->alert;
			   }
			   $message['custom_content']=$custom_content;
		   }
		   
		  
//		   var_dump($message); var_dump($optional);
		 $ret = $channel->pushMessage ( $push_type,$message, $message_key, $optional ) ;
		
	    if ( false === $ret ){ 
	    	return array('code'=>'201','response'=>$channel->errmsg());
	    }else{  
	    	return array('code'=>'200','response'=>$ret);
	    }
	}
	
}

//$msg = new Notification();
//		$msg->alert='This is test';
//		$msg->prod_id='11111';
//		$msg->prod_type='11111';
//		$msg->push_type='1';
//		$msg->channels=array('iso');
//		$msg->appid='38hyK1inq207SeCMogRvbtix';//bxh3Gv5eapTM1KcauwgWCCPQAPI 
//		
//		$msg->restkey='aikV4AURtqbCcqfk6mSc6Ek6twwpLP7H';
////		var_dump($msg);
//		$result= NotificationsManager::pushBaidu($msg);
//		var_dump($result);
		

class Notification{
	public $prod_id;
	public $prod_type;
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
	public $push_type;
	public $appid  ;
	public $restkey;
	public $iosCertPath;
	public $iosCertPathRel;
//	is an Android-specific string indicating that an Intent should be fired with the given action type. 
//  If you specify an "action" and do not specify an "alert" or "title", then no system tray notification will be shown to Android users.
	public $title;//is an Android-specific string that will be used to set a title on the Android system tray notification. 
}

class ChannlesMaps {
	 
	 static $myParse = null;
   	 private $channels = array();
     static function getInstance() {
		if (is_null ( self::$myParse )) {
			self::$myParse = new ChannlesMap ();
		}
		return self::$myParse;
	}
	
   	 public function setChannel($key,$value){
   	 	$this->channels[$key]=$value;
   	 }
   	 
     public function getChannel($key){
        if(array_key_exists($key, $this->channels)){ 
			return $this->channels[$key];
		}
		return false;
   	 }
}

class ChannlesMap{
	function &setInit(){
		static $static = array();
		return $static;
	}
	function setChannel($key,$value){
		$var = &ChannlesMap::setInit();
		$var[$key] = $value;
	}
	function &getChannel($key){
		$var = &ChannlesMap::setInit();
		return $var[$key];
	}
}

?>