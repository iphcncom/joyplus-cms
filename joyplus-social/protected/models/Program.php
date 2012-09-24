<?php

/**
 * This is the model class for table "program".
 *
 * The followings are the available columns in table 'program':
 * @property string $id
 * @property string $name
 * @property string $summary
 * @property string $poster
 * @property string $parent_id
 * @property string $labels
 * @property string $url
 * @property string $sources
 * @property string $last_update_time
 * @property integer $enable
 * @property integer $publish_owner_id
 * @property integer $love_user_count
 * @property integer $watch_user_count
 * @property integer $favority_user_count
 * @property integer $pro_type
 */
class Program extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Program the static model class
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
		return 'program';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('poster, url', 'required'),
			array('enable, publish_owner_id, love_user_count, watch_user_count, favority_user_count, pro_type', 'numerical', 'integerOnly'=>true),
			array('name, summary, labels, sources', 'length', 'max'=>255),
			array('parent_id', 'length', 'max'=>20),
			array('last_update_time', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, summary, poster, parent_id, labels, url, sources, last_update_time, enable, publish_owner_id, love_user_count, watch_user_count, favority_user_count, pro_type', 'safe', 'on'=>'search'),
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
	
	public function getVedios($prod_id){	      
      return Video::model()->findAll(array(
			'condition'=>'program_id=:program_id and enable=:enable',
			'order'=>'name,source_id DESC',
			'params'=>array(
				 ':program_id'=>$prod_id,
			    ':enable'=>Constants::OBJECT_APPROVAL,
			),
		));
	   
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'summary' => 'Summary',
			'poster' => 'Poster',
			'parent_id' => 'Parent',
			'labels' => 'Labels',
			'url' => 'Url',
			'sources' => 'Sources',
			'last_update_time' => 'Last Update Time',
			'enable' => 'Enable',
			'publish_owner_id' => 'Publish Owner',
			'love_user_count' => 'Love User Count',
			'watch_user_count' => 'Watch User Count',
			'favority_user_count' => 'Favority User Count',
			'pro_type' => 'Pro Type',
		);
	}
 function incLoveUserCount($id){		
	  $model=$this->findByPk($id);
	  if($model !==null){	 
	  	$model->love_user_count=$model->love_user_count+1;	
	     if($model->save()){
	       return Constants::SUCC;
	     }else{
	       return Constants::SYSTEM_ERROR;
	     }	     
	   }else {
	      Constants::OBJECT_NOT_FOUND;
	   }
	  
	}
	
   function incWatchUserCount($id){		
	  $model=$this->findByPk($id);
	  if($model !==null){	 
	  	$model->watch_user_count=$model->watch_user_count+1;	
	     if($model->save()){
	       return Constants::SUCC;
	     }else{
	       return Constants::SYSTEM_ERROR;
	     }
	     
	   }else {
	      Constants::OBJECT_NOT_FOUND;
	   }
	}
	
   function incFavorityUserCount($id){		
	  $model=$this->findByPk($id);
	  if($model !==null){	 
	  	$model->favority_user_count=$model->favority_user_count+1;	
	     if($model->save()){
	       return Constants::SUCC;
	     }else{
	       return Constants::SYSTEM_ERROR;
	     }
	     
	   }else {
	      Constants::OBJECT_NOT_FOUND;
	   }
	}
	
    function publish($author_id,$id){		
	  $model=$this->findByPk($id);
	  //var_dump($model->publish_owner_id);
	  if($model !==null){
	  	if(isset($model->publish_owner_id) && !is_null($model->publish_owner_id) && strlen($model->publish_owner_id)>0){	 
		  	  return  Constants::PROGRAM_IS_PUBLISHED;
		 }else {
		  	  $model->publish_owner_id=$author_id;	
			  if($model->save()){
			    return Constants::SUCC;
			  }else{
			    return Constants::SYSTEM_ERROR;
			  }
		}
	   }else {
	     return  Constants::OBJECT_NOT_FOUND;
	   }
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('summary',$this->summary,true);
		$criteria->compare('poster',$this->poster,true);
		$criteria->compare('parent_id',$this->parent_id,true);
		$criteria->compare('labels',$this->labels,true);
		$criteria->compare('url',$this->url,true);
		$criteria->compare('sources',$this->sources,true);
		$criteria->compare('last_update_time',$this->last_update_time,true);
		$criteria->compare('enable',$this->enable);
		$criteria->compare('publish_owner_id',$this->publish_owner_id);
		$criteria->compare('love_user_count',$this->love_user_count);
		$criteria->compare('watch_user_count',$this->watch_user_count);
		$criteria->compare('favority_user_count',$this->favority_user_count);
		$criteria->compare('pro_type',$this->pro_type);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}