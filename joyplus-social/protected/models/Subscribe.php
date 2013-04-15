<?php

/**
 * This is the model class for table "mac_vod_subscribe".
 *
 * The followings are the available columns in table 'mac_vod_subscribe':
 * @property integer $id
 * @property integer $prod_id
 * @property string $create_date
 * @property integer $subscriber_num
 * @property string $prod_name
 * @property integer $author_id
 */
class Subscribe extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Subscribe the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
    public function getSubscribeByProd($userid,$prod_id){
		return  $this->find(array(
			'condition'=>'author_id=:author_id and prod_id=:content_id',
			'params'=>array(
			    ':author_id'=>$userid,
			    ':content_id'=>$prod_id,
			 ),
		));
		
	}
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'mac_vod_subscribe';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array(' author_id', 'required'),
			array('prod_id, subscriber_num, author_id', 'numerical', 'integerOnly'=>true),
			array('prod_name', 'length', 'max'=>300),
			array('create_date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, prod_id, create_date, subscriber_num, prod_name, author_id', 'safe', 'on'=>'search'),
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
			'prod_id' => 'Prod',
			'create_date' => 'Create Date',
			'subscriber_num' => 'Subscriber Num',
			'prod_name' => 'Prod Name',
			'author_id' => 'Author',
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
		$criteria->compare('prod_id',$this->prod_id);
		$criteria->compare('create_date',$this->create_date,true);
		$criteria->compare('subscriber_num',$this->subscriber_num);
		$criteria->compare('prod_name',$this->prod_name,true);
		$criteria->compare('author_id',$this->author_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}