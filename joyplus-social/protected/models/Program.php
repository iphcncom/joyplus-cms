<?php

/**
 * This is the model class for table "mac_vod".
 *
 * The followings are the available columns in table 'mac_vod':
 * @property integer $d_id
 * @property string $d_name
 * @property string $d_subname
 * @property string $d_enname
 * @property integer $d_type
 * @property string $d_letter
 * @property integer $d_state
 * @property string $d_color
 * @property string $d_pic
 * @property string $d_starring
 * @property string $d_directed
 * @property string $d_area
 * @property string $d_year
 * @property string $d_language
 * @property integer $d_level
 * @property integer $d_stint
 * @property integer $d_hits
 * @property integer $d_dayhits
 * @property integer $d_weekhits
 * @property integer $d_monthhits
 * @property integer $d_topic
 * @property string $d_content
 * @property string $d_remarks
 * @property integer $d_hide
 * @property integer $d_good
 * @property integer $d_bad
 * @property integer $d_usergroup
 * @property integer $d_score
 * @property integer $d_scorecount
 * @property string $d_addtime
 * @property string $d_time
 * @property string $d_hitstime
 * @property string $d_playfrom
 * @property string $d_playserver
 * @property string $d_playurl
 * @property string $d_downurl
 * @property string $webUrls
 * @property integer $publish_owner_id
 * @property integer $love_user_count
 * @property integer $watch_user_count
 * @property integer $favority_user_count
 * @property string $d_type_name
 * @property string $d_pic_ipad
 * @property string $share_number
 * @property string $good_number 
 * @property string $total_comment_number 
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
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'mac_vod';
	}
	
	 function incLoveUserCount($id){		
		  try{
//			  Yii::app()->db->createCommand()->update($this->tableName(),array('love_user_count'=>':love_user_count'), 'd_id=:d_id', 
//		      array(
//		        ':love_user_count'=>'love_user_count+1',
//		        ':d_id'=>$id,
//		      )
//		      );
		      Yii::app()->db->createCommand("update ".$this->tableName() .' set love_user_count=love_user_count+1' .' where d_id='.$id)->execute();
		      return Constants::SUCC;
		  }catch (Exception $e){
		    return Constants::SYSTEM_ERROR;
		  }
		}
		
	   function incWatchUserCount($id){	
	     try{
//			  Yii::app()->db->createCommand()->update($this->tableName(),array('watch_user_count'=>':watch_user_count'), 'd_id=:d_id', 
//		      array(
//		        ':watch_user_count'=>'watch_user_count+1',
//		        ':d_id'=>$id,
//		      )
//		      );
		      Yii::app()->db->createCommand("update ".$this->tableName() .' set watch_user_count=watch_user_count+1' .' where d_id='.$id)->execute();
		      return Constants::SUCC;
		  }catch (Exception $e){
		    return Constants::SYSTEM_ERROR;
		  }
		}
		
	   function incFavorityUserCount($id){		
	     try{
//			  Yii::app()->db->createCommand()->update($this->tableName(),array('favority_user_count'=>':favority_user_count'), 'd_id=:d_id', 
//		      array(
//		        ':favority_user_count'=>'favority_user_count+1',
//		        ':d_id'=>$id,
//		      )
//		      );
		       Yii::app()->db->createCommand("update ".$this->tableName() .' set favority_user_count=favority_user_count+1' .' where d_id='.$id)->execute();
		      return Constants::SUCC;
		  }catch (Exception $e){
		    return Constants::SYSTEM_ERROR;
		  }	
		}
		
       function incGoodCount($id){		
	     try{
		       Yii::app()->db->createCommand("update ".$this->tableName() .' set good_number=good_number+1' .' where d_id='.$id)->execute();
		      return Constants::SUCC;
		  }catch (Exception $e){
		    return Constants::SYSTEM_ERROR;
		  }	
		}
     function incCommentCount($id){		
	     try{
		       Yii::app()->db->createCommand("update ".$this->tableName() .' set total_comment_number=total_comment_number+1' .' where d_id='.$id)->execute();
		      return Constants::SUCC;
		  }catch (Exception $e){
		    return Constants::SYSTEM_ERROR;
		  }	
		}
		
		
        function incShareCount($id){		
	     try{
		       Yii::app()->db->createCommand("update ".$this->tableName() .' set share_number=share_number+1' .' where d_id='.$id)->execute();
		      return Constants::SUCC;
		  }catch (Exception $e){
		    return Constants::SYSTEM_ERROR;
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
		 * @return array validation rules for model attributes.
		 */
		public function rules()
		{
			// NOTE: you should only define rules for those attributes that
			// will receive user inputs.
			return array(
				array('webUrls, publish_owner_id, love_user_count, watch_user_count, favority_user_count', 'required'),
				array('d_type, d_state, d_level, d_stint, d_hits, d_dayhits, d_weekhits, d_monthhits, d_topic, d_hide, d_good, d_bad, d_usergroup, d_score, d_scorecount, publish_owner_id, love_user_count, watch_user_count, favority_user_count', 'numerical', 'integerOnly'=>true),
				array('d_name, d_subname, d_enname, d_pic, d_starring, d_directed, d_remarks, d_playfrom, d_playserver', 'length', 'max'=>255),
				array('d_letter', 'length', 'max'=>1),
				array('d_color', 'length', 'max'=>8),
				array('d_area, d_year, d_language', 'length', 'max'=>32),
				array('d_content, d_addtime, d_time, d_hitstime, d_playurl, d_downurl', 'safe'),
				// The following rule is used by search().
				// Please remove those attributes that should not be searched.
				array('d_id, d_name, d_subname, d_enname, d_type, d_letter, d_state, d_color, d_pic, d_starring, d_directed, d_area, d_year, d_language, d_level, d_stint, d_hits, d_dayhits, d_weekhits, d_monthhits, d_topic, d_content, d_remarks, d_hide, d_good, d_bad, d_usergroup, d_score, d_scorecount, d_addtime, d_time, d_hitstime, d_playfrom, d_playserver, d_playurl, d_downurl, webUrls, publish_owner_id, love_user_count, watch_user_count, favority_user_count', 'safe', 'on'=>'search'),
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
			'd_id' => 'D',
			'd_name' => 'D Name',
			'd_subname' => 'D Subname',
			'd_enname' => 'D Enname',
			'd_type' => 'D Type',
			'd_letter' => 'D Letter',
			'd_state' => 'D State',
			'd_color' => 'D Color',
			'd_pic' => 'D Pic',
			'd_starring' => 'D Starring',
			'd_directed' => 'D Directed',
			'd_area' => 'D Area',
			'd_year' => 'D Year',
			'd_language' => 'D Language',
			'd_level' => 'D Level',
			'd_stint' => 'D Stint',
			'd_hits' => 'D Hits',
			'd_dayhits' => 'D Dayhits',
			'd_weekhits' => 'D Weekhits',
			'd_monthhits' => 'D Monthhits',
			'd_topic' => 'D Topic',
			'd_content' => 'D Content',
			'd_remarks' => 'D Remarks',
			'd_hide' => 'D Hide',
			'd_good' => 'D Good',
			'd_bad' => 'D Bad',
			'd_usergroup' => 'D Usergroup',
			'd_score' => 'D Score',
			'd_scorecount' => 'D Scorecount',
			'd_addtime' => 'D Addtime',
			'd_time' => 'D Time',
			'd_hitstime' => 'D Hitstime',
			'd_playfrom' => 'D Playfrom',
			'd_playserver' => 'D Playserver',
			'd_playurl' => 'D Playurl',
			'd_downurl' => 'D Downurl',
			'webUrls' => 'Web Urls',
			'publish_owner_id' => 'Publish Owner',
			'love_user_count' => 'Love User Count',
			'watch_user_count' => 'Watch User Count',
			'favority_user_count' => 'Favority User Count',
		    '$d_type_name'=>'Type Name',
		    '$d_pic_ipad'=>'Poster For Ipad',
		    
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

		$criteria->compare('d_id',$this->d_id);
		$criteria->compare('d_name',$this->d_name,true);
		$criteria->compare('d_subname',$this->d_subname,true);
		$criteria->compare('d_enname',$this->d_enname,true);
		$criteria->compare('d_type',$this->d_type);
		$criteria->compare('d_letter',$this->d_letter,true);
		$criteria->compare('d_state',$this->d_state);
		$criteria->compare('d_color',$this->d_color,true);
		$criteria->compare('d_pic',$this->d_pic,true);
		$criteria->compare('d_starring',$this->d_starring,true);
		$criteria->compare('d_directed',$this->d_directed,true);
		$criteria->compare('d_area',$this->d_area,true);
		$criteria->compare('d_year',$this->d_year,true);
		$criteria->compare('d_language',$this->d_language,true);
		$criteria->compare('d_level',$this->d_level);
		$criteria->compare('d_stint',$this->d_stint);
		$criteria->compare('d_hits',$this->d_hits);
		$criteria->compare('d_dayhits',$this->d_dayhits);
		$criteria->compare('d_weekhits',$this->d_weekhits);
		$criteria->compare('d_monthhits',$this->d_monthhits);
		$criteria->compare('d_topic',$this->d_topic);
		$criteria->compare('d_content',$this->d_content,true);
		$criteria->compare('d_remarks',$this->d_remarks,true);
		$criteria->compare('d_hide',$this->d_hide);
		$criteria->compare('d_good',$this->d_good);
		$criteria->compare('d_bad',$this->d_bad);
		$criteria->compare('d_usergroup',$this->d_usergroup);
		$criteria->compare('d_score',$this->d_score);
		$criteria->compare('d_scorecount',$this->d_scorecount);
		$criteria->compare('d_addtime',$this->d_addtime,true);
		$criteria->compare('d_time',$this->d_time,true);
		$criteria->compare('d_hitstime',$this->d_hitstime,true);
		$criteria->compare('d_playfrom',$this->d_playfrom,true);
		$criteria->compare('d_playserver',$this->d_playserver,true);
		$criteria->compare('d_playurl',$this->d_playurl,true);
		$criteria->compare('d_downurl',$this->d_downurl,true);
		$criteria->compare('webUrls',$this->webUrls,true);
		$criteria->compare('publish_owner_id',$this->publish_owner_id);
		$criteria->compare('love_user_count',$this->love_user_count);
		$criteria->compare('watch_user_count',$this->watch_user_count);
		$criteria->compare('favority_user_count',$this->favority_user_count);
		$criteria->compare('d_type_name',$this->d_type_name);	
		$criteria->compare('d_pic_ipad',$this->d_pic_ipad);		
		$criteria->compare('total_comment_number',$this->total_comment_number);		

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}