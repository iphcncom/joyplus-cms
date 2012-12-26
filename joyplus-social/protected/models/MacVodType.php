<?php

/**
 * This is the model class for table "mac_vod_type".
 *
 * The followings are the available columns in table 'mac_vod_type':
 * @property integer $t_id
 * @property string $t_name
 * @property string $t_enname
 * @property integer $t_sort
 * @property integer $t_pid
 * @property string $t_key
 * @property string $t_des
 * @property string $t_template
 * @property string $t_vodtemplate
 * @property string $t_playtemplate
 * @property integer $t_hide
 * @property string $t_union
 */
class MacVodType extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return MacVodType the static model class
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
		return 'mac_vod_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('t_sort', 'required'),
			array('t_sort, t_pid, t_hide', 'numerical', 'integerOnly'=>true),
			array('t_name, t_template, t_vodtemplate, t_playtemplate', 'length', 'max'=>64),
			array('t_enname', 'length', 'max'=>128),
			array('t_key, t_des', 'length', 'max'=>255),
			array('t_union', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('t_id, t_name, t_enname, t_sort, t_pid, t_key, t_des, t_template, t_vodtemplate, t_playtemplate, t_hide, t_union', 'safe', 'on'=>'search'),
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
			't_id' => 'T',
			't_name' => 'T Name',
			't_enname' => 'T Enname',
			't_sort' => 'T Sort',
			't_pid' => 'T Pid',
			't_key' => 'T Key',
			't_des' => 'T Des',
			't_template' => 'T Template',
			't_vodtemplate' => 'T Vodtemplate',
			't_playtemplate' => 'T Playtemplate',
			't_hide' => 'T Hide',
			't_union' => 'T Union',
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

		$criteria->compare('t_id',$this->t_id);
		$criteria->compare('t_name',$this->t_name,true);
		$criteria->compare('t_enname',$this->t_enname,true);
		$criteria->compare('t_sort',$this->t_sort);
		$criteria->compare('t_pid',$this->t_pid);
		$criteria->compare('t_key',$this->t_key,true);
		$criteria->compare('t_des',$this->t_des,true);
		$criteria->compare('t_template',$this->t_template,true);
		$criteria->compare('t_vodtemplate',$this->t_vodtemplate,true);
		$criteria->compare('t_playtemplate',$this->t_playtemplate,true);
		$criteria->compare('t_hide',$this->t_hide);
		$criteria->compare('t_union',$this->t_union,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}