<?php

/**
 * This is the model class for table "tbl_play_history".
 *
 * The followings are the available columns in table 'tbl_play_history':
 * @property integer $id
 * @property integer $author_id
 * @property integer $prod_type
 * @property string $prod_name
 * @property string $prod_subname
 * @property integer $prod_id
 * @property string $create_date
 * @property integer $status
 * @property integer $play_type
 * @property integer $playback_time
 * @property string $video_url
 * @property integer $duration
 */
class PlayHistory extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return PlayHistory the static model class
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
		return 'tbl_play_history';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('author_id, prod_type, prod_id, status, play_type, playback_time, duration', 'numerical', 'integerOnly'=>true),
			array('prod_name', 'length', 'max'=>100),
			array('prod_subname', 'length', 'max'=>200),
			array('video_url', 'length', 'max'=>500),
			array('create_date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, author_id, prod_type, prod_name, prod_subname, prod_id, create_date, status, play_type, playback_time, video_url, duration', 'safe', 'on'=>'search'),
		);
	}
	
    public function getHisotryByProd($userid,$prod_id){
		return  $this->find(array(
			'condition'=>'author_id=:author_id and prod_id=:content_id',
			'params'=>array(
			    ':author_id'=>$userid,
			    ':content_id'=>$prod_id,
			 ),
		));
	}
	
	
   public function getUserHistory($userid,$limit=20,$offset=0){
		return Yii::app()->db->createCommand()
		->select('prod_type, prod_name, prod_subname, prod_id, create_date, play_type, playback_time, video_url, duration')
		->from('tbl_play_history')
		->where('author_id=:author_id', array(
			    ':author_id'=>$userid,
		))->order('create_date DESC')->limit($limit)->offset($offset)
		->queryAll();
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
			'prod_id' => 'Prod',
			'create_date' => 'Create Date',
			'status' => 'Status',
			'play_type' => 'Play Type',
			'playback_time' => 'Playback Time',
			'video_url' => 'Video Url',
			'duration' => 'Duration',
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
		$criteria->compare('prod_id',$this->prod_id);
		$criteria->compare('create_date',$this->create_date,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('play_type',$this->play_type);
		$criteria->compare('playback_time',$this->playback_time);
		$criteria->compare('video_url',$this->video_url,true);
		$criteria->compare('duration',$this->duration);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}