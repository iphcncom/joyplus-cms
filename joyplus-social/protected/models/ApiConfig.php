<?php

/**
 * This is the model class for table "mac_thirdpart_config".
 *
 * The followings are the available columns in table 'mac_thirdpart_config':
 * @property integer $id
 * @property string $device_name
 * @property string $company_name
 * @property string $api_url
 * @property string $logo_url
 * @property string $app_key
 * @property string $create_date
 * @property integer $status
 */
class ApiConfig extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ApiConfig the static model class
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
		return 'mac_thirdpart_config';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('status', 'numerical', 'integerOnly'=>true),
			array('company_name, api_url, logo_url', 'length', 'max'=>250),
			array('app_key', 'length', 'max'=>50),
			array('device_name, create_date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, device_name, company_name, api_url, logo_url, app_key, create_date, status', 'safe', 'on'=>'search'),
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
			'device_name' => 'Device Name',
			'company_name' => 'Company Name',
			'api_url' => 'Api Url',
			'logo_url' => 'Logo Url',
			'app_key' => 'App Key',
			'create_date' => 'Create Date',
			'status' => 'Status',
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
		$criteria->compare('device_name',$this->device_name,true);
		$criteria->compare('company_name',$this->company_name,true);
		$criteria->compare('api_url',$this->api_url,true);
		$criteria->compare('logo_url',$this->logo_url,true);
		$criteria->compare('app_key',$this->app_key,true);
		$criteria->compare('create_date',$this->create_date,true);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}