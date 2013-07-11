<?php

/**
 * This is the model class for table "tbl_play_records".
 *
 * The followings are the available columns in table 'tbl_play_records':
 * @property integer $id
 * @property integer $author_id
 * @property integer $prod_type
 * @property string $prod_name
 * @property string $prod_subname
 * @property string $client
 * @property integer $prod_id
 * @property string $create_date
 */
class PlayRecords extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PlayRecords the static model class
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
		return 'tbl_play_records';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('author_id, prod_type, prod_id', 'numerical', 'integerOnly'=>true),
			array('prod_name, client', 'length', 'max'=>100),
			array('prod_subname', 'length', 'max'=>200),
			array('create_date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, author_id, prod_type, prod_name, prod_subname, client, prod_id, create_date', 'safe', 'on'=>'search'),
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
			'prod_type' => 'Prod Type',
			'prod_name' => 'Prod Name',
			'prod_subname' => 'Prod Subname',
			'client' => 'Client',
			'prod_id' => 'Prod',
			'create_date' => 'Create Date',
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
		$criteria->compare('prod_type',$this->prod_type);
		$criteria->compare('prod_name',$this->prod_name,true);
		$criteria->compare('prod_subname',$this->prod_subname,true);
		$criteria->compare('client',$this->client,true);
		$criteria->compare('prod_id',$this->prod_id);
		$criteria->compare('create_date',$this->create_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}