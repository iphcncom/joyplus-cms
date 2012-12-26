<?php

/**
 * This is the model class for table "mac_vod_topic".
 *
 * The followings are the available columns in table 'mac_vod_topic':
 * @property integer $t_id
 * @property string $t_name
 * @property string $t_enname
 * @property integer $t_sort
 * @property string $t_template
 * @property string $t_pic
 * @property string $t_des
 * @property integer $t_type
 * @property integer $t_flag
 * @property integer $t_userid
 * @property string $create_date
 */
class Topic extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Topic the static model class
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
		return 'mac_vod_topic';
	}
	const relatedTops_prod='select topic.t_id,topic.t_name from mac_vod_topic topic,mac_vod_topic_items item where topic.t_id=item.topic_id and topic.t_flag=1 and  topic.t_bdtype=1 and item.vod_id=';
    public function getRelatedTops($prod_id){    	
      return Yii::app()->db->createCommand(Topic::relatedTops_prod.$prod_id)->queryAll();
    }
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('t_sort, t_type, t_flag, t_userid', 'numerical', 'integerOnly'=>true),
			array('t_name', 'length', 'max'=>64),
			array('t_enname, t_template', 'length', 'max'=>128),
			array('t_pic, t_des', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('t_id, t_name, t_enname, t_sort, t_template, t_pic, t_des, t_type, t_flag, t_userid', 'safe', 'on'=>'search'),
		);
	}
    
	
	public function getTopic($userid,$topicName){
		return  $this->find(array(
			'condition'=>'t_name=:t_name and t_userid=:userid',
			'params'=>array(
			    ':t_name'=>$topicName,
		        ':userid'=>$userid,
			 ),
		));
	}	
	
	
	
	public function deleteTopic($topicId){
		$transaction = Yii::app()->db->beginTransaction(); 
		try{
			Yii::app()->db->createCommand()->delete("mac_vod_topic", 't_id=:id', 
		      array(
		        ':id'=>$topicId,
		      )
	        );
	        
			Yii::app()->db->createCommand()->delete("mac_vod_topic_items", 'topic_id=:id', 
		      array(
		        ':id'=>$topicId,
		      )
	        );
	        $transaction->commit();
	        return true;
		}catch (Exception $e) {
		   Yii::log( CJSON::encode($e), "error");
           $transaction->rollback();
           return false;
        }
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
			't_template' => 'T Template',
			't_pic' => 'T Pic',
			't_des' => 'T Des',
			't_type' => 'T Type',
			't_flag' => 'T Flag',
			't_userid' => 'T Userid',
		    'create_date' => 'create date',
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
		$criteria->compare('t_template',$this->t_template,true);
		$criteria->compare('t_pic',$this->t_pic,true);
		$criteria->compare('t_des',$this->t_des,true);
		$criteria->compare('t_type',$this->t_type);
		$criteria->compare('t_flag',$this->t_flag);
		$criteria->compare('t_userid',$this->t_userid);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}