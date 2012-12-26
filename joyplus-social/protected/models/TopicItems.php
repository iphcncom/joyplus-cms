<?php

/**
 * This is the model class for table "mac_vod_topic_items".
 *
 * The followings are the available columns in table 'mac_vod_topic_items':
 * @property integer $id
 * @property integer $topic_id
 * @property integer $author_id
 * @property integer $vod_id
 * @property string $vod_name
 * @property string $vod_pic
 * @property string $vod_pic_ipad
 * @property integer $flag
 * @property integer $disp_order 
 * @property string $create_date
 */
class TopicItems extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return TopicItems the static model class
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
		return 'mac_vod_topic_items';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('topic_id, vod_id', 'required'),
			array('topic_id, author_id, vod_id, flag, disp_order', 'numerical', 'integerOnly'=>true),
			array('vod_name', 'length', 'max'=>150),
			array('vod_pic, vod_pic_ipad', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, topic_id, author_id, vod_id, vod_name, vod_pic, vod_pic_ipad, flag, disp_order', 'safe', 'on'=>'search'),
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
			'topic_id' => 'Topic',
			'author_id' => 'Author',
			'vod_id' => 'Vod',
			'vod_name' => 'Vod Name',
			'vod_pic' => 'Vod Pic',
			'vod_pic_ipad' => 'Vod Pic Ipad',
			'flag' => 'Flag',
			'disp_order' => 'Disp Order',
		    'create_date' => 'create date',
		);
	}

	
	public function getItem($topic_id,$prod_id){
		return  $this->find(array(
			'condition'=>'topic_id=:topic_id and  vod_id=:vod_id',
			'params'=>array(
			    ':topic_id'=>$topic_id,
			    ':vod_id'=>$prod_id,
			 ),
		));
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
		$criteria->compare('topic_id',$this->topic_id);
		$criteria->compare('author_id',$this->author_id);
		$criteria->compare('vod_id',$this->vod_id);
		$criteria->compare('vod_name',$this->vod_name,true);
		$criteria->compare('vod_pic',$this->vod_pic,true);
		$criteria->compare('vod_pic_ipad',$this->vod_pic_ipad,true);
		$criteria->compare('flag',$this->flag);
		$criteria->compare('disp_order',$this->disp_order);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}