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
	
	public function getHisotryByShowProd($userid, $prod_id,$prod_subname){
	   return  $this->find(array(
			'condition'=>'author_id=:author_id and prod_id=:content_id and prod_subname=:prod_subname',
			'params'=>array(
			    ':author_id'=>$userid,
			    ':content_id'=>$prod_id,
			    ':prod_subname'=>$prod_subname,
			 ),
		));
	}
   //删除用户的播放记录 
   public function delUserHisotry($userid){		
	      Yii::app()->db->createCommand('update tbl_play_history set status='.Constants::OBJECT_DELETE.' where author_id='.$userid)->execute();
	}
	
   //删除用户的播放记录 
   public function delUserHisotryVodType($userid,$vodType){
	      Yii::app()->db->createCommand('update tbl_play_history set status='.Constants::OBJECT_DELETE.' where author_id='.$userid.' and prod_type='.$vodType)->execute();
	}
	
    public function delUserSingleHisotry($userid,$history_id){	 	      
	      Yii::app()->db->createCommand('update tbl_play_history set status='.Constants::OBJECT_DELETE.' where author_id='.$userid.' and id='.$history_id)->execute();
	}
	
   public function getUserHistory($userid,$limit=20,$offset=0){
		return Yii::app()->db->createCommand()
		->select('a.id,a.prod_type, a.prod_name, a.prod_subname, a.prod_id, a.create_date, a.play_type, a.playback_time, a.video_url, a.duration,	a.create_date ,vod.d_pic as prod_pic_url, substring_index( vod.d_pic_ipad, \'{Array}\', 1 )  as big_prod_pic_url  , vod.d_level as definition,vod.d_content as prod_summary, vod.d_starring as stars,vod.d_directed as directors ,vod.favority_user_count as favority_num ,vod.good_number as support_num ,vod.d_year as publish_date,vod.d_score as score,vod.d_area as area, vod.d_remarks as max_episode, vod.d_state as cur_episode  ')
		->from('tbl_play_history as a ')
		->join('mac_vod vod', "a.prod_id=vod.d_id")
		->where('a.author_id=:author_id and a.status=:status', array(
			    ':author_id'=>$userid,
		        ':status'=>Constants::OBJECT_APPROVAL,
		))->order('a.create_date DESC')->limit($limit)->offset($offset)
		->queryAll();
	}
	
	
	
   public function getUserHistoryVodType($userid,$limit=20,$offset=0,$vodType){
		return Yii::app()->db->createCommand()
		->select('a.id,a.prod_type, a.prod_name, a.prod_subname, a.prod_id, a.create_date, a.play_type, a.playback_time, a.video_url, a.duration,	a.create_date ,vod.d_pic as content_pic_url, substring_index( vod.d_pic_ipad, \'{Array}\', 1 )  as big_content_pic_url  , vod.d_level as definition,vod.d_content as prod_summary,  vod.d_pic_ipad as prod_pic_url,vod.d_starring as stars,vod.d_directed as directors ,vod.favority_user_count as favority_num ,vod.good_number as support_num ,vod.d_year as publish_date,vod.d_score as score,vod.d_area as area, vod.d_remarks as max_episode, vod.d_state as cur_episode  ')
		->from('tbl_play_history as a ')
		->join('mac_vod vod', "a.prod_id=vod.d_id")
		->where('a.author_id=:author_id and a.status=:status and a.prod_type='.$vodType, array(
			    ':author_id'=>$userid,
		        ':status'=>Constants::OBJECT_APPROVAL,
		))->order('a.create_date DESC')->limit($limit)->offset($offset)
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