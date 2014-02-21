<?php
//循环课程生成子课程
define('ROOT_PATH', realpath(dirname(dirname(__FILE__))));
require(ROOT_PATH.'/global.php');
$dateTime = date('Y-m-d')." 00:00:00";
$cDateTime = date('Y-m-d',strtotime('2 day'))." 23:59:59";
$_Event = load_model('event');
//获得所有循环课程
$result =  $_Event->getAll("is_loop = 1 and pid = 0 and `lock` = 0 and status = 0 and end_date > '$dateTime'");
if(!$result) exit;
		
import('repeat');
foreach($result as $event){
	$repeat = Repeat::resolve($event['start_date'], $event['end_date'], $event['rec_type'], $event['length']);	
	if($repeat){
		foreach($repeat as $_repeat){
			if($_repeat['start_date'] <= $cDateTime){
				//是否已经生成了
				$length = strtotime($_repeat['start_date']);
				$_Event->rec_create($event['id'], $length,false,true);
				
			}
		}
	}
}			