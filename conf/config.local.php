<?php
$config['system'] = array(		
	'debug' => true,		
);

$config['attach'] = array(	
	'avatar'=> 'http://static.hulapai.com/avatar/',
	'image' => 'http://static.hulapai.com/image/',
);

$config['upload'] = array(
	'path' => SYS . '/upload',
	'max' => 2097152,	
);

$config['session'] = array(	
	'name' => 'HLPSESS',
	'lifetime' => 3600,
	'handle' => 'file',
	'domain' => '',
	'path' => '',
);

$config['database'] = array(
	'master' => array(
		'host' => '42.120.22.28',
		'charset' => 'utf8',
		'dbname' => 'huladb',
		'username' => 'api',
		'password' => '7Vr5DeHCaXhxNqcW',
	),
	'slave' => array(
		'host' => '42.120.22.28',
		'charset' => 'utf8',
		'dbname' => 'huladb',
		'username' => 'api',
		'password' => '7Vr5DeHCaXhxNqcW',
	)
);

$config['thumb'] = array(
	'single' => array(
		'small_width' => 200,
		'small_height'=> 200,
		'middle_width' => 320,
		'middle_height'=> 400
	),
	'multi' => array(
		'width' => 100,
		'height'=> 100
	)	
);

$config['memcache'] = array(	
	'master' => array(
		'host' => '42.120.22.28',
		'port' => '11211'		
	),
	'slave'=> array(
		'host' => '127.0.0.1',
		'port' => '11211'
	)
);

$config['redis'] = array(	
	'host' => '42.120.22.28',
	'port' => '6379'
);

$config['xmpp'] = array(		
	'server' => 'hulapai.com',
	'host' => '42.120.22.28',
	'port' => 5222,
	'user' => 1,
	'password' => 'admin',
	'res' => 'hulapai'	
);

$config['sms'] = array(	
	'user' => 'hulapai',	
	'password' => '21f4e48eeaf7474e1196aea9f628b8ea'	
);

//缓存
$config['cache'] = array(
    't_keyword' => array(
    	'sql' => 'select * from t_keyword',
	    'index' => '',      // 索引列,多个以','分割
	    'value' => 'keyword',   // 取得值的列,如果要取得多个可不设置该项值
    ),
    
);