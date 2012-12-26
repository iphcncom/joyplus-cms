<?php

/**
 * This is the model class for table "tbl_my_friend".
 *
 * The followings are the available columns in table 'tbl_my_friend':
 * @property integer $id
 * @property integer $author_id
 * @property integer $friend_id
 * @property string $create_date
 * @property integer $status
 * @property string $friend_photo_url
 * @property string $friend_username
 *
 * The followings are the available model relations:
 * @property TblUser $friend
 * @property TblUser $author
 */
class Friend extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Friend the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
   public function isFollowedByOwn($friend_id){
     $userid=Yii::app()->user->id;
     $friend= $this->find(array(
			'condition'=>'author_id=:author_id and friend_id=:friend_id and status=:status',
			'params'=>array(
			    ':author_id'=>$userid,
			    ':status'=>Constants::OBJECT_APPROVAL,
			    ':friend_id'=>$friend_id,
			 ),
		));
		if(isset($friend) && !is_null($friend)){
		  return true;
		}else {
		  return false;
		}
   }
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_my_friend';
	}
	
	public function searchFriends($userid,$limit=20,$offset=0){
	  $friends=  $this->findAll(array(
			'condition'=>'author_id=:author_id and status=:status',
			'order'=>'create_date DESC',
			'limit'=>$limit,
            'offset'=>$offset,
			'params'=>array(
			    ':author_id'=>$userid,
			    ':status'=>Constants::OBJECT_APPROVAL,
			 ),
		));
		$friendT = array();
		if(isset($friends) && is_array($friends)){
		  $count=0;
		  foreach ($friends as $friend){
		    $user = new UserVO();
		    $user->id=$friend->friend_id;
		    $user->user_pic_url=$friend->friend_photo_url;
		    $user->nickname=$friend->friend_username;
		    $friendT[$count]=$user;
		    $count++;
		  }
		}
		return $friendT; 
	}
	public function getFriend($userid,$friend_id){
	   return  $this->find(array(
			'condition'=>'author_id=:author_id and friend_id=:friend_id',
			'params'=>array(
			    ':author_id'=>$userid,
			    ':friend_id'=>$friend_id,
			 ),
		));
	}
	
    public function searchFans($userid,$limit=20,$offset=0){
    	$sql = "SELECT user.id, user.nickname, user.user_photo_url AS user_pic_url, (case when f.id is null then 0 else 1 end) as is_follow FROM 
 (select u.id as id, u.nickname as nickname, u.user_photo_url as user_photo_url from tbl_user u,tbl_my_friend p where 
u.id=p.author_id and p.friend_id=".$userid." and p.status=1 and u.status=1 ) as user LEFT JOIN tbl_my_friend f ON user.id = f.friend_id and f.status=1 
             AND f.author_id =".$userid." and f.status=1 limit ".$offset .", ".$limit;	   
		return Yii::app()->db->createCommand($sql)	   
	    ->queryAll();		
	}
	
	public function followFriends($friends){
		if(isset($friends) &&is_array($friends)){
			$transaction = Yii::app()->db->beginTransaction(); 
             try {
               $userid = Yii::app()->user->id;
               $count=0;
               foreach($friends as $id){
               	 if($id === $userid){
               	   continue;
               	 }
                 $user =User::model()->findByPk($id);
                 if(isset($user) && !is_null($user)){
                 	 $friendt = $this->getFriend($userid, $id);
                 	 if(!(isset($friendt) && !is_null($friendt) && $friendt->status ==Constants::OBJECT_APPROVAL)){
                 	   if(isset($friendt) && !is_null($friendt)) {
                 	      $friendt->status =Constants::OBJECT_APPROVAL;
                 	      $friendt->save();
					     
                 	   } else {
		                 $friend = new Friend;
		                 $friend->author_id=$userid;
		                 $friend->friend_id=$id;
		                 $friend->friend_photo_url=$user->user_photo_url;
		                 $friend->friend_username=$user->nickname;                 
					     $friend->create_date=new CDbExpression('NOW()');
					     $friend->status=Constants::OBJECT_APPROVAL;
					     $friend->save();
                 	 }
				      $count++;
					  $user->fan_number=$user->fan_number+1;
	                  $user->save();
				     //add dynamic
				     $dynamic = new Dynamic();
			         $dynamic->author_id=$userid;
			         $dynamic->content_id=$id;
			         $dynamic->status=Constants::OBJECT_APPROVAL;
			         $dynamic->create_date=new CDbExpression('NOW()');
			         $dynamic->content_name=$user->nickname; 
			         $dynamic->dynamic_type=Constants::DYNAMIC_TYPE_FOLLOW;
			         $dynamic->content_pic_url=$user->user_photo_url;
			         $dynamic->save();	
			         
			         //ADD NOTIFY MSG
			          $msg = new NotifyMsg();
				      $msg->author_id=$id;
				      $msg->nofity_user_id=Yii::app()->user->id;
				      $msg->notify_user_name=Yii::app()->user->getState("nickname");
			          $msg->notify_user_pic_url=Yii::app()->user->getState("pic_url");			      
				      $msg->created_date=new CDbExpression('NOW()');
				      $msg->status=Constants::OBJECT_APPROVAL;
				      $msg->notify_type=Constants::NOTIFY_TYPE_FOLLOW;
				      $msg->save();
                 }
                 }
               }
               if($count >0){
                 User::model()->updateFollowUserCount($userid, $count);
               }
               $transaction->commit();
               
               return true;
             } catch (Exception $e) {
       	       $transaction->rollback();
       	       return false;
             }
		}
		return true;
	}
//	
//public function likeFriends($friends){
//		if(isset($friends) &&is_array($friends)){
//			$transaction = Yii::app()->db->beginTransaction(); 
//             try {
//               $userid = Yii::app()->user->id;
//               foreach($friends as $id){                 
//			     User::model()->updateLikeCount($id, 1);
//			     
//			     //add dynamic
//			     $dynamic = new Dynamic();
//		         $dynamic->author_id=$userid;
//		         $dynamic->content_id=$id;
//		         $dynamic->status=Constants::OBJECT_APPROVAL;
//		         $dynamic->create_date=new CDbExpression('NOW()');
//		         $dynamic->content_name=$user->username; 
//		         $dynamic->dynamic_type=Constants::DYNAMIC_TYPE_LIKE_FRIEND;
//		         $dynamic->content_pic_url=$user->user_photo_url;
//		         $dynamic->save();	
//		         
//		         //ADD NOTIFY MSG
//		          $msg = new NotifyMsg();
//			      $msg->author_id=$id;
//			      $msg->nofity_user_id=Yii::app()->user->id;
//			      $msg->notify_user_name=Yii::app()->user->getState("username");
//		          $msg->notify_user_pic_url=Yii::app()->user->getState("pic_url");			      
//			      $msg->create_date=new CDbExpression('NOW()');
//			      $msg->status=Constants::OBJECT_APPROVAL;
//			      $msg->notify_type=Constants::NOTIFY_TYPE_LIKE_FRIEND;
//			      $msg->save();
//               }
//              
//               $transaction->commit();
//               return true;
//             } catch (Exception $e) {
//       	       $transaction->rollback();
//       	       return false;
//             }
//		}
//		return true;
//	}
//	
    public function destroyFriends($friends){
        if(isset($friends) &&is_array($friends)){
             $transaction = Yii::app()->db->beginTransaction(); 
             try {
               $userid = Yii::app()->user->id;
               $count=0;
               foreach($friends as $id){
                 $user =User::model()->findByPk($id);
                 if(isset($user) && !is_null($user)){
                 	 $friendt = $this->getFriend($userid, $id);
                 	 if(isset($friendt) && !is_null($friendt) && $friendt->status ==Constants::OBJECT_APPROVAL){   
                 	 	             	              	 
		                 $friendt->status =Constants::OBJECT_DELETE;
                 	     $friendt->save();		                 
		                 $count++;
		                 if( $user->fan_number >=1){
		                   $user->fan_number=$user->fan_number-1;
		                   $user->save();
		                 }
		                 if( $user->fan_number <0){
		                 	$user->fan_number=0;
		                    $user->save();
		                 }
		                 
					    // $friend->save();
		//			     User::model()->updateFanCount($id, -1);
		//			     User::model()->up
					     //add dynamic
//					     $dynamic = new Dynamic();
//				         $dynamic->author_id=$userid;
//				         $dynamic->content_id=$id;
//				         $dynamic->status=Constants::OBJECT_APPROVAL;
//				         $dynamic->create_date=new CDbExpression('NOW()');
//				         $dynamic->content_name=$user->nickname; 
//				         $dynamic->dynamic_type=Constants::DYNAMIC_TYPE_UN_FOLLOW;
//				         $dynamic->content_pic_url=$user->user_photo_url;
//				         $dynamic->save();	
				         
				         //ADD NOTIFY MSG
//				          $msg = new NotifyMsg();
//					      $msg->author_id=$id;
//					      $msg->nofity_user_id=Yii::app()->user->id;
//					      $msg->notify_user_name=Yii::app()->user->getState("username");
//				          $msg->notify_user_pic_url=Yii::app()->user->getState("pic_url");			      
//					      $msg->created_date=new CDbExpression('NOW()');
//					      $msg->status=Constants::OBJECT_APPROVAL;
//					      $msg->notify_type=Constants::NOTIFY_TYPE_UN_FOLLOW;
//					      $msg->save();
                 	 }
               }
              }
              if($count >0){
                User::model()->updateFollowUserCount($userid, -$count);
              }
               $transaction->commit();
               return true;
             } catch (Exception $e) {
       	       $transaction->rollback();
       	       return false;
             }
		}
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id, author_id, friend_id, status', 'numerical', 'integerOnly'=>true),
			array('friend_photo_url', 'length', 'max'=>300),
			array('friend_username', 'length', 'max'=>50),
			array('create_date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, author_id, friend_id, create_date, status, friend_photo_url, friend_username', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			
			'author' => array(self::BELONGS_TO, 'User', 'author_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'author_id' => 'Author',
			'friend_id' => 'Friend',
			'create_date' => 'Create Date',
			'status' => 'Status',
			'friend_photo_url' => 'Friend Photo Url',
			'friend_username' => 'Friend Username',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('author_id',$this->author_id);
		$criteria->compare('friend_id',$this->friend_id);
		$criteria->compare('create_date',$this->create_date,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('friend_photo_url',$this->friend_photo_url,true);
		$criteria->compare('friend_username',$this->friend_username,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}