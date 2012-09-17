<?php

/**
 * This is the model class for table "tbl_comments".
 *
 * The followings are the available columns in table 'tbl_comments':
 * @property integer $id
 * @property integer $author_id
 * @property integer $content_type
 * @property string $content_name
 * @property integer $content_id
 * @property string $create_date
 * @property integer $status
 * @property integer $like_number
 * @property string $content_pic_url
 * @property string $comments
 * @property integer $thread_id
 * @property integer $thread_author_id
 * @property string $author_photo_url
 * @property string $author_username
 * @property integer $comments_leaf
 *
 * The followings are the available model relations:
 * @property TblUser $author
 * @property Comment $thread
 * @property Comment[] $tblComments
 * @property TblMyDynamic[] $tblMyDynamics
 */
class Comment extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Comment the static model class
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
		return 'tbl_comments';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id', 'required'),
			array('id, author_id, content_type, content_id, status, like_number, thread_id, thread_author_id, comments_leaf', 'numerical', 'integerOnly'=>true),
			array('content_name, author_username', 'length', 'max'=>50),
			array('content_pic_url, comments, author_photo_url', 'length', 'max'=>300),
			array('create_date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, author_id, content_type, content_name, content_id, create_date, status, like_number, content_pic_url, comments, thread_id, thread_author_id, author_photo_url, author_username, comments_leaf', 'safe', 'on'=>'search'),
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
			'author' => array(self::BELONGS_TO, 'TblUser', 'author_id'),
			'thread' => array(self::BELONGS_TO, 'Comment', 'thread_id'),
			'tblComments' => array(self::HAS_MANY, 'Comment', 'thread_id'),
			'tblMyDynamics' => array(self::HAS_MANY, 'TblMyDynamic', 'content_id'),
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
			'like_number' => 'Like Number',
			'content_pic_url' => 'Content Pic Url',
			'comments' => 'Comments',
			'thread_id' => 'Thread',
			'thread_author_id' => 'Thread Author',
			'author_photo_url' => 'Author Photo Url',
			'author_username' => 'Author Username',
			'comments_leaf' => 'Comments Leaf',
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
		$criteria->compare('like_number',$this->like_number);
		$criteria->compare('content_pic_url',$this->content_pic_url,true);
		$criteria->compare('comments',$this->comments,true);
		$criteria->compare('thread_id',$this->thread_id);
		$criteria->compare('thread_author_id',$this->thread_author_id);
		$criteria->compare('author_photo_url',$this->author_photo_url,true);
		$criteria->compare('author_username',$this->author_username,true);
		$criteria->compare('comments_leaf',$this->comments_leaf);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}