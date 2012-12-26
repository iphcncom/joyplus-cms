<?php

/**
 * This is the model class for table "tbl_system_config".
 *
 * The followings are the available columns in table 'tbl_system_config':
 * @property integer $id
 * @property string $sys_value
 * @property string $sys_key
 * @property string $create_date
 * @property string $sys_desc
 * @property integer $status
 */
class SystemConfig extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SystemConfig the static model class
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
		return 'tbl_system_config';
	}
	
     public static function getBooleanSystemConf($key,$defaultValue){
       $syskey = SystemConfig::model()->find('sys_key=:sys_key and status=:status',array(':sys_key'=>$key,':status'=>1));
       if(isset($syskey) && !is_null($syskey)){
       	if($syskey->sys_value==='1'){
       	  return true;
       	}else {
       	  return false;
       	}
       }else {
         return $defaultValue;
       }
    }
    
   public static function getStringSystemConf($key,$defaultValue){
       $syskey = SystemConfig::model()->find('sys_key=:sys_key and status=:status',array(':sys_key'=>$key,':status'=>1));
       if(isset($syskey) && !is_null($syskey) && isset($syskey->sys_value) && !is_null($syskey->sys_value)){
       	return $syskey->sys_value;
       }else {
         return $defaultValue;
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
			array('status', 'numerical', 'integerOnly'=>true),
			array('sys_key', 'length', 'max'=>100),
			array('sys_desc', 'length', 'max'=>500),
			array('sys_value, create_date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, sys_value, sys_key, create_date, sys_desc, status', 'safe', 'on'=>'search'),
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
			'sys_value' => 'Sys Value',
			'sys_key' => 'Sys Key',
			'create_date' => 'Create Date',
			'sys_desc' => 'Sys Desc',
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
		$criteria->compare('sys_value',$this->sys_value,true);
		$criteria->compare('sys_key',$this->sys_key,true);
		$criteria->compare('create_date',$this->create_date,true);
		$criteria->compare('sys_desc',$this->sys_desc,true);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}