<?php
define('ROOT_PATH', realpath(dirname(dirname(__FILE__))));
require(ROOT_PATH.'/global.php');
//debug('run event:' . date('Y-m-d H:i:s')); 
$_Event = load_model('event');	
import('repeat');
while (true){
	//读取前10条锁定循环课程
	$result =  $_Event->getAll(array('lock'=>1,'is_loop'=>1,'status'=>0),10);
	if($result){
		foreach($result as $event){
			$_Event->update(array('lock'=>2),array('id'=>$event['id']));
			$repeat = Repeat::resolve($event['start_date'], $event['end_date'], $event['rec_type'], $event['length']);	
			if($repeat){
				foreach($repeat as $_repeat){
					$length = strtotime($_repeat['start_date']);
					//$todayLastTime = strtotime(date('Y-m-d')." 23:59:59");
					//if($length <= $todayLastTime){
						$_Event->rec_create($event['id'], $length,false);
					//}
				}
			}
			$_Event->update(array('lock'=>0),array('id'=>$event['id']));
		}
	}
	sleep(5);
}