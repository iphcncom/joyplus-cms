<?php

 class IjoyPlusUserIdentity extends CUserIdentity
{
    private $_id;
    public function authenticate(){
        $record=User::model()->find('(LOWER(username)=?) and status=?',array(strtolower($this->username),Constants::USER_APPROVAL));
        if($record===null){
            $this->errorCode=Constants::USER_NOT_EXIST;
            return false;
        }else if(!$record->validatePassword($this->password)){
            $this->errorCode=Constants::ERROR_PASSWORD_INVALID;
            return false;
        } else {
            $this->_id=$record->id;
            $this->setState('pic_url', $record->user_photo_url);
            $this->setState('nickname', $record->nickname);
            $this->setState('lastLoginDate', $record->last_login_date);
            $this->errorCode=Constants::SUCC;
            return true;
        }        
    }
    public function  setId($id){
    	$this->_id=$id;
    }
    public function getId()
    {
        return $this->_id;
    }
}

?> 