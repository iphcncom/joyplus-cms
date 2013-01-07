<?php

/**
 * This is the model class for table "tbl_user".
 *
 * The followings are the available columns in table 'tbl_user':
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $phone
 * @property string $sina_wb_user_id
 * @property string $qq_wb_user_id
 * @property string $ren_user_id
 * @property string $douban_user_id
 * @property string $create_date
 * @property string $last_modify_date
 * @property string $last_login_date
 * @property string $user_photo_url
 * @property string $user_bg_photo_url
 * @property integer $status
 * @property string $signature
 * @property string $other_part_one_user_id
 * @property string $other_part_two_user_id
 * @property string $other_part_three_user_id
 * @property string $other_part_four_user_id
 * @property integer $like_number
 * @property integer $watch_number
 * @property integer $fan_number
 * @property string $category
 * @property integer $grade
 * @property integer $prestige
 * @property string $nickname
 * @property string $good_number
 * @property string $top_number
 * @property string $favority_number
 * @property string $share_number
 * @property string $device_number
 * @property string $device_type
 *
 * The followings are the available model relations:
 * @property TblComments[] $tblComments
 * @property TblMyFriend[] $tblMyFriends
 * @property TblMyFriend[] $tblMyFriends1
 */
class User extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	 /**
         * Checks if the given password is correct.
         * @param string the password to be validated
         * @return boolean whether the password is valid
         */
        public function validatePassword($password)
        {
                return $this->password === md5($password);
        }
	

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_user';
	}
	
    function bindAccount($userid,$third_part_userid,$source){
		$model=$this->findByPk($userid);
		if($model !==null){
			switch ($source){
				case Constants::THIRD_PART_ACCOUNT_DOUBAN:
					$model->douban_user_id=$third_part_userid;
					break;
				case Constants::THIRD_PART_ACCOUNT_QQ:
					$model->qq_wb_user_id=$third_part_userid;
					break;
				case Constants::THIRD_PART_ACCOUNT_REN_REN:
					$model->ren_user_id=$third_part_userid;
					break;
				case Constants::THIRD_PART_ACCOUNT_SINA:
					$model->sina_wb_user_id=$third_part_userid;
					break;
				case Constants::THIRD_PART_ACCOUNT_LOCAL_CONTACT:
					$model->phone=$third_part_userid;
					break;
					
					
			}
			//clear device number
			$model->device_number='';
			if($model->save()){
				return Constants::SUCC;
			}else{
				return Constants::SYSTEM_ERROR;
			}

		}else {
			Constants::USER_NOT_EXIST;
		}
	}

	function unBindAccount($userid,$source){
		$model=$this->findByPk($userid);
		if($model !==null){
			switch ($source){
				case Constants::THIRD_PART_ACCOUNT_DOUBAN:
					$model->douban_user_id='';
					break;
				case Constants::THIRD_PART_ACCOUNT_QQ:
					$model->qq_wb_user_id='';
					break;
				case Constants::THIRD_PART_ACCOUNT_REN_REN:
					$model->ren_user_id='';
					break;
				case Constants::THIRD_PART_ACCOUNT_SINA:
					$model->sina_wb_user_id='';
					break;
			}
			if($model->save()){
				return Constants::SUCC;
			}else{
				return Constants::SYSTEM_ERROR;
			}

		}else {
			Constants::USER_NOT_EXIST;
		}
	}

	function updateLastLoginDate($userid){
	  try{
		  Yii::app()->db->createCommand()->update($this->tableName(),array('last_login_date'=>new CDbExpression('NOW()')), 'id=:id', 
	      array(
//	        ':last_login_date'=>new CDbExpression('NOW()'),
	        ':id'=>$userid,
	      )
	      );
	      return Constants::SUCC;
	  }catch (Exception $e){
	    return Constants::SYSTEM_ERROR;
	  }
	}
    public function deleteUserInfo($userid){
	  $transaction = Yii::app()->db->beginTransaction(); 
	  try{
	  	  //delete user infor
		  Yii::app()->db->createCommand()->delete($this->tableName(),'id=:id', 
		      array(
		        ':id'=>$userid,
		      )
	      ); 
	      Yii::app()->db->createCommand()->delete('tbl_comments', 'author_id=:id', 
	      array(
	        ':id'=>$userid,
	      )
	      );
	      
	      Yii::app()->db->createCommand()->delete('tbl_my_dynamic', 'author_id=:id', 
	      array(
	        ':id'=>$userid,
	      )
	      );
	  	  //tbl_play_history
	  	  
	      Yii::app()->db->createCommand()->delete('tbl_play_history', 'author_id=:id', 
	      array(
	        ':id'=>$userid,
	      )
	      );
	  	  
	  	  $transaction->commit();
	      return Constants::SUCC;
	  }catch (Exception $e){	  	 
        $transaction->rollback();
        Yii::trace($e);
	    return Constants::SYSTEM_ERROR;
	  }
	}
    function updatePicUrl($userid,$url){
      $transaction = Yii::app()->db->beginTransaction(); 
      try{
		  Yii::app()->db->createCommand()->update($this->tableName(),array('user_photo_url'=>$url), 'id=:id', 
	      array(
//	        ':user_photo_url'=>$url,
	        ':id'=>$userid,
	      )
	      );
	      
//	      Yii::app()->db->createCommand()->update("tbl_my_friend",array('friend_photo_url'=>$url), 'friend_id=:id', 
//	      array(
////	        ':user_photo_url'=>$url,
//	        ':id'=>$userid,
//	      )
//	      );
//	      
//	      Yii::app()->db->createCommand()->update('tbl_notfiy_msg',array('notify_user_pic_url'=>$url), 'nofity_user_id=:id', 
//	      array(
////	        ':user_photo_url'=>$url,
//	        ':id'=>$userid,
//	      )
//	      );
	      
	      
	      
	      Yii::app()->db->createCommand()->update('tbl_comments',array('author_photo_url'=>$url), 'author_id=:id', 
	      array(
//	        ':user_photo_url'=>$url,
	        ':id'=>$userid,
	      )
	      );
	      
//	      Yii::app()->db->createCommand()->update('tbl_my_dynamic',array('content_pic_url'=>$url), 'content_id=:id', 
//	      array(
//	        ':id'=>$userid,
//	      )
//	      );
	      
	      $transaction->commit();
	      return Constants::SUCC;
	  }catch (Exception $e){	  	 
        $transaction->rollback();
        Yii::trace($e);
	    return Constants::SYSTEM_ERROR;
	  }
	}
	
   function updateNickname($userid,$nickname){
      $transaction = Yii::app()->db->beginTransaction(); 
      try{
		  Yii::app()->db->createCommand()->update($this->tableName(),array('nickname'=>$nickname), 'id=:id', 
	      array(
	        ':id'=>$userid,
	      )
	      );
	      
//	      Yii::app()->db->createCommand()->update("tbl_my_friend",array('friend_username'=>$nickname), 'friend_id=:id', 
//	      array(
////	        ':user_photo_url'=>$url,
//	        ':id'=>$userid,
//	      )
//	      );
//	      
//	      Yii::app()->db->createCommand()->update('tbl_notfiy_msg',array('notify_user_name'=>$nickname), 'nofity_user_id=:id', 
//	      array(
////	        ':user_photo_url'=>$url,
//	        ':id'=>$userid,
//	      )
//	      );
	     
	      
	      Yii::app()->db->createCommand()->update('tbl_comments',array('author_username'=>$nickname), 'author_id=:id', 
	      array(
//	        ':user_photo_url'=>$url,
	        ':id'=>$userid,
	      )
	      );
	      
//	      Yii::app()->db->createCommand()->update('tbl_my_dynamic',array('content_pic_url'=>$nickname), 'content_id=:id', 
//	      array(
//	        ':id'=>$userid,
//	      )
//	      );
	      
	      $transaction->commit();
	      return Constants::SUCC;
	  }catch (Exception $e){	  	 
        $transaction->rollback();
        Yii::trace($e);
	    return Constants::SYSTEM_ERROR;
	  }
	}
	
	
	function prestiges($limit,$offset){
		return Yii::app()->db->createCommand()
	    ->select('id, nickname, user_photo_url as user_pic_url')
	    ->from($this->tableName())
	    ->where('status=:status and prestige=:prestige', array(    
				    ':prestige'=>Constants::USER_IS_PRESTIGE_FLAG,
				    ':status'=>Constants::OBJECT_APPROVAL,
				 ))->limit($limit)->offset($offset)
	    ->queryAll();								
	}
	
    function userPrestiges($userid,$limit,$offset){
    	$sql = "SELECT user.id, user.nickname, user.user_photo_url AS user_pic_url, (case when f.id is null then 0 else 1 end) as is_follow FROM tbl_user user 
                 LEFT JOIN tbl_my_friend f ON user.id = f.friend_id and f.status=1 
             AND f.author_id =".$userid." 
            WHERE user.status=1 and user.prestige =".Constants::USER_IS_PRESTIGE_FLAG." limit ".$offset .", ".$limit;
//    	var_dump($sql);
		return Yii::app()->db->createCommand($sql)	   
	    ->queryAll();								
	}
	
    function updateBGPicUrl($userid,$url){
      try{
		  Yii::app()->db->createCommand()->update($this->tableName(),array('user_bg_photo_url'=>':user_bg_photo_url'), 'id=:id', 
	      array(
	        ':user_bg_photo_url'=>$url,
	        ':id'=>$userid,
	      )
	      );
	      return Constants::SUCC;
	  }catch (Exception $e){
	    return Constants::SYSTEM_ERROR;
	  }
	}
	
	function updateLikeCount($userid,$count){
	  try{
	  	  if($count<0){
	  	    Yii::app()->db->createCommand("update ".$this->tableName() .' set like_number= case when like_number>='.abs($count) .' then like_number'.$count.' else 0 end where id='.$userid)->execute();
	  	  }
	      if($count>0){
	        Yii::app()->db->createCommand("update ".$this->tableName() .' set like_number=like_number+'.$count .' where id='.$userid)->execute();
	      }
	      return Constants::SUCC;
	  }catch (Exception $e){
	    return Constants::SYSTEM_ERROR;
	  }
	}
	
   function updateTopBDCount($userid,$count){
	  try{
	  	  if($count<0){
	  	    Yii::app()->db->createCommand("update ".$this->tableName() .' set top_number= case when top_number>='.abs($count) .' then top_number'.$count.' else 0 end where id='.$userid)->execute();
	  	  }
	      if($count>0){
	        Yii::app()->db->createCommand("update ".$this->tableName() .' set top_number=top_number+'.$count .' where id='.$userid)->execute();
	      }
	      return Constants::SUCC;
	  }catch (Exception $e){
	    return Constants::SYSTEM_ERROR;
	  }
	}
	
    function updateFavorityCount($userid,$count){
	  try{
	  	  if($count<0){
	  	    Yii::app()->db->createCommand("update ".$this->tableName() .' set favority_number= case when favority_number>='.abs($count) .' then favority_number'.$count.' else 0 end where id='.$userid)->execute();
	  	  }
	      if($count>0){
	        Yii::app()->db->createCommand("update ".$this->tableName() .' set favority_number=favority_number+'.$count .' where id='.$userid)->execute();
	      }
	      return Constants::SUCC;
	  }catch (Exception $e){
	    return Constants::SYSTEM_ERROR;
	  }
	}
	
    function updateShareCount($userid,$count){
	  try{
	  	  if($count<0){
	  	    Yii::app()->db->createCommand("update ".$this->tableName() .' set share_number= case when share_number>='.abs($count) .' then share_number'.$count.' else 0 end where id='.$userid)->execute();
	  	  }
	      if($count>0){
	        Yii::app()->db->createCommand("update ".$this->tableName() .' set share_number=share_number+'.$count .' where id='.$userid)->execute();
	      }
	      return Constants::SUCC;
	  }catch (Exception $e){
	    return Constants::SYSTEM_ERROR;
	  }
	}
	
   function updateProgramGoodCount($userid,$count){
	  try{
	  	  if($count<0){
	  	    Yii::app()->db->createCommand("update ".$this->tableName() .' set good_number= case when good_number>='.abs($count) .' then good_number'.$count.' else 0 end where id='.$userid)->execute();
	  	  }
	      if($count>0){
	        Yii::app()->db->createCommand("update ".$this->tableName() .' set good_number=good_number+'.$count .' where id='.$userid)->execute();
	      }
	      return Constants::SUCC;
	  }catch (Exception $e){
	    return Constants::SYSTEM_ERROR;
	  }
	}

	function updateFollowUserCount($userid,$count){
	  try{
	  	 if($count<0){
	  	 	Yii::app()->db->createCommand("update ".$this->tableName() .' set watch_number= case when watch_number>='.abs($count) .' then watch_number'.$count.' else 0 end where id='.$userid)->execute();
	  	 }
	  	 if($count>0){
		  Yii::app()->db->createCommand("update ".$this->tableName() .' set watch_number=watch_number+'.$count .' where id='.$userid)->execute();
	  	 }
	      return Constants::SUCC;
	  }catch (Exception $e){
	    return Constants::SYSTEM_ERROR;
	  }
	}

	function updateFanCount($userid,$count){
	  try{
	      if($count<0){
	  	 	Yii::app()->db->createCommand("update ".$this->tableName() .' set fan_number= case when fan_number>='.abs($count) .' then fan_number'.$count.' else 0 end where id='.$userid)->execute();
	  	  }
	  	  
	  	  if($count>0){
	        Yii::app()->db->createCommand("update ".$this->tableName() .' set fan_number=fan_number+'.$count .' where id='.$userid)->execute();
	  	  }
	      return Constants::SUCC;
	  }catch (Exception $e){
	    return Constants::SYSTEM_ERROR;
	  }
	}
	
    public function searchThirdPartUsers($userid,$source_type){
        return Yii::app()->db->createCommand()
    ->select('friend_id, thirdpart_id')
    ->from('tbl_user_thirdpart')
    ->where('author_id=:author_id and thirdpart_type=:type', array(
			    ':author_id'=>$userid,
			    ':type'=>$source_type,
			 ))->queryAll();
    }
    
    public function searchUserByThirdParty($source_type,$source_ids){
        $sql = '';
    	switch ($source_type){
				case Constants::THIRD_PART_ACCOUNT_DOUBAN:
					$sql='douban_user_id=:party';
					break;
				case Constants::THIRD_PART_ACCOUNT_QQ:
					$sql='qq_wb_user_id=:party';
					break;
				case Constants::THIRD_PART_ACCOUNT_REN_REN:
					$sql='ren_user_id=:party';
					break;
				case Constants::THIRD_PART_ACCOUNT_SINA:
					$sql='sina_wb_user_id=:party';
					break;
		 }
       return  $this->find(array(
			'condition'=>'status=:status and '.$sql,
			'params'=>array(
			    ':status'=>Constants::OBJECT_APPROVAL,
			    ':party'=>$source_ids,
			 ),
		));
    }
    
	public function generateUsersByThirdPart($source_type,$source_ids){
		$command = Yii::app()->db->createCommand()		
		->from('tbl_user');
        $command ->where('status=:status', array(
			    ':status'=>Constants::OBJECT_APPROVAL,
				));
		switch ($source_type){
			case Constants::THIRD_PART_ACCOUNT_DOUBAN:
				$command ->where(array('in', 'douban_user_id',$source_ids))
				->select('id, douban_user_id as third_part_id');
				break;
			case Constants::THIRD_PART_ACCOUNT_QQ:
				$command ->where(array('in', 'qq_wb_user_id',$source_ids))
				->select('id,qq_wb_user_id as third_part_id');
				break;
			case Constants::THIRD_PART_ACCOUNT_REN_REN:
				$command ->where(array('in', 'ren_user_id',$source_ids))
				->select('id, ren_user_id as third_part_id');
				break;
			case Constants::THIRD_PART_ACCOUNT_SINA:
				$command ->select('id,sina_wb_user_id as third_part_id');
				$command ->where(array('in', 'sina_wb_user_id',$source_ids));
				break;
			case Constants::THIRD_PART_ACCOUNT_LOCAL_CONTACT:
				$command ->select('id, phone as third_part_id');
				$command ->where(array('in', 'phone',$source_ids));
				break;
		}
        	$results = $command->queryAll();     
        	 	
        	if(isset($results) && is_array($results)){
        		//var_dump($results);  
        	   $transaction = Yii::app()->db->beginTransaction(); 
        	   try{
	        	   	Yii::app()->db->createCommand()->delete('tbl_user_thirdpart', 'author_id=:author_id and thirdpart_type=:type',
	        	   	array(
		        	     ':author_id'=>Yii::app()->user->id,
		        	     ':type'=>$source_type,
	        	   	));
        	      foreach ($results as $result){
		        	   Yii::app()->db->createCommand()->insert('tbl_user_thirdpart', array(
		        	     'friend_id'=>$result['id'],
		        	     'thirdpart_id'=>$result['third_part_id'],
		        	     'author_id'=>Yii::app()->user->id,
		        	     'thirdpart_type'=>$source_type,
		        	   ));
        	      }
	        	   $transaction->commit();
        	   }catch(Exception $e){
        	   	  $transaction->rollback();
        	   	  Yii::trace($e);
        	   }
        	}
	}
	
	public function findUser($userid){
	  return Yii::app()->db->createCommand('select nickname,username,user_photo_url from '.$this->tableName() .' where id=\''.$userid.'\'')->queryRow();
	}
	
	public function generateUIID($deviceid,$deviceType){
          $model=new User;
   		  $model->nickname='用户'.$deviceid;
   		  $model->username=$deviceid;   		  
   		  $model->device_number=$deviceid; 		  
   		  $model->device_type=$deviceType;
   		  $model->status=Constants::USER_APPROVAL;
   		  $model->create_date=new CDbExpression('NOW()');
          $model->save();
                   
          $uid=$this->makeUIID($model->id.'');
          $model->nickname='用户'.$uid;
   		  $model->username=$uid; 
   		  $model->save();
   		  
          return array(
	             'user_id'=>$model->id,
	           	 'nickname'=>$model->nickname,
	             'pic_url'=>$model->user_photo_url,
	             'username'=>$model->username,
	     );
	}
	
	private function makeUIID($uid){
	  $length=strlen($uid);
	  if($length<8){
	     for($i=0;$i<(8-$length-1);$i++){
	       $uid='0'.$uid;
	     }
	     $uid='1'.$uid;
	  }
	  return $uid;
	  
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('nickname', 'required'),
			array('status, like_number, watch_number, fan_number, grade, prestige', 'numerical', 'integerOnly'=>true),
			array('username', 'length', 'max'=>300),
			array('password, email, sina_wb_user_id, qq_wb_user_id, ren_user_id, douban_user_id, other_part_one_user_id, other_part_two_user_id, other_part_three_user_id, other_part_four_user_id', 'length', 'max'=>50),
			array('phone, category', 'length', 'max'=>20),
			array('user_photo_url, user_bg_photo_url', 'length', 'max'=>200),
			array('signature', 'length', 'max'=>300),
			array('nickname', 'length', 'max'=>80),
			array('create_date, last_modify_date, last_login_date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, username, password, email, phone, sina_wb_user_id, qq_wb_user_id, ren_user_id, douban_user_id, create_date, last_modify_date, last_login_date, user_photo_url, user_bg_photo_url, status, signature, other_part_one_user_id, other_part_two_user_id, other_part_three_user_id, other_part_four_user_id, like_number, watch_number, fan_number, category, grade, prestige, nickname', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
//		// NOTE: you may need to adjust the relation name and the related
//		// class name for the relations automatically generated below.
		return array(
//			'tblComments' => array(self::HAS_MANY, 'TblComments', 'author_id'),
//			'tblMyFriends' => array(self::HAS_MANY, 'TblMyFriend', 'friend_id'),
//			'tblMyFriends1' => array(self::HAS_MANY, 'TblMyFriend', 'author_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'username' => 'Username',
			'password' => 'Password',
			'email' => 'Email',
			'phone' => 'Phone',
			'sina_wb_user_id' => 'Sina Wb User',
			'qq_wb_user_id' => 'Qq Wb User',
			'ren_user_id' => 'Ren User',
			'douban_user_id' => 'Douban User',
			'create_date' => 'Create Date',
			'last_modify_date' => 'Last Modify Date',
			'last_login_date' => 'Last Login Date',
			'user_photo_url' => 'User Photo Url',
			'user_bg_photo_url' => 'User Bg Photo Url',
			'status' => 'Status',
			'signature' => 'Signature',
			'other_part_one_user_id' => 'Other Part One User',
			'other_part_two_user_id' => 'Other Part Two User',
			'other_part_three_user_id' => 'Other Part Three User',
			'other_part_four_user_id' => 'Other Part Four User',
			'like_number' => 'Like Number',
			'watch_number' => 'Watch Number',
			'fan_number' => 'Fan Number',
			'category' => 'Category',
			'grade' => 'Grade',
			'prestige' => 'Prestige',
			'nickname' => 'Nickname',
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
		$criteria->compare('username',$this->username,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('sina_wb_user_id',$this->sina_wb_user_id,true);
		$criteria->compare('qq_wb_user_id',$this->qq_wb_user_id,true);
		$criteria->compare('ren_user_id',$this->ren_user_id,true);
		$criteria->compare('douban_user_id',$this->douban_user_id,true);
		$criteria->compare('create_date',$this->create_date,true);
		$criteria->compare('last_modify_date',$this->last_modify_date,true);
		$criteria->compare('last_login_date',$this->last_login_date,true);
		$criteria->compare('user_photo_url',$this->user_photo_url,true);
		$criteria->compare('user_bg_photo_url',$this->user_bg_photo_url,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('signature',$this->signature,true);
		$criteria->compare('other_part_one_user_id',$this->other_part_one_user_id,true);
		$criteria->compare('other_part_two_user_id',$this->other_part_two_user_id,true);
		$criteria->compare('other_part_three_user_id',$this->other_part_three_user_id,true);
		$criteria->compare('other_part_four_user_id',$this->other_part_four_user_id,true);
		$criteria->compare('like_number',$this->like_number);
		$criteria->compare('watch_number',$this->watch_number);
		$criteria->compare('fan_number',$this->fan_number);
		$criteria->compare('category',$this->category,true);
		$criteria->compare('grade',$this->grade);
		$criteria->compare('prestige',$this->prestige);
		$criteria->compare('nickname',$this->nickname,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}