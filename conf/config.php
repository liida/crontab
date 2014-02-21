<?php
$config['system'] = array(		
	'debug' => true,		
);

$config['attach'] = array(	
	'avatar'=> 'http://192.168.0.200:81/',
	'image' => 'http://192.168.0.200:81/',
);

$config['upload'] = array(
	'path' => '/home/www/server/sns/data/upload',
	'max' => 2097152,	
);

$config['session'] = array(	
	'name' => 'HLPSESS',
	'lifetime' => 3600,
	'handle' => 'file',
	'domain' => '',
	'path' => '',
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
//缓存
$config['cache'] = array(
    't_keyword' => array(
    	'sql' => 'select * from t_keyword',
	    'index' => '',      // 索引列,多个以','分割
	    'value' => 'keyword',   // 取得值的列,如果要取得多个可不设置该项值
    ),
    
);
