<?php

/**
 * This is the model class for table "tbl_notfiy_msg".
 *
 * The followings are the available columns in table 'tbl_notfiy_msg':
 * @property integer $id
 * @property integer $author_id
 * @property integer $notify_type
 * @property integer $nofity_user_id
 * @property string $notify_user_name
 * @property integer $content_id
 * @property string $content_info
 * @property integer $status
 * @property string $created_date
 */
class NotifyMsg extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return NotifyMsg the static model class
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
		return 'tbl_notfiy_msg';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('author_id, notify_type, nofity_user_id, content_id, status', 'numerical', 'integerOnly'=>true),
			array('notify_user_name', 'length', 'max'=>80),
			array('content_info', 'length', 'max'=>100),
			array('created_date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, author_id, notify_type, nofity_user_id, notify_user_name, content_id, content_info, status, created_date', 'safe', 'on'=>'search'),
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
			'notify_type' => 'Notify Type',
			'nofity_user_id' => 'Nofity User',
			'notify_user_name' => 'Notify User Name',
			'content_id' => 'Content',
			'content_info' => 'Content Info',
			'status' => 'Status',
			'created_date' => 'Created Date',
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
		$criteria->compare('notify_type',$this->notify_type);
		$criteria->compare('nofity_user_id',$this->nofity_user_id);
		$criteria->compare('notify_user_name',$this->notify_user_name,true);
		$criteria->compare('content_id',$this->content_id);
		$criteria->compare('content_info',$this->content_info,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('created_date',$this->created_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}