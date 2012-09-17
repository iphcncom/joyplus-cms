<?php

 class IjoyPlusUserIdentity extends CUserIdentity
{
    private $_id;
    public function authenticate(){
        $record=User::model()->find('(LOWER(username)=? or LOWER(email)=?) and status=?',array(strtolower($this->username),strtolower($this->username),Constants::USER_APPROVAL));
        if($record===null){
            $this->errorCode=Constants::USER_NOT_EXIST;
            return false;
        }else if(!$record->validatePassword($this->password)){
            $this->errorCode=Constants::ERROR_PASSWORD_INVALID;
            return false;
        } else {
            $this->_id=$record->id;
            $this->setState('username', $record->username);
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