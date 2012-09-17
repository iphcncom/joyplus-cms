<?php

/**
 * This is the model class for table "video".
 *
 * The followings are the available columns in table 'video':
 * @property string $id
 * @property string $url
 * @property string $hash
 * @property string $program_id
 * @property integer $source_id
 * @property string $last_update_time
 * @property integer $enable
 */
class Video extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Video the static model class
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
		return 'video';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('url', 'required'),
			array('source_id, enable', 'numerical', 'integerOnly'=>true),
			array('hash', 'length', 'max'=>255),
			array('program_id', 'length', 'max'=>20),
			array('last_update_time', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, url, hash, program_id, source_id, last_update_time, enable', 'safe', 'on'=>'search'),
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
			'url' => 'Url',
			'hash' => 'Hash',
			'program_id' => 'Program',
			'source_id' => 'Source',
			'last_update_time' => 'Last Update Time',
			'enable' => 'Enable',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('url',$this->url,true);
		$criteria->compare('hash',$this->hash,true);
		$criteria->compare('program_id',$this->program_id,true);
		$criteria->compare('source_id',$this->source_id);
		$criteria->compare('last_update_time',$this->last_update_time,true);
		$criteria->compare('enable',$this->enable);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}