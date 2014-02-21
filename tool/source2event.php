<?php
define('ROOT_PATH', realpath(dirname(dirname(__FILE__))));
require(ROOT_PATH.'/global.php');
load('ustring');
			
$_Event= load_model('event');	
$_Teacher_Course= load_model('teacher_course');
$_Student_Course= load_model('student_course');
$eventInfos = $_Event->getAll("source = 2");
if($eventInfos){
	foreach($eventInfos as $eventInfo){
		$teachers = $_Teacher_Course->getColumn(array('event'=>$eventInfo['id']), 'teacher');
		if($teachers){
			$_Teacher_Course->delete(array('event'=>$eventInfo['id']),true);
		}
		
		$students = $_Student_Course->getColumn(array('event'=>$eventInfo['id']), 'student');
		if($students){
			$_Student_Course->delete(array('event'=>$eventInfo['id']),true);
		}
		$_Event->delete(array('id'=>$eventInfo['id']),true);
		event_push($eventInfo,$teachers,$students,2,array(
		    		'act'=>'delete',
					'source' => array(
                        'old'=>array(
                            'text' => $eventInfo['text'], 'is_loop' => $eventInfo['is_loop'], 'rec_type' => $eventInfo['rec_type'],
                            'start_date' => $eventInfo['start_date'], 'end_date' => $eventInfo['end_date'],'school' => $eventInfo['school'],
                   		)
                   	)
    	));
	}
}


function event_push($eventInfo,$teachers,$students,$type=0,$data=array(), $whole=0){
	$hash = md5($eventInfo['id']).rand(10000,99999);
	$logsData = array(
		'hash'=>$hash,
		'app'=>'event',
		'act'=>'add',
		'character'=>'teacher',
		'creator'=>$eventInfo['creator'],
		'target'=>array(),
		'ext'=>array(),
		'source'=>array(
			'event' => $eventInfo['id'],
			'is_loop' => $eventInfo['is_loop'],
			'whole' => $whole,
			'school'=> $eventInfo['school'],
		),
		'data' => array(),
		'type'=>$type,
	);
	if($data['source']){
		$logsData['source'] = array_merge($logsData['source'],$data['source']);
		unset($data['source']);
	}
	$logsData = array_merge($logsData,$data);
	if($teachers){
		logs('db')->add('event', $hash,array_merge($logsData,array('character'=>'teacher','target'=>$teachers)));
	}
	if($students){
		logs('db')->add('event', $hash,array_merge($logsData,array('character'=>'student','target'=>$students)));
	}
}	
