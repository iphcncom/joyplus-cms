<?php
class SearchManager {
	
	const POPULAR_MOVIE_SPECIAL_ID=1; //
	const POPULAR_TV_SET_SPECIAL_ID=2;
	const POPULAR_TV_SHOW_SPECIAL_ID=3;
	const POPULAR_TV_VEDIO_SPECIAL_ID=4;
	const TOPS_LIST_ITEM_NUM=5; //悦单	
	const USER_TOPS_LIST_ITEM_NUM=7; //悦单
	const BD_TOPS_LIST_ITEM_NUM=10; //悦榜
	const CACHE_POPULAR_PROD_BY_TYPE_LIMIT_OFFSET="CACHE_POPULAR_PROD_BY_TYPE_LIMIT_OFFSET";
	const CACHE_LISTS_BY_TYPE_LIMIT_OFFSET="CACHE_LISTS_BY_TYPE_LIMIT_OFFSET";
	
	const CACHE_TOPS_LIMIT_OFFSET="CACHE_TOPS_LIMIT_OFFSET";
	
	const CACHE_MOVIE_TOPS_LIMIT_OFFSET="CACHE_MOVIE_TOPS_LIMIT_OFFSET";
	
	const CACHE_TV_TOPS_LIMIT_OFFSET="CACHE_TV_TOPS_LIMIT_OFFSET";
	const CACHE_ANAMATION_TOPS_LIMIT_OFFSET="CACHE_ANAMATION_TOPS_LIMIT_OFFSET";
	const CACHE_SHOW_TOPS_LIMIT_OFFSET="CACHE_SHOW_TOPS_LIMIT_OFFSET";
	
	const CACHE_LUN_BO="CACHE_LUN_BO";
	const CACHE_LIST_ITEMS_BY_TYPE_LIMIT_OFFSET="CACHE_LIST_ITEMS_BY_TYPE_LIMIT_OFFSET";
	const CACHE_SEARCH_PROD_BY_CONTENT_LIMIT_OFFSET="CACHE_SEARCH_PROD_BY_CONTENT_LIMIT_OFFSET";
	
	public static function popularProgram($type,$limit,$offset){
	    $key =SearchManager::CACHE_POPULAR_PROD_BY_TYPE_LIMIT_OFFSET.'_'.$type.'_LIMIT_'.$limit.'_OFFSET_'.$offset;
	    $prods = CacheManager::getValueFromCache($key);
	    if($prods){
	    	return $prods;
	    }
	    $prods= Yii::app()->db->createCommand()
		->select('d_id as prod_id, d_name as prod_name, d_type as prod_type,d_pic as prod_pic_url,d_score as score')
		->from('mac_vod ')
		->where('d_hide=:d_hide and d_topic=:d_topic', array(
			    ':d_hide'=>0,
			    ':d_topic'=>$type,
		))->order('d_level desc ,d_good desc,d_time DESC')->limit($limit)->offset($offset)
		->queryAll();
	    if(isset($prods) && !is_null($prods)){
	    	$prodExpired = CacheManager::getExpireByCache(CacheManager::CACHE_PARAM_EXPIRED_POPULAR_PROGRAM);
	  	    CacheManager::setValueToCache($key, $prods,$prodExpired);
	    }
	    return $prods;
	}
   
   const LUN_BO_SQL='select * from (
SELECT vod.d_name AS prod_name, vod.d_id AS prod_id, a.ipad_pic_url AS ipad_pic, a.iphone_pic_url AS iphone_pic, a.info_desc AS memo, vod.d_type AS prod_type, a.type AS
type ,a.disp_order as disp_order FROM mac_vod_popular a, mac_vod vod
WHERE vod.d_hide =0
AND a.type =0
AND a.vod_id = vod.d_id

union 

SELECT vod.t_name AS prod_name, vod.t_id AS prod_id, a.ipad_pic_url AS ipad_pic, a.iphone_pic_url AS iphone_pic, a.info_desc AS memo, NULL AS prod_type, a.type AS
type,a.disp_order as disp_order FROM mac_vod_popular a, mac_vod_topic vod
WHERE a.type =1 AND a.vod_id = vod.t_id

union 

SELECT "" AS prod_name, "" AS prod_id, a.ipad_pic_url AS ipad_pic, a.iphone_pic_url AS iphone_pic, a.info_desc AS memo, NULL AS prod_type, a.type AS
type,a.disp_order as disp_order FROM mac_vod_popular a
WHERE a.type !=1 and a.type !=0   


) as d 
ORDER BY d.disp_order asc ';
  
   public static function lunbo(){
         $key =SearchManager::CACHE_LUN_BO;
	    $lists = CacheManager::getValueFromCache($key);
	    if($lists){
	    	return $lists;
	    }	    
	    $lists= Yii::app()->db->createCommand(SearchManager::LUN_BO_SQL)->queryAll();
	    
	    if(isset($lists) && !is_null($lists)){
	    	$prodExpired = CacheManager::getExpireByCache(CacheManager::CACHE_PARAM_EXPIRED_POPULAR_PROGRAM);
	  	    CacheManager::setValueToCache($key, $lists,$prodExpired);
	    }
	    return $lists;
   }
   
   //yuedan
   public static function tops($limit,$offset){
   	    $device=IjoyPlusServiceUtils::getDevice();
   	    $where='';
   	    if($device ===false){
           $key =SearchManager::CACHE_TOPS_LIMIT_OFFSET.'_LIMIT_'.$limit.'_OFFSET_'.$offset;
   	    }else {
   	    	$where=' AND (can_search_device like \'%'.$device.'%\' or can_search_device is null or can_search_device =\'\' ) ';
   	    	$key =SearchManager::CACHE_TOPS_LIMIT_OFFSET.'_LIMIT_'.$limit.'_OFFSET_'.$offset.'_DEVICE_'.$device;
   	    }
	    $lists = CacheManager::getValueFromCache($key);
	    if($lists){
	    	return $lists;
	    }
	     $lists= Yii::app()->db->createCommand()
			->select('t_id as id, t_name as name, t_type as prod_type, 	t_pic as pic_url,t_des as content,t_toptype as toptype')
			->from('mac_vod_topic ')
			->where('t_flag=:t_flag and t_id>4 and t_bdtype=1'.$where, array(
				    ':t_flag'=>1,
			))->order('t_toptype desc ,t_sort desc ,create_date desc')->limit($limit)->offset($offset)
			->queryAll();	   
	  $temp = array();
	  if(isset($lists) && is_array($lists)){	  	
	  	 foreach ($lists as $list){	  	 	
	  	 	$items = SearchManager::listItems($list['id'], SearchManager::TOPS_LIST_ITEM_NUM, 0);	  	 	
	  	 	if(isset($items) && is_array($items) && count($items)>0){
	  	 		$list['items']=$items;
	  	 		if(!(isset($list['pic_url']) && is_null($list['pic_url']))){
	  	 			$list['pic_url']=$items[0]['prod_pic_url'];
	  	 		}
	  	 		$list['big_pic_url']=$items[0]['big_prod_pic_url'];
	  	 		
	  	 			
	  	 	}
	  	 	
	  	    $itemNum = SearchManager::listItemNums($list['id']);
	  	 	if(isset($itemNum) && is_array($itemNum) && count($itemNum)>0 && array_key_exists('countNum', $itemNum[0])){
	  	 		$list['num']=$itemNum[0]['countNum'];
	  	 	}
	  	 	
	  	 	$temp[]=$list;
	  	 }
	  	  if(count($temp)>0){
	  	    $prodExpired = CacheManager::getExpireByCache(CacheManager::CACHE_PARAM_EXPIRED_POPULAR_PROGRAM);
	  	    CacheManager::setValueToCache($key, $temp,$prodExpired);
	  	  }
	  }	
	  
	  return $temp;	
	  
   }
   
   //yuedan
   public static function tops_type($limit,$offset,$type){
   	    $device=IjoyPlusServiceUtils::getDevice();
   	    $where='';
   	    if($device ===false){
           $key =SearchManager::CACHE_TOPS_LIMIT_OFFSET.'_LIMIT_'.$limit.'_OFFSET_'.$offset.'_TYPE_'.$type;
   	    }else {
   	    	$where=' AND (can_search_device like \'%'.$device.'%\' or can_search_device is null or can_search_device =\'\' ) ';
   	    	$key =SearchManager::CACHE_TOPS_LIMIT_OFFSET.'_LIMIT_'.$limit.'_OFFSET_'.$offset.'_DEVICE_'.$device.'_TYPE_'.$type;
   	    }
	    $lists = CacheManager::getValueFromCache($key);
	    if($lists){
	    	return $lists;
	    }
	     $lists= Yii::app()->db->createCommand()
			->select('t_id as id, t_name as name, t_type as prod_type, 	t_pic as pic_url,t_des as content,t_toptype as toptype')
			->from('mac_vod_topic ')
			->where('t_flag=:t_flag and t_type='.$type.' and t_id>4 and t_bdtype=1 '.$where, array(
				    ':t_flag'=>1,
			))->order('t_toptype desc ,t_sort desc ,create_date desc')->limit($limit)->offset($offset)
			->queryAll();	   
	  $temp = array();
	  if(isset($lists) && is_array($lists)){	  	
	  	 foreach ($lists as $list){	  	 	
	  	 	$items = SearchManager::listItems($list['id'], SearchManager::TOPS_LIST_ITEM_NUM, 0);
	  	 	if(isset($items) && is_array($items) && count($items)>0){
	  	 		$list['items']=$items;
	  	 		if(!(isset($list['pic_url']) && is_null($list['pic_url']))){
	  	 			$list['pic_url']=$items[0]['prod_pic_url'];
	  	 		}
	  	 		$list['big_pic_url']=$items[0]['big_prod_pic_url'];
	  	 	}
	  	 	
	  	 	
	  	    $itemNum = SearchManager::listItemNums($list['id']);
	  	 	if(isset($itemNum) && is_array($itemNum) && count($itemNum)>0 && array_key_exists('countNum', $itemNum[0])){
	  	 		$list['num']=$itemNum[0]['countNum'];
	  	 	}
	  	 	$temp[]=$list;
	  	 }
	  	  if(count($temp)>0){
	  	    $prodExpired = CacheManager::getExpireByCache(CacheManager::CACHE_PARAM_EXPIRED_POPULAR_PROGRAM);
	  	    CacheManager::setValueToCache($key, $temp,$prodExpired);
	  	  }
	  }	
	  
	  return $temp;	
	  
   }
   
   //yuedan
   public static function movie_tops($limit,$offset){
        $device=IjoyPlusServiceUtils::getDevice();
   	    $where='';
   	    if($device ===false){
           $key =SearchManager::CACHE_MOVIE_TOPS_LIMIT_OFFSET.'_LIMIT_'.$limit.'_OFFSET_'.$offset;
   	    }else {
   	    	$where=' AND (can_search_device like \'%'.$device.'%\' or can_search_device is null or can_search_device =\'\' ) ';
   	    	$key =SearchManager::CACHE_MOVIE_TOPS_LIMIT_OFFSET.'_LIMIT_'.$limit.'_OFFSET_'.$offset.'_DEVICE_'.$device;
   	    }
	    $lists = CacheManager::getValueFromCache($key);
	    if($lists){
	    	return $lists;
	    }
	    $lists= Yii::app()->db->createCommand()
			->select('t_id as id, t_name as name, t_type as prod_type, 	t_pic as pic_url,t_des as content')
			->from('mac_vod_topic ')
			->where('t_flag=:t_flag and t_id>4 and t_bdtype=2 and t_type='.Constants::PROGRAM_TYPE_MOVIE .$where, array(
				    ':t_flag'=>1,
			))->order('t_sort desc ,create_date desc')->limit($limit)->offset($offset)
			->queryAll();
	  $temp = array();
	  if(isset($lists) && is_array($lists)){	  	
	  	 foreach ($lists as $list){	  	 	
	  	 	$items = SearchManager::listItems($list['id'], SearchManager::BD_TOPS_LIST_ITEM_NUM, 0);	  	 	
	  	 	if(isset($items) && is_array($items) && count($items)>0){
	  	 		$list['items']=$items;
	  	 		if(!(isset($list['pic_url']) && is_null($list['pic_url']))){
	  	 			$list['pic_url']=$items[0]['prod_pic_url'];
	  	 		}
	  	 		$list['big_pic_url']=$items[0]['big_prod_pic_url'];
	  	 	}
	  	 	$temp[]=$list;
	  	 }
	  	 if(count($temp)>0){
	  	    $prodExpired = CacheManager::getExpireByCache(CacheManager::CACHE_PARAM_EXPIRED_POPULAR_PROGRAM);
	  	    CacheManager::setValueToCache($key, $temp,$prodExpired);
	  	  }
	  }	
	  return $temp;	
   }
   
   public static function show_tops($limit,$offset){
        $device=IjoyPlusServiceUtils::getDevice();
   	    $where='';
   	    if($device ===false){
           $key =SearchManager::CACHE_SHOW_TOPS_LIMIT_OFFSET.'_LIMIT_'.$limit.'_OFFSET_'.$offset;
   	    }else {
   	    	$where=' AND (can_search_device like \'%'.$device.'%\' or can_search_device is null or can_search_device =\'\' ) ';
   	    	$key =SearchManager::CACHE_SHOW_TOPS_LIMIT_OFFSET.'_LIMIT_'.$limit.'_OFFSET_'.$offset.'_DEVICE_'.$device;
   	    }
	    $lists = CacheManager::getValueFromCache($key);
	    if($lists){
	    	return $lists;
	    }
	    $lists= Yii::app()->db->createCommand()
			->select('t_id as id, t_name as name, t_type as prod_type, 	t_pic as pic_url,t_des as content')
			->from('mac_vod_topic ')
			->where('t_flag=:t_flag and t_id>4 and t_bdtype=2 and t_type='.Constants::PROGRAM_TYPE_SHOW .$where, array(
				    ':t_flag'=>1,
			))->order('t_sort desc,create_date desc ')->limit($limit)->offset($offset)
			->queryAll();
	  $temp = array();
	  if(isset($lists) && is_array($lists)){	  	
	  	 foreach ($lists as $list){	  	 	
	  	 	$items = SearchManager::listShowItems($list['id'], SearchManager::BD_TOPS_LIST_ITEM_NUM, 0);	  	 	
	  	 	if(isset($items) && is_array($items) && count($items)>0){
	  	 		$list['items']=$items;
	  	 		if(!(isset($list['pic_url']) && is_null($list['pic_url']))){
	  	 			$list['pic_url']=$items[0]['prod_pic_url'];
	  	 		}
	  	 		$list['big_pic_url']=$items[0]['big_prod_pic_url'];
	  	 	}
	  	 	$temp[]=$list;
	  	 }
	     if(count($temp)>0){
	  	    $prodExpired = CacheManager::getExpireByCache(CacheManager::CACHE_PARAM_EXPIRED_POPULAR_PROGRAM);
	  	    CacheManager::setValueToCache($key, $temp,$prodExpired);
	  	 }
	  }	
	  return $temp;	
   }
   
   //yuedan
   public static function tv_tops($limit,$offset){
        $device=IjoyPlusServiceUtils::getDevice();
   	    $where='';
   	    if($device ===false){
           $key =SearchManager::CACHE_TV_TOPS_LIMIT_OFFSET.'_LIMIT_'.$limit.'_OFFSET_'.$offset;
   	    }else {
   	    	$where=' AND (can_search_device like \'%'.$device.'%\' or can_search_device is null or can_search_device =\'\' ) ';
   	    	$key =SearchManager::CACHE_TV_TOPS_LIMIT_OFFSET.'_LIMIT_'.$limit.'_OFFSET_'.$offset.'_DEVICE_'.$device;
   	    }
	    $lists = CacheManager::getValueFromCache($key);
	    if($lists){
	    	return $lists;
	    }
	     $lists= Yii::app()->db->createCommand()
			->select('t_id as id, t_name as name, t_type as prod_type, 	t_pic as pic_url,t_des as content')
			->from('mac_vod_topic ')
			->where('t_flag=:t_flag and t_id>4 and t_bdtype=2 and t_type='.Constants::PROGRAM_TYPE_TV .$where, array(
				    ':t_flag'=>1,
			))->order('t_sort desc,create_date desc ')->limit($limit)->offset($offset)
			->queryAll();
	  $temp = array();
	  if(isset($lists) && is_array($lists)){	  	
	  	 foreach ($lists as $list){	  	 	
	  	 	$items = SearchManager::listItems($list['id'], SearchManager::BD_TOPS_LIST_ITEM_NUM, 0);	  	 	
	  	 	if(isset($items) && is_array($items) && count($items)>0){
	  	 		$list['items']=$items;
	  	 		if(!(isset($list['pic_url']) && is_null($list['pic_url']))){
	  	 			$list['pic_url']=$items[0]['prod_pic_url'];
	  	 		}
	  	 		$list['big_pic_url']=$items[0]['big_prod_pic_url'];
	  	 	}
	  	 	$temp[]=$list;
	  	 }
	     if(count($temp)>0){
	  	    $prodExpired = CacheManager::getExpireByCache(CacheManager::CACHE_PARAM_EXPIRED_POPULAR_PROGRAM);
	  	    CacheManager::setValueToCache($key, $temp,$prodExpired);
	  	 }
	  }	
	  return $temp;	
   }
   
   public static function animation_tops($limit,$offset){
        $device=IjoyPlusServiceUtils::getDevice();
   	    $where='';
   	    if($device ===false){
           $key =SearchManager::CACHE_ANAMATION_TOPS_LIMIT_OFFSET.'_LIMIT_'.$limit.'_OFFSET_'.$offset;
   	    }else {
   	       $where=' AND (can_search_device like \'%'.$device.'%\' or can_search_device is null or can_search_device =\'\' ) ';
   	       $key =SearchManager::CACHE_ANAMATION_TOPS_LIMIT_OFFSET.'_LIMIT_'.$limit.'_OFFSET_'.$offset.'_DEVICE_'.$device;
   	    }
	    $lists = CacheManager::getValueFromCache($key);
	    if($lists){
	    	return $lists;
	    }
	    $lists= Yii::app()->db->createCommand()
			->select('t_id as id, t_name as name, t_type as prod_type, 	t_pic as pic_url,t_des as content')
			->from('mac_vod_topic ')
			->where('t_flag=:t_flag and t_id>4 and t_bdtype=2 and t_type='.Constants::PROGRAM_ANIMATION .$where, array(
				    ':t_flag'=>1,
			))->order('t_sort desc,create_date desc ')->limit($limit)->offset($offset)
			->queryAll();
	  $temp = array();
	  if(isset($lists) && is_array($lists)){	  	
	  	 foreach ($lists as $list){	  	 	
	  	 	$items = SearchManager::listItems($list['id'], SearchManager::BD_TOPS_LIST_ITEM_NUM, 0);	  	 	
	  	 	if(isset($items) && is_array($items) && count($items)>0){
	  	 		$list['items']=$items;
	  	 		if(!(isset($list['pic_url']) && is_null($list['pic_url']))){
	  	 			$list['pic_url']=$items[0]['prod_pic_url'];
	  	 		}
	  	 		$list['big_pic_url']=$items[0]['big_prod_pic_url'];
	  	 	}
	  	 	$temp[]=$list;
	  	 }
	     if(count($temp)>0){
	  	    $prodExpired = CacheManager::getExpireByCache(CacheManager::CACHE_PARAM_EXPIRED_POPULAR_PROGRAM);
	  	    CacheManager::setValueToCache($key, $temp,$prodExpired);
	  	 }
	  }	
	  return $temp;	
   }
   
   public static function lists($userid,$limit,$offset,$type){
	    $device=IjoyPlusServiceUtils::getDevice();
   	    $where='';
   	    if($device ===false){
           $key =SearchManager::CACHE_LISTS_BY_TYPE_LIMIT_OFFSET.'_USER_'.$userid.'_LIMIT_'.$limit.'_OFFSET_'.$offset;
   	    }else {
   	    	$where=' AND (can_search_device like \'%'.$device.'%\' or can_search_device is null or can_search_device =\'\' ) ';
   	    	$key =SearchManager::CACHE_LISTS_BY_TYPE_LIMIT_OFFSET.'_USER_'.$userid.'_LIMIT_'.$limit.'_OFFSET_'.$offset.'_DEVICE_'.$device;
   	    }
	    $lists = CacheManager::getValueFromCache($key);
	    if($lists){
	    	return $lists;
	    }
	    if($userid >0){
		    $lists= Yii::app()->db->createCommand()
				->select('t_id as id, t_name as name, t_type as prod_type, 	t_pic as pic_url,t_des as content')
				->from('mac_vod_topic ')
				->where('t_userid=:t_userid and t_id>4'.$where, array(
					    ':t_userid'=>$userid,
				))->order('t_sort desc,create_date desc ')->limit($limit)->offset($offset)
				->queryAll();
	    } else {
	    	$lists= Yii::app()->db->createCommand()
				->select('t_id as id, t_name as name, t_type as prod_type, 	t_pic as pic_url,t_des as content')
				->from('mac_vod_topic ')
				->where('t_flag=:t_flag and t_id>4 and t_bdtype=:type'.$where, array(
					    ':t_flag'=>1,
				        ':type'=>$type,
				))->order('t_sort desc,create_date desc ')->limit($limit)->offset($offset)
				->queryAll();
	    }
	    
	  $temp = array();
	  if(isset($lists) && is_array($lists)){	  	
	  	 foreach ($lists as $list){	  	 	
	  	 	$items = SearchManager::listItems($list['id'], SearchManager::USER_TOPS_LIST_ITEM_NUM, 0);	  	 	
	  	 	if(isset($items) && is_array($items) && count($items)>0){
	  	 		$list['items']=$items;
	  	 		if(!(isset($list['pic_url']) && is_null($list['pic_url']))){
	  	 			$list['pic_url']=$items[0]['prod_pic_url'];
	  	 		}
	  	 		$list['big_pic_url']=$items[0]['big_prod_pic_url'];
	  	 	}
	  	 	$temp[]=$list;
	  	 }
	  	  if(count($temp)>0){
	  	    $prodExpired = CacheManager::getExpireByCache(CacheManager::CACHE_PARAM_EXPIRED_POPULAR_PROGRAM);
	  	    CacheManager::setValueToCache($key, $temp,$prodExpired);
	  	  }
	  }	
	   
	    return $temp;
	}
	
    public static function listsByProdType($userid,$limit,$offset,$type,$prodType){
	    $device=IjoyPlusServiceUtils::getDevice();
   	    $where='';
   	    if($device ===false){
           $key =SearchManager::CACHE_LISTS_BY_TYPE_LIMIT_OFFSET.'_USER_'.$userid.'_LIMIT_'.$limit.'_OFFSET_'.$offset.'_type_'.$prodType;
   	    }else {
   	    	$where=' AND (can_search_device like \'%'.$device.'%\' or can_search_device is null or can_search_device =\'\' ) ';
   	       $key =SearchManager::CACHE_LISTS_BY_TYPE_LIMIT_OFFSET.'_USER_'.$userid.'_LIMIT_'.$limit.'_OFFSET_'.$offset.'_type_'.$prodType.'_DEVICE_'.$device;
   	    }
	    $lists = CacheManager::getValueFromCache($key);
	    if($lists){
	    	return $lists;
	    }
	    if($userid >0){
	    	$lists= Yii::app()->db->createCommand()
				->select('t_id as id, t_name as name, t_type as prod_type, 	t_pic as pic_url,t_des as content')
				->from('mac_vod_topic ')
				->where('t_userid=:t_userid and t_id>4 and t_type in ('.$prodType.')' .$where, array(
					    ':t_userid'=>$userid,
				))->order('t_sort desc,create_date desc ')->limit($limit)->offset($offset)
				->queryAll();
	    } else {
	    	$lists= Yii::app()->db->createCommand()
				->select('t_id as id, t_name as name, t_type as prod_type, 	t_pic as pic_url,t_des as content')
				->from('mac_vod_topic ')
				->where('t_flag=:t_flag and t_id>4 and t_bdtype=:type and t_type in ('.$prodType.')' .$where, array(
					    ':t_flag'=>1,
				        ':type'=>$type,
				))->order('t_sort desc,create_date desc ')->limit($limit)->offset($offset)
				->queryAll();
	    }
	    
	    $temp = array();
	  if(isset($lists) && is_array($lists)){	  	
	  	 foreach ($lists as $list){	  	 	
	  	 	$items = SearchManager::listItems($list['id'], SearchManager::USER_TOPS_LIST_ITEM_NUM, 0);	  	 	
	  	 	if(isset($items) && is_array($items) && count($items)>0){
	  	 		$list['items']=$items;
	  	 		if(!(isset($list['pic_url']) && is_null($list['pic_url']))){
	  	 			$list['pic_url']=$items[0]['prod_pic_url'];
	  	 		}
	  	 		$list['big_pic_url']=$items[0]['big_prod_pic_url'];
	  	 	}
	  	 	$temp[]=$list;
	  	 }
	  	  if(count($temp)>0){
	  	    $prodExpired = CacheManager::getExpireByCache(CacheManager::CACHE_PARAM_EXPIRED_POPULAR_PROGRAM);
	  	    CacheManager::setValueToCache($key, $temp,$prodExpired);
	  	  }
	  }	
	   
	    return $temp;
	}
	
   
	
	public static function listItems($top_id,$limit,$offset){
	    $device=IjoyPlusServiceUtils::getDevice();
   	    $where='';
   	    if($device ===false){
           $key =SearchManager::CACHE_LIST_ITEMS_BY_TYPE_LIMIT_OFFSET.'_TOP_'.$top_id.'_LIMIT_'.$limit.'_OFFSET_'.$offset;
   	    }else {
   	    	$where=' AND (can_search_device like \'%'.$device.'%\' or can_search_device is null or can_search_device =\'\' ) ';
   	       $key =SearchManager::CACHE_LIST_ITEMS_BY_TYPE_LIMIT_OFFSET.'_TOP_'.$top_id.'_LIMIT_'.$limit.'_OFFSET_'.$offset.'_DEVICE_'.$device;
   	    }
	    $items = CacheManager::getValueFromCache($key);
	    if($items){
	    	return $items;
	    }
	    $items= Yii::app()->db->createCommand()
			->select('items.id as id, vod.d_id as prod_id,vod.d_name as prod_name, vod.d_level as definition, vod.d_type as prod_type, vod.d_pic as prod_pic_url,substring_index( vod.d_pic_ipad, \'{Array}\', 1 )  as big_prod_pic_url,vod.d_starring as stars,vod.d_directed as directors ,vod.favority_user_count as favority_num ,vod.good_number as support_num,vod.d_year as publish_date,vod.d_score as score,vod.d_area as area, vod.d_remarks as max_episode, vod.d_state as cur_episode , vod.duraning as duration ')
			->from('mac_vod_topic_items as items')
			->join("mac_vod as vod","items.vod_id=vod.d_id")
			->where('items.flag=:t_flag and items.topic_id=:topic_id and  vod.d_hide=0 '.$where, array(
				    ':t_flag'=>1,
				    ':topic_id'=>$top_id,
			))->order('items.disp_order desc, vod.d_level desc ,vod.d_good desc,vod.d_time DESC ')->limit($limit)->offset($offset)
			->queryAll();
	    if(isset($items) && !is_null($items)){
	    	$prodExpired = CacheManager::getExpireByCache(CacheManager::CACHE_PARAM_EXPIRED_POPULAR_PROGRAM);
	  	    CacheManager::setValueToCache($key, $items,$prodExpired);
	    }
	    return $items;
	}
	
	public static function listItemNums($top_id){
	    $device=IjoyPlusServiceUtils::getDevice();
   	    $where='';
   	    if($device ===false){
           $key =SearchManager::CACHE_LIST_ITEMS_BY_TYPE_LIMIT_OFFSET.'_TOP_NUM_'.$top_id;
   	    }else {
   	    	$where=' AND (can_search_device like \'%'.$device.'%\' or can_search_device is null or can_search_device =\'\' ) ';
   	       $key =SearchManager::CACHE_LIST_ITEMS_BY_TYPE_LIMIT_OFFSET.'_TOP_NUM_'.$top_id.'_DEVICE_'.$device;
   	    }
	    $items = CacheManager::getValueFromCache($key);
	    if($items){
	    	return $items;
	    }
	    $items= Yii::app()->db->createCommand()
			->select('count(items.id) as countNum ')
			->from('mac_vod_topic_items as items')
			->join("mac_vod as vod","items.vod_id=vod.d_id")
			->where('items.flag=:t_flag and items.topic_id=:topic_id and  vod.d_hide=0 '.$where, array(
				    ':t_flag'=>1,
				    ':topic_id'=>$top_id,
			))->queryAll();
	    if(isset($items) && !is_null($items)){
	    	$prodExpired = CacheManager::getExpireByCache(CacheManager::CACHE_PARAM_EXPIRED_POPULAR_PROGRAM);
	  	    CacheManager::setValueToCache($key, $items,$prodExpired);
	    }
	    return $items;
	}
	
	
   public static function listShowItems($top_id,$limit,$offset){
	    $device=IjoyPlusServiceUtils::getDevice();
   	    $where='';
   	    if($device ===false){
           $key =SearchManager::CACHE_LIST_ITEMS_BY_TYPE_LIMIT_OFFSET.'_TOP_SHOW_'.$top_id.'_LIMIT_'.$limit.'_OFFSET_'.$offset;
   	    }else {
   	    	$where=' AND (can_search_device like \'%'.$device.'%\' or can_search_device is null or can_search_device =\'\' ) ';
   	    	$key =SearchManager::CACHE_LIST_ITEMS_BY_TYPE_LIMIT_OFFSET.'_TOP_SHOW_'.$top_id.'_LIMIT_'.$limit.'_OFFSET_'.$offset.'_DEVICE_'.$device;
   	    }
	    $items = CacheManager::getValueFromCache($key);
	    if($items){
	    	return $items;
	    }
	    $items= Yii::app()->db->createCommand()
			->select('items.id as id, vod.d_id as prod_id,vod.d_name as prod_name, vod.d_level as definition, vod.d_type as prod_type,d_pic_ipad  as prod_pic_url ,substring_index( vod.d_pic_ipad, \'{Array}\', 1 )  as big_prod_pic_url, vod.webUrls as cur_item_url ,vod.d_starring as stars,vod.d_directed as directors ,vod.favority_user_count as favority_num ,vod.good_number as support_num ,vod.d_year as publish_date,vod.d_score as score,vod.d_area as area, vod.d_remarks as max_episode, vod.d_state as cur_episode , vod.duraning as duration ')
			->from('mac_vod_topic_items as items')
			->join("mac_vod as vod","items.vod_id=vod.d_id")
			->where('items.flag=:t_flag and items.topic_id=:topic_id and vod.d_hide=0 '.$where, array(
				    ':t_flag'=>1,
				    ':topic_id'=>$top_id,
			))->order('items.disp_order desc, vod.d_level desc ,vod.d_good desc,vod.d_time DESC ')->limit($limit)->offset($offset)
			->queryAll();
		
	    $tempList= array();
	    if(isset($items) && !is_null($items) && is_array($items)){
	    	foreach ($items as $item){
	    	  $weburls = $item['cur_item_url'];
	    	  $prod_pic_url = $item['prod_pic_url'];
	    	  if(isset($prod_pic_url) && !is_null($prod_pic_url)){
	    	  	$prodPicArray = explode("{Array}", $prod_pic_url);
	    	  	if(count($prodPicArray)>1){
	    	  		$item['prod_pic_url']=$prodPicArray[1];
	    	  	}
	    	  }
	    	  $cur_name='';
	    	  $cur_url='';
	    	  if(isset($weburls) && !is_null($weburls)){
	    	    $weburlsA = explode("{Array}", $weburls);
	    	    if(isset($weburlsA) && is_array($weburlsA)  && count($weburlsA)>0){
	    	    	$temp= $weburlsA[0];
	    	        if(isset($temp) && !is_null($temp)){
	    	    	   $tempA = explode('$', $temp);
	    	    	   if(count($tempA)==2){
	    	    	   	 $cur_name=$tempA[0];
	    	             $cur_url=$tempA[1];
	    	    	   }
	    	    	   if(count($tempA)==1){
	    	    	   	 $cur_name=$tempA[0];
	    	    	   }
	    	        }
	    	  }
	    	}	    	   
	    	$item['cur_item_url']=$cur_url;
	    	$item['cur_item_name']=$cur_name;
	    	$tempList[]=$item;
	      }
	    	$prodExpired = CacheManager::getExpireByCache(CacheManager::CACHE_PARAM_EXPIRED_POPULAR_PROGRAM);
	  	    CacheManager::setValueToCache($key, $tempList,$prodExpired);
	    }
	    return $tempList;
	}
	
    public static function listTVNetItems($top_id,$limit,$offset){
	    $device=IjoyPlusServiceUtils::getDevice();
   	    $where='';
   	    if($device ===false){
           $key =SearchManager::CACHE_LIST_ITEMS_BY_TYPE_LIMIT_OFFSET.'_TOPTVNet_'.$top_id.'_LIMIT_'.$limit.'_OFFSET_'.$offset;
   	    }else {
   	    	$where=' AND (can_search_device like \'%'.$device.'%\' or can_search_device is null or can_search_device =\'\' ) ';
   	    	$key =SearchManager::CACHE_LIST_ITEMS_BY_TYPE_LIMIT_OFFSET.'_TOPTVNet_'.$top_id.'_LIMIT_'.$limit.'_OFFSET_'.$offset.'_DEVICE_'.$device;
   	    }
	    $items = CacheManager::getValueFromCache($key);
	    if($items){
	    	return $items;
	    }
	    $items= Yii::app()->db->createCommand()
			->select('items.id as id, vod.d_id as prod_id,vod.d_name as prod_name, vod.d_downurl as play_urls,vod.d_level as definition,vod.d_content as prod_summary, vod.d_type as prod_type,  substring_index( vod.d_pic_ipad, \'{Array}\', 1 )  as prod_pic_url,vod.d_starring as stars,vod.d_directed as directors ,vod.favority_user_count as favority_num ,vod.good_number as support_num ,vod.d_year as publish_date,vod.d_score as score,vod.d_area as area, vod.d_remarks as max_episode, vod.d_state as cur_episode , vod.duraning as duration ')
			->from('mac_vod_topic_items as items')
			->join("mac_vod as vod","items.vod_id=vod.d_id")
			->where('items.flag=:t_flag and items.topic_id=:topic_id and vod.d_hide=0 '.$where, array(
				    ':t_flag'=>1,
				    ':topic_id'=>$top_id,
			))->order('items.disp_order desc, vod.d_level desc ,vod.d_good desc,vod.d_time DESC ')->limit($limit)->offset($offset)
			->queryAll();
		
	    $tempList= array();
	    if(isset($items) && !is_null($items) && is_array($items)){
	    	foreach ($items as $item){
//	    	  $item['definition']='4';
	    	  if($item['prod_type'] ==='1'){
	    	  	$item['play_urls'] = ProgramUtil::parseMovidePlayurl($item['play_urls']);
	    	  }else {
	    	  	$item['play_urls']=array();
	    	  }
	    	  $tempList[]=$item;
	      }
	    	$prodExpired = CacheManager::getExpireByCache(CacheManager::CACHE_PARAM_EXPIRED_POPULAR_PROGRAM);
	  	    CacheManager::setValueToCache($key, $tempList,$prodExpired);
	    }
	    return $tempList;
	}
	
   
   public static function searchProgram($keyword,$limit,$offset){
	    $device=IjoyPlusServiceUtils::getDevice();
   	    $where='';
   	    if($device ===false){
           $key =SearchManager::CACHE_SEARCH_PROD_BY_CONTENT_LIMIT_OFFSET.'_'.$keyword.'_LIMIT_'.$limit.'_OFFSET_'.$offset;
   	    }else {
   	    	$where=' AND (can_search_device like \'%'.$device.'%\' or can_search_device is null or can_search_device =\'\' ) ';
   	       $key =SearchManager::CACHE_SEARCH_PROD_BY_CONTENT_LIMIT_OFFSET.'_'.$keyword.'_LIMIT_'.$limit.'_OFFSET_'.$offset.'_DEVICE_'.$device;
   	    }
	    $prods = CacheManager::getValueFromCache($key);
	    if($prods){
	    	return $prods;
	    }
	    
	    $keyword='%'.$keyword.'%';
//	    $keyword= iconv("iso-8859-1","UTF-8",$keyword);
	    $prods= Yii::app()->db->createCommand()
			->select('d_id as prod_id, d_name as prod_name, d_type as prod_type, d_level as definition,d_pic as prod_pic_url,  substring_index( d_pic_ipad, \'{Array}\', 1 )  as big_prod_pic_url,d_content as prod_sumary,d_starring as star,d_directed as director,d_score as score ,favority_user_count as favority_num ,good_number as support_num ,d_year as publish_date,d_area as area , d_remarks as max_episode, d_state as cur_episode , duraning as duration ')
			->from('mac_vod ')
			->where('d_hide=:d_hide  and d_type in (1,2,3,131) and ( d_directed like \''.$keyword.'\' or d_starring like \''.$keyword.'\' or d_name like \''.$keyword.'\' or d_enname like \''.$keyword.'\'   )' .$where, array(
				    ':d_hide'=>0,
			))->order('d_level desc ,d_play_num desc,d_type asc ,d_good desc,d_time DESC')->limit($limit)->offset($offset)
			->queryAll();
	    if(isset($prods) && !is_null($prods)){
	    	$prodExpired = CacheManager::getExpireByCache(CacheManager::CACHE_PARAM_EXPIRED_POPULAR_PROGRAM);
	  	    CacheManager::setValueToCache($key, $prods,$prodExpired);
	    }
	    return $prods;
	}
	
   public static function searchProgramByType($keyword,$type,$limit,$offset){
	    $device=IjoyPlusServiceUtils::getDevice();
   	    $where='';
   	    if($device ===false){
            $key =SearchManager::CACHE_SEARCH_PROD_BY_CONTENT_LIMIT_OFFSET.'_'.$keyword.'_type_'.$type.'_LIMIT_'.$limit.'_OFFSET_'.$offset;
   	    }else {
   	    	$where=' AND (can_search_device like \'%'.$device.'%\' or can_search_device is null or can_search_device =\'\' ) ';
   	    	$key =SearchManager::CACHE_SEARCH_PROD_BY_CONTENT_LIMIT_OFFSET.'_'.$keyword.'_type_'.$type.'_LIMIT_'.$limit.'_OFFSET_'.$offset.'_DEVICE_'.$device;
   	    }
	    $prods = CacheManager::getValueFromCache($key);
	    if($prods){
	    	return $prods;
	    }
	    
	    $keyword='%'.$keyword.'%';
//	    $keyword= iconv("iso-8859-1","UTF-8",$keyword);
	    $prods= Yii::app()->db->createCommand()
			->select('d_id as prod_id, d_name as prod_name, d_type as prod_type, d_level as definition,d_pic as prod_pic_url,  substring_index( d_pic_ipad, \'{Array}\', 1 )  as big_prod_pic_url,d_content as prod_sumary,d_starring as star,d_directed as director,d_score as score ,favority_user_count as favority_num ,good_number as support_num ,d_year as publish_date,d_area as area, d_remarks as max_episode, d_state as cur_episode , duraning as duration ')
			->from('mac_vod ')
			->where('d_hide=:d_hide and d_type in ('.$type.') and ( d_directed like \''.$keyword.'\' or d_starring like \''.$keyword.'\' or d_name like \''.$keyword.'\' or d_enname like \''.$keyword.'\'   ) '.$where, array(
				    ':d_hide'=>0,
			))->order('d_level desc ,d_play_num desc,d_type asc ,d_good desc,d_time DESC')->limit($limit)->offset($offset)
			->queryAll();
	    if(isset($prods) && !is_null($prods)){
	    	$prodExpired = CacheManager::getExpireByCache(CacheManager::CACHE_PARAM_EXPIRED_POPULAR_PROGRAM);
	  	    CacheManager::setValueToCache($key, $prods,$prodExpired);
	    }
	    return $prods;
	}
	
    public static function searchProgramCapitalByType($keyword,$type,$limit,$offset){
	    $device=IjoyPlusServiceUtils::getDevice();
   	    $where='';
   	    
   	    if(!isset($type) || $type == null || $type ===''){
   	    	$type='';
   	    }
   	    
   	    if($device ===false){
            $key =SearchManager::CACHE_SEARCH_PROD_BY_CONTENT_LIMIT_OFFSET.'_Capital_'.$keyword.'_type_'.$type.'_LIMIT_'.$limit.'_OFFSET_'.$offset;
   	    }else {
   	    	$where=' AND (can_search_device like \'%'.$device.'%\' or can_search_device is null or can_search_device =\'\' ) ';
   	    	$key =SearchManager::CACHE_SEARCH_PROD_BY_CONTENT_LIMIT_OFFSET.'_Capital_'.$keyword.'_type_'.$type.'_LIMIT_'.$limit.'_OFFSET_'.$offset.'_DEVICE_'.$device;
   	    }
	    $prods = CacheManager::getValueFromCache($key);
	    if($prods){
	    	return $prods;
	    }
	    
	    $keyword='%'.$keyword.'%';
//	    $keyword= iconv("iso-8859-1","UTF-8",$keyword);
	    if( $type ===''){
		    $prods= Yii::app()->db->createCommand()
				->select('d_id as prod_id, d_name as prod_name, d_type as prod_type, d_level as definition,d_pic as prod_pic_url,  substring_index( d_pic_ipad, \'{Array}\', 1 )  as big_prod_pic_url,d_content as prod_sumary,d_starring as star,d_directed as director,d_score as score ,favority_user_count as favority_num ,good_number as support_num ,d_year as publish_date,d_area as area, d_remarks as max_episode, d_state as cur_episode , duraning as duration ')
				->from('mac_vod ')
				->where('d_hide=:d_hide  and ( d_enname like \''.$keyword.'\' or d_capital_name like \''.$keyword.'\'  ) '.$where, array(
					    ':d_hide'=>0,
				))->order('d_level desc ,d_play_num desc,d_type asc ,d_good desc,d_time DESC')->limit($limit)->offset($offset)
				->queryAll();
        }else {
		    $prods= Yii::app()->db->createCommand()
				->select('d_id as prod_id, d_name as prod_name, d_type as prod_type, d_level as definition,d_pic as prod_pic_url,  substring_index( d_pic_ipad, \'{Array}\', 1 )  as big_prod_pic_url,d_content as prod_sumary,d_starring as star,d_directed as director,d_score as score ,favority_user_count as favority_num ,good_number as support_num ,d_year as publish_date,d_area as area, d_remarks as max_episode, d_state as cur_episode , duraning as duration ')
				->from('mac_vod ')
				->where('d_hide=:d_hide and d_type in ('.$type.') and ( d_enname like \''.$keyword.'\' or d_capital_name like \''.$keyword.'\'  ) '.$where, array(
					    ':d_hide'=>0,
				))->order('d_level desc ,d_play_num desc,d_type asc ,d_good desc,d_time DESC')->limit($limit)->offset($offset)
				->queryAll();        	
        }
	    if(isset($prods) && !is_null($prods)){
	    	$prodExpired = CacheManager::getExpireByCache(CacheManager::CACHE_PARAM_EXPIRED_POPULAR_PROGRAM);
	  	    CacheManager::setValueToCache($key, $prods,$prodExpired);
	    }
	    return $prods;
	}
	
   public static function filterPrograms($type,$sub_type,$area,$year,$limit,$offset){
   	    $device=IjoyPlusServiceUtils::getDevice();
   	    $whereDevice='';
   	    if($device ===false){
           $key =SearchManager::CACHE_SEARCH_PROD_BY_CONTENT_LIMIT_OFFSET.'_subtype_'.$sub_type.'_area_'.$area.'_year_'.$year.'_type_'.$type.'_LIMIT_'.$limit.'_OFFSET_'.$offset;
   	    }else{
   	    	$whereDevice=' AND (can_search_device like \'%'.$device.'%\' or can_search_device is null or can_search_device =\'\' ) ';
   	    	$key =SearchManager::CACHE_SEARCH_PROD_BY_CONTENT_LIMIT_OFFSET.'_subtype_'.$sub_type.'_area_'.$area.'_year_'.$year.'_type_'.$type.'_LIMIT_'.$limit.'_OFFSET_'.$offset.'_DEVICE_'.$device;
   	    }
	    $prods = CacheManager::getValueFromCache($key);
	    if($prods){
	    	return $prods;
	    }
	    $where=" ";	   
	    
	    if (!(is_null($year) || $year==='')){
	    	if($year==='其他'){
	    		$where=$where." and ( d_year < '2004' or d_year like '%".$year."%' )";
	    	}else {
	        	$where=$where." and d_year like '%".$year."%' ";
	    	}
	    }
	    
	    if (!(is_null($sub_type) || $sub_type==='')){
	    	$where=$where." and d_type_name like '%".$sub_type."%' ";
	    }
	    
        if (!(is_null($area) || $area==='')){
        	$areaGroup="";
        	if (!(is_null($type) || $type=='') && ($type ==='1' || $type ==='2'  || $type ==='3'  || $type ==='131' )){
        		try{
        			if(array_key_exists($area, Yii::app()->params['area_group'][$type])){
        			  $areaGroup = Yii::app()->params['area_group'][$type][$area];
        			}
        		}catch (Exception $e){
        			
        		}
//        		
        	}  
        	//var_dump($areaGroup);
        	     	
	        if (!(is_null($areaGroup) || $areaGroup=='')){
		    	$where=$where." and ( d_area like '%".$area."%'  or substring_index( d_area, ' ', 1 ) in (".$areaGroup.") ) ";
		    }else {
		    	$where=$where." and d_area like '%".$area."%' ";
		    }	    
	    } 
//	    var_dump($where);
	    if (!(is_null($type) || $type=='')){
	    	$where=$where." and d_type=".$type." ";
	    }
	    
	    $prods= Yii::app()->db->createCommand()
			->select('d_id as prod_id, d_name as prod_name, d_level as definition, d_type as prod_type,d_pic as prod_pic_url,  substring_index( d_pic_ipad, \'{Array}\', 1 )  as big_prod_pic_url,d_content as prod_sumary,d_starring as star,d_directed as director,d_score as score ,favority_user_count as favority_num ,good_number as support_num ,d_year as publish_date,d_area as area, d_remarks as max_episode, d_state as cur_episode, duraning as duration ')
			->from('mac_vod ')
			->where('d_hide=:d_hide '.$where .$whereDevice, array(
				    ':d_hide'=>0,
			))->order('d_level desc ,d_play_num desc,d_good desc,d_time DESC')->limit($limit)->offset($offset)
			->queryAll();
			
	    if(isset($prods) && !is_null($prods)){
	    	$prodExpired = CacheManager::getExpireByCache(CacheManager::CACHE_PARAM_EXPIRED_POPULAR_PROGRAM);
	  	    CacheManager::setValueToCache($key, $prods,$prodExpired);
	    }
	    return $prods;
	}
}

?>