<?php

/**
 * This is the model class for table "tbl_lookup".
 *
 * The followings are the available columns in table 'tbl_lookup':
 * @property integer $id
 * @property string $content
 * @property integer $search_count
 * @property string $last_search_date
 * @property integer $keyword_order
 */
class Lookup extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Lookup the static model class
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
		return 'tbl_lookup';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('search_count, keyword_order', 'numerical', 'integerOnly'=>true),
			array('content', 'length', 'max'=>50),
			array('last_search_date', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, content, search_count, last_search_date, keyword_order', 'safe', 'on'=>'search'),
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
			'content' => 'Content',
			'search_count' => 'Search Count',
			'last_search_date' => 'Last Search Date',
			'keyword_order' => 'Keyword Order',
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
		$criteria->compare('content',$this->content,true);
		$criteria->compare('search_count',$this->search_count);
		$criteria->compare('last_search_date',$this->last_search_date,true);
		$criteria->compare('keyword_order',$this->keyword_order);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}