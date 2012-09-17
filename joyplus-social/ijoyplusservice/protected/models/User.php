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
 *
 * The followings are the available model relations:
 * @property TblComments[] $tblComments
 * @property TblMyDynamic[] $tblMyDynamics
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

	function bindAccount($userid,$third_part_userid,$source){		
	  $model=$this->findByPk($userid);
	  if($model !==null){
	  	 switch ($source){
	  	 	case Constants::THIRD_PART_ACCOUNT_DOUBAN:
	  	 	  $model->douban_user_id=$third_part_userid;
	  	 	  break;
	  	 	case Constants::THIRD_PART_ACCOUNT_QQ:
	  	 	  $model->ren_user_id=$third_part_userid;
	  	 	  break;
	  	 	case Constants::THIRD_PART_ACCOUNT_REN_REN:
	  	 	  $model->qq_wb_user_id=$third_part_userid;
	  	 	  break;
	  	 	case Constants::THIRD_PART_ACCOUNT_SINA:
	  	 	  $model->sina_wb_user_id=$third_part_userid;
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
	
   function unBindAccount($userid,$source){		
	  $model=$this->findByPk($userid);
	  if($model !==null){
	  	 switch ($source){
	  	 	case Constants::THIRD_PART_ACCOUNT_DOUBAN:
	  	 	  $model->douban_user_id='';
	  	 	  break;
	  	 	case Constants::THIRD_PART_ACCOUNT_QQ:
	  	 	  $model->ren_user_id='';
	  	 	  break;
	  	 	case Constants::THIRD_PART_ACCOUNT_REN_REN:
	  	 	  $model->qq_wb_user_id='';
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
	   $model=$this->findByPk($userid);
	   if($model !==null){
	     $model->last_login_date=new CDbExpression('NOW()');
	     $model->save();
	   }
	}
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('status, like_number, watch_number, fan_number, grade, prestige', 'numerical', 'integerOnly'=>true),
			array('username', 'length', 'max'=>30),
//			array('email','email'),
//			array('username, password', 'required'),
			array('password, email, sina_wb_user_id, qq_wb_user_id, ren_user_id, douban_user_id, other_part_one_user_id, other_part_two_user_id, other_part_three_user_id, other_part_four_user_id', 'length', 'max'=>50),
			array('phone, category', 'length', 'max'=>20),
			array('user_photo_url, user_bg_photo_url', 'length', 'max'=>200),
			array('signature', 'length', 'max'=>300),
			array('create_date, last_modify_date, last_login_date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, username, password, email, phone, sina_wb_user_id, qq_wb_user_id, ren_user_id, douban_user_id, create_date, last_modify_date, last_login_date, user_photo_url, user_bg_photo_url, status, signature, other_part_one_user_id, other_part_two_user_id, other_part_three_user_id, other_part_four_user_id, like_number, watch_number, fan_number, category, grade, prestige', 'safe', 'on'=>'search'),
		);
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
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'comments' => array(self::HAS_MANY, 'Comment', 'author_id'),
			'myDynamics' => array(self::HAS_MANY, 'Dynamic', 'author_id'),
			'fans' => array(self::HAS_MANY, 'Friend', 'friend_id'),
			'friends' => array(self::HAS_MANY, 'Friend', 'author_id'),
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

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}