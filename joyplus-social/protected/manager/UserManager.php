<?php
 class UserManager {
 	
 	public static function followPrestiges($userid){
 		try{
	 		$prestiges = CacheManager::getPrestigeCache(1000, 0);
	 		if(isset($prestiges)  && is_array($prestiges)){
	 			foreach ($prestiges as $prestige){
	 				 $friend = new Friend;
	                 $friend->author_id=$userid;
	                 $friend->friend_id=$prestige['id'];
	                 $friend->friend_photo_url=$prestige['user_pic_url'];
	                 $friend->friend_username=$prestige['nickname'];               
				     $friend->create_date=new CDbExpression('NOW()');
				     $friend->status=Constants::OBJECT_APPROVAL;
				     $friend->save();
				     User::model()->updateFanCount($prestige['id'], 1);
	 			}
	 			User::model()->updateFollowUserCount($userid, count($prestiges));
 		     }
 		}catch (Exception $e){
 			
 		}
 	}
 }
?>