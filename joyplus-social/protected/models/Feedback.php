<?php

/**
 * This is the model class for table "tbl_feedback".
 *
 * The followings are the available columns in table 'tbl_feedback':
 * @property integer $id
 * @property integer $author_id
 * @property string $author_name
 * @property string $email
 * @property string $content
 * @property string $ip
 * @property string $user_agent
 * @property string $create_time
 */
class Feedback extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Feedback the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_feedback';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('content', 'required'),
			array('author_id', 'numerical', 'integerOnly'=>true),
			array('author_name, email, ip', 'length', 'max'=>255),
			array('user_agent', 'length', 'max'=>455),
			array('create_time', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, author_id, author_name, email, content, ip, user_agent, create_time', 'safe', 'on'=>'search'),
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
			'author_name' => 'Author Name',
			'email' => 'Email',
			'content' => 'Content',
			'ip' => 'Ip',
			'user_agent' => 'User Agent',
			'create_time' => 'Create Time',
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
		$criteria->compare('author_name',$this->author_name,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('content',$this->content,true);
		$criteria->compare('ip',$this->ip,true);
		$criteria->compare('user_agent',$this->user_agent,true);
		$criteria->compare('create_time',$this->create_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}