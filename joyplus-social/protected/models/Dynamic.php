<?php

/**
 * This is the model class for table "tbl_my_dynamic".
 *
 * The followings are the available columns in table 'tbl_my_dynamic':
 * @property integer $id
 * @property integer $author_id
 * @property integer $content_type
 * @property string $content_name
 * @property integer $content_id
 * @property string $create_date
 * @property integer $status
 * @property integer $order_position
 * @property integer $dynamic_type
 * @property string $content_pic_url
 * @property string $content_desc
 */
class Dynamic extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Dynamic the static model class
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
		return 'tbl_my_dynamic';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
		array('author_id, content_type, content_id, status, order_position, dynamic_type', 'numerical', 'integerOnly'=>true),
		array('content_name', 'length', 'max'=>50),
		array('content_pic_url, content_desc', 'length', 'max'=>300),
		array('create_date', 'safe'),
		// The following rule is used by search().
		// Please remove those attributes that should not be searched.
		array('id, author_id, content_type, content_name, content_id, create_date, status, order_position, dynamic_type, content_pic_url, content_desc', 'safe', 'on'=>'search'),
		);
	}

	public function searchUserWatchs($userid,$limit=20,$offset=0){
		return Yii::app()->db->createCommand()
		->select('content_id, content_name, content_pic_url,content_type,create_date')
		->from('tbl_my_dynamic ')
		->where('author_id=:author_id and status=:status and dynamic_type=:type', array(
			    ':author_id'=>$userid,
			    ':status'=>Constants::OBJECT_APPROVAL,
                ':type'=>Constants::DYNAMIC_TYPE_WATCH,
		))->order('create_date DESC')->limit($limit)->offset($offset)
		->queryAll();
	}
	
  public function searchUserFavorities($userid,$limit=20,$offset=0){
		return Yii::app()->db->createCommand()
		->select('content_id, content_name, content_pic_url,content_type,create_date')
		->from('tbl_my_dynamic ')
		->where('author_id=:author_id and status=:status and dynamic_type=:type', array(
			    ':author_id'=>$userid,
			    ':status'=>Constants::OBJECT_APPROVAL,
                ':type'=>Constants::DYNAMIC_TYPE_FAVORITY,
		))->order('create_date DESC')->limit($limit)->offset($offset)
		->queryAll();
	}
	
    public function getFavorityByProd($userid,$prod_id){
		return  $this->find(array(
			'condition'=>'author_id=:author_id and content_id=:content_id and dynamic_type=:type',
			'params'=>array(
			    ':author_id'=>$userid,
			    ':type'=>Constants::DYNAMIC_TYPE_FAVORITY,
			    ':content_id'=>$prod_id,
			 ),
		));
	}	

	public function friendRecommends($userid,$limit=20,$offset=0){
		
		$command= Yii::app()->db->createCommand()
		->select('dy.content_id, dy.content_name, dy.content_pic_url,dy.content_type,count(dy.content_id) as recommendRate')
		->from('tbl_my_dynamic dy')
		->join('tbl_my_friend fr', 'fr.friend_id=dy.author_id ')
		->where('fr.status=:status and dy.status=:dystatus and dy.author_id=fr.friend_id and fr.author_id=:author_id and dy.dynamic_type =:type', array(
			    ':status'=>Constants::OBJECT_APPROVAL,
			    ':dystatus'=>Constants::OBJECT_APPROVAL,
		        ':type' =>Constants::DYNAMIC_TYPE_LIKE,
		        ':author_id'=>$userid,
		));
		
		//$command ->where(array('in', 'dy.dynamic_type',array(1,2,3,4,5,6)));
     $command->group('dy.content_id, dy.content_name, dy.content_pic_url,dy.content_type');
		return $command->order('recommendRate DESC')->limit($limit)->offset($offset)
		->queryAll();
	}
	
    public function friendDynamicForProgram($userid,$prod_id,$limit=20,$offset=0){
//		$friendids=  Yii::app()->db->createCommand()->select("friend_id")->from("tbl_my_friend")
//		->where('author_id=:author_id and status=:status', array(
//			    ':author_id'=>$userid,
//			    ':status'=>Constants::OBJECT_APPROVAL,
//		))->queryAll();
//		
//		if(!(isset($friendids) && is_array($friendids))){
//		  return array();
//		}
		$command= Yii::app()->db->createCommand()
		->select('dy.content_desc,dy.dynamic_type,fr.friend_id,fr.friend_photo_url,fr.friend_username,dy.create_date')
		->from('tbl_my_dynamic dy')
		->join('tbl_my_friend fr', 'fr.friend_id=dy.author_id ')
		->where('dy.status=:status and dy.content_id=:content_id and fr.status=:frstatus and fr.author_id=:author_id and dy.dynamic_type in (1,2,3,4,5,6)', array(
			    ':status'=>Constants::OBJECT_APPROVAL,
		        ':frstatus'=>Constants::OBJECT_APPROVAL,
		        ':content_id'=>$prod_id,
		        ':author_id'=>$userid
		));
		
		return $command->order('dy.create_date DESC,dy.dynamic_type')->limit($limit)->offset($offset)
		->queryAll();
	}
	
public function friendAndMeDynamics($userid,$limit=20,$offset=0){
		$command= Yii::app()->db->createCommand()
		->select('dy.*')
		->from('tbl_my_dynamic dy')
		->join('tbl_my_friend fr', 'fr.friend_id=dy.author_id ')
		->where('fr.status=:status and dy.status=:dystatus and dy.author_id=fr.friend_id and fr.author_id=:author_id  or dy.author_id=:dyauthor_id', array(
			    ':status'=>Constants::OBJECT_APPROVAL,
			    ':dystatus'=>Constants::OBJECT_APPROVAL,
		        ':author_id'=>$userid,
		        ':dyauthor_id'=>$userid,
		));		
		
		return $command->order('dy.create_date DESC,dy.dynamic_type')->limit($limit)->offset($offset)
		->queryAll();
	}
	
   public function myDynamics($userid,$limit=20,$offset=0){
		$command= Yii::app()->db->createCommand()
		->select('dy.*')
		->from('tbl_my_dynamic dy')
		->where('dy.status=:dystatus and dy.author_id=:dyauthor_id', array(
			    ':dystatus'=>Constants::OBJECT_APPROVAL,
		        ':dyauthor_id'=>$userid,
		));				
		return $command->order('dy.create_date DESC,dy.dynamic_type')->limit($limit)->offset($offset)
		->queryAll();
	}
	
   public function friendDynamics($userid,$limit=20,$offset=0){
		$command= Yii::app()->db->createCommand()
		->select('dy.*')
		->from('tbl_my_dynamic dy')
		->join('tbl_my_friend fr', 'fr.friend_id=dy.author_id ')
		->where('fr.status=:status and dy.status=:dystatus and dy.author_id=fr.friend_id and fr.author_id=:author_id ', array(
			    ':status'=>Constants::OBJECT_APPROVAL,
			    ':dystatus'=>Constants::OBJECT_APPROVAL,
		        ':author_id'=>$userid,
		));		
		
		return $command->order('dy.create_date DESC,dy.dynamic_type')->limit($limit)->offset($offset)
		->queryAll();
	}
	
    public function unFavority($prod_id){
      Yii::app()->db->createCommand()->update($this->tableName(),array('status'=>':status'), 'author_id=:author_id and content_id=:id and dynamic_type=:type', 
      array(
        ':status'=>Constants::OBJECT_DELETE,
        ':author_id'=>Yii::app()->user->id,
        ':id'=>$prod_id,
        ':type'=>Constants::DYNAMIC_TYPE_FAVORITY,
      )
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
			'content_type' => 'Content Type',
			'content_name' => 'Content Name',
			'content_id' => 'Content',
			'create_date' => 'Create Date',
			'status' => 'Status',
			'order_position' => 'Order Position',
			'dynamic_type' => 'Dynamic Type',
			'content_pic_url' => 'Content Pic Url',
			'content_desc' => 'Content Desc',
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
		$criteria->compare('content_type',$this->content_type);
		$criteria->compare('content_name',$this->content_name,true);
		$criteria->compare('content_id',$this->content_id);
		$criteria->compare('create_date',$this->create_date,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('order_position',$this->order_position);
		$criteria->compare('dynamic_type',$this->dynamic_type);
		$criteria->compare('content_pic_url',$this->content_pic_url,true);
		$criteria->compare('content_desc',$this->content_desc,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}