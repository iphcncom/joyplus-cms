<?php

/**
 * This is the model class for table "tbl_my_dynamic".
 *
 * The followings are the available columns in table 'tbl_my_dynamic':
 * @property integer $id
 * @property integer $author_id
 * @property integer $content_type
 * @property string $content_name
 * @property integer $content_id
 * @property string $create_date
 * @property integer $status
 * @property integer $order_position
 * @property integer $dynamic_type
 * @property string $content_pic_url
 *
 * The followings are the available model relations:
 * @property TblUser $author
 * @property TblComments $content
 */
class Dynamic extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Dynamic the static model class
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
		return 'tbl_my_dynamic';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id', 'required'),
			array('id, author_id, content_type, content_id, status, order_position, dynamic_type', 'numerical', 'integerOnly'=>true),
			array('content_name', 'length', 'max'=>50),
			array('content_pic_url', 'length', 'max'=>300),
			array('create_date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, author_id, content_type, content_name, content_id, create_date, status, order_position, dynamic_type, content_pic_url', 'safe', 'on'=>'search'),
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
			'author' => array(self::BELONGS_TO, 'TblUser', 'author_id'),
			'content' => array(self::BELONGS_TO, 'TblComments', 'content_id'),
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
			'content_type' => 'Content Type',
			'content_name' => 'Content Name',
			'content_id' => 'Content',
			'create_date' => 'Create Date',
			'status' => 'Status',
			'order_position' => 'Order Position',
			'dynamic_type' => 'Dynamic Type',
			'content_pic_url' => 'Content Pic Url',
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
		$criteria->compare('content_type',$this->content_type);
		$criteria->compare('content_name',$this->content_name,true);
		$criteria->compare('content_id',$this->content_id);
		$criteria->compare('create_date',$this->create_date,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('order_position',$this->order_position);
		$criteria->compare('dynamic_type',$this->dynamic_type);
		$criteria->compare('content_pic_url',$this->content_pic_url,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}