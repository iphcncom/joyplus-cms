<?php
  class IjoyplusWebUser extends CWebUser{
  	
    protected function afterLogin($fromCookie)
	{   
		parent::afterLogin($fromCookie);
		User::model()->updateLastLoginDate(Yii::app()->user->id);
	}
  }
?>