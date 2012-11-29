<?php
class CacheManager {
	public static function getUserByID($userid){
		$user = User::model()->findByPk($userid);
		$userVO= new UserVO();
		if(isset($user) && !is_null($user)){
			$userVO->id=$user->id;
			$userVO->username=$user->username;
			$userVO->user_pic_url=$user->user_photo_url;
		}
		return $userVO;
	}
	
   public static function getCommentContent($thread_id){
		$comment = Comment::model()->findByPk($thread_id);
		if(isset($comment) && !is_null($comment)){
			return  $comment->comments;
		}
		return "";
	}
	
    public static function getCommentProgram($thread_id){
		$comment = Comment::model()->findByPk($thread_id);
		if(isset($comment) && !is_null($comment)){
			$prod= Program::model()->findByPk(  $comment->content_id);
			return array(
			  'id'=>$prod->id,
			  'name'=>$prod->name,
			  'poster'=>$prod->poster,			
			  'type'=>$prod->pro_type,
			);
		}
		 return array(
			  'id'=>'',
			  'name'=>'',
			  'poster'=>'',		
			  'type'=>'',
			);
	}
}
?>