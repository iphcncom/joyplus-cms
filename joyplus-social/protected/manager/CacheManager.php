<?php
class CacheManager {
	
	
	const CACHE_PROGRAM_VO_BY_ID='CACHE_PROGRAM_VO_BY_ID';
	const CACHE_PRESTIGE_LIST='CACHE_PRESTIGE_LIST';
	const CACHE_USER_VO_BY_ID='CACHE_USER_VO_BY_ID';
	const CACHE_COMMENT_VO_BY_ID='CACHE_COMMENT_VO_BY_ID';
	
	const CACHE_PROD_BY_PROD_ID='CACHE_PROD_BY_PROD_ID';
	const CACHE_USER_BY_USER_ID='CACHE_USER_BY_USER_ID';
	const CACHE_PROD_TYPE_BY_PROD_ID='CACHE_PROD_TYPE_BY_PROD_ID';
	
	const CACHE_PARAM_EXPIRED_DEFAULT='CACHE_PARAM_EXPIRED_DEFAULT'; 
  	
  	const CACHE_PARAM_EXPIRED_PROGRAM = 'CACHE_PARAM_EXPIRED_PROGRAM';
  	
  	const CACHE_PARAM_EXPIRED_POPULAR_PROGRAM = 'CACHE_PARAM_EXPIRED_POPULAR_PROGRAM';
  	
  	const CACHE_PARAM_EXPIRED_USER = 'CACHE_PARAM_EXPIRED_USER';
  	const CACHE_PARAM_EXPIRED_PRESTIGE = 'CACHE_PARAM_EXPIRED_PRESTIGE';
  	const CACHE_PARAM_EXPIRED_COMMENT = 'CACHE_PARAM_EXPIRED_COMMENT';
  	const CACHE_ENABLED = 'CACHE_ENABLED';
  	
    public static function getUserCache($user_id){
		$key =CacheManager::CACHE_USER_BY_USER_ID.'_'.$user_id;
	    $user = CacheManager::getValueFromCache($key);
	    if($user){
	    	return $user;
	    }
	    $user =User::model()->findByPk($user_id);
	    if(isset($user) && !is_null($user)){
	    	$prodExpired = CacheManager::getExpireByCache(CacheManager::CACHE_PARAM_EXPIRED_USER);
	  	    CacheManager::setValueToCache($key, $user,$prodExpired);
	    }
	  return $user;
	}
	
    public static function getCommentCache($id){
		$key =CacheManager::CACHE_COMMENT_VO_BY_ID.'_'.$id;
	    $comment = CacheManager::getValueFromCache($key);
	    if($comment){
	    	return $comment;
	    }
	    $comment =Comment::model()->findByPk($id);
	    if(isset($comment) && !is_null($comment)){
	    	$prodExpired = CacheManager::getExpireByCache(CacheManager::CACHE_PARAM_EXPIRED_COMMENT);
	  	    CacheManager::setValueToCache($key, $comment,$prodExpired);
	    }
	  return $comment;
	}
	
   public static function getPrestigeCache($limit,$offset){
		$key =CacheManager::CACHE_PRESTIGE_LIST.'_LIMIT_'.$limit.'_OFFSET_'.$offset;
	    $prestige = CacheManager::getValueFromCache($key);
	    if($prestige){
	    	return $prestige;
	    }
	    $prestiges=User::model()->prestiges($limit,$offset);
	    if(isset($prestiges) && !is_null($prestiges)){
//	    	$prodExpired = CacheManager::getExpireByCache(CacheManager::CACHE_PARAM_EXPIRED_COMMENT);
	  	    CacheManager::setValueToCache($key, $prestiges,0);
	    }
	  return $prestiges;
	}
	
//	public 
	
    public static function getUserByID($userid){
		$user = CacheManager::getUserCache($userid);
		$userVO= new UserVO();
		if(isset($user) && !is_null($user)){
			$userVO->id=$user->id;
			$userVO->nickname=$user->nickname;
			$userVO->user_pic_url=$user->user_photo_url;
		}
		return $userVO;
	}
	
   public static function getCommentContent($thread_id){
		$comment = CacheManager::getCommentCache($thread_id);
		if(isset($comment) && !is_null($comment)){
			return  $comment->comments;
		}
		return "";
	}
	
    public static function getCommentProgram($thread_id){
		$comment = CacheManager::getCommentCache($thread_id);
		if(isset($comment) && !is_null($comment)){
			$prod= CacheManager::getProgramCache(  $comment->content_id);
			if(isset($prod) && !is_null($prod)){
				return array(
				  'id'=>$prod->d_id,
				  'name'=>$prod->d_name,
				  'poster'=>$prod->d_pic,			
				  'type'=>$prod->d_type,
				);
			}
		}
		 return array(
			  'id'=>'',
			  'name'=>'',
			  'poster'=>'',		
			  'type'=>'',
			);
	}
	
  	public static function getValueFromCache($key){
  		$cacheEnabled = Yii::app()->params[CacheManager::CACHE_ENABLED];
  		if(isset($cacheEnabled) && !is_null($cacheEnabled) && $cacheEnabled === '1'){
//  			var_dump( Yii::app()->cache->get($key));
  			return  Yii::app()->cache->get($key);
  		}else {
  			return false;
  		}
  	}
  	
   public static function setValueToCache($key,$value,$expired){
  		$cacheEnabled = Yii::app()->params[CacheManager::CACHE_ENABLED];
  		if(isset($cacheEnabled) && !is_null($cacheEnabled) && $cacheEnabled === '1'){
  			 Yii::app()->cache->set($key, $value,$expired);
  		}
  	}
   
   public static function getProgramVOByID($prod_id){		
	    $key =CacheManager::CACHE_PROGRAM_VO_BY_ID.'_'.$prod_id;
	    $prodVO =CacheManager::getValueFromCache($key);
	    if($prodVO){
	    	return $prodVO;
	    }
	    $prod = CacheManager::getProgramCache($prod_id);
		$prodVO= new ProgramVO();
		if(isset($prod) && !is_null($prod)){
			$prodVO->id=$prod->d_id;
			$prodVO->pro_type=$prod->d_type;
			$prodVO->name=$prod->d_name;
			$prodVO->poster=$prod->d_pic;
			$prodVO->score=$prod->d_score;
			$prodVO->publish_owner_id=$prod->publish_owner_id;
			$userExpired = CacheManager::getExpireByCache(CacheManager::CACHE_PARAM_EXPIRED_PROGRAM);
	  	    CacheManager::setValueToCache($key, $prodVO,$userExpired);
	  	    return $prodVO;
		}else {
			return null;
		}
	}
	
	public static function getExpireByCache($paramKey){
		if(isset(Yii::app()->params[$paramKey]) && !is_null(Yii::app()->params[$paramKey])){
			return Yii::app()->params[$paramKey];
		}else if(isset(Yii::app()->params[CacheManager::CACHE_PARAM_EXPIRED_DEFAULT]) && !is_null(Yii::app()->params[CacheManager::CACHE_PARAM_EXPIRED_DEFAULT])){
			return Yii::app()->params[CacheManager::CACHE_PARAM_EXPIRED_DEFAULT];
		}
		return 0;
	}
	
	
    public static function getProgramCache($prod_id){
		$key =CacheManager::CACHE_PROD_BY_PROD_ID.'_'.$prod_id;
	    $prod = CacheManager::getValueFromCache($key);
	    if($prod){
	    	return $prod;
	    }
	    $prod =Program::model()->findByPk($prod_id);
	    if(isset($prod) && !is_null($prod)){
	    	$prodExpired = CacheManager::getExpireByCache(CacheManager::CACHE_PARAM_EXPIRED_PROGRAM);
	  	    CacheManager::setValueToCache($key, $prod,$prodExpired);
	    }
	  return $prod;
	}
	

	
    public static function getProgramTypeCache($prod_type){
		$key =CacheManager::CACHE_PROD_TYPE_BY_PROD_ID.'_'.$prod_type;
	    $prodType = CacheManager::getValueFromCache($key);
	    if($prodType){
	    	return $prodType;
	    }
	    $prodType =MacVodType::model()->findByPk($prod_type);
	    if(isset($prodType) && !is_null($prodType)){
	    	CacheManager::setValueToCache($key, $prodType,0);
	    }
	  return $prodType;
	}
	
	public static function  getTopParentType($prod_type){
		$prodType = CacheManager::getProgramTypeCache($prod_type);
		if(isset($prodType) && !is_null($prodType) && $prodType->t_pid !=="0"){
			return $prodType->t_pid;
		}
		return $prod_type;
	}
	
	public static function synProgramCache($prod){
		$key =CacheManager::CACHE_PROD_BY_PROD_ID.'_'.$prod->d_id;
	    if(isset($prod) && !is_null($prod)){
	    	$prodExpired = CacheManager::getExpireByCache(CacheManager::CACHE_PARAM_EXPIRED_PROGRAM);
	  	    CacheManager::setValueToCache($key, $prod,$prodExpired);
	    }
	}
	
  
	
   public static function synUserCache($user){
		$key =CacheManager::CACHE_PROD_BY_PROD_ID.'_'.$prod->d_id;
	    if(isset($user) && !is_null($user)){
	    	$prodExpired = CacheManager::getExpireByCache(CacheManager::CACHE_PARAM_EXPIRED_USER);
	  	    CacheManager::setValueToCache($key, $user,$prodExpired);
	    }
	}
	
}
?>