<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Web Application',
    'defaultController'=>'user',
	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
        'application.vo.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'12345',  //Enter Your Password Here
		 	// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
		
	),

	// application components
	'components'=>array(
		'user'=>array(
	        'class'=>'IjoyplusWebUser',
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		
		
		
//		'cache'=>array(
//            'class'=>'CMemCache',
//            'servers'=>array(
//                array('host'=>'localhost', 'port'=>11211, 'weight'=>100)
//            ),
//        ),
		
		// uncomment the following to enable URLs in path-format
		
		'urlManager'=>array(
			'urlFormat'=>'path',
//			'rules'=>array(
//				'login'=>'login/login',
//				'logout'=>'login/logout',
//				'register'=>'login/register',
//		        'profile'=>'login/userInfo',
//			),
		),
		
//		'db'=>array(
//			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
//		),
		// uncomment the following to use a MySQL database
		
		'db'=>array(
			'connectionString' => 'mysql:host=115.239.196.123;dbname=ijoyplus',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => 'ilovetv001',
			'charset' => 'utf8',
		    'enableParamLogging'=>true,
		
		),
			
//		'db'=>array(
//			'connectionString' => 'mysql:host='.SAE_MYSQL_HOST_M.';port='.SAE_MYSQL_PORT.';dbname='.SAE_MYSQL_DB,
//			'emulatePrepare' => true,
//			'username' => SAE_MYSQL_USER,
//			'password' => SAE_MYSQL_PASS,
//			'charset' => 'utf8',
//		),
		 'authManager'=>array(
            'class'=>'CDbAuthManager',
            'connectionID'=>'db',
		    'defaultRoles'=>array('authenticated', 'guest'),
        ),
//		'errorHandler'=>array(
//			// use 'site/error' action to display errors
//            'errorAction'=>'site/error',
//        ),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
//					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				
				//array(
				//	'class'=>'CWebLogRoute',
				//),
				
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@ijoyplus.com',
	    'app_key'=>'ijoyplus_android_0001,ijoyplus_ios_001',
	    'errors'=>array(    
		// this is used in contact page
		'10021'=>'HTTP method is not suported for this request',
	    '10006' =>'Source paramter (appkey) is missing or invalid',
	    '10001'=>'System error',
		'00000'=>'Success',
		'20001'=>'Account not Found',
	    '20002'=>'Password is invalid',
	    '20003'=>'Email is invalid',
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
	),
	),
	
);