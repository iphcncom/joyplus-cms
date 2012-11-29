<?php

/**
 * This is the model class for table "tbl_user_thirdpart".
 *
 * The followings are the available columns in table 'tbl_user_thirdpart':
 * @property integer $id
 * @property integer $author_id
 * @property integer $friend_id
 * @property integer $thirdpart_id
 * @property integer $thirdpart_type
 */
class ThirdPartUser extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ThirdPartUser the static model class
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
		return 'tbl_user_thirdpart';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('author_id, friend_id, thirdpart_id, thirdpart_type', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, author_id, friend_id, thirdpart_id, thirdpart_type', 'safe', 'on'=>'search'),
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
			'friend_id' => 'Friend',
			'thirdpart_id' => 'Thirdpart',
			'thirdpart_type' => 'Thirdpart Type',
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
		$criteria->compare('thirdpart_id',$this->thirdpart_id);
		$criteria->compare('thirdpart_type',$this->thirdpart_type);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}