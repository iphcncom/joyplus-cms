<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
		// this is used in contact page
		'adminEmail'=>'webmaster@ijoyplus.com',
	    'app_key'=>'ijoyplus_android_0001,ijoyplus_ios_001',
        'CACHE_PARAM_EXPIRED_DEFAULT'=>'3600',
        'CACHE_PARAM_EXPIRED_USER'=>'3600',
        'CACHE_PARAM_EXPIRED_PROGRAM'=>'3600',
        'CACHE_PARAM_EXPIRED_COMMENT'=>'3600',
        'CACHE_PARAM_EXPIRED_POPULAR_PROGRAM'=>'3600',
        'CACHE_PARAM_EXPIRED_PRESTIGE'=>'836000',
        'CACHE_ENABLED'=>'0',
	    'errors'=>array(    
		// this is used in contact page
		'10021'=>'HTTP method is not suported for this request',
	    '10006' =>'Source paramter (appkey) is missing or invalid',
	    '10001'=>'System error',
		'00000'=>'Success',
		'20001'=>'Account not Found',
	    '20002'=>'Password is invalid',
	    '20003'=>'Username is invalid,It must be your email.',
	    '20004'=>'Username can\'t be null',
	    '20005'=>'Password can\'t be null',
	    '20006'=>'Username exists.',
	    '20007'=>'Email exists.',
	    '20008'=>'Session is expired, need relogin.',	
	    '20009'=>'Third part account type is invalid.',		
	    '20010'=>'Object not found',
	    '20011'=>'Param is invalid',
	    '20012'=>'Result is null.',
	    '20013'=>'Program is published.',
	    '20014'=>'Url is invalid.',
	    '20015'=>'Program is favority.',
	    '20016'=>'Program is not favority.',
	    '20017'=>'Person is liked by you.',
	    '20018'=>'Nickname can not be null',
        '20019'=>'Nickname is exsting.',
        '20020'=>'Keyword can\'t be null.',
        '20021'=>'Same Requests.',
        '20022'=>'Object is created.',
        '20023'=>'No privilege to do.',
        '20024'=>'User id is null or Related user is not exist',
	),
);