<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
		// this is used in contact page
		'adminEmail'=>'webmaster@ijoyplus.com',
	    'app_key'=>'a06f18fb09aa30337082e9d43eb72016,e25387ffd1ffa14e2680491ef491edfa,87974e2b5501e6247ec64e49ce2d6217', // product
//		'app_key'=>'72dbdcec3b85d5d21c6777c696bc6aa3,24d8190c4578eeb613b2d8a2a2bee66d', // test
        'CACHE_PARAM_EXPIRED_DEFAULT'=>'3600',
        'CACHE_PARAM_EXPIRED_USER'=>'3600',
        'CACHE_PARAM_EXPIRED_PROGRAM'=>'3600',
        'CACHE_PARAM_EXPIRED_COMMENT'=>'3600',
        'CACHE_PARAM_EXPIRED_POPULAR_PROGRAM'=>'3600',
        'CACHE_PARAM_EXPIRED_PRESTIGE'=>'836000',
        'CAN_PLAY_FROM_PLATFORM'=>',letv,youku,56,qq,',
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
		'devices'=>array(    
			// this is used in contact page iPad,iphone,web,apad,TV,aphone
			'ipad'=>'iPad',
		    'iphone' =>'iphone',
		    'web'=>'web',
			'android-pad'=>'apad',
			'tv'=>'TV',
		    'android-mobile'=>'aphone',	    
		),
		'area_group'=>array(    
			// this is used in contact page iPad,iphone,web,apad,TV,aphone
			'1'=>array(
		         "内地"=>"'内地'",
		         "香港"=>"'香港'",
		         "台湾"=>"'台湾'",
		         "美国"=>"'美国'",
		         "日本"=>"'日本'",
		         "韩国"=>"'韩国'",
		         "欧洲"=>"'立陶宛','法国','英国','爱尔兰','德国','奥地利','波黑','希腊','爱沙尼亚','丹麦','罗马尼亚','匈牙利','俄罗斯','捷克斯洛伐克','捷克','斯洛伐克','苏联','法国','瑞士','瑞典','克罗地亚','塞浦路斯','冰岛','芬兰','波兰','荷兰','白俄罗斯','塞尔维亚','南斯拉夫','摩纳哥','挪威','列支敦士登','斯洛文尼亚','拉脱维亚','保加利亚','比利时','卢森堡','西班牙','葡萄牙','阿尔巴尼亚','意大利'",
		         "东南亚"=>"'不丹','印度','朝鲜','泰国','新加坡','菲律宾','马来西亚','印度尼西亚','越南'",
		         "其他"=>"'阿尔及利亚','阿根廷','乌拉圭','墨西哥','埃及','澳大利亚','新西兰','加拿大','巴哈马','巴西','土耳其','博茨瓦纳','南非','哈萨克斯坦','吉尔吉斯坦','委内瑞拉','以色列','黎巴嫩','加蓬','摩洛哥','伊朗','约旦','古巴','哥伦比亚','秘鲁','伊拉克','波多黎各','智利','阿联酋','坦桑尼亚','未知'",
		        ),
		    '2' =>array(
		         "内地"=>"'内地'",
		         "香港"=>"'香港'",
		         "台湾"=>"'台湾'",
		         "韩国"=>"'韩国'",
		         "美国"=>"'美国','迪斯尼'",
		         "日本"=>"'日本'",
		         "其他"=>"'德国','哥伦比亚','加拿大','新加坡','奥地利','澳大利亚','法国','俄罗斯','菲律宾','马来西亚','墨西哥','欧洲','苏联','泰国','委内瑞拉','西班牙','意大利','印度','英国'",
		        ),
		    '3'=>array(
		         "港台"=>"'港台','香港','台湾'",
		         "内地"=>"'内地'",
		         "日韩"=>"'韩国','日韩','日本'",
		         "欧美"=>"'美国','英国','欧美'",
		         "其他"=>"'澳大利亚','菲律宾','加拿大','柬埔寨'",
		        ),
			'131'=>array(
		         "日本"=>"'日本'",
		         "欧美"=>"'欧美','美国','西班牙','英国','意大利','比利时','德国'",
		         "国产"=>"'国产'",
		         "其他"=>"'澳大利亚','俄罗斯','韩国','泰国','伊朗','其他'",
		        ),    
		),
		);
